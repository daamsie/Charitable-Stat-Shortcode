<?php
namespace CharitableStatShortcodePlugin;

/**
 * Plugin Name:       Charitable - Stat Shortcode
 * Plugin URI:        https://github.com/Charitable/Charitable-Stat-Shortcode
 * Description:       The [charitable_stat] shortcode, with extra features.
 * Version:           1.2.0
 * Author:            WP Charitable
 * Author URI:        https://www.wpcharitable.com
 * Requires at least: 4.2
 * Tested up to:      5.9.1
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
add_action( 'plugins_loaded', function() {
	if ( class_exists( 'Charitable' ) ) {
		require_once( 'class-charitable-donation-report.php' );
		require_once( 'class-charitable-stat-shortcode.php' );

		remove_shortcode( 'charitable_stat', array( 'Charitable_Stat_Shortcode', 'display' ) );
		add_shortcode( 'charitable_stat', array( __NAMESPACE__ . '\Charitable_Stat_Shortcode', 'display' ) );
	}
}, 1 );
