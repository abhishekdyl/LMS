<?php 

require_once('../../config.php');
	global $DB;
	$alloptedcourse_list = $_POST['alloptedcourse_list'];
	$totallen = $_POST['totallen'];
	
	$sql_courses = "SELECT * FROM {course} WHERE id > 1 AND visible=1";
	if(!empty($alloptedcourse_list)){
	    $sql_courses .= " AND id NOT IN ({$alloptedcourse_list})";
	}
	$coursehtml =''; 
	$row_courses = $DB->get_records_sql($sql_courses);

	if(sizeof($row_courses) > 0){
    	$coursehtml .= '<select class="js-example-basic-single-new" multiple name="course_id_default_'.$totallen.'[]" required="required" style="width: 200px;">';
    	foreach ($row_courses as $value)
    	{
    	    if($row_courses = $DB->get_records_sql($sql_courses))
    	    { 
    			$coursehtml .= '<option value="'.$value->id.'">'.$value->fullname.'</option>'; 
    		}
    	}
    	$coursehtml .= '</select>';
	}
	$alloptedcourse = $_POST['alloptedcourse'];
	$tutorhtml = '';
	$fetch_assigned_tutor = "SELECT * FROM {assign_subs_tutors} WHERE deleted_status=0";
	$get_assigned_tutor   = $DB->get_records_sql($fetch_assigned_tutor);
	$tutorhtml .="<option value=''>Select Tutor</option>";
	foreach($get_assigned_tutor as $value){    
        $tutorhtml .= "<option value=".$value->id.">$value->name</option>";
	}
$returndata  = array("coursehtml"=>$coursehtml, "tutorhtml"=>$tutorhtml, "totallen"=>$totallen);
echo json_encode($returndata);