<?php

require_once('../../config.php');

global $DB, $USER, $PAGE;
$PAGE->requires->jquery();

require_login();
$is_siteadmin = is_siteadmin();
$context = \context_system::instance();
$current_logged_in_user =  $USER->id;
$has_capability = has_capability('local/assignment_subscription:plugin_setting', $context, $current_logged_in_user);

if (!$has_capability) {
	$urltogo_dashboard = $CFG->wwwroot . '/my/';
	redirect($urltogo_dashboard, 'You do not have permission to view this page', null, \core\output\notification::NOTIFY_WARNING);
}


$PAGE->set_title('Assignment Subscription');
$PAGE->set_heading('Assignment Subscription');



if (isset($_POST['submit'])) {


	$recurring_cost = $_POST['recurring_cost'];
	$one_off_cost = $_POST['one_off_cost'];
	$recurring_duration = $_POST['recurring_duration'];

	$stripe_currency = $_POST['stripe_currency'];
	$tutor_ids = $_POST['tutor_id'];
	$sub_limit_status = $_POST['sub_limit_status'];
	$course_id = $_POST['course_id'];
	$sub_limit = $_POST['sub_limit'];
	$sub_duration = $_POST['sub_duration'];

	$default_tutor = $_POST['default_tutor'];
	$course_id_default = $_POST['course_id_default'];



	// Submission dashboard date range setting
	if(count($_POST['tabs'])>0){
		set_config('tabs', implode(",", $_POST['tabs']), 'local_assignment_subscription');
	}

	if(!empty($_POST['duration'])){
		$duration = '';
		if($_POST['duration'] == 'custom_date'){
			$duration = strtotime($_POST['custom_start_date']).",".strtotime($_POST['custom_end_date']);
		}else{
			$duration = $_POST['duration'];
		}
		set_config('duration', $duration, 'local_assignment_subscription');
	}


	$a = array($course_id, $sub_limit, $sub_duration);
	$count =  count($course_id);

	$record_ins = new stdClass();
	$record_ins->recurring_cost = $recurring_cost;
	$record_ins->one_off_cost = $one_off_cost;
	$record_ins->recurring_duration = $recurring_duration;
	$record_ins->stripe_currency = $stripe_currency;

	$obj_new = new stdClass();
	$totors = $new = array_filter($_POST, function ($v, $k) {
		return strpos($k, "default_tutor_") !== false;
	}, ARRAY_FILTER_USE_BOTH);
	foreach ($totors as $totorkey => $totor) {
		$kayindex = str_replace("default_tutor_", "", $totorkey);
		$courses = $_POST['course_id_default_' . $kayindex];
		$b = array($totor, $courses);
		$tid = array_pop($totor);
		// echo "<pre> {$tid}";
		if (empty($courses)) {
			$courses = array();
			$existingtoothercourses = array();
		} else {
			$existingtoothercourses = $DB->get_fieldset_sql("SELECT course_id FROM {assign_subs_default_tutor} WHERE course_id in(" . implode(",", $courses) . ") AND tutor_id !=:tutor_id", array("tutor_id" => $tid));
		}
		$existingcourses = $DB->get_fieldset_sql("SELECT course_id FROM {assign_subs_default_tutor} WHERE tutor_id=:tutor_id", array("tutor_id" => $tid));

		$tobedeleted = array_values(array_diff($existingcourses, $courses));
		$tobedeleted = array_merge($tobedeleted, $existingtoothercourses);
		$tobeinserted = array_values(array_diff($courses, $existingcourses));
		if (sizeof($tobedeleted) > 0) {
			$DB->execute("DELETE FROM {assign_subs_default_tutor} where course_id in(" . implode(",", $tobedeleted) . ")");
		}
		$tobeinsert = array();
		foreach ($tobeinserted as $key => $cid) {
			$d = new stdClass();
			$d->tutor_id = $tid;
			$d->course_id = $cid;
			array_push($tobeinsert, $d);
		}
		$DB->insert_records("assign_subs_default_tutor", $tobeinsert);
	}
	// die;
	require_once('payment_config.php');


	// One-off-mode
	$productName = "Yearly One-off";
	// Search product if exists
	try {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/products');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, STRIPE_API_KEY);
		$response = curl_exec($ch);
		$product_list = json_decode($response);
		curl_close($ch);
		$products = $product_list->data;
		$product_count_one_off = 0;

		for ($i = 0; $i < count($products); $i++) {
			$product_id = $products[$i]->id;
			$product_name = $products[$i]->name;

			if ($product_name == $productName) {
				$product_count_one_off++;
				$one_off_pro_id = $products[$i]->id;
			}
		}
	} catch (Exception $e) {
		$api_error = $e->getMessage();
	}


	// Else Create products
	if (empty($api_error) && $product_count_one_off == 0) {
		try {

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/products');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded',]);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, STRIPE_API_KEY);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'name=' . $productName);
			$response = curl_exec($ch);
			curl_close($ch);
			$one_off_pro_id = json_decode($response)->id;
		} catch (Exception $e) {
			$api_error = $e->getMessage();
		}
	}


	$productName = $recurring_duration . " Recurring";
	if (empty($api_error)) {
		try {

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/products');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded',]);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, STRIPE_API_KEY);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'name=' . $productName);
			$response = curl_exec($ch);
			curl_close($ch);
			$pro_id = json_decode($response)->id;
		} catch (Exception $e) {
			$api_error = $e->getMessage();
		}
	}



	// Create a price
	if (empty($api_error)) {

		try {

			$recurring_cost = ($_POST['recurring_cost'] * 100);
			$recurring_duration_new = trim(str_ireplace('ly', ' ', strtolower($recurring_duration)));


			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/prices');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded',]);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, STRIPE_API_KEY);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'unit_amount=' . $recurring_cost . '&currency=' . $stripe_currency . '&recurring[interval]=' . $recurring_duration_new . '&product=' . $pro_id);
			$response = curl_exec($ch);
			curl_close($ch);
			$pri_id = json_decode($response)->id;
		} catch (Exception $e) {
			$api_error = $e->getMessage();
		}
	}


	$record_ins->stripe_recurring_price_id = $pri_id;
	$record_ins->stripe_recurring_product_id = $pro_id;
	$record_ins->stripe_one_off_product_id = $one_off_pro_id;
	$DB->delete_records('assign_subs_settings');
	$DB->insert_record('assign_subs_settings', $record_ins, false);


	foreach ($tutor_ids as $tutor_id) {
		$obj = new stdClass();
		$obj->id = $tutor_id;
		$obj->active = 1;
		$DB->update_record('assign_subs_tutors', $obj);
	}


	if ($sub_limit_status == 1) {
		$DB->set_field('assign_subs_sub_limit', "status", 1, array("status" => 0));
		for ($col = 0; $col < $count; $col++) {
			for ($row = 0; $row < 3; $row++) {
				if ($row == 0) {

					$course_idd = $a[$row][$col];
					if ($course_idd != 'All Courses') {
						$chk_sub_lmti = "SELECT * FROM {assign_subs_sub_limit} WHERE course_id = " . $course_idd;
						$row_sub_lmti = $DB->get_record_sql($chk_sub_lmti);
					} else {
						$DB->delete_records('assign_subs_sub_limit', array('course_id' => 'All Courses'));
					}
					$record_ins->course_id = $a[$row][$col];
				} elseif ($row == 1) {
					$record_ins->sub_limit = $a[$row][$col];
				} else {
					$record_ins->sub_duration = $a[$row][$col];
				}
				$record_ins->status = $sub_limit_status;
			}

			if (empty($row_sub_lmti->course_id)) {
				$DB->insert_record('assign_subs_sub_limit', $record_ins, false);
			} else {
				$record_ins->id = $row_sub_lmti->id;
				$DB->update_record('assign_subs_sub_limit', $record_ins);
			}
		}
	} else {
		$DB->set_field('assign_subs_sub_limit', "status", 0, array("status" => 1));
	}


	// If cancelled then redirect
	$urltogo = $CFG->wwwroot . '/local/assignment_subscription/plugin_setting.php';
	redirect($urltogo, 'Plugin setting successfull', \core\output\notification::NOTIFY_SUCCESS);
}





