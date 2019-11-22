<?php
/*
 * Master Show schedule
 * Author: Nikki Blight
 * @Since: 2.1.1
 */

// -------------------------
// Master Schedule Shortcode
// -------------------------
add_shortcode( 'master-schedule', 'radio_station_master_schedule' );
function radio_station_master_schedule( $atts ) {
	global $wpdb;

	// 2.3.0: added show_hosts (alias of show_djs)
	// 2.3.0: added show_file attribute (default off)
	// 2.3.0: added show_encore attribute (default on)
	// 2.3.0: added clock attribute (default on)
	// 2.3.0: set default time format according to plugin setting
	// 2.3.0: set default table display to new table formatting
	$time_format = (int) radio_station_get_setting( 'clock_time_format' );
	$defaults = array(
		'time'              => $time_format,
		'show_link'         => 1,
		'display_show_time' => 1,
		'view'              => 'table',
		'list'				=> 0,
		'clock'				=> 1,
		'show_image'        => 0,
		'show_djs'          => 0,
		'show_hosts'		=> 0,
		'show_genres'		=> 0,
		'show_file'			=> 0,
		'show_encore'		=> 1,
		'divheight'         => 45,
	);
	$atts = shortcode_atts( $defaults, $atts, 'master-schedule' );

	// --- check for old attributes ---
	// 2.3.0: make view attribute backwards compatible
	if ( $atts['list'] ) {
		if ( (int) $atts['list'] === 1 ) {$atts['list'] = 'list';}
		$atts['view'] = $atts['list'];
	}
	
	// --- enqueue schedule stylesheet ---
	// 2.3.0: use abstracted method for enqueueing widget styles
	radio_station_enqueue_style( 'schedule');
	
	// --- load master schedule template ---
	// 2.2.7: added tabbed master schedule template
	// 2.3.0: use new data model for table and tabs view
	// TODO: check for user theme templates
	$output = '';
	if ( $atts['view'] == 'table' ) {
		require( RADIO_STATION_DIR . '/templates/master-schedule-table.php' );
		return $output;
	} elseif ( $atts['view'] == 'tabs' ) {
		// 2.2.7: add tab switching javascript to footer
		add_action( 'wp_footer', 'radio_station_master_schedule_tabs_js' );
		require( RADIO_STATION_DIR . '/templates/master-schedule-tabs.php' );
		return $output;
	} elseif ( $atts['view'] == 'list' ) {
		require( RADIO_STATION_DIR . '/templates/master-schedule-list.php' );
		return $output;
	}
	
	// Legacy Master Schedule
	// ----------------------
	
	// 2.3.0: remove usused default DJ name option
	// $default_dj = get_option( 'dj_default_name' );

	// --- check to see what day of the week we need to start on ---
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
	$week_start = array_slice( $days_of_the_week, $start_of_week );
	foreach ( $days_of_the_week as $i => $weekday ) {
		if ( $start_of_week > 0 ) {
			$add = $days_of_the_week[$i];
			unset( $days_of_the_week[$i] );
			$days_of_the_week[$i] = $add;
		}
		$start_of_week--;
	}

	// --- create the master_list array based on the start of the week ---
	$master_list = array();
	for ( $i = 0; $i < 24;  $i++ ) {
		$master_list[ $i ] = $days_of_the_week;
	}

	// --- get the show schedules, excluding shows marked as inactive ---
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

	// --- insert scheduled shifts into the master list ---
	foreach ( $show_shifts as $shift ) {
		$shift->meta_value = maybe_unserialize( $shift->meta_value );

		// if a show is not scheduled yet, unserialize will return false... fix that.
		if ( ! is_array( $shift->meta_value ) ) {
			$shift->meta_value = array();
		}

		foreach ( $shift->meta_value as $time ) {

			// 2.3.0: added check for show disabled switch
			if ( !isset( $time['disabled'] ) || ( $time['disabled'] == 'yes' ) ) {
			
				// --- switch to 24-hour time ---
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

				// --- check if we are spanning multiple days ---
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
	}

	// --- sort the array by time ---
	foreach ( $master_list as $hour => $days ) {
		foreach ( $days as $day => $min ) {
			ksort( $min );
			$master_list[$hour][$day] = $min;

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

					if ( 12 === (int) $atts['time'] ) {
						$time['time']['real_start'] = ( $time['time']['start_hour'] - 12 ) . ':' . $time['time']['start_min'];
					} else {
						$pad_hour = '';
						if ( $time['time']['start_hour'] < 10 ) {
							$pad_hour = '0';
						}
						$time['time']['real_start'] = $pad_hour . $time['time']['start_hour'] . ':' . $time['time']['start_min'];
					}
					$time['time']['rollover'] = 1;

					// 2.3.0: use new get next day function
					$nextday = radio_station_get_next_day( $day );

					$master_list[0][ $nextday ]['00'] = $time;

				}
			}
		}
	}

	// --- check for schedule overrides ---
	// ? TODO ? check overrides for legacy views
	// $overrides = radio_station_master_get_overrides( true );

	// --- include the specified master schedule output template ---
	// TODO: check for user theme templates ?
	if ( $atts['view'] == 'divs' ) {
		require( RADIO_STATION_DIR . '/templates/master-schedule-div.php' );
	} elseif ( $atts['view'] == 'legacy' ) {
		require( RADIO_STATION_DIR . '/templates/master-schedule-legacy.php' );
	}

	return $output;
}

