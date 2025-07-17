<?php
/**
 * Template for master schedule shortcode tabs style.
 * ref: http://nlb-creations.com/2014/06/06/radio-station-tutorial-creating-a-tabbed-programming-schedule/
 */

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
$start_time = apply_filters( 'radio_station_schedule_start_time', $start_time, 'tabs' );

// --- set shift time formats ---
// 2.3.2: set time formats early
// 2.4.0.6: add filter for shift times separator
$shifts_separator = __( 'to', 'radio-station' );
$shifts_separator = apply_filters( 'radio_station_show_times_separator', $shifts_separator, 'schedule-tabs' );
$time_separator = ':';
$time_separator = apply_filters( 'radio_station_time_separator', $time_separator, 'schedule-tabs' );
if ( 24 == (int) $atts['time_format'] ) {
	$start_data_format = $end_data_format = 'H:i';
} else {
	$start_data_format = $end_data_format = 'g:i a';
}
$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'schedule-tabs', $atts );
$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'schedule-tabs', $atts );

// --- get schedule days and dates ---
// 2.3.2: allow for start day attribute
// 2.3.3.5: use the start_day value for getting the current schedule
if ( isset( $atts['start_day'] ) && $atts['start_day'] ) {
	$start_day = $atts['start_day'];
} else {
	// 2.3.3.5: add filter for changing start day (to accept 'today')
	$start_day = apply_filters( 'radio_station_schedule_start_day', false, 'tabs' );
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
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail', 'tabs' );

// --- filter excerpt length and more ---
if ( $atts['show_desc'] ) {
	$length = apply_filters( 'radio_station_schedule_tabs_excerpt_length', false );
	$more = apply_filters( 'radio_station_schedule_tabs_excerpt_more', '[&hellip;]' );
}

// --- filter arrows ---
$arrows = array( 'left' => '&#8249;', 'right' => '&#8250;', 'doubleleft' => '&#171;', 'doubleright' => '&#187;' );
$arrows = apply_filters( 'radio_station_schedule_arrows', $arrows, 'tabs' );

// --- set cell info key order ---
// 2.3.3.8: added for possible info rearrangement
$infokeys = array( 'title', 'hosts', 'times', 'encore', 'file', 'genres', 'custom' );
$infokeys = apply_filters( 'radio_station_schedule_tabs_info_order', $infokeys );

// --- reset loop variables ---
$tabs = $panels = '';
$tcount = 0;
$start_tab = false;

// 2.3.3.9: added filter for week loader control
$load_prev = apply_filters( 'radio_station_schedule_loader_control', '', 'tabs', 'left', $instance );
if ( '' != $load_prev ) {
	// 2.5.0: remove ID in favour of class
	$tabs .= '<li class="master-schedule-tabs-loader master-schedule-tabs-loader-left">' . "\n";
		$tabs .= $load_prev . "\n";
	$tabs .= '</li>' . "\n";
}

// --- start tabbed schedule output ---
// 2.3.0: loop weekdays instead of legacy master list
// 2.5.0: set odd/even day variable
$oddeven = 'even';
foreach ( $weekdays as $i => $weekday ) {

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

		// 2.3.2: set day start and end times
		// 2.3.2: replace strtotime with to_time for timezones
		// 2.4.0.4: adjust day start time for daylight saving
		$day_start_time = radio_station_to_time( $weekdates[$weekday] . ' 00:00' );
		$day_start_adjusted = $day_start_time + ( 2 * 60 * 60 );
		$day_end_time = $day_start_time + ( 24 * 60 * 60 );

		// 2.2.2: use translate function for weekday string
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
			// 2.3.3.5: allow for attribute to be set to 1 for default display
			if ( '1' == $atts['display_date'] ) {
				$date_subheading = radio_station_get_time( 'jS', $day_start_adjusted );
			} else {
				$date_subheading = radio_station_get_time( $atts['display_date'], $day_start_adjusted );
			}
		} else {
			$date_subheading = radio_station_get_time( 'j', $day_start_adjusted );
		}

		// 2.3.2: add attribute for short or long month display
		$month = radio_station_get_time( 'F', $day_start_adjusted );
		if ( $atts['display_month'] && !in_array( $atts['display_month'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_month'] = 'short';
		}
		if ( ( 'long' == $atts['display_month'] ) || ( 'full' == $atts['display_month'] ) ) {
			$date_subheading .= ' ' . radio_station_translate_month( $month, false );
		} elseif ( 'short' == $atts['display_month'] ) {
			$date_subheading .= ' ' . radio_station_translate_month( $month, true );
		}

		// --- set tab classes ---
		// 2.3.3.5: add extra class for starting tab
		// 2.5.0: added weekday specific class
		// 2.5.0: add odd/even day class
		$weekdate = $weekdates[$weekday];
		$classes = array( 'master-schedule-tabs-day', 'master-schedule-tabs-day-' . strtolower( $weekday ), 'day-' . $i );
		$oddeven = ( 'even' == $oddeven ) ? 'odd' : 'even';
		$classes[] = $oddeven . '-day';
		if ( !$start_tab ) {
			$classes[] = 'start-tab';
			$start_tab = true;
		}
		if ( $weekdate == $date ) {
			// $classes[] = 'selected-day';
			$classes[] = 'current-day';
			$classes[] = 'active-day-tab';
		}
		$classlist = implode( ' ', $classes );

		// 2.3.0: added left/right arrow responsive controls
		// 2.3.1: added (negative) return to arrow onclick functions
		$tabs .= '<li id="master-schedule-tabs-header-' . esc_attr( strtolower( $weekday ) ) . '" class="' . esc_attr( $classlist ) . '">' . "\n";
			$tabs .= '<div class="shift-left-arrow">' . "\n";
				$tabs .= '<a href="javacript:void(0);" onclick="return radio_shift_tab(\'left\',' . esc_attr( $instance ) . ');" title="' . esc_attr( __( 'Previous Day', 'radio-station' ) ) . '">' . $arrows['left'] . '</a>' . "\n";
			$tabs .= '</div>' . "\n";

			// 2.3.2: added optional display_date attribute and subheading
			$tabs .= '<div class="master-schedule-tabs-headings" data-href="' . esc_attr( strtolower( $weekday ) ) . '">' . "\n";
				$tabs .= '<div class="master-schedule-tabs-day-name"';
				if ( !$atts['display_date'] ) {
					$tabs .= ' title="' . esc_attr( $date_subheading ) . '"';
				}
				$tabs .= '>' . esc_html( $display_day ) . '</div>' . "\n";
				if ( $atts['display_date'] ) {
					$tabs .= '<div class="master-schedule-tabs-date">' . esc_html( $date_subheading ) . '</div>' . "\n";
				}
			$tabs .= '</div>' . "\n";

			$tabs .= '<div class="shift-right-arrow">' . "\n";
				$tabs .= '<a href="javacript:void(0);" onclick="return radio_shift_tab(\'right\', ' . esc_attr( $instance ) . ');" title="' . esc_attr( __( 'Next Day', 'radio-station' ) ) . '">' . $arrows['right'] . '</a>' . "\n";
			$tabs .= '</div>' . "\n";
			$tabs .= '<div id="master-schedule-tab-bottom-' . esc_attr( strtolower( $weekday ) ) . '" class="master-schedule-tab-bottom"></div>' . "\n";
			// 2.3.2: add start and end day times for automatic highlighting
			$tabs .= '<span class="rs-time rs-start-time" data="' . esc_attr( $day_start_time ) . '"></span>' . "\n";
			$tabs .= '<span class="rs-time rs-end-time" data="' . esc_attr( $day_end_time ) . '"></span>' . "\n";
		$tabs .= '</li>';

		// 2.2.7: separate headings from panels for tab view
		// 2.5.0: move panel ID to class list for selectors
		$classes = array( 'master-schedule-tabs-panel', 'master-schedule-tabs-panel-' . esc_attr( strtolower( $weekday ) ) );
		if ( $weekdate == $date ) {
			// $classes[] = 'selected-day';
			$classes[] = 'active-day-panel';
		}
		$classlist = implode( ' ', $classes );
		$panels .= '<ul class="' . esc_attr( $classlist ) . '">' . "\n";

		// 2.3.2: added extra current day display
		// 2.3.3: use numeric day index for ID instead of weekday name
		// 2.5.0: remove ID in favour of class
		$display_day = radio_station_translate_weekday( $weekday, false );
		$panels .= '<div class="master-schedule-tabs-selected master-schedule-tabs-selected-' . esc_attr( $i ) . '">' . "\n";
		$panels .= esc_html( __( 'Viewing', 'radio-station' ) ) . ': ' . esc_html( $display_day ) . '</div>' . "\n";

		// --- get shifts for this day ---
		if ( isset( $schedule[$weekday] ) ) {
			$shifts = $schedule[$weekday];
		} else {
			$shifts = array();
		}

		$foundshows = false;

		// 2.3.0: loop schedule day shifts instead of hours and minutes
		if ( count( $shifts ) > 0 ) {

			$foundshows = true;

			// --- maybe set start avatar position ---
			// 2.3.3.8: added for possible alternating avatar positions
			$avatar_position = false;
			if ( 'alternate' == $atts['image_position'] ) {
				$avatar_position = 'left';
				$avatar_position = apply_filters( 'radio_station_schedule_tabs_avatar_position_start', $avatar_position );
			}

			$j = 0;
			foreach ( $shifts as $shift ) {

				// 2.3.3.8: reset info array
				$j++;
				$show = $shift['show'];
				$info = array();
				$split_id = false;

				$show_link = false;
				$show_id = $show['id'];
				if ( $atts['show_link'] ) {
					$show_link = $show['url'];
				}
				$show_link = apply_filters( 'radio_station_schedule_show_link', $show_link, $show_id, 'tabs' );

				// --- convert shift time data ---
				// 2.3.2: replace strtotime with to_time for timezones
				// 2.3.2: fix to convert to 24 hour format first
				// 2.3.2: fix timestamps for midnight/split shifts
				// $shift_start = radio_station_convert_shift_time( $shift['start'] );
				// $shift_end = radio_station_convert_shift_time( $shift['end'] );
				// $shift_start_time = radio_station_to_time( $shift['day'] . ' ' . $shift_start );
				// $shift_end_time = radio_station_to_time( $shift['day'] . ' ' . $shift_end );
				// if ( $shift_end_time < $shift_start_time ) {
				// 	$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
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

				// --- shift debug ---
				// 2.3.2: added shift debugging
				if ( isset( $_GET['rs-shift-debug'] ) && ( '1' === sanitize_text_field( $_GET['rs-shift-debug'] ) ) ) {
					$shiftdebug = isset( $shiftdebug ) ? $shiftdebug : '';
					$shiftdebug .= 'Now: ' . $now . ' (' . radio_station_get_time( 'datetime', $now ) . ') -- Today: ' . $today . '<br>';
					$shiftdebug .= 'Shift Start: ' . $shift_start . ' (' . radio_station_get_time( 'Y-m-d l H: i', $shift_start ) . ' - ' . radio_station_get_time( 'Y-m-d l H:i', $shift_start ) . ')<br>';
					$shiftdebug .= 'Shift End: ' . $shift_end . ' (' . radio_station_get_time( 'Y-m-d l H: i', $shift_end ) . ' - ' . radio_station_get_time( 'Y-m-d l H:i', $shift_end ) . ')<br>';
				}

				// 2.3.0: add genre classes for highlighting
				$classes = array( 'master-schedule-tabs-show' );
				$terms = wp_get_post_terms( $show_id, RADIO_STATION_GENRES_SLUG, array() );
				if ( $terms && ( count( $terms ) > 0 ) ) {
					foreach ( $terms as $term ) {
						// 2.5.0: add genre- prefix to genre terms
						// $classes[] = strtolower( $term->slug );
						$classes[] = 'genre-' . strtolower( $term->slug );
					}
				}
				// 2.3.2: add first and last classes
				if ( 1 == $j ) {
					$classes[] = 'first-show';
				}
				if ( count( $shifts ) == $j ) {
					$classes[] = 'last-show';
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

				// --- open list item ---
				$classlist = implode( ' ', $classes );
				$hour = date( 'H', strtotime( $shift['start'] ) );
				$panels .= '<li class="' . esc_attr( $classlist ) . '" data="' . esc_attr( $hour ) . '">' . "\n";

				// --- Show Image ---
				// (defaults to display on)
				$avatar = '';
				if ( $atts['show_image'] ) {

					// --- get show avatar image ---
					// 2.3.0: filter show avatar by show and context
					// 2.3.0: maybe link avatar to show
					$show_avatar = radio_station_get_show_avatar( $show_id, $avatar_size );
					$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show_id, 'tabs' );

					// --- set show image classes ---
					$classes = array( 'show-image' );
					if ( ( 'left' == $atts['image_position'] ) || ( 'left' == $avatar_position ) ) {
						$classes[] = 'left-image';
					} elseif ( ( 'right' == $atts['image_position'] ) || ( 'right' == $avatar_position ) ) {
						$classes[] = 'right-image';
					}
					$classlist = implode( ' ', $classes );

					if ( $show_avatar ) {
						$avatar = '<div class="' . esc_attr( $classlist ) . '">' . "\n";
						if ( $show_link ) {
							$avatar .= '<a href="' . esc_url( $show_link ) . '">';
						}
						$avatar .= $show_avatar . "\n";
						if ( $show_link ) {
							$avatar .= '</a>' . "\n";
						}
						$avatar .= '</div>' . "\n";
					} else {
						$avatar = '<div class="' . esc_attr( $classlist ) . '"></div>' . "\n";
					}
					$avatar = apply_filters( 'radio_station_schedule_show_avatar_display', $avatar, $show_id, 'tabs' );
				}

				// --- show title ---
				if ( $show_link ) {
					$show_title = '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
				} else {
					$show_title = esc_html( $show['name'] );
				}
				$title = '<div class="show-title">' . "\n";
				$title .= $show_title . "\n";
				$title .= '</div>' . "\n";
				$title = apply_filters( 'radio_station_schedule_show_title_display', $title, $show_id, 'tabs' );
				if ( ( '' != $title ) && is_string( $title ) ) {
					$info['title'] = $title;
				}
				// 2.3.3.9: allow for admin edit link
				$edit_link = apply_filters( 'radio_station_show_edit_link', '', $show_id, $shift['id'], 'tabs' );
				if ( '' != $edit_link ) {
					if ( isset( $info['title'] ) ) {
						$info['title'] .= $edit_link;
					} else {
						$info['title'] = $edit_link;
					}
				}

				// --- show hosts ---
				if ( $atts['show_hosts'] ) {

					$show_hosts = '';
					if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

						$count = 0;
						$host_count = count( $show['hosts'] );
						$show_hosts .= '<span class="show-dj-names-leader"> ';
						$show_hosts .= esc_html( __( 'with', 'radio-station' ) );
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
								 || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
								$show_hosts .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
							} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
								$show_hosts .= ', ';
							}
						}
					}

					$show_hosts = apply_filters( 'radio_station_schedule_show_hosts', $show_hosts, $show_id, 'tabs' );
					if ( $show_hosts ) {
						$hosts = '<div class="show-dj-names show-host-names">' . "\n";
						$hosts .= $show_hosts;
						$hosts .= '</div>' . "\n";
						$hosts = apply_filters( 'radio_station_schedule_show_hosts_display', $hosts, $show_id, 'tabs' );
						if ( ( '' != $hosts ) && is_string( $hosts ) ) {
							$info['hosts'] = $hosts;
						}
					}
				}

				// --- show times ---
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
					$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
					$show_time .= '<span class="rs-sep rs-shift-sep"> ' . esc_html( $shifts_separator ) . ' </span>' . "\n";
					$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";

				} else {

					// 2.3.2: added for now playing check
					// 2.3.3.8: moved for better conditional logic
					$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '"></span>' . "\n";
					$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '"></span>' . "\n";

				}

				// 2.3.3.8: moved show time filter out and added display filter
				// 2.3.3.9: added tcount argument to filter
				$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show_id, 'tabs', $shift, $tcount );
				$times = '<div class="show-time" id="show-time-' . esc_attr( $tcount ) . '"';
				// note: unlike other display filters this hides/shows times rather than string filtering
				$display = apply_filters( 'radio_station_schedule_show_times_display', true, $show_id, 'tabs', $shift );
				if ( !$display ) {
					$times .= ' style="display:none;"';
				}
				$times .= '>' . $show_time . '</div>' . "\n";
				// 2.3.3.9: added internal spans for user time
				$times .= '<div class="show-user-time" id="show-user-time-' . esc_attr( $tcount ) . '">' . "\n";
				$times .= '[<span class="rs-time rs-start-time"></span>' . "\n";
				$times .= '<span class="rs-sep"> ' . esc_html( __( 'to', 'radio-station' ) ) . ' </span>' . "\n";
				$times .= '<span class="rs-time rs-end-time"></span>]' . "\n";
				$times .= '</div>' . "\n";
				$info['times'] = $times;
				$tcount++;

				// --- encore ---
				// 2.3.0: filter encore switch by show and context
				if ( $atts['show_encore'] ) {
					$show_encore = isset( $shift['encore'] ) ? $shift['encore'] : false;
					$show_encore = apply_filters( 'radio_station_schedule_show_encore', $show_encore, $show_id, 'tabs' );
					if ( 'on' == $show_encore ) {
						$encore = '<div class="show-encore">';
						$encore .= esc_html( __( 'encore airing', 'radio-station' ) );
						$encore .= '</div>' . "\n";
						$encore = apply_filters( 'radio_station_schedule_show_encore_display', $encore, $show_id, 'tabs' );
						if ( ( '' != $encore ) && is_string( $encore ) ) {
							$info['encore'] = $encore;
						}
					}
				}

				// --- show audio file ---
				if ( $atts['show_file'] ) {
					// 2.3.0: filter audio file by show and context
					// 2.3.2: check show download disable meta
					// 2.3.3: fix to incorrect filter name
					// 2.3.3.8: add filter for show file link div
					// 2.3.3.8: added filter for show file anchor
					$show_file = get_post_meta( $show_id, 'show_file', true );
					$show_file = apply_filters( 'radio_station_schedule_show_file', $show_file, $show_id, 'tabs' );
					$disable_download = get_post_meta( $show_id, 'show_download', true );
					if ( $show_file && !empty( $show_file ) && !$disable_download ) {
						$anchor = __( 'Audio File', 'radio-station' );
						$anchor = apply_filters( 'radio_station_schedule_show_file_anchor', $anchor, $show_id, 'tabs' );
						$file = '<div class="show-file">' . "\n";
						$file .= '<a href="' . esc_url( $show_file ) . '">';
						$file .= esc_html( $anchor );
						$file .= '</a>' . "\n";
						$file .= '</div>' . "\n";
						$file = apply_filters( 'radio_station_schedule_show_file_display', $file, $show_file, $show_id, 'tabs' );
						if ( ( '' != $file ) && is_string( $file ) ) {
							$info['file'] = $file;
						}
					}
				}

				// --- show genres ---
				// (defaults to display on)
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
					$genres = apply_filters( 'radio_station_schedule_show_genres', $genres, $show_id, 'tabs' );
					if ( ( '' != $genres ) && is_string( $genres ) ) {
						$info['genres'] = $genres;
					}
				}

				// --- custom info section ---
				// 2.3.3.8: allow for custom HTML to be added
				$custom = apply_filters( 'radio_station_schedule_show_custom_display', '', $show_id, 'tabs' );
				if ( ( '' != $custom ) && is_string( $custom ) ) {
					$info['custom'] = $custom;
				}

				// --- left aligned show avatar ---
				// 2.3.3.8: add image according to position
				if ( $avatar ) {
					if ( ( 'left' == $atts['image_position'] ) || ( 'left' == $avatar_position ) ) {
						$panels .= $avatar;
					}
				}

				// --- Show Information ---
				// 2.3.3.8: add avatar classes to show info for style targeting
				$classes = array( 'show-info' );
				if ( $atts['show_desc'] ) {
					$classes[] = 'has-show-desc';
				}
				if ( ( 'left' == $atts['image_position'] ) || ( 'left' == $avatar_position ) ) {
					$classes[] = 'left-image';
				} elseif ( ( 'right' == $atts['image_position'] ) || ( 'right' == $avatar_position ) ) {
					$classes[] = 'right-image';
				}
				$classlist = implode( ' ', $classes );
				$panels .= '<div class="' . esc_attr( $classlist ) . '">' . "\n";

				// --- add item info according to key order ---
				// 2.3.3.8: added for possible order rearrangement
				foreach ( $infokeys as $infokey ) {
					if ( isset( $info[$infokey] ) ) {
						$panels .= $info[$infokey];
					}
				}

				// --- close show info section ---
				$panels .= '</div>' . "\n";

				// --- right aligned show avatar ---
				// 2.3.3.8: add image according to position
				if ( $avatar ) {
					if ( 'right' == $atts['image_position'] ) {
						$panels .= $avatar;
					} elseif ( 'right' == $avatar_position ) {
						$panels .= $avatar;
						$avatar_position = 'left';
					} elseif ( 'left' == $avatar_position ) {
						$avatar_position = 'right';
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

					// --- filter excerpt by context ---
					$show_excerpt = apply_filters( 'radio_station_schedule_show_excerpt', $show_excerpt, $show_id, 'tabs' );

					// --- output excerpt ---
					$excerpt = '<div class="show-desc">' . "\n";
					$excerpt .= $show_excerpt . "\n";
					$excerpt .= '</div>' . "\n";
					$excerpt = apply_filters( 'radio_station_schedule_show_excerpt_display', $excerpt, $show_id, 'tabs' );
					if ( ( '' != $excerpt ) && is_string( $excerpt ) ) {
						$panels .= $excerpt;
					}

				}

				$panels .= '</li>';
			}
		}

		if ( !$foundshows ) {
			// 2.3.2: added no shows class
			// 2.5.0: added filter for no shows message
			$no_shows_message = __( 'No Shows scheduled for this day.', 'radio-station' );
			$no_shows_message = apply_filters( 'radio_station_schedule_no_shows_message', $no_shows_message, 'tabs' );
			$panels .= '<li class="master-schedule-tabs-show master-schedule-tabs-no-shows">' . "\n";
				$panels .= esc_html( $no_shows_message );
			$panels .= '</li>' . "\n";
		}
	}

	$panels .= '</ul>' . "\n";
}

