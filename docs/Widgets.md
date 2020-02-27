# Radio Station Plugin Widgets

***

You can add any of the Radio Station Plugin Widgets to your site's sidebar widget areas via the WordPress Appearance -> Widgets screen. 

Note Widgets are displayed via their corresponding Shortcodes to prevent code duplication and maintain display consistency. (The selected Widget options are simply converted into Shortcode attributes.) This also means if you want to display a Widget within a custom Template, you can use the corresponding shortcode in your template file. eg. `do_shortcode('[current-show]');`

[Radio Station Demo Site](http://radiostationdemo.com)


## Current Show Widget
Displays the currently playing Show - if there is one scheduled to play right now.

### Current Show Shortcode
`[current-show]` (previously `[dj-widget]`)

The following attributes are available for this shortcode:

* 'title' => The title you would like to appear over the Current Show block 
* 'display_hosts' => Display the names of the Hosts/DJs on the show.  Valid values are 0 for hide names, 1 for show names.  Default is 0.
* 'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.
* 'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.
* 'default_name' => The text you would like to display when no show is scheduled for the current time.
* 'time' => The time format used for displaying schedules.  Valid values are 12 and 24. Default is global Plugin Setting.
* 'show_sched' => Display the Show's schedule. Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.
* 'show_playlist' => Display a link to the show's current playlist.  Valid values are 0 for hide link, 1 for show link.  Default is 1.
* 'show_all_sched' => Displays all schedules for a show if it airs on multiple days.  Valid values are 0 for current schedule, 1 for all schedules.  Default is 0.
* 'show_desc' => Displays the first xx words of the show's description. Valid values are 0 for hide descripion, 1 for show description. Default is 0.


* 'countdown' => Display the Countdown until the current Show ends. Valid values are 0 for hide countdown, 1 for show countdown. Default is 0.


'default_name'   => '',

'avatar_width'   => '',
'title_position' => 'right',
'link_djs'       => 0,
'countdown'      => 0,
 
Example:
`[current-show title="Now On-Air" display_djs="1" show_avatar="1" show_link="1" default_name="RadioBot" time="12" show_sched="1" show_playlist="1"]`


## Upcoming Shows Widget
Displays a limited list of Upcoming Shows - if there are Shows scheduled.


### Upcoming Shows Shortcode
`[upcoming-shows]` (previously `[dj-coming-up-widget]`)

The following attributes are available for this shortcode:

* 'title' => The title you would like to appear over the Upcoming Shows block 
* 'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.
* 'display_djs' => Display the names of the DJs on the show.  Valid values are 0 for hide names, 1 for show names.  Default is 0.
* 'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.
* 'limit' => The number of upcoming shows to display.  Default is 1.
* 'time' => The time format used for displaying schedules.  Valid values are 12 and 24. Default is global Plugin Setting.
* 'show_sched' => Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.
* 'default_name' => The text you would like to display when no show is scheduled for the current time. Default is none.



* 'countdown' => Display the Countdown until the next Show. Valid values are 0 for hide countdown, 1 for show countdown. Default is 0.

Example:
`[upcoming-shows title="Coming Up On-Air" display_djs="1" show_avatar="1" show_link="1" limit="3" time="12" schow_sched="1"]`

'default_name'      => '',

'display_hosts'     => 0,
'avatar_width'      => '',
'title_position'    => 'right',
'link_djs'          => 0,
'countdown'         => 0,



## Current Playlist Widget
Displays the Playlist assigned to the currently playing Show - if there is one assigned to it.

### Current Playlist Shortcode
`[current-playlist]` (previously `[now-playing]`)

The following attributes are available for this shortcode:

* 'title' => The title you would like to appear over the Playlist display block
* 'artist' => Display artist name.  Valid values are 0 for hide, 1 for show.  Default is 1.
* 'song' => Display song name.  Valid values are 0 for hide, 1 for show.  Default is 1.
* 'album' => Display album name.  Valid values are 0 for hide, 1 for show.  Default is 0.
* 'label' => Display label name.  Valid values are 0 for hide, 1 for show.  Default is 0.
* 'comments' => Display DJ comments.  Valid values are 0 for hide, 1 for show.  Default is 0.
* 'countdown' => Display the Playlist remaining time Countdown. Default is 0.

Example:
`[current-playlist title="Current Song" artist="1" song="1" album="1" label="1" comments="0"]`


## Radio Clock Widget
A future version of Radio Station will include a Radio Clock Widget which will display the current Radio Station time in your selected Radio Station Timezone (via the Plugin Settings page.)

### [Pro] Timezone Switcher
A future version of Radio Station Pro will include a user Timezone Switcher which will allow your listener's to select a timezone and display adjusted Schedule and Show times.

## Streaming Player Widget
A future version of Radio Station will include a Streaming Player Widget.

### [Pro] Sitewide Player
A future version of Radio Station Pro will include a Sitewide Streaming Player

