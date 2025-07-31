<?php
require_once('../../config.php');
global $DB, $CFG;

$id = base64_decode($_GET['id']);
$fileid = base64_decode($_GET['fileid']);

if (isset($_GET['fileid'])) {
	if (!empty($_GET['fileid']) && !empty($_GET['id'])) {
    	if ($DB->record_exists('lcl_registration', ['id' => $id])) {
        	$get_record = $DB->get_record('lcl_prerequisite_data', ['id' => $fileid]);
	    	$DB->delete_records('lcl_prerequisite_data', array('id' => $fileid));
        	$filename = $CFG->dirroot.'/local/user_registration/temp/'.$get_record->content;
        	if (file_exists($filename)) {
			    if (unlink($filename)) {
    			    $msg = \core\output\notification::NOTIFY_SUCCESS;
    			} else {
			    	$msg = \core\output\notification::NOTIFY_WARNING;
    			}
			} 
        } else {
      	  	$msg = \core\output\notification::NOTIFY_WARNING;
        }
	} else {
      	$msg = \core\output\notification::NOTIFY_WARNING;
    }
}

redirect($CFG->wwwroot.'/local/user_registration/prerequisite.php?id='.base64_encode($id), $msg);