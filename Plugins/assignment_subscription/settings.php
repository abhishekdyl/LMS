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
 * @package    local_assignment_review
 * @author Andreas Grabs <moodle@grabs-edv.de>
 * @copyright  Andreas Grabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

$pluginname = get_string('pluginname', 'local_assignment_subscription');

$settings = new admin_settingpage('local_assignment_subscription_settings', $pluginname);
$ADMIN->add('localplugins', $settings);

$configs = array();

// Heading of Setting Page
$configs[] = new admin_setting_heading('local_assignment_subscription', get_string('stripe_key_lang', 'local_assignment_subscription'),'');

// publishableKey
$configs[] = new admin_setting_configpasswordunmask('publishableKey', get_string('publishableKey_lang', 'local_assignment_subscription'),'',0);

// secretKey
$configs[] = new admin_setting_configpasswordunmask('secretKey', get_string('secretKey_lang', 'local_assignment_subscription'),'',0);

// General target
$configs[] = new admin_setting_configtext('target_general', get_string('general_target', 'local_assignment_subscription'),'',0);

// Priority target
$configs[] = new admin_setting_configtext('target_priority', get_string('priority_target', 'local_assignment_subscription'),'',0);



$configs[] = new admin_setting_description('description', "Additional Setting",'<p><a class="btn btn-secondary" href="'.$CFG->wwwroot.'/local/assignment_subscription/home.php">Setting Page</a><br></p>');



// Put all settings into the settings page.
foreach ($configs as $config) {
    $config->plugin = 'local_assignment_subscription';
    $settings->add($config);
}
}



?>


