<?php

if($user_id = wp_insert_user($user_data)){
    foreach ($usermeta as $metakey => $metadata) {
      $updated = update_user_meta( $user_id, $metakey, $metadata );
    }
}

$user_data = array(
 'user_pass' =>$formdata->password,
 'user_login' => $formdata->email,
 'user_nicename' => $formdata->firstname." ".$formdata->lastname,
 'user_email' => $formdata->email,
 'display_name' => $formdata->firstname." ".$formdata->lastname,
 'nickname' => $formdata->email,
 'first_name' => $formdata->firstname,
 'last_name' => $formdata->lastname,
 'description' => "",
 'user_registered' => "",
 'role' => $formdata->role
);
$usermeta = array(
 'institution' => $formdata->institution,
 'accounttype' => $formdata->role,
 'address' => $formdata->address,
 'phone' => $formdata->phone,
 'contactname' => $formdata->firstname." ".$formdata->lastname,
 'jobtitle' => $formdata->jobtitle,
 'paymenttype' => $formdata->paymenttype,
 'presubscription' => $formdata->presubscription,
);


?>

