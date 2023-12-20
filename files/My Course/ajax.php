<?php
// use core_completion\progress;
require('../../config.php');
require_once($CFG->dirroot . '/lib/enrollib.php');
require_once("../../course/externallib.php");
global $DB, $USER, $PAGE;

$sort =  optional_param('sort', 0, PARAM_TEXT);
$param =  optional_param('param', "all", PARAM_TEXT);
$filter =  optional_param('filter', 0, PARAM_TEXT);
$searchvalue =  optional_param('search', 0, PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);
if($param != 'all'){
    $filter = "search";
}

$offset = ($page-1)*5;
if($offset >= 5){
    // $offset++;
}

$COURSEAPI = new core_course_external();
$dataapi =  $COURSEAPI->get_enrolled_courses_by_timeline_classification($filter, $limit = 5, $offset, $sort, "", "",$searchvalue);
$countdata = $COURSEAPI->get_enrolled_courses_by_timeline_classification($filter, 0, 0, $sort, "", "",$searchvalue);
$totalcount = count($countdata['courses']);

$msg='';
    foreach ($dataapi['courses'] as $course ) { 
        
        $msg .= '
            <div class="col-12 my-3 ">
                <div class="card">
                    <div class="row" >
                        <div style="display:flex; flex-wrap:nowrap; " >
                            <div class="col-4 p-3">
                                <div class="img-frame img-frame-1by1 ">
                                <a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'"><img src="'.$course->courseimage.'" alt="img""></a>
                                </div>
                            </div>
                            <div class="col-8 p-3">
                                <div class="card-body custom-border">
                                    <h6>'.$course->coursecategory.'</h6>
                                    <h4 class="custom-title head-lime-clamp">'.$course->fullname.'</h4>
                                    <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Unde eos sit et velit aut? Quaerat
                                    nihil atque consectetur dolorum optio error nulla autem a? Rerum quas quos saepe soluta fuga cum
                                    dignissimos eius nobis tempora. </p>
                                    <div class="row pro_side">
                                        <div class="col-4">
                                            <div class="h-progresss">
                                                <div id="myProgresss">
                                                    <div id="myBarr" style="width : '.$course->progress.'%;"></div>
                                                </div>
                                                        <p>'.round($course->progress).'% Complete</p>
                                            </div> 
                                        </div>
                                        <div class="col-6">
                                            <div class="side-details">
                                                <i class="fa fa-calendar"></i>
                                                
                                                <span>Subscribed on '.date("F j, Y",$course->startdate).'</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ';
    }
// $havemore = enrol_get_my_courses($fields = null, $sort = $sortord, $limit = 10, $courseids = [], $allaccessible = false, $offset = 10*($page+1), $excludecourses = []);
$returndata = new stdClass();
$returndata->html=$msg;
if($totalcount > 0){
    $totalpage = ceil($totalcount/5);
    $prevhidden= "";
    $nexthidden= "";
    if($page == 1){
        $prevhidden = 'disable';
    }
    if($page == $totalpage){
        $nexthidden = 'disable';
    }
    $returndata->pagination='<div><a href="javascript:void(0);"  class="more loadmoredata custom-data-load '.$prevhidden.'" data-to="prev">Prev</a>'.$page."/".$totalpage.'<a href="javascript:void(0);"  class="more loadmoredata custom-data-load '.$nexthidden.'" data-to="next">Next</a></div>';
} else {
    $returndata->pagination='';
}
$returndata->filter = array($filter, $limit = 5, $offset, $sort, "", "",$searchvalue);
$returndata->dataapi=$dataapi;
echo json_encode($returndata);