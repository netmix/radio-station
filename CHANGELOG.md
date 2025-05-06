# Radio Station Changelog

***

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

= 2.5.12 =
* Updated: Block translation and element functions
* Fixed: preload player settings when Block editing

= 2.5.11 =
* Added: AJAX load of Schedule views for cachebusting
* Fixed: instance validation for schedule loader shortcode
* Fixed: mismatched function name in popup player

= 2.5.10 =
* Updated: Freemius SDK (2.11.0)
* Updated: reader.php with prefixed reader functions
* Updated: Plugin Panel (1.3.5) with new reader function
* Updated: Color Picker Alpha Library (3.0.4)
* Updated: Howler Library (2.2.4)
* Improved: use wp_kses on player widget output
* Improved: player volume slider background sync/hover
* Fixed: Freemius optin image URL path

= 2.5.9 =
* Fixed: Missing use of prepare method on some database queries
* Added: Vulnerability disclosure program link to FAQ
* Added: Edit Host/Producer link to admin Show List column

= 2.5.8 =
* Fixed: Template display for themes showing excerpts on singular pages
* Fixed: Possible next show duplicates current show in Data API
* Fixed: Save multiple Show related post values
* Fixed: Count bug on Override Archive shortcode
* Fixed: Date/time display on Override Archive shortcode
* Fixed: Show file Disable Download checkbox saving
* Added: Use automatic Embeds on external Show file URLs
* Added: Nonce check to notice dismissal AJAX
* Changed: Removed player state saving iframe

= 2.5.7 =
* Updated: Freemius SDK (2.6.0)
* Disabled: Howler Player Script (browser compatibility issues)
* Improved: Schedule Engine Weekdate calculations
* Fixed: AJAX shortcode/widget loading for current time
* Fixed: Widget title display duplication

= 2.5.6 =
* Updated: Freemius SDK (2.5.11)
* Updated: Plugin Panel (1.3.0)
* Added: Filter for query and meta for show post list shortcode
* Updated: Language translations file (.pot)
* Updated: Bundled Dutch translation
* Fixed: check linked override shifts before displaying
* Fixed: hide empty widgets to work in AJAX loading mode
* Fixed: remove direct usages of date function
* Fixed: display of show posts on show page (query)
* Fixed: Data API next_show data field
* Fixed: minor schedule engine issues
* Improved: more consistent sanitization and escaping

= 2.5.5 =
* Updated: Freemius SDK (2.5.10)
* Added: RSS Posts Feed: Related Show node
* Added: RSS Show Feed: Host/Producer node
* Fixed: Prefix Block element JS constant to prevent conflict (EventOn)
* Fixed: RSS Posts Feed: filter by Show conflict

= 2.5.4 =
* Updated: Freemius SDK (2.5.9)
* Fixed: Missing player back-compat arguments

= 2.5.3 =
* Fixed: Bug in Admin Override Timeslot List

= 2.5.2 =
* Fixed: Bug retrieving show data for linked overrides

= 2.5.1 =
* Fixed: Widget Countdown Timer Display Bug
* Fixed: Pro Player Backwards Compatibility

= 2.5.0 =
* Added: Radio Station Blocks! (converted Widgets)
* Updated: Freemius SDK (2.5.7)
* Updated: Plugin Panel (1.2.9)
* Updated: AmplitudeJS (5.3.2)
* Updated: Howler (2.2.3)
* Updated: Moment JS (2.29.4) with WP Loading
* Improved: Refactored Schedule Engine Class
* Improved: Redesigned higher resolution player buttons
* Improved: Standardized Widget Input Fields
* Improved: WordPress Coding Standards
* Improved: Sanitization using KSES
* Improved: Translation Implementation
* Improved: use WP JSON functions for data endpoints
* Improved: Schedule Templates to use Classes and Instances
* Improved: Tab Schedule default date display on
* Improved: use wp_send_json for feed endpoints
* Added: Freemius Pricing Page v2
* Added: assign Playlist to a specific Show Shift
* Added: Quick Edit of Playlist to assign to Show
* Added: Volume Control options to Player widget
* Fixed: Countdowns with multiple widget instances
* Fixed: Radio Player iOS no volume control detection
* Fixed: Mobile detection (via any pointer type)
* Fixed: Genre/Language Archive Pagination
* Fixed: Adjacent Post Links (where show has one shift)
* Fixed: Workaround Amplitude pause event not firing
* Fixed: inline scripts when main script in head tag
* Security Fix: Escape all debug output content

