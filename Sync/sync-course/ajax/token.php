<?php
require_once('../../../../wp-config.php');
if(isset($_POST['token'])){
	$data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
	// echo "<pre>";
	// print_r($data);
	if($data){
		$wpdb->update("{$wpdb->prefix}moodle_settings", 
		array('token'=>trim($_POST['token']),'updateddate'=>time()), array('id'=>$data->id));
		$msg['status']=true;
		$msg['msg']='Updated successfully';
	}else{

		// $wpdb->insert("{$wpdb->prefix}moodle_settings", array(
		//     'url' => trim($_POST['url']),
		//     'createddate'=>time()
		// ));
		$msg['status']=false;
		$msg['msg']='Please set token';
	}
}else{
	$msg['status']=false;
	$msg['msg']='Set token';
}
echo json_encode($msg);