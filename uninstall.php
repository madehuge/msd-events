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

global $wpdb;

/**
 * 1. Delete all Events CPT posts in batches
 * -----------------------------------------
 * This prevents memory exhaustion on large sites.
 */
$batch_size = 500;

do {
    $event_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s LIMIT %d",
            'msd_event',
            $batch_size
        )
    );

    if ( empty( $event_ids ) ) {
        break;
    }

    // Suspend cache invalidation for performance
    wp_suspend_cache_invalidation( true );

    foreach ( $event_ids as $event_id ) {
        wp_delete_post( $event_id, true ); // force delete
    }

    wp_suspend_cache_invalidation( false );

    // Small pause between batches (optional)
    usleep( 50000 ); // 50ms

} while ( ! empty( $event_ids ) );

/**
 * 2. Remove plugin options/settings
 */
delete_option( 'msd_events_api_key' );
delete_option( 'msd_events_per_page' );
delete_option( 'msd_events_api_url' );
// delete_option( 'msd_events_other_setting' ); // extend here

/**
 * 3. Clear all transients/cache created by this plugin
 */

// General plugin transients
$transients = $wpdb->get_col(
    "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_msd_events_%'"
);

foreach ( $transients as $transient ) {
    $key = str_replace( '_transient_', '', $transient );
    delete_transient( $key );
}

// Geocoding cache (with md5 hash keys)
if ( defined( 'MSD_EVENTS_GEO_CACHE_PREFIX' ) ) {
    $geo_transients = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
            '_transient_' . MSD_EVENTS_GEO_CACHE_PREFIX . '%'
        )
    );

    foreach ( $geo_transients as $transient ) {
        $key = str_replace( '_transient_', '', $transient );
        delete_transient( $key );
    }
}

/**
 * 4. Drop custom DB tables (if any were created)
 */
// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}msd_events_meta" );

/**
 * 5. Log cleanup (dev only)
 */
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( 'MSD Events plugin uninstalled: events (batched), options, and caches removed.' );
}