### 2.4.0.9
* Update: Sysend (1.11.1) for Radio Player
* Fixed: missing register REST routes permission_callback argument
* Fixed: added property_exists checks for PHP8 TypeErrors

### 2.4.0.8
* Update: Plugin Panel (1.2.2)
* Added: filter plugin icon for Freemius activation screen
* Updated: clear plugin updates transient on activation/deactivation
* Fixed: filter plugin updates to prevent Pro ever overwriting Free
* Changed: plugin options array moved to a separate file

### 2.4.0.7
* Fix: remove debug output breaking redirects/data endpoints
* Updated: main language translation file
* Added: list of Pro filters to documentation

### 2.4.0.6
* Update: Freemius SDK (2.4.3)
* Updated: documentation links to new demo site address
* Fixed: remove duplicate Related Show box in Post Quick Edit
* Fixed: multiple attributes for automatic pages shortcodes
* Fixed: hide inactive tab shortcode section on tab click
* Fixed: undefined warning for debugshifts
* Fixed: current show in schedule when on exact start second
* Added: filters for time and date separators
* Added: description/excerpt to single show data endpoint

### 2.4.0.5
* Fixed: plugin conflicts causing fatal errors

### 2.4.0.4
* Improved: clear cache on show/override status transitions
* Fixed: DJ / Host can edit own/others Show permissions
* Fixed: Override link to show dropdown query
* Fixed: Fallback scripts and fallback stream URLs
* Fixed: Radio Clock responsive width display
* Fixed: Collapse descriptions for non-show pages
* Fixed: Deduplicate dates in week (daylight saving fix)

### 2.4.0.3
* Update: Plugin Panel (1.2.1) with zero value save and tab fixes
* Added: option to disable player audio fallback scripts
* Added: option to hide various volume controls
* Improved: lazy load player audio fallback scripts
* Improved: added author support to post types for quick edit
* Refix: missing fix to active day tab on pageload
* Fixed: player volume slider background position (cross-browser)
* Fixed: missing title value for adjacent post links
* Fixed: Fallback scripts and fallback stream URLs

### 2.4.0.2
* Fixed: Multiple Player instance IDs
* Fixed: Player loading button glow animation
* Added: Enabled Pro Pricing plans page
* Added: Widget type specific classes
* Added: Alternative text positions in Player
* Added: Pause button graphics to Player

### 2.4.0.1
* Fixed: Rounded player play button background corner style
* Fixed: Tabbed schedule active day tab on pageload
* Improved: Radio Clock Widget layout

### 2.4.0
* Added: Radio Stream Player!
* Fixed: Shows archive shortcode with no Shows selected

### 2.3.3.9
* Update: Plugin Panel (1.1.8) with Number Step Min/Max fix
* Update: Freemius SDK (2.4.2)
* Improved: Allow for Multiple Override Times (with AJAX Saving)
* Improved: Markdown Extra Compatibility for PHP 7.4+ 
* Added: Link Override to Show Data with selectable Show Fields
* Added: Language Archive Shortcode (similar to Genre Archive)
* Added: Display Linked Override Date List on Show Pages
* Added: Automatic user showtime conversion and display
* Fixed: Show Schedule sometimes starting on previous week
* Fixed: Current Show highlighting timer interval cycling
* Fixed: Before and After Show classes when no current Show
* Fixed: Shows Data Endpoint 24 Hour Shift Format and Encore Switch
* Fixed: Multiple host separator display in Current Show Widget
* Fixed: Playlist Widget playlist ended label when no next playlist
* Fixed: Conflicting duplicate filter name for Show Avatar
* Fixed: Time conversions where start/finish Show/Override is equal
* Fixed: Show page subarchive lists pagination button arrow display
* Fixed: Show Shifts with same start time overwriting bug

