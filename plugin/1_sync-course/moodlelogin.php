<?php

//echo "hiiii";
if(!is_user_logged_in()){
    wp_redirect( home_url(),302,"You are not loggedin" );//('location:','/');
    exit();
}
global $current_user, $wpdb;
$userid =  $current_user->ID;
//$currentuser = get_currentuserinfo();
//die;
//echo "<pre>";
$user_info = get_userdata($userid);
$useremail = $user_info->user_email;
$username = $user_info->user_login;
$courseid='';
if(isset($_GET['courseid'])){
	$courseid=$_GET['courseid'];
}
//print_r($user_info);

//$mdllist="SELECT moodleurl FROM " .$wpdb->prefix. "moodledetail WHERE id=1";
$mdllists =$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");;
$mdlurl =$mdllists->url;
// echo $mdlurl;
// die;

?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

		<?php if(!empty($useremail)){ ?>
		<script>
			$(document).ready(function () {
				   	var user                = "<?php echo base64_encode($username); ?>";
					var email               = "<?php echo base64_encode($useremail); ?>";
					var mdlurl              = "<?php echo $mdlurl; ?>";
					var courseid            = "<?php echo base64_encode($courseid); ?>";
				window.location.href = mdlurl+"/local/coursesync/session.php?user=" + user + "&email=" + email+"&courseid="+courseid;
			}); 
		</script>
	<?php 
}