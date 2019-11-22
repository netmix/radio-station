<?php

// Show Content Template
// Author: Tony Hayes
// @since 2.3.0

// --- get global and get show post ID ---
global $radio_station_data, $post; 
$post_id = $radio_station_data['show-id'] = $post->ID;

// --- get schedule time format ---
$time_format = (int) radio_station_get_setting( 'clock_time_format', $post_id );

// --- get show meta ---
$show_title = get_the_title( $post_id );
$header_id = get_post_meta( $post_id, 'show_header', true );
$avatar_id = get_post_meta( $post_id, 'show_avatar', true );
$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
$genres = wp_get_post_terms( $post_id, 'genres' );
$languages = wp_get_post_terms( $post_id, 'languages' );
$hosts = get_post_meta( $post_id, 'show_user_list', true );
$producers = get_post_meta( $post_id, 'show_producer_list', true );
$active = get_post_meta( $post_id, 'show_active', true );
$shifts = get_post_meta( $post_id, 'show_sched', true );

// --- get show icon data ---
$show_file = get_post_meta( $post_id, 'show_file', true );
$show_link = get_post_meta( $post_id, 'show_link', true );
$show_email = get_post_meta( $post_id, 'show_email', true );
$show_rss = get_post_meta( $post_id, 'show_rss', true );
$show_rss = false; // TEMP

// --- filter all show meta ---
$show_title = apply_filters( 'radio_station_show_title', $show_title, $post_id );
$header_id = apply_filters( 'radio_station_show_header', $header_id, $post_id );
$avatar_id = apply_filters( 'radio_station_show_avatar', $avatar_id, $post_id );
$thumbnail_id = apply_filters( 'radio_station_show_thumbnail', $thumbnail_id, $post_id );
$genres = apply_filters( 'radio_station_show_genres', $genres, $post_id );
$languages = apply_filters( 'radio_station_show_languages', $languages, $post_id );
$hosts = apply_filters( 'radio_station_show_djs', $hosts, $post_id );
$producers = apply_filters( 'radio_station_show_producers', $producers, $post_id );
$active = apply_filters( 'radio_station_show_active', $active, $post_id );
$shifts = apply_filters( 'radio_station_show_shifts', $shifts, $post_id );
$show_file = apply_filters( 'radio_station_show_file', $show_file, $post_id );
$show_link = apply_filters( 'radio_station_show_link', $show_link, $post_id );
$show_email = apply_filters( 'radio_station_show_email', $show_email, $post_id );
$show_rss = apply_filters( 'radio_station_show_rss', $show_rss, $post_id );

// --- create show icon display early ---
// 2.3.0: converted show links to icons
$show_icons = array();

// --- show home link icon ---
if ( $show_link ) {
	$title = esc_attr( __( 'Show Website', 'radio-station' ) );
	$show_icons['home'] = '<div class="show-icon show-website">';
	$show_icons['home'] .= '<a href="' . esc_url( $show_link ) . '" title="' . $title . '">';
	$show_icons['home'] .= '<span style="color:#A44B73;" class="dashicons dashicons-admin-links"></span>';
	$show_icons['home'] .= '</a></div>';
}

// --- email DJ / host icon ---
if ( $show_email ) {
	$title = esc_attr( __( 'Email Show Host', 'radio-station' ) );
	$show_icons['email'] = '<div class="show-icon show-email">';
	$show_icons['email'] .= '<a href="mailto:' . sanitize_email( $show_email ) . '" title="' . $title . '">';
	$show_icons['email'] .= '<span style="color:#0086CC;" class="dashicons dashicons-email"></span>';
	$show_icons['email'] .= '</a></div>';
}

