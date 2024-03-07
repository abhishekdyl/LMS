<?php
require_once('../../config.php');
global $DB, $CFG, $PAGE, $USER;
require_once($CFG->dirroot . '/mod/zoom/lib.php');
require_once($CFG->dirroot . '/mod/zoom/locallib.php');
require_once($CFG->dirroot . '/mod/zoom/classes/webservice.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once('../../group/lib.php');
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/course/lib.php');

/*date_default_timezone_set( date_default_timezone_get() );*/
function local_manage_course_zoom_get_user_id($user,$required = true) {
  $cache = cache::make('mod_zoom', 'zoomid');
  if (!($zoomuserid = $cache->get($user->id))) {
      $zoomuserid = false;
      try {
          $zoomuser = zoom_get_user(zoom_get_api_identifier($user));
          if ($zoomuser !== false && isset($zoomuser->id) && ($zoomuser->id !== false)) {
              $zoomuserid = $zoomuser->id;
              $cache->set($user->id, $zoomuserid);
          }
      } catch (moodle_exception $error) {
          if ($required) {
              throw $error;
          }
      }
  }
  return $zoomuserid;
}

$PAGE->requires->jquery();
require_login();
$id = optional_param('id', 0, PARAM_INT); //groupid
$courseid = optional_param('courseid', 0, PARAM_INT);
$coursecontext = context_course::instance($courseid);

if (!is_siteadmin() && empty(zoom_get_user_id())) {
  echo "You do not have permission to access this page.";
  redirect($CFG->wwwroot . "/local/manage_course/groups.php?id=" . $courseid . "");
}

if (is_siteadmin()) {
  $query = 'SELECT DISTINCT u.* FROM {role_assignments} ra INNER JOIN {role} r ON ra.roleid = r.id INNER JOIN {user} u ON ra.userid = u.id WHERE r.shortname = "editingteacher"';
  $teacher = $DB->get_records_sql($query);
  $tname = [];
  foreach ($teacher as $key) {
    $key->name = $key->firstname . " " . $key->lastname;
    array_push($tname, $key->lastname);
  }
  $teachname = array_column($teacher, 'name', 'id');
}

if ($delete) {
  debugging('Deleting a group through group/group.php is deprecated and will be removed soon. Please use group/delete.php instead');
  redirect(new moodle_url('delete.php', array('courseid' => $courseid, 'groups' => $id)));
}


if ($courseid) {
  if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    throw new \moodle_exception('invalidcourseid');
  }
  $group = new stdClass();
  $group->courseid = $course->id;
}

if ($id) {
  $groupname = $DB->get_record('groups', array('id' => $id, 'courseid' => $courseid));
  $sectionameid = $DB->get_record('course_sections', array('course' => $courseid, 'name' => $groupname->name));
  if (!empty($sectionameid->sequence)) {
    $activity_query = 'SELECT cm.id, z.id as zid, z.name , z.start_time, z.duration FROM {zoom} z INNER JOIN {course_modules} cm ON cm.instance = z.id INNER JOIN {modules} m ON cm.module = m.id WHERE cm.id in(' . $sectionameid->sequence . ') AND m.name = ?';
    $zoom_activity = $DB->get_records_sql($activity_query, array("zoom"));
    // die;
  } else {
    $zoom_activity = array();

  }
  if ($zoom_activity) {
    $html = '';
    foreach ($zoom_activity as $value) {
      $html .= '<div class="meetingdetails">
      <input type="hidden" name="starttime[]" value="'.$value->start_time.'">
      <input type="hidden" name="existingmeeting[]" value="1">
      <input type="hidden" name="endtime[]" value="'.$value->duration.'"> 
     <span class="starttime"> '. date("D, M j, h:i A", $value->start_time) . ' </span>-
     <span class="endtime"> ' . date("h:i A", $value->start_time + $value->duration) . '</span> '. date_default_timezone_get();
     $html .= '<span data-id="' . $value->id . '" class="removemeeting"> X </span>
   </div>';
    }
  }
}


function get_course_metadata($id) {
  $handler = \core_customfield\handler::get_handler('core_course', 'course');
  $datas = $handler->get_instance_data($id, true);
  $metadata = [];
  foreach ($datas as $data) {
      //echo 'data: '.$data->get_value();
      if (empty($data->get_value())) {
          continue;
      }
      $metadata[$data->get_field()->get('shortname')] = $data->get_value();
  }
  return $metadata;
}

class meeting_form extends moodleform
{

