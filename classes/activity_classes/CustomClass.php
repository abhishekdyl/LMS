<?php

class CustomClass {

	public $username;
	public $password;
	public $array;
	public $token;


	// Generate a secret for the user and store it in the database
	public static function generate_user_secret($userid) {
	    $secret = bin2hex(random_bytes(7)); // Generate a random 32-character hex string
	    // Store $secret in the database or another secure storage mechanism
	    self::update_user_secret_in_database($userid, $secret);
	    return $secret;
	}


	// Store the secret in the database
	public static function update_user_secret_in_database($userid, $secret) {
	    global $DB;
	    $user = new stdClass();
	    $user->id = $userid;
	    $user->secret = $secret;
	    $DB->update_record('user', $user);
	}


	// Generate new user token if not exist...
	public function create_user_token($username, $password) {
		//$validate_token = $this->validate_token();
		global $CFG, $DB;


		if(self::taxila_validate($username, $password) == 'Success.') {
			

			$return_manual = array();
			$fetch_user = "SELECT id, email, username FROM {user} WHERE (email = ? OR username = ?)";
			$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
			$usernameget = $get_user->username;
			$user_email = $get_user->email;
			$userid = $get_user->id;

			if($get_user = $DB->get_record_sql($fetch_user, array($username, $username))) {

				$DelSubLim = $get_user->id;
				$DB->delete_records('external_tokens', array('userid' => $DelSubLim));
				$password = $password."BitSized@123";

				$curl = curl_init();
				curl_setopt_array($curl, [
				CURLOPT_URL => $CFG->wwwroot."/login/token.php?username=".$username."&password=".$password."&service=moodle_mobile_app",
				CURLOPT_RETURNTRANSFER => true,
		  		CURLOPT_SSL_VERIFYPEER=> false,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",]);

				$responses = curl_exec($curl);
				// print_r($responses);
				$responses_decode = json_decode($responses);
				$token = $responses_decode->token;
				curl_close($curl);

				// die;


			} else { 

				$firstname = explode("@", $username);
				$lastname = "lastname".rand();
				$password = $password."BitSized@123";
				$array  = array('username' => $username, 'password' => $password, 'firstname' => $firstname[0], 'lastname' => $lastname, 'email'=> $username);
				$user_registration = $this->user_registration($array);


				$curl = curl_init();
				curl_setopt_array($curl, [
				CURLOPT_URL => $CFG->wwwroot."/login/token.php?username=".$username."&password=".$password."&service=moodle_mobile_app",
				CURLOPT_RETURNTRANSFER => true,
		  		CURLOPT_SSL_VERIFYPEER=> false,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",]);

				$responses = curl_exec($curl);
				$responses_decode = json_decode($responses);
				$token = $responses_decode->token;
				curl_close($curl);


				$fetch_user = "SELECT id, email, username FROM {user} WHERE (email = ? OR username = ?)";
				$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
				$usernameget = $get_user->username;
				$user_email = $get_user->email;
				$userid = $get_user->id;
	
			}




			$taxilaClass = self::taxila_course($user_email);
			$taxilaClass =  json_decode($taxilaClass, true);

			$shortname_taxila = array_column($taxilaClass["data-moodle"], 'shortname');
			$shortname_moodle = array_column(get_courses(), 'shortname');
			$matching_courses = array_intersect($shortname_taxila, $shortname_moodle); 
			$matching_courses_count = count($matching_courses); 

			if( $matching_courses_count == 0) {
				$return_manual['associated']=$matching_courses_count;
				return json_encode($return_manual);
				die;
			}



			// Generate user secret and update in the database..
			self::generate_user_secret($userid);

		

			if(!empty($token)) {

				foreach ($matching_courses as $shortname) { 
					$course = $DB->get_record('course', array('shortname' => $shortname));
					self::enrolCourse($course->id, $userid);
				}

				$curl_fetch = curl_init();
				curl_setopt_array($curl_fetch, [
				CURLOPT_URL => $CFG->wwwroot."/webservice/rest/server.php?wstoken=$token&moodlewsrestformat=json&wsfunction=core_user_get_users_by_field&field=username&values%5B0%5D=$usernameget",
				CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_SSL_VERIFYPEER=> false,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				
				]);

				$response_final = json_decode(curl_exec($curl_fetch));
				// var_dump($response_final);

				$id = $response_final[0]->id;
				// die;

				$fetch_user = "SELECT firstname, lastname  FROM {user} WHERE (id = ?)";
		        $get_user = $DB->get_record_sql($fetch_user, array($id));

				$return_manual['token'] = $token;
				$return_manual['username'] = $response_final[0]->username;
				$return_manual['fullname'] = $response_final[0]->fullname;
				$return_manual['firstname'] = $get_user->firstname;
				$return_manual['lastname'] = $get_user->lastname;
				$return_manual['email'] = $response_final[0]->email;
				$return_manual['profileimageurl'] = $response_final[0]->profileimageurl;

				curl_close($curl_fetch);
				return json_encode($return_manual);

			} else { return json_encode(array()); }

		} else { return null; }


	}





	// Update user
	public function edit_profile($array) {

		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: POST");

		//$validate_token = $this->validate_token();

		if($_SERVER['REQUEST_METHOD'] === 'POST') {
 
			global $CFG, $DB;
			$username = $array['username'];
			$fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  WHERE (u.email = ? OR u.username = ?)";
			$get_user = $DB->get_record_sql($fetch_user, array($username, $username));

			$token = $get_user->token;

			// var_dump($get_user);
			// die;
			$userid = $get_user-> id;
			$obj_upd = new stdClass();
			$obj_upd ->id 				=   (int)$userid;
			$obj_upd ->firstname 	    =   $array['firstname'];
			$obj_upd ->lastname 		=   $array['lastname'];
			$obj_upd ->email 			= 	$array['email'];
			$obj_upd ->city 			= 	$array['city'];
			$obj_upd ->profile_img 		= 	$array['profile_img'];


			$profile_img = self::base64ToImage($array['profile_img']);
			$url = $CFG->wwwroot.'/local/userdetails/'.$profile_img;

			// die;
			self::update_user_profile_image($userid, $url);
			

			require_once ('../../lang/en/countries.php');	
			$country_list = get_string_manager()->get_list_of_countries();

			$obj_upd ->country 		= 	array_search(ucwords($array['country']),$country_list);
			$obj_upd ->description  =  $array['description'];

			$return_final = $DB->update_record('user', $obj_upd);
			return $return_final;
			

		}else{
		return array();
		}
	
	}





	// No login token 
	public function get_no_login_token() {
		global $CFG, $DB;

		$fetch_token = "SELECT token FROM {external_tokens}  WHERE externalserviceid IN (SELECT id FROM {external_services} WHERE id=2) ORDER BY rand()";
		$get_token = $DB->get_record_sql($fetch_token);

		$token = $get_token ->token;
		return $token;
	}





	// User registration main function
	public function user_registration($array) {
		
		global $CFG, $DB;
		$token = $this->get_no_login_token();


		require_once ('MoodleRest.php');
		$MoodleRest = new MoodleRest();
		$MoodleRest->setServerAddress($CFG->wwwroot."/webservice/rest/server.php");
		$MoodleRest->setToken($token);
		$new_user = array('users' => array($array));


		$tempuser = new stdClass();
		$tempuser->id = null;
		$tempuser->username = $array['username'];
		$tempuser->firstname = $array['firstname'];
		$tempuser->lastname = $array['lastname'];
		$tempuser->email = $array['email'];
	
		$insert_return = '';
		if (!check_password_policy($array['password'], $insert_return, $tempuser)) {
			$insert_return['password'] = $insert_return;
		}else{}
			$insert_return = $MoodleRest->request('core_user_create_users', $new_user, MoodleRest::METHOD_POST);

		return json_encode($insert_return);
	}




	// User reset password function
	public function reset_password($username) {
		//$validate_token = $this->validate_token();
		global $CFG, $DB;
		$fetch_user = "SELECT email, username FROM {user} WHERE (email = ? OR username = ?)";
		$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
		$usernameget = $get_user-> username;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot.'/lib/ajax/service.php');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/x-www-form-urlencoded',]);

		$params = '[{"index":0,"methodname":"core_auth_request_password_reset","args":{"username": "'.$usernameget.'", "email": ""}}]';
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$response = curl_exec($ch);

		curl_close($ch);
		return $response;
	}




	// Self enroll course
	public function self_enroll_course($username, $courseid) {
		//$validate_token = $this->validate_token();
		global $CFG, $DB;
		$fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  WHERE (u.email = ? OR u.username = ?)";
		$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
		$token = $get_user->token;


		$fetch_password =	"SELECT * FROM {enrol} WHERE enrol = 'self' AND courseid  = $courseid AND password !='' ";
		$get_password = $DB->get_record_sql($fetch_password);            
		$password = $get_password->password;

		if($get_password = $DB->get_record_sql($fetch_password)){

		$curl = curl_init();
		curl_setopt_array($curl, [
		CURLOPT_URL => $CFG->wwwroot."/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=enrol_self_enrol_user&wstoken=$token&courseid=$courseid&password=$password",
		CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_SSL_VERIFYPEER=> false,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",]);

		$response = curl_exec($curl);
		

		curl_close($curl);
		return json_encode($response);

		}else{
		return  array();
		}
	}




	// Self course list
	public function self_course_list() {

		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: GET");

		//$validate_token = $this->validate_token();

		if($_SERVER['REQUEST_METHOD'] === 'GET'){

			global $CFG, $DB;
			$fetch_all_course_list = "SELECT c.id, c.fullname, c.summary, f.filename 
			FROM {course} AS c
			INNER JOIN {enrol} AS e ON e.courseid = c.id AND e.enrol = 'self' AND e.password !=''
			LEFT JOIN {context} AS ct ON ct.instanceid = c.id
			LEFT JOIN {files} AS f ON f.contextid = ct.id
			WHERE c.id>1 AND c.visible=1";

			$array_custom = array();
			if($get_all_course_list = $DB->get_records_sql($fetch_all_course_list)){ 
			$array_keys = array_keys($get_all_course_list);

			for($key=0; $key<count($array_keys); $key++)
			{ 
				
			$courseid = $get_all_course_list[$array_keys[$key]]-> id;
			$url = $this->getcourse_image($courseid);
			$fullname = $get_all_course_list[$array_keys[$key]]-> fullname;
			$summary  = $get_all_course_list[$array_keys[$key]]-> summary;
			$filename = $get_all_course_list[$array_keys[$key]]-> filename;

			array_push($array_custom, 
				array(
				"image_url"=> $url, 
				"courseid" => $courseid, 
				"fullname" => $fullname, 
				"summary"  => strip_tags($summary), 
				"filename" => $filename
				)
				);
			}
			
			return json_encode($array_custom);
			}else{
			return array();
			}

		}else{
		return array();
		}

	}




	// User courselist 
	public function courselist($username) {

		global $CFG, $DB;

		$fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  WHERE (u.email = ? OR u.username = ?)";
		$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
		$user_email = $get_user->email;
		$userid = $get_user->id;
		$token = $get_user->token;


		// $taxilaClass = self::taxila_course($user_email);
		// $taxilaClass =  json_decode($taxilaClass, true);

		// $shortname_taxila = array_column($taxilaClass["data-moodle"], 'shortname');
		// $shortname_moodle = array_column(get_courses(), 'shortname');
		// $matching_courses = array_intersect($shortname_taxila, $shortname_moodle); 

		// $array_custom = array();
		// foreach ($matching_courses as $shortname) { 

	    // $course = $DB->get_record('course', array('shortname' => $shortname));

	    // 	$url = $this->getcourse_image($course->id);
	    // 	$id = $course->id;
		// 	$fullname = $course->fullname;
		// 	$summary  = $course->summary;
		// 	$filename = $course->filename;
		


		// 	$course  = ['overviewfiles' => ['fileurl' => $url]];
		// 	$course->id = (int)$id;
		// 	$course->overviewfiles  = array(array('filename' => '', "filepath" => '', "filesize" => '', 'fileurl' => $url, "timemodified" => '', "mimetype" => "" ));
		// 	array_push($array_custom, $course);

		// } 


		require_once ('MoodleRest.php');
		$MoodleRest = new MoodleRest();
		$MoodleRest->setServerAddress($CFG->wwwroot."/webservice/rest/server.php");
		$MoodleRest->setToken($token);
		$MoodleRest->setReturnFormat(MoodleRest::RETURN_ARRAY);
		$params = array('userid' => $userid); 
		$result_final = $MoodleRest->request('core_enrol_get_users_courses', $params, MoodleRest::METHOD_POST);

		// $get_course_rating = $DB->get_record("tool_courserating_summary", array("courseid"=> $course->id));
		// $isEnrolled = true; 
		// $msg = '';


		if($result_final) { 

			$array_keys = array_keys($result_final);
			for($key=0; $key<count($array_keys); $key++) { 

			$courseid = $result_final[$array_keys[$key]]['id'];
			$summary = $result_final[$array_keys[$key]]['summary'];

			$url = $this->getcourse_image($courseid);
			$result_final[$array_keys[$key]]['overviewfiles'][0]['fileurl'] = $url;
			$result_final[$array_keys[$key]]['summary'] = strip_tags($summary);



			}
		}


		// return json_encode($array_custom);
		return json_encode($result_final);
	

	}



	// Course color
	public function coursecolor($courseid) {
		$basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894', '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];
		$color = $basecolors[$courseid % 10];
		return $color;
    }




	// Get course image
	public function getcourse_image($courseid) {
		global $DB, $CFG;
		require_once($CFG->dirroot. '/course/classes/list_element.php');
		$course = $DB->get_record('course', array('id' => $courseid));
		$course = new core_course_list_element($course);
		foreach ($course->get_course_overviewfiles() as $file) {
		$isimage = $file->is_valid_image();
		$imageurl = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'. $file->get_contextid(). '/'. $file->get_component(). '/'. $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
		return $imageurl;
		}
		if(empty($imageurl)){
		$color = $this->coursecolor($course->id);
		$pattern = new \core_geopattern();
		$pattern->setColor($color);
		$pattern->patternbyid($courseid);
		$classes = 'coursepattern';
		$imageurl = $pattern->datauri();

		
		$svg_collection = array('Untitled_blue.jpg', 'Untitled_gray.jpg', 'Untitled_purple.jpg', 'Untitled_skyblue.jpg');
		$image = array_rand($svg_collection, 1);
		$imageurl  = $CFG->wwwroot."/local/userdetails/src/".$svg_collection[$image];

		}
		return $imageurl;
    }




	// All courses 
	public function all_course($username) {

		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: POST");

		// $validate_token = $this->validate_token();

		if($_SERVER['REQUEST_METHOD'] === 'POST'){

			global $CFG, $DB, $USER;



			$fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  WHERE (u.email = ? OR u.username = ?)";
			$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
			$user_email = $get_user->email;
			$userid = $get_user->id;
	      

			$taxilaClass = self::taxila_course($user_email);
			$taxilaClass =  json_decode($taxilaClass, true);

			$shortname_taxila = array_column($taxilaClass["data-moodle"], 'shortname');
	        $shortname_moodle = array_column(get_courses(), 'shortname');
	        $matching_courses = array_intersect($shortname_taxila, $shortname_moodle); 
	        


			$time = time();
			// $fetch_all_course_list = "SELECT c.id, c.fullname, c.shortname, c.summary, f.filename 
			// FROM {course} AS c
			// LEFT JOIN {context} AS ct ON ct.instanceid = c.id
			// LEFT JOIN {files} AS f ON f.contextid = ct.id 
			// WHERE c.id>1 AND c.visible=1";

			$array_custom = array();

			// if($get_all_course_list = $DB->get_records_sql($fetch_all_course_list)){ 
			// $array_keys = array_keys($get_all_course_list);

			// $isEnrolled = false;
			// $courselist = $this->courselist($username);
			// $array_column = array_column(json_decode($courselist), "id");
			// $msg = "This course is currently unavailable to students";

			
			// $matching_courses = [];
	        
	        foreach ($matching_courses as $shortname) { 

	        	$course = $DB->get_record('course', array('shortname' => $shortname));
	        	
	        	$url = $this->getcourse_image($course->id);
				$fullname = $course->fullname;
				$summary  = $course->summary;
				$filename = $course->filename;

				$get_course_rating = $DB->get_record("tool_courserating_summary", array("courseid"=> $course->id));
				// $array_search = array_search($course->id, $array_column);
				$isEnrolled = true; 
				$msg = '';

				array_push($array_custom, array(
				 "image_url"=> $url,
				 "courseid" => $course->id, 
				 "fullname" => $fullname, 
				  "summary" => strip_tags($summary),
				 "filename" => $filename,
				 "rating" => $get_course_rating->avgrating,
				 "total_rating_count" => $get_course_rating->cntall,
				 "isEnrolled" => $isEnrolled,
				 "msg" => $msg
				));



	        }


			// var_dump($array_custom);
			// die;

			// for($key=0; $key<count($array_keys); $key++){ 
			
			
			// 	$courseid = $get_all_course_list[$array_keys[$key]]-> id;
			// 	$get_course_rating = $DB->get_record("tool_courserating_summary", array("courseid"=> $courseid));
			// 	$array_search = array_search($courseid, $array_column);
			// 	if(count($array_search)>0){ $isEnrolled = true; $msg = ''; }

			// 	// echo "<pre>";
			// 	// var_dump($array_search);
			// 	// die;
				
			// 	$url = $this->getcourse_image($courseid);
			// 	$fullname = $get_all_course_list[$array_keys[$key]]->fullname;
			// 	$summary  = $get_all_course_list[$array_keys[$key]]->summary;
			// 	$filename = $get_all_course_list[$array_keys[$key]]->filename;

			// 	$arrh = array_search($get_all_course_list[$array_keys[$key]]->shortname, $matching_courses);
				
			// 	if($arrh === false){

			// 		array_push($array_custom, array(
			// 		 "image_url"=> $url,
			// 		 "courseid" => $courseid, 
			// 		 "fullname" => $fullname, 
			// 		  "summary" => strip_tags($summary),
			// 		 "filename" => $filename,
			// 		 "rating" => $get_course_rating->avgrating,
			// 		 "total_rating_count" => $get_course_rating->cntall,
			// 		 "isEnrolled" => $isEnrolled,
			// 		 "msg" => $msg
			// 		));

			// 	}

			// }
			
			return json_encode($array_custom);
			// }
			// else{
			// return array();
			// }

		}else{
		return array();
		}
						
	}



	// All courses 
	public function course_category() {
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: GET");
		//$validate_token = $this->validate_token();
		if($_SERVER['REQUEST_METHOD'] === 'GET'){

			global $CFG, $DB;
			$fetch_category = "SELECT DISTINCT(ct.id), ct.name, ct.parent FROM {course_categories} AS ct
			INNER JOIN {course} AS c ON c.category = ct.id
			WHERE c.visible=1 AND ct.visible=1 AND c.id>1";

			if($get_category = $DB->get_records_sql($fetch_category)){ 
			return array_values($get_category);
			}else{
			return array();
			}

		}else{
		return array();
		}
					
	}


	// Category wise courses list
	public function categorywise_courselist($category_id, $username) {
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: POST");

		//$validate_token = $this->validate_token();


		if($_SERVER['REQUEST_METHOD'] === 'POST'){
		
			global $CFG, $DB;

			$fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  WHERE (u.email = ? OR u.username = ?)";
			$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
			$user_email = $get_user->email;
		    $userid = $get_user->id;
		    $token = $get_user->token;


			$taxilaClass = self::taxila_course($user_email);
			$taxilaClass =  json_decode($taxilaClass, true);

			$shortname_taxila = array_column($taxilaClass["data-moodle"], 'shortname');
	        $shortname_moodle = array_column(get_courses(), 'shortname');
	        $matching_courses = array_intersect($shortname_taxila, $shortname_moodle);

			$array_custom = array();
			foreach ($matching_courses as $shortname) { 

	        	$course = $DB->get_record('course', array('shortname' => $shortname));
	        	
	        	$url = $this->getcourse_image($course->id);
				$fullname = $course->fullname;
				$summary  = $course->summary;
				$filename = $course->filename;
				$category = $course->category;

				$get_course_rating = $DB->get_record("tool_courserating_summary", array("courseid"=> $course->id));
				$array_search = array_search($course->id, $array_column);
				$isEnrolled = true; 
				$msg = '';

				if($category == $category_id) {
					array_push($array_custom, array(
					"image_url" => $url, 
					"courseid" => $course->id, 
					"category_id" => $category_id, 
					"fullname" => $fullname, 
					"summary" => strip_tags($summary), 
					"filename" => $filename,
					"rating" => $get_course_rating->avgrating,
					"total_rating_count" => $get_course_rating->cntall,
					"isEnrolled" => $isEnrolled,
					"msg" => $msg));
					}
			 }


			return json_encode($array_custom);


			// $fetch_all_course_list = "SELECT c.id, c.fullname, c.summary, f.filename 
			// FROM {course} AS c
			// INNER JOIN {course_categories} AS cc ON cc.id = c.category 
			// LEFT JOIN {context} AS ct ON ct.instanceid = c.id
			// LEFT JOIN {files} AS f ON f.contextid = ct.id 
			// WHERE c.id>1 AND c.visible=1 AND c.category =".$category_id;


			// $isEnrolled = false;
			// $courselist = $this->courselist($username);
			// $array_column = array_column(json_decode($courselist), "id");
			// $msg = "This course is currently unavailable to students";

			// if ($get_all_course_list = $DB->get_records_sql($fetch_all_course_list)) {
			// 	$array_keys = array_keys($get_all_course_list);

			// 	for ($key = 0; $key < count($array_keys); $key++) {

			// 	$courseid = $get_all_course_list[$array_keys[$key]]->id;
			// 	$get_course_rating = $DB->get_record("tool_courserating_summary", array("courseid"=> $courseid));
			// 	$array_search = array_search($courseid, $array_column);
			// 	if(count($array_search)>0){ $isEnrolled = true; $msg = ''; }


			// 	$url = $this->getcourse_image($courseid);
			// 	$fullname = $get_all_course_list[$array_keys[$key]]->fullname;
			// 	$summary  = $get_all_course_list[$array_keys[$key]]->summary;
			// 	$filename = $get_all_course_list[$array_keys[$key]]->filename;


			// 	array_push($array_custom, array(
			// 		"image_url" => $url, 
			// 		"courseid" => $courseid, 
			// 		"category_id" => $category_id, 
			// 		"fullname" => $fullname, 
			// 		"summary" => strip_tags($summary), 
			// 		"filename" => $filename,
			// 		"rating" => $get_course_rating->avgrating,
			// 		"total_rating_count" => $get_course_rating->cntall,
			// 		"isEnrolled" => $isEnrolled,
			// 		"msg" => $msg));
			// 	}

			// 	return json_encode($array_custom);
			// } else {
			// return array();
			// }

		}else{
		return array();
		}
	}




	// Calendar events 
	public function calendar_events($course_id = null) {

		//$validate_token = $this->validate_token();
		global $CFG, $DB;
		$fetch_calendar = "SELECT * FROM {event} WHERE visible=1";

		if($get_calendar = $DB->get_records_sql($fetch_calendar)){ 

		$array_keys = array_keys($get_calendar);
		$array_custom = array();

		for($key=0; $key<count($array_keys); $key++)
		{ 

		array_push($array_custom, array(

			"event_id"			=> $get_calendar[$array_keys[$key]]-> id,
			"name" 				=> $get_calendar[$array_keys[$key]]-> name, 
			"description" 		=> $get_calendar[$array_keys[$key]]-> description,
			"categoryid" 		=> $get_calendar[$array_keys[$key]]-> categoryid,
			"courseid"			=> $get_calendar[$array_keys[$key]]-> courseid,
			"groupid" 			=> $get_calendar[$array_keys[$key]]-> groupid,
			"modulename" 		=> $get_calendar[$array_keys[$key]]-> modulename, 
			"type" 				=> $get_calendar[$array_keys[$key]]-> type,
			"eventtype" 		=> $get_calendar[$array_keys[$key]]-> eventtype,
			"timestart" 		=> $get_calendar[$array_keys[$key]]-> timestart,
			"timeduration" 		=> $get_calendar[$array_keys[$key]]-> timeduration,
			"timemodified" 		=> $get_calendar[$array_keys[$key]]-> timemodified,
			"subscriptionid" 	=> $get_calendar[$array_keys[$key]]-> subscriptionid 

		));

		}
		return json_encode($array_custom);
		}else{
		return array();
		}
					
	}





	// Notification  
	public function notification($username) {
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: POST");

		//$validate_token = $this->validate_token();

		if($_SERVER['REQUEST_METHOD'] === 'POST'){
		
			global $CFG, $DB, $PAGE;
			$fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  WHERE (u.email = ? OR u.username = ?)";
			$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
			$userid = $get_user-> id;

			$sort = true ? 'DESC' : 'ASC';
			$notifications = \message_popup\api::get_popup_notifications($userid, $sort, false, false);
			$notificationcontexts = [];
			$renderer = $PAGE->get_renderer('core_message');

			$obj = new stdClass();

			// echo "<pre>";
			// print_r($notifications);
			// die;

			if ($notifications) 
			{
				foreach ($notifications as $notification)

		        if($notification->useridto == $userid){
					{


					array_push($notificationcontexts, array(

						"Title"    => $notification->contexturlname,
						"subject"  => $notification->subject,
						"smallmessage" => strip_tags($notification->smallmessage),
						"timecreated" => $notification->timecreated,
						"imageurl" => null,
			
					));

					}
			    }
			}

			// $obj->notifications = $notificationcontexts;
			// $obj->unreadcount = \message_popup\api::count_unread_popup_notifications($userid);

			
			return $notificationcontexts;

		}else{
		return array();
		}
			
	}


	// get_old_conversation  
	public function get_old_conversation($username) {

		//$validate_token = $this->validate_token();

		global $CFG, $DB, $USER;
		$fetch_user = "SELECT u.*, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  WHERE (u.email = ? OR u.username = ?)";
		$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
		$userid = $get_user-> id;
		$token = $get_user-> token;

		$USER = $get_user;
		require_once($CFG->dirroot. '/message/externallib.php');

		$obj_varify  = new core_message_external();
		$return_data = $obj_varify->get_conversations($userid, $limitfrom = 0, $limitnum = 0,  $type = null, $favourites = null,
		$mergeself = false);

		// echo $return_data->conversations
		// echo "<pre>";
		// print_r($return_data->conversations);
		// die;

		$array_push = [];
		for ($i=0; $i<count($return_data->conversations); $i++) {
			  array_push($array_push, ($return_data->conversations[$i]));
		}
		 
		return $array_push;
	}



	// get_conversation_details  
	public function get_conversation_details($username, $convid) {
		//$validate_token = $this->validate_token();

		global $CFG, $DB, $USER;
		$fetch_user = "SELECT u.*, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  WHERE (u.email = ? OR u.username = ?)";
		$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
		$userid = $get_user-> id;
		$token = $get_user-> token;

		$USER = $get_user;
		require_once($CFG->dirroot. '/message/externallib.php');

		$obj_varify  = new core_message_external();
		$return_data = $obj_varify->get_conversation_messages($userid,  $convid,  $limitfrom = 0,  $limitnum = 0,
		$newest = false,  $timefrom = 0);


		return $return_data;

			
	}



	// search_user  
	public function search_user($username, $search) {

		//$validate_token = $this->validate_token();

		global $CFG, $DB, $USER;
		$fetch_user = "SELECT u.*, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  WHERE (u.email = ? OR u.username = ?)";
		$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
		$userid = $get_user-> id;
		$token = $get_user-> token;

		$USER = $get_user;
		require_once($CFG->dirroot. '/message/externallib.php');

		$obj_varify  = new core_message_external();
		$return_data = $obj_varify->message_search_users($userid, $search, $limitfrom = 0, $limitnum = 0);


		return $return_data;

			
	}



	// send_messsage  
	public function send_messsage($username, $convid, $messages = []) {

		//$validate_token = $this->validate_token();

		global $CFG, $DB, $USER;
		$fetch_user = "SELECT u.*, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  WHERE (u.email = ? OR u.username = ?)";
		$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
		$userid = $get_user-> id;
		$token = $get_user-> token;

		$USER = $get_user;
		require_once($CFG->dirroot. '/message/externallib.php');

		$obj_varify  = new core_message_external();
		$return_data = $obj_varify->send_messages_to_conversation($convid, $messages);


		return $return_data;

			
	}





	// get_course_activity
	public function get_course_activity($courseid, $username) {
		
		//$validate_token = $this->validate_token();
		global $CFG, $DB;
	

			$fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  
			WHERE (u.email = ? OR u.username = ?) AND externalserviceid = 1";
			$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
			$userid = $get_user-> id;
			$token = $get_user->token;

			if($get_user = $DB->get_record_sql($fetch_user, array($username, $username))){

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot.'/webservice/rest/server.php?moodlewsrestformat=json');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/x-www-form-urlencoded',
				]);
		
				$param = array("courseid" => $courseid);
				curl_setopt($ch, CURLOPT_POSTFIELDS, 'courseid='.$courseid.'&wsfunction=core_course_get_contents&wstoken='.$token.'&moodlewssettingfilter=true');
				$response = curl_exec($ch);
				curl_close($ch);

				return $response;

				
			}else{

				return array();
			}
		

	}




	// Validate token
	public function validate_token() {
		$headers = getallheaders();

		if (!array_key_exists('Authorization', $headers)) {
			echo json_encode(["error" => "Authorization header is missing"]);
			exit;
		}
		else {
		if (substr($headers['Authorization'], 0, 6) !== 'Bearer') {
			echo json_encode(["Bearer" => "Token keyword is missing"]);
			exit;
		}
		else {
		    $token = trim(substr($headers['Authorization'], 6));
		}
		}

		return $token;
	}


	static function taxila_course($userid) {

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://elearn.bits-pilani.ac.in/api-proxy/v1/get-user-courses/'.$userid,
        CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_SSL_VERIFYPEER=> false,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
        'Auth: 9a77948e-7829-4597-8f43-adfac6960ecf',
        'Content-Type: text/plain'
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }

    static function taxila_validate($username, $password) {



		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://elearn.bits-pilani.ac.in/user/vcredentials/',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_SSL_VERIFYPEER=> false,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('username' => $username, 'password' => $password),
		  
		));

		

// 		if(curl_exec($curl) === false)
// {
//     echo 'Curl error: ' . curl_error($curl);
// }
// else
// {
//     echo 'Operation completed without any errors';
// }
$response = curl_exec($curl);
// print_r($response);

// die;

