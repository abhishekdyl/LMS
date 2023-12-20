<?php
// create wp thempale page
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
            'add-families-members.php' => 'Add Families Members',
            're-subscription-request.php' => 'Re-Subscription Request',
            'user-subscription-request.php' => 'User Subscription Request',
            'thankyou.php' => 'Thank You',
            'add-children.php' => 'ADD Children',
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

//LDS Custom Function function for overwrite prodct price

function get_yearly_price()
{
    global $wpdb;
    $yrlyprice = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix ."options  WHERE option_name='yearly_package' " );
    $yearly_package=$yrlyprice->option_value;
     echo json_encode(array('price'=>$yearly_package));
     die;
}
add_action('wp_ajax_get_yearly_price', 'get_yearly_price');
add_action('wp_ajax_nopriv_get_yearly_price', 'get_yearly_price');

function get_monthly_price()
{
    global $wpdb;
    $mnthlyprice = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix ."options  WHERE option_name='monthly_package'" );
     //print_r($mnthlyprice);
    $monthly_package = $mnthlyprice->option_value; 
    echo json_encode(array('price'=>$monthly_package));
    die;
}

add_action('wp_ajax_get_monthly_price', 'get_monthly_price');
add_action('wp_ajax_nopriv_get_monthly_price', 'get_monthly_price');

function trail_buy(){
   global $wpdb;
    $uid            = $_POST['uid'];
    $type           = $_POST['get_type'];
    $num_of_stu     = $_POST['num_of_student'];
    $month_price    = $_POST['month_price'];
    $year_price     = $_POST['year_price'];
    $username       = sanitize_user($_POST['username']);
    $email          = sanitize_email($_POST['email']);
    $password       = esc_attr($_POST['password']);
    $cpassword      = esc_attr($_POST['cpassword']);
    $fname          = $_POST['fname'];
    $lname          = $_POST['lname'];
    $errors = array();  
    if( username_exists($username) OR empty($username) ) {  
        $errors['message'] = "Username already exists, please try another";  
    }
    elseif(email_exists($email) OR empty($email)) {  
        $errors['message'] = "This email address is already in use";  
    }elseif($password != $cpassword){
        $errors['message'] = "password Are not match";  
    }
    if(0 === count($errors)){
      if($type==1){
        $price = $year_price;
      }else{
        $price = $month_price;
      }
        $userdata = array(
          'user_login'    =>   $username,
          'user_email'    =>   $email,
          'user_pass'     =>   $password,
          'first_name'    =>   $fname,
          'last_name'     =>   $lname,
        );
        $user = wp_insert_user( $userdata );
        if($user){
          $user_password = update_user_meta($user, 'member_password', base64_encode($password));
          $membertab = $wpdb->prefix . "members_info";
          $memberdata = array(
              'members_type'=>'familie',
              'payment_type'=>$type,
              'member_count'=>$num_of_stu,
              'price'=>$price,
              'user_id'=>$user,
              'created_date'=>time()
              );
          if($wpdb->insert($membertab,$memberdata)){
              $member_id =  $wpdb->insert_id;
          }

          wp_clear_auth_cookie();
          wp_set_current_user ($user); // Set the current user detail
          wp_set_auth_cookie  ($user); // Set auth details in cookie
          echo json_encode(array('success' => true,'uid'=>$user,'mid'=>$member_id));
          die;
        }
    } else{
        echo json_encode(array('success' => false, 'message' => $errors));
        die;
    }

}

add_action('wp_ajax_trail_buy', 'trail_buy');
add_action('wp_ajax_nopriv_trail_buy', 'trail_buy');

// Add Custom Product Fields in WooCommerce

// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_members_children_fields');

// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_members_children_fields_save');


function woocommerce_product_members_children_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_members_children_field">';
    woocommerce_wp_text_input(
        array(
            'id' => '_members_children',
            'placeholder' => 'add members',
            'label' => __('Add Members Count', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'max' => '20',
                'min' => '0'
            )
        )
    );
    echo '</div>';

}

function woocommerce_product_members_children_fields_save($post_id)
{
    $woocommerce_members_children = $_POST['_members_children'];
    if (!empty($woocommerce_members_children))
        update_post_meta($post_id, '_members_children', esc_attr($woocommerce_members_children));
}
// END //

//====================add product restriction  ===========================//
function check_if_cart_has_product( $valid, $product_id, $quantity ) {  
  global $wpdb, $woocommerce, $post;
print_r($product_id);
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

//--------------------

add_filter ( 'woocommerce_account_menu_items', 'silva_one_more_link' );
function silva_one_more_link( $menu_links ){
 
  // We will hook "sometext" later
  $new = array( 'sometext' => 'Add Student' );
 
  // Or in case you need 2 links
  // $new = array( 'link1' => 'Link 1', 'link2' => 'Link 2' );
 
  // array_slice() is good when you want to add an element between the other ones
  $menu_links = array_slice( $menu_links, 0, 1, true ) 
  + $new 
  + array_slice( $menu_links, 1, NULL, true );
 
  return $menu_links;
}
 
add_filter( 'woocommerce_get_endpoint_url', 'silva_hook_endpoint', 10, 4 );
function silva_hook_endpoint( $url, $endpoint, $value, $permalink ){
  if( $endpoint === 'sometext' ) {
    // Here is the place for your custom URL, it could be external
    $url = site_url('?page_id=7027');
  }
  return $url;
}