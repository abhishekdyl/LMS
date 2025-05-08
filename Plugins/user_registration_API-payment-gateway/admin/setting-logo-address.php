<?php
require('../../../config.php');
global $DB, $CFG;
require_once($CFG->dirroot.'/local/user_registration/admin/setting-logo-address-form.php');

$type = required_param('type', PARAM_RAW);
$PAGE->set_context(context_system::instance());
// $PAGE->set_pagelayout('customblock');

$pageurl = $CFG->wwwroot."/local/user_registration/admin/setting-logo-address.php?type=".$type;
$args = array("type" => $type, "url" => $pageurl);
$type = base64_decode($type);

$PAGE->set_title('Assessor Panel');

if($_POST['submitbutton']) {
   $details = $DB->get_record('lcl_logo_address',array('type'=>base64_decode($_POST['type'])));
   if(!empty($details->id)) {
        if(!empty($_FILES['content'])) {
        	$filename = time().'_'.basename( $_FILES['content']['name']);
         	$target_path = $CFG->dirroot."/local/user_registration/temp/".$filename;  
		 	if(move_uploaded_file($_FILES['content']['tmp_name'], $target_path)) {  
        		 $updateimage = new stdClass();
            	 $updateimage->id = $details->id;
        		 $updateimage->type = base64_decode($_POST['type']);
        		 $updateimage->logo = $filename;
        		 $updateimage->address = $_POST['address'];
       		 	 $updateimage->modified_date = time();
       		 	 $DB->update_record('lcl_logo_address', $updateimage);
	     	} 
        }
        redirect($_POST['url']);
	 } else {
        if(!empty($_FILES['content'])) {
        	$filename = time().'_'.basename( $_FILES['content']['name']);
       		$target_path = $CFG->dirroot."/local/user_registration/temp/".$filename;  
		 	if(move_uploaded_file($_FILES['content']['tmp_name'], $target_path)) {  
        		$addimage  = new stdClass();
        		$addimage->type = base64_decode($_POST['type']);
        		$addimage->logo = $filename;
        		$addimage->address = $_POST['address'];
        		$addimage->created_date  = time();
        		$addimage->modified_date = time();
        		$DB->insert_record('lcl_logo_address', $addimage);
	     	}
        }
        redirect($_POST['url']);
	} 
}


echo $OUTPUT->header();

$addformaaa = new setting_logo_address_form($pageurl, $args);
$addformaaa->display();

echo $OUTPUT->footer();