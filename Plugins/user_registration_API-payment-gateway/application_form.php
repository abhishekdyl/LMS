<?php

require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once("$CFG->libdir/filelib.php");
global $DB, $PAGE, $CFG;

$PAGE->requires->jquery();
$id = optional_param('id', '', PARAM_RAW);
$registration_id = base64_decode($id);
$editid = optional_param('editid', '0', PARAM_INT);
$pageurl = $CFG->wwwroot . "/local/user_registration/application_form.php?id=".$id;
$args = array("id" => $editid, "rg_id"=>$registration_id);
$PAGE->set_title('Registration Form - Application Form');
// $PAGE->set_pagelayout('standard');
$PAGE->set_heading('<div>Course Registration - Application Form : Details of The Registered Delegates</div>');
$PAGE->set_url($CFG->wwwroot.'/local/user_registration/application_form.php?id='.$id);

if (isset($_POST['submit'])) {
    $record_ins = new stdClass();
    $record_ins->registration_id = $_POST['reg_id']; 
    $filetype = $_POST['filetype'];
    $upd_data = new \stdClass();
    if($DB->record_exists('lcl_individual_enrollment', array('registration_id' => $_POST['reg_id']))){
        $upd_data->id = $_POST['enrollment_id'];
        $upd_data->typenameprerequisite = $filetype;
        $DB->update_record('lcl_individual_enrollment', $upd_data);
    }elseif($DB->record_exists('lcl_corporate_enrollment', array('registration_id' => $_POST['reg_id']))){
        $upd_data->id = $_POST['enrollment_id'];
        $upd_data->typenameprerequisite = $filetype;
        $DB->update_record('lcl_corporate_enrollment', $upd_data);
    }
    if(empty($_POST['currentIndex'])) { $currentIndex = 1; } else { $currentIndex = $_POST['currentIndex']; }
      for ($i=1; $i<=$currentIndex; $i++) {
        $fullname = 'fullname'.$i;
        $cpr = 'cpr'.$i;
        $email = 'email'.$i;
        $mobile = 'mobile'.$i;
        $date_of_birth = 'date_of_birth'.$i;

        if($_POST['prerequisitemode']==2) {
            $filedata = json_decode($_POST['filedata']);
            $arr = [];
            foreach($filedata as $key => $value) {
                $fileupload = 'fileupload'.$value.$i;
                if($filetype == 'File Upload'){
                    if(isset($_FILES[$fileupload])) {
                        if(!empty($_FILES[$fileupload]['name'])) {
                            $filename = time().'_'.basename($_FILES[$fileupload]['name']);
                            $target_path = $CFG->dirroot."/local/user_registration/temp/".$filename;  
                            if(move_uploaded_file($_FILES[$fileupload]['tmp_name'], $target_path)) {
                                array_push($arr, $filename);
                            }    
                        }
                    }
                }else if($filetype == 'Ask Question' OR $filetype == 'Personal Info'){
                    array_push($arr, $_POST[$fileupload]);
                }else{
                }
            }
            $record_ins->content = serialize($arr);
        }else{
            $record_ins->content = "";
        }

        $record_ins->fullname = $_POST[$fullname];
        $record_ins->cpr = $_POST[$cpr]; 
        $record_ins->email = $_POST[$email]; 
        $record_ins->mobile = $_POST[$mobile];
        $record_ins->date_of_birth = strtotime($_POST[$date_of_birth]);
        $record_ins->relation = 'child';
        $record_ins->created_date = time();
        $record_ins->updated_date = time();
        $DB->insert_record('lcl_application_form', $record_ins, true);
        $msg = 'Record inserted';
    }
    redirect($CFG->wwwroot . '/local/user_registration/application_form.php?id='.base64_encode($_POST['reg_id']), $msg,  \core\output\notification::NOTIFY_SUCCESS);
}

if ($DB->record_exists('lcl_individual_enrollment', array('registration_id' => $registration_id))) {
    $data = $DB->get_record('lcl_individual_enrollment', array('registration_id' => $registration_id));
    $course_id = $data->course_id;
    $enrollment_id = $data->id;
    $email = $data->email;
} elseif ($DB->record_exists('lcl_corporate_enrollment', array('registration_id' => $registration_id))) {
    $data = $DB->get_record('lcl_corporate_enrollment', array('registration_id' => $registration_id));
    $course_id = $data->course_id;
    $enrollment_id = $data->id;
    $email = $data->email;
}

