<?php

/*
* Support Functions for Radio Station plugin
* Author: Tony Hayes
* Since: 2.3.0
*/

if ( !defined( 'ABSPATH' ) ) exit;

// =======================================
// === Radio Station Support Functions ===
// =======================================

// === Data Functions ===
// - Get Show
// - Get Shows
// - Get Schedule Overrides
// x Get Show Schedule
// - Generate Unique Shift ID
// - Get Show Data
// - Get Show Data Meta
// - Show Data Meta Filter
// - Get Show Description
// - Get Override Metadata
// - Override Data Meta Filter
// - Get Show Override Value
// - Get Linked Overrides for Show
// - Get Linked Override Times
// - Get Current Playlist
// - Get Blog Posts for Show
// - Get Playlists for Show
// - Get Genre
// - Get Genres
// - Get Shows for Genre
// - Get Shows for Language
// === Show Avatar ===
// - Get Show Avatar ID
// - Get Show Avatar URL
// - Get Show Avatar
// === URL Functions ===
// - Get Streaming URL
// - Get Fallback URL
// - Get Stream Formats
// - Get Master Schedule Page URL
// - Get Radio Station API URL
// - Get Route URL
// - Get Feed URL
// - Get DJ / Host Profile URL
// - Get Producer Profile URL
// - Get Upgrade URL
// - Patreon Supporter Button
// - Patreon Button Styles
// - Send Directory Ping
// === Helper Functions ===
// - Get Icon Colors
// - Encode URI Component
// - Get Languages
// - Get Language Options
// - Get Language
// - Trim Excerpt
// - Shorten String
// - Sanitize Values
// - Sanitize Input Value
// - Get Meta Input Types
// - Sanitize Shortcode Values
// - KSES Allowed HTML
// - Link Tag Allowed HTML
// - Settings Inputs Allowed HTML


// ----------------------
// === Data Functions ===
// ----------------------

// --------
// Get Show
// --------
// 2.3.0: added get show data grabber
function radio_station_get_show( $show ) {
	if ( !is_object( $show ) ) {
		if ( is_string( $show ) ) {
			global $wpdb;
			$query = "SELECT ID FROM " . $wpdb->posts . " WHERE post_type = '" . RADIO_STATION_SHOW_SLUG . "' AND post_name = %s";
			$show_id = $wpdb->get_var( $wpdb->prepare( $query, $show ) );
			$show = get_post( $show_id );
		} elseif ( is_int( $show ) ) {
			$show = get_post( $show );
		}
	}

	return $show;
}

// ---------
// Get Shows
// ---------
// 2.3.0: added get shows data grabber
function radio_station_get_shows( $args = false ) {

	// --- set default args ---
	$query_args = array(
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => 'publish',
		'numberposts' => -1,
		'meta_query'  => array(
			'relation' => 'AND',
			array(
				'key'     => 'show_sched',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => 'show_active',
				'value'   => 'on',
				'compare' => '=',
			),
		),
		'orderby' => 'post_name',
		'order'   => 'ASC',
	);

	// --- overwrite defaults with any arguments passed ---
	if ( $args && is_array( $args ) && ( count( $args ) > 0 ) ) {
		foreach ( $args as $key => $value ) {
			$query_args[$key] = $value;
		}
	}

	// 2.5.0: added query args filter
	$query_args = apply_filters( 'radio_station_get_shows_args', $query_args, $args );

	// --- get shows, filter and return ---
	// 2.5.0: added missing args as third filter argument
	$shows = get_posts( $query_args );
	$shows = apply_filters( 'radio_station_shows', $shows, $query_args, $args );
	// note: backwards compatible misnamed filter
	$shows = apply_filters( 'radio_station_get_shows', $shows, $query_args, $args );
	return $shows;
}

// ----------------------
// Get Schedule Overrides
// ----------------------
// 2.5.0: added function to match radio_station_get_shows
function radio_station_get_overrides( $args = false ) {

	// --- set default args ---
	$query_args = array(
		'post_type'   => RADIO_STATION_OVERRIDE_SLUG,
		'post_status' => 'publish',
		'numberposts' => -1,
		'meta_query'  => array(
			// 'relation' => 'AND',
			array(
				'key'     => 'show_override_sched',
				'compare' => 'EXISTS',
			),
			/* array(
				'key'     => 'show_active',
				'value'   => 'on',
				'compare' => '=',
			), */
		),
		'orderby' => 'post_name',
		'order'   => 'ASC',
	);

	// --- overwrite defaults with any arguments passed ---
	if ( $args && is_array( $args ) && ( count( $args ) > 0 ) ) {
		foreach ( $args as $key => $value ) {
			$query_args[$key] = $value;
		}
	}

	// 2.5.0: added query args filter
	$query_args = apply_filters( 'radio_station_get_overrides_args', $query_args, $args );

	// --- get overrides, filter and return ---
	$overrides = get_posts( $query_args );
	$overrides = apply_filters( 'radio_station_overrides', $overrides, $query_args, $args );
	return $overrides;
}

// -----------------
// Get Show Schedule
// -----------------
// 2.3.0: added to give each shift a unique ID
// 2.5.0: rewritten and moved to schedules.php

// ------------------------
// Generate Unique Shift ID
// ------------------------
function radio_station_unique_shift_id() {

	$shift_ids = get_option( 'radio_station_shifts_ids' );
	if ( !$shift_ids ) {
		$shift_ids = array();
	}
	$unique_id = wp_generate_password( 8, false, false );
	if ( in_array( $unique_id, $shift_ids ) ) {
		while ( in_array( $unique_id, $shift_ids ) ) {
			$unique_id = wp_generate_password( 8, false, false );
		}
		$shift_ids[] = $unique_id;
	}

	// --- store the unique shift ID ---
	update_option( 'radio_station_shifts_ids', $shift_ids );

	return $unique_id;
}

// --------------------
// Generate Hashed GUID
// --------------------
// 2.3.2: add hashing function for show GUID
function radio_station_get_show_guid( $show_id ) {

	global $wpdb;
	$query = "SELECT guid FROM " . $wpdb->posts . " WHERE ID = %d";
	// 2.5.6: added missing prepare for query
	$guid = $wpdb->get_var( $wpdb->prepare( $query, $show_id ) );
	if ( !$guid ) {
		$guid = get_permalink( $show_id );
	}
	$hash = md5( $guid );

	return $hash;
}

