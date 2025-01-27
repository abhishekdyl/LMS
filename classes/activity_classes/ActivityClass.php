<?php 

require_once 'QuizClass.php';
include 'AssignmentClass.php';

Class ActivityClass {


    public $username;
	public $courseid;
	public $token;


    // get_course_topic
	public function get_course_topic($courseid, $username)
	{
		//$validate_token = $this->validate_token();
		global $CFG, $DB;
        $has_subtopic = false;
		$fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  
		WHERE (u.email = ? OR u.username = ?) AND externalserviceid = 1";
		$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
		$userid = $get_user-> id;
		$token = $get_user->token;
        $parent = null;
        $APIManager = new APIManager();
        $get_coursetopics = $APIManager->get_coursetopics($courseid, $parent);


		return  $get_coursetopics;

	}


    // get_course_subtopic
	public function get_course_subtopic($courseid, $username, $parent)
	{
		//$validate_token = $this->validate_token();
		global $CFG, $DB;
        $has_modules = false;
		$fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  
		WHERE (u.email = ? OR u.username = ?) AND externalserviceid = 1";
		$get_user = $DB->get_record_sql($fetch_user, array($username, $username));
		$userid = $get_user-> id;
		$token = $get_user->token;

        if(!empty($parent)){
        $APIManager = new APIManager();
        $get_coursetopics = $APIManager->get_coursetopics($courseid, $parent);
        return  $get_coursetopics;
        }else{
        return  array();
        exit;
        }	
	}



    // get_course_activity
	public function get_course_activity($courseid, $username, $topicid)
	{
        
		//$validate_token = $this->validate_token();
		global $CFG, $DB;
        if($topic = $DB->get_record("course_sections", array("id" => $topicid))){

            $fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  
            WHERE (u.email = ? OR u.username = ?) AND externalserviceid = 1";
            $get_user = $DB->get_record_sql($fetch_user, array($username, $username));
            $userid = $get_user-> id;
            $token = $get_user->token;



            if($get_user = $DB->get_record_sql($fetch_user, array($username, $username))){

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot.'/webservice/rest/server.php?moodlewsrestformat=json');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded',
                ]);
        
                $param = array("courseid" => $courseid);
                curl_setopt($ch, CURLOPT_POSTFIELDS, 'courseid='.$courseid.'&wsfunction=core_course_get_contents&wstoken='.$token.'&moodlewssettingfilter=true');
                $response = curl_exec($ch);
                curl_close($ch);

                // var_dump($response);
                // die;
                
                $all_activity = json_decode($response);
                $key = array_search($topicid, array_column($all_activity, 'id'));

                if($key !== false) {
                    $topicdata = $all_activity[$key];
                    if($topicdata) {
                        $objects = [];
                        foreach($topicdata->modules as $key => $value) {
                            $url = $value->contents[0]->fileurl;
                            $parsedUrl = parse_url($url);
                            $value->isXr = 0;
                            if (strpos($parsedUrl['query'], "xr") !== false) {
                                $value->isXr = 1;
                            }
                            $objects[] = $value;
                        }
                    }
                    return $topicdata->modules;
                }
                
            }
        }

        return array();
	}




    // Activity details
	public function get_activity_details($courseid, $activityid, $username) {
        
		//$validate_token = $this->validate_token();
		global $CFG, $PAGE, $DB, $OUTPUT;

        $fetch_user = "SELECT u.id, u.email, u.username, u.secret, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  
        WHERE (u.email = ? OR u.username = ?) AND externalserviceid = 1";
        $get_user = $DB->get_record_sql($fetch_user, array($username, $username));
        $userid = $get_user-> id;
        $secret = $get_user-> secret;
        $token = $get_user->token;

        if($get_user = $DB->get_record_sql($fetch_user, array($username, $username))) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot.'/webservice/rest/server.php?moodlewsrestformat=json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            ]);
    
            $param = array("courseid" => $courseid);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'courseid='.$courseid.'&wsfunction=core_course_get_contents&wstoken='.$token.'&moodlewssettingfilter=true');
            $response = curl_exec($ch);
            curl_close($ch);
            $all_activity = json_decode($response);

            // return $all_activity; 

            $returned_data = [];
            foreach ($all_activity as $key => $value) {
                $contents = array_search($activityid, array_column($all_activity[$key]->modules, 'id'));
                if($contents !== false){
                     $topicdata = $all_activity[$key]->modules[$contents];
                    array_push($returned_data, array($topicdata)); 
                }
            }

            
            $completiondata = $returned_data[0][0]->completiondata->details[0]->rulevalue->description;
            if(empty($returned_data)){ return []; }else{
            $id = $return_manual['id'] = $returned_data[0][0]->instance;
            $modname = $return_manual['modname'] = $returned_data[0][0]->modname;
            $moduleid = $return_manual['cmid'] = $returned_data[0][0]->id;
            $return_manual['name'] = $returned_data[0][0]->name;
            $return_manual['modicon'] = $returned_data[0][0]->modicon;
            $return_manual['instance'] = $returned_data[0][0]->instance;
            $contents_fileurl = $returned_data[0][0]->contents[0]->fileurl;
            $filename = $returned_data[0][0]->contents[0]->filename;
            $mimetype = $returned_data[0][0]->contents[0]->mimetype;
            
            $instance = $returned_data[0][0]->instance;

            $return_manual['uservisible'] = $returned_data[0][0]->uservisible;
            $return_manual['availabilityinfo'] = strip_tags($returned_data[0][0]->availabilityinfo).". ".$completiondata;
            // $return_manual['completiondata'] = $returned_data[0][0]->contents[0]->mimetype;



            $substr_count = substr_count($contents_fileurl,"webservice/pluginfile.php");
            
            if(!empty($contents_fileurl) && $substr_count == 1){ 
                $return_manual['fileurl'] = $contents_fileurl."&token=".$token; 
               
            }else{  
                $return_manual['fileurl'] = $contents_fileurl; 
               
            }
            
            $fetch_intro = "SELECT  * FROM {{$modname}} WHERE (id = ?)";
            $get_intro = $DB->get_record_sql($fetch_intro, array($instance));
            

            switch ($modname) {


                case 'hvp': 

                        $id = $returned_data[0][0]->id;
                        $cm = get_coursemodule_from_id('hvp', $id);

                        if($cm->idnumber){
                            $idnumber = $cm->idnumber;
                            $dimention = explode("*",$idnumber);
                            if(isset($dimention)) {
                                $width = $dimention[0];
                                $height = $dimention[1];
                            }
                        }

                        if(!empty($width) && !empty($height)){
                            $viewOutput = '<iframe style="border: 1px solid #000 !important; overflow: scroll !important;" src="'.$CFG->wwwroot.'/local/userdetails/h5paccess.php?id='.$cm->id.'&token='.$token.'" frameborder="1" allowfullscreen="allowfullscreen" title="'.$cm->name.'" width="'.$width.'" height="100%" scrolling="yes"></iframe>';
                        } else {
                            $viewOutput = '<iframe src="'.$CFG->wwwroot.'/local/userdetails/h5paccess.php?id='.$cm->id.'&token='.$token.'" frameborder="0" allowfullscreen="allowfullscreen" title="'.$cm->name.'"></iframe>';
                        }
                        

                        $return_manual['display'] = 'embed';
                        $return_manual['filetype'] = 'html';
                        $return_manual['fileurl'] = "";
                        $return_manual['iframeurl'] = $CFG->wwwroot.'/local/userdetails/h5paccess.php?id='.$cm->id.'&token='.$token;
                        $return_manual['filecontent'] = $viewOutput;
                        $return_manual['description'] = '';
                       

                break;
                case 'h5pactivity': 

                        require_once($CFG->libdir . '/filelib.php');
                        require_once($CFG->dirroot . '/lib/filestorage/file_storage.php');
                        $id = $returned_data[0][0]->id;
                        
                        list ($course, $cm) = get_course_and_cm_from_cmid($id, 'h5pactivity');


                        // Instantiate player.
                        $fs = get_file_storage();
                        $files = $fs->get_area_files($returned_data[0][0]->contextid, 'mod_h5pactivity', 'package', 0, 'id', false);
                        $file = reset($files);

                        if($file) {
                           $fileurl = moodle_url::make_pluginfile_url(
                                $file->get_contextid(),
                                $file->get_component(),
                                $file->get_filearea(),
                                $file->get_itemid(),
                                $file->get_filepath(),
                                $file->get_filename()
                            );
                            $fileurl_string = $fileurl->out();
                        }
                        
                        $viewOutput = '<iframe src="'.$CFG->wwwroot.'/local/userdetails/h5paccess.php?url='.$fileurl_string.'&id='.$cm->id.'&token='.$token.'" frameborder="1" allowfullscreen="allowfullscreen" title="'.$cm->name.'"></iframe>';

                        $return_manual['display'] = 'embed';
                        $return_manual['filetype'] = 'html';
                        $return_manual['fileurl'] = "";
                        $return_manual['filecontent'] = $viewOutput;
                        $return_manual['description'] = '';


                break;
                case 'label':

                        $filecontent = $returned_data[0][0]->description;
                        $return_manual['display'] = 'embed';
                        $return_manual['filetype'] = 'html';
                        $return_manual['fileurl'] = "";
                        $return_manual['filecontent'] = $filecontent;
                        $return_manual['description'] = '';

                break;
                case 'lesson':

                        $return_manual['display'] = 'embed';
                        $return_manual['filetype'] = 'lesson'; 
                        $lessonid = $returned_data[0][0]->instance;
                        $get_lesson_pages = $DB->get_records_sql("SELECT * FROM {lesson_pages} WHERE lessonid='$lessonid'", array());
                        
                        $lesson_contents = '';
                        if($get_lesson_pages) {

                            foreach($get_lesson_pages as $value) {

                            $get_lesson_answer = $DB->get_record_sql("SELECT * FROM {lesson_answers} WHERE lessonid='$value->lessonid' AND pageid='$value->id'", array());


                                if($get_lesson_answer) {
                                    $lesson_answer = '<p><a herf="index.html" style="text-decoration: underline; color: blue;">'.$get_lesson_answer->answer.'</a></p>';
                                }


                                   $lesson_contents .= strip_tags($value->title);

                                   $lesson_contents .= '<div style="text-align: justify;">';
                                                        if(!empty($value->contents)) {
                                   $lesson_contents .= strip_tags($value->contents, "<b><a><img>");
                                                        } else {
                                   $lesson_contents .= $lesson_answer;
                                                        }
                                   $lesson_contents .= '</div>';
                                   $lesson_contents .= '</br>';
                                   
                            }
                                $lesson_contents .= '</br><br></br><br>';
                          

                        }


                        $return_manual['filecontent'] =  $lesson_contents; 
                        $return_manual['fileurl'] = "";

                break;
                case 'page':

                        $return_manual['display'] = 'embed';
                        $return_manual['filetype'] = 'html'; 
                        
                         
                        $filecontent = '';
                        foreach ($returned_data[0][0]->contents as  $key => $value) {

                            if (!empty($value->filename) && $value->filename == 'index.html') { 
                                $fileurl = $value->fileurl."&token=".$token;
                                $file_get_contents = file_get_contents($fileurl);
                                $filecontent .= "<div style='text-align: justify !important;'>".strip_tags($file_get_contents)."</div>"; 
                                $return_manual['fileurl'] = "";
                            }

                            if (!empty($value->mimetype)){
                                $fileurl = $value->fileurl."&token=".$token;
                                $filecontent .= '<img src="'.$fileurl.'" alt="Girl in a jacket" width="500" height="600">'; 
                                
                            } 

                        }

                        $return_manual['filecontent'] = $filecontent;



                break;
                case 'resource':

                    if ($mimetype == "video/mp4") {
                        $return_manual['display'] = 'embed';
                        $return_manual['filetype'] = 'video'; 
                    
                    } else if ($mimetype == "application/pdf") {
                        $return_manual['display'] = 'embed';
                        $return_manual['filetype'] = 'pdf';
                    
                    } else if ($mimetype == "application/vnd.openxmlformats-officedocument.presentationml.presentation") {
                        $return_manual['display'] = 'embed';
                        $return_manual['filetype'] = 'ppt';

                    }

                break;
                case 'url':
                    $fetch_intro_url = "SELECT  * FROM {{$modname}} WHERE (id = ?) AND (display != ?)";
                    $get_intro_url = $DB->get_record_sql($fetch_intro_url, array($instance, 1));

                    if($get_intro_url){ $return_manual['display'] = 'open'; }else{ $return_manual['display'] = 'embed'; }
                    $introo = html_to_text($get_intro->intro);
                    $return_manual['description'] = str_replace("\n", "", $introo);
                    $return_manual['fileurl'] = $CFG->wwwroot.'/local/userdetails/externalaccess.php?url='.$return_manual['fileurl'].'&token='.$token;

                  

                break;
                case 'quiz':
                    $quiz = $DB->get_record("quiz", array("id"=>$id));
                    require_once($CFG->dirroot.'/mod/quiz/locallib.php');
                    require_once($CFG->dirroot.'/mod/quiz/lib.php');
                    $started = false;
                    $canattempt = false;
                    $attempts = array_values(quiz_get_user_attempts($id, $userid, 'all', true));
                    $key = array_search("inprogress", array_column($attempts, 'state'));
                    if($key !== false){
                        $started = true;
                        $canattempt = true;
                    } else if($quiz->attempts ==0 ){
                        $canattempt = true;
                    } else if($quiz->attempts < sizeof($attempts)){
                        $canattempt = true;
                    }
                    $return_manual['attemptsummary'] = $attempts;
                    $return_manual['started'] = $started;
                    $return_manual['canattempt'] = $canattempt;
                    $return_manual['timelimit'] = $quiz->timelimit;
                    $return_manual['attempts'] = $quiz->attempts;
                    $return_manual['sumgrades'] = $quiz->sumgrades;
                    $return_manual['grade'] = $quiz->grade;
                    

                break;
                case 'assign':
                    $issubmitted = false; $iseditable = false; 
                    $AssignmentClass_obj = new AssignmentClass();

                    if($get_submission_status = $AssignmentClass_obj->get_submission_status($instance, $username)){
                        $status = $get_submission_status->lastattempt->submission->status; 
                        
                        $canedit = $get_submission_status->lastattempt->canedit;
                        $gradingstatus = $get_submission_status->lastattempt->gradingstatus;
                        $lastmodified = $get_submission_status->lastattempt->submission->timemodified;

                        if($status == 'submitted'){ 
                            $issubmitted = true;
                            $subid = $get_submission_status->lastattempt->submission->id; 

                            $query = 'SELECT s.id, ass.name, f.filename, f.mimetype, f.contextid, f.component, f.filearea, f.itemid, f.filepath, aso.onlinetext FROM mdl_assign_submission s
                            INNER JOIN mdl_user as u on u.id = s.userid
                            LEFT JOIN mdl_assign ass on ass.id = s.assignment
                            LEFT JOIN mdl_assignsubmission_file asf on asf.submission = s.id AND asf.assignment = s.assignment
                            LEFT JOIN mdl_assignsubmission_onlinetext aso on aso.submission = s.id AND aso.assignment = s.assignment
                            LEFT JOIN mdl_files f on f.itemid = asf.submission and f.component="assignsubmission_file" AND f.filearea="submission_files" and f.filesize > 0
                            WHERE s.assignment = '.$instance.' AND s.status="submitted" AND s.id = '.$subid.'';
                            $assignUser = $DB->get_record_sql($query);

                            $fs = get_file_storage();
                            $onlinetext = $assignUser->onlinetext;
                            $filename = $assignUser->filename;
                            $mimetype = $assignUser->mimetype;
                            $contextid = $assignUser->contextid;
                            $component = $assignUser->component;
                            $filearea = $assignUser->filearea;
                            $itemid = $assignUser->itemid;
                            $filepath = $assignUser->filepath;
                            $files = $fs->get_area_files($contextid, $component, $filearea, $itemid, false); 

                            // echo "<pre>";
                            // print_r($files);
                            // die;
                            $file_url = '';
                            foreach ($files as $file) {
                            // $file->copy_content_to($CFG->dataroot . '/temp/'. $file->get_filename());
                                if($file->get_filename($filename) !='.'){
                                    $file_url = moodle_url::make_pluginfile_url($file->get_contextid($contextid), $file->get_component($component), $file->get_filearea($filearea), $file->get_itemid($itemid), $file->get_filepath($filepath), $file->get_filename($filename), false);
                                }
                            }
                            

                        }
                        if($canedit === true){ $iseditable = true; }
                    }

                   

                    if($get_assignment_details = $AssignmentClass_obj->get_assignment_details($courseid, $username, $moduleid)){
                        $topicdata_final = $get_assignment_details->configs;
                        
                        $startdate = $get_assignment_details ->allowsubmissionsfromdate;
                        $duedate = $get_assignment_details ->duedate;
                        $plugin_type = array_column($topicdata_final, 'plugin');
                        $introattachments = $get_assignment_details->introattachments[0]->fileurl."?token=".$token;
                        $hasOnlinetext = false;
                        $hasFile = false;

                        foreach($plugin_type as $value){
                        if($value == 'onlinetext'){ $hasOnlinetext =  true; }
                        if($value == 'file'){ $hasFile =  true; }
                        }

                        if($hasOnlinetext){ $assignment_type = 'text'; }
                        if($hasFile){ $assignment_type = 'file'; }
                        if($hasOnlinetext && $hasFile){ $assignment_type = 'file & text'; }

                    }
                    

                    $return_manual['imageurl'] = $introattachments;
                    $return_manual['submissiondetails']=array("issubmitted" => $issubmitted, "iseditable" => $iseditable, "startdate" => $startdate, "duedate" => $duedate,  "gradingstatus" => $gradingstatus, "assignmenttype" => $assignment_type, "lastmodified" => $lastmodified, "userassignuploaded" => array("onlinetext" => $onlinetext, "fileurl" => "$file_url"));
                    

                break;
                case 'videotime':
                    $return_manual['fileurl']= $get_intro->vimeo_url;
                break;
                            
                default:
                    # code...
                break;
            }
            $return_manual['summary'] = strip_tags($get_intro->intro);
            
            return $return_manual;
        
        }
    }
        
        return array();

	}




    public static function get_completion_status($courseid, $username, $lessonid, $starttime, $endtime, $modname, $cmid) {

        global $CFG, $USER, $DB;

        $fetch_user = "SELECT u.*, e.token  FROM {user} AS u INNER JOIN {external_tokens} AS e ON e.userid = u.id  
        WHERE (u.email = ? OR u.username = ?) AND externalserviceid = 1";
        $get_user = $DB->get_record_sql($fetch_user, array($username, $username));
        $userid = $get_user-> id;


        require_once($CFG->dirroot. '/mod/lesson/locallib.php');
        $USER = $get_user; 

        $course = $DB->get_record('course', array('id'=>$courseid));
        $course_modules = $DB->get_record_sql("SELECT * FROM {course_modules} WHERE id=$cmid", array());

        
        $completion = new completion_info($course);
        $completion->set_module_viewed($course_modules);


        $fetch_lesson = $DB->get_record('lesson', array('id'=>$lessonid));
        $fetch_timer_details = $DB->get_record('lesson_timer', array('lessonid'=>$lessonid, 'userid' => $userid));



        $dataobject = new \stdClass();
        $dataobject->coursemoduleid = $course_modules->id;
        $dataobject->userid = $userid;
        $dataobject->timecreated = time();
        $dataobject->lessontime = $endtime; 
        $dataobject->completionstate = 1;
        $dataobject->overrideby = null; 
        $dataobject->timemodified = time();



        if (!($DB->record_exists('course_modules_viewed', array("userid"=>$userid, "coursemoduleid"=>$course_modules->id)))) {
          $DB->insert_record('course_modules_viewed', $dataobject);
        }

        if (!($DB->record_exists('course_modules_completion', array("userid"=>$userid, "coursemoduleid"=>$course_modules->id)))) {
          $DB->insert_record('course_modules_completion', $dataobject);
        }


        
        $dataobject->id = $fetch_timer_details->id; 

        if($fetch_timer_details) {


            $returnid = $DB->update_record('lesson_timer', $dataobject);

            $time_difference = ($fetch_timer_details->lessontime-$fetch_timer_details->starttime);
            if($time_difference >= $fetch_lesson->completiontimespent){



                // Update completion state.
                // $cm = get_coursemodule_from_instance('lesson', $lessonid, $courseid, false, MUST_EXIST);
                $cm = $DB->get_record('course_modules', array('id'=>$course_modules->id));
                $course = get_course($cm->course);
                // print_r($cm);
                // die;

                $completion = new completion_info($course);
                if ($completion->is_enabled($cm) && $fetch_lesson->completiontimespent > 0) {
                $completion->update_state($cm, COMPLETION_COMPLETE);
                }

                
            }

        } else {

                if($fetch_lesson && $modname=='lesson') {

                    $dataobject = new \stdClass();
                    $dataobject->lessonid = $lessonid;
                    $dataobject->userid = $userid;
                    $dataobject->starttime = $starttime;
                    $dataobject->lessontime = $endtime; 
                    $dataobject->completed = 0;
                    $dataobject->timemodifiedoffline = 0;
                    $returnid = $DB->insert_record('lesson_timer', $dataobject);

                } else {

                    return 0;
                    die;

                }

        }


        return 1;

        

    }





    
}
