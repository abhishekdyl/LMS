<?php
/* Custom Theme Functions */
define('SITE_KEY','6LfW34EkAAAAABwMYtHb30MRxMeshbL4KLQ43G79');
define('SECRET_KEY','6LfW34EkAAAAAMECScL3AvQ_8zI2pKibQyRe369z');
define('GSITE_VERIFY_URL','https://www.google.com/recaptcha/api/siteverify');
function custom_assets_load()
{
    $theme_path = get_template_directory_uri().'-child';
    $theme_version = wp_get_theme()->get('Version');

    wp_enqueue_style('custom', $theme_path . '/framework/assets/css/custom.css', array(), $theme_version);

    /* Only Cart Page Bootstrap */
    if (is_page('cart') || is_page('student-list') || is_page('my-courses') || is_product()) {
        wp_enqueue_style('bootstrap', $theme_path . '/framework/assets/css/bootstrap.min.css', array(), $theme_version);
        wp_enqueue_script('bootstrap', $theme_path . '/framework/assets/js/bootstrap.min.js', array(), $theme_version, true);

        wp_enqueue_style('jquery-datepicker', $theme_path . '/framework/assets/css/jquery-datepicker/jquery-ui.css', array(), $theme_version);
        wp_enqueue_script('jquery-datepicker', $theme_path . '/framework/assets/js/jquery-datepicker/jquery-ui.min.js', array(), $theme_version, true);
    }
    if (is_page('cart')){
        wp_enqueue_script('captcha','https://www.google.com/recaptcha/api.js?render='.SITE_KEY);
    }

    wp_enqueue_style('slick', $theme_path . '/framework/assets/css/slick.min.css', array(), $theme_version);
    wp_enqueue_style('slick', $theme_path . '/framework/assets/css/slick-theme.min.css', array(), $theme_version);

    wp_enqueue_script('slick-slider', $theme_path . '/framework/assets/js/slick.min.js', array(), $theme_version, true);

    wp_enqueue_script('jquery', $theme_path . '/framework/assets/js/jquery.min.js', array(), $theme_version, true);

    wp_enqueue_script('jquery.validate', $theme_path . '/framework/assets/js/jquery.validate-min.js', array(), $theme_version, true);
    wp_enqueue_script('jquery.validate-other', $theme_path . '/framework/assets/js/jquery.validate-other-min.js', array(), $theme_version, true);
    wp_enqueue_script('custom', $theme_path . '/framework/assets/js/custom.js', array(), $theme_version, true);
    wp_localize_script('custom', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

}
add_action('wp_enqueue_scripts', 'custom_assets_load');

/*Styles and Scripts For Admin*/
if (is_admin()) {
    function admin_scripts() {
        $theme_path = get_template_directory_uri().'-child';
        $theme_version = wp_get_theme()->get('Version');

        wp_enqueue_style('admin-css', $theme_path . '/framework/assets/css/admin.css', array(), $theme_version);
        wp_enqueue_script('admin-js', $theme_path . '/framework/assets/js/admin.js', array(), $theme_version, true);

        wp_enqueue_style('jquery-datepicker', $theme_path . '/framework/assets/css/jquery-datepicker/jquery-ui.css', array(), $theme_version);
        wp_enqueue_script('jquery-datepicker', $theme_path . '/framework/assets/js/jquery-datepicker/jquery-ui.min.js', array(), $theme_version, true);

        wp_enqueue_style('jquery-timepicker', $theme_path . '/framework/assets/css/jquery-timepicker/jquery.timepicker.min.css', array(), $theme_version);
        wp_enqueue_style('jquery-timepicker', $theme_path . '/framework/assets/css/jquery-timepicker/jquery.timepicker-style.css', array(), $theme_version);
        wp_enqueue_style('jquery-timepicker', $theme_path . '/framework/assets/css/jquery-timepicker/syntax.css', array(), $theme_version);
        wp_enqueue_script('jquery-timepicker', $theme_path . '/framework/assets/js/jquery-timepicker/jquery.timepicker.min.js', array(), $theme_version, true);

        wp_enqueue_style('bootstrap', $theme_path . '/framework/assets/css/bootstrap/flatpickr-new.css', array(), $theme_version);
        wp_enqueue_style('bootstrap', $theme_path . '/framework/assets/css/bootstrap/month-select.css', array(), $theme_version);
        wp_register_script('bootstrap', $theme_path . '/framework/assets/js/bootstrap/flatpickr-new.js');
        wp_register_script('bootstrap', $theme_path . '/framework/assets/js/bootstrap/month-select-index.js');
        wp_register_script('bootstrap', $theme_path . '/framework/assets/js/bootstrap/flatpickr-custom.js');
        wp_enqueue_script('bootstrap');
    }
    add_action('admin_enqueue_scripts', 'admin_scripts');
}

/* For Student Registration */
function student_register_login() {
    global $wpdb;
    // echo "<pre>-----formdata------- ";
    // print_r($_POST);
    // echo "</pre>";
    // die;
   // $student_timezone = $_POST['student_selected_timezone'];
    $first_name = $_POST['student_fname'];
    $last_name = $_POST['student_lname'];
    $email = $_POST['student_email'];
    $student_birth_date = $_POST['student_dob'];
    $randamn = rand(100,1000);
    $username = generateusername($first_name,$randamn); 
    //$username = $_POST['student_username'];
    $password = $_POST['student_password'];
    $confirm_password = $_POST['student_confirm_password'];
    $current_user_id = $_POST['clogin_id'];
    $account_type = $_POST['account_type'];
    $phone_number = $_POST['phone'];
    $student_meta_key = 'student_login_id';
    $url = get_site_url().'/cart/';
    $c_message = 'Success';
    /* Send mail */
    $send_mail = false;
    $is_child = false;
    /* End send mail */
    if (!isset($_POST['action']) || $_POST['action'] !== 'student_register_login')
        return;
    if (false === check_captcha() && isset( $_POST['recaptcha-response'] )){
        echo json_encode(array('success' => false, 'message' => 'reCAPTCHA verification failed.'));
        die();
    }
    if (username_exists($username) == null && email_exists($email) == false && !empty($password) && !empty($confirm_password) && $password == $confirm_password && (empty($account_type) || $account_type  == 'parent') ) {
        $user_id = wp_create_user($username, $password, $email);
        $user_id_role = new WP_User($user_id);
        $user = get_user_by('id', $user_id);
        update_user_meta($user->ID, 'student_timezone', sanitize_text_field($student_timezone));
        update_user_meta($user->ID, 'first_name', sanitize_text_field($first_name));
        update_user_meta($user->ID, 'last_name', sanitize_text_field($last_name));
        update_user_meta($user->ID, 'phone', sanitize_text_field($phone_number));
        if (!is_user_logged_in()) {
            /* Add Parent Role */
            $user_id_role->remove_role('student');
            $user_id_role->add_role('parent_wp');
            /* End Parent Role */
            update_user_meta($user->ID, 'parent_value_'.$user->ID, base64_encode($password));
            woofc_get_cart_after_login($user->user_login, $user);
            bp_set_member_type( $user_id, 'paren' );
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            $send_mail = true;
        }
        elseif (is_user_logged_in()){
            $user_data = $wpdb->get_row("SELECT * FROM $wpdb->usermeta WHERE user_id = '{$current_user_id}' AND meta_key LIKE '{$student_meta_key}' ");
            $data = unserialize($user_data->meta_value);
            if(!is_array($data)){
                $data = array();
            }
            array_push($data, $user->ID);
            $update_data1 = serialize($data);

            if ($user_data) {
                $wpdb->query("UPDATE $wpdb->usermeta SET meta_value = '{$update_data1}' WHERE user_id = '{$current_user_id}' AND meta_key LIKE '{$student_meta_key}' ");
                $c_message = 'Update Successful';
            } elseif (empty($user_data)) {
                $c_message = 'Insert Successful';
                update_user_meta($current_user_id, $student_meta_key, array($user->ID));
            }
            /*update_user_meta($current_user_id, 'student_login_id', array($user->ID));*/
            update_user_meta($user->ID, 'parent_login_id', $current_user_id);
            update_user_meta($user->ID, 'student_value_'.$user->ID, base64_encode($password));
            update_user_meta($user->ID, 'student_birth_date', $student_birth_date);

            xprofile_set_field_data( bp_xprofile_firstname_field_id(), $user->ID, get_user_meta( $user->ID, 'first_name', true ) );
            xprofile_set_field_data( bp_xprofile_lastname_field_id(), $user->ID, get_user_meta( $user->ID, 'last_name', true ) );

            /* Calculate student date */
            $student_age = get_student_age($student_birth_date);
            $user_id_role->remove_role('follower');
            if ($student_age) {
                bp_set_member_type($user_id, 'k-12-student');
                $user_id_role->add_role('student');
            }else{
                bp_set_member_type( $user_id, 'primary-student' );
                $user_id_role->add_role('student_primary');

            }

            $url = get_site_url().'/cart/';
            $send_mail = true;
            $is_child = true;
        }
        echo json_encode(array('success' => true, 'url' => $url, 'message' => 'Registration successful. Redirecting...', 'custom_message' => $c_message, 'msg' => 'parent'));
    }
    elseif (username_exists($username) == null && email_exists($email) == false && !empty($password) && !empty($confirm_password) && $password == $confirm_password && (empty($account_type) || $account_type == 'adult_learner') ) {
        $user_id = wp_create_user($username, $password, $email);
        $user_id_role = new WP_User($user_id);
        $user = get_user_by('id', $user_id);
        update_user_meta($user->ID, 'first_name', sanitize_text_field($first_name));
        update_user_meta($user->ID, 'last_name', sanitize_text_field($last_name));
        update_user_meta($user->ID, 'phone', sanitize_text_field($phone_number));
        if (!is_user_logged_in()) {
            /* Add Parent Role */
            $user_id_role->remove_role('student');
            $user_id_role->add_role('adult_learner');
            /* End Parent Role */
            update_user_meta($user->ID, 'parent_value_'.$user->ID, base64_encode($password));
            woofc_get_cart_after_login($user->user_login, $user);
            bp_set_member_type( $user_id, 'k-6-student' );
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            $send_mail = true;
        }
        echo json_encode(array('success' => true, 'url' => $url, 'message' => 'Registration successful. Redirecting...', 'custom_message' => 'student'));
    } else {
        echo json_encode(array('success' => false, 'message' => 'User account already exists.'));
    }

    if ($is_child){
        $parent = wp_get_current_user();
        $puser_data = array(
            'p_name' => $parent->first_name.' '.$parent->last_name,
            'p_uname' => $parent->user_login,
            'p_email' => $parent->user_email,
        );

        $child_data = array(
            's_name' => $user->first_name.' '.$user->last_name,
            's_uname' => $user->user_login,
            's_email' => $user->user_email,
        );

        $parent_message = '<p>Parent Name : '.$puser_data['p_name'].'<br>
                              Parent Username : '.$puser_data['p_uname'].'<br>
                              Parent Email : '.$puser_data['p_email'].'</p>';

        $child_message = '<p>Student Name : '.$child_data['s_name'].'<br>
                              Student Username : '.$child_data['s_uname'].'<br>
                              Student Email : '.$child_data['s_email'].'</p>';
    }else{
        $puser_data = array(
            'p_name' => $user->first_name.' '.$user->last_name,
            'p_uname' => $user->user_login,
            'p_email' => $user->user_email,
        );
        $parent_message = '<p>Name : '.$puser_data['p_name'].'<br>
                              Username : '.$puser_data['p_uname'].'<br>
                              Email : '.$puser_data['p_email'].'</p>';
    }
    if ($send_mail) {
        $url_link = get_site_url().'/my-courses/';
        $subject = 'Your Lemons-Aid Learning Account Details';
        $message = '<p>Dear '.$puser_data['p_name'].',<br> Welcome to Lemons-Aid Learning! Here are your login credentials:</p>
                '.$parent_message .$child_message.
            '<p>To log in and view your courses, click here: <a href="'.$url_link.'">Link to login</a><br></p>
                 <p>We can\'t wait to see you!<br></p>
                <p>For Him and for you,<br>
                    Karen Lemons</p>';
        send_custom_email($puser_data['p_email'],$subject,$message);
    }
    die();
}
add_action('wp_ajax_student_register_login', 'student_register_login');
add_action('wp_ajax_nopriv_student_register_login', 'student_register_login');
//add_action('init', 'student_register_login');

/* For Student Registration And Add/Edit */
function student_edit_action() {
    global $wpdb, $current_user;
    $student_id = $_POST['student_id'];
    $student_type = $_POST['student_type'];

    $first_name = $_POST['student_fname'];
    $last_name = $_POST['student_lname'];
    $email = $_POST['student_email'];
    $student_birth_date = $_POST['student_dob'];
    $username = $_POST['student_username'];
    $password = $_POST['student_password'];
    $confirm_password = $_POST['student_confirm_password'];

    $student_meta_key = 'student_login_id';
    $finaldobextend = "";
    if(!empty($student_birth_date)){
        $date = DateTime::createFromFormat('m-d-Y', $student_birth_date);
        if($date){
            $finaldobextend = date("Y-m-d h:i:s", $date->format('U'));      
        }
    }


    $url = get_site_url(). '/student-list';
    $custom_message = '';
    /* Send mail */
    $send_mail = false;
    /* End send mail */
    if (!isset($_POST['action']) || $_POST['action'] !== 'student_edit_action')
        return;

    if ($student_type == 'student-edit') {
        if (username_exists($username) && is_user_logged_in()) {
            update_user_meta($student_id, 'first_name', sanitize_text_field($first_name));
            update_user_meta($student_id, 'last_name', sanitize_text_field($last_name));
            update_user_meta($student_id, 'student_birth_date', $student_birth_date);
            // update_user_meta($student_id, 'student_birth_date', $finaldobextend);
            custom_updateuserdate($student_id, $student_birth_date, $finaldobextend);
            /* Calculate student date */
            $student_age = get_student_age($student_birth_date);
            if ($student_age) {
                bp_remove_member_type($student_id,'primary-student');
                bp_set_member_type($student_id, 'k-12-student');
            }else{
                bp_remove_member_type($student_id,'k-12-student');
                bp_set_member_type($student_id, 'primary-student');
            }

            if (!empty($password) && !empty($confirm_password)) {
                if ($password == $confirm_password)
                    wp_update_user(array('ID' => $student_id, 'user_pass' => esc_attr($password)));
                    update_user_meta($student_id, 'student_value_'.$student_id, base64_encode($password));
            }

            if (!empty($email)) {
                wp_update_user(array('ID' => $student_id, 'user_email' => esc_attr($email)));
            }
            echo json_encode(array('success' => true, 'url' => $url, 'message' => 'Submit successful, redirecting...', 'custom_message' => $custom_message));
        }
    } elseif ($student_type == 'student-create'){
            // working
        $randamn = rand(100,1000);
        $username = generateusername($first_name,$randamn); 
        if (username_exists($username) == null && email_exists($email) == false && !empty($password) && !empty($confirm_password) && $password == $confirm_password){
            $user_id = wp_create_user($username, $password, $email);
            $user = get_user_by('id', $user_id);
            $user_id_role2 = new WP_User($user_id);
            update_user_meta($user->ID, 'first_name', sanitize_text_field($first_name));
            update_user_meta($user->ID, 'last_name', sanitize_text_field($last_name));
            update_user_meta($user->ID, 'student_birth_date', $student_birth_date);
            custom_updateuserdate($student_id, $student_birth_date, $finaldobextend);

            // echo "<pre>";
            // echo "-----------------------------------------------------";
            // print_r($student_birth_date);
            // echo "</pre>";

            xprofile_set_field_data( bp_xprofile_firstname_field_id(), $user->ID, get_user_meta( $user->ID, 'first_name', true ) );
            xprofile_set_field_data( bp_xprofile_lastname_field_id(), $user->ID, get_user_meta( $user->ID, 'last_name', true ) );

            /* Calculate student date */
            $student_age = get_student_age($student_birth_date);
            $user_id_role2->remove_role('follower');
            if ($student_age) {
                bp_set_member_type($user->ID, 'k-12-student');
                $user_id_role2->add_role('student');
            }else{
                bp_set_member_type($user->ID, 'primary-student');
                $user_id_role2->add_role('student_primary');
            }

            /* Add New Student Id In Parent Usermeta */
            $user_data = $wpdb->get_row("SELECT * FROM $wpdb->usermeta WHERE user_id = '{$current_user->ID}' AND meta_key LIKE '{$student_meta_key}' ");
            $data = unserialize($user_data->meta_value);
            if(!is_array($data)){
                $data = array();
            }
            array_push($data, $user->ID);
            $update_data1 = serialize($data);



            if ($user_data) {
                $wpdb->query("UPDATE $wpdb->usermeta SET meta_value = '{$update_data1}' WHERE user_id = '{$current_user->ID}' AND meta_key LIKE '{$student_meta_key}' ");
                $custom_message = 'Update Successful';
            }

            /*update_user_meta($current_user->ID, 'student_login_id', array($user->ID));*/
            update_user_meta($user->ID, 'parent_login_id', $current_user->ID);
            update_user_meta($user->ID, 'student_value_'.$user->ID, base64_encode($password));
            $send_mail = true;
        }
        echo json_encode(array('success' => true, 'url' => $url, 'message' => 'Submit successful, redirecting...', 'custom_message' => $custom_message));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Not Success'));
    }

    if ($send_mail) {

        $parent = wp_get_current_user();
        $puser_data = array(
            'p_name' => $parent->first_name.' '.$parent->last_name,
            'p_uname' => $parent->user_login,
            'p_email' => $parent->user_email,
        );

        $child_data = array(
            's_name' => $user->first_name.' '.$user->last_name,
            's_uname' => $user->user_login,
            's_email' => $user->user_email,
        );
        $child_message = '<p>Student Name : '.$child_data['s_name'].'<br>
                              Student Username : '.$child_data['s_uname'].'<br>
                              Student Email : '.$child_data['s_email'].'</p>';

        $parent_message = '<p>Parent Name : '.$puser_data['p_name'].'<br>
                              Parent Username : '.$puser_data['p_uname'].'<br>
                              Parent Email : '.$puser_data['p_email'].'</p>';

        $url_link = get_site_url().'/my-courses/';
        $subject = 'Welcome to Lemons-Aid!';
        $message = '<p>Dear '.$puser_data['p_name'].',<br> Welcome to Lemons-Aid Learning! Here are your login credentials:</p>
                '.$parent_message .$child_message.
            '<p>To log in and view your courses, click here: <a href="'.$url_link.'">Link to login</a><br></p>
                 <p>We can\'t wait to see you!<br></p>
                <p>For Him and for you,<br>
                    Karen Lemons</p>';
        send_custom_email($puser_data['p_email'],$subject,$message);
    }
    die();
}
add_action('wp_ajax_student_edit_action', 'student_edit_action');
//add_action('wp_ajax_nopriv_student_edit', 'student_edit');
// lds custom function
function generateusername($firstnam, $randamno){
   global $wpdb;
    $firstnam=str_replace(" ","",trim($firstnam));
    $username=strtolower($firstnam).'-'.$randamno;
    $error = false;
    if ( !$error && username_exists( $username ) ){
        $randamno = rand(100,1000);
        $username = generateusername($firstnam,$randamno);
    }
    return $username;
}

/* For Student Login */
function student_login() {
    if (!isset($_POST['action']) || $_POST['action'] !== 'student_login') {
        return;
    }

    if (true === check_captcha() && isset( $_POST['recaptcha-response'] )){
        $info = array();
        $info['user_login'] = $_POST['student_username'];
        $info['user_password'] = $_POST['student_password'];
        $user_signon = wp_signon($info, false);

        if (is_wp_error($user_signon)) {
            echo json_encode(array('success' => false, 'message' => 'Wrong Username or Password.'));
        }

        if (!is_wp_error($user_signon)) {
            $id = $user_signon->ID;
            wp_set_current_user($id);
            $url = get_home_url() . '/cart/';
            echo json_encode(array('success' => true, 'url' => $url, 'message' => 'Login successful, redirecting...'));
        }
    }else{
        echo json_encode(array('success' => false, 'message' => 'reCAPTCHA verification failed.'));
    }
    die();
}
add_action('wp_ajax_nopriv_student_login', 'student_login');
//add_action('init', 'student_login');

/* Check email is registered or not */
function student_email_check()
{
    $user_email = $_REQUEST['user_email'];

    if (email_exists(esc_attr($user_email))) {
        echo json_encode(array('success' => false, 'message' => 'Email was already registered'));
    }
    else{
        echo json_encode(array('success' => true, 'message' => 'Success'));
    }
    die();
}

add_action('wp_ajax_student_email_check', 'student_email_check');
add_action('wp_ajax_nopriv_student_email_check', 'student_email_check');

/* Check Username is registered or not */
function student_username_check()
{
    $username = $_REQUEST['username'];

    if (username_exists($username) == null){
        echo json_encode(array('success' => true, 'message' => 'Success'));
    }
    else{
        echo json_encode(array('success' => false, 'message' => 'Username is already registered'));
    }
    die();
}

add_action('wp_ajax_student_username_check', 'student_username_check');
add_action('wp_ajax_nopriv_student_username_check', 'student_username_check');

/* Update Course Group Selection */
function custom_update_coursegroup() {
    /*if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'woocommerce-cart' ) ) {
        wp_send_json( array( 'nonce_fail' => 1 ) );
        exit;
    }*/
    global $woocommerce;
    if (isset($_POST['product_group_id'])){
        $woocommerce->cart->add_to_cart($_POST['product_id']);
        $cart = WC()->cart->cart_contents;
        foreach( $cart as $cart_item_id => $cart_item ) {
            if ($cart_item['product_id'] == $_POST['product_id']) {
                $cart_item['product_group_id'] = $_POST['product_group_id'];
                WC()->cart->cart_contents[$cart_item_id] = $cart_item;
                WC()->cart->set_session();
            }
        }
        $reload = do_shortcode('[woocommerce_cart]'); // Important To Update Cart
        if ($reload){
            wp_send_json( array('success' => 1, 'message' => 'success', 'url' => wc_get_cart_url()));
        }
        wp_send_json( array('success' => 1, 'message' => 'success', 'url' => wc_get_cart_url()));
    }else{
        wp_send_json( array('success' => 0, 'message' => 'You need to select child from list or create new one for this products'));
    }
    exit;
}
add_action( 'wp_ajax_custom_update_coursegroup', 'custom_update_coursegroup' );
add_action( 'wp_ajax_nopriv_custom_update_coursegroup', 'custom_update_coursegroup' );

/* Check Product Id On Update Cart */
function custom_update_cart() {
    if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'woocommerce-cart' ) ) {
        wp_send_json( array( 'nonce_fail' => 1 ) );
        exit;
    }
    $product_student_id = $_POST['product_student_id'];
    if (!empty($product_student_id)){
        $cart = WC()->cart->cart_contents;
        $cart_id = $_POST['cart_id'];
        $cart_item = $cart[$cart_id];
        $cart_item['product_student_id'] = $product_student_id;
        WC()->cart->cart_contents[$cart_id] = $cart_item;
        WC()->cart->set_session();
        $reload = do_shortcode('[woocommerce_cart]'); // Important To Update Cart
        if ($reload){
            wp_send_json( array('success' => 1, 'message' => 'success', ''));
        }
        wp_send_json( array('success' => 1, 'message' => 'success', ''));
    }else{
        wp_send_json( array('success' => 0, 'message' => 'You need to select child from list or create new one for this products'));
    }
    exit;
}
add_action( 'wp_ajax_custom_update_cart', 'custom_update_cart' );
add_action( 'wp_ajax_nopriv_custom_update_cart', 'custom_update_cart' );

