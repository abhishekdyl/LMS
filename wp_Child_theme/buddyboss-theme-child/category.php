<?php
 /*Template Name:  Category Listing*/


$categories = [];

//  get_header();
 global $wpdb ,$post ,$current_user ;
 $taxonomy     = 'product_cat';
 $orderby      = 'name';  
 $show_count   = 0;      // 1 for yes, 0 for no
 $pad_counts   = 0;      // 1 for yes, 0 for no
 $hierarchical = 1;      // 1 for yes, 0 for no  
 $title        = '';  
 $empty        = 0;

 $args = array(
        'taxonomy'     => $taxonomy,
        'orderby'      => $orderby,
        'show_count'   => $show_count,
        'pad_counts'   => $pad_counts,
        'hierarchical' => $hierarchical,
        'title_li'     => $title,
        'hide_empty'   => $empty
 );
$all_categories = get_categories( $args );
foreach ($all_categories as $cat) {

    if($cat->category_parent == 0) {
        // echo "<pre>";
        // print_r($cat);
        // echo "</pre>";

        $category_id = $cat->term_id;       
        //    echo '<br /><a href="'. get_term_link($cat->slug, 'product_cat') .'">'. $cat->name .'</a>';
        //    $categories[$category_id] = $cat->name;

       $args2 = array(
               'taxonomy'     => $taxonomy,
               'child_of'     => 0,
               'parent'       => $category_id,
               'orderby'      => $orderby,
               'show_count'   => $show_count,
               'pad_counts'   => $pad_counts,
               'hierarchical' => $hierarchical,
               'title_li'     => $title,
               'hide_empty'   => $empty
       );
       $sub_cats = get_categories( $args2 );
       if($sub_cats) {
           foreach($sub_cats as $sub_category) {
               $categories[$sub_category->term_id] = $sub_category->name;
           }   
       }
   }       
}
echo json_encode($categories);