// -------------
// Get Show Data
// -------------
// 2.3.0: added get show data grabber
// 2.5.0: added optional atts as fourth argument
function radio_station_get_show_data( $datatype, $show_id, $args = array(), $atts = array() ) {

	// --- we need a data type and show ID ---
	if ( !$datatype ) {
		return false;
	}
	if ( !$show_id ) {
		return false;
	}

	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Get ' . esc_html( $datatype ) . ' for Show ' . esc_html( $show_id ) . '</span>';
	}

	// --- get meta key for valid data types ---
	if ( 'posts' == $datatype ) {
		$metakey = 'post_showblog_id';
	} elseif ( 'playlists' == $datatype ) {
		$metakey = 'playlist_show_id';
	} elseif ( 'episodes' == $datatype ) {
		$metakey = 'episode_show_id';
	} elseif ( 'hosts' == $datatype ) {
		$metakey = 'show_user_list';
		$userkey = 'host_user_id';
	} elseif ( 'producers' == $datatype ) {
		$metakey = 'show_producer_list';
		$userkey = 'producer_user_id';
	} else {
		return false;
	}

	// --- check for optional arguments ---
	$default = true;
	if ( !isset( $args['limit'] ) ) {
		$args['limit'] = false;
	} elseif ( false !== $args['limit'] ) {
		$default = false;
	}
	if ( !isset( $args['columns'] ) || !is_array( $args['columns'] ) || ( count( $args['columns'] ) < 1 ) ) {
		$columns = '*';
	} else {
		$columns = array();
		$default = false;
		$valid = array(
			'ID',
			'post_author',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_title',
			'post_excerpt',
			'post_status',
			'comment_status',
			'ping_status',
			'post_password',
			'post_name',
			'to_ping',
			'pinged',
			'post_modified',
			'post_modified_gmt',
			'post_content_filtered',
			'post_parent',
			'guid',
			'menu_order',
			'post_type',
			'post_mime_type',
			'comment_count',
		);
		foreach ( $args['columns'] as $i => $column ) {
			if ( in_array( $column, $valid ) ) {
				if ( !isset( $columns ) ) {
					$columns = 'posts.' . $column;
				} else {
					$columns .= ', posts.' . $column;
				}
			}
		}
	}

	// --- check for cached default show data ---
	/* if ( $default ) {
		// 2.5.0: added args and atts as optional filter arguments to match cached data
		$default_data = apply_filters( 'radio_station_cached_data', false, $datatype, $show_id, $args, $atts );
		if ( $default_data ) {
			if ( RADIO_STATION_DEBUG ) {
				echo '<span style="display:none;">Using Cached Data:' . esc_html( print_r( $default_data, true ) ) . '</span>';
			}
			return $default_data;
		}
	} */

	// --- get records with associated show ID ---
	global $wpdb;
	if ( 'posts' == $datatype ) {

		// 2.3.3.4: handle possible multiple show post values
		// 2.3.3.9: added 'i:' prefix to LIKE match value
		// 2.5.6: fix prepare query syntax by separating LIKE statement
		// $query = "SELECT post_id,meta_value FROM " . $wpdb->prefix . "postmeta WHERE meta_key = %s";
		// $query = $wpdb->prepare( $query, $metakey );
		// $query .= "AND meta_value LIKE '%i:" . $show_id . "%'";
		// 2.5.6: then use wpdb prepare method for LIKE statement
		// 2.5.9: improve LIKE statement code with prepare method
		$query = "SELECT post_id,meta_value FROM " . $wpdb->prefix . "postmeta WHERE meta_key = %s AND meta_value LIKE '%%%s%%'";
		$results = $wpdb->get_results( $wpdb->prepare( $query, array( $metakey, 'i:' . $wpdb->esc_like( $show_id ) ) ), ARRAY_A );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Query: ' . esc_html( $query ) . '</span>';
			echo '<span style="display:none;">Related Results: ' . esc_html( print_r( $results, true ) ) . '</span>';
		}
		if ( !$results || !is_array( $results ) || ( count( $results ) < 1 ) ) {
			return false;
		}

		// --- get/check post IDs in post meta ---
		$post_ids = array();
		foreach ( $results as $result ) {
			// TODO: recheck if raw result is serialized or array ?
			$show_ids = maybe_unserialize( $result['meta_value'] );
			if ( $show_id == $result['meta_value'] || in_array( $show_id, $show_ids ) ) {
				$post_ids[] = $result['post_id'];
			}
		}

	} elseif ( ( 'hosts' == $datatype ) || ( 'producers' == $datatype ) ) {

		// 2.3.3.9; added get host/producer profile posts
		$user_ids = get_post_meta( $show_id, $metakey, true );
		if ( !$user_ids ) {
			return false;
		}
		$post_ids = $no_profile_ids = array();
		foreach ( $user_ids as $user_id ) {
			$query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = %s AND meta_value = %d";
			$profile_id = $wpdb->get_var( $wpdb->prepare( $query, array( $userkey, $user_id ) ) );
			if ( RADIO_STATION_DEBUG ) {
				echo '<span style="display:none;">Related Query: ' . esc_html( $query ) . '</span>';
				echo '<span style="display:none;">Related Result: ' . esc_html( print_r( $profile_id, true ) ) . '</span>';
			}
			if ( $profile_id ) {
				$post_ids[] = $profile_id;
			} else {
				$no_profile_ids[] = $user_id;
			}
		}

	} else {

		// --- other types (episodes/playlists) ---
		$query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = %s AND meta_value = %d";
		$post_metas = $wpdb->get_results( $wpdb->prepare( $query, array( $metakey, $show_id ) ), ARRAY_A );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Query: ' . esc_html( $query ) . '</span>';
			echo '<span style="display:none;">Related Results: ' . esc_html( print_r( $post_metas, true ) ) . '</span>';
		}
		if ( !$post_metas || !is_array( $post_metas ) || ( count( $post_metas ) < 1 ) ) {
			return false;
		}

		// --- get post IDs from post meta ---
		$post_ids = array();
		foreach ( $post_metas as $post_meta ) {
			$post_ids[] = $post_meta['post_id'];
		}
	}
	// 2.5.0: added filter for resulting post IDs
	$post_ids = apply_filters( 'radio_station_show_' . $datatype . '_ids', $post_ids, $show_id, $args, $atts );

	// --- check for post IDs ---
	if ( count( $post_ids ) < 1 ) {
		return false;
	}

	// --- get posts from post IDs ---
	// 2.5.9: create decimal placeholder list and values array for wpdb prepare
	$id_list_string = '';
	$values = array();
	foreach ( $post_ids as $i => $post_id ) {
		$id_list_string .= '%d';
		if ( ( $i + 1 ) != count( $post_ids ) ) {
			$id_list_string .= ',';
		}
		$values[] = $post_id;
	}
	// $post_id_list = implode( ',', $post_ids );
	// $query = "SELECT " . $columns . " FROM " . $wpdb->prefix . "posts WHERE ID IN(" . $post_id_list . ") AND post_status = 'publish'";
	$query = "SELECT " . $columns . " FROM " . $wpdb->prefix . "posts WHERE ID IN(" . $id_list_string . ") AND post_status = 'publish'";
	
	// 2.5.6: allow for alternative ordering attributes
	if ( !isset( $atts['orderby'] ) || ( 'date' == strtolower( $atts['orderby'] ) ) ) {
		$query .= " ORDER BY post_date";
	} elseif ( 'title' == strtolower( $atts['orderby'] ) ) {
		$query .= " ORDER BY post_title";
	} else {
		$query .= " ORDER BY post_date";
	}
	if ( !isset( $atts['order'] ) || ( 'DESC' == strtoupper( $atts['order'] ) ) ) {
		$query .= " DESC";
	} elseif ( 'ASC' == strtoupper( $atts['order'] ) ) {
		$query .= " ASC";
	}
	// 2.5.6: add filter to allow for modification of query
	$query = apply_filters( 'radio_station_show_' . $datatype . '_query', $query, $show_id, $args, $atts );
	if ( $args['limit'] ) {
		$query .= " LIMIT %d";
		$values[] = $args['limit'];
	}
	
	// 2.5.9: use wpdb prepare with post ID list placeholder string
	// $results = $wpdb->get_results( $query, ARRAY_A );
	$results = $wpdb->get_results( $wpdb->prepare( $query, $values ), ARRAY_A );
	$results = apply_filters( 'radio_station_show_' . $datatype, $results, $show_id, $args, $atts );

	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">' . esc_html( $datatype ) . ' for Show ' . esc_html( $show_id ) . ': ';
		echo esc_html( print_r( $results, true ) );
		echo 'Query: ' . esc_html( $query ) . '</span>';
	}

	// 2.4.0.6: add processing of post excerpts
	if ( $results && is_array( $results ) ) {
		foreach ( $results as $i => $result ) {
			// 2.5.0: added check that post content is not empty
			if ( empty( $result['post_excerpt'] ) && !empty( $result['post_content'] ) ) {
				$length = apply_filters( 'radio_station_show_data_excerpt_length', 55 );
				$more = apply_filters( 'radio_station_show_data_excerpt_more', '' );
				$result['post_excerpt'] = radio_station_trim_excerpt( $result['post_content'], $length, $more, false );
				$results[$i] = $result;
			}
		}
	}

	// --- non-post (user) IDS ---
	if ( isset( $no_profile_ids ) && ( count( $no_profile_ids ) > 0 ) ) {
		foreach ( $no_profile_ids as $user_id ) {
			$user = get_user_by( 'ID', $user_id );
			$results[] = $user;
		}
	}

	// --- maybe cache default show data ---
	if ( $default ) {
		do_action( 'radio_station_cache_data', $datatype, $show_id, $results, $args, $atts );
	}

	// --- filter and return ---
	// 2.5.0: added optional atts argument to filter
	$results = apply_filters( 'radio_station_show_' . $datatype, $results, $show_id, $args, $atts );
	return $results;
}

// ------------------
// Get Show Data Meta
// ------------------
function radio_station_get_show_data_meta( $show, $single = false ) {

	global $radio_station_data;

	// --- get show post ---
	if ( !is_object( $show ) ) {
		$show = get_post( $show );
	}

	// --- get show terms ---
	$genre_list = $language_list = array();
	$genres = wp_get_post_terms( $show->ID, RADIO_STATION_GENRES_SLUG );
	if ( $genres ) {
		foreach ( $genres as $genre ) {
			$genre_list[] = $genre->name;
		}
	}
	$languages = wp_get_post_terms( $show->ID, RADIO_STATION_LANGUAGES_SLUG );
	if ( $languages ) {
		foreach ( $languages as $language ) {
			$language_list[] = $language->name;
		}
	}

	// --- get show data ---
	// note: show email intentionally not included
	// $show_email = get_post_meta( $show->ID, 'show_email', true );
	$show_link = get_post_meta( $show->ID, 'show_link', true );
	$show_file = get_post_meta( $show->ID, 'show_file', true );
	$show_schedule = radio_station_get_show_schedule( $show->ID );
	$show_shifts = array();
	if ( $show_schedule && is_array( $show_schedule ) && ( count( $show_schedule ) > 0 ) ) {
		foreach ( $show_schedule as $i => $shift ) {
			$shift = radio_station_validate_shift( $shift );
			$shift['id'] = $i;
			if ( !isset( $shift['disabled'] ) || ( 'yes' != $shift['disabled'] ) ) {
				$show_shifts[] = $shift;
			}
		}
	}

	// --- get show user data ---
	$show_hosts = get_post_meta( $show->ID, 'show_user_list', true );
	$show_producers = get_post_meta( $show->ID, 'show_producer_list', true );
	$hosts = $producers = array();
	if ( $show_hosts ) {
		// 2.4.0.4: convert possible (old) non-array value
		if ( !is_array( $show_hosts ) ) {
			$show_hosts = array( $show_hosts );
		}
		if ( is_array( $show_hosts ) && ( count( $show_hosts ) > 0 ) ) {
			foreach ( $show_hosts as $host ) {
				if ( isset( $radio_station_data['user-' . $host] ) ) {
					$user = $radio_station_data['user-' . $host];
				} else {
					$user = get_user_by( 'ID', $host );
					$radio_station_data['user-' . $host] = $user;
				}
				// 2.3.3.9: added check user still exists
				if ( $user ) {
					$hosts[] = array(
						'name'  => $user->display_name,
						'url'   => radio_station_get_host_url( $host ),
					);
				}
			}
		}
	}
	if ( $show_producers ) {
		// 2.4.0.4: convert possible (old) non-array value
		if ( !is_array( $show_producers ) ) {
			$show_producers = array( $show_producers );
		}
		if ( is_array( $show_producers ) && ( count( $show_producers ) > 0 ) ) {
			foreach ( $show_producers as $producer ) {
				if ( isset( $radio_station_data['user-' . $producer] ) ) {
					$user = $radio_station_data['user-' . $producer];
				} else {
					$user = get_user_by( 'ID', $producer );
					$radio_station_data['user-' . $producer] = $user;
				}
				// 2.3.3.9: added check user still exists
				if ( $user ) {
					$producers[] = array(
						'name'  => $user->display_name,
						'url'   => radio_station_get_producer_url( $producer ),
					);
				}
			}
		}
	}

	// --- get avatar and thumbnail URL ---
	// 2.3.1: added show avatar and image URLs
	// 2.4.0.6: added get show avatar ID
	$avatar_id = radio_station_get_show_avatar_id( $show->ID );
	$avatar_url = radio_station_get_show_avatar_url( $show->ID );
	if ( !$avatar_url ) {
		$avatar_url = '';
	}
	$thumbnail_url = '';
	$thumbnail_id = get_post_meta( $show->ID, '_thumbnail_id', true );
	if ( $thumbnail_id ) {
		$thumbnail = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
		// 2.5.10: handle failure of return value
		if ( $thumbnail && is_array( $thumbnail ) && isset( $thumbnail[0] ) ) {
			$thumbnail_url = $thumbnail[0];
		}
	}

	// --- create array and return ---
	// 2.3.1: added show avatar and image URLs
	// 2.4.0.6: added avatar and image IDs
	$show_data = array(
		'id'         => $show->ID,
		'name'       => $show->post_title,
		'slug'       => $show->post_name,
		'url'        => get_permalink( $show->ID ),
		'latest'     => $show_file,
		'website'    => $show_link,
		// note: left out intentionally to avoid spam scraping
		// 'email'   => $show_email,
		'hosts'      => $hosts,
		'producers'  => $producers,
		'genres'     => $genre_list,
		'languages'  => $language_list,
		'schedule'   => $show_shifts,
		'avatar_url' => $avatar_url,
		'avatar_id'  => $avatar_id,
		'image_url'  => $thumbnail_url,
		'image_id'   => $thumbnail_id,
	);

	// --- data route / feed for show ---
	if ( 'yes' == radio_station_get_setting( 'enable_data_routes' ) ) {
		$route_link = radio_station_get_route_url( 'shows' );
		$show_route = add_query_arg( 'show', $show->post_name, $route_link );
		$show_data['route'] = $show_route;
	}
	if ( 'yes' == radio_station_get_setting( 'enable_data_feeds' ) ) {
		$feed_link = radio_station_get_feed_url( 'shows' );
		$show_feed = add_query_arg( 'show', $show->post_name, $feed_link );
		$show_data['feed'] = $show_feed;
	}

	// --- add extra data for single show route/feed ---
	$show_id = $show->ID;
	if ( $single ) {

		// --- add show posts ---
		$show_data['posts'] = radio_station_get_show_posts( $show->ID );

		// --- add show playlists ---
		$show_data['playlists'] = radio_station_get_show_playlists( $show->ID );

		// --- filter to maybe add more data ---
		$show_data = apply_filters( 'radio_station_show_data_meta', $show_data, $show_id );
	}

	// --- maybe cache Show meta data ---
	do_action( 'radio_station_cache_data', 'show_meta', $show_id, $show_data );

	return $show_data;
}