function quadlayers_subscribe_checkout( $checkout ) {
    $product_child_name = WC()->cart->get_cart_contents();
    echo '<div id="student_id_field">';
    foreach( $product_child_name as $cart_item ){
        $product_student_id = $cart_item['product_student_id'];
        echo '<input type="hidden" class="input-hidden" name="student_id" id="student_id" value="' . $product_student_id . '">';
    }
    echo '</div>';
}
add_action( 'woocommerce_after_order_notes', 'quadlayers_subscribe_checkout');

function wps_select_checkout_field_process($order_id) {

    // Check if set, if its not set add an error.
    if (empty($_POST['student_name']))
        wc_add_notice( '<strong>Student name is required</strong>', 'error' );

    if (!empty($_POST['student_id']))
        update_post_meta($order_id,'_student_id', sanitize_text_field($_POST['student_id']));
}

add_action('woocommerce_checkout_update_order_meta', 'wps_select_checkout_field_update_order_meta');

/* Update Order Meta To Add Custom Field */
function wps_select_checkout_field_update_order_meta( $order_id ) {

    $arr = array();
    $group_arr = array();
    $product_child_name = WC()->cart->get_cart_contents();
    foreach( $product_child_name as $cart_item ){
        $product_student_id = $cart_item['product_student_id'];
        $product_group_id = $cart_item['product_group_id'];
        $arr[] = $product_student_id;
        $group_arr[] = $product_group_id;
    }
    update_post_meta( $order_id, 'student_selected_id', $arr);
    update_post_meta( $order_id, 'product_group_id', $group_arr);
}

/* Add Student Name In Admin Panel Order List */
function my_custom_billing_fields_display_admin_order_meta1($order) {
    $student_name = get_post_meta($order->id, 'student_selected_id',true);
    $group_id = get_post_meta($order->id,'product_group_id',true);
    $order_user_id = $order->get_user_id();
    $phone_number = get_user_meta($order_user_id, 'phone', true);

    if (!in_array(null, $student_name, true) && !in_array('self_purchase',$student_name)){
        $customer = $student_name;
    }else{
        $customer = (array) get_post_meta($order->id,'_customer_user',true);
    }
    echo '<p><strong>' . __('Student Name') . ':</strong><br> ';
    foreach ($customer as $name){
        $username = get_user_by('id', $name);
        echo $username->first_name." ". $username->last_name . ", ";
    }
    echo '</p>';
    echo '<p><strong>' . __('Student username') . ':</strong><br> ';
    foreach ($customer as $name){
        $username = get_user_by('id', $name);
        echo $username->user_nicename.", ";
    }
    echo '</p>';
    echo '<p><strong>' . __('Student Email') . ':</strong><br> ';
    foreach ($customer as $name){
        $username = get_user_by('id', $name);
        echo $username->user_email.", ";
    }
    echo '</p>';
    if (!in_array('self_purchase',$student_name)) {
        echo '<p><strong>' . __('Student Birth Date') . ':</strong><br> ';
        foreach ($customer as $name) {
            $username = get_user_by('id', $name);
            echo get_user_meta($username->ID, 'student_birth_date', true) . ", ";
        }
        echo '</p>';
    }
    if ($group_id) {
        echo '<p><strong>' . __('Group Name') . ':</strong><br> ';
        $selectedgroups = !empty($group_id) ? moodleservice('get_groups', ['groupids' => (array)$group_id]) : [];
        if (!empty($selectedgroups)) {
            foreach ($selectedgroups as $group) {
                echo $group->name;
            }
        }
        echo '</p>';
    }
    if ($phone_number){
        echo '<p><strong>' . __('Student Phone Number') . ':</strong><br> ';
        echo '<a href="tel:' . $phone_number . '">' . $phone_number . '</a>';
        echo '</p>';
    }
}
add_action('woocommerce_admin_order_data_after_billing_address', 'my_custom_billing_fields_display_admin_order_meta1', 10, 1);

/* Add custom order meta data to make it accessible in Order preview template */
function admin_order_preview_add_custom_meta_data( $data, $order ) {
    $custom_value = $order->get_meta('student_selected_id');
    $group_id = $order->get_meta('product_group_id');
    $order_user_id = $order->get_user_id();
    $phone_number = get_user_meta($order_user_id, 'phone', true);

    if (!in_array(null, $custom_value, true) && !in_array('self_purchase',$custom_value)){
        $customer = $custom_value;
    }else{
        $customer = array_column($data, 'customer_id');
    }
    $name_arr = array();
    $username_arr = array();
    $email_arr = array();
    $birth_date_arr = array();
    $group_id_arr = array();
    foreach ($customer as $name) {
        $username = get_user_by('id', $name);
        $name_arr[] = $username->first_name . " " . $username->last_name;
        $username_arr[] = $username->user_nicename;
        $email_arr[] = $username->user_email;
        $birth_date_arr[] = get_user_meta($username->ID,'student_birth_date',true);
    }
    $selectedgroups = !empty($group_id) ? moodleservice('get_groups', ['groupids' => (array) $group_id]): [];
    if(!empty($selectedgroups)) {
        foreach ($selectedgroups as $group) {
            $group_id_arr[] = $group->name;
        }
    }

    if( !in_array('self_purchase',$custom_value) ) {
        $data['student_selected_name'] = $name_arr;
        $data['student_selected_id'] = $username_arr;
        $data['student_selected_email'] = $email_arr;
        $data['student_birth_date'] = $birth_date_arr;
    }else{
        $data['student_selected_name'] = $name_arr;
        $data['student_selected_id'] = $username_arr;
        $data['student_selected_email'] = $email_arr;
        $data['self_purchase'] = 'Enroll Myself';
    }
    if ($group_id){
        $data['group_name'] = $group_id_arr;
    }

    if ($phone_number){
        $data['student_phone_number'] = (array) $phone_number;
    }
    return $data;
}
add_filter( 'woocommerce_admin_order_preview_get_order_details', 'admin_order_preview_add_custom_meta_data', 10, 2 );

/* Display custom values in Order preview */
function custom_display_order_data_in_admin(){
    // echo '<div class="wc-order-preview-table-wrapper"><table class="wc-order-preview-table"><tbody><tr><td>Student Name: <table class="wc-order-item-meta"><tbody><tr><td>{{data.student_selected_id}}</td></tr></tbody></table></td></tr><td>Student Email: <table class="wc-order-item-meta"><tbody><tr><td>{{data.student_selected_email}}</td></tbody></table></div><br>';
    // echo '<div class="wc-order-preview-table-wrapper"><table class="wc-order-preview-table"><tbody><tr><td>Student Name: <table class="wc-order-item-meta"><tbody><tr><td>{{data.student_selected_id}}</td></tr></tbody></table></td></tr><td>Student Email: <table class="wc-order-item-meta"><tbody><tr><td>{{data.student_selected_email}}</td></tr></tbody></table></td></tbody></table></div><br>';
    echo '<div class="table-responsive wc-order-preview-table-wrapper">
            <# if ( !data.self_purchase ) { #>
            <div class="student-heading" style="display: flex;justify-content: center;"><h2>Student Details</h2></div>
            <table class="wc-order-preview-table table">
                <tbody>
                    <tr>
                        <td class="student-name"><strong>Student Name</strong></td>
                        <td>{{data.student_selected_name}}</td>
                    </tr>
                    <tr>
                        <td class="student-username"><strong>Student username:</strong></td>
                        <td>{{data.student_selected_id}}</td>
                    </tr>
                    <tr>
                        <td class="student-email"><strong>Student Email</strong></td>
                        <td>{{data.student_selected_email}}</td>
                    </tr>
                    <tr>
                        <td class="student-birth-date"><strong>Student Birth Date</strong></td>
                        <td>{{data.student_birth_date}}</td>
                    </tr>
                    <# if (data.group_name) { #>
                        <tr>
                            <td class="group-name"><strong>Group Name</strong></td>
                            <td>{{data.group_name}}</td>
                        </tr>
                    <# } #>
                    <# if (data.student_phone_number) { #>
                        <tr>
                            <td class="student-phone-number"><strong>Student Phone Number</strong></td>
                            <td><a href="tel:{{data.student_phone_number}}">{{data.student_phone_number}}</a></td>
                        </tr>
                    <# } #>
                </tbody>
            </table>
            <# } else { #>
                <div class="student-heading" style="display: flex;justify-content: center;"><h2>Student Details</h2></div>
                <table class="wc-order-preview-table table">
                    <tbody>
                        <tr>
                            <td class="student-name"><strong>Student Name</strong></td>
                            <td>{{data.student_selected_name}}</td>
                        </tr>
                        <tr>
                            <td class="student-username"><strong>Student username:</strong></td>
                            <td>{{data.student_selected_id}}</td>
                        </tr>
                        <tr>
                            <td class="student-email"><strong>Student Email</strong></td>
                            <td>{{data.student_selected_email}}</td>
                        </tr>
                        <# if (data.group_name) { #>
                            <tr>
                                <td class="group-name"><strong>Group Name</strong></td>
                                <td>{{data.group_name}}</td>
                            </tr>
                        <# } #>
                        <# if (data.student_phone_number) { #>
                            <tr>
                                <td class="student-phone-number"><strong>Student Phone Number</strong></td>
                                <td><a href="tel:{{data.student_phone_number}}">{{data.student_phone_number}}</a></td>
                            </tr>
                        <# } #>
                    </tbody>
                </table>
            <# } #>
        </div>';
}
add_action( 'woocommerce_admin_order_preview_end', 'custom_display_order_data_in_admin' );

/* Add selection field value to emails */
function wps_select_order_meta_keys( $keys ) {

    $keys['Student:'] = 'student_name';
    return $keys;

}
add_filter('woocommerce_email_order_meta_keys', 'wps_select_order_meta_keys');

/* Add Student Name After Product Name In Order List */
function prefix_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
    foreach( $item as $cart_item_key=>$cart_item ) {
        if( isset( $cart_item['product_student_id'] ) && $cart_item['product_student_id'] != 'self_purchase' ) {
            $product_student_id = $cart_item['product_student_id'];
            $username = get_user_by('id', $product_student_id);
            $item->add_meta_data( 'Student Name', $username->user_nicename, true );
        }
        if( isset( $cart_item['product_group_id'] ) ) {
            $item->add_meta_data( 'product_group_id', $cart_item['product_group_id'], true );
        }
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'prefix_checkout_create_order_line_item', 10, 4 );

/* Add Selected Student Name In Order */
function custom_checkout_cart_item_name( $item_qty, $cart_item, $cart_item_key ) {
    if (isset($cart_item['product_student_id']) && $cart_item['product_student_id'] != 'self_purchase') {
        $product_student_id = $cart_item['product_student_id'];
        $username = get_user_by('id', $product_student_id);
        $name = '<br><span class="product-student"><strong>' . __("Student Name", "woocommerce") . ':</strong> <br>' . $username->first_name . " " . $username->last_name . '</span>';
    }

    if (isset($cart_item['product_group_id'])){
        $selectedgroups = !empty($cart_item['product_group_id']) ? moodleservice('get_groups', ['groupids' => (array) $cart_item['product_group_id']]): [];
        if(!empty($selectedgroups)) {
            $selectedgroupid = $cart_item['product_group_id'] ?? 0;
            foreach($selectedgroups as $group){
                if($selectedgroupid == $group->id){
                    $name .= '<br><span class="product-group"><strong>' . __("Group Name", "woocommerce") . ':</strong> <br>' . $group->name . '</span>';
                }
            }
        }
    }

    return $name;
}
add_filter( 'woocommerce_checkout_cart_item_quantity', 'custom_checkout_cart_item_name', 10, 3 );

/* Update Cart Fragments For Select Student */
function custom_update_fragments( $fragments ) {
    $current_user = wp_get_current_user();
    ob_start();
    ?>
    <?php
    foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        ob_start();
        // $edit_url = esc_url(get_site_url() . '/student-list');
        $user_args = array(
            'order' => 'ASC',
            'orderby' => 'user_nicename',
            'meta_key' => 'parent_login_id',
            'meta_value' => $current_user->ID,
        );
        $user_query = new WP_User_Query($user_args);
        $user_results = $user_query->get_results();
        $total_users = $user_query->get_total();
        if ($total_users > 0) :
        echo '<select name="product_student" id="product_student_'.$cart_item_key.'" class="product_student form-group" data-cart-id="'.$cart_item_key.'">
                                    <option value="">-Select-</option>';
        foreach ($user_results as $user) :
            if ($cart_item['product_student_id'] == $user->ID) {
                $selected = "selected='selected'";
            } else {
                $selected = '';
            }
            echo '<option value="'.$user->ID.'" '.$selected.'>'.$user->first_name.' '.$user->last_name.'</option>';
        endforeach;
        if (!array_intersect(array('student'),$current_user->roles)):
            if ($cart_item['product_student_id'] == 'self_purchase') {
                $self_selected = "selected=selected";
            } else {
                $self_selected = '';
            }
            echo '<option value="self_purchase" '.$self_selected.'>Enroll Myself</option>'; 
        endif;
        echo '</select>';
        endif;
        if ($total_users == 0 && !array_intersect(array('student'),$current_user->roles)):
            echo '<select name="product_student" id="product_student_'.$cart_item_key.'" class="product_student form-group" data-cart-id="'.$cart_item_key.'">';
            if ($cart_item['product_student_id'] == 'self_purchase') {
                $self_selected = "selected=selected";
            } else {
                $self_selected = '';
            }
            echo '<option value="">-Select-</option><option value="self_purchase" '.$self_selected.'>Enroll Myself</option>';
        endif;
        // echo '<button type="button" class="student-btn btn btn-sm btn-outline-primary mr-1 col-xs-6" data-id="new_student" data-toggle="modal" data-target="#student-modal">Create an Account</button>';
        // echo sprintf( '<a href="%s">%s</a>', esc_url( get_site_url() . '/student-register' ), 'New Student' ).'<br>';
        // echo '<input type="hidden" value="" id="new_student_id">';
        /*if ($total_users > 0) :
            // echo sprintf( '<a href="%s" class="edit-student">%s</a>', $edit_url, 'Edit Student' );
            // echo sprintf( '<a href="%s" class="edit-student student-btn btn btn-sm btn-outline-primary col-xs-6" data-id="edit_student">%s</a>', $edit_url, 'Edit Student' );
            echo sprintf( '<a href="%s" class="edit-student btn btn-sm btn-outline-primary col-xs-6" data-id="edit_student">%s</a>', $edit_url, 'Add/Edit Student' );
        endif;*/
        $fragments['#product_student_'.$cart_item_key] = ob_get_clean();
    } ?>
    <?php
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'custom_update_fragments', 10, 1 );

/* Student User Pagination */
function student_user_pagination($user_args = array())
{
    $total_users_query = new WP_User_Query($user_args);
    $total_users = $total_users_query->get_total();
    $user_per_page = $user_args['number'];
    $wp_user_query = new WP_User_Query($user_args);
    $total_query = $wp_user_query->get_total();
    $total_pages = intval($total_users / $user_per_page) + 1;

    if ($total_users >= $total_query) {
        echo '<nav class="navigation pagination" aria-label="Posts">
               <h2 class="screen-reader-text">User navigation</h2>
               <div class="nav-links">';
        $current_page = max(1, get_query_var('paged'));

        echo paginate_links(array(
            'base' => get_pagenum_link(1) . '%_%',
            'format' => 'page/%#%/',
            'current' => $current_page,
            'total' => $total_pages,
            'prev_text' => __('<i class="fa fa-angle-left"></i>', 'twentysixteen'),
            'next_text' => __('<i class="fa fa-angle-right"></i>', 'twentysixteen'),
            'show_all' => false,
            /*'type' => 'plain',*/
        ));
        echo '</div></nav>';
    }
}

/* Student Form By User Id */
function student_edit_form(){
    $student_edit_id = $_POST['student_edit_id'];
    $button_name = $_POST['button_name'];
    if ($button_name == 'student-create'){
        $form_title = 'Add ';
        $note = '<label class="mb-0 mt-2" style="font-size: 14px;">Note: Each student needs a unique e-mail address.</label>';
        $pb = ' pb-0';
        $fieldhide = "style='display:none'";
        $readonly = '';
        $studen_birth_date = '';
    }elseif ($button_name == 'student-edit'){
        $form_title = 'Edit ';
        $readonly = 'readonly="readonly"';
        $fieldhide = '';
        $studen_birth_date = get_user_meta($student_edit_id,'student_birth_date',true);
    }
    $user_data = get_userdata($student_edit_id);
    $student_form = '';
    if (!empty($student_edit_id)){
        $zonedata = timezonelist();
        // echo "<pre>";
        // print_r($zonedata);
        // echo "</pre>";
       
        $student_form .= '<div class="card"><div class="card-header'.$pb.'"><h4 class="mb-0">'.$form_title.'Student Form</h4>'.$note.'</div><div class="card card-body"><form class="student_edit col-md-12 row mb-0" name="student_edit_action" id="student_edit" method="post">
                                    <div class="student-fname col-md-4 form-group">
                                        <label for="first_name" class="required">First Name</label>
                                        <input type="text" class="col-md-12 form-control" name="student_fname" id="first_name" value="'.$user_data->first_name.'" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-lname col-md-4 form-group">
                                        <label for="last_name" class="required">Last Name</label>
                                        <input type="text" class="col-md-12 form-control" name="student_lname" id="last_name" value="'.$user_data->last_name.'" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-email col-md-4 form-group">
                                        <label for="student_email" class="required">Email</label>
                                        <input type="email" class="col-md-12 form-control" name="student_email" id="student_email" value="'.$user_data->user_email.'" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-dob col-md-4 form-group">
                                        <label for="student_dob" id="custom_label" class="required">Student Birth Date</label>
                                        <input class="date-picker col-md-12 form-control" id="student_dob" name="student_dob" value="'.$studen_birth_date.'" placeholder="Date Of Birth" required="required">
                                    </div>
                                     <div class="student-dob col-md-4 form-group">
                                        <label for="student_timezone" id="custom_label" class="required">Timezone </label>
                                        <select name="student_timezone" id="student_timezone">';
                                       
                                         foreach ($zonedata as  $key => $listzonedata) {
                                            $student_form .= '<option>'.$listzonedata.'</option>';
                                             };


                        $student_form .= '

                                        </select>
                                    </div>

                                    <div class="student-username col-md-4 form-group" '.$fieldhide.'>
                                        <label for="username" class="required">Username</label>
                                        <input type="text" class="col-md-12 form-control" name="student_username" id="username" value="'.$user_data->user_nicename.'" autocomplete="off" required="required" '.$readonly.'/>
                                    </div>
                                    <div class="student-password col-md-4 form-group">
                                        <label for="password" class="required">Password</label>
                                        <input type="password" class="col-md-12 form-control" name="student_password" id="password" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-confirm-password col-md-4 form-group">
                                        <label for="confirm_password" class="required">Confirm Password</label>
                                        <input type="password" class="col-md-12 form-control" name="student_confirm_password" id="confirm_password" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <input type="hidden" name="action" value="student_edit_action" />
                                    <input type="hidden" name="student_id" value="'.$student_edit_id.'" />
                                    <input type="hidden" name="student_type" value="'.$button_name.'" />
                                    <div class="col-md-12">
                                    <button type="submit" class="submit-button btn btn-md btn-outline-success" name="submit"><span class="student-loader spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span><span>Submit</span></button>
                                    <button type="button" class="cancel-button btn btn-md btn-outline-secondary" name="cancel">Cancel</button>
                                    </div>
                                    <div class="form_message col-md-12 text-center pt-3"></div>
                                </form></div></div>';
        echo json_encode(array('success' => true, 'message' => 'Successfull', 'result' => $student_form));
    }else{
        $result = 'Please Select Valid Student From List';
        echo json_encode(array('success' => false, 'message'=> 'Not Successfull', 'result' => $result));
    }
    die();
}
add_action( 'wp_ajax_student_edit_form', 'student_edit_form' );


