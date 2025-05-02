<?php
add_action("admin_menu", "custom_user_report");

function custom_user_report() {
    add_menu_page('Export User Report', 'User Report', 'manage_options', 'expreport', 'exportUserReport', 'dashicons-menu', 6);
    add_submenu_page('expreport', 'WSLCB Export', 'WSLCB Export', 'manage_options', 'exreport', 'wslcbexport');

} 
function wslcbexport() {
    global $wpdb;
    $sql = 'SELECT u.*, um.meta_value as birthday FROM '. $wpdb->prefix .'users u JOIN '. $wpdb->prefix .'usermeta um ON u.ID = um.user_id AND um.meta_key LIKE "%date_of_birth%" WHERE u.ID >1 ORDER BY um.meta_value DESC';
    $users = $wpdb->get_results($sql);
	date_default_timezone_set( 'America/Los_Angeles' );
   date_default_timezone_get();
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">   
<div class="report-field row" style="margin-top:20px;">
    <div class="col-md-2">
        <div class='margin_top button mybtn'>  
            <a href="#" id ="export" role='button'>WSLCB Export
            </a>
        </div>
    </div>
    <form id="user_form_report" class="user_form_markbtn col-md-10" method="post">
        <div class="row">
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-md-3 ">
                        <div class="wrapper_enrollment"><label class="enrol_field">From </label>
                            <input id="datepickerFrom" class="enrol_input" name="date_to"  type="text" value="<?php if(!empty($_POST['date_to'])){echo $_POST['date_to']; }?>">
							</div>
                    </div>	
                    <div class="col-md-3 ">
                        <div class="wrapper_enrollment"><label class="enrol_field">To </label>
                            <input id="datepickerTo" class="enrol_input" name="date_from"  type="text" value="<?php if(!empty($_POST['date_from'])){echo $_POST['date_from']; }?>">
							</div>
                    </div>	
                    <div class="col-md-2 ">
                        <div class="wrapper_enrollment"><label class="enrol_field">Permit Issue </label>
                            <select  class="enrol_select_btn enrol_input" id="permit_issue" name="permit" > 
                                <option
								<?php if(!empty($_POST['permit'])&&$_POST['permit']=='1'){
									echo "selected"; }?>
								value="1"> Select One </option>
                                <option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='permit_issued_option_yes'){echo "selected"; }?> value="permit_issued_option_yes"> Yes </option>
                                <option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='permit_issued_option_no'){echo "selected"; }?> value="permit_issued_option_no"> No </option> 
                                <option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='null'){echo "selected"; }?> value="null"> Null </option> 
                            </select>
                        </div>
                    </div>	
                    <div class="col-md-2 ">
                        <div class="wrapper_enrollment"><label class="enrol_field">Order by age </label>
                            <select  class="enrol_select_btn enrol_input" id="order_issue" name="order" > 
                                <option
								<?php if(!empty($_POST['order'])&&$_POST['order']=='1'){echo "selected"; }?>value=" "> Select One </option>
                                <option <?php if(!empty($_POST['order'])&&$_POST['order']=='asc'){echo "selected"; }?> value="asc"> Ascending </option>
                                <option <?php if(!empty($_POST['order'])&&$_POST['order']=='desc'){echo "selected"; }?> value="desc"> Descending </option> 
                            </select>
                        </div>
                    </div>		
                    <div class="col-md-2 ">
                        <div class="margin_top">
                            <input type="checkbox" <?php if(!empty($_POST['coursecmplts'])){echo "checked"; }?> value="1" name="coursecmplts"> Course Complete
							
                        </div>
                    </div>		 
                </div>
            </div>
            <div class="col-md-3 ">
            <!-- <p><button class="btn clndr-date" type="button">Search</button></p> -->
                <p><input class="but_search margin_top mybtn" type='submit' name='but_search' value='Search'></p>
            </div>
        </div>								
    </form>	 
</div>
<!--  report-field row -->    	
<div class="user_detail table table-responsive table1 table_responsive1" id="dvData">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" rel="stylesheet">
   <!-- <table class="table table-bordered table-striped"> -->
	 <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%"> 
	  <thead>
        <tr>
            <th>Provider #</th>  
            <th>Trainer #</th> 
            <th>Month</th>  
            <th>Day</th> 
            <th>Year</th> 
            <th>Permit #</th> 
            <th>Last Name</th> 
            <th>First Name </th> 
            <th>Middle Initial</th> 
          <th style="min-width:100px;"> Social Security Number</th>
			<th>Month</th> 
            <th>Day</th> 
            <th>Year</th> 			
            <th> Sex-M/F/X</th> 
			<th> Street Address</th> 
            <th> City</th> 
            <th> State</th>  
            <th> Zip Code</th> 			
            <th> Feet </th> 
            <th> Inches </th> 
            <th> Weight </th>
            <th> Email</th>
            <th> Phone</th>         
		    <th> Ex. M</th> 
		    <th> Ex. D</th> 
		    <th> Ex. Yr</th> 
        </tr>
    </thead>
<?php
function date_compare_asc($element1, $element2) {
    $datetime1 = strtotime($element1->dob);
    $datetime2 = strtotime($element2->dob);
    return ($datetime1) - ($datetime2);
} 

function date_compare_desc($element1, $element2) {
    $datetime1 = strtotime($element1->dob);
    $datetime2 = strtotime($element2->dob);
    return ($datetime2) - ($datetime1);
} 