  public function definition()
  {
    $mform = $this->_form; // Don't forget the underscore! 
    global $DB, $CFG, $USER;
    $courseid = $this->_customdata['courseid'];
    $teachers = $this->_customdata['teacher'];
    $groupolddata = $this->_customdata['groupolddata'];
    $html = $this->_customdata['html'];
    $crscustomdata = get_course_metadata($courseid);
    $mform->addElement('hidden', 'courseid', $courseid);
    $mform->addElement('hidden', 'id', 0);

    if (!empty($groupolddata)) {
      $mform->addElement('static', 'staticname', 'Name', $groupolddata->name);
      $mform->addElement('hidden', 'name', $groupolddata->name);
    }

    $radioarray = array();
    $radioarray[] = $mform->createElement('radio', 'visibility', '', 'Public', 0, '');
    $radioarray[] = $mform->createElement('radio', 'visibility', '', 'Private', 1, '');
    $mform->addGroup($radioarray, 'radioar', 'Visibility', array(' '), false);

    // $mform->addElement('html', '<div class="meettime">' . (($html) ? $html : '') . '</div>
        
    //     <a class="meettimepopup">Add a meeting Time...</a>');
    $mform->addElement('static', 'meetinglink', '<a class="meettimepopup">Add a Meeting Time</a>', '<div class="meettime">' . (($html) ? $html : '') . '</div>');

    $options5 = range(0, 18);
    $select = $mform->addElement('select', 'availablespace', 'Available Spaces', $options5);

    if (is_siteadmin()) {
      $mform->addElement('select', 'sectionteacher', 'Section Teachers', $teachers);
      if (!empty($groupolddata->teacher)) {
        $mform->setDefault("sectionteacher", $groupolddata->teacher);
        $mform->hardFreeze('sectionteacher');
      }
    } else {
      $mform->addElement('hidden', 'sectionteacher', $USER->id);
    }

    $mform->addElement('hidden', 'deletedevents', "");

    $mform->addElement('text', 'price', 'Section Price Override');
    $mform->addRule('price', 'Enter the Amount Only', 'numeric', null, '');
    
    $mform->addElement('textarea','syllabus', 'Syllabus Override', 'wrap="virtual" rows="10" cols="10"');
    $mform->setType('syllabus', PARAM_RAW);

    if (!empty($groupolddata->visibility)) {
      $mform->setDefault('visibility', $groupolddata->visibility);
    }
    $courseconfig = get_config('moodlecourse');
    if (!empty($groupolddata->availablespace)) {
      $select->setSelected($groupolddata->availablespace);
    }else{
      $select->setSelected($courseconfig->newsitems);
    }
    if (!empty($groupolddata->price)) {
      $mform->setDefault('price', $groupolddata->price);
    }else{
      $mform->setDefault('price', $crscustomdata['price']);
    }
    if (!empty($groupolddata->syllabus)) {
      $mform->setDefault('syllabus', $groupolddata->syllabus);
    }else{
      $mform->setDefault('syllabus', $crscustomdata['syllabus']);
    }
    if (!empty($groupolddata->startgroup_time)) {
      $mform->setDefault('startgroup_time', $groupolddata->startgroup_time);
    }
    if (!empty($groupolddata)) {
      $mform->setConstant('id', $groupolddata->id);
    }


    $this->add_action_buttons();

  }

  //Custom validation should be added here
  function validation($data, $files){
    global $DB;
    $errors = array();
    if (!empty($data['sectionteacher'])) {
      $userobj = $DB->get_record('user',array('id'=>$data['sectionteacher']));
      $hostID = local_manage_course_zoom_get_user_id($userobj); 
      if(empty($hostID)){
        $errors['sectionteacher'] = 'This user has no permission to create meeting.';
      }
    }
    return $errors;
  }
}
require_capability('moodle/course:managegroups', $coursecontext);

$strgroups = get_string('groups');
$PAGE->set_title($strgroups);
$PAGE->set_heading($course->fullname . ': ' . $strgroups);
$PAGE->set_pagelayout('admin');
navigation_node::override_active_url(new moodle_url('/group/index.php', array('id' => $course->id)));

$returnurl = $CFG->wwwroot . '/group/index.php?id=' . $course->id . '&group=' . $id;

// Prepare the description editor: We do support files for group descriptions
$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $course->maxbytes, 'trust' => false, 'context' => $coursecontext, 'noclean' => true);
if (!empty($group->id)) {
  $editoroptions['subdirs'] = file_area_contains_subdirs($coursecontext, 'group', 'description', $group->id);
  $group = file_prepare_standard_editor($group, 'description', $editoroptions, $coursecontext, 'group', 'description', $group->id);
} else {
  $editoroptions['subdirs'] = false;
  $group = file_prepare_standard_editor($group, 'description', $editoroptions, $coursecontext, 'group', 'description', null);
}
$groupolddata = null;
if ($id) {
  $sql = 'SELECT g.*, cg.teacher, cg.price, cg.availablespace, cg.syllabus, cg.post_data FROM {groups} g LEFT JOIN {custome_groups} cg ON g.id = cg.groupid WHERE g.id = ' . $id . '';
  $groupolddata = $DB->get_record_sql($sql, array());
}

