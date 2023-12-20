<?php
require_once("../../config.php");
global $DB, $PAGE, $CFG, $USER;
$PAGE->requires->jquery();
require_login();

// $draw = $_POST['id'];
if(!isset($_SESSION['coursesforassignment']) || empty(($_SESSION['coursesforassignment']))){
    $_SESSION['coursesforassignment'] = $DB->get_records_sql("SELECT c.id, c.fullname,cdt.tutor_id FROM {course} c LEFT JOIN {assign_subs_default_tutor} cdt on cdt.course_id = c.id", array());
}
if(!isset($_SESSION['alltutorforassignment']) || empty(($_SESSION['alltutorforassignment']))){
    $_SESSION['alltutorforassignment'] = $DB->get_records_sql("SELECT id, name FROM {assign_subs_tutors} WHERE active=1 and deleted_status=0", array());
}
$moduleid = $DB->get_field_sql("SELECT id FROM {modules} m WHERE m.name='assign'");
$allcourse = $_SESSION['coursesforassignment'];
$alltutor = $_SESSION['alltutorforassignment'];


$draw = $_POST['draw'];
$rowstart = $_POST['start'];
$rowperpage = $_POST['length']; 
$columnIndex = $_POST['order'][0]['column']; 
$columnName = $_POST['columns'][$columnIndex]['data']; 
$columnSortOrder = $_POST['order'][0]['dir']; 
$searchValue = $_POST['search']['value']; 
$course_id = $_POST['course_id_archive']; 
$search_student = $_POST['search_student_archive']; 
$tutor_id  =$_POST['tutor_id_archive'];
$filter_date = $_POST['filter_date']; 
$admission_type_archive = $_POST['admission_type_archive']; 
$custom_start_date  =$_POST['custom_start_date'];
$custom_end_date = $_POST['custom_end_date'];


## Search 
$searchQuery = array("1=1");
$tabs = get_config('local_assignment_subscription', 'tabs');
$duration = get_config('local_assignment_subscription', 'duration');
$selectedtabs = explode(",",$tabs);
if(in_array(1, $selectedtabs)){
    if(!empty($duration)){
        $datelimit=0;
        switch ($duration) {
            case '1 months':
            case '4 months':
            case '8 months':
            case '1 year':
                $datelimit = strtotime("-{$duration}");
                array_push($searchQuery, "asg.timemodified > {$datelimit}");
                break;
            default:
                if(strpos($duration , ",") !== false){
                    $durationdates = explode(",", $duration);
                    $durationstart = $durationdates[0];
                    $durationend = $durationdates[1];
                    array_push($searchQuery, "(asg.timemodified between {$durationstart} AND {$durationend})");
                }
                break;
        }
    }
}
$searchQuerystring = "";
if(!empty($searchQuery)){
         $searchQuerystring = " WHERE ".implode(" AND ", $searchQuery )." ";
} 
$totalsqlquery = "
    SELECT count(asb.id)
    FROM {assign_submission} asb
    INNER JOIN  {assign} a ON a.id = asb.assignment
    INNER JOIN  {user} u ON u.id = asb.userid and u.deleted=0
    INNER JOIN  {assign_grades} asg ON asg.assignment = asb.assignment AND asg.userid = asb.userid  AND asg.grader != -1 AND asg.grade != '-1.00000' AND asg.timemodified > asb.timemodified
    INNER JOIN  {enrol} e ON e.courseid = a.course
    INNER JOIN  {user_enrolments} ue ON ue.enrolid = e.id and ue.userid = u.id
{$searchQuerystring} AND asb.latest=1 AND asb.status='submitted' AND asg.id IS NOT NULL
";
$iTotalRecords = $DB->get_field_sql($totalsqlquery);
// $iTotalRecords = 2000;

