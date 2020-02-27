<?php
/*
 * Define all Post Types and Taxonomies
 * Author: Nikki Blight
 * Since: 2.0.0
 */

// === Post Types ===
// - Register Post Types
// -- Show
// -- Playlist
// -- Override
// * DJ / Host
// * Producer
// - Set CPTs to Classic Editor
// - Add Theme Thumbnail Support
// - Add Admin Bar Add New Links
// - Add Admin Bar View/Edit Link
// === Taxonomies ===
// - Register Show Taxonomies
// -- Genre Taxonomy
// -- Language Taxonomy


// ------------------
// === Post Types ===
// ------------------

// -------------------
// Register Post Types
// -------------------
// --- create post types for playlists and shows ---
add_action( 'init', 'radio_station_create_post_types' );
function radio_station_create_post_types() {

	// ----
	// Show
	// ----
	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/show-menu-icon.png';
	// $icon = plugins_url( 'images/show-menu-icon.png', RADIO_STATION_FILE );
	$post_type = array(
		'labels'            => array(
			'name'          => __( 'Shows', 'radio-station' ),
			'singular_name' => __( 'Show', 'radio-station' ),
			'add_new'       => __( 'Add Show', 'radio-station' ),
			'add_new_item'  => __( 'Add Show', 'radio-station' ),
			'edit_item'     => __( 'Edit Show', 'radio-station' ),
			'new_item'      => __( 'New Show', 'radio-station' ),
			'view_item'     => __( 'View Show', 'radio-station' ),
			// 2.3.0: added archive title label
			'archive_title' => __( 'Shows', 'radio-station' ),
		),
		'show_ui'           => true,
		'show_in_menu'      => false, // now added to main menu
		'show_in_admin_bar' => false, // this is done manually
		'description'       => __( 'Post type for Show descriptions', 'radio-station' ),
		'public'            => true,
		'taxonomies'        => array( RADIO_STATION_GENRES_SLUG, RADIO_STATION_LANGUAGES_SLUG ),
		'hierarchical'      => false,
		// 2.3.0: added custom field and revision support
		'supports'          => array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions' ),
		'can_export'        => true,
		// 2.3.0: added show archives support
		'has_archive'       => 'shows',
		'rewrite'           => array(
			'slug'       => 'show',
			'with_front' => false,
			'feeds'      => true,
		),
		'capability_type'   => 'show',
		'map_meta_cap'      => true,
	);
	// 2.3.0: add filter for show post type array
	$post_type = apply_filters( 'radio_station_post_type_show', $post_type );
	register_post_type( RADIO_STATION_SHOW_SLUG, $post_type );

	// --------
	// Playlist
	// --------
	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/playlist-menu-icon.png';
	// $icon = plugins_url( 'images/playlist-menu-icon.png', RADIO_STATION_FILE );
	$post_type = array(
		'labels'            => array(
			'name'          => __( 'Playlists', 'radio-station' ),
			'singular_name' => __( 'Playlist', 'radio-station' ),
			'add_new'       => __( 'Add Playlist', 'radio-station' ),
			'add_new_item'  => __( 'Add Playlist', 'radio-station' ),
			'edit_item'     => __( 'Edit Playlist', 'radio-station' ),
			'new_item'      => __( 'New Playlist', 'radio-station' ),
			'view_item'     => __( 'View Playlist', 'radio-station' ),
			// 2.3.0: added archive title label
			'archive_title' => __( 'Playlists', 'radio-station' ),
		),
		'show_ui'           => true,
		'show_in_menu'      => false, // now added to main menu
		'show_in_admin_bar' => false, // this is done manually
		'description'       => __( 'Post type for Playlist descriptions', 'radio-station' ),
		'public'            => true,
		'hierarchical'      => false,
		// 2.3.0: added custom field and revision support
		'supports'          => array( 'title', 'editor', 'comments', 'custom-fields', 'revisions' ),
		'can_export'        => true,
		// 2.3.0: changed from playlists-archive
		'has_archive'       => 'playlists',
		'rewrite'           => array(
			'slug'       => 'playlist',
			'with_front' => true,
			'feeds'      => false,
		),
		'capability_type'   => 'playlist',
		'map_meta_cap'      => true,
	);
	// 2.3.0: add filter for playlist post type array
	$post_type = apply_filters( 'radio_station_post_type_playlist', $post_type );
	register_post_type( RADIO_STATION_PLAYLIST_SLUG, $post_type );

	// -----------------
	// Schedule Override
	// -----------------
	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/show-menu-icon.png';
	// $icon = plugins_url( 'images/show-menu-icon.png', RADIO_STATION_FILE );
	$post_type = array(
		'labels'            => array(
			'name'          => __( 'Schedule Overrides', 'radio-station' ),
			'singular_name' => __( 'Schedule Override', 'radio-station' ),
			'add_new'       => __( 'Add Schedule Override', 'radio-station' ),
			'add_new_item'  => __( 'Add Schedule Override', 'radio-station' ),
			'edit_item'     => __( 'Edit Schedule Override', 'radio-station' ),
			'new_item'      => __( 'New Schedule Override', 'radio-station' ),
			'view_item'     => __( 'View Schedule Override', 'radio-station' ),
		),
		'show_ui'           => true,
		'show_in_menu'      => false, // now added to main menu
		'show_in_admin_bar' => false, // this is done manually
		'description'       => __( 'Post type for Schedule Override', 'radio-station' ),
		'public'            => true,
		// 2.3.0: added taxonomies to overrides
		'taxonomies'        => array( RADIO_STATION_GENRES_SLUG, RADIO_STATION_LANGUAGES_SLUG ),
		'hierarchical'      => false,
		// 2.3.0: added editor support for override description
		// 2.3.0: added custom field and revision support
		'supports'          => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' ),
		'can_export'        => true,
		'has_archive'       => false,
		'rewrite'           => array(
			'slug'       => 'override',
			'with_front' => false,
			'feeds'      => false,
		),
		'capability_type'   => 'override',
		'map_meta_cap'      => true,
	);
	// 2.3.0: add filter for override post type array
	$post_type = apply_filters( 'radio_station_post_type_override', $post_type );
	register_post_type( RADIO_STATION_OVERRIDE_SLUG, $post_type );

	// ---------
	// DJ / Host
	// ---------
	// 2.3.0: added (dummy) post type for DJ / Host profiles
	// (so that rewrite rules and query vars are added for it)
	$ui = apply_filters( 'radio_station_host_interface', false );
	$post_type = array(
		'labels'              => array(
			'name'          => __( 'Host Profiles', 'radio-station' ),
			'singular_name' => __( 'Host Profile', 'radio-station' ),
			'add_new'       => __( 'New Host Profile', 'radio-station' ),
			'add_new_item'  => __( 'Add Host Profile', 'radio-station' ),
			'edit_item'     => __( 'Edit Host Profile', 'radio-station' ),
			'new_item'      => __( 'New Host Profile', 'radio-station' ),
			'view_item'     => __( 'View Host Profile', 'radio-station' ),
			'archive_title' => __( 'Show Hosts', 'radio-station' ),
		),
		'show_ui'             => $ui,
		'show_in_menu'        => false,
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'description'         => __( 'Post type for DJ / Host Profiles', 'radio-station' ),
		'exclude_from_search' => false,
		'public'              => true,
		'hierarchical'        => false,
		'can_export'          => false,
		'has_archive'         => 'hosts',
		'rewrite'             => array(
			'slug'       => 'host',
			'with_front' => true,
			'feeds'      => false,
		),
		'query_var'           => true,
		'capability_type'     => 'host',
		'map_meta_cap'        => false,
	);
	$post_type = apply_filters( 'radio_station_post_type_host', $post_type );
	// TODO: change Host post type slug to rs-host
	register_post_type( RADIO_STATION_HOST_SLUG, $post_type );

	// --------
	// Producer
	// --------
	// 2.3.0: added (dummy) post type for Producer profiles
	// (so that rewrite rules and query vars are added for it)
	$ui = apply_filters( 'radio_station_producer_interface', false );
	$post_type = array(
		'labels'              => array(
			'name'          => __( 'Producer Profiles', 'radio-station' ),
			'singular_name' => __( 'Producer Profile', 'radio-station' ),
			'add_new'       => __( 'New Producer Profile', 'radio-station' ),
			'add_new_item'  => __( 'Add Producer Profile', 'radio-station' ),
			'edit_item'     => __( 'Edit Producer Profile', 'radio-station' ),
			'new_item'      => __( 'New Producer Profile', 'radio-station' ),
			'view_item'     => __( 'View Producer Profile', 'radio-station' ),
			'archive_title' => __( 'Show Producers Profile', 'Hosts' ),
		),
		'show_ui'             => $ui,
		'show_in_menu'        => false,
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'description'         => __( 'Post type for Producer Profiles', 'radio-station' ),
		'exclude_from_search' => false,
		'public'              => true,
		'hierarchical'        => false,
		'can_export'          => false,
		'has_archive'         => 'producers',
		'rewrite'             => array(
			'slug'       => 'producer',
			'with_front' => true,
			'feeds'      => false,
		),
		'query_var'           => true,
		'capability_type'     => 'producer',
		'map_meta_cap'        => false,
	);
	$post_type = apply_filters( 'radio_station_post_type_producer', $post_type );
	// TODO: change Producer post type slug to rs-producer
	register_post_type( RADIO_STATION_PRODUCER_SLUG, $post_type );

	// --- maybe trigger flush of rewrite rules ---
	if ( get_option( 'radio_station_flush_rewrite_rules' ) ) {
		add_action( 'init', 'flush_rewrite_rules', 20 );
		delete_option( 'radio_station_flush_rewrite_rules' );
	}
}

