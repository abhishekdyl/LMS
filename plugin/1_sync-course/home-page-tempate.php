<?php get_header(); ?>
<script>
    jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
    jQuery('#content').find(':first-child').removeClass('tg-container--flex');
</script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo home_url().'/wp-content/themes/zakra/assets/css/home-page.css';?>" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.theme.default.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    .site-content,
    section,
    footer {
        padding: 0px !important;
    }
</style>


<section id="home">
    <div class="row pb-5">

        <div class="owl-carousel owl-theme home-slider">
            <div class="item item-first" style="background-image: url(https://images.unsplash.com/photo-1661956602868-6ae368943878?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80);">
                <div class="caption">
                    <div class="container">
                        <div class="col-md-6 col-sm-12">
                            <h1>Distance Learning Education Center</h1>
                            <h3>Our online courses are designed to fit in your industry supporting all-round with latest technologies.</h3>
                            <a href="#feature" class="section-btn btn btn-default smoothScroll">Discover more</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item item-second" style="background-image: url(https://images.unsplash.com/photo-1661956602116-aa6865609028?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=764&q=80);">
                <div class="caption">
                    <div class="container">
                        <div class="col-md-6 col-sm-12">
                            <h1>Start your journey with our practical courses</h1>
                            <h3>Our online courses are built in partnership with technology leaders and are designed to meet industry demands.</h3>
                            <a href="#courses" class="section-btn btn btn-default smoothScroll">Take a course</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item item-third" style="background-image: url(https://images.unsplash.com/photo-1661956601349-f61c959a8fd4?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80);">
                <div class="caption">
                    <div class="container">
                        <div class="col-md-6 col-sm-12">
                            <h1>Efficient Learning Methods</h1>
                            <h3>Nam eget sapien vel nibh euismod vulputate in vel nibh. Quisque eu ex eu urna venenatis sollicitudin ut at libero. Visit <a rel="nofollow" href="https://www.facebook.com/templatemo">templatemo</a> page.</h3>
                            <a href="#contact" class="section-btn btn btn-default smoothScroll">Let's chat</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<section id="feature">
    <div class="container">
        <div class="row py-5">

            <div class="col-md-4 col-sm-4">
                <div class="feature-thumb">
                    <span>01</span>
                    <h3>Trending Courses</h3>
                    <p>Known is free education HTML Bootstrap Template. You can download and use this for your website.</p>
                </div>
            </div>

            <div class="col-md-4 col-sm-4">
                <div class="feature-thumb">
                    <span>02</span>
                    <h3>Books & Library</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing eiusmod tempor incididunt ut labore et dolore magna.</p>
                </div>
            </div>

            <div class="col-md-4 col-sm-4">
                <div class="feature-thumb">
                    <span>03</span>
                    <h3>Certified Teachers</h3>
                    <p>templatemo provides a wide variety of free Bootstrap Templates for you. Please tell your friends about us. Thank you.</p>
                </div>
            </div>

        </div>
    </div>
</section>



<section id="about">
    <div class="container">
        <div class="row py-5">

            <div class="col-md-6 col-sm-12">
                <div class="about-info">
                    <h2>Start your journey to a better life with online practical courses</h2>

                    <figure>
                        <span><i class="fa fa-users"></i></span>
                        <figcaption>
                            <h3>Professional Trainers</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sint ipsa voluptatibus.</p>
                        </figcaption>
                    </figure>

                    <figure>
                        <span><i class="fa fa-certificate"></i></span>
                        <figcaption>
                            <h3>International Certifications</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sint ipsa voluptatibus.</p>
                        </figcaption>
                    </figure>

                    <figure>
                        <span><i class="fa fa-bar-chart-o"></i></span>
                        <figcaption>
                            <h3>Free for 3 months</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sint ipsa voluptatibus.</p>
                        </figcaption>
                    </figure>
                </div>
            </div>

            <div class="col-md-offset-1 col-md-4 col-sm-12">
                <div class="entry-form">
                    <form action="#" method="post">
                        <h2>Signup today</h2>
                        <input type="text" name="full name" class="form-control" placeholder="Full name" required="">

                        <input type="email" name="email" class="form-control" placeholder="Your email address" required="">

                        <input type="password" name="password" class="form-control" placeholder="Your password" required="">

                        <button class="submit-btn form-control" id="form-submit">Get started</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>



<section id="team">
    <div class="container">
        <div class="row py-5">

            <div class="col-md-12 col-sm-12">
                <div class="section-title">
                    <h2>Teachers <small>Meet Professional Trainers</small></h2>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="team-thumb">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80" class="img-responsive" alt="">
                    </div>
                    <div class="team-info">
                        <h3>Mark Wilson</h3>
                        <span>I love Teaching</span>
                    </div>
                    <ul class="social-icon">
                        <li><a href="#" class="fa fa-facebook-square" attr="facebook icon"></a></li>
                        <li><a href="#" class="fa fa-twitter"></a></li>
                        <li><a href="#" class="fa fa-instagram"></a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="team-thumb">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80" class="img-responsive" alt="">
                    </div>
                    <div class="team-info">
                        <h3>Catherine</h3>
                        <span>Education is the key!</span>
                    </div>
                    <ul class="social-icon">
                        <li><a href="#" class="fa fa-google"></a></li>
                        <li><a href="#" class="fa fa-instagram"></a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="team-thumb">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80" class="img-responsive" alt="">
                    </div>
                    <div class="team-info">
                        <h3>Jessie Ca</h3>
                        <span>I like Online Courses</span>
                    </div>
                    <ul class="social-icon">
                        <li><a href="#" class="fa fa-twitter"></a></li>
                        <li><a href="#" class="fa fa-envelope-o"></a></li>
                        <li><a href="#" class="fa fa-linkedin"></a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="team-thumb">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80" class="img-responsive" alt="">
                    </div>
                    <div class="team-info">
                        <h3>Andrew Berti</h3>
                        <span>Learning is fun</span>
                    </div>
                    <ul class="social-icon">
                        <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                        <li><a href="#" class="fa fa-google"></a></li>
                        <li><a href="#" class="fa fa-behance"></a></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>




<section id="courses">
    <div class="container">
        <div class="row py-5">

            <div class="col-md-12 col-sm-12">
                <div class="section-title">
                    <h2>Popular Courses <small>Upgrade your skills with newest courses</small></h2>
                </div>

                <div class="owl-carousel owl-theme owl-courses">
                    <div class="col-md-4 col-sm-4">
                        <div class="item">
                            <div class="courses-thumb">
                                <div class="courses-top">
                                    <div class="courses-image">
                                        <img src="https://images.unsplash.com/photo-1503428593586-e225b39bddfe?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" class="img-responsive" alt="">
                                    </div>
                                    <div class="courses-date">
                                        <span><i class="fa fa-calendar"></i> 12 / 7 / 2018</span>
                                        <span><i class="fa fa-clock-o"></i> 7 Hours</span>
                                    </div>
                                </div>

                                <div class="courses-detail">
                                    <h3><a href="#">Digi Tech</a></h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>

                                <div class="courses-info">
                                    <div class="courses-author">
                                        <img src="https://images.unsplash.com/photo-1532074205216-d0e1f4b87368?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=741&q=80" class="img-responsive" alt="">
                                        <span>Mark Wilson</span>
                                    </div>
                                    <div class="courses-price">
                                        <a href="<?php echo home_url().'/?page_id=1245&courseid=1220';?>"><span>Apply Now</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="item">
                            <div class="courses-thumb">
                                <div class="courses-top">
                                    <div class="courses-image">
                                        <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" class="img-responsive" alt="">
                                    </div>
                                    <div class="courses-date">
                                        <span><i class="fa fa-calendar"></i> 20 / 7 / 2018</span>
                                        <span><i class="fa fa-clock-o"></i> 4.5 Hours</span>
                                    </div>
                                </div>

                                <div class="courses-detail">
                                    <h3><a href="#">Test dk</a></h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>

                                <div class="courses-info">
                                    <div class="courses-author">
                                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=764&q=80" class="img-responsive" alt="">
                                        <span>Jessica</span>
                                    </div>
                                    <div class="courses-price">
                                        <a href="<?php echo home_url().'/?page_id=1245&courseid=1159';?>"><span>Apply Now</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="item">
                            <div class="courses-thumb">
                                <div class="courses-top">
                                    <div class="courses-image">
                                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" class="img-responsive" alt="">
                                    </div>
                                    <div class="courses-date">
                                        <span><i class="fa fa-calendar"></i> 15 / 8 / 2018</span>
                                        <span><i class="fa fa-clock-o"></i> 6 Hours</span>
                                    </div>
                                </div>

                                <div class="courses-detail">
                                    <h3><a href="#">test</a></h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>

                                <div class="courses-info">
                                    <div class="courses-author">
                                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" class="img-responsive" alt="">
                                        <span>Catherine</span>
                                    </div>
                                    <div class="courses-price free">
                                        <a href="<?php echo home_url().'/?page_id=1245&courseid=1149';?>"><span>Apply Now</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="item">
                            <div class="courses-thumb">
                                <div class="courses-top">
                                    <div class="courses-image">
                                        <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" class="img-responsive" alt="">
                                    </div>
                                    <div class="courses-date">
                                        <span><i class="fa fa-calendar"></i> 10 / 8 / 2018</span>
                                        <span><i class="fa fa-clock-o"></i> 8 Hours</span>
                                    </div>
                                </div>

                                <div class="courses-detail">
                                    <h3><a href="#">Digi solution</a></h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>

                                <div class="courses-info">
                                    <div class="courses-author">
                                        <img src="https://images.unsplash.com/photo-1466112928291-0903b80a9466?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1173&q=80" class="img-responsive" alt="">
                                        <span>Mark Wilson</span>
                                    </div>
                                    <div class="courses-price">
                                        <a href="<?php echo home_url().'/?page_id=1245&courseid=1165';?>"><span>Apply Now</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="item">
                            <div class="courses-thumb">
                                <div class="courses-top">
                                    <div class="courses-image">
                                        <img src="https://images.unsplash.com/photo-1532622785990-d2c36a76f5a6?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" class="img-responsive" alt="">
                                    </div>
                                    <div class="courses-date">
                                        <span><i class="fa fa-calendar"></i> 5 / 10 / 2018</span>
                                        <span><i class="fa fa-clock-o"></i> 10 Hours</span>
                                    </div>
                                </div>

                                <div class="courses-detail">
                                    <h3><a href="#">It soluntion</a></h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>

                                <div class="courses-info">
                                    <div class="courses-author">
                                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80" class="img-responsive" alt="">
                                        <span>Jessica</span>
                                    </div>
                                    <div class="courses-price free">
                                        <a href="<?php echo home_url().'/?page_id=1245&courseid=1202';?>"><span>Apply Now</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
</section>



<section id="testimonial">
    <div class="container">
        <div class="row py-5">

            <div class="col-md-12 col-sm-12">
                <div class="section-title">
                    <h2>Student Reviews <small>from around the world</small></h2>
                </div>

                <div class="owl-carousel owl-theme owl-client">
                    <div class="col-md-4 col-sm-4">
                        <div class="item">
                            <div class="tst-image">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80" class="img-responsive" alt="">
                            </div>
                            <div class="tst-author">
                                <h4>Jackson</h4>
                                <span>Shopify Developer</span>
                            </div>
                            <p>You really do help young creative minds to get quality education and professional job search assistance. I’d recommend it to everyone!</p>
                            <div class="tst-rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="item">
                            <div class="tst-image">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80" class="img-responsive" alt="">
                            </div>
                            <div class="tst-author">
                                <h4>Camila</h4>
                                <span>Marketing Manager</span>
                            </div>
                            <p>Trying something new is exciting! Thanks for the amazing law course and the great teacher who was able to make it interesting.</p>
                            <div class="tst-rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="item">
                            <div class="tst-image">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80" class="img-responsive" alt="">
                            </div>
                            <div class="tst-author">
                                <h4>Barbie</h4>
                                <span>Art Director</span>
                            </div>
                            <p>Donec erat libero, blandit vitae arcu eu, lacinia placerat justo. Sed sollicitudin quis felis vitae hendrerit.</p>
                            <div class="tst-rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="item">
                            <div class="tst-image">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80" class="img-responsive" alt="">
                            </div>
                            <div class="tst-author">
                                <h4>Andrio</h4>
                                <span>Web Developer</span>
                            </div>
                            <p>Nam eget mi eu ante faucibus viverra nec sed magna. Vivamus viverra sapien ex, elementum varius ex sagittis vel.</p>
                            <div class="tst-rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
</section>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js"></script>

<script>
    $('.home-slider').owlCarousel({
        animateOut: 'fadeOut',
        items: 1,
        loop: true,
        dots: false,
        autoplayHoverPause: false,
        autoplay: true,
        smartSpeed: 1000,
    });


    $('.owl-courses').owlCarousel({
        animateOut: 'fadeOut',
        loop: true,
        autoplayHoverPause: false,
        autoplay: true,
        smartSpeed: 1000,
        dots: false,
        nav: true,
        navText: [
            '<i class="fa fa-angle-left"></i>',
            '<i class="fa fa-angle-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            1000: {
                items: 3,
            }
        }
    });

    $('.owl-client').owlCarousel({
        animateOut: 'fadeOut',
        loop: true,
        autoplayHoverPause: false,
        autoplay: true,
        smartSpeed: 1000,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            1000: {
                items: 3,
            }
        }
    });
</script>
<?php
get_footer();
?>