<?php

// === Show Content Template ===
// Package: radio-station
// Author: Tony Hayes
// @since 2.3.0

// -----------------
// Set Template Data
// -----------------

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
$genres = wp_get_post_terms( $post_id, RADIO_STATION_GENRES_SLUG );
$languages = wp_get_post_terms( $post_id, RADIO_STATION_LANGUAGES_SLUG );
// if ( $languages && !is_array( $languages ) ) {
//	$languages = array( $languages );
// }
$hosts = get_post_meta( $post_id, 'show_user_list', true );
$producers = get_post_meta( $post_id, 'show_producer_list', true );
$active = get_post_meta( $post_id, 'show_active', true );
$shifts = get_post_meta( $post_id, 'show_sched', true );

// --- get show icon / button data ---
$show_file = get_post_meta( $post_id, 'show_file', true );
$show_link = get_post_meta( $post_id, 'show_link', true );
$show_email = get_post_meta( $post_id, 'show_email', true );
$show_patreon = get_post_meta( $post_id, 'show_patreon', true );
$patreon_title = __( 'Become a Supporter for', 'radio-station' ) . ' ' . $show_title;
// $show_rss = get_post_meta( $post_id, 'show_rss', true );
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
$show_patreon = apply_filters( 'radio_station_show_patreon', $show_patreon, $post_id );
$patreon_title = apply_filters( 'radio_station_show_patreon_title', $patreon_title, $post_id );
$show_rss = apply_filters( 'radio_station_show_rss', $show_rss, $post_id );

// --- create show icon display early ---
// 2.3.0: converted show links to icons
$show_icons = array();

// --- show home link icon ---
if ( $show_link ) {
	$title = esc_attr( __( 'Show Website', 'radio-station' ) );
	$icon = '<span style="color:#A44B73;" class="dashicons dashicons-admin-links"></span>';
	$icon = apply_filters( 'radio_station_show_home_icon', $icon, $post_id );
	$show_icons['home'] = '<div class="show-icon show-website">';
	$show_icons['home'] .= '<a href="' . esc_url( $show_link ) . '" title="' . $title . '" target="_blank">';
	$show_icons['home'] .= $icon;
	$show_icons['home'] .= '</a>';
	$show_icons['home'] .= '</div>';
}

// --- email DJ / host icon ---
if ( $show_email ) {
	$title = esc_attr( __( 'Email Show Host', 'radio-station' ) );
	$icon = '<span style="color:#0086CC;" class="dashicons dashicons-email"></span>';
	$icon = apply_filters( 'radio_station_show_email_icon', $icon, $post_id );
	$show_icons['email'] = '<div class="show-icon show-email">';
	$show_icons['email'] .= '<a href="mailto:' . sanitize_email( $show_email ) . '" title="' . $title . '">';
	$show_icons['email'] .= $icon;
	$show_icons['email'] .= '</a>';
	$show_icons['email'] .= '</div>';
}

// --- show RSS feed icon ---
if ( $show_rss ) {
	$feed_url = radio_station_get_show_rss_url( $post_id );
	$title = esc_attr( __( 'Show RSS Feed', 'radio-station' ) );
	$icon = '<span style="color:#FF6E01;" class="dashicons dashicons-rss"></span>';
	$icon = apply_filters( 'radio_station_show_rss_icon', $icon, $post_id );
	$show_icons['rss'] = '<div class="show-icon show-rss">';
	$show_icons['rss'] .= '<a href="' . esc_url( $feed_url ) . '" title="' . $title . '">';
	$show_icons['rss'] .= $icon;
	$show_icons['rss'] .= '</a>';
	$show_icons['rss'] .= '</div>';
}

// --- filter show icons ---
$show_icons = apply_filters( 'radio_station_show_page_icons', $show_icons, $post_id );

// --- set show related defaults ---
$show_latest = $show_posts = $show_playlists = $show_episodes = false;

// --- check for show blog posts ---
// $latest_limit = radio_station_get_setting( 'show_latest_posts' );
// $latest_limit = false;
// $latest_limit = apply_filters( 'radio_station_show_page_latest_limit', $latest_limit, $post_id );
// if ( absint( $latest_limit ) > 0 ) {
//	$show_latest = radio_station_get_show_posts( $post_id, array( 'limit' => $latest_limit ) );
// }

// --- check for show blog posts ---
$posts_per_page = radio_station_get_setting( 'show_posts_per_page' );
if ( absint( $posts_per_page ) > 0 ) {
	$limit = apply_filters( 'radio_station_show_page_posts_limit', false, $post_id );
	$show_posts = radio_station_get_show_posts( $post_id, array( 'limit' => $limit ) );
}

