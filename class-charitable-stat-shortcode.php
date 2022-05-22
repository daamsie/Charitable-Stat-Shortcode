<?php
namespace CharitableStatShortcodePlugin;

use DateTime;
use PHPUnit\Runner\AfterTestHook;

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

					if ( ! empty( $this->args['group_by'] ) ) {
						return $this->table_format( $amount, true, $this->get_column_headers() );
					}

					$amount = charitable_format_money( $amount );

					if ( $this->args['formatted'] ) {
						return $amount;
					}

					return charitable_sanitize_amount( $amount );

				case 'donations':
				case 'donors':
					if ( ! empty( $this->args['group_by'] ) ) {
						return $this->table_format( $this->report->get_report( $this->type ), false, $this->get_column_headers() );
					}
					return (string) $this->report->get_report( $this->type );

				case 'campaigns':
					return (string) $this->report->get_report( $this->type );
			}
		}

		/**
		 * Get the column headers to show.
		 */
		private function get_column_headers() {
			// Default to Campaign and Total. Adjust only if different.
			$th0 = __( 'Campaign', 'charitable' );
			$th1 = __( 'Total', 'charitable' );

			if ( 'donations' === $this->type ) {
				$th1 = __( 'Donations', 'charitable' );
			} elseif ( 'donors' === $this->type ) {
				$th1 = __( 'Donors', 'charitable' );
			}

			if ( 'category' === $this->args['group_by'] ) {
				$th0 = __( 'Category', 'charitable' );
			}

			return array( $th0, $th1 );
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
				'date_before'      => '',
				'date_after'       => '',
				'category'         => '',
				'tag'              => '',
				'type'             => '',
				'include_children' => true,
				'group_by'         => '',
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
			$args['end_date']         = $this->parse_possible_relative_date( $args['date_before'] );
			$args['start_date']       = $this->parse_possible_relative_date( $args['date_after'] );

			return $args;
		}

		/**
		 * Handle relative dates, eg -7 would become exactly one week in the past.
		 *
		 * @since  1.6.0
		 *
		 * @param  string $input_string The date provided as a shortcode attribute, could be a date or it could be a number
		 * @return int|'' The resulting time.
		 */
		private function parse_possible_relative_date( $date_string ) {
			if ( ! strtotime( $date_string ) ) {
				return '';
			}

			if ( is_numeric( $date_string ) ) {
				$st = strtotime( $date_string . ' day' );
			} else {
				$st = strtotime( $date_string );
			}

			$date = new DateTime( "@$st" );

			return $date->format( 'Y-m-d' );
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

		/**
		 * Format the results as a table.
		 *
		 * @since 1.6.57
		 *
		 * @param array $results The results. These are expected to be in an array with the format array[0] = label and array[1] = amount.
		 * @param bool  $format_money Whether to format the amount as money.
		 * @param array $headers An array with two items. One the label for the first column, the second for the second column.
		 *
		 * @return string A HTML table with the results.
		 */

		private function table_format( $results, $format_money = false, $headers = array( '', '' ) ) {
			if ( ! is_array( $results ) || ! count( $results ) ) {
				return '';
			}
			if ( 2 !== count( $results[0] ) ) {
				return '';
			}

			// Filtering out any 0 values.
			$results = array_filter(
				$results,
				function ( $result ) {
					return 0 != $result[1];
				}
			);

			// Sorting in descending value.
			usort(
				$results,
				function ( $item1, $item2 ) {
					return charitable_sanitize_amount( $item2[1] ) <=> charitable_sanitize_amount( $item1[1] );
				}
			);

			$html  = '<table class="charitable-stat-shortcode-table">';
			$html .= '<tr><th>' . $headers[0] . '</th><th>' . $headers[1] . '</th></tr>';
			foreach ( $results as $result ) {
				$html .= '<tr><td>' . $result[0] . '</td>';
				if ( $format_money && is_numeric( $result[1] ) ) {
					$formatted_amount = charitable_format_money( $result[1] );
					$html            .= '<td data-amount="' . $result[1] . '">' . $formatted_amount . '</td>';
				} else {
					$html .= '<td data-amount="' . $result[1] . '">' . $result[1] . '</td>';
				}
				$html .= '</tr>';
			}
			$html .= '</table>';
			return $html;
		}
	}

endif;
