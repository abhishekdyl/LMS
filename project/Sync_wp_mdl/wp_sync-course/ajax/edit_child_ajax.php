<?php
require_once('../../../../wp-config.php');
global $wpdb; 
$eduserid = $_POST['eduserid'];
$user_data = get_user_meta($eduserid);
$obj = new stdClass();
$obj->id = $eduserid;
$obj->ftname = $user_data['first_name'][0];
$obj->ltname = $user_data['last_name'][0];  
$obj->passw = base64_decode($user_data['member_password'][0]);

echo json_encode($obj);
exit();

?>