<?php

/* Sidebar Widget - Radio Clock
 * Displays the current radio time and user time
 * Since 2.3.0
 */

// === Clock Shortcode ===
// - Clock Display Shortcode
// === Clock Functions ===
// - Clock Javascript
// === Clock Widget Class ===


// -----------------------
// === Clock Shortcode ===
// -----------------------

// -----------------------
// Clock Display Shortcode
// -----------------------
add_shortcode( 'radio-clock', 'radio_station_clock_shortcode' );
function radio_station_clock_shortcode( $atts = array() ) {

	$clock_format = radio_station_get_setting( 'clock_format' );
	$defaults = array(
		'time' => $clock_format,
	);
	$atts = shortcode_atts( $defaults, $atts, 'radio-clock' );

	$clock = '<div class="radio-station-clock">';

	// --- server clock ---
	$clock .= '<div class="radio-station-server-clock">';
	$clock .= '<span class="radio-clock-title">';
	$clock .= esc_html( __( 'Radio Time', 'radio-station' ) );
	$clock .= '</span>: ';
	$clock .= '<span class="radio-server-time" data-format="' . esc_attr( $atts['time'] ) . '"></span> ';
	$clock .= '<span class="radio-server-date"></span> ';
	$clock .= '<span class="radio-server-zone"></span> ';
	$clock .= '</div>';

	// --- user clock ---
	$clock .= '<div class="radio-station-user-clock">';
	$clock .= '<span class="radio-clock-title">';
	$clock .= esc_html( __( 'Your Time', 'radio-station' ) );
	$clock .= '</span>: ';
	$clock .= '<span class="radio-user-time" data-format="' . esc_attr( $atts['time'] ) . '"></span> ';
	$clock .= '<span class="radio-user-date"></span> ';
	$clock .= '<span class="radio-user-zone"></span> ';
	$clock .= '</div>';

	$clock .= '</div>';

	// --- filter and return ---
	$clock = apply_filters( 'radio_station_clock', $clock );

	return $clock;
}


