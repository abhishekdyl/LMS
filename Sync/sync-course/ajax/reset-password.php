<?php
// require_once('../../../../wp-config.php');
// if(is_user_logged_in()){
// 	$user=wp_get_current_user();//reset_password( WP_User $user, string $new_pass )
// 	$password=$_POST['password'];
// 	$crmpassword=$_POST['crm_password'];
// 	if($crmpassword==$password){
// 		reset_password($user,$password);
// 		update_user_meta($user->ID,'user_force_login',0);
// 		//reset_password($user,$password);
// 		$msg['status']=true;
// 		$msg['msg']='Password updated successfully';
// 	}else{
// 		$msg['status']=false;
// 		$msg['msg']='Password and confirm password must be same';
// 	}

// }else{
// 	$msg['status']=false;
// 	$msg['msg']='You are not logged in user';
// }
// echo 'ooooooooooooo';
//echo json_encode($msg);