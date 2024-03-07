<?php
/*Template Name:  Student Register*/
get_header();

$current_user_id = wp_get_current_user()->ID;
?>
<div class="col-12">
    <div class="content-area">
        <section class="gray">
            <div class="container">
                <div class="row">
                    <div class="detail-wrapper">
                        <div class="create_parent_account">
                            <div class="student_form">
                                <?php if (!is_user_logged_in()): ?>
                                    <h3>Create an Account</h3>
                                <?php else: ?>
                                    <h3>Create a Student Account</h3>
                                <?php endif; ?>
                                <form class="student_register_login col-md-12 row" name="student_register_login" id="student_login" method="post">
                                    <div class="student-fname col-md-4 form-group">
                                        <label for="first_name">First Name <span class="required">*</span></label>
                                        <input type="text" class="col-md-12 form-control" name="student_fname" id="first_name" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-lname col-md-4 form-group">
                                        <label for="last_name">Last Name <span class="required">*</span></label>
                                        <input type="text" class="col-md-12 form-control" name="student_lname" id="last_name" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-email col-md-4 form-group">
                                        <label for="student_email">Email <span class="required">*</span></label>
                                        <input type="email" class="col-md-12 form-control" name="student_email" id="student_email" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-username col-md-4 form-group">
                                        <label for="username">Username <span class="required">*</span></label>
                                        <input type="text" class="col-md-12 form-control" name="student_username" id="username" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-password col-md-4 form-group">
                                        <label for="password">Password <span class="required">*</span></label>
                                        <input type="password" class="col-md-12 form-control" name="student_password" id="password" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <div class="student-confirm-password col-md-4 form-group">
                                        <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                                        <input type="password" class="col-md-12 form-control" name="student_confirm_password" id="confirm_password" value="" autocomplete="off" required="required"/>
                                    </div>
                                    <input type="hidden" name="action" value="student_register_login" />
                                    <input type="hidden" name="clogin_id" value="<?php echo $current_user_id; ?>" />
                                    <input type="submit" class="submit-button" name="submit" value="Register">
                                    <div class="form_message"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


<?php
get_sidebar();
get_footer();