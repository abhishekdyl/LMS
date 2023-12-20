<?php
session_start();
global $wpdb,$wp_session; 
//define('WP_DEBUG', true);
$orderid = $_GET['order'];
// Get an instance of the WC_Order object
$order = new WC_Order($orderid);
$order = wc_get_order( $orderid ); 
$order_data =$order->get_meta_data(); 
$user_id = $order->get_user_id();

// get user data
$user_info = get_userdata($user_id);
$user_email = $user_info->user_email;
$username = $user_info->user_login;

// get user meta data
$user_meta_data = get_user_meta( $user_id );
$first_name = $user_meta_data['first_name'][0];
$last_name = $user_meta_data['last_name'][0];
$userpassword = $user_meta_data['member_password'][0];

// get user iformatiom
$members_info = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "members_info WHERE user_id='".$user_id."'");
if($members_info){
   $wpdb->update($wpdb->prefix."members_info",
   	array(
      'status'=>1,
      'updated_date'=>time()
    ),
    array(
      'id'=>$members_info->id
    )
  );
}
$members_type = $members_info->members_type;
if($members_info->payment_type=="1"){
	$payment_type = "annual_subcription";
}else{
	$payment_type = "monthly_subcription";
}
$member_count = $members_info->member_count;

$request_data=array('user_email'=>$user_email,'username'=>$username,'firstname'=>$first_name,'lastname'=>$last_name,'userpassword'=>md5(base64_decode($userpassword)),'members_type'=>$members_type,'payment_type'=>$payment_type,'member_count'=>$member_count,'enrolldate'=>$members_info->updated_date);
// get moodle url
$setting_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
$url=$setting_data->url;
json_encode((object)$request_data);
// echo $url.'/local/coursesync/enrollcourse.php';
// print_r($request_data);

$curl =curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $url.'/local/coursesync/enrollcourse.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode((object)$request_data),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
return $response;
get_footer();
