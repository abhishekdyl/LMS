<?php

require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once("$CFG->libdir/filelib.php");
global $DB, $PAGE, $CFG, $OUTPUT;

$_SESSION['type'] = $type = required_param('type', PARAM_TEXT);
if($type != 'corporate') {
    redirect($CFG->wwwroot.'/local/user_registration/index.php');
}

$editid = optional_param('editid', '', PARAM_TEXT);
$args = array("editid" => $editid, "type"=>$type, "course_id"=>$_SESSION['course_id']);
$pageurl = $CFG->wwwroot."/local/user_registration/corporate_enrollment.php?type=".$type;
$PAGE->requires->jquery();
$PAGE->set_url($CFG->wwwroot.'/local/user_registration/corporate_enrollment.php?type='.$type);
// $PAGE->set_pagelayout('standard');
$PAGE->set_title('Registration Form - Corporate');
$PAGE->set_heading('<div>Course Registration Form - Corporate</div>');
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/user_registration/style2.css'));

echo $OUTPUT->header();
class registration extends moodleform {
  //Add elements to form
  public function definition() {
        global $CFG, $DB;
        $mform = $this->_form;
        $editid = $this->_customdata['editid'];
        $type = $this->_customdata['type'];
        $course_id = $this->_customdata['course_id'];
        
        if(!empty($editid)) {
          $userdata = $DB->get_record_sql('SELECT lce.*, lr.id FROM {lcl_registration} lr 
          INNER JOIN {lcl_corporate_enrollment} AS lce ON lr.id = lce.registration_id
          WHERE lr.status=0 AND lr.id = '.$editid);
          $course_id = $userdata->course_id;
        }
  
        $mform->addElement('html', '<h3 class="" style="background:#69c4b39e;padding-left:10px;">Company Information</h3>');
        $mform->addElement('text', 'client_name', 'Client Name');
        $mform->setDefault('client_name', $userdata->client_name);
  
        $mform->addElement('text', 'company_address', 'Company Address');
        $mform->setDefault('company_address', $userdata->company_address);
  
        $mform->addElement('text', 'contact_person', 'Contact Person');
        $mform->setDefault('contact_person', $userdata->contact_person);
  
        $mform->addElement('text', 'job_title', 'Job Title');
        $mform->setDefault('job_title', $userdata->job_title);
  
        $mform->addElement('html', '<div class="list-column  row">');
        $mform->addElement('text', 'mobile_number','Mobile Number', array('class'=>'col-6  justify-content-between', 'id'=>'validate_mobile', 'onkeyup' => 'javascript:validate_email_mobile_cpr();'));
        $mform->setDefault('mobile_number', $userdata->mobile_number);
        $mform->addElement('text', 'work_phone','Other Number', 'size="30" rows="20" cols="50"');
        $mform->setDefault('work_phone', $userdata->work_phone);
        $mform->addElement('html', '</div>');
        $mform->addElement('static', 'static_validate_mob_num', '', '<span id="status_validate_mobile" style="color: red;"></span>');

        $mform->addElement('html', '<div class="list-column row">');
        $mform->addElement('text', 'email','E-Mail', array("id" => "validate_email", 'class'=>'col-6  justify-content-between', 'onkeyup' => 'javascript:validate_email_mobile_cpr();'));
        $mform->setDefault('email', $userdata->email);
        $mform->addElement('text', 'po_box','PO Box', 'size="30" rows="20" cols="50"');
        $mform->setDefault('po_box', $userdata->po_box);
        $mform->addElement('html', '</div>');
        $mform->addElement('static', 'static_validate_em_num', '', '<span id="status_validate_email" style="color: red;"></span>');

        $mform->addElement('text', 'sponsor', 'Sponsoring Organisation');
        $mform->setDefault('sponsor', $userdata->sponsor);
        $mform->addElement('html', '<h3 class="" style="background:#69c4b39e;padding-left:10px;">Course Information</h3>');
        $course_list = $DB->get_records("course", array('visible'=>1));
        $arr = [0=>'Select Course'];
        foreach ($course_list as $data) {
           if($data->id!=1){
             $arr += array($data->id => $data->fullname);
           }
        }
        $courses = $arr;
        if (!empty($course_id)) {
          $mform->addElement('static', 'course_id_static', 'Course Name', $courses[$course_id]);
          $mform->addElement('hidden', 'course_id', $course_id, array('id'=>'id_course_id_static'));
        } else {
          $mform->addElement('select', 'course_id', 'Course Name', $courses, array('onchange' => 'javascript:fetchprice();', 'id'=>'id_level'));
        }
        $mform->addElement('html', '<div class="list-column  row">');
        $mform->addElement('hidden', 'start_date', 'Course Start Date', array('class' => 'col-6  justify-content-between'));
        $mform->setDefault('start_date', '0');
        $mform->addElement('hidden', 'end_date', 'Course End Date');
        $mform->setDefault('end_date', '0');
        $mform->addElement('html', '</div>');
        $mform->addElement('static', 'course_price', 'Course Price (BHD)', '<div id="course_price">Select course first</div>');
        $mform->addElement('static', 'course_level', 'Course Level', '<div id="course_level">Select course first</div>');
        $mform->addElement('hidden', 'course_level_final', '', array('id'=>'course_level_final'));
        $mform->addElement('hidden', 'course_price_final', '', array('id'=>'course_price_final'));
        $mform->addElement('hidden', 'course_startdate_final', '', array('id'=>'course_startdate_final'));
        $mform->addElement('hidden', 'course_enddate_final', '', array('id'=>'course_enddate_final'));
        $mform->addElement('hidden', 'course_examdate_final', '', array('id'=>'course_examdate_final'));
        $mform->addElement('hidden', 'type', $type);
        $mform->addElement('static', 'course_timing', 'Course Timing', '<ul id="course_timing">Select course first</ul>');    
        $mform->addElement('static', 'course_location', 'Course Location', '<ul id="course_location">Select course first</ul>');
        $this->add_action_buttons($cancel = true, $submitlabel = 'submit');
    }
    function validation($data, $files) {
        $errors = array();
        if (empty($data['course_id'])) {
            $errors['course_id'] = "select level";
        }
        if (empty($data['client_name'])) {
            $errors['client_name'] = "Filed is required";
        }
        if (empty($data['company_address'])) {
            $errors['company_address'] = "Field is required";
        }
        if (empty($data['job_title'])) {
            $errors['job_title'] = "Field is required";
        }
        if (empty($data['mobile_number'])) {
            $errors['mobile_number'] = "Field is required";
        }
        if (empty($data['email'])) {
            $errors['email'] = "Field is required";
        }
        if (empty($data['po_box'])) {
            $errors['po_box'] = "Field is required";
        }
        if (empty($data['sponsor'])) {
            $errors['sponsor'] = "Field is required";
        }
     return $errors;
    }
}
$mform = new registration($pageurl, $args);
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/local/user_registration/index.php');
} else if ($fromform = $mform->get_data()) {
    if(!empty($_POST['course_timing'])) {
        if(count($_POST['course_timing'])>0){
            $course_timing  = implode(", ", $_POST['course_timing']);
        }
    }
    if(!empty($_POST['course_location'])) {
        if(count($_POST['course_location'])>0){
            $course_location = implode(", ", $_POST['course_location']);
        }
    }
    $record_ins = new stdClass();
    $record_ins->course_timing = $course_timing;
    $record_ins->course_location = $course_location;
    $record_ins->course_price = $_POST['course_price_final'];
    $record_ins->start_date = $_POST['course_startdate_final'];
    $record_ins->end_date = $_POST['course_enddate_final'];
    $record_ins->exam_date = $_POST['course_examdate_final'];
    $record_ins->type = $_POST['type'];
    $record_ins->course_id = (int)$_POST['course_id'];
    $record_ins->level = $_POST['course_level_final'];
    $record_ins->client_name = $_POST['client_name'];
    $record_ins->company_address = $_POST['company_address'];
    $record_ins->contact_person = $_POST['contact_person'];
    $record_ins->job_title = $_POST['job_title'];
    $record_ins->mobile_number = $_POST['mobile_number'];
    $record_ins->work_phone = $_POST['work_phone'];
    $record_ins->email = $_POST['email'];
    $record_ins->po_box = $_POST['po_box'];
    $record_ins->sponsor_organisation = $_POST['sponsor'];
    $record_ins->created_date = time();
    $record_ins->updated_date = time(); 
    $registration_id = $DB->insert_record('lcl_registration', $record_ins, true);
    $record_ins->registration_id = $registration_id;
    $DB->insert_record('lcl_corporate_enrollment', $record_ins, false);
    $application_form = new \stdClass();
    $application_form->registration_id = $registration_id; 
    $application_form->fullname = $_POST['client_name'];
    $application_form->cpr = '';
    $application_form->email = $_POST['email'];
    $application_form->mobile = $_POST['mobile_number'];
    $application_form->date_of_birth = '';
    $application_form->relation = 'parent';
    $application_form->created_date = time();
    $application_form->updated_date = time();
    $DB->insert_record('lcl_application_form', $application_form, false);
    $prerequisite = $DB->get_record('customfield_field', array('shortname' => 'prerequisite'));                                                                             
    $customdata_prerequisite = $DB->get_record('customfield_data', array('fieldid' => $prerequisite->id, 'instanceid' => $_POST['course_id'])); 
    if(!empty($_POST['course_level_final'])) {
        if(($_POST['course_level_final']) == '1') {
            $_SESSION['course_level_final'] = $_POST['course_level_final']; 
            if($customdata_prerequisite->value == 2){
                redirect($CFG->wwwroot.'/local/user_registration/prerequisite.php?id='.base64_encode($registration_id));
            }else{
                redirect($CFG->wwwroot.'/local/user_registration/home.php?id='.base64_encode($registration_id));
            }
        } else {
            $_SESSION['course_level_final'] = $_POST['course_level_final']; 
            if($customdata_prerequisite->value == 2){
                redirect($CFG->wwwroot.'/local/user_registration/prerequisite.php?id='.base64_encode($registration_id));
            }else{
                redirect($CFG->wwwroot . '/local/user_registration/application_form.php?id='.base64_encode($registration_id));
            }
        }
    } else {
        $msg = 'something is wrong';
        redirect($CFG->wwwroot . '/local/user_registration/corporate_enrollment.php?type='.$registration_id, $msg,  \core\output\notification::NOTIFY_SUCCESS);
    }
}
$mform->display();
echo $OUTPUT->footer();
?>
<script>
$(document).ready(function() {
  var post_var = $("#id_course_id_static").val();
  var urltogo = "<?php echo $CFG->wwwroot; ?>/local/user_registration/ajax.php";
  if(post_var != "") {
   $.ajax({
            url: urltogo,
            type: 'POST',
            data: { post_var: post_var },
            success: function(response) {
                var responsedata = JSON.parse(response);
                if(responsedata != ''){
                  $('#course_price').html(responsedata.price); 
                  $('#course_level').html(responsedata.level); 
                  $('#course_level_final').val(responsedata.level_val); 
                  $('#course_price_final').val(responsedata.price); 
                  $('#course_startdate_final').val(responsedata.startdate); 
                  $('#course_enddate_final').val(responsedata.enddate); 
                  $('#course_examdate_final').val(responsedata.examdate); 
                  $('#course_location').html(responsedata.location);
                  $('#course_timing').html(responsedata.timing);
              }
           }
       });
  }
});
    
