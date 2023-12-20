<?php 


function local_assignment_review_extend_navigation_course(\navigation_node $navigation, \stdClass $course, \context $context) {
    global $DB;

   $coursecontext = context_course::instance($course->id);

   if(has_capability('moodle/course:update', $coursecontext)){


    $url = new moodle_url("/local/assignment_review/index.php");
    $navigation->add(
    "Assignment Review",
    $url,
    navigation_node::TYPE_CONTAINER,
    "assignmentreview",
    "courseadmin",
    new pix_icon('i/report', '')
    );

}
}

?>