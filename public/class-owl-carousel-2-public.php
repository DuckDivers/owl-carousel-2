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
        wp_register_style( 'owl-theme-css', plugin_dir_url(__FILE__) . 'css/owl.theme.default.min.css');
        wp_register_style( 'dd-featherlight-css', plugin_dir_url(__FILE__) . 'css/featherlight.css');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_scripts() {

        wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/owl-carousel-2-public.js', array( 'jquery' ), $this->version, false );
        wp_register_script ('owl-two', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.min.js', array('jquery'), '2.2.1', true);
        wp_register_script ('dd-featherlight', plugin_dir_url(__FILE__) . 'js/featherlight.js', array('jquery'), '1.7.13', true);
	}

    /**
	 * Include the Additional Functions for Admin
	 *
	 * @since    1.0.0
	 */

    private function include_admin_classes() {
        include_once( plugin_dir_path(__FILE__) . 'class-owl-carousel-2-shortcode.php');
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
		$image_options = get_post_meta( $post->ID, 'dd_owl_image_options', true );
		$excerpt_length = get_post_meta( $post->ID, 'dd_owl_excerpt_length', true );
		$excerpt_more = esc_html(get_post_meta($post->ID, 'dd_owl_excerpt_more', true));
		$hide_more = (get_post_meta( $post->ID, 'dd_owl_hide_excerpt_more', true ) === 'checked') ? 'true' : 'false';
		$css_id = get_post_meta( $post->ID, 'dd_owl_css_id', true );
		$cta_text = esc_html(get_post_meta( $post->ID, 'dd_owl_cta', true ));
		$btn_class = get_post_meta( $post->ID, 'dd_owl_btn_class', true );
		$btn_display = get_post_meta( $post->ID, 'dd_owl_btn_display', true );
		$btn_margin = ( !empty (get_post_meta( $post->ID, 'dd_owl_btn_margin', true ) ) ) ? 'margin: '. get_post_meta($post->ID, 'dd_owl_btn_margin', true). ';' : '';
		$show_cta = (get_post_meta( $post->ID, 'dd_owl_show_cta', true ) == 'checked') ? 'true' : 'false';
		$tax_options = get_post_meta( $post->ID, 'dd_owl_tax_options', true);
		$postIDs = get_post_meta($post->ID, 'dd_owl_post_ids', true);
		$orderby = get_post_meta( $post->ID, 'dd_owl_orderby', true );
		$taxonomy = get_post_meta( $post->ID, 'dd_owl_post_taxonomy_type', true );
		$term = get_post_meta( $post->ID, 'dd_owl_post_taxonomy_term', true );

    if ($hide_more == 'true') $excerpt_more = '';

    if ($image_options == 'lightbox') {
        wp_enqueue_script('dd-featherlight');
        wp_enqueue_style('dd-featherlight-css');
    }

    if ($orderby == 'menu'){
        $order = 'ASC';
    } elseif ($orderby == 'rand') {
        $orderby = 'rand';
        $order = 'ASC';
    }
    else {
        $new_order = explode('_', $orderby);
        $orderby = $new_order['0'];
        $order = $new_order['1'];
    }

    /**
     * Init WP Queries
     *
     * @since    1.0.0
     */
    // If its' by post
    if ($tax_options == 'postID'){
        $posts = maybe_unserialize($postIDs);
        $args = array(
            'post_type' => $post_type,
            'post__in' => $posts,
            );
        }
	// if it's featured products
    elseif ($tax_options == 'featured_product'){
        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'operator' => 'IN',
        );
        $args = array(
            'post_status' 		=> 'publish',
            'post_type' 		=> 'product',
            'tax_query' 		=>  $tax_query
	   );
    }
	// if it's product type by tax
    elseif ($post_type == 'product' && $tax_options == 'taxonomy'){
        $args = array(
            'post_type'     => array( 'product' ),
            'tax_query'     => array (
                'relation' => 'AND',
                array (
                    'taxonomy' => 'product_cat',
                    'terms' => $term,
                    'field' => 'slug',
                    'operator' => 'IN',
                )
            )
        );
    }
    elseif ($post_type !== 'product' && $tax_options == 'taxonomy'){
      $tax_query = array(
          'relation' => 'AND',
          array(
            'taxonomy'  => $taxonomy,
            'field'     => 'slug',
            'terms'     => $term,
            'operator'  => 'IN',
              )
          );

        $args = array(
          'post_type'   => $post_type,
          'tax_query'   => $tax_query,
        );

    }
	// if is Show Only Tax
    else {
    // WP_Query arguments
    $args = array(
        'post_type'              => array( $post_type ),
        'post_status'            => array( 'publish' ),
        );
    }
    $standard_args = array(
        'orderby'        => $orderby,
        'order'          => $order,
        'posts_per_page' => $per_page
    );
    $args = array_merge( $args, $standard_args);
    // The Query
	if ($tax_options !== 'show_tax_only'){
		$query = new WP_Query( apply_filters('dd_carousel_filter_query_args', $args, $post->ID) );

		//Owl Carousel Wrapper
		$output = '<div class="owl-wrapper"><div id="'.$css_id.'" class="owl-carousel owl-theme">';
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				// Retrieve Variables
				$title = get_the_title();
				$link = get_the_permalink();

				$output .= '<div class="item"><div class="item-inner">';

				// Add Hook before start of Carousel Content
				ob_start();
					do_action('dd-carousel-before-content', $atts['id']);
					$hooked_start = ob_get_contents();
				ob_end_clean();
				$output .= $hooked_start;

				// Show Image if Checked
				if ($thumbs == 'true'){

					// retreive image dimensions from settings

					$dd_owl_img_width = get_post_meta( $post->ID, 'dd_owl_img_width', true );
					$dd_owl_img_height = get_post_meta( $post->ID, 'dd_owl_img_height', true );
					$dd_owl_img_crop = get_post_meta( $post->ID, 'dd_owl_img_crop', true );
					$dd_owl_img_upscale = get_post_meta( $post->ID, 'dd_owl_img_upscale', true );

					$img_width = (intval($dd_owl_img_width));

					if ($img_width <= 300){
						$size = 'medium';
					}
					elseif ($img_width <= 600){
						$size = 'large';
					}
					else {
						$size = 'full';
					}

					$thumb = get_post_thumbnail_id();
					if (!empty($thumb)){
						$img_url = wp_get_attachment_url( $thumb, $size);
						$image = dd_aq_resize( $img_url, $dd_owl_img_width, $dd_owl_img_height, $dd_owl_img_crop, 'true', $dd_owl_img_upscale );
					} else {
						$image = plugin_dir_url( __FILE__ ) . 'images/placeholder.png';
					}
					if ($image_options == 'link' || $image_options == 'lightbox'){
						if ($image_options == 'lightbox') {
							$class = 'data-featherlight="'.$image.'" class="lightbox"';
						}
						else {
							$class = 'class="linked-image"';
						}

						$output .= sprintf('<a href="%s" %s>', $link, $class);
						}
					if (!empty($thumb)){
						$output .= '<img src="'.$image.'" class="carousel-image"/>';
					}else {
						$output .= '<figure class="no-image" style="height: '.$dd_owl_img_height.'px; max-height: '.$dd_owl_img_height.'px; width: '.$dd_owl_img_width.'px; background: url('.$image.');"></figure>';
					}

					$output .= ($image_options == 'link' || $image_options == 'lightbox') ? '</a>' : '';
				}
				// Add filter to change heading type

				$title_heading = get_post_meta( $post->ID, 'dd_owl_title_heading', true );

				if (null == get_post_meta( $post->ID, 'dd_owl_show_title', true ) ) $output .= "<{$title_heading}>{$title}</{$title_heading}>";

				if (has_excerpt()){
					 $excerpt = strip_shortcodes(get_the_excerpt());
					 $excerpt = wp_trim_words($excerpt, $excerpt_length, $excerpt_more);
					 $output .= $excerpt;
					} else {
					 $theContent = apply_filters('the_content', get_the_content());
					 $theContent = strip_shortcodes($theContent);
					 $output .= wp_trim_words( $theContent, $excerpt_length, $excerpt_more );
					}
				if ($show_cta == 'true'){
						$link = get_the_permalink();
						$output .= "<p class='owl-btn-wrapper'><a href='{$link}' class='carousel-button {$btn_class}' style='display: {$btn_display};{$btn_margin}'>{$cta_text}</a></p>";
					}
				$output .='</div>';
				// Add Hook After End of Carousel Content
				ob_start();
					do_action('dd-carousel-after-content', $atts['id']);
					$hooked_end = ob_get_contents();
				ob_end_clean();
				$output .= $hooked_end;
				$output .= '</div>';
			}
		}
		$output .= '</div></div>';
	} else {
	// Is term list only
		$output = '<div class="owl-wrapper"><div id="'.$css_id.'" class="owl-carousel owl-theme">';
		foreach ($term as $theTerm){
			$category = get_term_by('slug', $theTerm, $taxonomy);
				// Retrieve Variables
				$title = $category->name;
				$link = get_category_link($category->term_id);
				$output .= '<div class="item"><div class="item-inner">';

				// Add Hook before start of Carousel Content
				ob_start();
					do_action('dd-carousel-before-content', $atts['id']);
					$hooked_start = ob_get_contents();
				ob_end_clean();
				$output .= $hooked_start;

				// Show Image if Checked
				if ($thumbs == 'true'){

					// retreive image dimensions from settings

					$dd_owl_img_width = get_post_meta( $post->ID, 'dd_owl_img_width', true );
					$dd_owl_img_height = get_post_meta( $post->ID, 'dd_owl_img_height', true );
					$dd_owl_img_crop = get_post_meta( $post->ID, 'dd_owl_img_crop', true );
					$dd_owl_img_upscale = get_post_meta( $post->ID, 'dd_owl_img_upscale', true );

					$img_width = (intval($dd_owl_img_width));

					if ($img_width <= 300){
						$size = 'medium';
					}
					elseif ($img_width <= 600){
						$size = 'large';
					}
					else {
						$size = 'full';
					}

					$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true);
					$thumb = wp_get_attachment_url( $thumbnail_id );

					if (!empty($thumb)){
						$image = dd_aq_resize( $thumb, $dd_owl_img_width, $dd_owl_img_height, $dd_owl_img_crop, 'true', $dd_owl_img_upscale );
					} else {
						$image = plugin_dir_url( __FILE__ ) . 'images/placeholder.png';
					}
					if ($image_options == 'link' || $image_options == 'lightbox'){
						if ($image_options == 'lightbox') {
							$class = 'data-featherlight="'.$image.'" class="lightbox"';
						}
						else {
							$class = 'class="linked-image"';
						}

						$output .= sprintf('<a href="%s" %s>', $link, $class);
						}
					if (!empty($thumb)){
						$output .= '<img src="'.$image.'" class="carousel-image"/>';
					}else {
						$output .= '<figure class="no-image" style="height: '.$dd_owl_img_height.'px; max-height: '.$dd_owl_img_height.'px; width: '.$dd_owl_img_width.'px; background: url('.$image.');"></figure>';
					}

					$output .= ($image_options == 'link' || $image_options == 'lightbox') ? '</a>' : '';
				}

				// Add filter to change heading type
				$title_heading = get_post_meta( $post->ID, 'dd_owl_title_heading', true );

				if (null == get_post_meta( $post->ID, 'dd_owl_show_title', true ) ) $output .= "<{$title_heading}>{$title}</{$title_heading}>";

				if (intval($excerpt_length) > 0 ){
					$output .= wp_trim_words($category->description, $excerpt_length);
				}
				if ($show_cta == 'true'){
						$output .= "<p class='owl-btn-wrapper'><a href='{$link}' class='carousel-button {$btn_class}' style='display: {$btn_display};{$btn_margin}'>{$cta_text}</a></p>";
					}
				$output .='</div>';
				// Add Hook After End of Carousel Content
				ob_start();
					do_action('dd-carousel-after-content', $atts['id']);
					$hooked_end = ob_get_contents();
				ob_end_clean();
				$output .= $hooked_end;
				$output .= '</div>';
			}

		$output .= '</div></div>';

		} // EndIF

    // Get Owl Meta for Carousel Init
		$loop = (get_post_meta( $post->ID, 'dd_owl_loop', true ) === 'checked') ? 'true' : 'false';
		$center = (get_post_meta( $post->ID, 'dd_owl_center', true ) === 'checked') ? 'true' : 'false';
		$mousedrag = get_post_meta( $post->ID, 'dd_owl_mousedrag', true );
		$duration = get_post_meta( $post->ID, 'dd_owl_duration', true );
		$transition = get_post_meta( $post->ID, 'dd_owl_transition', true );
		$stop = (get_post_meta( $post->ID, 'dd_owl_stop', true ) === 'checked') ? 'true' : 'false';
		$dd_owl_orderby = get_post_meta( $post->ID, 'dd_owl_orderby', true );
		$navs = (get_post_meta( $post->ID, 'dd_owl_navs', true ) === 'checked') ? 'true' : 'false';
		$dots = (get_post_meta( $post->ID, 'dd_owl_dots', true ) === 'checked') ? 'true' : 'false';
		$margin = get_post_meta( $post->ID, 'dd_owl_margin', true );

    $items_width1 = intval(get_post_meta($post->ID, 'dd_owl_items_width1', true));
    $items_width2 = intval(get_post_meta($post->ID, 'dd_owl_items_width2', true));
    $items_width3 = intval(get_post_meta($post->ID, 'dd_owl_items_width3', true));
    $items_width4 = intval(get_post_meta($post->ID, 'dd_owl_items_width4', true));
    $items_width5 = intval(get_post_meta($post->ID, 'dd_owl_items_width5', true));
    $items_width6 = intval(get_post_meta($post->ID, 'dd_owl_items_width6', true));

    $output .= "
       <script type='text/javascript' async>
           jQuery(document).ready(function($){
            $('#{$css_id}').owlCarousel({
                loop:{$loop},
                autoplay : true,
                autoplayTimeout : {$duration},
                smartSpeed : {$transition},
                fluidSpeed : {$transition},
                autoplaySpeed : {$transition},
                navSpeed : {$transition},
                dotsSpeed : {$transition},
                margin: {$margin},
                autoplayHoverPause : {$stop},
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
	// Reset Post Data
	wp_reset_postdata();
    return $output;
    }

}
