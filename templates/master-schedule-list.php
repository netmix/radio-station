<?php
/**
 * Template for master schedule shortcode list style.
 */

// echo '<span style="display:none;">LIST ATTS: ' . print_r( $atts, true ) . '</span>';

// --- get all the required info ---
$hours = radio_station_get_hours();
$now = radio_station_get_now();
$date = radio_station_get_time( 'date', $now );
$today = radio_station_get_time( 'day', $now );

// --- check if start date is set ---
// 2.3.3.9: added for non-now schedule displays
if ( isset( $atts['start_date'] ) && $atts['start_date'] ) {
	$start_date = $atts['start_date'];
	// --- force display of date and month ---
	$atts['display_date'] = ( !$atts['display_date'] ) ? '1' : $atts['display_date'];
	$atts['display_month'] = ( !$atts['display_month'] ) ? 'short' : $atts['display_month'];
} else {
	// 2.3.3.9: set start date to current date
	$start_date = $date;
}
$start_time = radio_station_to_time( $start_date . ' 00:00:00' );

// --- set shift time formats ---
// 2.3.2: set time formats early
// 2.4.0.6: add filter for shift times separator
$shifts_separator = __( 'to', 'radio-station' );
$shifts_separator = apply_filters( 'radio_station_show_times_separator', $shifts_separator, 'schedule-list' );
$time_separator = ':';
$time_separator = apply_filters( 'radio_station_time_separator', $time_separator, 'schedule-list' );
if ( 24 == (int) $atts['time_format'] ) {
	$start_data_format = $end_data_format = 'H' . $time_separator . 'i';
} else {
	$start_data_format = $end_data_format = 'g' . $time_separator . 'i a';
}
$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'schedule-list', $atts );
$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'schedule-list', $atts );

// --- get schedule days and dates ---
// 2.3.2: allow for start day attibute
// 2.3.3.5: use the start_day value for getting the current schedule
if ( isset( $atts['start_day'] ) && $atts['start_day'] ) {
	$start_day = $atts['start_day'];
} else {
	// 2.3.3.5: add filter for changing start day (to accept 'today')
	$start_day = apply_filters( 'radio_station_schedule_start_day', false, 'list' );
}
if ( $start_day ) {
	$schedule = radio_station_get_current_schedule( $start_time, $start_day );
} elseif ( $start_time != $now ) {
	// 2.3.3.9: load current or time-specific schedule
	$schedule = radio_station_get_current_schedule( $start_time );
} else {
	$schedule = radio_station_get_current_schedule();
}
$weekdays = radio_station_get_schedule_weekdays( $start_day );
$weekdates = radio_station_get_schedule_weekdates( $weekdays, $start_time );

// --- filter show avatar size ---
// 2.3.3.5: fix to incorrect context value (tabs)
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail', 'list' );

// --- filter excerpt length and more ---
if ( $atts['show_desc'] ) {
	$length = apply_filters( 'radio_station_schedule_list_excerpt_length', false );
	$more = apply_filters( 'radio_station_schedule_list_excerpt_more', '[&hellip;]' );
}

// --- set list info key order ---
// 2.3.3.8: added for possible info rearrangement
$infokeys = array( 'avatar', 'title', 'hosts', 'times', 'encore', 'file', 'genres', 'excerpt', 'custom' );
$infokeys = apply_filters( 'radio_station_schedule_list_info_order', $infokeys );

// --- start list schedule output ---
// 2.5.0: append instance to element ID
$list = '<ul id="master-list-' . esc_attr( $instance ) . '" class="master-list">' . "\n";

