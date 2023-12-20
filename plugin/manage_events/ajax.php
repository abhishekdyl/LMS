<?php
include_once("../../config.php");
global $DB,$PAGE,$CFG,$USER;
$draw = $_POST['draw'];
$rowstart = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$del = $_POST['del_event_id'];
$edit = $_POST['edit_event_id'];

if($del){
    $objdel = new stdClass();
    $objdel->id = $del;    
    $objdel->deleted = 1;
    $aaa  = $DB->update_record('manual_events', $objdel, $bulk = false);
$sql_delete = 'SELECT mev.id as mid, e.* FROM "mdl_manual_events_list" mev INNER JOIN "mdl_event" e ON mev.eid = e.id WHERE mev.meid = '.$del.'';
$bbb = $DB->get_record_sql($sql_delete,array());
 $DB->delete_records('event',array("id"=>$bbb->id));
 $DB->delete_records('manual_events_list',array("id"=>$bbb->mid));
// redirect($CFG->wwwroot."/local/manage_events/index.php", null, "Updated");
}



$orderQuery = " ";
if(!empty($rowperpage)){
  $orderQuery = " ORDER BY $columnName $columnSortOrder";
}

if($searchValue != ''){
    $searchQuery = "(name ILIKE '%".$searchValue."%'
    or description ILIKE '%".$searchValue."%') AND "; 
 }

$sqlq = 'SELECT * FROM {manual_events} WHERE '.$searchQuery.' deleted = 0 '.$orderQuery.'';
$events = $DB->get_records_sql($sqlq,array(), $rowstart, $rowperpage);

$totalrecords = $DB->count_records('manual_events', array());

$sql = 'SELECT COUNT(id) FROM {manual_events} WHERE '.$searchQuery.' deleted = 0 limit '.$rowperpage.'';
$TotalDisplayRecords = $DB->count_records_sql($sql ,array());

foreach ($events as $key => $data) {
    $data->link = '<button class="btn del btn-danger" value="'.$data->id.'">Delete</button>';
    $data->edit = '<a href="'.$CFG->wwwroot.'/local/manage_events/events.php?edit='.$data->id.'"><button class="btn edit btn-primary" value="'.$data->id.'">Edit</button></a>';
    $data->timestart = date('d F, Y h:i A', $data->timestart);
    $events[$key] = $data;
   }
 $response = array(
     "draw" => intval($draw),
     "iTotalRecords" => $totalrecords,
     "iTotalDisplayRecords" => $TotalDisplayRecords,
     "aaData" => array_values($events)
   );
 echo json_encode($response);
 