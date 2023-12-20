<?php
require_once('form.php');
    $id  = optional_param('id', 0, PARAM_INT);
    $delete  = optional_param('delete', 0, PARAM_INT);  //use for get the value by the vlaue of url name
$mform  = new narationsetting_form();
if(!empty($delete)){
	if($data =$DB->delete_records("employee", array("id"=>$delete))){
		redirect($CFG->wwwroot.'/formm/inform.php');
	}
}
if($mform->is_cancelled()) {
	redirect($CFG->wwwroot.'/formm/inform.php');
} else if ($fromform=$mform->get_data()){
	
	if($fromform->id){
		
		$fromform->modifiedby = $USER->id;
		$fromform->modifieddate = time();
		$DB->update_record("employee", $fromform);
	} else{
		$fromform->createdby = $USER->id;
		$fromform->createddate = time();
		//    echo "<pre>";
		//   print_r($fromform);
		//  echo "</pre>";
		
		$aaa =$DB->insert_record("employee", $fromform);
		
	}
	redirect($CFG->wwwroot.'/formm/inform.php');
    // redirect($redirecturl, "Data Saved Successfully");
}
echo $OUTPUT->header();
if(!empty($id)){
	if($data = $DB->get_record("employee", array("id"=>$id))){
		$mform->set_data($data);
	}
}
$mform->display();
$mform->displayreport();
echo $OUTPUT->footer();






 