<?php

/**
 * @link              https://www.howardehrenberg.com
 * @since             1.0.0
 * @package           Owl_Carousel_2
 *
 * @wordpress-plugin
 * Plugin Name:       WP Owl Carousel 2
 * Plugin URI:        https://www.duckdiverllc.com
 * Description:       A Simple Implementation of Owl Carousel 2
 * Version:           1.0.0
 * Author:            Howard Ehrenberg
 * Author URI:        https://www.howardehrenberg.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       owl-carousel-2
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'DD_Owl_Carousel_2', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-owl-carousel-2-activator.php
 */
function activate_owl_carousel_2() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-owl-carousel-2-activator.php';
	Owl_Carousel_2_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-owl-carousel-2-deactivator.php
 */
function deactivate_owl_carousel_2() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-owl-carousel-2-deactivator.php';
	Owl_Carousel_2_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_owl_carousel_2' );
register_deactivation_hook( __FILE__, 'deactivate_owl_carousel_2' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-owl-carousel-2.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_owl_carousel_2() {

	$plugin = new Owl_Carousel_2();
	$plugin->run();

}
run_owl_carousel_2();
