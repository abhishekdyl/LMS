<?php

function local_business_after_require_login() {
	global $PAGE, $COURSE, $CFG;
	// if($PAGE->context && ($PAGE->context->contextlevel === CONTEXT_COURSE || $PAGE->context->contextlevel === CONTEXT_MODULE)){
     //     if (!$COURSE->visible && !has_capability('moodle/course:viewhiddencourses', context_course::instance($COURSE->id))) {
     //          redirect($CFG->wwwroot);
     //     }
     // }
}