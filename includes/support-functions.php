<?php
/*
* Support functions for shortcodes and widgets
* Author: Nikki Blight
* Since: 2.0.14
*/

// --- get only the currently relevant schedule ---
function radio_station_current_schedule( $scheds = array() ) {

	$now     = current_time( 'timestamp' );
	$current = array();

	foreach ( $scheds as $sched ) {

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

	return $current;
}

// --- convert shift times to 24-hour and timestamp formats for comparisons ---
function radio_station_convert_time( $time = array() ) {

	if ( empty( $time ) ) {
		return false;
	}

	$now      = strtotime( current_time( 'mysql' ) );
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
	$now     = strtotime( current_time( 'mysql' ) );
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
		if ( ! is_array( $shift->meta_value ) ) {
			$shift->meta_value = array();
		}

		foreach ( $shift->meta_value as $time ) {

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
				$time['end_timestamp']   = $time['end_timestamp'] - 86400;

				// compare to the current timestamp
				if ( ( $time['start_timestamp'] <= $now ) && ( $time['end_timestamp'] >= $now ) ) {
					$show_ids[] = $shift->post_id;
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
function radio_station_dj_get_next( $limit = 1 ) {

	global $wpdb;

	// get the various times/dates we need
	$cur_day     = date( 'l', strtotime( current_time( 'mysql' ) ) );
	$cur_day_num = date( 'N', strtotime( current_time( 'mysql' ) ) );
	$cur_date    = date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) );
	$now         = strtotime( current_time( 'mysql' ) );
	$shows       = array();

	// first check to see if there are any shift overrides
	$check     = radio_station_master_get_overrides();
	$overrides = array();

	if ( $check ) {

		foreach ( $check as $i => $p ) {

			$p['sched'] = radio_station_convert_time( $p['sched'] );

			// compare to the current timestamp
			if ( ( $p['sched']['start_timestamp'] <= $now ) && ( $p['sched']['end_timestamp'] >= $now ) ) {
				//show is on now, so we don't need it listed under upcoming
				//$overrides[$p['sched']['start_timestamp'].'|'.$p['sched']['end_timestamp']] = $p;
				unset( $check[ $i ] );
			} elseif ( ( $p['sched']['start_timestamp'] > $now ) && ( $p['sched']['end_timestamp'] > $now ) ) {
				// show is on later today
				$overrides[ $p['sched']['start_timestamp'] . '|' . $p['sched']['end_timestamp'] ] = $p;
			} else {
				// show is already over and we don't need it
				unset( $check[ $i ] );
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

	$show_ids = array();

	foreach ( $show_shifts as $shift ) {

		$shift->meta_value = maybe_unserialize( $shift->meta_value );

		// if a show has no shifts, unserialize() will return false instead of an empty array...
		// fix that to prevent errors in the foreach loop.
		if ( ! is_array( $shift->meta_value ) ) {
			$shift->meta_value = array();
		}

		$encore_ids = array();
		$days       = array(
			'Sunday'    => 7,
			'Monday'    => 1,
			'Tuesday'   => 2,
			'Wednesday' => 3,
			'Thursday'  => 4,
			'Friday'    => 5,
			'Saturday'  => 6,
		);
		foreach ( $shift->meta_value as $time ) {

			if ( $time['day'] === $cur_day ) {
				$cur_shift = strtotime( $cur_date . ' ' . $time['start_hour'] . ':' . $time['start_min'] . ':00 ' . $time['start_meridian'] );
				$end_shift = strtotime( $cur_date . ' ' . $time['end_hour'] . ':' . $time['end_min'] . ':00 ' . $time['end_meridian'] );
			} else {
				if ( $cur_day_num < $days[ $time['day'] ] ) {
					$day_diff  = $days[ $time['day'] ] - $cur_day_num;
					$cur_shift = strtotime( $cur_date . ' ' . $time['start_hour'] . ':' . $time['start_min'] . ':00 ' . $time['start_meridian'] ) + ( $day_diff * 86400 );
					$end_shift = strtotime( $cur_date . ' ' . $time['end_hour'] . ':' . $time['end_min'] . ':00 ' . $time['end_meridian'] ) + ( $day_diff * 86400 );
				} else {
					$day_diff  = $cur_day_num + $days[ $time['day'] ] + 1;
					$cur_shift = strtotime( $cur_date . ' ' . $time['start_hour'] . ':' . $time['start_min'] . ':00 ' . $time['start_meridian'] ) + ( $day_diff * 86400 );
					$end_shift = strtotime( $cur_date . ' ' . $time['end_hour'] . ':' . $time['end_min'] . ':00 ' . $time['end_meridian'] ) + ( $day_diff * 86400 );
				}
			}

			// if the shift occurs later than the current time, we want it
			if ( $cur_shift >= $now ) {
				$show_ids[ $cur_shift . '|' . $end_shift ] = $shift->post_id;
				// 2.2.4: set encore ID array to pass back
				if ( isset( $time['encore'] ) && 'on' === $time['encore'] ) {
					$encore_ids[ $cur_shift . '|' . $end_shift ] = $shift->post_id;
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
					unset( $show_ids[ $s ] ); // this show is overriden... drop it
				}
			}
		}
	}

	// Fallback function if the PHP Server does not have the array_replace function (i.e. prior to PHP 5.3)
	if ( ! function_exists( 'array_replace' ) ) {

		function array_replace() {
			$array = array();
			$n     = func_num_args();

			while ( $n-- > 0 ) {
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
		if ( ! is_array( $id ) ) {
			$shows['all'][ $timestamp ] = get_post( $id );
		} else {
			$id['type']                 = 'override';
			$shows['all'][ $timestamp ] = $id;
		}
	}
	$shows['type'] = 'shows';
	// 2.2.4: set encore IDs to pass back
	$shows['encore'] = $encore_ids;

	// return the information
	return $shows;
}

// --- get the most recently entered song ---
function radio_station_myplaylist_get_now_playing() {

	// grab the most recent playlist
	$args = array(
		'numberposts' => 1,
		'offset'      => 0,
		'orderby'     => 'post_date',
		'order'       => 'DESC',
		'post_type'   => 'playlist',
		'post_status' => 'publish',
	);

	$playlist = get_posts( $args );

	// if there are no playlists saved, return nothing
	if ( ! $playlist ) {
		return false;
	}

	// fetch the tracks for each playlist from the wp_postmeta table
	$songs = get_post_meta( $playlist[0]->ID, 'playlist' );

	if ( ! empty( $songs[0] ) ) {
		// removed any entries that are marked as 'queued'
		foreach ( $songs[0] as $i => $entry ) {
			if ( 'queued' === $entry['playlist_entry_status'] ) {
				unset( $songs[0][ $i ] );
			}
		}

		// pop the last track off the list for display
		$most_recent = array_pop( $songs[0] );

		// get the permalink for the playlist so it can be displayed
		$most_recent['playlist_permalink'] = get_permalink( $playlist[0]->ID );

		return $most_recent;
	} else {
		return false;
	}
}

// --- fetch all blog posts for a show's DJs ---
function radio_station_myplaylist_get_posts_for_show( $show_id = null, $title = '', $limit = 10 ) {

	global $wpdb;

	// do not return anything if we don't have a show
	if ( ! $show_id ) {
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
	$blogposts  = array();
	foreach ( $fetch_posts as $f ) {
		$blog_array[] = $f->post_id;
	}

	if ( $blog_array ) {

		// 2.2.8: fix to implode blog array to string
		$blogposts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT posts.ID, posts.post_title
				FROM {$wpdb->posts} AS posts
				WHERE posts.ID IN(%s) AND
					posts.post_status = 'publish'
				ORDER BY posts.post_date DESC
				LIMIT %d",
				implode( ',', $blog_array ),
				$limit
			)
		);
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
		$params       = array( 'show_id' => $show_id );
		$blog_archive = add_query_arg( $params, $blog_archive );

		$output .= '<a href="' . $blog_archive . '">' . __( 'More Blog Posts', 'radio-station' ) . '</a>';
	}

	return $output;
}

// --- get any schedule overrides for today's date ---
// If currenthour is true, only overrides that are in effect NOW will be returned
function radio_station_master_get_overrides( $currenthour = false ) {

	global $wpdb;

	$now         = strtotime( current_time( 'mysql' ) );
	$date        = date( 'Y-m-d', $now );
	$sql_date    = $wpdb->esc_like( $date );
	$sql_date    = '%' . $sql_date . '%';
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
			$time       = $next_sched[0];

			if ( $currenthour ) {

				// convert to 24 hour time
				$check = array();
				$check = $time;

				$time = radio_station_convert_time( $time );

				// compare to the current timestamp
				if ( ( $time['start_timestamp'] <= $now ) && ( $time['end_timestamp'] >= $now ) ) {
					$title    = get_the_title( $shift->post_id );
					$scheds[] = array(
						'post_id' => $shift->post_id,
						'title'   => $title,
						'sched'   => $time,
					);
				} else {
					continue;
				}
			} else {
				$title    = get_the_title( $shift->post_id );
				$sched    = get_post_meta( $shift->post_id, 'show_override_sched', false );
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

// --- shorten a string to a set number of words ---
function radio_station_shorten_string( $string, $limit ) {

	$shortened = $string; // just in case of a problem

	$array = explode( ' ', $string );
	if ( count( $array ) <= $limit ) {
		// already at or under the limit
		$shortened = $string;
	} else {
		array_splice( $array, $limit );
		$shortened = implode( ' ', $array ) . ' ...';
	}
	return $shortened;
}

// --- translate weekday ---
// important note: translated individually as cannot translate a variable
// 2.2.7: use wp locale class to translate weekdays
function radio_station_translate_weekday( $weekday, $short = false ) {
	global $wp_locale;
	if ( $short ) {
		if ( 'Sun' === $weekday ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 0 ) );
		} elseif ( 'Mon' === $weekday ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 1 ) );
		} elseif ( 'Tue' === $weekday ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 2 ) );
		} elseif ( 'Wed' === $weekday ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 3 ) );
		} elseif ( 'Thu' === $weekday ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 4 ) );
		} elseif ( 'Fri' === $weekday ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 5 ) );
		} elseif ( 'Sat' === $weekday ) {
			$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( 6 ) );
		}
	} else {
		// 2.2.7: fix to typo for Tuesday
		if ( 'Sunday' === $weekday ) {
			$weekday = $wp_locale->get_weekday( 0 );
		} elseif ( 'Monday' === $weekday ) {
			$weekday = $wp_locale->get_weekday( 1 );
		} elseif ( 'Tuesday' === $weekday ) {
			$weekday = $wp_locale->get_weekday( 2 );
		} elseif ( 'Wednesday' === $weekday ) {
			$weekday = $wp_locale->get_weekday( 3 );
		} elseif ( 'Thurday' === $weekday ) {
			$weekday = $wp_locale->get_weekday( 4 );
		} elseif ( 'Friday' === $weekday ) {
			$weekday = $wp_locale->get_weekday( 5 );
		} elseif ( 'Saturday' === $weekday ) {
			$weekday = $wp_locale->get_weekday( 6 );
		}
	}
	return $weekday;
}

// --- translate month ---
// important note: translated individually as cannot translate a variable
// 2.2.7: use wp locale class to translate months
function radio_station_translate_month( $month, $short = false ) {
	global $wp_locale;
	if ( $short ) {
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
		}
	} else {
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
