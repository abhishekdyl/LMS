<?php
require_once('../../../wp-config.php');
global $wpdb;

$json = file_get_contents('php://input');
$data = json_decode($json);

$parent_name = $_GET['parent_name'];


$wp_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}terms WHERE name='$parent_name'");
$parent  = $wp_data->term_id; 


$wp_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}course_category_map WHERE moodle_id={$data->id}");
if($wp_data){
	$term_data=get_term_by( 'id', $wp_data->wp_id, 'product_cat' );
	if($term_data){
		$cat_data=wp_update_term(
			$wp_data->wp_id,
		  	'product_cat', // the taxonomy
		  	array(
			    'name'   => $data->name,
			    'slug'   => strtolower($data->name),
			    'parent' => $parent
		  	)
		);
	}else{
		//echo "noooooooooot exist";
		
		$cat_data=wp_insert_term(
	  	$data->name, // the term 
	  	'product_cat', // the taxonomy
	  	array(
		    'description'=> $data->description,
		    'slug' => strtolower($data->name),
		    'parent'=>$parent_id
	  	)
	);
	if(is_wp_error($cat_data)){
		$cat_id=$cat_data->error_data['term_exists'];
	}else{
		$cat_id=$cat_data['term_id'];
	}
	$updated_data=$wpdb->update("{$wpdb->prefix}course_category_map", 
		array('wp_id'=>$cat_id,'updateddate'=>time()), array('id'=>$wp_data->id));
	}
//echo "updatedhhh";

}else{

	$cat_data=wp_insert_term(
	  $data->name, // the term 
	  'product_cat', // the taxonomy
	  	array(
		    'description'=> $data->description,
		    'slug' => strtolower($data->name)
	  	)
	);
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
}