if($searchValue != ''){
    $searchQuery[] = "(u.firstname LIKE '%$searchValue%' OR u.lastname LIKE '%$searchValue%' OR u.username LIKE '%$searchValue%' OR u.email LIKE '%$searchValue%' OR c.fullname LIKE '%$searchValue%' OR c.shortname LIKE '%$searchValue%') ";
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

if(!empty($admission_type_archive)){
  if($admission_type_archive!='All'){
    if($admission_type_archive == 'general'){   $searchQuery[] = "(asbs.ispriority = 0 OR asbs.id IS NULL)";  }

    if($admission_type_archive == 'priority'){  $searchQuery[] = "asbs.ispriority = 1";  }

   }
}



$start_time = strtotime(date("d F Y 00:00:00"));
$end_time = strtotime(date("d F Y 23:59:59"));


$this_month_start = strtotime(date("01 F Y 00:00:00"));
$this_month_end = strtotime(date("t F Y 23:59:59"));


if(!empty($filter_date)){

    if(date('D')!='Mon')
    {    
        //take the last monday
        $staticstart = strtotime('last Monday');    
    }else{
        $staticstart = strtotime(date('Y-m-d'));   
    }

        
    if(date('D')!='Sun')
    {
        //always next sunday
        $staticfinish = strtotime('next Sunday');

    }else{
        $staticfinish = strtotime(date('Y-m-d'));
    }



    if($filter_date=='today'){
        $start_time = strtotime(date("d F Y 00:00:00"));
        $end_time = strtotime(date("d F Y 23:59:59"));
    }



    if($filter_date=='this_week'){
        $start_time = $staticstart;
        $end_time = $staticfinish;
    }


    if($filter_date=='last_week'){
        $start_time = strtotime("-1 Week",$staticstart);
        $end_time = strtotime("-1 Week",$staticfinish);
    }


    if($filter_date=='this_month'){
        $start_time = $this_month_start;
        $end_time = $this_month_end;
    }


    if($filter_date=='last_month'){
        $start_time = strtotime("-1 Month",$this_month_start);
        $end_time = strtotime("-1 Month",$this_month_end);
    }

    if($filter_date=='custom_date'){
        $start_time = strtotime(date("d F Y H:i:s",strtotime($custom_start_date)));
        $end_time = strtotime(date("d F Y H:i:s",strtotime($custom_end_date)));
    }

    if($filter_date!='All'){
        $searchQuery[] = "asg.timemodified between '$start_time' AND '$end_time'";
    }

}

if(!empty($tutor_id)){
   if($tutor_id!='All'){
         $searchQuery[] = "ast.tutor_id = '$tutor_id'"; 
   }
}


$searchQuerystring = "";
if(!empty($searchQuery)){
         $searchQuerystring = " WHERE ".implode(" AND ", $searchQuery )." ";
} 
$orderQuery = " ORDER BY tmmd desc";


$sqlquery = "
    SELECT asb.*, asg.timemodified AS tmmd, a.course, u.firstname, u.lastname, ast.tutor_id, cm.id as cmid, c.fullname
    , (SELECT f.id FROM {assignsubmission_file} f WHERE f.submission=asb.id) as filename
    , (SELECT onlinetext FROM {assignsubmission_onlinetext} WHERE submission=asb.id) as onlinetext
    FROM {assign_submission} asb 
    INNER JOIN  {assign} a ON a.id = asb.assignment 
    INNER JOIN  {course_modules} cm ON cm.instance = a.id AND cm.module={$moduleid} 
    INNER JOIN  {course} c ON c.id = a.course 
    INNER JOIN  {user} u ON u.id = asb.userid and u.deleted=0 
    INNER JOIN  {enrol} e ON e.courseid = a.course
    INNER JOIN  {user_enrolments} ue ON ue.enrolid = e.id and ue.userid = u.id
    INNER JOIN {assign_grades} asg ON asg.assignment = asb.assignment AND asg.userid = asb.userid AND asg.grader != -1 AND asg.grade != '-1.00000' AND asg.timemodified > asb.timemodified
    LEFT JOIN {assign_subs_status} asbs on asbs.submissionid = asb.id
    LEFT JOIN {assign_subs_assign_tutors} ast on ast.assignment_id = a.id AND ast.user_id = asb.userid 
    {$searchQuerystring} AND asb.latest=1 AND asb.status='submitted' AND asg.id IS NOT NULL
    {$orderQuery} 
    LIMIT {$rowstart}, {$rowperpage} 
";
 $allforumsdata = $DB->get_records_sql($sqlquery,  array());

$totaldispsqlquery = "
    SELECT count(asb.id)

    FROM {assign_submission} asb
    INNER JOIN  {assign} a ON a.id = asb.assignment
    INNER JOIN  {course} c ON c.id = a.course 
    INNER JOIN  {user} u ON u.id = asb.userid and u.deleted=0
    INNER JOIN  {enrol} e ON e.courseid = a.course
    INNER JOIN  {user_enrolments} ue ON ue.enrolid = e.id and ue.userid = u.id
    INNER JOIN  {assign_grades} asg ON asg.assignment = asb.assignment AND asg.userid = asb.userid  AND asg.grader != -1 AND asg.grade != '-1.00000' AND asg.timemodified > asb.timemodified
    LEFT JOIN {assign_subs_status} asbs on asbs.submissionid = asb.id
    LEFT JOIN {assign_subs_assign_tutors} ast on ast.assignment_id = a.id AND ast.user_id = asb.userid 

{$searchQuerystring} AND asb.latest=1 AND asb.status='submitted' AND asg.id IS NOT NULL

";
$iTotalDisplayRecords = $DB->get_field_sql($totaldispsqlquery);

$count=0;
$allforums = array();
foreach ($allforumsdata as $data) {
    $course_id  = $data->course;
    $cmid  = $data->cmid;
    $user_id = $data->userid;
    $assignment_id = $data->assignment;
    $tutorid = $data->tutor_id;
    $submission_id = $data->id;

    $course = $allcourse[$course_id];
    $defaulttutorid = $course->tutor_id;
    $tutor = $alltutor[$tutorid];
    // $data->fullname = $course->fullname;
    if($tutor){
        $data->Assigned_archive = $tutor->name;
    } else if($tutor1 = $alltutor[$defaulttutorid]){
        $data->Assigned_archive = $tutor1->name;
    } else {
        $data->Assigned_archive = "None";
    }
    $filename = $data->filename;
    $onlinetext = $data->onlinetext;
    $data->Courses_archive = "<span class='wrapon250' title='{$data->fullname}'>$data->fullname</span>";
    $data->Students_archive = $data->firstname." ".$data->lastname;
    $data->Date_marked_archive = date("d/m/Y",$data->tmmd);
    $src = "./img/google-docs.png";
    $src_txt = "./img/text-format.png";
    $src_arrow = "./img/arrow-top-right.png";
    $filename_update =  "<span class='wrapon250'>File Submission</span>";
    if(!empty($onlinetext) && !empty($filename)){
        $data->Submission_archive = "<i class='fa fa-caret-down' aria-hidden='true' data-toggle='collapse' href='#collapseExample".$count."' role='button' aria-expanded='false' aria-controls='collapseExample'></i> <a target='_blank' style='text-decoration: none !important;' href='".$CFG->wwwroot."/mod/assign/view.php?id=".$cmid."&action=grader&userid=".$user_id."'/> <img src=".$src." alt='Some issue' width='20' /> ".$filename_update."</a><div style='transform: translate(25px, 0px);'  class='collapse' id='collapseExample".$count."'><img src=".$src_arrow." alt='Some issue' width='20' /><img src=".$src_txt." alt='Some issue' width='20' /> Onlinetext</div>";
    } else if(!empty($filename)){
         $data->Submission_archive = "<a target='_blank' style='text-decoration: none !important;' href='".$CFG->wwwroot."/mod/assign/view.php?id=".$cmid."&action=grader&userid=".$user_id."'/><img src=".$src_arrow." alt='Some issue' width='20' /> <img src=".$src." alt='Some issue' width='20' /> ".$filename_update."</a>";
    } else if(!empty($onlinetext)){
        $data->Submission_archive = "<a target='_blank' style='text-decoration: none !important;' href='".$CFG->wwwroot."/mod/assign/view.php?id=".$cmid."&action=grader&userid=".$user_id."'/><img  src=".$src_arrow." alt='Some issue' width='20' /> <img src=".$src_txt." alt='Some issue' width='17' /> Onlinetext</a>";
    } else {
        $data->Submission_archive = "<a target='_blank' style='text-decoration: none !important;' href='".$CFG->wwwroot."/mod/assign/view.php?id=".$cmid."&action=grader&userid=".$user_id."'/><img  src=".$src_arrow." alt='Some issue' width='20' /> <img src=".$src_txt." alt='Some issue' width='17' /> Not Found</a>";
    }
    $count++; 
    array_push($allforums, $data);
}
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $iTotalRecords,
    "iTotalDisplayRecords" => $iTotalDisplayRecords,
    "aaData" => $allforums,
    "sql" => $sql,
    "sqlquery" => $sqlquery
);
echo json_encode($response);