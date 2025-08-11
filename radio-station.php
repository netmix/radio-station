<?php

/*

Plugin Name: Radio Station
Plugin URI: https://radiostation.pro/radio-station
Description: Adds Show pages, DJ role, playlist and on-air programming functionality to your site.
Author: Tony Zeoli, Tony Hayes
Version: 2.5.17
Requires at least: 3.3.1
Text Domain: radio-station
Domain Path: /languages
Author URI: https://netmix.com/
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

if ( !defined( 'ABSPATH' ) ) exit;

// === Plugin Setup ===
// - Define Plugin Constants
// - Set Debug Mode Constant
// - Define Plugin Data Slugs
// - Include Plugin Files
// - Plugin Options and Defaults
// - Plugin Loader Settings
// - Bundle Plan Settings Filter
// - Start Plugin Loader Instance
// - Include Plugin Admin Files
// - Load Plugin Text Domain
// === Plugin Functions ===
// - Check Plan Options
// - Check Plugin Version
// - Plugin Activation Action
// x Activation Welcome Redirect
// - Plugin Deactivation Action
// - Filter Plugin Updates Transient
// - Filter Freemius Plugin Icon Path
// - Set Allowed Origins for Radio Player
// === Resource Handling ===
// - Enqueue Plugin Scripts
// - Register Moment JS
// - Enqueue Plugin Script
// - Add Inline Script
// - Print Footer Scripts
// - Print Admin Footer Scripts
// - Enqueue Plugin Stylesheet
// - Add Inline Style
// - Print Footer Styles
// - Print Admin Footer Styles
// - Enqueue Datepicker
// - Enqueue Localized Script Values
// - Localization Script
// - Filter Streaming Data
// - Filter Player Script
// === Transient Caching ===
// - Delete Prefixed Transients
// - Clear Cached Data
// - Clear Cache on Status Transitions
// === Debugging ===
// - maybe Clear Transient Data
// - Debug Output and Logging
// - Freemius Object Debug


// --------------------
// === Plugin Setup ===
// --------------------

// -----------------------
// Define Plugin Constants
// -----------------------
// 2.3.1: added constant for Netmix Directory
// 2.4.0.3: remove separate constant for API docs link
// 2.4.0.3: update home URLs to radiostation.pro
// 2.4.0.8: added RADIO_STATION_SLUG constant
// 2.5.0: added RADIO_STATION_PATREON constant
define( 'RADIO_STATION_SLUG', 'radio-station' );
define( 'RADIO_STATION_FILE', __FILE__ );
define( 'RADIO_STATION_DIR', dirname( __FILE__ ) );
define( 'RADIO_STATION_BASENAME', plugin_basename( __FILE__ ) );
define( 'RADIO_STATION_HOME_URL', 'https://radiostation.pro/radio-station/' );
define( 'RADIO_STATION_DOCS_URL', 'https://radiostation.pro/docs/' );
// define( 'RADIO_STATION_API_DOCS_URL', 'https://radiostation.pro/docs/api/' );
define( 'RADIO_STATION_PRO_URL', 'https://radiostation.pro/' );
define( 'RADIO_STATION_NETMIX_DIR', 'https://netmix.com/' );
define( 'RADIO_STATION_PATREON', 'https://patreon.com/radiostation' );

// ------------------------
// Define Plugin Data Slugs
// ------------------------
define( 'RADIO_STATION_LANGUAGES_SLUG', 'rs-languages' );
define( 'RADIO_STATION_HOST_SLUG', 'rs-host' );
define( 'RADIO_STATION_PRODUCER_SLUG', 'rs-producer' );

// --- check and define CPT slugs ---
// TODO: prefix original slugs and update post/taxonomy data
if ( get_option( 'radio_show_cpts_prefixed' ) ) {
	define( 'RADIO_STATION_SHOW_SLUG', 'rs-show' );
	define( 'RADIO_STATION_PLAYLIST_SLUG', 'rs-playlist' );
	define( 'RADIO_STATION_OVERRIDE_SLUG', 'rs-override' );
	define( 'RADIO_STATION_GENRES_SLUG', 'rs-genres' );
} else {
	define( 'RADIO_STATION_SHOW_SLUG', 'show' );
	define( 'RADIO_STATION_PLAYLIST_SLUG', 'playlist' );
	define( 'RADIO_STATION_OVERRIDE_SLUG', 'override' );
	define( 'RADIO_STATION_GENRES_SLUG', 'genres' );
}

// -----------------------
// Set Debug Mode Constant
// -----------------------
// 2.3.0: added debug mode constant
// 2.3.2: added saving debug mode constant
if ( !defined( 'RADIO_STATION_DEBUG' ) ) {
	// 2.5.0: use simplified single line condition
	$rs_debug = ( isset( $_REQUEST['rs-debug'] ) && ( '1' === sanitize_text_field( $_REQUEST['rs-debug'] ) ) ) ? true : false;
	define( 'RADIO_STATION_DEBUG', $rs_debug );
}
if ( !defined( 'RADIO_STATION_SAVE_DEBUG' ) ) {
	// 2.5.0: use simplified single line condition
	$rs_save_debug = ( isset( $_REQUEST['rs-save-debug'] ) && ( '1' === sanitize_text_field( $_REQUEST['rs-save-debug'] ) ) ) ? true : false;
	define( 'RADIO_STATION_SAVE_DEBUG', $rs_save_debug );
}

// --------------------
// Include Plugin Files
// --------------------
// 2.3.0: include new data feeds file
// 2.3.0: renamed widget files to match new widget names
// 2.3.0: separate file for legacy support functions
// 2.5.0: moved templates and user roles to separate include files
// 2.5.0: moved times and schedules to separate include files

// --- Main Includes ---
require RADIO_STATION_DIR . '/includes/support-functions.php';
require RADIO_STATION_DIR . '/includes/post-types.php';
require RADIO_STATION_DIR . '/includes/templates.php';
require RADIO_STATION_DIR . '/includes/user-roles.php';
require RADIO_STATION_DIR . '/includes/data-feeds.php';
require RADIO_STATION_DIR . '/includes/legacy.php';

// --- Player ---
// 2.4.0.4: include player as standard
require RADIO_STATION_DIR . '/player/radio-player.php';

// --- Scheduler ---
// note: keep this load order as schedules instantiates schedule engine class
require RADIO_STATION_DIR . '/scheduler/schedule-engine.php';
require RADIO_STATION_DIR . '/includes/schedules.php';
require RADIO_STATION_DIR . '/includes/times.php';

// --- Shortcodes ---
require RADIO_STATION_DIR . '/includes/master-schedule.php';
require RADIO_STATION_DIR . '/includes/shortcodes.php';

// --- Widgets ---
// 2.3.1: added radio player widget file
// 2.4.0.4: move player widget here
// 2.5.0: move widget classes to widgets directory
require RADIO_STATION_DIR . '/widgets/class-current-show-widget.php';
require RADIO_STATION_DIR . '/widgets/class-upcoming-shows-widget.php';
require RADIO_STATION_DIR . '/widgets/class-current-playlist-widget.php';
require RADIO_STATION_DIR . '/widgets/class-radio-clock-widget.php';
require RADIO_STATION_DIR . '/widgets/class-radio-player-widget.php';

// --- Blocks ---
// 2.5.0: added for registering block types
if ( function_exists( 'register_block_type' ) ) {
	require RADIO_STATION_DIR . '/includes/blocks.php';
}

// --- Feature Development ---
// 2.3.0: add feature branch development includes
$features = array( 'import-export' );
foreach ( $features as $feature ) {
	$filepath = RADIO_STATION_DIR . '/includes/' . $feature . '.php';
	if ( file_exists( $filepath ) ) {
		include $filepath;
	}
}

// --- Backwards Compatible Player ---
// 2.5.1: add player backwards compatible functions
add_action( 'plugins_loaded', 'radio_station_back_compat_player' );
function radio_station_back_compat_player() {
	$back_compat = apply_filters( 'radio_station_back_compat_player', true );
	if ( $back_compat ) {
		include RADIO_STATION_DIR . '/player/compat.php';
	}
}


// ---------------------------
// Plugin Options and Defaults
// ---------------------------
// 2.3.0: added plugin options
// 2.4.0.8: moved options array to separate file
// 2.5.0: move plan options check to separate function
$timezones = radio_station_get_timezone_options( true );
$languages = radio_station_get_language_options( true );
$formats = radio_station_get_stream_formats();
$plan_options = radio_station_check_plan_options();
require RADIO_STATION_DIR . '/options.php';

// ----------------------
// Plugin Loader Settings
// ----------------------
// 2.3.0: added plugin loader settings
// 2.4.0.8: use RADIO_STATION_SLUG constant

// --- settings array ---
$settings = array(
	// --- Plugin Info ---
	'slug'         => RADIO_STATION_SLUG,
	'file'         => RADIO_STATION_FILE,
	'version'      => '0.0.1',

	// --- Menus and Links ---
	'title'        => 'Radio Station',
	'parentmenu'   => RADIO_STATION_SLUG,
	'home'         => RADIO_STATION_HOME_URL,
	'docs'         => RADIO_STATION_DOCS_URL,
	'support'      => 'https://github.com/netmix/radio-station/issues/',
	'ratetext'     => __( 'Rate on WordPress.org', 'radio-station' ),
	'share'        => RADIO_STATION_HOME_URL . '#share',
	'sharetext'    => __( 'Share the Plugin Love', 'radio-station' ),
	'donate'       => 'https://patreon.com/radiostation',
	'donatetext'   => __( 'Support this Plugin', 'radio-station' ),
	'readme'       => false,
	'settingsmenu' => false,

	// --- Options ---
	'namespace'    => 'radio_station',
	'settings'     => 'rs',
	'option'       => 'radio_station',
	'options'      => $options,

	// --- WordPress.Org ---
	'wporgslug'    => RADIO_STATION_SLUG,
	'wporg'        => true,
	'textdomain'   => 'radio-station',

	// --- Freemius ---
	// 2.4.0.1: turn on addons switch for Pro
	// 2.4.0.3: turn on plans switch for Pro also
	// 2.4.0.3: set Pro details and Upgrade links
	// 2.4.0.4: change upgrade_link to -upgrade
	// 2.5.6: added affiliation selected key
	'freemius_id'  => '4526',
	'freemius_key' => 'pk_aaf375c4fb42e0b5b3831e0b8476b',
	'hasplans'     => $plan_options['has_plans'],
	'upgrade_link' => add_query_arg( 'page', RADIO_STATION_SLUG . '-pricing', admin_url( 'admin.php' ) ),
	'pro_link'     => RADIO_STATION_PRO_URL . 'pricing/',
	'hasaddons'    => $plan_options['has_addons'],
	'addons_link'  => add_query_arg( 'page', RADIO_STATION_SLUG . '-addons', admin_url( 'admin.php' ) ),
	'plan'         => $plan_options['plan_type'],
	'affiliation'  => 'selected',

);

// ---------------------------
// Bundle Plan Settings Filter
// ---------------------------
// 2.5.0: added for bundle pricing configuration
add_filter( 'freemius_init_settings_radio_station', 'radio_station_freemius_bundle_config' );
function radio_station_freemius_bundle_config( $settings ) {
	// 2.4.0.6: add bundles configuration
	$settings['bundle_id'] = '9521';
	$settings['bundle_public_key'] = 'pk_a2650f223ef877e87fe0fdfc4442b';
	$settings['bundle_license_auto_activation'] = true;
	return $settings;
}

// -------------------------
// Set Plugin Option Globals
// -------------------------
global $radio_station_data;
$radio_station_data['channel'] = '';
$radio_station_data['options'] = $options;
$radio_station_data['settings'] = $settings;
if ( RADIO_STATION_DEBUG ) {
	echo '<span style="display:none;">Radio Station Settings: ' . esc_html( print_r( $settings, true ) ) . '</span>';
}

// ----------------------------
// Start Plugin Loader Instance
// ----------------------------
require RADIO_STATION_DIR . '/loader.php';
$rs_instance = new radio_station_loader( $settings );


// --------------------------
// Include Plugin Admin Files
// --------------------------
// 2.2.7: added conditional load of admin includes
// 2.2.7: moved all admin functions to radio-station-admin.php
if ( is_admin() ) {

	// --- Admin Includes ---
	require RADIO_STATION_DIR . '/radio-station-admin.php';
	// require RADIO_STATION_DIR . '/includes/onboarding.php';
	require RADIO_STATION_DIR . '/includes/post-types-admin.php';

	// --- Contextual Help ---
	// 2.3.0: maybe load contextual help config
	if ( file_exists( RADIO_STATION_DIR . '/help/contextual-help-config.php' ) ) {
		include RADIO_STATION_DIR . '/help/contextual-help-config.php';
	}
}

// -----------------------
// Load Plugin Text Domain
// -----------------------
// 2.5.13: change plugins_loaded hook for load_plugin_textdomain
add_action( 'init', 'radio_station_init' );
function radio_station_init() {
	// 2.3.0: use RADIO_STATION_DIR constant
	load_plugin_textdomain( 'radio-station', false, RADIO_STATION_DIR . '/languages' );
}

// ----------------------------
// Add Pricing Page Path Filter
// ----------------------------
// 2.5.0: added for Freemius Pricing Page v2
add_action( 'radio_station_loaded', 'radio_station_add_pricing_path_filter' );
function radio_station_add_pricing_path_filter() {
	global $radio_station_freemius;
	if ( method_exists( $radio_station_freemius, 'add_filter' ) ) {
		$radio_station_freemius->add_filter( 'freemius_pricing_js_path', 'radio_station_pricing_page_path' );
	}
}

// ---------------------------------
// Freemius Pricing Page Path Filter
// ---------------------------------
// 2.5.0: added for Freemius Pricing Page v2
function radio_station_pricing_page_path( $default_pricing_js_path ) {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || RADIO_STATION_DEBUG ? '' : '.min';
	return RADIO_STATION_DIR . '/freemius-pricing/freemius-pricing' . $suffix . '.js';
}


// ------------------------
// === Plugin Functions ===
// ------------------------

// ------------------
// Check Plan Options
// ------------------
// 2.5.0: moved to separate function
function radio_station_check_plan_options() {

	// 2.4.0.3: added check active/installed Pro version
	// 2.4.0.4: add defaults for has_addons and has_plans
	$has_addons = false;
	$has_plans = true;
	$plan = 'free';

	// --- check for deactivated pro plugin ---
	// 2.4.0.4: remove unnecessary second argument to wp_cache_get
	$plugins = wp_cache_get( 'plugins' );
	if ( !$plugins ) {
		if ( !function_exists( 'get_plugins' ) ) {
			$admin_plugins = ABSPATH . 'wp-admin/includes/plugin.php';
			if ( file_exists( $admin_plugins ) ) {
				include_once $admin_plugins;
			}
		}
		$plugins = get_plugins();
	}
	if ( $plugins && is_array( $plugins ) && ( count( $plugins ) > 0 ) ) {
		foreach ( $plugins as $slug => $plugin ) {
			if ( strstr( $slug, 'radio-station-pro.php' ) ) {
				// 2.4.0.4: only set premium for upgrade version
				if ( isset( $plugin['Name'] ) && strstr( $plugin['Name'], '(Premium)' ) ) {
					$plan = 'premium';
					break;
				} else {
					// 2.4.0.4: detect and force enable addon version
					$plan = 'premium';
					$has_addons = true;
					$has_plans = false;
					break;
				}
			}
		}
	}

	// 2.5.0: set as array for returning
	$plan_options = array(
		'has_plans'  => $has_plans,
		'has_addons' => $has_addons,
		'plan_type'  => $plan,
	);
	return $plan_options;
}

// --------------------
// Check Plugin Version
// --------------------
// 2.3.0: check plugin version for updates and announcements
add_action( 'init', 'radio_station_check_version', 9 );
function radio_station_check_version() {

	// --- get current and stored versions ---
	// 2.3.2: use plugin version function
	$version = radio_station_plugin_version();
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
		// (code template if/when needed for future release updates)
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

// ------------------------
// Plugin Activation Action
// ------------------------
// (run on plugin activation, and thus also after a plugin update)
// 2.2.8: fix for mismatched flag function name
register_activation_hook( RADIO_STATION_FILE, 'radio_station_plugin_activation' );
function radio_station_plugin_activation() {

	// --- flag to flush rewrite rules ---
	// 2.2.3: added this for custom post types rewrite flushing
	add_option( 'radio_station_flush_rewrite_rules', true );

	// --- clear schedule transients ---
	// 2.3.3: added clear transients on (re)activation
	// 2.3.3.9: just use clear cached data function
	radio_station_clear_cached_data( false );

	// --- set welcome redirect transient ---
	// TODO: check if handled by Freemius activation
	// set_transient( 'radio_station_welcome', 1, 7 );

	// 2.4.0.8: clear plugin updates transient on activation
	delete_site_transient( 'update_plugins' );
}

// ---------------------------
// Activation Welcome Redirect
// ---------------------------
/* add_action( 'admin_init', 'radio_station_welcome_redirect' );
function radio_station_welcome_redirect() {
	if ( !get_transient( 'radio_station_welcome' ) || wp_doing_ajax() || is_network_admin() || !current_user_can( 'install_plugins' ) ) {
		return;
	}
	delete_transient( 'radio_station_welcome' );
	$location = admin_url( 'admin.php?page=radio-station&welcome=1' );
	wp_safe_redirect( $location );
	exit;
} */

