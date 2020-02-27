# Radio Station
Contributors: tonyzeoli, majick

Donate link: https://www.patreon.com/radiostation

Tags: dj, music, playlist, radio, shows, scheduling, broadcasting

Requires at least: 3.3.1

Tested up to: 5.2.2

Stable tag: trunk

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

Radio Station is a plugin to build and manage a Show Calendar in a radio station or Internet broadcaster's WordPress website. Functionality is based on Drupal 6's Station plugin.

## Description

Radio Station is a plugin to build and manage a Show Calendar in a radio station or Internet broadcaster's WordPress website. It's functionality is based on Drupal 6's Station plugin, reworked for use in Wordpress.

The plugin includes the ability to associate users (as member role "DJ") with the included custom post type of "Shows" (schedulable blocks of time that contain a Show description, and other meta information), and generate playlists associated with those shows.

The plugin contains a widget to display the currently on-air DJ with a link to the DJ's Show page and current playlist.  A schedule of all Shows can also be generated and added to a page with a short code. Shows can be categorized and a category filter appears when the Calendar is added using a short code to a WordPress page or post.

Posts can be assigned to Shows by creating a Post and using the meta-box to assign it to a specific Show. They will appear below the Show Description and Playlist on a Show page. Please use this function as your archive for each show, so that each Show archive has it's own URL and can be crawled for SEO purposes. Use your favorite SEO plugin to manage SEO on Show pages and Posts. We prefer [All in One SEO Pack](https://wordpress.org/plugins/all-in-one-seo-pack/).

