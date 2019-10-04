<?php
/* Sidebar Widget - Now Playing
 * Displays the currently playing song according to the entered playlists
 * Since 2.1.1
 */
class Playlist_Widget extends WP_Widget {

	// --- use __constuct instead of Playlist_Widget ---
	public function __construct() {
		$widget_ops          = array(
			'classname'   => 'Playlist_Widget',
			'description' => __( 'Display the current song.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Now Playing', 'radio-station' );
		parent::__construct( 'Playlist_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = $instance['title'];
		$artist   = isset( $instance['artist'] ) ? $instance['artist'] : true;
		$song     = isset( $instance['song'] ) ? $instance['song'] : true;
		$album    = isset( $instance['album'] ) ? $instance['album'] : false;
		$label    = isset( $instance['label'] ) ? $instance['label'] : false;
		$comments = isset( $instance['comments'] ) ? $instance['comments'] : false;

		?>
		<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php esc_html_e( 'Title', 'radio-station' ); ?>:
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'song' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'song' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'song' ) ); ?>" type="checkbox" <?php checked( $song ); ?>/>
					<?php esc_html_e( 'Show Song Title', 'radio-station' ); ?>
				</label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'artist' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'artist' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'artist' ) ); ?>" type="checkbox" <?php checked( $artist ); ?>/>
					<?php esc_html_e( 'Show Artist Name', 'radio-station' ); ?>
				</label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'album' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'album' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'album' ) ); ?>" type="checkbox" <?php checked( $album ); ?> />
					<?php esc_html_e( ' Show Album Name', 'radio-station' ); ?>
				</label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'label' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'label' ) ); ?>" type="checkbox" <?php checked( $label ); ?>/>
					<?php esc_html_e( 'Show Record Label Name', 'radio-station' ); ?>
				</label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'comments' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'comments' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'comments' ) ); ?>" type="checkbox" <?php checked( $comments ); ?> />
					<?php esc_html_e( 'Show DJ Comments', 'radio-station' ); ?>
				</label>
			</p>
		<?php
	}

	// --- update widget instance ---
	public function update( $new_instance, $old_instance ) {

		$instance             = $old_instance;
		$instance['title']    = $new_instance['title'];
		$instance['artist']   = ( isset( $new_instance['artist'] ) ? 1 : 0 );
		$instance['song']     = ( isset( $new_instance['song'] ) ? 1 : 0 );
		$instance['album']    = ( isset( $new_instance['album'] ) ? 1 : 0 );
		$instance['label']    = ( isset( $new_instance['label'] ) ? 1 : 0 );
		$instance['comments'] = ( isset( $new_instance['comments'] ) ? 1 : 0 );

		return $instance;
	}

	// --- output widget display ---
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		$title    = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$artist   = $instance['artist'];
		$song     = $instance['song'];
		$album    = $instance['album'];
		$label    = $instance['label'];
		$comments = $instance['comments'];

		// --- fetch the current song ---
		$most_recent = radio_station_myplaylist_get_now_playing();

		?>
		<div class="widget">
			<?php
			echo $args['before_title'];
			if ( ! empty( $title ) ) {
				echo esc_html( $title );
			}
			echo $args['after_title'];

			if ( $most_recent ) {

				$class = '';
				if ( isset( $most_recent['playlist_entry_new'] ) && 'on' === $most_recent['playlist_entry_new'] ) {
					$class = 'new';
				}
				?>
				<div id="myplaylist-nowplaying" class="<?php echo esc_attr( $class ); ?>">
					<?php

					// 2.2.3: convert span tags to div tags
					// 2.2.4: check value keys are set before outputting
					if ( $song && isset( $most_recent['playlist_entry_song'] ) ) {
						?>
						<div class="myplaylist-song">
							<?php echo __( 'Song:','radio-station' ).' '.esc_html( $most_recent['playlist_entry_song'] ); ?>
						</div>
						<?php
					}

					// 2.2.7: add label prefixes to now playing data
					if ( $artist && isset( $most_recent['playlist_entry_artist'] ) ) {
						?>
						<div class="myplaylist-artist">
							<?php echo __( 'Artist','radio-station' ).': '.esc_html( $most_recent['playlist_entry_artist'] ); ?>
						</div>
						<?php
					}

					if ( $album && isset( $most_recent['playlist_entry_album'] ) ) {
						?>
						<div class="myplaylist-album">
							<?php echo __( 'Album','radio-station' ).': '.esc_html( $most_recent['playlist_entry_album'] ); ?>
						</div>
						<?php
					}

					if ( $label && isset( $most_recent['playlist_entry_label'] ) ) {
						?>
						<div class="myplaylist-label">
							<?php echo __( 'Label','radio-station' ).': '.esc_html( $most_recent['playlist_entry_label'] ); ?>
						</div>
						<?php
					}

					if ( $comments && isset( $most_recent['playlist_entry_comments'] ) ) {
						?>
						<div class="myplaylist-comments">
							<?php echo __( 'Comments','radio-station' ).': '.esc_html( $most_recent['playlist_entry_comments'] ); ?>
						</div>
						<?php
					}

					if ( isset( $most_recent['playlist_permalink'] ) ) {
						?>
						<div class="myplaylist-link">
							<a href="<?php echo esc_url( $most_recent['playlist_permalink'] ); ?>">
								<?php echo esc_html__( 'View Playlist', 'radio-station' ); ?>
							</a>
						</div>
						<?php
					}
					?>
				</div>
				<?php
			} else {
				// 2.2.3: added missing translation wrapper
				?>
				<div>
					<?php echo esc_html__( 'No playlists available.', 'radio-station' ); ?>
				</div>
				<?php
			}
			?>
		</div>
		<?php

		// --- enqueue widget stylesheet in footer ---
		// (this means it will only load if widget is on page)
		// 2.2.4: renamed djonair.css and load for all widgets
		$dj_widget_css = get_stylesheet_directory() . '/widgets.css';
		if ( file_exists( $dj_widget_css ) ) {
			$version = filemtime( $dj_widget_css );
			$url     = get_stylesheet_directory_uri() . '/widgets.css';
		} else {
			$version = filemtime( RADIO_STATION_DIR . '/css/widgets.css' );
			$url     = plugins_url( 'css/widgets.css', RADIO_STATION_DIR . '/radio-station.php' );
		}
		wp_enqueue_style( 'dj-widget', $url, array(), $version, 'all' );

		echo $args['after_widget'];
	}
}

// --- register the widget ---
// 2.2.7: revert anonymous function usage for backwards compatibility
add_action( 'widgets_init', 'radio_station_register_nowplaying_widget' );
function radio_station_register_nowplaying_widget() {
	register_widget('Playlist_Widget');
}
