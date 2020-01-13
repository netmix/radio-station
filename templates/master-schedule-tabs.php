<?php
/**
 * Template for master schedule shortcode tabs style.
 * ref: http://nlb-creations.com/2014/06/06/radio-station-tutorial-creating-a-tabbed-programming-schedule/
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

// --- start tabbed schedule output ---
$output .= '<ul id="master-schedule-tabs">';

$panels = '';
$tcount = 0;
// 2.3.0: loop weekdays instead of legacy master list
foreach ( $weekdays as $day ) {

	// 2.2.2: use translate function for weekday string
	$display_day = radio_station_translate_weekday( $day );
	$output .= '<li class="master-schedule-tabs-day" id="master-schedule-tabs-header-' . strtolower( $day ) . '">';
	$output .= '<div class="master-schedule-tabs-day-name">' . $display_day . '</div>';
	$output .= '</li>';

	// 2.2.7: separate headings from panels for tab view
	$panels .= '<ul class="master-schedule-tabs-panel" id="master-schedule-tabs-day-' . strtolower( $day ) . '">';

	// --- get shifts for this day ---
	if ( isset( $schedule[$day] ) ) {
		$shifts = $schedule[$day];
	} else {
		$shifts = array();
	}

	$foundshows = false;

	// 2.3.0: loop schedule day shifts instead of hours and minutes
	if ( count( $shifts ) > 0 ) {

		$foundshows = true;

		foreach ( $shifts as $shift ) {

			$show = $shift['show'];

			$panels .= '<li class="master-schedule-tabs-show">';

			// --- Show Image ---
			// (defaults to display on)
			if ( ( 'false' !== $atts['show_image'] ) && ( 0 !== $atts['show_image'] ) ) {
				// 2.3.0: filter show avatar by show and context
				$show_avatar = radio_station_get_show_avatar( $show['id'], $avatar_size );
				$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show['id'], 'tabs' );
				if ( $show_avatar ) {
					$panels .= '<div class="show-image">';
					$panels .= $show_avatar;
					$panels .= '</div>';
				}
			}

			// --- Show Information ---
			$panels .= '<div class="show-info">';

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
			$panels .= '<span class="show-title">';
			$panels .= $show_title;
			$panels .= '</span>';

			// --- show hosts ---
			if ( $atts['show_djs'] || $atts['show_hosts'] ) {

				$hosts = '';
				if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

					$count = 0;
					$host_count = count( $show['hosts'] );
					$hosts .= '<span class="show-dj-names-leader"> ';
					$hosts .= esc_html( __( 'with', 'radio-station' ) );
					$hosts .= ' </span>';

					foreach ( $show['hosts'] as $host ) {
						$count ++;
						$user_info = get_userdata( $host );
						$hosts .= $user_info->display_name;

						if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
						     || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
							$hosts .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
						} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
							$hosts .= ', ';
						}
					}
				}

				$hosts = apply_filters( 'radio_station_schedule_show_hosts', $hosts, $show['id'], 'tabs' );
				if ( $hosts ) {
					$panels .= '<div class="show-dj-names">';
					$panels .= $hosts; // phpcs:ignore WordPress.Security.OutputNotEscaped
					$panels .= '</div>';
				}
			}

			// --- show times ---
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
					$data_format = 'G:i';
				} else {
					$start = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm), $shift['start'] );
					$end = str_replace( array( 'am', 'pm'), array( ' ' . $am, ' ' . $pm), $shift['end'] );
					$data_format = 'H:i a';
				}

				// 2.3.0: filter show time by show and context
				$show_time = '<span class="rs-time" data="' . esc_attr( $shift_start_time ) . '" data-format="H:i">' . $start . '</span>';
				$show_time .= ' ' . esc_html( __( 'to', 'radio-station' ) ) . ' ';
				$show_time .= '<span class="rs-time" data="' . esc_attr( $shift_end_time ) . '" data-format="H:i">' . $end . '</span>';
				$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show['id'], 'tabs' );

				$panels .= '<div class="show-time" id="show-time-' . esc_attr( $tcount ) . '">' . $show_time . '</div>';
				$panels .= '<div class="show-user-time" id="show-user-time-' . esc_attr( $tcount ) . '"></div>';
				$tcount ++;

			}

			// --- encore ---
			// 2.3.0: filter encore switch by show and context
			if ( $atts['show_encore'] ) {
				if ( isset( $shift['encore'] ) ) {
					$show_encore = $shift['encore'];
				} else {
					$show_encore = false;
				}
				$show_encore = apply_filters( 'radio_station_schedule_show_encore', $show_encore, $show['id'], 'tabs' );
				if ( 'on' == $show_encore ) {
					$panels .= ' <span class="show-encore">';
					$panels .= esc_html( __( 'encore airing', 'radio-station' ) );
					$panels .= '</span>';
				}
			}

			// --- show audio file ---
			if ( $atts['show_file'] ) {
				// 2.3.0: filter audio file by show and context
				$show_file = get_post_meta( $show['id'], 'show_file', true );
				$show_file = apply_filters( 'radio_station_schedule_show_link', $show_file, $show['id'], 'tabs' );
				if ( $show_file && !empty( $show_file ) ) {
					$panels .= '<span class="show-file">';
					$panels .= '<a href="' . esc_url( $show_file ) . '">';
					$panels .= esc_html( __( 'Audio File', 'radio-station' ) );
					$panels .= '</a>';
					$panels .= '</span>';
				}
			}

			$panels .= '</div>';

			// --- Show Genres list ---
			// (defaults to display on)
			if ( 'false' !== $atts['show_genres'] ) {
				$panels .= '<div class="show-genres">';
				$terms = wp_get_post_terms( $show['id'], RADIO_STATION_GENRES_SLUG, array() );
				$genres = array();
				if ( count( $terms ) > 0 ) {
					foreach ( $terms as $term ) {
						$genres[] = '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
					}
					$genre_display = implode( ', ', $genres );
					$panels .= esc_html( __( 'Genres', 'radio-station' ) ) . ': ' . $genre_display;
				}
				$panels .= '</div>';
			}

			$panels .= '</li>';
		}
	}

	if ( !$foundshows ) {
		$panels .= '<li class="master-schedule-tabs-show">';
		$panels .= esc_html( __( 'No Shows found for this day.', 'radio-station' ) );
		$panels .= '</li>';
	}

	$panels .= '</ul>';
}

$output .= '</ul>';

$output .= '<div id="master-schedule-tab-panels">';
$output .= $panels;
$output .= '</div>';
