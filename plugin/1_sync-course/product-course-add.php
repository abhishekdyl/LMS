<?php

require_once('../../../wp-config.php');
// Takes raw data from the request
//global $table_prefix ,$wpuser,$wpdb;
// echo "<pre>";
// echo $table_prefix ;



//print_r($wpdb);
//$course_sync_data=$wpdb->query();
//$datas=$wpdb->get_results("SELECT * FROM wp_coursesysc");
// echo "<pre>";
// print_r($datas);
$json = file_get_contents('php://input');


// Converts it into a PHP object
$data = json_decode($json);
// echo "<pre>";
// print_r($data);
//echo $data->custom_data->price;
//  echo "<pre>";
// print_r($data);

$wp_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}coursesysc WHERE moodle_id={$data->id}");
$objProduct = new WC_Product();
	$objProduct->set_name($data->fullname);
	$objProduct->set_status("publish");  // can be publish,draft or any wordpress post status
	$objProduct->set_catalog_visibility('visible'); // add the product visibility status
	$objProduct->set_description($data->summary);
	$objProduct->set_sku(""); //can be blank in case you don't have sku, but You can't add duplicate sku's
	$objProduct->set_price($data->custom_data->price);
	//$product->set_price( $price );
	$cat_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}course_category_map WHERE moodle_id={$data->category}");
		if($cat_data){
			// echo "<pre>";
			// print_r($cat_data);
			$objProduct->set_category_ids(array($cat_data->wp_id));
		}
	$objProduct->set_regular_price( $data->custom_data->price ); // To be sure
	// set product price
	// echo "<pre>";
	// print_r($objProduct);
	//  die;
	// $objProduct->set_regular_price(10.55); // set product regular price
	// $objProduct->set_manage_stock(true); // true or false
	// $objProduct->set_stock_quantity(10);
	// $objProduct->set_stock_status('instock'); // in stock or out of stock value
	// $objProduct->set_backorders('no');
	// $objProduct->set_reviews_allowed(true);
	// $objProduct->set_sold_individually(false);
	// $objProduct->set_category_ids(array(1,2,3)); // array of category ids, You can get category id from WooCommerce Product Category Section of Wordpress Admin
	// $attributes = array(
	//     array("name"=>"Size","options"=>array("S","L","XL","XXL"),"position"=>1,"visible"=>1,"variation"=>1),
	//     array("name"=>"Color","options"=>array("Red","Blue","Black","White"),"position"=>2,"visible"=>1,"variation"=>1)
	// );
	// if($attributes){
	//     $productAttributes=array();
	//     foreach($attributes as $attribute){
	//         $attr = wc_sanitize_taxonomy_name(stripslashes($attribute["name"])); // remove any unwanted chars and return the valid string for taxonomy name
	//         $attr = 'pa_'.$attr; // woocommerce prepend pa_ to each attribute name
	//         if($attribute["options"]){
	//             foreach($attribute["options"] as $option){
	//                 wp_set_object_terms($product_id,$option,$attr,true); // save the possible option value for the attribute which will be used for variation later
	//             }
	//         }
	//         $productAttributes[sanitize_title($attr)] = array(
	//             'name' => sanitize_title($attr),
	//             'value' => $attribute["options"],
	//             'position' => $attribute["position"],
	//             'is_visible' => $attribute["visible"],
	//             'is_variation' => $attribute["variation"],
	//             'is_taxonomy' => '1'
	//         );
	//     }
	//     update_post_meta($product_id,'_product_attributes',$productAttributes); // save the meta entry for product attributes
	// }



	// echo "ddd";
	// die;
	// above function uploadMedia, I have written which takes an image url as an argument and upload image to wordpress and returns the media id, later we will use this id to assign the image to product.
	$productImagesIDs = array(); // define an array to store the media ids.
	$images = array($data->image); // images url array of product
	foreach($images as $image){
	    $mediaID = rudr_upload_file_by_url($image);
	    echo "<br>";
	    echo $image.'rrrr';
	    echo $mediaID.'ddddd'; 
	    die;
	      // calling the uploadMedia function and passing image url to get the uploaded media id
	    if($mediaID) $productImagesIDs[] = $mediaID; // storing media ids in a array.
	}
	if($productImagesIDs){ 
	    $objProduct->set_image_id($productImagesIDs[0]); // set the first image as primary image of the product

	        //in case we have more than 1 image, then add them to product gallery. 
	    if(count($productImagesIDs) > 1){
	        $objProduct->set_gallery_image_ids($productImagesIDs);
	    }
	}
	$product_id = $objProduct->save();
	$wpdb->insert("{$wpdb->prefix}coursesysc", array(
	    'wp_id' => $product_id,
	    'moodle_id' => $data->id,
	    'status' => 1, 
	));

// $data->name="shams anjum";
// echo "<pre>";
// print_r($data);

function rudr_upload_file_by_url( $image_url ) {

	// it allows us to use download_url() and wp_handle_sideload() functions
	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	// download to temp dir
	$temp_file = download_url( $image_url );

	if( is_wp_error( $temp_file ) ) {
		return false;
	}

	// move the temp file into the uploads directory
	$file = array(
		'name'     => basename( $image_url ),
		'type'     => mime_content_type( $temp_file ),
		'tmp_name' => $temp_file,
		'size'     => filesize( $temp_file ),
	);
	$sideload = wp_handle_sideload(
		$file,
		array(
			'test_form'   => false // no needs to check 'action' parameter
		)
	);
	// echo "<pre>";
	// print_r($temp_file);
	// print_r($sideload);

	if( ! empty( $sideload[ 'error' ] ) ) {
		// you may return error message if you want
		return false;
	}
	// echo "vvvvvvvvvvvvvvv";
	// it is time to add our uploaded image into WordPress media library
	$attachment_id = wp_insert_attachment(
		array(
			'guid'           => $sideload[ 'url' ],
			'post_mime_type' => $sideload[ 'type' ],
			'post_title'     => basename( $sideload[ 'file' ] ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$sideload[ 'file' ]
	);

	if( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return false;
	}

	// update medatata, regenerate image sizes
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	wp_update_attachment_metadata(
		$attachment_id,
		wp_generate_attachment_metadata( $attachment_id, $sideload[ 'file' ] )
	);

	return $attachment_id;

}