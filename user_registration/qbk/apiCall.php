<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Payment;
use QuickBooksOnline\API\Facades\Money;

session_start();

// if (isset($_GET['trigger'])) {
//     $trigger = $_GET['trigger'];
//     // Get company information
//     if($trigger == 5){
//         makeAPICall('getCompanyInfo');
//     }

//     // Check if the customer exists or create a new one
//     if($trigger == 1){
//         $customer = makeAPICall('createCustomer', ['displayName' => 'John Doe']);
//     }
//     // Check if the item exists or create a new one
//     if($trigger == 2){
//         $item = makeAPICall('createItem', ['itemName' => 'Sample Item']);
//     }

//     // Create an invoice with the existing or new customer and item
//     if($trigger == 3){
//         makeAPICall('createInvoice', ['customer' => $customer, 'item' => $item]);
//     }
// }
$_SESSION['sessionAccessToken'] = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..EEr-VLxGM3JeGNqsVXqlFQ.9Sp7kNi53Dr-LaSzVBiEINrqpfP7t3Z3-d4pnuxlsSyGKQNuF-AWKvHcmFGmIpXzvNqVulq-JQpyFAcURh93mWPOdfJNasLbXEm_A-eE83nf-DlR1y90nqYcEns9JuFOXyYkx6SGZUEqo1qP135yyWElD_lI7lqxPSZCXfEPbRcgrm6ft7hauiX1tAzOhxukN_dGP5D99nfSPAyP4VNO_sNOM5P4Ht7GOTOpnDbNlxhVUUOOrroIUP_iyBUoXYaqrd0bmxaPVLktXQfszIkXVZ1UWWp1TiW1MYrwJ4A898xgKnlbcSlGwDJsLCKG8LzTXKnUI-Oeh9wTXJW6TPOIT2GYn9psmJXbtz5rIeSV949nAiS0NbrBkAHzDjbawhpD6-jaT3I3OdlftfJUYsPq1sHZtUVAyuKCDU-8kcYhZ0ETu-zBXLn2gEwAdomiaeKGkO7eytxa_TNtC5-gALdDt1_mCsw6J6sZ240LYwY0WhlPIC0iVEReOC61gnRgBJ3-4v0up8m3YzVrlD6NfTAhZb9WlZMjoVOk0COaCS2FDTjimlAOd8WX2nRgTq2UPl_lqo_p3YZhF2rgRJpMHP5FLSNSmFVNwdPgY3kif3h1ZCF8cHKAvPcpefkmNgMGGOxCH2d5-A4sPNAEATFAglxxyLz3TanF3wML1plGfxIoIKP_9UnEfzzoEjB-JV9C8x7zmKP4AsZuNgYg-RGMMbC0qXZDL62gBZopNyw9R0GZ9p7YUOEHl_H3MX_hJbk3J89DBrV-a7vnFo23Nx2YGC17ymQs-XVDz45XOtGaQCDOd6gCGcJhdBElB1hWjkvtvQUhNw1b-XZ2rwbu1RxphRHXF6_udia5tHwvUyFc07MGVCN1gtseCZ9I0Uu745OfRJO6bi0WeVCF_EcQa5WXDOOlyA.YI4Olh6m8nmP4AooA_pYNQ' ;
function makeAPICall($callType, $data = [])
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

    // Retrieve the accessToken value from the session variable
    // $accessToken = $_SESSION['sessionAccessToken'];
    $accessToken = $_SESSION['sessionAccessToken'] = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..EEr-VLxGM3JeGNqsVXqlFQ.9Sp7kNi53Dr-LaSzVBiEINrqpfP7t3Z3-d4pnuxlsSyGKQNuF-AWKvHcmFGmIpXzvNqVulq-JQpyFAcURh93mWPOdfJNasLbXEm_A-eE83nf-DlR1y90nqYcEns9JuFOXyYkx6SGZUEqo1qP135yyWElD_lI7lqxPSZCXfEPbRcgrm6ft7hauiX1tAzOhxukN_dGP5D99nfSPAyP4VNO_sNOM5P4Ht7GOTOpnDbNlxhVUUOOrroIUP_iyBUoXYaqrd0bmxaPVLktXQfszIkXVZ1UWWp1TiW1MYrwJ4A898xgKnlbcSlGwDJsLCKG8LzTXKnUI-Oeh9wTXJW6TPOIT2GYn9psmJXbtz5rIeSV949nAiS0NbrBkAHzDjbawhpD6-jaT3I3OdlftfJUYsPq1sHZtUVAyuKCDU-8kcYhZ0ETu-zBXLn2gEwAdomiaeKGkO7eytxa_TNtC5-gALdDt1_mCsw6J6sZ240LYwY0WhlPIC0iVEReOC61gnRgBJ3-4v0up8m3YzVrlD6NfTAhZb9WlZMjoVOk0COaCS2FDTjimlAOd8WX2nRgTq2UPl_lqo_p3YZhF2rgRJpMHP5FLSNSmFVNwdPgY3kif3h1ZCF8cHKAvPcpefkmNgMGGOxCH2d5-A4sPNAEATFAglxxyLz3TanF3wML1plGfxIoIKP_9UnEfzzoEjB-JV9C8x7zmKP4AsZuNgYg-RGMMbC0qXZDL62gBZopNyw9R0GZ9p7YUOEHl_H3MX_hJbk3J89DBrV-a7vnFo23Nx2YGC17ymQs-XVDz45XOtGaQCDOd6gCGcJhdBElB1hWjkvtvQUhNw1b-XZ2rwbu1RxphRHXF6_udia5tHwvUyFc07MGVCN1gtseCZ9I0Uu745OfRJO6bi0WeVCF_EcQa5WXDOOlyA.YI4Olh6m8nmP4AooA_pYNQ';

    // Update the OAuth2Token of the dataService object
    $dataService->updateOAuth2Token($accessToken);
    // echo $callType;

    switch ($callType) {
        case 'createCustomer':
            return createCustomer($data['displayName'], $dataService);
        case 'createItem':
            return createItem($data['itemName'], $dataService);
        case 'createInvoice':
            return createInvoice($data['customer'], $data['item'], $dataService);
        case 'createPayment':
            return createPayment($data['customer'], $data['invoice'], $dataService);
        case 'getCompanyInfo':
            return getCompanyInfo($dataService);
        default:
            echo "Invalid API call type.";
            return null;
    }
}