// ---------------------------------------
// Set Post Type Editing to Classic Editor
// ---------------------------------------
// 2.2.2: added so metabox displays can continue to use wide widths
add_filter( 'gutenberg_can_edit_post_type', 'radio_station_post_type_editor', 20, 2 );
add_filter( 'use_block_editor_for_post_type', 'radio_station_post_type_editor', 20, 2 );
function radio_station_post_type_editor( $can_edit, $post_type ) {
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_PLAYLIST_SLUG, RADIO_STATION_OVERRIDE_SLUG );
	// 2.2.8: removed strict in_array checking
	if ( in_array( $post_type, $post_types ) ) {
		return false;
	}

	return $can_edit;
}

// ---------------------------
// Add Theme Thumbnail Support
// ---------------------------
// --- declare featured image support for theme ---
// (probably no longer necessary as declared in register_post_type(s))
add_action( 'init', 'radio_station_add_featured_image_support' );
function radio_station_add_featured_image_support() {

	// 2.3.0: add override thumbnail to theme support declaration
	$supported_types = get_theme_support( 'post-thumbnails' );
	if ( false === $supported_types ) {
		$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG );
		add_theme_support( 'post-thumbnails', $post_types );
	} elseif ( is_array( $supported_types ) ) {
		$supported_types[0][] = RADIO_STATION_SHOW_SLUG;
		$supported_types[0][] = RADIO_STATION_OVERRIDE_SLUG;
		add_theme_support( 'post-thumbnails', $supported_types[0] );
	}
}

