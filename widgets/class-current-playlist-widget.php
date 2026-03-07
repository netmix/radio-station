<?php

/* Current Playlist Widget - (Now Playing)
 * Displays the currently playing song according to the entered playlists
 * Since 2.1.1
 */

if ( !defined( 'ABSPATH' ) ) exit;

// note: widget class name to remain unchanged for backwards compatibility
class Playlist_Widget extends WP_Widget {

	// --- use __constuct instead of Playlist_Widget ---
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'Playlist_Widget',
			'description' => __( 'Display currently playing playlist.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Now Playing List', 'radio-station' );
		parent::__construct( 'Playlist_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );

		// 2.3.0: added hide widget if empty option
		// 2.3.0: added countdown display option
		// 2.3.2: added AJAX load option
		// 2.3.3.8: added playlist link option
		// 2.5.0: added no_playlist text option
		// 2.5.0: added playlist_title option
		// --- widget display options ---
		$title = $instance['title'];
		$ajax = isset( $instance['ajax'] ) ? $instance['ajax'] : '';
		$hide_empty = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : true;
		// --- playlist display options ---
		$playlist_title = isset( $instance['playlist_title'] ) ? $instance['playlist_title'] : false;
		$link_playlist = isset( $instance['link'] ) ? $instance['link'] : true;
		$no_playlist = isset( $instance['no_playlist'] ) ? $instance['no_playlist'] : '';
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : false;
		// --- track display options ---
		$artist = isset( $instance['artist'] ) ? $instance['artist'] : true;
		$song = isset( $instance['song'] ) ? $instance['song'] : true;
		$album = isset( $instance['album'] ) ? $instance['album'] : false;
		$label = isset( $instance['label'] ) ? $instance['label'] : false;
		$comments = isset( $instance['comments'] ) ? $instance['comments'] : false;

		// --- get upgrade URLs ---
		$pricing_url = radio_station_get_pricing_url();
		$upgrade_url = radio_station_get_upgrade_url();

		// 2.3.0: convert template style code to strings
		// 2.3.2: added AJAX load option field
		// 2.3.3.8: added playlist link field
		$fields = array();

		// --- Widget Title ---
		$fields['title'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">
				' . esc_html( __( 'Widget Title', 'radio-station' ) ) . ':
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '">
			</label>
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
			<a href="' . esc_url( $pricing_url ) . '#dynamic-reload" target="_blank">' . esc_html( __( 'More Details', 'radio-station' ) ) . '</a>
		</p>';

		// --- Hide if Empty ---
		$fields['hide_empty'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'hide_empty' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'hide_empty' ) ) . '" name="' . esc_attr( $this->get_field_name( 'hide_empty' ) ) . '" type="checkbox" ' . checked( $hide_empty, true, false ) . '>
				' . esc_html( __( 'Hide Widget if Empty', 'radio-station' ) ) . '
			</label>
		</p>';

		// === Playlist Display Options ===

		// --- Link to Playlist ---
		$fields['playlist_title'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'playlist_title' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'playlist_title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'playlist_title' ) ) . '" type="checkbox" ' . checked( $playlist_title, true, false ) . '>
				' . esc_html( __( 'Display Playlist Title?', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Link to Playlist ---
		$fields['link_playlist'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'link' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'link' ) ) . '" name="' . esc_attr( $this->get_field_name( 'link' ) ) . '" type="checkbox" ' . checked( $link_playlist, true, false ) . '>
				' . esc_html( __( 'Link to Playlist?', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- No Playlist Text ---
		$fields['no_playlist'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'default' ) ) . '">
				' . esc_html( __( 'No Playlist Text', 'radio-station' ) ) . '
				<input class="widefat" id="' . esc_attr( $this->get_field_id( 'default' ) ) . '" name="' . esc_attr( $this->get_field_name( 'default' ) ) . '" type="text" value="' . esc_attr( $no_playlist ) . '" />
			</label>
			<small>' . esc_html( __( 'Empty for default, 0 for none.', 'radio-station' ) ) . '</small>
		</p>';