function createCustomer($displayName, $dataService)
{
    // Check if the customer already exists
    $existingCustomer = $dataService->Query("SELECT * FROM Customer WHERE DisplayName = '$displayName'");
    
    if (!empty($existingCustomer)) {
        // Customer already exists, return the existing customer
        echo "Customer already exists. Customer ID: " . $existingCustomer[0]->Id;
        return $existingCustomer[0];
    }

    // Create a Customer object
    $customer = Customer::create([
        "DisplayName" => $displayName,
        // Add other customer details as needed
    ]);

    // Make the API call to create the customer
    $result = $dataService->Add($customer);

    if ($result) {
        // Customer creation successful
        echo "Customer created successfully. Customer ID: " . $result->Id;
        return $result;
    } else {
        // Error in creating customer
        echo "Error creating customer: " . $dataService->getLastError();
        return null;
    }
}

function createItem($itemName,$restData,$dataService)
{

echo 'aaaaaaaaaaaaaaaaaaa';
	return array($itemName,$restData,$dataService);
	die;
    // Check if the item already exists
    $existingItem = $dataService->Query("SELECT * FROM Item WHERE Name = '$itemName'");
    
    if (!empty($existingItem)) {
        // Item already exists, return the existing item
        echo "Item already exists. Item ID: " . $existingItem[0]->Id;
        return $existingItem[0];
    }

    // Create an Item object
    $item = Item::create([
        "Name" => $itemName,
      	"Description" => $restData->description,
      	"Active" => true,
      	"FullyQualifiedName" => $restData->fullname,
      	"Taxable" => true,
      	"UnitPrice" => $restData->price,
        "Type" => "Service",
        // Add other item details as needed,
      	"PurchaseCost"=> $restData->taxprice,
      	"TrackQtyOnHand" => true,
      	"QtyOnHand"=> 100,
      	"InvStartDate"=> $restData->starttime    
    ]);

    // Make the API call to create the item
    $result = $dataService->Add($item);

    if ($result) {
        // Item creation successful
        echo "Item created successfully. Item ID: " . $result->Id;
        return $result;
    } else {
        // Error in creating item
        echo "Error creating item: " . $dataService->getLastError();
        return null;
    }
}

function createInvoice($customer, $item, $dataService)
{
    // Create an Invoice object
    $invoice = Invoice::create([
        "Line" => [
            [
                "Amount" => 100,
                "DetailType" => "SalesItemLineDetail",
                "SalesItemLineDetail" => [
                    "ItemRef" => [
                        "value" => $item->Id,
                        "name" => $item->Name,
                    ]
                ],
            ]
        ],
        "CustomerRef" => [
            "value" => $customer->Id,
            "name" => $customer->DisplayName,
        ],
    ]);

    // Make the API call to create the invoice
    $createdInvoice = $dataService->Add($invoice);

    if ($createdInvoice) {
        // Invoice creation successful
        echo "Invoice created successfully. Invoice ID: " . $createdInvoice->Id;

        // Now, create a payment for the invoice
        $payment = Payment::create([
            "CustomerRef" => [
                "value" => $customer->Id,
            ],
            "TotalAmt" => 100,
            "Line" => [
                [
                    "Amount" => 100,
                    "LinkedTxn" => [
                        [
                            "TxnId" => $createdInvoice->Id,
                            "TxnType" => "Invoice",
                        ],
                    ],
                ],
            ],
        ]);

        // Make the API call to create the payment
        $createdPayment = $dataService->Add($payment);

        if ($createdPayment) {
            // Payment creation successful
            echo "Payment created successfully. Payment ID: " . $createdPayment->Id;

            // Get the updated invoice with payment status
            $updatedInvoice = $dataService->Query("SELECT * FROM Invoice WHERE Id = '" . $createdInvoice->Id . "'");
            echo "Updated Invoice Status: " . $updatedInvoice[0]->TxnStatus;

            return $createdPayment;
        } else {
            // Error in creating payment
            echo "Error creating payment: " . $dataService->getLastError();
            return null;
        }
    } else {
        // Error in creating invoice
        echo "Error creating invoice: " . $dataService->getLastError();
        return null;
    }
}

function getCompanyInfo($dataService)
{
    // Retrieve company information
    $companyInfo = $dataService->getCompanyInfo();
    $address = "QBO API call Successful!! Response Company name: " . $companyInfo->CompanyName . " Company Address: " . $companyInfo->CompanyAddr->Line1 . " " . $companyInfo->CompanyAddr->City . " " . $companyInfo->CompanyAddr->PostalCode;
    echo $address;

    return $companyInfo;
}



