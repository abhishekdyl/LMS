<?php
require_once('../../config.php');
global $DB, $CFG, $PAGE, $USER;
require_once($CFG->dirroot . '/mod/zoom/lib.php');
require_once($CFG->dirroot . '/mod/zoom/locallib.php');
require_once($CFG->dirroot . '/mod/zoom/classes/webservice.php');
require_once($CFG->dirroot . '/course/modlib.php');
$course = $DB->get_record("course", array("id"=>102));
$zoom = new stdClass();
$zoom->name = 'testmeeting';
$zoom->start_time = time();
$zoom->duration = 3600;
$zoom->host_id = zoom_get_user_id();
$zoom->course = $course->id;
$zoom->section = 0;
$zoom->module = 37;
$zoom->modulename = 'zoom';
$zoom->visible = 1;
$zoom->visibleoncoursepage = 1;


// $response = add_moduleinfo($zoom, $course);

echo '<pre>';
print_r($response);
// function zoom_add_instance($zoom);
echo '</pre>';
die;


?>
