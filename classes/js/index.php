<?php
if(isset($_POST['newValue']) && !empty($_POST['newValue']) && $_POST['newValue'] != "NaN" && $_POST['newValue'] != -1){
    $newValue = intval($_POST['newValue']);
    file_put_contents('number.txt', $newValue);
}
if (file_exists('number.txt')) {
    $newValue = intval(file_get_contents('number.txt'));
} else {
    $newValue = 1000;
    file_put_contents('number.txt', $newValue);
}
echo json_encode($newValue);