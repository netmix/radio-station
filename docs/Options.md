# Radio Station Plugin Options

***

Plugin Settings are stored in an array under the `radio_station` key in the WordPress options table.

Below is a list of plugin options available via the Plugin Settings Screen.


### Plugin Setting Value Filters

Note for custom flexibility, all Plugin Settings can also be filtered programmatically via their respective option key. Use `add_filter` to add a filter to `radio_station_{settings_key}`, then check your desired conditions to modify the value before returning it. eg: 

```
add_filter( 'radio_station_station_phone', 'my_custom_station_phone' );
function my_custom_station_phone( $number ) {
	$current_hour = (int) date( 'G', time() );
	if ( $current_hour > 20 ) {$number = '(123) 456 7890';}
	return $number;
}
```

The above example will change the display of the Station Phone number after 8pm (server time).


## General 

### Broadcast

#### Streaming URL
Default: None. Key: streaming_format
Enter the Streaming URL for your Radio Station. This will be discoverable via Data Feeds and used by default by the Radio Player.

#### Stream Format
Default: AAC/M4A. Key: streaming_format
Select the format for your stream. This will be discoverable via Data Feeds and used by default by the Radio Player.

#### Fallback URL
Default: None. Key: fallback_url
Enter the fallback Streaming URL for your Radio Station. This will be discoverable via Data Feeds and used by default by the Radio Player.

#### Streaming URL
Default: OGG. Key: fallback_format
Select the format for your fallback stream. This will be discoverable via Data Feeds and used by default by the Radio Player.

#### Main Broadcast Language
Default: WordPress Language. Key: radio_language
Select the main language used on your Radio Station.


### Station

#### Station Title
Default: none. Key: station_title

#### Station Image
Default: none. Key: station_image

#### Location Timezone
Default: WordPress Timezone. Key: timezone_location
Select your Broadcast Location for Radio Timezone display.

#### Clock Time Format
Default: 12 Hour Format. Key: clock_time_format
Default Time Format for display output. Can be overridden in each shortcode or widget.

#### Station Phone
Default: none. Key: station_phone
Default Phone Number to use for requests etc.

#### Shows Phone
Default: On. Key: shows_phone
Use the Station Phone Number on Shows which do not have a phone number specified.

#### Station Email
Default: none. Key: station_email
Default Email Address to use for requests etc.

#### Shows Email
Default: On. Key: shows_email
Use the Station Email Address on Shows which do not have an email address specified.


### Feeds

#### Enable Data Routes
Default: On. Key: enable_data_routes
Enables Station Data Routes via WordPress REST API.

#### Enable Data Feeds
Default: On. Key: enable_data_feeds
Enable Station Data Feeds via WordPress Feed links.

#### Ping Netmix Directory
Default: On. Key: ping_netmix_directory

#### Clear Transients
Default: Off. Key: clear_transients

#### [Pro] Transient Caching
Default: On. transient_caching
Use Transient Caching to improve Schedule calculation performance.


## Player

### Basic Defaults

#### Player Title
Default: on. Key: player_title
Display your Radio Station Title in Player by default.

#### Player Image
Default: on. Key: player_image
Display your Radio Station Image in Player by default.

#### Player Script
Default: amplitude. Key: player_script
Default audio script to use for Radio Streaming Player. Ampliture, Howler and Jplayer.

#### Player Theme
Default: light. Key: player_theme
Default Player Controls theme style. Light or dark to match your theme.

#### Player Buttons
Default: rounded. Key: player_buttons
Default Player Buttons shape style. Circular, rounded or square.

#### Player Volume Controls
Default: all. Key: player_volumes
Which volume controls to display in the Player by default.

#### Player Debug Mode
Default: off. Key: player_debug
Output player debug information in browser javascript console.

### Player Colors

#### [Pro] Playing Highlight Color
Default: #70E070. Key: player_playing_color
Default highlight color to use for Play button icon when playing.

#### [Pro] Controls Highlight Color
Default: #00A0E0. Key: player_buttons_color
Default highlight color to use for player Control button icons when active.

#### [Pro] Volume Knob Color
Default: #80C080. Key: player_thumb_color
Default Knob Color for Player Volume Slider.

#### [Pro] Volume Track Color
Default: #80C080. Key: player_range_color
Default Track Color for Player Volume Slider.

### Advanced Defaults