// 2.3.3.9: added filter for week loader control
$load_next = apply_filters( 'radio_station_schedule_loader_control', '', 'tabs', 'right', $instance );
if ( '' != $load_next ) {
	// 2.5.0; remove ID in favour of class
	$tabs .= '<li class="master-schedule-tabs-loader master-schedule-tabs-loader-right">' . "\n";
		$tabs .= $load_next . "\n";
	$tabs .= '</li>' . "\n";
}

// --- add day tabs to output ---
// 2.5.0: maybe add instance to element ID
$id = ( 0 == $instance ) ? '' : '-' . $instance;
echo '<ul id="master-schedule-tabs' . esc_attr( $id ) . '" class="master-schedule-tabs">' . "\n";
	// TODO: test wp_kses on output
	echo $tabs;
echo '</ul>' . "\n";

// --- add day panels to output ---
// 2.3.3.8: check for hide past shows attribute
// 2.5.0: maybe add instance to element ID
echo '<div id="master-schedule-tab-panels' . esc_attr( $id ) . '" class="master-schedule-tab-panels"';
if ( $atts['hide_past_shows'] ) {
	echo ' class="hide-past-shows"';
}
echo '>' . "\n";
	// TODO: test wp_kses on output
	echo $panels;
echo '</div>' . "\n";

// --- hidden iframe for schedule reloading ---
echo '<iframe src="javascript:void(0);" id="schedule-tabs-loader" name="schedule-tabs-loader" style="display:none;"></iframe>' . "\n";

if ( isset( $_GET['rs-shift-debug'] ) && ( '1' === sanitize_text_field( $_GET['rs-shift-debug'] ) ) ) {
	echo '<br><b>Shift Debug Info:</b><br>' . esc_html( $shiftdebug ) . '<br>';
}
