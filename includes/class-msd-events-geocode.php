<?php
/**
 * Handles geocoding API integration and caching for MSD Events.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MSD_Events_Geocode' ) ) {

    class MSD_Events_Geocode {

        /**
         * API Key from settings
         *
         * @var string
         */
        private $api_key;

        /**
         * API URL from settings
         *
         * @var string
         */
        private $api_url;

        /**
         * Constructor
         */
        public function __construct() {
            // Load settings from DB
            $this->api_key = get_option( MSD_EVENTS_API_KEY_OPTION, '' );
            $this->api_url = get_option( MSD_EVENTS_GEOCODE_API_URL_OPTION, '' ); // Dynamic URL from settings.php
        }

        /**
         * Get coordinates (lat/lng) for an address
         *
         * @param string $address
         * @return array|WP_Error
         */
        public function get_coordinates( $address ) {
            if ( empty( $address ) ) {
                return new WP_Error( 'no_address', __( 'No address provided.', 'msd-events' ) );
            }

            // Check cache first
            $cache_key = MSD_EVENTS_GEO_CACHE_PREFIX . md5( strtolower( $address ) );
            $cached    = get_transient( $cache_key );

            if ( $cached !== false ) {
                return $cached;
            }

            // Build API request
            $params = [
                'address' => $address,
                'key'     => $this->api_key,
            ];

            // Use dynamic API URL from settings
            $url = add_query_arg( $params, $this->api_url );

            $response = wp_remote_get( $url );

            if ( is_wp_error( $response ) ) {
                return $response;
            }

            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );

            if ( empty( $data['results'] ) || $data['status'] !== 'OK' ) {
                return new WP_Error( 'geocode_failed', __( 'Geocoding failed.', 'msd-events' ) );
            }

            $location = $data['results'][0]['geometry']['location'];

            $coordinates = [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
            ];

            // Cache for 1 week
            set_transient( $cache_key, $coordinates, WEEK_IN_SECONDS );

            return $coordinates;
        }
    }
}
