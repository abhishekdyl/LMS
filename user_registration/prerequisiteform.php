<?php
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');
global $DB, $USER, $PAGE;
$PAGE->requires->jquery();
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
echo '<style>.form-control-static { width: 100% !important; }</style>';
class prerequisite_form extends moodleform {
// Define the form
function definition() {
    global $CFG, $DB, $USER;    
    $id = $this->_customdata['id'];
    $reg_id = base64_decode($id);
    $url = $this->_customdata['url'];
    $mform =& $this->_form; 
    $attr = $mform->getAttributes();
    $attr['enctype'] = "multipart/form-data";
    $mform->setAttributes($attr);
    
    if($DB->record_exists('lcl_corporate_enrollment', array('registration_id' => $reg_id))) {
        $data = $DB->get_record('lcl_corporate_enrollment', array('registration_id' => $reg_id));
        $course_id = $data->course_id;
        $enrollment_id = $data->id;
        $level = $data->level;
        
    }

    if($DB->record_exists('lcl_individual_enrollment', array('registration_id' => $reg_id))) {
        $data = $DB->get_record('lcl_individual_enrollment', array('registration_id' => $reg_id));
        $course_id = $data->course_id;
        $enrollment_id = $data->id;
        $level = $data->level;

    }

    // Start here
    $prerequisite = $DB->get_record('customfield_field', array('shortname' => 'prerequisite'));
    $customdata_prerequisite = $DB->get_record('customfield_data', array('fieldid' => $prerequisite->id, 'instanceid' => $course_id));
    if($customdata_prerequisite->value == 2) {  
    

    // fileupload
    $fileupload = $DB->get_record('customfield_field', array('shortname' => 'fileupload')); 
    $customdata_fileupload = $DB->get_record('customfield_data', array('fieldid' => $fileupload->id, 'instanceid' => $course_id));
	    if($customdata_fileupload->value == 1) {
	        $prerequisitetype_fileupload = $DB->get_record('customfield_field', array('shortname' => 'prerequisitetype_fileupload'));
	        $customdata_prerequisitetype_fileupload = $DB->get_record('customfield_data', array('fieldid' => $prerequisitetype_fileupload->id, 'instanceid' => $course_id)); 
	            $typename_fileupload = strip_tags($customdata_prerequisitetype_fileupload->value);
	            $typename_fileupload = explode("\n", $typename_fileupload);
	    }


    // prerequisitetype_askquestion_text_field
    $askquestion_text_field = $DB->get_record('customfield_field', array('shortname' => 'askquestion_text_field')); 
    $customdata_askquestion_text_field = $DB->get_record('customfield_data', array('fieldid' => $askquestion_text_field->id, 'instanceid' => $course_id));
		if($customdata_askquestion_text_field->value == 1) {
		    $prerequisitetype_askquestion_text_field = $DB->get_record('customfield_field', array('shortname' => 'prerequisitetype_askquestion_text_field'));
		    $customdata_prerequisitetype_askquestion_text_field = $DB->get_record('customfield_data', array('fieldid' => $prerequisitetype_askquestion_text_field->id, 'instanceid' => $course_id)); 
		        $typename_askquestion_text_field = strip_tags($customdata_prerequisitetype_askquestion_text_field->value);
		        $typename_askquestion_text_field = explode("\n", $typename_askquestion_text_field);
		}


    // prerequisitetype_askquestion_text_area
    $askquestion_text_area = $DB->get_record('customfield_field', array('shortname' => 'askquestion_text_area')); 
    $customdata_askquestion_text_area = $DB->get_record('customfield_data', array('fieldid' => $askquestion_text_area->id, 'instanceid' => $course_id));
		if($customdata_askquestion_text_area->value == 1) {
		    $prerequisitetype_askquestion_text_area = $DB->get_record('customfield_field', array('shortname' => 'prerequisitetype_askquestion_text_area'));
		    $customdata_prerequisitetype_askquestion_text_area = $DB->get_record('customfield_data', array('fieldid' => $prerequisitetype_askquestion_text_area->id, 'instanceid' => $course_id)); 
		        $typename_askquestion_text_area = strip_tags($customdata_prerequisitetype_askquestion_text_area->value);
		        $typename_askquestion_text_area = explode("\n", $typename_askquestion_text_area);
		}



    // askquestion_single_selection
	$askquestion_single_selection = $DB->get_record('customfield_field', array('shortname' => 'askquestion_single_selection')); 
    $customdata_askquestion_single_selection = $DB->get_record('customfield_data', array('fieldid' => $askquestion_single_selection->id, 'instanceid' => $course_id));
		if($customdata_askquestion_single_selection->value == 1) {
		    $prerequisitetype_askquestion_single_selection = $DB->get_record('customfield_field', array('shortname' => 'prerequisitetype_askquestion_single_selection'));
		    $customdata_prerequisitetype_askquestion_single_selection = $DB->get_record('customfield_data', array('fieldid' => $prerequisitetype_askquestion_single_selection->id, 'instanceid' => $course_id)); 
		        $typename_askquestion_single_selection = strip_tags($customdata_prerequisitetype_askquestion_single_selection->value);
		        $typename_askquestion_single_selection = explode("\n", $typename_askquestion_single_selection);
		}



    // askquestion_multiple_selection
	$askquestion_multiple_selection = $DB->get_record('customfield_field', array('shortname' => 'askquestion_multiple_selection')); 
    $customdata_askquestion_multiple_selection = $DB->get_record('customfield_data', array('fieldid' => $askquestion_multiple_selection->id, 'instanceid' => $course_id));
		if($customdata_askquestion_multiple_selection->value == 1) {
		    $prerequisitetype_askquestion_multiple_selection = $DB->get_record('customfield_field', array('shortname' => 'prerequisitetype_askquestion_multiple_selection'));
		    $customdata_prerequisitetype_askquestion_multiple_selection = $DB->get_record('customfield_data', array('fieldid' => $prerequisitetype_askquestion_multiple_selection->id, 'instanceid' => $course_id)); 
		        $typename_askquestion_multiple_selection = strip_tags($customdata_prerequisitetype_askquestion_multiple_selection->value);
		        $typename_askquestion_multiple_selection = explode("\n", $typename_askquestion_multiple_selection);
		}

	// personalinfo
    $personalinfo = $DB->get_record('customfield_field', array('shortname' => 'personalinfo')); 
    $customdata_personalinfo = $DB->get_record('customfield_data', array('fieldid' => $personalinfo->id, 'instanceid' => $course_id));
        if($customdata_personalinfo->value == 1) {
            $prerequisitetype_personalinfo = $DB->get_record('customfield_field', array('shortname' => 'prerequisitetype_personalinfo'));
            $customdata_prerequisitetype_personalinfo = $DB->get_record('customfield_data', array('fieldid' => $prerequisitetype_personalinfo->id, 'instanceid' => $course_id)); 
                $typename_personalinfo = strip_tags($customdata_prerequisitetype_personalinfo->value);
                $typename_personalinfo = explode("\n", $typename_personalinfo);
        }
    }






    // typename_fileupload
    if($typename_fileupload) {
        $mform->addElement('html', '<h2>File Upload</h2>');
        $mform->addElement('html', '<link rel="stylesheet" href="'.$CFG->wwwroot.'/local/user_registration/style3.css"></link>');
        $count_fileupload=0;
        foreach($typename_fileupload as $key => $value) {
            $mform->addElement('hidden', "field_fileupload$key", $value);
            $mform->addElement('static', 'file_text', "", $value);
            $mform->addElement('static', 'drop_drag', "", '<div class="box">
                                           <i class="fa fa-arrow-circle-o-down fa-3x m-2"></i>
                                           <label>
                                            <span>You can drag and drop files here to add them.</span>
                                                <input class="box_file" type="file" name="content'.$key.'" required/>
                                            </label>
                                           <div class="file-list"></div>
                                        </div>');
            $count_fileupload++;
            $mform->addElement('static', 'file_required', "", '<span class="text-danger"><i class="fa fa-info-circle"></i> Required</span>');
        }
        $mform->addElement('html', '<script src="'.$CFG->wwwroot.'/local/user_registration/js/custom-drop-drag.js"></script>');
        $mform->addElement('hidden', 'prerequisitetype[]', 'fileupload');
    }


    // typename_askquestion_text_field
    if($typename_askquestion_text_field) {
        $mform->addElement('html', '<h2><br></h2>');
        foreach($typename_askquestion_text_field as $key => $value) {
            $mform->addElement('hidden', "field_textfield$key", $value);
            $mform->addElement('static', 'ask_question', "", $value);
            $mform->addElement('text', "question_textfield$key", '');
            $mform->addRule("question_textfield$key", '', 'required', null, 'client');
        }
        $mform->addElement('hidden', 'prerequisitetype[]', 'askquestion_text_field');
    }


    // typename_askquestion_text_area
    if($typename_askquestion_text_area) {
        $mform->addElement('html', '<h2><br></h2>');
        foreach($typename_askquestion_text_area as $key => $value) {
            $mform->addElement('hidden', "field_textarea$key", $value);
            $mform->addElement('static', 'ask_question', "", $value);
            $mform->addElement('textarea', "question_textarea$key", '');
            $mform->addRule("question_textarea$key", '', 'required', null, 'client');
        }
        $mform->addElement('hidden', 'prerequisitetype[]', 'askquestion_text_area');
    }


    // typename_askquestion_single_selection
    if($typename_askquestion_single_selection) {
        $mform->addElement('html', '<h2><br></h2>');
        foreach($typename_askquestion_single_selection as $key => $value) {
            $mform->addElement('hidden', "field_questionradio$key", $value);
        	$mform->addElement('radio', 'questionradio', '', $value, $value, '');
        }
        $mform->addElement('static', 'ask_question', "", '<span class="text-danger"><i class="fa fa-info-circle"></i> Required</span>');
        $mform->addElement('hidden', 'prerequisitetype[]', 'askquestion_single_selection');
    }


    // typename_askquestion_multiple_selection
    if($typename_askquestion_multiple_selection) {
        $mform->addElement('html', '<h2><br></h2>');
        foreach($typename_askquestion_multiple_selection as $key => $value) {
            $mform->addElement('hidden', "field_question_multiple$key", $value);
        	$mform->addElement('advcheckbox', "question_multiple$key", '', $value, array(), array(0,$value));
        }
        $mform->addElement('static', 'ask_question', "", '<span class="text-danger"><i class="fa fa-info-circle"></i> Required</span>');
        $mform->addElement('hidden', 'prerequisitetype[]', 'askquestion_multiple_selection');
    }


	// typename_personalinfo
    if($typename_personalinfo) {
        $mform->addElement('html', '<h2>Personal Info</h2>');
        foreach($typename_personalinfo as $key => $value) {
            $mform->addElement('hidden', "field_personal_info$key", $value);
            $mform->addElement('static', 'personal_info', "", $value);
            $mform->addElement('textarea', "personal_info$key", '');
            $mform->addRule("personal_info$key", '', 'required', null, 'client');
        }
        $mform->addElement('hidden', 'prerequisitetype[]', 'personalinfo');
    }

    	$mform->addElement('hidden', 'id', $id);    
    	$mform->addElement('hidden', 'url', $url);    
    	$mform->addElement('hidden', 'course_id', $course_id);    
    	$mform->addElement('hidden', 'enrollment_id', $enrollment_id);    
    	$mform->addElement('hidden', 'level', $level);    
    	$mform->addElement('hidden', 'typename_fileupload', json_encode($typename_fileupload)); 
    	$mform->addElement('hidden', 'typename_askquestion_text_field', json_encode($typename_askquestion_text_field));    
    	$mform->addElement('hidden', 'typename_askquestion_text_area', json_encode($typename_askquestion_text_area));    
    	$mform->addElement('hidden', 'typename_askquestion_single_selection', json_encode($typename_askquestion_single_selection));    
    	$mform->addElement('hidden', 'typename_askquestion_multiple_selection', json_encode($typename_askquestion_multiple_selection));    
		$mform->addElement('hidden', 'typename_personalinfo', json_encode($typename_personalinfo));    
    
    	$this->add_action_buttons(false, "Submit");
    	// End Here

  }
}



