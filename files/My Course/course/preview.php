<?php
//  Display the course home page.

    require_once('../../config.php');
    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->libdir.'/completionlib.php');

    $id          = optional_param('id', 0, PARAM_INT);
    $name        = optional_param('name', '', PARAM_TEXT);
    $edit        = optional_param('edit', -1, PARAM_BOOL);
    $hide        = optional_param('hide', 0, PARAM_INT);
    $show        = optional_param('show', 0, PARAM_INT);
    $idnumber    = optional_param('idnumber', '', PARAM_RAW);
    $sectionid   = optional_param('sectionid', 0, PARAM_INT);
    $section     = optional_param('section', 0, PARAM_INT);
    $move        = optional_param('move', 0, PARAM_INT);
    $marker      = optional_param('marker',-1 , PARAM_INT);
    $switchrole  = optional_param('switchrole',-1, PARAM_INT); // Deprecated, use course/switchrole.php instead.
    $return      = optional_param('return', 0, PARAM_LOCALURL);

    $params = array();
    if (!empty($name)) {
        $params = array('shortname' => $name);
    } else if (!empty($idnumber)) {
        $params = array('idnumber' => $idnumber);
    } else if (!empty($id)) {
        $params = array('id' => $id);
    }else {
        print_error('unspecifycourseid', 'error');
    }

    $course = $DB->get_record('course', $params, '*', MUST_EXIST);

    $urlparams = array('id' => $course->id);

    // Sectionid should get priority over section number
    if ($sectionid) {
        $section = $DB->get_field('course_sections', 'section', array('id' => $sectionid, 'course' => $course->id), MUST_EXIST);
    }
    if ($section) {
        $urlparams['section'] = $section;
    }

    $PAGE->set_url($CFG->wwwroot.'/local/course/preview.php',array( 'id' =>$id));

    // Prevent caching of this page to stop confusion when changing page after making AJAX changes
    $PAGE->set_cacheable(false);

    context_helper::preload_course($course->id);
    $context = context_course::instance($course->id, MUST_EXIST);

    // Remove any switched roles before checking login
    if ($switchrole == 0 && confirm_sesskey()) {
        role_switch($switchrole, $context);
    }

    require_login($course);

    // Switchrole - sanity check in cost-order...
    $reset_user_allowed_editing = false;
    if ($switchrole > 0 && confirm_sesskey() &&
        has_capability('moodle/role:switchroles', $context)) {
        // is this role assignable in this context?
        // inquiring minds want to know...
        $aroles = get_switchable_roles($context);
        if (is_array($aroles) && isset($aroles[$switchrole])) {
            role_switch($switchrole, $context);
            // Double check that this role is allowed here
            require_login($course);
        }
        // reset course page state - this prevents some weird problems ;-)
        $USER->activitycopy = false;
        $USER->activitycopycourse = NULL;
        unset($USER->activitycopyname);
        unset($SESSION->modform);
        $USER->editing = 0;
        $reset_user_allowed_editing = true;
    }

    //If course is hosted on an external server, redirect to corresponding
    //url with appropriate authentication attached as parameter
    if (file_exists($CFG->dirroot .'/course/externservercourse.php')) {
        include $CFG->dirroot .'/course/externservercourse.php';
        if (function_exists('extern_server_course')) {
            if ($extern_url = extern_server_course($course)) {
                redirect($extern_url);
            }
        }
    }


    require_once($CFG->dirroot.'/calendar/lib.php');    /// This is after login because it needs $USER

    // Must set layout before gettting section info. See MDL-47555.
    $PAGE->set_pagelayout('course');
    $PAGE->add_body_class('limitedwidth');

    // Fix course format if it is no longer installed
    $format = course_get_format($course);
    $course->format = $format->get_format();

    // $PAGE->set_pagetype('course-view-' . $course->format);
    // $PAGE->set_other_editing_capability('moodle/course:update');
    // $PAGE->set_other_editing_capability('moodle/course:manageactivities');
    // $PAGE->set_other_editing_capability('moodle/course:activityvisibility');
    // if (course_format_uses_sections($course->format)) {
    //     $PAGE->set_other_editing_capability('moodle/course:sectionvisibility');
    //     $PAGE->set_other_editing_capability('moodle/course:movesections');
    // }

    // Preload course format renderer before output starts.
    // This is a little hacky but necessary since
    // format.php is not included until after output starts
    $format->get_renderer($PAGE);

    if ($reset_user_allowed_editing) {
        // ugly hack
        unset($PAGE->_user_allowed_editing);
    }
    if ($course->id == SITEID) {
        // This course is not a real course.
        redirect($CFG->wwwroot .'/?redirect=0');
    }

    // Determine whether the user has permission to download course content.
    $candownloadcourse = \core\content::can_export_context($context, $USER);

    // We are currently keeping the button here from 1.x to help new teachers figure out
    // what to do, even though the link also appears in the course admin block.  It also
    // means you can back out of a situation where you removed the admin block. :)
    if ($PAGE->user_allowed_editing()) {
        $buttons = $OUTPUT->edit_button($PAGE->url);
        $PAGE->set_button($buttons);
    }

    // If viewing a section, make the title more specific
    if ($section and $section > 0 and course_format_uses_sections($course->format)) {
        $sectionname = get_string('sectionname', "format_$course->format");
        $sectiontitle = get_section_name($course, $section);
        $PAGE->set_title(get_string('coursesectiontitle', 'moodle', array('course' => $course->fullname, 'sectiontitle' => $sectiontitle, 'sectionname' => $sectionname)));
    } else {
        $PAGE->set_title(get_string('coursetitle', 'moodle', array('course' => $course->fullname)));
    }

    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->footer();
