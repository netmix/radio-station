# Radio Station

Radio Station lets you build and manage a Show Schedule for a radio station or Internet broadcaster's WordPress website. 

## Plugin Details

Contributors: tonyzeoli, majick

Donate link: https://netmix.co/donate

Tags: dj, music, playlist, radio, shows, scheduling, broadcasting

Requires at least: 3.3.1

Tested up to: 5.9.3

Stable tag: 2.4.0.9

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html


## Description

Radio Station by NetMix is a plugin to build and manage a Show Schedule for a radio station or internet broadcaster's WordPress website, including podcasters. Its functionality was originally based on Drupal 6's Station plugin, reworked for use in Wordpress and then extended.

The plugin adds a new "Show" post type, schedulable blocks of time that contain a Show description, a Show shifts repeater field, assignable images and other meta information. You can also create Playlists associated with those shows, or assign standard blog posts to relate to a Show. It also supports adding Schedule Overrides for specific dates and times, and adds the ability to associate users (given a role of "Host" or "Producer") to Shows, so they can be displayed for that Show and to give them edit access.

A schedule of all Shows can be generated and added to a page with a shortcode (or simple page selection in the Plugin Settings) which has a number of Layout and display options. Shows can be categorized into Genres and a Genre highlighting filter appears on the embedded Schedule view. Each Show has its own dedicated page to display all the Show details in a responsive layout.

The plugin contains a widget to display the on-air Current Show linked to the Show page, with various widget display options, and further widgets for displaying Upcoming Shows and current Playlist tracks. Shortcodes are available for these widgets, as well as for displaying archive lists of any of the plugin's custom post types.

As there is a lot you can do with Radio Station, we've made an effort to provide complete [Radio Station Plugin Documentation](https://radiostation.pro/docs/). You can also find a Quickstart Guide there, as well as in the section below. You can see some example displays from the plugin via the Screenshots section, and full live examples are available on the [Radio Station Plugin Demo Site](https://radiostation.pro).

