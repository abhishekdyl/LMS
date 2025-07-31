<?php

require_once('../../config.php');
global $DB, $CFG;

$PAGE->requires->jquery();
$post_var = $_POST['post_var'];


function convertTo12HourFormat($hour, $minute = 0) {
    // Validate the hour
    if ($hour < 0 || $hour > 23) {
        return "Invalid hour";
    }

    // Convert to 12-hour format
    $ampm = ($hour < 12) ? 'AM' : 'PM';
    $hour12 = ($hour % 12 === 0) ? 12 : $hour % 12;
    $formattedHour = sprintf("%02d", $hour12);
    $formattedMinute = sprintf("%02d", $minute);

    // Return the formatted time string
    return "$formattedHour:$formattedMinute $ampm";
}

if(!empty($post_var)) {
    if($DB->record_exists('customfield_field', array('shortname'=>'price')) OR $DB->record_exists('customfield_field', array('shortname'=>'foundation_level'))){
        
        $getcustomlevel_id = $DB->get_record('customfield_field', array('shortname'=>'foundation_level'));
        $level_details = $DB->get_record("customfield_data", array('fieldid'=>$getcustomlevel_id->id, 'instanceid'=>$post_var));

        
        $getcustomprice_id = $DB->get_record('customfield_field', array('shortname'=>'price'));
        $price_details = $DB->get_record("customfield_data", array('fieldid'=>$getcustomprice_id->id, 'instanceid'=>$post_var));        


        $getcustomexam_date = $DB->get_record('customfield_field', array('shortname'=>'exam_date'));
        $exam_details = $DB->get_record("customfield_data", array('fieldid'=>$getcustomexam_date->id, 'instanceid'=>$post_var));
    

        $coursedetails = $DB->get_record("course", array('id'=>$post_var));
        $check_customfield_field_foundation_level = $DB->get_record("customfield_field", array('shortname'=>'foundation_level'));
        $options_json_decode = json_decode($check_customfield_field_foundation_level->configdata);
        $final_option_list = explode("\n",$options_json_decode->options);
    
    
    	$getcustom_location = $DB->get_record('customfield_field', array('shortname'=>'course_venue'));
        $arr = array();
        if(!empty($getcustom_location->id)) {
            $location_details = $DB->get_record("customfield_data", array('fieldid'=>$getcustom_location->id, 'instanceid'=>$post_var)); 	
            $location_details_value = strip_tags($location_details->value);	
        	if(!empty($location_details_value)) {
            	$location_details_value = explode("\n", $location_details_value);
            	if(count($location_details_value)>0) {
            	foreach($location_details_value as $data) {
            		$data = trim($data);
            		array_push($arr, trim($data));
            	 }
               }
            }
         }  
    	

    	$location = '';
        $count_arr = count($arr);
    	foreach($arr as $data) {
          if($count_arr==1) {
	  	    $location .= '<li><input type="checkbox" name="course_location[]" value="'.$data.'" checked="checked" hidden="hidden" /> <span for="'.$data.'">'.$data.'</span></li>';
          }else{
            $location .= '<li><input type="checkbox" name="course_location[]" value="'.$data.'"/> <span for="'.$data.'">'.$data.'</span></li>';
          }  
        }
    
    
    	$getcustom_course_timing = $DB->get_record('customfield_field', array('shortname'=>'course_timing'));
        $arra = array();
        if(!empty($getcustom_course_timing->id)) {
            $course_timing_data = $DB->get_record("customfield_data", array('fieldid'=>$getcustom_course_timing->id, 'instanceid'=>$post_var)); 
        	if(!empty($course_timing_data)) {
            	$course_timing_value = strip_tags($course_timing_data->value);	
            	$course_timing_configdata = json_decode($getcustom_course_timing->configdata);
        		$course_timing_configdata = $course_timing_configdata->options;
        		$course_timing_configdata = explode("\n", $course_timing_configdata);
            	$course_timing_value = explode(",", $course_timing_value);
            	if(count($course_timing_configdata)>0){
            		foreach($course_timing_value as $data){
            			$data = trim($course_timing_configdata[$data]);
            			array_push($arra, trim($data));
            		}
                }
            }
        }


        $starttime_hour = $DB->get_record('customfield_field', array('shortname'=>'starttime_hour'));  
        $customdata_starttime_hour = $DB->get_record("customfield_data", array('fieldid'=>$starttime_hour->id, 'instanceid'=>$post_var));
        $starttime_hour = $customdata_starttime_hour->value-1;

        $starttime_minute = $DB->get_record('customfield_field', array('shortname'=>'starttime_minute'));  
        $customdata_starttime_minute = $DB->get_record("customfield_data", array('fieldid'=>$starttime_minute->id, 'instanceid'=>$post_var));
        $starttime_minute = $customdata_starttime_minute->value-1;
        
        $starttime = convertTo12HourFormat($starttime_hour, $starttime_minute);

        $endtime_hour = $DB->get_record('customfield_field', array('shortname'=>'endtime_hour'));  
        $customdata_endtime_hour = $DB->get_record("customfield_data", array('fieldid'=>$endtime_hour->id, 'instanceid'=>$post_var));
        $endtime_hour = $customdata_endtime_hour->value-1;
      
        $endtime_minute = $DB->get_record('customfield_field', array('shortname'=>'endtime_minute'));  
        $customdata_endtime_minute = $DB->get_record("customfield_data", array('fieldid'=>$endtime_minute->id, 'instanceid'=>$post_var));
        $endtime_minute = $customdata_endtime_minute->value-1;

        $endtime = convertTo12HourFormat($endtime_hour, $endtime_minute);;
        $duration = " (".$starttime."-".$endtime.")";

        
    	$timing = '';
        $count_arra = count($arra);
    	foreach($arra as $data) {
            if($count_arra==1){
    		    $timing .= '<li><input type="checkbox" name="course_timing[]" value="'.$data.'" checked="checked" hidden="hidden" /> <span for="'.$data.'">'.$data.$duration.'</span></li>';
            }else{

                $timing .= '<li><input type="checkbox" name="course_timing[]" value="'.$data.'"/> <span for="'.$data.'">'.$data.$duration.'</span></li>';
            }
        }

        if($level_details->value <= '2'){
            $level = 1;
        }

        if($level_details->value >= '3'){
            $level = 2;
        }
    
        echo json_encode(array(
                               "price"=>$price_details->value,
                               "level"=>trim($final_option_list[$level_details->value-1]),
                               "level_val"=>$level_details->value,
                               "location"=>$location,
                               "startdate"=>$coursedetails->startdate,
                               "enddate"=>$coursedetails->enddate,
                               "examdate"=>$exam_details->value,
        					   "timing"=>$timing
                           ));
        die;
    } else {
        echo json_encode(array("msg"=>"Something is wrong !"));
        die;
    }
} else {
    echo json_encode(array("msg"=>"Something is wrong !"));
    die;
}










