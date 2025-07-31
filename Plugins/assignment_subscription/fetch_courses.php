<?php 

require_once('../../config.php');
	global $DB;
	$input_count = $_POST['input_count'];
	$alloptedcourse = $_POST['alloptedcourse'];
	$sql_courses = "SELECT * FROM {course} WHERE id > 1 AND id NOT IN (SELECT course_id FROM {assign_subs_sub_limit})";
	if(!empty($alloptedcourse)){
	    $sql_courses .= " AND id NOT IN ({$alloptedcourse})";
	}
	$row_courses = $DB->get_records_sql($sql_courses);

	$html = null;
	if(sizeof($row_courses) > 0){
    	$html .= '<select name="course_id[]" class="form-control courseforlimit" id="course_id" required="required" style="width: 190px !important;"><option value="">Select Course</option>';
    	foreach ($row_courses as $value)
    	{
    	    if($row_courses = $DB->get_records_sql($sql_courses))
    	    { 
    			$html .= '<option value="'.$value->id.'">'.$value->fullname.'</option>'; 
    		}
    	}
    
    	$html .= '</select>';
	}
    echo $html;