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
     * Carousel ID, the Post ID of the current carousel
     * @access public
     * @var string $carousel_id;
     */

    public $carousel_id;

    /**
     * All of the post meta for the current carousel
     * @var array $meta
     */
    protected $meta = array();

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
        wp_register_style('owl-carousel-css', plugin_dir_url(__FILE__) . 'css/owl.carousel.min.css');
        wp_register_style('owl-theme-css', plugin_dir_url(__FILE__) . 'css/owl.theme.default.min.css');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function register_scripts() {
        wp_register_script('owl-two', plugin_dir_url(__FILE__) . 'js/owl.carousel.min.js', array('jquery'), '2.2.1', true);
        wp_register_script('dd-featherlight', plugin_dir_url(__FILE__) . 'js/featherlight.min.js', array('jquery'), '1.7.13', true);
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
        $this->carousel_id = $post->ID;

        // Get Post Meta Array
        $this->get_carousel_meta();

        if ($this->meta['hide_more'] == 'true') $this->meta['excerpt_more'] = '';

        if ($this->meta['image_options'] == 'lightbox') {
            wp_enqueue_script('dd-featherlight');
        }
        // Check if is attachment / media do subroutine
        if ($this->meta['post_type'] === 'attachment') {
            $output = $this->do_media_carousel();
        } elseif ($this->meta['post_type'] === 'reviews') {
            $output = $this->do_review_carousel();
        } else {

            if ($this->meta['orderby'] === 'menu') {
                $order = 'ASC';
            } elseif ($this->meta['orderby'] == 'rand') {
                $orderby = 'rand';
                $order = 'ASC';
            } else {
                $new_order = explode('_', $this->meta['orderby']);
                $orderby = $new_order['0'];
                $order = $new_order['1'];
            }

            /**
             * Init WP Queries
             *
             * @since    1.0.0
             */

            // If its' by post
            if ($this->meta['tax_options'] == 'postID') {
                $posts = maybe_unserialize($this->meta['postIDs']);
                $args = array(
                    'post_type' => $this->meta['post_type'],
                    'post__in' => $posts,
                );
            } // if it's featured products
            elseif ($this->meta['tax_options'] == 'featured_product') {
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
            elseif ($this->meta['post_type'] == 'product' && $this->meta['tax_options'] == 'taxonomy') {
                $args = array(
                    'post_type' => array('product'),
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'product_cat',
                            'terms' => $this->meta['term'],
                            'field' => 'slug',
                            'operator' => 'IN',
                        )
                    )
                );
            } elseif ($this->meta['post_type'] !== 'product' && $this->meta['tax_options'] == 'taxonomy') {
                $tax_query = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => $this->meta['taxonomy'],
                        'field' => 'slug',
                        'terms' => $this->meta['term'],
                        'operator' => 'IN',
                    )
                );

                $args = array(
                    'post_type' => $this->meta['post_type'],
                    'tax_query' => $tax_query,
                );

            } // if is Show Only Tax
            else {
                // WP_Query arguments
                $args = array(
                    'post_type' => array($this->meta['post_type']),
                    'post_status' => array('publish'),
                );
            }
            $standard_args = array(
                'orderby' => $orderby,
                'order' => $order,
                'posts_per_page' => $this->meta['per_page']
            );
            $args = array_merge($args, $standard_args);
            // The Query
            if ($this->meta['tax_options'] !== 'show_tax_only') {
                /**
                 * Filters the Query Args
                 *
                 * Since 1.2.5
                 *
                 * @param array $args The arguments created for the WP_Query to get the carousel items
                 * @param int The post ID of the carousel
                 */
                $query = new WP_Query(apply_filters('dd_carousel_filter_query_args', $args, $this->carousel_id));

                //Owl Carousel Wrapper
                $output = '<div class="owl-wrapper"><div id="' . $this->meta['css_id'] . '" class="owl-carousel owl-theme' . $this->meta['centered'] . '">';
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
                        if ($this->meta['thumbs'] == 'true') {
                            if (0 !== $thumb) {
                                $output .= $this->get_post_image($this->meta['img_atts'], $thumb, $link);
                            }
                        }
                        // Add filter to change heading type
                        $title_heading = apply_filters('dd_carousel_filter_title_heading', get_post_meta($this->carousel_id, 'dd_owl_title_heading', true));

                        if (null == get_post_meta($this->carousel_id, 'dd_owl_show_title', true)) $output .= "<{$title_heading}>{$title}</{$title_heading}>";

                        if (has_excerpt()) {
                            $excerpt = strip_shortcodes(get_the_excerpt());
                            $excerpt = wp_trim_words($excerpt, $this->meta['excerpt_length'], $this->meta['excerpt_more']);
                            /**
                             * Filter dd_carousel_filter_excerpt
                             *
                             * Since 1.2.6
                             *
                             * @param string $excerpt
                             * @param int Post ID
                             */
                            $output .= apply_filters('dd_carousel_filter_excerpt', $excerpt, $this->carousel_id);
                        } else {
                            $theContent = apply_filters('the_content', get_the_content());
                            $theContent = strip_shortcodes($theContent);
                            $output .= apply_filters('dd_carousel_filter_excerpt', wp_trim_words($theContent, $this->meta['excerpt_length'], $this->meta['excerpt_more']), $this->carousel_id);
                        }
                        if ($this->meta['show_cta'] == 'true') {
                            $link = get_the_permalink();
                            $output .= "<p class='owl-btn-wrapper'><a href='{$link}' class='carousel-button {$this->meta['btn_class']}' style='display: {$this->meta['btn_display']};{$this->meta['btn_margin']}'>{$this->meta['cta_text']}</a></p>";
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
                $output = '<div class="owl-wrapper"><div id="' . $this->meta['css_id'] . '" class="owl-carousel owl-theme' . $this->meta['centered'] . '">';
                foreach ($this->meta['term'] as $theTerm) {
                    $category = get_term_by('slug', $theTerm, $this->meta['taxonomy']);
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
                    if ($this->meta['thumbs'] == 'true') {

                        $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);

                        if (0 !== $thumbnail_id) {
                            $output .= $this->get_post_image($this->meta['img_atts'], $thumbnail_id , $link);
                        }
                    }

                    // Add filter to change heading type
                    $title_heading = get_post_meta($this->carousel_id, 'dd_owl_title_heading', true);

                    if (null == get_post_meta($this->carousel_id, 'dd_owl_show_title', true)) $output .= "<{$title_heading}>{$title}</{$title_heading}>";

                    if (intval($this->meta['excerpt_length']) > 0) {
                        $output .= wp_trim_words($category->description, $this->meta['excerpt_length']);
                    }
                    if ($this->meta['show_cta'] == 'true') {
                        $output .= "<p class='owl-btn-wrapper'><a href='{$link}' class='carousel-button {$this->meta['btn_class']}' style='display: {$this->meta['btn_display']};{$this->meta['btn_margin']}'>{$this->meta['cta_text']}</a></p>";
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
        }

        $prev = apply_filters('dd_carousel_filter_prev', $this->meta['prev'], $this->carousel_id);
        $next = apply_filters('dd_carousel_filter_next', $this->meta['next'], $this->carousel_id);

        // Output the Script
        $output .= '<script type="text/javascript" async>';
        $owl_script = "jQuery(function($){
            $('#{$this->meta['css_id']}').owlCarousel({
                'loop':{$this->meta['loop']},
                'autoplay' : {$this->meta['autoplay']},
                'autoplayTimeout' : {$this->meta['duration']},
                'smartSpeed' : {$this->meta['transition']},
                'fluidSpeed' : {$this->meta['transition']},
                'autoplaySpeed' : {$this->meta['transition']},
                'navSpeed' : {$this->meta['transition']},
                'dotsSpeed' : {$this->meta['transition']},
                'margin': {$this->meta['margin']},
                'autoplayHoverPause' : {$this->meta['stop']},
                'center' : {$this->meta['center']},
                'responsiveRefreshRate' : 200,
                'slideBy' : {$this->meta['slideby']},
                'mergeFit' : true,
                'lazyLoad' : {$this->meta['lazy']},
                'mouseDrag' : {$this->meta['mousedrag']},
                'touchDrag' : {$this->meta['touchdrag']},
                'nav' : {$this->meta['navs']},
                'navText' : ['{$prev}','{$next}'],
                'dots' : {$this->meta['dots']},
                'responsive':{
                    0:{items:{$this->meta['items_width1']}},
                    480:{items:{$this->meta['items_width2']}},
                    768:{items:{$this->meta['items_width3']}},
                    991:{items:{$this->meta['items_width4']}},
                    1200:{items:{$this->meta['items_width5']}},
                    1500:{items:{$this->meta['items_width6']}},
                    },
                });
            });";
        /**
         * Filter the Owl Carousel Output Script
         *
         * Since 1.2.6
         */
        $output .= apply_filters('dd_filter_owl_carousel_script', $owl_script, $this->carousel_id);
        $output .= '</script>';
        // Reset Post Data
        wp_reset_postdata();
        return $output;
    }


    /**
     * @param array $media media element ids
     * @param array $img_atts
     * @return string $output
     * @since 1.3
     */
    public function do_media_carousel() {
        // Retrieve atts
        $use_lightbox = ('lightbox' === $this->meta['image_options']) ? true : false;
        $centered = ($this->meta['centered'] === 'centered') ? ' nav-centered' : '';
        $use_caption = ("checked" === get_post_meta($this->carousel_id, 'dd_owl_use_image_caption', true)) ? true : false;
        $img_atts = $this->meta['img_atts'];
        $output = '<div class="owl-wrapper"><div id="' . $this->meta['css_id'] . '" class="owl-carousel owl-theme'.$centered.'">';
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
        foreach (get_post_meta($this->carousel_id, 'dd_owl_media_items', true) as $image_id) {
            $my_image = wp_get_attachment_image_src($image_id, $size);
            if ('custom' == $img_atts['size']) {
                $img_url = $my_image[0];
                $image = dd_aq_resize($img_url, $img_atts['width'], $img_atts['height'], $img_atts['crop'], 'true', $img_atts['upscale']);
            } else {
                $image = $my_image[0];
            }
            $output .= '<div class="item">';
            if ($use_lightbox) {
                $output .= sprintf('<a href="%s" class="lightbox" data-featherlight="%s">', $image, $image);
            }
            if ($this->meta['lazy'] === 'true') {
                $output .= '<img data-src="' . $image . '" alt="' . get_post_meta($image_id, '_wp_attachment_image_alt', TRUE) . '" class="owl-lazy">';
            } else {
                $output .= '<img src="' . $image . '" alt="' . get_post_meta($image_id, '_wp_attachment_image_alt', TRUE) . '">';
            }
            if ($use_lightbox) $output .= '</a>';
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
        $output .= '</div></div>'; // End Carousel
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
        } elseif (empty($img_atts['size'])){
            $size = 'medium';
        } else {
            $size = $img_atts['size'];
        }
        if (!empty($thumb)) {
            $my_image = wp_get_attachment_image_src($thumb, $size);
            if ( false !== $my_image ) {
                if ('custom' == $img_atts['size']) {
                    $img_url = $my_image[0];
                    $image = dd_aq_resize($img_url, $img_atts['width'], $img_atts['height'], $img_atts['crop'], 'true', $img_atts['upscale']);
                } else {
                    $image = $my_image[0];
                }
            } else $image = plugin_dir_url(__FILE__) . 'images/placeholder.png';
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
            if ($this->meta['lazy'] === 'true' ){
                $output .= '<img data-src="' . $image . '" class="carousel-image owl-lazy"/>';
            } else {
                $output .= '<img src="' . $image . '" class="carousel-image"/>';
            }
        } else {
            $output .= '<figure class="no-image" style="height: ' . $img_atts['height'] . 'px; max-height: ' . $img_atts['height'] . 'px; width: ' . $img_atts['width'] . 'px; background: url(' . $image . ');"></figure>';
        }

        $output .= ($img_atts['options'] == 'link' || $img_atts['options'] == 'lightbox') ? '</a>' : '';

        return $output;
    }

    /**
     * Product Review Carousel
     * @param $carousel_id
     * @param $css_id
     * @return string
     */
    public function do_review_carousel() {

        $centered = (get_post_meta($this->carousel_id, 'dd_owl_nav_position', true) == 'centered') ? ' nav-centered' : '';

        $output = '<div class="owl-wrapper"><div id="' . $this->meta['css_id'] . '" class="woocommerce owl-carousel owl-theme' . $centered . '">';

        // WP_Comment_Query arguments
        $args = array(
            'status' => 'approve',
            'type' => 'review',
            'number' => $this->meta['per_page'],
            'post__in' => maybe_unserialize($this->meta['postIDs']),
        );
        /**
         * Filters the Comment Query Args
         *
         * dd_carousel_filter_review_query
         *
         * @param array $args
         * @param int $carousel_id
         */
        $comment_query = new WP_Comment_Query(apply_filters('dd_carousel_filter_review_query', $args, $this->carousel_id));

        // The Comment Loop
        if ($comment_query) {
            foreach ($comment_query->comments as $comment) {
                // Get the comment content
                $theContent = apply_filters('the_content', $comment->comment_content);
                $theContent = strip_shortcodes($theContent);
                // Get the rating
                $rating = get_comment_meta($comment->comment_ID, 'rating', true);

                $product = get_post($comment->comment_post_ID, ARRAY_A);

                $output .= sprintf('<div class="item" id="review" data-comment="%s">', $comment->comment_ID);

                $label = sprintf( __( 'Rated %s out of 5', 'owl-carousel-2' ), $rating );

                $output .= '<div class="review-head">';

                $date = date(apply_filters('dd_carousel_filter_comment_date_format', 'h/d/y', $this->carousel_id), strtotime($comment->comment_date));

                $review_date = ($this->meta['review_date']) ? '<div class="review-date">' . $date . '</div>' : '';

                if ($this->meta['stars']) {
                    $output .= '<div class="star-rating" role="img" aria-label="' . esc_attr( $label ) . '">';
                    $output .= wc_get_star_rating_html($rating) . '</div>'.$review_date.'</div>';
                } else {
                    $output .= $review_date . '</div>';
                }
                if ($this->meta['reviewer']) {
                    $output .= '<div class="byline">Reviewed by: ' . $comment->comment_author . '</div>';
                }
                $output .= '<p class="review"> ' . wp_trim_words($theContent, get_post_meta($this->carousel_id, 'dd_owl_excerpt_length', true), $this->meta['excerpt_more']) .
                    ' <a href="' . esc_url(get_permalink($product['ID'])) . '#comment-' . $comment->comment_ID . '">'.$this->meta['excerpt_more'].'</a></p>';

                if ($this->meta['show_product']) {
                    $output .= '<div class="review-product">' . __('Product Reviewed', 'owl-carousel-2') . ': <a href="' . esc_url(get_permalink($product['ID'])) . '">' . $product['post_title'] . '</a></div>';
                }

                $output .= '</div>';
            }
        } else {
            $output .= __('There are no reviews for this carousel', 'owl-carousel-2');
        }
        $output .= '</div></div>';
        return $output;
    }

    /**
     * Get the carousel post meta into a single array
     */
    private function get_carousel_meta(){

        $this->meta['css_id'] = get_post_meta($this->carousel_id, 'dd_owl_css_id', true);
        $this->meta['post_type'] = get_post_meta($this->carousel_id, 'dd_owl_post_type', true);
        $this->meta['per_page'] = get_post_meta($this->carousel_id, 'dd_owl_number_posts', true);
        $this->meta['thumbs'] = (get_post_meta($this->carousel_id, 'dd_owl_thumbs', true) == 'checked') ? 'true' : 'false';
        $this->meta['image_options'] = get_post_meta($this->carousel_id, 'dd_owl_image_options', true);
        $this->meta['excerpt_length'] = get_post_meta($this->carousel_id, 'dd_owl_excerpt_length', true);
        $this->meta['excerpt_more'] = esc_html(get_post_meta($this->carousel_id, 'dd_owl_excerpt_more', true));
        $this->meta['hide_more'] = (get_post_meta($this->carousel_id, 'dd_owl_hide_excerpt_more', true) === 'checked') ? 'true' : 'false';
        $this->meta['cta_text'] = esc_html(get_post_meta($this->carousel_id, 'dd_owl_cta', true));
        $this->meta['btn_class'] = get_post_meta($this->carousel_id, 'dd_owl_btn_class', true);
        $this->meta['btn_display'] = get_post_meta($this->carousel_id, 'dd_owl_btn_display', true);
        $this->meta['btn_margin'] = (!empty (get_post_meta($this->carousel_id, 'dd_owl_btn_margin', true))) ? 'margin: ' . get_post_meta($this->carousel_id, 'dd_owl_btn_margin', true) . ';' : '';
        $this->meta['show_cta'] = (get_post_meta($this->carousel_id, 'dd_owl_show_cta', true) == 'checked') ? 'true' : 'false';
        $this->meta['tax_options'] = get_post_meta($this->carousel_id, 'dd_owl_tax_options', true);
        $this->meta['postIDs'] = get_post_meta($this->carousel_id, 'dd_owl_post_ids', true);
        $this->meta['orderby'] = get_post_meta($this->carousel_id, 'dd_owl_orderby', true);
        $this->meta['taxonomy'] = get_post_meta($this->carousel_id, 'dd_owl_post_taxonomy_type', true);
        $this->meta['term'] = get_post_meta($this->carousel_id, 'dd_owl_post_taxonomy_term', true);
        $this->meta['centered'] = (get_post_meta($this->carousel_id, 'dd_owl_nav_position', true) == 'centered') ? ' nav-centered' : '';

        // Image Attributes
        $this->meta['img_atts'] = array(
            'size' => get_post_meta($this->carousel_id, 'dd_owl_image_size', true),
            'width' => get_post_meta($this->carousel_id, 'dd_owl_img_width', true),
            'height' => get_post_meta($this->carousel_id, 'dd_owl_img_height', true),
            'crop' => get_post_meta($this->carousel_id, 'dd_owl_img_crop', true),
            'upscale' => get_post_meta($this->carousel_id, 'dd_owl_img_upscale', true),
            'options' => get_post_meta($this->carousel_id, 'dd_owl_image_options', true)
        );

        // Get Owl Meta for Carousel Init
        $this->meta['loop'] = (get_post_meta($this->carousel_id, 'dd_owl_loop', true) === 'checked') ? 'true' : 'false';
        $this->meta['center'] = (get_post_meta($this->carousel_id, 'dd_owl_center', true) === 'checked') ? 'true' : 'false';
        $this->meta['duration'] = get_post_meta($this->carousel_id, 'dd_owl_duration', true);
        $this->meta['transition'] = get_post_meta($this->carousel_id, 'dd_owl_transition', true);
        $this->meta['stop'] = (get_post_meta($this->carousel_id, 'dd_owl_stop', true) === 'checked') ? 'true' : 'false';
        $this->meta['navs'] = (get_post_meta($this->carousel_id, 'dd_owl_navs', true) === 'checked') ? 'true' : 'false';
        $this->meta['dots'] = (get_post_meta($this->carousel_id, 'dd_owl_dots', true) === 'checked') ? 'true' : 'false';
        $this->meta['margin'] = get_post_meta($this->carousel_id, 'dd_owl_margin', true);
        $this->meta['prev'] = (!empty( get_post_meta( $this->carousel_id, 'dd_owl_prev', true ))) ? html_entity_decode(get_post_meta($this->carousel_id, 'dd_owl_prev', true)) : '&lt;';
        $this->meta['next'] = (!empty( get_post_meta( $this->carousel_id, 'dd_owl_next', true ))) ? html_entity_decode(get_post_meta($this->carousel_id, 'dd_owl_next', true)) : '&gt;';
        $this->meta['autoplay'] = (get_post_meta($this->carousel_id, 'dd_owl_autoplay', true) === 'checked') ? 'false' : 'true';
        $this->meta['slideby'] = (!empty(get_post_meta($this->carousel_id, 'dd_owl_slideby', true))) ? intval(get_post_meta($this->carousel_id, 'dd_owl_slideby', true)) : 1;
        $this->meta['mousedrag'] = (get_post_meta($this->carousel_id, 'dd_owl_mousedrag', true) === 'checked') ? 'true' : 'false';
        $this->meta['touchdrag'] = (get_post_meta($this->carousel_id, 'dd_owl_touchdrag', true) === 'checked') ? 'true' : 'false';
        $this->meta['lazy'] = (get_post_meta($this->carousel_id, 'dd_owl_lazy', true) === 'checked') ? 'true' : (metadata_exists('post', $this->carousel_id, 'dd_owl_lazy') ? 'false' : 'true');

        // Set Review Options
        $this->meta['stars'] = (get_post_meta($this->carousel_id, 'dd_owl_show_review_stars', true) === 'checked' ) ? false : true;
        $this->meta['show_product'] = (get_post_meta($this->carousel_id, 'dd_owl_show_review_product', true) === 'checked' ) ? false : true;
        $this->meta['review_date'] = (get_post_meta($this->carousel_id, 'dd_owl_show_review_date', true) === 'checked' ) ? false : true;
        $this->meta['reviewer'] = (get_post_meta($this->carousel_id, 'dd_owl_show_review_reviewer', true) === 'checked' ) ? false : true;

        // Get Responsive Settings
        $this->meta['items_width1'] = intval(get_post_meta($this->carousel_id, 'dd_owl_items_width1', true));
        $this->meta['items_width2'] = intval(get_post_meta($this->carousel_id, 'dd_owl_items_width2', true));
        $this->meta['items_width3'] = intval(get_post_meta($this->carousel_id, 'dd_owl_items_width3', true));
        $this->meta['items_width4'] = intval(get_post_meta($this->carousel_id, 'dd_owl_items_width4', true));
        $this->meta['items_width5'] = intval(get_post_meta($this->carousel_id, 'dd_owl_items_width5', true));
        $this->meta['items_width6'] = intval(get_post_meta($this->carousel_id, 'dd_owl_items_width6', true));

    }
}