// ---------------------------
// Add Admin Bar Add New Links
// ---------------------------
// 2.2.2: re-add new post type items to admin bar
// (as no longer automatically added by register_post_type)
// 2.3.0: fix to function prefix (was station_radio_)
// 2.3.0: change priority to be after main new content iteme
add_action( 'admin_bar_menu', 'radio_station_modify_admin_bar_menu', 71 );
function radio_station_modify_admin_bar_menu( $wp_admin_bar ) {

	// 2.3.0: loop post types to add post type items
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_PLAYLIST_SLUG, RADIO_STATION_OVERRIDE_SLUG );
	foreach ( $post_types as $post_type ) {
		if ( current_user_can( 'publish_' . $post_type . 's' ) ) {
			$post_type_object = get_post_type_object( $post_type );
			$args = array(
				'id'     => 'new-' . $post_type,
				'title'  => $post_type_object->labels->singular_name,
				'parent' => 'new-content',
				'href'   => admin_url( 'post-new.php?post_type=' . $post_type ),
			);
			$wp_admin_bar->add_node( $args );
		}
	}
}

// ----------------------------
// Add Admin Bar View/Edit Link
// ----------------------------
// 2.3.0: added (frontend) edit link to admin bar
// 2.3.0: changed priority to match bat edit link position
// 2.3.0: include view post type link for when editing
add_action( 'admin_bar_menu', 'radio_station_admin_bar_view_edit_links', 81 );
function radio_station_admin_bar_view_edit_links( $wp_admin_bar ) {

	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_PLAYLIST_SLUG, RADIO_STATION_OVERRIDE_SLUG );

	// --- loop to check for plugin post types ---
	if ( ! is_admin() && is_singular() ) {
		foreach ( $post_types as $post_type ) {
			if ( is_singular( $post_type ) ) {
				// --- add post type edit link ---
				if ( current_user_can( 'edit_' . $post_type . 's' ) ) {
					$post_type_object = get_post_type_object( $post_type );
					$post_id = get_the_ID();
					$args = array(
						'id'    => 'edit',
						'title' => __( 'Edit', 'radio-station' ) . ' ' . $post_type_object->labels->singular_name,
						'href'  => admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
					);
					$wp_admin_bar->add_node( $args );
				}
			}
		}
	}

	// --- check edit post match for view link ---
	// 2.3.0: add view links for admin
	global $pagenow;
	if ( is_admin() && ( 'post.php' == $pagenow ) ) {
		global $post;
		foreach ( $post_types as $post_type ) {
			if ( $post->post_type == $post_type ) {
				$post_type_object = get_post_type_object( $post_type );
				if ( 'draft' == $post->post_status ) {
					// --- add post type preview link ---
					$preview_link = get_preview_post_link( $post );
					$args = array(
						'id' => 'preview',
						'title' => $post_type_object->labels->view_item,
						'href' => esc_url( $preview_link ),
						'meta' => array( 'target' => 'wp-preview-' . $post->ID ),
					);
				} else {
					// --- add post type view link ---
					$args = array(
						'id'    => 'view',
						'title' => $post_type_object->labels->view_item,
						'href'  => get_permalink( $post->ID ),
					);
				}
				$wp_admin_bar->add_node( $args );
			}
		}
	}

}


