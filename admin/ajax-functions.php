<?php

add_action('wp_ajax_owl_carousel_tax', 'owl_carousel_tax');

function owl_carousel_tax(){
    global $wpdb;
    
    if (metadata_exists('post', $_POST['postid'], 'dd_owl_post_taxonomy_type')) $meta = get_post_meta( $_POST['postid'], 'dd_owl_post_taxonomy_type', true );

    $html = '';    

    $tax_objects = get_object_taxonomies($_POST['posttype'], 'objects');

    if (null == $tax_objects) {
        $html .= sprintf('<span class="no-cats">' . __('There are no matching Taxonomies', 'owl-carousel-2') . '</span>');
    }
    else {
        $html .= '<select id="dd_owl_post_taxonomy_type" name="dd_owl_post_taxonomy_type" class="dd_owl_post_taxonomy_type_field">';

            foreach ($tax_objects as $tax){
                $label = $tax->labels->name;
                $value= $tax->name;
                $html .= '<option value="'.$value.'" ';
                $html .= ($value == $meta) ? "selected" : null;  
                $html .=  '> ' . $label . '</option>';
                
            }

        $html .= '</select>';
    }

    echo $html;
        
    die();        
}
add_action('wp_ajax_owl_carousel_terms', 'owl_carousel_terms');
function owl_carousel_terms(){
    global $wpdb;
    
    $html = '';    
    
    $term_objects = get_terms($_POST['taxtype'], 'objects');

    if (metadata_exists('post', $_POST['postid'], 'dd_owl_post_taxonomy_term')) $theterm = get_post_meta( $_POST['postid'], 'dd_owl_post_taxonomy_term', true );
    
    if (null == $term_objects) {
        $html .= sprintf('<span class="no-cats">' . __('There are no matching terms', 'owl-carousel-2') . '</span>');
    }
    else {
        $html .= '<select id="dd_owl_post_taxonomy_term" name="dd_owl_post_taxonomy_term" class="dd_owl_post_taxonomy_term_field">';

            foreach ($term_objects as $term){
                $label = $term->name;
                $value= $term->slug;
                $html .= '<option value="'.$value.'" ';
                $html .= ($value == $theterm) ? "selected" : null;  
                $html .=  '> ' . $label . '</option>';
                
            }

        $html .= '</select>';
    }

    echo $html;
        
    die();        
}