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
 * Plugin settings for the local_[pluginname] plugin.
 *
 * @package   local_sso
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Ensure the configurations for this site are set
if ($hassiteconfig) {

    // Create the new settings page
    // - in a local plugin this is not defined as standard, so normal $settings->methods will throw an error as
    // $settings will be null
    $settings = new admin_settingpage('local_sso', 'SSO Setting');

    // Create
    $ADMIN->add('localplugins', $settings);
     $settings->add(new admin_setting_configtext(
        // This is the reference you will use to your configuration
        'local_sso/auth_api_root',

        // This is the friendly title for the config, which will be displayed
        'Authentication API Root',

        // This is helper text for this config field
        '',

        // This is the default value
        '',

        // This is the type of Parameter this config is
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        // This is the reference you will use to your configuration
        'local_sso/payment_api_root',

        // This is the friendly title for the config, which will be displayed
        'Payment API Root',

        // This is helper text for this config field
        '',

        // This is the default value
        '',

        // This is the type of Parameter this config is
        PARAM_TEXT
    ));
    // Add a setting field to the settings for this page
    $settings->add(new admin_setting_configtext(
        // This is the reference you will use to your configuration
        'local_sso/payment_home_url',

        // This is the friendly title for the config, which will be displayed
        'Payment Home URL',

        // This is helper text for this config field
        '',

        // This is the default value
        '',

        // This is the type of Parameter this config is
        PARAM_TEXT
    ));
    // Add a setting field to the settings for this page
    $settings->add(new admin_setting_configtext(
        // This is the reference you will use to your configuration
        'local_sso/username',

        // This is the friendly title for the config, which will be displayed
        'Username',

        // This is helper text for this config field
        'User name for calling sso api',

        // This is the default value
        '',

        // This is the type of Parameter this config is
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configpasswordunmask(
        // This is the reference you will use to your configuration
        'local_sso/userpassword',

        // This is the friendly title for the config, which will be displayed
        'Password',

        // This is helper text for this config field
        'User password for calling sso api',

        // This is the default value
        '',

        // This is the type of Parameter this config is
        PARAM_TEXT
    )); 
    $settings->add(new admin_setting_configtext(
        // This is the reference you will use to your configuration
        'local_sso/editprofileurl',

        // This is the friendly title for the config, which will be displayed
        'Profile Url',

        // This is helper text for this config field
        '',

        // This is the default value
        '',

        // This is the type of Parameter this config is
        PARAM_URL
    ));

     $settings->add(new admin_setting_configtext(
        // This is the reference you will use to your configuration
        'local_sso/utrainslogouturl',

        // This is the friendly title for the config, which will be displayed
        'Logout Url',

        // This is helper text for this config field
        '',

        // This is the default value
        '',

        // This is the type of Parameter this config is
        PARAM_URL
    ));
    $settings->add(new admin_setting_configtext(
        // This is the reference you will use to your configuration
        'local_sso/hubspot_base_url',

        // This is the friendly title for the config, which will be displayed
        'Hubspot API',

        // This is helper text for this config field
        '',

        // This is the default value
        '',

        // This is the type of Parameter this config is
        PARAM_URL
    ));
    $settings->add(new admin_setting_configtext(
        // This is the reference you will use to your configuration
        'local_sso/hubspot_token',

        // This is the friendly title for the config, which will be displayed
        'Hubspot API Token',

        // This is helper text for this config field
        '',

        // This is the default value
        '',

        // This is the type of Parameter this config is
        PARAM_URL
    ));
}