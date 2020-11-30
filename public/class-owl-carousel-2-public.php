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
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function register_styles() {

        wp_register_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/owl-carousel-2-public.css', array(), $this->version, 'all');
        wp_register_style('owl-carousel-css', plugin_dir_url(__FILE__) . 'css/owl.carousel.min.css');
        wp_register_style('owl-theme-css', plugin_dir_url(__FILE__) . 'css/owl.theme.default.min.css');
        wp_register_style('dd-featherlight-css', plugin_dir_url(__FILE__) . 'css/featherlight.css');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function register_scripts() {
        wp_register_script('owl-two', plugin_dir_url(__FILE__) . 'js/owl.carousel.min.js', array('jquery'), '2.2.1', true);
        wp_register_script('dd-featherlight', plugin_dir_url(__FILE__) . 'js/featherlight.js', array('jquery'), '1.7.13', true);
    }

    /**
     * Include the Shortcode Script
     *
     * @since    1.0.0
     * @param array $atts
     * @return mixed shortcode
     */

    public function dd_owl_carousel_two($atts) {

        /* Enqueue only for shortcode */
        wp_enqueue_script('owl-two');
        wp_enqueue_style('owl-carousel-css');
        wp_enqueue_style('owl-theme-css');
        wp_enqueue_style('owl-carousel-2');

        $atts = shortcode_atts(
            array(
                'id' => '',
            ), $atts, 'dd-owl-carousel');

        $post = get_post($atts['id']);
        $this_carousel = $post->ID;
        // Retrieve Meta from Carousel

        $post_type = get_post_meta($this_carousel, 'dd_owl_post_type', true);
        $per_page = get_post_meta($this_carousel, 'dd_owl_number_posts', true);
        $thumbs = (get_post_meta($this_carousel, 'dd_owl_thumbs', true) == 'checked') ? 'true' : 'false';
        $image_options = get_post_meta($this_carousel, 'dd_owl_image_options', true);
        $excerpt_length = get_post_meta($this_carousel, 'dd_owl_excerpt_length', true);
        $excerpt_more = esc_html(get_post_meta($this_carousel, 'dd_owl_excerpt_more', true));
        $hide_more = (get_post_meta($this_carousel, 'dd_owl_hide_excerpt_more', true) === 'checked') ? 'true' : 'false';
        $css_id = get_post_meta($this_carousel, 'dd_owl_css_id', true);
        $cta_text = esc_html(get_post_meta($this_carousel, 'dd_owl_cta', true));
        $btn_class = get_post_meta($this_carousel, 'dd_owl_btn_class', true);
        $btn_display = get_post_meta($this_carousel, 'dd_owl_btn_display', true);
        $btn_margin = (!empty (get_post_meta($this_carousel, 'dd_owl_btn_margin', true))) ? 'margin: ' . get_post_meta($this_carousel, 'dd_owl_btn_margin', true) . ';' : '';
        $show_cta = (get_post_meta($this_carousel, 'dd_owl_show_cta', true) == 'checked') ? 'true' : 'false';
        $tax_options = get_post_meta($this_carousel, 'dd_owl_tax_options', true);
        $postIDs = get_post_meta($this_carousel, 'dd_owl_post_ids', true);
        $orderby = get_post_meta($this_carousel, 'dd_owl_orderby', true);
        $taxonomy = get_post_meta($this_carousel, 'dd_owl_post_taxonomy_type', true);
        $term = get_post_meta($this_carousel, 'dd_owl_post_taxonomy_term', true);
        $centered = (get_post_meta($this_carousel, 'dd_owl_nav_position', true ) == 'centered') ? ' nav-centered' : '';

        $img_atts = array(
            'size' => get_post_meta($this_carousel, 'dd_owl_image_size', true),
            'width' => get_post_meta($this_carousel, 'dd_owl_img_width', true),
            'height' => get_post_meta($this_carousel, 'dd_owl_img_height', true),
            'crop' => get_post_meta($this_carousel, 'dd_owl_img_crop', true),
            'upscale' => get_post_meta($this_carousel, 'dd_owl_img_upscale', true),
            'options' => get_post_meta($this_carousel, 'dd_owl_image_options', true)
        );

        if ($hide_more == 'true') $excerpt_more = '';

        if ($image_options == 'lightbox') {
            wp_enqueue_script('dd-featherlight');
            wp_enqueue_style('dd-featherlight-css');
        }
        // Check if is attachment / media do subroutine
        if ($post_type == 'attachment') {
            $output = $this->do_media_carousel($this_carousel, $css_id, get_post_meta($this_carousel, 'dd_owl_media_items', true), $img_atts);
            goto beforeCarousel;
        }

        if ($orderby == 'menu') {
            $order = 'ASC';
        } elseif ($orderby == 'rand') {
            $orderby = 'rand';
            $order = 'ASC';
        } else {
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
        if ($tax_options == 'postID') {
            $posts = maybe_unserialize($postIDs);
            $args = array(
                'post_type' => $post_type,
                'post__in' => $posts,
            );
        } // if it's featured products
        elseif ($tax_options == 'featured_product') {
            $tax_query[] = array(
                'taxonomy' => 'product_visibility',
                'field' => 'name',
                'terms' => 'featured',
                'operator' => 'IN',
            );
            $args = array(
                'post_status' => 'publish',
                'post_type' => 'product',
                'tax_query' => $tax_query
            );
        } // if it's product type by tax
        elseif ($post_type == 'product' && $tax_options == 'taxonomy') {
            $args = array(
                'post_type' => array('product'),
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'product_cat',
                        'terms' => $term,
                        'field' => 'slug',
                        'operator' => 'IN',
                    )
                )
            );
        } elseif ($post_type !== 'product' && $tax_options == 'taxonomy') {
            $tax_query = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $term,
                    'operator' => 'IN',
                )
            );

            $args = array(
                'post_type' => $post_type,
                'tax_query' => $tax_query,
            );

        } // if is Show Only Tax
        else {
            // WP_Query arguments
            $args = array(
                'post_type' => array($post_type),
                'post_status' => array('publish'),
            );
        }
        $standard_args = array(
            'orderby' => $orderby,
            'order' => $order,
            'posts_per_page' => $per_page
        );
        $args = array_merge($args, $standard_args);
        // The Query
        if ($tax_options !== 'show_tax_only') {
            /**
             * Filters the Query Args
             *
             * Since 1.2.5
             *
             * @param array $args The arguments created for the WP_Query to get the carousel items
             * @param int The post ID of the carousel
             */
            $query = new WP_Query(apply_filters('dd_carousel_filter_query_args', $args, $this_carousel));

            //Owl Carousel Wrapper
            $output = '<div class="owl-wrapper"><div id="' . $css_id . '" class="owl-carousel owl-theme'.$centered.'">';
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();

                    // Retrieve Variables
                    $title = get_the_title();
                    $link = get_the_permalink();
                    $thumb = get_post_thumbnail_id();
                    $output .= '<div class="item"><div class="item-inner">';

                    // Add Hook before start of Carousel Content
                    ob_start();
                    do_action('dd-carousel-before-content', $atts['id']);
                    $hooked_start = ob_get_contents();
                    ob_end_clean();
                    $output .= $hooked_start;

                    // Show Image if Checked
                    if ($thumbs == 'true') {
                        if (0 !== $thumb) {
                            $output .= $this->get_post_image($img_atts, $thumb, $link);
                        }
                    }
                    // Add filter to change heading type
                    $title_heading = apply_filters('dd_carousel_filter_title_heading', get_post_meta($this_carousel, 'dd_owl_title_heading', true));

                    if (null == get_post_meta($this_carousel, 'dd_owl_show_title', true)) $output .= "<{$title_heading}>{$title}</{$title_heading}>";

                    if (has_excerpt()) {
                        $excerpt = strip_shortcodes(get_the_excerpt());
                        $excerpt = wp_trim_words($excerpt, $excerpt_length, $excerpt_more);
                        /**
                         * Filter dd_carousel_filter_excerpt
                         *
                         * Since 1.2.6
                         *
                         * @param string $excerpt
                         * @param int Post ID
                         */
                        $output .= apply_filters('dd_carousel_filter_excerpt', $excerpt, $this_carousel);
                    } else {
                        $theContent = apply_filters('the_content', get_the_content());
                        $theContent = strip_shortcodes($theContent);
                        $output .= apply_filters('dd_carousel_filter_excerpt', wp_trim_words($theContent, $excerpt_length, $excerpt_more), $this_carousel);
                    }
                    if ($show_cta == 'true') {
                        $link = get_the_permalink();
                        $output .= "<p class='owl-btn-wrapper'><a href='{$link}' class='carousel-button {$btn_class}' style='display: {$btn_display};{$btn_margin}'>{$cta_text}</a></p>";
                    }
                    $output .= '</div>';
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
            $output = '<div class="owl-wrapper"><div id="' . $css_id . '" class="owl-carousel owl-theme'.$centered.'">';
            foreach ($term as $theTerm) {
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
                if ($thumbs == 'true') {

                    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                    $thumb = wp_get_attachment_url($thumbnail_id);

                    if (0 !== $thumb) {
                            $output .= $this->get_post_image($img_atts, $thumb, $link);
                    }
                }

                // Add filter to change heading type
                $title_heading = get_post_meta($this_carousel, 'dd_owl_title_heading', true);

                if (null == get_post_meta($this_carousel, 'dd_owl_show_title', true)) $output .= "<{$title_heading}>{$title}</{$title_heading}>";

                if (intval($excerpt_length) > 0) {
                    $output .= wp_trim_words($category->description, $excerpt_length);
                }
                if ($show_cta == 'true') {
                    $output .= "<p class='owl-btn-wrapper'><a href='{$link}' class='carousel-button {$btn_class}' style='display: {$btn_display};{$btn_margin}'>{$cta_text}</a></p>";
                }
                $output .= '</div>';
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
        beforeCarousel:
        // Get Owl Meta for Carousel Init
        $loop = (get_post_meta($this_carousel, 'dd_owl_loop', true) === 'checked') ? 'true' : 'false';
        $center = (get_post_meta($this_carousel, 'dd_owl_center', true) === 'checked') ? 'true' : 'false';
        $duration = get_post_meta($this_carousel, 'dd_owl_duration', true);
        $transition = get_post_meta($this_carousel, 'dd_owl_transition', true);
        $stop = (get_post_meta($this_carousel, 'dd_owl_stop', true) === 'checked') ? 'true' : 'false';
        $navs = (get_post_meta($this_carousel, 'dd_owl_navs', true) === 'checked') ? 'true' : 'false';
        $dots = (get_post_meta($this_carousel, 'dd_owl_dots', true) === 'checked') ? 'true' : 'false';
        $margin = get_post_meta($this_carousel, 'dd_owl_margin', true);
        $get_prev = (!empty( get_post_meta( $this_carousel, 'dd_owl_prev', true ))) ? html_entity_decode(get_post_meta($this_carousel, 'dd_owl_prev', true)) : '&lt;';
        $get_next = (!empty( get_post_meta( $this_carousel, 'dd_owl_next', true ))) ? html_entity_decode(get_post_meta($this_carousel, 'dd_owl_next', true)) : '&gt;';
        
        $prev = apply_filters('dd_carousel_filter_prev', $get_prev, $this_carousel);
        $next = apply_filters('dd_carousel_filter_next', $get_next, $this_carousel);

        // Get Responsive Settings
        $items_width1 = intval(get_post_meta($this_carousel, 'dd_owl_items_width1', true));
        $items_width2 = intval(get_post_meta($this_carousel, 'dd_owl_items_width2', true));
        $items_width3 = intval(get_post_meta($this_carousel, 'dd_owl_items_width3', true));
        $items_width4 = intval(get_post_meta($this_carousel, 'dd_owl_items_width4', true));
        $items_width5 = intval(get_post_meta($this_carousel, 'dd_owl_items_width5', true));
        $items_width6 = intval(get_post_meta($this_carousel, 'dd_owl_items_width6', true));

        // Output the Script
        $output .= '<script type="text/javascript" async>';
        $owl_script = "jQuery(document).ready(function($){
            $('#{$css_id}').owlCarousel({
                'loop':{$loop},
                'autoplay' : true,
                'autoplayTimeout' : {$duration},
                'smartSpeed' : {$transition},
                'fluidSpeed' : {$transition},
                'autoplaySpeed' : {$transition},
                'navSpeed' : {$transition},
                'dotsSpeed' : {$transition},
                'margin': {$margin},
                'autoplayHoverPause' : {$stop},
                'center' : {$center},
                'responsiveRefreshRate' : 200,
                'slideBy' : 1,
                'mergeFit' : true,
                'mouseDrag' : true,
                'touchDrag' : true,
                'nav' : {$navs},
                'navText' : ['{$prev}','{$next}'],
                'dots' : {$dots},
                'responsive':{
                    0:{items:{$items_width1}},
                    480:{items:{$items_width2}},
                    768:{items:{$items_width3}},
                    991:{items:{$items_width4}},
                    1200:{items:{$items_width5}},
                    1500:{items:{$items_width6}},
                    },
                });
            });";
        /**
         * Filter the Owl Carousel Output Script
         *
         * Since 1.2.6
         */
        $output .= apply_filters('dd_filter_owl_carousel_script', $owl_script, $this_carousel);
        $output .= '</script>';
        // Reset Post Data
        wp_reset_postdata();
        return $output;
    }


    /**
     * @param integer $carousel_id id of this carousel (post->ID)
     * @param string $css_id the css ID as specified in the bilder page
     * @param array $media media element ids
     * @return string $output
     * @since 1.3
     */
    public function do_media_carousel($carousel_id, $css_id, $media, $img_atts) {
        // Retrieve atts
        $use_lightbox = ('lightbox' == get_post_meta($carousel_id, 'dd_owl_image_options', true)) ? true : false;
        $centered = (get_post_meta($carousel_id, 'dd_owl_nav_position', true ) == 'centered') ? ' nav-centered' : '';
        $use_caption = ("checked" == get_post_meta($carousel_id, 'dd_owl_use_image_caption', true)) ? true : false;

        $output = '<div class="owl-wrapper"><div id="' . $css_id . '" class="owl-carousel owl-theme'.$centered.'">';
        if ('custom' == $img_atts['size']) {
            $img_width = (intval($img_atts['width']));
            if ($img_width <= 300) {
                $size = 'medium';
            } elseif ($img_width <= 600) {
                $size = 'large';
            } else {
                $size = 'full';
            }
        } else {
            $size = $img_atts['size'];
        }
        foreach ($media as $image_id) {
            $my_image = wp_get_attachment_image_src($image_id, $size);
            if ('custom' == $img_atts['size']) {
                $img_url = $my_image[0];
                $image = dd_aq_resize($img_url, $img_atts['width'], $img_atts['height'], $img_atts['crop'], 'true', $img_atts['upscale']);
            } else {
                $image = $my_image[0];
            }
            $output .= '<div class="item">';
            if ($use_lightbox){
                $output .= sprintf('<a href="%s" class="lightbox" data-featherlight="%s">', $image , $image );
            }
            $output .= '<img src="' . $image . '" alt="' . get_post_meta($image_id, '_wp_attachment_image_alt', TRUE) . '">';
            if ($use_lightbox) echo '</a>';
            if ($use_caption) {
                $the_caption = '<div class="dd-owl-image-caption">';
                $the_caption .= (false !== ($caption = wp_get_attachment_caption($image_id))) ? $caption : '';
                $the_caption .= '</div>';
                /**
                 * Filters the Caption Output
                 *
                 * Since 1.3
                 *
                 * @param string $the_caption The caption created
                 * @param string $caption the `wp_get_attachment_caption` - Caption.
                 */
                $output .= apply_filters('dd_carousel_filter_caption', $the_caption, $caption);
            }
            $output .= '</div>';
        }
        $output .= '</div>'; // End Carousel
        return $output;
    }

    /**
     * Retrieve the image for a post carousel
     * @param  array $img_atts carousel image attributes
     * @param int $thumb the attachmenrt ID
     * @param string $link permalink to post if used
     * @return string
     */
    public function get_post_image( $img_atts, $thumb, $link){
        $output = '';

        if ('custom' == $img_atts['size']) {
            $img_width = (intval($img_atts['width']));
            if ($img_width <= 300) {
                $size = 'medium';
            } elseif ($img_width <= 600) {
                $size = 'large';
            } else {
                $size = 'full';
            }
        } else {
            $size = $img_atts['size'];
        }

        if (!empty($thumb)) {
            $my_image = wp_get_attachment_image_src($thumb, $size);
            if ('custom' == $img_atts['size']) {
                $img_url = $my_image[0];
                $image = dd_aq_resize($img_url, $img_atts['width'], $img_atts['height'], $img_atts['crop'], 'true', $img_atts['upscale']);
            } else {
                $image = $my_image[0];
            }
        } else {
            $image = plugin_dir_url(__FILE__) . 'images/placeholder.png';
        }
        if (in_array($img_atts['options'], array('link', 'lightbox'))) {
            if ($img_atts['options'] == 'lightbox') {
                $class = 'data-featherlight="' . $image . '" class="lightbox"';
            } else {
                $class = 'class="linked-image"';
            }

            $output .= '<a href="' . esc_url($link) . '" ' . $class . '>';
        }
        if (!empty($thumb)) {
            $output .= '<img src="' . $image . '" class="carousel-image"/>';
        } else {
            $output .= '<figure class="no-image" style="height: ' . $img_atts['height'] . 'px; max-height: ' . $img_atts['height'] . 'px; width: ' . $img_atts['width'] . 'px; background: url(' . $image . ');"></figure>';
        }

        $output .= ($img_atts['options'] == 'link' || $img_atts['options'] == 'lightbox') ? '</a>' : '';

        return $output;
    }
}
