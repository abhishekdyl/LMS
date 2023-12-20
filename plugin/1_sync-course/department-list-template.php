<?php get_header(); //1203 
?>
<script>
    jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
    jQuery('#content').find(':first-child').removeClass('tg-container--flex');
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
<link href="http://122.176.46.118/learnoneplanet/wp-content/themes/zakra/assets/css/style.css" rel="stylesheet">
<section class="elementor-custom-margin">
    <div class="container">
        <div class="row">
            <?php
            global $wpdb;
            $sql = "SELECT wp_id FROM {$wpdb->prefix}course_category_map WHERE wp_id IN ( SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE parent=0)";
            $cat_results = $wpdb->get_col($sql);
            $args = array(
                "taxonomy"  => "product_cat",
                "orderby"   => "name",
                "order"     => "ASC",
                "include"   => $cat_results
            );

            $all_cat = get_terms($args);

            // print_r($all_cat);
            if (count($all_cat)) {

                foreach ($all_cat as $cat) {

                    $term_id = $cat->term_id;

                    $sql_chk = "SELECT * FROM {$wpdb->prefix}term_taxonomy WHERE parent='$term_id'";
                    $cat_results_chk = $wpdb->get_row($sql_chk);
                    $parent = $cat_results_chk->parent;

                    if ($parent == '') {

                        $url = get_page_link(1190) . '&department=' . $cat->slug; //get_term_link($cat->term_id);

                    } else {

                        $url = get_page_link(1325) . '&semester=' . $parent; //get_term_link($cat->term_id);
                    }


                    $imageid = get_term_meta($cat->term_id, 'thumbnail_id', true);
                    $image = wp_get_attachment_image_url($imageid, 'full');

            ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">

                        <a href="<?php echo $url; ?>">
                            <div class="course-card">
                                <div class="course-card-img">
                                    <?php
                                    if (!empty($image)) {

                                    ?>
                                        <img src="<?php echo $image; ?>" class="main" alt="">
                                    <?php } ?>

                                </div>
                                <div class="course-card-content">
                                    <h4><?php echo $cat->name; ?></h4>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php }
            } else { ?>
                <h2>No Data Founds</h2>
            <?php } ?>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>

<?php get_footer(); ?>