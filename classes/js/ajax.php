<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$logatas = json_decode($_POST['visitslog']);
	foreach ($logatas as $logata) {

		echo '<pre>';
		print_r($logata);
		echo '----------------------';
		print_r($logata->visitorId);
		echo '</pre>';
	}	
}
echo json_encode($newValue);

?>