<?php
include_once("../../config.php");
global $DB,$PAGE,$CFG,$USER;

$time = time();
$draw = $_POST['draw'];
$rowstart = $_POST['start'];
$rowperpage = $_POST['length']; 
$columnIndex = $_POST['order'][0]['column']; 
$columnName = $_POST['columns'][$columnIndex]['data']; 
$columnSortOrder = $_POST['order'][0]['dir']; 


$searchValue = $_POST['search']['value']; 
$search_student = $_POST['search_student']; 
$subscription_method = $_POST['subscription_method']; 
$filter_status = $_POST['filter_status']; 
$filter_date = $_POST['filter_date']; 
$custom_start_date  =$_POST['custom_start_date'];
$custom_end_date = $_POST['custom_end_date'];



## Search 
$searchQuery = array( "1=1");

if($searchValue != ''){
   $searchQuery[] = "(u.firstname LIKE '%$searchValue%' OR u.lastname LIKE '%$searchValue%' OR u.email LIKE '%$searchValue%')"; 
}

if(!empty($search_student)){
   if($search_student!='All'){
   $searchQuery[] = "asu.userid = '$search_student'"; 
   }else{

  }
}

if(!empty($subscription_method)){
  if($subscription_method!='All'){
   $searchQuery[] = "asu.subscription_method = '$subscription_method'"; 
   }else{

  }
}


$start_time = strtotime(date("d F Y 00:00:00"));
$end_time = strtotime(date("d F Y 23:59:59"));


$this_month_start = strtotime(date("01 F Y 00:00:00"));
$this_month_end = strtotime(date("t F Y 23:59:59"));


if(!empty($filter_date)){


    if(date('D')!='Mon')
    {    
        //take the last monday
        $staticstart = strtotime('last Monday');    
    }else{
        $staticstart = strtotime(date('Y-m-d'));   
    }

        
    if(date('D')!='Sun')
    {
        //always next sunday
        $staticfinish = strtotime('next Sunday');

    }else{
        $staticfinish = strtotime(date('Y-m-d'));
    }



    if($filter_date=='today'){
        $start_time = strtotime(date("d F Y 00:00:00"));
        $end_time = strtotime(date("d F Y 23:59:59"));
    }



    if($filter_date=='this_week'){
        $start_time = $staticstart;
        $end_time = $staticfinish;
    }


    if($filter_date=='last_week'){
        $start_time = strtotime("-1 Week",$staticstart);
        $end_time = strtotime("-1 Week",$staticfinish);
    }


    if($filter_date=='this_month'){
        $start_time = $this_month_start;
        $end_time = $this_month_end;
    }


    if($filter_date=='last_month'){
        $start_time = strtotime("-1 Month",$this_month_start);
        $end_time = strtotime("-1 Month",$this_month_end);
    }

    if($filter_date=='custom_date'){
        $start_time = strtotime(date("d F Y H:i:s",strtotime($custom_start_date)));
        $end_time = strtotime(date("d F Y 23:29:29",strtotime($custom_end_date)));
    }


    if($filter_date!='All'){
    $searchQuery[] = "asu.start_date between '$start_time' and '$end_time'"; 
    }

}




   if($filter_status!='All'){

    if ($filter_status == 0 ) {
         $searchQuery[] = "asu.end_date < $time"; 
    }

    if ($filter_status == 1) {
        $searchQuery[] = "asu.end_date > $time"; 
    }
   
   }else{

  }



if(!empty($searchQuery)){
    
    $searchQuery = " WHERE ".implode(" AND ", $searchQuery );
} 


$orderQuery = " ";
if($rowperpage != ''){
    $orderQuery = " ORDER BY $columnName $columnSortOrder";
}


$allforums = array();
$sql = "SELECT asu.*, u.firstname, u.lastname FROM {assign_subs_users} asu INNER JOIN {user} u ON u.id=asu.userid $searchQuery $orderQuery";
$allforums = $DB->get_records_sql($sql, array(), $rowstart, $rowperpage);


