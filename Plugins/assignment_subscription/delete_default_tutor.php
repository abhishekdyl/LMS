<?php

require_once('../../config.php');
global $DB, $USER;

require_login();
$is_siteadmin = is_siteadmin();
$context = \context_system::instance();
$current_logged_in_user =  $USER->id;
$has_capability = has_capability('local/assignment_subscription:delete_subscription', $context, $current_logged_in_user);
if (!$has_capability) {
$urltogo_dashboard = $CFG->wwwroot.'/my/';
redirect($urltogo_dashboard, 'You do not have permission to view this page', null, \core\output\notification::NOTIFY_WARNING);
}

if (isset($_GET['tutor_id'])) {

$DelSubLim = $_GET['tutor_id'];
$DB->delete_records('assign_subs_default_tutor', array('tutor_id' => $DelSubLim));



$urltogo = $CFG->wwwroot.'/local/assignment_subscription/plugin_setting.php';

redirect($urltogo, \core\output\notification::NOTIFY_SUCCESS);

}