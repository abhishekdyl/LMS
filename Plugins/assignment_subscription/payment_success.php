<?php
// Include database connection file  
include_once '../../config.php';

global $DB, $USER, $PAGE;

$PAGE->requires->jquery();
require_login();
$current_logged_in_user =  $USER->id;


// Include configuration file  
require_once 'payment_config.php';

$PAGE->set_title('Assignment Subscription');
$PAGE->set_heading('Assignment Subscription');


$query = 'SELECT * FROM {assign_subs_users}';
$all_users = $DB->get_records_sql($query);


$payment_id = $statusMsg = '';
$status = 'error';


// Check whether stripe checkout session is not empty 
if (!empty($_GET['qtoken'])) {

    $session_id = $_GET['qtoken'];
    $s_Type = $_GET['s_type'];



    // Fetch transaction data from the database if already exists 
    $sqlQ = "SELECT * FROM {assign_subs_transaction} WHERE stripe_checkout_session_id='$session_id'";
    $result = $DB->get_record_sql($sqlQ);



    if (!empty($result->id)) {

        // Transaction details 
        $transData       =    $result;
        $payment_id      =    $transData->id;
        $transactionID   =    $transData->txn_id;
        $paidAmount      =    $transData->paid_amount;
        $paidCurrency    =    $transData->paid_amount_currency;
        $payment_status  =    $transData->payment_status;
        $customer_name   =    $transData->customer_name;
        $customer_email  =    $transData->customer_email;

        $status = 'success';
        $statusMsg = 'Your Payment has been Successful!';
    } else {

        // Include the Stripe PHP library 
        require_once 'stripe-payment/init.php';
        // Set API key 
        $stripe = new \Stripe\StripeClient(STRIPE_API_KEY);

        // Fetch the Checkout Session to display the JSON result on the success page 
        try {

            $checkout_session = $stripe->checkout->sessions->retrieve($session_id);
            $payment_intent_id = $checkout_session->payment_intent;
            $subscription = $checkout_session->subscription;

            // Customer full details
            $customer_details = $checkout_session->customer_details;
            $customer_name = !empty($customer_details->name) ? $customer_details->name : '';
            $customer_email = !empty($customer_details->email) ? $customer_details->email : '';
        } catch (Exception $e) {
            $api_error = $e->getMessage();
        }





        // One-off payment
        if (!empty($payment_intent_id)) {

            try {
                $paymentIntent = $stripe->paymentIntents->retrieve($payment_intent_id);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $api_error = $e->getMessage();
            }


            if ($paymentIntent->status == 'succeeded') {



                // Transaction details  
                $transactionID = $paymentIntent->id;
                $paidAmount = $paymentIntent->amount;
                $paidAmount = ($paidAmount / 100);
                $paidCurrency = $paymentIntent->currency;
                $payment_status = $paymentIntent->status;
                $start_date = strtotime(date("d F Y 00:00:00"));
                $current_period_end = strtotime("+1 year", $start_date);
                $sub_duration = $current_period_end - $start_date;


                // Check if any transaction data is exists already with the same TXN ID 
                $sqlQ = "SELECT * FROM {assign_subs_transaction} WHERE txn_id='$transactionID'";
                $result = $DB->get_record_sql($sqlQ);


                if (!empty($result - id)) {
                    $payment_id = $result->id;
                }
            }
        }



        // Recurring payment
        if (!empty($subscription)) {


            try {
                $paymentIntent = $stripe->subscriptions->retrieve($subscription);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $api_error = $e->getMessage();
            }


            $collection_method = $paymentIntent->collection_method;
            $current_period_end = $paymentIntent->current_period_end;
            $current_period_start = $paymentIntent->current_period_start;
            $customer = $paymentIntent->customer;
            $default_payment_method = $paymentIntent->default_payment_method;
            $transactionID = $paymentIntent->items->data[0]->id;
            $active = $paymentIntent->items->data[0]->plan->active;
            $amount = $paymentIntent->items->data[0]->plan->amount;
            $paidAmount = ($amount / 100);
            $paidCurrency = $paymentIntent->items->data[0]->plan->currency;
            $interval = $paymentIntent->items->data[0]->plan->interval;
            $interval_count = $paymentIntent->items->data[0]->plan->interval_count;
            $product = $paymentIntent->items->data[0]->plan->product;
            $price_id = $paymentIntent->items->data[0]->price->id;
            $latest_invoice = $paymentIntent->latest_invoice;
            $start_date = $paymentIntent->start_date;
            $payment_status = $paymentIntent->status;


            // echo "<pre>";
            // print_r($paymentIntent);
            // die();

        }
    }


    // Insert transaction data into the database 
    $record_ins = new stdClass();
    $record_ins->userid = $current_logged_in_user;
    $record_ins->customer_name = $customer_name;
    $record_ins->customer_email = $customer_email;
    $record_ins->paid_amount = $paidAmount;
    $record_ins->paid_amount_currency = $paidCurrency;
    $record_ins->txn_id = $transactionID;
    $record_ins->payment_status = $payment_status;
    $record_ins->stripe_checkout_session_id = $session_id;
    $record_ins->created = strtotime(date("d F Y H:i:s"));
    $record_ins->modified = strtotime(date("d F Y H:i:s"));
    $record_ins->stripe_subscription_id = $subscription;
    $record_ins->stripe_payment_intent_id = $payment_intent_id;
    $record_ins->plan_interval = $interval;
    $record_ins->plan_interval_count = $interval_count;
    $record_ins->plan_period_start = $start_date;
    $record_ins->plan_period_end = $current_period_end;
    $DB->insert_record('assign_subs_transaction', $record_ins, $returnid = true);


    // Insert in primary table
    $record_ins_subs = new stdClass();
    $record_ins_subs->userid = $current_logged_in_user;
    $record_ins_subs->subscription_method = 'Online Subscription';


    $query_setting = 'SELECT * FROM {assign_subs_settings}';
    $row_setting = $DB->get_record_sql($query_setting);
    $recurring_duration = $row_setting->recurring_duration;

    $sub_duration = $current_period_end - $start_date;
    $users_subscription = $DB->get_record('assign_subs_users', array('userid'=> $current_logged_in_user), '*');
    


    if(!empty($users_subscription)){
        $record_ins_subs->modified_date = $users_subscription->modified_date;
        $record_upd = new stdClass();
        $record_upd -> userid = $users_subscription->userid;
        $record_upd -> start_date = $users_subscription->start_date;
        $record_upd -> end_date = $users_subscription->end_date;
        $record_upd -> cost = $users_subscription->cost;
        $record_upd -> subscription_method = $users_subscription->subscription_method;
        $record_upd -> subscription_duration = $users_subscription->subscription_duration;
        $record_upd -> date_of_update = strtotime(date("d F Y H:i:s"));
        $DB -> insert_record('assign_subs_history', $record_upd, false);
        $DB -> delete_records('assign_subs_users', array('id' => $users_subscription->id)); //  Delete from primary table
       

        // echo "<pre>";
        // var_dump($record_ins_subs);
        // die();

    }else{
        $record_ins_subs->modified_date = strtotime(date("d F Y H:i:s"));
    }

        $record_ins_subs->start_date = $start_date;
        $record_ins_subs->end_date = $current_period_end;
        $record_ins_subs->subscription_duration = $sub_duration;
        $record_ins_subs->status = 1;
        $record_ins_subs->cost = $paidAmount;
        $record_ins_subs->created_date = strtotime(date("d F Y H:i:s"));
        $record_ins_subs->modified_by = $current_logged_in_user;
        $record_ins_subs->update_history = 'None';
        $record_ins_subs->subscription_type = $s_Type;
        $DB->insert_record('assign_subs_users', $record_ins_subs, false);

    if (!empty($_SESSION['redirectedtopayfrom'])) {
        $urltogo = $_SESSION['redirectedtopayfrom'];
    } else {
        $urltogo = $CFG->wwwroot . '/local/assignment_subscription/index.php';
    }
}

