<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MSD_EVENTS_DIR . 'includes/cpt/interfaces/interface-cpt-registrable.php';

class MSD_Events_CPT implements MSD_CPT_Registrable {

    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    public function register_cpt() {
        $labels = [
            'name'               => __( 'MSD Events', 'msd-events' ),
            'singular_name'      => __( 'MSD Event', 'msd-events' ),
            'menu_name'          => __( 'MSD Events', 'msd-events' ),
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
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'MSD Events' ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail' ],
            'show_in_rest'       => true,
        ];

        register_post_type( 'msd_event', $args );
    }
}
