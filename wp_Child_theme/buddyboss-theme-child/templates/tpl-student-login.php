<?php
/*Template Name:  Student Login*/
get_header();

if (is_user_logged_in()): ?>
<a class="login_button" href="<?php echo wp_logout_url(home_url()); ?>">Logout</a>
<?php endif; ?>
<?php
if (!is_user_logged_in()):
?>
<div class="col-12">
    <div class="content-area">
        <section class="gray">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-sm-12">
                        <div class="detail-wrapper">
                            <div class="create_parent_account">
                                <div class="student_form">
                                    <h3>Login</h3>
                                    <form class="student_login" name="student_login" id="student_login" method="post">
                                        <?php wp_nonce_field( 'student_login_nonce', 'student_login_nonce' ); ?>
                                        <div class="student-username">
                                            <label for="username">Username <span class="required">*</span></label>
                                            <br>
                                            <input type="text" class="form-control" name="student_username" id="username" value="" autocomplete="off" required="required"/>
                                        </div>
                                        <div class="student-password">
                                            <label for="password">Password <span class="required">*</span></label>
                                            <br>
                                            <input type="password" class="form-control" name="student_password" id="password" value="" autocomplete="off" required="required"/>
                                        </div>
                                        <input type="hidden" name="action" value="student_login" />
                                        <br>
                                        <input type="submit" class="submit-button" name="submit" value="Login">
                                        <div class="form_message"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
endif;

get_sidebar();
get_footer();