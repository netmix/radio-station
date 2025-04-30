<?php

if ( !defined( 'ABSPATH' ) ) exit;

/*
 * Master Show schedule
 * Author: Nikki Blight
 * @Since: 2.1.1
 */

// - Master Schedule Shortcode
// - Schedule Loader Script
// - AJAX Schedule Loader
// - Show Genre Selector
// - Table View Javascript
// - Tabbed View Javascript
// - List View Javascript


// -------------------------
// Master Schedule Shortcode
// -------------------------
// 2.5.0: added optional radio-schedule shortcode alias
add_shortcode( 'radio-schedule', 'radio_station_master_schedule' );
add_shortcode( 'master-schedule', 'radio_station_master_schedule' );
function radio_station_master_schedule( $atts ) {

	global $radio_station_data;

	// 2.5.0: maybe set schedule instances array
	if ( !isset( $radio_station_data['schedules'] ) ) {
		$radio_station_data['schedules'] = array();
	}
	if ( !isset( $radio_station_data['schedules']['instances'] ) ) {
		$radio_station_data['schedules']['instances'] = -1;
	}
	$radio_station_data['schedules']['instances']++;
	$instances = $radio_station_data['schedules']['instances'];

	// 2.5.10.1: allow specifying instance value for AJAX loading
	if ( isset( $atts['instance'] ) ) {
		$instance = $atts['instance'];
		unset( $atts['instance'] );
	}

	// --- make attributes backward compatible ---
	// 2.3.0: convert old list attribute to view
	if ( !isset( $atts['view'] ) && isset( $atts['list'] ) ) {
		if ( 1 === (int) $atts['list'] ) {
			$atts['list'] = 'list';
		}
		$atts['view'] = $atts['list'];
		unset( $atts['list'] );
	}
	// 2.3.0: convert show_djs attribute to show_hosts
	if ( !isset( $atts['show_hosts'] ) && isset( $atts['show_djs'] ) ) {
		$atts['show_hosts'] = $atts['show_djs'];
		unset( $atts['show_djs'] );
	}
	// 2.3.0: convert display_show_time attribute to show_times
	if ( !isset( $atts['show_times'] ) && isset( $atts['display_show_time'] ) ) {
		$atts['show_times'] = $atts['display_show_time'];
		unset( $atts['display_show_time'] );
	}
	// 2.3.0: convert single_day attribute to days
	if ( !isset( $atts['days'] ) && isset( $atts['single_day'] ) ) {
		$atts['days'] = $atts['single_day'];
		unset( $atts['single_day'] );
	}
	// 2.5.0: convert time attribute to time_format
	if ( isset( $atts['time'] ) ) {
		$atts['time_format'] = $atts['time'];
		unset( $atts['time'] );
	}

	// 2.5.0: convert widget option time_header to clock/timezone atts
	if ( isset( $atts['time_header'] ) ) {
		if ( 'clock' == $atts['time_header'] ) {
			$atts['clock'] = 1;
			$atts['timezone'] = 0;
		} elseif ( 'timezone' == $atts['time_header'] ) {
			$atts['clock'] = 0;
			$atts['timezone'] = 1;
		} elseif ( 'none' == $atts['time_header'] ) {
			$atts['clock'] = 0;
			$atts['timezone'] = 0;
		}
	}

	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Master Schedule Shortcode Attributes: ' . esc_html( print_r( $atts, true ) ) . '</span>';
	}

	// 2.5.10.1: get default AJAX loading setting
	$ajax = ( 'yes' == radio_station_get_setting( 'schedule_ajax' ) ) ? 1 : 0;

	// --- get default clock display settings ---
	$clock = radio_station_get_setting( 'schedule_clock' );
	$time_format = (int) radio_station_get_setting( 'clock_time_format' );

	// --- merge shortcode attributes with defaults ---
	// 2.3.0: added show_desc (default off)
	// 2.3.0: added show_hosts (alias of show_djs)
	// 2.3.0: added show_file attribute (default off)
	// 2.3.0: added show_encore attribute (default on)
	// 2.3.0: added display clock attribute (default on)
	// 2.3.0: added display selector attribute (default on)
	// 2.3.0: added link_hosts attribute (default off)
	// 2.3.0: set default time format according to plugin setting
	// 2.3.0: set default table display to new table formatting
	// 2.3.2: added start_day attribute (for use width days)
	// 2.3.2: added display_day, display_date and display_month attributes
	// 2.3.3.9: added start_date attribute for non-now schedules
	// 2.3.3.9: added active_date attribute for schedule switching
	$defaults = array(
		// --- schedule view ---
		'view'              => 'table',

		// --- control display options ---
		'selector'          => 1,
		'clock'             => $clock,
		'timezone'          => 1,

		// --- schedule display options ---
		'days'              => false,
		'start_day'         => false,
		'start_date'        => false,
		'active_date'       => false,
		'display_day'       => 'short',
		'display_date'      => 'jS',
		'display_month'     => 'short',
		'time_format'       => $time_format,

		// --- show display options ---
		'show_link'         => 1,
		'show_times'        => 1,
		'show_image'        => 0,
		'show_desc'         => 0,
		'show_hosts'        => 0,
		'link_hosts'        => 0,
		'show_genres'       => 0,
		'show_encore'       => 1,
		'show_file'         => 0,

		// --- instance values ---
		// 2.5.10.1: add AJAX attribute value
		'ajax'				=> $ajax,
		'widget'            => 0,
		'block'             => 0,
		// 'instance'			=> $instance,

		// --- converted and deprecated ---
		// 'list'              => 0,
		// 'show_djs'          => 0,
		// 'display_show_time' => 1,
	);
	// 2.3.0: change some defaults for tabbed and list view
	// 2.3.2: check for comma separated view list
	$view = '';
	$views = array();
	if ( isset( $atts['view'] ) ) {

		// 2.3.2: view value to lowercase to be case insensitive
		$view = $atts['view'] = strtolower( $atts['view'] );
		if ( strstr( $atts['view'], ',' ) ) {
			$views = explode( ',', $atts['view'] );
			foreach ( $views as $i => $aview ) {
				if ( 'tabbed' == $aview ) {
					$aview = 'tabs';
				}
				$views[$i] = trim( $aview );
			}
			// 2.3.3.9: set default view for multiviews to table
			$defaults['default_view'] = 'table';
		} elseif ( 'tabbed' == $view ) {
			// 2.3.3.9: fix for possible misspelt tab view
			$view = 'tabs';
		}

		// 2.3.3.9: set prefixed defaults for multiple views
		if ( 'tabs' == $atts['view'] ) {
			// 2.3.2: add show descriptions default for tabbed view
			// 2.3.2: add display_day and display_date attributes
			// 2.3.3: revert show description default for tabbed view
			// 2.3.3.8: add default show image position (left aligned)
			// 2.3.3.8: add default for hide past shows (false)
			// 2.5.0: change date display default to true
			$defaults['show_image'] = 1;
			$defaults['show_hosts'] = 1;
			$defaults['show_genres'] = 1;
			$defaults['display_day'] = 'full';
			$defaults['display_date'] = true;
			$defaults['image_position'] = 'left';
			$defaults['hide_past_shows'] = false;
		} elseif ( in_array( 'tabs', $views ) ) {
			// note: these apply to tab view only
			$defaults['image_position'] = 'left';
			$defaults['hide_past_shows'] = false;
			// 2.5.0: remove default separate view settings for widgets
			if ( ( !isset( $atts['widget'] ) || !$atts['widget'] ) && ( !isset( $atts['block'] ) || !$atts['block'] ) ) {
				$defaults['tabs_show_image'] = 1;
				$defaults['tabs_show_hosts'] = 1;
				$defaults['tabs_show_genres'] = 1;
				$defaults['tabs_display_day'] = 'full';
				$defaults['tabs_display_date'] = true;
			}
		}
		if ( 'list' == $atts['view'] ) {
			// 2.3.2: add display date attribute
			$defaults['show_genres'] = 1;
			$defaults['display_date'] = false;
		} elseif ( in_array( 'list', $views ) ) {
			// 2.5.0: remove default separate view settings for widgets
			if ( ( !isset( $atts['widget'] ) || !$atts['widget'] ) && ( !isset( $atts['block'] ) || !$atts['block'] ) ) {
				$defaults['list_show_genres'] = 1;
				$defaults['list_display_date'] = false;
			}
		}
		if ( ( 'divs' == $atts['view'] ) || ( in_array( 'divs', $views ) ) ) {
			// 2.3.3.8: moved divs view only default here
			// 2.3.3.9: added check if divs in views array
			$defaults['divheight'] = 45;
		}
	}
	// 2.3.3.9: filter defaults according to view(s)
	$defaults = apply_filters( 'radio_station_master_schedule_default_atts', $defaults, $view, $views, $atts );

	// --- merge attributes with defaults ---
	$atts = shortcode_atts( $defaults, $atts, 'master-schedule' );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Master Schedule Shortcode Attributes: ' . esc_html( print_r( $atts, true ) ) . '</span>';
	}
	// 2.5.0: force empty start_day string to false
	if ( '' == $atts['start_day'] ) {
		$atts['start_day'] = false;
	}

	// --- enqueue schedule stylesheet ---
	// 2.3.0: use abstracted method for enqueueing widget styles
	radio_station_enqueue_style( 'schedule' );

	// --- set initial empty output string ---
	$output = '';

	// 2.3.3.9: remove check if clock shortcode present
	// 2.3.3.6: set new line for easier debug viewing
	// 2.5.0: just set new line for output readability
	// $newline = "\n";

	// --- table for selector and clock  ---
	// 2.3.0: moved out from templates to apply to all views
	// 2.3.2: moved shortcode calls inside and added filters
	// 2.5.0: added instances IDs and use class for selector
	$id = ( 0 == $instances ) ? '' : '-' . $instances;
	$output .= '<div id="master-schedule-controls-wrapper' . esc_attr( $id ) . '" class="master-schedule-controls-wrapper">' . "\n";

		$controls = array();

		// --- display radio clock or timezone (or neither) ---
		if ( $atts['clock'] ) {

			// --- radio clock ---
			$clock_atts = apply_filters( 'radio_station_schedule_clock', array(), $atts );
			$controls['clock'] = '<div id="master-schedule-clock-wrapper' . esc_attr( $id ) . '" class="master-schedule-clock-wrapper">' . "\n";
				$controls['clock'] .= radio_station_clock_shortcode( $clock_atts );
			$controls['clock'] .= "\n" . '</div>' . "\n";

		} elseif ( $atts['timezone'] ) {

			// --- radio timezone ---
			$timezone_atts = apply_filters( 'radio_station_schedule_clock', array(), $atts );
			$controls['timezone'] = '<div id="master-schedule-timezone-wrapper' . esc_attr( $id ) . '" class="master-schedule-timezone-wrapper">' . "\n";
				$controls['timezone'] .= radio_station_timezone_shortcode( $timezone_atts );
			$controls['timezone'] .= "\n" . '</div>' . "\n";

		}

		// --- genre selector ---
		if ( $atts['selector'] ) {
			$controls['selector'] = '<div id="master-schedule-selector-wrapper' . esc_attr( $id ) . '" class="master-schedule-selector-wrapper">' . "\n";
				$controls['selector'] .= radio_station_master_schedule_genre_selector( $instances );
			$controls['selector'] .= "\n" . '</div>' . "\n";
		}

		// 2.3.1: add filters for control order
		$control_order = array( 'clock', 'timezone', 'selector' );
		$control_order = apply_filters( 'radio_station_schedule_control_order', $control_order, $atts );

		// 2.3.1: add filter for controls HTML
		$controls = apply_filters( 'radio_station_schedule_controls', $controls, $atts );

		// --- add ordered controls to output ---
		if ( is_array( $control_order ) && ( count( $control_order ) > 0 ) ) {
			foreach ( $control_order as $control ) {
				if ( isset( $controls[$control] ) && ( '' != $control ) ) {
					$output .= $controls[$control];
				}
			}
		}

	$output .= '<br></div><br>' . "\n";
	$output = apply_filters( 'radio_station_schedule_controls_output', $output, $atts );

	// --- hidden inputs for calendar start/active dates ---
	// 2.3.3.9: added for schedule week change reloading
	if ( isset( $atts['start_date'] ) && $atts['start_date'] ) {
		// 2.5.6: use radio_station_get_time instead of date
		// 2.5.10.1: revert this change as causing incorrect invalidation
		if ( date( 'Y-m-d', strtotime( $atts['start_date'] ) ) == $atts['start_date'] ) {
			$start_date = $atts['start_date'];
		}
	}
	if ( !isset( $start_date ) ) {
		$now = radio_station_get_now();
		$start_date = radio_station_get_time( 'date', $now );
	}
	$output .= '<input type="hidden" id="schedule-start-date" value="' . esc_attr( $start_date ) . '">';
	$active_date = $start_date;
	if ( isset( $atts['active_date'] ) && $atts['active_date'] ) {
		// 2.5.6: use radio_station_get_time instead of date
		if ( $atts['active_date'] == radio_station_get_time( 'Y-m-d', strtotime( $atts['active_date'] ) ) ) {
			$active_date = $atts['active_date'];
		}
	}
	$output .= '<input type="hidden" id="schedule-active-date" value="' . esc_attr( $active_date ) . '">';
	// 2.3.3.9: also added schedule start day input
	$start_day = ( $atts['start_day'] ) ? $atts['start_day'] : '';
	$output .= '<input type="hidden" id="schedule-start-day" value="' . esc_attr( $start_day ) . '">';

	// --- enqueue schedule loader script ---
	$js = radio_station_master_schedule_loader_js( $atts );
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'radio-station', $js );

	// --- schedule display override ---
	// 2.3.1: add full schedule override filter
	// 2.3.3.9: add existing controls output to filter
	// 2.5.10.1: maybe merge instance ID back into atts value
	if ( isset( $instance ) ) {
		$atts = array_merge( $atts, array( 'instance' => $instance ) );
	}
	$override = apply_filters( 'radio_station_schedule_override', $output, $atts );
	if ( strstr( $override, '<!-- SCHEDULE OVERRIDE -->' ) ) {
		$override = str_replace( '<!-- SCHEDULE OVERRIDE -->', '', $override );
		// 2.5.0: added wrapper to shortcode output
		$output = '<div id="master-schedule' . esc_attr( $id ) . '" class="master-schedule">' . $override . '</div>' . "\n";
		return $output;
	}

	// -------------------------
	// New Master Schedule Views
	// -------------------------

	// --- load master schedule template ---
	// 2.2.7: added tabbed master schedule template
	// 2.2.7: add tabbed view javascript to footer
	// 2.3.0: use new data model for table and tabs view
	// 2.3.0: check for user theme templates
	// 2.3.3.9: use output buffering on templates
	// 2.3.3.9: get and enqueue scripts inline directly
	if ( 'table' == $atts['view'] ) {

		// 2.5.10.1: added check for specified instance
		if ( !isset( $instance ) ) {
			// 2.5.0: set view instance number
			if ( !isset( $radio_station_data['schedules']['table'] ) ) {
				$radio_station_data['schedules']['table'] = -1;
			}
			$radio_station_data['schedules']['table']++;
			$instance = $radio_station_data['schedules']['table'];
		}
		// 2.5.6: fix for missing id definition
		$id = ( 0 == $instance ) ? '' : '-' . $instance;

		// --- load table view template ---
		ob_start();
		$template = radio_station_get_template( 'file', 'master-schedule-table.php' );
		require $template;
		$html = ob_get_contents();
		ob_end_clean();

		// --- enqueue table view script ---
		$js = radio_station_master_schedule_table_js();
		// 2.5.0: use radio_station_add_inline_script
		radio_station_add_inline_script( 'radio-station', $js );

		// --- filter and return ---
		// 2.5.0: added prefixed filter for consistency
		$html = apply_filters( 'radio_station_master_schedule_table_view', $html, $atts );
		// note: keep backwards compatible non-prefixed filter
		$html = apply_filters( 'master_schedule_table_view', $html, $atts );
		// 2.5.0: added shortcode wrapper
		$output = '<div id="master-schedule' . esc_attr( $id ) . '" class="master-schedule">' . $output . $html . '</div>' . "\n";
		return $output;

	} elseif ( 'tabs' == $atts['view'] ) {

		// 2.5.10.1: added check for specified instance
		if ( !isset( $instance ) ) {
			// 2.5.0: set view instance number
			if ( !isset( $radio_station_data['schedules']['tabs'] ) ) {
				$radio_station_data['schedules']['tabs'] = -1;
			}
			$radio_station_data['schedules']['tabs']++;
			$instance = $radio_station_data['schedules']['tabs'];
		}
		// 2.5.6: fix for missing id definition
		$id = ( 0 == $instance ) ? '' : '-' . $instance;

		// --- load tabs view template ---
		ob_start();
		$template = radio_station_get_template( 'file', 'master-schedule-tabs.php' );
		require $template;
		$html = ob_get_contents();
		ob_end_clean();

		// --- enqueue tabs view script ---
		$js = radio_station_master_schedule_tabs_js();
		// 2.5.0: use radio_station_add_inline_script
		radio_station_add_inline_script( 'radio-station', $js );

		// --- filter and return ---
		// 2.5.0: added prefixed filter for consistency
		$html = apply_filters( 'radio_station_master_schedule_tabs_view', $html, $atts );
		// note: keep backwards compatible non-prefixed filter
		$html = apply_filters( 'master_schedule_tabs_view', $html, $atts );
		// 2.5.0: added shortcode wrapper
		$output = '<div id="master-schedule' . esc_attr( $id ) . '" class="master-schedule">' . $output . $html . '</div>' . "\n";
		return $output;

	} elseif ( 'list' == $atts['view'] ) {

		// 2.5.10.1: added check for specified instance
		if ( !isset( $instance ) ) {
			// 2.5.0: set view instance number
			if ( !isset( $radio_station_data['schedules']['list'] ) ) {
				$radio_station_data['schedules']['list'] = -1;
			}
			$radio_station_data['schedules']['list']++;
			$instance = $radio_station_data['schedules']['list'];
		}
		// 2.5.6: fix for missing id definition
		$id = ( 0 == $instance ) ? '' : '-' . $instance;

		// --- load list view template ---
		ob_start();
		$template = radio_station_get_template( 'file', 'master-schedule-list.php' );
		require $template;
		$html = ob_get_contents();
		ob_end_clean();

		// --- enqueue list view script ---
		$js = radio_station_master_schedule_list_js();
		// 2.5.0: use radio_station_add_inline_script
		radio_station_add_inline_script( 'radio-station', $js );

		// --- filter and return ---
		// 2.5.0: added prefixed filter for consistency
		$html = apply_filters( 'radio_station_master_schedule_list_view', $html, $atts );
		// note: keep backwards compatible non-prefixed filter
		$html = apply_filters( 'master_schedule_list_view', $html, $atts );
		// 2.5.0: added shortcode wrapper
		$output = '<div id="master-schedule' . esc_attr( $id ) . '" class="master-schedule">' . $output . $html . '</div>' . "\n";
		return $output;

	}

	// ----------------------
	// Legacy Master Schedule
	// ----------------------
	// note: Legacy and Divs Views do not include Schedule Overrides

	global $wpdb;

	// 2.3.0: remove unused default DJ name option
	// $default_dj = get_option( 'dj_default_name' );

	// --- check to see what day of the week we need to start on ---
	$start_of_week = get_option( 'start_of_week' );
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
	for ( $i = 0; $i < 24; $i++ ) {
		$master_list[$i] = $days_of_the_week;
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
		if ( !is_array( $shift->meta_value ) ) {
			$shift->meta_value = array();
		}

		foreach ( $shift->meta_value as $time ) {

			// 2.3.0: added check for show disabled switch
			if ( !isset( $time['disabled'] ) || ( 'yes' == $time['disabled'] ) ) {

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

				$master_list[$time['start_hour']][$time['day']][$time['start_min']] = array(
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

					$master_list[0][$nextday]['00'] = $time;

				}
			}
		}
	}

	// --- check for schedule overrides ---
	// ? TODO - check/include schedule overrides in legacy template views
	// $overrides = radio_station_master_get_overrides( true );

	// --- include the specified master schedule output template ---
	// 2.3.0: check for user theme templates
	if ( 'divs' == $atts['view'] ) {
		$output = ''; // no selector / clock support
		$template = radio_station_get_template( 'file', 'master-schedule-div.php' );
		require $template;
	} elseif ( 'legacy' == $atts['view'] ) {
		$template = radio_station_get_template( 'file', 'master-schedule-legacy.php' );
		require $template;
	}

	return $output;
}