// --------------------------
// Plugin Deactivation Action
// --------------------------
// 2.4.0.8: clear plugin updates transient on deactivation
// 2.5.0: fix to typo in deactivation hook function and add _plugin
register_deactivation_hook( RADIO_STATION_FILE, 'radio_station_plugin_deactivation' );
function radio_station_plugin_deactivation() {
	flush_rewrite_rules();
	delete_site_transient( 'update_plugins' );
}

// -------------------------------
// Filter Plugin Updates Transient
// -------------------------------
// 2.4.0.8: added to ensure Pro never overwrites free on update
add_filter( 'pre_set_site_transient_update_plugins', 'radio_station_transient_update_plugins', 999 );
add_filter( 'site_transient_update_plugins', 'radio_station_transient_update_plugins', 999 );
function radio_station_transient_update_plugins( $transient_data ) {
	// 2.4.0.9: fix for PHP8 cannot check property_exists of non-object
	if ( $transient_data && is_object( $transient_data ) && property_exists( $transient_data, 'response' ) ) {
		$response = $transient_data->response;
		if ( isset( $response[RADIO_STATION_BASENAME] ) ) {
			if ( strstr( $response[RADIO_STATION_BASENAME]->url, 'freemius' ) ) {
				unset( $response[RADIO_STATION_BASENAME] );
			}
		}
		$transient_data->response = $response;
	}
	return $transient_data;
}

