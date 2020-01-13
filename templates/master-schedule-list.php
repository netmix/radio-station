<?php
/**
 * Template for master schedule shortcode list style.
 */

// --- get all the required info ---
$weekdays = radio_station_get_schedule_weekdays();
$schedule = radio_station_get_current_schedule();
$hours = radio_station_get_hours();
$now = strtotime( current_time( 'mysql' ) );
$am = str_replace( ' ', '', radio_station_translate_meridiem( 'am' ) );
$pm = str_replace( ' ', '', radio_station_translate_meridiem( 'pm' ) );

// --- filter show avatar size ---
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail', 'tabs' );

// --- start list schedule output ---
$output .= '<ul class="master-list">';

$tcount = 0;
// 2.3.0: loop weekdays instead of legacy master list
foreach ( $weekdays as $day ) {

	// 2.2.2: use translate function for weekday string
	$display_day = radio_station_translate_weekday( $day );
	$output .= '<li class="master-list-day" id="list-header-' . strtolower( $day ) . '">';
	$output .= '<span class="master-list-day-name">' . esc_html( $display_day ) . '</span>';

	$output .= '<ul class="master-list-day-' . esc_attr( strtolower( $day ) ) . '-list">';

	// --- get shifts for this day ---
	if ( isset( $schedule[$day] ) ) {
		$shifts = $schedule[$day];
	} else {
		$shifts = array();
	}

	// 2.3.0: loop schedule day shifts instead of hours and minutes
	if ( count( $shifts ) > 0 ) {
		foreach ( $shifts as $shift ) {

			$show = $shift['show'];

			$output .= '<li class="master-list-day-item">';

			// --- show avatar ---
			if ( $atts['show_image'] ) {
				// 2.3.0: filter show avatar via show ID and context
				$show_avatar = radio_station_get_show_avatar( $show['id'], $avatar_size );
				$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show['id'], 'list' );
				if ( $show_avatar ) {
					$output .= '<div class="show-image">';
					$output .= $show_avatar;
					$output .= '</div>';
				}
			}

			// --- show title ---
			$show_title = get_the_title( $show['id'] );
			if ( $atts['show_link'] ) {
				// 2.3.0: filter show link by show and context
				$show_link = get_permalink( $show['id'] );
				$show_link = apply_filters( 'radio_station_schedule_show_link', $show_link, $show['id'], 'list' );
				if ( $show_link ) {
					$show_title = '<a href="' . esc_url( $show_link ) . '">' . $show_title . '</a>';
				}
			}
			$output .= '<span class="show-title">';
			$output .= $show_title;
			$output .= '</span>';

			// --- show hosts ---
			if ( $atts['show_djs'] || $atts['show_hosts'] ) {

				$hosts = '';
				if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

					$count = 0;
					$host_count = count( $show['hosts'] );
					$hosts .= '<span class="show-dj-names-leader"> ' . esc_html( __( 'with', 'radio-station' ) ) . ' </span>';
					foreach ( $show['hosts'] as $host ) {
						$count ++;
						$user_info = get_userdata( $host );
						$hosts .= $user_info->display_name;

						if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
						     || ( ( $host_count > 2 ) && ( ( $count === $host_count - 1 ) ) ) ) {
							$hosts .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
						} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
							$hosts .= ', ';
						}
					}
				}

				$hosts = apply_filters( 'radio_station_schedule_show_hosts', $hosts, $show['id'], 'tabs' );
				if ( $hosts ) {
					$output .= '<div class="show-dj-names">';
					$output .= $hosts;
					$output .= '</div>';
				}
			}

			// --- show time ---
			if ( $atts['display_show_time'] ) {

				// --- convert shift time data ---
				$shift_start_time = strtotime( $shift['day'] . ' ' . $shift['start'] );
				$shift_end_time = strtotime( $shift['day'] . ' ' . $shift['end'] );

				// --- convert shift time for display ---
				// 2.3.0: updated to use new schedule data
				if ( '00:00 am' == $shift['start'] ) {
					$shift['start'] = '12:00 am';
				}
				if ( '11:59:59 pm' == $shift['end'] ) {
					$shift['end'] = '12:00 am';
				}
				if ( 24 == (int) $atts['time'] ) {
					$start = radio_station_convert_shift_time( $shift['start'], 24 );
					$end = radio_station_convert_shift_time( $shift['end'], 24 );
					$data_format = "G:i";
				} else {
					$start = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm), $shift['start'] );
					$end = str_replace( array( 'am', 'pm'), array( '  ' . $am, ' ' . $pm), $shift['end'] );
					$data_format = "H:i a";
				}

				// 2.3.0: filter show time by show and context
				$show_time = '<span class="rs-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $data_format ) . '">' . $start . '</span>';
				$show_time .= ' ' . esc_html( __( 'to', 'radio-station' ) ) . ' ';
				$show_time .= '<span class="rs-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $data_format ) . '">' . $end . '</span>';
				$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show['id'], 'list' );

				$output .= '<div class="show-time" id="show-time-' . esc_attr( $tcount ) . '">' . $show_time . '</div>';
				$output .= '<div class="show-user-time" id="show-user-time-' . esc_attr( $tcount ) . '"></div>';
				$tcount ++;

			}

			// --- encore ---
			if ( $atts['show_encore'] ) {
				// 2.3.0: filter encore by show and context ---
				if ( isset( $shift['encore'] ) ) {
					$show_encore = $shift['encore'];
				} else {
					$show_encore = false;
				}
				$show_encore = apply_filters( 'radio_station_schedule_show_encore', $show_encore, $show['id'], 'list' );
				if ( 'on' == $show_encore ) {
					$output .= '<span class="show-encore">';
					$output .= esc_html( __( 'encore airing', 'radio-station' ) );
					$output .= '</span>';
				}
			}

			// --- show file ---
			if ( $atts['show_file'] ) {
				// 2.3.0: filter show file by show and context
				$show_file = get_post_meta( $show['id'], 'show_file', true );
				$show_file = apply_filters( 'radio_station_schedule_show_link', $show_file, $show['id'], 'list' );
				if ( $show_file && !empty( $show_file ) ) {
					$output .= '<span class="show-file">';
					$output .= '<a href="' . esc_url( $show_file ) . '">';
					$output .= esc_html( __( 'Audio File', 'radio-station' ) );
					$output .= '</a>';
					$output .= '</span>';
				}
			}

			$output .= '</li>';
		}
	}
	$output .= '</ul>';

	// --- close master list day item ---
	$output .= '</li>';
}

// --- close master list ---
$output .= '</ul>';