$prerequisite = $DB->get_record('customfield_field', array('shortname' => 'prerequisite'));                                     
$prerequisitetype = $DB->get_record('customfield_field', array('shortname' => 'prerequisitetype'));
$customdata_prerequisitetype = $DB->get_record('customfield_data', array('fieldid' => $prerequisitetype->id, 'instanceid' => $course_id)); 
if($customdata_prerequisitetype) {            
    $prerequisitetype_set = $customdata_prerequisitetype->value-1;
    $prerequisitetype_data = explode("\n", json_decode($prerequisitetype->configdata)->options);
    $typenameprerequisite = trim($prerequisitetype_data[$prerequisitetype_set]);
}

$certificates = $DB->get_record('customfield_field', array('shortname' => 'certificates')); 
$customdata_prerequisite = $DB->get_record('customfield_data', array('fieldid' => $prerequisite->id, 'instanceid' => $course_id)); 
if($customdata_prerequisite->value == 2) {
    $customdata_certificates = $DB->get_record('customfield_data', array('fieldid' => $certificates->id, 'instanceid' => $course_id));                                              
    $certificate_data = $customdata_certificates->value;
    $certificate_data = explode("\n", $certificate_data);
    $arrnew = array();
    $typename = array();
    $key = 0; 
    foreach($certificate_data as $data){
        $data = trim(strip_tags($data));
        $prerequisite_data = $DB->record_exists('lcl_prerequisite_data', array('registration_id' => $reg_id, 'type'=> $key));
        $typename[$key] = $data; 
        if(!$prerequisite_data){
            if($data !=''){
              $arrnew[$key] = $data;
            }
        }
        $key++;
     }
} 
$colspan = 6 + $key;  
echo $OUTPUT->header();
?>
<form action="" method="post" enctype="multipart/form-data">
    <p class="status_validate_cpr" style="color: red;"></p>
    <p class="status_validate_mobile" style="color: red;"></p>
    <p class="status_validate_email" style="color: red;"></p>
    <table id="myTable" class="table table-responsive">
        <tr>
            <td colspan="'.$colspan.'"><input type="button" class="btn btn-success" value=" + Add another entry" onclick="addField();"></td>
        </tr>
        <tr> 
            <th>No.</th>
            <th>Full Name</th>
            <th>CPR</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Date of Birth</th>
            <?php
            if($customdata_prerequisite->value == 2){
                if($typenameprerequisite) {
                    echo "<th>Prerequisite</th>";
                    foreach($arrnew as $key => $value) {
                        echo "<th></th><th></th>";
                    }
                }
            }
            ?>
        </tr>
        <tr>
            <td><input type="text" name="sl" value="1" readonly></td>
            <td><input type="text" name="fullname1" required="required"></td>
            <td><input onkeyup="validate_email_mobile_cpr('cpr1', 'cpr');" type="text" name="cpr1" required="required"></td>
            <td><input onkeyup="validate_email_mobile_cpr('email1', 'email');" type="text" name="email1" required="required"></td>
            <td><input onkeyup="validate_email_mobile_cpr('mobile1', 'mobile');" type="text" name="mobile1" required="required"></td>
            <td><input type="date" name="date_of_birth1" required="required"></td>
            <?php
            if($customdata_prerequisite->value == 2){
                if($typenameprerequisite) {
                   foreach($arrnew as $key => $value) {
                    if($typenameprerequisite == 'File Upload'){
                        echo "<td>".$value."</td>
                              <td><input type='file' name='fileupload".$value."1' required='required'></td>";
                    }else if ($typenameprerequisite == 'Ask Question' || $typenameprerequisite == 'Personal Info') {
                        echo "<td>".$value."</td>
                              <td><input type='text' name='fileupload".$value."1'></td>";
                    }
                   }
                }
            }
            ?>
        </tr>
    </table>
    <input type="hidden" id="filedata" name="filedata" value='<?php echo json_encode($arrnew); ?>'>
    <input type="hidden" id="filetype" name="filetype" value='<?php echo $typenameprerequisite; ?>'>
    <input type="hidden" id="filetype" name="prerequisitemode" value='<?php echo $customdata_prerequisite->value; ?>'>
    <input type="hidden" name="enrollment_id" value='<?php echo $enrollment_id; ?>'>
    <input type="hidden" name="reg_id" value="<?php echo $registration_id; ?>">
    <input type="hidden" name="email" value="<?php echo base64_encode($email); ?>">
    <input type="submit" class="btn btn-primary ml-3" id="save_submit" name="submit" value="Save">
