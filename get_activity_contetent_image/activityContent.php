<?php
require_once('../../config.php');
global $CFG,$USER,$DB,$PAGE,$OUTPUT;
$id = required_param('cmid', PARAM_INT); // Course module id
// $id = optional_param('cmid', PARAM_INT); // Course module id

echo $OUTPUT->header();
$sqql = "SELECT q.id as question_id, q.name, q.questiontext, q.questiontextformat, qct.contextid
FROM mdl_course_modules cm
JOIN mdl_modules m ON m.id = cm.module AND m.name = 'quiz'
JOIN mdl_quiz quiz ON quiz.id = cm.instance
JOIN mdl_quiz_slots qs ON qs.quizid = quiz.id
JOIN mdl_question_references qr ON qr.itemid = qs.id
JOIN mdl_question_bank_entries qbe ON qbe.id = qr.questionbankentryid
JOIN mdl_question_categories qct ON qct.id = qbe.questioncategoryid
JOIN mdl_question_versions qv ON qv.questionbankentryid = qbe.id AND qv.version = '1'
JOIN mdl_question q ON q.id = qv.questionid
WHERE cm.id = $id";
$qdatas = $DB->get_records_sql($sqql);

$html = '
<style>
* {
  margin: 0%;
  padding: 0%;
  font-family: Raleway;
  box-sizing: border-box;
  scroll-behavior: smooth;
}

.header {
  background: linear-gradient(70deg, purple, #ff5600);
}
.container {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}
.slider {
    display: flex;
    max-width: 550px;
    overflow-x: hidden;
    justify-content: space-between;
    user-select: none;
    cursor: default;
}

.slider::-webkit-scrollbar {
  width: 0px;
  height: 0px;
}
.testimonal {
  width: 100%;
  display: flex;
  background: rgba(0, 0, 0, 0.5);
  padding: 30px;
  align-items: center;
  justify-content: space-around;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
}
.twitter {
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #02b1b1;
}
.twitter i {
  font-size: 20px;
}
.twitter small {
  transform: translateY(-3px);
}
.user {
  width: 100%;
  height: 210px;
  object-fit: cover;
}
.img {
  width: 100%;
 /* display: flex;*/
  align-items: center;
  justify-content: space-around;
}
.user-text {
  min-width: 500px;
  color: #fff;
  display: flex;
  flex-direction: column;
}
.slider-btn {
  background: rgba(0, 0, 0, 0.5);
  width: 550px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom-left-radius: 5px;
  border-bottom-right-radius: 5px;
}
.dot {
  cursor: pointer;
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
  margin-bottom: 13px;
}

.dot:hover {
  background-color: #717171;
}
.ansclass small{
    padding: 10px 20px;
    margin-right: 10px;
    border-radius: 5px;
    background: #1c77c3;
}

</style>';

function convertImageTagsToImg($content) {
    return preg_replace_callback(
        '/\[image\](.*?)\[\/image\]/i',
        function ($matches) {
            $url = trim($matches[1]);
            return '<img src="' . htmlspecialchars($url, ENT_QUOTES) . '" />';
        },
        $content
    );
}
	$html .='<div class="container">
	    <div class="slider">';
			foreach ($qdatas as $key => $qdata) {
				$options = [];
			   $questiontextarea = file_rewrite_pluginfile_urls($qdata->questiontext, "local/coursesync/file.php", $qdata->contextid, "question", "questiontext", $qdata->question_id, $options);
         $questiontextarea = convertImageTagsToImg($questiontextarea);
				$qanswers = $DB->get_records('question_answers',array('question'=>$qdata->question_id));

		      $html .='<div class="testimonal" id="testimonal-'.$key.'">
		        <div class="user-text">
		          <h4 class="text-white">'.$qdata->name.'</h4>
		          	<div class="img">
			            '. $questiontextarea .'
			          </div>
		          <div class="ansclass">
		          	<p>Select one : </p><span>';
					foreach ($qanswers as $ans) {
		          		$html .='<small>'.preg_replace("/<\/?p[^>]*>/", "", $ans->answer).'</small>';
		          	}
		  $html .='</span></div>
		        </div>
		      </div>';
	        }
$html .='</div>
		<div class="slider-btn">';
			foreach ($qdatas as $key => $qdata) {
		      $html .='<a href="#testimonal-'.$key.'"><span class="dot"></span></a>';
		    }
$html .='</div>
	</div>';
echo $html;

echo $OUTPUT->footer();


?>