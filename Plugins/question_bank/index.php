<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/question/editlib.php');
global $DB,$CFG,$USER,$PAGE,$COURSE;

$courseid = optional_param('courseid', 0, PARAM_INT);
if($courseid == 0){
    // redirect($CFG->wwwroot . "/local/question_bank/index.php?courseid=7");
    redirect($CFG->wwwroot . "/local/question_bank/index.php");
}
require_login();
list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) = question_edit_setup('questions', '/local/question_bank/index.php');
list($categoryid, $contextid) = explode(',', $pagevars['cat']);

$PAGE->requires->jquery();

$url = new moodle_url('/local/question_bank/index.php',array('courseid'=>$courseid));
$PAGE->set_url($url);
$courseContext = context_course::instance($courseid, MUST_EXIST);
$PAGE->set_context($courseContext);

$quetions = $DB->get_records_sql('SELECT qv.questionbankentryid, MAX(qv.questionid) as questid , qbe.* FROM {question_bank_entries} qbe JOIN {question_versions} qv ON qbe.id = qv.questionbankentryid GROUP BY qv.questionbankentryid');
$returnurl = $CFG->wwwroot.'/local/question_bank/index.php?courseid='.$courseid;

if (!$category = $DB->get_record('question_categories', array('id' => $categoryid))) {
    throw new moodle_exception('categorydoesnotexist', 'question', $returnurl);
}
$categorycontext = context::instance_by_id($category->contextid);
$quizpriview = has_capability('local/question_bank:quizpreview', $categorycontext);
$addquiz = has_capability('local/question_bank:addquestion', $categorycontext);
$questionreviewed = has_capability('local/question_bank:questionreviewed', $categorycontext);
$editquiz = has_capability('moodle/question:editmine', $categorycontext);
$approvercap = has_capability('local/question_bank:approvequestion', $categorycontext);

$add='';
if($addquiz){
    $add = '<button type="button" class="btn btn-light float-right px-5 m-1" data-toggle="modal" data-target="#myModal" >Add</button>';
}

$html = '<div class="container">
            <div class="row">
                <div class="col-md-12"><h3> Question listing from question bank </h3></div>
                <div class="col-md-12">'.$add; 
                if($approvercap){$html .= '<a href="'.$CFG->wwwroot.'/local/question_bank/assign_reviewer.php?category='.$categoryid.'&courseid='.$courseid.'" class="btn btn-light float-right m-1">Add reviewer</a>';}
      $html .= '</div>
                <div class="col-md-12">
                    <table class="table table-striped" id="quiztablelist" >
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Question</th>
                                <th>Createdby</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>  
                        </thead>
                        <tbody>';                  
                foreach ($quetions as $key => $quetion) {

                    if($approvercap || is_siteadmin()){
                        $quets = $DB->get_record_sql('SELECT q.*,qb.status,u.firstname,u.lastname FROM {local_qbquestions} qb JOIN {question} q ON qb.questionid = q.id JOIN {user} u ON qb.createdby = u.id WHERE qb.questionid ='.$quetion->questid.' AND qb.courseid = '.$courseid.' AND qb.coursecontextid = '.$courseContext->id.' AND qb.status != "approved"');
                    }elseif($questionreviewed){
                        $quets = $DB->get_record_sql('SELECT q.*,qb.status,u.firstname,u.lastname FROM {local_qbquestions} qb JOIN {question} q ON qb.questionid = q.id JOIN {user} u ON qb.createdby = u.id WHERE qb.questionid ='.$quetion->questid.' AND qb.courseid = '.$courseid.' AND qb.coursecontextid = '.$courseContext->id.' AND qb.status != "approved" AND (qb.status = "Sent for review" OR qb.status = "review again")');
                    }else{
                        $quets = $DB->get_record_sql('SELECT q.*,qb.status,u.firstname,u.lastname FROM {local_qbquestions} qb JOIN {question} q ON qb.questionid = q.id JOIN {user} u ON qb.createdby = u.id WHERE qb.createdby = '.$USER->id.' AND qb.courseid = '.$courseid.' AND qb.coursecontextid = '.$courseContext->id.' AND q.id ='.$quetion->questid.' AND qb.status = "pending"');
                    }

                    // echo '<pre>----oooooook----';
                    // print_r($quets);
                    // echo '</pre>';
                    
                    if(!empty($quets)){
                        $html .='<tr>
                                    <td>'.$quets->name.'</td>
                                    <td>'.$quets->questiontext.'</td>';
                                    if(!$questionreviewed){
                            $html .='<td>'.$quets->firstname.' '.$quets->lastname.' <br/><small>'.date('d F Y, H:i A',$quets->timecreated).'</small> </td>';}else{ $html .='<td class="text-center" > - </td>'; }
                                if($quets->status == 'pending'){
                                // $qcomment = $DB->get_record('comments',array('component'=>'qbank_comment','commentarea'=>'question','itemid'=>$quets->id));
                                $qcomment = $DB->get_record_sql('SELECT * FROM {comments} WHERE component = "qbank_comment" AND commentarea = "question" AND itemid = '.$quets->id.' ORDER BY id DESC LIMIT 0,1');
                            $html .='<td>'.$quets->status.'<br/><small class="re_comment text-danger" >'.$qcomment->content.'</small></td>';
                                }else{
                            $html .='<td>'.$quets->status.'</td>';
                                }
                            $html .='<td>';
                                    if($editquiz){ $html .='<a href="'.$CFG->wwwroot.'/local/question_bank/question.php?courseid='.$courseid.'&id='.$quets->id.'">edit</a> '; }
                                    if($quizpriview){ $html .=' <a href="'.$CFG->wwwroot.'/local/question_bank/preview.php?id='.$quets->id.'&courseid='.$courseid.'&category='.$categoryid.'">preview</a>';} 
                            $html .='</td>
                                </tr>';
                    }
                }
            $html .= '</tbody>
                    </table>
                </div>
                <div class="col-md-12">'.$add.'</div>
            </div>
        </div>
        
