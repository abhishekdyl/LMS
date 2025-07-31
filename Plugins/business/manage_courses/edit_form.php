<?php
require_once('../../../config.php');
global $DB, $CFG, $PAGE;
require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria_activity.php');

class course_form extends moodleform {

    function definition() {
        global $CFG;
        $mform = $this->_form; 

        $mform->disable_form_change_checker();
        $course        = $this->_customdata['course']; // this contains the data of this form
        $category      = $this->_customdata['category'];
        $editoroptions = $this->_customdata['editoroptions'];
        $returnto = $this->_customdata['returnto'];
        $returnurl = $this->_customdata['returnurl'];

        $systemcontext   = context_system::instance();
        $categorycontext = context_coursecat::instance($category->id);

        if (!empty($course->id)) {
            $coursecontext = context_course::instance($course->id);
            $context = $coursecontext;
        } else {
            $coursecontext = null;
            $context = $categorycontext;
        }
        $courseconfig = get_config('moodlecourse');
        $this->course  = $course;
        $this->context = $context;

        $mform->addElement('html', '<h3>Create/Edit Course</h3>');
      

        $mform->addElement('text','fullname', get_string('fullnamecourse'),'maxlength="254" size="50"');
        $mform->addHelpButton('fullname', 'fullnamecourse');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_TEXT);

        $choices = array();
        $choices['0'] = get_string('hide');
        $choices['1'] = get_string('show');
        $mform->addElement('select', 'visible', get_string('coursevisibility'), $choices);
        $mform->addHelpButton('visible', 'coursevisibility');
        $mform->setDefault('visible', $courseconfig->visible);


        $mform->addElement('date_time_selector', 'startdate', get_string('startdate'));
        $mform->addHelpButton('startdate', 'startdate');
        $date = (new DateTime())->setTimestamp(usergetmidnight(time()));
        // $date->modify('+1 day');
        $mform->setDefault('startdate', $date->getTimestamp());

        $mform->addElement('date_time_selector', 'enddate', get_string('enddate'), array('optional' => true));
        $mform->addHelpButton('enddate', 'enddate');
       