if (isset($_POST['but_search'])) {
	// print_r($_POST);

    $order = $_POST['order'];
    if($order == ""){
        $order = 1;
    }else{
        $order = $_POST['order'];
    }

    $fromDate = strtotime($_POST['date_to']);
    $endDate = strtotime($_POST['date_from']);
    if ($fromDate == "" OR $endDate == "") {
        $datefltr = 1;
    } else {
        $datefltr = "activity_status=1 AND activity_completed between " . $fromDate . " AND " . $endDate;
    }
    $permit = $_POST['permit'];
    if ($permit == "") {
        $permit = 1;
    } else {
        $permit = $_POST['permit'];
    }
    $coursecmplts = $_POST['coursecmplts'];
    if (!empty($coursecmplts)) {
        $complete = "activity_status=1";
    } else {
        $complete = 1;
    }
    if ($permit == 1) {
        $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
        $newRecordss = $wpdb->get_results($rec_query);
    }

    if ($permit == 1) {
        $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
        $newRecordss = $wpdb->get_results($rec_query);
    } elseif ($permit == "permit_issued_option_yes" OR $permit == "permit_issued_option_no") {
        $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
        $newRecordss = $wpdb->get_results($rec_query);
        foreach ($newRecordss as $key => $newRecordsss) {
            $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE '" . $permit . "' AND user_id='" . $newRecordsss->user_id . "' ";
            $eRecordss = $wpdb->get_results($premQuer);
            if (empty($eRecordss)) {
                unset($newRecordss[$key]);
            }
        }
    } elseif ($permit == "null") {
        $rec_query1 = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
        $newRecordss = $wpdb->get_results($rec_query1);
        foreach ($newRecordss as $key => $newRecordsss) {
            $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE 'permit_issued_option%'  AND user_id='" . $newRecordsss->user_id . "' AND `meta_key` LIKE '%date_of_birth%' order by um.meta_value DESC";
            $eRecordss = $wpdb->get_results($premQuer);
            if (!empty($eRecordss)) {
                unset($newRecordss[$key]);
            }
        }
    }

    $i = 1;
	?>
	<tbody>
	<?php
        foreach($newRecordss as $key => $d){
        $QurcourseTitle = "SELECT * FROM wp_posts WHERE ID=$eRecords->course_id";
        $getTitle = $wpdb->get_row($QurcourseTitle);
            if ($getTitle) {
            //  echo $eRecords->user_id;
            //  $newsql = "SELECT * FROM wp_users WHERE ID=$eRecords->user_id AND ID>1 ";  
            }
        $newsql = "SELECT * FROM `wp_users`u JOIN `wp_usermeta`um ON u.ID = um.user_id WHERE u.ID = ".$d->user_id." AND um.`meta_key` LIKE '%date_of_birth%' order by um.meta_value ASC";
        $newusers = $wpdb->get_results($newsql);
        $newuser_meta = get_userdata($newusers[0]->ID);  
        $birth = $newuser_meta->date_of_birth;
        $d->dob=$newuser_meta->date_of_birth;
        $d->dobs=strtotime($newuser_meta->date_of_birth);
        // $newRecordss[$key]->dob=$newuser_meta->date_of_birth;
        // $newRecordss[$key]->dobs=$newuser_meta->date_of_birth;
        }
		if($_POST['order']=="desc"){
			usort($newRecordss, 'date_compare_desc');	
		}else{
			usort($newRecordss, 'date_compare_asc');			
		}
        // echo "<pre>";
        // print_r($newRecordss);
    
        foreach ($newRecordss as $key) {
            $newuser_meta = get_userdata($key->user_id);

             //echo "<pre>";
            // print_r($key);
            // echo "<br>---------------<br>";
    //     }    
  
    // die;



    // foreach ($newRecordss as $eRecords) {
    // //     $QurcourseTitle = "SELECT * FROM wp_posts WHERE ID=$eRecords->course_id";
    // //     $getTitle = $wpdb->get_row($QurcourseTitle);
    //     if ($getTitle) {
    // //         $eRecords->user_id;
    // //         $newsql = "SELECT * FROM wp_users WHERE ID=$eRecords->user_id AND ID>1";
    // //         $newusers = $wpdb->get_results($newsql);
     
    // //         // echo "<pre>";
    // //         // print_r($newusers);
    // //         // echo "</pre>";
            

    //         foreach ($newusers as $newuser) {
    //             $newuser_meta = get_userdata($newuser->ID);
                ?>
 <tr>
  <td><?php echo 78;  ?></td>
  
 <td><?php echo  5465  //$key->user_id;?></td>
<?php $userId = $key->user_id; 
           $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $userId . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$d->course_id."' ";
          $corsCmplt = $wpdb->get_row($Qccmplt);
?>
<td><?php if (!empty($corsCmplt)) { echo date('m', $corsCmplt->activity_completed) . '<br>'; } ?></td>
<td><?php if (!empty($corsCmplt)) { echo date('d', $corsCmplt->activity_completed) . '<br>'; } ?></td>
<td><?php if (!empty($corsCmplt)) { echo date('Y', $corsCmplt->activity_completed) . '<br>'; } ?></td>
<td><?php if(!empty($newuser_meta->permit_number)){ echo $newuser_meta->permit_number;}?></td>
<td><?php if(!empty($newuser_meta->last_name)){ echo strtoupper($newuser_meta->last_name); }?></td>
<td><?php if(!empty($newuser_meta->first_name)){ echo strtoupper($newuser_meta->first_name);} ?></td>
<td><?php if(!empty($newuser_meta->billing_wooccm14)){ 
$mname = trim($newuser_meta->billing_wooccm14);
$fmnane =substr($mname,0,1);
echo strtoupper($fmnane);}?></td>
<td><?php if(!empty($newuser_meta->social_security)){echo $newuser_meta->social_security;} ?></td>
<td><?php $userdob = strtotime($newuser_meta->date_of_birth); 
echo date('m', $userdob);?></td>
<td><?php echo date('d', $userdob);?></td>
<td><?php  echo date('Y', $userdob); ?></td>
<td><?php if(!empty($newuser_meta->user_gender)){ 
			$gdr = trim($newuser_meta->user_gender," ");
			$gen =str_split($gdr); 
			echo $gen[0];  
}?></td>

<td><?php if(!empty($newuser_meta->shipping_address_2)){
                echo $newuser_meta->shipping_address_1 .",<br>". $newuser_meta->shipping_address_2;
            }else{ echo $newuser_meta->shipping_address_1; } ?></td>

<td><?php if(!empty($newuser_meta->shipping_city)){echo strtoupper($newuser_meta->shipping_city);}else{echo strtoupper($newuser_meta->billing_city);} ?></td>
<td><?php if(!empty($newuser_meta->shipping_state)){echo strtoupper($newuser_meta->shipping_state);}else{echo strtoupper($newuser_meta->billing_state);} ?></td>
<td><?php if(!empty($newuser_meta->shipping_postcode)){echo strtoupper($newuser_meta->shipping_postcode);}else{echo strtoupper($newuser_meta->billing_postcode);} ?></td>




<td><?php echo round($newuser_meta->height_ft_opt); ?></td>
<td><?php echo round($newuser_meta->height_in_opt); ?></td>
<td><?php echo round($newuser_meta->weight_lbs); ?></td>
<td><?php echo strtoupper($newuser_meta->user_email);  ?></td>
<td><?php if(!empty($newuser_meta->billing_phone)){echo $newuser_meta->billing_phone;} ?></td>
 <?php     
            $userId = $key->user_id; 
            $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $userId . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$d->course_id."' ORDER BY activity_id DESC LIMIT 0,1";
            $corsCmplt = $wpdb->get_row($Qccmplt);
?>
<td><?php if (!empty($corsCmplt)) {
$quizcomplete = date('Y-m-d',$corsCmplt->activity_completed);
$dt1 = date("d", strtotime($quizcomplete));
 $month = date("m", strtotime($quizcomplete));
if($month == 12){
 echo "0"."1"; 
}else if($month >=9){
 echo $mm = ($month +1);
} 
else {
    echo "0" . $mm = ($month +1);
}
/* if($dt1 == 31){
 $date = strtotime($quizcomplete .'+1 day'); 
echo date('m', $date);
}else{
 $date = strtotime($quizcomplete .'+61 month'); 
echo date('m', $date);
} */
} ?></td>
<td><?php if (!empty($corsCmplt)) { 
$quizcomplete = date('Y-m-d',$corsCmplt->activity_completed);
$date = strtotime($quizcomplete .'+61 month'); 
echo date('01', $date);
} ?></td>
<td><?php if (!empty($corsCmplt)) { 
$quizcomplete = date('Y-m-d',$corsCmplt->activity_completed);
$date = strtotime($quizcomplete .'+61 month'); 
echo date('Y', $date);
} ?></td>

</tr>

<?php }?>
 
</tbody> 

<?php 
} else{
    $users;

$sno=1;
	if($users)
	{
		foreach($users as $user)
		{
			$user_meta=get_userdata($user->ID);		
// new code 

$userId = $user->ID; 
  $querycourse ="SELECT * FROM wp_learndash_user_activity WHERE user_id='".$userId."' AND activity_type LIKE 'course'"; 
$Courselist= $wpdb->get_results($querycourse); 
foreach($Courselist as $Courselists){	
 $querycourse ="SELECT * FROM  wp_posts WHERE ID ='".$Courselists->post_id ."' AND post_type ='sfwd-courses'";  
 $CourseTitle= $wpdb->get_row($querycourse);
  $course_id=$CourseTitle->ID;
  if(!empty($course_id)){
?>
<tr>
 
 <td><?php echo 78;  ?></td>
 <td><?php echo 5465;  ?></td>
 <?php
$userId = $user->ID;
 $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $userId . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$course_id."' ORDER BY activity_id DESC LIMIT 0,1";
$corsCmplt = $wpdb->get_row($Qccmplt); ?>
 <td><?php if (!empty($corsCmplt)) { echo date('m', $corsCmplt->activity_completed) . '<br>';  } ?></td>
<td><?php if (!empty($corsCmplt)) { echo date('d', $corsCmplt->activity_completed) . '<br>';  } ?></td>
<td><?php if (!empty($corsCmplt)) { echo date('Y', $corsCmplt->activity_completed) . '<br>';  } ?></td>
 <td><?php if(!empty($user_meta->permit_number)) { echo $user_meta->permit_number; } ?></td> 
<td><?php if(!empty($user_meta->last_name)){ echo strtoupper($user_meta->last_name);} ?></td> 
<td><?php if(!empty($user_meta->first_name)){ echo strtoupper($user_meta->first_name);}?></td> 
<td><?php if(!empty($user_meta->billing_wooccm14)){ 
$mname = trim($user_meta->billing_wooccm14);
$fmnane =substr($mname,0,1);
echo strtoupper($fmnane);} ?></td> 
<td><?php if(!empty($user_meta->social_security)){echo $user_meta->social_security;}  ?></td>
<td><?php $userdob = strtotime($user_meta->date_of_birth); 
echo date('m', $userdob);
?></td>
<td><?php echo date('d', $userdob); ?></td>
<td><?php echo date('Y', $userdob); ?></td>
<td><?php if(!empty($user_meta->user_gender)){ 
$gdr = trim($user_meta->user_gender," ");
$gen =str_split($gdr); 
echo $gen[0]; 
}?></td> 




<td><?php if(!empty($user_meta->shipping_address_2)){
                echo $user_meta->shipping_address_1 .",<br>". $user_meta->shipping_address_2;
            }else{ echo $user_meta->shipping_address_1; } ?></td> 

<td><?php if(!empty($user_meta->shipping_city)){echo strtoupper($user_meta->shipping_city);}else{echo strtoupper($user_meta->billing_city);} ?></td>
<td><?php if(!empty($user_meta->shipping_state)){echo strtoupper($user_meta->shipping_state);}else{echo strtoupper($user_meta->billing_state);} ?></td>
<td><?php if(!empty($user_meta->shipping_postcode)){echo strtoupper($user_meta->shipping_postcode);}else{echo strtoupper($user_meta->billing_postcode);} ?></td>





<td><?php echo round($user_meta->height_ft_opt); ?></td>
<td><?php echo round($user_meta->height_in_opt); ?></td>
<td><?php echo round($user_meta->weight_lbs); ?></td>
<td><?php if(!empty($user->user_email)){ echo strtoupper($user->user_email); }?></td>
<td><?php if(!empty($user_meta->billing_phone)){ echo $user_meta->billing_phone; }?></td>
		
<?php
$userId = $user->ID;
 $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $userId . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$course_id."' ORDER BY activity_id DESC LIMIT 0,1";
$corsCmplt = $wpdb->get_row($Qccmplt);?>

<td><?php if (!empty($corsCmplt)) { 
 $quizcomplete = date('Y-m-d',$corsCmplt->activity_completed);
 $dt1 = date("d", strtotime($quizcomplete));
 $month = date("m", strtotime($quizcomplete));
if($month == 12){
 echo "0"."1"; 
}else if($month >=9){
 echo $mm = ($month +1);
} 
else {
    echo "0" . $mm = ($month +1);
}
/* 
if($dt1 == 31){
 $date = strtotime($quizcomplete .'+1 day'); 
echo date('m', $date);
}else{
 $date = strtotime($quizcomplete .'+61 month'); 
echo date('m', $date);
} 
*/
} ?></td>

<td><?php if (!empty($corsCmplt)) { 
$quizcomplete = date('Y-m-d',$corsCmplt->activity_completed);
$date = strtotime($quizcomplete .'+61 month'); 
echo date('01', $date);
} ?></td>

<td><?php if (!empty($corsCmplt)) { 
$quizcomplete = date('Y-m-d',$corsCmplt->activity_completed);
$date = strtotime($quizcomplete .'+61 month'); 
echo date('Y', $date);
} ?></td>
	
	
	</tr>
<?php
 }
		}
	}
 } 
}


