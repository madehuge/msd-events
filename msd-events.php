<?php
/**
 * Plugin Name: MSD Events
 * Plugin URI:  https://react-portfolio-livid-psi.vercel.app/msd-events
 * Description: Events CPT with front-end submission, geocoding integration and Gutenberg block.
 * Version:     0.1.0
 * Author:      Manish Kumar Jangir
 * Author URI:  https://react-portfolio-livid-psi.vercel.app
 * Text Domain: msd-events
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Minimum PHP version check
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="error"><p>MSD Events requires PHP 7.4 or higher.</p></div>';
    } );
    return;
}

class MSD_Events {

    const VERSION = '0.1.0';
    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
            self::$instance->setup_constants();
            self::$instance->includes();
            self::$instance->init_hooks();
        }
        return self::$instance;
    }

    private function setup_constants() {
        define( 'MSD_EVENTS_VERSION', self::VERSION );
        define( 'MSD_EVENTS_DIR', plugin_dir_path( __FILE__ ) );
        define( 'MSD_EVENTS_URL', plugin_dir_url( __FILE__ ) );
        define( 'MSD_EVENTS_FILE', __FILE__ );
    }

    private function includes() {
        //require_once MSD_EVENTS_DIR . 'includes/class-msd-events-cpt.php';  DELETE THIS LINE
        require_once MSD_EVENTS_DIR . 'includes/cpt/interfaces/interface-cpt-registrable.php';
        require_once MSD_EVENTS_DIR . 'includes/cpt/interfaces/interface-taxonomy-registrable.php';
        require_once MSD_EVENTS_DIR . 'includes/cpt/interfaces/interface-meta-box-registrable.php';

        require_once MSD_EVENTS_DIR . 'includes/cpt/class-msd-events-cpt.php';
        require_once MSD_EVENTS_DIR . 'includes/cpt/class-msd-events-taxonomy.php';
        require_once MSD_EVENTS_DIR . 'includes/cpt/class-msd-events-meta-box.php';
        require_once MSD_EVENTS_DIR . 'includes/cpt/class-msd-events-admin-columns.php';

        //require_once MSD_EVENTS_DIR . 'includes/class-msd-events-settings.php';
        //require_once MSD_EVENTS_DIR . 'includes/class-msd-events-form.php';
        //require_once MSD_EVENTS_DIR . 'includes/class-msd-events-geocode.php';
        //require_once MSD_EVENTS_DIR . 'includes/class-msd-events-display.php';
        // Block class will be added later
       // require_once MSD_EVENTS_DIR . 'includes/helpers.php';
    }

    private function init_hooks() {
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        // CPT instance

        new MSD_Events_CPT();
        new MSD_Events_Taxonomy();
        new MSD_Events_Meta_Box();

        if ( is_admin() ) {
            new MSD_Events_Admin_Columns();
        }

        // Settings instance
       // new MSD_Events_Settings();

        // Form instance
        //new MSD_Events_Form();
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'msd-events',
            false,
            dirname( plugin_basename( __FILE__ ) ) . '/languages'
        );
    }

}

// Load the plugin
MSD_Events::instance();
