<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'MSD_Events_Assets' ) ) {
    class MSD_Events_Assets {

        public function __construct() {
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
            add_filter( 'script_loader_tag', [ $this, 'add_async_defer' ], 10, 3 );
        }

        public function enqueue_assets() {
            if ( is_singular() ) {
                // CSS
                wp_enqueue_style(
                    'msd-events-css',
                    MSD_EVENTS_URL . 'assets/css/msd-events.css',
                    [],
                    MSD_EVENTS_VERSION
                );

                // Form validation JS
                wp_enqueue_script(
                    'msd-event-form-validation',
                    MSD_EVENTS_URL . 'assets/js/form-validation.js',
                    [ 'jquery' ],
                    MSD_EVENTS_VERSION,
                    true
                );

                wp_localize_script(
                    'msd-event-form-validation',
                    'msd_ajax_object',
                    [
                        'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
                    ]
                );

                // Google Maps API
                $api_key = MSD_Events_Settings::get_api_key();
                if ( ! empty( $api_key ) ) {
                    $maps_url = add_query_arg( [
                        'key'       => $api_key,
                        'libraries' => 'places',
                        'callback'  => 'initAutocomplete'
                    ], 'https://maps.googleapis.com/maps/api/js' );

                    wp_register_script(
                        'msd-google-maps',
                        esc_url( $maps_url ),
                        [],
                        null,
                        true
                    );

                    wp_enqueue_script( 'msd-google-maps' );
                }
            }
        }

        /**
         * Add async + defer to Google Maps script
         */
        public function add_async_defer( $tag, $handle, $src ) {
            if ( 'msd-google-maps' === $handle ) {
                return '<script src="' . esc_url( $src ) . '" async defer></script>';
            }
            return $tag;
        }
    }
}
