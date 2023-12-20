<?php

/**
  Plugin Name: Wp Moodle Sync 
  Description: Sync Wordpress And Moodle information
  Version: 1.0.0
  Author: Anjum Parwej
  Author URI: https://ldsengineers.com

 * */
//define('WP_DEBUG', true);


add_action('admin_menu', 'wp_moodle_sync');
function wp_moodle_sync(){
    add_menu_page('WP Moodle Sync', 'WP Moodle Sync', 'manage_options', 'wpmoodlesync', 'moodleSettings' );
    add_submenu_page('wpmoodlesync', 'Couse Mapping', 'Couse Mapping', 'manage_options', 'mapping','couse_mapping');
    add_submenu_page('wpmoodlesync', 'Required Pages', 'Required Pages', 'manage_options', 'requiredpages','impNote' );
    add_submenu_page('wpmoodlesync', 'Course Mapped List', 'Course Mapped List', 'manage_options', 'course_mapped_list','course_mapped' );
    add_submenu_page('wpmoodlesync', 'Course Request Data', 'Course Request Data', 'manage_options', 'course_request_list','course_request_lists' );
}
require_once("functions.php");
require_once("course_mapping.php");

function my_awesome_func($data){
	return array("user"=>"anjum","age"=>17);

}


function moodleSettings(){
  global $wpdb;
  $data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
  $url='';
  $cmid='';
  $token='';
   $options='<option value="">Select Digital Litracy Course</option>';
  if($data){
    $url=$data->url;
    $cmid=$data->cmid;
    $token=$data->token;
    $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $url.'/local/coursesync/getCourseList.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET'
      ));
      $response = curl_exec($curl);

      curl_close($curl);
      $responsedata=json_decode($response);
      if($responsedata->status){
       foreach($responsedata->course as $course){
        $selected='';
        if($course->id==$data->courseid){
          $selected='selected';
          
        }
        $options .='<option value="'.$course->id.'" '.$selected.'>'.$course->fullname.'</option>';
       }

      }
  }

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
  <li><a data-toggle="tab" href="#token">Token</a></li>
  <li><a data-toggle="tab" href="#digital-litracy-quiz">Digital Litracy Course Quiz</a></li>
</ul>

<div class="tab-content">
  <div id="moodle-base-url" class="tab-pane fade in active">
    <div class="row">
      <div class="col-12 col-lg-6 col-md-8 mt-3">
          <div class="panel panel-default">
            <div class="panel-heading">Set Moodle base url</div>
                <div class="panel-body">
                  <form method="POST" id="moodle-url-form"> 
                    <div>
                      
                      <input type="text" name="url" class="form-control" placeholder="Base url" value="<?php echo $url; ?>">
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
  <div id="token" class="tab-pane fade">
    <div class="row">
      <div class="col-12 col-lg-6 col-md-8 mt-3">
          <div class="panel panel-default">
            <div class="panel-heading">Set Token</div>
                <div class="panel-body">
                  <form id="token-form">
                    <div>
                      
                      <input type="text" name="token" class="form-control" placeholder="Enter Token" value="<?php echo $token; ?>">
                    </div>
                    <div class="submit_btn">
                      <input type="submit" name="submit" value="Submit" class="btn btn-info">
                    </div>
                  </form>
                </div>
            </div>
          </div>
      </div>
    
  </div>
  <div id="digital-litracy-quiz" class="tab-pane fade">
    <div class="row">
      <div class="col-12 col-lg-6 col-md-8 mt-3">
          <div class="panel panel-default">
            <div class="panel-heading">Set Digital Litracy test Quiz</div>
                <div class="panel-body">
                  <form id="digital-litracy-quiz-form">
                    <div>
                      <input type="text" name="cmid" class="form-control" placeholder="Enter Course module id" value="<?php echo $cmid; ?>">
                      
                    </div>
                    <div class="submit_btn">
                      <input type="submit" name="submit" value="Submit" class="btn btn-info">
                    </div>
                  </form>
                </div>
            </div>
          </div>
      </div>
    
  </div>