// --- check for show playlists ---
$playlists_per_page = radio_station_get_setting( 'show_playlists_per_page' );
if ( absint( $playlists_per_page ) > 0 ) {
	$limit = apply_filters( 'radio_station_show_page_playlist_limit', false, $post_id );
	$show_playlists = radio_station_get_show_playlists( $post_id, array( 'limit' => $limit ) );
}

// --- check for show episodes ---
$episodes_per_page = radio_station_get_setting( 'show_episodes_per_page' );
$show_episodes = apply_filters( 'radio_station_show_page_episodes', false, $post_id );

// --- get layout display settings ----
$block_position = radio_station_get_setting( 'show_block_position' );
$section_layout = radio_station_get_setting( 'show_section_layout' );
$jump_links = apply_filters( 'radio_station_show_jump_links', 'yes', $post_id );


// ------------------
// Set Blocks Content
// ------------------

// --- set empty blocks ---
$blocks = array( 'show_images' => '', 'show_meta' => '', 'show_schedule' => '' );

// Show Images Block
// -----------------
if ( ( $avatar_id || $thumbnail_id ) || ( count( $show_icons ) > 0 ) || ( $show_file ) ) {

	// --- Show Avatar ---
	if ( $avatar_id || $thumbnail_id ) {
		// --- get show avatar (with thumbnail fallback) ---
		$size = apply_filters( 'radio_station_show_avatar_size', 'medium', $post_id, 'show-page' );
		$attr = array( 'class' => 'show-image' );
		if ( $show_title ) {
			$attr['alt'] = $attr['title'] = $show_title;
		}
		$show_avatar = radio_station_get_show_avatar( $post_id, $size, $attr );
		if ( $show_avatar ) {
			if ( $header_id ) {
				$class = ' has-header-image';
			} else {
				$class = '';
			}
			$blocks['show_images'] .= '<div class="show-avatar' . esc_attr( $class ) . '">';
			$blocks['show_images'] .= $show_avatar;
			$blocks['show_images'] .= '</div>';
		}
	}

	// --- show controls
	if ( ( count( $show_icons ) > 0 ) || ( $show_file ) ) {

		$blocks['show_images'] .= '<div class="show-controls">';

		// --- Show Icons ---
		if ( count( $show_icons ) > 0 ) {
			$blocks['show_images'] .= '<div class="show-icons">';
			$blocks['show_images'] .= implode( "\n", $show_icons );
			$blocks['show_images'] .= '</div>';
		}

		// --- Show Patreon Button ---
		if ( $show_patreon ) {
			$blocks['show_images'] .= '<div class="show-patreon">';
			$blocks['show_images'] .= radio_station_patreon_button( $show_patreon, $patreon_title );
			$blocks['show_images'] .= '</div>';
		}

		// --- Show Player ---
		// 2.3.0: embed latest broadcast audio player
		if ( $show_file ) {
			$blocks['show_images'] .= '<div class="show-player">';
			$shortcode = '[audio src="' . $show_file . '" preload="metadata"]';
			$player_embed = do_shortcode( $shortcode );
			$blocks['show_images'] .= '<div class="show-embed">';
			$blocks['show_images'] .= $player_embed;
			$blocks['show_images'] .= '</div>';

			// --- Download Audio Icon ---
			$title = __( 'Download Latest Broadcast', 'radio-station' );
			$blocks['show_images'] .= '<div class="show-download">';
			$blocks['show_images'] .= '<a href="' . esc_url( $show_file ) . '" title="' . esc_attr( $title ) . '">';
			$blocks['show_images'] .= '<span style="color:#7DBB00;" class="dashicons dashicons-download"></span>';
			$blocks['show_images'] .= '</a>';
			$blocks['show_images'] .= '</div>';
			$blocks['show_images'] .= '</div>';
		}

		$blocks['show_images'] .= '</div>';
	}
}

