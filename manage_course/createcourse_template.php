<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
global $DB, $CFG, $USER, $PAGE;
require_login();
echo $OUTPUT->header();
$courseid = 55;
$returnurl = new moodle_url('/course/view.php', array('id' => $courseid));


$newtemplatecourse =  new stdClass();
$newtemplatecourse->courseid = 55;
$newtemplatecourse->returnto = 'course';
$newtemplatecourse->returnurl = $returnurl;
$newtemplatecourse->fullname = "LDS berry 4.2";
$newtemplatecourse->shortname = "LDS berry 4.2";
$newtemplatecourse->category = "17";
$newtemplatecourse->visible = 1;
$newtemplatecourse->startdate = time();
$newtemplatecourse->enddate = 0;
$newtemplatecourse->idnumber = '';
$newtemplatecourse->userdata = 0;
$newtemplatecourse->submitdisplay = "Copy and view";
$mdata = $newtemplatecourse;
$copydata = \copy_helper::process_formdata($mdata);
$newcourse =  \copy_helper::create_copy($copydata);
if($newcourse){
    echo $newtemplatecourse->shortname;
    echo $cquery1 = "SELECT * FROM {course} where shortname LIKE ?";
    $getcourseid = $DB->get_record_sql($cquery1, array($newtemplatecourse->shortname));


}


defined('MOODLE_INTERNAL') || die();
echo $OUTPUT->footer();
?>