?></table> 
		</div>
<script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"> </script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
<script>

/* 		    $(document).ready(function () {
        $('#example').DataTable();
        }); */


$(document).ready(function() {
    $('#example').DataTable( {
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    } );
} );
 
</script>
		
<script type='text/javascript'>
		
    jQuery(document).ready(function () {
	jQuery(".clndr-date").click(function(){		 
		//alert('hiiii');
	var datepickerFrom   =   document.getElementById("datepickerFrom").value;
	var datepickerTo     =   document.getElementById("datepickerTo").value;
	alert(datepickerFrom+'#'+datepickerTo);
	
	 });
	   
    jQuery("#datepickerTo" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
	
	 jQuery("#datepickerFrom" ).datepicker({
      changeMonth: true,
      changeYear: true
    });			
			
            //console.log("HELLO")
            function exportTableToCSV($table, filename) {
                var $headers = $table.find('tr:has(th)')
                    ,$rows = $table.find('tr:has(td)')

                    // Temporary delimiter characters unlikely to be typed by keyboard
                    // This is to avoid accidentally splitting the actual contents
                    ,tmpColDelim = String.fromCharCode(11) // vertical tab character
                    ,tmpRowDelim = String.fromCharCode(0) // null character

                    // actual delimiter characters for CSV format
                    ,colDelim = '","'
                    ,rowDelim = '"\r\n"';

                    // Grab text from table into CSV formatted string
                    var csv = '"';
                    csv += formatRows($headers.map(grabRow));
                    csv += rowDelim;
                    csv += formatRows($rows.map(grabRow)) + '"';

                    // Data URI
                    var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

                $(this)
                    .attr({
                    'download': filename
                        ,'href': csvData
                        //,'target' : '_blank' //if you want it to open in a new window
                });

                //------------------------------------------------------------
                // Helper Functions 
                //------------------------------------------------------------
                // Format the output so it has the appropriate delimiters
                function formatRows(rows){
                    return rows.get().join(tmpRowDelim)
                        .split(tmpRowDelim).join(rowDelim)
                        .split(tmpColDelim).join(colDelim);
                }
                // Grab and format a row from the table
                function grabRow(i,row){
                     
                    var $row = $(row);
                    //for some reason $cols = $row.find('td') || $row.find('th') won't work...
                    var $cols = $row.find('td'); 
                    if(!$cols.length) $cols = $row.find('th');  

                    return $cols.map(grabCol)
                                .get().join(tmpColDelim);
                }
                // Grab and format a column from the table 
                function grabCol(j,col){
                    var $col = $(col),
                        $text = $col.text();

                    return $text.replace('"', '""'); // escape double quotes
                }
            }


            // This must be a hyperlink
			
            $("#export").click(function (event) {
                // var outputFile = 'export'
//                var outputFile = window.prompt("What do you want to name your output file") || 'export';
                 var d = new Date();
				 var mymonth= d.getMonth()+1;
				 if(mymonth < 10){
					 mymonth = "0"+mymonth;
				 }
				var mydate = d.getDate();
				 if(mydate <10){
					 mydate = "0" + mydate;
				 }
				 //1ALERT20200225
                //yyyymmdd
                 var outputFile= "1ALERT"+d.getFullYear()+mymonth+mydate;
                outputFile = outputFile.replace('.csv','') + '.csv'
                 
                // CSV
                //exportTableToCSV.apply(this, [$('#dvData>table'), outputFile]);
                //exportTableToCSV.apply(this, [$('#dvData>table'), outputFile]);
                  exportTableToCSV.apply(this, [$('#dvData .dataTable'), outputFile]);				
                // IF CSV, don't do event.preventDefault() or return false
                // We actually need this to be a typical hyperlink  
            });
        });


    </script>	  
	 

<style>
    .ui-datepicker td .ui-state-default {
        padding: 4px 6px !important;
    }
    .table_responsive1{
        border: 1px solid #ddd; 
        margin-top: 20px;
    }	     
    .btncsv{
        width: 100px;
        height: 30px;
        margin: 15px 0 0 0;
        text-align: center;
        line-height: 30px;
        transition: 0.5s;
    }
    .btncsv:hover{
        background:#fff !important;
        color:#555 !important;
    }
   
    .ui-datepicker select.ui-datepicker-month, .ui-datepicker select.ui-datepicker-year {
        color: #000 !important;
    }
    .row {
        width: 100%;
        margin: 0 auto;
    }
    .block {
        width: 300px;
        display: inline-block;
    }
    .but_search{	
        width: 150px;
        border-radius: 3px;
        background: #ffb900;}
    .button{background: #fff;
            border: 1px solid #e4081233;}  

    .ui-datepicker td.ui-datepicker-week-end {
        background-color: #fff !important;
        border: 1px solid #fff !important;
    } 	
    .ui-datepicker td {
        border: 0 !important;
        padding: 0 !important; 
    }

    .enrol_select_btn{
        display:block;
    }
    .margin_top{
	margin-top:27px !important;
	display:inline-block;
}
.mybtn {
    border: 2px solid #f9bb00 !important;
	background:#f9bb00 !important ;
	padding:5px !important;
	border:none !important;
    font-size: 15px !important;
	text-align:center;
	color:#fff;
	line-height:unset !important;
	min-height:unset !important ; 
	
}
.mybtn a{
	color:#fff !important;
}
.wrapper_enrollment input{
	width:100%; 
} 
a.paginate_button { 
    padding: 5px 8px;
    border: 1px solid #337ab7;
    margin-left: -1px;
	cursor:pointer;
}

a.paginate_button.current { 
    color: #fff;
    background: #337ab7; 
} 
</style>
<!---- //--exportUserReport ---->	
<?php
}
 
function exportUserReport()
{
	global $wpdb;
   
	date_default_timezone_set( 'America/Los_Angeles' );
	date_default_timezone_get();
	$sql = 'SELECT u.*, um.meta_value as birthday FROM '. $wpdb->prefix .'users u JOIN '. $wpdb->prefix .'usermeta um ON u.ID = um.user_id AND um.meta_key LIKE "%date_of_birth%" WHERE u.ID >1 ORDER BY um.meta_value DESC';
	$users = $wpdb->get_results($sql);

?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">   
<div class="report-field row" style="margin-top:20px;">
    <div class="col-md-2">
        <div class='mybtn button margin_top'>
            <a href="#" id ="export" role='button'>User Report 
            </a>
        </div>
    </div>
    <form id="user_form_report" class="user_form_markbtn col-md-10" method="post">
        <div class="row">
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-md-3 ">
                        <div class="wrapper_enrollment"><label class="enrol_field">From </label>
                            <input id="datepickerFrom" class="enrol_input" name="date_to"  type="text" value="<?php if(!empty($_POST['date_to'])){echo $_POST['date_to']; }?>">
                        </div>
                    </div>	
                    <div class="col-md-3 ">
                        <div class="wrapper_enrollment"><label class="enrol_field">To </label>
                            <input id="datepickerTo" class="enrol_input" name="date_from"  type="text" value="<?php if(!empty($_POST['date_from'])){echo $_POST['date_from']; }?>">
                        </div>
                    </div>	
                    <div class="col-md-2 ">
                        <div class="wrapper_enrollment"><label class="enrol_field">Permit Issue </label>
                            <select  class="enrol_select_btn enrol_input" id="permit_issue" name="permit" > 
                                <option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='1'){echo "selected"; }?>  value="1"> Select One </option>
                                <option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='permit_issued_option_yes'){echo "selected"; }?> value="permit_issued_option_yes"> Yes </option>
                                <option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='permit_issued_option_no'){echo "selected"; }?> value="permit_issued_option_no"> No </option> 
                                <option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='null'){echo "selected"; }?> value="null"> Null </option> 
                            </select>
                        </div>
                    </div>	
                    <div class="col-md-2 ">
                        <div class="wrapper_enrollment"><label class="enrol_field">Order by age </label>
                            <select  class="enrol_select_btn enrol_input" id="order_issue" name="order" > 
                                <option
								<?php if(!empty($_POST['order'])&&$_POST['order']=='1'){echo "selected"; }?>value=" "> Select One </option>
                                <option <?php if(!empty($_POST['order'])&&$_POST['order']=='asc'){echo "selected"; }?> value="asc"> Ascending </option>
                                <option <?php if(!empty($_POST['order'])&&$_POST['order']=='desc'){echo "selected"; }?> value="desc"> Descending </option> 
                            </select>
                        </div>
                    </div>	
                    <div class="col-md-2 ">
                        <div class="margin_top">
                            <input type="checkbox" <?php if(!empty($_POST['coursecmplts'])){echo "checked"; }?> value="1" name="coursecmplts"> Course Complete
							
                        </div>
                    </div>		 
                </div>
            </div>
            <div class="col-md-3 ">
            <!-- <p><button class="btn clndr-date" type="button">Search</button></p> -->
                <p><input class="but_search margin_top mybtn" type='submit' name='but_search' value='Search'></p>
            </div>
        </div>								
    </form>	 
