<?php
/**
 * Template for master schedule shortcode div style.
 */

$now = radio_station_get_now();
$date = radio_station_get_time( 'date', $now );

// --- set shift time formats ---
// 2.3.2: set time formats early
if ( 24 == (int) $atts['time_format'] ) {
	$start_data_format = $end_data_format = 'H:i';
} else {
	$start_data_format = $end_data_format = 'g:i a';
}
$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'schedule-div', $atts );
$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'schedule-div', $atts );

// --- get schedule days and dates ---
// 2.3.2: allow for start day attibute
if ( isset( $atts['start_day'] ) && $atts['start_day'] ) {
	$weekdays = radio_station_get_schedule_weekdays( $atts['start_day'] );
} else {
	$weekdays = radio_station_get_schedule_weekdays();
}
$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now );

// --- filter show avatar size ---
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail', 'div' );

// output some dynamic styles
$output .= '<style type="text/css">';
for ( $i = 2; $i < 24; $i++ ) {
	$rowheight = $atts['divheight'] * $i;
	$output .= '#master-schedule-divs .rowspan' . $i . ' { ';
	$output .= 'height: ' . ( $rowheight ) . 'px; }';
}

$output .= '#master-schedule-divs .rowspan-half { height: 15px; margin-top: -7px; }';
$output .= '#master-schedule-divs .rowspan { top: ' . $atts['divheight'] . 'px; }';
$output .= '</style>';

// output the schedule
$output .= '<div id="master-schedule-divs">';
// $weekdays = array_keys( $days_of_the_week );

$output .= '<div class="master-schedule-hour">';
$output .= '<div class="master-schedule-hour-header">&nbsp;</div>';
foreach ( $weekdays as $weekday ) {
	$display_day = radio_station_translate_weekday( $weekday );
	$output    .= '<div class="master-schedule-weekday-header master-schedule-weekday">' . esc_html( $display_day ) . '</div>';
}
$output .= '</div>';

