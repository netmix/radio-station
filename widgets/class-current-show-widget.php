<?php

/* Current Show Widget - (DJ On Air)
 * Displays the current on-air Show / DJ
 * Since 2.1.1
 */

if ( !defined( 'ABSPATH' ) ) exit;

// note: widget class name to remain unchanged for backwards compatibility
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

		// 2.3.3: set time format default to plugin setting
		// 2.2.4: added title position, avatar width and DJ link options
		// 2.3.0: added countdown display option
		// 2.3.2: added AJAX load option
		// 2.3.3.8: added show encore text display option
		// 2.5.0: add avatar_size field

		// ---- widget options ---
		$title = $instance['title'];
		$ajax = isset( $instance['ajax'] ) ? $instance['ajax'] : '';
		$no_shows = isset( $instance['default'] ) ? $instance['default'] : '';
		$hide_empty = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : false;

		// --- show display options ---
		$show_link = isset( $instance['link'] ) ? $instance['link'] : false;
		$title_position = isset( $instance['title_position'] ) ? $instance['title_position'] : 'below';
		$show_avatar = isset( $instance['djavatar'] ) ? $instance['djavatar'] : false;
		$avatar_size = isset( $instance['avatar_size'] ) ? $instance['avatar_size'] : 'thumbnail';
		$avatar_width = isset( $instance['avatar_width'] ) ? $instance['avatar_width'] : '';

		// --- show time display options ---
		$show_sched = isset( $instance['show_sched'] ) ? $instance['show_sched'] : false;
		$show_all_sched = isset( $instance['show_all_sched'] ) ? $instance['show_all_sched'] : false;
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : false;
		$time_format = isset( $instance['time'] ) ? $instance['time'] : '';

		// --- extra display options ---
		$display_djs = isset( $instance['display_djs'] ) ? $instance['display_djs'] : false;
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';
		$show_desc = isset( $instance['show_desc'] ) ? $instance['show_desc'] : false;
		$show_playlist = isset( $instance['show_playlist'] ) ? $instance['show_playlist'] : false;
		$show_encore = isset( $instance['show_encore'] ) ? $instance['show_encore'] : true;

		// --- get upgrade URLs ---
		$pricing_url = radio_station_get_pricing_url();
		$upgrade_url = radio_station_get_upgrade_url();

		// 2.3.0: convert template style code to strings
		// 2.5.0: use fields array
		$fields = array();

		// === Widget Display Options ===

		// --- Widget Title ---
		$fields['title'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">
			' . esc_html( __( 'Widget Title', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" />
			</label>
		</p>';

		// --- AJAX Loading ---
		// 2.3.2: added AJAX load option field
		$fields['ajax'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'ajax' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'ajax' ) ) . '" name="' . esc_attr( $this->get_field_name( 'ajax' ) ) . '">
					<option value="" ' . selected( $ajax, '', false ) . '>' . esc_html( __( 'Default', 'radio-station' ) ) . '</option>
					<option value="on" ' . selected( $ajax, 'on', false ) . '>' . esc_html( __( 'On', 'radio-station' ) ) . '</option>
					<option value="off" ' . selected( $ajax, 'off', false ) . '>' . esc_html( __( 'Off', 'radio-station' ) ) . '</option>
				</select>
				' . esc_html( __( 'AJAX Load Widget?', 'radio-station' ) ) . '
			</label>
        </p>';

		// --- [Pro] Dynamic Reloading ---
		$fields['dynamic'] = '<p>
			<label for="dynamic">' . esc_html( __( 'Show changeover reloading available in Pro.', 'radio-station' ) ) . '</label><br>
			<a href="' . esc_url( $upgrade_url ) . '">' . esc_html( __( 'Upgrade to Pro', 'radio-station' ) ) . '</a> |
			<a href="' . esc_url( $pricing_url ) . '#dynamic-reload" target="_blank">' . esc_html( __( 'More Details', 'radio-station' ) ) . '</a>
		</p>';

		// --- No Current Show Text ---
		$fields['no_shows'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'default' ) ) . '">
				' . esc_html( __( 'No Current Show Text', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'default' ) ) . '" name="' . esc_attr( $this->get_field_name( 'default' ) ) . '" type="text" value="' . esc_attr( $no_shows ) . '">
			</label>
			<small>' . esc_html( __( 'Empty for default. 0 for none.', 'radio-station' ) ) . '</small>
		</p>';

		// --- Hide if Empty ---
		$fields['hide_empty'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'hide_empty' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'hide_empty' ) ) . '" name="' . esc_attr( $this->get_field_name( 'hide_empty' ) ) . '" type="checkbox" ' . checked( $hide_empty, true, false ) . '>
				' . esc_html( __( 'Hide Widget if Empty', 'radio-station' ) ) . '
			</label>
		</p>';

		// === Show Display Options ===

		// --- Show Link ---
		$fields['show_link'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'link' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link' ) ) . '" type="checkbox" ' . checked( $show_link, true, false ) . '>
				' . esc_html( __( 'Link Show title to  Show page.', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Title Position ---
		$field = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'title_position' ) ) . '">
				<select id="' . esc_attr( $this->get_field_id( 'title_position' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title_position' ) ) . '">';
				$positions = array(
					'above' => __( 'Above', 'radio-station' ),
					'left'  => __( 'Left', 'radio-station' ),
					'right' => __( 'Right', 'radio-station' ),
					'below' => __( 'Below', 'radio-station' ),
				);
				foreach ( $positions as $position => $label ) {
					$field .= '<option value="' . esc_attr( $position ) . '" ' . selected( $title_position, $position, false ) . '>' . esc_html( $label ) . '</option>';
				}
				$field .= '</select>
				' . esc_html( __( 'Show Title Position (relative to Avatar)', 'radio-station' ) ) . '
			</label>
		</p>';
		$fields['title_position'] = $field;

		// --- Show Avatar ---
		$fields['show_avatar'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'djavatar' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'djavatar' ) ) . '" name="' . esc_attr( $this->get_field_name( 'djavatar' ) ) . '" type="checkbox" ' . checked( $show_avatar, true, false ) . '>
				' . esc_html( __( 'Display Show Avatar', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Avatar Size ---
		$fields['avatar_size'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'avatar_size' ) ) . '">
				' . esc_html( __( 'Avatar Image Size', 'radio-station' ) ) . ':
				<select id="' . esc_attr( $this->get_field_id( 'avatar_size' ) ) . '" name="' . esc_attr( $this->get_field_name( 'avatar_size' ) ) . '">';
				// --- set image size options ---
				// 2.5.6: move loop to directly inside select field
				$image_sizes = radio_station_get_image_sizes();
				foreach ( $image_sizes as $image_size => $label ) {
					$fields['avatar_size'] .= '<option value="' . esc_attr( $image_size ) . '"';
					// 2.5.6: added missing check for current avatar size selection
					if ( $image_size == $avatar_size ) {
						$fields['avatar_size'] .= ' selected="selected"';
					}
					$fields['avatar_size'] .= '>' . esc_html( $label ) . '</option>' . "\n";
				}
		$fields['avatar_size'] .= '</select>
			</label>
		</p>';

		// --- Avatar Width ---
		$fields['avatar_width'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'avatar_width' ) ) . '">
				' . esc_html( __( 'Avatar Width', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'avatar_width' ) ) . '" name="' . esc_attr( $this->get_field_name( 'avatar_width' ) ) . '" type="text" value="' . esc_attr( $avatar_width ) . '">
			</label>
			<small>' . esc_html( __( 'Show Avatar Width override. 0 or empty for none.', 'radio-station' ) ) . '</small>
		</p>';

		// === Show Time Display Options ===

		// --- Display Show Time ---
		$fields['show_sched'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_sched' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_sched' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_sched' ) ) . '" type="checkbox" ' . checked( $show_sched, true, false ) . '>
				' . esc_html( __( 'Display Show Shift Info', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Display All Shifts ---
		$fields['show_all_sched'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_all_sched' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_all_sched' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_all_sched' ) ) . '" type="checkbox" ' . checked( $show_all_sched, true, false ) . '>
				' . esc_html( __( 'Display All Show Shifts (if more than current.)', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Countdown ---
		$fields['countdown'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'countdown' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'countdown' ) ) . '" name="' . esc_attr( $this->get_field_name( 'countdown' ) ) . '" type="checkbox" ' . checked( $countdown, true, false ) . '>
				' . esc_html( __( 'Display Countdown Timer', 'radio-station' ) ) . '
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

		// === Extra Display Options ===

		// --- Display Hosts ---
		$fields['display_hosts'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'display_djs' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'display_djs' ) ) . '" name="' . esc_attr( $this->get_field_name( 'display_djs' ) ) . '" type="checkbox" ' . checked( $display_djs, true, false ) . '>
				' . esc_html( __( 'Display Show Host names.', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Link Host Profiles ---
		$fields['link_hosts'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'link_djs' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link_djs' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link_djs' ) ) . '" type="checkbox" ' . checked( $link_djs, true, false ) . '>
				' . esc_html( __( 'Link Host names to author pages.', 'radio-station' ) ) . '
			</label>
		</p>';

		// _--- Display Show Description Excerpt ---
		$fields['show_desc'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_desc' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_desc' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_desc' ) ) . '" type="checkbox" ' . checked( $show_desc, true, false ) . '>
				' . esc_html( __( 'Display Show description excerpt.', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Display Show Playlist ---
		$fields['show_playlist'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_playlist' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_playlist' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_playlist' ) ) . '" type="checkbox" ' . checked( $show_playlist, true, false ) . '>
				' . esc_html( __( 'Display link to Show playlist.', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Display Show Encore ---
		$fields['show_encore'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_encore' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_encore' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_encore' ) ) . '" type="checkbox" ' . checked( $show_encore, true, false ) . '>
				' . esc_html( __( 'Display encore presentation text for Show', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- filter and output ---
		// 2.3.0: added field filter for extra fields
		// 2.3.2: fix for second and third filter arguments
		// 2.5.0: added filter for array fields for ease of adding fields
		$fields = apply_filters( 'radio_station_current_show_widget_fields_list', $fields, $this, $instance );
		$fields_html = implode( "\n", $fields );
		$fields_html = apply_filters( 'radio_station_current_show_widget_fields', $fields_html, $this, $instance );
		// 2.5.0: use wp_kses on field settings output
		$allowed = radio_station_allowed_html( 'content', 'settings' );
		echo wp_kses( $fields_html, $allowed );
	}

	// --- update widget instance values ---
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// 2.2.4: added title position and avatar width settings
		// 2.2.7: fix checkbox value saving
		// 2.3.0: added countdown display option
		// 2.3.2: added ajax load option
		// 2.5.6: fix hide_empty to 1 or 0
		// --- widget display options ---
		$instance['title'] = $new_instance['title'];
		$instance['ajax'] = isset( $new_instance['ajax'] ) ? $new_instance['ajax'] : 0;
		$instance['default'] = $new_instance['default'];
		$instance['hide_empty'] = isset( $new_instance['hide_empty'] ) ? 1 : 0;
		// --- show display options ---
		$instance['link'] = isset( $new_instance['link'] ) ? 1 : 0;
		$instance['title_position'] = $new_instance['title_position'];
		$instance['djavatar'] = isset( $new_instance['djavatar'] ) ? 1 : 0;
		$instance['avatar_size'] = isset( $new_instance['avatar_size'] ) ? $new_instance['avatar_size'] : 'thumbnail';
		$instance['avatar_width'] = $new_instance['avatar_width'];
		// --- show time display options ---
		$instance['show_sched'] = isset( $new_instance['show_sched'] ) ? 1 : 0;
		$instance['show_all_sched'] = isset( $new_instance['show_all_sched'] ) ? 1 : 0;
		$instance['countdown'] = isset( $new_instance['countdown'] ) ? 1 : 0;
		$instance['time'] = $new_instance['time'];
		// --- extra display options ---
		$instance['display_djs'] = isset( $new_instance['display_djs'] ) ? 1 : 0;
		$instance['link_djs'] = isset( $new_instance['link_djs'] ) ? 1 : 0;
		$instance['show_desc'] = isset( $new_instance['show_desc'] ) ? 1 : 0;
		$instance['show_playlist'] = isset( $new_instance['show_playlist'] ) ? 1 : 0;
		$instance['show_encore'] = isset( $new_instance['show_encore'] ) ? 1 : 0;

		// 2.5.18: save (or preserve) channel setting
		if ( isset( $new_instance['channel'] ) ) {
			$instance['channel'] = $new_instance['channel'];
		} elseif ( isset( $old_instance['channel'] ) ) {
			$instance['channel'] = $old_instance['channel'];
		}

		// 2.3.0: filter widget update instance
		$instance = apply_filters( 'radio_station_current_show_widget_update', $instance, $new_instance, $old_instance );
		return $instance;
	}

	// --- widget output ---
	public function widget( $args, $instance ) {

		global $radio_station_data;

		// --- set widget id ---
		// 2.3.0: added unique widget id
		// 2.5.0: simplify widget id setting
		if ( !isset( $radio_station_data['widgets']['current-show'] ) ) {
			$radio_station_data['widgets']['current-show'] = 0;
		}
		$radio_station_data['widgets']['current-show']++;
		$id = $radio_station_data['widgets']['current-show'];

		// 2.2.4: added title position, avatar width and DJ link settings
		// 2.3.0: filter widget_title whether empty or not
		// 2.3.2: fix old false values to use 0 for shortcodes
		// 2.3.2: added AJAX load option
		// 2.5.0: added avatar_size for show image size
		// 2.5.6: cast hide_empty to 1 or 0

		// --- widget display options ---
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );
		$ajax = isset( $instance['ajax'] ) ? $instance['ajax'] : 0;
		$no_shows = empty( $instance['default'] ) ? '' : $instance['default'];
		$hide_empty = ( isset( $instance['hide_empty'] ) && $instance['hide_empty'] ) ? 1 : 0;
		// --- show display options ---
		$show_link = $instance['link'];
		$title_position = empty( $instance['title_position'] ) ? 'bottom' : $instance['title_position'];
		$show_avatar = $instance['djavatar'];
		$avatar_size = isset( $instance['avatar_size'] ) ? $instance['avatar_size'] : 'thumbnail';
		$avatar_width = empty( $instance['avatar_width'] ) ? '' : $instance['avatar_width'];
		// --- show time display options ---
		$show_sched = $instance['show_sched'];
		$show_all_sched = isset( $instance['show_all_sched'] ) ? $instance['show_all_sched'] : 0;
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : 0;
		$time_format = empty( $instance['time'] ) ? '' : $instance['time'];
		// --- extra display options ---
		$display_hosts = $instance['display_djs'];
		$link_hosts = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';
		$show_playlist = $instance['show_playlist'];
		$show_desc = isset( $instance['show_desc'] ) ? $instance['show_desc'] : 0;
		$show_encore = isset( $instance['show_encore'] ) ? $instance['show_encore'] : 0;

		// --- set shortcode attributes ---
		// 2.3.0: map widget options to shortcode attributes
		// 2.3.2: added AJAX load option
		// 2.3.2: only set AJAX attribute if overriding default
		// 2.5.0: set AJAX attribute anyway (checked in shortcode)
		// 2.5.0: changed default_name key to no_shows
		$atts = array(
			// --- widget display options ---
			'title'          => $title,
			'ajax'           => $ajax,
			'no_shows'       => $no_shows,
			'hide_empty'     => $hide_empty,
			// --- show display options  ---
			'show_link'      => $show_link,
			'title_position' => $title_position,
			'show_avatar'    => $show_avatar,
			'avatar_size'    => $avatar_size,
			'avatar_width'   => $avatar_width,
			// --- show time display options ---
			'show_sched'     => $show_sched,
			'show_all_sched' => $show_all_sched,
			'countdown'      => $countdown,
			'time_format'    => $time_format,
			// --- extra display options ---
			'display_hosts'  => $display_hosts,
			'link_hosts'     => $link_hosts,
			'show_desc'      => $show_desc,
			'show_playlist'  => $show_playlist,
			'show_encore'    => $show_encore,
			// --- widget data ---
			'widget'         => 1,
			'id'             => $id,
		);

		// 2.3.3.9: add filter for default widget attributes
		$atts = apply_filters( 'radio_station_current_show_widget_atts', $atts, $instance );

		// --- get default display output ---
		// 2.3.0: use shortcode to generate default widget output
		$output = radio_station_current_show_shortcode( $atts );

		// --- check for widget output override ---
		// 2.3.0: added this override filter
		$output = apply_filters( 'radio_station_current_show_widget_override', $output, $args, $atts );

		// 2.5.0: added hide widget if empty option
		if ( !$hide_empty || ( $hide_empty && $output ) ) {

			// 2.5.0: get context filtered allowed HTML
			$allowed = radio_station_allowed_html( 'widget', 'current-show' );

			// --- before widget ---
			// 2.5.0: use wp_kses on output
			echo wp_kses( $args['before_widget'], $allowed );

			// --- open widget container ---
			// 2.3.0: add unique id to widget
			// 2.3.2: add class to widget
			// 2.4.0.1: add current-show-widget class
			echo '<div id="current-show-widget-' . esc_attr( $id ) . '" class="current-show-widget widget">' . "\n";

				// --- widget title ---
				// 2.5.0: use wp_kses on output
				echo wp_kses( $args['before_title'], $allowed );
				if ( !empty( $title ) ) {
					echo wp_kses( $title, $allowed );
				}
				// 2.5.0: use wp_kses on output
				echo wp_kses( $args['after_title'], $allowed );

				// 2.3.3.9: add div wrapper for widget contents
				echo '<div id="current-show-widget-contents-' . esc_attr( $id ) . '" class="current-show-wrap">' . "\n";

					// --- output widget display ---
					// TODO: test wp_kses on shortcode output
					// echo wp_kses( $output, $allowed );
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $output;

				// --- close widget contents wrapper ---
				echo '</div>' . "\n";

			// --- close widget container ---
			echo '</div>' . "\n";

			// --- after widget ---
			// 2.5.0: use wp_kses on output
			echo wp_kses( $args['after_widget'], $allowed );

			// --- enqueue widget stylesheet in footer ---
			// (this means it will only load if widget is on page)
			// 2.2.4: renamed djonair.css to widgets.css and load for all widgets
			// 2.3.0: widgets.css merged into rs-shortcodes.css
			// 2.3.0: use abstracted method for enqueueing widget styles
			radio_station_enqueue_style( 'shortcodes' );

		}

	}
}

// --- register the widget ---
// 2.2.7: revert anonymous function usage for backwards compatibility
add_action( 'widgets_init', 'radio_station_register_current_show_widget' );
function radio_station_register_current_show_widget() {
	// note: widget class name to remain unchanged for backwards compatibility
	register_widget( 'DJ_Widget' );
}
