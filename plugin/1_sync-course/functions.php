<?php
// create thanku page
class PageTemplater {

    /**
     * A reference to an instance of this class.
     */
    private static $instance;

    /**
     * The array of templates that this plugin tracks.
     */
    protected $templates;

    /**
     * Returns an instance of this class. 
     */
    public static function get_instance() {

        if (null == self::$instance) {
            self::$instance = new PageTemplater();
        }

        return self::$instance;
    }

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    private function __construct() {

        $this->templates = array();


        // Add a filter to the attributes metabox to inject template into the cache.
        if (version_compare(floatval(get_bloginfo('version')), '4.7', '<')) {

            // 4.6 and older
            add_filter(
                    'page_attributes_dropdown_pages_args', array($this, 'register_project_templates')
            );
        } else {

            // Add a filter to the wp 4.7 version attributes metabox
            add_filter(
                    'theme_page_templates', array($this, 'add_new_template')
            );
        }

        // Add a filter to the save post to inject out template into the page cache
        add_filter(
                'wp_insert_post_data', array($this, 'register_project_templates')
        );


        // Add a filter to the template include to determine if the page has our 
        // template assigned and return it's path
        add_filter(
                'template_include', array($this, 'view_project_template')
        );


        // Add your templates to this array.
        $this->templates = array(
            'thankyou.php' => 'Thank You',
            'moodlelogin.php' => 'moodle login',
            'user_login.php' => 'User Login', 
            'student-admission-form.php' => 'Students Admission Form',
            'course_temp.php' => 'Course Template',
            'application_form.php' => 'Application Form',
            'registration_form.php' => 'Registration Form',
            'course-card.php' => 'Course card',
            'samester.php' => 'Samester',
            'generate-invoice-template-multiple.php' => 'Generate Invoice Template Multiple',
            'department-list-template.php' => 'Department List Template',
            'home-page-tempate.php' => 'Home Page Template',
            'digital-litracy-test.php' => 'Digital Litracy Test',
            'invoice-template.php' => 'Invoice template',
            'quiz-attempt-instrcution.php' => 'Quiz attempt instrcution',
            'invoice-upload-template.php' => 'Invoice Upload Template',
            'generate-invoice-template.php' => 'generate invoice Template',
            'resset-password-template.php' => 'reset password template',
            'device-requirement.php' => 'Digital Device Requirement',

        );
    }

    /**
     * Adds our template to the page dropdown for v4.7+
     *
     */
    public function add_new_template($posts_templates) {
        $posts_templates = array_merge($posts_templates, $this->templates);
        return $posts_templates;
    }

    /**
     * Adds our template to the pages cache in order to trick WordPress
     * into thinking the template file exists where it doens't really exist.
     */
    public function register_project_templates($atts) {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

        // Retrieve the cache list. 
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if (empty($templates)) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete($cache_key, 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge($templates, $this->templates);

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add($cache_key, $templates, 'themes', 1800);

        return $atts;
    }

    /**
     * Checks if the template is assigned to the page
     */
    public function view_project_template($template) {

        // Get global post
        global $post;

        // Return template if post is empty
        if (!$post) {
            return $template;
        }

        // Return default template if we don't have a custom one defined
        if (!isset($this->templates[get_post_meta(
                                $post->ID, '_wp_page_template', true
                )])) {
            return $template;
        }

        $file = plugin_dir_path(__FILE__) . get_post_meta(
                        $post->ID, '_wp_page_template', true
        );

        // Just to be safe, we check if the file exist first
        if (file_exists($file)) {
            return $file;
        } else {
            echo $file;
        }

        // Return template
        return $template;
    }

}

add_action('plugins_loaded', array('PageTemplater', 'get_instance'));



// function for call thanku page 

add_action('template_redirect', 'wc_custom_redirect_after_purchase');
function wc_custom_redirect_after_purchase() {
    global $wp;

    if (is_checkout() && !empty($wp->query_vars['order-received'])) {
        $order_id = absint($wp->query_vars['order-received']);
        $order_key = wc_clean($_GET['key']);
        /**
         * Replace {PAGE_ID} with the ID of your page
         */
		 
		 // get page id
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'thankyou.php'
        ));
        foreach ($pages as $page) {
             $page->ID;
        }
         $redirect = get_permalink($page->ID);
         $redirect;

        $redirect .= get_option('permalink_structure') === '' ? '&' : '?';
        $redirect .= 'order=' . $order_id . '&key=' . $order_key;
        wp_redirect($redirect);
        exit;
    } 
}

// remove woocommrce form fields
add_filter( 'woocommerce_checkout_fields' , 'remove_woocommerce_fields_name' );

function remove_woocommerce_fields_name( $fields ) {
     unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_city']);
     unset($fields['billing']['billing_address_1']);
     unset($fields['billing']['billing_postcode']);
     unset($fields['billing']['billing_phone']);
     unset($fields['billing']['billing_address_2']);
     unset($fields['billing']['billing_address_2']);
     //unset($fields ['account']['account_password']);
     return $fields;
}