/* Student Modal Show */
function student_modal_show(){
    $current_user_id = wp_get_current_user()->ID;
    $student_data = $_POST['student_data'];
    $student_modal = '';
    if (!empty($student_data)){
        $account_type = '';
        if ($student_data == 'new_student'){
            $modal_dialog = 'modal-dialog modal-xl';
            if (!is_user_logged_in()){
                $title = 'Create an Account';
                $account_type = '<div class="col-md-12"><div class="row" id="account_checkbox">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="parent" name="account_type" value="parent" checked>
                                            <label for="parent" class="custom-control-label">Parent</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="adult_learner" name="account_type" value="adult_learner">
                                            <label for="adult_learner" class="custom-control-label">Adult Learner</label>
                                            </div>
                                        </div>
                                    </div></div></div>';
                $field_title = 'Parent ';
                $phone_field = '<div class="phone-field col-md-4 form-group">
                                    <label for="phone" id="custom_label" class="required"><span id="label_text">'.$field_title.'</span> Phone</label>
                                    <input class="date-picker col-md-12 form-control" id="phone" name="phone" value="" autocomplete="off" required="required">
                                </div>';
            }else{
                $title = '<div>Create a Student Account</div>';
                $title .= '<label class="mb-0 mt-2" style="font-size: 14px;">Note: Each student needs a unique e-mail address.</label>';
                $field_title = 'Student ';
                $student_birth_field = '<div class="student-dob col-md-4 form-group">
                                            <label for="student_dob" id="custom_label" class="required">Student Birth Date</label>
                                            <input class="date-picker col-md-12 form-control" id="student_dob" name="student_dob" placeholder="Date Of Birth" required="required">
                                        </div>';
            }
            $student_modal = '<form role="form" class="student_register_login col-md-12 row mb-0" name="student_register_login" id="student_login" method="post">
                                    '.$account_type.'
                                    <div class="student-fname col-md-4 form-group">
                                        <label for="first_name" id="custom_label" class="required"><span id="label_text">'.$field_title.'</span> First Name</label>
                                        <input type="text" class="col-md-12 form-control" name="student_fname" id="first_name" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-lname col-md-4 form-group">
                                        <label for="last_name" id="custom_label" class="required"><span id="label_text">'.$field_title.'</span> Last Name</label>
                                        <input type="text" class="col-md-12 form-control" name="student_lname" id="last_name" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-email col-md-4 form-group">
                                        <label for="student_email" id="custom_label" class="required"><span id="label_text">'.$field_title.'</span> Email</label>
                                        <input type="email" class="col-md-12 form-control" name="student_email" id="student_email" value="" autocomplete="off" required="required"/>
                                    </div>
                                    '.$phone_field.'
                                    '.$student_birth_field.'
                                    <!--<div class="student-username col-md-4 form-group">
                                        <label for="username" id="custom_label" class="required"><span id="label_text">'.$field_title.'</span> Username </label>
                                        <input type="text" class="col-md-12 form-control" name="student_username" id="username" value="" autocomplete="off" required="required"/>
                                    </div>-->
                                    <div class="student-password col-md-4 form-group">
                                        <label for="password" id="custom_label" class="required"><span id="label_text">'.$field_title.'</span> Password</label>
                                        <input type="password" class="col-md-12 form-control" name="student_password" id="password" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-confirm-password col-md-4 form-group">
                                        <label for="confirm_password" id="custom_label" class="required"><span id="label_text">'.$field_title.'</span> Confirm Password</label>
                                        <input type="password" class="col-md-12 form-control" name="student_confirm_password" id="confirm_password" value="" autocomplete="off" required="required"/>
                                    </div>
                                    
                                   <!--<div class="student-timezone col-md-4 form-group">
                                        <select name="student_selected_timezone" id="student_selected_timezone">
                                          <option disabled><span>US</span></option>                                        
                                            <option value="America/Cuiaba">(UTC-4) East-Indiana</option>
                                            <option value="America/Halifax">(UTC-4) Eastern</option>
                                            <option value="America/New_York">(UTC-5) Central</option>
                                            <option value="America/Indiana/Indianapolis">(UTC-5) Inadiana-Starke</option>
                                            <option value="America/Guatemala">(UTC-6) Mountain</option>
                                            <option value="America/Phoenix">(UTC-7) Arizone</option>
                                            <option value="America/Denver">(UTC-7) Pacific</option>
                                            <option value="America/Los_Angeles">(UTC-8) Alaska</option>
                                            <option value="America/Anchorage">(UTC-9) Aleutian</option>
                                            <option value="Pacific/Honolulu">(UTC-10) Hawaii</option>
                                            <option disabled><span>Africa</span></option>
                                            <option value="Asia/Tehran">(UTC+3) Addis Ababa</option>
                                            <option value="Africa/Nairobi">(UTC+3) Cairo</option>
                                            <option value="Asia/Riyadh">(UTC+3) Djibouti</option>
                                            <option value="Europe/Minsk">(UTC+3) Mogadishu</option>
                                            <option value="Asia/Baghdad">(UTC+3) Nairobi</option>
                                            <option value="Africa/Cairo">(UTC+2) Johannesburg</option>
                                            <option value="Europe/Istanbul">(UTC+2) Khartoum</option>
                                            <option value="Asia/Amman">(UTC+2) Tripoli</option>
                                            <option value="Europe/Paris">(UTC+1) Algiers</option>
                                            <option value="Europe/Berlin">(UTC+1) Casablanca</option>
                                            <option value="Europe/Warsaw">(UTC+1) Douala</option>
                                            <option value="Africa/Lagos">(UTC+1) Lagos</option>
                                            <option value="Africa/Casablanca">(UTC+0) Accra</option>
                                            <option value="Europe/London">(UTC+0) Dakar</option>
                                          <option disabled><span>America</span></option>
                                            <option value="America/Argentina/Buenos_Aires">(UTC-3) Buenos Aires</option>
                                            <option value="America/Cayenne">(UTC-3) Cordoba</option>
                                            <option value="America/Sao_Paulo">(UTC-3) Halifax</option>
                                            <option value="America/Montevideo">(UTC-3) Montevideo</option>
                                            <option value="America/St_Johns">(UTC-3) Santiago</option>
                                        </select>                                    
                                    </div> -->

                                    <input type="hidden" name="action" value="student_register_login" />
                                    <input type="hidden" class="g-value" value="'.SITE_KEY.'">
                                    <input type="hidden" name="recaptcha-response" class="recaptcha-response">
                                    <input type="hidden" name="clogin_id" value="'.$current_user_id.'" />
                                    <input type="hidden" name="type" id="type" value="Parent" />
                                    <div class="modal-footer col-md-12">
                                    <button type="submit" class="btn btn-outline-success submit-button" name="submit"><span class="student-loader spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span><span>Register</span></button>
                                    <button type="button" class="btn btn-outline-secondary student-close" data-dismiss="modal">Close</button>
                                    </div>
                                    <div class="form_message col-md-12 text-center"></div>
                                </form>';
        }
        elseif ($student_data == 'student_login'){
            $modal_dialog = 'modal-dialog modal-md';
            $title = 'Login';
            $student_modal = '<form role="form" class="student_login mb-0" name="student_login" id="student_login" method="post">
                                        <div class="student-username form-group">
                                            <label for="username" class="required">Username</label>
                                            <input type="text" class="form-control" name="student_username" id="username" value="" autocomplete="off" required="required"/>
                                        </div>
                                        <div class="student-password form-group">
                                            <label for="password" class="required">Password</label>
                                            <input type="password" class="form-control" name="student_password" id="password" value="" autocomplete="off" required="required"/>
                                        </div>
                                        <input type="hidden" name="action" value="student_login" />
                                        <input type="hidden" class="g-value" value="'.SITE_KEY.'">
                                        <input type="hidden" name="recaptcha-response" class="recaptcha-response">
                                        <div class="modal-footer col-md-12">
                                            <button type="submit" class="btn btn-outline-success submit-button" name="submit"><span class="student-loader spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span><span>Login</span></button>
                                            <button type="button" class="btn btn-outline-secondary student-close" data-dismiss="modal">Close</button>
                                        </div>
                                        <div class="form_message col-md-12 text-center"></div>
                                    </form>';
        }
        echo json_encode(array('success' => true, 'message' => 'Successfull', 'result' => array('student_title' => $title, 'student_content' => $student_modal, 'student_dialog' => $modal_dialog )));
    }
    else{
        echo json_encode(array('success' => false, 'message' => 'Not Successfull', 'result' => 'Something Went Wrong.'));
    }
    die();
}
add_action( 'wp_ajax_student_modal_show', 'student_modal_show' );
add_action( 'wp_ajax_nopriv_student_modal_show', 'student_modal_show' );

/* Rest Api */
function moodle_woocommerce_order_status_completed( $order_id ) {
    $order = wc_get_order($order_id);
    $parent_user = $order->get_user();
    foreach ($order->get_items() as $item_key => $orderitem){
        $product_id = $orderitem->get_product_id();
        $courseids = get_post_meta($product_id, '_course_ids', true);
        $moodle_courses_enable = get_post_meta( $product_id, '_moodle_courses_enable', true );
        $course_groups = $orderitem->get_meta('product_group_id');
        $course_group_ids = array_filter(explode(',',$course_groups));

        $product_qty = $orderitem->get_quantity();

        if (empty($courseids)) {
            continue;
        }

        if ($moodle_courses_enable == '1'){
            continue;
        }

        if (metadata_exists( 'user', $parent_user->ID, 'parent_value_'.$parent_user->ID )){
            $password = get_user_meta($parent_user->ID, 'parent_value_'.$parent_user->ID, true);
        }elseif (metadata_exists( 'user', $parent_user->ID, 'student_value_'.$parent_user->ID )){
            $password = get_user_meta($parent_user->ID, 'student_value_'.$parent_user->ID, true);
        }

        $body = [
            'wstoken' => MOODLE_ACCESS_TOKEN,
            'wsfunction' => 'local_wpmoodle_create_enrolled_users',
            'moodlewsrestformat' => 'json',
            'courseid' => trim($courseids),
            'groupid' => join(',', $course_group_ids),
            'parent[username]' => $parent_user->user_login,
            'parent[firstname]' => $parent_user->first_name,
            'parent[lastname]' => $parent_user->last_name,
            'parent[email]' => $parent_user->user_email,
            'parent[password]' => base64_decode($password),
        ];

        $child_umeta = $orderitem->get_meta('Student Name');
        if (!empty($child_umeta)) {
            $child_user = get_user_by('login',$child_umeta);
            $body += [
                'child[username]' => $child_user->user_login,
                'child[firstname]' => $child_user->first_name,
                'child[lastname]' => $child_user->last_name,
                'child[email]' => $child_user->user_email,
                'child[password]' => base64_decode(get_user_meta($child_user->ID, 'student_value_'.$child_user->ID, true)),
            ];
        } else {
            $body += [
                'child[username]' => $parent_user->user_login,
                'child[firstname]' => $parent_user->first_name,
                'child[lastname]' => $parent_user->last_name,
                'child[email]' => $parent_user->user_email,
                'child[password]' => base64_decode(get_user_meta($parent_user->ID, 'parent_value_'.$parent_user->ID, true)),
            ];
        }

        $response = wp_remote_post(MOODLE_ACCESS_URL . '/webservice/rest/server.php',compact('body'));

        $moodle_data = json_decode($response['body']);

        // update_user_meta(wp_get_current_user()->ID, 'moodlereponse', var_export($moodle_data, true));

        if (empty($moodle_data->error)) {
            if (is_int($moodle_data->parent->id) && $moodle_data->parent->id > 0) {
                update_user_meta($parent_user->ID, 'moodle_userid', $moodle_data->parent->id);
            }
            if (is_int($moodle_data->child->id) && $moodle_data->child->id > 0) {
                update_user_meta($child_user->ID, 'moodle_userid', $moodle_data->child->id);
            }
        }
        course_update_avalibility($product_id,$course_groups,$product_qty);
    }
}

function moodle_woocommerce_change_status($subscription, $new_status, $old_status) {
    if (!wcs_is_subscription($subscription)) {
        return;
    }
    /**
     * @var $order WC_Order
     */
    $order = $subscription->get_parent();
    $parent_user = $order->get_user();
    if (in_array($new_status, ['active'])) {
        if ($order->get_status() != 'completed') {
            $order->update_status('completed');
        }
        foreach ($order->get_items() as $item_key => $orderitem){
            $product_id = $orderitem->get_product_id();
            $courseids = get_post_meta($product_id, '_course_ids', true);
            $moodle_courses_enable = get_post_meta( $product_id, '_moodle_courses_enable', true );
            if (empty($courseids)) {
                continue;
            }
            if ($moodle_courses_enable == '1'){
                continue;
            }
            $moodleuserids = [get_user_meta($parent_user->ID, 'moodle_userid', true)];
            $child_umeta = $orderitem->get_meta('Student Name');
            if (!empty($child_umeta)) {
                $child_user = get_user_by('login',$child_umeta);
                $moodleuserids[] = get_user_meta($child_user->ID, 'moodle_userid', true);
            }
            $moodleuserids = array_filter($moodleuserids, 'trim');

            if (empty($moodleuserids)) {
                continue;
            }

            $body = [
                'wstoken' => MOODLE_ACCESS_TOKEN,
                'wsfunction' => 'local_wpmoodle_unsuspend_course_user',
                'moodlewsrestformat' => 'json',
                'courseid' => trim($courseids),
                'userid' => join(',', $moodleuserids),
            ];
            $response = wp_remote_post(MOODLE_ACCESS_URL . '/webservice/rest/server.php',compact('body'));
        }
    } else if (in_array($old_status, ['active'])) {
        foreach ($order->get_items() as $item_key => $orderitem){
            $product_id = $orderitem->get_product_id();
            $courseids = get_post_meta($product_id, '_course_ids', true);
            $moodle_courses_enable = get_post_meta( $product_id, '_moodle_courses_enable', true );
            if (empty($courseids)) {
                continue;
            }
            if ($moodle_courses_enable == '1'){
                continue;
            }
            $moodleuserids = [get_user_meta($parent_user->ID, 'moodle_userid', true)];
            $child_umeta = $orderitem->get_meta('Student Name');
            if (!empty($child_umeta)) {
                $child_user = get_user_by('login',$child_umeta);
                $moodleuserids[] = get_user_meta($child_user->ID, 'moodle_userid', true);
            }
            $moodleuserids = array_filter($moodleuserids, 'trim');

            if (empty($moodleuserids)) {
                continue;
            }

            $body = [
                'wstoken' => MOODLE_ACCESS_TOKEN,
                'wsfunction' => 'local_wpmoodle_suspend_course_user',
                'moodlewsrestformat' => 'json',
                'courseid' => trim($courseids),
                'userid' => join(',', $moodleuserids),
            ];
            $response = wp_remote_post(MOODLE_ACCESS_URL . '/webservice/rest/server.php',compact('body'));
            error_log(var_export($response,true).'\n',3,dirname(__FILE__).'/err.log');
        }
    }
}

add_action( 'woocommerce_order_status_completed', 'moodle_woocommerce_order_status_completed', 10, 1 );

add_action( 'woocommerce_subscription_status_updated', 'moodle_woocommerce_change_status', 10, 3);

/* Show SSO Button On Account Page */
function sso_url(){
    global $current_user;
    $current_user_meta = get_user_meta($current_user->ID, 'moodle_userid', true);
    if (empty($current_user_meta)){
        echo json_encode(array('success' => false, 'message' => 'Not Successfull', 'result' => 'Something Went Wrong.'));
    }
    else{
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $body = [
            'wstoken' => MOODLE_ACCESS_TOKEN,
            'wsfunction' => 'local_wpmoodle_get_login_key',
            'moodlewsrestformat' => 'json',
            'username' => $current_user->user_login,
            'ip' => $ip,
        ];
        $response = wp_remote_post(MOODLE_ACCESS_URL . '/webservice/rest/server.php',compact('body'));

        $moodle_data = json_decode($response['body']);
        if (empty($moodle_data->error)) {
            $ssourl = MOODLE_ACCESS_URL . '/local/wpmoodle/autologin.php?userid='.$current_user_meta.'&key='.$moodle_data->key;
            echo json_encode(array('success' => true, 'message' => 'Successfull', 'result' => 'Successfull', 'ssourl' => $ssourl));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Not Successfull', 'result' => 'Something Went Wrong.'));
        }
    }
    die();
}
add_action('wp_ajax_sso_url','sso_url');


/* Prevent Parent User To Checkout Without Selecting Student */
function redirect_checkout_page_not_selected_student() {
    global $current_user;
    $redirect = get_site_url().'/cart';
    $product_child_name = WC()->cart->get_cart_contents();
    if (is_order_received_page() || is_view_order_page()){
        return;
    }
    if( is_checkout() && !is_user_logged_in() && !is_cart()){
        foreach( $product_child_name as $cart_item ){
            if (isset($cart_item['product_group_id'])) {
                wc_add_notice(sprintf('You must be logged in to proceed checkout.'), 'error');
                wp_redirect($redirect);
                exit;
            }
        }
    }
    foreach( $product_child_name as $cart_item ){
        $product_student_id = $cart_item['product_student_id'];
        $moodle_courses_enable = get_post_meta( $cart_item['product_id'], '_moodle_courses_enable', true );
        if ($moodle_courses_enable == '0') {
            if (is_user_logged_in() && empty($product_student_id) && is_checkout() && !array_intersect(array('student'),$current_user->roles)) {
                $redirect = add_query_arg('update', 'cart', $redirect);
                wp_redirect($redirect);
                exit;
            }
        }
    }
    foreach ($product_child_name as $item){
        $moodle_course = get_post_meta( $item['product_id'], 'moodle_course_section_fields', true );
        if(empty($moodle_course)){
            continue;
        }
        $group_id = array_column($moodle_course, 'course_groups');
        if (!empty($group_id) && is_checkout() && empty($item['product_group_id'])){
            $redirect = add_query_arg('update', 'group', $redirect);
            wp_redirect($redirect);
            exit;
        }
    }
    /* course stock qty redirect */
    foreach ($product_child_name as $key => $value){
        $moodle_courses_enable = get_post_meta( $value['product_id'], '_moodle_courses_enable', true );
        $product_quantity = $value['quantity'];
        if ($moodle_courses_enable == '0') {
            $custom_repeater_item = get_post_meta( $value['product_id'], 'moodle_course_section_fields', true );
            $manage_stock = get_post_meta($value['product_id'],'_manage_stock',true);
            if ($manage_stock == 'no'){
                continue;
            }
            foreach ($custom_repeater_item as $item_key => $item_value){
                if (is_null($item_value['total_count']) && $item_value['course_groups'] == $value['product_group_id']  ){
                    if (is_checkout()) {
                        $redirect = add_query_arg('update', 'course-stock', $redirect);
                        wp_redirect($redirect);
                        exit;
                    }
                }
                elseif ($item_value['course_groups'] == $value['product_group_id'] && $product_quantity > (int)$item_value['total_count']){
                    if (is_checkout()){
                        $redirect = add_query_arg('update', 'course-qty', $redirect);
                        wp_redirect($redirect);
                        exit;
                    }
                }
            }
        }
    }
}

add_action('template_redirect', 'redirect_checkout_page_not_selected_student');

/* Prevent Parent User To Checkout Without Selecting Group */
function redirect_checkout_page_not_selected_group() {

    $items = WC()->cart->get_cart_contents();
    $redirecturl = get_site_url().'/cart/';
    $redirect = false;
    foreach( $items as $cart_item ){
        if (empty($cart_item['product_group_id'])) {
            $redirect = true;
        }
    }
    if (!empty($redirect) && is_user_logged_in() && is_checkout()) {
        $redirecturl = add_query_arg('update', 'group', $redirecturl);
        wc_add_notice(sprintf('Please select group from the list'), 'error');
        wp_redirect($redirecturl);
        exit;
    }
}
//add_action('template_redirect', 'redirect_checkout_page_not_selected_group');

/* Add Custom Menu Item In Woocommerce Account Page */
function add_mycourses_menu_items( $menu_links ){
    global $current_user;
    $current_user_meta = get_user_meta($current_user->ID, 'moodle_userid', true);
    if (is_user_logged_in()) {
        $new_item = array('my-courses' => 'My Courses');
        $menu_links = array_slice($menu_links, 0, 2, true)
            + $new_item
            + array_slice($menu_links, 1, NULL, true);
    }
    if (array_intersect(array('administrator','instructor','adult_learner','parent_wp'),$current_user->roles)){
        $new_item = array('add-edit-student' => 'Add/Edit Student');
        $menu_links = array_slice($menu_links, 0, 1, true)
            + $new_item
            + array_slice($menu_links, 1, NULL, true);
    }
    return $menu_links;
}
add_filter ( 'woocommerce_account_menu_items', 'add_mycourses_menu_items' );

/* Add Custom Menu Item Url In Woocommerce Account Page */
function add_my_courses_link( $url, $endpoint, $value, $permalink ){

    if( 'my-courses' === $endpoint ) {
        $url = get_site_url() .'/my-courses/';
    }
    if( 'add-edit-student' === $endpoint ) {
        $url = get_site_url() .'/student-list/';
    }
    return $url;
}
add_filter( 'woocommerce_get_endpoint_url', 'add_my_courses_link', 10, 4 );

/* Add Moodle Course Metabox for Woocommerce Product */
function moodle_course_repeater_box() {
    add_meta_box( 'moodel-course-section-data', 'Moodle Course Section', 'moodle_course_section_callback', 'product', 'normal', 'default');
}
add_action( 'admin_init', 'moodle_course_repeater_box', 2 );

