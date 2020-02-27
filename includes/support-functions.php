<?php

/*
* Support Functions for Radio Station plugin
* Author: Tony Hayes
* Since: 2.3.0
*/

// === Data Functions ===
// - Get Show
// - Get Shows
// - Get Show Shifts
// - Get Schedule Overrides
// - Get Show Data
// - Get Show Metadata
// - Get Override Metadata
// - Get Current Schedule
// - Get Current Show
// - Get Next Show
// - Get Next Shows
// - Get Blog Posts for Show
// - Get Playlists for Show
// - Get Genre
// - Get Genres
// - Get Shows for Genre
// - Get Shows for Language
// === Shift Checking ===
// - Schedule Conflict Checker
// - Show Shift Checker
// - New Shifts Checker
// - Validate Shift Time
// === Show Avatar ===
// - Get Show Avatar ID
// - Get Show Avatar URL
// - Get Show Avatar
// === URL Functions ===
// - Get Streaming URL
// - Get Master Schedule Page URL
// - Get Radio Station API URL
// - Get Route URL
// - Get Feed URL
// - Get DJ / Host Profile URL
// - Get Producer Profile URL
// - Get Upgrade URL
// - Patreon Supporter Button
// === Helper Functions ===
// - Get Profile ID
// - Get Timezones Options
// - Get Timezone Code
// - Get Languages
// - Get Language Options
// - Get Language
// - Get Next Day
// - Get Previous Day
// - Trim Excerpt
// - Shorten String
// - Sanitize Values
// === Translations ===
// - Translate Weekday
// - Translate Month
// - Translate Meridiem
// === Legacy Functions ===


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
			$query = $wpdb->prepare( $query, $show );
			$show_id = $wpdb->get_var( $query );
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
	$defaults = array(
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => 'publish',
		'numberposts' => - 1,
		'meta_query'  => array(
			'relation' => 'AND',
			array(
				'meta_key' => 'show_sched',
				'compare'  => 'EXISTS',
				// 'meta_value'	=> 's:',
				// 'compare'	=> 'LIKE',
			),
			array(
				'meta_key'   => 'show_active',
				'meta_value' => 'on',
				'compare'    => '=',
			),
		),
		'orderby'     => 'post_name',
		'order'       => 'ASC',
	);

	// --- overwrite defaults with any arguments passed ---
	if ( $args && is_array( $args ) && ( count( $args ) > 0 ) ) {
		foreach ( $args as $key => $value ) {
			$defaults[$key] = $value;
		}
	}

	// --- get and return shows ---
	$shows = get_posts( $defaults );
	$shows = apply_filters( 'radio_station_get_shows', $shows, $defaults );

	return $shows;
}

// ---------------
// Get Show Shifts
// ---------------
// 2.3.0: added get show shifts data grabber
function radio_station_get_show_shifts( $check_conflicts = true ) {

	// --- get all shows ---
	$errors = array();
	$shows = radio_station_get_shows();

	// --- debug point ---
	if ( RADIO_STATION_DEBUG ) {
		foreach ( $shows as $i => $show ) {
			$shows[$i]->post_content = '';
		}
		$debug = "Shows" . PHP_EOL . PHP_EOL . print_r( $shows, true );
		radio_station_debug( $debug );
	}

	// --- loop shows to get shifts ---
	$all_shifts = array();
	if ( count( $shows ) > 0 ) {
		foreach ( $shows as $show ) {
			$shifts = get_post_meta( $show->ID, 'show_sched', true );
			if ( $shifts ) {
				foreach ( $shifts as $shift ) {

					// --- make sure shift has sufficient info ---
					if ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) {
						$isdisabled = true;
					} else {
						$isdisabled = false;
					}
					$shift = radio_station_validate_shift( $shift );

					if ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) {

						// --- if it was not already disabled, add to shift errors ---
						if ( !$isdisabled ) {
							$errors[$show->ID][] = $shift;
						}

					} else {

						// --- shift is valid so continue checking ---
						$day = $shift['day'];
						$thisday = date( 'Y-m-d', strtotime( $day ) );
						$nextday = date( 'Y-m-d', ( strtotime( $thisday ) + ( 24 * 60 * 60 ) ) );
						$midnight = strtotime( $nextday . ' 12:00 am' );
						$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
						$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
						if ( '12:00 am' == $end ) {
							$end_time = strtotime( $nextday . ' 12:00 am' );
						} else {
							$end_time = strtotime( $thisday . ' ' . $end );
						}
						$start_time = strtotime( $thisday . ' ' . $start );
						if ( isset( $shift['encore'] ) && ( 'on' == $shift['encore'] ) ) {
							$encore = true;
						} else {
							$encore = false;
						}
						$updated = $show->post_modified_gmt;

						// --- check if show goes over midnight ---
						if ( ( $end_time > $start_time ) || ( $end_time == $midnight ) ) {

							// --- set the shift time as is ---
							$all_shifts[$day][$start_time . '.' . $show->ID] = array(
								'day'     => $day,
								'start'   => $start,
								'end'     => $end,
								'show'    => $show->ID,
								'encore'  => $encore,
								'split'   => false,
								'updated' => $updated,
								'shift'   => $shift,
							);
						} else {
							// --- split shift for this day ---
							$all_shifts[$day][$start_time . '.' . $show->ID] = array(
								'day'      => $day,
								'start'    => $start,
								'end'      => '11:59:59 pm', // midnight
								'show'     => $show->ID,
								'split'    => true,
								'encore'   => $encore,
								'updated'  => $updated,
								'shift'    => $shift,
								'real_end' => $end,
							);

							// --- split shift for next day ---
							$nextday = radio_station_get_next_day( $day );
							$all_shifts[$nextday][$midnight . '.' . $show->ID] = array(
								'day'        => $nextday,
								'start'      => '00:00 am', // midnight
								'end'        => $end,
								'show'       => $show->ID,
								'encore'     => $encore,
								'split'      => true,
								'updated'    => $updated,
								'shift'      => $shift,
								'real_start' => $start,
							);
						}
					}
				}
			}
		}
	}

	// --- maybe store any found shift errors ---
	if ( count( $errors ) > 0 ) {
		update_option( 'radio_station_shift_errors', $errors );
	} else {
		delete_option( 'radio_station_shift_errors' );
	}

	// --- debug point ---
	if ( RADIO_STATION_DEBUG ) {
		$debug = "Raw Shifts" . PHP_EOL . PHP_EOL . print_r( $all_shifts, true );
		$debug .= "Shift Errors" . PHP_EOL . PHP_EOL . print_r( $errors, true );
		radio_station_debug( $debug );
	}

	// --- sort by start time for each day ---
	// note: all_shifts keys are made unique by combining start time and show ID
	// which allows them to be both be sorted and then checked for conflicts
	if ( count( $all_shifts ) > 0 ) {
		foreach ( $all_shifts as $day => $shifts ) {
			ksort( $shifts );
			$all_shifts[$day] = $shifts;
		}
	}

	// --- debug point ---
	if ( RADIO_STATION_DEBUG ) {
		$debug = "Sorted Shifts" . PHP_EOL . PHP_EOL . print_r( $all_shifts, true );
		radio_station_debug( $debug );
	}

	// --- check shifts for conflicts ---
	if ( $check_conflicts ) {
		$all_shifts = radio_station_check_shifts( $all_shifts );
	} else {
		// --- return raw data for other shift conflict checking ---
		return $all_shifts;
	}

	// --- debug point ---
	if ( RADIO_STATION_DEBUG ) {
		$debug = "Conflict Checked Shifts" . PHP_EOL . PHP_EOL . print_r( $all_shifts, true );
		radio_station_debug( $debug );
	}

	// --- shuffle shift days so today is first day ---
	$today = date( 'l' );
	$day_shifts = array();
	for ( $i = 0; $i < 7; $i ++ ) {
		if ( 0 == $i ) {
			$day = $today;
		} else {
			$day = radio_station_get_next_day( $day );
		}
		if ( isset( $all_shifts[$day] ) ) {
			$day_shifts[$day] = $all_shifts[$day];
		}
	}

	// --- filter and return ---
	$day_shifts = apply_filters( 'radio_station_show_shifts', $day_shifts );

	return $day_shifts;
}

// ----------------------
// Get Schedule Overrides
// ----------------------
// 2.3.0: added get schedule overrides data grabber
function radio_station_get_overrides( $start_date = false, $end_date = false ) {

	// --- convert dates to times for checking
	if ( $start_date ) {
		$start_time = strtotime( $start_date );
	}
	if ( $end_date ) {
		$end_time = strtotime( $end_date ) + ( 24 * 60 * 60 ) - 1;
	}

	// --- get all override IDs ---
	global $wpdb;
	$query = "SELECT ID,post_title,post_name FROM " . $wpdb->posts
	         . " WHERE post_type = '" . RADIO_STATION_OVERRIDE_SLUG . "' AND post_status = 'publish'";
	$overrides = $wpdb->get_results( $query, ARRAY_A );
	if ( !$overrides || !is_array( $overrides ) || ( count( $overrides ) < 1 ) ) {
		return false;
	}

	// --- loop overrides and get data ---
	$override_list = array();
	foreach ( $overrides as $i => $override ) {
		$data = get_post_meta( $override['ID'], 'show_override_sched', true );
		if ( $data ) {
			$date = $data['date'];
			if ( '' != $date ) {
				$date_time = strtotime( $date );
				$inrange = true;

				// --- check if in specified date range ---
				if ( ( isset( $start_time ) && ( $date_time < $start_time ) )
				     || ( isset( $end_time ) && ( $date_time > $end_time ) ) ) {
					$inrange = false;
				}

				// --- add the override data ---
				if ( $inrange ) {
					$day = date( 'l', $date_time );
					$start = $data['start_hour'] . ':' . $data['start_min'] . ' ' . $data['start_meridian'];
					$end = $data['end_hour'] . ':' . $data['end_min'] . ' ' . $data['end_meridian'];
					$override_start_time = strtotime( $day . ' ' . $start );
					$override_end_time = strtotime( $day . ' ' . $end );

					if ( $override_start_time < $override_end_time ) {

						// --- add the override as is ---
						$override_data = array(
							'override' => $override['ID'],
							'name'     => $override['post_title'],
							'slug'     => $override['post_name'],
							'date'     => $date,
							'day'      => $day,
							'start'    => $start,
							'end'      => $end,
							'url'      => get_permalink( $override['ID'] ),
							'split'    => false,
						);
						$override_list[$date][] = $override_data;

					} else {

						// --- split the override overnight ---
						$override_data = array(
							'override' => $override['ID'],
							'name'     => $override['post_title'],
							'slug'     => $override['post_name'],
							'date'     => $date,
							'day'      => $day,
							'start'    => $start,
							'end'      => '11:59 pm',
							'url'      => get_permalink( $override['ID'] ),
							'split'    => true,
						);
						$override_list[$date][] = $override_data;

						$nextday = date( 'l', $date_time + ( 24 * 60 * 60 ) );
						$nextdate = date( 'Y-m-d', $date_time + ( 24 * 60 * 60 ) );
						$override_data = array(
							'override' => $override['ID'],
							'name'     => $override['post_title'],
							'slug'     => $override['post_name'],
							'date'     => $nextdate,
							'day'      => $nextday,
							'start'    => '00:00 am',
							'end'      => $end,
							'url'      => get_permalink( $override['ID'] ),
							'split'    => true,
						);
						$override_list[$date][] = $override_data;
					}
				}
			}
		}
	}

	// --- filter and return ---
	$override_list = apply_filters( 'radio_station_get_overrides', $override_list, $start_date, $end_date );

	return $override_list;
}

