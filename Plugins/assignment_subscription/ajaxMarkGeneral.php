<?php
require_once("../../config.php");
global $DB, $PAGE, $CFG, $USER;
$PAGE->requires->jquery();
require_login();


$draw = $_POST['draw'];
$rowstart = $_POST['start'];
$rowperpage = $_POST['length']; 
$columnIndex = $_POST['order'][0]['column']; 
$columnName = $_POST['columns'][$columnIndex]['data']; 
$columnSortOrder = $_POST['order'][0]['dir']; 
$searchValue = $_POST['search']['value']; 
$course_id = $_POST['course_id']; 
$search_student = $_POST['search_student']; 
$filter_order = $_POST['filter_order']; 
$filter_status = $_POST['filter_status']; 
$tutor_id  =$_POST['tutor_id'];

$submitted = "submitted";
## Search 
$searchQuery = array("1=1 AND asb.latest=1 ");
$tabs = get_config('local_assignment_subscription', 'tabs');
$duration = get_config('local_assignment_subscription', 'duration');
$selectedtabs = explode(",",$tabs);
if(in_array(2, $selectedtabs)){
    if(!empty($duration)){
        $datelimit=0;
        switch ($duration) {
            case '1 months':
            case '4 months':
            case '8 months':
            case '1 year':
                $datelimit = strtotime("-{$duration}");
                array_push($searchQuery, "asb.timemodified > {$datelimit}");
                break;
            default:
                if(strpos($duration , ",") !== false){
                    $durationdates = explode(",", $duration);
                    $durationstart = $durationdates[0];
                    $durationend = $durationdates[1];
                    array_push($searchQuery, "(asb.timemodified between {$durationstart} AND {$durationend})");
                }
                break;
        }
    }
}
$searchQuerystring = "";
if(!empty($searchQuery)){
         $searchQuerystring = " WHERE ".implode(" AND ", $searchQuery )." ";
} 

$iGeneralMarkedSql = "
	SELECT count(asb.id)
	FROM {assign_submission} asb
	INNER JOIN  {assign} a ON a.id = asb.assignment
	INNER JOIN  {course_modules} cm ON cm.instance = a.id
	INNER JOIN  {modules} m ON m.id = cm.module AND m.name='assign'
	INNER JOIN  {course} c ON c.id = a.course
	INNER JOIN  {user} u ON u.id = asb.userid
	INNER JOIN  {enrol} e ON e.courseid = a.course
    INNER JOIN  {user_enrolments} ue ON ue.enrolid = e.id and ue.userid = u.id
	 LEFT JOIN  {assign_subs_status} ass ON ass.userid = u.id AND ass.submissionid = asb.id AND ass.ispriority = 1
	 LEFT JOIN  {assign_grades} ag ON ag.assignment = asb.assignment AND ag.userid = asb.userid  AND ag.grader != -1 AND ag.grade != '-1.00000' AND ag.timemodified >= asb.timemodified
	 LEFT JOIN  {assign_subs_assign_tutors} asat ON asat.course_id = c.id AND asat.assignment_id = a.id AND asat.user_id = u.id
	 LEFT JOIN  {assign_subs_tutors} ast on ast.id = asat.tutor_id
	{$searchQuerystring}
	AND asb.status='$submitted' AND ag.id IS NOT NULL AND ass.id IS NULL AND ast.id IS NOT NULL AND ag.timemodified >= ?
";
$iGeneralMarked = $DB->get_field_sql($iGeneralMarkedSql, array(strtotime(date("d F Y"))));