// ----------------------
// Schedule Loader Script
// ----------------------
function radio_station_master_schedule_loader_js( $atts ) {

	// --- set AJAX URL with attribute keys  ---
	$loader_url = add_query_arg( 'action', 'radio_station_schedule', admin_url( 'admin-ajax.php' ) );

	// --- schedule loader function ---
	// 2.5.0: added instance ID argument
	// 2.5.10.1: reload multiviews to match direction change
	$js = "function radio_load_schedule(instance,direction,view,clear) {
		/* handle multi-views */
		if (direction && view) {
			if (view == 'table') {id = 'master-program-schedule';} else {id = 'master-schedule-'+view;}
			if (instance > 0) {id += '-'+instance;}
			container = jQuery('#'+id).closest('.master-schedule');
			if ((view != 'table') && container.find('.master-program-schedule').length) {
				i = container.find('.master-program-schedule').attr('id').replace('master-program-schedule','').replace('-','');
				setTimeout(function(i,direction) {radio_load_schedule(i,direction,'table',false);}, 800);
			}
			if ((view != 'tabs') && container.find('.master-schedule-tabs').length) {
				i = container.find('.master-schedule-tabs').attr('id').replace('master-schedule-tabs','').replace('-','');
				setTimeout(function(i,direction) {radio_load_schedule(i,direction,'tabs',false);}, 900);
			}
			if ((view != 'list') && container.find('.master-schedule-list').length) {
				i = container.find('.master-schedule-list').attr('id').replace('master-schedule-list','').replace('-','');
				setTimeout(function(i,direction) {radio_load_schedule(i,direction,'list',false);}, 1000);
			}
			if ((view != 'grid') && container.find('.master-schedule-grid').length) {
				i = container.find('.master-schedule-grid').attr('id').replace('master-schedule-grid','').replace('-','');
				setTimeout(function(i,direction) {radio_load_schedule(i,direction,'grid',false);}, 1100);
			}
			if ((view != 'Calendar') && container.find('.master-schedule-calendar').length) {
				i = container.find('.master-schedule-calendar').attr('id').replace('master-schedule-calendar','').replace('-','');
				setTimeout(function(i,direction) {radio_load_schedule(i,direction,'calendar',false);}, 1200);
			}
		}
		if (document.getElementById('schedule-loader-frame')) {
			view = radio_cookie.get('admin_schedule_view'); radio_load_view(view); return;
		}
		startday = document.getElementById('schedule-start-day').value;
		startdate = document.getElementById('schedule-start-date').value;
		activedate = document.getElementById('schedule-active-date').value;
		if (!view) {
			if ((0 == instance) || (false === instance)) {id = '';} else {id = '-'+instance;}
			if (jQuery('.master-schedule-view-tab.current-view').length) {
				view = jQuery('.master-schedule-view-tab.current-view').attr('data');
			} else {
				if (jQuery('#master-program-schedule'+id).length) {view = 'table';}
				else if (jQuery('#master-schedule-tabs'+id).length) {view = 'tabs';}
				else if (jQuery('#master-schedule-list'+id).length) {view = 'list';}
				else if (jQuery('#master-schedule-grid'+id).length) {view = 'grid';}
				else if (jQuery('#master-schedule-calendar'+id).length) {view = 'calendar';}
			}
		}
		if (radio.debug) {console.log('Reloading Schedule View: '+view);}
		if (!view) {return;}
		if (!direction) {offset = 0;}
		else if (direction == 'previous') {offset = -(7 * 24 * 60 * 60 * 1000);}
		else if (direction == 'next') {offset = (7 * 24 * 60 * 60 * 1000);}
		newdate = new Date(new Date(startdate).getTime() + offset).toISOString().substr(0,10);
		timestamp = Math.floor((new Date()).getTime() / 1000);
		url = '" . esc_url( $loader_url );
		// 2.6.0: add args directly to avoid double escaping
		$ignore_keys = array( 'start_date', 'view', 'ajax' );
		foreach ( $atts as $key => $value ) {
			if ( !in_array( $key, $ignore_keys ) ) {
				$js .= '&' . esc_js( $key ) . '=' . esc_js( $value );
			}
		}
		$js .= "&view='+view+'&instance='+instance+'&timestamp='+timestamp+'&start_date='+newdate+'&active_date='+activedate;
		if (startday != '') {url += '&start_day='+startday;}
		if (clear) {url += '&clear=1';}
		if (radio.debug) {url += '&rs-debug=1'; console.log('Reload View URL: '+url);}
		/* if (document.getElementById('schedule-'+view+'-loader').src != url) {
			document.getElementById('schedule-'+view+'-loader').src = url;
		} */
		iframe_id = 'master-schedule-'+view+'-'+instance+'-loader';
		iframe = document.getElementById(iframe_id);
		if (iframe) {
			if (iframe.src == url) {return;}
			iframe.src = url;
		} else {
			iframe = document.createElement('iframe');
			iframe.id = iframe_id; iframe.src = url; iframe.style.display = 'none';
			document.getElementsByTagName('head')[0].appendChild(iframe);
		}
		if (radio.debug) {console.log('Loading Schedule via Iframe '+iframe_id+' - URL: '+url);}
	}" . "\n";

	// 2.5.10.1: added multi-schedule reloader
	$js .= "function radio_load_schedules() {
		tables = jQuery('.master-program-schedule');
		tabs = jQuery('.master-schedule-tabs');
		lists = jQuery('.master-schedule-list');
		grids = jQuery('.master-schedule-grid');
		calendars = jQuery('.master-schedule-calendar');
		if (tables.length) {tables.each(function() {
			instance = jQuery(this).attr('id').replace('master-program-schedule-','');
			if (radio.debug) {console.log('Refreshing Table Instance '+instance);}
			radio_load_schedule(instance,false,'table',true);
		}); }
		if (tabs.length) {tabs.each(function() {
			instance = jQuery(this).attr('id').replace('master-schedule-tabs-','');
			if (radio.debug) {console.log('Refreshing Tabs Instance '+instance);}
			radio_load_schedule(instance,false,'tabs',true);
		}); }
		if (lists.length) {lists.each(function() {
			instance = jQuery(this).attr('id').replace('master-schedule-list-','');
			if (radio.debug) {console.log('Refreshing List Instance '+instance);}
			radio_load_schedule(instance,false,'list',true);
		}); }
		if (grids.length) {grids.each(function() {
			instance = jQuery(this).attr('id').replace('master-schedule-grid-','');
			if (radio.debug) {console.log('Refreshing Grid Instance '+instance);}
			radio_load_schedule(instance,false,'grid',true);
		}); }
		if (calendars.length) {calendars.each(function() {				
			instance = jQuery(this).attr('id').replace('master-schedule-calendar-','');
			if (radio.debug) {console.log('Refreshing Calendar Instance '+instance);
			radio_load_schedule(instance,false,'calendar',true);}
		}); }			
	}" . "\n";

	// 2.5.10.1: check for AJAX attribute to refresh schedule on pageload
	if ( isset( $atts['ajax'] ) && $atts['ajax'] ) {
		$js .= "jQuery(document).ready(function() {radio_load_schedules();}, 500);" . "\n";
	}
	
	// --- filter and return ---
	$js = apply_filters( 'radio_station_master_schedule_loader_js', $js );
	return $js;
}

