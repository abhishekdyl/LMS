<?php
require_once('../../config.php');
global $CFG,$USER,$DB,$PAGE;
$json = file_get_contents('php://input');
$data = json_decode($json,true);
$email                = $data['user_email'];
$user_name            = $data['username'];
$wp_userid            = $data['wp_userid'];
$getuserdata = "SELECT * FROM {user} where username = ? or email=?";
$userinfo = $DB->get_record_sql($getuserdata,array($user_name,$email));
$getmember = "SELECT * FROM {wpmenber_info} WHERE moodle_userid =?";
$memberinfos = $DB->get_record_sql($getmember,array($userinfo->id));
if($memberinfos){
    $deleted = $DB->delete_records("wpmenber_info", array("id"=>$memberinfos->id));
}
if($userinfo){
    $deleteuser = delete_user($userinfo);
    $arr = array('status'=>1);
}else{
    $arr = array('status'=>3);
}
echo json_encode($arr);

?>