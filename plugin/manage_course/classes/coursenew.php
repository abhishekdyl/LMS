<?php
namespace local_manage_course;

class coursenew{
	public static function wp_course_created(\core\event\course_created $events){
		global $DB;
		echo "Hello";
/*
		$coursedata=$DB->get_record('course', array( "id"=> $events->courseid));
		print_r($coursedata);
		if($coursedata){

			$coursedata->custom_data=self::get_course_metadata($coursedata->id);
			$coursedata->wpcategory = 1559;
			
			print_r($coursedata);
			$wpurl= self::get_wp_url();

			$curl = curl_init();
	
			curl_setopt_array($curl, array(
			CURLOPT_URL =>$wpurl.'/wp-content/plugins/sync-course/product-course-add.php',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>json_encode($coursedata),
			  CURLOPT_HTTPHEADER => array(
			   'Content-Type: application/json'
			  ),
			));
	
			$response = curl_exec($curl);
			print_r($response);
			die;

			return $response;
			

		} else {
			echo "<pre>";
			print_r($events);
			die;
		}
		*/

	}
	private static function get_course_metadata($courseid) {
	    $handler = \core_customfield\handler::get_handler('core_course', 'course');
	    $datas = $handler->get_instance_data($courseid);
	    $metadata = [];
	    foreach ($datas as $data) {
	    	//echo 'data: '.$data->get_value();
	        if (empty($data->get_value())) {
	            continue;
	        }
	        $cat = $data->get_field()->get_category()->get('name');
	        $metadata[$data->get_field()->get('shortname')] = $data->get_value();
	    }
	    return $metadata;
	}
	public static function wp_course_updated(\core\event\course_updated $events){
		global $DB;
		$coursedata=$DB->get_record("course", array( "id"=> $events->courseid));
		//$coursedata=get_course($events->courseid);
		$coursedata->image='https://www.oneplanet.edu.et/wp-content/uploads/2023/03/yuvraj-singh-B4p9zFTuXnA-unsplash-scaled.jpg';
		$coursedata->custom_data=self::get_course_metadata($events->courseid);
		if(\core_course\external\course_summary_exporter::get_course_image($coursedata) !== false){

		$coursedata->image=\core_course\external\course_summary_exporter::get_course_image($coursedata);//self::get_course_image($coursedata);

		}
		$wpurl= self::get_wp_url();
		//$wpurl= "https://www.oneplanet.edu.et/";
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL =>$wpurl.'/wp-content/plugins/sync-course/product-course-update.php',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>json_encode($coursedata),
		  CURLOPT_HTTPHEADER => array(
		   'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);

	}
	public static function get_wp_url(){
		return get_config('local_manage_course','wpurl');
	}
	public static function wp_category_created(\core\event\course_category_created $events){
		global $DB;
		
		$cat_id = $events->objectid;
		$cat_data=$DB->get_record('course_categories',array('id'=>$cat_id));
		$parent_id = $cat_data->parent;


		$cat_data_chk_parent=$DB->get_record('course_categories',array('id'=>$parent_id));
		$parent_name = $cat_data_chk_parent->name;

		// echo "<pre>";
		// print_r($cat_data);

		// die();
	    $wpurl= self::get_wp_url();
		//$wpurl= "https://www.oneplanet.edu.et";
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL =>$wpurl.'/wp-content/plugins/sync-course/product-category-add.php?parent_name='.base64_encode($parent_name),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>json_encode($cat_data),
		  CURLOPT_HTTPHEADER => array(
		   'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		// echo "<pre>";
		// print_r($cat_data);

		// echo $parent_name;

		// die();
	}
	public static function wp_category_updated(\core\event\course_category_updated $events){
		global $DB;
		$cat_id=$events->objectid;
		$cat_data=$DB->get_record('course_categories',array('id'=>$cat_id));
		
		foreach($cat_data AS $key => $val){
			if($key == 'parent'){
				$parent_id = $val;
			}
		}


		$cat_data_chk_parent=$DB->get_record('course_categories',array('id'=>$parent_id));

		foreach($cat_data_chk_parent AS $key_sec => $val_sec){
			if($key_sec == 'name'){
				$parent_name = $val_sec;
			}
		}


		$wpurl= self::get_wp_url();
		//$wpurl= "https://www.oneplanet.edu.et/";
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL =>$wpurl.'/wp-content/plugins/sync-course/product-category-update.php?parent_name='.$parent_name,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>json_encode($cat_data),
		  CURLOPT_HTTPHEADER => array(
		   'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		

	}
	// public static function digital_litracy_submitted_quiz(\mod_quiz\event\attempt_submitted $events){
	// 	global $DB,$USER;
	// 	$url=self::get_wp_url();
	// 	//$url= "https://www.oneplanet.edu.et/";
	// 	//echo "<pre>";
	// 	if(!empty($url)){
	// 		$curl = curl_init();

	// 		curl_setopt_array($curl, array(
	// 			CURLOPT_URL => $url.'/wp-content/plugins/sync-course/get_digital_litracy_course.php',
	// 			CURLOPT_RETURNTRANSFER => true,
	// 			CURLOPT_ENCODING => '',
	// 			CURLOPT_MAXREDIRS => 10,
	// 		  	CURLOPT_TIMEOUT => 0,
	// 		  	CURLOPT_FOLLOWLOCATION => true,
	// 		  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 		  	CURLOPT_CUSTOMREQUEST => 'GET'
	// 		));

	// 		$response = curl_exec($curl);

	// 		curl_close($curl);
	// 		$responsedata=json_decode($response);
	// 		if($responsedata->status){
	// 		$sql="SELECT cmc.* FROM {course_modules_completion} cmc JOIN {course_modules} cm ON cm.id=cmc.coursemoduleid JOIN {course} c ON c.id=cm.course WHERE cmc.userid=:userid AND cmc.completionstate=:completionstate";
	// 			$userdata=$DB->get_record_sql($sql,array('userid'=>$USER->id,'completionstate'=>2));
			
	// 			if($userdata){
	// 				$litracy_status=false;
	// 				if($userdata->completionstate){
	// 					$litracy_status=true;
	// 				}

	// 				$curl1 = curl_init();
	// 				$user_info=(object)array('username'=>$USER->username,'useremail'=>$USER->email,'userlitracy_status'=>$litracy_status);
	// 				curl_setopt_array($curl1, array(
	// 					CURLOPT_URL => $url.'/wp-content/plugins/sync-course/update_user_litracy.php',
	// 					CURLOPT_RETURNTRANSFER => true,
	// 					CURLOPT_ENCODING => '',
	// 					CURLOPT_MAXREDIRS => 10,
	// 				  	CURLOPT_TIMEOUT => 0,
	// 				  	CURLOPT_FOLLOWLOCATION => true,
	// 				  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 				  	CURLOPT_CUSTOMREQUEST => 'POST',
	// 				  	CURLOPT_POSTFIELDS =>json_encode($user_info),
	// 	   				CURLOPT_HTTPHEADER => array(
	// 	     				'Content-Type: application/json'
	// 	   				),
	// 				));

	// 				$response2 = curl_exec($curl1);
	// 				curl_close($curl1);

	// 			}
				
	// 		}

	// 	}
	
	// }

	
}