foreach ( $master_list as $hour => $days ) {

	$output .= '<div class="master-schedule-' . $hour . ' master-schedule-hour">';

	// output the hour labels
	$output .= '<div class="master-schedule-hour-header">';
	if ( 12 === (int) $atts['time_format'] ) {
		// random date needed to convert time to 12-hour format
		$output .= date( 'ga', strtotime( '1981-04-28 ' . $hour . ':00:00' ) );
	} else {
		// random date needed to convert time to 24-hour format
		$output .= date( 'H:i', strtotime( '1981-04-28 ' . $hour . ':00:00' ) );
	}
	$output .= '</div>';

	foreach ( $weekdays as $weekday ) {
		$output .= '<div class="master-schedule-' . strtolower( $weekday ) . ' master-schedule-weekday" style="height: ' . $atts['divheight'] . 'px;">';
		if ( isset( $days[ $weekday ] ) ) {
			foreach ( $days[ $weekday ] as $min => $show ) {

				// --- genre terms ---
				// TODO: check term output formatting
				$terms   = wp_get_post_terms( $show['id'], RADIO_STATION_GENRES_SLUG, array() );
				$classes = array( 'master-show-entry', 'show-id-' . $show['id'] );
				$classes[] = sanitize_title_with_dashes( str_replace( '_', '-', get_the_title( $show['id'] ) ) );
				foreach ( $terms as $term ) {
					// $classes .= sanitize_title_with_dashes( $show_term->name ) . ' ';
					$classes[] = $term->slug;
				}
				// 2.5.6: fix for uncombined class list
				$classlist = implode( ' ', $classes );

				$output .= '<div class="' . esc_attr( $classlist ) . '">';

				// --- show avatar ---
				if ( $atts['show_image'] ) {
					// 2.3.0: filter show avatar by show and context
					$show_avatar = radio_station_get_show_avatar( $show['id'], $avatar_size );
					$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show['id'], 'tabs' );
					if ( $show_avatar ) {
						$output .= '<div class="show-image">' . $show_avatar . '</div>';
					}
				}

				// --- show title / link ---
				$show_title = get_the_title( $show['id'] );
				if ( $atts['show_link'] ) {
					// 2.3.0: filter show link by show and context
					$show_link = get_permalink( $show['id'] );
					$show_link = apply_filters( 'radio_station_schedule_show_link', $show_link, $show['id'], 'div' );
					if ( $show_link ) {
						$show_title .= '<a href="' . esc_url( $show_link ) . '">' . $show_title . '</a>';
					}
				}
				$output .= '<div class="show-title">';
					$output .= $show_title;
				$output .= '</div>';

				// list of DJs
				// 2.3.0: changed from show_djs
				if ( $atts['show_hosts'] ) {

					$output .= '<span class="show-dj-names show-host-names">';

					$show_names = get_post_meta( $show['id'], 'show_user_list', true );
					$show_names = apply_filters( 'radio_station_show_hosts', $show_names, $show['id'] );
					$count = 0;

					if ( $show_names ) {

						$output .= '<span class="show-dj-names-leader"> ' . esc_html( __( 'with', 'radio-station' ) ) . '</span>';

						foreach ( $show_names as $name ) {

							$count++;
							$user_info = get_userdata( $name );

							$output .= $user_info->display_name;

							$names_count = count( $show_names );
							if ( ( 1 === $count && 2 === $names_count ) || ( $names_count > 2 && $count === $names_count - 1 ) ) {
								$output .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
							} elseif ( $count < $names_count && $names_count > 2 ) {
								$output .= ', ';
							}
						}
					}

					$output .= '</span>';
				}

				// --- show time ---
				if ( $atts['show_times'] ) {

					$output .= '<span class="show-time">';

					if ( 12 === (int) $atts['time_format'] ) {
						$show_time = date( 'g:i a', strtotime( '1981-04-28 ' . $show['time']['start_hour'] . ':' . $show['time']['start_min'] . ':00 ' ) );
						$show_time .= ' - ';
						$show_time .= date( 'g:i a', strtotime( '1981-04-28 ' . $show['time']['end_hour'] . ':' . $show['time']['end_min'] . ':00 ' ) );
					} else {
						$show_time = date( 'H:i', strtotime( '1981-04-28 ' . $show['time']['start_hour'] . ':' . $show['time']['start_min'] . ':00 ' ) );
						$show_time .= ' - ';
						$show_time .= date( 'H:i', strtotime( '1981-04-28 ' . $show['time']['end_hour'] . ':' . $show['time']['end_min'] . ':00 ' ) );
					}

					/* if ( 12 === (int) $atts['time_format'] ) {
						$start_data_format = $end_data_format = 'g:i a';
					} else {
						$start_data_format = $end_data_format = 'H:i';
					}
					$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, '', $atts );
					$start_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, '', $atts );

					$start = radio_station_get_time( $shift_start_time );
					$end = radio_station_get_time( $shift_end_time );
					$start = radio_station_translate_time( $start );
					$end = radio_station_translate_time( $end );
					*/

					// 2.3.0: filter show time by show and context
					// 2.4.0.4: added missing filter arguments for Pro
					$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show['id'], 'div', false, false );
					$output .= $show_time;
					$output .= '</span>';
				}

				// --- encore ---
				// 2.3.0: filter encore by show and context ---
				if ( isset( $show['time']['encore'] ) ) {
					$show_encore = $show['time']['encore'];
				} else {
					$show_encore = false;
				}
				$show_encore = apply_filters( 'radio_station_schedule_show_encore', $show_encore, $show['id'], 'list' );
				if ( 'on' == $show_encore ) {
					$output .= ' <span class="show-encore">' . esc_html( __( 'encore airing', 'radio-station' ) ) . '</span>';
				}

				// --- show file ---
				// 2.3.0: filter show file by show and context
				$show_file = get_post_meta( $show['id'], 'show_file', true );
				$show_file = apply_filters( 'radio_station_schedule_show_file', $show_file, $show['id'], 'div' );
				// 2.3.2: check disable download meta
				$disable_download = get_post_meta( $show['id'], 'show_download', true );
				if ( $show_file && ! empty( $show_file ) && !$disable_download ) {
					$output .= ' <span class="show-file"><a href="' . esc_url( $show_file ) . '">' . esc_html( __( 'Audio File', 'radio-station' ) ) . '</a>';
				}

				// calculate duration of show for rowspanning
				if ( isset( $show['time']['rollover'] ) ) { //show started on the previous day
					$duration = $show['time']['end_hour'];
				} else {
					if ( $show['time']['end_hour'] >= $show['time']['start_hour'] ) {
						$duration = $show['time']['end_hour'] - $show['time']['start_hour'];
					} else {
						$duration = 23 - $show['time']['start_hour'];
					}
				}

				if ( $duration >= 1 ) {
					$output .= '<div class="rowspan rowspan' . $duration . '"></div>';

					if ( '00' !== $show['time']['end_min'] ) {
						$output .= '<div class="rowspan rowspan-half"></div>';
					}
				}

				$output .= '</div>'; // end master-show-entry
			}
		}
		$output .= '</div>'; // end master-schedule-weekday
	}
	$output .= '</div>'; // end master-schedule-hour
}
$output .= '</div>'; // end master-schedule-divs
