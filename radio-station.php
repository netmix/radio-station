<?php
/**
 * @package Radio Station
 * @version 2.3.0
 */
/*
Plugin Name: Radio Station
Plugin URI: https://netmix.com/radio-station
Description: Adds Show pages, DJ role, playlist and on-air programming functionality to your site.
Author: Tony Zeoli <tonyzeoli@netmix.com>
Version: 2.2.9
Text Domain: radio-station
Domain Path: /languages
Author URI: https://netmix.com/radio-station
GitHub Plugin URI: netmix/radio-station

Copyright 2019 Digital Strategy Works  (email : info@digitalstrategyworks.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// === Setup ===
// - Include Plugin Files
// - Plugin Options and Defaults
// - Plugin Loader Settings
// - Start Plugin Loader Instance
// - Include Plugin Admin Files
// - Load Plugin Text Domain
// - Check Plugin Version
// - Flush Rewrite Rules
// - Enqueue Plugin Script
// - Enqueue Plugin Stylesheet
// - Localize Time Strings
// === Template Filters ===
// ? Add Rewrite Rules
// - Automatic Pages Content Filter
// - Single Content Template Filter
// - Show Content Template Filter
// - Playlist Content Template Filter
// - Override Content Template Filter
// - Playlist Archive Show Filtering
// - DJ / Host / Author Template Fix
// - Get DJ / Host Template
// - Get Producer Template
// - Single Template Hierarchy
// - Single Templates Loader
// - Archive Template Hierarchy
// x Archive Templates Loader
// - Show Archive Page Content
// - Show Playlist Page Content
// === User Roles ===
// - Set Roles and Capabilities
// - maybe Revoke Edit Show Capability


// -------------
// === Setup ===
// -------------

define( 'RADIO_STATION_DIR', dirname( __FILE__ ) );
define( 'RADIO_STATION_HOME_URL', 'https://netmix.com/radio-station/' );
define( 'RADIO_STATION_DOCS_URL', 'https://netmix.com/radio-station/docs/' );
define( 'RADIO_STATION_API_DOCS_URL', 'https://netmix.com/radio-station/docs/api/' );

// --------------------
// Include Plugin Files
// --------------------
// 2.3.0: include new data feeds file
require RADIO_STATION_DIR . '/includes/post-types.php';
require RADIO_STATION_DIR . '/includes/master-schedule.php';
require RADIO_STATION_DIR . '/includes/shortcodes.php';
require RADIO_STATION_DIR . '/includes/support-functions.php';
require RADIO_STATION_DIR . '/includes/data-feeds.php';
require RADIO_STATION_DIR . '/includes/class-dj-upcoming-widget.php';
require RADIO_STATION_DIR . '/includes/class-dj-widget.php';
require RADIO_STATION_DIR . '/includes/class-playlist-widget.php';

// ---------------------------
// Plugin Options and Defaults
// ---------------------------
// 2.3.0: added plugin options
$timezones = radio_station_get_timezone_options( true );
$languages = radio_station_get_language_options( true );
$options = array(

	// --- Broadcast --- 
 	'streaming_url'		=>	array(
							'type' 		=> 'text',
							'options'	=> 'URL',
							'label'		=> __( 'Streaming URL', 'radio-station' ),
							'default'	=> '',
							'helper'	=> __( 'Enter the Streaming URL for your Radio Station.', 'radio-station'),
							'tab'		=> 'general',
							'section'	=> 'broadcast',
						),
	'radio_language'	=> array(
							'type'		=> 'select',
							'options'	=> $languages,
							'label'		=> __( 'Main Broadcast Language', 'radio-station' ),
							'default'	=> '',
							'helper'	=> __( 'Select the main language used on your Radio Station.', 'radio-station' ),
							'tab'		=> 'general',
							'section'	=> 'broadcast',
						),
						
	// --- Times ---
 	'timezone_location'	=> array(
							'type' 		=> 'select',
							'options'	=> $timezones,
							'label'		=> __( 'Location Timezone', 'radio-station' ),
							'default'	=> '',
							'helper'	=> __( 'Select your Broadcast Location for Timezone display.', 'radio-station'),
							'tab'		=> 'general',
							'section'	=> 'times',
						),
	'clock_time_format' => array(
							'type'		=> 'select',
							'options'	=> array(
								'12'	=> __( '12 Hour Format', 'radio-station'),
								'24'	=> __( '24 Hour Format', 'radio-station'),
							),
							'label'		=> __( 'Clock Time Format', 'radio-station'),
							'default'	=> '12',
							'helper'	=> __( 'Default Time Format Display for plugin output. Can be overridden in each shortcode or widget.', 'radio-station' ),
							'tab'		=> 'general',
							'section'	=> 'times',
						),
					

	// [Pro] Alternative Stream URL ?
	// 'streaming_alt' => array(
	//						'type'		=> 'text',
	//						'options'	=> 'URL',
	//						'label'		=> __( 'Fallback Stream URL', 'radio-station' ),
	//						'default'	=> __( 'Enter ane alternative fallback streaming URL.', 'radio-station' ),
	// 						'tab'		=> 'general',
	//						'section'	=> 'broadcast',
	//						'pro'		=> true,
	// ),

	'enable_data_routes'	=> array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Enable Data Routes', 'radio-station' ),
							'default'	=> 'yes',
							'value'		=> 'yes',
							'helper'	=> __( 'Enables Station Data Routes via WordPress REST API.', 'radio-station'),
							'tab'		=> 'general',
							'section'	=> 'feeds',
						),
	'enable_data_feeds' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Enable Data Feeds', 'radio-station' ),
							'default'	=> 'yes',
							'value'		=> 'yes',
							'helper'	=> __( 'Enable Station Data Feeds via WordPress feed links.', 'radio-station' ),
							'tab'		=> 'general',
							'section'	=> 'feeds',
						),
	'show_shift_feeds' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Show Shift Feeds', 'radio-station' ),
							'default'	=> 'yes',
							'value'		=> 'yes',
							'helper'	=> __( 'Convert RSS Feeds for a single Show to a Show shift feed, allowing a visitor to subscribe to a Show feed to be notified of Show shifts.', 'radio-station' ),
							'tab'		=> 'general',
							'section'	=> 'feeds',
							'pro'		=> true,
						),
						

	// --- Master Schedule Page ---
 	'schedule_page'	=>	array(
							'type' 		=> 'select',
							'options'	=> 'PAGEID',
							'label'		=> __( 'Master Schedule Page', 'radio-station' ),
							'default'	=> '',
							'helper'	=> __( 'Select the Page you are displaying the Master Schedule on.', 'radio-station'),
							'tab'		=> 'pages',
							'section'	=> 'schedule',
						),
	'schedule_auto'	=> array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Automatic Display', 'radio-station' ),
							'default'	=> 'yes',
							'value'		=> 'yes',
							'helper'	=> __( 'Replaces selected page content with Master Schedule. Alternatively customize with the shortcode: ', 'radio-station') . ' [master-schedule]',
							'tab'		=> 'pages',
							'section'	=> 'schedule',
						),
	'schedule_view'	=>	array(
							'type' 		=> 'select',
							'label'		=> __( 'Schedule View Default', 'radio-station' ),
							'default'	=> 'table',
							'options'	=> array(
								'table' 	=> __( 'Table View', 'radio-station' ),
								'list'		=> __( 'List View', 'radio-station' ),
								'div'		=> __( 'Divs View', 'radio-station' ),
								'tabs'		=> __( 'Tabbed View', 'radio-station' ),
								'default'	=> __( 'Legacy Table', 'radio-station' ),
							),
							'helper'	=> __( 'View type to use for automatic display on Master Schedule Page.', 'radio-station' ),
							'tab'		=> 'pages',
							'section'	=> 'schedule',
						),
	// [Pro] Schedule Switcher
	'schedule_switcher'	=> array(
							'type'		=> 'checkbox',
							'label'		=> __( 'View Switching', 'radio-station' ),
							'default'	=> '',
							'value'		=> 'yes',
							'helper'	=> __( 'Enable View Switching on the Master Schedule.', 'radio-station' ),
							'tab'		=> 'pages',
							'section'	=> 'schedule',
							'pro'		=> true,
						),

	// --- Show Page ---
	'show_posts_per_page' => array(
							'type'		=> 'numeric',
							'label'		=> __( 'Posts per Page', 'radio-station' ),
							'step'		=> 1,
							'min'		=> 1,
							'max'		=> 1000,
							'default'	=> 10,
							'helper'	=> __( 'Blog Posts per page on the Show Page tab.', 'radio-station'),
							'tab'		=> 'pages',
							'section'	=> 'show',
						),
	'show_playlists_per_page' => array(
							'type'		=> 'numeric',
							'step'		=> 1,
							'min'		=> 1,
							'max'		=> 1000,
							'label'		=> __( 'Playlists per Page', 'radio-station' ),
							'default'	=> 10,
							'helper'	=> __( 'Playlists per page on the Show Page tab.', 'radio-station'),
							'tab'		=> 'pages',
							'section'	=> 'show',
						),
	// [Pro] Episodes per page
	// 'show_episodes_per_page' => array(
	//						'type'		=> 'number',
	//						'label'		=> __( 'Episodes per Page', 'radio-station' ),
	//						'step'		=> 1,
	//						'min'		=> 1,
	//						'max'		=> 1000,
	//						'default'	=> 10,
	//						'helper'	=> __( 'Number of Show Episodes per page to display on the Show page.', 'radio-station'),
	//						'tab'		=> 'pages',
	//						'section'	=> 'show',
	//						'pro'		=> true,
	//					),

	// --- Archives ---
 	'show_archive_page'	=> array(
 							'label'		=> __( 'Show Archives Page', 'radio-station' ),
							'type' 		=> 'select',
							'options'	=> 'PAGEID',						
							'default'	=> '',
							'helper'	=> __( 'Select the Page for displaying the Show archive list.', 'radio-station' ),
							'tab'		=> 'pages',
							'section'	=> 'archives',
						),
	'show_archive_auto' => array(
							'label'		=> __( 'Automatic Display', 'radio-station' ),
							'type' 		=> 'checkbox',
							'value'		=> 'yes',
							'default'	=> 'yes',						
							'helper'	=> __( 'Replaces selected page content with default Show Archive. Alternatively customize display using the shortcode:', 'radio-station') . ' [shows-archive]',
							'tab'		=> 'pages',
							'section'	=> 'archives',	
						),
	// removes / redirects from default show archive ?
	// 'show_archive_override' => array(
	//						'label'		=> __( 'Redirect Show Archive', 'radio-station' ),
	//						'type'		=> 'checkbox',
	//						'value'		=> 'yes',
	//						'default'	=> '',
	//						'helper'	=> __( '', 'radio-station' );
	//						'tab'		=> 'pages',
	//						'section'	=> 'archives',
	//					),
 	'playlist_archive_page'	=> array(
 							'label'		=> __( 'Playlist Archives Page', 'radio-station' ),
							'type' 		=> 'select',
							'options'	=> 'PAGEID',						
							'default'	=> '',
							'helper'	=> __( 'Select the Page for displaying the Playlist archive list.', 'radio-station' ),
							'tab'		=> 'pages',
							'section'	=> 'archives',
						),
	'playlist_archive_auto' => array(
							'label'		=> __( 'Automatic Display', 'radio-station' ),
							'type' 		=> 'checkbox',
							'value'		=> 'yes',
							'default'	=> 'yes',						
							'helper'	=> __( 'Replaces selected page content with default Playlist Archive. Alternatively customize display using the shortcode:', 'radio-station') . ' [playlists-archive]',
							'tab'		=> 'pages',
							'section'	=> 'archives',	
						),
	// removes / redirects from default playlist archive ?
	// 'playlist_archive_override' => array(
	//						'label'		=> __( 'Redirect Playlist Archive', 'radio-station' ),
	//						'type'		=> 'checkbox',
	//						'value'		=> 'yes',
	//						'default'	=> '',
	//						'helper'	=> __( '', 'radio-station' );
	//						'tab'		=> 'pages',
	//						'section'	=> 'archives',
	//					),
 	'genre_archive_page'	=> array(
 							'label'		=> __( 'Genre Archives Page', 'radio-station' ),
							'type' 		=> 'select',
							'options'	=> 'PAGEID',						
							'default'	=> '',
							'helper'	=> __( 'Select the Page for displaying the Genre archive list.', 'radio-station' ),
							'tab'		=> 'pages',
							'section'	=> 'archives',
						),
	'genre_archive_auto' => array(
							'label'		=> __( 'Automatic Display', 'radio-station' ),
							'type' 		=> 'checkbox',
							'value'		=> 'yes',
							'default'	=> 'yes',						
							'helper'	=> __( 'Replaces selected page content with default Genre Archive. Alternatively customize display using the shortcode:', 'radio-station') . ' [genres-archive]',
							'tab'		=> 'pages',
							'section'	=> 'archives',	
						),
	// removes / redirects from default genre archive ?
	// 'genre_archive_override' => array(
	//						'label'		=> __( 'Redirect Genres Archive', 'radio-station' ),
	//						'type'		=> 'checkbox',
	//						'value'		=> 'yes',
	//						'default'	=> '',
	//						'helper'	=> __( '', 'radio-station' );
	//						'tab'		=> 'pages',
	//						'section'	=> 'archives',
	//					),

	// --- Templates ---
	'templates_change_note' => array(
							'type'		=> 'note',
							'label'		=> __( 'Templates Change Note', 'radio-station' ),
							'helper'	=> __( 'Since 2.3.0, the way that Templates are implemented has changed.', 'radio-station' )
										. ' ' . __( 'See the Documentation for more information:', 'radio-station' )
										. ' ' . '<a href="' . RADIO_STATION_DOCS_URL . 'templates/" target="_blank">' . __( 'Templates Documentation', 'radio-station' ) . '</a>',
							 'tab'		=> 'templates',
							 'section'	=> 'single',
						),
	'show_template' => array(
							'label'		=> __( 'Show Template', 'radio-station' ),
							'type'		=> 'select',
							'options'	=> array(
								'page'		=> __( 'Theme Page Template (page.php)', 'radio-station' ),
								'post'		=> __( 'Theme Post Template (single.php)', 'radio-station' ),
								'singular'	=> __( 'Theme Singular Template (singular.php)', 'radio-station' ),
								'legacy'	=> __( 'Legacy Plugin Template', 'radio-station' ),
							),
							'default'	=> 'page',
							'helper'	=> __( 'Which template to use for displaying Show content.', 'radio-station' ),
							'tab'		=> 'templates',
							'section'	=> 'single',
						),
	'show_template_combined' => array(
							'label'		=> __( 'Combined Method', 'radio-station' ),
							'type'		=> 'checkbox',
							'value'		=> 'yes',
							'default'	=> '',
							'helper'	=> __( 'Advanced usage. Use both a custom template AND content filtering for a Show. (Not compatible with Legacy templates.)', 'radio-station'),
							'tab'		=> 'templates',
							'section'	=> 'single',
						),
	'playlist_template' => array(
							'label'		=> __( 'Playlist Template', 'radio-station' ),
							'type'		=> 'select',
							'options'	=> array(
								'page'	=> __( 'Theme Page Template (page.php)', 'radio-station'),
								'post'	=> __( 'Theme Post Template (single.php)', 'radio-station'),
								'legacy' => __( 'Legacy Plugin Template', 'radio-station' ),
							),
							'default'	=> 'page',
							'helper'	=> __( 'Which template to use for displaying Playlist content.', 'radio-station' ),
							'tab'		=> 'templates',
							'section'	=> 'single',
						),
	'playlist_template_combined' => array(
							'label'		=> __( 'Combined Method', 'radio-station' ),
							'type'		=> 'checkbox',
							'value'		=> 'yes',
							'default'	=> '',
							'helper'	=> __( 'Advanced usage. Use both a custom template AND content filtering for a Playlist. (Not compatible with Legacy templates.)', 'radio-station'),
							'tab'		=> 'templates',
							'section'	=> 'single',
						),


	// --- Roles / Capabilities / Permissions  ---
	// 2.3.0: added new capability and role options
	'show_editor_role_note' => array(
							'type'		=> 'note',
							'label'		=> __( 'Show Editor Role', 'radio-station' ),
							'helper'	=> __( 'Since 2.3.0, a new Show Editor role has been added with Publish and Edit capabilities for all Radio Station Post Types.', 'radio-station' )
								. ' ' . __( 'You can assign this Role to any user to give them full Station Schedule updating permissions.', 'radio-station' ),
							'tab'		=> 'roles',
							'section'	=> 'permissions',
						),
	// 'add_show_editor_role'	=> array(
	//						'type'		=> 'checkbox',
	//						'label'		=> __( 'Enable Show Editor Role', 'radio-station' ),
	//						'default'	=> '',
	//						'value'		=> 'yes',
	//						'helper'	=> __( 'Adds a separate role that can publish and edit all Radio Station post types.', 'radio-station' ),
	//						'tab'		=> 'roles',
	//						'section'	=> 'permissions',
	//					),

	'add_author_capabilities' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Add to Author Capabilities', 'radio-station' ),
							'default'	=> 'yes',
							'value'		=> 'yes',
							'helper'	=> __( 'Allow users with WordPress Author role to publish and edit their own Shows and Playlists.', 'radio-station' ),
							'tab'		=> 'roles',
							'section'	=> 'permissions',
						),
	'add_editor_capabilities' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Add to Editor Capabilities', 'radio-station' ),
							'default'	=> 'yes',
							'value'		=> 'yes',
							'helper'	=> __( 'Allow users with WordPress Editor role to edit all Radio Station post types.', 'radio-station' ),
							'tab'		=> 'roles',
							'section'	=> 'permissions',
						),
						
	// 'disallow_shift_changes' => array(
	//						'type'		=> 'checkbox',
	//						'label'		=> __( 'Disallow Shift Changes', 'radio-station' ),
	//						'default'	=> array(),
	//						'options'	=> array(
	//							'authors'	=> __( 'WordPress Authors', 'radio-station' ),
	//							'editors'	=> __( 'WorddPress Editors', 'radio-station' ),
	//							'hosts'		=> __( 'Assigned DJs / Hosts', 'radio-station' ),
	//							'producers'	=> __( 'Assigned Producers', 'radio-station' ),
	//						),
	//						'helper'	=> __( 'Prevents users of these Roles changing Show Shift times.', 'radio-station' ),
	//						'tab'		=> 'roles',
	//						'section'	=> 'permissions',
	//						'pro'		=> true,
	//					),

	// --- Tabs and Sections ---
	'tabs'			=>	array(
							'general'		=> __( 'General', 'radio-station' ),
							'pages'			=> __( 'Pages', 'radio-station' ),
							'templates'		=> __( 'Templates', 'radio-station' ),
							'roles'			=> __( 'Roles', 'radio-station' ),
						),
	'sections'		=>	array(
							'station'			=> __( 'Station', 'radio-station' ),
							'broadcast'			=> __( 'Broadcast', 'radio-station' ),
							'times'				=> __( 'Times', 'radio-station' ),
							'feeds'				=> __( 'Feeds', 'radio-station' ),
							'single'			=> __( 'Single Templates', 'radio-station' ),
							'archive'			=> __( 'Archive Templates', 'radio-station' ),
							'schedule'			=> __( 'Schedule Page', 'radio-station' ),
							'show'				=> __( 'Show Pages', 'radio-station' ),
							'archives'			=> __( 'Archives', 'radio-station' ),
							'permissions'		=> __( 'Permissions', 'radio-station'),
						),
	);

// ----------------------
// Plugin Loader Settings
// ----------------------
// 2.3.0: added plugin loader settings
$slug = 'radio-station';
$args = array(
	// --- Plugin Info ---
	'slug'			=> $slug,
	'file'			=> __FILE__,
	'version'		=> '0.0.1',

	// --- Menus and Links ---
	'title'			=> 'Radio Station',
	'parentmenu'	=> 'radio-station',
	'home'			=> 'https://netmix.com/radio-station/',
	'support'		=> 'https://github.com/netmix/radio-station/issues/',
	'ratetext'		=> __( 'Rate on WordPress.org', 'radio-station' ),
	// 'share'			=> 'https://netmix.com/radio-station/#share',
	// 'sharetext'		=> __( 'Share the Plugin Love', 'radio-station' ),
	'donate'		=> 'https://patreon.com/radiostation',
	'donatetext'	=> __( 'Support this Plugin',  'radio-station' ),
	'readme'		=> false,
	'settingsmenu'	=> false,

	// --- Options ---
	'namespace'		=> 'radio_station',
	'settings'		=> 'rs',
	'option'		=> 'radio_station',
	'options'		=> $options,

	// --- WordPress.Org ---
	'wporgslug'		=> 'radio-station',
	'wporg'			=> true,
	'textdomain'	=> 'radio-station',

	// --- Freemius ---
	'freemius_id'	=> '4526',
	'freemius_key'	=> 'pk_aaf375c4fb42e0b5b3831e0b8476b',
	'hasplans'		=> false,
	'hasaddons'		=> false,
	'plan'			=> 'free',
);

// ----------------------------
// Start Plugin Loader Instance
// ----------------------------
require RADIO_STATION_DIR . '/loader.php';
$instance = new radio_station_loader( $args );

// --------------------------
// Include Plugin Admin Files
// --------------------------
// 2.2.7: added conditional load of admin includes
// 2.2.7: moved all admin functions to radio-station-admin.php
if ( is_admin() ) {
	require RADIO_STATION_DIR . '/radio-station-admin.php';
	require RADIO_STATION_DIR . '/includes/post-types-admin.php';
}

// -----------------------
// Load Plugin Text Domain
// -----------------------
add_action( 'plugins_loaded', 'radio_station_init' );
function radio_station_init() {
	// 2.3.0: use RADIO_STATION_DIR constant
	load_plugin_textdomain( 'radio-station', false, RADIO_STATION_DIR . '/languages' );
}

// --------------------
// Check Plugin Version
// --------------------
// 2.3.0: check plugin version for updates and announcements
add_action( 'init', 'radio_station_check_version', 9 );
function radio_station_check_version() {

	// --- get current and stored versions ---
	$version = radio_station_loader_instance()->version;
	$stored_version = get_option( 'radio_station_version', false );

	// --- check current against stored version ---
	if ( !$stored_version ) {
	
		// --- no stored plugin version, add it now ---
		update_option( 'radio_station_version', $version );
		
		if ( version_compare( $version, '2.3.0', '>=' ) ) {
			// --- flush rewrite rules (for new post type and rest route rewrites) ---
			// (handled separately as 2.3.0 is first version with version checking)
			add_option( 'radio_station_flush_rewrite_rules', true );
		}
		
	} elseif ( version_compare( $version, $stored_version, '>' ) ) {

		// --- updates from before to after x.x.x ---
		// (example code template if neede for future release updates)
		// if ( ( version_compare( $version, 'x.x.x', '>=' ) )
		//   && ( version_compare( $stored_version, 'x.x.x', '<' ) ) ) {
		//		// eg. trigger a single thing to do
		//		add_option( 'radio_station_do_thing_once', true );
		// }

		// --- bump stored version to current version ---
		update_option( 'radio_station_previous_version', $stored_version );
		update_option( 'radio_station_version', $version );
	}
}

// -------------------
// Flush Rewrite Rules
// -------------------
// (on plugin activation / deactivation)
// 2.2.3: added this for custom post types rewrite flushing
// 2.2.8: fix for mismatched flag function name
register_activation_hook( __FILE__, 'radio_station_flush_rewrite_flag' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
function radio_station_flush_rewrite_flag() {
	add_option( 'radio_station_flush_rewrite_rules', true );
}

// ----------------------
// Enqueue Plugin Scripts
// ----------------------
// 2.3.0: added for enqueueing main Radio Station script
add_action( 'wp_enqueue_scripts', 'radio_station_enqueue_scripts' );
function radio_station_enqueue_scripts() {
	radio_station_enqueue_script( 'radio-station' );
}

// ---------------------
// Enqueue Plugin Script
// ---------------------
function radio_station_enqueue_script( $scriptkey ) {

	// --- set stylesheet filename and child theme path ---
	$filename = $scriptkey . '.js';
	$theme_path = get_stylesheet_directory();

	if ( file_exists( $theme_path . '/js/' . $filename ) ) {
		// --- child theme JS subdirectory ---
		$version = filemtime( $theme_path  . '/js/' . $filename );
		$url     = get_stylesheet_directory_uri() . '/js/' . $filename;
	} elseif ( file_exists( $theme_path . '/' . $filename ) ) {
		// --- child theme directory ---
		$version = filemtime( $theme_path  . '/' . $filename );
		$url     = get_stylesheet_directory_uri() . '/' . $filename;
	} elseif ( file_exists( RADIO_STATION_DIR . '/js/' . $filename ) ) {
		// --- plugin JS subdirectory (default) ---
		$version = filemtime( RADIO_STATION_DIR . '/js/' . $filename );
		$url     = plugins_url( 'js/' . $filename, __FILE__ );
	} else {
		// --- missing javascript file ---
		// (could mean plugin is corrupt)
		echo "<!-- Warning: missing file: ".$filename." -->";
	}

	// --- enqueue script ---
	wp_enqueue_script( $scriptkey, $url, array(), $version );
}

// -------------------------
// Enqueue Plugin Stylesheet
// -------------------------
// ?.?.?: widgets.css style conditional enqueueing moved to within widget classes
// 2.3.0: added abstracted method for enqueueing plugin stylesheets
// 2.3.0: moved master schedule style enqueueing to conditional in master-schedule.php
function radio_station_enqueue_style( $stylekey ) {

	// --- check style enqueued switch ---
	global $radio_station_styles;
	if ( !isset( $radio_station_styles ) ) {$radio_station_styles = array();}
	if ( !isset( $radio_station_styles[$stylekey] ) ) {
	
		// --- set stylesheet filename and child theme path ---
		$filename = 'rs-' . $stylekey . '.css';
		$theme_path = get_stylesheet_directory();
		
		if ( file_exists( $theme_path . '/css/' . $filename ) ) {
			// --- child theme CSS subdirectory ---
			$version = filemtime( $theme_path  . '/css/' . $filename );
			$url     = get_stylesheet_directory_uri() . '/css/' . $filename;
		} elseif ( file_exists( $theme_path . '/' . $filename ) ) {
			// --- child theme directory ---
			$version = filemtime( $theme_path  . '/' . $filename );
			$url     = get_stylesheet_directory_uri() . '/' . $filename;
		} elseif ( file_exists( RADIO_STATION_DIR . '/css/' . $filename ) ) {
			// --- plugin CSS subdirectory (default) ---
			$version = filemtime( RADIO_STATION_DIR . '/css/' . $filename );
			$url     = plugins_url( 'css/' . $filename, __FILE__ );
		} else {
			// --- missing plugin file ---
			// (could mean plugin is corrupt)		
			echo "<!-- Warning: missing file: ".$filename." -->";
		}
		
		// --- enqueue styles in footer ---
		wp_enqueue_style( 'rs-' . $stylekey, $url, array(), $version, 'all' );
		
		// --- set style enqueued switch ---
		$radio_station_styles[$stylekey] = true;
	}
}

// ---------------------
// Localize Time Strings
// ---------------------
add_action( 'wp_footer', 'radio_station_localize_time_strings' );
function radio_station_localize_time_strings() {

	// --- output localized time variables ---
	echo "<script>";
	
		// --- clock time format ---
		$clock_time_format = radio_station_get_setting( 'clock_time_format' );
		echo "var radio_clock_format = '" . $clock_time_format . "'; ";
		echo "var radio_am = '" . radio_station_translate_meridiem( 'am' ) . "'; ";
		echo "var radio_pm = '" . radio_station_translate_meridiem( 'pm' ) . "'; ";
		
		// --- radio timezone ---
		$timezone = radio_station_get_setting( 'timezone_location' );
		if ( !$timezone || ( $timezone == '' ) ) {
			// --- fallback to WordPress timezone ---
			$timezone = get_option( 'timezone_string' );
			if ( false !== strpos($timezone,'Etc/GMT') ) {$timezone = '';}
			if ( $timezone == '' ) {
				$offset = get_option( 'gmt_offset' );
			}
		}		
		if ( isset( $offset ) ) {
			if ( !$offset ) {$offset = 0;}
			// $offset = intval( $offset ) * 60 * 60 * 1000;
			echo "var radio_timezone_offset = " . $offset. "; ";
			if ( $offset == 0 ) {$offset = '';}
			elseif ( $offset > 0 ) {$offset = '+' . $offset;}
			echo "var radio_timezone_code = 'UTC" . $offset. "'; ";
			echo "var radio_timezone_utc = '" . $offset ."'; ";
		} else {
			$datetimezone = new DateTimeZone( $timezone );
			$offset = $datetimezone->getOffset(new DateTime);
			if ( $offset == 0 ) {$utc_offset = '';}
			elseif ( $offset > 0 ) {$utc_offset = '+' . $offset;}
			else {$utc_offset = $offset;}
			$utc_offset = 'UTC' . $utc_offset;
			$code = radio_station_get_timezone_code( $timezone );
			echo "var radio_timezone_location = '" . $timezone. "'; ";
			echo "var radio_timezone_offset = " . $offset. "; ";
			echo "var radio_timezone_code = '" . $timezone. "'; ";
			echo "var radio_timezone_utc = '" . $utc_offset . "'; ";
		}

		// --- translated months array ---
		echo "var radio_months = new Array(";
		$months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
		foreach ( $months as $i => $month ) {
			$month = radio_station_translate_month( $month );
			$month = str_replace( "'", "", $month );
			echo "'" . $month ."'";
			if ( $i < count( $months ) ) {echo ", ";}
		}
		echo ");".PHP_EOL;
		
		// --- translated days array ---
		echo "var radio_days = new Array(";
		$days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
		foreach ( $days as $i => $day ) {
			$day = radio_station_translate_weekday( $day );
			$day = str_replace( "'", "", $day );
			echo "'" . $day ."'";
			if ( $i < count( $days ) ) {echo ", ";}
		}
		echo ");".PHP_EOL;	
		
	echo "</script>";
}
	
// ------------------------
// === Template Filters ===
// ------------------------

// -----------------
// Add Rewrite Rules
// -----------------
// (not implemented as custom post types registered)
// add_action( 'init', 'radio_station_add_rewrite_rules' );
function radio_station_add_rewrite_rules() {

	global $wp;
	$wp->add_query_var( 'host' );
	$wp->add_query_var( 'producer' );
	
	// --- Host Single Page ---
	// $host_page = radio_station_get_setting( 'host_page' );
	// add_rewrite_rule(
	//	$basename . '/?([^/]*)',
	//  	'index.php?page_id=' . $host_page . '&host=$matches[1]',
	//  	'top'
	// );
	
}

// ------------------------------
// Automatic Pages Content Filter
// ------------------------------
// 2.3.0: standalone filter for automatic page content
add_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
function radio_station_automatic_pages_content( $content ) {

	// --- for automatic output on selected master schedule page ---
	$schedule_page = radio_station_get_setting( 'schedule_page' );
	if ( !is_null( $schedule_page ) && ( $schedule_page != '' ) ) {
		if ( is_page( $schedule_page ) ) {
			$automatic = radio_station_get_setting( 'schedule_auto' );
			if ( $automatic == 'yes' ) {
				$view = radio_station_get_setting( 'schedule_view' );
				return do_shortcode( '[master-schedule list="' . $view . '"]' );
			}
		}
	}

	// --- show archive page ---
	// 2.3.0: added automatic display of show archive page
	$show_archive_page = radio_station_get_setting( 'show_archive_page' );
	if ( !is_null( $show_archive_page ) && ( $show_archive_page != '' ) ) {
		if ( is_page( $show_archive_page ) ) {
			$automatic = radio_station_get_setting( 'show_archive_auto' );
			if ( $automatic == 'yes' ) {
				// $view = radio_station_get_setting( 'show_archive_view' );
				$view = '';
				return do_shortcode( '[shows-archive view="' . $view . '"]' );
			}
		}
	}

	// --- playlist archive page ---
	// 2.3.0: added automatic display of playlist archive page
	$playlist_archive_page = radio_station_get_setting( 'playlist_archive_page' );
	if ( !is_null( $playlist_archive_page ) && ( $playlist_archive_page != '' ) ) {
		if ( is_page( $playlist_archive_page ) ) {
			$automatic = radio_station_get_setting( 'playlist_archive_auto' );
			if ( $automatic == 'yes' ) {
				// $view = radio_station_get_setting( 'playlist_archive_view' );
				$view = '';
				return do_shortcode( '[playlists-archive view="' . $view . '"]' );
			}
		}
	}
	
	// --- genre archive page ---
	// 2.3.0: added automatic display of genre archive page
	$genre_archive_page = radio_station_get_setting( 'genre_archive_page' );
	if ( !is_null( $genre_archive_page ) && ( $genre_archive_page != '' ) ) {
		if ( is_page( $genre_archive_page ) ) {
			$automatic = radio_station_get_setting( 'genre_archive_auto' );
			if ( $automatic == 'yes' ) {
				// $view = radio_station_get_setting( 'genre_archive_view' );
				$view = '';
				return do_shortcode( '[genres-archive view="' . $view . '"]' );
			}
		}
	}

	return $content;
}

// ------------------------------
// Single Content Template Filter
// ------------------------------
// 2.3.0: moved here and abstracted from templates/single-show.php
// 2.3.0: standalone filter name to allow for replacement
function radio_station_single_content_template( $content, $post_type ) {

	// --- check if single plugin post type ---
	if ( !is_singular( $post_type ) ) {return $content;}

	// --- check for user content templates ---
	$theme_dir = get_stylesheet_directory();
	$templates = array(
		$theme_dir . '/templates/single-' . $post_type . '-content.php',
		$theme_dir . '/single-' . $post_type . '-content.php',
		RADIO_STATION_DIR . '/templates/single-' . $post_type . '-content.php',
	);
	$templates = apply_filters( 'radio_station_' . $post_type . '_content_templates', $templates, $post_type );
	foreach ( $templates as $template ) {
		if ( file_exists( $template ) ) {$content_template = $template; break;}
	}
	if ( !isset( $content_template ) ) {return $content;}

	// --- start buffer and include content template ---
	ob_start(); include( $content_template );

	// --- enqueue template styles ---
	radio_station_enqueue_style( 'templates' );

	// --- enqueue dashicons for frontend ---
	wp_enqueue_style( 'dashicons' );

	// --- filter and return buffered content ---
	$output = ob_get_contents(); ob_end_clean();
	$output = str_replace( '<!-- the_content -->', $content, $output );
	$output = apply_filters( 'radio_station_content_' . $post_type, $output, get_the_ID() );
	return $output;
}

// ----------------------------
// Show Content Template Filter
// ----------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_show_content_template', 11 );
function radio_station_show_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_show_content_template', 11 );
	return radio_station_single_content_template( $content, 'show' );
}

// --------------------------------
// Playlist Content Template Filter
// --------------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
function radio_station_playlist_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
	return radio_station_single_content_template( $content, 'playlist' );
}

// --------------------------------
// Override Content Template Filter
// --------------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_override_content_template', 11 );
function radio_station_override_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_override_content_template', 11 );
	return radio_station_single_content_template( $content, 'override' );
}

// -------------------------------
// Playlist Archive Show Filtering
// -------------------------------
// 2.3.0: added to replace old archive template meta query
add_filter( 'pre_get_posts', 'radio_station_show_playlist_query' );
function radio_station_show_playlist_query( $query ) {

	if ( $query->get( 'post_type' ) == 'playlist' ) {

		// --- not needed if using legacy template ---
		$theme_dir = get_stylesheet_directory();
		if ( file_exists( $theme_dir . '/archive-playlist.php' ) 
		  || file_exists( $theme_dir . '/templates/archive-playlist.php' ) ) {return;}

		// --- check if show ID or slug is set --
		if ( isset( $_GET['show_id'] ) ) {
			$show_id = absint( $_GET['show_id'] );
			if ( $show_id < 0 ) {unset( $show_id );}
		} elseif ( isset( $_GET['show'] ) ) {
			$show = sanitize_title( $_GET['show'] );
			global $wpdb;
			$show_query = "SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'show' AND post_name = %s";
			$show_query = $wpdb->prepare( $show_query, $show );
			$show_id = $wpdb->get_var( $show_query );
			if ( !$show_id ) {unset( $show_id );}
		}

		// --- maybe add the playlist meta query ---
		if ( isset( $show_id ) ) {
			$meta_query = array(
				'key'	=> 'playlist_show_id',
				'value'	=> $show_id,
			);
			$query->set( $meta_query );
		}
	}
}


// -------------------------------
// DJ / Host / Author Template Fix
// -------------------------------
// 2.2.8: temporary fix to not 404 author pages for DJs without blog posts
// Ref: https://wordpress.org/plugins/show-authors-without-posts/
add_filter( '404_template', 'radio_station_author_host_pages' );
function radio_station_author_host_pages( $template ) {

	global $wp_query;
	if ( !is_author() ) {

		if ( get_query_var( 'host' ) ) {

			// --- get user by ID or name ---
			$host = get_query_var( 'host' );
			if ( absint( $host ) > -1 ) {$user = get_user_by( 'ID', $host );}
			else {$user = get_user_by( 'slug', $host );}

			// --- check if specified user has DJ role ---
			if ( $user && in_array( 'dj', $user->roles ) ) {
				$host_template = radio_station_get_host_template();
				if ( $host_template ) {$template = $host_template;}
			}

		} elseif ( get_query_var( 'producer' ) ) {

			// --- get user by ID or name ---
			$producer = get_query_var( 'producer' );
			if ( absint( $producer ) > -1 ) {$user = get_user_by( 'ID', $producer );}
			else {$user = get_user_by( 'slug', $producer );}

			// --- check if specified user has DJ role ---
			if ( $user && in_array( 'producer', $user->roles ) ) {
				$producer_template = radio_station_get_producer_template();
				if ( $producer_template ) {$template = $producer_template;}
			}

		} elseif ( get_query_var( 'author' ) && ( 0 == $wp_query->posts->post ) ) {

			// --- get the author user ---
			if ( get_query_var( 'author_name' ) ) {
				$author =  get_user_by( 'slug', get_query_var( 'author_name' ) );
			} else {
				$author = get_userdata( get_query_var( 'author' ) );
			}
			
			if ( $author ) {
			
				// --- check if author has DJ, producer or administrator role ---
				if ( in_array( 'dj', $author->roles ) 
				  || in_array( 'producer', $author->roles ) 
				  || in_array( 'administrator', $author->roles ) ) {
				  
					// TODO: maybe check if user is assigned to any shows ?
					$template = get_author_template();
				}
			}

		} 

	}
	return $template;
}

// ----------------------
// Get DJ / Host Template
// ----------------------
// 2.3.0: added get DJ template function
// (modified template hierarchy from get_page_template)
function radio_station_get_host_template() {

	$templates = array();
	$hostname = get_query_var( 'host' );
	if ( $hostname ) {
		$hostname_decoded = urldecode( $hostname );
		if ( $hostname_decoded !== $hostname ) {
			$templates[] = 'host-' . $hostname_decoded . '.php';
		}
		$templates[] = 'host-' . $hostname . '.php';
	}
	$templates[] = 'single-host.php';

	$templates = apply_filters( 'radio_station_host_templates', $templates );
    return get_query_template( 'host', $templates );
}

// ---------------------
// Get Producer Template
// ---------------------
// 2.3.0: added get producer template function
// (modified template hierarchy from get_page_template)
function radio_station_get_producer_template() {

	$templates = array();
	$producername = get_query_var( 'producer' );
	if ( $producername ) {
		$producername_decoded = urldecode( $producername );
		if ( $producername_decoded !== $producername ) {
			$templates[] = 'producer-' . $producername_decoded . '.php';
		}
		$templates[] = 'producer-' . $producername . '.php';
	}
	$templates[] = 'single-producer.php';
	
	$templates = apply_filters( 'radio_station_producer_templates', $templates );
    return get_query_template( 'producer', $templates );
}

// -------------------------
// Single Template Hierarchy
// -------------------------
function radio_station_single_template_hierarchy( $templates ) {

	global $post;

	// --- remove single.php as the show / playlist fallback ---
	// (allows for user selection of page.php or single.php later)
	if ( ( $post->post_type == 'show') 
	  || ( $post->post_type == 'playlist') ) {	
		$i = array_search( 'single.php', $templates );
		if ( $i !== false ) {unset( $templates[$i] );}
	}
	return $templates;
}

// -----------------------
// Single Templates Loader
// -----------------------
add_filter( 'single_template', 'radio_station_load_template', 10, 3 );
function radio_station_load_template( $single_template, $type, $templates ) {

	global $post;

	// --- handle single templates ---
	$post_type = $post->post_type;
	$post_types = array( 'show', 'playlist' ); // 'host', 'producer' );
	if ( in_array( $post_type, $post_types ) ) {
		
		// $user_template = get_stylesheet_directory() . '/single-' . $post_type . '.php';
		
		// --- check for existing template override ---
		// note: single.php is removed from template hierarchy via filter
		remove_filter( 'single_template', 'radio_station_load_template' );
		add_filter( 'single_template_hierarchy', 'radio_station_single_template_hierarchy' );
		$template = get_single_template();
		remove_filter( 'single_template_hierarchy', 'radio_station_single_template_hierarchy' );
		
		// --- use legacy template ---
		if ( $template ) {
			
			// --- use the found user template ---
			$single_template = $template;
			
			// --- check for combined template and content filter ---
			$combined = radio_station_get_setting( $post_type . '_template_combined' );
			if ( $combined != 'yes' ) {
				remove_filter( 'the_content', 'radio_station_' . $post_type . '_content_template', 11 );
			}

		} else {

			// --- get template selection ---
			// 2.3.0: removed default usage of single show/playlist templates (not theme agnostic)
			// 2.3.0: added option for use of template hierarchy
			$show_template = radio_station_get_setting( $post_type . '_template' );

			// --- maybe use legacy template ---
			if ( $show_template == 'legacy' ) {
				return RADIO_STATION_DIR . '/templates/legacy/single-' . $post_type .' .php';
			}
			
			// --- use post or page template ---
			if ( $show_template == 'post' ) {$templates = array( 'single.php' );}
			elseif ( $show_template == 'page' ) {$templates = array ( 'page.php' );}
			
			// --- add standard fallbacks to singular and index ---
			$templates[] = 'singular.php'; $templates[] = 'index.php';
			$single_template = get_query_template( $post_type, $templates );
		}
	}

	return $single_template;
}

// --------------------------
// Archive Template Hierarchy
// --------------------------
add_filter( 'archive_template_hierarchy', 'radio_station_archive_template_hierarchy' );
function radio_station_archive_template_hierarchy( $templates ) {

	// --- add extra template search path of /templates/ ---
    $post_types = array_filter( (array) get_query_var( 'post_type' ) );
    if ( count( $post_types ) == 1 ) {
        $post_type = reset( $post_types );
        $post_types = array( 'show', 'playlist', 'host', 'producer' );
        if ( in_array( $post_type, $post_types ) ) {
        	$templates = array_merge( 'templates/archive-' . $post_type . '.php', $templates );
		}
    }

	return $templates;
}

// ------------------------
// Archive Templates Loader
// ------------------------
// add_filter( 'archive_template', 'radio_station_load_custom_post_type_template', 10, 3 );
function radio_station_load_custom_post_type_template( $archive_template, $type, $templates ) {
	global $post;

	// --- check for archive template override ---
	$post_types = array( 'show', 'playlist', 'host', 'producer' );
	foreach ( $post_types as $post_type ) {
		if ( is_post_type_archive( $post_type ) ) {
			$override = radio_station_get_setting( $post_type . '_archive_override' );
			if ( $override == 'yes' ) {
				$archive_template = get_page_template();
				add_filter( 'the_content', 'radio_station_' . $post_type . '_archive', 11 );
			}
		}
	}

	return $archive_template;
}

// -------------------------
// Show Archive Page Content
// -------------------------
function radio_station_show_archive( $content ) {
	$auto = radio_station_get_setting( 'show_archive_auto' );
	if ( $auto != 'yes' ) {return $content;}
	$shortcode = '[show-archive';
	// $view = radio_station_get_setting( 'show_archive_view' );
	// if ( $view == 'grid' ) {$shortcode .= ' view="grid"';}
	$shortcode .= ']';
	$content = do_shortcode( $shortcode );
	return $content;
}

// -----------------------------
// Playlist Archive Page Content
// -----------------------------
function radio_station_playlist_archive( $content ) {
	$auto = radio_station_get_setting( 'playlist_archive_auto' );
	if ( $auto != 'yes' ) {return $content;}
	$shortcode = '[playlist-archive';
	// $view = radio_station_get_setting( 'playlist_archive_view' );
	// if ( $view == 'grid' ) {$shortcode .= ' view="grid"';}
	$shortcode .= ']';
	$content = do_shortcode( $shortcode );
	return $content;
}


// ------------------
// === User Roles ===
// ------------------

// ----------------------------
// Set DJ Role and Capabilities
// ----------------------------
if ( is_multisite() ) {
	add_action( 'init', 'radio_station_set_roles', 10, 0 );
} else {
	add_action( 'admin_init', 'radio_station_set_roles', 10, 0 );
}
function radio_station_set_roles() {

	global $wp_roles;

	// --- set only necessary capabilities for DJs ---
	$caps = array(
		'edit_shows'				=> true,
		'edit_published_shows'		=> true,
		'edit_others_shows'			=> true,
		'read_shows'				=> true,
		'edit_playlists'			=> true,
		'edit_published_playlists'	=> true,
		// by default DJs cannot edit others playlists
		// 'edit_others_playlists'	=> true,  
		'read_playlists'			=> true,
		'publish_playlists'			=> true,
		'read'						=> true,
		'upload_files'				=> true,
		'edit_posts'				=> true,
		'edit_published_posts'		=> true,
		'publish_posts'				=> true,
		'delete_posts'				=> true,
	);

	// --- add the DJ role ---
	// 2.3.0: translate DJ role name
	// 2.3.0: change label from 'DJ' to 'DJ / Host'
	$wp_roles->add_role( 'dj', __( 'DJ / Host', 'radio-station' ), $caps );
	// 2.3.0: add profile capabilities to hosts
	$role_caps = $wp_roles->roles['dj']['capabilities'];
	$host_caps = array( 'edit_hosts', 'edit_published_hosts', 'delete_hosts', 'read_hosts', 'publish_hosts' );
	foreach ( $host_caps as $cap ) {
		if ( !array_key_exists( $cap, $role_caps ) || ( !$role_caps[$cap] ) ) {
			$wp_roles->add_cap( 'dj', $cap, true );
		}
	}

	// --- add Show Producer role ---
	// 2.3.0: add equivalent capability role for Show Producer
	$wp_roles->add_role( 'producer', __( 'Show Producer', 'radio-station' ), $caps );
	$role_caps = $wp_roles->roles['producer']['capabilities'];
	$producer_caps = array( 'edit_producers', 'edit_published_producers', 'delete_producers', 'read_producers', 'publish_producers' );
	foreach ( $producer_caps as $cap ) {
		if ( !array_key_exists( $cap, $role_caps ) || ( !$role_caps[$cap] ) ) {
			$wp_roles->add_cap( 'producer', $cap, true );
		}
	}

	// --- check plugin setting for Show Editor role ---
	// if ( radio_station_get_setting('add_show_editor_role') == 'yes' ) {

		// --- grant all capabilities to Show Editors ---
		// 2.3.0: set Show Editor role capabilities
		$caps = array(
			'edit_shows'				 => true,
			'edit_published_shows'		 => true,
			'edit_others_shows'			 => true,
			'edit_private_shows'		 => true,
			'delete_shows'				 => true,
			'delete_published_shows'	 => true,
			'delete_others_shows'		 => true,
			'delete_private_shows'		 => true,
			'read_shows'				 => true,
			'publish_shows'				 => true,
			
			'edit_playlists'			 => true,
			'edit_published_playlists'	 => true,
			'edit_others_playlists'		 => true,
			'edit_private_playlists'	 => true,
			'delete_playlists'			 => true,
			'delete_published_playlists' => true,
			'delete_others_playlists'	 => true,
			'delete_private_playlists'	 => true,
			'read_playlists'			 => true,
			'publish_playlists'			 => true,

			'edit_overrides'			=> true,
			'edit_overrides_playlists'	=> true,
			'edit_others_overrides'		=> true,
			'edit_private_overrides'	=> true,
			'delete_overrides'			=> true,
			'delete_published_overrides'=> true,
			'delete_others_overrides'	=> true,
			'delete_private_overrides'	=> true,
			'read_overrides'			=> true,
			'publish_overrides'			=> true,

			'edit_hosts'				=> true,
			'edit_published_hosts'		=> true,
			'edit_others_hosts'			=> true,
			'delete_hosts'				=> true,
			'read_hosts'				=> true,
			'publish_hosts'				=> true,

			'edit_producers'			=> true,
			'edit_published_producers'	=> true,
			'edit_others_producers'		=> true,
			'delete_producers'			=> true,
			'read_producers'			=> true,
			'publish_producers'			=> true,
			
			'read'						=> true,
			'upload_files'				=> true,
			'edit_posts'				=> true,
			'edit_others_posts'			=> true,
			'edit_published_posts'		=> true,
			'publish_posts'				=> true,
			'delete_posts'	            => true,
		);

		// --- add the Show Editor role ---
		// 2.3.0: added Show Editor role
		$wp_roles->add_role( 'show-editor', __('Show Editor', 'radio-station'), $caps );
	// }

	// --- check plugin setting for authors ---
	if ( radio_station_get_setting( 'add_author_capabilities' ) == 'yes' ) {

		// --- grant show edit capabilities to editor users ---
		$author_caps = $wp_roles->roles['author']['capabilities'];
		$extra_caps = array(
			'edit_shows', 'edit_published_shows', 'read_shows', 'publish_shows',
			'edit_playlists', 'edit_published_playlists', 'read_playlists', 'publish_playlists'
			// 'edit_overrides', 'edit_published_overrides', 'read_overrides',  'publish_overrides'
		);
		foreach ( $extra_caps as $cap ) {
			if ( !array_key_exists( $cap, $author_caps ) || ( !$author_caps[$cap] ) ) {
				$wp_roles->add_cap( 'author', $cap, true );
			}
		}	
	}

	// --- specify edit caps (for editors and admins) ---
	// 2.3.0: added show override, host and producer capabilities
	$edit_caps = array(
		'edit_shows', 'edit_published_shows', 'edit_others_shows', 'edit_private_shows',
		'delete_shows', 'delete_published_shows', 'delete_others_shows', 
		'delete_private_shows', 'read_shows', 'publish_shows',

		'edit_playlists', 'edit_published_playlists', 'edit_others_playlists', 'edit_private_playlists', 
		'delete_playlists', 'delete_published_playlists', 'delete_others_playlists', 
		'delete_private_playlists', 'read_playlists', 'publish_playlists',

		'edit_overrides', 'edit_published_overrides', 'edit_others_overrides', 'edit_private_overrides',
		'delete_overrides', 'delete_published_overrides', 'delete_others_overrides', 
		'delete_private_overrides', 'read_overrides', 'publish_overrides',

		'edit_hosts', 'edit_published_hosts', 'edit_others_hosts', 
		'delete_hosts', 'delete_others_hosts', 'read_hosts', 'publish_hosts', 

		'edit_producers', 'edit_published_producers', 'edit_others_producers',
		'delete_producers', 'delete_others_producers', 'read_producers', 'publish_producers', 
	);

	// --- check plugin setting for editors ---
	if ( radio_station_get_setting( 'add_editor_capabilities' ) == 'yes' ) {

		// --- grant show edit capabilities to editor users ---
		$editor_caps = $wp_roles->roles['editor']['capabilities'];
		foreach ( $edit_caps as $cap ) {
			if ( !array_key_exists( $cap, $editor_caps ) || ( !$editor_caps[$cap] ) ) {
				$wp_roles->add_cap( 'editor', $cap, true );
			}
		}
	}

	// --- grant all plugin capabilities to admin users ---
	$admin_caps = $wp_roles->roles['editor']['capabilities'];
	foreach ( $edit_caps as $cap ) {
		if ( !array_key_exists( $cap, $admin_caps ) || ( !$admin_caps[$cap] ) ) {
			$wp_roles->add_cap( 'administrator', $cap, true );
		}
	}

}

// ---------------------------------
// maybe Revoke Edit Show Capability
// ---------------------------------
// (revoke ability to edit show if user is not assigned to it)
add_filter( 'user_has_cap', 'radio_station_revoke_show_edit_cap', 10, 3 );
function radio_station_revoke_show_edit_cap( $allcaps, $caps, $args ) {

	global $post, $wp_roles;

	// --- get the current user ---
	$user = wp_get_current_user();

	// --- get roles with publish shows capability ---
	$publish_show_roles = array( 'administrator' );
	if ( isset( $wp_roles->roles ) && is_array( $wp_roles->roles ) ) {
		foreach ( $wp_roles->roles as $name => $role ) {
			foreach ( $role['capabilities'] as $capname => $capstatus ) {
				if ( 'publish_shows' === $capname && (bool) $capstatus ) {
					$publish_show_roles[] = $name;
				}
			}
		}
	}

	// --- check if current user has any of these roles ---
	// 2.2.8: remove strict in_array checking
	$found = false;
	foreach ( $publish_show_roles as $role ) {
		if ( in_array( $role, $user->roles ) ) {$found = true;}
	}

	if ( !$found ) {

		// --- limit this to published shows ---
		// 2.3.0: added object and property_exists check to be safe
		if ( is_object( $post) && property_exists( $post, 'post_type' ) && isset( $post->post_type ) ) {

			// 2.3.0: removed is_admin check (so works with frontend edit show link)
			// 2.3.0: moved check if show is published inside
			if ( 'show' === $post->post_type ) {

				// --- get show hosts and producers ---
				$hosts = get_post_meta( $post->ID, 'show_user_list', true );
				$producers = get_post_meta( $post->ID, 'show_producer_list', true );

				if ( !$hosts  || empty( $hosts ) ) {$hosts = array();}
				if ( !$producers || empty( $producers ) ) {$producers = array();}

				// ---- revoke editing capability if not assigned to this show ---
				// 2.2.8: remove strict in_array checking
				// 2.3.0: also check new Producer role
				if ( !in_array( $user->ID, $hosts ) && !in_array($user->ID, $producers ) ) {

					// --- remove the edit_shows capability ---
					$allcaps['edit_shows'] = false;

					// 2.3.0: move check if show is published inside
					if ( 'publish' == $post->post_status ) {
						$allcaps['edit_published_shows'] = false;
					}
				}
			}
		}
	}
	return $allcaps;
}