// --------------------------------
// Filter Freemius Plugin Icon Path
// --------------------------------
// 2.4.0.8: filter Freemius plugin icon path
add_filter( 'fs_plugin_icon_radio-station', 'radio_station_freemius_plugin_url_path' );
function radio_station_freemius_plugin_url_path( $default_path ) {
	$icon_path = RADIO_STATION_DIR . '/assets/icon-256x256.png';
	if ( file_exists( $icon_path ) ) {
		return $icon_path;
	}
	$icon_path = RADIO_STATION_DIR . '/images/' . RADIO_STATION_SLUG . '.png';
	// 2.5.10: fix to mismatched variable default_path
	if ( file_exists( $icon_path ) ) {
		return $icon_path;
	}
	// 2.5.0: fix to incorrect return of undefined local_path
	return $default_path;
}

// ------------------------------------
// Set Allowed Origins for Radio Player
// ------------------------------------
// 2.3.3.9: added for embedded radio player control
add_filter( 'allowed_http_origins', 'radio_station_allowed_player_origins' );
function radio_station_allowed_player_origins( $origins ) {

	if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) {
		return $origins;
	}
	// 2.5.6: added sanitize_text_field
	if ( !isset( $_REQUEST['action'] ) || ( 'radio_player' !== sanitize_text_field( $_REQUEST['action'] ) ) ) {
		return $origins;
	}

	$netmix = untrailingslashit( RADIO_STATION_NETMIX_DIR );
	$allowed = array( $netmix );
	$allowed = apply_filters( 'radio_station_player_allowed_origins', $allowed );
	foreach ( $allowed as $allow ) {
		$origins[] = $allow;
	}
	return $origins;
}


