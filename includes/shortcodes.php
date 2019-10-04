<?php

/* Shortcode for displaying the current song
 * Since 2.0.0
 */
function radio_station_shortcode_now_playing( $atts ) {

	$atts = shortcode_atts(
		array(
			'title'    => '',
			'artist'   => 1,
			'song'     => 1,
			'album'    => 0,
			'label'    => 0,
			'comments' => 0,
		),
		$atts,
		'now-playing'
	);

	$most_recent = radio_station_myplaylist_get_now_playing();
	$output      = '';

	if ( $most_recent ) {
		$class = '';
		if ( isset( $most_recent['playlist_entry_new'] ) && 'on' === $most_recent['playlist_entry_new'] ) {
			$class = 'new';
		}

		$output .= '<div id="myplaylist-nowplaying" class="' . $class . '">';
		if ( ! empty( $atts['title'] ) ) {
			$output .= '<h3>' . $atts['title'] . '</h3>';}

		if ( 1 === $atts['song'] ) {
			$output .= '<span class="myplaylist-song">' . $most_recent['playlist_entry_song'] . '</span> ';
		}
		if ( 1 === $atts['artist'] ) {
			$output .= '<span class="myplaylist-artist">' . $most_recent['playlist_entry_artist'] . '</span> ';
		}
		if ( 1 === $atts['album'] ) {
			$output .= '<span class="myplaylist-album">' . $most_recent['playlist_entry_album'] . '</span> ';
		}
		if ( 1 === $atts['label'] ) {
			$output .= '<span class="myplaylist-label">' . $most_recent['playlist_entry_label'] . '</span> ';
		}
		if ( 1 === $atts['comments'] ) {
			$output .= '<span class="myplaylist-comments">' . $most_recent['playlist_entry_comments'] . '</span> ';
		}
		$output .= '<span class="myplaylist-link"><a href="' . $most_recent['playlist_permalink'] . '">' . __( 'View Playlist', 'radio-station' ) . '</a></span> ';
		$output .= '</div>';

	} else {
		echo 'No playlists available.';
	}

	return $output;
}
add_shortcode( 'now-playing', 'radio_station_shortcode_now_playing' );


/* Shortcode to fetch all playlists for a given show id
 * Since 2.0.0
 */
function radio_station_shortcode_get_playlists_for_show( $atts ) {

	$atts = shortcode_atts(
		array(
			'show'  => '',
			'limit' => -1,
		),
		$atts,
		'get-playlists'
	);

	// don't return anything if we do not have a show
	if ( empty( $atts['show'] ) ) {
		return false;
	}

	$args = array(
		'posts_per_page' => $atts['limit'],
		'offset'         => 0,
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'post_type'      => 'playlist',
		'post_status'    => 'publish',
		'meta_key'       => 'playlist_show_id',
		'meta_value'     => $atts['show'],
	);

	$query     = new WP_Query( $args );
	$playlists = $query->posts;

	$output = '';

	$output .= '<div id="myplaylist-playlistlinks">';
	$output .= '<ul class="myplaylist-linklist">';
	foreach ( $playlists as $playlist ) {
		$output .= '<li><a href="' . get_permalink( $playlist->ID ) . '">' . $playlist->post_title . '</a></li>';
	}
	$output .= '</ul>';

	$playlist_archive = get_post_type_archive_link( 'playlist' );
	$params           = array( 'show_id' => $atts['show'] );
	$playlist_archive = add_query_arg( $params, $playlist_archive );

	$output .= '<a href="' . $playlist_archive . '">' . __( 'More Playlists', 'radio-station' ) . '</a>';

	$output .= '</div>';

	return $output;
}
add_shortcode( 'get-playlists', 'radio_station_shortcode_get_playlists_for_show' );

/* Shortcode for displaying a list of all shows
 * Since 2.0.0
 */
