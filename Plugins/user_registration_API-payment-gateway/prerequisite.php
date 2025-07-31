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
   $course_id = $_POST['course_id'];
   $upd_data = new \stdClass();
   if($DB->record_exists('lcl_individual_enrollment', array('registration_id' => base64_decode($_POST['id'])))){
        $upd_data->id = $_POST['enrollment_id'];
        $upd_data->typenameprerequisite = $prerequisitetype;
        $DB->update_record('lcl_individual_enrollment', $upd_data);
   }elseif($DB->record_exists('lcl_corporate_enrollment', array('registration_id' => base64_decode($_POST['id'])))){
        $upd_data->id = $_POST['enrollment_id'];
        $upd_data->typenameprerequisite = $prerequisitetype;
        $DB->update_record('lcl_corporate_enrollment', $upd_data);
   }
   if($prerequisitetype == 'Ask Question' || $prerequisitetype == 'Personal Info') {
			$prerequisitetype = $DB->get_record('customfield_field', array('shortname' => 'prerequisitetype'));
        	$customdata_prerequisitetype = $DB->get_record('customfield_data', array('fieldid' => $prerequisitetype->id, 'instanceid' => $course_id)); 
         	$prerequisitetype_set = $customdata_prerequisitetype->value-1;
      		$prerequisitetype_data = explode("\n", json_decode($prerequisitetype->configdata)->options);
      		$typenameprerequisite = $prerequisitetype_data[$prerequisitetype_set];
        	if($typenameprerequisite) {            
        			foreach ($prerequisitetype_data as $key => $data) {
   						$details = $DB->get_record('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'type'=>$key));
        				$content = "content".$key;
                    	if(!empty($_POST["$content"])){
   						if ($DB->record_exists('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'type'=>$key))) {
							$updatefield = new stdClass();
        					$updatefield->id = $details->id;
        					$updatefield->type = $key;
        					$updatefield->content = $_POST["$content"];
        					$updatefield->modified_date = time();
        					$DB->update_record('lcl_prerequisite_data', $updatefield);
            			} else {
					 		$addfield  = new stdClass();
					 		$addfield->registration_id = base64_decode($_POST['id']);
					 		$addfield->type = $key;
					 		$addfield->content = $_POST["$content"];
				    		$addfield->created_date  = time();
			       			$addfield->modified_date = time();
			       			$DB->insert_record('lcl_prerequisite_data', $addfield);
        				}	
                     }
     		   	}
          } 
   	  redirect($_POST['url']);
   } else if($prerequisitetype == 'File Upload') {
   	$details = $DB->get_record('lcl_prerequisite_data', array('registration_id'=>base64_decode($_POST['id']), 'type'=>$_POST['type']));
		if(!empty($details->id)) {
	   	 if(!empty($_FILES['content'])) {
	    	 $filename = time().'_'.basename( $_FILES['content']['name']);
	   	 	 $target_path = $CFG->dirroot."/local/user_registration/temp/".$filename;  
	            if(move_uploaded_file($_FILES['content']['tmp_name'], $target_path)) {  
	    			$updateimage = new stdClass();
	    			$updateimage->id = $details->id;
	    			$updateimage->type = $_POST['type'];
	    			$updateimage->content = $filename;
	    			$updateimage->modified_date = time();
	    			$DB->update_record('lcl_prerequisite_data', $updateimage);
	        } 
	     }
	     redirect($_POST['url']);
		} else {
			if(!empty($_FILES['content'])){
			$filename = time().'_'.basename( $_FILES['content']['name']);
			$target_path = $CFG->dirroot."/local/user_registration/temp/".$filename;  
			 	if(move_uploaded_file($_FILES['content']['tmp_name'], $target_path)) {  
					$addimage  = new stdClass();
					$addimage->registration_id = base64_decode($_POST['id']);
					$addimage->content = $filename;
					$addimage->type = $_POST['type'];
					$addimage->created_date  = time();
					$addimage->modified_date = time();
					$DB->insert_record('lcl_prerequisite_data', $addimage);
		    }
		 }
		 redirect($_POST['url']);
		}
   } else {
   		echo "Something is wrong";
   }
}



echo $OUTPUT->header();

$addformaaa = new prerequisite_form($pageurl, $args);
$addformaaa->display();

echo $OUTPUT->footer();