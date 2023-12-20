<?php
require_once('../../../config.php');
require_once('./edit_form.php');
require_once($CFG->dirroot.'/course/lib.php');
global $CFG, $DB, $USER, $PAGE, $TEXTAREA_OPTIONS;
$id = optional_param('id', 0, PARAM_INT); // Course id.
require_login();
$userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
if(empty($userbrand)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
$branding = $DB->get_record('custom_branding', array("id"=>$userbrand->cbid));
if(empty($branding)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
$returnurl = new moodle_url($CFG->wwwroot . '/local/business/');
$reloadurl = new moodle_url($CFG->wwwroot . '/local/business/manage_courses/');
$categoryid = $branding->brand_category;
if ($id) {
    // Editing course.
    if ($id == SITEID){
        // Don't allow editing of  'site course' using this from.
        redirect($CFG->wwwroot.'/local/business/', 'You don\'t have permision to edit this course', null, \core\output\notification::NOTIFY_WARNING);
    }
    // Login to the course and retrieve also all fields defined by course format.
    $course = get_course($id);
    require_login($course);
    $course = course_get_format($course)->get_course();
    if($course->category != $categoryid){
        redirect($CFG->wwwroot.'/local/business/', 'You don\'t have permision to edit this course', null, \core\output\notification::NOTIFY_WARNING);
    }
    $category = $DB->get_record('course_categories', array('id'=>$course->category), '*', MUST_EXIST);
    $coursecontext = context_course::instance($course->id);
    // require_capability('moodle/course:update', $coursecontext);

} else if ($categoryid) {
    // Creating new course in this category.
    $course = null;
    $category = $DB->get_record('course_categories', array('id'=>$categoryid), '*', MUST_EXIST);
    $catcontext = context_coursecat::instance($category->id);
    // require_capability('moodle/course:create', $catcontext);
    $PAGE->set_context($catcontext);

} else {
    redirect($CFG->wwwroot.'/local/business/', 'Please try accessing this page after some time', null, \core\output\notification::NOTIFY_WARNING);
}
// Prepare course and the editor.
$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);
$overviewfilesoptions = course_overviewfiles_options($course);
if (!empty($course)) {
    // Add context for editor.
    
    $editoroptions['context'] = $coursecontext;
    $editoroptions['subdirs'] = file_area_contains_subdirs($coursecontext, 'course', 'summary', 0);

    $course = file_prepare_standard_editor($course, 'summary', $editoroptions, $coursecontext, 'course', 'summary', 0);
    if ($overviewfilesoptions) {
        // print_r($overviewfilesoptions);
        // die();
        file_prepare_standard_filemanager($course, 'overviewfiles', $overviewfilesoptions, $coursecontext, 'course', 'overviewfiles', 0);
    }

    // Inject current aliases.
    $aliases = $DB->get_records('role_names', array('contextid'=>$coursecontext->id));
    foreach($aliases as $alias) {
        $course->{'role_'.$alias->roleid} = $alias->name;
    }

    // Populate course tags.
    $course->tags = core_tag_tag::get_item_tags_array('core', 'course', $course->id);

} else {
    // Editor should respect category context if course context is not set.
    $editoroptions['context'] = $catcontext;
    $editoroptions['subdirs'] = 0;
    $course = file_prepare_standard_editor($course, 'summary', $editoroptions, null, 'course', 'summary', null);
    if ($overviewfilesoptions) {
        file_prepare_standard_filemanager($course, 'overviewfiles', $overviewfilesoptions, null, 'course', 'overviewfiles', 0);
    }
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


if($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/local/business/manage_courses/");
} else if ($data=$mform->get_data()){
    $data->shortname = $data->fullname;
    $data->category = $categoryid;
    $data->format = "topics";
    $data->numsections = 1;
    $data->hiddensections = 0;
    $data->coursedisplay = 0;
    $data->addcourseformatoptionshere = 0;
    $data->lang =""; 
    $data->newsitems = 0;
    $data->showgrades = 0;
    $data->showreports = 0;
    $data->maxbytes = 0;
    $data->enablecompletion = 1;
    $data->groupmode = 0;
    $data->groupmodeforce = 0;
    $data->defaultgroupingid = 0;
    $data->tags = array();
    if (empty($course->id)) {
        // In creating the course.
        $course = create_course($data, $editoroptions);
        $gettopic = $DB->get_record_sql("SELECT * FROM {course_sections} WHERE course = ".$course->id." AND section=1  AND name IS NULL");
        if(empty($gettopic->name)){
            $updatedetopic = new stdClass();
            $updatedetopic->id             = $gettopic->id;
            $updatedetopic->name           = "Course Content";
            $updatedetopic->timemodified    = time();
            $DB->update_record("course_sections",$updatedetopic);
        }

        $context = context_course::instance($course->id, MUST_EXIST);
        if (!empty($CFG->brandmaincategoryroleid) and !is_viewing($context, NULL, 'moodle/role:assign') and !is_enrolled($context, NULL, 'moodle/role:assign')) {
            // Deal with course creators - enrol them internally with default role.
            // Note: This does not respect capabilities, the creator will be assigned the default role.
            // This is an expected behaviour. See MDL-66683 for further details.
            enrol_try_internal_enrol($course->id, $USER->id, $CFG->brandmaincategoryroleid);
        }
        // The URL to take them to if they chose save and display.
        $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
    } else {
        $gettopic = $DB->get_record_sql("SELECT * FROM {course_sections} WHERE course = ".$course->id." AND section=1  AND name IS NULL");
        if(empty($gettopic->name)){
            $updatedetopic = new stdClass();
            $updatedetopic->id             = $gettopic->id;
            $updatedetopic->name           = "Course Content";
            $updatedetopic->timemodified    = time();
            $DB->update_record("course_sections",$updatedetopic);
        }
        update_course($data, $editoroptions);
        $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
    }
    if (isset($data->saveanddisplay)) {
        // Redirect user to newly created/updated course.
        redirect($courseurl);
    } else {
        // Save and return. Take them back to wherever.
        redirect($reloadurl);
    }
}
if(!empty($course)){
    $mform->set_data($course);
}
echo $OUTPUT->header();
echo "<a href='$CFG->wwwroot/local/business/manage_courses/'><button>Back</button></a>";
$mform->display();
echo $OUTPUT->footer();