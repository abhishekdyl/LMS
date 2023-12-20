<?php 
require_once('../../config.php');
global $DB, $USER, $PAGE;
$PAGE->requires->jquery();
require_login();
$is_siteadmin = is_siteadmin();
$context = \context_system::instance();
$current_logged_in_user =  $USER->id;
$has_capability = has_capability('local/assignment_subscription:grade_student', $context, $current_logged_in_user);
if (!$has_capability) {
	$urltogo_dashboard = $CFG->wwwroot.'/my/';
	redirect($urltogo_dashboard, 'You do not have permission to view this page', null, \core\output\notification::NOTIFY_WARNING);
}
$tabtype = optional_param('tabtype', 0, PARAM_INT);
$activetab=2;
if($tabtype) {
	$activetab = $tabtype;
	$_SESSION['markdata']['tabbtn']=$tabtype;	
} else if(isset($_SESSION['markdata']) && !empty($_SESSION['markdata']['tabbtn'])){
	$activetab = $_SESSION['markdata']['tabbtn'];	
}
if($activetab == 1){
	set_user_preference('assign_filter', "", $USER);
} else {
	set_user_preference('assign_filter', "requiregrading", $USER);
}

$PAGE->set_title('Assignment Subscription');
$PAGE->set_heading('Assignment Subscription');
$current_date = strtotime(date("d F Y H:i:s"));



echo $OUTPUT->header();

?>
	<a class="btn btn-primary" href="home.php">Back</a><br/><br/>


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<style type="text/css">
.dataTables_processing {
    background: #028090;
    color: white !important;
/*    display: none;*/
}
.wrapon250 {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 250px;
    display: inline-block;
	vertical-align: -webkit-baseline-middle;
}

.wrapon200_general{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 150px;
    display: inline-block;
	vertical-align: -webkit-baseline-middle;
}

.btn-default {
	color: #3e475100 !important;
	background-color: #ced4da00 !important;
	border-color: #ced4da0a !important;
}


.tabs {
  margin: 5 auto;
  font-size: 0;
}

.tabs > input[type="radio"] {
  display: none;
}

.tabs > div {
  display: none;
  border: 1px solid #e0e0e0;
  padding: 10px 15px;
  font-size: 16px;
}

#tab-btn-1:checked ~ #content-1,
#tab-btn-2:checked ~ #content-2,
#tab-btn-3:checked ~ #content-3 {
  display: block;
  border-top: 3px solid #028090;
}

.tabs > label {
  display: inline-block;
  text-align: center;
  vertical-align: middle;
  user-select: none;
  background-color: #f5f5f5;
  border: 1px solid #e0e0e0;
  padding: 8px 14px;
  font-size: 16px;
  line-height: 1.5;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
  cursor: pointer;
  position: relative;
  top: 1px;
  margin-right: 6px;
}

.tabs > label:not(:first-of-type) {
  border-left: none;
}

.tabs > input[type="radio"]:checked + label {
  background-color: #028090;
  border-bottom: 1px solid #fff;
  color: #fff;
}
.multiselect.dropdown-toggle.form-control {
  text-align: left;
  width: 100%;
  display: flex;
  align-content: center;
  justify-content: space-between;
  position: relative;
}
.multiselect.dropdown-toggle.form-control::after {
  position: absolute;
  right: 10px;
  top: 15px;
}
</style>     


<div class="tabs">

	<input type="radio" name="tab-btn" id="tab-btn-1" class="filterupdated" value="1" <?php echo $activetab == 1?'checked':''?> >
	<label for="tab-btn-1" onclick="location.href='<?php echo $CFG->wwwroot.'/local/assignment_subscription/mark_student.php?tabtype=1';?>'" >Archive</label>
	<input type="radio" name="tab-btn" id="tab-btn-2" class="filterupdated" value="2" <?php echo $activetab == 2?'checked':''?> >
	<label for="tab-btn-2" onclick="location.href='<?php echo $CFG->wwwroot.'/local/assignment_subscription/mark_student.php?tabtype=2';?>'">General</label>
	<input type="radio" name="tab-btn" id="tab-btn-3" class="filterupdated" value="3"  <?php echo $activetab == 3?'checked':''?>>
	<label for="tab-btn-3" onclick="location.href='<?php echo $CFG->wwwroot.'/local/assignment_subscription/mark_student.php?tabtype=3';?>'">Priority</label>
	<label for="tab-btn-4" style="float: right;"><a style="text-decoration: none;" id="reloaddata" href="javascript:void(0);">Refresh</a></label>
