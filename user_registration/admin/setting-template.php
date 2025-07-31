<?php

require_once('../../../config.php');
global $DB, $USER, $PAGE, $OUTPUT;
$PAGE->requires->jquery();
require_login();
// $PAGE->set_pagelayout('standard');
$type = required_param('type', PARAM_RAW);
$pageurl = $CFG->wwwroot."/local/user_registration/admin/setting-template.php?type=".$type;
$PAGE->set_url($CFG->wwwroot.'/local/user_registration/admin/setting-template.php?type='.$type);
$PAGE->set_title('Assessor Panel');
$context = \context_system::instance();
$has_capability = has_capability('local/user_registration:assessor_access', $context, $USER->id);
if (!$has_capability) {
    $urltogo_dashboard = $CFG->wwwroot.'/local/user_registration/';
    redirect($urltogo_dashboard, 'You do not have permission to view this page', null, \core\output\notification::NOTIFY_WARNING);
}
$type = base64_decode($type);
if($type == 1){ $typename = ' - Individual'; }
if($type == 2){ $typename = ' - Corporate'; }
$get_data = $DB->get_records('lcl_email_template', array('type' => $type));

$data_acceptance = ""; 
$data_modification = ""; 
$data_rejection = ""; 
$data_confirmation =  ""; 
$data_recieved =  ""; 
$data_reminder =  ""; 

$data_acceptance_subject = ""; 
$data_modification_subject = ""; 
$data_rejection_subject = ""; 
$data_confirmation_subject =  ""; 
$data_recieved_subject =  ""; 
$data_reminder_subject =  ""; 

foreach ($get_data as $value) {
  if($value->template_type == 1) { $data_acceptance = trim($value->template); $data_acceptance_subject = trim($value->subject); }
  if($value->template_type == 2) { $data_modification = trim($value->template); $data_modification_subject = trim($value->subject); }
  if($value->template_type == 3) { $data_rejection = trim($value->template); $data_rejection_subject = trim($value->subject); }
  if($value->template_type == 4) { $data_confirmation =  trim($value->template); $data_confirmation_subject =  trim($value->subject); }
  if($value->template_type == 5) { $data_recieved =  trim($value->template); $data_recieved_subject =  trim($value->subject); }
  if($value->template_type == 6) { $data_reminder =  trim($value->template); $data_reminder_subject =  trim($value->subject); }
}

if(isset($_POST['submit1'])){
  $subject = htmlentities($_POST['subject']);
  $template = htmlentities($_POST['template']);
  $acceptance_letter = htmlentities($_POST['acceptance_letter']);
  $data = new stdClass();
  if(!$DB->record_exists('lcl_email_template', array('template_type' => $acceptance_letter, 'type' => $type))){
    $data->subject = $subject;
    $data->template = $template;
    $data->type = $type;
    $data->template_type = $acceptance_letter;
    $data->created_date = time(); 
    $data->updated_date = time();
    $DB -> insert_record('lcl_email_template', $data, false); 
  } else {
    $dataid = $DB->get_record('lcl_email_template', array('template_type' => $acceptance_letter, 'type' => $type));
    $data->id = $dataid->id;
    $data->subject = $subject;
    $data->template = $template;
    $data->updated_date = time();
    $DB -> update_record('lcl_email_template', $data, false); 
  }
 redirect($pageurl);
}
if(isset($_POST['submit2'])){
  $subject = htmlentities($_POST['subject']);
  $template = htmlentities($_POST['template']);
  $modification_letter = htmlentities($_POST['modification_letter']);
  $data = new stdClass();
  if(!$DB->record_exists('lcl_email_template', array('template_type' => $modification_letter, 'type' => $type))){
    $data->subject = $subject;
    $data->template = $template;
    $data->type = $type;
    $data->template_type = $modification_letter;
    $data->created_date = time(); 
    $data->updated_date = time();
    $DB -> insert_record('lcl_email_template', $data, false); 
  } else {
    $dataid = $DB->get_record('lcl_email_template', array('template_type' => $modification_letter, 'type' => $type));
    $data->id = $dataid->id;
    $data->subject = $subject;
    $data->template = $template;
    $data->updated_date = time();
    $DB -> update_record('lcl_email_template', $data, false); 
  }
 redirect($pageurl);
}
if(isset($_POST['submit3'])){
  $subject = htmlentities($_POST['subject']);
  $template = htmlentities($_POST['template']);
  $rejection_letter = htmlentities($_POST['rejection_letter']);
  $data = new stdClass();
  if(!$DB->record_exists('lcl_email_template', array('template_type' => $rejection_letter, 'type' => $type))){
    $data->subject = $subject;  
    $data->template = $template;
    $data->type = $type;
    $data->template_type = $rejection_letter;
    $data->created_date = time(); 
    $data->updated_date = time();
    $DB -> insert_record('lcl_email_template', $data, false); 
  } else {
    $dataid = $DB->get_record('lcl_email_template', array('template_type' => $rejection_letter, 'type' => $type));
    $data->id = $dataid->id;
    $data->subject = $subject;
    $data->template = $template;
    $data->updated_date = time();
    $DB -> update_record('lcl_email_template', $data, false); 
  } 
 redirect($pageurl);
}
if(isset($_POST['submit4'])){
  $subject = htmlentities($_POST['subject']);
  $template = htmlentities($_POST['template']);
  $confirmation_letter = htmlentities($_POST['confirmation_letter']);
  $data = new stdClass();
  if(!$DB->record_exists('lcl_email_template', array('template_type' => $confirmation_letter, 'type' => $type))){
    $data->subject = $subject;
    $data->template = $template;
    $data->type = $type;
    $data->template_type = $confirmation_letter;
    $data->created_date = time(); 
    $data->updated_date = time();
    $DB -> insert_record('lcl_email_template', $data, false); 
  } else {
    $dataid = $DB->get_record('lcl_email_template', array('template_type' => $confirmation_letter, 'type' => $type));
    $data->id = $dataid->id;
    $data->subject = $subject;
    $data->template = $template;
    $data->updated_date = time();
    $DB -> update_record('lcl_email_template', $data, false); 
  } 
 redirect($pageurl);
}

