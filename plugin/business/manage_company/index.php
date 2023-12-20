<?php 

require_once('../../../config.php');
global $CFG, $DB, $PAGE, $USER; 
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
//    echo "<pre>";
//     print_r ($data);
//     die;
redirect($CFG->wwwroot."/local/business/manage_company/?userid=".$USER->id);
}

echo $OUTPUT->header();
$html .='<form method="POST">
            <div class="p-10 my-50">
                <h3>Request New Company</h3>
                <div>
                    <label class="span3" >Company Name : </label> 
                    <input type="text" id="name" class="w-50" name="comp_name" class="w-50 p-3 span9" >
                </div>
                <div>
                    <label class="span3" >Company Address : </label>
                    <input type="text" id="address" class="w-50" name="comp_address" class="w-50 p-3 span9" >
                </div>
                <br/>
                <button type="submit" name="submit">Submit</button>
                <button type="submit" name="cancel">Cancel</button>
            </div>
        </form>
'; 

$html .='
<h3>Existing Company</h3>

<table class="table table-stripped">
        <tr>
        <th>id</th>
        <th>name</th>
        <th>address</th>
        </th>';

foreach ($compname as $key ) {
$html .='<tr>';
$html .='<td>'.$key->id.'</td>';
$html .='<td>'.$key->name.'</td>';
$html .='<td>'.$key->address.'</td>';
$html .='</tr>';
}
$html .= '</table>'; 
echo $html;


echo $OUTPUT->footer();

?>