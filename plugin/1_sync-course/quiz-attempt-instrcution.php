<?php
ob_start();
session_start();

if (!isset($_POST['courseid'])) {
    wp_redirect(get_page_link(1203));
    // return redirect();
    exit();
}

//print_r($_POST['courseid']);

$_SESSION['one_planet']['courseid'] = $_POST['courseid'];

if (get_current_user_id()) {
    wp_redirect(get_page_link(1239));
    // return redirect();
    exit();
}
get_header(); //1245

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
    }

    .elementor-custom-margin{
            margin : 100px 0px;
    }
</style>

<div class="elementor-custom-margin">
    <div class="row justify-content-center ">
        <div class="col-12 col-lg-8">
            <div class="main-wrapper">

                <div class="quiz-wrapper">
                    <div id="top-content" class="notification-notes">
                        <h2 class="">Quiz Attemp Instrcution</h2>
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
                                Click <a href="<?php echo get_page_link(1237); ?>" class="next-quiz">Next</a> to start the quiz.
                            </li>
                            <li>
                                If you've already then click <a href="<?php echo get_page_link(1144); ?>">Login</a>
                            </li>
                        </ul>
                        <!--<hr />-->
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>







<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js"></script>
<script type="text/javascript">
</script>


<?php get_footer(); ?>