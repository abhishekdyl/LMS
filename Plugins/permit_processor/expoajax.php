<?php
ob_start();
session_start();
if(!isset($_SESSION['expuserid'])){
    $_SESSION['expuserid'] = array();
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// die;

if(is_array($_POST['userid'])){
    foreach ($_POST['userid'] as $key) {
        if($key != 'on'){
            if ($_POST['checked'] == 'true') {
                $_SESSION['expuserid'][$key]=true;
            } else if(isset($_SESSION['expuserid'][$key])) {
                unset($_SESSION['expuserid'][$key]);
            }
        }
    }
    $return = array(
        "status"=>true,
        "totaladded"=>sizeof($_SESSION["expuserid"]),
        "totaladdedids"=>array_keys($_SESSION["expuserid"])
    );
}else{
    $userid = $_POST['userid'];
    if ($_POST['checked'] == 'true') {
        $_SESSION['expuserid'][$userid]=true;
    } else if(isset($_SESSION['expuserid'][$userid])) {
        unset($_SESSION['expuserid'][$userid]);
    }
    $return = array(
        "status"=>true,
        "totaladded"=>sizeof($_SESSION["expuserid"]),
        "totaladdedids"=>array_keys($_SESSION["expuserid"])
    );
}
echo json_encode($return);

?>