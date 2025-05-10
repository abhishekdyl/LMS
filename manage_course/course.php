<?php
require_once('../../config.php');
require_once('courseform.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
global $DB, $CFG, $USER, $PAGE;
require_login();


$id = optional_param('id', 0, PARAM_INT); // Course id.
$categoryid = optional_param('category', 17, PARAM_INT); // Course category - can be changed in edit form.

//---------------API for get Wordpress Product Category List----------------------------------
$wpurl = get_config('local_manage_course','wpurl');
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $wpurl.'/product-category-listing/',///wp-content/themes/buddyboss-theme-child/category.php
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
// echo "<pre>";
// print_r($coursecat);
// echo "</pre>";

//---------------API for get Wordpress Product Tag List-----------------------------------
$curl2 = curl_init();
curl_setopt_array($curl2, array(
    CURLOPT_URL => $wpurl.'/product-tags-list/',
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
$responsetag = curl_exec($curl2);
curl_close($curl2);
$producttag = json_decode($responsetag, true);
if ($id) {
    // Editing course.
    if ($id == SITEID){
        // Don't allow editing of  'site course' using this form.
        throw new \moodle_exception('cannoteditsiteform');
    }
    $catewp = $DB->get_record('wpproduct', array('courseid'=>$id));
    $wpcategory = $catewp->categoryid;

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

$template = $DB->get_record('wpproduct',array('courseid'=>$id));

$args = array(
    'course' => $course,
    'category' => $category,
    'wpcategory' => $wpcategory,
    'returnto' => $returnto,
    'returnurl' => $returnurl,
    'fullcourse' => $fullcourse,
    'course_template' => $template->parent_template,
);
$mform = new customcourses_form($CFG->wwwroot."/local/manage_course/course.php?id=".$id, $args); // To pass custome data in form using argument
if($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/local/manage_course/courselist.php");
} else if ($formdata=$mform->get_data()){
    // echo "<pre>------------------------------------";
    // print_r($formdata);
    // echo "</pre>";
    // die;

    $formdata->customfield_age_group = implode(",",$formdata->customfield_age_group);
    $settingapp = get_config('local_manage_course','approved');
    $formdata->approval = (($settingapp == 1)? 0 : 1); 
    $catid = $formdata->pcategory;
    $wpcatname = $coursecat[$catid];
    $mdcatname = $DB->get_record('course_categories',array('name'=>$wpcatname));

    if(!empty($mdcatname)){
        $formdata->category = $mdcatname->id;
    }else{
        $catobj = new stdClass();
        $catobj->id = 0;
        $catobj->parent = 0;
        $catobj->name = $wpcatname;
        $category = core_course_category::create($catobj);
        $formdata->category = $category->id;
    }

    // echo "test";
    if(str_contains(strtolower($coursecat[$catid]), 'subscription')){
        $formdata->customfield_productype = 2; //Simple Subscription
    }else{
        $formdata->customfield_productype = 1; //Simple Product
    }

    // echo $coursecat[$catid];
 if(str_contains(strtolower($coursecat[$catid]), 'one')){
        $formdata->customfield_course_type = 1; //one-time course
    }else{
        $formdata->customfield_course_type = 2; //muli-time course
    }
   $formdata->tags = array();

    // echo "<pre>";
    // print_r($formdata);
    // die;

    if (empty($formdata->id)) {
        if(!empty($formdata->format1)){
            $newtemplatecourse =  new stdClass();
            $newtemplatecourse->courseid = $formdata->format1;
            $newtemplatecourse->returnto = 'course';
            $newtemplatecourse->returnurl = $formdata->returnurl;
            $newtemplatecourse->fullname = $formdata->fullname;
            $newtemplatecourse->shortname = $formdata->shortname;
            $newtemplatecourse->category = $formdata->category;
            $newtemplatecourse->visible = 1;
            $newtemplatecourse->startdate = time();
            $newtemplatecourse->enddate = 0;
            $newtemplatecourse->idnumber = '';
            $newtemplatecourse->userdata = 0;
            $newtemplatecourse->submitdisplay = "Copy and view";
            $mdata = $newtemplatecourse;

            $copydata = \copy_helper::process_formdata($mdata);
            $newcourse =  \copy_helper::create_copy($copydata);
            sleep(20);
            if($newcourse){
                //$newtemplatecourse->shortname;
                $cquery1 = "SELECT * FROM {course} where shortname LIKE ?";
                $newcourseid = $DB->get_record_sql($cquery1, array($newtemplatecourse->shortname));
                $mapdata = new stdclass();
                $mapdata->categoryid = $formdata->pcategory;
                $mapdata->parent_template = $formdata->format1;
                $mapdata->courseid = $newcourseid->id;
                $mapdata->userid = $USER->id;
                $mapdata->approval = $formdata->approval;
                $mapdata->post_data = json_encode($_POST['attribute']);
                $insertquery = $DB->insert_record('wpproduct',$mapdata);           
                // Get the context of the newly created course.
                $context = context_course::instance($newcourseid->id, MUST_EXIST);
                // Admins have all capabilities, so is_viewing is returning true for admins.
                // We are checking 'enroladminnewcourse' setting to decide to enrol them or not.

                if($context){
                    $teachrole = $DB->get_record('role',array('shortname'=>'editingteacher'));
                    if (!empty($teachrole)) {
                        enrol_try_internal_enrol($course->id, $USER->id, $teachrole->id);  // user enrol the course fuction
                    }
                }

                if($newcourseid->id){
                    $formdata->id = $newcourseid->id;
                    update_course($formdata, $editoroptions);
                }
            }
        }else{
            $course = create_course($formdata, $editoroptions); //course creation fuction
            $mapdata = new stdclass();
            $mapdata->categoryid = $formdata->pcategory;
            $mapdata->parent_template = $formdata->format1;
            $mapdata->courseid = $course->id;
            $mapdata->userid = $USER->id;
            $mapdata->approval = $formdata->approval;
            $mapdata->post_data = json_encode($_POST['attribute']);
            $insertquery = $DB->insert_record('wpproduct',$mapdata);
           
            // Get the context of the newly created course.
            $context = context_course::instance($course->id, MUST_EXIST);

            // Admins have all capabilities, so is_viewing is returning true for admins.
            // We are checking 'enroladminnewcourse' setting to decide to enrol them or not.
           
            if($context){
                $teachrole = $DB->get_record('role',array('shortname'=>'editingteacher'));
                if (!empty($teachrole)) {
                    enrol_try_internal_enrol($course->id, $USER->id, $teachrole->id);  // user enrol the course fuction
                }
            }

        }
        // The URL to take them to if they chose save and display.
    }else {

        if($record = $DB->get_record('wpproduct',array('courseid'=>$formdata->id))){
            $mapdata = new stdclass();
            $mapdata->id = $record->id;
            $mapdata->categoryid = $formdata->pcategory;
            $mapdata->parent_template = $formdata->format1;
            $mapdata->courseid = $record->courseid;
            $mapdata->userid = $USER->id;
            $mapdata->approval = $formdata->approval;
            $mapdata->post_data = json_encode($_POST['attribute']);
            $updatequery = $DB->update_record('wpproduct', $mapdata, $bulk=false);
        }
      
        if($formdata->format1){
            $coursee = get_course($formdata->format1);
            $formdata->format1 = $coursee->format;
        }
        // Save any changes to the files used in the editor.
        update_course($formdata, $editoroptions);
    }
    $courseurl = new moodle_url('/local/manage_course/courselist.php');
    redirect($courseurl);
}

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
$mform->display();
echo $OUTPUT->footer();
?>

<style>
    div.felement[data-fieldtype="textarea"] {
    flex-direction: column;
    align-items: flex-start;
}

#syllabus_count ,#metadescript_count {
    text-align: end;
    margin-top: 10px;
    display: inline-block;
    width: 35%;
    font-size: 15px;
}

</style>


<script type="text/javascript">
    $('<div id="syllabus_count"><span id="syllabus_current">0</span><span id="syllabus_maximum">/ 1300</span></div>').insertAfter("#id_customfield_syllabus");
    $('<div id="metadescript_count"><span id="metadescript_current">0</span><span id="metadescript_maximum">/ 137</span></div>').insertAfter("#id_customfield_metadescript");
$("#id_attributes").change(function () {
    var selected = $(this).val();
    console.log("selected: ", selected);
    const selectedattributes = allatributes.find((element, index, arr) => {
      return element.attribute_name == selected;
    });
    console.log("selectedattributes: ", selectedattributes);
   if($(`#element_${selected}`).length > 0){
    alert("element already added");
   } else if(selectedattributes){
    var newelement = ``;
    newelement +=`<div><lebel>${selectedattributes.attribute_label}</lebel><select name="attribute[${selectedattributes.attribute_name}]" id="element_${selectedattributes.attribute_name}">`;
    selectedattributes?.child.forEach((child) => {
      newelement +=`<option value="${child.slug}">${child.name}</option>`;
    });
    newelement +=`</select>`;
    newelement +=`<span type="button" style="cursor: pointer !important;" data-id="attribute[${selectedattributes.attribute_name}]" class="removeattribute"> X </span>`;
    newelement +=`</div>`;
    $("#additionalattributes").append(newelement);
    console.log("need to add element")
   }
});

$(document).on("click", ".removeattribute", function () {
  var rattr = $(this).data("id");
  $(this).closest("div").remove();
});



$('#id_customfield_syllabus').keyup(function() {
  var characterCount = $(this).val().length,
      current = $('#syllabus_current'),
      maximum = $('#syllabus_maximum'),
      theCount = $('#syllabus_count');
  current.text(characterCount);
  /*This isn't entirely necessary, just playin around*/
  if (characterCount >= 1300) {
    maximum.css('color', '#8f0001');
    current.css('color', '#8f0001');
    theCount.css('font-weight','bold');
  } else {
    maximum.css('color','#008000');
    current.css('color','#008000');
    theCount.css('font-weight','normal');
  } 
});
$('#id_customfield_syllabus').trigger("keyup");

$('#id_customfield_metadescript').keyup(function() {
  var characterCount = $(this).val().length,
      current = $('#metadescript_current'),
      maximum = $('#metadescript_maximum'),
      theCount = $('#metadescript_count');
  current.text(characterCount);
  /*This isn't entirely necessary, just playin around*/
  if (characterCount >= 137) {
    maximum.css('color', '#8f0001');
    current.css('color', '#8f0001');
    theCount.css('font-weight','bold');
  } else {
    maximum.css('color','#008000');
    current.css('color', '#008000');
    theCount.css('font-weight','normal');
  } 
});
$('#id_customfield_metadescript').trigger("keyup");

</script>