// ------------------
// === Taxonomies ===
// ------------------

// ------------------------
// Register Show Taxonomies
// ------------------------
add_action( 'init', 'radio_station_register_show_taxonomies' );
function radio_station_register_show_taxonomies() {

	// --------------
	// Genre Taxonomy
	// --------------

	// --- Genre taxonomy labels ---
	$labels = array(
		'name'              => _x( 'Genres', 'taxonomy general name', 'radio-station' ),
		'singular_name'     => _x( 'Genre', 'taxonomy singular name', 'radio-station' ),
		'search_items'      => __( 'Search Genres', 'radio-station' ),
		'all_items'         => __( 'All Genres', 'radio-station' ),
		'parent_item'       => __( 'Parent Genre', 'radio-station' ),
		'parent_item_colon' => __( 'Parent Genre:', 'radio-station' ),
		'edit_item'         => __( 'Edit Genre', 'radio-station' ),
		'update_item'       => __( 'Update Genre', 'radio-station' ),
		'add_new_item'      => __( 'Add New Genre', 'radio-station' ),
		'new_item_name'     => __( 'New Genre Name', 'radio-station' ),
		'menu_name'         => __( 'Genre', 'radio-station' ),
	);

	// --- register the genre taxonomy ---
	// 2.2.3: added show_admin_column and show_in_quick_edit arguments
	// 2.3.0: added show_in_rest argument
	$args = array(
		'hierarchical'       => true,
		'labels'             => $labels,
		'public'             => true,
		'show_tagcloud'      => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'genre' ),
		'show_ui'            => true,
		'show_in_menu'       => false,
		'show_in_rest'       => true,
		'show_admin_column'  => true,
		'show_in_quick_edit' => true,
		'capabilities'       => array(
			'manage_terms' => 'edit_shows',
			'edit_terms'   => 'edit_shows',
			'delete_terms' => 'edit_shows',
			'assign_terms' => 'edit_shows',
		),
	);
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG );
	// 2.3.0: added filter for genre taxonomy arguments
	$args = apply_filters( 'radio_station_genre_taxonomy_args', $args );
	register_taxonomy( RADIO_STATION_GENRES_SLUG, $post_types, $args );

	// -----------------
	// Language Taxonomy
	// -----------------

	// --- Language taxonomy labels ---
	$labels = array(
		'name'              => _x( 'Languages', 'taxonomy general name', 'radio-station' ),
		'singular_name'     => _x( 'Language', 'taxonomy singular name', 'radio-station' ),
		'search_items'      => __( 'Search Languages', 'radio-station' ),
		'all_items'         => __( 'All Languages', 'radio-station' ),
		'parent_item'       => __( 'Parent Language', 'radio-station' ),
		'parent_item_colon' => __( 'Parent Language:', 'radio-station' ),
		'edit_item'         => __( 'Edit Language', 'radio-station' ),
		'update_item'       => __( 'Update Language', 'radio-station' ),
		'add_new_item'      => __( 'Add New Language', 'radio-station' ),
		'new_item_name'     => __( 'New Language Name', 'radio-station' ),
		'menu_name'         => __( 'Language', 'radio-station' ),
	);

	// --- register the language taxonomy ---
	$args = array(
		'hierarchical'       => false,
		'labels'             => $labels,
		'public'             => true,
		'show_tagcloud'      => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'language' ),
		'show_ui'            => true,
		'show_in_menu'       => false,
		'show_in_rest'       => true,
		'show_admin_column'  => true,
		'show_in_quick_edit' => true,
		'capabilities'       => array(
			'manage_terms' => 'edit_shows',
			'edit_terms'   => 'edit_shows',
			'delete_terms' => 'edit_shows',
			'assign_terms' => 'edit_shows',
		),
	);
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG );
	$args = apply_filters( 'radio_station_language_taxonomy_args', $args );
	register_taxonomy( RADIO_STATION_LANGUAGES_SLUG, $post_types, $args );

}

