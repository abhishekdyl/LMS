<?php
require_once('../../../config.php');
require_once('./edit_form.php');
global $DB, $CFG, $PAGE, $USER;
require_login();
$id=optional_param('id',0,PARAM_INT);
$userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
if(empty($userbrand)){
    redirect($CFG->wwwroot);
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

$context = context_system::instance();
$args = array(
    'userbrand' => $userbrand,
    'branding' => $branding,
);

$mform = new learning_pro(null, $args);


if($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/local/business/learning_program/");
}else if ($fromform=$mform->get_data()){
    // echo "<pre>";
    // print_r($fromform);
    // echo "</pre>";
    // die;

    $data = new stdclass();
    $data->program_name = $fromform->program_name;
    $data->introduction_message = $fromform->introduction_message;
    $data->stream1title = $fromform->stream1title;
    $data->stream1courseid = $fromform->stream1courseid;
    $data->stream2title = $fromform->stream2title;
    $data->stream2courseid = $fromform->stream2courseid;
    $data->stream3title = $fromform->stream3title;
    $data->stream3courseid = $fromform->stream3courseid;
    if(!empty($fromform->id)){
        $data->id=$fromform->id;
        $DB->update_record("business_learning_program",$data);
    } else {
        $data->cbid=$userbrand->cbid;
        $data->id=$DB->insert_record("business_learning_program",$data);
    }
    file_save_draft_area_files($fromform->mainimage, $context->id, 'learningprogram', "mainimage", $data->id);
    redirect($CFG->wwwroot."/local/business/learning_program/");
}
  
if(!empty($id)){
    $sql="SELECT * FROM {business_learning_program} WHERE cbid=? and id=?";
    $data2=$DB->get_record_sql($sql,array($userbrand->cbid, $id));
    $data2->introduction_message=$data2->introduction_message;
    $mainimage = file_get_submitted_draft_itemid('mainimage');
    file_prepare_draft_area($mainimage, $context->id, 'learningprogram', 'mainimage', $data2->id);
    $data2->mainimage = $mainimage;
    $mform->set_data($data2);
}
echo $OUTPUT->header();
?>
<style lang="">

.inputmsg .form-inline .form-control {
    width: 100%;
    max-width: 100%;
    height: 200px;
}
 
 .col-form-label.d-inline-block {
    font-size: xx-large;
}

    div#fitem_id_towers>.element {
     display: list-item;
}
div#fitem_id_towers .fitem .col-md-9, div#fitem_id_towers .fitem .col-md-3 {
    display: block !important;
    margin: 0px;
    float: unset;
    clear: both;
    padding: 10px;
}
.list-column {
    width: 33.33%;
}
@media (max-width: 768px){
    .list-column {
        width: 100%;
    }
}
</style>
<?php
echo "<a href='$CFG->wwwroot/local/business/learning_program/'><button>Back</button></a>";
$mform->display();
echo $OUTPUT->footer();