// --------------------
// AJAX Schedule Loader
// --------------------
add_action( 'wp_ajax_radio_station_schedule', 'radio_station_ajax_schedule_loader' );
add_action( 'wp_ajax_nopriv_radio_station_schedule', 'radio_station_ajax_schedule_loader' );
function radio_station_ajax_schedule_loader() {

	// --- maybe clear cached data ---
	if ( isset( $_REQUEST['clear'] ) && ( '1' === sanitize_text_field( $_REQUEST['clear'] ) ) ) {
		radio_station_clear_cached_data( false );
	}

	// 2.5.0: get schedule instance ID
	$instance = absint( $_REQUEST['instance'] );
	
	$active_date = sanitize_text_field( $_REQUEST['active_date'] );
	$day = strtolower( date( 'l', strtotime( $active_date ) ) );

	// --- sanitize shortcode attributes ---
	$debug = true;
	$atts = radio_station_sanitize_shortcode_values( 'master-schedule' );
	if ( RADIO_STATION_DEBUG || $debug ) {
		echo "Full Request Inputs: " . esc_html( print_r( array_map( 'sanitize_text_field', $_REQUEST ), true ) );
		echo "Sanitized Master Schedule Shortcode Attributes: " . esc_html( print_r( $atts, true ) );
	}
	/* foreach ( array_keys( $_REQUEST ) as $key ) {
		if ( !isset( $atts[$key] ) ) {
			echo 'Possible missing key? ' . $key . "\n";
		}
	} */

	// --- output schedule contents ---
	// 2.5.0: remove unused schedule contents wrap
	// TODO: test wp_kses on master schedule output
	echo radio_station_master_schedule( $atts );

	$js = '';
	if ( strstr( $atts['view'], ',' ) ) {
		$views = explode( ',', $atts['view'] );
	} else {
		$views = array( $atts['view'] );
	}
	// echo "Views: " . esc_html( print_r( $views, true ) ) . "\n";
	foreach ( $views as $view ) {

		$view = trim( $view );

		// --- set schedule element ID ---
		if ( 'table' == $view ) {
			$schedule_id = 'master-program-schedule';
		} elseif ( 'tabs' == $view ) {
			$schedule_id = 'master-schedule-tabs';
			$panels_id = 'master-schedule-tab-panels';
		} elseif ( 'list' == $view ) {
			$schedule_id = 'master-list';
		} elseif ( 'grid' == $view ) {
			$schedule_id = 'master-schedule-grid';
		} elseif ( 'calendar' == $view ) {
			$schedule_id = 'master-schedule-calendar';
		}

		// 2.5.0: maybe append instance ID
		if ( $instance > 0 ) {
			$schedule_id .= '-' . $instance;
			// 2.5.10.1: fix to variable typo
			if ( isset( $panels_id ) ) {
				$panels_id .= '-' . $instance;
			}
		}

		// --- get schedule shortcode output ---
		$js .= "schedule = document.getElementById('" . esc_attr( $schedule_id ) . "').innerHTML;" . "\n";
		if ( isset( $panels_id ) ) {
			$js .= "panels = document.getElementById('" . esc_attr( $panels_id ) . "').innerHTML;" . "\n";
		}

		// --- send new schedule to parent window ---
		$js .= "if (parent.document.getElementById('" . esc_attr( $schedule_id ) . "')) {" . "\n";
			$js .= "parent.document.getElementById('" . esc_attr( $schedule_id ) . "').innerHTML = schedule;" . "\n";
		$js .= "}" . "\n";
		if ( isset( $panels_id ) ) {
			$js .= "parent.document.getElementById('" . esc_attr( $panels_id ) . "').innerHTML = panels;" . "\n";
			// 2.5.0: unset panels to prevent possible multiple views conflict
			unset( $panels_id );
		}

		// --- copy the new start date value to the parent window ---
		// TODO: get by child class of schedule_id if multiview?
		$js .= "start_date = document.getElementById('schedule-start-date').value;" . "\n";
		$js .= "parent.document.getElementById('schedule-start-date').value = start_date;" . "\n";
		// $js .= "console.log('New Start Date: '+start_date);" . "\n";

	}

	// --- maybe retrigger view(s) javascript in parent window ---
	// (uses set interval cycle in case script not yet loaded)
	$js .= "var genres_highlighted = false;" . "\n";

	$js .= "function radio_schedule_init() {" . "\n";

		// --- genre highlighter ---
		$js .= "if (!genres_highlighted && (typeof parent.radio_genre_highlight == 'function')) {" . "\n";
			// TODO: add (wrapper) instance to radio_genre_highlight call
			// $js .= "parent.radio_genre_highlight();" . "\n";
			$js .= "genres_highlighted = true;" . "\n";
		$js .= "}" . "\n";

		
		// 2.5.0: loop views to add individual init scripts
		foreach ( $views as $view ) {

			$init_js = '';
			if ( 'table' == $view ) {
				$init_js .= "if (typeof parent.radio_table_initialize == 'function') {" . "\n";
					// 2.5.0: set table state to uninitialized
					$init_js .= "parent.radio_table_init = false;" . "\n";
					$init_js .= "parent.radio_table_initialize();" . "\n";
				$init_js .= "}" . "\n";
			} elseif ( 'tabs' == $view ) {
				$init_js .= "if (typeof parent.radio_tabs_initialize == 'function') {" . "\n";
					$init_js .= "parent.radio_tabs_init = false;" . "\n";
					$schedule_id = 'master-schedule-tabs';
					if ( $instance > 0 ) {
						$schedule_id .= '-' . $instance;
					}
					$init_js .= "parent.radio_tabs_active_tab('" . esc_js( $day ) . "','" . esc_js( $schedule_id ) . "');" . "\n";
					$init_js .= "parent.radio_tabs_initialize();" . "\n";
				$init_js .= "}" . "\n";
			} elseif ( 'list' == $view ) {
				$init_js .= "if (typeof parent.radio_list_highlight == 'function') {" . "\n";
					$init_js .= "parent.radio_list_highlight();" . "\n";
				$init_js .= "}" . "\n";
			}

			// 2.5.0: added individual init script filtering
			$init_js = apply_filters( 'radio_station_master_schedule_init_script', $init_js, $view, $atts );
			$js .= $init_js;

		}

		// --- maybe retrigger convert to user times ---
		$js .= "if (typeof parent.radio_convert_times == 'function') {parent.radio_convert_times();}" . "\n";

		// --- placeholder for extra loader functions ---
		// (do not remove or modify this line - for backwards compatibility)
		$js .= "/* LOADER PLACEHOLDER */" . "\n";

		$js .= "clearInterval(schedule_loader);" . "\n";
		// $js .= "console.log('Schedule Reinitialization Complete');" . "\n";

	$js .= "}" . "\n";

	// --- loop until fired as complete ---
	$js .= "schedule_loader = setInterval(radio_schedule_init, 2000);" . "\n";

	// --- filter load script and output ---
	$js = apply_filters( 'radio_station_master_schedule_load_script', $js, $atts );
	if ( '' != $js ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "<script>" . $js . "</script>";
	}

	exit;
}


