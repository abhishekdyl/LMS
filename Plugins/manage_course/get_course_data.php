<?php
require_once('../../config.php');
$json = file_get_contents('php://input');
$data = json_decode($json);
// echo "<pre>";
// print_r($data);
// die;
$course_data=$DB->get_record('course',array('id'=>$data->courseid));
if($course_data){
	$msg['status']=true;
	$msg['data']=$course_data;
}else{
	$msg['status']=false;
	$msg['data']='';
}
echo json_encode($msg);