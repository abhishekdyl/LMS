<?php
require_once('../../../wp-config.php');

global $wpdb;
// $sql = 'SELECT * FROM '. $wpdb->prefix .'users';
// $users = $wpdb->get_results($sql);
// echo "<pre>";
// print_r($users);
// echo "</pre>";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $user_data = array( 
            'user_login' => $username, 
            'user_email' => $email,
            );
    $usermeta = array( 
            'first_name' => $firstname, 
            'last_name' => $lastname,
            );

    if($user_id = wp_insert_user($user_data)){
        foreach ($usermeta as $metakey => $metadata) {
          $updated = update_user_meta( $user_id, $metakey, $metadata );
        }
     }  
    //  global $wpdb;     
    //  $table_name = $wpdb->prefix . 'users';   
    // $wpdb->insert($table_name, array('user_login' => $username, 'user_email' => $email)); 
    // $wpdb->query("INSERT INTO $table_name(first_name,last_name,user_login,user_email) VALUES('$firstname', '$lastname', '$username', '$email')"); 
} 

   
//   $response = array(
//     "draw" => intval($draw),
//     "iTotalRecords" => $iTotalRecords,
//     "iTotalDisplayRecords" => $iTotalDisplayRecords,
//     "aaData" => array_values($allforums)
//   );
// echo json_encode($response);
    



?>