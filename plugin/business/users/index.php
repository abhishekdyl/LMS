<?php
require_once('../../../config.php');
global $CFG, $DB, $PAGE, $USER;
require_login();
$cbuuser = $DB->get_record("custom_branding_users", array("userid"=>$USER->id));
$status = $cbuuser->status; 
$id = $cbuuser->cbid;
if(empty($cbuuser->cbid)){
    redirect($CFG->wwwroot);
}
$userid = optional_param('userid', 0, PARAM_INT);
$status = optional_param('status', 0, PARAM_INT);
$field = optional_param('field', "", PARAM_TEXT);
$branding = $DB->get_record('custom_branding', array("id"=>$id));
if(!empty($userid) && !empty($field) ){
    switch ($field) {
        case 'removeusers':
            echo $userid;
            $fields = $DB->get_records_sql("select * from {user_info_field} where shortname in ('company', 'companyid')");
            foreach ($fields as $key => $fieldname) {
                $DB->set_field("user_info_data", "data", "", array("userid"=>$userid, "fieldid"=>$fieldname->id));
            }
            $status = 0;
        case 'status':
            if($olddata = $DB->get_record("custom_branding_users", array("userid"=>$userid, "cbid"=>$id))){
                $olddata->$field = $status;
                $DB->update_record('custom_branding_users', $olddata);
            } else {
                $data = new stdClass();
                $data->cbid = $id;
                $data->userid = $userid;
                $data->$field = $status;
                $DB->insert_record('custom_branding_users',$data);
            }
            redirect($CFG->wwwroot."/local/business/users/?id=".$id);
            break;
        default:
            # code...
            break;
    }
}
function update_userinfodata($userid, $fieldname, $data = "" ){
    global $DB;
    if($fieldid = $DB->get_field_sql("select id from {user_info_field} where shortname=?",array($fieldname))){
        if($olddata = $DB->get_record("user_info_data", array("userid"=>$userid, "fieldid"=>$fieldid))){
            $olddata->data = $data;
            return $DB->update_record("user_info_data", $olddata);
        } else {
            $newdata = new stdCLass();
            $newdata->userid = $userid;
            $newdata->fieldid = $fieldid;
            $newdata->data = $data;
            return $DB->insert_record("user_info_data", $newdata);
        }
    }
}
echo $OUTPUT->header();
$allusers = array();
if($branding->company_id){
    $sql = "SELECT u.*, cbu.status,cbu.isadmin 
    FROM mdl_user u 
     INNER JOIN mdl_user_info_data uid on uid.userid = u.id
     INNER JOIN mdl_user_info_field uif on uif.id = uid.fieldid AND uif.shortname = 'companyid'
     LEFT JOIN mdl_custom_branding_users cbu on cbu.cbid = ? and cbu.userid=u.id
    WHERE uid.data in (".$branding->company_id.") and u.id !=?
     order by u.firstname, u.lastname";
    $allusers =$DB->get_records_sql($sql,array($id, $USER->id));
    $compname = $DB->get_records_sql('SELECT cl.* FROM {company_list} cl WHERE cl.id in('.$branding->company_id.')', array());
    if(sizeof($compname)){
        echo "<a href='$CFG->wwwroot/local/business/'><button>Back</button></a>";
    }
}
?>
<h3>Manage users</h3>
<table class="table table-stripped">
 <thead>
        <tr> 
            <th> Firstname </th>
            <th> lastname </th>
            <th> email </th>
            <th> Status </th>
            <th> Role </th> 
            <th>Disable</th>
            <th>Remove Users</th>
        </tr>
    </thead>
<?php
foreach ($allusers as $value) {
    ?>
    <tr> 
        <td> <?php echo $value->firstname; ?> </td>
        <td> <?php echo $value->lastname; ?></td>
        <td> <?php echo $value->email; ?></td>
        <td> <?php echo ($value->status?'active':'inactive'); ?></td>
        <td> <?php echo custom_manipulate_getuserrolename($value->id); ?> </td>
        <td> <?php echo ($value->status?'<a href="'.$CFG->wwwroot.'/local/business/users/?id='.$id.'&userid='.$value->id.'&status=0&field=status"><i class="icon fa fa-eye fa-fw " title="Suspend user account" role="img" aria-label="Suspend user account"></i></a>':'<a href="'.$CFG->wwwroot.'/local/business/users/?id='.$id.'&userid='.$value->id.'&status=1&field=status"><i class="icon fa fa-eye-slash fa-fw " title="Activate user account" role="img" aria-label="Activate user account"></i></a>'); ?> 
        </td>
        <td> <?php echo ($value->status?'<a href="'.$CFG->wwwroot.'/local/business/users/?id='.$id.'&userid='.$value->id.'&status=0&field=removeusers"><button>Remove</button></a>':'<a href="'.$CFG->wwwroot.'/local/business/users/?id='.$id.'&userid='.$value->id.'&status=1&field=status"><button class="btn-inverse" >Approve</button></a>'); ?> 
        </td>
    </tr>
<?php }
echo '</table>';
function custom_manipulate_getuserrolename($userid){
    global $CFG, $DB;
    $roleid = "";
    $existing_roles = $DB->get_records_sql("select r.* from {role_assignments} as ra inner join {role} as r on ra.roleid = r.id where ra.roleid in (9,10,12,17,18,19,20,21, 22) and ra.userid = ".$userid);
    foreach ($existing_roles as $key => $roles) {
//        print_r($roles);
        $roleid = $roles->name;
    }
    return $roleid;
}
echo $OUTPUT->footer();
