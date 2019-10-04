<?php
/*
 * Define all Post Types and Genre Taxonomy
 * Author: Nikki Blight
 * Since: 2.0.0
 */


// === Post Types ===
// - Register Post Types
// - Set CPTs to Classic Editor
// - Add Show Thumbnail Support
// === Taxonomies ===
// - Add Genre Taxonomy


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
	// $icon = plugins_url( 'images/show-menu-icon.png', dirname(dirname(__FILE__)).'/radio-station.php' );
	register_post_type(
		'show',
		array(
			'labels'          => array(
				'name'          => __( 'Shows', 'radio-station' ),
				'singular_name' => __( 'Show', 'radio-station' ),
				'add_new'       => __( 'Add Show', 'radio-station' ),
				'add_new_item'  => __( 'Add Show', 'radio-station' ),
				'edit_item'     => __( 'Edit Show', 'radio-station' ),
				'new_item'      => __( 'New Show', 'radio-station' ),
				'view_item'     => __( 'View Show', 'radio-station' ),
			),
			'show_ui'         => true,
			'show_in_menu'    => false, // now added to main menu
			'description'     => __( 'Post type for Show descriptions', 'radio-station' ),
			// 'menu_position'	=> 5,
			// 'menu_icon'		=> $icon,
			'public'          => true,
			'taxonomies'      => array( 'genres' ),
			'hierarchical'    => false,
			'supports'        => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'can_export'      => true,
			'capability_type' => 'show',
			'map_meta_cap'    => true,
		)
	);

	// --------
	// Playlist
	// --------
	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/playlist-menu-icon.png';
	// $icon = plugins_url( 'images/playlist-menu-icon.png', dirname(dirname(__FILE__)).'/radio-station.php' );
	register_post_type(
		'playlist',
		array(
			'labels'          => array(
				'name'          => __( 'Playlists', 'radio-station' ),
				'singular_name' => __( 'Playlist', 'radio-station' ),
				'add_new'       => __( 'Add Playlist', 'radio-station' ),
				'add_new_item'  => __( 'Add Playlist', 'radio-station' ),
				'edit_item'     => __( 'Edit Playlist', 'radio-station' ),
				'new_item'      => __( 'New Playlist', 'radio-station' ),
				'view_item'     => __( 'View Playlist', 'radio-station' ),
			),
			'show_ui'         => true,
			'show_in_menu'    => false, // now added to main menu
			'description'     => __( 'Post type for Playlist descriptions', 'radio-station' ),
			// 'menu_position'	=> 5,
			// 'menu_icon'		=> $icon,
			'public'          => true,
			'hierarchical'    => false,
			'supports'        => array( 'title', 'editor', 'comments' ),
			'can_export'      => true,
			'has_archive'     => 'playlists-archive',
			'rewrite'         => array( 'slug' => 'playlists' ),
			'capability_type' => 'playlist',
			'map_meta_cap'    => true,
		)
	);

	// -----------------
	// Schedule Override
	// -----------------
	// $icon = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'images/show-menu-icon.png';
	// $icon = plugins_url( 'images/show-menu-icon.png', dirname(dirname(__FILE__)).'/radio-station.php' );
	register_post_type(
		'override',
		array(
			'labels'          => array(
				'name'          => __( 'Schedule Override', 'radio-station' ),
				'singular_name' => __( 'Schedule Override', 'radio-station' ),
				'add_new'       => __( 'Add Schedule Override', 'radio-station' ),
				'add_new_item'  => __( 'Add Schedule Override', 'radio-station' ),
				'edit_item'     => __( 'Edit Schedule Override', 'radio-station' ),
				'new_item'      => __( 'New Schedule Override', 'radio-station' ),
				'view_item'     => __( 'View Schedule Override', 'radio-station' ),
			),
			'show_ui'         => true,
			'show_in_menu'    => false, // now added to main menu
			'description'     => __( 'Post type for Schedule Override', 'radio-station' ),
			// 'menu_position'	=> 5,
			// 'menu_icon'		=> $icon,
			'public'          => true,
			'hierarchical'    => false,
			'supports'        => array( 'title', 'thumbnail' ),
			'can_export'      => true,
			'rewrite'         => array( 'slug' => 'show-override' ),
			'capability_type' => 'show',
			'map_meta_cap'    => true,
		)
	);

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
	$post_types = array( 'show', 'playlist', 'override' );
	// 2.2.8: remove strict in_array checking
	if ( in_array( $post_type, $post_types ) ) {
		return false;
	}
	return $can_edit;
}

// --------------------------
// Add Show Thumbnail Support
// --------------------------
// --- add featured image support to "show" post type ---
// (this is probably no longer necessary as declared in register_post_type for show)
add_action( 'init', 'radio_station_add_featured_image_support' );
function radio_station_add_featured_image_support() {
	$supported_types = get_theme_support( 'post-thumbnails' );

	if ( false === $supported_types ) {
		add_theme_support( 'post-thumbnails', array( 'show' ) );
	} elseif ( is_array( $supported_types ) ) {
		$supported_types[0][] = 'show';
		add_theme_support( 'post-thumbnails', $supported_types[0] );
	}
}

// ------------------
// === Taxonomies ===
// ------------------

// -----------------------
// Register Genre Taxonomy
// -----------------------
// --- create custom taxonomy for the Show post type ---
add_action( 'init', 'radio_station_myplaylist_create_show_taxonomy' );
function radio_station_myplaylist_create_show_taxonomy() {

	// --- add taxonomy labels ---
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
	register_taxonomy(
		'genres',
		array( 'show' ),
		array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'public'             => true,
			'show_tagcloud'      => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'genre' ),
			'show_admin_column'  => true,
			'show_in_quick_edit' => true,
			'capabilities'       => array(
				'manage_terms' => 'edit_shows',
				'edit_terms'   => 'edit_shows',
				'delete_terms' => 'edit_shows',
				'assign_terms' => 'edit_shows',
			),
		)
	);

}

