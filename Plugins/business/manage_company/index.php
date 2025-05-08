<?php 

require_once('../../../config.php');
global $CFG, $DB, $PAGE, $USER; 
require_login();
$PAGE->requires->jquery();
$html = '';
$cbuuser = $DB->get_record("custom_branding_users", array("userid"=>$USER->id));
$status = $cbuuser->status; 
$cbidval = $cbuuser->cbid;
if(empty($cbuuser->cbid)){
    redirect($CFG->wwwroot);
}
$cbid = $DB->get_record("custom_branding", array("id"=>$cbuuser->cbid));
if(empty($cbid->company_id)){
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/accesslib.php');
$brandingcontext = context_coursecat::instance($cbid->brand_category);
$roleid = $DB->get_field_sql("select id from {role} where shortname=?", array("companyadmin"));
if(empty($roleid)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
if(!user_has_role_assignment($USER->id, $roleid, $brandingcontext->id)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}

$compname = $DB->get_records_sql('SELECT cl.* FROM {company_list} cl WHERE cl.id in('.$cbid->company_id.')', array());
// echo "<pre>";
// print_r($key->name);

if(isset($_POST['cancel'])){
    redirect($CFG->wwwroot."/local/business/");
}

if(isset($_POST['submit'])){
    $data = new stdclass;
    $data->comp_name = $_POST['comp_name'];
    $data->comp_address = $_POST['comp_address'];
    $data->status = $status;
    $data->cbid = $cbidval;
    $data->createdby= $USER->id;
    $data->createddate= time();
    $aa = $DB->insert_record("custom_branding_request",$data );

    $messages = '<html><body><table style="background: #fff;
        border: 5px solid #004282;
        width: 600px;
        margin: auto;
        padding: 7px;">
       <tr style="text-align:center;"><td colspan="2"><img src="'.$CFG->wwwroot.'/theme/lambda/pix/biahimg/biahvms-logo.png" width="200"></td></tr>
       <tr style="text-align:center;"><td colspan="2"><h3 style="    margin: 0px;
        background: #ccc;
        padding: 6px;">Company change request</h3></td></tr>
       <tr><td><strong>Company name: </strong></td><td>'.$_POST['comp_name'].'</td></tr>
       <tr><td><strong>Address: </strong></td><td>'.$_POST['comp_address'].'</td></tr>
       <tr><td><strong>action: </strong></td><td>'.$_POST['needto'].'</td></tr>
        </table></body></html>';
    $subject = "Company change request";
    $to = "AHAnimalHealthAcademy.AU@boehringer-ingelheim.com";
    $emailuser->email = $to;
    $emailuser->maildisplay = true;
    $emailuser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
    $emailuser->id = -1;
    $emailuser->firstnamephonetic = false;
    $emailuser->lastnamephonetic = false;
    $emailuser->middlename = false;
    $emailuser->alternatename = false;
    $fromUser = $USER->email;
    $s = email_to_user($emailuser, $fromUser, $subject, $message = '', $messages, $attachment = '', $attachname = '', $usetrueaddress = true, $replyto = $USER->email, $replytoname = '', $wordwrapwidth = 70);
    $ccemail = "adamhill@fastmail.com.au";
    $emailuser->email = $ccemail;
    $s = email_to_user($emailuser, $fromUser, $subject, $message = '', $messages, $attachment = '', $attachname = '', $usetrueaddress = true, $replyto = $USER->email, $replytoname = '', $wordwrapwidth = 70); 
    $ccemail = $CFG->testemail;
    $s = email_to_user($emailuser, $fromUser, $subject, $message = '', $messages, $attachment = '', $attachname = '', $usetrueaddress = true, $replyto = $USER->email, $replytoname = '', $wordwrapwidth = 70);  
    redirect($CFG->wwwroot."/local/business/manage_company/", 'New clinics will be added by Boehringer Ingelheim within 2 business days.', null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo "<div style='padding:20px 40px;'><a href='$CFG->wwwroot/local/business/'><button style='color:#fff;background-color:#999 !important;'>Back</button></a></div>";

$html .='
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">

<form method="POST">
            <div class="p-10 my-50" style="padding:20px 40px;">
                <h3>Sumbit request to add/remove a company in your private group</h3>
                <div>
                    <label class="span2" >Company Address : </label>
					<div class="span10">
                    <input type="text" id="address" class="w-100" required name="comp_address"  >
					</div>
                </div>

                <div>
                    <label class="span2" >Company Name : </label> 
					<div class="span10">
                    <input type="text" id="name" class="w-100" required name="comp_name"  >
					</div>
                </div>             
                <div>
                    <label class="span2" ></label>
                    <div class="span10">
					<table style="margin-bottom:35px;">
						<tr>
							<td><label ><input type="radio" name="needto" checked value="Add company" class="p-3" > Add</label></td>
							<td style="padding-left:40px;"><label ><input type="radio" name="needto" value="Remove company" class="p-3" >Remove</label></td>
						</tr>
					</table>
                    <button type="submit" name="submit">Submit</button>
                    </div>
                </div>
            </div>
        </form>
'; 

$html .='

<div style="padding:40px 40px 20px 40px;clear: both;">
<h3>Clinics in your group</h3>

<table id="sort_table" class="table table-stripped">
 <thead>
        <tr>
        <th>ID</th>
        <th>Name & Address</th>
        </tr>
 </thead>
        ';

foreach ($compname as $key ) {
$html .='<tr>';
$html .='<td>'.$key->id.'</td>';
$html .='<td>'.$key->name.' - '.$key->address.'</td>';
$html .='</tr>';
}
$html .= '</table></div>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#sort_table").DataTable({
    	paging: false
    	});
    });
</script>
';
$html.= '<style>
div#sort_table_info {
    display: none;
}
</style>';
echo $html;


echo $OUTPUT->footer();

?>