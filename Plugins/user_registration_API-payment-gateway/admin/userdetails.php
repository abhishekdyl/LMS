<?php
require_once('../../../config.php');
global $DB, $USER, $PAGE;
$id = required_param('id', PARAM_RAW);
$post_var = base64_decode($id);
$PAGE->requires->jquery();
// $PAGE->set_pagelayout('standard');
$pageurl = $CFG->wwwroot."/local/user_registration/admin/userdetails.php?id=".$id;
$PAGE->set_url($CFG->wwwroot.'/local/user_registration/admin/userdetails.php?id='.$id);
$PAGE->set_title('Assessor Panel');
$PAGE->set_heading('<div>Assessor Panel</div>');
$type = null;

if($DB->record_exists('lcl_individual_enrollment', array('registration_id'=>$post_var))) {
    $sql = "SELECT ind.*, c.fullname as fullname, c.shortname as shortname, cc.name as category FROM {lcl_individual_enrollment} ind 
    INNER JOIN {lcl_registration} reg ON reg.id = ind.registration_id
    INNER JOIN {course} c ON c.id = ind.course_id
    INNER JOIN {course_categories} cc ON cc.id = c.category
    WHERE reg.id = '$post_var'";
    $user_data = $DB->get_record_sql($sql);
    $type='Individual';
    $course_id = $user_data->course_id;
    $typenameprerequisite = $user_data->typenameprerequisite;
}

if($DB->record_exists('lcl_corporate_enrollment', array('registration_id'=>$post_var))) {
    $sql = "SELECT cor.*, c.fullname as fullname, c.shortname as shortname, cc.name as category FROM {lcl_corporate_enrollment} cor 
    INNER JOIN {lcl_registration} reg ON reg.id = cor.registration_id
    INNER JOIN {course} c ON c.id = cor.course_id
    INNER JOIN {course_categories} cc ON cc.id = c.category
    WHERE reg.id = '$post_var'";
    $user_data = $DB->get_record_sql($sql);
    $type='Corporate';
    $course_id = $user_data->course_id;
    $typenameprerequisite = $user_data->typenameprerequisite;
}

$prerequisite = $DB->get_record('customfield_field', array('shortname' => 'prerequisite'));                                     
$certificates = $DB->get_record('customfield_field', array('shortname' => 'certificates')); 
$customdata_prerequisite = $DB->get_record('customfield_data', array('fieldid' => $prerequisite->id, 'instanceid' => $course_id)); 

if($customdata_prerequisite->value == 2) {
    $customdata_certificates = $DB->get_record('customfield_data', array('fieldid' => $certificates->id, 'instanceid' => $course_id));                                              
    $certificate_data = $customdata_certificates->value;
    $certificate_data = explode("\n", $certificate_data);
    $arrnewdata = array();
    $typename = array();
    $key = 0; 
    foreach($certificate_data as $data) {
        $data = trim(strip_tags($data));
        $prerequisite_data = $DB->record_exists('lcl_prerequisite_data', array('registration_id' => $post_var, 'type'=> $key));
        $typename[$key] = $data; 
        if($prerequisite_data){
            if($data !=''){
              $arrnewdata[$key] = $data;
            }
        }
        $key++;
     }
} 