/*add_filter( 'woocommerce_default_address_fields' , 'set_custom_company_checkout_field' );
function set_custom_company_checkout_field( $address_fields ) {
    global $wpdb;
    $select_options = array();
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";

    unset($fields['billing']['billing_company']);

    unset($fields['first_name']['type']);
    $address_fields['first_name']['type'] = 'text';
    $address_fields['first_name']['default'] = 'rrrr';

    return $address_fields;
}*/

// set woocommrce form fields value
add_filter( 'woocommerce_default_address_fields' , 'set_custom_company_checkout_field' );
function set_custom_company_checkout_field( $address_fields ) {
    global $wpdb,$wp_session; 
    ob_start();
    session_start();
   // echo "<pre>";

    //print_r($_SESSION);
   // echo "</pre>";

    $multistepform_id =$_SESSION['one_planet']['multistepform_id'];
    $question_status_id =$_SESSION['one_planet']['question_status_id'];
    $get_userinfo = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}multistepform WHERE question_status_id={$question_status_id}");
    $userdata = unserialize($get_userinfo->post_data);
   /* echo "<pre>";
    print_r($userdata);
    echo "</pre>";*/
    //echo $userdata['fname'];

    $address_fields['first_name']['default'] = $userdata['fname'];
    $address_fields['last_name']['default'] = $userdata['lname'];

    //$address_fields['billing_email']['default'] = $userdata['e_mail'];
  /*  unset($address_fields['billing']['billing_email']);
    $address_fields['email']['type'] = 'text';
    $address_fields['email']['default'] = $userdata['e_mail'];*/

    // unset($fields['billing']['billing_company']);
    //$address_fields['company']['default'] = $userdata['po_box'];

    // unset($fields['billing']['billing_postcode']);
   /* echo "<pre>";
    print_r($address_fields);
    echo "</pre>";
    $address_fields['postcode']['default'] = "111111";*/
    //$address_fields['billing']['billing_postcode']['default'] = $userdata['po_box'];
   // $address_fields['billing']['billing_city']['default'] = $userdata['city'];
   

    return $address_fields;
} 

/*add_filter( 'woocommerce_checkout_fields', 'bbloomer_set_checkout_field_input_value_default' );
 
function bbloomer_set_checkout_field_input_value_default($fields) {
    $fields['billing']['billing_city']['default'] = 'Kadapa';
    $fields['billing']['billing_state']['default'] = 'Andhra Pradesh';
    return $fields;
}*/




/*add_filter( 'woocommerce_checkout_fields', 'bbloomer_set_checkout_field_input_value_default' );
 
function bbloomer_set_checkout_field_input_value_default($fields) {
    $fields['billing']['billing_city']['default'] = 'Kadapa';
    $fields['billing']['billing_state']['default'] = 'Andhra Pradesh';
    return $fields;
}*/

//====================add product restriction  ===========================//
function check_if_cart_has_product( $valid, $product_id, $quantity ) {  

    if(!empty(WC()->cart->get_cart()) && $valid){
        foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
            $_product = $values['data'];

            if( $product_id == $_product->id ) {
                wc_add_notice( 'The product is already in cart', 'error' );
                return false;
            }
        }
    }

    return $valid;

}
add_filter( 'woocommerce_add_to_cart_validation', 'check_if_cart_has_product', 10, 3 ); 


// add multypule product in card
function webroom_add_multiple_products_to_cart( $url = false ) {
    // Make sure WC is installed, and add-to-cart qauery arg exists, and contains at least one comma.
    if ( ! class_exists( 'WC_Form_Handler' ) || empty( $_REQUEST['add-to-cart'] ) || false === strpos( $_REQUEST['add-to-cart'], ',' ) ) {
        return;
    }

    // Remove WooCommerce's hook, as it's useless (doesn't handle multiple products).
    remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );

    $product_ids = explode( ',', $_REQUEST['add-to-cart'] );
    $count       = count( $product_ids );
    $number      = 0;

    foreach ( $product_ids as $id_and_quantity ) {
        // Check for quantities defined in curie notation (<product_id>:<product_quantity>)
        
        $id_and_quantity = explode( ':', $id_and_quantity );
        $product_id = $id_and_quantity[0];

        $_REQUEST['quantity'] = ! empty( $id_and_quantity[1] ) ? absint( $id_and_quantity[1] ) : 1;

        if ( ++$number === $count ) {
            // Ok, final item, let's send it back to woocommerce's add_to_cart_action method for handling.
            $_REQUEST['add-to-cart'] = $product_id;

            return WC_Form_Handler::add_to_cart_action( $url );
        }

        $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
        $was_added_to_cart = false;
        $adding_to_cart    = wc_get_product( $product_id );

        if ( ! $adding_to_cart ) {
            continue;
        }

        $add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->get_type(), $adding_to_cart );

        // Variable product handling
        if ( 'variable' === $add_to_cart_handler ) {
            woo_hack_invoke_private_method( 'WC_Form_Handler', 'add_to_cart_handler_variable', $product_id );

        // Grouped Products
        } elseif ( 'grouped' === $add_to_cart_handler ) {
            woo_hack_invoke_private_method( 'WC_Form_Handler', 'add_to_cart_handler_grouped', $product_id );

        // Custom Handler
        } elseif ( has_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler ) ){
            do_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler, $url );

        // Simple Products
        } else {
            woo_hack_invoke_private_method( 'WC_Form_Handler', 'add_to_cart_handler_simple', $product_id );
        }
    }
}

