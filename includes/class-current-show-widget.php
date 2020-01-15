<?php

/* Sidebar Widget - DJ On Air
 * Displays the current on-air show/DJ
 * Since 2.1.1
 */

class DJ_Widget extends WP_Widget {

	// --- use __contruct instead of DJ_Widget ---
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'DJ_Widget',
			'description' => __( 'The currently playing on-air Show.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Current Show On-Air', 'radio-station' );
		parent::__construct( 'DJ_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = $instance['title'];
		$display_djs = isset( $instance['show_desc'] ) ? $instance['display_djs'] : false;
		$djavatar = isset( $instance['djavatar'] ) ? $instance['djavatar'] : false;
		$default = isset( $instance['default'] ) ? $instance['default'] : '';
		$link = isset( $instance['link'] ) ? $instance['link'] : false;
		$time = isset( $instance['time'] ) ? $instance['time'] : 12;
		$show_sched = isset( $instance['show_sched'] ) ? $instance['show_sched'] : false;
		$show_playlist = isset( $instance['show_playlist'] ) ? $instance['show_playlist'] : false;
		$show_all_sched = isset( $instance['show_all_sched'] ) ? $instance['show_all_sched'] : false;
		$show_desc = isset( $instance['show_desc'] ) ? $instance['show_desc'] : false;

		// 2.2.4: added title position, avatar width and DJ link options
		$title_position = isset( $instance['title_position'] ) ? $instance['title_position'] : 'below';
		$avatar_width = isset( $instance['avatar_width'] ) ? $instance['avatar_width'] : '';
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';

		// 2.3.0: convert template style code to straight php echo
		echo '
		<p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">: 
			' . esc_html( __( 'Title', 'radio-station' ) ) . '
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" />
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'link' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link' ) ) . '" type="checkbox" ' . checked( $link, true, false ) . ' />
				' . esc_html( __( 'Link the title to the Show page', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'title_position' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'title_position' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title_position' ) ) . '">';

		$positions = array(
			'above' => __( 'Above', 'radio-station' ),
			'left'  => __( 'Left', 'radio-station' ),
			'right' => __( 'Right', 'radio-station' ),
			'below' => __( 'Below', 'radio-station' ),
		);
		foreach ( $positions as $position => $label ) {
			echo '<option value="' . esc_attr( $position ) . '" ' . selected( $title_position, $position, false ) . '>' . esc_html( $label ) . '</option>';
		}

		echo '</select>
				' . esc_html( __( 'Show Title Position (relative to Avatar)', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'djavatar' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'djavatar' ) ) . '" name="' . esc_attr( $this->get_field_name( 'djavatar' ) ) . '" type="checkbox" ' . checked( $djavatar, true, false ) . '/>
				' . esc_html( __( 'Display Show Avatar', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'avatar_width' ) ) . '">: 
				' . esc_html( __( 'Avatar Width', 'radio-station' ) ) . '
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'avatar_width' ) ) . '" name="' . esc_attr( $this->get_field_name( 'avatar_width' ) ) . '" type="text" value="' . esc_attr( $avatar_width ) . '" />
			</label>
			<small>' . esc_html( __( 'Width of Show Avatar (in pixels, default full width)', 'radio-station' ) ) . '</small>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'display_djs' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'display_djs' ) ) . '" name="' . esc_attr( $this->get_field_name( 'display_djs' ) ) . '" type="checkbox" ' . checked( $display_djs, true, false ) . '/>
				' . esc_html( __( 'Display names of the DJs on the Show', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'link_djs' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link_djs' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link_djs' ) ) . '" type="checkbox" ' . checked( $link_djs, true, false ) . '/>
				' . esc_html( __( 'Link DJ names to author pages', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_sched' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_sched' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_sched' ) ) . '" type="checkbox" ' . checked( $show_sched, true, false ) . '/>
				' . esc_html( __( 'Display schedule info for this show', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_all_sched' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_all_sched' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_all_sched' ) ) . '" type="checkbox" ' . checked( $show_all_sched, true, false ) . '/>
				' . esc_html( __( 'Display multiple schedules (if show airs more than once per week)', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_desc' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_desc' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_desc' ) ) . '" type="checkbox" ' . checked( $show_desc, true, false ) . '/>
				' . esc_html( __( 'Display description of show', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_playlist' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_playlist' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_playlist' ) ) . '" type="checkbox" ' . checked( $show_playlist, true, false ) . '/>
				' . esc_html( __( "Display link to show's playlist", 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'default' ) ) . '">
				' . esc_html( __( 'No Show Display Text', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'default' ) ) . '" name="' . esc_attr( $this->get_field_name( 'default' ) ) . '" type="text" value="' . esc_attr( $default ) . '" />
			</label>
			<small>' . esc_html( __( 'If no Show is scheduled for the current time, display this text.', 'radio-station' ) ) . '</small>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'time' ) ) . '">' . esc_html( __( 'Time Format', 'radio-station' ) ) . ':<br />
				<select id="' . esc_attr( $this->get_field_id( 'time' ) ) . '" name="' . esc_attr( $this->get_field_name( 'time' ) ) . '">
					<option value="12" ' . selected( $time, 12, false ) . '>' . esc_html( __( '12 Hour', 'radio-station' ) ) . '</option>
					<option value="24" ' . selected( $time, 24, false ) . '>' . esc_html( __( '24 Hour', 'radio-station' ) ) . '</option>
				</select>
			</label>
			<br />
			<small>' . esc_html( __( 'Choose time format for displayed schedules', 'radio-station' ) ) . '</small>
		</p>';
	}

	// --- update widget instance values ---
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['display_djs'] = ( isset( $new_instance['display_djs'] ) ? 1 : 0 );
		$instance['djavatar'] = ( isset( $new_instance['djavatar'] ) ? 1 : 0 );
		$instance['link'] = ( isset( $new_instance['link'] ) ? 1 : 0 );
		$instance['default'] = $new_instance['default'];
		$instance['time'] = $new_instance['time'];
		// 2.2.7: fix checkbox value saving
		$instance['show_sched'] = ( isset( $new_instance['show_sched'] ) ? 1 : 0 );
		$instance['show_playlist'] = ( isset( $new_instance['show_playlist'] ) ? 1 : 0 );
		$instance['show_all_sched'] = ( isset( $new_instance['show_all_sched'] ) ? 1 : 0 );
		$instance['show_desc'] = ( isset( $new_instance['show_desc'] ) ? 1 : 0 );

		// 2.2.4: added title position and avatar width settings
		$instance['title_position'] = $new_instance['title_position'];
		$instance['avatar_width'] = $new_instance['avatar_width'];
		$instance['link_djs'] = ( isset( $new_instance['link_djs'] ) ? 1 : 0 );

		return $instance;

	}

	// --- widget output ---
	public function widget( $args, $instance ) {

		// 2.3.0: filter widget_title whether empty or not
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );
		$display_djs = $instance['display_djs'];
		$djavatar = $instance['djavatar'];
		$link = $instance['link'];
		$default = empty( $instance['default'] ) ? '' : $instance['default'];
		$time = empty( $instance['time'] ) ? '' : $instance['time'];
		$show_sched = $instance['show_sched'];
		$show_playlist = $instance['show_playlist'];
		// keep the default settings for people updating from 1.6.2 or earlier
		$show_all_sched = isset( $instance['show_all_sched'] ) ? $instance['show_all_sched'] : false;
		// keep the default settings for people updating from 2.0.12 or earlier
		$show_desc = isset( $instance['show_desc'] ) ? $instance['show_desc'] : false;

		// 2.2.4: added title position, avatar width and DJ link settings
		$position = empty( $instance['title_position'] ) ? 'bottom' : $instance['title_position'];
		$width = empty( $instance['avatar_width'] ) ? '' : $instance['avatar_width'];
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';

		// --- set shortcode attributes ---
		// 2.3.0: map widget options to shortcode attributes
		$atts = array(
			'title'          => $title, //
			'display_djs'    => $display_djs,
			'show_avatar'    => $djavatar,
			'show_link'      => $link,
			// 'default_name'	=> '',
			'time'           => $time,
			'show_sched'     => $show_sched,
			'show_playlist'  => $show_playlist,
			'show_all_sched' => $show_all_sched,
			'show_desc'      => $show_desc,
			// new widget options
			'title_position' => $position,
			'avatar_width'   => $width,
			'link_djs'       => $link_djs,
			'widget'         => 1,
		);

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.OutputNotEscaped

		// --- open widget container ---
		echo '<div class="widget">';

		// --- widget title ---
		echo $args['before_title']; // phpcs:ignore WordPress.Security.OutputNotEscaped
		if ( !empty( $title ) ) {
			echo esc_html( $title );
		}
		echo $args['after_title']; // phpcs:ignore WordPress.Security.OutputNotEscaped

		// --- get default display output ---
		// 2.3.0: use shortcode to generate default widget output
		$output = radio_station_current_show_shortcode( $atts );

		// --- check for widget output override ---
		// 2.3.0: added this override filter
		$output = apply_filters( 'radio_station_current_show_widget_override', $output, $args, $atts );

		// --- output widget display ---
		if ( $output ) {
			echo $output; // phpcs:ignore WordPress.Security.OutputNotEscaped
		}

		echo '</div>';

		// --- enqueue widget stylesheet in footer ---
		// (this means it will only load if widget is on page)
		// 2.2.4: renamed djonair.css to widgets.css and load for all widgets
		// 2.3.0: widgets.css prefixed to rs-widgets.css
		// 2.3.0: use abstracted method for enqueueing widget styles
		radio_station_enqueue_style( 'widgets' );

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.OutputNotEscaped
	}
}

// --- register the widget ---
// 2.2.7: revert anonymous function usage for backwards compatibility
add_action( 'widgets_init', 'radio_station_register_dj_widget' );
function radio_station_register_dj_widget() {
	register_widget( 'DJ_Widget' );
}