// Show Meta Block
// ---------------
if ( $hosts || $producers || $genres || $languages ) {

	// --- show meta title ---
	$blocks['show_meta'] = '<h4>' . esc_html( __( 'Show Info', 'radio-station' ) ) . '</h4>';

	// --- Show DJs / Hosts ---
	if ( $hosts ) {
		$blocks['show_meta'] .= '<div class="show-djs show-hosts">';
		$blocks['show_meta'] .= '<b>' . esc_html( __( 'Hosted by', 'radio-station' ) ) . '</b>: ';
		$count = 0;
		$host_count = count( $hosts );
		foreach ( $hosts as $host ) {
			$count ++;
			$user_info = get_userdata( $host );

			// --- DJ / Host URL / display---
			$host_url = radio_station_get_host_url( $host );
			if ( $host_url ) {
				$blocks['show_meta'] .= '<a href="' . esc_url( $host_url ) . '">';
			}
			$blocks['show_meta'] .= esc_html( $user_info->display_name );
			if ( $host_url ) {
				$blocks['show_meta'] .= '</a>';
			}

			if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
			     || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
				$blocks['show_meta'] .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
			} elseif ( ( count( $hosts ) > 2 ) && ( $count < count( $hosts ) ) ) {
				$blocks['show_meta'] .= ', ';
			}
		}
		$blocks['show_meta'] .= '</div>';
	}

	// --- Show Producers ---
	// 2.3.0: added assigned producer display
	if ( $producers ) {
		$blocks['show_meta'] .= '<div class="show-producers">';
		$blocks['show_meta'] .= '<b>' . esc_html( __( 'Produced by', 'radio-station' ) ) . '</b>: ';
		$count = 0;
		$producer_count = count( $producers );
		foreach ( $producers as $producer ) {
			$count ++;
			$user_info = get_userdata( $producer );

			// --- Producer URL / display ---
			$producer_url = radio_station_get_producer_url( $producer );
			if ( $producer_url ) {
				$blocks['show_meta'] .= '<a href="' . esc_url( $producer_url ) . '">';
			}
			$blocks['show_meta'] .= esc_html( $user_info->display_name );
			if ( $producer_url ) {
				$blocks['show_meta'] .= '</a>';
			}

			if ( ( ( 1 === $count ) && ( 2 === $producer_count ) )
			     || ( ( $producer_count > 2 ) && ( ( $producer_count - 1 ) === $count ) ) ) {
				$blocks['show_meta'] .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
			} elseif ( ( count( $producers ) > 2 ) && ( $count < count( $producers ) ) ) {
				$blocks['show_meta'] .= ', ';
			}
		}
		$blocks['show_meta'] .= '</div>';
	}

	// --- Show Genre(s) ---
	// 2.3.0: only display if genre assigned
	if ( $genres ) {
		$tax_object = get_taxonomy( RADIO_STATION_GENRES_SLUG );
		if ( count( $genres ) == 1 ) {
			$label = $tax_object->labels->singular_name;
		} else {
			$label = $tax_object->labels->name;
		}
		$blocks['show_meta'] .= '<div class="show-genres">';
		$blocks['show_meta'] .= '<b>' . esc_html( $label ) . '</b>: ';
		$genre_links = array();
		foreach ( $genres as $genre ) {
			$genre_link = get_term_link( $genre );
			$genre_links[] = '<a href="' . esc_url( $genre_link ) . '">' . esc_html( $genre->name ) . '</a>';
		}
		$blocks['show_meta'] .= implode( ', ', $genre_links );
		$blocks['show_meta'] .= '</div>';
	}

	// --- Show Language(s) ---
	// 2.3.0: only display if language is assigned
	if ( $languages ) {
		$tax_object = get_taxonomy( RADIO_STATION_LANGUAGES_SLUG );
		if ( count( $languages ) == 1 ) {
			$label = $tax_object->labels->singular_name;
		} else {
			$label = $tax_object->labels->name;
		}

		$blocks['show_meta'] .= '<div class="show-languages">';
		$blocks['show_meta'] .= '<b>' . esc_html( $label ) . '</b>: ';
		$language_links = array();
		foreach ( $languages as $language ) {
			$lang_label = $language->name;
			if ( !empty( $language->description ) ) {
				$lang_label .= ' (' . $language->description . ')';
			}
			$language_link = get_term_link( $language );
			$language_links[] = '<a href="' . esc_url( $language_link ) . '">' . esc_html( $lang_label ) . '</a>';
		}
		$blocks['show_meta'] .= implode( ', ', $language_links );
		$blocks['show_meta'] .= '</div>';
	}
}

// Show Times Block
// ----------------

// --- check to remove incomplete and disabled shifts ---
if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
	foreach ( $shifts as $i => $shift ) {
		$shift = radio_station_validate_shift( $shift );
		if ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) {
			unset( $shifts[$i] );
		}
	}
	if ( count( $shifts ) == 0 ) {
		$shifts = false;
	}
}

// --- show times title ---
$blocks['show_times'] = '<h4>' . esc_html( __( 'Show Times', 'radio-station' ) ) . '</h4>';

