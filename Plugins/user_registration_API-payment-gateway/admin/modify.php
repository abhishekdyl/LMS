<?php

require_once('../../../config.php');
global $DB, $USER, $PAGE;

$PAGE->requires->jquery();

$reg_id = $_POST['reg_id'];
$post_var = $_POST['post_var'];
$to = base64_decode($_POST['email']);

if($DB->record_exists('lcl_individual_enrollment', array('registration_id'=>$reg_id))) {
	$user_details = $DB->get_record('lcl_individual_enrollment',array('registration_id'=> $reg_id));
	$name = $user_details->name;
	$course_id = $user_details->course_id;
	$coursedata = $DB->get_record('course',array('id'=> $course_id));
	$exam_date_field = $DB->get_record('customfield_field', array('shortname' => 'exam_date'));   
	$exam_date = '';
	if($DB->record_exists('customfield_data', array('fieldid' => $exam_date_field->id, 'instanceid' => $course_id))) {
		$customdata_exam_date = $DB->get_record('customfield_data', array('fieldid' => $exam_date_field->id, 'instanceid' => $course_id));                                   
		$exam_date = $customdata_exam_date->value;
	} 
	$email_template = $DB->get_record('lcl_email_template', array('type'=>1, 'template_type'=>2));
	$msg = $email_template->template;
	$subject = $email_template->subject;
	$email_template_logo_address = $DB->get_record('lcl_logo_address', array('type'=>3));
	$logo = $email_template_logo_address->logo;
	$address = text_to_html($email_template_logo_address->address);
	$redirecting_url = $CFG->wwwroot.'/local/user_registration/individual_enrollment.php?type=individual&editid='.base64_encode($reg_id);
	
}

if($DB->record_exists('lcl_corporate_enrollment', array('registration_id'=>$reg_id))) {
	$user_details = $DB->get_record('lcl_corporate_enrollment',array('registration_id'=> $reg_id));
	$name = $user_details->client_name;
	$course_id = $user_details->course_id;
	$coursedata = $DB->get_record('course',array('id'=> $course_id));
	$exam_date_field = $DB->get_record('customfield_field', array('shortname' => 'exam_date'));   
	$exam_date = '';
	if($DB->record_exists('customfield_data', array('fieldid' => $exam_date_field->id, 'instanceid' => $course_id))) {
		$customdata_exam_date = $DB->get_record('customfield_data', array('fieldid' => $exam_date_field->id, 'instanceid' => $course_id));                                   
		$exam_date = $customdata_exam_date->value;
	}
	$email_template = $DB->get_record('lcl_email_template', array('type'=>2, 'template_type'=>2));
	$msg = $email_template->template;
    $subject = $email_template->subject;
	$email_template_logo_address = $DB->get_record('lcl_logo_address', array('type'=>3));
	$logo = $email_template_logo_address->logo;
	$address = text_to_html($email_template_logo_address->address);
	$redirecting_url = $CFG->wwwroot.'/local/user_registration/corporate_enrollment.php?type=corporate&editid='.base64_encode($reg_id);
}


$ins_record = new \stdClass();

