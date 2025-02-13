<?php

// ===============================
// === Radio Station Schedules ===
// ===============================
// 2.5.0: use new schedule_engine class

if ( !defined( 'ABSPATH' ) ) exit;

// - Instantiate Schedule Engine Class
// === Schedule ===
// - Get Show Schedule
// - Get Show Shifts
// - Get All Schedule Overrides
// - Get Current Schedule
// - Get Current Show
// - Get Previous Show
// - Get Next Show
// - Get Next Shows
// === Shift Checking ===
// - Schedule Conflict Checker
// - Show Shift Checker
// - New Shifts Checker
// - Validate Shift Time
// === Data Setting ===
// - Set Current Schedule
// - Set Previous Show
// - Set Current Show
// - Set Next Show
// - Set Shift Errors
// - Set Shift Conflicts


// ---------------------------------
// Instantiate Schedule Engine Class
// ---------------------------------
// 2.5.0: extracted functions to new schedule engine class
global $rs_se;
$args = array( 'context' => 'radio_station' );
$rs_se = new radio_station_schedule_engine( $args );

add_action( 'plugins_loaded', 'radio_station_schedule_debug_init', 9 );
function radio_station_schedule_debug_init() {
	if ( defined( 'RADIO_STATION_DEBUG' ) && RADIO_STATION_DEBUG ) {
		global $rs_se;
		$rs_se->debug = RADIO_STATION_DEBUG;
	}
}


// ----------------
// === Schedule ===
// ----------------

// -----------------
// Get Show Schedule
// -----------------
function radio_station_get_show_schedule( $show_id ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->get_record_shifts( $show_id, 'show_sched' );
}

// ---------------
// Get Show Shifts
// ---------------
// 2.3.0: added get show shifts data grabber
// 2.3.2: added second argument to get non-split shifts
// 2.3.3: added time as third argument for ondemand shifts
function radio_station_get_show_shifts( $check_conflicts = true, $split = true, $time = false ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	// --- get all show data ---
	$shows = radio_station_get_shows();

	// 2.5.0: map show data before combining to all shifts
	$records = array();
	if ( count( $shows ) > 0 ) {
		foreach ( $shows as $show ) {

			// 2.5.0: get metadata early (maybe via cached)
			if ( isset( $radio_station_data['show-' . $show->ID] ) ) {
				$metadata = $radio_station_data['show-' . $show->ID];
			} else {
				$metadata = radio_station_get_show_data_meta( $show );
				$radio_station_data['show-' . $show->ID] = $metadata;
			}

			$shifts = $metadata['schedule'];
			unset( $metadata['schedule'] );
			$records[] = array(
				'ID'      => $show->ID,
				'updated' => $show->post_modified_gmt,
				'shifts'  => $shifts,
				'show'    => $metadata,
			);
		}
	}

	// --- debug point for show data ---
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">';
		echo 'Show Data: ' . esc_html( print_r( $records, true ) ) . '</span>';
		$data = 'Show Data: ' . print_r( $records, true ) . PHP_EOL;
		radio_station_debug( $data );
	}

	// --- set times for schedule ---
	// 2.5.6: removed as setting of times is no longer necessary here
	$timezone = radio_station_get_timezone();
	/* $now = $time ? $time : radio_station_get_now();
	$today = radio_station_get_time( 'l', $now, $timezone );
	$weekdays = radio_station_get_schedule_weekdays( $today, $now, $timezone );
	$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now, $timezone );
	$times = array(
		'now'       => $now,
		'today'     => $today,
		'weekdays'  => $weekdays,
		'weekdates' => $weekdates,
		'timezone'  => $timezone,
	); */

	// --- get shifts from records ---
	$all_shifts = $rs_se->get_all_shifts( $records, $check_conflicts, $split, $time, $timezone );
	return $all_shifts;
}

