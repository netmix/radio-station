<?php
/**
 * Template for master schedule shortcode list style.
 */
// output as a list
$flip = $days_of_the_week;
foreach ( $master_list as $hour => $days ) {
	foreach ( $days as $day => $mins ) {
		foreach ( $mins as $fmin => $fshow ) {
			$flip[ $day ][ $hour ][ $fmin ] = $fshow;
		}
	}
}

$output .= '<ul class="master-list">';

foreach ( $flip as $day => $hours ) {

	// 2.2.2: use translate function for weekday string
	$display_day = radio_station_translate_weekday( $day );
	$output     .= '<li class="master-list-day" id="list-header-' . strtolower( $day ) . '">';
	$output     .= '<span class="master-list-day-name">' . $display_day . '</span>';
	$output     .= '<ul class="master-list-day-' . strtolower( $day ) . '-list">';
	foreach ( $hours as $hour => $mins ) {

		foreach ( $mins as $min => $show ) {
			$output .= '<li>';

			if ( $atts['show_image'] ) {
				$output .= '<span class="show-image">';
				if ( has_post_thumbnail( $show['id'] ) ) {
					$output .= get_the_post_thumbnail( $show['id'], 'thumbnail' );
				}
				$output .= '</span>';
			}

			$output .= '<span class="show-title">';
			if ( $atts['show_link'] ) {
				$output .= '<a href="' . get_permalink( $show['id'] ) . '">' . get_the_title( $show['id'] ) . '</a>';
			} else {
				$output .= get_the_title( $show['id'] );
			}
			$output .= '</span>';

			if ( $atts['show_djs'] ) {
				$output .= '<span class="show-dj-names">';

				$show_names = get_post_meta( $show['id'], 'show_user_list', true );
				$count      = 0;

				if ( $show_names ) {
					$output .= '<span class="show-dj-names-leader"> '.__( 'with', 'radio-station').' </span>';
					foreach ( $show_names as $name ) {
						$count++;
						$user_info = get_userdata( $name );
						$output .= $user_info->display_name;

						$names_count = count( $show_names );
						if ( ( 1 === $count && 2 === $names_count ) || ( $names_count > 2 && $count === $names_count - 1 ) ) {
							$output .= ' '.__( 'and', 'radio-station' ).' ';
						} elseif ( $count < $names_count && $names_count > 2 ) {
							$output .= ', ';
						}
					}
				}

				$output .= '</span> ';
			}

			if ( $atts['display_show_time'] ) {

				$output .= '<span class="show-time">';

				if ( 12 === (int) $timeformat ) {

					// 2.2.7: added meridiem translation
					$starttime = strtotime( '1981-04-28 ' . $show['time']['start_hour'] . ':' . $show['time']['start_min'] . ':00 ' );
					$endtime = strtotime( '1981-04-28 ' . $show['time']['end_hour'] . ':' . $show['time']['end_min'] . ':00 ' );
					$output .= date( 'g:i', $starttime ) . ' ' . radio_station_translate_meridiem( date( 'a', $starttime ) );
					$output .= ' - ';
					$output .= date( 'g:i', $endtime ) . ' ' . radio_station_translate_meridiem( date( 'a', $endtime ) );

				} else {

					$output .= date( 'H:i', strtotime( '1981-04-28 ' . $show['time']['start_hour'] . ':' . $show['time']['start_min'] . ':00 ' ) );
					$output .= ' - ';
					$output .= date( 'H:i', strtotime( '1981-04-28 ' . $show['time']['end_hour'] . ':' . $show['time']['end_min'] . ':00 ' ) );

				}

				$output .= '</span>';
			}

			if ( isset( $show['time']['encore'] ) && 'on' === $show['time']['encore'] ) {
				$output .= ' <span class="show-encore">' . __( 'encore airing', 'radio-station' ) . '</span>';
			}

			$show_link = get_post_meta( $show['id'], 'show_file', true );
			if ( $show_link && ! empty( $show_link ) ) {
				$output .= ' <span class="show-file"><a href="' . $show_link . '">' . __( 'Audio File', 'radio-station' ) . '</a>';
			}

			$output .= '</li>';
		}
	}
	$output .= '</ul>';
	$output .= '</li>';
}

$output .= '</ul>';
