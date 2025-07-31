<?php
    
$publishableKey =  get_config('local_assignment_subscription','publishableKey');
$secretKey = get_config('local_assignment_subscription','secretKey');


define('STRIPE_API_KEY', $secretKey); 
define('STRIPE_PUBLISHABLE_KEY', $publishableKey); 
define('STRIPE_SUCCESS_URL', $CFG->wwwroot.'/local/assignment_subscription/payment_success.php');   //Payment success URL 
define('STRIPE_CANCEL_URL',  $CFG->wwwroot.'/local/assignment_subscription/payment_cancel.php');    //Payment cancel URL 

$query_setting = 'SELECT * FROM {assign_subs_settings}';
$row_setting = $DB->get_record_sql($query_setting);

$recurring_cost = $row_setting->recurring_cost;
$recurring_duration = $row_setting->recurring_duration;
$stripe_currency = $row_setting->stripe_currency;
$one_off_cost = $row_setting->one_off_cost;
$stripe_recurring_price_id = $row_setting->stripe_recurring_price_id;
$stripe_recurring_product_id = $row_setting->stripe_recurring_product_id;
$stripe_one_off_product_id = $row_setting->stripe_one_off_product_id;