// --------------------------
// Get All Schedule Overrides
// --------------------------
// 2.3.0: added get schedule overrides data grabber
function radio_station_get_all_overrides( $start_date = false, $end_date = false, $timezone = false ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	// --- get overrides ---
	$overrides = radio_station_get_overrides();
	// echo '<span style="display:none;">OVERRIDES! ' . print_r( $overrides, true ) . '</span>';

	$timezone = $timezone ? $timezone : radio_station_get_timezone();
	$override_data = array();
	if ( $overrides && is_array( $overrides ) && ( count( $overrides ) > 0 ) ) {
		foreach ( $overrides as $i => $override ) {

			// --- set override ID ---
			$data = array();
			$data['id'] = $override_id = $override->ID;

			// --- linked show and show title ---
			// 2.3.3.9: allow for usage of linked show title
			$data['slug'] = $override->post_name;
			$data['title'] = $override->post_title;
			$linked_id = get_post_meta( $override_id, 'linked_show_id', true );
			if ( $linked_id ) {
				// 2.5.6: fix to assignment of linked_show variable
				$linked_show = get_post( $linked_id );
				$override_data[$i]['linked_show'] = $linked_show;
				// 2.5.2: fix to use override property not array key
				$linked_fields = get_post_meta( $override_id, 'linked_show_fields', true );
				if ( !isset( $linked_fields['show_title'] ) || !$linked_fields['show_title'] ) {
					$data['title'] = $linked_show->post_title;
				}
			}

			// --- get override shifts ---
			// 2.3.3.9: get possible array of override shifts
			$override_shifts = get_post_meta( $override_id, 'show_override_sched', true );
			// 2.3.3.9: convert possible single override to array
			if ( $override_shifts && is_array( $override_shifts ) && array_key_exists( 'date', $override_shifts ) ) {
				$override_shifts = array( $override_shifts );
				update_post_meta( $override_id, 'show_override_sched', $override_shifts );
			}

			// --- check/update old shift format ---
			if ( $override_shifts && is_array( $override_shifts ) && ( count( $override_shifts ) > 0 ) ) {
				// 2.2.3.9: loop to add unique shift IDs and maybe resave
				$update_override_shifts = false;
				foreach ( $override_shifts as $j => $shift_data ) {
					if ( !isset( $shift_data['id'] ) ) {
						$shift_data['id'] = radio_station_unique_shift_id();
						$override_shifts[$j] = $shift_data;
						$update_override_shifts = true;
					}
				}
				if ( $update_override_shifts ) {
					update_post_meta( $override_id, 'show_override_sched', $override_shifts );
				}
			}

			// 2.5.0: added filter for override shifts
			$override_shifts = apply_filters( 'radio_station_override_shifts', $override_shifts, $override, $start_date, $end_date, $timezone );

			// --- set override shifts ---
			$data['shifts'] = $override_shifts;

			// --- get override metadata ---
			$metadata = radio_station_get_override_data_meta( $override );
			$data['show'] = $metadata;

			// --- set override data ---
			$override_data[$i] = $data;
		}
	}
	$overrides = $override_data;

	// --- get all overrides list ---
	$overrides = $rs_se->process_overrides( $overrides, $start_date, $end_date, $timezone );
	// 2.5.0: keep filter for backwards compatibility
	$overrides = apply_filters( 'radio_station_get_overrides', $overrides, $start_date, $end_date, $timezone );
	return $overrides;
}

