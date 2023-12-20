<?php
  function update_user_field($userid,$key,$data){ 
    global $DB; 
    if($DB->record_exists('user_info_data',array('userid'=>$userid,'fieldid'=>$key))){
             $sql = "UPDATE mdl_user_info_data SET `data`= '".$data."' WHERE userid=$userid AND fieldid=$key";
           $res =$DB->execute($sql);
     }else{
        $stdobj = new stdClass();
        $stdobj->userid=$userid;
        $stdobj->fieldid = $key;
        $stdobj->data = $data;
       // print_r($stdobj);
        $DB->insert_record('user_info_data',$stdobj);
     } 
  }

	 function generateUID($data, $number){
    global $DB;
    $field = $DB->get_record("user_info_field", array("shortname"=>"uniqueID"));
    if($field){
        $likefieldid = $DB->sql_like('fieldid', ':fieldid');
        $likedata = $DB->sql_like('data', ':data');
      if($DB->record_exists_sql("SELECT * FROM {user_info_data} WHERE {$likefieldid} AND {$likedata}",array('fieldid'=>$field->id, "data"=>$data))){
        $number++;
        $data = $data.$number;
        $data = generateUID($data, $number);
      }
    }
    return $data;

}

function user_created(\core\event\user_created $event){
	global $DB, $USER,$CFG;
    $userid = $event->relateduserid;
    $get_data_dob =  $DB->get_record_sql("SELECT ud.*,u.firstname, u.lastname FROM {user} u INNER JOIN {user_info_data} ud ON u.id = ud.userid WHERE u.id = $userid AND ud.fieldid = 1");
  $get_data_fath =  $DB->get_record_sql("SELECT ud.*,u.firstname, u.lastname FROM {user} u INNER JOIN {user_info_data} ud ON u.id = ud.userid WHERE u.id = $userid AND ud.fieldid = 13"); 
  $get_unikey =  $DB->get_record_sql("SELECT ud.*,u.firstname, u.lastname FROM {user} u INNER JOIN {user_info_data} ud ON u.id = ud.userid WHERE u.id = $userid AND ud.fieldid = 22");
 $data = strtoupper(substr($get_data_dob->firstname, 0, 2)).''.strtoupper(substr($get_data_dob->lastname, 0, 2)).''.strtoupper(substr($get_data_fath->data, 0, 2)).''.substr(date('Y',$get_data_dob->data), strlen(date('Y',$get_data_dob->data))-2).''.strtoupper(substr($get_unikey->data, 0, 1));
  $key = 18 ;
 $data = generateUID($data, 0);
    update_user_field ($userid,$key,$data);

}

?>

