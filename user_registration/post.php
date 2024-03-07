<?php
require_once('../../config.php');
global $DB;

$reg_id = required_param('id', PARAM_RAW);
$post = file_get_contents('php://input');
$decode = (array)json_decode($post);
$data = $DB->get_record('lcl_transection', array("registration_id" => $reg_id));

$obj_upd = new \stdClass();
$obj_upd->id = $data->id; 
$obj_upd->postdata_after = serialize($decode);  
$obj_upd->modified_date = time();

if($decode['status'] == 'CAPTURED' && $decode['response']->message == 'Captured' && $decode['gateway']->response->message == 'Transaction Approved') {
    $obj_upd->status = 1; 
    if($DB->record_exists('lcl_individual_enrollment', array('registration_id'=>$reg_id))) {
    $user_details = $DB->get_record('lcl_individual_enrollment',array('registration_id'=> $reg_id));
    $userlevel = $user_details->level;
    if($userlevel <= 2) {
      $email = base64_encode($decode['customer']->email);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot.'/local/user_registration/admin/enrolment.php');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/x-www-form-urlencoded',
      ]);
      curl_setopt($ch, CURLOPT_POSTFIELDS, 'reg_id='.$reg_id.'&level=1');
      $response = curl_exec($ch);
      curl_close($ch);
    
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot.'/local/user_registration/admin/confirm.php');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/x-www-form-urlencoded',
      ]);
      curl_setopt($ch, CURLOPT_POSTFIELDS, 'reg_id='.$reg_id.'&email='.$email);
      $response = curl_exec($ch);
      curl_close($ch);

      $obj_updr = new \stdClass();
      $obj_updr->id = $reg_id; 
      $obj_updr->status = 1;  
      $obj_updr->payment_status = 1;
      $obj_updr->modified_date = time();
      $DB->update_record('lcl_registration', $obj_updr);
    }
  }else if($DB->record_exists('lcl_corporate_enrollment', array('registration_id'=>$reg_id))) {
    $user_details = $DB->get_record('lcl_corporate_enrollment',array('registration_id'=> $reg_id));
    $userlevel = $user_details->level;
    if($userlevel <= 2) {

      $email = base64_encode($decode['customer']->email);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot.'/local/user_registration/admin/enrolment.php');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/x-www-form-urlencoded',
      ]);
      curl_setopt($ch, CURLOPT_POSTFIELDS, 'reg_id='.$reg_id.'&level=1');
      $response = curl_exec($ch);
      curl_close($ch);

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot.'/local/user_registration/admin/confirm.php');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/x-www-form-urlencoded',
      ]);
      curl_setopt($ch, CURLOPT_POSTFIELDS, 'reg_id='.$reg_id.'&email='.$email);
      $response = curl_exec($ch);
      curl_close($ch);
      
      $obj_updr = new \stdClass();
      $obj_updr->id = $reg_id; 
      $obj_updr->status = 1;  
      $obj_updr->payment_status = 1;
      $obj_updr->modified_date = time();
      $DB->update_record('lcl_registration', $obj_updr);
    }
  }
}

$DB->update_record('lcl_transection', $obj_upd);









