<?php
ob_start();
session_start();
if(!isset($_SESSION['repusersid'])){
    $_SESSION['repusersid'] = array();
}

if ($_POST['action'] == 'delete_permit_number') {
	require_once("../../../wp-config.php");
    global $wpdb;
    $id = $_POST['id'];
 	$sqlDel = "DELETE FROM " . $wpdb->prefix . "permit_number WHERE id='" . $id . "'";
    $queryDel = $wpdb->query($sqlDel);
    if ($queryDel) {
        echo $status = 1;
    } else {
        echo $status = 0; 
    } 
}
if ($_POST['action'] == 'hide_permit_range') { 
    require_once("../../../wp-config.php");
    global $wpdb;
    $permit_range_id = $_POST['permit_range_id'];
	$query = "SELECT * FROM " . $wpdb->prefix . "permit_range where id= '". $permit_range_id ."'";
	$getrecords =$wpdb->get_row($query);
	$status = $getrecords->status;
    if($status=='1'){
	  $qup = $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "permit_range SET status='0' WHERE id='". $permit_range_id ."'"));
	  $newstatus =0;
    }else{
	  $qup = $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "permit_range SET status='1' WHERE id='". $permit_range_id ."'")); 
	  $newstatus =1;
	
    } 	 
}

if ($_POST['action'] == 'action_permit_unassign') {
	require_once("../../../wp-config.php");
	global $wpdb;	
	$userdata=$_POST['userId'];
	if(!is_array($userdata)){
		$userdata=[$userdata];
	}
	$responsedata = array();
	//$i=1;
	foreach($userdata as $userId){   
		$permit_recordstb = $wpdb->prefix . "user_permit_records"; 
		$getpermit = "SELECT * FROM " . $wpdb->prefix . "usermeta where user_id= '". $userId ."' AND meta_key='permit_number'";
		$permit_number =$wpdb->get_row($getpermit); 
		$metaId = $permit_number->umeta_id; 
		$std_permit_number = $permit_number->meta_value;
		if(!empty($std_permit_number)){

			$active_permit_query= "SELECT * From " . $wpdb->prefix . "user_permit_records WHERE permit='". $std_permit_number ."' AND userid ='".$userId."' AND status = 1 "; 
			$active_permit = $wpdb->get_row($active_permit_query);
			if($active_permit){
				update_user_meta ( $userId, 'permit_number_old', $std_permit_number);
				// var_dump($userId);
				// print_r($std_permit_number);
				// die;
				$updat=$wpdb->update($permit_recordstb, 
					array( 
						'status' => '0',
						'updatedtime' => time(),
					),
					array('id' => $active_permit->id)
				); 

			}else{
				update_user_meta ( $userId, 'permit_number_old', $std_permit_number, true );
				$permit_record  = array(
					'permit'=>$std_permit_number, 
					'userid'=>$userId, 
					'status'=>0, 
					'updatedtime'=>time(),
				); 
				$per_rec = $wpdb->insert($permit_recordstb,$permit_record);	
			}
		}
		// die;
		$sqlDel = "DELETE FROM " . $wpdb->prefix . "usermeta WHERE umeta_id='" . $metaId . "'";
		$queryDelpage = $wpdb->query($sqlDel);
		if ($queryDelpage) {
			$get_permit= "SELECT * From " . $wpdb->prefix . "permit_number WHERE permit_number='". $std_permit_number ."'"; 
			$alow_permit_number = $wpdb->get_row($get_permit);
			$permit_id = $alow_permit_number->id;
			$table_name = $wpdb->prefix . "permit_number";  
			$updated=$wpdb->update($table_name, 
				array( 
					'status' => '2',
				),
				array('id' => $permit_id) 
			);      
			unset($_SESSION['repusersid'][$userId]);
			array_push($responsedata,array('userid'=>$userId,'status'=>1,'permit_number'=>'', "aaaaa"=>$_SESSION['repusersid']));
		} else {
			array_push($responsedata,array('userid'=>$userId,'status'=>0));
		}
	}
	echo json_encode($responsedata);
}
if ($_POST['action'] == 'action_permit_reassign') {
	require_once("../../../wp-config.php");
	global $wpdb;	
	$userdata = $_POST['userId'];
	if(!is_array($userdata)){
		$userdata=[$userdata];
	}
	$responsedata = array();
	foreach($userdata as $userId){
		$permit_recordstb = $wpdb->prefix . "user_permit_records";
		$getpermit = "SELECT * FROM " . $wpdb->prefix . "usermeta where user_id= '". $userId ."' AND meta_key='permit_number'"; 
		$permit_number =$wpdb->get_row($getpermit);
		$metaId = $permit_number->umeta_id;
		$std_permit_number = $permit_number->meta_value;
		if(!empty($std_permit_number)){
			$active_permit_query= "SELECT * From " . $wpdb->prefix . "user_permit_records WHERE permit='". $std_permit_number ."' AND userid ='".$userId."' AND status = 1 "; 
			$active_permit = $wpdb->get_row($active_permit_query);
			if($active_permit){
				update_user_meta ( $userId, 'permit_number_old', $std_permit_number, true );
				$updat=$wpdb->update($permit_recordstb, 
					array( 
						'status' => '0',
						'updatedtime' => time(),
					),
					array('id' => $active_permit->id)
				); 

			}else{
				update_user_meta ( $userId, 'permit_number_old', $std_permit_number, true );
				$permit_record  = array(
					'permit'=>$std_permit_number, 
					'userid'=>$userId, 
					'status'=>0, 
					'updatedtime'=>time(),
				); 
				$per_rec = $wpdb->insert($permit_recordstb,$permit_record);	
			}	
		}
		$sqlDel = "DELETE FROM " . $wpdb->prefix . "usermeta WHERE umeta_id='" . $metaId . "'"; 
		$queryDelpage = $wpdb->query($sqlDel);
		if ($queryDelpage) {
			$get_permit= "SELECT * From " . $wpdb->prefix . "permit_number WHERE permit_number='". $std_permit_number ."'"; 
			$alow_permit_number = $wpdb->get_row($get_permit);
			$table_name = $wpdb->prefix . "permit_number";  
			if(!empty($alow_permit_number)){
				$permit_id = $alow_permit_number->id;
				$updated1=$wpdb->update($table_name, 
					array( 
						'status' => '3',
					),
					array('id' => $permit_id)
				); 
			}else{
				$updated_oldpermit  = array(
                                'class_id'=>'class_13', 
                                'permit_number'=>$std_permit_number, 
                                'status'=>3, 
                                'createdtime'=>time(),
                                'assigntime'=>time(),
                            ); 
                $updated1 = $wpdb->insert($table_name,$updated_oldpermit);
			}
			if($updated1){	   
				// get max permit number usermeta table
				$queryassprt = "SELECT * FROM " . $wpdb->prefix . "permit_number WHERE class_id='class_12' AND status=0 ORDER BY permit_number ASC LIMIT 0,1";
				$perng = $wpdb->get_row($queryassprt);		
				$classrang= substr($perng->permit_number, 0, 3);
				$classprrang=$classrang.'%';  
				$classprrang=$classrang.'%';  
				if(empty($classrang)){
					$classprrang='12%'; 
				}
				// get max permit range
				$getpermit="SELECT max(meta_value) max_permit_number FROM " . $wpdb->prefix . "usermeta WHERE meta_key= 'permit_number' AND meta_value LIKE '".$classprrang."'";
				$permit_number = $wpdb->get_row($getpermit);
				$user_max_permit_range = $permit_number->max_permit_number; 

				//get permit number
				$permit_range = "SELECT * FROM " . $wpdb->prefix . "permit_number WHERE class_id='class_12' AND permit_number >" . $user_max_permit_range . " AND status = 0 ORDER BY permit_number ASC LIMIT 0,1";
				$permit_range_id = $wpdb->get_row($permit_range);  

				if(empty($permit_range_id)){
					//echo "Incress Class 12 permit range greater then " . $user_max_permit_range;
					array_push($responsedata,array('status'=>'Incress Class 12 permit range greater then ' . $user_max_permit_range,'userid'=>$userId));
					
				}else{ 	
					$permit_id = $permit_range_id->id;	  
					$permit_class = $permit_range_id->class_id;	  
					$student_permit = $permit_range_id->permit_number;
					$verifyrecd="SELECT * FROM " . $wpdb->prefix . "permit_number WHERE class_id='".$permit_class."' AND user_id=".$userId." AND status = 1";
					$verifyuserrecd = $wpdb->get_row($verifyrecd); 
					$table_name = $wpdb->prefix . "permit_number"; 
					
					if(empty($verifyuserrecd)){
						$updated=$wpdb->update($table_name, 
							array(
								'user_id' => $userId,
								'status' => '1',
								'assigntime' => time(),
							),
							array('id' => $permit_id)
						);
						
						if($updated){

							$permit_record  = array(
								'permit'=>$student_permit, 
								'userid'=>$userId, 
								'status'=>1, 
								'createdtime'=>time(),
							); 
							$per_rec = $wpdb->insert($permit_recordstb,$permit_record);	

							$insertpermit_number 	= update_user_meta( $userId, 'permit_number', $student_permit, $prev_value = '' );
							$deletepermit_no 		= delete_user_meta( $userId, 'permit_issued_option_no', $meta_value = '' );
							$deletepermit_missing 	= delete_user_meta( $userId, 'permit_issued_option_missing-info', $meta_value = '' );
							$deletepermit_yes 		= delete_user_meta( $userId, 'permit_issued_option_yes', $meta_value = '' );
							$updatepermit_yes 		= update_user_meta( $userId, 'permit_issued_option_yes', 'on', $prev_value = '' );

							//echo $status = 1;
							array_push($responsedata,array('userid'=>$userId,'status'=>1,'permit_number'=>$student_permit,'permit_status'=>'Yes'));
						}else {
							//echo $status = 0;
							array_push($responsedata,array('userid'=>$userId,'status'=>0));
						}

					}else{	
						array_push($responsedata,array('status'=>'pemit number alredy assign','userid'=>$userId));
					}
				}	  
		//--end		 
			}
			// echo $status = 1;
		} else {
			array_push($responsedata,array('userid'=>$userId,'status'=>0));
		}
	}
	echo json_encode($responsedata); 
}
if ($_POST['action'] == 'action_permit_assign_new') {
     require_once("../../../wp-config.php");
     global $wpdb;
	 $permit_recordstb = $wpdb->prefix . "user_permit_records";
	$userdata=$_POST['userId'];
	if(!is_array($userdata)){
		$userdata=[$userdata];
	}
	// echo "<pre>";
	//print_r($userdata);
	//die;
	$responsedata = array();
	foreach($userdata as $userId){

	
	 $student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='". $userId ."'");
	// die;
	 //An example date of birth.
	//$userDob = '2001-06-16';
	$userDob = $student_age->meta_value;
	$udob = date('Y-m-d',strtotime($userDob));
 
	//Create a DateTime object using the user's date of birth.
	 $dob = new DateTime($udob);

	//We need to compare the user's date of birth with today's date.
	$now = new DateTime();

	//Calculate the time difference between the two dates.
	$difference = $now->diff($dob);

	//Get the difference in years, as we are looking for the user's age.
	 $age = $difference->y;


	
	if($age <= 17){
		echo "Student is Under 18 years old"; 
	}else if($age >= 21){  
		//echo "class 12";
		 // get max permit number usermeta table
	 
        $queryassprt = "SELECT * FROM " . $wpdb->prefix . "permit_number WHERE class_id='class_12' AND status=0 ORDER BY permit_number ASC LIMIT 0,1";
        $perng = $wpdb->get_row($queryassprt);
		
         $classrang= substr($perng->permit_number, 0, 3);
		 $classprrang=$classrang.'%';   
		 if(empty($classrang)){
			$classprrang='12%';
		 } 
		// get max permit range
		$getpermit="SELECT max(meta_value) max_permit_number FROM " . $wpdb->prefix . "usermeta WHERE meta_key= 'permit_number' AND meta_value LIKE '".$classprrang."'";
		 $permit_number = $wpdb->get_row($getpermit);
		 $user_max_permit_range = $permit_number->max_permit_number; 

        //get permit number
		 $permit_range = "SELECT * FROM " . $wpdb->prefix . "permit_number WHERE class_id='class_12' AND permit_number >" . $user_max_permit_range . " AND status = 0 ORDER BY permit_number ASC LIMIT 0,1";
		$permit_range_id = $wpdb->get_row($permit_range);  
		 
		if(empty($permit_range_id)){
			//echo "Incress Class 12 permit range greater then " . $user_max_permit_range;
			array_push($responsedata,array('status'=>'Incress Class 12 permit range greater then '.$user_max_permit_range,'userid'=>$userId));

		}else{ 	
			$permit_id = $permit_range_id->id;	  
			$permit_class = $permit_range_id->class_id;	  
			$student_permit = $permit_range_id->permit_number;
			//$verifyrecd="SELECT * FROM " . $wpdb->prefix . "permit_number WHERE class_id='".$permit_class."' AND user_id=".$userId." AND status = 1";

			 $verifyrecd="SELECT pm.*,um.meta_value as assignpermit FROM " . $wpdb->prefix . "permit_number as pm LEFT JOIN " . $wpdb->prefix . "usermeta as um on um.user_id = pm.user_id and um.meta_key ='permit_number' WHERE pm.class_id='".$permit_class."' AND pm.user_id=".$userId." AND pm.status = 1";

			$verifyuserrecd = $wpdb->get_row($verifyrecd);
			$table_name = $wpdb->prefix . "permit_number";
			if(!empty($verifyuserrecd->assignpermit)){
				//echo "pemit number alredy assign";
				array_push($responsedata,array('status'=>'pemit number alredy assign','userid'=>$userId));

			}else{
				//echo "new";
			    //print_r($verifyuserrecd);
			    //die;
			    if(!empty($verifyuserrecd->permit_number)){
			    	$updatedoldpermit = $wpdb->update($table_name, 
					array(
						'status' => '2',
						'assigntime' => time(),
						),
					array('id' => $verifyuserrecd->id)
					);

			    } 		
				$addnewpermit=$wpdb->update($table_name, 
				array(
					'user_id' => $userId,
					'status' => '1',
					'assigntime' => time(),
					),
				array('id' => $permit_id)
				);
				if($addnewpermit){
					$permit_record  = array(
						'permit'=>$student_permit, 
						'userid'=>$userId, 
						'status'=>1, 
						'createdtime'=>time(),
					); 
					$per_rec = $wpdb->insert($permit_recordstb,$permit_record);	
					/* $table_usermeta = $wpdb->prefix . "usermeta";
					$my_data1 = array( 
						'user_id'=>$userId, 
						'meta_key'=>'permit_number', 
						'meta_value'=>$student_permit, 
					);
					$insertrecords = $wpdb->insert($table_usermeta,$my_data1); */
					$insertpermit_number 	= update_user_meta( $userId, 'permit_number', $student_permit, $prev_value = '' );
					$deletepermit_no 		= delete_user_meta( $userId, 'permit_issued_option_no', $meta_value = '' );
					$deletepermit_missing 	= delete_user_meta( $userId, 'permit_issued_option_missing-info', $meta_value = '' );
					$deletepermit_yes 		= delete_user_meta( $userId, 'permit_issued_option_yes', $meta_value = '' );
					$updatepermit_yes 		= update_user_meta( $userId, 'permit_issued_option_yes', 'on', $prev_value = '' );	
					
					if($insertpermit_number){ 
						//$status = 1;
						array_push($responsedata,array('userid'=>$userId,'status'=>1,'permit_number'=>$student_permit,'permit_status'=>'Yes'));
					}else {
						//$status = 0;
						array_push($responsedata,array('userid'=>$userId,'status'=>0));
					}
					//echo $status;
				}
			}  
		}	  
	}else if($age > 17 or $age < 21){
        $queryassprt = "SELECT * FROM " . $wpdb->prefix . "permit_number WHERE class_id='class_13' AND status=0 ORDER BY permit_number ASC LIMIT 0,1";
        $perng = $wpdb->get_row($queryassprt);		
		$classrang= substr($perng->permit_number, 0, 3);
		$classprrang=$classrang.'%';  
		if(empty($classrang)){
			$classprrang='13%';
		} 
		// get max permit range
		$getpermit="SELECT max(meta_value) max_permit_number FROM " . $wpdb->prefix . "usermeta WHERE meta_key= 'permit_number' AND meta_value LIKE '".$classprrang."'";
		$permit_number = $wpdb->get_row($getpermit);
		$user_max_permit_range = $permit_number->max_permit_number; 

		//get permit number
		$permit_range = "SELECT * FROM " . $wpdb->prefix . "permit_number WHERE class_id='class_13' AND permit_number >" . $user_max_permit_range . " AND status = 0 ORDER BY permit_number ASC LIMIT 0,1";
		$permit_range_id = $wpdb->get_row($permit_range);  
		
		if(empty($permit_range_id)){
			//echo "Incress Class 13 permit range greater then " . $user_max_permit_range;
			array_push($responsedata,array('status'=>'Incress Class 13 permit range greater then '.$user_max_permit_range,'userid'=>$userId));			
			}else{
				// print_r($permit_range_id);
				$permit_id = $permit_range_id->id;	 
				$permit_class = $permit_range_id->class_id;	  
				$student_permit = $permit_range_id->permit_number;
				$verifyrecd="SELECT * FROM " . $wpdb->prefix . "permit_number WHERE class_id='".$permit_class."' AND user_id=".$userId." AND status = 1";
				$verifyuserrecd = $wpdb->get_row($verifyrecd); 
				$table_name = $wpdb->prefix . "permit_number"; 
				
				//print_r($verifyuserrecd);
				
				//die;
				if(!empty($verifyuserrecd)){
				 // echo "pemit number alredy assign";
				  array_push($responsedata,array('status'=>'pemit number alredy assign','userid'=>$userId));
				}else{		
					$updated=$wpdb->update($table_name, 
					array( 
						'user_id' => $userId,
						'status' => '1',
						'assigntime' => time(),
					),
					array('id' => $permit_id));
					if($updated){	
						
						$permit_record  = array(
							'permit'=>$student_permit, 
							'userid'=>$userId, 
							'status'=>1, 
							'createdtime'=>time(),
						); 
						$per_rec = $wpdb->insert($permit_recordstb,$permit_record);
						
						$insertpermit_number 	= update_user_meta( $userId, 'permit_number', $student_permit, $prev_value = '' );
						$deletepermit_no 		= delete_user_meta( $userId, 'permit_issued_option_no', $meta_value = '' );
						$deletepermit_missing 	= delete_user_meta( $userId, 'permit_issued_option_missing-info', $meta_value = '' );
						$deletepermit_yes 		= delete_user_meta( $userId, 'permit_issued_option_yes', $meta_value = '' );
						$updatepermit_yes 		= update_user_meta( $userId, 'permit_issued_option_yes', 'on', $prev_value = '' );	
						if($insertpermit_number){ 
							//$status = 1;
							array_push($responsedata,array('userid'=>$userId,'status'=>1,'permit_number'=>$student_permit,'permit_status'=>'Yes'));
						}else {
							//$status = 0;
							array_push($responsedata,array('userid'=>$userId,'status'=>0));
						}
					}	
				} 	
			}	  	  
		} 
	}
	echo json_encode($responsedata);
}

?>