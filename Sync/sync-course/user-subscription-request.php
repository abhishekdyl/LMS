<?php
global $wpdb,$woocommerce;
$userid = $_REQUEST['uid'];
$membersid = $_REQUEST['mid'];
$getmemberdata = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'members_info WHERE id='.$membersid);
if($getmemberdata){
	$product_data = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'postmeta WHERE meta_key ="_members_children" AND meta_value='.$getmemberdata->member_count);
	$product_id = $product_data->post_id;
	$woocommerce->cart->add_to_cart($product_id);
	$cart = WC()->cart->cart_contents;
	 $reload = do_shortcode('[woocommerce_cart]'); // Important To Update Cart
	wp_redirect(wc_get_cart_url());
	exit;
}
//print_r($getmemberdata);