// --------------------
// Get Current Schedule
// --------------------
// 2.3.2: added optional time argument
// 2.3.3.5: added optional weekstart argument
function radio_station_get_current_schedule( $time = false, $weekstart = false ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	$show_shifts = false;

	// --- maybe get cached schedule ---
	// 2.3.3: check data global first
	if ( !$time ) {
		if ( '' != $channel ) {
			if ( isset( $radio_station_data['current_schedule_' . $channel] ) ) {
				return $radio_station_data['current_schedule_' . $channel];
			} else {
				$show_shifts = get_transient( 'radio_station_current_schedule_' . $channel );
			}
		} elseif ( isset( $radio_station_data['current_schedule'] ) ) {
			return $radio_station_data['current_schedule'];
		} else {
			$show_shifts = get_transient( 'radio_station_current_schedule' );
		}
	} else {
		// --- get schedule for time ---
		// 2.3.2: added transient for time schedule
		if ( '' != $channel ) {
			if ( isset( $radio_station_data['current_schedule_' . $channel . '_' . $time] ) ) {
				return $radio_station_data['current_schedule_' . $channel . '_' . $time];
			} else {
				$show_shifts = get_transient( 'radio_station_current_schedule_' . $channel . '_' . $time );
			}
		} elseif ( isset( $radio_station_data['current_schedule_' . $time] ) ) {
			return $radio_station_data['current_schedule_' . $time];
		} else {
			$show_shifts = get_transient( 'radio_station_current_schedule_' . $time );
			if ( RADIO_STATION_DEBUG && $show_shifts ) {
				// 2.3.3.9: fix to clear transient object cache (OMFG.)
				if ( isset( $_REQUEST['clear'] ) && ( '1' === sanitize_text_field( $_REQUEST['clear'] ) ) ) {
					echo "Clearing object cache for requested schedule time." . PHP_EOL;
					wp_cache_delete( 'radio_station_current_schedule_' . $time, 'transients' );
					$show_shifts = false;
				}
			}
		}
	}

	if ( !$show_shifts ) {

		// --- get weekdates ---
		$now  = $time ? $time : radio_station_get_now();
		$timezone = radio_station_get_timezone();
		// 2.3.3.5: add passthrough of optional week start argument
		$weekdays = radio_station_get_schedule_weekdays( $weekstart, $now, $timezone );
		$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now, $timezone );
		$times = array(
			'time'      => $now,
			'timezone'  => $timezone,
			'weekdays'  => $weekdays,
			'weekdates' => $weekdates,
		);

		// --- get all show shifts ---
		// 2.3.3: also pass time to get show_shifts function
		$show_shifts = radio_station_get_show_shifts( true, true, $time );

		// 2.3.1: add any empty keys to ensure overrides are checked
		foreach ( $weekdays as $weekday ) {
			if ( !isset( $show_shifts[$weekday] ) ) {
				$show_shifts[$weekday] = array();
			}
		}

		// --- debug point ---
		if ( RADIO_STATION_DEBUG ) {
			$debug = "Show Shifts: " . esc_html( print_r( $show_shifts, true ) ) . PHP_EOL;
			radio_station_debug( $debug );
		}

		// --- get show overrides ---
		// (from 12am this morning, for one week ahead and back)
		// 2.3.1: get start and end dates from weekdays
		// 2.3.2: use get time function with timezone
		// 2.3.3.9: pass second argument as time may not be now
		// $date = radio_station_get_time( 'date', $now, $timezone );
		// $start_time = strtotime( '12am ' . $date );
		// $end_time = $start_time + ( 7 * 24 * 60 * 60 ) + 1;
		// $start_time = $start_time - ( 7 * 24 * 60 * 60 ) - 1;
		// $start_date = radio_station_get_time( 'd-m-Y', $start_time );
		// $end_date = radio_station_get_time( 'd-m-Y', $end_time );
		$start_date = $weekdates[$weekdays[0]];
		$end_date = $weekdates[$weekdays[6]];
		$overrides = radio_station_get_all_overrides( $start_date, $end_date, $timezone );

		// --- debug point ---
		if ( RADIO_STATION_DEBUG ) {
			$date = radio_station_get_time( 'date', $now );
			$debug = "Now: " . $now . " - Date: " . $date . PHP_EOL;
			$debug .= "Week Start Date: " . $start_date . " - Week End Date: " . $end_date . PHP_EOL;
			$debug .= "Schedule Overrides: " . print_r( $overrides, true ) . PHP_EOL;
			radio_station_debug( $debug );
		}

		// --- combine shifts and overrides ---
		$show_shifts = $rs_se->combine_shifts( $show_shifts, $overrides, $times );

		// --- filter and process ---
		// 2.3.2: added time argument to filter
		// 2.3.3: apply filter only once
		$show_shifts = apply_filters( 'radio_station_current_schedule', $show_shifts, $time, $weekstart, $timezone, $channel );

		// --- process shifts ---
		$show_shifts = $rs_se->process_shifts( $show_shifts, $times );

		// 2.5.0: use do action to set current schedule data/transient
		$expires = 3600;
		do_action( 'radio_station_set_current_schedule', $show_shifts, $expires, $time, $channel );

		// echo '<span style="display:none;">SHOW SHIFTS! ' . print_r( $show_shifts, true ) . '</span>';

	}

	return $show_shifts;
}

