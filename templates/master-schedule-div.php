<?php
/**
 * Template for master schedule shortcode div style.
 */

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
$output  .= radio_station_master_fetch_js_filter();
$output  .= '<div id="master-schedule-divs">';
$weekdays = array_keys( $days_of_the_week );

$output .= '<div class="master-schedule-hour">';
$output .= '<div class="master-schedule-hour-header">&nbsp;</div>';
foreach ( $weekdays as $weekday ) {
	$translated = radio_station_translate_weekday( $weekday );
	$output    .= '<div class="master-schedule-weekday-header master-schedule-weekday">' . $translated . '</div>';
}
		$output .= '</div>';

foreach ( $master_list as $hour => $days ) {

	$output .= '<div class="master-schedule-' . $hour . ' master-schedule-hour">';

	// output the hour labels
	$output .= '<div class="master-schedule-hour-header">';
	if ( 12 === (int) $timeformat ) {
		$output .= date( 'ga', strtotime( '1981-04-28 ' . $hour . ':00:00' ) ); //random date needed to convert time to 12-hour format
	} else {
		$output .= date( 'H:i', strtotime( '1981-04-28 ' . $hour . ':00:00' ) ); //random date needed to convert time to 24-hour format
	}
	$output .= '</div>';

	foreach ( $weekdays as $weekday ) {
		$output .= '<div class="master-schedule-' . strtolower( $weekday ) . ' master-schedule-weekday" style="height: ' . $atts['divheight'] . 'px;">';
		if ( isset( $days[ $weekday ] ) ) {
			foreach ( $days[ $weekday ] as $min => $showdata ) {

				$terms   = wp_get_post_terms( $showdata['id'], 'genres', array() );
				$classes = ' show-id-' . $showdata['id'] . ' ' . sanitize_title_with_dashes( str_replace( '_', '-', get_the_title( $showdata['id'] ) ) ) . ' ';
				foreach ( $terms as $show_term ) {
					$classes .= sanitize_title_with_dashes( $show_term->name ) . ' ';
				}

				$output .= '<div class="master-show-entry' . $classes . '">';

				// featured image
				if ( $atts['show_image'] ) {
					$output .= '<span class="show-image">';
					if ( has_post_thumbnail( $showdata['id'] ) ) {
						$output .= get_the_post_thumbnail( $showdata['id'], 'thumbnail' );
					}
					$output .= '</span>';
				}

				// title + link to page if requested
				$output .= '<span class="show-title">';
				if ( $atts['show_link'] ) {
					$output .= '<a href="' . get_permalink( $showdata['id'] ) . '">' . get_the_title( $showdata['id'] ) . '</a>';
				} else {
					$output .= get_the_title( $showdata['id'] );
				}
				$output .= '</span>';

				// list of DJs
				if ( $atts['show_djs'] ) {

					$output .= '<span class="show-dj-names">';

					$show_names = get_post_meta( $showdata['id'], 'show_user_list', true );
					$count      = 0;

					if ( $show_names ) {

						$output .= '<span class="show-dj-names-leader"> with </span>';

						foreach ( $show_names as $name ) {

							$count++;
							$user_info = get_userdata( $name );

							$output .= $user_info->display_name;

							$names_count = count( $show_names );
							if ( ( 1 === $count && 2 === $names_count ) || ( $names_count > 2 && $count === $names_count - 1 ) ) {
								$output .= ' and ';
							} elseif ( $count < $names_count && $names_count > 2 ) {
								$output .= ', ';
							}
						}
					}

					$output .= '</span>';
				}

				// show's schedule
				if ( $atts['display_show_time'] ) {

					$output .= '<span class="show-time">';

					if ( 12 === (int) $timeformat ) {
						// $output .= $weekday.' ';
						$output .= date( 'g:i a', strtotime( '1981-04-28 ' . $showdata['time']['start_hour'] . ':' . $showdata['time']['start_min'] . ':00 ' ) );
						$output .= ' - ';
						$output .= date( 'g:i a', strtotime( '1981-04-28 ' . $showdata['time']['end_hour'] . ':' . $showdata['time']['end_min'] . ':00 ' ) );
					} else {
						$output .= date( 'H:i', strtotime( '1981-04-28 ' . $showdata['time']['start_hour'] . ':' . $showdata['time']['start_min'] . ':00 ' ) );
						$output .= ' - ';
						$output .= date( 'H:i', strtotime( '1981-04-28 ' . $showdata['time']['end_hour'] . ':' . $showdata['time']['end_min'] . ':00 ' ) );
					}
					$output .= '</span>';
				}

				// designate as encore
				if ( isset( $showdata['time']['encore'] ) && 'on' === $showdata['time']['encore'] ) {
					$output .= '<span class="show-encore">' . __( 'encore airing', 'radio-station' ) . '</span>';
				}

				// link to media file
				$show_link = get_post_meta( $showdata['id'], 'show_file', true );
				if ( $show_link && ! empty( $show_link ) ) {
					$output .= '<span class="show-file"><a href="' . $show_link . '">' . __( 'Audio File', 'radio-station' ) . '</a>';
				}

				// calculate duration of show for rowspanning
				if ( isset( $showdata['time']['rollover'] ) ) { //show started on the previous day
					$duration = $showdata['time']['end_hour'];
				} else {
					if ( $showdata['time']['end_hour'] >= $showdata['time']['start_hour'] ) {
						$duration = $showdata['time']['end_hour'] - $showdata['time']['start_hour'];
					} else {
						$duration = 23 - $showdata['time']['start_hour'];
					}
				}

				if ( $duration >= 1 ) {
					$output .= '<div class="rowspan rowspan' . $duration . '"></div>';

					if ( '00' !== $showdata['time']['end_min'] ) {
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
