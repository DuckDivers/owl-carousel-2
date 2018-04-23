<?php

add_action('wp_ajax_owl_carousel_tax', 'owl_carousel_tax');

function owl_carousel_tax(){
    global $wpdb;
    
    $html = '<select id="dd_owl_post_taxonomy" name="dd_owl_post_taxonomy" class="dd_owl_post_taxonomy_field">';
       $tax_objects = get_object_taxonomies($_POST['posttype'], 'objects');
    foreach ($tax_objects as $tax){

		$html .= '<option value="dd_owl_'.$tax->name.'" ' . selected( $dd_owl_post_taxonomy, 'dd_owl_' .$tax->name, false ) . '> ' . __( $tax->labels->name, 'owl-carousel-2' ) . '</option>';
        }
    
    $html .= '</select>';

    echo $html;
        
    wp_die();        
}



//        echo '<pre>'; print_r($tax_objects); echo '</pre>';
//        
//         $args = array(
//              'orderby' => 'name',
//              'show_count' => 0,
//              'pad_counts' => 0,
//              'hierarchical' => 1,
//              'taxonomy' => 'faq_category',
//            );
//
//       $cats = get_categories( $args );
//    
//        foreach ($cats as $cat){
//            echo $cat->name . '<br />';
//        }
