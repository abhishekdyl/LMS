<?php 
require_once('../../config.php');

global $DB, $USER, $PAGE;

$PAGE->requires->jquery();
require_login();



$current_logged_in_user =  $USER->id;

if(isset($_POST)){

   $course_id = $_POST['course_id'];
   $tutor_id = $_POST['tutor_id'];
   $user_id = $_POST['user_id'];
   $assignment_id = $_POST['assignment_id'];

   $chk_entry=  "SELECT * FROM {assign_subs_assign_tutors} WHERE course_id=$course_id AND user_id=$user_id AND assignment_id=$assignment_id";
   $row_entry = $DB->get_record_sql($chk_entry);

   if(!empty($row_entry->id)){

	$DB-> delete_records('assign_subs_assign_tutors', array('id' => $row_entry->id));

   }


   if($tutor_id!=0){

   $record_ins = new stdClass();
   $record_ins -> course_id = $course_id;
   $record_ins -> assignment_id = $assignment_id;
   $record_ins -> tutor_id = $tutor_id;
   $record_ins -> user_id = $user_id;

   $DB -> insert_record('assign_subs_assign_tutors', $record_ins, false);

   } 
}

?>