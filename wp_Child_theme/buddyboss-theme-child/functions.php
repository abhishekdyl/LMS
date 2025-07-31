<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'buddyboss-theme', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
  // load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'buddyboss_theme_child_languages' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  // Styles
  wp_enqueue_style( 'buddyboss-child-css', get_stylesheet_directory_uri().'/assets/css/custom.css' );

  // Javascript
  wp_enqueue_script( 'buddyboss-child-js', get_stylesheet_directory_uri().'/assets/js/custom.js' );
}
add_action( 'wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here

/* Add Custom Function File */
require_once locate_template( '/framework/functions.php' );

add_filter( 'gettext', 'bbloomer_translate_tag_taxonomy', 9999, 5 );

function bbloomer_translate_tag_taxonomy( $translated, $untranslated, $domain ) {
   if ( is_product() && 'woocommerce' === $domain ) {
     switch ( $translated ) {

        case 'Tags:':

           $translated = 'Subject Area';
           break;

        case 'Category':

           $translated = 'Product Type';
           break;

        // ETC

     }
   }
   return $translated;
}

function product_imagetab_fix(){ ?>

  <script type="text/javascript">
  (function($) {
  	$('.wc-tabs li a').each(function() {
  		$(this).toggleClass('testing');
  		$(this).on("click", function() {
  		$(this).parents('.woocommerce-tabs').trigger('resize');
  	});
  	});

  })(jQuery);
  </script>

<?php }

add_action('wp_footer', 'product_imagetab_fix');


function bb_custom_login_styles() { ?>
    <style type="text/css">
        .login-split__entry h1 {
          font-family: 'Montserrat';
          font-weight: bold;
          font-size: 1.8rem;
          text-align: center !important;
          margin-bottom: 30px;
          text-transform: uppercase;
          letter-spacing: 1px;
        }
        .login-split__entry {
          text-align: center;
          font-family: 'Ruluko';
          font-weight: bold;
          font-size: 1.6rem;
          min-height: 300px;
        }
        body.login.login-split-page .login-split__entry p {
          font-size: 1.6rem!important;
        }
        .login-split__entry h3 {
          color: #fff;
          font-family: 'Ruluko';
          font-size: 2.6rem;
          line-height: 1;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'bb_custom_login_styles' );


function display_moodle_calendar() {
// Moodle API endpoint URL
$apiEndpoint = 'https://classrooms.lemons-aid.com/webservice/rest/server.php';

// API token for authentication
// $token = '8100c9482ec47c7d5a21588deedb1322';
   $token = 'b68d8fd89fad728e7b73e604ec8507df';

// Get the email of the currently logged-in WordPress user
$current_user = wp_get_current_user();
$email = $current_user->user_email;

// Parameters for the API request
$params = [
    'wstoken' => $token,
    'moodlewsrestformat' => 'json',
    'wsfunction' => 'core_user_get_users',
    'criteria' => [
        [
            'key' => 'email',
            'value' => $email
        ]
    ]
];

// Make the API request
$response = file_get_contents($apiEndpoint . '?' . http_build_query($params));

// Check if the request was successful
if ($response !== false) {
    // Parse the response JSON
    $data = json_decode($response, true);

    // Check if the response contains any errors
    if (isset($data['exception']) || isset($data['errorcode'])) {
        // Handle the error
        echo 'API Error: ' . $data['message'];
    } else {
        // User data is available in $data['users']
        // Process the user information as needed
        $users = $data['users'];
        foreach ($users as $user) {
            echo 'User ID: ' . $user['id'] . '<br>';
            echo 'Username: ' . $user['username'] . '<br>';
            echo 'Email: ' . $user['email'] . '<br>';
            // ... Process other user data
        }
    }
} else {
    // Handle the request error
    echo 'Error: Failed to connect to the API endpoint.';
}
}

add_action('dokan_product_edit_after_main', 'display_moodle_calendar');


add_action('wp_logout','bb_redirect_after_logout');
function bb_redirect_after_logout(){
         wp_redirect( '/wp-login.php' );
         exit();
}
add_action( 'profile_update', 'my_profile_update', 10, 2 );

function my_profile_update( $user_id, $old_user_data ) {
    $userdata = get_userdata($user_id);
    $usermetadata = get_user_meta($user_id);
    $select_student = unserialize($usermetadata['select_student'][0]);
    if(!empty($select_student)){
      foreach ($select_student as $selectedstudentid) {
        $studentdata = get_user_meta($selectedstudentid);
        $parent_login_id = $studentdata['parent_login_id'][0];
        if(empty($parent_login_id)){
          $updatedparent_login_id = update_user_meta( $selectedstudentid, 'parent_login_id', $user_id);

        }
      }
    }
}

// LDS Custom Function //
function my_extra_author_fields( $user ) { 
  $DOB = esc_attr( get_user_meta( $user->ID, 'student_birth_date' , true ));
  $finaldob = "";
  if(!empty($DOB)){
    $date = DateTime::createFromFormat('m-d-Y', $DOB);
    if($date){
        $finaldob = date("Y-m-d", $date->format('U'));    
    }
  }
?>
    <h3>Student Birth Date</h3>

    <table class="form-table">
        <tr>
            <th><label for="fstudent_birth_date">student birth date</label></th>
            <td>
                <input type="date" name="student_birth_date" id="student_birth_date" class="regular-text" value="<?php echo $finaldob; ?>" />
                <br />
                <span class="description">Please enter your Date Of birth</span>
            </td>
        </tr>
    </table>
<?php }

add_action( 'show_user_profile', 'my_extra_author_fields' );
add_action( 'edit_user_profile', 'my_extra_author_fields' );


function save_my_extra_author_fields( $user_id ) {
  $finaldob = "";
  $finaldobextend = "";
  if(!empty($_POST['student_birth_date'])){
      $date = DateTime::createFromFormat('Y-m-d', $_POST['student_birth_date']);
      if($date){
          $finaldob = date("m-d-Y", $date->format('U'));    
          $finaldobextend = date("Y-m-d h:i:s", $date->format('U'));    
      }
  }
  // Check to see if user can edit this profile
  if ( ! current_user_can( 'user_id', $user_id ) )
    return false;
  custom_updateuserdate($user_id, $finaldob, $finaldobextend);
}

function custom_updateuserdate($user_id, $finaldob="", $finaldobextend="") {
  global $wpdb;
  update_user_meta( $user_id, 'student_birth_date', $finaldob);
  try {
    $bpdob= $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "bp_xprofile_fields where name =  'Birthdate '");
    if($bpdob){
      $getbpdob = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "bp_xprofile_data where field_id =  '".$bpdob->id."' AND user_id='$user_id'");
      if(!empty($getbpdob)){
         $updateRow = $wpdb->update($wpdb->prefix ."bp_xprofile_data" , array("value" => $finaldobextend,"user_id"=>$user_id) ,array("id" =>$getbpdob->id));
      }else{
        $table_name = $wpdb->prefix . "bp_xprofile_data";
        $my_bpdob = array(
          'field_id'=>$bpdob->id, 
          'user_id'=>$user_id, 
          'value' =>  $finaldobextend,
          'last_updated' => date("Y-m-d h:i:s"),
          );
          $user_bpdob = $wpdb->insert($table_name,$my_bpdob);
       }
     }
  } catch (Exception $e) {
    
  }
}
add_action( 'personal_options_update', 'save_my_extra_author_fields' );
add_action( 'edit_user_profile_update', 'save_my_extra_author_fields' );



// function custom_login_redirect() {

//   return 'home_url()';
// //   return 'https://staging.lemons-aid.com/courses/';
  
// }
// add_filter('login_redirect', 'custom_login_redirect')



//custom function for price overwrite checkout page


