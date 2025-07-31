<?php
require_once('../../../config.php');
require_once('./edit_form.php');
global $DB, $CFG, $PAGE, $USER;
require_login();
$userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
if(empty($userbrand)){
    redirect($CFG->wwwroot);
}
$branding = $DB->get_record('custom_branding', array("id"=>$userbrand->cbid));
if(empty($branding)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
require_once($CFG->libdir.'/accesslib.php');
$brandingcontext = context_coursecat::instance($branding->brand_category);
$roleid = $DB->get_field_sql("select id from {role} where shortname=?", array("companyadmin"));
if(empty($roleid)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
if(!user_has_role_assignment($USER->id, $roleid, $brandingcontext->id)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
$args = array(
    'userbrand' => $userbrand,
    'branding' => $branding,
);

$mform = new learning_pro(null, $args);
echo $OUTPUT->header();
echo "<a href='$CFG->wwwroot/local/business/'><button>Back</button></a>";
echo "<a href='$CFG->wwwroot/local/business/learning_program/edit.php'><button>Create New Learning Program</button></a>";
$mform->display_allprograms();
echo $OUTPUT->footer();
