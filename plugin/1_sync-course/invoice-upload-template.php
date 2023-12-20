<?php
ob_start();
session_start();
get_header(); //1248
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

	.elementor-custom-margin {
		margin: 100px 0px;
	}
</style>
<?php
//echo ABSPATH;
?>
<section class="elementor-custom-margin">
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<div class="response-msg">

				</div>
				<form id="upload-invoice-form">
					<div class="mb-3 mt-3">
						<label for="text" class="form-label">Invoice</label>
						<input type="text" class="form-control required" id="invoice" placeholder="Enter Invoice No" name="invoice">
					</div>
					<div class="mb-3">
						<label for="file" class="form-label">Upload Invoice</label>
						<input type="file" class="form-control required" name="invoice_image" id="upload-invoice" accept="image/*">
					</div>
					<br>
					<button type="submit" class="btn btn-primary btn-submit">Submit</button>
				</form>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	$(function() {
		$('#upload-invoice-form').submit(function(e) {
			e.preventDefault();
			var flag = true;
			var that = $(this);
			$(that).find('[name]').each(function() {
				if ($(this).hasClass('required') && $(this).val() == "") {
					flag = false;
					$(this).addClass('input-error');
				} else {
					$(this).removeClass('input-error');
				}
			});
			if (flag == false) {
				return false;
			}
			var formData = new FormData($(that)[0]);
			$.ajax({
				type: "POST",
				url: "<?php echo plugins_url('sync-course/ajax/upload-invoice.php') ?>",
				data: formData,
				beforeSend: function() {
					$('.btn-submit').prop('disabled', true);
					$('.btn-submit').text('Please Wait...');
				},
				contentType: false,
				processData: false,
				success: function(response) {
					var data = JSON.parse(response);
					var success_msg = '';
					if (data.status) {
						$(that)[0].reset();
						$('.response-msg').html(`<div class="alert alert-success">${data.data}</div>`);
					} else {
						var error_data = '';
						data.data?.forEach(function(ele, index) {
							console.log('ele', ele);
							console.log('index', index);
							error_data += `<div class="alert alert-danger">${ele.msg}</div>`;
						});
						$('.response-msg').html(error_data);

					}
					console.log('response', response);
				},
				complete: function() {

					$('.btn-submit').prop('disabled', false);
					$('.btn-submit').text('Submit');
				}
			});
			console.log('upload invoice', e);
		});
	});
	$('body').on('focus', '[name]', function() {
		$(this).removeClass('input-error');
	});
</script>

<?php
get_footer();
?>