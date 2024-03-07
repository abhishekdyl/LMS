<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Add page to admin menu.
 *
 * @package    local_question_bank
 * @author Andreas Grabs <moodle@grabs-edv.de>
 * @copyright  Andreas Grabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $pluginname = get_string('pluginname', 'local_question_bank');

    $settings = new admin_settingpage('local_qbsettings', $pluginname);
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_heading('local_question_bank/createQuestion','Question create',''));
    $settings->add(new admin_setting_configtext('local_question_bank/createQuestionSubject', 'subject','','New question is added by author'));
    $settings->add(new admin_setting_configtext('local_question_bank/createQuestionContent', "Content",'','The author added new question, here you can access'));
    
    $settings->add(new admin_setting_heading('local_question_bank/commentQuestion','Question comment',''));
    $settings->add(new admin_setting_configtext('local_question_bank/commentQuestionSubject', 'subject','','Comments'));
    $settings->add(new admin_setting_configtext('local_question_bank/commentQuestionContent', "Content",'','Here user added new comment on this question'));
    
    $settings->add(new admin_setting_heading('local_question_bank/reviewAgainQuestion','Question review again',''));
    $settings->add(new admin_setting_configtext('local_question_bank/reviewAgainQuestionSubject', 'subject','','Review again this question'));
    $settings->add(new admin_setting_configtext('local_question_bank/reviewAgainQuestionContent', "Content",'','Review this question and sent valuable feedback'));
    
    $settings->add(new admin_setting_heading('local_question_bank/rejectQuestion','Reject Question by reviewer ',''));
    $settings->add(new admin_setting_configtext('local_question_bank/rejectQuestionSubject', 'subject','','Reviewer feedback on question'));
    $settings->add(new admin_setting_configtext('local_question_bank/rejectQuestionContent', "Content",'','Your Question commented by the reviewer please check and update'));
    
    $settings->add(new admin_setting_heading('local_question_bank/reviewedQuestion','Question reviewed',''));
    $settings->add(new admin_setting_configtext('local_question_bank/reviewedQuestionSubject', 'subject','','Reviewed by reviewer'));
    $settings->add(new admin_setting_configtext('local_question_bank/reviewedQuestionContent', "Content",'','This question is reviewed by reviewer'));

}