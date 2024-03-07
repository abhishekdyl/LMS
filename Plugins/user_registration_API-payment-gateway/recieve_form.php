<?php

require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once("$CFG->libdir/filelib.php");
global $DB, $USER, $PAGE, $CFG;

$PAGE->requires->jquery();
// $PAGE->set_pagelayout('standard');

$id = optional_param('id', '', PARAM_RAW);
$registration_id = base64_decode($id);

$editid = optional_param('editid', '0', PARAM_INT);

$pageurl = $CFG->wwwroot . "/local/user_registration/recieve_form.php?id=" . $id;
$args = array("id" => $editid, "rg_id"=>$registration_id);
$PAGE->set_title('Registration Form - Application Form');
$PAGE->set_heading('<div>Course Registration Form- Recieve Documents</div>');
$PAGE->set_url($CFG->wwwroot.'/local/user_registration/recieve_form.php?id='.$id);


$chk_data = $DB->get_record_sql('SELECT lr.id,c.id as cid, c.fullname, c.startdate, lce.client_name FROM {lcl_registration} lr 
INNER JOIN {lcl_corporate_enrollment} as lce ON lr.id = lce.registration_id
INNER JOIN {course} as c ON lce.course_id = c.id
WHERE lr.id = '.$registration_id);

echo $OUTPUT->header();

if (isset($_POST['submit'])) {
    $record_ins = new stdClass();
    if (count($_POST['certificate'])>0) {
        $record_ins->recipient_name = $_POST['recipient_name']; 
        $record_ins->company = $_POST['company'];
        $record_ins->course_name = $_POST['course_name']; 
        $record_ins->course_date = $_POST['course_date']; 
        $record_ins->certificate = implode(',',$_POST['certificate']);
        $record_ins->recieved_by = $_POST['recieved_by'];
        $record_ins->telephone = $_POST['telephone'];
        $record_ins->signature = $_POST['signature'];
        $record_ins->registration_id = $_POST['reg_id'];
        $record_ins->created_date = time();
        $record_ins->updated_date = time();
        $DB->insert_record('lcl_recieve_form', $record_ins, true);
        $msg = 'Record inserted';
        redirect($CFG->wwwroot . '/local/user_registration/application_form.php?id='.base64_encode($_POST['reg_id']), $msg,  \core\output\notification::NOTIFY_SUCCESS);
    } else {
        $msg = 'Something is wrong !';
        redirect($CFG->wwwroot . '/local/user_registration/recieve_form.php?id='.base64_encode($_POST['reg_id']), $msg,  \core\output\notification::NOTIFY_SUCCESS);
    }
} 

?>

<style>
    .text {
      width: 30%;
      padding: 12px 20px;
      margin: 8px 0;
      box-sizing: border-box;
      border: none;
      border-bottom: 1px solid black;
    }.text_d {
      width: 30%;
      padding: 12px 20px;
      margin: 8px 0px;
      box-sizing: border-box;
      border: none;
      border-bottom: 1px solid black;
    }
</style>

<form action="" method="post">
<div class="row">
<div class="col-md-12" align="center"><h3>Confirmation of Received Documents</h3></div>
    
    
   <div class="col-md-12">
    I, <input type="text" name="recipient_name" class="text" value="<?php echo $chk_data->client_name; ?>"  required> (recipient name) of <input type="text" name="company" class="text" required> (company), have recieved the following documents:
   </div>

   <div class="col-md-12"><b>(A). </b><input type="text" name="course_name" value="<?php echo $chk_data->fullname;  ?>" class="text_d" required readonly>(course name) 
   <input type="date" value="<?php if($chk_data->startdate != 0){ echo date('Y-m-d', $chk_data->startdate); }else{ echo ''; } ?>"  name="course_date" class="text_d" required readonly>(coursedate)</div>


    <?php 

    $prerequisite = $DB->get_record('customfield_field', array('shortname' => 'prerequisite'));                                     
    $certificates = $DB->get_record('customfield_field', array('shortname' => 'certificates'));   
    $customdata_prerequisite = $DB->get_record('customfield_data', array('fieldid' => $prerequisite->id, 'instanceid' => $chk_data->cid));

    if($customdata_prerequisite->value == 2) {
        $customdata_certificates = $DB->get_record('customfield_data', array('fieldid' => $certificates->id, 'instanceid' => $chk_data->cid));
        $certificate_data = $customdata_certificates->value;
        $certificate_data = explode("\n", $certificate_data);
        $arrnew = array();
        $key = 0; 
        foreach($certificate_data as $data){
             $data = trim(strip_tags($data));
             $prerequisite_data = $DB->record_exists('lcl_prerequisite_data', array('registration_id' => $registration_id, 'type'=> $key));
             $typename[$key] = $data; 
             if(!$prerequisite_data){
                if($data !=''){
                    $arrnew[$key] = $data;
                }
             }
             $key++;
        }
    } 

   echo '<div class="col-md-12 mt-5" align="left">License/ Certificates : Tick the checkbox <i class="fa fa-check" style="color: green;"></i></div><br>';
   $i=1;
   foreach($arrnew as $key => $value) {
        echo '<div class="col-md-12 mt-1" align="left"> <input type="checkbox" name="certificate[]" value="'.$key.'" required> '.$value.'</div>';
        $i++; 
   }
   
   ?>

    <div class="col-md-12" align="left"><span style="font-weight: bold;">Recieved By: </span><input type="text" name="recieved_by" value="" class="text_d"> <span class="text-danger">(required)</span></div>
    <div class="col-md-12" align="left"><span style="font-weight: bold;">Tel: </span><input type="text" name="telephone" value="" class="text_d"> <span class="text-danger">(required)</span></div>
    <div class="col-md-12" align="left"><span style="font-weight: bold;">Signature: </span><input type="text" name="signature" value="" class="text_d"> <span class="text-danger">(required)</span></div>

    <input type="hidden" name="reg_id" value="<?php echo $registration_id; ?>">
    <div class="col-md-12" align="center"><input type="submit" class="btn btn-primary" name="submit" value="Submit"></div>
    
 </div>
</form>




<?php

echo $OUTPUT->footer();