We are grateful to [Nikki Blight] (https://profiles.wordpress.org/kionae/) for her contribution to creating and developing this plugin for as long as she could maintain the codebase. As of June 22, 2019, Radio Station is managed by [Tony Zeoli](https://profiles.wordpress.org/tonyzeoli/) and developed by contributing committers to the project overseen by Lead Developer, [Tony Hayes] (https://profiles.wordpress.org/majick/)

We are actively seeking radio station partners and donations to fund further development of the free, open source version of this plugin at: [https://www.patreon.com/radiostation](https://www.patreon.com/radiostation).

You can find a demo version of the plugin on our demo site here: [https://radiostationdemo.com](https://radiostationdemo.com).

## Plugin Support

For plugin support, please give 24-48 hours to answer support questions, which will be handled in the Wordpress Support Forums for this free version of the plugin here: [https://wordpress.org/support/plugin/radio-station](https://wordpress.org/support/plugin/radio-station/).

## Development

You can find a demo version of the plugin on our demo site [here](https://radiostationdemo.com).

## Installation

1. Upload plugin .zip file to the `/wp-content/plugins/` directory and unzip.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Give any users who need access to the plugin the role of "DJ".  Only DJ and administrator roles have administrative access.
4. Create Shows and set up Shifts.
5. [Optional] Add Playlists to your Shows.


## Frequently Asked Questions

### I've scheduled all my shows, but they're not showing up on the programming grid! 

Did you remember to check the "Active" checkbox for each show?  If a show is not marked active, the plugin assumes that it's not currently in production and hides it on the grid.

### I'm seeing 404 Not Found errors when I click on the link for a show! 
Try re-saving your site's permalink settings.  Wordpress sometimes gets confused with a new custom post type is added.

### How do I schedule a show? 

Simply create a new show.  You will be able to assign it to any timeslot you wish on the edit page.

### What if I have a special event?

If you have a one-off event that you need to show up in the On-Air or Coming Up Next widgets, you can create a Schedule Override by clicking the Schedule Override tab
in the Dashboard menu.  This will allow you to set aside a block of time on a specific date, and will display the title you give it in the widgets.  Please note that 
this will only override the widgets and their corresponding shortcodes.  If you are using the weekly master schedule shortcode on a page, its output will not be altered.



### Can I change how show pages are laid out/displayed? 
Yes.  Copy the `radio-station/templates/single-show.php` file into your theme directory, and alter as you wish.  This template, and all of the other templates
in this plugin, are based on the TwentyEleven theme.  If you're using a different theme, you may have to rework them to reflect your theme's layout.

### What about playlist pages? 

Same deal.  Grab the radio-station/templates/single-playlist.php file, copy it to your theme directory, and go to town.

### And playlist archive pages?  

Same deal.  Grab the radio-station/templates/archive-playlist.php file, copy it to your theme directory, and go to town.

### And the program schedule, too? 

### What if I want to style the DJ on air sidebar widget? 

Copy the radio-station/templates/djonair.css file to your theme directory.

### How do I get an archive page that lists ALL of the playlists instead of just the archives of individual shows? 

First, grab the radio-station/templates/playlist-archive-template.php file, and copy it to your active theme directory.  Then, create a Page in wordpress
to hold the playlist archive.  Under Page Attributes, set the template to Playlist Archive.  Please note: If you don't copy the template file to your theme first, 
the option to select it will not appear.


### Can show pages link to an archive of related blog posts? 

Yes, in much the same way as the full playlist archive described above. First, grab the radio-station/templates/show-blog-archive-template.php file, and copy it to 
your active theme directory.  Then, create a Page in wordpress to hold the blog archive.  Under Page Attributes, set the template to Show Blog Archive.


### I need users other than just the Administrator and DJ roles to have access to the Shows and Playlists post types.  How do I do that? 

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

### How do I change the DJ's avatar in the sidebar widget? 

The avatar is whatever image is assigned as the Show's featured image.  All you have to do is set a new featured image.

### Why don't any users show up in the DJs list on the Show edit page? 

You did remember to assign the DJ role to the users you want to be DJs, right?

### My DJs can't edit a show page.  What do I do? 

The only DJs that can edit a show are the ones listed as being ON that show in the DJs select menu.  This is to prevent DJs from editing other DJs shows 
without permission.

### How can I export a list of songs played on a given date? 

Under the Playlists menu in the dashboard is an Export link.  Simply specify the a date range, and a text file will be generated for you.

### Can my DJ's have customized user pages in addition to Show pages? 

Yes. These pages are the same as any other author page (edit or create the author.php template file in your theme directory).  A sample can be found 
in the radio-station/templates/author.php file (please note that this file doesn't actually do anything unless you copy it over to your theme's
directory).  Like the other theme templates included with this plugin, this file is based on the TwentyEleven theme and may need to be modified in
order to work with your theme.

### I don't want to use Gravatar for my DJ's image on their profile page. 

Then you'll need to install a plugin that lets you add a different image to your DJ's user account and edit your author.php theme file accordingly.  That's a 
little out of the scope of this plugin.  I recommend Cimy User Extra Fields:  http://wordpress.org/extend/plugins/cimy-user-extra-fields/

### What languages other than English is the plugin available in? 

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

### Can the plugin be translated into my language? 

You may translate the plugin into another language. Please visit our WordPress Translate project page for this plugin for further instruction: <a target="_top" href="https://translate.wordpress.org/locale/en-gb/default/wp-plugins/radio-station/">https://translate.wordpress.org/locale/en-gb/default/wp-plugins/radio-station/</a> The radio-station.pot file is located in the /languages directory of the plugin. Please send the finished translation to info@netmix.com. We'd love to include it.

## [View Full Changelog](./CHANGELOG.md)


## Upgrade Notices

## 2.3.0
* First Major Update including many new features, standards and fixes!

## 2.2.8
* Stable version before major update, including many fixes from 2.2.0 onwards 
* Fix to remove strict type checking (introduced 2.2.6) which fixes DJ can't edit Show

## 2.2.0
* WordPress coding standards refactoring for WP 5 (thanks to Tony Hayes @majick777)

## 2.1.2
* Compatibility fix for Wordpress 4.3.x - Updated the widgets to use PHP5 constructors instead of the deprecated PHP4 constructors.

## 2.1
* General code cleanup, 4.1 compatibility testing, and changes for better efficiency.

## 2.0.0
* Major code reorganization for better future development
