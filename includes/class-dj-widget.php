<?php
/* Sidebar Widget - DJ On Air
 * Displays the current on-air show/DJ
 * Since 2.1.1
 */
class DJ_Widget extends WP_Widget {

	// --- use __contruct instead of DJ_Widget ---
	public function __construct() {
		$widget_ops          = array(
			'classname'   => 'DJ_Widget',
			'description' => __( 'The current on-air DJ.', 'radio-station' ),
		);
		$widget_display_name = __( '(Radio Station) Show/DJ On-Air', 'radio-station' );
		parent::__construct( 'DJ_Widget', $widget_display_name, $widget_ops );
	}

	// --- widget instance form ---
	public function form( $instance ) {

		$instance       = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title          = $instance['title'];
		$display_djs    = isset( $instance['show_desc'] ) ? $instance['display_djs'] : false;
		$djavatar       = isset( $instance['djavatar'] ) ? $instance['djavatar'] : false;
		$default        = isset( $instance['default'] ) ? $instance['default'] : '';
		$link           = isset( $instance['link'] ) ? $instance['link'] : false;
		$time           = isset( $instance['time'] ) ? $instance['time'] : 12;
		$show_sched     = isset( $instance['show_sched'] ) ? $instance['show_sched'] : false;
		$show_playlist  = isset( $instance['show_playlist'] ) ? $instance['show_playlist'] : false;
		$show_all_sched = isset( $instance['show_all_sched'] ) ? $instance['show_all_sched'] : false;
		$show_desc      = isset( $instance['show_desc'] ) ? $instance['show_desc'] : false;

		// 2.2.4: added title position, avatar width and DJ link options
		$title_position = isset( $instance['title_position'] ) ? $instance['title_position'] : 'below';
		$avatar_width   = isset( $instance['avatar_width'] ) ? $instance['avatar_width'] : '';
		$link_djs       = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
			<?php esc_html_e( 'Title', 'radio-station' ); ?>:
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
						?>
						<option value="<?php echo esc_attr( $position ); ?>" <?php selected( $title_position, $position ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
						<?php
					}
					?>
				</select>
				<?php esc_html_e( 'Show Title Position (relative to Avatar)', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'djavatar' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'djavatar' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'djavatar' ) ); ?>" type="checkbox" <?php checked( $djavatar ); ?>/>
				<?php esc_html_e( 'Display Show Avatar', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'avatar_width' ) ); ?>">
				<?php esc_html_e( 'Avatar Width', 'radio-station' ); ?>:
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'avatar_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'avatar_width' ) ); ?>" type="text" value="<?php echo esc_attr( $avatar_width ); ?>" />
			</label>
			<small><?php esc_html_e( 'Width of Show Avatar (in pixels, default full width)', 'radio-station' ); ?></small>
		</p>


		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_djs' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'display_djs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_djs' ) ); ?>" type="checkbox" <?php checked( $display_djs ); ?>/>
				<?php esc_html_e( 'Display names of the DJs on the Show', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link_djs' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'link_djs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_djs' ) ); ?>" type="checkbox" <?php checked( $link_djs ); ?>/>
				<?php esc_html_e( 'Link DJ names to author pages', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_sched' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'show_sched' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_sched' ) ); ?>" type="checkbox" <?php checked( $show_sched ); ?> />
				<?php esc_html_e( 'Display schedule info for this show', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_all_sched' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'show_all_sched' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_all_sched' ) ); ?>" type="checkbox" <?php checked( $show_all_sched ); ?> />
				<?php esc_html_e( 'Display multiple schedules (if show airs more than once per week)', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_desc' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'show_desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_desc' ) ); ?>" type="checkbox" <?php checked( $show_desc ); ?> />
				<?php esc_html_e( 'Display description of show', 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_playlist' ) ); ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_playlist' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_playlist' ) ); ?>" type="checkbox" <?php checked( $show_playlist ); ?> />
				<?php esc_html_e( "Display link to show's playlist", 'radio-station' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'default' ) ); ?>">
				<?php esc_html_e( 'Default DJ Name', 'radio-station' ); ?>:
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'default' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'default' ) ); ?>" type="text" value="<?php echo esc_attr( $default ); ?>" />
			</label>
			<small><?php esc_html_e( 'If no Show/DJ is scheduled for the current hour, display this name/text.', 'radio-station' ); ?></small>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'time' ) ); ?>"><?php esc_html_e( 'Time Format', 'radio-station' ); ?>:<br />
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
			<small><?php esc_html_e( 'Choose time format for displayed schedules', 'radio-station' ); ?></small>
		</p>
		<?php
	}

	// --- update widget instance values ---
	public function update( $new_instance, $old_instance ) {

		$instance                   = $old_instance;
		$instance['title']          = $new_instance['title'];
		$instance['display_djs']    = ( isset( $new_instance['display_djs'] ) ? 1 : 0 );
		$instance['djavatar']       = ( isset( $new_instance['djavatar'] ) ? 1 : 0 );
		$instance['link']           = ( isset( $new_instance['link'] ) ? 1 : 0 );
		$instance['default']        = $new_instance['default'];
		$instance['time']           = $new_instance['time'];
		// 2.2.7: fix checkbox value saving
		$instance['show_sched']     = ( isset( $new_instance['show_sched'] ) ? 1 : 0 );
		$instance['show_playlist']  = ( isset( $new_instance['show_playlist'] ) ? 1 : 0 );
		$instance['show_all_sched'] = ( isset( $new_instance['show_all_sched'] ) ? 1 : 0 );
		$instance['show_desc']      = ( isset( $new_instance['show_desc'] ) ? 1 : 0 );

		// 2.2.4: added title position and avatar width settings
		$instance['title_position'] = $new_instance['title_position'];
		$instance['avatar_width']   = $new_instance['avatar_width'];
		$instance['link_djs']       = ( isset( $new_instance['link_djs'] ) ? 1 : 0 );

		return $instance;

	}

	// --- widget output ---
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		$title         = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$display_djs   = $instance['display_djs'];
		$djavatar      = $instance['djavatar'];
		$link          = $instance['link'];
		$default       = empty( $instance['default'] ) ? '' : $instance['default'];
		$time          = empty( $instance['time'] ) ? '' : $instance['time'];
		$show_sched    = $instance['show_sched'];
		$show_playlist = $instance['show_playlist'];
		// keep the default settings for people updating from 1.6.2 or earlier
		$show_all_sched = isset( $instance['show_all_sched'] ) ? $instance['show_all_sched'] : false;
		// keep the default settings for people updating from 2.0.12 or earlier
		$show_desc = isset( $instance['show_desc'] ) ? $instance['show_desc'] : false;

		// 2.2.4: added title position, avatar width and DJ link settings
		$position = empty( $instance['title_position'] ) ? 'bottom' : $instance['title_position'];
		$width    = empty( $instance['avatar_width'] ) ? '' : $instance['avatar_width'];
		$link_djs = isset( $instance['link_djs'] ) ? $instance['link_djs'] : '';

		// --- fetch the current DJs and playlist ---
		$djs      = radio_station_dj_get_current();
		$playlist = radio_station_myplaylist_get_now_playing();

		// 2.2.3: convert all span tags to div tags
		// 2.2.4: maybe set float class and avatar width style
		$widthstyle = '';
		if ( ! empty( $width ) ) {
			$widthstyle = 'style="width:' . esc_attr( $width ) . 'px;"';
		}
			$floatclass = '';
		if ( 'right' === $position ) {
			$floatclass = ' float-left';
		} elseif ( 'left' === $position ) {
			$floatclass = ' float-right';
		}

		?>
		<div class="widget">
			<?php
			echo $args['before_title'];
			if ( ! empty( $title ) ) {
				echo esc_html( $title );
			}
			echo $args['after_title'];
			?>

			<ul class="on-air-list">
				<?php

				// --- find out which DJ/show is currently scheduled to be on-air and display them ---
				if ( 'override' === $djs['type'] ) {
					?>
					<li class="on-air-dj">
						<?php
						// --- show title *for above only) ---
						if ( 'above' === $position ) {
							?>
							<div class="on-air-dj-title">
								<?php echo esc_html( $djs['all'][0]['title'] ); ?>
							</div>
							<?php
						}

						if ( $djavatar ) {
							if ( has_post_thumbnail( $djs['all'][0]['post_id'] ) ) {
								?>
								<div class="on-air-dj-avatar<?php echo esc_attr( $floatclass ); ?>" <?php echo $widthstyle; ?>>
									<?php echo get_the_post_thumbnail( $djs['all'][0]['post_id'], 'thumbnail' ); ?>
								</div>
								<?php
							}
						}

						// --- show title ---
						if ( 'above' !== $position ) {
							?>
							<div class="on-air-dj-title">
								<?php echo esc_html( $djs['all'][0]['title'] ); ?>
							</div>
							<?php
						}
						?>
						<span class="radio-clear"></span>
						<?php

						// --- display the schedule override if requested ---
						if ( $show_sched ) {

							if ( 12 === (int) $time ) {

								// 2.2.7: add fix to convert back from 24 hour time if past midday
								$start_hour = $djs['all'][0]['sched']['start_hour'];
								if ( substr( $start_hour, 0, 1 ) === '0' ) {
									$start_hour = substr( $start_hour, 1 );
								} elseif ( (int) $start_hour > 12 ) {$start_hour = $start_hour - 12;}

								// 2.2.7: add fix to convert back from 24 hour time if past midday
								$end_hour = $djs['all'][0]['sched']['end_hour'];
								if ( substr( $end_hour, 0, 1 ) === '0' ) {
									$end_hour = substr( $end_hour, 1 );
								} elseif ( (int) $end_hour > 12 ) {$end_hour = $end_hour - 12;}

								?>
								<div class="on-air-dj-sched">
									<?php echo esc_html( $start_hour . ':' . $djs['all'][0]['sched']['start_min'] . ' ' . $djs['all'][0]['sched']['start_meridian'] . ' - ' . $end_hour . ':' . $djs['all'][0]['sched']['end_min'] . ' ' . $djs['all'][0]['sched']['end_meridian'] ); ?>
								</div>
								<?php
							} else {

								$djs['all'][0]['sched'] = radio_station_convert_schedule_to_24hour( $djs['all'][0]['sched'] );
								?>
								<div class="on-air-dj-sched">
									<?php echo esc_html( $djs['all'][0]['sched']['start_hour'] . ':' . $djs['all'][0]['sched']['start_min'] . ' -' . $djs['all'][0]['sched']['end_hour'] . ':' . $djs['all'][0]['sched']['end_min'] ); ?>
								</div>
								<?php
							}
						}
						?>
						</li>
						<?php
				} else {

					if ( isset( $djs['all'] ) && ( count( $djs['all'] ) > 0 ) ) {
						foreach ( $djs['all'] as $dj ) {

							$scheds        = get_post_meta( $dj->ID, 'show_sched', true );
							$current_sched = radio_station_current_schedule( $scheds );
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
									<div class="on-air-dj-avatar<?php echo esc_attr( $floatclass ); ?>" <?php echo $widthstyle; ?>>
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
								if ( isset( $current_sched['encore'] ) && 'on' === $current_sched['encore'] ) {
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

												$dj_link = get_author_posts_url( $user_info->ID );
												$dj_link = apply_filters( 'radio_station_dj_link', $dj_link, $user_info->ID );
												if ( $link_djs ) {
													?>
													<a href="<?php echo esc_url( $dj_link ); ?>">
														<?php echo esc_html( $user_info->display_name ); ?>
													</a>
													<?php
												} else {
													echo esc_html( $user_info->display_name );
												}

												$id_count = count( $ids );
												if ( ( 1 === $count && 2 === $id_count ) || ( $id_count > 2 && $count === $id_count - 1 ) ) {
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

								// --- show description ---
								if ( $show_desc ) {
									$desc_string = radio_station_shorten_string( wp_strip_all_tags( $dj->post_content ), 20 );
									$desc_string = apply_filters( 'radio_station_show_description', $desc_string, $dj->ID );
									?>
									<div class="on-air-show-desc">
										<?php esc_html( $desc_string ); ?>
									</div>
									<?php
								}

								// --- playlist link ---
								if ( $show_playlist ) {
									?>
									<div class="on-air-dj-playlist">
										<a href="<?php echo esc_url( $playlist['playlist_permalink'] ); ?>">
											<?php echo esc_html__( 'View Playlist', 'radio-station' ); ?>
										</a>
									</div>
									<?php
								}
								?>
								<span class="radio-clear"></span>
								<?php
								// --- show schedule ---
								if ( $show_sched ) {

									// --- if we only want the schedule that is relevant now to display ---
									if ( ! $show_all_sched ) {

										if ( $current_sched ) {
											// 2.2.2: translate weekday for display
											$display_day = radio_station_translate_weekday( $current_sched['day'] );
											if ( 12 === (int) $time ) {
												?>
												<div class="on-air-dj-sched">
													<?php echo esc_html( $display_day . ', ' . $current_sched['start_hour'] . ':' . $current_sched['start_min'] . ' ' . $current_sched['start_meridian'] . ' - ' . $current_sched['end_hour'] . ':' . $current_sched['end_min'] . ' ' . $current_sched['end_meridian'] ); ?>
												</div>
												<?php
											} else {
												$current_sched = radio_station_convert_schedule_to_24hour( $current_sched );
												?>
												<div class="on-air-dj-sched">
													<?php echo esc_html( $display_day . ', ' . $current_sched['start_hour'] . ':' . $current_sched['start_min'] . ' - ' . $current_sched['end_hour'] . ':' . $current_sched['end_min'] ); ?>
												</div>
												<?php
											}
										}
									} else {

										foreach ( $scheds as $sched ) {

											// 2.2.2: translate weekday for display
											$display_day = radio_station_translate_weekday( $sched['day'] );
											if ( 12 === (int) $time ) {
												?>
												<div class="on-air-dj-sched">
													<?php echo esc_html( $display_day . ', ' . $sched['start_hour'] . ':' . $sched['start_min'] . ' ' . $sched['start_meridian'] . ' - ' . $sched['end_hour'] . ':' . $sched['end_min'] . ' ' . $sched['end_meridian'] ); ?>
												</div>
												<?php
											} else {
												$sched = radio_station_convert_schedule_to_24hour( $sched );
												?>
												<div class="on-air-dj-sched">
													<?php
													echo esc_html( $display_day . ', ' . $sched['start_hour'] . ':' . $sched['start_min'] . ' - ' . $sched['end_hour'] . ':' . $sched['end_min'] );
													?>
												</div>
												<?php
											}
										}
									}
								}
								?>
							</li>
							<?php
						}
					} else {
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
		// 2.2.2: fix to file check logic (file_exists not !file_exists)
		if ( file_exists( $dj_widget_css ) ) {
			$version = filemtime( $dj_widget_css );
			$url     = get_stylesheet_directory_uri() . '/widgets.css';
		} else {
			// 2.2.3: fix to version path check also
			$version = filemtime( RADIO_STATION_DIR . '/css/widgets.css' );
			$url     = plugins_url( 'css/widgets.css', RADIO_STATION_DIR . '/radio-station.php' );
		}
		// 2.2.4: fix to media argument
		wp_enqueue_style( 'dj-widget', $url, array(), $version, 'all' );

		echo $args['after_widget'];
	}
}

// --- register the widget ---
// 2.2.7: revert anonymous function usage for backwards compatibility
add_action( 'widgets_init', 'radio_station_register_dj_widget' );
function radio_station_register_dj_widget() {
	register_widget('DJ_Widget');
}