// ----------------
// Get Current Show
// ----------------
// 2.3.0: added new get current show function
// 2.3.2: added optional time argument
function radio_station_get_current_show( $time = false ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	// --- get cached current show value ---
	// 2.3.3: remove current show transient
	// 2.3.3: check for existing global data first
	// 2.5.0: maybe get channel or combined channel/time data
	if ( !$time ) {
		if ( ( '' != $channel ) && isset( $radio_station_data['current_show_' . $channel] ) ) {
			$current_show = $radio_station_data['current_show_' . $channel];
		} elseif ( isset( $radio_station_data['current_show'] ) ) {
			$current_show = $radio_station_data['current_show'];
		}
	} else {
		if ( isset( $radio_station_data['current_show_' . $channel . '_' . $time] ) ) {
			$current_show = $radio_station_data['current_show_' . $channel . '_' . $time];
		} elseif ( isset( $radio_station_data['current_show_' . $time] ) ) {
			$current_show = $radio_station_data['current_show_' . $time];
		}
	}
	if ( isset( $current_show ) ) {
		if ( RADIO_STATION_DEBUG ) {
			echo 'Current Show (Stored): ' . esc_html( print_r( $current_show, true ) ) . PHP_EOL;
		}
		return $current_show;
	}

	// --- get all show shifts ---
	if ( !$time ) {
		$show_shifts = radio_station_get_current_schedule( false, $channel );
	} else {
		$show_shifts = radio_station_get_current_schedule( $time, $channel );
	}

	// --- get currently scheduled show ---
	$timezone = radio_station_get_timezone();
	$current_show = $rs_se->get_current_shift( $show_shifts, $time, $timezone );

	// --- filter current show ---
	// 2.3.2: added time argument to filter
	$current_show = apply_filters( 'radio_station_current_show', $current_show, $time, $show_shifts, $channel );

	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Current Show (Set): ' . esc_html( print_r( $current_show, true ) ) . '</span>' . PHP_EOL;
	}

	// --- set to global data ---
	// 2.5.0: maybe set channel or combined channel/time data
	if ( !$time ) {
		if ( '' != $channel ) {
			$radio_station_data['current_show_' . $channel] = $current_show;
		} else {
			$radio_station_data['current_show'] = $current_show;
		}
	} else {
		if ( '' != $channel ) {
			$radio_station_data['current_show_' . $channel . '_' . $time] = $current_show;
		} else {
			$radio_station_data['current_show_' . $time] = $current_show;
		}
	}

	return $current_show;
}

