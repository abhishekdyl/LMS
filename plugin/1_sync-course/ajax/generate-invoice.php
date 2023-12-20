<?php
ob_start();
session_start();
require_once('../../../../wp-config.php');
require_once(ABSPATH.'wp-content/plugins/sync-course/dompdf/autoload.inc.php');
use Dompdf\Dompdf;

if(!isset($_SESSION['one_planet']['courseid']) && empty($_SESSION['one_planet']['courseid'])){
    wp_redirect(get_page_link(1203));
    exit();
}
$courseid=$_SESSION['one_planet']['courseid'];
$multi_step_id=$_SESSION['one_planet']['multistepform_id'];
$user=wp_get_current_user();
$invoice_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}invoice WHERE userid=$user->ID OR multistep_id=$multi_step_id");
$product = wc_get_product($courseid);
$multistep_form_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}multistepform WHERE id=$multi_step_id");
if($invoice_data){
	$invoice_no=$invoice_data->id;
}else{
	if($user->ID){
		$invoice_arr=array('userid'=>$user->ID,'courseid'=>$courseid,'price'=>$product->price,'createddate'=>time());
	}else{
		$invoice_arr=array('multistep_id'=>$multi_step_id,'courseid'=>$courseid,'price'=>$product->price,'createddate'=>time());
	}
	$wpdb->insert("{$wpdb->prefix}invoice", 
    $invoice_arr
     );
	$invoice_no=$wpdb->insert_id;
}
$post_data=unserialize($multistep_form_data->post_data);
//get_header(); //1239
?>
<script>
	jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
	jQuery('#content').find(':first-child').removeClass('tg-container--flex');
</script>



<?php
	//echo ABSPATH;
$html='
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
</style>
<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<div class="invoice-title">
				<h2>Invoice</h2>
				<h3 class="pull-right">Order #'.$invoice_no.'</h3>
			</div>
			<hr>
			<div class="row">
				<div class="col-xs-6">
					<address>
						<strong>Billed To:</strong><br>
						'.$post_data['fname'].' '.$post_data['lname'].' 
						<br>
						 '.$post_data['house_number'].' '.$post_data['wore_da'].' '.$post_data['zone'].'

						<br>
						<br>
						'.$post_data['city'].'
						'.$post_data['country'].'
					</address>
				</div>
				<div class="col-xs-6 text-right">
					<address>
						<strong>Order Date:</strong><br>
						 '.date('M d,Y',time()).'<br><br>
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
								<tr>
									<td>'.$product->name.'</td>
									<td class="text-center">$'.$product->price.'</td>
									<td class="text-center">1</td>
									<td class="text-right">$'.$product->price.'</td>
								</tr>
								
							</tbody>
						</table>
					</div>
					<div class="payment-container">
						<div class="text-center">
							<a href="javascript:void(0);" class="btn btn-info">Pay</a>
							<a href="'.get_page_link(1251).'" class="btn btn-info">Print</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>';
// die;
// echo $html;

try{
	$dompdf = new Dompdf();
	$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();
$pdf = $dompdf->output();
$invnoabc = 'Bokkinglist.pdf';
ob_end_clean();
$dompdf->stream($invnoabc,array("Attachment" => false));
exit(0);

}catch(Exception $e){
	print_r($e);
}
// instantiate and use the dompdf class

