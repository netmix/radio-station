<?php
/**
 * @package Radio Station
 * @version 2.2.8
 */
/*
Plugin Name: Radio Station
Plugin URI: https://netmix.com/radio-station
Description: Adds Show pages, DJ role, playlist and on-air programming functionality to your site.
Author: Tony Zeoli <tonyzeoli@netmix.com>
Version: 2.2.8
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
// - Load Plugin Text Domain
// - Flush Rewrite Rules
// - Enqueue Stylesheets
// === Template Filters ===
// - Single Templates Loader
// - Archive Templates Loader
// === User Roles ===
// - Set DJ Role and Capabilities
// - maybe Revoke Edit Show Capability

// 2.2.7: moved all admin functions to radio-station-admin.php

// -------------
// === Setup ===
// -------------

define( 'RADIO_STATION_DIR', dirname( __FILE__ ) );

// --------------------
// Include Plugin Files
// --------------------
require RADIO_STATION_DIR . '/includes/post-types.php';
require RADIO_STATION_DIR . '/includes/master-schedule.php';
require RADIO_STATION_DIR . '/includes/shortcodes.php';
require RADIO_STATION_DIR . '/includes/support-functions.php';
require RADIO_STATION_DIR . '/includes/class-dj-upcoming-widget.php';
require RADIO_STATION_DIR . '/includes/class-dj-widget.php';
require RADIO_STATION_DIR . '/includes/class-playlist-widget.php';

// 2.2.7: added conditional load of admin includes
if ( is_admin() ) {
	require RADIO_STATION_DIR . '/radio-station-admin.php';
	require RADIO_STATION_DIR . '/includes/post-types-admin.php';
}

// -----------------------
// Load Plugin Text Domain
// -----------------------
function radio_station_init() {
	load_plugin_textdomain( 'radio-station', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'radio_station_init' );

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

// --------------------------
// Enqueue Plugin Stylesheets
// --------------------------
function radio_station_enqueue_styles() {

	$program_css = get_stylesheet_directory() . '/program-schedule.css';
	if ( file_exists( $program_css ) ) {
		$version = filemtime( $program_css );
		$url     = get_stylesheet_directory_uri() . '/program-schedule.css';
	} else {
		$version = filemtime( RADIO_STATION_DIR . '/css/program-schedule.css' );
		$url     = plugins_url( 'css/program-schedule.css', __FILE__ );
	}
	wp_enqueue_style( 'program-schedule', $url, array(), $version );

	// note: widgets.css style enqueueing moved to within widgets
}
add_action( 'wp_enqueue_scripts', 'radio_station_enqueue_styles' );


// ------------------------
// === Template Filters ===
// ------------------------

// -----------------------
// Single Templates Loader
// -----------------------
function radio_station_load_template( $single_template ) {
	global $post;

	if ( 'playlist' === $post->post_type ) {
		// first check to see if there's a template in the active theme's directory
		$user_theme = get_stylesheet_directory() . '/single-playlist.php';
		if ( ! file_exists( $user_theme ) ) {
			$single_template = RADIO_STATION_DIR . '/templates/single-playlist.php';
		}
	}

	if ( 'show' === $post->post_type ) {
		// first check to see if there's a template in the active theme's directory
		$user_theme = get_stylesheet_directory() . '/single-show.php';
		if ( ! file_exists( $user_theme ) ) {
			$single_template = RADIO_STATION_DIR . '/templates/single-show.php';
		}
	}

	return $single_template;
}
add_filter( 'single_template', 'radio_station_load_template' );

// ------------------------
// Archive Templates Loader
// ------------------------
function radio_station_load_custom_post_type_template( $archive_template ) {
	global $post;

	if ( is_post_type_archive( 'playlist' ) ) {
		$playlist_archive_theme = get_stylesheet_directory() . '/archive-playlist.php';
		if ( ! file_exists( $playlist_archive_theme ) ) {
			$archive_template = RADIO_STATION_DIR . '/templates/archive-playlist.php';
		}
	}

	return $archive_template;
}
add_filter( 'archive_template', 'radio_station_load_custom_post_type_template' );

// ----------------------
// DJ Author Template Fix
// ----------------------
// 2.2.8: fix to not 404 author pages for DJs without blog posts
// Ref: https://wordpress.org/plugins/show-authors-without-posts/
function radio_station_fix_djs_without_posts( $template ) {
	global $wp_query;
	if ( ! is_author() && get_query_var( 'author' ) && ( 0 == $wp_query->posts->post ) ) {

		// --- check if author has DJ (or administrator) role ---
		$author = get_query_var( 'author_name' ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
		if ( in_array( 'dj', $author->roles ) || in_array( 'administrator', $author->roles ) ) {
			return get_author_template();
		}

	}
	return $template;
}
add_filter( '404_template', 'radio_station_fix_djs_without_posts' );


// ------------------
// === User Roles ===
// ------------------

// ----------------------------
// Set DJ Role and Capabilities
// ----------------------------
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
		// 'edit_others_playlists'	=> true,  // uncomment to allow DJs to edit all playlists
		'read_playlists'           => true,
		'publish_playlists'        => true,
		'read'                     => true,
		'upload_files'             => true,
		'edit_posts'               => true,
		'edit_published_posts'     => true,
		'publish_posts'            => true,
		'delete_posts'             => true,
	);
	// $wp_roles->remove_role('dj'); // we need this here in case we ever update the capabilities list

	// --- add the role ---
	// TODO: maybe translate role name ?
	$wp_roles->add_role( 'dj', 'DJ', $caps );

	// --- grant all new capabilities to admin users ---
	$wp_roles->add_cap( 'administrator', 'edit_shows', true );
	$wp_roles->add_cap( 'administrator', 'edit_published_shows', true );
	$wp_roles->add_cap( 'administrator', 'edit_others_shows', true );
	$wp_roles->add_cap( 'administrator', 'edit_private_shows', true );
	$wp_roles->add_cap( 'administrator', 'delete_shows', true );
	$wp_roles->add_cap( 'administrator', 'delete_published_shows', true );
	$wp_roles->add_cap( 'administrator', 'delete_others_shows', true );
	$wp_roles->add_cap( 'administrator', 'delete_private_shows', true );
	$wp_roles->add_cap( 'administrator', 'read_shows', true );
	$wp_roles->add_cap( 'administrator', 'publish_shows', true );
	$wp_roles->add_cap( 'administrator', 'edit_playlists', true );
	$wp_roles->add_cap( 'administrator', 'edit_published_playlists', true );
	$wp_roles->add_cap( 'administrator', 'edit_others_playlists', true );
	$wp_roles->add_cap( 'administrator', 'edit_private_playlists', true );
	$wp_roles->add_cap( 'administrator', 'delete_playlists', true );
	$wp_roles->add_cap( 'administrator', 'delete_published_playlists', true );
	$wp_roles->add_cap( 'administrator', 'delete_others_playlists', true );
	$wp_roles->add_cap( 'administrator', 'delete_private_playlists', true );
	$wp_roles->add_cap( 'administrator', 'read_playlists', true );
	$wp_roles->add_cap( 'administrator', 'publish_playlists', true );
}
if ( is_multisite() ) {
	add_action( 'init', 'radio_station_set_roles', 10, 0 );
} else {
	add_action( 'admin_init', 'radio_station_set_roles', 10, 0 );
}

// ---------------------------------
// maybe Revoke Edit Show Capability
// ---------------------------------
// (revoke ability to edit show if user is not assigned as a DJ to it)
function radio_station_revoke_show_edit_cap( $allcaps, $cap = 'edit_shows', $args ) {

	global $post, $wp_roles;

	$user = wp_get_current_user();

	// --- get roles with publish shows capability ---
	$add_roles = array( 'administrator' );
	if ( isset( $wp_roles->roles ) && is_array( $wp_roles->roles ) ) {
		foreach ( $wp_roles->roles as $name => $role ) {
			foreach ( $role['capabilities'] as $capname => $capstatus ) {
				if ( 'publish_shows' === $capname && (bool) $capstatus ) {
					$add_roles[] = $name;
				}
			}
		}
	}

	// --- check if current user has any of these roles ---
	$found = false;
	foreach ( $add_roles as $role ) {
		// 2.2.8: remove strict in_array checking
		if ( in_array( $role, $user->roles ) ) {
			$found = true;
		}
	}

	if ( ! $found ) {

		// --- limit this to published shows ---
		if ( isset( $post->post_type ) ) {
			if ( is_admin() && ( 'show' === $post->post_type ) && ( 'publish' === $post->post_status ) ) {

				$djs = get_post_meta( $post->ID, 'show_user_list', true );

				if ( ! isset( $djs ) || empty( $djs ) ) {
					$djs = array();
				}

				// ---- revoke editing capability if not assigned to this show ---
				// 2.2.8: remove strict in_array checking
				if ( ! in_array( $user->ID, $djs ) ) {
					$allcaps['edit_shows']           = false;
					$allcaps['edit_published_shows'] = false;
				}
			}
		}
	}
	return $allcaps;
}
add_filter( 'user_has_cap', 'radio_station_revoke_show_edit_cap', 10, 3 );

