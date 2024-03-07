<?php

function local_assignment_subscription_extend_navigation($settingsnav) {
    global $DB, $USER, $CFG, $PAGE;
    return;
    $foonode = navigation_node::create(
        "Home",
        $CFG->wwwroot."/local/assignment_subscription/home.php",
        navigation_node::NODETYPE_LEAF,
        '[assignment_subscription]',
        '[assignment_subscription]',
        new pix_icon('t/addcontact', $strfoo)
    );
    $foonode->make_active();
    $settingsnav->add_node($foonode);

    // $settingnode = $PAGE->settingsnav->add("dsfsdfs", new moodle_url('/a/link/if/you/want/one.php'), navigation_node::TYPE_CONTAINER);
    // $thingnode = $settingnode->add("aaaaa", new moodle_url('/a/link/if/you/want/one.php'));
    // $thingnode->make_active();
}

// function local_assignment_subscription_extend_navigation(global_navigation $navigation){
//     // echo "<pre>";
//     // print_r($navigation);
//     // //$node = navigation_node::create(...);
//     // //$node->showinflatnavigation = true;
//     // //$navigation->add_node($node);
//     // die;

//     global $PAGE,$CFG;
//    try {

//      $main_node = $PAGE->navigation->find('myprofile', navigation_node::TYPE_ROOTNODE);
//      $main_node->nodetype=1;
//      $main_node->collapse = false;

//      $name = 'aaaaaaa';//get_string('glossary','local_glossary');
//      $url = new moodle_url($CFG->wwwroot.'/local/glossary/index.php');
//      $navigation->add( $name, $url);
//    }
//   catch (Exception $e) {}
// }

