<?php
/**
 * Plugin Name:       Charitable - Stat Shortcode
 * Plugin URI:
 * Description:
 * Version:           1.0.1
 * Author:            WP Charitable
 * Author URI:        https://www.wpcharitable.com
 * Requires at least: 4.2
 * Tested up to:      4.9
 *
 * Text Domain:       charitable-stat-shortcode
 * Domain Path:       /languages/
 *
 * @package  Charitable Stat_Shortcode
 * @category Core
 * @author   WP Charitable
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Load plugin class, but only if Charitable is found and activated.
 *
 * @return false|Charitable_Stat_Shortcode Whether the class was loaded.
 */
function charitable_stat_shortcode_load() {	
	if ( class_exists( 'Charitable' ) ) {
		require_once( 'class-charitable-stat-query.php' );
		require_once( 'class-charitable-stat-shortcode.php' );

		add_shortcode( 'charitable_stat', array( 'Charitable_Stat_Shortcode', 'display' ) );
	}
}

add_action( 'plugins_loaded', 'charitable_stat_shortcode_load', 1 );