// -------------
// Get Show Data
// -------------
// 2.3.0: added get show data grabber
function radio_station_get_show_data( $datatype, $show_id, $args = array() ) {

	// --- we need a data type and show ID ---
	if ( !$datatype ) {
		return false;
	}
	if ( !$show_id ) {
		return false;
	}

	// --- get meta key for valid data types ---
	if ( 'posts' == $datatype ) {
		$metakey = 'post_showblog_id';
	} elseif ( 'playlists' == $datatype ) {
		$metakey = 'playlist_show_id';
	} elseif ( 'episodes' == $datatype ) {
		$metakey = 'episode_show_id';
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
	if ( !isset( $args['data'] ) ) {
		$args['data'] = true;
	} elseif ( true !== $args['data'] ) {
		$default = false;
	}
	if ( !isset( $args['columns'] ) || !is_array( $args['columns'] ) || ( count( $args['columns'] ) < 1 ) ) {
		$columns = 'posts.ID, posts.post_title, posts.post_content, posts.post_excerpt, posts.post_date';
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
	if ( $default ) {
		$defaultdata = apply_filters( 'radio_station_cached_data', false, $datatype, $show_id );
		if ( $defaultdata ) {
			return $defaultdata;
		}
	}

	// --- get episodes with associated show ID ---
	global $wpdb;
	$query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta"
	         . " WHERE meta_key = '" . $metakey . "' AND meta_value = %d";
	$query = $wpdb->prepare( $query, $show_id );
	$post_metas = $wpdb->get_results( $query, ARRAY_A );
	if ( !$post_metas || !is_array( $post_metas ) || ( count( $post_metas ) < 1 ) ) {
		return false;
	}

	// --- get post IDs from post meta ---
	$post_ids = array();
	foreach ( $post_metas as $post_meta ) {
		$post_ids[] = $post_meta['post_id'];
	}

	// --- get posts from post IDs ---
	$post_id_list = implode( ',', $post_ids );
	$query = "SELECT " . $columns . " FROM " . $wpdb->prefix . "posts AS posts
		WHERE posts.ID IN(" . $post_id_list . ") AND posts.post_status = 'publish'
		ORDER BY posts.post_date DESC";
	if ( $args['limit'] ) {
		$query .= $wpdb->prepare( " LIMIT %d", $args['limit'] );
	}
	$results = $wpdb->get_results( $query, ARRAY_A );

	// --- maybe get additional data ---
	// TODO: maybe get additional data for each data type ?
	// if ( $args['data'] && $results && is_array( $results ) && ( count( $results ) > 0 ) ) {
	// if ( 'posts' == $datatype ) {
	// } elseif ( 'playlists' == $datatype ) {
	// } elseif ( 'episodes' == $datatype ) {
	// }
	// }

	// --- maybe cache default show data ---
	if ( $default ) {
		do_action( 'radio_station_cache_data', $datatype, $show_id, $results );
	}

	// --- filter and return ---
	$results = apply_filters( 'radio_station_show_' . $datatype, $results, $show_id, $args );

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
	// $show_email = get_post_meta( $show->ID, 'show_email', true );
	$show_link = get_post_meta( $show->ID, 'show_link', true );
	$show_file = get_post_meta( $show->ID, 'show_file', true );
	$show_schedule = get_post_meta( $show->ID, 'show_sched', true );
	if ( $show_schedule && is_array( $show_schedule ) && ( count( $show_schedule ) > 0 ) ) {
		$show_shifts = array();
		foreach ( $show_schedule as $i => $shift ) {
			$shift = radio_station_validate_shift( $shift );
			if ( !isset( $shift['disabled'] ) || ( 'yes' != $shift['disabled'] ) ) {
				$show_shifts[] = $shift;
			}
		}
	} else {
		$show_shifts = array();
	}

	// --- get show user data ---
	$show_hosts = get_post_meta( $show->ID, 'show_user_list', true );
	$show_producers = get_post_meta( $show->ID, 'show_producer_list', true );
	$hosts = $producers = array();
	if ( is_array( $show_hosts ) && ( count( $show_hosts ) > 0 ) ) {
		foreach ( $show_hosts as $host ) {
			if ( isset( $radio_station_data['user-' . $host] ) ) {
				$user = $radio_station_data['user-' . $host];
			} else {
				$user = get_user_by( 'ID', $host );
				$radio_station_data['user-' . $host] = $user;
			}
			$hosts[] = array(
				'name'  => $user->display_name,
				'url'   => radio_station_get_host_url( $host ),
			);
		}
	}
	if ( is_array( $show_producers ) && ( count( $show_producers ) > 0 ) ) {
		foreach ( $show_producers as $producer ) {
			if ( isset( $radio_station_data['user-' . $producer] ) ) {
				$user = $radio_station_data['user-' . $producer];
			} else {
				$user = get_user_by( 'ID', $producer );
				$radio_station_data['user-' . $producer] = $user;
			}
			$producers[] = array(
				'name'  => $user->display_name,
				'url'   => radio_station_get_producer_url( $producer ),
			);
		}
	}

	// --- create array and return ---
	$show_data = array(
		'id'        => $show->ID,
		'name'      => $show->post_title,
		'slug'      => $show->post_name,
		'url'       => get_permalink( $show->ID ),
		'latest'    => $show_file,
		'website'   => $show_link,
		// note: left out intentionally to avoid spam scrapers
		// 'email'		=> $show_email,
		'hosts'     => $hosts,
		'producers' => $producers,
		'genres'    => $genre_list,
		'languages' => $language_list,
		'schedule'  => $show_shifts,
	);

	// --- data route / feed for show ---
	if ( radio_station_get_setting( 'enable_data_routes' ) == 'yes' ) {
		$route_link = radio_station_get_route_url( 'show' );
		$show_route = add_query_arg( 'show', $show->post_name, $route_link );
		$show_data['route'] = $show_route;
	}
	if ( radio_station_get_setting( 'enable_data_feeds' ) == 'yes' ) {
		$feed_link = radio_station_get_feed_url( 'show' );
		$show_feed = add_query_arg( 'show', $show->post_name, $feed_link );
		$show_data['feed'] = $show_feed;
	}

	// --- add extra data for single show route/feed ---
	if ( $single ) {

		// --- add show posts ---
		$show_data['posts'] = radio_station_get_show_posts( $show->ID );

		// --- add show playlists ---
		$show_data['playlists'] = radio_station_get_show_playlists( $show->ID );

		// --- filter to maybe add more data ---
		$show_data = apply_filters( 'radio_station_show_data_meta', $show_data, $show->ID );
	}

	// --- maybe cache Show meta data ---
	do_action( 'radio_station_cache_data', 'show_meta', $show->ID, $show_data );

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

	// --- get override terms ---
	$genre_list = $language_list = array();
	$genres = wp_get_post_terms( $override->ID, RADIO_STATION_GENRES_SLUG );
	if ( $genres ) {
		foreach ( $genres as $genre ) {
			$genre_list[] = $genre->name;
		}
	}
	$languages = wp_get_post_terms( $override->ID, RADIO_STATION_LANGUAGES_SLUG );
	if ( $languages ) {
		foreach ( $languages as $language ) {
			$language_list[] = $language->name;
		}
	}

	// --- get override user data ---
	$override_hosts = get_post_meta( $override->ID, 'override_user_list', true );
	$override_producers = get_post_meta( $override->ID, 'override_producer_list', true );
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

	// --- create array and return ---
	$override_data = array(
		'id'        => $override->ID,
		'name'      => $override->post_title,
		'slug'      => $override->post_name,
		'url'       => get_permalink( $override->ID ),
		'genres'    => $genre_list,
		'languages' => $language_list,
		'hosts'     => $hosts,
		'producers' => $producers,
	);

	// --- filter and return ---
	$override_data = apply_filters( 'radio_station_override_data', $override_data, $override->ID );

	return $override_data;
}

// --------------------
// Get Current Schedule
// --------------------
function radio_station_get_current_schedule() {

	global $radio_station_data;

	// --- maybe get cached schedule ---
	$schedule = get_transient( 'radio_station_current_schedule' );
	if ( $schedule ) {
		$schedule = apply_filters( 'radio_station_current_schedule', $schedule );
		if ( $schedule ) {
			return $schedule;
		}
	}

	// --- get all show shifts ---
	$show_shifts = radio_station_get_show_shifts();

	// --- debug point ---
	if ( RADIO_STATION_DEBUG ) {
		$debug = "Show Shifts: " . print_r( $show_shifts, true ) . PHP_EOL;
		radio_station_debug( $debug );
	}

	if ( count( $show_shifts ) > 0 ) {

		// --- get show overrides ---
		// (from 12am this morning, for one week ahead and back)
		$now = strtotime( current_time( 'mysql' ) );
		$date = date( 'd-m-Y', $now );
		$start_time = strtotime( '12am ' . $date );
		$end_time = $start_time + ( 7 * 24 * 60 * 60 ) + 1;
		$start_time = $start_time - ( 7 * 24 * 60 * 60 ) - 1;
		$start_date = date( 'd-m-Y', $start_time );
		$end_date = date( 'd-m-Y', $end_time );
		$override_list = radio_station_get_overrides( $start_date, $end_date );

		// --- debug point ---
		if ( RADIO_STATION_DEBUG ) {
			$debug = "Now: " . $now . " - Today: " . $date . PHP_EOL;
			$debug .= "Start Date: " . $start_date . " - End Date: " . $end_date . PHP_EOL;
			$debug .= "Schedule Overrides: " . print_r( $override_list, true ) . PHP_EOL;
			radio_station_debug( $debug );
		}

		// --- apply overrides to the schedule ---
		if ( $override_list && is_array( $override_list ) && ( count( $override_list ) > 0 ) ) {
			foreach ( $show_shifts as $day => $shifts ) {
				$date = date( 'Y-m-d', strtotime( $day ) );
				if ( isset( $override_list[$date] ) ) {
					$overrides = $override_list[$date];
					foreach ( $shifts as $start => $shift ) {
						$start_time = strtotime( $date . ' ' . $shift['start'] );
						$end_time = strtotime( $date . ' ' . $shift['end'] );
						foreach ( $overrides as $i => $override ) {
							$override_start_time = strtotime( $date . ' ' . $override['start'] );
							$override_end_time = strtotime( $date . ' ' . $override['end'] );

							// --- check if start time is the same ---
							if ( $override_start_time == $start_time ) {

								// overwrite the existing shift
								$show_shifts[$day][$start] = $override;

								// check if there is remainder of existing show
								// ...this should hopefully not happen!
								// echo $override_end_time.'<->'.$end_time;
								if ( $override_end_time < $end_time ) {
									$shift['start'] = $override['end'];
									$shift['trimmed'] = 'start';
									$show_shifts[$day][$override['end']] = $shift;
								}
								unset( $overrides[$i] );

							} elseif ( ( $override_start_time > $start_time )
							           && ( $override_start_time < $end_time ) ) {

								// cut the existing shift short to add the override
								$show_shifts[$day][$start]['end'] = $override['start'];
								$show_shifts[$day][$start]['trimmed'] = 'end';
								$show_shifts[$day][$override['start']] = $override;
								unset( $overrides[$i] );

							}
						}
					}

					// --- maybe reloop to insert overrides before shows ---
					if ( count( $overrides ) > 0 ) {
						foreach ( $overrides as $i => $override ) {
							$show_shifts[$day][$override['start']] = $override;
							$override_end_time = strtotime( $date . ' ' . $override['end'] );
							$found = false;

							// --- check for overlapped shift (if any) ---
							foreach ( $shifts as $start => $shift ) {
								// find the first show overlapped
								if ( !$found ) {
									$start_time = strtotime( $date . ' ' . $shift['start'] );
									// echo $override_end_time.'~~~'.$start_time;
									if ( $override_end_time > $start_time ) {
										// adjust the show start time to override end time
										unset( $overrides[$day][$start] );
										$shift['start'] = $override['end'];
										$shift['trimmed'] = 'start';
										$show_shifts[$day][$override['end']] = $shift;
										$found = true;
									}
								}
							}
						}
					}

				}
			}
		}

		if ( RADIO_STATION_DEBUG ) {
			$debug = "Combined Schedule: " . print_r( $show_shifts, true ) . PHP_EOL;
			radio_station_debug( $debug );
		}

		// --- loop (remaining) shifts to add show data ---
		$now = strtotime( current_time( 'mysql' ) );
		foreach ( $show_shifts as $day => $shifts ) {
			foreach ( $shifts as $start => $shift ) {

				$show_id = $shift['show'];

				// --- check if shift is an override ---
				if ( isset( $shift['override'] ) && $shift['override'] ) {
					// ---- add the override data ---
					$override = radio_station_get_override_data_meta( $shift['override'] );
					$shift['show'] = $show_shifts[$day][$start]['show'] = $override;
				} else {
					// --- get (or get stored) show data ---
					if ( isset( $radio_station_data['show-' . $show_id] ) ) {
						$show = $radio_station_data['show-' . $show_id];
					} else {
						$show = radio_station_get_show_data_meta( $show_id );
						$radio_station_data['show-' . $show_id] = $show;
					}
					unset( $show['schedule'] );

					// --- add show data back to shift ---
					$shift['show'] = $show_shifts[$day][$start]['show'] = $show;
				}

				if ( !isset( $current_show ) ) {

					// --- get this shift start and end times ---
					$shift_start = $day . ' ' . $shift['start'];
					$shift_end = $day . ' ' . $shift['end'];
					$shift_start_time = strtotime( $shift_start );
					$shift_end_time = strtotime( $shift_end );

					// --- debug point ---
					if ( RADIO_STATION_DEBUG ) {
						$debug = '[[[' . date( 'm-d H:i:s', $now ) . ' - ' . $now . ']]]' . PHP_EOL;
						$debug .= '[[[' . $shift_start . ' - ' . $shift_start_time . ']]]' . PHP_EOL;
						$debug .= '[[[' . $shift_end . ' - ' . $shift_end_time . ']]]' . PHP_EOL . PHP_EOL;
						radio_station_debug( $debug );
					}

					// --- check if this is the currently scheduled show ---
					if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {
						$shift['day'] = $day;
						$current_show = $shift;
						$expires = $shift_end_time - $now - 1;
						if ( $expires > 3600 ) {
							$expires = 3600;
						} // cache for one hour max
						set_transient( 'radio_station_current_show', $current_show, $expires );
					}

				} elseif ( isset( $current_show['split'] ) && $current_show['split'] ) {

					// --- recombine split current shift ---
					$current_show['end'] = $shift['end'];
					unset( $current_show['split'] );
					set_transient( 'radio_station_current_show', $current_show, $expires );

				} elseif ( !isset( $next_show ) ) {

					// --- set next show transient ---
					$shift['day'] = $day;
					$next_show = $shift;
					$shift_end_time = strtotime( $day . ' ' . $shift['end'] );
					$next_expires = $shift_end_time - $now - 1;
					if ( $next_expires > ( $expires + 3600 ) ) {
						$next_expires = $expires + 3600;
					}
					set_transient( 'radio_station_next_show', $next_show, $next_expires );

				} elseif ( isset( $next_show['split'] ) && $next_show['split'] ) {

					// --- recombine split next shift ---
					$next_show['end'] = $shift['end'];
					unset( $next_show['split'] );
					set_transient( 'radio_station_next_show', $next_show, $next_expires );

				}
			}
		}

		// --- get next show if we did not find a current one ---
		if ( !isset( $next_show ) ) {
			// --- pass calculated shifts with limit of 1 ---
			$next_shows = radio_station_get_next_shows( 1, $show_shifts );
			if ( count( $next_shows ) > 0 ) {
				$next_show = $next_shows[0];
				$shift_end_time = strtotime( $day . ' ' . $next_show['end'] );
				$next_expires = $shift_end_time - $now - 1;
				set_transient( 'radio_station_next_show', $next_show, $next_expires );
			}
		}
	}

	// TODO: edge case where current show or next show is split
	// ...but actually unfinished due to end of schedule week ?

	// --- debug point ---
	if ( RADIO_STATION_DEBUG ) {
		$debug = "Show Schedule: " . print_r( $show_shifts, true ) . PHP_EOL;
		if ( isset( $current_show ) ) {
			$debug .= "Current Show: " . print_r( $current_show, true ) . PHP_EOL;
		}
		if ( isset( $next_show ) ) {
			$debug .= "Next Show: " . print_r( $next_show, true ) . PHP_EOL;
		}

		$next_shows = radio_station_get_next_shows( 5, $show_shifts );
		$debug .= "Next 5 Shows: " . print_r( $next_shows, true ) . PHP_EOL;

		radio_station_debug( $debug );
	}

	// --- cache current schedule data ---
	if ( isset( $current_show ) ) {
		set_transient( 'radio_station_current_schedule', $show_shifts, $expires );
	}

	// --- filter and return ---
	$show_shifts = apply_filters( 'radio_station_current_schedule', $show_shifts );

	return $show_shifts;
}

// ----------------
// Get Current Show
// ----------------
// 2.3.0: added new get current show function
function radio_station_get_current_show() {

	// --- get cached current show value ---
	$current_show = get_transient( 'radio_station_current_show' );

	// --- if not set it has expired so recheck schedule ---
	if ( !$current_show ) {
		$schedule = radio_station_get_current_schedule();
		$current_show = get_transient( 'radio_station_current_show ' );
	}

	// --- filter and return ---
	$current_show = apply_filters( 'radio_station_current_show', $current_show );

	return $current_show;
}

// -------------
// Get Next Show
// -------------
// 2.3.0: added new get next show function
function radio_station_get_next_show() {

	// --- get cached current show value ---
	$next_show = get_transient( 'radio_station_next_show' );

	// --- if not set it has expired so recheck schedule ---
	if ( !$next_show ) {
		$schedule = radio_station_get_current_schedule();
		$next_show = get_transient( 'radio_station_next_show' );
	}

	// --- filter and return ---
	$next_show = apply_filters( 'radio_station_next_show', $next_show );

	return $next_show;
}

// --------------
// Get Next Shows
// --------------
// 2.3.0: added new get next shows function
function radio_station_get_next_shows( $limit = 3, $show_shifts = false ) {

	// --- get all show shifts ---
	// (this check is needed to prevent an endless loop!)
	if ( !$show_shifts ) {
		$show_shifts = radio_station_get_current_schedule();
	}

	// --- loop (remaining) shifts to add show data ---
	$next_shows = array();
	$current_split = false;
	$now = strtotime( current_time( 'mysql' ) );
	$today = date( 'w', $now ); // numerical
	$weekdays = radio_station_get_schedule_weekdays( $today );

	foreach ( $weekdays as $day ) {
		if ( isset( $show_shifts[$day] ) ) {
			$shifts = $show_shifts[$day];
			foreach ( $shifts as $start => $shift ) {

				// --- get this shift start and end times ---
				$shift_start = $day . ' ' . $shift['start'];
				$shift_end = $day . ' ' . $shift['end'];
				$shift_start_time = strtotime( $shift_start );
				$shift_end_time = strtotime( $shift_end );

				// --- check if show has started ---
				if ( $now < $shift_start_time ) {

					// --- dedupe for shifts split overnight ---
					$skip = false;
					if ( $current_split ) {
						$skip = true;
						$current_split = false;
					}
					if ( isset( $split ) && $split ) {
						// --- recombine split shift ---
						$show_index = count( $next_shows ) - 1;
						$next_shows[$show_index]['end'] = $shift['end'];
						unset( $next_shows[$show_index]['split'] );
						$skip = true;
						$split = false;
					} elseif ( isset( $shift['split'] ) && $shift['split'] ) {
						$split = true;
					} else {
						$split = false;
					}

					if ( !$skip ) {

						// --- add to next shows data ---
						$next_shows[] = $shift;

						// --- return if we have reached limit ---
						if ( count( $next_shows ) == $limit ) {
							return $next_shows;
						}
					}

				} else {

					// TODO: test this with a split current shift

					// -- set flag for possibly split current shift ---
					if ( $current_split ) {
						// --- reset as in second part of split current shift ---
						$current_split = false;
					} elseif ( isset( $shift['split'] ) && $shift['split'] ) {
						// --- flag as in first part of split current shift ---
						$current_split = true;
					}
				}
			}
		}
	}

	// --- filter and return ---
	$next_shows = apply_filters( 'radio_station_next_shows', $next_shows, $limit, $show_shifts );

	return $next_shows;
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
	$term = get_term_by( 'slug', $genre, RADIO_STATION_GENRES_SLUG );
	if ( !$term ) {
		$term = get_term_by( 'name', $genre, RADIO_STATION_GENRES_SLUG );
	}
	if ( !$term ) {
		$term = get_term_by( 'id', $genre, RADIO_STATION_GENRES_SLUG );
	}
	if ( !$term ) {
		return false;
	}
	$genre[$term->name] = array(
		'id'            => $term->term_id,
		'name'          => $term->name,
		'slug'          => $term->slug,
		'description'   => $term->description,
		'url'           => get_term_link( $term, RADIO_STATION_GENRES_SLUG ),
	);

	return $genre;
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
				'meta_key' => 'show_sched',
				'compare'  => 'EXISTS',
			),
			array(
				'meta_key'   => 'show_active',
				'meta_value' => 'on',
				'compare'    => '=',
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
				'meta_key' => 'show_sched',
				'compare'  => 'EXISTS',
			),
			array(
				'meta_key'   => 'show_active',
				'meta_value' => 'on',
				'compare'    => '=',
			),
		),
	);
	$args = apply_filters( 'radio_station_show_languages_query_args', $args, $language );
	$shows = new WP_Query( $args );

	return $shows;
}


