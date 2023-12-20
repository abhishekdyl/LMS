<?php

require_once('../../../wp-config.php');

$json = file_get_contents('php://input');


// Converts it into a PHP object
$data = json_decode($json);
$sql="SELECT * FROM {$wpdb->prefix}users WHERE user_login='".$data->username."' OR user_email='".$data->useremail."'";
$userdata=$wpdb->get_row($sql);
if($userdata){
	$litracy_status=get_user_meta($userdata->ID ,'litracy_status', true );
	if(!$litracy_status){
		update_user_meta($userdata->ID,'litracy_status',$data->userlitracy_status);
	}

}