// --- check if show is active and has shifts ---
if ( !$active || !$shifts ) {

	$blocks['show_times'] .= esc_html( __( 'Not Currently Scheduled.', 'radio-station' ) );

} else {

	// --- get timezone and offset ---
	$timezone = radio_station_get_setting( 'timezone_location' );
	if ( !$timezone || ( '' == $timezone ) ) {
		// --- fallback to WordPress timezone ---
		$timezone = get_option( 'timezone_string' );
		if ( false !== strpos( $timezone, 'Etc/GMT' ) ) {
			$timezone = '';
		}
		if ( '' == $timezone ) {
			$offset = get_option( 'gmt_offset' );
		}
	}
	if ( $timezone && ( '' != $timezone ) ) {
		$timezone_code = radio_station_get_timezone_code( $timezone );
		$datetimezone = new DateTimeZone( $timezone );
		$offset = $datetimezone->getOffset( new DateTime() );
		$offset = round( $offset / 60 / 60 );
	}
	if ( strstr( (string) $offset, '.' ) ) {
		if ( substr( $offset, - 2, 2 ) == '.5' ) {
			$offset = str_replace( '.5', ':30', $offset );
		} elseif ( substr( $offset, - 3, 3 ) == '.75' ) {
			$offset = str_replace( '.75', ':45', $offset );
		} elseif ( substr( $offset, - 3, 3 ) == '.25' ) {
			$offset = str_replace( '.25', ':15', $offset );
		}
	}
	if ( 0 == $offset ) {
		$utc_offset = '';
	} elseif ( $offset > 0 ) {
		$utc_offset = '+' . $offset;
	} else {
		$utc_offset = $offset;
	}

	// --- display timezone ---
	$blocks['show_times'] .= '<b>' . esc_html( __( 'Timezone', 'radio-station' ) ) . '</b>: ';
	if ( !isset( $timezone_code ) ) {
		$blocks['show_times'] .= esc_html( __( 'UTC', 'radio-station' ) ) . $utc_offset;
	} else {
		$blocks['show_times'] .= esc_html( $timezone_code );
		$blocks['show_times'] .= '<span class="show-offset">';
		$blocks['show_times'] .= ' (' . esc_html( __( 'UTC', 'radio-station' ) ) . $utc_offset . ')';
		$blocks['show_times'] .= '</span>';
	}

	// TODO: --- display user timezone ---
	// $block['show_times'] .= ...

	$blocks['show_times'] .= '<table class="show-times" cellpadding="0" cellspacing="0">';

	$found_encore = false;
	$am = radio_station_translate_meridiem( 'am' );
	$pm = radio_station_translate_meridiem( 'pm' );
	$weekdays = radio_station_get_schedule_weekdays();
	$current_time = strtotime( current_time( 'mysql' ) );
	foreach ( $weekdays as $day ) {
		$show_times = array();
		foreach ( $shifts as $shift ) {
			if ( $day == $shift['day'] ) {

				// --- convert shift info ---
				$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
				$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
				$shift_start_time = strtotime( $start );
				$shift_end_time = strtotime( $end );
				if ( $shift_end_time < $shift_start_time ) {
					$shift_end_time = $shift_end_time + ( 7 * 60 * 60 );
				}

				// --- maybe convert to 24 hour format ---
				if ( 24 == (int) $time_format ) {
					$start = radio_station_convert_shift_time( $start, 24 );
					$end = radio_station_convert_shift_time( $end, 24 );
					$data_format = 'G:i';
				} else {
					$start = str_replace( array( 'am', 'pm' ), array( $am, $pm ), $start );
					$end = str_replace( array( 'am', 'pm' ), array( $am, $pm ), $end );
					$data_format = 'H:i a';
				}

				// --- check if current shift ---
				$classes = array( 'show-shift-time' );
				if ( ( $current_time > $shift_start_time ) && ( $current_time < $shift_end_time ) ) {
					$classes[] = 'current-shift';
				}
				$class = implode( ' ', $classes );

				// --- set show time output ---
				$show_time = '<div class="' . esc_attr( $class ) . '">';
				$show_time .= '<span class="rs-time" data-format="' . esc_attr( $data_format ) . '">' . esc_html( $start ) . '</span>';
				$show_time .= ' - <span class="rs-time" data-format="' . esc_attr( $data_format ) . '">' . esc_html( $end ) . '</span>';
				if ( isset( $shift['encore'] ) && ( 'on' == $shift['encore'] ) ) {
					$found_encore = true;
					$show_time .= '<span class="show-encore">*</span>';
				}
				$show_time .= '</div>';
				$show_times[] = $show_time;
			}
		}
		$show_times_count = count( $show_times );
		if ( $show_times_count > 0 ) {
			$blocks['show_times'] .= '<td class="show-day-time ' . strtolower( $day ) . '">';
			$weekday = radio_station_translate_weekday( $day, true );
			$blocks['show_times'] .= '<b>' . esc_html( $weekday ) . '</b>: ';
			$blocks['show_times'] .= '</td><td>';
			foreach ( $show_times as $i => $show_time ) {
				$blocks['show_times'] .= '<span class="show-time">' . $show_time . '</span>';
				// if ( $i < ( $show_times_count - 1 ) ) {
				//	$blocks['show_times'] .= '<br>';
				// }
			}
			$blocks['show_times'] .= '</td></tr>';
		}
	}

	// --- * encore note ---
	if ( $found_encore ) {
		$blocks['show_times'] .= '<tr><td></td><td>';
		$blocks['show_times'] .= '<span class="show-encore">*</span> ';
		$blocks['show_times'] .= '<span class="show-encore-label">';
		$blocks['show_times'] .= esc_html( __( 'Encore Presentation', 'radio-station' ) );
		$blocks['show_times'] .= '</span>';
		$blocks['show_times'] .= '</td></tr>';
	}

	$blocks['show_times'] .= '</table>';
}

