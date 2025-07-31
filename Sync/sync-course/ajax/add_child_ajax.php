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
$email=$username.'@studyif.com';
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
		// echo "<pre>";
		// print_r($default_newuser);
		// print_r($edituser);
		// die;
		if($userdata){
			$user_password = update_user_meta($userdata, 'member_password', base64_encode($password));
			$user_parent = update_user_meta($userdata, 'parent_id',$parentid);

			$request_data=array('parentid'=>$parentid,'user_email'=>$edituser->user_email,'username'=>$edituser->user_login,'firstname'=>$fname,'lastname'=>$lname,'userpassword'=>md5(base64_decode($password)));
			$setting_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
			$url=$setting_data->url;

			$curl =curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url.'/local/coursesync/wp-childuser.php',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>json_encode((object)$request_data),
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json'
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);
		
			$obj = new stdClass();
			$obj->username = $username;
			$obj->fname = $fname;
			$obj->lname = $lname;
			$obj->email = $email;
			$msg['status']='update';
			// $msg['data']=$obj;
			$msg['msg']='User has been updated';	
		}
	}else{
		// Separate Data
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
			$childcount = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "usermeta  WHERE `meta_key` LIKE '%parent_id%' AND `meta_value` = '".$parentid."'");
	
			if($totalmem->member_count > count($childcount)){
				$userdata = wp_insert_user($default_newuser);
				// printf($userdata);
				// die;
				if($userdata){
					$user_password = update_user_meta($userdata, 'member_password', base64_encode($password));
					$user_parent = update_user_meta($userdata, 'parent_id',$parentid);
	
					$request_data=array('parentid'=>$parentid,'user_email'=>$email,'username'=>$username,'firstname'=>$fname,'lastname'=>$lname,'userpassword'=>md5(base64_decode($password)));
					$setting_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
					$url=$setting_data->url;
	
					$curl =curl_init();
					curl_setopt_array($curl, array(
						CURLOPT_URL => $url.'/local/coursesync/wp-childuser.php',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS =>json_encode((object)$request_data),
						CURLOPT_HTTPHEADER => array(
							'Content-Type: application/json'
						),
					));
					$response = curl_exec($curl);
					curl_close($curl);				
				}
	
				$obj = new stdClass();
				$obj->id = $userdata;
				$obj->username = $username;
				$obj->fname = $fname;
				$obj->lname = $lname;
				$obj->email = $email;
				$msg['status']=true;
				$msg['data']=$obj;
				$msg['msg']='Your child has been registered';	
			}else{
				$msg['msg']='Your have reach limit to creation of child';	
				$msg['limit'] = 2;	
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