<?php
function couse_mapping(){
	global $wpdb;
	$para = $_GET['id'];
	// echo $para;
	$rows = $wpdb->get_row("SELECT * FROM wp_multistepform WHERE id = ".$para."");
	$postdata = unserialize($rows->post_data);

	$html = '
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<div class="row" id="userinfo" >
		<div class="col-lg-6 col-12 mb-3">
			<h5>Study Level</h5>
			<h5>Admission Type</h5>
			<h5>Program</h5>
			<h5>Country</h5>
			<h5>City</h5>
			<h5>Subcity</h5>
			<h5>Region</h5>
			<h5>Zone</h5>
			<h5>Wore Da</h5>
			<h5>Special Location</h5>
			<h5>House Number</h5>
			<h5>Phone Number</h5>
			<h5>Alternate Phone no</h5>
			<h5>Contact Person name</h5>
			<h5>Contact Person Relationship</h5>
			<h5>Contact Person Number</h5>
			<h5>Contact Person Current Occupation</h5>
			<h5>Came Current Employer</h5>
			<h5>Email Employer</h5>
			<h5>Employer Phone Number</h5>
			<h5>Po Box</h5>
			<h5>Last Attended High School</h5>
			<h5>School Address</h5>
			<h5>Grade Matric Result</h5>
			<h5>Exam Year</h5>
			<h5>First Name</h5>
			<h5>Middle Name</h5>
			<h5>Last Name</h5>
			<h5>Gender</h5>
			<h5>DOB</h5>
			<h5>Nationality</h5>
			<h5>Birth Place</h5>
			<h5>Status</h5>
			<h5>Email</h5>
			<h5>pass_word</h5>
			<h5>c_password</h5>
		</div>
		<div class="col-lg-6 col-12 mb-3">
			<h5>'.$postdata["study_level"].'</h5>
			<h5>'.$postdata["admission_type"].'</h5>
			<h5>'.$postdata["program"].'</h5>
			<h5>'.$postdata["country"].'</h5>
			<h5>'.$postdata["city"].'</h5>
			<h5>'.$postdata["subcity"].'</h5>
			<h5>'.$postdata["region"].'</h5>
			<h5>'.$postdata["zone"].'</h5>
			<h5>'.$postdata["wore_da"].'</h5>
			<h5>'.$postdata["special_location"].'</h5>
			<h5>'.$postdata["house_number"].'</h5>
			<h5>'.$postdata["house_number"].'</h5>
			<h5>'.$postdata["alternate_phone_no"].'</h5>
			<h5>'.$postdata["contact_person_name"].'</h5>
			<h5>'.$postdata["contact_Person_relationship"].'</h5>
			<h5>'.$postdata["contact_person__number"].'</h5>
			<h5>'.$postdata["contact_person_current_occupation"].'</h5>
			<h5>'.$postdata["name_current_employer"].'</h5>
			<h5>'.$postdata["email_employer"].'</h5>
			<h5>'.$postdata["employer_phone_number"].'</h5>
			<h5>'.$postdata["po_box"].'</h5>
			<h5>'.$postdata["last_attended_high_school"].'</h5>
			<h5>'.$postdata["school_address"].'</h5>
			<h5>'.$postdata["grade_matric_result"].'</h5>
			<h5>'.$postdata["exam_year"].'</h5>
			<h5>'.$postdata["fname"].'</h5>
			<h5>'.$postdata["mname"].'</h5>
			<h5>'.$postdata["lname"].'</h5>
			<h5>'.$postdata["gender"].'</h5>
			<h5>'.$postdata["dob"].'</h5>
			<h5>'.$postdata["nationality"].'</h5>
			<h5>'.$postdata["place_birth"].'</h5>
			<h5>'.$postdata["m_status"].'</h5>
			<h5>'.$postdata["e_mail"].'</h5>
			<h5>'.$postdata["e_mail"].'</h5>
			<h5>'.$postdata["e_mail"].'</h5>
		</div>
		
	<div class="modal" id="myModal">
		<div class="modal-body">
			<textarea type="text" class="txtarea" name="massage"></textarea>
			<button type="submit">Submit</button>
			<button type="cancle" class="removepopup" >Cancel</button>
		</div>
	</div>

		<div class="btnsection">
			<button type="button" class="btn btn-success btn-lg appr" >Approve</button>
			<button type="button" class="btn btn-primary btn-lg fdback">Feedback</button>
			<button type="button" class="btn btn-danger btn-lg cancel">Cancel</button>
		<div>

	</div>
	<style>
		#userinfo .btnsection{
			text-align: inherit;
		}
		#myModal .txtarea{
			height : 145px;
		}
		#myModal.modal{
			top: 35%;	
			left: 45%;	
			padding: 20px;
			width: auto;
			height: auto;
			background: #a6bfda;
			border-radius: 5px;
		}
		#myModal .modal-body{
			display: table-caption;
		}
		.visible{
			display : block ;             
		}
	</style>
	
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script>
		$(document).ready(function() {			
			$(".fdback").click(function() {
				$(".modal").addClass("visible");
			});
			$(".removepopup").click(function() {
				$(".modal").removeClass("visible");
			});
		});
	</script>
	
	';

  	echo $html;


}
function impNote(){  
	?>
	<div class="impnote">
		<h1 class="ntitle">Enrollment Custom Pages</h1>
		<p class="ntext">After installation, Enrolled Course Plugin, You need to  creates the following new pages.</p>
		<p class="ntext2">You need to create two custom pages (i) thanku and (ii) Moodle loging.</p>
		<ul> 
			<li> <b>Go to dashboard->pages-> add new</b></li>
			<li>Thanku – Create thanku page and select Thank You Template.</li>
			<li>Moodle login – Create Moodle login page and select moodle login  Template.</li>
		</ul>
	</div>
	<style>
		.impnote {
			background: #fff;
			padding: 15px;
			margin-top: 20px;
			margin-left: 15px;
			margin-right: 18px;
			box-shadow: 0 0 5px 0 #ddd;
			border: 1px solid #eee;
			border-top: 5px solid #c1970c;
			font-size: 16px;
		}
		.ntitle{
			color: #c1970c;
			text-transform: capitalize;
		}
		.ntext{
			font-size: 18px;
			color: #777;
		}
		.ntext2{
			font-size: 16px;

		}
		.impnote ul {
			padding: 0;
			margin: 0;
			margin-left: 30px;
			line-height: 30px;
			list-style: circle;
		}
	</style>

	<?php 
}
?>