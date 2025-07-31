<?php
require_once('../../../config.php');
require_once("$CFG->libdir/formslib.php");
class learning_pro extends moodleform {
    function definition() {
        global $CFG, $DB, $PAGE, $USER; 
        $mform = $this->_form; 
        $mform->addElement('static', 'headding', 'Create New Learning Program');
        // $mform->addElement('html', 'Create New Learning Program');
        $mform->addElement('text', 'program_name', 'Learning program name:', 'maxlength="500" size="30" placeholder="Please enter program name"');
        $mform->addRule('program_name', 'Missing Learning program name', 'required', 'client');
        $mform->setType('program_name', PARAM_MULTILANG);
        
        $mform->addElement('filemanager', 'mainimage', 'Main Image', null,
        array('subdirs' => 0, 'maxbytes' => $maxbytes, 'areamaxbytes' => $maxbytes, 'maxfiles' => 1,'accepted_types' => array('image'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL));
        
        $mform->addElement('html', '<div class="inputmsg">');
        $mform->addElement('textarea', 'introduction_message', 'Introduction message');
        $mform->addElement('html', '</div>');
        // $mform->addElement('editor', 'introduction_message', 'Introduction message', array("enable_filemanagement"=>false, 'autosave' => false));
        // $mform->setType('introduction_message', PARAM_RAW);

        $mform->addElement('html', '<div id="fitem_id_towers" class="form-group row  fitem">
            <div class="col-md-3">
                <label class="col-form-label d-inline " for="id_title1">
                    Course streams
                </label><br/>
                <small>Add a list of the course ID numbers<br/>
                    that should appear in this stream<br/>
                    (maximum 15 courses per stream).<br/>

                    numbers. Click here for help on<br/>
                    locating course ID numbers</small>
            </div>
            <div class="col-md-9 form-inline felement">
        ');

        $mform->addElement('html', '<div class="list-column">');
            $mform->addElement('text', 'stream1title','Stream #1 Title', 'size="30" rows="20" cols="50"');
            $mform->addRule('stream1title', 'Missing Stream #1 Title', 'required', 'client');
            $mform->addElement('textarea', 'stream1courseid','Stream #1 Course ID\'s', ' rows="5"');
            $mform->addRule('stream1courseid', 'Missing Stream #1 Course ID\'s', 'required', 'client');
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '<div class="list-column">');
            $mform->addElement('text', 'stream2title','Stream #2 Title', 'size="30" rows="20" cols="50"');
            $mform->addElement('textarea', 'stream2courseid','Stream #2 Course ID’s', 'rows="5"');
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '<div class="list-column">');
            $mform->addElement('text', 'stream3title','Stream #3 Title', ' size="30" rows="20" cols="50"');
            $mform->addElement('textarea', 'stream3courseid','Stream #3 Course ID’s', 'rows="5"');
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '</div></div>');
        $mform->addElement('hidden', 'id');

        $buttonarray[] = $mform->createElement('submit', 'submitbutton', "Save");
        $buttonarray[] = $mform->createElement('cancel', 'cancel',"Cancel");
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false); 
        
    }  

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    function validation($data, $files) {
        global $DB, $USER;
        $errors = array();
        $userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
        $branding = $DB->get_record('custom_branding', array("id"=>$userbrand->cbid));
        $categoryid = $branding->brand_category;
        $totalcourseids = $DB->get_fieldset_sql("SELECT id FROM {course} WHERE category = :category", array("category" => $categoryid));
        if(empty($data['program_name'])){
            $errors['program_name'] = 'Missing Learning program name';
        }
        if(empty($data['stream1title'])){
            $errors['stream1title'] = 'Missing Stream #1 Title';
        }
        
        $stream1courseid = str_replace(" ", "",$data['stream1courseid']);
        $stream2courseid = str_replace(" ", "",$data['stream2courseid']);
        $stream3courseid = str_replace(" ", "",$data['stream3courseid']);
        if(empty($stream1courseid)){
            $errors['stream1courseid'] = 'Missing Stream #1 Course ID\'s';
        }
        else {
            $usercoid = (explode(",",$stream1courseid));
            list($insql, $inparams) = $DB->get_in_or_equal(explode(",", $stream1courseid));
            $validdata = $DB->get_records_sql('SELECT cl.* FROM {course} cl WHERE cl.id '.$insql.' ', $inparams);
            $result=array_diff($usercoid,array_keys($validdata));
            if(!empty($result)){
                $errors['stream1courseid'] = implode(",",$result).' ID\'s are not valid courseid';
            }
        }
        if(!empty($stream2courseid)){
            $usercoid = (explode(",",$stream2courseid));
            list($insql, $inparams) = $DB->get_in_or_equal(explode(",", $stream2courseid));
            $validdata = $DB->get_records_sql('SELECT cl.* FROM {course} cl WHERE cl.id '.$insql.' ', $inparams);
            $result=array_diff($usercoid,array_keys($validdata));
            if(!empty($result)){
                $errors['stream2courseid'] = implode(",",$result).' ID\'s are not valid courseid';
            }
        }
        if(!empty($stream3courseid)){
            $usercoid = (explode(",",$stream3courseid));
            list($insql, $inparams) = $DB->get_in_or_equal(explode(",", $stream3courseid));
            $validdata = $DB->get_records_sql('SELECT cl.* FROM {course} cl WHERE cl.id '.$insql.' ', $inparams);
            $result=array_diff($usercoid,array_keys($validdata));
            if(!empty($result)){
                $errors['stream3courseid'] = implode(",",$result).' ID\'s are not valid courseid';
            }
        }

        return $errors;
    }
    function display_allprograms(){
        global $DB, $CFG;
        $userbrand        = $this->_customdata['userbrand'];
        $branding        = $this->_customdata['branding'];
        $html='';
        $programs = $DB->get_records("business_learning_program", array("cbid"=>$branding->id));
        $html .= '<table class="table">';
        $html .= '<tr>';
            $html .= '<th> ID </th>';
            $html .= '<th> Program Name </th>';
            $html .= '<th> Copy link </th>';
            $html .= '<th> Stream1 Title </th>';
            $html .= '<th> Stream2 Title </th>';
            $html .= '<th> Stream3 Title </th>';
            $html .= '<th>Action</th>';
        $html .= '</tr>';
        if(sizeof($programs)>0){
            foreach ($programs as $key => $program) {
                $html .= '<tr>';
                    $html .= '<td>'.$program->id.'</td>';
                    $html .= '<td><a href="'.$CFG->wwwroot.'/local/business/learning_program.php?id='.$program->id.'">'.$program->program_name.'</a></td>';
                    $html .= '<td><a onclick="copytoclipboard(\''.$CFG->wwwroot.'/local/business/learning_program.php?id='.$program->id.'\')" href="javascript:void(0);" class="btn">Copy link</a></td>';
                    $html .= '<td>'.$program->stream1title.'</td>';
                    $html .= '<td>'.$program->stream2title.'</td>';
                    $html .= '<td>'.$program->stream3title.'</td>';
                    $html .= '<td><a href="'.$CFG->wwwroot.'/local/business/learning_program/edit.php?id='.$program->id.'">Edit</a></td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr>';
                $html .= '<td colspan="6">No course Available yet</td>';
            $html .= '</tr>';
        }
        $html .= '</table><script>function copytoclipboard(link) {
            navigator.clipboard.writeText(link);
          }
          </script>';
        echo $html;
    }
}    