// ---------------------
// Show Data Meta Filter
// ---------------------
// 2.5.6: added to get show data for show in schedule engine
add_filter( 'radio_station_schedule_show_data_meta', 'radio_station_show_data_meta_filter', 10, 2 );
function radio_station_show_data_meta_filter( $show_id, $shift_id ) {

	// --- get (or get stored) show data ---						
	global $radio_station_data;
	if ( isset( $radio_station_data['show-' . $show_id] ) ) {
		$show = $radio_station_data['show-' . $show_id];
	} else {
		$show = radio_station_get_show_data_meta( $show_id );
		$radio_station_data['show-' . $show_id] = $show;
	}
	return $show;
}

// --------------------
// Get Show Description
// --------------------
// 2.3.3.8: added for show data API feed
function radio_station_get_show_description( $show_data ) {

	// --- get description and excerpt ---
	$show_id = $show_data['id'];
	$show_post = get_post( $show_id );
	$description = $show_post->post_content;
	if ( !empty( $show_post->post_excerpt ) ) {
		$excerpt = $show_post->post_excerpt;
	} else {
		$length = apply_filters( 'radio_station_show_data_excerpt_length', 55 );
		$more = apply_filters( 'radio_station_show_data_excerpt_more', '' );
		$excerpt = radio_station_trim_excerpt( $description, $length, $more, false );
	}

	// --- filter description and excerpt ---
	$description = apply_filters( 'radio_station_show_data_description', $description, $show_id );
	$excerpt = apply_filters( 'radio_station_show_data_excerpt', $excerpt, $show_id );

	// --- add to existing show data ---
	$show_data['description'] = $description;
	$show_data['excerpt'] = $excerpt;

	// echo "Description: " . print_r( $description, true ) . PHP_EOL;
	// echo "Excerpt: " . print_r( $excerpt, true ) . PHP_EOL;

	return $show_data;
}

// ----------------------
// Get Override Data Meta
// ----------------------
function radio_station_get_override_data_meta( $override ) {

	global $radio_station_data;

	// --- get override post ---
	if ( !is_object( $override ) ) {
		$override = get_post( $override );
	}
	$override_id = $override->ID;

	// --- get override terms ---
	$genre_list = $language_list = array();
	$genres = wp_get_post_terms( $override_id, RADIO_STATION_GENRES_SLUG );
	if ( $genres ) {
		foreach ( $genres as $genre ) {
			$genre_list[] = $genre->name;
		}
	}
	$languages = wp_get_post_terms( $override_id, RADIO_STATION_LANGUAGES_SLUG );
	if ( $languages ) {
		foreach ( $languages as $language ) {
			$language_list[] = $language->name;
		}
	}

	// --- get override user data ---
	$override_hosts = get_post_meta( $override_id, 'override_user_list', true );
	$override_producers = get_post_meta( $override_id, 'override_producer_list', true );
	$hosts = $producers = array();
	if ( is_array( $override_hosts ) && ( count( $override_hosts ) > 0 ) ) {
		foreach ( $override_hosts as $host ) {
			if ( isset( $radio_station_data['user-' . $host] ) ) {
				$user = $radio_station_data['user-' . $host];
			} else {
				$user = get_user_by( 'ID', $host );
				$radio_station_data['user-' . $host] = $user;
			}
			$hosts[]['name'] = $user->display_name;
			$hosts[]['url'] = radio_station_get_host_url( $host );
		}
	}
	if ( is_array( $override_producers ) && ( count( $override_producers ) > 0 ) ) {
		foreach ( $override_producers as $producer ) {
			if ( isset( $radio_station_data['user-' . $producer] ) ) {
				$user = $radio_station_data['user-' . $producer];
			} else {
				$user = get_user_by( 'ID', $producer );
				$radio_station_data['user-' . $producer] = $user;
			}
			$producers[]['name'] = $user->display_name;
			$producers[]['url'] = radio_station_get_producer_url( $producer );
		}
	}

	// --- get avatar and thumbnail URL ---
	// 2.3.1: added show avatar and image URLs
	// 2.4.0.6: get avatar image attachment ID
	$avatar_id = radio_station_get_show_avatar_id( $override_id );
	$avatar_url = radio_station_get_show_avatar_url( $override_id );
	if ( !$avatar_url ) {
		$avatar_url = '';
	}
	$thumbnail_url = '';
	$thumbnail_id = get_post_meta( $override_id, '_thumbnail_id', true );
	if ( $thumbnail_id ) {
		$thumbnail = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
		$thumbnail_url = $thumbnail[0];
	}

	// --- create array and return ---
	// 2.3.1: added show avatar and image URLs
	// 2.4.0.6: added avatar and image IDs
	// 2.5.0: add duplicated title key for compatibility
	$override_data = array(
		'id'         => $override_id,
		'title'      => $override->post_title,
		'name'       => $override->post_title,
		'slug'       => $override->post_name,
		'url'        => get_permalink( $override_id ),
		'genres'     => $genre_list,
		'languages'  => $language_list,
		'hosts'      => $hosts,
		'producers'  => $producers,
		'avatar_url' => $avatar_url,
		'avatar_id'  => $avatar_id,
		'image_url'  => $thumbnail_url,
		'image_id'   => $thumbnail_id,
	);

	// --- linked Show ID ---
	// 2.3.3.9: maybe use linked show data
	$linked_id = get_post_meta( $override_id, 'linked_show_id', true );
	if ( $linked_id ) {

		// --- use linked show data ---
		$show_data = radio_station_get_show_data_meta( $linked_id );
		$show_fields = get_post_meta( $override_id, 'linked_show_fields', true );

		// --- map info keys to meta keys ---
		$fields = array(
			'name'       => 'show_title',
			'hosts'      => 'show_user_list',
			'producers'  => 'show_producer_list',
			'avatar_url' => 'show_avatar',
			// 'image_url' => 'show_thumbnail',
		);

		// --- apply selected show data to override ---
		foreach ( $fields as $key => $meta_key ) {
			if ( isset( $show_fields[$meta_key] ) && $show_fields[$meta_key] ) {
				$override_data[$key] = $show_data[$key];
			}
		}
	}

	// --- filter and return ---
	$override_data = apply_filters( 'radio_station_override_data', $override_data, $override_id );
	return $override_data;
}

// -------------------------
// Override Data Meta Filter
// -------------------------
// 2.5.6: added to get override data for override in schedule engine
add_filter( 'radio_station_schedule_override_data_meta', 'radio_station_override_data_meta_filter', 10, 2 );
function radio_station_override_data_meta_filter( $override_id, $shift_id ) {

	// --- get (or get stored) override data ---						
	global $radio_station_data;
	if ( isset( $radio_station_data['override-' . $override_id] ) ) {
		$override = $radio_station_data['override-' . $override_id];
	} else {
		$override = radio_station_get_override_data_meta( $override_id );
		$radio_station_data['override-' . $override_id] = $override;
	}
	return $override;
}

// -----------------------
// Get Show Override Value
// -----------------------
// 2.3.3.9: added to check linked show field by key
function radio_station_get_show_override( $override_id, $meta_key ) {

	global $radio_station_data;

	// --- check/cache linked show values ---
	if ( isset( $radio_station_data['linked-show-' . $override_id] ) ) {
		$linked_id = $radio_station_data['linked-show-' . $override_id];
		if ( isset( $radio_station_data['linked-fields-' . $override_id] ) ) {
			$linked_fields = $radio_station_data['linked-fields-' . $override_id];
		}
	} else {
		$linked_id = get_post_meta( $override_id, 'linked_show_id', true );
		$radio_station_data['linked-show-' . $override_id] = $linked_id;
		if ( $linked_id ) {
			$linked_fields = get_post_meta( $override_id, 'linked_show_fields', true );
			$radio_station_data['linked-fields-' . $override_id] = $linked_fields;
		}
	}

	// --- return the show field value for this key ---
	// echo "Override: " . $override_id . " - Meta Key: " . $meta_key;
	if ( $linked_id ) {
		// echo " - Linked Show: " . $linked_id;
		if ( !isset( $linked_fields[$meta_key] ) || empty( $linked_fields[$meta_key] ) ) {
			if ( 'show_title' == $meta_key ) {
				$post = get_post( $linked_id );
				$value = $post->post_title;
			} elseif ( 'show_image' == $meta_key ) {
				$value = get_post_meta( $linked_id, '_thumbnail_id', true );
			} else {
				$value = get_post_meta( $linked_id, $meta_key, true );
				// echo " - Value: " . $value . "<br>" . PHP_EOL;
			}
			if ( $value ) {
				return $value;
			} else {
				return '';
			}
		}
	}
	return false;
}

// -----------------------------
// Get Linked Overrides for Show
// -----------------------------
// 2.3.3.9: added for getting linked show overrides
function radio_station_get_linked_overrides( $post_id ) {

	// -- get show ID for override or show post ---
	$linked_id = get_post_meta( $post_id, 'linked_show_id', true );
	if ( $linked_id ) {
		$show_id = $linked_id;
	} else {
		$show_id = $post_id;
	}

	// --- get linked override IDs via show ID ---
	global $wpdb;
	$query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'linked_show_id' AND meta_value = %d";
	$results = $wpdb->get_results( $wpdb->prepare( $query, $show_id ), ARRAY_A );
	$override_ids = array();
	if ( $results && is_array( $results ) && ( count( $results ) > 0 ) ) {
		foreach ( $results as $result ) {
			$override_id = $result['post_id'];

			// --- check for published post status ---
			$query = "SELECT post_status FROM " . $wpdb->prefix . "posts WHERE ID = %d";
			$status = $wpdb->get_var( $wpdb->prepare( $query, $override_id ) );
			if ( 'publish' == $status ) {
				$override_ids[] = $override_id;
			}
		}
	}

	// --- filter and return ---
	$override_ids = apply_filters( 'radio_station_linked_overrides', $override_ids, $post_id );
	return $override_ids;
}

