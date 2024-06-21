<?php 
require_once('../../../config.php');
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
header("HTTP/1.0 200 Successfull operation");
$getpatameter=json_decode(file_get_contents('php://input',True),true);
$functionname = null;
$args = null;
if(is_array($getpatameter)){
    $functionname = $getpatameter['wsfunction'];
    $args = $getpatameter['wsargs'];
}
class APIManager {
	private $wpdb;
    public $status = 0; 
    public $message = "Error";
    public $data = null;
    public $code = 404;
    public $error = array(
        "code"=> 404,
        "title"=> "Server Error.",
        "message"=> "Server under maintenance"
    );
    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->code = 404;
        $this->error = array(
            "code"=> 404,
            "title"=> "Server Error...",
            "message"=> "Missing functionality---------"
        );
    }
    private function sendResponse($data) {
        $this->status = 1;
        $this->message = "Success";
        $this->data = $data;
        $this->code = 200;
        $this->error = null;
    }
    private function sendError($title, $message, $code=400) {
        $this->status = 0;
        $this->message = "Error";
        $this->data = null;
        $this->code = $code;
        $this->error = array(
            "code"=> $code,
            "title"=> $title,
            "message"=> $message
        );
    }
    public function getContent($args)
    {
    	global $DB,$CFG,$USER;
    	// $args = (object)$args;
    	$tamplate = $DB->get_records('tool_certificate_templates',array());
		$html ='<label for="certif">Designate Certificate: &nbsp</label>
				<select name="cert" id="certif">
				<option value="0">Select Certificates</option>';
		foreach ($tamplate as $temp) {
			$html .='<option value="'.$temp->id.'">'.$temp->name.'</option>';
		}
		$html .='</select>
		<input type="hidden" name="user" id="uid"  value="'.$args.'">'; 

    	$this->sendResponse($html);
    }
    
    public function assign_certificate($args)
    {
    	global $DB,$CFG,$USER;
    	$args = (object)$args;
			$template = \tool_certificate\template::instance($args->temp_id);
			$template->issue_certificate($args->userid);
    	$this->sendResponse($args);
    }
}
$baseobject = new APIManager();
if (method_exists($baseobject, $functionname)) {
    $baseobject->$functionname($args);
}
echo json_encode($baseobject);