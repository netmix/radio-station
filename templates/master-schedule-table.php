<?php
/**
 * Template for master schedule shortcode default (table) style.
 */

// --- get all the required info ---
$weekdays = radio_station_get_schedule_weekdays();
$schedule = radio_station_get_current_schedule();
$hours = radio_station_get_hours();
$now = strtotime( current_time( 'mysql' ) );
$today =  strtolower( date( 'j', $now ) );
$am = str_replace( ' ', '', radio_station_translate_meridiem( 'am' ) );
$pm = str_replace( ' ', '', radio_station_translate_meridiem( 'pm' ) );

// --- filter avatar size ---
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail', 'table' );

// --- clear floats ---
$output .= '<div style="clear:both;"></div>';

// --- start master program table ---
$output .= '<table id="master-program-schedule" cellspacing="0" cellpadding="0" class="grid">';

// --- weekday table headings row ---
$output .= '<tr class="master-program-day-row">';
$output .= '<th></th>';
foreach ( $weekdays as $weekday ) {
	$heading = substr( $weekday, 0, 3 );
	$heading = radio_station_translate_weekday( $heading, true );
	$classes = array( 'master-program-day', strtolower( $weekday ) );
	if ( strtolower( $weekday ) == $today ) {$classes[] = 'current-day';}
	$class = implode( ' ', $classes );
	$output .= '<th class="' . esc_attr( $class ) . '">' . esc_html( $heading ) . '</th>';
}
$output .= '</tr>';

