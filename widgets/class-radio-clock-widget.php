<?php

// --------------------------
// === Radio Clock Widget ===
// --------------------------
// @since 2.3.2

if ( !defined( 'ABSPATH' ) ) exit;

class Radio_Clock_Widget extends WP_Widget {

	// --- construct widget class ---
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'Radio_Clock_Widget',
			'description' => __( 'Display current radio and user times.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Radio Clock', 'radio-station' );
		parent::__construct( 'Radio_Clock_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => __( 'Radio Clock', 'radio-station' ) ) );

		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$time_format = isset( $instance['time'] ) ? $instance['time'] : '';
		$day = isset( $instance['day'] ) ? $instance['day'] : 'full';
		$date = isset( $instance['date'] ) ? $instance['date'] : 1;
		$month = isset( $instance['month'] ) ? $instance['month'] : 'full';
		$seconds = isset( $instance['seconds'] ) ? $instance['seconds'] : 0;
		$zone = isset( $instance['zone'] ) ? $instance['zone'] : 1;

		// --- widget options form ---
		// 2.5.0: use fields array
		$fields = array();

		// --- Widget Title ---
		$fields['title'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">
				' . esc_html( __( 'Widget Title', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '">
			</label>
		</p>';

		// --- Day Format Display ---
		$fields['day_display'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'day' ) ) . '">' . esc_html( __( 'Day Display Format', 'radio-station' ) ) . ':<br />
				<select id="' . esc_attr( $this->get_field_id( 'day' ) ) . '" name="' . esc_attr( $this->get_field_name( 'day' ) ) . '">
					<option value="full" ' . selected( $day, 'full', false ) . '>' . esc_html( __( 'Full', 'radio-station' ) ) . '</option>
					<option value="short" ' . selected( $day, 'short', false ) . '>' . esc_html( __( 'Short', 'radio-station' ) ) . '</option>
					<option value="none" ' . selected( $day, 'none', false ) . '>' . esc_html( __( 'None', 'radio-station' ) ) . '</option>
				</select>
			</label>
		</p>';

		// --- Display Date ---
		$fields['date_display'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'date' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'date' ) ) . '" name="' . esc_attr( $this->get_field_name( 'date' ) ) . '" type="checkbox" ' . checked( $date, true, false ) . '>
				' . esc_html( __( 'Include Date Display?', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Month Format Display ---
		$fields['month_display'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'month' ) ) . '">' . esc_html( __( 'Month Display Format', 'radio-station' ) ) . ':<br />
				<select id="' . esc_attr( $this->get_field_id( 'month' ) ) . '" name="' . esc_attr( $this->get_field_name( 'month' ) ) . '">
					<option value="full" ' . selected( $month, 'full', false ) . '>' . esc_html( __( 'Full', 'radio-station' ) ) . '</option>
					<option value="short" ' . selected( $month, 'short', false ) . '>' . esc_html( __( 'Short', 'radio-station' ) ) . '</option>
					<option value="none" ' . selected( $month, 'none', false ) . '>' . esc_html( __( 'None', 'radio-station' ) ) . '</option>
				</select>
			</label>
		</p>';

		// --- Display Seconds ---
		$fields['seconds'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'seconds' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'seconds' ) ) . '" name="' . esc_attr( $this->get_field_name( 'seconds' ) ) . '" type="checkbox" ' . checked( $seconds, true, false ) . '>
				' . esc_html( __( 'Include seconds display?', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Time Format ---
		$fields['time_format'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'time' ) ) . '">' . esc_html( __( 'Time Format Display', 'radio-station' ) ) . ':<br />
				<select id="' . esc_attr( $this->get_field_id( 'time' ) ) . '" name="' . esc_attr( $this->get_field_name( 'time' ) ) . '">
					<option value="" ' . selected( $time_format, '', false ) . '>' . esc_html( __( 'Default', 'radio-station' ) ) . '</option>
					<option value="12" ' . selected( $time_format, 12, false ) . '>' . esc_html( __( '12 Hour', 'radio-station' ) ) . '</option>
					<option value="24" ' . selected( $time_format, 24, false ) . '>' . esc_html( __( '24 Hour', 'radio-station' ) ) . '</option>
				</select>
			</label>
		</p>';

		// --- Timezone Display ----
		$fields['timezone_display'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'zone' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'zone' ) ) . '" name="' . esc_attr( $this->get_field_name( 'zone' ) ) . '" type="checkbox" ' . checked( $zone, true, false ) . '>
				' . esc_html( __( 'Include timezone display?', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- filter and output ---
		// 2.5.0: added missing field filters for consistency
		$fields = apply_filters( 'radio_station_clock_widget_fields_list', $fields, $this, $instance );
		$fields_html = implode( '', $fields );
		$fields_html = apply_filters( 'radio_station_clock_widget_fields', $fields_html, $this, $instance );
		// 2.5.0: use wp_kses on field settings output
		$allowed = radio_station_allowed_html( 'content', 'settings' );
		echo wp_kses( $fields_html, $allowed );
	}

	// --- update widget instance ---
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];

		// --- update widget options ---
		$instance['time'] = isset( $new_instance['time'] ) ? $new_instance['time'] : 12;
		$instance['day'] = isset( $new_instance['day'] ) ? $new_instance['day'] : 'full';
		$instance['date'] = isset( $new_instance['date'] ) ? 1 : 0;
		$instance['month'] = isset( $new_instance['month'] ) ? $new_instance['month'] : 'full';
		$instance['seconds'] = isset( $new_instance['seconds'] ) ? 1 : 0;
		$instance['zone'] = isset( $new_instance['zone'] ) ? 1 : 0;

		// 2.5.18: save (or preserve) channel setting
		if ( isset( $new_instance['channel'] ) ) {
			$instance['channel'] = $new_instance['channel'];
		} elseif ( isset( $old_instance['channel'] ) ) {
			$instance['channel'] = $old_instance['channel'];
		}

		// 2.5.0: filter widget update instance
		$instance = apply_filters( 'radio_station_clock_widget_update', $instance, $new_instance, $old_instance );
		return $instance;
	}

	// --- output widget display ---
	public function widget( $args, $instance ) {

		global $radio_station_data;

		// --- set widget id ---
		// 2.3.3.9: added unique widget id
		// 2.5.0: simplify widget id setting
		if ( !isset( $radio_station_data['widgets']['clock'] ) ) {
			$radio_station_data['widgets']['clock'] = 0;
		}
		$radio_station_data['widgets']['clock']++;
		$id = $radio_station_data['widgets']['clock'];

		// 2.3.0: added hide widget if empty option
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );

		$time = $instance['time'];
		$day = $instance['day'];
		$date = $instance['date'];
		$zone = $instance['zone'];
		$month = $instance['month'];
		$seconds = $instance['seconds'];

		// --- set shortcode attributes for display ---
		$atts = array(
			// 2.5.18: change time key to time_format
			// --- clock options ---
			'time_format'    => $time,
			'day'     => $day,
			'date'    => $date,
			'month'   => $month,
			'zone'    => $zone,
			'seconds' => $seconds,
			// --- widget data ---
			'widget'  => 1,
			'id'      => $id,
		);
		
		// TODO: added attributes fields
		/* 'timezone'    => $timezone,
		'code'        => 1,
		'region'      => 1,
		'location'    => 1,
		'offset'      => 1, */

		// 2.3.3.9: add missing filter for clock widget attributes
		$atts = apply_filters( 'radio_station_clock_widget_atts', $atts, $instance );

		// 2.5.0: get context filtered allowed HTML
		$allowed = radio_station_allowed_html( 'widget', 'radio-clock' );

		// --- before widget ---
		// 2.5.0: use wp_kses on output
		echo wp_kses( $args['before_widget'], $allowed );

		// --- open widget container ---
		// 2.3.0: add instance id and class to widget container
		echo '<div id="radio-clock-widget-' . esc_attr( $id ) . '" class="radio-clock-widget widget">' . "\n";

			// --- output widget title ---
			// 2.5.0: use wp_kses on output
			echo wp_kses( $args['before_title'], $allowed );
			if ( !empty( $title ) ) {
				echo wp_kses( $title, $allowed );
			}
			// 2.5.0: use wp_kses on output
			echo wp_kses( $args['after_title'], $allowed );

			echo '<div id="radio-clock-widget-contents-' . esc_attr( $id ) . '" class="radio-clock-wrap">' . "\n";

				// --- get default display output ---
				$output = radio_station_clock_shortcode( $atts );

				// --- check for widget output override ---
				$output = apply_filters( 'radio_station_radio_clock_widget_override', $output, $args, $atts );

				// --- output widget display ---
				// TODO: test wp_kses on output ?
				// echo wp_kses( $output, $allowed );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $output;

			// --- close widget contents ---
			echo '</div>' . "\n";

		// --- close widget container ---
		echo '</div>' . "\n";

		// --- after widget ---
		// 2.5.0: use wp_kses on output
		echo wp_kses( $args['after_widget'], $allowed );

		// --- enqueue widget stylesheet in footer ---
		// (this means it will only load if widget is on page)
		// 2.4.0.4: fix to load shortcode stylesheet
		radio_station_enqueue_style( 'shortcodes' );

	}
}

// --- register the widget ---
add_action( 'widgets_init', 'radio_station_register_radio_clock_widget' );
function radio_station_register_radio_clock_widget() {
	register_widget( 'Radio_Clock_Widget' );
}
