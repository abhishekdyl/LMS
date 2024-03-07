<?php
// This file is part of Moodle - https://moodle.org/
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
 * Adds admin settings for the plugin.
 *
 * @package     local_manage_course
 * @category    admin
 * @copyright   2020 Your Name <email@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settingspage = new admin_settingpage('local_manage_course', new lang_string('pluginname', 'local_manage_course'));
    $ADMIN->add('localplugins', $settingspage);

    $name = new lang_string('approve', 'local_manage_course');
    $description = new lang_string('approve_desc', 'local_manage_course');
    $settingspage->add(new admin_setting_configcheckbox('local_manage_course/approved', $name,$description,'',1));

     if ($ADMIN->fulltree) {
        $name = new lang_string('wpurl', 'local_manage_course');
        $description = new lang_string('wpurl_desc', 'local_manage_course');
        $settingspage->add(new admin_setting_configtext('local_manage_course/wpurl', $name,$description,'',PARAM_TEXT));
    }

//    $ADMIN->add('localplugins', $settingspage);
}