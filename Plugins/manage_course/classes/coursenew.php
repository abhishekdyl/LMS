<?php
namespace local_manage_course;

class coursenew{
	public static function wp_course_created(\core\event\course_created $event){
		global $DB, $CFG, $USER;
		$request = get_config('local_manage_course','approved'); //approvel condition
		if( $request == 0 ){ 
			$coursedata = $DB->get_record("course", array( "id"=> $event->courseid)); 
			if($coursedata){ 
				$coursedata->image = self::getcourse_image($event->courseid);
				$coursedata->wpcategory 			= $_POST['pcategory'];
				$coursedata->virtual 				= $_POST['customfield_virtual'];
				$coursedata->price 					= $_POST['customfield_price'];
				$coursedata->age_group 				= $_POST['customfield_age_group'];
				$coursedata->shortsummary_editor 	= $_POST['customfield_shortsummary_editor']['text'];
				$coursedata->metadescript 			= $_POST['customfield_metadescript'];
				$coursedata->upsells 				= $_POST['customfield_upsells'];
				$coursedata->cross_sells 			= $_POST['customfield_cross_sells'];
				$coursedata->attribute              = $_POST['attribute']; 
				$coursedata->syllabus               = $_POST['customfield_syllabus']; 
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
			}
		}
	}
	public static function wp_course_updated(\core\event\course_updated $event){
		global $DB;
		$coursedata=$DB->get_record("course", array( "id"=> $event->courseid));
		if($coursedata){
			$coursedata->image = self::getcourse_image($event->courseid);
			$coursedata->wpcategory 			= $_POST['pcategory'];
			$coursedata->virtual 				= $_POST['customfield_virtual'];
			$coursedata->price 					= $_POST['customfield_price'];
			$coursedata->age_group 				= $_POST['customfield_age_group'];
			$coursedata->shortsummary_editor 	= $_POST['customfield_shortsummary_editor']['text'];
			$coursedata->metadescript 			= $_POST['customfield_metadescript'];
			$coursedata->upsells 				= $_POST['customfield_upsells'];
			$coursedata->cross_sells 			= $_POST['customfield_cross_sells']; 
			$coursedata->attribute              = $_POST['attribute'];
			$coursedata->syllabus               = $_POST['customfield_syllabus']; 
			//echo json_encode($coursedata);
			//die;
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
			curl_close($curl);
		}
	}
	public static function get_wp_url(){
		return get_config('local_manage_course','wpurl');
	}
	public static function getcourse_image($courseid) {
	    global $DB, $CFG, $USER; 
	    //$imageurl= "/wp-content/uploads/2021/05/no-image-220x220.jpg";
	    require_once($CFG->dirroot. '/course/classes/list_element.php');
	    $course = $DB->get_record('course', array('id' => $courseid));
	    $course = new \core_course_list_element($course);
	    foreach ($course->get_course_overviewfiles() as $file) {
	        $isimage = $file->is_valid_image();
	        $token = get_user_key('core_files', $USER->id);
	        $imageurl = file_encode_url("$CFG->wwwroot/tokenpluginfile.php", '/'.$token.'/'. $file->get_contextid(). '/'. $file->get_component(). '/'. $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
	        return $imageurl;
	    }
	    return $imageurl;
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
}