<?php

/* Sidebar Widget - Upcoming DJ
 * Displays the the next show(s)/DJ(s) in the schedule
 * Since 2.1.1
 */

// note: widget class name to remain unchanged for backwards compatibility
class DJ_Upcoming_Widget extends WP_Widget {

	// use __construct instead of DJ_Upcoming_Widget
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'DJ_Upcoming_Widget',
			'description' => __( 'Display the upcoming Shows.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Upcoming Shows', 'radio-station' );
		parent::__construct( 'DJ_Upcoming_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = $instance['title'];
		$display_djs = isset( $instance['display_djs'] ) ? $instance['display_djs'] : false;
		$djavatar = isset( $instance['djavatar'] ) ? $instance['djavatar'] : false;
		$default = isset( $instance['default'] ) ? $instance['default'] : '';
		$link = isset( $instance['link'] ) ? $instance['link'] : false;
		$limit = isset( $instance['limit'] ) ? $instance['limit'] : 1;
		$time = isset( $instance['time'] ) ? $instance['time'] : 12;
		$show_sched = isset( $instance['show_sched'] ) ? $instance['show_sched'] : false;

		// 2.2.4: added title position, avatar width and DJ link options
		$title_position = isset( $instance['title_position'] ) ? $instance['title_position'] : 'right';
		$avatar_width = isset( $instance['avatar_width'] ) ? $instance['avatar_width'] : '75';
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';

		// 2.3.0: convert template style code to straight php echo
		$fields = '
		<p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">
				' . esc_html( __( 'Title', 'radio-station' ) ) . '
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" />
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'link' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link' ) ) . '" type="checkbox"' . checked( $link, true, false ) . '/>
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
			$fields .= '<option value="' . esc_attr( $position ) . '"' . selected( $title_position, $position, false ) . '>' . esc_html( $label ) . '</option>';
		}
		$fields .= '</select>
			' . esc_html( __( 'Show Title Position (relative to Avatar)', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'djavatar' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'djavatar' ) ) . '" name="' . esc_attr( $this->get_field_name( 'djavatar' ) ) . '" type="checkbox" ' . checked( $djavatar, true, false ) . '/>
				' . esc_html( __( 'Display Show Avatars', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'avatar_width' ) ) . '">
				' . esc_html( __( 'Avatar Width', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'avatar_width' ) ) . '" name="' . esc_attr( $this->get_field_name( 'avatar_width' ) ) . '" type="text" value="' . esc_attr( $avatar_width ) . '" />
			</label>
			<small>' . esc_html( __( 'Width of Show Avatars (in pixels, default 75px)', 'radio-station' ) ) . '</small>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'display_djs' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'display_djs' ) ) . '" name="' . esc_attr( $this->get_field_name( 'display_djs' ) ) . '" type="checkbox" ' . checked( $display_djs, true, false ) . '/>
				' . esc_html( __( 'Display names of the DJs on the show', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'link_djs' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link_djs' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link_djs' ) ) . '" type="checkbox" ' . checked( $link_djs, true, false ) . '/>
				' . esc_html( __( 'Link DJ names to author pages', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'default' ) ) . '">
				' . esc_html( __( 'No Additional Schedules', 'radio-station' ) ) . '
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'default' ) ) . '" name="' . esc_attr( $this->get_field_name( 'default' ) ) . '" type="text" value="' . esc_attr( $default ) . '" />
			</label>
			<small>' . esc_html_e( 'If no Show is scheduled for the current time, display this text.', 'radio-station' ) . '</small>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_sched' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_sched' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_sched' ) ) . '" type="checkbox"' . checked( $show_sched, true, false ) . '/>
				' . esc_html( __( 'Display schedule info for this show', 'radio-station' ) ) . '
			</label>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'limit' ) ) . '">
				' . esc_html( __( 'Limit', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'limit' ) ) . '" name="' . esc_attr( $this->get_field_name( 'limit' ) ) . '" type="text" value="' . esc_attr( $limit ) . '" />
			</label>
			<small>' . esc_html( __( 'Number of upcoming Shows to display.', 'radio-station' ) ) . '</small>
		</p>

		<p>
			<label for="' . esc_attr( $this->get_field_id( 'time' ) ) . '">' . esc_html( __( 'Time Format', 'radio-station' ) ) . ':<br />
				<select id="' . esc_attr( $this->get_field_id( 'time' ) ) . '" name="' . esc_attr( $this->get_field_name( 'time' ) ) . '">
					<option value="12" ' . selected( $time, 12, false ) . '>' . esc_html( __( '12 Hour', 'radio-station' ) ) . '</option>
					<option value="24" ' . selected( $time, 24, false ) . '>' . esc_html( __( '24 Hour', 'radio-station' ) ) . '</option>
				</select>
			</label>
			<br />
			<small>' . esc_html( __( 'Choose time format for displayed schedules.', 'radio-station' ) ) . '	</small>
		</p>
		
		<p>
			<label for="' . esc_attr( $this->get_field_id( 'countdown' ) ) . '">
			<input id="' .esc_attr( $this->get_field_id( 'countdown' ) ) . '" name="' . esc_attr( $this->get_field_name( 'countdown' ) ) . '" type="checkbox" ' . checked( $countdown, true, false ) . '/>
				' . esc_html( __( 'Display Countdown Timer', 'radio-station' ) ) . '
			</label>
        </p>';

		// --- filter and output ---
		$fields = apply_filters( 'radio_station_upcoming_shows_widget_fields', $fields, $this, $instance );
		echo $fields;
	}

	// --- update widget instance ---
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['display_djs'] = isset( $new_instance['display_djs'] ) ? 1 : 0;
		$instance['djavatar'] = isset( $new_instance['djavatar'] ) ? 1 : 0;
		$instance['link'] = isset( $new_instance['link'] ) ? 1 : 0;
		$instance['default'] = $new_instance['default'];
		$instance['limit'] = $new_instance['limit'];
		$instance['time'] = $new_instance['time'];
		$instance['show_sched'] = isset( $new_instance['show_sched'] ) ? 1 : 0;

		// 2.2.4: added title position, avatar width and DJ link settings
		// 2.3.0: added countdown display option
		$instance['title_position'] = $new_instance['title_position'];
		$instance['avatar_width'] = $new_instance['avatar_width'];
		$instance['link_djs'] = isset( $new_instance['link_djs'] ) ? 1 : 0;
		$instance['countdown'] = isset( $new_instance['countdown'] ) ? 1 : 0;

		// 2.3.0: added widget filter instance to update
		$instance = apply_filters( 'radio_station_upcoming_shows_widget_update', $instance, $new_instance, $old_instance );
		return $instance;
	}

	// --- output widget display ---
	public function widget( $args, $instance ) {

		global $radio_station_data;

		// --- set widget id ---
		// 2.3.0: added unique widget id
		if ( !isset( $radio_station_data['widgets']['upcoming-shows'] ) ) {
			$id = $radio_station_data['widgets']['upcoming-shows'] = 0;
		} else {
			$id = $radio_station_data['widgets']['upcoming-shows']++;
		}

		// 2.3.0: filter widget_title whether empty or not
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );
		$display_djs = $instance['display_djs'];
		$djavatar = $instance['djavatar'];
		$link = $instance['link'];
		$default = empty( $instance['default'] ) ? '' : $instance['default'];
		$limit = empty( $instance['limit'] ) ? '1' : $instance['limit'];
		$time = empty( $instance['time'] ) ? '' : $instance['time'];
		$show_sched = $instance['show_sched'];

		// 2.2.4: added title position, avatar width and DJ link settings
		// 2.3.0: added countdown display option
		$position = empty( $instance['title_position'] ) ? 'right' : $instance['title_position'];
		$width = empty( $instance['avatar_width'] ) ? '75' : $instance['avatar_width'];
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : 0;
		$dynamic = isset( $instance['dynamic'] ) ? $instance['dynamic'] : 0;

		// --- set shortcode attributes ---
		// 2.3.0: map widget options to shortcode attributes
		$atts = array(
			// --- legacy widget options ---
			'title'          => $title,
			'display_djs'    => $display_djs,
			'show_avatar'    => $djavatar,
			'show_link'      => $link,
			'default'	     => $default,
			'limit'          => $limit,
			'time'           => $time,
			'show_sched'     => $show_sched,
			// --- new widget options ---
			// 'show_playlist'		=> $show_playlist,
			// 'show_desc'			=> $show_desc,
			'title_position' => $position,
			'avatar_width'   => $width,
			'link_djs'       => $link_djs,
			'countdown'      => $countdown,
			'dynamic'        => $dynamic,
			'widget'         => 1,
			'id'             => $id,
		);

		// --- before widget ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['before_widget'];

		// --- open widget container ---
		// 2.3.0: add unique id to widget
		$id = 'upcoming-shows-widget-' . $id;
		echo '<div id="' . esc_attr( $id ) . '" class="widget">';

		// --- output widget title ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['before_title'];
		if ( !empty( $title ) ) {
			echo esc_html( $title );
		}
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['after_title'];

		// --- get default display output ---
		// 2.3.0: use shortcode to generate default widget output
		$output = radio_station_upcoming_shows_shortcode( $atts );

		// --- check for widget output override ---
		// 2.3.0: added this override filter
		$output = apply_filters( 'radio_station_upcoming_shows_widget_override', $output, $args, $atts );

		// --- output widget display ---
		if ( $output ) {
			// phpcs:ignore WordPress.Security.OutputNotEscaped
			echo $output;
		}

		// --- close widget container ---
		echo '</div>';

		// --- after widget ---
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $args['after_widget'];

		// --- enqueue widget stylesheet in footer ---
		// (this means it will only load if widget is on page)
		// 2.2.4: renamed djonair.css to widgets.css and load for all widgets
		// 2.3.0: widgets.css merged into rs-shortcodes.css
		// 2.3.0: use abstracted method for enqueueing widget styles
		radio_station_enqueue_style( 'shortcodes' );

	}
}


// --- register the widget ---
// 2.2.7: revert anonymous function usage for backwards compatibility
add_action( 'widgets_init', 'radio_station_register_upcoming_shows_widget' );
function radio_station_register_upcoming_shows_widget() {
	// note: widget class name to remain unchanged for backwards compatibility
	register_widget( 'DJ_Upcoming_Widget' );
}
