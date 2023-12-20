<?php

/**
  Plugin Name: Permit Processor
  Description: 
  Version: 1.0.0
  Author: Suneet sharma
  Author URI: https://ldsengineers.com
 * */
add_action("admin_menu", "permit_processor");

function permit_processor() {
 $position   = 7;   
    add_menu_page('Permit Processor', 'Permit Processor', 'manage_options', 'permit_section', 'add_permit_range','dashicons-clipboard', $position );
	add_submenu_page('permit_section', 'ADD Permit Range', 'ADD Permit Range', 'manage_options', 'permit_section' );	
    add_submenu_page('permit_section', 'Assign Permit Number', 'Assign Permit Number', 'manage_options', 'list','course_completed'); 	  
    add_submenu_page('permit_section', 'Class 12', 'Class 12', 'manage_options', 'list','course_completed'); 	   
    add_submenu_page('permit_section', 'Class 13', 'Class 13', 'manage_options', 'class13','course_completed_class_13'); 
    add_submenu_page('permit_section', 'Assign permit', 'Assign permit', 'manage_options', 'assignpermit','user_assign_permit'); 
    add_submenu_page('permit_section', '', '', 'manage_options', 'assignewpermit','assign_new_permit'); 
    add_submenu_page('permit_section', '', '', 'manage_options', 'replacepermit','replace_permit'); 
    add_submenu_page('permit_section', '', '', 'manage_options', 'exportpermit','export_permit'); 
    add_submenu_page('permit_section', '', '', 'manage_options', 'upgradepermit','upgrade_permit'); 

}
require_once("add_permit_class12.php"); 
require_once("add_permit_class13.php"); 
require_once("user_assign_permit.php"); 
require_once("assign_new_permit.php"); 
require_once("replace_permit.php"); 
require_once("export_permit.php"); 
require_once("upgrade_permit.php"); 

