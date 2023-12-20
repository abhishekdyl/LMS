<?php
require_once('../../config.php');
global $DB, $CFG ,$PAGE ,$USER;
require_login();
$id = optional_param('id', 0, PARAM_INT);
if(is_siteadmin($USER->id)){
    $groups = $DB->get_records('groups',array('courseid'=> $id )); 
}else{
    $sql ='SELECT g.* FROM {custome_groups} cg INNER JOIN {groups} g ON cg.groupid = g.id   WHERE cg.teacher ='.$USER->id.' AND cg.courseid = '.$id.' ';
    $groups = $DB->get_records_sql($sql,array()); 
}


$course = $DB->get_record('course',array('id'=>$id));
$html = '
<a href="'.$CFG->wwwroot.'/local/manage_course/courselist.php"><button>Back</button></a>
<h2>'.$course->fullname.'</h2>
<h4> Upcoming Sections </h4>
<table class="table table-stripped" >
<tr><th>S no.</th><th>Name</th><th>Status</th></tr>';
foreach ($groups as $value) {
    // echo "<pre>";
    // print_r($value->id);
    // echo "</pre>";
    $html .= '<tr><td>'.$value->id.'</td><td>'.$value->name.'</td><td><a href="'.$CFG->wwwroot.'/local/manage_course/meetings.php?id='.$value->id.'&courseid='.$id.'">Edit</a></td></tr>';
}

$html .= '</table>
<a href="'.$CFG->wwwroot.'/local/manage_course/meetings.php?courseid='.$id.'"><button>Add a Section</button></a>
';
echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();