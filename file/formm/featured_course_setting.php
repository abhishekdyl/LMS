<?php
require_once('../config.php');
require_once("$CFG->libdir/formslib.php");
global $CFG, $DB, $PAGE, $USER;
$PAGE->set_title("Featured section setting");
$page        = optional_param('page', '', PARAM_TEXT);
$pageurl = $CFG->wwwroot.'/staticpages/featured_course_setting.php?page='.$page;
class featuredcourse_form extends moodleform {
    function definition() {
        global $CFG,$DB,$USER, $TEXTAREA_OPTIONS;
        $mform = $this->_form;
        $mform->addElement('header', 'formheader', "Featured Course");

        $mform->addElement('filemanager', 'file', 'IMAGE SETTINGS:', null,
                   array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));

        $mform->addElement('text', 'headline', 'HEADLINE SETTINGS:', 'maxlength="250" size="50"');
        
        $mform->addElement('textarea', 'summary', 'SUMMARY SETTINGS:', 'rows="10" cols="10"');

        $mform->addElement('text', 'button_label', 'BUTTON LABEL SETTINGS:', 'maxlength="250" size="50"');

        $mform->addElement('text', 'button_url', 'BUTTON URL SETTINGS:', 'maxlength="250" size="50"');
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'page');
        $this->add_action_buttons();
       
    }
}
$mform = new featuredcourse_form($pageurl);
$context = get_context_instance(CONTEXT_SYSTEM);

if($mform->is_cancelled()) {
	redirect($CFG->wwwroot.'/staticpages/featured_course_setting.php');
} else if ($fromform=$mform->get_data()){
    $filearea = 'imagearea';

    if($fromform->id){
        $fromform->modifiedby = $USER->id;
        $fromform->modifieddate = $USER->id;
        $bbb =$DB->update_record("course_featured_setting", $fromform);
        file_save_draft_area_files($fromform->file, $context->id, 'course_featured_setting_image', $filearea, $fromform->id);
    }else{
        $fromform->createdby = $USER->id;
        $fromform->createddate = time();
        $fromform->id =$DB->insert_record("course_featured_setting", $fromform);
        file_save_draft_area_files($fromform->file, $context->id, 'course_featured_setting_image', $filearea, $fromform->id);
    }
    redirect($CFG->wwwroot.'/staticpages/featured_course_setting.php');
}

echo $OUTPUT->header();
if($data = $DB->get_record_sql("SELECT * FROM {course_featured_setting} WHERE page=:page ORDER BY `id` DESC", array("page"=>$page))){ 
    $draftitemid = file_get_submitted_draft_itemid('image');
    file_prepare_draft_area($draftitemid, $context->id, 'course_featured_setting_image', 'imagearea', $data->id);
    $data->file = $draftitemid;
} else {
    $data = new stdClass();
    $data->page = $page;
}
$mform->set_data($data);
if(empty($page)){
    echo '
<h1>Featured Section setting</h1>
<a class="btn" href="'.$CFG->wwwroot.'/staticpages/featured_course_setting.php?page=vet">Vet Events </a>
<a class="btn" href="'.$CFG->wwwroot.'/staticpages/featured_course_setting.php?page=vetnurse">Nurse Events </a>
    ';
} else {
    $mform->display();
}
echo $OUTPUT->footer();

?>
