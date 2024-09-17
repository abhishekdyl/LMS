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
.card {	height: 100%;padding: 0px;box-sizing: border-box;background-color:#f7f7f7;border: 0px solid #e3e3e3;}
.card a { width:100%;}
.card a.widthauto { width:auto; color:white !important;}
.card button {margin:0;width:100%;font-weight: bold;border-top: 18px solid #fff;}
.card h5 { color:#515151;margin-top: 0;font-weight: bold !important;font-size: 1.6rem;}
.card .card-text {line-height: normal;}
.card-top { padding:10px; text-align:center;width:100%;}
@media (min-width: 1200px) {
	.col-group {display:flex;flex-flow: wrap;}
  .col-group>div { margin-bottom: 40px;}
	.card {	display: flex;flex-wrap: wrap;align-content: space-between;-min-height: 290px;}
	}
	@media (min-width: 992px) and (max-width: 1199px) {
	.card {	min-height: 260px;}
	.card {	display: flex;flex-wrap: wrap;align-content: space-between;}
	}
	
</style>
<h2>Settings for Your Private eLearning Portal</h2>
<p>This page is <b>only visible</b> to yourself and any other assigned Group Administrators within your business or group of companies. Here you can manage learning content, user access, your priviate menu plus access administrative report/s. If you need assistance, please contact Animal Health Academy customer support at AHAnimalHealthAcademy.AU@boehringer-ingelheim.com </p>
<p>&nbsp;</p>

<div class="card card-wrap">
  <div class="card-top text-center"><br>
    <h5 class="card-title">Home page & quick links</h5>
    <a class="widthauto btn btn-primary" href= "'.$CFG->wwwroot.'/local/business/homepage/edit.php">Edit Home Page </a>
  <br><br></div>
</div>
<br>

<div class="col-group">
<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
  <img src="images/icon-courses.png"/>
    <h5 class="card-title">Create <br>& edit</h5>
    <p class="card-text">Build custom courses & activities</p>
   </div>
    <a href= "'.$CFG->wwwroot.'/local/business/manage_courses/"><button name="manageCoursed">View Courses</button> </a>
</div>

</div>
<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
  <img src="images/icon-learningpro.png"/>
    <h5 class="card-title">Learning <br>programs</h5>
    <p class="card-text">Standardise learning journeys</p>
   </div>
   <a href= "'.$CFG->wwwroot.'/local/business/learning_program/"><button name="manage_programs">View Programs</button> </a>
</div>
</div>

<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
  <img src="images/icon-assign.png"/>
    <h5 class="card-title">Assigned <br>learning</h5>
    <p class="card-text">Set learning objectives for your&nbsp;members</p>
   </div>
   <a href= "'.$CFG->wwwroot.'/local/business/assign_learning/"> <button name="manageCompanyassign">Assign Learning</button> </a>
</div>
</div>

<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
  <img src="images/icon-permissions.png"/>
    <h5 class="card-title">Staff <br>permissions</h5>
    <p class="card-text">Set access for staff to your private&nbsp;area</p>
   </div>
   <a href= "'.$CFG->wwwroot.'/local/business/users"> <button name="manageCompany">Permissions</button> </a>
</div>
</div>

<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
  <img src="images/icon-menu.png"/>
    <h5 class="card-title">Menu <br>manager</h5>
    <p class="card-text">Customise your private dropdown&nbsp;menu</p>
   </div>
   <a href= "'.$CFG->wwwroot.'/local/business/menu_manager/"><button name="menuManager">Edit Menu</button> </a>
</div>
</div>

<div class="col-dt-4 col-ld-2">
<div class="card card-wrap">
  <div class="card-top">
  <img src="images/icon-company.png"/>
    <h5 class="card-title">Company <br>list</h5>
    <p class="card-text">Companies with access to your&nbsp;content</p>
   </div>
   <a href= "'.$CFG->wwwroot.'/local/business/manage_company/"> <button name="manageCompany">View Companies</button> </a>
</div>
</div>








</div>
<hr/>
<h2>Reports</h2>
<p class="card-text">Download activity reports for your members.</p>
<ul style="margin-bottom:40px;">
	<li><a href= "'.$CFG->wwwroot.'/local/business/activity_report/">Activity Reports </a></li>
</ul>
';
echo $OUTPUT->footer();