        $mform->addElement('editor','summary_editor', get_string('coursesummary'), null, $editoroptions);
        $mform->addHelpButton('summary_editor', 'coursesummary');
        $mform->setType('summary_editor', PARAM_RAW);
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        if ($overviewfilesoptions = course_overviewfiles_options($course)) {
            $mform->addElement('filemanager', 'overviewfiles_filemanager', get_string('courseoverviewfiles')."<p><small>Adding an image is optional.<br/> The image will appear as a banner <br/>at the top of the course page and <br/>should be 1480px wide.</small></p>", null, $overviewfilesoptions);
            $mform->addHelpButton('overviewfiles_filemanager', 'courseoverviewfiles');
        }
        /*course completion criteria*/
        if(!empty($course->id)){
            $params = array(
                'course'  => $course->id
            ); 
            $completion = new completion_info($course);
            $aggregation_methods = $completion->get_aggregation_methods();
            $activities = $completion->get_activities();
            if ($completion->is_course_locked()) {
                $mform->addElement('header', 'completionsettingslocked', get_string('completionsettingslocked', 'completion'));
                $mform->addElement('static', '', '', get_string('err_settingslocked', 'completion'));
                $mform->addElement('submit', 'settingsunlock', get_string('unlockcompletiondelete', 'completion'));
            } else if (!$completion->is_course_locked()) {
                $mform->addElement('header', 'activitiescompleted', "Course Completion Requirements");
                $mform->addElement('static', 'subheader', "", 'Select all activities that a student must complete to achieve overall Course Completion.  NOTE: This setting cannot be changed later.');
                $mform->addElement('hidden', 'updatecompletion', 1);
                $this->add_checkbox_controller(1, null, null, 0);
            }
            if (!empty($activities)) {
                foreach ($activities as $activity) {
                    $params_a = array('moduleinstance' => $activity->id);
                    $criteria = new completion_criteria_activity(array_merge($params, $params_a));
                    $criteria->config_form_display($mform, $activity);
                }
                $mform->addElement('static', 'criteria_role_note', '', get_string('activitiescompletednote', 'core_completion'));
                if (count($activities) > 1) {
                    // Map aggregation methods to context-sensitive human readable dropdown menu.
                    $activityaggregationmenu = array();
                    foreach ($aggregation_methods as $methodcode => $methodname) {
                        if ($methodcode === COMPLETION_AGGREGATION_ALL) {
                            $activityaggregationmenu[COMPLETION_AGGREGATION_ALL] = get_string('activityaggregation_all', 'core_completion');
                        } else if ($methodcode === COMPLETION_AGGREGATION_ANY) {
                            $activityaggregationmenu[COMPLETION_AGGREGATION_ANY] = get_string('activityaggregation_any', 'core_completion');
                        } else {
                            $activityaggregationmenu[$methodcode] = $methodname;
                        }
                    }
                    $mform->addElement('select', 'activity_aggregation', get_string('activityaggregation', 'core_completion'), $activityaggregationmenu);
                    $mform->setDefault('activity_aggregation', $completion->get_aggregation_method(COMPLETION_CRITERIA_TYPE_ACTIVITY));
                }
            } else {
                $mform->addElement('static', 'noactivities', '', get_string('err_noactivities', 'completion'));
            }
            if ($completion->is_course_locked()) {
                $except = array('settingsunlock', 'fullname', 'visible', 'startdate', 'enddate', 'summary_editor', 'overviewfiles_filemanager');
                $mform->hardFreezeAllVisibleExcept($except);
            }
        }
        $buttonarray[] = $mform->createElement('submit', 'saveandreturn', 'Save');
        // $buttonarray[] = $mform->createElement('submit', 'saveanddisplay',  get_string('savechangesanddisplay')); get_string('savechangesandreturn')
        $buttonarray[] = $mform->createElement('cancel', 'cancel',"Cancel");
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false); 
    }
    /**
     * Fill in the current page data for this course.
     */
    function definition_after_data() {
        global $DB;

        $mform = $this->_form;

        // Tweak the form with values provided by custom fields in use.
        // $handler  = core_course\customfield\course_handler::create();
        // $handler->instance_form_definition_after_data($mform, empty($courseid) ? 0 : $courseid);
    }

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        // Add field validation check for duplicate shortname.
        $data['shortname'] = $data['fullname'];
        if ($course = $DB->get_record('course', array('shortname' => $data['shortname']), '*', IGNORE_MULTIPLE)) {
            if (empty($data['id']) || $course->id != $data['id']) {
                $errors['shortname'] = get_string('shortnametaken', '', $course->fullname);
            }
        }

        if ($errorcode = course_validate_dates($data)) {
            $errors['enddate'] = get_string($errorcode, 'error');
        }
        return $errors;
    }
    function display_allcourses(){
        global $DB, $CFG;
        $userbrand        = $this->_customdata['userbrand'];
        $branding        = $this->_customdata['branding'];
        $category      = $this->_customdata['category'];
        $html='';
        $courses = $DB->get_records("course", array("category"=>$branding->brand_category), "id desc");
		$html .= '<h3>Course List</h3>';
        $html .= '<table id="table_filter" class="table" >
        <thead>
        ';
        $html .= '<tr>';
            $html .= '<th> ID </th>';
            $html .= '<th> '.get_string('fullnamecourse').' </th>';
            $html .= '<th> '.get_string('coursevisibility').' </th>';
            $html .= '<th> '.get_string('startdate').' </th>';
            $html .= '<th>Action</th>';
        $html .= '</tr></thead><tbody>';
        if(sizeof($courses)>0){
            foreach ($courses as $key => $course) {
                $html .= '<tr>';
                    $html .= '<td>'.$course->id.'</td>';
                    $html .= '<td><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a></td>';
                    $html .= '<td>'.($course->visible?"visible":"hidden").'</td>';
                    $html .= '<td>'.($course->startdate?date("d M Y ", $course->startdate):"").'</td>';
                    $html .= '<td>
                    <a href="'.$CFG->wwwroot.'/local/business/manage_courses/edit.php?id='.$course->id.'">Edit</a> 
                    <a href="'.$CFG->wwwroot.'/local/business/manage_courses/index.php?delete='.$course->id.'">Delete</a>
                    </td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr>';
                $html .= '<td colspan="6">No course Available yet</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
   

        echo $html;
    }
}   