</div> 
<!--  report-field row -->    	
<div class="user_detail table table-responsive table1 table_responsive1" id="dvData">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" rel="stylesheet">
   <!-- <table class="table table-bordered table-striped"> -->
	 <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%"> 
	 <thead>
        <tr>
            <th>S.No </th> 
            <th>  Permit Number </th> 
            <th>First Name </th> 
            <th> Middle Initial</th> 
            <th> Last Name</th> 
            <th> DOB Month</th> 
            <th> DOB Day</th> 
            <th> DOB Year</th> 
            <th> Gender (M/F/X)</th> 
            <th>  Height Feet</th> 
            <th> Height Inches</th> 
            <th>Weight</th>
            <th style="min-width:100px;"> Social Security</th>
            <th> Address Street</th> 
            <th> Address City</th> 
            <th> Address State</th>  
            <th> Address Zip</th> 
            <th> Email</th>
            <th> Phone</th>         
            <th> Course Completed</th>  
            <th style="min-width:300px;"> Course Name</th> 
            <th> Student Number </th> 
            <th> Username </th> 
            <th> Permit Issued</th> 
            <th> Student Registration Date</th> 
        </tr>  
		</thead>
<?php

function date_compare_asc($element1, $element2) {
    $datetime1 = strtotime($element1->dob);
    $datetime2 = strtotime($element2->dob);
    return ($datetime1) - ($datetime2);
} 