echo $OUTPUT->header();
?>
<style>#headerTable tr th{ background-color:#eeeae4; }</style>
<script src="<?php echo $CFG->dirroot.'/local/user_registration/js/dataTables.min.js'; ?>"></script>
<div class="table-responsive">
<h2 class="text-center p-2 text-white" style="background-color: #110051d4 !important;">Applicant Details (Level 3 and above) - <?php echo $type; ?></h2>
<?php if($type=='Individual'){  ?>
    <div><b>Course Details</b></div>
    <table class="table table-bordered table-striped " style="background-color: #d6f0e7;">
        <tbody>
                <tr><th>Course Applied For</th><th>Short Name</th><th>Category Name</th><th>Id Number</th></tr>
                <tr><td><?php echo $user_data->fullname; ?></td><td><?php echo $user_data->shortname; ?></td><td><?php echo $user_data->category; ?></td><td><?php echo $user_data->idnumber; ?></td></tr>
        </tbody>
    </table>
    <?php 
    if($DB->record_exists('lcl_application_form', array('registration_id' => $post_var))){ 
    $lcl_application_formdata = $DB->get_records('lcl_application_form', array('registration_id' => $post_var)); 
    ?>
    <button class="btn btn-info mb-3" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
        Details of the Registered Delegates <i class="fa fa-arrow-down" aria-hidden="true"></i>
    </button>
    <div class="collapse" id="collapseExample">
      <div class="card card-body">
        <div><b>Registered Delegates</b></div>
        <table class="table table-bordered table-striped " style="width: 100%;">
            <thead>
                <tr>
                    <th>Sl.</th>
                    <th>Fullname</th>
                    <th>Cpr</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Date of birth</th>
                    <?php
    				if($customdata_prerequisite->value == 2){
	                    if($typenameprerequisite) {
	                        echo "<th>Prerequisite</th>";
	                        foreach($arrnewdata as $key => $value) {
	                            echo "<th></th><th></th>";
	                        }
	                    }
                	}
                    ?>

                </tr>
            </thead>
            <tbody>
                <?php $count=1; foreach ($lcl_application_formdata as $data){ ?>
                <tr>
                    <td><?php echo $count++; ?></td>
                    <td><?php echo $data->fullname; ?></td>
                    <td><?php echo $data->cpr; ?></td>
                    <td><?php echo $data->email; ?></td>
                    <td><?php echo $data->mobile; ?></td>
                    <td><?php if(!empty($data->date_of_birth)){ echo date('Y-m-d', $data->date_of_birth); } ?></td>

                    <?php
                    if($customdata_prerequisite->value == 2){
	                    if($typenameprerequisite) {
	                       $content = unserialize($data->content);
	                       foreach($arrnewdata as $key => $value) {
	                                echo  "<td>".$value."</td>
	                                       <td><input style='color: blue;' type='text' data-toggle='modal' data-target='#exampleModalCenter".$data->id.$key."' value='View' readonly></td>
	                                       <div class='modal fade' id='exampleModalCenter".$data->id.$key."' tabindex='-1' role='dialog' aria-labelledby='exampleModalCenterTitle' aria-hidden='true'>
	                                            <div class='modal-dialog modal-lg modal-dialog-centered' role='document'>
	                                            <div class='modal-content'>
	                                              <div class='modal-header'>
	                                                <h5 class='modal-title' id='exampleModalLongTitle'>Uploaded content</h5>
	                                              </div>
	                                              <div class='modal-body'>";
	                                                if($data->relation == 'parent') {
	                                                    $prerequisite_data = $DB->get_record('lcl_prerequisite_data', array('registration_id' => $post_var, 'type'=> $key));
	                                                    if($typenameprerequisite == 'File Upload') {
	                                                         echo "<img width='100%' src='".$CFG->wwwroot."/local/user_registration/temp/".$prerequisite_data->content."' alt='Uploaded File' />";
	                                                    }else if ($typenameprerequisite == 'Ask Question' || $typenameprerequisite == 'Personal Info') {
	                                                         echo "<div>".$prerequisite_data->content."</div>";
	                                                    }
	                                                } else {
	                                                    if($typenameprerequisite == 'File Upload') {
	                                                         echo "<img width='100%' src='".$CFG->wwwroot."/local/user_registration/temp/".$content[$key]."' alt='Uploaded File' />";
	                                                    }else if ($typenameprerequisite == 'Ask Question' || $typenameprerequisite == 'Personal Info') {
	                                                         echo "<div>".$content[$key]."</div>";
	                                                   }
	                                                }    
	                                        echo "</div>
	                                              <div class='modal-footer'>
	                                                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
	                                              </div>
	                                            </div>
	                                          </div>
	                                        </div>";
	                                }
	                         }
                       }
                    ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
      </div>
    </div>
    <?php } ?>
    <div><b>Basic Details</b></div>
    <table id="headerTable" class="table table-bordered table-striped " style="width: 100%;">
        <tbody>
                <tr><th>Registration No.</th> <td><?php echo $post_var; ?></td></tr>
                <tr><th>Name</th> <td><?php echo $user_data->name; ?></td></tr>
                <tr><th>Email</th> <td><?php echo $user_data->email; ?></td> </tr>
                <tr><th>Mobile Number</th> <td><?php echo $user_data->mobile_number; ?></td> </tr>
                <tr><th>Alternative Mobile Number</th> <td><?php echo $user_data->other_phone; ?></td></tr>
                <tr><th>Sponsor</th> <td><?php echo $user_data->sponsor; ?></td> </tr>
                <tr><th>Date Of Birth</th> <td><?php echo date('Y-m-d', $user_data->date_of_birth); ?></td></tr>
                <tr><th>CPR</th> <td><?php echo $user_data->cpr; ?></td></tr>
                <tr><th>Job Title</th> <td><?php echo $user_data->job_title; ?></td></tr>
                <tr><th>Referrel By</th> <td><?php echo $user_data->referrel_by; ?></td></tr>
                <tr><th>Major</th> <td><?php echo $user_data->major; ?></td></tr>
                <tr><th>University</th> <td><?php echo $user_data->university; ?></td></tr>
                <tr><th>Course Timing</th> <td><?php echo $user_data->course_timing; ?></td></tr>
                <tr><th>Course Venue</th> <td><?php echo $user_data->course_location; ?></td></tr>
                <tr><th>Course Price</th> <td><?php echo $user_data->course_price; ?></td></tr>
                <tr><th>Start Date</th> <td><?php if(!empty($user_data->start_date)){ echo date('Y-m-d', $user_data->start_date); }  ?></td></tr>
                <tr><th>End Date</th> <td><?php if(!empty($user_data->start_date)){ date('Y-m-d', $user_data->end_date); } ?></td></tr>
        </tbody>
    </table>
   <?php } if($type=='Corporate') {  ?>
    <table class="table table-bordered table-striped " style="background-color: #d6f0e7; width: 100%;">
        <tbody>
                <tr><th>Course Applied For</th><th>Short Name</th><th>Category Name</th><th>Id Number</th></tr>
                <tr><td><?php echo $user_data->fullname; ?></td><td><?php echo $user_data->shortname; ?></td><td><?php echo $user_data->category; ?></td><td><?php echo $user_data->idnumber; ?></td></tr>
        </tbody>
    </table>
    <?php 
    if($DB->record_exists('lcl_application_form', array('registration_id' => $post_var))){ 
    $lcl_application_formdata = $DB->get_records('lcl_application_form', array('registration_id' => $post_var)); 
    ?>
    <button class="btn btn-info mb-3" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
    Details of the Registered Delegates <i class="fa fa-arrow-down" aria-hidden="true"></i>
    </button>
    <div class="collapse" id="collapseExample">
      <div class="card card-body">
        <div><b>Registered Delegates</b></div>
        <table class="table table-bordered table-striped " style="width: 100%;"> 
            <thead>
                <tr>
                    <th>Sl.</th>
                    <th>Fullname</th>
                    <th>Cpr</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Date of birth</th>
    				<?php
                    if($customdata_prerequisite->value == 2){
                        if($typenameprerequisite) {
                            echo "<th>Prerequisite</th>";
                            foreach($arrnewdata as $key => $value) {
                                echo "<th></th><th></th>";
                            }
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php $count=1; foreach ($lcl_application_formdata as $data){ ?>
                <tr>
                    <td><?php echo $count++; ?></td>
                    <td><?php echo $data->fullname; ?></td>
                    <td><?php echo $data->cpr; ?></td>
                    <td><?php echo $data->email; ?></td>
                    <td><?php echo $data->mobile; ?></td>
                    <td><?php if(!empty($data->date_of_birth)){ echo date('Y-m-d', $data->date_of_birth); } ?></td>
                    <?php

                    if($customdata_prerequisite->value == 2){
                    if($typenameprerequisite) {
                       $content = unserialize($data->content);
                       foreach($arrnewdata as $key => $value) {
                                echo  "<td>".$value."</td>
                                       <td><input style='color: blue;' type='text' data-toggle='modal' data-target='#exampleModalCenter".$data->id.$key."' value='View' readonly></td>
                                       <div class='modal fade' id='exampleModalCenter".$data->id.$key."' tabindex='-1' role='dialog' aria-labelledby='exampleModalCenterTitle' aria-hidden='true'>
                                            <div class='modal-dialog modal-lg modal-dialog-centered' role='document'>
                                            <div class='modal-content'>
                                              <div class='modal-header'>
                                                <h5 class='modal-title' id='exampleModalLongTitle'>Uploaded content</h5>
                                              </div>
                                              <div class='modal-body'>";
                                                if($data->relation == 'parent') {
                                                    $prerequisite_data = $DB->get_record('lcl_prerequisite_data', array('registration_id' => $post_var, 'type'=> $key));
                                                    if($typenameprerequisite == 'File Upload') {
                                                         echo "<img width='100%' src='".$CFG->wwwroot."/local/user_registration/temp/".$prerequisite_data->content."' alt='Uploaded File' />";
                                                    }else if ($typenameprerequisite == 'Ask Question' || $typenameprerequisite == 'Personal Info') {
                                                         echo "<div>".$prerequisite_data->content."</div>";
                                                    }
                                                } else {
                                                    if($typenameprerequisite == 'File Upload') {
                                                         echo "<img width='100%' src='".$CFG->wwwroot."/local/user_registration/temp/".$content[$key]."' alt='Uploaded File' />";
                                                    }else if ($typenameprerequisite == 'Ask Question' || $typenameprerequisite == 'Personal Info') {
                                                         echo "<div>".$content[$key]."</div>";
                                                   }
                                                }    
                                        echo "</div>
                                              <div class='modal-footer'>
                                                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
                                              </div>
                                            </div>
                                          </div>
                                        </div>";
                                }
                         }
                     }
                    ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
      </div>
    </div>
    <?php } ?>
    <div><b>Basic Details</b></div>
    <table id="headerTable" class="table table-bordered table-striped" style="width: 100%;">
        <tbody>
                <tr><th>Registration No.</th> <td><?php echo $post_var; ?></td></tr>
                <tr><th>Client Name</th> <td><?php echo $user_data->client_name; ?></td></tr>
                <tr><th>Contact Person</th> <td><?php echo $user_data->contact_person; ?></td></tr>
                <tr><th>Job Title</th> <td><?php echo $user_data->job_title; ?></td></tr>
                <tr><th>Email</th> <td><?php echo $user_data->email; ?></td> </tr>
                <tr><th>P.O Box</th> <td><?php echo $user_data->po_box; ?></td> </tr>
                <tr><th>Mobile Number</th> <td><?php echo $user_data->mobile_number; ?></td> </tr>
                <tr><th>Other Number</th> <td><?php echo $user_data->work_phone; ?></td></tr>
                <tr><th>Sponsoring Organisation </th> <td><?php echo $user_data->sponsor_organisation; ?></td> </tr>
                <tr><th>Course Timing</th> <td><?php echo $user_data->course_timing; ?></td></tr>
                <tr><th>Course Venue</th> <td><?php echo $user_data->course_location; ?></td></tr>
                <tr><th>Course Price</th> <td><?php echo $user_data->course_price; ?></td></tr>
                <tr><th>Start Date</th> <td><?php if(!empty($user_data->start_date)){ echo date('Y-m-d', $user_data->start_date); }  ?></td></tr>
                <tr><th>End Date</th> <td><?php if(!empty($user_data->start_date)){ date('Y-m-d', $user_data->end_date); } ?></td></tr>
        </tbody>
    </table>
<?php } ?>
  
</div>
<script>$(document).ready(function(){ $("#headerTable").dataTable(); }); </script>
<?php echo $OUTPUT->footer(); ?>


