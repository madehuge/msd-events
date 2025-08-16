<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MSD_EVENTS_DIR . 'includes/cpt/interfaces/interface-taxonomy-registrable.php';

class MSD_Events_Taxonomy implements MSD_Taxonomy_Registrable {

    public function __construct() {
        add_action( 'init', [ $this, 'register_taxonomy' ] );
    }

    public function register_taxonomy() {
        $labels = [
            'name'          => __( 'Event Type', 'msd-events' ),
            'singular_name' => __( 'Event Category', 'msd-events' ),
            'search_items'  => __( 'Search Event Types', 'msd-events' ),
            'all_items'     => __( 'All Event Types', 'msd-events' ),
            'edit_item'     => __( 'Edit Event Category', 'msd-events' ),
            'update_item'   => __( 'Update Event Category', 'msd-events' ),
            'add_new_item'  => __( 'Add New Event Category', 'msd-events' ),
        ];

        $args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'rewrite'           => [ 'slug' => 'event-category' ],
            'show_in_rest'      => true,
        ];

        register_taxonomy( 'msd_event_category', [ 'msd_event' ], $args );
    }
}
