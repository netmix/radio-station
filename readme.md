## Radio Station

Radio Station is a plugin to build and manage a Show Calendar in a radio station or Internet broadcaster's WordPress website. Functionality is based on Drupal 6's Station plugin.

### Description
Radio Station is a plugin to build and manage a Show Calendar in a radio station or Internet broadcaster's WordPress website. It's functionality is based on Drupal 6's Station plugin, reworked for use in Wordpress.

The plugin includes the ability to associate users (as member role "DJ"") with the included custom post type of "Shows" (schedulable blocks of time that contain a Show description, and other meta information), and generate playlists associated with those shows.

The plugin contains a widget to display the currently on-air DJ with a link to the DJ's Show page and current playlist.  A schedule of all Shows can also be generated and added to a page with a short code. Shows can be categorized and a category filter appears when the Calendar is added using a short code to a WordPress page or post.

We are grateful to Nikki Blight for her contribution to creating and developing this plugin for as long as she could maintain the codebase. As of June 22, 2019, Radio Station is managed by [Tony Zeoli](https://profiles.wordpress.org/tonyzeoli/)  and developed by contributing committers to the project.

If you are a WordPress developer wanting to contribute to Radio Station, please join the team and follow plugin development on Github: [https://github.com/netmix/radio-station](https://github.com/netmix/radio-station).

Submit bugs and feature requests here: [https://github.com/netmix/radio-station/issues](https://github.com/netmix/radio-station/issues).

We are actively seeking radio station partners and donations to fund further development of the free, open source version of this plugin at  [https://www.patreon.com/radiostation](https://www.patreon.com/radiostation).

For plugin support, please give 24-48 hours to answer support questions, which will be handled in the Wordpress Support Forums for this free version of the plugin [here](https://wordpress.org/support/plugin/radio-station/).

You can find a demo version of the plugin on our demo site [here](https://radiostationdemo.com).

## Installation

1. Upload plugin .zip file to the `/wp-content/plugins/` directory and unzip.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Give any users who need access to the plugin the role of "DJ".  Only DJ and administrator roles have administrative access.
4. Create shows and set up shifts.
5. Add playlists to your shows.

### Frequently Asked Questions 

### I've scheduled all my shows, but they're not showing up on the programming grid! 

Did you remember to check the "Active" checkbox for each show?  If a show is not marked active, the plugin assumes that it's not currently in production and hides it on the grid.

### I'm seeing 404 Not Found errors when I click on the link for a show! 
Try re-saving your site's permalink settings.  Wordpress sometimes gets confused with a new custom post type is added.

### How do I display a full schedule of my station's shows? 
Use the shortcode `[master-schedule]` on any page.  This will generate a full-page schedule in one of three formats.

The following attributes are available for the shortcode:
* 'list' => If set to a value of 'list', the schedule will display in list format rather than table or div format. Valid values are 'list', 'divs', 'table'.  Default value is 'table'.
* 'time' => The time format you with to use.  Valid values are 12 and 24.  Default is 12.
* 'show_link' => Display the title of the show as a link to its profile page.  Valid values are 0 for hide, 1 for show.  Default is 1.
* 'display_show_time' => Display start and end times of each show after the title in the grid.  Valid values are 0 for hide, 1 for show.  Default is 1.
* 'show_image' => If set to a value of 1, the show's avatar will be displayed.  Default value is 0.
* 'show_djs' => If set to a value of 1, the names of the show's DJs will be displayed.  Default value is 0.
* 'divheight' => Set the height, in pixels, of the individual divs in the 'divs' layout.  Default is 45.
* 'single_day' => Display schedule for only a single day of the week.  Only works if you are using the 'list' format.  Valid values are sunday, monday, tuesday, wednesday, thursday, friday, saturday.
* 
For example, if you wish to display the schedule in 24-hour time format, use `[master-schedule time="24"]`.  If you want to only show Sunday's schedule, use `[master-schedule list="list" single_day="sunday"]`.

### How do I schedule a show? 

Simply create a new show.  You will be able to assign it to any timeslot you wish on the edit page.

### What if I have a special event?

If you have a one-off event that you need to show up in the On-Air or Coming Up Next widgets, you can create a Schedule Override by clicking the Schedule Override tab
in the Dashboard menu.  This will allow you to set aside a block of time on a specific date, and will display the title you give it in the widgets.  Please note that 
this will only override the widgets and their corresponding shortcodes.  If you are using the weekly master schedule shortcode on a page, its output will not be altered.

### How do I get the last song played to show up? 

You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode `[now-playing]` in your page/post, or use `do_shortcode('[now-playing]');` in your template files.

The following attributes are available for the shortcode:
* 'title' => The title you would like to appear over the now playing block
* 'artist' => Display artist name.  Valid values are 0 for hide, 1 for show.  Default is 1.
* 'song' => Display song name.  Valid values are 0 for hide, 1 for show.  Default is 1.
* 'album' => Display album name.  Valid values are 0 for hide, 1 for show.  Default is 0.
* 'label' => Display label name.  Valid values are 0 for hide, 1 for show.  Default is 0.
* 'comments' => Display DJ comments.  Valid values are 0 for hide, 1 for show.  Default is 0.

Example:
`[now-playing title="Current Song" artist="1" song="1" album="1" label="1" comments="0"]`

### What about displaying the current DJ on air? 

You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode `[dj-widget]` in your page/post, or you can use
`do_shortcode('[dj-widget]');` in your template files.

The following attributes are available for the shortcode:
* 'title' => The title you would like to appear over the on-air block 
* 'display_djs' => Display the names of the DJs on the show.  Valid values are 0 for hide names, 1 for show names.  Default is 0.
* 'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.
* 'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.
* 'default_name' => The text you would like to display when no show is schedule for the current time.
* 'time' => The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.
* 'show_sched' => Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.
* 'show_playlist' => Display a link to the show's current playlist.  Valid values are 0 for hide link, 1 for show link.  Default is 1.
* 'show_all_sched' => Displays all schedules for a show if it airs on multiple days.  Valid values are 0 for current schedule, 1 for all schedules.  Default is 0.
* 'show_desc' => Displays the first 20 words of the show's description. Valid values are 0 for hide descripion, 1 for show description.  Default is 0.
* 
Example:
`[dj-widget title="Now On-Air" display_djs="1" show_avatar="1" show_link="1" default_name="RadioBot" time="12" show_sched="1" show_playlist="1"]`


### Can I display upcoming shows, too? 

You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode `[dj-coming-up-widget]` in your page/post, or you can use
`do_shortcode('[dj-coming-up-widget]');` in your template files.

The following attributes are available for the shortcode:
* 'title' => The title you would like to appear over the on-air block 
* 'display_djs' => Display the names of the DJs on the show.  Valid values are 0 for hide names, 1 for show names.  Default is 0.
* 'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.
* 'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.
* 'limit' => The number of upcoming shows to display.  Default is 1.
* 'time' => The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.
* 'show_sched' => Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.
* 
Example:
`[dj-coming-up-widget title="Coming Up On-Air" display_djs="1" show_avatar="1" show_link="1" limit="3" time="12" schow_sched="1"]`

### Can I change how show pages are laid out/displayed? 
Yes.  Copy the `radio-station/templates/single-show.php` file into your theme directory, and alter as you wish.  This template, and all of the other templates
in this plugin, are based on the TwentyEleven theme.  If you're using a different theme, you may have to rework them to reflect your theme's layout.

### What about playlist pages? 

Same deal.  Grab the radio-station/templates/single-playlist.php file, copy it to your theme directory, and go to town.

### And playlist archive pages?  

Same deal.  Grab the radio-station/templates/archive-playlist.php file, copy it to your theme directory, and go to town.

### And the program schedule, too? 

Because of the complexity of outputting the data, you can't directly alter the template, but you can copy the radio-station/templates/program-schedule.css file
into your theme directory and change the CSS rules for the page.

### What if I want to style the DJ on air sidebar widget? 

Copy the radio-station/templates/djonair.css file to your theme directory.

### How do I get an archive page that lists ALL of the playlists instead of just the archives of individual shows? 

First, grab the radio-station/templates/playlist-archive-template.php file, and copy it to your active theme directory.  Then, create a Page in wordpress
to hold the playlist archive.  Under Page Attributes, set the template to Playlist Archive.  Please note: If you don't copy the template file to your theme first, 
the option to select it will not appear.

### Can show pages link to an archive of related blog posts? 

Yes, in much the same way as the full playlist archive described above. First, grab the radio-station/templates/show-blog-archive-template.php file, and copy it to 
your active theme directory.  Then, create a Page in wordpress to hold the blog archive.  Under Page Attributes, set the template to Show Blog Archive.

### How can I list all of my shows? 

Use the shortcode `[list-shows]` in your page/posts or use `do_shortcode(['list-shows']);` in your template files.  This will output an unordered list element
containing the titles of and links to all shows marked as "Active". 

The following attributes are available for the shortcode:
* 'genre' => Displays shows only from the specified genre(s).  Separate multiple genres with a comma, e.g. genre="pop,rock".

Example:
`[list-shows genre="pop"]`
`[list-shows genre="pop,rock,metal"]`

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

The avatar is whatever image is assigned as the DJ/Show's featured image.  All you have to do is set a new featured image.

### Why don't any users show up in the DJs list on the Show edit page? 

You did remember to assign the DJ role to the users you want to be DJs, right?

### My DJs can't edit a show page.  What do I do? 

The only DJs that can edit a show are the ones listed as being ON that show in the DJs select menu.  This is to prevent DJs from editing other DJs shows 
without permission.

### How can I export a list of songs played on a given date? 

Under the Playlists menu in the dashboard is an Export link.  Simply specify the a date range, and a text file will be generated for you.

### Can my DJ's have customized user pages in addition to Show pages? 

Yes.  These pages are the same as any other author page (edit or create the author.php template file in your theme directory).  A sample can be found 
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