// $iTotalRecordsSql = "
// 	SELECT count(asb.id) 
// 	FROM {assign_submission} asb
// 	INNER JOIN  {assign} a ON a.id = asb.assignment
// 	INNER JOIN  {course_modules} cm ON cm.instance = a.id
// 	INNER JOIN  {modules} m ON m.id = cm.module AND m.name='assign'
// 	INNER JOIN  {course} c ON  c.id = a.course
// 	INNER JOIN  {user} u ON u.id = asb.userid
// 	INNER JOIN  {enrol} e ON e.courseid = a.course
//     INNER JOIN  {user_enrolments} ue ON ue.enrolid = e.id and ue.userid = u.id
// 	 LEFT JOIN  {assign_subs_status} ass ON ass.userid = u.id AND ass.submissionid = asb.id AND ass.ispriority = 1
// 	 LEFT JOIN  {assign_subs_default_tutor} asdt ON asdt.course_id = c.id
// 	 LEFT JOIN  {assign_grades} ag ON ag.assignment = asb.assignment AND ag.userid = asb.userid  AND ag.grader != -1 AND ag.grade != '-1.00000' AND ag.timemodified >= asb.timemodified
// 	 LEFT JOIN  {assign_subs_assign_tutors} asat ON asat.course_id = c.id AND asat.assignment_id = a.id AND asat.user_id = u.id
// 	 LEFT JOIN  {assign_subs_tutors} ast on ast.id = asat.tutor_id
// 	WHERE asb.status='$submitted' AND ag.id IS NULL AND ass.id IS NULL
// ";
$iTotalRecordsSql = "
	SELECT count(asb.id) 
	FROM {assign_submission} asb
	INNER JOIN  {assign} a ON a.id = asb.assignment
	INNER JOIN  {course_modules} cm ON cm.instance = a.id
	INNER JOIN  {modules} m ON m.id = cm.module AND m.name='assign'
	INNER JOIN  {course} c ON  c.id = a.course
	INNER JOIN  {user} u ON u.id = asb.userid
	INNER JOIN  {enrol} e ON e.courseid = a.course
    INNER JOIN  {user_enrolments} ue ON ue.enrolid = e.id and ue.userid = u.id
	 LEFT JOIN  {assign_subs_status} ass ON ass.userid = u.id AND ass.submissionid = asb.id AND ass.ispriority = 1
	 LEFT JOIN  {assign_subs_default_tutor} asdt ON asdt.course_id = c.id
	 LEFT JOIN  {assign_grades} ag ON ag.assignment = asb.assignment AND ag.userid = asb.userid  AND ag.grader != -1 AND ag.grade != '-1.00000' AND ag.timemodified >= asb.timemodified
	 LEFT JOIN  {assign_subs_assign_tutors} asat ON asat.course_id = c.id AND asat.assignment_id = a.id AND asat.user_id = u.id
	 LEFT JOIN  {assign_subs_tutors} ast on ast.id = asat.tutor_id
	WHERE asb.status='$submitted' AND ag.id IS NULL AND ass.id IS NULL

";
// $iTotalRecords = $DB->get_field_sql($iTotalRecordsSql);
$iTotalRecords = 100;

$orderQuery = array();

if($searchValue != ''){
    $searchQuery[] = "(u.firstname LIKE '%$searchValue%' OR u.lastname LIKE '%$searchValue%' OR u.username LIKE '%$searchValue%' OR u.email LIKE '%$searchValue%' OR c.fullname LIKE '%$searchValue%' OR c.shortname LIKE '%$searchValue%' )";
}
if(!empty($course_id)){
	if($course_id!='All'){
		$searchQuery[] = "a.course = '$course_id'"; 
	}
}
if(!empty($search_student)){
	if($search_student!='All'){
		$searchQuery[] = "asb.userid = '$search_student'"; 
	}
}
if(!empty($filter_status)){
	if($filter_status!='All'){
		if($filter_status == 'pending'){
			$searchQuery[] = "asat.id IS NULL"; 
		}
	if($filter_status == 'in_progress'){
			$searchQuery[] = "asat.id IS NOT NULL";
		}
	}
}
if(!empty($tutor_id)){
	if($tutor_id!='All'){
		$searchQuery[] = "(asat.tutor_id = '$tutor_id' OR (asdt.tutor_id = '$tutor_id' AND asat.tutor_id IS NULL))"; 
	}
}
if(!empty($searchQuery)){
	$searchQuery = " WHERE ".implode(" AND ", $searchQuery )." ";
} 
if(!empty($filter_order)){
	if($filter_order == 'oldest_to_newest'){
		$orderQuery[] = "asb.timemodified "; 
		$columnSortOrder = "asc";
	}
	if($filter_order == 'newest_to_oldest'){
		$orderQuery[] = "asb.timemodified ";
		$columnSortOrder = "desc";
	}
}

if($rowperpage != ''){
	if($columnName=='Courses_general'){         
		$columnName = "c.id"; 
	}
	if($columnName=='Students_general'){        
		$columnName = "u.id"; 
	}
	if($columnName=='Submission_Date_general'){ 
		$columnName = "asb.timemodified"; 
	}
	if($columnName=='Assign_general'){          
		$columnName = "asat.tutor_id"; 
	}
		$orderQuery = " ORDER BY ".implode($orderQuery)." ".$columnSortOrder;
}


