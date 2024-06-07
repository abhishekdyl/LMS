<?php
require('../../config.php');
require_once($CFG->dirroot."/course/externallib.php");
global $DB, $CFG, $USER;
// $cousreId =  $id  = optional_param('id', 0, PARAM_INT);
$cousreId = 100;
$quertech=	"SELECT u.id as teacherId, c.id as courseId FROM {user} u INNER JOIN {role_assignments} ra ON ra.userid = u.id INNER JOIN {context} ct ON ct.id = ra.contextid INNER JOIN {course} c ON c.id = ct.instanceid INNER JOIN {role} r ON r.id = ra.roleid WHERE r.id = 3 and c.id = $cousreId";
$enroltech = $DB->get_records_sql($quertech);

foreach ($enroltech as $erlusers) {                  
    $enlluser = $erlusers->teacherid;
   if ($enlluser) {
       $techdetail = "SELECT * FROM {user} where id=$enlluser";
       $infotech = $DB->get_record_sql($techdetail);
   }
}
echo $OUTPUT->header();
echo "<pre>";
print_r($infotech);
echo "</pre>";
echo $OUTPUT->footer(); 
?>