// --- loop schedule hours ---
$tcount = 0;
foreach ( $hours as $hour ) {

	$raw_hour = $hour;
	$nexthour = radio_station_convert_hour( ( $hour + 1 ), $atts['time'] );
	$hour = radio_station_convert_hour( $hour, $atts['time'] );

	// --- start hour row ---
	$output .= '<tr class="master-program-hour-row hour-row-' . esc_attr( $raw_hour ) . '">';

	// --- set data format for timezone conversions ---
	if ( 24 == (int) $atts['time'] ) {
		$data_format = "G:i";
	} else {
		$data_format = "H a";
	}

	// --- hour heading ---
	$output .= '<th class="master-program-hour">';
	$output .= '<div>';
	$output .= esc_html( $hour );
	$output .= '<br>';
	$output .= '<div class="master-program-user-hour rs-time" data="' . esc_attr( $raw_hour ) . '" data-format="' . esc_attr( $data_format ) . '"></div>';
	$output .= '</div>';
	$output .= '</th>';

	foreach ( $weekdays as $weekday ) {

		// --- clear the cell ---
		if ( isset( $cell ) ) {
			unset( $cell );
		}
		$cellcontinued = $showcontinued = $overflow = $newshift = false;
		// $fullcell = $partcell = false;
		$cellshifts = 0;

		// --- get shifts for this day ---
		if ( isset( $schedule[$weekday] ) ) {
			$shifts = $schedule[$weekday];
		} else {
			$shifts = array();
		}
		$nextday = radio_station_get_next_day( $weekday );

		// --- get hour and next hour start and end times ---
		$hour_start = strtotime( $weekday . ' ' . $hour );
		$hour_end = $next_hour_start = $hour_start + ( 60 * 60 );
		$next_hour_end = $hour_end + ( 60 * 60 );

		// --- loop the shifts for this day ---
		foreach ( $shifts as $shift ) {

			if ( !isset( $shift['finished'] ) || !$shift['finished'] ) {

				// --- get shift start and end times ---
				$display = $nowplaying = false;
				if ( '00:00 am' == $shift['start'] ) {
					$shift_start = strtotime( $weekday . ' 12:00 am' );
				} else {
					$shift_start = strtotime( $weekday . ' ' . $shift['start'] );
				}
				if ( ( '11:59:59 pm' == $shift['end'] ) || ( '12:00 am' == $shift['end'] ) ) {
					$shift_end = strtotime( $nextday . ' 12:00 am' );
				} else {
					$shift_end = strtotime( $weekday . ' ' . $shift['end'] );
				}

				if ( isset( $_GET['shiftdebug'] ) && ( '1' == $_GET['shiftdebug'] ) ) {
					$test .= $weekday . ' - ' . $hour . ' - ' . $nexthour . '<br>';
					$test .= '<br>' . $shift_start . '--' . $hour_start . '--' . $next_hour_start . '<br>';
					$test .= date( 'l H:i', $shift_start ) . '-' . date( 'l H:i', $hour_start ) . '-' . date( 'l H:i', $next_hour_start ) . '<br>';
					$test .= '<br>' . $shift_end . '--' . $hour_end . '<br>';
					$test .= date( 'l H:i', $shift_end ) . '-' . date( 'l H:i', $hour_end ) . '<br>';
					$test .= print_r( $shift, true ) . '<br>';
				}

				// --- check if the shift is starting / started ---
				if ( isset( $shift['started'] ) && $shift['started'] ) {
					// continue display of shift
					if ( !isset( $cell ) ) {
						$cellcontinued = true;
					}
					$display = true;
					$showcontinued = true;
					$cellshifts ++;
				} elseif ( $shift_start == $hour_start ) {
					// start display of shift
					$started = $shift['started'] = true;
					$schedule[$weekday][$shift['start']] = $shift;
					$display = true;
					$showcontinued = false;
					$cellshifts ++;
				} elseif ( ( $shift_start > $hour_start )
				           && ( $shift_start < $next_hour_start ) ) {
					// start display of shift
					$started = $shift['started'] = true;
					$schedule[$weekday][$shift['start']] = $shift;
					$display = true;
					$newshift = true;
					$cellshifts ++;
				}

				// --- check if shift is current ---
				if ( ( $now >= $shift_start ) && ( $now < $shift_end ) ) {
					$nowplaying = true;
				}

				// --- check if shift finishes in this hour ---
				if ( isset( $shift['started'] ) && $shift['started'] ) {
					if ( $shift_end == $hour_end ) {
						$finished = $shift['finished'] = true;
						$schedule[$weekday][$shift['start']] = $shift;
						// $fullcell = true;
					} elseif ( $shift_end < $hour_end ) {
						$finished = $shift['finished'] = true;
						$schedule[$weekday][$shift['start']] = $shift;
						// $percent = round( ( $shift_end - $hour_start ) / 3600 );
						// $partcell = true;
					} else {
						$overflow = true;
					}
				}

				// --- maybe add shift display to the cell ---
				if ( $display ) {

					$show = $shift['show'];

					// --- set the show div classes ---
					$divclasses = array( 'master-show-entry', 'show-id-' . $show['id'], $show['slug'] );
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
					// if ( $finished ) {$divclasses[] = 'finished';}
					// if ( $fullcell ) {$divclasses[] = 'fullcell';}
					// if ( $partcell ) {$divclasses[] = 'partcell';}
					if ( isset( $show['genres'] ) && is_array( $show['genres'] ) && ( count( $show['genres'] ) > 0 ) ) {
						foreach ( $show['genres'] as $genre ) {
							$divclasses[] = sanitize_title_with_dashes( $genre );
						}
					}
					$divclass = implode( ' ', $divclasses );

					// --- start the cell contents ---
					if ( !isset( $cell ) ) {
						$cell = '';
					}
					$cell .= '<div class="' . esc_attr( $divclass ) . '">';

					if ( $showcontinued ) {
						// --- display empty div (for highlighting) ---
						$cell .= '&nbsp;';
					} else {

						// --- show logo / thumbnail ---
						if ( $atts['show_image'] ) {
							// 2.3.0: filter show avatar via show ID and context
							$show_avatar = radio_station_get_show_avatar( $show['id'], $avatar_size );
							$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show['id'], 'table' );
							if ( $show_avatar ) {
								$cell .= '<span class="show-image">';
								$cell .= $show_avatar;
								$cell .= '</span>';
							}
						}

						// --- show title ---
						$cell .= '<span class="show-title">';
						$show_link = false;
						if ( $atts['show_link'] ) {
							// 2.3.0: filter show link via show ID and context
							$show_link = apply_filters( 'radio_station_schedule_show_link', $show['url'], $show['id'], 'table' );
						}
						if ( $show_link ) {
							$cell .= '<a href="' . $show_link . '">';
						}
						$cell .= $show['name'];
						if ( $show_link ) {
							$cell .= '</a>';
						}
						$cell .= '</span>';

						// --- show DJs / hosts ---
						if ( $atts['show_djs'] || $atts['show_hosts'] ) {

							$hosts = '';
							if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

								$hosts .= '<span class="show-dj-names-leader show-host-names-leader"> ';
								$hosts .= esc_html( __( 'with', 'radio-station' ) );
								$hosts .= ' </span>';

								$count = 0;
								$hostcount = count( $show['hosts'] );
								foreach ( $show['hosts'] as $host ) {
									$count ++;
									$user_info = get_userdata( $host );
									$hosts .= $user_info->display_name;

									if ( ( ( 1 === $count ) && ( 2 === $hostcount ) )
									     || ( ( $hostcount > 2 ) && ( ( $hostcount - 1 ) === $count ) ) ) {
										$hosts .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
									} elseif ( ( $count < $hostcount ) && ( $hostcount > 2 ) ) {
										$hosts .= ', ';
									}
								}
							}

							$hosts = apply_filters( 'radio_station_schedule_show_hosts', $hosts, $show['id'], 'table' );
							if ( $hosts ) {
								$cell .= '<span class="show-dj-names show-host-names">';
								$cell .= $hosts;
								$cell .= '</span>';
							}
						}

						// --- show time ---
						if ( $atts['display_show_time'] ) {

							// --- convert shift time data ---
							$shift_start_time = strtotime( $shift['day'] . ' ' . $shift['start'] );
							$shift_end_time = strtotime( $shift['day'] . ' ' . $shift['end'] );

							// --- convert shift time for display ---
							if ( '00:00 am' == $shift['start'] ) {
								$shift['start'] = '12:00 am';
							}
							if ( '11:59:59 pm' == $shift['end'] ) {
								$shift['end'] = '12:00 am';
							}
							if ( 24 == (int) $atts['time'] ) {
								$start = radio_station_convert_shift_time( $shift['start'], 24 );
								$end = radio_station_convert_shift_time( $shift['end'], 24 );
								$data_format = 'G:i';
							} else {
								$start = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm ), $shift['start'] );
								$end = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm ), $shift['end'] );
								$data_format = 'H:i a';
							}

							$show_time = '<span class="rs-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $data_format ) . '">' . $start . '</span>';
							$show_time .= ' ' . esc_html( __( 'to', 'radio-station' ) ) . ' ';
							$show_time .= '<span class="rs-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $data_format ) . '">' . $end . '</span>';
							$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show['id'], 'table' );
							$cell .= '<div class="show-time" id="show-time-' . esc_attr( $tcount ) . '">' . $show_time . '</div>';
							$cell .= '<div class="show-user-time" id="show-user-time-' . esc_attr( $tcount ) . '"></div>';
							$tcount ++;

						}

						// --- encore airing ---
						if ( $atts['show_encore'] ) {
							$encore = apply_filters( 'radio_station_schedule_show_encore', $shift['encore'], $show['id'], 'table' );
							if ( 'on' == $encore ) {
								$cell .= '<span class="show-encore">';
								$cell .= esc_html( __( 'encore airing', 'radio-station' ) );
								$cell .= '</span>';
							}
						}

						// --- show file ---
						if ( $atts['show_file'] ) {
							$show_file = get_post_meta( $show['id'], 'show_file', true );
							$show_file = apply_filters( 'radio_station_schedule_show_file', $show_file, $show['id'], 'table' );
							if ( $show_file && !empty( $show_file ) ) {
								$cell .= '<span class="show-file">';
								$cell .= '<a href="' . esc_url( $show_file ) . '">';
								$cell .= esc_html( __( 'Audio File', 'radio-station' ) );
								$cell .= '</a>';
								$cell .= '</span>';
							}
						}
					}
					$cell .= '</div>';
				}

			}
		}

		// --- add cell to hour row - weekday column ---
		$cellclasses = array( 'show-info', $weekday );
		if ( $cellcontinued ) {
			$cellclasses[] = 'continued';
		}
		if ( $overflow ) {
			$cellclasses[] = 'overflow';
		}
		if ( $cellshifts > 0 ) {
			$cellclasses[] = $cellshifts . '-shifts';
		}
		$cellclass = implode( ' ', $cellclasses );
		$output .= '<td class="' . $cellclass . '">';
		$output .= "<div class='show-wrap'>";
		if ( isset( $cell ) ) {
			$output .= $cell;
		}
		$output .= "</div>";
		$output .= '</td>';
	}

	// --- close hour row ---
	$output .= '</tr>';
}

$output .= '</table>';

if ( isset( $_GET['shiftdebug'] ) && ( '1' == $_GET['shiftdebug'] ) ) {
	$output .= $test;
}