/* Moodle Course Section Callback */
function moodle_course_section_callback( $post ) {
    date_default_timezone_set( 'America/New_York' );
    $options = moodleservice('get_courses');

    $selectedcourses = explode(',',get_post_meta($post->ID,'_course_ids',true));
    $moodlecourseid = get_post_meta($post->ID,'_moodle_course_id',true);
    $custom_repeater_item = get_post_meta( $post->ID, 'moodle_course_section_fields', true );

    if($moodlecourseid){
        $fielstatus = "moodle_course";
    }else{
        $fielstatus = "";
    }
    // echo "<pre>-------";
    // print_r($custom_repeater_item);
    // echo "</pre>";
    //die;
    $moodle_courses_enable = get_post_meta( $post->ID, '_moodle_courses_enable', true );
    $moodle_one_time_subscription = get_post_meta( $post->ID, '_moodle_one_time_subscription', true );
    wp_nonce_field( 'moodle_course_section', 'formType' );

    $user_roles = get_users(array('role__in' => array('instructor','administrator')));
    $teacher_role_option = [];
    $teacher_role_option[] = '<option value="">'.esc_html('Choose Teacher').'</option>';
    foreach ($user_roles as $user_role) {
        $teacher_role_option[] = '<option value="' . esc_attr($user_role->ID) . '">' .
            esc_html($user_role->first_name." ".$user_role->last_name ) .
            '</option>';
    }

    ?>
    <style>
        .weeks-off[readonly] {
            background-color: white;
        }
        .moodle_course{
            display:none !important;
        }
        input[type="checkbox"][readonly] {
          pointer-events: none;
        }
        select.flatpickr-monthDropdown-months:focus {
            box-shadow: none;
        }
        span.flatpickr-day {
            background: #e6e6e6;
            border-color: #e6e6e6;
            border-radius: 0;
            margin: 3px 0px 5px 1px;
        }
        .flatpickr-months {
            background: #e6e6e6;
            height: 40px;
        }
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            height: 0;
            width: 0;
            visibility: hidden;
        }
        .numInputWrapper:hover {
            background: none;
        }
    </style>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            const fixIndexes = () => {
                $('[name^=course_classes],[name^=course_weeks_off]').each(function() {
                    $(this).attr('name', $(this).attr('name').replace(/\d+/g, $(this).parents('table').index()));
                })
            }
            let coursedata = [];
            $('.moodlegroupselect:first option').each((_, opt) => coursedata.push({id: opt.value, text: opt.innerText}));
            $(document).on('click', '.wc-remove-item', function() {
                $(this).parents('.wc-sub-row').remove();
                fixIndexes();
            });
            $(document).on('click', '.wc-add-item', function() {
                const noitems = $('#wc-sub-row').length;
                let teacher_list = '<?=implode('', $teacher_role_option)?>';
                let clone = $($('#moodle_course_html').text().replace(/{no}/g, noitems));
                clone.find('#course_teacher').html(teacher_list);
                if (noitems === 0){
                    let table_elem = document.createElement('table');
                    $(table_elem).addClass('wc-sub-row').html(clone).appendTo($(".wc-item-table"));
                    $(table_elem).find('.moodlegroupselect').select2({placeholder: "Select course group", data: coursedata,})
                }else {
                    clone.insertAfter('.wc-sub-row:last');
                }
                clone.find('.course-start,.moodle-date').datepicker({
                    dateFormat: 'MM dd, yy',
                    changeMonth: true,
                    changeYear: true,
                });

                clone.find('.moodle-time').timepicker({
                    timeFormat: 'h:mm p',
                    interval: 1,
                    dropdown: true,
                    scrollbar: true
                });
                clone.find(".weeks-off").flatpickr({
                    // locale: {"firstDayOfWeek": 1},
                    mode: "multiple",
                    altInput: true,
                    altFormat: "F j, Y",
                    dateFormat: "M-d-Y",
                    changeMonth: true,
                    changeYear: true,
                    monthSelectorType: "dropdown",
                    allowInput: false
                });
                clone.find('.moodlegroupselect').select2({placeholder: "Select course group", data: coursedata,})
                fixIndexes();
            });
            const courseselect = $('#moodle_courses').select2();
            const coursegroups = $('.moodlegroupselect').select2({
                placeholder: "Select course group",
            });
            $(document).one('select2:open','.moodlegroupselect', function (e) {
                let length = $(this).children('option').length;
                if (length === 1 && courseselect.val() !== '') {
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo admin_url('admin-ajax.php')?>',
                        dataType: 'json',
                        data: {
                            action: 'get_moodle_groups',
                            courseid: courseselect.val()
                        }
                    }).done(response => {
                        const thisCourse = response.find(course => course.id == courseselect.val());
                        coursedata = [{id: 0, text: "No Group"}];
                        if (thisCourse) {
                            $.each(thisCourse.groups, function (i, group) {
                                group.text = group.name;
                                coursedata.push(group);
                            });
                        }
                        $('.moodlegroupselect').empty().select2({data: coursedata});
                        $(this).select2('open');
                    })
                }
            });
            courseselect.on('select2:select', () => {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    dataType: 'json',
                    data: {
                        action: 'get_moodle_groups',
                        courseid: courseselect.val()
                    }
                }).done(response => {
                    const thisCourse = response.find(course => course.id == courseselect.val());
                    coursedata = [{id: 0, text: "No Group"}];
                    if (thisCourse) {
                        $.each(thisCourse.groups, function(i ,group) {
                            group.text = group.name;
                            coursedata.push(group);
                        });
                    }
                    $('.moodlegroupselect').empty().select2({data: coursedata});
                })
            });

            $('.course-start,.moodle-date').each(function(){
                $(this).datepicker({
                    dateFormat: 'MM dd, yy',
                    changeMonth: true,
                    changeYear: true,
                });
            });

            $('.moodle-time').timepicker({
                timeFormat: 'h:mm p',
                interval: 1,
                dropdown: true,
                scrollbar: true
            });
            $(".weeks-off").flatpickr({
                // locale: {"firstDayOfWeek": 1},
                mode: "multiple",
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "M-d-Y",
                changeMonth: true,
                changeYear: true,
                monthSelectorType: "dropdown",
                allowInput: false
            });
        });
    </script>
    <script id="moodle_course_html" type="text/template">
        <table id="wc-sub-row" class="wc-sub-row">
            <tbody>
            <tr>
                <td><label for="course_groups">Course Group:</label></td>
                <td><select id="course_groups" name="course_groups[]" class="first moodlegroupselect"
                            style="width: 220px">
                        <option value="0">No Group</option>
                    </select></td>
            </tr>
            <tr>
                <td><label for="course_start">Starts:</label></td>
                <td><input class="col-md-12 form-control course-start" name="course_start[]" type="text" value="" autocomplete="off">
                </td>
            </tr>
            <tr class="courselength">
                <td><label>Course Length:</label></td>
                <td>
                    <span class="course-date">
                        <label>From:</label>
                        <input type="text" class="course-length form-control moodle-date" name="course_length_from_date[]" value="" autocomplete="off">
                        <label>To:</label>
                        <input type="text" class="course-length form-control moodle-date" name="course_length_to_date[]" value="" autocomplete="off">
                    </span>
                </td>
            </tr>
            <tr class="calendarweekoff">
                <td>
                    <label>Calendar weeks Off:</label>
                </td>
                <td>
                    <input type="text" class="weeks-off" name="course_weeks_off[{no}][]" value="" autocomplete="off" />
                </td>
            </tr>
            <tr>
                <td><label for="course_classes">Live Classes:</label></td>
                <td class="course-class-ckb">
                    <span class="course-days">
                        <label><input type="checkbox" name="course_classes[{no}][]" value="monday">Mondays</label>
                        <label><input type="checkbox" name="course_classes[{no}][]" value="tuesday">Tuesdays</label>
                        <label><input type="checkbox" name="course_classes[{no}][]" value="wednesday">Wednesdays</label>
                        <label><input type="checkbox" name="course_classes[{no}][]" value="thursday">Thursdays</label>
                        <label><input type="checkbox" name="course_classes[{no}][]" value="friday">Fridays</label>
                        <label><input type="checkbox" name="course_classes[{no}][]" value="saturday">Saturdays</label>
                        <label><input type="checkbox" name="course_classes[{no}][]" value="sunday">Sundays</label>
                    </span>
                    <span class="course-time">
                        <label for="from_time">From:</label>
                        <input type="text" class="form-control moodle-time" id="from_time" name="course_from_time[]" value="" autocomplete="off">
                        <label for="to_time">To:</label>
                        <input type="text" class="form-control moodle-time" id="to_time" name="course_to_time[]" value="" autocomplete="off">
                    </span>
                </td>
            </tr>
            <tr>
                <td><label for="course_teacher">Teacher:</label></td>
                <td><select name="course_teacher[]" id="course_teacher" class="first"></select></td>
            </tr>
            <tr class="subterms">
                <td>
                    <label>Subscription Terms:</label>
                </td>
                <td>
                    <span class="course-subscription-time">
                        <select name="course_subscription_time[]" id="course_subscription_times">
                            <option value="">- Select -</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                        </select>
                        <label for="course_subscription_times">Times</label>
                        <select name="course_subscription_week[]" id="course_subscription_weeks">
                            <option value="">- Select -</option>
                            <option value="every">Every</option>
                            <option value="every_other">Every Other</option>
                        </select>
                        <label for="course_subscription_weeks">Week</label>
                    </span>
                </td>
            </tr>
            <tr class="stock-qty">
                <td>
                    <label>Total Seats Availability:</label>
                </td>
                <td>
                    <input type="number" class="form-control" name="course_stock_qty[]" value="" min="0" />
                </td>
            </tr>
            <tr>
                <td>
                    <button class="wc-remove-item button" type="button">Remove</button>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr class="moodle-section">
                </td>
            </tr>
            </tbody>
        </table>

    </script>
    <p class="form-field form-field-wide">
        <input type="checkbox" name="moodle_courses_disable" id="moodle_courses_disable" value="1" <?php checked($moodle_courses_enable, '1');?> disabled/>
        <label for="moodle_courses_disable"><?php esc_html_e( 'Disable Moodle Course For This Product', 'woocommerce' ); ?></label>
    </p>
    <p>
        <label name="form-field form-field-wide"></label>
        <input type="checkbox" name="one_time_subscription" id="one_time_subscription" value="1" <?php checked($moodle_one_time_subscription, '1');?> disabled/>
        <label for="one_time_subscription"><?php esc_html_e( 'One Time Course For This Product', 'woocommerce' ); ?></label>
    </p>
    <p class="form-field form-field-wide course-select">
        <label name="moodle_courses"><?php esc_html_e( 'Moodle Course', 'woocommerce' ); ?></label>
        <select name="moodle_courses" id="moodle_courses" class="first" style="width: 220px" disabled>
            <?php
            echo '<option selected="selected" value="">No course</option>';
            foreach ( $options as $option ) {
                echo '<option value="' . esc_attr( $option->id ) . '" ' . (in_array( $option->id, $selectedcourses )?'selected="selected"':'') . '>' .
                    esc_html( $option->fullname .($option->idnumber?"({$option->idnumber})":"") ) .
                    '</option>';
            }
            ?>
        </select>
    </p>
    <table class="wc-item-table" width="100%">
        <tbody>
        <tr><td>
        <?php
        if( $custom_repeater_item ){
            foreach( $custom_repeater_item as $item_key => $item_value ){
                if(sizeof($item_value['directfrommoodle']) > 0){
              		$post_data   = get_post( $post->ID );
	                $coursestarttime = strtotime($post_data->post_date);
	              	$producttime = date("F d, Y",$coursestarttime);
              		 $item_value['course_start'] = $producttime;
              		
              	}
                if(isset($item_value['meeting_visibility']) && $item_value['meeting_visibility']==1){
                    continue;
                }
               // if() 
                $check_box_value = (isset($item_value['course_classes'])) ? $item_value['course_classes'] : '';
                $course_subscription_term_time = (isset($item_value['course_subscription_time'])) ? $item_value['course_subscription_time'] : '';
                $course_subscription_term_week = (isset($item_value['course_subscription_week'])) ? $item_value['course_subscription_week'] : '';
                $course_stock_qty = (isset($item_value['course_stock_qty'])) ? $item_value['course_stock_qty'] : '0';
                $purchase_count = (isset($item_value['purchase_count'])) ? $item_value['purchase_count'] : '0';
                $total_count = (isset($item_value['total_count'])) ? $item_value['total_count'] : '0';
                // echo "<pre>";
                // echo "helooo-------";
                // print_r($item_value);
                // echo "</pre>";
                ?>
                <table id="wc-sub-row" class="wc-sub-row">
                    <tbody>
                    <tr>
                        <td>
                            <label for="course_groups">Course Group:</label>
                        </td>
                        <td>
                            <select id="course_groups" name="course_groups[]" class="first moodlegroupselect" style="width: 220px" disabled>
                                <option value="0">No Group</option>
                                <?php foreach ($options as $option ): if (in_array($option->id, $selectedcourses)):
                                    foreach ($option->groups as $group): ?>
                                        <option value="<?php echo $group->id ?>" <?php echo (($group->id == $item_value['course_groups'] ?? null) ? 'selected': ''); ?>><?php echo $group->name ?></option>
                                    <?php endforeach; endif;
                                endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="course-start">
                        <td>
                            <label for="course_start">Starts:</label>
                        </td>
                        <td><input class="col-md-12 form-control course-start" name="course_start[]" type="text" value="<?php echo (isset($item_value['course_start'])) ? $item_value['course_start'] : ''; ?>" autocomplete="off" disabled></td>
                    </tr>
            <?php if($item_value['allmetings']){ ?>
                                         
                    <tr class="course-start">
                        <td>
                            <label for="course_start">Meeting Schedule Time :</label>
                        </td>
                        <td>
                            <?php 
                            foreach ($item_value['allmetings'] as $allmetings) {
                            $totime  = $allmetings['starttime'] + $allmetings['duration'];
                            // echo  date('m/d/Y H:i:s', $allmetings['starttime']) . 'To ' . date('H:i' , $totime);
                            echo  '<input class="col-md-12 form-control course-start" name="course_start[]" type="text" value="'.date('F j, Y H:i', $allmetings['starttime']).'" autocomplete="off" disabled>
                                <input class="col-md-12 form-control" type="text" value="'.date('H:i' , $totime).'" autocomplete="off" disabled><br>

                            ';
                            }
                            ?>
                        </td>
                    </tr>
            <?php } ?>    
                    <tr <?php echo "class='courselength ".$fielstatus."'"?>>
                        <td>
                            <label>Course Length:</label>
                        </td>
                        <td>
                            <span class="course-date">
                                <label>From:</label>
                                <input type="text" class="course-length form-control moodle-date" name="course_length_from_date[]" value="<?php echo (isset($item_value['course_length_from_date'])) ? $item_value['course_length_from_date'] : ''; ?>" autocomplete="off" disabled>
                                <label>To:</label>
                                <input type="text" class="course-length form-control moodle-date" name="course_length_to_date[]" value="<?php echo (isset($item_value['course_length_to_date'])) ? $item_value['course_length_to_date'] : ''; ?>" autocomplete="off" disabled>
                            </span>
                        </td>
                    </tr>
                    <tr <?php echo "class='calendarweekoff ".$fielstatus."'";?>>
                        <td>
                            <label>Calendar weeks Off:</label>
                        </td>
                        <td><input type="text" class="weeks-off" name="course_weeks_off[<?php echo $item_key ?>][]" value="<?php echo $item_value['course_weeks_off'][0];?>" autocomplete="off" disabled/></td>
                    </tr>
                    <tr <?php echo "class='".$fielstatus."'";?>>
                        <td>
                            <label for="course_classes">Live Classes:</label>
                        </td>
                        <td class="course-class-ckb">
                            <span class="course-days">
                                <label><input type="checkbox" name="course_classes[<?php echo $item_key ?>][]" value="monday" <?php if (in_array('monday', $check_box_value)): echo "checked"; endif;?> disabled>Mondays</label>
                                <label><input type="checkbox" name="course_classes[<?php echo $item_key ?>][]" value="tuesday" <?php if (in_array('tuesday', $check_box_value)): echo "checked"; endif;?> disabled>Tuesdays</label>
                                <label><input type="checkbox" name="course_classes[<?php echo $item_key ?>][]" value="wednesday" <?php if (in_array('wednesday', $check_box_value)): echo "checked"; endif;?> disabled>Wednesdays</label>
                                <label><input type="checkbox" name="course_classes[<?php echo $item_key ?>][]" value="thursday" <?php if (in_array('thursday', $check_box_value)): echo "checked"; endif;?> disabled>Thursdays</label>
                                <label><input type="checkbox" name="course_classes[<?php echo $item_key ?>][]" value="friday" <?php if (in_array('friday', $check_box_value)): echo "checked"; endif;?> disabled>Fridays</label>
                                <label><input type="checkbox" name="course_classes[<?php echo $item_key ?>][]" value="saturday" <?php if (in_array('saturday', $check_box_value)): echo "checked"; endif;?> disabled>Saturdays</label>
                                <label><input type="checkbox" name="course_classes[<?php echo $item_key ?>][]" value="sunday" <?php if (in_array('sunday', $check_box_value)): echo "checked"; endif;?> disabled>Sundays</label>
                            </span>
                            <span class="course-time">
                            <label for="from_time">From:</label>
                            <input type="text" class="form-control moodle-time" id="from_time" name="course_from_time[]" value="<?php echo (isset($item_value['course_from_time'])) ? $item_value['course_from_time'] : ''; ?>" autocomplete="off" disabled>
                            <label for="to_time">To:</label>
                            <input type="text" class="form-control moodle-time" id="to_time" name="course_to_time[]" value="<?php echo (isset($item_value['course_to_time'])) ? $item_value['course_to_time'] : ''; ?>" autocomplete="off" disabled>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="course_teacher">Teacher:</label>
                        </td>
                        <td>
                            <select name="course_teacher[]" id="course_teacher" class="first" disabled>
                                <?php
                                $user_roles = get_users(array('role__in' => array('instructor','administrator')));
                                $selected = (isset($item_value['course_teacher'])) ? $item_value['course_teacher'] : '';
                                echo '<option value="">'.esc_html('Choose Teacher').'</option>';
                                foreach ($user_roles as $user_role) {
                                    echo '<option value="' . esc_attr($user_role->ID) . '" ' . (-1 < array_search($user_role->ID, (array)$selected) ? 'selected="selected"' : '') . '>' .
                                        esc_html($user_role->first_name." ".$user_role->last_name ) .
                                        '</option>';
                                }
                                ?>
                            </select>
                    </tr>
                    <tr <?php echo "class='subterms ".$fielstatus."'";?>>
                        <td>
                            <label>Subscription Terms:</label>
                        </td>
                        <td>
                            <span class="course-subscription-time">
                                <select name="course_subscription_time[]" id="course_subscription_times" disabled>
                                    <option value="">- Select -</option>
                                    <option value="1" <?php selected($course_subscription_term_time, '1');?>>1</option>
                                    <option value="2" <?php selected($course_subscription_term_time, '2');?>>2</option>
                                    <option value="3" <?php selected($course_subscription_term_time, '3');?>>3</option>
                                    <option value="4" <?php selected($course_subscription_term_time, '4');?>>4</option>
                                    <option value="5" <?php selected($course_subscription_term_time, '5');?>>5</option>
                                    <option value="6" <?php selected($course_subscription_term_time, '6');?>>6</option>
                                    <option value="7" <?php selected($course_subscription_term_time, '7');?>>7</option>
                                </select>
                                <label for="course_subscription_times">Times</label>
                                <select name="course_subscription_week[]" id="course_subscription_weeks" disabled> 
                                    <option value="">- Select -</option>
                                    <option value="every" <?php selected($course_subscription_term_week, 'every');?>>Every</option>
                                    <option value="every_other" <?php selected($course_subscription_term_week, 'every_other');?>>Every Other</option>
                                </select>
                                <label for="course_subscription_weeks">Week</label>
                            </span>
                        </td>
                    </tr>
                    <tr class="stock-qty">
                        <td>
                            <label>Total Seats Availability:</label>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="course_stock_qty[]" value="<?php echo $course_stock_qty; ?>" min="0" / disabled>
                            <?php if (!is_null($item_value['purchase_count'])): ?>
                            <span class="purchase-count">Purchased Seats: <span class="count"><?php echo $purchase_count; ?></span></span>
                            <?php endif; ?>
                        </td>
                        <input type="hidden" class="form-control" name="total_count[]" value="<?php echo $total_count; ?>" min="0"/ disabled>
                    </tr>
                    <tr>
                        <td>
                            <!-- <button class="wc-remove-item button" type="button">Remove</button> -->
                        </td>
                    </tr>
                    </tbody>
                    <tr>
                        <td colspan="2">
                            <hr class="moodle-section">
                        </td>
                    </tr>
                </table>
                <?php
            }
        }?>
        </td></tr>
        </tbody>
        <tfoot>
        <tr>
            <!-- <td colspan="4"><button class="wc-add-item button" type="button">Add another</button></td> -->
        </tr>
        </tfoot>
    </table>
    <?php
}