// -------------------
// Show Genre Selector
// -------------------
// 2.3.3.9: change name from radio_station_master_schedule_selector
function radio_station_master_schedule_genre_selector( $instance ) {

	// --- get genres ---
	// 2.5.6: add taxonomy to get_terms arguments
	$args = array(
		'taxonomy'   => RADIO_STATION_GENRES_SLUG,
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);
	// 2.5.6: remove deprecated second argument from get_terms
	// $genres = get_terms( RADIO_STATION_GENRES_SLUG, $args );
	$genres = get_terms( $args );
	// 2.3.2: bug out if there are no genre terms
	if ( !$genres || !is_array( $genres ) ) {
		return '';
	}

	// --- open genre highlighter div ---
	$id = ( 0 == $instance ) ? '' : '-' . $instance;
	$html = '<div id="master-genre-list' . esc_attr( $id ) . '" class="master-genre-list">';
	$html .= '<span class="heading">' . esc_html( __( 'Genres', 'radio-station' ) ) . ': </span>';

	// --- genre highlight links ---
	// 2.3.0: fix by imploding with genre link spacer
	// 2.3.3.9: escape genre slug and assign javascript to onclick
	$genre_links = array();
	foreach ( $genres as $i => $genre ) {
		$slug = sanitize_title_with_dashes( $genre->name );
		// 2.5.0: added instance argument to genre highlight function
		$onclick = "radio_genre_highlight(" . esc_js( $instance ) . ",'" . esc_js( $slug ) . "');";
		$title = __( 'Click to toggle Highlight of Shows with this Genre.', 'radio-station' );
		// 2.5.0: remove element ID in favour of class
		$genre_link = '<a class="genre-highlight genre-highlight-' . esc_attr( $slug ) . '" href="javascript:void(0);" onclick="' . $onclick . '" title="' . esc_attr( $title ) . '">';
		$genre_link .= esc_html( $genre->name ) . '</a>';
		$genre_links[] = $genre_link;
	}
	$html .= implode( ' | ', $genre_links );

	$html .= '</div>';

	// --- genre highlighter script ---
	// 2.3.0: improved to highlight / unhighlight multiple genres
	// 2.3.0: improved to work with table, tabs or list view
	// 2.3.3.9: added genre class targets for grid and calendar views
	// 2.3.3.9: added accepting false to retrigger highlights for AJAX
	// 2.5.0: added instance argument to genre highlight function
	// 2.5.0: modified to use traversal via passed wrapper instance
	// 2.5.0: added genre- prefix to class selectors
	$js = "var radio_genres_selected = new Array();
	function radio_genre_highlight(instance,genre) {
		if (0 == instance) {id = '';} else {id = '-'+instance;}
		wrapper = jQuery('#master-schedule'+id);
		classes = ['.master-show-entry', '.master-schedule-tabs-show', '.master-list-day-item', '.master-schedule-grid-show', '.master-schedule-calendar-date', '.master-schedule-calendar-show', '.shows-slider-item'];
		if (radio.debug) {console.log('Genres Before: '+radio_genres_selected[instance]);}
		if (genre === false) {
			for (i = 0; i < classes.length; i++) {
				wrapper.find(classes[i]).removeClass('highlighted');
			}
			if (typeof radio_genres_selected.instance != 'undefined') {
				for (i = 0; i < radio_genres_selected[instance].length; i++) {
					wrapper.find('.genre-'+radio_genres_selected[instance][i]).addClass('highlighted');
				}
			}
		} else {
			if (wrapper.find('.genre-highlight-'+genre).hasClass('highlighted')) {
				wrapper.find('.genre-highlight-'+genre).removeClass('highlighted');
				for (i = 0; i < classes.length; i++) {
					wrapper.find(classes[i]).removeClass('highlighted');
				}
				j = 0; new_genre_highlights = new Array();
				if (typeof radio_genres_selected[instance] != 'undefined') {
					for (i = 0; i < radio_genres_selected[instance].length; i++) {
						if (radio_genres_selected[instance][i] != genre) {
							wrapper.find('.genre-'+radio_genres_selected[instance][i]).addClass('highlighted');
							new_genre_highlights[j] = radio_genres_selected[instance][i]; j++;
						}
					}
				}
				radio_genres_selected[instance] = new_genre_highlights;
			} else {
				wrapper.find('.genre-highlight-'+genre).addClass('highlighted');
				wrapper.find('.genre-'+genre).addClass('highlighted');
				if (typeof radio_genres_selected[instance] == 'undefined') {radio_genres_selected[instance] = new Array();}
				radio_genres_selected[instance][radio_genres_selected[instance].length] = genre;
			}
		}
		if (radio.debug) {console.log('Genres After: '+radio_genres_selected[instance]);}
	}";

	// --- enqueue script ---
	// 2.3.0: add script code to existing handle
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'radio-station', $js );

	return $html;
}

