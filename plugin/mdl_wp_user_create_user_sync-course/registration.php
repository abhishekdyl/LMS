<?php
global $wpdb, $woocommerce;
get_header(); ?>

<style>
    /* start Ragistraion form styling */

    .headerr h1 {
        font-size: 35px;
        text-align: center;
        font-weight: normal;
        margin-bottom: 10px !important;
    }


    .headerr+hr {
        border-top: 7px solid #ED6E37;
    }

    form .form-group label {
        font-size: 18px;
        color: #878383;
    }

    .form-group input {
        border-color: #ED6E37;
        height: 40px;
        border-radius: 0px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }


    .form-group input#contact {
        background-image: none;
    }

    .form-group select#country,
    .form-group select#acctype {
        height: 42px;
        border-color: #ED6E37;
        font-size: 18px;
        color: #aaa;
    }

    .info-parent {
        display: flex;
        justify-content: space-between;
        margin: 40px 0;
    }


    .info-parent .info {
        font-size: 16px;
        font-weight: bold;
    }


    .info-parent button#submit {
        padding: 10px 16px;
        width: 150px;
        font-size: 16px;
        font-weight: bold;
        letter-spacing: 2px;
        background-color: #ED6E37;
        border: 2px solid #000;
        border-radius: 5px;
    }

    .required::after {
        content: ' *';
        font-size: 30px;
        color: #cd3939;
    }


    .pop-parent {
        margin: 20px;
        border: 5px solid #565454;
        padding: 20px;
    }

    /* End Ragistration form styling */
</style>


<div class="container" style="margin-top:80px;">
    <div class="pop-parent">


        <div class="headerr">
            <h1 class="">SIGN UP</h1>
        </div>
        <hr>

        <form>
            <div class="form-group">
                <label for="email" class="required">Email address</label>
                <input type="email" name="email" required class="form-control" id="email">
            </div>
            <div class="form-group">
                <label for="fname" class="required">First Name(s)</label>
                <input type="text" name="fname" required class="form-control" id="fname">
            </div>
            <div class="form-group">
                <label for="lname" class="required">Last Name(s)</label>
                <input type="text" name="lname" required class="form-control" id="lname">
            </div>
            <div class="form-group">
                <label for="compname" class="required">Company Name (Option)</label>
                <input type="text" name="compname" required class="form-control" id="compname">
            </div>
            <div class="form-group">
                <label for="contact" class="required">Contact Number</label>
                <input type="text" name="contact" required class="form-control" id="contact">
            </div>
            <div class="form-group">
                <label for="country" class="required">Select Country</label>
                <select name="country" id="country" required>
                    <?php
                    $countries = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_countries");
                    foreach ($countries as $country) {
                        echo "<option value='{$country->country_code}'>{$country->country_name}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="acctype" class="required">Account Type</label>
                <select name="acctype" id="acctype" required>
                    <option value=1>Select</option>
                    <option value="volvo">Volvo</option>
                    <option value="saab">Saab</option>
                    <option value="mercedes">Mercedes</option>
                    <option value="audi">Audi</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password" class="required">Enter Password</label>
                <input type="password" name="password" required class="form-control" id="password">
            </div>
            <div class="form-group">
                <label for="crmpassword" class="required">Confirm Password</label>
                <input type="password" name="crmpassword" required class="form-control" id="crmpassword">
            </div>
            <div class="info-parent">
                <div class="info">YOU ALREADY HAVE AN ACCOUNT? CLICK HERE TO <a href="">LOGIN</a></div>
                <button type="button" id="submit" class="btn btn-default">REGISTER</button>
            </div>
        </form>

    </div>
</div>


<?php get_footer(); ?>

<script>
    jQuery(document).ready(function() {
        jQuery("#submit").click(function() {
            // console.log('aaaaaaaaaaaaaaaaaaaaa');
            var formdata = {
                username: jQuery('#email').val(),
                email: jQuery('#email').val(),
                fname: jQuery('#fname').val(),
                lname: jQuery('#lname').val(),
                password: jQuery('#password').val(),
                crmpassword: jQuery('#crmpassword').val(),
                compname: jQuery('#compname').val(),
                contact: jQuery('#contact').val(),
                country: jQuery('#country').val(),
                acctype: jQuery('#acctype').val()
            }
            jQuery.ajax({
                type: 'POST',
                url: "<?php echo plugins_url('sync-course/ajax.php'); ?>",
                data: formdata,
                success: function(response) {
                    console.log('aaaaaaa', response);
                    var res = JSON.parse(response);
                    console.log('bbbbbb', res);

                }
            });

        });
    });
</script>