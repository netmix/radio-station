<?php

// =============================
// === Radio Station Options ===
// =============================

if ( !defined( 'ABSPATH' ) ) exit;

// ------------------
// Set Plugin Options
// ------------------
$options = array(

	// === Broadcast ===

	// --- [Player] Streaming URL ---
	'streaming_url' => array(
		'type'    => 'text',
		'options' => 'URL',
		'label'   => __( 'Streaming URL', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Enter the Streaming URL for your Radio Station. This is used in the Radio Player and discoverable via Data Feeds.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'broadcast',
	),

	// --- [Player] Stream Format ---
	'streaming_format' => array(
		'type'    => 'select',
		'options' => $formats,
		'label'   => __( 'Streaming Format', 'radio-station' ),
		'default' => 'aac',
		'helper'  => __( 'Select streaming format for streaming URL.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'broadcast',
	),

	// --- [Player] Fallback Stream URL ---
	'fallback_url' => array(
		'type'    => 'text',
		'options' => 'URL',
		'label'   => __( 'Fallback Stream URL', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Enter an alternative Streaming URL for Player fallback.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'broadcast',
	),

	// --- [Player] Fallback Stream Format ---
	'fallback_format' => array(
		'type'    => 'select',
		'options' => $formats,
		'label'   => __( 'Fallback Format', 'radio-station' ),
		'default' => 'ogg',
		'helper'  => __( 'Select streaming fallback for fallback URL.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'broadcast',
	),

	// --- [Player] Stream GeoBlocking ---
	'stream_geo_blocking' => array(
		'label'		=> __( 'GeoIP Stream Blocking', 'radio-station' ),
		'type'		=> 'select',
		'options' => array(
			''			=> __( 'No GeoIP Blocking', 'radio-station' ),
			'live365'	=> __( 'Live365 (only US, UK, Canada, Mexico)', 'radio-station' ),
			// 'blacklist' => __( 'Custom Country Blacklist', 'radio-station' ),
			// 'whitelist' => __( 'Custom Country Whitelist', 'radio-station' ),
		),
		'default' => '',
		'helper'  => __( 'Block streaming according to country, detected by user IP address.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'broadcast',
		'pro'     => true,
	),
		
	// --- Main Radio Language ---
	'radio_language'    => array(
		'type'    => 'select',
		'options' => $languages,
		'label'   => __( 'Main Broadcast Language', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Select the main language used on your Radio Station.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'broadcast',
	),

	// --- Ping Netmix Directory ---
	// note: disabled by default for WordPress.org repository compliance
	// 2.5.0: moved from feeds to broadcast section
	'ping_netmix_directory' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Ping Netmix Directory', 'radio-station' ),
		'default' => '',
		'value'   => 'yes',
		'helper'  => __( 'If you have a Netmix Directory listing, enable this to ping the directory whenever you update your schedule.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'broadcast',
	),

	// === Station ===

	// --- [Player] Station Title ---
	// 2.3.3.8: added station title field
	'station_title' => array(
		'type'    => 'text',
		'label'   => __( 'Station Title', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Name of your Radio Station. For use in Stream Player and Data Feeds.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'station',
	),

	// --- [Player] Station Image ---
	// 2.3.3.8: added station logo image field
	'station_image' => array(
		'type'    => 'image',
		'label'   => __( 'Station Logo Image', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Add a logo image for your Radio Station. Please ensure image is square before uploading. Recommended size 256 x 256', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'station',
	),

	// --- Timezone Location ---
	'timezone_location' => array(
		'type'    => 'select',
		'options' => $timezones,
		'label'   => __( 'Location Timezone', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Select your Broadcast Location for Radio Timezone display.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'station',
	),

	// --- Clock Time Format ---
	'clock_time_format' => array(
		'type'    => 'select',
		'options' => array(
			'12' => __( '12 Hour Format', 'radio-station' ),
			'24' => __( '24 Hour Format', 'radio-station' ),
		),
		'label'   => __( 'Clock Time Format', 'radio-station' ),
		'default' => '12',
		'helper'  => __( 'Default Time Format for display output. Can be overridden in each shortcode or widget.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'station',
	),

	// --- Station Phone Number ---
	// 2.3.3.6: added station phone number option
	'station_phone' => array(
		'type'    => 'text',
		'options' => 'PHONE',
		'label'   => __( 'Station Phone', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Main call in phone number for the Station (for requests etc.)', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'station',
	),

	// --- Phone for Shows ---
	// 2.3.3.6: added default to station phone option
	'shows_phone' => array(
		'type'    => 'checkbox',
		'default' => '',
		'value'   => 'yes',
		'label'   => __( 'Show Phone Display', 'radio-station' ),
		'helper'  => __( 'Display Station phone number on Shows where a Show phone number is not set.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'station',
	),

	// --- Station Email Address ---
	// 2.3.3.8: added station email address option
	'station_email' => array(
		'type'    => 'email',
		'default' => '',
		'label'   => __( 'Station Email', 'radio-station' ),
		'helper'  => __( 'Main email address for the Station (for requests etc.)', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'station',
	),

	// --- Email for Shows ---
	// 2.3.3.8: added default to email address option
	'shows_email' => array(
		'type'    => 'checkbox',
		'default' => '',
		'value'   => 'yes',
		'label'   => __( 'Show Email Display', 'radio-station' ),
		'helper'  => __( 'Display Station email address on Shows where a Show email address is not set.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'station',
	),

	// === Feeds ===

	// --- REST Data Routes ---
	'enable_data_routes' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Enable Data Routes', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Enables Station Data Routes via WordPress REST API.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'feeds',
	),

	// --- Data Feed Links ---
	'enable_data_feeds' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Enable Data Feeds', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Enable Station Data Feeds via WordPress Feed links.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'feeds',
	),

	// === Performance ===
	// 2.4.0.6: separated performance section

	// --- Shift Conflict Checker ---
	// 2.5.6: added setting for conflict checker
	'conflict_checker' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Shift Conflict Checker', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Check for Shift conflicts when saving Show shift times.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'performance',
	),

	// --- Disable Transients ---
	// 2.4.0.6: change label from Clear Transients
	'clear_transients' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Disable Transients', 'radio-station' ),
		'default' => '',
		'value'   => 'yes',
		'helper'  => __( 'Clear Schedule transients with every pageload. Less efficient but more reliable.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'performance',
	),

	// --- Transient Caching ---
	'transient_caching' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Show Transients', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Use Show Transient Data to improve Schedule calculation performance.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'performance',
		'pro'     => true,
	),

	// --- Show Shift Feeds ---
	/* 'show_shift_feeds' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Show Shift Feeds', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Convert RSS Feeds for a single Show to a Show shift feed, allowing a visitor to subscribe to a Show feed to be notified of Show shifts.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'feeds',
		'pro'     => true,
	), */

	// === Basic Stream Player ===

	// --- Defaults Note ---
	// 2.5.0: added note about defaults being overrideable in widgets
	'player_defaults_note' => array(
		'type'    => 'note',
		'label'   => __( 'Player Defaults Note', 'radio-station' ),
		'helper'  => __( 'Note that you can override these defaults in specific Player Widgets.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'basic',
	),

	// --- [Player] Player Title ---
	'player_title' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Display Station Title', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Display your Radio Station Title in Player by default.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'basic',
	),

	// --- [Player] Player Image ---
	'player_image' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Display Station Image', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Display your Radio Station Image in Player by default.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'basic',
	),

	// --- [Player] Player Script ---
	// 2.4.0.3: change script default to jplayer
	// 2.5.7: disable howler script (browser incompatibilities)
	'player_script' => array(
		'type'    => 'select',
		'label'   => __( 'Player Script', 'radio-station' ),
		'default' => 'jplayer',
		'options' => array(
			'amplitude' => __( 'Amplitude', 'radio-station' ),
			'jplayer'   => __( 'jPlayer', 'radio-station' ),
			// 'howler'    => __( 'Howler', 'radio-station' ),
		),
		'helper'  => __( 'Default audio script to use for playback in the Player.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'basic',
	),

	// --- [Player] Fallback Scripts ---
	// 2.4.0.3: added fallback enable/disable switching
	// 2.4.0.3: fixed option label from Player Script
	// 2.5.7: disable howler script (browser incompatibilities)
	'player_fallbacks' => array(
		'type'    => 'multicheck',
		'label'   => __( 'Fallback Scripts', 'radio-station' ),
		'default' => array( 'amplitude', 'howler', 'jplayer' ),
		'options' => array(
			'amplitude' => __( 'Amplitude', 'radio-station' ),
			'jplayer'   => __( 'jPlayer', 'radio-station' ),
			// 'howler'    => __( 'Howler', 'radio-station' ),
		),
		'helper'  => __( 'Enabled fallback audio scripts to try when the default Player script fails.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'basic',
	),

	// --- [Player] Player Theme ---
	'player_theme' => array(
		'type'    => 'select',
		'label'   => __( 'Default Player Theme', 'radio-station' ),
		'default' => 'light',
		'options' => array(
			'light' => __( 'Light', 'radio-station' ),
			'dark'  => __( 'Dark', 'radio-station' ),
		),
		'helper'  => __( 'Default Player Controls theme style.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'basic',
	),

	// --- [Player] Player Buttons ---
	// 2.5.15: change default to circular
	'player_buttons' => array(
		'type'    => 'select',
		'label'   => __( 'Default Player Buttons', 'radio-station' ),
		'default' => 'circular',
		'options' => array(
			'circular' => __( 'Circular Buttons', 'radio-station' ),
			'rounded'  => __( 'Rounded Buttons', 'radio-station' ),
			'square'   => __( 'Square Buttons', 'radio-station' ),
		),
		'helper'  => __( 'Default Player Buttons shape style.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'basic',
	),

	// --- [Player] Volume Controls  ---
	// 2.4.0.3: added enable/disable volume controls option
	// 2.5.0: default to volume slider only
	'player_volumes' => array(
		'type'    => 'multicheck',
		'label'   => __( 'Volume Controls', 'radio-station' ),
		'default' => array( 'slider' ),
		'options' => array(
			'slider'   => __( 'Volume Slider', 'radio-station' ),
			'updown'   => __( 'Volume Plus / Minus', 'radio-station' ),
			'mute'     => __( 'Mute Volume Toggle', 'radio-station' ),
			'max'      => __( 'Maximize Volume', 'radio-station' ),
		),
		'helper'  => __( 'Which volume controls to display in the Player by default.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'basic',
	),

	// --- [Player] Player Debug Mode ---
	'player_debug' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Player Debug Mode', 'radio-station' ),
		'default' => '',
		'value'   => 'yes',
		'helper'  => __( 'Output player debug information in browser javascript console.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'basic',
	),

	// === Player Colours ===

	// --- [Pro/Player] Playing Highlight Color ---
	'player_playing_color' => array(
		'type'    => 'color',
		'label'   => __( 'Playing Icon Highlight Color', 'radio-station' ),
		'default' => '#70E070',
		'helper'  => __( 'Default highlight color to use for Play button icon when playing.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'colors',
		'pro'     => true,
	),

	// --- [Pro/Player] Control Icons Highlight Color ---
	'player_buttons_color' => array(
		'type'    => 'color',
		'label'   => __( 'Control Icons Highlight Color', 'radio-station' ),
		'default' => '#00A0E0',
		'helper'  => __( 'Default highlight color to use for Control button icons when active.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'colors',
		'pro'     => true,
	),

	// --- [Pro/Player] Volume Knob Color ---
	'player_thumb_color' => array(
		'type'    => 'color',
		'label'   => __( 'Volume Knob Color', 'radio-station' ),
		'default' => '#80C080',
		'helper'  => __( 'Default Knob Color for Player Volume Slider.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'colors',
		'pro'     => true,
	),

	// --- [Pro/Player] Volume Track Color ---
	'player_range_color' => array(
		'type'    => 'coloralpha',
		'label'   => __( 'Volume Track Color', 'radio-station' ),
		'default' => '#80C080',
		'helper'  => __( 'Default Track Color for Player Volume Slider.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'colors',
		'pro'     => true,
	),

	// === Advanced Stream Player ===

	// --- [Player] Player Volume ---
	'player_volume' => array(
		'type'    => 'number',
		'label'   => __( 'Player Start Volume', 'radio-station' ),
		'default' => 77,
		'min'     => 0,
		'step'    => 1,
		'max'     => 100,
		'helper'  => __( 'Initial volume for when the Player starts playback.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'advanced',
		'pro'     => false,
	),

	// --- [Player] Single Player ---
	'player_single' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Single Player at Once', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Stop any existing Player instances on the page or in other windows or tabs when a Player is started.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'advanced',
		'pro'     => false,
	),

	// --- [Pro/Player] Player Autoresume ---
	// 2.5.15: change autoresume default to off so activated manually
	// 2.5.16: updated helper text
	'player_autoresume' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Autoresume Playback', 'radio-station' ),
		'default' => '',
		'value'   => 'on',
		'helper'  => __( 'On return to site or page reload, ask the user to resume stream playback if they were playing the stream previously, using a popup a modal dialogue box.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'advanced',
		'pro'     => true,
	),

	// --- [Pro/Player] Popup Player Button ---
	// 2.5.0: enabled popup player button
	'player_popup' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Popup Player Button', 'radio-station' ),
		'default' => '',
		'value'   => 'yes',
		'helper'  => __( 'Add button to open Popup Player in separate window.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'advanced',
		'pro'     => true,
	),

	// === Sitewide Player Bar ===

	// --- Player Bar Note ---
	'player_bar_note' => array(
		'type'    => 'note',
		'label'   => __( 'Bar Defaults Note', 'radio-station' ),
		'helper'  => __( 'The Bar Player uses the default configurations set above.', 'radio-station' )
					. ' ' . __( 'You can override these in specific Player Widgets.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'bar',
	),

	// --- [Pro/Player] Sitewide Player Bar ---
	'player_bar' => array(
		'type'    => 'select',
		'label'   => __( 'Sitewide Player Bar', 'radio-station' ),
		'default' => 'off',
		'options' => array(
			'off'    => __( 'No Player Bar', 'radio-station' ),
			'top'    => __( 'Top Player Bar', 'radio-station' ),
			'bottom' => __( 'Bottom Player Bar', 'radio-station' ),
		),
		'tab'     => 'player',
		'section' => 'bar',
		'helper'  => __( 'Add a fixed position Player Bar which displays Sitewide.', 'radio-station' ),
		'pro'     => true,
	),

	// --- [Pro/Player] Player Bar Height ---
	// 2.5.15: add px suffix
	'player_bar_height' => array(
		'type'    => 'number',
		'min'     => 40,
		'max'     => 400,
		'step'    => 1,
		'label'   => __( 'Player Bar Height', 'radio-station' ),
		'default' => 80,
		'suffix'  => 'px',
		'tab'     => 'player',
		'section' => 'bar',
		'helper'  => __( 'Set the height of the Sitewide Player Bar in pixels.', 'radio-station' ),
		'pro'     => true,
	),

	// --- [Pro/Player] Fade In Player Bar ---
	'player_bar_fadein' => array(
		'type'    => 'number',
		'label'   => __( 'Fade In Player Bar', 'radio-station' ),
		'default' => 2500,
		'min'     => 0,
		'step'    => 100,
		'max'     => 10000,
		'helper'  => __( 'Number of milliseconds after Page load over which to fade in Player Bar. Use 0 for instant display.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'bar',
		'pro'     => true,
	),

	// --- [Pro/Player] Continuous Playback ---
	// 2.4.0.1: fix for missing value field
	'player_bar_continuous' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Continuous Playback', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Uninterrupted Sitewide Bar playback while user is navigating between pages! Pages are loaded in background and faded in while Player Bar persists.', 'radio-station' )
			. ' <a href="' . RADIO_STATION_DOCS_URL . 'player/#pro-continous-player-integration" target="_blank">' . __( 'Click here for setup notes.', 'radio-station' ) . '</a>',
		'tab'     => 'player',
		'section' => 'bar',
		'pro'     => true,
	),

	// --- [Pro/Player] Player Page Fade ---
	'player_bar_pagefade' => array(
		'type'    => 'number',
		'label'   => __( 'Page Fade Time', 'radio-station' ),
		'default' => 2000,
		'min'     => 0,
		'step'    => 100,
		'max'     => 10000,
		'helper'  => __( 'Number of milliseconds over which to fade in new Pages (when continuous playback is enabled.) Use 0 for instant display.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'bar',
		'pro'     => true,
	),

	// --- [Pro/Player] Page Load Timeout ---
	// 2.4.0.3: add page load timeout option
	'player_bar_timeout' => array(
		'type'    => 'number',
		'label'   => __( 'Page Load Timeout', 'teleporter' ),
		'default' => 7000,
		'min'     => 0,
		'step'    => 500,
		'max'     => 20000,
		'helper'  => __( 'Number of milliseconds to wait for new Page to load before fading in anyway (when continuous playback is enabled.)', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'bar',
		'pro'     => true,
	),

	// --- [Pro/Player] Bar Player Text Color ---
	'player_bar_text' => array(
		'type'    => 'color',
		'label'   => __( 'Bar Player Text Color', 'radio-station' ),
		'default' => '#FFFFFF',
		'helper'  => __( 'Text color for the fixed position Sitewide Bar Player.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'bar',
		'pro'     => true,
	),

	// --- [Pro/Player] Bar Player Background Color ---
	'player_bar_background' => array(
		'type'    => 'coloralpha',
		'label'   => __( 'Bar Player Background Color', 'radio-station' ),
		'default' => 'rgba(0,0,0,255)',
		'helper'  => __( 'Background color for the fixed position Sitewide Bar Player.', 'radio-station' ),
		'tab'     => 'player',
		'section' => 'bar',
		'pro'     => true,
	),

	// --- [Pro/Player] Display Current Show ---
	// 2.4.0.3: added for current show display
	'player_bar_currentshow' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Display Current Show', 'radio-station' ),
		'value'   => 'yes',
		'default' => 'yes',
		'tab'     => 'player',
		'section' => 'bar',
		'helper'  => __( 'Display the Current Show in the Player Bar.', 'radio-station' ),
		'pro'     => true,
	),

	// --- [Pro/Player] Display Metadata ---
	// 2.4.0.3: added for now playing metadata display
	'player_bar_nowplaying' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Display Now Playing', 'radio-station' ),
		'value'   => 'yes',
		'default' => 'yes',
		'tab'     => 'player',
		'section' => 'bar',
		'helper'  => __( 'Display the currently playing song in the Player Bar, if a supported metadata format is available. (Icy Meta, Icecast, Shoutcast 1/2, Current Playlist)', 'radio-station' ),
		'pro'     => true,
	),

	// --- [Pro/Player] Track Animation ---
	// 2.5.0: added track animation option
	'player_bar_track_animation' => array(
		'type'    => 'select',
		'label'   => __( 'Track Animation', 'radio-station' ),
		'default' => 'backandforth',
		'options' => array(
			'none'         => __( 'No Animation', 'radio-station' ),
			'lefttoright'  => __( 'Left to Right Ticker', 'radio-station' ),
			'righttoleft'  => __( 'Right to Left Ticker', 'radio-station' ),
			'backandforth' => __( 'Back and Forth', 'radio-station' ),
		),
		'tab'     => 'player',
		'section' => 'bar',
		'helper'  => __( 'How to animate the currently playing track display.', 'radio-station' ),
		'pro'     => true,
	),

	// --- [Pro/Player] Metadata URL ---
	// 2.4.0.3: added for alternative stream metadata URL
	'player_bar_metadata' => array(
		'type'    => 'text',
		'options' => 'URL',
		'label'   => __( 'Metadata URL', 'radio-station' ),
		'default' => '',
		'tab'     => 'player',
		'section' => 'bar',
		'helper'  => __( 'Now playing metadata is normally retrieved via the Stream URL. Use this setting if you need to provide an alternative metadata location.', 'radio-station' ),
		'pro'     => true,
	),

	// --- [Pro/Player] Store Track Metadata ---
	// 2.5.6: added option to store stream
	'player_store_metadata' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Store Track Metadata?', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'tab'     => 'player',
		'section' => 'bar',
		'helper'  => __( 'Save now playing track metadata in a separate database table for later use.', 'radio-station' ),
		'pro'     => true,
	),

	// === Master Schedule Page ===

	// --- Schedule Page ---
	'schedule_page' => array(
		'type'    => 'select',
		'options' => 'PAGEID',
		'label'   => __( 'Master Schedule Page', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Select the Page you are displaying the Master Schedule on.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
	),

	// --- Automatic Schedule Display ---
	'schedule_auto' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Replaces selected page content with Master Schedule. Alternatively customize with the shortcode: ', 'radio-station' ) . ' [master-schedule]',
		'tab'     => 'pages',
		'section' => 'schedule',
	),

	// --- Default Schedule View ---
	'schedule_view' => array(
		'type'    => 'select',
		'label'   => __( 'Schedule View Default', 'radio-station' ),
		'default' => 'table',
		'options' => array(
			'table'   => __( 'Table View', 'radio-station' ),
			'list'    => __( 'List View', 'radio-station' ),
			'div'     => __( 'Divs View', 'radio-station' ),
			'tabs'    => __( 'Tabbed View', 'radio-station' ),
			'default' => __( 'Legacy Table', 'radio-station' ),
		),
		'helper'  => __( 'View type to use for automatic display on Master Schedule Page.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
	),

	// --- Schedule Clock Display ---
	'schedule_clock' => array(
		'type'    => 'select',
		'label'   => __( 'Schedule Clock?', 'radio-station' ),
		'default' => 'clock',
		'options' => array(
			''         => __( 'None', 'radio-station' ),
			'clock'    => __( 'Clock', 'radio-station' ),
			'timezone' => __( 'Timezone', 'radio-station' ),
		),
		'helper'  => __( 'Radio Time section display above program Schedule.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
	),

	// --- Schedule AJAX Load ---
	// 2.5.10.1: added schedule AJAX load default
	'schedule_ajax' => array(
		'type'    => 'checkbox',
		'label'   => __( 'AJAX Load?', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Whether to load schedule display via AJAX by default.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
	),

	// --- [Pro/Plus] Schedule Switcher ---
	'schedule_switcher' => array(
		'type'    => 'checkbox',
		'label'   => __( 'View Switching', 'radio-station' ),
		'default' => '',
		'value'   => 'yes',
		'helper'  => __( 'Enable View Switching on the automatic Master Schedule page.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
		'pro'     => true,
	),

	// --- [Pro/Plus] Available Views ---
	// 2.3.2: added additional views option
	'schedule_views' => array(
		'type'    => 'multicheck',
		'label'   => __( 'Available Views', 'radio-station' ),
		// note: unstyled list view not included in defaults
		'default' => array( 'table', 'calendar' ),
		'value'   => 'yes',
		'options' => array(
			'table'    => __( 'Table View', 'radio-station' ),
			'tabs'     => __( 'Tabbed View', 'radio-station' ),
			'list'     => __( 'List View', 'radio-station' ),
			'grid'     => __( 'Grid View', 'radio-station' ),
			'calendar' => __( 'Calendar View', 'radio-station' ),
		),
		'helper'  => __( 'Switcher Views available on automatic Master Schedule page.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
		'pro'     => true,
	),

	// --- [Pro/Plus] Time Spaced Grid View ---
	// 2.4.0.4: added grid view time spacing option
	'schedule_timegrid' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Time Spaced Grid', 'radio-station' ),
		'default' => '',
		'value'   => 'yes',
		'helper'  => __( 'Enable Grid View option for equalized time spacing and background imsges.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
		'pro'     => true,
	),

	// === Show Pages ===

	// --- Show Blocks Position ---
	'show_block_position' => array(
		'type'    => 'select',
		'label'   => __( 'Info Blocks Position', 'radio-station' ),
		'options' => array(
			'left'  => __( 'Float Left', 'radio-station' ),
			'right' => __( 'Float Right', 'radio-station' ),
			'top'   => __( 'Float Top', 'radio-station' ),
		),
		'default' => 'left',
		'helper'  => __( 'Where to position Show info blocks relative to Show Page content.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// ---- Show Section Layout ---
	'show_section_layout' => array(
		'type'    => 'select',
		'label'   => __( 'Show Content Layout', 'radio-station' ),
		'options' => array(
			'tabbed'   => __( 'Tabbed', 'radio-station' ),
			'standard' => __( 'Standard', 'radio-station' ),
		),
		'default' => 'tabbed',
		'helper'  => __( 'How to display extra sections below Show description. In content tabs or standard layout down the page.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// ---- Show Player ---
	// 2.5.15: added show player selection
	'show_player' => array(
		'type'    => 'select',
		'label'   => __( 'Latest Show Player', 'radio-station' ),
		'options' => array(
			'radio_player'   => __( 'Radio Station Stream Player', 'radio-station' ),
			'media_elements' => __( 'MediaElements (WordPress)', 'radio-station' ),
		),
		'default' => 'media_elements',
		'helper'  => __( 'Which player to use on the Show pages for the latest show recording.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// --- Show Player Theme ---
	// 2.5.15: added show player theme selection
	'show_player_theme' => array(
		'type'    => 'select',
		'label'   => __( 'Show Player Theme', 'radio-station' ),
		'default' => '',
		'options' => array(
			''		=> __( 'Player Default', 'radio-station' ),
			'light' => __( 'Light', 'radio-station' ),
			'dark'  => __( 'Dark', 'radio-station' ),
		),
		'helper'  => __( 'Show Player Controls theme style (Radio Station Stream Player only,)', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// --- Show Header Image ---
	// 2.3.2: added plural to option label
	'show_header_image' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Content Header Images', 'radio-station' ),
		'value'   => 'yes',
		'default' => '',
		'helper'  => __( 'If your chosen template does not display the Featured Image, enable this and use the Content Header Image box on the Show edit screen instead.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// --- Latest Show Posts ---
	// 'show_latest_posts' => array(
	// 	'type'    => 'numeric',
	// 	'label'   => __( 'Latest Show Posts', 'radio-station' ),
	// 	'step'    => 1,
	// 	'min'     => 0,
	// 	'max'     => 100,
	// 	'default' => 3,
	// 	'helper'  => __( 'Number of Latest Blog Posts to display above Show Page tabs.', 'radio-station' ),
	// 	'tab'     => 'pages',
	// 	'section' => 'show',
	// ),

	// --- Show Posts Per Page ---
	'show_posts_per_page' => array(
		'type'    => 'numeric',
		'label'   => __( 'Posts per Page', 'radio-station' ),
		'step'    => 1,
		'min'     => 0,
		'max'     => 1000,
		'default' => 10,
		'helper'  => __( 'Linked Show Posts per page on the Show Page tab/display.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// --- Show Playlists per Page ---
	'show_playlists_per_page' => array(
		'type'    => 'numeric',
		'step'    => 1,
		'min'     => 0,
		'max'     => 1000,
		'label'   => __( 'Playlists per Page', 'radio-station' ),
		'default' => 10,
		'helper'  => __( 'Playlists per page on the Show Page tab/display', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// --- [Pro] Show Episodes per Page ---
	'show_episodes_per_page' => array(
		'type'    => 'number',
		'label'   => __( 'Episodes per Page', 'radio-station' ),
		'step'    => 1,
		'min'     => 1,
		'max'     => 1000,
		'default' => 10,
		'helper'  => __( 'Number of Show Episodes per page on the Show page tab/display.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
		'pro'     => true,
	),

	// --- [Pro] Combined Team Tab ---
	// 2.4.0.7: added combined team grid option
	// 2.5.7: updated to add option to remove team display
	'combined_team_tab' => array(
		'type'    => 'select',
		'label'   => __( 'Team Tab Display', 'radio-station' ),
		'default' => 'yes',
		'options' => array(
			'off'  => __( 'No Team Display', 'radio-station' ),
			''     => __( 'Separate Role Tabs', 'radio-station' ),
			'yes'  => __( 'Combined Team List', 'radio-station' ),
			// 'grid' => __( 'Combined Team Grid', 'radio-station' ),
		),
		'helper'  => __( 'How to display Show team members (eg. hosts, producers) on a Show page.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
		'pro'     => true,
	),

	// === Profile Pages ===
	// 2.3.3.9: added proflie page settings

	// --- [Pro/Plus] Profile Blocks Position ---
	'profile_block_position' => array(
		'type'    => 'select',
		'label'   => __( 'Info Blocks Position', 'radio-station' ),
		'options' => array(
			'left'  => __( 'Float Left', 'radio-station' ),
			'right' => __( 'Float Right', 'radio-station' ),
			'top'   => __( 'Float Top', 'radio-station' ),
		),
		'default' => 'left',
		'helper'  => __( 'Where to position Profile info blocks relative to Profile Page content.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'profile',
		'pro'     => true,
	),

	// ---- [Pro/Plus] Profile Section Layout ---
	'profile_section_layout' => array(
		'type'    => 'select',
		'label'   => __( 'Profile Content Layout', 'radio-station' ),
		'options' => array(
			'tabbed'   => __( 'Tabbed', 'radio-station' ),
			'standard' => __( 'Standard', 'radio-station' ),
		),
		'default' => 'tabbed',
		'helper'  => __( 'How to display extra sections below Profile description. In content tabs or standard layout down the page.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'profile',
		'pro'     => true,
	),

	// === Episode Pages ===
	// 2.3.3.9: added episode page settings

	// --- [Pro] Episode Blocks Position ---
	'episode_block_position' => array(
		'type'    => 'select',
		'label'   => __( 'Info Blocks Position', 'radio-station' ),
		'options' => array(
			'left'  => __( 'Float Left', 'radio-station' ),
			'right' => __( 'Float Right', 'radio-station' ),
			'top'   => __( 'Float Top', 'radio-station' ),
		),
		'default' => 'left',
		'helper'  => __( 'Where to position Episode info blocks relative to Episode Page content.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'episode',
		'pro'     => true,
	),

	// ---- [Pro] Episode Section Layout ---
	'episode_section_layout' => array(
		'type'    => 'select',
		'label'   => __( 'Episode Content Layout', 'radio-station' ),
		'options' => array(
			'tabbed'   => __( 'Tabbed', 'radio-station' ),
			'standard' => __( 'Standard', 'radio-station' ),
		),
		'default' => 'tabbed',
		'helper'  => __( 'How to display extra sections below Episode description. In content tabs or standard layout down the page.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'episode',
		'pro'     => true,
	),

	// ---- [Pro] Episode Player ---
	// 2.5.15: added episode player selection
	'episode_player' => array(
		'type'    => 'select',
		'label'   => __( 'Episode Player', 'radio-station' ),
		'options' => array(
			'radio_player'   => __( 'Radio Station Stream Player', 'radio-station' ),
			'media_elements' => __( 'MediaElements (WordPress)', 'radio-station' ),
		),
		'default' => 'radio_player',
		'helper'  => __( 'Which player to use on the Episode pages for the Episode recording.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'episode',
		'pro'     => true,
	),

	// --- [Pro] Episode Player Theme ---
	// 2.5.15: added episode player theme selection
	'episode_player_theme' => array(
		'type'    => 'select',
		'label'   => __( 'Episode Player Theme', 'radio-station' ),
		'default' => '',
		'options' => array(
			''		=> __( 'Player Default', 'radio-station' ),
			'light' => __( 'Light', 'radio-station' ),
			'dark'  => __( 'Dark', 'radio-station' ),
		),
		'helper'  => __( 'Episode Player Controls theme style (Radio Station Stream Player only,)', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'episode',
		'pro'     => true,
	),

	// --- [Pro] Use Latest Episode ---
	'episode_use_latest' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Use Latest Episode', 'radio-station' ),
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Automatically use the latest Episode URL for the player embed on the Show page.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'episode',
		'pro'     => true,
	),

	// ==== Post Type Archives ===
	// 2.4.0.6: move archives to separate tab
	// 2.4.0.6: added post type archives section

	// --- Shows Archive Page ---
	'show_archive_page' => array(
		'label'   => __( 'Shows Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Show archive list.', 'radio-station' ),
		'tab'     => 'archives',
		'section' => 'posttypes',
	),

	// --- Automatic Display ---
	'show_archive_auto' => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Show Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [shows-archive]',
		'tab'     => 'archives',
		'section' => 'posttypes',
	),

	// ? --- Redirect Shows Archive --- ?
	// 'show_archive_override' => array(
	// 	'label'   => __( 'Redirect Shows Archive', 'radio-station' ),
	// 	'type'    => 'checkbox',
	// 	'value'   => 'yes',
	// 	'default' => '',
	// 	'helper'  => __( 'Redirect Custom Post Type Archive for Shows to Shows Archive Page.', 'radio-station' ),
	// 	'tab'     => 'archives',
	// 	'section' => 'posttypes',
	// ),

	// --- Overrides Archive Page ---
	'override_archive_page' => array(
		'label'   => __( 'Overrides Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Override archive list.', 'radio-station' ),
		'tab'     => 'archives',
		'section' => 'posttypes',
	),

	// --- Automatic Display ---
	'override_archive_auto' => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Override Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [overrides-archive]',
		'tab'     => 'archives',
		'section' => 'posttypes',
	),

	// ? --- Redirect Overrides Archive --- ?
	// 'override_archive_override' => array(
	// 	'label'   => __( 'Redirect Overrides Archive', 'radio-station' ),
	// 	'type'    => 'checkbox',
	// 	'value'   => 'yes',
	// 	'default' => '',
	// 	'helper'  => __( 'Redirect Custom Post Type Archive for Overrides to Overrides Archive Page.', 'radio-station' ),
	// 	'tab'     => 'archives',
	// 	'section' => 'posttypes',
	// ),

	// --- Playlists Archive Page ---
	'playlist_archive_page' => array(
		'label'   => __( 'Playlists Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Playlist archive list.', 'radio-station' ),
		'tab'     => 'archives',
		'section' => 'posttypes',
	),

	// --- Automatic Display ---
	'playlist_archive_auto' => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Playlist Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [playlists-archive]',
		'tab'     => 'archives',
		'section' => 'posttypes',
	),

	// ? --- Redirect Playlists Archive --- ?
	// 'playlist_archive_override' => array(
	// 	'label'   => __( 'Redirect Playlists Archive', 'radio-station' ),
	// 	'type'    => 'checkbox',
	// 	'value'   => 'yes',
	// 	'default' => '',
	// 	'helper'  => __( 'Redirect Custom Post Type Archive for Playlists to Playlist Archive Page.', 'radio-station' ),
	// 	'tab'     => 'archives',
	// 	'section' => 'posttypes',
	// ),

	// --- [Pro] Episodes Archive Page ---
	// 2.5.8: added episodes archive page option
	'episode_archive_page' => array(
		'label'   => __( 'Episodes Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Episode archive list.', 'radio-station' ),
		'tab'     => 'archives',
		'section' => 'posttypes',
		'pro'     => true,
	),

	// --- [Pro] Automatic Display ---
	// 2.5.8: added episodes archive automatic display option
	'episode_archive_auto' => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Episode Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [episodes-archive]',
		'tab'     => 'archives',
		'section' => 'posttypes',
		'pro'     => true,
	),

	// --- [Pro] Team Archive Page ---
	// 2.4.0.6: added option for team archive page
	'team_archive_page' => array(
		'label'   => __( 'Team Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Team archive list.', 'radio-station' ),
		'tab'     => 'archives',
		'section' => 'posttypes',
		'pro'     => true,
	),

	// --- [Pro] Automatic Display ---
	// 2.4.0.6: added option for team archive page
	'team_archive_auto' => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'select',
		'options' => array(
			''     => __( 'Off', 'radio-station' ),
			'yes'  => __( 'List', 'radio-station' ),
			'grid' => __( 'Grid', 'radio-station' ),
		),
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Team Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [teams-archive]',
		'tab'     => 'archives',
		'section' => 'posttypes',
		'pro'     => true,
	),

	// === Taxonomy Archives ===
	// 2.4.0.6: added taxonomy archives section

	// --- Genres Archive Page ---
	'genre_archive_page' => array(
		'label'   => __( 'Genres Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Genre archive list.', 'radio-station' ),
		'tab'     => 'archives',
		'section' => 'taxonomies',
	),

	// --- Automatic Display ---
	'genre_archive_auto' => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Genre Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [genres-archive]',
		'tab'     => 'archives',
		'section' => 'taxonomies',
	),

	// ? --- Redirect Genres Archives --- ?
	// 'genre_archive_override' => array(
	//  'label'   => __( 'Redirect Genres Archive', 'radio-station' ),
	//	'type'    => 'checkbox',
	//	'value'   => 'yes',
	//	'default' => '',
	//	'helper'  => __( 'Redirect Taxonomy Archive for Genres to Genres Archive Page.', 'radio-station' ),
	//	'tab'     => 'archives',
	//	'section' => 'taxonomies',
	// ),

	// --- Languages Archive Page ---
	// 2.3.3.9: added language archive page
	'language_archive_page' => array(
		'label'   => __( 'Languages Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Language archive list.', 'radio-station' ),
		'tab'     => 'archives',
		'section' => 'taxonomies',
	),

	// --- Automatic Display ---
	// 2.3.3.9: added language archive automatic page
	'language_archive_auto' => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Language Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [languages-archive]',
		'tab'     => 'archives',
		'section' => 'taxonomies',
	),

	// ? --- Redirect Languages Archives --- ?
	// 'language_archive_override' => array(
	//  'label'   => __( 'Redirect Genres Archive', 'radio-station' ),
	//	'type'    => 'checkbox',
	//	'value'   => 'yes',
	//	'default' => '',
	//	'helper'  => __( 'Redirect Taxonomy Archive for Languages to Languages Archive Page.', 'radio-station' ),
	//	'tab'     => 'archives',
	//	'section' => 'taxonomies',
	// ),

	// TODO: guest archive pages


	// === Single Templates ===

	// --- Templates Change Note ---
	'templates_change_note' => array(
		'type'    => 'note',
		'label'   => __( 'Templates Change Note', 'radio-station' ),
		'helper'  => __( 'Since 2.3.0, the way that Templates are implemented has changed.', 'radio-station' )
					. ' ' . __( 'See the Documentation for more information:', 'radio-station' )
					. ' <a href="' . RADIO_STATION_DOCS_URL . 'display/#page-templates" target="_blank">' . __( 'Templates Documentation', 'radio-station' ) . '</a>',
		'tab'     => 'pages',
		'section' => 'single',
	),

	// --- Show Template ---
	'show_template' => array(
		'label'   => __( 'Show Template', 'radio-station' ),
		'type'    => 'select',
		'options' => array(
			'page'     => __( 'Theme Page Template (page.php)', 'radio-station' ),
			'post'     => __( 'Theme Post Template (single.php)', 'radio-station' ),
			'singular' => __( 'Theme Singular Template (singular.php)', 'radio-station' ),
			'legacy'   => __( 'Legacy Plugin Template', 'radio-station' ),
		),
		'default' => 'page',
		'helper'  => __( 'Which template to use for displaying Show content.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'single',
	),

	// --- Combined Template Method ---
	'show_template_combined' => array(
		'label'   => __( 'Combined Method', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => '',
		'helper'  => __( 'Advanced usage. Use both a custom template AND content filtering for a Show. (Not compatible with Legacy templates.)', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'single',
	),

	// --- Playlist Template ---
	// 2.3.3.8: added missing singular.php option to match show_template
	'playlist_template' => array(
		'label'   => __( 'Playlist Template', 'radio-station' ),
		'type'    => 'select',
		'options' => array(
			'page'     => __( 'Theme Page Template (page.php)', 'radio-station' ),
			'post'     => __( 'Theme Post Template (single.php)', 'radio-station' ),
			'singular' => __( 'Theme Singular Template (singular.php)', 'radio-station' ),
			'legacy'   => __( 'Legacy Plugin Template', 'radio-station' ),
		),
		'default' => 'page',
		'helper'  => __( 'Which template to use for displaying Playlist content.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'single',
	),

	// --- Combined Template Method ---
	'playlist_template_combined' => array(
		'label'   => __( 'Combined Method', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => '',
		'helper'  => __( 'Advanced usage. Use both a custom template AND content filtering for a Playlist. (Not compatible with Legacy templates.)', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'single',
	),

	// === Widgets ===

	// --- AJAX Loading ---
	// 2.3.3: fix to value of value key
	'ajax_widgets' => array(
		'type'    => 'checkbox',
		'label'   => __( 'AJAX Load Widgets?', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Defaults plugin widgets to AJAX loading. Can also be set on individual widgets.', 'radio-station' ),
		'tab'     => 'widgets',
		'section' => 'loading',
	),

	// --- [Pro/Plus] Dynamic Reloading ---
	'dynamic_reload' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Dynamic Reloading?', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Automatically reload all plugin widgets on change of current Show. Can also be set on individual widgets.', 'radio-station' ),
		'tab'     => 'widgets',
		'section' => 'loading',
		'pro'     => true,
	),

	// --- [Pro/Plus] Translate User Times ---
	'convert_show_times' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Convert Show Times', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Automatically display Show times converted into the visitor timezone, based on their browser setting.', 'radio-station' ),
		'tab'     => 'widgets',
		'section' => 'loading',
		'pro'     => true,
	),

	// --- [Pro/Plus] Timezone Switching ---
	'timezone_switching' => array(
		'type'    => 'checkbox',
		'label'   => __( 'User Timezone Switching', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Allow visitors to select their Timezone manually for Show time conversions.', 'radio-station' ),
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
		'label'   => __( 'Show Editing Permissions', 'radio-station' ),
		'helper'  => __( 'By default, only Hosts and Producers that are assigned to a Show can edit that Show.', 'radio-station' )
					. ' ' . __( 'This means an Administrator or Show Editor must assign these users to the Show first.', 'radio-station' ),
		'tab'     => 'roles',
		'section' => 'permissions',
	),

	// --- Playlist Editing Role Note ---
	// 2.4.0.3: added role to playlist assignment note
	'permissions_playlist_role_note' => array(
		'type'    => 'note',
		'label'   => __( 'Playlist Permissions', 'radio-station' ),
		'helper'  => __( 'Any user with a Host or Producer role can create Playlists.', 'radio-station' ),
		'tab'     => 'roles',
		'section' => 'permissions',
	),

	// --- Show Editor Role Note ---
	'show_editor_role_note' => array(
		'type'    => 'note',
		'label'   => __( 'Show Editor Role', 'radio-station' ),
		'helper'  => __( 'Since 2.3.0, a new Show Editor role has been added with Publish and Edit capabilities for all Radio Station Post Types.', 'radio-station' )
					. ' ' . __( 'You can assign this Role to any user to give them full Station Schedule updating permissions.', 'radio-station' )
					. ' ' . __( 'This is so a manager can edit the schedule without requiring full site administration role.', 'radio-station' ),
		'tab'     => 'roles',
		'section' => 'permissions',
	),

	// --- Author Role Capabilities ---
	'add_author_capabilities' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Add to Author Capabilities', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Allow users with WordPress Author role to publish and edit their own Shows and Playlists.', 'radio-station' ),
		'tab'     => 'roles',
		'section' => 'permissions',
	),

	// --- Editor Role Capabilities ---
	'add_editor_capabilities' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Add to Editor Capabilities', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Allow users with WordPress Editor role to edit all Radio Station post types.', 'radio-station' ),
		'tab'     => 'roles',
		'section' => 'permissions',
	),

	// ? --- Disallow Shift Changes --- ?
	// 'disallow_shift_changes' => array(
	// 	'type'    => 'checkbox',
	// 	'label'   => __( 'Disallow Shift Changes', 'radio-station' ),
	// 	'default' => array(),
	// 	'options' => array(
	// 		'authors'   => __( 'WordPress Authors', 'radio-station' ),
	// 		'editors'   => __( 'WorddPress Editors', 'radio-station' ),
	// 		'hosts'     => __( 'Assigned DJs / Hosts', 'radio-station' ),
	// 		'producers' => __( 'Assigned Producers', 'radio-station' ),
	// 	),
	// 	'helper'  => __( 'Prevents users of these Roles changing Show Shift times.', 'radio-station' ),
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
	'tabs' => array(
		'general'   => __( 'General', 'radio-station' ),
		'player'    => __( 'Player', 'radio-station' ),
		'pages'     => __( 'Pages', 'radio-station' ),
		'archives'  => __( 'Archives', 'radio-station' ),
		// 'templates' => __( 'Templates', 'radio-station' ),
		'widgets'   => __( 'Widgets', 'radio-station' ),
		'roles'     => __( 'Roles', 'radio-station' ),
	),

	// --- Section Labels ---
	// 2.3.2: add widget loading section
	// 2.3.3.9: added profile pages section
	// 2.4.0.6: added performance section
	// 2.4.0.6: added posttypes and taxonomies archive sections
	'sections' => array(
		'broadcast'   => __( 'Broadcast', 'radio-station' ),
		'station'     => __( 'Station', 'radio-station' ),
		'feeds'       => __( 'Feeds', 'radio-station' ),
		'performance' => __( 'Performance', 'radio-station' ),
		'basic'       => __( 'Basic Defaults', 'radio-station' ),
		'advanced'    => __( 'Advanced Defaults', 'radio-station' ),
		'colors'      => __( 'Player Colors', 'radio-station' ),
		'bar'         => __( 'Sitewide Bar Player', 'radio-station' ),
		'schedule'    => __( 'Schedule Page', 'radio-station' ),
		'single'      => __( 'Single Templates', 'radio-station' ),
		// 'archive'     => __( 'Archive Templates', 'radio-station' ),
		'show'        => __( 'Show Pages', 'radio-station' ),
		'profile'     => __( 'Profile Pages', 'radio-station' ),
		'episode'     => __( 'Episode Pages', 'radio-station' ),
		'archives'    => __( 'Archives', 'radio-station' ),
		'posttypes'   => __( 'Post Types', 'radio-station' ),
		'taxonomies'  => __( 'Taxonomies', 'radio-station' ),
		'loading'     => __( 'Widget Loading', 'radio-station' ),
		'permissions' => __( 'Permissions', 'radio-station' ),
	),
);