// ----------------------
// === Shift Checking ===
// ----------------------

// -------------------------
// Schedule Conflict Checker
// -------------------------
// (checks all existing show shifts for schedule)
// 2.3.0: added show shift conflict checker
function radio_station_check_shifts( $all_shifts ) {

	$conflicts = array();
	if ( count( $all_shifts ) > 0 ) {
		foreach ( $all_shifts as $day => $shifts ) {

			// --- get previous and next days for comparisons ---
			$thisday = date( 'Y-m-d' );
			$prevday = date( 'Y-m-d', strtotime( $thisday ) - ( 24 * 60 * 60 ) );
			$nextday = date( 'Y-m-d', strtotime( $thisday ) + ( 24 * 60 * 60 ) );

			// --- check for conflicts (overlaps) ---
			$checked_shifts = array();
			$prev_shift = false;
			foreach ( $shifts as $key => $shift ) {

				// --- reset shift switches ---
				$set_shift = true;
				$conflict = $disabled = false;
				if ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) {
					$disabled = true;
				}

				// --- account for midnight times ---
				if ( ( '11:59:59 pm' == $shift['start'] ) || ( '12:00 am' == $shift['start'] ) ) {
					$start_time = strtotime( $nextday . ' 12:00 am' );
				} else {
					$start_time = strtotime( $thisday . ' ' . $shift['start'] );
				}
				if ( ( '11:59:59 pm' == $shift['end'] ) || ( '12:00 am' == $shift['end'] ) ) {
					$end_time = strtotime( $nextday . ' 12:00 am' );
				} else {
					$end_time = strtotime( $thisday . ' ' . $shift['end'] );
				}

				if ( false != $prev_shift ) {

					// note: previous shift start and end times set in previous loop iteration

					// --- detect shift conflicts ---
					// (and maybe *attempt* to fix them up)
					if ( isset( $prev_start_time ) && ( $start_time == $prev_start_time ) ) {
						if ( $shift['split'] || $prev_shift['split'] ) {
							$conflict = 'overlap';
							if ( $shift['split'] && $prev_shift['split'] ) {
								// need to compare start times on previous day
								$data = $shift['shift'];
								$prevdata = $prev_shift['shift'];
								$real_start_time = strtotime( $prevday . ' ' . $data['real_start'] );
								$prev_real_start_time = strtotime( $prevday . ' ' . $prevdata['real_start'] );
								if ( $real_start_time > $prev_real_start_time ) {
									// current shift started later (overwrite from midnight)
									$set_shift = true;
								} elseif ( $real_start_time == $prev_real_start_time ) {
									$conflict = false; // do not duplicate, already recorded
									// total overlap, check last updated post time
									$updated = strtotime( $shift['updated'] );
									$prev_updated = strtotime( $prev_shift['updated'] );
									if ( $updated < $prev_updated ) {
										$set_shift = false;
									}
								}
							} elseif ( $shift['split'] ) {
								// the current shift has been split overnight
								// assume previous shift is correct (ignore new shift time)
								$set_shift = false;
							} elseif ( $prev_shift['split'] ) {
								// the previous shift has been split overnight
								// so we will assume the new shift start is correct
								// (overwrites previous shift from midnight key)
								$set_shift = true;
							}
						} else {
							$conflict = 'same_start';
							// we do not know which of these is correct
							// no solution here, so check most recent last updated time
							// we will assume (without certainty) most recent is correct
							$updated = strtotime( $shift['updated'] );
							$prev_updated = strtotime( $prev_shift['updated'] );
							if ( $updated < $prev_updated ) {
								$set_shift = false;
							}
						}
					} elseif ( isset( $prev_end_time ) && ( $start_time < $prev_end_time ) ) {

						// --- set the previous shift end time to current shift start ---
						$conflict = 'overlap';

						// --- modify only if this shift is not disabled ---
						if ( !$disabled ) {
							$checked_shift[$prev_shift['start']]['end'] = $shift['start'];
							$checked_shift[$prev_shift['start']]['trimmed'] = true;
						}

						// --- conflict debug output ---
						if ( RADIO_STATION_DEBUG && current_user_can( 'edit_shows' ) ) {
							$debug = "(Conflicting Start Time: " . date( "m-d l H:i", $start_time ) . " (" . $start_time . ")";
							$debug .= "Overlaps previous End Time: " . date( "m-d l H:i", $prev_end_time ) . " (" . $prev_end_time . ")";
							$debug .= "Shift: " . print_r( $shift, true );
							$debug .= "Previous Shift: " . print_r( $prev_shift, true );
							radio_station_debug( $debug );
						}
					}
				}

				// --- maybe store shift conflict data ---
				if ( $conflict ) {

					// ---- set short shift time data ---
					$shift_start = $shift['shift']['start_hour'] . ':' . $shift['shift']['start_min'] . $shift['shift']['start_meridian'];
					$shift_end = $shift['shift']['end_hour'] . ':' . $shift['shift']['end_min'] . $shift['shift']['end_meridian'];
					$prev_shift_start = $prev_shift['shift']['start_hour'] . ':' . $prev_shift['shift']['start_min'] . $prev_shift['shift']['start_meridian'];
					$prev_shift_end = $prev_shift['shift']['end_hour'] . ':' . $prev_shift['shift']['end_min'] . $prev_shift['shift']['end_meridian'];

					// --- store conflict for this shift ---
					$conflicts[$shift['show']][] = array(
						'show'          => $shift['show'],
						'day'           => $shift['shift']['day'],
						'start'         => $shift_start,
						'end'           => $shift_end,
						'disabled'      => $disabled,
						'with_show'     => $prev_shift['show'],
						'with_day'      => $prev_shift['shift']['day'],
						'with_start'    => $prev_shift_start,
						'with_end'      => $prev_shift_end,
						'with_disabled' => $prev_disabled,
						'conflict'      => $conflict,
						'duplicate'     => false,
					);

					// --- store for previous shift only if a different show ---
					if ( $shift['show'] != $prev_shift['show'] ) {
						$conflicts[$prev_shift['show']][] = array(
							'show'          => $prev_shift['show'],
							'day'           => $prev_shift['shift']['day'],
							'start'         => $prev_shift_start,
							'end'           => $prev_shift_end,
							'disabled'      => $prev_disabled,
							'with_show'     => $shift['show'],
							'with_day'      => $shift['shift']['day'],
							'with_start'    => $shift_start,
							'with_end'      => $shift_end,
							'with_disabled' => $disabled,
							'conflict'      => $conflict,
							'duplicate'     => true,
						);
					}
				}

				// --- set current shift to previous for next iteration ---
				$prev_start_time = $start_time;
				$prev_end_time = $end_time;
				$prev_shift = $shift;
				$prev_disabled = $disabled;

				// --- set the now checked shift data ---
				// (...but only if not disabled!)
				if ( $set_shift && !$disabled ) {
					// --- no longer need shift and post updated times ---
					unset( $shift['shift'] );
					unset( $shift['updated'] );
					if ( '00:00 am' == $shift['start'] ) {
						$shift['start'] = '12:00 am';
					}
					$checked_shifts[$shift['start']] = $shift;
				}

			}

			// --- set checked shifts for day ---
			$all_shifts[$day] = $checked_shifts;
		}
	}

	// --- check if any conflicts found ---
	if ( count( $conflicts ) > 0 ) {

		// --- debug point ---
		if ( RADIO_STATION_DEBUG ) {
			$debug = "Shift Conflict Data: " . print_r( $conflicts, true ) . PHP_EOL;
			radio_station_debug( $debug );
		}

		// --- save any conflicts found ---
		update_option( 'radio_station_schedule_conflicts', $conflicts );

	} else {
		// --- clear conflicts data ---
		delete_option( 'radio_station_schedule_conflicts' );
	}

	return $all_shifts;
}