// --- maybe add link to full schedule page ---
$schedule_page = radio_station_get_setting( 'schedule_page' );
if ( $schedule_page && !empty( $schedule_page ) ) {
	$schedule_link = get_permalink( $schedule_page );
	$blocks['show_times'] .= '<div class="show-schedule-link">';
	$blocks['show_times'] .= '<a href="' . esc_url( $schedule_link ) . '" title="' . esc_attr( __( 'Go to Full Station Schedule Page', 'radio-station' ) ) . '">';
	$blocks['show_times'] .= esc_html( __( 'Full Station Schedule', 'radio-station' ) ) . ' &rarr;</a>';
	$blocks['show_times'] .= '</div>';
}

// --- filter show info blocks ---
$blocks = apply_filters( 'radio_station_show_page_blocks', $blocks, $post_id );


// -----------------
// Set Show Sections
// -----------------
// 2.3.0: add show information sections

// Set Show Description
// --------------------
$show_description = false;
if ( strlen( trim( $content ) ) > 0 ) {
	$show_description = '<div class="show-desc-content">' . $content . '</div>';
	$show_description .= '<div id="show-more-overlay"></div>';
	$show_desc_buttons = '<div id="show-desc-buttons">';
	$show_desc_buttons .= '	<input type="button" id="show-desc-more" onclick="radio_show_desc(\'more\');" value="' . esc_html( __( 'Show More', 'radio-station' ) ) . '">';
	$show_desc_buttons .= '	<input type="button" id="show-desc-less" onclick="radio_show_desc(\'less\');" value="' . esc_html( __( 'Show Less', 'radio-station' ) ) . '">';
	$show_desc_buttons .= '	<input type="hidden" id="show-desc-state" value="">';
	$show_desc_buttons .= '</div>';
}

