<?php

namespace local_assignment_subscription\task;


/**
 * An example of a scheduled task.
*/

defined('MOODLE_INTERNAL') || die();

class user_subscription extends \core\task\scheduled_task {
    

    // Private variables for team meeting 
    private $TenantID;
    private $ClientID;


    public function get_name() {
        return "Assignment Subscription - Renewal Checking Tracking";
    }


    public function execute() {

        global $DB, $CFG;

        echo "\n\n\nAssignment Subscription - Renewal Checking Tracking Starts Here\n";

        require_once($CFG->dirroot.'/local/assignment_subscription/stripe-payment/init.php');

        $publishableKey =  get_config('local_assignment_subscription','publishableKey');
        $secretKey = get_config('local_assignment_subscription','secretKey');    
        $stripe = new \Stripe\StripeClient($secretKey);

        echo "<pre>";
        // Fetch the Checkout Session to display the JSON result on the success page 
        $subs_user = $DB->get_records('assign_subs_transaction', array('stripe_canceled_status' => 0));
        
        foreach ($subs_user as $data) {
            
            if (!empty($subscription = $data->stripe_subscription_id)) {
                try {
                    $paymentIntent = $stripe->subscriptions->retrieve($subscription);
                    if($paymentIntent->status == 'active' && empty($paymentIntent->canceled_at)){
                        $subslist = $stripe->invoices->upcoming(['customer' => $paymentIntent->customer,]); 
                        if(time() >= $subslist->period_end){
                            $assign_subs = $DB->get_record('assign_subs_users', array('userid' => $data->userid));
                            $obj = new \stdClass();
                            $obj->id = $assign_subs->id;
                            $obj->end_date = $subslist->period_end;
                            $aa = $DB->update_record('assign_subs_users', $obj, $bulk=false);
                            $assign_subs2 = $DB->get_record('assign_subs_transaction', array('userid' => $data->userid));
                            $obj2 = new \stdClass();
                            $obj2->id = $assign_subs2->id;
                            $obj2->plan_period_end = $subslist->period_end;
                            $bb = $DB->update_record('assign_subs_transaction', $obj2, $bulk=false);

                        } 
                        // print_r($paymentIntent);

                    }

                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $api_error = $e->getMessage();
                }
    
    
            }


        }


        echo "<br>";


           

        echo "\n\n\nAssignment Subscription - Renewal Checking Tracking Ends Here\n";
    }




}


