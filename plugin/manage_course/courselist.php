<?php
require_once('../../config.php');
global $DB, $CFG ,$PAGE ,$USER;
$PAGE->requires->jquery(); 
require_login();
if(is_siteadmin($USER->id)){
    $coursesql = 'SELECT wpp.id,wpp.courseid,c.fullname,c.startdate,cat.name FROM {wpproduct} wpp INNER JOIN {course} c ON wpp.courseid = c.id INNER JOIN {course_categories} cat ON c.category = cat.id';
    $data = $DB->get_records_sql($coursesql,array());   
}else{
    $coursesql = 'SELECT c.instanceid as courseid ,co.fullname,co.startdate,cat.name FROM {role_assignments} ra INNER JOIN {context} c ON ra.contextid = c.id 
    INNER JOIN {wpproduct} wp ON c.instanceid = wp.courseid
    INNER JOIN {course} co ON wp.courseid = co.id INNER JOIN {course_categories} cat ON co.category = cat.id WHERE ra.userid = ? AND ra.roleid = 3';
    $data = $DB->get_records_sql($coursesql,array($USER->id));   
}


echo $OUTPUT->header();

echo "<a href='$CFG->wwwroot/local/manage_course/'><button>Back</button></a>";
echo "<a href='$CFG->wwwroot/local/manage_course/course.php'><button>Create New Course</button></a>";
$html = '
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
<table class="table table-stripped" id="table_filter">
            <thead>
                <tr>
                    <th>Sno.</th>
                    <th>Course</th>
                    <th>Category</th>
                    <th>Course Visibility</th>
                    <th>Course start Date</th>
                    <th>Status</th>
                </tr>
            </thead><tbody>'; 
        $i = 1; 
foreach ($data as $courses) {


    $html .='<tr>
                <td>'.$i.'</td>
                <td>'.$courses->fullname.'</td>
                <td>'.$courses->name.'</td>
                <td>Visibility</td>
                <td>'.($courses->startdate?date("d F Y h:i A", $courses->startdate):"").'</td>
                <td><a href="/local/manage_course/course.php?id='.$courses->courseid.'">Edit  </a><a href="/local/manage_course/groups.php?id='.$courses->courseid.'"> Manage Sections </a></th>
            </tr>';
    // echo '<pre>';
    // print_r($courses);
    // echo '</pre>';
$i++;
}
$html .='</tbody></table>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
$(function(){
    console.log("aaaaaaaaaaaaaaaaaaaa");
    $("#table_filter").DataTable();
});
</script>';
echo $html;
echo $OUTPUT->footer();
