# Radio Station Plugin Shortcodes

***

Note if you want to display a Shortcode within a custom Template, you can use `do_shortcode`.
eg. `do_shortcode('[master-schedule]');`


[Radio Station Demo Site](http://radiostationdemo.com)


## Master Schedule

### Master Schedule Shortcode

Use the shortcode `[master-schedule]` on any page. This will generate a full-page schedule in one of five Views: Table, Tabbed, List, Divs and Legacy, with Table being the default. Note that Divs and Legacy do not honour Schedule Overrides and should be considered Deprecated but have been kept for backwards compatibility.

The following attributes are available for the shortcode:

* 'view' => Valid values are 'table', 'tabbed', 'list', 'divs', 'legacy'. Default value is 'table'.
* 'time' => The time format you with to use.  Valid values are 12 and 24. Default is the Plugin Setting.
* 'show_link' => Display the title of the show as a link to its profile page. Valid values are 0 for hide, 1 for show.  Default is 1.
* 'display_show_time' => Display start and end times of each show after the title in the grid. Valid values are 0 for hide, 1 for show.  Default is 1.
* 'show_image' => If set to a value of 1, the show's avatar will be displayed. Default value is 0.
* 'show_djs' => If set to a value of 1, the names of the show's DJs will be displayed.  Default value is 0.

* 'divheight' => Set the height, in pixels, of the individual divs in the 'divs' layout only. Default is 45.
* 'single_day' => Display schedule for only a single day of the week.  Only works if you are using the 'list' format.  

For example, if you wish to display the schedule in 24-hour time format, use `[master-schedule time="24"]`.  If you want to only show Sunday's schedule, use `[master-schedule list="list" single_day="sunday"]`.


## Archive Shortcodes

### Shows Archive Shortcode
`[shows-archive]` (or `[show-archive]`)

### Overrides Archive Shortcode
`[overrides-archive]` (or `[override-archive]`)

### Playlists Archive Shortcode
`[playlists-archive]` (or `[playlist-archive]`)

### Genres Archive Shortcode
`[genres-archive]` (or `[genre-archive]`)

### Language Archive Shortcode
`[languages-archive]` (or `[language-archive]`)
This shortcode will be available in a future version.

### Show Posts Archive Shortcode
`[show-posts-archive]` (or `[show-post-archive]`)

### Show Playlists Archive Shortcode
`[show-playlists-archive]` (or `[show-playlist-archive]`)


## Widget Shortcodes

### Current Show Widget Shortcode
see [Current Show Widget](./Widgets.md#current-show-widget)

### Upcoming Shows Widget Shortcode
see [Upcoming Shows Widget](./Widgets.md#upcoming-shows-widget)

### Current Playlist Widget Shortcode
see [Current Playlist Widget](./Widgets.md#current-playlist-widget)


### Legacy Shortcodes

#### Show List
`[show-list]`
This shortcode is considered Deprecated. Use the [Shows Archive Shortcode](#shows-archive-shortcode) instead: `[shows-archive]`

The following attributes are available for this shortcode:
* 'genre' => Displays shows only from the specified genre(s). Separate multiple genres with a comma, e.g. genre="pop,rock".

Examples: `[list-shows genre="pop"]`, `[list-shows genre="pop,rock,metal"]`

#### Show Playlists
This shortcode is considered Deprecated. Use the [Show Playlists Archive Shortcode](#show-playlists-archive-shortcode) instead: `[show-playlists-archive]`
`[show-playlists]`