<?php
require_once('../../config.php');
global $CFG,$USER,$DB,$PAGE;
$json = file_get_contents('php://input');
$data = json_decode($json,true);

$email                = $data['user_email'];
$user_name            = $data['username'];
$fname                = $data['firstname'];
$lname                = $data['lastname'];
$userpassword         = $data['userpassword'];

$userinfo = $DB->get_record_sql("SELECT id FROM {user} where username ='$user_name' or email='$email'");
// echo "<pre>";
// print_r($userinfo);
// echo "</pre>";
// die;
if(empty($userinfo)){
    // echo "new user";die;
    $userinsert  = new stdClass();
    $userinsert->username = $user_name;
    $userinsert->password=$userpassword;
    $userinsert->firstname= $fname;
    $userinsert->lastname= $lname;
    $userinsert->email= $email;
    $userinsert->timecreated= time();
    $userinsert->timemodified= time();
    $userinsert->middlename= " ";
    $userinsert->confirmed= 1;
    $userinsert->mnethostid= 1;
    $insertRecords=$DB->insert_record('user', $userinsert);
}else{
    $userupdate  = new stdClass();
    $userupdate->id = $userinfo;
    $userupdate->username = $user_name;
    $userupdate->password=$userpassword;
    $userupdate->firstname= $fname;
    $userupdate->lastname= $lname;
    $userupdate->email= $email;
    $userupdate->timecreated= time();
    $userupdate->timemodified= time();
    $userupdate->middlename= " ";
    $userupdate->confirmed= 1;
    $userupdate->mnethostid= 1;
    $insertRecords=$DB->update_record('user', $userupdate);

}

?>