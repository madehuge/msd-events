<?php
/**
 * MSD Events Uninstall
 *
 * @package MSD_Events
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Optional: 
//error_log( 'MSD Events plugin is being uninstalled. All data will be removed.' );

// 1. Delete plugin options
$option_keys = [
    'msd_events_api_key',
    'msd_events_api_url',
    'msd_events_per_page',
];
foreach ( $option_keys as $key ) {
    delete_option( $key );
    //delete_site_option( $key ); // In case of multisite
}

// 2. Delete all MSD Events posts
$event_posts = get_posts( [
    'post_type'      => 'msd_event',
    'post_status'    => 'any',
    'numberposts'    => -1,
    'fields'         => 'ids',
] );

if ( ! empty( $event_posts ) ) {
    foreach ( $event_posts as $post_id ) {
        wp_delete_post( $post_id, true ); // true = force delete
    }
}

// 3. Delete associated taxonomies terms
$taxonomies = [ 'msd_event_category', 'msd_event_tag' ]; // Replace with your taxonomies
foreach ( $taxonomies as $taxonomy ) {
    $terms = get_terms( [
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ] );
    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        foreach ( $terms as $term ) {
            wp_delete_term( $term->term_id, $taxonomy );
        }
    }
}

// 4. Delete transients (cached geocodes)
global $wpdb;
$transient_like = '_transient_msd_events_geo_%';
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM $wpdb->options WHERE option_name LIKE %s",
        $transient_like
    )
);
