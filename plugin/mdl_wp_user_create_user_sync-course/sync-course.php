<?php

/**
  Plugin Name: One Data Room
  Description: with help of this plugin you can implement members dashboard
  Version: 1.0.0
  Author: Suneet Sharma
  Author URI: https://ldsengineers.com

 * */

add_action('admin_menu', 'wp_one_data_room');
function wp_one_data_room(){
    add_menu_page('One Data Room', 'Client Dashboard', 'manage_options', 'client', 'clientdashboard' );
    add_submenu_page('client', 'Member Dashboard', 'Member Dashboard', 'manage_options', 'member','memberdashboard');
}

require_once("functions.php");


function clientdashboard(){
  global $wpdb;
 }
 
 function memberdashboard(){
  global $wpdb;
 }

?>