// --- show RSS feed icon ---
if ( $show_rss ) {
 	$feed_url = radio_station_get_show_rss_url( $post_id );
	$title = esc_attr( __( 'Show RSS Feed', 'radio-station' ) );
	$show_icons['rss'] = '<div class="show-icon show-rss">';
	$show_icons['rss'] .= '<a href="' . esc_url( $feed_url ) . '" title="' . $title . '">';
	$show_icons['rss'] .= '<span style="color:#FF6E01;" class="dashicons dashicons-rss"></span>';
	$show_icons['rss'] .= '</a></div>';
}

// --- latest audio play / download icons ---
if ( $show_file ) {
	$title = esc_attr( __( 'Load Latest Broadcast Player', 'radio-station' ) );
	$show_icons['play'] = '<div class="show-icon show-play">';
	$show_icons['play'] .= '<a href="javascript:void(0);" onclick="radio_show_player();" title="' . $title . '">';
	$show_icons['play'] .= '<span style="color:#444444;" class="dashicons dashicons-controls-play"></span>';
	$show_icons['play'] .= '</a></div>';

	$title = esc_attr( __( 'Download Latest Broadcast', 'radio-station' ) );
	$show_icons['download'] = '<div class="show-icon show-download">';
	$show_icons['download'] .= '<a href="' . esc_url( $show_file ) .'" title="' . $title . '">';
	$show_icons['download'] .= '<span style="color:#7DBB00;" class="dashicons dashicons-download"></span>';
	$show_icons['download'] .= '</a></div>';
}

// --- filter show icons ---
$show_icons = apply_filters( 'radio_station_show_icons', $show_icons, $post_id );

// --- check for show blog posts ---
$posts_per_page = radio_station_get_setting( 'show_posts_per_page' );
$limit = apply_filters( 'radio_station_show_page_posts_limit', false, $post_id );
$show_posts = radio_station_get_show_posts( $post_id, array( 'limit' => $limit ) );

// --- check for show playlists ---
$playlists_per_page = radio_station_get_setting( 'show_playlists_per_page' );
$limit = apply_filters( 'radio_station_show_page_playlist_limit', false, $post_id );
$show_playlists = radio_station_get_show_playlists( $post_id, array( 'limit' => $limit ) );

// --- [Pro] check for show episodes ---
$show_episodes = false;
if ( function_exists( 'radio_station_pro_get_show_episodes' ) ) {
	// $episodes_per_page = radio_station_pro_get_setting( 'show_episodes_per_page' );
	$episodes_per_page = 10; // TEMP: until Pro settings are working properly
	$limit = apply_filters( 'radio_station_show_page_episodes_limit', false, $post_id );
	$show_episodes = radio_station_pro_get_show_episodes( $post_id, array( 'limit' => $limit ) );
}

// --- check display states ---
if ( $hosts || $producers || $genres || $languages ) {$show_meta = true;} else {$show_meta = false;}
if ( $show_posts || $show_playlists || $show_episodes ) {$show_tabs = true;} else {$show_tabs = false;}

?>

<script>
function radio_show_player() {
	if (typeof jQuery == 'function') {jQuery('#show-player').fadeIn(1000);}
	else {document.getElementById('show-player').style.display = 'block';}
}
function radio_show_tab(tab) {
	if (typeof jQuery == 'function') {
		jQuery('.show-tab').removeClass('tab-active').addClass('tab-inactive');
		jQuery('#show-'+tab+'-tab').removeClass('tab-inactive').addClass('tab-active');
		jQuery('#show-'+tab).removeClass('tab-inactive').addClass('tab-active');
	} else {
		tabs = document.getElementsByClassName('show-tab');
		for (i = 0; i < tabs.length; i++) {
			tabs[i].className = tabs[i].className.replace('-tab-active', '-tab-inactive');
		}
		button = document.getElementById('show-'+tab+'-tab');
		button.className = button.className.replace('-tab-inactive', '-tab-active');
		content = document.getElementById('show-'+tab);
		content.className = content.className.replace('-tab-inactive', '-tab-active');
	}
}
</script>

