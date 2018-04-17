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
        $this->register_shortcode();
        $this->include_shortcode();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_styles() {

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/owl-carousel-2-public.css', array(), $this->version, 'all' );
        wp_register_style( 'owl-carousel-css', plugin_dir_url(__FILE__) . 'css/owl.carousel.css');
        wp_register_style( 'owl-theme-css', plugin_dir_url(__FILE__) . 'css/owl.theme.default.min.css');

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
    
    public function include_shortcode(){
        require ('/partials/owl-carousel-2-public-display.php');
    }
    
    public function register_shortcode(){
        add_shortcode('dd-owl-carousel', 'dd_owl_carousel_two');
    }
}
