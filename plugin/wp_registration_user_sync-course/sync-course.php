<?php

/**
  Plugin Name: Wp Moodle Sync 
  Description: Sync Wordpress And Moodle information
  Version: 1.0.0
  Author: Suneet Sharma
  Author URI: https://ldsengineers.com

 * */
//define('WP_DEBUG', true);


add_action('admin_menu', 'wp_moodle_sync');
function wp_moodle_sync(){
    add_menu_page('WP Moodle Sync', 'WP Moodle Sync', 'manage_options', 'wpmoodlesync', 'moodleSettings' );
    add_submenu_page('wpmoodlesync', 'Couse Mapping', 'Couse Mapping', 'manage_options', 'mapping','couse_mapping');
}
require_once("functions.php");


function moodleSettings(){
  global $wpdb;
  if(isset($_POST['submit'])){
    global $wpdb;
    $url   =  $_POST['url'];
    $table_name = $wpdb->prefix . "moodle_settings";
    $moodleurl = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "moodle_settings"); 
    if(empty($moodleurl)){
      $add_moodleurl= array(
        'url'=>$url, 
        'createddate'=>time() 
      );
      $wpdb->insert($table_name,$add_moodleurl);
    }else{
      $wpdb->update($table_name,
        array(
          'url'=>$url,
          'updateddate'=>time()
        ),
        array(
          'id'=>$moodleurl->id
        )
      );
    }
  }
  $moodle_url = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "moodle_settings");
?>

  <style type="text/css">
    .mt-3{
      margin-top: 25px;
    }

    .submit_btn{
      text-align: right;
      margin-top: 25px;
    }
    .submit_btn input{
      min-width: 150px;
      padding: 5px;
      font-size: 20px;
    }

    .panel-heading{
      background-color: #ffffff !important;
      color: #333333 !important;
      font-size: 20px !important;
    }

    .selectors{
      min-width: 100% !important;
      padding: 5px !important;
    }
  </style>

  <h1>Moodle Settings</h1>
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!--<link href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" rel="stylesheet">-->

<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#moodle-base-url">Moodle Base Url</a></li>
</ul>

<div class="tab-content">
  <div id="moodle-base-url" class="tab-pane fade in active">
    <div class="row">
      <div class="col-12 col-lg-6 col-md-8 mt-3">
          <div class="panel panel-default">
            <div class="panel-heading">Set Moodle base url</div>
                <div class="panel-body">
                  <form method="POST" id="moodle-url-form" enctype="multipart/form-data"> 
                    <div>
                      
                      <input type="text" name="url" class="form-control" placeholder="Base url" value="<?php if(!empty($moodle_url)){ echo $moodle_url->url; }?>">
                    </div>
                    <div class="submit_btn">
                      <input type="submit" name="submit" value="Submit" class="btn btn-info">
                    </div>
                    <input type="hidden" name="set_url" value="set_url">
                  </form>
                </div>
            </div>
          </div>
      </div>
  </div>
</div>
  <?php
}
//function create table 
function CreateTable_wootomoodle1()
{
  global $wpdb;
  $faqtable = $wpdb->prefix . 'moodle_settings';

  // create table 1
  if($wpdb->get_var("show tables like '$faqtable'") != $faqtable)
  {
    $sql = "CREATE TABLE IF NOT EXISTS " . $faqtable . " (
      `id` int(9) NOT NULL AUTO_INCREMENT, 
      `url` varchar(200) NULL,
      `createddate` int(20) NULL,
      `updateddate` bigint NULL,
      PRIMARY KEY id (id) 
      );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

}

//activation
register_activation_hook( __FILE__, 'CreateTable_wootomoodle1');