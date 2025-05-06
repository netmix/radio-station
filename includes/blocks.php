<?php

// =====================
// === Radio Station ===
// ------- Blocks ------
// =====================
// @since 2.5.0

if ( !defined( 'ABSPATH' ) ) exit;

// === Block Editor Support ===
// - Register Block Category
// - Get Block Callbacks
// - Get Block Attributes
// - Register Blocks
// - Enqueue Block Editor Assets
// - Enqueue Frontend Block Styles


// ----------------------------
// === Block Editor Support ===
// ----------------------------

// -----------------------
// Register Block Category
// -----------------------
add_action( 'plugins_loaded', 'radio_station_register_block_categories' );
function radio_station_register_block_categories() {
	global $wp_version;
	if ( version_compare( $wp_version, '5.8', '>=' ) ) {
		add_filter( 'block_categories_all', 'radio_station_register_block_category' );
	} else {
		add_filter( 'block_categories', 'radio_station_register_block_category' );
	}
}
function radio_station_register_block_category( $categories ) {
	$new_categories = array();
	foreach ( $categories as $category ) {
		if ( 'embed' == $category['slug'] ) {
			$new_categories[] = array(
				'slug'  => 'radio-station',
				'title' => __( 'Radio Station', 'radio-station' ),
			);
		}
		$new_categories[] = $category;
	}
	return $new_categories;
}

// -------------------
// Get Block Callbacks
// -------------------
function radio_station_get_block_callbacks() {

	// --- set block names and related callbacks ---
	$callbacks = array(
		// --- Radio Clock ---
		// 'timezone'         => 'radio_station_timezone_shortcode',
		'clock'            => 'radio_station_clock_shortcode',
		// --- Stream Player ---
		'player'           => 'radio_player_shortcode',
		// --- Master Schedule ---
		'schedule'         => 'radio_station_master_schedule',
		// --- Archive Lists ---
		'archive'          => 'radio_station_archive_list',
		// --- Shows ---
		'current-show'     => 'radio_station_current_show_shortcode',
		'upcoming-shows'   => 'radio_station_upcoming_shows_shortcode',
		'current-playlist' => 'radio_station_current_playlist_shortcode',
		// --- Show Related ---
		// 'show_posts'       => 'radio_station_show_posts_archive',
		// 'show_playlists'   => 'radio_station_show_playlists_archive',
	);

	// --- filter and return ---
	$callbacks = apply_filters( 'radio_station_block_callbacks', $callbacks );
	return $callbacks;
}

