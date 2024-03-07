<?php
include('../../../../wp-config.php');
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $invoice_no=$_POST['invoice_no'];
    $invoice_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}invoice WHERE id=$invoice_no");
    $allcourse = array();
    $course_data=$wpdb->get_results("SELECT * FROM {$wpdb->prefix}invoice_details WHERE invoice_id=$invoice_no");
    foreach ($course_data as $coursedata) {
        array_push($allcourse, $coursedata->courseid);
    }
    $allcourse = implode(",", $allcourse);

    if($invoice_data){
        //print_r($invoice_data);
        if(!empty($invoice_data->userid)){
        	$userdata=get_user_data($invoice_data->userid);
        	$courseid=get_moodle_course_id($allcourse);
        	if(!empty($userdata) AND !empty($courseid)){
        		$api_response=json_decode(course_enroll_api($userdata,$courseid));
        		if($api_response->status){
        			$wpdb->update("{$wpdb->prefix}invoice",array("status"=>3,'updateddate'=>time(),'approved_by'=>get_current_user_id()),array('id'=>$invoice_data->id));
        		}
        		echo json_encode($api_response);
        		exit();
        	}else{
        		$msg['status']=false;
        		$msg['msg']='User data not found';
        		echo json_encode($msg);
        		exit();
        	}
        }else{
        	$userdata=create_user_data($invoice_data->multistep_id);
        	$user_meta=get_user_meta($userdata->ID);
        	$courseid=get_moodle_course_id($allcourse);
            /*print_r($allcourse);
            die;*/
        	if(!empty($userdata) AND !empty($courseid)){
        		$api_response=json_decode(course_enroll_api($userdata,$courseid));
        		if($api_response->status){
        			$wpdb->update("{$wpdb->prefix}invoice",array("status"=>3,'userid'=>$userdata->ID,'updateddate'=>time(),'approved_by'=>get_current_user_id()),array('id'=>$invoice_data->id));
        		}
        		echo json_encode($api_response);
        		exit();
        	}else{
        		$msg['status']=true;
        		$msg['msg']='User data not found';
        		echo json_encode($msg);
        		exit();
        	}
        }
    }else{
        $msg['status']=false;
        $msg['msg']="Invalid Invoice no";
        echo json_encode($msg);
        exit();
    }
}
function get_user_data($userid){
    global $wpdb;
    $userdata=get_userdata($userid);
    if($userdata){
    	return $userdata;
    }
    return false;
}
function create_user_data($multiform_id){
	global $wpdb;
    $multiform_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}multistepform WHERE id=$multiform_id");
    if($multiform_data){
    	$post_data=unserialize($multiform_data->post_data);
    	$email=$post_data['e_mail'];
    	$password=$post_data['pass_word'];
    	$userdata=get_user_by( 'email', $email );
    	$userFirstName=$post_data['fname'];
    	$userLastName=$post_data['lname'];
    	$gender=$post_data['gender'];
    	$post_data['first_name']=$post_data['fname'];
    	$post_data['last_name']=$post_data['lname'];
    	$post_data['nickname']=$post_data['fname'];
    	if(empty($userdata)){
    		$userid=wp_create_user($email,$password,$email);
    		foreach($post_data as $key =>$value){
    			update_user_meta($userid,$key,$value);
    		}
    		update_user_meta($userid,'user_force_login',1);
    		$userdata=get_userdata($userid);
    		return $userdata;
    	}else{
    		return $userdata;
    	}
    }else{
    	return false;
    }
}
function course_enroll_api($userdata,$courseid){
	global $wpdb;
	$firstname=get_user_meta($userdata->ID,'first_name',true);
	$lastname=get_user_meta($userdata->ID,'last_name',true);
	$request_data=array('user_data'=>$userdata,'courseid'=>$courseid,'firstname'=>$firstname,'lastname'=>$lastname);
	$setting_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
	$url=$setting_data->url;
	$curl =curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url.'/local/coursesync/course-enrolments.php',
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
	return $response;


}
function get_moodle_course_id($wpcourseid){
	global $wpdb;
	$coursedata=$wpdb->get_results("SELECT * FROM {$wpdb->prefix}coursesysc WHERE wp_id in($wpcourseid)");
	if($coursedata){
        $coursesid = array();
        foreach ($coursedata as $coursesdata) {
            array_push($coursesid, $coursesdata->moodle_id);
        }
		return $coursesid;
	}
	return false;
}
