<?php 
ob_start();
session_start();
$_SESSION['trailemail']=$_POST['email'];
/**
* Template Name: Free Registration Form
**/
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
get_header();
global $wpdb, $user_ID;
$plugingpath = plugins_url() . "/enrolled_subscription/enrolled_ajax.php";
if(!empty($user_ID)){
  $readonly ='readonly';
}else{
  $readonly =" "; 
}
$user_info = get_userdata($user_ID); 
$udetails= get_user_meta ($user_ID);
//print_r($user_info);
//echo "<pre>";
//print_r($udetails);
$googlelogin = $udetails['_wc_social_login_google_identifier'][0];
if(!empty($user_ID)){
if(!empty($googlelogin)){

       $free_subscription = array();
       $free_subscription['username'] =$user_info->user_login; 
       $free_subscription['useremail'] =$user_info->user_login; 
       $free_subscription['first_name'] =$udetails['first_name'][0]; 
       $free_subscription['last_name'] =$udetails['last_name'][0]; 
       $free_subscription['password'] ="P@ssw0rd";
$jsondata=  json_encode($free_subscription);

       $curl = curl_init();

       curl_setopt_array($curl, array(
        CURLOPT_URL => $mdl_siteurl."/local/designer/free_subscription.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>$jsondata,  
        CURLOPT_HTTPHEADER => array(
          "cache-control: no-cache",
          "content-type: application/json",
          "postman-token: dc99a883-036b-4179-9a03-f0a180ec2753"
        ),
      ));

       $response = curl_exec($curl);
       $err = curl_error($curl);
       curl_close($curl);
       if ($err) {
       } else {
        wp_redirect(site_url('thank-you/'));
        exit;
      }
	
}else{
	        wp_redirect(site_url('thank-you/'));
        exit;

}
}

   //All code goes in here. 
