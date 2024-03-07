<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;

session_start();

function processCode()
{

    // Create SDK instance
    $config = include('config.php');
    $dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID' => $config['client_id'],
        'ClientSecret' =>  $config['client_secret'],
        'RedirectURI' => $config['oauth_redirect_uri'],
        'scope' => $config['oauth_scope'],
        'baseUrl' => "development"
    ));

    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
    $parseUrl = parseAuthRedirectUrl(htmlspecialchars_decode($_SERVER['QUERY_STRING']));

    /*
     * Update the OAuth2Token
     */
    $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);
    $dataService->updateOAuth2Token($accessToken);

    /*
     * Setting the accessToken for session variable
     */
    // $_SESSION['sessionAccessToken'] = $accessToken;
	$_SESSION['sessionAccessToken'] = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..EEr-VLxGM3JeGNqsVXqlFQ.9Sp7kNi53Dr-LaSzVBiEINrqpfP7t3Z3-d4pnuxlsSyGKQNuF-AWKvHcmFGmIpXzvNqVulq-JQpyFAcURh93mWPOdfJNasLbXEm_A-eE83nf-DlR1y90nqYcEns9JuFOXyYkx6SGZUEqo1qP135yyWElD_lI7lqxPSZCXfEPbRcgrm6ft7hauiX1tAzOhxukN_dGP5D99nfSPAyP4VNO_sNOM5P4Ht7GOTOpnDbNlxhVUUOOrroIUP_iyBUoXYaqrd0bmxaPVLktXQfszIkXVZ1UWWp1TiW1MYrwJ4A898xgKnlbcSlGwDJsLCKG8LzTXKnUI-Oeh9wTXJW6TPOIT2GYn9psmJXbtz5rIeSV949nAiS0NbrBkAHzDjbawhpD6-jaT3I3OdlftfJUYsPq1sHZtUVAyuKCDU-8kcYhZ0ETu-zBXLn2gEwAdomiaeKGkO7eytxa_TNtC5-gALdDt1_mCsw6J6sZ240LYwY0WhlPIC0iVEReOC61gnRgBJ3-4v0up8m3YzVrlD6NfTAhZb9WlZMjoVOk0COaCS2FDTjimlAOd8WX2nRgTq2UPl_lqo_p3YZhF2rgRJpMHP5FLSNSmFVNwdPgY3kif3h1ZCF8cHKAvPcpefkmNgMGGOxCH2d5-A4sPNAEATFAglxxyLz3TanF3wML1plGfxIoIKP_9UnEfzzoEjB-JV9C8x7zmKP4AsZuNgYg-RGMMbC0qXZDL62gBZopNyw9R0GZ9p7YUOEHl_H3MX_hJbk3J89DBrV-a7vnFo23Nx2YGC17ymQs-XVDz45XOtGaQCDOd6gCGcJhdBElB1hWjkvtvQUhNw1b-XZ2rwbu1RxphRHXF6_udia5tHwvUyFc07MGVCN1gtseCZ9I0Uu745OfRJO6bi0WeVCF_EcQa5WXDOOlyA.YI4Olh6m8nmP4AooA_pYNQ' ;
}

function parseAuthRedirectUrl($url)
{
    parse_str($url,$qsArray);
    return array(
        'code' => $qsArray['code'],
        'realmId' => $qsArray['realmId']
    );
}

$result = processCode();

?>
