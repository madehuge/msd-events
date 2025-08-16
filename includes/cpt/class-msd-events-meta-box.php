<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MSD_EVENTS_DIR . 'includes/cpt/interfaces/interface-meta-box-registrable.php';

class MSD_Events_Meta_Box implements MSD_Meta_Box_Registrable {

    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
        add_action( 'save_post_msd_event', [ $this, 'save_event_meta' ], 10, 2 );
    }

    public function register_meta_boxes() {
        add_meta_box(
            'msd_event_details',
            __( 'Event Details', 'msd-events' ),
            [ $this, 'render_event_meta_box' ],
            'msd_event',
            'normal',
            'default'
        );
    }

    public function render_event_meta_box( $post ) {
        wp_nonce_field( 'msd_event_meta_nonce', 'msd_event_meta_nonce_field' );

        $event_date     = get_post_meta( $post->ID, '_msd_event_date', true );
        $event_location = get_post_meta( $post->ID, '_msd_event_location', true );

        ?>
        <p>
            <label for="msd_event_date"><?php esc_html_e( 'Event Date & Time', 'msd-events' ); ?></label><br>
            <input type="datetime-local" 
                   id="msd_event_date" 
                   name="msd_event_date" 
                   value="<?php echo esc_attr( $event_date ); ?>" />
        </p>
        <p>
            <label for="msd_event_location"><?php esc_html_e( 'Event Location', 'msd-events' ); ?></label><br>
            <input type="text" 
                   id="msd_event_location" 
                   name="msd_event_location" 
                   value="<?php echo esc_attr( $event_location ); ?>" 
                   class="widefat" />
        </p>
        <?php
    }

    public function save_event_meta( $post_id, $post ) {
        if ( ! isset( $_POST['msd_event_meta_nonce_field'] ) ||
             ! wp_verify_nonce( $_POST['msd_event_meta_nonce_field'], 'msd_event_meta_nonce' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['msd_event_date'] ) ) {
            update_post_meta( $post_id, '_msd_event_date', sanitize_text_field( $_POST['msd_event_date'] ) );
        }

        if ( isset( $_POST['msd_event_location'] ) ) {
            update_post_meta( $post_id, '_msd_event_location', sanitize_text_field( $_POST['msd_event_location'] ) );
        }
    }
}
