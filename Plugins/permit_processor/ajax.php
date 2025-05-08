<?php
ob_start();
session_start();
if(!isset($_SESSION['usersid'])){
    $_SESSION['usersid'] = array();
}
    if(is_array($_POST['userid'])){
        foreach ($_POST['userid'] as $key) {
            if($key != 'on'){
                if ($_POST['checked'] == 'true') {
                    $_SESSION['usersid'][$key]=true;
                } else if(isset($_SESSION['usersid'][$key])) {
                    unset($_SESSION['usersid'][$key]);
                }
            }
        }
        $return = array(
            "status"=>true,
            "totaladded"=>sizeof($_SESSION["usersid"]),
            "totaladdedids"=>$_SESSION["usersid"]
        );

    }else{
        
        $userid = $_POST['userid'];
        if ($_POST['checked'] == 'true') {
            $_SESSION['usersid'][$userid]=true;
        } else if(isset($_SESSION['usersid'][$userid])) {
            unset($_SESSION['usersid'][$userid]);
        }
        $return = array(
            "status"=>true,
            "totaladded"=>sizeof($_SESSION["usersid"]),
            "totaladdedids"=>$_SESSION["usersid"]
        );   
    }
echo json_encode($return);


