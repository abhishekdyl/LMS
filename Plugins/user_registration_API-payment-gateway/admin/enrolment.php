<?php 
require_once('../../../config.php');
global $DB, $USER, $PAGE;

$reg_id = $_POST['reg_id'];
$level = $_POST['level'];

if($DB->record_exists('lcl_individual_enrollment', array('registration_id'=>$reg_id))) {
    $name = $user_details->name;
    $course_id = $user_details->course_id;
    $userlevel = $user_details->level;
    $coursedata = $DB->get_record('course',array('id'=> $course_id));
    if($level == 1 && $userlevel <= 2) {
         $data = $DB->get_record('lcl_application_form', array('registration_id' => $reg_id));
         $userdata = create_user($data);
         $usertype = enrolCourse_individual($coursedata, $userdata['userid']);
         $upddata = new \stdclass();
         if($DB->record_exists('lcl_user_created', array('registration_id' => $reg_id))) {
            $lcl_user_created = $DB->get_record('lcl_user_created', array('registration_id' => $reg_id));
            $upddata->id = $lcl_user_created->id;
            $upddata->usertype = $usertype;
            $upddata->modified_date = time();
            $DB->update_record('lcl_user_created', $upddata);
         }
    }
    if($level == 2 && $userlevel>2) {
       $upd_reg = new \stdClass();
       $upd_reg->id = $reg_id;
       $upd_reg->payment_status = 1;
       $DB->update_record('lcl_registration', $upd_reg);
       $lcl_application_form = $DB->get_records('lcl_application_form', array('registration_id' => $reg_id));
       foreach ($lcl_application_form as $data) {
         $userdata = create_user($data);
         $usertype = enrolCourse_individual($coursedata, $userdata['userid']);
         $upddata = new \stdclass();
         if($DB->record_exists('lcl_user_created', array('registration_id' => $reg_id, 'id'=>$userdata['user_created_id']))) {
            $lcl_user_created = $DB->get_record('lcl_user_created', array('registration_id' => $reg_id, 'id'=>$userdata['user_created_id']));
            $upddata->id = $lcl_user_created->id;
            $upddata->usertype = $usertype;
            $upddata->modified_date = time();
            $DB->update_record('lcl_user_created', $upddata);
         }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot.'/local/user_registration/admin/confirm.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'reg_id='.$reg_id.'&email='.base64_encode($user_details->email));
        $response = curl_exec($ch);
        curl_close($ch);
    }
}

if($DB->record_exists('lcl_corporate_enrollment', array('registration_id'=>$reg_id))) {
    $user_details = $DB->get_record('lcl_corporate_enrollment',array('registration_id'=> $reg_id));
    $name = $user_details->client_name;
    $course_id = $user_details->course_id;
    $userlevel = $user_details->level;
    $coursedata = $DB->get_record('course',array('id'=> $course_id));
    if($level == 1 && $userlevel<=2) {
         $arr = [];
         $data = $DB->get_record('lcl_application_form', array('registration_id' => $reg_id));
         $userdata = create_user($data);
         $usertype = enrolCourse_corporate($coursedata, $userdata['userid'], $level, $arr);
         $upddata = new \stdclass();
         if($DB->record_exists('lcl_user_created', array('registration_id' => $reg_id, 'id'=>$userdata['user_created_id']))) {
            $lcl_user_created = $DB->get_record('lcl_user_created', array('registration_id' => $reg_id, 'id'=>$userdata['user_created_id']));
            $upddata->id = $lcl_user_created->id;
            $upddata->usertype = $usertype;
            $upddata->modified_date = time();
            $DB->update_record('lcl_user_created', $upddata);
         }
    }
    if($level == 2 && $userlevel>2) {
       $upd_reg = new \stdClass();
       $upd_reg->id = $reg_id;
       $upd_reg->payment_status = 1;
       $DB->update_record('lcl_registration', $upd_reg);
       $arr = corporate_create_company($user_details);
       $lcl_application_form = $DB->get_records('lcl_application_form', array('registration_id'=>$reg_id));
       foreach ($lcl_application_form as $data) {
         if ($data->relation == 'child') {
	            $userdata = create_user($data);
		        if($DB->record_exists('company_course', array('companyid'=>$arr['companyid'], 'courseid'=>$coursedata->id, 'departmentid'=>$arr['departmentid']))) {
		            $insdata = new \stdclass();
		            $insdata->companyid = $arr['companyid'];
		            $insdata->userid = $userdata['userid'];
		            $insdata->managertype = 0;
		            $insdata->departmentid = $arr['departmentid'];
		            $insdata->suspended = 0;
		            $insdata->educator = 0;
		            $DB->insert_record('company_users', $insdata);
		            enrolCourse_individual($coursedata, $userdata['userid']);
		        }
	            $usertype = "User"; 
	            $upddata = new \stdclass();
	            if($DB->record_exists('lcl_user_created', array('registration_id' => $reg_id, 'id'=>$userdata['user_created_id']))) {
	                $lcl_user_created = $DB->get_record('lcl_user_created', array('registration_id'=>$reg_id, 'id'=>$userdata['user_created_id']));
	                $upddata->id = $lcl_user_created->id;
	                $upddata->usertype = $usertype;
	                $upddata->modified_date = time();
	                $DB->update_record('lcl_user_created', $upddata);
	            }
	            $local_iomad_track_ins = new \stdClass();
	            $local_iomad_track_ins->courseid = $coursedata->id;
	            $local_iomad_track_ins->coursename = $coursedata->fullname;
	            $local_iomad_track_ins->userid = $userdata['userid'];
	            $local_iomad_track_ins->timecompleted = "";
	            $local_iomad_track_ins->timeenrolled = time();
	            $local_iomad_track_ins->timestarted = time();
	            $local_iomad_track_ins->timeexpires = "";
	            $local_iomad_track_ins->finalscore = '0.00000';
	            $local_iomad_track_ins->companyid = $arr['companyid'];
	            $local_iomad_track_ins->licenseid = 0;
	            $local_iomad_track_ins->licensename = "";
	            $local_iomad_track_ins->licenseallocated = "";
	            $local_iomad_track_ins->expirysent = "";
	            $local_iomad_track_ins->notstartedstop = 0;
	            $local_iomad_track_ins->completedstop = 0;
	            $local_iomad_track_ins->expiredstop = 0;
	            $local_iomad_track_ins->coursecleared = 0;
	            $local_iomad_track_ins->modifiedtime = time();
	            $DB->insert_record('local_iomad_track', $local_iomad_track_ins);
         }elseif($data->relation == 'parent') {
            enrolCourse_individual($coursedata, $arr['userdata']['userid']);
            $usertype = "Company manager";
            $upddata = new \stdclass();
            if($DB->record_exists('lcl_user_created', array('registration_id' => $reg_id, 'id'=>$arr['userdata']['user_created_id']))) {
                $lcl_user_created = $DB->get_record('lcl_user_created', array('registration_id' => $reg_id, 'id'=>$arr['userdata']['user_created_id']));
                $upddata->id = $lcl_user_created->id;
                $upddata->usertype = $usertype;
                $upddata->modified_date = time();
                $DB->update_record('lcl_user_created', $upddata);
            }
          }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot.'/local/user_registration/admin/confirm.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'reg_id='.$reg_id.'&email='.base64_encode($user_details->email));
        $response = curl_exec($ch);
        curl_close($ch);
    }
}

function create_user($userobj) {
    global $DB;
    $password_raw = bin2hex(random_bytes(10));
    $password = md5($password_raw);
    $name = explode(" ", $userobj->fullname);
    $lastname = array_pop($name);
    $firstname = !empty(implode(" ", $name))?implode(" ", $name):$lastname;
    $user = new \stdClass();
    $user->username = $userobj->email;
    $user->firstname = $firstname;
    $user->lastname = $lastname;
    $user->email = $userobj->email;
    $user->confirmed = 1;
    $user->mnethostid = 1;
    $user->password = $password;
    $user->timecreated = time();
    $user->created_date = time();
    $user->modified_date = time();
    $user->registration_id = $userobj->registration_id;
    $userid = $DB->insert_record('user', $user);
    $user->usertype = '';
    $user->password = $password_raw;
    $user_created_id = $DB->insert_record('lcl_user_created', $user);
    return array("userid"=>$userid, "user_created_id"=>$user_created_id);
}

function enrolCourse_individual($coursedata, $userid) {
    global $DB, $CFG, $USER;
    $query = 'SELECT * FROM {enrol} WHERE enrol = "manual" AND courseid = '.$coursedata->id;
    $enrollmentID = $DB->get_record_sql($query);
    $role = $DB->get_record('role', array('shortname'=>'student'));
    $context = $DB->get_record('context', array('contextlevel'=>50, 'instanceid'=>$coursedata->id));
    if (!$DB->record_exists('role_assignments', array('userid'=>$userid, 'contextid'=>$context->id, 'roleid'=>$role->id))) {
            $role_assignments = new \stdClass();
            $role_assignments->roleid = $role->id;
            $role_assignments->contextid = $context->id;
            $role_assignments->userid = $userid;
            $role_assignments->timemodified = time();
            $role_assignments->modifierid = $userid;
            $role_assignments->component = '';
            $role_assignments->itemid = 0;
            $role_assignments->sortorder = 0;
            $DB->insert_record('role_assignments', $role_assignments);
    }
    if (!empty($enrollmentID->id)) {
        if (!$DB->record_exists('user_enrolments', array('enrolid'=>$enrollmentID->id, 'userid'=>$userid))) {
            $userenrol = new \stdClass();
            $userenrol->status = 0;
            $userenrol->userid = $userid;
            $userenrol->enrolid = $enrollmentID->id;
            $userenrol->timestart  = $coursedata->startdate;
            $userenrol->timeend = $coursedata->enddate;
            $userenrol->modifierid  = $userid;
            $userenrol->timecreated  = time();
            $userenrol->timemodified  = time();
            $enrol_manual = enrol_get_plugin('manual');
            $enrol_manual->enrol_user($enrollmentID, $userid, $role->id, $coursedata->startdate, $coursedata->enddate);
        } else {
            $oldenroll = $DB->get_record('user_enrolments', array('enrolid'=>$enrollmentID->id, 'userid'=>$userid));
            $oldenroll->timestart = $coursedata->startdate;
            $oldenroll->timeend = $coursedata->enddate;
            $role_assignments = new \stdClass();
            $role_assignments->roleid = $role->id;
            $role_assignments->contextid = $context->id;
            if($oldenroll) {
                $insertRecords=$DB->update_record('user_enrolments', $oldenroll);
                $DB->update_record('role_assignments', $role_assignments);
            }
        }
    }
    return $role->shortname;
}

function enrolCourse_corporate($coursedata, $userid, $level, $arr) {
    global $DB;
    if($level == 1) {
        if($DB->record_exists('company_course', array('companyid'=>1, 'courseid'=>$coursedata->id, 'departmentid'=>1))) {
            $insdata = new \stdclass();
            $insdata->companyid = 1;
            $insdata->userid = $userid;
            $insdata->managertype = 0;
            $insdata->departmentid = 8;
            $insdata->suspended = 0;
            $insdata->educator = 0;
            $DB->insert_record('company_users', $insdata);
            enrolCourse_individual($coursedata, $userid);
            return 'User';
        }
    }
}

function corporate_create_company($user_details) {
    global $DB;
    $userdata = create_user($DB->get_record('lcl_application_form', array('registration_id' => $user_details->registration_id, 'relation' => 'parent')));
    $company = "New_".str_replace(" ", "", $user_details->client_name);
    $coursecat = new \stdclass();
    $coursecat->name = $company; 
    $coursecat->sortorder = 999;
    $coursecat_id = $DB->insert_record('course_categories', $coursecat);
    $coursedata = $DB->get_record('course', array('id'=>$user_details->course_id));

    $companydata = new \stdClass();
    $companydata->name = $company;
    $companydata->shortname = strtolower($company);
    $companydata->code = strtolower($company).rand();
    $companydata->city = 'city_name';
    $companydata->country = 'AF'; 
    $companydata->address = $user_details->company_address; 
    $companydata->maildisplay = 2;
    $companydata->mailformat = 1;
    $companydata->maildigest = 0;
    $companydata->maxusers = count($DB->get_records('lcl_application_form', array('registration_id' => $user_details->registration_id)));
    $companydata->autosubscribe = 1;
    $companydata->trackforums = 0;
    $companydata->htmleditor = 1;
    $companydata->screenreader = 0;
    $companydata->timezone = 99;
    $companydata->lang = "en";
    $companydata->theme = 'iomadboost';
    $companydata->category = $coursecat_id;
    $companydata->profileid = '';
    $companydata->suspended = '';
    $companydata->supervisorprofileid = 0;
    $companydata->managernotify = 0;
    $companydata->parentid  = 0;
    $companydata->ecommerce  = 0;
    $companydata->managerdigestday = 0;
    $companydata->previousroletemplateid = 0;
    $companydata->previousemailtemplateid = 0;
    $companydata->companyterminated = 0;
    $companydata->departmentprofileid = 0;
    $companyid = $DB->insert_record('company', $companydata);

    $coursedepart = new \stdclass();
    $coursedepart->name = $company; 
    $coursedepart->shortname = strtolower($company); 
    $coursedepart->company = $companyid; 
    $coursedepart->parent = 0;
    $coursedep_id = $DB->insert_record('department', $coursedepart);

    $insdata = new \stdclass();
    $insdata->companyid = $companyid;
    $insdata->userid = $userdata['userid'];
    $insdata->managertype = 1;
    $insdata->departmentid = $coursedep_id;
    $insdata->suspended = 0;
    $insdata->educator = 1;
    $DB->insert_record('company_users', $insdata);

    // Manager role
    $roledata = $DB->get_record('role', array('name' => 'Company Manager'));
    $role_id = $roledata->id;

    $role_assignments_ins = new \stdClass();
    $role_assignments_ins->roleid = $role_id;
    $role_assignments_ins->contextid = 1;
    $role_assignments_ins->userid = $userdata['userid'];
    $role_assignments_ins->timemodified = time();
    $role_assignments_ins->modifierid = $userdata['userid'];
    $role_assignments_ins->component = ''; 
    $role_assignments_ins->itemid = 0;
    $role_assignments_ins->sortorder= 0;
    $DB->insert_record('role_assignments', $role_assignments_ins);

    $context = context_course::instance($coursedata->id);
    $contextid = $context->id;

    $role_assignments_ins->contextid = $contextid;
    $DB->insert_record('role_assignments', $role_assignments_ins);

    $local_iomad_track_ins = new \stdClass();
    $local_iomad_track_ins->courseid = $coursedata->id;
    $local_iomad_track_ins->coursename = $coursedata->fullname;
    $local_iomad_track_ins->userid = $userdata['userid'];
    $local_iomad_track_ins->timecompleted = "";
    $local_iomad_track_ins->timeenrolled = time();
    $local_iomad_track_ins->timestarted = time();
    $local_iomad_track_ins->timeexpires = "";
    $local_iomad_track_ins->finalscore = '0.00000';
    $local_iomad_track_ins->companyid = $companyid;
    $local_iomad_track_ins->licenseid = 0;
    $local_iomad_track_ins->licensename = "";
    $local_iomad_track_ins->licenseallocated = "";
    $local_iomad_track_ins->expirysent = "";
    $local_iomad_track_ins->notstartedstop = 0;
    $local_iomad_track_ins->completedstop = 0;
    $local_iomad_track_ins->expiredstop = 0;
    $local_iomad_track_ins->coursecleared = 0;
    $local_iomad_track_ins->modifiedtime = time();
    $DB->insert_record('local_iomad_track', $local_iomad_track_ins);

    $company_course = new \stdclass();
    $company_course->companyid = $companyid;
    $company_course->courseid = $coursedata->id;
    $company_course->departmentid = $coursedep_id;
    $DB->insert_record('company_course', $company_course);

    return array("companyid"=>$companyid, "departmentid"=>$coursedep_id, "userdata"=>$userdata);
}
