<?php
require_once('../../../wp-config.php');
if(isset($_POST['cmid'])){
	$data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
	// echo "<pre>";
	// print_r($data);
	if($data){
		$wpdb->update("{$wpdb->prefix}moodle_settings", 
		array('cmid'=>trim($_POST['cmid']),'updateddate'=>time()), array('id'=>$data->id));
		$msg['status']=true;
		$msg['msg']='Updated successfully';
	}else{

		// $wpdb->insert("{$wpdb->prefix}moodle_settings", array(
		//     'url' => trim($_POST['url']),
		//     'createddate'=>time()
		// ));
		$msg['status']=false;
		$msg['msg']='Please set moodle Cmid';
	}
}else{
	$msg['status']=false;
	$msg['msg']='Set Cmid';
}
echo json_encode($msg);