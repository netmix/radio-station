<?php
/**
 * Template for master schedule shortcode legacy (table) style.
 */

// --- filter show avatar size ---
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail', 'legacy' );

// --- clear floats ---
$output .= '<div style="clear:both;"></div>';

// --- create the output in a table ---
$output .= '<table id="master-program-schedule">';

// --- output the headings in the correct order ---
$output .= '<tr class="master-program-day-row"> <th></th>';
foreach ( $days_of_the_week as $weekday => $info ) {
	// 2.2.2: fix to translate incorrect variable (heading)
	$heading = substr( $weekday, 0, 3 );
	$heading = radio_station_translate_weekday( $heading, true );
	$output .= '<th>' . $heading . '</th>';
}
$output .= '</tr>';

if ( !isset( $nextskip ) ) {
	$nextskip = array();
}

foreach ( $master_list as $hour => $days ) {

	$output .= '<tr class="master-program-hour-row">';

	$output .= '<th class="master-program-hour"><div>';

	// 2.2.7: added meridiem translations
	if ( 12 === (int) $atts['time'] ) {
		if ( 0 === $hour ) {
			$output .= '12' . radio_station_translate_meridiem( 'am' );
		} elseif ( (int) $hour < 12 ) {
			$output .= $hour . radio_station_translate_meridiem( 'am' );
		} elseif ( 12 === (int) $hour ) {
			$output .= '12' . radio_station_translate_meridiem( 'pm' );
		} else {
			$output .= ( $hour - 12 ) . radio_station_translate_meridiem( 'pm' );
		}
	} else {
		if ( $hour < 10 ) {
			$output .= '0';
		}
		$output .= $hour . ':00';
	}

	$output .= '</div></th>';

	$curskip = $nextskip;
	$nextskip = array();

	foreach ( $days as $day => $min ) {

		// overly complex way of determining if we need to accomodate a rowspan due to a show spanning multiple hours
		$continue = 0;
		foreach ( $curskip as $x => $skip ) {

			if ( $skip['day'] === $day ) {
				if ( $skip['span'] > 1 ) {
					$continue = 1;
					$skip['span'] = $skip['span'] - 1;
					$curskip[$x]['span'] = $skip['span'];
					$nextskip = $curskip;
				}
			}
		}

		$rowspan = 0;
		foreach ( $min as $show ) {

			if ( 0 === (int) $show['time']['start_hour'] && 0 === (int) $show['time']['end_hour'] ) { ///midnight to midnight shows
				if ( $show['time']['start_min'] === $show['time']['end_min'] ) { //accomodate shows that end midway through the 12am hour
					$rowspan = 24;
				}
			}

			if ( 0 === (int) $show['time']['end_hour'] && 0 !== (int) $show['time']['start_hour'] ) {
				//fix shows that end at midnight (BUT take into account shows that start at midnight and end before the hour is up e.g. 12:00 - 12:30am), otherwise you could end up with a negative row span
				$rowspan = $rowspan + ( 24 - $show['time']['start_hour'] );
			} elseif ( $show['time']['start_hour'] > $show['time']['end_hour'] ) {
				// show runs from before midnight night until the next morning
				if ( isset( $show['time']['real_start'] ) ) {
					// if we're on the second day of a show that spans two days
					$rowspan = $show['time']['end_hour'];
				} else {
					// if we're on the first day of a show that spans two days
					$rowspan = $rowspan + ( 24 - $show['time']['start_hour'] );
				}
			} else {
				// all other shows
				$rowspan = $rowspan + ( $show['time']['end_hour'] - $show['time']['start_hour'] );
			}
		}

		$span = '';
		if ( $rowspan > 1 ) {
			$span = ' rowspan="' . $rowspan . '"';
			// add to both arrays
			$curskip[] = array(
				'day'  => $day,
				'span' => $rowspan,
				'show' => get_the_title( $show['id'] ),
			);
			$nextskip[] = array(
				'day'  => $day,
				'span' => $rowspan,
				'show' => get_the_title( $show['id'] ),
			);
		}

		// if we need to accomodate a rowspan, skip this iteration so we don't end up with an extra table cell.
		if ( $continue ) {
			continue;
		}

		$output .= '<td' . $span . '>';

		foreach ( $min as $show ) {

			$terms = wp_get_post_terms( $show['id'], RADIO_STATION_GENRES_SLUG, array() );
			$classes = ' show-id-' . $show['id'] . ' ' . sanitize_title_with_dashes( str_replace( '_', '-', get_the_title( $show['id'] ) ) ) . ' ';
			foreach ( $terms as $show_term ) {
				$classes .= sanitize_title_with_dashes( $show_term->name ) . ' ';
			}

			$output .= '<div class="master-show-entry' . $classes . '">';

			if ( $atts['show_image'] ) {
				// 2.3.0: get show avatar filtered by show ID and context
				$show_avatar = radio_station_get_show_avatar( $show['id'] );
				$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show['id'], 'legacy' );
				if ( $show_avatar ) {
					$output .= '<span class="show-image">' . $show_avatar . '</span>';
				}
			}

			$output .= '<span class="show-title">';
			if ( $atts['show_link'] ) {
				// 2.3.0: filter show link via show ID
				$show_link = get_permalink( $show['id'] );
				$show_link = apply_filters( 'radio_station_schedule_show_link', $show_link, $show['id'], 'legacy' );
				if ( $show_link ) {
					$output .= '<a href="' . $show_link . '">' . get_the_title( $show['id'] ) . '</a>';
				} else {
					$output .= get_the_title( $show['id'] );
				}
			} else {
				$output .= get_the_title( $show['id'] );
			}
			$output .= '</span>';

			if ( $atts['show_djs'] ) {

				$output .= '<span class="show-dj-names">';

				$dj_names = get_post_meta( $show['id'], 'show_user_list', true );
				$count = 0;

				if ( $dj_names ) {

					$output .= '<span class="show-dj-names-leader"> ';
					$output .= esc_html( __( 'with', 'radio-station' ) );
					$output .= ' </span>';

					foreach ( $dj_names as $name ) {
						$count ++;
						$user_info = get_userdata( $name );

						$output .= $user_info->display_name;

						$names_count = count( $dj_names );
						if ( ( 1 === $count && 2 === $names_count ) || ( $names_count > 2 && $count === $names_count - 1 ) ) {
							$output .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
						} elseif ( $count < $names_count && $names_count > 2 ) {
							$output .= ', ';
						}
					}
				}

				$output .= '</span>';
			}

			if ( $atts['display_show_time'] ) {

				$output .= '<span class="show-time">';

				if ( 12 === (int) $atts['time'] ) {

					// 2.2.7: added meridiem translation
					$starttime = strtotime( '1981-04-28 ' . $show['time']['start_hour'] . ':' . $show['time']['start_min'] . ':00 ' );
					$endtime = strtotime( '1981-04-28 ' . $show['time']['end_hour'] . ':' . $show['time']['end_min'] . ':00 ' );
					$times = date( 'g:i', $starttime ) . ' ' . radio_station_translate_meridiem( date( 'a', $starttime ) );
					$times .= ' - ';
					$times .= date( 'g:i', $endtime ) . ' ' . radio_station_translate_meridiem( date( 'a', $endtime ) );

				} else {
					$times = date( 'H:i', strtotime( '1981-04-28 ' . $show['time']['start_hour'] . ':' . $show['time']['start_min'] . ':00 ' ) );
					$times .= ' - ';
					$times .= date( 'H:i', strtotime( '1981-04-28 ' . $show['time']['end_hour'] . ':' . $show['time']['end_min'] . ':00 ' ) );
				}
				$time = apply_filters( 'radio_station_schedule_show_time', $times, $show['id'], 'legacy' );
				$output .= $time;
				$output .= '</span>';
			}

			// --- encore airing ---
			if ( $atts['show_encore'] ) {
				// 2.3.0: filter encore switch by show ID and context
				if ( isset( $show['time']['encore'] ) ) {
					$encore = $show['time']['encore'];
				} else {
					$encore = false;
				}
				$encore = apply_filters( 'radio_station_schedule_show_encore', $encore, $show['id'], 'legacy' );
				if ( 'on' == $encore ) {
					$output .= '<span class="show-encore">';
					$output .= esc_html( __( 'encore airing', 'radio-station' ) );
					$output .= '</span>';
				}
			}

			// --- show file ---
			if ( $atts['show_file'] ) {
				// 2.3.0: filter show file by show ID and context
				$show_file = get_post_meta( $show['id'], 'show_file', true );
				$show_file = apply_filters( 'radio_station_schedule_show_file', $show_file, $show['id'], 'legacy' );
				if ( $show_file && !empty( $show_file ) ) {
					// 2.3.0: added missing span close tag
					$output .= '<span class="show-file"><a href="' . esc_url( $show_file ) . '">';
					$output .= esc_html( __( 'Audio File', 'radio-station' ) ) . '</a>';
					$output .= '</span>';
				}
			}

			$output .= '</div>';
		}
		$output .= '</td>';
	}

	$output .= '</tr>';
}
$output .= '</table>';