function radio_station_shortcode_list_shows( $atts ) {

	$atts = shortcode_atts(
		array(
			'genre' => '',
		),
		$atts,
		'list-shows'
	);

	// grab the published shows
	$args = array(
		'posts_per_page' => 1000,
		'offset'         => 0,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'post_type'      => 'show',
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'   => 'show_active',
				'value' => 'on',
			),
		),
	);
	if ( ! empty( $atts['genre'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'genres',
				'field'    => 'slug',
				'terms'    => $atts['genre'],
			),
		);
	}

	$query = new WP_Query( $args );

	// if there are no shows saved, return nothing
	if ( ! $query->have_posts() ) {
		return false;
	}

	$output = '';

	$output .= '<div id="station-show-list">';
	$output .= '<ul>';
	while ( $query->have_posts() ) :
		$query->the_post();
		$output     .= '<li>';
			$output .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
		$output     .= '</li>';
	endwhile;
	$output .= '</ul>';
	$output .= '</div>';
	wp_reset_postdata();
	return $output;
}
add_shortcode( 'list-shows', 'radio_station_shortcode_list_shows' );

/* Shortcode function for current DJ on-air
 * Since 2.0.9
 */
function radio_station_shortcode_dj_on_air( $atts ) {
	$atts = shortcode_atts(
		array(
			'title'          => '',
			'display_djs'    => 0,
			'show_avatar'    => 0,
			'show_link'      => 0,
			'default_name'   => '',
			'time'           => '12',
			'show_sched'     => 1,
			'show_playlist'  => 1,
			'show_all_sched' => 0,
			'show_desc'      => 0,
		),
		$atts,
		'dj-widget'
	);

	// find out which DJ(s) are currently scheduled to be on-air and display them
	$djs      = radio_station_dj_get_current();
	$playlist = radio_station_myplaylist_get_now_playing();

	$dj_str = '';

	$dj_str .= '<div class="on-air-embedded dj-on-air-embedded">';
	if ( ! empty( $atts['title'] ) ) {
		$dj_str .= '<h3>' . $atts['title'] . '</h3>';
	}
	$dj_str .= '<ul class="on-air-list">';

	// echo the show/dj currently on-air
	if ( 'override' === $djs['type'] ) {

		$dj_str .= '<li class="on-air-dj">';
		if ( $atts['show_avatar'] ) {
			if ( has_post_thumbnail( $djs['all'][0]['post_id'] ) ) {
				$dj_str .= '<span class="on-air-dj-avatar">' . get_the_post_thumbnail( $djs['all'][0]['post_id'], 'thumbnail' ) . '</span>';
			}
		}

		$dj_str .= $djs['all'][0]['title'];

		// display the override's schedule if requested
		if ( $atts['show_sched'] ) {

			if ( 12 === (int) $atts['time'] ) {
				$dj_str .= '<span class="on-air-dj-sched">' . $djs['all'][0]['sched']['start_hour'] . ':' . $djs['all'][0]['sched']['start_min'] . ' ' . $djs['all'][0]['sched']['start_meridian'] . '-' . $djs['all'][0]['sched']['end_hour'] . ':' . $djs['all'][0]['sched']['end_min'] . ' ' . $djs['all'][0]['sched']['end_meridian'] . '</span><br />';
			} else {
				$djs['all'][0]['sched'] = radio_station_convert_schedule_to_24hour( $djs['all'][0]['sched'] );
				$dj_str                .= '<span class="on-air-dj-sched">' . $djs['all'][0]['sched']['start_hour'] . ':' . $djs['all'][0]['sched']['start_min'] . ' -' . $djs['all'][0]['sched']['end_hour'] . ':' . $djs['all'][0]['sched']['end_min'] . '</span><br />';
			}

			$dj_str .= '</li>';
		}
	} else {

		if ( isset( $djs['all'] ) && ( count( $djs['all'] ) > 0 ) ) {
			foreach ( $djs['all'] as $dj ) {

				$dj_str .= '<li class="on-air-dj">';
				if ( $atts['show_avatar'] ) {
					$dj_str .= '<span class="on-air-dj-avatar">' . get_the_post_thumbnail( $dj->ID, 'thumbnail' ) . '</span>';
				}

				$dj_str .= '<span class="on-air-dj-title">';
				if ( $atts['show_link'] ) {
					$dj_str .= '<a href="' . get_permalink( $dj->ID ) . '">' . $dj->post_title . '</a>';
				} else {
					$dj_str .= $dj->post_title;
				}
				$dj_str .= '</span>';

				if ( $atts['display_djs'] ) {

					$names = get_post_meta( $dj->ID, 'show_user_list', true );
					$count = 0;

					if ( $names ) {

						$dj_str .= '<div class="on-air-dj-names">' . __( 'With', 'radio-station' ) . ' ';
						foreach ( $names as $name ) {
							$count++;
							$user_info = get_userdata( $name );

							$dj_str .= $user_info->display_name;

							$count_names = count( $names );
							if ( ( 1 === $count && 2 === $count_names ) || ( $count_names > 2 && $count === $count_names - 1 ) ) {
								$dj_str .= ' and ';
							} elseif ( $count < $count_names && $count_names > 2 ) {
								$dj_str .= ', ';
							}
						}
						$dj_str .= '</div>';
					}
				}

				if ( $atts['show_desc'] ) {
					$desc_string = radio_station_shorten_string( wp_strip_all_tags( $dj->post_content ), 20 );
					$dj_str     .= '<span class="on-air-show-desc">' . $desc_string . '</span>';
				}

				if ( $atts['show_playlist'] ) {
					$dj_str .= '<span class="on-air-dj-playlist"><a href="' . $playlist['playlist_permalink'] . '">' . __( 'View Playlist', 'radio-station' ) . '</a></span>';
				}

				$dj_str .= '<span class="radio-clear"></span>';

				if ( $atts['show_sched'] ) {

					$scheds = get_post_meta( $dj->ID, 'show_sched', true );

					// if we only want the schedule that's relevant now to display...
					if ( ! $atts['show_all_sched'] ) {

						$current_sched = radio_station_current_schedule( $scheds );

						if ( $current_sched ) {
							// 2.2.2: translate weekday for display
							$display_day = radio_station_translate_weekday( $current_sched['day'] );
							if ( 12 === (int) $atts['time'] ) {
								$dj_str .= '<span class="on-air-dj-sched">' . $display_day . ', ' . $current_sched['start_hour'] . ':' . $current_sched['start_min'] . ' ' . $current_sched['start_meridian'] . ' - ' . $current_sched['end_hour'] . ':' . $current_sched['end_min'] . ' ' . $current_sched['end_meridian'] . '</span><br />';
							} else {
								$current_sched = radio_station_convert_schedule_to_24hour( $current_sched );
								$dj_str       .= '<span class="on-air-dj-sched">' . $display_day . ', ' . $current_sched['start_hour'] . ':' . $current_sched['start_min'] . ' - ' . $current_sched['end_hour'] . ':' . $current_sched['end_min'] . '</span><br />';
							}
						}
					} else {

						foreach ( $scheds as $sched ) {
							// 2.2.2: translate weekday for display
							$display_day = radio_station_translate_weekday( $sched['day'] );
							if ( 12 === (int) $atts['time'] ) {
								$dj_str .= '<span class="on-air-dj-sched">' . $display_day . ', ' . $sched['start_hour'] . ':' . $sched['start_min'] . ' ' . $sched['start_meridian'] . ' - ' . $sched['end_hour'] . ':' . $sched['end_min'] . ' ' . $sched['end_meridian'] . '</span><br />';
							} else {
								$sched   = radio_station_convert_schedule_to_24hour( $sched );
								$dj_str .= '<span class="on-air-dj-sched">' . $display_day . ', ' . $sched['start_hour'] . ':' . $sched['start_min'] . ' - ' . $sched['end_hour'] . ':' . $sched['end_min'] . '</span><br />';
							}
						}
					}
				}

				$dj_str .= '</li>';
			}
		} else {
			$dj_str .= '<li class="on-air-dj default-dj">' . $atts['default_name'] . '</li>';
		}
	}

	$dj_str .= '</ul>';
	$dj_str .= '</div>';

	return $dj_str;

}
add_shortcode( 'dj-widget', 'radio_station_shortcode_dj_on_air' );

