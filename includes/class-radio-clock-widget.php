<?php

/* Sidebar Widget - Radio Clock
 * Displays the current radio time and user time
 * Since 2.3.0
 */

class Radio_Clock_Widget extends WP_Widget {

	// --- use __constuct instead of Playlist_Widget ---
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

		// 2.3.0: added hide widget if empty option
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );


	}

	// --- update widget instance ---
	public function update( $new_instance, $old_instance ) {

		// 2.3.0: added hide widget if empty option
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];

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
			'day'    => 'long',
			'date'   => '',
			'widget' => 1,
		);

		// --- get default display output ---
		// 2.3.0: use shortcode to generate default widget output
		$output = radio_station_clock_shortcode( $atts );

		// --- check for widget output override ---
		// 2.3.0: added this override filter
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
// TODO: register widget when it is ready
// add_action( 'widgets_init', 'radio_station_register_radio_clock_widget' );
function radio_station_register_radio_clock_widget() {
	register_widget( 'Radio_Clock_Widget' );
}