// -----------------
// Get Previous Show
// -----------------
// 2.3.3: added get previous show function
function radio_station_get_previous_show( $time = false ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	$prev_show = false;

	// --- get cached current show value ---
	if ( !$time ) {
		// 2.5.0: maybe get previous show for channel
		if ( '' != $channel ) {
			if ( isset( $radio_station_data['previous_show_' . $channel] ) ) {
				$prev_show = $radio_station_data['previous_show_' . $channel];
			} else {
				$prev_show = get_transient( 'radio_station_previous_show_' . $channel );
			}
		} elseif ( isset( $radio_station_data['previous_show'] ) ) {
			$prev_show = $radio_station_data['previous_show'];
		} else {
			$prev_show = get_transient( 'radio_station_previous_show' );
		}
	} else {
		// 2.5.0: maybe get previous show for channel/time combination
		if ( '' != $channel ) {
			if ( isset( $radio_station_data['previous_show_' . $channel . '_' . $time] ) ) {
				$prev_show = $radio_station_data['previous_show_' . $channel . '_' . $time];
			} else {
				$prev_show = get_transient( 'radio_station_previous_show_' . $channel . '_' . $time );
			}
		} elseif ( isset( $radio_station_data['previous_show_' . $time] ) ) {
			$prev_show = $radio_station_data['previous_show_' . $time];
		} else {
			$prev_show = get_transient( 'radio_station_previous_show_' . $time );
		}
	}

	// --- if not set it has expired so recheck schedule ---
	if ( !$prev_show ) {
		if ( !$time ) {
			$schedule = radio_station_get_current_schedule( false, $channel );
			if ( '' != $channel ) {
				if ( isset( $radio_station_data['previous_show_' . $channel] ) ) {
					$prev_show = $radio_station_data['previous_show_' . $channel];
				}
			} elseif ( isset( $radio_station_data['previous_show'] ) ) {
				$prev_show = $radio_station_data['previous_show'];
			}
		} else {
			$schedule = radio_station_get_current_schedule( $time, $channel );
			if ( '' != $channel ) {
				if ( isset( $radio_station_data['previous_show_' . $channel . '_' . $time] ) ) {
					$prev_show = $radio_station_data['previous_show_' . $channel . '_' . $time];
				}
			} elseif ( isset( $radio_station_data['previous_show_' . $time] ) ) {
				$prev_show = $radio_station_data['previous_show_' . $time];
			}
		}
	}

	// note: already filtered when set
	return $prev_show;
}

// -------------
// Get Next Show
// -------------
// 2.3.0: added new get next show function
// 2.3.2: added optional time argument
function radio_station_get_next_show( $time = false ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	$next_show = false;
	if ( $rs_se->now == $time ) {
		$time = false;
	}

	// --- get cached current show value ---
	if ( !$time ) {
		// 2.5.0: maybe get next show for channel
		if ( '' != $channel ) {
			if ( isset( $radio_station_data['next_show_' . $channel] ) ) {
				$next_show = $radio_station_data['next_show_' . $channel];
			} else {
				$next_show = get_transient( 'radio_station_next_show_' . $channel );
			}
		} else {
			if ( isset( $radio_station_data['next_show'] ) ) {
				$next_show = $radio_station_data['next_show'];
			} else {
				$next_show = get_transient( 'radio_station_next_show' );
			}
		}

	} else {
		// 2.5.0: maybe get next show for channel/time combo
		if ( '' != $channel ) {
			if ( isset( $radio_station_data['next_show_' . $channel . '_' . $time] ) ) {
				$next_show = $radio_station_data['next_show_' . $channel . '_' . $time];
			} else {
				$next_show = get_transient( 'radio_station_next_show_' . $channel . '_' . $time );
			}
		}
		if ( isset( $radio_station_data['next_show_' . $time] ) ) {
			$next_show = $radio_station_data['next_show_' . $time];
		} else {
			$next_show = get_transient( 'radio_station_next_show_' . $time );
		}
	}

	// echo '<span style="display:none;">&1&'; var_dump( $time ); var_dump( $next_show ); echo '</span>';

	// --- if not set it has expired so recheck schedule ---
	if ( !$next_show ) {
		if ( !$time ) {
			$schedule = radio_station_get_current_schedule( false, $channel );
			// 2.5.0: maybe get next show for channel
			if ( '' != $channel ) {
				if ( isset( $radio_station_data['next_show_' . $channel] ) ) {
					$next_show = $radio_station_data['next_show_' . $channel];
				} else {
					$next_show = get_transient( 'radio_station_next_show_' . $channel );
				}
			} elseif ( isset( $radio_station_data['next_show'] ) ) {
				$next_show = $radio_station_data['next_show'];
			} else {
				$next_show = get_transient( 'radio_station_next_show' );
			}
		} else {
			$schedule = radio_station_get_current_schedule( $time, $channel );
			// 2.5.0: maybe get next show for channel/time combo
			if ( '' != $channel ) {
				if ( isset( $radio_station_data['next_show_' . $channel . '_' . $time] ) ) {
					$next_show = $radio_station_data['next_show_' . $channel . '_' . $time];
				} else {
					$next_show = get_transient( 'radio_station_next_show_' . $channel . '_' . $time );
				}
			} elseif ( isset( $radio_station_data['next_show_' . $time] ) ) {
				$next_show = $radio_station_data['next_show_' . $time];
			} else {
				$next_show = get_transient( 'radio_station_next_show_' . $time );
			}
		}

		// 2.3.2: added time argument to filter
		// 2.3.4: moved filter to where data is set so only applied once
		// $next_show = apply_filters( 'radio_station_next_show', $next_show, $time );
	}

	// echo '<span style="display:none;">&2&'; var_dump( $time ); var_dump( $next_show ); echo '</span>';

	return $next_show;
}

