<?php

require_once('../../config.php');
global $DB;

$clientId = 'ABnD2XfVRmma8f8f8WHILa4mYXnAwFWZX7yGt8ZOwnObnltZ6v';
$clientSecret = 'ExfVmBcgdUf4kQnbJtidoyuz4tAprkfePolSPdX3';
$redirectUri = 'your_redirect_uri';
$authorizationCode = 'authorization_code';  // The code obtained during OAuth authorization

$tokenEndpoint = 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer';

$curl = curl_init($tokenEndpoint);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, [
    'grant_type' => 'authorization_code',
    'code' => $authorizationCode,
    'redirect_uri' => $redirectUri,
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
]);

$response = curl_exec($curl);
curl_close($curl);

// Parse the response JSON to get access_token, refresh_token, and realm_id
$data = json_decode($response, true);
echo "<pre>";
print_r($data);
echo "</pre>";
// Check if the response includes the realm_id (company ID)
// if (isset($data['realmId'])) {
//     $realmId = $data['realmId'];
//     echo 'Company ID (Realm ID): ' . $realmId;
// } else {
//     echo 'Failed to obtain Company ID. Error: ' . print_r($data, true);
// }
