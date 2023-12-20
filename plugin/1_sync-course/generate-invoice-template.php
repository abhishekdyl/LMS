<?php
ob_start();//1251
session_start();
//require_once('../../../../wp-config.php');
require_once(ABSPATH.'wp-content/plugins/sync-course/dompdf/autoload.inc.php');
use Dompdf\Dompdf;

if(!isset($_SESSION['one_planet']['courseid']) && empty($_SESSION['one_planet']['courseid'])){
    wp_redirect(get_page_link(1203));
    exit();
}

$courseids=$_SESSION['one_planet']['courseid'];
// print_r($courseids);


$total_product = array();
foreach ($courseids as $courseid) {
	$multi_step_id=$_SESSION['one_planet']['multistepform_id'];
	$user=wp_get_current_user();
	if(isset($multi_step_id)){
		$invoice_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}invoice WHERE userid={$user->ID} OR multistep_id={$multi_step_id}");
	}else{
		//$invoice_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}invoice WHERE userid={$user->ID} AND status=0 ORDER BY id DESC");
		$invoice_data=$wpdb->get_row("SELECT inv.*,invd.courseid FROM {$wpdb->prefix}invoice as inv JOIN {$wpdb->prefix}invoice_details as invd ON invd.invoice_id = inv.id WHERE invd.courseid={$courseid} AND inv.userid={$user->ID}");
	}
	//$invoice_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}invoice WHERE userid=$user->ID OR multistep_id=$multi_step_id");
	$product = wc_get_product($courseid);
	array_push($total_product, $product); 
	$multistep_form_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}multistepform WHERE id=$multi_step_id");
	if($invoice_data){
		$invoice_no=$invoice_data->id;
		$wpdb->update("{$wpdb->prefix}invoice",array('status'=>1,'updateddate'=>time()),array('id'=>$invoice_data->id));
		//unset($_SESSION['one_planet']);
	}
	if($multistep_form_data){
		$post_data=unserialize($multistep_form_data->post_data);

	}else{
		$post_data=array('fname'=>get_user_meta($user->ID,'first_name',true),
		'lname'=>get_user_meta($user->ID,'last_name',true),
		'house_number'=>get_user_meta($user->ID,'house_number',true),
		'wore_da'=>get_user_meta($user->ID,'wore_da',true),
		'zone'=>get_user_meta($user->ID,'zone',true),
		'city'=>get_user_meta($user->ID,'city',true),
		'country'=>get_user_meta($user->ID,'country',true),
		);
	}
}
//get_header(); //1239
?>
<script>
	jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
	jQuery('#content').find(':first-child').removeClass('tg-container--flex');
</script>

<style type="text/css">

@page {
  size: A4 landscape;
  margin: default;
}

</style>

<?php

$object_id = array_column($total_product, 'price', 'name');
$total_price = 0;
$count=0;

$string = '';
foreach($object_id as $key => $value){ 

$total_price += $value;

$string .= '<tr>
				<td class="text-left">'.$key.'</td>
				<td class="text-center">$'.$value.'</td>
				<td class="text-center">1</td>
				<td class="text-right"></td>
		   </tr>';

$count++; 

} 



$html= '<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
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
</style>
<section class="elementor-custom-margin" id="printableArea">
<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<div class="invoice-title">
				<h2>Invoice</h2>
				<h3 class="pull-right">Order #' . $invoice_no . '</h3>
			</div>
			<hr>
			<div class="row">
				<div class="col-xs-6">
					<address>
						<strong>Billed To:</strong><br>
						' . $post_data['fname'] . ' ' . $post_data['lname'] . ' 
						<br>
						 ' . $post_data['house_number'] . ' ' . $post_data['wore_da'] . ' ' . $post_data['zone'] . '

						<br>
						<br>
						' . $post_data['city'] . '
						' . $post_data['country'] . '
					</address>
				</div>
				<div class="col-xs-6 text-right">
					<address>
						<strong>Order Date:</strong><br>
						 ' . date('M d,Y', time()) . '<br><br>
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
							' . $string . '
							<tr>
								<td class="text-right"></td>
								<td class="text-right"></td>
								<td class="text-center">' . $count . '</td>
								<td class="text-right"><b>$' . $total_price . '</b></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</section>';



// try{
// 	$dompdf = new Dompdf();
// 	$dompdf->loadHtml($html);

// // (Optional) Setup the paper size and orientation
// $dompdf->setPaper('A4', 'landscape');

// // Render the HTML as PDF
// $dompdf->render();
// $pdf = $dompdf->output();
// $invnoabc = 'Bokkinglist.pdf';
// ob_end_clean();
// $dompdf->stream($invnoabc,array("Attachment" => false));
// exit(0);

// }catch(Exception $e){
// 	print_r($e);
// }
// instantiate and use the dompdf class

echo $html;

?>

<center><button class="btn btn-success" onclick="printDiv('printableArea')">Click to Print here</button></center>

<script type="text/javascript">
function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>