#### Player Start Volume
Default: 77. Key: player_volume
Initial volume for when the Player starts playback. 0-100

#### Single Player
Default: on. Key: player_single
Stop any existing Player instances on the page or in other windows or tabs when a Player is started.

#### [Pro] Player Autoresume
Default: on. Key: player_autoresume
Attempt to resume playback if visitor was playing. Only triggered when the user first interacts with the page.

#### [Pro] Player Popup
Default: off. Key: player_popup
Add button to open Popup Player in separate window.

### Player Bar

#### [Pro] Sitewide Player Bar
Default: off. Key: player_bar
Add a fixed position Player Bar which displays Sitewide. Fixed top or bottom position.

#### [Pro] Fade In Player Bar
Default: 2500. Key: player_bar_fadein
Number of milliseconds after Page load over which to fade in Player Bar. Use 0 for instant display

#### [Pro] Continuous Playback
Default: on. Key: player_bar_continuous
Uninterrupted Sitewide Bar playback while user is navigating between pages! Pages are loaded in background and faded in while Player Bar persists.

#### [Pro] Player Page Fade
Default: 2000. Key: player_bar_pagefade
Number of milliseconds over which to fade in new Pages when continuous playback is enabled. Use 0 for instant display.

#### [Pro] Bar Player Text Color
Default: #FFFFFF. Key: player_bar_text
Text color for the fixed position Sitewide Bar Player.

#### [Pro] Bar Player Background Color
Default: black. Key: player_bar_background
Background color for the fixed position Sitewide Bar Player.

#### [Pro] Bar Player Current Show
Default: on. Key: player_bar_currentshow
Display the Current Show in the Player Bar.

#### [Pro] Bar Player Now Playing
Default: on. Key: player_bar_nowplaying
Display the Now Playing Track metadata in the Player Bar.

#### [Pro] Bar Player Track Animation
Default: backandforth. Key: player_bar_track_animation
How to animate the currently playing track display.

#### [Pro] Bar Player Metadata Source
Default: none (use Stream URL.) Key: player_bar_metadata
Alternative metadata source URL for Now Playing Track metadata.

