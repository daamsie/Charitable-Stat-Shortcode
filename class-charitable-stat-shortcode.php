<?php
namespace CharitableStatShortcodePlugin;

/**
 * Responsible for parsing and displaying the output of the [charitable_stat] shortcode.
 *
 * @package   Charitable_Stat/Classes/Charitable_Stat_Shortcode
 * @author    Eric Daams
 * @copyright Copyright (c) 2020, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.6.0
 * @version   1.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( __NAMESPACE__ . '\Charitable_Stat_Shortcode' ) ) :

	/**
	 * Charitable_Stat_Shortcode
	 *
	 * @since 1.6.0
	 */
	class Charitable_Stat_Shortcode {

		/**
		 * The type of query.
		 *
		 * @since 1.6.0
		 *
		 * @var   string
		 */
		private $type;

		/**
		 * Mixed set of arguments for the query.
		 *
		 * @since 1.6.0
		 *
		 * @var   array
		 */
		private $args;

		/**
		 * Create class object.
		 *
		 * @since 1.6.0
		 *
		 * @param array $atts User-defined attributes.
		 */
		private function __construct( $atts ) {
			$this->args   = $this->parse_args( $atts );
			$this->type   = $this->args['display'];
			$this->report = $this->get_report();
		}

		/**
		 * Create class object.
		 *
		 * @since 1.6.0
		 *
		 * @param  array $atts User-defined attributes.
		 * @return string
		 */
		public static function display( $atts ) {
			$object = new Charitable_Stat_Shortcode( $atts );
			return $object->get_query_result();
		}

		/**
		 * Return the query result.
		 *
		 * @since  1.6.0
		 *
		 * @return string
		 */
		public function get_query_result() {
			/* A goal is necessary for the progress report. */
			if ( 'progress' === $this->type && ! $this->args['goal'] ) {
				$this->type = 'total';
			}

			switch ( $this->type ) {
				case 'progress':
					$total   = $this->report->get_report( 'amount' );
					$goal    = charitable_sanitize_amount( $this->args['goal'], false );
					$total   = charitable_sanitize_amount( $total, true );
					$percent = ( $total / $goal ) * 100;

					return '<div class="campaign-progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . $percent . '"><span class="bar" style="width:' . $percent . '%;"></span></div>';

				case 'total':
					$amount = $this->report->get_report( 'amount' );

					if ( $this->args['formatted'] ) {
						return charitable_format_money( $amount );
					}

					return charitable_get_currency_helper()->cast_to_decimal_format( $amount );

				case 'donors':
				case 'donations':
				case 'campaigns':
					return (string) $this->report->get_report( $this->type );
			}
		}

		/**
		 * Parse shortcode attributes.
		 *
		 * @since  1.6.0
		 *
		 * @param  array $atts User-defined attributes.
		 * @return array
		 */
		private function parse_args( $atts ) {
			$defaults = array(
				'display'          => 'total',
				'campaigns'        => '',
				'goal'             => false,
				'category'         => '',
				'tag'              => '',
				'type'             => '',
				'include_children' => true,
				'parent_id'        => '',
				'formatted'        => true,
			);

			$args                     = shortcode_atts( $defaults, $atts, 'charitable_stat' );
			$args['campaigns']        = strlen( $args['campaigns'] ) ? explode( ',', $args['campaigns'] ) : array();
			$args['category']         = strlen( $args['category'] ) ? explode( ',', $args['category'] ) : null;
			$args['tag']              = strlen( $args['tag'] ) ? explode( ',', $args['tag'] ) : null;
			$args['type']             = strlen( $args['type'] ) ? explode( ',', $args['type'] ) : null;
			$args['include_children'] = (bool) $args['include_children'];
			$args['parent_id']        = strlen( $args['parent_id'] ) ? explode( ',', $args['parent_id'] ) : array();
			$args['formatted']        = (bool) $args['formatted'];

			return $args;
		}

		/**
		 * Run the report for the shortcode.
		 *
		 * @since  1.6.0
		 *
		 * @return Charitable_Donation_Report
		 */
		private function get_report() {
			return new Charitable_Donation_Report( $this->get_report_args() );
		}

		/**
		 * Return the arguments used for generating the report.
		 *
		 * @since  1.6.0
		 *
		 * @return array
		 */
		private function get_report_args() {
			$args                = $this->args;
			$args['report_type'] = in_array( $this->type, array( 'progress', 'total' ), true ) ? 'amount' : $this->type;

			return $args;
		}
	}

endif;