// ------------------
// Show Shift Checker
// ------------------
// (checks shift being saved against other shows)
function radio_station_check_shift( $show_id, $shift, $context = 'all' ) {

	global $radio_station_data;

	// --- get all show shift times ---
	if ( isset( $radio_station_data['all-shifts'] ) ) {
		// --- get stored data ---
		$all_shifts = $radio_station_data['all-shifts'];
	} else {
		// (with conflict checking off as we are doing that now)
		$all_shifts = radio_station_get_show_shifts( false );

		// --- store this data for efficiency ---
		$radio_station_data['all-shifts'] = $all_shifts;
	}

	// --- get shows to check against via context ---
	$check_shifts = array();
	if ( 'all' == $context ) {
		$check_shifts = $all_shifts;
	} elseif ( 'shows' == $context ) {
		// --- check only against other show shifts ---
		foreach ( $all_shifts as $day => $day_shifts ) {
			foreach ( $day_shifts as $start => $day_shift ) {
				// --- ...so remove any shifts for this show ---
				if ( $day_shift['show'] != $show_id ) {
					$check_shifts[$day][$start] = $day_shift;
				}
			}
		}
	}

	// --- get shift start and end time ---
	$shift_start_time = strtotime( 'next ' . $shift['day'] . ' ' . $shift['start_hour'] . ':' . $shift['start_min'] . $shift['start_meridian'] );
	$shift_end_time = strtotime( 'next ' . $shift['day'] . ' ' . $shift['end_hour'] . ':' . $shift['end_min'] . $shift['end_meridian'] );
	if ( $shift_end_time < $shift_start_time ) {
		$shift_end_time = $shift_end_time + 86400;
	}

	// --- check for conflicts with other show shifts ---
	$conflicts = false;
	foreach ( $check_shifts as $day => $day_shifts ) {
		if ( $day == $shift['day'] ) {
			foreach ( $day_shifts as $i => $day_shift ) {

				// note: no need to adjust times for midnight as shifts are already split
				$day_shift_start_time = strtotime( 'next ' . $day . ' ' . $day_shift['start'] );
				$day_shift_end_time = strtotime( 'next ' . $day . ' ' . $day_shift['end'] );

				// --- ignore if this is the same shift we are checking ---
				if ( $day_shift['shift'] != $shift ) {

					// if the new shift starts before existing shift but finishes after existing shift starts
					// or new shift starts at the same time as the existing shift
					// of the existing shift starts before the new shift and finishes after new shift starts
					// ...then there is a shift overlap conflict
					if ( ( ( $shift_start_time < $day_shift_start_time ) && ( $shift_end_time > $day_shift_start_time ) )
					     || ( $shift_start_time == $day_shift_start_time )
					     || ( ( $day_shift_start_time < $shift_start_time ) && ( $day_shift_end_time > $shift_start_time ) ) ) {
						if ( !$conflicts ) {
							$conflicts = array();
						}
						$conflicts[] = $day_shift;
					}
				}
			}
		}
	}

	return $conflicts;
}