// -------------------------
// Get Linked Override Times
// -------------------------
// 2.3.3.9: added for show page display
function radio_station_get_linked_override_times( $post_id ) {

	$override_ids = radio_station_get_linked_overrides( $post_id );
	$overrides = array();
	if ( $override_ids && is_array( $override_ids ) && ( count( $override_ids ) > 0 ) ) {
		foreach ( $override_ids as $override_id ) {
			$schedule = get_post_meta( $override_id, 'show_override_sched', true );
			if ( $schedule ) {
				if ( !is_array( $schedule ) ) {
					$schedule = array( $schedule );
				}
				foreach ( $schedule as $override ) {
					// 2.5.6: add check if override is disabled
					if ( 'yes' != $override['disabled'] ) {
						$overrides[] = $override;
					}
				}
			}
		}
	}

	// --- filter and return ---
	$overrides = apply_filters( 'radio_station_linked_override_times', $overrides, $post_id );
	return $overrides;
}

// --------------------
// Get Current Playlist
// --------------------
// 2.3.3.5: added get current playlist function
function radio_station_get_current_playlist() {

	$current_show = radio_station_get_current_show();
	// 2.5.0: return false early if no current show/id
	if ( !$current_show || !isset( $current_show['show']['id'] ) ) {
		return false;
	}
	$show_id = $current_show['show']['id'];
	$playlists = radio_station_get_show_playlists( $show_id );
	if ( !$playlists || !is_array( $playlists ) || ( count( $playlists ) < 1 ) ) {
		return false;
	}

	$playlist_id = $playlists[0]['ID'];
	$tracks = get_post_meta( $playlist_id, 'playlist', true );
	if ( !$tracks || !is_array( $tracks ) || ( count( $tracks ) < 1 ) ) {
		return false;
	}

	// --- split off tracks marked as queued ---
	$entries = $queued = $played = array();
	foreach ( $tracks as $i => $track ) {
		foreach ( $track as $key => $value ) {
			unset( $track[$key] );
			$key = str_replace( 'playlist_entry_', '', $key );
			$track[$key] = $value;
			$entries[$i] = $track;
		}
		// 2.4.0.3: fix for queued and played arrays
		foreach ( $track as $key => $value ) {
			if ( 'status' == $key ) {
				if ( 'queued' == $value ) {
					$queued[] = $track;
				} elseif ( 'played' == $value ) {
					$played[] = $track;
				}
			}
		}
	}

	$latest = array();
	if ( isset( $queued[0] ) ) {
		$latest = $queued[0];
	}

	// --- get the track list for display  ---
	$playlist = array(
		'tracks'   => $entries,
		'queued'   => $queued,
		'played'   => $played,
		'latest'   => $latest,
		'id'       => $playlist_id,
		'url'      => get_permalink( $playlist_id ),
		'show'     => $show_id,
		'show_url' => get_permalink( $show_id ),
	);

	return $playlist;
}

// -----------------------
// Get Blog Posts for Show
// -----------------------
// 2.3.0: added show blog post data grabber
function radio_station_get_show_posts( $show_id = false, $args = array() ) {
	return radio_station_get_show_data( 'posts', $show_id, $args );
}

// ----------------------
// Get Playlists for Show
// ----------------------
// 2.3.0: added show playlist data grabber
function radio_station_get_show_playlists( $show_id = false, $args = array() ) {
	return radio_station_get_show_data( 'playlists', $show_id, $args );
}

// ---------
// Get Genre
// ---------
// 2.3.0: added genre data grabber
function radio_station_get_genre( $genre ) {

	// 2.3.3.8: explicitly check for numberic genre term ID
	// 2.5.9.10: fix to check with ctype_digit not absint
	if ( !ctype_digit( $genre ) ) {
		// $genre = sanitize_title( $genre );
		$term = get_term_by( 'slug', $genre, RADIO_STATION_GENRES_SLUG );
		// echo '<span style="display:none;">Genre slug ' . $genre. ' - Genre ' . print_r( $term, true ) . '</span>';
		if ( !$term ) {
			$term = get_term_by( 'name', $genre, RADIO_STATION_GENRES_SLUG );
		}
	} else {
		$id = absint( $genre );
		if ( $id < 1 ) {
			return false;
		}
		$term = get_term_by( 'id', $genre, RADIO_STATION_GENRES_SLUG );
	}
	if ( !$term ) {
		return false;
	}
	$genre_data = array();
	$genre_data[$term->name] = array(
		'id'            => $term->term_id,
		'name'          => $term->name,
		'slug'          => $term->slug,
		'description'   => $term->description,
		'url'           => get_term_link( $term, RADIO_STATION_GENRES_SLUG ),
	);

	return $genre_data;
}

// ----------
// Get Genres
// ----------
// 2.3.0: added genres data grabber
function radio_station_get_genres( $args = false ) {

	$defaults = array( 'taxonomy' => RADIO_STATION_GENRES_SLUG, 'orderby' => 'name', 'hide_empty' => true );
	if ( $args && is_array( $args ) ) {
		foreach ( $args as $key => $value ) {
			$defaults[$key] = $value;
		}
	}
	$terms = get_terms( $defaults );
	$genres = array();
	if ( $terms ) {
		foreach ( $terms as $term ) {
			$genres[$term->name] = array(
				'id'            => $term->term_id,
				'name'          => $term->name,
				'slug'          => $term->slug,
				'description'   => $term->description,
				'url'           => get_term_link( $term, RADIO_STATION_GENRES_SLUG ),
			);
		}
	}

	// --- filter and return ---
	$genres = apply_filters( 'radio_station_get_genres', $genres, $args );

	return $genres;
}

// -------------------
// Get Shows for Genre
// -------------------
// 2.3.0: added get shows for genre data grabber
function radio_station_get_genre_shows( $genre = false ) {

	if ( !$genre ) {
		// --- get shows without a genre assigned ---
		// ref: https://core.trac.wordpress.org/ticket/29181
		$tax_query = array(
			array(
				'taxonomy' => RADIO_STATION_GENRES_SLUG,
				'operator' => 'NOT EXISTS',
			),
		);
	} else {
		// --- get shows with specific genre assigned ---
		$tax_query = array(
			array(
				'taxonomy' => RADIO_STATION_GENRES_SLUG,
				'field'    => 'slug',
				'terms'    => $genre,
			),
		);
	}
	$args = array(
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => 'publish',
		'tax_query'   => $tax_query,
		'meta_query'  => array(
			'relation' => 'AND',
			array(
				'key'     => 'show_sched',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => 'show_active',
				'value'   => 'on',
				'compare' => '=',
			),
		),
	);
	$args = apply_filters( 'radio_station_show_genres_query_args', $args, $genre );
	$shows = new WP_Query( $args );

	return $shows;
}

// ----------------------
// Get Shows for Language
// ----------------------
// 2.3.0: added get shows for language data grabber
function radio_station_get_language_shows( $language = false ) {

	if ( !$language ) {
		// --- get shows without a language assigned ---
		// ref: https://core.trac.wordpress.org/ticket/29181
		$tax_query = array(
			array(
				'taxonomy' => RADIO_STATION_LANGUAGES_SLUG,
				'operator' => 'NOT EXISTS',
			),
		);
	} else {
		// --- get shows with specific language assigned ---
		$tax_query = array(
			array(
				'taxonomy' => RADIO_STATION_LANGUAGES_SLUG,
				'field'    => 'slug',
				'terms'    => $language,
			),
		);
	}

	$args = array(
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => 'publish',
		'tax_query'   => $tax_query,
		'meta_query'  => array(
			'relation' => 'AND',
			array(
				'key'     => 'show_sched',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => 'show_active',
				'value'   => 'on',
				'compare' => '=',
			),
		),
	);
	$args = apply_filters( 'radio_station_show_languages_query_args', $args, $language );
	$shows = new WP_Query( $args );

	return $shows;
}


// -------------------
// === Show Avatar ===
// -------------------

// ---------------
// Get Image Sizes
// ---------------
// 2.5.0: added for widget field dropdowns
function radio_station_get_image_sizes() {

	// --- get image size names ---
	$image_sizes = array(
		'thumbnail' => __( 'Thumbnail' ),
		'medium'    => __( 'Medium' ),
		'large'     => __( 'Large' ),
		'full'      => __( 'Full Size' ),
	);
	$image_sizes = apply_filters( 'image_size_names_choose', $image_sizes );
	if ( isset( $image_sizes['full'] ) ) {
		unset( $image_sizes['full'] );
	}

	/// --- get image size dimensions ---
	$wp_additional_image_sizes = wp_get_additional_image_sizes();
	$get_intermediate_image_sizes = get_intermediate_image_sizes();
	foreach ( $get_intermediate_image_sizes as $size ) {
		if ( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
			$sizes[$size]['width'] = get_option( $size . '_size_w' );
			$sizes[$size]['height'] = get_option( $size . '_size_h' );
			// $sizes[$size]['crop'] = (bool) get_option( $size . '_crop' );
		} elseif ( isset( $wp_additional_image_sizes[$size] ) ) {
			$sizes[$size] = array(
				'width' => $wp_additional_image_sizes[$size]['width'],
				'height' => $wp_additional_image_sizes[$size]['height'],
				// 'crop' =>  $wp_additional_image_sizes[$size]['crop'],
			);
		}
	}

	// --- loop size names to add dimensions ---
	foreach ( $image_sizes as $image_size => $label ) {
		if ( isset( $sizes[$image_size]['width'] ) && isset( $sizes[$image_size]['height'] ) ) {
			$image_sizes[$image_size] = $label . ' (' . $sizes[$image_size]['width'] . 'x' . $sizes[$image_size]['height'] . ')';
		}
	}
	return $image_sizes;
}

// ------------------
// Update Show Avatar
// ------------------
// 2.3.0: trigger show avatar check/update when editing
add_action( 'replace_editor', 'radio_station_update_show_avatar', 10, 2 );
function radio_station_update_show_avatar( $replace_editor, $post ) {

	// 2.3.3.9: fix to only apply to image-specific post types
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG );
	$post_types = apply_filters( 'radio_station_show_avatar_post_types', $post_types );
	if ( in_array( $post->post_type, $post_types ) ) {
		$show_id = $post->ID;
		radio_station_get_show_avatar_id( $show_id );
	} else {
		// 2.3.3.9: add cleanup for non-intended post types
		delete_post_meta( $post->ID, 'show_avatar' );
		delete_post_meta( $post->ID, '_rs_image_updated' );
	}

	return $replace_editor;
}

