<?php

require('../../config.php');
global $DB, $CFG;
require_once($CFG->dirroot.'/local/user_registration/prerequisiteform.php');

$id = required_param('id', PARAM_RAW);
$PAGE->set_context(context_system::instance());
// $PAGE->set_pagelayout('customblock');
$pageurl = $CFG->wwwroot."/local/user_registration/prerequisite.php?id=".$id;
$args = array("id" => $id, "url" => $pageurl);
$id = base64_decode($id);
$PAGE->set_title('Prerequisite Form');
$PAGE->set_heading('<div>Course Registration Form - Prerequisite</div>');

if($_POST['submitbutton']) {
	
   $prerequisitetype = $_POST['prerequisitetype'];
   if($prerequisitetype) {
	   	foreach ($prerequisitetype as $key => $value) {

	   		// fileupload
		   	if($value == 'fileupload') {
					foreach($_FILES as $key => $data) {
					$details = $DB->get_record('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'fileupload', 'typeid'=>$key));
						if(!empty($details->id)) {
					   	 	if(!empty($key)) {
					   	 			$fieldname = "field_fileupload".$key;
	                            	$field = $_POST[$fieldname];
						    	 	$filename = time().'_'.basename( $_FILES[$key]['name']);
					   	 			$target_path = $CFG->dirroot."/local/user_registration/temp/".$filename;  
					            	if(move_uploaded_file($_FILES[$key]['tmp_name'], $target_path)) {  
					    				$updateimage = new stdClass();
					    				$updateimage->id = $details->id;
					    				$updateimage->typeid = $key;
					    				$updateimage->prerequisitetype = 'fileupload';
						    			$updateimage->field = $field;
						    			$updateimage->content = $filename;
						    			$updateimage->modified_date = time();
						    			$DB->update_record('lcl_prerequisite_data', $updateimage);
					        		} 
					     	}
					} else {
						if(!empty($_FILES[$key])) {
								$fieldname = "field_fileupload".$key;
	                            $field = $_POST[$fieldname];
								$filename = time().'_'.basename( $_FILES[$key]['name']);
								$target_path = $CFG->dirroot."/local/user_registration/temp/".$filename;  
						   		if(move_uploaded_file($_FILES[$key]['tmp_name'], $target_path)) {  
									$addimage  = new stdClass();
									$addimage->registration_id = base64_decode($_POST['id']);
						    		$addimage->field = $field;
									$addimage->content = $filename;
									$addimage->typeid = $key;
					    			$addimage->prerequisitetype = 'fileupload';
									$addimage->created_date  = time();
									$addimage->modified_date = time();
									$DB->insert_record('lcl_prerequisite_data', $addimage);
					    		}
					 	}
					}

				}
		    } 


            // askquestion_text_field
		    if($value == 'askquestion_text_field') {
				foreach (json_decode($_POST['typename_askquestion_text_field']) as $key => $data) {
					$details = $DB->get_record('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'askquestion_text_field', 'typeid'=>$key));
					$question_textfield = "question_textfield".$key;
					$fieldname = "field_textfield".$key;
	                $field = $_POST[$fieldname];
					if(!empty($_POST[$question_textfield])) {
						if ($DB->record_exists('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'askquestion_text_field', 'typeid'=>$key))) {
							$updatefield = new stdClass();
							$updatefield->id = $details->id;
							$updatefield->typeid = $key;
							$updatefield->prerequisitetype = 'askquestion_text_field';
							$updatefield->field = $field;
							$updatefield->content = $_POST[$question_textfield];
							$updatefield->modified_date = time();
							$DB->update_record('lcl_prerequisite_data', $updatefield);
						} else {
							$addfield  = new stdClass();
							$addfield->registration_id = base64_decode($_POST['id']);
							$addfield->typeid = $key;
							$addfield->prerequisitetype = 'askquestion_text_field';
							$addfield->field = $field;
							$addfield->content = $_POST[$question_textfield];
							$addfield->created_date  = time();
							$addfield->modified_date = time();
							$DB->insert_record('lcl_prerequisite_data', $addfield);
						}	
					}
				}
			}



			// askquestion_text_area
			if($value == 'askquestion_text_area') {
				foreach (json_decode($_POST['typename_askquestion_text_area']) as $key => $data) {
					$details = $DB->get_record('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'askquestion_text_area', 'typeid'=>$key));
					$question_textarea = "question_textarea".$key;
					$fieldname = "field_textarea".$key;
	                $field = $_POST[$fieldname];
					if(!empty($_POST[$question_textarea])) {
						if ($DB->record_exists('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'askquestion_text_area', 'typeid'=>$key))) {
							$updatefield = new stdClass();
							$updatefield->id = $details->id;
							$updatefield->typeid = $key;
							$updatefield->prerequisitetype = 'askquestion_text_area';
							$updatefield->field = $field;
							$updatefield->content = $_POST[$question_textarea];
							$updatefield->modified_date = time();
							$DB->update_record('lcl_prerequisite_data', $updatefield);
						} else {
							$addfield  = new stdClass();
							$addfield->registration_id = base64_decode($_POST['id']);
							$addfield->typeid = $key;
							$addfield->prerequisitetype = 'askquestion_text_area';
							$addfield->field = $field;
							$addfield->content = $_POST[$question_textarea];
							$addfield->created_date  = time();
							$addfield->modified_date = time();
							$DB->insert_record('lcl_prerequisite_data', $addfield);
						}	
					}
				}
			} 


			// askquestion_single_selection
			if($value == 'askquestion_single_selection') {
				foreach (json_decode($_POST['typename_askquestion_single_selection']) as $key => $data) {
					$details = $DB->get_record('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'askquestion_single_selection', 'typeid'=>$key));
					$questionradio = "questionradio";
					$fieldname = "field_questionradio".$key;
	                $field = $_POST[$fieldname];
					if(!empty($_POST[$questionradio])) {
						if ($DB->record_exists('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'askquestion_single_selection', 'typeid'=>$key))) {
							$updatefield = new stdClass();
							$updatefield->id = $details->id;
							$updatefield->typeid = $key;
							$updatefield->prerequisitetype = 'askquestion_single_selection';
							$updatefield->field = $field;
							$updatefield->content = $_POST[$questionradio];
							$updatefield->modified_date = time();
							$DB->update_record('lcl_prerequisite_data', $updatefield);
						} else {
							$addfield  = new stdClass();
							$addfield->registration_id = base64_decode($_POST['id']);
							$addfield->typeid = $key;
							$addfield->prerequisitetype = 'askquestion_single_selection';
							$addfield->field = $field;
							$addfield->content = $_POST[$questionradio];
							$addfield->created_date  = time();
							$addfield->modified_date = time();
							$DB->insert_record('lcl_prerequisite_data', $addfield);
						}	
					}
				}
			} 

			

			// askquestion_multiple_selection
			if($value == 'askquestion_multiple_selection') {
				foreach (json_decode($_POST['typename_askquestion_multiple_selection']) as $key => $data) {
					$details = $DB->get_record('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'askquestion_multiple_selection', 'typeid'=>$key));
					$question_multiple = "question_multiple".$key;
					$fieldname = "field_question_multiple".$key;
	                $field = $_POST[$fieldname];
					if(!empty($_POST[$question_multiple])) {
						if ($DB->record_exists('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'askquestion_multiple_selection', 'typeid'=>$key))) {
							$updatefield = new stdClass();
							$updatefield->id = $details->id;
							$updatefield->typeid = $key;
							$updatefield->prerequisitetype = 'askquestion_multiple_selection';
							$updatefield->field = $field;
							$updatefield->content = $_POST[$question_multiple];
							$updatefield->modified_date = time();
							$DB->update_record('lcl_prerequisite_data', $updatefield);
						} else {
							$addfield  = new stdClass();
							$addfield->registration_id = base64_decode($_POST['id']);
							$addfield->typeid = $key;
							$addfield->prerequisitetype = 'askquestion_multiple_selection';
							$addfield->field = $field;
							$addfield->content = $_POST[$question_multiple];
							$addfield->created_date  = time();
							$addfield->modified_date = time();
							$DB->insert_record('lcl_prerequisite_data', $addfield);
						}	
					}
				}
			}  


			// personalinfo
			if($value == 'personalinfo') {
		   	   	foreach (json_decode($_POST['typename_personalinfo']) as $key => $data) {
					$details = $DB->get_record('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'personalinfo', 'typeid'=>$key));
					$personal_info = "personal_info".$key;
					$fieldname = "field_personal_info".$key;
	                $field = $_POST[$fieldname];
					if(!empty($_POST[$personal_info])) {
						if ($DB->record_exists('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'prerequisitetype'=>'personalinfo', 'typeid'=>$key))) {
							$updatefield = new stdClass();
							$updatefield->id = $details->id;
							$updatefield->typeid = $key;
							$updatefield->prerequisitetype = 'personalinfo';
							$updatefield->field = $field;
							$updatefield->content = $_POST[$personal_info];
							$updatefield->modified_date = time();
							$DB->update_record('lcl_prerequisite_data', $updatefield);
						} else {
							$addfield  = new stdClass();
							$addfield->registration_id = base64_decode($_POST['id']);
							$addfield->typeid = $key;
							$addfield->prerequisitetype = 'personalinfo';
							$addfield->field = $field;
							$addfield->content = $_POST[$personal_info];
							$addfield->created_date  = time();
							$addfield->modified_date = time();
							$DB->insert_record('lcl_prerequisite_data', $addfield);
						}	
					}
				}
			}

	 	}

		$upd_data = new \stdClass();
		if($DB->record_exists('lcl_individual_enrollment', array('registration_id'=>base64_decode($_POST['id'])))) {
		   $upd_data->id = $_POST['enrollment_id'];
		   $upd_data->typenameprerequisite = serialize($prerequisitetype);
		   $DB->update_record('lcl_individual_enrollment', $upd_data);
		}

		if($DB->record_exists('lcl_corporate_enrollment', array('registration_id'=>base64_decode($_POST['id'])))) {
		   $upd_data->id = $_POST['enrollment_id'];
		   $upd_data->typenameprerequisite = serialize($prerequisitetype);
		   $DB->update_record('lcl_corporate_enrollment', $upd_data);
		}
    }

    if($_POST['level']>2) {
		redirect($CFG->wwwroot.'/local/user_registration/application_form.php?id='.$_POST['id']);
    } elseif ($_POST['level']<=2) {
		redirect($CFG->wwwroot.'/local/user_registration/home.php?id='.$_POST['id']);
    } else {
		redirect($CFG->wwwroot.'/local/user_registration/prerequisite.php?id='.$_POST['id']);
    }
}

echo $OUTPUT->header();

$addformaaa = new prerequisite_form($pageurl, $args);
$addformaaa->display();

echo $OUTPUT->footer();