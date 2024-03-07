<?php
ob_start();
session_start();
if(!isset($_SESSION['repusersid'])){
    $_SESSION['repusersid'] = array();
}
    if(is_array($_POST['userid'])){
        foreach ($_POST['userid'] as $key) {
            if($key != 'on'){
                if ($_POST['checked'] == 'true') {
                    $_SESSION['repusersid'][$key]=true;
                } else if(isset($_SESSION['repusersid'][$key])) {
                    unset($_SESSION['repusersid'][$key]);
                }
            }
        }
        $return = array(
            "status"=>true,
            "totaladded"=>sizeof($_SESSION["repusersid"]),
            "totaladdedids"=>array_keys($_SESSION["repusersid"])
        );
    }else{
        $userid = $_POST['userid'];
        if ($_POST['checked'] == 'true') {
            $_SESSION['repusersid'][$userid]=true;
        } else if(isset($_SESSION['repusersid'][$userid])) {
            unset($_SESSION['repusersid'][$userid]);
        }
        $return = array(
            "status"=>true,
            "totaladded"=>sizeof($_SESSION["repusersid"]),
            "totaladdedids"=>array_keys($_SESSION["repusersid"])
        );
    }
echo json_encode($return);