add_action( 'save_post', 'cxc_single_repeatable_meta_box_save' );
function cxc_single_repeatable_meta_box_save( $post_id ) {

    if ( !isset( $_POST['formType'] ) && !wp_verify_nonce( $_POST['formType'], 'moodle_course_section' ) ){
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
        return;
    }

    if ( !current_user_can( 'edit_post', $post_id ) ){
        return;
    }

    if (isset($_POST['moodle_courses_disable'])) {
        update_post_meta($post_id, '_moodle_courses_enable', $_POST['moodle_courses_disable']);
    }else{
        update_post_meta($post_id, '_moodle_courses_enable', '0');
    }

    if (isset($_POST['one_time_subscription'])) {
        update_post_meta($post_id, '_moodle_one_time_subscription', $_POST['one_time_subscription']);
    }else{
        update_post_meta($post_id, '_moodle_one_time_subscription', '0');
    }

    if (isset($_POST['moodle_courses'])) {
        update_post_meta($post_id, '_course_ids', $_POST['moodle_courses']);
    }

    $old = get_post_meta( $post_id, 'moodle_course_section_fields', true );
    $new = array();
    $names = $_POST['course_start'];
    $course_length_from = $_POST['course_length_from_date'];
    $course_length_to = $_POST['course_length_to_date'];
    $course_live_class = $_POST['course_classes'];
    $course_from_time = $_POST['course_from_time'];
    $course_to_time = $_POST['course_to_time'];
    $course_teacher = $_POST['course_teacher'];
    $course_groups = $_POST['course_groups'];

    $course_weeks_off = $_POST['course_weeks_off'];

    $course_subscription_terms_time = $_POST['course_subscription_time'];
    $course_subscription_terms_week = $_POST['course_subscription_week'];

    $course_stock_qty = $_POST['course_stock_qty'];
    $total_count = $_POST['total_count'];

    $count = 0;
    if(is_array($names)){
        $count = count($names);
    }


    for ($i = 0; $i < $count; $i++) {
        if ($names[$i] != '') :
            $new[$i]['course_start'] = stripslashes(strip_tags($names[$i]));
        endif;
        if ($course_length_from[$i] != '') :
            $new[$i]['course_length_from_date'] = stripslashes(strip_tags($course_length_from[$i]));
        endif;
        if ($course_length_to[$i] != '') :
            $new[$i]['course_length_to_date'] = stripslashes(strip_tags($course_length_to[$i]));
        endif;
        if (!empty($course_live_class[$i])):
            $new[$i]['course_classes'] = $course_live_class[$i];
        else:
            $new[$i]['course_classes'] = [];
        endif;
        if ($course_from_time[$i] != ''):
            $new[$i]['course_from_time'] = stripslashes(strip_tags($course_from_time[$i]));
        endif;
        if ($course_to_time[$i] != ''):
            $new[$i]['course_to_time'] = stripslashes(strip_tags($course_to_time[$i]));
        endif;
        if ($course_teacher[$i] != ''):
            $new[$i]['course_teacher'] = stripslashes(strip_tags($course_teacher[$i]));
        endif;
        if ($course_groups[$i] != ''):
            $new[$i]['course_groups'] = stripslashes(strip_tags($course_groups[$i]));
        endif;

        if (!empty($course_weeks_off[$i])):
            $new[$i]['course_weeks_off'] = $course_weeks_off[$i];
        endif;

        if (!empty($course_subscription_terms_time[$i])):
            $new[$i]['course_subscription_time'] = $course_subscription_terms_time[$i];
        endif;

        if (!empty($course_subscription_terms_week[$i])):
            $new[$i]['course_subscription_week'] = $course_subscription_terms_week[$i];
        endif;

        if (!empty($course_stock_qty[$i])):
            $new[$i]['course_stock_qty'] = $course_stock_qty[$i];
            $new[$i]['purchase_count'] = '';
        endif;

        if ($total_count[$i] == '0'){
            $value = '';
        }else{
            $value = $total_count[$i];
        }
        $new[$i]['total_count'] = $value;
    }

    $array = array_column(!is_array($new) ? []: $new, 'course_classes');

    global $wpdb;
    $group_id = [];
    $stock_qty = array_column(!is_array($new) ? []: $new, 'course_groups');
    foreach ($stock_qty as $key => $val){
        $quantity = [];
        $item_data = $wpdb->get_results("SELECT *
        FROM {$wpdb->prefix}woocommerce_order_itemmeta as woim,
             {$wpdb->prefix}woocommerce_order_itemmeta as woim2,
             {$wpdb->prefix}woocommerce_order_items as woi,
             {$wpdb->prefix}posts as p
        WHERE woi.order_item_id = woim.order_item_id
        AND woi.order_id = p.ID
        AND woim2.order_item_id = woi.order_item_id
        AND p.post_status IN ( 'wc-completed' )
        AND woim.meta_key IN ( '_product_id', '_variation_id' )
        AND woim.meta_value LIKE '$post_id'
        AND woim2.meta_key IN ( 'product_group_id' )
        AND woim2.meta_value LIKE '$val'");
        foreach ($item_data as $data){
            $order = wc_get_order($data->order_id);
            foreach( $order->get_items() as $k => $item ){
                $quantity[] = $item->get_quantity();
                $group_id[] = $item->get_meta('product_group_id');
                /*if ($item->get_meta('product_group_id') == $val){
                    $new[$key]['purchase_count'] = $meta;
                }*/
            }
        }
        $quantity_total = sum(array_values($quantity),sizeof($quantity));
        $stock[] = $new[$key]['course_stock_qty'];
        if (in_array($new[$key]['course_groups'],$group_id)){
            $new[$key]['purchase_count'] = $quantity_total;
            $total_count = $new[$key]['course_stock_qty'] - $new[$key]['purchase_count'];
            if ($total_count >=0) {
                $stock_count = $total_count;
            }else{
                $stock_count = 0;
            }
            $new[$key]['total_count'] = $stock_count;
            if ($new[$key]['purchase_count'] >= 0){
                $purchased[] = $new[$key]['purchase_count'];
            }
        }
        else{
            $new[$key]['purchase_count'] = 0;
            $new[$key]['total_count'] = $new[$key]['course_stock_qty'];
            $purchased[] += $new[$key]['purchase_count'];
        }
    }
    if(is_array($stock)){
        $course_stock_total = sum(array_values($stock),sizeof($stock));
    } 
    if(is_array($purchased)){
            $purchase_total = sum(array_values($purchased),sizeof($purchased));
    } 

    //$course_stock_total = sum(array_values($stock),sizeof($stock));
    $total_quantity = $course_stock_total - $purchase_total;
    if ($total_quantity >= 0){
        $update_quantity = $total_quantity;
    }else{
        $update_quantity = 0;
    }
    /*Sushil commented this*/
    /*if (!empty($new) && $new != $old && !empty($array)) {
        update_post_meta($post_id, 'moodle_course_section_fields', $new);
    }elseif(empty($array)){
        update_post_meta($post_id, 'moodle_course_section_fields', '');
    }*/

    if (is_array($course_stock_qty) && array_sum($course_stock_qty) != 0){
        update_post_meta($post_id,'_manage_stock','yes');
    }else{
        update_post_meta($post_id,'_manage_stock','no');
    }
    course_avalibility_update_stock($post_id,$update_quantity);
}

/* Show Moodle Course Section in Frontend product page */
/* For Without Elementor Plugin */
function moodle_course_section(){
    global $post, $product, $wpdb;
    date_default_timezone_set( 'America/New_York' );
    $number_format = new NumberFormatter('en_US', NumberFormatter::ORDINAL);
    $subscription_interval = get_post_meta($post->ID,'_subscription_period_interval',true);
    $subscription_period = get_post_meta($post->ID,'_subscription_period',true);
    $product_stock_status = get_post_meta($post->ID,'_stock_status',true);
    $product_syllabus = get_post_meta($post->ID,'_moodle_courses_syllabus',true);

    $custom_repeater_item = get_post_meta( $post->ID, 'moodle_course_section_fields', true );
    // echo "<pre>";
    // print_r($product_syllabus);
    // print_r($custom_repeater_item);
    // echo "</pre>";
   //die;

        $current_productid = $product->get_id();
        $product_cats_ids = wc_get_product_term_ids( $current_productid, 'product_cat' );
        foreach( $product_cats_ids as $cat_id ) {
            $term = get_term_by( 'id', $cat_id, 'product_cat' );
            $product_catname = $term->name;
        }
        if(strpos(strtolower($product_catname),"one")){
            $displaytype = 0;
        }elseif(strpos(strtolower($product_catname),"subscription")){
            $displaytype = 2;
        }else{
            $displaytype = 1;
        }

    $moodle_courses_enable = get_post_meta( $post->ID, '_moodle_courses_enable', true );
    $moodle_one_time_subscription = get_post_meta( $post->ID, '_moodle_one_time_subscription', true );
    $group_id = array_column(!is_array($custom_repeater_item) ? []: $custom_repeater_item, 'course_groups');
    $directfrommoodle = array_column(!is_array($custom_repeater_item) ? []: $custom_repeater_item, 'directfrommoodle');
    
    $manage_stock = get_post_meta($post->ID,'_manage_stock',true);

    if (count(array_values(array_unique($group_id))) == 1){
        $val = in_array('0',$group_id);
    }else{
        $val = '1';
    }
    if ($subscription_interval > '1'){
        $subscription = sprintf('Every %s %s', $number_format->format($subscription_interval), ucwords(implode('', array_map(function($i){return $i;}, (array)$subscription_period))));
    }else{
        $subscription = sprintf('Every %s', ucwords($subscription_period));
    }
    if($moodle_courses_enable == '0'):
        if( !empty($custom_repeater_item) && count(array_values(array_unique($group_id))) != $val){
            $alphabet_val = '';
            $section_data = array();
            $usertimezone = getRemoteUserTimeZone();
               //$usertimezone = date_default_timezone_set( 'America/Los_Angeles' );
            //echo date_default_timezone_get();
           // echo "Today is " . date("Y/m/d : H:i:sa") . "<br>";

          /*  echo '<section class="moodle-course-product">
                    <h2>Available Sections:</h2>
                    <h2 class="timezone_display"><img style="max-width: 20px !important;" src="'.get_theme_root_uri().'/buddyboss-theme-child/framework/assets/images/14.png'.'" alt="" class="timezone-img course-img">Timezone: '.$usertimezone.'</h2>';*/

            echo '<h2 class="timezone_display"><img style="max-width: 20px !important;" src="'.get_theme_root_uri().'/buddyboss-theme-child/framework/assets/images/14.png'.'" alt="" class="timezone-img course-img">Timezone:
            <select name="user_select_timezone" id="user_select_timezone">
              <option value="'.$usertimezone.'" selected>'.$usertimezone.'</option>
              <option value="America/Cuiaba">(UTC-4) East-Indiana</option>
              <option value="America/Halifax">(UTC-4) Eastern</option>
              <option value="America/New_York">(UTC-5) Central</option>
              <option value="America/Indiana/Indianapolis">(UTC-5) Inadiana-Starke</option>
              <option value="America/Guatemala">(UTC-6) Mountain</option>
              <option value="America/Phoenix">(UTC-7) Arizone</option>
              <option value="America/Denver">(UTC-7) Pacific</option>
              <option value="America/Los_Angeles">(UTC-8) Alaska</option>
              <option value="America/Anchorage">(UTC-9) Aleutian</option>
              <option value="Pacific/Honolulu">(UTC-10) Hawaii</option>
              <option value="Etc/GMT+11">(UTC-11) Samoa</option>
            </select></h2>
        <section class="moodle-course-product">
        <h2>Available Sections :</h2>';
                
        foreach( $custom_repeater_item as $item_key => $item_value ) {
            $coursestart_date = (isset($item_value['course_start'])) ? $item_value['course_start'] : '';
            $teacher_name = get_user_by('id', (isset($item_value['course_teacher'])) ? $item_value['course_teacher'] : '');
            $moodlegroupid = isset($item_value['course_groups']) ? $item_value['course_groups'] : '';
            if ($moodlegroupid == '0'){
                continue;
            }
            $studentsenrolled = null;
            if (!empty($moodlegroupid)) {
                $studentsenrolled = $wpdb->get_var("SELECT COUNT(DISTINCT p.ID)
                FROM {$wpdb->prefix}woocommerce_order_itemmeta as woim,
                     {$wpdb->prefix}woocommerce_order_itemmeta as woim2,
                     {$wpdb->prefix}woocommerce_order_items as woi,
                     {$wpdb->prefix}posts as p
                WHERE woi.order_item_id = woim.order_item_id
                AND woi.order_id = p.ID
                AND woim2.order_item_id = woi.order_item_id
                AND p.post_status IN ( 'wc-completed' )
                AND woim.meta_key IN ( '_product_id', '_variation_id' )
                AND woim.meta_value LIKE '$post->ID'
                AND woim2.meta_key IN ( 'product_group_id' )
                AND woim2.meta_value LIKE '$moodlegroupid'");
            }
            if (empty($studentsenrolled)) {
                $studentsenrolled = 0;
            }
            if (!empty($studentsenrolled)) {
                if ($studentsenrolled > 1) {
                    $student = 'Students ';
                } else {
                    $student = 'Student ';
                }
                
                $studentsenrolled_result = '<li class="course-students course">
                                                    <strong><i>' . $studentsenrolled . ' ' . $student . ' Enrolled</i></strong>
                                                </li>';
                //------------Remaining available seat--------------
                $custom_repeater_item = get_post_meta( $product->get_id(), 'moodle_course_section_fields', true ); 
                $totalavailableseat = $custom_repeater_item[0]['total_count'];
                $remain_available_seat = $totalavailableseat - $studentsenrolled ;
                if($remain_available_seat != 0){
                    $available_seat = '<li class="course-students course">
                                            <strong><i>' . $remain_available_seat . ' seats available</i></strong>
                                        </li>';
                }

            }else{
                $studentsenrolled_result = '';
                $available_seat = '';
            }
            $total_count = (isset($item_value['total_count'])) ? $item_value['total_count'] : '';
            if (empty($total_count) && $manage_stock == 'yes' ){
                $course_enroll_btn = '';
            }
            elseif ($product_stock_status == 'instock') {
                if (!empty($studentsenrolled)) {
                    $custom_repeater_item = get_post_meta( $product->get_id(), 'moodle_course_section_fields', true ); 
                    $totalavailableseat = $custom_repeater_item[0]['total_count'];
                    $remain_available_seat = $totalavailableseat - $studentsenrolled ;
                    if($remain_available_seat != 0){
                        $course_enroll_btn = '<li class="course-enrolbtn course">
                            <span class="moodle-course-enroll-btn">
                                <button type="button" name="course-enroll" class="course-enroll-btn" id="course-enroll" data-id="' . $item_value['course_groups'] . '" data-item-id="' . $product->get_id() . '"><strong>ENROLL NOW</strong></button>
                            </span>
                        </li>';
                        $subscribe_enroll_btn = '<li class="course-enrolbtn course">
                            <span class="moodle-course-enroll-btn">
                                <button type="button" name="course-enroll" class="course-enroll-btn" id="course-enroll" data-id="' . $item_value['course_groups'] . '" data-item-id="' . $product->get_id() . '"><strong>SUBSCRIBE NOW</strong></button>
                            </span>
                        </li>';
                    }else{
                        $course_enroll_btn = '';
                        $subscribe_enroll_btn = '';
                    }
                }else{
                    $course_enroll_btn = '<li class="course-enrolbtn course">
                                                    <span class="moodle-course-enroll-btn">
                                                        <button type="button" name="course-enroll" class="course-enroll-btn" id="course-enroll" data-id="' . $item_value['course_groups'] . '" data-item-id="' . $product->get_id() . '"><strong>ENROLL NOW</strong></button>
                                                    </span>
                                                </li>';
                    $subscribe_enroll_btn = '<li class="course-enrolbtn course">
                        <span class="moodle-course-enroll-btn">
                            <button type="button" name="course-enroll" class="course-enroll-btn" id="course-enroll" data-id="' . $item_value['course_groups'] . '" data-item-id="' . $product->get_id() . '"><strong>SUBSCRIBE NOW</strong></button>
                        </span>
                    </li>';
                }
            }
            if(sizeof($directfrommoodle) > 0){
                $course_class = "dates of meeting";
                $havemeetings = false;
                $meetdate = [];
                // var_dump($item_value);
                // die;
                // echo "<pre>";
                // echo "LDS";
                // print_r($item_value); 
                // echo "<pre>";
                //die;
                if(is_array($item_value['allmetings']) && sizeof($item_value['allmetings']) > 0){
                    $totmeeting = $item_value['allmetings'];
                    $totmeeting2 = $item_value['allmetings'];
                    $dday = [];
                    $dday1 = [];
                    $enddday = 0;
                    $startdday = 0;

                    // live class days 
                    for ($i=0; $i < count($totmeeting); $i++) {
                    	 // var_dump($totmeeting[$i]);
                      //    echo "<pre>";
                      //    print_r($totmeeting[$i]);
                         // echo date("d/m/Y h:i:s a" ,$totmeeting[$i]['starttime']);
                         // echo "<br>" . date("d/m/Y h:i:s a",strtotime("this week + 6 day"));
                         // echo "<br>" . date("d/m/Y h:i:s a",strtotime("this week + 13 day"));
                        if(empty($totmeeting[$i]['starttime']) or $totmeeting[$i]['starttime'] == "NaN" or empty($totmeeting[$i]['duration']) or $totmeeting[$i]['duration'] == "NaN"){
    	                    continue; 
    	                 }
                         if(empty($startdday) ||  ($startdday > $totmeeting[$i]['starttime'] && $totmeeting[$i]['starttime'] > time())){
                            $startdday = $totmeeting[$i]['starttime'];

                         }
                         if(empty($enddday) ||  ($enddday < ($totmeeting[$i]['starttime']+$totmeeting[$i]['duration']) && $totmeeting[$i]['starttime'] > time())){
                            $enddday = $totmeeting[$i]['starttime']+$totmeeting[$i]['duration'];

                         }
                        array_push($dday,$totmeeting[$i]['starttime']);
                        array_push($dday1,date("l", $totmeeting[$i]['starttime']));

                        /*if(($totmeeting[$i]['starttime'] >= time()) && ($totmeeting[$i]['starttime'] <= strtotime("this week + 6 day"))){
                            $totmeeting[$i]['starttime'];
                            array_push($dday,date($displaytype?"D":"l", $totmeeting[$i]['starttime']));
                        }elseif(($totmeeting[$i]['starttime'] >= time()) && ($totmeeting[$i]['starttime'] > strtotime("this week + 6 day")) && ($totmeeting[$i]['starttime'] < strtotime("this week + 13 day")) ){
                            array_push($dday,date($displaytype?"D":"l", $totmeeting[$i]['starttime']));
                        }*/
                    }
                    $dday1 = array_unique($dday1);
                    $commaday = array();
                    if(sizeof($dday1) > 1){
                        foreach ($dday as $key => $value) {
                            array_push($commaday, date("D", $value));
                        }
                    } else {
                        foreach ($dday as $key => $value) {
                            array_push($commaday, date("l", $value));
                        }
                    }
                    $commaday = implode(', ',array_unique($commaday));
                    if(sizeof($dday) == 1){
                        $course_subscription = 'Meets Once';
                    } else {
                        $dateTime = new DateTime();
                        // $dateTime->setDate(date("Y", $startdday), date("m", $startdday), date("d", $startdday));
                        $dateTime->setTimestamp($startdday);
                        $monday = clone $dateTime->modify(('Sunday' == $dateTime->format('l')) ? 'Monday last week' : 'Monday this week');
                        $sunday = clone $dateTime->modify('Sunday this week');
                        $weekstart = date_timestamp_get($monday);
                        $weekend = date_timestamp_get($sunday);
                        // $nextweekend = strtotime("+1 week", date_timestamp_get($sunday));
                        $filtereddates = array_filter($dday, function($value) use ($weekstart, $weekend) {
                            return ($value >= $weekstart && $value <= $weekend);
                        });
                        $course_subscription = '';
                        switch (sizeof($filtereddates)) {
                            // case 1:
                            //     $course_subscription .= 'One Meeting Every Week';
                            //     break;
                            case 1:
                                $course_subscription .= 'One Meeting Every Week';
                                break;
                            case 2:
                                $course_subscription .= 'Two Meeting Every Week';
                                break;
                            case 3:
                                $course_subscription .= 'Three Meeting Every Week';
                                break;
                            case 4:
                                $course_subscription .= 'Four Meeting Every Week';
                                break;
                            case 5:
                                $course_subscription .= 'Five Meeting Every Week';
                                break;
                            case 6:
                                $course_subscription .= 'Meets Daily';
                                break;
                            default:

                                break;
                        }
                    }

                    // echo "<pre>";
                    // print_r($dday1);
                    // print_r($dday);
                    // print_r($commaday);
                    // echo "<pre>";
                    // die;
                    
                    $laststarttime = 0;
                    foreach($item_value['allmetings'] as $meeting){
                        $addend = false;
                        if($meeting['starttime'] > time()){
                           // print_r($meeting);
                            $havemeetings = true;
                            $meeting['endtime'] = $meeting['starttime']+$meeting['duration'];
                            $datetime_1 = date("Y-m-d h:i:s A", $meeting['starttime']); 
                            $datetime_2 = date("Y-m-d h:i:s A", $meeting['endtime']); 
                            $start_datetime = new DateTime($datetime_1); 
                            $diff = $start_datetime->diff(new DateTime($datetime_2)); 
                            $ttime = (($diff->h*60)+$diff->i);
                            $meeting_starttime = $meeting['starttime'];
                            if(empty($laststarttime) || $laststarttime > $meeting['endtime']){
                                $laststarttime = $meeting['endtime'];
                                $addend = true;
                            }
                            if (!empty($meeting['starttime'])) {                               
                                $meeting['starttime'] = dateInTimeZone(date("F d, Y g:i A",$meeting['starttime']), "F d, Y g:i A", "g:i", $usertimezone);
                            }
                            if (!empty($meeting['endtime'])) {
                                $meeting['endtime'] = dateInTimeZone(date("F d, Y g:i A",$meeting['endtime']), "F d, Y g:i A", "g:i A", $usertimezone);
                            }

                            if($addend === true){
                                $course_class = $ttime." minutes <br>".$meeting['starttime']." - ".$meeting['endtime'];
                            }
                            array_push($meetdate,$meeting[$meeting_starttime]);
                        }
                    } 
                }
                $memday = $commaday;
                $meetcommaday = explode(",",$memday);

                $course_class = $commaday."<br>".($displaytype?'':date('F d, Y',$startdday)."<br>").$course_class;
                    $dateFirst = $meetdate[0];
                    $firstt = date('Y-m-d', $startdday );
                    $dateSecond = $enddday;
                    $secondd = date('Y-m-d', $dateSecond );
                    $startDate = new DateTime($firstt);
                    $endDate = new DateTime($secondd);
                    $diff = $endDate->diff($startDate);
                    $numberOfWeeks = round($diff->days / 7);
                   
                    
                if(!$havemeetings){continue;}
                $section_data[] = array(
                    'course_date' => $startdday,
                   // 'meet_startdate' => $meetdate[0],
                    'meet_startdate' => $startdday,
                    'meet_enddate' => $enddday,
                    'meet_durationweak' => $numberOfWeeks,
                    'meetcommaday' => $meetcommaday,
                    'course_weeks_off' => "",
                    'course_length' => "",
                    'course_class' => "",
                    'course_subscription' => $course_subscription,
                    'course_classes' => $course_class,
                    'course_teacher' => $teacher_name,
                    'course_calendar' => $item_value['course_start'],
                    'course_enroll_btn' => $course_enroll_btn,
                    'student_enroll' => $studentsenrolled_result,
                    'available_seat' => $available_seat,
                    'subscribe_enroll_btn' => $subscribe_enroll_btn,
                );                
            } else {
                $classess = (isset($item_value['course_classes'])) ? $item_value['course_classes'] : '';
                $course_from_time = (isset($item_value['course_from_time'])) ? $item_value['course_from_time'] : '';
                $course_to_time = (isset($item_value['course_to_time'])) ? $item_value['course_to_time'] : '';
                $course_length_from_date = (isset($item_value['course_length_from_date'])) ? $item_value['course_length_from_date'] : '';
                $course_length_to_date = (isset($item_value['course_length_to_date'])) ? $item_value['course_length_to_date'] : '';
                $course_weeks_off = (isset($item_value['course_weeks_off'])) ? $item_value['course_weeks_off'] : '';
                //course_from_time->h:i A
                //course_from_date->F d, Y
                $coursestart = (isset($item_value['course_length_from_date'])) ? $item_value['course_length_from_date'] : '';
    
                $course_subscription_term_time = (isset($item_value['course_subscription_time'])) ? $item_value['course_subscription_time'] : '';
                $course_subscription_term_week = (isset($item_value['course_subscription_week'])) ? $item_value['course_subscription_week'] : '';
    
    
                if ($moodle_one_time_subscription == '1'){
                    $subscription_terms = 'Meets Once';
                }
                elseif (!empty($course_subscription_term_time) || !empty($course_subscription_term_week)){
                    $sub_number_format = new NumberFormatter('en_US', NumberFormatter::SPELLOUT);
                    $subnumber = $sub_number_format->format($course_subscription_term_time);
                    if ($course_subscription_term_time == '1'){
                        $time = 'Time';
                    }else{
                        $time = 'Times';
                    }
                    $week_split = str_replace('_',' ',$course_subscription_term_week);
                    $subscription_terms = ucwords($subnumber) ." $time ".ucwords($week_split)." Week";
                }
    
                if (!empty($classess)) {
                    $classess = array_map(function ($day) use ($course_from_time, $usertimezone) {
                        return dateInTimeZone("$day $course_from_time", "l h:i A", "l", $usertimezone);
                    }, $classess);
                }
                if (!empty($coursestart)) {
                    $coursestart = dateInTimeZone("$coursestart $course_from_time", "F d, Y h:i A", "F d, Y", $usertimezone);
                }
                if (!empty($course_length_from_date)) {
                    $course_length_from_date = dateInTimeZone("$course_length_from_date $course_from_time", "F d, Y h:i A", "M d, Y", $usertimezone);
                }
                if (!empty($course_length_to_date)) {
                    $course_length_to_date = dateInTimeZone("$course_length_to_date $course_from_time", "F d, Y h:i A", "M d, Y", $usertimezone);
                }
                if (!empty($course_from_time)) {
                    $course_from_time = dateInTimeZone($course_from_time, "h:i A", null, $usertimezone);
                }
                if (!empty($course_to_time)) {
                    $course_to_time = dateInTimeZone($course_to_time, "h:i A", null, $usertimezone);
                }
    
                if ($moodle_one_time_subscription == '0' && empty($course_length_from_date)) {
                    echo "moodle_one_time_subscription: {$moodle_one_time_subscription} not found";
                    continue;
                }
    
                if ($moodle_one_time_subscription == '0') {
                    $week_days = array_map('strtolower', $classess);
                    $dates = days_in_interval($course_length_from_date, $course_length_to_date, $week_days, $usertimezone);
                    $new_dates = remove_old_dates($dates, true);
                    $weeks_off = weeks_off_calendar($course_weeks_off, $new_dates);
                }
    
                if ($product->is_type('simple') && strtotime('today') > strtotime($course_length_from_date)){
                    echo " hide if to date passed";
                    continue; // hide if to date passed
                }
                if ($product->is_type('simple')){
                    $course_date = $course_length_from_date;
                }
                elseif ($moodle_one_time_subscription == '1'){
                    $course_date = date('M j, Y',strtotime($coursestart_date));
                    if (strtotime('today') > strtotime($coursestart_date)){
                        echo " hide if to date passed";
                        continue;
                    }
                }
                elseif ($product->is_type('subscription') && is_null($weeks_off[0])) {
                    $course_date = $new_dates[0];
                }
                else {
                    $course_date = $weeks_off[0];
                }
    
                if (!empty($subscription_interval)) {
                    $subscription_result = '<span class="course-length-date">'.$subscription_terms.'</span>';
                    //$subscription_result = '<span class="course-length-date">'.$subscription_terms.'</span><span class="course-length-weeks">'.get_total_weeks($course_date,end($weeks_off),$course_weeks_off,$week_days).'</span>';
                    $course_length = 'MEETING FREQUENCY:';
                } else if($moodle_one_time_subscription == 1){
                    $subscription_result = '<span class="course-length-date">'.$subscription_terms.'</span>';
                    //$subscription_result = '<span class="course-length-date">'.$subscription_terms.'</span><span class="course-length-weeks">'.get_total_weeks($course_date,end($weeks_off),$course_weeks_off,$week_days).'</span>';
                    $course_length = 'MEETING FREQUENCY:';
                } else {
                    $subscription_result = '<span class="course-length-date">' . $course_length_from_date . ' - ' . $course_length_to_date . '</span>
                                        <span class="course-length-weeks">' . get_total_weeks($course_date,$course_length_to_date,$course_weeks_off, $week_days) . '</span>';
                    $course_length = 'COURSE LENGTH:';
                }
                if ($moodle_one_time_subscription == '1') {
                    $day = date("l",strtotime($coursestart_date));
                    $course_class = '<span class="course-days">' . ucwords($day) . '</span>
                                        <span class="course-time">' . $course_from_time . ' - ' . $course_to_time . '</span>';
                    $courseclass = 'LIVE CLASS:';
                }else{
                    $course_class = '<span class="course-days">' . ucwords(implode('s & ', $classess) . 's') . '</span>
                                        <span class="course-time">' . $course_from_time . ' - ' . $course_to_time . '</span>';
                    $courseclass = 'LIVE CLASSES:';
                }
    
                $section_data[] = array(
                    'course_date' => date('Y-m-d', strtotime($course_date)),
                    'meet_startdate' => strtotime($course_date),
                    'meet_enddate' => strtotime($course_length_to_date),
                    'course_weeks_off' => $course_date,
                    'course_length' => $course_length,
                    'course_class' => $courseclass,
                    'course_subscription' => $subscription_result,
                    'course_classes' => $course_class,
                    'course_teacher' => $teacher_name,
                    'course_calendar' => $item_value['course_start'],
                    'course_enroll_btn' => $course_enroll_btn,
                    'student_enroll' => $studentsenrolled_result,
                );
            }
        }
        usort($section_data, function ($a, $b) {
            return strtotime($a['course_date']) - strtotime($b['course_date']);
        });
        /*custom script*/
        ?>
       
        <script>

            jQuery("#user_select_timezone").change(function () {
                var selected = jQuery(this).val();
                var plugins_url = "<?php echo plugins_url() .'/sync-course/time-zone-ajax.php'?>";
                var postid = "<?php echo $post->ID ?>";
                var productid = "<?php echo $product->get_id() ?>";
                jQuery.ajax({
                    type: "POST",
                    url : plugins_url,
                    data : {
                        selected_time   :   selected,                              
                        postid          :   postid,                             
                        productid       :   productid,                             
                    },
                success:function(data){
                console.log(data);
                //if(!empty(data)){
                     jQuery("#meetingbyzone").html(data);

               // }
                }

                });
            });
        </script>


        <?php
        /*custom script End*/

        /*Sushil commented this*/
        /*echo '<div class="moodle-course-section slider">';
        foreach ($section_data as $data) {
            $result = '<div class="moodle-section">
                            <div class="moodle-section-content-div">
                            <h2 class="text-center section-title">Section ' . chr(64 + ++$alphabet_val) . '</h2>
                            <ul class="course-content">
                                <li class="course-start course">
                                    <strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/2.png' . '" alt="" class="course-img">STARTS:</strong>
                                    <span>' . $data['course_weeks_off'] . '</span>
                                </li>
                                <li class="course-length course">
                                    <strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/1.png' . '" alt="" class="course-img">'.$data['course_length'].'</strong>
                                    ' . $data['course_subscription'] . '
                                </li>
                                <li class="course-classes course">
                                    <strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/4.png' . '" alt="" class="course-img">'.$data['course_class'].'</strong>
                                    ' . $data['course_classes'] . '
                                </li>
                                <li class="course-teacher course">
                                    <strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/3.png' . '" alt="" class="course-img">TEACHER:</strong>
                                    <span class="teacher-desc"><a href="#course-teacher-modal" class="link teacher-link" class="btn btn-primary" data-toggle="modal" data-target="#course-teacher-modal" data-id="' . $data['course_teacher']->ID . '">' . $data['course_teacher']->first_name . ' ' . $data['course_teacher']->last_name . '</a></span>
                                </li>
                                <li class="course-calendar course">
                                    <strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/6.png' . '" alt="" class="course-img">CALENDAR:</strong>
                                    <span class="calendar-desc"><a href="#course-calendar-modal" class="calendar-link link" data-toggle="modal" data-id="' . $post->ID . '" data-value="' . $data['course_calendar'] . '">View Entire Calendar</a></span>
                                </li>
                            </ul>
                            </div>
                            <input type="hidden" name="cs_start_date" id="cs_start_date" data-value="'.$data['course_weeks_off'].'">
                            <ul class="enrol-btn">' . $data['course_enroll_btn'] . $data['student_enroll'] . '</ul>
                        </div>';
            echo $result;
        }
        echo '</div>';*/



         $html = '<style>
                    #mcslist tbody tr td {
                        width: 19.95%;
                        text-overflow: ellipsis;
                        text-wrap: wrap;
                    }
                    #mcslist .subcol .subcoltxt{
                        align-self: center;
                    }
                    #mcslist .subcol{
                        display: flex;
                    }

                </style>';

        $html .= '<div id="meetingbyzone"><table class="moodle-course-section-list" id="mcslist" ><thead>';
        $html .= '<tr><th><strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/4.png' . '" alt="" class="course-img">Live Classes</strong></th>';
      
            if($displaytype != 0 ){
                if($displaytype == 2){
                    $html .= '<th><strong class="subcol"><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/6.png' . '" alt="" class="course-img"><span class="subcoltxt" >Subscription Schedule </span></strong></th>';
                } else {
                    $html .= '<th><strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/6.png' . '" alt="" class="course-img">Schedule</strong></th>';
                }
            }
        
        $html .= '<th><strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/3.png' . '" alt="" class="course-img">TEACHER</strong></th>';
        if($displaytype !=0 ){
            $html .= '<th><strong><img src="https://staging.lemons-aid.com/wp-content/uploads/2023/08/slybus.png
            " alt="" class="course-img loaded" data-was-processed="true">Syllabus</strong></th>';
        }
        $html .= '<th></th></tr>';
        $html .= '</thead>
        <tbody>';
        $count = 0;
        foreach ($section_data as $data) {
            /*echo "<pre>";
            print_r($data);
            echo "</pre>";*/
            $html .= '<tr><td>'. $data['course_classes'] . '</td>';
            if($displaytype !=0 ){
                if($displaytype == 2){
                    $html .= '<td><span class="calendar-desc ">';
                    // if($data['meet_startdate'] <= strtotime("this week + 6 day")){
                    //     if( count($data['meetcommaday']) == 1 ){
                    //         $html .= 'One Meeting Every Week : '.date('M j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 2){
                    //         $html .= 'Two Meeting Every Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 3){
                    //         $html .= 'Three Meeting Every Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 4){
                    //         $html .= 'Four Meeting Every Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }else{
                    //         $html .= 'Five Meeting Every Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }
                    // }else{
                    //     if( count($data['meetcommaday']) == 1 ){
                    //         $html .= 'One Meeting Every other Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 2){
                    //         $html .= 'Two Meeting Every other Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 3){
                    //         $html .= 'Three Meeting Every other Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 4){
                    //         $html .= 'Four Meeting Every other Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }else{
                    //         $html .= 'Four Meeting Every other Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }
                    // }
                    $html .= $data['course_subscription'];
                    $html .= '<br> Next Meeting: '.date('M j, Y', $data['meet_startdate']);
                    $html .= '<br><a href="#course-calendar-modal" class="calendar-link link" data-toggle="modal" data-id="' . $post->ID . '" data-count="' . $count . '" data-value="' . $data['course_calendar'] .'" data-zone="' . $usertimezone .'">View All Meetings</a></span></td>';
                    $count++;
                } else {
                    $html .= '<td><span class="calendar-desc "> '.(($data['meet_durationweak'] == 0)?"":$data['meet_durationweak']." Weaks <br>" ).'Starts : '. date('M j, Y', $data['meet_startdate']).'<br>Ends : '.date('M j, Y', $data['meet_enddate']).'<br><a href="#course-calendar-modal" class="calendar-link link" data-toggle="modal" data-id="' . $post->ID . '" data-value="' . $data['course_calendar'] . '" data-count="' . $count . '" data-zone="' . $usertimezone .'" >View All Meetings </a></span></td>';
                    $count++;
                }
            }
            $html .= '<td><span class="teacher-desc"><a href="#course-teacher-modal" class="link teacher-link" class="btn btn-primary" data-toggle="modal" data-target="#course-teacher-modal" data-id="' . $data['course_teacher']->ID . '">' . $data['course_teacher']->first_name . ' ' . $data['course_teacher']->last_name . '</a></span></td>';
            
            if($displaytype !=0 ){
                $html .= '<td><span class="syllabus-data"><a href="#course-syllabus-modal" class="link syllabus-link" class="btn btn-primary" data-toggle="modal" data-target="#course-syllabus-modal">-syllabus-</a></span></td>';
            }
            
            if($displaytype == 2){
                $html .= '<td><input type="hidden" name="cs_start_date" id="cs_start_date" data-value="'.$data['course_weeks_off'].'"><ul class="enrol-btn">' .$data['subscribe_enroll_btn'] . $data['student_enroll'] . $data['available_seat'] . '</ul></td></tr>';
            }else{
                $html .= '<td><input type="hidden" name="cs_start_date" id="cs_start_date" data-value="'.$data['course_weeks_off'].'"><ul class="enrol-btn">' .$data['course_enroll_btn'] . $data['student_enroll'] . $data['available_seat'] . '</ul></td></tr>';
            }    
            
            
        }

        $html .= '<tbody>
        </table></div>';        
        echo $html;
                echo '</section>';
    }
    endif;
}
add_action('woocommerce_after_single_product_summary','moodle_course_section', 1);

