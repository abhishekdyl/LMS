<?php
include_once("../../config.php");
global $DB,$PAGE,$CFG,$USER;
// $draw = $_POST['id'];
$draw = $_POST['draw'];
$rowstart = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchByCourse = $_POST['searchByCourse']; // Search value

if(empty($rowperpage) || $rowperpage <=0){
  $rowperpage = 5;
}
## Search 
$searchQuery = array( "1=1");
if($searchValue != ''){
   $searchQuery[] = "(f.name ILIKE '%".$searchValue."%'
   or c.fullname ILIKE '%".$searchValue."%')"; 
}
if(!empty($searchByCourse)){
  $searchQuery[] = "c.id = $searchByCourse"; 
}
if(!empty($searchQuery)){
  $searchQuery = " WHERE ".implode(" AND ", $searchQuery )." ";
} 
$orderQuery = " ";
if(!empty($columnName)){
  $orderQuery = " ORDER BY $columnName $columnSortOrder";
}

$allforums = array();
$sql = "SELECT cm.id, c.fullname, f.name from {course_modules} cm INNER JOIN {modules} m ON m.id=cm.module and m.name='forum' INNER JOIN {forum} f ON f.id=cm.instance INNER JOIN {course} c ON f.course = c.id $searchQuery $orderQuery";
$allforums = $DB->get_records_sql($sql, array(), $rowstart, $rowperpage); 
$iTotalRecords = $DB->get_field_sql("SELECT count(cm.id) from {course_modules} cm INNER JOIN {modules} m ON m.id=cm.module and m.name='forum' INNER JOIN {forum} f ON f.id = cm.instance INNER JOIN {course} c ON f.course = c.id");
$iTotalDisplayRecords = $DB->get_field_sql("SELECT COUNT(cm.id) from {course_modules} cm INNER JOIN {modules} m ON m.id=cm.module and m.name='forum' INNER JOIN {forum} f ON f.id = cm.instance INNER JOIN {course} c ON f.course = c.id $searchQuery");

foreach ($allforums as $key => $data) {
  $data->link = '<a href="'.$CFG->wwwroot.'/mod/forum/view.php?id='.$data->id.'">View</a>';
  $allforums[$key] = $data;
}
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $iTotalRecords,
    "iTotalDisplayRecords" => $iTotalDisplayRecords,
    "aaData" => array_values($allforums)
  );
echo json_encode($response);