$tcount = 0;
// 2.3.0: loop weekdays instead of legacy master list
// 2.5.0: set odd/even variable
$oddeven = 'even';
foreach ( $weekdays as $weekday ) {

	// --- maybe skip all days but those specified ---
	// 2.3.2: improve days attribute checking logic
	$skip_day = false;
	if ( $atts['days'] ) {
		$days = explode( ',', $atts['days'] );
		$found_day = false;
		foreach ( $days as $day ) {
			$day = trim( $day );
			// 2.3.2: allow for numeric days (0=sunday to 6=saturday)
			if ( is_numeric( $day ) && ( $day > -1 ) && ( $day < 7 ) ) {
				$day = radio_station_get_weekday( $day );
			}
			if ( trim( strtolower( $day ) ) == strtolower( $weekday ) ) {
				$found_day = true;
			}
		}
		if ( !$found_day ) {
			$skip_day = true;
		}
	}

	if ( !$skip_day ) {

		// 2.3.3.6: set next and previous day for split shift IDs
		$nextday = radio_station_get_next_day( $weekday );
		$prevday = radio_station_get_previous_day( $weekday );

		// 2.3.2: move up time calculations for optional date display
		$day_start_time = radio_station_to_time( $weekdates[$weekday] . ' 00:00' );
		$day_end_time = $day_start_time + ( 24 * 60 * 60 );

		// 2.3.2: added check for short/long day display attribute
		if ( !in_array( $atts['display_day'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_day'] = 'long';
		}
		if ( 'short' == $atts['display_day'] ) {
			$display_day = radio_station_translate_weekday( $weekday, true );
		} elseif ( ( 'full' == $atts['display_day'] ) || ( 'long' == $atts['display_day'] ) ) {
			$display_day = radio_station_translate_weekday( $weekday, false );
		}

		// 2.3.2: add attribute for date subheading format (see PHP date() format)
		// $subheading = date( 'jS M', strtotime( $weekdate ) );
		if ( $atts['display_date'] ) {
			$date_subheading = radio_station_get_time( $atts['display_date'], $day_start_time );
		} else {
			$date_subheading = radio_station_get_time( 'j', $day_start_time );
		}

		// 2.3.2: add attribute for short or long month display
		$month = radio_station_get_time( 'F', $day_start_time );
		if ( $atts['display_month'] && !in_array( $atts['display_month'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_month'] = 'short';
		}
		if ( ( 'long' == $atts['display_month'] ) || ( 'full' == $atts['display_month'] ) ) {
			$date_subheading .= ' ' . radio_station_translate_month( $month, false );
		} elseif ( 'short' == $atts['display_month'] ) {
			$date_subheading .= ' ' . radio_station_translate_month( $month, true );
		}

		// 2.3.2: add classes to day
		// 2.5.0: add odd/even class day
		$classes = array( 'master-list-day' );
		$oddeven = ( 'even' == $oddeven ) ? 'odd' : 'even';
		$classes[] = $oddeven . '-day';
		$weekdate = $weekdates[$weekday];
		if ( $weekdate == $date ) {
			$classes[] = 'current-day';
		}
		$classlist = implode( ' ', $classes );

		// 2.2.2: use translate function for weekday string
		// 2.3.2: added optional display-date attribute
		$display_day = radio_station_translate_weekday( $weekday );
		$list .= '<li id="list-header-' . esc_attr( strtolower( $weekday ) ) . '" class="' . esc_attr( $classlist ) . '" >' . "\n";
		$list .= '<span class="master-list-day-name"';
		if ( !$atts['display_date'] ) {
			$list .= ' title="' . esc_attr( $date_subheading ) . '"';
		}
		$list .= '>' . esc_html( $display_day ) . '</span>' . "\n";
		if ( $atts['display_date'] ) {
			$list .= ' <span class="master-list-day-date">' . esc_html( $date_subheading ) . '</span>' . "\n";
		}

		// 2.3.2: add output of day start and end times
		// 2.3.2: replace strtotime with to_time for timezones
		$list .= '<span class="rs-time rs-start-time" data="' . esc_attr( $day_start_time ) . '"></span>' . "\n";
		$list .= '<span class="rs-time rs-end-time" data="' . esc_attr( $day_end_time ) . '"></span>' . "\n";

		// --- open day list ---
		$list .= '<ul class="master-list-day-' . esc_attr( strtolower( $weekday ) ) . '-list">' . "\n";

		// --- get shifts for this day ---
		if ( isset( $schedule[$weekday] ) ) {
			$shifts = $schedule[$weekday];
		} else {
			$shifts = array();
		}

		// 2.3.0: loop schedule day shifts instead of hours and minutes
		if ( count( $shifts ) > 0 ) {
			foreach ( $shifts as $shift ) {

				// 2.3.3.8: reset info array
				$show = $shift['show'];
				$show_id = $show['id'];
				$info = array();
				$split_id = false;

				// --- convert shift time data ---
				// 2.3.2: replace strtotime with to_time for timezones
				// 2.3.2: fix to convert to 24 hour format first
				// 2.3.2: fix timestamps for midnight/split shifts
				// $shift_start = radio_station_convert_shift_time( $shift['start'] );
				// $shift_end = radio_station_convert_shift_time( $shift['end'] );
				// $shift_start_time = radio_station_to_time( $shift['day'] . ' ' . $shift_start );
				// $shift_end_time = radio_station_to_time( $shift['day'] . ' ' . $shift_end );
				// if ( $shift_end_time < $shift_start_time ) {
				//  $shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
				// }
				if ( '00:00 am' == $shift['start'] ) {
					$shift_start_time = radio_station_to_time( $weekdate . ' 00:00' );
				} else {
					$shift_start = radio_station_convert_shift_time( $shift['start'] );
					$shift_start_time = radio_station_to_time( $weekdate . ' ' . $shift_start );
				}
				if ( ( '11:59:59 pm' == $shift['end'] ) || ( '12:00 am' == $shift['end'] ) ) {
					$shift_end_time = radio_station_to_time( $weekdate . ' 23:59:59' ) + 1;
				} else {
					$shift_end = radio_station_convert_shift_time( $shift['end'] );
					$shift_end_time = radio_station_to_time( $weekdate . ' ' . $shift_end );
				}

				// --- get split shift real start and end times ---
				// 2.3.2: added for shift display output
				$real_shift_start = $real_shift_end = false;
				if ( isset( $shift['split'] ) && $shift['split'] ) {
					if ( isset( $shift['real_start'] ) ) {
						$real_shift_start = radio_station_convert_shift_time( $shift['real_start'] );
						$real_shift_start = radio_station_to_time( $weekdate . ' ' . $real_shift_start ) - ( 24 * 60 * 60 );
						$split_id = strtolower( $prevday . '-' . $weekday );
					} elseif ( isset( $shift['real_end'] ) ) {
						$real_shift_end = radio_station_convert_shift_time( $shift['real_end'] );
						$real_shift_end = radio_station_to_time( $weekdate . ' ' . $real_shift_end ) + ( 24 * 60 * 60 );
						$split_id = strtolower( $weekday . '-' . $nextday );
					}
				}

				// 2.3.0: filter show link by show and context
				$show_link = false;
				if ( $atts['show_link'] ) {
					$show_link = $show['url'];
				}
				$show_link = apply_filters( 'radio_station_schedule_show_link', $show_link, $show_id, 'list' );

				// --- list item classes ---
				// 2.3.0: add genre classes for highlighting
				$classes = array( 'master-list-day-item' );
				$terms = wp_get_post_terms( $show_id, RADIO_STATION_GENRES_SLUG, array() );
				if ( $terms && ( count( $terms ) > 0 ) ) {
					foreach ( $terms as $term ) {
						// 2.5.0: add genre- prefix to genre terms
						// $classes[] = strtolower( $term->slug );
						$classes[] = 'genre-' . strtolower( $term->slug );
					}
				}
				// 2.3.2: check for now playing shift
				if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {
					$classes[] = 'nowplaying';
				}
				// 2.3.3.6: add overnight split ID for highlighting
				if ( $split_id ) {
					$classes[] = 'overnight';
					$classes[] = 'split-' . $split_id;
				}

				// --- open show list item ---
				$classlist = implode( ' ', $classes );
				$list .= '<li class="' . esc_attr( $classlist ) . '">' . "\n";

				// --- show avatar ---
				if ( $atts['show_image'] ) {
					// 2.3.0: filter show avatar via show ID and context
					$show_avatar = radio_station_get_show_avatar( $show_id, $avatar_size );
					$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show_id, 'list' );
					if ( $show_avatar ) {
						$avatar = '<div class="show-image">' . "\n";
						if ( $show_link ) {
							$avatar .= '<a href="' . esc_url( $show_link ) . '">';
						}
						$avatar .= $show_avatar;
						if ( $show_link ) {
							$avatar .= '</a>';
						}
						$avatar .= '</div>' . "\n";
						$avatar = apply_filters( 'radio_station_schedule_show_avatar_display', $avatar, $show_id, 'list' );
						if ( ( '' != $avatar ) && is_string( $avatar ) ) {
							$info['avatar'] = $avatar;
						}
					}
				}

				// --- show title ---
				if ( $show_link ) {
					$show_title = '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
				} else {
					$show_title = esc_html( $show['name'] );
				}
				$title = '<span class="show-title">' . "\n";
					$title .= $show_title;
				$title .= '</span>' . "\n";
				$title = apply_filters( 'radio_station_schedule_show_title', $title, $show_id, 'list' );
				if ( ( '' != $title ) && is_string( $title ) ) {
					$info['title'] = $title;
				}
				// 2.3.3.9: allow for admin edit link
				$edit_link = apply_filters( 'radio_station_show_edit_link', '', $show_id, $shift['id'], 'list' );
				if ( '' != $edit_link ) {
					if ( isset( $info['title'] ) ) {
						$info['title'] .= $edit_link;
					} else {
						$info['title'] = $edit_link;
					}
				}

				// --- show hosts ---
				// 2.3.0: changed from show_djs
				if ( $atts['show_hosts'] ) {

					$show_hosts = '';
					if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

						$count = 0;
						$host_count = count( $show['hosts'] );
						$show_hosts .= '<span class="show-dj-names-leader">';
							$show_hosts .= esc_html( __( 'with', 'radio-station' ) );
						// 2.3.2: fix variable to close span tag
						$show_hosts .= ' </span>' . "\n";

						foreach ( $show['hosts'] as $host ) {
							$count++;
							// 2.3.0: added link_hosts attribute check
							if ( $atts['link_hosts'] && !empty( $host['url'] ) ) {
								$show_hosts .= '<a href="' . esc_url( $host['url'] ) . '">' . esc_html( $host['name'] ) . '</a>' . "\n";
							} else {
								$show_hosts .= esc_html( $host['name'] );
							}

							if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
								 || ( ( $host_count > 2 ) && ( ( $count === $host_count - 1 ) ) ) ) {
								$show_hosts .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
							} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
								$show_hosts .= ', ';
							}
						}
					}

					// 2.3.3.5: fix to incorrect context value (tabs)
					$show_hosts = apply_filters( 'radio_station_schedule_show_hosts', $show_hosts, $show_id, 'list' );
					if ( $show_hosts ) {
						$hosts = '<div class="show-dj-names show-host-names">' . "\n";
							$hosts .= $show_hosts;
						$hosts .= '</div>' . "\n";
						$hosts = apply_filters( 'radio_station_schedule_show_hosts_display', $hosts, $show_id, 'list' );
						if ( ( '' != $hosts ) && is_string( $hosts ) ) {
							$info['hosts'] = $hosts;
						}
					}
				}

				// --- show time ---
				if ( $atts['show_times'] ) {

					// --- get start and end times ---
					// 2.3.2: maybe use real start and end times
					if ( $real_shift_start ) {
						$start = radio_station_get_time( $start_data_format, $real_shift_start );
					} else {
						$start = radio_station_get_time( $start_data_format, $shift_start_time );
					}
					if ( $real_shift_end ) {
						$end = radio_station_get_time( $end_data_format, $real_shift_end );
					} else {
						$end = radio_station_get_time( $end_data_format, $shift_end_time );
					}
					$start = radio_station_translate_time( $start );
					$end = radio_station_translate_time( $end );

					// 2.3.0: filter show time by show and context
					// 2.4.0.6: used filtered shift times separator
					$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
					$show_time .= '<span class="rs-sep rs-shift-sep"> ' . esc_html( $shifts_separator ) . ' </span>' . "\n";
					$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";

				} else {

					// 2.3.3.8: added for now playing check
					$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '"></span>' . "\n";
					$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '"></span>' . "\n";

				}

				// 2.3.3.8: moved filter out and added display filter
				// 2.3.3.9: added tcount argument to filter
				$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show_id, 'list', $shift, $tcount );
				$times = '<div class="show-time" id="show-time-' . esc_attr( $tcount ) . '"';
				// note: unlike other display filters this hides/shows times rather than string filtering
				$display = apply_filters( 'radio_station_schedule_show_time_display', true, $show_id, 'list', $shift );
				if ( !$display ) {
					$times .= ' style="display:none;"';
				}
				$times .= '>' . $show_time . '</div>' . "\n";
				// 2.3.3.9: added internal spans for user time
				// 2.4.0.6: use filtered shift times separator
				$times .= '<div class="show-user-time" id="show-user-time-' . esc_attr( $tcount ) . '">';
					$times .= '[<span class="rs-time rs-start-time"></span>' . "\n";
					$times .= '<span class="rs-sep"> ' . esc_html( $shifts_separator ) . ' </span>' . "\n";
					$times .= '<span class="rs-time rs-end-time"></span>]' . "\n";
				$times .= '</div>' . "\n";
				$info['times'] = $times;
				$tcount++;

				// --- encore ---
				if ( $atts['show_encore'] ) {
					// 2.3.0: filter encore by show and context ---
					if ( isset( $shift['encore'] ) ) {
						$show_encore = $shift['encore'];
					} else {
						$show_encore = false;
					}
					$show_encore = apply_filters( 'radio_station_schedule_show_encore', $show_encore, $show_id, 'list' );
					if ( 'on' == $show_encore ) {
						$encore = '<div class="show-encore">';
							$encore .= esc_html( __( 'encore airing', 'radio-station' ) );
						$encore .= '</div>' . "\n";
						$encore = apply_filters( 'radio_station_schedule_show_encore_display', $encore, $show_id, 'list' );
						if ( ( '' != $encore ) && is_string( $encore ) ) {
							$info['encore'] = $encore;
						}
					}
				}

				// --- show file ---
				if ( $atts['show_file'] ) {
					// 2.3.0: filter show file by show and context
					// 2.3.2: check disable download meta
					// 2.3.3: fix to incorrect filter name
					// 2.3.3.8: add filter for show file link
					// 2.3.3.8: add filter for show file anchor
					$show_file = get_post_meta( $show_id, 'show_file', true );
					$show_file = apply_filters( 'radio_station_schedule_show_file', $show_file, $show_id, 'list' );
					$disable_download = get_post_meta( $show_id, 'show_download', true );
					if ( $show_file && !empty( $show_file ) && !$disable_download ) {
						$anchor = __( 'Audio File', 'radio-station' );
						$anchor = apply_filters( 'radio_station_schedule_show_file_anchor', $anchor, $show_id, 'list' );
						$file = '<div class="show-file">' . "\n";
							$file .= '<a href="' . esc_url( $show_file ) . '">';
								$file .= esc_html( $anchor );
							$file .= '</a>' . "\n";
						$file .= '</div>' . "\n";
						$file = apply_filters( 'radio_station_schedule_show_file_display', $file, $show_file, $show_id, 'list' );
						if ( ( '' != $file ) && is_string( $file ) ) {
							$info['file'] = $file;
						}
					}
				}

				// --- show genres ---
				// (defaults to on)
				// 2.3.0: add genres to list view
				if ( $atts['show_genres'] ) {
					$genres = '<div class="show-genres">' . "\n";
					$show_genres = array();
					if ( count( $terms ) > 0 ) {
						$genres .= esc_html( __( 'Genres', 'radio-station' ) ) . ': ';
						foreach ( $terms as $term ) {
							$show_genres[] = '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>' . "\n";
						}
						$genres .= implode( ', ', $show_genres );
					}
					$genres .= '</div>' . "\n";
					$genres = apply_filters( 'radio_station_schedule_show_genres_display', $genres, $show_id, 'list' );
					if ( ( '' != $genres ) && is_string( $genres ) ) {
						$info['genres'] = $genres;
					}
				}

				// --- show description ---
				if ( $atts['show_desc'] ) {

					$show_post = get_post( $show_id );
					$permalink = get_permalink( $show_id );

					// --- get show excerpt ---
					if ( !empty( $show_post->post_excerpt ) ) {
						$show_excerpt = $show_post->post_excerpt;
						$show_excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
					} else {
						$show_excerpt = radio_station_trim_excerpt( $show_post->post_content, $length, $more, $permalink );
					}
					$show_excerpt = apply_filters( 'radio_station_schedule_show_excerpt', $show_excerpt, $show_id, 'list' );

					// --- set excerpt display ---
					$excerpt = '<div class="show-desc">' . "\n";
					$excerpt .= $show_excerpt . "\n";
					$excerpt .= '</div>' . "\n";
					if ( ( '' != 'excerpt' ) && is_string( $excerpt ) ) {
						$info['excerpt'] = $excerpt;
					}
				}

				// --- custom info section ---
				// 2.3.3.8: allow for custom HTML to be added
				$custom = apply_filters( 'radio_station_schedule_show_custom_display', '', $show_id, 'list' );
				if ( ( '' != $custom ) && is_string( $custom ) ) {
					$info['custom'] = $custom;
				}

				// --- add item info according to key order ---
				// 2.3.3.8: added for possible order rearrangement
				foreach ( $infokeys as $infokey ) {
					if ( isset( $info[$infokey] ) ) {
						$list .= $info[$infokey];
					}
				}

				$list .= '</li>' . "\n";
			}
		}
		$list .= '</ul>' . "\n";

		// --- close master list day item ---
		$list .= '</li>' . "\n";
	}
}

// --- close master list ---
$list .= '</ul>' . "\n";

// --- hidden iframe for schedule reloading ---
$list .= '<iframe src="javascript:void(0);" id="schedule-list-loader" name="schedule-list-loader" style="display:none;"></iframe>' . "\n";

// TODO: test wp_kses on output ?
echo $list;
