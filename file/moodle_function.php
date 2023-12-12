 
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