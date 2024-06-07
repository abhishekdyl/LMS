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
 * Dashboard widget class contains the layout information and generate the data for widget.
 *
 * @package    block_dash
 * @copyright  2022 bdecent gmbh <https://bdecent.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_dash\local\widget\dashboard;

use block_dash\local\widget\abstract_widget;
use context_block;
use moodle_url;

/**
 * Dashboard widget contains list of available dashboard.
 */
class dashboard_widget extends abstract_widget {

    /**
     * Get the name of widget.
     *
     * @return void
     */
    public function get_name() {
        return get_string('widget:dashboard', 'block_dash');
    }

    /**
     * Check the widget support uses the query method to build the widget.
     *
     * @return bool
     */
    public function supports_query() {
        return false;
    }

    /**
     * Layout class widget will use to render the widget content.
     *
     * @return \abstract_layout
     */
    public function layout() {
        return new dashboard_layout($this);
    }

    /**
     * Pre defined preferences that widget uses.
     *
     * @return array
     */
    public function widget_preferences() {
        $preferences = [
            'datasource' => 'dashboard',
            'layout' => 'dashboard',
        ];
        return $preferences;
    }

    /**
     * Build widget data and send to layout thene the layout will render the widget.
     *
     * @return void
     */
    public function build_widget() {
        global $USER, $DB,$CFG, $PAGE;
        static $jsincluded = false;
        $userid = $USER->id;
        
        $contextid = $this->get_block_instance()->context->id;
        $this->data = [];

        $this->include_suggest_dashboard();

        if (!$jsincluded) {
            $PAGE->requires->jquery();
            $PAGE->requires->js('/blocks/dash/classes/local/widget/dashboard/dashboard_data.js');
            // console.log('aaaaaaaaa1');
            // $PAGE->requires->js_call_amd('block_dash/dashboard', 'init', ['contextid' => $contextid]);
            // $jsincluded = true;
        }

        return $this->data;
    }

    /**
     * Get user picture url for contact.
     *
     * @param stdclass $userid
     * @param string $suggestiontext
     * @return stdclass
     */
    public function get_user_data($userid, $suggestiontext) {
        global $PAGE, $OUTPUT;
        $user = \core_user::get_user($userid);
        $user->fullname = fullname($user);
        $user->suggestinfo[] = $suggestiontext;
        if (isset($user->picture) && $user->picture == 0) {
            $user->profiletext = ucwords($user->fullname)[0];
        }
        $userpicture = new \user_picture($user);
        $userpicture->size = 1; // Size f1.
        $user->profileimageurl = $userpicture->get_url($PAGE)->out(false);
        $user->addcontacticon = $icon = $OUTPUT->pix_icon('t/addcontact', get_string('addtodashboard', 'block_dash'), 'moodle',
        ['class' => 'drag-handle']);
        return $user;
    }

    /**
     * Include suggest dashboard.
     *
     * @return array
     */
    public function include_suggest_dashboard() {
        global $USER, $DB, $CFG;
        require_once($CFG->dirroot. '/cohort/lib.php');
        $userid = $USER->id;
        
        $this->data['suggestions'] = array();
        $this->data['currentuser'] = $userid;
    }
}
