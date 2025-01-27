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
 * This file contains the moodle hooks for the comments feedback plugin
 *
 * @package   assignfeedback_adaptive
 * @copyright 2018 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_adaptive\external;

defined('MOODLE_INTERNAL') || die();
global $CFG;

use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;
use context_system;
use context_user;
use core_user;
use external_warnings;
use core_user_external;
use stdClass;
use \assignfeedback_adaptive\assign_comments;

class assign_feedback extends external_api {

    public static function insert_assignfeedback_is_allowed_from_ajax() {
        return true;
    }

    public static function insert_assignfeedback_returns() {
        return new external_value(PARAM_RAW, 'Add feedback');
    }

    public static function insert_assignfeedback_parameters() {
        return new external_function_parameters(
            array(
                'feedback' => new external_value(PARAM_RAW, '', false),
                'type' => new external_value(PARAM_INT, 0, false),
                'asgid' => new external_value(PARAM_INT, 0, false)              
            )
        );
    }

    public static function insert_assignfeedback($feedback, $type, $asgid) {        
        $params = self::validate_parameters(
            self::insert_assignfeedback_parameters(),
            array(
                'feedback' => $feedback,
                'type' => $type,
                'asgid' => $asgid
            )
        );

        return json_encode(assign_comments::insert_assign_feedback($params));
    }
    public static function delete_assignfeedback_is_allowed_from_ajax() {
        return true;
    }

    public static function delete_assignfeedback_returns() {
        return new external_value(PARAM_RAW, 'Delete feedback');
    }

    public static function delete_assignfeedback_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 0, false)          
            )
        );
    }

    public static function delete_assignfeedback($id) {        
        $params = self::validate_parameters(
            self::delete_assignfeedback_parameters(),
            array(
                'id' => $id
            )
        );

        return json_encode(assign_comments::delete_assign_feedback($params));
    }

    public static function update_assignfeedback_is_allowed_from_ajax() {
        return true;
    }

    public static function update_assignfeedback_returns() {
        return new external_value(PARAM_RAW, 'Delete feedback');
    }

    public static function update_assignfeedback_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 0, false),       
                'feedback' => new external_value(PARAM_RAW, '', false)         
            )
        );
    }

    public static function update_assignfeedback($id, $feedback) {        
        $params = self::validate_parameters(
            self::update_assignfeedback_parameters(),
            array(
                'id' => $id,
                'feedback' => $feedback
            )
        );

        return json_encode(assign_comments::edit_assign_feedback($params));
    }
   
}
