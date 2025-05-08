<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/accesslib.php');
require_once("$CFG->libdir/formslib.php");
global $DB,$CFG,$USER,$PAGE,$COURSE;
$courseiid = optional_param('courseid', 0, PARAM_INT);
$categoryiid = optional_param('category', 0, PARAM_INT);
require_login();
$PAGE->requires->jquery();
$url = new moodle_url('/local/question_bank/assign_reviewer.php',array('courseid'=>$courseiid,'category'=>$categoryiid));
$PAGE->set_url($url);
$courseContext = context_course::instance($courseiid, MUST_EXIST);
$PAGE->set_context($courseContext);

// question_require_capability_on($question, 'use');
if (!$category = $DB->get_record('question_categories', array('id' => $categoryiid))) {
    throw new moodle_exception('categorydoesnotexist', 'question', $returnurl);
}

$categorycontext = context::instance_by_id($category->contextid);
$assignreviewerrole = has_capability('local/question_bank:assignreviewerrole', $categorycontext);

if($assignreviewerrole){
    
    $courseContext = context_course::instance($courseiid, MUST_EXIST);
    
    class assign_reviewer extends moodleform {
    
        function definition() {
            global $CFG,$COURSE; 
            $mform = $this->_form;  
            $totalusers = $this->_customdata['totalusers'];
            $courseid = $this->_customdata['courseid'];

            $mform->addElement('header', 'reviewerhdr', 'Manage Reviewer Roles');
            $mform->setExpanded('reviewerhdr', true);

            
            $options = array(                                                                                                           
                'multiple' => true,                                                  
                'noselectionstring' => 'Select Multiple Users',                                                                
            );         
            $mform->addElement('autocomplete', 'users', ' Add Users : ', $totalusers, $options);

            $this->add_action_buttons();
            
            $mform->addElement('html', "<a href='$CFG->wwwroot/local/question_bank/index.php?courseid=".$courseid."' class='btn btn-light' >Back</a>");
            // $mform->addElement('html', '<a class="btn btn-primary" href= "'.$CFG->wwwroot .'/local/question_bank/reviewerlist.php"> Reviewers</a>');
        }         
        public function validation($data, $files) {
            global $DB;
            $errors = array();
            return $errors;
        }      
    
    }    
    echo '<input type="hidden" id="coursecontent_id" name="coursecontent" value="'.$courseContext->id.'">';       
    
    $totaluser = $DB->get_records_sql("SELECT u.id,CONCAT(u.firstname,' ',u.lastname) as name ,ra.id as raid FROM {user} u JOIN {role} r ON r.shortname = 'reviewer' LEFT JOIN {role_assignments} ra ON ra.userid = u.id AND ra.contextid = ".$courseContext->id." LEFT JOIN {role_assignments} ra1 ON r.id = ra1.roleid and ra1.userid = u.id AND ra1.contextid = ".$courseContext->id." WHERE u.id>1 AND u.deleted=0 AND ra.id IS NOT NULL AND ra1.id IS NULL");
    $totalusers = array_column($totaluser, 'name','id');
    
    $args = array(
        'totalusers' => $totalusers,
        'courseid' => $courseiid,
    );

    $mform  = new assign_reviewer($CFG->wwwroot ."/local/question_bank/assign_reviewer.php?courseid=$courseiid&category=$categoryiid",$args);
    
    if ($mform->is_cancelled()) {
        redirect($CFG->wwwroot ."/local/question_bank/index.php?courseid=$courseiid");
    } else if ($formdata = $mform->get_data()) {
        $role = $DB->get_record('role',array('shortname'=>'reviewer'));
        foreach ($formdata->users as $key => $userids) {
            role_assign($role->id, $userids, $courseContext->id);
        }
        redirect($CFG->wwwroot ."/local/question_bank/assign_reviewer.php?category=$categoryiid&courseid=$courseiid");
    }
    
    $reviewerss = $DB->get_records_sql("SELECT u.id,u.firstname,u.lastname,u.email,ra.id as raid FROM {user} u JOIN {role} r ON r.shortname = 'reviewer' LEFT JOIN {role_assignments} ra ON r.id = ra.roleid and ra.userid = u.id AND ra.contextid = ".$courseContext->id." WHERE u.id>1 AND u.deleted=0 AND ra.id IS NOT NULL");
    $html = '<br><h3>Reviewer User List</h3><br>
            <div class="error"></div>
            <table class="table table-striped">
                <tr class="text-center">
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>';
            foreach ($reviewerss as $key => $review) {
        $html .='<tr class="text-center">
                    <td>'.$review->firstname.'</td>
                    <td>'.$review->lastname.'</td>
                    <td>'.$review->email.'</td>
                    <td><a class="removerole p-3" value="'.$review->id.'" > X </a></td>
                </tr>';
            }
    $html .='</table>';

    echo $OUTPUT->header();
    $mform->display();
    echo $html;
    echo $OUTPUT->footer();

}else{
    redirect($CFG->wwwroot . "/local/question_bank/");
}

?>

<script>
$(document).ready(function(){
    $(".removerole").click(function(){
        var that = this;
        var coursecontentid = $('#coursecontent_id').attr('value');
        console.log('++++++1',coursecontentid);
        var qid = $(this).attr('value');
        $.ajax({
                type: 'post',
                url: '<?php echo $CFG->wwwroot; ?>/local/question_bank/ajax.php',
                data: {action:'removerole',id:qid,ccid:coursecontentid}, //sand data on url
                success: function (responseData) { //return responseData (anyname)
                    console.log('formData',responseData);
                var data = JSON.parse(responseData);

                    console.log('formData2222',data);
                    if(data.status == true){
                        $(that).closest("tr").hide();
                        $(".error").append(data.msg);
                        $(".error").addClass('p-3 bg-success text-white');
                        setTimeout(function(){
                            $(".error").html('');
                            $(".error").removeClass('p-3 bg-success text-white');
                            // $(".error").hide();
                        }, 3000);
                    }else{
                        $(".error").append(data.msg);
                        $(".error").addClass('p-3 bg-danger text-white');
                        setTimeout(function(){
                            $(".error").html('');
                            $(".error").removeClass('p-3 bg-danger text-white');
                            // $(".error").hide();
                        }, 3000);
                    }

                }
            });
       
    });
});
</script>