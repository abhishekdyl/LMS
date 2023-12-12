<?php
	require_once('../../config.php');
	$id=required_param('id',PARAM_INT);

	$PAGE->set_context(context_course::instance($id));
	$course=$DB->get_record('course',array('id'=>$id));
	$PAGE->set_course($course);
	$PAGE->set_pagelayout('course');
	//$PAGE->set_pagetype('course-view');
	$PAGE->set_title('Course:'.$course->shortname);
	$PAGE->set_heading($course->shortname);

	//$PAGE->set_button('<a href="#" class="btn btn-info">Goto Course View Page</a>');
	$PAGE->set_url($CFG->wwwroot.'/local/course/preview.php',array( 'id' =>$id));
	require_login($course, true);
	echo $OUTPUT->header();

	echo $OUTPUT->footer();
?>
