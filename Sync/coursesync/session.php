<?php 
require('../../config.php');
require('locallib.php');
global $CFG,$USER,$DB;

 $codehas=$_REQUEST['codehas'];
 $key=$_REQUEST['key'];
 $useremail=base64_decode($key);
 $email = decription($codehas,$useremail);

 $userinfo="SELECT * FROM {user} WHERE email='".$email."' and email!=' '";
$userinfos=$DB->get_record_sql($userinfo);
if(!empty($userinfos)){
		$userdata = $DB->get_record("user", array("id"=>$userinfos->id));

	if(!empty($userdata)){
		complete_user_login($userdata);
	    \core\session\manager::apply_concurrent_login_limit($userdata->id, session_id());
		redirect(new moodle_url($CFG->wwwroot.'/my/index.php'));
	} else {
		redirect(new moodle_url('/'));
	}
	
}else{
	redirect(new moodle_url('/'));
}



?>