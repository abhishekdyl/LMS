<?php /* Template Name: Users Register */ ?>
<?php get_header(); ?>
<script>
    jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
    jQuery('#content').find(':first-child').removeClass('tg-container--flex');
    //console.log("$('#content:first-child')",jQuery('#content:first-child'));
</script>
<!-- <h1>Hello</h1> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="http://122.176.46.118/learnoneplanet/wp-content/themes/zakra/assets/css/style.css" rel="stylesheet">

<div class="row">
    <div class="col-12 col-lg-6 col-md-6">
        <div class="login-background">
            <div class="wrapper-content">
                <img src="http://122.176.46.118/learnoneplanet/wp-content/uploads/2023/03/Picture__2.png" alt="" class="logo-img" />
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6 col-md-6 custom-my">
        <div class="form-container">
            <div class="form-wrap">
                <h1 class="fw-bold mb-5">Application Form</h1>
                <form method="post" id="user-register-form">
                    <div class="wrap">
                        <div class="floating-label-group input-parent">
                            <input type="text" id="fname" name="fname" data-name="First name" class="form-control required" autocomplete="off" autofocus />
                            <label class="floating-label">First Name</label>
                        </div>
                        <div class="floating-label-group input-parent">
                            <input type="text" id="lname" name="lname" data-name="Last name" class="form-control required" autocomplete="off" autofocus />
                            <label class="floating-label">Last Name</label>
                        </div>
                        <div class="floating-label-group input-parent">
                            <input type="text" id="username" name="username" data-name="Username" class="form-control required" autocomplete="off" autofocus />
                            <label class="floating-label">Username</label>

                        </div>
                        <div class="floating-label-group input-parent">
                            <input type="email" id="email" name="email" data-name="Email" class="form-control required" autocomplete="off" autofocus />
                            <label class="floating-label">E-mail</label>
                        </div>
                        <div class="floating-label-group input-parent">
                            <input type="password" id="#password3-field" name="password" data-name="Password" class="form-control required" autocomplete="off" autofocus />
                            <a href="javascript:void(0);" toggle="#password3-field" class="fa fa-fw fa-eye field-icon toggle-password text-decoration-none text-dark"></a>
                                <label class="floating-label">Password</label>
                        </div>
                        <div class="floating-label-group input-parent">
                            <input type="password" id="repassword" name="crmpassword" data-name="Confirm Password"  class="form-control required" autocomplete="off" autofocus />
                            <a href="javascript:void(0);" toggle="#password3-field" class="fa fa-fw fa-eye field-icon toggle-password text-decoration-none text-dark"></a>
                            <label class="floating-label">Re-password</label>
                        </div>
                        <input type="submit" name="submit" value="Apply Now" class="w-100 fs-4 fw-bold" />

                        <div class="success-msg">
                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    var $ = jQuery.noConflict(); // jquery keyword to $ sign

    // Special correcter function
    function lettersNumbersCheck(name) {
        var regEx = /^[0-9a-zA-Z]+$/;
        if (name.match(regEx)) {
            return true;
        } else {
            return false;
        }
    }
    // length function
    function checkLenght(str) {
        if (str.length < 3) {
            return {
                status: false,
                msg: "User name should be greater than 3 character"
            };
        }
        if (str.length > 50) {
            return {
                status: false,
                msg: "User name should be less than 50 character"
            };
        }
        return {
            status: true,
            msg: ""
        };
    }

    $(function() {
        $('#user-register-form').submit(function(e) {
            var flag = true;
            var form_error = {};
            e.preventDefault();
            $(this).find('input').each(function() {
                // validation for empty feild.
                if ($(this).hasClass('required') && $(this).val() == "") {
                    flag = false;
                    // $(this).addClass('input-error');
                    form_error[$(this).attr('name')] = `${$(this).attr('data-name')} is required`;
                } else {
                    //validation for special correcter.
                    if ($(this).attr('name') == 'username') {
                        if (!lettersNumbersCheck($(this).val().trim())) {
                            flag = false;
                            form_error[$(this).attr('name')] = `Special corrector and space not Valid.`;
                            return false;
                        }
                    }
                    //validation for min & max langth
                    if ($(this).attr('name') == 'username') {
                        userlengthdata = checkLenght($(this).val());
                        if (userlengthdata.status == false) {
                            flag = false;
                            form_error[$(this).attr('name')] = `${userlengthdata.msg}`;
                            return false;
                        }
                    }


                }

            });
            console.log('after ajax flag: dfffff ', flag);
            // validation for password and crmpassword are same
            if ($(this).find('input[name="password"]').val() !== $(this).find('input[name="crmpassword"]').val() && $(this).find('input[name="crmpassword"]').val() != "" && $(this).find('input[name="password"]').val() != "") {
                flag = false;
                form_error['crmpassword'] = `Please enter same as password`;
            }
            //echo all validation 
            if (flag == false) {
                $.each(form_error, function(i, n) {
                    //alert( "Name: " + i + ", Value: " + n );
                    $('#user-register-form').find(`[name="${i}"]`).parents('.input-parent').find('.error-data').remove();
                    $('#user-register-form').find(`[name="${i}"]`).parents('.input-parent').append(`<div class="error-data">${n}</div>`);
                });
                console.log('form-error', form_error);
                return false;
            }
            $.ajax({
                type: "POST",
                url: "<?php echo plugins_url('sync-course/ajax/user-register.php'); ?>",
                data: $(this).serialize(),
                beforeSend: function() {
                    $('#user-register-form').find('input[type="submit"]').val('PLEASE WAIT...');
                    $('#user-register-form').find('input[type="submit"]').prop('disabled',true);
                },
                success: function(response) {
                   var responsedata=JSON.parse(response);
                    if(responsedata.status==false){
                        responsedata.data.forEach(function(ele,index){
                            $('#user-register-form').find(`[name="${ele.key}"]`).parents('.input-parent').find('.error-data').remove();
                            $('#user-register-form').find(`[name="${ele.key}"]`).parents('.input-parent').append(`<div class="error-data">${ele.error}</div>`);
                        });
                    }else{
                        $('#user-register-form')[0].reset();
                        $('.success-msg').html(`<div class="alert alert-success text-center mt-3">
  <strong>Success!</strong> ${responsedata.msg}
</div>`);
                        //alert(responsedata.msg);
                    }
                    console.log('response ', response);
                },
                complete: function() {
                    $('#user-register-form').find('input[type="submit"]').val('Apply Now');
                    $('#user-register-form').find('input[type="submit"]').prop('disabled',false);
                }
            });

        });
        // Remove any validation 
        $('#user-register-form').on('focus', 'input', function() {
            $(this).parent('.input-parent').find('.error-data').remove();
        });

    });
$(function(){

    $("#user-register-form input").focusin(function() {
        if ($(this).val() == "") {
            $(this).closest(".floating-label-group").find('.floating-label').addClass('floating-label-up');
        }
    });

    $("#user-register-form input").focusout(function() {
        if ($(this).val() == "") {
            $(this).closest(".floating-label-group").find('.floating-label').removeClass('floating-label-up');
        }

    });

    $(".toggle-password").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if ($(this).closest('.floating-label-group').find('input').attr('type') == "password") {
            $(this).closest('.floating-label-group').find('input').attr("type", "text");
        } else {
            $(this).closest('.floating-label-group').find('input').attr("type", "password");
        }
    });
});
</script>
<?php get_footer(); ?>