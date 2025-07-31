<?php

require_once(__DIR__ . '/../../config.php');
require_once('classes/task/group_cron.php');
global $DB , $CFG,$USER;

print_r($USER->id);die;
// $obj = new \local_mdl_course_enroll\task\group_cron();
// print_r($obj->execute());