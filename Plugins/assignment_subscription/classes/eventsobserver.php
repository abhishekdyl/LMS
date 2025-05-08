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
 * Local plugin "Boost navigation fumbling" - Event observers
 *
 * @package    local_assignment_subscription
 * @copyright  2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// namespace local_assignment_subscription;

defined('MOODLE_INTERNAL') || die();

/**
 * Observer class containing methods monitoring various events.
 *
 * @package    local_assignment_subscription
 * @copyright  2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class local_assignment_subscription_eventsobserver
{

    /**
     * User loggedin event observer.
     *
     * @param \core\event\base $event The event.
     */

    public static function assessable_submitted(\mod_assign\event\assessable_submitted $event) 
    {
        
        global $USER, $DB;

        $current_date = strtotime(date("d F Y H:i:s"));

            $newdata = new stdClass();
            $newdata->submissionid = $event->objectid;
            $newdata->userid = $event->userid;
            $ischk = $DB->record_exists_sql("SELECT * FROM {assign_subs_status} WHERE userid=".$event->userid." AND submissionid=".$event->objectid."");
        
        if(empty($ischk)){

            $ispriority = $DB->record_exists_sql("SELECT * FROM {assign_subs_users} WHERE userid=".$event->userid." AND end_date>=".$current_date." AND status=1");
           

            if(!empty($ispriority)){ 
                $ispriority_chk = 1; 
            }else{ 
                $ispriority_chk = 0; 
            }

            $newdata->ispriority = $ispriority_chk;
            $DB -> insert_record('assign_subs_status', $newdata, false);

        }else{}
       
        // die();

    }

}
