<?php
/**
 * Handles the display of events on the front-end with caching and Google Maps.
 */

if ( ! class_exists( 'MSD_Events_Display' ) ) {

    class MSD_Events_Display {

        public function __construct() {
            add_shortcode( 'msd_events_list', [ $this, 'render_events_list' ] );
        }

        /**
         * Render the events list with caching.
         */
        public function render_events_list( $atts ) {
            $per_page = get_option( 'msd_events_per_page', 10 );
            $paged    = max( 1, get_query_var( 'paged' ) );

            // Use a transient for caching
            $cache_key = 'msd_events_list_' . $paged . '_' . $per_page;
            $cached_output = get_transient( $cache_key );

            if ( $cached_output ) {
                return $cached_output; // Return cached HTML
            }

            ob_start();

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
                    
                    $date      = get_post_meta( get_the_ID(), '_msd_event_date', true );
                    $location  = get_post_meta( get_the_ID(), '_msd_event_location', true );
                    $lat       = get_post_meta( get_the_ID(), '_msd_event_lat', true );
                    $lng       = get_post_meta( get_the_ID(), '_msd_event_lng', true );

                    echo '<div class="msd-event-item">';
                        echo '<h3>' . esc_html( get_the_title() ) . '</h3>';

                        if ( $date ) {
                            echo '<p><strong>Date:</strong> ' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) . '</p>';
                        }

                        if ( $location ) {
                            echo '<p><strong>Location:</strong> ' . esc_html( $location ) . '</p>';
                        }

                        echo '<div class="msd-event-excerpt">' . wp_kses_post( wp_trim_words( get_the_content(), 20 ) ) . '</div>';

                        // Google Map container
                        if ( $lat && $lng ) : 
                            $map_id = 'msd-map-' . get_the_ID(); 
                        ?>
                            <div 
                                id="<?php echo esc_attr( $map_id ); ?>" 
                                class="msd-event-map" 
                                data-lat="<?php echo esc_attr( $lat ); ?>" 
                                data-lng="<?php echo esc_attr( $lng ); ?>" 
                                style="width:100%; height:300px">
                            </div>
                        <?php endif;

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

            // Enqueue Google Maps once
            $this->enqueue_google_maps();

            $output = ob_get_clean();

            // Store output in transient for 12 hours (43200 seconds)
            set_transient( $cache_key, $output, 12 * HOUR_IN_SECONDS );

            return $output;
        }

        /**
         * Enqueue Google Maps API
         */
        protected function enqueue_google_maps() {
            static $loaded = false;

            if ( $loaded ) return;

            // Get API key from plugin settings
            $api_key = get_option( 'msd_events_api_key', '' );

            if ( ! empty( $api_key ) ) {
                echo '<script async defer src="https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $api_key ) . '"></script>';
                $loaded = true;
            } else {
                error_log( 'MSD Events: Google Maps API key is missing in plugin settings.' );
            }
        }


    }

    new MSD_Events_Display();
}
