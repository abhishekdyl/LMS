<?php
require_once('../../../../wp-config.php');
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	// echo "<pre>";
	// print_r($_POST);
	// print_r($_FILES);
	$invoice_no=$_POST['invoice'];
	$error_arr=array();
	if(!isset($invoice_no) || empty($invoice_no)){
		array_push($error_arr,array('key'=>'invoice','msg'=>'Please input invoice no'));
	}
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	// Do the upload, this moves the file to the uploads folder
	$upload = wp_handle_upload( $_FILES['invoice_image'], array( 'test_form' => false, 'action' => 'local' ) );
	if ( !empty($upload['error']) ){
		array_push($error_arr,array('key'=>'invoice_image','msg'=>'Image not uploaded properly'));
	}
	// Generate a title if needed
	if ( empty($title) ) $title = pathinfo($path, PATHINFO_FILENAME);
	// Create the "attachment" post, as seen on the media page
	$args = array(
		'post_title' => $title,
		'post_content' => '',
		'post_status' => 'publish',
		'post_mime_type' => $upload['type'],
	);
	
	$attachment_id = wp_insert_attachment( $args, $upload['file'] );
	
	// Abort if we could not insert the attachment
	// Also when aborted, delete the unattached file since it would not show up in the media gallery
	if ( is_wp_error( $attachment_id ) ) {
		@unlink($upload['file']);
		array_push($error_arr,array('key'=>'invoice_image','msg'=>'Image not uploaded properly'));
		//return false;
	}
	if(count($error_arr)){
		$msg['status']=false;
		$msg['data']=$error_arr;
	}else{
		$invoice_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}invoice WHERE id=$invoice_no");
		if(!$invoice_data){
			array_push($error_arr,array('key'=>'invoice','msg'=>'Please Enter valid invoice no'));
			$msg['status']=false;
			$msg['data']=$error_arr;
		}else{
			if($invoice_data->status>2){
				array_push($error_arr,array('key'=>'invoice','msg'=>'Invoice already uploaded'));
				$msg['status']=false;
				$msg['data']=$error_arr;

			}else{
				$wpdb->update("{$wpdb->prefix}invoice",array('status'=>2,'updateddate'=>time(),'post_id'=>$attachment_id),array('id'=>$invoice_no));
				$msg['status']=true;
				$msg['data']='Uploaded successfully';	
			}
			
			//$msg['data']=$invoice_no;
		}
		$data = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
		wp_update_attachment_metadata( $attachment_id, $data );

	}

		
	// Upload was successful, generate and save the image metadata
	
	
}
echo json_encode($msg);