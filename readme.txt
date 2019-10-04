=== Radio Station ===
Contributors: tonyzeoli, majick, nourma
Donate link: https://netmix.co/donate
Tags: dj, music, playlist, radio, shows, scheduling, broadcasting
Requires at least: 3.3.1
Tested up to: 5.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Radio Station is a plugin to build and manage a Show Calendar in a radio station or Internet broadcaster's WordPress website. Functionality is based on Drupal 6's Station plugin.

== Description ==

Radio Station is a plugin to build and manage a Show Calendar in a radio station or Internet broadcaster's WordPress website. It's functionality is based on Drupal 6's Station plugin, reworked for use in Wordpress.

The plugin includes the ability to associate users (as member role "DJ"") with the included custom post type of "Shows" (schedulable blocks of time that contain a Show description, and other meta information), and generate playlists associated with those shows.

The plugin contains a widget to display the currently on-air DJ with a link to the DJ's Show page and current playlist.  A schedule of all Shows can also be generated and added to a page with a short code. Shows can be categorized and a category filter appears when the Calendar is added using a short code to a WordPress page or post.

We are grateful to Nikki Blight for her contribution to creating and developing this plugin for as long as she could maintain the codebase. As of June 22, 2019, Radio Station is managed by <a href="https://profiles.wordpress.org/tonyzeoli/">Tony Zeoli</a>  and developed by contributing committers to the project.

If you are a WordPress developer wanting to contribute to Radio Station, please join the team and follow plugin development on Github: <a href="https://github.com/netmix/radio-station">https://github.com/netmix/radio-station</a>/.

Submit bugs and feature requests here: <a href="https://github.com/netmix/radio-station/issues">https://github.com/netmix/radio-station/issues</a>

We are actively seeking radio station partners and donations to fund further development of the free, open source version of this plugin at: <a href="https://www.patreon.com/radiostation">https://www.patreon.com/radiostation</a>.

For plugin support, please give 24-48 hours to answer support questions, which will be handled in the Wordpress Support Forums for this free version of the plugin here: <a href="https://wordpress.org/support/plugin/radio-station/">https://wordpress.org/support/plugin/radio-station/</a>

You can find a demo version of the plugin on our demo site here: <a href="https://radiostationdemo.com">https://radiostationdemo.com</a>

== Installation ==

1. Upload plugin .zip file to the `/wp-content/plugins/` directory and unzip.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Give any users who need access to the plugin the role of "DJ".  Only DJ and administrator roles have administrative access.
4. Create shows and set up shifts.
5. Add playlists to your shows.

== Frequently Asked Questions ==

= I've scheduled all my shows, but they're not showing up on the programming grid! =
Did you remember to check the "Active" checkbox for each show?  If a show is not marked active, the plugin assumes that it's not currently in production and 
hides it on the grid.

= I'm seeing 404 Not Found errors when I click on the link for a show! = 
Try re-saving your site's permalink settings.  Wordpress sometimes gets confused with a new custom post type is added.

= How do I display a full schedule of my station's shows? =
Use the shortcode `[master-schedule]` on any page.  This will generate a full-page schedule in one of three formats.

The following attributes are available for the shortcode:
			'list' => If set to a value of 'list', the schedule will display in list format rather than table or div format. Valid values are 'list', 'divs', 'table'.  Default value is 'table'.
			'time' => The time format you with to use.  Valid values are 12 and 24.  Default is 12.
			'show_link' => Display the title of the show as a link to its profile page.  Valid values are 0 for hide, 1 for show.  Default is 1.
			'display_show_time' => Display start and end times of each show after the title in the grid.  Valid values are 0 for hide, 1 for show.  Default is 1.
			'show_image' => If set to a value of 1, the show's avatar will be displayed.  Default value is 0.
			'show_djs' => If set to a value of 1, the names of the show's DJs will be displayed.  Default value is 0.
			'divheight' => Set the height, in pixels, of the individual divs in the 'divs' layout.  Default is 45.
			'single_day' => Display schedule for only a single day of the week.  Only works if you are using the 'list' format.  Valid values are sunday, monday, tuesday, wednesday, thursday, friday, saturday.
			
For example, if you wish to display the schedule in 24-hour time format, use `[master-schedule time="24"]`.  If you want to only show Sunday's schedule, use `[master-schedule list="list" single_day="sunday"]`.

= How do I schedule a show? =

Simply create a new show.  You will be able to assign it to any timeslot you wish on the edit page.

= What if I have a special event? =

If you have a one-off event that you need to show up in the On-Air or Coming Up Next widgets, you can create a Schedule Override by clicking the Schedule Override tab
in the Dashboard menu.  This will allow you to set aside a block of time on a specific date, and will display the title you give it in the widgets.  Please note that 
this will only override the widgets and their corresponding shortcodes.  If you are using the weekly master schedule shortcode on a page, its output will not be altered.

= How do I get the last song played to show up? = 

You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode `[now-playing]` in your page/post, or use `do_shortcode('[now-playing]');` in your template files.

The following attributes are available for the shortcode:
			'title' => The title you would like to appear over the now playing block
			'artist' => Display artist name.  Valid values are 0 for hide, 1 for show.  Default is 1.
			'song' => Display song name.  Valid values are 0 for hide, 1 for show.  Default is 1.
			'album' => Display album name.  Valid values are 0 for hide, 1 for show.  Default is 0.
			'label' => Display label name.  Valid values are 0 for hide, 1 for show.  Default is 0.
			'comments' => Display DJ comments.  Valid values are 0 for hide, 1 for show.  Default is 0.

Example:
`[now-playing title="Current Song" artist="1" song="1" album="1" label="1" comments="0"]`

= What about displaying the current DJ on air? =

You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode `[dj-widget]` in your page/post, or you can use
`do_shortcode('[dj-widget]');` in your template files.

The following attributes are available for the shortcode:
		'title' => The title you would like to appear over the on-air block 
		'display_djs' => Display the names of the DJs on the show.  Valid values are 0 for hide names, 1 for show names.  Default is 0.
		'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.
		'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.
		'default_name' => The text you would like to display when no show is schedule for the current time.
		'time' => The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.
		'show_sched' => Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.
		'show_playlist' => Display a link to the show's current playlist.  Valid values are 0 for hide link, 1 for show link.  Default is 1.
		'show_all_sched' => Displays all schedules for a show if it airs on multiple days.  Valid values are 0 for current schedule, 1 for all schedules.  Default is 0.
		'show_desc' => Displays the first 20 words of the show's description. Valid values are 0 for hide descripion, 1 for show description.  Default is 0.
		
Example:
`[dj-widget title="Now On-Air" display_djs="1" show_avatar="1" show_link="1" default_name="RadioBot" time="12" show_sched="1" show_playlist="1"]`


= Can I display upcoming shows, too? =

You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode `[dj-coming-up-widget]` in your page/post, or you can use
`do_shortcode('[dj-coming-up-widget]');` in your template files.

The following attributes are available for the shortcode:
		'title' => The title you would like to appear over the on-air block 
		'display_djs' => Display the names of the DJs on the show.  Valid values are 0 for hide names, 1 for show names.  Default is 0.
		'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.
		'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.
		'limit' => The number of upcoming shows to display.  Default is 1.
		'time' => The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.
		'show_sched' => Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.
		
Example:
`[dj-coming-up-widget title="Coming Up On-Air" display_djs="1" show_avatar="1" show_link="1" limit="3" time="12" schow_sched="1"]`

= Can I change how show pages are laid out/displayed? =

Yes.  Copy the radio-station/templates/single-show.php file into your theme directory, and alter as you wish.  This template, and all of the other templates
in this plugin, are based on the TwentyEleven theme.  If you're using a different theme, you may have to rework them to reflect your theme's layout.

= What about playlist pages? =

Same deal.  Grab the radio-station/templates/single-playlist.php file, copy it to your theme directory, and go to town.

= And playlist archive pages?  =

Same deal.  Grab the radio-station/templates/archive-playlist.php file, copy it to your theme directory, and go to town.

= And the program schedule, too? = 

Because of the complexity of outputting the data, you can't directly alter the template, but you can copy the radio-station/templates/program-schedule.css file
into your theme directory and change the CSS rules for the page.

= What if I want to style the DJ on air sidebar widget? =

Copy the radio-station/templates/djonair.css file to your theme directory.

= How do I get an archive page that lists ALL of the playlists instead of just the archives of individual shows? =

First, grab the radio-station/templates/playlist-archive-template.php file, and copy it to your active theme directory.  Then, create a Page in wordpress
to hold the playlist archive.  Under Page Attributes, set the template to Playlist Archive.  Please note: If you don't copy the template file to your theme first, 
the option to select it will not appear.

= Can show pages link to an archive of related blog posts? =

Yes, in much the same way as the full playlist archive described above. First, grab the radio-station/templates/show-blog-archive-template.php file, and copy it to 
your active theme directory.  Then, create a Page in wordpress to hold the blog archive.  Under Page Attributes, set the template to Show Blog Archive.

= How can I list all of my shows? =

Use the shortcode `[list-shows]` in your page/posts or use `do_shortcode(['list-shows']);` in your template files.  This will output an unordered list element
containing the titles of and links to all shows marked as "Active". 

The following attributes are available for the shortcode:
		'genre' => Displays shows only from the specified genre(s).  Separate multiple genres with a comma, e.g. genre="pop,rock".

Example:
`[list-shows genre="pop"]`
`[list-shows genre="pop,rock,metal"]`

= I need users other than just the Administrator and DJ roles to have access to the Shows and Playlists post types.  How do I do that? =

Since I'm stongly opposed to reinventing the wheel, I recommend Justin Tadlock's excellent "Members" plugin for that purpose.  You can find it on
Wordpress.org, here: http://wordpress.org/extend/plugins/members/

Add the following capabilities to any role you want to give access to Shows and Playlist:

edit_shows
edit_published_shows
edit_others_shows
read_shows
edit_playlists
edit_published_playlists
read_playlists
publish_playlists
read
upload_files
edit_posts
edit_published_posts
publish_posts

If you want the new role to be able to create or approve new shows, you should also give them the following capabilities:

publish_shows
edit_others_shows

= How do I change the DJ's avatar in the sidebar widget? =

The avatar is whatever image is assigned as the DJ/Show's featured image.  All you have to do is set a new featured image.

= Why don't any users show up in the DJs list on the Show edit page? =

You did remember to assign the DJ role to the users you want to be DJs, right?

= My DJs can't edit a show page.  What do I do? = 

The only DJs that can edit a show are the ones listed as being ON that show in the DJs select menu.  This is to prevent DJs from editing other DJs shows 
without permission.

= How can I export a list of songs played on a given date? =

Under the Playlists menu in the dashboard is an Export link.  Simply specify the a date range, and a text file will be generated for you.

= Can my DJ's have customized user pages in addition to Show pages? = 

Yes.  These pages are the same as any other author page (edit or create the author.php template file in your theme directory).  A sample can be found 
in the radio-station/templates/author.php file (please note that this file doesn't actually do anything unless you copy it over to your theme's
directory).  Like the other theme templates included with this plugin, this file is based on the TwentyEleven theme and may need to be modified in
order to work with your theme.

= I don't want to use Gravatar for my DJ's image on their profile page. =

Then you'll need to install a plugin that lets you add a different image to your DJ's user account and edit your author.php theme file accordingly.  That's a 
little out of the scope of this plugin.  I recommend Cimy User Extra Fields:  http://wordpress.org/extend/plugins/cimy-user-extra-fields/

= What languages other than English is the plugin available in? =

Right now:

Albanian (sq_AL)
Dutch (nl_NL)
French (fr_FR)
German (de_DE)
Italian (it_IT)
Russion (ru_RU)
Serbian (sr_RS)
Spanish (es_ES)
Catalan (ca)

= Can the plugin be translated into my language? =

You may translate the plugin into another language. Please visit our WordPress Translate project page for this plugin for further instruction: <a target="_top" href="https://translate.wordpress.org/locale/en-gb/default/wp-plugins/radio-station/">https://translate.wordpress.org/locale/en-gb/default/wp-plugins/radio-station/</a> The radio-station.pot file is located in the /languages directory of the plugin. Please send the finished translation to info@netmix.com. We'd love to include it.

== Changelog ==

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
* Verified compatibility woth WordPress 4.0

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
* Compatibility fix: Fixed a jquery conflict in the backend that was occuring in certain themes

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
* Master schedule shortcode now displays indiviual shows in both 24 and 12 hour time
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
* Added a second Update button on the bottom of the playlist edit page for convinience.
* Added sample template for DJ user pages
* Fixed a bug in the master schedule shortcode that messed up the table for shows that are more than two hours in duration
* Fixed a bug in the master schedule shortcode to accomodate shows that run from late night into the following morning.
* Added new field to associate blog posts with shows

= 1.0 =
* Initial release

== Upgrade Notice ==

= 2.2.7 =
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

= 2.2.6 =
* Reorganize master-list shortcode into templates,
* Add constant for plugin directory,
* WP_Query instead of get_posts,
* new posts_per_page and tax_query,
* fixes for undefined indexes,
* fixes for raw mysql queries,
* typecasting to support strict comparisons.

= 2.2.5 =
* WordPress coding standards and best practices (thanks to Mike Garrett @mikengarrett)

= 2.2.4 =
Adds title position and avatar width options to widgets; missing DJ author links as new option to widgets; cleanup, improve and fix enqueued Widget CSS (on air/upcoming); show Encore Presentation in show widget displays; fix to Show shift Encore Presentation checkbox saving

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
* added dismissable development changeover admin notice
* added simple Patreon supporter image button and blurb
* added filter for DJ Avatar size on Author page template
* fix to Schedule Override metabox value saving
* fix to Playlist track list overflowing metabox
* fix to missing day of week headings in master schedule
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
* Verified compatibility woth WordPress 4.0

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
* Compatibility fix: Fixed a jquery conflict in the backend that was occuring in certain themes

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
* Master schedule shortcode now displays indiviual shows in both 24 and 12 hour time
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
* Added a second Update button on the bottom of the playlist edit page for convinience.
* Added sample template for DJ user pages
* Fixed a bug in the master schedule shortcode that messed up the table for shows that are more than two hours in duration
* Fixed a bug in the master schedule shortcode to accomodate shows that run from late night into the following morning.
* Added new field to associate blog posts with shows

= 1.0 =
* Initial release  