<div id="show-content" class="show-wrapper">

	<?php
	// --- Show Header ---
	// 2.3.0: added new optional show header display

	if ( $header_id ) {
		$size = apply_filters( 'radio_station_show_header_size', 'full', $post_id );
		$header_src = wp_get_attachment_image_src( $header_id, $size );
		$header_url = $header_src[0];
		$header_width = $header_src[1];
		$header_height = $header_src[2];
	?>
	<div class="show-header">
		<img class="show-image" src="<?php echo $header_url; ?>" width="<?php echo $header_width; ?>" height="<?php echo $header_height; ?>">
	</div><br>
	<?php } ?>

	<div class="show-info">

		<div class="show-images show-info-block first">

			<?php
			// --- Show Avatar / Thumbnail ---
			if ( $avatar_id || $thumbnail_id ) { ?>
				<div class="show-avatar">
					<?php
					// --- get show avatar (with thumbnail fallback) ---
					$size = apply_filters( 'radio_station_show_avatar_size', 'thumbnail', $post_id, 'show-page' );
					$attr = array( 'class' => 'show-image' );
					$show_avatar = radio_station_get_show_avatar( $post_id, $size, $attr );
					if ( $show_avatar ) {echo $show_avatar;} 
					?>
				</div>
			<?php }

			// --- Show Icons ---
			if ( count( $show_icons ) > 0 ) { ?>
				<div class="show-icons">
					<?php echo implode( "\n", $show_icons ); ?>
				</div>
			<?php }

			// --- Audio Player ---
			// 2.3.0: embed latest broadcast audio player
			if ( $show_file ) {
				$player_embed = do_shortcode( '[audio src="' . esc_url( $show_file ) . '" preload="metadata"]' );
				?>
				<div id="show-player" class="show-player">
					<?php echo $player_embed; ?>
				</div>
			<?php } ?>
		</div>	
		
	<?php
	if ( $show_meta ) { ?>

		<div class="show-meta show-info-block">

		<h3><?php esc_html_e( $show_title ); ?></h3>

		<?php
		// --- DJs / Hosts ---
		if ( $hosts ) { ?>
			<div class="show-djs show-hosts">
				<b><?php esc_html_e( 'Hosted by', 'radio-station' ); ?></b>:
				<?php $count = 0; $host_count = count( $hosts );
				foreach ( $hosts as $host ) {
					$count++;
					$user_info = get_userdata( $host );

					// --- DJ / Host URL / display---
					$host_url = radio_station_get_host_url( $host );
					if ( $host_url ) {echo '<a href="' . esc_url( $host_url ) . '">';}
						echo esc_html( $user_info->display_name );
					if ( $host_url ) {echo '</a>';}

					if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
					  || ( ( $host_count > 2 ) && ( $count === ( $host_count - 1 ) ) ) ) {
						echo ' ' . __( 'and', 'radio-station') . ' ';
					} elseif ( ( count( $hosts ) > 2 ) && ( $count < count( $hosts ) ) ) {
						echo ', ';
					}
				} ?>
			</div>
		<?php }

		// --- Producers ---
		// 2.3.0: added assigned producer display
		if ( $producers ) { ?>
			<div class="show-producers">
				<b><?php esc_html_e( 'Produced by', 'radio-station' ); ?></b>:
				<?php $count = 0; $producer_count = count( $producers );
				foreach ( $producers as $producer ) {
					$count++;
					$user_info = get_userdata( $producer );

					// --- Producer URL / display ---
					$producer_url = radio_station_get_producer_url( $producer );
					if ( $producer_url ) {echo '<a href="' . esc_url( $producer_url ) . '">';}
						echo esc_html( $user_info->display_name );
					if ( $producer_url ) {echo '</a>';}

					if ( ( ( 1 === $count ) && ( 2 === $producer_count ) )
					  || ( ( $producer_count > 2 ) && ( $count === ( $producer_count - 1 ) ) ) ) {
						echo ' ' . __( 'and', 'radio-station') . ' ';
					} elseif ( ( count( $producers ) > 2 ) && ( $count < count( $producers ) ) ) {
						echo ', ';
					}
				} ?>
			</div>
		<?php }

		// --- Show Genre(s) ---
		// 2.3.0: only display if genre assigned
		if ( $genres ) { 
			$tax_object = get_taxonomy( 'genres' );
			if ( count( $languages ) == 1 ) {$label = $tax_object->labels->name;}
			else {$label = $tax_object->labels->singular_name;}		
			?>
			<div class="show-genres">
				<b><?php esc_html_e( $label ); ?></b>:
				<?php
					$genre_links = array();
					foreach ( $genres as $genre ) {
						$genre_link = get_term_link( $genre );
						$genre_links[] = '<a href="' . esc_url( $genre_link ) . '">' . esc_html( $genre->name ) . '</a>';
					}
					echo implode( ', ', $genre_links );
				?>
			</div>
		<?php }

		// --- Show Language(s) ---
		// 2.3.0: only display if language is assigned
		if ( $languages ) { 
			$tax_object = get_taxonomy( 'languages' );
			if ( count( $languages ) == 1 ) {$label = $tax_object->labels->name;}
			else {$label = $tax_object->labels->singular_name;}
			?>
			<div class="show-languages">
				<b><?php esc_html_e( $label ); ?></b>:
				<?php
					$language_list = radio_station_get_languages();
					$language_links = array();
					foreach ( $languages as $language ) {
						// --- get the language name label ---
						foreach ( $language_list as $lang ) {
							if ( $language->name == $lang['language'] ) {
								$label = $lang['native_name'];
								if ( $lang['native_name'] != $lang['english_name'] ) {
									$label .= ' (' . $lang['english_name'] . ')';
								}
							}
						}					
						$language_link = get_term_link( $language );
						$language_links[] = '<a href="' . esc_url( $language_link ) . '">' . esc_html( $label ) . '</a>';
					}
					echo implode( ', ', $language_links );
				?>
			</div>
		<?php } ?>

		</div>

	<?php }

	// --- Show Shifts ---
	?>

		<div class="show-schedule show-info-block">

			<h3><?php esc_html_e( 'Show Times', 'radio-station' ); ?></h3>
			
			<?php 
			// --- check to remove incomplete and disabled shifts ---
			if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
				foreach ( $shifts as $i => $shift ) {
					$shift = radio_station_validate_shift( $shift );
					if ( isset( $shift['disabled'] ) && ( $shift['disabled'] == 'yes' ) ) {
						unset( $shifts[$i] );
					}
				}
				if ( count( $shifts ) == 0 ) {$shifts = false;}
			}

			// --- check if show is active and has shifts ---
			if ( !$active || !$shifts ) {
				echo __( 'Not Currently Scheduled.', 'radio-station' );
			} else {

				// --- get timezone and offset ---
				$timezone = radio_station_get_setting( 'timezone_location' );
				if ( !$timezone || ( $timezone == '' ) ) {
					// --- fallback to WordPress timezone ---
					$timezone = get_option( 'timezone_string' );
					if ( false !== strpos( $timezone, 'Etc/GMT' ) ) {$timezone = '';}
					if ( $timezone == '' ) {
						$offset = get_option( 'gmt_offset' );
					}
				} 
				if ( $timezone && ( $timezone != '' ) ) {
					$timezone_code = radio_station_get_timezone_code( $timezone );
					$datetimezone = new DateTimeZone( $timezone );
					$offset = $datetimezone->getOffset(new DateTime);
					$offset = round( $offset / 60 / 60 );
				}
				if ( strstr( (string) $offset, '.' ) ) {
					if ( substr( $offset, -2, 2 ) == '.5' ) {$offset = str_replace( '.5', ':30', $offset );}
					elseif ( substr( $offset, -3, 3 ) == '.75' ) {$offset = str_replace( '.75', ':45', $offset );}
					elseif ( substr( $offset, -3, 3 ) == '.25' ) {$offset = str_replace( '.25', ':15', $offset );}
				}
				if ( $offset == 0 ) {$utc_offset = '';}
				elseif ( $offset > 0 ) {$utc_offset = '+' . $offset;}
				else {$utc_offset = $offset;}

				// --- display timezone ---
				echo '<b>' . __( 'Timezone', 'radio-station' ) . '</b>: ';
				if ( !isset( $timezone_code ) ) {
					echo __( 'UTC', 'radio-station' ) . $utc_offset;
				} else {
					echo esc_html( $timezone_code );
					echo '<span class="show-offset">';
						echo ' (' . esc_html( __( 'UTC', 'radio-station' ) ) . $utc_offset . ')';
					echo '</span>';
				}
				
				echo '<table class="show-times" cellpadding="0" cellspacing="0">';

				// TODO: check for now playing show ?
				$found_encore = false;
				$am = radio_station_translate_meridiem( 'am' );
				$pm = radio_station_translate_meridiem( 'pm' );
				$weekdays = radio_station_get_schedule_weekdays();
				foreach ( $weekdays as $day ) {
					$show_times = array();
					foreach ( $shifts as $shift ) {
						if ( $day == $shift['day'] ) {

							$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
							$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];

							// --- maybe convert to 24 hour format ---
							if ( (int) $time_format == 24 ) {
								$start = radio_station_convert_shift_time( $start, 24 );
								$end = radio_station_convert_shift_time( $end, 24 );
							} else {
								$start = str_replace( array( ' am', ' pm'), array( $am, $pm ), $start ); 
								$end = str_replace( array( ' am', ' pm'), array( $am, $pm ), $end ); 
							}

							$show_time = '<span class="rs-time">' . esc_html( $start ) . '</span>';
							$show_time .= '-<span class="rs-time">' . esc_html( $end ) . '</span>';
							if ( isset( $shift['encore'] ) && ( $shift['encore'] == 'on' ) ) {
								$found_encore = true;
								$show_time .= '<span class="show-encore">*</span>';
							}
							$show_times[] = $show_time;
						}
					}
					$show_times_count = count( $show_times );
					if ( $show_times_count > 0 ) {
						echo '<td class="show-day-times ' . strtolower( $day ) . '">';
							$weekday = radio_station_translate_weekday( $day, true );
							echo '<b>' . esc_html( $weekday ) . '</b>: ';
						echo '</td><td>';
							foreach ( $show_times as $i => $show_time ) {
								echo '<span class="show-time">' . $show_time . '</span>';
								if ( $i < ( $show_times_count - 1 ) ) {echo ',<br>';}
							}
						echo '</td></tr>';
					}
				}

				if ( $found_encore ) {
					echo '<tr><td></td><td>';
						echo '<span class="show-encore">*</span> ';
						echo '<span class="show-encore-label">';
							echo esc_html( __( 'Encore Presentation', 'radio-station' ) );
						echo '</span>';
					echo '</td></tr>';
				}
				
				echo '</table>';
			} ?>		
		</div>
	</div>