// -------------------------
// === Resource Handling ===
// -------------------------

// ----------------------
// Enqueue Plugin Scripts
// ----------------------
// 2.3.0: added for enqueueing main Radio Station script
// 2.5.0: change to prevent conflict with radio station theme
add_action( 'wp_enqueue_scripts', 'radio_station_enqueue_plugin_scripts' );
function radio_station_enqueue_plugin_scripts() {

	// --- enqueue custom stylesheet if found ---
	// 2.3.0: added for automatic custom style loading
	radio_station_enqueue_style( 'custom' );

	// --- enqueue plugin script ---
	// 2.3.0: added jquery dependency for inline script fragments
	radio_station_enqueue_script( 'radio-station', array( 'jquery' ), true );

	// --- set script suffix ---
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$suffix = ( defined( 'RADIO_STATION_DEBUG' ) && RADIO_STATION_DEBUG ) ? '' : $suffix;

	// -- enqueue javascript timezone detection script ---
	// 2.3.3.9: activated for improved timezone detection
	$jstz_url = plugins_url( 'js/jstz' . $suffix . '.js', RADIO_STATION_FILE );
	wp_enqueue_script( 'jstz', $jstz_url, array(), '1.0.6', false );

	// --- register moment js ---
	// 2.5.0: move to separate function for reusability
	radio_station_register_moment();
	wp_enqueue_script( 'moment' );
}

// ------------------
// Register Moment JS
// ------------------
// 2.5.0: moved to separate function for resusability
function radio_station_register_moment() {

	// --- set script suffix ---
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$suffix = ( defined( 'RADIO_STATION_DEBUG' ) && RADIO_STATION_DEBUG ) ? '' : $suffix;

	// --- enqueue Moment.js ---
	// ref: https://momentjs.com
	// 2.3.3.9: added for improved time format display
	// 2.5.0: add check for registered script in WP core
	if ( !wp_script_is( 'moment', 'registered' ) ) {
		$moment_url = plugins_url( 'js/moment' . $suffix . '.js', RADIO_STATION_FILE );
		wp_register_script( 'moment', $moment_url, array( 'jquery' ), '2.29.4', false );
	}
}

// ---------------------
// Enqueue Plugin Script
// ---------------------
function radio_station_enqueue_script( $scriptkey, $deps = array(), $infooter = false ) {

	// --- set stylesheet filename and child theme path ---
	$filename = $scriptkey . '.js';

	// 2.3.0: check template hierarchy for file
	$template = radio_station_get_template( 'both', $filename, 'js' );
	if ( $template ) {

		// 2.3.2: use plugin version for releases
		$plugin_version = radio_station_plugin_version();
		$version_length = strlen( $plugin_version );
		// 2.5.0: allow for minor version release numbers
		if ( 4 > $version_length ) {
			$version = $plugin_version;
		} else {
			$version = filemtime( $template['file'] );
		}

		$url = $template['url'];

		// --- enqueue script ---
		// 2.5.0: register script before enqueueing
		wp_register_script( $scriptkey, $url, $deps, $version, $infooter );
		wp_enqueue_script( $scriptkey );
	}
}

// -----------------
// Add Inline Script
// -----------------
// 2.5.0: added for missed inline scripts (via shortcodes)
function radio_station_add_inline_script( $handle, $js, $position = 'after' ) {

	// --- maybe enqueue footer dummy script ---
	/* if ( ( 'rs-footer' == $handle ) && !wp_script_is( 'rs-footer', 'enqueued' ) ) {
		$script_url = plugins_url( '/js/rs-footer.js', RADIO_STATION_FILE );
		wp_enqueue_script( 'rs-footer', $script_url, array(), '1.0.0', true );
	} */

	// --- add check if script is already done ---
	if ( wp_script_is( $handle, 'registered' ) && !wp_script_is( $handle, 'done' ) ) {

		// --- handle javascript normally ---
		wp_add_inline_script( $handle, $js, $position );		

	} else {

		// 2.5.7: enqueue dummy javascript file to output in footer
		// 2.5.10: fix slug from rp-footer to rs-footer
		// 2.5.10: change from register_script to enqueue_script
		/* if ( !wp_script_is( 'rs-footer', 'enqueued' ) ) {
			$script_url = plugins_url( '/js/rs-footer.js', RADIO_STATION_FILE );
			wp_enqueue_script( 'rs-footer', $script_url, array(), '1.0.0', true );
		}
		wp_add_inline_script( 'rs-footer', $js, $position ); */
		
		// 2.5.7: enqueue dummy javascript file to output in footer
		if ( !wp_script_is( 'radio-station-footer', 'registered' ) ) {
			$version = functIon_exists( 'radio_station_plugin_version' ) ? radio_station_plugin_version() : '2.5.0';
			wp_register_script( 'radio-station-footer', null, array( 'jquery' ), $version, true );
			wp_enqueue_script( 'radio-station-footer' );
		}
		wp_add_inline_script( 'radio-station-footer', $js, $position );

	}
}

