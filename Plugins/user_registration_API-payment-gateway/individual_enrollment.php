<?php

require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once("$CFG->libdir/filelib.php");
global $DB, $PAGE, $CFG, $OUTPUT;

$_SESSION['type'] = $type = required_param('type', PARAM_TEXT);
if($type != 'individual') {
    redirect($CFG->wwwroot.'/local/user_registration/index.php');
}

$editid = optional_param('editid', '', PARAM_TEXT);
$args = array("editid" => $editid, "type"=>$type, "course_id"=>$_SESSION['course_id']);
$pageurl = $CFG->wwwroot."/local/user_registration/individual_enrollment.php?type=".$type;
$PAGE->requires->jquery();
$PAGE->set_url($CFG->wwwroot.'/local/user_registration/individual_enrollment.php?type='.$type);
// $PAGE->set_pagelayout('standard');
$PAGE->set_title('Registration Form - Individual');
$PAGE->set_heading('<div>Course Registration Form - Individual</div>');
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/user_registration/style.css'));

echo $OUTPUT->header();


class registration extends moodleform {
  //Add elements to form
  public function definition() {
       global $CFG, $DB;

       $mform = $this->_form;
       $editid = base64_decode($this->_customdata['editid']);
       $type = $this->_customdata['type'];
       $course_id = $this->_customdata['course_id'];
  
       if(!empty($editid)) {
          $userdata = $DB->get_record_sql('SELECT lie.*, lr.id FROM {lcl_registration} lr 
          INNER JOIN {lcl_individual_enrollment} AS lie ON lr.id = lie.registration_id
          WHERE lr.status=0 AND lr.id = '.$editid);
          $course_id = $userdata->course_id;
          // echo "<pre>";
          // print_r($userdata);
          // die;
        }
        
        // validate_email_mobile_cpr
        $mform->addElement('html', '<h3 class="" style="background:#69c4b39e;padding-left:10px;">Personal Information</h3>');
        $mform->addElement('text', 'name', 'Name');
        $mform->setDefault('name', $userdata->name);

        $mform->addElement('html', '<div class="list-column d-flex">');
        $mform->addElement('text', 'mobile_number','Mobile Number', array('class'=>'col-6  justify-content-between', 'id'=>'validate_mobile', 'onkeyup' => 'javascript:validate_email_mobile_cpr();'));
        $mform->setDefault('mobile_number', $userdata->mobile_number);
        $mform->addElement('text', 'other_phone','Other Number', 'size="30" rows="20" cols="50"');
        $mform->setDefault('other_phone', $userdata->other_phone);
        $mform->addElement('html', '</div>');
        $mform->addElement('static', 'static_validate_mob_num', '', '<span id="status_validate_mobile" style="color: red;"></span>');


        $mform->addElement('text', 'email', 'E-Mail', array("id" => "validate_email", 'onkeyup' => 'javascript:validate_email_mobile_cpr();'));
        $mform->addRule('email', 'email is required', 'email', 'client');
        $mform->setDefault('email', $userdata->email);
        $mform->addElement('static', 'static_validate_em_num', '', '<span id="status_validate_email" style="color: red;"></span>');

        $mform->addElement('textarea', 'address', 'Address');
        $mform->setDefault('address', $userdata->address);
        $mform->addElement('text', 'sponsor', 'Sponsor');
        $mform->setDefault('sponsor', $userdata->sponsor);
        $mform->addElement('text', 'job_title', 'Job Title(If Employed)');
        $mform->setDefault('job_title', $userdata->job_title);
        
        $mform->addElement('html', '<div class="list-column d-flex cstm-class">');
        $mform->addElement('date_selector', 'date_of_birth', 'Date of Birth' , array());
        $mform->setDefault('date_of_birth', $userdata->date_of_birth);
        $mform->addElement('text', 'cpr', 'CPR', array("id"=>"validate_cpr", 'onkeyup' => 'javascript:validate_email_mobile_cpr();'));
        $mform->setDefault('cpr', $userdata->cpr);
        $mform->addElement('html', '<span id="status_validate_cpr" style="margin: 5px; color: red;"></span>');
        $mform->addElement('html', '</div>');

        if(empty($editid)) {
            $mform->addElement('html', '<h3 class="" style="background:#69c4b39e;padding-left:10px;">How did you hear about this course</h3>');
            $about_course=array();
            $about_course[] = $mform->createElement('checkbox', 'social', ' Social Media ');
            $about_course[] = $mform->createElement('checkbox', 'referrel', ' Referrel ');
            $about_course[] = $mform->createElement('checkbox', 'other', ' Other ' ,array(), ['class'=>'pex']);
            $mform->addGroup($about_course, 'about_course', '', array(''), false);

            $about_coursep2=array();
            $about_coursep2[] = $mform->createElement('checkbox', 'website', ' Website ');
            $about_coursep2[] = $mform->createElement('checkbox', 'exhibition', ' Exhibition ', array(), ['class'=>'chk']);
            $about_coursep2[] = $mform->createElement('text', 'other_text', ' Other ', array('class'=>'custom'));
            $mform->addGroup($about_coursep2, 'about_course', '', array(''), false);
        }
  
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
        }else{
          $mform->addElement('select', 'course_id', 'Course Name', $courses, array('onchange' => 'javascript:fetchprice();'));
          $mform->setDefault('course_id', $userdata->course_id);
        }

        $mform->addElement('html', '<div class="list-column d-flex">');
        $mform->addElement('hidden', 'start_date', 'Course Start Date' , array('class'=>'col-6  justify-content-between'));
        $mform->setDefault('start_date', $userdata->start_date);
        $mform->addElement('hidden', 'end_date', 'Course End Date');
        $mform->setDefault('end_date', $userdata->end_date);
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
        $mform->addElement('html', '<h3 style="background:#69c4b39e;padding-left:10px;">Academic Qualification</h3>');
        $mform->addElement('text', 'major', 'Major');
        $mform->setDefault('major', $userdata->major);
        $mform->addElement('text', 'university', 'University/Institution');
        $mform->setDefault('university', $userdata->university);
        $mform->addElement('html', '<b>Special Needs</b><br><span>Do you have any specific need that you would like Al mashreq to consider? If yes, please fill the below table</span>');
        $mform->addElement('text', 'special_requirement', 'Special Requirement');
        $mform->setDefault('special_requirement', $userdata->special_requirement);
        $mform->addElement('hidden', 'editid', $editid);
        $mform->addElement('hidden', 'updid', $userdata->id);
        
        $this->add_action_buttons($cancel = false, $submitlabel = 'Next');
    
    }


