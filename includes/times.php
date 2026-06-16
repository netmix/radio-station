<?php

// ===========================
// === Radio Station Times ===
// ===========================
// 2.5.0: separated from support-functions.php
// 2.5.0: use new schedule_engine class

if ( !defined( 'ABSPATH' ) ) exit;

// - Set Server Timezone Mode
// === Time Conversions ===
// - Get Now
// - Get Timezone
// - Get Timezone Code
// - Get Timezone Options
// - Get Date Time Object
// - String To Time
// - Get Times
// - Get Time
// - Get Weekday(s)
// - Get Month(s)
// - Get Schedule Weekdays
// - Get Schedule Weekdates
// - Get Next Day
// - Get Previous Day
// - Get Next Date
// - Get Previous Date
// - Get All Hours
// - Convert Hour to Time Format
// - Convert Shift to Time Format
// === Time Translations ===
// - Translate Weekday
// - Replace Weekdays
// - Translate Month
// - Replace Months
// - Translate Meridiem
// - Replace Meridiems
// - Translate Time String


// ------------------------
// Set Server Timezone Mode
// ------------------------
add_action( 'plugins_loaded', 'radio_station_use_server_times', 9 );
function radio_station_use_server_times() {
	if ( defined( 'RADIO_STATION_USE_SERVER_TIMES' ) && !defined( 'SCHEDULE_ENGINE_USE_SERVER_TIMES' ) ) {
		define( 'SCHEDULE_ENGINE_USE_SERVER_TIMES', RADIO_STATION_USE_SERVER_TIMES );
	}
}


// ------------------------
// === Time Conversions ===
// ------------------------

// -------
// Get Now
// -------
// 2.3.2: added for current time consistency
function radio_station_get_now( $gmt = true ) {
	global $rs_se;
	// note: channel not explicitly needed here
	// 2.5.6: use stored now time to prevent data mismatch
	$now = $rs_se->now;
	// $now = $rs_se->get_now( $gmt );
	return $now;
}

// -----------
// Date Format
// -----------
// 2.5.6: added for localized date formatting output
function radio_station_date( $format, $timestamp = false, $timezone = false ) {

	// --- maybe get time / timezone ---
	if ( !$timestamp ) {
		$timestamp = radio_station_get_now();
	}
	if ( !$timezone ) {
		$timezone = radio_station_get_timezone();
	}
	$timezone = new DateTimeZone( $timezone );

	if ( function_exists( 'wp_date' ) ) {
		$date = wp_date( $format, $timestamp, $timezone );
	} else {
		// --- fallback to calculate timezone offset manually ---
		$date_time = new DateTime( $timestamp, $timezone );
		$offset = timezone_offset_get( $timezone, $date_time );
		$timestamp_with_offset = $timestamp + $offset;
		$date = date_i18n( $format, $timestamp_with_offset );
	}
	
	return $date;
}

// ------------
// Get Timezone
// ------------
// 2.3.2: added get timezone with fallback
// 2.5.0: added optional channel argument
function radio_station_get_timezone() {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	$timezone = radio_station_get_setting( 'timezone_location' );
	if ( !$timezone || ( '' == $timezone ) ) {
		$timezone = $rs_se->get_timezone();
	}

	// --- filter and return ---
	// 2.5.0: added filter and channel argument
	$timezone = apply_filters( 'radio_station_timezone', $timezone, $channel );
	return $timezone;
}

// -----------------
// Get Timezone Code
// -----------------
// note: this should only be used for display purposes
// (as the actual code used is based on timezone/location)
function radio_station_get_timezone_code( $timezone ) {
	global $rs_se;
	// note: channel not explicitly needed here
	return $rs_se->get_timezone_code( $timezone );
}

// --------------------
// Get Timezone Options
// --------------------
// ref: (based on) https://stackoverflow.com/a/17355238/5240159
// 2.5.18: add admin argument for optional translation
function radio_station_get_timezone_options( $include_wp_timezone = false, $admin = false ) {

	global $rs_se;

	// --- maybe get stored timezone options ---
	$options = get_transient( 'radio_station_timezone_options' );
	if ( !$options ) {
		$options = $rs_se->get_timezone_options( false );
		$expiry = 7 * 24 * 60 * 60;
		set_transient( 'radio_station_timezone_options', $options, $expiry );
	}

	// --- maybe add WordPress timezone (default) option ---
	if ( $include_wp_timezone ) {
		// 2.5.18: add operator for optional translation
		$wp_timezone = array( '' => $admin ? __( 'WordPress Timezone', 'radio-station' ) : '' );
		$options = array_merge( $wp_timezone, $options );
	}

	// --- filter and return ---
	$options = apply_filters( 'radio_station_get_timezone_options', $options, $include_wp_timezone );
	return $options;
}