// --------------------
// Print Footer Scripts
// --------------------
// 2.5.0: added for missed inline scripts
// 2.5.7: deprecated in favour of adding inline to dummy script
/* function radio_station_print_footer_scripts() {
	global $radio_station_scripts;
	if ( is_array( $radio_station_scripts ) && ( count( $radio_station_scripts ) > 0 ) ) {
		foreach ( $radio_station_scripts as $handle => $js ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<script id="' . esc_attr( $handle ) . '-js-after">' . $js . '</script>';
		}
	}
} */

// --------------------------
// Print Admin Footer Scripts
// --------------------------
// 2.5.7: deprecated in favour of adding inline to dummy script
/* function radio_station_admin_print_footer_scripts() {
	global $radio_station_admin_scripts;
	if ( is_array( $radio_station_admin_scripts ) && ( count( $radio_station_admin_scripts ) > 0 ) ) {
		foreach ( $radio_station_admin_scripts as $handle => $js ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<script id="' . esc_attr( $handle ) . '-js-after">' . $js . '</script>';
		}
	}
} */

// -------------------------
// Enqueue Plugin Stylesheet
// -------------------------
// ?.?.?: widgets.css style conditional enqueueing moved to within widget classes
// 2.3.0: added abstracted method for enqueueing plugin stylesheets
// 2.3.0: moved master schedule style enqueueing to conditional in master-schedule.php
// 2.5.0: added optional dependencies argument to style enqueues
function radio_station_enqueue_style( $stylekey, $deps = array() ) {

	// --- check style enqueued switch ---
	global $radio_station_styles;
	if ( !isset( $radio_station_styles ) ) {
		$radio_station_styles = array();
	}
	if ( !isset( $radio_station_styles[$stylekey] ) ) {

		// --- set stylesheet filename and child theme path ---
		$filename = 'rs-' . $stylekey . '.css';

		// 2.3.0: check template hierarchy for file
		$template = radio_station_get_template( 'both', $filename, 'css' );
		if ( $template ) {

			// --- use found template values ---
			// 2.3.2: use plugin version for releases
			$plugin_version = radio_station_plugin_version();
			$version_length = strlen( $plugin_version );
			// 2.5.0: allow for minor version release numbers
			if ( 4 > $version_length ) {
				$version = $plugin_version;
			} else {
				$version = filemtime( $template['file'] );
			}
			$url = $template['url'];

			// --- enqueue styles in footer ---
			wp_enqueue_style( 'rs-' . $stylekey, $url, $deps, $version, 'all' );

			// --- set style enqueued switch ---
			$radio_station_styles[$stylekey] = true;
		}
	}
}

// -----------------
// Add Inline Styles
// -----------------
// 2.5.0: added for missed inline styles (via shortcodes)
function radio_station_add_inline_style( $handle, $css ) {

	// --- add check if style is already done ---
	if ( wp_style_is( $handle, 'registered' ) && !wp_style_is( $handle, 'done' ) ) {

		// --- handle style normally ---
		wp_add_inline_style( $handle, $css );

	} else {
		// --- store extra styles for later output ---
		/* if ( !strstr( $handle, '-admin' ) ) {
			global $radio_station_styles;
			add_action( 'wp_print_footer_scripts', 'radio_station_print_footer_styles', 20 );
			if ( !isset( $radio_station_styles[$handle] ) ) {
				$radio_station_styles[$handle] = '';
			}
			$radio_station_styles[$handle] .= $css;
		} else {
			global $radio_station_admin_styles;
			add_action( 'admin_print_footer_scripts', 'radio_station_admin_print_footer_styles', 20 );
			if ( !isset( $radio_station_admin_styles[$handle] ) ) {
				$radio_station_admin_styles[$handle] = '';
			}
			$radio_station_admin_styles[$handle] .= $css;
		} */
		if ( !wp_style_is( 'radio-station-footer', 'registered' ) ) {
			$version = function_exists( 'radio_station_plugin_version' ) ? radio_station_plugin_version() : '2.5.0';
			wp_register_style( 'radio-station-footer', null, array(), $version, 'all' );
			wp_enqueue_style( 'radio-station-footer' );
		}
		wp_add_inline_style( 'radio-station-footer', $css );
	}
}

// -------------------
// Print Footer Styles
// -------------------
// 2.5.0: added for missed inline styles
/* function radio_station_print_footer_styles() {
	global $radio_station_styles;
	if ( is_array( $radio_station_styles ) && ( count( $radio_station_styles ) > 0 ) ) {
		foreach ( $radio_station_styles as $handle => $css ) {
			echo '<style>' . wp_kses_post( $css ) . '</style>';
		}
	}
} */

// -------------------------
// Print Admin Footer Styles
// -------------------------
// 2.5.0: added for missed inline styles
/* function radio_station_admin_print_footer_styles() {
	global $radio_station_admin_styles;
	if ( is_array( $radio_station_admin_styles ) && ( count( $radio_station_admin_styles ) > 0 ) ) {
		foreach ( $radio_station_admin_styles as $handle => $css ) {
			echo '<style>' . wp_kses_post( $css ) . '</style>';
		}
	}
} */

// ------------------
// Enqueue Datepicker
// ------------------
// 2.3.0: enqueued separately by override post type only
// 2.3.3.9: moved here from radio-station-admin.php
function radio_station_enqueue_datepicker() {

	// --- enqueue jquery datepicker ---
	wp_enqueue_script( 'jquery-ui-datepicker' );

	// --- enqueue jquery datepicker styles ---
	// 2.3.0: update theme styles from 1.8.2 to 1.12.1
	// 2.3.0: use local datepicker styles instead of via Google
	// $protocol = is_ssl() ? 'https' : 'http';
	// $url = $protocol . '://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css';
	// wp_enqueue_style( 'jquery-ui-style', $url, array(), '1.12.1' );
	$style = radio_station_get_template( 'both', 'jquery-ui.css', 'css' );
	wp_enqueue_style( 'jquery-ui-smoothness', $style['url'], array(), '1.12.1', 'all' );

}