</div>


   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $(function(){
        $('#moodle-url-form').submit(function(e){
          e.preventDefault();
          if($(this).find('input[name="url"]').val() != ""){
            $.ajax({
              type:"POST",
              url:"<?php echo plugins_url('sync-course/moodle-url-ajax.php');?>",
              data:{url:$(this).find('input[name="url"]').val()},
              beforeSend:function(){
                $('#moodle-url-form').find('input[type="submit"]').val('Please Wait...');
                $('#moodle-url-form').find('input[type="submit"]').prop('disabled',true);
              },
              success:function(response){
                console.log('moodle-url-ajax.php',response);
                var data=JSON.parse(response);
                if(data.status==true){
                  alert(data.msg);
                  window.location.reload();
                }else{
                  alert(data.msg);
                }
              },
              complete:function(){
                $('#moodle-url-form').find('input[type="submit"]').val('submit');
                $('#moodle-url-form').find('input[type="submit"]').prop('disabled',false);
              }
            });
          }
        });
        //digital-litracy-form
        $('#token-form').submit(function(e){
          e.preventDefault();
          var token=$(this).find('input[name="token"]').val();
          if(token != ""){
            $.ajax({
              type:"POST",
              url:"<?php echo plugins_url('sync-course/ajax/token.php');?>",
              data:{token:token},
              beforeSend:function(){

              },
              success:function(res){
                var data=JSON.parse(res);
                if(data.status==true){
                  alert(data.msg);
                  window.location.reload();
                }else{
                  alert(data.msg);
                }
              },
              complete:function(){

              }
            });
          }
        });

        $('#digital-litracy-quiz-form').submit(function(e){
          e.preventDefault();
          var cmid=$(this).find('input[name="cmid"]').val();
          console.log('cmid==',cmid);
          if(cmid != ""){
            $.ajax({
              type:"POST",
              url:"<?php echo plugins_url('sync-course/setDigitalLitracyCourse.php');?>",
              data:{cmid:cmid},
              beforeSend:function(){

              },
              success:function(res){
                var data=JSON.parse(res);
                if(data.status==true){
                  alert(data.msg);
                  window.location.reload();
                }else{
                  alert(data.msg);
                }
                console.log('res',res);
              },
              complete:function(){

              }
            });
          }
        });

      });
    </script>
   <!--  <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"> </script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script> -->
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
  `courseid` int(20) NULL,
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

add_filter('wp_nav_menu_items', 'logout_login_menu', 10, 2);
function logout_login_menu($items, $args) {
        ob_start();
        if(is_user_logged_in()){
        	wp_loginout('index.php');
        	$loginoutlink = ob_get_contents();
        }else{
        	$loginoutlink='<a href="'.home_url('/?page_id=1144').'">Login</a>';
        }
        
       // echo "<pre>";
        //print_r($loginoutlink);
       // echo "oooooooooooooooooo";
        //die();
        ob_end_clean();
        // $items .= '<li class="login-logout-btn">'. $loginoutlink .'</li>';
    return $items;
}

