<?php
require_once('../../../config.php');
require_once("$CFG->libdir/formslib.php");
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
        $date->modify('+1 day');
        $mform->setDefault('startdate', $date->getTimestamp());

        $mform->addElement('date_time_selector', 'enddate', get_string('enddate'), array('optional' => true));
        $mform->addHelpButton('enddate', 'enddate');
       
        $mform->addElement('editor','summary_editor', get_string('coursesummary'), null, $editoroptions);
        $mform->addHelpButton('summary_editor', 'coursesummary');
        $mform->setType('summary_editor', PARAM_RAW);
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        if ($overviewfilesoptions = course_overviewfiles_options($course)) {
            $mform->addElement('filemanager', 'overviewfiles_filemanager', get_string('courseoverviewfiles')."<p><small>Adding an image is optional.<br/> The image will appear as a banner <br/>at the top of the course pageand <br/>should be 1480px wide.</small></p>", null, $overviewfilesoptions);
            $mform->addHelpButton('overviewfiles_filemanager', 'courseoverviewfiles');
        }
        $buttonarray[] = $mform->createElement('submit', 'saveandreturn', get_string('savechangesandreturn'));
        $buttonarray[] = $mform->createElement('submit', 'saveanddisplay',  get_string('savechangesanddisplay'));
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
        $courses = $DB->get_records("course", array("category"=>$branding->brand_category));
		$html .= '<h3>Course List</h3>';
        $html .= '<table class="table">';
        $html .= '<tr>';
            $html .= '<th> ID </th>';
            $html .= '<th> '.get_string('fullnamecourse').' </th>';
            $html .= '<th> '.get_string('coursevisibility').' </th>';
            $html .= '<th> '.get_string('startdate').' </th>';
            $html .= '<th> '.get_string('enddate').' </th>';
            $html .= '<th>Action</th>';
        $html .= '</tr>';
        if(sizeof($courses)>0){
            foreach ($courses as $key => $course) {
                $html .= '<tr>';
                    $html .= '<td>'.$course->id.'</td>';
                    $html .= '<td><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a></td>';
                    $html .= '<td>'.($course->visible?"visible":"hidden").'</td>';
                    $html .= '<td>'.($course->startdate?date("d F Y h:i A", $course->startdate):"").'</td>';
                    $html .= '<td>'.($course->enddate?date("d F Y h:i A", $course->enddate):"N/A").'</td>';
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
        $html .= '</table>';
        echo $html;
    }
}   