// ------------------
// Get Show Avatar ID
// ------------------
// 2.3.0: added get show avatar ID with thumbnail update
// note: existing thumbnail (featured image) ID is duplicated to the show avatar ID,
// allowing for handling of Show Avatars and Featured Images separately.
function radio_station_get_show_avatar_id( $show_id ) {

	// --- get thumbnail and avatar ID ---
	$avatar_id = get_post_meta( $show_id, 'show_avatar', true );

	// --- check thumbnail to avatar updated switch ---
	$updated = get_post_meta( $show_id, '_rs_image_updated', true );
	if ( !$updated ) {
		if ( !$avatar_id ) {
			$thumbnail_id = get_post_meta( $show_id, '_thumbnail_id', true );
			if ( $thumbnail_id ) {
				// --- duplicate the existing thumbnail to avatar meta ---
				$avatar_id = $thumbnail_id;
				add_post_meta( $show_id, 'show_avatar', $avatar_id );
			}
		}
		// --- add a flag indicating image has been updated ---
		add_post_meta( $show_id, '_rs_image_updated', true );
	}

	// --- filter and return ---
	$avatar_id = apply_filters( 'radio_station_show_avatar_id', $avatar_id, $show_id );

	return $avatar_id;
}

// -------------------
// Get Show Avatar URL
// -------------------
// 2.3.0: added to get the show avatar URL
function radio_station_get_show_avatar_url( $show_id, $size = 'thumbnail' ) {

	// --- get avatar ID ---
	$avatar_id = radio_station_get_show_avatar_id( $show_id );

	// --- get the attachment image source ---
	$avatar_url = false;
	if ( $avatar_id ) {
		// 2.4.0.6: added show avatar size filter
		$size = apply_filters( 'radio_station_show_avatar_size', $size );
		$avatar_src = wp_get_attachment_image_src( $avatar_id, $size );
		// 2.5.10: handle failure to get image source
		if ( $avatar_src && is_array( $avatar_src ) && isset( $avatar_src[0] ) ) {
			$avatar_url = $avatar_src[0];
		}
	}

	// --- filter and return ---
	// 2.4.0.6: added third argument for avatar size
	$avatar_url = apply_filters( 'radio_station_show_avatar_url', $avatar_url, $show_id, $size );
	return $avatar_url;
}

// ---------------
// Get Show Avatar
// ---------------
// 2.3.0: added this function for getting show avatar tag
function radio_station_get_show_avatar( $show_id, $size = 'thumbnail', $attr = array() ) {

	// --- get avatar ID ---
	$avatar_id = radio_station_get_show_avatar_id( $show_id );

	// --- get the attachment image tag ---
	$avatar = false;
	if ( $avatar_id ) {
		// 2.4.0.6: added show avatar size filter
		$size = apply_filters( 'radio_station_show_avatar_size', $size );
		$avatar = wp_get_attachment_image( $avatar_id, $size, false, $attr );
	}

	// --- filter and return ---
	// 2.3.3.9: change conflicting (duplicate) filter name for show avatar
	// 2.4.0.6: added third argument for avatar size
	$avatar = apply_filters( 'radio_station_show_avatar_output', $avatar, $show_id, $size );
	return $avatar;
}


// ---------------------
// === URL Functions ===
// ---------------------

// -----------------
// Get Streaming URL
// -----------------
// 2.3.0: added get streaming URL helper
function radio_station_get_stream_url() {
	$streaming_url = '';
	$stream = radio_station_get_setting( 'streaming_url' );
	
	// commented out: this breaks player settings
	// if ( RADIO_STATION_DEBUG ) {
		// echo '<span style="display:none;">Stream URL Setting: ' . esc_html( $stream ) . '</span>';
	// }

	if ( $stream && ( '' != $stream ) ) {
		$streaming_url = $stream;
	}
	$streaming_url = apply_filters( 'radio_station_stream_url', $streaming_url );

	return $streaming_url;
}

// ----------------
// Get Fallback URL
// ----------------
// 2.3.3.9: added get fallback URL helper
function radio_station_get_fallback_url() {
	$fallback_url = '';
	$fallback = radio_station_get_setting( 'fallback_url' );
	if ( $fallback && ( '' != $fallback ) ) {
		$fallback_url = $fallback;
	}
	
	// commented out: this breaks player settings
	// if ( RADIO_STATION_DEBUG ) {
	// 	echo '<span style="display:none;">Fallback URL Setting: ' . esc_html( $fallback_url ) . '</span>';
	// }

	$fallback_url = apply_filters( 'radio_station_fallback_url', $fallback_url );

	return $fallback_url;
}

// ------------------
// Get Stream Formats
// ------------------
// 2.3.3.7: added streaming format options
function radio_station_get_stream_formats() {

	// TODO: recheck amplitude formats ?
	// [Amplitude] HTML5 Support - mp3, aac ...?
	// ref: https://en.wikipedia.org/wiki/HTML5_audio#Supporting_browsers
	// [Howler] mp3, opus, ogg, wav, aac, m4a, mp4, webm
	// +mpeg, oga, caf, weba, webm, dolby, flac
	// [JPlayer] Audio: mp3, m4a - Video: m4v
	// +Audio: webma, oga, wav, fla, rtmpa +Video: webmv, ogv, flv, rtmpv
	// [Media Elements] Audio: mp3, wma, wav +Video: mp4, ogg, webm, wmv

	$formats = array(
		'aac'   => 'AAC/M4A',	// A/H/J
		'mp3'   => 'MP3',		// A/H/J
		'ogg'   => 'OGG',		// H
		'oga'   => 'OGA',		// H/J
		'webm'  => 'WebM',		// H/J
		'rtmpa' => 'RTMPA',		// J
		'opus'  => 'OPUS',		// H
	);

	// --- filter and return ---
	$formats = apply_filters( 'radio_station_stream_formats', $formats );
	return $formats;
}

// ---------------
// Get Station URL
// ---------------
function radio_station_get_station_url() {
	$station_url = '';
	$page_id = radio_station_get_setting( 'station_page' );
	if ( $page_id && ( '' != $page_id ) ) {
		$station_url = get_permalink( $page_id );
	}
	$station_url = apply_filters( 'radio_station_station_url', $station_url );

	return $station_url;
}

// ---------------------
// Get Station Image URL
// ---------------------
// 2.3.3.8: added get station logo image URL
function radio_station_get_station_image_url() {
	$station_image = '';
	$attachment_id = radio_station_get_setting( 'station_image' );
	$image = wp_get_attachment_image_src( $attachment_id, 'full' );
	if ( is_array( $image ) ) {
		$station_image = $image[0];
	}
	$station_image = apply_filters( 'radio_station_station_image_url', $station_image );

	return $station_image;
}

// ----------------------------
// Get Master Schedule Page URL
// ----------------------------
// 2.3.0: added get master schedule URL permalink
function radio_station_get_schedule_url() {
	$schedule_url = '';
	$page_id = radio_station_get_setting( 'schedule_page' );
	if ( $page_id && ( '' != $page_id ) ) {
		$schedule_url = get_permalink( $page_id );
	}
	$schedule_url = apply_filters( 'radio_station_schedule_url', $schedule_url );

	return $schedule_url;
}

// -------------------------
// Get Radio Station API URL
// -------------------------
function radio_station_get_api_url() {
	$routes = radio_station_get_setting( 'enable_data_routes' );
	$feeds = radio_station_get_setting( 'enable_data_feeds' );
	$rest_url = get_rest_url( null, '/' );
	$api_url = false;
	if ( ( 'yes' == $routes ) && !empty( $rest_url ) ) {
		$api_url = radio_station_get_route_url( '' );
	} elseif ( 'yes' == $feeds ) {
		$api_url = radio_station_get_feed_url( 'radio' );
	}
	$api_url = apply_filters( 'radio_station_api_url', $api_url );

	return $api_url;
}

// -------------
// Get Route URL
// -------------
function radio_station_get_route_url( $route ) {

	global $radio_station_routes;

	// --- maybe return cached route URL ---
	if ( isset( $radio_station_routes[$route] ) ) {
		return $radio_station_routes[$route];
	}

	/// --- get route URL ---
	$base = apply_filters( 'radio_station_route_slug_base', 'radio' );
	if ( '' != $route ) {
		$route = apply_filters( 'radio_station_route_slug_' . $route, $route );
	}
	if ( '' == $route ) {
		$path = '/' . $base . '/';
	} elseif ( !$route ) {
		return false;
	} else {
		$path = '/' . $base . '/' . $route . '/';
	}

	// --- cache route URL ---
	// echo "<!-- Route: " . $route . " - Path: " . $path . " -->";
	$radio_station_routes[$route] = $route_url = get_rest_url( null, $path );

	return $route_url;
}

// ------------
// Get Feed URL
// ------------
function radio_station_get_feed_url( $feedname ) {

	global $radio_station_feeds;

	// --- maybe return cached feed URL ---
	if ( isset( $radio_station_feeds[$feedname] ) ) {
		return $radio_station_feeds[$feedname];
	}

	// --- get feed URL ---
	$feedname = apply_filters( 'radio_station_feed_slug_' . $feedname, $feedname );
	if ( !$feedname ) {
		return false;
	}

	// --- cache feed URL ---
	$radio_station_feeds[$feedname] = $feed_url = get_feed_link( $feedname );

	return $feed_url;
}

// ----------------
// Get Show RSS URL
// ----------------
function radio_station_get_show_rss_url( $show_id ) {
	// TODO: combine comments and full show content
	$rss_url = get_post_comments_feed_link( $show_id );
	$rss_url = add_query_arg( 'withoutcomments', '1', $rss_url );

	return $rss_url;
}

// -------------------------
// Get DJ / Host Profile URL
// -------------------------
// 2.3.0: added to get DJ / Host author/profile permalink
// 2.3.3.9: moved get possible profile ID to Pro filter
function radio_station_get_host_url( $host_id ) {
	$host_url = get_author_posts_url( $host_id );
	$host_url = apply_filters( 'radio_station_host_url', $host_url, $host_id );
	return $host_url;
}

// ------------------------------
// Get DJ / Host Profile Edit URL
// ------------------------------
// 2.5.9: added get host edit URL function
function radio_station_get_host_edit_url( $host_id ) {
	$host_edit_url = add_query_arg( 'user_id', $host_id, admin_url( 'user-edit.php' ) );
	$host_edit_url = apply_filters( 'radio_station_host_edit_url', $host_edit_url, $host_id );
	return $host_edit_url;
}

// ------------------------
// Get Producer Profile URL
// ------------------------
// 2.3.0: added to get Producer author/profile permalink
// 2.3.3.9: moved get possible profile ID to Pro filter
function radio_station_get_producer_url( $producer_id ) {
	$producer_url = get_author_posts_url( $producer_id );
	$producer_url = apply_filters( 'radio_station_producer_url', $producer_url, $producer_id );
	return $producer_url;
}

