<?php
ob_start();
get_header();
global $wpdb;
wp_enqueue_style('custom', plugin_dir_url(__FILE__) . '/assets/style/custom.css', false, '1.0.0', 'all');
if (isset($_POST['submit'])) {
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $user_signon = wp_signon($info, false);
    if (is_wp_error($user_signon)) {
        echo $message = "invalid user credentials";
    }
    if (!is_wp_error($user_signon)) {
        $id = $user_signon->ID;
        wp_set_current_user($id);
        $url = get_home_url() . '/cart/';
        $message = "Login successful, redirecting...";
        $url = wp_redirect(get_home_url() . '/cart/');
    }
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<div class="tabber-parent">
    <div class="inner-parent">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Creat Account<br> <span>Creat and publish deals</span></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">MEMBER ACCOUNT <br> <span>Access and purchase deals</span></button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane  show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <?php echo '<div style="color: #cc1818; margin-bottom: 9px; font-size:18px; font-weight:bold;">' . $message . '</div>'; ?>
                <form action="" method="post">
                    <label for="username" class="required">USERNAME</label><br>
                    <input type="text" name="username" value="<?php if($_POST['username']){ echo $_POST['username']; }?>" required><br><br>

                    <label for="password" class="required">PASSWORD</label><br>
                    <input type="password" required name="password" <?php if($_POST['password']){ echo $_POST['password']; }?>>

                    <span class="forget-info">Forgot password ?</span>
                    <input id="submit" type="submit" name="submit" value="ENTER" class="btn btn-default">
                    <div class="info-parent">
                        <div class="info">YOU ALREADY HAVE AN ACCOUNT? NO WORRIES, <BR> CLICK HERE TO <a href="https://prepackdeals.co.uk/regitration_form/">SIGN UP.</a>
                        </div>
                    </div>

                </form>
            </div>
            <div class="tab-pane " id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <?php echo '<div style="color: #cc1818; margin-bottom: 9px; font-size:18px; font-weight:bold;">' . $message . '</div>'; ?>
                <!-- <form action="" method="post"> 
                    <label for="username" class="required">USERNAME</label><br>
                    <input type="text" required name="username"><br><br>

                    <label for="password" class="required">PASSWORD</label><br>
                    <input type="password" required name="password">

                    <span class="forget-info">Forgot password ?</span>
                    <input id="submit" type="submit" name="submit" value="ENTER" class="btn btn-default">  
                    <div class="info-parent">
                        <div class="info">YOU ALREADY HAVE AN ACCOUNT? NO WORRIES, <BR> CLICK HERE TO <a href="">SIGN UP.</a>
                        </div>
                    </div>
                        
                </form> -->

            </div>
        </div>

    </div>
</div>


<?php

get_footer();

?>