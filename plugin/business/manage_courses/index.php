<?php
require_once('../../../config.php');
require_once('./edit_form.php');
require_once($CFG->dirroot.'/course/lib.php');
global $CFG, $DB, $USER, $PAGE, $TEXTAREA_OPTIONS;
$delete = optional_param('delete', 0, PARAM_INT); // Course id.
$confirmhash = optional_param('confirmhash', '', PARAM_ALPHANUM); // Confirmation hash.
require_login();
$userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
if(empty($userbrand)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
$branding = $DB->get_record('custom_branding', array("id"=>$userbrand->cbid));
if(empty($branding)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
$categoryid = $branding->brand_category;
if ($categoryid) {
    // Creating new course in this category.
    $course = null;
    $category = $DB->get_record('course_categories', array('id'=>$categoryid), '*', MUST_EXIST);
    $catcontext = context_coursecat::instance($category->id);
    // require_capability('moodle/course:create', $catcontext);
    $PAGE->set_context($catcontext);

} else {
    redirect($CFG->wwwroot.'/local/business/', 'Please try accessing this page after some time', null, \core\output\notification::NOTIFY_WARNING);
}
$pageurl = "$CFG->wwwroot/local/business/manage_courses/";
if(!empty($delete)){
    $course = $DB->get_record("course", array("id"=>$delete));
    if($categoryid != $course->category){
        redirect($pageurl, 'You don\'t have permission to delete this course', null, \core\output\notification::NOTIFY_ERROR);

    } else if ($confirmhash === md5($course->timemodified)) {
        require_sesskey();

        $strdeletingcourse = get_string("deletingcourse", "", $course->shortname);

        $PAGE->navbar->add($strdeletingcourse);
        $PAGE->set_title("$SITE->shortname: $strdeletingcourse");
        $PAGE->set_heading($SITE->fullname);

        echo $OUTPUT->header();
        echo $OUTPUT->heading($strdeletingcourse);
        // This might take a while. Raise the execution time limit.
        core_php_time_limit::raise();
        // We do this here because it spits out feedback as it goes.
        delete_course($course);
        echo $OUTPUT->heading( get_string("deletedcourse", "", $course->shortname) );
        // Update course count in categories.
        fix_course_sortorder();
        echo $OUTPUT->continue_button($pageurl);
        echo $OUTPUT->footer();
        exit;
    } else {
        $title = "Delete course: ".$course->fullname;
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        echo $OUTPUT->header();
        $strdeletecoursecheck = get_string("deletecoursecheck");
        $message = "{$strdeletecoursecheck}<br /><br />{$course->fullname} ({$course->shortname})";
        $continueurl = new moodle_url('/local/business/manage_courses/index.php', array('delete' => $course->id, 'confirmhash' => md5($course->timemodified)));
        $continuebutton = new single_button($continueurl, get_string('delete'), 'post');
        echo $OUTPUT->confirm($message, $continuebutton, $pageurl);
        echo $OUTPUT->footer();
        exit;
    }

}

// Prepare course and the editor.
$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);
$overviewfilesoptions = course_overviewfiles_options($course);
// Editor should respect category context if course context is not set.
$editoroptions['context'] = $catcontext;
$editoroptions['subdirs'] = 0;
$course = file_prepare_standard_editor($course, 'summary', $editoroptions, null, 'course', 'summary', null);
if ($overviewfilesoptions) {
    file_prepare_standard_filemanager($course, 'overviewfiles', $overviewfilesoptions, null, 'course', 'overviewfiles', 0);
}



// First create the form.
$args = array(
    'course' => $course,
    'category' => $category,
    'editoroptions' => $editoroptions,
    'returnto' => $returnto,
    'returnurl' => $returnurl,
    'userbrand' => $userbrand,
    'branding' => $branding,
);
   
$mform=new course_form(null, $args);
echo $OUTPUT->header();
echo "<a href='$CFG->wwwroot/local/business/'><button>Back</button></a>";
echo "<a href='$CFG->wwwroot/local/business/manage_courses/edit.php'><button>Create New Course</button></a>";
$mform->display_allcourses();
echo $OUTPUT->footer();