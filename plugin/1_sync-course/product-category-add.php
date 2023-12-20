<?php

require_once('../../../wp-config.php');
global $wpdb;

$json = file_get_contents('php://input');
$data = json_decode($json);
$parent_name = $_GET['parent_name'];


$wp_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}terms WHERE name='$parent_name'");
$parent  = $wp_data->term_id; 


// echo "<pre>";
// print_r($wp_data);
// echo "</pre>";


// die();

// Checking parent exists or not
$cat_data=wp_insert_term(
  $data->name, // the term 
  'product_cat', // the taxonomy
  	array(
	    'description'=> $data->description,
      'parent'=> $parent
  	)
);


// echo "<pre>";
// print_r($cat_data);
// die();




if(is_wp_error($cat_data)){
	$cat_id=$cat_data->error_data['term_exists'];
}else{
	$cat_id=$cat_data['term_id'];
}



$wpdb->insert("{$wpdb->prefix}course_category_map", array(
    'wp_id' => $cat_id,
    'moodle_id' => $data->id,
    'createddate'=>time()
));


// echo "<pre>";
// print_r($final);
// die();

?>