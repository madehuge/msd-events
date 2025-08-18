<?php
/**
 * Handles the display of events on the front-end with caching and Google Maps.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MSD_Events_Display' ) ) {

    class MSD_Events_Display {

        public function __construct() {
            add_shortcode( 'msd_events_list', [ $this, 'render_events_list' ] );

            // Clear cache when events change
            add_action( 'save_post_msd_event', [ $this, 'clear_cache' ] );
            add_action( 'deleted_post', [ $this, 'clear_cache' ] );
        }

        /**
         * Render the events list with caching.
         */
        public function render_events_list( $atts ) {
            $atts = shortcode_atts(
                [
                    'per_page' => get_option( 'msd_events_per_page', 10 ),
                ],
                $atts,
                'msd_events_list'
            );

            $per_page = (int) $atts['per_page'];
            $paged    = max( 1, get_query_var( 'paged' ) );

            // Use transient caching
            $cache_key     = 'msd_events_list_' . $paged . '_' . $per_page;
            $cached_output = get_transient( $cache_key );

            if ( $cached_output ) {
                return $cached_output;
            }

            ob_start();

            $args = [
                'post_type'      => 'msd_event',
                'post_status'    => 'publish',
                'paged'          => $paged,
                'posts_per_page' => $per_page,
                'order'          => 'ASC',
            ];

            $events = new WP_Query( $args );

            if ( $events->have_posts() ) {
                echo '<div class="msd-events-list">';

                while ( $events->have_posts() ) {
                    $events->the_post();

                    $date     = get_post_meta( get_the_ID(), '_msd_event_date', true );
                    $location = get_post_meta( get_the_ID(), '_msd_event_location', true );
                    $lat      = get_post_meta( get_the_ID(), '_msd_event_lat', true );
                    $lng      = get_post_meta( get_the_ID(), '_msd_event_lng', true );

                    echo '<div class="msd-event-item">';
                        echo '<h3>' . esc_html( get_the_title() ) . '</h3>';

                        if ( $date ) {
                            echo '<p><strong>' . esc_html__( 'Date:', 'msd-events' ) . '</strong> ' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) . '</p>';
                        }

                        if ( $location ) {
                            echo '<p><strong>' . esc_html__( 'Location:', 'msd-events' ) . '</strong> ' . esc_html( $location ) . '</p>';
                        }

                        echo '<div class="msd-event-excerpt">' . wp_kses_post( wp_trim_words( get_the_content(), 20 ) ) . '</div>';

                        if ( $lat && $lng ) {
                            $map_id = 'msd-map-' . get_the_ID();
                            echo '<div 
                                    id="' . esc_attr( $map_id ) . '" 
                                    class="msd-event-map" 
                                    data-lat="' . esc_attr( $lat ) . '" 
                                    data-lng="' . esc_attr( $lng ) . '" 
                                    style="width:100%; height:300px"></div>';
                        }

                    echo '</div>';
                }

                echo '</div>';

                $pagination = paginate_links( [
                    'total'   => $events->max_num_pages,
                    'current' => $paged,
                    'format'  => '?paged=%#%',
                    'type'    => 'list',
                ] );

                if ( $pagination ) {
                    echo '<div class="msd-events-pagination">' . wp_kses_post( $pagination ) . '</div>';
                }

                wp_reset_postdata();
            } else {
                echo '<p>' . esc_html__( 'No events found.', 'msd-events' ) . '</p>';
            }

            // Ensure Google Maps script is enqueued
            $this->enqueue_google_maps();

            $output = ob_get_clean();

            // Store cached HTML for 12 hours
            set_transient( $cache_key, $output, 12 * HOUR_IN_SECONDS );

            return $output;
        }

        /**
         * Enqueue Google Maps API
         */
        protected function enqueue_google_maps() {
            static $loaded = false;
            if ( $loaded ) {
                return;
            }

            $api_key = get_option( 'msd_events_api_key', '' );
            if ( ! empty( $api_key ) ) {
                wp_enqueue_script(
                    'google-maps',
                    esc_url( 'https://maps.googleapis.com/maps/api/js?key=' . rawurlencode( $api_key ) ),
                    [],
                    null,
                    true
                );
               
                $loaded = true;
            } else {
                error_log( 'MSD Events: Google Maps API key is missing in plugin settings.' );
            }
        }

        /**
         * Clear cached transients when events are updated/deleted
         */
        public function clear_cache() {
            global $wpdb;
            $transients = $wpdb->get_col(
                "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_msd_events_list_%'"
            );
            foreach ( $transients as $transient ) {
                $key = str_replace( '_transient_', '', $transient );
                delete_transient( $key );
            }
        }
    }

    new MSD_Events_Display();
}
