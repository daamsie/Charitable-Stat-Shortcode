<?php
namespace CharitableStatShortcodePlugin;

/**
 * Model for generating a donation report.
 *
 * @package   Charitable/Classes/Charitable_Donation_Report
 * @author    Eric Daams
 * @copyright Copyright (c) 2020, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.6.0
 * @version   1.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( __NAMESPACE__ . '\Charitable_Donation_Report' ) ) :

	/**
	 * Charitable_Donation_Report
	 *
	 * @since 1.6.0
	 */
	class Charitable_Donation_Report {

		/**
		 * Mixed set of arguments for the query.
		 *
		 * @since 1.6.0
		 *
		 * @var   array
		 */
		private $args;

		/**
		 * Types of reports.
		 *
		 * @since 1.6.0
		 *
		 * @var   array
		 */
		private $types = array(
			'amount',
			'donations',
			'donors',
		);

		/**
		 * Reports.
		 *
		 * @since 1.6.0
		 *
		 * @var   array|false
		 */
		private $reports = false;

		/**
		 * Create class object.
		 *
		 * @since 1.6.0
		 *
		 * @param array $args Mixed set of arguments for the query.
		 */
		public function __construct( $args = array() ) {
			$this->args = $this->parse_args( $args );
		}

		/**
		 * Get the reports for each report type.
		 *
		 * @since  1.6.0
		 *
		 * @return array
		 */
		public function get_reports() {
			if ( false === $this->reports ) {
				$this->reports = array();

				if ( false === $this->args['report_type'] ) {
					return $this->reports;
				}

				foreach ( $this->types as $type ) {
					if ( in_array( $type, $this->args['report_type'] ) ) {
						$this->reports[ $type ] = $this->run_report_query( $type );
					}
				}
			}

			return $this->reports;
		}

		/**
		 * Get a single report result.
		 *
		 * @since  1.6.0
		 *
		 * @param  string $report_type The report type to get.
		 * @return mixed
		 */
		public function get_report( $report_type ) {
			if ( is_array( $this->reports ) ) {
				return $this->reports[ $report_type ];
			}

			return $this->run_report_query( $report_type );
		}

		/**
		 * Run a particular report query.
		 *
		 * @since  1.6.0
		 *
		 * @param  string $type The type of report.
		 * @return mixed
		 */
		private function run_report_query( $type ) {
			if ( false === $this->args['campaigns'] ) {
				return 0;
			}

			if ( ! empty( $this->args['date_range'] ) ) {
				$dates = explode( ',', $this->args['date_range'] );
				if ( 2 === count( $dates ) ) {
					$this->args['date_query'] = array(
						'after'  => $dates[0],
						'before' => $dates[1],
					);
				}
			}

			switch ( $type ) {
				case 'amount':
					return $this->run_amount_query();

				case 'donors':
					if ( 'campaign' === $this->args['group_by'] ) {
						return $this->get_donors_grouped_by_campaign();
					}
					return $this->run_donor_query();
				case 'donations':
					return $this->run_donation_query();
			}
		}

		/**
		 * Generate a total query type.
		 *
		 * @since  1.6.0
		 *
		 * @return string
		 */
		private function run_amount_query() {
			if ( empty( $this->args['campaigns'] ) ) {
				return charitable_get_table( 'campaign_donations' )->get_total();
			}

			if ( 'campaign' === $this->args['group_by'] ) {
				return charitable_get_table( 'campaign_donations' )->get_amount_by_campaign_report( $this->args );
			}

			return charitable_get_table( 'campaign_donations' )->get_amount_report( $this->args );
		}

		/**
		 * Generate a donations query type.
		 *
		 * @since  1.6.0
		 *
		 * @return int
		 */
		private function run_donation_query() {

			$query = new \Charitable_Donations_Query(
				array(
					'output'     => 'count',
					'campaign'   => $this->args['campaigns'],
					'status'     => $this->args['status'],
					'date_query' => $this->args['date_query'],
				)
			);

			return $query->count();
		}

		/**
		 * Generate a donors query type.
		 *
		 * @since  1.6.0
		 *
		 * @return mixed
		 */
		private function run_donor_query() {

			$args = array(
				'output'     => 'count',
				'campaign'   => $this->args['campaigns'],
				'status'     => $this->args['status'],
				'date_query' => $this->args['date_query'],
			);

			$query = new \Charitable_Donor_Query( $args );

			return $query->count();
		}

		/**
		 * Get donor counts grouped by campaign
		 *
		 * @since  1.6.57
		 *
		 * @return mixed
		 */
		private function get_donors_grouped_by_campaign() {

			// Get the campaigns with donors first.
			$results = array();

			foreach ( $this->args['campaigns'] as $campaign_id ) {
				$args = array(
					'output'     => 'count',
					'campaign'   => $campaign_id,
					'status'     => $this->args['status'],
					'date_query' => $this->args['date_query'],
				);

				$query     = new \Charitable_Donor_Query( $args );
				$campaign  = charitable_get_campaign( $campaign_id );
				$results[] = array( $campaign->post_title, $query->count() );
			}
			return $results;
		}

		/**
		 * Parse arguments.
		 *
		 * @since  1.6.0
		 *
		 * @param  array $args User defined arguments.
		 * @return array
		 */
		private function parse_args( $args ) {
			$defaults = array(
				'report_type'      => 'all',
				'campaigns'        => array(),
				'status'           => array( 'charitable-completed', 'charitable-preapproved' ),
				'category'         => null,
				'tag'              => null,
				'include_children' => true,
			);

			$args                = array_merge( $defaults, $args );
			$args['campaigns']   = $this->parse_campaigns( $args );
			$args['report_type'] = $this->parse_report_type( $args );

			return $args;
		}

		/**
		 * Parse campaigns argument.
		 *
		 * @since  1.6.0
		 *
		 * @param  array $args The passed report arguments.
		 * @return array|false
		 */
		private function parse_campaigns( $args ) {
			if ( ! is_array( $args['campaigns'] ) ) {
				$args['campaigns'] = array();
			}

			$campaigns = array_map( 'intval', $args['campaigns'] );

			$query_args = array(
				'post_type'      => \Charitable::CAMPAIGN_POST_TYPE,
				'posts_per_page' => -1,
				'post__in'       => $campaigns,
				'tax_query'      => array(),
				'fields'         => 'ids',
			);

			if ( ! is_null( $args['category'] ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'campaign_category',
					'field'    => 'slug',
					'terms'    => $args['category'],
				);
			}

			if ( ! is_null( $args['tag'] ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'campaign_tag',
					'field'    => 'slug',
					'terms'    => $args['tag'],
				);
			}

			if ( $args['include_children'] ) {
				$query_args['post_parent__in'] = $campaigns;

				unset( $query_args['post__in'] );
			}

			if ( empty( $campaigns ) && empty( $query_args['tax_query'] ) && ! $args['include_children'] ) {
				return array();
			}

			$campaigns = array_merge(
				$campaigns,
				get_posts( $query_args )
			);

			/* Return false if there are no campaigns matching the query. */
			if ( empty( $campaigns ) ) {
				return false;
			}

			return $campaigns;
		}

		/**
		 * Parse the passed report type.
		 *
		 * @since  1.6.0
		 *
		 * @param  array $args The passed report arguments.
		 * @return array|false
		 */
		private function parse_report_type( $args ) {
			$report_type = $args['report_type'];

			if ( 'all' == $report_type ) {
				return $this->types;
			}

			if ( is_array( $report_type ) ) {
				return $report_type;
			}

			if ( ! in_array( $report_type, $this->types ) ) {
				charitable_get_deprecated()->doing_it_wrong(
					__METHOD__,
					/* translators: %s: report type */
					sprintf( __( '%s is not a valid donation report type.', 'charitable' ), $report_type ),
					'1.6.0'
				);

				return false;
			}

			return array( $report_type );
		}
	}

endif;
