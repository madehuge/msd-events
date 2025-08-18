<?php
/**
 * Uninstall script for MSD Events
 *
 * Fired when the plugin is deleted from WordPress.
 * Cleans up CPT posts, options, and transients.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// 1. Delete all Events CPT posts
$events = get_posts( [
    'post_type'      => 'msd_event',
    'posts_per_page' => -1,
    'fields'         => 'ids',
] );

if ( $events ) {
    foreach ( $events as $event_id ) {
        wp_delete_post( $event_id, true ); // force delete
    }
}

// 2. Remove plugin options/settings
delete_option( 'msd_events_api_key' );
delete_option( 'msd_events_per_page' );
delete_option( 'msd_events_api_url' );

// If you have more plugin-specific options, remove them here
// delete_option( 'msd_events_other_setting' );

// 3. Clear transients/cache
global $wpdb;
$transients = $wpdb->get_col(
    "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_msd_events_list_%'"
);

foreach ( $transients as $transient ) {
    $key = str_replace( '_transient_', '', $transient );
    delete_transient( $key );
}

// 4. Optional: drop custom DB tables (if you ever created any)
// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}msd_events_meta" );

// Log cleanup (useful in dev mode)
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( 'MSD Events plugin uninstalled: all events, options, and caches removed.' );
}
