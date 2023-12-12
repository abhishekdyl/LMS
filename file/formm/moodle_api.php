<?php
require_once("./config.php");
global $DB;

$sql="SELECT id,employee_name,employee_email,employee_contact,employee_address,employee_salary FROM mdl_employee m LEFT JOIN mdl_employee_salary n ON m.employee_sal=n.employee_id "; //ok
    //  $sql="SELECT * FROM mdl_employee m INNER JOIN mdl_employee_salary n ON m.employee_sal=n.employee_id "; //ok
    //  $sql=" SELECT * FROM mdl_employee m RIGHT JOIN mdl_employee_salary n ON m.employee_sal=n.employee_id ";
// $table= 'employee';
//    $userlog = new stdClass();
//     //    $userlog->id = 2;    
//        $userlog->employee_name = "";
//        $userlog->employee_email = "";
//        $userlog->employee_contact = ;
//        $userlog->employee_address = "";
//         $userlog->employee_sal = 2;
//  $DB->insert_record('employee',$userlog);    //ok
// $DB->update_record('employee', $userlog, $bulk=false);  //ok
//  $DB ->delete_records('employee' ,array("id"=>4));  //ok
// $alluser =$DB -> get_records_sql('SELECT * FROM {employee} WHERE Id = 14', array());  //ok
// $alluser = $DB->get_records('employee', array("id"=>1)); //ok     
//  $alluser =$DB->count_records('employee', array ()); //ok
// $alluser =$DB->record_exists('employee', array("id"=>2));
$alluser = $DB->get_records_sql($sql, array());  
// $alluser = $DB->get_records(['employee', 'employee_salary'], 'employee_salary = employee.employee_id');
echo "<pre>";
print_r($alluser);
echo "</pre>";

 




?>