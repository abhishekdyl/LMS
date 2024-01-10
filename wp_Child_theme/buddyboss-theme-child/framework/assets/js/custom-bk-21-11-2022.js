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
                $.ajax({
                    url: my_ajax_object.ajax_url,
                    type: "POST",
                    dataType: "json",
                    data: ($(form)).serialize(),
                    beforeSend: function () {
                        $(document).find('.student_login .form_message').html(' ');
                    },
                    success: function (data) {
                        // let html = '';
                        if (data.success) {
                            $(".student_login .form_message").html(data.message);
                            setTimeout(function () {
                                document.location.href = data.url;
                            }, 3000);
                        } else {
                            $(".student_login .form_message").html(data.message);
                        }
                    }
                });
                return false;
            }
        });
    });

    /* For Register User */
    $(document).on('click', '.submit-button', function() {
        $(".student_register_login").validate({
            rules: {
                student_fname: {required: true},
                student_lname: {required: true},
                student_email: {required: true},
                student_username: {required: true},
                student_password: {required: true}
            },
            messages: {
                student_fname: {required: 'First Name is Required'},
                student_lname: {required: 'Last Name is Required'},
                student_email: {required: 'E-mail is Required'},
                student_username: {required: 'Username is Required'},
                student_password: {required: 'Password is Required'}
            },
            submitHandler: function (form) {
                $.ajax({
                    url: my_ajax_object.ajax_url,
                    type: "POST",
                    dataType: "json",
                    data: $(form).serialize(),
                    beforeSend: function () {
                        $(document).find('.student_register_login .form_message').html(' ');
                    },
                    success: function (data) {
                        // let html = '';
                        if (data.success) {
                            $(".student_register_login .form_message").html(data.message);
                            setTimeout(function () {
                                document.location.href = data.url;
                            }, 3000);
                        } else {
                            $(".student_register_login .form_message").html(data.message);
                        }
                    }
                });
                return false;
            }
        });
    });

    /* For Edit Student Form */
    $(document).on('click', '.submit-button', function() {
        $(".student_edit").validate({
            rules: {
                student_fname: {required: true},
                student_lname: {required: true},
                student_email: {required: true},
                student_username: {required: true},
                student_password: {required: true},
                student_repeat_password: {required: true, equalTo: "#password"}
            },
            messages: {
                student_fname: {required: 'First Name is Required'},
                student_lname: {required: 'Last Name is Required'},
                student_email: {required: 'E-mail is Required'},
                student_username: {required: 'Username is Required'},
                student_password: {required: 'Password is Required'},
                student_repeat_password: {required: 'Confirm Password is Required',
                    equalTo: 'Please enter the same password as above'
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: my_ajax_object.ajax_url,
                    type: "POST",
                    dataType: "json",
                    data: $(form).serialize(),
                    beforeSend: function () {
                        $(document).find('.student_edit .form_message').html(' ');
                    },
                    success: function (data) {
                        if (data.success) {
                            $(".student_edit .form_message").html(data.message);
                            setTimeout(function () {
                                document.location.href = data.url;
                            }, 3000);
                        } else {
                            $(".student_edit .form_message").html(data.message);
                        }
                    }
                });
                return false;
            }
        });
    });

    /* Student Edit Form On Edit User Id */
    $(document).on('click','.student-edit', function () {
        let user_id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                "action": "student_edit_form",
                "student_edit_id": user_id,
            },
            success: function (data) {
                let result = JSON.parse(data);
                if (result.success) {
                    $('.edit_student_form').html(result.result);
                    $('html, body').animate({
                        'scrollTop' : $(".edit_student_form").position().top
                    }, 1000);
                }
                else {
                    $('.edit_student_form').html(result.result);
                }
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
                let result = JSON.parse(data);
                if (result.success) {
                    $('.student_register_login .form_message').html('').hide();
                    $('.student_edit .form_message').html('').hide();
                }
                else {
                    $('.student_register_login .form_message').html(result.message).show();
                    $('.student_edit .form_message').html(result.message).show();
                }
            }
        });
        return false;
    });

    /* For Check Username */
    $(document).on('change input','#student_username', function () {
        let username = $(this).val();
        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: {
                "action": "student_username_check",
                "username": username,
            },
            success: function (data) {
                let result = JSON.parse(data);
                if (result.success) {
                    $('.student_register_login .form_message').html('').hide();
                }
                else {
                    $('.student_register_login .form_message').html(result.message).show();
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
    });

    /* validation on select field */
    $(document).on('click', '.checkout-button',function (e) {
        let calssname = '.product_student';
        let custom_div = $('.entry-content > .woocommerce > .woocommerce-notices-wrapper');
        $(calssname).each(function () {
            let value = $(this).find('option:selected').val();
            if (value === ''){
                $(custom_div).html('<div class="woocommerce-error" role="alert">Please Select Student From The List</div>');
                $('html, body').animate({
                    'scrollTop' : $(".entry-header").position().top
                }, 1000);
                e.preventDefault();
            }
        });
    });

    /* Auto Update Cart On Change */
    $(document).on("change", "input.qty, .product_student", function(){
        $("[name='update_cart']").trigger("click");
    });

    /* Search Student User */
    /*$(document).on('click', ".search-student", function(){
        let formData = $('#student_search1').val();
        let pagination_class = $('.pagination');
        $.ajax({
            url: my_ajax_object.ajax_url,
            type: "POST",
            dataType: "json",
            data: {
                'action': 'get_student_search_list',
                'formData': formData,
            },
            beforeSend: function () {
                // jQuery('.is_btn_loader').show();
            },
            success: function (data) {
                if (data.success) {
                    $('.student_search_list').html(data.result);
                    /!*if (type === 'apply_filter') {
                        $('.pf_job_search_list_lm').html(data.result);
                    } else if (type === 'apply_load_more') {
                        $('.pf_job_search_list_lm').append(data.result);
                    }
                    $(document).find('.pf_job_list_lm').data('paged', data.paged);
                    if (!data.is_load_more) {
                        $('.job_search_lm').addClass('hidden');
                    } else {
                        $('.job_search_lm').removeClass('hidden');
                    }*!/
                } else {
                    $('.student_search_list').html(data.result);
                    // $('.job_search_lm').addClass('hidden');
                }
            },
            complete: function () {
                // jQuery('.is_btn_loader').hide();
            }
        });
        return false;
    });*/


    /* Custom Pagination */
    $(document).ready(function($) {
        var ajaxurl = my_ajax_object.ajax_url;
        function dcs_load_all_posts(page){
            $(".dcs_pag_loading").fadeIn().css('background','#ccc');
            var data = {
                page: page,
                action: "demo-pagination-load-posts"
            };
            $.post(ajaxurl, data, function(response) {
                $(".dcs_universal_container").html('').append(response);
                $(".dcs_pag_loading").css({'background':'none', 'transition':'all 1s ease-out'});
            });
        }
        dcs_load_all_posts(1);
        $(document).on('click','.dcs_universal_container .dcs-universal-pagination li.active',function(){
            alert('hello');
            var page = $(this).attr('p');
            dcs_load_all_posts(page);
        });
    });

})(jQuery);