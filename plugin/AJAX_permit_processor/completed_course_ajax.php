<?php
    require_once("../../../wp-config.php");
	global $wpdb;
   date_default_timezone_set( 'America/Los_Angeles' );
   date_default_timezone_get();
   $sql='SELECT * FROM '. $wpdb->users. ' Where ID>1 ORDER BY ID DESC';
   $users=$wpdb->get_results($sql);
   $coursearr = array();	
   if($users)
	{
		foreach($users as $user)
		{
			$user_meta=get_userdata($user->ID);	
			 $user_meta->first_name;
			   array_push($coursearr, $user_meta->first_name);
	}
	$jsondata = json_encode($coursearr);
	}
	
	 
	// print_r($jsondata); 