if (isset($_POST['user_registeration']))
{
  global $reg_errors;
  $reg_errors = new WP_Error;

  $username=trim($_POST['useremail']," ");
  $useremail=trim($_POST['useremail']," ");
  $password=$_POST['password'];
  $pass_confirm=$_POST['password_confirmation'];
  $studentname = explode('@',$useremail);
  $first_name = $studentname[0];
  $last_name= $studentname[0];
  if(empty($user_ID)){

    if(empty( $username ) || empty( $useremail ) || empty($password))
    {
      $reg_errors->add('field', 'Required form field is missing');
    }    
    if ( 6 > strlen( $username ) )
    {
      $reg_errors->add('username_length', 'Username too short. At least 6 characters is required' );
    }
    if ( username_exists( $username ) )
    {
      $reg_errors->add('user_name', 'The username you entered already exists!');
    }
    if ( ! validate_username( $username ) )
    {
      $reg_errors->add( 'username_invalid', 'The username you entered is not valid!' );
    }
    if ( !is_email( $useremail ) )
    {
      $reg_errors->add( 'email_invalid', 'Email id is not valid!' );
    }
    
    if ( email_exists( $useremail ) )
    {
      $reg_errors->add( 'email', 'Cet email existe déjà. Veuillez utiliser un email différent pour vous inscrire.' );
    }
       
       if ( 5 > strlen( $password ) ) {
        $reg_errors->add( 'password', 'Votre mot de passe doit contenir au moins 5 caractères.' );
      } 
      
      if($password != $pass_confirm) {
      // passwords do not match
       $reg_errors->add( 'password_mismatch', 'Vos mots de passe ne correspondent pas. Veuillez saisir vos mots de passe de nouveau.' );
     }

     
     
     if (is_wp_error( $reg_errors ))
     { 
      foreach ( $reg_errors->get_error_messages() as $error )
      {
       $signUpError='<p style="color:#FF0000; text-aling:left;"><strong>ERREUR</strong>: '.$error . '<br /></p>';
     } 
   }
   
   
   if ( 1 > count( $reg_errors->get_error_messages() ) )
   {
        // sanitize user form input
    global $username, $useremail;
    $username   =   sanitize_user( $_POST['useremail'] );
    $useremail  =   sanitize_email( $_POST['useremail'] );
    $password   =   esc_attr( $_POST['password'] );
    
    $userdata = array(
      'user_login'    =>   $username,
      'user_email'    =>   $useremail,
      'user_pass'     =>   $password,
      'first_name'    =>   $first_name,
      'last_name'     =>   $last_name,
    );
    $user = wp_insert_user( $userdata );
    $user_id =$user;

    $metas = array( 
   
          'billing_last_name'     =>   $last_name,
          'billing_first_name'    =>   $first_name,
        );

    foreach($metas as $key => $value) {
      update_user_meta( $user_id, $key, $value );
    }
    
    $table_name = $wpdb->prefix . "user_subscription";
    $my_post = array(
      'user_id'=>$user, 
         // 'subscription_id'=>$subscription_id, 
      'subscription_type'=>'free', 
    );
    $mydata = $wpdb->insert($table_name,$my_post);
    // First get the user details
    $user = get_user_by('login', $username );
    
  // If no error received, set the WP Cookie
    if ( !is_wp_error( $user ) )
    {
      wp_clear_auth_cookie();
        wp_set_current_user ( $user->ID ); // Set the current user detail
        wp_set_auth_cookie  ( $user->ID ); // Set auth details in cookie
        $message = "account create successfully";
        
		// send mail
        
        $to      = $useremail;

        $subject = "Bienvenue chez Five Students ! Votre compte est actif"; 

// To send HTML mail, the Content-type header must be set

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
// Additional headers
        $headers .= 'From: no-reply@fivestudents.com' . "\r\n";	 

// Compose a simple HTML email message 

        $message = '<html><body>';

        $message .= "<p style='font-size:18px;'>Bonjour,<p>";
        $message .= "<p style='font-size:18px;'>Merci de nous faire confiance pour vous assister dans la préparation de vos examens.</p>";
        $message .= "<p style='font-size:18px;'>Veuillez <a href='#'>cliquer ICI </a> pour télécharger notre application sur Google Play Store.</p>";
        $message .= '<div style="max-width: 100%; padding: 20px 15px;margin-bottom: 15px;"> 
        <figure>
        <img src="https://fivestudents.com/wp-content/uploads/2021/04/googleplay.png" width="200" style="max-width: 100%;display: block;" />
        </figure>
        </div>';
        $message .= "<p style='font-size:18px;'>Bonne préparation !</p>";

        $message .= "<p style='font-size:18px;'>L’Équipe Five Students</p>";

        $message .= '</body></html>';

// Sending email
$sent = wp_mail( $to, $subject, $message, $headers );
      if($sent) {
  echo $status=1; 
//die;  
      }
//message sent!
      else  {
  echo $status=0;	
//die; 
      } 

       
       $free_subscription = array();
       $free_subscription['username'] =$username; 
       $free_subscription['useremail'] =$useremail; 
       $free_subscription['first_name'] =$first_name; 
       $free_subscription['last_name'] =$last_name; 
      //$free_subscription['subscription_id'] =$subscription_id; 
       $free_subscription['password'] =$password;
       $jsondata=  json_encode($free_subscription);

       $curl = curl_init();

       curl_setopt_array($curl, array(
        CURLOPT_URL => $mdl_siteurl."/local/designer/free_subscription.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>$jsondata,  
        CURLOPT_HTTPHEADER => array(
          "cache-control: no-cache",
          "content-type: application/json",
          "postman-token: dc99a883-036b-4179-9a03-f0a180ec2753"
        ),
      ));

       $response = curl_exec($curl);
       $err = curl_error($curl);
       curl_close($curl);
       if ($err) {
  //print_r($err);
       } else {
    //print_r($response); 
    //die();
        wp_redirect(site_url('thank-you/'));
        exit;
      }
    } else {
      $message = "Failed to log in";
   }
 }
}
}  
 
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<div class="container reg_box">
  <div class="reg_box_sect">
    <h2></h2> 
    <form action="" method="post" class="form-horizontal free_trail" name="user_registeration">
      <div class="row">
       <div class="col-sm-12">
         <h2 class="formtitle text-center"> Inscrivez-vous et commencez  à  <br> préparer vos examens</h2>
       </div>
     </div>
     <?php if(empty($user_ID)){
     ?>
      <div class="row">
        <div class="col-sm-3"></div>    
        <div class="col-sm-6">
         <div class="google_acc text-center">
           <a href="<?php echo home_url(); ?>/?wc-api=auth&start=google&return=https%3A%2F%2Fqa.fivestudents.com%2Ffree-trial%2F" class="button-social-login button-social-login-google"><span class="si si-google"></span>Inscrivez-vous avec Gmail </a>          
           
         </div>
         <div class="google_acc text-center">        
         </div>
         <div class="border_top text-center">
           <span>OU</span>
         </div>
       </div>
       <div class="col-sm-3"></div>  
     </div>

     <?php
   }?>
   <div class="row">
     <div class="col-sm-3"></div>
     <div class="col-sm-6">
      <?php if(isset($signUpError)){echo '<div>'.$signUpError.'</div>';}?>
      <?php echo '<div>'.$message.'</div>';?>
      <div class="form-group">
      </div>
    </div>
    <div class="col-sm-3"></div>
  </div>
  <div class="row">
   <div class="col-sm-3"></div>
   <div class="col-sm-6">
    <div class="form-group">
      <label class="control-label " for="email">Email<span class="required" title="required">*</span></label>
      <input type="text" name="useremail" value="<?php if(!empty($user_ID)){ echo $user_info->user_email;}if(!empty($_POST['useremail'])){ echo $_POST['useremail'];} ?>" class="form-control" id="email" <?php echo $readonly;?> placeholder="Saisissez votre adresse Email " required />
    </div>
  </div>
  <div class="col-sm-3"></div>
