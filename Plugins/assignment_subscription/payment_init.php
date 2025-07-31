<?php 
require_once('../../config.php');

global $DB, $USER;
require_login();

// Include the Stripe PHP library 
require_once 'stripe-payment/init.php'; 
$ptype = $_GET['ptype'];

require_once 'payment_config.php'; 

// Set API key 
$stripe = new \Stripe\StripeClient(STRIPE_API_KEY); 
 
$response = array( 
    'status' => 0, 
    'error' => array( 
        'message' => 'Invalid Request!'    
    ) 
); 
 

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $input = file_get_contents('php://input'); 
    $request = json_decode($input);     
} 
 


if (json_last_error() !== JSON_ERROR_NONE) { 
    http_response_code(400); 
    echo json_encode($response); 
    exit; 
} 
 

if(!empty($request->createCheckoutSession)){ 
  
    // Create new Checkout Session for the order 
    try { 

        if($ptype == 'recurring'){

            $productName = $recurring_duration." Recurring";
            $stripeAmount = round($recurring_cost*100, 2);

            $checkout_session = $stripe->checkout->sessions->create([ 
            'line_items' => [[
            'price' => $stripe_recurring_price_id,
            'quantity' => 1,
            ]],

            'mode' => 'subscription', 
            'success_url' => STRIPE_SUCCESS_URL.'?qtoken={CHECKOUT_SESSION_ID}&s_type=2', 
            'cancel_url' => STRIPE_CANCEL_URL, 
            ]); 

        }else{

            $productName = "Yearly One-off";
            $stripeAmount = round($one_off_cost*100, 2);

            $checkout_session = $stripe->checkout->sessions->create([ 
            'line_items' => [[ 
            'price_data' => [ 
            'product_data' => [ 
            'name' => $productName, 
            'metadata' => [ 
            'pro_id' => $stripe_one_off_product_id 
            ] 
            ], 

            'unit_amount' => $stripeAmount, 
            'currency' => $stripe_currency, 
            ], 
            'quantity' => 1 
            ],],

            'mode' => 'payment', 
            'success_url' => STRIPE_SUCCESS_URL.'?qtoken={CHECKOUT_SESSION_ID}&s_type=1', 
            'cancel_url' => STRIPE_CANCEL_URL, 
            ]); 


        }
        


    } catch(Exception $e) {  
        $api_error = $e->getMessage();  
    } 
     
    if(empty($api_error) && $checkout_session){ 
        $response = array( 
            'status' => 1, 
            'message' => 'Checkout Session created successfully!', 
            'sessionId' => $checkout_session->id 
        ); 
    }else{ 
        $response = array( 
            'status' => 0, 
            'error' => array( 
            'message' => 'Checkout Session creation failed! '.$api_error    
            ) 
        ); 
    } 
} 
 
// Return response 
echo json_encode($response); 
 
?>