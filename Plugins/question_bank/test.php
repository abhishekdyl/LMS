<?php
require_once('../../config.php');
global $DB, $CFG, $USER, $OUTPUT;
// $url = new moodle_url();
$wpurl = get_config('local_question_bank','commentQuestionSubject');
$wpurl1 = get_config('local_question_bank','reviewAgainQuestionContent');
// if (!$category = $DB->get_record('question_categories', array('id' => 2))) {
//     throw new moodle_exception('categorydoesnotexist', 'question', $returnurl);
// }
// $categorycontext = context::instance_by_id($category->contextid);
echo '<pre>';
print_r($wpurl);
print_r($wpurl1);
// print_r($categorycontext);
echo '</pre>';

die;
require_login();
$html = '
<form action="question.php" method="GET">
    <fieldset>
        <legend class="moduletypetitle">Questions</legend>
        <div class="option" >
            <label for="item_qtype_multichoice">
                <input type="radio" name="qtype" id="item_qtype_multichoice" value="multichoice" data-initial-value="multichoice">
                <span class="modicon">
                        <img class="icon icon" title="Multiple choice" alt="Multiple choice" src="http://122.176.28.104/sushiltest/theme/image.php/boost/qtype_multichoice/1705999415/icon">                                            </span>
                <span class="typename">Multiple choice</span>
            </label>
            <div class="typesummary">
                <p>Allows the selection of a single or multiple responses from a pre-defined list.</p>
            </div>
        </div>
        <div class="option">
            <label for="item_qtype_truefalse">
                <input type="radio" name="qtype" id="item_qtype_truefalse" value="truefalse">
                <span class="modicon">
                        <img class="icon icon" title="True/False" alt="True/False" src="http://122.176.28.104/sushiltest/theme/image.php/boost/qtype_truefalse/1705999415/icon">                                            </span>
                <span class="typename">True/False</span>
            </label>
            <div class="typesummary">
                <p>A simple form of multiple choice question with just the two choices "True" and "False".</p>
            </div>
        </div>
    </fieldset>
    <input type="hidden" name="courseid" value=1>
    <input type="hidden" name="category" value=1>
    <button type="submit" >Save Change</button>
    <button type="cancel" >Cancel</button>
</form>';

echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();
?>