// Show Sections
// -------------
$sections = array();
if ( ( strlen( trim( $content ) ) > 0 ) || $show_posts || $show_playlists || $show_episodes ) {

	// --- About Show Tab (Post Content) ---
	$i = 0;
	if ( $show_description ) {

		$sections['about']['heading'] = '<h3 id="show-section-about">' . esc_html( __( 'About the Show', 'radio-station' ) ) . '</h3>';
		$sections['about']['anchor'] = __( 'About', 'radio-station' );

		// $sections['about']['label'] = '<div id="show-about-tab" class="show-tab tab-active" onclick="radio_show_tab(\'about\');">';
		// 	$sections['about']['label'] .= esc_html( 'About', 'radio-station' );
		// $sections['about']['label'] .= '</div>';

		$sections['about']['content'] = '<div id="show-about" class="show-tab tab-active"><br>';
		$sections['about']['content'] .= '<div id="show-description" class="show-description">';
		$sections['about']['content'] .= $show_description;
		$sections['about']['content'] .= '</div>';
		$sections['about']['content'] .= $show_desc_buttons;
		$sections['about']['content'] .= '</div>';
		$i ++;
	}

	// --- Show Episodes Tab ---
	if ( $show_episodes ) {

		$sections['episodes']['heading'] = '<h3 id="show-section-episodes">' . esc_html( __( 'Show Episodes', 'radio-station' ) ) . '</h3>';
		$sections['episodes']['anchor'] = __( 'Episodes', 'radio-station' );

		// $sections['episodes']['label'] = '<div id="show-episodes-tab" class="show-tab ';
		// if ( $i == 0 ) {$class = "tab-active";} else {$class = "tab-inactive";}
		// $sections['episodes']['label'] .= ' ' . $class . '"  onclick="radio_show_tab(\'episodes\');">';
		//	$sections['episodes']['label'] .= esc_html( 'Episodes', 'radio-station' );
		// $sections['episodes']['label'] .= '</div>';

		$sections['episodes']['content'] = '<div id="show-episodes" class="show-section-content"><br>';
		$radio_station_data['show-episodes'] = $show_posts;
		$shortcode = '[show-episodes-list per_page="' . $episodes_per_page . '"]';
		$shortcode = apply_filters( 'radio_station_show_page_episodes_shortcode', $shortcode, $post_id );
		$sections['episodes']['content'] .= do_shortcode( $shortcode );
		$sections['episodes']['content'] .= '</div>';
		$i ++;
	}

	// --- Show Blog Posts Tab ---
	if ( $show_posts ) {

		$sections['posts']['heading'] = '<h3 id="show-section-posts">' . esc_html( __( 'Show Posts', 'radio-station' ) ) . '</h3>';
		$sections['posts']['anchor'] = __( 'Posts', 'radio-station' );

		// if ( $i == 0 ) {$class = "tab-active";} else {$class = "tab-inactive";}
		// $sections['posts']['label'] = '<div id="show-posts-tab" class="show-tab ' . $class . '" onclick="radio_show_tab(\'posts\');">';
		//	$sections['posts']['label'] .= esc_html( 'Posts', 'radio-station' );
		// $sections['posts']['label'] .= '</div>';

		$sections['posts']['content'] = '<div id="show-posts" class="show-section-content"><br>';
		$radio_station_data['show-posts'] = $show_posts;
		$shortcode = '[show-posts-list per_page="' . $posts_per_page . '"]';
		$shortcode = apply_filters( 'radio_station_show_page_posts_shortcode', $shortcode, $post_id );
		$sections['posts']['content'] .= do_shortcode( $shortcode );
		$sections['posts']['content'] .= '</div>';
		$i ++;
	}

	// --- Show Playlists Tab ---
	if ( $show_playlists ) {

		$sections['playlists']['heading'] = '<h3 id="show-section-playlists">' . esc_html( __( 'Show Playlists', 'radio-station' ) ) . '</h3>';
		$sections['playlists']['anchor'] = __( 'Playlists', 'radio-station' );

		// if ( $i == 0 ) {$class = "tab-active";} else {$class = "tab-inactive";}
		// $sections['playlists']['label'] = '<div id="show-playlists-tab" class="show-tab ' . $class . '" onclick="radio_show_tab(\'playlists\');">';
		//	$sections['playlists']['label'] .= esc_html( 'Playlists', 'radio-station' );
		// $sections['playlists']['label'] .= '</div>';

		$sections['playlists']['content'] = '<div id="show-playlists" class="show-section-content"><br>';
		$radio_station_data['show-playlists'] = $show_playlists;
		$shortcode = '[show-playlists-list per_page="' . $playlists_per_page . '"]';
		$shortcode = apply_filters( 'radio_station_show_page_playlists_shortcode', $shortcode, $post_id );
		$sections['playlists']['content'] .= do_shortcode( $shortcode );
		$sections['playlists']['content'] .= '</div>';
		$i ++;
	}
}
$sections = apply_filters( 'radio_station_show_page_sections', $sections, $post_id );


// ---------------
// Template Output
// ---------------

// --- set content classes ---
$classes = array();
if ( 'right' == $block_position ) {
	$classes[] = 'right-blocks';
} elseif ( 'top' == $block_position ) {
	$classes[] = 'top-blocks';
} else {
	$classes[] = 'left-blocks';
}
$class = implode( ' ', $classes );

