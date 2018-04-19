<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.howardehrenberg.com
 * @since      1.0.0
 *
 * @package    Owl_Carousel_2
 * @subpackage Owl_Carousel_2/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Owl_Carousel_2
 * @subpackage Owl_Carousel_2/public
 * @author     Howard Ehrenberg <howard@howardehrenberg.com>
 */
class Owl_Carousel_2_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_styles() {

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/owl-carousel-2-public.css', array(), $this->version, 'all' );
        wp_register_style( 'owl-carousel-css', plugin_dir_url(__FILE__) . 'css/owl.carousel.min.css');
        wp_register_style( 'owl-theme-css', plugin_dir_url(__FILE__) . 'css/owl.theme.default.css');

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_scripts() {

        wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/owl-carousel-2-public.js', array( 'jquery' ), $this->version, false );
        wp_register_script ('owl-two', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.min.js', array('jquery'), '2.2.1', true);
	}
    
    /**
     * Include the Shortcode Script
     *
     * @since    1.0.0
     */
    
    public function dd_owl_carousel_two($atts){

    /* Enqueue only for shortcode */ 
        wp_enqueue_script('owl-two');
        wp_enqueue_style('owl-carousel-css'); 
        wp_enqueue_style('owl-theme-css');
        wp_enqueue_style('owl-carousel-2');
    
    $atts = shortcode_atts(
        array(
            'id'    => '',    
        ), $atts, 'dd-owl-carousel' );

    $post = get_post($atts['id']);
        
    // Retrieve Meta from Carousel    
    
    $post_type = get_post_meta($post->ID, 'dd_owl_post_type', true);
    $per_page = get_post_meta( $post->ID, 'dd_owl_number_posts', true );        
	$thumbs = (get_post_meta( $post->ID, 'dd_owl_thumbs', true ) == 'checked') ? 'true' : 'false';
            
    // WP_Query arguments
    $args = array(
        'post_type'              => array( $post_type ),
        'post_status'            => array( 'publish' ),
        'posts_per_page'         => $per_page,
        'orderby'                => 'rand',
    );
        
    // The Query
    $query = new WP_Query( $args );
    $output = '<div class="owl-wrapper"><div id="carousel-'.$post->ID.'" class="owl-carousel owl-theme">';
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $output .= '<div class="item">';
            // Show Image if Checked
            if ($thumbs == 'true'){
                $thumb = get_post_thumbnail_id();
                $image = wp_get_attachment_url( $thumb,'medium' ); 
                $output .= '<img src="'.$image.'" />';
            }
            $title = get_the_title();
            $output .= "<h4>{$title}</h4>";
            
            if (has_excerpt()){
                 $excerpt = strip_shortcodes(get_the_excerpt());
                 $excerpt = wp_trim_words($excerpt, 40, '...');
                 $output .= $excerpt;
                } else {
                 $theContent = apply_filters('the_content', get_the_content()); 
                 $theContent = strip_shortcodes($theContent);
                 $output .= wp_trim_words( $theContent, 10, '...' );
                }
            
            $output .= '</div>';
        }
    }
    $output .= '</div></div>';
    
    // Get Owl Meta
	$loop = (get_post_meta( $post->ID, 'dd_owl_loop', true ) === 'checked') ? 'true' : 'false';
	$center = (get_post_meta( $post->ID, 'dd_owl_center', true ) === 'checked') ? 'true' : 'false';
	$mousedrag = get_post_meta( $post->ID, 'dd_owl_mousedrag', true );
    $duration = get_post_meta( $post->ID, 'dd_owl_duration', true );
    $transition = get_post_meta( $post->ID, 'dd_owl_transition', true );
    $stop = (get_post_meta( $post->ID, 'dd_owl_stop', true ) === 'checked') ? 'true' : 'false';
    $dd_owl_orderby = get_post_meta( $post->ID, 'dd_owl_orderby', true );
    $navs = (get_post_meta( $post->ID, 'dd_owl_navs', true ) === 'checked') ? 'true' : 'false';
    $dots = (get_post_meta( $post->ID, 'dd_owl_dots', true ) === 'checked') ? 'true' : 'false';

    $items_width1 = intval(get_post_meta($post->ID, 'dd_owl_items_width1', true));
    $items_width2 = intval(get_post_meta($post->ID, 'dd_owl_items_width2', true));
    $items_width3 = intval(get_post_meta($post->ID, 'dd_owl_items_width3', true));
    $items_width4 = intval(get_post_meta($post->ID, 'dd_owl_items_width4', true));
    $items_width5 = intval(get_post_meta($post->ID, 'dd_owl_items_width5', true));
    $items_width6 = intval(get_post_meta($post->ID, 'dd_owl_items_width6', true));
   
    $output .="
       <script type='text/javascript'>
           jQuery(document).ready(function($){
            $('#carousel-{$post->ID}').owlCarousel({
                loop:{$loop},
                autoplay : true,
                autoplayTimeout : {$duration},
                smartSpeed : {$transition},
                fluidSpeed : {$transition},
                autoplaySpeed : {$transition},
                navSpeed : {$transition},
                dotsSpeed : {$transition},
                autoplayHoverPause : true,
                nav : {$navs},
                navText : ['&lt;','&gt;'],
                dots : {$dots},
                center : {$center},
                responsiveRefreshRate : 200,
                slideBy : 1,
                mergeFit : true,
                mouseDrag : true,
                touchDrag : true,
                responsive:{
                    0:{items:{$items_width1}},
                    480:{items:{$items_width2}},
                    768:{items:{$items_width3}},
                    991:{items:{$items_width4}},
                    1200:{items:{$items_width5}},
                    1500:{items:{$items_width6}},
                    }
                });
            });
        </script>";

    return $output;
}
    
}
