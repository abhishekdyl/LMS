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
defined('MOODLE_INTERNAL') || die();



$functions = array(  
    'mod_assign_insertfeedback' => array(
        'classname'   => 'assignfeedback_adaptive\external\assign_feedback',
        'methodname'  => 'insert_assignfeedback',
        'classpath'   => 'mod/assign/feedback/adaptive/classes/external/assign_feedback.php',
        'description' => 'Insert assignment feedback',
        'type'        => 'write',
        'ajax'        => true
    ),'mod_assign_deletefeedback' => array(
        'classname'   => 'assignfeedback_adaptive\external\assign_feedback',
        'methodname'  => 'delete_assignfeedback',
        'classpath'   => 'mod/assign/feedback/adaptive/classes/external/assign_feedback.php',
        'description' => 'Delete assignment feedback',
        'type'        => 'write',
        'ajax'        => true
    ),'mod_assign_updatefeedback' => array(
        'classname'   => 'assignfeedback_adaptive\external\assign_feedback',
        'methodname'  => 'update_assignfeedback',
        'classpath'   => 'mod/assign/feedback/adaptive/classes/external/assign_feedback.php',
        'description' => 'Update assignment feedback',
        'type'        => 'write',
        'ajax'        => true
    ),
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.

$services = array(
    'assignfeedback_adaptive' => array(
        'functions' => array(            
            'mod_assign_insertfeedback',
            'mod_assign_deletefeedback',
            'mod_assign_updatefeedback'
        ),
        'restrictedusers' => 0,
        'enabled'=>1
    )
);



