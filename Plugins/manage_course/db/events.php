<?php
$observers = array(
    array(
        'eventname'   => '\core\event\course_created',
        'callback'    => '\local_manage_course\coursenew::wp_course_created',
        'internal'  => true,
    ),
    array(
        'eventname'   => '\core\event\course_updated',
        'callback'    => '\local_manage_course\coursenew::wp_course_updated',
        'internal'  => true,
    ),
);
?>