// --------------------
// Get Block Attributes
// --------------------
function radio_station_get_block_attributes() {

	// --- set block names and related attributes ---
	$attributes = array(

		// === Master Schedule ===
		'schedule' => array(
			// --- Schedule Display ---
			'view' => array( 'type' => 'string', 'default' => 'table' ),
			'image_position' => array( 'type' => 'string', 'default' => 'left' ),
			'hide_past_shows' => array( 'type' => 'boolean', 'default' => false ),
			// --- Header Displays ---
			'time_header' => array( 'type' => 'string', 'default' => 'clock' ),
			'clock' => array( 'type' => 'boolean', 'default' => true ),
			'timezone' => array( 'type' => 'boolean', 'default' => true ),
			'selector' => array( 'type' => 'boolean', 'default' => true ),
			// --- Time Display ---
			'display_day' => array( 'type' => 'string', 'default' => 'short' ),
			'display_month' => array( 'type' => 'string', 'default' => 'short' ),
			'start_day' => array( 'type' => 'string', 'default' => '' ),
			'time_format' => array( 'type' => 'string', 'default' => '' ),
			// --- Show Display ---
			'show_times' => array( 'type' => 'boolean', 'default' => true ),
			'show_link' => array( 'type' => 'boolean', 'default' => true ),
			'show_image' => array( 'type' => 'boolean', 'default' => false ),
			'show_desc' => array( 'type' => 'boolean', 'default' => false ),
			'show_hosts' => array( 'type' => 'boolean', 'default' => false ),
			'link_hosts' => array( 'type' => 'boolean', 'default' => false ),
			'show_genres' => array( 'type' => 'boolean', 'default' => false ),
			'show_encore' => array( 'type' => 'boolean', 'default' => true ),

			/* 'views' => array( 'type' => 'array', 'default' => array( 'table', 'tabs', 'grid', 'calendar' ) ),
			'gridwidth' => array( 'type' => 'number', 'default' => 150 ),
			'time_spaced' => array( 'type' => 'boolean', 'default' => true ),
			'weeks' => array( 'type' => 'number', 'default' => 3 ),
			'previous_weeks' => array( 'type' => 'number', 'default' => 1 ), */
		),

		// === Stream Player ===
		'player' => array(
			// --- Player Content ---
			'url' => array( 'type' => 'string', 'default' => '' ),
			'title' => array( 'type' => 'string', 'default' => '' ),
			'image' => array( 'type' => 'string', 'default' => 'default' ),
			// ---- Player Options ---
			'script' => array( 'type' => 'string', 'default' => 'default' ),
			'volume' => array( 'type' => 'number', 'default' => 77 ),
			'volumes' => array( 'type' => 'array', 'default' => array( 'slider' ) ),
			'default' => array( 'type' => 'boolean', 'default' => false ),
			// --- Player Styles ---
			'layout' => array( 'type' => 'string', 'default' => 'horizontal' ),
			'theme' => array( 'type' => 'string', 'default' => 'default' ),
			'buttons' => array( 'type' => 'string', 'default' => 'default' ),
		),

		// === Radio Clock ===
		'timezone' => array(),
		'clock' => array(
			'time_format' => array( 'type' => 'string', 'default' => '' ),
			'day' => array( 'type' => 'string', 'default' => 'full' ),
			'date' => array( 'type' => 'boolean', 'default' => true ),
			'month' => array( 'type' => 'string', 'default' => 'full' ),
			'zone' => array( 'type' => 'boolean', 'default' => true ),
			'seconds' => array( 'type' => 'boolean', 'default' => true ),
		),

		// === Archive Lists ===
		'archive' => array(
			// --- Archive List Details ---
			'archive_type' => array( 'type' => 'string', 'default' => 'shows' ),
			'view' => array( 'type' => 'string', 'default' => 'list' ),
			'perpage' => array( 'type' => 'number', 'default' => 10 ),
			'pagination' => array( 'type' => 'boolean', 'default' => true ),
			'hide_empty' => array( 'type' => 'boolean', 'default' => false ),
			// --- Archive Record Query ---
			'orderby' => array( 'type' => 'string', 'default' => 'title' ),
			'order' => array( 'type' => 'string', 'default' => 'ASC' ),
			'status' => array( 'type' => 'string', 'default' => 'publish' ),
			'genre' => array( 'type' => 'string', 'default' => '' ),
			'language' => array( 'type' => 'string', 'default' => '' ),
			// --- Archive Record Display ---
			'description' => array( 'type' => 'string', 'default' => 'excerpt' ),
			'time_format' => array( 'type' => 'string', 'default' => '' ),
			'show_avatars' => array( 'type' => 'boolean', 'default' => true ), /* shows and overrides only */
			'with_shifts' => array( 'type' => 'boolean', 'default' => true ), /* shows only */
			'show_dates' => array( 'type' => 'boolean', 'default' => true ), /* overrides only */
		),

		// === Shows Shortcodes ===
		// --- Current Show ---
		'current-show' => array(
			// --- Loading Options ---
			'ajax' => array( 'type' => 'string', 'default' => '' ),
			// dynamic' => array( 'type' => 'boolean', 'default' => true ),
			'no_shows' => array( 'type' => 'string', 'default' => '' ),
			'hide_empty' => array( 'type' => 'boolean', 'default' => false ),
			// --- Show Display Options ---
			'show_link' => array( 'type' => 'boolean', 'default' => true ),
			'title_position' => array( 'type' => 'string', 'default' => 'right' ),
			'show_avatar' => array( 'type' => 'boolean', 'default' => true ),
			'avatar_size' => array( 'type' => 'string', 'default' => 'thumbnail' ),
			'avatar_width' => array( 'type' => 'number', 'default' => 0 ),
			// --- Show Time Display Options ---
			'show_sched' => array( 'type' => 'boolean', 'default' => true ),
			'show_all_sched' => array( 'type' => 'boolean', 'default' => false ),
			'countdown' => array( 'type' => 'boolean', 'default' => true ),
			'time_format' => array( 'type' => 'string', 'default' => '' ),
			// --- Extra Display Options ---
			'display_hosts' => array( 'type' => 'boolean', 'default' => false ),
			'link_hosts' => array( 'type' => 'boolean', 'default' => true ),
			// 'display_producers' => array( 'type' => 'boolean', 'default' => false ),
			// 'link_producers' => array( 'type' => 'boolean', 'default' => false ),
			'show_desc' => array( 'type' => 'boolean', 'default' => false ),
			'show_playlist' => array( 'type' => 'boolean', 'default' => false ),
			'show_encore' => array( 'type' => 'boolean', 'default' => true ),
		),
		// --- Upcoming Shows ---
		'upcoming-shows' => array(
			// --- Loading Options ---
			'ajax' => array( 'type' => 'string', 'default' => '' ),
			'limit' => array( 'type' => 'number', 'default' => 1 ),
			'dynamic' => array( 'type' => 'boolean', 'default' => true ),
			'no_shows' => array( 'type' => 'string', 'default' => '' ),
			'hide_empty' => array( 'type' => 'boolean', 'default' => false ),
			// --- Show Display Options ---
			'show_link' => array( 'type' => 'boolean', 'default' => true ),
			'title_position' => array( 'type' => 'string', 'default' => 'right' ),
			'show_avatar' => array( 'type' => 'boolean', 'default' => true ),
			'avatar_size' => array( 'type' => 'string', 'default' => 'thumbnail' ),
			'avatar_width' => array( 'type' => 'number', 'default' => 0 ),
			// --- Show Time Display Options ---
			'show_sched' => array( 'type' => 'boolean', 'default' => true ),
			'countdown' => array( 'type' => 'boolean', 'default' => true ),
			'time_format' => array( 'type' => 'string', 'default' => '' ),
			// --- Extra Display Options ---
			'display_hosts' => array( 'type' => 'boolean', 'default' => false ),
			'link_hosts' => array( 'type' => 'boolean', 'default' => true ),
			// 'display_producers' => array( 'type' => 'boolean', 'default' => false ),
			// 'link_producers' => array( 'type' => 'boolean', 'default' => false ),
			'show_encore' => array( 'type' => 'boolean', 'default' => true ),
		),
		// --- Current Playlist ---
		'current-playlist' => array(
			// --- Loading Options ---
			'ajax' => array( 'type' => 'string', 'default' => '' ),
			// dynamic' => array( 'type' => 'boolean', 'default' => true ),
			'no_playlist' => array( 'type' => 'string', 'default' => '' ),
			'hide_empty' => array( 'type' => 'boolean', 'default' => false ),
			// --- Playlist Display Options ---
			'playlist_title' => array( 'type' => 'boolean', 'default' => false ),
			'link' => array( 'type' => 'boolean', 'default' => true ),
			'countdown' => array( 'type' => 'boolean', 'default' => true ),
			// --- Track Display Options ---
			'song' => array( 'type' => 'boolean', 'default' => true ),
			'artist' => array( 'type' => 'boolean', 'default' => true ),
			'album' => array( 'type' => 'boolean', 'default' => false ),
			'label' => array( 'type' => 'boolean', 'default' => false ),
			'comments' => array( 'type' => 'boolean', 'default' => false ),
		),

		// === Show Related ===
		// 'show_posts'       => array(),
		// 'show_playlists'   => array(),

	);

	// --- add default switches to each block ---
	foreach ( $attributes as $block_slug => $attribute_list ) {
		$attribute_list['block'] = array( 'type' => 'boolean', 'default' => true );
		$attribute_list['pro'] = array( 'type' => 'boolean', 'default' => false );
		$attributes[$block_slug] = $attribute_list;
	}

	// --- filter and return ---
	$attributes = apply_filters( 'radio_station_block_attributes', $attributes );
	return $attributes;
}