#### [Pro] Fetch Album Artwork?
Default: on. Key: player_bar_artwork
Look up and display album artwork for the current track via the iTunes Search API (and Spotify as a fallback, if configured below.) Useful for streams like Live365 that do not include artwork in their metadata. See [Track Metadata](./Player.md#pro-track-metadata) for more info.

#### [Pro] Spotify Client ID
Default: none. Key: player_bar_artwork_spotify_id
Optional fallback artwork source used when iTunes has no match for a track. Requires a free Spotify Developer account - see [Track Metadata](./Player.md#pro-track-metadata) for setup instructions.

#### [Pro] Spotify Client Secret
Default: none. Key: player_bar_artwork_spotify_secret
Used together with the Spotify Client ID above.


## Pages

### Master Schedule

#### Master Schedule Page
Default: None. Key: schedule_page
Select the Page you are displaying the Master Schedule on.

#### Automatic Schedule Display
Default: On. Key: schedule_auto
Replaces selected page content with Master Schedule.  
Alternatively customize with the shortcode: `[master-schedule]`
See [Master Schedule Shortcode](./Shortcodes.md#master-schedule) for more info.

#### Schedule View Default
Default: Table. Key: schedule_view
View type to use for automatic display on Master Schedule Page.

#### Radio Clock
Default: On. Key: schedule_clock
Whether to enable the display of the Radio/User Times clock on the automatic Master Schedule Page.

#### [Pro] Schedule View Switcher
Default: On. Key: schedule_switcher
Enable View Switching on the automatic Master Schedule Page.

#### [Pro] Available Views
Default: table, tabs. Key: schedule_views
Which Views to enable for switching on the automatic Master Schedule Page.


### Show Pages

#### Show Info Blocks Position
Default: Left. Key: show_block_position
Where to position Show info blocks relative to Show Page content.

#### Show Content Layout
Default: Tabbed. Key: show_section_layout
How to display extra sections below Show description. In content tabs or standard layout down the page.

#### Show Content Header Image
Default: Off. Key: show_header_image
If your chosen template does not display the Featured Image, enable this and use the Content Header Image box on the Show edit screen instead.

#### Show Posts Per Page
Default: 10. Key: show_posts_per_page
Linked Show Posts per page on the Show Page tab/display.

#### Show Playlists per Page
Default: 10. Key: show_playlists_per_page
Playlists per page on the Show Page tab/display.

#### [Pro] Show Episodes per Page
Default: 10. Key: show_episodes_per_page
Number of Show Episodes per page on the Show page tab/display.


### Archives

#### Show Archives Page
Default: None. Key: show_archive_page
Select the Page for displaying the Show archive list.

#### Show Archives Automatic Display
Default: On. Key: show_archive_auto
Replaces selected page content with default Show Archive.  
Alternatively customize display using the shortcode: `[shows-archive]`
See [Show Archives Shortcode](./Shortcodes.md#show-archives-shortcode) for more info.  

#### Override Archives Page
Default: None. Key: override_archive_page
Select the Page for displaying the Override archive list.

#### Override Archives Automatic Display
Default: On. Key: override_archive_auto
Replaces selected page content with default Override Archive.  
Alternatively customize display using the shortcode: `[overrides-archive]`
See [Show Archives Shortcode](./Shortcodes.md#overrides-archives-shortcode) for more info.  

#### Playlist Archives Page
Default: None. Key: playlist_archive_page
Select the Page for displaying the Playlist archive list.

#### Playlist Archives Automatic Display
Default: On. Key: playlist_archive_auto
Replaces selected page content with default Playlist Archive.  
Alternatively customize display using the shortcode: `[playlists-archive]`
See [Playlist Archives Shortcode](./Shortcodes.md#playlist-archives-shortcode) for more info.  

#### [Pro] Team Archives Page
Default: None. Key: team_archive_page
Replaces selected page content with default Team Archive (Hosts and Producers.)
Alternatively customize display using the shortcode: `[team-archive]`

#### Genre Archives Page
Default: None. Key: genre_archive_page
Select the Page for displaying the Genre archive list.

#### Genre Archives Automatic Display
Default: On. Key: genre_archive_auto
Replaces selected page content with default Genre Archive.  
Alternatively customize display using the shortcode: `[genres-archive]`
See [Genre Archives Shortcode](./Shortcodes.md#genre-archives-shortcode) for more info.


## Templates

Since 2.3.0, the way that Templates are implemented has changed.
See [Templates](./Display.md#page-templates) for more info.

### Single Templates

#### Show Template
Default: page.php. Key: show_template
Which template to use for displaying Show and Override content.

#### Combines Show Template Method
Default: Off. Key: show_template_combined
For advanced usage. Use both a custom template AND content filtering for a Show. (Not compatible with Legacy templates.)

#### Playlist Template
Default: page.php. Key: playlist_template
Which template to use for displaying Playlist content.

#### Combined Playlist Template Method
Default: Off. Key: playlist_template_combined
For advanced usage. Use both a custom template AND content filtering for a Playlist. (Not compatible with Legacy templates.)

## Widgets

#### AJAX Loading
Default: On. Key: ajax_widgets
Whether to load Widget contents via AJAX by default. This prevents stale cached displays of Current/Upcoming Shows etc. Note this can be changed on a per widget basis.

#### [Pro] Dynamic Reloading
Default: On. Key: dynamic_reload
Whether to reload Widgets automatically on change of Current Show. Can also be set on a per widget basis.

#### [Pro] Convert Show Times
Default: On. Key: convert_show_times
Automatically display Show times converted into the visitor timezone, based on their browser setting.

#### [Pro] User Timezone Switching
Default: On. Key: timezone_switching
Allow visitors to select their Timezone manually for Show time conversions.


## Roles

Since 2.3.0, a new Show Editor role has been added with Publish and Edit capabilities for all Radio Station Post Types. You can assign this Role to any user to give them complete Station Schedule and Radio Station Post Type updating permissions without giving them a full WordPress administrator role.
See [Roles](./Roles.md#show-editor-role] for more info.

### Permissions

#### Add to Author Role Capabilities
Default: On. Key: add_author_capabilities
Allow users with the WordPress Author role to publish and edit their own Shows and Playlists.

#### Add to Editor Role Capabilities
Default: On. Key: add_editor_capabilities
Allow users with the WordPress Editor role to edit all Radio Station post types.


### Role Editing

#### [Pro] Role Editor Interface
Allows you to assign any of the Radio Station plugin Roles directly to any user. For more information see [Roles Documentation](./Roles.md#role-editing)

