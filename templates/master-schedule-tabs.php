<?php
/**
 * Template for master schedule shortcode tabs style.
 ref: http://nlb-creations.com/2014/06/06/radio-station-tutorial-creating-a-tabbed-programming-schedule/
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

$output .= '<ul id="master-schedule-tabs">';

$panels = '';
foreach ( $flip as $day => $hours ) {

	// 2.2.2: use translate function for weekday string
	$display_day = radio_station_translate_weekday( $day );
	$output .= '<li class="master-schedule-tabs-day" id="master-schedule-tabs-header-' . strtolower( $day ) . '">';
	$output .= '<div class="master-schedule-tabs-day-name">' . $display_day . '</div>';
	$output .= '</li>';

	// 2.2.7: separate headings from panels for tab view
	$panels .= '<ul class="master-schedule-tabs-panel" id="master-schedule-tabs-day-' . strtolower( $day ) . '">';

	$foundshows = false;
	foreach ( $hours as $hour => $mins ) {

		foreach ( $mins as $min => $show ) {

			$foundshows = true;
			$panels .= '<li class="master-schedule-tabs-show">';

				// --- Show Image ---
				// (defaults to display on)
				if ( $atts['show_image'] !== 'false' ) {
					$panels .= '<div class="show-image">';
					if ( has_post_thumbnail( $show['id'] ) ) {
						$panels .= get_the_post_thumbnail( $show['id'], 'thumbnail' );
					}
					$panels .= '</div>';
				}

				// --- Show Information ---
				$panels .= '<div class="show-info">';

					$panels .= '<div class="show-title">';
					if ( $atts['show_link'] ) {
						$panels .= '<a href="' . get_permalink( $show['id'] ) . '">' . get_the_title( $show['id'] ) . '</a>';
					} else {
						$panels .= get_the_title( $show['id'] );
					}
					$panels .= '</div>';

					if ( $atts['show_djs'] ) {
						$panels .= '<div class="show-dj-names">';

						$show_names = get_post_meta( $show['id'], 'show_user_list', true );
						$count = 0;

						if ( $show_names ) {
							$panels .= '<span class="show-dj-names-leader"> '.__( 'with', 'radio-station').' </span>';
							foreach ( $show_names as $name ) {
								$count++;
								$user_info = get_userdata( $name );
								$panels .= $user_info->display_name;

								$names_count = count( $show_names );
								if ( ( 1 === $count && 2 === $names_count ) || ( $names_count > 2 && $count === $names_count - 1 ) ) {
									$panels .= ' '.__( 'and', 'radio-station' ).' ';
								} elseif ( $count < $names_count && $names_count > 2 ) {
									$panels .= ', ';
								}
							}
						}

						$panels .= '</div>';
					}

					if ( $atts['display_show_time'] ) {

						$panels .= '<div class="show-time">';

						if ( 12 === (int) $timeformat ) {

							// 2.2.7: added meridiem translation
							$starttime = strtotime( '1981-04-28 ' . $show['time']['start_hour'] . ':' . $show['time']['start_min'] . ':00 ' );
							$endtime = strtotime( '1981-04-28 ' . $show['time']['end_hour'] . ':' . $show['time']['end_min'] . ':00 ' );
							$panels .= date( 'g:i', $starttime ) . ' ' . radio_station_translate_meridiem( date( 'a', $starttime ) );
							$panels .= ' - ';
							$panels .= date( 'g:i', $endtime ) . ' ' . radio_station_translate_meridiem( date( 'a', $endtime ) );

						} else {

							$panels .= date( 'H:i', strtotime( '1981-04-28 ' . $show['time']['start_hour'] . ':' . $show['time']['start_min'] . ':00 ' ) );
							$panels .= ' - ';
							$panels .= date( 'H:i', strtotime( '1981-04-28 ' . $show['time']['end_hour'] . ':' . $show['time']['end_min'] . ':00 ' ) );

						}

						$panels .= '</div>';
					}

					if ( isset( $show['time']['encore'] ) && 'on' === $show['time']['encore'] ) {
						$panels .= ' <div class="show-encore">' . __( 'encore airing', 'radio-station' ) . '</div>';
					}

					$show_link = get_post_meta( $show['id'], 'show_file', true );
					if ( $show_link && ! empty( $show_link ) ) {
						$panels .= ' <div class="show-file"><a href="' . $show_link . '">' . __( 'Audio File', 'radio-station' ) . '</a></div>';
					}

				$panels .= '</div>';

				// --- Show Genres list ---
				// (defaults to display on)
				if ( $atts['show_genres'] !== 'false' ) {
					$panels .= '<div class="show-genres">';
						$terms = wp_get_post_terms( $show['id'], 'genres', array() );
						$genres = array();
						if ( count( $terms ) > 0 ) {
							foreach ( $terms as $term ) {$genres[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';}
							$genredisplay = implode( ', ', $genres );
							$panels .= __( 'Genres', 'radio-station' ) . ': ' . $genredisplay;
						}
					$panels .= '</div>';
				}

			$panels .= '</li>';
		}
	}

	if (!$foundshows) {
		$panels .= '<li class="master-schedule-tabs-show">';
			$panels .= __('No Shows found for this day.','radio-station');
		$panels .= '</li>';
	}

	$panels .= '</ul>';
}

$output .= '</ul>'.$panels;
