<?php
use core_completion\progress;
require('../../config.php');
require_once($CFG->dirroot . '/lib/enrollib.php');
require_once("../../course/externallib.php");
global $DB, $USER, $PAGE;

$page = $_GET['page'];
$next_page=(int)$page+1;

function coursecolor($courseid) {
    $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894', '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];
    $color = $basecolors[$courseid % 10];
    return $color;
}
function getcourse_image($courseid) {
    global $DB, $CFG;
    require_once($CFG->dirroot. '/course/classes/list_element.php');
    $course = $DB->get_record('course', array('id' => $courseid));
    $course = new core_course_list_element($course);
    foreach ($course->get_course_overviewfiles() as $file) {
        $isimage = $file->is_valid_image();
        $imageurl = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'. $file->get_contextid(). '/'. $file->get_component(). '/'. $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
        return $imageurl;
    }
    if(empty($imageurl)){
        $color = coursecolor($course->id);
        $pattern = new \core_geopattern();
        $pattern->setColor($color);
        $pattern->patternbyid($courseid);
        $classes = 'coursepattern';
        $imageurl = $pattern->datauri();
    }
    return $imageurl;
}
$data = enrol_get_my_courses($fields = null, $sort = "sortorder asc", $limit = 10, $courseids = [], $allaccessible = false, $offset = 10*$page, $excludecourses = []);


$msg='';
    foreach ($data as $course ) { 
        $percentage = progress::get_course_progress_percentage($course, $userid);
        $percentagedata=(!empty($percentage))? $percentage:0;
        
        $msg .= '

            <div class="col-12 col-lg-3 col-md-4 my-3 custom-course-frame">
                <div class="card">
                    <div class="img-frame img-frame-1by1">
                         <img src="'.getcourse_image($course->id).'" alt="img"">
                    </div>
                    <div class="card-body custom-border">
                        <h4 class="custom-title head-lime-clamp">'.$course->fullname.'</h4>
                        <div class="h-progresss">
                            <div id="myProgresss">
                                <div id="myBarr" style="width : '.$percentage.'%;"></div>
                            </div>
                            <p>'.$percentagedata.'%</p>
                        </div>  
                        <a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" class="theme-btn">Visualizza</a>
                    </div>
                </div>

            </div>
            ';
    }
$havemore = enrol_get_my_courses($fields = null, $sort = null, $limit = 10, $courseids = [], $allaccessible = false, $offset = 10*($page+1), $excludecourses = []);

$returndata = new stdClass();
$returndata->html=$msg;
$returndata->havemore= (!empty($havemore));
echo json_encode($returndata);