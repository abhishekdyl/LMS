<?php 
require_once('../../config.php');
global $DB, $USER, $PAGE;
$PAGE->requires->jquery();
require_login();
$is_siteadmin = is_siteadmin();
$context = \context_system::instance();
$current_logged_in_user =  $USER->id;
$has_capability = has_capability('local/assignment_subscription:createuser_subscription', $context, $current_logged_in_user);
if (!$has_capability) {
$urltogo_dashboard = $CFG->wwwroot.'/my/';
redirect($urltogo_dashboard, 'You do not have permission to view this page', null, \core\output\notification::NOTIFY_WARNING);
}


$PAGE->set_title('Assignment Subscription');
$PAGE->set_heading('Assignment Subscription');


$query = 'SELECT * FROM {assign_subs_users}';
$all_users = $DB->get_records_sql($query);



echo $OUTPUT->header();


?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
<style type="text/css">
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

<div class="container" id="cont">
	<a class="btn btn-primary" href="home.php">Back</a><br/><br/>
<h2>List of Priority Users</h2><br>
<div class="table-responsive">


<div class="row">

	<div class="col-md-3">
	<p><b>Search Student</b></p>
	<select name="search_student" class="form-control" id="search_student">
		<option value="All">All</option>
		<?php foreach ($all_users as $data_user) {  

			$user_id = $data_user -> userid; 
			$sqli_query_user = "SELECT id,firstname,lastname,email FROM {user} WHERE  id=".$user_id;
			$row_query_user  = $DB->get_records_sql($sqli_query_user);

			$firstname_user = array_column($row_query_user, firstname);
			$lastname_user  = array_column($row_query_user, lastname);
			$email_user  = array_column($row_query_user, email);

		?>
		<option value="<?php echo $user_id; ?>"><?php echo $firstname_user[0]." ".$lastname_user[0]." (".$email_user[0].")"; ?></option>
		<?php } ?>
	</select>
	</div>


	<div class="col-md-3">
	<p><b>Subscription Method</b></p>
	<select name="subscription_method" class="form-control" id="subscription_method">
		<option value="All">All</option>
		<option value="Manual Subscription">Manual Subscription</option>
		<option value="Online Subscription">Online Subscription</option>
	</select>
	</div>


	<div class="col-md-3">
	<p><b>Filter Date</b></p>
	<select name="filter_date" class="form-control" id="filter_date" >
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



	<div class="col-md-3">
	    <p><b>Filter Status</b></p>
		<select name="filter_status" class="form-control" id="filter_status" >
			<option value="All">All</option>
			<option value="1">Active</option>
			<option value="0">Inactive</option>
		</select>
	</div>

</div>



<div class="row" style="margin-top: 10px;">
	<div class="col-md-6" id="custom_start_date" style="display: none;">
		<p><b>Start Date</b></p>
		<input type="date" name="custom_start_date" id="custom_start_date_val" class="form-control">
	</div>
	<div class="col-md-6" id="custom_end_date"   style="display: none;">
		<p><b>End Date</b></p>
	    <input type="date" name="custom_end_date"  id="custom_end_date_val" class="form-control">
	</div>
</div>

<br>

<div class="row">
	<div class="col-md-12" align="right" style="font-size: 20px;"><b>Total: <span id="total_view_subs"></span></b></div>
</div>



<table class="table table-striped table-bordered" id="sorttable" style="width:100%; color: " > 
<thead>  
	<tr>  
		<th>First name/Surname</th>  
		<th>Start Date</th>  
		<th>End Date</th> 
		<th>Cost (&#163;)</th>  
		<th>Subscription Method</th>  
		<th>Subscription Duration</th>  
		<th>Status</th>
		<th>Edit</th>  
	</tr>  
</thead> 

<tbody>  


</tbody>  
</table>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>

	$(document).ready(function(){

		$("#search_student").change(function(){
		dataTable.draw();
		});

		$("#subscription_method").change(function(){
		dataTable.draw();
		});


		$("#filter_status").change(function(){
		dataTable.draw();
		});

		
		$("#filter_date").change(function(){
		dataTable.draw();
		});
	

		$("#custom_start_date_val").change(function(){
		dataTable.draw();
		});

		$("#custom_end_date_val").change(function(){
		dataTable.draw();
		});

		

	var dataTable = $("#sorttable").DataTable({
		"order":[[1, "desc"]],
		"processing": false,
		"serverSide": true,
		"serverMethod": "post",
		"ajax": {
		"url": "<?php echo $CFG->wwwroot.'/local/assignment_subscription/ajax.php'; ?>",

		"data": function(data){
			data.search_student = $("#search_student").val();
			data.subscription_method = $("#subscription_method").val();
			data.filter_status = $("#filter_status").val();
			data.custom_start_date = $("#custom_start_date_val").val();
			data.custom_end_date = $("#custom_end_date_val").val();
			data.filter_date = $("#filter_date").val();
		}
		},

		"drawCallback": function(settings) {
			//console.log(settings);
			
			$("#total_view_subs").text(settings.json.iTotalDisplayRecords);

		},

	"columns": [
		
			{ data: "username" },
			{ data: "start_date" },
			{ data: "end_date" },
			{ data: "cost" },
			{ data: "subscription_method" },
			{ data: "subscription_duration" },
			{ data: "status" },
			{ data: "link", orderable: false, targets: -1  },
			]
		                       
		});

	});

</script>
<script>
					
	$("#search_student").multiselect({
		widthSynchronizationMode: 'always',
		includeSelectAllOption: true,
		enableCaseInsensitiveFiltering: true,
		buttonContainer: '<span  />',
		buttonClass: 'form-control',
		maxHeight: 200,
		buttonWidth: '100%',
		enableFiltering: false
		
	});

</script>


</div>
	<button onclick="window.location='create_subscription.php'" class="btn btn-primary" style="margin: 5px;">Add a new user</button>
</div>

<?php echo $OUTPUT->footer(); ?>