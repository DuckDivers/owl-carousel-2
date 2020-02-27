<?php

add_action('wp_ajax_owl_carousel_tax', 'owl_carousel_tax');

function owl_carousel_tax(){
    
    //Terms to exclude from WooCommerce
    $wc_not = array('product_type', 'product_visibility' , 'product_shipping_class');
	
	$meta = '';
    
    if (metadata_exists('post', $_POST['postid'], 'dd_owl_post_taxonomy_type')) $meta = get_post_meta( $_POST['postid'], 'dd_owl_post_taxonomy_type', true );
    
    $html = '';    

    $tax_objects = get_object_taxonomies($_POST['posttype'], 'objects');

    if (null == $tax_objects) {
        $html .= sprintf('<span class="no-cats">' . __('There are no matching Taxonomies', 'owl-carousel-2') . '</span>');
    }
    else {
        $html .= '<select id="dd_owl_post_taxonomy_type" name="dd_owl_post_taxonomy_type" class="dd_owl_post_taxonomy_type_field">';
            
            if (!in_array($meta, $tax_objects)) $html .= '<option value="" selected> - - Choose A Tax Type - -</option>';

            foreach ($tax_objects as $tax){
                if ($_POST['posttype'] == 'product' && in_array($tax->name, $wc_not)) {
                    continue;
                }
                else {
                    $label = $tax->labels->name;
                    $value= $tax->name;
                    $html .= '<option value="'.$value.'" ';
                    $html .= ($value == $meta) ? "selected" : null;  
                    $html .=  '> ' . $label . '</option>';
                }
            }
		
        $html .= '</select>';
    }

    echo $html;
        
    die();        
}
add_action('wp_ajax_owl_carousel_terms', 'owl_carousel_terms');
function owl_carousel_terms(){
    
    $html = '';    

    $tax_objects = get_object_taxonomies($_POST['posttype'], 'objects');

    $term_objects = (isset($_POST['taxtype'])) ? get_terms($_POST['taxtype'], 'objects') : null;

    if (metadata_exists('post', $_POST['postid'], 'dd_owl_post_taxonomy_term')) $theterm = get_post_meta( $_POST['postid'], 'dd_owl_post_taxonomy_term', true );
 
    if (null == $tax_objects || is_wp_error($term_objects)){
        $html .= sprintf('<span class="no-cats">' . __('There are no matching terms', 'owl-carousel-2') . '</span>');
    }
    else {
        if (null == $term_objects) {
            $html .= sprintf('<span class="no-cats">' . __('There are no matching terms', 'owl-carousel-2') . '</span>');
        }
        else {
            $html .= '<select id="dd_owl_post_taxonomy_term" name="dd_owl_post_taxonomy_term[]" multiple="multiple" class="dd-owl-multi-select">';
                if (!in_array($theterm, $term_objects) || $term_objects->errors) $html .= '<option value=""> - - Choose A Term - -</option>';
                foreach ($term_objects as $term){
                    $label = $term->name;
                    $value = $term->slug;
                    $html .= '<option value="'.$value.'" ';
                    if ( is_array($theterm) )$html .= ((in_array($value, $theterm)) && (!isset($term_objects->errors))) ? "selected data-selected='true'" : null;  
                    $html .=  '> ' . $label . '</option>';
                }

            $html .= '</select>';
        }
    }

    echo json_encode($html);
        
    die();        
}
add_action('wp_ajax_owl_carousel_posts', 'owl_carousel_posts');
function owl_carousel_posts() {
    global $post;
    $args = array(
        'post_type'              => $_POST['posttype'],
        'posts_per_page'         => '-1',
    );
    
    $query = new WP_Query( $args );
    $html = '';
    // The Loop
    $selectedArray = maybe_unserialize(get_post_meta($_POST['carousel_id'], 'dd_owl_post_ids', true));
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $html .= "<option value='{$post->ID}'";
                if (is_array($selectedArray) ) {
                    if (in_array($post->ID, $selectedArray) ){
                        $html .='selected="selected"';
                    }
                }
            $html .= ">";
            $html .= get_the_title($post->ID);
            $html .= "</option>";
        }
    } else {
        $html .= '<p>No Posts Found</p>';
    }
    echo $html;
    die();
}