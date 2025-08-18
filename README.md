# MSD Events

## Description
A WordPress plugin that provides an Events Custom Post Type (CPT), front-end submission form, geocoding integration (Google Geocoding API), and Gutenberg block for displaying events.

## Requirements
- WordPress 5.8+
- PHP 7.4+
- Google Maps Geocoding API key

## Installation
1. Upload the msd-events folder to `/wp-content/plugins/` via FTP or upload the `msd-events.zip` file via `WordPress Dashboard → Plugins → Add New → Upload Plugin`.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to Settings -> MSD Events and add your Google Geocoding API key.
4. Use the `[msd_event_form]` shortcode to display the front-end form, or use the Gutenberg block to show events.
5. Use the `[msd_events_list per_page="5"]` shortcode to display the events at any page/post, or use the Gutenberg block to display events.

### Git Clone (Developer Setup)
If you are setting this up as a developer:

1. Navigate to your WordPress `plugins` directory:
   ```bash
   cd /path-to-your-wordpress/wp-content/plugins/
   git clone https://github.com/madehuge/msd-events.git
   cd msd-events
2. Activate the plugin from WordPress Dashboard → Plugins.
3. Go to Settings → MSD Events and add your Google Geocoding API key.

## Usage
- Admin: Configure API key and cache TTL.
- Front-end submission: Use shortcode or include template.
- Events listing: Create a page and assign the `events-listing.php` template or use the block with Shortcode.

## Changelog
See CHANGELOG.md