$iTotalRecords = $DB->get_field_sql("SELECT COUNT(id) FROM {assign_subs_users} ");
$iTotalDisplayRecords = $DB->get_field_sql("SELECT COUNT(asu.id) FROM {assign_subs_users} asu INNER JOIN {user} u ON u.id=asu.userid $searchQuery");



foreach ($allforums as $key => $data) {

   $uid = $data->id;
   $userid = $data->userid;

   $sql_chk = "SELECT * FROM {assign_subs_history} WHERE userid='$userid'";
   $allforums_chk = $DB->get_records_sql($sql_chk);

   if(count($allforums_chk)>0){
     $updated = '<i>(Updated)</i>';
   }else{
     $updated = '';
   }
  

   $start_date = date("d/m/Y",$data->start_date);
   $end_date = date("d/m/Y",$data->end_date);
   $cost = ($data->cost)==0 ? 'N/A' : $data->cost;
   $subscription_method = $data->subscription_method;
   $subscription_duration = $data->subscription_duration;

   if(!empty($data->end_date)){
    $date1=date_create(date("Y-m-d", $data->start_date));
    $date2=date_create(date("Y-m-d", $data->end_date));
    $diff=date_diff($date1,$date2);
    $days = $diff->format("%a days");
   } else {
    $days = "N/A";
   }
   $days .= $updated;
//    $days = (($data->end_date-$data->start_date) / (60 * 60 * 24)-1). " days ".$updated;

   

   $status =  $data->status;

   if($status==1 AND $data->end_date>$time){ 
   $status_active = "<span style='color: green;'>Active</span>"; 
   $active_deactive = 'inactive.php';
   $eye = '<i class="fa fa-eye"></i>';
   }else{  
   $status_active = "<span style='color: red;'>Inactive</span>";
   $active_deactive = 'active.php'; 
   $eye = '<i class="fa fa-eye-slash"></i>';
   }

   $confirm = 'Are you sure?';

   if($status==1 AND $data->end_date>$time)
    {

        $data->username = $data->firstname." ".$data->lastname;
        $data->start_date = $start_date;
        $data->end_date = $end_date;
        $data->cost = $cost;
        $data->subscription_method = $subscription_method;
        $data->subscription_duration = $days;
        $data->status = $status_active;
        $data->link = '<a href="delete_user.php?uid='.$uid.'" onclick="return confirm('."'".($confirm)."'".')"><i class="fa fa-trash"></i> </a>
        <a href="javascript:void(0);" >'.$eye.' </a>
        <a href="create_subscription.php?uid='.$uid.'"><i class="fa fa-gear"></i> </a>';

    }else{

        $data->username = "<span style='color: #c1a7a7e8;'>".$data->firstname." ".$data->lastname."</span>";
        $data->start_date = "<span style='color: #c1a7a7e8;'>".$start_date."</span>";
        $data->end_date = "<span style='color: #c1a7a7e8;'>".$end_date."</span>";
        $data->cost = "<span style='color: #c1a7a7e8;'>".$cost."</span>";
        $data->subscription_method = "<span style='color: #c1a7a7e8;'>".$subscription_method."</span>";
        $data->subscription_duration = "<span style='color: #c1a7a7e8;'>".$days."</span>";
        $data->status = "<span style='color: #c1a7a7e8;'>".$status_active."</span>";
        $data->link = '<a href="delete_user.php?uid='.$uid.'" onclick="return confirm('."'".($confirm)."'".')"><i class="fa fa-trash"></i> </a>
        <a href="javascript:void(0);">'.$eye.' </a>
        <a href="create_subscription.php?uid='.$uid.'"><i class="fa fa-gear"></i> </a>';

    }


   $allforums[$key] = $data;
   
}


  $response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $iTotalRecords,
    "iTotalDisplayRecords" => $iTotalDisplayRecords,
    "aaData" => array_values($allforums)
  );


echo json_encode($response);
