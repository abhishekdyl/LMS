<?php
function course_completed_class_13(){
	$plugingpath = plugins_url() . "/permit_processor/permit_processor_ajax.php";	
	global $wpdb;
	$sql = 'SELECT * FROM ' . $wpdb->users . ' Where ID>1  ORDER BY ID DESC ';
	$users = $wpdb->get_results($sql);
	date_default_timezone_set( 'America/Los_Angeles' );
	date_default_timezone_get();
	?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">   
	<div class="report-field row" style="margin-top:20px;">
		<div class="col-md-2">
			<div class='margin_top button mybtn'>
				<a href="#" id ="export" role='button'>Permit Report
				</a>
			</div>
		</div>
		<form id="user_form_report" class="user_form_markbtn col-md-10" method="post">
			<div class="row">
				<div class="col-sm-9">
					<div class="row">
						<div class="col-md-3 ">
							<div class="wrapper_enrollment"><label class="enrol_field">From </label>
								<input id="datepickerFrom" class="enrol_input" name="date_to"  type="text" value="<?php if(!empty($_POST['date_to'])){echo $_POST['date_to']; }?>">
							</div>
						</div>	
						<div class="col-md-3 ">
							<div class="wrapper_enrollment"><label class="enrol_field">To </label>
								<input id="datepickerTo" class="enrol_input" name="date_from"  type="text" value="<?php if(!empty($_POST['date_from'])){echo $_POST['date_from']; }?>">
							</div>
						</div>	
						<div class="col-md-2 ">
							<div class="wrapper_enrollment"><label class="enrol_field">Permit Issue </label>
								<select  class="enrol_select_btn enrol_input" id="permit_issue" name="permit" > 
									<option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='1'){echo "selected"; }?>  value="1"> Select One </option>
									<option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='permit_issued_option_yes'){echo "selected"; }?> value="permit_issued_option_yes"> Yes </option>
									<option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='permit_issued_option_no'){echo "selected"; }?> value="permit_issued_option_no"> No </option> 
									<option <?php if(!empty($_POST['permit'])&&$_POST['permit']=='null'){echo "selected"; }?> value="null"> Null </option> 
								</select>
							</div>
						</div>
						<div class="col-md-2 ">
                            <div class="wrapper_enrollment"><label class="enrol_field">Order by age </label>
                                <select  class="enrol_select_btn enrol_input" id="order_issue" name="order" > 
                                    <option
                                    <?php if(!empty($_POST['order'])&&$_POST['order']=='1'){echo "selected"; }?>value=" "> Select One </option>
                                    <option <?php if(!empty($_POST['order'])&&$_POST['order']=='asc'){echo "selected"; }?> value="asc"> Ascending </option>
                                    <option <?php if(!empty($_POST['order'])&&$_POST['order']=='desc'){echo "selected"; }?> value="desc"> Descending </option> 
                                </select>
                            </div>
                        </div>	
						<div class="col-md-2 ">
							<div class="wrapper_enrollment"><label class="enrol_field">Class </label>
								<select  class="enrol_select_btn enrol_input" id="std_class" name="std_class" > 
                                <!--<option <?php if(!empty($_POST['std_class'])&&$_POST['std_class']=='1'){echo "selected"; }?>  value="1"> Select Class </option>
                                	<option <?php if(!empty($_POST['std_class'])&&$_POST['std_class']=='class_12'){echo "selected"; }?> value="class_12"> class 12 </option-->
                                		<option <?php if(!empty($_POST['std_class'])&&$_POST['std_class']=='class_13'){echo "selected"; }?> value="class_13"> class 13 </option>
                                		
                                	</select>
                                </div>
                            </div>				 
                        </div>
                    </div>
                    <div class="col-md-3 ">
                    	<!-- <p><button class="btn clndr-date" type="button">Search</button></p> -->
                    	<p><input class="but_search margin_top mybtn" type='submit' name='but_search' value='Search'></p>
                    </div>
                </div>								
            </form>	 
        </div>
        <!--  report-field row -->    	
        <div class="user_detail table table-responsive table1 table_responsive1" id="dvData">
        	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        	<link href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" rel="stylesheet">
        	<!-- <table class="table table-bordered table-striped"> -->
        		<h3>Course completed Student List</h3>
        		<h3 id='prod_cat_id'></h3>  
        		
        		<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%"> 
        			<thead>
        				<tr>
							<th><input type="checkbox" id="checkAll">All</th>
        					<th>S No.</th>
        					<th>Actions</th>
        					<th>License_no</th>
        					<th>Permit Issued</th>	
        					<th>Name</th>                                       
        					<th>Sex</th>                                       
        					<th>M</th>                    
        					<th>D</th>                    
        					<th>Y</th>                  
        					<th>Ht</th>                  
        					<th>Wt</th>                  
        					<th>Exp</th>
        					<th>Address</th>
        					<th>City</th>
        					<th>State</th>
        					<th>Postcode</th>
        					<th>Student Number</th>
        					<th>License Type</th> 
                    <!--th>Completion Date</th>
                    	<th>Assigned Date</th-->
                    		<!--th>Course Name</th-->
                    		
                    	</tr>
                    </thead> 
                    <?php

					function date_compare_asc($element1, $element2) {
						$datetime1 = strtotime($element1->dob);
						$datetime2 = strtotime($element2->dob);
						return ($datetime1) - ($datetime2);
					} 
				

					function date_compare_desc($element1, $element2) {
						$datetime1 = strtotime($element1->dob);
						$datetime2 = strtotime($element2->dob);
						return ($datetime2) - ($datetime1);
					} 

                    if (isset($_POST['but_search'])) {

						$order = $_POST['order'];
						if($order == ""){
							$order = 1;
						}else{
							$order = $_POST['order'];
						}					

                    	$complete = "activity_status=1";
                    	$fromDate = strtotime($_POST['date_to']);
                    	$endDate = strtotime($_POST['date_from']);
                    	if ($fromDate == "" OR $endDate == "") {
                    		$datefltr = 1;
                    	} else {
                    		$datefltr = "activity_status=1 AND activity_completed between " . $fromDate . " AND " . $endDate;
                    	}
                    	$permit = $_POST['permit'];
                    	if ($permit == "") {
                    		$permit = 1;
                    	} else {
                    		$permit = $_POST['permit'];
                    	}
                    	$std_class = $_POST['std_class'];
                    	if ($std_class == "") {
                    		$std_class = 1;
                    	} else {
                    		$std_class = $_POST['std_class'];
                    	}
                    	
   // $coursecmplts = $_POST['coursecmplts'];
   /*  if (!empty($coursecmplts)) {
        $complete = "activity_status=1";
    } else {
        $complete = 1;
    } */
    
/*     if ($permit == 1) {
        echo $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
        $newRecordss = $wpdb->get_results($rec_query);
    } */


    if ($permit == 1) {
    	$rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
    	$newRecordss = $wpdb->get_results($rec_query);
    	$newarray = array();
    	foreach ($newRecordss as $key => $newRecordsss) { 
    		if($std_class=='class_12'){ 
    			$student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='". $newRecordsss->user_id ."'");
    			$userDob = $student_age->meta_value;
    			$udob = date('Y-m-d',strtotime($userDob));
    			$dob = new DateTime($udob);
    			$now = new DateTime();
    			$difference = $now->diff($dob);
    			$age = $difference->y;
    			if($age >= 21){
    				$student_id = $student_age->user_id; 		
    				$premQuer = "SELECT * From wp_users WHERE ID ='" . $student_id . "'";			
    				$eRecordss = $wpdb->get_results($premQuer);             			
    				if (!empty($eRecordss)) {
    					$newarray[$key] = $newRecordsss;
    				} 			   
    			} 
    		}
    		else if($std_class=='class_13'){ 
    			$student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='". $newRecordsss->user_id ."'");
    			$userDob = $student_age->meta_value;
            //$dob = new DateTime($userDob);
    			$udob = date('Y-m-d',strtotime($userDob));
    			$dob = new DateTime($udob);
    			$now = new DateTime();
    			$difference = $now->diff($dob);
    			$age = $difference->y;
    			if($age < 21){ 
    				$student_id = $student_age->user_id;  		
    				$premQuer = "SELECT * From wp_users WHERE ID ='" . $student_id . "'";			
    				$eRecordss = $wpdb->get_results($premQuer);           			
    				if (!empty($eRecordss)) {
    					$newarray[$key] = $newRecordsss;
    				} 			   
    			}
    		}  
    	}
    	$newRecordss = $newarray;
    }

    elseif ($permit == "permit_issued_option_yes" OR $permit == "permit_issued_option_no") { 
    	
    	if($std_class==1){
    		$rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
    		$newRecordss = $wpdb->get_results($rec_query);
    		foreach ($newRecordss as $key => $newRecordsss) {
    			$premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE '" . $permit . "' AND user_id='" . $newRecordsss->user_id . "'";
    			$eRecordss = $wpdb->get_results($premQuer);
    			if (empty($eRecordss)) {
    				unset($newRecordss[$key]);
    			}
    		}
    	}
    	else if($std_class!=1){
    		$rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
    		$newRecordss = $wpdb->get_results($rec_query);
    		$newarray1 = array();
    		foreach ($newRecordss as $key => $newRecordsss) {		
    			$student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='". $newRecordsss->user_id ."'");
    			$userDob = $student_age->meta_value;           
    			$udob = date('Y-m-d',strtotime($userDob));
    			$dob = new DateTime($udob);
    			$now = new DateTime();
    			$difference = $now->diff($dob); 
    			$age = $difference->y;
    			if($std_class=='class_12'){ 
    				if($age >= 21){
    					$student_id = $student_age->user_id; 		
    					$premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE '" . $permit . "' AND user_id='".$student_id. "'";			
    					$eRecordss = $wpdb->get_results($premQuer);			 
    					if (!empty($eRecordss)) {
    						$newarray1[$key] = $newRecordsss;
    					} 			   
    				}	
    			}else if($std_class=='class_13'){
    				if($age < 21){
    					$student_id = $student_age->user_id; 		
    					$premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE '" . $permit . "' AND user_id='".$student_id. "'";			
    					$eRecordss = $wpdb->get_results($premQuer);			 
    					if (!empty($eRecordss)) {
    						$newarray1[$key] = $newRecordsss;
    					} 			   
    				}
    			}		  
    		}          
    		$newRecordss = $newarray1;
    	}  
    }else if($permit == "null") {
    	if($std_class==1){
    		$rec_query1 = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
    		$newRecordss = $wpdb->get_results($rec_query1);
    		foreach ($newRecordss as $key => $newRecordsss) {
    			$premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE 'permit_issued_option%'  AND user_id='" . $newRecordsss->user_id . "'";
    			$eRecordss = $wpdb->get_results($premQuer);
    			if (!empty($eRecordss)) {
    				unset($newRecordss[$key]); 
    			} 
    		}
    	}else if($std_class!=1){ 
    		$rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
    		$newRecordss = $wpdb->get_results($rec_query);
    		$newarray1 = array();
    		foreach ($newRecordss as $key => $newRecordsss) {		
    			$student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='". $newRecordsss->user_id ."'");
    			$userDob = $student_age->meta_value;
    			$udob = date('Y-m-d',strtotime($userDob));
    			$dob = new DateTime($udob);
    			$now = new DateTime();
    			$difference = $now->diff($dob); 
    			$age = $difference->y;
    			if($std_class=='class_12'){ 
    				if($age >= 21){ 
    					$student_id = $student_age->user_id; 					 			 
    					$premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE 'permit_issued_option%'  AND user_id='".$student_id. "'";
    					
    					$eRecordss = $wpdb->get_results($premQuer);			 
    					if (empty($eRecordss)) {
    						$newarray1[$key] = $newRecordsss;
    					} 			   
    				}	
    			}else if($std_class=='class_13'){
    				if($age < 21){
    					$student_id = $student_age->user_id; 					 			 
    					$premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE 'permit_issued_option%'  AND user_id='".$student_id. "'";
    					
    					$eRecordss = $wpdb->get_results($premQuer);			 
    					if (empty($eRecordss)) {
    						$newarray1[$key] = $newRecordsss;
    					}
    				}		  
    			} 
    		} 		
    		$newRecordss = $newarray1;
    	}	
		//die;       
    }


    $i = 1; 
    ?>
    <tbody> 
    	<?php
    	 foreach($newRecordss as $key => $d){
			//$QurcourseTitle = "SELECT * FROM wp_posts WHERE ID=$eRecords->course_id";
			//$getTitle = $wpdb->get_row($QurcourseTitle);
			
			$newsql = "SELECT * FROM `wp_users`u JOIN `wp_usermeta`um ON u.ID = um.user_id WHERE u.ID = ".$d->user_id." AND um.`meta_key` LIKE '%date_of_birth%' order by um.meta_value ASC";
			$newusers = $wpdb->get_results($newsql);
			$newuser_meta = get_userdata($newusers[0]->ID);  
			$birth = $newuser_meta->date_of_birth;
			$d->dob=$newuser_meta->date_of_birth;
			$d->dobs=strtotime($newuser_meta->date_of_birth);
		}
			if($_POST['order']=="desc"){
				usort($newRecordss, 'date_compare_desc');	
			}else{
				usort($newRecordss, 'date_compare_asc');			
			}
			
			// echo "<pre>";
			// print_r($newRecordss);
			// echo "</pre>";

			foreach ($newRecordss as $key) {
				$newuser_meta = get_userdata($key->user_id);  
                $userId = $key->user_id;  
    				?>
    				<tr>
						<td><input name="select_all[]" value="<?php echo $key->user_id;?>" class="assignpermit" type="checkbox" /></td>
    					<td><?php echo $i++;  ?></td> 
    					<?php //$userId = $key->user_id; 
    					$Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $userId . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$d->course_id."' ORDER BY activity_id DESC LIMIT  0,1";
    					$corsCmplt = $wpdb->get_row($Qccmplt);
    					?>
    					<td><?php $student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='". $key->user_id ."'");
    					$userDob = $student_age->meta_value;
    					$dob = new DateTime($userDob);
    					$dob = new DateTime($udob);
    					$now = new DateTime();
    					$difference = $now->diff($dob);
    					$age = $difference->y;
    					if($age >= 21 AND !empty($newuser_meta->permit_number)){
    						$mynumber = $newuser_meta->permit_number;
    						$get_pernumber = substr($mynumber, 0, 2); 
    						if($get_pernumber=='13'){
    							?>
    							<a class="btn-info btn-yellow" onclick="action_permit_reassign(<?php echo $userId; ?>)">Reassign</i></a>       
    							<?php
    						}else{ 
    							if(!empty($newuser_meta->permit_issued_option_yes)){
    								?>
    								<a class="btn-info btn-green">Assign</i></a>
    								<?php
    							}else if(!empty($newuser_meta->permit_issued_option_no)){
    								?>
    								<a class="btn-info btn-red" onclick="action_permit_unassign(<?php echo $userId; ?>)">Unassign</i></a>	
    								<?php 
    							}else{ ?>
    								<a class="btn-info btn-red" onclick="action_permit_unassign(<?php echo $userId; ?>)">Unassign</i></a> 
    							<?php }
    						}
    						
    					}else{
    						if(!empty($newuser_meta->permit_number))
    						{
    							if(!empty($newuser_meta->permit_issued_option_yes)){
    								?>
    								<a class="btn-info btn-green">Assign</i></a>
    								<?php
    							}else if(!empty($newuser_meta->permit_issued_option_no)){
    								?>
    								<a class="btn-info btn-red" onclick="action_permit_unassign(<?php echo $userId; ?>)">Unassign</i></a>	
    								<?php 
    							}else{ ?>
    								<a class="btn-info btn-red" onclick="action_permit_unassign(<?php echo $userId; ?>)">Unassign</i></a> 
    							<?php }  
    							?><?php
    						}else {  
    							?><a class="btn-info btn-blue" onclick="action_permit_assign_new(<?php echo $userId; ?>)">AssignNew</i></a><?php } } ?><div class="permitstatus"></div></td>
    							<td><?php if(!empty($newuser_meta->permit_number)){ echo $newuser_meta->permit_number;}?></td>

    							<td><?php if(!empty($newuser_meta->permit_issued_option_yes)){ echo "Yes"; } elseif(!empty($newuser_meta->permit_issued_option_no)) { echo "No";}?></td>

    							<td><?php echo strtoupper($newuser_meta->first_name .' '. $newuser_meta->last_name);?></td> 
    							<td><?php if(!empty($newuser_meta->user_gender)){ 
    								$gdr = trim($newuser_meta->user_gender," ");
    								$gen =str_split($gdr); 
    								echo $gen[0]; 
    							}?></td>
                                <?php if(!empty($newuser_meta->date_of_birth)){
                                        $dateValue = strtotime($newuser_meta->date_of_birth);
                                        echo "<td>".date("m",$dateValue)."</td>";
                                        echo "<td>".date("d",$dateValue)."</td>";
                                        echo "<td>".date("Y",$dateValue)."</td>";
                                    }else{
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                    }
                            ?>
                                    <td><?php 
                                    if(gettype($newuser_meta->height_ft_opt)=="integer"){
                                        $ht_ft = round($newuser_meta->height_ft_opt);
                                    }else{ 
                                        $ht_ft = $newuser_meta->height_ft_opt;
                                    }
                                    if(gettype($newuser_meta->height_in_opt)=="integer"){
                                         $ht_in = round($newuser_meta->height_in_opt);
                                     }else{
                                        $ht_in = $newuser_meta->height_in_opt;
                                    }
                                    echo $ht_ft . "'".$ht_in .'"';?>    
                                </td>
    							<td><?php if(gettype($newuser_meta->weight_lbs)=="integer"){ echo round($newuser_meta->weight_lbs);}else{ echo $newuser_meta->weight_lbs;} ?></td> 
    							<td><?php
    							$Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $newuser->ID . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$eRecords->course_id."' ORDER BY activity_id DESC LIMIT  0,1";
    							$corsCmplt = $wpdb->get_row($Qccmplt);	 		
    							if (!empty($corsCmplt)) { 
    								$quizcomplete = date('Y-m-d',$corsCmplt->activity_completed);
    								$date = strtotime($quizcomplete .'+61 month'); 
    								echo date('m/d/Y', $date);} ?></td>

    								<td><?php if(!empty($newuser_meta->billing_address_1)){ echo strtoupper($newuser_meta->billing_address_1);} if(!empty($newuser_meta->billing_address_2)){
    									echo strtoupper($newuser_meta->billing_address_1 .','. $newuser_meta->billing_address_2);} ?></td>
    									<td><?php if(!empty($newuser_meta->billing_city)){echo strtoupper($newuser_meta->billing_city);} ?></td>
    									<td><?php if(!empty($newuser_meta->billing_state)){echo strtoupper($newuser_meta->billing_state);} ?></td>
    									<td><?php if(!empty($newuser_meta->billing_postcode)){echo $newuser_meta->billing_postcode;} ?></td>		
    									<td><?php echo strtoupper($newuser->ID);?></td>
    									<td><?php $userDob1 = $newuser_meta->date_of_birth;
    									$udob = date('Y-m-d',strtotime($userDob1));
    									$dob1 = new DateTime($udob);
    									$now1 = new DateTime();
    									$difference1 = $now1->diff($dob1);
    									$age1 = $difference1->y;
    									if($age1 < 17){ 
    										echo "Under age"; 
    									}
    									else if($age1 > 21){ 
    										echo "Class 12";
    									}else if($age1 > 17 or $age1 < 21){
    										echo "Class 13";
    									} 
    									?></td>


    								</tr>

								<?php }?> 
    							</tbody> 

    							<?php 
    						} else{ 
    							$rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz' AND activity_status=1 GROUP BY user_id ORDER BY activity_id DESC";
    							$newRecordss = $wpdb->get_results($rec_query);				
    							$i = 1;

    							foreach ($newRecordss as $eRecords) {
    								$QurcourseTitle = "SELECT * FROM wp_posts WHERE ID=$eRecords->course_id";
    								$getTitle = $wpdb->get_row($QurcourseTitle);
    								if ($getTitle) {
    									$eRecords->user_id;
    									$newsql = "SELECT * FROM wp_users WHERE ID=$eRecords->user_id AND ID>1";
    									$newusers = $wpdb->get_results($newsql);
    									
    									foreach ($newusers as $newuser) {
    										$newuser_meta = get_userdata($newuser->ID);
    										$student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='". $newuser->ID ."'");
    										$userDob = $student_age->meta_value;
    										$udob = date('Y-m-d',strtotime($userDob));
    										$dob = new DateTime($udob);
    										$now = new DateTime();
    										$difference = $now->diff($dob);
    										$age = $difference->y; 
    										if($age < 21){						
    											?>
    											<tr>
												<td><input name="select_all[]" value="<?php echo $newuser->ID;?>" class="assignpermit" type="checkbox" /></td>
    												<td><?php echo $i++; ?>  
    											</td>
    											<td><?php 
    											$student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='". $newuser->ID ."'");
    											$userDob = $student_age->meta_value;
    											$udob = date('Y-m-d',strtotime($userDob));
    											$dob = new DateTime($udob);
    											$now = new DateTime();
    											$difference = $now->diff($dob); 
    											$age = $difference->y; 
    											if($age >= 21 AND !empty($newuser_meta->permit_number)){
    												$mynumber = $newuser_meta->permit_number;
    												
    												$get_pernumber = substr($mynumber, 0, 2); 
    												if($get_pernumber=='13'){
    													?>
    													<a class="btn-info btn-yellow" onclick="action_permit_reassign(<?php echo $newuser->ID; ?>)">Reassign</i></a>     
    													<?php
    												}else{ 
    													if(!empty($newuser_meta->permit_issued_option_yes)){
    														?>
    														<a class="btn-info btn-green">Assign</i></a>
    														<?php
    													}else if(!empty($newuser_meta->permit_issued_option_no)){
    														?>
    														<a class="btn-info btn-red" onclick="action_permit_unassign(<?php echo $newuser->ID; ?>)">Unassign</i></a>	
    														<?php 
    													}else{ ?>
    														<a class="btn-info btn-red" onclick="action_permit_unassign(<?php echo $newuser->ID; ?>)">Unassign</i></a> 
    													<?php }
    												}			
    											}else{ 
    												
    												if(!empty($newuser_meta->permit_number))
    												{
    													if(!empty($newuser_meta->permit_issued_option_yes)){
    														?>
    														<a class="btn-info btn-green">Assign</i></a>
    														<?php
    													}else if(!empty($newuser_meta->permit_issued_option_no)){
    														?>
    														<a class="btn-info btn-red" onclick="action_permit_unassign(<?php echo $newuser->ID; ?>)">Unassign</i></a>	
    														<?php 
    													}else{ ?>
    														<a class="btn-info btn-red" onclick="action_permit_unassign(<?php echo $newuser->ID; ?>)">Unassign</i></a> 
    													<?php }  
    													?><?php
    												}else {  
    													?><a class="btn-info btn-blue" onclick="action_permit_assign_new(<?php echo $newuser->ID; ?>)">AssignNew</i></a><?php } }?><div class="permitstatus"></div></td>
    													
    													<td><?php if(!empty($newuser_meta->permit_number)){ echo $newuser_meta->permit_number;} ?></td>	
    													<td><?php if(!empty($newuser_meta->permit_issued_option_yes)){ echo "Yes"; } elseif(!empty($newuser_meta->permit_issued_option_no)) { echo "No";}?></td>
    													<td><?php echo strtoupper($newuser_meta->first_name ." " . $newuser_meta->last_name); ?></td>
    													<td><?php if(!empty($newuser_meta->user_gender)){ 
    														$gdr = trim($newuser_meta->user_gender," ");
    														$gen =str_split($gdr); 
    														echo $gen[0]; 
    													}?></td> 
                                                        <?php if(!empty($newuser_meta->date_of_birth)){
                                                                    $dateValue = strtotime($newuser_meta->date_of_birth);
                                                                    echo "<td>".date("m",$dateValue)."</td>";
                                                                    echo "<td>".date("d",$dateValue)."</td>";
                                                                    echo "<td>".date("Y",$dateValue)."</td>";
                                                                }else{
                                                                    echo "<td></td>";
                                                                    echo "<td></td>";
                                                                    echo "<td></td>";
                                                                }
                                                        ?>
    													<td><?php 
                                                       // $ht_ft = round($newuser_meta->height_ft_opt);
    													//$ht_in = round($newuser_meta->height_in_opt);
                                                        if(gettype($newuser_meta->height_ft_opt)=="integer"){
                                                            $ht_ft = round($newuser_meta->height_ft_opt);
                                                        }else{ 
                                                            $ht_ft = $newuser_meta->height_ft_opt;
                                                        }
                                                        if(gettype($newuser_meta->height_in_opt)=="integer"){
                                                             $ht_in = round($newuser_meta->height_in_opt);
                                                         }else{
                                                            $ht_in = $newuser_meta->height_in_opt;
                                                        }
    													echo $ht_ft . "'".$ht_in .'"'; ?></td>
    													
    													<td><?php if(gettype($newuser_meta->weight_lbs)=="integer"){ echo round($newuser_meta->weight_lbs);}else{ echo $newuser_meta->weight_lbs;} ?></td> 			
    													<td><?php
    													$Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $newuser->ID . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$eRecords->course_id."' ORDER BY activity_id DESC LIMIT  0,1";
    													$corsCmplt = $wpdb->get_row($Qccmplt);	 		
    													if (!empty($corsCmplt)) { 
    														$quizcomplete = date('Y-m-d',$corsCmplt->activity_completed);
    														$date = strtotime($quizcomplete .'+61 month'); 
    														echo date('m/d/Y', $date);} ?></td>
    														
    														<td><?php if(!empty($newuser_meta->billing_address_1)){ echo strtoupper($newuser_meta->billing_address_1);} if(!empty($newuser_meta->billing_address_2)){
    															echo strtoupper($newuser_meta->billing_address_1 .','. $newuser_meta->billing_address_2);} ?></td> 
    															<td><?php if(!empty($newuser_meta->billing_city)){echo strtoupper($newuser_meta->billing_city);} ?></td>
    															<td><?php if(!empty($newuser_meta->billing_state)){echo strtoupper($newuser_meta->billing_state);} ?></td>
    															<td><?php if(!empty($newuser_meta->billing_postcode)){echo $newuser_meta->billing_postcode;} ?></td>		
    															<td><?php echo strtoupper($newuser->ID);?></td>
    															<td><?php $userDob1 = $newuser_meta->date_of_birth;
    															$udob = date('Y-m-d',strtotime($userDob1));
    															$dob1 = new DateTime($udob);
    															$now1 = new DateTime();
    															$difference1 = $now1->diff($dob1);
    															$age1 = $difference1->y;
    															if($age1 < 17){ 
    																echo "Under age"; 
    															}
    															else if($age1 > 21){ 
    																echo "Class 12";
    															}else if($age1 > 17 or $age1 < 21){
    																echo "Class 13";
    															} 
    															?></td> 
	  <!--td><?php echo "completed";  ?></td>   
	  	<td><?php
	  	$userId = $newuser->ID;
	  	$queryenrol ="SELECT * FROM wp_learndash_user_activity WHERE user_id='".$userId."' AND activity_type LIKE 'topic' AND `activity_status`=1  AND course_id ='".$eRecords->course_id."' ORDER BY `activity_id` ASC LIMIT 0,1";
	  	$enrollist= $wpdb->get_row($queryenrol); 
	  	if(!empty($enrollist)){
	  		echo date('m/d/Y H:i',$enrollist->activity_started);
	  	} ?></td-->      	  
	  	<!--td><?php  echo strtoupper($getTitle->post_title);  ?></td--> 					
	  </tr>
	<?php } } } } } ?>    
