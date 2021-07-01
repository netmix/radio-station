=== Radio Station ===
Contributors: tonyzeoli, majick
Donate link: https://netmix.co/donate
Tags: dj, music, playlist, radio, shows, scheduling, broadcasting
Requires at least: 3.3.1
Tested up to: 5.6.2
Stable tag: 2.3.3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Radio Station let's you build and manage a Show Schedule for a radio station or Internet broadcaster's WordPress website. 

== Description ==

Radio Station by NetMix is a plugin to build and manage a Show Schedule for a radio station or internet broadcaster's WordPress website. If you're a podcaster with scheduled releases or a Clubhouse app moderator who schedule rooms, you could use this plugin to announce your schedule on your website. It's functionality was originally based on Drupal 6's Station plugin, reworked for use in Wordpress and then extended.

The plugin adds a new "Show" post type, schedulable blocks of time that contain a Show description, a Show shifts repeater field, assignable images and other meta information. You can also create Playlists associated with those shows, or assign standard blog posts to relate to a Show. It also supports adding Schedule Overrides for specific dates and times, and adds the ability to associate users (given a role of "Host" or "Producer") to Shows, so they can be displayed for that Show and to give them edit access.

A schedule of all Shows can be generated and added to a page with a shortcode (or simple page selection in the Plugin Settings) which has a number of Layout and display options. Shows can be categorized into Genres and a Genre highlighting filter appears on the embedded Schedule view. Each Show has it's own dedicated page to display all the Show details in a responsive layout.

The plugin contains a widget to display the on-air Current Show linked to the Show page, with various widget display options, and further widgets for displaying Upcoming Shows and current Playlist tracks. Shortcodes are available for these widgets, as well as for displaying archive lists of any of the plugin's custom post types.

As there is a lot you can do with Radio Station, we've made an effort to provide complete [Radio Station Plugin Documentation](https://netmix.com/radio-station/docs/). You can also find a Quickstart Guide there, as well as in the section below. You can see some example displays from the plugin via the Screenshots section, and full live examples are available on the [Radio Station Plugin Demo Site](https://radiostationdemo.com).