echo $OUTPUT->header();


?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />



<style type="text/css">

	.nav {
		list-style-type: none !important;
	}

	.nav li {
		display: inline-block !important;
	}


	.modal-backdrop {
		background-color: #1313127a !important;
	}

	#ajax_tutors {
		overflow-Y: scroll;
		height: 150px;
	}

	.customtutor.readonly>ul>li {
		position: relative;
	}

	.customtutor.readonly>ul>li:before {
		content: " ";
		position: absolute;
		width: 100%;
		background: rgba(0, 0, 0, 0.2);
		z-index: 2;
		height: 100%;
	}
	.customtutor .select2-container .select2-selection {
		border: unset;
	}
	.customtutor .select2-container {
	    border: solid black 1px;
	    border-radius: 5px;
	}

	.customtutor .select2-search {
	    width: 100%;
	}
</style>



<div class="container">
	<a class="btn btn-primary" href="home.php">Back</a><br/><br/>
	<h2>Default Priority Setting</h2>
	<?php

	$chk_settings =  "SELECT * FROM {assign_subs_settings}";
	$row_settings = $DB->get_record_sql($chk_settings);

	?>
	<form action="" method="post">

		<div class="row">
			<div class="col-md-6"><b>Stripe Recurring Cost</b></div>
			<div class="col-md-3"><input type="text" name="recurring_cost" class="form-control" value="<?php if (!empty($row_settings->recurring_cost)) {
																											echo $row_settings->recurring_cost;
																										} else {
																											echo 0;
																										} ?>"><label>Default: 0</label></div>
			<div class="col-md-3"></div>
		</div>
		<hr>

		<div class="row">
			<div class="col-md-6"><b>Stripe One-off Cost (Yearly)</b></div>
			<div class="col-md-3"><input type="text" name="one_off_cost" class="form-control" value="<?php if (!empty($row_settings->one_off_cost)) {
																											echo $row_settings->one_off_cost;
																										} else {
																											echo 0;
																										} ?>"><label>Default: 0</label></div>
			<div class="col-md-3"></div>
		</div>
		<hr>

		<div class="row">
			<div class="col-md-6"><b>Stripe Recurring Duration</b></div>
			<div class="col-md-3">
				<select name="recurring_duration" class="form-control">
					<option value="Monthly" <?php if (($row_settings->recurring_duration) == 'Monthly') {
												echo "selected";
											} ?>>Monthly</option>
					<option value="Yearly" <?php if (($row_settings->recurring_duration) == 'Yearly') {
												echo "selected";
											} ?>>Yearly</option>
				</select>
				<label>Default: <?php echo $row_settings->recurring_duration; ?></label>
			</div>
			<div class="col-md-3"></div>
		</div>
		<hr>

		<div class="row">
			<div class="col-md-6"><b>Currency</b></div>
			<div class="col-md-3">
				<select name="stripe_currency" class="form-control">
					<option value="USD" <?php if (($row_settings->stripe_currency) == 'USD') {
											echo "selected";
										} ?>>United States Dollar</option>
					<option value="AED" <?php if (($row_settings->stripe_currency) == 'AED') {
											echo "selected";
										} ?>>United Arab Emirates</option>
					<option value="AFN" <?php if (($row_settings->stripe_currency) == 'AFN') {
											echo "selected";
										} ?>>Afghan Afghani</option>
					<option value="ALL" <?php if (($row_settings->stripe_currency) == 'ALL') {
											echo "selected";
										} ?>>Albanian lek</option>
					<option value="AMD" <?php if (($row_settings->stripe_currency) == 'AMD') {
											echo "selected";
										} ?>>Armenian dram</option>
					<option value="ANG" <?php if (($row_settings->stripe_currency) == 'ANG') {
											echo "selected";
										} ?>>Netherlands Antillean Guilder</option>
					<option value="AOA" <?php if (($row_settings->stripe_currency) == 'AOA') {
											echo "selected";
										} ?>>Angolan kwanza</option>
					<option value="ARS" <?php if (($row_settings->stripe_currency) == 'ARS') {
											echo "selected";
										} ?>>Argentine peso</option>
					<option value="AUD" <?php if (($row_settings->stripe_currency) == 'AUD') {
											echo "selected";
										} ?>>Australian Dollar</option>
					<option value="AWG" <?php if (($row_settings->stripe_currency) == 'AWG') {
											echo "selected";
										} ?>>Aruban Florin</option>
					<option value="AZN" <?php if (($row_settings->stripe_currency) == 'AZN') {
											echo "selected";
										} ?>>Azerbaijani manat</option>
					<option value="BAM" <?php if (($row_settings->stripe_currency) == 'BAM') {
											echo "selected";
										} ?>>Bosnia and Herzegovina convertible mark</option>
					<option value="BBD" <?php if (($row_settings->stripe_currency) == 'BBD') {
											echo "selected";
										} ?>>Barbadian dollar</option>
					<option value="BDT" <?php if (($row_settings->stripe_currency) == 'BDT') {
											echo "selected";
										} ?>>Bangladeshi taka</option>
					<option value="BGN" <?php if (($row_settings->stripe_currency) == 'BGN') {
											echo "selected";
										} ?>>Bulgarian Lev</option>
					<option value="BIF" <?php if (($row_settings->stripe_currency) == 'BIF') {
											echo "selected";
										} ?>>Burundian franc</option>
					<option value="BMD" <?php if (($row_settings->stripe_currency) == 'BMD') {
											echo "selected";
										} ?>>Bermudian dollar</option>
					<option value="BND" <?php if (($row_settings->stripe_currency) == 'BND') {
											echo "selected";
										} ?>>Brunei dollar</option>
					<option value="BOB" <?php if (($row_settings->stripe_currency) == 'BOB') {
											echo "selected";
										} ?>>Bolivian boliviano</option>
					<option value="BRL" <?php if (($row_settings->stripe_currency) == 'BRL') {
											echo "selected";
										} ?>>Brazilian Real</option>
					<option value="BSD" <?php if (($row_settings->stripe_currency) == 'BSD') {
											echo "selected";
										} ?>>Bahamian dollar</option>
					<option value="BWP" <?php if (($row_settings->stripe_currency) == 'BWP') {
											echo "selected";
										} ?>>Botswana pula</option>
					<option value="BYN" <?php if (($row_settings->stripe_currency) == 'BYN') {
											echo "selected";
										} ?>>Belarusian ruble</option>
					<option value="BZD" <?php if (($row_settings->stripe_currency) == 'BZD') {
											echo "selected";
										} ?>>Belize dollar</option>
					<option value="CAD" <?php if (($row_settings->stripe_currency) == 'CAD') {
											echo "selected";
										} ?>>Canadian Dollar</option>
					<option value="CDF" <?php if (($row_settings->stripe_currency) == 'CDF') {
											echo "selected";
										} ?>>Congolese franc</option>
					<option value="CHF" <?php if (($row_settings->stripe_currency) == 'CHF') {
											echo "selected";
										} ?>>Swiss Franc</option>
					<option value="CLP" <?php if (($row_settings->stripe_currency) == 'CLP') {
											echo "selected";
										} ?>>Chilean peso</option>
					<option value="CNY" <?php if (($row_settings->stripe_currency) == 'CNY') {
											echo "selected";
										} ?>>Chinese yuan</option>
					<option value="COP" <?php if (($row_settings->stripe_currency) == 'COP') {
											echo "selected";
										} ?>>Colombian peso</option>
					<option value="CRC" <?php if (($row_settings->stripe_currency) == 'CRC') {
											echo "selected";
										} ?>>Costa Rican colón</option>
					<option value="CVE" <?php if (($row_settings->stripe_currency) == 'CVE') {
											echo "selected";
										} ?>>Cape Verdean escudo</option>
					<option value="CZK" <?php if (($row_settings->stripe_currency) == 'CZK') {
											echo "selected";
										} ?>>Czech Koruna</option>
					<option value="DJF" <?php if (($row_settings->stripe_currency) == 'DJF') {
											echo "selected";
										} ?>>Djiboutian franc</option>
					<option value="DKK" <?php if (($row_settings->stripe_currency) == 'DKK') {
											echo "selected";
										} ?>>Danish Krone</option>
					<option value="DOP" <?php if (($row_settings->stripe_currency) == 'DOP') {
											echo "selected";
										} ?>>Dominican peso</option>
					<option value="DZD" <?php if (($row_settings->stripe_currency) == 'DZD') {
											echo "selected";
										} ?>>Algerian dinar</option>
					<option value="EGP" <?php if (($row_settings->stripe_currency) == 'EGP') {
											echo "selected";
										} ?>>Egyptian pound</option>
					<option value="ETB" <?php if (($row_settings->stripe_currency) == 'ETB') {
											echo "selected";
										} ?>>Ethiopian birr</option>
					<option value="EUR" <?php if (($row_settings->stripe_currency) == 'EUR') {
											echo "selected";
										} ?>>Euro</option>
					<option value="FJD" <?php if (($row_settings->stripe_currency) == 'FJD') {
											echo "selected";
										} ?>>Fijian dollar</option>
					<option value="FKP" <?php if (($row_settings->stripe_currency) == 'FKP') {
											echo "selected";
										} ?>>Falkland Islands Pound</option>
					<option value="GBP" <?php if (($row_settings->stripe_currency) == 'GBP') {
											echo "selected";
										} ?>>British Pound</option>
					<option value="GEL" <?php if (($row_settings->stripe_currency) == 'GEL') {
											echo "selected";
										} ?>>Georgian lari</option>
					<option value="GIP" <?php if (($row_settings->stripe_currency) == 'GIP') {
											echo "selected";
										} ?>>Gibraltar Pound</option>
					<option value="GMD" <?php if (($row_settings->stripe_currency) == 'GMD') {
											echo "selected";
										} ?>>Gambian dalasi</option>
					<option value="GNF" <?php if (($row_settings->stripe_currency) == 'GNF') {
											echo "selected";
										} ?>>Guinean franc</option>
					<option value="GTQ" <?php if (($row_settings->stripe_currency) == 'GTQ') {
											echo "selected";
										} ?>>Guatemalan quetzal</option>
					<option value="GYD" <?php if (($row_settings->stripe_currency) == 'GYD') {
											echo "selected";
										} ?>>Guyanese dollar</option>
					<option value="HKD" <?php if (($row_settings->stripe_currency) == 'HKD') {
											echo "selected";
										} ?>>Hong Kong Dollar</option>
					<option value="HNL" <?php if (($row_settings->stripe_currency) == 'HNL') {
											echo "selected";
										} ?>>Honduran lempira</option>
					<option value="HRK" <?php if (($row_settings->stripe_currency) == 'HRK') {
											echo "selected";
										} ?>>Croatian kuna</option>
					<option value="HTG" <?php if (($row_settings->stripe_currency) == 'HTG') {
											echo "selected";
										} ?>>Haitian gourde</option>
					<option value="HUF" <?php if (($row_settings->stripe_currency) == 'HUF') {
											echo "selected";
										} ?>>Hungarian Forint</option>
					<option value="IDR" <?php if (($row_settings->stripe_currency) == 'IDR') {
											echo "selected";
										} ?>>Indonesian rupiah</option>
					<option value="ILS" <?php if (($row_settings->stripe_currency) == 'ILS') {
											echo "selected";
										} ?>>Israeli New Sheqel</option>
					<option value="INR" <?php if (($row_settings->stripe_currency) == 'INR') {
											echo "selected";
										} ?>>Indian rupee</option>
					<option value="ISK" <?php if (($row_settings->stripe_currency) == 'ISK') {
											echo "selected";
										} ?>>Icelandic króna</option>
					<option value="JMD" <?php if (($row_settings->stripe_currency) == 'JMD') {
											echo "selected";
										} ?>>Jamaican dollar</option>
					<option value="JPY" <?php if (($row_settings->stripe_currency) == 'JPY') {
											echo "selected";
										} ?>>Japanese Yen</option>
					<option value="KES" <?php if (($row_settings->stripe_currency) == 'KES') {
											echo "selected";
										} ?>>Kenyan shilling</option>
					<option value="KGS" <?php if (($row_settings->stripe_currency) == 'KGS') {
											echo "selected";
										} ?>>Kyrgyzstani som</option>
					<option value="KHR" <?php if (($row_settings->stripe_currency) == 'KHR') {
											echo "selected";
										} ?>>Cambodian riel</option>
					<option value="KMF" <?php if (($row_settings->stripe_currency) == 'KMF') {
											echo "selected";
										} ?>>Comorian franc</option>
					<option value="KRW" <?php if (($row_settings->stripe_currency) == 'KRW') {
											echo "selected";
										} ?>>South Korean won</option>
					<option value="KYD" <?php if (($row_settings->stripe_currency) == 'KYD') {
											echo "selected";
										} ?>>Cayman Islands Dollar</option>
					<option value="KZT" <?php if (($row_settings->stripe_currency) == 'KZT') {
											echo "selected";
										} ?>>Kazakhstani tenge</option>
					<option value="LAK" <?php if (($row_settings->stripe_currency) == 'LAK') {
											echo "selected";
										} ?>>Lao kip</option>
					<option value="LBP" <?php if (($row_settings->stripe_currency) == 'LBP') {
											echo "selected";
										} ?>>Lebanese pound</option>
					<option value="LKR" <?php if (($row_settings->stripe_currency) == 'LKR') {
											echo "selected";
										} ?>>Sri Lankan rupee</option>
					<option value="LRD" <?php if (($row_settings->stripe_currency) == 'LRD') {
											echo "selected";
										} ?>>Liberian dollar</option>
					<option value="LSL" <?php if (($row_settings->stripe_currency) == 'LSL') {
											echo "selected";
										} ?>>Lesotho loti</option>
					<option value="MAD" <?php if (($row_settings->stripe_currency) == 'MAD') {
											echo "selected";
										} ?>>Moroccan dirham</option>
					<option value="MDL" <?php if (($row_settings->stripe_currency) == 'MDL') {
											echo "selected";
										} ?>>Moldovan leu</option>
					<option value="MGA" <?php if (($row_settings->stripe_currency) == 'MGA') {
											echo "selected";
										} ?>>Malagasy ariary</option>
					<option value="MKD" <?php if (($row_settings->stripe_currency) == 'MKD') {
											echo "selected";
										} ?>>Macedonian denar</option>
					<option value="MMK" <?php if (($row_settings->stripe_currency) == 'MMK') {
											echo "selected";
										} ?>>Burmese kyat</option>
					<option value="MNT" <?php if (($row_settings->stripe_currency) == 'MNT') {
											echo "selected";
										} ?>>Mongolian tögrög</option>
					<option value="MOP" <?php if (($row_settings->stripe_currency) == 'MOP') {
											echo "selected";
										} ?>>Macanese Pataca</option>
					<option value="MRO" <?php if (($row_settings->stripe_currency) == 'MRO') {
											echo "selected";
										} ?>>Mauritanian ouguiya</option>
					<option value="MUR" <?php if (($row_settings->stripe_currency) == 'MUR') {
											echo "selected";
										} ?>>Mauritian rupee</option>
					<option value="MVR" <?php if (($row_settings->stripe_currency) == 'MVR') {
											echo "selected";
										} ?>>Maldivian rufiyaa</option>
					<option value="MWK" <?php if (($row_settings->stripe_currency) == 'MWK') {
											echo "selected";
										} ?>>Malawian kwacha</option>
					<option value="MXN" <?php if (($row_settings->stripe_currency) == 'MXN') {
											echo "selected";
										} ?>>Mexican Peso</option>
					<option value="MYR" <?php if (($row_settings->stripe_currency) == 'MYR') {
											echo "selected";
										} ?>>Malaysian Ringgit</option>
					<option value="MZN" <?php if (($row_settings->stripe_currency) == 'MZN') {
											echo "selected";
										} ?>>Mozambican metical</option>
					<option value="NAD" <?php if (($row_settings->stripe_currency) == 'NAD') {
											echo "selected";
										} ?>>Namibian dollar</option>
					<option value="NGN" <?php if (($row_settings->stripe_currency) == 'NGN') {
											echo "selected";
										} ?>>Nigerian naira</option>
					<option value="NIO" <?php if (($row_settings->stripe_currency) == 'NIO') {
											echo "selected";
										} ?>>Nicaraguan córdoba</option>
					<option value="NOK" <?php if (($row_settings->stripe_currency) == 'NOK') {
											echo "selected";
										} ?>>Norwegian Krone</option>
					<option value="NPR" <?php if (($row_settings->stripe_currency) == 'NPR') {
											echo "selected";
										} ?>>Nepalese rupee</option>
					<option value="NZD" <?php if (($row_settings->stripe_currency) == 'NZD') {
											echo "selected";
										} ?>>New Zealand dollar</option>
					<option value="PAB" <?php if (($row_settings->stripe_currency) == 'PAB') {
											echo "selected";
										} ?>>Panamanian balboa</option>
					<option value="PEN" <?php if (($row_settings->stripe_currency) == 'PEN') {
											echo "selected";
										} ?>>Peruvian sol</option>
					<option value="PGK" <?php if (($row_settings->stripe_currency) == 'PGK') {
											echo "selected";
										} ?>>Papua New Guinean kina</option>
					<option value="PHP" <?php if (($row_settings->stripe_currency) == 'PHP') {
											echo "selected";
										} ?>>Philippine Peso</option>
					<option value="PKR" <?php if (($row_settings->stripe_currency) == 'PKR') {
											echo "selected";
										} ?>>Pakistani rupee</option>
					<option value="PLN" <?php if (($row_settings->stripe_currency) == 'PLN') {
											echo "selected";
										} ?>>Polish Złoty</option>
					<option value="PYG" <?php if (($row_settings->stripe_currency) == 'PYG') {
											echo "selected";
										} ?>>Paraguayan guaraní</option>
					<option value="QAR" <?php if (($row_settings->stripe_currency) == 'QAR') {
											echo "selected";
										} ?>>Qatari riyal</option>
					<option value="RON" <?php if (($row_settings->stripe_currency) == 'RON') {
											echo "selected";
										} ?>>Romanian Leu</option>
					<option value="RSD" <?php if (($row_settings->stripe_currency) == 'RSD') {
											echo "selected";
										} ?>>Serbian dinar</option>
					<option value="RUB" <?php if (($row_settings->stripe_currency) == 'RUB') {
											echo "selected";
										} ?>>Russian Ruble</option>
					<option value="RWF" <?php if (($row_settings->stripe_currency) == 'RWF') {
											echo "selected";
										} ?>>Rwandan franc</option>
					<option value="SAR" <?php if (($row_settings->stripe_currency) == 'SAR') {
											echo "selected";
										} ?>>Saudi riyal</option>
					<option value="SBD" <?php if (($row_settings->stripe_currency) == 'SBD') {
											echo "selected";
										} ?>>Solomon Islands dollar</option>
					<option value="SCR" <?php if (($row_settings->stripe_currency) == 'SCR') {
											echo "selected";
										} ?>>Seychellois rupee</option>
					<option value="SEK" <?php if (($row_settings->stripe_currency) == 'SEK') {
											echo "selected";
										} ?>>Swedish Krona</option>
					<option value="SGD" <?php if (($row_settings->stripe_currency) == 'SGD') {
											echo "selected";
										} ?>>Singapore Dollar</option>
					<option value="SHP" <?php if (($row_settings->stripe_currency) == 'SHP') {
											echo "selected";
										} ?>>The St. Helena Pound</option>
					<option value="SLE" <?php if (($row_settings->stripe_currency) == 'SLE') {
											echo "selected";
										} ?>>Sierra Leonean leone</option>
					<option value="SOS" <?php if (($row_settings->stripe_currency) == 'SOS') {
											echo "selected";
										} ?>>Somali shilling</option>
					<option value="SRD" <?php if (($row_settings->stripe_currency) == 'SRD') {
											echo "selected";
										} ?>>Surinamese dollar</option>
					<option value="STD" <?php if (($row_settings->stripe_currency) == 'STD') {
											echo "selected";
										} ?>>São Tomé and Príncipe dobra</option>
					<option value="SZL" <?php if (($row_settings->stripe_currency) == 'SZL') {
											echo "selected";
										} ?>>Swazi lilangeni</option>
					<option value="THB" <?php if (($row_settings->stripe_currency) == 'THB') {
											echo "selected";
										} ?>>Thai Baht</option>
					<option value="TJS" <?php if (($row_settings->stripe_currency) == 'TJS') {
											echo "selected";
										} ?>>Tajikistani somoni</option>
					<option value="TOP" <?php if (($row_settings->stripe_currency) == 'TOP') {
											echo "selected";
										} ?>>Tongan pa'anga</option>
					<option value="TRY" <?php if (($row_settings->stripe_currency) == 'TRY') {
											echo "selected";
										} ?>>Turkish lira</option>
					<option value="TTD" <?php if (($row_settings->stripe_currency) == 'TTD') {
											echo "selected";
										} ?>>Trinidad and Tobago dollar</option>
					<option value="TWD" <?php if (($row_settings->stripe_currency) == 'TWD') {
											echo "selected";
										} ?>>New Taiwan Dollar</option>
					<option value="TZS" <?php if (($row_settings->stripe_currency) == 'TZS') {
											echo "selected";
										} ?>>Tanzanian shilling</option>
					<option value="UAH" <?php if (($row_settings->stripe_currency) == 'UAH') {
											echo "selected";
										} ?>>Ukrainian hryvnia</option>
					<option value="UGX" <?php if (($row_settings->stripe_currency) == 'UGX') {
											echo "selected";
										} ?>>Ugandan shilling</option>
					<option value="UYU" <?php if (($row_settings->stripe_currency) == 'UYU') {
											echo "selected";
										} ?>>Uruguayan peso</option>
					<option value="UZS" <?php if (($row_settings->stripe_currency) == 'UZS') {
											echo "selected";
										} ?>>Uzbekistani som</option>
					<option value="VND" <?php if (($row_settings->stripe_currency) == 'VND') {
											echo "selected";
										} ?>>Vietnamese dong</option>
					<option value="VUV" <?php if (($row_settings->stripe_currency) == 'VUV') {
											echo "selected";
										} ?>>Vanuatu vatu</option>
					<option value="WST" <?php if (($row_settings->stripe_currency) == 'WST') {
											echo "selected";
										} ?>>Samoan tala</option>
					<option value="XAF" <?php if (($row_settings->stripe_currency) == 'XAF') {
											echo "selected";
										} ?>>Central African CFA franc</option>
					<option value="XCD" <?php if (($row_settings->stripe_currency) == 'XCD') {
											echo "selected";
										} ?>>East Caribbean dollar</option>
					<option value="XOF" <?php if (($row_settings->stripe_currency) == 'XOF') {
											echo "selected";
										} ?>>West African CFA franc</option>
					<option value="XPF" <?php if (($row_settings->stripe_currency) == 'XPF') {
											echo "selected";
										} ?>>The Central Pacific Franc (CFP)</option>
					<option value="YER" <?php if (($row_settings->stripe_currency) == 'YER') {
											echo "selected";
										} ?>>Yemeni rial</option>
					<option value="ZAR" <?php if (($row_settings->stripe_currency) == 'ZAR') {
											echo "selected";
										} ?>>South African rand</option>
					<option value="ZMW" <?php if (($row_settings->stripe_currency) == 'ZMW') {
											echo "selected";
										} ?>>Zambian kwacha</option>
				</select>
				<label>Default: <?php echo $row_settings->stripe_currency; ?></label>
			</div>
			<div class="col-md-3"></div>
		</div>
		<hr>

		<?php
		$chk_tutors_chk =  "SELECT * FROM {assign_subs_tutors} WHERE deleted_status=0";
		$row_tutors_chk = $DB->get_records_sql($chk_tutors_chk);
		?>

		<div class="row">
			<div class="col-md-6"><b>Tutors</b></div>
			<?php if ($row_tutors_chk = $DB->get_records_sql($chk_tutors_chk)) {
			} else { ?>
				<div class="col-md-6" id="create_tutor" <?php if ($row_tutors_chk = $DB->get_records_sql($chk_tutors_chk)) {
															echo "style='display: none;'";
														} else {
															echo "style='display: block;'";
														} ?>><a class="btn btn-danger create_tutor" style="color: white;">Create Tutor</a>
				</div>
				<script type="text/javascript">
					$(".create_tutor").click(function() {
						$(".dropdown_tutor").show();
						$(".create_tutor").hide();
					});
				</script>
			<?php } ?>
		</div>

		<div class="row">
			<div class="col-md-6"></div>
			<div class="col-md-3 dropdown_tutor" <?php if ($row_tutors_chk = $DB->get_records_sql($chk_tutors_chk)) {
														echo "style='display: block;'";
													} else {
														echo "style='display: none;'";
													} ?>>
				<input type="text" class="form-control" placeholder="Enter name" id="add_tutor" />
			</div>
			<div class="col-md-3 dropdown_tutor" <?php if ($row_tutors_chk = $DB->get_records_sql($chk_tutors_chk)) {
														echo "style='display: block;'";
													} else {
														echo "style='display: none;'";
													} ?>>
				<a class="btn btn-success submit_tutor" style="color: #fff;">Add tutor</a>
			</div>
		</div>

		<script type="text/javascript">
			$(document).ready(function() {
				$("body").on("click", '.submit_tutor', function() {

					var add_tutor_value = $("#add_tutor").val();
					var redi = "<?php echo $CFG->wwwroot . '/local/assignment_subscription/plugin_setting.php'; ?>";
					if (add_tutor_value != '') {
						$.ajax({
							url: "<?php echo $CFG->wwwroot . '/local/assignment_subscription/add_tutor.php'; ?>",
							type: "post",
							data: {
								add_tutor_value: add_tutor_value
							},
							success: function(response) {
								alert(response);
								window.location.href = redi;

							},
						});
					} else {
						alert("Please enter tutor name first");
					}

				});
			});
		</script>


		<div class="row" style="margin-top: 20px;">
			<div class="col-md-6"></div>
			<div class="col-md-3" id="show_select_dropdown" <?php if ($row_tutors_chk = $DB->get_records_sql($chk_tutors_chk)) {
																echo "style='display: block;'";
															} else {
																echo "style='display: none;'";
															} ?>>
				<?php

				$sqli_tutors_list =  "SELECT * FROM {assign_subs_tutors} WHERE deleted_status=0";
				$row_tutors_list = $DB->get_records_sql($sqli_tutors_list);
				$name = array_column($row_tutors_list, 'name', 'id');

				?>
				<select name="tutor_id[]" id="tutor_id" class="selectpicker form-control" multiple data-live-search="true">
					<?php foreach ($name as $key => $val) {  ?>
						<option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
					<?php } ?>
				</select>
			</div>

			<div class="col-md-3" <?php if ($row_tutors_chk = $DB->get_records_sql($chk_tutors_chk)) {
										echo "style='display: block;'";
									} else {
										echo "style='display: none;'";
									} ?>>
				<button type="button" class="btn-sm btn-danger delete_tutor">Delete tutor</button>
			</div>
		</div>



		<script type="text/javascript">
			$(function() {
				$(document).on("click", ".delete_tutor", function() {
					var tutors = $("#tutor_id :selected").map((_, e) => e.text).get();
					var tutors_id = $("#tutor_id :selected").map((_, e) => e.value).get();
					if (tutors_id != '') {
						var ok = confirm("Are you sure to delete " + tutors)
						if (ok) {
							$.ajax({
								type: "POST",
								url: "<?php echo $CFG->wwwroot . '/local/assignment_subscription/delete_tutors.php'; ?>",
								data: {
									"tutors": tutors_id
								},
								success: function(res) {
									alert(res);
									window.location = "<?php echo $CFG->wwwroot . '/local/assignment_subscription/plugin_setting.php'; ?>";
								}
							});
						}
					} else {
						alert("Please firstly select the tutors");
					}
				});
			});
		</script>

		<hr>
		<!-- Default teacher list -->

		<div class="row">
			<div class="col-md-6"><b>Default Tutor Assign</b></div>
			<div class="col-md-6">

				<ul id="dynamic_field" class="nav ">
					<p style=""><a href="javascript:void(0);" class="add_custom_field_default_tutor" id="add_custom_field_default_tutor" title="Add field">Add Custom Field</a></p>
					<br>

				</ul>



				<?php

				$fetch_default_tutor_loop = "SELECT DISTINCT(tutor_id) FROM {assign_subs_default_tutor}";
				$get_default_tutor_loop = $DB->get_records_sql($fetch_default_tutor_loop);

				$counter = 0;

				if ($get_default_tutor_loop) {
					foreach ($get_default_tutor_loop as $tut) {
						$tutor_id = $tut->tutor_id;
				?>

						<div class="customtutor" style="margin-top: 5px;">
							<ul id="dynamic_field" class="nav ">
								<li style="margin-right: 2px;">
									<select class="form-control" name="default_tutor_<?php echo $counter; ?>[]" required="required" style="width: 200px;">
										<?php
										$fetch_tutor_list = "SELECT * FROM {assign_subs_tutors} WHERE deleted_status=0";
										$get_tutor_list = $DB->get_records_sql($fetch_tutor_list);
										echo '<option value="0" >Select Tutor</option>';
										foreach ($get_tutor_list as $vali) {
											echo '<option value="' . $vali->id . '" ' . (($vali->id == $tutor_id) ? "selected" : "") . '> ' . $vali->name . '</option>';
										?>

										<?php } ?>

									</select>
								</li>
								<li style="margin-right: 2px;">
									<select class="form-control js-example-basic-single" name="course_id_default_<?php echo $counter; ?>[]" multiple style="width: 200px;">
										<?php

										$fetch_course_list = "SELECT * FROM {course} WHERE id > 1 ";
										$get_course_list = $DB->get_records_sql($fetch_course_list);

										if ($get_course_list) {
											foreach ($get_course_list as $vall) {

												$fetch_default_course = "SELECT * FROM {assign_subs_default_tutor} WHERE tutor_id='$tutor_id' AND course_id=" . $vall->id;
												$get_default_course = $DB->get_record_sql($fetch_default_course);

												$fetch_default_course_tut = "SELECT * FROM {assign_subs_default_tutor} WHERE  course_id=" . $vall->id;
												$get_default_course_tut = $DB->get_record_sql($fetch_default_course_tut);

												echo '<option value="' . $vall->id . '" ' . ($get_default_course ? 'selected' : '') . ' ' . ($get_default_course_tut ? '' : '') . '>' . $vall->fullname . '</option>';
											}
										} ?>
									</select>
								</li>
							</ul>
							<a style="float: right; transform: translate(-40px,-35px);color: white;" href="delete_default_tutor.php?tutor_id=<?php echo $tutor_id; ?>" class="btn btn-sm btn-danger  rounded-pill" onclick="return confirm('Are you sure?')" title="Remove field">Delete</a>

						</div>


				<?php
						$counter++;
					}
				} ?>


				<div class="field_wrapper_custom_fields"></div>

			</div>
		</div>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
		


		<script type="text/javascript">

			var jQuery_3_6_4 = $.noConflict(true); 
			
			jQuery_3_6_4(document).ready(function() {

				jQuery_3_6_4(".js-example-basic-date-range").select2({ placeholder: "Select Submission",});


				jQuery_3_6_4(".js-example-basic-single").select2({
					placeholder: "Select the course",
				});

				// disableprevcustomtutor();
				var add_custom_field_default_tutor = jQuery_3_6_4('.add_custom_field_default_tutor');
				var field_wrapper_custom_fields = jQuery_3_6_4('.field_wrapper_custom_fields');
				var input_count = 0;
				var count = 1;
				var totallen = <?php echo $counter; ?>;
				// Add button dynamically
				jQuery_3_6_4(add_custom_field_default_tutor).click(function() {
					var alloptedcourse_list = [];
					var checkvalid = true;
					jQuery_3_6_4('.js-example-basic-single, .js-example-basic-single-new').each(function(e) {
						// console.log('jQuery_3_6_4(this).val()', jQuery_3_6_4(this).val());
						if (jQuery_3_6_4(this).val().length > 0) {
							alloptedcourse_list.push(jQuery_3_6_4(this).val());
						} else {
							checkvalid = false;
						}

					});
					if (checkvalid) {
						jQuery_3_6_4.ajax({
							type: "post",
							data: {
								alloptedcourse_list: alloptedcourse_list.join(),
								totallen: totallen
							},
							url: "<?php echo $CFG->wwwroot . '/local/assignment_subscription/fetch_courses_default.php'; ?>",
							context: document.body,
							success: function(response) {
								totallen++;
								var responsedata = JSON.parse(response);
								// console.log("responsedata- ", responsedata);
								if (responsedata.coursehtml) {
									var new_field_html = '<div  id="tutor_list_container' + responsedata.totallen + '" class="customtutor" style="margin-top: 5px;"><ul id="dynamic_field" class="nav custom_fields"><li style="margin-right: 2px;"><select style="width: 200px;" id="tutor_list' + responsedata.totallen + '" class="form-control courseforlimit_default" name="default_tutor_' + responsedata.totallen + '[]" required="required">' + responsedata.tutorhtml + '</select></li><li style="margin-right: 2px;">' + responsedata.coursehtml + '</li></ul><a style="float: right; transform: translate(-40px,-35px); color: white;" href="javascript:void(0);" class="btn btn-sm btn-danger remove_input_button rounded-pill" title="Remove field">Delete</a></div>';
									jQuery_3_6_4(".field_wrapper_custom_fields").append(new_field_html);
									setTimeout(() => {
										// console.log(new_field_html);
										jQuery_3_6_4(".js-example-basic-single-new").select2({
											placeholder: "Select the course",
										});
									}, 120);

								}

								

							}
						});
					} else {
						alert("Please select tutor and courses in all existing dropdown");
					}
				});
				// Remove dynamically added button
				jQuery_3_6_4(field_wrapper_custom_fields).on('click', '.remove_input_button', function(e) {
					e.preventDefault();
					jQuery_3_6_4(this).parent('div').remove();
					input_count--;
					if (input_count == 0) {
						jQuery_3_6_4("#custom").hide();
					}
				});
			});
		</script>


		<?php 

			$tabs = get_config('local_assignment_subscription', 'tabs');
			$duration = get_config('local_assignment_subscription', 'duration');
			// echo "Duration: $duration";
			// var_dump(strpos($duration , ","));
			$selectedtabs = explode(",",$tabs);
			$durationstart = "";
			$durationend = "";
			if(strpos($duration , ",") !== false){
				$durationdates = explode(",", $duration);
				$durationstart = date("Y-m-d", $durationdates[0]);
				$durationend = date("Y-m-d", $durationdates[1]);
			}
		?>

		<!-- Submission dashboard date range -->
		<div class="row">
			<div class="col-md-6"><b>Submission Dashboard Date Range</b></div>
			<div class="col-md-6">
				<div class="" style="margin-top: 5px;">
					<ul id="" class="nav">
						<li style="margin-right: 2px;">
							<select class="form-control js-example-basic-date-range" name="tabs[]" required  multiple style="width: 200px;">
								<option value="1" <?php echo (in_array(1, $selectedtabs)?"Selected":''); ?>>Archive</option>
								<option value="2" <?php echo (in_array(2, $selectedtabs)?"Selected":''); ?>>General</option>
								<option value="3" <?php echo (in_array(3, $selectedtabs)?"Selected":''); ?>>Priority</option>
							</select>
						</li>
						<li style="margin-right: 2px;">
							<select class="form-control" name="duration" id="filter_date" required style="width: 200px;">
								<option value="All Entries" <?php echo ($duration == "All Entries"?"Selected":''); ?>>All Entries</option>
								<option value="1 months"    <?php echo ($duration == "1 months"?"Selected":''); ?>>1 Month</option>
								<option value="4 months"    <?php echo ($duration == "4 months"?"Selected":''); ?>>4 Months</option>
								<option value="8 months"    <?php echo ($duration == "8 months"?"Selected":''); ?>>8 Months</option>
								<option value="1 year"      <?php echo ($duration == "1 year"?"Selected":''); ?>>1 Year</option>
								<option value="custom_date" <?php echo (strpos($duration , ",") !== false?"Selected":''); ?> id="custom_date">Custom date</option>
							</select>
						</li>
					</ul>
				</div>
			</div>
		</div>
		

   		<!-- Custom date field -->
		<div class="row">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-6" id="custom_start_date">
						<p><b>Start Date</b></p>
						<input type="date" name="custom_start_date" id="custom_start_date_val" class="form-control" value="<?php echo $durationstart; ?>" > 
					</div>
					<div class="col-md-6" id="custom_end_date">
						<p><b>End Date</b></p>
					    <input type="date" name="custom_end_date"  id="custom_end_date_val" class="form-control" value="<?php echo $durationend; ?>" >
					</div>
				</div>
			</div>
		</div>


		<script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


		<!-- Default teacher list end -->
		<hr>


		<?php

		$chk_sub_lmt = "SELECT * FROM {assign_subs_sub_limit} WHERE status = 1 ORDER BY id DESC LIMIT 1";
		$row_sub_lmt = $DB->get_record_sql($chk_sub_lmt);


		$sql_sub_lmt_dbs = "SELECT * FROM {assign_subs_sub_limit} WHERE status = 1 AND course_id='All Courses'";
		$row_sub_lmt_dbs = $DB->get_record_sql($sql_sub_lmt_dbs);

		?>


		<div class="row">
			<div class="col-md-6"><b>Submission Limit</b></div>
			<div class="col-md-6">

				<input type="checkbox" name="sub_limit_status" data-toggle="toggle" data-onstyle="danger" value="1" id="sub_limit_status" <?php if (($row_sub_lmt->status) == 1) {
																																				echo "checked";
																																			} ?>>

				<ul id="dynamic_field" class="nav custom_fields" <?php if (($row_sub_lmt->status) == 1) {
																		echo 'style="display: block; margin-top: 10px;"';
																	} else {
																		echo 'style="display: none; margin-top: 10px;"';
																	} ?>>
					<li style="margin-right: 2px;">
						<input type="text" name="course_id[]" class="form-control" value="All Courses" style="width: 120px;" readonly>
					</li>
					<li style="margin-right: 2px;">
						<input type="number" name="sub_limit[]" class="form-control" value="<?php if (empty(($row_sub_lmt_dbs->sub_limit))) {
																								echo 5;
																							} else {
																								echo $row_sub_lmt_dbs->sub_limit;
																							} ?>">
					</li>
					<li style="margin-right: 2px;">
						<select name="sub_duration[]" class="form-control">
							<option value="Weekly" <?php if (($row_sub_lmt_dbs->sub_duration) == 'Weekly') {
														echo "selected";
													} ?>>Weekly</option>
							<option value="Monthly" <?php if (($row_sub_lmt_dbs->sub_duration) == 'Monthly') {
														echo "selected";
													} ?>>Monthly</option>
							<option value="Yearly" <?php if (($row_sub_lmt_dbs->sub_duration) == 'Yearly') {
														echo "selected";
													} ?>>Yearly</option>
						</select>
					</li>

					<label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
					<p style="margin: 3px;"><a href="javascript:void(0);" class="add_input_button" title="Add field">Add Custom Field</a></p>
					<p style="margin: 3px;">Default: All courses <?php echo $row_sub_lmt_dbs->sub_limit;
																	echo " ";
																	echo $row_sub_lmt_dbs->sub_duration; ?></p>

				</ul>



				<script type="text/javascript">
					$('#sub_limit_status').change(function() {
						if ($(this).is(':checked')) {
							$(".custom_fields").show();
							$(".table-custom").show();

						} else {
							$(".custom_fields").hide();
						}
					});
				</script>


				<script type="text/javascript">
					$('.add_input_button').click(function() {
						$("#custom").show();
					});
				</script>


				<script type="text/javascript">
					$(document).ready(function() {

						var max_fields = 20;
						var add_input_button = $('.add_input_button');
						var field_wrapper = $('.field_wrapper');
						var input_count = 0;

						// Add button dynamically
						$(add_input_button).click(function() {
							var alloptedcourse = [];
							var checkvalid = true;
							$(".field_wrapper").find('.courseforlimit').each(function(e) {
								console.log("$(this).val()- ", $(this).val())
								console.log("$(this).val()- ", $(this).attr("id"))
								if ($(this).val()) {
									alloptedcourse.push($(this).val());
								} else {
									checkvalid = false;
								}
							});
							if (checkvalid) {
								$.ajax({
									type: "post",
									url: "<?php echo $CFG->wwwroot . '/local/assignment_subscription/fetch_courses.php'; ?>",
									data: {
										input_count: input_count,
										alloptedcourse: alloptedcourse.join()
									},
									context: document.body,
									success: function(response) {
										if (response) {
											var new_field_html = '<div style="margin-top: 5px;"><ul id="dynamic_field" class="nav custom_fields"><li style="margin-right: 2px;" id="course_id' + input_count;
											new_field_html += '">' + response + '</li><li style="margin-right: 2px;"><input type="number" name="sub_limit[]" class="form-control" value="" required style="width: 120px;"></li><li style="margin-right:2px;"><select name="sub_duration[]" class="form-control" required><option value="Weekly" >Weekly</option><option value="Monthly">Monthly</option><option value="Yearly" >Yearly</option></select></li></ul><a style="float: right; transform: translate(0px,-33px);color:white;" href="javascript:void(0);" class="btn btn-sm btn-danger remove_input_button" title="Remove field">X</a></div>';
											$(".field_wrapper").append(new_field_html);
										} else {
											alert("course not found");
										}
									}
								});
							} else {
								alert("Please select course in all existing course dropdown");
							}
						});


						// Remove dynamically added button
						$(field_wrapper).on('click', '.remove_input_button', function(e) {
							e.preventDefault();
							$(this).parent('div').remove();
							input_count--;
							//  alert(input_count);
							if (input_count < 0) {
								$("#custom").hide();
							}


						});

					});
				</script>



			</div>
		</div>
		<hr>

		<div class="row custom_fields">
			<div class="col-md-6" id="custom" style="display: none;"><b>Custom Submission Limit</b></div>
			<div class="col-md-6 field_wrapper"></div>
		</div>



		<div class="row custom_fields ">
			<div class="col-md-6"><b></b></div>
			<div class="col-md-6 table-custom" style="<?php if ($row_sub_lmt->status == 1) {
															echo "display:block";
														} else {
															echo "display:none";
														} ?>">
				<table class="table table-bordered table-striped">
					<?php

					$sql_sub_lmt_dbs = "SELECT * FROM {assign_subs_sub_limit} WHERE course_id!='All Courses'";
					$row_sub_lmt_dbs = $DB->get_records_sql($sql_sub_lmt_dbs);

					if ($row_sub_lmt_dbs) { ?>
						<thead>
							<tr>
								<th>Course</th>
								<th>Limit</th>
								<th>Duration</th>
								<th>Action</th>
							</tr>
						</thead>
						<?php foreach ($row_sub_lmt_dbs as $sub_lmt) { ?>
							<tr>
								<td><?php $course = get_course($sub_lmt->course_id);
									echo  $course->fullname;  ?></td>
								<td><?php echo $sub_lmt->sub_limit; ?></td>
								<td><?php echo $sub_lmt->sub_duration; ?></td>
								<td><a href="delete_sub_limit.php?DelSubLim=<?php echo $sub_lmt->id; ?>" class="btn btn-sm btn-danger rounded-pill">Delete</a></td>
							</tr>
					<?php }
					} ?>
				</table>
			</div>
		</div>




		<div class="row" style="margin-top: 10px;">
			<div class="col-md-6"></div>
			<div class="col-md-3"><input type="submit" name="submit" class="btn btn-primary" value="Save changes"></div>
			<div class="col-md-3"></div>
		</div>

	</form>

</div>

<script type="text/javascript">
	$("#filter_date").change(function(){
	  var value = $(this).val();
		if(value == 'custom_date'){
		    $("#custom_start_date").show();
		    $("#custom_end_date").show();
		}else{
			$("#custom_start_date").hide();
			$("#custom_end_date").hide();
		}
	}); 
	$("#filter_date").trigger("change");
</script>

<?php

echo $OUTPUT->footer();