// Fire before the WC_Form_Handler::add_to_cart_action callback.
add_action( 'wp_loaded', 'webroom_add_multiple_products_to_cart', 15 );


/**
 * Invoke class private method
 *
 * @since   0.1.0
 *
 * @param   string $class_name
 * @param   string $methodName
 *
 * @return  mixed
 */
function woo_hack_invoke_private_method( $class_name, $methodName ) {
    if ( version_compare( phpversion(), '5.3', '<' ) ) {
        throw new Exception( 'PHP version does not support ReflectionClass::setAccessible()', __LINE__ );
    }

    $args = func_get_args();
    unset( $args[0], $args[1] );
    $reflection = new ReflectionClass( $class_name );
    $method = $reflection->getMethod( $methodName );
    $method->setAccessible( true );

    //$args = array_merge( array( $class_name ), $args );
    $args = array_merge( array( $reflection ), $args );
    return call_user_func_array( array( $method, 'invoke' ), $args );
}

//end
//desable woocommrce card option
add_filter( 'woocommerce_cart_item_quantity', 'wc_cart_item_quantity', 10, 3 );

function wc_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ){

    if( is_cart() ){

        $product_quantity = sprintf( '%2$s <input type="hidden" name="cart[%1$s][qty]" value="%2$s" />', $cart_item_key, $cart_item['quantity'] );

    }

    return $product_quantity;

}
//change proceed to checkout text

function woocommerce_button_proceed_to_checkout() {
   
   $new_checkout_url = WC()->cart->get_checkout_url();
   ?>
   <a href="<?php echo $new_checkout_url; ?>" class="checkout-button button alt wc-forward">
   <?php _e( 'Fee Payment', 'woocommerce' ); ?></a>   
<?php
}
// add addition css in header and footer

function addcustom_footer() {
    
}

add_action( 'wp_footer', 'addcustom_footer' );
function addcustom_head() { 
    global $woocommerce,$wp;
    $cart_page_url = $woocommerce->cart->get_cart_url(); 
    $current_url = add_query_arg( $wp->query_vars, home_url( $wp->request.'/'));
    if($cart_page_url==$current_url){
        echo "<style>
            td.product-remove a.remove {
                display: none;
            }
        </style>";

    }

    // add CSS for home page
    $homeurl = home_url($wp->request) . "/";
    if($homeurl==$current_url){
        echo "<style>
            #masthead {
                margin-bottom: 0px !important;
            }

            #elementor-custom-botttom-section a.hfe-menu-item {
                color: #fff;
            }

            #elementor-custom-overlay {
                height: 100%;
                width: 100%;
            }

            .image-section .elementor-widget-wrap {
                min-height: 75vh;
            }

            #elementor-custom-scholarship .image-divider {
                position: absolute;
                left: 45%;
                z-index: 1;
                height: 75vh;
                width: 200px;
            }

            .elementor-custom-right-content {
                transform: translate(-100px, 0px);
            }

            .elemantor-custom-left-content {
                align-items: center;
                transform: translate(100px, 0px);
            }

            #elemantor-custom-logo {
                transform: translate(0px, -100px);
                display: block;
            }

            #elemantor-custom-logo img {
                width: 200px;
                max-width: 200px;
            }

            #elementor-custom-botttom-section.she-header #elemantor-custom-logo {
                transform: unset;
            }

            #elementor-custom-botttom-section.she-header .elementor-widget-wrap {
                padding-top: 10px;
            }

            #elementor-custom-botttom-section.she-header #elemantor-custom-logo img {
                width: 100px;
                max-width: 100px;
            }

            #elementor-custom-botttom-section.she-header .elementor-column {
                align-items: center;
            }

            #elementor-custom-nav-color-logo {
                display: none;
            }

            #elementor-custom-nav-white-logo {
                display: block !important;
            }
             .hfe-nav-menu-icon {
                color: #ffffff !important;
            }

            /* #elementor-custom-navigation  */
            @media (max-width: 1024px) and (min-width: 768px) {

                .elemantor-custom-left-content,
                .elementor-custom-right-content {
                    transform: unset;
                }
            }

            @media (max-width: 767px) {

                .elemantor-custom-left-content,
                .elementor-custom-right-content {
                    transform: unset;
                }

                #elementor-custom-botttom-section {
                    padding: 0px 10px !important;
                }
            }
        </style>";
    }







    ?>
        
    <?php
}
add_action('wp_head', 'addcustom_head');

function add_content_after_header() {
  echo '<div class="tg-container">';
};
//add_action ('__after_header' , 'add_content_after_header', 20);
add_action( 'loop_start', 'add_content_after_header', 1 );

?>