</table>
</div>
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"> </script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script>
$("#checkAll").click(function () {
	 $('input:checkbox').not(this).prop('checked', this.checked);
 });
function action_permit_unassign(id){
    //alert(id);
    jQuery.ajax({
    	type: "POST",
    	url: '<?php echo $plugingpath; ?>',
    	data: {
    		action: 'action_permit_unassign', 
    		userId: id
    	},
        success: function (response) {
            var data=JSON.parse(response);
            data.forEach(function(ele,index){
                if(ele.status==1){
                    $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('a').removeClass('btn-red').addClass('btn-blue').text('AssignNew').attr('onclick',`action_permit_assign_new(${ele.userid})`);
                    $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(3).text(ele.permit_number);
                }else{
                 document.getElementById('prod_cat_id').innerHTML="records not updated"; 
                }
                       // console.log('element',ele);
            });
                    //console.log('data :',data);
        }
    });
}
function action_permit_assign_new(id) {
    jQuery.ajax({
        type: "POST",
        url: '<?php echo $plugingpath; ?>',
        data: {
            action: 'action_permit_assign_new', 
            userId: id
        },
        success: function (response) {
            var data = JSON.parse(response);
            data.forEach(function(ele,index){
                $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(2).find(".permitstatus").html("");
                if(ele.status==1){
                    $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('a').removeClass('btn-blue').addClass('btn-green').text('Assign');
                    $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(3).text(ele.permit_number);
                    $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(4).text(ele.permit_status);
                }else if(data==0){
                    document.getElementById('prod_cat_id').innerHTML="permit number not issue";
                }else{
                    $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(2).find(".permitstatus").html(ele.status);
                }
            });
            //console.log('data :',data);
        } 
    });
}
$(document).ready(function() {
	$('#example').DataTable( {
          "columnDefs": [ {
                    'targets': [0], 
                    'orderable': false
                } ], 
		// add buttom
		  dom: 'Blfrtip',
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		buttons: [
            {
                text: 'AssignNew',
                className:'assign-new',
                action: function ( e, dt, node, config ) {
                    var arr=new Array();
                    $('body #example tbody').find('input[type="checkbox"]').each(function(){
                        if($(this).is(":checked")){
							if($(this).closest('tr').find('.btn-blue').hasClass('btn-blue')){
								arr.push(parseInt($(this).val()));
							}
                            
                        }
						
                       // console.log('checkbox :',$(this));
                    });
					//action_permit_assign_new
					if(arr.length){
						action_permit_assign_new(arr);
					}else{
						alert("Please atleast select one");
					}
                    //console.log('arrr',arr);
                    //alert( 'Button activated' );
                }
            },
            {
                text: 'Unassign', 
                className:'unassign-new',
                action: function ( e, dt, node, config ) {
                    var arr=new Array();
                    $('body #example tbody').find('input[type="checkbox"]').each(function(){
                        if($(this).is(":checked")){
							if($(this).closest('tr').find('.btn-red').hasClass('btn-red')){
								arr.push(parseInt($(this).val()));								
							}
                        }
                       // console.log('checkbox :',$(this));
                    });
					//action_permit_unassign
					if(arr.length){
						action_permit_unassign(arr);
						
					}else{
						alert("Please Select Atleast On Unassign Records");
					}
                    //console.log('arrrkkkkk11vv',arr);
                }
            },
        ],
		//end
		
	} );
} );

