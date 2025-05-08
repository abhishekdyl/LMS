<?php
require_once('../../config.php');
global $CFG, $DB, $PAGE, $USER, $COMPLETION_CRITERIA_TYPES;
require_login();
$id=optional_param('id',0,PARAM_INT);
$userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1));
if(empty($userbrand)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
$dataval = $DB->get_record('business_learning_program',array("id"=>$id,"cbid"=>$userbrand->cbid));
if(empty($dataval)){
  redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
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
.regir_btn {
	padding-left: 65px;
	padding-right: 65px;
	font-size: 20px;
	min-height: 50px;
	min-width: 200px;
}
/*ENROL BUTTON*/
.regir_btn {
	padding-left: 65px;
	padding-right: 65px;
	font-size: 20px;
	min-height: 70px;
	min-width: 200px;
	background-color: #f19913;
	-border: 3px solid rgb(0, 122, 196);
	margin: 15px 20px;
	text-transform: uppercase;
	text-align: center;/*text-decoration: underline;*/
}
/*.regir_btn::before {content: "Click here to ";}*/
.regir_btn {
	animation: pulse-animation 2s infinite;
}
@keyframes pulse-animation {
0% {
box-shadow: 0 0 0 0px rgba(0, 122, 196, 0.2);
}
100% {
box-shadow: 0 0 0 20px rgba(0, 122, 196, 0);
}
}

@media (max-width: 767px) {
.hideMobile {
	display: none !important;
}
}
/*anil css*/
.infoBlocks3 {
	padding-top: 50px;
}
.block1Blue, .block2DrkBlue, .block1Green {
	-webkit-border-radius: 30px;
	-moz-border-radius: 30px;
	border-radius: 30px;
	padding: 25px;
	color: #fff;
	overflow:hidden;
	background-color:#156495;
}
.block1Blue a, .block2DrkBlue a, .block1Green a {
	color: #fff !important;
	font-weight: normal;
}
.block1Blue span, .block2DrkBlue span, .block1Green span {
}
.block1Blue {
	margin-top: 50px;
	padding: 0px;
}
/*.block1Blue::after {
	content: "Sponsored by Pawly Understood";
	display: block;
	color: gray;
	text-align: right;
	padding-right: 5px;
}*/
.block1Blue .puppyChew {
	background-color: rgba(0,0,0,.3);
	padding: 25px 25px 0 25px;
}

@media (min-width: 768px) {
.block1Blue .puppyChew {
	background-image: unset;
	background-size: contain;
	background-position: right bottom;
	background-repeat: no-repeat;
	padding: 25px 25px 0 25px;
	height: 100%;
	box-sizing: border-box;
    padding-bottom: 180px;
}
}
.block2DrkBlue, .block1Green {
	
	padding: 0px;
}
/*.block2DrkBlue::after {
	content: "Sponsored by NexGard Spectra";
	display: block;
	color: gray;
	text-align: right;
	padding-right: 5px;
}*/
.block2DrkBlue .puppyPlay {
	background-color: rgba(0,0,0,.5);
	padding: 25px 25px 0 25px;
}

@media (min-width: 768px) {
.block2DrkBlue .puppyPlay {
	background-image: unset;
	background-size: contain;
	background-position: right bottom;
	background-repeat: no-repeat;
	padding: 25px 25px 0 25px;
	height: 100%;
	box-sizing: border-box;
}
}
.block1Green {
	margin-top: 50px;
	

}
.block1Green .puppyPlay {
	padding: 25px 25px 0 25px;
	background-color: rgba(0,0,0,.7);
}
.circle {
	width: 30px;
	height: 30px;
	background-color: #babec0;
	display: block;
	border: 4px solid #9c9fa1;
	-webkit-border-radius: 50%;
	-moz-border-radius: 50%;
	border-radius: 50%;
	margin: 5px;
}
tr.active > td, tr.deactive > td, tr.locked-deactive > td, tr.locked > td, tr.complete > td {
	font-size: 22px;
	line-height: 26px;
	padding-bottom: 10px;
}
tr.active > td {
	background-color: transparent;
	font-size: 22px;
}
tr.deactive > td {
	background-color: transparent;
	color: #babec0;
	font-size: 22px;
}
tr.locked-deactive > td {
	background-color: transparent;
	color: #babec0;
	font-size: 22px;
}
tr.locked-deactive > td span::before, tr.deactive > td span::before {
	content: 'COMING SOON';
	display: block;
	font-size: .5em;
	line-height: .7em;
}
tr.locked > td {
	background-color: transparent;
	color: #fff;
	font-weight: bold;
	font-size: 22px;
}

@media (max-width: 1024px) {
tr.active > td, tr.deactive > td, tr.locked-deactive > td, tr.locked > td {
	font-size: 17px;
}
}
.block1Blue .active, .block1Blue .complete {
	color: #fff;
}
.block1Blue .active .circle {
	border-color: #d2d1d1;
	background-color: #fff;
}
.block2DrkBlue .active .circle {
	border-color: #d2d1d1;
	background-color: #fff;
}
.block1Green .active .circle {
	border-color: #d2d1d1;
	background-color: #fff;
}
.block1Blue .complete .circle {
	border-color: #d2d1d1;
	background-color: #fff;
	background-image: url("images/complete-lightblue.png");
	background-size: cover;
}
.block2DrkBlue .complete .circle {
	border-color: #d2d1d1;
	background-color: #fff;
	background-image: url("images/complete-blue.png");
	background-size: cover;
}
.block1Green .complete .circle {
	border-color: #d2d1d1;
	background-color: #fff;
	background-image: url("images/complete-green.png");
	background-size: cover;
}
.block1Green .locked .circle, .block2DrkBlue .locked .circle, .block1Blue .locked .circle {
	border-color: #d2d1d1;
	background-color: #fff;
	background-image: url("images/locked-active.png");
	background-size: cover;
}
.block1Blue .locked-deactive .circle, .block1Green .locked-deactive .circle, .block2DrkBlue .locked-deactive .circle {
	border-color: #d2d1d1;
	background-color: #fff;
	background-image: url("images/locked-deactive.png");
	background-size: cover;
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
  color:#ffffff;
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
.block2DrkBlue .stageHeaderHolder {
	
}
.lineDiv {
	display: block;
	background-color: rgba(255,255,255,.3);
	height: 5px;
	width: 100%;
	position: relative;
	margin: 20px 0 15px 0;
}
.lineDiv::after {
	content: "";
	display: block;
	height: 0px;
	width: 0px;
	background-color: rgba(255,255,255,.3);
	position: absolute;
	right: -2px;
	top: -2px;
	-webkit-border-radius: 50%;
	-moz-border-radius: 50%;
	border-radius: 50%;
}
.meetExpectsImg {
	border: 3px solid #fff;
	-webkit-border-radius: 18px;
	-moz-border-radius: 18px;
	border-radius: 18px;
}
.meetExpectsTable {
	font-size: 12px;
	color: #fff;
	margin-top: 10px
}
.meetExpectsTable span {
	font-size: 20px;
	font-weight: bold;
	padding-bottom: 5px;
	display: block;
}
.meetExpectsTable a {
	font-size: 15px;
	text-decoration: underline;
	padding-top: 5px;
	font-style: italic;
}
.noscroll {
	overflow: hidden;
}
.introtick, .introlock, .introtickdrk {
	display: inline-block;
	width: 25px;
	height: 25px;
	-webkit-border-radius: 50%;
	-moz-border-radius: 50%;
	border-radius: 50%;
	margin: 5px;
    vertical-align: middle;
}
.introlock {
	background-image: url("../images/locked-active.png");
	background-size: cover;
    border: 4px solid #9bc033;
}
.introtick {
	background-image: url("../images/complete-lightblue.png");
	background-size: cover;
    border: 4px solid #6aabe6;
}
    .introtickdrk {
        border: 4px solid #3372b4;
	    background-color: #fff;
	    background-image: url("../images/complete-blue.png");
        background-size: cover;
    }
</style>

<!--  ****** TEMPLATE BANNER IMAGE/FULL WIDTH IMAGE ****** -->
<?php 
  if($banner = $DB->get_record_sql("SELECT * from {files} where component=:component and filearea=:filearea and itemid=:itemid and filesize>0", array("component"=>"learningprogram","filearea"=>"mainimage","itemid"=>$dataval->id))){
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
    <h1 class="p_head"><b><?php echo $dataval->program_name;   ?></b></h1>
    <h3><?php echo $dataval->introduction_message;   ?></h3>
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
	<?php  if(!empty($dataval->stream1title) && !empty($dataval->stream1courseid)){  ?>
  <div class="span4 block1Blue">
    <div class="puppyChew">
      <div class="row marginbottom-30 height100">
        <div class="span12">
        <div class="row stageHeaderHolder"><h2><?php echo $dataval->stream1title;   ?></h2> </div>

          <div class="lineDiv"></div>
          <table>
            <?php

            list($insql, $inparams) = $DB->get_in_or_equal(explode(",", $dataval->stream1courseid));
            $data = $DB->get_records_sql('SELECT cl.* FROM {course} cl WHERE cl.id '.$insql.' ', $inparams);

            $currentdate = time();
            foreach ( $data as $key => $course ) {

              $status = "deactive";
              $nolink = true;
              if ( $course->startdate > $currentdate ) {
                $status = "locked";
                if ( $course->visible == 0 ) {
                  $status = "locked-deactive";
                }
              } else {
                if ( $course->visible ) {
                  $nolink = false;
                  $status = "active";
                } else {
                  $status = "deactive";
                }
                if ( $DB->record_exists_sql( "select * from {course_completions} where userid=? and course=? and timecompleted is not null", array( $USER->id, $course->id ) ) ) {
                  $status = "complete";
                }
              }

              ?>
            <tr class="<?php echo $status; ?>">
              <td><div class="circle"></div></td>
              <td><span></span><?php echo (($nolink?"":"<a href=\"".$CFG->wwwroot."/course/view.php?id=".$course->id."\">")).$course->fullname."<br>".($nolink?"":"</a>") ; ?></td>
            </tr>
            <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php	} 
  if(!empty($dataval->stream2title) && !empty($dataval->stream2courseid)){
 ?>
  <!-- COL2 TXT LEFT holds 2 -->
  <div class="span4 block2DrkBlue">
    <div class="puppyPlay">
      <div class="row marginbottom-30 height100">
        <div class="span12">
        <div class="row stageHeaderHolder"><h2><?php echo $dataval->stream2title;   ?></h2> </div>

          <div class="lineDiv"></div>
          <table>
            <?php

            list($insql, $inparams) = $DB->get_in_or_equal(explode(",", $dataval->stream2courseid));
            $data = $DB->get_records_sql('SELECT cl.* FROM {course} cl WHERE cl.id '.$insql.' ', $inparams);
            $currentdate = time();
            foreach ( $data as $key => $course ) {
              //   echo "<pre>";
              //   print_r($course);
              //   echo "</pre>";
              $status = "deactive";
              $nolink = true;
              if ( $course->startdate > $currentdate ) {
                $status = "locked";
                if ( $course->visible == 0 ) {
                  $status = "locked-deactive";
                }
              } else {
                if ( $course->visible ) {
                  $status = "active";
                  $nolink = false;
                } else {
                  $status = "deactive";
                }
                $complete = $DB->record_exists_sql( "select * from {course_completions} where userid=? and course=? and timecompleted is not null", array( $USER->id, $course->id ) );
                if ( $complete ) {
                  $status = "complete";
                }
              }
              ?>
            <tr class="<?php echo $status; ?>">
              <td><div class="circle"></div></td>
              <td><span></span><?php echo (($nolink?"":"<a href=\"".$CFG->wwwroot."/course/view.php?id=".$course->id."\">")).$course->fullname."<br>".($nolink?"":"</a>") ; ?></td>
            </tr>
            <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php	} 
  if(!empty($dataval->stream3title) && !empty($dataval->stream3courseid)){
 ?>
  <!-- COL2 TXT Right holds 1 -->
  <div class="span4 block1Green" >
	  <div class="puppyPlay">
    <div class="row marginbottom-30 height100">
      <div class="span12" style="height: 100%;display: flex;flex-direction: column;justify-content: space-between;">
        <div>
              <div class="row stageHeaderHolder"><h2><?php echo $dataval->stream3title;   ?></h2> </div>
              <div class="lineDiv"></div>
              <table>
                <?php
                    list($insql, $inparams) = $DB->get_in_or_equal(explode(",", $dataval->stream3courseid));
                      $data = $DB->get_records_sql('SELECT cl.* FROM {course} cl WHERE cl.id '.$insql.' ', $inparams);

                    $currentdate = time();
                    foreach ( $data as $key => $course ) {
                      $nolink = true;
                      $status = "deactive";
                      if ( $course->startdate > $currentdate ) {
                        $status = "locked";
                        if ( $course->visible == 0 ) {
                          $status = "locked-deactive";
                        }
                      } else {
                        if ( $course->visible ) {
                          $status = "active";
                          $nolink = false;
                        } else {
                          $status = "deactive";
                        }
                        $complete = $DB->record_exists_sql( "select * from {course_completions} where userid=? and course=? and timecompleted is not null", array( $USER->id, $course->id ) );
                        if ( $complete ) {
                          $status = "complete";
                        }
                      }
                ?>
                <tr class="<?php echo $status; ?>">
                  <td><div class="circle"></div></td>
                  <td><span></span><?php echo (($nolink?"":"<a href=\"".$CFG->wwwroot."/course/view.php?id=".$course->id."\">")).$course->fullname."<br>".($nolink?"":"</a>") ; ?></td>
                </tr>
                <?php } ?>
              </table>
        </div>
      </div>
    </div>
	</div>
  </div>
<?php  } ?>

</div>


<?php

echo $OUTPUT->footer();
