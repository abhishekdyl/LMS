<?php 
require_once('../../../config.php');
require_once('coursesyncevents.php');

$events = new stdClass();
$events->id = 97;

$pr =  coursesyncevents::wp_course_created($events);

var_dump($pr);
