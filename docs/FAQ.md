# Radio Station Plugin FAQ

***


### How do I schedule a Show? 

Simply create a new show via Add Show in the Radio Station plugin menu in the Admin area. You will be able to assign Shift timeslots to it on the Show edit page, as well as add the Show description and other meta fields, including Show images.

### How do I display a full schedule of my Station's shows? 

In the Plugin Settings, you can select a Page on which to automatically display the schedule as well as which View to display (a Table grid by default.) Alternatively, you can use the shortcode `[master-schedule]` on any page (or post.) This option allows you to use further shortcode attributes to control the what is displayed in the Schedule (see [Master Schedule Shortcode Docs](./Shortcodes.md#master-schedule-shortcode) )

### I've scheduled all my Shows, but some are not showing up on the programming grid?!

Did you remember to check the "Active" checkbox for each Show? If a Show is not marked active, the plugin assumes that it's not currently in production and it is not shown on the Schedule. A Show will also not be shown if it has no active Shifts assigned to it.

### What if I want to schedule a special event?

If you have a one-off event that you need to show up in the Schedule and Widgets, you can create a Schedule Override by clicking the Schedule Override tab in the Admin menu. This will allow you to set aside a block of time on a specific date, and when the Schedule or Widget is displaying that date, the override will be used instead of the normally scheduled Show. (Note that Schedule Overrides will not display in the old Legacy Table or List Views of the Master Schedule.)

### I'm seeing 404 Not Found errors when I click on the link for a Show! 

Try re-saving your site's permalink settings via Settings -> Permalinks.  Wordpress sometimes gets confused with a new custom post type is added. Permalink rewrites are automatically flushed on plugin activation, so you can also just deactivate and reactivate the plugin.

### What if I want to change or style the plugin's displays? 

The default styles for Radio Station have intionally kept fairly minimal so as to be compatible with most themes, so you may wish to add your own styles to suit your site's look and feel. The best way to do this is to add your own `rs-custom.css` to your Child Theme's directory, and add more specific style rules that modify or override the existing styles. Radio Station will automatically detect the presence of this file and enqueue it. You can find the base styles in the `/css/` directory of the plugin.

### What Widgets are available with this plugin?
The following Widgets are available to add via the WordPress Appearance -> Widgets page:
Current Show, Upcoming Shows, Current Playlist. Radio Clock and Streaming Player Widgets will also be available in future versions. See the [Widget Documentation](./Widgets.md) for more details on these Widgets.

### What Shortcodes are available with this plugin?
See the [Shortcode Documentation](./Shortcodes.md) for more details and a full list of possible Attributes for these Shortcodes:
`[master-schedule]` - Master Program Schedule Display
`[current-show]` - Current Show Widget
`[upcoming-shows]` - Upcoming Shows Widget
`[current-playlist]` - Current Playlist Widget
`[shows-archive]` - Archive List of Shows
`[genres-archive]` - Archive List of Shows sorted by Genre
`[overrides-archive]` - Archive List of Schedule overrides
`[playlists-archive]` - Archive List of Show Playlists
Note old shortcode aliases will still work in current and future versions to prevent breakage.


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

### How do I get an archive page that lists ALL of the playlists instead of just the archives of individual shows? 

First, grab the radio-station/templates/playlist-archive-template.php file, and copy it to your active theme directory.  Then, create a Page in wordpress
to hold the playlist archive.  Under Page Attributes, set the template to Playlist Archive.  Please note: If you don't copy the template file to your theme first, 
the option to select it will not appear.







### Can show pages link to an archive of related blog posts? 

Yes, in much the same way as the full playlist archive described above. First, grab the radio-station/templates/show-blog-archive-template.php file, and copy it to your active theme directory.  Then, create a Page in wordpress to hold the blog archive. Under Page Attributes, set the template to Show Blog Archive.





### How can I list all of my shows? 

Use the shortcode `[list-shows]` in your page/posts or use `do_shortcode(['list-shows']);` in your template files.  This will output an unordered list element
containing the titles of and links to all shows marked as "Active". 

The following attributes are available for the shortcode:
* 'genre' => Displays shows only from the specified genre(s).  Separate multiple genres with a comma, e.g. genre="pop,rock".

Example:
`[list-shows genre="pop"]`
`[list-shows genre="pop,rock,metal"]`

### I need users other than just the Administrator and DJ roles to have access to the Shows and Playlists post types.  How do I do that? 

Since I'm stongly opposed to reinventing the wheel, I recommend Justin Tadlock's excellent [Members plugin](http://wordpress.org/extend/plugins/members/) for that purpose. 

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

### How do I change the Show Avatar in the sidebar widget? 

The avatar is whatever image is assigned as the Show's Avatar.  All you have to do is set a new Sho Avatar on the Edit page for that Show.

### Why don't any users show up in the Hosts or Producers list on the Show edit page? 

You did remember to assign the Host or Producer role to the users you want, right?

### My Show Hosts and Producers can't edit a Show page.  What do I do? 

The only Hosts and Producers that can edit a show are the ones listed as being Hosts or Producers for that Show in the respective user selection menus. This is to prevent Hosts/Producers from editing other Host/Producer's Shows without permission.

### How can I export a list of songs played on a given date? 

Under the Playlists menu in the dashboard is an Export link. Simply specify the a date range, and a text file will be generated for you.

### Can my DJ's have customized user pages in addition to Show pages? 

Yes. These pages are the same as any other Author page (edit or create the author.php template file in your theme directory).  A sample can be found in the radio-station/templates/author.php file (please note that this file doesn't actually do anything unless you copy it over to your theme's directory).  

### I don't want to use Gravatar for my Host/Producer's image on their profile page. 

Then you'll need to install a plugin that lets you add a different image to your Host/Producer's user account and edit your author.php theme file accordingly.  That's a little out of the scope of this plugin. I recommend [Cimy User Extra Fields](http://wordpress.org/extend/plugins/cimy-user-extra-fields/)

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

You may translate the plugin into another language. Please visit our [WordPress Translate project page](https://translate.wordpress.org/locale/en-gb/default/wp-plugins/radio-station/) for this plugin for further instruction. The `radio-station.pot` file is located in the `/languages` directory of the plugin. Please send the finished translation to `info@netmix.com`. We'd love to include it.
