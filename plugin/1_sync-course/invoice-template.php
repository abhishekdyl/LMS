<?php
ob_start();
session_start();
if (!isset($_SESSION['one_planet']['courseid']) && empty($_SESSION['one_planet']['courseid'])) {
	wp_redirect(get_page_link(1203));
	exit();
}

$courseids = $_SESSION['one_planet']['courseid'];
$multi_step_id = $_SESSION['one_planet']['multistepform_id'];
$user = wp_get_current_user();

// create invoice
if (isset($multi_step_id)) {
	$invoice_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}invoice WHERE multistep_id={$multi_step_id}");
	if (empty($invoice_data)) {
		$invoice_arr = array('multistep_id' => $multi_step_id, 'createddate' => time());
		$wpdb->insert("{$wpdb->prefix}invoice", $invoice_arr);
		$invoice_no = $_SESSION['invoice_no'] = $wpdb->insert_id;
	}
} else {
	if (empty($_SESSION['invoice_no'])) {
		if ($user->ID) {
			$invoice_arr = array('userid' => $user->ID, 'createddate' => time());
			$wpdb->insert("{$wpdb->prefix}invoice", $invoice_arr);
			$invoice_no = $_SESSION['invoice_no'] = $wpdb->insert_id;
		}
	}
}
$invoice_no =  $_SESSION['invoice_no'];
//store invoice details
$total_product = array();
$totalcourses = array();
foreach ($courseids as $courseid) {
	array_push($totalcourses, $courseid);
	if (isset($multi_step_id)) {
		$invoice_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}invoice_details WHERE multistep_id={$multi_step_id} AND courseid={$courseid}");
	} else {
		$invoice_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}invoice_details WHERE invoice_id={$invoice_no} AND courseid={$courseid} AND status=0 ORDER BY id DESC");
	}
	$product = wc_get_product($courseid);
	array_push($total_product, $product);
	$multistep_form_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}multistepform WHERE id=$multi_step_id");
	if ($invoice_data) {
		$invoice_no = $invoice_data->invoice_no;
	} else {
		if ($user->ID) {
			$invoice_arr = array('courseid' => $courseid, 'price' => $product->price, 'invoice_id' => $invoice_no, 'createddate' => time());
		} else {
			$invoice_arr = array('multistep_id' => $multi_step_id, 'courseid' => $courseid, 'price' => $product->price, 'invoice_id' => $invoice_no, 'createddate' => time());
		}
		$wpdb->insert(
			"{$wpdb->prefix}invoice_details",
			$invoice_arr
		);
		$invoice_no = $_SESSION['invoice_no'];
	}
	if ($multistep_form_data) {
		$post_data = unserialize($multistep_form_data->post_data);
	} else {
		$post_data = array(
			'fname' => get_user_meta($user->ID, 'first_name', true),
			'lname' => get_user_meta($user->ID, 'last_name', true),
			'house_number' => get_user_meta($user->ID, 'house_number', true),
			'wore_da' => get_user_meta($user->ID, 'wore_da', true),
			'zone' => get_user_meta($user->ID, 'zone', true),
			'city' => get_user_meta($user->ID, 'city', true),
			'country' => get_user_meta($user->ID, 'country', true),
		);
	}
}
/*echo "<pre>";
print_r($totalcourses);
echo "</pre>";*/
$purchcourse = implode(',', $totalcourses);
get_header(); //1239
?>
<script>
	jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
	jQuery('#content').find(':first-child').removeClass('tg-container--flex');
</script>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

<style>
	.invoice-title h2,
	.invoice-title h3 {
		display: inline-block;
	}

	.table>tbody>tr>.no-line {
		border-top: none;
	}

	.table>thead>tr>.no-line {
		border-bottom: none;
	}

	.table>tbody>tr>.thick-line {
		border-top: 2px solid;
	}

	.elementor-custom-margin {
		margin: 100px 0px;
	}
</style>
<?php
//echo ABSPATH;
?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="invoice-title">
					<h2>Invoice</h2>
					<h3 class="pull-right">Order #<?php echo $_SESSION['invoice_no']; ?></h3>
				</div>
				<hr>
				<div class="row">
					<div class="col-xs-6">
						<address>
							<strong>Billed To:</strong><br>
							<?php echo $post_data['fname'] . ' ' . $post_data['lname']; ?>
							<br>

							<?php
							echo $post_data['house_number'] . ' ' . $post_data['wore_da'] . ' ' . $post_data['zone']
							?>
							<br>
							<?php echo $post_data['city']; ?>
							<br>
							<?php echo $post_data['country']; ?>
						</address>
					</div>
					<div class="col-xs-6 text-right">
						<address>
							<strong>Order Date:</strong><br>
							<?php echo date('M d,Y', time()); ?><br><br>
						</address>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><strong>Order summary</strong></h3>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-condensed">
								<thead>
									<tr>
										<td><strong>Item</strong></td>
										<td class="text-center"><strong>Price</strong></td>
										<td class="text-center"><strong>Quantity</strong></td>
										<td class="text-right"><strong>Totals</strong></td>
									</tr>
								</thead>
								<tbody>
									<!-- foreach ($order->lineItems as $line) or some such thing here -->
									<?php
									$object_id = array_column($total_product, 'price', 'name');
									$total_price = 0;
									$count = 0;
									foreach ($object_id as $key => $value) {
									?>
										<tr>
											<td class="text-left"><?php echo $key; ?></td>
											<td class="text-center">$<?php $total_price += $value;
																		echo $value; ?></td>
											<td class="text-center">1</td>
											<td class="text-right"></td>
										</tr>
									<?php $count++;
									} ?>
									<tr>
										<td class="text-right"></td>
										<td class="text-right"></td>
										<td class="text-center"><?php echo $count; ?></td>
										<td class="text-right"><b>$<?php echo $total_price; ?></b></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="payment-container">
							<div class="text-center">
								 <?php $totalcartpro =  WC()->cart->get_cart_contents_count();
								if($totalcartpro){
									$woocommerce->cart->empty_cart();

									}
								 ?>
								<a href="<?php echo home_url() . '?add-to-cart=' . $purchcourse; ?>" class="btn btn-info">Pay</a>
								<a href="<?php echo get_page_link(1251); ?>" target="_blank" class="btn btn-info">Print</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
?>