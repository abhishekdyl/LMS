<?php
	require_once('../../config.php');
	$id=required_param('id',PARAM_INT);

	$PAGE->set_context(context_course::instance($id));
	$PAGE->set_pagelayout('course');
	//$PAGE->set_pagelayout('standard');
	$course=$DB->get_record('course',array('id'=>$id));
	$PAGE->set_course($course);
	$PAGE->set_button('<a href="#" class="btn btn-info">Goto Course View Page</a>');
	$PAGE->set_url($CFG->wwwroot.'/local/sso/index.php',array( 'id' =>$id));
	require_login($course, true);
	echo $OUTPUT->header();

	echo $OUTPUT->footer();
?>