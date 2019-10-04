<?php
/**
 * Template for master schedule shortcode default (table) style.
 */

// create the output in a table
$output .= radio_station_master_fetch_js_filter();

$output .= '<table id="master-program-schedule">';

// output the headings in the correct order
$output .= '<tr class="master-program-day-row"> <th></th>';
foreach ( $days_of_the_week as $weekday => $info ) {
	// 2.2.2: fix to translate incorrect variable (heading)
	$heading = substr( $weekday, 0, 3 );
	$heading = radio_station_translate_weekday( $heading, true );
	$output .= '<th>' . $heading . '</th>';
}
$output .= '</tr>';
// $output .= '<tr class="master-program-day-row"> <th></th> <th>'.__('Sun', 'radio-station').'</th> <th>'.__('Mon', 'radio-station').'</th> <th>'.__('Tue', 'radio-station').'</th> <th>'.__('Wed', 'radio-station').'</th> <th>'.__('Thu', 'radio-station').'</th> <th>'.__('Fri', 'radio-station').'</th> <th>'.__('Sat', 'radio-station').'</th> </tr>';

if ( ! isset( $nextskip ) ) {
	$nextskip = array();
}

foreach ( $master_list as $hour => $days ) {

	$output .= '<tr>';
	$output .= '<th class="master-program-hour"><div>';

	// 2.2.7: added meridiem translations
	if ( 12 === (int) $timeformat ) {
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

	$curskip  = $nextskip;
	$nextskip = array();

	foreach ( $days as $day => $min ) {

		// overly complex way of determining if we need to accomodate a rowspan due to a show spanning multiple hours
		$continue = 0;
		foreach ( $curskip as $x => $skip ) {

			if ( $skip['day'] === $day ) {
				if ( $skip['span'] > 1 ) {
					$continue              = 1;
					$skip['span']          = $skip['span'] - 1;
					$curskip[ $x ]['span'] = $skip['span'];
					$nextskip              = $curskip;
				}
			}
		}

		$rowspan = 0;
		foreach ( $min as $shift ) {

			if ( 0 === (int) $shift['time']['start_hour'] && 0 === (int) $shift['time']['end_hour'] ) { ///midnight to midnight shows
				if ( $shift['time']['start_min'] === $shift['time']['end_min'] ) { //accomodate shows that end midway through the 12am hour
					$rowspan = 24;
				}
			}

			if ( 0 === (int) $shift['time']['end_hour'] && 0 !== (int) $shift['time']['start_hour'] ) {
				//fix shows that end at midnight (BUT take into account shows that start at midnight and end before the hour is up e.g. 12:00 - 12:30am), otherwise you could end up with a negative row span
				$rowspan = $rowspan + ( 24 - $shift['time']['start_hour'] );
			} elseif ( $shift['time']['start_hour'] > $shift['time']['end_hour'] ) {
				// show runs from before midnight night until the next morning
				if ( isset( $shift['time']['real_start'] ) ) {
					// if we're on the second day of a show that spans two days
					$rowspan = $shift['time']['end_hour'];
				} else {
					// if we're on the first day of a show that spans two days
					$rowspan = $rowspan + ( 24 - $shift['time']['start_hour'] );
				}
			} else {
				// all other shows
				$rowspan = $rowspan + ( $shift['time']['end_hour'] - $shift['time']['start_hour'] );
			}
		}

		$span = '';
		if ( $rowspan > 1 ) {
			$span = ' rowspan="' . $rowspan . '"';
			// add to both arrays
			$curskip[]  = array(
				'day'  => $day,
				'span' => $rowspan,
				'show' => get_the_title( $shift['id'] ),
			);
			$nextskip[] = array(
				'day'  => $day,
				'span' => $rowspan,
				'show' => get_the_title( $shift['id'] ),
			);
		}

		// if we need to accomodate a rowspan, skip this iteration so we don't end up with an extra table cell.
		if ( $continue ) {
			continue;
		}

		$output .= '<td' . $span . '>';

		foreach ( $min as $shift ) {

			$terms   = wp_get_post_terms( $shift['id'], 'genres', array() );
			$classes = ' show-id-' . $shift['id'] . ' ' . sanitize_title_with_dashes( str_replace( '_', '-', get_the_title( $shift['id'] ) ) ) . ' ';
			foreach ( $terms as $shift_term ) {
				$classes .= sanitize_title_with_dashes( $shift_term->name ) . ' ';
			}

			$output .= '<div class="master-show-entry' . $classes . '">';

			if ( $atts['show_image'] ) {
				$output .= '<span class="show-image">';
				if ( has_post_thumbnail( $shift['id'] ) ) {
					$output .= get_the_post_thumbnail( $shift['id'], 'thumbnail' );
				}
				$output .= '</span>';
			}

			$output .= '<span class="show-title">';
			if ( $atts['show_link'] ) {
				$output .= '<a href="' . get_permalink( $shift['id'] ) . '">' . get_the_title( $shift['id'] ) . '</a>';
			} else {
				$output .= get_the_title( $shift['id'] );
			}
			$output .= '</span>';

			if ( $atts['show_djs'] ) {

				$output .= '<span class="show-dj-names">';

				$dj_names = get_post_meta( $shift['id'], 'show_user_list', true );
				$count    = 0;

				if ( $dj_names ) {

					$output .= '<span class="show-dj-names-leader"> ' . __( 'with', 'radio-station' ) . ' </span>';
					foreach ( $dj_names as $name ) {
						$count ++;
						$user_info = get_userdata( $name );

						$output .= $user_info->display_name;

						$names_count = count( $dj_names );
						if ( ( 1 === $count && 2 === $names_count ) || ( $names_count > 2 && $count === $names_count - 1 ) ) {
							$output .= ' and ';
						} elseif ( $count < $names_count && $names_count > 2 ) {
							$output .= ', ';
						}
					}
				}

				$output .= '</span>';
			}

			if ( $atts['display_show_time'] ) {

				$output .= '<span class="show-time">';

				if ( 12 === (int) $timeformat ) {

					// 2.2.7: added meridiem translation
					$starttime = strtotime( '1981-04-28 ' . $shift['time']['start_hour'] . ':' . $shift['time']['start_min'] . ':00 ' );
					$endtime = strtotime( '1981-04-28 ' . $shift['time']['end_hour'] . ':' . $shift['time']['end_min'] . ':00 ' );
					$output .= date( 'g:i', $starttime ) . ' ' . radio_station_translate_meridiem( date( 'a', $starttime ) );
					$output .= ' - ';
					$output .= date( 'g:i', $endtime ) . ' ' . radio_station_translate_meridiem( date( 'a', $endtime ) );

				} else {
					$output .= date( 'H:i', strtotime( '1981-04-28 ' . $shift['time']['start_hour'] . ':' . $shift['time']['start_min'] . ':00 ' ) );
					$output .= ' - ';
					$output .= date( 'H:i', strtotime( '1981-04-28 ' . $shift['time']['end_hour'] . ':' . $shift['time']['end_min'] . ':00 ' ) );
				}
				$output .= '</span>';
			}

			if ( isset( $shift['time']['encore'] ) && 'on' === $shift['time']['encore'] ) {
				$output .= '<span class="show-encore">' . __( 'encore airing', 'radio-station' ) . '</span>';
			}

			$show_link = get_post_meta( $shift['id'], 'show_file', true );
			if ( $show_link && ! empty( $show_link ) ) {
				$output .= '<span class="show-file"><a href="' . $show_link . '">' . __( 'Audio File', 'radio-station' ) . '</a>';
			}

			$output .= '</div>';
		}
		$output .= '</td>';
	}

	$output .= '</tr>';
}
$output .= '</table>';