// ---------------------
// Table View Javascript
// ---------------------
// 2.3.0: added for table responsiveness
// 2.5,0: use DOM traversals with jQuery and classes
function radio_station_master_schedule_table_js() {

	// 2.3.2: added current show highlighting cycle
	// 2.3.2: fix to currenthour substr
	// 2.3.3.5: change selected day and arrow logic (to single day shifting)
	// 2.3.3.6: also highlight split shift via matching shift class
	// 2.3.3.9: prefix show-info selector with .master-program-hour-row
	// 2.3.3.9: use setInterval instead of setTimeout for highlighting
	// 2.3.3.9: check for required elements before executing functions
	// 2.3.3.9: fix to check before and after current time not show
	$js = "/* Initialize Table */
	var radio_table_init = false;
	jQuery(document).ready(function() {
		radio_table_initialize();
		var radio_table_highlighting = setInterval(radio_table_highlight, 60000);
	});
	jQuery(window).resize(function () {
		radio_resize_debounce(function() {radio_table_responsive(false,false);}, 500, 'scheduletable');
	});

	/* Table Initialize */
	function radio_table_initialize() {
		radio_table_responsive(false,false);
		radio_table_highlight();
		radio_table_init = true;
	}

	/* Current Time Highlighting */
	function radio_table_highlight() {
		if (!jQuery('.master-program-schedule').length) {return;}
		jQuery('.master-program-schedule').each(function() {
			var scheduletable = jQuery(this);
			radio.current_time = Math.floor((new Date()).getTime() / 1000);
			radio.offset_time = radio.current_time + radio.timezone.offset;
			if (radio.debug) {console.log(radio.current_time+' - '+radio.offset_time);}
			if (radio.timezone.adjusted) {radio.offset_time = radio.current_time;}
			jQuery(this).find('.master-program-day').each(function() {
				start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
				end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
				if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
					jQuery(this).addClass('current-day');
				} else {jQuery(this).removeClass('current-day');}
			});
			jQuery(this).find('.master-program-hour').each(function() {
				hour = parseInt(jQuery(this).find('.master-program-server-hour').attr('data'));
				offset_time = radio.current_time + radio.timezone.offset;
				current = new Date(offset_time * 1000).toISOString();
				currenthour = current.substr(11, 2);
				if (currenthour.substr(0,1) == '0') {currenthour = currenthour.substr(1,1);}
				if (hour == currenthour) {jQuery(this).addClass('current-hour');}
				else {jQuery(this).removeClass('current-hour');}
			});
			for (i = 0; i < 7; i++) {
				jQuery(this).find('.day-'+i).each(function() {
					var radio_table_shift = false;
					jQuery(this).find('.master-show-entry').each(function() {
						start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
						end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
						if (radio.debug) {console.log(jQuery(this)); console.log(start+' - '+end);}
						if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
							if (radio.debug) {console.log('^ Now Playing ^');}
							jQuery(this).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
							/* also highlight split shift via matching shift class */
							if (jQuery(this).hasClass('overnight')) {
								classes = jQuery(this).attr('class').split(/\s+/);
								for (i = 0; i < classes.length; i++) {
									if (classes[i].substr(0,6) == 'split-') {radio_table_shift = classes[i];}
								}
							}
						} else {
							jQuery(this).removeClass('nowplaying');
							if (start > radio.offset_time) {jQuery(this).addClass('after-current');}
							else if (end < radio.offset_time) {jQuery(this).addClass('before-current');}
						}
					});
					if (radio_table_shift) {
						scheduletable.find('.'+radio_table_shift).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
					}
				});
			}
		});
	}

	/* Make Table Responsive */
	function radio_table_responsive(leftright,instance) {
		if (!jQuery('.master-program-schedule').length) {return;}
		if (instance) {
			if (0 == instance) {id = '';} else {id = '-'+instance;}
			tableschedules = jQuery('#master-program-schedule'+id);
		} else {tableschedules = jQuery('.master-program-schedule');}

		tableschedules.each(function() {
			if (jQuery(this).find('.master-program-day').length) {
				fallback = -1; selected = -1; foundday = false;
				if (!leftright || (leftright == 'left')) {
					if (jQuery(this).find('.master-program-day.first-column').length) {
						start = jQuery(this).find('.master-program-day.first-column');
					} else {start = jQuery(this).find('.master-program-day').first(); fallback = 0;}
					classes = start.attr('class').split(' ');
				} else if (leftright == 'right') {
					if (jQuery(this).find('.master-program-day.last-column').length) {
						end = jQuery(this).find('.master-program-day.last-column');
					} else {end = jQuery(this).find('.master-program-day').last(); fallback = 6;}
					classes = end.attr('class').split(' ');
				}
				for (i = 0; i < classes.length; i++) {
					if (classes[i].indexOf('day-') === 0) {selected = parseInt(classes[i].replace('day-',''));}
				}
				if (selected < 0) {selected = fallback;}
				if (radio.debug) {console.log('Current Column: '+selected);}

				if (leftright == 'left') {selected--;} else if (leftright == 'right') {selected++;}
				if (selected < 0) {selected = 0;} else if (selected > 6) {selected = 6;}
				if (!jQuery(this).find('.master-program-day.day-'+selected).length) {
					while (!foundday) {
						if (leftright == 'left') {selected--;} else if (leftright == 'right') {selected++;}
						if (jQuery(this).find('.master-program-day.day-'+selected).length) {foundday = true;}
						if ((selected < 0) || (selected > 6)) {selected = fallback; foundday = true;}
					}
				}
				if (radio.debug) {console.log('Selected Column: '+selected);}

				/* totalwidth = jQuery(this).find('.master-program-hour-heading').width(); */
				jQuery(this).find('.master-program-day, .master-program-hour-row .show-info').removeClass('first-column').removeClass('last-column').hide();
				jQuery(this).css('width','100%');
				tablewidth = jQuery(this).width();
				jQuery(this).css('width','auto').css('overflow','visible');
				columns = 0; firstcolumn = -1; lastcolumn = 7; endtable = false;
				for (i = selected; i < 7; i++) {
					if (!endtable && (jQuery(this).find('.master-program-day.day-'+i).length)) {
						if ((i > 0) && (i == selected)) {jQuery(this).find('.master-program-day.day-'+i).addClass('first-column'); firstcolumn = i;}
						else if (i < 6) {jQuery(this).find('.master-program-day.day-'+i).addClass('last-column');}
						jQuery(this).find('.master-program-day.day-'+i+', .master-program-hour-row .show-info.day-'+i).show();
						colwidth = jQuery(this).find('.master-program-day.day-'+i).width();
						/* totalwidth = totalwidth + colwidth; */
						totalwidth = jQuery(this).width();
						if (radio.debug) {console.log('('+colwidth+') : '+totalwidth+' / '+tablewidth);}
						jQuery(this).find('.master-program-day.day-'+i).removeClass('last-column');
						if (totalwidth > tablewidth) {
							if (radio.debug) {console.log('Hiding Column '+i);}
							jQuery(this).find('.master-program-day.day-'+i+', .master-program-hour-row .show-info.day-'+i).hide(); endtable = true;
						} else {
							if (radio.debug) {console.log('Showing Column '+i);}
							jQuery(this).find('.master-program-day.day-'+i).removeClass('last-column');
							totalwidth = totalwidth - colwidth + jQuery(this).find('.master-program-day.day-'+i).width();
							lastcolumn = i; columns++;
						}
					}
				}
				if (lastcolumn < 6) {jQuery(this).find('.master-program-day.day-'+lastcolumn).addClass('last-column');}
				jQuery(this).css('overflow','');

				if (leftright == 'right') {
					for (i = (selected - 1); i > -1; i--) {
						if (!endtable && (jQuery(this).find('.master-program-day.day-'+i).length)) {
							jQuery(this).find('.master-program-day.day-'+i+', .master-program-hour-row .show-info.day-'+i).show();
							colwidth = jQuery(this).find('.master-program-day.day-'+i).width();
							totalwidth = totalwidth + colwidth;
							if (radio.debug) {console.log('('+colwidth+') : '+totalwidth+' / '+tablewidth);}
							if (totalwidth > tablewidth) {
								if (radio.debug) {console.log('Hiding Column '+i);}
								jQuery(this).find('.master-program-day.day-'+i+', .master-program-hour-row .show-info.day-'+i).hide();
								endtable = true;
							} else {
								if (radio.debug) {console.log('Showing Column '+i);}
								jQuery(this).find('.master-program-day').removeClass('first-column');
								jQuery(this).find('.master-program-day.day-'+i).addClass('first-column');
								columns++;
							}
						}
					}
				}
				jQuery(this).css('width','100%');
			}
		});
	}

	/* Shift Day Left / Right */
	function radio_shift_day(leftright,instance) {
		radio_table_responsive(leftright,instance); return false;
	}" . "\n";

	// --- filter and return ---
	// 2.3.3.9: add filter and return instead of inline enqueue
	$js = apply_filters( 'radio_station_master_schedule_table_js', $js );
	return $js;
}

