<?php
 /*Template Name:  SSO Login*/
 
global $wpdb, $current_user ;

$current_user = wp_get_current_user();
 $username = $current_user->data->user_login;
 $email = $current_user->data->user_email;

function encryption($email){
    // Storing a string into the variable which
    // needs to be Encrypted
    $simple_string = $email;
    
    // Displaying the original string
    // echo "Original String: " . $simple_string;
    
    // Storingthe cipher method
    $ciphering = "AES-128-CTR";
    
    // Using OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    
    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '354698521478542';
    
    // Storing the encryption key
    $encryption_key = 'develop';
    
    // Using openssl_encrypt() function to encrypt the data
     return $encryption = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv);
}

 $encrypt = encryption($email);
 $key = 'develop';
 get_header(); 
 
 echo '<a href="https://classroomstaging.lemons-aid.com/local/sso_login/index.php?key='.$key.'&codehas='.$encrypt.'"><button>Send</button></a>';
 
 get_footer()

?>