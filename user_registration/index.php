<?php
require_once('../../config.php');
global $DB, $PAGE, $CFG;

if(isset($_POST['individual'])) {
    $_SESSION["registartion_type"] = $_POST['individual'];
    $_SESSION["course_id"] = $_POST['course_id'];
    redirect('individual_enrollment.php?type='.$_SESSION["registartion_type"]);
}

if(isset($_POST['corporate'])) {
    $_SESSION["registartion_type"] = $_POST['corporate'];
    $_SESSION["course_id"] = $_POST['course_id'];
    redirect('corporate_enrollment.php?type='.$_SESSION["registartion_type"]);
}

$PAGE->requires->jquery();
$PAGE->set_context(context_system::instance());
// $PAGE->set_pagelayout('standard');
$PAGE->set_url($CFG->wwwroot.'/local/user_registration/index.php');
$PAGE->set_title('User Registration');
require_once($CFG->dirroot.'/local/user_registration/classes/customClass.php');

function convertTo12HourFormat($hour, $minute = 0) {
    // Validate the hour
    if ($hour < 0 || $hour > 23) {
        return "Invalid hour";
    }

    // Convert to 12-hour format
    $ampm = ($hour < 12) ? 'AM' : 'PM';
    $hour12 = ($hour % 12 === 0) ? 12 : $hour % 12;
    $formattedHour = sprintf("%02d", $hour12);
    $formattedMinute = sprintf("%02d", $minute);

    // Return the formatted time string
    return "$formattedHour:$formattedMinute $ampm";
}

$html = '<div class="container mt-5">
<ul class="nav nav-tabs" id="myTabs">
  <li class="nav-item">
    <a class="nav-link active" id="about-tab" data-bs-toggle="tab" href="#about">Courses</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="individual-tab" data-bs-toggle="tab" href="#individual">Individual</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="corporate-tab" data-bs-toggle="tab" href="#corporate">Corporate</a>
  </li>
</ul>

