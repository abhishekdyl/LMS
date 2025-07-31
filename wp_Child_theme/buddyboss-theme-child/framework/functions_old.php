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
            <div class="student-heading" style="display: flex;justify-content: center;"><h2>Student De