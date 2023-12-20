<?php
require_once('../../../config.php');
require_once("$CFG->libdir/formslib.php");
global $DB, $CFG, $PAGE, $USER;
require_login();
$id=optional_param('id',0,PARAM_INT);
$userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
if(empty($userbrand)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
$branding = $DB->get_record('custom_branding', array("id"=>$userbrand->cbid));
if(empty($branding)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}

class activity_form extends moodleform {
    function definition(){
        global $CFG, $DB, $PAGE, $USER; 
        $mform = $this->_form; 
//-------------------

            $userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
            $branding = $DB->get_record('custom_branding', array("id"=>$userbrand->cbid));
            $categoryid = $branding->brand_category;
            $courses = $DB->get_records('course' , array("category"=>$categoryid));
            $allcourses=array(""=>"Select Course");
            foreach ($courses as $key => $value) {
                $allcourses[$value->id] = $value->fullname;
            }

            $sql = "SELECT u.*, cbu.status,cbu.isadmin 
            FROM mdl_user u 
             INNER JOIN mdl_user_info_data uid on uid.userid = u.id
             INNER JOIN mdl_user_info_field uif on uif.id = uid.fieldid AND uif.shortname = 'companyid'
             LEFT JOIN mdl_custom_branding_users cbu on cbu.cbid = ? and cbu.userid=u.id
            WHERE uid.data in (".$branding->company_id.") and u.id !=?
             order by u.firstname, u.lastname";
//------------------- 
            $allusers =$DB->get_records_sql($sql,array($id, $USER->id));
            $areanames = array();  
            foreach($allusers as $record){
                $areanames[$record->id] = $record->firstname." ".$record->lastname." ".$record->email;  
            }
            $options = array(                                                                                                           
            'multiple' => true,                                                  
            'noselectionstring' => "Please select user",                                                                
            );         
            $mform->addElement('autocomplete', 'userids', 'Users', $areanames, $options);


            $options = array('0' => 'Content uploaded by the clinic',
                            '1' => 'Assigned to anyone');
            $select = $mform->addElement('select', 'searchfor', 'Search for', $options);
            
            $mform->addElement('html', '<table><tr><td>');
            $mform->addElement('select', 'course', "Course",$allcourses);
            $mform->addElement('html', '</td>');
            $mform->addElement('html', '<td style="padding:0 25px;">');
            $mform->addElement('html', '<label class="" >OR</label>');
            $programs = $DB->get_records("business_learning_program",array("cbid"=>$userbrand->cbid));
            $allprograms=array(""=>"Select Program");
            foreach ($programs as $key => $value) {
                $allprograms[$value->id] = $value->program_name;
            }
            $mform->addElement('html', '</td>');
            $mform->addElement('html', '<td>');
            $mform->addElement('select', 'program', "Program", $allprograms);
            $mform->addElement('html', '</td></tr></table></div><hr/>');

            $buttonarray[] = $mform->createElement('submit', 'filter',  'Search');
            $buttonarray[] = $mform->createElement('submit', 'reset',  'Reset');
            $buttonarray[] = $mform->createElement('cancel', 'cancel',"Return");
            $mform->addGroup($buttonarray, 'buttonarr', '', ' ', false); 

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

        if(empty($data['userids'])){
            $errors['userids'] = 'Missing to select user';
        }
        if(!empty($data['course']) && !empty($data['program']) ){
            $errors['program'] = 'Please select only one either Course or Program';
            $errors['course'] = 'Please select only one either Course or Program';
        }
        if(empty($data['course']) && empty($data['program']) ){
            $errors['course'] = 'Please select one either course or program';
            $errors['program'] = 'Please select one either Course or Program';
        }
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die;

        return $errors;
    }

    function display_filterdata($filters){
        global $DB, $CFG;
        $userbrand        = $this->_customdata['userbrand'];
        $branding        = $this->_customdata['branding'];
        $brandcourse = $DB->get_fieldset_sql("select id from {course} where category=:category", array("category"=>$branding->brand_category));
        $courseids = array();
            if(!empty($filters->course)){
                $courseids = array($filters->course);
            } else if(!empty($filters->program)){
                if($program = $DB->get_record("business_learning_program", array("id"=>$filters->program))){
                    $courseids = $program->stream1courseid.','.$program->stream2courseid.','.$program->stream3courseid;
                    $courseids = str_replace(" ", '', $courseids);
                    $courseids = array_filter(explode(",", $courseids));
                    if(!$filters->searchfor){
                        $courseids = array_intersect($brandcourse,$courseids);
                    }

                }
            }
        $courses = array();
            if(!empty($courseids)){
                list($insql, $inparams) = $DB->get_in_or_equal($courseids);
                $sql = "SELECT * FROM {course} WHERE id $insql";
                $courses = $DB->get_records_sql($sql, $inparams);
            }
        $html = "";
            if(!empty($filters) && !empty($courses)){
                $html = '      <div class=" table-responsive">
                        <table class="table table-striped" id="brokers">
                            <thead>
                                <tr> 
                                    <th> First Name </th>
                                    <th> Last Name </th>
                                    <th> Email </th>';           
                foreach ($courses as $key => $course ) { 
                    $html .= '<th>'.$course->fullname.' </th>';
                }    
            $html .= '
            </tr>
            </thead>
                        <tbody>';
$bbb = $DB->get_records_sql("SELECT * FROM {user} WHERE id in(".implode(',',$filters->userids).")", array());

                            foreach ($bbb as $key => $useval ) {
                 $html .= '  <tr> 
                                <td>'.$useval->firstname.'</td>
                                <td>'.$useval->lastname.'</td>
                                <td>'.$useval->email.'</td>';
                                foreach ($courses as $val) { 
                                    $assign = $DB->get_record("business_assign_learning",array('userid'=>$useval->id, 'courseid'=>$val->id));
                                    $ccc = $DB->get_record("course_completions",array('userid'=>$useval->id, 'course'=>$val->id));
                            $html .= '<td>'.(!empty($assign->due_date)?date("d-m-Y h:i A", $assign->due_date):'N/A').'<strong> / </strong>'.(!empty($ccc->timecompleted)?date("d-m-Y h:i A", $ccc->timecompleted):'N/C').'</td>';
                                }
                            $html .= '</tr>';
                            }
                $html .= '  </tbody>
                        </table>    
                    </div> '; 

            }
                        
                    return $html;
    }
}

$args = array(
     'coursedata' =>  $coursedata,
    'userbrand' => $userbrand,
    'branding' => $branding,
);
$filterdata = "";
$mform = new activity_form(null, $args, 'get');
if($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/local/business/");
} else if(isset($_REQUEST['reset']) && !empty($_REQUEST['reset'])) {
    redirect($CFG->wwwroot."/local/business/activity_report/");
} else if ($filters=$mform->get_data()){
        // echo "<pre>";
        // print_r($filters);
        // echo "</pre>";
        // die;

    $filterdata = $mform->display_filterdata($filters);
}


echo $OUTPUT->header();
echo '<style type="text/css">.table-responsive{overflow-x: scroll;}</style>';
$mform->display();
echo $filterdata;
echo $OUTPUT->footer();