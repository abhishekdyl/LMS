<?php
require_once('../../../wp-config.php');
$data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
if($data){
	$courseid=$data->courseid;
	if(!empty($courseid)){
		$msg['status']=true;
		$msg['msg']='Success';
		$msg['courseid']=$courseid;

	}else{
		$msg['status']=false;
		$msg['msg']='courseid not found';
	}
}else{
	$msg['status']=false;
	$msg['msg']='Course data not found';

}
echo json_encode($msg);