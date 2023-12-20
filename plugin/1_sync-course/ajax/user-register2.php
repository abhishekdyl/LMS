<?php
require_once('../../../../wp-config.php');

// echo "<pre>";
// print_r($_POST);
$fname=$_POST['fname'];
$lname=$_POST['lname'];
$username=$_POST['username'];
$email=$_POST['email'];
$password=$_POST['password'];
$crmpassword=$_POST['crmpassword'];
$error_arr=array();
$flag=true;
if(!isset($fname) OR empty($fname)){
	$flag=false;
	array_push($error_arr,array('error'=>"Firstname is required",'key'=>"fname"));
}
if(!isset($lname) OR empty($lname)){
	$flag=false;
	array_push($error_arr,array('error'=>"Lastname is required",'key'=>'lname'));
}
if(!isset($password) OR empty($password)){
	$flag=false;
	array_push($error_arr,array('error'=>"password is required",'key'=>'password'));
}
if(!isset($username) OR empty(trim($username))){
	$flag=false;
	array_push($error_arr,array('error'=>"Username is required",'key'=>'username'));
}else{
	if(username_exists($username)){
		array_push($error_arr,array('error'=>"Username already exist",'key'=>'username'));
	}	
}
if(!isset($email) OR empty(trim($email))){
	$flag=false;
	array_push($error_arr,array('error'=>"Email is required",'key'=>'email'));
}else{
	if(email_exists($email)){
		array_push($error_arr,array('error'=>"Email already exist",'key'=>'email'));
	}
}
if(!isset($crmpassword) OR empty($crmpassword)){
	$flag=false;
	array_push($error_arr,array('error'=>"Confirm password is required",'key'=>'crmpassword'));
}
if(strcmp($crmpassword,$password) != 0){
	$flag=false;
	array_push($error_arr,array('error'=>"Enter same password",'key'=>'crmpassword'));
}
// echo "<pre>";
// print_r($error_arr);
if(sizeof($error_arr)){
	$msg['status']=false;
	$msg['data']=$error_arr;
	$msg['msg']='';
	echo json_encode($msg);
	exit();
}else{

    // Separate Data
    $default_newuser = array(
        'user_pass' =>  $password,
        'user_login' => $username,
        'user_email' => $email,
        'first_name' => $fname,
        'last_name' => $lname
    );

   $userdata = wp_insert_user($default_newuser);
   $user_id = $userdata;
   $pages = get_pages(array(
    'meta_key' => '_wp_page_template',
    'meta_value' => 'digital-litracy-test.php'
	));
	$pageid = '';
	foreach ($pages as $page) {
		$pageid = $page->ID;
	}
	$pagelink = get_permalink($pageid);

	// $data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
    // echo "<pre>";
    // print_r($userdata);
    // echo "-----------------------";
    // print_r($data);
    // echo $pagelink;
    // die;

$user = get_user_by( 'id', $user_id ); 

		if(!empty($user)){
			$msg['user']= $user;
			$msg['pagelink']=$pagelink;
			$msg['status']=true;
			$msg['data']=$error_arr;	
			// $msg['msg']='Thank you for registration ';
			//echo json_encode($msg);
		}else{
			$msg['status']=false;
			$msg['data']=$error_arr;
			$msg['msg']='Invalide user detail.';
			//echo json_encode($msg);
		}
		
	
	echo json_encode($msg);
	exit();
}