(function ($) {
    "use strict";

    /* For Login User */
    $(document).on('click', '.submit-button', function() {
        $(".student_login").validate({
            rules: {
                student_username: {required: true},
                student_password: {required: true}
            },
            messages: {
                student_username: {required: 'Username is Required'},
                student_password: {required: 'Password is Required'}
            },
            submitHandler: function (form) {
                let g_val = $(document).find('.g-value').val();
                grecaptcha.execute(g_val, {action: 'student_login'}).then(function (token) {
                    $(document).find('.recaptcha-response').val(token);
                    $.ajax({
                        url: my_ajax_object.ajax_url,
                        type: "POST",
                        dataType: "json",
                        data: ($(form)).serialize(),
                        beforeSend: function () {
                            $('.submit-button').attr('disabled', true);
                            $('.student_login').addClass('form-submitting');
                            $(document).find('.student_login .student-loader').css('display','inline-block').show();
                        },
                        success: function (data) {
                            let custom_html = '';
                            if (data.success) {
                                custom_html = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>Success</strong> ' + data.message + ' </div>';
                                $(".student_login .form_message").html(custom_html).show();
                                setTimeout(function () {
                                    document.location.href = data.url;
                                }, 3000);
                            } else {
                                custom_html = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' + data.message + ' </div>';
                                $(".student_login .form_message").html(custom_html).show();
                            }
                        },
                        complete: function () {
                            $('.submit-button').attr('disabled', false);
                            $('.student_login').removeClass('form-submitting');
                            $(document).find('.student_login .student-loader').hide();
                        }
                    });
                    return false;
                });
            }
        });
    });

    /* Account Checkbox */
    $(document).on('change', '.custom-control-input:checkbox',function(){
        $('.student-dob').remove();
        var validator = $(".student_register_login").validate({
            rules: {
                account_type : {required: true},
                student_fname: {required: true},
                student_lname: {required: true},
                student_email: {required: true},
                phone: {required: true, validatephone: true},
                student_dob: {required: true},
                student_username: {required: true},
                student_password: {required: true, minlength: 8},
                student_confirm_password: {required: true, minlength: 8, equalTo: "#password"}
            },
            messages: {
                account_type: {required: 'You must check at least 1 box'},
                /*student_fname: {required: 'First Name is Required'},
                student_lname: {required: 'Last Name is Required'},
                student_email: {required: 'E-mail is Required'},
                student_username: {required: 'Username is Required'},*/
                student_password: {minlength: 'Your password must be at least 8 characters long'},
                student_confirm_password: {minlength: 'Your password must be at least 8 characters long',
                    equalTo: 'Please enter the same password as password'
                }
            },
            errorPlacement: function(error, element) {
                if (element.is(":checkbox")) {
                    error.insertAfter('#account_checkbox');
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                let g_val = $(document).find('.g-value').val();
                grecaptcha.execute(g_val, {action: 'student_register_login'}).then(function (token) {
                    $(document).find('.recaptcha-response').val(token);
                    $.ajax({
                        url: my_ajax_object.ajax_url,
                        type: "POST",
                        dataType: "json",
                        data: $(form).serialize(),
                        beforeSend: function () {
                            $('.submit-button').attr('disabled', true);
                            $('.student_register_login').addClass('form-submitting');
                            $(document).find('.student_register_login .student-loader').css('display', 'inline-block').show();
                        },
                        success: function (data) {
                            let custom_html = '';
                            if (data.success) {
                                custom_html = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>Success!</strong> ' + data.message + ' </div>';
                                $(".student_register_login .form_message").html(custom_html).show();
                                setTimeout(function () {
                                    document.location.href = data.url;
                                }, 4000);
                            } else {
                                custom_html = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' + data.message + ' </div>';
                                $(".student_register_login .form_message").html(custom_html).show();
                            }
                        },
                        error: function () {
                            alert('Error');
                        },
                        complete: function () {
                            $('.submit-button').attr('disabled', false);
                            $('.student_register_login').removeClass('form-submitting');
                            $(document).find('.student_register_login .student-loader').hide();
                        }
                    });
                    return false;
                });
            }
        });
        validator.resetForm();
        $('.custom-control .custom-control-input').prop('checked', false);
        let checked_value = $(this).prop('checked', true);
        if (checked_value.val() === 'parent'){
            $('.student_register_login #label_text').each(function() {
                $(this).text('Parent');
            });
        }else if (checked_value.val() === 'adult_learner'){
            $('.student_register_login #label_text').each(function() {
                $(this).text('Adult Learner');
            });
        }
    });

    /* For Register User */
    $("body").delegate("#student_dob", "focusin", function(){
        $("#student_dob").datepicker({
            inline: false,
            dateFormat:'mm-dd-yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2004:+0',
        });
    });
    $(document).on('click', '.submit-button', function() {
        $(".student_register_login").validate({
            rules: {
                account_type : {required: true},
                student_fname: {required: true},
                student_lname: {required: true},
                student_email: {required: true},
                phone: {required: true, validatephone: true},
                student_dob: {required: true},
                student_username: {required: false}, 
                student_password: {required: true, minlength: 8},
                student_confirm_password: {required: true, minlength: 8, equalTo: "#password"}
            },
            messages: {
                account_type: {required: 'You must check at least 1 box'},
                /*student_fname: {required: 'First Name is Required'},
                student_lname: {required: 'Last Name is Required'},
                student_email: {required: 'E-mail is Required'},
                student_username: {required: 'Username is Required'},*/
                student_password: {minlength: 'Your password must be at least 8 characters long'},
                student_confirm_password: {minlength: 'Your password must be at least 8 characters long',
                    equalTo: 'Please enter the same password as password'
                }
            },
            errorPlacement: function(error, element) {
                if (element.is(":checkbox")) {
                    error.insertAfter('#account_checkbox');
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                let g_val = $(document).find('.g-value').val();
                grecaptcha.execute(g_val, {action: 'student_register_login'}).then(function (token) {
                    $(document).find('.recaptcha-response').val(token);
                    $.ajax({
                        url: my_ajax_object.ajax_url,
                        type: "POST",
                        dataType: "json",
                        data: $(form).serialize(),
                        beforeSend: function () {
                            $('.submit-button').attr('disabled', true);
                            $('.student_register_login').addClass('form-submitting');
                            $(document).find('.student_register_login .student-loader').css('display', 'inline-block').show();
                        },
                        success: function (data) {
                            let custom_html = '';
                            if (data.success) {
                                custom_html = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>Success!</strong> ' + data.message + ' </div>';
                                $(".student_register_login .form_message").html(custom_html).show();
                                setTimeout(function () {
                                    document.location.href = data.url;
                                }, 4000);
                            } else {
                                custom_html = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' + data.message + ' </div>';
                                $(".student_register_login .form_message").html(custom_html).show();
                            }
                        },
                        complete: function () {
                            $('.submit-button').attr('disabled', false);
                            $('.student_register_login').removeClass('form-submitting');
                            $(document).find('.student_register_login .student-loader').hide();
                        }
                    });
                    return false;
                });
            }
        });
        $.validator.messages.required = function (param, input) {
            let input_error = '#'+input.id+'-error';
            $(input_error).remove();
            if ($(input_error).length === 0) {
                return $("label[for='" + input.id + "']").closest('label').text().toLowerCase() + ' is required';
            }
        }
        $.validator.addMethod('validatephone', function (value, element) {
            // return this.optional(element) || /^(\+91-|\+91|0)?\d{10}$/.test(value);
            return this.optional(element) || /^[0-9-+]+$/.test(value);
        }, "Please enter a valid phone number");
    });

    /* For Edit Student Form */
    $(document).on('click', '.submit-button', function() {
        $(".student_edit").validate({
            rules: {
                student_fname: {required: true},
                student_lname: {required: true},
                student_email: {required: true},
                student_dob: {required: true},
                student_username: {required: true},
                student_password: {required: true, minlength: 8},
                student_confirm_password: {required: true, minlength: 8, equalTo: "#password"}
            },
            messages: {
                student_fname: {required: 'First Name is Required'},
                student_lname: {required: 'Last Name is Required'},
                student_email: {required: 'E-mail is Required'},
                student_dob: {required: 'Student Birth Date Required'},
                student_username: {required: 'Username is Required'},
                student_password: {required: 'Password is Required', minlength: 'Your password must be at least 8 characters long'},
                student_confirm_password: {required: 'Confirm Password is Required',
                    minlength: 'Your password must be at least 8 characters long',
                    equalTo: 'Please enter the same password as password'
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: my_ajax_object.ajax_url,
                    type: "POST",
                    dataType: "json",
                    data: $(form).serialize(),
                    beforeSend: function () {
                        $('.submit-button').attr('disabled', true);
                        $('#student_edit').addClass('form-submitting');
                        $(document).find('.student_edit .student-loader').css('display','inline-block').show();
                    },
                    success: function (data) {
                        let custom_html = '';
                        if (data.success) {
                            custom_html = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>Success</strong> ' + data.message + ' </div>';
                            $(".student_edit .form_message").html(custom_html).show();
                            setTimeout(function () {
                                document.location.href = data.url;
                            }, 3000);
                        } else {
                            custom_html = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' + data.message + ' </div>';
                            $(".student_edit .form_message").html(custom_html).show();
                        }
                    },
                    complete: function () {
                        $('.submit-button').attr('disabled', false);
                        $('#student_edit').removeClass('form-submitting');
                        $(document).find('.student_edit .student-loader').hide();
                    }
                });
                return false;
            }
        });
    });

    /* Student Edit Form On Edit User Id */
    $(document).on('click','.student-edit, .student-create', function () {
        let user_id = $(this).data('id');
        let button_name = $(this).data('name');
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                "action": "student_edit_form",
                "student_edit_id": user_id,
                "button_name": button_name,
            },
            beforeSend: function () {
                $(document).find('[data-id='+user_id+'] .student-loader').css('display','inline-block').show();
            },
            success: function (data) {
                let result = JSON.parse(data);
                if (result.success) {
                    $('.edit_student_form').html(result.result).show();
                    $('html, body').animate({
                        'scrollTop' : $(".edit_student_form").position().top
                    }, 1000);
                }
                else {
                    $('.edit_student_form').html(result.result).show();
                }
            },
            complete: function () {
                $(document).find('[data-id='+user_id+'] .student-loader').hide();
            }
        });
        return false;
    });

    /* For Check email */
    $(document).on('change input','#student_email', function () {
        let email = $(this).val();
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                "action": "student_email_check",
                "user_email": email,
            },
            success: function (data) {
                let custom_html = '';
                let result = JSON.parse(data);
                if (result.success) {
                    custom_html = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>' + result.message + '</strong></div>';
                    $('.student_register_login .form_message').html(custom_html).hide();
                    $('.student_edit .form_message').html(custom_html).hide();
                }
                else {
                    custom_html = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' + result.message + ' </div>';
                    $('.student_register_login .form_message').html(custom_html).show();
                    $('.student_edit .form_message').html(custom_html).show();
                }
            }
        });
        return false;
    });

    /* For Check Username */
    $(document).on('change input','#username', function () {
        let username = $(this).val();
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                "action": "student_username_check",
                "username": username,
            },
            success: function (data) {
                let custom_html = '';
                let result = JSON.parse(data);
                if (result.success) {
                    custom_html = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>' + result.message + '</strong></div>';
                    $('.student_register_login .form_message').html(custom_html).hide();
                }
                else {
                    custom_html = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' + result.message + ' </div>';
                    $('.student_register_login .form_message').html(custom_html).show();
                }
            }
        });
        return false;
    });

    /* For Product Student Change */
    $(document).on('change','.product_student',function(e){
        let cart_id = $(this).data('cart-id');
        let nonce = $('#woocommerce-cart-nonce').val();
        let selected_value = $('#product_student_'+cart_id+' option:selected').val();
        let custom_div = $('.entry-content > .woocommerce > .woocommerce-notices-wrapper');
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                action: 'custom_update_cart',
                security: nonce,
                product_student_id: selected_value,
                cart_id: cart_id
            },
            success: function( response ) {
                if (response.success) {
                    setTimeout(function () {
                        $(custom_div).html('<div class="woocommerce-message" role="alert">Cart updated.</div>').show();
                        $('html, body').animate({
                            'scrollTop' : $(".entry-header").position().top
                        }, 1000);
                    }, 1000);
                }
            }
        });
    }).on('click','.sso_btn',function(){
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                action: 'sso_url',
            },
            success: function( response ) {
                let custom_html = '';
                response = JSON.parse(response);
                if (response.success) {
                    custom_html = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>Success</strong> ' + response.message + ' </div>';
                    $(".msg_sso").html(custom_html).show();
                    setTimeout(function () {
                        window.open(response.ssourl);
                    }, 1000);
                }
                else {
                    custom_html = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>Success</strong> ' + response.message + ' </div>';
                    $(".msg_sso").html(custom_html).show();
                }
            }
        });
    }).on('click','#course-enroll',function(e){
        let product_id = $(this).data('item-id');
        let product_group_id = $(this).data('id');
        let custom_div = $('.entry-content > .woocommerce > .woocommerce-notices-wrapper');
        let cs_start_date = $(this).parents('ul').prev('#cs_start_date').attr('data-value');
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                action: 'custom_update_coursegroup',
                // security: nonce,
                product_group_id: product_group_id,
                product_id: product_id,
                cs_start_date: cs_start_date
            },
            success: function( response ) {
                if (response.success) {
                    document.location.href = response.url;
                    setTimeout(function () {
                        $(custom_div).html('<div class="woocommerce-message" role="alert">Cart updated.</div>').show();
                        $('html, body').animate({
                            'scrollTop' : $(".entry-header").position().top
                        }, 1000);
                    }, 1000);
                }
            }
        });
    });

    /* validation on select field */
    $(document).on('click', '.checkout-button',function (e) {
        let course_qty = $(document).find('li.course-qty-error');
        if (course_qty.length){
            $(course_qty).prevAll().remove();
        }
        let calssname = '.product_student';
        let moodle_class = '.product-moodlegroup';
        let user_login = '.student-btn';
        let custom_div = $('.entry-content > .woocommerce > .woocommerce-notices-wrapper');
        $(user_login).each(function () {
            let value = $(this).data('id');
            if (value === 'student_login'){
                $(custom_div).html('<div class="woocommerce-error" role="alert">You must be logged in to proceed checkout.</div>');
                $('html, body').animate({
                    'scrollTop' : $(".entry-header").position().top
                }, 1000);
                e.preventDefault();
            }
        });
        $(calssname).each(function () {
            let value = $(this).find('option:selected').val();
            if (value === '' && !user_login){
                $(custom_div).html('<div class="woocommerce-error" role="alert">Please select student from the list or create a new student.</div>');
                $('html, body').animate({
                    'scrollTop' : $(".entry-header").position().top
                }, 1000);
                e.preventDefault();
            }else if (value === '' && user_login){
                $(custom_div).html('<div class="woocommerce-error" role="alert">Please select from the list or create a new student.</div>');
                $('html, body').animate({
                    'scrollTop' : $(".entry-header").position().top
                }, 1000);
                e.preventDefault();
            }
        });
        $(moodle_class).each(function () {
            let value = $(this).find(':input').val();
            if (value === ''){
                $(custom_div).html('<div class="woocommerce-error" role="alert">Please select group from the list.</div>');
                $('html, body').animate({
                    'scrollTop' : $(".entry-header").position().top
                }, 1000);
                e.preventDefault();
            }
        });
    });

    /* Update Woocommerce Fragments On Page Reload */
    $(document).ready(function () {
        $("[name='update_cart']").removeAttr('disabled').attr('aria-disabled', 'false').trigger("click");
        let class_name = $('.woocommerce-cart-form').hasClass('processing');
        if (class_name) {
            $('.student-btn').hide();
            $('.edit-student').hide();
        }
    });

    /* Auto Update Cart On Change */
    $(document).on("change", "input.qty, .product_student1", function(){
        $("[name='update_cart']").removeAttr('disabled').attr('aria-disabled','false').trigger("click");
    });

    /* Hide Form On Cancel */
    $(document).on('click','.cancel-button', function () {
        $('.edit_student_form').hide();
        $('html, body').animate({
            'scrollTop' : $(".student-edit-table").position().top
        }, 1000);
    });

    /* Student Modal */
    $(document).on('click','.student-btn', function () {
        let student_data = $(this).data('id');
        let modal_dialog = $('#student-modal').find('#modal-dialog');
        let modal_html = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                action: 'student_modal_show',
                student_data: student_data
            },
            beforeSend: function () {
                $(modal_dialog).removeClass().addClass('modal-dialog');
                $('#student-modal-label').html('Loading...');
                $('#student-modal-body').html(modal_html);
            },
            success: function( response ) {
                let response_result = JSON.parse(response);
                if (response_result.success) {
                    $(modal_dialog).removeClass();
                    $(modal_dialog).addClass(response_result.result.student_dialog);
                    $('#student-modal-label').html(response_result.result.student_title);
                    $('#student-modal-body').html(response_result.result.student_content);
                    $('#student-modal').modal('show').show();
                }else {
                    $('#student-modal').modal().hide();

                }
            }
        });
    });

    /* Slick Slider */
    $('.moodle-course-section').slick({
        infinite: true,
        autoplay: false,
        // autoplaySpeed: 2000,
        slidesToShow: 3,
        slidesToScroll: 3,
        arrows: true,
        // focusOnSelect: true,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $(window).on('resize', function() {
        $(".moodle-course-section").slick("refresh");
    });

    /* Teacher Modal */
    $(document).on('click','.teacher-link', function () {
        let course_teacher_data = $(this).data('id');
        let modal_dialog = '#modal-dialog';
        let modal_html = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                action: 'course_teacher_modal_show',
                course_teacher_data: course_teacher_data
            },
            beforeSend: function () {
                let modal_dialog = '#modal-dialog';
                // $(modal_dialog).removeClass().addClass('modal-dialog');
                $('#course-teacher-label').html('Loading...');
                $('#course-teacher-modal-body').html(modal_html);
            },
            success: function( response ) {
                let response_result = JSON.parse(response);
                if (response_result.success) {
                    /*$(modal_dialog).removeClass();
                    $(modal_dialog).addClass(response_result.result.teacher_dialog);*/
                    $('#course-teacher-label').html(response_result.result.teacher_title);
                    $('#course-teacher-modal-body').html(response_result.result.teacher_content);
                    $('#course-teacher-modal').modal('show').show();
                }else {
                    $('#course-teacher-modal').modal().hide();
                }
            }
        });
    });

    /* Remove redirect parameter */
    if (location.href.includes('?ref')) {
        history.pushState({}, null, location.href.split('?ref')[0]);
    }

    /* View syllabus modal */
    $(document).on('click','.syllabus-link', function () {
        let course_syllabus_data = $(this).data('id');
        let modal_dialog = '#modal-dialog';
        let modal_html = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                action: 'teacher_modal',
                course_syllabus_data: course_syllabus_data
            },
            beforeSend: function () {
                let modal_dialog = '#modal-dialog';
                $('#course-syllabus-modal-body').html(course_syllabus_data);
            },
            success: function( response ) {
               
                if (course_syllabus_data!='') {
                    $('#course-syllabus-modal-body').html(course_syllabus_dat);
                    $('#course-syllabus-modal').modal('show').show();
                }else {
                    $('#ourse-syllabus-modal').modal().hide();
                }
            }
        });
    });

    /* View calendar modal */
    $(document).on('click','.calendar-link', function () {
        let post_id = $(this).data('id');
        let section_count = $(this).data('count');
        let course_calendar_data = $(this).data('value');
        let usertimezone = $(this).data('zone');
        let modal_dialog = '#modal-dialog';
        let modal_html = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';
        console.log(usertimezone);
        $.ajax({ 
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                action: 'course_calendar_modal_show',
                course_calendar_data: course_calendar_data,
                post_id: post_id,
                section_count: section_count,
                usertimezone: usertimezone
            },
            beforeSend: function () {
                let modal_dialog = '#modal-dialog';
                // $(modal_dialog).removeClass().addClass('modal-dialog');
                $('#course-calendar-label').html('Loading...');
                $('#course-calendar-modal-body').html(modal_html);
            },
            success: function( response ) {
                let response_result = JSON.parse(response);
                if (response_result.success) {
                    /*$(modal_dialog).removeClass();
                    $(modal_dialog).addClass(response_result.result.calendar_dialog);*/
                    $('#course-calendar-label').html(response_result.result.calendar_title);
                    $('#course-calendar-modal-body').html(response_result.result.calendar_content);
                    $('#course-calendar-modal').modal('show').show();
                }else {
                    $('#course-calendar-modal').modal().hide();
                }
            }
        });
    });

    if (typeof Cookies !== "undefined" && !Cookies.get('wc_tz')) {
        Cookies.set('wc_tz', Intl.DateTimeFormat().resolvedOptions().timeZone, {expires: 1});
    }
    /* Hide mini cart and redirect to cart page */
    $(document).on('click','.header-cart-link-wrap',function () {
        $(this).removeClass('selected');
        document.location.href = $(this).children('a').attr('href');
    });

    /* Show Add/Edit student button in dropdown */
    $(document.body).on('wc_fragments_refreshed', function () {
        let student_class = 'select.product_student', product_student_table = '.create-login';
        $(product_student_table).select2();
        $(student_class).select2();
        $(student_class).on('select2:open', function (e) {
            let new_button_html = '', edit_button_html = '', final_button;
            let select_results = '.select2-results';
            let new_student_html = $(document).find('#newstd').val();
            let edit_student_html = $(document).find('#editstd');
            if (parseInt(new_student_html) === 1){
                new_button_html = '<button type="button" class="student-btn btn btn-sm btn-outline-primary mr-1 col-xs-6 select-std" data-id="new_student" data-toggle="modal" data-target="#student-modal">Add a Student</button>';
            }
            if (parseInt(edit_student_html.val()) === 1){
                edit_button_html = '<a href="'+edit_student_html.attr('data-href')+'" class="edit-student btn btn-sm btn-outline-primary col-xs-6 mt-2 select-std" data-id="edit_student">Add/Edit Student</a>'
            }
            final_button = new_button_html + edit_button_html;
            $(select_results).nextAll().remove();
            $(final_button).insertAfter(select_results);
        });

        /* for before login select field */
        $(product_student_table).on('select2:open', function (e) {
            let button_html = '';
            let select_results = '.select2-results';
            let login_html = $(document).find('#new-login').val();
            if (parseInt(login_html) === 1){
                button_html = '<button type="button" class="student-btn btn btn-sm btn-outline-primary mr-1 col-xs-6 select-std" data-id="new_student" data-toggle="modal" data-target="#student-modal">Create an Account</button>' +
                    '<button type="button" class="student-btn btn btn-sm btn-outline-primary col-xs-6 select-std mt-2" data-id="student_login" data-toggle="modal" data-target="#student-modal">Sign In</button>';
            }
            $(select_results).html(button_html);
        });

        $(document).on('click','.select-std',function (e) {
            $(product_student_table).select2('close');
            $(student_class).select2('close');
        });
    });

    /* Change Product categories and tags link in product details page */
    $(document).ready(function() {
        $('.posted_in a').each(function (e) {
            $(this).attr('href','#product-type').attr('data-toggle','modal').attr('class','product-type').removeAttr('rel');
        });

        $('.tagged_as a').each(function (e) {
            $(this).removeAttr('href').removeAttr('rel');
        });
    });

    /* Show modal product page product type */
    $(document).on('click','.product-type', function () {
        let product_type_category = $(this).text();
        let modal_html = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                action: 'product_type_modal_show',
                product_type_category: product_type_category
            },
            beforeSend: function () {
                $('#product-modal-label').html('Loading...');
                $('#product-modal-body').html(modal_html);
            },
            success: function( response ) {
                let response_result = JSON.parse(response);
                if (response_result.success) {
                    $('#product-modal-label').html(response_result.result.product_type_title);
                    $('#product-modal-body').html(response_result.result.product_type_content);
                    $('#product-type').modal('show').show();
                }else {
                    $('#product-type').modal().hide();
                }
            }
        });
        return false;
    });

})(jQuery);