function moodle_course_section_ajax($post, $product, $usertimezone = ""){
    global $wpdb;
    date_default_timezone_set( 'America/New_York' );
    if(empty($usertimezone)){
        $usertimezone = getRemoteUserTimeZone();
    }
    $number_format = new NumberFormatter('en_US', NumberFormatter::ORDINAL);
    $subscription_interval = get_post_meta($post->ID,'_subscription_period_interval',true);
    $subscription_period = get_post_meta($post->ID,'_subscription_period',true);
    $product_stock_status = get_post_meta($post->ID,'_stock_status',true);

    $custom_repeater_item = get_post_meta( $post->ID, 'moodle_course_section_fields', true );
    $moodle_courses_enable = get_post_meta( $post->ID, '_moodle_courses_enable', true );
    $moodle_one_time_subscription = get_post_meta( $post->ID, '_moodle_one_time_subscription', true );
    $group_id = array_column(!is_array($custom_repeater_item) ? []: $custom_repeater_item, 'course_groups');
    $directfrommoodle = array_column(!is_array($custom_repeater_item) ? []: $custom_repeater_item, 'directfrommoodle');
    
    $current_productid = $product->get_id();
    $product_cats_ids = wc_get_product_term_ids( $current_productid, 'product_cat' );
    foreach( $product_cats_ids as $cat_id ) {
        $term = get_term_by( 'id', $cat_id, 'product_cat' );
        $product_catname = $term->name;
    }
    if(strpos(strtolower($product_catname),"one")){
        $displaytype = 0;
    }elseif(strpos(strtolower($product_catname),"subscription")){
        $displaytype = 2;
    }else{
        $displaytype = 1;
    }    


    $manage_stock = get_post_meta($post->ID,'_manage_stock',true);

    if (count(array_values(array_unique($group_id))) == 1){
        $val = in_array('0',$group_id);
    }else{
        $val = '1';
    }
    if ($subscription_interval > '1'){
        $subscription = sprintf('Every %s %s', $number_format->format($subscription_interval), ucwords(implode('', array_map(function($i){return $i;}, (array)$subscription_period))));
    }else{
        $subscription = sprintf('Every %s', ucwords($subscription_period));
    }
    if($moodle_courses_enable == '0'):
        if( !empty($custom_repeater_item) && count(array_values(array_unique($group_id))) != $val){
            $alphabet_val = '';
            $section_data = array();
                     
        foreach( $custom_repeater_item as $item_key => $item_value ) {
            $coursestart_date = (isset($item_value['course_start'])) ? $item_value['course_start'] : '';
            $teacher_name = get_user_by('id', (isset($item_value['course_teacher'])) ? $item_value['course_teacher'] : '');
            $moodlegroupid = isset($item_value['course_groups']) ? $item_value['course_groups'] : '';
            if ($moodlegroupid == '0'){
                continue;
            }
            $studentsenrolled = null;
            if (!empty($moodlegroupid)) {
                $studentsenrolled = $wpdb->get_var("SELECT COUNT(DISTINCT p.ID)
                FROM {$wpdb->prefix}woocommerce_order_itemmeta as woim,
                     {$wpdb->prefix}woocommerce_order_itemmeta as woim2,
                     {$wpdb->prefix}woocommerce_order_items as woi,
                     {$wpdb->prefix}posts as p
                WHERE woi.order_item_id = woim.order_item_id
                AND woi.order_id = p.ID
                AND woim2.order_item_id = woi.order_item_id
                AND p.post_status IN ( 'wc-completed' )
                AND woim.meta_key IN ( '_product_id', '_variation_id' )
                AND woim.meta_value LIKE '$post->ID'
                AND woim2.meta_key IN ( 'product_group_id' )
                AND woim2.meta_value LIKE '$moodlegroupid'");
            }
            if (empty($studentsenrolled)) {
                $studentsenrolled = 0;
            }
            if (!empty($studentsenrolled)) {
                if ($studentsenrolled > 1) {
                    $student = 'Students ';
                } else {
                    $student = 'Student ';
                }
                
                $studentsenrolled_result = '<li class="course-students course">
                                                    <strong><i>' . $studentsenrolled . ' ' . $student . ' Enrolled</i></strong>
                                                </li>';
                //------------Remaining available seat--------------
                $custom_repeater_item = get_post_meta( $product->get_id(), 'moodle_course_section_fields', true ); 
                $totalavailableseat = $custom_repeater_item[0]['total_count'];
                $remain_available_seat = $totalavailableseat - $studentsenrolled ;
                if($remain_available_seat != 0){
                    $available_seat = '<li class="course-students course">
                                            <strong><i>' . $remain_available_seat . ' seats available</i></strong>
                                        </li>';
                }

            }else{
                $studentsenrolled_result = '';
                $available_seat = '';
            }
            $total_count = (isset($item_value['total_count'])) ? $item_value['total_count'] : '';
            if (empty($total_count) && $manage_stock == 'yes' ){
                $course_enroll_btn = '';
            }
            elseif ($product_stock_status == 'instock') {
                if (!empty($studentsenrolled)) {
                    $custom_repeater_item = get_post_meta( $product->get_id(), 'moodle_course_section_fields', true ); 
                    $totalavailableseat = $custom_repeater_item[0]['total_count'];
                    $remain_available_seat = $totalavailableseat - $studentsenrolled ;
                    if($remain_available_seat != 0){
                        $course_enroll_btn = '<li class="course-enrolbtn course">
                            <span class="moodle-course-enroll-btn">
                                <button type="button" name="course-enroll" class="course-enroll-btn" id="course-enroll" data-id="' . $item_value['course_groups'] . '" data-item-id="' . $product->get_id() . '"><strong>ENROLL NOW</strong></button>
                            </span>
                        </li>';
                        $subscribe_enroll_btn = '<li class="course-enrolbtn course">
                            <span class="moodle-course-enroll-btn">
                                <button type="button" name="course-enroll" class="course-enroll-btn" id="course-enroll" data-id="' . $item_value['course_groups'] . '" data-item-id="' . $product->get_id() . '"><strong>SUBSCRIBE NOW</strong></button>
                            </span>
                        </li>';
                    }else{
                        $course_enroll_btn = '';
                        $subscribe_enroll_btn = '';
                    }
                }else{
                    $course_enroll_btn = '<li class="course-enrolbtn course">
                                                    <span class="moodle-course-enroll-btn">
                                                        <button type="button" name="course-enroll" class="course-enroll-btn" id="course-enroll" data-id="' . $item_value['course_groups'] . '" data-item-id="' . $product->get_id() . '"><strong>ENROLL NOW</strong></button>
                                                    </span>
                                                </li>';
                    $subscribe_enroll_btn = '<li class="course-enrolbtn course">
                        <span class="moodle-course-enroll-btn">
                            <button type="button" name="course-enroll" class="course-enroll-btn" id="course-enroll" data-id="' . $item_value['course_groups'] . '" data-item-id="' . $product->get_id() . '"><strong>SUBSCRIBE NOW</strong></button>
                        </span>
                    </li>';
                }
            }
            if(sizeof($directfrommoodle) > 0){
                $course_class = "dates of meeting";
                $havemeetings = false;
                $meetdate = [];
                if(is_array($item_value['allmetings']) && sizeof($item_value['allmetings']) > 0){
                    $totmeeting = $item_value['allmetings'];
                    $totmeeting2 = $item_value['allmetings'];
                    $dday = [];
                    $dday1 = [];
                    $enddday = 0;
                    $startdday = 0;

                    // live class days 
                   
                    for ($i=0; $i < count($totmeeting); $i++) { 
                        if(empty($totmeeting[$i]['starttime']) or $totmeeting[$i]['starttime'] == "NaN" or empty($totmeeting[$i]['duration']) or $totmeeting[$i]['duration'] == "NaN"){
                            continue; 
                        }
                        if(empty($startdday) ||  ($startdday > $totmeeting[$i]['starttime'] && $totmeeting[$i]['starttime'] > time())){
                            $startdday = $totmeeting[$i]['starttime'];

                         }
                        if(empty($enddday) ||  ($enddday < ($totmeeting[$i]['starttime']+$totmeeting[$i]['duration']) && $totmeeting[$i]['starttime'] > time())){
                            $enddday = $totmeeting[$i]['starttime']+$totmeeting[$i]['duration'];

                        }
                        array_push($dday,$totmeeting[$i]['starttime']);
                        array_push($dday1,date("l", $totmeeting[$i]['starttime']));

                        // if(($totmeeting[$i]['starttime'] >= time()) && ($totmeeting[$i]['starttime'] <= strtotime("this week + 6 day"))){
                        //     array_push($dday,date($displaytype?"D":"l", $totmeeting[$i]['starttime']));
                        // }elseif(($totmeeting[$i]['starttime'] >= time()) && ($totmeeting[$i]['starttime'] > strtotime("this week + 6 day")) && ($totmeeting[$i]['starttime'] < strtotime("this week + 13 day")) ){
                        //     array_push($dday,date($displaytype?"D":"l", $totmeeting[$i]['starttime']));
                        // }
               
                    }
                    $dday1 = array_unique($dday1);
                    $commaday = array();
                    if(sizeof($dday1) > 1){
                        foreach ($dday as $key => $value) {
                            array_push($commaday, date("D", $value));
                        }
                    } else {
                        foreach ($dday as $key => $value) {
                            array_push($commaday, date("l", $value));
                        }
                    }
                    $commaday = implode(', ',array_unique($commaday));
                    if(sizeof($dday) == 1){
                        $course_subscription = 'Meets Once';
                    } else {
                        $dateTime = new DateTime();
                        // $dateTime->setDate(date("Y", $startdday), date("m", $startdday), date("d", $startdday));
                        $dateTime->setTimestamp($startdday);
                        $monday = clone $dateTime->modify(('Sunday' == $dateTime->format('l')) ? 'Monday last week' : 'Monday this week');
                        $sunday = clone $dateTime->modify('Sunday this week');
                        $weekstart = date_timestamp_get($monday);
                        $weekend = date_timestamp_get($sunday);
                        // $nextweekend = strtotime("+1 week", date_timestamp_get($sunday));
                        $filtereddates = array_filter($dday, function($value) use ($weekstart, $weekend) {
                            return ($value >= $weekstart && $value <= $weekend);
                        });
                        $course_subscription = '';
                        switch (sizeof($filtereddates)) {
                            // case 1:
                            //     $course_subscription .= 'One Meeting Every Week'; 
                            //     break;
                            case 1:
                                $course_subscription .= 'One Meeting Every Week';
                                break;
                            case 2:
                                $course_subscription .= 'Two Meeting Every Week';
                                break;
                            case 3:
                                $course_subscription .= 'Three Meeting Every Week';
                                break;
                            case 4:
                                $course_subscription .= 'Four Meeting Every Week';
                                break;
                            case 5:
                                $course_subscription .= 'Five Meeting Every Week';
                                break;
                            case 6:
                                $course_subscription .= 'Meets Daily';
                                break;
                            default:

                                break; 
                        }
                    }
                    $laststarttime = 0;
                    foreach($item_value['allmetings'] as $meeting){
                        $addend = false;
                        if($meeting['starttime'] > time()){
                            $havemeetings = true;
                            $meeting['endtime'] = $meeting['starttime']+$meeting['duration'];
                            $datetime_1 = date("Y-m-d h:i:s A", $meeting['starttime']); 
                            $datetime_2 = date("Y-m-d h:i:s A", $meeting['endtime']); 
                            $start_datetime = new DateTime($datetime_1); 
                            $diff = $start_datetime->diff(new DateTime($datetime_2)); 
                            $ttime = (($diff->h*60)+$diff->i);
                            $meeting_starttime = $meeting['starttime'];
                            if(empty($laststarttime) || $laststarttime > $meeting['endtime']){
                                $laststarttime = $meeting['endtime'];
                                $addend = true;
                            }
                            if (!empty($meeting['starttime'])) {
                                
                                $meeting['starttime'] = dateInTimeZone(date("F d, Y g:i A",$meeting['starttime']), "F d, Y g:i A", "g:i", $usertimezone);
                            }
                            if (!empty($meeting['endtime'])) {
                                $meeting['endtime'] = dateInTimeZone(date("F d, Y g:i A",$meeting['endtime']), "F d, Y g:i A", "g:i A", $usertimezone);

                            }
                            if($addend === true){
                                $course_class = $ttime." minutes <br>".$meeting['starttime']." - ".$meeting['endtime'];
                            }

                            array_push($meetdate,$meeting[$meeting_starttime]);
                        }
                    }
                }
                $memday = $commaday;
                $meetcommaday = explode(",",$memday);

                $course_class = $commaday."<br>".($displaytype?'':date('F d, Y',$startdday)."<br>").$course_class;
                    $dateFirst = $meetdate[0];
                    $firstt = date('Y-m-d', $startdday );
                    $dateSecond = $enddday;
                    $secondd = date('Y-m-d', $enddday );
                    $startDate = new DateTime($firstt);
                    $endDate = new DateTime($secondd);
                    $diff = $endDate->diff($startDate);
                    $numberOfWeeks = round($diff->days / 7);
                   
                    
                if(!$havemeetings){continue;}
                $section_data[] = array(
                    'course_date' => $startdday,
                    'meet_startdate' => $startdday,
                    'meet_enddate' => $enddday,
                    'meet_durationweak' => $numberOfWeeks,
                    'meetcommaday' => $meetcommaday,
                    'course_weeks_off' => "",
                    'course_length' => "",
                    'course_class' => "",
                    'course_subscription' => $course_subscription,
                    'course_classes' => $course_class,
                    'course_teacher' => $teacher_name,
                    'course_calendar' => $item_value['course_start'],
                    'course_enroll_btn' => $course_enroll_btn,
                    'student_enroll' => $studentsenrolled_result,
                    'available_seat' => $available_seat,
                    'subscribe_enroll_btn' => $subscribe_enroll_btn,
                );                
            } else {
                $classess = (isset($item_value['course_classes'])) ? $item_value['course_classes'] : '';
                $course_from_time = (isset($item_value['course_from_time'])) ? $item_value['course_from_time'] : '';
                $course_to_time = (isset($item_value['course_to_time'])) ? $item_value['course_to_time'] : '';
                $course_length_from_date = (isset($item_value['course_length_from_date'])) ? $item_value['course_length_from_date'] : '';
                $course_length_to_date = (isset($item_value['course_length_to_date'])) ? $item_value['course_length_to_date'] : '';
                $course_weeks_off = (isset($item_value['course_weeks_off'])) ? $item_value['course_weeks_off'] : '';
                //course_from_time->h:i A
                //course_from_date->F d, Y
                $coursestart = (isset($item_value['course_length_from_date'])) ? $item_value['course_length_from_date'] : '';
                // print_r($coursestart);
                // echo "<br>";
                // print_r($usertimezone);
                // echo "<br>";
                // print_r($course_from_time);
                // die;
    
                $course_subscription_term_time = (isset($item_value['course_subscription_time'])) ? $item_value['course_subscription_time'] : '';
                $course_subscription_term_week = (isset($item_value['course_subscription_week'])) ? $item_value['course_subscription_week'] : '';
    
    
                if ($moodle_one_time_subscription == '1'){
                    $subscription_terms = 'Meets Once';
                }
                elseif (!empty($course_subscription_term_time) || !empty($course_subscription_term_week)){
                    $sub_number_format = new NumberFormatter('en_US', NumberFormatter::SPELLOUT);
                    $subnumber = $sub_number_format->format($course_subscription_term_time);
                    if ($course_subscription_term_time == '1'){
                        $time = 'Time';
                    }else{
                        $time = 'Times';
                    }
                    $week_split = str_replace('_',' ',$course_subscription_term_week);
                    $subscription_terms = ucwords($subnumber) ." $time ".ucwords($week_split)." Week";
                }
    
                if (!empty($classess)) {
                    $classess = array_map(function ($day) use ($course_from_time, $usertimezone) {
                        return dateInTimeZone("$day $course_from_time", "l h:i A", "l", $usertimezone);
                    }, $classess);
                }
                if (!empty($coursestart)) {
                    $coursestart = dateInTimeZone("$coursestart $course_from_time", "F d, Y h:i A", "F d, Y", $usertimezone);
                }
                if (!empty($course_length_from_date)) {
                    $course_length_from_date = dateInTimeZone("$course_length_from_date $course_from_time", "F d, Y h:i A", "M d, Y", $usertimezone);
                }
                if (!empty($course_length_to_date)) {
                    $course_length_to_date = dateInTimeZone("$course_length_to_date $course_from_time", "F d, Y h:i A", "M d, Y", $usertimezone);
                }
                if (!empty($course_from_time)) {
                    $course_from_time = dateInTimeZone($course_from_time, "h:i A", null, $usertimezone);
                }
                if (!empty($course_to_time)) {
                    $course_to_time = dateInTimeZone($course_to_time, "h:i A", null, $usertimezone);
                }
    
                if ($moodle_one_time_subscription == '0' && empty($course_length_from_date)) {
                    // echo  "moodle_one_time_subscription: {$moodle_one_time_subscription} not found";
                    continue;
                }
    
                if ($moodle_one_time_subscription == '0') {
                    $week_days = array_map('strtolower', $classess);
                    $dates = days_in_interval($course_length_from_date, $course_length_to_date, $week_days, $usertimezone);
                    $new_dates = remove_old_dates($dates, true);
                    $weeks_off = weeks_off_calendar($course_weeks_off, $new_dates);
                }
    
                if ($product->is_type('simple') && strtotime('today') > strtotime($course_length_from_date)){
                    // echo  " hide if to date passed";
                    continue; // hide if to date passed
                }
                if ($product->is_type('simple')){
                    $course_date = $course_length_from_date;
                }
                elseif ($moodle_one_time_subscription == '1'){
                    $course_date = date('M j, Y',strtotime($coursestart_date));
                    if (strtotime('today') > strtotime($coursestart_date)){
                        // echo  " hide if to date passed";
                        continue;
                    }
                }
                elseif ($product->is_type('subscription') && is_null($weeks_off[0])) {
                    $course_date = $new_dates[0];
                }
                else {
                    $course_date = $weeks_off[0];
                }
    
                if (!empty($subscription_interval)) {
                    $subscription_result = '<span class="course-length-date">'.$subscription_terms.'</span>';
                    //$subscription_result = '<span class="course-length-date">'.$subscription_terms.'</span><span class="course-length-weeks">'.get_total_weeks($course_date,end($weeks_off),$course_weeks_off,$week_days).'</span>';
                    $course_length = 'MEETING FREQUENCY:';
                } else if($moodle_one_time_subscription == 1){
                    $subscription_result = '<span class="course-length-date">'.$subscription_terms.'</span>';
                    //$subscription_result = '<span class="course-length-date">'.$subscription_terms.'</span><span class="course-length-weeks">'.get_total_weeks($course_date,end($weeks_off),$course_weeks_off,$week_days).'</span>';
                    $course_length = 'MEETING FREQUENCY:';
                } else {
                    $subscription_result = '<span class="course-length-date">' . $course_length_from_date . ' - ' . $course_length_to_date . '</span>
                                        <span class="course-length-weeks">' . get_total_weeks($course_date,$course_length_to_date,$course_weeks_off, $week_days) . '</span>';
                    $course_length = 'COURSE LENGTH:';
                }
                if ($moodle_one_time_subscription == '1') {
                    $day = date("l",strtotime($coursestart_date));
                    $course_class = '<span class="course-days">' . ucwords($day) . '</span>
                                        <span class="course-time">' . $course_from_time . ' - ' . $course_to_time . '</span>';
                    $courseclass = 'LIVE CLASS:';
                }else{
                    $course_class = '<span class="course-days">' . ucwords(implode('s & ', $classess) . 's') . '</span>
                                        <span class="course-time">' . $course_from_time . ' - ' . $course_to_time . '</span>';
                    $courseclass = 'LIVE CLASSES:';
                }
    
                $section_data[] = array(
                    'course_date' => date('Y-m-d', strtotime($course_date)),
                    'course_weeks_off' => $course_date,
                    'course_length' => $course_length,
                    'course_class' => $courseclass,
                    'course_subscription' => $subscription_result,
                    'course_classes' => $course_class,
                    'course_teacher' => $teacher_name,
                    'course_calendar' => $item_value['course_start'],
                    'course_enroll_btn' => $course_enroll_btn,
                    'student_enroll' => $studentsenrolled_result,
                );
            }
        }
        usort($section_data, function ($a, $b) {
            return strtotime($a['course_date']) - strtotime($b['course_date']);
        });
        $current_productid = $product->get_id();
        $post = get_post( $current_productid );
        // $post_categories = wp_get_post_categories( $current_productid );
        $product_cats_ids = wc_get_product_term_ids( $current_productid, 'product_cat' );
        foreach( $product_cats_ids as $cat_id ) {
            $term = get_term_by( 'id', $cat_id, 'product_cat' );
            $product_catname = $term->name;
        }
        if(strpos(strtolower($product_catname),"one")){
            $displaytype = 0;
        }elseif(strpos(strtolower($product_catname),"subscription")){
            $displaytype = 2;
        }else{
            $displaytype = 1;
        }

         $html = '<style>
                    #mcslist tbody tr td {
                        width: 19.95%;
                        text-overflow: ellipsis;
                        text-wrap: wrap;
                    }
                    #mcslist .subcol .subcoltxt{
                        align-self: center;
                    }
                    #mcslist .subcol{
                        display: flex;
                    }

                </style>';
        $html .= '<table class="moodle-course-section-list" id="mcslist" ><thead>';
        $html .= '<tr><th><strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/4.png' . '" alt="" class="course-img">Live Classes</strong></th>';
      
            if($displaytype != 0 ){
                if($displaytype == 2){
                    $html .= '<th><strong class="subcol"><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/6.png' . '" alt="" class="course-img"><span class="subcoltxt" >Subscription Schedule </span></strong></th>';
                } else {
                    $html .= '<th><strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/6.png' . '" alt="" class="course-img">Schedule</strong></th>';
                }
            }
        
        $html .= '<th><strong><img src="' . get_theme_root_uri() . '/buddyboss-theme-child/framework/assets/images/3.png' . '" alt="" class="course-img">TEACHER</strong></th>';
        if($displaytype !=0 ){
            $html .= '<th><strong><img src="https://staging.lemons-aid.com/wp-content/uploads/2023/08/slybus.png
            " alt="" class="course-img loaded" data-was-processed="true">Syllabus</strong></th>';
        }
        $html .= '<th></th></tr>';
        $html .= '</thead>
        <tbody>';
        $count = 0;
        foreach ($section_data as $data) {
            $html .= '<tr><td>'. $data['course_classes'] . '</td>';
            if($displaytype !=0 ){
                if($displaytype == 2){
                    $html .= '<td><span class="calendar-desc ">';
                    // if($data['meet_startdate'] <= strtotime("this week + 6 day")){
                    //     if( count($data['meetcommaday']) == 1 ){
                    //         $html .= 'One Meeting Every Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 2){
                    //         $html .= 'Two Meeting Every Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 3){
                    //         $html .= 'Three Meeting Every Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 4){
                    //         $html .= 'Four Meeting Every Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }else{
                    //         $html .= 'Five Meeting Every Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }
                    // }else{
                    //     if( count($data['meetcommaday']) == 1 ){
                    //         $html .= 'One Meeting Every other Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 2){
                    //         $html .= 'Two Meeting Every other Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 3){
                    //         $html .= 'Three Meeting Every other Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }elseif(count($data['meetcommaday']) == 4){
                    //         $html .= 'Four Meeting Every other Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }else{
                    //         $html .= 'Four Meeting Every other Week : '.date('D j, Y', $data['meet_startdate']);
                    //     }
                    // }
                    $html .= $data['course_subscription'];
                    $html .= '<br> Next Meeting: '.date('M j, Y', $data['meet_startdate']);
                    $html .= '<br><a href="#course-calendar-modal" class="calendar-link link" data-toggle="modal" data-id="' . $post->ID . '" data-count="' . $count . '" data-value="' . $data['course_calendar'] .'" data-zone="' . $usertimezone . '">View All Meetings </a></span></td>';
                    $count++;
                } else {
                    $html .= '<td><span class="calendar-desc "> '.(($data['meet_durationweak'] == 0)?"":$data['meet_durationweak']." Weaks <br>" ).'Starts : '. date('M j, Y', $data['meet_startdate']).'<br>Ends : '.date('M j, Y', $data['meet_enddate']).'<br><a href="#course-calendar-modal" class="calendar-link link" data-toggle="modal" data-id="' . $post->ID . '" data-value="' . $data['course_calendar'] . '" data-count="' . $count . '" data-zone="' . $usertimezone . '" >View All Meetings</a></span></td>';
                    $count++;
                }
            }
            $html .= '<td><span class="teacher-desc"><a href="#course-teacher-modal" class="link teacher-link" class="btn btn-primary" data-toggle="modal" data-target="#course-teacher-modal" data-id="' . $data['course_teacher']->ID . '">' . $data['course_teacher']->first_name . ' ' . $data['course_teacher']->last_name . '</a></span></td>';
            if($displaytype !=0 ){
                $html .= '<td>----</td>';
            }
            
            if($displaytype == 2){
                $html .= '<td><input type="hidden" name="cs_start_date" id="cs_start_date" data-value="'.$data['course_weeks_off'].'"><ul class="enrol-btn">' .$data['subscribe_enroll_btn'] . $data['student_enroll'] . $data['available_seat'] . '</ul></td></tr>';
            }else{
                $html .= '<td><input type="hidden" name="cs_start_date" id="cs_start_date" data-value="'.$data['course_weeks_off'].'"><ul class="enrol-btn">' .$data['course_enroll_btn'] . $data['student_enroll'] . $data['available_seat'] . '</ul></td></tr>';
            }          
        }

        $html .= '<tbody>
        </table>';        
        return $html;
    }
    endif;
}


