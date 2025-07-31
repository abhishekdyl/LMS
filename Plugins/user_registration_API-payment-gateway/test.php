<?php

require_once('../../config.php');
global $DB;

$courseid = 4;

$context = context_course::instance($courseid);
echo "Context id: ".$context->id;