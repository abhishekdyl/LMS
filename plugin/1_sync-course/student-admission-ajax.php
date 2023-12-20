<?php
ob_start();
session_start();
require_once("../../../wp-config.php");
if ( ! function_exists( 'wp_handle_upload' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
}
$error_arr=array();
$user = wp_get_current_user();
if(!isset($_POST['study_level']) || empty($_POST['study_level'])){
    array_push($error_arr,array('msg'=>'Study Level is required','key'=>'study_level'));
}
if(!isset($_POST['admission_type']) || empty($_POST['admission_type'])){
    array_push($error_arr,array('msg'=>'Admission type is required','key'=>'admission_type'));
}
if(!isset($_POST['country']) || empty($_POST['country'])){
    array_push($error_arr,array('msg'=>'Country required','key'=>'country'));
}
if(!isset($_POST['city']) || empty($_POST['city'])){
    array_push($error_arr,array('msg'=>'city is required','key'=>'city'));
}
if(!isset($_POST['contact_person_name']) || empty($_POST['contact_person_name'])){
    array_push($error_arr,array('msg'=>'contact person name is required','key'=>'contact_person_name'));
}
if(!isset($_POST['contact_Person_relationship']) || empty($_POST['contact_Person_relationship'])){
    array_push($error_arr,array('msg'=>'contact Person relationship is required','key'=>'contact_Person_relationship'));
}
if(!isset($_POST['contact_person__number']) || empty($_POST['contact_person__number'])){
    array_push($error_arr,array('msg'=>'contact person number is required','key'=>'contact_person__number'));
}
if(!isset($_POST['school_address']) || empty($_POST['school_address'])){
    array_push($error_arr,array('msg'=>'school address is required','key'=>'school_address'));
}
if(!isset($_POST['grade_matric_result']) || empty($_POST['grade_matric_result'])){
    array_push($error_arr,array('msg'=>'grade matric result is required','key'=>'grade_matric_result'));
}
if(!isset($_POST['exam_year']) || empty($_POST['exam_year'])){
    array_push($error_arr,array('msg'=>'exam year is required','key'=>'exam_year'));
}
if(!isset($_POST['gender']) || empty($_POST['gender'])){
    array_push($error_arr,array('msg'=>'gender is required','key'=>'gender'));
}
if(!isset($_POST['dob']) || empty($_POST['dob'])){
    array_push($error_arr,array('msg'=>'dob is required','key'=>'dob'));
}
if(!isset($_POST['nationality']) || empty($_POST['nationality'])){                              
    array_push($error_arr,array('msg'=>'dob is required','key'=>'nationality'));
}
if(!isset($_POST['place_birth']) || empty($_POST['place_birth'])){
    array_push($error_arr,array('msg'=>'place birth is required','key'=>'place_birth'));
}
if(!isset($_POST['m_status']) || empty($_POST['m_status'])){
    array_push($error_arr,array('msg'=>'Marital status is required','key'=>'m_status'));
}
if(!isset($_POST['e_mail']) || empty($_POST['e_mail'])){
    array_push($error_arr,array('msg'=>'Email is required','key'=>'e_mail'));
}
if(!isset($_FILES['profile_image']) || empty($_FILES['profile_image']['name'])){
    array_push($error_arr,array('msg'=>'Profile Image is required','key'=>'profile_image'));
}
if(!isset($_POST['lname']) || empty($_POST['lname'])){
    array_push($error_arr,array('msg'=>'Last name is required','key'=>'lname'));
}
if(!isset($_POST['mname']) || empty($_POST['mname'])){
    array_push($error_arr,array('msg'=>'Middle name is required','key'=>'mname'));
}
$meta_keys=array_keys($_POST);
$files_keys=array_keys($_FILES);
$files_arr=array();
if(count($error_arr)){
    $msg['status']=false;
    $msg['msg']='';
    $msg['data']=$error_arr;
    //update_user_meta( $user->ID, 'admission_docs',false);

}else{
    // foreach($meta_keys as $user_meta_key){
    //     update_user_meta($user->ID, $user_meta_key, $_POST[$user_meta_key]);
    // }
    foreach($files_keys as $file){
        $uploadedfile = $_FILES[$file];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile,$upload_overrides);
        $finalimgurl = "";
        if ( $movefile && ! isset( $movefile['error'] ) ) {
            $finalimgurl = str_replace(home_url( $wp->request ),"",$movefile["url"]);
           // update_user_meta( $user->ID, $file, $finalimgurl );
            array_push($files_arr,array($file=>$finalimgurl));
        } 
    }

    $inserted_id=$wpdb->insert( "{$wpdb->prefix}multistepform", 
    array('question_status_id' => $_SESSION['one_planet']['question_status_id'], 
        'post_data' => serialize($_POST),
        'all_files' => serialize($files_arr),
        'createddate' => time()
    ) );

    $_SESSION['one_planet']['multistepform_id']=$wpdb->insert_id;
    //update_user_meta( $user->ID, 'admission_docs',true);
    $msg['status']=true;    
    $msg['msg']='Admission form submitted successfully';    
}
echo json_encode($msg);

?>