// ----------------------
// Tabbed View Javascript
// ----------------------
// 2.2.7: added for tabbed schedule view
// TODO: use traversals with instance IDs
function radio_station_master_schedule_tabs_js() {

	// --- tab switching function ---
	// 2.3.2: added fallback if current day is not viewed
	// TODO: check current server time for onload display ?
	/* date = new Date(); dayweek = date.getDay(); day = radio_get_weekday(dayweek);
	if (jQuery('#master-schedule-tabs-header-'+day).length) {
		id = jQuery('.master-schedule-tabs-day.selected-day').first().attr('id');
		day = id.replace('master-schedule-tabs-header-','');
		jQuery('#master-schedule-tabs-header-'+day).addClass('active-day-tab');
		jQuery('#master-schedule-tabs-day-'+day).addClass('active-day-panel');
	} else {
		jQuery('.master-schedule-tabs-day').first().addClass('active-day-tab');
		jQuery('.master-schedule-tabs-panel').first().addClass('active-day-panel');
	} */

	// 2.3.3.6: allow for clicking on date to change days
	// 2.3.3.8: make entire heading label div clickable to change tabs
	// 2.3.3.9: make into function and add to document ready code block
	/* $js = "function radio_tabs_clicks() {
		jQuery('.master-schedule-tabs-headings').bind('click', function (event) {
			headerID = jQuery(event.target).closest('li').attr('id');
			panelID = headerID.replace('header', 'day');
			jQuery('.master-schedule-tabs-day').removeClass('active-day-tab');
			jQuery('#'+headerID).addClass('active-day-tab');
			jQuery('.master-schedule-tabs-panel').removeClass('active-day-panel');
			jQuery('#'+panelID).addClass('active-day-panel');
		});
	}" . "\n"; */
	// 2.5.0: use relative traversal from click target instead of IDs
	$js = "function radio_tabs_clicks() {
		if (radio.debug) {console.log('Binding Tabbed Schedule Tab Clicks');}
		jQuery('.master-schedule-tabs-headings').bind('click', function (event) {
			if (jQuery(event.target).hasClass('master-schedule-tabs-headings')) {day = jQuery(event.target).attr('data-href');}
			else {day = jQuery(event.target).closest('.master-schedule-tabs-headings').attr('data-href');}
			schedule = jQuery(event.target).closest('.master-schedule-tabs');
			panels = schedule.parent().find('.master-schedule-tab-panels');
			schedule.find('.master-schedule-tabs-day').removeClass('active-day-tab');
			schedule.find('.master-schedule-tabs-day-'+day).addClass('active-day-tab');
			panels.find('.master-schedule-tabs-panel').removeClass('active-day-panel');
			panels.find('.master-schedule-tabs-panel-'+day).addClass('active-day-panel');
		});
	}" . "\n";

	// --- tabbed view responsiveness ---
	// 2.3.0: added for tabbed responsiveness
	// 2.3.2: display selected day message if outside view
	// 2.3.3.5: change selected day and arrow logic (to single day shifting)
	// 2.3.3.6: also highlight split shift via matching shift class
	// 2.3.3.9: use setInterval instead of setTimeout for highlighting check
	// 2.3.3.9: check for required elements before executing functions
	// 2.3.3.9: fix to check before and after current time not show
	// 2.3.3.9: adjust responsive tabs for possible loader control presence
	$js .= "/* Initialize Tabs */
	var radio_tabs_init = false;
	jQuery(document).ready(function() {
		radio_tabs_initialize();
		var radio_tab_highlighting = setInterval(radio_tabs_show_highlight, 60000);
	});
	jQuery(window).resize(function () {
		radio_resize_debounce(function() {radio_tabs_responsive(false,false);}, 500, 'scheduletabs');
	});

	/* Initialize Tabs */
	function radio_tabs_initialize() {
		radio_tabs_clicks();
		radio_tabs_responsive(false,false);
		radio_tabs_show_highlight();
	}

	/* Set Day Tab on Load */
	function radio_tabs_active_tab(day,scheduleid) {
		if (radio_tabs_init) {return;}
		if (day) {
			jQuery('#'+scheduleid+' .master-schedule-tabs-day').removeClass('active-day-tab');
			jQuery('#'+scheduleid+' .master-schedule-tabs-day-'+day).addClass('active-day-tab');
			panelid = scheduleid.replace('-tabs','-tab-panels');
			jQuery('#'+panelid+' .master-schedule-tabs-panel').removeClass('active-day-panel');
			jQuery('#'+panelid+' .master-schedule-tabs-panel-'+day).addClass('active-day-panel');
		} else {
			jQuery('#'+scheduleid+' .master-schedule-tabs-day').first().addClass('active-day-tab');
			jQuery('#'+scheduleid+' .master-schedule-tabs-panel').first().addClass('active-day-panel');
		}
		radio_tabs_init = true;
	}

	/* Current Show Highlighting */
	function radio_tabs_show_highlight() {
		if (!jQuery('.master-schedule-tabs').length) {return;}
		jQuery('.master-schedule-tabs').each(function() {
			scheduleid = jQuery(this).attr('id');
			radio.current_time = Math.floor( (new Date()).getTime() / 1000 );
			radio.offset_time = radio.current_time + radio.timezone.offset;
			if (radio.debug) {console.log(radio.current_time+' - '+radio.offset_time);}
			if (radio.timezone.adjusted) {radio.offset_time = radio.current_time;}
			jQuery(this).find('.master-schedule-tabs-day').each(function() {
				start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
				end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
				if ((start < radio.offset_time) && (end > radio.offset_time)) {
					jQuery(this).addClass('current-day');
					day = jQuery(this).attr('id').replace('master-schedule-tabs-header-', '');
					radio_tabs_active_tab(day,scheduleid);
				} else {jQuery(this).removeClass('current-day');}
			});
			radio_tabs_active_tab(false,scheduleid); /* fallback */
			var radio_tabs_split = false;
			jQuery(this).parent().find('.master-schedule-tabs-show').each(function() {
				start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
				end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
				if (radio.debug) {console.log(start+' - '+end);}
				if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
					if (radio.debug) {console.log('^ Now Playing ^');}
					jQuery(this).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
					/* also highlight split shift via matching shift class */
					if (jQuery(this).hasClass('overnight')) {
						classes = jQuery(this).attr('class').split(/\s+/);
						for (i = 0; i < classes.length; i++) {
							if (classes[i].substr(0,6) == 'split-') {radio_tabs_split = classes[i];}
						}
					}
				} else {
					jQuery(this).removeClass('nowplaying');
					if (start > radio.offset_time) {jQuery(this).addClass('after-current');}
					else if (end < radio.offset_time) {jQuery(this).addClass('before-current');}
				}
			});
			if (radio_tabs_split) {
				jQuery(this).parent().find('.'+radio_tabs_split).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
			}
		});
	}

	/* Make Tabs Responsive */
	function radio_tabs_responsive(leftright,instance) {
		if (!jQuery('.master-schedule-tabs').length) {return;}
		if (instance) {
			if (0 == instance) {id = '';} else {id = '-'+instance;}
			tabschedules = jQuery('#master-schedule-tabs'+id);
		} else {tabschedules = jQuery('.master-schedule-tabs');}

		tabschedules.each(function() {
			if (jQuery(this).find('.master-schedule-tabs-day').length) {
				fallback = -1; selected = -1; foundtab = false;
				if (!leftright || (leftright == 'left')) {
					if (jQuery(this).find('.master-schedule-tabs-day.first-tab').length) {
						start = jQuery(this).find('.master-schedule-tabs-day.first-tab');
					} else {start = jQuery(this).find('.master-schedule-tabs-day').first(); fallback = 0;}
					classes = start.attr('class').split(' ');
				} else if (leftright == 'right') {
					if (jQuery(this).find('.master-schedule-tabs-day.last-tab').length) {
						end = jQuery(this).find('.master-schedule-tabs-day.last-tab');
					} else {end = jQuery(this).find('.master-schedule-tabs-day').last(); fallback = 6;}
					classes = end.attr('class').split(' ');
				}
				for (i = 0; i < classes.length; i++) {
					if (classes[i].indexOf('day-') === 0) {selected = parseInt(classes[i].replace('day-',''));}
				}
				if (selected < 0) {selected = fallback;}
				if (radio.debug) {console.log('Current Tab: '+selected);}

				if (leftright == 'left') {selected--;} else if (leftright == 'right') {selected++;}
				if (selected < 0) {selected = 0;} else if (selected > 6) {selected = 6;}
				if (!jQuery(this).find('.master-schedule-tabs-day.day-'+selected).length) {
					while (!foundtab) {
						if (leftright == 'left') {selected--;} else if (leftright == 'right') {selected++;}
						if (jQuery(this).find('.master-schedule-tabs-day.day-'+selected).length) {foundtab = true;}
						if ((selected < 0) || (selected > 6)) {selected = fallback; foundtab = true;}
					}
				}
				if (radio.debug) {console.log('Selected Tab: '+selected);}

				jQuery(this).css('width','100%');
				tabswidth = jQuery(this).width();
				jQuery(this).css('width','auto');
				jQuery(this).find('.master-schedule-tabs-day').removeClass('first-tab').removeClass('last-tab').hide();

				totalwidth = 0; tabs = 0; firsttab = -1; lasttab = 7; endtabs = false;
				if (jQuery(this).find('.master-schedule-tabs-loader-left').length) {totalwidth = totalwidth + jQuery(this).find('.master-schedule-tabs-loader-left').width();}
				if (jQuery(this).find('.master-schedule-tabs-loader-right').length) {totalwidth = totalwidth + jQuery(this).find('.master-schedule-tabs-loader-right').width();}
				if (radio.debug) {console.log('Tabs Start Width: '+totalwidth);}
				
				for (i = selected; i < 7; i++) {
					if (!endtabs && (jQuery(this).find('.master-schedule-tabs-day.day-'+i).length)) {
						if ((i > 0) && (i == selected)) {jQuery(this).find('.master-schedule-tabs-day.day-'+i).addClass('first-tab'); firsttab = i;}
						else if (i < 6) {jQuery(this).find('.master-schedule-tabs-day.day-'+i).addClass('last-tab');}
						tabwidth = jQuery(this).find('.master-schedule-tabs-day.day-'+i).show().width();
						mleft = parseInt(jQuery(this).find('.master-schedule-tabs-day.day-'+i).css('margin-left').replace('px',''));
						mright = parseInt(jQuery(this).find('.master-schedule-tabs-day.day-'+i).css('margin-right').replace('px',''));
						/* totalwidth = totalwidth + tabwidth + mleft + mright; */
						totalwidth = 0;
						jQuery(this).find('li').each(function() {
							if (jQuery(this).is(':visible')) {
								totalwidth += jQuery(this).width();
								totalwidth += parseInt(jQuery(this).css('margin-left').replace('px',''));
								totalwidth += parseInt(jQuery(this).css('margin-right').replace('px',''));
							}
						});
						if (radio.debug) {console.log(tabwidth+' - ('+mleft+'/'+mright+') - '+totalwidth+' / '+tabswidth);}
						if (totalwidth > tabswidth) {
							if (radio.debug) {console.log('Hiding Tab '+i);}
							jQuery(this).find('.master-schedule-tabs-day.day-'+i).hide(); endtabs = true;
						} else {
							jQuery(this).find('.master-schedule-tabs-day.day-'+i).removeClass('last-tab');
							totalwidth = totalwidth - tabwidth + jQuery(this).find('.master-schedule-tabs-day.day-'+i).width();
							if (radio.debug) {console.log('Showing Tab '+i);}
							lasttab = i; tabs++;
						}
					}
				}
				if (lasttab < 6) {jQuery(this).find('.master-schedule-tabs-day.day-'+lasttab).addClass('last-tab');}

				if (leftright == 'right') {
					for (i = (selected - 1); i > -1; i--) {
						if (!endtabs && (jQuery(this).find('.master-schedule-tabs-day.day-'+i).length)) {
							tabwidth = jQuery(this).find('.master-schedule-tabs-day.day-'+i).show().width();
							mleft = parseInt(jQuery(this).find('.master-schedule-tabs-day.day-'+i).css('margin-left').replace('px',''));
							mright = parseInt(jQuery(this).find('.master-schedule-tabs-day.day-'+i).css('margin-right').replace('px',''));
							totalwidth = totalwidth + tabwidth + mleft + mright;
							if (radio.debug) {console.log(tabwidth+' - ('+mleft+'/'+mright+') - '+totalwidth+' / '+tabswidth);}
							if (totalwidth > tabswidth) {
								if (radio.debug) {console.log('Hiding Tab '+i);}
								jQuery(this).find('.master-schedule-tabs-day.day-'+i).hide(); endtabs = true;
							} else {
								jQuery(this).find('.master-schedule-tabs-day').removeClass('first-tab');
								jQuery(this).find('.master-schedule-tabs-day.day-'+i).addClass('first-tab');
								if (radio.debug) {console.log('Showing Tab '+i);}
								tabs++;
							}
						}
					}
				}
				jQuery(this).css('width','100%');

				/* display selected day message if outside view */
				activeday = false;
				for (i = 0; i < 7; i++) {
					if (jQuery(this).find('.master-schedule-tabs-day.day-'+i).length) {
						if (jQuery(this).find('.master-schedule-tabs-day.day-'+i).hasClass('active-day-tab')) {activeday = i;}
					}
				}
				jQuery(this).find('.master-schedule-tabs-selected').hide();
				if ( activeday && ( (activeday > lasttab) || (activeday < firsttab ) ) ) {
					jQuery(this).find('.master-schedule-tabs-selected-'+activeday).show();
				}

				if (radio.debug) {
					console.log('Active Day: '+activeday);
					console.log('Selected: '+selected);
					console.log('Fallback: '+fallback);
					console.log('First Tab: '+firsttab);
					console.log('Last Tab: '+lasttab);
					console.log('Visible Tabs: '+tabs);
				}
			}
		});
	}

	/* Shift Day Left / Right */
	function radio_shift_tab(leftright,instance) {
		radio_tabs_responsive(leftright,instance); return false;
	}" . "\n";

	// --- filter and return ---
	// 2.3.3.9: add filter and return instead of inline enqueue
	$js = apply_filters( 'radio_station_master_schedule_tabs_js', $js );
	return $js;
}