// --------------
// Get Next Shows
// --------------
// 2.3.0: added new get next shows function
// 2.3.2: added optional time argument
function radio_station_get_next_shows( $limit = 3, $show_shifts = false, $time = false ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	// --- get all show shifts ---
	// (this check is needed to prevent an endless loop!)
	if ( !$show_shifts ) {
		if ( !$time ) {
			$show_shifts = radio_station_get_current_schedule( false, $channel );
		} else {
			$show_shifts = radio_station_get_current_schedule( $time, $channel );
		}
	}

	// --- loop (remaining) shifts to add show data ---
	// 2.3.2: maybe set provided time as now
	$now = $time ? $time : radio_station_get_now();
	$timezone = radio_station_get_timezone();

	// --- get next shows ---
	$next_shows = $rs_se->get_next_shifts( $limit, $show_shifts, $time, $timezone );

	// --- maybe set next show transient ---
	// 2.3.3: also set global data key
	// 2.3.4: moved next show filter here (before setting data)
	if ( is_array( $next_shows ) && ( count( $next_shows ) > 0 ) ) {

		$next_show = $next_shows[0];
		$next_show = apply_filters( 'radio_station_next_show', $next_show, $time, $channel );
		$shift_start_time = radio_station_to_time( $next_show['date'] . ' ' . $next_show['start'], $timezone );
		$shift_end_time = radio_station_to_time( $next_show['date'] . ' ' . $next_show['end'], $timezone );
		// 2.5.0: handle split shifts over midnight
		if ( $shift_end_time < $shift_start_time ) {
			$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
		}
		$expires = $shift_end_time - $now - 1;

		// 2.5.0: call next shift action to set transient data
		do_action( 'radio_station_set_next_shift', $next_show, $expires, $time, $channel );

		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Next Show: ' . esc_html( print_r( $next_show, true ) ) . '</span>';
		}
	}

	// --- filter and return ---
	$next_shows = apply_filters( 'radio_station_next_shows', $next_shows, $limit, $show_shifts, $time, $channel );
	return $next_shows;
}


// ----------------------
// === Shift Checking ===
// ----------------------

// -------------------------
// Schedule Conflict Checker
// -------------------------
// (checks all existing show shifts for schedule)
// 2.3.0: added show shift conflict checker
function radio_station_check_shifts( $all_shifts ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	// --- get times ---
	// 2.3.3.9: fix weekday/dates code to match radio_station_get_show_shifts
	// 2.5.0: set times array for conflict checking
	$now = radio_station_get_now();
	$timezone = radio_station_get_timezone();
	$today = radio_station_get_time( 'l', $now, $timezone );
	$weekdays = radio_station_get_schedule_weekdays( $today, $now, $timezone );
	$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now, $timezone );
	$times = array(
		'now'       => $now,
		'today'     => $today,
		'weekdays'  => $weekdays,
		'weekdates' => $weekdates,
		'timezone'  => $timezone,
	);

	// --- check for shift conflicts ---
	$checked_shifts = $rs_se->check_shifts( $all_shifts, $times );
	return $checked_shifts;
}