// -----------------------------
// Get Producer Profile Edit URL
// -----------------------------
// 2.5.9: added get producer edit URL function
function radio_station_get_producer_edit_url( $producer_id ) {
	$producer_edit_url = add_query_arg( 'user_id', $producer_id, admin_url( 'user-edit.php' ) );
	$producer_edit_url = apply_filters( 'radio_station_producer_edit_url', $producer_edit_url, $producer_id );
	return $producer_edit_url;
}

// ---------------
// Get Upgrade URL
// ---------------
// 2.3.0: added to get Upgrade to Pro link
function radio_station_get_upgrade_url() {
	$upgrade_url = add_query_arg( 'page', 'radio-station-pricing', admin_url( 'admin.php' ) );
	return $upgrade_url;
}

// ---------------
// Get Pricing URL
// ---------------
// 2.5.0: added to get link to Pricing page
function radio_station_get_pricing_url() {
	$pricing_url = RADIO_STATION_PRO_URL . 'pricing/';
	return $pricing_url;
}

// ------------------------
// Patreon Supporter Button
// ------------------------
// 2.2.2: added simple patreon supporter image button
// 2.3.0: added Patreon page argument
// 2.3.0: moved from radio-station-admin.php
function radio_station_patreon_button( $page, $title = '' ) {
	$image_url = plugins_url( 'images/patreon-button.jpg', RADIO_STATION_FILE );
	$button = '<a href="https://patreon.com/' . esc_attr( $page ) . '" target="_blank" title="' . esc_attr( $title ) . '">';
	$button .= '<img id="radio-station-patreon-button" src="' . esc_url( $image_url ) . '" border="0">';
	$button .= '</a>';

	// 2.3.0: add button styling to footer
	if ( is_admin() ) {
		add_action( 'admin_footer', 'radio_station_patreon_button_styles' );
	} else {
		add_action( 'wp_footer', 'radio_station_patreon_button_styles' );
	}

	// --- filter and return ---
	$button = apply_filters( 'radio_station_patreon_button', $button, $page );
	return $button;
}

// ---------------------
// Patreon Button Styles
// ---------------------
// 2.3.0: added separately in footer
function radio_station_patreon_button_styles() {
	// 2.2.7: added button hover opacity
	echo '<style>#radio-station-patreon-button {opacity:0.9;}
	#radio-station-patreon-button:hover {opacity:1 !important;}</style>';
}

// --------------------
// Queue Directory Ping
// --------------------
// 2.3.2: queue directory ping on saving
function radio_station_queue_directory_ping() {
	// 2.3.3.9: fix to bug out during plugin activation
	if ( !function_exists( 'radio_station_get_setting' ) ) {
		return;
	}
	$queue_ping = radio_station_get_setting( 'ping_netmix_directory' );
	if ( 'yes' == $queue_ping ) {
		update_option( 'radio_station_ping_directory', '1' );
	}
}

// -------------------
// Send Directory Ping
// -------------------
// 2.3.1: added directory ping function prototype
function radio_station_send_directory_ping() {

	$do_ping = radio_station_get_setting( 'ping_netmix_directory' );
	if ( 'yes' != $do_ping ) {
		return false;
	}

	// --- set the URL to ping ---
	// 2.3.2: fix url_encode to urlencode
	// 2.5.6: use rawurlencode instead of urlencode
	$site_url = site_url();
	$url = add_query_arg( 'ping', 'directory', RADIO_STATION_NETMIX_DIR );
	$url = add_query_arg( 'station-url', rawurlencode( $site_url ), $url );
	$url = add_query_arg( 'timestamp', time(), $url );

	// --- send the ping ---
	$args = array( 'timeout' => 10 );
	if ( !function_exists( 'wp_remote_get' ) ) {
		include_once ABSPATH . WPINC . '/http.php';
	}
	$response = wp_remote_get( $url, $args );
	if ( isset( $_GET['rs-test-ping'] ) && ( '1' === sanitize_text_field( $_GET['rs-test-ping'] ) ) ) {
		echo '<span style="display:none;">Directory Ping Response:</span>';
		echo '<textarea style="display:none; float:right; width:700px; height:200px;">';
		echo esC_html( print_r( $response, true ) ) . '</textarea>';
	}
	return $response;
}

// -------------------
// Check and Send Ping
// -------------------
// 2.3.2: send queued directory ping
add_action( 'admin_footer', 'radio_station_check_directory_ping', 99 );
function radio_station_check_directory_ping() {
	$ping = get_option( 'radio_station_ping_directory' );
	if ( $ping ) {
		$response = radio_station_send_directory_ping();
		if ( !is_wp_error( $response ) && isset( $response['response']['code'] ) && ( 200 == $response['response']['code'] ) ) {
			delete_option( 'radio_station_ping_directory' );
		}
	} elseif ( isset( $_GET['rs-test-ping'] ) && ( '1' === sanitize_text_field( $_GET['rs-test-ping'] ) ) ) {
		$response = radio_station_send_directory_ping();
	}
}


// ------------------------
// === Helper Functions ===
// ------------------------

// ---------------
// Get Icon Colors
// ---------------
// 2.3.3.9: moved out from single-show-content.php template
function radio_station_get_icon_colors( $context = false ) {
	$icon_colors = array(
		'website'  => '#A44B73',
		'email'    => '#0086CC',
		'phone'    => '#008000',
		'download' => '#7DBB00',
		'rss'      => '#FF6E01',
	);
	$icon_colors = apply_filters( 'radio_station_icon_colors', $icon_colors, $context );
	return $icon_colors;
}

// --------------------
// Encode URI Component
// --------------------
// 2.3.2: added PHP equivalent of javascript encodeURIComponent
// ref: https://stackoverflow.com/a/1734255/5240159
function radio_station_encode_uri_component( $component ) {
	$revert = array( '%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')' );
    return strtr( rawurlencode( $component ), $revert );
}

// ------------------
// Get Language Terms
// ------------------
// 2.3.3.9: added for language archive shortcode
function radio_station_get_language_terms( $args = false ) {

	$defaults = array(
		'taxonomy'   => RADIO_STATION_LANGUAGES_SLUG,
		'orderby'    => 'name',
		'hide_empty' => true
	);
	if ( $args && is_array( $args ) ) {
		foreach ( $args as $key => $value ) {
			$defaults[$key] = $value;
		}
	}
	$terms = get_terms( $defaults );
	$languages = array();
	if ( $terms ) {
		foreach ( $terms as $term ) {
			$languages[$term->name] = array(
				'id'            => $term->term_id,
				'name'          => $term->name,
				'slug'          => $term->slug,
				'description'   => $term->description,
				'url'           => get_term_link( $term, RADIO_STATION_LANGUAGES_SLUG ),
			);
		}
	}

	// --- filter and return ---
	$languages = apply_filters( 'radio_station_get_language_terms', $languages, $args );

	return $languages;
}

// -------------
// Get Languages
// -------------
function radio_station_get_languages() {

	// --- get all language translations ---
	$translations = get_site_transient( 'available_translations' );
	if ( ( false === $translations ) || is_wp_error( $translations )
		|| !isset( $translations['translations'] ) || empty( $translations['translations'] ) ) {
		// --- fallback to language selection data ---
		// (note this file is a minified from translations API result)
		// http://api.wordpress.org/translations/core/1.0/
		$language_file = RADIO_STATION_DIR . '/languages/languages.json';
		if ( file_exists( $language_file ) ) {
			$contents = file_get_contents( $language_file );
			$translations = json_decode( $contents, true );
		}
	}
	if ( isset( $translations['translations'] ) ) {
		$translations = $translations['translations'];
	}

	// --- merge in default language (en_US) ---
	if ( is_array( $translations ) && ( count( $translations ) > 0 ) ) {
		$trans_before = $trans_after = array();
		$found = false;
		foreach ( $translations as $i => $translation ) {
			if ( '' == $translation['language'] ) {
				$found = true;
			}
			if ( !$found ) {
				$trans_before[] = $translation;
			} else {
				$trans_after[] = $translation;
			}
		}
		$trans_before[] = array(
			'language'     => 'en_US',
			'native_name'  => 'English (United States)',
			'english_name' => 'English (United States)',
		);
		$translations = array_merge( $trans_before, $trans_after );
	}

	// --- filter and return ---
	$translations = apply_filters( 'radio_station_get_languages', $translations );

	return $translations;
}

// --------------------
// Get Language Options
// --------------------
function radio_station_get_language_options( $include_wp_default = false ) {

	// --- maybe get stored timezone options ---
	$languages = get_transient( 'radio-station-language-options' );
	if ( !$languages ) {
		$languages = array();
		$translations = radio_station_get_languages();
		if ( $translations && is_array( $translations ) && ( count( $translations ) > 0 ) ) {
			foreach ( $translations as $translation ) {
				$lang = $translation['language'];
				$languages[$lang] = $translation['native_name'];
			}
		}
		// set_transient( 'radio-station-language-options', $languages, 24 * 60 * 60 );
	}

	// --- maybe include WordPress default language ---
	if ( $include_wp_default ) {
		// 2.3.3.6: fix to array for WordPress language setting
		$wp_language = array( '' => __( 'WordPress Setting', 'radio-station' ) );
		$languages = array_merge( $wp_language, $languages );
	}

	// --- filter and return ---
	$languages = apply_filters( 'radio_station_get_language_options', $languages, $include_wp_default );

	return $languages;
}

// ------------
// Get Language
// ------------
function radio_station_get_language( $lang = false ) {

	// --- maybe get the main language ---
	$main = false;
	if ( !$lang ) {
		$main = true;
		$lang = radio_station_get_setting( 'radio_language' );
		// 2.3.3.6: add fallback for value of 1 due to language options bug
		if ( !$lang || ( '' == $lang ) || ( '0' == $lang ) || ( '1' == $lang ) ) {
			$lang = get_option( 'WPLANG' );
			if ( !$lang ) {
				$lang = 'en_US';
			}
		}
	}
	if ( isset( $_REQUEST['lang-debug'] ) && ( '1' === sanitize_text_field( $_REQUEST['lang-debug'] ) ) ) {
		echo PHP_EOL . "LANG: " . esc_html( print_r( $lang, true ) ) . PHP_EOL;
	}

	// --- get the specified language term ---
	// 2.3.3.8: explicitly check for numberic language term ID
	// 2.5.10: fix to check for numeric digit
	if ( !ctype_digit( $lang ) ) {
		$term = get_term_by( 'slug', $lang, RADIO_STATION_LANGUAGES_SLUG );
		if ( !$term ) {
			$term = get_term_by( 'name', $lang, RADIO_STATION_LANGUAGES_SLUG );
		}
	} else {
		$id = absint( $lang );
		if ( $id < 1 ) {
			$term = get_term_by( 'id', $lang, RADIO_STATION_LANGUAGES_SLUG );
		}
	}

	// --- set language from term ---
	if ( $term ) {
		$language = array(
			'id'          => $term->term_id,
			'slug'        => $term->slug,
			'name'        => $term->name,
			'description' => $term->description,
			'url'         => get_term_link( $term, RADIO_STATION_LANGUAGES_SLUG ),
		);
	} else {
		// --- set main language info ---
		if ( $main ) {
			$languages = radio_station_get_languages();
			foreach ( $languages as $i => $lang_data ) {
				if ( $lang_data['language'] == $lang ) {
					$language = array(
						'id'          => 0,
						'slug'        => $lang,
						'name'        => $lang_data['native_name'],
						'description' => $lang_data['english_name'],
						// TODO: set URL for main language and filter archive page results ?
						// 'url'      => '',
					);
				}
			}
		} else {
			$language = false;
		}
	}
	if ( isset( $_REQUEST['lang-debug'] ) && ( '1' === sanitize_text_field( $_REQUEST['lang-debug'] ) ) ) {
		echo 'LANGUAGE: ' . esc_html( print_r( $language, true ) ) . "\n";
	}

	return $language;
}

