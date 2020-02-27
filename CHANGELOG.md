# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 2.3.0
* Include: Plugin Loader (1.0.9) with plugin options and settings
* Include: Freemius SDK (2.3.0) and Freemius integration
* Feature: assign new Producer role to a Show for Show displays
* Feature: internal Schedule Show Shift Conflict checking 
* Feature: Show Shift saving completeness and conflict checking
* Feature: added REST/Feed endpoints (broadcast, schedule, shows, genres)
* Feature: options to set Page and default View for Master Schedule
* Feature: post type Archive Shortcodes and Show-related Shortcodes
* Feature: display Radio Timezone on Master Schedule table view
* Feature: added Show Header image to Shows for single Show display
* Feature: add Show Language Taxonomy to Shows (and Overrides)
* Improved: new Data Model and Schedule (with Override) Calculation
* Improved: new Show Content Template layout display method
* Improved: new Playlist Content Template layout display method
* Improved: added multiple Genre highlight selection on Master Schedule
* Improved: added Custom Field and Revision support to post types
* Improved: missing output sanitization throughout the plugin
* Improved: added file hierarchy fallbacks for CSS, JS and Templates
* Improved: enqueue conditional scripts inline instead of echoing
* Improved: Master Schedule displays enhancements and styling
* Improved: add View/Edit links for editing custom post types
* Improved: load Datepicker styles locally instead of via Google
* Improved: add debug function for debug display and logging
* Roles: new Show Producer role (same capabilities as DJ / Host)
* Roles: new Show Editor role (edit permissions but not Admin)
* Roles: Changed DJ role Label to DJ / Host (for talk show usage)
* Admin: Added Plugin Settings Admin Page (via Plugin Loader)
* Admin: Added plugin Upgrade / Updated details admin notices
* Admin: Schedule conflict notice and Show conflicts in Shift column
* Admin: Show/Override content indicator columns to Admin Show list
* Admin: Show Description helper text metabox on Show edit screen
* Admin: Fix to restore Admin Bar New/Edit links for plugin post types
* Admin: Store installed version for future updates and announcements
* Disabled: automatic loading of old templates (non theme agnostic)

## 2.2.8

* Fix to remove strict type checking from in_array (introduced 2.2.6)
* Fix to mismatched flush rewrite rules flag function name
* Fix to undefined index warnings for new Schedule Overrides
* Fix to not 404 author pages for DJs without blog posts
* Fix to implode blog array for Show blog post listing

## 2.2.7

* Dutch translation added (Thank you to André Dortmont for the file!)
* Added Tabbed Display for Master Schedule Shortcode (via Tutorial)
* Add Show list columns with active, shift, DJs and show image displays
* Add Schedule Override list columns with date sorting and filtering
* Add playlist track information labels to Now Playing Widget
* Added meridiem (am/pm) translations via WP Locale class
* Added star rating link to plugin announcement box
* Added update subscription form to plugin Help page
* Fix to checkbox value saving for On Air/Upcoming Widgets
* Fix 12 hour show time display in Upcoming Widget
* Fix PM 12 hour shot time display in On Air Widget
* Fix to schedule override date picker value visibility
* Fix to weekday and month translations to use WP Locale
* Fix to checkbox value saving in Upcoming Widget
* Split Plugin Admin Functions into separate file
* Split Post Type Admin Functions into separate include
* Revert anonymous function use in widget registrations
## 2.2.6

* Reorganize master-list shortcode into templates
* Add constant for plugin directory
* Use WP_Query instead of get_posts
* New posts_per_page and tax_query
* Fixes for undefined indexes
* Fixes for raw mysql queries
* Typecasting to support strict comparisons

## 2.2.5

* WordPress coding standards and best practices (thanks to Mike Garrett @mikengarrett)

## 2.2.4

* added title position and avatar width options to widgets
* added missing DJ author links as new option to widgets
* cleanup, improve and fix enqueued Widget CSS (on air/upcoming)
* improved to show Encore Presentation in show widget displays
* fix to Show shift Encore Presentation checkbox saving

## 2.2.3

* added flush rewrite rules on plugin activation/deactivation
* added show_admin_column and show_in_quick_edit for Genres
* added show metadata and schedule value sanitization
* fix to 00 minute validation for Schedule Override
* convert span tags to div tags in Widgets to fix line breaks

## 2.2.2