function local_assignment_subscription_extend_settings_navigation($settingsnav, $context){
  global $PAGE,$COURSE;
  //echo "<pre>";
  //print_r( $PAGE->navigation);
  //echo "<pre>";
 // print_r($settingsnav);
  // echo ""
  // print_r($context);
     // $id = $context->instanceid;
     //    $urltext = 'testttt';//get_string('gradereportlink', 'myplugin');
     //    $url = new moodle_url($CFG->wwwroot . '/grade/report/grader/index.php', array('id'=>$id));
     //    $coursesettingsnode = $settingsnav->find('courseadmin', null);   // 'courseadmin' is the menu key
     //    $node = $coursesettingsnode->create($urltext, $url, navigation_node::NODETYPE_LEAF, null, 'gradebook',  new pix_icon('i/report', 'grades'));
     //    $coursesettingsnode->add_node($node,  'gradebooksetup'); //'gradebooksetup' is 
  //&&has_capability('gradereport/grader:view', $context)
    if(($context->contextlevel === 70) || ($context->contextlevel === 50)){
        //$secondarynav=$PAGE->secondarynav;
        // echo "<pre>";
        // print_r($PAGE);
        // echo "</pre>";

      //$PAGE->navbar->ignore_active();
//$PAGE->navbar->add("rrrr", new moodle_url('/a/link/if/you/want/one.php'));
//$PAGE->navbar->add('ssss', new moodle_url('/a/link/if/you/want/one.php'));
      // echo "<pre>";
      // print_r($COURSE);
      // die;
        // $coursenode = $PAGE->navigation->find($COURSE->id, navigation_node::TYPE_COURSE);
        // $thingnode = $coursenode->add("testtt", new moodle_url('/a/link/if/you/want/one.php'));
        // $thingnode->make_active();
       // $settingnode = $PAGE->settingsnav->add("test", new moodle_url('/a/link/if/you/want/one.php'), navigation_node::TYPE_CONTAINER); 
       // $thingnode = $settingnode->add("testtt", new moodle_url('/a/link/if/you/want/one.php'));
       //  $thingnode->make_active();
    //  $PAGE->set_headingmenu('ssss');
    //  $PAGE->set_button('<a href="javascript:void(0);" class="btn btn-info">Common Forum</a>');
     // echo "testttttttt";
        // $id = $context->instanceid;
        // $urltext = 'test linkkk';//get_string('gradereportlink', 'myplugin');
        // $url = new moodle_url($CFG->wwwroot . '/grade/report/grader/index.php', array('id'=>$id));
        // $coursesettingsnode = $settingsnav->find('courseadmin', null);   // 'courseadmin' is the menu key
        // $node = $coursesettingsnode->create($urltext, $url, navigation_node::NODETYPE_LEAF, null, 'gradebook',  new pix_icon('i/report', 'grades'));
        // $coursesettingsnode->add_node($node,  'gradebooksetup'); //'gradebooksetup' is an where you put the link before
  }
}
function local_assignment_subscription_before_footer() {
    global $PAGE;
   $PAGE->requires->jquery();
   // var_dump($PAGE->pagetype);
   // if(trim($PAGE->pagetype) == "mod-assign-view"){
   //     echo 'before_footer<script>
   //      $(document).ready(function(){
   //          $(\'<tr><th class="cell c0" scope="row">Submission Type</th><td class="cell c1 lastcol"><span style="color: green;">Priority</span></td></tr>\').insertAfter(".submissionsummarytable>table>tbody>tr:nth-child(3)");

   //      });
   //      </script>
   //     ';
   // }
}
function local_assignment_subscription_standard_footer_html() {
    global $PAGE, $DB, $USER, $CFG;
    // return;

   $PAGE->requires->jquery();
   if(trim($PAGE->pagetype) == "mod-assign-view"){
        $id = required_param('id', PARAM_INT);
        $action = optional_param('action', '', PARAM_ALPHA);
        list ($course, $cm) = get_course_and_cm_from_cmid($id, 'assign');
        $assignment = $cm->instance;
        $iTotalRecords = $DB->get_field_sql("SELECT count(id) FROM {assign_submission} WHERE assignment=:assignment AND userid=:userid AND status=:status", array("assignment"=>$assignment, "userid"=>$USER->id, "status"=>"submitted"));
        if($iTotalRecords==''){ $iTotalRecords=1; }else{ $iTotalRecords = $iTotalRecords+1; }
        $attemptnumberhtml = '<tr class=""><th class="cell c0" style="" scope="row">Attempt number</th><td class="submissionstatussubmitted cell c1 lastcol" style="">This is attempt number '.$iTotalRecords.'</td></tr>';

        $sub_type ='General';
        $havesubs = false;
        $url_goto = $CFG->wwwroot.'/local/assignment_subscription';
        $sub_type_message ='<p class="prioritynotification" style="text-align: center; padding: 10px; background-color: #028090; color: #ffffff; margin-left: 20%; margin-right: 20%; border-radius: 25px;"><b>To upgrade future submissions to <span style="color: #eb904c;">PRIORITY MARKING</span> and receive your feedback within 24 hours Mon-Fri, sign up <a href="'.$url_goto.'/index.php" style="color: #eb904c;">HERE</a></b></p>';
        $allforums_chk = $DB->get_record_sql("SELECT * FROM {assign_subs_users} WHERE userid=:userid AND end_date>:end_date AND status=1", array("userid"=>$USER->id, "end_date"=>time()));
        if($allforums_chk){
            $havesubs = true;
            $current_date = date("Y-m-d");
            $end_date = date("Y-m-d", $allforums_chk->end_date);
            $sub_status = $allforums_chk->status;
            if(!empty($allforums_chk->end_date) AND ($allforums_chk->end_date > time()) AND ($sub_status == 1)){ 
                $sub_type ='<span style="color: green;">Priority</span>'; 
                $sub_type_message = '<p style="text-align: center; padding: 10px; background-color: #028090; color: #ffffff; margin-left: 20%; margin-right: 20%; border-radius: 25px;"><b>You have an active priority subscription <a href="'.$url_goto.'/index.php" style="color: #eb904c;">CLICK HERE </a> </b> to see details.</p>';
            } elseif (!empty($allforums_chk->end_date) AND ($allforums_chk->end_date<time()) AND ($sub_status == 1)){
                $sub_type_message = '<p style="text-align: center; padding: 10px; background-color: #028090; color: #ffffff; margin-left: 20%; margin-right: 20%; border-radius: 25px;"><b>To upgrade future submissions to <span style="color: #eb904c;">PRIORITY MARKING</span> and receive your feedback within 24 hours Mon-Fri, sign up <a href="'.$url_goto.'/index.php" style="color: #eb904c;">HERE</a></b></p>';
                }
        }
        $sub_typehtml = '<tr><th class="cell c0" scope="row">Submission Type</th><td class="cell c1 lastcol">'.$sub_type.'</td></tr>';

        $sub_limit_html = "";
        $remove_action_button = false;






        $course_id  = $course->id;
        // Custom
        $starttime = null;
        $endtime = null;
        $sub_duration = null;
        $sub_limit = 0;
        $count_submission = 0;

        if($row_query_current_course = $DB->get_record_sql("SELECT * FROM {assign_subs_sub_limit} WHERE course_id = :course_id AND status = 1", array("course_id"=>$course_id))){
            $sub_duration = $row_query_current_course->sub_duration;
            $sub_limit = $row_query_current_course->sub_limit;
        } else if($row_query_current_course_all = $DB->get_record_sql("SELECT * FROM {assign_subs_sub_limit} WHERE course_id = 'All Courses' AND status = 1")){
            $sub_duration = $row_query_current_course_all->sub_duration;
            $sub_limit = $row_query_current_course_all->sub_limit;
        }
        // Assignment submission timeframe
        if($sub_duration =='Weekly'){
            if(date('D')!='Mon')
            {
                $starttime = strtotime('last Monday');    
            } else {
                $starttime = strtotime(date('d F Y'));   
            }
            $endtime = strtotime('+7 day', $starttime);
        } else if($sub_duration =='Monthly'){
            $starttime = strtotime(date("01 F Y 00:00:00"));
            $endtime = strtotime(date("t F Y 23:59:59"));
        } else if($sub_duration =='Yearly'){
            $starttime = strtotime('01 January '.date( 'Y 00:00:00' ));
            $endtime = strtotime('31 December '.date( 'Y 23:59:59' ));
        }

        $removesubmission_html = '';
        if(!empty($starttime) && !empty($endtime)){
            // $count_submission = $DB->get_field_sql("SELECT count(id) FROM {assign_submission} WHERE userid=:userid AND status='submitted' AND timemodified  BETWEEN :starttime AND :endtime", array("userid"=>$USER->id, "starttime"=>$starttime, "endtime"=> $endtime));

            // $count_submission = $DB->get_records_sql("SELECT * FROM {assign_submission} WHERE assignment IN (SELECT id FROM {assign} WHERE course=:courseid) AND userid=:userid AND status='submitted' AND timemodified  BETWEEN :starttime AND :endtime", array("courseid"=>$course_id,  "userid"=>$USER->id, "starttime"=>$starttime, "endtime"=> $endtime));

            $count_submission = $DB->get_field_sql("SELECT count(id) FROM {assign_submission} WHERE assignment IN (SELECT id FROM {assign} WHERE course=:courseid) AND userid=:userid AND status='submitted' AND timemodified  BETWEEN :starttime AND :endtime", array("courseid"=>$course_id,  "userid"=>$USER->id, "starttime"=>$starttime, "endtime"=> $endtime));

            if($count_submission < $sub_limit){
                
            }else{
                $sub_limit_html .= '<p style="text-align:center;"><b>You have exceeded your limit</b></p>';
                $remove_action_button = true;
                $removesubmission_html ='$(".submithelp").remove();';
            }
        }
        $o .= '<br><br>';
        if(!empty($sub_limit)){
            $sub_limit_html .= '<p style="text-align:center;"><b>You have made '.$count_submission.' Submissions out of '.$sub_limit.' for this '.str_ireplace('ly', ' ',$sub_duration).'</b></p>';
            $removesubmission_html ='$(".submithelp").remove();';
        }
        if($iTotalRecords == 1 && $remove_action_button){
            $removesubmission_html ='$(".submissionaction").remove();';
        }




















       $html= '<script>
            setTimeout(myFunction, 700);
            function myFunction() {
                $(\''.$attemptnumberhtml.'\').insertBefore(".submissionsummarytable table>tbody>tr:nth-child(1)");
                $(\''.$sub_typehtml.'\').insertAfter(".submissionsummarytable table>tbody>tr:nth-child(3)");
                $(\''.$sub_type_message.'\').insertAfter(".submissionsummarytable");
                $(".submissionstatustable").append(\''.$sub_limit_html.'\');
                '.$removesubmission_html.'
            }
        </script>
       ';
       return $html;
   }
}
function local_assignment_subscription_before_standard_top_of_body_html() {
    $context = get_context_instance (CONTEXT_SYSTEM);
    $has_capability = has_capability('local/assignment_subscription:grade_student', $context);
    if(!$has_capability){
        return '
            <style>
                header a[href*="assignment_subscription/mark_student.php"] {
                    display: none;
                }
            </style>
        ';
    }
}