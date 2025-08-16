<?php
/**
 * Class MSD_Events_CPT
 *
 * Handles registration of the Events custom post type and taxonomy.
 *
 * @package MSD_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'MSD_Events_CPT' ) ) {

    class MSD_Events_CPT {

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'init', [ $this, 'register_cpt' ] );
            add_action( 'init', [ $this, 'register_taxonomy' ] );

            // Admin columns.
            add_filter( 'manage_msd_event_posts_columns', [ $this, 'add_admin_columns' ] );
            add_action( 'manage_msd_event_posts_custom_column', [ $this, 'render_admin_columns' ], 10, 2 );
            add_filter( 'manage_edit-msd_event_sortable_columns', [ $this, 'make_columns_sortable' ] );
        }

        /**
         * Registers the Events CPT.
         */
        public function register_cpt() {
            $labels = [
                'name'               => __( 'Events', 'msd-events' ),
                'singular_name'      => __( 'Event', 'msd-events' ),
                'menu_name'          => __( 'Events', 'msd-events' ),
                'name_admin_bar'     => __( 'Event', 'msd-events' ),
                'add_new'            => __( 'Add New', 'msd-events' ),
                'add_new_item'       => __( 'Add New Event', 'msd-events' ),
                'edit_item'          => __( 'Edit Event', 'msd-events' ),
                'new_item'           => __( 'New Event', 'msd-events' ),
                'view_item'          => __( 'View Event', 'msd-events' ),
                'search_items'       => __( 'Search Events', 'msd-events' ),
                'not_found'          => __( 'No events found', 'msd-events' ),
                'not_found_in_trash' => __( 'No events found in Trash', 'msd-events' ),
            ];

            $args = [
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => [ 'slug' => 'events' ],
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => 5,
                'menu_icon'          => 'dashicons-calendar-alt',
                'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
                'show_in_rest'       => true,
            ];

            register_post_type( 'msd_event', $args );
        }

        /**
         * Registers the Event Category taxonomy.
         */
        public function register_taxonomy() {
            $labels = [
                'name'              => __( 'Event Categories', 'msd-events' ),
                'singular_name'     => __( 'Event Category', 'msd-events' ),
                'search_items'      => __( 'Search Event Categories', 'msd-events' ),
                'all_items'         => __( 'All Event Categories', 'msd-events' ),
                'parent_item'       => __( 'Parent Event Category', 'msd-events' ),
                'parent_item_colon' => __( 'Parent Event Category:', 'msd-events' ),
                'edit_item'         => __( 'Edit Event Category', 'msd-events' ),
                'update_item'       => __( 'Update Event Category', 'msd-events' ),
                'add_new_item'      => __( 'Add New Event Category', 'msd-events' ),
                'new_item_name'     => __( 'New Event Category Name', 'msd-events' ),
                'menu_name'         => __( 'Event Categories', 'msd-events' ),
            ];

            $args = [
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => [ 'slug' => 'event-category' ],
                'show_in_rest'      => true,
            ];

            register_taxonomy( 'msd_event_category', [ 'msd_event' ], $args );
        }

        /**
         * Adds custom admin columns.
         */
        public function add_admin_columns( $columns ) {
            $new_columns = [];
            foreach ( $columns as $key => $label ) {
                $new_columns[ $key ] = $label;
                if ( 'title' === $key ) {
                    $new_columns['event_date'] = __( 'Event Date', 'msd-events' );
                    $new_columns['location']   = __( 'Location', 'msd-events' );
                }
            }
            return $new_columns;
        }

        /**
         * Renders custom admin column content.
         */
        public function render_admin_columns( $column, $post_id ) {
            if ( 'event_date' === $column ) {
                $date = get_post_meta( $post_id, '_msd_event_date', true );
                echo $date ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '—';
            }

            if ( 'location' === $column ) {
                $location = get_post_meta( $post_id, '_msd_event_location', true );
                echo $location ? esc_html( $location ) : '—';
            }
        }

        /**
         * Makes custom columns sortable.
         */
        public function make_columns_sortable( $columns ) {
            $columns['event_date'] = 'event_date';
            return $columns;
        }
    }
}

// Instantiate only once
if ( class_exists( 'MSD_Events_CPT' ) && ! isset( $GLOBALS['msd_events_cpt'] ) ) {
    $GLOBALS['msd_events_cpt'] = new MSD_Events_CPT();
}