* shift main playlist and show metaboxes above editor
* set plugin custom post types editor to Classic Editor
* add high priority to side metaboxes for plugin post types
* added dismissable development changeover admin notice
* added simple Patreon supporter image button and blurb
* added filter for DJ Avatar size on Author page template
* fix to Schedule Override metabox value saving
* fix to Playlist track list items overflowing metabox
* fix to shift up time row on Master Schedule table view
* fix to missing weekday headings in Master Schedule table
* fix to weekday display for Upcoming DJ Widget
* fix to user display labels on select DJ metabox
* fix to file_exists check for DJ on Air stylesheet path
* fix to make DJ multi-select input full metabox width
* fix to expand admin menu when on genre taxonomy page
* fix to expand admin menu when editing plugin post types
* fix to genre submenu item link for current page
* added GitHub URI to plugin header for GitHub updater

## 2.2.1

* Re-commit all missing files via SVN

## 2.2.0

* WordPress coding standards refactoring for WP 5 (thanks to Tony Hayes @majick777)
* fixed the protocol in jQuery UI style Google URL
* reprefixed all functions for consistency (radio_station_)
* updated all the widget constructor methods
* merged the menu items into a single main menu
* updated the capability checks for the menu items
* moved the help and export pages to /templates/
* moved all the css files to /css/
* enqeued the djonair css from within the widget
* use plugins_url for all resource URLs
* added $wpdb->prepare to sanitize a query
* added some sanization for metabox save values
* added a week and month translation helper
* added a radio station antenna icon

## 2.1.3

* Added method for displaying schedule for only a single day (see readme section for the master-schedule shortcode for details).

## 2.1.2

* Compatibility fix for Wordpress 4.3.x - Updated the widgets to use PHP5 constructors instead of the deprecated PHP4 constructors.
* Catalan translation added (Thank you to Victor Riera for the file!)

## 2.1.1 
* Bug fix - Fixed day of the week language translation issue in master schedule shortcode
* Bug fix - Added some error checking in the sidebar widgets
* New Feature - Added ability to give schedule overrides a featured image
* New Feature - Added built-in help page

## 2.1
* General code cleanup, 4.1 compatibility testing, and changes for better efficiency.
* Bug fix - Fixed issue with early morning shows spanning entire column in the programming grid shortcode
* New Feature - Master programming grid can now be displayed in div format, as well as the original table and list formats.

## 2.0.16
* Minor revisions to German translation.
* Fixed a bug that was resetting custom-sert role capabilities for the DJ role.

## 2.0.15
* German translation added (Thank you to Ian Hook for the file!)

## 2.0.14
* Fixed issue on the master schedule where genres containing more than one work wouldn't highlight when clicked
* Added ability to display DJ names on the master schedule.
* Fixed bug in the Upcoming widget.  Override Schedule no longer display as upcoming when they are on-air.
* Verified compatibility woth WordPress 4.0

## 2.0.13
* Added the ability to display show avatars on the program grid.
* Added the ability to display show description in the now on-air widget and short code. 

## 2.0.12
* Fixed a bug in the master schedule shortcode

## 2.0.11
* Russian translation added (Thank you to Alexander Esin for the file!)

## 2.0.10
* Fixed role/capability conflict with WP User Avatar plugin.
* Added the missing leading zero to 24-hour time format on the master schedule.
* Fixed dj_get_current function so that it no longer returns shows that have been moved to the trash.
* Fixed dj_get_next function so that it no longer ignores the "Active" checkbox on a show.
* Added some CSS ids and classes to the master program schedule list format to make it more useful

## 2.0.9 
* Fixed broken upcoming show shortcode.
* Added ability to display DJ names along with the show title in the widgets.

## 2.0.8
* Fixed the display of schedules for upcoming shows in the widget and shortcode.
* Fixed a bug in the dj_get_next function that was causing it to ignore the beginning of the next week at the end of the current week.

## 2.0.7
* Fixed scheduling bug in shortcode function

## 2.0.6
* Master Schedule now displays days starting with the start_of_week option set in the WordPress General Settings panel. 
* Fixed issue with shows that have been unplublished still showing up on the master schedule.
* Fixed missing am/pm text on shows that run overnight on the master schedule.
* Fixed an issue with shows that run overnight not spanning the correct number of hours on the second day on the master schedule.
* Fixed problem in Upcoming DJ Widget that wasn't displaying the correct upcoming shift.

## 2.0.5
* Fixed an issue with some shows displaying in 24 hour time on master schedule grid even though 12-hour time is specified
* Fixed a bug in the On-Air widget that was preventing shows spanning two day from displaying
* Added code to enable theme support for post-thumbnails on the "show" post-type so users don't have to add it to their theme's functions.php file anymore.

## 2.0.4
* Master Schedule bug for shows that start at midnight and end before the hour is up fixed.

## 2.0.3
* Compatibility fix: Fixed a jquery conflict in the backend that was occuring in certain themes

## 2.0.2
* Bug fix: Scheduling issue with overnight shows fixed

## 2.0.1
* Bug fix: Fixed PHP error in Playlist save function that was triggered during preview
* Bug fix: Fixed PHP notice in playlist template file
* Bug fix: Fixed PHP error in dj-widget shortcode