### 2.3.3.8
* Update: Plugin Panel (1.1.7) with Image and Color Picker fields
& Documentation: Full Plugin Filter List added to docs/Filters.md
* Added: Stream Format and Fallback/Format selection setting
* Added: Station Image and Station Title for future Player Display
* Added: Station Email Address setting with default display option
* Added: Section order filtering for Master Schedule Views
* Added: Section display filtering for Master Schedule Views
* Added: Section display filtering for Widget sections
* Added: Show image alignment attribute to Schedule Tabs View
* Added: Show Description/Excerpt to Show Data Endpoint (via querystring)
* Added: Reduced opacity for past Shows on Schedule Tab/Table Views
* Added: Screen Reader text for Show icons on Show Page
* Fixed: Display Widget Countdown when no Current Show/Playlist
* Fixed: Check for explicit singular.php template usage setting
* Fixed: Access to Shows Data via querystring of Show ID/name
* Fixed: Shows Data for Genres/Languages querystring of ID/name
* Fixed: Override Display order output for Tab/List Views

### 2.3.3.7
* Fixed: Schedule Overrides overlapping multiple Show shifts
* Fixed: Bulk Edit field repetition and possible jQuery conflict
* Fixed: Related Posts check producing error output
* Fixed: WordPress Readme Parser deprecated errors for PHP7

### 2.3.3.6
* Update: Freemius SDK (2.4.1)
* Update: Plugin Loader (1.1.6) with phone number and CSV validation
* Added: Station phone number setting with default display option
* Added: Schedule classes for Shows before and after current Show
* Improved: current Show highlighting on Schedule for overnight shifts
* Improved: info section reordering filters on single Show template
* Fixed: Edit permissions checks for Related to Show post assignments
* Fixed: Main Language option value for WordPress Setting
* Fixed: make Date on Tab clickable on Tabbed Schedule View
* Fixed: prevent possible conflicts with changes not saved reload message
* Fixed: do not conflict check Shift against itself for last shift check
* Fixed: link back to Show posts for related Show posts (allow multiple)
* Fixed: filter next/previous post link for (multiple) related Show posts
* Fixed: automatic pages conflict where themes filter the_content early

### 2.3.3.5
* Fixed: use schedule based on start_day if specified for Schedule view
* Fixed: day left/right shifting on Schedule table/tab mobile views
* Added: past/today/future filter for Schedule Override List 
* Added: filter for Schedule display start day (and to accept today)
* Added: current playlist (if any) to Broadcast Data endpoint

### 2.3.3.4
* Improved: auto-match show description to info height on Show pages
* Improved: allow multiple Related Show selection for single post
* Improved: ability to assign Post to relate to multiple Shows
* Added: Related Show Post List column and Quick Edit field
* Added: Related Show selection Bulk Edit Action for Post List
* Added: filters for label texts and title attributes on Show Page
* Added: filter for label text above Show Player (default empty)

### 2.3.3.3
* Fixed: improved Current Show and Upcoming Shows calculations
* (Display showtimes when show starts before and ends after midnight)

### 2.3.3.2
* Update: Freemius SDK (2.4.0)
* Update: Plugin Loader (1.1.4) with weird isset glitch fix
* Fixed: Current Show for Shows ending at midnight
* Fixed: incorrect AJAX Widget plugin setting value
* Fixed: use pageload data for schedules before transients

### 2.3.3
* Update: Plugin Loader (1.1.3) with non-strict select match fix
* Improved: width responsiveness for table/tabbed Schedule views
* Improved: show shifts interface background colors
* Added: navigate away from page on shift change check
* Added: default time format option to Widgets
* Removed: current show transients (intermittant unreliability)
* Fixed: AJAX call causing plugin conflicts via save_post action
* Fixed: calculation of Upcoming Shows near end of the week
* Fixed: remove and duplicate actions on new shifts

### 2.3.2
* Update: Plugin Loader (1.1.2) with settings link fix
* Improved: use plugin timezone setting for all times
* Improved: show shift conflict checker logic
* Added: Radio Clock Widget for user/server time display
* Added: AJAX widget load option (to bypass page caches)
* Added: automated show schedule highlighting (table/tabs/list)
* Added: playlist track arrows for re-ordering tracks
* Added: AJAX save of show shifts and playlist tracks
* Added: post type editing metabox position filtering
* Added: more display attributes to Master Schedule shortcode
* Added: time format filters for time output displays
* Added: javascript user timezone display on Master Schedule
* Fixed: handling of UTC only timezone settings
* Fixed: added check for empty role capabilities
* Fixed: added settings submenu redirection fix
* Fixed: show and override midnight end conflict
* Fixed: calculate next shows at end of schedule week
* Fixed: metaboxes disappearing on position sorting
* Fixed: move tracks marked New to end of Playlist on update
* Fixed: override shift array output showing above schedule
* Fixed: master schedule specify days attribute bug
* Fixed: display real end time of overnight split shifts
* Fixed: master schedule display with days attribute
* Fixed: logic for Affected Shifts in override list
* Fixed: removed auto-tab selection change on tab view resize
* Fixed: Current Show widget schedule/countdown for Overrides
* Fixed: multiple overrides in schedule range variable conflict

