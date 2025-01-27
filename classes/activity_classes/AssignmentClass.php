<?php

require_once('CustomClass.php');

class AssignmentClass {

    public $username;
    public $moduleid;
    public $textsubmission;
    public $filesubmission;
    public $filename;
    public $activityid;
    public $courseid;


    // Get assignment details
    public function get_assignment_details($courseid, $username, $activityid)
    {
        //$validate_token = $this->validate_token();
        global $CFG, $DB;
        $fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u 
        INNER JOIN {external_tokens} AS e ON e.userid = u.id  
        WHERE (u.email = ? OR u.username = ?)";
        $get_user = $DB->get_record_sql($fetch_user, array($username, $username));
        $userid = $get_user-> id;
        $token = $get_user->token;

        $curl = curl_init();
		curl_setopt_array($curl, [
		CURLOPT_URL => $CFG->wwwroot."/webservice/rest/server.php?wsfunction=mod_assign_get_assignments&wstoken=".$token."&moodlewsrestformat=json&moodlewsrestformat=json&courseids[0]=".$courseid,
		CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_SSL_VERIFYPEER=> false,
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",]);

		$responses = curl_exec($curl);
		curl_close($curl);
        $decodes = json_decode($responses);


        $assignments = $decodes->courses[0]->assignments;
        $key = array_search($activityid, array_column($assignments, 'cmid'));
        $topicdata = $assignments[$key];
        

        return $topicdata;
      
    }




    // Get submission status
    public function get_submission_status($assignid, $username)
    {
        //$validate_token = $this->validate_token();
        global $CFG, $DB;
        $fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u 
        INNER JOIN {external_tokens} AS e ON e.userid = u.id  
        WHERE (u.email = ? OR u.username = ?)";
        $get_user = $DB->get_record_sql($fetch_user, array($username, $username));
        $userid = $get_user-> id;
        $token = $get_user->token;

        $curl = curl_init();
		curl_setopt_array($curl, [
		CURLOPT_URL => $CFG->wwwroot."/webservice/rest/server.php?wsfunction=mod_assign_get_submission_status&wstoken=".$token."&moodlewsrestformat=json&moodlewsrestformat=json&assignid=".$assignid."&userid=".$userid,
		CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_SSL_VERIFYPEER=> false,
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",]);

		$responses = curl_exec($curl);
		curl_close($curl);
        $decodes = json_decode($responses);
        
        return $decodes;
    }


    // Final upload file
    public function assignsubmission($username, $moduleid, $textsubmission, $filesubmission, $filename) {
        
        //$validate_token = $this->validate_token();

        global $CFG, $DB, $PAGE;
        $fetch_user = "SELECT u.id, u.email, u.username, e.token  FROM {user} AS u 
        INNER JOIN {external_tokens} AS e ON e.userid = u.id  
        WHERE (u.email = ? OR u.username = ?)";
        $get_user = $DB->get_record_sql($fetch_user, array($username, $username));
        $userid = $get_user->id;
        $token  = $get_user->token;

        $returndata = array("status"=>0, "message"=>"Token required");
        if($token){
            if($get_user = $DB->get_record_sql($fetch_user, array($username, $username))){
                if(empty($textsubmission) && empty($filesubmission) && empty($filename)){
                    $returndata['message'] = "Submission data is missing";
                } else {
                    $query_course_module = $DB->get_record('course_modules', array('id' => $moduleid));
                    $query_context = $DB->get_record('context', array('instanceid' => $moduleid, 'contextlevel'=>'70'));
                    if(!empty($query_course_module)){
                        $assign_id = $query_course_module->instance;
                        $module_id = $query_course_module->module;
                        $query_assign = $DB->get_record('assign', array('id' => $assign_id));
                        if(!empty($query_assign)){
                            
                            $query_submission = $DB->get_record('assign_submission', array('assignment' => $assign_id, 'userid'=>$userid));
                            $have_submitted=0;
                            $assign_sid = 0;
                            if(!empty($query_submission)){
                                $assign_sid = $query_submission->id;
                                if($query_submission->status == "submitted"){
                                    $have_submitted = 1;
                                }
                                $query_submissionfile = $DB->get_records('files', array('contextid' => $query_context->id, 'itemid'=>$query_submission->id));
                                if(!empty($query_submissionfile)){
                                    $fileobj = new stdClass();
                                    foreach ($query_submissionfile as $key => $submissionfile) {
                                        if($submissionfile->filename != "."){
                                            $fileobj = $submissionfile;
                                            $have_submitted=1;
                                        }
                                    }
                                } else {
                                    $returndata['status'] = 0;
                                    $returndata['message'] = "Assign file already avaialable";
                                }
                            }
                            if($assign_sid == 0){
                                $record2 = new stdClass();
                                $record2->assignment= $assign_id;
                                $record2->userid = $userid;
                                $record2->timecreated = time();
                                $record2->status = 'new';
                                $record2->latest = 1;
                                $assign_sid = $DB->insert_record('assign_submission', $record2, true);                      
                            }
                            if($have_submitted){
                                $returndata['status'] = 0;
                                $returndata['message'] = "Assign already Submitted";
                            } else {
                                $returndata['message'] = "Failed to submit Assigment";
                                if(!empty($filesubmission) && !empty($filename)){
                                    $fs = get_file_storage();
                                    $wsfiledata_decoded = base64_decode($filesubmission);
                                    $fileinfo = array(
                                        'contextid' => $query_context->id, // ID of context
                                        'component' => 'assignsubmission_file',
                                        'filearea' => 'submission_files',     // usually = table name
                                        'itemid' => $assign_sid,               // usually = ID of row in table
                                        'filepath' => "/",           // any path beginning and ending in /
                                        'filename' => $filename); // any filename
                                    $fs->create_file_from_string($fileinfo, $wsfiledata_decoded);
                                    $returndata['status'] = 1;
                                    $updatetime = time();
                                    $message = "Assign file submitted";
                                    $rec_update=new stdClass();
                                    $rec_update->id= $assign_sid;
                                    $rec_update->timemodified = time();
                                    $rec_update->status = 'submitted';
                                    $DB->update_record('assign_submission', $rec_update, false);
                                    $returndata['status'] = 1;
                                    $returndata['message'] = "successfully";
                                    if($lastfilesubmission = $DB->get_record('assignsubmission_file', array('assignment' => $assign_id, 'submission'=>$assign_sid))){

                                    } else {
                                        $assignfilesubmission = new stdClass();
                                        $assignfilesubmission->assignment = $assign_id;
                                        $assignfilesubmission->submission = $assign_sid;
                                        $assignfilesubmission->numfiles = 1;
                                        $DB->insert_record('assignsubmission_file', $assignfilesubmission, false);
                                    }
                                    $returndata['status'] = 1;
                                    $returndata['message'] = "Assign Submitted";
                                }
                                if(!empty($textsubmission)){
                                    if($textedalready = $DB->get_record("assignsubmission_onlinetext", array('assignment' => $assign_id, 'submission'=>$assign_sid))){
                                        $textedalready->onlinetext=$textsubmission;
                                        $DB->update_record("assignsubmission_onlinetext", $textedalready);
                                    } else {
                                        $testsubmission = new stdClass();
                                        $testsubmission->assignment=$assign_id;
                                        $testsubmission->submission=$assign_sid;
                                        $testsubmission->onlinetext=$textsubmission;
                                        $testsubmission->onlineformat=1;
                                        $DB->insert_record("assignsubmission_onlinetext", $testsubmission);
                                    }
                                    $returndata['status'] = 1;
                                    $returndata['message'] = "Assign Submitted";
                                }
                            }
                        } else {
                            $returndata['message'] ="Assign not found";
                        }
                    } else {
                        $returndata['message'] ="module not found";
                    }
                }
            } else {
                $returndata["message"]="Invalid session";
            }
        }
        return $returndata;
    }



     
}