// --------------------
// Get Date Time Object
// --------------------
// 2.3.2: added for consistent timezone conversions
function radio_station_get_date_time( $timestring, $timezone ) {
	global $rs_se;
	// note: channel not explicitly needed here
	return $rs_se->get_date_time( $timestring, $timezone );
}

// --------------
// String To Time
// --------------
// 2.3.2: added for timezone handling
// 2.5.0: added optional timezone argument for consistency
function radio_station_to_time( $timestring, $timezone = false ) {
	global $rs_se;
	// 2.5.6: remove unnecessary channel argument
	// $radio_station_data
	// $channel = $rs_se->channel = $radio_station_data['channel'];
	if ( !$timezone ) {
		$timezone = radio_station_get_timezone();
	}
	$time = $rs_se->to_time( $timestring, $timezone );
	return $time;
}

// ---------
// Get Times
// ---------
// 2.5.0: added for separation / abstraction
function radio_station_get_times( $time = false, $timezone = false ) {

	global $radio_station_data, $rs_se;
	// note: channel not explicitly needed here

	// --- maybe get current time ---
	if ( !$time ) {
		$time = radio_station_get_now();
	}

	// --- get offset time and date ---
	if ( defined( 'RADIO_STATION_USE_SERVER_TIMES' ) && RADIO_STATION_USE_SERVER_TIMES ) {
		$times = $rs_se->get_times( $time, false );
	} else {
		// --- maybe get timezone ---
		// 2.5.6: moved inside to replace duplicate line
		if ( !$timezone ) {
			$timezone = radio_station_get_timezone();
		}
		$times = $rs_se->get_times( $time, $timezone );
	}

	return $times;
}

// --------
// Get Time
// --------
// 2.3.2: added for timezone adjustments
function radio_station_get_time( $key = false, $time = false, $timezone = false ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	// --- maybe get timezone ---
	if ( !$timezone ) {
		$timezone = radio_station_get_timezone();
	}

	// --- get formatted times ---
	$times = radio_station_get_times( $time, $timezone );
	// if ( RADIO_STATION_DEBUG ) {
	// 	echo '<span style="display:none;">Times: ' . esc_html( print_r( $times, true ) ) . '</span>' . "\n";
	// }
	if ( !$key ) {
		return $times;
	}

	// --- return time key ---
	if ( array_key_exists( $key, $times ) ) {
		$time = $times[$key];
	} elseif ( isset( $times['object'] ) ) {
		$datetime = $times['object'];
		$time = $datetime->format( $key );
	} else {
		// 2.5.6: use radio_station_get_time instead of date
		$time = radio_station_get_time( $key, $time );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Time key not found: ' . esc_html( $key ) . '</span>' . "\n";
		}
	}
	return $time;
}

// --------------
// Get Weekday(s)
// --------------
// 2.3.2: added get weekday from number helper
function radio_station_get_weekday( $day_number = null ) {
	global $rs_se;
	return $rs_se->get_weekday( $day_number );
}

// ------------
// Get Month(s)
// ------------
// 2.3.2: added get weekday from number helper
function radio_station_get_month( $month_number = null ) {
	global $rs_se;
	return $rs_se->get_month( $month_number );
}

// ---------------------
// Get Schedule Weekdays
// ---------------------
// note: no translations here because used internally for sorting
// 2.3.0: added to get schedule weekdays from start of week
function radio_station_get_schedule_weekdays( $weekstart = false ) {
	global $radio_station_data, $rs_se;
	$rs_se->channel = $channel = $radio_station_data['channel'];
	$weekdays = $rs_se->get_schedule_weekdays( $weekstart );
	return $weekdays;
}