$allforums = array();
$sql = "
	SELECT asb.*, c.fullname, u.firstname, u.lastname, a.course, ag.timemodified AS tmmd, cm.id as cmid, ast.name as tutorname
    , (SELECT f.id FROM {assignsubmission_file} f WHERE f.submission=asb.id) as filename
    , (SELECT onlinetext FROM {assignsubmission_onlinetext} WHERE submission=asb.id) as onlinetext
	FROM {assign_submission} asb
	INNER JOIN  {assign} a ON a.id = asb.assignment
	INNER JOIN  {course_modules} cm ON cm.instance = a.id
	INNER JOIN  {modules} m ON m.id = cm.module AND m.name='assign'
	INNER JOIN  {course} c ON  c.id = a.course
	INNER JOIN  {user} u ON u.id = asb.userid
	INNER JOIN  {enrol} e ON e.courseid = a.course
    INNER JOIN  {user_enrolments} ue ON ue.enrolid = e.id and ue.userid = u.id
	 LEFT JOIN  {assign_subs_status} ass ON ass.userid = u.id AND ass.submissionid = asb.id AND ass.ispriority = 1
	 LEFT JOIN  {assign_subs_default_tutor} asdt ON asdt.course_id = c.id
	 LEFT JOIN  {assign_grades} ag ON ag.assignment = asb.assignment AND ag.userid = asb.userid  AND ag.grader != -1 AND ag.grade != '-1.00000' AND ag.timemodified >= asb.timemodified
	 LEFT JOIN  {assign_subs_assign_tutors} asat ON asat.course_id = c.id AND asat.assignment_id = a.id AND asat.user_id = u.id
	 LEFT JOIN  {assign_subs_tutors} ast on ast.id = asat.tutor_id
	{$searchQuery} AND asb.status='$submitted' AND ag.id IS NULL AND ass.id IS NULL {$orderQuery} LIMIT {$rowstart}, {$rowperpage}
 ";
 $allforums = $DB->get_records_sql($sql,  array());
 // $allforums = array();






$iGeneralTotalDisplayRecordsSql = "
	SELECT count(asb.id)
	FROM {assign_submission} asb
	INNER JOIN  {assign} a ON a.id = asb.assignment
	INNER JOIN  {course_modules} cm ON cm.instance = a.id
	INNER JOIN  {modules} m ON m.id = cm.module AND m.name='assign'
	INNER JOIN  {course} c ON  c.id = a.course
	INNER JOIN  {user} u ON u.id = asb.userid
	INNER JOIN  {enrol} e ON e.courseid = a.course
    INNER JOIN  {user_enrolments} ue ON ue.enrolid = e.id and ue.userid = u.id
	 LEFT JOIN  {assign_subs_status} ass ON ass.userid = u.id AND ass.submissionid = asb.id AND ass.ispriority = 1
	 LEFT JOIN  {assign_subs_default_tutor} asdt ON asdt.course_id = c.id
	 LEFT JOIN  {assign_grades} ag ON ag.assignment = asb.assignment AND ag.userid = asb.userid  AND ag.grader != -1 AND ag.grade != '-1.00000' AND ag.timemodified >= asb.timemodified
	 LEFT JOIN  {assign_subs_assign_tutors} asat ON asat.course_id = c.id AND asat.assignment_id = a.id AND asat.user_id = u.id
	 LEFT JOIN  {assign_subs_tutors} ast on ast.id = asat.tutor_id
	$searchQuery AND asb.status='$submitted' AND ag.id IS NULL AND ass.id IS NULL
";
$iGeneralTotalDisplayRecords = $DB->get_field_sql($iGeneralTotalDisplayRecordsSql);






