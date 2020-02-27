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
$output .= '<ul id="master-list" class="master-list">';

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

			// 2.3.0: filter show link by show and context
			$show_link = false;
			if ( $atts['show_link'] ) {
				$show_link = apply_filters( 'radio_station_schedule_show_link', $show['url'], $show['id'], 'list' );
			}

			// 2.3.0: add genre classes for highlighting
			$classes = array( 'master-list-day-item' );
			$terms = wp_get_post_terms( $show['id'], RADIO_STATION_GENRES_SLUG, array() );
			if ( $terms && ( count( $terms ) > 0 ) ) {
				foreach ( $terms as $term ) {
					$classes[] = strtolower( $term->slug );
				}
			}
			$class = implode( ' ', $classes );

			$output .= '<li class="' . esc_attr( $class ) . '">';

			// --- show avatar ---
			if ( $atts['show_image'] ) {
				// 2.3.0: filter show avatar via show ID and context
				$show_avatar = radio_station_get_show_avatar( $show['id'], $avatar_size );
				$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show['id'], 'list' );
				if ( $show_avatar ) {
					$output .= '<div class="show-image">';
					if ( $show_link ) {
						$output .= '<a href="' . esc_url( $show_link ) . '">' . $show_avatar . '</a>';
					} else {
						$output .= $show_avatar;
					}
					$output .= '</div>';
				}
			}

			// --- show title ---
			if ( $show_link ) {
				$show_title = '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
			} else {
				$show_title = esc_html( $show['name'] );
			}
			$output .= '<span class="show-title">';
			$output .= $show_title;
			$output .= '</span>';

			// --- show hosts ---
			// 2.3.0: changed from show_djs
			if ( $atts['show_hosts'] ) {

				$hosts = '';
				if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

					$count = 0;
					$host_count = count( $show['hosts'] );
					$hosts .= '<span class="show-dj-names-leader">';
                    $hosts .= esc_html( __( 'with', 'radio-station' ) );
                    $host .= ' </span>';

					foreach ( $show['hosts'] as $host ) {
						$count ++;
						// 2.3.0: added link_hosts attribute check
						if ( $atts['link_hosts'] && !empty( $host['url'] ) ) {
							$hosts .= '<a href="' . esc_url( $host['url'] ) . '">' . esc_html( $host['name'] ) . '</a>';
						} else {
							$hosts .= esc_html( $host['name'] );
						}

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
					$output .= '<div class="show-dj-names show-host-names">';
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
					$output .= '<div class="show-encore">';
					$output .= esc_html( __( 'encore airing', 'radio-station' ) );
					$output .= '</div>';
				}
			}

			// --- show file ---
			if ( $atts['show_file'] ) {
				// 2.3.0: filter show file by show and context
				$show_file = get_post_meta( $show['id'], 'show_file', true );
				$show_file = apply_filters( 'radio_station_schedule_show_link', $show_file, $show['id'], 'list' );
				if ( $show_file && !empty( $show_file ) ) {
					$output .= '<div class="show-file">';
					$output .= '<a href="' . esc_url( $show_file ) . '">';
					$output .= esc_html( __( 'Audio File', 'radio-station' ) );
					$output .= '</a>';
					$output .= '</div>';
				}
			}

			// --- show genres ---
			// (defaults to on)
			// 2.3.0: add genres to list view
			if ( $atts['show_genres'] ) {
				$output .= '<div class="show-genres">';
				$genres = array();
				if ( count( $terms ) > 0 ) {
					foreach ( $terms as $term ) {
						$genres[] = '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
					}
					$genre_display = implode( ', ', $genres );
					$output .= esc_html( __( 'Genres', 'radio-station' ) ) . ': ' . $genre_display;
				}
				$output .= '</div>';
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
