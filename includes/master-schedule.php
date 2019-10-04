<?php
/*
 * Master Show schedule
 * Author: Nikki Blight
 * @Since: 2.1.1
 */

// --- shortcode to display a full schedule of DJs and shows ---
function radio_station_master_schedule( $atts ) {
	global $wpdb;

	$atts = shortcode_atts(
		array(
			'time'              => '12',
			'show_link'         => 1,
			'display_show_time' => 1,
			'list'              => 'table',
			'show_image'        => 0,
			'show_djs'          => 0,
			'show_genres'		=> 0,
			'divheight'         => 45,
		),
		$atts,
		'master-schedule'
	);

	$timeformat = $atts['time'];

	// $overrides = radio_station_master_get_overrides(true);

	// set up the structure of the master schedule
	$default_dj = get_option( 'dj_default_name' );

	// check to see what day of the week we need to start on
	$start_of_week    = get_option( 'start_of_week' );
	$days_of_the_week = array(
		'Sunday'    => array(),
		'Monday'    => array(),
		'Tuesday'   => array(),
		'Wednesday' => array(),
		'Thursday'  => array(),
		'Friday'    => array(),
		'Saturday'  => array(),
	);
	$week_start       = array_slice( $days_of_the_week, $start_of_week );

	foreach ( $days_of_the_week as $i => $weekday ) {
		if ( $start_of_week > 0 ) {
			$add = $days_of_the_week[ $i ];
			unset( $days_of_the_week[ $i ] );
			$days_of_the_week[ $i ] = $add;
		}
		$start_of_week--;
	}

	// create the master_list array based on the start of the week
	$master_list = array();
	for ( $i = 0; $i < 24;  $i++ ) {
		$master_list[ $i ] = $days_of_the_week;
	}

	// get the show schedules, excluding shows marked as inactive
	$show_shifts = $wpdb->get_results(
		"SELECT meta.post_id, meta.meta_value
		FROM {$wpdb->postmeta} AS meta
		JOIN {$wpdb->postmeta} AS active
			ON meta.post_id = active.post_id
		JOIN {$wpdb->posts} as posts
			ON posts.ID = meta.post_id
		WHERE meta.meta_key = 'show_sched' AND
			posts.post_status = 'publish' AND
			(
				active.meta_key = 'show_active' AND
				active.meta_value = 'on'
			)"
	);

	// insert schedules into the master list
	foreach ( $show_shifts as $shift ) {
		$shift->meta_value = maybe_unserialize( $shift->meta_value );

		// if a show is not scheduled yet, unserialize will return false... fix that.
		if ( ! is_array( $shift->meta_value ) ) {
			$shift->meta_value = array();}

		foreach ( $shift->meta_value as $time ) {

			// switch to 24-hour time
			if ( 'pm' === $time['start_meridian'] && 12 !== (int) $time['start_hour'] ) {
				$time['start_hour'] += 12;
			}
			if ( 'am' === $time['start_meridian'] && 12 === (int) $time['start_hour'] ) {
				$time['start_hour'] = 0;
			}

			if ( 'pm' === $time['end_meridian'] && 12 !== (int) $time['end_hour'] ) {
				$time['end_hour'] += 12;
			}
			if ( 'am' === $time['end_meridian'] && 12 === (int) $time['end_hour'] ) {
				$time['end_hour'] = 0;
			}

			// check if we're spanning multiple days
			$time['multi-day'] = 0;
			if ( $time['start_hour'] > $time['end_hour'] || $time['start_hour'] === $time['end_hour'] ) {
				$time['multi-day'] = 1;
			}

			$master_list[ $time['start_hour'] ][ $time['day'] ][ $time['start_min'] ] = array(
				'id'   => $shift->post_id,
				'time' => $time,
			);
		}
	}

	// sort the array by time
	foreach ( $master_list as $hour => $days ) {
		foreach ( $days as $day => $min ) {
			ksort( $min );
			$master_list[ $hour ][ $day ] = $min;

			// we need to take into account shows that start late at night and end the following day
			foreach ( $min as $i => $time ) {

				// if it ends at midnight, we don't need to worry about carry-over
				if ( 0 === (int) $time['time']['end_hour'] && 0 === (int) $time['time']['end_min'] ) {
					continue;
				}

				// if it ends after midnight, fix it
				// if it starts at night and ends in the morning, end hour is on the following day
				if ( ( 'pm' === $time['time']['start_meridian'] && 'am' === $time['time']['end_meridian'] ) ||
					// if the start and end times are identical, assume the end time is the following day
					( $time['time']['start_hour'] . $time['time']['start_min'] . $time['time']['start_meridian'] === $time['time']['end_hour'] . $time['time']['end_min'] . $time['time']['end_meridian'] ) ||
					// if the start hour is in the morning, and greater than the end hour, assume end hour is the following day
						( 'am' === $time['time']['start_meridian'] && $time['time']['start_hour'] > $time['time']['end_hour'] )
					) {

					if ( 12 === (int) $timeformat ) {
						$time['time']['real_start'] = ( $time['time']['start_hour'] - 12 ) . ':' . $time['time']['start_min'];
					} else {
						$pad_hour = '';
						if ( $time['time']['start_hour'] < 10 ) {
							$pad_hour = '0';
						}
						$time['time']['real_start'] = $pad_hour . $time['time']['start_hour'] . ':' . $time['time']['start_min'];
					}
					// $time['time']['start_hour'] = "0";
					// $time['time']['start_min'] = "00";
					// $time['time']['start_meridian'] = "am";
					$time['time']['rollover'] = 1;

					$nextday = '';
					switch ( $day ) {
						case 'Sunday':
							$nextday = 'Monday';
							break;
						case 'Monday':
							$nextday = 'Tuesday';
							break;
						case 'Tuesday':
							$nextday = 'Wednesday';
							break;
						case 'Wednesday':
							$nextday = 'Thursday';
							break;
						case 'Thursday':
							$nextday = 'Friday';
							break;
						case 'Friday':
							$nextday = 'Saturday';
							break;
						case 'Saturday':
							$nextday = 'Sunday';
							break;
					}

					$master_list[0][ $nextday ]['00'] = $time;

				}
			}
		}
	}

	$output = '';

	// 2.2.7: added tabbed master schedule template
	if ( 1 === (int) $atts['list'] || 'list' === $atts['list'] ) {
		require( RADIO_STATION_DIR . '/templates/master-schedule-list.php' );
	} elseif ( 'divs' === $atts['list'] ) {
		require( RADIO_STATION_DIR . '/templates/master-schedule-div.php' );
	} elseif ( 'tabs' === $atts['list'] ) {
		require( RADIO_STATION_DIR . '/templates/master-schedule-tabs.php' );
		// 2.2.7: add tab switching javascript to footer
		add_action( 'wp_footer', 'radio_station_master_schedule_tabs_js' );
	} else {
		require( RADIO_STATION_DIR . '/templates/master-schedule-default.php' );
	}

	return $output;

}
add_shortcode( 'master-schedule', 'radio_station_master_schedule' );

