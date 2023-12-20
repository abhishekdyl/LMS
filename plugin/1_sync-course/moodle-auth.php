<?php
require_once('../../../wp-config.php');
global $wpdb;

// echo "ooooooooooo";
// die;
$json = file_get_contents('php://input');
// echo $json;
$data = json_decode($json);
// echo "<pre>";


$username=$data->username;
$password=$data->password;
//echo json_encode($data);
$userdata=wp_signon(array('user_login'=>$username,'user_password'=>$password));
// echo "<pre>";
// print_r($userdata);
// die;

if(!is_wp_error($userdata)){
	//$userdatasss=get_userdata($userdata->ID);
	$lastname = get_user_meta( $userdata->ID, 'last_name', true );
	$firstname = get_user_meta( $userdata->ID, 'first_name', true );
	$userdata->data->lastname=$lastname;
	$userdata->data->firstname=$firstname;
	// echo "<pre>";
	// echo "2222";
	// print_r($userdata);
	// die;
	$msg['status']=true;
	$msg['msg']="Thanks you for login Moodle plateform";
	$msg['data']=$userdata;
	echo json_encode($msg);
}else{
	$msg['status']=false;
	$msg['msg']="Credentials false";
	echo json_encode($msg);
}

