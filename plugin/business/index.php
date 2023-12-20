    <?php
require_once('../../config.php');
global $CFG, $DB, $PAGE, $USER; 
require_login();
$loguser = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
if(empty($loguser)){
    redirect($CFG->wwwroot);
}
echo $OUTPUT->header();

echo '
<link rel="stylesheet" href="https://www.animalhealthacademy.com.au/technicalhub/css/fluidable.css">
<style>
.col-group>div { margin-bottom: 15px;}
.card {	height: 100%;padding: 10px;box-sizing: border-box;background-color:rgb(242, 242, 242);}
.card a { width:100%;}
.card button {margin:0;width:100%;}
.card h5 { color:rgb(247, 148, 29);margin-top: 0;}
.card .card-text {line-height: normal;}
@media (min-width: 1200px) {
	.col-group {display:flex;flex-flow: wrap;}
  .col-group>div { margin-bottom: 40px;}
	.card {	display: flex;flex-wrap: wrap;align-content: space-between;min-height: 290px;}
	}
	@media (min-width: 992px) and (max-width: 1199px) {
	.card {	min-height: 260px;}
	.card {	display: flex;flex-wrap: wrap;align-content: space-between;}
	}
	
</style>
<h2>Settings for Your Private eLearning Portal</h2>
<p>This page is <b>only visible</b> to yourself and any other assigned Group Administrators within your business or group of companies. Here you can manage learning content, user access, your priviate menu plus access administrative report/s. If you need assistance, please contact Animal Health Academy customer support at AHAnimalHealthAcademy.AU@boehringer-ingelheim.com </p>
<p>&nbsp;</p>

<div class="col-group">
<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
    <h5 class="card-title">Manage Courses</h5>
    <p class="card-text">Create a new eLearning course or activity for your private member group.</p>
   </div>
    <a href= "'.$CFG->wwwroot.'/local/business/manage_courses/"><button name="manageCoursed">Manage Course</button> </a>
</div>
</div>
<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
    <h5 class="card-title">Learning Programs</h5>
    <p class="card-text">Create or manage structured learning programs for your private member group. You can use private modules or Animal Health Academy modules, or both.</p>
   </div>
   <a href= "'.$CFG->wwwroot.'/local/business/learning_program/"><button name="manage_programs">Manage Programs</button> </a>
</div>
</div>

<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
    <h5 class="card-title">Menu Manager</h5>
    <p class="card-text">This is where you can edit the dropdown menu for your private eLearning portal. </p>
   </div>
   <a href= "'.$CFG->wwwroot.'/local/business/menu_manager/"><button name="menuManager">Edit Menu</button> </a>
</div>
</div>

<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
    <h5 class="card-title">Company List</h5>
    <p class="card-text">View/manage the list of companies whose staff have access to your private eLearning portal.</p>
   </div>
   <a href= "'.$CFG->wwwroot.'/local/business/manage_company/"> <button name="manageCompany">Manage Company List</button> </a>
</div>
</div>


<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
    <h5 class="card-title">Members</h5>
    <p class="card-text">View/manage permissions for Animal Health Academy users that have access to your private eLearning portal</p>
   </div>
   <a href= "'.$CFG->wwwroot.'/local/business/users"> <button name="manageCompany">Member Access</button> </a>
</div>
</div>



<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
    <h5 class="card-title">Assign learnings</h5>
    <p class="card-text">Assign learning to usersus</p>
   </div>
   <a href= "'.$CFG->wwwroot.'/local/business/assign_learning/"> <button name="manageCompanyassign">Assign Learning</button> </a>
</div>
</div>

</div>
<hr/>
<h2>Reports</h2>
<p class="card-text">Download activity reports for your members.</p>
<ul style="margin-bottom:40px;">
	<li><a href= "'.$CFG->wwwroot.'/local/business/activity_report/">Activity Reports </a></li>
	<li><a href= "#">Example Report 1 </a></li>
	<li><a href= "#">Example Report 2 </a></li>
</ul>
';
echo $OUTPUT->footer();