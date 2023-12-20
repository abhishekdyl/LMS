<?php
ob_start();
session_start();
if (!isset($_SESSION['one_planet']['courseid']) && empty($_SESSION['one_planet']['courseid'])) {
    wp_redirect(get_page_link(1203));
    exit();
}
// echo "<pre>";
// print_r($_SESSION['one_planet']);
// echo "</pre>";
get_header(); //1237
?>



<script>
    jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
    jQuery('#content').find(':first-child').removeClass('tg-container--flex');
</script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
<link href="http://122.176.46.118/learnoneplanet/wp-content/themes/zakra/assets/css/home-page.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.theme.default.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    .elementor-custom-margin {
        margin-top: 100px;
    }

    .row {
        margin-left: 0px !important;
        margin-right: 0px !important;
    }

    .site-content,
    section,
    footer {
        padding: 0px !important;
    }

    #top-content ul {
        list-style: none;
    }

    #top-content ul li {
        font-size: 18px;
        font-weight: 500;
        color: #7a7a7a;
        padding-bottom: 15px;
    }

    #top-content ul li a {
        color: #000000;
        font-size: 18px;
        text-decoration: underline !important;
    }

    #first-form form label {
        font-size: 16px;
    }

    .progress-btn {
        cursor: pointer;
        padding: 10px 20px;
        background-color: #00acd3;
        color: #ffffff;
        font-size: 20px;
        font-weight: 500;
        border-radius: 3px;
        box-shadow: 1px 1px 3px gray;
    }

    .progress-btn:hover {
        color: #ffffff;
        background-color: #029dc0;
    }

    #question-sheet .question-board p {
        font-size: 20px;
        font-weight: 500;
        color: #333333;
    }

    #question-sheet label,
    #question-sheet input {
        cursor: pointer;
    }

    #question-sheet label img {
        max-width: 200px;
        box-sizing: content-box;
    }

    #question-sheet label p {
        font-size: 18px;
        font-weight: 400;
    }

    /*#question-sheet{
        display: none;
    }*/

    .quiz-response {
        display: none;
        font-size: 20px;
        padding: 50px;
        margin: 50px;
        border: 1px solid #444;
        border-radius: 15px;
        font-weight: 500;
        text-align: justify;
    }

    .continue-btn {
        margin-top: 25px;
        text-align: center;
    }

    .continue-btn a {
        background: #982A10;
        color: #ffffff;
        padding: 10px 25px;
    }
</style>

<div class="elementor-custom-margin">
    <div class="row justify-content-center ">
        <div class="col-12 col-lg-8">
            <div class="main-wrapper">

                <div class="quiz-wrapper">
                    <!-- <div id="top-content" class="notification-notes">
            <h2 class="text-center">Welcome to One Planet Digital Literacy Test</h2>
            <ul class="p-0 m-0">
                <li>
                    You have 10 mins to answer 10 questions.
                </li>
                <li>
                    The result will be calculated out of 100%.
                </li>
                <li>
                    The passing result is <b>50% and above.</b>
                </li>
                <li>
                    Click <a href="javascript:void(0);" class="next-quiz">Next</a> to start the quiz.
                </li>
                <li>
                   If you've already then click <a href="javascript:void(0);">Login</a>
                </li>
            </ul>
        </div> -->
                    <div id="question-sheet">
                        <form class="digital-litracy-form">
                            <fieldset>
                                <div class="question-wrapper">

                                </div>
                            </fieldset>
                        </form>
                        <div id="progress-bar">
                            <div class="row pt-3 pb-5 align-items-center">
                                <div class="col-2">
                                    <div class="text-start">
                                        <button class="progress-btn next-pre-btn pre" data-type="pre">Previous</button>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="progress rounded-0">
                                        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <!--<p class="text-end m-0 progress-text">0%</p>-->
                                </div>
                                <div class="col-2">
                                    <div class="text-end">
                                        <button class="progress-btn next-pre-btn next" data-type="next">Next</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="quiz-response">

                </div>
            </div>
        </div>
    </div>
</div>

<?php

$settingdata = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}moodle_settings");
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $settingdata->url . '/local/coursesync/get_letracy_quiz.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode((object)array('cmid' => $settingdata->cmid, 'token' => $settingdata->token)),
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
$response_data = json_decode($response);
$_SESSION['one_planet']['question_data'] = $response_data;

?>