?>
	<!-- #show-content -->
	<div id="show-content" class="<?php echo esc_attr( $class ); ?>">

		<?php

		// --- Show Header ---
		// 2.3.0: added new optional show header display
		$header = radio_station_get_setting( 'show_header_image' );
		if ( $header && $header_id ) {
			$size = apply_filters( 'radio_station_show_header_size', 'full', $post_id );
			$header_src = wp_get_attachment_image_src( $header_id, $size );
			$header_url = $header_src[0];
			$header_width = $header_src[1];
			$header_height = $header_src[2];
			$header_image = '<div class="show-header">';
			$header_image .= '<img class="show-image" src="' . esc_url( $header_url ) . '" width="' . esc_attr( $header_width ) . '" height="' . esc_attr( $header_height ) . '">';
			$header_image .= '</div><br>';
			$header_image = apply_filters( 'radio_station_show_page_header_image', $header_image, $post_id );
			echo wp_kses_post( $header_image );
		}

		// --- Show Info Blocks ---
		?>

	    <div class="show-info">

			<?php

			// --- filter block order ---
			$block_order = array( 'show_images', 'show_meta', 'show_times' );
			$block_order = apply_filters( 'radio_station_show_page_block_order', $block_order, $post_id );

			// --- loop blocks ---
			if ( is_array( $block_order ) && ( count( $block_order ) > 0 ) ) {
				foreach ( $block_order as $i => $block ) {
					if ( isset( $blocks[$block] ) && ( '' != trim( $blocks[$block] ) ) ) {

						// --- set block classes ---
						$classes = array( 'show-block' );
						$classes[] = str_replace( '_', '-', $block );
						if ( 0 == $i ) {
							$classes[] = 'first-block';
						} elseif ( count( $block_order ) == ( $i + 1 ) ) {
							$classes[] = 'last-block';
						}
						$class = implode( ' ', $classes );

						// --- output blocks ---
						echo '<div class="' . esc_attr( $class ) . '">';
						echo $blocks[$block]; // phpcs:ignore WordPress.Security.OutputNotEscaped
						echo '</div>';

						$first = '';
					}
				}
			}
		?>

		</div>

		<div class="show-sections">

			<?php

			// --- Display Latest Show Posts ---
			if ( $show_latest ) {
			?>

				<div id="show-latest">
					<div class="show-latest-title">
						<b><?php echo esc_html( __( 'Latest Show Posts', 'radio-station' ) ); ?></b>
					</div>
					<?php
					$radio_station_data['show-latests'] = $show_latest;
					$shortcode = '[show-latest-list thumbnails="0" pagination="0" content="none"]';
					$shortcode = apply_filters( 'radio_station_show_page_latest_shortcode', $shortcode, $post_id );
					echo wp_kses_post( do_shortcode( $shortcode ) );
					?>
				</div>

				<?php
			}

			// --- filter section order ---
			$section_order = array( 'about', 'episodes', 'posts', 'playlists', 'hosts', 'producers' );
			$section_order = apply_filters( 'radio_station_show_page_section_order', $section_order, $post_id );

			// --- Display Show Sections ---
			// 2.3.0: filter show sections for display
			if ( ( is_array( $sections ) && ( count( $sections ) > 0 ) )
			     && is_array( $section_order ) && ( count( $section_order ) > 0 ) ) {

				// --- tabs for tabbed layout ---
				if ( 'tabbed' == $section_layout ) {

					// --- output first section as non-tabbed ---
					if ( isset( $sections[$section_order[0]] ) ) {
						echo wp_kses_post( $sections[$section_order[0]]['heading'] );
						echo $sections[$section_order[0]]['content'];
					}
					unset( $section_order[0] );

					?>

					<div class="show-tabs">
						<?php
                        $i = 0;
						foreach ( $section_order as $section ) {
							if ( isset( $sections[$section] ) ) {
								if ( 0 == $i ) {
									$class = "tab-active";
								} else {
									$class = "tab-inactive";
								}
								echo '<div id="show-' . esc_attr( $section ) . '-tab" class="show-tab ' . esc_attr( $class ) . '" onclick="radio_show_tab(\'' . esc_attr( $section ) . '\');">';
								echo esc_html( $sections[$section]['anchor'] );
								echo '</div>';
								if ( ( $i + 1 ) < count( $sections ) ) {
									echo '<div class="show-tab-spacer">&nbsp;</div>';
								}
								$i ++;
							}
						}
						?>
                    </div>
				<?php } ?>

                <div class="show-section">
                    <?php
					$i = 0;
					foreach ( $section_order as $section ) {
						if ( isset( $sections[$section] ) ) {

							if ( 'tabbed' != $section_layout ) {

								// --- section heading ---
								echo wp_kses_post( $sections[$section]['heading'] );

								// --- section jump links ---
								if ( 'yes' == $jump_links ) {
									echo '<div class="show-jump-links">';
									echo '<b>' . esc_html( __( 'Jump to', 'radio-station' ) ) . '</b>: ';
									$found_link = false;
									foreach ( $section_order as $link ) {
										if ( isset( $sections[$link] ) && ( $link != $section ) ) {
											if ( $found_link ) {
												echo ' | ';
											}
											echo '<a href="javascript:void(0);" onclick="radio_scroll_link(\'' . esc_attr( $link ) . '\');">';
											echo esc_html( $sections[$link]['anchor'] );
											echo '</a>';
											$found_link = true;
										}
									}
									echo '</div>';
								}

							} else {

								// --- add tab classes to section ---
								$classes = array( 'show-tab' );
								if ( 0 == $i ) {
									$classes[] = 'tab-active';
								} else {
									$classes[] = 'tab-inactive';
								}
								$class = implode( ' ', $classes );
								$sections[$section]['content'] = str_replace( 'class="show-section-content"', 'class="' . esc_attr( $class ) . '"', $sections[$section]['content'] );

							}

							// --- section content ---
							// echo wp_kses_post( $sections[$section]['content'] );
							echo $sections[$section]['content']; // phpcs:ignore WordPress.Security.OutputNotEscaped

							$i ++;
						}
					}
					?>

                </div>

			<?php } ?>

        </div>

    </div>
    <!-- /#show-content -->