echo $OUTPUT->header();

?>




<?php if ($payment_status == 'succeeded' || $payment_status == 'active') { ?>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous"> -->
    <style type="text/css">
        div#region-main-box .container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            height: 100%;
        }

        /* body#page-local-assignment_subscription-payment_success {
            padding-right: 0 !important;
        } */

        .overlay {
            position: absolute;
            background: #ffffff;
            opacity: 0.7;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            filter: blur(20px);
            z-index: 999;
        }

        div#region-main-box {
            min-height: 600px;
        }

        div#myModal {
            padding: 15px;
        }

        section#region-main>div {
            height: 100%;
        }

        div#region-main-box .container>div {
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        div#region-main-box .container .overlay+div {
            width: 100%;
            height: 100%;
        }

        .row.pad {
            z-index: 999999;
            position: relative;
            padding-top: 70px;
            padding-bottom: 70px;
            transition: all 0.6s;
            animation: 1s ease-out 0s 1 slideInFromLeft;
        }

        @keyframes slideInFromLeft {
            0% {
                scale: 0;
            }

            100% {
                scale: 1;
            }
        }



        section#region-main {
            padding: 0px;
            overflow-x: hidden;
            height: 100%;

        }


        .container {
            font-family: Georgia;
        }

        .pad {
            padding-left: 25%;
            padding-right: 25%;
        }

        .fontsize {
            font-size: 30px;
        }

        @media screen and (max-width: 1020px) {
            .pad {
                padding-left: 15%;
                padding-right: 15%;
            }

            .fontsize {
                font-size: 16px;
            }
        }


        @media (max-width: 767px) {
            nav.fixed-top.navbar.navbar-light.bg-white.navbar-expand.moodle-has-zindex button.btn.nav-link.float-sm-left.mr-1.btn-light.bg-gray 
            {
                display: none;
            }

            div#nav-drawer 
            {
                display: none;
            }
        }


        .modal-backdrop {
            background-color: transparent !important;
        }

    </style>

    <div class="overlay"></div>
    <div class="container">
        <div align="center" style="background-image: url('img/bgImage.png'); padding: 10px; background-repeat: auto; background-size: cover;">
            <div class="row pad">
                <div class="col-md-12" class="modal hide fade" id="myModal" style="background-color: #f0f8ff; border-radius: 30px;" align="center">
                    <div align="center"><img src="img/correct.png" alt="Something is wrong" style="background-size: cover;" /></div>

                    <p><b class="fontsize">Congratulations!</b></p>
                    <p>Your upgrade was successfull!</p>
                    <p>
                        <a href="<?php echo $urltogo; ?>" style="background-color: red; color: white; padding: 10px; text-decoration: none;  box-shadow: 1px 2px #888888; border-radius: 8px;">Continue to submission</a>
                    </p>
                </div>
            </div>
        </div>
    </div>



<?php } else { ?>
    <h1 class="error">Your Payment has been failed!</h1>
<?php } ?>


<?php echo $OUTPUT->footer(); ?>

<script type="text/javascript">
    $(window).on('load', function() {
        $('#myModal').modal('show');
    });
</script>