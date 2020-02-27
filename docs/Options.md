# Radio Station Plugin Options

***

Plugin Settings are stored in an array under the `radio_station` option key.

Below is a list of plugin options available via the Plugin Settings Screen.


## General 

### Broadcast

#### Streaming URL
Default: None. Enter the Streaming URL for your Radio Station. This will be discoverable via Data Feeds and used in the upcoming Radio Player.

#### Main Broadcast Language
Default: WordPress Language. Select the main language used on your Radio Station.


### Times

#### Location Timezone
Default: WordPress Timezone. Select your Broadcast Location for Radio Timezone display.

#### Clock Time Format
Default: 12 Hour Format. Default Time Format for display output. Can be overridden in each shortcode or widget.


### Feeds

#### Enable Data Routes
Default: On. Enables Station Data Routes via WordPress REST API.


#### Enable Data Feeds
Default: On. Enable Station Data Feeds via WordPress Feed links.


#### [Pro] Show Shift Feeds
Default: On. Convert RSS Feeds for a single Show to a Show shift feed, allowing a visitor to subscribe to a Show feed to be notified of Show shifts.


#### [Pro] Transient Caching
Default: On. Use Transient Caching to improve Schedule calculation performance.



## Pages

### Master Schedule

#### Master Schedule Page
Default: None. Select the Page you are displaying the Master Schedule on.


#### Automatic Schedule Display
Default: Yes. Replaces selected page content with Master Schedule.  
Alternatively customize with the shortcode: `[master-schedule]`
See [Master Schedule Shortcode](./Shortcodes.md#master-schedule) for more info.

#### Schedule View Default
Default: Table. View type to use for automatic display on Master Schedule Page.

#### [Pro] Schedule View Switcher
Default: On. Enable View Switching on the Master Schedule.


### Show Pages

#### Show Info Blocks Position
Default: Left. Where to position Show info blocks relative to Show Page content.

#### Show Content Layout
Default: Tabbed. How to display extra sections below Show description. In content tabs or standard layout down the page.


#### Show Content Header Image
Default: Off. If your chosen template does not display the Featured Image, enable this and use the Content Header Image box on the Show edit screen instead.

#### Show Posts Per Page
Default: 10. Linked Show Posts per page on the Show Page tab/display.

#### Show Playlists per Page
Default: 10. Playlists per page on the Show Page tab/display.

#### [Pro] Show Episodes per Page
Default: 10. Number of Show Episodes per page on the Show page tab/display.


### Archives

#### Show Archives Page
Default: None. Select the Page for displaying the Show archive list.

#### Show Archives Automatic Display
Default: On. Replaces selected page content with default Show Archive.  
Alternatively customize display using the shortcode: `[shows-archive]`
See [Show Archives Shortcode](./Shortcodes.md#show-archives-shortcode) for more info.  

#### Playlist Archives Page
Default: None. Select the Page for displaying the Playlist archive list.

#### Playlist Archives Automatic Display
Default: On. Replaces selected page content with default Playlist Archive.  
Alternatively customize display using the shortcode: `[playlists-archive]`
See [Playlist Archives Shortcode](./Shortcodes.md#playlist-archives-shortcode) for more info.  

#### Genre Archives Page
Default: None. Select the Page for displaying the Genre archive list.

#### Genre Archives Automatic Display
Default: On. Replaces selected page content with default Genre Archive.  
Alternatively customize display using the shortcode: `[genres-archive]`
See [Genre Archives Shortcode](./Shortcodes.md#genre-archives-shortcode) for more info.


## Templates

Since 2.3.0, the way that Templates are implemented has changed.
See [Templates](./Display.md#page-templates) for more info.

### Single Templates

#### Show Template
Default: page.php. Which template to use for displaying Show content.

#### Combines Show Template Method
Default: Off. Advanced usage. Use both a custom template AND content filtering for a Show. (Not compatible with Legacy templates.)

#### Playlist Template
Default: page.php. Which template to use for displaying Playlist content.

#### Combined Playlist Template Method
Default: Off. Advanced usage. Use both a custom template AND content filtering for a Playlist. (Not compatible with Legacy templates.)


## Roles

Since 2.3.0, a new Show Editor role has been added with Publish and Edit capabilities for all Radio Station Post Types.
You can assign this Role to any user to give them full Station Schedule updating permissions.
See [Roles](./Roles.md#show-editor-role] for more info.

### Permissions

#### Add to Author Role Capabilities
Default: On. Allow users with WordPress Author role to publish and edit their own Shows and Playlists.

#### Add to Editor Role Capabilities
Default: On. Allow users with WordPress Editor role to edit all Radio Station post types.


### Role Editing

#### [Pro] Role Editor Interface
Allows you to assign any of the Radio Station plugin Roles directly to any user.
