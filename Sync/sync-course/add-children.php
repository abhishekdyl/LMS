<?php
function QuadLayers_add_student_content()
{
	global $wpdb;
	$current_user = wp_get_current_user();
	if ($current_user->roles[0] == 'parent' OR $current_user->roles[0] == 'administrator') {
		$ajfile = plugins_url() . '/sync-course/ajax/add_child_ajax.php';
		$editajax = plugins_url() . '/sync-course/ajax/edit_child_ajax.php';

		if(!empty($_REQUEST['parentid'])){
			$parenturs = get_userdata($_REQUEST['parentid']);
		}else{
			$parenturs =  wp_get_current_user();
		}
		$members_info = $wpdb->prefix . "members_info";
		?>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

		<div class="container">
			<div class="parent_data">
				<table>
					<tr>
						<th>Parent Name</th>
						<th>Subscription Type</th>
						<th>Total Member</th>
						<th>Payment Status</th>
						<th>Enroll Date</th>
					</tr>
					<?php 
					$queryget = "SELECT mif.*,u.user_email,u.display_name FROM " . $wpdb->prefix . "members_info as mif INNER JOIN " .$wpdb->prefix. "users as u ON u.ID = mif.user_id WHERE u.ID = '".$parenturs->ID."'"; 
					$data = $wpdb->get_row($queryget);
						if($data->payment_type==0){
	                            $subtype = "Yearly";
	                        }else if($data->payment_type==1){
	                            $subtype = "monthly";
	                        }
	                        if($data->status==0){
	                            $paystatus = "Not Completed";
	                        }else if($data->status==1){
	                            $paystatus = "Completed";
	                        }
						?>
						<tr>
							<td><?php echo $data->display_name; ?></td>
							<td><?php echo $subtype; ?></td>
							<td><?php echo $data->member_count; ?></td>
							<td><?php echo $paystatus; ?></td>
							<td><?php echo date("d-m-Y", $data->updated_date); ?></td>
						</tr>
				</table>
			</div>
				<br><br>
			<?php if($data->status==1){ ?>	
			<div>
				<table>
					<thead>
						<tr>
							<th>Username</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Email</th>
							<th>Action</th>
						</tr>
					</thead>
					<?php
					$totalmem = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "members_info WHERE `user_id` = " . $parenturs->ID . "");
					$childcount = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "usermeta umeta INNER JOIN " . $wpdb->prefix . "users u WHERE umeta.user_id = u.id AND umeta.meta_key LIKE '%parent_id%' AND umeta.meta_value = '" . $parenturs->ID . "'");
					echo '<tbody class="user_list">';
					foreach ($childcount as $key => $muser) {
						$disname = explode(" ", $muser->display_name);
						$fname = $disname[0];
						$lname = $disname[1];
						echo '<tr>
						<td>' . $muser->user_login . '</td>
						<td>' . $fname . '</td>
						<td>' . $lname . '</td>
						<td>' . $muser->user_email . '</td>	
						<td><a class="edit-parent" onclick="edit_childuser('.$muser->user_id.')"><i class="fa-solid fa-pen-to-square"></i></a> | <a class="del-parent" onclick="delete_childuser('.$muser->user_id.') "><i class="fa-solid fa-trash"></i></a>
						 </td>	
					</tr>';
					}
					echo '</tbody>
				</table>
				<div class="err_msg" id="err_msg"></div>';
					if ($totalmem->member_count > count($childcount)) {
						echo '<button type="button" id="addusr" onclick="AddUser()">Add User +</button>';
					}
					?>
			</div>
		<?php }else{ echo "Payment are pending";}?>
		</div>
		<br><br>
		<style>
			.err_msg{
				color: #d31818;
			}
			.edit-parent i.fa-solid {
				font-size: 16px;
				cursor: pointer;
				color:#28c7e1;
			}
			.del-parent i.fa-solid {
				font-size: 16px;
				cursor: pointer;
				color: #d31818;
			}

		</style>
		<div id="add-user" style="display:none;">
			<div class="container">
				<form id="adchildfrm" class="user_form_markbtn" method="post" enctype="multipart/form-data">

					<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
						<label for="first_name">Firstname<span class="required">*</span></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="first_name" id="account_first_name" />
					</p>

					<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
						<label for="last_name">Lastname<span class="required">*</span></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="last_name" id="account_last_name" />
					</p>
					<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
						<label for="password">Password<span class="required">*</span></label>
						<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="account_password" />
					</p>
					<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
						<label for="cpassword">Confirm Password<span class="required">*</span></label>
						<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="cpassword" id="account_crmpassword" />
					</p>
					<p>
						<input type="hidden" name="childuserid" id="childuserid" value ="0" />
						<input type="hidden" name="parent" id="parentid" value="<?php echo $current_user->ID; ?> " />
						<input type="hidden" name="action" id="editajax" value=" <?php echo $editajax; ?> " />
						<input type="hidden" name="action" id="ajexfile" value=" <?php echo $ajfile; ?> " />
						<button type="button" id="submit" name="submit">Saves</button>
					</p>
				</form>
			</div>
		</div>
			
		<script src="jquery.confirmModal.min.js"></script>
		<script type="text/javascript">
			function edit_childuser(id){
				var editajax = jQuery('#editajax').val();
				var eduserid = id;
				//alert(edituserid+'#'+editajax);
				jQuery.ajax({
					type:'POST',						
					url: editajax,
					data: {
						action: 'edit_childuser',
						eduserid : eduserid,
					},	
					success: function(response){
						var res = JSON.parse(response);
						//console.log('doooooooooooone',response);
						if(res){
							// var repass = res.passw;
							jQuery('#adchildfrm').trigger("reset");
							jQuery('#add-user').show();
							jQuery("#childuserid").attr("value",res.id);
							jQuery("#account_first_name").attr("value",res.ftname);
							jQuery("#account_last_name").attr("value",res.ltname);
							jQuery("#account_password").attr("value",res.passw);
							jQuery("#account_crmpassword").attr("value",res.passw);
						}
					}
				});
			};
			function delete_childuser(id){
				var editajax = jQuery('#editajax').val();
				console.log('userid',id);
				 if (confirm("Are you sure you want to delete this Record?")) {
		            jQuery.ajax({
						type: "POST",
						url: editajax,
						data: {
							action: 'delete_childuser',
							userid: id
						},
						success: function (data) {
							console.log('ddddddddd',data);
							if(data == 0){
								document.getElementById('err_msg').innerHTML="This user can't be deleted";
							}else{
								window.location.reload();
							}
						}
					});
		        }

			}
			jQuery(document).ready(function() {
				jQuery("#submit").click(function() {
					var filepath = jQuery('#ajexfile').val();
					var firstpas = jQuery('#account_password').val();
					var lastpas = jQuery('#account_crmpassword').val();
					var parentid = jQuery('#parentid').val();
					// alert(parentid);
					if (firstpas == lastpas) {
						var formdata = {
							parentid: jQuery('#parentid').val(),
							childid: jQuery('#childuserid').val(),
							fname: jQuery('#account_first_name').val(),
							lname: jQuery('#account_last_name').val(),
							//email: jQuery('#account_email').val(),
							password: jQuery('#account_password').val(),
						}
						jQuery.ajax({
							type: 'POST',
							url: filepath,
							data: formdata,
							success: function(response) {
								// console.log('aaaaaaa', response);
								var res = JSON.parse(response);
								console.log('bbbbb', res);
								if (res.status == 'update') {
									jQuery(".err_msg").html(res.msg).show().delay(5000).queue(function(n) {
										jQuery(this).hide();
										n();
									});
									jQuery('#add-user').hide();
									jQuery("form")[0].reset();
									window.location.reload();
								}

								if (res.status == true) {
									jQuery('.user_list').append('<tr><td>' + res.data.username + '</td><td>' + res.data.fname + '</td><td>' + res.data.lname + '</td><td>' + res.data.email + '</td><td><a class="edit-parent" onclick="edit_childuser('+ res.data.id +')"><i class="fa-solid fa-pen-to-square"></i></a> | <a class="del-parent" onclick="delete_childuser(' + res.data.id + ') "><i class="fa-solid fa-trash"></i></td></tr>');
									jQuery(".err_msg").html(res.msg).show().delay(5000).queue(function(n) {
										jQuery(this).hide();
										n();
									});
									jQuery('#add-user').hide();
									jQuery("form")[0].reset();
								}
								if (res.status == false) {
									jQuery(res.data).each(function(index, data) {
										jQuery(".err_msg").html(data.error).show().delay(5000).queue(function(n) {
											jQuery(this).hide();
											n();
										});
									});
									jQuery('#add-user').hide();
									jQuery("form")[0].reset();
								}
								if (res.limit) {
									jQuery('#addusr').hide();
									jQuery(".err_msg").html(res.msg);
									jQuery("form")[0].reset();
								}


							}
						});
					} else {
						alert('Both Password should be same..');
					}
				});
			});

			function AddUser() {
				var add_user = document.getElementById("add-user");
				if (add_user.style.display == "none") {
					add_user.style.display = "block";
				} else {
					add_user.style.display = "none";
				}
			}
		</script>
<?php
	} else {
		wp_redirect(get_permalink(wc_get_page_id('myaccount')), 302);
	}
}
?>
