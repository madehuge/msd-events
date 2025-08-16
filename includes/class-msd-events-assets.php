<?php
/**
 * Handles plugin scripts and styles.
 *
 * @package MSD_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MSD_Events_Assets' ) ) {
    class MSD_Events_Assets {

        public function __construct() {
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        }

        public function enqueue_assets() {
            // Enqueue plugin stylesheet
            wp_enqueue_style(
                'msd-events-css',
                MSD_EVENTS_URL . 'assets/css/msd-events.css',
                [],
                '1.0.0'
            );

            // Load form validation JS only when needed
            if ( is_singular() ) {
                wp_enqueue_script(
                    'msd-event-form-validation',
                    MSD_EVENTS_URL . 'assets/js/form-validation.js',
                    [ 'jquery' ],
                    '1.0.0',
                    true
                );

                // Localize script to pass AJAX URL
                wp_localize_script(
                    'msd-event-form-validation',
                    'msd_ajax_object',
                    array(
                        'ajax_url' => admin_url( 'admin-ajax.php' )
                    )
                );
            }
        }
    }
}
