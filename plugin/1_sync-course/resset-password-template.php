<?php
ob_start();
session_start();
$res_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$res_msg = '';
	$user = wp_get_current_user(); //reset_password( WP_User $user, string $new_pass )
	$password = $_POST['password'];
	$crmpassword = $_POST['crm_password'];
	if ($crmpassword == $password) {
		$userdata = [
			'ID'        => $user->ID,
			'user_pass' => $password,
		];
		wp_update_user($userdata);
		// wp_set_password($password, $user->ID);
		// global $wpdb;

		// 	$hash = wp_hash_password( $password );
		// 	$wpdb->update(
		// 		$wpdb->users,
		// 		array(
		// 			'user_pass'           => $hash,
		// 			'user_activation_key' => '',
		// 		),
		// 		array( 'ID' => $user->ID )
		// 	);

		update_user_meta($user->ID, 'user_force_login', 0);
		//reset_password($user,$password);
		$msg['status'] = true;
		$msg['msg'] = 'Password updated successfully';
		$res_msg = 'Password updated successfully';
		wp_redirect(home_url());
		exit;
	} else {
		$msg['status'] = false;
		$msg['msg'] = 'Password and confirm password must be same';
		$res_msg = 'Password and confirm password must be same';
	}
}
get_header(); //1284
?>
<script>
	jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
	jQuery('#content').find(':first-child').removeClass('tg-container--flex');
</script>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

<style>
	.input-error {
		border: 1px solid red !important;
	}
</style>
<?php
//echo ABSPATH;
?>

<style>
	.elementor-custom-margin {
		margin: 100px 0px;
	}
</style>

<div class="elementor-custom-margin">
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h3>Reset Password</h3>
				<div class="response-msg">
					<?php echo $res_msg; ?>
				</div>
				<form id="reset-password-form" method="POST">
					<div class="mb-3 mt-3">
						<label for="text" class="form-label">Password</label>
						<input type="password" class="form-control required" id="password" placeholder="Enter password" name="password">
					</div>
					<div class="mb-3">
						<label for="file" class="form-label">Confirm Password</label>
						<input type="password" class="form-control required" id="crm-password" placeholder="Enter confirm password" name="crm_password">
					</div>
					<br>
					<button type="text" class="btn btn-primary btn-submit">Submit</button>
					<div class="error-msg">

					</div>
				</form>
			</div>
		</div>
	</div>

</div>

<script type="text/javascript">
	$(function() {
		$('.btn-submit').click(function(e) {
			e.preventDefault();
			var flag = true;
			var password = $('#password').val();
			var crmpassword = $('#crm-password').val();
			var that = $('#reset-password-form');
			$(that).find('[name]').each(function() {
				if ($(this).hasClass('required') && $(this).val() == "") {
					flag = false;
					$(this).addClass('input-error');
				} else {
					$(this).removeClass('input-error');
				}
			});
			if (password != crmpassword) {
				flag = false;
				$('#crm-password').addClass('input-error');
				$('#password').addClass('input-error');
				$('.error-msg').html('Password and confirm password must be same');
			}
			// }else{
			// 	$('#crm-password').removeClass('input-error');
			// 	$('#password').removeClass('input-error');
			// 	//$('.error-msg').html('Password and confirm password must be same');
			// }
			if (flag == false) {
				return false;
			}
			$('#reset-password-form').submit();
			//  var formData = new FormData($(that)[0]);
			// $.ajax({
			// 	type:"POST",
			// 	url:"<?php echo plugins_url('sync-course/ajax/reset-password.php'); ?>",
			// 	data:$(that).serialize(),
			// 	beforeSend:function(){
			// 		$('.btn-submit').prop('disabled',true);
			// 		$('.btn-submit').text('Please Wait...');
			// 	},
			// 	//contentType: false,
			//              //processData: false,
			// 	success:function(response){
			// 		var data=JSON.parse(response);
			// 		var success_msg='';
			// 		if(data.status){
			// 			$(that)[0].reset();
			// 			$('.response-msg').html(`<div class="alert alert-success">${data.data}</div>`);
			// 		}else{
			// 			var error_data='';
			// 			data.data?.forEach(function(ele,index){
			// 				console.log('ele',ele);
			// 				console.log('index',index);
			// 				error_data +=`<div class="alert alert-danger">${ele.msg}</div>`;
			// 			});
			// 			$('.response-msg').html(error_data);

			// 		}
			// 		console.log('response',response);
			// 	},
			// 	complete:function(){

			// 		$('.btn-submit').prop('disabled',false);
			// 		$('.btn-submit').text('Submit');
			// 	}
			// });
			console.log('upload invoice', e);
		});
	});
	$('body').on('focus', '[name]', function() {
		$(this).removeClass('input-error');
	});
</script>

<?php
session_unset();
// destroy the session
session_destroy();
get_footer();
?>