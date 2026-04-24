<?php

// =======================
// === Schedule Engine ===
// =======================
// ==== Version 2.5.0 ====
// =======================

if ( !defined( 'ABSPATH' ) ) exit;

// - Set Scheduler Debug Mode
// - Open Schedule Engine Class
// - Class Constructor
// === Caching ===
// - Set Current Schedule
// - Set Previous Shift
// - Set Current Shift
// - Set Next Shift
// === Schedule ===
// - Get Record Shifts
// - Generate Unique Shift ID
// - Generate Hashed GUID
// - Get All Shifts
// - Get Schedule Overrides
// - Get Current Schedule
// - Get Current Show
// - Get Previous Shift
// - Get Next Shift
// - Get Next Scheduled
// === Shift Checking ===
// - Schedule Conflict Checker
// - Shift Checker
// - New Shifts Checker
// - Validate Shift Time
// === Time Conversions ===
// - Get Now
// - Get Timezone
// - Get Timezone Code
// - Get Date Time Object
// - String To Time
// - Get Time
// - Get Timezone Options
// - Get Weekday(s)
// - Get Month(s)
// - Get Schedule Weekdays
// - Get Schedule Weekdates
// - Get Next Day
// - Get Previous Day
// - Get Next Date
// - Get Previous Date
// - Convert Hour to Time Format
// - Convert Shift to Time Format
// - Convert Show Shift
// - Convert Show Shifts
// - Convert Schedule Shifts
// === Time Translations ===
// - Get Locale
// - Get All Months
// - Get All Days
// - Get All Hours
// - Get All Minutes
// - Translate Weekday
// - Replace Weekdays
// - Translate Month
// - Replace Months
// - Translate Meridiem
// - Replace Meridiems
// - Translate Time String
// === Debugging ===
// - Write to Debug Log File

// ------------------------
// Set Scheduler Debug Mode
// ------------------------
// note: use anonymous function to prevent possible future conflicts
// 2.5.6: remove this as it is  unnecessary
/* add_action( 'plugins_loaded', function() {
	if ( !defined( 'SCHEDULE_ENGINE_DEBUG' ) ) {
		define( 'SCHEDULE_ENGINE_DEBUG', false );
	}
}); */

// --------------------------
// Open Schedule Engine Class
// --------------------------
// 2.5.0: added prefixed class to prevent future conflicts
class radio_station_schedule_engine {

	// --- set default arguments ---
	public $channel = '';
	public $context = '';

	public $locale = '';
	public $now = '';
	public $expires = 1800;

	public $conflicts = false;
	public $debug = false;
	public $debug_log = false;
	
	// -----------------
	// Class Constructor
	// -----------------
	function __construct( $args ) {
		
		// --- set class variables ---
		if ( isset( $args['channel'] ) ) {
			$this->channel = $args['channel'];
		}
		if ( isset( $args['context'] ) ) {
			$this->context = $args['context'];
		}
		if ( isset( $args['locale'] ) ) {
			$this->locale = $args['locale'];
		} else {
			$this->locale = get_locale();
		}

		// 2.5.6: set now time once to prevent data mismatches
		if ( isset( $args['time'] ) ) {
			$this->now = $args['time'];
		} else {
			$this->now = $this->get_now();
		}
		// 2.5.6: allow passing of transient expiry times
		if ( isset( $args['expires'] ) ) {
			$this->expires = $args['expires'];
		}

		// --- check debug constants ---
		if ( defined( 'SCHEDULE_ENGINE_DEBUG' ) && SCHEDULE_ENGINE_DEBUG ) {
			$this->debug = SCHEDULE_ENGINE_DEBUG;
		} elseif ( isset( $args['debug'] ) ) {
			$this->debug = $args['debug'];
		}
			
		if ( defined( 'SCHEDULE_ENGINE_DEBUG_LOG' ) && SCHEDULE_ENGINE_DEBUG_LOG ) {
			$this->debug_log = SCHEDULE_ENGINE_DEBUG_LOG;
		} elseif ( isset( $args['debug_log'] ) ) {
			$this->debug_log = $args['debug_log'];
		}
		
		
		// --- add class actions ---
		add_action( 'schedule_engine_set_current_schedule', array( $this, 'set_current_schedule' ), 10, 5 );
		add_action( 'schedule_engine_set_previous_shift', array( $this, 'set_previous_shift' ), 10, 5 );
		add_action( 'schedule_engine_set_current_shift', array( $this, 'set_current_shift' ), 10, 5 );
		add_action( 'schedule_engine_set_next_shift', array( $this, 'set_next_shift' ), 10, 5 );

	}


	// ---------------
	// === Caching ===
	// ---------------

	// --------------------
	// Set Current Schedule
	// --------------------
	public function set_current_schedule( $current_schedule, $expires, $time, $channel, $context ) {

		// --- require a context ---
		if ( '' == $context ) {
			return;
		}

		// --- set current schedule transient ---
		$transient_key = 'current_schedule';
		if ( '' != $channel ) {
			$transient_key .= '_' . $channel;
		}	
		if ( $time ) {
			$time = (string) $time;
			$transient_key .= '_' . $time;
		}
		set_transient( $context . '_' . $transient_key, $current_schedule, $expires );
	}

	// ------------------
	// Set Previous Shift
	// ------------------
	public function set_previous_shift( $previous_shift, $expires, $time, $channel, $context ) {

		// --- require a context ---
		if ( '' == $context ) {
			return;
		}

		// --- set previous shift transient ---
		$transient_key = 'previous_shift';
		if ( '' != $channel ) {
			$transient_key .= '_' . $channel;
		}	
		if ( $time ) {
			$time = (string) $time;
			$transient_key .= '_' . $time;
		}
		set_transient( $context . '_' . $transient_key, $previous_shift, $expires );
	}

	// -----------------
	// Set Current Shift
	// -----------------
	function set_current_shift( $next_show, $expires, $time, $channel, $context ) {

		// --- require a context ---
		if ( '' == $context ) {
			return;
		}

		// --- set current shift transient ---
		$transient_key = 'current_shift';
		if ( '' != $channel ) {
			$transient_key .= '_' . $channel;
		}	
		if ( $time ) {
			$time = (string) $time;
			$transient_key .= '_' . $time;
		}
		set_transient( $context . '_' . $transient_key, $next_show, $expires );
	}

	// --------------
	// Set Next Shift
	// --------------
	public function set_next_shift( $next_shift, $expires, $time, $channel, $context ) {

		// --- require a context ---
		if ( '' == $context ) {
			return;
		}

		// --- set next shift transient ---
		$transient_key = 'next_shift';
		if ( '' != $channel ) {
			$transient_key .= '_' . $channel;
		}	
		if ( $time ) {
			$time = (string) $time;
			$transient_key .= '_' . $time;
		}
		set_transient( $context . '_' . $transient_key, $next_shift, $expires );
	}

	
	// ----------------
	// === Schedule ===
	// ----------------

	// -----------------
	// Get Record Shifts
	// -----------------
	public function get_record_shifts( $record_id, $key ) {

		// --- get show shift schedule ---
		$shifts = get_post_meta( $record_id, $key, true );
		if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
			$changed = false;
			foreach ( $shifts as $i => $shift ) {

				// --- check for unique ID length ---
				if ( strlen( $i ) != 8 ) {

					// --- generate unique shift ID ---
					unset( $shifts[$i] );
					$unique_id = $this->unique_shift_id();
					$shifts[$unique_id] = $shift;
					$changed = true;
				}
			}

			// --- update shifts to save unique ID indexes ---
			if ( $changed ) {
				update_post_meta( $record_id, $key, $shifts );
			}
		}

