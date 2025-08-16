<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'MSD_Events_Form' ) ) {

    class MSD_Events_Form {

        public function __construct() {
            // Shortcode for frontend form
            add_shortcode( 'msd_event_form', [ $this, 'render_form_shortcode' ] );

            // Handle non-AJAX submissions
            add_action( 'admin_post_msd_event_submit', [ $this, 'handle_form_submission' ] );
            add_action( 'admin_post_nopriv_msd_event_submit', [ $this, 'handle_form_submission' ] );

            // Handle AJAX submissions
            add_action( 'wp_ajax_msd_event_submit', [ $this, 'handle_form_submission_ajax' ] );
            add_action( 'wp_ajax_nopriv_msd_event_submit', [ $this, 'handle_form_submission_ajax' ] );
        }

        /**
         * Render the event submission form.
         */
        public function render_form_shortcode() {
            ob_start();

            $template_path = msd_events_get_template( 'form-event-submit.php' );

            if ( file_exists( $template_path ) ) {
                include $template_path;
            } else {
                echo '<p>' . esc_html__( 'Form template not found.', 'msd-events' ) . '</p>';
            }

            return ob_get_clean();
        }

        /**
         * Handle standard (non-AJAX) form submission.
         */
        public function handle_form_submission() {
            if ( ! isset( $_POST['msd_event_nonce'] ) || 
                 ! msd_events_verify_nonce( $_POST['msd_event_nonce'], 'form_action' ) ) {
                wp_die( esc_html__( 'Security check failed.', 'msd-events' ) );
            }

            $result = $this->process_form_data( wp_unslash( $_POST ) );

            if ( is_wp_error( $result ) ) {
                wp_die( esc_html( $result->get_error_message() ) );
            }

            wp_safe_redirect( add_query_arg( 'event_submitted', '1', wp_get_referer() ) );
            exit;
        }

        /**
         * Handle AJAX form submission.
         */
        public function handle_form_submission_ajax() {
            if ( ! isset( $_POST['msd_event_nonce'] ) || 
                 ! msd_events_verify_nonce( $_POST['msd_event_nonce'], 'form_action' ) ) {
                wp_send_json_error( [ 'message' => __( 'Security check failed.', 'msd-events' ) ] );
            }

            $result = $this->process_form_data( wp_unslash( $_POST ) );

            if ( is_wp_error( $result ) ) {
                wp_send_json_error( [ 'message' => $result->get_error_message() ] );
            }

            wp_send_json_success( [ 'message' => __( 'Event submitted successfully!', 'msd-events' ) ] );
        }

        /**
         * Process form data and insert event post.
         */
        private function process_form_data( $data ) {
            $title       = msd_events_sanitize_text( $data['event_title'] ?? '' );
            $description = msd_events_sanitize_html( $data['event_description'] ?? '' );
            $date        = msd_events_sanitize_text( $data['event_date'] ?? '' );
            $location    = msd_events_sanitize_text( $data['event_location'] ?? '' );

            if ( empty( $title ) || empty( $date ) || empty( $location ) ) {
                return new WP_Error( 'missing_fields', __( 'Please fill in all required fields.', 'msd-events' ) );
            }

            // Geocode location
            $lat = '';
            $lng = '';
            if ( class_exists( 'MSD_Events_Geocode' ) ) {
                $geo    = new MSD_Events_Geocode();
                $coords = $geo->get_coordinates( $location );
                if ( ! is_wp_error( $coords ) ) {
                    $lat = $coords['lat'];
                    $lng = $coords['lng'];
                } else {
                    msd_events_log( $coords->get_error_message(), 'warning' );
                }
            }

            // Insert event
            $post_id = wp_insert_post( [
                'post_type'    => 'msd_event',
                'post_title'   => $title,
                'post_content' => $description,
                'post_status'  => 'pending',
            ], true );

            if ( is_wp_error( $post_id ) ) {
                msd_events_log( $post_id->get_error_message(), 'error' );
                return $post_id;
            }

            // Save meta
            update_post_meta( $post_id, '_msd_event_date', $date );
            update_post_meta( $post_id, '_msd_event_location', $location );
            update_post_meta( $post_id, '_msd_event_lat', $lat );
            update_post_meta( $post_id, '_msd_event_lng', $lng );

            return $post_id;
        }
    }
}