// ------------------
// Show Shift Checker
// ------------------
// (checks shift being saved against other shows)
function radio_station_check_shift( $show_id, $shift, $scope = 'all' ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	// 2.3.2: manual bypass of shift checking
	if ( isset( $_REQUEST['check-bypass'] ) && ( '1' === sanitize_text_field( $_REQUEST['check-bypass'] ) ) ) {
		return false;
	}

	// --- get all show shift times ---
	if ( '' != $channel ) {
		if ( isset( $radio_station_data['all_shifts_' . $channel] ) ) {
			$all_shifts = $radio_station_data['all_shifts_' . $channel];
		}
	} elseif ( isset( $radio_station_data['all_shifts'] ) ) {
		// --- get stored data ---
		$all_shifts = $radio_station_data['all_shifts'];
	} else {

		// (with conflict checking off as we are doing that now)
		$all_shifts = radio_station_get_show_shifts( false, false, false );

		// --- store this data for efficiency ---
		if ( ( '' != $channel ) && is_string( $channel ) ) {
			$radio_station_data['all_shifts_' . $channel] = $all_shifts;
		} else {
			$radio_station_data['all_shifts'] = $all_shifts;
		}
	}

	// --- convert days to dates for checking ---
	// 2.3.3.9: fix weekday/dates code to match radio_station_get_show_shifts
	$now = radio_station_get_now();
	$timezone = radio_station_get_timezone();
	$today = radio_station_get_time( 'l', $now, $timezone );
	$weekdays = radio_station_get_schedule_weekdays( $today, $now, $timezone );
	$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now, $timezone );
	$times = array(
		'now'       => $now,
		'today'     => $today,
		'weekdays'  => $weekdays,
		'weekdates' => $weekdates,
		'timezone'  => $timezone,
	);

	// --- check shift for conflict ---
	$conflicts = $rs_se->check_shift( $show_id, $shift, $scope, $all_shifts, $times );
 	return $conflicts;
}

// ------------------
// New Shifts Checker
// ------------------
// (checks show shifts for conflicts with same show)
function radio_station_check_new_shifts( $new_shifts, $weekdates = false ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	// --- shift checking bypass switch ---
	if ( isset( $_REQUEST['check-bypass'] ) && ( '1' === sanitize_text_field( $_REQUEST['check-bypass'] ) ) ) {
		return $new_shifts;
	}

	// --- maybe get weekdates ---
	if ( !$weekdates ) {
		$now = radio_station_get_now();
		$timezone = radio_station_get_timezone();
		$today = radio_station_get_time( 'l', $now, $timezone );
		$weekdays = radio_station_get_schedule_weekdays( $today, $now, $timezone );
		$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now, $timezone );
	}

	// --- check new shifts ---
	$new_shifts = $rs_se->check_new_shifts( $new_shifts, $weekdates );
	return $new_shifts;
}

// -------------------
// Validate Shift Time
// -------------------
// 2.3.0: added check for incomplete shift times
function radio_station_validate_shift( $shift ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	$validated = $rs_se->validate_shift( $shift );
	return $validated;
}


// --------------------
// === Data Setting ===
// --------------------

// --------------------
// Set Current Schedule
// --------------------
add_action( 'radio_station_set_current_schedule', 'radio_station_set_current_schedule', 10, 4 );
function radio_station_set_current_schedule( $show_shifts, $expires, $time, $channel ) {

	global $radio_station_data;

	// --- set current schedule data global ---
	// note: transients are already set by schedule engine
	if ( !$time ) {
		if ( '' != $channel ) {
			$radio_station_data['current_schedule_' . $channel] = $show_shifts;
			// set_transient( 'radio_station_current_schedule_' . $channel, $show_shifts );
		} else {
			$radio_station_data['current_schedule'] = $show_shifts;
			// set_transient( 'radio_station_current_schedule', $show_shifts );
		}
	} else {
		$time = (string) $time;
		if ( '' != $channel ) {
			$radio_station_data['current_schedule_' . $channel . '_' . $time] = $show_shifts;
			// set_transient( 'radio_station_current_schedule_' . $channel . '_' . $time, $show_shifts );
		} else {
			$radio_station_data['current_schedule_' . $time] = $show_shifts;
			// set_transient( 'radio_station_current_schedule_' . $time, $show_shifts );
		}
	}
}

