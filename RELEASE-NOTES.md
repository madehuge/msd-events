# MSD Events - Release Notes

## 1.0.0 - 2025-08-12

### Initial Release

* Added main plugin file (`msd-events.php`)
* Registered **custom post type** `msd_event` with **taxonomy** `event_category`
* Created plugin folders: `includes`, `templates`, `assets`, `languages`
* Added core classes for:

  * CPT registration
  * Admin settings (Google Maps API)
  * Front-end form handling
  * Geocoding & caching
  * Front-end display & templates
  * Gutenberg block registration
  * Helper functions
* Added initial templates for event listing and submission
* Added placeholder CSS/JS in `assets`
* Added POT file in `languages` for translations