 
 Moodle functions

1. course creation cache function.
	rebuild_course_cache($module->course, true);

2. user enroll in course by role
	enrol_try_internal_enrol($course->id, $USER->id, $CFG->creatornewroleid);

3. course create function
	create_course($formdata, $editoroptions);

4. moodle login function
	$userdata = $DB->get_record("user", array("id"=>$userinfos->id));
		if(!empty($userdata)){
			///////complete_user_login($userdata);
		    \core\session\manager::apply_concurrent_login_limit($userdata->id, session_id());
			redirect(new moodle_url($CFG->wwwroot.'/my/index.php'));
		} else {
			redirect(new moodle_url('/'));
		}

5. require_once($CFG->libdir.'/accesslib.php');

// Get the system context
$systemContext = context_system::instance();

// Assign the role to the user in the system context
role_assign($roleId, $userId, $systemContext->id);

// Unassign only roles that are added manually
role_unassign($roleid, $userId, $systemContext->id);						

//get setting content data
6. $wpurl = get_config('local_question_bank','createQuestionSubject'); 

7.
// ---------------get global question id---------------
list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) = question_edit_setup('questions', '/local/question_bank/index.php');
list($categoryid, $contextid) = explode(',', $pagevars['cat']);
// ---------------require jquery---------------
$PAGE->requires->jquery();
// ---------------page url function ---------------
$url = new moodle_url('/local/question_bank');
$PAGE->set_url($url);
// ---------------plugin set context---------------
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);

8. 
// ---------------user profile function---------------
	user_create_user($usernew, false, false);
	user_update_user($upuser, false, false);
    profile_save_data($upuser);