// ---------------------------
// Enqueue Widget Color Picker
// ---------------------------
// 2.5.0: added for widget color options
function radio_station_enqueue_color_picker() {

	// --- enqueue color picker ---
	$suffix = SCRIPT_DEBUG ? '' : '.min';
	$url = plugins_url( '/js/wp-color-picker-alpha' . $suffix . '.js', RADIO_STATION_FILE );
	wp_enqueue_script( 'wp-color-picker-a', $url, array( 'wp-color-picker' ), '3.0.0', true );

	// --- init color picker fields ---
	$js = "jQuery(document).ready(function() {";
	$js .= "if (jQuery('.color-picker').length) {jQuery('.color-picker').wpColorPicker();}";
	$js .= "});" . "\n";
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'wp-color-picker-a', $js );
}

// -------------------------------
// Enqueue Localized Script Values
// -------------------------------
add_action( 'wp_enqueue_scripts', 'radio_station_localize_script' );
function radio_station_localize_script() {

	// 2.5.0: check flag to run once only
	global $radio_station;
	if ( isset( $radio_station['script-localized'] ) ) {
		return;
	}

	$js = radio_station_localization_script();
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'radio-station', $js );
	$radio_station['script-localized'] = true;
}

// -------------------
// Localization Script
// -------------------
// 2.3.3.9: separated script from enqueueing
function radio_station_localization_script() {

	// --- create settings objects ---
	$js = "var radio = {}; radio.timezone = {}; radio.time = {}; radio.labels = {}; radio.units = {};";

	// 2.4.0.6: add filterable time display separator
	$time_separator = apply_filters( 'radio_station_time_separator', ':', 'javascript' );
	$js .= " radio.sep = '" . esc_js( $time_separator ) . "';";

	// --- set AJAX URL ---
	// 2.3.2: add admin AJAX URL
	$js .= "radio.ajax_url = '" . esc_url( admin_url( 'admin-ajax.php' ) ) . "';" . "\n";

	// --- clock time format ---
	// TODO: maybe set time format ?
	// ref: https://devhints.io/wip/intl-datetime
	$clock_format = radio_station_get_setting( 'clock_time_format' );
	$js .= "radio.clock_format = '" . esc_js( $clock_format ) . "';" . "\n";

	// --- detect touchscreens ---
	// ref: https://stackoverflow.com/a/52855084/5240159
	// 2.5.0: use !any-pointer:fine instead of pointer:coarse
	// ref: https://stackoverflow.com/a/51774045/5240159
	$js .= "matchmedia = window.matchMedia || window.msMatchMedia;" . "\n";
	$js .= "radio.touchscreen = !matchmedia('(any-pointer: fine)').matches;" . "\n";

	// --- set debug flag ---
	if ( RADIO_STATION_DEBUG ) {
		$js .= "radio.debug = true;" . "\n";
	} else {
		$js .= "radio.debug = false;" . "\n";
	}

	// 2.5.0: set separate clock debug flag
	if ( isset( $_REQUEST['rs-clock-debug'] ) && ( '1' === sanitize_text_field( $_REQUEST['rs-clock-debug'] ) ) ) {
		$js .= "radio.clock_debug = true;" . "\n";
	} else {
		$js .= "radio.clock_debug = false;" . "\n";
	}

	// --- radio timezone ---
	// 2.3.2: added get timezone function
	$timezone = radio_station_get_timezone();

	if ( stristr( $timezone, 'UTC' ) ) {

		if ( 'UTC' == $timezone ) {
			$offset = '0';
		} else {
			$offset = str_replace( 'UTC', '', $timezone );
		}
		$js .= "radio.timezone.offset = " . esc_js( $offset * 60 * 60 ) . "; ";
		if ( '0' == $offset ) {
			$offset = '';
		} elseif ( $offset > 0 ) {
			$offset = '+' . $offset;
		}
		$js .= "radio.timezone.code = 'UTC" . esc_js( $offset ) . "';" . "\n";
		$js .= "radio.timezone.utc = '" . esc_js( $offset ) . "';" . "\n";
		$js .= "radio.timezone.utczone = true;" . "\n";

	} else {

		// --- get offset and code from timezone location ---
		$datetimezone = new DateTimeZone( $timezone );
		$offset = $datetimezone->getOffset( new DateTime() );
		$offset_hours = $offset / ( 60 * 60 );
		if ( 0 == $offset ) {
			$utc_offset = '';
		} elseif ( $offset > 0 ) {
			$utc_offset = '+' . $offset_hours;
		} else {
			$utc_offset = $offset_hours;
		}
		$utc_offset = 'UTC' . $utc_offset;
		$code = radio_station_get_timezone_code( $timezone );
		$js .= "radio.timezone.location = '" . esc_js( $timezone ) . "';" . "\n";
		$js .= "radio.timezone.offset = " . esc_js( $offset ) . ";" . "\n";
		$js .= "radio.timezone.code = '" . esc_js( $code ) . "';" . "\n";
		$js .= "radio.timezone.utc = '" . esc_js( $utc_offset ) . "';" . "\n";
		$js .= "radio.timezone.utczone = false;" . "\n";

	}

	if ( defined( 'RADIO_STATION_USE_SERVER_TIMES' ) && RADIO_STATION_USE_SERVER_TIMES ) {
		$js .= "radio.timezone.adjusted = false;" . "\n";
	} else {
		$js .= "radio.timezone.adjusted = true;" . "\n";
	}

	// --- set user timezone offset ---
	// (and convert offset minutes to seconds)
	$js .= "radio.timezone.useroffset = (new Date()).getTimezoneOffset() * 60;" . "\n";

	// --- translated months array ---
	// 2.3.2: also translate short month labels
	$js .= "radio.labels.months = new Array(";
	$short = "radio.labels.smonths = new Array(";
	$months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
	foreach ( $months as $i => $month ) {
		$month = radio_station_translate_month( $month );
		$short_month = radio_station_translate_month( $month, true );
		$month = str_replace( "'", "", $month );
		$short_month = str_replace( "'", "", $short_month );
		$js .= "'" . esc_js( $month ) . "'";
		$short .= "'" . esc_js( $short_month ) . "'";
		if ( $i < ( count( $months ) - 1 ) ) {
			$js .= ", ";
			$short .= ", ";
		}
	}
	$js .= ");" . "\n";
	$js .= $short . ");" . "\n";

	// --- translated days array ---
	// 2.3.2: also translate short day labels
	$js .= "radio.labels.days = new Array(";
	$short = "radio.labels.sdays = new Array(";
	$days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
	foreach ( $days as $i => $day ) {
		$day = radio_station_translate_weekday( $day );
		$short_day = radio_station_translate_weekday( $day, true );
		$day = str_replace( "'", "", $day );
		$short_day = str_replace( "'", "", $short_day );
		$js .= "'" . esc_js( $day ) . "'";
		$short .= "'" . esc_js( $short_day ) . "'";
		if ( $i < ( count( $days ) - 1 ) ) {
			$js .= ", ";
			$short .= ", ";
		}
	}
	$js .= ");" . "\n";
	$js .= $short . ");" . "\n";

	// --- set countdown labels ---
	// 2.5.0: moved here from countdown enqueue function
	$js .= "radio.labels.showstarted = '" . esc_js( __( 'This Show has started.', 'radio-station' ) ) . "';" . "\n";
	$js .= "radio.labels.showended = '" . esc_js( __( 'This Show has ended.', 'radio-station' ) ) . "';" . "\n";
	$js .= "radio.labels.playlistended = '" . esc_js( __( 'This Playlist has ended.', 'radio-station' ) ) . "';" . "\n";
	$js .= "radio.labels.timecommencing = '" . esc_js( __( 'Commencing in', 'radio-station' ) ) . "';" . "\n";
	$js .= "radio.labels.timeremaining = '" . esc_js( __( 'Remaining Time', 'radio-station' ) ) . "';" . "\n";

	// --- translated time unit strings ---
	$js .= "radio.units.am = '" . esc_js( radio_station_translate_meridiem( 'am' ) ) . "'; ";
	$js .= "radio.units.pm = '" . esc_js( radio_station_translate_meridiem( 'pm' ) ) . "'; ";
	$js .= "radio.units.second = '" . esc_js( __( 'Second', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.seconds = '" . esc_js( __( 'Seconds', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.minute = '" . esc_js( __( 'Minute', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.minutes = '" . esc_js( __( 'Minutes', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.hour = '" . esc_js( __( 'Hour', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.hours = '" . esc_js( __( 'Hours', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.day = '" . esc_js( __( 'Day', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.days = '" . esc_js( __( 'Days', 'radio-station' ) ) . "'; " . "\n";

	// --- time key map ---
	// 2.3.3.9: added for PHP Date Format to MomentJS conversions
	// (object of approximate 'PHP date() key':'moment format() key' conversions)
	$js .= "radio.moment_map = {'d':'D', 'j':'D', 'w':'e', 'D':'e', 'l':'e', 'N':'e', 'S':'Do', ";
	$js .= "'F':'M', 'm':'M', 'n':'M', 'M':'M', 'Y':'YYYY', 'y':'YY',";
	$js .= "'a':'a', 'A':'a', 'g':'h', 'G':'H', 'g':'h', 'H':'H', 'i':'m', 's':'s'}" . "\n";

	// --- convert show times ---
	// 2.3.3.9:
	// 2.5.0: shorten logic for convert show times
	$usertimes = radio_station_get_setting( 'convert_show_times' );
	$convert_show_times = ( 'yes' === (string) $usertimes ) ? 'true' : 'false';
	$js .= "radio.convert_show_times = " . esc_js( $convert_show_times ) . ";" . "\n";

	// --- add inline script ---
	$js = apply_filters( 'radio_station_localization_script', $js );
	return $js;
}

// ---------------------
// Filter Streaming Data
// ---------------------
// 2.3.3.7: added streaming data filter for player integration
// 2.5.6: added missing 4th argument (arg count declaration)
add_filter( 'radio_player_data', 'radio_station_streaming_data', 10, 2 );
function radio_station_streaming_data( $data, $station = false ) {

	$data = array(
		'script'   => radio_station_get_setting( 'player_script' ),
		'instance' => 0,
		'url'      => radio_station_get_stream_url(),
		'format'   => radio_station_get_setting( 'streaming_format' ),
		'fallback' => radio_station_get_fallback_url(),
		'fformat'  => radio_station_get_setting( 'fallback_format' ),
	);
	
	// commented out: this breaks player settings
	// if ( RADIO_STATION_DEBUG ) {
	//	echo '<span style="display:none;">Player Stream Data: ' . esc_html( print_r( $data, true ) ) . '</span>' . "\n";
	// }
	$data = apply_filters( 'radio_station_streaming_data', $data, $station );
	return $data;
}

// --------------------
// Filter Player Script
// --------------------
// 2.5.7: disable Howler script setting (browser incompatibilities)
add_filter( 'radio_station_player_script', 'radio_station_filter_player_script', 11 );
function radio_station_filter_player_script( $script ) {
	if ( 'howler' == $script ) {
		$script = 'amplitude';
	}
	return $script;
}


// -------------------------
// === Transient Caching ===
// -------------------------
// 2.5.0: moved here from includes/support-functions.php

// --------------------------
// Delete Prefixed Transients
// --------------------------
// 2.3.3.4: added helper for clearing transient data
function radio_station_delete_transients_with_prefix( $prefix ) {
	global $wpdb;

	// 2.3.3.9: add trailing underscore to prefix
	$prefix = $wpdb->esc_like( '_transient_' . $prefix . '_' );

	// 2.3.3.9: fix to LIKE match
	// $query = "SELECT `option_name` FROM " . $wpdb->prefix . "options WHERE `option_name` LIKE '%" . $prefix . "%'";
	// $results = $wpdb->get_results( $query, ARRAY_A );
	// 2.5.9: use wpdb_prepare on LIKE statement
	// ref: https://wordpress.stackexchange.com/a/8834/76440
	$query = "SELECT `option_name` FROM " . $wpdb->prefix . "options WHERE `option_name` LIKE '%%%s%%'";
	$results = $wpdb->get_results( $wpdb->prepare( $query, $prefix ), ARRAY_A );

	// if ( RADIO_STATION_DEBUG ) {
	//	echo $query . PHP_EOL . '<br>';
	//	echo 'Transients: ' . esc_html( print_r( $results, true ) ) . "\n";
	// }
	if ( !$results || !is_array( $results ) || ( count( $results ) < 1 ) ) {
		return;
	}

	foreach ( $results as $option ) {
		// 2.3.3.9: fix to replace malfunctioning ltrim
		// $key = ltrim( $option['option_name'], '_transient_' );
		$key = substr( $option['option_name'], 11 );
		delete_transient( $key );
		// 2.3.3.9: also delete transient cache object by key
		wp_cache_delete( $key, 'transient' );
		// if ( RADIO_STATION_DEBUG ) {
		// 	echo "Deleting transient and cache object for '" . esc_html( $key ) . "'" . "\n";
		// }
	}
}

// -----------------
// Clear Cached Data
// -----------------
// 2.3.3.9: made into separate function
// 2.4.0.3: added second argument for post type
function radio_station_clear_cached_data( $post_id = false, $post_type = false ) {

	// --- clear main schedule transients ---
	// 2.3.3: remove current show transient
	// 2.3.4: add previous show transient
	delete_transient( 'radio_station_current_schedule' );
	delete_transient( 'radio_station_next_show' );
	delete_transient( 'radio_station_previous_show' );

	// --- clear time-based schedule transients ---
	// 2.3.4: delete all prefixed transients (for times)
	radio_station_delete_transients_with_prefix( 'radio_station_current_schedule' );
	radio_station_delete_transients_with_prefix( 'radio_station_next_show' );
	radio_station_delete_transients_with_prefix( 'radio_station_previous_show' );

	// --- maybe clear show meta data ---
	if ( $post_id ) {
		do_action( 'radio_station_clear_data', $post_type, $post_id );
		if ( $post_type ) {
			do_action( 'radio_station_clear_data', $post_type . '_meta', $post_id );
		}
	}

	// --- maybe send directory ping ---
	// 2.3.1: added directory update ping option
	// 2.3.2: queue directory ping
	radio_station_queue_directory_ping();

	// --- set last updated schedule time ---
	// 2.3.2: added for data API use
	update_option( 'radio_station_schedule_updated', time() );

}

// ---------------------------------
// Clear Cache on Status Transitions
// ---------------------------------
// 2.4.0.4: clear show and override caches on post status changes
add_action( 'transition_post_status', 'radio_station_clear_cache_on_transitions', 10, 3 );
function radio_station_clear_cache_on_transitions( $new_status, $old_status, $post ) {
	if ( $new_status == $old_status ) {
		return;
	}
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG );
	if ( in_array( $post->post_type, $post_types ) ) {
		radio_station_clear_cached_data( $post->ID, $post->post_type );
	}
}


// -----------------
// === Debugging ===
// -----------------

// --------------------------
// maybe Clear Transient Data
// --------------------------
// 2.3.0: clear show transients if debugging
// 2.3.1: added action to init hook
// 2.3.1: check clear show transients option
add_action( 'init', 'radio_station_clear_transients' );
function radio_station_clear_transients() {
	$clear_transients = radio_station_get_setting( 'clear_transients' );
	if ( RADIO_STATION_DEBUG || ( 'yes' === (string) $clear_transients ) ) {
		// 2.3.2: do not clear on AJAX calls
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		// 2.3.3.9: just use clear cached data function
		radio_station_clear_cached_data( false );
	}
}

// ------------------------
// Debug Output and Logging
// ------------------------
// 2.3.0: added debugging function
function radio_station_debug( $data, $echo = true, $file = false ) {

	// --- maybe output debug info ---
	if ( $echo ) {
		// 2.3.0: added span wrap for hidden display
		// 2.3.1.1: added class for page source searches
		// 2.5.0: wrap in esc_html anyway (though it might mangle debug output)
		echo '<span class="radio-station-debug" style="display:none;">' . esc_html( $data ) . '</span>' . PHP_EOL;
	}

	// --- check for logging constant ---
	if ( defined( 'RADIO_STATION_DEBUG_LOG' ) ) {
		if ( !$file && RADIO_STATION_DEBUG_LOG ) {
			$file = 'radio-station.log';
		} elseif ( false === RADIO_STATION_DEBUG_LOG ) {
			$file = false;
		}
	}

	// --- write to debug file ---
	if ( $file ) {
		if ( !is_dir( RADIO_STATION_DIR . '/debug' ) ) {
			wp_mkdir_p( RADIO_STATION_DIR . '/debug' );
		}
		$file = RADIO_STATION_DIR . '/debug/' . $file;
		error_log( $data, 3, $file );
	}
}

// ---------------------
// Freemius Object Debug
// ---------------------
// 2.4.0.4: added to debug freemius instance
add_action( 'shutdown', 'radio_station_freemius_debug' );
function radio_station_freemius_debug() {
	if ( is_admin() && RADIO_STATION_DEBUG && current_user_can( 'manage_options' ) ) {
		// 2.4.0.6: check if global instance is set directly
		if ( isset( $GLOBALS['radio_station_freemius'] ) ) {
			$instance = $GLOBALS['radio_station_freemius'];
			echo '<span style="display:none;">Freemius Object: ' . esc_html( print_r( $instance, true ) ) . '</span>';
		}
	}
}

// ---------------------------
// Debug Footer Scripts/Styles
// ---------------------------
// add_action( 'wp_footer', 'radio_station_script_debug', 1 );
function radio_station_script_debug() {
	if ( !RADIO_STATION_DEBUG ) {
		return;
	}
	if ( isset( $_REQUEST['debug-script'] ) ) {
		$handle = sanitize_text_field( $_REQUEST['debug-script'] );
		global $wp_scripts;
		$debug = $wp_scripts->registered[$handle];
		echo '<span style="display:none;">Script object for ' . esc_html( $handle ) . ': ' . esc_html( print_r( $debug, true ) ) . '</span>' . "\n";
	}
	if ( isset( $_REQUEST['debug-style'] ) ) {
		$handle = sanitize_text_field( $_REQUEST['debug-style'] );
		global $wp_styles;
		$debug = $wp_styles->registered[$handle];
		echo '<span style="display:none;">Style object for ' . esc_html( $handle ) . ': ' . esc_html( print_r( $debug, true ) ) . '</span>' . "\n";
	}
}