<?php
if($activetab ==1){
?>

  <!-- Archive Tab -->

	<div id="content-1">
			 <!-- <div class="table-responsive"> -->
				<div id="archive_tab">
				<div class="row">

					<div class="col-md-3">
						<p><b>Filter Course</b></p>
						<select name="course_id_archive" class="form-control filterupdated formfilterdata" id="course_id_archive">
							<option value="All">All courses</option>
							<?php  

							$courses = get_courses($categoryid="all", $sort="c.fullname ASC", $fields="c.*");
							foreach ($courses as $value) { 
							if($value->id > 1){

							?>

							<option value="<?php echo $value->id; ?>"><?php echo $value->fullname; ?></option>

						<?php }} ?>

						</select>
					</div>



					<div class="col-md-3">
						<p><b>Search Student</b></p>
						<select name="search_student_archive" class="form-control filterupdated formfilterdata" id="search_student_archive">
							<option value="All">All</option>
							<?php
							// Archive
							$query = "SELECT distinct(u.id), u.firstname, u.lastname, u.email FROM {$CFG->prefix}user u 
							INNER JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
							INNER JOIN {$CFG->prefix}context ct ON ct.id = ra.contextid
							INNER JOIN {$CFG->prefix}role r ON r.id = ra.roleid 
							WHERE r.id =5 AND u.deleted = 0 ";
							$all_users_archive = $DB->get_records_sql($query);

							foreach ($all_users_archive as $data_user) {  
							?>
							<option value="<?php echo $data_user->id; ?>"><?php echo $data_user->firstname." ".$data_user->lastname." (".$data_user->email.")"; ?></option>
							<?php } ?>


						</select>
					</div>




					<div class="col-md-2">
					<p><b>Filter Date</b></p>
					<select name="filter_date" class="form-control filterupdated formfilterdata" id="filter_date" >
						<option value="All">All</option>
						<option value="today" class="hide">Today</option>
						<option value="this_week" class="hide">This week</option>
						<option value="last_week" class="hide">Last week</option>
						<option value="this_month" class="hide">This month</option>
						<option value="last_month" class="hide">Last month</option>
						<option value="custom_date" id="custom_date">Custom date</option>
					</select>
					</div>


					<script type="text/javascript">
						$("#filter_date").change(function(){
							var value = $(this).val();
								if(value == 'custom_date'){
									$("#custom_start_date").show();
									$("#custom_end_date").show();
								}else{
									$("#custom_start_date").hide();
									$("#custom_end_date").hide();
								}
						}); 
					</script>




					<div class="col-md-2">
					    <p><b>Filter Tutor</b></p>

					    <?php 
						   $row_users  = $DB->get_records("assign_subs_tutors",array("active"=>1, "deleted_status"=>0), 'name', $fields='*', $limitfrom=0, $limitnum=0);
					    ?>

						<select name="tutor_id_archive" class="form-control filterupdated formfilterdata" id="tutor_id_archive" >
							<option value="All">All</option>
								<?php 

								foreach ($row_users as $value) { 
									?>
									<option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>

							    <?php } ?>
						</select>
					</div>


					<div class="col-md-2">
						<p><b>Admission Type</b></p>
						<select name="admission_type_archive filterupdated formfilterdata" class="form-control" id="admission_type_archive" >
							<option value="All">All</option>
							<option value="general">General</option>
							<option value="priority">Priority</option>
						</select>
					</div>



				</div>

				<div class="row" style="margin-top: 10px;">

					<div class="col-md-6" id="custom_start_date" style="display: none;">
						<p><b>Start Date</b></p>
						<input type="date" name="custom_start_date" id="custom_start_date_val" class="form-control filterupdated formfilterdata">
					</div>


					<div class="col-md-6" id="custom_end_date"   style="display: none;">
						<p><b>End Date</b></p>
					    <input type="date" name="custom_end_date"  id="custom_end_date_val" class="form-control filterupdated formfilterdata">
					</div>

				</div>


				<div class="row">
					<div class="col-md-12" align="center" style="font-size: 20px;"><b>Total: <span id="total_archive"></span></b></div>
				</div>


				<table class="table table-striped table-bordered" id="sorttable_archive" style="width: 100%;"> 
					<thead>  
						<tr>  
							<th>Courses</th>
							<th>Students</th>  
							<th>Date</th>  
							<th>Assigned</th>  
							<th>Submission</th>   
						</tr>  
					</thead> 

					<tbody>  


					</tbody>  
				</table>

				
				<script>
				var dataTable_priority = null;
				var dataTable_archive =null;
				var dataTable_general =null;
				$(document).ready(function(){
					$("#course_id_archive").change(function(){
						dataTable_archive.draw();
					});

					$("#search_student_archive").change(function(){
						dataTable_archive.draw();
					});
				
					$("#tutor_id_archive").change(function(){
						dataTable_archive.draw();
					});

					$("#admission_type_archive").change(function(){
					  dataTable_archive.draw();

					});

					$("#filter_date").change(function(){
					  dataTable_archive.draw();
					});

					$("#custom_start_date_val").change(function(){
					  dataTable_archive.draw();
					});

					$("#custom_end_date_val").change(function(){
					  dataTable_archive.draw();
					});

					dataTable_archive= $("#sorttable_archive").DataTable({
						"order":false,
						"processing": true,
						"serverSide": true,
						"serverMethod": "post",
						"ajax": {
							"url": "<?php echo $CFG->wwwroot.'/local/assignment_subscription/ajaxMarkArchive.php'; ?>",

							"data": function(data){
								data.course_id_archive = $("#course_id_archive").val();
								data.search_student_archive = $("#search_student_archive").val();
								data.tutor_id_archive = $("#tutor_id_archive").val();
								data.admission_type_archive = $("#admission_type_archive").val();
								data.custom_start_date = $("#custom_start_date_val").val();
								data.custom_end_date = $("#custom_end_date_val").val();
								data.filter_date = $("#filter_date").val();
							}
						},
						"drawCallback": function(settings) {
							$("#total_archive").text(settings.json.iTotalDisplayRecords);
						},
						"columns": [
						
							{ data: "Courses_archive", orderable: false },
							{ data: "Students_archive", orderable: false },
							{ data: "Date_marked_archive", orderable: false },
							{ data: "Assigned_archive", orderable: false },
							{ data: "Submission_archive", orderable: false },
						]
					});

					$("#search_student_archive").multiselect({
						widthSynchronizationMode: 'always',
						includeSelectAllOption: true,
						enableCaseInsensitiveFiltering: true,
						buttonContainer: '<span  />',
						buttonClass: 'form-control',
						maxHeight: 200,
						buttonWidth: '100%',
						enableFiltering: false
						
					});
				});
				</script>
		</div>
	</div>






<?php
}
if($activetab ==2){

?>












 <!-- General Tab -->

	<div id="content-2">
	<!-- <div class="table-responsive"> -->
				<div id="general_tab">
				<div class="row">

					<div class="col-md-3">
					<p><b>Filter Course</b></p>
					<select name="course_id_general" class="form-control filterupdated formfilterdata" id="course_id_general">
						<option value="All">All courses</option>
						<?php  

						$courses = get_courses($categoryid="all", $sort="c.fullname ASC", $fields="c.*");
						foreach ($courses as $value) { 
						if($value->id > 1){

						?>

						<option value="<?php echo $value->id; ?>"><?php echo $value->fullname; ?></option>

					  <?php }} ?>

					</select>
					</div>


					<?php 

					$query = "SELECT distinct(u.id), u.firstname, u.lastname, u.email FROM {$CFG->prefix}user u 
					INNER JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
					INNER JOIN {$CFG->prefix}context ct ON ct.id = ra.contextid
					INNER JOIN {$CFG->prefix}role r ON r.id = ra.roleid 
					WHERE r.id =5 AND u.deleted = 0 ";
					$all_users_general = $DB->get_records_sql($query);
				
					?>

					<div class="col-md-3">
						<p><b>Search Student</b></p>
						<select name="search_student_general" class="form-control filterupdated formfilterdata" id="search_student_general">
						<option value="All">All</option>
						<?php 
						foreach ($all_users_general as $data_user) {  
						?>
						<option value="<?php echo $data_user->id; ?>"><?php echo $data_user->firstname." ".$data_user->lastname." (".$data_user->email.")"; ?></option>
						<?php } ?>
						</select>
					</div>




					<div class="col-md-2">
						<p><b>Filter Order</b></p>
						<select name="filter_order_general" class="form-control filterupdated formfilterdata" id="filter_order_general" >
							<option value="oldest_to_newest" >Oldest to newest</option>
							<option value="newest_to_oldest" >Newest to oldest</option>
						</select>
					</div>




					<div class="col-md-2">
					    <p><b>Filter Status</b></p>
							<select name="filter_status_general" class="form-control filterupdated formfilterdata" id="filter_status_general" >
								<option value="All">All</option>
								<option value="pending">Pending</option>
								<option value="in_progress">In progress</option>
							</select>
					</div>



					<div class="col-md-2">
					    <p><b>Filter Tutor</b></p>

							<?php 
							$row_users  = $DB->get_records("assign_subs_tutors",array("active"=>1, "deleted_status"=>0), 'name', $fields='*', $limitfrom=0, $limitnum=0);
							?>

							<select name="tutor_id_general" class="form-control filterupdated formfilterdata" id="tutor_id_general" >
									<option value="All">All</option>
									<?php 
									foreach ($row_users as $value) { 
									?>
									<option value="<?php echo $value->id; ?>" ><?php echo $value->name; ?></option>

							<?php } ?>
							
						</select>
					</div>

				</div>

				<br>

				<div class="row">
					<div class="col-md-4" align="left" style="font-size: 20px;"><b>Total: <span id="total_general"></span></b></div>
					<div class="col-md-4" align="center" style="font-size: 20px;"><b>Target: <?php echo get_config( 'local_assignment_subscription', 'target_general'); ?></b>
					</div>
					<div class="col-md-4" align="right" style="font-size: 20px;"><b>Daily Mark count: <span id="daily_count_general"></span></b></div>
				</div>



				<table class="table table-striped table-bordered" id="sorttable_general" style="width: 100%;"> 
				<thead>  
					<tr>  
						<th>Courses</th>
						<th>Students</th>  
						<th>Date</th>  
						<th>Status</th> 
						<th>Assign</th>  
						<th>Submission</th>   
					</tr>  
				</thead> 

				<tbody>  


				</tbody>  
				</table>
				<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

				<script>
					$(document).ready(function(){
						$(document).on("change", ".update_tutor_general", function(){
							var tutor_id = $(this).val();
							var courseid = $(this).data("courseid");
							var userid = $(this).data("userid");
							var assignmentid = $(this).data("assignmentid");
							if(tutor_id){
								$.ajax ({
								    type: "POST",
								    url: "<?php echo $CFG->wwwroot;?>/local/assignment_subscription/ajaxPost.php",
								    data: { tutor_id: tutor_id , course_id: courseid, user_id: userid, assignment_id: assignmentid },
								    success : function(htmlresponse) {
								     dataTable_general.ajax.reload(null, false);
								    }
								});
							}
						});

						$("#course_id_general").change(function(){
							dataTable_general.draw();
						});

						$("#search_student_general").change(function(){
							dataTable_general.draw();
						});

						$("#filter_order_general").change(function(){
							dataTable_general.draw();
						});
						
						$("#filter_status_general").change(function(){
							dataTable_general.draw();
						});
					
						$("#tutor_id_general").change(function(){
							dataTable_general.draw();
						});
					 	dataTable_general = $("#sorttable_general").DataTable({
							"order":false,
							"processing": true,
							"serverSide": true,
							"serverMethod": "post",
							"ajax": {
								"url": "<?php echo $CFG->wwwroot.'/local/assignment_subscription/ajaxMarkGeneral.php'; ?>",
								"data": function(data){
									data.course_id = $("#course_id_general").val();
									data.search_student = $("#search_student_general").val();
									data.filter_order = $("#filter_order_general").val();
									data.filter_status = $("#filter_status_general").val();
									data.tutor_id = $("#tutor_id_general").val();
								}
							},
							"drawCallback": function(settings) {
								$("#total_general").text(settings.json.iTotalDisplayRecords);
								$("#daily_count_general").text(settings.json.iGeneralMarked);
							},
							"columns": [
								{ data: "Courses_general", orderable: false },
								{ data: "Students_general", orderable: false },
								{ data: "Submission_Date_general", orderable: false },
								{ data: "Status_general", orderable: false },
								{ data: "Assign_general", orderable: false },
								{ data: "Submission_general", orderable: false },
							]
						});

						$("#search_student_general").multiselect({
							widthSynchronizationMode: 'always',
							includeSelectAllOption: true,
							enableCaseInsensitiveFiltering: true,
							buttonContainer: '<span  />',
							buttonClass: 'form-control',
							maxHeight: 200,
							buttonWidth: '100%',
							enableFiltering: false
							
						});
					});


					function selectEnable_general(elem){
							document.getElementById('span_none'+elem).style.display = 'none';
							document.getElementById('select'+elem).style.display = 'block';
					}

			    </script>
		</div>
	</div>








<?php
}
if($activetab ==3){

?>












  <!-- Priority Tab -->

	<div id="content-3">
  <!-- <div class="table-responsive"> -->
				<div id="priority_tab">
				<div class="row">

					<div class="col-md-3">
					<p><b>Filter Course</b></p>
					<select name="course_id_priority" class="form-control filterupdated formfilterdata" id="course_id_priority">
						<option value="All">All courses</option>
						<?php  

						$courses = get_courses($categoryid="all", $sort="c.fullname ASC", $fields="c.*");
						foreach ($courses as $value) { 
						if($value->id > 1){

						?>

						<option value="<?php echo $value->id; ?>"><?php echo $value->fullname; ?></option>

					  <?php }} ?>

					</select>
					</div>

					<?php 

					$time = time();
					$query = "SELECT distinct(u.id), u.firstname, u.lastname, u.email FROM {$CFG->prefix}user u 
					INNER JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
					INNER JOIN {$CFG->prefix}context ct ON ct.id = ra.contextid
					INNER JOIN {$CFG->prefix}role r ON r.id = ra.roleid 
					WHERE r.id =5 AND u.deleted = 0";
					$all_users_priority  = $DB->get_records_sql($query);
				
					?>


					<div class="col-md-3">
						<p><b>Search Student</b></p>
						<select name="search_student_priority" class="form-control filterupdated formfilterdata" id="search_student_priority">
						<option value="All">All</option>
						<?php 


						foreach ($all_users_priority as $data_user) {  


						?>
						<option value="<?php echo $data_user->id; ?>"><?php echo $data_user->firstname." ".$data_user->lastname." (".$data_user->email.")"; ?></option>
						<?php } ?>
						</select>
					</div>




					<div class="col-md-2">
						<p><b>Filter Order</b></p>
						<select name="filter_order_priority" class="form-control filterupdated formfilterdata" id="filter_order_priority" >
							<option value="oldest_to_newest" >Oldest to newest</option>
							<option value="newest_to_oldest" >Newest to oldest</option>
						</select>
					</div>




					<div class="col-md-2">
					    <p><b>Filter Status</b></p>
						<select name="filter_status_priority" class="form-control filterupdated formfilterdata" id="filter_status_priority" >
							<option value="All">All</option>
							<option value="pending">Pending</option>
							<option value="in_progress">In progress</option>
						</select>
					</div>



					<div class="col-md-2">
					    <p><b>Filter Tutor</b></p>

					    <?php 
						$row_users  = $DB->get_records("assign_subs_tutors",array("active"=>1, "deleted_status"=>0), 'name', $fields='*', $limitfrom=0, $limitnum=0);
					    ?>

						<select name="tutor_id_priority" class="form-control filterupdated formfilterdata" id="tutor_id_priority" >
							<option value="All">All</option>
								<?php 

								foreach ($row_users as $value) { 
								?>
								<option value="<?php echo $value->id; ?>" ><?php echo $value->name; ?></option>

								<?php } ?>
							
						</select>
					</div>

				</div>

				<br>

				<div class="row">
					<div class="col-md-4" align="left" style="font-size: 20px;"><b>Total: <span id="total_priority"></span></b></div>


					<div class="col-md-4" align="center" style="font-size: 20px;"><b>Target: 
					<?php echo get_config( 'local_assignment_subscription', 'target_priority'); ?>
						</b>
					</div>

					<div class="col-md-4" align="right" style="font-size: 20px;"><b>Daily Mark count: <span id="daily_count_priority"></span></b></div>
				</div>



				<table class="table table-striped table-bordered" id="sorttable_priority" style="width: 100%;"> 
				<thead>  
					<tr>  
						<th>Courses</th>
						<th>Students</th>  
						<th>Date</th>  
						<th>Status</th> 
						<th>Assign</th>  
						<th>Submission</th>   
					</tr>  
				</thead> 

				<tbody>  


				</tbody>  
				</table>

				<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
				<script>

					$(document).ready(function(){

						$(document).on("change", ".update_tutor_priority", function(){
							var tutor_id = $(this).val();
							var courseid = $(this).data("courseid");
							var userid = $(this).data("userid");
							var assignmentid = $(this).data("assignmentid");
							if(tutor_id){
								$.ajax ({
								    type: "POST",
								    url: "<?php echo $CFG->wwwroot;?>/local/assignment_subscription/ajaxPost.php",
								    data: { tutor_id: tutor_id , course_id: courseid, user_id: userid, assignment_id: assignmentid },
								    success : function(htmlresponse) {
										dataTable_priority.ajax.reload(null, false);
								    }
								});
							}
						});


						$("#course_id_priority").change(function(){
							dataTable_priority.draw();
						});

						$("#search_student_priority").change(function(){
							dataTable_priority.draw();
						});


						$("#filter_order_priority").change(function(){
							dataTable_priority.draw();
						});

						
						$("#filter_status_priority").change(function(){
							dataTable_priority.draw();
						});
					

						$("#tutor_id_priority").change(function(){
							dataTable_priority.draw();
						});
						dataTable_priority = $("#sorttable_priority").DataTable({
							"order":false,
							"processing": true,
							"serverSide": true,
							"serverMethod": "post",
							"ajax": {
								"url": "<?php echo $CFG->wwwroot.'/local/assignment_subscription/ajaxMarkPriority.php'; ?>",
								"data": function(data){
									data.course_id_priority = $("#course_id_priority").val();
									data.search_student_priority = $("#search_student_priority").val();
									data.filter_order_priority = $("#filter_order_priority").val();
									data.filter_status_priority = $("#filter_status_priority").val();
									data.tutor_id_priority = $("#tutor_id_priority").val();
								}
							},
							"drawCallback": function(settings) {
							   $("#total_priority").text(settings.json.iTotalDisplayRecords);
							   $("#daily_count_priority").text(settings.json.iPriorityMarked);
							},
							"columns": [
								{ data: "Courses_priority", orderable: false  },
								{ data: "Students_priority", orderable: false  },
								{ data: "Submission_Date_priority", orderable: false  },
								{ data: "Status_priority", orderable: false },
								{ data: "Assign_priority", orderable: false  },
								{ data: "Submission_priority", orderable: false },
							]
						});

						$("#search_student_priority").multiselect({
							widthSynchronizationMode: 'always',
							includeSelectAllOption: true,
							enableCaseInsensitiveFiltering: true,
							buttonContainer: '<span  />',
							buttonClass: 'form-control',
							maxHeight: 200,
							buttonWidth: '100%',
							enableFiltering: false
							
						});
					});


					  function selectEnable_priority(elem){
							document.getElementById('span_none_priority'+elem).style.display = 'none';
							document.getElementById('select_priority'+elem).style.display = 'block';
						}
			    </script>
		</div>
	</div>
</div>
<?php
}
?>

