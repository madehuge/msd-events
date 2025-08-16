<?php
/**
 * Handles custom admin columns for MSD Events CPT.
 *
 * @package MSD_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSD_Events_Admin_Columns' ) ) {

    class MSD_Events_Admin_Columns {

        /**
         * Post type slug.
         *
         * @var string
         */
        private string $post_type = 'msd_event';

        /**
         * Constructor.
         */
        public function __construct() {
            add_filter( "manage_{$this->post_type}_posts_columns", [ $this, 'add_columns' ] );
            add_action( "manage_{$this->post_type}_posts_custom_column", [ $this, 'render_columns' ], 10, 2 );
        }

        /**
         * Adds custom columns to the CPT list table.
         *
         * @param array $columns Existing columns.
         * @return array
         */
        public function add_columns( array $columns ): array {
            $new_columns = [];

            foreach ( $columns as $key => $label ) {
                $new_columns[ $key ] = $label;

                if ( 'title' === $key ) {
                    $new_columns['event_date'] = __( 'Event Date & Time', 'msd-events' );
                    $new_columns['location']   = __( 'Location', 'msd-events' );
                }
            }

            return $new_columns;
        }

        /**
         * Renders custom column content.
         *
         * @param string $column  Column key.
         * @param int    $post_id Post ID.
         * @return void
         */
        public function render_columns( string $column, int $post_id ): void {
            // Prevent duplicate rendering in Quick Edit / Inline Edit
            if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'inline-save' ) {
                return;
            }

            switch ( $column ) {
                case 'event_date':
                    $this->render_event_date( $post_id );
                    break;

                case 'location':
                    $this->render_location( $post_id );
                    break;
            }
        }

        /**
         * Render Event Date column.
         *
         * @param int $post_id Post ID.
         * @return void
         */
        private function render_event_date( int $post_id ): void {
            $datetime = get_post_meta( $post_id, '_msd_event_date', true );

            if ( $datetime ) {
                echo esc_html(
                    date_i18n(
                        get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
                        strtotime( $datetime )
                    )
                );
            } else {
                echo '—';
            }
        }

        /**
         * Render Location column.
         *
         * @param int $post_id Post ID.
         * @return void
         */
        private function render_location( int $post_id ): void {
            $location = get_post_meta( $post_id, '_msd_event_location', true );
            echo $location ? esc_html( $location ) : '—';
        }
    }
}
