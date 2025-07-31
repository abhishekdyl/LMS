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

   // $userdata = wp_insert_user($default_newuser);
    //if ( $userdata && !is_wp_error( $userdata ) ) {
    	$curl = curl_init();
    	$data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
    	$courseid='';
    	// echo "<pre>";
    	// print_r($data);
    	// die;
    	$url='';
    	if($data){
    		$courseid=$data->courseid;
    		$url=$data->url;
    	}

    	$moodle_user=(object)array('username'=>$username,'email'=>$email,'firstname'=>$fname,'lastname'=>$lname,'password'=>md5($password),'courseid'=>$courseid);
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url.'/local/coursesync/wp-user-register.php',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>json_encode($moodle_user),
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);


		curl_close($curl);
		//echo "<pre>";
		//echo "oooooooooooo";
		//print_r(curl_error($curl));
		//print_r($response);
		$responsedata=json_decode($response);
		if($responsedata->status){
			$userdata = wp_insert_user($default_newuser);
			$msg['status']=true;
			$msg['data']=$error_arr;
			$msg['msg']='Thank you for registration ';
			//echo json_encode($msg);
		}else{
			array_push($error_arr, $responsedata->data);
			$msg['status']=false;
			$msg['data']=$error_arr;
			$msg['msg']='';
			//echo json_encode($msg);
		}
		//echo "<pre>";
	
		
		//echo $response;
        //$code = sha1( $user_id . time() );
      //  $activation_link = add_query_arg( array( 'key' => $code, 'user' => $user_id ), get_permalink( /* YOUR ACTIVATION PAGE ID HERE */ ));
        //add_user_meta( $user_id, 'has_to_be_activated', $code, true );
       // wp_mail( $data['user_email'], 'ACTIVATION SUBJECT', 'CONGRATS BLA BLA BLA. HERE IS YOUR ACTIVATION LINK: ' . $activation_link );
    //}
	
	echo json_encode($msg);
	exit();
}