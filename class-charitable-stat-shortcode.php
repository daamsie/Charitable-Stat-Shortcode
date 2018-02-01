<?php
/**
 * Responsible for parsing and displaying the output of the [charitable_stat] shortcode.
 *
 * @package   Charitable_Stat/Classes/Charitable_Stat_Shortcode
 * @version   1.0.0
 * @author    Eric Daams
 * @copyright Copyright (c) 2017, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Stat_Shortcode' ) ) :

	/**
	 * Charitable_Stat_Shortcode
	 *
	 * @since 1.0.0
	 */
	class Charitable_Stat_Shortcode {

		/**
		 * Create class object.
		 *
		 * @since 1.0.0
		 */
		public static function display( $atts ) {
			$defaults = array(
				'display'   => 'total',
				'campaigns' => '',
				'goal'      => false,
			);

			$args              = shortcode_atts( $defaults, $atts, 'charitable_stat' );
			$args['campaigns'] = strlen( $args['campaigns'] ) ? explode( ',', $args['campaigns'] ) : array();

			return new Charitable_Stat_Query( $args['display'], $args );
		}
	}

endif;
