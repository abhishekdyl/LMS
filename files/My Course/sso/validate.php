<?php
require_once('../../config.php');
require_once('user_validate.php');
// echo "<pre>";
// print_r($USER);
$otp=required_param('otp',PARAM_RAW);
UserValidate::validateOtp($otp);