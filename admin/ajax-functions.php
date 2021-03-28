<?php

/**
 * Class for Admin Ajax Functions
 */
class Owl_Carousel_2_Admin_Ajax {

    public function __construct() {

        add_action('wp_ajax_owl_carousel_tax', array($this, 'owl_carousel_tax'));
        add_action('wp_ajax_owl_carousel_terms', array($this, 'owl_carousel_terms'));
        add_action('wp_ajax_owl_carousel_posts', array($this, 'owl_carousel_posts'));
        add_action('wp_ajax_dd_owl_get_image', array($this, 'dd_owl_get_image'));

    }

    public function owl_carousel_tax() {
        $post_type = ($_POST['posttype'] == 'reviews') ? 'product' : $_POST['posttype'];

        //Terms to exclude from WooCommerce
        $wc_not = array('product_type', 'product_visibility', 'product_shipping_class');

        $meta = '';

        if (metadata_exists('post', $_POST['postid'], 'dd_owl_post_taxonomy_type')) $meta = get_post_meta($_POST['postid'], 'dd_owl_post_taxonomy_type', true);

        $html = '';

        $tax_objects = get_object_taxonomies($post_type, 'objects');

        if (empty($tax_objects)) {
            $html .= sprintf('<span class="no-cats">' . __('There are no matching Taxonomies', 'owl-carousel-2') . '</span>');
        } else {
            $html .= '<select id="dd_owl_post_taxonomy_type" name="dd_owl_post_taxonomy_type" class="dd_owl_post_taxonomy_type_field">';

            if (!in_array($meta, $tax_objects)) $html .= '<option value="" selected> - - Choose A Tax Type - -</option>';

            foreach ($tax_objects as $tax) {
                if ($post_type == 'product' && in_array($tax->name, $wc_not)) {
                    continue;
                } else {
                    $label = $tax->labels->name;
                    $value = $tax->name;
                    $html .= '<option value="' . $value . '" ';
                    $html .= ($value == $meta) ? "selected" : null;
                    $html .= '> ' . $label . '</option>';
                }
            }

            $html .= '</select>';
        }

        echo $html;

        die();
    }

    public function owl_carousel_terms() {

        $post_type = ($_POST['posttype'] == 'reviews') ? 'product' : $_POST['posttype'];

        $html = '';

        $tax_objects = get_object_taxonomies($post_type, 'objects');

        $term_objects = (isset($_POST['taxtype'])) ? get_terms($_POST['taxtype'], 'objects') : null;

        $theterm = get_post_meta($_POST['postid'], 'dd_owl_post_taxonomy_term', true);

        if (null == $tax_objects || is_wp_error($term_objects)) {
            $html .= sprintf('<span class="no-cats">' . __('There are no matching terms', 'owl-carousel-2') . '</span>');
        } else {
            if (null == $term_objects) {
                $html .= sprintf('<span class="no-cats">' . __('There are no matching terms', 'owl-carousel-2') . '</span>');
            } else {
                $html .= '<select id="dd_owl_post_taxonomy_term" name="dd_owl_post_taxonomy_term[]" multiple="multiple" class="dd-owl-multi-select">';
                if (!in_array($theterm, $term_objects) || $term_objects->errors) $html .= '<option value=""> - - Choose A Term - -</option>';
                foreach ($term_objects as $term) {
                    $label = $term->name;
                    $value = $term->slug;
                    $html .= '<option value="' . $value . '" ';
                    if (is_array($theterm)) $html .= ((in_array($value, $theterm)) && (!isset($term_objects->errors))) ? "selected data-selected='true'" : null;
                    $html .= '> ' . $label . '</option>';
                }

                $html .= '</select>';
            }
        }

        echo json_encode($html);

        die();
    }

    public function owl_carousel_posts() {

        $post_type = ($_POST['posttype'] == 'reviews') ? 'product' : $_POST['posttype'];

        global $post;
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => '-1',
        );

        $query = new WP_Query($args);
        $html = '';
        // The Loop
        $selectedArray = maybe_unserialize(get_post_meta($_POST['carousel_id'], 'dd_owl_post_ids', true));

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $html .= "<option value='{$post->ID}'";
                if (is_array($selectedArray)) {
                    if (in_array($post->ID, $selectedArray)) {
                        $html .= 'selected="selected"';
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

    public function dd_owl_get_image() {
        if (isset($_GET['id'])) {
            $image = wp_get_attachment_image(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT), 'medium', false, array('id' => 'dd-preview-image'));
            $data = array(
                'image' => $image,
            );
            wp_send_json_success($data);
        } else {
            wp_send_json_error();
        }
    }
}