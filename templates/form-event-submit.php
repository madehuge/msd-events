<?php
/**
 * Frontend Event Submission Form
 *
 * @package MSD_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<form id="msd-event-form" method="post">

    <?php 
    // Add nonce field for CSRF protection
    wp_nonce_field( 'msd_events_form_action', 'msd_event_nonce' ); 
    ?>

    <input type="hidden" name="action" value="msd_event_submit">

    <p>
        <label for="event_title">
            <?php esc_html_e( 'Event Title', 'msd-events' ); ?> 
            <span class="required">*</span>
        </label><br>
        <input type="text" id="event_title" name="event_title" 
               data-required="true" 
               data-error="<?php esc_attr_e('Event Title is required.', 'msd-events'); ?>">
    </p>

    <p>
        <label for="event_description"><?php esc_html_e( 'Event Description', 'msd-events' ); ?></label><br>
        <textarea id="event_description" name="event_description" rows="5"></textarea>
    </p>

    <p>
        <label for="event_datetime">
            <?php esc_html_e( 'Event Date & Time', 'msd-events' ); ?> 
            <span class="required">*</span>
        </label><br>
        <input type="datetime-local" id="event_datetime" name="event_datetime" 
               data-required="true" 
               data-error="<?php esc_attr_e('Event Date & Time is required.', 'msd-events'); ?>">
    </p>

    <p>
        <label for="event_location">
            <?php esc_html_e( 'Event Location', 'msd-events' ); ?> 
            <span class="required">*</span>
        </label><br>
        <input type="text" id="event_location" name="event_location" 
               data-required="true" 
               data-error="<?php esc_attr_e('Event Location is required.', 'msd-events'); ?>">
    </p>

    <p>
        <button type="submit" class="msd-btn">
            <?php esc_html_e( 'Submit Event', 'msd-events' ); ?>
        </button>
    </p>

    <!-- AJAX Response Message -->
    <div id="msd-form-message" style="display:none;"></div>
</form>
