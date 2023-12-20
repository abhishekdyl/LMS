<?php
require_once('../../../../wp-config.php');
	$draw = $_POST['draw'];
	$row = $_POST['start'];
	$rowperpage = $_POST['length']; // Rows display per page
	$columnIndex = $_POST['order'][0]['column']; // Column index
	$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
	$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
	$searchValue = $_POST['search']['value']; // Search value
	$searchQuery = " ";
	if($searchValue != ''){
	   $searchQuery = " and (inv.multistep_id like '%".$searchValue."%' or 
	        invd.courseid like '%".$searchValue."%' or 
	        invd.price like'%".$searchValue."%' or 
	        inv.status like'%".$searchValue."%' ) ";
	}
	$total_count = $wpdb->get_var("select count(*) as allcount from {$wpdb->prefix}invoice");
	$totalRecords = $total_count;//$records['allcount'];

	## Total number of record with filtering
	$total_fil_count = $wpdb->get_var("select count(distinct inv.id) as allcount from {$wpdb->prefix}invoice as inv JOIN {$wpdb->prefix}invoice_details as invd ON invd.invoice_id = inv.id WHERE 1 ".$searchQuery);
	//$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter =$total_fil_count;// $records['allcount'];
	//print_r($totalRecordwithFilter);

	## Fetch records
	//$inv_Query = "select inv.*,mul.post_data from {$wpdb->prefix}invoice inv JOIN {$wpdb->prefix}posts p ON p.id=inv.courseid LEFT JOIN {$wpdb->prefix}multistepform mul ON mul.id=inv.multistep_id WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

	$inv_Query = "SELECT inv.*,invd.multistep_id,GROUP_CONCAT(invd.courseid) as usercourseid ,SUM(invd.price) as totalprice,mul.post_data FROM {$wpdb->prefix}invoice as inv INNER JOIN {$wpdb->prefix}invoice_details as invd ON invd.invoice_id = inv.id LEFT JOIN {$wpdb->prefix}multistepform as mul ON mul.id=inv.multistep_id WHERE 1 ".$searchQuery." GROUP BY inv.id order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage; 
	
	//$inv_Query = "select inv.*,invd.multistep_id,GROUP_CONCAT(invd.courseid) as usercourseid ,mul.post_data from wp_invoice as inv INNER JOIN wp_invoice_details as invd ON inv.id = invd.invoice_id LEFT JOIN wp_multistepform mul ON mul.id=inv.multistep_id GROUP by inv.id"
	
	$invRecords = $wpdb->get_results($inv_Query);
	$data = array();
	$i=1;
	foreach($invRecords as $inv){
		$status="";
		$action="";
		switch($inv->status){
			case 1:
			$status="Print";
			break;
			case 2:
			$status="Uploaded";
			$action='<button  class="approved" data-id="'.$inv->id.'" class="btn btn-info">Approved</button>
			';
			break;
			case 3:
			$status='Approved
			';
			break;
			case 4:
			$status="Online";
			break;
			default:
			$status="New";
		}
		$action= '<a href="/learnoneplanet/wp-admin/admin.php?page=mapping&id='.$inv->multistep_id.'">View</a>'; 
		$image='';
		if(!empty($inv->post_id)){
			$image='<a href="javascript:void(0);" post-id="'.$inv->post_id.'" class="invoice-class">'.wp_get_attachment_image($inv->post_id).'</a>';
		}
		$user="";
		if(!empty($inv->userid)){
			$userdata=get_user_by('id',$inv->userid);
			$firstname=get_user_meta($inv->userid,'first_name',true);
			$lastname=get_user_meta($inv->userid,'last_name',true);
			$user=$firstname.' '.$lastname;
		}else if(!empty($inv->multistep_id)){
			$post_data=unserialize($inv->post_data);
			$user=$post_data['fname'].' '.$post_data['lname'];
		}
		$data[] = array( 
		"id"=>$inv->id,
	    "userid"=>$user,
	    "multistep_id"=>$inv->multistep_id,
	    "courseid"=>$inv->usercourseid,
	    "price"=>'$'.$inv->totalprice,
	    "post_id"=>$image,
	    "status"=>$status,
	    "action"=>$action,
	   	);
	}
	// ## Response
	$response = array(
	  "draw" => intval($draw),
	  "iTotalRecords" => $totalRecords,
	  "iTotalDisplayRecords" => $totalRecordwithFilter,
	  "aaData" => $data
	);

	echo json_encode($response);

?>