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
 * Post-install code for the feedback_comments module.
 *
 * @package   assignfeedback_adaptive
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Set the initial order for the feedback comments plugin (top)
 * @return bool
 */
function xmldb_assignfeedback_adaptive_install() {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/mod/assign/adminlib.php');

    // Set the correct initial order for the plugins.
    $pluginmanager = new assign_plugin_manager('assignfeedback');
    $pluginmanager->move_plugin('commentsfeedback', 'up');
    $allassigns = $DB->get_records_sql("SELECT id FROM {assign}");    
    foreach ($allassigns as $allassig) {
        $assign_fdbk = new stdClass;
        $assign_fdbk->assignment = $allassig->id;
        $assign_fdbk->plugin = 'adaptive';
        $assign_fdbk->subtype = 'assignfeedback';
        $assign_fdbk->name = 'enabled';
        $assign_fdbk->value = '1';
        $DB->insert_record('assign_plugin_config', $assign_fdbk);
    }

    $dbman = $DB->get_manager();
    $table = new xmldb_table('assignfeedback_adaptive');
    if ($dbman->table_exists($table)) {
        $assigns = $DB->get_records_sql("SELECT * FROM {assignfeedback_comments}");
        foreach ($assigns as $assign) {
            $tenantid = $DB->insert_record('assignfeedback_adaptive', $assign);
        }
        /*$defaulttenancy = new stdClass();
        $defaulttenancy->name = 'Default tenancy';
        $defaulttenancy->archived = 0;
        $defaulttenancy->isdefault = 1;
        $defaulttenancy->categoryid = 0;
        $defaulttenancy->byid = 0;
        $defaulttenancy->byidnumber = 0;
        $defaulttenancy->deleted = 0;
        $defaulttenancy->timecreated = time();*/
        
        // Users add in default tenancy.
    }
    return true;
}
