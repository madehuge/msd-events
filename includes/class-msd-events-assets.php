<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'MSD_Events_Assets' ) ) {
    class MSD_Events_Assets {

        public function __construct() {
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        }

        public function enqueue_assets() {
            // Enqueue plugin CSS (conditionally if needed)
            if ( is_singular() ) {
                wp_enqueue_style(
                    'msd-events-css',
                    MSD_EVENTS_URL . 'assets/css/msd-events.css',
                    [],
                    '1.0.0'
                );
            }

            // Enqueue form validation JS only on relevant pages
            if ( is_singular() ) {
                wp_enqueue_script(
                    'msd-event-form-validation',
                    MSD_EVENTS_URL . 'assets/js/form-validation.js',
                    [ 'jquery' ],
                    '1.0.0',
                    true
                );

                wp_localize_script(
                    'msd-event-form-validation',
                    'msd_ajax_object',
                    [
                        'ajax_url' => admin_url( 'admin-ajax.php' ),
                    ]
                );

                // Load Google Maps API async with defer
                $api_key = MSD_Events_Settings::get_api_key();
                if ( ! empty( $api_key ) ) {
                    add_action('wp_footer', function() use ( $api_key ) {
                        ?>
                        <script>
                        (function() {
                            var script = document.createElement('script');
                            script.src = "https://maps.googleapis.com/maps/api/js?key=<?php echo esc_js($api_key); ?>&libraries=places&callback=initAutocomplete";
                            script.async = true;
                            script.defer = true;
                            document.head.appendChild(script);
                        })();
                        </script>
                        <?php
                    });
                }
            }
        }
    }
}
