<?php

class quickbookClass{
	
	public static function oauth2token(para1,para2){
    global $DB,$CFG;
    	
    }


   public static function createCustomer(para1,para2){
    global $DB,$CFG;
	$curl = curl_init();

	curl_setopt_array($curl, array(
  		CURLOPT_URL => 'https://sandbox-quickbooks.api.intuit.com/v3/company/9130357979914686/customer',
  		CURLOPT_RETURNTRANSFER => true,
  		CURLOPT_ENCODING => '',
  		CURLOPT_MAXREDIRS => 10,
  		CURLOPT_TIMEOUT => 0,
  		CURLOPT_FOLLOWLOCATION => true,
  		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  		CURLOPT_CUSTOMREQUEST => 'POST',
  		CURLOPT_POSTFIELDS =>'{
    		"BillAddr": {
        		"Line1": "123 Main Street",
        		"City": "Mountain View",
        		"Country": "USA",
        		"CountrySubDivisionCode": "CA",
        		"PostalCode": "94042"
    		},
    		"Notes": "Here are other details.",
    		"DisplayName": "King\'s Groceries1",
    		"PrimaryPhone": {
        		"FreeFormNumber": "(555) 555-5555"
    		},
    		"PrimaryEmailAddr": {
        		"Address": "jdrew@myemail.com"
    		}
		}',
  		CURLOPT_HTTPHEADER => array(
    		'User-Agent: PaymentsAPI-OAuth2-Postman',
    		'Accept: application/json',
    		'Content-Type: application/json',
    		'Authorization: Bearer eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..P81cD-Zn3eHKwAKjZBZG3g.kEpYQ2-y28ftdWxRD3T8PhmmYCVmRvvgNXgdPi2oDHiLTbxnWFOh03bzB8cDMASLelhMZZHlkLPOL_542cBxcCfEU7YOSqH8BFTGK9PAEJHSnlpec_ifln4dc-TZrMaAw5SKA3Fg6RK9AbFrdjC1ZJRBpuTMTWAOnevNgHSduIeApdXBaC4IleKO4vNADVcI--M1wMTdvX0FuS8lxsiQ2E5zGVPGcrxErqTOCCSlnLwYgXPzrCL353JR8Ft6qDF8CJvSgzU1tG3f7MPTSqR1qAhrKFWeYgHfh33MaYddF4kG2sMSf8izJnWaTEstBjdqNE-jjTMM_lXYTptgdNGL7xnc3mNUddpmITDufUVHZfYA8Qc5zW8OpV1R7pycaXzhxKZp4O8XgRSg40PMFvKpjDlVWtWznAsjRNK8sZ89FumV1PPjGGKsZaLJpkUfQQYaxqYbetPQsmAH_vMjNOktn2PGUG38hKOnL4qH2_q2dSrI8yYJHSL-gk5HbSkxYebJ8-8BKFNET_5zWXKHNEbZog-05mPh0tf5bSGBtFhdVCxfm16L3QpzZJxI6GqmvKlLxUYKvpZT7NRpkJ7ctcwx0SWEc5dvCLGZERumOpkWeonUIEznWJWCPFyUxxytdnjEabwcdP1FZ-umT-qw1OZqoZz2AGhYKx9UHn9wd-3fA4v-Tim5RUqniH9shRlLHN3N7qdKJzx3N5MnmfObmzox7561nAFVJjIS2Hu_OOg-vneLhDq7kzXkv4-SBirjYCg8mG-CQhxlosZCg_JIFG5fYBORSQME3qiMQXZtrpVC095oQQ3zXLnwqFl9ggEjxgpJNKeP-Y2JpWQoREOy3o-sgoML77KS1vkZbj0rEPDhRaXRgM2haQXor1AGkxWnYng_.hlgvvKvprIwNi2aYOpKs6A'
  		),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	echo $response;

    }
					

	public static function careateInvoice(para1,para2){
    global $DB,$CFG;

	$curl = curl_init();

	curl_setopt_array($curl, array(
  		CURLOPT_URL => 'https://sandbox-quickbooks.api.intuit.com/v3/company/9130357979914686/customer',
 	 	CURLOPT_RETURNTRANSFER => true,
  		CURLOPT_ENCODING => '',
  		CURLOPT_MAXREDIRS => 10,
  		CURLOPT_TIMEOUT => 0,
  		CURLOPT_FOLLOWLOCATION => true,
  		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  		CURLOPT_CUSTOMREQUEST => 'POST',
  		CURLOPT_POSTFIELDS =>'{
  		"Line": [
    		{
      		"Amount": 100.00,
      		"DetailType": "SalesItemLineDetail",
      		"SalesItemLineDetail": {
        		"ItemRef": {
          		"value": "{{ItemId}}",
          		"name": "{{ItemName}}"
        		},
        		"Qty": 1
      		}
    		}
  		],
  		"CustomerRef": {
    		"value": "{{CustomerId}}"
  		}
		}',
  		CURLOPT_HTTPHEADER => array(
    		'User-Agent: Intuit-qbov3-postman-collection2',
    		'Accept: application/json',
    		'Content-Type: application/json'
  		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;

    	
    }

	


}

?>