function date_compare_desc($element1, $element2) {
    $datetime1 = strtotime($element1->dob);
    $datetime2 = strtotime($element2->dob);
    return ($datetime2) - ($datetime1);
} 

if (isset($_POST['but_search'])) {
    $fromDate = strtotime($_POST['date_to']);
    $endDate = strtotime($_POST['date_from']);
    if ($fromDate == "" OR $endDate == "") {
        $datefltr = 1;
    } else {
        $datefltr = "activity_status=1 AND activity_completed between " . $fromDate . " AND " . $endDate;
    }
    $permit = $_POST['permit'];
    if ($permit == "") {
        $permit = 1;
    } else {
        $permit = $_POST['permit'];
    }
    $coursecmplts = $_POST['coursecmplts'];
    if (!empty($coursecmplts)) {
        $complete = "activity_status=1";
    } else {
        $complete = 1;
    }
    if ($permit == 1) {
        $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
        $newRecordss = $wpdb->get_results($rec_query);
    } elseif ($permit == "permit_issued_option_yes" OR $permit == "permit_issued_option_no") {
        $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz' AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
        $newRecordss = $wpdb->get_results($rec_query);
        foreach ($newRecordss as $key => $newRecordsss) {
            $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE '" . $permit . "' AND user_id='" . $newRecordsss->user_id . "'";
            $eRecordss = $wpdb->get_results($premQuer);
            if (empty($eRecordss)) {
                unset($newRecordss[$key]);
            }
        }
    } elseif ($permit == "null") {
        $rec_query1 = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz' AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
        $newRecordss = $wpdb->get_results($rec_query1);
        foreach ($newRecordss as $key => $newRecordsss) {
            $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE 'permit_issued_option%'  AND user_id='" . $newRecordsss->user_id . "'";
            $eRecordss = $wpdb->get_results($premQuer);
            if (!empty($eRecordss)) {
                unset($newRecordss[$key]);
            }
        }
    }


    $i = 1;
	?>
	<tbody>
	<?php
    // foreach ($newRecordss as $eRecords) {
    //     $QurcourseTitle = "SELECT * FROM wp_posts WHERE ID=$eRecords->course_id";
    //     $getTitle = $wpdb->get_row($QurcourseTitle);
    //     if ($getTitle) {
    //         $eRecords->user_id;
    //         $newsql = "SELECT * FROM wp_users WHERE ID=$eRecords->user_id AND ID>1";
    //         $newusers = $wpdb->get_results($newsql);		
    //         foreach ($newusers as $newuser) {
    //             $newuser_meta = get_userdata($newuser->ID);
            foreach($newRecordss as $key => $d){
       // $QurcourseTitle = "SELECT * FROM wp_posts WHERE ID=$eRecords->course_id";
       // $getTitle = $wpdb->get_row($QurcourseTitle);
            /* if ($getTitle) {
                // code
            } */
        $newsql = "SELECT * FROM `wp_users`u JOIN `wp_usermeta`um ON u.ID = um.user_id WHERE u.ID = ".$d->user_id." AND um.`meta_key` LIKE '%date_of_birth%' order by um.meta_value ASC";
        $newusers = $wpdb->get_results($newsql);
        $newuser_meta = get_userdata($newusers[0]->ID);  
        $birth = $newuser_meta->date_of_birth;
        $d->dob=$newuser_meta->date_of_birth;
        $d->dobs=strtotime($newuser_meta->date_of_birth);
        }
		if($_POST['order']=="desc"){
			usort($newRecordss, 'date_compare_desc');	
		}else{
			usort($newRecordss, 'date_compare_asc');			
		}

        foreach ($newRecordss as $key) {
            $newuser_meta = get_userdata($key->user_id);
                ?>
 <tr>
 <td><?php echo $i++; ?></td>
<td><?php if(!empty($newuser_meta->permit_number)){ echo $newuser_meta->permit_number;}?></td>
<td><?php if(!empty($newuser_meta->first_name)){ echo strtoupper($newuser_meta->first_name);} ?></td>
<td><?php if(!empty($newuser_meta->billing_wooccm14)){ echo strtoupper($newuser_meta->billing_wooccm14); }?></td>
<td><?php if(!empty($newuser_meta->last_name)){ echo strtoupper($newuser_meta->last_name); }?></td>
<td><?php $userdob = strtotime($newuser_meta->date_of_birth); 
echo date('m', $userdob);?> </td>
<td><?php echo date('d', $userdob); ?></td>
<td><?php echo date('Y', $userdob);?></td>

<td><?php if(!empty($newuser_meta->user_gender)){ echo strtoupper($newuser_meta->user_gender); }?></td>
<td><?php echo round($newuser_meta->height_ft_opt); ?></td>
<td><?php echo round($newuser_meta->height_in_opt); ?></td>
<td><?php  echo round($newuser_meta->weight_lbs); ?></td>
<td><?php if(!empty($newuser_meta->social_security)){echo $newuser_meta->social_security;} ?></td>

<td><?php if(!empty($newuser_meta->shipping_address_2)){
                echo $newuser_meta->shipping_address_1 .",<br>". $newuser_meta->shipping_address_2;
            }else{
                echo $newuser_meta->shipping_address_1;
            } ?></td>

<td><?php if(!empty($newuser_meta->shipping_city)){echo strtoupper($newuser_meta->shipping_city);}else{ echo strtoupper($newuser_meta->billing_city);} ?></td>
<td><?php if(!empty($newuser_meta->shipping_state)){echo strtoupper($newuser_meta->shipping_state);}else{echo strtoupper($newuser_meta->billing_state);}?></td>
<td><?php if(!empty($newuser_meta->shipping_postcode)){echo $newuser_meta->shipping_postcode;}else{echo $newuser_meta->billing_postcode;} ?></td>
<td><?php echo strtoupper($newuser_meta->user_email);  ?></td>
<td><?php if(!empty($newuser_meta->billing_phone)){echo $newuser_meta->billing_phone;} ?></td>

<td><?php
            $userId = $key->user_id; 
            $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $userId . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$d->course_id."' ORDER BY activity_id DESC LIMIT 0,1";
            $corsCmplt = $wpdb->get_row($Qccmplt);
if (!empty($corsCmplt)) {
 echo date('m/d/Y  H:i', $corsCmplt->activity_completed) . '<br>';
} 

?></td>

<td><?php 
//  echo strtoupper($getTitle->post_title);  
 $querycourse ="SELECT * FROM  wp_posts WHERE ID ='".$d->course_id ."' AND post_type ='sfwd-courses'";  
$CourseTitle= $wpdb->get_row($querycourse);
 echo $CourseTitle->post_title;
 
 ?></td>

<td><?php echo $newuser_meta->ID;  ?></td>
<td><?php echo $newuser_meta->user_login;  ?></td>
<td><?php if(!empty($newuser_meta->permit_issued_option_yes)){ echo "Yes"; } elseif(!empty($newuser_meta->permit_issued_option_no)) { echo "No";}?></td>
<td><?php  $userId = $key->user_id; 
 $queryenrol ="SELECT * FROM wp_learndash_user_activity WHERE user_id='".$userId."' AND activity_type LIKE 'topic' AND `activity_status`=1  AND course_id ='".$d->course_id."' ORDER BY `activity_id` ASC LIMIT 0,1";
   $enrollist= $wpdb->get_row($queryenrol); 
   if(!empty($enrollist)){
   echo date('m/d/Y H:i',$enrollist->activity_started);
   }?></td>
</tr>

	<?php } ?>
</tbody>	
<?php }
 else{
    $users;
$sno=1;
	if($users)
	{
		foreach($users as $user)
		{
			$user_meta=get_userdata($user->ID);		
// new code 

$userId = $user->ID; 
 //$querycourse ="SELECT * FROM wp_tc_course_access WHERE user_id='".$userId."'"; 
  $querycourse ="SELECT * FROM wp_learndash_user_activity WHERE user_id='".$userId."' AND activity_type LIKE 'course'"; 
$Courselist= $wpdb->get_results($querycourse); 
//echo "<pre>";
//print_r($Courselist);
foreach($Courselist as $Courselists){	 
 $querycourse ="SELECT * FROM  wp_posts WHERE ID ='".$Courselists->post_id ."' AND post_type ='sfwd-courses'";  
 $CourseTitle= $wpdb->get_row($querycourse);
  $course_id=$CourseTitle->ID;
  if(!empty($course_id)){
?>
<tr>
 <td><?php echo $sno++;  ?></td>
 <td><?php if(!empty($user_meta->permit_number)) { echo $user_meta->permit_number; } ?></td> 
<td><?php if(!empty($user_meta->first_name)){ echo strtoupper($user_meta->first_name);}?></td> 
<td><?php if(!empty($user_meta->billing_wooccm14)){ echo strtoupper($user_meta->billing_wooccm14);} ?></td> 
<td><?php if(!empty($user_meta->last_name)){ echo strtoupper($user_meta->last_name);} ?></td> 
<td><?php $userdob = strtotime($user_meta->date_of_birth); 
echo date('m', $userdob); ?></td>
<td><?php echo date('d', $userdob); ?></td>
<td><?php echo date('Y', $userdob); ?></td>
<td><?php if(!empty($user_meta->user_gender)){ echo strtoupper($user_meta->user_gender); }?></td>
<td><?php echo round($user_meta->height_ft_opt); ?></td> 
<td><?php echo round($user_meta->height_in_opt); ?></td> 
<td><?php echo round($user_meta->weight_lbs); ?></td>
<td><?php if(!empty($user_meta->social_security)){echo $user_meta->social_security;}  ?></td>

<td><?php if(!empty($user_meta->shipping_address_2)){
                echo $user_meta->shipping_address_1 .",<br>". $user_meta->shipping_address_2;
            }else{ echo $user_meta->shipping_address_1; } ?></td>

<td><?php if(!empty($user_meta->shipping_city)){ echo strtoupper($user_meta->shipping_city); }else{ echo strtoupper($user_meta->billing_city); }?></td>
<td><?php if(!empty($user_meta->shipping_state)){ echo strtoupper($user_meta->shipping_state); }else{ echo strtoupper($user_meta->billing_state); }?></td>
<td><?php if(!empty($user_meta->shipping_postcode)){ echo strtoupper($user_meta->shipping_postcode); }else{ echo strtoupper($user_meta->billing_postcode); }?></td>
<td><?php if(!empty($user->user_email)){ echo strtoupper($user->user_email); }?></td>
<td><?php if(!empty($user_meta->billing_phone)){ echo $user_meta->billing_phone; }?></td>
<td><?php
$userId = $user->ID;
 $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $userId . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$course_id."' ORDER BY activity_id DESC LIMIT 0,1";
$corsCmplt = $wpdb->get_row($Qccmplt);
if (!empty($corsCmplt)) {
 echo date('m/d/Y  H:i', $corsCmplt->activity_completed) . '<br>';
} ?></td>
<td><?php
$userId = $user->ID; 
 $querycourse ="SELECT * FROM  wp_posts WHERE ID ='".$course_id ."' AND post_type ='sfwd-courses'";  
$CourseTitle= $wpdb->get_row($querycourse);
 if(!empty($CourseTitle->post_title)){
 	echo $CourseTitle->post_title; 
 }
?></td> 
<td><?php echo $userId = $user->ID;  ?></td>
<td><?php echo $user->user_login;  ?></td>
<td><?php 
if(!empty($user_meta->permit_issued_option_yes)){ 
echo "Yes"; 
}elseif(!empty($user_meta->permit_issued_option_no)){ 
	echo "No";
}
?></td>
<td><?php
    $userId = $user->ID; 
  $queryenrol ="SELECT * FROM wp_learndash_user_activity WHERE user_id='".$userId."' AND activity_type LIKE 'topic' AND `activity_status`=1  AND course_id ='".$course_id."' ORDER BY `activity_id` ASC LIMIT 0,1";
$enrollist= $wpdb->get_row($queryenrol); 
 if(!empty($enrollist)){
echo date('m/d/Y H:i', $enrollist->activity_started);
}
 
    ?></td>
	</tr>
<?php
 }
		}
	}
 } 
}
 //print_r($eRecords); 
 ?>		

