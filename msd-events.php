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

/**
 * Minimum PHP version check
 */
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
    add_action( 'admin_notices', function() {
        printf(
            '<div class="error"><p>%s</p></div>',
            esc_html__( 'MSD Events requires PHP 7.4 or higher.', 'msd-events' )
        );
    } );
    return;
}

final class MSD_Events {

    const VERSION = '0.1.0';
    private static $instance = null;

    /**
     * Singleton Instance
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
            self::$instance->setup_constants();
            self::$instance->includes();
            self::$instance->init_hooks();
        }
        return self::$instance;
    }

    /**
     * Define plugin constants
     */
    private function setup_constants() {
        if ( ! defined( 'MSD_EVENTS_VERSION' ) ) {
            define( 'MSD_EVENTS_VERSION', self::VERSION );
        }
        if ( ! defined( 'MSD_EVENTS_DIR' ) ) {
            define( 'MSD_EVENTS_DIR', plugin_dir_path( __FILE__ ) );
        }
        if ( ! defined( 'MSD_EVENTS_URL' ) ) {
            define( 'MSD_EVENTS_URL', plugin_dir_url( __FILE__ ) );
        }
        if ( ! defined( 'MSD_EVENTS_FILE' ) ) {
            define( 'MSD_EVENTS_FILE', __FILE__ );
        }
    }

    /**
     * Load required files
     */
    private function includes() {
        require_once MSD_EVENTS_DIR . 'includes/cpt/interfaces/interface-cpt-registrable.php';
        require_once MSD_EVENTS_DIR . 'includes/cpt/interfaces/interface-taxonomy-registrable.php';
        require_once MSD_EVENTS_DIR . 'includes/cpt/interfaces/interface-meta-box-registrable.php';

        require_once MSD_EVENTS_DIR . 'includes/cpt/class-msd-events-cpt.php';
        require_once MSD_EVENTS_DIR . 'includes/cpt/class-msd-events-taxonomy.php';
        require_once MSD_EVENTS_DIR . 'includes/cpt/class-msd-events-meta-box.php';
        require_once MSD_EVENTS_DIR . 'includes/cpt/class-msd-events-admin-columns.php';

        require_once MSD_EVENTS_DIR . 'includes/class-msd-events-settings.php';
        require_once MSD_EVENTS_DIR . 'includes/class-msd-events-form.php';
        require_once MSD_EVENTS_DIR . 'includes/class-msd-events-geocode.php';
        require_once MSD_EVENTS_DIR . 'includes/class-msd-events-display.php';
        require_once MSD_EVENTS_DIR . 'includes/class-msd-events-template.php';
        require_once MSD_EVENTS_DIR . 'includes/class-msd-events-assets.php';

        require_once MSD_EVENTS_DIR . 'includes/helpers.php';
    }

    /**
     * Hook into WordPress
     */
    private function init_hooks() {
        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );

        // Register core components on init (correct timing for CPT/Tax/Meta)
        add_action( 'init', function() {
            new MSD_Events_CPT();
            new MSD_Events_Taxonomy();
            new MSD_Events_Meta_Box();
        }, 5 );

        if ( is_admin() ) {
            new MSD_Events_Admin_Columns();
        }

        // Settings
        new MSD_Events_Settings();

        // Forms (frontend submission with nonce/validation inside that class)
        new MSD_Events_Form();

        // Template loader
        new MSD_Events_Template();

        // Assets (enqueue scripts/styles safely)
        new MSD_Events_Assets();
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'msd-events',
            false,
            dirname( plugin_basename( __FILE__ ) ) . '/languages'
        );
    }
}

/**
 * Plugin activation/deactivation hooks
 */
register_activation_hook( __FILE__, function() {
    // Flush rewrite rules for CPTs
    MSD_Events::instance();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );

// Load the plugin
MSD_Events::instance();
