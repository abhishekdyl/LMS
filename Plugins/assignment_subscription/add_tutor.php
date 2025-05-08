<?php 
require_once('../../config.php');

global $DB, $USER;
require_login();


if(isset($_POST)){

   $name = $_POST['add_tutor_value'];
   $createdby = $USER->id;
   $createddate = strtotime(date("Y-m-d h:i:s"));

   $record_ins = new stdClass();
   $record_ins -> name = $name;
   $record_ins -> active = 1;
   $record_ins -> deleted_status = 0;
   $record_ins -> createdby = $createdby;
   $record_ins -> createddate = $createddate;

   $DB -> insert_record('assign_subs_tutors', $record_ins, false);

   echo 'Tutor '.$_POST['add_tutor_value'].' added successfully';

}else{
   echo 'Something is wrong';
}



?>