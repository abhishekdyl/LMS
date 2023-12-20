<?php
require_once("../../../wp-config.php");
    global $wpdb;
    $username = $_POST['username'];
    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $password = $_POST['password'];
    $crmpassword = $_POST['crmpassword'];
    $compname = $_POST['compname'];
    $contact = $_POST['contact'];
    $country = $_POST['country'];
    $acctype = $_POST['acctype'];
    
    $error_arr=array();
    $flag=true;
    if(!isset($fname) OR empty($fname)){
        $flag=false;
        array_push($error_arr,array('error'=>"Firstname is required",'key'=>"fname"));
    }
    if(!isset($lname) OR empty($lname)){
        $flag=false;
        array_push($error_arr,array('error'=>"Lastname is required",'key'=>'lname'));
    }
    if(!isset($password) OR empty($password)){
        $flag=false;
        array_push($error_arr,array('error'=>"password is required",'key'=>'password'));
    }
    if(!isset($username) OR empty(trim($username))){
        $flag=false;
        array_push($error_arr,array('error'=>"Username is required",'key'=>'username'));
    }
    // else{
    //     if(username_exists($username)){
    //         array_push($error_arr,array('error'=>"Username already exist",'key'=>'username'));
    //     }   
    // }
    if(!isset($email) OR empty(trim($email))){
        $flag=false;
        array_push($error_arr,array('error'=>"Email is required",'key'=>'email'));
    }
    // else{
    //     if(email_exists($email)){
    //         array_push($error_arr,array('error'=>"Email already exist",'key'=>'email'));
    //     }
    // }
    if(!isset($crmpassword) OR empty($crmpassword)){
        $flag=false;
        array_push($error_arr,array('error'=>"Confirm password is required",'key'=>'crmpassword'));
    }
    if(strcmp($crmpassword,$password) != 0){
        $flag=false;
        array_push($error_arr,array('error'=>"Enter same password",'key'=>'crmpassword'));
    }
    
    if(sizeof($error_arr)){
        $msg['status']=false;
        $msg['data']=$error_arr;
        $msg['msg']='';
        echo json_encode($msg);
        exit();

    }else{
        
        $default_newuser = array(
            'user_pass' =>  $password,
            'user_login' => $username,
            'user_email' => $email,
            'first_name' => $fname,
            'last_name' => $lname,
        );

        $newuserid = wp_insert_user($default_newuser);

        // echo "<pre>-----esle---------";
        // print_r($newuserid);
        // echo "</pre>";
        // die;

        if($newuserid){
            $comp_name = update_user_meta($newuserid, 'company_name',$compname);
            $new_contact = update_user_meta($newuserid, 'contact',$contact );
            $country_name = update_user_meta($newuserid, 'country',$country);
            $account_type = update_user_meta($newuserid, 'account_type',$acctype);
        }
        $msg['status']=true;
        $msg['data']=$newuserid;
        $msg['msg']='Your user has been created.';
        echo json_encode($msg);
        exit();
    }
    

?>