// ---------------
// Register Blocks
// ---------------
// 2.5.0: added shortcode blocks for Gutenberg block editor
add_action( 'init', 'radio_station_register_blocks' );
function radio_station_register_blocks() {

	if ( !function_exists( 'register_block_type' ) ) {
		return;
	}

	// --- get block callbacks and attributes ---
	$callbacks = radio_station_get_block_callbacks();
	$attributes = radio_station_get_block_attributes();

	// --- loop block names to register blocks ---
	foreach ( $callbacks as $block_slug => $callback ) {
		$block_key = 'radio-station/' . $block_slug;
		$args = array(
			'render_callback' => $callback,
			'attributes'      => $attributes[$block_slug],
			'category'        => 'radio-station',
		);
		$args = apply_filters( 'radio_station_block_args', $args, $block_slug, $callback );
		register_block_type( $block_key, $args );
	}
}

// ---------------------------
// Enqueue Block Editor Assets
// ---------------------------
// 2.5.0: added for editor block scripts/styles
// reF: https://jasonyingling.me/enqueueing-scripts-and-styles-for-gutenberg-blocks/
add_action( 'enqueue_block_editor_assets', 'radio_station_block_editor_assets' );
function radio_station_block_editor_assets() {

	// --- get block callabacks ---
	$callbacks = radio_station_get_block_callbacks();

	// --- set block dependencies ---
	$deps = array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' );

	// --- set base block URL and path ---
	$blocks_url = plugins_url( '/blocks/', RADIO_STATION_FILE );
	$blocks_path = RADIO_STATION_DIR . '/blocks/';

	// --- loop callbacks to enqueue scripts ---
	$block_scripts = array();
	foreach ( $callbacks as $block_slug => $callback ) {
		$block_path = $blocks_path . $block_slug . '.js';
		if ( file_exists( $block_path ) ) {

			// --- set script data ---
			$block_scripts[$block_slug] = array(
				'slug'      => $block_slug,
				'handle'    => $block_slug . '-js',
				'url'       => $blocks_url . $block_slug . '.js',
				'version'   => filemtime( $block_path ),
				'deps'      => $deps,
			);

		}
	}

	// --- filter scripts and loop to enqueue ---
	$block_scripts = apply_filters( 'radio_station_block_scripts', $block_scripts );
	foreach ( $block_scripts as $script ) {
		wp_enqueue_script( $script['handle'], $script['url'], $script['deps'], $script['version'], true );
	}

	// --- enqueue admin script for localized variables ---
	$script_url = plugins_url( '/js/radio-station-admin.js', RADIO_STATION_FILE );
	$script_path = RADIO_STATION_DIR . 'js/radio-station-admin.js';
	$version = filemtime( $script_path );
	wp_enqueue_script( 'radio-station-admin', $script_url, $deps, $version, true );
	$js = radio_station_localization_script();
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'radio-station-admin', $js );

	// --- block editor support for conditional loading ---
	$script_url = plugins_url( '/blocks/editor.js', RADIO_STATION_FILE );
	$script_path = RADIO_STATION_DIR . 'blocks/editor.js';
	$version = filemtime( $script_path );
	wp_enqueue_script( 'radio-blockedit-js', $script_url, $deps, $version, true );

	// --- 2.5.12: init radio player settings in editor
	$js = radio_player_get_player_settings();
	wp_add_inline_script( 'radio-blockedit-js', $js, 'before' );

	// --- enqueue shortcode styles for blocks ---
	// $deps = array( 'wp-edit-blocks' );
	$stylesheets = array( 'shortcodes', 'schedule' ); // 'block-editor', 'blocks'
	foreach ( $stylesheets as $stylekey ) {
		$style_path = RADIO_STATION_DIR . 'css/rs-' . $stylekey . '.css';
		$style_url = plugins_url( 'css/rs-' . $stylekey . '.css', RADIO_STATION_FILE );
		$version = filemtime( $style_path );
		wp_enqueue_style( 'rs-' . $stylekey, $style_url, array(), $version, 'all' );
	}

	// --- block control style fixes ---
	$css = array();
	// - fix cutoff label widths -
	$css[] = '.components-panel .components-panel__body.radio-block-controls .components-panel__row label {
	width: 100%; max-width: 100%; min-width: 150px; overflow: visible;}' . "\n";
	$css[] = '.components-panel .components-panel__body.radio-block-controls .components-panel__row label.components-toggle-control__label {width: auto;}';
	// - multiple select minimum height fix -
	// ref: https://github.com/WordPress/gutenberg/issues/27166
	$css[] = '.components-panel .components-panel__body.radio-block-controls .components-select-control__input[multiple] {min-height: 100px;}';
	// - color dropdown label with fix -
	$css[] = '.components-panel .color-dropdown-control .components-base-control__label {min-width: 130px; vertical-align: middle;}';
	$css[] = '.components-panel .color-dropdown-control .color-dropdown-buttons, .components-panel .color-dropdown-control .color-dropdown-buttons button {vertical-align: middle;}';

	// --- add style fixes inline ---
	$css = implode( "\n", $css );
	$css = apply_filters( 'radio_station_block_control_styles', $css );
	// 2.5.0: use radio_station_add_inline_style
	radio_station_add_inline_style( 'wp-edit-blocks', $css );

	// --- enqueue radio player styles ---
	if ( array_key_exists( 'player', $callbacks ) ) {
		$suffix = ''; // dev temp
		$style_path = RADIO_STATION_DIR . 'player/css/radio-player' . $suffix . '.css';
		$style_url = plugins_url( '/player/css/radio-player' . $suffix . '.css', RADIO_STATION_FILE );
		$version = filemtime( $style_path );
		wp_enqueue_style( 'radio-player', $style_url, array(), $version, 'all' );

		// --- enqueue player control styles inline ---
		$control_styles = radio_player_control_styles( false );
		// 2.5.0: use radio_station_add_inline_style
		radio_station_add_inline_style( 'radio-player', $control_styles );
	}
}