// ------------------
// New Shifts Checker
// ------------------
// (checks show shifts for conflicts with same show)
function radio_station_check_new_shifts( $new_shifts ) {

	// ---debug point ---
	if ( RADIO_STATION_DEBUG ) {
		$debug = "New Shifts: " . print_r( $new_shifts, true );
		radio_station_debug( $debug, false, 'shift-save.log' );
	}

	// --- double loop shifts to check against others ---
	foreach ( $new_shifts as $i => $shift_a ) {

		$a_start = $shift_a['day'] . ' ' . $shift_a['start_hour'] . ':' . $shift_a['start_min'] . $shift_a['start_meridian'];
		$a_end = $shift_a['day'] . ' ' . $shift_a['end_hour'] . ':' . $shift_a['end_min'] . $shift_a['end_meridian'];

		// ---debug point ---
		if ( RADIO_STATION_DEBUG ) {
			$debug = "Shift: " . $a_start . ' - ' . $a_end . PHP_EOL;
			radio_station_debug( $debug, false, 'shift-save.log' );
		}

		// --- get shift A start and end times ---
		$shift_a_start_time = strtotime( 'next ' . $shift_a['day'] . ' ' . $shift_a['start_hour'] . ':' . $shift_a['start_min'] . $shift_a['start_meridian'] );
		$shift_a_end_time = strtotime( 'next ' . $shift_a['day'] . ' ' . $shift_a['end_hour'] . ':' . $shift_a['end_min'] . $shift_a['end_meridian'] );
		if ( $shift_a_end_time < $shift_a_start_time ) {
			$shift_a_end_time = $shift_a_end_time + 86400;
		}

		foreach ( $new_shifts as $j => $shift_b ) {
			if ( $i != $j ) {

				$b_start = $shift_b['day'] . ' ' . $shift_b['start_hour'] . ':' . $shift_b['start_min'] . $shift_b['start_meridian'];
				$b_end = $shift_b['day'] . ' ' . $shift_b['end_hour'] . ':' . $shift_b['end_min'] . $shift_b['end_meridian'];

				if ( RADIO_STATION_DEBUG ) {
					$debug = "with Shift: " . $b_start . ' - ' . $b_end . PHP_EOL;
					radio_station_debug( $debug, false, 'show-shift-save.log' );
				}

				// --- get shift B start and end times ---
				$shift_b_start_time = strtotime( 'next ' . $shift_b['day'] . ' ' . $shift_b['start_hour'] . ':' . $shift_b['start_min'] . $shift_b['start_meridian'] );
				$shift_b_end_time = strtotime( 'next ' . $shift_b['day'] . ' ' . $shift_b['end_hour'] . ':' . $shift_b['end_min'] . $shift_b['end_meridian'] );
				if ( $shift_b_end_time < $shift_b_start_time ) {
					$shift_b_end_time = $shift_b_end_time + 86400;
				}

				// --- compare shift A and B times ---
				if ( ( ( $shift_a_start_time < $shift_b_start_time ) && ( $shift_a_end_time > $shift_b_start_time ) )
				     || ( $shift_a_start_time == $shift_b_start_time )
				     || ( ( $shift_b_start_time < $shift_a_start_time ) && ( $shift_b_end_time > $shift_a_start_time ) ) ) {

					// --- maybe disable shift B ---
					if ( ( 'yes' != $new_shifts[$i]['disabled'] )
					     && ( 'yes' != $new_shifts[$j]['disabled'] ) ) {

						if ( RADIO_STATION_DEBUG ) {
							$debug = "!Disabled!" . PHP_EOL;
							radio_station_debug( $debug, false, 'show-shift-save.log' );
						}

						$new_shifts[$j]['disabled'] = 'yes';
					}
				}
			}
		}
	}

	// --- debug point ---
	if ( RADIO_STATION_DEBUG ) {
		$debug = "Checked New Shifts: " . print_r( $new_shifts, true ) . PHP_EOL;
		radio_station_debug( $debug );
	}

	return $new_shifts;
}

// -------------------
// Validate Shift Time
// -------------------
// 2.3.0: added check for incomplete shift times
function radio_station_validate_shift( $shift ) {

	if ( '' == $shift['day'] ) {
		$shift['disabled'] = 'yes';
	}
	if ( ( '' == $shift['start_meridian'] ) || ( '' == $shift['end_meridian'] ) ) {
		$shift['disabled'] = 'yes';
	}
	if ( ( '' == $shift['start_hour'] ) || ( '' == $shift['end_hour'] ) ) {
		$shift['disabled'] = 'yes';
	}
	if ( '' == $shift['start_min'] ) {
		$shift['start_min'] = '00';
	}
	if ( '' == $shift['end_min'] ) {
		$shift['end_min'] = '00';
	}

	return $shift;
}


// -------------------
// === Show Avatar ===
// -------------------

// ------------------
// Update Show Avatar
// ------------------
// 2.3.0: trigger show avatar update when editing
add_action( 'replace_editor', 'radio_station_update_show_avatar', 10, 2 );
function radio_station_update_show_avatar( $replace_editor, $post ) {
	$show_id = $post->ID;
	radio_station_get_show_avatar_id( $show_id );

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
		$avatar_src = wp_get_attachment_image_src( $avatar_id, $size );
		$avatar_url = $avatar_src[0];
	}

	// --- filter and return ---
	$avatar_url = apply_filters( 'radio_station_show_avatar_url', $avatar_url, $show_id );
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
		$avatar = wp_get_attachment_image( $avatar_id, $size, false, $attr );
	}

	// --- filter and return ---
	$avatar = apply_filters( 'radio_station_show_avatar', $avatar, $show_id );
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
	if ( $stream && ( '' != $stream ) ) {
		$streaming_url = $stream;
	}
	$streaming_url = apply_filters( 'radio_station_stream_url', $streaming_url );

	return $streaming_url;
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
	$rest_url = get_rest_url();
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
	if ( isset( $radio_station_feeds[$route] ) ) {
		return $radio_station_routes[$route];
	}
	$base = apply_filters( 'radio_station_route_slug_base', 'base' );
	$route = apply_filters( 'radio_station_route_slug_' . $route, $route );
	if ( '' == $route ) {
		$path = '/' . $base . '/';
	} elseif ( !$route ) {
		return false;
	} else {
		$path = '/' . $base . '/' . $route . '/';
	}
	$radio_station_routes[$route] = $route_url = get_rest_url( $path );

	return $route_url;
}

// ------------
// Get Feed URL
// ------------
function radio_station_get_feed_url( $feedname ) {
	global $radio_station_feeds;
	if ( isset( $radio_station_feeds[$feedname] ) ) {
		return $radio_station_feeds[$feedname];
	}
	$feedname = apply_filters( 'radio_station_feed_slug_' . $feedname, $feedname );
	if ( !$feedname ) {
		return false;
	}
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
// 2.3.0: added to get DJ / Host profile permalink
function radio_station_get_host_url( $host_id ) {
	$post_id = radio_station_get_profile_id( RADIO_STATION_HOST_SLUG, $host_id );
	if ( $post_id ) {
		$host_url = get_permalink( $post_id );
	} else {
		$host_url = get_author_posts_url( $host_id );
	}
	$host_url = apply_filters( 'radio_station_host_url', $host_url, $host_id );

	return $host_url;
}

// ------------------------
// Get Producer Profile URL
// ------------------------
// 2.3.0: added to get Producer profile permalink
function radio_station_get_producer_url( $producer_id ) {
	$post_id = radio_station_get_profile_id( RADIO_STATION_PRODUCER_SLUG, $producer_id );
	if ( $post_id ) {
		$producer_url = get_permalink( $post_id );
	} else {
		$producer_url = get_author_posts_url( $producer_id );
	}
	$producer_url = apply_filters( 'radio_station_producer_url', $producer_url, $producer_id );

	return $producer_url;
}

// ---------------
// Get Upgrade URL
// ---------------
// 2.3.0: added to get Upgrade to Pro link
function radio_station_get_upgrade_url() {
	// TODO: test Freemius upgrade to Pro URL
	// ...maybe it is -addons instead of -pricing ???
	$upgrade_url = add_query_arg( 'page', 'radio-station-pricing', admin_url( 'admin.php' ) );

	return $upgrade_url;
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


// ------------------------
// === Helper Functions ===
// ------------------------

// --------------
// Get Profile ID
// --------------
// 2.3.0: added to get host or producer profile post ID
function radio_station_get_profile_id( $type, $user_id ) {

	global $radio_station_data;

	if ( isset( $radio_station_data[$type . '-' . $user_id] ) ) {
		$post_id = $radio_station_data[$type . '-' . $user_id];
		return $post_id;
	}

	// --- get the post ID(s) for the profile ---
	global $wpdb;
	$query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta
			  WHERE meta_key = '" . $type . "_user_id' AND meta_value = %d";
	$query = $wpdb->prepare( $query, $user_id );
	$results = $wpdb->get_results( $query, ARRAY_A );

	// --- check for and return published profile ID ---
	if ( $results && is_array( $results ) && ( count( $results ) > 0 ) ) {
		foreach ( $results as $result ) {
			$query = "SELECT ID FROM " . $wpdb->prefix . "posts
					  WHERE post_status = 'publish' AND post_id = %d";
			$query = $wpdb->prepare( $query, $result['ID'] );
			$post_id = $wpdb->get_var( $query );
			if ( $post_id ) {
				$radio_station_data[$type . '-' . $user_id] = $post_id;

				return $post_id;
			}
		}
	}

	return false;
}

// --------------------
// Get Timezone Options
// --------------------
// ref: (based on) https://stackoverflow.com/a/17355238/5240159
function radio_station_get_timezone_options( $include_wp_timezone = false ) {

	// --- maybe get stored timezone options ---
	$options = get_transient( 'radio-station-timezone-options' );
	if ( !$options ) {

		// --- set regions ---
		$regions = array(
			DateTimeZone::AFRICA     => __( 'Africa', 'radio-station' ),
			DateTimeZone::AMERICA    => __( 'America', 'radio-station' ),
			DateTimeZone::ASIA       => __( 'Asia', 'radio-station' ),
			DateTimeZone::ATLANTIC   => __( 'Atlantic', 'radio-station' ),
			DateTimeZone::AUSTRALIA  => __( 'Australia', 'radio-station' ),
			DateTimeZone::EUROPE     => __( 'Europe', 'radio-station' ),
			DateTimeZone::INDIAN     => __( 'Indian', 'radio-station' ),
			DateTimeZone::PACIFIC    => __( 'Pacific', 'radio-station' ),
			DateTimeZone::ANTARCTICA => __( 'Antarctica', 'radio-station' ),
		);

		// --- loop regions ---
		foreach ( $regions as $region => $label ) {

			// --- option group by region ---
			$options['*OPTGROUP*' . $region] = $label;

			$timezones = DateTimeZone::listIdentifiers( $region );
			$timezone_offsets = array();
			foreach ( $timezones as $timezone ) {
				$datetimezone = new DateTimeZone( $timezone );
				$offset = $datetimezone->getOffset( new DateTime() );
				$timezone_offsets[$offset][] = $timezone;
			}
			ksort( $timezone_offsets );

			foreach ( $timezone_offsets as $offset => $timezones ) {
				foreach ( $timezones as $timezone ) {
					$prefix = $offset < 0 ? '-' : '+';
					$hour = gmdate( 'H', abs( $offset ) );
					$hour = gmdate( 'H', abs( $offset ) );
					$minutes = gmdate( 'i', abs( $offset ) );
					$code = event_flow_get_timezone_code( $timezone );
					$label = $code . ' (GMT' . $prefix . $hour . ':' . $minutes . ') - ';
					$timezone_split = explode( '/', $timezone );
					unset( $timezone_split[0] );
					$timezone_joined = implode( '/', $timezone_split );
					$label .= str_replace( '_', ' ', $timezone_joined );
					$options[$timezone] = $label;
				}
			}
		}
		$expiry = 7 * 24 * 60 * 60;
		set_transient( 'radio-station-timezone-options', $options, $expiry );
	}

	// --- maybe add WordPress timezone (default) option ---
	if ( $include_wp_timezone ) {
		$wp_timezone = array( '' => __( 'WordPress Timezone', 'radio-station' ) );
		$options = array_merge( $wp_timezone, $options );
	}

	$options = apply_filters( 'radio_station_get_timezone_options', $options, $include_wp_timezone );

	return $options;
}

// -----------------
// Get Timezone Code
// -----------------
// note: this should only be used for display in the "now"
// (as the actual code to be used is based on location)
function radio_station_get_timezone_code( $timezone ) {
	$date_time = new DateTime();
	$date_time->setTimeZone( new DateTimeZone( $timezone ) );
	return $date_time->format( 'T' );
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
	}

	// --- maybe include WordPress default language ---
	if ( $include_wp_default ) {
		$wp_language = array( '', __( 'WordPress Setting', 'radio-station' ) );
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
		if ( !$lang || ( '' == $lang ) ) {
			$lang = get_option( 'WPLANG' );
			if ( !$lang ) {
				$lang = 'en_US';
			}
		}
	}

	// --- get the specified language term ---
	$term = get_term_by( 'slug', $lang, RADIO_STATION_LANGUAGES_SLUG );
	if ( !$term ) {
		$term = get_term_by( 'name', $lang, RADIO_STATION_LANGUAGES_SLUG );
	}
	if ( !$term ) {
		$term = get_term_by( 'id', $lang, RADIO_STATION_LANGUAGES_SLUG );
	}
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
						// 'url'   => '',
					);
				}
			}
		} else {
			$language = false;
		}
	}

	return $language;
}

