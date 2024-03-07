<?php
require_once('../../config.php');
function course_enrolments($courseid,$emailid,$firstname=null,$lastname=null){
	global $DB;
	if(isset($courseid) && !empty($courseid) && isset($emailid) && !empty($emailid)){
		$coursesql="SELECT * FROM {course} WHERE visible=? AND id=?";
		$coursedata=$DB->get_record_sql($coursesql,array(1,$courseid)); 
		if($coursedata){

			$usersql="SELECT * FROM {user} WHERE email=? AND deleted=?";
			$userdata=$DB->get_record_sql($usersql,array($emailid,0));
			if($userdata){
				$user_enrol_sql="SELECT u.* FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid JOIN {enrol} e ON e.id=ue.enrolid JOIN {course} c ON c.id=e.courseid WHERE c.id=? AND u.id=?";
				$user_enrol_data=$DB->get_record_sql($user_enrol_sql,array($courseid,$userdata->id));
				if($user_enrol_data){
					$msg['status']=false;
					$msg['msg']="User already enroled";
					echo json_encode($msg);
				}else{
					$response=enrol_user($courseid,$userdata->id);
					echo json_encode($response);
				}

			}else{

				$userid=create_user($emailid,$firstname,$lastname);
				if($userid){
					$response=enrol_user($courseid,$userid);
					echo json_encode($response);
				}else{
					$msg['status']=false;
					$msg['msg']="Server error...";
					echo json_encode($msg);
				}
			}
		}else{
			$msg['status']=false;
			$msg['msg']="Invalid Courseid";
			echo json_encode($msg);
		}

	}else{
		$msg['status']=false;
		$msg['msg']="Course id and emailid is required";
		echo json_encode($msg);
	}
}
function create_user($email,$firstname=null,$lastname=null){
	global $DB;
	$userdata=new stdClass();
	$userdata->username=$email;
	$userdata->email=$email;
	$userdata->confirmed=1;
	$userdata->mnethostid=1;
	$userdata->lastname=($lastname)? $lastname:"Lastname";
	$userdata->firstname=($firstname)? $firstname:"Firstname";
	$password=generate_passwords();
	$userdata->password=md5($password);
	if($userid=$DB->insert_record("user",$userdata)){
		return $userid;
	}
	return false;
}

