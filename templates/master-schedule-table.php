<?php
/**
 * Template for master schedule shortcode default (table) style.
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
$start_time = apply_filters( 'radio_station_schedule_start_time', $start_time, 'table', $atts );

// --- set shift time formats ---
// 2.3.2: set time formats early
// 2.4.0.6: add filter for shift times separator
$shifts_separator = __( 'to', 'radio-station' );
$shifts_separator = apply_filters( 'radio_station_show_time_separator', $shifts_separator, 'schedule-table' );
$time_separator = ':';
$time_separator = apply_filters( 'radio_station_time_separator', $time_separator, 'schedule-table' );
if ( 24 == (int) $atts['time_format'] ) {
	$start_data_format = $end_data_format = 'H' . $time_separator . 'i';
} else {
	$start_data_format = $end_data_format = 'g' . $time_separator . 'i a';
}
$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'schedule-table', $atts );
$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'schedule-table', $atts );

// --- get schedule days and dates ---
// 2.3.2: allow for start day attibute
// 2.3.3.5: use the start_day value for getting the current schedule
if ( isset( $atts['start_day'] ) && $atts['start_day'] ) {
	$start_day = $atts['start_day'];
} else {
	// 2.3.3.5: add filter for changing start day (to accept 'today')
	$start_day = apply_filters( 'radio_station_schedule_start_day', false, 'table' );
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

// --- filter avatar size ---
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail', 'table' );

// --- filter excerpt length and more ---
if ( $atts['show_desc'] ) {
	$length = apply_filters( 'radio_station_schedule_table_excerpt_length', false );
	$more = apply_filters( 'radio_station_schedule_table_excerpt_more', '[&hellip;]' );
}

// --- filter arrows ---
$arrows = array( 'left' => '&#8249;', 'right' => '&#8250;', 'doubleleft' => '&#171;', 'doubleright' => '&#187;' );
$arrows = apply_filters( 'radio_station_schedule_arrows', $arrows, 'table' );

// --- set cell info key order ---
// 2.3.3.8: added for possible info rearrangement
$infokeys = array( 'avatar', 'title', 'hosts', 'times', 'encore', 'file', 'excerpt', 'custom' );
$infokeys = apply_filters( 'radio_station_schedule_table_info_order', $infokeys );

// --- clear floats ---
echo '<div class="schedule-clearfix" style="clear:both;"></div>' . "\n";

// --- start master program table ---
// 2.5.0: maybe add instance to element ID
// 2.5.0: set oddeven variable
$oddeven = 'even';
$id = ( 0 == $atts['instance'] ) ? '' : '-' . $atts['instance'];
$table = '<table id="master-program-schedule' . esc_attr( $id ) . '" class="master-program-schedule" cellspacing="0" cellpadding="0">' . "\n";

// --- weekday table headings row ---
// 2.3.2: added hour column heading id
$table .= '<tr class="master-program-day-row">' . "\n";
// 2.5.0: change element ID to class for selectors
$table .= '<th class="master-program-hour-heading">' . "\n";
	// 2.3.3.9: added filters for week loader controls
	$table .= apply_filters( 'radio_station_schedule_loader_control', '', 'table', 'left', $atts['instance'] );
	$table .= apply_filters( 'radio_station_schedule_loader_control', '', 'table', 'right', $atts['instance'] );
$table .= '</th>' . "\n";

// 2.5.0: set odd/even day variable
$oddeven_day = 'even';
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

		// --- set day column heading ---
		// 2.3.2: added check for short/long day display attribute
		if ( !in_array( $atts['display_day'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_day'] = 'short';
		}
		if ( 'short' == $atts['display_day'] ) {
			$display_day = radio_station_translate_weekday( $weekday, true );
		} elseif ( ( 'full' == $atts['display_day'] ) || ( 'long' == $atts['display_day'] ) ) {
			$display_day = radio_station_translate_weekday( $weekday, false );
		}

		// --- get weekdate subheading ---
		// 2.3.2: set day start and end times
		// 2.3.2: add subheading adjustment for timezone
		// 2.3.2: replace strtotime with to_time function for timezone
		$weekdate = $weekdates[$weekday];
		$day_start_time = radio_station_to_time( $weekdate . ' 00:00:00' );
		$day_end_time = $day_start_time + ( 24 * 60 * 60 );

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

		// --- set heading classes ---
		// 2.3.0: add day and check for highlighting
		// 2.5.0: add odd/even day class
		$classes = array( 'master-program-day', 'day-' . $i, strtolower( $weekday ), 'date-' . $weekdate );
		$oddeven_day = ( 'even' == $oddeven_day ) ? 'odd' : 'even';
		$classes[] = $oddeven_day . '-day';
		if ( ( $now > $day_start_time ) && ( $now < $day_end_time ) ) {
			$classes[] = 'current-day';
			// $classes[] = 'selected-day';
		}
		$classlist = implode( ' ', $classes );

		// --- output table column heading ---
		// 2.3.0: added left/right arrow responsive controls
		// 2.3.1: added (negative) return to arrow onclick functions
		// 2.3.2: added check for optional display_date attribute
		$table .= '<th class="' . esc_attr( $classlist ) . '">' . "\n";
			$table .= '<div class="shift-left-arrow">' . "\n";
				$table .= '<a href="javascript:void(0);" onclick="return radio_shift_day(\'left\',' . esc_attr( $atts['instance'] ) . ');" title="' . esc_attr( __( 'Previous Day', 'radio-station' ) ) . '">' . $arrows['left'] . '</a>' . "\n";
			$table .= '</div>' . "\n";
			$table .= '<div class="headings">' . "\n";
				$table .= '<div class="day-heading"';
				if ( $atts['display_date'] ) {
					$table .= ' title="' . esc_attr( $date_subheading ) . '"';
				}
				$table .= '>' . esc_html( $display_day ) . '</div>' . "\n";
				if ( $atts['display_date'] ) {
					$table .= '<div class="date-subheading">' . esc_html( $date_subheading ) . '</div>' . "\n";
				}
			$table .= '</div>' . "\n";
			$table .= '<div class="shift-right-arrow">' . "\n";
				$table .= '<a href="javacript:void(0);" onclick="return radio_shift_day(\'right\',' . esc_attr( $atts['instance'] ) . ');" title="' . esc_attr( __( 'Next Day', 'radio-station' ) ) . '">' . $arrows['right'] . '</a>' . "\n";
			$table .= '</div>' . "\n";
			// 2.3.2: add day start and end time date
			$table .= '<span class="rs-time rs-start-time" data="' . esc_attr( $day_start_time ) . '"></span>' . "\n";
			$table .= '<span class="rs-time rs-end-time" data="' . esc_attr( $day_end_time ) . '"></span>' . "\n";
		$table .= '</th>' . "\n";

	}
}
$table .= '</tr>' . "\n";

// --- loop schedule hours ---
// 2.5.0: set odd/even hour variable
$tcount = 0;
$oddeven_hour = 'odd';
foreach ( $hours as $hour ) {

	// 2.3.1: fix to set translated hour for display only
	$raw_hour = $hour;
	$hour_display = radio_station_convert_hour( $hour, $atts['time_format'] );
	if ( 1 == strlen( $hour ) ) {
		$hour = '0' . $hour;
	}

	// --- start hour row ---
	$table .= '<tr class="master-program-hour-row hour-row-' . esc_attr( $raw_hour ) . '">' . "\n";

	// --- set data format for timezone conversions ---
	if ( 24 == (int) $atts['time_format'] ) {
		$hour_data_format = "H:i";
	} else {
		$hour_data_format = "g a";
	}

	// --- set heading classes ---
	// 2.3.0: check current hour for highlighting
	// 2.3.1: fix to use untranslated hour (12 hr format bug)
	// 2.3.2: replace strtotime with to_time function for timezone
	// 2.5.0: add odd/even hour class
	$classes = array( 'master-program-hour' );
	$oddeven_hour = ( 'odd' == $oddeven_hour ) ? 'even' : 'odd';
	$classes[] = $oddeven_hour . '-hour';
	$hour_start = radio_station_to_time( $date . ' ' . $hour . ':00' );
	$hour_end = $hour_start + ( 60 * 60 );
	if ( ( $now > $hour_start ) && ( $now < $hour_end ) ) {
		$classes[] = 'current-hour';
	}
	$class = implode( ' ', $classes );

	// --- hour heading ---
	$table .= '<th class="' . esc_attr( $class ) . '">' . "\n";
		if ( isset( $_GET['hourdebug'] ) && ( '1' === sanitize_text_field( $_GET['hourdebug'] ) ) ) {
			$table .= '<span style="display:none;">';
			$table .= 'Now' . esc_html( $now ) . '(' . esc_html( radio_station_get_time( 'H:i', $now ) ) . ')<br>';
			$table .= 'Hour Start' . esc_html( $hour_start ) . '(' . esc_html( radio_station_get_time( 'H:i', $hour_start ) ) . ')<br>';
			$table .= 'Hour End' . esc_html( $hour_end ) . '(' . esc_html( radio_station_get_time( 'H:i', $hour_end ) ) . ')<br>';
			$table .= '</span>';
		}

		$table .= '<div class="master-program-server-hour rs-time" data="' . esc_attr( $raw_hour ) . '" data-format="' . esc_attr( $hour_data_format ) . '">';
			$table .= esc_html( $hour_display );
		$table .= '</div>' . "\n";
		$table .= '<br>' . "\n";
		$table .= '<div class="master-program-user-hour rs-time" data="' . esc_attr( $raw_hour ) . '" data-format="' . esc_attr( $hour_data_format ) . '"></div>' . "\n";
	$table .= '</th>' . "\n";

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

			// --- clear the cell ---
			if ( isset( $cell ) ) {
				unset( $cell );
			}
			$cellcontinued = $showcontinued = $overflow = $newshift = false;
			$cellshifts = 0;

			// --- get shifts for this day ---
			if ( isset( $schedule[$weekday] ) ) {
				$shifts = $schedule[$weekday];
			} else {
				$shifts = array();
			}
			$nextday = radio_station_get_next_day( $weekday );
			$prevday = radio_station_get_previous_day( $weekday );

			// --- get weekdates ---
			$weekdate = $weekdates[$weekday];
			// $nextdate = $weekdates[$nextday];

			// --- get hour and next hour start and end times ---
			// 2.3.1: fix to use untranslated hour (12 hr format bug)
			// 2.3.2: replace strtotime with to_time function for timezone
			$hour_start = radio_station_to_time( $weekdate . ' ' . $hour . ':00' );
			$hour_end = $next_hour_start = $hour_start + ( 60 * 60 );
			$next_hour_end = $hour_end + ( 60 * 60 );

			// --- loop the shifts for this day ---
			foreach ( $shifts as $shift ) {

				$split_id = false;

				if ( !isset( $shift['finished'] ) || !$shift['finished'] ) {

					// --- get shift start and end times ---
					// 2.3.2: replace strtotime with to_time function for timezone
					// 2.3.2: fix to convert to 24 hour format first
					$display = $nowplaying = false;
					if ( '00:00 am' == $shift['start'] ) {
						$shift_start_time = radio_station_to_time( $weekdate . ' 00:00' );
					} else {
						$shift_start_time = radio_station_convert_shift_time( $shift['start'] );
						$shift_start_time = radio_station_to_time( $weekdate . ' ' . $shift_start_time );
					}
					if ( ( '11:59:59 pm' == $shift['end'] ) || ( '12:00 am' == $shift['end'] ) ) {
						$shift_end_time = radio_station_to_time( $weekdate . ' 23:59:59' ) + 1;
					} else {
						$shift_end_time = radio_station_convert_shift_time( $shift['end'] );
						$shift_end_time = radio_station_to_time( $weekdate . ' ' . $shift_end_time );
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

					// --- check if the shift is starting / started ---
					if ( isset( $shift['started'] ) && $shift['started'] ) {
						// - continue display of shift -
						if ( !isset( $cell ) ) {
							$cellcontinued = true;
						}
						$display = $showcontinued = true;
						$cellshifts ++;
					} elseif ( ( $shift_start_time == $hour_start )
						|| ( ( $shift_start_time > $hour_start ) && ( $shift_start_time < $next_hour_start ) ) ) {
						// - start display of shift -
						$started = $shift['started'] = true;
						$schedule[$weekday][$shift['start']] = $shift;
						$display = $newshift = true;
						// 2.3.1: reset showcontinued flag
						$showcontinued = false;
						$cellshifts ++;
					}

					// --- check if shift is current ---
					if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {
						$nowplaying = true;
					}

					// --- check if shift finishes in this hour ---
					if ( isset( $shift['started'] ) && $shift['started'] ) {
						if ( $shift_end_time == $hour_end ) {
							$finished = $shift['finished'] = true;
							$schedule[$weekday][$shift['start']] = $shift;
							// $fullcell = true;
						} elseif ( $shift_end_time < $hour_end ) {
							$finished = $shift['finished'] = true;
							$schedule[$weekday][$shift['start']] = $shift;
							// $percent = round( ( $shift_end_time - $hour_start ) / 3600 );
							// $partcell = true;
						} else {
							$overflow = true;
						}
					}

					if ( isset( $_GET['rs-shift-debug'] ) && ( '1' === sanitize_title( $_GET['rs-shift-debug'] ) ) ) {
						if ( !isset( $shiftdebug ) ) {$shiftdebug = '';}
						if ( $display ) {
							$shiftdebug .= 'Now: ' . $now . ' (' . radio_station_get_time( 'datetime', $now ) . ') -- Today: ' . $today . '<br>';
							$shiftdebug .= 'Day: ' . $weekday . ' - Raw Hour: ' . $raw_hour . ' - Hour: ' . $hour . ' - Hour Display: ' . $hour_display . '<br>';
							$shiftdebug .= 'Hour Start: ' . $hour_start . ' (' . gmdate( 'Y-m-d l H:i', $hour_start ) . ' - ' . radio_station_get_time( 'Y-m-d l H:i', $hour_start ) . ')<br>';
							$shiftdebug .= 'Hour End: ' . $hour_end . ' (' . gmdate( 'Y-m-d l H: i', $hour_end ) . ' - ' . radio_station_get_time( 'Y-m-d l H:i', $hour_end ) . ')<br>';
							$shiftdebug .= 'Shift Start: ' . $shift_start_time . ' (' . gmdate( 'Y-m-d l H: i', $shift_start_time ) . ' - ' . radio_station_get_time( 'Y-m-d l H:i', $shift_start_time ) . ')<br>';
							$shiftdebug .= 'Shift End: ' . $shift_end_time . ' (' . gmdate( 'Y-m-d l H: i', $shift_end_time ) . ' - ' . radio_station_get_time( 'Y-m-d l H:i', $shift_end_time ) . ')<br>';
							$shiftdebug .= 'Display: ' . ( $display ? 'yes' : 'no' ) . ' - ';
							$shiftdebug .= 'New Shift: ' . ( $newshift ? 'yes' : 'no' ) . ' - ';
							$shiftdebug .= 'Now Playing: ' . ( $nowplaying ? 'YES' : 'no' ) . ' - ';
							$shiftdebug .= 'Cell Continues: ' . ( $cellcontinued ? 'yes' : 'no' ) . ' - ';
							$shiftdebug .= 'Overflow: ' . ( $overflow ? 'yes' : 'no' ) . ' - ';
							$shiftdebug .= 'Show Continued: ' . ( $showcontinued ? 'yes' : 'no' ) . ' - ';
							// $shiftdebug .= 'Shift: ' . print_r( $shift, true ) . '<br>';
						}
					}

					// --- maybe add shift display to the cell ---
					if ( $display ) {

						// 2.3.3.8: reset info array for each cell
						$info = array();
						$show = $shift['show'];
						$show_id = $show['id'];

						// --- set the show div classes ---
						$divclasses = array( 'master-show-entry', 'show-id-' . $show_id, $show['slug'] );
						if ( $nowplaying ) {
							$divclasses[] = 'nowplaying';
						}
						if ( $overflow ) {
							$divclasses[] = 'overflow';
						}
						if ( $showcontinued ) {
							$divclasses[] = 'continued';
						}
						if ( $newshift ) {
							$divclasses[] = 'newshift';
						}
						if ( isset( $show['genres'] ) && is_array( $show['genres'] ) && ( count( $show['genres'] ) > 0 ) ) {
							foreach ( $show['genres'] as $genre ) {
								// 2.5.0: add genre- prefix to genre terms
								// $divclasses[] = sanitize_title_with_dashes( $genre );
								$divclasses[] = 'genre-' . sanitize_title_with_dashes( $genre );
							}
						}
						if ( $split_id ) {
							$divclasses[] = 'overnight';
							$divclasses[] = 'split-' . $split_id;
						}
						$divclasslist = implode( ' ', $divclasses );

						// --- start the cell contents ---
						if ( !isset( $cell ) ) {
							$cell = '';
						}
						$cell .= '<div class="' . esc_attr( $divclasslist ) . '">' . "\n";

						if ( $showcontinued ) {

							// --- display empty div (for highlighting) ---
							$cell .= '&nbsp;';

							// 2.3.2: set shift times for highlighting
							$cell .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '"></span>' . "\n";
							$cell .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '"></span>' . "\n";

						} else {

							// --- set filtered show link ---
							// 2.3.0: filter show link via show ID and context
							$show_link = false;
							if ( $atts['show_link'] ) {
								$show_link = $show['url'];
							}
							$show_link = apply_filters( 'radio_station_schedule_show_link', $show_link, $show_id, 'table' );

							// --- show logo / thumbnail ---
							// 2.3.0: filter show avatar via show ID and context
							$show_avatar = false;
							if ( $atts['show_image'] ) {
								$show_avatar = radio_station_get_show_avatar( $show_id, $avatar_size );
							}
							$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show_id, 'table' );
							if ( $show_avatar ) {
								$avatar = '<div class="show-image">' . "\n";
								if ( $show_link ) {
									$avatar .= '<a href="' . esc_url( $show_link ) . '">' . $show_avatar . '</a>' . "\n";
								} else {
									$avatar .= $show_avatar . "\n";
								}
								$avatar .= '</div>' . "\n";
								$avatar = apply_filters( 'radio_station_schedule_show_avatar_display', $avatar, $show_id, 'table' );
								if ( ( '' != $avatar ) && is_string( $avatar ) ) {
									$info['avatar'] = $avatar;
								}
							}

							// --- show title ---
							$title = '<div class="show-title">' . "\n";
							if ( $show_link ) {
								$title .= '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>' . "\n";
							} else {
								$title .= esc_html( $show['name'] ) . "\n";
							}
							$title .= '</div>' . "\n";
							$title = apply_filters( 'radio_station_schedule_show_title_display', $title, $show_id, 'table' );
							if ( ( '' != $title ) && is_string( $title ) ) {
								$info['title'] = $title;
							}
							// 2.3.3.9: allow for admin edit link
							$edit_link = apply_filters( 'radio_station_show_edit_link', '', $show_id, $shift['id'], 'table' );
							if ( '' != $edit_link ) {
								if ( isset( $info['title'] ) ) {
									$info['title'] .= $edit_link;
								} else {
									$info['title'] = $edit_link;
								}
							}

							// --- show DJs / hosts ---
							if ( $atts['show_hosts'] ) {

								$show_hosts = '';
								if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

									$show_hosts .= '<span class="show-dj-names-leader show-host-names-leader"> ';
										$show_hosts .= esc_html( __( 'with', 'radio-station' ) );
									$show_hosts .= ' </span>' . "\n";

									$count = 0;
									$host_count = count( $show['hosts'] );
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

								$show_hosts = apply_filters( 'radio_station_schedule_show_hosts', $show_hosts, $show_id, 'table' );
								if ( $show_hosts ) {
									$hosts = '<div class="show-dj-names show-host-names">' . "\n";
									$hosts .= $show_hosts;
									$hosts .= '</div>' . "\n";
									$hosts = apply_filters( 'radio_station_schedule_show_hosts_display', $hosts, $show_id, 'table' );
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

								// --- set show time output ---
								$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
								$show_time .= '<span class="rs-sep rs-shift-sep"> ' . esc_html( $shifts_separator ) . ' </span>' . "\n";
								$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";

							} else {

								// 2.3.3.8: added for now playing check
								$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '"></span>' . "\n";
								$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '"></span>' . "\n";

							}

							// --- add show time to cell ---
							// 2.3.3.9: added tcount argument to filter
							$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show_id, 'table', $shift, $tcount );
							$times = '<div class="show-time" id="show-time-' . esc_attr( $tcount ) . '"';
								// note: unlike other display filters this hides/shows times rather than string filtering
								$display = apply_filters( 'radio_station_schedule_show_time_display', true, $show_id, 'table', $shift );
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

							// --- encore airing ---
							// 2.3.1: added isset check for encore switch
							$show_encore = false;
							if ( $atts['show_encore'] && isset( $shift['encore'] ) ) {
								$show_encore = $shift['encore'];
							}
							// 2.3.3.8: fix to filtered value variable
							$show_encore = apply_filters( 'radio_station_schedule_show_encore', $show_encore, $show_id, 'table' );
							if ( 'on' == $show_encore ) {
								$encore = '<div class="show-encore">';
									$encore .= esc_html( __( 'encore airing', 'radio-station' ) );
								$encore .= '</div>' . "\n";
								$encore = apply_filters( 'radio_station_schedule_show_encore_display', $encore, $show_id, 'table' );
								if ( ( '' != $encore ) && is_string( $encore ) ) {
									$info['encore'] = $encore;
								}
							}

							// --- show file ---
							// 2.3.2: check disable download meta
							// 2.3.3.8: add filter for show file link
							// 2.3.3.8: added filter for show file anchor
							$show_file = false;
							if ( $atts['show_file'] ) {
								$show_file = get_post_meta( $show_id, 'show_file', true );
							}
							$show_file = apply_filters( 'radio_station_schedule_show_file', $show_file, $show_id, 'table' );
							$disable_download = get_post_meta( $show_id, 'show_download', true );
							if ( $show_file && !empty( $show_file ) && !$disable_download ) {
								$anchor = __( 'Audio File', 'radio-station' );
								// 2.3.3.8: fix to incorrect context argument 'tabs'
								$anchor = apply_filters( 'radio_station_schedule_show_file_anchor', $anchor, $show_id, 'table' );
								$file = '<div class="show-file">' . "\n";
									$file .= '<a href="' . esc_url( $show_file ) . '">';
										$file .= esc_html( $anchor );
									$file .= '</a>' . "\n";
								$file .= '</div>' . "\n";
								$file = apply_filters( 'radio_station_schedule_show_file_display', $file, $show_file, $show_id, 'table' );
								if ( ( '' != $file ) && is_string( $file ) ) {
									$info['file'] = $file;
								}
							}

							// --- show description ---
							if ( $atts['show_desc'] ) {

								$show_post = get_post( $show['id'] );
								$permalink = get_permalink( $show_post->ID );

								// --- get show excerpt ---
								if ( !empty( $show_post->post_excerpt ) ) {
									$show_excerpt = $show_post->post_excerpt;
									// 2.5.0: use esc_html on more anchor text
									$show_excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . esc_html( $more ) . '</a>' . "\n";
								} else {
									$show_excerpt = radio_station_trim_excerpt( $show_post->post_content, $length, $more, $permalink );
								}

								// --- filter excerpt by context ---
								$show_excerpt = apply_filters( 'radio_station_schedule_show_excerpt', $show_excerpt, $show_id, 'table' );

								// --- output excerpt ---
								$excerpt = '<div class="show-desc">' . "\n";
								$excerpt .= $show_excerpt . "\n";
								$excerpt .= '</div>' . "\n";
								// 2.5.0: fix to variable typo (excerpy)
								$excerpt = apply_filters( 'radio_station_schedule_show_excerpt_display', $excerpt, $show_id, 'table' );
								if ( ( '' != $excerpt ) && is_string( $excerpt ) ) {
									$info['excerpt'] = $excerpt;
								}
							}

							// --- custom info section ---
							// 2.3.3.8: allow for custom HTML to be added
							$custom = apply_filters( 'radio_station_schedule_show_custom_display', '', $show_id, 'table' );
							if ( ( '' != $custom ) && is_string( $custom ) ) {
								$info['custom'] = $custom;
							}

						}

						// --- add cell info according to key order ---
						// 2.3.3.8: added for possible order rearrangement
						foreach ( $infokeys as $infokey ) {
							if ( isset( $info[$infokey] ) ) {
								$cell .= $info[$infokey];
							}
						}

						$cell .= '</div>' . "\n";

						// 2.3.1: fix to ensure reset showcontinued flag in cell
						if ( isset( $shift['finished'] ) && $shift['finished'] ) {
							$showcontinued = false;
						}
					}
				}
			}

			// --- add cell to hour row - weekday column ---
			$cellclasses = array( 'show-info', 'day-' . $i, strtolower( $weekday ), 'date-' . $weekdate );
			if ( $cellcontinued ) {
				$cellclasses[] = 'continued';
			}
			if ( $overflow ) {
				$cellclasses[] = 'overflow';
			}
			if ( $cellshifts > 0 ) {
				$cellclasses[] = $cellshifts . '-shifts';
			} else {
				// 2.3.2: add no-shifts class
				$cellclasses[] = 'no-shifts';
				$times = array( 'date' => $weekdate, 'day' => $weekday, 'hour' => $hour, 'mins' => 0 );
				$add_link = apply_filters( 'radio_station_schedule_add_link', '', $times, 'table' );
				if ( '' != $add_link ) {
					if ( !isset( $cell ) ) {
						$cell = '';
					}
					$cell .= '<center>' . $add_link . '</center>';
				}
			}
			$cellclasslist = implode( ' ', $cellclasses );
			$table .= '<td class="' . $cellclasslist . '">' . "\n";
				$table .= '<div class="show-wrap">' . "\n";
				if ( isset( $cell ) ) {
					$table .= $cell;
				}
				$table .= '</div>' . "\n";
			$table .= '</td>' . "\n";
		}
	}

	// --- close hour row ---
	$table .= '</tr>' . "\n";
}

$table .= '</table>' . "\n";

// --- hidden iframe for schedule reloading ---
$table .= '<iframe src="javascript:void(0);" id="schedule-table-loader" name="schedule-table-loader" style="display:none;"></iframe>' . "\n";

if ( isset( $_GET['rs-shift-debug'] ) && ( '1' === sanitize_text_field( $_GET['rs-shift-debug'] ) ) ) {
	$table .= '<br><b>Shift Debug Info:</b><br>' . esc_html( $shiftdebug ) . '<br>';
}

// TODO: test wp_kses on output
echo $table;
