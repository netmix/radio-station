# Radio Station Stream Player

***


## Radio Player

The Radio Player is available as a Shortcode, Widget or Block. In Pro it is also available as a Sitewide Bar Player, as well as an Elementor Widget and Beaver Builder Module.

### Default Player Settings

Default settings for the Player can be set on the Plugin Settings page on the Player tab. These will be used in widgets wherever the widget options are set to "Default". This saves you from setting them twice, but also means that you can override these defaults in individual widgets as needed. (see [Options](./Options.md#player) for a list of these options.)

* Player Title: Display your Radio Station Title in Player by default.
* Player Image: Display your Radio Station Image in Player by default.
* Script: Default audio script to use for Radio Streaming Player. Ampliture, Howler or Jplayer.
* Fallback Scripts: Fallback audio scripts to try if/when the default Player script fails.
* Theme: Default Player Controls theme style. Light or dark to match your theme.
* Buttons: Default Player Buttons shape style. Circular, rounded or square.
* Volume Controls: Which volume controls to display in the Player by default.
* Player Debug Mode: Output player debug information in browser javascript console.
* Start Volume: Initial volume for when the Player starts playback.
* Single Player: Stop other Player instances when a Player is started.

### Radio Player Widget

A Player widget instance can be added to any widget area via the WordPress Admin Appearance -> Widgets page. The widget options correspond to the shortcode attributes below, allowing you control over the widget display output.

### Radio Player Block

A Player block can be added via the Block Editor by clicking the blue + icon. The `[Radio Station] Stream Player` Block can be found in the Radio Station block category (above the Embed category.)

#### [Pro] Sitewide Bar Player

[Radio Station Pro](https://radiostation.pro) includes a Sitewide Bar Streaming Player, including continuous uninterruptable playback via the integrated Teleporter page transition plugin. The Player Bar isn't added via the Widgets page, but is instead configured via the Plugin Settings page under the Player tab. It has the following main options:

* Bar Position: Fixed in the header or footer area (top or bottom of page, unaffected by scrolling.)
* Bar Height: Set the absolute height of the Player Bar in pixels.
* Fade In Player Bar: How long to take to fade in Player Bar after page load.
* Continous Playback: Enables uninterrupt playback while navigating between pages.
* Player Page Fade: How long to take to fade between pages when continous playback is enabled.
* Text Color and Background Color: Bar Player Default text and background colors.
* Autoresume Playback: attempts to autoresume playback for returning visitors.
* Popup Player Button: adds a button to open the player in a separate window.
* Current Show Display: whether to display the Current Show in the Player Bar.
* Now Playing Display: whether to display current track information via stream metadata.
* Metadata URL: alternative URL for metadata retrieval (normally via stream URL.)

#### [Pro] Extra Color Options

The Player Bar also includes some extra color options to match the look and feel of your site (these are also used as defaults for other Player instances.) 

* Playing Icon Highlight Color: Play button highlight color when playing stream.
* Control Icons Highlight Color: Volume Button Hover highlight and player loading highlight.
* Volume Track and Knob Colors: To style the volume slider track and it's thumb knob.
		
And in addition to the existing Light and Dark button themes, you can also choose from colored button themes of: Red, Orange, Yellow, Light Green, Green, Cyan, Light Blue, Blue, Purple or Magenta. Matching Play/Pause and Volume/Track control images will be used when you activate these theme options.

### Radio Player Shortcode

`[radio-player]`

The following attributes are available for this shortcode:

* *url* : Stream or file URL. Default: Plugin setting.
* *script* : Default audio script. 'amplitude', 'jplayer', or 'howler'. Default: amplitude
* *layout* : Player section layout. 'horizontal', 'vertical'. Default: 'vertical'
* *theme* : Player Buttom theme. 'light' or 'dark'. Default: 'light'.
* *buttons* : Control buttons shape. 'circular', 'rounded' or 'square'. Default 'rounded'.
* *title* : Player/Station Title. String. 0 for none
* *image* : Player/Station Image. URL (recommended size 256x256). Default none.
* *volume* : Initial Player Volume. 0-100. Default: 77
* *volumes* : Volume Control Buttons, comma separated. (slider,updown,mute,max) Default: all.
* *default* : Use this as the default Player on the page. 0 or 1. Default 0.

[Demo Site Example Output](https://demo.radiostation.pro/player-shortcode/)

#### Using the Shortcode in Templates

Remember, if you want to display a Shortcode within a custom Template (or elsewhere in custom code), you can use the WordPress `do_shortcode` function. eg. `do_shortcode('[radio-player]');`

#### [Pro] Extra Widget and Shortcode Options

All additional options available in Pro are also available for individual Player widgets and shortcodes (including Gutenberg Block, Elementor Widget and Beaver Builder Module.) 

* **Color Options**
* *text_color* : Player Text Color. Defaults to none (inherit.)
* *background_color* : Player Background Color. Defaults to none (inherit.)
* *playing_color* : Playing Highlight Color. Defaults to Plugin Setting.
* *buttons_color* : Buttons Highlight Color. Defaults to Plugin Setting.
* *track_color* : Volume Track Color. Defaults to Plugin Setting.
* *thumb_color* : Volume Thumb Color. Defaults to Plugin Setting.
* **Extra Options**
* *popup* : Add button to open Popup Player in separate window. 0 or 1. Defaults to Plugin Setting.
* *currentshow* : Display current Show information in player section. 0 or 1. Defaults to Plugin Setting.
* *nowplaying* : Display currently playing track via stream metadata/playlist. 0 or 1. Defaults to Plugin Setting.
* *animation* : Track animation to use. none, lefttoright, righttoleft, backandforth. Default: backandforth
