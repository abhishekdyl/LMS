<?php
global $wpdb, $woocommerce;
get_header();
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>



<style>
    .banner-page img {
        max-width: 100vw;
        width: 100vw;
    }


    .text-container {
        position: absolute;
        top: 200px;
        left: calc((100vw - 1200px) / 2);
        width: 450px;
        text-align: left;
        text-transform: initial;
    }

    .home-slide-text h3 {
        color: #33c84c;
        margin-top: 8px;
        margin-bottom: 10px;
        line-height: 1.5;
        font-size: 20px;
    }



    .home-slide-text p {
        font-size: 16px;
        font-family: sans-serif;
    }

    .home-slide-text a {
        width: 150px;
        display: inline-block;
        padding: 8px 0px;
        text-align: center;
        background: #33c84c;
        color: #fff;
        border-radius: 3px;
        text-decoration: none;
    }

    .home-slide-text a:last-child {
        background: #0281eb;
        margin-left: 50px;

    }

    .add-clearfix:before,
    .add-clearfix:after {
        content: " ";
        display: table;

    }


    .add-clearfix:after {
        clear: both;
    }

    .repeat-control:before,
    .repeat-control:after {
        content: " ";
        display: table;

    }

    .repeat-control:after {
        clear: both;
    }

    .repeat-control {
        margin-left: -15px;
        margin-right: -15px;
    }

    .left-box-inner,
    .center-box-inner {
        box-sizing: border-box;
        padding-left: 15px;
        padding-right: 15px;
        width: 100%;
    }

    .left-box-inner .left-box-core-inner,
    .center-box-inner .center-box-core-inner {
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
        min-height: 160px;
    }

    .left-box-core-inner .left-back,
    .center-box-core-inner .center-back,
    .right-box-core-inner .right-back {
        background-image: url(./wp-content/plugins/sync-course/image/hnad-circle.png);
        background-size: 100%;
        min-height: 340px;
    }

    .center-box-core-inner .center-back {
        background-image: url(./wp-content/plugins/sync-course/image/hnad-circle-yellow.png) !important;
    }

    .right-box-core-inner .right-back {
        background-image: url(./wp-content/plugins/sync-course/image/hnad-circle-green.png) !important;
    }



    .left-back h3.bts-hd,
    .center-back h3.bts-hd {
        margin-bottom: 30px !important;
        width: max-content;
        padding: 5px 10px;
        color: #fff;
        transform: skewX(-15deg);
        background-color: #f44336;
    }


    .center-back-text .bottom-texts h3.bts-hd {
        background-color: #f49316 !important;
    }


    .right-back-text .bottom-texts h3.bts-hd {}


    .left-back a.vc_single_image-wrapper.vc_box_border_grey,
    .center-back a.vc_single_image-wrapper.vc_box_border_grey {
        display: inline-block;
    }

    .bottom-texts {
        width: 78.8%;
        position: absolute;
        top: 16.5%;
        left: 10.5%;
        /* background: #191717ad; */
        background: none !important;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        text-align: center !important;
    }

    .bottom-texts h3.bts-hd {
        margin-bottom: 30px !important;
        width: max-content;
        padding: 5px 10px;
        color: #fff;
        transform: skewX(-15deg);
        background-color: #f44336;

    }

    .swiper-container {
        width: 100%;
        height: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    .swiper-slide {
        text-align: center;
        font-size: 48px;
        font-weight: 500;

        /* Center slide text vertically */
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }
</style>



<div class="banner-page">
    <img src="https://studyif.com/wp-content/uploads/2020/01/home-banenr2.jpg" />


    <div class="container-fluid">
        <div class="text-container">

            <div class="home-slide-text">
                <div class="wp_wrapper">
                    <h2></h2>
                    <h3>For Maths and English learning</h3>
                    <p>Have fun learning with the Multi-Award-Winning online Maths &amp; English Quizzes for Reception to year 13</p>
                    <p>
                        <br />
                        <br />
                        <br />
                        <a href="/subscriptions-and-pricing#parentSub">Parents</a><a href="/subscriptions-and-pricing#schoolSubs">Schools</a>
                    </p>

                </div>
            </div>
        </div>
    </div>

</div>

<section class="signify-classess">
    <div class="clearfix"></div>

    <div class="classes-method-signify">
        <div class="container-lg">
            <!-- <div class="row repeat-control">

                <div class="left-box animate__animated animate__backInLeft col-lg-4">
                    <div class="left-box-inner clearfix">
                        <div class="left-box-core-inner">
                            <div class="left-back">
                                <a href="/courses-categories/" target="_self" class="vc_single_image-wrapper   vc_box_border_grey"></a>
                            </div>
                            <div class="left-back-text">
                                <div class="bottom-texts">
                                    <h3 class="bts-hd">Reception</h3>
                                    <p>Counting objects, Learn Shapes, Fun with Alphabets, Rhymes and more.</p>
                                    <p><b>Maths :</b> &nbsp; 63 Topics</p>
                                    <p><b>English :</b> &nbsp; 27 Topics</p>
                                    <p>&nbsp;</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="center-box animate__animated animate__backInLeft col-lg-4">
                    <div class="center-box-inner clearfix">
                        <div class="center-box-core-inner">
                            <div class="center-back">
                                <a href="/courses-categories/" target="_self" class="vc_single_image-wrapper   vc_box_border_grey"></a>
                            </div>
                            <div class="center-back-text">
                                <div class="bottom-texts">
                                    <h3 class="bts-hd">CLASS 1</h3>
                                    <p>Counting objects, Learn Shapes, Fun with Alphabets, Rhymes and more.</p>
                                    <p><b>Maths :</b> &nbsp; 63 Topics</p>
                                    <p><b>English :</b> &nbsp; 27 Topics</p>
                                    <p>&nbsp;</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="right-box animate__animated animate__backInLeft col-lg-4">
                    <div class="right-box-inner clearfix">
                        <div class="right-box-core-inner">
                            <div class="right-back">
                                <a href="/courses-categories/" target="_self" class="vc_single_image-wrapper   vc_box_border_grey"></a>
                            </div>
                            <div class="right-back-text">
                                <div class="bottom-texts">
                                    <h3 class="bts-hd">CLASS 1</h3>
                                    <p>Counting objects, Learn Shapes, Fun with Alphabets, Rhymes and more.</p>
                                    <p><b>Maths :</b> &nbsp; 63 Topics</p>
                                    <p><b>English :</b> &nbsp; 27 Topics</p>
                                    <p>&nbsp;</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div> -->


            <!-- <div class="vc_row wpb_row vc_row-fluid extra-ro">
                <div class="receptions_id wpb_animate_when_almost_visible wpb_bounceInLeft bounceInLeft wpb_column vc_column_container vc_col-sm-4 wpb_start_animation animated">
                    <div class="vc_column-inner ">
                        <div class="wpb_wrapper">
                            <div class="wpb_single_image wpb_content_element vc_align_center">

                                <figure class="wpb_wrapper vc_figure">
                                    <a href="/courses-categories/" target="_self" class="vc_single_image-wrapper   vc_box_border_grey"><img width="300" height="300" src="https://studyif.com/wp-content/uploads/2020/01/hnad-circle-300x300.png" class="vc_single_image-img attachment-medium" alt="Online Maths Quiz" srcset="https://studyif.com/wp-content/uploads/2020/01/hnad-circle-300x300.png 300w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-600x601.png 600w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-150x150.png 150w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-768x769.png 768w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-1024x1024.png 1024w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-100x100.png 100w" sizes="(max-width: 300px) 100vw, 300px"></a>
                                </figure>
                            </div>

                            <div class="wpb_text_column wpb_content_element ">
                                <div class="wpb_wrapper">
                                    <div class="bottom-texts">
                                        <h3 class="bts-hd">Reception</h3>
                                        <p>Counting objects, Learn Shapes, Fun with Alphabets, Rhymes and more.</p>
                                        <p><b>Maths :</b>&nbsp; 63 Topics</p>
                                        <p><b>English :</b>&nbsp; 27 Topics</p>
                                        <p>&nbsp;</p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="year1_id wpb_animate_when_almost_visible wpb_bounceInUp bounceInUp wpb_column vc_column_container vc_col-sm-4 wpb_start_animation animated">
                    <div class="vc_column-inner ">
                        <div class="wpb_wrapper">
                            <div class="wpb_single_image wpb_content_element vc_align_center">

                                <figure class="wpb_wrapper vc_figure">
                                    <a href="/year-1/" target="_self" class="vc_single_image-wrapper   vc_box_border_grey"><img width="300" height="300" src="https://studyif.com/wp-content/uploads/2020/01/hnad-circle-300x300.png" class="vc_single_image-img attachment-medium" alt="Online Maths Quiz" srcset="https://studyif.com/wp-content/uploads/2020/01/hnad-circle-300x300.png 300w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-600x601.png 600w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-150x150.png 150w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-768x769.png 768w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-1024x1024.png 1024w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-100x100.png 100w" sizes="(max-width: 300px) 100vw, 300px"></a>
                                </figure>
                            </div>

                            <div class="wpb_text_column wpb_content_element ">
                                <div class="wpb_wrapper">
                                    <div class="bottom-texts">
                                        <h3 class="bts-hd">Class 1</h3>
                                        <p>Learn to Add, Subtract and Read Clock, Consonant and vowel sounds, Part of Speech and more.</p>
                                        <p><b>Maths :</b>&nbsp; 107 Topics</p>
                                        <p><b>English :</b>&nbsp; 47 Topics</p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="year2_id wpb_animate_when_almost_visible wpb_bounceInRight bounceInRight wpb_column vc_column_container vc_col-sm-4 wpb_start_animation animated">
                    <div class="vc_column-inner ">
                        <div class="wpb_wrapper">
                            <div class="wpb_single_image wpb_content_element vc_align_center">

                                <figure class="wpb_wrapper vc_figure">
                                    <a href="/year-2/" target="_self" class="vc_single_image-wrapper   vc_box_border_grey"><img width="300" height="300" src="https://studyif.com/wp-content/uploads/2020/01/hnad-circle-300x300.png" class="vc_single_image-img attachment-medium" alt="Online Maths Quiz" srcset="https://studyif.com/wp-content/uploads/2020/01/hnad-circle-300x300.png 300w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-600x601.png 600w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-150x150.png 150w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-768x769.png 768w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-1024x1024.png 1024w, https://studyif.com/wp-content/uploads/2020/01/hnad-circle-100x100.png 100w" sizes="(max-width: 300px) 100vw, 300px"></a>
                                </figure>
                            </div>

                            <div class="wpb_text_column wpb_content_element ">
                                <div class="wpb_wrapper">
                                    <div class="bottom-texts">
                                        <h3 class="bts-hd">Class 2</h3>
                                        <p>Number Patterns, Multiply, Measurement, Probability, Sentences, Grammar and more.</p>
                                        <p><b>Maths :</b>&nbsp; 112 Topics</p>
                                        <p><b>English :</b>&nbsp; 65 Topics</p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->



            <!-- <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide" style="background: linear-gradient(to right, #757f9a, #d7dde8);">Slide 1</div>
                    <div class="swiper-slide" style="background: linear-gradient(to right, #134e5e, #71b280);">Slide 2</div>
                    <div class="swiper-slide" style="background: linear-gradient(to right, #5c258d, #4389a2);">Slide 3</div>
                    <div class="swiper-slide" style="background: linear-gradient(to right, #2bc0e4, #eaecc6);">Slide 4</div>
                    <div class="swiper-slide" style="background: linear-gradient(to right, #085078, #85d8ce);">Slide 5</div>
                </div> -->
                <!-- Add Pagination -->
                <!-- <div class="swiper-pagination"></div>
            </div> -->

        </div>
    </div>

</section>



<section class="assess-control col-lg-12">

</section>



<!-- <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script> -->
<!-- <script src="https://unpkg.com/swiper/swiper-bundle.js"></script> -->
<!-- <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script> -->


<!-- <script>
    var swiper = new Swiper('.swiper-container', {
        direction: 'vertical',
        slidesPerView: 1,
        mousewheel: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
</script> -->


<?php
get_footer();
?>