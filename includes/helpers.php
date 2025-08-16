<?php
/**
 * Helper functions for MSD Events plugin
 *
 * @package MSD_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! function_exists( 'msd_events_sanitize_text' ) ) {
    /**
     * Sanitize text input
     *
     * @param string $text Raw text input.
     * @return string Sanitized text.
     */
    function msd_events_sanitize_text( $text ) {
        return sanitize_text_field( wp_strip_all_tags( $text ) );
    }
}

if ( ! function_exists( 'msd_events_sanitize_html' ) ) {
    /**
     * Sanitize HTML input, allowing limited tags
     *
     * @param string $html HTML input.
     * @return string Sanitized HTML.
     */
    function msd_events_sanitize_html( $html ) {
        return wp_kses_post( $html );
    }
}

if ( ! function_exists( 'msd_events_log' ) ) {
    /**
     * Log debug/info messages (only if WP_DEBUG is true)
     *
     * @param mixed $message The message to log.
     * @param string $level Log level (info|warning|error).
     * @return void
     */
    function msd_events_log( $message, $level = 'info' ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            $log_message = sprintf(
                '[%s] %s: %s',
                strtoupper( $level ),
                current_time( 'mysql' ),
                is_array( $message ) || is_object( $message ) ? wp_json_encode( $message ) : $message
            );
            error_log( $log_message ); // Writes to PHP error log.
        }
    }
}

if ( ! function_exists( 'msd_events_get_option' ) ) {
    /**
     * Get plugin option with default fallback
     *
     * @param string $key Option key.
     * @param mixed  $default Default value if not set.
     * @return mixed
     */
    function msd_events_get_option( $key, $default = '' ) {
        $options = get_option( 'msd_events_settings', [] );
        return isset( $options[ $key ] ) && '' !== $options[ $key ] ? $options[ $key ] : $default;
    }
}

if ( ! function_exists( 'msd_events_get_pagination_limit' ) ) {
    /**
     * Get pagination limit from settings or WP default
     *
     * @return int
     */
    function msd_events_get_pagination_limit() {
        $limit = (int) msd_events_get_option( 'events_per_page', 0 );
        if ( $limit > 0 ) {
            return $limit;
        }
        return (int) get_option( 'posts_per_page', 10 ); // WP default.
    }
}

if ( ! function_exists( 'msd_events_generate_nonce' ) ) {
    /**
     * Generate plugin-specific nonce
     *
     * @param string $action Action name.
     * @return string Nonce value.
     */
    function msd_events_generate_nonce( $action ) {
        return wp_create_nonce( 'msd_events_' . $action );
    }
}

if ( ! function_exists( 'msd_events_verify_nonce' ) ) {
    /**
     * Verify plugin-specific nonce
     *
     * @param string $nonce Nonce value.
     * @param string $action Action name.
     * @return bool
     */
    function msd_events_verify_nonce( $nonce, $action ) {
        return wp_verify_nonce( $nonce, 'msd_events_' . $action );
    }
}

if ( ! function_exists( 'msd_events_get_template' ) ) {
    /**
     * Locate and load a template from theme or plugin
     *
     * @param string $template_name Template file name.
     * @return string Template path.
     */
    function msd_events_get_template( $template_name ) {
        $template_path = locate_template( [ 'msd-events/' . $template_name ] );
        if ( ! $template_path ) {
            $template_path = MSD_EVENTS_DIR . 'templates/' . $template_name;
        }
        return apply_filters( 'msd_events_template_path', $template_path, $template_name );
    }
}
