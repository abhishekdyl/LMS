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
class assign_learning extends moodleform {
    function definition() {
        global $CFG, $DB, $PAGE, $USER; 
        $mform = $this->_form; 
        $userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
        $branding = $DB->get_record('custom_branding', array("id"=>$userbrand->cbid));
        $categoryid = $branding->brand_category;
        $courses = $DB->get_records('course' , array("category"=>$categoryid));
        $allcourses=array(""=>"Select Course");
        foreach ($courses as $key => $value) {
            $allcourses[$value->id] = $value->fullname;
        }
        $mform->addElement('html', '<h3>Assign Learning</h3><p>Complete the 3 steps below to assign Learning to any students. You may select from a single course or a learning program. Then define a due date for the assigned learning and then select the students you wish to be assigned. To complete click the button "Assign to selected users" to save your choices.</p> <hr/>');  
        //$mform->addElement('html', '<div class="span10"></div>');
        //$mform->addElement('button', 'sumbit1', 'Assign to selected users');
        $mform->addElement('html', '<div style="padding:0 35px;"><h4><strong>Step 1)</strong> Select a single Course OR entire Learning Program</h4>');
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

        $mform->addElement('html', '<div style="padding:0 35px;"><h4><strong>Step 2)</strong> Select due date</h4>');
        $mform->addElement('date_selector', 'assesstimefinish', 'Completion due');
        $mform->addElement('html', '<hr/></div>');
                    $allusers = array();
                    $sql = "SELECT u.*, cbu.status,cbu.isadmin 
                    FROM {user} u 
                    INNER JOIN {user_info_data} uid on uid.userid = u.id
                    INNER JOIN {user_info_field} uif on uif.id = uid.fieldid AND uif.shortname = 'companyid'
                    LEFT JOIN {custom_branding_users} cbu on cbu.cbid = ? and cbu.userid=u.id
                    WHERE uid.data in (".$branding->company_id.") and u.id !=?
                    order by u.firstname, u.lastname";
                    $allusers =array_values($DB->get_records_sql($sql,array($userbrand->cbid, $USER->id)));
                    // echo"<pre>"; // print_r($allusers);// echo"<pre>";
                    function custom_manipulate_getuserrolename($userid){
                        global $CFG, $DB;
                        $roleid = "";
                        $existing_roles = $DB->get_records_sql("select r.* from {role_assignments} as ra inner join {role} as r on ra.roleid = r.id where ra.roleid in (9,10,12,17,18,19,20,21, 22) and ra.userid = ".$userid);
                        foreach ($existing_roles as $key => $roles) {
                            $roleid = $roles->name;
                        }
                        return $roleid;
                    }

                    $tbl = '';
                $selecteduser = isset($_POST['users'])?$_POST['users']:array();
            foreach ($allusers as $value) {
                $tbl .= ' <tr>
                            <td><input type="checkbox" name="users[]" '.(in_array($value->id, $selecteduser)? 'checked':'').' value="'.$value->id.'"/></td>
                            <td>'.$value->firstname.' '.$value->lastname.'</td>
                            <td style="word-break: break-all;">'.$value->email.'</td>
                            <td>'.custom_manipulate_getuserrolename($value->id).'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>';
            }

        $mform->addElement('html', '<div style="padding:0 35px;"><h4><strong>Step 3)</strong> Select students</h4>
        <div style="width:100%;overflow-x:auto;">');
        $mform->addElement('html', '<table class="table name="tbl" table-stripped">
        <tr>
          <th></th>
          <th>Firstname/Surname</th>
          <th>Email Address</th>
          <th>Role</th>
          <th>Groups</th>
          <th>Last access to course</th>
          <th>Status</th>
        </tr>
         '.$tbl.'
      </table></div></div>');
      $buttonarray[] = $mform->createElement('submit', 'submitbutton', "Assign to selected users");
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
        if(empty($data['course']) && empty($data['program']) ){
            $errors['program'] = 'Missing to select atleast one them (Course OR program) ';
        }
        if(!empty($data['course']) && !empty($data['program']) ){
            $errors['program'] = 'Please select only one either Course or Program';
        }
        if($data['assesstimefinish'] <= time()){
            $errors['assesstimefinish'] = 'Please select future date';
        }
        if(empty($_POST['users']) && empty($errors['assesstimefinish'])){
            $errors['assesstimefinish'] = 'Please select atleast one user';
        }
        // if(empty($data->users)){
        //     $errors['submitbutton'] = 'Please! select the particular IDs ';
            
        // }
        
        // $errors['course'] = 'Missing Select course';
        // $errors['program'] = 'Missing Select Program';
        return $errors;
    }
    function assignlearningcourse($coursedata, $learningtime, $userid){
        global $DB;

        $userbrand = $this->_customdata['userbrand'];

        $dataobject = new stdClass();
        $dataobject->name =$coursedata->fullname;
        $dataobject->description ='<p>My learning plan for <a href="'.$CFG->wwwroot.'/course/view.php?id='.$coursedata->id.'">'.$coursedata->fullname.'</a></p>';
        $dataobject->format =1;
        $dataobject->learning_course =$coursedata->id;
        $dataobject->userid =$userid;
        $dataobject->modulename =0;
        $dataobject->eventtype ="user";
        $dataobject->timestart =$learningtime;
        $dataobject->timemodified =time();
        $dataobject->learning_record =1;
        $dataobject->reminded =1;

        $data = new stdclass();
        $data->cbid = $userbrand->cbid;///
        $data->courseid =$coursedata->id; ///
        $data->userid =$userid; ///
        $data->due_date =$learningtime;  ///

       $aa = $DB->insert_record("event", $dataobject, true, false);
       $bbb = $DB->insert_record("business_assign_learning", $data);
    }
}

$args = array(
    'userbrand' => $userbrand,
);

$mform = new assign_learning(null, $args);
if($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/local/business/");
} else if ($data=$mform->get_data()){
    $users = $_POST['users'];
    $course = $data->course;
    $program = $data->program;
    $learningtime = $data->assesstimefinish;
    $courses = array();

    if(!empty($course)){
        $courses[]=$course;
    } else {
        $programs = $DB->get_record("business_learning_program",array("cbid"=>$userbrand->cbid , "id"=> $data->program));
        $cour = $programs->stream1courseid.','.$programs->stream2courseid.','.$programs->stream3courseid;
        $courses = array_unique(array_filter(explode(",",$cour)));
    }
    foreach ($courses as $key => $courseid) {
        if($coursedata = $DB->get_record("course", array("id"=>$courseid))){
            foreach ($users as $key => $userid) {
                if($DB->record_exists("user", array("id"=>$userid))){
                    $mform->assignlearningcourse($coursedata, $learningtime, $userid);
                }
            }
        }
    }
    redirect($CFG->wwwroot.'/local/business/assign_learning/', 'Course assigned Successfully', null, \core\output\notification::NOTIFY_SUCCESS);
}
echo $OUTPUT->header();
echo '<style>.mform .fitem .felement {margin-left:0px;}.form-group.row.fitem {border:0px;} .form-group.row.fitem {margin:0;padding:0;}</style>';
echo "<a href='$CFG->wwwroot/local/business/'><button>Back</button></a>";
$mform->display();
echo $OUTPUT->footer();
