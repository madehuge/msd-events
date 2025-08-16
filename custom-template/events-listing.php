<?php
/**
 * Template Name: Events Listing Pagess
 *
 * Displays events with Google Map.
 */

get_header(); 
?> 

<div class="events-listing-page">
    <h1><?php esc_html_e( 'Events', 'msd-events' ); ?></h1>

    <div class="events-content">
        <?php 
        // Display dynamic content from backend like the_content
        while ( have_posts() ) : the_post(); 
            the_content(); 
        endwhile; 
        ?>
    </div>
</div>

<?php get_footer(); ?>
