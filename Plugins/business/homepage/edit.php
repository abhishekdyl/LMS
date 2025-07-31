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

$mform = new homepage_form(null, $args);


if($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/local/business/");
}else if ($fromform=$mform->get_data()){
    $data = $DB->get_record("business_learning_homapage", array("cbid"=>$userbrand->cbid));
    $settingdata = array();
    for ($i=0; $i < 3; $i++) { 
        $headingkey = "qlinkinput{$i}heading";
        $headingname = "qlinkinput{$i}name";
        $headinglink = "qlinkinput{$i}link";
        if(!isset($settingdata[$i])){
            $settingdata[$i] = array();
        }
        $settingdata[$i] = array("heading"=>$_POST[$headingkey], "linkname"=>$_POST[$headingname], "url"=>$_POST[$headinglink]);
    }
    if(empty($data)){
        $data = new stdclass();
        $data->cbid=$userbrand->cbid;
    }
    $data->cbid=$userbrand->cbid;
    $data->settingdata = json_encode($settingdata);
    $data->introductiontext = $fromform->introductiontext['text'];
    if(!empty($data->id)){
        $data->modifiedby = $USER->id;
        $data->modifieddate = time();
        $DB->update_record("business_learning_homapage",$data);
    } else {
        $data->createdby = $USER->id;
        $data->createddate = time();
        $data->id=$DB->insert_record("business_learning_homapage",$data);
    }
    file_save_draft_area_files($fromform->bannerimage, $context->id, 'businessfront', "bannerimage", $data->id);
    redirect($CFG->wwwroot."/local/business/homepage/edit.php");
}
$sql="SELECT * FROM {business_learning_homapage} WHERE cbid=?";
$olddata=$DB->get_record_sql($sql,array($userbrand->cbid));
if($olddata){
    $bannerimage = file_get_submitted_draft_itemid('bannerimage');
    file_prepare_draft_area($bannerimage, $context->id, 'businessfront', 'bannerimage', $olddata->id);
    $olddata1 = new stdClass();
    $olddata1->bannerimage = $bannerimage;
    $olddata1->introductiontext = array("text"=>$olddata->introductiontext);
    $mform->set_data($olddata1);
}
$PAGE->set_title("Edit your Home page");
echo $OUTPUT->header();
?>
<style lang="">
.smalltxt {
    font-size: 11px;
    line-height: 1.6;
    display: inline-block;
}
.w100 {
    width: 100%;
}
</style>
<?php
echo "<a href='$CFG->wwwroot/local/business/'><button>Back</button></a>";
$mform->display();
echo $OUTPUT->footer();