<?php

// --- set show page javascript ---
$js = "/* Show/Hide Audio Player */
function radio_show_player() {
	if (typeof jQuery == 'function') {jQuery('#show-player').fadeIn(1000);}
	else {document.getElementById('show-player').style.display = 'block';}
}

/* Switch Section Tabs */
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

/* Responsive Page */
function radio_show_responsive() {

	/* Check to Add Narrow Class */
	if (typeof jQuery == 'function') {
		showcontent = jQuery('#show-content');
		if (showcontent.width() < 500) {showcontent.addClass('narrow');}
		else {showcontent.removeClass('narrow');}
		
	} else {
		showcontent = document.getElementById('show-content');
		if (showcontent.offsetWidth < 500) {showcontent.classList.add('narrow');}
		else {showcontent.classList.remove('narrow');}
	}

	/* Maybe Display Show More Button */
	descstate = document.getElementById('show-desc-state');
	if ( descstate && (descstate.value != 'expanded') ) {
		showdesc = document.getElementsByClassName('show-description')[0];
		if (showdesc.offsetHeight < showdesc.scrollHeight) {
			document.getElementById('show-more-overlay').style.display = 'block';
			document.getElementById('show-desc-buttons').style.display = 'block';
			showdesc.style.paddingBottom = '0';
		} else {
			document.getElementById('show-more-overlay').style.display = 'none';
			document.getElementById('show-desc-buttons').style.display = 'none';
			showdesc.style.paddingBottom = '30px';
		}
	}
}

/* Description Show More/Less */
function radio_show_desc(moreless) {
	if (moreless == 'more') {
		if (typeof jQuery == 'function') {jQuery('#show-description').addClass('expanded');}
		else {document.getElementById('show-description').classList.add('expanded');}
		document.getElementById('show-more-overlay').style.display = 'none';		
		document.getElementById('show-desc-more').style.display = 'none';
		document.getElementById('show-desc-less').style.display = 'inline-block';
	}
	if (moreless == 'less') {
		if (typeof jQuery == 'function') {jQuery('.show-description').removeClass('expanded');}
		else {document.getElementById('show-description').classList.remove('expanded');}
		document.getElementById('show-more-overlay').style.display = 'block';
		document.getElementById('show-desc-less').style.display = 'none';
		document.getElementById('show-desc-more').style.display = '';
		radio_scroll_to('show-section-about');
	}
}

/* Section Scroll Link */
function radio_scroll_link(id) {
	if (typeof jQuery == 'function') {
		section = jQuery('#show-section-'+id);
		scrolltop = section.offset().top - section.height() - 40;
		jQuery('html, body').animate({ 'scrollTop': scrolltop }, 1000);
	} else {
		radio_scroll_to('show-section-'+id);
	}
}

/* Responsive Load and Resizing */
if (typeof jQuery == 'function') {
	jQuery(document).ready(function() {radio_show_responsive();} );
	jQuery(window).resize(function () {
		radio_resize_debounce(radio_show_responsive, 500, 'showpage');
	});
} else {
	if (window.addEventListener) {
		document.body[addEventListener]('load', radio_show_responsive, false);
		document.body[addEventListener]('resize', radio_show_responsive, false);
	} else {
		document.body[attachEvent]('onload', radio_show_responsive, false);
		document.body[attachEvent]('onresize', radio_show_responsive, false);
	}
}";

// --- enqueue script inline ---
// 2.3.0: enqueue instead of echoing
wp_add_inline_script( 'radio-station', $js );