		return $shifts;
	}

	// ------------------------
	// Generate Unique Shift ID
	// ------------------------
	public function unique_shift_id() {

		// --- set channel / context ---
		$channel = $this->channel;
		$context = $this->context;

		if ( '' != $channel ) {
			$option_key = $context . '_' . $channel . '_shift_ids';
		} else {
			$option_key = $context . '_shift_ids';
		}

		// --- get all shift IDs ---
		$shift_ids = get_option( $option_key );
		if ( !$shift_ids ) {
			$shift_ids = array();
		}
		$unique_id = wp_generate_password( 8, false, false );
		if ( in_array( $unique_id, $shift_ids ) ) {
			while ( in_array( $unique_id, $shift_ids ) ) {
				$unique_id = wp_generate_password( 8, false, false );
			}
			$shift_ids[] = $unique_id;
		}

		// --- store the unique shift ID ---
		update_option( $option_key, $shift_ids );

		// --- return the unique ID ---
		return $unique_id;
	}

	// --------------------
	// Generate Hashed GUID
	// --------------------
	// 2.3.2: add hashing function for hashed GUID
	function get_hashed_guid( $record_id ) {

		global $wpdb;
		$query = "SELECT guid FROM " . $wpdb->posts . " WHERE ID = %d";
		// 2.5.0: fix to use prepare method on query
		$guid = $wpdb->get_var( $wpdb->prepare( $query, $record_id ) );
		if ( !$guid ) {
			$guid = get_permalink( $record_id );
		}
		$hash = md5( $guid );

		return $hash;
	}

	// --------------
	// Get All Shifts
	// --------------
	// 2.3.0: added get show shifts data grabber
	// 2.3.2: added second argument to get non-split shifts
	// 2.3.3: added time as third argument for ondemand shifts
	public function get_all_shifts( $records, $check_conflicts = true, $split = true, $time = false, $timezone = false ) {

		// --- get channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- get weekdates for checking ---
		$now = $time ? $time : $this->get_now();
		$timezone = $timezone ? $timezone : $this->get_timezone();
		$today = $this->get_time( 'l', $now, $timezone );
		$weekdays = $this->get_schedule_weekdays( $today, $now, $timezone );
		$weekdates = $this->get_schedule_weekdates( $weekdays, $now, $timezone );
		$times = array(
			'now'       => $now,
			'today'     => $today,
			'weekdays'  => $weekdays,
			'weekdates' => $weekdates,
			'timezone'  => $timezone,
		);

		// --- process records into shift data ---
		$all_shifts = $this->get_shift_data( $records, $split, $times );
		$all_shifts = $this->sort_shifts( $all_shifts, $weekdays );

		// --- check shifts for conflicts ---
		if ( $check_conflicts ) {
			$all_shifts = $this->check_shifts( $all_shifts, $times );
		} else {
			// --- return raw data for other shift conflict checking ---
			return $all_shifts;
		}

		// --- sort into day shifts (starting today) ---
		$all_shifts = $this->sort_day_shifts( $all_shifts, $today, $timezone );
		$all_shifts = apply_filters( 'schedule_engine_all_shifts', $all_shifts, $records, $check_conflicts, $split, $time, $timezone, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$all_shifts = apply_filters( $context . '_all_shifts', $all_shifts, $records, $check_conflicts, $split, $time, $timezone, $channel );
		}
		return $all_shifts;
	}

	// --------------
	// Get Shift Data
	// --------------
	public function get_shift_data( $records, $split, $times ) {

		// --- get channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- set time date to short names ---
		$now = $times['now'];
		$today = $times['today'];
		$weekdays = $times['weekdays'];
		$weekdates = $times['weekdates'];
		$timezone = $times['timezone'];

		// --- loop shows to get shifts ---
		// TODO: maybe use filter for setting record shift key?
		// $key = apply_filters( 'schedule_engine_shift_key', false, $context );
		$errors = $all_shifts = array();
		if ( $records && is_array( $records ) && ( count( $records ) > 0 ) ) {
			foreach ( $records as $record ) {

				// --- set short record ID ---
				$id = $record['ID'];

				// --- get record shifts ---
				// note: shifts must be already set
				$shifts = $record['shifts'];
				if ( $this->debug ) {
					$debug = 'Shifts for Record ' . $id . ': ' . print_r( $shifts, true );
					$this->debug_log( $debug );
				}

				if ( $shifts && is_array( $shifts) && ( count( $shifts ) > 0 ) ) {
					foreach ( $shifts as $i => $shift ) {

						// 2.5.18: match shift channel to specified channel
						if ( ( ( ( '' == $channel ) || ( '0' == $channel ) ) && ( !isset( $shift['channel'] ) || ( '' == $shift['channel'] ) || ( '0' == $shift['channel'] ) ) )
						  || ( ( '' != $channel ) && ( isset( $shift['channel'] ) && ( $channel == $shift['channel'] ) ) ) ) {

							// 2.3.3.9: set shift ID to key
							// 2.5.0: use already set key
							if ( !isset( $shift['id'] ) ) {
								$shift['id'] = $i;
							}

							// --- make sure shift has sufficient info ---
							$isdisabled = ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) ? true : false;
							$shift = $this->validate_shift( $shift );

							if ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) {

								// --- if it was not already disabled, add to shift errors ---
								if ( !$isdisabled ) {
									$errors[$id][] = $shift;
								}

							} else {

								// --- shift is valid so continue checking ---
								// 2.3.2: replace strtotime with to_time for timezones
								// 2.3.2: fix to conver to 24 hour format first
								// # midnight calculation
								$day = $shift['day'];
								$thisdate = $weekdates[$day];
								$midnight = $this->to_time( $thisdate . ' 23:59:59', $timezone ) + 1;
								$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
								$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
								$start_time = $this->convert_shift_time( $start );
								$start_time = $this->to_time( $thisdate . ' ' . $start_time, $timezone );
								if ( ( '11:59:59 pm' == $end ) || ( '12:00 am' == $end ) ) {
									// 2.3.2: simplify using existing midnight time
									$end_time = $midnight;
								} else {
									$end_time = $this->convert_shift_time( $end );
									$end_time = $this->to_time( $thisdate . ' ' . $end, $timezone );
								}
								$encore = ( isset( $shift['encore'] ) && ( 'on' == $shift['encore'] ) ) ? true : false;
								$updated = $record['updated'];

								if ( $split ) {

									// --- check if show goes over midnight ---
									if ( ( $end_time > $start_time ) || ( $end_time == $midnight ) ) {

										// --- set the shift time as is ---
										// 2.3.2: added date data
										$all_shifts[$day][$start_time . '.' . $id] = array(
											'ID'       => $id,
											'day'      => $day,
											'date'     => $thisdate,
											'start'    => $start,
											'end'      => $end,
											// 'show'     => $id,
											'encore'   => $encore,
											'split'    => false,
											'updated'  => $updated,
											'shift'    => $shift,
											'override' => false,
										);

									} else {

										// --- split shift for this day ---
										// 2.3.2: added date data
										$all_shifts[$day][$start_time . '.' . $id] = array(
											'ID'       => $id,
											'day'      => $day,
											'date'     => $thisdate,
											'start'    => $start,
											'end'      => '11:59:59 pm', // midnight
											// 'show'     => $id,
											'split'    => true,
											'encore'   => $encore,
											'updated'  => $updated,
											'shift'    => $shift,
											'real_end' => $end,
											'override' => false,
										);

										// --- split shift for next day ---
										// 2.3.2: added date data for next day
										$nextday = $this->get_next_day( $day );
										$nextdate = $weekdates[$nextday];
										// 2.3.2: fix midnight timestamp for sorting
										if ( strtotime( $nextdate ) < strtotime( $thisdate ) ) {
											$midnight = $this->to_time( $nextdate . ' 00:00:00', $timezone );
										}
										$all_shifts[$nextday][$midnight . '.' . $id] = array(
											'ID'         => $id,
											'day'        => $nextday,
											'date'       => $nextdate,
											'start'      => '00:00 am', // midnight
											'end'        => $end,
											// 'show'       => $id,
											'encore'     => $encore,
											'split'      => true,
											'updated'    => $updated,
											'shift'      => $shift,
											'real_start' => $start,
											'override'   => false,
										);
									}

								} else {

									// --- set the shift time as is ---
									// 2.3.2: added for non-split argument
									$all_shifts[$day][$start_time . '.' . $id] = array(
										'ID'       => $id,
										'day'      => $day,
										'date'     => $thisdate,
										'start'    => $start,
										'end'      => $end,
										// 'show'     => $id,
										'encore'   => $encore,
										'split'    => false,
										'updated'  => $updated,
										'shift'    => $shift,
										'override' => false,
									);

								}
							}
						}
					}
				}
			}
		}

		// --- debug point ---
		if ( $this->debug ) {
			$debug = "Raw Shifts: " . print_r( $all_shifts, true ) . PHP_EOL;
			$debug .= "Shift Errors: " . print_r( $errors, true ) . PHP_EOL;
			$this->debug_log( $debug );
		}

		// --- do action to record shift error ---
		do_action( 'schedule_engine_set_shift_errors', $errors, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			do_action( $context . '_set_shift_errors', $errors, $channel );
		}

		// --- filter and return ---
		$all_shifts = apply_filters( 'schedule_engine_shift_data', $all_shifts, $split, $times, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$all_shifts = apply_filters( $context . '_shift_data', $all_shifts, $split, $channel, $times );
		}
		return $all_shifts;
	}

	// -----------
	// Sort Shifts
	// -----------
	public function sort_shifts( $all_shifts, $weekdays ) {

		// --- set channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- sort by start time for each day ---
		// note: all_shifts keys are made unique by combining start time and show ID
		// which allows them to be both be sorted and then checked for conflicts
		if ( count( $all_shifts ) > 0 ) {
			foreach ( $all_shifts as $day => $shifts ) {
				ksort( $shifts );
				$all_shifts[$day] = $shifts;
			}
		}

		// --- reorder by weekdays ---
		// 2.3.2: added for passing to shift checker
		$sorted_shifts = array();
		foreach ( $weekdays as $weekday ) {
			if ( isset( $all_shifts[$weekday] ) ) {
				$sorted_shifts[$weekday] = $all_shifts[$weekday];
			}
		}

		// --- debug point ---
		if ( $this->debug ) {
			$debug = "Sorted Shifts: " . print_r( $sorted_shifts, true );
			$this->debug_log( $debug );
		}
		
		// --- filter and return ---
		$sorted_shifts = apply_filters( 'schedule_engine_sorted_shifts', $sorted_shifts, $all_shifts, $weekdays, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			apply_filters( $context . '_sorted_shifts', $sorted_shifts, $all_shifts, $weekdays, $channel, $context );
		}
		return $sorted_shifts;
	}

	// ---------------
	// Sort Day Shifts
	// ---------------
	public function sort_day_shifts( $all_shifts, $today = false, $timezone = false ) {
		
		// --- set channel and context ---
		$channel = $this->channel;
		$context = $this->context;
		
		if ( !$today ) {
			$now = $this->get_now();
			$timezone = $this->get_timezone();
			$today = $this->get_time( 'l', $now, $timezone );
		}

		// --- shuffle shift days so today is first day ---
		// 2.3.2: use get time function for day with timezone
		// 2.5.0: removed get today here as already set
		$day_shifts = array();
		for ( $i = 0; $i < 7; $i ++ ) {
			// 2.5.0: shorten conditional logic to one line
			$day = ( 0 === $i ) ? $today : $this->get_next_day( $day );
			if ( isset( $all_shifts[$day] ) ) {
				$day_shifts[$day] = $all_shifts[$day];
			}
		}

		// --- filter and return ---
		// 2.3.3.9: changed conflicting filter name from radio_station_show_shifts
		$day_shifts = apply_filters( 'schedule_engine_day_shifts', $day_shifts, $all_shifts, $today, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$day_shifts = apply_filters( $context . '_day_shifts', $day_shifts, $all_shifts, $today, $channel );
		}
		return $day_shifts;
	}

	// --------------------------
	// Process Schedule Overrides
	// --------------------------
	// 2.3.0: added get schedule overrides data grabber
	public function process_overrides( $overrides, $start_date = false, $end_date = false, $timezone = false ) {

		// --- get channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		if ( !$timezone ) {
			$timezone = $this->get_timezone();
		}

		// 2.5.18: add missing range time definitions
		if ( $start_date ) {
			$range_start_time = $this->to_time( $start_date, $timezone );
		}
		if ( $end_date ) {
			$range_end_time = $this->to_time( $end_date, $timezone );
		}

		// --- loop overrides and get data ---
		$override_list = array();
		foreach ( $overrides as $i => $override ) {

			// echo '<span style="display:none;">Override: ' . print_r( $override, true ) . '</span>';

			$override_shifts = $override['shifts'];
			$show = $override['show'];

			if ( $override_shifts && is_array( $override_shifts ) && ( count( $override_shifts ) > 0 ) )  {

				// --- loop override shifts ---
				// 2.3.3.9: loop to allow for multiple overrides
				foreach ( $override_shifts as $j => $data ) {

					if ( $this->debug ) {
						$debug = 'Override Data: ' . print_r( $data, true ) . PHP_EOL;
						$this->debug_log( $debug );
					}

					// 2.3.3.9: ignore disabled overrides
					if ( !isset( $data['disabled'] ) || ( 'yes' != $data['disabled'] ) ) {

						$date = $data['date'];
						if ( '' != $date ) {

							// 2.3.2: replace strtotime with to_time for timezones
							$date_time = $this->to_time( $date, $timezone );
							$inrange = true;

							// --- check if in specified date range ---
							if ( ( isset( $range_start_time ) && ( $date_time < $range_start_time ) )
							  || ( isset( $range_end_time ) && ( $date_time > $range_end_time ) ) ) {
								$inrange = false;
							}

							// --- add the override data ---
							if ( $inrange ) {

								// 2.3.2: get day from date directly
								// $thisday = date( 'l', $date_time );
								// 2.5.6: use get_time method instead of date()
								$day = $this->get_time( 'l', strtotime( $date ) );

								// 2.3.2: replace strtotime with to_time for timezones
								// 2.3.2: fix to conver to 24 hour format first
								$start = $data['start_hour'] . ':' . $data['start_min'] . ' ' . $data['start_meridian'];
								$end = $data['end_hour'] . ':' . $data['end_min'] . ' ' . $data['end_meridian'];
								$start_time = $this->convert_shift_time( $start );
								$end_time = $this->convert_shift_time( $end );
								$override_start_time = $this->to_time( $date . ' ' . $start_time, $timezone );
								$override_end_time = $this->to_time( $date . ' ' . $end_time, $timezone );
								// 2.3.2: fix for overrides ending at midnight
								// 2.3.3.9: fix to use standardized operator check
								if ( $override_end_time <= $override_start_time ) {
									$override_end_time = $override_end_time + ( 24 * 60 * 60 );
								}
								// TODO: allow for multiday overrides ?
								/* if ( isset( $data['multiday'] ) && ( 'yes' == $data['multiday'] ) ) {
									if ( isset( $data['enddate'] ) && ( '' != $data['enddate'] ) ) {

									}
								} */
								$recurs = isset( $data['recurs'] ) ? $data['recurs'] : '';

								if ( $override_start_time < $override_end_time ) {

									// --- add the override as is ---
									$override_data = array(
										'ID'       => $show['id'],
										'show'     => $show,
										'id'       => $data['id'],
										'name'     => $show['title'],
										'slug'     => $show['slug'],
										'date'     => $date,
										'day'      => $day,
										'start'    => $start,
										'end'      => $end,
										'override' => $override['ID'],
										'recurs'   => $recurs,
										'url'      => get_permalink( $show['id'] ),
										'split'    => false,
									);
									// 2.3.3.7: set array order by start time
									$override_list[$date][$override_start_time] = $override_data;

								} else {

									// --- split the override overnight ---
									$override_data = array(
										'ID'       => $show['id'],
										'show'     => $show,
										'id'       => $data['id'],
										'name'     => $show['title'],
										'slug'     => $show['slug'],
										'date'     => $date,
										'day'      => $day,
										'start'    => $start,
										'end'      => '11:59:59 pm',
										'real_end' => $end,
										'override' => $override['ID'],
										'recurs'   => $recurs,
										'url'      => get_permalink( $show['id'] ),
										'split'    => true,
									);
									// 2.3.3.7: set array order by start time
									$override_list[$date][$override_start_time] = $override_data;

									// --- set the next day split shift ---
									// note: these should not wrap around to start of week
									// 2.3.2: use get next date/day functions
									// $nextday = date( 'l', $next_date_time );
									// $nextdate = date( 'Y-m-d', $next_date_time );
									// 2.5.6: fixed to use internal class methods
									$nextdate = $this->get_next_date( $date );
									$nextday = $this->get_next_day( $day );

									$override_data = array(
										'ID'         => $show['id'],
										'show'       => $show,
										'id'         => $data['id'],
										'name'       => $show['title'],
										'slug'       => $show['slug'],
										'date'       => $nextdate,
										'day'        => $nextday,
										'real_start' => $start,
										'start'      => '00:00 am',
										'end'        => $end,
										'override'   => $override['ID'],
										'recurs'     => $recurs,
										'url'        => get_permalink( $show['id'] ),
										'split'      => true,
									);
									// 2.3.3.7: set array order by start time
									$override_list[$nextdate][$override_start_time] = $override_data;
								}
							}
						}
					}
				}
			}
		}

		// 2.3.3.7: reorder overrides by sequential times
		if ( count( $override_list ) > 0 ) {
			foreach ( $override_list as $day => $overrides ) {
				ksort( $overrides );
				$override_list[$day] = $overrides;
			}
		}

		// --- filter and return ---
		$override_list = apply_filters( 'schedule_engine_all_overrides', $override_list, $overrides, $start_date, $end_date, $timezone, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$override_list = apply_filters( $context . '_all_overrides', $override_list, $overrides, $start_date, $end_date, $timezone, $channel );
		}
		return $override_list;
	}

	// --------------------
	// Get Current Schedule
	// --------------------
	// 2.3.2: added optional time argument
	// 2.3.3.5: added optional weekstart argument
	public function get_current_schedule( $records, $overrides, $time = false, $weekstart = false, $timezone = false ) {

		// --- get channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- get all show shifts ---
		// 2.3.3: also pass time to get show_shifts function
		$show_shifts = $this->get_all_shifts( $records, true, true, $time, $timezone );

		// --- get weekdates ---
		// 2.5.6: ensure time valus is separate to now value
		$now = $this->get_now();
		$time = $time ? $time : $now;
		$timezone = $timezone ? $timezone : $this->get_timezone();
		// $today = $this->get_time( 'l', $now, $timezone );
		// 2.3.3.5: add passthrough of optional week start argument
		$weekdays = $this->get_schedule_weekdays( $weekstart, $time, $timezone );
		$weekdates = $this->get_schedule_weekdates( $weekdays, $time, $timezone );
		// 2.5.0: set an array of times data
		$times = array(
			'time'      => $time,
			'timezone'  => $timezone,
			'weekdays'  => $weekdays,
			'weekdates' => $weekdates,
		);

		// 2.3.1: add empty keys to ensure overrides are checked
		foreach ( $weekdays as $weekday ) {
			if ( !isset( $show_shifts[$weekday] ) ) {
				$show_shifts[$weekday] = array();
			}
		}

		// --- debug point ---
		if ( $this->debug ) {
			$debug = 'Shifts: ' . print_r( $show_shifts, true );
			$this->debug_log( $debug );
		}

		// --- get show overrides ---
		// (from 12am this morning, for one week ahead and back)
		// 2.3.1: get start and end dates from weekdays
		// 2.3.2: use get time function with timezone
		// 2.3.3.9: pass second argument as time may not be now
		$start_date = $weekdates[$weekdays[0]];
		$end_date = $weekdates[$weekdays[6]];
		$overrides = apply_filters( 'schedule_engine_overrides', array(), $times, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$overrides = apply_filters( $context . '_overrides', $overrides, $times, $channel );
		}
		$overrides = $this->process_overrides( $overrides, $start_date, $end_date, $timezone );

		// --- debug point ---
		if ( $this->debug ) {
			$debug = "Time: " . $time . " - Date: " . date( 'd-m-Y', $time ) . PHP_EOL;
			if ( $now != $time ) {
				$debug .= "Now: " . $now . " - Date: " . date( 'd-m-Y', $now ) . PHP_EOL;
			}
			$debug .= "Week Start Date: " . $start_date . " - Week End Date: " . $end_date . PHP_EOL;
			$debug .= "Schedule Overrides: " . print_r( $overrides, true ) . PHP_EOL;
			$this->debug_log( $debug );
		}

		$show_shifts = $this->combine_shifts( $show_shifts, $overrides, $times );

		// --- filter and return ---
		// 2.3.2: added time argument to filter
		// 2.3.3: apply filter only once
		$show_shifts = apply_filters( 'schedule_engine_current_schedule', $show_shifts, $time, $weekstart, $timezone, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$show_shifts = apply_filters( $context . '_current_schedule', $show_shifts, $time, $weekstart, $timezone, $channel );
		}

		// --- cache expiry time ---
		// 2.3.2: set temporary transient if time is specified
		// 2.3.3: also set global data for current schedule
		// 2.5.6: use global expiry time fr\ir class
		$expires = $this->expires;

		// 2.5.0: do set current schedule action
		do_action( 'schedule_engine_set_current_schedule', $show_shifts, $expires, $time, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			do_action( $context . '_set_current_schedule', $show_shifts, $expires, $time, $channel );
		}

		// --- process shifts ---
		$show_shifts = $this->process_shifts( $show_shifts, $times );

		return $show_shifts;
	}

	// --------------
	// Combine Shifts
	// --------------
	public function combine_shifts( $show_shifts, $override_list, $times ) {

		// --- get channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- set variables from times ---
		$time = $times['time'];
		$timezone = $times['timezone'];
		$weekdays = $times['weekdays'];
		$weekdates = $times['weekdates'];

		// --- apply overrides to the schedule ---
		$debugday = 'Monday';
		if ( isset( $_REQUEST['debug-day'] ) ) {
			$debugday = sanitize_text_field( $_REQUEST['debug-day'] );
		}
		// $done_overrides = array();
		if ( $override_list && is_array( $override_list ) && ( count( $override_list ) > 0 ) ) {
			foreach ( $show_shifts as $day => $shifts ) {

				$date = $weekdates[$day];
				if ( $this->debug ) {
					$debug = "Override Date: " . $date;
					$this->debug_log( $debug );
				}

				// 2.3.2: reset overrides for loop
				$overrides = array();
				if ( isset( $override_list[$date] ) ) {

					$overrides = $override_list[$date];
					if ( $this->debug ) {
						$debug = "Overrides for " . $day . ": " . print_r( $overrides, true );
						$this->debug_log( $debug );
					}

					// --- maybe reloop to insert any overrides before shows ---
					if ( count( $overrides ) > 0 ) {
						foreach ( $overrides as $i => $override ) {

							if ( $date == $override['date'] ) {

								// 2.3.1: added check if override already done
								// 2.3.2: replace strtotime with to_time for timezones
								// 2.3.2: fix to convert to 24 hour format first
								// 2.3.3.7: remove check if override already done from here
								$override_start = $this->convert_shift_time( $override['start'] );
								$override_end = $this->convert_shift_time( $override['end'] );
								$override_start_time = $this->to_time( $date . ' ' . $override_start, $timezone );
								$override_end_time = $this->to_time( $date . ' ' . $override_end, $timezone );
								if ( isset( $override['split'] ) && $override['split'] && ( '11:59:59 pm' == $override['end'] ) ) {
									// 2.3.3.8: fix to add 60 seconds instead of 1
									$override_end_time = $override_end_time + 60;
								}
								// 2.3.2: fix for non-split overrides ending on midnight
								// 2.3.3.9: added or equals to operator
								if ( $override_end_time <= $override_start_time ) {
									$override_end_time = $override_end_time + ( 24 * 60 * 60 );
								}

								// --- check for overlapped shift (if any) ---
								// 2.3.1 added check for shift count
								if ( count( $shifts ) > 0 ) {
									// 2.3.3.7: change shifts variable in loop not just show_shifts
									foreach ( $shifts as $start => $shift ) {

										// 2.3.2: replace strtotime with to_time for timezones
										// 2.3.2: fix to convert to 24 hour format first
										$shift_start = $this->convert_shift_time( $shift['start'] );
										$shift_end = $this->convert_shift_time( $shift['end'] );
										$start_time = $this->to_time( $date . ' ' . $shift_start, $timezone );
										$end_time = $this->to_time( $date . ' ' . $shift_end, $timezone );
										if ( isset( $shift['split'] ) && $shift['split'] && ( '11:59:59 pm' == $shift['end'] ) ) {
											// 2.3.3.8: fix to add 60 seconds instead of 1
											$end_time = $end_time + 60;
										}
										// 2.3.2: fix for non-split shifts ending on midnight
										// 2.3.3.9: added or equals to operator
										if ( $end_time <= $start_time ) {
											$end_time = $end_time + ( 24 * 60 * 60 );
										}

										if ( $day == $debugday ) {
											// 2.4.0.6: fix to undefined variable warning
											if ( !isset( $debugshifts ) ) {
												$debugshifts = '';
											}
											$debugshifts .= $day . ' Show from ' . $shift['start'] . ': ' . $start_time . ' to ' . $shift['end'] . ': ' . $end_time . PHP_EOL;
											$debugshifts .= $day . ' Override from ' . $override['start'] . ': ' . $override_start_time . ' to ' . $override['end'] . ': ' . $override_end_time . PHP_EOL;
										}

										// --- check if the override starts earlier than shift ---
										if ( $override_start_time < $start_time ) {

											// --- check when the shift ends ---
											if ( ( $override_end_time > $end_time )
												|| ( $override_end_time == $end_time ) ) {
												// --- overlaps so remove shift ---
												if ( $day == $debugday ) {
													$debugshifts .= "Removed Shift: " . print_r( $shift, true ) . PHP_EOL;
												}
												unset( $show_shifts[$day][$start] );
												unset( $shifts[$start] );
											} elseif ( $override_end_time > $start_time ) {
												// --- add trimmed shift remainder ---
												if ( $day == $debugday ) {
													$debugshifts .= "Trimmed Start of Shift to " . $override['end'] . ": " . print_r( $shift, true ) . PHP_EOL;
												}
												unset( $show_shifts[$day][$start] );
												unset( $shifts[$start] );
												$shift['start'] = $override['end'];
												$shift['trimmed'] = 'start';
												$shift['override'] = '1';
												$shifts[$override['end']] = $shift;
												$show_shifts[$day] = $shifts;
											}

											// --- add the override if not already added ---
											// 2.3.3.8: removed adding of overrides here
											/* if ( !in_array( $override['date'] . '--' . $i, $done_overrides ) ) {
												$done_overrides[] = $override['date'] . '--' . $i;
												if ( $day == $debugday ) {
													$debugshifts .= "Added Override: " . print_r( $override, true ) . PHP_EOL;
												}
												$shifts[$override['start']] = $override;
												$show_shifts[$day] = $shifts;
											} */

										} elseif ( $override_start_time == $start_time ) {

											// --- same start so overwrite the existing shift ---
											// 2.3.1: set override done instead of unsetting override
											// 2.3.3.7: remove check if override already done
											// $done_overrides[] = $date . '--' . $i;
											if ( $day == $debugday ) {
												$debugshifts .= "Replaced Shift with Override: " . print_r( $show_shifts[$day][$start], true ) . PHP_EOL;
											}
											$shifts[$start] = $override;
											$show_shifts[$day] = $shifts;

											// --- check if there is remainder of existing show ---
											if ( $override_end_time < $end_time ) {
												$shift['start'] = $override['end'];
												$shift['trimmed'] = 'start';
												$shift['override'] = '1';
												$shifts[$override['end']] = $shift;
												$show_shifts[$day] = $shifts;
												if ( $day == $debugday ) {
													$debugshifts .= "And trimmed Shift Start to " . $override['end'] . PHP_EOL;
												}
											}
											// elseif ( $override_end_time == $end_time ) {
												// --- remove exact override ---
												// do nothing, already overridden
											// }

										} elseif ( ( $override_start_time > $start_time )
												&& ( $override_start_time < $end_time ) ) {

											$end = $shift['end'];

											// --- partial shift before override ---
											if ( $day == $debugday ) {
												$debugshifts .= "Trimmed Shift End to " . $override['start'] . ": " . print_r( $shift, true ) . PHP_EOL;
											}
											$shift['start'] = $start;
											$shift['end'] = $override['start'];
											$shift['trimmed'] = 'end';
											$shift['override'] = '1';
											$shifts[$start] = $shift;
											$show_shifts[$day] = $shifts;

											// --- add the override ---
											$show_shifts[$day][$override['start']] = $override;
											// 2.3.1: track done instead of unsetting
											// 2.3.3.7: remove check if override already done here
											// $done_overrides[] = $date . '--' . $i;

											// --- partial shift after override ----
											if ( $override_end_time < $end_time ) {
												if ( $day == $debugday ) {
													$debugshifts .= "And added partial Shift after " . $override['end'] . ": " . print_r( $shift, true ) . PHP_EOL;
												}
												$shift['start'] = $override['end'];
												$shift['end'] = $end;
												$shift['trimmed'] = 'start';
												$shift['override'] = '1';
												$shifts[$override['end']] = $shift;
												$show_shifts[$day] = $shifts;
											}
										}
									}
								}
							}
						}
					}
				}

				// --- add directly any remaining overrides ---
				// 2.3.1: fix to include standalone overrides on days
				// 2.3.3.8: moved override adding to fix shift order
				if ( count( $overrides ) > 0 ) {
					foreach ( $overrides as $i => $override ) {
						if ( $date == $override['date'] ) {
							// 2.3.3.7: remove check if override already done
							// if ( !in_array( $date . '--' . $i, $done_overrides ) ) {
								// $done_overrides[] = $date . '--' . $i;
								$overrides['override'] = '1';
								$show_shifts[$day][$override['start']] = $override;
								if ( $day == $debugday ) {
									$debugshifts .= "Added Override: " . print_r( $override, true ) . PHP_EOL;
								}
							// }
						}
					}
				}

				// --- sort the shifts using 24 hour time ---
				$shifts = $show_shifts[$day];
				if ( count( $shifts ) > 0 ) {
					// 2.3.2: fix to clear shift keys between days
					$new_shifts = $shift_keys = array();
					$keys = array_keys( $shifts );
					foreach ( $keys as $i => $key ) {
						$converted = $this->convert_shift_time( $key, 24 );
						unset( $keys[$i] );
						$keys[$key] = $shift_keys[$key] = $converted;
					}
					sort( $shift_keys );
					foreach ( $shift_keys as $shift_key ) {
						if ( in_array( $shift_key, $keys ) ) {
							$key = array_search( $shift_key, $keys );
							$new_shifts[$key] = $shifts[$key];
						}
					}
					$shifts = $show_shifts[$day] = $new_shifts;

					if ( $this->debug ) {
						if ( isset( $debugshifts ) ) {
							$this->debug_log( "Day Debug: " . $debugshifts );
						}
						$debug = "Shift Keys: " . print_r( $keys, true ) . PHP_EOL;
						$debug .= "Sorted Keys: " . print_r( $shift_keys, true ) . PHP_EOL;
						$debug .= "Sorted Shifts: " . print_r( $new_shifts, true ) . PHP_EOL;
						$this->debug_log( $debug );
					}

				}

				// 2.5.6: remove assignment repetition
				// $shifts = $show_shifts[$day];
				// ksort( $shifts );
				if ( $this->debug ) {
					$debug = "New Day Shifts: " . print_r( $shifts, true );
					$this->debug_log( $debug );
				}
			}

		}

		// 2.5.18: added duration field
		foreach ( $show_shifts as $day => $shifts ) {
			foreach ( $shifts as $i => $shift ) {
				$date = $weekdates[$day];				
				$start_time = $this->to_time( $date . ' ' . $shift['start'], $timezone );
				$end_time = $this->to_time( $date . ' ' . $shift['end'], $timezone );
				if ( $end_time <= $start_time ) {
					$end_time = $end_time + ( 24 * 60 * 60 );
				}			
				$duration = $end_time - $start_time;
				$show_shifts[$day][$i]['duration'] = $duration;
			}
		}

		if ( $this->debug ) {
			$debug = "Combined Schedule: " . print_r( $show_shifts, true );
			$this->debug_log( $debug );
		}
		
		return $show_shifts;
	}

	// --------------
	// Process Shifts
	// --------------
	public function process_shifts( $show_shifts, $times ) {

		// --- set channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- set variables from times ---
		$now = $this->now;
		$time = $times['time'];
		$timezone = $times['timezone'];
		$weekdays = $times['weekdays'];
		$weekdates = $times['weekdates'];

		// --- loop all shifts to check current ---
		$prev_shift = $set_prev_shift = $prev_shift_end = false;
		foreach ( $show_shifts as $day => $shifts ) {

			// 2.3.1: added check for shift count
			if ( count( $shifts ) > 0 ) {

				foreach ( $shifts as $start => $shift ) {

					// --- check if shift is an override ---
					if ( isset( $shift['override'] ) && $shift['override'] ) {

						// ---- add the override data ---
						// 2.5.6: use apply_filters instead of prefixed functions
						$override = apply_filters( 'schedule_engine_schedule_override_data_meta', $shift['override'], $shift['id'], $context );
						if ( ( '' != $context ) && is_string( $context ) ) {
							$override = apply_filters( $context . '_schedule_override_data_meta', $shift['override'], $shift['id'] );
						}
						$shift['show'] = $show_shifts[$day][$start]['show'] = $override;

					} else {
				
						// --- add show data back to shift ---
						// 2.5.6: use apply_filters instead of prefixed functions
						// 2.5.18: fix fo possibly missing show ID
						$show_id = isset( $shift['ID'] ) ? $shift['ID'] : '';
						$show = apply_filters( 'schedule_engine_schedule_show_data_meta', $show_id, $shift['id'], $context );
						if ( ( '' != $context ) && is_string( $context ) ) {
							$show = apply_filters( $context . '_schedule_show_data_meta', $show_id, $shift['id'] );
						}
						unset( $show['schedule'] );
						$shift['show'] = $show_shifts[$day][$start]['show'] = $show;
					}

					if ( !isset( $current_show ) ) {

						// --- get this shift start and end times ---
						// 2.3.2: replace strtotime with to_time for timezones
						// 2.3.2: fix to convert to 24 hour format first
						$shift_start = $this->convert_shift_time( $shift['start'] );
						$shift_end = $this->convert_shift_time( $shift['end'] );
						$shift_start_time = $this->to_time( $weekdates[$day] . ' ' . $shift_start, $timezone );
						$shift_end_time = $this->to_time( $weekdates[$day] . ' ' . $shift_end, $timezone );

						// if ( isset( $shift['split'] ) && $shift['split'] && isset( $shift['real_end'] ) {
						//	$nextdate = $this->get_time( 'date', $shift_end_time + ( 23 * 60 * 60 ) );
						//	$shift_end = $nextdate[$day] . ' ' . $shift['real_end'];
						// }

						// - adjust for shifts ending past midnight -
						// 2.3.3.9: added or equals to operator
						if ( $shift_end_time <=  $shift_start_time ) {
							$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
						}

						// --- check if this is the currently scheduled show ---
						if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {

							if ( isset( $maybe_next_show ) ) {
								unset( $maybe_next_show );
							}
							$shift['day'] = $day;
							$current_show = $shift;

							// 2.3.3: set current show to global data
							// 2.3.4: set previous show shift to global and transient
							// 2.3.3.8: move expires declaration earlier
							// 2.5.6: fallback to global expiration time
							$expires = $shift_end_time - $now - 1;
							if ( $expires > $this->expires ) {
								$expires = $this->expires;
							}
							
							// 2.5.0: do set current shift action
							do_action( 'schedule_engine_set_current_shift', $current_show, $expires, $time, $channel, $context );
							if ( ( '' != $context ) && is_string( $context ) ) {
								do_action( $context . '_set_current_shift', $current_show, $expires, $time, $channel );
							}

						} elseif ( $now > $shift_end_time ) {

							// 2.3.2: set previous shift flag
							$set_prev_shift = true;

						} elseif ( ( $now < $shift_start_time ) && !isset( $maybe_next_show ) ) {

							// 2.3.2: set maybe next show
							$maybe_next_show = $shift;

						}

						// --- debug point ---
						if ( $this->debug ) {
							$debug = 'Time: ' . $time . ' - Date: ' . $this->get_time( 'm-d H:i:s', $time ) . PHP_EOL;
							if ( $time != $now ) {
								$debug .= 'Now: ' . $now . ' - Date: ' . $this->get_time( 'm-d H:i:s', $now ) . PHP_EOL;
							}
							$debug .= 'Shift Start: ' . $shift_start . ' (' . $shift_start_time . ')' . PHP_EOL;
							$debug .= 'Shift End: ' . $shift_end . ' (' . $shift_end_time . ')' . PHP_EOL;
							if ( isset ( $current_show ) ) {
								$debug .= '[Current Shift] ' . print_r( $current_show, true ) . PHP_EOL;
							}
							if ( $now >= $shift_start_time ) {$debug .= "!A!";}
							if ( $now < $shift_end_time ) {$debug .= "!B!";}
							$this->debug_log( $debug );
						}

					} elseif ( isset( $current_show['split'] ) && $current_show['split'] ) {

						// --- skip second part of split shift for current shift ---
						// (so that it is not set as the next show)
						unset( $current_show['split'] );

					}

					// 2.3.2: change to logic to allow for no current show found
					if ( !isset( $next_show ) ) {

						// --- get shift times ---
						// 2.3.2: replace strtotime with to_time for timezones
						// 2.3.2: fix to convert to 24 hour format first
						// 2.5.6: fix start_time and end_time to shift_start and shift_end
						$shift_start = $this->convert_shift_time( $shift['start'] );
						$shift_end = $this->convert_shift_time( $shift['end'] );
						$shift_start_time = $this->to_time( $weekdates[$day] . ' ' . $shift_start, $timezone );
						$shift_end_time = $this->to_time( $weekdates[$day] . ' ' . $shift_end, $timezone );
						// 2.3.3.9: added or equals to operator
						if ( $shift_end_time <= $shift_start_time ) {
							$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
						}

						if ( isset( $current_show ) || ( $prev_shift_end && ( $now > $prev_shift_end ) && ( $now < $shift_start_time ) ) ) {

							// --- set next show ---
							// 2.3.2: set date for widget
							$next_show['date'] = $weekdates[$day];
							$next_show = $shift;

						}
					}

					// 2.3.2: maybe set previous shift end value
					if ( $set_prev_shift ) {
						$prev_shift_end = $shift_end_time;
					}

					// 2.3.4: set previous shift value
					$prev_shift = $shift;

				}
			}
		}

		// --- maybe set next show transient ---
		// 2.3.2: check for (possibly first) next show found
		if ( !isset( $next_show ) && isset( $maybe_next_show ) ) {
			$next_show = $maybe_next_show;
		}

		// 2.5.6: fix to reset time flag if same as now
		if ( $time == $now ) {
			$time = false;
		}
			
		if ( isset( $next_show ) ) {

			// 2.3.2: recombine split shift end times
			$shift = $next_show;
			if ( isset( $shift['split'] ) && $shift['split'] && isset( $shift['real_end'] ) ) {
				$next_show['end'] = $shift['real_end'];
				unset( $next_show['split'] );
			}

			// 2.3.2: added check that expires is set
			$next_expires = $shift_end_time - $now - 1;
			if ( isset( $expires ) && ( $next_expires > ( $expires + $this->expires ) ) ) {
				$next_expires = $expires + $this->expires;
			}

			// 2.5.0: do next shift record action
			// echo '<span style="display:none;">&&&'; var_dump( $time ); var_dump( $next_expires ); var_dump( $next_show ); echo '</span>';
			do_action( 'schedule_engine_set_next_shift', $next_show, $next_expires, $time, $channel, $context );
			if ( ( '' != $context ) && is_string( $context ) ) {
				do_action( $context . '_set_next_shift', $next_show, $next_expires, $time, $channel );
			}

		}

		if ( $this->debug ) {
			if ( !isset( $current_show ) ) {
				$debug = 'Current Show Not Found.';
			} else {
				$debug = 'Current Show: ' . print_r( $current_show, true );
			}
			$this->debug_log( $debug );
		}

		// --- get next show if we did not find one ---
		if ( !isset( $next_show ) ) {

			if ( $this->debug ) {
				$debug = 'No Next Show Found. Rechecking...';
				$this->debug_log( $debug );
			}

			// --- fallback to using get next shift function ---
			// 2.3.2: added time argument to next shows retrieval
			// 2.3.2: set next show transient within next shows function
			$next_shows = $this->get_next_shift( $show_shifts, $time, $timezone );

		}

		// --- debug point ---
		if ( $this->debug ) {
			$debug = '';
			if ( isset( $current_show ) ) {
				$debug .= "Current Show: " . print_r( $current_show, true ) . PHP_EOL;
			}
			if ( isset( $next_show ) ) {
				$debug .= "Next Show: " . print_r( $next_show, true ) . PHP_EOL;
			}
			$next_shows = $this->get_next_shifts( 5, $show_shifts, $time, $timezone );
			$debug .= "Next 5 Shows: " . print_r( $next_shows, true ) . PHP_EOL;
			$this->debug_log( $debug );
		}

		return $show_shifts;
	}

	// ------------------
	// Get Current Shifts
	// ------------------
	// 2.3.0: added new get current show function
	// 2.3.2: added optional time argument
	public function get_current_shifts( $schedule_shifts = false, $time = false, $timezone = false ) {

		// --- set channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		$current_shift = $previous_shift = $next_shift = false;

		// --- get all scheduled shifts ---
		// 2.5.6: disabled as get_all_shifts args incorrect
		/* if ( !$schedule_shifts ) {
			if ( !$time ) {
				$schedule_shifts = $this->get_all_shifts();
			} else {
				$schedule_shifts = $this->get_all_shifts( $time );
			}
		} */

		// --- get current time ---
		$now = $time ? $time : $this->get_now();

		// --- get schedule for time ---
		// 2.3.3: use weekday name instead of number (w)
		// 2.3.3: add fix to start from previous day
		$today = $this->get_time( 'l', $now, $timezone );
		$yesterday = $this->get_previous_day( $today );
		$weekdays = $this->get_schedule_weekdays( $yesterday );
		$weekdates = $this->get_schedule_weekdates( $weekdays, $now, $timezone );

		if ( $this->debug ) {
			$debug = 'Finding Current Show from ' . $yesterday . PHP_EOL;
			$debug .= 'Weekdays: ' . print_r( $weekdays, true ) . PHP_EOL;
			$debug .= 'Weekdates: ' . print_r( $weekdates, true ) . PHP_EOL;
			$this->debug_log( $debug );
		}

		// --- loop shifts to get current show ---
		$current_split = $prev_show = false;
		foreach ( $weekdays as $day ) {
			if ( isset( $schedule_shifts[$day] ) ) {
				$shifts = $schedule_shifts[$day];
				foreach ( $shifts as $start => $shift ) {

					// --- get this shift start and end times ---
					$shift_start = $this->convert_shift_time( $shift['start'] );
					$shift_end = $this->convert_shift_time( $shift['end'] );
					$shift_start_time = $this->to_time( $weekdates[$day] . ' ' . $shift_start, $timezone );
					$shift_end_time = $this->to_time( $weekdates[$day] . ' ' . $shift_end, $timezone );
					// 2.3.3: fix for shifts split over midnight
					// 2.3.3.9: added or equals to operator
					if ( $shift_end_time <= $shift_start_time ) {
						$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
					}

					if ( $this->debug ) {
						$debug = 'Now: ' . $now . ' - Shift Start: ' . $shift_start_time . ' - Shift End: ' . $shift_end_time . PHP_EOL;
						$debug .= 'Shift: ' . print_r( $shift, true ) . PHP_EOL;
						$this->debug_log( $debug );
					}

					// --- set current show ---
					// 2.3.3: get current show directly and remove transient
					// 2.4.0.6: fix to add equal to operator for start time
					if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {

						if ( $this->debug ) {
							$debug = '^^^ Current ^^^';
							$this->debug_log( $debug );
						}
						// --- recombine possible split shift to set current show ---
						$current_shift = $shift;

						// 2.3.4: also set previous shift data
						// 2.5.6: added isset check for prev_shift
						if ( isset( $prev_shift ) && $prev_shift ) {
							
							$previous_shift = $prev_shift;
							$expires = $shift_end_time - $now - 1;
							
							do_action( 'schedule_engine_previous_shift', $previous_shift, $expires, $time, $channel, $context );
							if ( ( '' != $context ) && is_string( $context ) ) {
								do_action( $context . '_previous_shift', $previous_shift, $expires, $time, $channel );
							}

						}

					}

					// 2.3.4: store previous shift
					$prev_shift = $shift;
				}
			}
		}

		// --- filter current show ---
		// 2.3.2: added time argument to filter
		$current_shifts = array(
			'current'	=> $current_shift,
			'previous'  => $previous_shift,
			// 'next'		=> $next_shift,
		);
		$current_shifts = apply_filters( 'schedule_engine_current_shifts', $current_shifts, $time, $schedule_shifts, $timezone );
		return $current_shifts;
	}

	// -----------------
	// Get Current Shift
	// -----------------
	public function get_current_shift( $schedule_shifts = false, $time = false, $timezone = false ) {
		// TODO: check for cached current shift?
		$current_shifts = $this->get_current_shifts( $schedule_shifts, $time, $timezone );
		$current_shift = $current_shifts['current'];
		return $current_shift;
	}

	// ------------------
	// Get Previous Shift
	// ------------------
	public function get_previous_shift( $schedule_shifts = false, $time = false, $timezone = false ) {
		
		// TODO: check for cached previous shift?
		// 2.5.6: disabled as get_all_shifts args incorrect
		/* if ( !$schedule_shifts ) {
			if ( !$time ) {
				$schedule_shifts = $this->get_all_shifts();
			} else {
				$schedule_shifts = $this->get_all_shifts( $time );
			}
		} */

		$current_shifts = $this->get_current_shifts( $schedule_shifts, $time, $timezone );
		$previous_shift = $current_shifts['previous'];
		return $previous_shift;
	}

	// --------------
	// Get Next Shift
	// --------------
	public function get_next_shift( $schedule_shifts = false, $time = false, $timezone = false ) {

		// 2.5.6: disabled as get_all_shifts args incorrect
		/* if ( !$schedule_shifts ) {
			if ( !$time ) {
				$schedule_shifts = $this->get_all_shifts();
			} else {
				$schedule_shifts = $this->get_all_shifts( $time );
			}
		} */

		// $current_shifts = $this->get_current_shifts( $schedule_shifts, $time, $timezone );
		// $next_shift = $current_shifts['next'];
		$next_shifts = $this->get_next_shifts( 1, $schedule_shifts, $time, $timezone );
		$next_shift = ( is_array( $next_shifts ) && ( count( $next_shifts ) > 0 ) ) ? $next_shifts[0] : false;
		return $next_shift;
	}

	// ------------------
	// Get Next Scheduled
	// ------------------
	// 2.3.0: added new get next shows function
	// 2.3.2: added optional time argument
	public function get_next_shifts( $limit = 3, $scheduled_shifts = false, $time = false, $timezone = false ) {

		// --- get all show shifts ---
		// TODO: check this as may be needed to prevent an endless loop?
		// 2.5.6: disabled as get_all_shifts args incorrect
		/* if ( !$scheduled_shifts ) {
			if ( !$time ) {
				$scheduled_shifts = $this->get_all_shifts();
			} else {
				$scheduled_shifts = $this->get_all_shifts( $time );
			}
		} */

		// --- loop (remaining) shifts to add show data ---
		$next_shows = array();
		// 2.3.2: maybe set provided time as now
		$now = $time ? $time : $this->get_now();
		$timezone = $timezone ? $timezone : $this->get_timezone();

		// 2.3.2: use get time function with timezone
		// 2.3.2: fix to pass week day start as numerical (w)
		// 2.3.3: revert to passing week day start as day (l)
		// 2.3.3: added fix to start from previous day
		$today = $this->get_time( 'l', $now, $timezone );
		$yesterday = $this->get_previous_day( $today );
		$weekdays = $this->get_schedule_weekdays( $yesterday );
		$weekdates = $this->get_schedule_weekdates( $weekdays, $now, $timezone );

		if ( $this->debug ) {
			$debug = 'Next Shows from ' . $yesterday . PHP_EOL;
			$debug .= 'Weekdays: ' . print_r( $weekdays, true ) . PHP_EOL;
			$debug .= 'Weekdates: ' . print_r( $weekdates, true ) . PHP_EOL;
			$this->debug_log( $debug );
		}

		// --- loop shifts to find next shows ---
		$current_split = false;
		foreach ( $weekdays as $day ) {
			if ( isset( $scheduled_shifts[$day] ) ) {
				$shifts = $scheduled_shifts[$day];
				foreach ( $shifts as $start => $shift ) {

					// --- get this shift start and end times ---
					// 2.3.2: replace strtotime with to_time for timezones
					// 2.3.2: fix to convert to 24 hour format first
					$shift_start = $this->convert_shift_time( $shift['start'] );
					$shift_end = $this->convert_shift_time( $shift['end'] );
					$shift_start_time = $this->to_time( $weekdates[$day] . ' ' . $shift_start, $timezone );
					$shift_end_time = $this->to_time( $weekdates[$day] . ' ' . $shift_end, $timezone );

					if ( $this->debug ) {
						$debug = 'Next? ' . $now . ' - ' . $shift_start_time . ' - ' . $shift_end_time . PHP_EOL;
						$debug .= radio_station_get_time( 'date', $now ) . ' - ' . radio_station_get_time( 'date', $shift_start_time ) . ' - ' . radio_station_get_time( 'date', $shift_end_time ) . PHP_EOL;
						$debug .= 'Shift: ' . print_r( $shift, true ) . PHP_EOL;
						$this->debug_log( $debug );
					}

					// --- set current show ---
					// 2.3.2: set current show transient
					// 2.3.3: remove current show transient

					// --- check if show is upcoming ---
					if ( $now < $shift_start_time ) {

						// --- reset skip flag ---
						$skip = false;

						if ( $current_split ) {

							$skip = true;
							$current_split = false;

						} elseif ( isset( $shift['split'] ) && $shift['split'] ) {

							// --- dedupe for shifts split overnight ---
							if ( isset( $shift['real_end'] ) ) {
								$shift['end'] = $shift['real_end'];
								$current_split = true;
							} elseif ( isset( $shift['real_start'] ) ) {
								// 2.3.3: skip this shift instead of setting
								// (because second half of current show!)
								$skip = true;
							}

						} else {
							// --- reset split shift flag ---
							$current_split = false;
						}

						if ( !$skip ) {

							// --- add to next shows data ---
							// 2.3.2: set date for widget display
							$shift['date'] = $weekdates[$day];
							$next_shows[] = $shift;
							if ( $this->debug ) {
								$debug = 'Next Shows: ' . print_r( $next_shows, true );
								$this->debug_log( $debug );
							}

							// --- return if we have reached limit ---
							if ( count( $next_shows ) == $limit ) {
								$next_shows = apply_filters( 'schedule_engine_next_scheduled', $next_shows, $limit, $scheduled_shifts, $time, $timezone );
								return $next_shows;
							}
						}
					}
				}
			}
		}

		// --- filter and return ---
		$next_shows = apply_filters( 'schedule_engine_next_scheduled', $next_shows, $limit, $scheduled_shifts, $time, $timezone );
		return $next_shows;
	}


	// ----------------------
	// === Shift Checking ===
	// ----------------------

	// ----------------
	// Check All Shifts
	// ----------------
	// 2.5.18: added all shift checker
	public function check_all_shifts( $records ) {
		
		// --- get weekdates for checking ---
		$now = $this->get_now();
		$timezone = $this->get_timezone();
		$today = $this->get_time( 'l', $now, $timezone );
		$weekdays = $this->get_schedule_weekdays( $today, $now, $timezone );
		$weekdates = $this->get_schedule_weekdates( $weekdays, $now, $timezone );
		$times = array(
			'now'       => $now,
			'today'     => $today,
			'weekdays'  => $weekdays,
			'weekdates' => $weekdates,
			'timezone'  => $timezone,
		);

		// --- process records into shift data ---
		// note: if channel is specified, get_shift_data gets only shifts for channel
		$split = false;
		$all_shifts = $this->get_shift_data( $records, $split, $times );
		$all_shifts = $this->sort_shifts( $all_shifts, $weekdays );

		// --- check for shift conflicts ---
		$checked_shifts = $this->check_shifts( $all_shifts, $times );
		return $checked_shifts;
	}
	
	// -------------------------
	// Schedule Conflict Checker
	// -------------------------
	// (checks all existing show shifts for schedule)
	// 2.3.0: added show shift conflict checker
	public function check_shifts( $all_shifts, $times = false ) {

		// --- set channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// TODO: check for start of week and end of week shift conflicts?

		// 2.3.3.9: fix weekday/dates code to match get_show_shifts
		if ( $times ) {
			$now = $times['now'];
			$timezone = $times['timezone'];
			$today = $times['today'];
			$weekdays = $times['weekdays'];
			$weekdates = $times['weekdates'];
		} else {
			$now = $this->get_now();
			$timezone = $this->get_timezone();
			$today = $this->get_time( 'l', $now, $timezone );
			$weekdays = $this->get_schedule_weekdays( $today, $now, $timezone );
			$weekdates = $this->get_schedule_weekdates( $weekdays, $now, $timezone );
		}

		$conflicts = $checked_shifts = array();
		if ( count( $all_shifts ) > 0 ) {
			$prev_shift = $prev_prev_shift = false;
			// foreach ( $all_shifts as $day => $shifts ) {
			foreach ( $weekdays as $day ) {

				if ( isset( $all_shifts[$day] ) ) {
					$shifts = $all_shifts[$day];

					// --- get previous and next days for comparisons ---
					// 2.3.2: fix to use week date schedule
					$thisdate = $weekdates[$day];
					$date_time = $this->to_time( $weekdates[$day] . ' 00:00', $timezone );

					// --- check for conflicts (overlaps) ---
					foreach ( $shifts as $key => $shift ) {

						// --- set first shift checked ---
						// 2.3.2: added for checking against last shift
						if ( !isset( $first_shift ) ) {
							$first_shift = $shift;
						}
						$last_shift = $shift;

						// --- reset shift switches ---
						$set_shift = true;
						$conflict = false;
						$disabled = ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) ? true : false;

						// --- account for split midnight times ---
						// 2.3.2: replace strtotime with to_time for timezones
						// # midnight
						if ( ( '00:00 am' == $shift['start'] ) || ( '12:00 am' == $shift['start'] ) ) {
							$start_time = $this->to_time( $thisdate . ' 00:00', $timezone );
						} else {
							$shift_start = $this->convert_shift_time( $shift['start'] );
							$start_time = $this->to_time( $thisdate . ' ' . $shift_start, $timezone );
						}
						if ( ( '11:59:59 pm' == $shift['end'] ) || ( '12:00 am' == $shift['end'] ) ) {
							$end_time = $this->to_time( $thisdate . ' 11:59:59', $timezone ) + 1;
						} else {
							$shift_end = $this->convert_shift_time( $shift['end'] );
							$end_time = $this->to_time( $thisdate . ' ' . $shift_end, $timezone );
						}

						if ( false != $prev_shift ) {

							// note: previous shift start and end times set in previous loop iteration
							if ( $this->debug ) {
								$debug = "Shift Date: " . $thisdate . " - Day: " . $day . " - Time: " . $date_time . PHP_EOL;
								$prevdata = $prev_shift['shift'];
								$prevday = $prev_shift['day'];
								$prevdate = $prev_shift['date'];
								$debug .= "Previous Shift Date: " . $prevdate . " - Shift Day: " . $prevday . PHP_EOL;
								$debug .= "Shift: " . print_r( $shift, true ) . PHP_EOL;
								$debug .= "Previous Shift: " . print_r( $prev_shift, true ) . PHP_EOL;
								$debug .= "Weekdays: " . print_r( $weekdays, true ) . PHP_EOL;
								$debug .= "Weekdates: " . print_r( $weekdates, true ) . PHP_EOL;
								$this->debug_log( $debug );
							}

							// --- detect shift conflicts ---
							// (and maybe *attempt* to fix them up)
							if ( isset( $prev_start_time ) && ( $start_time == $prev_start_time ) ) {
								if ( $shift['split'] || $prev_shift['split'] ) {
									$conflict = 'overlap';
									if ( $shift['split'] && $prev_shift['split'] ) {
										// - need to compare start times on previous day -
										// 2.3.2: replace strtotime with to_time for timezones
										// 2.3.2: fix to convert to 24 hour format first
										$data = $shift['shift'];
										$real_start = $this->convert_shift_time( $data['real_start'] );
										$shiftdate = $this->get_previous_date( $thisdate );
										$real_start_time = $this->to_time( $shiftdate . ' ' . $real_start, $timezone );

										// 2.3.2: fix to calculation of previous shift day start time
										$prevdata = $prev_shift['shift'];
										// $prevday = $prevdata['day'];
										// $prevdate = %this->get_previous_date( $thisdate, $prevday );
										$prevdate = $prev_shift['date'];
										$prev_real_start = $this->convert_shift_time( $prevdata['real_start'] );
										$prev_real_start_time = $this->to_time( $prevdate . ' ' . $prev_real_start, $timezone );

										// --- compare start times ---
										if ( $real_start_time > $prev_real_start_time ) {
											// - current shift started later (overwrite from midnight) -
											$set_shift = true;
										} elseif ( $real_start_time == $prev_real_start_time ) {
											// - do not duplicate, already recorded -
											$conflict = false;
											// - total overlap, check last updated post time -
											$updated = strtotime( $shift['updated'] );
											$prev_updated = strtotime( $prev_shift['updated'] );
											if ( $updated < $prev_updated ) {
												$set_shift = false;
											}
										}
									} elseif ( $shift['split'] ) {
										// - the current shift has been split overnight -
										// assume previous shift is correct (ignore new shift time)
										$set_shift = false;
									} elseif ( $prev_shift['split'] ) {
										// the previous shift has been split overnight
										// so we will assume the new shift start is correct
										// (overwrites previous shift from midnight key)
										$set_shift = true;
									}
								} else {
									$conflict = 'same_start';
									// - we do not know which of these is correct -
									// no solution here, so check most recent last updated time
									// we will assume (without certainty) most recent is correct
									$updated = strtotime( $shift['updated'] );
									$prev_updated = strtotime( $prev_shift['updated'] );
									if ( $updated < $prev_updated ) {
										$set_shift = false;
									}
								}
							} elseif ( isset( $prev_end_time ) && ( $start_time < $prev_end_time ) ) {

								// 2.5.0: fix to missing variable operator on prev_end_time
								if ( ( $end_time > $prev_start_time ) || ( $end_time > $prev_end_time ) ) {

									// --- set the previous shift end time to current shift start ---
									$conflict = 'overlap';

									// --- modify only if this shift is not disabled ---
									if ( !$disabled ) {
										// 2.3.2: variable type fix (from checked_shift)
										// 2.3.2: fix for midnight starting aplit shifts
										// 2.3.2: set checked shifts with day key directly
										if ( '00:00 am' == $prev_shift['start'] ) {
											$prev_shift['start'] = '12:00 am';
										}
										$checked_shifts[$day][$prev_shift['start']]['end'] = $shift['start'];
										$checked_shifts[$day][$prev_shift['start']]['trimmed'] = true;

										if ( $this->debug ) {
											$debug = "Previous Previous Shift: " . print_r( $prev_prev_shift, true );
											$this->debug_log( $debug );
										}

										// --- fix for real end of first part of previous split shift ---
										if ( isset( $prev_shift['split'] ) && $prev_shift['split'] && isset( $prev_shift['real_start'] ) ) {
											if ( isset( $prev_prev_shift ) && isset( $prev_prev_shift['split'] ) && $prev_prev_shift['split'] ) {
												$checked_shifts[$prev_prev_shift['start']]['real_end'] = $shift['start'];
												$checked_shifts[$prev_prev_shift['start']]['trimmed'] = true;
											}
										}
									}

									// --- conflict debug output ---
									if ( $this->debug ) {
										$debug = "Conflicting Start Time: " . $this->get_time( "m-d l H:i", $start_time ) . " (" . $start_time . ")" . PHP_EOL;
										$debug .= '[ ' . $this->get_time( "m-d l H:i", $start_time ) . ' ]';
										$debug .= "Overlaps previous End Time: " .  $this->get_time( "m-d l H:i", $prev_end_time ) . " (" . $prev_end_time . ")" . PHP_EOL;
										$debug .= '[ ' . $this->get_time( "m-d l H:i", $prev_end_time ) . ' ]';
										// $debug .= "Shift: " . print_r( $shift, true );
										// $debug .= "Previous Shift: " . print_r( $prev_shift, true );
										$this->debug_log( $debug );
									}
								}
							}
						}

						// --- maybe store shift conflict data ---
						if ( $conflict ) {

							// ---- set short shift time data ---
							$shift_start = $shift['shift']['start_hour'] . ':' . $shift['shift']['start_min'] . $shift['shift']['start_meridian'];
							$shift_end = $shift['shift']['end_hour'] . ':' . $shift['shift']['end_min'] . $shift['shift']['end_meridian'];
							$prev_shift_start = $prev_shift['shift']['start_hour'] . ':' . $prev_shift['shift']['start_min'] . $prev_shift['shift']['start_meridian'];
							$prev_shift_end = $prev_shift['shift']['end_hour'] . ':' . $prev_shift['shift']['end_min'] . $prev_shift['shift']['end_meridian'];
							// 2.5.6: added for undefined index warning
							if ( !isset( $prev_disabled ) ) {
								$prev_disabled = '';
							}

							// --- store conflict for this shift ---
							// $conflicts[$shift['show']][] = array(
							$conflicts[$shift['ID']][] = array(
								// 'show'          => $shift['show'],
								'ID'            => $shift['ID'],
								'day'           => $shift['shift']['day'],
								'start'         => $shift_start,
								'end'           => $shift_end,
								'disabled'      => $disabled,
								// 'with_show'     => $prev_shift['show'],
								'with_show'     => $prev_shift['ID'],
								'with_day'      => $prev_shift['shift']['day'],
								'with_start'    => $prev_shift_start,
								'with_end'      => $prev_shift_end,
								'with_disabled' => $prev_disabled,
								'conflict'      => $conflict,
								'duplicate'     => false,
							);

							// --- store for previous shift only if a different show ---
							// if ( $shift['show'] != $prev_shift['show'] ) {
							if ( $shift['ID'] != $prev_shift['ID'] ) {
								$conflicts[$prev_shift['ID']][] = array(
									// 'show'          => $prev_shift['show'],
									'ID'            => $shift['ID'],
									'day'           => $prev_shift['shift']['day'],
									'start'         => $prev_shift_start,
									'end'           => $prev_shift_end,
									'disabled'      => $prev_disabled,
									// 'with_show'     => $shift['show'],
									'with_show'     => $shift['ID'],
									'with_day'      => $shift['shift']['day'],
									'with_start'    => $shift_start,
									'with_end'      => $shift_end,
									'with_disabled' => $disabled,
									'conflict'      => $conflict,
									'duplicate'     => true,
								);
							}
						}

						// --- set current shift to previous for next iteration ---
						$prev_start_time = $start_time;
						$prev_end_time = $end_time;
						if ( $prev_shift ) {
							$prev_prev_shift = $prev_shift;
						}
						$prev_shift = $shift;
						$prev_disabled = $disabled;

						// --- set the now checked shift data ---
						// (...but only if not disabled!)
						if ( $set_shift && !$disabled ) {
							// - no longer need shift and post updated times -
							// 2.3.3.9: keep shift ID with schedule data
							$shift['id'] = $shift['shift']['id'];
							unset( $shift['shift'] );
							unset( $shift['updated'] );
							if ( '00:00 am' == $shift['start'] ) {
								$shift['start'] = '12:00 am';
							}
							// 2.3.2: set checked shifts with day key directly
							$checked_shifts[$day][$shift['start']] = $shift;
						}
					}
				}

				// --- set checked shifts for day ---
				// 2.3.2: set checked shifts with day key directly
				// $all_shifts[$day] = $checked_shifts;
			}
		}

		// --- check last shift against first shift ---
		// 2.3.2: added for possible overlap (split by weekly schedule dates)
		// 2.5.6: added isset check for first_shift
		if ( isset( $last_shift ) && isset( $first_shift ) && ( $last_shift != $first_shift ) ) {

			// --- use days for different weeks to compare ---
			$l_shift_start = $this->convert_shift_time( $last_shift['start'] );
			$l_shift_end = $this->convert_shift_time( $last_shift['end'] );
			$last_shift_start = $this->to_time( $last_shift['day'] . ' ' . $l_shift_start, $timezone );
			$last_shift_end = $this->to_time( $last_shift['day'] . ' ' . $l_shift_end, $timezone );
			if ( $last_shift_end < $last_shift_start ) {
				$last_shift_end = $last_shift_end + ( 24 * 60 * 60 );
			}

			$f_shift_start = $this->convert_shift_time( $first_shift['start'] );
			$f_shift_end = $this->convert_shift_time( $first_shift['end'] );
			$first_shift_start = $this->to_time( 'next ' . $first_shift['day'] . ' ' . $f_shift_start, $timezone );
			$first_shift_end = $this->to_time( 'next ' . $first_shift['day'] . ' ' . $f_shift_end, $timezone );
			if ( $first_shift_end < $first_shift_start ) {
				$first_shift_end = $first_shift_end + ( 24 * 60 * 60 );
			}

			if ( $this->debug ) {
				$debug = 'Last Shift End: ' . $last_shift['day'] . ' ' . $l_shift_end . ' - (' . $last_shift_end . ')' . PHP_EOL;
				$debug .=  'First Shift Start: ' . $first_shift['day'] . ' ' . $f_shift_start . ' - (' . $first_shift_start . ')' . PHP_EOL;
				$this->debug_log( $debug );
			}

			// --- end of the week overlap check ---
			// 2.3.3.9: fix to incorrect overlap logic
			if ( $last_shift_end > $first_shift_start ) {
			// if ( $last_shift_start < $first_shift_end ) {

				// --- record a conflict ---
				if ( $this->debug ) {
					$debug = "First/Last Shift Overlap Conflict" . PHP_EOL;
					$debug .= "First Shift: " . print_r( $first_shift, true ) . PHP_EOL;
					$debug .= "Last Shift: " . print_r( $last_shift, true ) . PHP_EOL;
					$this->debug_log( $debug );
				}

				/*
				// --- store conflict for this shift ---
				$conflicts[$first_shift['show']][] = array(
					'show'          => $first_shift['show'],
					'day'           => $first_shift['shift']['day'],
					'start'         => $first_shift_start,
					'end'           => $first_shift_end,
					'disabled'      => $first_shift['shift']['disabled'],
					'with_show'     => $last_shift['show'],
					'with_day'      => $last_shift['shift']['day'],
					'with_start'    => $last_shift_start,
					'with_end'      => $last_shift_end,
					'with_disabled' => $last_shift['shift']['disabled'],
					'conflict'      => 'overlap',
					'duplicate'     => false,
				);

				// --- store for other shift if different show ---
				if ( $first_shift['show'] != $last_shift['show'] ) {
					$conflicts[$last_shift['show']][] = array(
						'show'          => $last_shift['show'],
						'day'           => $last_shift['shift']['day'],
						'start'         => $last_shift_start,
						'end'           => $last_shift_end,
						'disabled'      => $last_shift['shift']['disabled'],
						'with_show'     => $first_shift['show'],
						'with_day'      => $first_shift['shift']['day'],
						'with_start'    => $first_shift_start,
						'with_end'      => $first_shift_end,
						'with_disabled' => $first_shift['shift']['disabled'],
						'conflict'      => 'overlap',
						'duplicate'     => true,
					);
				} */
			}
		}

		// --- do shift conflict action ---
		$this->conflicts = $conflicts;
		do_action( 'schedule_engine_set_shift_conflicts', $conflicts, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			do_action( $context . '_set_shift_conflicts', $conflicts, $channel );
		}
		
		// --- return checked results ---
		return $checked_shifts;
	}

	// -------------
	// Shift Checker
	// -------------
	// (checks shift being saved against other shows)
	public function check_shift( $record_id, $shift, $scope, $all_shifts, $times = false ) {

		// --- set channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// 2.3.2: bug out if day is empty
		if ( '' == $shift['day'] ) {
			return false;
		}

		// --- convert days to dates for checking ---
		// 2.3.3.9: fix weekday/dates code to match get_show_shifts
		if ( $times ) {
			$now = $times['now'];
			$timezone = $times['timezone'];
			$today = $times['today'];
			$weekdays = $times['weekdays'];
			$weekdates = $times['weekdates'];
		} else {
			$now = $this->get_now();
			$timezone = $this->get_timezone();
			$today = $this->get_time( 'l', $now, $timezone );
			$weekdays = $this->get_schedule_weekdays( $today, $now, $timezone );
			$weekdates = $this->get_schedule_weekdates( $weekdays, $now, $timezone );
		}

		// --- get shows to check against via scope ---
		$check_shifts = array();
		if ( 'all' == $scope ) {
			$check_shifts = $all_shifts;
		} elseif ( 'other' == $scope ) {
			// --- check only against other shifts ---
			// 2.5.0: changed scope from shows to other
			foreach ( $all_shifts as $day => $day_shifts ) {
				foreach ( $day_shifts as $start => $day_shift ) {
					// --- ...so remove (skip) any shifts for this record ---
					// 2.5.0: changed show key to ID key
					// if ( $day_shift['show'] != $record_id ) {
					if ( $day_shift['ID'] != $record_id ) {
						$check_shifts[$day][$start] = $day_shift;
					}
				}
			}
		}

		// 2.3.2: to doubly ensure shifts are set in schedule order
		$sorted_shifts = array();
		foreach ( $weekdays as $weekday ) {
			if ( isset( $check_shifts[$weekday] ) ) {
				$sorted_shifts[$weekday] = $check_shifts[$weekday];
			}
		}
		$check_shifts = $sorted_shifts;

		// --- get shift start and end time ---
		// 2.3.2: fix to convert to 24 hour times first
		// 2.3.3.9: set shift start and end to prevent undefined index warning later
		$shift['start'] = $shift['start_hour'] . ':' . $shift['start_min'] . $shift['start_meridian'];
		$shift['end'] = $shift['end_hour'] . ':' . $shift['end_min'] . $shift['end_meridian'];
		$start_time = $this->convert_shift_time( $shift['start'] );
		$end_time = $this->convert_shift_time( $shift['end'] );

		// 2.3.2: use next week day instead of date
		$shift_start_time = $this->to_time( $weekdates[$shift['day']] . ' ' . $start_time, $timezone );
		$shift_end_time = $this->to_time( $weekdates[$shift['day']] . ' ' . $end_time, $timezone );
		// 2.3.3.9: added or equals to operator
		if ( $shift_end_time <= $shift_start_time ) {
			$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
		}

		if ( $this->debug ) {
			$debug = "Checking Shift for Show " . $record_id . ": ";
			$debug .= $shift['day'] . " - " . $weekdates[$shift['day']] . " - " . $shift['start_hour'] . ":" . $shift['start_min'] . $shift['start_meridian'];
			$debug .= " (" . $shift_start_time . ")" . PHP_EOL;
			$debug .= " to " . $weekdates[$shift['day']] . " - " . $shift['end_hour'] . ":" . $shift['end_min'] . $shift['end_meridian'];
			$debug .= " (" . $shift_end_time . ")" . PHP_EOL;
			$this->debug_log( $debug );
		}

		// 2.5.18: normalize default shift channel
		if ( !isset( $shift['channel'] ) || ( '' == $shift['channel'] ) || ( '0' == $shift['channel'] ) ) {
			$shift_channel = false;
		} else {
			$shift_channel = $shift['channel'];
		}

		// --- check for conflicts with other show shifts ---
		$conflicts = array();
		foreach ( $check_shifts as $day => $day_shifts ) {
			// 2.3.2: removed day match check
			// if ( $day == $shift['day'] ) {
				foreach ( $day_shifts as $i => $day_shift ) {

					// 2.5.18: normalize default day shift channel
					if ( !isset( $day_shift['channel'] ) || ( '' == $day_shift['channel'] ) || ( '0' == $day_shift['channel'] ) ) {
						$day_shift_channel = false;
					} else {
						$day_shift_channel = $day_shift['channel'];
					}

					// 2.5.18: add shift channel matching
					if ( $shift_channel != $day_shift_channel ) {
						// (also skip last shift rechecking if not matching)
						unset( $day_shift );
					} else {

						if ( !isset( $first_shift ) ) {
							$first_shift = $day_shift;
						}

						// 2.3.2: replace strtotime with to_time for timezones
						// 2.3.2: fix to convert to 24 hour times first
						$day_shift_start = $this->convert_shift_time( $day_shift['start'] );
						$day_shift_end = $this->convert_shift_time( $day_shift['end'] );

						// 2.3.2: use next week day instead of date
						$day_shift_start_time = $this->to_time( $day_shift['date'] . ' ' . $day_shift_start, $timezone );
						$day_shift_end_time = $this->to_time( $day_shift['date'] . ' ' . $day_shift_end, $timezone );
						// 2.3.2: adjust for midnight with change to use non-split shifts
						// 2.3.3.9: added or equals to operator
						if ( $day_shift_end_time <= $day_shift_start_time ) {
							$day_shift_end_time = $day_shift_end_time + ( 24 * 60 * 60 );
						}

						// --- ignore if this is the same shift we are checking ---
						$check_shift = true;
						// if ( $day_shift['show'] == $record_id ) {
						if ( $day_shift['ID'] == $record_id ) {
							// ? only ignore same shift not same show ?
							// if ( ( $day_shift_start_time == $shift_start_time ) && ( $day_shift_end_time == $shift_end_time ) ) {
								$check_shift = false;
							// }
						}

						if ( $check_shift ) {

							if ( $this->debug ) {
								$debug = "...with Shift for Show " . $day_shift['ID'] . ": ";
								$debug .= $day_shift['day'] . " - " . $day_shift['date'] . " - " . $day_shift['start'] . " (" . $day_shift_start_time . ")" . PHP_EOL;
								$debug .= " to " . $day_shift['end'] . " (" . $day_shift_end_time . ")" . PHP_EOL;
								$this->debug_log( $debug );
							}

							// 2.3.2: improved shift checking logic
							// 2.3.3.6: separated logic for conflict match code
							$conflict = false;
							if ( ( $shift_start_time < $day_shift_start_time ) && ( $shift_end_time > $day_shift_start_time ) ) {
								// if the new shift starts before existing shift but ends after existing shift starts
								$conflict = 'start overlap';
							} elseif ( ( $shift_start_time < $day_shift_start_time ) && ( $shift_end_time > $day_shift_end_time ) ) {
								// ...or starts before but ends after the existing shift end time
								$conflict = 'blockout overlap';
							} elseif ( ( $shift_start_time == $day_shift_start_time ) ) {
								// ...or new shift starts at the same time as the existing shift
								$conflict = 'equal start time';
							} elseif ( ( $shift_start_time > $day_shift_start_time ) && ( $shift_end_time < $day_shift_end_time ) ) {
								// ...or if the new shift starts after existing shift and ends before it ends
								$conflict = 'internal overlap';
							} elseif ( ( $shift_start_time > $day_shift_start_time ) && ( $shift_start_time < $day_shift_end_time ) ) {
								// ...or the new shift starts after the existing shift but before it ends
								$conflict = 'end overlap';
							}
							if ( $conflict ) {
								// --- if there is a shift overlap conflict ---
								$conflicts[] = $day_shift;
								if ( $this->debug ) {
									$debug = '^^^ CONFLICT ( ' . $conflict . ' ) ^^^';
									$this->debug_log( $debug );
								}
							}
						}
					}
				}
			// }
		}

		// --- recheck for first shift overlaps ---
		// (not implemented as not needed)
		/* if ( isset( $first_shift ) ) {
			// --- check for first shift overlap using next week ---
			$shift_start = $this->convert_shift_time( $first_shift['start'] );
			$shift_end = $this->convert_shift_time( $first_shift['end'] );
			$first_shift_start_time = $this->to_time( $first_shift['date'] . ' ' . $shift_start, $timezone ) + ( 7 * 24 * 60 * 60 );
			$first_shift_end_time = $this->to_time( $first_shift['date'] . ' ' . $shift_end, $timezone ) + ( 7 * 24 * 60 * 60 );

			if ( $this->debug ) {
				echo "...with First Shift for Show " . $first_shift['show'] . ": ";
				echo $first_shift['day'] . " - " . $first_shift['date'] . " - " . $first_shift['start'] . " (" . $first_shift_start_time . ")";
				echo " to " . $first_shift['end'] . " (" . $first_shift_end_time . ")" . PHP_EOL;
			}

			if ( ( ( $shift_start_time < $first_shift_start_time ) && ( $shift_end_time > $first_shift_start_time ) )
				 || ( ( $shift_start_time < $first_shift_start_time ) && ( $shift_end_time > $first_shift_end_time ) )
				 || ( $shift_start_time == $first_shift_start_time )
				 || ( ( $shift_start_time > $first_shift_start_time ) && ( $shift_end_time < $first_shift_end_time ) )
				 || ( ( $shift_start_time > $first_shift_start_time ) && ( $shift_start_time < $first_shift_end_time ) ) ) {
				$conflicts[] = $first_shift;
				if ( $this->debug ) {
					echo "^^^ CONFLICT ^^^" . PHP_EOL;
				}
			}
		} */

		// --- recheck for last shift ---
		// (for date based schedule overflow rechecking)
		if ( isset( $day_shift ) ) {

			// 2.3.3.6: added check to not last check shift against itself
			// 2.5.1: check all indexes exist to avoid undefined index warnings
			// 2.5.6: fix to check ID key instead of show key
			// if ( ( isset( $day_shift['show'] ) && ( $day_shift['show'] != $record_id ) )
			if ( ( isset( $day_shift['ID'] ) && ( $day_shift['ID'] != $record_id ) )
				|| ( isset( $day_shift['day'] ) && ( $day_shift['day'] != $shift['day'] ) )
				|| ( isset( $day_shift['start'] ) && ( $day_shift['start'] != $shift['start'] ) )
				|| ( isset( $day_shift['end'] ) && ( $day_shift['end'] != $shift['end'] ) ) ) {

				// --- check for new shift overlap using next week ---
				$shift_start_time = $shift_start_time + ( 7 * 24 * 60 * 60 );
				$shift_end_time = $shift_end_time + ( 7 * 24 * 60 * 60 );

				if ( $this->debug ) {
					$debug = "...with Last Shift (using next week):" . PHP_EOL;
					// echo $this->get_time( 'date', $shift_start_time ) . " - " . $shift['start'] . " (" . $shift_start_time . ")";
					// echo " to " . $shift['end'] . " (" . $shift_end_time . ")" . PHP_EOL;
					$debug .= $day_shift['day'] . " - " . $this->get_time( 'date', $day_shift_start_time );
					$debug .= " - " . $day_shift['start'] . " (" . $day_shift_start_time . ")";
					$debug .= " to " . $day_shift['end'] . " (" . $day_shift_end_time . ")" . PHP_EOL;
					$this->debug_log( $debug );
				}

				// 2.3.3.6: separated logic for conflict match code
				$conflict = false;
				if ( ( $shift_start_time < $day_shift_start_time ) && ( $shift_end_time > $day_shift_start_time ) ) {
					$conflict = 'start overlap';
				} elseif ( ( $shift_start_time < $day_shift_start_time ) && ( $shift_end_time > $day_shift_end_time ) ) {
					$conflict = 'blockout overlap';
				} elseif ( $shift_start_time == $day_shift_start_time ) {
					$conflict = 'equal start time';
				} elseif ( ( $shift_start_time > $day_shift_start_time ) && ( $shift_end_time < $day_shift_end_time ) ) {
					$conflict = 'internal overlap';
				} elseif ( ( $shift_start_time > $day_shift_start_time ) && ( $shift_start_time < $day_shift_end_time ) ) {
					$conflict = 'end overlap';
				}
				if ( $conflict ) {
					$conflicts[] = $day_shift;
					if ( $this->debug ) {
						$debug = '^^^ CONFLICT ( ' . $conflict . ') ^^^';
						$this->debug_log( $debug );
					}
				}
			}
		}

		if ( count( $conflicts ) == 0 ) {
			return false;
		}

		return $conflicts;
	}

	// ------------------
	// New Shifts Checker
	// ------------------
	// (checks show shifts for conflicts with same show)
	public function check_new_shifts( $new_shifts, $weekdates = false ) {
		
		// --- get channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- debug point ---
		if ( $this->debug ) {
			$debug = "New Shifts: " . print_r( $new_shifts, true ) . PHP_EOL;
			$this->debug_log( $debug );
		}

		// --- convert days to dates for checking ---
		$timezone = $this->get_timezone();
		if ( !$weekdates ) {
			$now = $this->get_now();
			$today = $this->get_time( 'l', $now, $timezone );
			$weekdays = $this->get_schedule_weekdays( $today, $now, $timezone );
			$weekdates = $this->get_schedule_weekdates( $weekdays, $now, $timezone );
		}

		// --- double loop shifts to check against others ---
		$new_shifts_in = $new_shifts;
		foreach ( $new_shifts as $i => $shift_a ) {

			// 2.5.18: normalize default shift channel
			if ( !isset( $shift_a['channel'] ) || ( '' == $shift_a['channel'] ) || ( '0' == $shift_a['channel'] ) ) {
				$shift_a_channel = false;
			} else {
				$shift_a_channel = $shift_a['channel'];
			}

			if ( '' != $shift_a['day'] ) {

				// --- get shift A start and end times ---
				// 2.3.2: replace strtotime with to_time for timezones
				// 2.3.2: fix to convert to 24 hour format first
				$shift_a_start = $shift_a['start_hour'] . ':' . $shift_a['start_min'] . $shift_a['start_meridian'];
				$shift_a_end = $shift_a['end_hour'] . ':' . $shift_a['end_min'] . $shift_a['end_meridian'];
				$shift_a_start = $this->convert_shift_time( $shift_a_start );
				$shift_a_end = $this->convert_shift_time( $shift_a_end );

				// 2.3.2: use next week day instead of date
				$shift_a_start_time = $this->to_time( $weekdates[$shift_a['day']] . ' ' . $shift_a_start );
				$shift_a_end_time = $this->to_time( $weekdates[$shift_a['day']] . ' ' . $shift_a_end );
				// $shift_a_start_time = $this->to_time( '+2 weeks ' . $shift_a['day'] . ' ' . $shift_a_start );
				// $shift_a_end_time = $this->to_time( '+2 weeks ' . $shift_a['day'] . ' ' . $shift_a_end );
				// 2.3.3.9: added or equals to operator
				if ( $shift_a_end_time <= $shift_a_start_time ) {
					$shift_a_end_time = $shift_a_end_time + ( 24 * 60 * 60 );
				}

				// --- debug point ---
				if ( $this->debug ) {
					$a_start = $shift_a['day'] . ' ' . $shift_a['start_hour'] . ':' . $shift_a['start_min'] . $shift_a['start_meridian'] . ' (' . $shift_a_start_time . ')';
					$a_end = $shift_a['day'] . ' ' . $shift_a['end_hour'] . ':' . $shift_a['end_min'] . $shift_a['end_meridian'] . ' (' . $shift_a_end_time . ')';
					$debug = "Shift A Start: " . $a_start . ' - Shift A End: ' . $a_end . "\n";
					$this->debug_log( $debug );
				}

				$debug = '';
				foreach ( $new_shifts as $j => $shift_b ) {

					// 2.5.18: normalize default shift channel
					if ( !isset( $shift_b['channel'] ) || ( '' == $shift_b['channel'] ) || ( '0' == $shift_b['channel'] ) ) {
						$shift_b_channel = false;
					} else {
						$shift_b_channel = $shift_b['channel'];
					}

					// 2.5.18: added shift channel matching
					if ( ( $shift_a_channel == $shift_b_channel ) && ( $i != $j ) ) {

						if ( $this->debug ) {
							$debug .= $i . ' ::: ' . $j . "\n";
						}

						if ( '' != $shift_b['day'] ) {
							// --- get shift B start and end times ---
							// 2.3.2: replace strtotime with to_time for timezones
							$shift_b_start = $shift_b['start_hour'] . ':' . $shift_b['start_min'] . $shift_b['start_meridian'];
							$shift_b_end = $shift_b['end_hour'] . ':' . $shift_b['end_min'] . $shift_b['end_meridian'];
							$shift_b_start = $this->convert_shift_time( $shift_b_start );
							$shift_b_end = $this->convert_shift_time( $shift_b_end );

							// 2.3.2: use next week day instead of date
							$shift_b_start_time = $this->to_time( $weekdates[$shift_b['day']] . ' ' . $shift_b_start, $timezone );
							$shift_b_end_time = $this->to_time( $weekdates[$shift_b['day']] . ' ' . $shift_b_end, $timezone );
							// 2.3.3.9: added or equals to operator
							if ( $shift_b_end_time <= $shift_b_start_time ) {
								$shift_b_end_time = $shift_b_end_time + ( 24 * 60 * 60 );
							}

							// --- debug point ---
							if ( $this->debug ) {
								$b_start = $shift_b['day'] . ' ' . $shift_b_start . ' (' . $shift_b_start_time . ')';
								$b_end = $shift_b['day'] . ' ' . $shift_b_end . ' (' . $shift_b_end_time . ')';
								$debug .= "with Shift B Start: " . $b_start . ' - Shift B End: ' . $b_end . PHP_EOL;
							}

							// --- compare shift A and B times ---
							if ( ( ( $shift_a_start_time < $shift_b_start_time ) && ( $shift_a_end_time > $shift_b_start_time ) )
									|| ( ( $shift_a_start_time < $shift_b_start_time ) && ( $shift_a_end_time > $shift_b_end_time ) )
									|| ( $shift_a_start_time == $shift_b_start_time )
									|| ( ( $shift_a_start_time > $shift_b_start_time ) && ( $shift_a_end_time < $shift_b_end_time ) )
									|| ( ( $shift_a_start_time > $shift_b_start_time ) && ( $shift_a_start_time < $shift_b_end_time ) ) ) {

								// --- maybe disable shift B ---
								// 2.3.2: added check for isset on disabled key
								if ( ( !isset( $new_shifts[$i]['disabled'] ) || ( 'yes' != $new_shifts[$i]['disabled'] ) )
									&& ( !isset( $new_shifts[$j]['disabled'] ) || ( 'yes' != $new_shifts[$j]['disabled'] ) ) ) {

									// --- debug point ---
									if ( $this->debug ) {
										// TODO: write more detailed explanations for debug conditions
										$debug .= "* Conflict Found! New Shift (B) Disabled ";
										if ( ( $shift_a_start_time < $shift_b_start_time ) && ( $shift_a_end_time > $shift_b_start_time ) ) {
											$debug .= "[A]";
										}
										if ( ( $shift_a_start_time < $shift_b_start_time ) && ( $shift_a_end_time > $shift_b_end_time ) ) {
											$debug .= "[B]";
										}
										if ( $shift_a_start_time == $shift_b_start_time ) {
											$debug .= "[C]";
										}
										if ( ( $shift_a_start_time > $shift_b_start_time ) && ( $shift_a_end_time < $shift_b_end_time ) ) {
											$debug .= "[D]";
										}
										if ( ( $shift_a_start_time > $shift_b_start_time ) && ( $shift_a_start_time < $shift_b_end_time ) ) {
											$debug .= "[E]";
										}
										$debug .= "*" . PHP_EOL;
									}

									$new_shifts[$j]['disabled'] = 'yes';

								} else {
									if ( $this->debug ) {
										$debug .= "[Conflict with disabled shift.]" . PHP_EOL;
									}
								}
							}
						}
					}
				}
			}
		}

		// --- debug point ---
		if ( $this->debug ) {
			$debug .= "Checked New Shifts: " . print_r( $new_shifts, true ) . PHP_EOL;
			$this->debug_log( $debug, false );
		}

		// --- filter and return ---
		$new_shifts = apply_filters( 'schedule_engine_checked_new_shifts', $new_shifts, $new_shifts_in, $weekdates, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$new_shifts = apply_filters( $context . '_checked_new_shifts', $new_shifts, $new_shifts_in, $weekdates, $channel );
		}
		return $new_shifts;
	}

	// -------------------
	// Validate Shift Time
	// -------------------
	// 2.3.0: added check for incomplete shift times
	public function validate_shift( $shift ) {

		if ( '' == $shift['day'] ) {
			$shift['disabled'] = 'yes';
		}
		if ( ( '' == $shift['start_meridian'] ) || ( '' == $shift['end_meridian'] ) ) {
			$shift['disabled'] = 'yes';
		}
		if ( ( '' == $shift['start_hour'] ) || ( '' == $shift['end_hour'] ) ) {
			$shift['disabled'] = 'yes';
		}
		if ( '' == $shift['start_min'] ) {
			$shift['start_min'] = '00';
		}
		if ( '' == $shift['end_min'] ) {
			$shift['end_min'] = '00';
		}

		return $shift;
	}


	// ------------------------
	// === Time Conversions ===
	// ------------------------


	// -------
	// Get Now
	// -------
	public function get_now( $gmt = true ) {

		$channel = $this->channel;
		$context = $this->context;

		if ( defined( 'SCHEDULE_ENGINE_USE_SERVER_TIMES' ) && SCHEDULE_ENGINE_USE_SERVER_TIMES ) {
			$now = strtotime( current_time( 'mysql' ) );
		} else {
			$datetime = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
			$now = $datetime->format( 'U' );
		}
		
		// 2.5.6: allow explicit override of now time
		if ( isset( $_REQUEST['for_time'] ) ) {
			$now = absint( $_REQUEST['for_time'] );
		}

		// 2.5.6: added programmatic filter for now time
		$now = apply_filters( 'schedule_engine_now_time', $now, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$now = apply_filters( $context . '_now_time', $now, $channel );
		}
		return $now;
	}

	// ------------
	// Get Timezone
	// ------------
	public function get_timezone() {
		
		// --- get channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- fallback to WordPress timezone ---
		$timezone = get_option( 'timezone_string' );
		if ( false !== strpos( $timezone, 'Etc/GMT' ) ) {
			$offset = get_option( 'gmt_offset' );
			$timezone = 'UTC' . $offset;
		} elseif ( '' == $timezone ) {
			// 2.5.0: handle empty (not set) timezone location
			$timezone = 'UTC';
		}

		$timezone = apply_filters( 'schedule_engine_timezone', $timezone, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$timezone = apply_filters( $context . '_timezone', $timezone, $channel );
		}
		return $timezone;
	}

	// -----------------
	// Get Timezone Code
	// -----------------
	// note: this should only be used for display purposes
	// (as the actual code used is based on timezone/location)
	public function get_timezone_code( $timezone ) {
		$datetime = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$code = $datetime->format( 'T' );
		return $code;
	}

	// --------------------
	// Get Date Time Object
	// --------------------
	// 2.3.2: added for consistent timezone conversions
	public function get_date_time( $timestring, $timezone ) {

		// echo "*TIMEZONE*: " . $timezone . PHP_EOL;

		if ( 'UTC' == $timezone ) {
			$utc = new DateTimeZone( 'UTC' );
			$datetime = new DateTime( $timestring, $utc );
		} elseif ( strstr( $timezone, 'UTC' ) ) {
			$offset = str_replace( 'UTC', '', $timezone );
			$offset = (int) $offset * 60 * 60;
			$utc = new DateTimeZone( 'UTC' );
			$datetime = new DateTime( $timestring, $utc );
			$timestamp = (int) $datetime->format( 'U' );
			$timestamp = $timestamp + $offset;
			$datetime->setTimestamp( $timestamp );
		} elseif ( $timezone ) {
			$datetime = new DateTime( $timestring, new DateTimeZone( $timezone ) );
			// ...fix to set timestamp again just in case
			// echo "A: " . print_r( $datetime, true ) . PHP_EOL;
			// if ( '@' == substr( $timestring, 0, 1 ) ) {
			//	$timestamp = substr( $timestring, 1, strlen( $timestring ) );
			//	$datetime->setTimestamp( $timestamp );
			// }
			// echo "B: " . print_r( $datetime, true ) . PHP_EOL;
		} else {
			// 2.5.6: debug output instead of exiting
			if ( $this->debug ) {
				$debug = 'Error in Time String Timezone: ' . $timestring . ' - Timezone: ' . $timezone;
				$this->debug_log( $debug );
			}
			return false;
		}

		return $datetime;
	}

	// --------------
	// String To Time
	// --------------
	// 2.3.2: added for timezone handling
	public function to_time( $timestring, $timezone = false ) {

		if ( defined( 'SCHEDULE_ENGINE_USE_SERVER_TIMES' ) && SCHEDULE_ENGINE_USE_SERVER_TIMES ) {
			$time = strtotime( $timestring );
		} else {
			if ( strstr( $timezone, 'UTC' ) && ( 'UTC' != $timezone ) ) {
				// --- fallback for UTC offsets ---
				$offset = str_replace( 'UTC', '', $timezone );
				$offset = (int) $offset * 60 * 60;
				$utc = new DateTimeZone( 'UTC' );
				$datetime = new DateTime( $timestring, $utc );
				$timestamp = $datetime->getTimestamp();
				$timestamp = $timestamp - $offset;
				$datetime->setTimestamp( $timestamp );
			} else {
				if ( !$timezone ) {
					$timezone = $this->get_timezone();
				}
				$datetime = $this->get_date_time( $timestring, $timezone );
			}
			$time = $datetime->format( 'U' );
		}

		return $time;
	}

	// ---------
	// Get Times
	// ---------
	// 2.5.0: added for separation / abstraction
	public function get_times( $time = false, $timezone = false ) {

		// --- get offset time and date ---
		if ( !$timezone || ( defined( 'SCHEDULE_ENGINE_USE_SERVER_TIMES' ) && SCHEDULE_ENGINE_USE_SERVER_TIMES ) ) {

			if ( !$time ) {
				$time = $this->get_now();
			}
			// 2.4.5: use gmdate here
			$day = gmdate( 'l', $time );
			$date = gmdate( 'Y-m-d', $time );
			$date_time = gmdate( 'Y-m-d H:i:s', $time );
			$timestamp = gmdate( 'U', $time );

		} else {

			// 2.5.0: shortened et timestring condition
			$timestring = $time ? '@' . $time : 'now';

			// --- get datetime object ---
			if ( strstr( $timezone, 'UTC' ) ) {
				$datetime = $this->get_date_time( $timestring, $timezone );
			} else {
				// ...and refix for location timezones
				$datetime = new DateTime( $timestring, new DateTimeZone( 'UTC' ) );
				$datetime->setTimezone( new DateTimeZone( $timezone ) );
			}

			// --- set formatted strings ---
			$day = $datetime->format( 'l' );
			$date = $datetime->format( 'Y-m-d' );
			$date_time = $datetime->format( 'Y-m-d H:i:s' );
			$timestamp = $datetime->format( 'U' );

		}

		// --- set times array ---
		$times = array(
			'day'       => $day,
			'date'      => $date,
			'datetime'  => $date_time,
			'timestamp' => $timestamp,
		);

		// --- maybe set datetime object ---
		if ( isset( $datetime ) ) {
			$times['object'] = $datetime;
		}

		return $times;
	}

	// --------
	// Get Time
	// --------
	// 2.3.2: added for timezone adjustments
	public function get_time( $key = false, $time = false, $timezone = false ) {

		$times = $this->get_times( $time, $timezone );

		if ( !$key ) {
			return $times;
		}

		if ( array_key_exists( $key, $times ) ) {
			// --- use preformatted time ---
			$time = $times[$key];
		} elseif ( isset( $times['object'] ) ) {
			// --- use datetime object for formatting ---
			$datetime = $times['object'];
			$time = $datetime->format( $key );
		} else {
			// --- fallback to server date ---
			$time = date( $key, $time );
		}
		return $time;
	}

	// --------------------
	// Get Timezone Options
	// --------------------
	// ref: (based on) https://stackoverflow.com/a/17355238/5240159
	public function get_timezone_options( $include_wp_timezone = false ) {

		// --- maybe get stored timezone options ---
		$options = get_transient( 'schedule_engine_timezone_options' );
		if ( !$options ) {

			// --- set regions ---
			$regions = array(
				DateTimeZone::AFRICA     => __( 'Africa', 'schedule-engine' ),
				DateTimeZone::AMERICA    => __( 'America', 'schedule-engine' ),
				DateTimeZone::ASIA       => __( 'Asia', 'schedule-engine' ),
				DateTimeZone::ATLANTIC   => __( 'Atlantic', 'schedule-engine' ),
				DateTimeZone::AUSTRALIA  => __( 'Australia', 'schedule-engine' ),
				DateTimeZone::EUROPE     => __( 'Europe', 'schedule-engine' ),
				DateTimeZone::INDIAN     => __( 'Indian', 'schedule-engine' ),
				DateTimeZone::PACIFIC    => __( 'Pacific', 'schedule-engine' ),
				DateTimeZone::ANTARCTICA => __( 'Antarctica', 'schedule-engine' ),
			);

			// --- loop regions ---
			foreach ( $regions as $region => $label ) {

				// --- option group by region ---
				$options['*OPTGROUP*' . $region] = $label;

				$timezones = DateTimeZone::listIdentifiers( $region );
				$timezone_offsets = array();
				foreach ( $timezones as $timezone ) {
					$datetimezone = new DateTimeZone( $timezone );
					$offset = $datetimezone->getOffset( new DateTime() );
					$timezone_offsets[$offset][] = $timezone;
				}
				ksort( $timezone_offsets );

				foreach ( $timezone_offsets as $offset => $timezones ) {
					foreach ( $timezones as $timezone ) {
						$prefix = $offset < 0 ? '-' : '+';
						$hour = gmdate( 'H', abs( $offset ) );
						$minutes = gmdate( 'i', abs( $offset ) );
						$code = $this->get_timezone_code( $timezone );
						$label = $code . ' (GMT' . $prefix . $hour . ':' . $minutes . ') - ';
						$timezone_split = explode( '/', $timezone );
						unset( $timezone_split[0] );
						$timezone_joined = implode( '/', $timezone_split );
						$label .= str_replace( '_', ' ', $timezone_joined );
						$options[$timezone] = $label;
					}
				}
			}
			$expiry = 7 * 24 * 60 * 60;
			set_transient( 'schedule_engine_timezone_options', $options, $expiry );
		}

		// --- maybe add WordPress timezone (default) option ---
		if ( $include_wp_timezone ) {
			$wp_timezone = array( '' => __( 'WordPress Timezone', 'schedule-engine' ) );
			$options = array_merge( $wp_timezone, $options );
		}

		// --- filter and return ---
		$options = apply_filters( 'schedule_engine_timezone_options', $options, $include_wp_timezone );
		return $options;
	}

	// --------------
	// Get Weekday(s)
	// --------------
	// 2.3.2: added get weekday from number helper
	public function get_weekday( $day_number = null ) {

		// 2.5.0: use internal get_weekdays function
		$weekdays = $this->get_weekdays();
		if ( !is_null( $day_number ) ) {
			return $weekdays[$day_number];
		}
		return $weekdays;
	}

	// ------------
	// Get Month(s)
	// ------------
	// 2.3.2: added get weekday from number helper
	public function get_month( $month_number = null ) {

		// 2.5.0: use internal get_months function
		$months = $this->get_months();
		if ( !is_null( $month_number ) ) {
			return $months[$month_number];
		}
		return $months;
	}

	// ---------------------
	// Get Schedule Weekdays
	// ---------------------
	// note: no translations here because used internally for sorting
	// 2.3.0: added to get schedule weekdays from start of week
	// 2.5.0: added time and timezone arguments for when weekstart is today
	public function get_schedule_weekdays( $weekstart = false, $time = false, $timezone = false ) {
		
		// --- get channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- maybe get start of the week ---
		if ( !$weekstart ) {
			$weekstart = get_option( 'start_of_week' );
			$weekstart = apply_filters( 'schedule_engine_schedule_weekday_start', $weekstart, $channel, $context );
			if ( ( '' != $context ) && is_string( $context ) ) {
				$weekstart = apply_filters( $context . '_schedule_weekday_start', $weekstart, $channel );
			}
		}

		// 2.5.0: use internal get_weekdays function
		$weekdays = $this->get_weekdays();

		// 2.3.2: also accept string format for weekstart
		if ( is_string( $weekstart ) ) {
			// 2.3.3.5: accept today as valid week start
			if ( 'today' == $weekstart ) {
				// 2.5.0: added time and timezone arguments
				$timezone = $timezone ? $timezone : $this->get_timezone();
				$weekstart = $this->get_time( 'day', $time, $timezone );
			}
			foreach ( $weekdays as $i => $weekday ) {
				if ( strtolower( $weekday ) == strtolower( $weekstart ) ) {
					$weekstart = $i;
				}
			}
		}

		// --- loop weekdays and reorder from start day ---
		$start = $before = $after = array();
		foreach ( $weekdays as $i => $weekday ) {
			// 2.3.2: allow matching of numerical index or weekday name
			if ( ( $i == $weekstart ) || ( $weekday == $weekstart ) ) {
				$start[] = $weekday;
			} elseif ( $i > $weekstart ) {
				$after[] = $weekday;
			} elseif ( $i < $weekstart ) {
				$before[] = $weekday;
			}
		}

		// --- put the days before the start day at the end ---
		$weekdays = array_merge( $start, $after );
		$weekdays = array_merge( $weekdays, $before );

		// --- filter and return ---
		$weekdays = apply_filters( 'schedule_engine_schedule_weekdays', $weekdays, $weekstart, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$weekdays = apply_filters( $context . '_schedule_weekdays', $weekdays, $weekstart, $channel );
		}
		return $weekdays;
	}

	// ----------------------
	// Get Schedule Weekdates
	// ----------------------
	// 2.3.0: added for date based calculations
	public function get_schedule_weekdates( $weekdays, $time = false, $timezone = false ) {

		// --- get channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- maybe get current time ---
		$time = $time ? $time : $this->get_now();
		$timezone = $timezone ? $timezone : $this->get_timezone();

		// 2.3.2: use timezone setting to get offset date
		if ( defined( 'SCHEDULE_ENGINE_USE_SERVER_TIMES' ) && SCHEDULE_ENGINE_USE_SERVER_TIMES ) {
			$today = date( 'l', $time );
		} else {
			// 2.3.3.9: fix to use get_time
			$today = $this->get_time( 'day', $time, $timezone );
		}

		// --- get weekday index for today ---
		$weekdates = array();
		foreach ( $weekdays as $i => $weekday ) {
			if ( $weekday == $today ) {
				$index = $i;
			}
		}
		foreach ( $weekdays as $i => $weekday ) {

			$diff = $i - $index;

			// 2.5.7: use datetime object and modify
			$times = $this->get_times( $time, $timezone );
			$date_time = $times['object'];
			// $weekdate_time = $time - ( $diff * 24 * 60 * 60 );
			if ( 0 == $diff ) {
				$weekdate_time = $date_time->format( 'U');
			} elseif ( $diff > 0 ) {
				$date_time->modify( '+' . $diff . ' day' );
				$weekdate_time = $date_time->format( 'U');
			} elseif ( $diff < 0 ) {
				$date_time->modify( (string) $diff . ' day' );
				$weekdate_time = $date_time->format( 'U');
			}
			// 2.3.2: include timezone adjustment
			if ( defined( 'SCHEDULE_ENGINE_USE_SERVER_TIMES' ) && SCHEDULE_ENGINE_USE_SERVER_TIMES ) {
				$weekdate = date( 'Y-m-d', $weekdate_time );
			} else {
				// 2.3.3.9: fix to use get_time
				$weekdate = $this->get_time( 'Y-m-d', $weekdate_time, $timezone );
			}
			$weekdates[$weekday] = $weekdate;
		}
		// echo '**A**'; print_r( $weekdates );

		// 2.4.0.4: check/fix for duplicate date crackliness (daylight saving?)
		foreach ( $weekdates as $day => $date ) {
			if ( isset( $prevdate ) && ( $prevdate == $date ) ) {
				$weekdates[$day] = $this->get_next_date( $date );
				$found = false;
				foreach ( $weekdates as $k => $v ) {
					if ( $found ) {
						$weekdates[$k] = $this->get_next_date( $v );
					}
					if ( $k == $day ) {
						$found = true;
					}
				}
			}
			$prevdate = $date;
		}
		// echo '**B**'; print_r( $weekdates );

		// --- filter and return ---
		$weekdates = apply_filters( 'schedule_engine_schedule_weekdates', $weekdates, $weekdays, $time, $timezone, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$weekdates = apply_filters( $context . '_schedule_weekdates', $weekdates, $weekdays, $time, $timezone, $channel );
		}
		
		return $weekdates;
	}

	// ------------
	// Get Next Day
	// ------------
	// 2.3.0: added get next day helper
	public function get_next_day( $day ) {
		// note: for internal use so not translated
		$day = trim( $day );
		if ( 'Sunday' == $day ) {
			return 'Monday';
		}
		if ( 'Monday' == $day ) {
			return 'Tuesday';
		}
		if ( 'Tuesday' == $day ) {
			return 'Wednesday';
		}
		if ( 'Wednesday' == $day ) {
			return 'Thursday';
		}
		if ( 'Thursday' == $day ) {
			return 'Friday';
		}
		if ( 'Friday' == $day ) {
			return 'Saturday';
		}
		if ( 'Saturday' == $day ) {
			return 'Sunday';
		}

		return '';
	}

	// ----------------
	// Get Previous Day
	// ----------------
	// 2.3.0: added get previous day helper
	public function get_previous_day( $day ) {
		// note: for internal use so not translated
		$day = trim( $day );
		if ( 'Sunday' == $day ) {
			return 'Saturday';
		}
		if ( 'Monday' == $day ) {
			return 'Sunday';
		}
		if ( 'Tuesday' == $day ) {
			return 'Monday';
		}
		if ( 'Wednesday' == $day ) {
			return 'Tuesday';
		}
		if ( 'Thursday' == $day ) {
			return 'Wednesday';
		}
		if ( 'Friday' == $day ) {
			return 'Thursday';
		}
		if ( 'Saturday' == $day ) {
			return 'Friday';
		}

		return '';
	}

	// -------------
	// Get Next Date
	// -------------
	// 2.3.2: added for more reliable calculations
	public function get_next_date( $date, $weekday = false ) {

		// note: this is used internally so timezone not needed
		$timedate = strtotime( $date );
		$timedate = $timedate + ( 24 * 60 * 60 );
		if ( $weekday ) {
			$day = $this->get_time( 'l', $timedate );
			if ( $day != $weekday ) {
				$i = 0;
				while ( $day != $weekday ) {
					$timedate = $timedate + ( 24 * 60 * 60 );
					$day = strtotime( 'l', $timedate );
					if ( 8 == $i ) {
						// - failback for while failure -
						$timedate = strtotime( $date );
						$next_date = date( 'next ' . $weekday, $timedate );
						return $next_date;
					}
					$i++;
				}
			}
		}
		$next_date = $this->get_time( 'Y-m-d', $timedate );
		return $next_date;
	}

	// -----------------
	// Get Previous Date
	// -----------------
	// 2.3.2: added for more reliable calculations
	public function get_previous_date( $date, $weekday = false ) {

		// note: this is used internally so timezone not used
		$timedate = strtotime( $date );
		$timedate = $timedate - ( 24 * 60 * 60 );
		if ( $weekday ) {
			$day = date( 'l', $timedate );
			if ( $day != $weekday ) {
				$i = 0;
				while ( $day != $weekday ) {
					$timedate = $timedate - ( 24 * 60 * 60 );
					$day = strtotime( 'l', $timedate );
					if ( 8 == $i ) {
						// - failback for while failure -
						$timedate = strtotime( $date );
						$previous_date = date( 'previous ' . $weekday, $timedate );
						return $previous_date;
					}
					$i++;
				}
			}
		}
		$previous_date = date( 'Y-m-d', $timedate );
		return $previous_date;
	}

	// ---------------------------
	// Convert Hour to Time Format
	// ---------------------------
	// (note: used with suffix for on-the-hour times)
	// 2.3.0: standalone function via master-schedule-default.php
	// 2.3.0: optionally add suffix for both time formats
	public function convert_hour( $hour, $timeformat = 24, $suffix = true ) {

		$hour = intval( $hour );

		// 2.3.0: handle next and previous hours (over 24 or below 0)
		if ( $hour < 0 ) {
			while ( $hour < 0 ) {
				$hour = $hour + 24;
			}
		}
		if ( $hour > 24 ) {
			while ( $hour > 24 ) {
				$hour = $hour - 24;
			}
		}

		if ( 24 === (int) $timeformat ) {
			// --- 24 hour time format ---
			if ( 24 == $hour ) {
				$hour = '00';
			} elseif ( $hour < 10 ) {
				$hour = '0' . $hour;
			}
			if ( $suffix ) {
				$hour .= ':00';
			}
		} elseif ( 12 === (int) $timeformat ) {
			// --- 12 hour time format ---
			// 2.2.7: added meridiem translations
			if ( ( $hour === 0 ) || ( 24 === $hour ) ) {
				// midnight
				$hour = '12';
				if ( $suffix ) {
					$hour .= ' ' . $this->translate_meridiem( 'am' );
				}
			} elseif ( $hour < 12 ) {
				// morning
				if ( $suffix ) {
					$hour .= ' ' . $this->translate_meridiem( 'am' );
				}
			} elseif ( 12 === $hour ) {
				// noon
				if ( $suffix ) {
					$hour .= ' ' . $this->translate_meridiem( 'pm' );
				}
			} elseif ( $hour > 12 ) {
				// after-noon
				$hour = $hour - 12;
				if ( $suffix ) {
					$hour .= ' ' . $this->translate_meridiem( 'pm' );
				}
			}
		}
		// 2.3.2: fix for possible double spacing crackliness
		$hour = str_replace( '  ', ' ', $hour );

		return $hour;
	}

	// ----------------------------
	// Convert Shift to Time Format
	// ----------------------------
	// 2.3.0: added to convert shift time to 24 hours (or back)
	public function convert_shift_time( $time, $timeformat = 24 ) {

		// note: timezone can be ignored here as just getting hours and minutes
		// 2.3.3.9: added space between date and time
		$timestamp = strtotime( $this->get_time( 'Y-m-d' ) . ' ' . $time );
		if ( 12 == (int) $timeformat ) {
			$time = $this->get_time( 'g:i a', $timestamp );
			// 2.5.6: removed translation here as for data not output
			// str_replace( 'am', $this->translate_meridiem( 'am' ), $time );
			// str_replace( 'pm', $this->translate_meridiem( 'pm' ), $time );
		} elseif ( 24 == (int) $timeformat ) {
			$time = $this->get_time( 'H:i', $timestamp );
		}

		return $time;
	}


	// --------------------
	// === Translations ===
	// --------------------

	// ----------
	// Get Locale
	// ----------
	public function get_locale() {
		
		// --- get channel and context ---
		$channel = $this->channel;
		$context = $this->context;

		// --- get set locale ---
		$locale = $this->locale;

		// --- filter and return ---
		$locale = apply_filters( 'schedule_engine_locale', $locale, $channel, $context );
		if ( ( '' != $context ) && is_string( $context ) ) {
			$locale = apply_filters( $context . '_locale', $locale, $channel );
		}
		return $locale;
	}

	// --------------
	// Get All Months
	// --------------
	public function get_months( $translate = false ) {
		$months = array(
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December',
		);
		if ( $translate ) {
			foreach ( $months as $i => $month ) {
				$months[$i] = $this->translate_month( $month );
			}
		}
		return $months;
	}
		
	// ------------
	// Get All Days
	// ------------
	public function get_weekdays( $translate = false ) {
		$days = array(
			'Sunday',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday'
		);
		if ( $translate ) {
			foreach ( $days as $i => $day ) {
				$days[$i] = $this->translate_weekday( $day );
			}
		}
		return $days;
	}

	// -------------
	// Get All Hours
	// -------------
	public function get_hours( $format = 24, $translate = false ) {
		$hours = array();
		if ( 24 === (int) $format ) {
			$hours = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23 );
		} elseif ( 12 === (int) $format ) {
			$hours = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 );
		}
		if ( $translate ) {
			foreach ( $hours as $i => $hour ) {
				$hours[$i] = number_format_i18n( $hour );	
			}
		}
		return $hours;
	}

	// ---------------
	// Get All Minutes
	// ---------------
	public function get_minutes( $translate = false ) {

		$mins = array();
		for ( $i = 0; $i < 60; $i++ ) {
			if ( $i < 10 ) {
				$min = '0' . $i;
			} else {
				$min = $i;
			}
			$mins[$i] = $min;
		}
		if ( $translate ) {
			foreach ( $mins as $i => $min ) {
				$mins[$i] = number_format_i18n( $min );	
			}
		}
		return $mins;
	}

	// -----------------
	// Translate Weekday
	// -----------------
	// important note: translated individually as cannot translate a variable
	// 2.2.7: use wp locale class to translate weekdays
	// 2.3.0: allow for abbreviated and long version changeovers
	// 2.3.2: default short to null for more flexibility
	public function translate_weekday( $weekday, $short = null ) {

		// 2.3.0: return empty for empty select option
		if ( empty( $weekday ) ) {
			return '';
		}

		// --- get weekdays ---
		$days = $this->get_weekday();

		// --- maybe switch locales ---
		// 2.5.0: added for possible locale switching
		$locale = $this->locale;
		if ( '' != $locale ) {
			$switcher = new WP_Locale_Switcher();
			$switcher->switch_to_locale( $locale );
		}
		global $wp_locale;

		// --- translate weekday ---
		// 2.3.2: optimized weekday translations
		foreach ( $days as $i => $day ) {
			$abbr = substr( $day, 0, 3 );
			if ( ( $weekday == $day ) || ( $weekday == $abbr ) ) {
				if ( ( !$short && !is_null( $short ) ) || ( is_null( $short ) && ( $weekday == $day ) ) ) {
					$weekday = $wp_locale->get_weekday( $i );
				} elseif ( $short || ( is_null( $short ) && ( $weekday == $abbr ) ) ) {
					$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( $i ) );
				}
			}
		}

		// --- fallback if day number supplied ---
		// 2.3.2: optimized day number fallback
		if ( !isset ($weekday ) ) {
			$daynum = intval( $weekday );
			if ( ( $daynum > -1 ) && ( $daynum < 7 ) ) {
				if ( $short ) {
					$weekday = $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( $daynum ) );
				} else {
					$weekday = $wp_locale->get_weekday( $daynum );
				}
			}
		}

		// --- maybe switch back to original locale ---
		// 2.5.0: added for possible locale switching
		if ( '' != $locale ) {
			$switcher->restore_current_locale();
		}
		return $weekday;
	}

	// ----------------
	// Replace Weekdays
	// ----------------
	// 2.3.2: to replace with translated weekdays in a time string
	public function replace_weekday( $string ) {

		$days = $this->get_weekday();
		foreach( $days as $day ) {
			$abbr = substr( $day, 0, 3 );
			if ( strstr( $string, $day ) ) {
				$translated = $this->translate_weekday( $day );
				$string = str_replace( $day, $translated, $string );
			} elseif ( strstr( $string, $abbr ) ) {
				$translated = $this->translate_weekday( $abbr );
				$string = str_replace( $abbr, $translated, $string );
			}
		}

		return $string;
	}

	// ---------------
	// Translate Month
	// ---------------
	// important note: translated individually as cannot translate a variable
	// 2.2.7: use wp locale class to translate months
	// 2.3.2: default short to null for more flexibility
	public function translate_month( $month, $short = null ) {

		// 2.3.0: return empty for empty select option
		if ( empty( $month ) ) {
			return '';
		}

		// --- get months ---
		$months = $this->get_month();

		// --- maybe switch locales ---
		// 2.5.0: added for possible locale switching
		$locale = $this->locale;
		if ( '' != $locale ) {
			$switcher = new WP_Locale_Switcher();
			$switcher->switch_to_locale( $locale );
		}
		global $wp_locale;

		// --- translate month ---
		// 2.3.2: optimized month translations
		foreach ( $months as $i => $fullmonth ) {
			$abbr = substr( $fullmonth, 0, 3 );
			if ( ( $month == $fullmonth ) || ( $month == $abbr ) ) {
				if ( ( !$short && !is_null( $short ) ) || ( is_null( $short ) && ( $month == $fullmonth ) ) ) {
					$month = $wp_locale->get_month( ( $i + 1 ) );
				} elseif ( $short || ( is_null( $short ) && ( $month == $abbr ) ) ) {
					// 2.5.6: fix to match month not weekday in condition above
					$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( ( $i + 1 ) ) );
				}
			}
		}

		// --- fallback if month number supplied ---
		// 2.3.2: optimized month number fallback
		if ( !isset( $month ) ) {
			$monthnum = intval( $month );
			if ( ( $monthnum > 0 ) && ( $monthnum < 13 ) ) {
				if ( $short ) {
					$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( $monthnum ) );
				} else {
					$month = $wp_locale->get_month( $monthnum );
				}
			}
		}

		// --- maybe switch back to original locale ---
		// 2.5.0: added for possible locale switching
		if ( '' != $locale ) {
			$switcher->restore_current_locale();
		}
		return $month;
	}

	// --------------
	// Replace Months
	// --------------
	// 2.3.2: to replace with translated months in a time string
	public function replace_month( $string ) {

		$months = $this->get_month();
		foreach( $months as $month ) {
			$abbr = substr( $month, 0, 3 );
			if ( strstr( $string, $month ) ) {
				$translated = $this->translate_month( $month );
				$string = str_replace( $month, $translated, $string );
			} elseif ( strstr( $string, $abbr ) ) {
				$translated = $this->translate_month( $abbr );
				$string = str_replace( $abbr, $translated, $string );
			}
		}

		return $string;
	}

	// ------------------
	// Translate Meridiem
	// ------------------
	// 2.2.7: added meridiem translation function
	public function translate_meridiem( $meridiem ) {

		// --- maybe switch locales ---
		// 2.5.0: added for possible locale switching
		$locale = $this->locale;
		if ( '' != $locale ) {
			$switcher = new WP_Locale_Switcher();
			$switcher->switch_to_locale( $locale );
		}

		// --- get translated meridiem ---
		global $wp_locale;
		if ( !is_object( $wp_locale ) ) {
			if ( !class_exists( 'WP_Locale' ) ) {
				require_once ABSPATH . WPINC . '/locale.php';
			}
			$wp_locale = new WP_Locale();
		}	
		$meridiem = $wp_locale->get_meridiem( $meridiem );

		// --- maybe switch back to original locale ---
		// 2.5.0: added for possible locale switching
		if ( '' != $locale ) {
			$switcher->restore_current_locale();
		}

		return $meridiem;
	}

	// ----------------
	// Replace Meridiem
	// ----------------
	// 2.3.2: added optimized meridiem replacement
	public function replace_meridiem( $string ) {

		if ( strstr( $string, 'am' ) ) {
			$am = $this->translate_meridiem( 'am' );
			$string = str_replace( 'am', $am, $string );
		}
		if ( strstr( $string, 'pm' ) ) {
			$pm = $this->translate_meridiem( 'pm' );
			$string = str_replace( 'pm', $pm, $string );
		}
		if ( strstr( $string, 'AM' ) ) {
			$am = $this->translate_meridiem( 'AM' );
			$string = str_replace( 'AM', $am, $string );
		}
		if ( strstr( $string, 'PM' ) ) {
			$pm = $this->translate_meridiem( 'PM' );
			$string = str_replace( 'PM', $pm, $string );
		}

		return $string;
	}

	// ---------------------
	// Translate Time String
	// ---------------------
	// 2.3.2: replace with translated month, day and meridiem in a string
	public function translate_time( $string ) {

		$string = $this->replace_meridiem( $string );
		$string = $this->replace_weekday( $string );
		$string = $this->replace_month( $string );

		return $string;
	}


	// -----------------
	// === Debugging ===
	// -----------------

	// -----------------------
	// Write to Debug Log File
	// -----------------------
	public function debug_log( $data, $echo = true, $filename = false ) {

		// --- maybe output debug info ---
		if ( $echo ) {
			echo '<span class="schedule-engine-debug" style="display:none;">' . esc_html( rtrim( $data ) ) . '</span>' . PHP_EOL;
		}

		// --- check for logging constant ---
		if ( !$filename && $this->debug_log ) {
			$filename = 'schedule-engine.log';
		} elseif ( false === $this->debug_log ) {
			$filename = false;
		}

		// --- maybe write to file ---
		if ( $filename ) {

			// --- maybe create debug directory ---
			if ( !is_dir( dirname( __FILE__ ) . '/debug' ) ) {
				wp_mkdir_p( dirname( __FILE__ ) . '/debug' );
			}

			// --- ensure line break at end of debug line ---
			$data = rtrim( $data ) . PHP_EOL;

			// --- write to debug file path ---
			$filepath = dirname( __FILE__ ) . '/debug/' . $filename;
			error_log( $data, 3, $filepath );
		}
	}

// --- close schedule engine class ---
}

