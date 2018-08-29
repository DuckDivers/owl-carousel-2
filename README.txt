=== Custom Post Carousels with Owl ===
Contributors: thehowarde
Donate link: https://www.duckdiverllc.com
Tags: Carousel,Slider,Owl Carousel,Post Carousel,Rotator,Product Carousel,CPT Carousel,CPT Slider,Testimonial Slider,FAQ Slider
Requires at least: 4.5
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: 1.0.3
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Easily add post carousels to your website. Works with any custom post type or regular posts. Controls allow for insertion of multiple carousels on a single page.  You can specify margins, number of items per page at multiple breakpoints. Choose options by category, tag, other custom taxonomy or by post ID. 

== Description ==

This plugin uses the Owl Carousel 2 jQuery plugin to create carousels (sliders) from any built in or (public) custom post type in WordPress. You can use this plugin to make:

* Product Carousels
* Featured Product Carousels
* Carousel by post (product) ID
* Testimonial Sliders (Carousels)
* Event Sliders
* Latest Posts
* More

Easy to use controls allow for customization of each carousel with options to show or hide Titles, Featured Image, Call to Action buttons (links) and more.

This plugin is simple and without on screen nags, promotions or upsells.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `owl-carousel-2.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Click on the menu item called `Carousels`
1. Create your carousel.
1. Copy the Shortcode and place in your page or post, or place `<?php echo do_shortcode('[dd-owl-carousel id="1" title="Carousel Title"]'); ?>` in your templates 

== Frequently Asked Questions ==

= I made the carousel, but now I can't see it. =

Make sure you insert the shortcode created by the plugin.

= Can I use multiple carousels on a single page? =

Yes, you can use as many as you want. Each one will have it's own CSS ID so you can target them in your custom CSS files if you need to.

= Are there programming Hooks? =

Yes, there are 2 hooks right now. One is before the carousel contents, and the other is after the contents.  There will be more enhancement to this at a later date.

1. dd-carousel-before-content
1. dd-carousel-after-content

Example to add pricing for WooCommerce Carousels - Add to your theme functions.php :

`<?php 
	function add_wc_price_to_carousel(){
    global $post, $woocommerce;
    $product = wc_get_product( $post->ID );
    if ($product) {
        echo '<p class="price">$' . $product->get_price() . '</p>' ;
        echo '<a href="'.get_permalink( $post->ID ).'" class="btn btn-primary ">Shop Now</a>';
		}
	}
	add_action('dd-carousel-after-content', 'add_wc_price_to_carousel', 10);
`

== Screenshots ==

1. Admin View of a Featured Product Carousel
2. Admin View of choosing by post ID.
3. Admin View of Chosen Category
4. Public Large Desktop View. With Featured Image and CTA Link to item.

== Changelog ==

= 1.0.3 =
Add Thumbnail Image Sizes

= 1.0.2 =
Allow for empty excerpt under title.

= 1.0.1 =
Change admin script to only enqueu on carousel custom post type.

= 1.0 =
Initial Release