/* Shortcode for displaying upcoming DJs/shows
 * Since 2.0.9
*/
function radio_station_shortcode_coming_up( $atts ) {

	$atts = shortcode_atts(
		array(
			'title'       => '',
			'display_djs' => 0,
			'show_avatar' => 0,
			'show_link'   => 0,
			'limit'       => 1,
			'time'        => '12',
			'show_sched'  => 1,
		),
		$atts,
		'dj-coming-up-widget'
	);

	// find out which DJ(s) are coming up today
	$djs = radio_station_dj_get_next( $atts['limit'] );
	if ( ! isset( $djs['all'] ) || count( $djs['all'] ) <= 0 ) {
		$output = '<li class="on-air-dj default-dj">' . __( 'None Upcoming', 'radio-station' ) . '</li>';
		return $output;
	}

	ob_start();
	?>
	<div class="on-air-embedded dj-coming-up-embedded">
		<?php
		if ( ! empty( $atts['title'] ) ) {
			?>
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
			<?php
		}
		?>
		<ul class="on-air-list">
			<?php
			// echo the show/dj coming up
			foreach ( $djs['all'] as $show_time => $dj ) {

				if ( is_array( $dj ) && 'override' === $dj['type'] ) {
					?>
					<li class="on-air-dj">
						<?php
						if ( $atts['show_avatar'] ) {
							if ( has_post_thumbnail( $dj['post_id'] ) ) {
								?>
								<span class="on-air-dj-avatar"><?php echo get_the_post_thumbnail( $dj['post_id'], 'thumbnail' ); ?></span>
								<?php
							}
						}

						echo esc_html( $dj['title'] );
						if ( $atts['show_sched'] ) {

							if ( 12 === (int) $atts['time'] ) {
								?>
								<span class="on-air-dj-sched">
									<?php echo esc_html( $dj['sched']['start_hour'] . ':' . $dj['sched']['start_min'] . ' ' . $dj['sched']['start_meridian'] . '-' . $dj['sched']['end_hour'] . ':' . $dj['sched']['end_min'] . ' ' . $dj['sched']['end_meridian'] ); ?>
								</span><br />
								<?php
							} else {
								$dj['sched'] = radio_station_convert_schedule_to_24hour( $dj['sched'] );
								?>
								<span class="on-air-dj-sched">
									<?php echo esc_html( $dj['sched']['start_hour'] . ':' . $dj['sched']['start_min'] . ' -' . $dj['sched']['end_hour'] . ':' . $dj['sched']['end_min'] ); ?>
								</span><br />
								<?php
							}
						}
						?>
					</li>';
					<?php
				} else {
					?>
					<li class="on-air-dj">
						<?php
						if ( $atts['show_avatar'] ) {
							?>
							<span class="on-air-dj-avatar">
								<?php echo get_the_post_thumbnail( $dj->ID, 'thumbnail' ); ?>
							</span>
							<?php
						}
						?>
						<span class="on-air-dj-title">
							<?php
							if ( $atts['show_link'] ) {
								?>
								<a href="<?php echo esc_url( get_permalink( $dj->ID ) ); ?>">
									<?php echo esc_html( $dj->post_title ); ?>
								</a>
								<?php
							} else {
								echo esc_html( $dj->post_title );
							}
							?>
						</span>
						<?php
						if ( $atts['display_djs'] ) {

							$names = get_post_meta( $dj->ID, 'show_user_list', true );
							$count = 0;

							if ( $names ) {
								?>
								<div class="on-air-dj-names">With
									<?php
									foreach ( $names as $name ) {
										$count++;
										$user_info = get_userdata( $name );

										echo esc_html( $user_info->display_name );

										$count_names = count( $names );
										if ( ( 1 === $count && 2 === $count_names ) || ( $count_names > 2 && $count === $count_names - 1 ) ) {
											echo ' and ';
										} elseif ( $count < $count_names && $count_names > 2 ) {
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
						if ( $atts['show_sched'] ) {
							$show_times = explode( '|', $show_time );
							if ( 12 === $atts['time'] ) {
								?>
								<span class="on-air-dj-sched">
									<span class="on-air-dj-sched-day">
										<?php echo esc_html__( date( 'l', $show_times[0] ), 'radio-station' ); ?>,
									</span>
									<?php echo esc_html( date( 'g:i a', $show_times[0] ) . '-' . date( 'g:i a', $show_times[1] ) ); ?>
								</span><br />
								<?php
							} else {
								?>
								<span class="on-air-dj-sched">
									<span class="on-air-dj-sched-day">
										<?php echo esc_html__( date( 'l', $show_times[0] ), 'radio-station' ); ?>,
									</span>
									<?php echo esc_html( date( 'H:i', $show_times[0] ) . '-' . date( 'H:i', $show_times[1] ) ); ?>
								</span><br />
								<?php
							}
						}
						?>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</div>
	<?php
	return ob_get_clean();

}
add_shortcode( 'dj-coming-up-widget', 'radio_station_shortcode_coming_up' );