		// --- Countdown ---
		$fields['countdown'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'countdown' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'countdown' ) ) . '" name="' . esc_attr( $this->get_field_name( 'countdown' ) ) . '" type="checkbox" ' . checked( $countdown, true, false ) . '>
				' . esc_html( __( 'Display Countdown Timer', 'radio-station' ) ) . '
			</label>
		</p>';

		// === Track Display Options ===

		// --- Display Song ---
		$fields['song'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'song' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'song' ) ) . '" name="' . esc_attr( $this->get_field_name( 'song' ) ) . '" type="checkbox" ' . checked( $song, true, false ) . '>
				' . esc_html( __( 'Show Song Title', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Display Artist ---
		$fields['artist'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'artist' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'artist' ) ) . '" name="' . esc_attr( $this->get_field_name( 'artist' ) ) . '" type="checkbox" ' . checked( $artist, true, false ) . '>
				' . esc_html( __( 'Show Artist Name', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Display Album ---
		$fields['album'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'album' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'album' ) ) . '" name="' . esc_attr( $this->get_field_name( 'album' ) ) . '" type="checkbox" ' . checked( $album, true, false ) . '>
				' . esc_html( __( ' Show Album Name', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Display Label ---
		$fields['label'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'label' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'label' ) ) . '" name="' . esc_attr( $this->get_field_name( 'label' ) ) . '" type="checkbox" ' . checked( $label, true, false ) . '>
				' . esc_html( __( 'Show Record Label Name', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- Display Comments ---
		$fields['comments'] = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'comments' ) ) . '">
			<input id="' . esc_attr( $this->get_field_id( 'comments' ) ) . '" name="' . esc_attr( $this->get_field_name( 'comments' ) ) . '" type="checkbox" ' . checked( $comments, true, false ) . '>
				' . esc_html( __( 'Show DJ Comments', 'radio-station' ) ) . '
			</label>
		</p>';

		// --- filter and output ---
		// 2.3.0: filter to allow for extra fields
		// 2.5.0: added filter for array fields for ease of adding fields
		$fields = apply_filters( 'radio_station_playlist_widget_fields_list', $fields, $this, $instance );
		$fields_html = implode( "\n", $fields );
		$fields_html = apply_filters( 'radio_station_playlist_widget_fields', $fields_html, $this, $instance );
		// 2.5.0: use wp_kses on field settings output
		$allowed = radio_station_allowed_html( 'content', 'settings' );
		echo wp_kses( $fields_html, $allowed );
	}

	// --- update widget instance ---
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// 2.3.0: added hide widget if empty option
		// 2.3.0: added countdown display option
		// 2.3.2: added AJAX load option
		// 2.3.3.8: added playlist link option
		// 2.5.0: added no_playlist text option
		// 2.5.0: added playlist_title option
		// --- widget display options ---
		$instance['title'] = $new_instance['title'];
		$instance['ajax'] = isset( $new_instance['ajax'] ) ? $new_instance['ajax'] : 0;
		$instance['hide_empty'] = isset( $new_instance['hide_empty'] ) ? 1 : 0;
		// --- playlist display options ---
		$instance['playlist_title'] = isset( $new_instance['playlist_title'] ) ? 1 : 0;
		$instance['link'] = isset( $new_instance['link'] ) ? 1 : 0;
		$instance['no_playlist'] = isset( $new_instance['no_playlist'] ) ? $new_instance['no_playlist'] : '';
		$instance['countdown'] = isset( $new_instance['countdown'] ) ? 1 : 0;
		// --- track display options ---
		$instance['artist'] = isset( $new_instance['artist'] ) ? 1 : 0;
		$instance['song'] = isset( $new_instance['song'] ) ? 1 : 0;
		$instance['album'] = isset( $new_instance['album'] ) ? 1 : 0;
		$instance['label'] = isset( $new_instance['label'] ) ? 1 : 0;
		$instance['comments'] = isset( $new_instance['comments'] ) ? 1 : 0;

		// 2.5.18: save (or preserve) channel setting
		if ( isset( $new_instance['channel'] ) ) {
			$instance['channel'] = $new_instance['channel'];
		} elseif ( isset( $old_instance['channel'] ) ) {
			$instance['channel'] = $old_instance['channel'];
		}

		// 2.3.0: apply filters to widget instance update
		$instance = apply_filters( 'radio_station_playlist_widget_update', $instance, $new_instance, $old_instance );
		return $instance;
	}

	// --- output widget display ---
	public function widget( $args, $instance ) {

		global $radio_station_data;

		// --- set widget id ---
		// 2.3.0: added unique widget id
		// 2.5.0: simplify widget id setting
		if ( !isset( $radio_station_data['widgets']['current-playlist'] ) ) {
			$radio_station_data['widgets']['current-playlist'] = 0;
		}
		$radio_station_data['widgets']['current-playlist']++;
		$id = $radio_station_data['widgets']['current-playlist'];

		// 2.3.0: filter widget_title whether empty or not
		// 2.3.0: added hide widget if empty option
		// 2.3.0: added countdown display option
		// 2.3.2: set fallback options to numeric for shortcode
		// 2.3.2: added AJAX load option
		// 2.5.0: added no_playlist text option
		// 2.5.0: added playlist_title option
		// 2.5.6: cast hide_empty to 1 or 0

		// --- widget display options ---
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title );
		$ajax = isset( $instance['ajax'] ) ? $instance['ajax'] : 0;
		$hide_empty = ( isset( $instance['hide_empty'] ) && $instance['hide_empty'] ) ? 1 : 0;
		// --- playlist display options ---
		$playlist_title = isset( $instance['playlist_title'] ) ? $instance['playlist_title'] : 0;
		$link = isset( $instance['link'] ) ? $instance['link'] : 1;
		$no_playlist = isset( $instance['no_playlist'] ) ? $instance['no_playlist'] : '';
		$countdown = isset( $instance['countdown'] ) ? $instance['countdown'] : 0;
		// --- track display options ---
		$song = $instance['song'];
		$artist = $instance['artist'];
		$album = $instance['album'];
		$label = $instance['label'];
		$comments = $instance['comments'];

		// --- set shortcode attributes for display ---
		// 2.3.2: only set AJAX attribute if overriding default
		// 2.5.0: set AJAX attribute anyway (checked in shortcode)
		// 2.5.0: added no_playlist and hide_empty attributes
		// 2.5.0: removed title attribute (only used for shortcodes)
		// 2.5.6: added missing no_playlist variable value
		$atts = array(
			// --- widget display options ---
			'ajax'           => $ajax,
			'hide_empty'     => $hide_empty,
			// --- playlist display options ---
			'playlist_title' => $playlist_title,
			'link'           => $link,
			'no_playlist'    => $no_playlist,
			'countdown'      => $countdown,
			// --- track display options ---
			'artist'         => $artist,
			'song'           => $song,
			'album'          => $album,
			'label'          => $label,
			'comments'       => $comments,
			// --- widget data ---
			'widget'         => 1,
			'id'             => $id,
		);

		// 2.3.3.9: add filter for default widget attributes
		$atts = apply_filters( 'radio_station_current_playlist_widget_atts', $atts, $instance );

		// --- get default display output ---
		// 2.3.0: use shortcode to generate default widget output
		$output = radio_station_current_playlist_shortcode( $atts );

		// --- check for widget output override ---
		// 2.3.0: added this override filter
		$output = apply_filters( 'radio_station_current_playlist_widget_override', $output, $args, $atts );

		// 2.3.0: added hide widget if empty option
		if ( !$hide_empty || ( $hide_empty && $output ) ) {

			// 2.5.0: get context filtered allowed HTML
			$allowed = radio_station_allowed_html( 'widget', 'current-playlist' );

			// --- beore widget ---
			// 2.5.0: use wp_kses on output
			echo wp_kses( $args['before_widget'], $allowed );

			// --- open widget container ---
			// 2.3.0: add unique id to widget
			// 2.3.2: add class to widget
			// 2.4.0.1: add current-playlist-widget class
			echo '<div id="current-playlist-widget-' . esc_attr( $id ) . '" class="current-playlist-widget widget">' . "\n";

				// --- output widget title ---
				// 2.5.0: use wp_kses on output
				echo wp_kses( $args['before_title'], $allowed );
				if ( !empty( $title ) ) {
					echo wp_kses( $title, $allowed );
				}
				// 2.5.0: use wp_kses on output
				echo wp_kses( $args['after_title'], $allowed );

				// 2.3.3.9: add div wrapper for widget contents
				echo '<div id="current-playlist-widget-contents-' . esc_attr( $id ) . '" class="current-playlist-wrap">' . "\n";

					// --- check for widget output override ---
					// 2.3.3.9: added missing override filter
					// 2.5.0: remove duplicated override filter

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
add_action( 'widgets_init', 'radio_station_register_current_playlist_widget' );
function radio_station_register_current_playlist_widget() {
	// note: widget class name to remain unchanged for backwards compatibility
	register_widget( 'Playlist_Widget' );
}