// --------------------
// List View Javascript
// --------------------
// 2.3.2: added for list schedule view
// TODO: use traversals with instance IDs
function radio_station_master_schedule_list_js() {

	// --- list view javascript ---
	// 2.3.3.6: also highlight split shift via matching shift class
	// 2.3.3.9: use setInterval instead of setTimeout for highlighting
	// 2.3.3.9: check for required elements before executing functions
	// 2.3.3.9: fix to check before and after current time not show
	$js = "/* Initialize List */
	jQuery(document).ready(function() {
		radio_list_highlight();
		var radio_list_highlighting = setInterval(radio_list_highlight, 60000);
	});

	/* Current Show Highlighting */
	function radio_list_highlight() {
		if (!jQuery('.master-list-day').length) {return;}
		radio.current_time = Math.floor( (new Date()).getTime() / 1000 );
		radio.offset_time = radio.current_time + radio.timezone.offset;
		if (radio.timezone.adjusted) {radio.offset_time = radio.current_time;}
		jQuery('.master-list-day').each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').first().attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').first().attr('data'));
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				jQuery(this).addClass('current-day');
			} else {jQuery(this).removeClass('current-day');}
		});
		var radio_list_split = false;
		jQuery('.master-list-day-item').each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				radio_list_current = true;
				if (radio.debug) {console.log('^ Now Playing ^');}
				jQuery(this).addClass('nowplaying');
				/* also highlight split shift via matching shift class */
				if (jQuery(this).hasClass('overnight')) {
					classes = jQuery(this).attr('class').split(/\s+/);
					for (i = 0; i < classes.length; i++) {
						if (classes[i].substr(0,6) == 'split-') {radio_list_split = classes[i];}
					}
				}
			} else {
				jQuery(this).removeClass('nowplaying');
				if (start > radio.offset_time) {jQuery(this).addClass('after-current');}
				else if (end < radio.offset_time) {jQuery(this).addClass('before-current');}
			}
		});
		if (radio_list_split) {
			jQuery('.'+radio_list_split).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
		}
	}" . "\n";

	// --- filter and return ---
	// 2.3.3.9: add filter and return instead of inline enqueue
	$js = apply_filters( 'radio_station_master_schedule_list_js', $js );
	return $js;
}