### 2.3.1
* Update: Plugin Loader (1.1.1) with Freemius first path fix
* Fixed: conditions for Schedule Override time calculations
* Fixed: schedule table view - 12 hour format with translations
* Fixed: schedule table view hour column width style
* Fixed: javascript table/tab arrows to prevent default click
* Fixed: undefined index warning when saving show with no shifts
* Fixed: append not echo override date to shortcode archive list
* Fixed: compatibility with multiple the_content calls (Yoast)
* Fixed: reset to showcontinued flag in Schedule (table view)
* Added: option to clear transients on every pageload
* Added: show avatar and featured image URLs to Data API output
* Added: option to ping Netmix directory on show updates
* Added: filters for widget section display order

### 2.3.0
* Include: Plugin Loader (1.1.0) with plugin options and settings
* Include: Freemius SDK (2.3.0) and Freemius integration
* Feature: assign new Producer role to a Show for Show displays
* Feature: internal Schedule Show Shift Conflict checking 
* Feature: Show Shift saving completeness and conflict checking
* Feature: added Data Endpoints API via WordPress REST and Feeds
* Feature: options to set Page and default View for Master Schedule
* Feature: post type Archive Shortcodes and Show-related Shortcodes
* Feature: display Radio Timezone on Master Schedule table view
* Feature: added Show Header image to Shows for single Show display
* Feature: added Show Language Taxonomy to Shows (and Overrides)
* Feature: added Countdown clock for Show and Playlists Widgets
* Improved: new Data Model and Schedule (with Override) Calculation
* Improved: new Show Content Template layout display method
* Improved: new Playlist Content Template layout display method
* Improved: added multiple Genre highlight selection on Master Schedule
* Improved: added Custom Field and Revision support to post types
* Improved: missing output sanitization throughout the plugin
* Improved: added file hierarchy fallbacks for CSS, JS and Templates
* Improved: enqueue conditional scripts inline instead of echoing
* Improved: Master Schedule displays enhancements and styling
* Improved: add Responsiveness to Master Schedule Table and Tab View
* Improved: add View/Edit links for editing custom post types
* Improved: load Datepicker styles locally instead of via Google
* Improved: add debug function for debug display and logging
* Improved: add links from Show Posts back to Show Page
* Improved: added Duplicate Shift button to Show Shift Editing
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

### 2.2.8
* Fix to remove strict type checking from in_array (introduced 2.2.6)
* Fix to mismatched flush rewrite rules flag function name
* Fix to undefined index warnings for new Schedule Overrides
* Fix to not 404 author pages for DJs without blog posts
* Fix to implode blog array for Show blog post listing

### 2.2.7
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

### 2.2.6
* Reorganize master-list shortcode into templates
* Add constant for plugin directory
* Use WP_Query instead of get_posts
* New posts_per_page and tax_query
* Fixes for undefined indexes
* Fixes for raw mysql queries
* Typecasting to support strict comparisons

### 2.2.5
* WordPress coding standards and best practices (thanks to Mike Garrett @mikengarrett)

### 2.2.4
* added title position and avatar width options to widgets
* added missing DJ author links as new option to widgets
* cleanup, improve and fix enqueued Widget CSS (on air/upcoming)
* improved to show Encore Presentation in show widget displays
* fix to Show shift Encore Presentation checkbox saving

### 2.2.3
* added flush rewrite rules on plugin activation/deactivation
* added show_admin_column and show_in_quick_edit for Genres
* added show metadata and schedule value sanitization
* fix to 00 minute validation for Schedule Override
* convert span tags to div tags in Widgets to fix line breaks