<script>

	$(document).ready(function(){
		$("[name=tab-btn]").change(function(){
			var tabtype = $(this).val();
			// location.href = `<?php echo $CFG->wwwroot.'/local/assignment_subscription/mark_student.php?tabtype=';?>${tabtype}`;
		})
		$(document).on("click", "#reloaddata", function(){
			<?php
				if($activetab == 1){
					echo 'dataTable_archive.draw();';
				} else if($activetab == 2){
					echo 'dataTable_general.draw();';
				} else if($activetab == 3){
					echo 'dataTable_priority.draw();';
				} else {

				}
			?>
		});

		var olddata = <?php echo (isset($_SESSION['markdata']))?json_encode($_SESSION['markdata']):'{}';?>;
		$.each(olddata, function(dkey, dvalue) {
			console.log(`${dkey}: ${dvalue}`);
			if($(`#${dkey}`).length > 0){
				$(`#${dkey}`).val(dvalue);
			}
		});
		console.log("olddata- ", olddata);
	});
	$(document).on("change", ".filterupdated", function(){
		setTimeout(() => {
			var markdata = {
				"tabbtn": $("[name=tab-btn]:checked").val()
			};
			$(".formfilterdata").each(function( index ) {
				var eleid = $(this).attr("id");
				var eleval = $(this).val();
				markdata[eleid]=eleval;
			});
			$.ajax ({
				type: "POST",
				url: "<?php echo $CFG->wwwroot;?>/local/assignment_subscription/mark_pagedata.php",
				data: markdata,
				success : function(htmlresponse) {
					console.log("htmlresponse- ", htmlresponse);
				}
			});
		}, 200);
	});
</script>




<?php echo $OUTPUT->footer(); ?>