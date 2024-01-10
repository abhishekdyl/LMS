(function ($) {
    "use strict";

    /* Hide show moodle section fields */
    let courselength = 'tr.courselength', calendarweekoff = 'tr.calendarweekoff', course_days = 'span.course-days', subterms = 'tr.subterms';
    function hide_show_section_table(){
        if ($('#one_time_subscription').is(":checked")) {
            $(courselength).each(function () {
                let course_start_val = $(this).prev('.course-start').find(':input').val();
                $(this).find(':input').val(course_start_val);
                $(this).hide();
            });
            $(calendarweekoff).each(function () {
                $(this).hide();
            });
            $(course_days).each(function () {
                $(this).hide();
            });
            $(subterms).each(function () {
                $(this).hide();
            });
        }else {
            $(courselength).each(function () {
                $(this).show();
            });
            $(calendarweekoff).each(function () {
                $(this).show();
            });
            $(course_days).each(function () {
                $(this).show();
            });
            $(subterms).each(function () {
                $(this).show();
            });
        }
    }

    $(document).on('change','#one_time_subscription',function (e) {
        $('.courselength').each(function () {
           $(this).find(':input').val('');
        });
        hide_show_section_table();
    });

    $(document).on('click', '.wc-item-table', function (e) {
        hide_show_section_table();
    });

    /* Hide if checked on page load */
    hide_show_section_table();

    $("body").delegate('[name^=course_stock_qty]', 'change', function(){
        if($(this).val() < 0){
            $(this).val('0');
        }
    });

    /* Hide show subscription terms fields */
    let product_data_type = '#product-type,#_wps_sfw_product', sub_term = '.subterms';
    $(document).on('change click select',product_data_type,function () {
        let selected_val = $('#product-type').val(), subscription_checkbox = $('#_wps_sfw_product').filter(':checked').length;
        if (selected_val === 'subscription' || selected_val === 'variable-subscription' || subscription_checkbox === 1){
            $(sub_term).each(function () {
                $(this).show();
            });
        }
        else {
            $(sub_term).each(function () {
                $(this).hide();
            });
        }
    });

    /* Enable manage stock on input */
    $(document).on('click keydown change input', function () {
        let check = false, manage_checkbox = $(document).find('#_manage_stock');
        let availability = [];
        $('.stock-qty').find(':input').each(function(){
            let input_val = $(this).val();
            if (input_val && parseInt(input_val) !== 0) {
                availability.push(input_val);
            }
        });
        if (availability.length !== 0){
            check = true;
        }
        manage_checkbox.prop('checked',check);
    });



})(jQuery);