/* For Elementor Product Page */
function moodle_course_section_elementor( $element ) {
    global $post, $product;
    $custom_repeater_item = get_post_meta( $post->ID, 'moodle_course_section_fields', true );
    $moodle_courses_enable = get_post_meta( $post->ID, '_moodle_courses_enable', true );
    if ($moodle_courses_enable == '0'):
        if ('woocommerce-product-data-tabs' === $element->get_name() && !empty($custom_repeater_item)){
            moodle_course_section();
        }
    endif;
}
add_action( 'elementor/frontend/widget/before_render', 'moodle_course_section_elementor' );

/* Change add to cart link */
function moodle_course_add_to_cart_link($link) {
    global $product;
    $product_id = $product->get_id();
    $custom_repeater_item = get_post_meta( $product_id, 'moodle_course_section_fields', true );
    $moodle_courses_enable = get_post_meta( $product_id, '_moodle_courses_enable', true );
    $group_id = array_column(!is_array($custom_repeater_item) ? []: $custom_repeater_item, 'course_groups');
    if (count(array_values(array_unique($group_id))) == 1){
        $val = in_array('0',$group_id);
    }else{
        $val = '1';
    }
    if ($moodle_courses_enable == '0'):
        if ($custom_repeater_item && count(array_values(array_unique($group_id))) != $val) {
            $link = '<a href="' . get_permalink($product_id) . '" class="button product_type_simple" rel="nofollow">READ MORE</a>';
        }
    endif;
    return $link;
}
add_filter('woocommerce_loop_add_to_cart_link','moodle_course_add_to_cart_link');

/* Remove Add to cart on product page */
function moodle_add_to_cart_widget_content( $widget_content, $widget ) {
    global $product;
    if (is_product()) {
        $product_id = $product->get_id();
        $custom_repeater_item = get_post_meta($product_id, 'moodle_course_section_fields', true);
        $moodle_courses_enable = get_post_meta( $product_id, '_moodle_courses_enable', true );
        $group_id = array_column(!is_array($custom_repeater_item) ? []: $custom_repeater_item, 'course_groups');
        if (count(array_values(array_unique($group_id))) == 1){
            $val = in_array('0',$group_id);
        }else{
            $val = '1';
        }
        if ($moodle_courses_enable == '0'):
            if ('woocommerce-product-add-to-cart' === $widget->get_name() && $custom_repeater_item && count(array_values(array_unique($group_id))) != $val) {
                $widget_content = '';
            }
        endif;
    }
    return $widget_content;
}
add_filter( 'elementor/widget/render_content', 'moodle_add_to_cart_widget_content', 10, 2 );

/* Change order item details display label and value */
function modify_woocommerce_display_item_meta( $html, $item, $args = array() ) {
    $class2li = '';
    $before = '<ul class="wc-item-meta">';
    $after = '</ul>';
    foreach ( $item->get_formatted_meta_data() as $meta ) {
        if( $item->get_type() === 'line_item' && $meta->key === 'product_group_id' ) {
            $selectedgroups = !empty($meta->value) ? moodleservice('get_groups', ['groupids' => (array) $meta->value]): [];
            if(!empty($selectedgroups)) {
                $selectedgroupid = $meta->value ?? 0;
                foreach ($selectedgroups as $group) {
                    if ($selectedgroupid == $group->id) {
                        $display_value = wp_strip_all_tags($group->name);
                    }
                }
            }
            $display_key = 'Group Name';
            $display_clean_key = sanitize_title_with_dashes($meta->display_key);
            $value = $args['autop'] ? wp_kses_post($display_value) : wp_kses_post(make_clickable(trim($display_value)));
            $class2li .= '<li class="' . wp_strip_all_tags($display_clean_key) . '" style="margin-left: -27px;">' .
                '<strong class="wc-item-meta-label">' . $display_key . ': </strong>
            <p>' . $value . '</p></li>';
        }
    }
    $html = $before . $class2li . $after;
    return $html;
}
add_filter( 'woocommerce_display_item_meta', 'modify_woocommerce_display_item_meta', 10, 3 );

/* Remove add to cart for non elementor page */
function remove_product_description_add_cart_button() {
    global $post;
    $custom_repeater_item = get_post_meta( $post->ID, 'moodle_course_section_fields', true );
    $moodle_courses_enable = get_post_meta( $post->ID, '_moodle_courses_enable', true );
    if(is_array($custom_repeater_item)){
        $group_id = array_column($custom_repeater_item, 'course_groups');
    }
    if ($moodle_courses_enable == '0'):
        if ($custom_repeater_item && $group_id ) {
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
        }
    endif;
}
add_action('woocommerce_single_product_summary', 'remove_product_description_add_cart_button', 1 );
/* Add teacher modal to footer */
function teacher_modal(){
    echo '<div class="modal fade" id="course-teacher-modal" tabindex="-1" role="dialog" aria-labelledby="course-teacher-label" aria-hidden="true">
              <div id="modal-dialog" class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="course-teacher-label"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body mb-0 mt-0" id="course-teacher-modal-body"></div>
                </div>
              </div>
            </div>';

    echo '<div class="modal fade pt-5" id="course-syllabus-modal" tabindex="-1" role="dialog" aria-labelledby="course-syllabus-label" aria-hidden="true">
      <div id="modal-dialog" class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="course-syllabus-label">Welcome to Syllabus</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body mb-0 mt-0" id="course-syllabus-modal-body">syllabus-data render here...</div>
        </div>
      </div>
    </div>';


    echo '<div class="modal fade pt-5" id="course-calendar-modal" tabindex="-1" role="dialog" aria-labelledby="course-calendar-modal" aria-hidden="true">
            <div id="modal-dialog" class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="course-calendar-label"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body mb-0 mt-0" id="course-calendar-modal-body"></div>
            </div>
            </div>
        </div>';

    echo '<div class="modal fade pt-5" id="student-modal" tabindex="-1" role="dialog" aria-labelledby="student-modal-label" aria-hidden="true">
            <div id="modal-dialog" class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title justify-content-center" id="student-modal-label"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mb-0 mt-0" id="student-modal-body"></div>
                </div>
            </div>
        </div>';

    echo '<div class="modal fade pt-5" id="product-type" tabindex="-1" role="dialog" aria-labelledby="product-modal-label" aria-hidden="true">
            <div id="modal-dialog" class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title justify-content-center" id="product-modal-label"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mb-0 mt-0" id="product-modal-body"></div>
                </div>
            </div>
        </div>';
}
add_action('wp_footer','teacher_modal');

/* Course Teacher Show Modal */
function course_teacher_modal_show()
{
    $course_teacher_data = $_POST['course_teacher_data'];
    $teacher_modal = '';
    if (!empty($course_teacher_data)) {
        $username = get_user_by('id', $course_teacher_data);
        $title = 'Teacher Details';
        $modal_dialog = '';
        $teacher_modal = '<div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <label><i class="fa fa-user mr-1" aria-hidden="true"></i>Full Name</label>
                                </div>
                                <div class="col-md-8">
                                    <p>'.$username->first_name.' '.$username->last_name.'</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label><i class="fa fa-info-circle mr-1" aria-hidden="true"></i>About</label>
                                </div>
                                <div class="col-md-8">
                                    <p>'.$username->user_description.'</p>
                                </div>
                            </div>
                        </div>';

        echo json_encode(array('success' => true, 'message' => 'Successfull', 'result' => array('teacher_title' => $title, 'teacher_content' => $teacher_modal)));
    } else{
        echo json_encode(array('success' => false, 'message' => 'Not Successfull', 'result' => 'Something Went Wrong.'));
    }
    die();
}
add_action( 'wp_ajax_course_teacher_modal_show', 'course_teacher_modal_show' );
add_action( 'wp_ajax_nopriv_course_teacher_modal_show', 'course_teacher_modal_show' );

/* Course Calendar Show Modal */
function course_calendar_modal_show()
{
    date_default_timezone_set( 'America/New_York' );
    $course_calendar_data = $_POST['course_calendar_data'];
    $post_id = $_POST['post_id'];
    $section_count = $_POST['section_count'];
    $usertimezone = $_POST['usertimezone'];
    $_product = wc_get_product( $post_id );
    if (!empty($course_calendar_data)) {
        $moodle_one_time_subscription = get_post_meta( $post_id, '_moodle_one_time_subscription', true );
        $custom_repeater_item = get_post_meta( $post_id, 'moodle_course_section_fields', true );
        $key = array_search($course_calendar_data, array_column($custom_repeater_item,'course_start'));
        $calendar_arr = $custom_repeater_item[$key];
        $directfrommoodle = array_column(!is_array($custom_repeater_item) ? []: $custom_repeater_item, 'directfrommoodle');
        $title = 'Calendar Details';
        $calendar_data = array();
        if(empty($usertimezone)){
        	$usertimezone = getRemoteUserTimeZone();
        }
        if(sizeof($directfrommoodle)>0){
            $sectiondata=[];
            $sectiondata = $custom_repeater_item[$section_count];
            if(!empty($sectiondata)){
                usort($sectiondata['allmetings'], function ($a, $b) {
                    return $a['starttime'] - $b['starttime'];
                });
                    foreach ($sectiondata['allmetings'] as $value2) {
                    	$meeting = array();
                    	$meeting['starttime'] = $value2['starttime'];
                    	$meeting['endtime'] = $value2['starttime']+$value2['duration'];
                    	if (!empty($meeting['starttime'])) {                  
                            $meeting['starttime'] = dateInTimeZone(date("F d, Y g:i A",$meeting['starttime']), "F d, Y g:i A", "g:i", $usertimezone);
                        }
                        if (!empty($meeting['endtime'])) {
                            $meeting['endtime'] = dateInTimeZone(date("F d, Y g:i A",$meeting['endtime']), "F d, Y g:i A", "g:i A", $usertimezone);
                        }

                        $date = date("D M j, Y",$value2["starttime"]);
                       // $calendar_data[] = '<li class="d-flex mb-2 details"><span class="date"><i class="fa fa-calendar mr-2" aria-hidden="true"></i>'.$date.'</span><span class="time ml-2">'.date('h:i A',$value2["starttime"]).' - '.date('h:i A',($value2["starttime"]+$value2['duration'])).'</span></li>';

						$calendar_data[] = '<li class="d-flex mb-2 details"><span class="date"><i class="fa fa-calendar mr-2" aria-hidden="true"></i>'.$date.'</span><span class="time ml-2">'.$meeting['starttime'].' - '.$meeting['endtime'].'</span></li>';
    
                    }
            }
        } else {
            $start_date = $calendar_arr['course_length_from_date'];
            $end_date = $calendar_arr['course_length_to_date'];
            $course_from_time = $calendar_arr['course_from_time'];
            $course_to_time = $calendar_arr['course_to_time'];
            $course_weeks_off = $calendar_arr['course_weeks_off'];
    
            $weeks_days = array_map(function ($day) use ($course_from_time, $usertimezone) {
                return strtolower(dateInTimeZone("$day $course_from_time", "l h:i A", "l", $usertimezone));
            }, $calendar_arr['course_classes']);
            if (!empty($course_from_time)) {
                $course_from_time = dateInTimeZone($course_from_time, "h:i A", null, $usertimezone);
            }
            if (!empty($course_to_time)) {
                $course_to_time = dateInTimeZone($course_to_time, "h:i A", null, $usertimezone);
            }
            if ($moodle_one_time_subscription == '0' || empty($moodle_one_time_subscription)) {
                $dates = days_in_interval($start_date, $end_date, $weeks_days, $usertimezone);
                if ($_product->is_type('subscription')) {
                    $dates = remove_old_dates($dates);
                }
                $weeks_off = weeks_off_calendar($course_weeks_off, $dates);
                foreach ($weeks_off as $date) {
                    $result_date = '<li class="d-flex mb-2 details"><span class="date"><i class="fa fa-calendar mr-2" aria-hidden="true"></i>' . $date . '</span>';
                    $result_time = '<span class="time ml-2">' . $course_from_time . ' - ' . $course_to_time . '</span></li>';
                    $calendar_data[] = $result_date . $result_time;
                }
            }else{
                $date = date("D M j, Y",strtotime($course_calendar_data));
                $result_date = '<li class="d-flex mb-2 details"><span class="date"><i class="fa fa-calendar mr-2" aria-hidden="true"></i>' . $date . '</span>';
                $result_time = '<span class="time ml-2">' . $course_from_time . ' - ' . $course_to_time . '</span></li>';
                $calendar_data[] = $result_date . $result_time;
            }
        }
        $modal_dialog = '';
        $calendar_modal = '<div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul>
                                    '.implode('',$calendar_data).'
                                    </ul>
                                </div>
                            </div>
                        </div>';

        echo json_encode(array('success' => true, 'message' => 'Successfull', 'result' => array('calendar_title' => $title, 'calendar_content' => $calendar_modal, 'weeks_off' => $moodle_one_time_subscription, 'calendar_data' => $calendar_data)));
    } else{
        echo json_encode(array('success' => false, 'message' => 'Not Successfull', 'result' => 'Something Went Wrong.'));
    }
    die();
}
add_action( 'wp_ajax_course_calendar_modal_show', 'course_calendar_modal_show' );
add_action( 'wp_ajax_nopriv_course_calendar_modal_show', 'course_calendar_modal_show' );

