<?php
require_once('../../../wp-config.php');
if(isset($_POST['url'])){
	$data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
	if($data){
		$wpdb->update("{$wpdb->prefix}moodle_settings", 
		array('url'=>trim($_POST['url']),'updateddate'=>time()), array('id'=>$data->id));
		$msg['status']=true;
		$msg['msg']='Updated successfully';
	}else{

		$wpdb->insert("{$wpdb->prefix}moodle_settings", array(
		    'url' => trim($_POST['url']),
		    'createddate'=>time()
		));
		$msg['status']=true;
		$msg['msg']='Updated successfully';
	}
}else{
	$msg['status']=false;
	$msg['msg']='Set url';
}
echo json_encode($msg);