$args = array(
  'courseid' => $courseid,
  'teacher' => $teachname,
  'groupolddata' => $groupolddata,
  'html' => $html,
);
$mform = new meeting_form($CFG->wwwroot . "/local/manage_course/meetings.php?courseid=" . $courseid, $args);


if ($mform->is_cancelled()) {
  redirect($CFG->wwwroot . "/local/manage_course/groups.php?id=" . $courseid . "");
} else if ($formdata = $mform->get_data()) {
  if(empty($formdata->syllabus)){
    $crscustomdata = get_course_metadata($formdata->courseid);
    $formdata->syllabus = $crscustomdata['syllabus'];
  }
    foreach ($_POST['starttime'] as $k=> $meetstarttime) {
        if(strpos($meetstarttime, " ")){
          $_POST['starttime'][$k] = strtotime($meetstarttime);
        }
    }

    //section teacher enroll 
    if($formdata->sectionteacher){
      
      $query = 'SELECT * FROM {enrol} WHERE enrol = "manual" AND courseid = '.$formdata->courseid;
      $enrollmentID = $DB->get_record_sql($query);
      if(!empty($enrollmentID->id)) {
          if (!$DB->record_exists('user_enrolments', array('enrolid'=>$enrollmentID->id, 'userid'=>$formdata->sectionteacher))) {
              $enrol_manual = enrol_get_plugin('manual');
              $enrol_manual->enrol_user($enrollmentID, $formdata->sectionteacher, 3, time(), 0);
              // add_to_log($id, 'course', 'enrol', '../enrol/users.php?id='.$id, $id, $formdata->sectionteacher); //there should be userid somewhere!
          }
      }
    }

    $teacherobj = $DB->get_record('user',array('id'=>$formdata->sectionteacher));
    $hid = local_manage_course_zoom_get_user_id($teacherobj);  

    if (empty($formdata->name)) {
      $totalrecode = $DB->get_records('groups', array());
      $tname = $DB->get_field('user', 'lastname', array('id' => $formdata->sectionteacher));
      if ($count = $DB->get_field_sql("SELECT count(id) AS count FROM {groups} WHERE name LIKE '" . $tname . "%'")) {
        $count++;
        $tname = $tname . '_' . $count;
      }
      $formdata->name = $tname;
    }

    if (!has_capability('moodle/course:changeidnumber', $coursecontext)) {
      // Remove the idnumber if the user doesn't have permission to modify it
      unset($obj->idnumber);
    }
      $formdata->timemodified = time();
      if ($formdata->id) {
        groups_update_group($formdata, $editform, $editoroptions);
      } else {
        $formdata->id = groups_create_group($formdata, $editform, $editoroptions); // create course group in course.
      }
      $obj = new stdClass();
      $obj->groupid = $formdata->id;
      $obj->courseid = $courseid;
      $customdata = $DB->get_record('custome_groups', (array) $obj);
      if ($customdata) {
        $obj = $customdata;
      }
      $obj->name = $formdata->name;
      $obj->availablespace = $formdata->availablespace;
      $obj->price = $formdata->price;
      $obj->syllabus = $formdata->syllabus;

     // $obj->syllabus = htmlentities($formdata->syllabus,ENT_QUOTES, "UTF-8");
      // $obj->syllabus = '';
      //$obj->startgroup_time = $formdata->startgroup_time;
      if (!empty($obj->id)) {
        $obj->updatetime = $formdata->timemodified;
        $obj->post_data = json_encode($_POST);
        $customegroups = $DB->update_record('custome_groups', $obj);
        $sectioname = $DB->get_record('course_sections', array('course' => $courseid, 'name' => $formdata->name));
        $sectionid = $sectioname->section;
      } else {
        $obj->post_data = json_encode($_POST);
        $obj->teacher = $formdata->sectionteacher;
        $obj->createdtime = $formdata->timemodified;
        $obj->id = $DB->insert_record('custome_groups', $obj);
        $courseorid = new stdClass();
        $courseorid->id = $courseid;
        $courseorid->name = $tname;
        $sectdata = course_create_section($courseorid, $position = 0, $skipcheck = false); // create section in course like : Topic 1 && Topic 2
        $sectionid = $sectdata->section;
      }
      //echo "<pre>";
      $request = get_config('local_manage_course','approved'); // approvel condition
      if( $request == 0 ){ 

        $allsectiondata = $DB->get_records("custome_groups", array("courseid" => $courseid));
        $datatopush = array();
        foreach ($allsectiondata as $skey => $course_sections_data) {
          $userdata = $DB->get_record('user', array('id' => $course_sections_data->teacher));
          $course_sections_data->teacheremail = $userdata->email;
          $course_sections_data->post_data = json_decode($course_sections_data->post_data);
          $course_sections_data->starttime = $course_sections_data->post_data->starttime;
          $course_sections_data->endtime = $course_sections_data->post_data->endtime;
          $course_sections_data->meeting_visibility = $course_sections_data->post_data->visibility;
          array_push($datatopush, $course_sections_data);
        }

         $wpurl = get_config('local_manage_course', 'wpurl'); // Render data from plugin setting.
        $postdata = array("allsessions" => $datatopush, "courseid" => $courseid);
        $curl = curl_init();
//         echo "<pre>";
//         print_r($postdata['allsessions'][1]->post_data->starttime);
// 		     echo date_default_timezone_get();
// 		     echo "<br>";
//         echo $time1 = date('Y-m-d H:i:s a', $postdata['allsessions'][1]->post_data->starttime[0]);
//         echo "<br>";
//         echo $time2 = date('Y-m-d H:i:s a', $postdata['allsessions'][1]->post_data->starttime[1]);
//         print_r($postdata);
//         echo "</pre>";
//         echo json_encode($postdata); 
//         die;
//         echo json_encode($postdata); 
//         die;
        curl_setopt_array(
          $curl,
          array(
            CURLOPT_URL => $wpurl . '/wp-content/plugins/sync-course/product-course-section-add.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json'
            ),
          )
        );
      $response = curl_exec($curl);
      }      
  $moduleid = $_POST['deletedevents'];
  if (!empty($moduleid)) {
    $cmidarr = explode(",", $moduleid);
    foreach ($cmidarr as $value) {
      course_delete_module($value);
    }
    $returnurl = $CFG->wwwroot . '/local/manage_course/groups.php?id=' . $formdata->courseid;
    redirect($returnurl);
  }
  $coursedata = $DB->get_record("course", array("id" => $courseid));
  $zooml = new stdClass();
  if (is_array($_POST['starttime'])) {
    for ($i = 0; $i < sizeof($_POST['starttime']); $i++) {
      if(isset($_POST['existingmeeting'][$i]) && $_POST['existingmeeting'][$i] == 1){continue;}
      $starttime = $_POST['starttime'][$i];
      $endtime = $_POST['endtime'][$i];
      $zooml->name = date("l, j F Y h:i A", $starttime);
      // $zooml->host_id = local_manage_course_zoom_get_user_id($teacherobj);
      $zooml->host_id = $hid;
      $zooml->course = $course->id;
      $zooml->section = $sectionid;
      $zooml->module = 37;
      $zooml->modulename = 'zoom';
      $zooml->visible = 1;
      $zooml->visibleoncoursepage = 1;
      $zooml->availabilityconditionsjson = '{"op":"&","c":[{"type":"group","id":'.$formdata->id.'}],"showc":[true]}';
      $zooml->start_time = $starttime;
      $zooml->duration = $endtime;
      $response = add_moduleinfo($zooml, $coursedata); // create zoom activity module in course
    }
  }

  $returnurl = $CFG->wwwroot . '/local/manage_course/groups.php?id=' . $formdata->courseid;
  redirect($returnurl);
}
echo $OUTPUT->header();
$mform->display();
if($id){
  $wpurl= get_config('local_manage_course', 'wpurl');
   $wpurl.'/group-meet?courseid='.$courseid.'&groupid='.$id;
  echo '<div class="meetinglinkurl">
  <p><b>Meeting Enrollment Link:</b> '.$wpurl.'/group-meet?courseid='.$courseid.'&groupid='.$id.'</a></span>
  </div>';
}
echo $OUTPUT->footer();