</table> 
		</div>
<script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"> </script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
<script>

/* 		    $(document).ready(function () {
        $('#example').DataTable();
        }); */


$(document).ready(function() {
    $('#example').DataTable( {
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    } );
} );
 
</script>
		
<script type='text/javascript'>
		
    jQuery(document).ready(function () {
	jQuery(".clndr-date").click(function(){		 
		//alert('hiiii');
	var datepickerFrom   =   document.getElementById("datepickerFrom").value;
	var datepickerTo     =   document.getElementById("datepickerTo").value;
	alert(datepickerFrom+'#'+datepickerTo);
	
	 });
	   
    jQuery("#datepickerTo" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
	
	 jQuery("#datepickerFrom" ).datepicker({
      changeMonth: true,
      changeYear: true
    });			
			
            //console.log("HELLO")
            function exportTableToCSV($table, filename) {
                var $headers = $table.find('tr:has(th)')
                    ,$rows = $table.find('tr:has(td)')

                    // Temporary delimiter characters unlikely to be typed by keyboard
                    // This is to avoid accidentally splitting the actual contents
                    ,tmpColDelim = String.fromCharCode(11) // vertical tab character
                    ,tmpRowDelim = String.fromCharCode(0) // null character

                    // actual delimiter characters for CSV format
                    ,colDelim = '","'
                    ,rowDelim = '"\r\n"';

                    // Grab text from table into CSV formatted string
                    var csv = '"';
                    csv += formatRows($headers.map(grabRow));
                    csv += rowDelim;
                    csv += formatRows($rows.map(grabRow)) + '"';

                    // Data URI
                    var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

                $(this)
                    .attr({
                    'download': filename
                        ,'href': csvData
                        //,'target' : '_blank' //if you want it to open in a new window
                });

                //------------------------------------------------------------
                // Helper Functions 
                //------------------------------------------------------------
                // Format the output so it has the appropriate delimiters
                function formatRows(rows){
                    return rows.get().join(tmpRowDelim)
                        .split(tmpRowDelim).join(rowDelim)
                        .split(tmpColDelim).join(colDelim);
                }
                // Grab and format a row from the table
                function grabRow(i,row){
                     
                    var $row = $(row);
                    //for some reason $cols = $row.find('td') || $row.find('th') won't work...
                    var $cols = $row.find('td'); 
                    if(!$cols.length) $cols = $row.find('th');  

                    return $cols.map(grabCol)
                                .get().join(tmpColDelim);
                }
                // Grab and format a column from the table 
                function grabCol(j,col){
                    var $col = $(col),
                        $text = $col.text();

                    return $text.replace('"', '""'); // escape double quotes
                   

                }
            }


            // This must be a hyperlink
			
            $("#export").click(function (event) {
                // var outputFile = 'export'
//                var outputFile = window.prompt("What do you want to name your output file") || 'export';
                 var d = new Date();
				 var mymonth= d.getMonth()+1;
				 if(mymonth < 10){
					 mymonth = "0"+mymonth;
				 }
				var mydate = d.getDate();
				 if(mydate <10){
					 mydate = "0" + mydate;
				 }
				 //1ALERT20200225
                //yyyymmdd
                 var outputFile= "1ALERT"+d.getFullYear()+mymonth+mydate;
                outputFile = outputFile.replace('.csv','') + '.csv'
                 
                // CSV
                //exportTableToCSV.apply(this, [$('#dvData>table'), outputFile]);
                //exportTableToCSV.apply(this, [$('#dvData>table'), outputFile]);
                  exportTableToCSV.apply(this, [$('#dvData .dataTable'), outputFile]);				
                // IF CSV, don't do event.preventDefault() or return false
                // We actually need this to be a typical hyperlink  
            });
        });


    </script>	  
	 

<style>
    .ui-datepicker td .ui-state-default {
        padding: 4px 6px !important;
    }
    .table_responsive1{
        border: 1px solid #ddd; 
        margin-top: 20px;
    }	     
    .btncsv{
        width: 100px;
        height: 30px;
        margin: 15px 0 0 0;
        text-align: center;
        line-height: 30px;
        transition: 0.5s;
    }
    .btncsv:hover{
        background:#fff !important;
        color:#555 !important;
    }
   
    .ui-datepicker select.ui-datepicker-month, .ui-datepicker select.ui-datepicker-year {
        color: #000 !important;
    }
    .row {
        width: 100%;
        margin: 0 auto;
    }
    .block {
        width: 300px;
        display: inline-block;
    }
    .but_search{	
        width: 150px;
        border-radius: 3px;
        background: #ffb900;}
    .button{background: #fff;
            border: 1px solid #e4081233;}  

    .ui-datepicker td.ui-datepicker-week-end {
        background-color: #fff !important;
        border: 1px solid #fff !important;
    } 	
    .ui-datepicker td {
        border: 0 !important;
        padding: 0 !important; 
    }

    .enrol_select_btn{
        display:block;
    }
    .margin_top{
	margin-top:27px !important;
	display:inline-block;
}
.mybtn {
    border: 2px solid #f9bb00 !important;
	background:#f9bb00 !important ;
	padding:5px !important;
	border:none !important;
    font-size: 15px !important;
	text-align:center;
	color:#fff;
	line-height:unset !important;
	min-height:unset !important ; 
	
}
.mybtn a{
	color:#fff !important;
}
.wrapper_enrollment input{
	width:100%; 
} 
a.paginate_button {
    padding: 5px 8px;
    border: 1px solid #337ab7;
    margin-left: -1px;
	cursor:pointer;
}

a.paginate_button.current { 
    color: #fff;
    background: #337ab7; 
} 
</style>
<?php
}
?>