// ------------
// Get Next Day
// ------------
// 2.3.0: added get next day helper
function radio_station_get_next_day( $day ) {
	// note: for internal use so not translated
	$day = trim( $day );
	if ( 'Sunday' == $day ) {
		return 'Monday';
	}
	if ( 'Monday' == $day ) {
		return 'Tuesday';
	}
	if ( 'Tuesday' == $day ) {
		return 'Wednesday';
	}
	if ( 'Wednesday' == $day ) {
		return 'Thursday';
	}
	if ( 'Thursday' == $day ) {
		return 'Friday';
	}
	if ( 'Friday' == $day ) {
		return 'Saturday';
	}
	if ( 'Saturday' == $day ) {
		return 'Sunday';
	}

	return '';
}

// ----------------
// Get Previous Day
// ----------------
// 2.3.0: added get previous day helper
function radio_station_get_previous_day( $day ) {
	// note: for internal use so not translated
	$day = trim( $day );
	if ( 'Sunday' == $day ) {
		return 'Saturday';
	}
	if ( 'Monday' == $day ) {
		return 'Sunday';
	}
	if ( 'Tuesday' == $day ) {
		return 'Monday';
	}
	if ( 'Wednesday' == $day ) {
		return 'Tuesday';
	}
	if ( 'Thursday' == $day ) {
		return 'Wednesday';
	}
	if ( 'Friday' == $day ) {
		return 'Thursday';
	}
	if ( 'Saturday' == $day ) {
		return 'Friday';
	}

	return '';
}

// -------------
// Get All Hours
// -------------
function radio_station_get_hours( $format = 24 ) {
	$hours = array();
	if ( 24 === (int) $format ) {
		$hours = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23 );
	} elseif ( 12 === (int) $format ) {
		$hours = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 );
	}
	return $hours;
}

// ---------------------------
// Convert Hour to Time Format
// ---------------------------
// (note: used with suffix for on-the-hour times)
// 2.3.0: standalone function via master-schedule-default.php
// 2.3.0: optionally add suffix for both time formats
function radio_station_convert_hour( $hour, $timeformat = 24, $suffix = true ) {

	$hour = intval( $hour );

	// 2.3.0: handle next and previous hours (over 24 or below 0)
	if ( $hour < 0 ) {
		while ( $hour < 0 ) {
			$hour = $hour + 24;
		}
	}
	if ( $hour > 24 ) {
		while ( $hour > 24 ) {
			$hour = $hour - 24;
		}
	}

	if ( 24 === (int) $timeformat ) {
		// --- 24 hour time format ---
		if ( 24 == $hour ) {
			$hour = '00';
		} elseif ( $hour < 10 ) {
			$hour = '0' . $hour;
		}
		if ( $suffix ) {
			$hour .= ':00';
		}
	} elseif ( 12 === (int) $timeformat ) {
		// --- 12 hour time format ---
		// 2.2.7: added meridiem translations
		if ( ( $hour === 0 ) || ( 24 === $hour ) ) {
			// midnight
			$hour = '12';
			if ( $suffix ) {
				$hour .= ' ' . radio_station_translate_meridiem( 'am' );
			}
		} elseif ( $hour < 12 ) {
			// morning
			if ( $suffix ) {
				$hour .= ' ' . radio_station_translate_meridiem( 'am' );
			}
		} elseif ( 12 === $hour ) {
			// noon
			if ( $suffix ) {
				$hour .= ' ' . radio_station_translate_meridiem( 'pm' );
			}
		} elseif ( $hour > 12 ) {
			// after-noon
			$hour = $hour - 12;
			if ( $suffix ) {
				$hour .= ' ' . radio_station_translate_meridiem( 'pm' );
			}
		}
	}

	return $hour;
}

// ----------------------------
// Convert Shift to Time Format
// ----------------------------
// 2.3.0: added to convert shift time to 24 hours (or back)
function radio_station_convert_shift_time( $time, $timeformat ) {
	$timestamp = strtotime( date( 'l ' . $time ) );
	if ( 12 == (int) $timeformat ) {
		$time = date( 'h:i a', $timestamp );
		str_replace( 'am', radio_station_translate_meridiem( 'am' ), $time );
		str_replace( 'pm', radio_station_translate_meridiem( 'pm' ), $time );
	} elseif ( 24 == (int) $timeformat ) {
		$time = date( 'H:i', $timestamp );
	}

	return $time;
}

// ---------------------
// Get Schedule Weekdays
// ---------------------
// note: no translations here because used internally for sorting
// 2.3.0: added to get schedule weekdays from start of week
function radio_station_get_schedule_weekdays( $weekstart = false ) {

	// --- maybe get start of the week ---
	if ( !$weekstart ) {
		$weekstart = get_option( 'start_of_week' );
		$weekstart = apply_filters( 'radio_station_schedule_weekday_start', $weekstart );
	}

	// --- loop weekdays and reorder from start day ---
	$weekdays = array(
		'Sunday',
		'Monday',
		'Tuesday',
		'Wednesday',
		'Thursday',
		'Friday',
		'Saturday',
	);
	$start = $before = $after = array();
	foreach ( $weekdays as $i => $weekday ) {
		if ( $i == $weekstart ) {
			$start[] = $weekday;
		} elseif ( $i > $weekstart ) {
			$after[] = $weekday;
		} elseif ( $i < $weekstart ) {
			$before[] = $weekday;
		}
	}

	// --- put the days before the start day at the end ---
	$weekdays = array_merge( $start, $after );
	$weekdays = array_merge( $weekdays, $before );

	return $weekdays;
}

// ------------
// Trim Excerpt
// ------------
// (modified copy of wp_trim_excerpt)
function radio_station_trim_excerpt( $content, $length = false, $more = false ) {

	$raw_content = $content;
	$content = strip_shortcodes( $content );
	// TODO: check for Gutenberg plugin-only equivalent ?
	if ( function_exists( 'excerpt_remove_blocks' ) ) {
		$content = excerpt_remove_blocks( $content );
	}
	$content = apply_filters( 'the_content', $content );
	$content = str_replace( ']]>', ']]&gt;', $content );

	if ( !$length ) {
		$length = (int) apply_filters( 'radio_station_excerpt_length', 35 );
	}
	if ( !$more ) {
		$more = apply_filters( 'radio_station_excerpt_more', ' [&hellip;]' );
	}
	$excerpt = wp_trim_words( $content, $length, $more );

	return apply_filters( 'radio_station_trim_excerpt', $excerpt, $raw_content );
}

// --------------
// Shorten String
// --------------
// (shorten a string to a set number of words)
function radio_station_shorten_string( $string, $limit ) {

	$shortened = $string;
	$array = explode( ' ', $string );

	if ( count( $array ) <= $limit ) {
		// --- already at or under the limit ---
		$shortened = $string;
	} else {
		// --- over the word limit so trim ---
		array_splice( $array, $limit );
		$shortened = implode( ' ', $array ) . ' ...';
	}

	return $shortened;
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
	return $sanitized;
}


// --------------------
// === Translations ===
// --------------------

// -----------------
// Translate Weekday
// -----------------
// important note: translated individually as cannot translate a variable
// 2.2.7: use wp locale class to translate weekdays
// 2.3.0: allow for abbreviated and long version changeovers
function radio_station_translate_weekday( $weekday, $short = false ) {

	// 2.3.0: return empty for empty select option
	if ( empty( $weekday ) ) {
		return '';
	}

	global $wp_locale;

	if ( $short ) {

		// --- translate abbreviated weekday ---
		if ( ( 'Sunday' == $weekday ) || ( 'Sun' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 0 ) );
		} elseif ( ( 'Monday' == $weekday ) || ( 'Mon' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 1 ) );
		} elseif ( ( 'Tuesday' == $weekday ) || ( 'Tue' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 2 ) );
		} elseif ( ( 'Wednesday' == $weekday ) || ( 'Wed' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 3 ) );
		} elseif ( ( 'Thursday' == $weekday ) || ( 'Thu' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 4 ) );
		} elseif ( ( 'Friday' == $weekday ) || ( 'Fri' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 5 ) );
		} elseif ( ( 'Saturday' == $weekday ) || ( 'Sat' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 6 ) );
		} elseif ( ( intval( $weekday ) > - 1 ) && ( intval( $weekday ) < 7 ) ) {
			// 2.3.0: add support for numeric weekday value
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( $weekday ) );
		}

	} else {

		// --- translate full weekday ---
		// 2.2.7: fix to typo for Tuesday
		// 2.3.0: fix to typo for Thursday (jeez!)
		if ( ( 'Sunday' == $weekday ) || ( 'Sun' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday( 0 );
		} elseif ( ( 'Monday' == $weekday ) || ( 'Mon' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday( 1 );
		} elseif ( ( 'Tuesday' == $weekday ) || ( 'Tue' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday( 2 );
		} elseif ( ( 'Wednesday' == $weekday ) || ( 'Wed' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday( 3 );
		} elseif ( ( 'Thursday' == $weekday ) || ( 'Thu' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday( 4 );
		} elseif ( ( 'Friday' == $weekday ) || ( 'Fri' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday( 5 );
		} elseif ( ( 'Saturday' == $weekday ) || ( 'Sat' == $weekday ) ) {
			$weekday = $wp_locale->get_weekday( 6 );
		} elseif ( ( intval( $weekday ) > - 1 ) && ( intval( $weekday ) < 7 ) ) {
			// 2.3.0: add support for numeric weekday value
			$weekday = $wp_locale->get_weekday( $weekday );
		}

	}

	return $weekday;
}

// ---------------
// Translate Month
// ---------------
// important note: translated individually as cannot translate a variable
// 2.2.7: use wp locale class to translate months
function radio_station_translate_month( $month, $short = false ) {

	// 2.3.0: return empty for empty select option
	if ( empty( $month ) ) {
		return '';
	}

	global $wp_locale;
	if ( $short ) {

		// --- translate abbreviated month ---
		if ( 'Jan' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 1 ) );
		} elseif ( 'Feb' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 2 ) );
		} elseif ( 'Mar' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 3 ) );
		} elseif ( 'Apr' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 4 ) );
		} elseif ( 'May' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 5 ) );
		} elseif ( 'Jun' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 6 ) );
		} elseif ( 'Jul' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 7 ) );
		} elseif ( 'Aug' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 8 ) );
		} elseif ( 'Sep' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 9 ) );
		} elseif ( 'Oct' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 10 ) );
		} elseif ( 'Nov' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 11 ) );
		} elseif ( 'Dec' === $month ) {
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( 12 ) );
		} elseif ( ( intval( $month ) > 0 ) && ( intval( $month ) < 13 ) ) {
			// 2.3.0: add support for numeric month value
			$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( $month ) );
		}
	} else {

		// --- translate full month ---
		if ( 'January' === $month ) {
			$month = $wp_locale->get_month( 1 );
		} elseif ( 'February' === $month ) {
			$month = $wp_locale->get_month( 2 );
		} elseif ( 'March' === $month ) {
			$month = $wp_locale->get_month( 3 );
		} elseif ( 'April' === $month ) {
			$month = $wp_locale->get_month( 4 );
		} elseif ( 'May' === $month ) {
			$month = $wp_locale->get_month( 5 );
		} elseif ( 'June' === $month ) {
			$month = $wp_locale->get_month( 6 );
		} elseif ( 'July' === $month ) {
			$month = $wp_locale->get_month( 7 );
		} elseif ( 'August' === $month ) {
			$month = $wp_locale->get_month( 8 );
		} elseif ( 'September' === $month ) {
			$month = $wp_locale->get_month( 9 );
		} elseif ( 'October' === $month ) {
			$month = $wp_locale->get_month( 10 );
		} elseif ( 'November' === $month ) {
			$month = $wp_locale->get_month( 11 );
		} elseif ( 'December' === $month ) {
			$month = $wp_locale->get_month( 12 );
		} elseif ( ( intval( $month ) > 0 ) && ( intval( $month ) < 13 ) ) {
			// 2.3.0: add support for numeric month value
			$month = $wp_locale->get_month( $month );
		}

	}

	return $month;
}

