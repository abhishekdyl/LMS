<?php
require_once('../../config.php');
global $CFG,$USER,$DB,$PAGE;
$PAGE->set_context(context_system::instance());
$json = file_get_contents('php://input');
$data = json_decode($json,true);
$email                = $data['user_email'];
$user_name            = $data['username'];
$fname                = $data['firstname'];
$lname                = $data['lastname'];
$userpassword         = $data['userpassword'];
$members_type         = $data['members_type'];
$payment_type         = $data['payment_type'];
$member_count         = $data['member_count'];
$enrolldate           = $data['enrolldate'];
$members_id           = $data['members_id'];
$wp_userid            = $data['wp_userid'];
if($payment_type=="monthly_subcription"){
  $enrolled_enddate = strtotime("+1 month", $enrolldate);
}else if($payment_type=="annual_subcription"){
    $enrolled_enddate = strtotime("+1 year", $enrolldate);
}

// get admin emial
$adminEmail = $DB->get_record("user", array("id"=>2));
$email_id=$adminEmail->email;
$getuser = "SELECT * FROM {user} WHERE username ='".$user_name."' OR email='".$email."'";
$userinfos = $DB->get_record_sql($getuser);
if(empty($userinfos)){
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
    if($insertRecords){
        $wpmenberinsert  = new stdClass();
        $wpmenberinsert->member_id = $members_id;
        $wpmenberinsert->wp_userid = $wp_userid;
        $wpmenberinsert->moodle_userid = $insertRecords;
        $wpmenberinsert->members_type = $members_type;
        $wpmenberinsert->payment_type = $payment_type;
        $wpmenberinsert->member_count = $member_count;
        $wpmenberinsert->enrolldate = $enrolldate;
        $wpmenberinsert->enrolled_enddate = $enrolled_enddate;
        $wpmenberinsert->created_date = time();
        $insertwpmenber=$DB->insert_record('wpmenber_info', $wpmenberinsert,true);
        if(!empty($insertwpmenber)){
            $arr = array('status'=>1);
        }else{
           $arr = array('status'=>0);
        }
        echo json_encode($arr);

    }
}else{
    $getmember = "SELECT * FROM {wpmenber_info} WHERE moodle_userid ='".$userinfos->id."'";
    $memberinfos = $DB->get_record_sql($getmember);
    if($memberinfos){
        $wpmenberupdated  = new stdClass();
        $wpmenberupdated->id = $memberinfos->id;
        $wpmenberupdated->member_id = $members_id;
        $wpmenberupdated->wp_userid = $wp_userid;
        $wpmenberupdated->moodle_userid = $userinfos->id;
        $wpmenberupdated->members_type = $members_type;
        $wpmenberupdated->payment_type = $payment_type;
        $wpmenberupdated->member_count = $member_count;
        $wpmenberupdated->enrolldate = $enrolldate;
        $wpmenberupdated->enrolled_enddate = $enrolled_enddate;
        $wpmenberupdated->updated_date = time();
        $updatedRecords=$DB->update_record('wpmenber_info', $wpmenberupdated,true);
        if(!empty($updatedRecords)){
            $arr = array('status'=>1);
        }else{
           $arr = array('status'=>0);
        }
        echo json_encode($arr);
    }else{
        $wpmenberinsert  = new stdClass();
        $wpmenberinsert->member_id     = $members_id;
        $wpmenberinsert->wp_userid     = $wp_userid;
        $wpmenberinsert->moodle_userid = $userinfos->id;
        $wpmenberinsert->members_type = $members_type;
        $wpmenberinsert->payment_type = $payment_type;
        $wpmenberinsert->member_count = $member_count;
        $wpmenberinsert->enrolldate = $enrolldate;
        $wpmenberinsert->enrolled_enddate = $enrolled_enddate;
        $wpmenberinsert->created_date = time();
        $insertRecords=$DB->insert_record('wpmenber_info', $wpmenberinsert,true);
        if(!empty($insertRecords)){
            $arr = array('status'=>1);
        }else{
           $arr = array('status'=>0);
        }
        echo json_encode($arr);
    }

}
?>