<?php
require_once("../../../wp-config.php");
global $wpdb;
// Reading value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

$searchArray = array();
// Search
// Total number of records without filtering
$stmt = $wpdb->get_row("SELECT COUNT(*) AS allcount FROM " . $wpdb->prefix . "permit_number as pn LEFT JOIN ".$wpdb->prefix."users as u ON u.ID=pn.user_id LEFT JOIN ".$wpdb->prefix."usermeta as umf ON umf.user_id=u.ID AND umf.meta_key = 'first_name' LEFT JOIN ".$wpdb->prefix ."usermeta as uml ON uml.user_id=u.ID AND uml.meta_key = 'last_name'");
$totalRecords = $stmt->allcount;

// Total number of records with filtering
//$stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM employees WHERE 1 ".$searchQuery);
$search_array=array('1=1');
if(!empty($searchValue)){
	$searchValue = implode("%", explode(" ", $searchValue));
	$arra = array();
	array_push($arra, 'pn.class_id like "%'. $searchValue .'%" ');
	array_push($arra, 'pn.permit_number like "%'. $searchValue .'%" ');
	array_push($arra, 'umf.meta_value like "%'. $searchValue .'%" ');
	array_push($arra, 'uml.meta_value like "%'. $searchValue .'%" ');
	array_push($search_array, '('.implode(" or ", $arra).')');
}

$searchQuery=implode(' AND ',$search_array);


$stmt = $wpdb->get_row("SELECT COUNT(*) AS allcount FROM " . $wpdb->prefix . "permit_number as pn LEFT JOIN ".$wpdb->prefix."users as u ON u.ID=pn.user_id LEFT JOIN ".$wpdb->prefix."usermeta as umf ON umf.user_id=u.ID AND umf.meta_key = 'first_name' LEFT JOIN ".$wpdb->prefix ."usermeta as uml ON uml.user_id=u.ID AND uml.meta_key = 'last_name' WHERE  ".$searchQuery);
$totalRecordwithFilter = $stmt->allcount;


// Fetch records
$resultdata = $wpdb->get_results("SELECT pn.*,umf.meta_value as fname,uml.meta_value as lname FROM " . $wpdb->prefix . "permit_number as pn LEFT JOIN ".$wpdb->prefix."users as u ON u.ID=pn.user_id LEFT JOIN ".$wpdb->prefix."usermeta as umf ON umf.user_id=u.ID AND umf.meta_key = 'first_name' LEFT JOIN ".$wpdb->prefix ."usermeta as uml ON uml.user_id=u.ID AND uml.meta_key = 'last_name' WHERE ".$searchQuery." ORDER BY pn.".$columnName." ".$columnSortOrder." LIMIT ".$row.",".$rowperpage);




//echo "SELECT pn.*,umf.meta_value as fname,uml.meta_value as lname FROM " . $wpdb->prefix . "permit_number as pn LEFT JOIN ".$wpdb->prefix."users as u ON u.ID=pn.user_id LEFT JOIN ".$wpdb->prefix."usermeta as umf ON umf.user_id=u.ID AND umf.meta_key = 'first_name' LEFT JOIN ".$wpdb->prefix ."usermeta as uml ON uml.user_id=u.ID AND uml.meta_key = 'last_name' WHERE ".$searchQuery." ORDER BY pn.".$columnName." ".$columnSortOrder." LIMIT ".$row.",".$rowperpage;

$data = array();
$i=1;
foreach ($resultdata as $row) {
	//$userlist = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "users WHERE ID=".$row->user_id);
	array_push($data, array(
		"id"=>$i++,
		"class_id"=>($row->class_id == "class_12" ? "Class 12" : "class 13"),
		"permit_number"=>$row->permit_number,
		"user_id"=>$row->fname ." ".  $row->lname,
		"createdtime"=>date('m/d/Y', $row->createdtime), 
		"assigntime"=>($row->assigntime ? date('m/d/Y',$row->assigntime):'Null'),//date($row->assigntime),
		"action"=>'<a onclick="delete_permit_number('.$row->id.')" class="permit_delete">Delete</a>',
	)); 
}

// Response
$response = array(
	"draw" => intval($draw),
	"iTotalRecords" => $totalRecords,
	"iTotalDisplayRecords" => $totalRecordwithFilter,
	"aaData" => $data
);

echo json_encode($response);
