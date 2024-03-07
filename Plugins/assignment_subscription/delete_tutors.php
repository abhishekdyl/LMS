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


if (isset($_POST['tutors'])) {

	$tutors = $_POST['tutors'];
	foreach ($tutors as $val) {
		$obj_del = new stdClass();
		$obj_del->id = $val;
		$obj_del->deleted_status =1;
		$return = $DB->update_record('assign_subs_tutors', $obj_del);
	}
}

echo "Deleted successfully";