// ------------------
// Translate Meridiem
// ------------------
// 2.2.7: added meridiem translation function
function radio_station_translate_meridiem( $meridiem ) {
	global $wp_locale;

	return $wp_locale->get_meridiem( $meridiem );
}


// ------------------------
// === Legacy Functions ===
// ------------------------

/*
* Support functions for shortcodes and widgets
* Author: Nikki Blight
* Since: 2.0.14
*/

// (still used in legacy shortcodes, templates and widgets)
// note: to be gradually deprecated as they are replaced

// --- get only the currently relevant schedule ---
// (used in DJ Widget)
function radio_station_current_schedule( $scheds = array() ) {

	$now = current_time( 'timestamp' );
	$current = array();

	foreach ( $scheds as $sched ) {

		// 2.3.0: added check is shift is disabled
		if ( !isset( $sched['disabled'] ) || ( 'yes' != $sched['disabled'] ) ) {

			if ( date( 'l', $now ) !== $sched['day'] ) {
				continue;
			}

			$start = strtotime( date( 'Y-m-d', $now ) . $sched['start_hour'] . ':' . $sched['start_min'] . ' ' . $sched['start_meridian'] );

			if ( 'pm' === $sched['start_meridian'] && 'am' === $sched['end_meridian'] ) {
				// check for shows that run overnight into the next morning
				$end = strtotime( date( 'Y-m-d', ( $now + 86400 ) ) . $sched['end_hour'] . ':' . $sched['end_min'] . ' ' . $sched['end_meridian'] );
			} else {
				$end = strtotime( date( 'Y-m-d', $now ) . $sched['end_hour'] . ':' . $sched['end_min'] . ' ' . $sched['end_meridian'] );
			}

			// a show cannot end before it begins... if it does, it ends the following day.
			if ( $end <= $start ) {
				$end = $end + 86400;
			}

			// compare to the current timestamp
			if ( ( $start <= $now ) && ( $end >= $now ) ) {
				$current = $sched;
			} else {
				continue;
			}
		}
	}

	return $current;
}

// --- convert shift times to 24-hour and timestamp formats for comparisons ---
function radio_station_convert_time( $time = array() ) {

	if ( empty( $time ) ) {
		return false;
	}

	$now = strtotime( current_time( 'mysql' ) );
	$cur_date = date( 'Y-m-d', $now );
	$tom_date = date( 'Y-m-d', ( $now + 86400 ) ); // get the date for tomorrow

	// --- convert to 24 hour time ---
	$time = radio_station_convert_schedule_to_24hour( $time );

	// --- get a timestamp for the schedule start and end ---
	$time['start_timestamp'] = strtotime( $cur_date . ' ' . $time['start_hour'] . ':' . $time['start_min'] );

	if ( 'pm' === $time['start_meridian'] && 'am' === $time['end_meridian'] ) {
		// check for shows that run overnight into the next morning
		$time['end_timestamp'] = strtotime( $tom_date . ' ' . $time['end_hour'] . ':' . $time['end_min'] );
	} else {
		$time['end_timestamp'] = strtotime( $cur_date . ' ' . $time['end_hour'] . ':' . $time['end_min'] );
	}

	// a show cannot end before it begins... if it does, it ends the following day.
	if ( $time['end_timestamp'] <= $time['start_timestamp'] ) {
		$time['end_timestamp'] = $time['end_timestamp'] + 86400;
	}

	return $time;
}

// --- convert a shift to 24 hour time for display ---
// TODO: replace all use of this function with new radio_station_convert_shift_time
function radio_station_convert_schedule_to_24hour( $sched = array() ) {

	if ( empty( $sched ) ) {
		return false;
	}

	if ( 'pm' === $sched['start_meridian'] && 12 !== (int) $sched['start_hour'] ) {
		$sched['start_hour'] = $sched['start_hour'] + 12;
	}
	if ( 'am' === $sched['start_meridian'] && $sched['start_hour'] < 10 ) {
		$sched['start_hour'] = '0' . $sched['start_hour'];
	}
	if ( 'am' === $sched['start_meridian'] && 12 === (int) $sched['start_hour'] ) {
		$sched['start_hour'] = '00';
	}

	if ( 'pm' === $sched['end_meridian'] && 12 !== (int) $sched['end_hour'] ) {
		$sched['end_hour'] = $sched['end_hour'] + 12;
	}
	if ( 'am' === $sched['end_meridian'] && $sched['end_hour'] < 10 ) {
		$sched['end_hour'] = '0' . $sched['end_hour'];
	}
	if ( 'am' === $sched['end_meridian'] && 12 === (int) $sched['end_hour'] ) {
		$sched['end_hour'] = '00';
	}

	return $sched;
}

// --- fetch the current DJ(s) on-air --
// used in DJ Widget, dj-widget shortcode
function radio_station_dj_get_current() {

	// --- first check to see if there are any shift overrides ---
	$check = radio_station_master_get_overrides( true );
	if ( $check ) {
		$shows = array(
			'all'  => $check,
			'type' => 'override',
		);
		return $shows;
	}

	global $wpdb;

	// --- get the current time and day ---
	$now = strtotime( current_time( 'mysql' ) );
	$cur_day = date( 'l', $now );

	// --- query for active shows only ---
	$show_shifts = $wpdb->get_results(
		"SELECT meta.post_id, meta.meta_value FROM {$wpdb->postmeta} AS meta
		JOIN {$wpdb->postmeta} AS metab
			ON meta.post_id = metab.post_id
		JOIN {$wpdb->posts} as posts
			ON posts.ID = meta.post_id
		WHERE meta.meta_key = 'show_sched' AND
			posts.post_status = 'publish' AND
			(
				metab.meta_key = 'show_active' AND
				metab.meta_value = 'on'
			)"
	);

	$show_ids = array();
	foreach ( $show_shifts as $shift ) {
		$shift->meta_value = maybe_unserialize( $shift->meta_value );

		// if a show has no shifts, unserialize() will return false instead of an empty array... fix that to prevent errors in the foreach loop.
		if ( !is_array( $shift->meta_value ) ) {
			$shift->meta_value = array();
		}

		foreach ( $shift->meta_value as $time ) {

			// 2.3.0: added check if shift has been disabled
			if ( !isset( $time['disabled'] ) || ( 'yes' != $time['disabled'] ) ) {

				// check if the shift is for the current day.  If it's not, skip it
				if ( $time['day'] === $cur_day ) {
					// format the time so that it is more easily compared
					$time = radio_station_convert_time( $time );

					// compare to the current timestamp
					if ( ( $time['start_timestamp'] <= $now ) && ( $time['end_timestamp'] >= $now ) ) {
						$show_ids[] = $shift->post_id;
					}
				}

				// we need to make a special allowance for shows that run from one day into the next
				if ( date( 'w', strtotime( $time['day'] ) ) + 1 == date( 'w', strtotime( $cur_day ) ) ) {

					$time = radio_station_convert_time( $time );
					// because station_convert_time assumes that the show STARTS on the current day,
					// when, in this case, it ends on the current day, we have to subtract 1 day from the timestamps
					$time['start_timestamp'] = $time['start_timestamp'] - 86400;
					$time['end_timestamp'] = $time['end_timestamp'] - 86400;

					// compare to the current timestamp
					if ( ( $time['start_timestamp'] <= $now ) && ( $time['end_timestamp'] >= $now ) ) {
						$show_ids[] = $shift->post_id;
					}
				}
			}
		}
	}

	$shows = array();
	foreach ( $show_ids as $id ) {
		$shows['all'][] = get_post( $id );
	}
	$shows['type'] = 'shows';

	return $shows;
}

