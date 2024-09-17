<?php
require_once(__DIR__ . '/../../config.php');
global $DB,$CFG,$USER,$PAGE,$COURSE;
echo 'oooooooooooooooooooooooooooooo';
die;
require_login();
$quetions = $DB->get_records_sql('SELECT q.*,qb.status,u.firstname,u.lastname FROM {local_question_bank_questions} qb JOIN {question} q ON qb.questionid = q.id JOIN {user} u ON qb.createdby = u.id WHERE qb.createdby = '.$USER->id);
$returnurl = $CFG->wwwroot.'/local/question_bank/questionlist.php';

if (!$category = $DB->get_record('question_categories', array('id' => 2))) {
    throw new moodle_exception('categorydoesnotexist', 'question', $returnurl);
}
$categorycontext = context::instance_by_id($category->contextid);
$quizpriview = has_capability('local/question_bank:quizpreview', $categorycontext);

$cmid = $DB->get_record('course_modules',array('course'=>$COURSE->id,'module'=>18));
$add = '<div class="col-md-12"><button type="button" class="btn btn-light float-right px-5 mb-1" data-toggle="modal" data-target="#myModal" >Add</button></div>';
$html = '<div class="container">
            <div class="row">'.$add.'
                <div class="col-md-12">
                    <table class="table table-striped">
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
                    $html .='<tr>
                                <td>'.$quetion->name.'</td>
                                <td>'.$quetion->questiontext.'</td>
                                <td>'.$quetion->firstname.' '.$quetion->lastname.' <br/><small>'.date('d F Y, H:i A',$quetion->timecreated).'</small> </td>
                                <td>Process/approved</td>
                                <td><a href="'.$CFG->wwwroot.'/local/question_bank/question.php?courseid='.$COURSE->id.'&id='.$quetion->id.'">edit</a> '; 
                                // if($quizpriview){
                                $html .=' <a href="'.$CFG->wwwroot.'/local/question_bank/preview.php?id='.$quetion->id.'&courseid='.$COURSE->id.'">preview</a>';
                                // }
                        $html .='</td>
                            </tr>';
                }
            $html .= '</tbody>
                    </table>
                </div>'.$add.'
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
                            <img class="icon icon" title="Multiple choice" alt="Multiple choice" src="http://122.176.28.104/sushiltest/theme/image.php/boost/qtype_multichoice/1705999415/icon">
                        </span>
                    <span class="typename">Multiple choice</span>
                </label>
            </div>
            <div class="option">
                <label for="item_qtype_truefalse">
                    <input type="radio" name="qtype" id="item_qtype_truefalse" value="truefalse">&nbsp
                        <span class="modicon">
                            <img class="icon icon" title="True/False" alt="True/False" src="http://122.176.28.104/sushiltest/theme/image.php/boost/qtype_truefalse/1705999415/icon"> 
                        </span>
                    <span class="typename">True/False</span>
                </label>
            </div>
        </fieldset>
            <input type="hidden" name="courseid" value=1>
            <input type="hidden" name="category" value=2>
        <div class="modal-footer">
            <button type="submit" >Save Change</button>
            <button><a href="'.$returnurl.'">Cancel</a></button>
        </div>
    </form> 
</div>
</div>
</div>
';
echo $OUTPUT->header();
// var_dump($quizpriview);
echo $html;
// echo '<pre>';
// print_r($cmid);
// print_r($COURSE);
// echo '</pre>';
echo $OUTPUT->footer();
?>