</script>
<script type='text/javascript'>
	
	jQuery(document).ready(function () {
		jQuery(".clndr-date").click(function(){		 
		//alert('hiiii');
		var datepickerFrom   =   document.getElementById("datepickerFrom").value;
		var datepickerTo     =   document.getElementById("datepickerTo").value;
		//alert(datepickerFrom+'#'+datepickerTo);
		
	});
		
		jQuery("#datepickerTo" ).datepicker({
			changeMonth: true,
			changeYear: true
		});
		
		jQuery("#datepickerFrom" ).datepicker({
			changeMonth: true,
			changeYear: true
		});			
		
            //console.log("HELLO")
            function exportTableToCSV($table, filename) {
            	var $headers = $table.find('tr:has(th)')
            	,$rows = $table.find('tr:has(td)')

                    // Temporary delimiter characters unlikely to be typed by keyboard
                    // This is to avoid accidentally splitting the actual contents
                    ,tmpColDelim = String.fromCharCode(11) // vertical tab character
                    ,tmpRowDelim = String.fromCharCode(0) // null character

                    // actual delimiter characters for CSV format
                    ,colDelim = '","'
                    ,rowDelim = '"\r\n"';

                    // Grab text from table into CSV formatted string
                    var csv = '"';
                    csv += formatRows($headers.map(grabRow));
                    csv += rowDelim;
                    csv += formatRows($rows.map(grabRow)) + '"';

                    // Data URI
                    var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

                    $(this)
                    .attr({
                    	'download': filename
                    	,'href': csvData
                        //,'target' : '_blank' //if you want it to open in a new window
                    });

                //------------------------------------------------------------
                // Helper Functions 
                //------------------------------------------------------------
                // Format the output so it has the appropriate delimiters
                function formatRows(rows){
                	return rows.get().join(tmpRowDelim)
                	.split(tmpRowDelim).join(rowDelim)
                	.split(tmpColDelim).join(colDelim);
                }
                // Grab and format a row from the table
                function grabRow(i,row){
                	
                	var $row = $(row);
                    //for some reason $cols = $row.find('td') || $row.find('th') won't work...
                    var $cols = $row.find('td'); 
                    if(!$cols.length) $cols = $row.find('th');  

                    return $cols.map(grabCol)
                    .get().join(tmpColDelim);
                }
                // Grab and format a column from the table 
                function grabCol(j,col){
                	var $col = $(col),
                	$text = $col.text();

                    return $text.replace('"', '""'); // escape double quotes
                    

                }
            }


            // This must be a hyperlink
            
            $("#export").click(function (event) {
                // var outputFile = 'export'
//                var outputFile = window.prompt("What do you want to name your output file") || 'export';
var d = new Date();
var mymonth= d.getMonth()+1;
if(mymonth < 10){
	mymonth = "0"+mymonth;
}
var mydate = d.getDate();
if(mydate <10){
	mydate = "0" + mydate;
}
				 //1ALERT20200225
                //yyyymmdd
                var outputFile= "permit_process"+d.getFullYear()+mymonth+mydate;
                outputFile = outputFile.replace('.csv','') + '.csv'
                
                // CSV
                //exportTableToCSV.apply(this, [$('#dvData>table'), outputFile]);
                //exportTableToCSV.apply(this, [$('#dvData>table'), outputFile]);
                exportTableToCSV.apply(this, [$('#dvData .dataTable'), outputFile]);				
                // IF CSV, don't do event.preventDefault() or return false
                // We actually need this to be a typical hyperlink  
            });
        });


    </script>	 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" rel="stylesheet">

    <style>
	#wpfooter p {
    display: none;
}
	.permitstatus {
    color: #b72b38;
}
	.assign-new,
        .unassign-new,
        .reassign-new {
            padding: 10px !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            width: 100px !important;
            text-align: center !important;
            color: #fff !important;
        }

        .assign-new{
            background-color: #5bc0de !important;
            border-color: #46b8da !important;
        }

        .unassign-new{
                background-color: #bd2130 !important;
                border-color: #b21f2d !important;
        }

        .reassign-new {
                background-color: #d39e00 !important;
                border-color: #c69500 !important;
        }


        .reassign-new:hover {
            background-color: #5bc0de !important;
            border-color: #46b8da !important;
        }
    	#prod_cat_id {
    		/*  color: #eb1111; */
    		font-size: 16px;
    		font-weight: bold;
    		margin: 10px 0 10px 0;
    		width: 50%;
    		/*  background: #ff000017; */
    		padding: 5px 0px 5px 5px;
    	}
    	#msg2{
    		color:#dc3545
    	}
    	#msg1{
    		color:#dc3545
    	}
    	.page_height{
    		margin-bottom: 10px;
    	}
    	.dbfield{
    		padding:6px 10px;
    		box-shadow: 0 0 5px 0 #ddd;
    		border: 1px solid #ddd;
    		color: #333;
    		font-size: 14px;
    	}
    	form#testimonal {
    		padding-top: 20px;
    	}
    	i.fa.fa-eye.btn-success {
    		padding: 10px;
    		border-radius: 4px;
    		cursor: pointer;
    	}
    	i.fa.fa-eye-slash.btn-danger {
    		padding: 10px;
    		border-radius: 4px;
    		cursor: pointer;
    	}
    	a.btn-gray {
    		padding: 10px;
    		background-color: #f5902b;
    		border-radius: 4px;
    		cursor: pointer;
    	}
    	a.btn-info {
    		padding: 10px;
    		border-radius: 4px;
    		cursor: pointer;
    		width: 100px;
    		display: block;
    		text-align: center;
    	}
    	.btn-yellow{
    		background-color: #d39e00;
    		border-color: #c69500;
    	}
    	.btn-red{
    		background-color: #bd2130;
    		border-color: #b21f2d;
    	}
    	.btn-green{
    		background-color: #5a6268;
    		border-color: #545b62;
    	}
    	select.form_width {
    		max-width: 100%;
    	}
    	.ui-datepicker td .ui-state-default {
    		padding: 4px 6px !important;
    	}
    	.table_responsive1{
    		border: 1px solid #ddd; 
    		margin-top: 20px;
    	}	     
    	.btncsv{
    		width: 100px;
    		height: 30px;
    		margin: 15px 0 0 0;
    		text-align: center;
    		line-height: 30px;
    		transition: 0.5s;
    	}
    	.btncsv:hover{
    		background:#fff !important;
    		color:#555 !important;
    	}
    	
    	.ui-datepicker select.ui-datepicker-month, .ui-datepicker select.ui-datepicker-year {
    		color: #000 !important;
    	}
    	.row {
    		width: 100%;
    		margin: 0 auto;
    	}
    	.block {
    		width: 300px;
    		display: inline-block;
    	}
    	.but_search{	
    		width: 150px;
    		border-radius: 3px;
    		background: #ffb900;}
    		.button{background: #fff;
    			border: 1px solid #e4081233;}  

    			.ui-datepicker td.ui-datepicker-week-end {
    				background-color: #fff !important;
    				border: 1px solid #fff !important;
    			} 	
    			.ui-datepicker td {
    				border: 0 !important;
    				padding: 0 !important; 
    			}

    			.enrol_select_btn{
    				display:block;
    			}
    			.margin_top{
    				margin-top:27px !important;
    				display:inline-block;
    			}
    			.mybtn {
    				border: 2px solid #f9bb00 !important;
    				background:#f9bb00 !important ;
    				padding:5px !important;
    				border:none !important;
    				font-size: 15px !important;
    				text-align:center;
    				color:#fff;
    				line-height:unset !important;
    				min-height:unset !important ; 
    				
    			}
    			.mybtn a{
    				color:#fff !important;
    			}
    			.wrapper_enrollment input{
    				width:100%; 
    			} 
    			a.paginate_button { 
    				padding: 5px 8px;
    				border: 1px solid #337ab7;
    				margin-left: -1px;
    				cursor:pointer;
    			}

    			a.paginate_button.current { 
    				color: #fff;
    				background: #337ab7; 
    			} 
    		</style>	
    	</div>


    	
    <?php  }