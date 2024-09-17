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
require_once($CFG->libdir.'/accesslib.php');
$brandingcontext = context_coursecat::instance($branding->brand_category);
$roleid = $DB->get_field_sql("select id from {role} where shortname=?", array("companyadmin"));
if(empty($roleid)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
if(!user_has_role_assignment($USER->id, $roleid, $brandingcontext->id)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
class assign_learning extends moodleform {
    function definition() {
        global $CFG, $DB, $PAGE, $USER; 
        $mform = $this->_form; 
        $userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
        $branding = $DB->get_record('custom_branding', array("id"=>$userbrand->cbid));
        $categoryid = $branding->brand_category;
        $courses = $DB->get_records_sql('SELECT c.* FROM mdl_course as c WHERE c.visible = 1 and (availablefor like \'%"vet":"1"%\' or availablefor like \'%"vetnurse":"1"%\' or c.category=:category ) ORDER BY c.fullname ASC' , array("category"=>$categoryid));
        $allcourses=array(""=>"Select Course");
        foreach ($courses as $key => $value) {
            $allcourses[$value->id] = $value->fullname;
        }
        $mform->addElement('html', '<h3>Assign Learning</h3><p>Complete the 3 steps below to assign Learning to any students. You may select from a single course or a learning program. Then define a due date for the assigned learning and then select the students you wish to be assigned. To complete click the button "Assign to selected users" to save your choices.</p> <hr/>');  
        //$mform->addElement('html', '<div class="span10"></div>');
        //$mform->addElement('button', 'sumbit1', 'Assign to selected users');
        $mform->addElement('html', '<div style="padding:0 35px;"><h4><strong>Step 1)</strong> Select a single Course OR entire Learning Program</h4>');
		$mform->addElement('html', '<table><tr style="vertical-align: bottom;"><td>');
        $options = array(
            'multiple' => false
        );         
		$mform->addElement('autocomplete', 'course', "Course", $allcourses, $options);
		$mform->addElement('html', '</td>');
		$mform->addElement('html', '<td style="padding:0 25px;">');
    $mform->addElement('html', '<label class="" >OR</label>');
    $programs = $DB->get_records("business_learning_program",array("cbid"=>$userbrand->cbid));
    $allprograms=array(""=>"Select Program", "cat_175.176.177"=>"Puppy School Masterclass", "cat_134"=>"Dental Technical Advisor", "cat_133"=>"Parasitology Technical Advisor", "cat_165"=>"Pain Management Program");
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
                    $sql = "SELECT u.*, cbu.status,cbu.isadmin, r.shortname as roleshortname, r.name as rolename, CONCAT(cl.name, ' - ', cl.address) as companyname
                    FROM {user} u 
                    INNER JOIN {user_info_data} uid on uid.userid = u.id
                    INNER JOIN {user_info_field} uif on uif.id = uid.fieldid AND uif.shortname = 'companyid'
                    INNER JOIN {company_list} cl ON cl.id = uid.data
                    LEFT JOIN {custom_branding_users} cbu on cbu.cbid = ? and cbu.userid=u.id
                    LEFT JOIN {role_assignments} as ra on ra.userid = u.id and ra.roleid > 8
                    LEFT join {role} as r on ra.roleid = r.id
                    WHERE uid.data in (".$branding->company_id.") and u.id !=?
                    order by u.firstname, u.lastname";
                    $allusers =array_values($DB->get_records_sql($sql,array($userbrand->cbid, $USER->id)));
                    // echo"<pre>"; // print_r($allusers);// echo"<pre>";
                    $tbl = '';
                $selecteduser = isset($_POST['users'])?$_POST['users']:array();
            foreach ($allusers as $value) {
                $tbl .= ' <tr>
                            <td><input type="checkbox" data-role="'.$value->roleshortname.'" name="users[]" '.(in_array($value->id, $selecteduser)? 'checked':'').' value="'.$value->id.'"/></td>
                            <td>'.$value->firstname.'</td>
                            <td>'.$value->lastname.'</td>
                            <td>'.$value->companyname.'</td>
                            <td>'.$value->rolename.'</td>
                        </tr>';
            }

        $mform->addElement('html', '<div style="padding:0 35px;"><h4><strong>Step 3)</strong> Select students</h4>');
        $mform->addElement('static', 'description', "Select  &nbsp; &nbsp; &nbsp;",'<a href="javascript:void(0);" onclick="select_allstudents(\'\')">All staff</a> &nbsp; &nbsp; &nbsp;<a href="javascript:void(0);" onclick="select_allstudents(\'vetnurse\')">All nurses</a> &nbsp; &nbsp; &nbsp;<a href="javascript:void(0);" onclick="select_allstudents(\'vet\')">All vets</a><br><br>');
        $mform->addElement('static', 'description', "Clear  &nbsp; &nbsp; &nbsp;",'<a href="javascript:void(0);" onclick="clear_allstudents(\'\')">All Selection</a><br><br>');
        $mform->addElement('html', '<div style="display:flex;"><input type="text" id="search_allstudents" onkeyup="Filter_allstudents()" style="background-image: url(\'../images/searchicon.png\'); background-position: 10px 12px; background-repeat: no-repeat; width: 100%; font-size: 16px; padding: 12px 20px 12px 40px; border: 1px solid #ddd; margin-bottom: 12px;" placeholder="Search for Students.."></div><div style="width:100%;overflow-x:auto;"><table id="allstudents" class="table name="tbl" table-stripped"><thead>
        <tr>
          <th></th>
          <th style="cursor: pointer;" onclick = sortallstudentsTable(1)>Firstname</th>
          <th style="cursor: pointer;" onclick = sortallstudentsTable(2)>Surname</th>
          <th style="cursor: pointer;" onclick = sortallstudentsTable(3)>Clinic</th>
          <th style="cursor: pointer;" onclick = sortallstudentsTable(4)>Role</th>
        </tr></thead>
         '.$tbl.'
      </table></div></div>
<script>
function Filter_allstudents() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("search_allstudents");
  filter = input.value.toUpperCase();
  table = document.getElementById("allstudents");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    txtValue = tr[i].textContent || tr[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
    } else {
        tr[i].style.display = "none";
    }
  }
}
function select_allstudents(role) {
  var input, table, tr;
  table = document.getElementById("allstudents");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    input = tr[i].getElementsByTagName("input")[0];
    if(input && (role == "" || input.dataset.role == role)){
        input.checked = true;
    }
  }
}
function clear_allstudents() {
  var input, table, tr;
  table = document.getElementById("allstudents");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    input = tr[i].getElementsByTagName("input")[0];
    if(input ){
        input.checked = false;
    }
  }
}
function sortallstudentsTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("allstudents");
  switching = true;
  //Set the sorting direction to ascending:
  dir = "asc"; 
  /*Make a loop that will continue until
  no switching has been done:*/
  while (switching) {
    //start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /*Loop through all table rows (except the
    first, which contains table headers):*/
    for (i = 1; i < (rows.length - 1); i++) {
      //start by saying there should be no switching:
      shouldSwitch = false;
      /*Get the two elements you want to compare,
      one from current row and one from the next:*/
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /*check if the two rows should switch place,
      based on the direction, asc or desc:*/
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          //if so, mark as a switch and break the loop:
          shouldSwitch= true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          //if so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /*If a switch has been marked, make the switch
      and mark that a switch has been done:*/
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      //Each time a switch is done, increase this count by 1:
      switchcount ++;      
    } else {
      /*If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again.*/
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>
      ');
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
        global $DB, $CFG;

        $userbrand = $this->_customdata['userbrand'];

        $dataobject = new stdClass();
        $dataobject->name =$coursedata->fullname;
        $dataobject->description ='<p><a href="'.$CFG->wwwroot.'/course/view.php?id='.$coursedata->id.'">View Course</a></p>';
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
      if(strpos($program, "cat_") !== false){
        $categoryids = explode(".", str_replace("cat_", "", $program));
        $catid = implode(",",$categoryids);
        $sql = "SELECT * FROM {course} WHERE `category` IN ($catid) and visible=1";
        $courseid = $DB->get_records_sql($sql,array()); 
        $courses = [];
        foreach ($courseid as $key) {
          array_push($courses,$key->id);
        }
      } else {
        $programs = $DB->get_record("business_learning_program",array("cbid"=>$userbrand->cbid , "id"=> $data->program));
        $cour = $programs->stream1courseid.','.$programs->stream2courseid.','.$programs->stream3courseid;
        $courses = array_unique(array_filter(explode(",",$cour)));
      }
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
echo '<style>.mform .fitem .felement {margin-left:0px;}.form-group.row.fitem {border:0px;} .form-group.row.fitem {margin:0;padding:0;}#fitem_id_course .col-md-9 { float: left; } #fitem_id_course { align-items: flex-end; display: flex; }</style>';
echo "<a href='$CFG->wwwroot/local/business/'><button>Back</button></a>";
$mform->display();
echo $OUTPUT->footer();