### 2.2.2
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

### 2.2.1
* Re-commit all missing files via SVN

### 2.2.0
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

### 2.1.3
* Added method for displaying schedule for only a single day (see readme section for the master-schedule shortcode for details).

### 2.1.2
* Compatibility fix for Wordpress 4.3.x - Updated the widgets to use PHP5 constructors instead of the deprecated PHP4 constructors.
* Catalan translation added (Thank you to Victor Riera for the file!)

### 2.1.1 
* Bug fix - Fixed day of the week language translation issue in master schedule shortcode
* Bug fix - Added some error checking in the sidebar widgets
* New Feature - Added ability to give schedule overrides a featured image
* New Feature - Added built-in help page

### 2.1
* General code cleanup, 4.1 compatibility testing, and changes for better efficiency.
* Bug fix - Fixed issue with early morning shows spanning entire column in the programming grid shortcode
* New Feature - Master programming grid can now be displayed in div format, as well as the original table and list formats.

### 2.0.16
* Minor revisions to German translation.
* Fixed a bug that was resetting custom-sert role capabilities for the DJ role.

### 2.0.15
* German translation added (Thank you to Ian Hook for the file!)

### 2.0.14
* Fixed issue on the master schedule where genres containing more than one work wouldn't highlight when clicked
* Added ability to display DJ names on the master schedule.
* Fixed bug in the Upcoming widget.  Override Schedule no longer display as upcoming when they are on-air.
* Verified compatibility woth WordPress 4.0

### 2.0.13
* Added the ability to display show avatars on the program grid.
* Added the ability to display show description in the now on-air widget and short code. 

### 2.0.12
* Fixed a bug in the master schedule shortcode

### 2.0.11
* Russian translation added (Thank you to Alexander Esin for the file!)

### 2.0.10
* Fixed role/capability conflict with WP User Avatar plugin.
* Added the missing leading zero to 24-hour time format on the master schedule.
* Fixed dj_get_current function so that it no longer returns shows that have been moved to the trash.
* Fixed dj_get_next function so that it no longer ignores the "Active" checkbox on a show.
* Added some CSS ids and classes to the master program schedule list format to make it more useful

### 2.0.9 
* Fixed broken upcoming show shortcode.
* Added ability to display DJ names along with the show title in the widgets.

### 2.0.8
* Fixed the display of schedules for upcoming shows in the widget and shortcode.
* Fixed a bug in the dj_get_next function that was causing it to ignore the beginning of the next week at the end of the current week.

### 2.0.7
* Fixed scheduling bug in shortcode function

### 2.0.6
* Master Schedule now displays days starting with the start_of_week option set in the WordPress General Settings panel. 
* Fixed issue with shows that have been unplublished still showing up on the master schedule.
* Fixed missing am/pm text on shows that run overnight on the master schedule.
* Fixed an issue with shows that run overnight not spanning the correct number of hours on the second day on the master schedule.
* Fixed problem in Upcoming DJ Widget that wasn't displaying the correct upcoming shift.

### 2.0.5
* Fixed an issue with some shows displaying in 24 hour time on master schedule grid even though 12-hour time is specified
* Fixed a bug in the On-Air widget that was preventing shows spanning two day from displaying
* Added code to enable theme support for post-thumbnails on the "show" post-type so users don't have to add it to their theme's functions.php file anymore.

### 2.0.4
* Master Schedule bug for shows that start at midnight and end before the hour is up fixed.

### 2.0.3
* Compatibility fix: Fixed a jquery conflict in the backend that was occuring in certain themes

### 2.0.2
* Bug fix: Scheduling issue with overnight shows fixed

### 2.0.1
* Bug fix: Fixed PHP error in Playlist save function that was triggered during preview
* Bug fix: Fixed PHP notice in playlist template file
* Bug fix: Fixed PHP error in dj-widget shortcode

### 2.0.0
* Major code reorganization for better future development
* PHP warning fix
* Enabled option to add comments on Shows and Playlists
* Added option to show either single or multiple schedules in the On Air widget

### 1.6.2
* Minor PHP warning fixes

### 1.6.1
* Bug fix: Some of the code added in the previous update uses the array_replace() function that is only available in PHP 5.3+.  Added a fallback for older PHP versions.

