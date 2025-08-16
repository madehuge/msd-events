<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'MSD_Events_Template' ) ) {

    class MSD_Events_Template {

        protected $templates = [];
        protected $templates_dir;
        protected $template_file = 'events-listing.php';

        public function __construct() {
            // Path to the templates directory inside the plugin
            $this->templates_dir = MSD_EVENTS_DIR . 'custom-template/';

            // Hooks
            add_filter( 'theme_page_templates', [ $this, 'register_templates' ] );
            add_filter( 'template_include', [ $this, 'load_template' ] );
        }

        // Add plugin template to Page Template dropdown
        public function register_templates( $templates ) {
            // Translation happens here, after init
            $templates[ $this->template_file ] = __( 'Events Listing Page', 'msd-events' );
            return $templates;
        }

        // Load the template from plugin folder if selected
        public function load_template( $template ) {
            if ( is_page() ) {
                $page_id = get_queried_object_id();
                $page_template = get_post_meta( $page_id, '_wp_page_template', true );

                if ( $page_template === $this->template_file ) {
                    $plugin_template = $this->templates_dir . $this->template_file;

                    if ( file_exists( $plugin_template ) ) {
                        return $plugin_template;
                    }
                }
            }

            return $template;
        }
    }

    
}
