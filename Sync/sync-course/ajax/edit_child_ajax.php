<?php
require_once('../../../../wp-config.php');
if ($_POST['action'] == 'edit_childuser') {
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
}
if ($_POST['action'] == 'delete_childuser') {
    global $wpdb;
    $userid = $_POST['userid'];
    $user_info 	= get_userdata($userid);
    $username 	= $user_info->user_login;
    $useremail 	= $user_info->user_email;
    $deleteuser = wp_delete_user( $userid );
    if($deleteuser){
    	$request_data=array('user_email'=>$useremail,'username'=>$username,'wp_userid'=>$userid);
		// get moodle url
		$setting_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
		$url=$setting_data->url;
		 // echo json_encode((object)$request_data);
		 // die;
		  $curl =curl_init();
		  curl_setopt_array($curl, array(
		    CURLOPT_URL => $url.'/local/coursesync/delete_users.php',
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
		$response = json_decode($response);
		echo $status = $response->status;
    }else{
    	echo $status = 0;
    }
}
if ($_POST['action'] == 'delete_parent') {
    global $wpdb;
    $userid = $_POST['userid'];
    $getchild  =  "SELECT * FROM {$wpdb->prefix}usermeta WHERE meta_key LIKE 'parent_id' AND meta_value='".$userid."'";
    $childlist = $wpdb->get_results($getchild);
    if($childlist){
    	echo $status = 4;
    }else{
   		$deleteuser = wp_delete_user($userid);
   		if($deleteuser){
	    	$request_data=array('user_email'=>$useremail,'username'=>$username,'wp_userid'=>$userid);
			// get moodle url
			$setting_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
			$url=$setting_data->url;
			  $curl =curl_init();
			  curl_setopt_array($curl, array(
			    CURLOPT_URL => $url.'/local/coursesync/delete_users.php',
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
			$response = json_decode($response);
			echo $status = $response->status;
	    }else{
	    	echo $status = 0;
	    }

    }
    
  
}

?>