// -----------------
// Set Previous Show
// -----------------
add_action( 'radio_station_set_previous_shift', 'radio_station_set_previous_show', 10, 4 );
function radio_station_set_previous_show( $previous_show, $expires, $time, $channel ) {

	global $radio_station_data;
	$transient_key = 'previous_show';
	if ( '' != $channel ) {
		$transient_key .= '_' . $channel;
	}
	if ( $time ) {
		$time = (string) $time;
		$transient_key .= '_' . $time;
	}
	$radio_station_data[$transient_key] = $previous_show;
	// note: sets previous_show not previous_shift
	set_transient( 'radio_station_' . $transient_key, $previous_show, $expires );
}

// ----------------
// Set Current Show
// ----------------
add_action( 'radio_station_set_current_shift', 'radio_station_set_current_show', 10, 4 );
function radio_station_set_current_show( $next_show, $expires, $time, $channel ) {

	global $radio_station_data;
	$transient_key = 'current_show';
	if ( '' != $channel ) {
		$transient_key .= '_' . $channel;
	}
	if ( $time ) {
		$time = (string) $time;
		$transient_key .= '_' . $time;
	}
	$radio_station_data[$transient_key] = $next_show;
	// note: sets current_show not current_shift
	set_transient( 'radio_station_' . $transient_key, $next_show, $expires );
}

// -------------
// Set Next Show
// -------------
add_action( 'radio_station_set_next_shift', 'radio_station_set_next_show', 10, 4 );
function radio_station_set_next_show( $next_show, $expires, $time, $channel ) {

	global $radio_station_data;
	$transient_key = 'next_show';
	if ( '' != $channel ) {
		$transient_key .= '_' . $channel;
	}
	if ( $time ) {
		$time = (string) $time;
		$transient_key .= '_' . $time;
	}
	$radio_station_data[$transient_key] = $next_show;

	// note: sets next_show not next_shift
	// echo '<span style="display:none;">Next Show Transient: ' . $transient_key . ' (' . $expires . ')' . PHP_EOL; var_dump( $next_show ); echo '</span>';
	set_transient( 'radio_station_' . $transient_key, $next_show, $expires );
}

// ----------------
// Set Shift Errors
// ----------------
add_action( 'radio_station_set_shift_errors', 'radio_station_shift_errors', 10, 2 );
function radio_station_shift_errors( $errors, $channel ) {
	// --- maybe store any found shift errors ---
	if ( $errors && is_array( $errors ) && count( $errors ) > 0 ) {
		if ( ( '' != $channel ) && is_string( $channel ) ) {
			update_option( 'radio_station_' . $channel . '_shift_errors', $errors );
		} else {
			update_option( 'radio_station_shift_errors', $errors );
		}
	} else {
		if ( ( '' != $channel ) && is_string( $channel ) ) {
			delete_option( 'radio_station_' . $channel . '_shift_errors' );
		} else {
			delete_option( 'radio_station_shift_errors' );
		}
	}
}

// -------------------
// Set Shift Conflicts
// -------------------
add_action( 'radio_station_set_shift_conflicts', 'radio_station_set_shift_conflicts', 10, 2 );
function radio_station_set_shift_conflicts( $conflicts, $channel ) {

	// --- check if any conflicts found ---
	if ( count( $conflicts ) > 0 ) {

		// --- debug point ---
		if ( RADIO_STATION_DEBUG ) {
			$debug = "Shift Conflict Data: " . print_r( $conflicts, true ) . PHP_EOL;
			if ( ( '' != $channel ) && is_string( $channel ) ) {
				$debug .= '(for Channel ' . $channel . ')' . PHP_EOL;
			}
			radio_station_debug( $debug );
		}

		// --- save any conflicts found ---
		if ( ( '' != $channel ) && is_string( $channel ) ) {
			update_option( 'radio_station_' . $channel . '_schedule_conflicts', $conflicts );
		} else {
			update_option( 'radio_station_schedule_conflicts', $conflicts );
		}

	} else {

		// --- clear conflicts data ---
		if ( ( '' != $channel ) && is_string( $channel ) ) {
			delete_option( 'radio_station_' . $channel . '_schedule_conflicts' );
		} else {
			delete_option( 'radio_station_schedule_conflicts' );
		}
	}
}