We are actively seeking Radio Station partners and supporters to fund further development of the free, open source version of this plugin via [Patreon](https://www.patreon.com/radiostation) and are also in the process of developing more exciting features and functionality for a future [Radio Station Pro](https://netmix.com/radio-station-pro/) upgrade. Enter your [email address on the Radio Station Pro website] (https://radiostation.pro) to add yourself to the cue to be able to download the be notified when PRO is released.

= Updating from Prior to 2.3.0 =

Since 2.3.0, the first major feature update since plugin takeover in July 2019, Radio Station has incorporated a whole bunch of enhancements (see the changelog for a full list)... but here is a shortlist of the main new features:

* an Updated Show Page Layout (based on Content Filters not Templates)
* Responsive Schedule Views (with integrated Override support)
* Revamped Schedule calculations (with Show Shift Conflict Checking)
* Producer and Show Editor Roles (for improved Show management)
* Language Taxonomy Assignments (for Shows and Overrides)
* Admin Plugin Settings Page (with a plethora of new options)
* ...and a Radio Station Data API via the WordPress REST API! 

If you have been using Radio Station prior to version 2.3.0 and want to update, it is recommended that you read [the blog post for the 2.3.0 release](https://netmix.com/2-3-0-release-announcement/). As there is quite a lot of refactoring and changes in this version, you will want to check the details of the new changes with your current usage - especially if you have been using any custom page templates in your theme or other plugin-related custom code on your site. As these are probably the most significant changes that will ever be made to the plugin in a release, we have worked hard to maintain backwards compatibility and test the new features thoroughly, but it's important you know what is going and test things out yourself in the update process.

= Support and Contribution =

We are grateful to Nikki Blight for her contribution to creating and developing this plugin for as long as she could maintain the codebase. As of June 22, 2019, Radio Station is managed by [Tony Zeoli](https://profiles.wordpress.org/tonyzeoli/) with [Tony Hayes](https://profiles.wordpress.org/majick/) as lead developer and other contributing committers to the project.

For free version plugin support, you can ask in the [Wordpress Plugin Support Forum](https://wordpress.org/support/plugin/radio-station/). Please give 24-48 hours to answer support questions. Alternatively (and preferably) you can submit bugs, enhancement and feature requests directly to [Github Repository Issues](https://github.com/netmix/radio-station/issues/).

If you are a WordPress developer wanting to contribute to Radio Station, please join the team and follow plugin development on [Github](https://github.com/netmix/radio-station) and submit Issues and Pull Requests there. You can see the current progress via the Projects tab. Or if you would prefer to get involved even more substantially, please [Contact Us via Email](mailto:info@netmix.com) and let us know what you would like to do.

= Quickstart Guide =

Once you have installed and activated the Radio Station Plugin on your WordPress site, your WordPress Admin area will now have a new menu item titled Radio Station with submenu page items. If you are trying to do something specific, you can check out the [FAQ](https://netmix.com/radio-station/docs/FAQ.md) for Frequently Asked Questions as you may find the answer there.

Firstly, you can visit the Plugin Settings screen to adjust the default [Options](https://netmix.com/radio-station/docs/Options.md) to your liking. Here you can set your Radio Timezone and Streaming URL (if you have one) along with other global plugin settings. Also from this Settings page you may want to assign [Pages](https://netmix.com/radio-station/docs/Display.md#automatic-pages) and Views for your Program Schedule display and other optional Post Type Archive displays.

Add a New Show and assign it a Shift timeslot and Publish. Then check out how it displays on a single Show page by clicking the Show Permalink. Schedule Overrides work in a similar way but are for specific date and time blocks only. Depending on your Theme, you may wish to adjust the [Templates](https://netmix.com/radio-station/docs/Display.md#page-templates) used. You can also assign different [Images](https://netmix.com/radio-station/docs/Display.md#images) to Shows (and Schedule Overrides.) Then have a look at your Program Schedule page to see the Show displayed there also. Just keep adding Shows until you have your Schedule filled in! You can further [Manage](https://netmix.com/radio-station/docs/Manage.md) your Shows and other Station data via the WordPress Admin area.

Next you may want to give some users on your site some plugin [Roles](https://netmix.com/radio-station/docs/Roles.md). (Note that while the default interface in WordPress allows you to assign a single role to a user, it also supports multiple roles, but you need to add a plugin to get an interface for this.) Giving a Role of Host/DJ or Producer to a user will allow them to be assigned to a Show on the Show Edit Page and thus edit that particular Show also. You can also assign the Show Editor role if you have someone needs to edit all plugin records without being a site Administrator.

There are a few [Widgets](https://netmix.com/radio-station/docs/Widgets.md) you can add via your Appearance -> Widgets menu. The main one will display the currently playing Show, and another will display Upcoming Shows. There is also a Current Playlist Widget for if you have created and assigned a Playlist to a Show.

Then there are also a number of other [Shortcodes](https://netmix.com/radio-station/docs/Shortcodes.md) you can use in your pages with different display options you can use in various places on your site also. There is the Master Schedule, Widget Shortcodes, and also Archive Shortcodes for each of the different data records. 

Radio Station has several in-built [Data](https://netmix.com/radio-station/docs/Data.md) types. These include [Custom Post Types](https://netmix.com/radio-station/docs/Data.md#custom-post-types) for Shows, Schedule Overrides and Playlists. There are [Taxonomies](https://netmix.com/radio-station/docs/Data.md#taxonomies) for Genres and Languages. You can override most data values and display output via custom [Data Filters](#data-filters) throughout the plugin. We have also incorporated an [API](https://netmix.com/radio-station/docs/API.md) in the plugin via REST and/or WordPress Feeds, and this data is accessible in JSON format. 

This plugin is under active development and we are continuously working to enhance the Free version available on [WordPress.Org](https://wordpress.org/plugins/radio-station/), as well as creating new feature additions for **Radio Station Pro**. Check out the [Roadmap](https://netmix.com/radio-station/docs/Roadmap.md) if you are interested in seeing what is coming up next!

= Upgrading to Radio Station Pro =

Love Radio Station and ready for more? As the free version develops, we have also been working hard to introduce new features to create a Professional version that will "level up" the plugin to make your Station's site even more useable and accessible for your listeners! [Click here to learn more about Radio Station Pro](https://netmix.com/radio-station-pro/). Add your email address at [RadioStation.pro](https://radiostation.pro) to get in the cue so you can receive an invite to download the release when it's ready.


== Installation ==

1. Upload plugin .zip file to the `/wp-content/plugins/` directory and unzip.
2. Activate the plugin through the 'Plugins' menu in the WordPress Admin
3. Alternatively search for Radio Station via the WordPress admin Add New plugin interface and install and activate it there.
4. Give any users who need access to the plugin the role of "Host", "Producer" or "Show Editor". Assigning These roles gives publish and edit access to the plugin's records.
5. Create Shows, add Shifts to them, and assign Images, Genres, Languages, Hosts and/or Producers.
6. Add Playlists to your Shows or assign posts to Shows as needed.
7. Go to your admin Appearance -> Widgets page to add and configure Current and Upcoming Show Widgets, and any other desired plugin widgets.
8. See the QuickStart Guide above for more detailed instructions of what else is available.

== Frequently Asked Questions ==

= How do I get started with Radio Station? =

Read the [Quickstart Guide](https://netmix.com/radio-station/docs/#quickstart-guide) for an introduction to the plugin, what features are available and how to set them up.

= Where can I find the full plugin documentation? =

The latest documentation can be found online at [NetMix.com](https://netmix.com/radio-station/docs/). Documentation is also included with for the currently installed version via the Radio Station Help menu. You can find the Markdown-formatted files in the `/docs` folder of the [GitHub Repository](https://github.com/netmix/radio-station/docs/) and in the `/docs` folder of the plugin directory. 

= How do I schedule a Show? =

Simply create a new show via Add Show in the Radio Station plugin menu in the Admin area. You will be able to assign Shift timeslots to it on the Show edit page, as well as add the Show description and other meta fields, including Show images.

= How do I display a full schedule of my Station's shows? =

In the Plugin Settings, you can select a Page on which to automatically display the schedule as well as which View to display (a Table grid by default.) Alternatively, you can use the shortcode `[master-schedule]` on any page (or post.) This option allows you to use further shortcode attributes to control the what is displayed in the Schedule (see [Master Schedule Shortcode Docs](./Shortcodes.md#master-schedule-shortcode) )

= I've scheduled all my Shows, but some are not showing up on the program schedule? =

Did you remember to check the "Active" checkbox for each Show? If a Show is not marked active, the plugin assumes that it's not currently in production and it is not shown on the Schedule. A Show will also not be shown if it has no active Shifts assigned to it.

= What if I want to schedule a special event? =

If you have a one-off event that you need to show up in the Schedule and Widgets, you can create a Schedule Override by clicking the Schedule Override tab in the Admin menu. This will allow you to set aside a block of time on a specific date, and when the Schedule or Widget is displaying that date, the override will be used instead of the normally scheduled Show. (Note that Schedule Overrides will not display in the old Legacy Table or List Views of the Master Schedule.)

= I'm seeing a 404 Not Found error when I click on the link for a Show! =

Try re-saving your site's permalink settings via Settings -> Permalinks.  Wordpress sometimes gets confused with a new custom post type is added. Permalink rewrites are automatically flushed on plugin activation, so you can also just deactivate and reactivate the plugin.

= What if I want to change or style the plugin's displays? =

The default styles for Radio Station have intentionally been kept fairly minimal so as to be compatible with most themes, so you may wish to add your own styles to suit your site's look and feel. The best way to do this is to add your own `rs-custom.css` to your Child Theme's directory, and add more specific style rules that modify or override the existing styles. Radio Station will automatically detect the presence of this file and enqueue it. You can find the base styles in the `/css/` directory of the plugin.

= What Widgets are available with this plugin? =

The following Widgets are available to add via the WordPress Appearance -> Widgets page:
Current Show, Upcoming Shows, Current Playlist. Radio Clock and Streaming Player Widgets will also be available in future versions. See the [Widget Documentation](./Widgets.md) for more details on these Widgets.

= What Shortcodes are available with this plugin? =

See the [Shortcode Documentation](./Shortcodes.md) for more details and a full list of possible Attributes for these Shortcodes:

* `[master-schedule]` - Master Program Schedule Display
* `[current-show]` - Current Show Widget
* `[upcoming-shows]` - Upcoming Shows Widget
* `[current-playlist]` - Current Playlist Widget
* `[shows-archive]` - Archive List of Shows
* `[genres-archive]` - Archive List of Shows sorted by Genre
* `[overrides-archive]` - Archive List of Schedule overrides
* `[playlists-archive]` - Archive List of Show Playlists

Note old shortcode aliases will still work in current and future versions to prevent breakage.

= I need users other than just the Administrator and DJ roles to have access to the Shows and Playlists post types. How do I do that? =

There are a number of different options depending on what you are wanting to to do. You can find more information on these in the [Roles Documentation](./Roles.md)

= How do I change the Show Avatar displayed in the sidebar widget? =

The avatar is whatever image is assigned as the Show's Avatar.  All you have to do is set a new Show Avatar on the Edit page for that Show.

= Why don't any users show up in the Hosts or Producers list on the Show edit page? =

You did remember to assign the Host or Producer role to the users you want, right?

= My Show Hosts and Producers can't edit a Show page.  What do I do? =

The only Hosts and Producers that can edit a show are the ones listed as being Hosts or Producers for that Show in the respective user selection menus. This is to prevent Hosts/Producers from editing other Host/Producer's Shows without permission.

= I don't want to use Gravatar for my Host/Producer's image on their profile page. =

Then you'll need to install a plugin that lets you add a different image to your Host/Producer's user account and edit your author.php theme file accordingly.  That's a little out of the scope of this plugin. I recommend [Cimy User Extra Fields](http://wordpress.org/extend/plugins/cimy-user-extra-fields/)

= What languages other than English is the plugin available in? =

Right now:

* Albanian (sq_AL)
* Dutch (nl_NL)
* French (fr_FR)
* German (de_DE)
* Italian (it_IT)
* Russian (ru_RU)
* Serbian (sr_RS)
* Spanish (es_ES)
* Catalan (ca)

= Can the plugin be translated into my language? =

You may translate the plugin into another language. Please visit our [WordPress Translate project page](https://translate.wordpress.org/locale/en-gb/default/wp-plugins/radio-station/) for this plugin for further instruction. The `radio-station.pot` file is located in the `/languages` directory of the plugin. Please send the finished translation to `info@netmix.com`. We'd love to include it.

= Can I use this plugin for Podcasts? =

While the plugin is not specifically geared toward Podcasting, which is not live programming, some podcaster's have used Radio Station to let their subscribers know when they publish new shows.

= Can I use this plugin for TWitchTV, Facebook Live, or Clubhouse shows? =

Sure, there's no reason why you couldn't use the plugin to display a show schedule on a WordPress site for those services. Unfortunately, we are not currently syncing events from these platforms, but may do so in the future. While there may be APIs available from the larger services, Clubhouse does not yet have a public API, so scheduled rooms can't be automated to the Radio Station show scheduling system.

= I use Google Calendar to print a show schedule online. Can I import/sync my Google Calendar with Radio Station? =

We haven't built an interface between Google Calendar and Radio Station just yet, but it's on our radar to do so in the foreseeable future.



== Screenshots ==
1. Table Schedule View
2. Tabbed Schedule View
3. Show Page Layout
4. Playlist Page Layout
5. Current Show Widget
6. Show Archive List Shortcode
7. Admin Settings Panel
8. Show Conflict Display

== Changelog ==

= 2.3.3.8 =
* Update: Plugin Panel (1.1.7) with Image and Color Picker fields
* Added: Stream Format and Fallback/Format selection setting
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
* Fixed: Changed stable tag from trunk to version number to fix translations issue

= 2.3.3.7 =
* Fixed: Schedule Overrides overlapping multiple Show shifts
* Fixed: Bulk Edit field repetition and possible jQuery conflict
* Fixed: Related Posts check producing error output
* Fixed: WordPress Readme Parser deprecated errors for PHP7

= 2.3.3.6 =
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

= 2.3.3.5 =
* Fixed: use schedule based on start_day if specified for Schedule view
* Fixed: day left/right shifting on Schedule table/tab mobile views
* Added: past/today/future filter for Schedule Override List 
* Added: filter for Schedule display start day (and to accept today)
* Added: current playlist (if any) to Broadcast Data endpoint

= 2.3.3.4 =
* Improved: auto-match show description to info height on Show pages
* Improved: allow multiple Related Show selection for single post
* Improved: ability to assign Post to relate to multiple Shows
* Added: Related Show Post List column and Quick Edit field
* Added: Related Show selection Bulk Edit Action for Post List
* Added: filters for label texts and title attributes on Show Page
* Added: filter for label text above Show Player (default empty)

= 2.3.3.3 =
* Fixed: improved Current Show and Upcoming Shows calculations
* (Display showtimes when show starts before and ends after midnight)

= 2.3.3.2 =
* Update: Freemius SDK (2.4.0)
* Update: Plugin Loader (1.1.4) with weird isset glitch fix
* Fixed: Current Show for Shows ending at midnight
* Fixed: incorrect AJAX Widget plugin setting value
* Fixed: use pageload data for schedules before transients

= 2.3.3 =
* Update: Plugin Loader (1.1.3) with non-strict select match fix
* Improved: width responsiveness for table/tabbed Schedule views
* Improved: show shifts interface background colors
* Added: navigate away from page on shift change check
* Added: default time format option to Widgets
* Removed: current show transients (intermittent unreliability)
* Fixed: AJAX call causing plugin conflicts via save_post action
* Fixed: calculation of Upcoming Shows near end of the week
* Fixed: remove and duplicate actions on new shifts

= 2.3.2 =
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

= 2.3.1 =
* Update: Plugin Loader (1.1.1) with Freemius first path fix
* Fixed: conditions for Schedule Override time calculations
* Fixed: schedule table view - 12 hour format with translations
* Fixed: schedule table view hour column width style
* Fixed: javascript table/tab arrows to prevent default click
* Fixed: undefined index warning when saving show with no shifts
* Fixed: append not echo override date to shortcode archive list
* Fixed: compatibility with multiple the_content calls (Yoast)
* Fixed: reset to showcontinued flag in Schedule (table view)
* Added: show avatar and featured image URLs to Data API output
* Added: option to ping Netmix directory on show updates
* Added: option to clear transients on every pageload
* Added: filters for widget section display order

= 2.3.0 =
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

= 2.2.8 =
* Fix to remove strict type checking from in_array (introduced 2.2.6)
* Fix to mismatched flush rewrite rules flag function name
* Fix to undefined index warnings for new Schedule Overrides
* Fix to not 404 author pages for DJs without blog posts

= 2.2.7 =
* Dutch translation added (Thank you to André Dortmont for the file!)
* Added Tabbed Display for Master Schedule Shortcode (via Tutorial)
* Add Show list columns with active, shift, DJs and show image displays
* Add Schedule Override list columns with date sorting and filtering
* Add playlist track information labels to Now Playing Widget
* Added meridian (am/pm) translations via WP Locale class
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

= 2.2.6 =
* Reorganize master-list shortcode into templates
* Add constant for plugin directory
* Use WP_Query instead of get_posts
* New posts_per_page and tax_query
* Fixes for undefined indexes
* Fixes for raw mysql queries
* Typecasting to support strict comparisons

= 2.2.5 =
* WordPress coding standards and best practices (thanks to Mike Garrett @mikengarrett)

= 2.2.4 =
* added title position and avatar width options to widgets
* added missing DJ author links as new option to widgets
* cleanup, improve and fix enqueued Widget CSS (on air/upcoming)
* improved to show Encore Presentation in show widget displays
* fix to Show shift Encore Presentation checkbox saving

= 2.2.3 =
* added flush rewrite rules on plugin activation/deactivation
* added show_admin_column and show_in_quick_edit for Genres
* added show metadata and schedule value sanitization
* fix to 00 minute validation for Schedule Override
* convert span tags to div tags in Widgets to fix line breaks

= 2.2.2 =
* shift main playlist and show metaboxes above editor
* set plugin custom post types editor to Classic Editor
* add high priority to side metaboxes for plugin post types
* added dismissible development changeover admin notice
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

= 2.2.1 =
* Re-commit all missing files via SVN

= 2.2.0 =
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

= 2.1.3 =
* Added method for displaying schedule for only a single day (see readme section for the master-schedule shortcode for details).

= 2.1.2 =
* Compatibility fix for Wordpress 4.3.x - Updated the widgets to use PHP5 constructors instead of the deprecated PHP4 constructors.
* Catalan translation added (Thank you to Victor Riera for the file!)

= 2.1.1 = 
* Bug fix - Fixed day of the week language translation issue in master schedule shortcode
* Bug fix - Added some error checking in the sidebar widgets
* New Feature - Added ability to give schedule overrides a featured image
* New Feature - Added built-in help page

= 2.1 =
* General code cleanup, 4.1 compatibility testing, and changes for better efficiency.
* Bug fix - Fixed issue with early morning shows spanning entire column in the programming grid shortcode
* New Feature - Master programming grid can now be displayed in div format, as well as the original table and list formats.

= 2.0.16 =
* Minor revisions to German translation.
* Fixed a bug that was resetting custom-sert role capabilities for the DJ role.

= 2.0.15 =
* German translation added (Thank you to Ian Hook for the file!)

= 2.0.14 =
* Fixed issue on the master schedule where genres containing more than one work wouldn't highlight when clicked
* Added ability to display DJ names on the master schedule.
* Fixed bug in the Upcoming widget.  Override Schedule no longer display as upcoming when they are on-air.
* Verified compatibility with WordPress 4.0

= 2.0.13 =
* Added the ability to display show avatars on the program grid.
* Added the ability to display show description in the now on-air widget and short code. 

= 2.0.12 =
* Fixed a bug in the master schedule shortcode

= 2.0.11 =
* Russian translation added (Thank you to Alexander Esin for the file!)

= 2.0.10 =
* Fixed role/capability conflict with WP User Avatar plugin.
* Added the missing leading zero to 24-hour time format on the master schedule.
* Fixed dj_get_current function so that it no longer returns shows that have been moved to the trash.
* Fixed dj_get_next function so that it no longer ignores the "Active" checkbox on a show.
* Added some CSS ids and classes to the master program schedule list format to make it more useful

= 2.0.9 = 
* Fixed broken upcoming show shortcode.
* Added ability to display DJ names along with the show title in the widgets.

= 2.0.8 =
* Fixed the display of schedules for upcoming shows in the widget and shortcode.
* Fixed a bug in the dj_get_next function that was causing it to ignore the beginning of the next week at the end of the current week.

= 2.0.7 =
* Fixed scheduling bug in shortcode function

= 2.0.6 =
* Master Schedule now displays days starting with the start_of_week option set in the WordPress General Settings panel. 
* Fixed issue with shows that have been unplublished still showing up on the master schedule.
* Fixed missing am/pm text on shows that run overnight on the master schedule.
* Fixed an issue with shows that run overnight not spanning the correct number of hours on the second day on the master schedule.
* Fixed problem in Upcoming DJ Widget that wasn't displaying the correct upcoming shift.

= 2.0.5 =
* Fixed an issue with some shows displaying in 24 hour time on master schedule grid even though 12-hour time is specified
* Fixed a bug in the On-Air widget that was preventing shows spanning two day from displaying
* Added code to enable theme support for post-thumbnails on the "show" post-type so users don't have to add it to their theme's functions.php file anymore.

= 2.0.4 =
* Master Schedule bug for shows that start at midnight and end before the hour is up fixed.

= 2.0.3 =
* Compatibility fix: Fixed a jquery conflict in the backend that was occurring in certain themes

= 2.0.2 =
* Bug fix: Scheduling issue with overnight shows fixed

= 2.0.1 =
* Bug fix: Fixed PHP error in Playlist save function that was triggered during preview
* Bug fix: Fixed PHP notice in playlist template file
* Bug fix: Fixed PHP error in dj-widget shortcode

= 2.0.0 =
* Major code reorganization for better future development
* PHP warning fix
* Enabled option to add comments on Shows and Playlists
* Added option to show either single or multiple schedules in the On Air widget

= 1.6.2 =
* Minor PHP warning fixes

= 1.6.1 =
* Bug fix: Some of the code added in the previous update uses the array_replace() function that is only available in PHP 5.3+.  Added a fallback for older PHP versions.

= 1.6.0 =
* Added the ability to override the weekly schedule to allow one-off events to be scheduled
* Added a list format option to the master schedule shortcode
* Added Italian translation (it_IT) (thank you to Cristofaro Giuseppe!)

= 1.5.4 =
* Fixed some PHP notices that were being generated when there were no playlist entries in the system.

= 1.5.3 =
* Added Serbian translation (sr_RS) (thank you to Miodarag Zivkovic!)

= 1.5.2.1 =
* Removed some debug code from one of the template files

= 1.5.2 =
* Fixed some localization bugs.
* Added Albanian translation (sq_AL) (thank you to Lorenc!)

= 1.5.1 =
* Fixed some localization bugs.
* Added French translation (fr_FR) (a big thank you to Dan over at BuddyPress France - http://bp-fr.net/)

= 1.5.0 =
* Plugin modified to allow for internationalization.
* Spanish translation (es_ES) added.

= 1.4.6 =
* Fixed a bug with shows that start at midnight not displaying in the on-air sidebar widget.
* Switched DJ/Show avatars in the widgets to use the featured image of the show instead of gravatar.
* Updated show template to get rid of a PHP warning that appeared if the show had no schedules. 
* Fixed some other areas of the code that were generating PHP notices in WordPress 3.6
* Added CSS classes to master program schedule output so CSS rules can be applied to specific shows
* Added new attribute to the list-shows shortcode to allow only specified genres to be displayed

= 1.4.5 =
* Fixed master-schedule shortcode bug that was preventing display of 12 hour time

= 1.4.4 =
* Compatibility fix for Wordpress 3.6 - fixed problem with giving alternative roles DJ capabilities
* Fixed some areas of the code that were generating PHP notices in WordPress 3.6

= 1.4.3 =
* Master schedule shortcode now displays individual shows in both 24 and 12 hour time
* Fixed some areas of the code that were generating PHP notices in WordPress 3.6
* Added example of how to display show schedule to single-show.php template
* Added more options to the plugin's widgets
* Added new options to the master-schedule shortcode

= 1.4.2 =
* Fixed a bug in the CSS file override from theme directory

= 1.4.1 =
* Fixed issue with templates copied to the theme directory not overriding the defaults correctly
* Fixed incorrectly implemented wp_enqueue_styles()
* Removed deprecated escape_attribute() function from the plugin widgets
* Fixed some areas of the code that were generating PHP notices

= 1.4.0 =
* Compatibility fix for WordPress 3.6

= 1.3.9 =
* Fixed a bug that was preventing sites using a non-default table prefix from seeing the list of DJs on the add/edit show pages

= 1.3.8 =
* Changes to fix the incorrect list of available shows on the Add Playlist page
* Removing Add Show links from admin menu for DJs, since they don't have permission to use them anyway.

= 1.3.7 =
* Fixed a scheduling bug in the upcoming shows widget
* By popular request, switched the order of artist and song in the now playing widget

= 1.3.6 =
* Fixed issue with shows that run overnight not showing up correctly in the sidebar widgets

= 1.3.5 =
* Fixed a time display bug in the DJ On-Air sidebar widget
* Fixed a display bug on the master schedule with overnight shows

= 1.3.4 =
* By request, added as 24-hour time format option to the master schedule and sidebar widgets.

= 1.3.3 =
* Added the ability to assign any user with the edit_shows capability as a DJ, to accomodate custom and edited roles.

= 1.3.2 =
* Fixed a bug in the DJ-on-air widget

= 1.3.1 =
* Fixed a major bug in the master schedule output

= 1.3 =
* Fixed some minor compatibility issues with WordPress 3.5
* Fixed Shows icon in Dashboard

= 1.2 =
* Fixed thumbnail bug in sidebar widgets
* Added new widget to display upcoming shows
* Added pagination options for playlists and show blogs

= 1.1 =
* Fixed playlist edit screen so that queued songs fall to the bottom of the list to maintain play order
* Reduced the size of the content field in the playlist post type
* Some minor formatting changes to default templates
* Added genre highlighter to the master programming schedule page
* Added a second Update button on the bottom of the playlist edit page for convenience.
* Added sample template for DJ user pages
* Fixed a bug in the master schedule shortcode that messed up the table for shows that are more than two hours in duration
* Fixed a bug in the master schedule shortcode to accomodate shows that run from late night into the following morning.
* Added new field to associate blog posts with shows

= 1.0 =
* Initial release

== Upgrade Notice ==

= 2.3.3.8 =
* Updated Plugin Panel Library
* Added Stream Format selection setting
* Added Station email address setting with default display option
* Added Section order filtering for Master Schedules and Widgets
* Added Show image alignment attribute to Schedule Tabs View

= 2.3.3.7 =
* Updated Freemius SDK and Plugin Loader libraries
* Added Station phone number setting with default display option
* Added Schedule classes for Shows before and after current Show
* Multiple Related Show Post assignment edit and link fixes
* Bugfixes for permissions, main language and shift checker

= 2.3.3.6 =
* Updated Freemius SDK and Plugin Loader libraries
* Added Station phone number setting with default display option
* Added Schedule classes for Shows before and after current Show
* Multiple Related Show Post assignment edit and link fixes
* Bugfixes for permissions, main language and shift checker

= 2.3.3.5 =
* Ability to assign Post to relate to multiple Shows
* Added Admin Filtering, Bulk Edit and Quick Edit interfaces
* Fixes for Schedule display left/right shifting on mobiles
* Fixes for starting Schedule display on different day

= 2.3.3.3 =
* Current and Upcoming Shows Widget Fix

= 2.3.3.2 =
* Minor Bugfix Update

= 2.3.3 =
* Important Bugfix Update
* Fix to conflict with plugins using AJAX save_post calls
* Improved accuracy for responsive table/tab Schedule views
* Added colour improvements to Show Shift interface
* Fix to calculate Current Show (transient no longer used)

= 2.3.2 =
* Improved Times, AJAX Loading and Bugfix Update 
* https://netmix.com/radio-station-2-3-2-release/
* Radio Clock Widget and Widget AJAX Loading
* AJAX Saving of Show Shifts and Playlist Tracks
* Automated current Show schedule highlighting
* Improved timezones, overrides, shift checking and more

= 2.3.1 =
* Bugfix Update and Announcing New Netmix Station Directory!
* https://netmix.com/announcing-new-netmix-directory/
* Including minor fixes to major update release
* Option to ping Netmix Directory on show updates

= 2.3.0 =
* Major Update including many new features, enhancements and fixes!
* https://netmix.com/radio-station-2-3-0-release/
* Revamped Templates, Master Schedule Views, Shortcodes and Widgets
* Added Admin Options, REST API Routes and Shift Conflict Checking
* Added Show Producers, Language Taxonomy, Timezones and Countdowns
* Improved User Roles, Show Images, Post Type Supports + much more!
