<?php
require_once('../../../config.php');
global $DB, $PAGE, $CFG, $OUTPUT;
$PAGE->requires->jquery();
$id = required_param('id', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
require_login();

$enrolid = $DB->get_record_sql('SELECT * FROM {enrol} e INNER JOIN {user_enrolments} ue ON e.id = ue.enrolid where e.courseid = '.$courseid.' AND ue.userid ='.$id);
$cert = $DB->get_record('lcl_custom_certificate',array('userid'=>$id,'courseid'=>$courseid));
$context = context_course::instance($courseid);

require_once("$CFG->libdir/formslib.php");

class upload_certificate extends moodleform {

    function definition() {
        global $CFG;
       
        $mform = $this->_form;

        $mform->addElement('filemanager', 'certificate_file', 'Certificate', null,  [
            'subdirs' => 0,
            'maxbytes' => 100000000000,
            'maxfiles' => 50,
        ]);
        $mform->addHelpButton('certificate_file', 'courseoverviewfiles');

        $buttonarray[] = $mform->createElement('submit', 'submitbutton', "Save");
        $buttonarray[] = $mform->createElement('cancel', 'cancel',"Cancel");
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false); 
        
    }                           
}

$mform = new upload_certificate($CFG->wwwroot.'/local/user_registration/admin/certificate_upload.php?id='.$id.'&courseid='.$courseid, $args);

// 322669961
if($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/user/index.php?id=$courseid");
}else if ($fromform=$mform->get_data()){

    if(empty($cert)){
        $obj = new stdClass();
        $obj->userid = $id;
        $obj->courseid = $courseid;
        $obj->enrolid = $enrolid->id;
        // $obj->itemid = $fromform->certificate_file;
        // $obj->certificate = $filepath->pathnamehash;
        $obj->createdate = time();
        $obj->id = $DB->insert_record('lcl_custom_certificate',$obj);
    }else{
        $filepath = $DB->get_record_sql("SELECT * FROM {files} WHERE component='local_user_registration' AND itemid = $cert->id AND filesize > 0 AND filename IS NOT NULL ORDER BY id ASC LIMIT 1");
        $obj = new stdClass();
        $obj->id = $cert->id;
        $obj->userid = $id;
        $obj->courseid = $courseid;
        $obj->enrolid = $enrolid->id;
        $obj->itemid = $filepath->itemid;
        // $obj->certificate = $filepath->pathnamehash;
        $obj->modifiedate = time();
        $aaa = $DB->update_record('lcl_custom_certificate',$obj);
    }
    file_save_draft_area_files($fromform->certificate_file, $context->id, 'local_user_registration', 'user_certificate', $obj->id);
    // redirect($CFG->wwwroot."/user/index.php?id=$courseid");
}
echo $OUTPUT->header();
if($cert){
    $draftitemid = file_get_submitted_draft_itemid('image');
    file_prepare_draft_area($draftitemid, $context->id, 'local_user_registration', 'user_certificate', $cert->id);
    $cert->certificate_file = $draftitemid;
    $mform->set_data($cert);

}

$mform->display();
echo $OUTPUT->footer();