add_action( 'wp_logout', 'auto_redirect_external_after_logout');
function auto_redirect_external_after_logout(){
  wp_redirect(home_url('/'));
  exit();
}
function course_mapped(){
	global $wpdb;
	$data_arr=$wpdb->get_results("SELECT p.*,cs.moodle_id FROM {$wpdb->prefix}posts p JOIN {$wpdb->prefix}coursesysc cs ON p.ID=cs.wp_id");
	// echo "<pre>";
	// print_r($data);

	$html='
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
	<div>
	<table class="table table-stripe" id="course-list-mapped">
		<thead>
			<tr>
				<th>S.NO</th>
				<th>Moodle Course Name(ID)</th>
				<th>WP Course Name(ID)</th>
			</tr>

		</thead>
		<tbody>';
		$i=1;
		foreach($data_arr as $data){
			$moodle_data=get_moodle_course($data->moodle_id);
			if(!$moodle_data->status){
				continue;
			}
		$html .='<tr>
				<td>'.$i++.'</td>
				<td>'.$moodle_data->data->fullname.'('.$moodle_data->data->id.')</td>
				<td>'.$data->post_title.'('.$data->ID.')</td>
			</tr>';
		}
		$html .='
		</tbody>
	</table>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
    	$(function(){
    		let table = new DataTable("#course-list-mapped");
    	});
    </script>
	';
	echo $html;

}
function get_moodle_course($id){
	global $wpdb;
	$data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
	//echo $data->url;
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $data->url.'/local/coursesync/get_course_data.php',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS =>json_encode((object)(array('courseid'=>$id))),
	  CURLOPT_HTTPHEADER => array(
	    'Content-Type: application/json'
	  ),
	));

	$response=curl_exec($curl);

	curl_close($curl);
	//print_r($response);

	return json_decode($response);
}
function course_request_lists(){


  $html='
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
  <div class="container">
    <div class="row">
      <div class="col-12">
      <select name="ss" class="form-control change-status">
        <option value="2">
          Generated Print
        </option>
        <option value="3">
          Uploaded Print
        </option>
      </select>

          <table class="table table-stripe" id="course-request-list">
            <thead>
              <tr>
                <th>S.NO</th>
                <th>User</th>
                <th>multistep id</th>
                <th>Course Id</th>
                <th>Price</th>
                <th>Invoice</th>
                <th>Status</th>
                <th>Action</th>
              </tr>

            </thead>
            <tbody>
            
            </tbody>
          </table>
      </div>
    </div>
  </div>
  <!---->

  <div id="image-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Invoice</h4>
          </div>
          <div class="modal-body invoice-image">
      
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>
  <!---->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
    var datatablles;
      $(function(){

       datatablles= $(\'#course-request-list\').DataTable({
      \'processing\': true,
      \'serverSide\': true,
      \'serverMethod\': \'post\',
      \'ajax\': {
        \'url\':\''.plugins_url('sync-course/ajax/').'get_invoice_data.php\'
      },

      \'columns\': [
        { data: \'id\' },
        { data: \'userid\' },
        { data: \'multistep_id\'},
        { data: \'courseid\'},
        { data: \'price\'},
        { data: \'post_id\' },
        { data: \'status\' },
        { data: \'action\' },
      ]

   });
   $("body").on("change",".change-status",function(){
    console.log("oooooooooooo");
      datatablles.fnFilter($(this).val(),4);
    });

   // this.$form.on("keyup", "#datatable-search", function (e) {
                        
   //                  }),
       // let table = new DataTable("#");

      $("#course-request-list").on("click",".approved",function(){
        var that=$(this);
            //console.log("oooooooooooo");
          var invoice_id=$(this).attr("data-id");
          console.log("id",invoice_id);
          if(invoice_id != ""){
            $.ajax({
              type:"POST",
              url:"'.plugins_url('sync-course/ajax/course_enrolment.php').'",
              data:{invoice_no:invoice_id},
              beforeSend:function(){
                $(that).prop("disabled",true);
              },
              success:function(response){
                var data=JSON.parse(response);
                if(data.status==true){
                    alert(data.msg);
                    window.location.reload();
                  }else{
                    alert(data.msg);

                  }
                console.log("response",response);
              },
              complete:function(){
                $(that).prop("disabled",false);
              }
            });
          }
        });
        $("#course-request-list").on("click",".invoice-class",function(){
          var post_id=$(this).attr("post-id");
          console.log("Post id",post_id);
          if(post_id != ""){
            $.ajax({
              type:"POST",
              url:"'.plugins_url('sync-course/ajax/get_invoice_image.php').'",
              data:{post_id:post_id},
              success:function(response){
                $(".invoice-image").html(`<img src="${response}" width="100%"/>`);
                $("#image-modal").modal("show");
              }
            });
          }
        });
      });
    </script>
  ';
  echo $html;
}
add_action( 'init', 'firsttime_attempt_reset_password' );
function firsttime_attempt_reset_password(){
  global $wpdb;
  if(is_user_logged_in()){

    if ( !current_user_can( 'manage_options' ) ) {
      $userid=get_current_user_id();
      $force_login=get_user_meta($userid,'user_force_login',true);
      $mypost_id = url_to_postid($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
      $action='';
      if(isset($_GET['action'])){
        $action=$_GET['action'];
      }
      
      if($force_login==1 && $mypost_id != 1284 && $action !='logout'){
        wp_redirect(get_page_link(1284));
        exit();
      }
    }
  }
}