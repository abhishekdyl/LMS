<?php
ob_start();
session_start();
// echo "<pre>" ;
// print_r($_SESSION);
$login_error = array('username' => '', 'password' => '');
if (is_user_logged_in()) {
    wp_redirect(home_url()); //('location:','/');
    exit();
}

$username = '';
$password = '';
if (isset($_POST['submit'])) {

    global $wpdb;

    //We shall SQL escape all inputs  
    $username = $wpdb->escape($_REQUEST['username']);
    $password = $wpdb->escape($_REQUEST['password']);
    //$remember = ($wpdb->escape($_REQUEST['rememberme'])?true:false);

    //if ($remember) $remember = "true";
    //else $remember = "false";

    $login_data = array();
    $login_data['user_login'] = $username;
    $login_data['user_password'] = $password;
    // $login_data['remember'] = $remember;

    $user_verify = wp_signon($login_data, false);

    if (is_wp_error($user_verify)) {

        // echo "hello abhishek";
        $message = "login Invalid";
    } else {
        wp_clear_auth_cookie();
        wp_set_current_user($user_verify->ID, $user_verify->data->user_login); // Set the current user detail
        wp_set_auth_cookie($user_verify->ID); // Set auth details in cookie
        //do_action( 'wp_login', $user_verify->data->user_login );
        if (current_user_can('editor') || current_user_can('administrator')) {
        } else {
            // $litracy_status=get_user_meta($user_verify->ID ,'litracy_status', true );
            // if(!$litracy_status){
            //     $moodle_setting_data=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
            //     if($moodle_setting_data){
            //         wp_redirect(home_url().'?page_id=886&courseid='.$moodle_setting_data->courseid);
            //         exit();
            //     }
            // }

        }
        if (isset($_SESSION['one_planet']['courseid'])) {
            wp_redirect(home_url('/?page_id=1239'));
            exit();
        }
        wp_redirect(home_url('/'));
        exit();
    }
}



get_header(); //1144

?>

<script>
    jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
    jQuery('#content').find(':first-child').removeClass('tg-container--flex');
    //console.log("$('#content:first-child')",jQuery('#content:first-child'));
</script>
<!-- <h1>Hello</h1> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="http://122.176.46.118/learnoneplanet/wp-content/themes/zakra/assets/css/style.css" rel="stylesheet">

<div class="elementor-custom-margin">
    <div class="row">
        <div class="col-12 col-lg-6 col-md-6">
            <div class="login-background">
                <div class="wrapper-content">
                    <img src="http://122.176.46.118/learnoneplanet/wp-content/uploads/2023/03/Picture__2.png" alt="" class="logo-img" />
                </div>
            </div>
        </div>
        <?php
        $labelupusername = "";
        $labeluppassword = "";
        if (!empty($username)) {
            $labelupusername = 'floating-label-up';
        }
        if (!empty($password)) {
            $labeluppassword = 'floating-label-up';
        }
        ?>
        <div class="col-12 col-lg-6 col-md-6 custom-my">
            <div class="form-container">
                <div class="form-wrap ">
                    <h1 class="display-1 fw-bold mb-5 text-center">LOGIN</h1>
                    <form method="post" id="user-login-form" action="">
                        <div class="wrap">
                            <div class="floating-label-group input-parent">
                                <input type="text" id="username" name="username" class="form-control required" autocomplete="off" value="<?php echo $username; ?>" />
                                <label class="floating-label <?php echo $labelupusername; ?>">Username</label>
                            </div>
                            <div class="floating-label-group input-parent mb-3">
                                <input type="password" id="password3-field" name="password" class="form-control required" autocomplete="off" value="<?php echo $password; ?>" />
                                <a href="javascript:void(0);" toggle="#password3-field" class="fa fa-fw fa-eye field-icon toggle-password text-decoration-none text-dark"></a>
                                <label class="floating-label <?php echo $labeluppassword; ?>">Password</label>
                            </div>
                            <div class="floating-label-group input-parent mb-3">
                                <input type="checkbox" id="rememberme" class="cursor-pointer" name="rememberme">
                                <label for="rememberme" class="cursor-pointer">Remember Me</label>
                            </div>
                            <input type="submit" name="submit" value="LOGIN" class="w-100 fs-4 fw-bold" />
                            <div class="mt-3 text-center">
                                <span>Don't have an account?</span><span><a href="<?php echo home_url('/?page_id=1053') ?>" class="ms-2">REGISTER</a></span>
                            </div>
                            <div>
                                <?php if (!empty($message)) {
                                    echo '<div class="alert alert-danger mt-3 text-center text-transform-uppercase" role="alert">' . $message . '</div>';
                                } ?>
                                <div>
                                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    $("#user-login-form input").focusin(function() {
        console.log('sssss');
        if ($(this).val() == "") {
            $(this).closest(".floating-label-group").find('.floating-label').addClass('floating-label-up');
        }
    });
    $("#user-login-form input").focusout(function() {
        if ($(this).val() == "") {
            $(this).closest(".floating-label-group").find('.floating-label').removeClass('floating-label-up');
        }
    });
    $(".toggle-password").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if ($(this).closest('.floating-label-group').find('input').attr('type') == "password") {
            $(this).closest('.floating-label-group').find('input').attr("type", "text");
        } else {
            $(this).closest('.floating-label-group').find('input').attr("type", "password");
        }
    });
    //
    // $('#user-login-form').submit(function(e){
    // 	e.preventDefault();
    // 	var=flag=true;
    // 	var form_error={};
    // 	$(this).find('input').each(function(){
    // 		if($(this).hasClass('required') && $(this).val()==""){
    // 			flag=false;
    // 		}
    // 	});
    // 	if(!flag){
    // 		return false;
    // 	}

    // });
</script>
<?php get_footer(); ?>