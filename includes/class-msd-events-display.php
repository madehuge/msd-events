<?php
/**
 * Handles the display of events on the front-end with pagination.
 */

if ( ! class_exists( 'MSD_Events_Display' ) ) {

    class MSD_Events_Display {

        public function __construct() {
            add_shortcode( 'msd_events_list', [ $this, 'render_events_list' ] );
        }

        /**
         * Render the events list with pagination.
         */
        public function render_events_list( $atts ) {
            ob_start();

            // Determine per-page setting: plugin option OR WP default
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
                'orderby'        => 'meta_value',
                'meta_key'       => 'event_date',
                'order'          => 'ASC',
            ];

            $events = new WP_Query( $args );

            if ( $events->have_posts() ) {
                echo '<div class="msd-events-list">';
                while ( $events->have_posts() ) {
                    $events->the_post();
                    $date = get_post_meta( get_the_ID(), 'event_date', true );
                    $location = get_post_meta( get_the_ID(), 'event_location', true );

                    echo '<div class="msd-event-item">';
                        echo '<h3>' . esc_html( get_the_title() ) . '</h3>';
                        if ( $date ) {
                            echo '<p><strong>Date:</strong> ' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) . '</p>';
                        }
                        if ( $location ) {
                            echo '<p><strong>Location:</strong> ' . esc_html( $location ) . '</p>';
                        }
                        echo '<div class="msd-event-excerpt">' . wp_kses_post( wp_trim_words( get_the_content(), 20 ) ) . '</div>';
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

            return ob_get_clean();
        }
    }

    new MSD_Events_Display();
}