## 2.0.0
* Major code reorganization for better future development
* PHP warning fix
* Enabled option to add comments on Shows and Playlists
* Added option to show either single or multiple schedules in the On Air widget

## 1.6.2
* Minor PHP warning fixes

## 1.6.1
* Bug fix: Some of the code added in the previous update uses the array_replace() function that is only available in PHP 5.3+.  Added a fallback for older PHP versions.

## 1.6.0
* Added the ability to override the weekly schedule to allow one-off events to be scheduled
* Added a list format option to the master schedule shortcode
* Added Italian translation (it_IT) (thank you to Cristofaro Giuseppe!)

## 1.5.4
* Fixed some PHP notices that were being generated when there were no playlist entries in the system.

## 1.5.3
* Added Serbian translation (sr_RS) (thank you to Miodarag Zivkovic!)

## 1.5.2.1
* Removed some debug code from one of the template files

## 1.5.2
* Fixed some localization bugs.
* Added Albanian translation (sq_AL) (thank you to Lorenc!)

## 1.5.1
* Fixed some localization bugs.
* Added French translation (fr_FR) (a big thank you to Dan over at [BuddyPress France](http://bp-fr.net/).

## 1.5.0
* Plugin modified to allow for internationalization.
* Spanish translation (es_ES) added.

## 1.4.6
* Fixed a bug with shows that start at midnight not displaying in the on-air sidebar widget.
* Switched DJ/Show avatars in the widgets to use the featured image of the show instead of gravatar.
* Updated show template to get rid of a PHP warning that appeared if the show had no schedules. 
* Fixed some other areas of the code that were generating PHP notices in WordPress 3.6
* Added CSS classes to master program schedule output so CSS rules can be applied to specific shows
* Added new attribute to the list-shows shortcode to allow only specified genres to be displayed

## 1.4.5
* Fixed master-schedule shortcode bug that was preventing display of 12 hour time

## 1.4.4
* Compatibility fix for Wordpress 3.6 - fixed problem with giving alternative roles DJ capabilities
* Fixed some areas of the code that were generating PHP notices in WordPress 3.6

## 1.4.3
* Master schedule shortcode now displays indiviual shows in both 24 and 12 hour time
* Fixed some areas of the code that were generating PHP notices in WordPress 3.6
* Added example of how to display show schedule to single-show.php template
* Added more options to the plugin's widgets
* Added new options to the master-schedule shortcode

## 1.4.2
* Fixed a bug in the CSS file override from theme directory

## 1.4.1
* Fixed issue with templates copied to the theme directory not overriding the defaults correctly
* Fixed incorrectly implemented wp_enqueue_styles()
* Removed deprecated escape_attribute() function from the plugin widgets
* Fixed some areas of the code that were generating PHP notices

## 1.4.0
* Compatibility fix for WordPress 3.6

## 1.3.9
* Fixed a bug that was preventing sites using a non-default table prefix from seeing the list of DJs on the add/edit show pages

## 1.3.8
* Changes to fix the incorrect list of available shows on the Add Playlist page
* Removing Add Show links from admin menu for DJs, since they don't have permission to use them anyway.

## 1.3.7
* Fixed a scheduling bug in the upcoming shows widget
* By popular request, switched the order of artist and song in the now playing widget

## 1.3.6
* Fixed issue with shows that run overnight not showing up correctly in the sidebar widgets

## 1.3.5
* Fixed a time display bug in the DJ On-Air sidebar widget
* Fixed a display bug on the master schedule with overnight shows

## 1.3.4
* By request, added as 24-hour time format option to the master schedule and sidebar widgets.

## 1.3.3
* Added the ability to assign any user with the edit_shows capability as a DJ, to accomodate custom and edited roles.

## 1.3.2
* Fixed a bug in the DJ-on-air widget

## 1.3.1
* Fixed a major bug in the master schedule output

## 1.3
* Fixed some minor compatibility issues with WordPress 3.5
* Fixed Shows icon in Dashboard

## 1.2
* Fixed thumbnail bug in sidebar widgets
* Added new widget to display upcoming shows
* Added pagination options for playlists and show blogs

## 1.1
* Fixed playlist edit screen so that queued songs fall to the bottom of the list to maintain play order
* Reduced the size of the content field in the playlist post type
* Some minor formatting changes to default templates
* Added genre highlighter to the master programming schedule page
* Added a second Update button on the bottom of the playlist edit page for convinience.
* Added sample template for DJ user pages
* Fixed a bug in the master schedule shortcode that messed up the table for shows that are more than two hours in duration
* Fixed a bug in the master schedule shortcode to accomodate shows that run from late night into the following morning.
* Added new field to associate blog posts with shows

## 1.0
* Initial release