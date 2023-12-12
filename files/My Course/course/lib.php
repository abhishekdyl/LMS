<?php

/**
 * This function extends the navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param context $context The context of the course
 * @throws coding_exception
 * @throws moodle_exception
 */


function local_course_extend_navigation_course(\navigation_node $navigation, \stdClass $course, \context $context) {
    global $DB;
    $sql="SELECT c.id FROM {course} c  JOIN {enrol} e ON c.id=e.courseid WHERE e.enrol=:enrol AND customint1=:customint1 ";
    $courseid=$DB->get_field_sql($sql,array('enrol'=>'meta','customint1'=>$course->id));
    $url = new moodle_url("/course/view.php", ['id' => $courseid]);
    if($courseid){
        $navigation->add(
        "Course Forum",
        $url,
        navigation_node::TYPE_CONTAINER,
        "courseforum",
        "courseadmin",
        new pix_icon('i/report', '')
        );
    }
    $zooms = get_coursemodules_in_course("zoom", $course->id, 'm.start_time, m.start_url');
    foreach ($zooms as $key => $zoom) {
        // if($zoom->start_time > time()){
            $navigation->add(get_string('join_meeting', 'zoom'), $zoom->start_url, navigation_node::TYPE_CONTAINER, "liveclass", "liveclass", new pix_icon('i/report', '')  );
            break;
        // }
    }
}

/**
 * Add navigation nodes
 * @param navigation_node $coursenode
 * @param object $course
 * @return void
 */
function local_course_add_course_navigation(\navigation_node $coursenode, $course) {
    global $CFG, $DB;
}
function local_course_before_footer() {
    global $CFG, $DB,$USER;
    $html='<script type="text/javascript" id="hs-script-loader" async defer src="//js-na1.hs-scripts.com/19992181.js"></script>';
    
    if(!isset($_SESSION['hubspot_token']) || empty($_SESSION['hubspot_token'])){

        $token_response=get_user_indentification_token();
        $token=$token_response->token;
        $_SESSION['hubspot_token']=$token;
    }else{
        $token=$_SESSION['hubspot_token'];
    }
    $html .='<script>
        window.hsConversationsSettings = {
            loadImmediately: false,

        };
        window.hsConversationsSettings = {
            identificationEmail: "'.$USER->email.'",
            identificationToken: "'.$token.'",

        };
        window.HubSpotConversations.widget.load();
    </script>';
    echo $html;
}
function get_user_indentification_token(){
    global $USER,$CFG,$_SESSION;
    $url=get_config('local_sso', 'hubspot_base_url');
    $api_token=get_config('local_sso', 'hubspot_token');
    $curl = curl_init();
    $userdata= new stdClass();
    $userdata->email=$USER->email;
    $userdata->firstName=$USER->firstname;
    $userdata->lastName=$USER->lastname;
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url.'/conversations/v3/visitor-identification/tokens/create',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>json_encode($userdata),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Bearer '.$api_token//pat-na1-37b51654-e574-4ca6-a44c-a18e51e83d07
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response);

}