if(isset($_POST['reg_id'])){

	$toUser = new \stdClass();
	$toUser->email = $to;
	$toUser->firstname = $name;
	$toUser->lastname = '';
	$toUser->maildisplay = true;
	$toUser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
	$toUser->id = -99; 
	$toUser->firstnamephonetic = '';
	$toUser->lastnamephonetic = '';
	$toUser->middlename = '';
	$toUser->alternatename = '';

	$fromUser = new \stdClass();
	$fromUser->email = $CFG->supportemail;
	$fromUser->firstname = $name;
	$fromUser->lastname = '';
	$fromUser->maildisplay = true;
	$fromUser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
	$fromUser->id = -99; 
	$fromUser->firstnamephonetic = '';
	$fromUser->lastnamephonetic = '';
	$fromUser->middlename = '';
	$fromUser->alternatename = '';
	
 	require_once($CFG->dirroot.'/local/user_registration/classes/customClass.php');
	$data = new stdClass();
	$data->name = ucwords($name);
	$data->link = $redirecting_url;
	$data->modification = $post_var;
	$data->table = $table;
		
    $msg = html_entity_decode($msg);
	$msg = customClass::template_string($msg, $data);
	$msg = text_to_html($msg);
	$messageText = '';
	$messageHtml =   "<!DOCTYPE html>
							<html>
								<head>
								<title>".$CFG->supportemail."</title>
								<link rel='stylesheet' type='text/css' href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
								<style>
                                	.container {
            							display: flex;
            							justify-content: space-between; /* Adjust as needed */
        							}
        							.box {
            							width: 48%; /* Adjust as needed */
            							padding: 4px;
        							}
                                    .table {
                                   		border-collapse: collapse;
                                    	width: 96%; 
                                    }
                                    .table thead td {
            							padding: 5px;
                                    }
                                    .tablediv{
                                    	width: 100%; 
                                        margin: 10px;
                                    }
                                </style>
								</head>
								<body>
									<div class='main'>
                                     	<div class='container'>
											<div class='box' align='left'>
                                            	<img src='".$CFG->wwwroot."/local/user_registration/temp/".$logo."' alt='image' width='100'>
                                            </div>
                                     		<div class='box' align='right'>
                                            	".$address."
                                            </div>
										</div>
										<div class='tablediv'>
                                     		<div class='col-md-12' align='center'><h1><strong>(Modification Letter)</strong></h1></div>
                                     		<div class='col-md-12' align='left'>Date: ".date('d/m/Y')."</div>
                                     		<div class='col-md-12'>
                                      			<table border=1 class='table'>
                                    				<thead>
                                        				<tr><td style='width: 50%;'><strong>Learner Name</strong></td><td>".$name."</td></tr>
                                        				<tr><td style='width: 50%;'><strong>Learner Identification No. (ID Number)</strong></td><td>".$user_details->registration_id."</td></tr>
                                        				<tr><td style='width: 50%;'><strong>Course Name</strong></td><td>".$coursedata->fullname."</td></tr>
                                        				<tr><td style='width: 50%;'><strong>Awarding Body</strong></td><td>".$coursedata->fullname."</td></tr>
                                        				<tr><td style='width: 50%;'><strong>Start Date</strong></td><td>".date('d/m/Y', $user_details->start_date)."</td></tr>
                                        				<tr><td style='width: 50%;'><strong>End Date</strong></td><td>".date('d/m/Y', $user_details->end_date)."</td></tr>
                                        				<tr><td style='width: 50%;'><strong>Exam Date</strong></td><td>".date('d/m/Y', $exam_date)."</td></tr>
                                        				<tr><td style='width: 50%;'><strong>Course Timing</strong></td><td>".$user_details->course_timing."</td></tr>
                                        				<tr><td style='width: 50%;'><strong>Course Venue</strong></td><td>".$user_details->course_location."</td></tr>
                                        			</thead>
                                      			</table>
                                     		</div>
                                            <br>
                                            <br>
                                     		<div class='col-md-12' align='left'>".$msg."</div>
                                     	</div>
                                    </div>
								</body>
							</html>";
	$completeFilePath = '';
	$nameOfFile = '';

	email_to_user($toUser, $fromUser, $subject, $messageText, $messageHtml, $completeFilePath, $nameOfFile, false);



	if($DB->record_exists('lcl_modification_form', array('registration_id'=>$reg_id))) {

		$ins_id = $DB->get_record('lcl_modification_form',array('registration_id'=> $reg_id));
		$ins_record->id = $ins_id->id;
		$ins_record->modification = $post_var;
		$ins_record->modified_date  = time();

		$DB->update_record('lcl_modification_form',$ins_record, false);
		
		echo json_encode(array('statustd'=> '<span class="text-danger">Pending for modification</span>', 'value' => $post_var, 'status' => '<span class="text-success">Record Updated, and will be notified to user</span>'));
		
	
	} else {

		$ins_record->registration_id = $reg_id; 
		$ins_record->modification = $post_var;
		$ins_record->created_date = time();
		$ins_record->modified_date  = time();
		$ins_record->status = 0;
		$DB->insert_record('lcl_modification_form',$ins_record, false);


		$upd_record = new \stdClass();
		$upd_record->id = $reg_id;
		$upd_record->updated_date = time();
		$upd_record->assessor_status = 2;
		$DB->update_record('lcl_registration',$upd_record, false);


		echo json_encode(array('statustd'=> '<span class="text-danger">Pending for modification</span>', 'value' => $post_var, 'status' => '<span class="text-success">Record Saved, and will be notified to user</span>'));

	}


}