function add_permit_range() {	
    $plugingpath = plugins_url() . "/permit_processor/permit_processor_ajax.php";	
    $plugingpath2 = plugins_url() . "/permit_processor/datatable_ajax.php";   
    global $wpdb;
    if(isset($_POST['addrange'])){
        $class_id = $_POST['class_id'];
        $start_range = $_POST['start_range'];
        $end_range = $_POST['end_range'];
        $checkclass= substr($start_range, 0, 2);
        if($class_id=='class_12' AND $checkclass!=12){
            $msg="Class 12 permit number must be start with 12";
        }else if($class_id=='class_13' AND $checkclass!=13){
            $msg="Class 13 permit number must be start with 13";
        }else if($start_range > $end_range){
            $msg="start range must be less then end range";	
        }else{
            for ($i=$start_range; $i <= $end_range; $i++) { 
                $permit_numberlist[]=$i;		  
            }
            $query_maxrang = "SELECT * FROM " .$wpdb->prefix ."permit_number WHERE class_id='". $class_id ."' ORDER BY permit_number DESC LIMIT 0,1"; 
            $max_prange = $wpdb->get_row($query_maxrang); 
            $last_assg_max_range = $max_prange->permit_number;
            $query_get_prnum = "SELECT * FROM " .$wpdb->prefix ."permit_number WHERE class_id='". $class_id ."' AND permit_number = '".$start_range."'";
            $check_recods = $wpdb->get_row($query_get_prnum);
            $table_name = $wpdb->prefix . "permit_number"; 
            if(empty($check_recods)){
                if(!empty($last_assg_max_range)){
                    if($start_range > $last_assg_max_range){
                        foreach($permit_numberlist as $permit_number){ 
                            $my_data = array(
                                'class_id'=>$class_id, 
                                'permit_number'=>$permit_number, 
                                'status'=>0, 
                                'createdtime'=>time(),
                            ); 
                            $insertrecords = $wpdb->insert($table_name,$my_data);
                            if($insertrecords == 1) { 
                                $msg="Add data successfully";
                            } else{
                                $msg="Something went wrong";
                            }

                        }     
                    }else{
                        $msg = "Start range1 is must be greater than " . $last_assg_max_range;		
                    }
                }else{
                    foreach($permit_numberlist as $permit_number){ 
                        $my_data = array(
                            'class_id'=>$class_id, 
                            'permit_number'=>$permit_number, 
                            'status'=>0, 
                            'createdtime'=>time(),
                        ); 
                        $insertrecords = $wpdb->insert($table_name,$my_data);
                        if($insertrecords == 1) { 
                            $msg="Add data successfully";
                        } else{
                            $msg="Something went wrong";
                        }

                    }
                }	 
            }else{   
                $msg = "Start range is must be greater than " . $last_assg_max_range;
            } 
        }		  
    }
    if(isset($_POST['deleterange'])){
        $class_id = $_POST['class_id'];
        $start_range = $_POST['start_range'];
        $end_range = $_POST['end_range'];
        $query_deletrange = "SELECT * FROM " .$wpdb->prefix ."permit_number WHERE class_id='". $class_id ."' AND permit_number BETWEEN " . $start_range . " AND " . $end_range . " AND user_id IS NULL"; 
        $deletrange = $wpdb->get_results($query_deletrange);

        if(!empty($deletrange)){
            foreach($deletrange as $deletpermitnumb){ 
                $sqlDel = "DELETE FROM " . $wpdb->prefix . "permit_number WHERE id='" . $deletpermitnumb->id . "'";    
                $queryDel = $wpdb->query($sqlDel);
                    if ($queryDel) {
                        $msg = "Records deleted";
                    } else {
                         $msg = "error to delete records"; 
                    }	 
            }
        }else{
            $msg = "Select currect permit range";
        }  
    }
?> 
    <div class="moodle_database container">
        <h2>Permit Range</h2>
        <?php if(!empty($msg)){ echo "<h3 style='font-size: 17px; color: #ca4a1f; font-weight:700;'>".$msg."</h3>"; } ?> 
        <form id="testimonal" class="user_form_markbtn" method="post" enctype="multipart/form-data">
            <div class="form-group page_box">
                <div class="row page_height">
                    <div class="col-md-2">
                        <label class="control-label" for="class_id">ADD Class</label>
                    </div>	  
                    <div class="col-md-6">
                        <select name="class_id" class="form-control form_width"  id="class_id" required>
                            <option value="">Select Class</option>
                            <option <?php if(!empty($_POST['class_id'])&&$_POST['class_id']=='class_12'){echo "selected"; }?> value="class_12">Class 12</option>
                            <option <?php if(!empty($_POST['class_id'])&&$_POST['class_id']=='class_13'){echo "selected"; }?> value="class_13">Class 13</option>                   
                        </select>                   
                    </div> 
                    <div class="col-md-4"></div>
                </div>
                <div class="row page_height">
                    <div class="col-md-2">
                        <label class="control-label" for="start_range">Start Range</label>
                    </div>	  
                    <div class="col-md-6">
                        <input type="text" class="form-control dbfield" id="start_range" name="start_range" value="<?php if(!empty($_POST['start_range'])){echo $_POST['start_range']; } ?>"required> 
                       

                    </div> 
                    <div class="col-md-4"></div> 
                </div>
                <div class="row page_height">
                    <div class="col-md-2">
                        <label class="control-label" for="url">END Range</label>
                    </div>	  
                    <div class="col-md-6">
                        <input type="text" class="form-control dbfield" id="end_range" name="end_range" value="<?php if(!empty($_POST['end_range'])){ echo $_POST['end_range']; }?>" required>   

                    </div> 
                    <div class="col-md-4"></div>
                </div>

                <div class="row ">
                    <div class="col-md-2">
                    </div>	  
                    <div class="col-md-6">
                        <button class="btn btn-success"  name="addrange" type="submit">Add Range</button>
                        <button class="btn btn-danger"  name="deleterange" type="submit">Delete Range</button>
                    </div> 
                    <div class="col-md-4"></div>
                </div>						
            </div>

        </form>
    <br>
    <br>
    <div class="divider"></div>
    <h3>Permit Range</h3>
    <div class="row">
        <div class="col-md-8">

            <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">

                <thead>
                    <tr>
                        <th>S No.</th>
                        <th>Class Name</th>                    
                        <th>Permit Number</th>                    
                        <th>Student Name</th>                    
                        <th>Created time</th>                    
                        <th>Assign time</th>                    
                        <th>Actions</th>
                    </tr>
                </thead> 
                <tbody>
                    
                </tbody>  
            </table>
        </div>
    </div>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <script>
        var datatables=null;
        $(document).ready(function() {
          datatables=  $('#example').DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url':'<?php echo $plugingpath2; ?>'
                },
                'columns': [
                    { data: 'id' },
                    { data: 'class_id' },
                    { data: 'permit_number' },
                    { data: 'user_id' },
                    { data: 'createdtime' },
                    { data: 'assigntime' },
                    { data: 'action' },
                ]
           });

        } );
      
        function delete_permit_number(id){
            //alert(id); 
            if (confirm("Are you sure you want to delete this Record?")) {
                jQuery.ajax({
                    type: "POST",
                    url: '<?php echo $plugingpath; ?>',
                    data: {
                        action: 'delete_permit_number',
                        id: id
                    },
                    success: function (data) {
                    //alert(data);
                   // window.location.reload();
                    datatables.ajax.reload();
                    }
                });
            } 
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" rel="stylesheet">

    <style>
        a.permit_delete {
            cursor: pointer;
            color: #ab0000;
            font-weight: bold;
        }
        .btn-assign {
            cursor: pointer;
            color: #28a745;
            font-weight: bold;
        }
        .btn-unassign {
            cursor: pointer;
            color: #4e555b;
            font-weight: bold;
        }
        #prod_cat_id {
            /*  color: #eb1111; */
            font-size: 16px;
            font-weight: bold;
        }
        #msg2{
            color:#dc3545
        }
        #msg1{
            color:#dc3545
        }
        .page_height{
            margin-bottom: 10px;
        }
        .dbfield{
            padding:6px 10px;
            box-shadow: 0 0 5px 0 #ddd;
            border: 1px solid #ddd;
            color: #333;
            font-size: 14px;
        }
        form#testimonal {
            padding-top: 20px;
        }
        i.fa.fa-eye.btn-success {
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        i.fa.fa-eye-slash.btn-danger {
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        a.btn-gray {
            padding: 10px;
            background-color: #f5902b;
            border-radius: 4px;
            cursor: pointer;
        }
        a.btn-info {
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100px;
            display: block;
            text-align: center;
        }
        select.form_width {
            max-width: 100%;
        }
    </style>	
    </div>
<?php  
}

//permit_range
function CreateTable_permit_range()
{
global $wpdb;
$wptbprmit = $wpdb->prefix . 'permit_number';

if($wpdb->get_var("show tables like '$wptbprmit'") != $wptbprmit)
{

$sql = "CREATE TABLE " . $wptbprmit . " (
  `id` int(10) NOT NULL AUTO_INCREMENT, 
  `class_id` varchar(225) NULL,
  `permit_number` varchar(225) NULL,
  `status` int(10) NULL,
  `user_id` int(10) NULL,
  `createdtime` varchar(225) NULL,
  `assigntime` varchar(225) NULL,
  PRIMARY KEY id (id) 
  );";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
}

}
//activation
register_activation_hook( __FILE__, 'CreateTable_permit_range');





 