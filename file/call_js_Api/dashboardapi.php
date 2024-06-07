<?php 
require_once('../../../../../../config.php');
use core_completion\progress;
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
header("HTTP/1.0 200 Successfull operation");
$getpatameter=json_decode(file_get_contents('php://input',True),true);
$wstoken = "";
if(isset($getpatameter['wstoken'])){
    $wstoken = $getpatameter['wstoken'];
}
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
    	require_once($CFG->dirroot . '/lib/enrollib.php');
    	require_once($CFG->dirroot . '/lib/datalib.php');
    	$args = (object)$args;
    	switch ($args->activetab) {
    		case 'mycourse':
    			// $enrolled_courses_count = $DB->get_records_sql("SELECT COUNT(DISTINCT c.id) FROM {user_enrolments} ue JOIN {enrol} e ON ue.enrolid = e.id JOIN {course} c ON e.courseid = c.id WHERE ue.userid = :userid", array('userid' => $USER->id));
    			$data = enrol_get_my_courses($fields = null, $sort = "sortorder asc", $limit = 10, $courseids = [], $allaccessible = false, $offset = 0, $excludecourses = []);
    			// $data2 = $data;
    			$enrol_count = 0;
    			$inpro_count = 0;
    			$finish_count = 0;
    			$html = '';
    			foreach ($data as $course) {
    				$enrol_count++;
    				$percentage = progress::get_course_progress_percentage($course, $USER->id);
					if(round($percentage) < 100 && round($percentage) < 0){
						$inpro_count++;
					}else if(round($percentage) == 100){
						$finish_count++;
					}
    			}

    			// $html .= 'aa'.$enrol_count.'bb'.$inpro_count.'cc'.$pass_count;
         		// $havemoredata = enrol_get_my_courses($fields = null, $sort = "sortorder asc", $limit = 10, $courseids = [], $allaccessible = false, $offset = 10, $excludecourses = []);


				$html = '
						    <div class="learn-press-subtab-content">
						        <div class="learn-press-profile-course__statistic">
						            <div id="dashboard-statistic">
						                <div class="dashboard-statistic__row">
						                    <div class="statistic-box" title="Total enrolled courses">
						                        <p class="statistic-box__text">Enrolled Course</p>
						                        <span class="statistic-box__number">'.$enrol_count.'</span>
						                    </div>
						                    <div class="statistic-box" title="Total course is in progress">
						                        <p class="statistic-box__text">Inprogress Course</p>
						                        <span class="statistic-box__number">'.$inpro_count.'</span>
						                    </div>
						                    <div class="statistic-box" title="Total courses finished">
						                        <p class="statistic-box__text">Finished Course</p>
						                        <span class="statistic-box__number">'.$finish_count.'</span>
						                    </div>
						                    <div class="statistic-box" title="Total courses passed">
						                        <p class="statistic-box__text">Passed Course</p>
						                        <span class="statistic-box__number">0</span>
						                    </div>
						                    <div class="statistic-box" title="Total courses failed">
						                        <p class="statistic-box__text">Failed Course</p>
						                        <span class="statistic-box__number">0</span>
						                    </div>
						                </div>
						            </div>
						        </div>
						        <div class="learn-press-profile-course__tab">
						        </div>
						    </div>';
		    	$this->sendResponse($html);
		    	// $this->sendResponse("sendResponse_mycourse");
    		break;
    		case 'certificates':
    			// $enrolled_courses = $DB->get_records_sql("SELECT DISTINCT c.id, c.fullname FROM {user_enrolments} ue JOIN {enrol} e ON ue.enrolid = e.id JOIN {course} c ON e.courseid = c.id WHERE ue.userid = :userid", array('userid' => $USER->id));
    			// foreach ($enrolled_courses as $course) {
    			// 	get_all_instances_in_courses('certificate', array($course->id => $course), $userid, $includeinvisible);
    			// }
		    	$this->sendResponse("sendResponse_certificates");
    		break;
    		case 'quizzes':
    			// $enrolled_courses = $DB->get_records_sql("SELECT DISTINCT c.id, c.fullname FROM {user_enrolments} ue JOIN {enrol} e ON ue.enrolid = e.id JOIN {course} c ON e.courseid = c.id WHERE ue.userid = :userid", array('userid' => $USER->id));
    			// foreach ($enrolled_courses as $course) {
    			// 	get_all_instances_in_courses('quizzes', array($course->id => $course), $userid, $includeinvisible);
    			// }

    			// get_all_instances_in_courses($modulename, array($course->id => $course), $userid, $includeinvisible);
    			$this->sendResponse("sendResponse_quizzes");
    		break;
    		case 'wishlist':
		    	$this->sendResponse("sendResponse_wishlist");
    		break;
    		case 'orders':
		    	$this->sendResponse("sendResponse_orders");
    		break;
    		case 'events':
		    	$this->sendResponse("sendResponse_events");
    		break;
    		case 'basic-information':
		    	$this->sendResponse("sendResponse_basic-information");
    		break;
    		case 'avatar':
		    	$this->sendResponse("sendResponse_avatar");
    		break;
    		case 'change-password':
		    	$this->sendResponse("sendResponse_change-password");
    		break;
    		case 'uploaded-id':
		    	$this->sendResponse("sendResponse_uploaded-id");
    		break;
    		case 'logout':
		    	$this->sendResponse("sendResponse_logout");
    		break;
    		
    		default:
    			$this->sendResponse("No data..");
    		break;
    	}
    	// $this->sendResponse($args);
    }
}
$baseobject = new APIManager();
if (method_exists($baseobject, $functionname)) {
    $baseobject->$functionname($args);
}
echo json_encode($baseobject);