function fetchprice() {
    var post_var = $("#id_level").val();
    var urltogo =  "<?php echo $CFG->wwwroot; ?>/local/user_registration/ajax.php";
        $.ajax({
            url: urltogo,
            type: 'POST',
            data: { post_var: post_var },
            success: function(response) {
                var responsedata = JSON.parse(response);
                if(responsedata != ''){
                $('#course_price').html(responsedata.price); 
                $('#course_level').html(responsedata.level); 
                $('#course_level_final').val(responsedata.level_val); 
                $('#course_price_final').val(responsedata.price); 
                $('#course_startdate_final').val(responsedata.startdate); 
                $('#course_enddate_final').val(responsedata.enddate); 
                $('#course_examdate_final').val(responsedata.examdate); 
                $('#course_location').html(responsedata.location);
                $('#course_timing').html(responsedata.timing);
            }
            }
        });
}

function validate_email_mobile_cpr() {
    var urltogo = "<?php echo $CFG->wwwroot; ?>/local/user_registration/validate.php";
        $.ajax({
            url: urltogo,
            type: 'POST',
            data: { val4: $("#validate_mobile").val(), val5: $("#validate_email").val() },
            success: function(response) {
               var responsedata = JSON.parse(response);
               if(responsedata){
               if(responsedata.mobile != null || responsedata.email != null || responsedata.cpr != null) {
                    $('#status_validate_mobile').html(responsedata.mobile); 
                    $('#status_validate_email').html(responsedata.email); 
                    $('#id_submitbutton').prop('disabled', true);
                    console.warn("#id_submitbutton true");
                 }else if(responsedata.mobile == null && responsedata.email == null && responsedata.cpr == null) {
                    $('#status_validate_mobile').html(""); 
                    $('#status_validate_email').html(""); 
                    $('#id_submitbutton').prop('disabled', false);
                    console.warn("#id_submitbutton false");
                 } else {
                    $('#status_validate_mobile').html(""); 
                    $('#status_validate_email').html(""); 
                    $('#id_submitbutton').prop('disabled', false);
                    console.warn("#id_submitbutton false");
                 }
               }
          }
     });
}
</script>