<?php
// --- Show Tabs ---
// 2.3.0: add show information tabs
if ( $show_tabs ) {

	// --- About Show Tab (Post Content) ---
	$tabs = $tabbed = array(); $i = 0;
	if ( strlen( trim( $content ) ) > 0 ) {
	
		$tabs[$i]['label'] = '<div id="show-about-tab" class="show-tab tab-active" onclick="radio_show_tab(\'about\');">';
			$tabs[$i]['label'] .= esc_html( 'About', 'radio-station' );
		$tabs[$i]['label'] .= '</div>';
		
		// --- placeholder for the show content (description) ---
		$tabs[$i]['content'] = '<div id="show-about" class="show-tab tab-active"><br>';
			$tabs[$i]['content'] .= '<div class="show-description">';
				$tabs[$i]['content'] .= $content;
			$tabs[$i]['content'] .= '</div>';
		$tabs[$i]['content'] .= '</div>';
		$i++;
	}

	// --- Show Episodes Tab ---
	if ( $show_episodes ) {
	
		$tabs[$i]['label'] = '<div id="show-episodes-tab" class="show-tab ';
		if ( $i == 0 ) {$class = "tab-active";} else {$class = "tab-inactive";}
		$tabs[$i]['label'] .= ' ' . $class . '"  onclick="radio_show_tab(\'episodes\');">';
			$tabs[$i]['label'] .= esc_html( 'Episodes', 'radio-station' );
		$tabs[$i]['label'] .= '</div>';
		
		$tabs[$i]['content'] = '<div id="show-episodes" class="show-tab ' . $class . '"><br>';
			$radio_station_data['show-episodes'] = $show_posts;
			$shortcode = '[show-episodes-list per_page="' . $posts_per_page . '"]';
			$tabs[$i]['content'] .= do_shortcode( $shortcode );
		$tabs[$i]['content'] .= '</div>';
		$i++;
	}

	// --- Show Blog Posts Tab ---
	if ( $show_posts ) {
		
		if ( $i == 0 ) {$class = "tab-active";} else {$class = "tab-inactive";}
		$tabs[$i]['label'] = '<div id="show-posts-tab" class="show-tab ' . $class . '" onclick="radio_show_tab(\'posts\');">';
			$tabs[$i]['label'] .= esc_html( 'Posts', 'radio-station' );
		$tabs[$i]['label'] .= '</div>';
			
		$tabs[$i]['content'] = '<div id="show-posts" class="show-tab ' . $class . '"><br>';
			$radio_station_data['show-posts'] = $show_posts;
			$shortcode = '[show-posts-list per_page="' . $posts_per_page . '"]';
			$tabs[$i]['content'] .= do_shortcode( $shortcode );
		$tabs[$i]['content'] .= '</div>';
		
		$i++;
	}

	// --- Show Playlists Tab ---
	if ( $show_playlists ) {
	
		if ( $i == 0 ) {$class = "tab-active";} else {$class = "tab-inactive";}
		$tabs[$i]['label'] = '<div id="show-playlists-tab" class="show-tab ' . $class . '" onclick="radio_show_tab(\'playlists\');">';
			$tabs[$i]['label'] .= esc_html( 'Playlists', 'radio-station' );
		$tabs[$i]['label'] .= '</div>';
		
		$tabs[$i]['content'] = '<div id="show-playlists" class="show-tab ' . $class . '"><br>';
			$radio_station_data['show-playlists'] = $show_playlists;
			$shortcode = '[show-playlists-list per_page="' . $playlists_per_page . '"]';
			$tabs[$i]['content'] .= do_shortcode( $shortcode );	
		$tabs[$i]['content'] .= '</div>';
		$i++;
	}

	// --- Display Show Tabs ---
	// 2.3.0: filter show tabs for display
	$tabs = apply_filters( 'radio_station_show_tabs', $tabs, $post_id );
	if ( count( $tabs ) > 0 ) { ?>

		<div class="show-tabs">
			<?php $tabspacer = '<div class="show-tab-spacer">&nbsp;</div>' . "\n";
			foreach( $tabs as $i => $tab ) {echo $tab['label'] . $tabspacer;} ?>
		</div>
		
		<div class="show-tabbed">
			<?php foreach( $tabs as $i => $tab ) {echo $tab['content'] . "\n";} ?>
		</div>

	<?php
	} else {$show_tabs = false;}
}

if ( !$show_tabs ) { ?>

	<div class="show-description">
		<?php echo $content; ?>
	</div>

<?php } ?>

</div>
