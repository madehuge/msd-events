# MSD Events - Release Notes

## 1.0.0 - 2025-08-12
### Initial Release
- Added plugin bootstrap file (`msd-events.php`)
- Registered custom post type `msd_event` with custom taxonomy `event_category`
- Created plugin folder structure with `includes`, `templates`, `assets`, and `languages`
- Added placeholder classes for:
  - CPT registration (`class-msd-events-cpt.php`)
  - Admin settings for Google Maps API key (`class-msd-events-settings.php`)
  - Front-end form rendering & handling (`class-msd-events-form.php`)
  - Geocoding API integration & caching (`class-msd-events-geocode.php`)
  - Front-end display & template override (`class-msd-events-display.php`)
  - Gutenberg block registration (`class-msd-events-block.php`)
  - Helper functions (`helpers.php`)
- Added initial templates for event listing & submission form
- Added assets folder with placeholder CSS/JS files
- Added `languages` folder with POT file for translation