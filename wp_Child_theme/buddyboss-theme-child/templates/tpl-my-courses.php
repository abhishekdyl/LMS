<?php
/*Template Name:  My Courses*/
global $current_user;
define('DONOTCACHEPAGE', true);
$current_user_meta = get_user_meta($current_user->ID, 'moodle_userid', true);
$redirect_url = get_site_url() . '/courses/';
?>

<?php
    if (!is_user_logged_in() || empty($current_user_meta)){
        nocache_headers();
        @header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other');
        @header('Location: '.$redirect_url.'?ref');
        exit;
        /*echo '<section class="gray"><div class="alert alert-danger mt-5 text-center">Please <a href="'.get_site_url().'/wp-login.php'.'">login</a> first to access this page</div></section>';*/
    }
    /*elseif (empty($current_user_meta)) {
        // echo '<div class="wc-custom-button"><button type="button" class="sso_btn btn btn-md btn-outline-secondary" name="sso">SSO</button><span class="msg_sso"></span></div>';
        echo '<section class="gray"><div class="alert alert-danger mt-5 text-center">Sorry! You have not enrolled in a course yet. Find a course now. <a href="'.get_site_url().'/courses'.'">Browse Courses</a></div></section>';
    }*/
    elseif (!empty($current_user_meta)){
        get_header();
        echo '<section class="gray"><div class="sso_cmsg alert alert-success mt-5 text-center">Please Wait While Redirecting...</div></section>';
    }
?>

<script type="text/javascript">
    window.addEventListener('load', function (e) {
        jQuery.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                action: 'sso_url',
            },
            success: function (response) {
                response = JSON.parse(response);
                if (response.success) {
                    let redirect = window.open(response.ssourl,'_self');
                    if (redirect) {
                        setTimeout(function () {
                            jQuery('.sso_cmsg').hide();
                        }, 2000);
                    }
                } else {
                    jQuery('.sso_cmsg').hide();
                    e.preventDefault();
                }
            }
        });
    });
</script>

<?php
//get_sidebar();
get_footer();