We are actively seeking Radio Station partners and supporters to fund further development of the free, open source version of this plugin via [Patreon](https://www.patreon.com/radiostation) and are also continuing to develop more exciting features and functionality for [Radio Station Pro](https://radiostation.pro/).

### Updating from Prior to 2.3.0

Since 2.3.0, the first major feature update since plugin takeover in July 2019, Radio Station has incorporated a whole bunch of enhancements (see the changelog for a full list)... but here is a shortlist of the main new features:

* an Updated Show Page Layout (based on Content Filters not Templates)
* Responsive Schedule Views (with integrated Override support)
* Revamped Schedule calculations (with Show Shift Conflict Checking)
* Producer and Show Editor Roles (for improved Show management)
* Language Taxonomy Assignments (for Shows and Overrides)
* Admin Plugin Settings Page (with a plethora of new options)
* ...and a Radio Station Data API via the WordPress REST API! 

If you have been using Radio Station prior to version 2.3.0 and want to update, it is recommended that you read [the blog post for the 2.3.0 release](https://netmix.com/2-3-0-release-announcement/). As there is quite a lot of refactoring and changes in this version, you will want to check the details of the new changes with your current usage - especially if you have been using any custom page templates in your theme or other plugin-related custom code on your site. As these are probably the most significant changes that will ever be made to the plugin in a release, we have worked hard to maintain backwards compatibility and test the new features thoroughly, but it's important you know what is going and test things out yourself in the update process.

### Support and Contribution

We are grateful to Nikki Blight for her contribution to creating and developing this plugin for as long as she could maintain the codebase. As of June 22, 2019, Radio Station is managed by [Tony Zeoli](https://profiles.wordpress.org/tonyzeoli/) with [Tony Hayes](https://profiles.wordpress.org/majick/) as lead developer and other contributing committers to the project.

For free version plugin support, you can ask in the [Wordpress Plugin Support Forum](https://wordpress.org/support/plugin/radio-station/). Please give 24-48 hours to answer support questions. Alternatively (and preferably) you can submit bugs, enhancement and feature requests directly to [Github Repository Issues](https://github.com/netmix/radio-station/issues/).

If you are a WordPress developer wanting to contribute to Radio Station, please join the team and follow plugin development on [Github](https://github.com/netmix/radio-station) and submit Issues and Pull Requests there. You can see the current progress via the Projects tab. Or if you would prefer to get involved even more substantially, please [Contact Us via Email](mailto:info@netmix.com) and let us know what you would like to do.

### Quickstart Guide

Once you have installed and activated the Radio Station Plugin on your WordPress site, your WordPress Admin area will now have a new menu item titled Radio Station with submenu page items. If you are trying to do something specific, you can check out the [FAQ](https://radiostation.pro/docs/FAQ.md) for Frequently Asked Questions as you may find the answer there.

Firstly, you can visit the Plugin Settings screen to adjust the default [Options](https://radiostation.pro/docs/Options.md) to your liking. Here you can set your Radio Timezone and Streaming URL (if you have one) along with other global plugin settings. Also from this Settings page you may want to assign [Pages](https://radiostation.pro/docs/Display.md#automatic-pages) and Views for your Program Schedule display and other optional Post Type Archive displays.

Add a New Show and assign it a Shift timeslot and Publish. Then check out how it displays on a single Show page by clicking the Show Permalink. Schedule Overrides work in a similar way but are for specific date and time blocks only. Depending on your Theme, you may wish to adjust the [Templates](https://radiostation.pro/docs/Display.md#page-templates) used. You can also assign different [Images](https://radiostation.pro/docs/Display.md#images) to Shows (and Schedule Overrides.) Then have a look at your Program Schedule page to see the Show displayed there also. Just keep adding Shows until you have your Schedule filled in! You can further [Manage](https://radiostation.pro/docs/Manage.md) your Shows and other Station data via the WordPress Admin area.

Next you may want to give some users on your site some plugin [Roles](https://radiostation.pro/docs/Roles.md). (Note that while the default interface in WordPress allows you to assign a single role to a user, it also supports multiple roles, but you need to add a plugin to get an interface for this.) Giving a Role of Host/DJ or Producer to a user will allow them to be assigned to a Show on the Show Edit Page and thus edit that particular Show also. You can also assign the Show Editor role if you have someone needs to edit all plugin records without being a site Administator.

There are a few [Widgets](https://radiostation.pro/docs/Widgets.md) you can add via your Appearance -> Widgets menu. The main one will display the currently playing Show, and another will display Upcoming Shows. There is also a Current Playlist Widget for if you have created and assigned a Playlist to a Show.

Then there are also a number of other [Shortcodes](https://radiostation.pro/docs/Shortcodes.md) you can use in your pages with different display options you can use in various places on your site also. There is the Master Schedule, Widget Shortcodes, and also Archive Shortcodes for each of the different data records. 

Radio Station has several in-built [Data](https://radiostation.pro/docs/Data.md) types. These include [Custom Post Types](https://radiostation.pro/docs/Data.md#custom-post-types) for Shows, Schedule Overrides and Playlists. There are [Taxonomies](https://radiostation.pro/docs/Data.md#taxonomies) for Genres and Languages. You can override most data values and display output via custom [Data Filters](#data-filters) throughout the plugin. We have also incorporated an [API](https://radiostation.pro/docs/API.md) in the plugin via REST and/or WordPress Feeds, and this data is accessible in JSON format. 

This plugin is under active development and we are continuously working to enhance the Free version available on [WordPress.Org](https://wordpress.org/plugins/radio-station/), as well as creating new feature additions for **Radio Station Pro**. Check out the [Roadmap](https://radiostation.pro/docs/Roadmap.md) if you are interested in seeing what is coming up next!

### Upgrading to Radio Station Pro

Love Radio Station and ready for more? As the free version develops, we have also been working hard to introduce new features to create a Professional version that will "level up" the plugin to make your Station's site even more useable and accessible for your listeners! [Click here to learn more about Radio Station Pro](https://radiostation.pro/). 


## Installation

1. Upload plugin .zip file to the `/wp-content/plugins/` directory and unzip.
2. Activate the plugin through the 'Plugins' menu in the WordPress Admin
3. Alternatively search for Radio Station via the WordPress admin Add New plugin interface and install and activate it there.
4. Give any users who need access to the plugin the role of "Host", "Producer" or "Show Editor". Assigning These roles gives publish and edit access to the plugin's records.
5. Create Shows, add Shifts to them, and assign Images, Genres, Languages, Hosts and/or Producers.
6. Add Playlists to your Shows or assign posts to Shows as needed.
7. Go to your admin Appearance -> Widgets page to add and configure Current and Upcoming Show Widgets, and any other desired plugin widgets.
8. See the QuickStart Guide above for more detailed instructions of what else is available.


## Frequently Asked Questions

#### How do I get started with Radio Station? =

Read the [Quickstart Guide](https://radiostation.pro/docs/#quickstart-guide) for an introduction to the plugin, what features are available and how to set them up.

#### Where can I find the full plugin documentation?

The latest documentation can be found online at [RadioStation.Pro](https://radiostation.pro/docs/). Documentation is also included with for the currently installed version via the Radio Station Help menu. You can find the Markdown-formatted files in the `/docs` folder of the [GitHub Repository](https://github.com/netmix/radio-station/docs/) and in the `/docs` folder of the plugin directory. 

#### Is there a demo site I can view to see how Radio Station works? =

Yes, visit the [Radio Station Demo Site](https://demo.radiostation.pro/) to view the Free and PRO features of Radio Station. (Note you will not be able to test the Visual Schedule Editor in PRO as it's only available for logged in users.)

#### How do I schedule a Show?

Simply create a new show via Add Show in the Radio Station plugin menu in the Admin area. You will be able to assign Shift timeslots to it on the Show edit page, as well as add the Show description and other meta fields, including Show images.

#### How do I display a full schedule of my Station's shows?

In the Plugin Settings, you can select a Page on which to automatically display the schedule as well as which View to display (a Table grid by default.) Alternatively, you can use the shortcode `[master-schedule]` on any page (or post.) This option allows you to use further shortcode attributes to control the what is displayed in the Schedule (see [Master Schedule Shortcode Docs](./Shortcodes.md#master-schedule-shortcode) )

#### I've scheduled all my Shows, but some are not showing up on the program schedule?

Did you remember to check the "Active" checkbox for each Show? If a Show is not marked active, the plugin assumes that it's not currently in production and it is not shown on the Schedule. A Show will also not be shown if it has a Draft status or has no active Shifts assigned to it.

#### What if I want to schedule a special event or one-off schedule change?

If you have a one-off event that you need to show up in the Schedule and Widgets, you can create a Schedule Override by clicking Schedule Override in the Radio Station admin menu. This will allow you to set aside a block of time on a specific date, and when the Schedule or Widget is displaying that date, the override will be used instead of the normally scheduled Show. (Note that Schedule Overrides will not display in the old Legacy Table/Div Views of the Master Schedule.)

#### I'm seeing a 404 Not Found error when I click on the link for a Show!

Try re-saving your site's permalink settings via Settings -> Permalinks.  Wordpress sometimes gets confused with a new custom post type is added. Permalink rewrites are automatically flushed on plugin activation, so you can also just deactivate and reactivate the plugin.

#### What if I want to change or style the plugin's displays?

The default styles for Radio Station have intentionally been kept fairly minimal so as to be compatible with most themes, so you may wish to add your own styles to suit your site's look and feel. The best way to do this is to add your own `rs-custom.css` to your Child Theme's directory, and add more specific style rules that modify or override the existing styles. Radio Station will automatically detect the presence of this file and enqueue it. You can find the base styles in the `/css/` directory of the plugin.

#### What Widgets are available with this plugin?

The following Widgets are available to add via the WordPress Appearance -> Widgets page:
Current Show, Upcoming Shows, Current Playlist. Radio Clock and Streaming Player Widgets will also be available in future versions. See the [Widget Documentation](./Widgets.md) for more details on these Widgets.

#### Do the Widgets reload automatically?

Current Show, Upcoming Shows and Current Playlist widgets do not refresh automatically in this free version. This capability has been added as a new feature to the [Pro version](https://radiostation.pro) however - so that the widgets refresh exactly at Show changeover times.

#### What Shortcodes are available with this plugin?

See the [Shortcode Documentation](./Shortcodes.md) for more details and a full list of possible Attributes for these Shortcodes:

* `[master-schedule]` - Master Program Schedule Display
* `[current-show]` - Current Show Widget
* `[upcoming-shows]` - Upcoming Shows Widget
* `[current-playlist]` - Current Playlist Widget
* `[shows-archive]` - Archive List of Shows
* `[genres-archive]` - Archive List of Shows sorted by Genre
* `[languages-archive]` - Archive List of Shows sorted by Language
* `[overrides-archive]` - Archive List of Schedule overrides
* `[playlists-archive]` - Archive List of Show Playlists

Note old shortcode aliases will still work in current and future versions to prevent breakage.

#### I need users other than just the Administrator and DJ roles to have access to the Shows and Playlists post types. How do I do that?

There are a number of different options depending on what you are wanting to to do. To address this situation, we have added a Show Editor role that can edit Shows without being an Administrator. You can find more information on roles in the [Roles Documentation](./Roles.md)

#### How do I change the Show Avatar displayed in the sidebar widget?

The avatar is whatever image is assigned as the Show's Avatar.  All you have to do is set a new Show Avatar on the Edit page for that Show.

#### Why don't any users show up in the Hosts or Producers list on the Show edit page? =

You need to assign the Host or Producer role to the users you want, so that you can select these users on the Show edit page. You can assign roles by editing the User via the WordPress admin. The [Pro version](https://radiostation.pro) has an additional Role Editor interface where you can assign the plugin roles to any number of users at once.

#### My Show Hosts and Producers can't edit a Show page.  What do I do?

The only Hosts and Producers that can edit a show are the ones listed as being Hosts or Producers for that Show in the respective user selection menus. This is to prevent Hosts/Producers from editing other Host/Producer's Shows without permission.

#### I don't want to use Gravatar for my Host/Producer's image on their profile page.

Then you'll need to install a plugin that lets you add a different image to your Host/Producer's user account. As there are a number of plugins that do just this, it's a little out of the scope of this plugin. However, in the [Pro version](https://radiostation.pro) you can create separate profile pages to showcase each of your Hosts and Producers, and assign profile images to these profile pages.

#### What languages other than English is the plugin available in?

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

#### Can the plugin be translated into my language?

You may translate the plugin into another language. Please visit our [WordPress Translate project page](https://translate.wordpress.org/projects/wp-plugins/radio-station/) for this plugin for further instruction. The `radio-station.pot` file is located in the `/languages` directory of the plugin. Please send the finished translation to `info@netmix.com`. We'd love to include it.

#### Can I use this plugin for Podcasts?

While the plugin is not specifically geared toward Podcasting, which is not live programming, some podcaster's have used Radio Station to let their subscribers know when they publish new shows.

#### Can I use this plugin for TwitchTV, Facebook Live, or Clubhouse shows?

Sure, there's no reason why you couldn't use the plugin to display a show schedule on a WordPress site for those services. Unfortunately, we are not currently syncing events from these platforms, but may do so in the future. While there may be APIs available from the larger services, Clubhouse does not yet have a public API, so scheduled rooms can't be automated to the Radio Station show scheduling system.

#### I use Google Calendar to print a show schedule online. Can I import/sync my Google Calendar with Radio Station?

We haven't built an interface between Google Calendar and Radio Station just yet, but it's on our radar to do so in the foreseeable future.

#### How do I install the latest Development version for testing?

If you are having issues with the plugin, we may recommend you install the development version for further bugix testing, as it may contain fixes that are not yet released into the next stable WordPress version. It is recommended you do this on a staging site. Instructions:

1. Download the `develop` branch zip from the Github repository at: 
`https://github.com/netmix/radio-station/tree/develop/`
2. Unzip the downloaded file on your computer and upload it via FTP to the subdirectory of your WordPress install on your web server: `/wp-content/plugins/radio-station-dev/`
3. Rename the subdirectory `/wp-content/plugins/radio-station/` to `/wp-content/plugins/radio-station-old`
4. Rename the subdirectory `/wp-content/plugins/radio-station-dev/` to `/wp-content/plugins/radio-station/`

You can now visit your site to make sure nothing is broken. If you experience issues you can reverse the folder renaming process to activate the old copy of the plugin. If the new development version works fine, at your convenience you can delete the `/wp-content/plugins/radio-station-old/` directory.




## Changelog

[View Full Changelog](./CHANGELOG.md)


## Upgrade Notices

#### 2.4.0
* Radio Station Stream Player Widget!
* https://netmix.com/radio-station-2-4-0-release-with-stream-player/

#### 2.3.3.9
* Multiple Dates and Times for Schedule Overrides!
* https://netmix.com/radio-station-2-3-3-9-release-announcement/
* Link Override to Show with Selective Fields
* Automatic Visitor Showtime Conversion and Display
* Language Archive Shortcode for Shows
* Various Bugfixes and Improvements
* Updated Freemius SDK and Plugin Loader

#### 2.3.3.8
* Updated Plugin Panel Library
* Added Stream Format selection setting
* Added Station email address setting with default display option
* Added Section order filtering for Master Schedules and Widgets
* Added Show image alignment attribute to Schedule Tabs View

#### 2.3.3.7
* Updated Freemius SDK and Plugin Loader libraries
* Added Station phone number setting with default display option
* Added Schedule classes for Shows before and after current Show
* Multiple Related Show Post assignment edit and link fixes
* Bugfixes for permissions, main language and shift checker

#### 2.3.3.6
* Updated Freemius SDK and Plugin Loader libraries
* Added Station phone number setting with default display option
* Added Schedule classes for Shows before and after current Show
* Multiple Related Show Post assignment edit and link fixes
* Bugfixes for permissions, main language and shift checker

#### 2.3.3.5
* Ability to assign Post to relate to multiple Shows
* Added Admin Filtering, Bulk Edit and Quick Edit interfaces
* Fixes for Schedule display left/right shifting on mobiles
* Fixes for starting Schedule display on different day

#### 2.3.3.3
* Current and Upcoming Shows Widget Fix

#### 2.3.3.2
* Minor Bugfix Update

#### 2.3.3 =
* Important Bugfix Update
* Fix to conflict with plugins using AJAX save_post calls
* Improved accuracy for responsive table/tab Schedule views
* Added colour improvements to Show Shift interface
* Fix to calculate Current Show (transient no longer used)

#### 2.3.2 =
* Improved Times, AJAX Loading and Bugfix Update 
* https://netmix.com/radio-station-2-3-2-release/
* Radio Clock Widget and Widget AJAX Loading
* AJAX Saving of Show Shifts and Playlist Tracks
* Automated current Show schedule highlighting
* Improved timezones, overrides, shift checking and more

#### 2.3.1 =
* Bugfix Update and Announcing New Netmix Station Directory!
* https://netmix.com/announcing-new-netmix-directory/
* Including minor fixes to major update release
* Option to ping Netmix Directory on show updates

#### 2.3.0
* Major Update including many new features, enhancements and fixes!
* https://netmix.com/radio-station-2-3-0-release/
* Revamped Templates, Master Schedule Views, Shortcodes and Widgets
* Added Admin Options, REST API Routes and Shift Conflict Checking
* Added Show Producers, Language Taxonomy, Timezones and Countdowns
* Improved User Roles, Show Images, Post Type Supports + much more!

#### 2.2.8
* Stable version before major update, including many fixes from 2.2.0 onwards 
* Fix to remove strict type checking (introduced 2.2.6) which fixes DJ can't edit Show

#### 2.2.0
* WordPress coding standards refactoring for WP 5 (thanks to Tony Hayes @majick777)

#### 2.1.2
* Compatibility fix for Wordpress 4.3.x - Updated the widgets to use PHP5 constructors instead of the deprecated PHP4 constructors.

#### 2.1
* General code cleanup, 4.1 compatibility testing, and changes for better efficiency.

#### 2.0.0
* Major code reorganization for better future development