if(isset($_POST['submit5'])){
  $subject = htmlentities($_POST['subject']);
  $template = htmlentities($_POST['template']);
  $recieved_letter = htmlentities($_POST['recieved_letter']);
  $data = new stdClass();
  if(!$DB->record_exists('lcl_email_template', array('template_type' => $recieved_letter, 'type' => $type))){
    $data->subject = $subject;
    $data->template = $template;
    $data->type = $type;
    $data->template_type = $recieved_letter;
    $data->created_date = time(); 
    $data->updated_date = time();
    $DB -> insert_record('lcl_email_template', $data, false); 
  } else {
    $dataid = $DB->get_record('lcl_email_template', array('template_type' => $recieved_letter, 'type' => $type));
    $data->id = $dataid->id;
    $data->subject = $subject;
    $data->template = $template;
    $data->updated_date = time();
    $DB -> update_record('lcl_email_template', $data, false); 
  } 
 redirect($pageurl);
}

if(isset($_POST['submit6'])){
  $subject = htmlentities($_POST['subject']);
  $template = htmlentities($_POST['template']);
  $payment_reminder = htmlentities($_POST['payment_reminder']);
  $data = new stdClass();
  if(!$DB->record_exists('lcl_email_template', array('template_type' => $payment_reminder, 'type' => $type))){
    $data->subject = $subject;
    $data->template = $template;
    $data->type = $type;
    $data->template_type = $payment_reminder;
    $data->created_date = time(); 
    $data->updated_date = time();
    $DB -> insert_record('lcl_email_template', $data, false); 
  } else {
    $dataid = $DB->get_record('lcl_email_template', array('template_type' => $payment_reminder, 'type' => $type));
    $data->id = $dataid->id;
    $data->subject = $subject;
    $data->template = $template;
    $data->updated_date = time();
    $DB -> update_record('lcl_email_template', $data, false); 
  } 
 redirect($pageurl);
}


$note = '<div style="text-align: justify;" class="text-danger">Note: <b>{a->name}</b> to show dynamic aplicant name in the mail during the process of application, <b>{a->link}</b> will act as payment link in acceptance letter mail and modification link in modification letter in the mail template, <b>{a->modification}</b> refers the modifications suggested in the mail, <b>{a->table}</b> will work in confirmation letter section in the mail to show the delegates credentials.</b></div>';

$html = '
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<style>
section#main-body-form .nav-link {
    margin: 0 20px;
    color: #000;
    font-size: 18px;
    font-weight: bold;
}.area {
    margin: 2px;
}
</style>
<h3><i class="fa-solid fa-bars pr-2" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"></i> Assessor Panel</h3>
<div class="collapse" id="collapseExample">
<ul>
 <li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/index.php">Home</a></li>
 <li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/setting-template.php?type='.base64_encode(1).'">Email template individual</a></li>
 <li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/setting-template.php?type='.base64_encode(2).'">Email template corporate</a></li>
 <li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/setting-logo-address.php?type='.base64_encode(3).'">Email Logo , Address</a></li>
