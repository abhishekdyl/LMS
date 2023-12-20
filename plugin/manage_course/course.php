<?php
require_once('../../config.php');
require_once('courseform.php');
require_once($CFG->dirroot.'/course/lib.php');
global $DB, $CFG, $USER, $PAGE;
require_login();


$id = optional_param('id', 0, PARAM_INT); // Course id.
$categoryid = optional_param('category', 17, PARAM_INT); // Course category - can be changed in edit form.
// $categoryid = '17';

//--------------------------------------------------
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://staging.lemons-aid.com/product-category-listing/',///wp-content/themes/buddyboss-theme-child/category.php
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Cookie: mailchimp_landing_site=https%3A%2F%2Fstaging.lemons-aid.com%2Fproduct-category-listing%2F'
    ),
));
$response = curl_exec($curl);
curl_close($curl);
$coursecat = json_decode($response, true);
//--------------------------------------------------


if ($id) {
    // Editing course.
    if ($id == SITEID){
        // Don't allow editing of  'site course' using this from.
        throw new \moodle_exception('cannoteditsiteform');
    }

    // Login to the course and retrieve also all fields defined by course format.
    $course = get_course($id);
    require_login($course);
    $course = course_get_format($course)->get_course();

    $category = $DB->get_record('course_categories', array('id'=>$course->category), '*', MUST_EXIST);
    $coursecontext = context_course::instance($course->id);
    require_capability('moodle/course:update', $coursecontext);

} else if ($categoryid) {
    // Creating new course in this category.
    $course = null;
    require_login();
    $category = $DB->get_record('course_categories', array('id'=>$categoryid), '*', MUST_EXIST);
    $catcontext = context_coursecat::instance($category->id);
    require_capability('moodle/course:create', $catcontext);
    $PAGE->set_context($catcontext);

} else {
    // Creating new course in default category.
    $course = null;
    require_login();
    $category = core_course_category::get_default();
    $catcontext = context_coursecat::instance($category->id);
    require_capability('moodle/course:create', $catcontext);
    $PAGE->set_context($catcontext);
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





if(is_siteadmin($USER->id)){
    $coursesql = 'SELECT wpp.id,wpp.courseid,c.fullname FROM {wpproduct} wpp INNER JOIN {course} c ON wpp.courseid = c.id INNER JOIN {course_categories} cat ON c.category = cat.id';
    $courdata = $DB->get_records_sql($coursesql,array());   
}else{
    $coursesql = 'SELECT wpp.id,wpp.courseid,c.fullname FROM {wpproduct} wpp INNER JOIN {course} c ON wpp.courseid = c.id INNER JOIN {course_categories} cat ON c.category = cat.id WHERE wpp.userid = ?';
    $courdata = $DB->get_records_sql($coursesql,array($USER->id));   
}

$courseid = array_column($courdata,'courseid'); // To create saperate array
$fullnames = array_column($courdata,'fullname');
$fullcourse = array_combine($courseid,$fullnames); // combine '[$courseid]=>$fullnames'


$args = array(
    'course' => $course,
    'category' => $category,
    'editoroptions' => $editoroptions,
    'returnto' => $returnto,
    'returnurl' => $returnurl,
    'fullcourse' => $fullcourse,
);
$mform = new customcourses_form($CFG->wwwroot."/local/manage_course/course.php?id=".$id, $args); // To pass custome data in form using argument
if($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/local/manage_course/courselist.php");
} else if ($formdata=$mform->get_data()){

    $catid = $formdata->pcategory;
// echo $coursecat[$catid];
 if(str_contains(strtolower($coursecat[$catid]), 'subscription')){
        $formdata->customfield_productype = 2; //Simple Subscription
    }else{
        $formdata->customfield_productype = 1; //Simple Product
    }
 if(str_contains(strtolower($coursecat[$catid]), 'one')){
        $formdata->customfield_course_type = 1; //one-time course
    }else{
        $formdata->customfield_course_type = 2; //muli-time course
    }
   $formdata->tags = array();

// echo "<pre>";
// print_r($formdata);
// echo "</pre>";
// die;


    if (empty($course->id)) {
        $course = create_course($formdata, $editoroptions);
        // print_r($course);
        // echo "</pre>";
        // die;
        $mapdata = new stdclass();
        $mapdata->categoryid = $formdata->pcategory;
        $mapdata->courseid = $course->id;
        $mapdata->userid = $USER->id;
        $insertquery = $DB->insert_record('wpproduct',$mapdata);
       
        // Get the context of the newly created course.
        $context = context_course::instance($course->id, MUST_EXIST);

        // Admins have all capabilities, so is_viewing is returning true for admins.
        // We are checking 'enroladminnewcourse' setting to decide to enrol them or not.
        if (is_siteadmin($USER->id)) {
            $enroluser = $CFG->enroladminnewcourse;
        } else {
            $enroluser = !is_viewing($context, null, 'moodle/role:assign');
        }

        if (!empty($CFG->creatornewroleid) and $enroluser and !is_enrolled($context, null, 'moodle/role:assign')) {
            // Deal with course creators - enrol them internally with default role.
            // Note: This does not respect capabilities, the creator will be assigned the default role.
            // This is an expected behaviour. See MDL-66683 for further details.
            enrol_try_internal_enrol($course->id, $USER->id, $CFG->creatornewroleid);
        }

        // The URL to take them to if they chose save and display.
    }else {
        // Save any changes to the files used in the editor.
        update_course($formdata, $editoroptions);
    }
    $courseurl = new moodle_url('/local/manage_course/courselist.php');
    redirect($courseurl);
}

// Print the form.

$site = get_site();

$streditcoursesettings = get_string("editcoursesettings");
$straddnewcourse = get_string("addnewcourse");
$stradministration = get_string("administration");
$strcategories = get_string("categories");

if (!empty($course->id)) {
    // Navigation note: The user is editing a course, the course will exist within the navigation and settings.
    // The navigation will automatically find the Edit settings page under course navigation.
    $pagedesc = $streditcoursesettings;
    $title = $streditcoursesettings;
    $fullname = $course->fullname;
} else {
    // The user is adding a course, this page isn't presented in the site navigation/admin.
    // Adding a new course is part of course category management territory.
    // We'd prefer to use the management interface URL without args.
    $managementurl = new moodle_url('/course/management.php');
    // These are the caps required in order to see the management interface.
    $managementcaps = array('moodle/category:manage', 'moodle/course:create');
    if ($categoryid && !has_any_capability($managementcaps, context_system::instance())) {
        // If the user doesn't have either manage caps then they can only manage within the given category.
        $managementurl->param('categoryid', $categoryid);
    }
    // Because the course category interfaces are buried in the admin tree and that is loaded by ajax
    // we need to manually tell the navigation we need it loaded. The second arg does this.
    navigation_node::override_active_url(new moodle_url('/course/index.php', ['categoryid' => $category->id]), true);
    $PAGE->set_primary_active_tab('home');
    $PAGE->navbar->add(get_string('coursemgmt', 'admin'), $managementurl);

    $pagedesc = $straddnewcourse;
    $title = "$site->shortname: $straddnewcourse";
    $fullname = format_string($category->name);
    $PAGE->navbar->add($pagedesc);
}

$PAGE->set_title($title);
$PAGE->add_body_class('limitedwidth');
$PAGE->set_heading($fullname);

echo $OUTPUT->header();
// echo "<pre>";
// print_r($coursecat);
// echo "</pre>";
$mform->display();
echo $OUTPUT->footer();