/* Count course length weeks */
function course_length_weeks($date_from, $date_to){

    $date_from = date('Y-m-d',strtotime($date_from));
    $date_to = date('Y-m-d',strtotime($date_to));
    $firstDate = new DateTime($date_from);
    $secondDate = new DateTime($date_to);
    $diff_days = $firstDate->diff($secondDate)->days;
    $diff_weeks = $diff_days / 7;
    $total_weeks = ceil($diff_weeks);
    if ($total_weeks == 0){
        $return = '';
    }elseif ($total_weeks == 1){
        $return = $total_weeks . ' Week';
    }else{
        $return = $total_weeks . ' Weeks';
    }
    return $return;
}

/* Count days interval for calendar */
function days_in_interval($start_date, $end_date,$week_days = array(), $timezone){
    $timezone = new DateTimeZone($timezone);
    $start = DateTime::createFromFormat('M j, Y',$start_date, $timezone);
    $end = DateTime::createFromFormat('M j, Y',$end_date, $timezone);
    $dates = [];
    $count = 1000;

    while ($start->diff($end)->days > 0 && !empty($count)) {
        if (in_array(strtolower($start->format('l')), $week_days)) {
            $dates[] = $start->format('D M j, Y');
        }
        $start->add(DateInterval::createFromDateString('1 day'));
        $count--;
    }
    return $dates;
}

/* My courses redirect notice */
function pt_custom_notice() {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $components = $url['query'];
    if(!empty($components) && $components == 'ref'){
        wc_add_notice( 'Sorry! You have not enrolled in a course yet.', 'error' );
    }
}
add_action( 'woocommerce_init', 'pt_custom_notice' );

function getRemoteUserTimeZone() {
    $servertimezone = 'America/New_York';

    static $timezone = null;
    if (empty($timezone) && isset($_COOKIE['wc_tz']) && in_array($_COOKIE['wc_tz'], DateTimeZone::listIdentifiers())) {
        $timezone = $_COOKIE['wc_tz'];
    }
    if (!empty($timezone)) {
        return $timezone;
    }
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    $userip = $ip;
                    break 2;
                }
            }
        }
    }

    if (!empty($userip)) {
        $usertimezonearr = @unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip={$userip}"));
        if (is_array($usertimezonearr) && in_array($usertimezonearr['geoplugin_timezone'], DateTimeZone::listIdentifiers())) {
            $timezone = $usertimezonearr['geoplugin_timezone'];
        }
    }
    if (empty($timezone)) {
        $timezone = $servertimezone;
    }
    return $timezone;
}

function dateInTimeZone($date, $dateformat, $convertformat = null, $timezone = null) {
    date_default_timezone_set( 'America/New_York' );
    $servertimezone = 'America/New_York';
    if (empty($timezone)) {
        $timezone = $servertimezone;
    }
    if (empty($convertformat)) {
        $convertformat = $dateformat;
    }
    $origdate = date_create_from_format($dateformat, $date, new DateTimeZone($servertimezone));
    $origdate->setTimezone(new DateTimeZone($timezone));
    return $origdate->format($convertformat);
}

/* User register save password */
function user_registration_save_password($user_id,$user_password,$usermeta){
    if (!empty($user_password)){
        update_user_meta($user_id, 'parent_value_'.$user_id, base64_encode($user_password));
    }
}
add_action('user_registration','user_registration_save_password', 10, 3);
add_action('bp_core_signup_user','user_registration_save_password', 10, 3);

/* Add submenu for lemonsaidintegration */
function add_submenu_lemonsaidintegration(){
    add_submenu_page(
        'lemonsaidintegration',
        'Link Teacher',
        'Link Teacher',
        'manage_options',
        'link-teacher',
        'link_teacher_callback'
    );
}
add_action('admin_menu','add_submenu_lemonsaidintegration');

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

add_action( 'wp_login', 'woofc_get_cart_after_login', 99, 2 );
function woofc_get_cart_after_login( $user_login, $user ) {
    $saved_cart = get_user_meta( $user->ID, '_woocommerce_persistent_cart_' . get_current_blog_id(), true ) ?: ['cart' => []];
    $cart       = WC()->session->get( 'cart', null );
    $merge_cart = array_merge( $cart, $saved_cart['cart'] );

    if ( ! empty( $merge_cart ) ) {
        $saved_cart['cart'] = $merge_cart;
        update_user_meta( $user->ID, '_woocommerce_persistent_cart_' . get_current_blog_id(), $saved_cart );
    }
}

function link_teacher_callback(){
    $user_args = array(
        'order' => 'ASC',
        'orderby' => 'user_nicename',
        'role__in' => array('instructor','administrator')
    );
    $user_query = new WP_User_Query($user_args);
    $user_results = $user_query->get_results();
    if (!empty($_POST['userid']) && !empty( $_POST['teacher_nonce'] ) && wp_verify_nonce( $_POST['teacher_nonce'], 'teacher_link_nonce' ) ){
        $teacherUser = get_user_by('id', $_POST['userid']);
        $result = moodleservice('getset_user', [
            'username' => $teacherUser->user_login,
            'firstname' => $teacherUser->first_name,
            'lastname' => $teacherUser->last_name,
            'email' => $teacherUser->user_email,
            'password' => generateRandomString(8),
        ]);
        if (!is_wp_error($result)) {
            update_user_meta($teacherUser->ID, 'moodle_userid', $result);
            $redirect = add_query_arg('action', 'success', home_url('/wp-admin/admin.php?page=link-teacher'));
            wp_redirect($redirect);
        } else {
            $redirect = add_query_arg('action', 'not-success', home_url('/wp-admin/admin.php?page=link-teacher'));
            wp_redirect($redirect);
        }
    }
    ?>
    <div class="wrap"><h2>Link Teacher With Moodle</h2><div class="teacher-msg"></div>
        <div class="metabox-holder" id="poststuff">
            <?php if (filter_input(INPUT_GET, 'action') === 'success') { ?>
                <div class="notice notice-success is-dismissible"><p>Account linked.</p></div>
            <?php } elseif (filter_input(INPUT_GET, 'action') === 'not-success') { ?>
                <div class="notice notice-error is-dismissible"><p>There is error linking account.</p></div>
            <?php } ?>
            <div id="post-body">
                <div id="post-body-content">
                    <div class="postbox">
                        <div class="inside">
                            <form method="post">
                                <?php wp_nonce_field( 'teacher_link_nonce', 'teacher_nonce' ); ?>
                                <table cellspacing="2" cellpadding="5" style="width: 50%;" class="form-table">
                                    <thead>
                                    <tr>
                                        <th class="teacher-name">Teacher Name</th>
                                        <th class="teacher-action">Action</th>
                                    </tr>
                                    </thead>
                                    <?php foreach ($user_results as $user) :
                                    $teacher_value = metadata_exists('user', $user->ID,'moodle_userid');
                                    if (!$teacher_value){
                                        $action = '<button class="teacher-edit button-primary" name="userid" value=' . $user->ID . '>Link Teacher</button>';
                                    }else{
                                        $action = 'Already Linked With Moodle';
                                    }
                                    ?>
                                    <tbody>
                                    <tr class="teacher-detail form-field">
                                        <td class="teacher-name"><?php echo $user->first_name." ".$user->last_name; ?></td>
                                        <td class="teacher-action"><?php echo $action; ?></td>
                                    </tr>
                                    </tbody>
                                    <?php endforeach; ?>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/* Weeksoff from the calendar */
function weeks_off_calendar($weeks_off, $dates){
    $course_weeks_off = explode(', ',$weeks_off[0]);
    $old_date = array();
    $even = array();
    $odd = array();
    foreach ($dates as $date){
        $old_date[] = date("M-d-Y", strtotime($date));
    }
    /*Sushil commented this*/
    // foreach($course_weeks_off as $val1) {
    //     if(intval($val1) % 2 == 0) {
    //         $even[] = $val1;
    //     } else {
    //         $odd[] = $val1;
    //     }
    // }
    foreach($even as $key => $val) {
        $date_all = $val.', '.$odd[$key];
        $remove_date = date("M-d-Y", strtotime($date_all));
        if (($key = array_search($remove_date, $old_date)) !== false) {
            unset($dates[$key]);
        }
    }
    return $dates;
}

/* Remove old dates from the card sections */
function remove_old_dates($dates,$formatted = false): array
{
    foreach ($dates as $key => $date) {
        $all_date = strtotime($date);
        $today = strtotime('today');
        if ($all_date < $today) {
            unset($dates[$key]);
        }
    }
    if ($formatted) {
        $dates = array_map(function ($date) {
            return date('M j, Y', strtotime($date));
        }, $dates);
    }
    return array_values($dates);
}

/* Custom email function for email */
function send_custom_email($to,$subject,$message){
    $image_url = get_theme_root_uri().'/buddyboss-theme-child/framework/assets/images/logo-on-transparent.png';
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $mail_message = '<!DOCTYPE html>
        <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <meta name="x-apple-disable-message-reformatting">
            <style>
                table, td, div, h1, p {
                    font-family: Arial, sans-serif;
                }
            </style>
        </head>
        <body style="margin:0;padding:0;word-spacing:normal;">
        <div role="article" aria-roledescription="email" lang="en" style="text-size-adjust:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">   
            <table role="presentation" style="width:100%;border:none;border-spacing:0;">       
                <tr>
                    <td align="center" style="padding: 30px;">
                        <table
                            style="width:100%;max-width:600px;border:none;border-spacing:0;text-align:left;font-family:Arial,sans-serif;font-size:16px;line-height:24px;color:#2F4141; ">
                            <tr>
                                <td align="center" style="border-bottom: 1px solid;">
                                    <img src="'.$image_url.'" style="max-width: 400px;" alt="Lemons-Aid">
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="background-color: #4caf50;padding: 40px;font-size: 30px;text-align: left;color: #fff;">
                                    Login details
                                </td>
                            </tr>
                        </table>
                        <table
                            style="width:100%;max-width:600px;border:none;border-spacing:0;text-align:left;font-family:Arial,sans-serif;font-size:16px;line-height:24px;color:#2F4141; background-color: #efefef;">
                            <tr>
                                <td align="left" style="padding: 20px">
                                    '.$message.'
                                </td>
                            </tr>
                        </table>               
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>';

    wp_mail($to, $subject, $mail_message, $headers);
}

/* Student age calculate by birthdate */
function get_student_age($student_birth_date): bool
{
    $today_date = date('m-d-Y', strtotime('today'));
    $first_date = DateTime::createFromFormat('m-d-Y', $student_birth_date);
    $second_date = DateTime::createFromFormat('m-d-Y', $today_date);
    $diff_years = $first_date->diff($second_date)->y;
    $return = false;
    if ($diff_years > 13) {
        $return = true;
    }
    return $return;
}

/* Count total weeks */
function get_total_weeks($start, $end, $weeks_off = [], $week_days){

    if (!$week_days) {
        return course_length_weeks($start, $end);
    }

    $totalWeek = 0;
    $course_weeks_off = explode(', ',$weeks_off[0]);

    $remove_date = array();
    foreach($course_weeks_off as $val1) {
        $remove_date[] = date("Y-m-d", strtotime($val1));
    }

    // Get Start date
    $start = new DateTime(date('Y-m-d', strtotime($start)));

    // For First selected date week
    $firstStartDate = $start->format('Y-m-d');
    $firstEndDate = $start->modify('next sunday')->format('Y-m-d');

    $firstWeekPeriods = getDateWeekPeriods($firstStartDate, $firstEndDate);
    $totalWeek = getPeriodsTotalWeek($firstWeekPeriods,$remove_date, $week_days, $totalWeek);
    // End of first selected date week

    // Get start with next monday for first day of week
    $start->modify("next monday");
    $end = new DateTime(date('Y-m-d', strtotime($end)));
    $end->modify('+1 day');

    // For Next selected date week
    $weeklyDates = [];
    while($start < $end){
        $key = $start->format('M-d');
        $weeklyDates[$key] = [
            'start' => $start->format('Y-m-d'),
            'end'   => $start->modify('next sunday')->format('Y-m-d')
        ];
        $start->modify("next monday");
    }

    foreach ($weeklyDates as $wd) {
        $weekPeriod = getDateWeekPeriods($wd['start'], $wd['end']);
        $totalWeek = getPeriodsTotalWeek($weekPeriod,$remove_date, $week_days, $totalWeek);
    }
    // End of end selected date week

    if ($totalWeek <= 0) {
        $return = '';
    } elseif ($totalWeek == 1) {
        $return = $totalWeek .' Week';
    } else {
        $return = $totalWeek .' Weeks';
    }
    return $return;
}

function getDateWeekPeriods($startDate, $endDate) {

    $sDate = new DateTime(date('Y-m-d', strtotime($startDate)));
    $eDate = new DateTime(date('Y-m-d', strtotime($endDate)));

    $datePeriods = new \DatePeriod($sDate, new \DateInterval('P1D'), $eDate);

    return $datePeriods;
}

function getPeriodsTotalWeek($weekPeriods, $remove_date, $week_days, $totalWeek = 0) {
    $newWeekPeriod = array();
    foreach ($weekPeriods as $wdt) {
        $wcd = $wdt->format('Y-m-d');
        $wDay = strtolower($wdt->format('l'));
        if (in_array($wcd, $remove_date)){
            if (!in_array($wDay,$week_days)){
                $newWeekPeriod[] = $wDay;
            }
        } else {
            $newWeekPeriod[] = $wDay;
        }
    }
    $intersection = array_intersect($newWeekPeriod, $week_days);
    if ($intersection) {
        $totalWeek = $totalWeek + 1;
    }
    return $totalWeek;
}

/* Change product details page product meta */
/* Change availability html */
function filter_product_availability( $availability, $product ) {
    $stock_quantity = $product->get_stock_quantity();

    if (is_product()) {
        if ($availability['availability'] == '') {
            $availability['availability'] = 'Seats Available';
        } elseif ($product->is_in_stock() && $stock_quantity > 0) {
            $availability['availability'] = 'Seats Available';
        } elseif (!$product->is_in_stock() && $stock_quantity <= 0) {
            $availability['availability'] = 'Not Available';
        }
    }
    return $availability;
}
add_filter( 'woocommerce_get_availability', 'filter_product_availability', 10, 2 );

/* Change Product categories and tags html in product details page */
function replace_product_text( $translated, $untranslated ) {
    if (is_product() && $translated == 'Specs'){
        $translated = 'Details';
    }
    return $translated;
}
add_filter( 'gettext', 'replace_product_text', 10, 2 );

function replace_product_html( $translation, $single, $plural, $number, $domain ) {
    if ( is_product()) {
        $translation = str_ireplace( 'Category:', 'Product Type:', $translation );
        $translation = str_ireplace( 'Tag:', 'Age Level:', $translation );

        $translation = str_ireplace( 'Categories:', 'Product Type:', $translation );
        $translation = str_ireplace( 'Tags:', 'Age Level:', $translation );
    }
    return $translation;
}
add_filter( 'ngettext', 'replace_product_html', 10, 5 );

/* Remove Breadcrumb from product page */
function custom_breadcrumb( $crumbs, $breadcrumb ) {
    if (is_product()) {
        unset($crumbs);
    }
}
add_filter( 'woocommerce_get_breadcrumb', 'custom_breadcrumb', 20, 2 );

/* Show Modal Popup Product Type */
function product_type_modal_show(){
    $product_cat = $_POST['product_type_category'];
    if (!empty($product_cat)) {
        $category_data = get_term_by( 'name', $product_cat, 'product_cat' );
        echo json_encode(array('success' => true, 'message' => 'Successfull', 'result' => array('product_type_title' => $category_data->name, 'product_type_content' => $category_data->description)));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Not Successfull', 'result' => 'Something Went Wrong.'));
    }
    die();
}
add_action('wp_ajax_product_type_modal_show', 'product_type_modal_show');
add_action('wp_ajax_nopriv_product_type_modal_show', 'product_type_modal_show');

/* Check And Validate Captcha */
function check_captcha(){
    $req_arr = array(
        'secret' => SECRET_KEY,
        'response' => $_POST['recaptcha-response'],
    );
    $recaptcha_url = wp_remote_post(GSITE_VERIFY_URL,['body' => $req_arr]);
    $recaptcha = json_decode($recaptcha_url['body']);
    return $recaptcha->success;
}

/* Check Page */
function check_page(){
    if (!is_page('cart')) {
        remove_filter('authenticate','cartpage_authenticate_login',10,2);
    }
}
add_action('wp','check_page');

/* Authenticate User */
function cartpage_authenticate_login($user,$password){
    //remove_all_filters('wp_authenticate_user');
    remove_all_filters('authenticate');
    return $user;
}
add_filter('wp_authenticate_user','cartpage_authenticate_login',10,2);

/* Do not reduce product quantity on renewal order */
function wcs_do_not_reduce_renewal_stock( $reduce_stock, $order ) {
    if ( function_exists( 'wcs_order_contains_renewal' ) && wcs_order_contains_renewal( $order ) ) {
        $reduce_stock = false;
    } elseif ( class_exists( 'WC_Subscriptions_Renewal_Order' ) && WC_Subscriptions_Renewal_Order::is_renewal( $order ) ) {
        $reduce_stock = false;
    }
    return $reduce_stock;
}
add_filter( 'woocommerce_can_reduce_order_stock', 'wcs_do_not_reduce_renewal_stock', 10, 2 );

/* Subscription start date add to cart */
function custom_add_item_data( $cart_item_data, $product_id, $variation_id ) {
    $data = $_POST;
    $cart = WC()->cart->cart_contents;
    $product = wc_get_product($product_id);
    $product_data = $product->get_meta('moodle_course_section_fields');

    if ($product->is_type('simple') || !$data){
        return $cart_item_data;
    }
    if ($product_data && !empty($cart)) {
        foreach ($cart as $cart_key => $cart_value) {
            if ($cart_value['product_id'] == $product_id) {
                WC()->cart->remove_cart_item($cart_key);
            }
        }
        $cart_item_data['wscsd_start_date'] = $data['cs_start_date'];
    }elseif (empty($cart)){
        $cart_item_data['wscsd_start_date'] = $data['cs_start_date'];
    }
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'custom_add_item_data', 10, 3 );

/* update product stock quantity and set status on course avalibility */
function course_avalibility_update_stock($product_post_id,$c_stock_qty){
    $manage_stock = get_post_meta($product_post_id,'_manage_stock',true);
    $custom_repeater_item = get_post_meta( $product_post_id, 'moodle_course_section_fields', true );
    if ($manage_stock == 'no' || !$custom_repeater_item){
        return;
    }
    if ($c_stock_qty == 0 || empty($c_stock_qty)){
        $status = 'outofstock';
        wp_set_post_terms( $product_post_id, 'outofstock', 'product_visibility', true );
    }else{
        $status = 'instock';
    }
    update_post_meta($product_post_id, '_stock', $c_stock_qty);
    update_post_meta( $product_post_id, '_stock_status', wc_clean( $status ) );
    // wc_delete_product_transients( $post_id ); //optional clear cache of variation product
}

/* check avalibility in course section */
function course_update_avalibility($product_id, $group_id, $qty){
    $manage_stock = get_post_meta($product_id,'_manage_stock',true);
    $custom_repeater_item = get_post_meta( $product_id, 'moodle_course_section_fields', true );
    if ($manage_stock == 'no' || !$custom_repeater_item){
        return;
    }

    $key = array_search($group_id, array_column($custom_repeater_item,'course_groups'));
    $course_arr = $custom_repeater_item[$key]['purchase_count'];
    $total_count = $custom_repeater_item[$key]['total_count'] - $qty;

    $total = $course_arr + $qty;

    foreach ($custom_repeater_item as $k => $item){
        $custom_repeater_item[$key]['purchase_count'] = $total;
        $custom_repeater_item[$key]['total_count'] = $total_count;
    }
        /*Sushil commented this*/
    // update_post_meta($product_id,'moodle_course_section_fields',$custom_repeater_item);

    /*$total_stock = 0;
    foreach ($custom_repeater_item as $k => $value){
        $total_stock += $value['total_stock_count'];
    }*/
    $stock_total = array_column($custom_repeater_item,'total_count');
    $stock_total_count = sum(array_values($stock_total),sizeof($stock_total));
    course_avalibility_update_stock($product_id,$stock_total_count);
}

/* Total sum of array value */
function sum($arr, $n){
    $sum = 0;
    for ($i = 0; $i < $n; $i++) {
        $sum += $arr[$i];
    }
    return $sum;
}

/* set quantity maximum and validation */
function course_quantity_input_args( $args, $product ){
    $product_id = $product->get_id();
    $custom_repeater_item = get_post_meta( $product_id, 'moodle_course_section_fields', true );
    foreach (WC()->cart->get_cart_contents() as $cart_key => $cart_item){
        if(is_array($custom_repeater_item)){
            $item = array_search($cart_item['product_group_id'], array_column($custom_repeater_item,'course_groups'));
            $course_arr = $custom_repeater_item[$item]['total_count'];
            if ((int)$course_arr < $cart_item['quantity'] ){
                return $args;
            }else{
                $args['max_value'] = (int)$course_arr;
                $args['step']       = 1;
            }
        }
    }
    return $args;
}
add_filter( 'woocommerce_quantity_input_args', 'course_quantity_input_args', 10, 2 );
/**
 * Custom user profile fields.
 *
 * @param $user
 * @author Webkul
 */
function wk_custom_user_profile_fields( $user ) {
    echo '<h3 class="heading">Custom Fields</h3>';
    ?>
    <table class="form-table">
        <tr>
            <th><label for="moodle_userid">Moodle Userid</label></th>
            <td>
                <input type="text" name="moodle_userid" id="moodle_userid" value="<?php echo esc_attr( get_the_author_meta( 'moodle_userid', $user->ID ) ); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}
 
add_action( 'show_user_profile', 'wk_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'wk_custom_user_profile_fields' );