?>

<!-- The Modal -->
<div class="modal hide " id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Add a meeting
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form>
          <label for="date">Date : </label>
          <input type="date" id="date" name="date" /><br />
          <span>Start :</span>
          <div id="timePickerstart" style="width: 20rem;"></div>
          <span>End :</span>
          <div id="timePickerend" style="width: 20rem;"></div>
          <input type="button" class="close" style="    background-color: #c23a34 !important; opacity: 100;"
            value="Cancel">
          <input type="button" class="saveappend" style="float:right;" value="Save meeting time"/><br></br>
          <div class="eventlistpopup"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" href="<?php $CFG->wwwroot ?>/local/manage_course/css/style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
<script src="<?php $CFG->wwwroot ?>/local/manage_course/jquery-time-picker.js"></script>
<script>
  $(function () {
    // $("#timePickerValue").html($("#timePicker").timepicker().getValue());
    $("#timePickerstart").timepicker({});
    $("#timePickerend").timepicker({});
    $(".meettimepopup").click(function () {
      // console.log('sssssssss');
      $(".eventlistpopup").html($(".meettime").html());
      $(".modal").show();
    });
    $(".close").click(function () {
      // console.log('aaaaaaaaa');
      $(".modal").hide();
    });
    $(document).on("click", ".removemeeting", function () {
      var mid = $(this).data("id");
      if (mid) {
        var deleted = $("[name=deletedevents]").val() + '';
        if (deleted) {
          deleted = deleted.split(",")
        } else {
          deleted = [];
        }
        console.log('mid- ', mid)
        console.log('deletedfddddgfs- ', deleted)
        deleted.push(mid);
        console.log('deleted- ', deleted)
        $("[name=deletedevents]").val(deleted);
      }
      $(this).closest(".meetingdetails").remove();
    });

    $('.saveappend').click(function (e) {
      e.preventDefault();
      var date = $("#date").val();
      console.log('date-----',date);
      const datArray = date.split("-");

      if (date == '') {
        alert('Date is required.');
        return;
      }
      const d = new Date(date);
      var CurrentDate = new Date();
      if (CurrentDate.getTime() >= d.getTime()) {
        alert('Please select the current date or more...');
        return;
      }
      //----------function----------
      const convertTime = timeStr => {
        const [time, modifier] = timeStr.split(' ');
        let [hours, minutes] = time.split(':');
        if (hours === '12') {
            hours = '00';
        }
        if (modifier === 'PM') {
            hours = parseInt(hours, 10) + 12;
        }
        return `${hours}:${minutes}`;
      };
      // GET day
      const weekday = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
      // GET month
      const month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
      // GET dat
      var starttime = $("#timePickerstart").timepicker().getValue();
      // console.log('stime-----',starttime);
      var ttttttt = starttime;
      var sttt = convertTime(ttttttt);  
      const startArray = sttt.split(":");
      // console.log('aaaaa----',datArray,'------',startArray);
      const dstart = new Date(datArray[0] ,(datArray[1]-1) ,datArray[2] ,startArray[0], startArray[1]);
      let dat = dstart.getDate();
      let day = weekday[dstart.getDay()];
      let mont = month[dstart.getMonth()];
      console.log('aaaaa222-----------',dstart);
      console.log('dstart.getDay()',dstart.getDay());
      console.log('dstart.getMonth()',dstart.getMonth());
      let starttimestr = dstart.getTime();
      // console.log('starttime',starttime,'dstarttime',dstarttime);
      var endtime = $("#timePickerend").timepicker().getValue();
      // console.log('etime-----',endtime);
      var endttttt = endtime;
      var endttt = convertTime(endttttt); 
      const endArray = endttt.split(":");
      // console.log('bbbbbb----',datArray,'------',endArray);
      const dend = new Date(datArray[0] ,(datArray[1]-1) ,datArray[2] ,endArray[0], endArray[1]);
      // console.log('bbbbbb22-----------',dend);
      let endtimestr = dend.getTime();
      let duration = endtimestr - starttimestr;
      
      // if (CurrentDate.getTime() > starttimestr) {
      //   alert('Please select the current date or more...');
      //   return;
      // }

      if (starttimestr >= endtimestr) {
        alert('Please Enter the right meeting time..');
        return;
      }
      var olddata = $(".meettime").text();
      if(olddata.includes(`${day}, ${mont} ${dat}, ${starttime} -`)){
        alert('Your are not added same meeting again.');
        return;
      }
      // console.log('endtime',endtime,'endtimestr',endtimestr);
      $(".meettime").append(`<div class="meetingdetails">
                                      <span class="starttime"> ${day}, ${mont} ${dat},</span><span class="starttime"> ${starttime} </span>-
                                      <span class="endtime"> ${endtime} </span>  
                                      <input type="hidden" name="starttime[]" value="${date} ${starttime}">
                                      <input type="hidden" name="endtime[]" value="${duration/1000}"><?php echo date_default_timezone_get();?>                               
                                      
                                      <span class="removemeeting"> X </span>
                                    </div>`);
      $(".eventlistpopup").html($(".meettime").html());
    });


  });
</script>