    //Custom validation should be added here
    function validation($data, $files) {
        $errors = array();
        if (empty($data['name'])) {
            $errors['name'] = "Name is required";
        }if (empty($data['mobile_number'])) {
            $errors['mobile_number'] = "Mobile is required";
        }if (empty($data['other_phone'])) {
            $errors['other_phone'] = "Other Mobile is required";
        }if (empty($data['email'])) {
            $errors['email'] = "Email is required";
        }if (empty($data['address'])) {
            $errors['address'] = "Address is required";
        }if (empty($data['sponsor'])) {
            $errors['sponsor'] = "Sponsor is required";
        }if (empty($data['job_title'])) {
            $errors['job_title'] = "Job Title is required";
        }if (empty($data['date_of_birth'])) {
            $errors['date_of_birth'] = "Date of birth is required";
        }if (empty($data['cpr'])) {
            $errors['cpr'] = "Cpr is required";
        }if (empty($data['course_id'])) {
            $errors['course_id'] = "select Course";
        }if (empty($data['major'])) {
            $errors['major'] = "Field is required";
        }if (empty($data['university'])) {
            $errors['university'] = "Field is required";
        }if (empty($data['special_requirement'])) {
            $errors['special_requirement'] = "Field is required";
        }
        return $errors;
    }
}



