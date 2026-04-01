<?php

// =============================
// === Radio Station Options ===
// =============================

// ---------------------------
// Plugin Admin Options Filter
// ---------------------------
// 2.5.18: added filter for admin options
add_action( 'plugins_loaded', 'radio_station_load_admin_options' );
function radio_station_load_admin_options() {
	add_filter( 'radio_station_options', 'radio_station_admin_options' );
	function radio_station_admin_options( $options ) {
		$admin = is_admin();
		$options = radio_station_plugin_options( $admin );
		return $options;
	}
}

// ------------------
// Set Plugin Options
// ------------------
add_filter( 'radio_station_plugin_options', 'radio_station_plugin_options' );
function radio_station_plugin_options( $admin = false ) {

	$timezones = radio_station_get_timezone_options( true, $admin );
	$languages = radio_station_get_language_options( true, $admin );
	$formats = radio_station_get_stream_formats();

	// 2.6.16: add am/pm translations for schedule_start_hour
	if ( $admin ) {
		$am = radio_station_translate_meridiem( 'am' );
		$pm = radio_station_translate_meridiem( 'pm' );
	}

	$options = array(

		// === Stream ===

		// --- [Player] Streaming URL ---
		'streaming_url' => array(
			'type'    => 'text',
			'options' => 'URL',
			'label'   => $admin ? __( 'Streaming URL', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Enter the Streaming URL for your Radio Station. This is used in the Radio Player and discoverable via Data Feeds.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'stream',
		),

		// --- [Player] Stream Format ---
		'streaming_format' => array(
			'type'    => 'select',
			'options' => $formats,
			'label'   => $admin ? __( 'Streaming Format', 'radio-station' ) : '',
			'default' => 'aac',
			'helper'  => $admin ? __( 'Select streaming format for streaming URL.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'stream',
		),

		// --- [Player] Fallback Stream URL ---
		'fallback_url' => array(
			'type'    => 'text',
			'options' => 'URL',
			'label'   => $admin ? __( 'Fallback Stream URL', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Enter an alternative Streaming URL for Player fallback.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'stream',
		),

		// --- [Player] Fallback Stream Format ---
		'fallback_format' => array(
			'type'    => 'select',
			'options' => $formats,
			'label'   => $admin ? __( 'Fallback Format', 'radio-station' ) : '',
			'default' => 'ogg',
			'helper'  => $admin ? __( 'Select streaming fallback for fallback URL.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'stream',
		),

		// --- [Player] Stream GeoBlocking ---
		// TODO: conditional display of blacklist/whitelist fields
		'stream_geo_blocking' => array(
			'label'		=> $admin ? __( 'GeoIP Stream Blocking', 'radio-station' ) : '',
			'type'		=> 'select',
			'options' => array(
				''			=> $admin ? __( 'No GeoIP Blocking', 'radio-station' ) : '',
				'live365'	=> $admin ? __( 'Live365 (only US, UK, Canada, Mexico)', 'radio-station' ) : '',
				// 'blacklist' => $admin ? __( 'Custom Country Blacklist', 'radio-station' ) : '',
				// 'whitelist' => $admin ? __( 'Custom Country Whitelist', 'radio-station' ) : '',
				// 'both'      => $admin ? __( 'Blacklist and Whitelist', 'radio-station' ) : '',
			),
			'default' => '',
			'helper'  => $admin ? __( 'Block streaming according to country, detected by user IP address.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'stream',
			'pro'     => true,
		),

		// === Broadcast ===

		// --- Main Radio Language ---
		'radio_language'    => array(
			'type'    => 'select',
			'options' => $languages,
			'label'   => $admin ? __( 'Main Broadcast Language', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Select the main language used on your Radio Station.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'broadcast',
		),

		// --- Timezone Location ---
		'timezone_location' => array(
			'type'    => 'select',
			'options' => $timezones,
			'label'   => $admin ? __( 'Location Timezone', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Select your Broadcast Location for Radio Timezone display.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'broadcast',
		),

		// --- Clock Time Format ---
		'clock_time_format' => array(
			'type'    => 'select',
			'options' => array(
				'12' => $admin ? __( '12 Hour Format', 'radio-station' ) : '',
				'24' => $admin ? __( '24 Hour Format', 'radio-station' ) : '',
			),
			'label'   => $admin ? __( 'Clock Time Format', 'radio-station' ) : '',
			'default' => '12',
			'helper'  => $admin ? __( 'Default Time Format for display output. Can be overridden in each shortcode or widget.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'broadcast',
		),

		// --- Station Frequency ---
		// 2.5.18: added station frequency option
		'station_frequency' => array(
			'type'    => 'text',
			'label'   => $admin ? __( 'Station Frequency', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Your station frequency as a number.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'broadcast',
		),
		
		// --- Station Band ---
		// 2.5.18: added station band option
		'station_band' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Frequency Band', 'radio-station' ) : '',
			'options' => array(
				''    => $admin ? __( 'n/a', 'radio-station' ) : '',
				'fm'  => $admin ? __( 'FM', 'radio-station' ) : '',
				'am'  => $admin ? __( 'AM', 'radio-station' ) : '',
				'dab' => $admin ? __( 'DAB', 'radio-station' ) : '',
			),
			'default' => '',
			'helper'  => $admin ? __( 'Your station frequency band identifier.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'broadcast',
		),

		// --- Service Identifier ---
		// TODO: service identifier for RadioDNS
		/* 'service_identifier' => array(
			'type'    => 'text',
			'label'   => $admin ? __( 'Service Identifier', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'RadioDNS Service Identifier.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'station',
		), */

		// --- Ping Netmix Directory ---
		// note: disabled by default for WordPress.org repository compliance
		'ping_netmix_directory' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Ping Netmix Directory', 'radio-station' ) : '',
			'default' => '',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Enable this to ping the Netmix Directory whenever you update your schedule.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'broadcast',
		),
			
		// === Station ===

		// --- Station Title ---
		// 2.3.3.8: added station title field
		'station_title' => array(
			'type'    => 'text',
			'label'   => $admin ? __( 'Station Title', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Name of your Radio Station. For use in Stream Player and Data Feeds.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'station',
		),

		// --- Station Tagline ---
		// 2.5.18: added station tagline
		'station_tagline' => array(
			'type'    => 'text',
			'label'   => $admin ? __( 'Station Tagline', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Tagline for your Radio Station. (eg. 24/7 Greatest Hits.)', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'station',
		),

		// --- Station Callsign ---
		// 2.5.18: added station callsign field
		'station_callsign' => array(
			'type'    => 'text',
			'label'   => $admin ? __( 'Station Callsign', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Short callsign for your Radio Station. (eg. TOP.)', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'station',
		),

		// --- Station Image ---
		// 2.3.3.8: added station logo image field
		'station_image' => array(
			'type'    => 'image',
			'label'   => $admin ? __( 'Station Logo Image', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Add a logo image for your Radio Station. Please ensure image is square before uploading. Recommended size 256 x 256', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'station',
		),

		// --- Station Location ---
		// 2.5.18: added station location option
		'station_location' => array(
			'type'    => 'text',
			'label'   => $admin ? __( 'Station Location', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Text display to inform users of your station location.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'station',
		),

		// --- Station Phone Number ---
		// 2.3.3.6: added station phone number option
		'station_phone' => array(
			'type'    => 'text',
			'options' => 'PHONE',
			'label'   => $admin ? __( 'Station Phone', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Main call in phone number for the Station (for requests etc.)', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'station',
		),

		// --- Station Text Number ---
		// 2.5.18: added station text in number
		'station_text' => array(
			'type'    => 'text',
			'options' => 'PHONE',
			'label'   => $admin ? __( 'Station Text', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Text line phone number for the Station (for requests etc.)', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'station',
		),

		// --- Station Email Address ---
		// 2.3.3.8: added station email address option
		// TODO: allow for contact page URL instead ?
		'station_email' => array(
			'type'    => 'email',
			'default' => '',
			'label'   => $admin ? __( 'Station Email', 'radio-station' ) : '',
			'helper'  => $admin ? __( 'Main email address for the Station (for requests etc.)', 'radio-station' ) : '',
			//  . ' ' . __( 'Alternatively, enter the URL for your contact page.' , 'radio-station' ) 
			'tab'     => 'general',
			'section' => 'station',
		),

		// === Feeds ===

		// --- REST Data Routes ---
		'enable_data_routes' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Enable Data Routes', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Enables Station Data Routes via WordPress REST API.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'feeds',
		),

		// --- Data Feed Links ---
		'enable_data_feeds' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Enable Data Feeds', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Enable Station Data Feeds via WordPress Feed links.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'feeds',
		),

		// --- Show Shift Feeds ---
		/* 'show_shift_feeds' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Show Shift Feeds', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Convert RSS Feeds for a single Show to a Show shift feed, allowing a visitor to subscribe to a Show feed to be notified of Show shifts.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'feeds',
			'pro'     => true,
		), */

		// === Performance ===
		// 2.4.0.6: separated performance section

		// --- Shift Conflict Checker ---
		// 2.5.6: added setting for conflict checker
		'conflict_checker' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Shift Conflict Checker', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Check for Shift conflicts when saving Show shift times.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'performance',
		),

		// --- Disable Transients ---
		// 2.4.0.6: change label from Clear Transients
		'clear_transients' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Disable Transients', 'radio-station' ) : '',
			'default' => '',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Clear Schedule transients with every pageload. Less efficient but more reliable.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'performance',
		),

		// --- Transient Caching ---
		'transient_caching' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Show Transients', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Use Show Transient Data to improve Schedule calculation performance.', 'radio-station' ) : '',
			'tab'     => 'general',
			'section' => 'performance',
			'pro'     => true,
		),

		// === Basic Stream Player ===

		// --- Defaults Note ---
		// 2.5.0: added note about defaults being overrideable in widgets
		'player_defaults_note' => array(
			'type'    => 'note',
			'label'   => $admin ? __( 'Player Defaults Note', 'radio-station' ) : '',
			'helper'  => $admin ? __( 'Note that you can override these defaults in specific Player Shortcodes or Widgets.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'basic',
		),

		// --- [Player] Player Title ---
		// TODO: option to display callsign instead ?
		'player_title' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Display Station Title', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Display your Radio Station Title in Player by default.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'basic',
		),

		// --- [Player] Player Image ---
		'player_image' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Display Station Image', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Display your Radio Station Image in Player by default.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'basic',
		),

		// --- [Player] Player Meta ---
		// 2.5.18: added station meta display options
		'player_meta' => array(
			'type'    => 'multicheck',
			'label'   => $admin ? __( 'Display Station Meta', 'radio-station' ) : '',
			'options' => array(
				'tagline'   => $admin ? __( 'Tagline', 'radio-station' ) : '',
				'frequency' => $admin ? __( 'Frequency', 'radio-station' ) : '',
				'location'  => $admin ? __( 'Location', 'radio-station' ) : '',
				'timezone'  => $admin ? __( 'Timezone', 'radio-station' ) : '',
			),
			'default' => array( 'frequency' ),
			'helper'  => $admin ? __( 'Display your Radio Station Meta in Player by default.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'basic',
		),

		// --- [Player] Player Script ---
		// 2.4.0.3: change script default to jplayer
		// 2.5.7: disable howler script (browser incompatibilities)
		'player_script' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Player Script', 'radio-station' ) : '',
			'default' => 'jplayer',
			'options' => array(
				'amplitude' => $admin ? __( 'Amplitude', 'radio-station' ) : '',
				'jplayer'   => $admin ? __( 'jPlayer', 'radio-station' ) : '',
				// 'howler'    => $admin ? __( 'Howler', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'Default audio script to use for playback in the Player.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'basic',
		),

		// --- [Player] Fallback Scripts ---
		// 2.4.0.3: added fallback enable/disable switching
		// 2.4.0.3: fixed option label from Player Script
		// 2.5.7: disable howler script (browser incompatibilities)
		'player_fallbacks' => array(
			'type'    => 'multicheck',
			'label'   => $admin ? __( 'Fallback Scripts', 'radio-station' ) : '',
			'default' => array( 'amplitude', 'jplayer' ),
			'options' => array(
				'amplitude' => $admin ? __( 'Amplitude', 'radio-station' ) : '',
				'jplayer'   => $admin ? __( 'jPlayer', 'radio-station' ) : '',
				// 'howler'    => $admin ? __( 'Howler', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'Enabled fallback audio scripts to try when the default Player script fails.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'basic',
		),

		// --- [Player] Player Theme ---
		'player_theme' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Default Control Theme', 'radio-station' ) : '',
			'default' => 'light',
			'options' => array(
				'light' => $admin ? __( 'Light', 'radio-station' ) : '',
				'dark'  => $admin ? __( 'Dark', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'Default Player Controls theme style. (Color spectrum options available in Pro.)', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'basic',
		),

		// --- [Player] Player Buttons ---
		// 2.5.15: change default to circular
		// 2.5.18: added solid button images options
		'player_buttons' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Default Player Buttons', 'radio-station' ) : '',
			'default' => 'circular',
			'options' => array(
				'solid'     => $admin ? __( 'Solid Buttons (No Outline)', 'radio-station' ) : '',
				'semisolid' => $admin ? __( 'Solid Buttons (Transparent Outline)', 'radio-station' ) : '',
				'circular'  => $admin ? __( 'Hollow Circular Buttons', 'radio-station' ) : '',
				'rounded'   => $admin ? __( 'Hollow Rounded Buttons', 'radio-station' ) : '',
				'square'    => $admin ? __( 'Hollow Square Buttons', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'Default Player Buttons shape style.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'basic',
		),

		// --- [Player] Volume Controls  ---
		// 2.4.0.3: added enable/disable volume controls option
		// 2.5.0: default to volume slider only
		'player_volumes' => array(
			'type'    => 'multicheck',
			'label'   => $admin ? __( 'Volume Controls', 'radio-station' ) : '',
			'default' => array( 'slider' ),
			'suffix'  => '%',
			'options' => array(
				'slider'   => $admin ? __( 'Volume Slider', 'radio-station' ) : '',
				'updown'   => $admin ? __( 'Volume Plus / Minus', 'radio-station' ) : '',
				'mute'     => $admin ? __( 'Mute Volume Toggle', 'radio-station' ) : '',
				'max'      => $admin ? __( 'Maximize Volume', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'Which volume controls to display in the Player by default.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'basic',
		),

		// --- [Player] Player Debug Mode ---
		'player_debug' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Player Debug Mode', 'radio-station' ) : '',
			'default' => '',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Output player debug information in browser javascript console.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'basic',
		),

		// === Player Colours ===

		// --- [Pro/Player] Playing Highlight Color ---
		'player_playing_color' => array(
			'type'    => 'color',
			'label'   => $admin ? __( 'Playing Icon Highlight Color', 'radio-station' ) : '',
			'default' => '#70D070',
			'helper'  => $admin ? __( 'Default highlight color to use for Play button icon when playing.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'colors',
			'pro'     => true,
		),

		// --- [Pro/Player] Control Icons Highlight Color ---
		'player_buttons_color' => array(
			'type'    => 'color',
			'label'   => $admin ? __( 'Control Icons Highlight Color', 'radio-station' ) : '',
			'default' => '#00A0E0',
			'helper'  => $admin ? __( 'Default highlight color to use for Control button icons when active.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'colors',
			'pro'     => true,
		),

		// --- [Pro/Player] Volume Knob Color ---
		'player_thumb_color' => array(
			'type'    => 'color',
			'label'   => $admin ? __( 'Volume Knob Color', 'radio-station' ) : '',
			'default' => '#80C080',
			'helper'  => $admin ? __( 'Default Knob Color for Player Volume Slider.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'colors',
			'pro'     => true,
		),

		// --- [Pro/Player] Volume Track Color ---
		'player_range_color' => array(
			'type'    => 'coloralpha',
			'label'   => $admin ? __( 'Volume Track Color', 'radio-station' ) : '',
			'default' => '#80C080',
			'helper'  => $admin ? __( 'Default Track Color for Player Volume Slider.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'colors',
			'pro'     => true,
		),

		// === Advanced Stream Player ===

		// --- [Player] Player Volume ---
		'player_volume' => array(
			'type'    => 'number',
			'label'   => $admin ? __( 'Player Start Volume', 'radio-station' ) : '',
			'default' => 77,
			'min'     => 0,
			'step'    => 1,
			'max'     => 100,
			'helper'  => $admin ? __( 'Initial volume for when the Player starts playback.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'advanced',
			'pro'     => false,
		),

		// --- [Player] Single Player ---
		'player_single' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Single Player at Once', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Stop any existing Player instances on the page or in other windows or tabs when a Player is started.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'advanced',
			'pro'     => false,
		),

		// --- [Pro/Player] Player Autoresume ---
		// 2.5.15: change autoresume default to off so activated manually
		// 2.5.16: updated helper text
		'player_autoresume' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Autoresume Playback', 'radio-station' ) : '',
			'default' => '',
			'value'   => 'on',
			'helper'  => $admin ? __( 'On return to site or page reload, ask the user to resume stream playback if they were playing the stream previously, using a popup a modal dialogue box.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'advanced',
			'pro'     => true,
		),

		// --- [Pro/Player] Popup Player Button ---
		// 2.5.0: enabled popup player button
		'player_popup' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Popup Player Button', 'radio-station' ) : '',
			'default' => '',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Add button to open Popup Player in separate window.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'advanced',
			'pro'     => true,
		),

		// === Sitewide Player Bar ===

		// --- Player Bar Note ---
		'player_bar_note' => array(
			'type'    => 'note',
			'label'   => $admin ? __( 'Bar Defaults Note', 'radio-station' ) : '',
			'helper'  => $admin ? __( 'The Bar Player uses the default configurations set above.', 'radio-station' )
						. ' ' . __( 'You can override these in specific Player Widgets.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'bar',
		),

		// --- [Pro/Player] Sitewide Player Bar ---
		'player_bar' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Sitewide Player Bar', 'radio-station' ) : '',
			'default' => 'off',
			'options' => array(
				'off'    => $admin ? __( 'No Player Bar', 'radio-station' ) : '',
				'top'    => $admin ? __( 'Top Player Bar', 'radio-station' ) : '',
				'bottom' => $admin ? __( 'Bottom Player Bar', 'radio-station' ) : '',
			),
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => $admin ? __( 'Add a fixed position Player Bar which displays Sitewide.', 'radio-station' ) : '',
			'pro'     => true,
		),

		// --- [Pro/Player] Player Bar Height ---
		// 2.5.15: add px suffix
		'player_bar_height' => array(
			'type'    => 'number',
			'min'     => 40,
			'max'     => 400,
			'step'    => 1,
			'label'   => $admin ? __( 'Player Bar Height', 'radio-station' ) : '',
			'default' => 80,
			'suffix'  => 'px',
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => $admin ? __( 'Set the height of the Sitewide Player Bar in pixels.', 'radio-station' ) : '',
			'pro'     => true,
		),

		// --- [Pro/Player] Responsive Mode ---
		// 2.5.18: added option for responsive expander bar
		'player_bar_responsive' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Bar Responsive Mode', 'radio-station' ) : '',
			'options' => array(
				'left-right' => $admin ? __( 'Horizontal Arrows', 'radio-station' ) : '',
				'expander'   => $admin ? __( 'Vertical Expander', 'radio-station' ) : '',
			),
			'default' => 'left-right',
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => $admin ? __( 'Player Bar responsive mode for mobile overflow sections.', 'radio-station' ) : '',
			'pro'     => true,
		),

		// --- [Pro/Player] Fade In Player Bar ---
		'player_bar_fadein' => array(
			'type'    => 'number',
			'label'   => $admin ? __( 'Fade In Player Bar', 'radio-station' ) : '',
			'default' => 2500,
			'min'     => 0,
			'step'    => 100,
			'max'     => 10000,
			'suffix'  => 'ms',
			'helper'  => $admin ? __( 'Number of milliseconds after Page load over which to fade in Player Bar. Use 0 for instant display.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'bar',
			'pro'     => true,
		),

		// --- [Pro/Player] Continuous Playback ---
		// 2.4.0.1: fix for missing value field
		'player_bar_continuous' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Continuous Playback', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Uninterrupted Sitewide Bar playback while user is navigating between pages! Pages are loaded in background and faded in while Player Bar persists.', 'radio-station' ) . ' <a href="' . RADIO_STATION_DOCS_URL . 'player/#pro-continous-player-integration" target="_blank">' . __( 'Click here for setup notes.', 'radio-station' ) . '</a>' : '',
			'tab'     => 'player',
			'section' => 'bar',
			'pro'     => true,
		),

		// --- [Pro/Player] Player Page Fade ---
		'player_bar_pagefade' => array(
			'type'    => 'number',
			'label'   => $admin ? __( 'Page Fade Time', 'radio-station' ) : '',
			'default' => 2000,
			'min'     => 0,
			'step'    => 100,
			'max'     => 10000,
			'suffix'  => 'ms',
			'helper'  => $admin ? __( 'Number of milliseconds over which to fade in new Pages (when continuous playback is enabled.) Use 0 for instant display.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'bar',
			'pro'     => true,
		),

		// --- [Pro/Player] Page Load Timeout ---
		// 2.4.0.3: add page load timeout option
		'player_bar_timeout' => array(
			'type'    => 'number',
			'label'   => $admin ? __( 'Page Load Timeout', 'radio-station' ) : '',
			'default' => 7000,
			'min'     => 0,
			'step'    => 500,
			'max'     => 20000,
			'suffix'  => 'ms',
			'helper'  => $admin ? __( 'Number of milliseconds to wait for new Page to load before fading in anyway or prompting (if continuous playback is enabled.)', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'bar',
			'pro'     => true,
		),

		// --- [Pro/Player] Page Load Prompt ---
		// 2.5.18: add timeout/error prompt
		'player_bar_prompt' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Load Fail Prompt', 'radio-station' ) : '',
			'default' => '404',
			'options' => array(
				''     => $admin ? __( 'Off', 'radio-station' ) : '',
				'yes'  => $admin ? __( 'Prompt on Timeout only', 'radio-station' ) : '',
				'404'  => $admin ? __( 'Prompt on 404 Not Found only', 'radio-station' ) : '',
				'404t' => $admin ? __( 'Prompt on 404 or Timeout', 'radio-station' ) : '',
				'all'  => $admin ? __( 'Prompt on Timeout or any Error', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'Whether to show a user prompt on page timeout and/or when there is an error (if continuous playback is enabled.)', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'bar',
			'pro'     => true,
		),

		// --- [Pro/Player] Bar Player Text Color ---
		'player_bar_text' => array(
			'type'    => 'color',
			'label'   => $admin ? __( 'Bar Player Text Color', 'radio-station' ) : '',
			'default' => '#FFFFFF',
			'helper'  => $admin ? __( 'Text color for the fixed position Sitewide Bar Player.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'bar',
			'pro'     => true,
		),

		// --- [Pro/Player] Bar Player Background Color ---
		// 2.5.18: fix default alpha value from 255 to 1
		'player_bar_background' => array(
			'type'    => 'coloralpha',
			'label'   => $admin ? __( 'Bar Player Background Color', 'radio-station' ) : '',
			'default' => 'rgba(0,0,0,1)',
			'helper'  => $admin ? __( 'Background color for the fixed position Sitewide Bar Player.', 'radio-station' ) : '',
			'tab'     => 'player',
			'section' => 'bar',
			'pro'     => true,
		),

		// --- [Pro/Player] Display Current Show ---
		// 2.4.0.3: added for current show display
		'player_bar_currentshow' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Display Current Show', 'radio-station' ) : '',
			'value'   => 'yes',
			'default' => 'yes',
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => $admin ? __( 'Display the Current Show in the Player Bar.', 'radio-station' ) : '',
			'pro'     => true,
		),
		
		// --- [Pro/Player] Show Meta ---
		// 2.5.18: added show meta display option
		'player_bar_showmeta' => array(
			'type'    => 'multicheck',
			'label'   => $admin ? __( 'Show Meta Display', 'radio-station' ) : '',
			'options' => array(
				'hosts'       => $admin ? __( 'Hosts', 'radio-station' ) : '',
				'producers'   => $admin ? __( 'Producers', 'radio-station' ) : '',
				'shift'       => $admin ? __( 'Shift Time', 'radio-station' ) : '',
				// 'remaining'   => $admin ? __( 'Remaining Time', 'radio-station' ) : '',
				// 'description' => $admin ? __( 'Description', 'radio-station' ) : '',
			),
			'default' => array( 'hosts', 'shift' ),
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => $admin ? __( 'Show meta to display in the Player Bar.', 'radio-station' ) : '',
			'pro'     => true,
		),

		// --- [Pro/Player] Display Metadata ---
		// 2.4.0.3: added for now playing metadata display
		'player_bar_nowplaying' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Display Now Playing', 'radio-station' ) : '',
			'value'   => 'yes',
			'default' => 'yes',
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => $admin ? __( 'Display the currently playing song in the Player Bar, if a supported metadata format is available. (Icy Meta, Icecast, Shoutcast 1/2, Current Playlist)', 'radio-station' ) : '',
			'pro'     => true,
		),

		// --- [Pro/Player] Track Animation ---
		// 2.5.0: added track animation option
		'player_bar_track_animation' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Track Animation', 'radio-station' ) : '',
			'default' => 'backandforth',
			'options' => array(
				'none'         => $admin ? __( 'No Animation', 'radio-station' ) : '',
				'lefttoright'  => $admin ? __( 'Left to Right Ticker', 'radio-station' ) : '',
				'righttoleft'  => $admin ? __( 'Right to Left Ticker', 'radio-station' ) : '',
				'backandforth' => $admin ? __( 'Back and Forth', 'radio-station' ) : '',
			),
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => $admin ? __( 'How to animate the currently playing track display.', 'radio-station' ) : '',
			'pro'     => true,
		),

		// --- [Pro/Player] Metadata URL ---
		// 2.4.0.3: added for alternative stream metadata URL
		'player_bar_metadata' => array(
			'type'    => 'text',
			'options' => 'URL',
			'label'   => $admin ? __( 'Metadata URL', 'radio-station' ) : '',
			'default' => '',
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => $admin ? __( 'Now playing metadata is normally retrieved via the Stream URL. Use this setting if you need to provide an alternative metadata location.', 'radio-station' ) : '',
			'pro'     => true,
		),

		// --- [Pro/Player] Store Track Metadata ---
		// 2.5.6: added option to store stream
		'player_store_metadata' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Store Track Metadata?', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => $admin ? __( 'Save now playing track metadata in a separate database table for later use.', 'radio-station' ) : '',
			'pro'     => true,
		),

		// === Master Schedule Page ===

		// --- Schedule Page ---
		'schedule_page' => array(
			'type'    => 'select',
			'options' => 'PAGEID',
			'label'   => $admin ? __( 'Master Schedule Page', 'radio-station' ) : '',
			'default' => '',
			'helper'  => $admin ? __( 'Select the Page you are displaying the Master Schedule on.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'schedule',
		),

		// --- Automatic Schedule Display ---
		'schedule_auto' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Automatic Display', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Replaces selected page content with Master Schedule. Alternatively customize with the shortcode: ', 'radio-station' ) . ' [master-schedule]' : '',
			'tab'     => 'pages',
			'section' => 'schedule',
		),

		// --- Default Schedule View ---
		'schedule_view' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Schedule View Default', 'radio-station' ) : '',
			'default' => 'table',
			'options' => array(
				'table'   => $admin ? __( 'Table View', 'radio-station' ) : '',
				'list'    => $admin ? __( 'List View', 'radio-station' ) : '',
				'div'     => $admin ? __( 'Divs View', 'radio-station' ) : '',
				'tabs'    => $admin ? __( 'Tabbed View', 'radio-station' ) : '',
				'default' => $admin ? __( 'Legacy Table', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'View type to use for automatic display on Master Schedule Page.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'schedule',
		),

		// --- Schedule Clock Display ---
		'schedule_clock' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Schedule Clock?', 'radio-station' ) : '',
			'default' => 'clock',
			'options' => array(
				''         => $admin ? __( 'None', 'radio-station' ) : '',
				'clock'    => $admin ? __( 'Clock', 'radio-station' ) : '',
				'timezone' => $admin ? __( 'Timezone', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'Radio Time section display above program Schedule.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'schedule',
		),

		// --- Schedule AJAX Load ---
		// 2.5.10.1: added schedule AJAX load default
		'schedule_ajax' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'AJAX Load?', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Whether to load schedule display via AJAX by default.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'schedule',
		),

		// --- [Pro/Plus] Schedule Switcher ---
		'schedule_switcher' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'View Switching', 'radio-station' ) : '',
			'default' => '',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Enable View Switching on the automatic Master Schedule page.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'schedule',
			'pro'     => true,
		),

		// --- [Pro/Plus] Available Views ---
		// 2.3.2: added additional views option
		'schedule_views' => array(
			'type'    => 'multicheck',
			'label'   => $admin ? __( 'Available Views', 'radio-station' ) : '',
			// note: unstyled list view not included in defaults
			'default' => array( 'table', 'calendar' ),
			'value'   => 'yes',
			'options' => array(
				'table'    => $admin ? __( 'Table View', 'radio-station' ) : '',
				'tabs'     => $admin ? __( 'Tabbed View', 'radio-station' ) : '',
				'list'     => $admin ? __( 'List View', 'radio-station' ) : '',
				'grid'     => $admin ? __( 'Grid View', 'radio-station' ) : '',
				'calendar' => $admin ? __( 'Calendar View', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'Switcher Views available on automatic Master Schedule page.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'schedule',
			'pro'     => true,
		),

		// --- [Pro/Plus] Time Spaced Grid View ---
		// 2.4.0.4: added grid view time spacing option
		'schedule_timegrid' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Time Spaced Grid', 'radio-station' ) : '',
			'default' => '',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Enable Grid View option for equalized time spacing and background imsges.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'schedule',
			'pro'     => true,
		),

		// --- schedule start hour ---
		'schedule_start_hour' => array(
			'label'   => $admin ? __( 'Display Start Hour', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => array(
				0  => $admin ? __( 'Midnight', 'radio-station' ) : 'Midnight',
				1  => $admin ? '1 ' . $am : '1 am',
				2  => $admin ? '2 ' . $am : '2 am',
				3  => $admin ? '3 ' . $am : '3 am',
				4  => $admin ? '4 ' . $am : '4 am',
				5  => $admin ? '5 ' . $am : '5 am',
				6  => $admin ? '6 ' . $am : '6 am',
				7  => $admin ? '7 ' . $am : '7 am',
				8  => $admin ? '8 ' . $am : '8 am',
				9  => $admin ? '9 ' . $am : '9 am',
				10 => $admin ? '10 ' . $am : '10 am',
				11 => $admin ? '11 ' . $am : '11 am',
				12 => $admin ? __( 'Noon', 'radio-station' ) : 'Noon',
				13 => $admin ? '1 ' . $pm : '1 pm',
				14 => $admin ? '2 ' . $pm : '2 pm',
				15 => $admin ? '3 ' . $pm : '3 pm',
				16 => $admin ? '4 ' . $pm : '4 pm',
				17 => $admin ? '5 ' . $pm : '5 pm',
				18 => $admin ? '6 ' . $pm : '6 pm',
				19 => $admin ? '7 ' . $pm : '7 pm',
				20 => $admin ? '8 ' . $pm : '8 pm',
				21 => $admin ? '9 ' . $pm : '9 pm',
				22 => $admin ? '10 ' . $pm : '10 pm',
				23 => $admin ? '11 ' . $pm : '11 pm',
			),
			'default' => '0',
			'helper'  => $admin ? __( 'Schedule displays will start from this hour instead of midnight.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'schedule',
			'pro'     => true,
		),

		// === Show Pages ===

		// --- Show Blocks Position ---
		'show_block_position' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Info Blocks Position', 'radio-station' ) : '',
			'options' => array(
				'left'  => $admin ? __( 'Float Left', 'radio-station' ) : '',
				'right' => $admin ? __( 'Float Right', 'radio-station' ) : '',
				'top'   => $admin ? __( 'Float Top', 'radio-station' ) : '',
			),
			'default' => 'left',
			'helper'  => $admin ? __( 'Where to position Show info blocks relative to Show Page content.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
		),

		// ---- Show Section Layout ---
		'show_section_layout' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Show Content Layout', 'radio-station' ) : '',
			'options' => array(
				'tabbed'   => $admin ? __( 'Tabbed', 'radio-station' ) : '',
				'standard' => $admin ? __( 'Standard', 'radio-station' ) : '',
			),
			'default' => 'tabbed',
			'helper'  => $admin ? __( 'How to display extra sections below Show description. In content tabs or standard layout down the page.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
		),

		// ---- Show Player ---
		// 2.5.15: added show player selection
		'show_player' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Latest Show Player', 'radio-station' ) : '',
			'options' => array(
				'radio_player'   => $admin ? __( 'Radio Station Stream Player', 'radio-station' ) : '',
				'media_elements' => $admin ? __( 'MediaElements (WordPress)', 'radio-station' ) : '',
			),
			'default' => 'media_elements',
			'helper'  => $admin ? __( 'Which player to use on the Show pages for the latest show recording.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
		),

		// --- Show Player Theme ---
		// 2.5.15: added show player theme selection
		'show_player_theme' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Show Player Theme', 'radio-station' ) : '',
			'default' => '',
			'options' => array(
				''		=> $admin ? __( 'Player Default', 'radio-station' ) : '',
				'light' => $admin ? __( 'Light', 'radio-station' ) : '',
				'dark'  => $admin ? __( 'Dark', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'Show Player Controls theme style (Radio Station Stream Player only,)', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
		),

		// --- Phone for Shows ---
		// 2.3.3.6: added default to station phone option
		// 2.5.18: moved from station section
		'shows_phone' => array(
			'type'    => 'checkbox',
			'default' => '',
			'value'   => 'yes',
			'label'   => $admin ? __( 'Show Phone Default', 'radio-station' ) : '',
			'helper'  => $admin ? __( 'Display Station phone number on Shows where a Show phone number is not set.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
		),

		// --- Text for Shows ---
		// TODO: text number display on show pages
		/* 'shows_text' => array(
			'type'    => 'checkbox',
			'default' => '',
			'value'   => 'yes',
			'label'   => $admin ? __( 'Show Email Default', 'radio-station' ) : '',
			'helper'  => $admin ? __( 'Display Station text number on Shows where a Show text number is not set.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
		), */
		
		// --- Email for Shows ---
		// 2.3.3.8: added default to email address option
		// 2.5.18: moved from station section
		'shows_email' => array(
			'type'    => 'checkbox',
			'default' => '',
			'value'   => 'yes',
			'label'   => $admin ? __( 'Show Email Display', 'radio-station' ) : '',
			'helper'  => $admin ? __( 'Display Station email address on Shows where a Show email address is not set.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
		),

		// TODO: Show RSS / Calendar Links ?

		// --- Latest Show Posts ---
		// 'show_latest_posts' => array(
		// 	'type'    => 'numeric',
		// 	'label'   => $admin ? __( 'Latest Show Posts', 'radio-station' ) : '',
		// 	'step'    => 1,
		// 	'min'     => 0,
		// 	'max'     => 100,
		// 	'default' => 3,
		// 	'helper'  => $admin ? __( 'Number of Latest Blog Posts to display above Show Page tabs.', 'radio-station' ) : '',
		// 	'tab'     => 'pages',
		// 	'section' => 'show',
		// ),

		// --- Show Posts Per Page ---
		'show_posts_per_page' => array(
			'type'    => 'numeric',
			'label'   => $admin ? __( 'Posts per Page', 'radio-station' ) : '',
			'step'    => 1,
			'min'     => 0,
			'max'     => 1000,
			'default' => 10,
			'helper'  => $admin ? __( 'Linked Show Posts per page on the Show Page tab/display.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
		),

		// --- Show Playlists per Page ---
		'show_playlists_per_page' => array(
			'type'    => 'numeric',
			'step'    => 1,
			'min'     => 0,
			'max'     => 1000,
			'label'   => $admin ? __( 'Playlists per Page', 'radio-station' ) : '',
			'default' => 10,
			'helper'  => $admin ? __( 'Playlists per page on the Show Page tab/display', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
		),

		// --- [Pro] Show Episodes per Page ---
		'show_episodes_per_page' => array(
			'type'    => 'number',
			'label'   => $admin ? __( 'Episodes per Page', 'radio-station' ) : '',
			'step'    => 1,
			'min'     => 1,
			'max'     => 1000,
			'default' => 10,
			'helper'  => $admin ? __( 'Number of Show Episodes per page on the Show page tab/display.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
			'pro'     => true,
		),

		// --- [Pro] Combined Team Tab ---
		// 2.4.0.7: added combined team grid option
		// 2.5.7: updated to add option to remove team display
		'combined_team_tab' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Team Tab Display', 'radio-station' ) : '',
			'default' => 'yes',
			'options' => array(
				'off'  => $admin ? __( 'No Team Display', 'radio-station' ) : '',
				''     => $admin ? __( 'Separate Role Tabs', 'radio-station' ) : '',
				'yes'  => $admin ? __( 'Combined Team List', 'radio-station' ) : '',
				// 'grid' => $admin ? __( 'Combined Team Grid', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'How to display Show team members (eg. hosts, producers) on a Show page.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
			'pro'     => true,
		),

		// --- Show Header Image ---
		// 2.3.2: added plural to option label
		'show_header_image' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Content Header Images', 'radio-station' ) : '',
			'value'   => 'yes',
			'default' => '',
			'helper'  => $admin ? __( 'If your theme template does not display the Featured Image, enable this and use the Content Header Image box on the Show edit screen instead.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'show',
		),

		// === Profile Pages ===
		// 2.3.3.9: added proflie page settings

		// --- [Pro/Plus] Profile Blocks Position ---
		'profile_block_position' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Info Blocks Position', 'radio-station' ) : '',
			'options' => array(
				'left'  => $admin ? __( 'Float Left', 'radio-station' ) : '',
				'right' => $admin ? __( 'Float Right', 'radio-station' ) : '',
				'top'   => $admin ? __( 'Float Top', 'radio-station' ) : '',
			),
			'default' => 'left',
			'helper'  => $admin ? __( 'Where to position Profile info blocks relative to Profile Page content.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'profile',
			'pro'     => true,
		),

		// ---- [Pro/Plus] Profile Section Layout ---
		'profile_section_layout' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Profile Content Layout', 'radio-station' ) : '',
			'options' => array(
				'tabbed'   => $admin ? __( 'Tabbed', 'radio-station' ) : '',
				'standard' => $admin ? __( 'Standard', 'radio-station' ) : '',
			),
			'default' => 'tabbed',
			'helper'  => $admin ? __( 'How to display extra sections below Profile description. In content tabs or standard layout down the page.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'profile',
			'pro'     => true,
		),

		// === Episode Pages ===
		// 2.3.3.9: added episode page settings

		// --- [Pro] Episode Blocks Position ---
		'episode_block_position' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Info Blocks Position', 'radio-station' ) : '',
			'options' => array(
				'left'  => $admin ? __( 'Float Left', 'radio-station' ) : '',
				'right' => $admin ? __( 'Float Right', 'radio-station' ) : '',
				'top'   => $admin ? __( 'Float Top', 'radio-station' ) : '',
			),
			'default' => 'left',
			'helper'  => $admin ? __( 'Where to position Episode info blocks relative to Episode Page content.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'episode',
			'pro'     => true,
		),

		// ---- [Pro] Episode Section Layout ---
		'episode_section_layout' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Episode Content Layout', 'radio-station' ) : '',
			'options' => array(
				'tabbed'   => $admin ? __( 'Tabbed', 'radio-station' ) : '',
				'standard' => $admin ? __( 'Standard', 'radio-station' ) : '',
			),
			'default' => 'tabbed',
			'helper'  => $admin ? __( 'How to display extra sections below Episode description. In content tabs or standard layout down the page.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'episode',
			'pro'     => true,
		),

		// ---- [Pro] Episode Player ---
		// 2.5.15: added episode player selection
		'episode_player' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Episode Player', 'radio-station' ) : '',
			'options' => array(
				'radio_player'   => $admin ? __( 'Radio Station Stream Player', 'radio-station' ) : '',
				'media_elements' => $admin ? __( 'MediaElements (WordPress)', 'radio-station' ) : '',
			),
			'default' => 'radio_player',
			'helper'  => $admin ? __( 'Which player to use on the Episode pages for the Episode recording.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'episode',
			'pro'     => true,
		),

		// --- [Pro] Episode Player Theme ---
		// 2.5.15: added episode player theme selection
		'episode_player_theme' => array(
			'type'    => 'select',
			'label'   => $admin ? __( 'Episode Player Theme', 'radio-station' ) : '',
			'default' => '',
			'options' => array(
				''		=> $admin ? __( 'Player Default', 'radio-station' ) : '',
				'light' => $admin ? __( 'Light', 'radio-station' ) : '',
				'dark'  => $admin ? __( 'Dark', 'radio-station' ) : '',
			),
			'helper'  => $admin ? __( 'Episode Player Controls theme style (Radio Station Stream Player only,)', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'episode',
			'pro'     => true,
		),

		// --- [Pro] Use Latest Episode ---
		'episode_use_latest' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Use Latest Episode', 'radio-station' ) : '',
			'value'   => 'yes',
			'default' => 'yes',
			'helper'  => $admin ? __( 'Automatically use the latest Episode URL for the player embed on the Show page.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'episode',
			'pro'     => true,
		),

		// ==== Post Type Archives ===
		// 2.4.0.6: move archives to separate tab
		// 2.4.0.6: added post type archives section

		// --- Shows Archive Page ---
		'show_archive_page' => array(
			'label'   => $admin ? __( 'Shows Archive Page', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => 'PAGEID',
			'default' => '',
			'helper'  => $admin ? __( 'Select the Page for displaying the Show archive list.', 'radio-station' ) : '',
			'tab'     => 'archives',
			'section' => 'posttypes',
		),

		// --- Automatic Display ---
		'show_archive_auto' => array(
			'label'   => $admin ? __( 'Automatic Display', 'radio-station' ) : '',
			'type'    => 'checkbox',
			'value'   => 'yes',
			'default' => 'yes',
			'helper'  => $admin ? __( 'Replaces selected page content with default Show Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [shows-archive]' : '',
			'tab'     => 'archives',
			'section' => 'posttypes',
		),

		// ? --- Redirect Shows Archive --- ?
		// 'show_archive_override' => array(
		// 	'label'   => $admin ? __( 'Redirect Shows Archive', 'radio-station' ) : '',
		// 	'type'    => 'checkbox',
		// 	'value'   => 'yes',
		// 	'default' => '',
		// 	'helper'  => $admin ? __( 'Redirect Custom Post Type Archive for Shows to Shows Archive Page.', 'radio-station' ) : '',
		// 	'tab'     => 'archives',
		// 	'section' => 'posttypes',
		// ),

		// --- Overrides Archive Page ---
		'override_archive_page' => array(
			'label'   => $admin ? __( 'Overrides Archive Page', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => 'PAGEID',
			'default' => '',
			'helper'  => $admin ? __( 'Select the Page for displaying the Override archive list.', 'radio-station' ) : '',
			'tab'     => 'archives',
			'section' => 'posttypes',
		),

		// --- Automatic Display ---
		'override_archive_auto' => array(
			'label'   => $admin ? __( 'Automatic Display', 'radio-station' ) : '',
			'type'    => 'checkbox',
			'value'   => 'yes',
			'default' => 'yes',
			'helper'  => $admin ? __( 'Replaces selected page content with default Override Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [overrides-archive]' : '',
			'tab'     => 'archives',
			'section' => 'posttypes',
		),

		// ? --- Redirect Overrides Archive --- ?
		// 'override_archive_override' => array(
		// 	'label'   => $admin ? __( 'Redirect Overrides Archive', 'radio-station' ) : '',
		// 	'type'    => 'checkbox',
		// 	'value'   => 'yes',
		// 	'default' => '',
		// 	'helper'  => $admin ? __( 'Redirect Custom Post Type Archive for Overrides to Overrides Archive Page.', 'radio-station' ) : '',
		// 	'tab'     => 'archives',
		// 	'section' => 'posttypes',
		// ),

		// --- Playlists Archive Page ---
		'playlist_archive_page' => array(
			'label'   => $admin ? __( 'Playlists Archive Page', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => 'PAGEID',
			'default' => '',
			'helper'  => $admin ? __( 'Select the Page for displaying the Playlist archive list.', 'radio-station' ) : '',
			'tab'     => 'archives',
			'section' => 'posttypes',
		),

		// --- Automatic Display ---
		'playlist_archive_auto' => array(
			'label'   => $admin ? __( 'Automatic Display', 'radio-station' ) : '',
			'type'    => 'checkbox',
			'value'   => 'yes',
			'default' => 'yes',
			'helper'  => $admin ? __( 'Replaces selected page content with default Playlist Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [playlists-archive]' : '',
			'tab'     => 'archives',
			'section' => 'posttypes',
		),

		// ? --- Redirect Playlists Archive --- ?
		// 'playlist_archive_override' => array(
		// 	'label'   => $admin ? __( 'Redirect Playlists Archive', 'radio-station' ) : '',
		// 	'type'    => 'checkbox',
		// 	'value'   => 'yes',
		// 	'default' => '',
		// 	'helper'  => $admin ? __( 'Redirect Custom Post Type Archive for Playlists to Playlist Archive Page.', 'radio-station' ) : '',
		// 	'tab'     => 'archives',
		// 	'section' => 'posttypes',
		// ),

		// --- [Pro] Episodes Archive Page ---
		// 2.5.8: added episodes archive page option
		'episode_archive_page' => array(
			'label'   => $admin ? __( 'Episodes Archive Page', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => 'PAGEID',
			'default' => '',
			'helper'  => $admin ? __( 'Select the Page for displaying the Episode archive list.', 'radio-station' ) : '',
			'tab'     => 'archives',
			'section' => 'posttypes',
			'pro'     => true,
		),

		// --- [Pro] Automatic Display ---
		// 2.5.8: added episodes archive automatic display option
		'episode_archive_auto' => array(
			'label'   => $admin ? __( 'Automatic Display', 'radio-station' ) : '',
			'type'    => 'checkbox',
			'value'   => 'yes',
			'default' => 'yes',
			'helper'  => $admin ? __( 'Replaces selected page content with default Episode Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [episodes-archive]' : '',
			'tab'     => 'archives',
			'section' => 'posttypes',
			'pro'     => true,
		),

		// --- [Pro] Team Archive Page ---
		// 2.4.0.6: added option for team archive page
		'team_archive_page' => array(
			'label'   => $admin ? __( 'Team Archive Page', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => 'PAGEID',
			'default' => '',
			'helper'  => $admin ? __( 'Select the Page for displaying the Team archive list.', 'radio-station' ) : '',
			'tab'     => 'archives',
			'section' => 'posttypes',
			'pro'     => true,
		),

		// --- [Pro] Automatic Display ---
		// 2.4.0.6: added option for team archive page
		'team_archive_auto' => array(
			'label'   => $admin ? __( 'Automatic Display', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => array(
				''     => $admin ? __( 'Off', 'radio-station' ) : '',
				'yes'  => $admin ? __( 'List', 'radio-station' ) : '',
				'grid' => $admin ? __( 'Grid', 'radio-station' ) : '',
			),
			'value'   => 'yes',
			'default' => 'yes',
			'helper'  => $admin ? __( 'Replaces selected page content with default Team Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [teams-archive]' : '',
			'tab'     => 'archives',
			'section' => 'posttypes',
			'pro'     => true,
		),

		// === Taxonomy Archives ===
		// 2.4.0.6: added taxonomy archives section

		// --- Genres Archive Page ---
		'genre_archive_page' => array(
			'label'   => $admin ? __( 'Genres Archive Page', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => 'PAGEID',
			'default' => '',
			'helper'  => $admin ? __( 'Select the Page for displaying the Genre archive list.', 'radio-station' ) : '',
			'tab'     => 'archives',
			'section' => 'taxonomies',
		),

		// --- Automatic Display ---
		'genre_archive_auto' => array(
			'label'   => $admin ? __( 'Automatic Display', 'radio-station' ) : '',
			'type'    => 'checkbox',
			'value'   => 'yes',
			'default' => 'yes',
			'helper'  => $admin ? __( 'Replaces selected page content with default Genre Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [genres-archive]' : '',
			'tab'     => 'archives',
			'section' => 'taxonomies',
		),

		// ? --- Redirect Genres Archives --- ?
		// 'genre_archive_override' => array(
		//  'label'   => $admin ? __( 'Redirect Genres Archive', 'radio-station' ) : '',
		//	'type'    => 'checkbox',
		//	'value'   => 'yes',
		//	'default' => '',
		//	'helper'  => $admin ? __( 'Redirect Taxonomy Archive for Genres to Genres Archive Page.', 'radio-station' ) : '',
		//	'tab'     => 'archives',
		//	'section' => 'taxonomies',
		// ),

		// --- Languages Archive Page ---
		// 2.3.3.9: added language archive page
		'language_archive_page' => array(
			'label'   => $admin ? __( 'Languages Archive Page', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => 'PAGEID',
			'default' => '',
			'helper'  => $admin ? __( 'Select the Page for displaying the Language archive list.', 'radio-station' ) : '',
			'tab'     => 'archives',
			'section' => 'taxonomies',
		),

		// --- Automatic Display ---
		// 2.3.3.9: added language archive automatic page
		'language_archive_auto' => array(
			'label'   => $admin ? __( 'Automatic Display', 'radio-station' ) : '',
			'type'    => 'checkbox',
			'value'   => 'yes',
			'default' => 'yes',
			'helper'  => $admin ? __( 'Replaces selected page content with default Language Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [languages-archive]' : '',
			'tab'     => 'archives',
			'section' => 'taxonomies',
		),

		// ? --- Redirect Languages Archives --- ?
		// 'language_archive_override' => array(
		//  'label'   => $admin ? __( 'Redirect Genres Archive', 'radio-station' ) : '',
		//	'type'    => 'checkbox',
		//	'value'   => 'yes',
		//	'default' => '',
		//	'helper'  => $admin ? __( 'Redirect Taxonomy Archive for Languages to Languages Archive Page.', 'radio-station' ) : '',
		//	'tab'     => 'archives',
		//	'section' => 'taxonomies',
		// ),

		// TODO: guest archive pages


		// === Single Templates ===

		// --- Templates Change Note ---
		'templates_change_note' => array(
			'type'    => 'note',
			'label'   => $admin ? __( 'Templates Change Note', 'radio-station' ) : '',
			'helper'  => $admin ? __( 'Since 2.3.0, the way that Templates are implemented has changed.', 'radio-station' )
						. ' ' . __( 'See the Documentation for more information:', 'radio-station' )
						. ' <a href="' . RADIO_STATION_DOCS_URL . 'display/#page-templates" target="_blank">' . __( 'Templates Documentation', 'radio-station' ) . '</a>' : '',
			'tab'     => 'pages',
			'section' => 'single',
		),

		// --- Show Template ---
		'show_template' => array(
			'label'   => $admin ? __( 'Show Template', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => array(
				'page'     => $admin ? __( 'Theme Page Template (page.php)', 'radio-station' ) : '',
				'post'     => $admin ? __( 'Theme Post Template (single.php)', 'radio-station' ) : '',
				'singular' => $admin ? __( 'Theme Singular Template (singular.php)', 'radio-station' ) : '',
				'legacy'   => $admin ? __( 'Legacy Plugin Template', 'radio-station' ) : '',
			),
			'default' => 'page',
			'helper'  => $admin ? __( 'Which template to use for displaying Show content.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'single',
		),

		// --- Combined Template Method ---
		'show_template_combined' => array(
			'label'   => $admin ? __( 'Combined Method', 'radio-station' ) : '',
			'type'    => 'checkbox',
			'value'   => 'yes',
			'default' => '',
			'helper'  => $admin ? __( 'Advanced usage. Use both a custom template AND content filtering for a Show. (Not compatible with Legacy templates.)', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'single',
		),

		// --- Playlist Template ---
		// 2.3.3.8: added missing singular.php option to match show_template
		'playlist_template' => array(
			'label'   => $admin ? __( 'Playlist Template', 'radio-station' ) : '',
			'type'    => 'select',
			'options' => array(
				'page'     => $admin ? __( 'Theme Page Template (page.php)', 'radio-station' ) : '',
				'post'     => $admin ? __( 'Theme Post Template (single.php)', 'radio-station' ) : '',
				'singular' => $admin ? __( 'Theme Singular Template (singular.php)', 'radio-station' ) : '',
				'legacy'   => $admin ? __( 'Legacy Plugin Template', 'radio-station' ) : '',
			),
			'default' => 'page',
			'helper'  => $admin ? __( 'Which template to use for displaying Playlist content.', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'single',
		),

		// --- Combined Template Method ---
		'playlist_template_combined' => array(
			'label'   => $admin ? __( 'Combined Method', 'radio-station' ) : '',
			'type'    => 'checkbox',
			'value'   => 'yes',
			'default' => '',
			'helper'  => $admin ? __( 'Advanced usage. Use both a custom template AND content filtering for a Playlist. (Not compatible with Legacy templates.)', 'radio-station' ) : '',
			'tab'     => 'pages',
			'section' => 'single',
		),

		// === Widgets ===

		// --- AJAX Loading ---
		// 2.3.3: fix to value of value key
		'ajax_widgets' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'AJAX Load Widgets?', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Defaults plugin widgets to AJAX loading. Can also be set on individual widgets.', 'radio-station' ) : '',
			'tab'     => 'widgets',
			'section' => 'loading',
		),

		// --- [Pro/Plus] Dynamic Reloading ---
		'dynamic_reload' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Dynamic Reloading?', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Automatically reload all plugin widgets on change of current Show. Can also be set on individual widgets.', 'radio-station' ) : '',
			'tab'     => 'widgets',
			'section' => 'loading',
			'pro'     => true,
		),

		// --- Convert User Show Times ---
		// 2.5.18: fix to remove incorrect pro flag on free feature
		'convert_show_times' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Convert Show Times', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Automatically display Show times converted into the visitor timezone, based on their browser setting.', 'radio-station' ) : '',
			'tab'     => 'widgets',
			'section' => 'loading',
		),

		// --- [Pro/Plus] Timezone Switching ---
		'timezone_switching' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'User Timezone Switching', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Allow visitors to select their Timezone manually for Show time conversions.', 'radio-station' ) : '',
			'tab'     => 'widgets',
			'section' => 'loading',
			'pro'     => true,
		),

		// === Roles / Capabilities / Permissions  ===
		// 2.3.0: added new capability and role options

		// --- Show Editing Permission Note ---
		// 2.4.0.3: added role to show assignment note
		'permissions_show_role_note' => array(
			'type'    => 'note',
			'label'   => $admin ? __( 'Show Editing Permissions', 'radio-station' ) : '',
			'helper'  => $admin ? __( 'By default, only Hosts and Producers that are assigned to a Show can edit that Show.', 'radio-station' ) . ' ' . __( 'This means an Administrator or Show Editor must assign these users to the Show first.', 'radio-station' ) : '',
			'tab'     => 'roles',
			'section' => 'permissions',
		),

		// --- Playlist Editing Role Note ---
		// 2.4.0.3: added role to playlist assignment note
		'permissions_playlist_role_note' => array(
			'type'    => 'note',
			'label'   => $admin ? __( 'Playlist Permissions', 'radio-station' ) : '',
			'helper'  => $admin ? __( 'Any user with a Host or Producer role can create Playlists.', 'radio-station' ) : '',
			'tab'     => 'roles',
			'section' => 'permissions',
		),

		// --- Show Editor Role Note ---
		'show_editor_role_note' => array(
			'type'    => 'note',
			'label'   => $admin ? __( 'Show Editor Role', 'radio-station' ) : '',
			'helper'  => $admin ? __( 'Since 2.3.0, a new Show Editor role has been added with Publish and Edit capabilities for all Radio Station Post Types.', 'radio-station' ) . ' ' . __( 'You can assign this Role to any user to give them full Station Schedule updating permissions.', 'radio-station' ) . ' ' . __( 'This is so a manager can edit the schedule without requiring full site administration role.', 'radio-station' ) : '',
			'tab'     => 'roles',
			'section' => 'permissions',
		),

		// --- Author Role Capabilities ---
		'add_author_capabilities' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Add to Author Capabilities', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Allow users with WordPress Author role to publish and edit their own Shows and Playlists.', 'radio-station' ) : '',
			'tab'     => 'roles',
			'section' => 'permissions',
		),

		// --- Editor Role Capabilities ---
		'add_editor_capabilities' => array(
			'type'    => 'checkbox',
			'label'   => $admin ? __( 'Add to Editor Capabilities', 'radio-station' ) : '',
			'default' => 'yes',
			'value'   => 'yes',
			'helper'  => $admin ? __( 'Allow users with WordPress Editor role to edit all Radio Station post types.', 'radio-station' ) : '',
			'tab'     => 'roles',
			'section' => 'permissions',
		),

		// ? --- Disallow Shift Changes --- ?
		// 'disallow_shift_changes' => array(
		// 	'type'    => 'checkbox',
		// 	'label'   => $admin ? __( 'Disallow Shift Changes', 'radio-station' ) : '',
		// 	'default' => array(),
		// 	'options' => array(
		// 		'authors'   => $admin ? __( 'WordPress Authors', 'radio-station' ) : '',
		// 		'editors'   => $admin ? __( 'WorddPress Editors', 'radio-station' ) : '',
		// 		'hosts'     => $admin ? __( 'Assigned DJs / Hosts', 'radio-station' ) : '',
		// 		'producers' => $admin ? __( 'Assigned Producers', 'radio-station' ) : '',
		// 	),
		// 	'helper'  => $admin ? __( 'Prevents users of these Roles changing Show Shift times.', 'radio-station' ) : '',
		// 	'tab'     => 'roles',
		// 	'section' => 'permissions',
		// 	'pro'     => true,
		// ),

		// === Tabs and Sections ===

		// --- Tab Labels ---
		// 2.3.2: add widget options tab
		// 2.3.3.8: added player options tab
		// 2.3.3.8: move templates section onto pages tab
		// 2.4.0.6: added separate archives tab
		// 2.7.0: add subscribe and app tabs
		'tabs' => array(
			'general'   => $admin ? __( 'General', 'radio-station' ) : '',
			'player'    => $admin ? __( 'Player', 'radio-station' ) : '',
			'subscribe'	=> $admin ? __( 'Subscribe', 'radio-station' ) : '',
			'pages'     => $admin ? __( 'Pages', 'radio-station' ) : '',
			'archives'  => $admin ? __( 'Archives', 'radio-station' ) : '',
			// 'templates' => $admin ? __( 'Templates', 'radio-station' ) : '',
			'widgets'   => $admin ? __( 'Widgets', 'radio-station' ) : '',
			'roles'     => $admin ? __( 'Roles', 'radio-station' ) : '',
			'app'	    => $admin ? __( 'App', 'radio-station' ) : '',
		),

		// --- Section Labels ---
		// 2.3.2: add widget loading section
		// 2.3.3.9: added profile pages section
		// 2.4.0.6: added performance section
		// 2.4.0.6: added posttypes and taxonomies archive sections
		// 2.7.0: add subscribe sections
		'sections' => array(
			// --- general ---
			'stream'      => $admin ? __( 'Stream', 'radio-station' ) : '',
			'broadcast'   => $admin ? __( 'Broadcast', 'radio-station' ) : '',
			'station'     => $admin ? __( 'Station', 'radio-station' ) : '',
			'feeds'       => $admin ? __( 'Feeds', 'radio-station' ) : '',
			'performance' => $admin ? __( 'Performance', 'radio-station' ) : '',
			// --- player ---
			'basic'       => $admin ? __( 'Basic Defaults', 'radio-station' ) : '',
			'advanced'    => $admin ? __( 'Advanced Defaults', 'radio-station' ) : '',
			'colors'      => $admin ? __( 'Player Colors', 'radio-station' ) : '',
			'bar'         => $admin ? __( 'Sitewide Bar Player', 'radio-station' ) : '',
			// --- pages ---
			'schedule'    => $admin ? __( 'Schedule Page', 'radio-station' ) : '',
			'single'      => $admin ? __( 'Single Templates', 'radio-station' ) : '',
			// 'archive'     => $admin ? __( 'Archive Templates', 'radio-station' ) : '',
			'show'        => $admin ? __( 'Show Pages', 'radio-station' ) : '',
			'profile'     => $admin ? __( 'Profile Pages', 'radio-station' ) : '',
			'episode'     => $admin ? __( 'Episode Pages', 'radio-station' ) : '',
			// --- archives ---
			'archives'    => $admin ? __( 'Archives', 'radio-station' ) : '',
			'posttypes'   => $admin ? __( 'Post Types', 'radio-station' ) : '',
			'taxonomies'  => $admin ? __( 'Taxonomies', 'radio-station' ) : '',
			// --- widgets ---
			'loading'     => $admin ? __( 'Widget Loading', 'radio-station' ) : '',
			// --- roles ---
			'permissions' => $admin ? __( 'Permissions', 'radio-station' ) : '',
		),
	);

	return $options;
}

