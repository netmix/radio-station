<?php

/**
 * @package Radio Station
 * @version 2.4.0
 */
/*

Plugin Name: Radio Station
Plugin URI: https://radiostation.pro/radio-station
Description: Adds Show pages, DJ role, playlist and on-air programming functionality to your site.
Author: Tony Zeoli, Tony Hayes
Version: 2.4.0.9
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

// === Setup ===
// - Define Plugin Constants
// - Set Debug Mode Constant
// - Define Plugin Data Slugs
// - Include Plugin Files
// - Plugin Options and Defaults
// - Pro Version Install Check
// - Plugin Loader Settings
// - Start Plugin Loader Instance
// - Include Plugin Admin Files
// - Load Plugin Text Domain
// - Check Plugin Version
// - Flush Rewrite Rules on Deactivation
// - Enqueue Plugin Script
// - Enqueue Plugin Stylesheet
// - Enqueue Datepicker
// - Enqueue Localized Script Values
// - Localization Script
// === Template Filters ===
// - Get Template
// - Station Phone Number Filter
// - Automatic Pages Content Filter
// - Single Content Template Filter
// - Show Content Template Filter
// - Playlist Content Template Filter
// - Override Content Template Filter
// - DJ / Host / Producer Template Fix
// - Get DJ / Host Template
// - Get Producer Template
// - Single Template Hierarchy
// - Single Templates Loader
// - Archive Template Hierarchy
// x Archive Templates Loader
// - Add Links Back to Show
// - Show Posts Adjacent Links
// === Query Filters ===
// - Playlist Archive Query Filter
// - Schedule Override Filters
// === User Roles ===
// - Set Roles and Capabilities
// - Admin Fix for DJ / Host Role Label
// - maybe Revoke Edit Show Capability
// === Debugging ===
// - maybe Clear Transient Data
// - Debug Output and Logging


// -------------
// === Setup ===
// -------------

// -----------------------
// Define Plugin Constants
// -----------------------
// 2.3.1: added constant for Netmix Directory
// 2.4.0.3: remove separate constant for API docs link
// 2.4.0.3: update home URLs to radiostation.pro
// 2.4.0.8: added RADIO_STATION_SLUG constant
define( 'RADIO_STATION_SLUG', 'radio-station' );
define( 'RADIO_STATION_FILE', __FILE__ );
define( 'RADIO_STATION_DIR', dirname( __FILE__ ) );
define( 'RADIO_STATION_BASENAME', plugin_basename( __FILE__ ) );
define( 'RADIO_STATION_HOME_URL', 'https://radiostation.pro/radio-station/' );
define( 'RADIO_STATION_DOCS_URL', 'https://radiostation.pro/docs/' );
// define( 'RADIO_STATION_API_DOCS_URL', 'https://radiostation.pro/docs/api/' );
define( 'RADIO_STATION_PRO_URL', 'https://radiostation.pro/' );
define( 'RADIO_STATION_NETMIX_DIR', 'https://netmix.com/' );

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
	$rs_debug = false;
	if ( isset( $_REQUEST['rs-debug'] ) && ( '1' == $_REQUEST['rs-debug'] ) ) {
		$rs_debug = true;
	}
	define( 'RADIO_STATION_DEBUG', $rs_debug );
}
if ( !defined( 'RADIO_STATION_SAVE_DEBUG' ) ) {
	$rs_save_debug = false;
	if ( isset( $_REQUEST['rs-save-debug'] ) && ( '1' == $_REQUEST['rs-save-debug'] ) ) {
		$rs_save_debug = true;
	}
	define( 'RADIO_STATION_SAVE_DEBUG', $rs_save_debug );
}

// --------------------
// Include Plugin Files
// --------------------
// 2.3.0: include new data feeds file
// 2.3.0: renamed widget files to match new widget names
// 2.3.0: separate file for legacy support functions

// --- Main Includes ---
require RADIO_STATION_DIR . '/includes/post-types.php';
require RADIO_STATION_DIR . '/includes/support-functions.php';
require RADIO_STATION_DIR . '/includes/data-feeds.php';
require RADIO_STATION_DIR . '/includes/legacy.php';

// --- Player ---
// 2.4.0.4: include player as standard
require RADIO_STATION_DIR . '/player/radio-player.php';

// --- Shortcodes ---
require RADIO_STATION_DIR . '/includes/master-schedule.php';
require RADIO_STATION_DIR . '/includes/shortcodes.php';

// --- Widgets ---
// 2.4.0.4: move player widget here
require RADIO_STATION_DIR . '/includes/class-current-show-widget.php';
require RADIO_STATION_DIR . '/includes/class-upcoming-shows-widget.php';
require RADIO_STATION_DIR . '/includes/class-current-playlist-widget.php';
require RADIO_STATION_DIR . '/includes/class-radio-clock-widget.php';
require RADIO_STATION_DIR . '/includes/class-radio-player-widget.php';

// --- Feature Development ---
// 2.3.0: add feature branch development includes
// 2.3.1: added radio player widget file
$features = array( 'import-export' );
foreach ( $features as $feature ) {
	$filepath = RADIO_STATION_DIR . '/includes/' . $feature . '.php';
	if ( file_exists ( $filepath ) ) {
		include $filepath;
	}
}

// ---------------------------
// Plugin Options and Defaults
// ---------------------------
// 2.3.0: added plugin options
// 2.4.0.8: moved options array to separate file
$timezones = radio_station_get_timezone_options( true );
$languages = radio_station_get_language_options( true );
$formats = radio_station_get_stream_formats();
include RADIO_STATION_DIR . '/options.php';


// -------------------------
// Pro Version Install Check
// -------------------------
// 2.4.0.3: added check active/installed Pro version
// 2.4.0.4: add defaults for has_addons and has_plans
$has_addons = false;
$has_plans = true;
$plan = 'free';

// --- check for deactivated pro plugin ---
// 2.4.0.4: remove unnecessary second argument to wp_cache_get
$plugins = wp_cache_get( 'plugins' );
if ( !$plugins ) {
	if ( function_exists( 'get_plugins' ) ) {
		$plugins = get_plugins();
	} else {
		$plugin_path = ABSPATH . 'wp-admin/includes/plugin.php';
		if ( file_exists( $plugin_path ) ) {
			include $plugin_path;
			$plugins = get_plugins();
		}
	}
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

// ----------------------
// Plugin Loader Settings
// ----------------------
// 2.3.0: added plugin loader settings
// 2.4.0.8: use RADIO_STATION_SLUG constant

// --- settings array ---
$settings = array(
	// --- Plugin Info ---
	'slug'         => RADIO_STATION_SLUG,
	'file'         => __FILE__,
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
	'freemius_id'  => '4526',
	'freemius_key' => 'pk_aaf375c4fb42e0b5b3831e0b8476b',
	'hasplans'     => $has_plans,
	'upgrade_link' => add_query_arg( 'page', RADIO_STATION_SLUG . '-pricing', admin_url( 'admin.php' ) ),
	'pro_link'     => RADIO_STATION_PRO_URL . 'pricing/',
	'hasaddons'    => $has_addons,
	'addons_link'  => add_query_arg( 'page', RADIO_STATION_SLUG . '-addons', admin_url( 'admin.php' ) ),
	'plan'         => $plan,
	// 2.4.0.6: add bundles configuration
	// 'bundle_id'           => '9521',
	// 'bundle_public_key'   => 'pk_a2650f223ef877e87fe0fdfc4442b',
	// 'bundle_license_auto_activation' => true,
);

// -------------------------
// Set Plugin Option Globals
// -------------------------
global $radio_station_data;
$radio_station_data['options'] = $options;
$radio_station_data['settings'] = $settings;
if ( RADIO_STATION_DEBUG ) {
	echo '<span style="display:none;">Radio Station Settings: ' . print_r( $settings, true ) . '</span>';
}

// ----------------------------
// Start Plugin Loader Instance
// ----------------------------
require RADIO_STATION_DIR . '/loader.php';
$instance = new radio_station_loader( $settings );

// --------------------------
// Include Plugin Admin Files
// --------------------------
// 2.2.7: added conditional load of admin includes
// 2.2.7: moved all admin functions to radio-station-admin.php
if ( is_admin() ) {
	require RADIO_STATION_DIR . '/radio-station-admin.php';
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

// -----------------
// Plugin Activation
// -----------------
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

// -----------------------------------
// Flush Rewrite Rules on Deactivation
// -----------------------------------
// 2.4.0.8: clear plugin updates transient on deactivation
register_deactivation_hook( RADIO_STATION_FILE, 'radio-station_deactivatoin');
function radio_station_deactivation() {
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
	if ( file_exists( $default_path ) ) {
		return $icon_path;
	}
	return $local_path;
}

// ----------------------
// Enqueue Plugin Scripts
// ----------------------
// 2.3.0: added for enqueueing main Radio Station script
add_action( 'wp_enqueue_scripts', 'radio_station_enqueue_scripts' );
function radio_station_enqueue_scripts() {

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

	// --- Moment.js ---
	// ref: https://momentjs.com
	// 2.3.3.9: added for improved time format display
	$moment_url = plugins_url( 'js/moment' . $suffix . '.js', RADIO_STATION_FILE );
	wp_enqueue_script( 'momentjs', $moment_url, array(), '2.29.1', false );

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
		// TODO: maybe allow for minor version release numbers
		// if ( ( 5 == $version_length ) || ( 7 == $version_length ) ) {
		if ( 5 == $version_length ) {
			$version = $plugin_version;
		} else {
			$version = filemtime( $template['file'] );
		}

		$url = $template['url'];

		// --- enqueue script ---
		wp_enqueue_script( $scriptkey, $url, $deps, $version, $infooter );
	}
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
			// TODO: maybe allow for minor version release numbers ?
			// if ( ( 5 == $version_length ) || ( 7 == $version_length ) ) {
			if ( 5 == $version_length ) {
				$version = $plugin_version;
			} else {
				$version = filemtime( $template['file'] );
			}
			$url = $template['url'];

			// --- enqueue styles in footer ---
			wp_enqueue_style( 'rs-' . $stylekey, $url, array(), $version, 'all' );

			// --- set style enqueued switch ---
			$radio_station_styles[$stylekey] = true;
		}
	}
}

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
	// $protocol = 'http';
	// if ( is_ssl() ) {$protocol .= 's';}
	// $url = $protocol . '://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css';
	// wp_enqueue_style( 'jquery-ui-style', $url, array(), '1.12.1' );
	$style = radio_station_get_template( 'both', 'jquery-ui.css', 'css' );
	wp_enqueue_style( 'jquery-ui-smoothness', $style['url'], array(), '1.12.1', 'all' );

}

// -------------------------------
// Enqueue Localized Script Values
// -------------------------------
add_action( 'wp_enqueue_scripts', 'radio_station_localize_script' );
function radio_station_localize_script() {

	$js = radio_station_localization_script();
	wp_add_inline_script( 'radio-station', $js );
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
	$js .= "radio.ajax_url = '" . esc_url( admin_url( 'admin-ajax.php' ) ) . "';" . PHP_EOL;

	// --- clock time format ---
	// TODO: maybe set time format ?
	// ref: https://devhints.io/wip/intl-datetime
	$clock_format = radio_station_get_setting( 'clock_time_format' );
	$js .= "radio.clock_format = '" . esc_js( $clock_format ) . "';" . PHP_EOL;

	// --- detect touchscreens ---
	// ref: https://stackoverflow.com/a/52855084/5240159
	$js .= "if (window.matchMedia('(pointer: coarse)').matches) {radio.touchscreen = true;} else {radio.touchscreen = false;}" . PHP_EOL;

	// --- set debug flag ---
	if ( RADIO_STATION_DEBUG ) {
		$js .= "radio.debug = true;" . PHP_EOL;
	} else {
		$js .= "radio.debug = false;" . PHP_EOL;
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
		$js .= "radio.timezone.code = 'UTC" . esc_js( $offset ) . "'; ";
		$js .= "radio.timezone.utc = '" . esc_js( $offset ) . "'; ";
		$js .= "radio.timezone.utczone = true; ";

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
		$js .= "radio.timezone.location = '" . esc_js( $timezone ) . "'; ";
		$js .= "radio.timezone.offset = " . esc_js( $offset ) . "; ";
		$js .= "radio.timezone.code = '" . esc_js( $code ) . "'; ";
		$js .= "radio.timezone.utc = '" . esc_js( $utc_offset ) . "'; ";
		$js .= "radio.timezone.utczone = false; ";

	}

	if ( defined( 'RADIO_STATION_USE_SERVER_TIMES' ) && RADIO_STATION_USE_SERVER_TIMES ) {
		$js .= "radio.timezone.adjusted = false; ";
	} else {
		$js .= "radio.timezone.adjusted = true; ";
	}

	// --- set user timezone offset ---
	// (and convert offset minutes to seconds)
	$js .= "radio.timezone.useroffset = (new Date()).getTimezoneOffset() * 60;" . PHP_EOL;

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
	$js .= ");" . PHP_EOL;
	$js .= $short . ");" . PHP_EOL;

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
	$js .= ");" . PHP_EOL;
	$js .= $short . ");" . PHP_EOL;

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
	$js .= "radio.units.days = '" . esc_js( __( 'Days', 'radio-station' ) ) . "'; " . PHP_EOL;

	// --- time key map ---
	// 2.3.3.9: added for PHP Date Format to MomentJS conversions
	// (object of approximate 'PHP date() key':'moment format() key' conversions)
	$js .= "radio.moment_map = {'d':'D', 'j':'D', 'w':'e', 'D':'e', 'l':'e', 'N':'e', 'S':'Do', ";
	$js .= "'F':'M', 'm':'M', 'n':'M', 'M':'M', 'Y':'YYYY', 'y':'YY',";
	$js .= "'a':'a', 'A':'a', 'g':'h', 'G':'H', 'g':'h', 'H':'H', 'i':'m', 's':'s'}" . PHP_EOL;

	// --- convert show times ---
	// 2.3.3.9:
	$usertimes = radio_station_get_setting( 'convert_show_times' );
	if ( 'yes' == $usertimes ) {
		$js .= "radio.convert_show_times = true;" . PHP_EOL;
	} else {
		$js .= "radio.convert_show_times = false;" . PHP_EOL;
	}

	// --- add inline script ---
	$js = apply_filters( 'radio_station_localization_script', $js );
	return $js;

}

// -------------------------
// Filter for Streaming Data
// -------------------------
// 2.3.3.7: added streaming data filter for player integration
add_filter( 'radio_station_player_data', 'radio_station_streaming_data' );
function radio_station_streaming_data( $data, $station = false ) {
	$data = array(
		'script'	=> radio_station_get_setting( 'player_script' ),
		'instance'	=> 0,
		'url'		=> radio_station_get_stream_url(),
		'format'	=> radio_station_get_setting( 'streaming_format' ),
		'fallback'	=> radio_station_get_fallback_url(),
		'fformat'	=> radio_station_get_setting( 'fallback_format' ),
	);
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Player Stream Data: ' . print_r( $data, true ) . '</span>';
	}
	$data = apply_filters( 'radio_station_streaming_data', $data, $station );
	return $data;
}

// -----------------------------------------
// Fix to Redirect Plugin Settings Menu Link
// -----------------------------------------
// 2.3.2: added settings submenu page redirection fix
add_action( 'init', 'radio_station_settings_page_redirect' );
function radio_station_settings_page_redirect() {

	// --- bug out if not admin page ---
	if ( !is_admin() ) {
		return;
	}

	// --- but out if not plugin settings page ---
	if ( !isset( $_REQUEST['page'] ) || ( 'radio-station' != $_REQUEST['page'] ) ) {
		return;
	}

	// --- check if link is for options-general.php ---
	if ( strstr( $_SERVER['REQUEST_URI'], '/options-general.php' ) ) {

		// --- redirect to plugin settings page (admin.php) ---
		$url = add_query_arg( 'page', 'radio-station', admin_url( 'admin.php' ) );
		wp_redirect( $url );
		exit;
	}
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
	if ( !isset( $_REQUEST['action'] ) || ( 'radio_player' != $_REQUEST['action'] ) ) {
		return $origins;
	}
	$allowed = array( 'https://netmix.com' );
	$allowed = apply_filters( 'radio_station_player_allowed_origins', $allowed );
	foreach ( $allowed as $allow ) {
		$origins[] = $allow;
	}
	return $origins;
}


// ------------------------
// === Template Filters ===
// ------------------------

// --------------------
// Doing Template Check
// --------------------
// 2.3.3.9: added to help distinguish filter contexts
function radio_station_doing_template() {
	global $radio_station_data;
	if ( isset( $radio_station_data['doing-template'] ) && $radio_station_data['doing-template'] ) {
		return true;
	}
	return false;
}

// ------------
// Get Template
// ------------
// 2.3.0: added for template file hierarchy
function radio_station_get_template( $type, $template, $paths = false ) {

	global $radio_station_data;

	// --- maybe set default paths ---
	if ( !$paths ) {
		if ( isset( $radio_station_data['template-dirs'] ) ) {
			$dirs = $radio_station_data['template-dirs'];
		}
		$paths = array( 'templates', '' );
	} elseif ( is_string( $paths ) ) {
		if ( 'css' == $paths ) {
			if ( isset( $radio_station_data['style-dirs'] ) ) {
				$dirs = $radio_station_data['style-dirs'];
			}
			$paths = array( 'css', 'styles', '' );
		} elseif ( 'js' == $paths ) {
			if ( isset( $radio_station_data['script-dirs'] ) ) {
				$dirs = $radio_station_data['script-dirs'];
			}
			$paths = array( 'js', 'scripts', '' );
		}
	}

	if ( !isset( $dirs ) ) {
		$dirs = array();
		$styledir = get_stylesheet_directory();
		$styledirurl = get_stylesheet_directory_uri();
		$templatedir = get_template_directory();
		$templatedirurl = get_template_directory_uri();

		// --- maybe generate default hierarchies ---
		foreach ( $paths as $path ) {
			$dirs[] = array(
				'path'    => $styledir . '/' . $path,
				'urlpath' => $styledirurl . '/' . $path,
			);
		}
		if ( $styledir != $templatedir ) {
			foreach ( $paths as $path ) {
				$dirs[] = array(
					'path'    => $templatedir . '/' . $path,
					'urlpath' => $templatedirurl . '/' . $path,
				);
			}
		}
		if ( defined( 'RADIO_STATION_PRO_DIR' ) ) {
			foreach ( $paths as $path ) {
				$dirs[] = array(
					'path'    => RADIO_STATION_PRO_DIR . '/' . $path,
					'urlpath' => plugins_url( $path, RADIO_STATION_PRO_FILE ),
				);
			}
		}
		foreach ( $paths as $path ) {
			$dirs[] = array(
				'path'    => RADIO_STATION_DIR . '/' . $path,
				'urlpath' => plugins_url( $path, RADIO_STATION_FILE ),
			);
		}
	}
	$dirs = apply_filters( 'radio_station_template_dir_hierarchy', $dirs, $template, $paths );

	// --- loop directory hierarchy to find first template ---
	foreach ( $dirs as $dir ) {

		// 2.3.4: use trailingslashit to account for empty paths
		$template_path = trailingslashit( $dir['path'] ) . $template;
		$template_url = trailingslashit( $dir['urlpath'] ) . $template;

		if ( file_exists( $template_path ) ) {
			if ( 'file' == (string) $type ) {
				return $template_path;
			} elseif ( 'url' === (string) $type ) {
				return $template_url;
			} else {
				return array( 'file' => $template_path, 'url' => $template_url );
			}
		}
	}

	return false;
}

// -------------------------------------
// Station Phone Number for Shows Filter
// -------------------------------------
// 2.3.3.6: added to return station phone for all Shows (if not set for Show)
add_filter( 'radio_station_show_phone', 'radio_station_phone_number', 10, 2 );
function radio_station_phone_number( $phone, $post_id ) {
	if ( $phone ) {
		return $phone;
	}
	$shows_phone = radio_station_get_setting( 'shows_phone' );
	if ( 'yes' == $shows_phone ) {
		$phone = radio_station_get_setting( 'station_phone' );
		return $phone;
	}
	return false;
}

// --------------------------------------
// Station Email Address for Shows Filter
// --------------------------------------
// 2.3.3.8: added to return station email for all Shows (if not set for Show)
add_filter( 'radio_station_show_email', 'radio_station_email_address', 10, 2 );
function radio_station_email_address( $email, $post_id ) {
	if ( $email ) {
		return $email;
	}
	$shows_email = radio_station_get_setting( 'shows_email' );
	if ( 'yes' == $shows_email ) {
		$email = radio_station_get_setting( 'station_email' );
		return $email;
	}
	return false;
}

// ------------------------------
// Automatic Pages Content Filter
// ------------------------------
// 2.3.0: standalone filter for automatic page content
// 2.3.1: re-add filter so the_content can be processed multiple times
// 2.3.3.6: set automatic content early and clear existing content
add_filter( 'the_content', 'radio_station_automatic_pages_content_set', 1 );
function radio_station_automatic_pages_content_set( $content ) {

	global $radio_station_data;

	// if ( isset( $radio_station_data['doing_excerpt'] ) && $radio_station_data['doing_excerpt'] ) {
	//	return $content;
	// }

	// --- for automatic output on selected master schedule page ---
	$schedule_page = radio_station_get_setting( 'schedule_page' );
	if ( !is_null( $schedule_page ) && !empty( $schedule_page ) ) {
		if ( is_page( $schedule_page ) ) {
			$automatic = radio_station_get_setting( 'schedule_auto' );
			if ( 'yes' === (string) $automatic ) {
				$view = radio_station_get_setting( 'schedule_view' );
				$atts = array( 'view' => $view );
				$atts = apply_filters( 'radio_station_automatic_schedule_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[master-schedule' . $atts_string . ']';
			}
		}
	}

	// --- show archive page ---
	// 2.3.0: added automatic display of show archive page
	$show_archive_page = radio_station_get_setting( 'show_archive_page' );
	if ( !is_null( $show_archive_page ) && !empty( $show_archive_page ) ) {
		if ( is_page( $show_archive_page ) ) {
			$automatic = radio_station_get_setting( 'show_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				// $view = radio_station_get_setting( 'show_archive_view' );
				// if ( $view ) {
				// 	$atts['view'] = $view;
				// }
				$atts = apply_filters( 'radio_station_automatic_show_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[shows-archive' . $atts_string . ']';
			}
		}
	}

	// --- override archive page ---
	// 2.3.0: added automatic display of override archive page
	$override_archive_page = radio_station_get_setting( 'override_archive_page' );
	if ( !is_null( $override_archive_page ) && !empty( $override_archive_page ) ) {
		if ( is_page( $override_archive_page ) ) {
			$automatic = radio_station_get_setting( 'override_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				// $view = radio_station_get_setting( 'override_archive_view' );
				// if ( $view ) {
				// 	$atts['view'] = $view;
				// }
				$atts = apply_filters( 'radio_station_automatic_override_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[overrides-archive' . $atts_string . ']';
			}
		}
	}

	// --- playlist archive page ---
	// 2.3.0: added automatic display of playlist archive page
	$playlist_archive_page = radio_station_get_setting( 'playlist_archive_page' );
	if ( !is_null( $playlist_archive_page ) && !empty( $playlist_archive_page ) ) {
		if ( is_page( $playlist_archive_page ) ) {
			$automatic = radio_station_get_setting( 'playlist_archive_auto' );
			if ( 'yes' == $automatic ) {
				$atts = array();
				// $view = radio_station_get_setting( 'playlist_archive_view' );
				// if ( $view ) {
				// 	$atts['view'] = $view;
				// }
				$atts = apply_filters( 'radio_station_automatic_playlist_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[playlists-archive' . $atts_string . ']';
			}
		}
	}

	// --- genre archive page ---
	// 2.3.0: added automatic display of genre archive page
	$genre_archive_page = radio_station_get_setting( 'genre_archive_page' );
	if ( !is_null( $genre_archive_page ) && !empty( $genre_archive_page ) ) {
		if ( is_page( $genre_archive_page ) ) {
			$automatic = radio_station_get_setting( 'genre_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				// $view = radio_station_get_setting( 'genre_archive_view' );
				// if ( $view ) {
				// 	$atts['view'] = $view;
				// }
				$atts = apply_filters( 'radio_station_automatic_genre_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[genres-archive' . $atts_string. ']';
			}
		}
	}

	// --- languages archive page ---
	// 2.3.3.9: added automatic display of language archive page
	$language_archive_page = radio_station_get_setting( '' );
	if ( !is_null( $language_archive_page ) && !empty( $language_archive_page ) ) {
		if ( is_page( $language_archive_page ) ) {
			$automatic = radio_station_get_setting( 'language_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				// $view = radio_station_get_setting( 'language_archive_view' );
				// if ( $view ) {
				// 	$atts['view'] = $view;
				// }
				$atts = apply_filters( 'radio_station_automatic_languagee_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[languages-archive' . $atts_string. ']';
			}
		}
	}

	// 2.3.3.6: moved out to reduce repetitive code
	if ( isset( $shortcode ) ) {
		remove_filter( 'the_content', 'radio_station_automatic_pages_content_set', 1 );
		remove_filter( 'the_content', 'radio_station_automatic_pages_content_get', 11 );
		$radio_station_data['automatic_content'] = do_shortcode( $shortcode );
		// 2.3.1: re-add filter so the_content may be processed multuple times
		add_filter( 'the_content', 'radio_station_automatic_pages_content_set', 1 );
		add_filter( 'the_content', 'radio_station_automatic_pages_content_get', 11 );
		// 2.3.3.6: clear existing content to allow for interim filters
		$content = '';
	}

	return $content;
}

// ----------------------------------
// Automatic Pages Content Set Filter
// ----------------------------------
// 2.3.3.6: append existing automatic page content to allow for interim filters
add_filter( 'the_content', 'radio_station_automatic_pages_content_get', 11 );
function radio_station_automatic_pages_content_get( $content ) {
	global $radio_station_data;
	if ( isset( $radio_station_data['automatic_content'] ) ) {
		$content .= $radio_station_data['automatic_content'];
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
	if ( !is_singular( $post_type ) ) {
		return $content;
	}

	// --- check for user content templates ---
	// 2.3.3.9: allow for prefixed and unprefixed post types
	$theme_dir = get_stylesheet_directory();
	$templates = array();
	$templates[] = $theme_dir . '/templates/single-' . $post_type . '-content.php';
	$templates[] = $theme_dir . '/single-' . $post_type . '-content.php';
	$templates[] = RADIO_STATION_DIR . '/templates/single-' . $post_type . '-content.php';
	$unprefixed_post_type = str_replace( 'rs-', '', $post_type );
	if ( $post_type != $unprefixed_post_type ) {
		$templates[] = $theme_dir . '/templates/single-' . $unprefixed_post_type . '-content.php';
		$templates[] = $theme_dir . '/single-' . $unprefixed_post_type . '-content.php';
		$templates[] = RADIO_STATION_DIR . '/templates/single-' . $unprefixed_post_type . '-content.php';
	}

	// 2.3.0: fallback to show content template for overrides
	if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
		// $templates[] = $theme_dir . '/templates/single-rs-show-content.php';
		// $templates[] = $theme_dir . '/single-rs-show-content.php';
		// $templates[] = RADIO_STATION_DIR . '/templates/single-rs-show-content.php';
		$templates[] = $theme_dir . '/templates/single-show-content.php';
		$templates[] = $theme_dir . '/single-show-content.php';
		$templates[] = RADIO_STATION_DIR . '/templates/single-show-content.php';
	}
	$templates = apply_filters( 'radio_station_' . $post_type . '_content_templates', $templates, $post_type );
	foreach ( $templates as $template ) {
		if ( file_exists( $template ) ) {
			$content_template = $template;
			break;
		}
	}
	if ( !isset( $content_template ) ) {
		return $content;
	}

	// --- enqueue template styles ---
	// 2.3.3.9: check post type for page template style enqueue
	$page_templates = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG, RADIO_STATION_PLAYLIST_SLUG );
	if ( in_array( $post_type, $page_templates ) ) {
		radio_station_enqueue_style( 'templates' );
	}
	// 2.3.3.9: fire action for enqueueing other template styles
	do_action( 'radio_station_enqueue_template_styles', $post_type );

	// --- enqueue dashicons for frontend ---
	wp_enqueue_style( 'dashicons' );

	// --- filter post before including template ---
	global $post;
	$original_post = $post;
	$post = apply_filters( 'radio_station_single_template_post_data', $post, $post_type );

	// --- start buffer and include content template ---
	ob_start();
	include $content_template;
	$output = ob_get_contents();
	ob_end_clean();

	// --- restore post global to be safe ---
	$post = $original_post;

	// --- filter and return buffered content ---
	$output = str_replace( '<!-- the_content -->', $content, $output );
	$post_id = get_the_ID();
	$output = apply_filters( 'radio_station_content_' . $post_type, $output, $post_id );

	return $output;
}

// ------------------------------------
// Filter for Override Show Linked Data
// ------------------------------------
add_filter( 'radio_station_single_template_post_data', 'radio_station_override_linked_show_data', 10, 2 );
function radio_station_override_linked_show_data( $post, $post_type ) {
	if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
		$linked_id = get_post_meta( $post->ID, 'linked_show_id', true );
		if ( $linked_id ) {
			$show_post = get_post( $linked_id );
			if ( $show_post ) {
				$linked_fields = get_post_meta( $post->ID, 'linked_show_fields', true );
				if ( $linked_fields ) {
					foreach ( $linked_fields as $key => $switch ) {
						if ( !$switch ) {
							if ( 'show_title' == $key ) {
								$post->post_title = $show_post->post_title;
							} elseif ( 'show_excerpt' == $key ) {
								$post->post_excerpt = $show_post->post_excerpt;
							} elseif ( 'show_content' == $key ) {
								$post->post_content = $show_post->post_content;
							}
						}
					}
				}
			}
		}
	}
	return $post;
}

// ----------------------------
// Show Content Template Filter
// ----------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_show_content_template', 11 );
function radio_station_show_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_show_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_SHOW_SLUG );
	// 2.3.1: re-add filter so the_content can be processed multuple times
	add_filter( 'the_content', 'radio_station_show_content_template', 11 );
	return $output;
}

// --------------------------------
// Playlist Content Template Filter
// --------------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
function radio_station_playlist_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_PLAYLIST_SLUG );
	// 2.3.1: re-add filter so the_content can be processed multuple times
	add_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
	return $output;
}

// --------------------------------
// Override Content Template Filter
// --------------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_override_content_template', 11 );
function radio_station_override_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_override_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_OVERRIDE_SLUG );
	// 2.3.1: re-add filter so the_content can be processed multiple times
	add_filter( 'the_content', 'radio_station_override_content_template', 11 );
	return $output;
}

// ----------------------------------
// Override Content with Show Content
// ----------------------------------
// 2.3.3.9: maybe use show content for override content
add_filter( 'the_content', 'radio_station_override_content', 0 );
function radio_station_override_content( $content ) {
	if ( !is_singular( RADIO_STATION_OVERRIDE_SLUG ) ) {
		return $content;
	}
	remove_filter( 'the_content', 'radio_station_override_content', 0 );
	global $post;
	$override = radio_station_get_show_override( $post->ID, 'show_content' );
	if ( false !== $override ) {
		$override = radio_station_override_linked_show_data( $post, RADIO_STATION_OVERRIDE_SLUG );
		$content = $override->post_content;
	}
	add_filter( 'the_content', 'radio_station_override_content', 0 );
	return $content;
}

// ---------------------------------
// DJ / Host / Producer Template Fix
// ---------------------------------
// 2.2.8: temporary fix to not 404 author pages for DJs without blog posts
// Ref: https://wordpress.org/plugins/show-authors-without-posts/
add_filter( '404_template', 'radio_station_author_host_pages' );
function radio_station_author_host_pages( $template ) {

	global $wp_query;
	if ( !is_author() ) {

		if ( get_query_var( 'host' ) ) {

			// --- get user by ID or name ---
			$host = get_query_var( 'host' );
			if ( absint( $host ) > - 1 ) {
				$user = get_user_by( 'ID', $host );
			} else {
				$user = get_user_by( 'slug', $host );
			}

			// --- check if specified user has DJ/host role ---
			if ( $user && in_array( 'dj', $user->roles ) ) {
				$host_template = radio_station_get_host_template();
				if ( $host_template ) {
					$template = $host_template;
				}
			}

		} elseif ( get_query_var( 'producer' ) ) {

			// --- get user by ID or name ---
			$producer = get_query_var( 'producer' );
			if ( absint( $producer ) > - 1 ) {
				$user = get_user_by( 'ID', $producer );
			} else {
				$user = get_user_by( 'slug', $producer );
			}

			// --- check if specified user has producer role ---
			if ( $user && in_array( 'producer', $user->roles ) ) {
				$producer_template = radio_station_get_producer_template();
				if ( $producer_template ) {
					$template = $producer_template;
				}
			}

		} elseif ( get_query_var( 'author' ) && ( 0 == $wp_query->posts->post ) ) {

			// --- get the author user ---
			if ( get_query_var( 'author_name' ) ) {
				$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
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

	return get_query_template( RADIO_STATION_HOST_SLUG, $templates );
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

	return get_query_template( RADIO_STATION_PRODUCER_SLUG, $templates );
}

// -------------------------
// Single Template Hierarchy
// -------------------------
function radio_station_single_template_hierarchy( $templates ) {

	global $post;

	// --- remove single.php as the show / playlist fallback ---
	// (allows for user selection of page.php or single.php later)
	if ( ( RADIO_STATION_SHOW_SLUG === (string) $post->post_type )
		 || ( RADIO_STATION_OVERRIDE_SLUG === (string) $post->post_type )
	     || ( RADIO_STATION_PLAYLIST_SLUG === (string) $post->post_type ) ) {
		$i = array_search( 'single.php', $templates );
		if ( false !== $i ) {
			unset( $templates[$i] );
		}
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
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG, RADIO_STATION_PLAYLIST_SLUG );
	// TODO: RADIO_STATION_EPISODE_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG
	if ( in_array( $post_type, $post_types ) ) {

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
			if ( 'yes' != $combined ) {
				remove_filter( 'the_content', 'radio_station_' . $post_type . '_content_template', 11 );
			}

		} else {

			// --- get template selection ---
			// 2.3.0: removed default usage of single show/playlist templates (not theme agnostic)
			// 2.3.0: added option for use of template hierarchy
			$show_template = radio_station_get_setting( $post_type . '_template' );

			// --- maybe use legacy template ---
			if ( 'legacy' === (string) $show_template ) {
				return RADIO_STATION_DIR . '/templates/legacy/single-' . $post_type . '.php';
			}

			// --- use post or page template ---
			// 2.3.3.8: added missing singular.php template setting
			if ( 'post' == $show_template ) {
				$templates = array( 'single.php' );
			} elseif ( 'page' == $show_template ) {
				$templates = array( 'page.php' );
			} elseif ( 'singular' == $show_template ) {
				$template = array( 'singular.php' );
			}

			// --- add standard fallbacks to index ---
			// 2.3.3.8: remove singular fallback as it is explicitly chosen
			$templates[] = 'index.php';
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
		$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_PLAYLIST_SLUG, RADIO_STATION_OVERRIDE_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
		if ( in_array( $post_type, $post_types ) ) {
			$template = array( 'templates/archive-' . $post_type . '.php' );
			// 2.3.0: add fallback to show archive template for overrides
			if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
				$template[] = 'templates/archive-' . RADIO_STATION_SHOW_SLUG . '.php';
			}
			$templates = array_merge( $template, $templates );
		}
	}

	return $templates;
}

// ------------------------
// Archive Templates Loader
// ------------------------
// TODO: implement standard archive page overrides via plugin settings
// add_filter( 'archive_template', 'radio_station_post_type_archive_template', 10, 3 );
function radio_station_post_type_archive_template( $archive_template, $type, $templates ) {
	global $post;

	// --- check for archive template override ---
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_PLAYLIST_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
	foreach ( $post_types as $post_type ) {
		if ( is_post_type_archive( $post_type ) ) {
			$override = radio_station_get_setting( $post_type . '_archive_override' );
			if ( 'yes' !== (string) $override ) {
				$archive_template = get_page_template();
				add_filter( 'the_content', 'radio_station_' . $post_type . '_archive', 11 );
			}
		}
	}

	return $archive_template;
}

// -------------------------
// Add Links to Back to Show
// -------------------------
// 2.3.0: add links to show from show posts and playlists
// 2.3.3.6: allow for multiple related show post assignments
add_filter( 'the_content', 'radio_station_add_show_links', 20 );
function radio_station_add_show_links( $content ) {

	global $post;

	// note: playlists are linked via single-playlist-content.php template

	// 2.4.0.6: bug out if no post object
	if ( !is_object( $post ) ) {
		return $content;
	}

	// --- filter to allow related post types ---
	$post_type = $post->post_type;
	$post_types = array( 'post' );
	$post_types = apply_filters( 'radio_station_show_related_post_types', $post_types );

	if ( in_array( $post_type, $post_types ) ) {

		// --- link show posts ---
		$related_shows = get_post_meta( $post->ID, 'post_showblog_id', true );
		// 2.3.3.6: convert string value if not multiple
		if ( $related_shows && !is_array( $related_shows ) ) {
			$related_shows = array( $related_shows );
		}
		// 2.3.3.6: remove possible zero values
		// 2.3.3.7: added count check for before looping
		if ( $related_shows && ( count( $related_shows ) > 0 ) ) {
			foreach ( $related_shows as $i => $related_show ) {
				if ( 0 == $related_show ) {
					unset( $related_shows[$i] );
				}
			}
		}
		if ( $related_shows && is_array( $related_shows ) && ( count( $related_shows ) > 0 ) ) {

			$positions = array( 'after' );
			$positions = apply_filters( 'radio_station_link_to_show_positions', $positions, $post_type, $post );
			if ( $positions && is_array( $positions ) && ( count( $positions ) > 0 ) ) {
				if ( in_array( 'before', $positions ) || in_array( 'after', $positions ) ) {

					// --- set related shows link(s) ---
					// 2.3.3.6: get all related show links
					$show_links = '';
					$hash_ref = '#show-' . str_replace( 'rs-', '', $post_type ) . 's';
					foreach ( $related_shows as $related_show ) {
						$show = get_post( $related_show );
						$title = $show->post_title;
						$permalink = get_permalink( $show->ID ) . $hash_ref;
						if ( '' != $show_links ) {
							$show_links .= ', ';
						}
						$show_links .= '<a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a>';
					}

					// --- set post type labels ---
					$before = $after = '';
					$post_type_object = get_post_type_object( $post_type );
					$singular = $post_type_object->labels->singular_name;
					$plural = $post_type_object->labels->name;

					// --- before content links ---
					if ( in_array( 'before', $positions ) ) {
						if ( count( $related_shows ) > 1 ) {
							$label = sprintf( __( '%s for Shows', 'radio-station' ), $singular );
						} else {
							$label = sprintf( __( '%s for Show', 'radio-station' ), $singular );
						}
						$before = $label . ': ' . $show_links . '<br><br>';
						$before = apply_filters( 'radio_station_link_to_show_before', $before, $post, $related_shows );
					}

					// --- after content links ---
					if ( in_array( 'after', $positions ) ) {
						if ( count( $related_shows ) > 1 ) {
							$label = sprintf( __( 'More %s for Shows', 'radio-station' ), $plural );
						} else {
							$label = sprintf( __( 'More %s for Show', 'radio-station' ), $plural );
						}
						$after = '<br>' . $label . ': ' . $show_links;
						$after = apply_filters( 'radio_station_link_to_show_after', $after, $post, $related_shows );
					}
					$content = $before . $content . $after;
				}
			}
		}

	}

	// --- adjacent post links debug output ---
	if ( RADIO_STATION_DEBUG ) {
		$content .= '<span style="display:none;">Previous Post Link: ' . get_previous_post_link() . '</span>' . PHP_EOL;
		$content .= '<span style="display:none;">Next Post Link: ' . get_next_post_link() . '</span>' . PHP_EOL;
	}

	return $content;
}

// -------------------------
// Show Posts Adjacent Links
// -------------------------
// 2.3.0: added show post adjacent links filter
add_filter( 'next_post_link', 'radio_station_get_show_post_link', 11, 5 );
add_filter( 'previous_post_link', 'radio_station_get_show_post_link', 11, 5 );
function radio_station_get_show_post_link( $output, $format, $link, $adjacent_post, $adjacent ) {

	global $radio_station_data, $post;

	// --- filter next and previous Show links ---
	// 2.3.4: add filtering for adjacent show links
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG );
	if ( in_array( $post->post_type, $post_types ) ) {
		if ( RADIO_STATION_OVERRIDE_SLUG == $post->post_type ) {
			// 2.3.3.6: get next/previous Show for override date/time
			// 2.3.3.9: modified to handle multiple override times
			// 2.3.3.9: added check that schedule key is set
			$scheds = get_post_meta( $post->ID, 'show_override_sched', true );
			if ( $scheds && is_array( $scheds ) ) {
				if ( array_key_exists( 'date', $scheds ) ) {
					$sched = array( $scheds );
				}
				$now = time();
				foreach ( $scheds as $sched ) {
					$override_start = $sched['date'] . ' ' . $sched['start_hour'] . ':' . $sched['start_min'] . ' ' . $sched['start_meridian'];
					$override_time = radio_station_get_time( ( $override_start + 1 ) );
					if ( !isset( $time ) ) {
						$time = $override_time;
					} elseif ( ( $time < $now ) && ( $override_time > $now ) ) {
						$time = $override_time;
					}
				}
				if ( 'next' == $adjacent ) {
					$show = radio_station_get_next_show( $time );
				} elseif ( 'previous' == $adjacent ) {
					$show = radio_station_get_previous_show( $time );
				}
			}
		} else {
			$shifts = get_post_meta( $post->ID, 'show_sched', true );
			if ( $shifts && is_array( $shifts ) ) {
				if ( count( $shifts ) < 1 ) {
					// 2.3.3.6: default to standard adjacent post link
					return $output;
				}
				if ( 1 == count( $shifts ) ) {
					$shift = $shifts[0];
					$shift_start = $shift['day'] . ' ' . $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
					// 2.3.3.9: fix to put addition outside bracket
					$time = radio_station_get_time( $shift_start ) + 1;
					if ( 'next' == $adjacent ) {
						$show = radio_station_get_next_show( $time );
					} elseif ( 'previous' == $adjacent ) {
						$show = radio_station_get_previous_show( $time );
					}
				} else {
					// 2.3.3.6: added method for Show with multiple shifts
					$now = radio_station_get_now();
					$show_shifts = radio_station_get_current_schedule();
					if ( !$show_shifts ) {
						return $output;
					}

					// --- get upcoming shift for Show ---
					$next_shift = false;
					foreach ( $show_shifts as $day => $day_shifts ) {
						foreach ( $day_shifts as $day_shift ) {
							if ( !$next_shift && ( $day_shift['show']['id'] == $post->ID ) ) {
								if ( !isset( $last_shift ) ) {
									$last_shift = $day_shift;
								}
								$start = $day_shift['date'] . ' ' . $day_shift['start'];
								$start_time = radio_station_to_time( $start );
								$end = $day_shift['date'] . ' ' . $day_shift['end'];
								$end_time = radio_station_to_time( $end );
								if ( ( $start_time > $now ) || ( $now < $end_time ) ) {
									$next_shift = $day_shift;
								}
							}
						}
					}
					if ( !$next_shift ) {
						$next_shift = $last_shift;
					}
					// echo "Next Show Shift: " . print_r( $next_shift, true );

					// --- reverse order for finding previous show shift ---
					if ( 'previous' == $adjacent ) {
						foreach ( $show_shifts as $day => $day_shifts ) {
							$show_shifts[$day] = array_reverse( $day_shifts, true );
						}
						$show_shifts = array_reverse( $show_shifts, true );
					}

					// --- loop shifts to find adjacent shift's Show ---
					$found = false;
					foreach ( $show_shifts as $day => $day_shifts ) {
						foreach ( $day_shifts as $day_shift ) {
							if ( !isset( $first_shift ) && ( $day_shift['show']['id'] != $post->ID ) ) {
								$first_shift = $day_shift;
							}
							// echo "Shift: " . print_r( $day_shift, true ) . PHP_EOL;
							if ( !isset( $show ) ) {
								if ( $found && ( $day_shift['show']['id'] != $post->ID ) ) {
									$show = $day_shift['show'];
								} elseif ( !$found ) {
									if ( $next_shift == $day_shift ) {
										$found = true;
									}
								}
							}
						}
					}
					if ( !isset( $show ) && isset( $first_shift ) ) {
						$show = $first_shift['show'];
					}
				}
			}
		}

		// --- generate adjacent Show link ---
		if ( isset( $show ) ) {
			if ( 'next' == $adjacent ) {
				$rel = 'next';
			} elseif ( 'previous' == $adjacent ) {
				$rel = 'prev';
			}
			$adjacent_post = get_post( $show['id'] );

			// --- adjacent post title ---
			// 2.4.0.3: added fix for missing post title
			$post_title = $adjacent_post->post_title;
			if ( empty( $adjacent_post->post_title ) ) {
				$post_title = $title;
			}
			$post_title = apply_filters( 'the_title', $post_title, $adjacent_post->ID );

			$date = mysql2date( get_option( 'date_format' ), $adjacent_post->post_date );
			$string = '<a href="' . esc_url( get_permalink( $adjacent_post ) ) . '" rel="' . esc_attr( $rel ) . '" title="' . $title . '">';
			$inlink = str_replace( '%title', $post_title, $link );
			$inlink = str_replace( '%date', $date, $inlink );
			$inlink = $string . $inlink . '</a>';
			$output = str_replace( '%link', $inlink, $format );
		}

		return $output;
	}

	// --- filter to allow related post types ---
	$related_post_types = array( 'post' );
	$show_post_types = apply_filters( 'radio_station_show_related_post_types', $related_post_types );
	if ( in_array( $post->post_type, $related_post_types ) ) {

		// --- filter to allow disabling ---
		$link_show_posts = apply_filters( 'radio_station_link_show_posts', true, $post );
		if ( !$link_show_posts ) {
			return $output;
		}

		// --- get related show ---
		$related_show = get_post_meta( $post->ID, 'post_showblog_id', true );
		if ( !$related_show ) {
			return $output;
		}
		if ( is_array( $related_show ) ) {
			$related_shows = $related_show;
		} else {
			$related_shows = array( $related_show );
		}
		// 2.3.3.6: remove possible saved zero value
		foreach ( $related_shows as $i => $related_show ) {
			if ( 0 == $related_show ) {
				unset( $related_shows[$i] );
			}
		}
		if ( 0 == count( $related_shows ) ) {
			return $output;
		}
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Shows A: ' . print_r( $related_shows, true ) . '</span>';
		}

		// --- get more Shows related to this related Post ---
		// 2.3.3.6: allow for multiple related posts
		// 2.3.3.9: added 'i:' prefix to LIKE value matches
		global $wpdb;
		$query = "SELECT post_id,meta_value FROM " . $wpdb->prefix . "postmeta"
				. " WHERE meta_key = 'post_showblog_id' AND meta_value LIKE '%i:" . $related_shows[0] . "%'";
		if ( count( $related_shows ) > 1 ) {
			foreach ( $related_show as $i => $show_id ) {
				if ( $i > 0 ) {
					$query .= " OR meta_key = 'post_showblog_id' AND meta_value LIKE '%i:" . $show_id . "%'";
				}
			}
		}
		$results = $wpdb->get_results( $query, ARRAY_A );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Shows B: ' . print_r( $results, true ) . '</span>';
		}
		if ( !$results || !is_array( $results ) || ( count( $results ) < 1 ) ) {
			return $output;
		}
		$related_posts = array();
		foreach ( $results as $result ) {
			$values = maybe_unserialize( $result['meta_value'] );
			if ( RADIO_STATION_DEBUG ) {
				echo '<span style="display:none;">Post ' . $result['post_id'] . ' Related Show Values : ' . print_r( $values, true ) . '</span>';
			}
			// --- double check Show ID is actually a match ---
			if ( ( $result['meta_value'] == $related_show ) || ( is_array( $values ) && array_intersect( $related_shows, $values ) ) ) {
				// --- recheck post is of the same post type ---
				$query = "SELECT post_type FROM " . $wpdb->prefix . "posts WHERE ID = %d";
				$query = $wpdb->prepare( $query, $result['post_id'] );
				$related_post_type = $wpdb->get_var( $query );
				if ( $related_post_type == $post->post_type ) {
					$related_posts[] = $result['post_id'];
				}
			}
		}
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Posts B: ' . print_r( $related_posts, true ) . '</span>';
		}
		if ( 0 == count( $related_posts ) ) {
			return $output;
		}

		// --- get adjacent post query ---
		// 2.3.3.6: use post__in related post array instead of meta_query
		$args = array(
			'post_type'			  => $post->post_type,
			'posts_per_page'	  => 1,
			'orderby'             => 'post_modified',
			'post__in'            => $related_posts,
			'ignore_sticky_posts' => true,
		);

		// --- setup for previous or next post ---
		// 2.3.3.6: set date_query instead of meta_query
		$post_type_object = get_post_type_object( $post->post_type );
		if ( 'previous' == $adjacent ) {
			$args['order'] = 'DESC';
			$args['date_query'] = array( array( 'before' => $post->post_date ) );
			$rel = 'prev';
			$title = __( 'Previous Related Show', 'radio-station' ) . ' ' . $post_type_object->labels->singular_name;
		} elseif ( 'next' == $adjacent ) {
			$args['order'] = 'ASC';
			$args['date_query'] = array( array( 'after' => $post->post_date ) );
			$rel = 'next';
			$title = __( 'Next Related Show', 'radio-station' ) . ' ' . $post_type_object->labels->singular_name;
		}

		// --- get the adjacent post ---
		// 2.3.3.6: use date_query instead of looping posts
		$show_posts = get_posts( $args );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Posts Args: ' . print_r( $args, true ) . '</span>';
		}
		if ( 0 == count( $show_posts ) ) {
			return $output;
		}
		$adjacent_post = $show_posts[0];
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Adjacent Post: ' . print_r( $adjacent_post, true ) . '</span>';
		}

		// --- adjacent post title ---
		$post_title = $adjacent_post->post_title;
		if ( empty( $adjacent_post->post_title ) ) {
			$post_title = $title;
		}
		$post_title = apply_filters( 'the_title', $post_title, $adjacent_post->ID );

		// --- adjacent post link ---
		// (from function get_adjacent_post_link)
		$date = mysql2date( get_option( 'date_format' ), $adjacent_post->post_date );
		$string = '<a href="' . esc_url( get_permalink( $adjacent_post ) ) . '" rel="' . esc_attr( $rel ) . '" title="' . esc_attr( $title ) . '">';
		$inlink = str_replace( '%title', $post_title, $link );
		$inlink = str_replace( '%date', $date, $inlink );
		$inlink = $string . $inlink . '</a>';
		$output = str_replace( '%link', $inlink, $format );

	}

	return $output;
}


// =============
// Query Filters
// =============

// -----------------------------
// Playlist Archive Query Filter
// -----------------------------
// 2.3.0: added to replace old archive template meta query
add_filter( 'pre_get_posts', 'radio_station_show_playlist_query' );
function radio_station_show_playlist_query( $query ) {

	if ( RADIO_STATION_PLAYLIST_SLUG == $query->get( 'post_type' ) ) {

		// --- not needed if using legacy template ---
		$styledir = get_stylesheet_directory();
		if ( file_exists( $styledir . '/archive-playlist.php' )
		     || file_exists( $styledir . '/templates/archive-playlist.php' ) ) {
			return;
		}
		// 2.3.0: also check in parent theme directory
		$templatedir = get_template_directory();
		if ( $templatedir != $styledir ) {
			if ( file_exists( $templatedir . '/archive-playlist.php' )
			     || file_exists( $templatedir . '/templates/archive-playlist.php' ) ) {
				return;
			}
		}

		// --- check if show ID or slug is set --
		// TODO: maybe use get_query_var here ?
		if ( isset( $_GET['show_id'] ) ) {
			$show_id = absint( $_GET['show_id'] );
			if ( $show_id < 0 ) {
				unset( $show_id );
			}
		} elseif ( isset( $_GET['show'] ) ) {
			$show = sanitize_title( $_GET['show'] );
			global $wpdb;
			$show_query = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_type = '" . RADIO_STATION_SHOW_SLUG . "' AND post_name = %s";
			$show_query = $wpdb->prepare( $show_query, $show );
			$show_id = $wpdb->get_var( $show_query );
			if ( !$show_id ) {
				unset( $show_id );
			}
		}

		// --- maybe add the playlist meta query ---
		if ( isset( $show_id ) ) {
			$meta_query = array(
				'key'   => 'playlist_show_id',
				'value' => $show_id,
			);
			$query->set( $meta_query );
		}
	}
}


// ------------------
// === User Roles ===
// ------------------

// --------------------------
// Set Roles and Capabilities
// --------------------------
if ( is_multisite() ) {
	add_action( 'init', 'radio_station_set_roles', 10, 0 );
	// 2.3.1: added possible fix for roles not being set on multisite
	add_action( 'admin_init', 'radio_station_set_roles', 10, 0 );
} else {
	add_action( 'admin_init', 'radio_station_set_roles', 10, 0 );
}
function radio_station_set_roles() {

	global $wp_roles;

	// --- set only necessary capabilities for DJs ---
	$caps = array(
		'edit_shows'               => true,
		'edit_published_shows'     => true,
		'edit_others_shows'        => true,
		'read_shows'               => true,
		'edit_playlists'           => true,
		'edit_published_playlists' => true,
		// by default DJs cannot edit others playlists
		// 'edit_others_playlists'    => false,
		'read_playlists'           => true,
		'publish_playlists'        => true,
		'read'                     => true,
		'upload_files'             => true,
		'edit_posts'               => true,
		'edit_published_posts'     => true,
		'publish_posts'            => true,
		'delete_posts'             => true,
	);

	// --- add the DJ role ---
	// 2.3.0: translate DJ role name
	// 2.3.0: change label from 'DJ' to 'DJ / Host'
	// 2.3.0: check/add profile capabilities to hosts
	$wp_roles->add_role( 'dj', __( 'DJ / Host', 'radio-station' ), $caps );
	$role_caps = $wp_roles->roles['dj']['capabilities'];
	// 2.3.1.1: added check if role caps is an array
	if ( !is_array( $role_caps ) ) {
		$role_caps = array();
	}
	$host_caps = array(
		'edit_hosts',
		'edit_published_hosts',
		'delete_hosts',
		'read_hosts',
		'publish_hosts'
	);
	foreach ( $host_caps as $cap ) {
		if ( !array_key_exists( $cap, $role_caps ) || !$role_caps[$cap] ) {
			$wp_roles->add_cap( 'dj', $cap, true );
		}
	}
	// 2.3.3.9: fix for existing DJ role old name
	$wp_roles->roles['dj']['name'] = __( 'DJ / Host', 'radio_station' );
	$wp_roles->role_names['dj'] = __( 'DJ / Host', 'radio_station' );

	// --- add Show Producer role ---
	// 2.3.0: add equivalent capability role for Show Producer
	$wp_roles->add_role( 'producer', __( 'Show Producer', 'radio-station' ), $caps );
	$role_caps = $wp_roles->roles['producer']['capabilities'];
	// 2.3.1.1: added check if role caps is an array
	if ( !is_array( $role_caps ) ) {
		$role_caps = array();
	}
	$producer_caps = array(
		'edit_producers',
		'edit_published_producers',
		'delete_producers',
		'read_producers',
		'publish_producers',
	);
	foreach ( $producer_caps as $cap ) {
		if ( !array_key_exists( $cap, $role_caps ) || !$role_caps[$cap] ) {
			$wp_roles->add_cap( 'producer', $cap, true );
		}
	}

	// --- grant all capabilities to Show Editors ---
	// 2.3.0: set Show Editor role capabilities
	$caps = array(
		'edit_shows'             => true,
		'edit_published_shows'   => true,
		'edit_others_shows'      => true,
		'edit_private_shows'     => true,
		'delete_shows'           => true,
		'delete_published_shows' => true,
		'delete_others_shows'    => true,
		'delete_private_shows'   => true,
		'read_shows'             => true,
		'publish_shows'          => true,

		'edit_playlists'             => true,
		'edit_published_playlists'   => true,
		'edit_others_playlists'      => true,
		'edit_private_playlists'     => true,
		'delete_playlists'           => true,
		'delete_published_playlists' => true,
		'delete_others_playlists'    => true,
		'delete_private_playlists'   => true,
		'read_playlists'             => true,
		'publish_playlists'          => true,

		'edit_overrides'             => true,
		'edit_overrides_playlists'   => true,
		'edit_others_overrides'      => true,
		'edit_private_overrides'     => true,
		'delete_overrides'           => true,
		'delete_published_overrides' => true,
		'delete_others_overrides'    => true,
		'delete_private_overrides'   => true,
		'read_overrides'             => true,
		'publish_overrides'          => true,

		'edit_hosts'           => true,
		'edit_published_hosts' => true,
		'edit_others_hosts'    => true,
		'delete_hosts'         => true,
		'read_hosts'           => true,
		'publish_hosts'        => true,

		'edit_producers'           => true,
		'edit_published_producers' => true,
		'edit_others_producers'    => true,
		'delete_producers'         => true,
		'read_producers'           => true,
		'publish_producers'        => true,

		'read'                 => true,
		'upload_files'         => true,
		'edit_posts'           => true,
		'edit_others_posts'    => true,
		'edit_published_posts' => true,
		'publish_posts'        => true,
		'delete_posts'         => true,
	);

	// --- add the Show Editor role ---
	// 2.3.0: added Show Editor role
	$wp_roles->add_role( 'show-editor', __( 'Show Editor', 'radio-station' ), $caps );

	// --- check plugin setting for authors ---
	if ( radio_station_get_setting( 'add_author_capabilities' ) == 'yes' ) {

		// --- grant show edit capabilities to author users ---
		$author_caps = $wp_roles->roles['author']['capabilities'];
		// 2.3.1.1: added check if role caps is an array
		if ( !is_array( $author_caps ) ) {
			$author_caps = array();
		}
		$extra_caps = array(
			'edit_shows',
			'edit_published_shows',
			'read_shows',
			'publish_shows',

			'edit_playlists',
			'edit_published_playlists',
			'read_playlists',
			'publish_playlists',

			'edit_overrides',
			'edit_published_overrides',
			'read_overrides',
			'publish_overrides',
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
		'edit_shows',
		'edit_published_shows',
		'edit_others_shows',
		'edit_private_shows',
		'delete_shows',
		'delete_published_shows',
		'delete_others_shows',
		'delete_private_shows',
		'read_shows',
		'publish_shows',

		'edit_playlists',
		'edit_published_playlists',
		'edit_others_playlists',
		'edit_private_playlists',
		'delete_playlists',
		'delete_published_playlists',
		'delete_others_playlists',
		'delete_private_playlists',
		'read_playlists',
		'publish_playlists',

		'edit_overrides',
		'edit_published_overrides',
		'edit_others_overrides',
		'edit_private_overrides',
		'delete_overrides',
		'delete_published_overrides',
		'delete_others_overrides',
		'delete_private_overrides',
		'read_overrides',
		'publish_overrides',

		'edit_hosts',
		'edit_published_hosts',
		'edit_others_hosts',
		'delete_hosts',
		'delete_others_hosts',
		'read_hosts',
		'publish_hosts',

		'edit_producers',
		'edit_published_producers',
		'edit_others_producers',
		'delete_producers',
		'delete_others_producers',
		'read_producers',
		'publish_producers',
	);

	// --- check plugin setting for editors ---
	if ( radio_station_get_setting( 'add_editor_capabilities' ) == 'yes' ) {

		// --- grant show edit capabilities to editor users ---
		$editor_caps = $wp_roles->roles['editor']['capabilities'];
		// 2.3.1.1: added check if capabilities is an array
		if ( !is_array( $editor_caps ) ) {
			$editor_caps = array();
		}
		foreach ( $edit_caps as $cap ) {
			if ( !array_key_exists( $cap, $editor_caps ) || ( !$editor_caps[$cap] ) ) {
				$wp_roles->add_cap( 'editor', $cap, true );
			}
		}
	}

	// --- grant all plugin capabilities to admin users ---
	$admin_caps = $wp_roles->roles['administrator']['capabilities'];
	// 2.3.1.1: added check if capabilities is an array
	if ( !is_array( $admin_caps ) ) {
		$admin_caps = array();
	}
	foreach ( $edit_caps as $cap ) {
		if ( !array_key_exists( $cap, $admin_caps ) || ( !$admin_caps[$cap] ) ) {
			$wp_roles->add_cap( 'administrator', $cap, true );
		}
	}

}

// ----------------------------------
// Admin Fix for DJ / Host Role Label
// ----------------------------------
// 2.3.3.9: added for user edit screen crackliness
add_filter( 'editable_roles', 'radio_station_role_check_test', 9 );
function radio_station_role_check_test( $roles ) {
	if ( RADIO_STATION_DEBUG && is_admin() ) {
		echo "DJ Role: " . print_r( $roles['dj'], true );
	}
	$roles['dj']['name'] = __( 'DJ / Host', 'radio-station' );
	return $roles;
}

// ---------------------------------
// maybe Revoke Edit Show Capability
// ---------------------------------
// (revoke ability to edit show if user is not assigned to it)
add_filter( 'user_has_cap', 'radio_station_revoke_show_edit_cap', 10, 4 );
function radio_station_revoke_show_edit_cap( $allcaps, $caps, $args, $user ) {

	global $post, $wp_roles;

	// 2.4.0.4.1: fix for early capability check plugin conflict
	if ( !function_exists( 'radio_station_get_setting' ) ) {
		return $allcaps;
	}

	// --- check if super admin ---
	// 2.3.3.6: get user object from fourth argument instead
	// ? fix to not revoke edit caps from super admin ?
	// (not implemented, as causing a connection reset error!)
	// if ( function_exists( 'is_super_admin' ) && is_super_admin() ) {
	//	return $allcaps;
	// }

	// --- debug passed capability arguments ---
	// TODO: get post object from args instead of global ?
	if ( isset( $_REQUEST['cap-debug'] ) && ( '1' == $_REQUEST['cap-debug'] ) ) {
		echo '<span style="display:none;">Cap Args: ' . print_r( $args, true ) . '</span>';
	}

	// --- check for editor role ---
	// 2.3.3.6: check editor roles first separately
	// 2.4.0.4: only add WordPress editor role if on in settings
	$editor_roles = array( 'administrator', 'show-editor' );
	$editor_role_caps = radio_station_get_setting( 'add_editor_capabilities' );
	if ( 'yes' == $editor_role_caps ) {
		$editor_roles[] = 'editor';
	}
	foreach ( $editor_roles as $role ) {
	 	if ( in_array( $role, $user->roles ) ) {
			return $allcaps;
		}
	}

	// --- get roles with edit shows capability ---
	$edit_show_roles = $edit_others_shows_roles = array();
	if ( isset( $wp_roles->roles ) && is_array( $wp_roles->roles ) ) {
		foreach ( $wp_roles->roles as $name => $role ) {
			// 2.3.0: fix to skip roles with no capabilities assigned
			if ( isset( $role['capabilities'] ) ) {
				foreach ( $role['capabilities'] as $capname => $capstatus ) {
					// 2.3.0: change publish_shows cap check to edit_shows
					if ( ( 'edit_shows' === $capname ) && (bool) $capstatus ) {
						if ( !in_array( $name, $edit_show_roles ) ) {
							$edit_show_roles[] = $name;
						}
					}
					// 2.3.3.6: add check for edit-others_shows capability
					if ( ( 'edit_others_shows' === $capname ) && (bool) $capstatus ) {
						if ( !in_array( $name, $edit_others_shows_roles ) ) {
							$edit_others_shows_roles[] = $name;
						}
					}
				}
			}
		}
	}

	// 2.3.3.6: preserve if user has edit_others_shows capability
	foreach ( $edit_others_shows_roles as $role ) {
		if ( in_array( $role, $user->roles ) ) {
			// 2.4.0.4: do not automatically assume capability match
			// return $allcaps;
			$found = true;
		}
	}

	// 2.2.8: remove strict in_array checking
	$found = false;
	foreach ( $edit_show_roles as $role ) {
		if ( in_array( $role, $user->roles ) ) {
			$found = true;
		}
	}

	// --- maybe revoke edit show capability for post ---
	// 2.3.3.6: fix to incorrect logic for removing edit show capability
	if ( $found ) {

		// --- limit this to published shows ---
		// 2.3.0: added object and property_exists check to be safe
		if ( isset( $post ) && is_object( $post ) && property_exists( $post, 'post_type' ) && isset( $post->post_type ) ) {

			// 2.3.0: removed is_admin check (so works with frontend edit show link)
			// 2.3.0: moved check if show is published inside
			if ( RADIO_STATION_SHOW_SLUG == $post->post_type ) {

				// --- get show hosts and producers ---
				$hosts = get_post_meta( $post->ID, 'show_user_list', true );
				$producers = get_post_meta( $post->ID, 'show_producer_list', true );

				// 2.3.0.4: convert possible (old) non-array values
				if ( !$hosts || empty( $hosts ) ) {
					$hosts = array();
				} elseif ( !is_array( $hosts ) ) {
					$hosts = array( $hosts );
				}
				if ( !$producers || empty( $producers ) ) {
					$producers = array();
				} elseif ( !is_array( $producers ) ) {
					$producers = array( $producers );
				}

				// ---- revoke editing capability if not assigned to this show ---
				// 2.2.8: remove strict in_array checking
				// 2.3.0: also check new Producer role
				if ( !in_array( $user->ID, $hosts ) && !in_array( $user->ID, $producers ) ) {

					// --- remove the edit_shows capability ---
					$allcaps['edit_shows'] = false;
					$allcaps['edit_others_shows'] = false;
					if ( RADIO_STATION_DEBUG ) {
						echo "Removed Edit Show Caps (" . $post->ID . ")";
					}

					// 2.3.0: move check if show is published inside
					if ( 'publish' == $post->post_status ) {
						$allcaps['edit_published_shows'] = false;
					}
				} else {
					// 2.4.0.4: add edit others shows capability
					// (fix for when not original show author)
					$allcaps['edit_shows'] = true;
					$allcaps['edit_others_shows'] = true;
					if ( RADIO_STATION_DEBUG ) {
						echo "Added Edit Show Caps (" . $post->ID . ")";
					}

				}
			}
		}
	}

	return $allcaps;
}


// =================
// --- Debugging ---
// =================

// --------------------------
// maybe Clear Transient Data
// --------------------------
// 2.3.0: clear show transients if debugging
// 2.3.1: added action to init hook
// 2.3.1: check clear show transients option
add_action( 'init', 'radio_station_clear_transients' );
function radio_station_clear_transients() {
	$clear_transients = radio_station_get_setting( 'clear_transients' );
	if ( RADIO_STATION_DEBUG || ( 'yes' == $clear_transients ) ) {
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
		echo '<span class="radio-station-debug" style="display:none;">';
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $data;
		echo '</span>' . PHP_EOL;
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
			echo '<span style="display:none;">Freemius Object: ' . print_r( $instance, true ) . '</span>';
		}
	}
}