// === Clock Functions ===
// -----------------------
add_action( 'wp_enqueue_scripts', 'radio_station_localize_time_strings', 11 );
function radio_station_clock_javascript() {

	$js = "
	/* Convert Date Time to Time String */
	function radio_time_string(datetime) {
	
		h = datetime.getHours();
		m = datetime.getMinutes();
		s = datetime.getSeconds();
		if (m < 10) {m = '0'+m;}
		if (s < 10) {s = '0'+s;}
	
		if (radio.clock_format == '12') {
			if ( h < 12 ) {mer = radio.units_am;}
			if ( h == 0 ) {h = '12';}
			if ( h > 11 ) {mer = radio.units_pm;}
			if ( h > 12 ) {h = h - 12;}
		} else {
			mer = '';
			if ( h < 10 ) {h = '0'+h;}
		}
	
		timestring = h+':'+m+':'+s+' '+mer;
		return timestring;
	}
	
	/* Convert Date Time to Date String */
	function radio_date_string(datetime) {
	
		month = datetime.getMonth(); day = datetime.getDay(); d = datetime.getDate();
		datestring = radio.days[day]+' '+d+' '+radio.months[month];
		return datestring;
	}
	
	/* Update Current Time Clock */
	function radio_clock_date_time(init) {
	
		/* user date time */
		userdatetime = new Date();
		useroffset  = -(userdatetime.getTimezoneOffset());
		usertime = radio_time_string(userdatetime);
		userdate = radio_date_string(userdatetime);
	
		/* timezone offset */
		houroffset = parseInt(useroffset);
		if (houroffset == 0) {userzone = '[UTC]';}
		else {
			houroffset = houroffset / 60;
			if (houroffset > 0) {userzone = '[UTC+'+houroffset+']';}
			else {userzone = '[UTC'+houroffset+']';}
		}
	
		/* server date time */
		serverdatetime = new Date();
		servertime = serverdatetime.getTime();
		serveroffset = ( -(useroffset) + (radio.timezone_offset * 60) ) * 60;
		serverdatetime.setTime(userdatetime.getTime() + (serveroffset * 1000) );
		servertime = radio_time_string(serverdatetime);
		serverdate = radio_date_string(serverdatetime);
	
		/* server timezone code */
		if (typeof radio.timezone_code != 'undefined') {
			serverzone = '['+radio.timezone_code+']';
		} else {serverzone = '';}
	
		/* update server clocks */
		clocks = document.getElementsByClassName('radio-station-server-clock');
		for (i = 0; i < clocks.length; i++ ) {
			if (clocks[i]) {
				spans = clocks[i].children;
				for (j = 0; j < spans.length; j++) {
					if (spans[j].className == 'radio-server-time') {spans[j].innerHTML = servertime;}
					if (spans[j].className == 'radio-server-date') {spans[j].innerHTML = serverdate;}
					if (init) {
						if (spans[j].className == 'radio-server-zone') {spans[j].innerHTML = serverzone;}
					}
				}
			}
		}
	
		/* update user clocks */
		clocks = document.getElementsByClassName('radio-station-user-clock');
		for (i = 0; i < clocks.length; i++ ) {
			if (clocks[i]) {
				spans = clocks[i].children;
				for (j = 0; j < spans.length; j++) {
					if (spans[j].className == 'radio-user-time') {spans[j].innerHTML = usertime;}
					if (spans[j].className == 'radio-user-date') {spans[j].innerHTML = userdate;}
					if (init) {
						if (spans[j].className == 'radio-user-zone') {spans[j].innerHTML = userzone;}
					}
				}
			}
		}
	
		/* clock loop */
		setTimeout('radio_clock_date_time();', 1000);
		return true;
	}
	
	/* Start the Clock */
	setTimeout('radio_clock_date_time(true);', 1000);
	";

	wp_add_inline_script( 'radio-station', $js );
}

// --------------------------
// === Clock Widget Class ===
// --------------------------

class Radio_Clock_Widget extends WP_Widget {

	// --- construct widget class ---
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'Radio_Station_Widget',
			'description' => __( 'Display current radio and user times.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Radio Clock', 'radio-station' );
		parent::__construct( 'Radio_Clock_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );

		// TODO: widget options form

	}

	// --- update widget instance ---
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];

		// TODO: widget options update

		return $instance;
	}

	// --- output widget display ---
	public function widget( $args, $instance ) {

		// 2.3.0: added hide widget if empty option
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );

		// --- set shortcode attributes for display ---
		$atts = array(
			'time'   => '12',
			'day'    => 'long', // full or abbreviated
			'date'   => '',
			'widget' => 1,
		);

		// --- get default display output ---
		$output = radio_station_clock_shortcode( $atts );

		// --- check for widget output override ---
		$output = apply_filters( 'radio_station_radio_clock_widget_override', $output, $args, $atts );

		// --- open widget container --
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.OutputNotEscaped

		echo '<div class="widget">';

		// --- output widget title ---
		echo $args['before_title']; // phpcs:ignore WordPress.Security.OutputNotEscaped
		if ( !empty( $title ) ) {
			echo esc_html( $title );
		}
		echo $args['after_title']; // phpcs:ignore WordPress.Security.OutputNotEscaped

		// --- output widget display ---
		echo wp_kses_post( $output );

		// --- close widget container ---
		echo '</div>';

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.OutputNotEscaped

		// --- enqueue widget stylesheet in footer ---
		// (this means it will only load if widget is on page)
		radio_station_enqueue_style( 'widgets' );
	}
}

// --- register the widget ---
add_action( 'widgets_init', 'radio_station_register_radio_clock_widget' );
function radio_station_register_radio_clock_widget() {
	register_widget( 'Radio_Clock_Widget' );
}
