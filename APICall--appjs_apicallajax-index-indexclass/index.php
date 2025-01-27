<?php
$allowed_origins = array(
	"https://qaweb.fivestudents.com",
    "https://newqaweb.fivestudents.com",
	"https://web.fivestudents.com"
);
// Handle OPTIONS requests (preflight requests)
$http_origin = $_SERVER['HTTP_ORIGIN'];
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
// $m = new stdClass();
// $m->status = 0;
// $m->message = "Error";
// $m->data = null;
// $m->code = $code;
// $m->error = array(
//     "code"=> 400,
//     "title"=> "Maintenance",
//     "message"=> "Server is undergoing administrative maintenance. please try again later",
//     "data"=> null
// );
// echo json_encode($m);
// die;
require_once("../../config.php");
require_once("index_class.php");
define('NO_DEBUG_DISPLAY', true);
define('WS_SERVER', true);
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
header("HTTP/1.0 200 Successfull operation");
$getpatameter=json_decode(file_get_contents('php://input',True),true);
$functionname = $getpatameter['wsfunction'];
$wstoken = "";
global $wstoken, $lang, $PARENTUSER, $CATGRADE;
$lang = "fr";
if(isset($getpatameter['wstoken'])){
    $wstoken = $getpatameter['wstoken'];
}

$args = $getpatameter['wsargs'];

$logdata = new stdClass();
$logdata->version = 7;
$logdata->token = $wstoken;
$logdata->wsfunction = $functionname;
$logdata->args = json_encode($args);
$logdata->logtime = time();
$logdata->id=$DB->insert_record("apilog",$logdata);

$baseobject = new APIManager();
$baseobject->set_args($args);
$skippedfunctions = array("login", "ForgotPasswordReset","requestForgotPassword", "prerequisiteData","getRegions", "getGradelevels","getValidateUsername", "registerAdventure", "getLastAdventure", "accountMigrationLink", "restoreAccount","getWinnerAddress", "getServerTime", "getdeletedAccount", "recoverAccount", "assignComponent");
if (method_exists($baseobject, $functionname)) {
    if(in_array($functionname, $skippedfunctions)){
        $baseobject->validatetoken($wstoken);
        $baseobject->$functionname($args);
    } else if($baseobject->validatetoken($wstoken)) {
        $baseobject->$functionname($args);
    }
} else {
    $baseobject->sendError("error", "functionality not found");
}

echo json_encode($baseobject);
$apilog_response= new stdClass();
$apilog_response->logid = $logdata->id;
$apilog_response->userid = $USER->id;
$apilog_response->wsfunction = $logdata->wsfunction;
$apilog_response->responsedata = json_encode($baseobject);
$apilog_response->responsetime = time();
$DB->insert_record("apilog_response",$apilog_response);