// ------------
// Trim Excerpt
// ------------
// (modified copy of wp_trim_excerpt)
// 2.3.0: added permalink argument
function radio_station_trim_excerpt( $content, $length = false, $more = false, $permalink = false ) {

	$excerpt = '';
	$raw_content = $content;

	// 2.3.2: added check for content
	if ( '' != trim( $content ) ) {

		$content = strip_shortcodes( $content );

		// TODO: check for Gutenberg plugin-only equivalent ?
		// if ( function_exists( 'gutenberg_remove_blocks' ) {
		//	$content = gutenberg_remove_blocks( $content );
		// } elseif ( function_exists( 'excerpt_remove_blocks' ) ) {
		if ( function_exists( 'excerpt_remove_blocks' ) ) {
			$content = excerpt_remove_blocks( $content );
		}

		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );

		if ( !$length ) {
			$length = 35;
			$length = (int) apply_filters( 'radio_station_excerpt_length', $length );
		}
		if ( !$more ) {
			// $more = ' [&hellip;]';
			// $more = apply_filters( 'excerpt_more', $more);
			$more = apply_filters( 'radio_station_excerpt_more', ' [&hellip;]' );
		}
		// 2.3.0: added link wrapper
		if ( $permalink ) {
			// 2.5.0: add esc_html to more anchor
			$more = ' <a href="' . esc_url( $permalink ) . '">' . esc_html( $more ) . '</a>';
		}
		$excerpt = wp_trim_words( $content, $length, $more );
	}

	$excerpt = apply_filters( 'radio_station_trim_excerpt', $excerpt, $raw_content, $length, $more, $permalink );
	return $excerpt;
}

// ---------------
// Sanitize Values
// ---------------
function radio_station_sanitize_values( $data, $keys ) {

	$sanitized = array();
	foreach ( $keys as $key => $type ) {
		if ( isset( $data[$key] ) ) {
			if ( 'boolean' == $type ) {
				if ( ( 0 == $data[$key] ) || ( 1 == $data[$key] ) ) {
					$sanitized[$key] = $data[$key];
				}
			} elseif ( 'integer' == $type ) {
				$sanitized[$key] = absint( $data[$key] );
			} elseif ( 'alphanumeric' == $type ) {
				$value = preg_match( '/^[a-zA-Z0-9_]+$/', $data[$key] );
				if ( $value ) {
					$sanitized[$key] = $value;
				}
			} elseif ( 'text' == $type ) {
				$sanitized[$key] = sanitize_text_field( $data[$key] );
			} elseif ( 'slug' == $type ) {
				$sanitized[$key] = sanitize_title( $data[$key] );
			}
		}
	}

	// echo 'Sanitize Keys: '; print_r( $keys );
	// echo 'Sanitize Data: '; print_r( $data );
	// echo 'Sanitized: '; print_r( $sanitized );
	// 2.5.10: add filter for sanitized values (in case fixes needed)
	$sanitized = apply_filters( 'radio_station_sanitized_values', $sanitized, $data, $keys );
	return $sanitized;
}

// --------------------
// Sanitize Input Value
// --------------------
// 2.3.3.9: added for combined show/override input saving
function radio_station_sanitize_input( $prefix, $key ) {

	$postkey = $prefix . '_' . $key;
	$types = radio_station_get_meta_input_types();

	// 2.4.0.3: bug out if post key not set
	// 2.5.0: set empty value as default
	// 2.5.6: put POST directly in sanitize functions
	$value = '';
	if ( isset( $_POST[$postkey] ) ) {

		if ( in_array( $key, $types['file'] ) ) {
			$value = wp_strip_all_tags( trim( $_POST[$postkey] ) );
		} elseif ( in_array( $key, $types['email'] ) ) {
			$value = sanitize_email( trim( $_POST[$postkey] ) );
		} elseif ( in_array( $key, $types['url'] ) ) {
			$value = filter_var( trim( $_POST[$postkey] ), FILTER_SANITIZE_URL );
		} elseif ( in_array( $key, $types['slug'] ) ) {
			$value = sanitize_title( $_POST[$postkey] );
		} elseif ( in_array( $key, $types['phone'] ) ) {
			// 2.3.3.6: added phone number with character filter validation
			$value = trim( sanitize_text_field( $_POST[$postkey] ) );
			if ( strlen( $value ) > 0 ) {
				$value = str_split( $value, 1 );
				$value = preg_filter( '/^[0-9+\(\)#\.\s\-]+$/', '$0', $value );
				if ( count( $value ) > 0 ) {
					$value = implode( '', $value );
				} else {
					$value = '';
				}
			}
		} elseif ( in_array( $key, $types['numeric'] ) ) {

			$value = absint( $_POST[$postkey] );
			if ( $value < 0 ) {
				$value = '';
			}

		} elseif ( in_array( $key, $types['checkbox'] ) ) {

			// --- checkbox inputs ---
			// 2.2.8: removed strict in_array checking
			// 2.3.2: fix for unchecked boxes index warning
			$value = sanitize_text_field( $_POST[$postkey] );
			if ( !in_array( $value, array( '', 'on', 'yes' ) ) ) {
				$value = '';
			}

		} elseif ( in_array( $key, $types['user'] ) ) {

			// --- user selection inputs ---
			// 2.5.6: use array_map on posted value
			$value = array_map( 'absint', $_POST[$postkey] );
			if ( !isset( $value ) || !is_array( $value ) ) {
				$value = array();
			} else {
				foreach ( $value as $i => $userid ) {
					if ( ! empty( $userid ) ) {
						$user = get_user_by( 'ID', $userid );
						if ( !$user ) {
							unset( $value[ $i ] );
						}
					}
				}
			}

		} elseif ( in_array( $key, $types['date'] ) ) {

			// --- datepicker date field ---
			$date  = sanitize_text_field( $_POST[$postkey] );
			$parts = explode( '-', $date );
			if ( 3 == count( $parts ) ) {
				if ( checkdate( (int) $parts[1], (int) $parts[2], (int) $parts[0] ) ) {
					$value = $date;
				}
			}

		} elseif ( in_array( $key, $types['hour'] ) ) {

			// --- hours (24) ---
			$value = absint( $_POST[$postkey] );
			if ( ( $value < 0 ) || ( $value > 23 ) ) {
				$value = '00';
			} elseif ( $value < 10 ) {
				$value = '0' . $value;
			} else {
				$value = (string) $value;
			}

		} elseif ( in_array( $key, $types['mins'] ) ) {

			// --- minutes (or seconds) ---
			$value = absint( $_POST[$postkey] );
			if ( ( $value < 0 ) || ( $value > 60 ) ) {
				$value = '00';
			} elseif ( $value < 10 ) {
				$value = '0' . $value;
			} else {
				$value = (string) $value;
			}

		} elseif ( in_array( $key, $types['meridiem'] ) ) {

			// --- meridiems ---
			$valid = array( '', 'am', 'pm' );
			$value = sanitize_text_field( $_POST[$postkey] );
			if ( !in_array( $value, $valid ) ) {
				$value = '';
			}

		}
	}

	return $value;
}

// --------------------
// Get Meta Input Types
// --------------------
// 2.3.3.9: added for meta input type mapping
function radio_station_get_meta_input_types() {

	$types = array(
		'numeric'  => array( 'avatar', 'image', 'number' ),
		'checkbox' => array( 'active', 'download' ),
		'user'     => array( 'user_list', 'producer_list' ),
		'file'     => array( 'file' ),
		'email'    => array( 'email' ),
		'url'      => array( 'link', 'url' ),
		'slug'     => array( 'slug', 'patreon' ),
		'phone'    => array( 'phone' ),
		'date'     => array( 'date' ),
		'hour'     => array( 'hour' ),
		'mins'     => array( 'mins', 'minutes', 'secs', 'seconds' ),
		'meridiem' => array( 'meridian', 'meridiem' ),
	);

	// --- filter and return ---
	$types = apply_filters( 'radio_station_meta_input_types', $types );
	return $types;
}

// -----------------------
// Sanitize Playlist Entry
// -----------------------
// 2.3.3.9: added for entry validation
function radio_station_sanitize_playlist_entry( $entry ) {

	// --- set playlist entry keys ---
	$entry_keys = array(
		'playlist_entry_artist',
		'playlist_entry_song',
		'playlist_entry_album',
		'playlist_entry_label',
		'playlist_entry_minutes',
		'playlist_entry_seconds',
		'playlist_entry_comments',
		'playlist_entry_new',
		'playlist_entry_status',
	);
	$text_keys = array( 'artist', 'song', 'album', 'label', 'comments' );
	$numeric_keys = array( 'minutes', 'seconds' );
	foreach ( $entry_keys as $entry_key ) {
		if ( isset( $entry[$entry_key] ) ) {
			$value = $entry[$entry_key];
			$key = str_replace( 'playlist_entry_', '', $entry_key );
			if ( in_array( $key, $text_keys ) ) {
				$value = sanitize_text_field( $value );
			} elseif ( in_array( $key, $numeric_keys ) ) {
				$value = absint( $value );
				// 2.5.6: set non-numeric values to blank
				if ( $value < 0 ) {
					$value = '';
				}
				if ( ( 'seconds' == $key ) && ( $value < 10 ) ) {
					// pad seconds with zero prefix ?
				}
			} elseif ( 'status' == $key ) {
				if ( $value != 'played' ) {
					$value = 'queued';
				}
			}
			$entry[$entry_key] = $value;
		}
	}

	return $entry;
}