<div class="modal" id="myModal">
<div class="modal-dialog">
<div class="modal-content">
    <form action="question.php" method="GET">
        <div class="modal-header">
            <legend class="moduletypetitle modal-title">Choose a question type to add !</legend>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <fieldset class="modal-body">
            <div class="option" >
                <label for="item_qtype_multichoice">
                    <input type="radio" name="qtype" id="item_qtype_multichoice" value="multichoice" data-initial-value="multichoice">&nbsp
                        <span class="modicon">
                            <img class="icon icon" title="Multiple choice" alt="Multiple choice" src="'.$CFG->wwwroot.'/theme/image.php/boost/qtype_multichoice/1705999415/icon">
                        </span>
                    <span class="typename">Multiple choice</span>
                </label>
            </div>
            <div class="option">
                <label for="item_qtype_truefalse">
                    <input type="radio" name="qtype" id="item_qtype_truefalse" value="truefalse">&nbsp
                        <span class="modicon">
                            <img class="icon icon" title="True/False" alt="True/False" src="'.$CFG->wwwroot.'/theme/image.php/boost/qtype_truefalse/1705999415/icon"> 
                        </span>
                    <span class="typename">True/False</span>
                </label>
            </div>
            <div class="option">
                <label for="item_qtype_shortanswer">
                    <input type="radio" name="qtype" id="item_qtype_shortanswer" value="shortanswer">
                    <span class="modicon">
                            <img class="icon icon" title="Short answer" alt="Short answer" src="'.$CFG->wwwroot.'/theme/image.php/boost/qtype_shortanswer/1707568397/icon">                                            </span>
                    <span class="typename">Short answer</span>
                </label>
            </div>
            <div class="option">
                <label for="item_qtype_numerical">
                    <input type="radio" name="qtype" id="item_qtype_numerical" value="numerical">
                    <span class="modicon">
                            <img class="icon icon" title="Numerical" alt="Numerical" src="'.$CFG->wwwroot.'/theme/image.php/boost/qtype_numerical/1707568397/icon">                                            </span>
                    <span class="typename">Numerical</span>
                </label>
            </div>



        </fieldset>
            <input type="hidden" name="courseid" value='.$courseid.'>
            <input type="hidden" name="category" value='.$categoryid.'>
        <div class="modal-footer">
            <button type="submit" >Save Change</button>
            <button><a href="'.$returnurl.'">Cancel</a></button>
        </div>
    </form> 
</div>
</div>
</div>
<link href="https://cdn.datatables.net/v/dt/dt-1.13.4/datatables.min.css" rel="stylesheet"/> 
<script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script> 
<script type="text/javascript">  
    $(document).ready(function(){
        $("#quiztablelist").DataTable();
    });
   
</script> 
';
echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();

?>
