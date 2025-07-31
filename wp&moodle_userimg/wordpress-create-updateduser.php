<?php
require_once('../../../../wp-config.php');
global $wpdb; 
// if($_POST['delid']){
// 	$deluserid = $_POST['delid'];
// }

if($_POST['childid'] > 0){
	$childid = $_POST['childid'];
}
$fname=$_POST['fname'];
$lname=$_POST['lname'];
$randamn = rand(100,1000);
$username = generateusername($fname,$randamn); 
$email=$username.'@learndigo.com';
$password=$_POST['password'];
$parentid=$_POST['parentid'];
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

if(sizeof($error_arr)){
	$msg['status']=false;
	$msg['data']=$error_arr;
	$msg['msg']='';
	echo json_encode($msg);
	exit();
}else{
	if($childid > 0){
		$default_newuser = array(
			'ID' => $childid,
			'user_pass' =>  $password,
			'display_name' => $fname.' '.$lname,
			'first_name' => $fname,
			'last_name' => $lname
		);
		$userdata = wp_update_user($default_newuser);
		$edituser = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "users WHERE `id` = ".$childid."");

		
	}else{
		$default_newuser = array(
			'user_pass' =>  $password,
			'user_login' => $username,
			'user_email' => $email,
			'first_name' => $fname,
			'last_name' => $lname
		);
		$usr = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "users WHERE user_login = '".$username."'");
		if(empty($usr)){
			$totalmem = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "members_info WHERE `user_id` = ".$parentid."");
			$childcount = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "usermeta  WHERE `meta_key` LIKE 'parent_id' AND `meta_value` = '".$parentid."'");
			if($totalmem->member_count > count($childcount)){
				$userdata = wp_insert_user($default_newuser);
				if($userdata){
					$user_password = update_user_meta($userdata, 'member_password', base64_encode($password));
					$user_parent = update_user_meta($userdata, 'parent_id',$parentid);
	
					$request_data=array('parentid'=>$parentid,'user_email'=>$email,'wp_user_id'=>$userdata,'username'=>$username,'firstname'=>$fname,'lastname'=>$lname,'userpassword'=>md5($password));

					$setting_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
					$url=$setting_data->url;
	
		
				}
	
				$obj = new stdClass();
				$obj->id = $userdata;
				$obj->username = $username;
				$obj->fname = $fname;
				$obj->lname = $lname;
				$obj->email = $email;
				if($status==1){
					$msg['status']=true;
					$msg['data']=$obj;
					$msg['msg']='Your child has been registered';	
				}else{
					$msg['msg']='Your child are not registered In Moodle';
				}
			}else{
				$msg['status']="reach";
				$msg['msg']='You have reached the maximum limit of child x ' .$totalmem->member_count.' as per your subscription';	
				$msg['limit'] = $totalmem->member_count;	
			}
	
		}else{
			array_push($error_arr, $responsedata->data);
			$msg['status']=false;
			$msg['data']=$error_arr;
			$msg['msg']='';
		}
	}
	echo json_encode($msg);
	// echo json_encode($moodle_user);
	exit();
}