// ----------------------------------
// Show  / Genre Selection Javascript
// ----------------------------------
function radio_station_master_schedule_selection_js() {

	// --- open genre highlighter div ---
	$js = '<div id="master-genre-list">';
		$js .= '<span class="heading">' . __( 'Genres', 'radio-station' ) . ': </span>';

		// --- get genres ---
		$args =  array(	'hide_empty' => true, 'orderby'    => 'name', 'order'      => 'ASC' );
		$genres = get_terms( 'genres', $args );

		// --- genre highlight links ---
		// 2.3.0: fix by imploding with genre link spacer
		$genre_links = array();
		foreach ( $genres as $i => $genre) {
			$slug = sanitize_title_with_dashes( $genre->name );
			$javascript = 'javascript:show_highlight(\'' . $slug . '\')';
			$title = __( 'Click to toggle Highlight of Shows with this Genre.', 'radio-station' );
			$genre_link = '<a id="genre-highlight-' . esc_attr( $slug ) . '" class="genre-highlight" href="' . $javascript . '" title="' . esc_attr( $title ) . '">';
			$genre_link .= $genre->name . '</a>';
			$genre_links[] = $genre_link;
		}
		$js .= implode( ' | ', $genre_links );

	$js .= '</div>';

	// --- genre highlighter script ---
	// 2.3.0: improved to highlight / unhighlight multiple genres
	$js .= "<script>var highlighted_genres = new Array();
	function show_highlight(genre) {
		if (jQuery('#genre-highlight-'+genre).hasClass('highlighted')) {
			jQuery('#genre-highlight-'+genre).removeClass('highlighted');

			jQuery('.master-show-entry').each(function() {
				jQuery(this).removeClass('highlighted');
			});

			j = 0; new_genre_highlights = new Array();
			for (i = 0; i < highlighted_genres.length; i++) {
				if (highlighted_genres[i] != genre) {
					jQuery('.'+genre).addClass('highlighted');
					new_genre_highlights[j] = genre; j++;
				}
			}
			highlighted_genres = new_genre_highlights;

		} else {
			jQuery('#genre-highlight-'+genre).addClass('highlighted');
			highlighted_genres[highlighted_genres.length] = genre;
			jQuery('.'+genre).each(function () {
				jQuery(this).addClass('highlighted');
			});
		}	
	}</script>";
	
	return $js;
}

// ------------------------
// Tab Switching Javascript
// ------------------------
// 2.2.7: added for tabbed schedule view
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
	});	
	</script>';
}