		curl_close($curl);
		return  $response;


    }




    static function enrolCourse($courseid, $userid) {

    global $DB, $CFG;

    $startime = time();
    $endtime = 0;
    $context = context_course::instance($courseid);

    $role = $DB->get_record("role", array("shortname" => "student"));
    $roleid = $role->id;


    $query = 'SELECT * FROM {enrol} WHERE enrol = "manual" AND courseid = '.$courseid;
    $enrollmentID = $DB->get_record_sql($query);
    if(!empty($enrollmentID->id)) {
        if (!$DB->record_exists('user_enrolments', array('enrolid'=>$enrollmentID->id, 'userid'=>$userid))) {
            $userenrol = new stdClass();
            $userenrol->status = 0;
            $userenrol->userid = $userid;
            $userenrol->enrolid = $enrollmentID->id;
            $userenrol->timestart  = $startime;
            $userenrol->timeend = $endtime;
            $userenrol->modifierid  = 2;
            $userenrol->timecreated  = time();
            $userenrol->timemodified  = time();
            $enrol_manual = enrol_get_plugin('manual');
            $enrol_manual->enrol_user($enrollmentID, $userid, $roleid, $userenrol->timestart, $userenrol->timeend);

        } else {
            $oldenroll = $DB->get_record('user_enrolments', array('enrolid'=>$enrollmentID->id, 'userid'=>$userid));
            $oldenroll->timeend = $endtime;
            if($oldenroll){
                $insertRecords=$DB->update_record('user_enrolments', $oldenroll);
            }
        }

	        return "done";
	    }else{
	    	return "notdone";
	    }

	}



	public static function update_user_profile_image($userid, $url) {
        global $CFG, $DB;

        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->libdir . '/gdlib.php');

        $fs = get_file_storage();

        $context = \context_user::instance($userid, MUST_EXIST);
        $fs->delete_area_files($context->id, 'user', 'newicon');

        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'user',
            'filearea' => 'newicon',
            'itemid' => 0,
            'filepath' => '/'
        );

        $urlparams = array(
            'calctimeout' => false,
            'timeout' => 5,
            'skipcertverify' => true,
            'connecttimeout' => 5
        );

        try {
            $fs->create_file_from_url($filerecord, $url, $urlparams);
        } catch (\file_exception $e) {
            return get_string($e->errorcode, $e->module, $e->a);
        }

        $iconfile = $fs->get_area_files($context->id, 'user', 'newicon', false, 'itemid', false);


        // There should only be one.
        $iconfile = reset($iconfile);

        // Something went wrong while creating temp file - remove the uploaded file.
        if (!$iconfile = $iconfile->copy_content_to_temp()) {
            $fs->delete_area_files($context->id, 'user', 'newicon');
            return 0;
        }

        // Copy file to temporary location and the send it for processing icon.
        $newpicture = (int) process_new_icon($context, 'user', 'icon', 0, $iconfile);
        // Delete temporary file.
        @unlink($iconfile);
        // Remove uploaded file.
        $fs->delete_area_files($context->id, 'user', 'newicon');
        // Set the user's picture.
        $DB->set_field('user', 'picture', $newpicture, array('id' => $userid));
           return 1;
    }



    public static function base64ToImage($imageData) {

		list($type, $imageData) = explode(';', $imageData);
		list(,$extension) = explode('/',$type);
		list(,$imageData)      = explode(',', $imageData);
		$fileName = uniqid().'.'.$extension;
		$imageData = base64_decode($imageData);
		file_put_contents($fileName, $imageData);

		return $fileName;
				
	}




}
