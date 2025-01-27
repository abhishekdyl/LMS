<?php
$param = file_get_contents('access.log');

$parameter = json_decode($param);

 getLog($parameter);

function getLog($parameter) {

foreach($parameter as $key => $value)
	{
		echo '<pre>';
		print_r($value);
		echo '</pre>';
		
	}

}
?>