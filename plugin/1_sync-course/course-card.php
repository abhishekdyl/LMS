<?php

if (!isset($_GET['department'])) {
    wp_redirect(get_page_link(1203));
    exit();
}

$deparment = $_GET['department'];

$currentPage = isset($_GET['paged']) ? $_GET['paged'] : 1;
$posts = new WP_Query(array(
    'post_type' => 'product', // Default or custom post type
    'posts_per_page' => 5, // Max number of posts per page
    'paged' => $currentPage,
    'tax_query' => array(
        array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $deparment,
        )
    ),
));


function bootstrap_pagination(\WP_Query $wp_query = null, $echo = true, $params = [])
{
    if (null === $wp_query) {
        global $wp_query;
    }

    $add_args = [];

    $pages = paginate_links(
        array_merge([
            'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format'       => '?paged=%#%',
            'current'      => max(1, get_query_var('paged')),
            'total'        => $wp_query->max_num_pages,
            'type'         => 'array',
            'show_all'     => false,
            'end_size'     => 3,
            'mid_size'     => 1,
            'prev_next'    => true,
            'prev_text'    => __('« Prev'),
            'next_text'    => __('Next »'),
            'add_args'     => $add_args,
            'add_fragment' => ''
        ], $params)
    );

    if (is_array($pages)) {
        //$current_page = ( get_query_var( 'paged' ) == 0 ) ? 1 : get_query_var( 'paged' );
        $pagination = '<nav aria-label="Page navigation"><ul class="pagination">';

        foreach ($pages as $page) {
            $pagination .= '<li class="page-item' . (strpos($page, 'current') !== false ? ' active' : '') . '"> ' . str_replace('page-numbers', 'page-link', $page) . '</li>';
        }

        $pagination .= '</ul></nav>';

        if ($echo) {
            echo $pagination;
        } else {
            return $pagination;
        }
    }

    return null;
}
get_header(); //1190
?>

<script>
    jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
    jQuery('#content').find(':first-child').removeClass('tg-container--flex');
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
<link href="http://122.176.46.118/learnoneplanet/wp-content/themes/zakra/assets/css/style.css" rel="stylesheet">
<section class="elementor-custom-margin">
    <!-- wp-content/plugins/sync-course/generate-invoice-template-multiple.php -->
    <form method="post" action="<?php echo get_page_link(1245); ?>">
        <div class="container">
            <div class="row">
                <?php
                global $wpdb;
                $currentuser = wp_get_current_user();
                if ($posts->have_posts()) {
                    if (!empty($posts->posts)) {
                        $i = 1;
                        foreach ($posts->posts as $course) {
                            $product = wc_get_product($course->ID);
                            $course_meta = get_post_meta($course->ID);
                            $image_id  = $product->get_image_id();
                            $image_url = wp_get_attachment_image_url($image_id, 'full');
                            $add_to_cart = esc_html($product->single_add_to_cart_text());
                            $regular_price = $product->regular_price;
                            $sale_price = $product->sale_price;
                            if (!empty($sale_price)) {
                                $regular_price = $sale_price;
                            }
                            $checkout_url = wc_get_checkout_url();
                            //https://yourdomain.com/checkout/?add-to-cart=PRODUCT_ID
                            if ($currentuser->roles[0] != "administrator") {
                                $getenrollecourse = $wpdb->get_row("SELECT invd.*,inv.userid FROM {$wpdb->prefix}invoice as inv JOIN {$wpdb->prefix}invoice_details as invd ON invd.invoice_id = inv.id WHERE invd.courseid=" . $course->ID . " AND inv.userid=" . $currentuser->ID);
                                if (!empty($getenrollecourse)) {
                                    $disabled = 'disabled';
                                } else {
                                    $disabled = '';
                                }
                            }
                ?>

                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="course-card">
                                    <div class="course-card-img">
                                        <img src="<?php echo $image_url; ?>" class="main" alt="">
                                    </div>
                                    <div class="course-card-content">
                                        <h4><?php echo $product->name; ?></h4>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6>$<?php echo $regular_price; ?></h6>
                                            <!-- <a href="<?php echo get_page_link(1245) . '&courseid=' . $course->ID; ?>" class="buy_btn">Apply Now</a> -->
                                            <h6><input type="checkbox" name="courseid[]" value="<?php echo $course->ID; ?>" class="checkbox" id="chk_box" onclick="my_chk_fun<?php echo $i; ?>();" <?php if ($disabled) {
                                                                                                                                                                                                        echo $disabled;
                                                                                                                                                                                                    } ?>> Tick the course to proceed </h6>
                                        </div>
                                        <!-- <h6>$<?php echo $add_to_cart; ?></h6> -->

                                    </div>
                                </div>
                            </div>

                            <!-- <input type="text" value="<?php echo get_page_link(1245); ?>"> -->

                            <script type="text/javascript">
                                function my_chk_fun<?php echo $i; ?>() {
                                    var countCheckd = $('input[type=checkbox]:checked').length;

                                    //alert(countCheckd);

                                    if (countCheckd > 0) {
                                        document.getElementById("bulk_btn").style.display = "block";
                                    }
                                    if (countCheckd == 0) {
                                        document.getElementById("bulk_btn").style.display = "none";
                                    }
                                }
                            </script>
                <?php
                            $i++;
                        }
                    }
                }
                ?>
                <button type="submit" id="bulk_btn" class="btn btn-warning btn-md" style="display: none; margin-top: 2px; margin-bottom: 6px;">Apply Now</button>
            </div>
        </div>
    </form>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>

<?php get_footer(); ?>