</form>
<br>
<table id="myTableView" class="table table-responsive">
    <tr> 
        <th>No.</th>
        <th>Full Name</th>
        <th>CPR</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Date of Birth</th>
        <?php
        if($customdata_prerequisite->value == 2){
            if($typenameprerequisite) {
                echo "<th>Prerequisite</th>";
                foreach($arrnew as $key => $value) {
                    echo "<th></th><th></th>";
               }
            }
        }
        ?>
    </tr>
    <?php
    $count = 1;
    $getdata = $DB->get_records('lcl_application_form', array('registration_id' => $registration_id));
    foreach ($getdata as $data) { if($count<=50) { ?>
    <tr>
        <td><input type="text"  value="<?php echo $count; ?>" readonly></td>
        <td><input type="text"  value="<?php echo $data->fullname; ?>" readonly></td>
        <td><input type="text"  value="<?php echo $data->cpr; ?>" readonly></td>
        <td><input type="text"  value="<?php echo $data->email; ?>" readonly></td>
        <td><input type="text"  value="<?php echo $data->mobile; ?>" readonly></td>
        <td><input type="date"  value="<?php if(!empty($data->date_of_birth)){ echo date('Y-m-d',$data->date_of_birth); } ?>"></td>
        <?php

        if($customdata_prerequisite->value == 2){

        if($typenameprerequisite) {
           $content = unserialize($data->content);
           foreach($arrnew as $key => $value) {
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
                                        $prerequisite_data = $DB->get_record('lcl_prerequisite_data', array('registration_id' => $registration_id, 'type'=> $key));
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
    <?php $count++; }}  ?>
</table>
<center>
<button class="btn btn-primary" id="final_submit" onclick="return final_confirm();">Next & Continue</button>
</center>

<script type="text/javascript">
    function addField (argument) {
        var myTable = document.getElementById("myTable");
        var currentIndex = myTable.rows.length-1;
        var currentRow = myTable.insertRow(-1);
        if (currentIndex <=50) {
            var filedata = $('#filedata').val();
            var filetype = $('#filetype').val();
            var jsonObject = JSON.parse(filedata);
            var sl = document.createElement("input");
            sl.setAttribute("name", "sl" + currentIndex);
            sl.setAttribute("value", currentIndex);
            var fullname = document.createElement("input");
            fullname.setAttribute("name", "fullname" + currentIndex);
            fullname.setAttribute("required", "required");
            var cpr = document.createElement("input");
            cpr.setAttribute("name", "cpr" + currentIndex);
            var cprname = "cpr" + currentIndex;
            cpr.setAttribute("onkeyup", "validate_email_mobile_cpr('"+ cprname +"', 'cpr');");
            cpr.setAttribute("required", "required");
            var email = document.createElement("input");
            email.setAttribute("name", "email" + currentIndex);
            var emailname = "email" + currentIndex;
            email.setAttribute("onkeyup", "validate_email_mobile_cpr('"+ emailname +"', 'email');");
            email.setAttribute("required", "required");
            var mobile = document.createElement("input");
            mobile.setAttribute("name", "mobile" + currentIndex);
            var mobilename = "mobile" + currentIndex;
            mobile.setAttribute("onkeyup", "validate_email_mobile_cpr('"+ mobilename +"', 'mobile');");
            mobile.setAttribute("required", "required");
            var date_of_birth = document.createElement("input");
            date_of_birth.setAttribute("name", "date_of_birth" + currentIndex);
            date_of_birth.setAttribute("type", "date");
            date_of_birth.setAttribute("required", "required");
            var hidden = document.createElement("input");
            hidden.setAttribute("type", "hidden");
            hidden.setAttribute("value", currentIndex);
            hidden.setAttribute("name", 'currentIndex');
            var currentCell = currentRow.insertCell(-1);
            currentCell.appendChild(sl);
            currentCell = currentRow.insertCell(-1);
            currentCell.appendChild(fullname);
            currentCell = currentRow.insertCell(-1);
            currentCell.appendChild(cpr);
            currentCell = currentRow.insertCell(-1);
            currentCell.appendChild(email);
            currentCell = currentRow.insertCell(-1);
            currentCell.appendChild(mobile);
            currentCell = currentRow.insertCell(-1);
            currentCell.appendChild(date_of_birth);
            jsonObject.forEach(function (value, key) {
                var keyCell = currentRow.insertCell(-1);
                keyCell.textContent = value;
                var filevalue = document.createElement("input");
                filevalue.setAttribute("name", "fileupload" + value + currentIndex);
                if(filetype =="File Upload"){
                    filevalue.setAttribute("type", "file");
                }else if(filetype =="Ask Question" || filetype =="Personal Info"){
                    filevalue.setAttribute("type", "text");
                }
                filevalue.setAttribute("required", "required");
                currentCell = currentRow.insertCell(-1);
                currentCell.appendChild(filevalue);
            });
            currentCell = currentRow.insertCell(-1);
            currentCell.appendChild(hidden);
        }
    }

   function validate_email_mobile_cpr(valname, type) {
    if(type == 'cpr'){ var cpr = $('input[name="'+ valname +'"]').val(); }
    if(type == 'email'){ var email =  $('input[name="'+ valname +'"]').val(); }
    if(type == 'mobile'){ var mobile =  $('input[name="'+ valname +'"]').val(); }
    var urltogo = "<?php echo $CFG->wwwroot; ?>/local/user_registration/validate.php";
        $.ajax({
            url: urltogo,
            type: 'POST',
            data: { val1: mobile, val2: email, val6: cpr },
            success: function(response) {
                  var responsedata = JSON.parse(response);
                  if(responsedata.mobile != null || responsedata.email != null || responsedata.cpr != null) {
                        console.warn("#save_submit true");
                        $('.status_validate_mobile').html(responsedata.mobile); 
                        $('.status_validate_cpr').html(responsedata.cpr); 
                        $('.status_validate_email').html(responsedata.email); 
                        $('#save_submit').prop('disabled', true); 
                  } 
                  if(responsedata.mobile == null && responsedata.email == null && responsedata.cpr == null) {
                        console.warn("#save_submit false");
                        $('.status_validate_mobile').html(""); 
                        $('.status_validate_cpr').html(""); 
                        $('.status_validate_email').html("");
                        $('#save_submit').prop('disabled', false); 
                  }
            }
      });
    }   

    function final_confirm() {
        var seconds = 15; 
        var userConfirmed = confirm("Are you sure you want to proceed?");
        if (userConfirmed) {
            $("#final_submit").prop("disabled", true);
            countdownTimer(seconds, function () {
                executeAjax(function (response) {
                	console.warn("Response received: " + response);
                    $("#final_submit").html("Done");
                });
            });
        }
        return false;
    }


    function countdownTimer(seconds, callback) {
        var countdown = seconds;
        var countdownInterval = setInterval(function () {
            if (countdown > 0) {
                $("#final_submit").html("Waits for: " + countdown + " seconds, you will be auto redirected.");
                countdown--;
            } else {
                $("#final_submit").html("Time's up!");
                clearInterval(countdownInterval); 
            }
        }, 1000);
        setTimeout(function () {
            clearInterval(countdownInterval); 
            callback();
        }, seconds * 1000);
    }


    function executeAjax(callback) {
        setTimeout(function () {
            var urltogo = "<?php echo $CFG->wwwroot; ?>/local/user_registration/admin/recieved.php";
            $.ajax({
                url: urltogo,
                type: 'POST',
                data: { 
                  reg_id: $("input[name='reg_id']").val(), 
                  email: $("input[name='email']").val() 
                },
                success: function(response) {
                      var responsedata = JSON.parse(response);
                      if(responsedata.statustd != null && responsedata.status != null) {
                          callback(responsedata.status);
                          window.location.href = "<?php echo $CFG->wwwroot; ?>/local/user_registration/home.php?id=<?php echo base64_encode($registration_id); ?>";
                      }
                }
            });
        }, 3000);
    }
    
    
</script>
<?php
echo $OUTPUT->footer();