// -------------------------
// Sanitize Shortcode Values
// -------------------------
// 2.3.2: added for AJAX widget loading
// 2.5.0: updated to match changed shortcode keys
function radio_station_sanitize_shortcode_values( $type, $extras = false ) {

	// $atts = array();
	if ( 'current-show' == $type ) {

		// --- current show attribute keys ---
		// 2.3.3: added for_time value
		// 2.5.0: default_name to no_shows key, time to time_format key
		// 2.5.0: added hide_empty, avatar_size and block keys
		$keys = array(

			// --- general options ---
			'title'          => 'text',
			'ajax'           => 'boolean',
			'dynamic'        => 'boolean',
			'no_shows'       => 'text',
			'hide_empty'     => 'boolean',
			// --- show display options ---
			'show_link'      => 'boolean',
			'title_position' => 'slug',
			'show_avatar'    => 'boolean',
			'avatar_size'    => 'slug',
			'avatar_width'   => 'integer',
			// --- show time display options ---
			'show_sched'     => 'boolean',
			'show_all_sched' => 'boolean',
			'countdown'      => 'boolean',
			'time_format'    => 'integer',
			// --- extra display options ---
			'display_hosts'  => 'boolean',
			'link_hosts'     => 'boolean',
			// 'display_producers' => 'boolean',
			// 'link_producers' => 'boolean',
			'show_desc'      => 'boolean',
			'show_playlist'  => 'boolean',
			'show_encore'    => 'boolean',
			// --- shortcode data ---
			'widget'         => 'boolean',
			'block'          => 'boolean',
			'id'             => 'integer',
			'for_time'       => 'integer',
			'instance'       => 'integer',
		);

	} elseif ( 'upcoming-shows' == $type ) {

		// --- upcoming shows attribute keys ---
		// 2.3.3: added for_time value
		// 2.5.0: added hide_empty, avatar_size and block keys
		// 2.5.0: changed default_name to no_shows, time to time_format keys
		$keys = array(
			// --- general options ---
			'title'          => 'text',
			'limit'          => 'integer',
			'ajax'           => 'boolean',
			'dynamic'        => 'boolean',
			'no_shows'       => 'string',
			'hide_empty'     => 'boolean',
			// --- show display options ---
			'show_link'      => 'boolean',
			'title_position' => 'slug',
			'show_avatar'    => 'boolean',
			'avatar_size'    => 'slug',
			'avatar_width'   => 'integer',
			// --- show timed display options ---
			'show_sched'     => 'boolean',
			'countdown'      => 'boolean',
			'time_format'    => 'integer',
			// --- extra display options ---
			'display_hosts'  => 'boolean',
			'link_hosts'     => 'boolean',
			// 'display_producers' => 'boolean',
			// 'link_producers' => 'boolean',
			'show_encore'    => 'boolean',
			// --- shortcode data ---
			'widget'         => 'boolean',
			'block'          => 'boolean',
			'id'             => 'integer',
			'for_time'       => 'integer',
			'instance'       => 'integer',
		);

	} elseif ( 'current-playlist' == $type ) {

		// --- current playlist attribute keys ---
		// 2.3.3: added for_time value
		// 2.5.0: added hide_empty, no_playlist, playlist_title, block
		$keys = array(
			// --- general options ---
			'title'          => 'text',
			'dynamic'        => 'boolean',
			'ajax'           => 'boolean',
			'hide_empty'     => 'boolean',
			// --- playlist display options ---
			'playlist_title' => 'boolean',
			'link'           => 'boolean',
			'no_playlist'    => 'text',
			'countdown'      => 'boolean',
			// --- track display options ---
			'artist'         => 'boolean',
			'song'           => 'boolean',
			'album'          => 'boolean',
			'label'          => 'boolean',
			'comments'       => 'boolean',
			// --- shortcode data ---
			'widget'         => 'boolean',
			'block'          => 'boolean',
			'id'             => 'integer',
			'for_time'       => 'integer',
			'instance'       => 'integer',
		);

	} elseif ( 'master-schedule' == $type ) {

		// --- master schedule attribute keys ---
		// 2.3.3.9: added for AJAX schedule loading
		// 2.5.0: added active_date,
		$keys = array(

			// --- schedule display options ---
			'view'              => 'text',
			'days'              => 'text',
			'start_day'         => 'text',
			'start_date'        => 'text',
			'active_date'       => 'text',
			'display_day'       => 'text',
			'display_date'      => 'text',
			'display_month'     => 'text',
			'time_format'       => 'text',

			// --- show display options ---
			'show_times'        => 'boolean',
			'show_link'         => 'boolean',
			'show_image'        => 'boolean',
			'show_desc'         => 'boolean',
			'show_hosts'        => 'boolean',
			'link_hosts'        => 'boolean',
			'show_genres'       => 'boolean',
			'show_encore'       => 'boolean',
			'show_file'         => 'boolean',

			// --- extra display options ---
			'selector'          => 'boolean',
			'clock'             => 'boolean',
			'timezone'          => 'boolean',

			// --- view specific options ---
			'divheight'         => 'integer',
			'hide_past_shows'   => 'boolean',
			'image_position'    => 'text',
			'gridwidth'         => 'integer',
			'time_spaced'       => 'boolean',
			'weeks'             => 'integer',
			'previous_weeks'    => 'integer',

			// --- shortcode data ---
			'widget'			=> 'boolean',
			'block'             => 'boolean',
			'ajax'				=> 'boolean',
			// 2.5.10.1: fix instance type boolean to integer
			'instance'          => 'integer',
		);

	}

	// 2.5.0: added filter for shortcode attribute key types
	$keys = apply_filters( 'radio_station_shortcode_attribute_key_types', $keys, $type );

	// --- handle extra keys ---
	if ( $extras && is_array( $extras ) && ( count( $extras ) > 0 ) ) {
		$keys = array_merge( $keys, $extras );
	}

	// --- sanitize values by key type ---
	$atts = radio_station_sanitize_values( $_REQUEST, $keys );
	return $atts;
}

// -----------------
// KSES Allowed HTML
// -----------------
// 2.5.0: added for allowing custom wp_kses output
function radio_station_allowed_html( $type, $context = false ) {
	$allowed = wp_kses_allowed_html( 'post' );
	$allowed = apply_filters( 'radio_station_allowed_html', $allowed, $type, $context );
	return $allowed;
}

// ---------------------
// Link Tag Allowed HTML
// ---------------------
// 2.5.0: added for allowing link tag output for wp_kses
add_filter( 'radio_station_allowed_html', 'radio_station_link_tag_allowed_html', 10, 3 );
function radio_station_link_tag_allowed_html( $allowed, $type, $context ) {

	// 2.5.6: change type to context
	if ( 'link' != $context ) {
		return $allowed;
	}

	$allowed['link'] = array(
		'rel'            => array(),
		'href'           => array(),
		'hreflang'       => array(),
		'crossorigin'    => array(),
		'media'          => array(),
		'referrerpolicy' => array(),
		'sizes'          => array(),
		'title'          => array(),
		'type'           => array(),
	);

	return $allowed;
}

// -----------------------
// Anchor Tag Allowed HTML
// -----------------------
// 2.5.6: added for allowing extended anchor tag output for wp_kses
add_filter( 'radio_station_allowed_html', 'radio_station_anchor_tag_allowed_html', 10, 3 );
function radio_station_anchor_tag_allowed_html( $allowed, $type, $context ) {

	// 2.5.6: added docs context for documentation links
	if ( 'docs' != $context ) {
		return $allowed;
	}

	// 2.5.6: added data-href attribute for docs
	$allowed['a']['id'] = true;

	return $allowed;
}

// ----------------------------
// Settings Inputs Allowed HTML
// ----------------------------
// 2.5.0: added for admin settings inputs
add_filter( 'radio_station_allowed_html', 'radio_station_settings_allowed_html', 10, 3 );
function radio_station_settings_allowed_html( $allowed, $type, $context ) {

	if ( ( 'content' != $type ) || ( 'settings' != $context ) ) {
		return $allowed;
	}

	// --- input ---
	$allowed['input'] = array(
		'id'          => array(),
		'class'       => array(),
		'name'        => array(),
		'value'       => array(),
		'type'        => array(),
		'data'        => array(),
		'placeholder' => array(),
		'style'       => array(),
		'checked'     => array(),
		'onclick'     => array(),
	);

	// --- textarea ---
	$allowed['textarea'] = array(
		'id'          => array(),
		'class'       => array(),
		'name'        => array(),
		'value'       => array(),
		'type'        => array(),
		'placeholder' => array(),
		'style'       => array(),
	);

	// --- select ---
	$allowed['select'] = array(
		'id'          => array(),
		'class'       => array(),
		'name'        => array(),
		'value'       => array(),
		'type'        => array(),
		'multiselect' => array(),
		'style'       => array(),
		'onchange'    => array(),
	);

	// --- select option ---
	$allowed['option'] = array(
		'selected' => array(),
		'value'    => array(),
	);

	// --- option group ---
	$allowed['optgroup'] = array(
		'label' => array(),
	);

	// --- allow onclick on spans and divs ---
	$allowed['span']['onclick'] = array();
	$allowed['div']['onclick'] = array();

	return $allowed;
}

// --------------------------
// Player Widget Allowed HTML
// --------------------------
// 2.5.10: added allowed HTML for player widget
add_filter( 'radio_station_allowed_html', 'radio_station_widget_player_allowed_html', 10, 3 );
function radio_station_widget_player_allowed_html( $allowed, $type, $context ) {

	if ( ( 'widget' != $type ) || ( 'player' != $context ) ) {
		return $allowed;
	}

	// --- link ---
	$allowed['link'] = array(
		'rel'         => array(),
		'href'        => array(),
	);

	// --- button ---
	$allowed['button'] = array(
		'id'          => array(),
		'class'       => array(),
		'role'        => array(),
		'title'       => array(),
		'onclick'     => array(),
		'tabindex'    => array(),
		'aria-label'  => array(),
		'style'       => array(),
	);

	// --- input ---
	$allowed['input'] = array(
		'id'          => array(),
		'class'       => array(),
		'name'        => array(),
		'value'       => array(),
		'type'        => array(),
		'data'        => array(),
		'placeholder' => array(),
		'style'       => array(),
		'checked'     => array(),
		'onclick'     => array(),
		'max'         => array(),
		'min'         => array(),
		'aria-label'  => array(),
	);

	// --- select ---
	// 2.5.13: add select tag
	$allowed['select'] = array(
		'id'          => array(),
		'class'       => array(),
		'name'        => array(),
		'value'       => array(),
		'type'        => array(),
		'multiselect' => array(),
		'style'       => array(),
		'onchange'    => array(),
	);

	// --- styles ---
	$allowed['style'] = array();
	
	return $allowed;
}

