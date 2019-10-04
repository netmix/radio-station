<?php
/* Sidebar Widget - Upcoming DJ
 * Displays the the next show(s)/DJ(s) in the schedule
 * Since 2.1.1
 */
class DJ_Upcoming_Widget extends WP_Widget {

	// use __construct instead of DJ_Upcoming_Widget
	public function __construct() {
		$widget_ops          = array(
			'classname'   => 'DJ_Upcoming_Widget',
			'description' => __( 'The upcoming DJs/Shows.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Upcoming DJ On-Air', 'radio-station' );
		parent::__construct( 'DJ_Upcoming_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance    = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title       = $instance['title'];
		$display_djs = isset( $instance['display_djs'] ) ? $instance['display_djs'] : false;
		$djavatar    = isset( $instance['djavatar'] ) ? $instance['djavatar'] : false;
		$default     = isset( $instance['default'] ) ? $instance['default'] : '';
		$link        = isset( $instance['link'] ) ? $instance['link'] : false;
		$limit       = isset( $instance['limit'] ) ? $instance['limit'] : 1;
		$time        = isset( $instance['time'] ) ? $instance['time'] : 12;
		$show_sched  = isset( $instance['show_sched'] ) ? $instance['show_sched'] : false;

		// 2.2.4: added title position, avatar width and DJ link options
		$title_position = isset( $instance['title_position'] ) ? $instance['title_position'] : 'right';
		$avatar_width   = isset( $instance['avatar_width'] ) ? $instance['avatar_width'] : '75';
		$link_djs       = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title', 'radio-station' ); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" type="checkbox" <?php checked( $link ); ?> />
				<?php esc_html_e( 'Link the title to the Show page', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title_position' ) ); ?>">
				<select id="<?php echo esc_attr( $this->get_field_id( 'title_position' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title_position' ) ); ?>">
			<?php
			$positions = array(
				'above' => __( 'Above', 'radio-station' ),
				'left'  => __( 'Left', 'radio-station' ),
				'right' => __( 'Right', 'radio-station' ),
				'below' => __( 'Below', 'radio-station' ),
			);
			foreach ( $positions as $position => $label ) {
				echo '<option value="' . esc_attr( $position ) . '" ' . selected( $title_position, $position ) . '>' . esc_html( $label ) . '</option>';
			}
			?>
				</select>
			<?php esc_html_e( 'Show Title Position (relative to Avatar)', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'djavatar' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'djavatar' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'djavatar' ) ); ?>" type="checkbox" <?php checked( $djavatar ); ?>
				/>
				<?php esc_html_e( 'Display Show Avatars', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'avatar_width' ) ); ?>">
				<?php esc_html_e( 'Avatar Width', 'radio-station' ); ?>:
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'avatar_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'avatar_width' ) ); ?>" type="text" value="<?php echo esc_attr( $avatar_width ); ?>" />
			</label>
			<small><?php esc_html_e( 'Width of Show Avatars (in pixels, default 75px)', 'radio-station' ); ?></small>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_djs' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'display_djs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_djs' ) ); ?>" type="checkbox" <?php checked( $display_djs ); ?> />
				<?php esc_html_e( 'Display names of the DJs on the show', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link_djs' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'link_djs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_djs' ) ); ?>" type="checkbox" <?php checked( $link_djs ); ?> />
				<?php esc_html_e( 'Link DJ names to author pages', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'default' ) ); ?>">
				<?php esc_html_e( 'No Additional Schedules', 'radio-station' ); ?>:
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'default' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'default' ) ); ?>" type="text" value="<?php echo esc_attr( $default ); ?>" />
			</label>
			<small><?php esc_html_e( 'If no Show/DJ is scheduled for the current hour, display this name/text.', 'radio-station' ); ?></small>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_sched' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'show_sched' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_sched' ) ); ?>" type="checkbox" <?php checked( $show_sched ); ?> />
				<?php esc_html_e( 'Display schedule info for this show', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>">
				<?php esc_html_e( 'Limit', 'radio-station' ); ?>:
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
			</label>
			<small><?php esc_html_e( 'Number of upcoming DJs/Shows to display.', 'radio-station' ); ?></small>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'time' ) ); ?>">
				<?php esc_html_e( 'Time Format', 'radio-station' ); ?>:<br />
				<select id="<?php echo esc_attr( $this->get_field_id( 'time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'time' ) ); ?>">
					<option value="12" <?php selected( $time, 12 ); ?>>
				<?php esc_html_e( '12-hour', 'radio-station' ); ?>
					</option>
					<option value="24" <?php selected( $time, 24 ); ?>>
				<?php esc_html_e( '24-hour', 'radio-station' ); ?>
					</option>
				</select>
			</label>
			<br />
			<small><?php esc_html_e( 'Choose time format for displayed schedules.', 'radio-station' ); ?></small>
		</p>
		<?php
	}

	// --- update widget instance ---
	public function update( $new_instance, $old_instance ) {

		$instance                = $old_instance;
		$instance['title']       = $new_instance['title'];
		$instance['display_djs'] = ( isset( $new_instance['display_djs'] ) ? 1 : 0 );
		$instance['djavatar']    = ( isset( $new_instance['djavatar'] ) ? 1 : 0 );
		$instance['link']        = ( isset( $new_instance['link'] ) ? 1 : 0 );
		$instance['default']     = $new_instance['default'];
		$instance['limit']       = $new_instance['limit'];
		$instance['time']        = $new_instance['time'];
		$instance['show_sched']  = ( isset( $new_instance['show_sched'] ) ? 1 : 0 );

		// 2.2.4: added title position, avatar width and DJ link settings
		$instance['title_position'] = $new_instance['title_position'];
		$instance['avatar_width']   = $new_instance['avatar_width'];
		$instance['link_djs']       = ( isset( $new_instance['link_djs'] ) ? 1 : 0 );

		return $instance;
	}

	// --- output widget display ---
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		$title       = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$display_djs = $instance['display_djs'];
		$djavatar    = $instance['djavatar'];
		$link        = $instance['link'];
		$default     = empty( $instance['default'] ) ? '' : $instance['default'];
		$limit       = empty( $instance['limit'] ) ? '1' : $instance['limit'];
		$time        = empty( $instance['time'] ) ? '' : $instance['time'];
		$show_sched  = $instance['show_sched'];

		// 2.2.4: added title position, avatar width and DJ link settings
		$position = empty( $instance['title_position'] ) ? 'right' : $instance['title_position'];
		$width    = empty( $instance['avatar_width'] ) ? '75' : $instance['avatar_width'];
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';

		// --- find out which DJ(s) are coming up today ---
		$djs = radio_station_dj_get_next( $limit );

		// 2.2.3: convert all span tags to div tags
		// 2.2.4: maybe set float class and avatar width style
		$width_style = '';
		if ( ! empty( $width ) ) {
			$width_style = 'style="width:' . esc_attr( $width ) . 'px;"';
		}

		$float_class = '';
		if ( 'right' === $position ) {
			$float_class = ' float-left';
		} elseif ( 'left' === $position ) {
			$float_class = ' float-right';
		}

		?>

		<div class="widget">
			<?php
			// --- output widget title ---
			echo $args['before_title'];
			if ( ! empty( $title ) ) {
				echo esc_html( $title );
			}
			echo $args['after_title'];
			?>
			<ul class="on-air-upcoming-list">

				<?php
					// --- echo the show/dj currently on-air ---

				if ( isset( $djs['all'] ) && ( count( $djs['all'] ) > 0 ) ) {

					foreach ( $djs['all'] as $showtime => $dj ) {
						if ( is_array( $dj ) && isset( $dj['type'] ) && 'override' === $dj['type'] ) {
							?>
							<li class="on-air-dj">
								<?php
								// --- show title (for above only) ---
								if ( 'above' === $position ) {
									?>
									<div class="on-air-dj-title">
										<?php echo esc_html( $dj['title'] ); ?>
									</div>
									<?php
								}

								// --- show avatar ---
								if ( $djavatar ) {
									if ( has_post_thumbnail( $dj['post_id'] ) ) {
										?>
										<div class="on-air-dj-avatar'<?php echo esc_attr( $float_class ); ?>" <?php echo $width_style; ?>>
											<?php echo get_the_post_thumbnail( $dj['post_id'], 'thumbnail' ); ?>
										</div>
										<?php
									}
								}

								// --- show title ---
								if ( 'above' !== $position ) {
									?>
									<div class="on-air-dj-title">
										<?php echo esc_html( $dj['title'] ); ?>
									</div>
									<?php
								}
								?>
								<span class="radio-clear"></span>
								<?php

								// --- show schedule ---
								if ( $show_sched ) {

									if ( 12 === (int) $time ) {
										$start_hour = $dj['sched']['start_hour'];
										if ( substr( $dj['sched']['start_hour'], 0, 1 ) === '0' ) {
											$start_hour = substr( $dj['sched']['start_hour'], 1 );
										}

										$end_hour = $dj['sched']['end_hour'];
										if ( substr( $dj['sched']['end_hour'], 0, 1 ) === '0' ) {
											$end_hour = substr( $dj['sched']['end_hour'], 1 );
										}
										?>
										<div class="on-air-dj-sched">
											<?php echo esc_html( $start_hour . ':' . $dj['sched']['start_min'] . ' ' . $dj['sched']['start_meridian'] . ' - ' . $end_hour . ':' . $dj['sched']['end_min'] . ' ' . $dj['sched']['end_meridian'] ); ?>
										</div>
										<?php
									} else {
										?>
										<div class="on-air-dj-sched">
											<?php echo esc_html( $dj['sched']['start_hour'] . ':' . $dj['sched']['start_min'] . ' - ' . $dj['sched']['end_hour'] . ':' . $dj['sched']['end_min'] ); ?>
										</div>
										<?php
									}
								}
								?>
							</li>
							<?php
						} else {
							?>
							<li class="on-air-dj">
								<?php
								// --- show title (for above only) ---
								if ( 'above' === $position ) {
									?>
									<div class="on-air-dj-title">
										<?php
										if ( $link ) {
											?>
											<a href="<?php echo esc_url( get_permalink( $dj->ID ) ); ?>">
												<?php echo esc_html( $dj->post_title ); ?>
											</a>
											<?php
										} else {
											echo esc_html( $dj->post_title );
										}
										?>
									</div>
									<?php
								}

								// --- show avatar ---
								if ( $djavatar ) {
									?>
									<div class="on-air-dj-avatar<?php echo esc_attr( $float_class ); ?>" <?php echo $width_style; ?>>
										<?php echo get_the_post_thumbnail( $dj->ID, 'thumbnail' ); ?>
									</div>
									<?php
								}

								// --- show title ---
								if ( 'above' !== $position ) {
									?>
									<div class="on-air-dj-title">
										<?php
										if ( $link ) {
											?>
										<a href="<?php echo esc_url( get_permalink( $dj->ID ) ); ?>">
											<?php echo esc_html( $dj->post_title ); ?>
										</a>
											<?php
										} else {
											echo esc_html( $dj->post_title );
										}
										?>
									</div>
									<?php
								}
								?>
								<span class="radio-clear"></span>
								<?php

								// --- encore presentation ---
								// 2.2.4: added encore presentation display
								if ( array_key_exists( $showtime, $djs['encore'] ) ) {
									?>
									<div class="on-air-dj-encore">
										<?php echo esc_html__( 'Encore Presentation', 'radio-station' ); ?>
									</div>
									<?php
								}

									// --- DJ names ---
								if ( $display_djs ) {

									$ids   = get_post_meta( $dj->ID, 'show_user_list', true );
									$count = 0;

									if ( $ids && is_array( $ids ) ) {
										?>
										<div class="on-air-dj-names">
											<?php
											echo esc_html__( 'with ', 'radio-station' );

											foreach ( $ids as $id ) {
												$count++;
												$user_info = get_userdata( $id );

												if ( $link_djs ) {
													$dj_link = get_author_posts_url( $user_info->ID );
													$dj_link = apply_filters( 'radio_station_dj_link', $dj_link, $user_info->ID );
													?>
													<a href="<?php echo esc_url( $dj_link ); ?>">
														<?php echo esc_html( $user_info->display_name ); ?>
													</a>
													<?php
												} else {
													echo esc_html( $user_info->display_name );
												}

												$id_count = count( $ids );
												if ( ( 1 === $count && 2 === $id_count )
												|| ( $id_count > 2 ) && $count === $id_count - 1 ) {
													echo ' ' . esc_html__( 'and', 'radio-station' ) . ' ';
												} elseif ( $count < $id_count && $id_count > 2 ) {
													echo ', ';
												}
											}
											?>
											</div>
										<?php
									}
								}
								?>
								<span class="radio-clear"></span>
								<?php
								// --- show schedule ---
								if ( $show_sched ) {

									$showtimes = explode( '|', $showtime );
									// 2.2.2: fix to weekday value to be translated
									$weekday = radio_station_translate_weekday( date( 'l', $showtimes[0] ) );

									// 2.2.7: fix to convert time to integer
									if ( 12 === (int) $time ) {
										?>
										<div class="on-air-dj-sched">
											<?php echo esc_html( $weekday ); ?>, <?php echo esc_html( date( 'g:i a', $showtimes[0] ) ); ?> - <?php echo esc_html( date( 'g:i a', $showtimes[1] ) ); ?>
										</div>
										<?php
									} else {
										?>
										<div class="on-air-dj-sched">
											<?php echo esc_html( $weekday ); ?>, <?php echo esc_html( date( 'H:i', $showtimes[0] ) ); ?>
											- <?php echo esc_html( date( 'H:i', $showtimes[1] ) ); ?>
										</div>
										<?php
									}
								}
								?>
							</li>
							<?php
						}
					}
				} else {
					if ( ! empty( $default ) ) {
						?>
						<li class="on-air-dj default-dj">
							<?php echo esc_html( $default ); ?>
						</li>
						<?php
					}
				}
				?>
			</ul>
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
add_action( 'widgets_init', 'radio_station_register_djcomingup_widget' );
function radio_station_register_djcomingup_widget() {
	register_widget('DJ_Upcoming_Widget');
}