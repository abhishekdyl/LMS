<?php
global $wpdb;
$current_user = wp_get_current_user();
$ajfile = plugins_url().'/sync-course/ajax/add_child_ajax.php';
$parenturs = $current_user;
$members_info = $wpdb->prefix."members_info";
$query = $wpdb->prepare("SELECT * FROM $members_info WHERE user_id=%s;", $current_user->ID);
$row = $wpdb->get_results( $query );

get_header();
?>


<br><br><br><br><br>

<div class="container">
	<table>

	    <tr>
	        <th>Member Type</th>
	        <th>Payment Type</th>
	        <th>Member Count</th>
	        <th>Enroll Date</th>
	    </tr>
	   <?php  foreach($row as $data) { ?>
	    <tr>
	        <td><?php  echo $data->members_type; ?></td>
	        <td><?php  echo $data->payment_type; ?></td>
	        <td><?php  echo $data->member_count; ?></td>
	        <td><?php  echo date("d-m-Y",$data->updated_date); ?></td>
	    </tr>

	<?php }
	// echo "<pre>";
	// print_r($parenturs->ID);
	?>

	</table>

	<br><br>

	<table>
		<thead>
		    <tr>
		        <th>Username</th>
		        <th>First Name</th>
		        <th>Last Name</th>
		        <th>Email</th>
		    </tr>
		</thead>
	<?php
	$totalmem = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "members_info WHERE `user_id` = ".$parenturs->ID."");
	$childcount = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "usermeta umeta INNER JOIN " . $wpdb->prefix . "users u WHERE umeta.user_id = u.id AND umeta.meta_key LIKE '%parent_id%' AND umeta.meta_value = '".$parenturs->ID."'");

	// echo "<pre>";
	// print_r($disname);
	// echo "</pre>"
	echo'<tbody class="user_list">';
	foreach ($childcount as $key => $muser) {
		$disname = explode(" ", $muser->display_name);
	;
		$fname = $disname[0];
		$lname = $disname[1];
	echo '<tr>
			<td>'.$muser->user_login.'</td>
			<td>'.$fname.'</td>
			<td>'.$lname.'</td>
			<td>'.$muser->user_email.'</td>	
		</tr>';
	}
	echo'</tbody>
	</table>
	<div class="err_msg"></div>';


	if($totalmem->member_count > count($childcount)){
		echo '<button type="button" id="addusr" onclick="AddUser()">Add User +</button>';
	}	
	?>

</div>
<br><br>


<div id="add-user" style="display:none;">
<div class="container">
	 <form id="adchildfrm" class="user_form_markbtn" method="post" enctype="multipart/form-data">

		<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
			<label for="first_name">Firstname<span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="first_name" id="account_first_name"/>
		</p>

	    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
			<label for="last_name">Lastname<span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="last_name" id="account_last_name" />
		</p>


	    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
			<label for="username">Username<span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="account_username"/>
		</p>


	    <!-- <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
			<label for="email">Email<span class="required">*</span></label>
			<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="account_email"/>
		</p> -->


	    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
			<label for="password">Password<span class="required">*</span></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="account_password" />
		</p>


	    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
			<label for="cpassword">Confirm Password<span class="required">*</span></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="cpassword" id="account_crmpassword"/>
		</p>

		<p>
			<input type="hidden" name="parent" id="parentid" value="<?php echo $current_user->ID; ?> " />
			<input type="hidden" name="action" id="ajexfile" value=" <?php echo $ajfile; ?> " />
			<button type="button" id="submit"  name="submit">Saves</button>
		</p>

	</form>
</div>
</div>

	<?php get_footer(); ?>

<script>

	jQuery(document).ready(function(){
		jQuery("#submit").click(function(){ 
			var filepath = jQuery('#ajexfile').val();
			var firstpas = jQuery('#account_password').val();
			var lastpas = jQuery('#account_crmpassword').val();
			if(firstpas == lastpas){
				var formdata = {
					parentid: jQuery('#parentid').val(),
					username: jQuery('#account_username').val(),
					fname: jQuery('#account_first_name').val(),
					lname: jQuery('#account_last_name').val(),
					email: jQuery('#account_email').val(),
					password: jQuery('#account_password').val(),
				} 

				jQuery.ajax({
					type:'POST',						
					url: filepath,								
					data: formdata,						
					success: function(response){
						console.log('aaaaaaa',response);
						var res = JSON.parse(response);
						console.log('bbbbb',res);
						if(res.status == true){
							jQuery('.user_list').append('<tr><td>'+res.data.username+'</td><td>'+res.data.fname+'</td><td>'+res.data.lname+'</td><td>'+res.data.email+'</td></tr>');
							jQuery(".err_msg").html(res.msg).show().delay(5000).queue(function(n) {
								jQuery(this).hide(); n();
							});
							jQuery('#add-user').hide();
							jQuery("form")[0].reset();
						}
						if(res.status == false){
							jQuery(res.data).each(function(index , data) {
								jQuery(".err_msg").html(data.error).show().delay(5000).queue(function(n) {
									jQuery(this).hide(); n();
								});
							});
							jQuery('#add-user').hide();
							jQuery("form")[0].reset();
						}
						if(res.limit){
							jQuery('#addusr').hide();
							jQuery(".err_msg").html(res.msg);
							jQuery("form")[0].reset();
						}
					}						
				});

			//   console.log("Submitted",formdata);
			//   alert(filepath);
			}else{
			  alert('Both Password should be same..');
			}
		});
	});

    function AddUser(){
		var add_user = document.getElementById("add-user");

		if(add_user.style.display=="none"){
			add_user.style.display = "block";
		}
		else{
			add_user.style.display = "none";
		}
	}

</script>

