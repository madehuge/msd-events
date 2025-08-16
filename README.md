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

## Usage
- Admin: Configure API key and cache TTL.
- Front-end submission: Use shortcode or include template.
- Events listing: Create a page and assign the `events-list.php` template or use the block.

## Changelog
See CHANGELOG.md