// --- add javascript for highlighting shows based on genre ---
function radio_station_master_fetch_js_filter() {

	$js = '<div id="master-genre-list"><span class="heading">' . __( 'Genres', 'radio-station' ) . ': </span>';

	$taxes = get_terms(
		'genres',
		array(
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
	foreach ( $taxes as $i => $tax ) {
		$js .= '<a href="javascript:show_highlight(\'' . sanitize_title_with_dashes( $tax->name ) . '\')">' . $tax->name . '</a>';
		// 2.2.2: fix to not add pipe suffix for last genre
		if ( count( $taxes ) - 1 !== $i ) {
			$js .= ' | ';
		}
	}

	$js .= '</div>';

	$js .= '<script type="text/javascript">';
	$js .= 'function show_highlight(myclass) {';
	$js .= '	jQuery(".master-show-entry").css("border","none");';
	$js .= '	jQuery("." + myclass).css("border","3px solid red");';
	$js .= '}';
	$js .= '</script>';

	return $js;
}

// --- javascript for tab switching ---
// 2.2.7: added for tabbed display type
function radio_station_master_schedule_tabs_js() {
	echo '<script>var masterschedule = jQuery.noConflict();
	masterschedule(document).ready(function() {
		dayweek = new Date().getDay();
		if (dayweek == "0") {day = "sunday";}
		if (dayweek == "1") {day = "monday";}
		if (dayweek == "2") {day = "tuesday";}
		if (dayweek == "3") {day = "wednesday";}
		if (dayweek == "4") {day = "thursday";}
		if (dayweek == "5") {day = "friday";}
		if (dayweek == "6") {day = "saturday";}
		masterschedule("#master-schedule-tabs-header-"+day).addClass("active-day-tab");
		masterschedule("#master-schedule-tabs-day-"+day).addClass("active-day-panel");
		masterschedule(".master-schedule-tabs-day").bind("click", function (event) {
			headerID = masterschedule(event.target).closest("li").attr("id");
			panelID = headerID.replace("header", "day");
			masterschedule(".master-schedule-tabs-day").removeClass("active-day-tab");
			masterschedule("#"+headerID).addClass("active-day-tab");
			masterschedule(".master-schedule-tabs-panel").removeClass("active-day-panel");
			masterschedule("#"+panelID).addClass("active-day-panel");
		});
	});</script>';
}