// ----------------------
// Get Schedule Weekdates
// ----------------------
// 2.3.0: added for date based calculations
function radio_station_get_schedule_weekdates( $weekdays, $time = false, $timezone = false, $channel = '' ) {

	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];

	if ( !$time ) {
		$time = radio_station_get_now();
	}
	if ( !$timezone ) {
		$timezone = radio_station_get_timezone();
	}

	$weekdates = $rs_se->get_schedule_weekdates( $weekdays, $time, $timezone );
	return $weekdates;
}

// ------------
// Get Next Day
// ------------
// 2.3.0: added get next day helper
function radio_station_get_next_day( $day ) {
	global $rs_se;
	return $rs_se->get_next_day( $day );
}

// ----------------
// Get Previous Day
// ----------------
// 2.3.0: added get previous day helper
function radio_station_get_previous_day( $day ) {
	global $rs_se;
	return $rs_se->get_previous_day( $day );
}

// -------------
// Get Next Date
// -------------
// 2.3.2: added for more reliable calculations
function radio_station_get_next_date( $date, $weekday = false ) {
	global $rs_se;
	return $rs_se->get_next_date( $date, $weekday );
}

// -----------------
// Get Previous Date
// -----------------
// 2.3.2: added for more reliable calculations
function radio_station_get_previous_date( $date, $weekday = false ) {
	global $rs_se;
	return $rs_se->get_previous_date( $date, $weekday );
}

// ------------
// Get All Days
// ------------
function radio_station_get_days( $translate = false ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->get_weekdays( $translate );
}

// -------------
// Get All Hours
// -------------
function radio_station_get_hours( $format = 24, $translate = false ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->get_hours( $format, $translate );
}

// ---------------
// Get All Minutes
// ---------------
function radio_station_get_minutes( $translate = false ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->get_minutes( $translate );
}


// ---------------------------
// Convert Hour to Time Format
// ---------------------------
// (note: used with suffix for on-the-hour times)
// 2.3.0: standalone function via master-schedule-default.php
// 2.3.0: optionally add suffix for both time formats
function radio_station_convert_hour( $hour, $timeformat = 24, $suffix = true ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->convert_hour( $hour, $timeformat, $suffix );
}

// ----------------------------
// Convert Shift to Time Format
// ----------------------------
// 2.3.0: added to convert shift time to 24 hours (or back)
function radio_station_convert_shift_time( $time, $timeformat = 24 ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->convert_shift_time( $time, $timeformat );
}


// --------------------
// === Translations ===
// --------------------

// ----------
// Get Locale
// ----------
function radio_station_get_locale() {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	$locale = $rs_se->get_locale();
	return $locale;
}

// -----------------
// Translate Weekday
// -----------------
// important note: translated individually as cannot translate a variable
// 2.2.7: use wp locale class to translate weekdays
// 2.3.0: allow for abbreviated and long version changeovers
// 2.3.2: default short to null for more flexibility
function radio_station_translate_weekday( $weekday, $short = null ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->translate_weekday( $weekday, $short );
}

// ----------------
// Replace Weekdays
// ----------------
// 2.3.2: to replace with translated weekdays in a time string
function radio_station_replace_weekday( $weekday ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->replace_weekday( $weekday );
}

// ---------------
// Translate Month
// ---------------
// important note: translated individually as cannot translate a variable
// 2.2.7: use wp locale class to translate months
// 2.3.2: default short to null for more flexibility
function radio_station_translate_month( $month, $short = null ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->translate_month( $month, $short );
}

// --------------
// Replace Months
// --------------
// 2.3.2: to replace with translated months in a time string
function radio_station_replace_month( $month ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->replace_month( $month );
}

// ------------------
// Translate Meridiem
// ------------------
// 2.2.7: added meridiem translation function
function radio_station_translate_meridiem( $meridiem ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->translate_meridiem( $meridiem );
}

// ----------------
// Replace Meridiem
// ----------------
// 2.3.2: added optimized meridiem replacement
function radio_station_replace_meridiem( $meridiem ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->replace_meridiem( $meridiem );
}

// ---------------------
// Translate Time String
// ---------------------
// 2.3.2: replace with translated month, day and meridiem in a string
function radio_station_translate_time( $time ) {
	global $radio_station_data, $rs_se;
	$channel = $rs_se->channel = $radio_station_data['channel'];
	return $rs_se->translate_time( $time );
}