</ul>
</div>
<br>
<h4>Set Email Template '.$typename.'<div></h4>
<hr>
<div class="cst-cont mt-5">
   <nav>
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Acceptance Letter</button>
        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Modification Letter</button>
        <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Rejection Letter</button>
        <button class="nav-link" id="nav-contact-tab1" data-bs-toggle="tab" data-bs-target="#nav-contact1" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Confirmation Letter</button>
            <button class="nav-link" id="nav-contact-tab2" data-bs-toggle="tab" data-bs-target="#nav-contact2" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Recieved Letter</button>
        <button class="nav-link" id="nav-contact-tab3" data-bs-toggle="tab" data-bs-target="#nav-contact3" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Payment Reminder</button>
      </div>
   </nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        <form class="form w-100" action="" method="post">
          <label class="m-1">Subject</label>
          <input type="text" name="subject" value="'.$data_acceptance_subject.'" class="form-control mt-1">
          <label class="m-1">Body</label>
          <textarea class="form-control area" rows="8" name="template">'.$data_acceptance.'</textarea>
          <input type="hidden" name="acceptance_letter" value="1">
          '.$note.'
          <button type="submit" name="submit1" class="btn btn-primary mt-5">Save</button>
        </form>
    </div>
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
      <div class="header-nav d-flex justify-content-between">
          <form class="form w-100" action="" method="post">
            <label class="m-1">Subject</label>
            <input type="text" name="subject" value="'.$data_modification_subject.'" class="form-control mt-1">
            <label class="m-1">Body</label>
            <textarea class="form-control area" rows="8" name="template">'.$data_modification.'</textarea>
            <input type="hidden" name="modification_letter" value="2">
            '.$note.'
            <button type="submit" name="submit2" class="btn btn-primary mt-5">Save</button>
          </form>
      </div>
    </div>
    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
      <div class="header-nav d-flex justify-content-between">
          <form class="form w-100" action="" method="post">
            <label class="m-1">Subject</label>
            <input type="text" name="subject" value="'.$data_rejection_subject.'" class="form-control mt-1">
            <label class="m-1">Body</label>
            <textarea class="form-control area" rows="8" name="template">'.$data_rejection.'</textarea>
            <input type="hidden" name="rejection_letter" value="3">
            '.$note.'
            <button type="submit" name="submit3" class="btn btn-primary mt-5">Save</button>
          </form>
      </div>
    </div>
    <div class="tab-pane fade" id="nav-contact1" role="tabpanel" aria-labelledby="nav-contact-tab">
     <div class="header-nav d-flex justify-content-between">    
        <form class="form w-100" action="" method="post">
          <label class="m-1">Subject</label>
          <input type="text" name="subject" value="'.$data_confirmation_subject.'" class="form-control mt-1">
          <label class="m-1">Body</label>
          <textarea class="form-control area" rows="8" name="template">'.$data_confirmation.'</textarea>
          <input type="hidden" name="confirmation_letter" value="4">
          '.$note.'
          <button type="submit" name="submit4" class="btn btn-primary mt-5">Save</button>
        </form>
     </div>
    </div>
    <div class="tab-pane fade" id="nav-contact2" role="tabpanel" aria-labelledby="nav-contact-tab">
     <div class="header-nav d-flex justify-content-between">    
        <form class="form w-100" action="" method="post">
          <label class="m-1">Subject</label>
          <input type="text" name="subject" value="'.$data_recieved_subject.'" class="form-control mt-1">
          <label class="m-1">Body</label>
          <textarea class="form-control area" rows="8" name="template">'.$data_recieved.'</textarea>
          <input type="hidden" name="recieved_letter" value="5">
          '.$note.'
          <button type="submit" name="submit5" class="btn btn-primary mt-5">Save</button>
        </form>
     </div>
    </div>
    <div class="tab-pane fade" id="nav-contact3" role="tabpanel" aria-labelledby="nav-contact-tab">
     <div class="header-nav d-flex justify-content-between">    
        <form class="form w-100" action="" method="post">
          <label class="m-1">Subject</label>
          <input type="text" name="subject" value="'.$data_reminder_subject.'" class="form-control mt-1">
          <label class="m-1">Body</label>
          <textarea class="form-control area" rows="8" name="template">'.$data_reminder.'</textarea>
          <input type="hidden" name="payment_reminder" value="6">
          '.$note.'
          <button type="submit" name="submit6" class="btn btn-primary mt-5">Save</button>
        </form>
     </div>
    </div>
</div>';
echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();