### 1.6.0
* Added the ability to override the weekly schedule to allow one-off events to be scheduled
* Added a list format option to the master schedule shortcode
* Added Italian translation (it_IT) (thank you to Cristofaro Giuseppe!)

### 1.5.4
* Fixed some PHP notices that were being generated when there were no playlist entries in the system.

### 1.5.3
* Added Serbian translation (sr_RS) (thank you to Miodarag Zivkovic!)

### 1.5.2.1
* Removed some debug code from one of the template files

### 1.5.2
* Fixed some localization bugs.
* Added Albanian translation (sq_AL) (thank you to Lorenc!)

### 1.5.1
* Fixed some localization bugs.
* Added French translation (fr_FR) (a big thank you to Dan over at [BuddyPress France](http://bp-fr.net/).

### 1.5.0
* Plugin modified to allow for internationalization.
* Spanish translation (es_ES) added.

### 1.4.6
* Fixed a bug with shows that start at midnight not displaying in the on-air sidebar widget.
* Switched DJ/Show avatars in the widgets to use the featured image of the show instead of gravatar.
* Updated show template to get rid of a PHP warning that appeared if the show had no schedules. 
* Fixed some other areas of the code that were generating PHP notices in WordPress 3.6
* Added CSS classes to master program schedule output so CSS rules can be applied to specific shows
* Added new attribute to the list-shows shortcode to allow only specified genres to be displayed

### 1.4.5
* Fixed master-schedule shortcode bug that was preventing display of 12 hour time

### 1.4.4
* Compatibility fix for Wordpress 3.6 - fixed problem with giving alternative roles DJ capabilities
* Fixed some areas of the code that were generating PHP notices in WordPress 3.6

### 1.4.3
* Master schedule shortcode now displays indiviual shows in both 24 and 12 hour time
* Fixed some areas of the code that were generating PHP notices in WordPress 3.6
* Added example of how to display show schedule to single-show.php template
* Added more options to the plugin's widgets
* Added new options to the master-schedule shortcode

### 1.4.2
* Fixed a bug in the CSS file override from theme directory

### 1.4.1
* Fixed issue with templates copied to the theme directory not overriding the defaults correctly
* Fixed incorrectly implemented wp_enqueue_styles()
* Removed deprecated escape_attribute() function from the plugin widgets
* Fixed some areas of the code that were generating PHP notices

### 1.4.0
* Compatibility fix for WordPress 3.6

### 1.3.9
* Fixed a bug that was preventing sites using a non-default table prefix from seeing the list of DJs on the add/edit show pages

### 1.3.8
* Changes to fix the incorrect list of available shows on the Add Playlist page
* Removing Add Show links from admin menu for DJs, since they don't have permission to use them anyway.

### 1.3.7
* Fixed a scheduling bug in the upcoming shows widget
* By popular request, switched the order of artist and song in the now playing widget

### 1.3.6
* Fixed issue with shows that run overnight not showing up correctly in the sidebar widgets

### 1.3.5
* Fixed a time display bug in the DJ On-Air sidebar widget
* Fixed a display bug on the master schedule with overnight shows

### 1.3.4
* By request, added as 24-hour time format option to the master schedule and sidebar widgets.

### 1.3.3
* Added the ability to assign any user with the edit_shows capability as a DJ, to accomodate custom and edited roles.

### 1.3.2
* Fixed a bug in the DJ-on-air widget

### 1.3.1
* Fixed a major bug in the master schedule output

### 1.3
* Fixed some minor compatibility issues with WordPress 3.5
* Fixed Shows icon in Dashboard

### 1.2
* Fixed thumbnail bug in sidebar widgets
* Added new widget to display upcoming shows
* Added pagination options for playlists and show blogs

### 1.1
* Fixed playlist edit screen so that queued songs fall to the bottom of the list to maintain play order
* Reduced the size of the content field in the playlist post type
* Some minor formatting changes to default templates
* Added genre highlighter to the master programming schedule page
* Added a second Update button on the bottom of the playlist edit page for convinience.
* Added sample template for DJ user pages
* Fixed a bug in the master schedule shortcode that messed up the table for shows that are more than two hours in duration
* Fixed a bug in the master schedule shortcode to accomodate shows that run from late night into the following morning.
* Added new field to associate blog posts with shows

### 1.0
* Initial release