// --- get the next DJ or DJs scheduled to be on air based on the current time ---
// used in DJ Upcoming Widget, dj-coming-up-widget shortcode
function radio_station_dj_get_next( $limit = 1 ) {

	global $wpdb;

	// get the various times/dates we need
	$cur_day = date( 'l', strtotime( current_time( 'mysql' ) ) );
	$cur_day_num = date( 'N', strtotime( current_time( 'mysql' ) ) );
	$cur_date = date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) );
	$now = strtotime( current_time( 'mysql' ) );
	$shows = array();

	// first check to see if there are any shift overrides
	$check = radio_station_master_get_overrides();
	$overrides = array();

	if ( $check ) {

		foreach ( $check as $i => $p ) {

			$p['sched'] = radio_station_convert_time( $p['sched'] );

			// compare to the current timestamp
			if ( ( $p['sched']['start_timestamp'] <= $now ) && ( $p['sched']['end_timestamp'] >= $now ) ) {
				//show is on now, so we don't need it listed under upcoming
				//$overrides[$p['sched']['start_timestamp'].'|'.$p['sched']['end_timestamp']] = $p;
				unset( $check[$i] );
			} elseif ( ( $p['sched']['start_timestamp'] > $now ) && ( $p['sched']['end_timestamp'] > $now ) ) {
				// show is on later today
				$overrides[$p['sched']['start_timestamp'] . '|' . $p['sched']['end_timestamp']] = $p;
			} else {
				// show is already over and we don't need it
				unset( $check[$i] );
			}
		}

		// sort the overrides by start time
		ksort( $overrides );
	}

	// Fetch all schedules... we only want active shows
	$show_shifts = $wpdb->get_results(
		"SELECT meta.post_id, meta.meta_value
		FROM {$wpdb->postmeta} AS meta
		JOIN {$wpdb->postmeta} AS metab
			ON meta.post_id = metab.post_id
		JOIN {$wpdb->posts} as posts
			ON posts.ID = meta.post_id
		WHERE meta.meta_key = 'show_sched' AND
			posts.post_status = 'publish' AND
			(
				metab.meta_key = 'show_active' AND
				metab.meta_value = 'on'
			)"
	);

	$show_ids = $encore_ids = array();

	foreach ( $show_shifts as $shift ) {

		$shift->meta_value = maybe_unserialize( $shift->meta_value );

		// if a show has no shifts, unserialize() will return false instead of an empty array...
		// fix that to prevent errors in the foreach loop.
		if ( !is_array( $shift->meta_value ) ) {
			$shift->meta_value = array();
		}

		$encore_ids = array();
		$days = array(
			'Sunday'    => 7,
			'Monday'    => 1,
			'Tuesday'   => 2,
			'Wednesday' => 3,
			'Thursday'  => 4,
			'Friday'    => 5,
			'Saturday'  => 6,
		);
		foreach ( $shift->meta_value as $time ) {

			// 2.3.0: added check is shift is disabled
			if ( !isset( $time['disabled'] ) || ( 'yes' != $time['disabled'] ) ) {

				if ( $time['day'] === $cur_day ) {
					$cur_shift = strtotime( $cur_date . ' ' . $time['start_hour'] . ':' . $time['start_min'] . ':00 ' . $time['start_meridian'] );
					$end_shift = strtotime( $cur_date . ' ' . $time['end_hour'] . ':' . $time['end_min'] . ':00 ' . $time['end_meridian'] );
				} else {
					if ( $cur_day_num < $days[$time['day']] ) {
						$day_diff = $days[$time['day']] - $cur_day_num;
						$cur_shift = strtotime( $cur_date . ' ' . $time['start_hour'] . ':' . $time['start_min'] . ':00 ' . $time['start_meridian'] ) + ( $day_diff * 86400 );
						$end_shift = strtotime( $cur_date . ' ' . $time['end_hour'] . ':' . $time['end_min'] . ':00 ' . $time['end_meridian'] ) + ( $day_diff * 86400 );
					} else {
						$day_diff = $cur_day_num + $days[$time['day']] + 1;
						$cur_shift = strtotime( $cur_date . ' ' . $time['start_hour'] . ':' . $time['start_min'] . ':00 ' . $time['start_meridian'] ) + ( $day_diff * 86400 );
						$end_shift = strtotime( $cur_date . ' ' . $time['end_hour'] . ':' . $time['end_min'] . ':00 ' . $time['end_meridian'] ) + ( $day_diff * 86400 );
					}
				}

				// if the shift occurs later than the current time, we want it
				if ( $cur_shift >= $now ) {
					$show_ids[$cur_shift . '|' . $end_shift] = $shift->post_id;
					// 2.2.4: set encore ID array to pass back
					if ( isset( $time['encore'] ) && 'on' === $time['encore'] ) {
						$encore_ids[$cur_shift . '|' . $end_shift] = $shift->post_id;
					}
				}
			}
		}
	}

	// sort the shows by start time
	ksort( $show_ids );

	// merge in the overrides array
	foreach ( $show_ids as $s => $id ) {
		foreach ( $overrides as $o => $info ) {
			$stime = explode( '|', $s );
			$otime = explode( '|', $o );

			if ( $otime[0] <= $stime[1] ) { //check if an override starts before a show ends
				if ( $otime[1] > $stime[0] ) { //and it ends after the show begins (so we're not pulling overrides that are already over based on current time)
					unset( $show_ids[$s] ); // this show is overriden... drop it
				}
			}
		}
	}

	// Fallback function if the PHP Server does not have the array_replace function (i.e. prior to PHP 5.3)
	if ( !function_exists( 'array_replace' ) ) {

		function array_replace() {
			$array = array();
			$n = func_num_args();

			while ( $n -- > 0 ) {
				$array += func_get_arg( $n );
			}

			return $array;
		}
	}

	$combined = array_replace( $show_ids, $overrides );
	ksort( $combined );

	// grab the number of shows from the list the user wants to display
	$combined = array_slice( $combined, 0, $limit, true );

	// fetch detailed show information
	foreach ( $combined as $timestamp => $id ) {
		if ( !is_array( $id ) ) {
			$shows['all'][$timestamp] = get_post( $id );
		} else {
			$id['type'] = 'override';
			$shows['all'][$timestamp] = $id;
		}
	}
	$shows['type'] = 'shows';
	// 2.2.4: set encore IDs to pass back
	$shows['encore'] = $encore_ids;

	// return the information
	return $shows;
}

// --- get the most recently entered song ---
// (used in DJ Widget, dj-widget shortcode, Playlist Widget, now-playing shortcode)
function radio_station_get_now_playing() {

	// --- get the currently playing show ---
	// 2.3.0: added to prevent playlist/show mismatch!
	$current_show = radio_station_get_current_show();
	if ( !$current_show ) {
		return false;
	}
	if ( isset( $current_show['override'] ) && $current_show['override'] ) {
		$playlist = apply_filters( 'radio_station_override_now_playing', false, $current_show['override'] );
		return $playlist;
	}
	$show_id = $current_show['show'];
	$shifts = $current_show['shifts'];

	// --- grab the most recent playlist for the current show ---
	$args = array(
		'numberposts' => 1,
		'offset'      => 0,
		'orderby'     => 'post_date',
		'order'       => 'DESC',
		'post_type'   => RADIO_STATION_PLAYLIST_SLUG,
		'post_status' => 'publish',
		'meta_query'  => array(
			array(
				'meta_key'   => 'playlist_show_id',
				'meta_value' => $show_id,
				'compare'    => '=',
			),
		),
	);
	$playlist_post = get_posts( $args );

	$playlist = false;
	if ( $playlist_post ) {

		// --- fetch the tracks for the playlist ---
		// 2.3.0: added singular argument to true
		$songs = get_post_meta( $playlist_post[0]->ID, 'playlist', true );

		if ( !empty( $songs ) ) {

			// --- aplit off tracks marked as queued ---
			$queued = array();
			foreach ( $songs as $i => $song ) {
				if ( 'queued' == $songs['playlist_entry_status'] ) {
					$queued[] = $song;
					unset( $songs[$i] );
				}
			}

			// --- get the track list for display  ---
			// 2.3.0: return full playlist instead of just last song
			$playlist = array( 'tracks' => $songs );
			$playlist['queued'] = $queued;
			$playlist['latest'] = array_pop( $songs );

			// --- add show and playlist data ---
			// 2.3.0: add IDs and URLs instead of just playlist URL
			$playlist['show'] = $show_id;
			$playlist['show_url'] = get_permalink( $show_id );
			$playlist['playlist'] = $playlist[0]->ID;
			$playlist['playlist_url'] = get_permalink( $playlist[0]->ID );

		}
	}

	// --- filter and return tracks ---
	$playlist = apply_filters( 'radio_station_show_now_playing', $playlist, $show_id );

	return $playlist;
}

// --- get any schedule overrides for today's date ---
// (used in radio_station_dj_get_current, radio_station_dj_get_next)
// If currenthour is true, only overrides that are in effect NOW will be returned
// 2.3.0: add date argument to allow getting specific data overrides
function radio_station_master_get_overrides( $currenthour = false, $date = false ) {

	global $wpdb;

	$now = strtotime( current_time( 'mysql' ) );
	// 2.3.0: check if date argument supplied
	if ( !$date ) {
		$date = date( 'Y-m-d', $now );
	}
	$sql_date = $wpdb->esc_like( $date );
	$sql_date = '%' . $sql_date . '%';
	$show_shifts = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT meta.post_id
			FROM {$wpdb->postmeta} AS meta
			WHERE meta_key = 'show_override_sched' AND
				meta_value LIKE %s",
			$sql_date
		)
	);

	$scheds = array();
	if ( $show_shifts ) {
		foreach ( $show_shifts as $shift ) {

			$next_sched = get_post_meta( $shift->post_id, 'show_override_sched', false );
			$time = $next_sched[0];

			if ( $currenthour ) {

				// --- convert to 24 hour time ---
				$check = $time;
				$time = radio_station_convert_time( $time );

				// --- compare to the current timestamp ---
				if ( ( $time['start_timestamp'] <= $now ) && ( $time['end_timestamp'] >= $now ) ) {
					$title = get_the_title( $shift->post_id );
					$scheds[] = array(
						'post_id' => $shift->post_id,
						'title'   => $title,
						'sched'   => $time,
					);
				} else {
					continue;
				}
			} else {
				$title = get_the_title( $shift->post_id );
				$sched = get_post_meta( $shift->post_id, 'show_override_sched', false );
				$scheds[] = array(
					'post_id' => $shift->post_id,
					'title'   => $title,
					'sched'   => $sched[0],
				);
			}
		}
	}

	return $scheds;
}

// --- fetch all blog posts for a show's DJs ---
// [Deprecated] replaced and no longer used
function radio_station_myplaylist_get_posts_for_show( $show_id = null, $title = '', $limit = 10 ) {

	global $wpdb;

	// do not return anything if we don't have a show
	if ( !$show_id ) {
		return false;
	}

	$fetch_posts = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT meta.post_id
			FROM {$wpdb->postmeta} AS meta
			WHERE meta.meta_key = 'post_showblog_id' AND
  			meta.meta_value = %d",
			$show_id
		)
	);

	$blog_array = array();
	$blogposts = array();
	foreach ( $fetch_posts as $f ) {
		$blog_array[] = $f->post_id;
	}

	if ( $blog_array ) {

		// 2.2.8: fix to implode blog array to string
		// 2.3.0: allow for getting without limit
		$query = $wpdb->prepare(
			"SELECT posts.ID, posts.post_title
			FROM {$wpdb->posts} AS posts
			WHERE posts.ID IN(%s) AND
				posts.post_status = 'publish'
			ORDER BY posts.post_date DESC",
			implode( ',', $blog_array )
		);
		if ( $limit > 0 ) {
			$query .= $wpdb->prepare( " LIMIT %d", $limit );
		}
		$blogposts = $wpdb->get_results( $query );
	}

	$output = '';

	$output .= '<div id="myplaylist-blog-posts">';
	$output .= '<h3>' . $title . '</h3>';
	$output .= '<ul class="myplaylist-post-list">';
	foreach ( $blogposts as $p ) {
		$output .= '<li><a href="' . get_permalink( $p->ID ) . '">' . $p->post_title . '</a></li>';
	}
	$output .= '</ul>';
	$output .= '</div>';

	// if the blog archive page has been created, add a link to the archive for this show
	$page = $wpdb->get_results(
		"SELECT meta.post_id
		FROM {$wpdb->postmeta} AS meta
		WHERE meta.meta_key = '_wp_page_template' AND
			meta.meta_value = 'show-blog-archive-template.php'
		LIMIT 1"
	);

	if ( $page ) {
		$blog_archive = get_permalink( $page[0]->post_id );
		$params = array( 'show_id' => $show_id );
		$blog_archive = add_query_arg( $params, $blog_archive );

		$output .= '<a href="' . esc_url( $blog_archive ) . '">' . esc_html( __( 'More Show Blog Posts', 'radio-station' ) ) . '</a>';
	}

	return $output;
}
