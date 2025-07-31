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
 * @package    local
 * @subpackage business
 * @copyright  2021 Devlion.co
 * @author  Evgeniy Voevodin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_business\task;

defined('MOODLE_INTERNAL') || die();

/**
 * The local_business cache task class.
 *
 * @package    local_business
 * @copyright  2021 Devlion <info@devlion.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion extends \core\task\scheduled_task {

     /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return "New completion cron";
    }

    /**
     * Execute scheduled task
     *
     * @return boolean
     */
    public function execute() {

        global $CFG, $DB;
        require_once($CFG->libdir . "/completionlib.php");

        $records = \local_business\completion::get_records(['enabled' => \local_business\completion::COMPLETION_TRACKING_AUTOMATIC]);
        echo "<pre>";
        print_r($records); 
        echo"</pre>";
        die;
    }
}
