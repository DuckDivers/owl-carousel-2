<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// The Shortcode 
function dd_owl_carousel_two($atts){

    /* Enqueue only for shortcode */ 
        wp_enqueue_script('owl-two');
        wp_enqueue_style('owl-carousel-css'); 
        wp_enqueue_style('owl-theme-css');
        wp_enqueue_style('owl-carousel-2');
    
    $atts = shortcode_atts(
        array(
            'post_type' => 'post',
            'items' => '6',
            
        ), $atts, 'dd-owl-carousel' );
    
    // WP_Query arguments
    $args = array(
        'post_type'              => array( 'post' ),
        'post_status'            => array( 'publish' ),
        'orderby'                => 'rand',
    );
        
    // The Query
    $query = new WP_Query( $args );
    $output = '<div class="owl-wrapper"><div class="owl-carousel">';
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $thumb = get_post_thumbnail_id();
            $image = wp_get_attachment_url( $thumb,'medium' ); 
            $output .= '<div><img src="'.$image.'" /></div>';
        }
    }
    $output .= '</div></div>';
        
    ob_start();?>
       <script type="text/javascript">
           jQuery(document).ready(function($){
            $(".owl-carousel").owlCarousel({
                loop:true,
                autoplay: true,
                autoWidth: false,
                center: false,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                margin: 10,
                responsive:{
                    0:{
                        items:1,
                        nav:true
                    },
                    600:{
                        items:4,
                        nav:false
                    },
                    1000:{
                        items: <?php echo $atts['items'];?>,
                        nav:false,
                        }
                }
                });
            });
        </script>
    <?php $output .= ob_get_clean();
    return $output;
}