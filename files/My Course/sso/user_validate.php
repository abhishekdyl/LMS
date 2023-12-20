<?php
class UserValidate{

	function  __construct(){

	}
	private static function getToken(){
		$curl = curl_init();
		$username=self::getUsername();
		$password=self::getPassword();

		$auth_api_url=self::getAuthApiUrl();
		$userdata=array('username'=>$username,'password'=>$password);
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $auth_api_url.'/api/login',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>json_encode($userdata),
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return json_decode($response);
	}
	public static function validateOtp($otp){
		global $CFG;
		$tokendata=self::getToken();
		$auth_api_url=self::getAuthApiUrl();
		if(!$tokendata->success){

			return false;
		}
		 $token=$tokendata->token;
		$curl = curl_init();
		$otpdata=json_encode(array('otp'=>$otp));
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $auth_api_url.'/api/micro-service/moodle/validate-otp',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>$otpdata,
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'Authorization: Bearer '.$token
		  ),
		));

		$response = curl_exec($curl);


		curl_close($curl);
		$responsedata=json_decode($response);
		// echo "<pre>";
		// print_r($responsedata);
		// die;
		if($responsedata->success){
			$login_response=self::check_userlogin($responsedata);
			if($login_response){
				// redirect("$CFG->wwwroot/my",'Your are loggedin in moodle platform',null,\core\output\notification::NOTIFY_SUCCESS);
				redirect("$CFG->wwwroot/my",'You are logged in to your account',null,\core\output\notification::NOTIFY_SUCCESS);
			}else{
				redirect("$CFG->wwwroot/",'Something Wrong',null,\core\output\notification::NOTIFY_ERROR);
			}
		}else{
			redirect("$CFG->wwwroot/",$responsedata->msg,null,\core\output\notification::NOTIFY_ERROR);
		}
		
	}
	private static function getUsername(){
		return get_config('local_sso', 'username');
	}
	private static function getPassword(){
		return get_config('local_sso', 'userpassword');
	}
	private static function getAuthApiUrl(){
		return get_config('local_sso', 'auth_api_root');
	}
	private static function getPaymentApiUrl(){
		return get_config('local_sso', 'payment_api_root');
	}
	public static function getPaymentHomeUrl(){
		return get_config('local_sso', 'payment_home_url');	
	}
	private static function check_userlogin($userdata){
		global $DB,$USER;
		$sql="SELECT * FROM {user} WHERE (email=:email OR username=:username) AND deleted != 1";
		if($DB->record_exists_sql($sql,array('email'=>$userdata->user_email,'username'=>$userdata->username))){
           
            if($userdata = $DB->get_record_sql($sql,array('email'=>$userdata->user_email,'username'=>$userdata->username))){
                complete_user_login($userdata);
                \core\session\manager::apply_concurrent_login_limit($userdata->id, session_id());
                return true;
            }else{
            	return false;
            }
     		

		}else{
			return false;
		}
		return false;
	}
	private static function course_access_api($userdata){
		global $DB,$USER,$CFG;
		$tokendata=self::getToken();
		$payment_api_url=self::getPaymentApiUrl();
		if(!$tokendata->success){

			return false;
		}
		
		$token=$tokendata->token;
		$curl = curl_init();
		$request_data=json_encode(array('email'=>$userdata->email,'group_id'=>$userdata->groupid));
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $payment_api_url.'/api/micro-service/check-course-acces-on-group',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $request_data,
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'Authorization: Bearer '.$token
		  ),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		$response=json_decode($response);
		return $response;
		
	}
	public static function validate_user($courseid){
		global $DB,$USER;
		//$courseid=0;
		$sql="SELECT u.*,ug.id as groupid FROM {user} u 
		JOIN {utrains_groups_user} ugu  ON u.id=ugu.userid 
		JOIN {utrains_groups} ug ON ugu.groupid=ug.id 
		JOIN {utrains_groups_course} ugc ON ugc.groupid=ug.id AND ugu.groupid=ugc.groupid
		WHERE ugu.userid=:userid
		AND ugu.deleted=:deleted1 
		AND ug.deleted=:deleted2 
		AND ug.status=:status
		AND ug.deleted=:deleted3
		AND ugc.courseid=:courseid
		"; 
		$userdata=$DB->get_record_sql($sql,array('userid'=>$USER->id,'deleted1'=>0,'deleted2'=>0,'deleted3'=>0,'courseid'=>$courseid,'status'=>1));
		if($userdata){
			$check_sql="SELECT * FROM {utrains_course_access_api} WHERE userid=:userid AND groupid=:groupid";
			$check_data=$DB->get_record_sql($check_sql,array('groupid'=>$userdata->groupid,'userid'=>$userdata->id));
			if($check_data){
				if($check_data->expired_at >= time() && $check_data->is_in_good_standing==true && $check_data->status==true ){

					return true;
				}else{
					$response=self::course_access_api($userdata);
					if(empty($response->success)){
						redirect("/",$response->message,null,\core\output\notification::NOTIFY_ERROR);
						exit();
					}
					$check_data->expired_at=$response->expiredAt;
					$check_data->is_in_good_standing=$response->isInGoodStanding;
					$check_data->status=$response->success;
					$check_data->updateddate=time();
					$DB->update_record("utrains_course_access_api",$check_data);
					if($response->success==true && $response->isInGoodStanding==true && $response->expiredAt >= time() ){
						return true;
					}else{
						return false;
					}
				}
			}else{
				$response=self::course_access_api($userdata);
				echo json_encode($response);
				// echo "<pre>";
				// print_r($userdata);
				// die;

				if(empty($response->success)){
					redirect("/",$response->message,null,\core\output\notification::NOTIFY_ERROR);
					exit();
				}
				$std=new stdClass();
				$std->userid=$USER->id;
				$std->groupid=$userdata->groupid;
				$std->expired_at=$response->expiredAt;
				$std->is_in_good_standing=$response->isInGoodStanding;
				$std->status=$response->success;
				$std->createddate=time();
				$DB->insert_record("utrains_course_access_api",$std);
				if($response->success==true && $response->isInGoodStanding==true && $response->expiredAt >= time() ){
					return true;
				}else{
					return false;
				}
			}
		}
		return true;

	}
	public static function getSubscriptions(){
		global $CFG, $USER;
		$tokendata=self::getToken();
		$auth_api_url=self::getPaymentApiUrl();
		if(!$tokendata->success){

			return array();
		}
		 $token=$tokendata->token;
		$curl = curl_init();
		$otpdata=json_encode(array('email'=>$USER->email));
		// $otpdata=json_encode(array('email'=>'tech.user+lds2@utrains.org'));
		// $otpdata=json_encode(array('email'=>'melissa.dassi+5@utrains.org'));
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $auth_api_url.'/api/micro-service/subscriptions-details',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>$otpdata,
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'Authorization: Bearer '.$token
		  ),
		));

		$response = curl_exec($curl);


		curl_close($curl);
		$responsedata=json_decode($response);
		return is_array($responsedata)?$responsedata:array();
	}
}

?>