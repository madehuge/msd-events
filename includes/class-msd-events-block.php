<?php
/**
 * Class MSD_Events_Block
 *
 * Registers the Gutenberg block for displaying events.
 *
 * @package MSD_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'MSD_Events_Block' ) ) {

    class MSD_Events_Block {

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'init', array( $this, 'register_block' ) );
        }

        /**
         * Register Gutenberg block.
         */
        public function register_block() {

            // Register editor script for the block
            wp_register_script(
                'msd-events-block',
                plugins_url( '../assets/js/block.js', __FILE__ ),
                array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
                filemtime( plugin_dir_path( __FILE__ ) . '../assets/js/block.js' ),
                true
            );

            register_block_type( 'msd-events/event-list', array(
                'editor_script'   => 'msd-events-block',
                'render_callback' => array( $this, 'render_block' ),
            ) );
        }

        /**
         * Render callback for block.
         *
         * @param array $attributes Block attributes.
         * @return string HTML output.
         */
        public function render_block( $attributes ) {
            // Output events using shortcode to avoid duplication of logic
            return do_shortcode( '[msd_events_list]' );
        }
    }
}

// Instantiate only once
if ( class_exists( 'MSD_Events_Block' ) && ! isset( $GLOBALS['msd_events_block'] ) ) {
    $GLOBALS['msd_events_block'] = new MSD_Events_Block();
}