</div>
<div class="row">
 <div class="col-sm-3"></div>
 <div class="col-sm-6">          
 <?php
 
 // Display the encrypted string 
/* $encryption=$store_pass; 
$decryption_iv = '8674327650527659'; 
$ciphering = "AES-128-CTR";
$decryption_key = "Five@Student!!"; 
$decryption=openssl_decrypt ($encryption, $ciphering, $decryption_key, $options, $decryption_iv); */
 ?>
 
 
 
 
  <div class="form-group">
    <label class="control-label " for="pwd">Créez un mot de passe <span class="required" title="required">*</span></label>
    <input type="password" name="password" class="form-control" id="password2-field" value=""  placeholder="Saisissez votre mot de passe" required />
      <span toggle="#password2-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
    </div>
  </div>
  <div class="col-sm-3"></div>
</div>
<?php if(empty($user_ID)){?>
  <div class="row">
   <div class="col-sm-3"></div>
   <div class="col-sm-6">          
    <div class="form-group">
      <label class="control-label " for="pwd">Confirmez votre mot de passe <span class="required" title="required">*</span></label>
      <input type="password" name="password_confirmation" class="form-control" id="password1-field" value=""  placeholder="Saisissez votre mot de passe" required>  
      <span toggle="#password1-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>    
    </div>
  </div>
  <div class="col-sm-3"></div>
</div>
<?php } ?>
 <div class="row">
  <div class="col-sm-3"></div>  
  <div class="col-sm-6">
    <div class="form-group chck-bx">   
      <input type="checkbox" id="vehicle1" name="vehicle1" value="Bike" required>
	  <label for="vehicle1">J’ai lu et j’accepte les <a href="/cgu/">conditions générales d’utilisation</a>.
        <br>
        et notamment la mention relative à la protection des données personnelles. Conformément à la loi 09-08, vous disposez d'un droit d'accès, de rectification et d'opposition au traitement de vos données personnelles.
        <br>
        <br>
		</label>
      </div>
    </div>
    <div class="col-sm-3"></div>  
  </div>
  <hr>
  <div class="row">
    <div class="col-sm-3"></div>  
    <div class="col-sm-6">

      <?php  echo do_shortcode( '[bws_google_captcha]' ); ?> 
      <?php 
      do_action( 'login_form' );
      ?>
      <input type="submit" name="user_registeration" class="form-control" value="S'INSCRIRE" />
      <div>
      </br>
    </form>
  </div>
 </div>
<div class="col-sm-3"></div>  
</div>
</div>
</div>



<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script>
  $(".toggle-password").click(function() {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
      input.attr("type", "text");
    } else {
      input.attr("type", "password");
    }
  });
</script>

<?php get_footer(); ?>