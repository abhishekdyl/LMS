<?php
require_once('../../../config.php');
global $CFG, $DB, $PAGE, $USER;
require_login();
$PAGE->requires->jquery();
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
$delete = optional_param('delete', 0, PARAM_INT); // Course id.
$confirmhash = optional_param('confirmhash', '', PARAM_ALPHANUM); // Confirmation hash.
$pageurl = $CFG->wwwroot."/local/business/users/?id=".$id;
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

if(!empty($delete)){
    $sql = "SELECT u.*, cbu.status,cbu.isadmin, cl.name as companyname 
    FROM mdl_user u 
     INNER JOIN mdl_user_info_data uid on uid.userid = u.id
     INNER JOIN mdl_user_info_field uif on uif.id = uid.fieldid AND uif.shortname = 'companyid'
     INNER JOIN {company_list} cl ON cl.id = uid.data
     LEFT JOIN mdl_custom_branding_users cbu on cbu.cbid = :cbid and cbu.userid=u.id
    WHERE uid.data in (".$branding->company_id.") and u.id = :userid";

    $deletinguser = $DB->get_record_sql($sql, array('cbid'=>$id,'userid'=>$delete));


    if(empty($deletinguser)){
        redirect($pageurl, 'This user is not part of your private eLearning portal', null, \core\output\notification::NOTIFY_ERROR);
    } else  if ($confirmhash === md5($delete)) {
        require_sesskey();
        $fields = $DB->get_records_sql("select * from {user_info_field} where shortname in ('company', 'companyid')");
        foreach ($fields as $key => $fieldname) {
            $DB->set_field("user_info_data", "data", "", array("userid"=>$delete, "fieldid"=>$fieldname->id));
        }
        $status = 0;
        if($olddata = $DB->get_record("custom_branding_users", array("userid"=>$delete, "cbid"=>$id))){
            $olddata->status = $status;
            $DB->update_record('custom_branding_users', $olddata);
        } else {
            $data = new stdClass();
            $data->cbid = $id;
            $data->userid = $delete;
            $data->status = $status;
            $DB->insert_record('custom_branding_users',$data);
        }
        redirect($CFG->wwwroot."/local/business/users/?id=".$id);
        exit;
    } else {
        $title = "Delete user from private eLearning portal";
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        echo $OUTPUT->header();
        $strdeletecoursecheck = get_string("deletecoursecheck");
        $message = "If you proceed, this person will no longer have access to your private eLearning portal";
        $continueurl = new moodle_url('/local/business/users/index.php', array('id' => $id,'delete' => $delete, 'confirmhash' => md5($delete)));
        $continuebutton = new single_button($continueurl, get_string('delete'), 'post');
        echo $OUTPUT->confirm($message, $continuebutton, $pageurl);
        echo $OUTPUT->footer();
        exit;
    }

}

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
    $sql = "SELECT u.*, cbu.status,cbu.isadmin, CONCAT(cl.name, ' - ', cl.address) as companyname 
    FROM mdl_user u 
     INNER JOIN mdl_user_info_data uid on uid.userid = u.id
     INNER JOIN mdl_user_info_field uif on uif.id = uid.fieldid AND uif.shortname = 'companyid'
     INNER JOIN {company_list} cl ON cl.id = uid.data
     LEFT JOIN mdl_custom_branding_users cbu on cbu.cbid = ? and cbu.userid=u.id
    WHERE uid.data in (".$branding->company_id.") and u.id !=?
     order by u.firstname, u.lastname";
    $allusers =$DB->get_records_sql($sql,array($id, $USER->id));
    // $compname = $DB->get_records_sql('SELECT cl.* FROM {company_list} cl WHERE cl.id in('.$branding->company_id.')', array());
    // if(sizeof($compname)){
        echo "<a href='$CFG->wwwroot/local/business/'><button>Back</button></a>";
    // }
}
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
<h3>Manage users</h3>
<table class="table table-stripped" id="sort_table">
 <thead>
        <tr> 
            <th> Firstname </th>
            <th> Surname </th>
            <!-- <th> email </th> -->
            <!-- <th> Status </th> -->
            <th> Clinic </th> 
            <th> Role </th> 
            <th style="text-align: center;">Access</th>
            <th style="text-align: center;">Delete</th>
        </tr>
    </thead>
<?php
foreach ($allusers as $value) {
    // echo "<pre>";
    // print_r($value);
    // die;
    ?>
    <tr> 
        <td> <?php echo $value->firstname; ?> </td>
        <td> <?php echo $value->lastname; ?></td>
        <!-- <td> <?php echo $value->email; ?></td> -->
        <td> <?php echo $value->companyname; ?></td>
        <!-- <td> <?php //echo ($value->status?'active':'inactive'); ?></td> -->
        <td> <?php echo custom_manipulate_getuserrolename($value->id); ?> </td>
        <!--<a href="'.$CFG->wwwroot.'/local/business/users/?id='.$id.'&userid='.$value->id.'&status=0&field=removeusers"><button>Approved</button></a> -->
        <td style="text-align: center;"> <?php echo ($value->status?'Approved':'<a href="'.$CFG->wwwroot.'/local/business/users/?id='.$id.'&userid='.$value->id.'&status=1&field=status"><button class="btn btn-primary" >Approve</button></a>'); ?> 
        </td>
        <td style="text-align: center;"> <?php echo '<a href="'.$CFG->wwwroot.'/local/business/users/index.php?id='.$id.'&delete='.$value->id.'&sesskey='.sesskey().'" class="btn btn-danger del">X</a>'; ?></td>
    </tr>
<?php }
echo '</table>';


function custom_manipulate_getuserrolename($userid){
    global $CFG, $DB;
    $roleid = "";
    $existing_roles = $DB->get_records_sql("select r.* from {role_assignments} as ra inner join {role} as r on ra.roleid = r.id where ra.roleid >= 9 and ra.userid = ".$userid);
    foreach ($existing_roles as $key => $roles) {
//        print_r($roles);
        $roleid = $roles->name;
    }
    return $roleid;
}
?>
<!-- <div class="container">

  <div class="modal" id="myModal" style="display:none">
      <div class="modal-dialog">   
          <div class="modal-content">

              <div class="modal-header">
              <h4 class="modal-title">Confirmation</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>

              <div class="modal-body">
              If you proceed, this person will no longer have access to your private eLearning portal
              </div>
              
              <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
              <button class="btn btn-danger">Proceed</button>
              </div>
          </div>
      </div>
  </div>

</div> -->

<script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#sort_table').DataTable({
            "pageLength": 100
        });
    });
</script>

<?php
echo $OUTPUT->footer();
?>
