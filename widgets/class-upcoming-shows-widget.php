<?php

/* Upcoming Shows Widget - (Upcoming DJ)
 * Displays the the next show(s)/DJ(s) in the schedule
 * Since 2.1.1
 */

if ( !defined( 'ABSPATH' ) ) exit;

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

		// 2.3.3: set time format default to empty (plugin setting)
		// 2.2.4: added title position, avatar width and DJ link options
		// 2.3.0: added countdown field option
		// 2.3.2: added AJAX load option
		// 2.5.0: added avatar_size and show_encore fields

		// --- widget display options ---
		$title = $instance['title'];
		$limit = isset( $instance['limit'] ) ? $instance['limit'] : 1;
		$ajax = isset( $instance['ajax'] ) ? $instance['ajax'] : '';
		$no_shows = isset( $instance['default'] ) ? $instance['default'] : '';
		$hide_empty = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : false;
		// --- show display options ---
		$link = isset( $instance['link'] ) ? $instance['link'] : false;
		$title_position = isset( $instance['title_position'] ) ? $instance['title_position'] : 'right';
		$djavatar = isset( $instance['djavatar'] ) ? $instance['djavatar'] : false;
		$avatar_size = isset( $intance['avatar_size'] ) ? $instance['avatar_size'] : 'thumbnail';
		$avatar_width = isset( $instance['avatar_width'] ) ? $instance['avatar_width'] : '75';
		// --- show time display options ----
		$show_sched = isset( $instance['show_sched'] ) ? $instance['show_sched'] : false;
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : '';
		$time = isset( $instance['time'] ) ? $instance['time'] : '';
		// --- extra display options ---
		$display_djs = isset( $instance['display_djs'] ) ? $instance['display_djs'] : false;
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';
		$show_encore = isset( $instance['show_encore'] ) ? $instance['show_encore'] : true;

		// --- get upgrade URLs ---
		$pricing_url = add_query_arg( 'page', 'radio-station-pricing', admin_url( 'admin.php' ) );
		$upgrade_url = radio_station_get_upgrade_url();

		// 2.3.0: convert template style code to straight php echo
		// 2.3.2: added AJAX load option field
		// 2.5.0: create fields array for filtering
		$fields = array();

		// === Widget Loading Options ===

		// --- Widget Title ---
		$fields['title'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">
				' . esc_html( __( 'Widget Title', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" />
			</label>
		</p>';

		// --- Number of Shows ---
		$fields['limit'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'limit' ) ) . '">
				' . esc_html( __( 'Limit', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'limit' ) ) . '" name="' . esc_attr( $this->get_field_name( 'limit' ) ) . '" type="text" value="' . esc_attr( $limit ) . '" />
			</label>
			<small>' . esc_html( __( 'Number of upcoming Shows to display.', 'radio-station' ) ) . '</small>
		</p>';

		// --- AJAX Loading ---
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
			<a href="' . esc_url( $pricing_url ) . '" target="_blank">' . esc_html( __( 'More Details', 'radio-station' ) ) . '</a>
		</p>';

		// --- No Shows Text ---
		$fields['no_shows'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'default' ) ) . '">
				' . esc_html( __( 'No Upcoming Shows Text', 'radio-station' ) ) . '
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'default' ) ) . '" name="' . esc_attr( $this->get_field_name( 'default' ) ) . '" type="text" value="' . esc_attr( $no_shows ) . '" />
			</label>
			<small>' . esc_html( __( 'Empty for default, 0 for none.', 'radio-station' ) ) . '</small>
		</p>';

		// --- Hide if Empty ---
		$fields['hide_empty'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'hide_empty' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'hide_empty' ) ) . '" name="' . esc_attr( $this->get_field_name( 'hide_empty' ) ) . '" type="checkbox" ' . checked( $hide_empty, true, false ) . '>
				' . esc_html( __( 'Hide Widget if Empty', 'radio-station' ) ) . '
			</label>
		</p>';

		// === Show Display Options ===

		// --- Link to Show ---
		$fields['show_link'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'link' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link' ) ) . '" type="checkbox" ' . checked( $link, true, false ) . '/>
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
		$fields['show_avatar'] ='<p>
			<label for="' . esc_attr( $this->get_field_id( 'djavatar' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'djavatar' ) ) . '" name="' . esc_attr( $this->get_field_name( 'djavatar' ) ) . '" type="checkbox" ' . checked( $djavatar, true, false ) . '/>
				' . esc_html( __( 'Display Show Avatars', 'radio-station' ) ) . '
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
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'avatar_width' ) ) . '" name="' . esc_attr( $this->get_field_name( 'avatar_width' ) ) . '" type="text" value="' . esc_attr( $avatar_width ) . '" />
			</label>
			<small>' . esc_html( __( 'Show Avatar Width override. 0 or empty for none.', 'radio-station' ) ) . '</small>
		</p>';

		// === Show Time Options ===

		// --- Display Show Time ---
		$fields['show_sched'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_sched' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_sched' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_sched' ) ) . '" type="checkbox" ' . checked( $show_sched, true, false ) . '/>
				' . esc_html( __( 'Display shift info for this show', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Countdown ---
		$fields['countdown'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'countdown' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'countdown' ) ) . '" name="' . esc_attr( $this->get_field_name( 'countdown' ) ) . '" type="checkbox" ' . checked( $countdown, true, false ) . '/>
				' . esc_html( __( 'Display Countdown Timer', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Time Format ---
		$fields['time_format'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'time' ) ) . '">' . esc_html( __( 'Time Format Display', 'radio-station' ) ) . ':<br />
				<select id="' . esc_attr( $this->get_field_id( 'time' ) ) . '" name="' . esc_attr( $this->get_field_name( 'time' ) ) . '">
					<option value="" ' . selected( $time, '', false ) . '>' . esc_html( __( 'Default', 'radio-station' ) ) . '</option>
					<option value="12" ' . selected( $time, 12, false ) . '>' . esc_html( __( '12 Hour', 'radio-station' ) ) . '</option>
					<option value="24" ' . selected( $time, 24, false ) . '>' . esc_html( __( '24 Hour', 'radio-station' ) ) . '</option>
				</select>
			</label>
		</p>';

		// === Extra Display Options ===

		// --- Display Hosts ---
		$fields['display_hosts'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'display_djs' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'display_djs' ) ) . '" name="' . esc_attr( $this->get_field_name( 'display_djs' ) ) . '" type="checkbox" ' . checked( $display_djs, true, false ) . '/>
				' . esc_html( __( 'Display Show host names.', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Link Hosts ---
		$fields['link_hosts'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'link_djs' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link_djs' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link_djs' ) ) . '" type="checkbox" ' . checked( $link_djs, true, false ) . '/>
				' . esc_html( __( 'Link host names to author pages', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Show Encore ---
		$fields['show_encore'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'show_encore' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'show_encore' ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_encore' ) ) . '" type="checkbox" ' . checked( $show_encore, true, false ) . '>
				' . esc_html( __( 'Display encore presentation text for Show', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- filter and output ---
		// 2.5.0: added filter for array fields for ease of adding fields
		$fields = apply_filters( 'radio_station_upcoming_shows_widget_field_list', $fields, $this, $instance );
		$fields_html = implode( "\n", $fields );
		$fields_html = apply_filters( 'radio_station_upcoming_shows_widget_fields', $fields_html, $this, $instance );
		// 2.5.0: use wp_kses on field settings output
		$allowed = radio_station_allowed_html( 'content', 'settings' );
		echo wp_kses( $fields_html, $allowed );
	}

	// --- update widget instance ---
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// 2.2.4: added title position, avatar width and DJ link settings
		// 2.3.0: added countdown display option
		// 2.3.2: added AJAX load option
		// 2.5.0; added hide_empty, avatar_size, show_title
		// 2.5.6: fix hide_empty to 1 or 0
		// --- widget display options ---
		$instance['title'] = $new_instance['title'];
		$instance['limit'] = $new_instance['limit'];
		$instance['ajax'] = isset( $new_instance['ajax'] ) ? $new_instance['ajax'] : 0;
		$instance['default'] = $new_instance['default'];
		$instance['hide_empty'] = isset( $new_instance['hide_empty'] ) ? 1 : 0;
		// --- show display options ---
		$instance['link'] = isset( $new_instance['link'] ) ? 1 : 0;
		$instance['title_position'] = $new_instance['title_position'];
		$instance['djavatar'] = isset( $new_instance['djavatar'] ) ? 1 : 0;
		$instance['avatar_size'] = isset( $new_instance['avatar_size'] ) ? $new_instance['avatar_size'] : 'thumbnail';
		$instance['avatar_width'] = $new_instance['avatar_width'];
		// --- show time display options ----
		$instance['show_sched'] = isset( $new_instance['show_sched'] ) ? 1 : 0;
		$instance['countdown'] = isset( $new_instance['countdown'] ) ? 1 : 0;
		$instance['time'] = $new_instance['time'];
		// --- extra display options ---
		$instance['display_djs'] = isset( $new_instance['display_djs'] ) ? 1 : 0;
		$instance['link_djs'] = isset( $new_instance['link_djs'] ) ? 1 : 0;
		$instance['show_encore'] = isset( $new_instance['show_encore'] ) ? 1 : 0;

		// 2.5.18: save (or preserve) channel setting
		if ( isset( $new_instance['channel'] ) ) {
			$instance['channel'] = $new_instance['channel'];
		} elseif ( isset( $old_instance['channel'] ) ) {
			$instance['channel'] = $old_instance['channel'];
		}

		// 2.3.0: added widget filter instance to update
		$instance = apply_filters( 'radio_station_upcoming_shows_widget_update', $instance, $new_instance, $old_instance );
		return $instance;
	}

	// --- output widget display ---
	public function widget( $args, $instance ) {

		global $radio_station_data;

		// --- set widget id ---
		// 2.3.0: added unique widget id
		// 2.5.0: simplify widget id setting
		if ( !isset( $radio_station_data['widgets']['upcoming-shows'] ) ) {
			$radio_station_data['widgets']['upcoming-shows'] = 0;
		}
		$radio_station_data['widgets']['upcoming-shows']++;
		$id = $radio_station_data['widgets']['upcoming-shows'];

		// --- get widget title ---
		// 2.3.0: filter widget_title whether empty or not
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );

		// 2.2.4: added title position, avatar width and DJ link settings
		// 2.3.0: added countdown display option
		// 2.3.2: added AJAX load option
		// 2.5.0: renamed default to no_shows
		// 2.5.0; added avatar_size, show_title
		// 2.5.6: cast hide_empty to 1 or 0
		// 2.5.6: fix to variable typo for encore
		// --- widget display options ---
		$limit = empty( $instance['limit'] ) ? '1' : $instance['limit'];
		$ajax = isset( $instance['ajax'] ) ? $instance['ajax'] : 0;
		$no_shows = empty( $instance['default'] ) ? '' : $instance['default'];
		$hide_empty = ( isset( $instance['hide_empty'] ) && $instance['hide_empty'] ) ? 1 : 0;
		// --- show display options ---
		$link = $instance['link'];
		$position = empty( $instance['title_position'] ) ? 'right' : $instance['title_position'];
		$dj_avatar = $instance['djavatar'];
		$avatar_size = isset( $instance['avatar_size'] ) ? $instance['avatar_size'] : 'thumbnail';
		$width = empty( $instance['avatar_width'] ) ? '75' : $instance['avatar_width'];
		// --- show time display options ----
		$show_sched = $instance['show_sched'];
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : 0;
		$time_format = empty( $instance['time'] ) ? '' : $instance['time'];
		// --- extra display options ---
		$display_djs = $instance['display_djs'];
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';
		$encore = isset( $instance['encore'] ) ? $instance['encore'] : 0;

		// --- set shortcode attributes ---
		// 2.3.0: map widget options to shortcode attributes
		// 2.3.2: added AJAX load option
		// 2.3.2: only set AJAX attribute if overriding default
		// 2.5.0: set AJAX attribute anyway (checked in shortcode)
		// 2.5.0: changed default_name key to no_shows
		// 2.5.0: changed time key to time_format
		$atts = array(
			// --- widget display options ---
			'title'          => $title,
			'limit'          => $limit,
			'ajax'           => $ajax,
			'no_shows'       => $no_shows,
			'hide_empty'     => $hide_empty,
			// --- show display options ---
			'show_link'      => $link,
			'title_position' => $position,
			'show_avatar'    => $dj_avatar,
			'avatar_size'    => $avatar_size,
			'avatar_width'   => $width,
			// --- show time display options ----
			'show_sched'     => $show_sched,
			'countdown'      => $countdown,
			'time_format'    => $time_format,
			// --- extra display options ---
			'display_djs'    => $display_djs,
			'link_djs'       => $link_djs,
			'show_encore'    => $encore,
			// --- widget data ---
			'widget'         => 1,
			'id'             => $id,
		);

		// 2.3.3.9: add filter for default widget attributes
		$atts = apply_filters( 'radio_station_upcoming_shows_widget_atts', $atts, $instance );

		// --- get default display output ---
		// 2.3.0: use shortcode to generate default widget output
		$output = radio_station_upcoming_shows_shortcode( $atts );

		// --- check for widget output override ---
		// 2.3.0: added this override filter
		$output = apply_filters( 'radio_station_upcoming_shows_widget_override', $output, $args, $atts );

		// 2.5.0: added hide widget if empty option
		if ( !$hide_empty || ( $hide_empty && $output ) ) {

			// 2.5.0: get context filtered allowed HTML
			$allowed = radio_station_allowed_html( 'widget', 'upcoming-shows' );

			// --- before widget ---
			// 2.5.0: use wp_kses on output
			echo wp_kses( $args['before_widget'], $allowed );

			// --- open widget container ---
			// 2.3.0: add unique id to widget
			// 2.3.2: add class to widget
			// 2.4.0.1: add upcoming-shows-widget class
			echo '<div id="upcoming-shows-widget-' . esc_attr( $id ) . '" class="upcoming-shows-widget widget">' . "\n";

				// --- output widget title ---
				// 2.5.0: use wp_kses on output
				echo wp_kses( $args['before_title'], $allowed );
				if ( !empty( $title ) ) {
					echo wp_kses( $title, $allowed );
				}
				// 2.5.0: use wp_kses on output
				echo wp_kses( $args['after_title'], $allowed );

				// 2.3.3.9: add div wrapper for widget contents
				echo '<div id="upcoming-shows-widget-contents-' . esc_attr( $id ) . '" class="upcoming-shows-wrap">' . "\n";


					// --- output widget display ---
					// TODO: test wp_kses on output
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
add_action( 'widgets_init', 'radio_station_register_upcoming_shows_widget' );
function radio_station_register_upcoming_shows_widget() {
	// note: widget class name to remain unchanged for backwards compatibility
	register_widget( 'DJ_Upcoming_Widget' );
}
