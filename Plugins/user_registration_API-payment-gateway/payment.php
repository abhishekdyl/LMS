<?php
require_once('../../config.php');
global $DB, $CFG;
 
$id = required_param('id', PARAM_RAW);
$reg_id = base64_decode($id);

require_once($CFG->dirroot.'/local/user_registration/vendor/autoload.php');
use TapPayments\GoSell;
GoSell::setPrivateKey("sk_test_KgVxzicaJXL6NsGIwWhmSqMQ");

if($DB->record_exists('lcl_individual_enrollment', array('registration_id'=>$reg_id))) {
  $user_details = $DB->get_record('lcl_individual_enrollment',array('registration_id'=> $reg_id));
  $courseid = $user_details->course_id;
  $name = $user_details->name;
  $mobile_number = $user_details->mobile_number;
  $email = $user_details->email;
  $course_price = $user_details->course_price; 
}

if($DB->record_exists('lcl_corporate_enrollment', array('registration_id'=>$reg_id))) {
  $user_details = $DB->get_record('lcl_corporate_enrollment',array('registration_id'=> $reg_id));
  $courseid = $user_details->course_id;
  $name = $user_details->client_name;
  $mobile_number = $user_details->mobile_number;
  $email = $user_details->email;
  $course_price = $user_details->course_price; 
}

if($course_price>0){
 $txn_id = "txn_".rand(99, 999999999); 
 $order_id = "order_".$id;
 $charge = GoSell\Charges::create(
   [
      "amount"=> $course_price,
      "currency"=> "BHD",
      "threeDSecure"=> true,
      "save_card"=> false,
      "description"=> "Al Mashreq Training",
      "statement_descriptor"=> "Al Mashreq Training Sample Statement",
      "metadata"=> [
        "udf1"=> $reg_id,
        "udf2"=> "Al Mashreq Training"
      ],
      "reference"=> [
        "transaction"=> $txn_id,
        "order"=> $order_id
      ],
      "receipt"=> [
        "email"=> true,
        "sms"=> true
      ],
      "customer"=> [
        "first_name"=> $name,
        "middle_name"=> " ",
        "last_name"=> " ",
        "email"=> $email,
        "phone"=> [
          "number"=> $mobile_number
        ]
      ],
      "source"=> [
        "id"=> "src_all"
      ],
      "post"=> [
        "url"=> $CFG->wwwroot."/local/user_registration/post.php?id=".$reg_id
      ],
      "redirect"=> [
		"url"=> $CFG->wwwroot."/local/user_registration/home.php?id=".$id
      ]
    ]
);

if(!$DB->record_exists('lcl_transection', array("registration_id" => $reg_id))){
 $obj_ins = new \stdClass();
 $obj_ins->txn_id = $txn_id; 
 $obj_ins->order_id = $order_id;
 $obj_ins->registration_id = $reg_id; 
 $obj_ins->postdata_before = serialize($charge); 
 $obj_ins->status = 0;
 $obj_ins->created_date = time(); 
 $obj_ins->modified_date = time();
 $DB->insert_record('lcl_transection', $obj_ins);
} else {
 $data = $DB->get_record('lcl_transection', array("registration_id" => $reg_id));
 $obj_upd = new \stdClass();
 $obj_upd->id = $data->id; 
 $obj_upd->txn_id = $txn_id; 
 $obj_upd->order_id = $order_id;
 $obj_upd->postdata_before = serialize($charge);  
 $obj_upd->modified_date = time();
 $DB->update_record('lcl_transection', $obj_upd);
}
 redirect($charge->transaction->url);
} else {
 redirect($CFG->wwwroot."/local/user_registration/home.php?id=".$id, 'Course is not available, Please try again later');
}


