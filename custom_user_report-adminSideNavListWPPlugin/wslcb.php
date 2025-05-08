<?php function wslcbexport_new(){ 
	global $wpdb;
	$sql = 'SELECT u.*, um.meta_value as birthday FROM '. $wpdb->prefix .'users u JOIN '. $wpdb->prefix .'usermeta um ON u.ID = um.user_id AND um.meta_key LIKE "%date_of_birth%" WHERE u.ID >1 ORDER BY um.meta_value DESC';
    $users = $wpdb->get_results($sql);
	?>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">   

	<style>
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



	<div class="report-field row" style="margin-top:20px;">
	    <div class="col-md-2">
	        <div class='margin_top button mybtn'>  
	            <a href="#" id ="export" role='button'>WSLCB Export
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
	                                <option
									<?php if(!empty($_POST['permit'])&&$_POST['permit']=='1'){
										echo "selected"; }?>
									value="1"> Select One </option>
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
	                        <div class="margin_top">
	                            <input type="checkbox" <?php if(!empty($_POST['coursecmplts'])){echo "checked"; }?> value="1" name="coursecmplts"> Course Complete
								
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
	   	<table id="userreportable" class="table table-striped table-bordered" cellspacing="0" width="100%"> 
		  <thead>
	        <tr>
	            <th>First Name</th>  
	            <th>Middle Name</th> 
	            <th>Last Name</th>  
	            <th>Gender</th> 
	            <th>Social Security Number</th> 
	            <th>Height Feet</th> 
	            <th>Height Inches</th> 
	            <th>Weight</th> 
	            <th>Email</th>
	            <th>Date of Birth</th> 
	          	<!-- <th style="min-width:100px;"> Social Security Number</th> -->
				<th>Street Address</th> 
	            <th>City</th> 
	            <th>State</th> 			
	            <th>ZipCode</th> 			
	            <th>Trainer Number</th> 
				<th>Mast Permit Number</th> 
	            <th>Date of Class</th> 
	        </tr>
	      </thead>
	      <tbody>

	      	<?php

	      	if (isset($_POST['but_search'])) {

	      		$order = $_POST['order'];
			    if($order == ""){ $order = 1; }else{ $order = $_POST['order']; }
			    $fromDate = strtotime($_POST['date_to']);
			    $endDate = strtotime($_POST['date_from']);
			    if ($fromDate == "" OR $endDate == "") { $datefltr = 1; } else {
			        $datefltr = "activity_status=1 AND activity_completed between " . $fromDate . " AND " . $endDate; }
			    $permit = $_POST['permit'];
			    if ($permit == "") { $permit = 1; } else { $permit = $_POST['permit']; }
			    $coursecmplts = $_POST['coursecmplts'];
			    if (!empty($coursecmplts)) { $complete = "activity_status=1"; } else { $complete = 1; }
			    if ($permit == 1) {
			        $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
			        $newRecordss = $wpdb->get_results($rec_query);
			    } elseif ($permit == "permit_issued_option_yes" OR $permit == "permit_issued_option_no") {
			        $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz' AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
			        $newRecordss = $wpdb->get_results($rec_query);
			        foreach ($newRecordss as $key => $newRecordsss) {
			            $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE '" . $permit . "' AND user_id='" . $newRecordsss->user_id . "'";
			            $eRecordss = $wpdb->get_results($premQuer);
			            if (empty($eRecordss)) {
			                unset($newRecordss[$key]);
			            }
			        }
			    } elseif ($permit == "null") {
			        $rec_query1 = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz' AND " . $datefltr . " AND " . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
			        $newRecordss = $wpdb->get_results($rec_query1);
			        foreach ($newRecordss as $key => $newRecordsss) {
			            $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE 'permit_issued_option%'  AND user_id='" . $newRecordsss->user_id . "'";
			            $eRecordss = $wpdb->get_results($premQuer);
			            if (!empty($eRecordss)) {
			                unset($newRecordss[$key]);
			            }
			        }
			    }

				foreach ($newRecordss as $key => $d) {
				    $newsql = "SELECT * FROM `wp_users` u 
				               JOIN `wp_usermeta` um ON u.ID = um.user_id 
				               WHERE u.ID = {$d->user_id} 
				               AND um.`meta_key` LIKE '%date_of_birth%' 
				               ORDER BY um.meta_value ASC";

				    $newusers = $wpdb->get_results($newsql);
				    $newuser_meta = get_userdata($newusers[0]->ID);
				    $birth = $newuser_meta->date_of_birth;
				    $d->dob = $birth;
				    $d->dobs = strtotime($birth);
				}
				

				// Sorting by date of birth
				// if ($_POST['order'] == "desc") {
				//     usort($newRecordss, 'date_compare_desc');
				// } else {
				//     usort($newRecordss, 'date_compare_asc');
				// }

				foreach ($newRecordss as $key) {
					$userId = $key->user_id; 
				    $newuser_meta = get_userdata($key->user_id);
				    ?>

				    <tr>
				        <td><?= strtoupper($newuser_meta->first_name ?? '') ?></td>
				        <td>
				            <?php
				            if (!empty($newuser_meta->billing_wooccm14)) {
				                $mname = trim($newuser_meta->billing_wooccm14);
				                // echo strtoupper(substr($mname, 0, 1));
				                echo strtoupper($mname);
				            }
				            ?>
				        </td>
				        <td><?= strtoupper($newuser_meta->last_name ?? '') ?></td>
				        <td>
				            <?php
				            if (!empty($newuser_meta->user_gender)) {
				                $gdr = trim($newuser_meta->user_gender);
				                echo strtoupper(substr($gdr, 0, 1));
				            }
				            ?>
				        </td>
				        <td><?= preg_replace('/(?<=\d)--?(?=\d)/', '', $newuser_meta->social_security) ?? '' ?></td>

						<td><?= is_numeric($newuser_meta->height_ft_opt) ? round($newuser_meta->height_ft_opt) : $newuser_meta->height_ft_opt ?></td>
						<td><?= is_numeric($newuser_meta->height_in_opt) ? round($newuser_meta->height_in_opt) : $newuser_meta->height_in_opt ?></td>
						<td><?= is_numeric($newuser_meta->weight_lbs) ? round($newuser_meta->weight_lbs) : $newuser_meta->weight_lbs ?></td>
						<td><?= strtoupper($newuser_meta->user_email) ?></td>

						<td><?= date('d/m/Y', strtotime($newuser_meta->date_of_birth)) ?></td>
				        <td>
				            <?= $newuser_meta->shipping_address_1 ?? '' ?>
				            <?= !empty($newuser_meta->shipping_address_2) ? ',<br>' . $newuser_meta->shipping_address_2 : '' ?>
				        </td>
				        <td><?= strtoupper($newuser_meta->shipping_city ? str_replace(" ", "", $newuser_meta->shipping_city) : str_replace(" ", "", $newuser_meta->billing_city)) ?></td>
				        <td><?= strtoupper($newuser_meta->shipping_state ?? $newuser_meta->billing_state ?? '') ?></td>
				        <td><?= strtoupper($newuser_meta->shipping_postcode ?? $newuser_meta->billing_postcode ?? '') ?></td>
				        <td><?php echo 5465;  ?></td>
				        <td><?= $newuser_meta->permit_number ?? '' ?></td>
				        <?php  $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $userId . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$d->course_id."' ORDER BY activity_id DESC LIMIT  0,1";
							$corsCmplt = $wpdb->get_row($Qccmplt);
						?>
						<td><?php if(!empty($corsCmplt)){ echo date('d/m/Y', $corsCmplt->activity_completed);  } ?></td>

				    </tr>

				    <?php
				}


			} else{
				if($users){
					foreach($users as $user){
						$user_meta=get_userdata($user->ID);		
						$userId = $user->ID; 
						$querycourse ="SELECT * FROM wp_learndash_user_activity WHERE user_id='".$userId."' AND activity_type LIKE 'course'"; 
						$Courselist= $wpdb->get_results($querycourse); 
						foreach($Courselist as $Courselists){	
							$querycourse2 ="SELECT * FROM  wp_posts WHERE ID ='".$Courselists->post_id ."' AND post_type ='sfwd-courses'";  
							$CourseTitle= $wpdb->get_row($querycourse2);
							$course_id=$CourseTitle->ID;
						  	if(!empty($course_id)){ ?>
							  	<tr>
									<td><?php if(!empty($user_meta->first_name)){ echo strtoupper($user_meta->first_name);}?></td> 
									<td><?php if(!empty($user_meta->billing_wooccm14)){ 
												$mname = trim($user_meta->billing_wooccm14);
												// $fmnane =substr($mname,0,1);
												echo strtoupper($mname);} ?></td> 
												
									<td><?php if(!empty($user_meta->last_name)){ echo strtoupper($user_meta->last_name);} ?></td> 
									<td><?php if(!empty($user_meta->user_gender)){ 
												$gdr = trim($user_meta->user_gender," ");
												$gen =str_split($gdr); 
												echo $gen[0]; }?></td> 
									<td><?php if(!empty($user_meta->social_security)){echo preg_replace('/(?<=\d)--?(?=\d)/', '', $user_meta->social_security);}  ?></td>

									<td><?php if(gettype($user_meta->height_ft_opt)=="integer"){ echo round($user_meta->height_ft_opt);}else{ echo $user_meta->height_ft_opt;}?></td> 
									<td><?php if(gettype($user_meta->height_in_opt)=="integer"){ echo round($user_meta->height_in_opt);}else{ echo $user_meta->height_in_opt;} ?></td> 
									<td><?php if(gettype($user_meta->weight_lbs)=="integer"){ echo round($user_meta->weight_lbs);}else{ echo $user_meta->weight_lbs;} ?></td>
									<td><?php if(!empty($user->user_email)){ echo strtoupper($user->user_email); }?></td>
									<td><?php echo date('d/m/Y', strtotime($user_meta->date_of_birth)); ?></td>


									<td><?php if(!empty($user_meta->shipping_address_2)){echo $user_meta->shipping_address_1 .",<br>". $user_meta->shipping_address_2; }else{ echo $user_meta->shipping_address_1; } ?></td> 

									<td><?php if(!empty($user_meta->shipping_city)){echo strtoupper(str_replace(" ", "", $user_meta->shipping_city));}else{echo strtoupper(str_replace(" ", "", $user_meta->billing_city));} ?></td>

									<td><?php if(!empty($user_meta->shipping_state)){echo strtoupper($user_meta->shipping_state);}else{echo strtoupper($user_meta->billing_state);} ?></td>
									<td><?php if(!empty($user_meta->shipping_postcode)){echo strtoupper($user_meta->shipping_postcode);}else{echo strtoupper($user_meta->billing_postcode);} ?></td>
									<td><?php echo 5465;  ?></td>
									<td><?php if(!empty($user_meta->permit_number)) { echo $user_meta->permit_number; } ?></td> 
									<?php $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $userId . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='".$course_id."' ORDER BY activity_id DESC LIMIT  0,1";
										$corsCmplt = $wpdb->get_row($Qccmplt); ?>
									<td><?php if (!empty($corsCmplt)) { echo date('d/m/Y', $corsCmplt->activity_completed) . '<br>';  } ?></td>
								</tr>
								
								<?php	
							}
						}
					}
				}
			}?>
	      </tbody>
	    </table>
	</div>



	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"> </script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		$(document).ready(function() {
		    $('#userreportable').DataTable();
		} );
	</script>

	<script type='text/javascript'>
		
	    jQuery(document).ready(function () {
			jQuery(".clndr-date").click(function(){		 
				var datepickerFrom   =   document.getElementById("datepickerFrom").value;
				var datepickerTo     =   document.getElementById("datepickerTo").value;
				alert(datepickerFrom+'#'+datepickerTo);
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

	        $("#export").click(function (event) {
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
	             var outputFile= "1ALERT"+d.getFullYear()+mymonth+mydate;
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



<?php
} ?>

