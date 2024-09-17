<?php

require_once('../../config.php');

global $DB, $USER, $PAGE;
$PAGE->requires->jquery();

require_login();
$is_siteadmin = is_siteadmin();
$context = \context_system::instance();
$current_logged_in_user =  $USER->id;


$sqlQ = "SELECT * FROM {assign_subs_transaction} WHERE userid='$current_logged_in_user' ORDER BY id DESC LIMIT 1";
$result = $DB->get_record_sql($sqlQ);
$secretKey = get_config('local_assignment_subscription','secretKey');
$publishableKey =  get_config('local_assignment_subscription','publishableKey');

if($result = $DB->get_record_sql($sqlQ))
{

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/subscriptions/'.$result->stripe_subscription_id);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $secretKey);
	$response = curl_exec($ch);

	$obj_upd = new stdClass();
	$obj_upd->id = $result->id;
	$obj_upd->stripe_canceled_status =1;
	$DB->update_record('assign_subs_transaction', $obj_upd);

	$chk_user=  "SELECT * FROM {assign_subs_users} WHERE userid='$current_logged_in_user'";
	$row_user = $DB->get_record_sql($chk_user);

	$obj_upd_usr = new stdClass();
	$obj_upd_usr->id = $row_user->id;
	$obj_upd_usr->stripe_canceled_status =1;
	$DB->update_record('assign_subs_users', $obj_upd_usr);

	curl_close($ch);
	$urltogo_dashboard = $CFG->wwwroot.'/local/assignment_subscription/index.php';
	redirect($urltogo_dashboard, null, \core\output\notification::NOTIFY_SUCCESS);

}
