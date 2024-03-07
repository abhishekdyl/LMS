<?php

require_once('../../config.php');
global $DB, $USER;

require_login();
$is_siteadmin = is_siteadmin();
$context = \context_system::instance();
$current_logged_in_user =  $USER->id;
$has_capability = has_capability('local/assignment_subscription:createuser_subscription', $context, $current_logged_in_user);
if (!$has_capability) {
$urltogo_dashboard = $CFG->wwwroot.'/my/';
redirect($urltogo_dashboard, 'You do not have permission to view this page', null, \core\output\notification::NOTIFY_WARNING);
}


$uid = $_GET['uid'];

$obj = new stdClass();
$obj->id = $uid;
$obj->status = 1;

$DB->update_record('assign_subs_users', $obj);

$urltogo = $CFG->wwwroot.'/local/assignment_subscription/view_subscription.php';

redirect($urltogo, \core\output\notification::NOTIFY_SUCCESS);

?>
