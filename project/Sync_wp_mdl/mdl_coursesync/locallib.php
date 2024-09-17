<?php
include_once("../../config.php");
function decription($code,$keycode){

    $ciphering = "AES-128-CTR";
    
    // Using OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    
    // Non-NULL Initialization Vector for decryption
    $decryption_iv = '354698521478542';
    
    // Storing the decryption key
    $decryption_key = $keycode;
    
    // Using openssl_decrypt() function to decrypt the data
    $decryption = openssl_decrypt($code, $ciphering, $decryption_key, $options, $decryption_iv);
    
    // Displaying the decrypted string
    return $decryption;
}