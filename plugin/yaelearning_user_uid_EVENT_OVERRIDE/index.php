<?php

require_once("../../config.php");
global $DB, $CFG;

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
$data = '55555555';

echo generateUID($data, 0);

?>