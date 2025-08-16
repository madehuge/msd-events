<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MSD_Events_Settings' ) ) {

    class MSD_Events_Settings {

        /**
         * Option keys.
         */
        const OPTION_API_KEY      = 'msd_events_api_key';
        const OPTION_API_URL      = 'msd_events_api_url';
        const OPTION_PER_PAGE     = 'msd_events_per_page';

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
            add_action( 'admin_init', array( $this, 'register_settings' ) );
        }

        /**
         * Add settings page.
         */
        public function add_settings_page() {
            add_options_page(
                __( 'MSD Events Settings', 'msd-events' ),
                __( 'MSD Events', 'msd-events' ),
                'manage_options',
                'msd-events-settings',
                array( $this, 'render_settings_page' )
            );
        }

        /**
         * Register plugin settings.
         */
        public function register_settings() {
            // API Key
            register_setting( 'msd_events_settings_group', self::OPTION_API_KEY, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            ) );

            // API URL
            register_setting( 'msd_events_settings_group', self::OPTION_API_URL, array(
                'type'              => 'string',
                'sanitize_callback' => 'esc_url_raw',
                'default'           => 'https://maps.googleapis.com/maps/api/geocode/json',
            ) );

            // Pagination setting
            register_setting( 'msd_events_settings_group', self::OPTION_PER_PAGE, array(
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'default'           => get_option( 'posts_per_page' ),
            ) );

            add_settings_section(
                'msd_events_main_section',
                __( 'General Settings', 'msd-events' ),
                '__return_false',
                'msd-events-settings'
            );

            // API Key field
            add_settings_field(
                self::OPTION_API_KEY,
                __( 'Google Geocoding API Key', 'msd-events' ),
                array( $this, 'render_api_key_field' ),
                'msd-events-settings',
                'msd_events_main_section'
            );

            // API URL field
            add_settings_field(
                self::OPTION_API_URL,
                __( 'Google Geocoding API URL', 'msd-events' ),
                array( $this, 'render_api_url_field' ),
                'msd-events-settings',
                'msd_events_main_section'
            );

            // Pagination field
            add_settings_field(
                self::OPTION_PER_PAGE,
                __( 'Events Per Page', 'msd-events' ),
                array( $this, 'render_per_page_field' ),
                'msd-events-settings',
                'msd_events_main_section'
            );
        }

        /**
         * Render API key input field.
         */
        public function render_api_key_field() {
            $value = esc_attr( get_option( self::OPTION_API_KEY, '' ) );
            echo '<input type="text" name="' . esc_attr( self::OPTION_API_KEY ) . '" value="' . $value . '" class="regular-text" />';
        }

        /**
         * Render API URL input field.
         */
        public function render_api_url_field() {
            $default_url = 'https://maps.googleapis.com/maps/api/geocode/json';
            $value       = esc_url( get_option( self::OPTION_API_URL, $default_url ) );
            echo '<input type="url" name="' . esc_attr( self::OPTION_API_URL ) . '" value="' . $value . '" class="regular-text code" />';
            echo '<p class="description">' . __( 'Base URL for the Google Geocoding API.', 'msd-events' ) . '</p>';
        }

        /**
         * Render per-page input field.
         */
        public function render_per_page_field() {
            $default_ppp = get_option( 'posts_per_page' );
            $value       = absint( get_option( self::OPTION_PER_PAGE, $default_ppp ) );
            echo '<input type="number" name="' . esc_attr( self::OPTION_PER_PAGE ) . '" value="' . $value . '" class="small-text" min="1" />';
            echo '<p class="description">' . sprintf( __( 'Leave blank to use WordPress default (%d).', 'msd-events' ), $default_ppp ) . '</p>';
        }

        /**
         * Render settings page HTML.
         */
        public function render_settings_page() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            ?>
            <div class="wrap">
                <h1><?php _e( 'MSD Events Settings', 'msd-events' ); ?></h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'msd_events_settings_group' );
                    do_settings_sections( 'msd-events-settings' );
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Helper: Get API key.
         */
        public static function get_api_key() {
            return get_option( self::OPTION_API_KEY, '' );
        }

        /**
         * Helper: Get API URL.
         */
        public static function get_api_url() {
            return get_option( self::OPTION_API_URL, 'https://maps.googleapis.com/maps/api/geocode/json' );
        }

        /**
         * Helper: Get per-page value or fallback.
         */
        public static function get_per_page() {
            $pp = absint( get_option( self::OPTION_PER_PAGE ) );
            if ( $pp <= 0 ) {
                return absint( get_option( 'posts_per_page' ) );
            }
            return $pp;
        }

    }

}
