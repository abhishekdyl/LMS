<?php
require('../../config.php');
// require_once($CFG->dirroot."/course/externallib.php");
require_once("$CFG->libdir/formslib.php");
global $DB, $CFG, $USER;
class loginform extends moodleform{
  
  public function definition() {
    global $DB,$CFG,$USER;
    $mform = $this->_form; 
    $mform->addElement('filemanager', 'file', 'Login Page Image:', null, array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
    $this->add_action_buttons();
    }
}
$filearea = 'imagearea';

$mform  = new loginform();
$context = get_context_instance(CONTEXT_SYSTEM);

if($mform->is_cancelled()) {
	redirect($CFG->wwwroot.'/blocks/galgano_course_teacher/loginpage_image.php');
} else if ($fromform=$mform->get_data()){
    // echo "<pre>";
    // print_r($fromform);
    // die;

    if($fromform->id){
        // $bbb =$DB->update_record("login_form", $fromform);
        file_save_draft_area_files($fromform->file, $context->id, 'login_form_image', $filearea, 0);
    }else{
        file_save_draft_area_files($fromform->file, $context->id, 'login_form_image', $filearea, 0);
    }
    redirect($CFG->wwwroot.'/blocks/galgano_course_teacher/loginpage_image.php');
}

// file_save_draft_area_files($fromform->file, $context->id, 'course_featured_setting_image', $filearea, $fromform->id);
// $filearea = 'imagearea';
// $context = get_context_instance(CONTEXT_SYSTEM);
$draftitemid = file_get_submitted_draft_itemid('image');
file_prepare_draft_area($draftitemid, $context->id, 'login_form_image', $filearea, 0);
$data = new stdClass();
$data->file = $draftitemid;
$mform->set_data($data);

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

?>