function generate_passwords(){
	global $DB;
	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
function enrol_user($courseid,$userid){
	global $DB;
	$enrollmentID = $DB->get_record_sql('SELECT * FROM {enrol} WHERE enrol = "manual" AND courseid = ?', array($courseid));
    if(!empty($enrollmentID->id)) {
        if (!$DB->record_exists('user_enrolments', array('enrolid'=>$enrollmentID->id, 'userid'=>$userid))) {
            $userenrol = new stdClass();
            $userenrol->status = 0;
            $userenrol->userid = $userid;
            $userenrol->enrolid = $enrollmentID->id; 
            $userenrol->timestart  = time(); 
            $userenrol->timeend = 0; 
            $userenrol->modifierid  = 2; 
            $userenrol->timecreated  = time();
            $userenrol->timemodified  = time(); 
            $enrol_manual = enrol_get_plugin('manual');
            $enrol_manual->enrol_user($enrollmentID, $userid, 5, $userenrol->timestart, $userenrol->timeend);
            //add_to_log($courseid, 'course', 'enrol', '../enrol/users.php?id='.$courseid, $courseid, $userid); //there should be userid somewhere!
            //mailtemplate($userid,$courseid);
            $result = array(
                'status' => true,
                'msg' => 'enrolled successfully',
            );
        } else {
            $result = array(
                'status' => false,
                'msg' => 'Already enrolled',
            );
        }
    } else {
        $result = array(
            'status' => false,
            'msg' => 'manual enrolemnt not available',
        );
    }

    return $result;
}

function sso($userdata){
	global $DB,$CFG;
	// echo "<pre>";
	if(is_jwt_valid($userdata)){
	
	
		$data=parseJwt($userdata);
	
		if($data){
			if(!empty($data->email)){
				$sql="SELECT * FROM {user} WHERE email=?";
				$get_userdata=$DB->get_record_sql($sql,array($data->email));
				if($get_userdata){
					$urltogo= $CFG->wwwroot.'/my/';
					user_login($get_userdata);
					redirect($urltogo);
				}else{
					$userid=create_user($data->email,$data->firstname,$data->lastname);
					if($userid){
						$usersql="SELECT * FROM {user} WHERE id=?";
						$reg_user=$DB->get_record_sql($usersql,array($userid));
						$urltogo= $CFG->wwwroot.'/my/';
						user_login($reg_user);
						redirect($urltogo);
					}else{
						// $msg['status']=false;
						// $msg['msg']="Server Error, Please try again!";
						redirect($CFG->wwwroot, "Server Error, Please try again!", 0); 
						//echo json_encode($msg);
					}
				}
			}else{
				redirect($CFG->wwwroot, "Email is required", 0); 
				// $msg['status']=false;
				// $msg['msg']="Email is required";
				// echo json_encode($msg);
			}
		}else{
			redirect($CFG->wwwroot, "User data not found", 0); 
			// $msg['status']=false;
			// $msg['msg']="User data not found";
			// echo json_encode($msg);
		}
	}
	else{

		redirect($CFG->wwwroot, "Token is not  valid", 0); 
		// $msg['status']=false;
		// $msg['msg']="Token is not  valid";
		// echo json_encode($msg);
	}
	
}
function parseJwt ($tokendata) {
	$base64Url = explode(".", $tokendata)[1];
	$base64 = str_replace( "_","/",  str_replace( "-","+",  $base64Url));
	$jsonPayload = urldecode(implode("", array_map('mapb64', str_split(base64_decode($base64)))));
	return json_decode($jsonPayload);
}
function mapb64($c){
	return '%' . substr(('00' . dechex(ord($c))), -2);
}
// function encodeJwt($userdata){
// 	$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

// 	// Create token payload as a JSON string
// 	$payload = json_encode($userdata);

// 	// Encode Header to Base64Url String
// 	$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

// 	// Encode Payload to Base64Url String
// 	$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

// 	// Create Signature Hash
// 	$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);

// 	// Encode Signature to Base64Url String
// 	$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

// 	// Create JWT
// 	$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

// 	return $jwt;
// }

function is_jwt_valid($jwt, $secret = 'xsd3pHCoUenDtplKVEPrCBqmJ9i844s1') {
	// split the jwt
	$tokenParts = explode('.', $jwt);
	$header = base64_decode($tokenParts[0]);
	$payload = base64_decode($tokenParts[1]);
	$signature_provided = $tokenParts[2];

	// check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
	// $expiration = json_decode($payload)->exp;
	// $is_token_expired = ($expiration - time()) < 0;

	// build a signature based on the header and payload using the secret
	$base64_url_header = base64url_encode($header);
	$base64_url_payload = base64url_encode($payload);
	$signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
	$base64_url_signature = base64url_encode($signature);

	// verify it matches the signature provided in the jwt
	$is_signature_valid = ($base64_url_signature === $signature_provided);
	
	if (!$is_signature_valid) {
		return false;
	} else {
		return true;
	}
}
function base64url_encode($str) {
    return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
}
function generate_jwt($payload, $secret = 'xsd3pHCoUenDtplKVEPrCBqmJ9i844s1') {
	$headers=array('alg'=>'HS256','typ'=>'JWT');
	$headers_encoded = base64url_encode(json_encode($headers));
	
	$payload_encoded = base64url_encode(json_encode($payload));
	
	$signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
	$signature_encoded = base64url_encode($signature);
	
	$jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
	
	return $jwt;
}
function user_login($userdata){
	complete_user_login($userdata);
    \core\session\manager::apply_concurrent_login_limit($userdata->id, session_id());
    return true;
}
