<?php

require_once('../../config.php');
global $DB, $CFG;

// Individual
$val1 = $_POST['val1'];
$val2 = $_POST['val2'];
$val3 = $_POST['val3'];

// Corporate
$val4 = $_POST['val4'];
$val5 = $_POST['val5'];
$val6 = $_POST['val6'];


if(!empty($val1) OR !empty($val4)) {
	$val = $val1 != "" ? $val1 : ($val4 != "" ? $val4 : $val1);
    if($DB->record_exists('lcl_individual_enrollment', array('mobile_number'=>$val))) {
        $mobile_number = "Mobile is already in use, please try another number";
    } else if($DB->record_exists('lcl_corporate_enrollment', array('mobile_number'=>$val))) {
        $mobile_number = "Mobile is already in use, please try another number";
    } else if ($DB->record_exists('lcl_application_form', array('mobile'=>$val))){
        $mobile_number = "Mobile is already in use, please try another number";
    } else {
        $mobile_number = null;
    }
}


if(!empty($val2) OR !empty($val5)) {
	$val = $val2 != "" ? $val2 : ($val5 != "" ? $val5 : $val2);
    if($DB->record_exists('lcl_individual_enrollment', array('email'=>$val))) {
        $email = "Email is already in use, please try another email";
    } else if($DB->record_exists('lcl_corporate_enrollment', array('email'=>$val))) {
        $email = "Email is already in use, please try another email";
    } else if ($DB->record_exists('lcl_application_form', array('email'=>$val))){
        $email = "Email is already in use, please try another email";
    } else {
        $email = null;
   }
}


if(!empty($val3) OR !empty($val6)) {
	$val = $val3 != "" ? $val3 : ($val6 != "" ? $val6 : $val3);
    if ($DB->record_exists('lcl_individual_enrollment', array('cpr'=>$val))) {
        $cpr = "Cpr is already in use, please try another";
    } else if ($DB->record_exists('lcl_application_form', array('cpr'=>$val))){
        $cpr = "Cpr is already in use, please try another";
    } else {
        $cpr = null;
   }
}

echo json_encode(
    array(
        "mobile"=>$mobile_number, 
        "email"=>$email, 
        "cpr"=>$cpr
    )
);











