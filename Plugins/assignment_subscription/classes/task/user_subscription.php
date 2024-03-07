<?php

namespace local_assignment_subscription\task;


/**
 * An example of a scheduled task.
*/

defined('MOODLE_INTERNAL') || die();

class user_subscription extends \core\task\scheduled_task {
    



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
                        
                        // print_r($paymentIntent);
                        $current_period_end = $paymentIntent->current_period_end;
                        $current_period_start = $paymentIntent->current_period_start;
                        if(!empty($subslist->period_end)){
                            // echo "userid";
                            $assign_subs = $DB->get_record('assign_subs_users', array('userid' => $data->userid));
                            $userdata = $DB->get_record('user', array('id' => $assign_subs->userid));

                            $userobj = new \stdClass();
                            $userobj->fullname = $userdata->firstname." ".$userdata->lastname;;
                            $userobj->id = $userdata->id;
                            $userobj->site_email = $userdata->email;
                            $userobj->stripe_email_used = $subslist->customer_email;
                            $userobj->period_start = date("d-m-Y", $subslist->period_start);
                            $userobj->period_end = date("d-m-Y", $subslist->period_end);
                            $userobj->current_start_date = date("d-m-Y", $assign_subs->start_date);
                            $userobj->current_event_date = date("d-m-Y", $assign_subs->end_date);

                            print_r($userobj);

                            $obj = new \stdClass();
                            $obj->userid = $assign_subs->userid;
                            $obj->start_date = $assign_subs->start_date;
                            $obj->end_date = $assign_subs->end_date;
                            $obj->cost = $assign_subs->cost;
                            $obj->subscription_method = $assign_subs->subscription_method;
                            $obj->subscription_duration = $assign_subs->subscription_duration;
                            $obj->date_of_update = time();

                            $result = $DB->record_exists('assign_subs_history', array('start_date' => $subslist->period_start, 'end_date' => $subslist->period_end, 'userid' => $assign_subs->userid));

                            if($result != 1){
                                $DB->insert_record('assign_subs_history', $obj);
                            }
                          

                            $obj->id = $assign_subs->id;
                            $obj->start_date = $subslist->period_start;
                            $obj->end_date = $subslist->period_end;
                            $obj->modified_date = time();
                            $obj->modified_by = $data->userid;
                            $obj->status = 1;
                            $aa = $DB->update_record('assign_subs_users', $obj, $bulk=false);


                            $assign_subs2 = $DB->get_record('assign_subs_transaction', array('userid' => $data->userid));
                            $obj2 = new \stdClass();
                            $obj2->id = $assign_subs2->id;
                            $obj2->plan_period_start = $subslist->period_start;
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


