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
$wpparentid         = $data['parentid'];

// $email                = 'abhishek1-800@studyif.com';
// $user_name            = 'abhishek1-800';
// $fname                = 'abhishek02';
// $lname                = 'lds0222';
// $userpassword         = 'P@ssw0rd';

$userinfo = $DB->get_record_sql("SELECT id FROM {user} where username ='$user_name' or email='$email'");

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
    $menberid = $DB->get_record('wpmenber_info',array('wp_userid'=>$wpparentid));
    if(!empty($menberid)){
        $obj = new stdClass();
        $obj->memberinfoid = $menberid->id;
        $obj->mdl_userid = $insertRecords;
        $obj->createddate = time();
        $obj->updateddate = time();
        $insertRecords=$DB->insert_record('member_child_info', $obj);
    }
    
}else{
     
    $userupdate  = new stdClass();
    $userupdate->id = $userinfo->id;
    // $userupdate->username = $user_name;
    $userupdate->password=$userpassword;
    $userupdate->firstname= $fname;
    $userupdate->lastname= $lname;
    // $userupdate->email= $email;
    // $userupdate->timecreated= time();
    $userupdate->timemodified= time();
    // $userupdate->middlename= " ";
    // $userupdate->confirmed= 1;
    // $userupdate->mnethostid= 1;
    $updateRecords=$DB->update_record('user', $userupdate);
    $mem_childid =$DB->get_record('member_child_info',array('mdl_userid'=>$userinfo->id));
    if(!empty($mem_childid)){
        $obj = new stdClass();
        $obj->id = $mem_childid->id;
        $obj->updateddate = time();
        $upRecords=$DB->update_record('member_child_info', $obj);
    }

}

?>