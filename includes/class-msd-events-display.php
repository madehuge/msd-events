<?php
/**
 * Handles the display of events on the front-end with pagination and Google Maps.
 */

if ( ! class_exists( 'MSD_Events_Display' ) ) {

    class MSD_Events_Display {

        public function __construct() {
            add_shortcode( 'msd_events_list', [ $this, 'render_events_list' ] );
        }

        /**
         * Render the events list with pagination and map.
         */
        public function render_events_list( $atts ) {
            ob_start();

            // Determine per-page setting
            $per_page = get_option( 'msd_events_per_page', '' );
            if ( empty( $per_page ) || ! is_numeric( $per_page ) || $per_page <= 0 ) {
                $per_page = get_option( 'posts_per_page', 10 );
            }

            // Current page
            $paged = max( 1, get_query_var( 'paged' ) );

            // Query events CPT
            $args = [
                'post_type'      => 'msd_event',
                'post_status'    => 'publish',
                'paged'          => $paged,
                'posts_per_page' => (int) $per_page,
                'order'          => 'ASC'
            ];

            $events = new WP_Query( $args );

            if ( $events->have_posts() ) {
                echo '<div class="msd-events-list">';

                while ( $events->have_posts() ) {
                    $events->the_post();
                    $date = get_post_meta( get_the_ID(), 'event_date', true );
                    $location = get_post_meta( get_the_ID(), 'event_location', true );
                    $lat = get_post_meta( get_the_ID(), 'event_latitude', true );
                    $lng = get_post_meta( get_the_ID(), 'event_longitude', true );

                    echo '<div class="msd-event-item">';
                        echo '<h3>' . esc_html( get_the_title() ) . '</h3>';

                        if ( $date ) {
                            echo '<p><strong>Date:</strong> ' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) . '</p>';
                        }

                        if ( $location ) {
                            echo '<p><strong>Location:</strong> ' . esc_html( $location ) . '</p>';
                        }

                        echo '<div class="msd-event-excerpt">' . wp_kses_post( wp_trim_words( get_the_content(), 20 ) ) . '</div>';

                        // Google Map
                        if ( $lat && $lng ) {
                            $map_id = 'msd-map-' . get_the_ID();
                            echo '<div id="' . esc_attr( $map_id ) . '" style="width:100%; height:300px;"></div>';
                            echo "
                            <script>
                                function initMap{$map_id}() {
                                    var location = { lat: " . floatval( $lat ) . ", lng: " . floatval( $lng ) . " };
                                    var map = new google.maps.Map(document.getElementById('{$map_id}'), { zoom: 14, center: location });
                                    new google.maps.Marker({ position: location, map: map });
                                }
                                document.addEventListener('DOMContentLoaded', function() { initMap{$map_id}(); });
                            </script>";
                        }

                    echo '</div>';
                }

                echo '</div>';

                // Pagination
                $pagination = paginate_links( [
                    'total'   => $events->max_num_pages,
                    'current' => $paged,
                    'format'  => '?paged=%#%',
                    'type'    => 'list',
                ] );

                if ( $pagination ) {
                    echo '<div class="msd-events-pagination">' . $pagination . '</div>';
                }

                wp_reset_postdata();
            } else {
                echo '<p>' . esc_html__( 'No events found.', 'msd-events' ) . '</p>';
            }

            // Enqueue Google Maps API once
            $this->enqueue_google_maps();

            return ob_get_clean();
        }

        /**
         * Enqueue Google Maps API
         */
        protected function enqueue_google_maps() {
            static $loaded = false;

            if ( $loaded ) return;

            $api_key = 'YOUR_GOOGLE_MAPS_API_KEY'; // replace with your API key
            echo '<script async defer src="https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $api_key ) . '"></script>';

            $loaded = true;
        }

    }

    new MSD_Events_Display();
}