// After submission
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

  // echo "<pre>";
  // print_r($record_ins);
  // die;

  if(!empty($_POST['editid'])){

      $record_upd = new stdClass();
      $record_upd->id = $_POST['updid']; 
      $record_upd->course_timing = $course_timing;
      $record_upd->course_location = $course_location;
      $record_upd->name = $_POST['name'];
      $record_upd->other_phone = $_POST['other_phone'];
      $record_upd->address = $_POST['address'];
      $record_upd->sponsor = $_POST['sponsor'];
      $record_upd->job_title = $_POST['job_title'];
      $day = $_POST['date_of_birth']['day'];
      $month = $_POST['date_of_birth']['month'];
      $year = $_POST['date_of_birth']['year'];
      $date_of_birth = strtotime(date($year."/".$month."/".$day));
      $record_upd->date_of_birth = $date_of_birth;
      $record_upd->cpr = $_POST['cpr'];
      $record_upd->special_requirement = $_POST['special_requirement'];
      $record_upd->major = $_POST['major'];
      $record_upd->university = $_POST['university'];
      $record_upd->updated_date = time();
      $DB->update_record('lcl_individual_enrollment', $record_upd, false);

      $application_form_upddata = $DB->get_record('lcl_application_form', array('registration_id' => $_POST['editid'], 'relation'=>'parent'));

      $application_form_upd = new \stdClass();
      $application_form_upd->id = $application_form_upddata->id; 
      $application_form_upd->fullname = $_POST['name'];
      $application_form_upd->cpr = $_POST['cpr'];
      $application_form_upd->date_of_birth = $date_of_birth;
      $application_form_upd->updated_date = time();
      $DB->update_record('lcl_application_form', $application_form_upd, false);
  
  }else{

      $record_ins = new stdClass();
      $record_ins->course_timing = $course_timing;
      $record_ins->course_location = $course_location;
      $record_ins->course_id = (int)$_POST['course_id'];
      $record_ins->level = $_POST['course_level_final'];
      $record_ins->name = $_POST['name'];
      $record_ins->mobile_number = $_POST['mobile_number'];
      $record_ins->other_phone = $_POST['other_phone'];
      $record_ins->email = $_POST['email'];
      $record_ins->address = $_POST['address'];
      $record_ins->sponsor = $_POST['sponsor'];
      $record_ins->job_title = $_POST['job_title'];
      $day = $_POST['date_of_birth']['day'];
      $month = $_POST['date_of_birth']['month'];
      $year = $_POST['date_of_birth']['year'];
      $date_of_birth = strtotime(date($year."/".$month."/".$day));
      $record_ins->date_of_birth = $date_of_birth;
      $record_ins->cpr = $_POST['cpr'];
      $record_ins->special_requirement = $_POST['special_requirement'];


      $referrel_by = '';
      if($_POST['social']==1){
      $referrel_by .= 'Social,';
      }if($_POST['referrel']==1){
      $referrel_by .= 'Referrel,';
      }if($_POST['other']==1){
      $referrel_by .= 'Other,';
      }if($_POST['website']==1){
      $referrel_by .= 'Website,';
      }if($_POST['exhibition']==1){
      $referrel_by .= 'Exhibition,';
      }if(!empty($_POST['other_text'])){
      $referrel_by .= $_POST['other_text'].",";
      }

      $record_ins->referrel_by = $referrel_by;
      $record_ins->course_price = $_POST['course_price_final'];
      $record_ins->start_date = $_POST['course_startdate_final'];
      $record_ins->end_date = $_POST['course_enddate_final'];
      $record_ins->exam_date = $_POST['course_examdate_final'];
      $record_ins->type = $_POST['type'];
      $record_ins->major = $_POST['major'];
      $record_ins->university = $_POST['university'];
      $record_ins->created_date = time();
      $record_ins->updated_date = time(); 

  
      $registration_id = $DB->insert_record('lcl_registration', $record_ins, true);
      $record_ins->registration_id = $registration_id; 
      $DB->insert_record('lcl_individual_enrollment', $record_ins, false);

      $application_form = new \stdClass();
      $application_form->registration_id = $registration_id; 
      $application_form->fullname = $_POST['name'];
      $application_form->cpr = $_POST['cpr'];
      $application_form->email = $_POST['email'];
      $application_form->mobile = $_POST['mobile_number'];
      $application_form->date_of_birth = $date_of_birth;
      $application_form->relation = "parent";
      $application_form->created_date = time();
      $application_form->updated_date = time();
      $DB->insert_record('lcl_application_form', $application_form, false);
      
    } 


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
              redirect($CFG->wwwroot.'/local/user_registration/application_form.php?id='.base64_encode($registration_id));
            }
        }
    } else {
        $msg = 'something is wrong';
        redirect($CFG->wwwroot . '/local/user_registration/individual_enrollment.php?type='.$registration_id, $msg,  \core\output\notification::NOTIFY_SUCCESS);
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
    var post_var = $("#id_course_id").val();
    var urltogo = "<?php echo $CFG->wwwroot; ?>/local/user_registration/ajax.php";
        $.ajax({
            url: urltogo,
            type: 'POST',
            data: { post_var: post_var },
            success: function(response) {
                var responsedata = JSON.parse(response);
                if(responsedata != '') {
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
            data: { val1: $("#validate_mobile").val(), val2: $("#validate_email").val(), val3: $("#validate_cpr").val() },
            success: function(response) {
                var responsedata = JSON.parse(response);
            	if(responsedata){
            	 if(responsedata.mobile != null || responsedata.email != null || responsedata.cpr != null) {
                    $('#status_validate_mobile').html(responsedata.mobile); 
                    $('#status_validate_email').html(responsedata.email); 
                    $('#status_validate_cpr').html(responsedata.cpr); 
                	$('#id_submitbutton').prop('disabled', true);
                	console.warn("#id_submitbutton true");
                 }else if(responsedata.mobile == null && responsedata.email == null && responsedata.cpr == null) {
 					$('#status_validate_mobile').html(""); 
                    $('#status_validate_email').html(""); 
                    $('#status_validate_cpr').html(""); 
					$('#id_submitbutton').prop('disabled', false);
                	console.warn("#id_submitbutton false");
                 } else {
                	$('#status_validate_mobile').html(""); 
                    $('#status_validate_email').html(""); 
                    $('#status_validate_cpr').html(""); 
					$('#id_submitbutton').prop('disabled', false);
                	console.warn("#id_submitbutton false");
                 }
                }
          }
     });
}
</script>

