<?php
require_once("../../../../config.php");
global $DB, $CFG, $PAGE, $USER;

$action = $_POST['action'];

if($action == 'feedfilter'){
	$filter = $_POST['search_query'];
	$assignment_id = $_POST['assignid'];
	$feedtyp = $_POST['feedtyp'];

	if($feedtyp == "fstsearch"){
		$fedtyp = 1;
	}elseif($feedtyp == "secsearch"){
		$fedtyp = 2;
	}elseif($feedtyp == "thrsearch"){
		$fedtyp = 3;
	}

	$opt4= $DB->get_records_sql("SELECT id, feedback FROM {feedback_type} WHERE feedback_type = $fedtyp AND feedback LIKE '%$filter%' AND assignment_id = $assignment_id");
	echo json_encode($opt4);
}


if($action == "feedaction"){
	$filter = $_POST['search_query'];
	$assignment_id = $_POST['assignid'];
	$fedtype = $_POST['fedtype'];

	if(!empty($filter)){
		if($filter == "recupdate"){
			$condition = ' ORDER BY ck.timemodified DESC';
		}elseif($filter == "comused"){
			$condition = '';
			$comax = 'count(cku.id)';
		}elseif($filter == "recused"){	
			$condition = '';
			$comax = 'max(cku.id)';
		}elseif($filter == "recdesc"){
			$condition = ' ORDER BY ck.id DESC';
		}
	}

	if($condition != ''){
		if($fedtype == 'introsupport'){
			$sql = "SELECT f.id, f.feedback, ck.id as kid, ck.keyword, f.assignment_id FROM {feedback_type} f JOIN {custom_keywords_data} ck on f.id = ck.feed_typeid WHERE ck.keyword != '' AND f.feedback_type = 1 AND f.assignment_id = $assignment_id". $condition ."";
			$opt1= $DB->get_records_sql($sql);
		}else if($fedtype == 'mainsupport'){
			$opt1= $DB->get_records_sql("SELECT f.id, f.feedback, ck.id as kid, ck.keyword, f.assignment_id FROM {feedback_type} f JOIN {custom_keywords_data} ck on f.id = ck.feed_typeid WHERE ck.keyword != '' AND f.feedback_type = 2 AND f.assignment_id = $assignment_id". $condition ."");
		}else if($fedtype == 'finalsupport'){
			$opt1= $DB->get_records_sql("SELECT f.id, f.feedback, ck.id as kid, ck.keyword, f.assignment_id FROM {feedback_type} f JOIN {custom_keywords_data} ck on f.id = ck.feed_typeid WHERE ck.keyword != '' AND f.feedback_type = 3 AND f.assignment_id = $assignment_id". $condition ."");
		}
	}else{

		if($fedtype == 'introsupport'){
			$sql = "SELECT  f.id, ck.id as kid, f.assignment_id, ck.keyword, count(cku.id),max(cku.id) FROM {feedback_type} f JOIN {custom_keywords_data} ck on f.id = ck.feed_typeid JOIN {custom_keywords_uses} cku on cku.key_dataid = ck.id WHERE f.feedback_type = 1 AND f.assignment_id = $assignment_id GROUP BY f.feedback ORDER BY $comax desc";
			$opt1= $DB->get_records_sql($sql);
		}else if($fedtype == 'mainsupport'){
			$opt1= $DB->get_records_sql("SELECT  f.id, ck.id as kid, f.assignment_id, ck.keyword, count(cku.id),max(cku.id) FROM {feedback_type} f JOIN {custom_keywords_data} ck on f.id = ck.feed_typeid JOIN {custom_keywords_uses} cku on cku.key_dataid = ck.id WHERE f.feedback_type = 2 AND f.assignment_id = $assignment_id GROUP BY f.feedback ORDER BY $comax desc");
		}else if($fedtype == 'finalsupport'){
			$opt1= $DB->get_records_sql("SELECT  f.id, ck.id as kid, f.assignment_id, ck.keyword, count(cku.id),max(cku.id) FROM {feedback_type} f JOIN {custom_keywords_data} ck on f.id = ck.feed_typeid JOIN {custom_keywords_uses} cku on cku.key_dataid = ck.id WHERE f.feedback_type = 3 AND f.assignment_id = $assignment_id GROUP BY f.feedback ORDER BY $comax desc");
		}

	}

	$opt1 = array_values($opt1);
	echo json_encode($opt1);
}