$count=0;
$row_users  = $DB->get_records("assign_subs_tutors", array("active"=>1, "deleted_status"=>0));
foreach ($allforums as $key => $data) {

	$course_id  = $data->course;
	$cmid  = $data->cmid;
	$user_id = $data->userid;
	$assignment_id = $data->assignment;
	$submission_id = $data->id;
	$filename = $data->filename;
	$onlinetext = $data->onlinetext;
	$data->Courses_general = "<span class='wrapon250' title='{$data->fullname}'>$data->fullname</span>";
	$data->Students_general = $data->firstname." ".$data->lastname;
	$data->Submission_Date_general = date("d/m/Y",$data->timemodified);
	$src = "./img/google-docs.png";
	$src_txt = "./img/text-format.png";
	$src_arrow = "./img/arrow-top-right.png";

	// Select dropdown inside datatabe code
	if($get_assigned_tutorname = $DB->get_record_sql("SELECT id FROM {assign_subs_tutors} WHERE name='$data->tutorname'")){
		$selected = "selected";
		$select = '<select id="select'.$count.'" class="form-control update_tutor_general" data-courseid="'.$course_id.'" data-userid="'.$user_id.'" data-assignmentid="'.$assignment_id.'" style="display: none;">';
		foreach ($row_users as $key) {
			$select .= '<option value="'.$key->id.'" "'.$selected.'">'.$key->name.'</option>';
		}
		$select .= '</select><span id="span_none'.$count.'">'.$data->tutorname.'&nbsp;&nbsp;&nbsp;<sup><i class="fa fa-edit" onclick="selectEnable_general('.$count.')" id='.$count.'></i><sup></span>';
	} else {
		if($get_default_tutor_course_list = $DB->get_record_sql("SELECT tutor_id FROM {assign_subs_default_tutor} WHERE course_id=$course_id")){
			$get_assigned_tutor = $DB->get_record_sql("SELECT `name` FROM {assign_subs_tutors} WHERE id=$get_default_tutor_course_list->tutor_id");
			$None = $get_assigned_tutor->name."(Default)";
		} else {
			$None = "None";
		}
		$selected  ='';
		$select = '<select class="form-control update_tutor_general" data-courseid="'.$course_id.'" data-userid="'.$user_id.'" data-assignmentid="'.$assignment_id.'">
		<option value="0">'.$None.'</option>';
		foreach ($row_users as $key) {
			$select .= '<option value="'.$key->id.'" "'.$selected.'">'.$key->name.'</option>';
		}
		$select .= '</select>';
	}


	// File submission column
	$filename_update =  "<span class='wrapon200_general' title='{$filename}'> File Submission</span>";
	if(empty($data->tutorname)){
		$status_chk = "Pending";
		if(!empty($onlinetext) && !empty($filename)){
			$Submission_general = "<i class='fa fa-caret-down' aria-hidden='true' data-toggle='collapse' href='#collapseExample".$count."' role='button' aria-expanded='false' aria-controls='collapseExample".$count."'></i> <img src=".$src." alt='Some issue' width='20' /> ".$filename_update."<div style='transform: translate(25px, 0px);'  class='collapse' id='collapseExample".$count."'><img src=".$src_arrow." alt='Some issue' width='20' /><img src=".$src_txt." alt='Some issue' width='20' /> Onlinetext</div>";
		} else if(!empty($filename)){
			$Submission_general = "<img src=".$src_arrow." alt='Some issue' width='20' /> <img src=".$src." alt='Some issue' width='20' /> $filename_update";
		} else if(!empty($onlinetext)){
			$Submission_general = "<img  src=".$src_arrow." alt='Some issue' width='20' /> <img src=".$src_txt." alt='Some issue' width='17' /> Onlinetext";
		} else {
			$Submission_general = "<img src=".$src_arrow." alt='Some issue' width='20' /> <img src=".$src." alt='Some issue' width='20' /> Not Found";
		}

	}else{
		$status_chk = "In progress";
		if(!empty($onlinetext) && !empty($filename)){
			$Submission_general = "<i class='fa fa-caret-down' aria-hidden='true' data-toggle='collapse' href='#collapseExample".$count."' role='button' aria-expanded='false' aria-controls='collapseExample".$count."'></i> <a target='_blank' style='text-decoration: none !important;' href='".$CFG->wwwroot."/mod/assign/view.php?id=".$cmid."&action=grader&userid=".$user_id."'/> <img src=".$src." alt='Some issue' width='20' /> ".$filename_update."<div style='transform: translate(25px, 0px);'  class='collapse' id='collapseExample".$count."'><img src=".$src_arrow." alt='Some issue' width='20' /><img src=".$src_txt." alt='Some issue' width='20' /> Onlinetext</div></a>";
		} else if(!empty($filename)){
			$Submission_general = "<a target='_blank' style='text-decoration: none !important;' href='".$CFG->wwwroot."/mod/assign/view.php?id=".$cmid."&action=grader&userid=".$user_id."'/><img src=".$src_arrow." alt='Some issue' width='20' /> <img src=".$src." alt='Some issue' width='20' /> ".$filename_update."</a>";
		} else if(!empty($onlinetext)){
			$Submission_general = "<a target='_blank' style='text-decoration: none !important;' href='".$CFG->wwwroot."/mod/assign/view.php?id=".$cmid."&action=grader&userid=".$user_id."'/><img  src=".$src_arrow." alt='Some issue' width='20' /> <img src=".$src_txt." alt='Some issue' width='17' /> Onlinetext</a>";
		}else{
			$Submission_general = "<img src=".$src_arrow." alt='Some issue' width='20' /> <img src=".$src." alt='Some issue' width='20' /> Not Found";
		}
	} 

	$data->Status_general = $status_chk;
	$data->Assign_general =  $select;
	$data->Submission_general = $Submission_general;
	$allforums[$key] = $data;
	$count++; 

}


$response = array(
	"draw" => intval($draw),
	"iTotalRecords" => $iGeneralTotalDisplayRecords,
	"iTotalDisplayRecords" => $iGeneralTotalDisplayRecords,
	"iGeneralMarked" => $iGeneralMarked,
	"sqlquery" => $sql,
	"aaData" => array_values($allforums)
);


echo json_encode($response);