<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js"></script>
<script type="text/javascript">
    var response_data = <?php echo json_encode($response_data->questions); ?>;
    var total_length = response_data.length;
    var formdata = {};
    $(function() {
        $('body').on('click', '.next-quiz', function() {
            $('.notification-notes').hide();
            $('#question-sheet').show();
        });
        $('body').on('click', '.next-pre-btn', function() {
            var current_index = parseInt($('.question-wrapper').find('.text-question').attr('question-index'));
            var next_pre_index = 0;
            var progress = 0;
            var next_btn = "Next";
            var that = $(this);
            var checkbox_arr = new Array();

            $('.digital-litracy-form').find('[name]')?.each(function() {
                //if($(that).hasClass('next')){
                console.log('checkbox_arr- ', checkbox_arr);

                if ($(this).attr('type') == 'radio') {
                    if ($(this).is(':checked')) {
                        console.log('ccked');
                        formdata[$(this).attr('name')] = $(this).val();

                    }
                } else if ($(this).attr('type') == 'checkbox') {
                    if ($(this).is(':checked')) {
                        console.log('cckedbox');
                        checkbox_arr.push($(this).val());
                    }
                    formdata[$(this).attr('name')] = checkbox_arr;
                } else {

                    formdata[$(this).attr('name')] = $(this).val();
                }
                //}
            });
            console.log('formdata- ', formdata);
            if ($(this).hasClass('pre')) {
                if (current_index == 0) {

                    return false;
                }
                $('.next').text("Next");
                next_pre_index = (current_index - 1);
            } else {
                next_pre_index = (current_index + 1);
                if (next_pre_index == (total_length - 1)) {
                    next_btn = "Submit";
                    //console.log('btmmmmmmmmmmmm');
                }

            }

            if (total_length == next_pre_index) {
                $('.progress-bar').css('width', `100%`);
                $.ajax({
                    type: "POST",
                    url: "<?php echo plugins_url('sync-course/ajax/get-quiz-result.php'); ?>",
                    data: formdata,
                    beforeSend: function() {
                        $(that).text('Please Wait...');
                        $(that).prop('disabled', true);
                    },
                    success: function(response) {
                        var obj = JSON.parse(response);
                        $('.quiz-wrapper').hide();

                        if (obj.status == true) {
                            $('.quiz-response').html(`<div><div>${obj.msg}</div>
                                <div class="continue-btn"><a href="<?php echo home_url('?page_id=1187'); ?>">Continue</a></div>
                                </div>`);
                        } else {
                            $('.quiz-response').html(obj.msg);
                            location.replace("<?php echo get_page_link(1237); ?>");
                        }
                        $('.quiz-response').show();
                        console.log('response- ', response);
                    },
                    complete: function() {
                        $(that).text('Submit');
                        $(that).prop('disabled', false);
                    }
                });

                return false;
            }
            progress = (next_pre_index * 100) / total_length;

            // console.log('next_pre_index-  ',next_pre_index);
            render_question(next_pre_index, progress);
            if ($(that).hasClass('next')) {
                $(that).text(next_btn);
            }

        });

        // console.log('response_data',response_data);
        // console.log('response_data[index]',response_data[0]);
        // console.log('response length',response_data.length);
        function render_question($index = 0, $progress = 0) {
            var data = response_data[$index];
            //console.log('data--- ',data);
            var template = '';
            switch (data.type) {
                case "multichoice":
                    var input_type = (data.isRadioButton == true) ? "radio" : "checkbox";
                    template += `<div class="text-question" question-index="${$index}">
                            <div class="question-board my-5">
                                ${data.questiontext}
                            </div>
                            <div class="row">`;
                    data.question_answer_text?.forEach(function(inputdata) {
                        var checked = "";
                        if (input_type == "radio") {
                            if (formdata.hasOwnProperty(data.id)) {
                                if (formdata[data.id] == inputdata.id) {
                                    checked = 'checked';
                                }

                            }
                        } else {
                            if (formdata.hasOwnProperty(data.id)) {
                                if (formdata[data.id].includes(inputdata.id)) {
                                    checked = 'checked';
                                }

                            }

                        }

                        template += `<div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="${input_type}" name="${data.id}" id="${inputdata.id}" value="${inputdata.id}" ${checked}>
                                        <label class="form-check-label" for="redio4">
                                            ${inputdata.answer}
                                        </label>
                                    </div>
                                </div>

                                `;
                    });
                    template += `</div></div>`;
                    break;
                case "truefalse":
                    template += `<div class="text-question" question-index="${$index}">
                            <div class="question-board my-5">
                                ${data.questiontext}
                            </div>
                            <div class="row">
                                <div class="col-12">
                                <select name="${data.id}">
                                <option value="" >Select</option>
                            `;

                    data.question_answer_text?.forEach(function(inputdata) {
                        var selected = '';
                        if (formdata.hasOwnProperty(data.id)) {
                            if (formdata[data.id] == inputdata.id) {
                                selected = 'selected';
                            }

                        }
                        template += `
                                    <div class="form-check">
                                        <option value="${inputdata.id}" ${selected}>${inputdata.answer}</option>
                                        <!-- <input class="form-check-input" type="${input_type}" name="" id="redio4" value="opt1">
                                        <label class="form-check-label" for="redio4">
                                         <p>${inputdata.answer}</p>
                                         </label>-->
                                    </div>
                                

                                `;
                    });
                    template += `</select></div></div></div>`;
                    break;
            }
            $('.question-wrapper').html(template);
            //$('.progress-text').html(`${$progress.toFixed(0)}%`);
            $('.progress-bar').css('width', `${$progress}%`);
            return;

        }
        render_question();

    });
</script>


<?php get_footer(); ?>