if($action == "delresourcedata"){
	$delid = $_POST['res_id'];
	if($DB->delete_records('custom_resources', array("id"=>$delid))){
		$result = ["status" => true, "msg" => "done","did"=>$delid];
	}else{
		$result = ["status" => false, "msg" => "Something went wrong."];
	}
	echo json_encode($result);
}

if($action == "resourcedata"){
	$rname = $_POST['rname'];
	$rlink = $_POST['rlink'];
	$assignment_id = $_POST['assignid'];

	$obj = new stdClass();
	$obj->resource = $rname;
	$obj->link = $rlink;
	$obj->assignment_id = $assignment_id;
	$obj->timecreated = time();
	$resid= $DB->insert_record('custom_resources', $obj, $bulk = false);
	if($resid){
		$result = ["status" => true, "msg" => "Your data has been inserted"];
	}else{
		$result = ["status" => false, "msg" => "Something went wrong."];
	}
	echo json_encode($result);
}

if($action == "resfilter"){
	$filter = $_POST['search_query'];
	$assignment_id = $_POST['assignid'];
	if(!empty($filter)){
		$condition = ' AND resource LIKE "%'.$filter.'%"';
	}else{
		$condition = '';
	}
	$opt4= $DB->get_records_sql("SELECT id, link, resource FROM {custom_resources} WHERE assignment_id = $assignment_id". $condition);
	echo json_encode($opt4);
}

if($action == "toupdatekeyword"){
	$feedid =  $_POST['fedid'];
	// echo $feedid;
	$keywd= $DB->get_record_sql("SELECT * from {custom_keywords_data} WHERE feed_typeid = $feedid");
	if(!empty($keywd)){
		$result = ["status" => true, "data" => $keywd];
	}else{
		$result = ["status" => false];
	}
	echo json_encode($result);
}


if($action == "savekeyword"){

	$keyid = $_POST['keyid'];	
		if($keyid != 0){
			$obj = new stdClass();
			$obj->id = $keyid;
			$obj->keyword = $_POST['keyword'];
			$obj->feed_typeid = $_POST['feedid'];
			$obj->timemodified = time();
			if($DB->update_record('custom_keywords_data', $obj, $bulk = false)){
				$result = ["status" => true];
			}else{
				$result = ["status" => false, "msg" => "Error! to update data"];
			}
		}else{
			$feed = $DB->get_record_sql("SELECT id from {feedback_type}  ORDER BY id DESC LIMIT 1");
			$obj = new stdClass();
			$obj->keyword = $_POST['keyword'];
			$obj->feed_typeid = (!empty($_POST['feedid']) ? $_POST['feedid'] : $feed->id) ;
			$obj->timecreated = time();
			if($DB->insert_record('custom_keywords_data', $obj, $bulk = false)){
				$result = ["status" => true];
			}else{
				$result = ["status" => false, "msg" => "Error! to insert data"];
			}
		}
		echo json_encode($result);
}

if($action == "checkeyword"){
	$keyword = $_POST['keyword'];
	$keywd = $DB->get_record_sql("SELECT * FROM {custom_keywords_data} WHERE keyword LIKE '".$keyword."'");
	if(!empty($keywd)){
		$result = ["status" => true, "msg" => "keyword already exists"];
	}else{
		$result = ["status" => false];
	}
	echo json_encode($result);
}


if($action == 'keyuses'){
	$keyid = $_POST['kid'];
	$feedid = $_POST['fedbid'];
	$assid = $_POST['assid'];

	$obj =  new stdClass();
	$obj->key_dataid = $keyid;
	$obj->assignid = $assid;
	$obj->userid = $USER->id;
	$obj->time = time();

	if($DB->insert_record('custom_keywords_uses', $obj, $bulk = false)){
		$fdback = $DB->get_record("feedback_type",array("id"=>$feedid));
		$result = ["status" => true, 'data' => $fdback];
	}else{
		$result = ["status" => false, "msg" => "keyword uses not detect"];
	}
	echo json_encode($result);
}

?>