// -----------------------------
// Enqueue Frontend Block Styles
// -----------------------------
// note: this is currently unnecessary as styles are enqueued in shortcodes
// and the shortcodes are used as the block render_callback functions already
// 2.5.0: added for any future frontend block style fixes
// note: according to WP docs this fired on both editor and frontend
// add_action( 'enqueue_block_assets', 'radio_station_enqueue_block_assets' );
function radio_station_enqueue_block_assets() {

    // --- enqueue shortcode styles for blocks ---
	// radio_station_enqueue_style( 'blocks' );
}

// ----------------------
// AJAX Load Block Script
// ----------------------
add_action( 'wp_ajax_radio_station_block_script', 'radio_station_block_script' );
function radio_station_block_script() {

	if ( !isset( $_REQUEST['handle'] ) ) {
		exit;
	}

	$js = '';
	$handle = sanitize_text_field( $_REQUEST['handle'] );
	if ( 'clock' == $handle ) {
		$js .= file_get_contents( RADIO_STATION_DIR . '/js/radio-station-clock.js' );
	} elseif ( 'countdown' == $handle ) {
		$js .= file_get_contents( RADIO_STATION_DIR . '/js/radio-station-countdown.js' );
	} elseif ( 'player' == $handle ) {
		// 2.5.12: prepend player settings to block loading
		$js .= radio_player_get_player_settings();
		$js .= file_get_contents( RADIO_STATION_DIR . '/player/js/radio-player.js' );
	} elseif ( 'schedule-table' == $handle ) {
		$js .= radio_station_master_schedule_table_js();
	} elseif ( 'schedule-tabs' == $handle ) {
		$js .= radio_station_master_schedule_tabs_js();
	} elseif ( 'schedule-list' == $handle ) {
		$js .= radio_station_master_schedule_list_js();
	}

	// --- filter javascript ---
	$js = apply_filters( 'radio_station_block_script', $js, $handle );

	// --- output javascript ---
	header( 'Content-Type: application/javascript' );
	if ( '' != $js ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $js;
	}
	exit;
}
