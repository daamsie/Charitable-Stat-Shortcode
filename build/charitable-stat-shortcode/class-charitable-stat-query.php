<?php
/**
 * Responsible for executing a stat request and returning the correct value.
 *
 * @package   Charitable Stat/Classes/Charitable_Stat_Query
 * @version   1.0.0
 * @author    Eric Daams
 * @copyright Copyright (c) 2017, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Stat_Query' ) ) :

    /**
     * Charitable_Stat_Query
     *
     * @since 1.0.0
     */
    class Charitable_Stat_Query {

        /**
         * The type of query.
         *
         * @since 1.0.0
         *
         * @var   string
         */
        private $type;

        /**
         * Mixed set of arguments for the query.
         *
         * @since 1.0.0
         *
         * @var   array
         */
        private $args;

        /**
         * Create class object.
         *
         * @since 1.0.0
         *
         * @param string $type The type of query we are executing.
         * @param array  $args Mixed set of arguments for the query.
         */
        public function __construct( $type, $args ) {
            $this->type  = $type;
            $this->args  = $this->parse_args( $args );
            $this->query = $this->generate_query();

            echo '<pre>';
            var_dump( $this->query );
            echo '</pre>';
        }

        /**
         * Return the result of the query.
         *
         * @since  1.0.0
         *
         * @return string
         */
        public function __toString() {
            return $this->get_query_result();
        }

        /**
         * Return the query result.
         *
         * @since  1.0.0
         *
         * @return string
         */
        public function get_query_result() {
            switch ( $this->type ) {
                case 'total' : 
                    return charitable_format_money( $this->query );

                case 'donors' :
                case 'donations' : 
                    return (string) $this->query->count();
            }
        }

        /**
         * Parse arguments.
         *
         * @since  1.0.0
         *
         * @param  array $args User defined arguments.
         * @return array
         */
        private function parse_args( $args ) {
            $defaults = array(
                'display'   => 'total',
                'campaigns' => array(),
                'status'    => array( 'charitable-completed', 'charitable-preapproved' ),
            );

            $args = array_merge( $defaults, $args );

            if ( ! is_array( $args['campaigns'] ) ) {
                $args['campaigns'] = array();
            } else {
                $args['campaigns'] = array_filter( $args['campaigns'], 'intval' );
            }

            return $args;
        }

        /**
         * Generates the appropriate query.
         *
         * @since  1.0.0
         *
         * @return mixed
         */
        private function generate_query() {
            switch ( $this->type ) {
                case 'total' : 
                    return $this->generate_total_query();

                case 'donors' :
                    return $this->generate_donor_query();

                case 'donations' : 
                    return $this->generate_donation_query();
            }
        }

        /**
         * Generate a total query type.
         *
         * @since  1.0.0
         *
         * @return ?
         */
        private function generate_total_query() {
            if ( empty( $this->args['campaigns'] ) ) {
                return charitable_get_table( 'campaign_donations' )->get_total();
            }

            return charitable_get_table( 'campaign_donations' )->get_campaign_donated_amount( $this->args['campaigns'] );
        }

        /**
         * Generate a donations query type.
         *
         * @since  1.0.0
         *
         * @return ?
         */
        private function generate_donation_query() {
            $query_args = array(
                'output'   => 'count',
                'campaign' => $this->args['campaigns'],
                'status'   => $this->args['status'],
            );

            return new Charitable_Donations_Query( $query_args );        
        }

        /**
         * Generate a donors query type.
         *
         * @since  1.0.0
         *
         * @return Charitable_Donor_Query
         */
        private function generate_donor_query() {
            $query_args = array(
                'output'   => 'count',
                'campaign' => $this->args['campaigns'],
                'status'   => $this->args['status'],
            );

            return new Charitable_Donor_Query( $query_args );
        }
    }

endif;
