<?php
require_once('../../config.php');
global $CFG, $DB, $PAGE, $USER, $COMPLETION_CRITERIA_TYPES;
require_login();
$id=optional_param('id',0,PARAM_INT);
$userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1));
if(empty($userbrand)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
$dataval = $DB->get_record('business_learning_homapage',array("cbid"=>$userbrand->cbid));
if(empty($dataval)){
  redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
$settingdata = json_decode($dataval->settingdata);
$PAGE->requires->jquery();
$PAGE->requires->js( new moodle_url( $CFG->wwwroot. '/theme/lambda/jquery/slideshow.js' ), true );
echo "<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\"><link href=\"https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;300;400;600;800;900&display=swap\" rel=\"stylesheet\"><link href=\"https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700&display=swap\" rel=\"stylesheet\"><link href=\"" .$CFG->wwwroot."/puppytrainermasterclass/css/fluidable.css\" rel=\"stylesheet\"><link href=\"" .$CFG->wwwroot."/puppytrainermasterclass/css/fluidable-min.css\" rel=\"stylesheet\"><link href=\"" .$CFG->wwwroot."/puppytrainermasterclass/css/fluidable.less\" rel=\"stylesheet\"><link href=\"" .$CFG->wwwroot."/puppytrainermasterclass/css/fluidable.scss\" rel=\"stylesheet\"><link href=\"" .$CFG->wwwroot."/local/business/css/customstyle.css\" rel=\"stylesheet\">";
echo $OUTPUT->header();
?>
<style>

@media (min-width: 1199px) {
#page-content {
	padding: 0 40px;
	box-sizing: border-box;
}
}
#page-content p, #page-content li {
	font-size: 17px;
}
#page-content ul {
	list-style: none; /* Remove default bullets */
}
#page-content ul li::before {
	content: "\25A0";  /* Add content: \2022 is the CSS Code/unicode for a bullet */
	color: #2caae1; /* Change the color */
	font-weight: bold; /* If you want it to be bold */
	display: inline-block; /* Needed to add space between the bullet and the text */
	width: 1em;
	margin-left: -.8em;
	font-size: 1.6em;
}
.block1Blue a, .block1Blue span, .block2DrkBlue a, .block1Green a {
	color: orange !important;
	font-weight: normal;
    font-size: 17px;
}

.block1Blue {
	margin-top: 0px;
	padding: 0px;
}

.block1Blue .puppyChew {
	background-color: white;
	padding: 25px 25px 0 25px;
}

@media (min-width: 768px) {
}
tr.active > td, tr.deactive > td, tr.locked-deactive > td, tr.locked > td, tr.complete > td {
	line-height:17px;
	padding-bottom: 10px;
}
tr.active > td {
	background-color: transparent;
}

@media (max-width: 1024px) {
tr.active > td, tr.deactive > td, tr.locked-deactive > td, tr.locked > td {
	font-size: 17px;
}
}
.block1Blue .active, .block1Blue .complete {
	color: orange;
}

.stageHeader {
	margin-top: -19%;
	margin-left: -5%;
	max-width: 100%;
}
.stageHeaderHolder {
	margin: 0 !important;
}
.stageHeaderHolder h2{
  color:#555;
  text-transform: capitalize;
}
@media (max-width: 767px) {
.stageHeader {
	margin-top: 0;
	margin-left: 0;
}
.stageHeaderHolder {
	margin: 0 !important;
}
.block1Blue, .block2DrkBlue, .block1Green {
	margin-top: 25px;
}
}
.noscroll {
	overflow: hidden;
}
    .introtext {
        font-size: 24px;
    }
</style>

<!--  ****** TEMPLATE BANNER IMAGE/FULL WIDTH IMAGE ****** -->
<?php 
  if($banner = $DB->get_record_sql("SELECT * from {files} where component=:component and filearea=:filearea and itemid=:itemid and filesize>0", array("component"=>"businessfront","filearea"=>"bannerimage","itemid"=>$dataval->id))){
    //ADD DYNAMIC BANNER URL FORM ADMIN SECTION
      echo '<div class="row marginbottom-30">
    <div class="span12"> <img src="'.$CFG->wwwroot.'/custom_report/file.php?id='.$banner->pathnamehash.'" class="img-responsive img-fluid" alt=""> </div>
  </div>
    ';
  }
?>

<!--  ****** TEMPLATE TXT FULLWIDTH WITH ****** -->
<div class="row marginbottom-30"> 
  <!-- TXT  -->
  <div class="span12"> 
    <!-- HEADLINE -->
    
    <div class="introtext"><?php echo $dataval->introductiontext;   ?></div>
  </div>
</div>

<!-- 3 blocks -->
<style>
@media (min-width: 768px){
    .infoBlocks3 {display: flex;align-content: flex-end;}
    .infoBlock3Last {align-self: end;}
    .infoBlockCenterA {align-items: center;display: flex;}
    .height100 {
        height: 100%;
    }
    }
</style>
<div class="row marginbottom-30 infoBlocks3">
	<?php
		for ($i=0; $i < 3; $i++) { 
			$sdata = $settingdata[$i];
			if(empty($sdata->heading)){ continue; }
			echo '
				<div class="span4 block1Blue">
			    <div class="puppyChew">
			      <div class="row marginbottom-30 height100">
			        <div class="span12">
			        	<div class="row stageHeaderHolder"><h2>'.$sdata->heading.'</h2> </div>
			          <table>';
			          $linknames = $sdata->linkname;
            		$linkurls = $sdata->url;
			          for ($j=0; $j < 10; $j++) { 
			          	if(empty($linknames[$j])){continue;}
			          	$nolink = false;
			          	if(empty($linkurls[$j])){$nolink = true;}
			          	$link = $linkurls[$j];
			          	if(strpos($linkurls[$j], "http") != 0){
			          		$link = $CFG->wwwroot.$linkurls[$j];
			          	}
			            echo '<tr class="active">
			              <td style="padding-right:10px;vertical-align: top;"><i class="fa fa-caret-right" aria-hidden="true"></i></td>
			              <td><span></span>'.(($nolink?"<span>":"<a href=".$link.">")).$linknames[$j]."<br>".($nolink?"</span>":"</a>").'</td>
			              </tr>';
			          }
          echo '</table>
			        </div>
			      </div>
			    </div>
			  </div>
			';
		}
	?>
</div>
<?php
echo $OUTPUT->footer();