<div class="tab-content mt-3">
  <div class="tab-pane fade show active" id="about">
    <div class="row">';
      $getcustom_course_timing = $DB->get_record('customfield_field', array('shortname'=>'course_timing'));
      $course_timing_configdata = json_decode($getcustom_course_timing->configdata);
      $course_timing_configdata = $course_timing_configdata->options;
      $course_timing_configdata = explode("\n", trim($course_timing_configdata));


      $getcustom_course_days = $DB->get_record('customfield_field', array('shortname'=>'course_days'));
      $course_days_configdata = json_decode($getcustom_course_days->configdata);
      $course_days_configdata = $course_days_configdata->options;
      $course_days_configdata = explode("\n", trim($course_days_configdata));

      $getcustom_location = $DB->get_record('customfield_field', array('shortname'=>'course_venue'));
      $getcustomlevel_id = $DB->get_record('customfield_field', array('shortname'=>'foundation_level'));
      $getcustomprice_id = $DB->get_record('customfield_field', array('shortname'=>'price'));
      $check_customfield_field_foundation_level = $DB->get_record("customfield_field", array('shortname'=>'foundation_level'));

      $courses = $DB->get_records('course', array('visible' => 1)); 
      foreach($courses as $data) {
      if($data->id != 1) {
      
        $level_details = $DB->get_record("customfield_data", array('fieldid'=>$getcustomlevel_id->id, 'instanceid'=>$data->id));
        $price_details = $DB->get_record("customfield_data", array('fieldid'=>$getcustomprice_id->id, 'instanceid'=>$data->id));
        $coursedetails = $DB->get_record("course", array('id'=>$data->id));
        $options_json_decode = json_decode($check_customfield_field_foundation_level->configdata);
        $final_option_list = explode("\n",$options_json_decode->options);
        $location_details = $DB->get_record("customfield_data", array('fieldid'=>$getcustom_location->id, 'instanceid'=>$data->id));  
        if($coursedetails->startdate != 0){ $startdate = date("Y-m-d", $coursedetails->startdate); }else{ $startdate = '';}
        if($coursedetails->enddate != 0){ $enddate = date("Y-m-d", $coursedetails->enddate); }else{ $enddate = ''; }
        

        if($location_details->value) {
        $location_details_value = explode("\n", strip_tags($location_details->value));
          $location = '';
          foreach($location_details_value as $value) {
             $location .= '<p>- <span for="'.$value.'">'.$value.'</span></p>';
          }
        }else{
          $location = '<p>- <span>Not specified</span></p>';
        }

		
        $starttime_hour = $DB->get_record('customfield_field', array('shortname'=>'starttime_hour'));  
        $customdata_starttime_hour = $DB->get_record("customfield_data", array('fieldid'=>$starttime_hour->id, 'instanceid'=>$data->id));
        $starttime_hour = $customdata_starttime_hour->value-1;

        $starttime_minute = $DB->get_record('customfield_field', array('shortname'=>'starttime_minute'));  
        $customdata_starttime_minute = $DB->get_record("customfield_data", array('fieldid'=>$starttime_minute->id, 'instanceid'=>$data->id));
        $starttime_minute = $customdata_starttime_minute->value-1;
        
        $starttime = convertTo12HourFormat($starttime_hour, $starttime_minute);

        $endtime_hour = $DB->get_record('customfield_field', array('shortname'=>'endtime_hour'));  
        $customdata_endtime_hour = $DB->get_record("customfield_data", array('fieldid'=>$endtime_hour->id, 'instanceid'=>$data->id));
        $endtime_hour = $customdata_endtime_hour->value-1;
      
        $endtime_minute = $DB->get_record('customfield_field', array('shortname'=>'endtime_minute'));  
        $customdata_endtime_minute = $DB->get_record("customfield_data", array('fieldid'=>$endtime_minute->id, 'instanceid'=>$data->id));
        $endtime_minute = $customdata_endtime_minute->value-1;

        $endtime = convertTo12HourFormat($endtime_hour, $endtime_minute);
      
        $duration = " (".$starttime."-".$endtime.")";
      
        $course_timing_data = $DB->get_record("customfield_data", array('fieldid'=>$getcustom_course_timing->id, 'instanceid'=>$data->id));  
        if($course_timing_data->value) {
          $timing = '';
          $course_timing_value = explode(",", $course_timing_data->value); 
          foreach($course_timing_value as $value) {
             $timing .= '<p>- <span for="'.$value.'">'.$course_timing_configdata[$value].$duration.'</span></p>';
          }
        } else {
          $timing = '<p>- <span>Not specified</span></p>';
        }

        
        $course_days_data = $DB->get_record("customfield_data", array('fieldid'=>$getcustom_course_days->id, 'instanceid'=>$data->id));  
        if($course_days_data->value) {
          $days = '';
          $course_days_value = explode(",", $course_days_data->value);  
          foreach($course_days_value as $value) {
             $days .= '<p>- <span for="'.$value.'">'.$course_days_configdata[$value].'</span></p>';
          }
        } else {
          $days = '<p>- <span>Not specified</span></p>';
        }

        
        $imageUrl = customClass::getcourse_image($data->id);

        $html .= ' <div class="col-md-4 mb-4">
                  <div class="card">
                      <img src="'.$imageUrl.'" class="card-img-top" alt="Course Image" width="40" height="140">
                      <div class="card-body">
                        <ul>
                          <li><h5 class="card-title">'.$data->fullname.'</h5></li>
                          <li><p class="card-text">'.$data->summary.'</p></li>
                          <li><b>Course Price : </b>'.$price_details->value.' .&#1583;.&#1576;</li>
                          <li><b>Course Level : </b>'.$level_details->value.'</li>
                          <li><b>Startdate : </b>'.$startdate.'</li>
                          <li><b>Enddate : </b>'.$enddate.'</li>
                          <li><b>Course Location : </b>'.$location.'</li>
                          <li><b>Course Timing : </b>'.$timing.'</li>
                          <li><b>Course Days : </b>'.$days.'</li>
                          <p><a href="#" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal'.$data->id.'">Apply</a></p>
                        </ul>
                      </div>
                  </div>
                 </div>
                   <div class="modal fade" id="exampleModal'.$data->id.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Register for: </h5>
                        </div>
                        <div class="modal-body">
                        <form action="" method="POST">
                        <div class="row">
                            <input type="hidden" name="course_id" value="'.$data->id.'">
                          <div class="col-sm-6 col-md-6 col-xl-6" align="center">
                            <button type="submit" name="individual" value="individual" class="btn btn-primary">Individual</button>
                          </div>
                          <div class="col-sm-6 col-md-6 col-xl-6" align="center">
                            <button type="submit" name="corporate" value="corporate" class="btn btn-primary">Corporate</button>
                          </div>
                        </div>
                        </form>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>';
          }
        }
$html .='</div>
    </div>
     
    <div class="tab-pane fade" id="individual">
        <h3>Individual Services</h3>
        <div class="row">
            <div class="col-md-6">
                <img src="'.$CFG->wwwroot.'/local/user_registration/src/banner_individual.jpg" class="img-fluid" alt="Individual Services Image">
            </div>
            <div class="col-md-6">
                <p>This is the individual services content.</p>
        <form action="" method="POST">
                  <button type="submit" name="individual" value="individual" class="btn btn-primary">Apply Here</button>
        </form>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="corporate">
        <h3>Corporate Services</h3>
        <div class="row">
            <div class="col-md-6">
                <img src="'.$CFG->wwwroot.'/local/user_registration/src/banner_corporate.jpg" class="img-fluid" alt="Corporate Services Image">
            </div>
            <div class="col-md-6">
                <p>This is the corporate services content.</p>
        <form action="" method="POST">
                  <button type="submit" name="corporate" value="corporate" class="btn btn-primary">Apply Here</button>
        </form>
            </div>
        </div>
    </div>
  
  </div>
</div>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>'; 
echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();
