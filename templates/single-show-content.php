<?php

// === Show Content Template ===
// Package: radio-station
// Author: Tony Hayes
// @since 2.3.0

// -----------------
// Set Template Data
// -----------------

// --- get global and get show post ID ---
// 2.3.3.9: added doing template flag
global $radio_station_data, $post;
$radio_station_data['doing-template'] = true;
$post_id = $radio_station_data['show-id'] = $post->ID;
$post_type = $radio_station_data['show-type'] = $post->post_type;

// 2.3.3.6: set new line for easier debug viewing
// 2.5.0: remove newline variable to use standard line breaks

// --- get schedule time format ---
$time_format = (int) radio_station_get_setting( 'clock_time_format', $post_id );

// --- get show meta data ---
$show_title = get_the_title( $post_id );
$header_id = get_post_meta( $post_id, 'show_header', true );
$avatar_id = get_post_meta( $post_id, 'show_avatar', true );
$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
$genres = wp_get_post_terms( $post_id, RADIO_STATION_GENRES_SLUG );
$languages = wp_get_post_terms( $post_id, RADIO_STATION_LANGUAGES_SLUG );
$hosts = get_post_meta( $post_id, 'show_user_list', true );
$producers = get_post_meta( $post_id, 'show_producer_list', true );
$active = get_post_meta( $post_id, 'show_active', true );
$shifts = radio_station_get_show_schedule( $post_id );

// --- get show icon / button data ---
// 2.3.3.6: added Show phone display
$show_file = get_post_meta( $post_id, 'show_file', true );
$show_link = get_post_meta( $post_id, 'show_link', true );
$show_email = get_post_meta( $post_id, 'show_email', true );
$show_phone = get_post_meta( $post_id, 'show_phone', true );
$show_patreon = get_post_meta( $post_id, 'show_patreon', true );
// $show_rss = get_post_meta( $post_id, 'show_rss', true );
$show_rss = false; // TEMP

// 2.3.2: added show download disabled check
// note: on = disabled
$disable_download = get_post_meta( $post_id, 'show_download', true );
$show_download = ( 'on' == $disable_download ) ? false : true;

// --- filter all show meta data ---
// 2.3.2: added show download filter
$show_title = apply_filters( 'radio_station_show_title', $show_title, $post_id );
$header_id = apply_filters( 'radio_station_show_header', $header_id, $post_id );
$avatar_id = apply_filters( 'radio_station_show_avatar', $avatar_id, $post_id );
$thumbnail_id = apply_filters( 'radio_station_show_thumbnail', $thumbnail_id, $post_id );
$genres = apply_filters( 'radio_station_show_genres', $genres, $post_id );
$languages = apply_filters( 'radio_station_show_languages', $languages, $post_id );
$hosts = apply_filters( 'radio_station_show_hosts', $hosts, $post_id );
$producers = apply_filters( 'radio_station_show_producers', $producers, $post_id );
$active = apply_filters( 'radio_station_show_active', $active, $post_id );
$shifts = apply_filters( 'radio_station_show_shifts', $shifts, $post_id );
$show_file = apply_filters( 'radio_station_show_file', $show_file, $post_id );
$show_download = apply_filters( 'radio_station_show_download', $show_download, $post_id );
$show_link = apply_filters( 'radio_station_show_link', $show_link, $post_id );
$show_email = apply_filters( 'radio_station_show_email', $show_email, $post_id );
$show_phone = apply_filters( 'radio_station_show_phone', $show_phone, $post_id );
$show_patreon = apply_filters( 'radio_station_show_patreon', $show_patreon, $post_id );
$show_rss = apply_filters( 'radio_station_show_rss', $show_rss, $post_id );
$social_icons = apply_filters( 'radio_station_show_social_icons', false, $post_id );

// --- get data format ---
// 2.3.2: set filterable time formats
// 2.3.3.9: moved out of show times block
if ( 24 == (int) $time_format ) {
	$start_data_format = $end_data_format = 'H:i';
} else {
	$start_data_format = $end_data_format = 'g:i a';
}
$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'show-template', $post_id );
$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'show-template', $post_id );

// --- create show icon display early ---
// 2.3.0: converted show links to icons
// 2.3.3.9: add download icon color
// 2.3.3.9: filter icon colors
$show_icons = array();
$icon_colors = radio_station_get_icon_colors( 'show-page' );

// --- show home link icon ---
// 2.3.3.4: added filter for title attribute
// 2.3.3.8: added alt text to span for screen readers
if ( $show_link ) {
	$title = __( 'Visit Show Website', 'radio-station' );
	$title = apply_filters( 'radio_station_show_website_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['website'] ) . ';" class="dashicons dashicons-admin-links" aria-hidden="true"></span>' . "\n";
	$icon = apply_filters( 'radio_station_show_home_icon', $icon, $post_id );
	$show_icons['home'] = '<div class="show-icon show-website">' . "\n";
		$show_icons['home'] .= '<a href="' . esc_url( $show_link ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '" target="_blank">' . "\n";
			$show_icons['home'] .= $icon . "\n";
		$show_icons['home'] .= '</a>' . "\n";
	$show_icons['home'] .= '</div>' . "\n";
}

// --- phone number icon ---
// 2.3.3.6: added show phone icon
// 2.3.3.8: added aria label to link and hidden to span icon
if ( $show_phone ) {
	$title = __( 'Call in Phone Number', 'radio-station' );
	$title = apply_filters( 'radio_station_show_phone_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['phone'] ) . ';" class="dashicons dashicons-phone" aria-hidden="true"></span>' . "\n";
	$icon = apply_filters( 'radio_station_show_phone_icon', $icon, $post_id );
	$show_icons['phone'] = '<div class="show-icon show-phone">' . "\n";
		$show_icons['phone'] .= '<a href="tel:' . esc_attr( $show_phone ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . "\n";
				$show_icons['phone'] .= $icon . "\n";
			$show_icons['phone'] .= '</a>' . "\n";
	$show_icons['phone'] .= '</div>' . "\n";
}

// --- email DJ / host icon ---
// 2.3.3.4: added filter for title attribute
// 2.3.3.8: added aria label to link and hidden to span icon
if ( $show_email ) {
	$title = __( 'Email Show Host', 'radio-station' );
	$title = apply_filters( 'radio_station_show_email_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['email'] ) . ';" class="dashicons dashicons-email" aria-hidden="true"></span>' . "\n";
	$icon = apply_filters( 'radio_station_show_email_icon', $icon, $post_id );
	$show_icons['email'] = '<div class="show-icon show-email">' . "\n";
		$show_icons['email'] .= '<a href="mailto:' . sanitize_email( $show_email ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . "\n";
			$show_icons['email'] .= $icon . "\n";
		$show_icons['email'] .= '</a>' . "\n";
	$show_icons['email'] .= '</div>' . "\n";
}

// --- show RSS feed icon ---
// 2.3.3.4: added filter for title attribute
// 2.3.3.8: added aria label to link and hidden to span icon
if ( $show_rss ) {
	$feed_url = radio_station_get_show_rss_url( $post_id );
	$title = __( 'Show RSS Feed', 'radio-station' );
	$title = apply_filters( 'radio_station_show_rss_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['rss'] ) . ';" class="dashicons dashicons-rss" aria-hidden="true"></span>' . "\n";
	$icon = apply_filters( 'radio_station_show_rss_icon', $icon, $post_id );
	$show_icons['rss'] = '<div class="show-icon show-rss">' . "\n";
		$show_icons['rss'] .= '<a href="' . esc_url( $feed_url ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . "\n";
			$show_icons['rss'] .= $icon . "\n";
		$show_icons['rss'] .= '</a>' . "\n";
	$show_icons['rss'] .= '</div>' . "\n";
}

// --- filter show icons ---
$show_icons = apply_filters( 'radio_station_show_page_icons', $show_icons, $post_id );

// --- set show related defaults ---
$show_latest = $show_posts = $show_playlists = false;

// --- check for latest show blog posts ---
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
	if ( RADIO_STATION_DEBUG ) {
		echo "Show Posts: " . esc_html( print_r( $show_posts, true ) ) . "\n";
	}
}

// --- check for show playlists ---
$playlists_per_page = radio_station_get_setting( 'show_playlists_per_page' );
if ( absint( $playlists_per_page ) > 0 ) {
	$limit = apply_filters( 'radio_station_show_page_playlist_limit', false, $post_id );
	$show_playlists = radio_station_get_show_playlists( $post_id, array( 'limit' => $limit ) );
	if ( RADIO_STATION_DEBUG ) {
		echo "Show Playlists: " . esc_html( print_r( $show_playlists, true ) ) . "\n";
	}
}

// --- get layout display settings ----
$block_position = radio_station_get_setting( 'show_block_position' );
$section_layout = radio_station_get_setting( 'show_section_layout' );
$jump_links = apply_filters( 'radio_station_show_jump_links', 'yes', $post_id );


// --------------------------
// === Set Blocks Content ===
// --------------------------

// --- set empty blocks ---
$blocks = array( 'show_images' => '', 'show_meta' => '', 'show_schedule' => '' );

// -----------------
// Show Images Block
// -----------------
$image_blocks = array();

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
		$blocks['show_images'] = '<div class="show-avatar' . esc_attr( $class ) . '">' . "\n";
			$blocks['show_images'] .= $show_avatar . "\n";
		$blocks['show_images'] .= '</div>' . "\n";
	}
}

// --- Show Icons ---
// 2.3.3.9: remove unnecessary image block condition check
if ( count( $show_icons ) > 0 ) {
	$image_blocks['icons'] = '<div class="show-icons">' . "\n";
		$image_blocks['icons'] .= implode( "\n", $show_icons );
	$image_blocks['icons'] .= '</div>' . "\n";
}

// --- Social Icons ---
// 2.3.3.6: added filter for social icon display output
if ( $social_icons ) {
	$social_icons = apply_filters( 'radio_station_show_social_icons_display', '' );
	if ( '' != $social_icons ) {
		$image_blocks['social'] = '<div id="show-social-icons" class="social-icons">' . "\n";
			$image_blocks['social'] .= $social_icons . "\n";
		$image_blocks['social'] .= '</div>' . "\n";
	}
}

// --- Show Patreon Button ---
$patreon_button = '';
if ( $show_patreon ) {
	$patreon_button .= '<div class="show-patreon">' . "\n";
	$title = __( 'Become a Supporter for', 'radio-station' ) . ' ' . $show_title;
	$title = apply_filters( 'radio_station_show_patreon_title', $title, $post_id );
	$patreon_button .= radio_station_patreon_button( $show_patreon, $title );
	$patreon_button .= '</div>' . "\n";
}
// 2.3.1: added filter for patreon button
$patreon_button = apply_filters( 'radio_station_show_patreon_button', $patreon_button, $post_id );
if ( '' != $patreon_button ) {
	$image_blocks['patreon'] = $patreon_button;
}

// --- Show Player ---
// 2.3.0: embed latest broadcast audio player
// 2.3.3.4: add filter for optional title above Show Player (default empty)
// 2.3.3.4: add filter for title text on Show Download link icon
if ( $show_file ) {

	$image_blocks['player'] = '<div class="show-player">' . "\n";
	$label = apply_filters( 'radio_station_show_player_label', '', $post_id );
	if ( $label && ( '' != $label ) ) {
		$image_blocks['player'] .= '<span class="show-player-label show-label">' . esc_html( $label ) . '</span><br>';
	}
	
	// 2.5.15: maybe use radio player instead of media elements
	$show_player = radio_station_get_setting( 'show_player' );
	if ( 'radio_player' == $show_player ) {
		$player_theme = radio_station_get_setting( 'show_player_theme' );
		if ( '' == $player_theme ) {
			$player_theme = radio_station_get_setting( 'player_theme' );
		}
		$player_buttons = radio_station_get_setting( 'player_buttons' );
		$atts = array( 
			'media'	   => 'file',
			'url'	   => $show_file,
			'layout'   => 'horizontal',
			'theme'    => $player_theme,
			'buttons'  => $player_buttons,
			'volumes'  => array( 'slider', 'mute' ),
			'default'  => false, // TODO: test true here
		);
		$atts = apply_filters( 'radio_station_show_player_atts', $atts );
		$player_embed = radio_player_shortcode( $atts );
	} elseif ( 'media_elements' == $show_player ) {
		// 2.5.8: run embed shortcode to embed external audio URLs
		global $wp_embed;
		$embed = '[embed]' . $show_file . '[/embed]';
		add_filter( 'embed_maybe_make_link', '__return_false' );
		$player_embed = $wp_embed->run_shortcode( $embed );
		remove_filter( 'embed_maybe_make_link', '__return_false' );
		
		if ( $player_embed && !stristr( $player_embed, '[audio' ) ) {
			// 2.5.8: full width and height fix for embeds
			// 2.5.15: move style next to embedded player
			$player_embed .= '<style>.show-embed iframe {width: 100%; height: 100%;</style>' . "\n";
		} else {
			$shortcode = '[audio src="' . $show_file . '" preload="metadata"]';
			$player_embed = do_shortcode( $shortcode );
		}
	}

	$image_blocks['player'] .= '<div class="show-embed">' . "\n";
		$image_blocks['player'] .= $player_embed . "\n";
	$image_blocks['player'] .= '</div>' . "\n";

	// --- Download Audio Icon ---
	// 2.3.2: check show download switch
	// 2.3.3.8: added aria label to link and hidden to span icon
	if ( $show_download ) {
		$title = __( 'Download Latest Broadcast', 'radio-station' );
		$title = apply_filters( 'radio_station_show_download_title', $title, $post_id );
		$image_blocks['player'] .= '<div class="show-download">' . "\n";
			$image_blocks['player'] .= '<a href="' . esc_url( $show_file ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . "\n";
				$image_blocks['player'] .= '<span style="color:' . esc_attr( $icon_colors['download'] ) . ';" class="dashicons dashicons-download" aria-hidden="true"></span>' . "\n";
			$image_blocks['player'] .= '</a>' . "\n";
		$image_blocks['player'] .= '</div>' . "\n";
	}

	$image_blocks['player'] .= '</div>' . "\n";

}

// 2.3.3.6: allow subblock order to be changed
$image_blocks = apply_filters( 'radio_station_show_images_blocks', $image_blocks, $post_id );
$image_block_order = array( 'icons', 'social', 'patreon', 'player' );
$image_block_order = apply_filters( 'radio_station_show_image_block_order', $image_block_order, $post_id );
if ( RADIO_STATION_DEBUG ) {
	echo '<span style="display:none;">Image Block Order: ' . esc_html( print_r( $image_block_order, true ) ) . '</span>';
	// echo '<span style="display:none;">Image Blocks: ' . esc_html( print_r( $image_blocks, true ) ) . '</span>';
}

// --- combine image blocks to show images block ---
if ( is_array( $image_blocks ) && ( count( $image_blocks ) > 0 ) && is_array( $image_block_order ) && ( count( $image_block_order ) > 0 ) ) {
	$blocks['show_images'] .= '<div class="show-controls">';
	foreach ( $image_block_order as $image_block ) {
		if ( isset( $image_blocks[$image_block] ) ) {
			$blocks['show_images'] .= $image_blocks[$image_block];
		}
	}
	$blocks['show_images'] .= '</div>' . "\n";
}


// ---------------
// Show Meta Block
// ---------------
// 2.3.3.6: added Show phone display section
if ( $show_phone || $hosts || $producers || $genres || $languages ) {

	$meta_blocks = array();

	// --- show meta title ---
	// 2.3.3.4: added filter for show info label
	// 2.3.3.4: added class to show info label tag
	$label = __( 'Show Info', 'radio-station' );
	$label = apply_filters( 'radio_station_show_info_label', $label, $post_id );
	$blocks['show_meta'] = '<h4 class="show-info-label">' . esc_html( $label ) . '</h4>' . "\n";

	// --- Show DJs / Hosts ---
	// 2.3.3.4: added filter for hosted by label
	// 2.3.3.4: replace bold title tag with span and class
	if ( $hosts ) {
		$meta_blocks['hosts'] = '<div class="show-djs show-hosts">' . "\n";
		$label = __( 'Hosted by', 'radio-station' );
		$label = apply_filters( 'radio_station_show_hosts_label', $label, $post_id );
		$meta_blocks['hosts'] .= '<span class="show-hosts-label show-label">' . esc_html( $label ) . '</span>: ' . "\n";
		$count = 0;
		// 2.4.0.4: convert possible (old) non-array values
		if ( !is_array( $hosts ) ) {
			$hosts = array( $hosts );
		}
		$host_count = count( $hosts );
		foreach ( $hosts as $host ) {
			$count++;
			$user_info = get_userdata( $host );

			// --- DJ / Host URL and/or display ---
			$host_url = radio_station_get_host_url( $host );
			if ( $host_url ) {
				$meta_blocks['hosts'] .= '<a href="' . esc_url( $host_url ) . '">';
			}
			$meta_blocks['hosts'] .= esc_html( $user_info->display_name );
			if ( $host_url ) {
				$meta_blocks['hosts'] .= '</a>' . "\n";
			}

			if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
					|| ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
				$meta_blocks['hosts'] .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
			} elseif ( ( count( $hosts ) > 2 ) && ( $count < count( $hosts ) ) ) {
				$meta_blocks['hosts'] .= ', ';
			}
		}
		$meta_blocks['hosts'] .= '</div>' . "\n";
	}

	// --- Show Producers ---
	// 2.3.0: added assigned producer display
	// 2.3.3.4: added filter for produced by label
	// 2.3.3.4: replace bold title tag with span and class
	if ( $producers ) {
		$meta_blocks['producers'] = '<div class="show-producers">' . "\n";
		$label = __( 'Produced by', 'radio-station' );
		$label = apply_filters( 'radio_station_show_producers_label', $label, $post_id );
		$meta_blocks['producers'] .= '<span class="show-producers-label show-label">' . esc_html( $label ) . '</span>: ' . "\n";
		$count = 0;
		// 2.4.0.4: convert possible (old) non-array values
		if ( !is_array( $producers ) ) {
			$producers = array( $producers );
		}
		$producer_count = count( $producers );
		foreach ( $producers as $producer ) {
			$count++;
			$user_info = get_userdata( $producer );

			// --- Producer URL / display ---
			$producer_url = radio_station_get_producer_url( $producer );
			if ( $producer_url ) {
				$meta_blocks['producers'] .= '<a href="' . esc_url( $producer_url ) . '">';
			}
			$meta_blocks['producers'] .= esc_html( $user_info->display_name );
			if ( $producer_url ) {
				$meta_blocks['producers'] .= '</a>' . "\n";
			}

			if ( ( ( 1 === $count ) && ( 2 === $producer_count ) )
					|| ( ( $producer_count > 2 ) && ( ( $producer_count - 1 ) === $count ) ) ) {
				$meta_blocks['producers'] .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
			} elseif ( ( count( $producers ) > 2 ) && ( $count < count( $producers ) ) ) {
				$meta_blocks['producers'] .= ', ';
			}
		}
		$meta_blocks['producers'] .= '</div>' . "\n";
	}

	// --- Show Genre(s) ---
	// 2.3.0: only display if genre assigned
	// 2.3.3.4: added filter for genres label
	// 2.3.3.4: replace bold title tag with span and class
	if ( $genres ) {
		$tax_object = get_taxonomy( RADIO_STATION_GENRES_SLUG );
		if ( count( $genres ) == 1 ) {
			$label = $tax_object->labels->singular_name;
		} else {
			$label = $tax_object->labels->name;
		}
		$label = apply_filters( 'radio_station_show_genres_label', $label, $post_id );
		$meta_blocks['genres'] = '<div class="show-genres">' . "\n";
		$meta_blocks['genres'] .= '<span class="show-genres-label show-label">' . esc_html( $label ) . '</span>: ' . "\n";
		$genre_links = array();
		foreach ( $genres as $genre ) {
			$genre_link = get_term_link( $genre );
			$genre_links[] = '<a href="' . esc_url( $genre_link ) . '">' . esc_html( $genre->name ) . '</a>' . "\n";
		}
		$meta_blocks['genres'] .= implode( ', ', $genre_links ) . "\n";
		$meta_blocks['genres'] .= '</div>' . "\n";
	}

	// --- Show Language(s) ---
	// 2.3.0: only display if language is assigned
	// 2.3.3.4: added filter for languages label
	// 2.3.3.4: replace bold title tag with span and class
	if ( $languages ) {
		$tax_object = get_taxonomy( RADIO_STATION_LANGUAGES_SLUG );
		if ( count( $languages ) == 1 ) {
			$label = $tax_object->labels->singular_name;
		} else {
			$label = $tax_object->labels->name;
		}
		$label = apply_filters( 'radio_station_show_languages_label', $label, $post_id );
		$meta_blocks['languages'] = '<div class="show-languages">' . "\n";
		$meta_blocks['languages'] .= '<span class="show-languages-label show-label">' . esc_html( $label ) . '</span>: ' . "\n";
		$language_links = array();
		foreach ( $languages as $language ) {
			$lang_label = $language->name;
			if ( !empty( $language->description ) ) {
				$lang_label .= ' (' . $language->description . ')';
			}
			$language_link = get_term_link( $language );
			$language_links[] = '<a href="' . esc_url( $language_link ) . '">' . esc_html( $lang_label ) . '</a>' . "\n";
		}
		$meta_blocks['languages'] .= implode( ', ', $language_links ) . "\n";
		$meta_blocks['languages'] .= '</div>' . "\n";
	}

	// --- Show Phone ---
	if ( $show_phone ) {
		$meta_blocks['phone'] = '<div class="show-phone">';
		$label = __( 'Call in', 'radio-station' );
		$label = apply_filters( 'radio_station_show_phone_label', $label, $post_id );
		$meta_blocks['phone'] .= '<span class="show-phone-label show-label">' . esc_html( $label ) . '</span>: ' . "\n";
			$meta_blocks['phone'] .= '<a href="tel:' . esc_attr( $show_phone ) . '">' . esc_html( $show_phone ) . '</a>';
		$meta_blocks['phone'] .= '</div>';
	}

	// --- filter meta blocks and order ---
	// 2.3.3.6: allow subblock order to be changed
	$meta_blocks = apply_filters( 'radio_station_show_meta_blocks', $meta_blocks, $post_id );
	$meta_block_order = array( 'hosts', 'producers', 'genres', 'languages', 'phone' );
	$meta_block_order = apply_filters( 'radio_station_show_meta_block_order', $meta_block_order, $post_id );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Meta Block Order: ' . esc_html( print_r( $meta_block_order, true ) ) . '</span>' . "\n";
		// echo '<span style="display:none;">Meta Blocks: ' . esc_html( print_r( $meta_blocks, true ) ) . '</span>' . "\n";
	}

	// --- combine meta blocks to show meta block ---
	if ( is_array( $meta_blocks ) && ( count( $meta_blocks ) > 0 ) && is_array( $meta_block_order ) && ( count( $meta_block_order ) > 0 ) ) {
		foreach ( $meta_block_order as $meta_block ) {
			if ( isset( $meta_blocks[$meta_block] ) ) {
				$blocks['show_meta'] .= $meta_blocks[$meta_block];
			}
		}
	}
}

// ----------------
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
$label = __( 'Show Times', 'radio-station' );
$label = apply_filters( 'radio_station_show_times_label', $label, $post_id );
$blocks['show_times'] = '<h4>' . esc_html( $label ) . '</h4>' . "\n";

// --- check if show is active and has shifts ---
if ( !$active || !$shifts ) {

	$label = __( 'Not Currently Scheduled.', 'radio-station' );
	$label = apply_filters( 'radio_station_show_no_shifts_label', $label, $post_id );
	$blocks['show_times'] .= esc_html( $label );

} else {

	// --- get timezone and offset ---
	// 2.3.2: use get timezone function
	$timezone = radio_station_get_timezone();
	if ( strstr( $timezone, 'UTC' ) ) {
		$offset = str_replace( 'UTC', '', $timezone );
	} else {
		$timezone_code = radio_station_get_timezone_code( $timezone );
		$datetimezone = new DateTimeZone( $timezone );
		$offset = $datetimezone->getOffset( new DateTime() );
		$offset = round( $offset / 60 / 60 );

		if ( strstr( (string) $offset, '.' ) ) {
			if ( substr( $offset, - 2, 2 ) == '.5' ) {
				$offset = str_replace( '.5', ':30', $offset );
			} elseif ( substr( $offset, - 3, 3 ) == '.75' ) {
				$offset = str_replace( '.75', ':45', $offset );
			} elseif ( substr( $offset, - 3, 3 ) == '.25' ) {
				$offset = str_replace( '.25', ':15', $offset );
			}
		}
	}

	if ( ( 0 == $offset ) || ( '' == $offset ) ) {
		$utc_offset = '';
	} elseif ( $offset > 0 ) {
		$utc_offset = '+' . $offset;
	} else {
		$utc_offset = $offset;
	}

	// --- display timezone ---
	// 2.3.3.4: added filter for timezone label
	// 2.3.3.9: added span wrap with class for actual timezone
	$label = __( 'Timezone', 'radio-station' );
	$label = apply_filters( 'radio_station_show_timezone_label', $label, $post_id );
	$blocks['show_times'] .= '<div class="station-timezone">' . "\n";
	$blocks['show_times'] .= '<span class="show-timezone-label show-label">' . esc_html( $label ) . '</span>: ' . "\n";
	if ( !isset( $timezone_code ) ) {
		$blocks['show_times'] .= '<span class="show-timezone">';
		$blocks['show_times'] .= esc_html( __( 'UTC', 'radio-station' ) ) . $utc_offset;
		$blocks['show_times'] .= '</span>';
	} else {
		$blocks['show_times'] .= '<span class="show-timezone">';
		$blocks['show_times'] .= esc_html( $timezone_code );
		$blocks['show_times'] .= '</span>';
		$blocks['show_times'] .= '<span class="show-offset">';
		$blocks['show_times'] .= ' [' . esc_html( __( 'UTC', 'radio-station' ) ) . $utc_offset . ']';
		$blocks['show_times'] .= '</span>' . "\n";
	}
	$blocks['show_times'] .= '</div>' . "\n";

	// --- user timezone ---
	// TODO: check user timezone setting !!!
	$blocks['show_times'] .= '<div class="div-timezone"></div>' . "\n";

	// --- show shift times ---
	$blocks['show_times'] .= '<table class="show-times" cellpadding="0" cellspacing="0">' . "\n";

	$found_encore = false;
	$weekdays = radio_station_get_schedule_weekdays();
	$now = radio_station_get_now();
	foreach ( $weekdays as $day ) {
		$show_times = array();
		if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
			foreach ( $shifts as $shift ) {
				if ( $day == $shift['day'] ) {

					// --- convert shift info ---
					// 2.3.2: replace strtotime with to_time for timezones
					// 2.3.2: fix to convert to 24 hour format first
					$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
					$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
					$start_time = radio_station_convert_shift_time( $start );
					$end_time = radio_station_convert_shift_time( $end );
					$shift_start_time = radio_station_to_time( $start_time );
					$shift_end_time = radio_station_to_time( $end_time );
					// 2.3.3.9: added or equals to operator
					if ( $shift_end_time <= $shift_start_time ) {
						$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
					}

					// --- get shift time display ---
					$start_display = radio_station_get_time( $start_data_format, $shift_start_time );
					$end_display = radio_station_get_time( $end_data_format, $shift_end_time );
					$start_display = radio_station_translate_time( $start_display );
					$end_display = radio_station_translate_time( $end_display );

					// --- check if current shift ---
					$classes = array( 'show-shift-time' );
					if ( ( $now > $shift_start_time ) && ( $now < $shift_end_time ) ) {
						$classes[] = 'current-shift';
					}
					$class = implode( ' ', $classes );

					// 2.4.0.6: use filtered shift separator
					$separator = ' - ';
					$separator = apply_filters( 'radio_station_show_times_separator', $separator, 'show-content' );

					// --- set show time output ---
					// 2.3.4: fix to start data_format attribute
					$show_time = '<div class="' . esc_attr( $class ) . '">' . "\n";
						$show_time .= '<span class="rs-time rs-start-time" data-format="' . esc_attr( $start_data_format ) . '" data="' . esc_attr( $shift_start_time ) . '">' . esc_html( $start_display ) . '</span>' . "\n";
						$show_time .= '<span class="rs-sep"> - </span>' . "\n";
						$show_time .= '<span class="rs-time rs-end-time" data-format="' . esc_attr( $end_data_format ) . '" data="' . esc_attr( $shift_end_time ) . '">' . esc_html( $end_display ) . '</span>' . "\n";
						if ( isset( $shift['encore'] ) && ( 'on' == $shift['encore'] ) ) {
							$found_encore = true;
							$show_time .= '<span class="show-encore">*</span>' . "\n";
						}
					$show_time .= '</div>' . "\n";

					// 2.3.3.9: add user show time div
					$show_time .= '<div class="show-user-time">' . "\n";
						$show_time .= '[<span class="rs-time rs-start-time"></span>' . "\n";
						$show_time .= '<span class="rs-sep">' . esc_html( $separator ) . '</span>' . "\n";
						$show_time .= '<span class="rs-time rs-end-time"></span>]' . "\n";
					$show_time .= '</div>' . "\n";

					$show_times[] = $show_time;
				}
			}
		}
		$show_times_count = count( $show_times );
		if ( $show_times_count > 0 ) {
			$blocks['show_times'] .= '<td class="show-day-time ' . strtolower( $day ) . '">' . "\n";
			$weekday = radio_station_translate_weekday( $day, true );
			$blocks['show_times'] .= '<b>' . esc_html( $weekday ) . '</b>: ' . "\n";
			$blocks['show_times'] .= '</td><td>' . "\n";
			foreach ( $show_times as $i => $show_time ) {
				$blocks['show_times'] .= '<span class="show-time">' . $show_time . '</span>' . "\n";
				// if ( $i < ( $show_times_count - 1 ) ) {
				//	$blocks['show_times'] .= '<br>';
				// }
			}
			$blocks['show_times'] .= '</td></tr>' . "\n";
		}
	}

	// --- * encore note ---
	// 2.3.3.4: added filter for encore label
	if ( $found_encore ) {
		$label = __( 'Encore Presentation', 'radio-station' );
		$label = apply_filters( 'radio_station_show_encore_label', $label, $post_id );
		$blocks['show_times'] .= '<tr><td></td><td>' . "\n";
			$blocks['show_times'] .= '<span class="show-encore">*</span> ' . "\n";
			$blocks['show_times'] .= '<span class="show-encore-label">' . esc_html( $label ) . '</span>' . "\n";
		$blocks['show_times'] .= '</td></tr>' . "\n";
	}

	$blocks['show_times'] .= '</table>' . "\n";
}

// 2.3.3.9: maybe output linked override times
$date_format = apply_filters( 'radio_station_override_date_format', 'j F' );
$show_past_dates = apply_filters( 'radio_station_override_show_past_dates', false );
$overrides = radio_station_get_linked_override_times( $post_id );
$scheduled = '';
if ( $overrides && is_array( $overrides ) && ( count( $overrides ) > 0 ) ) {
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">LINKED OVERRIDES: ' . esc_html( print_r( $overrides, true ) ) . '</span>' . "\n";
	}
	$now = radio_station_get_now();
	foreach ( $overrides as $override ) {
		// 2.5.8: added isset check for override disabled key
		if ( !isset( $override['disabled'] ) || ( 'yes' != $override['disabled'] ) ) {
			// 2.5.6: added check that override keys are not empty
			if ( !empty( $override['date'] ) && !empty( $override['start_hour'] ) && !empty( $override['start_min'] ) && !empty( $override['start_meridian'] ) && !empty( $override['end_hour'] ) && !empty( $override['end_min'] ) && !empty( $override['end_meridian'] ) ) {

				$start = $override['date'] . ' ' . $override['start_hour'] . ':' . $override['start_min'] . ' ' . $override['start_meridian'];
				$end = $override['date'] . ' ' . $override['end_hour'] . ':' . $override['end_min'] . ' ' . $override['end_meridian'];
				$override_start_time = radio_station_to_time( $start );
				$override_end_time = radio_station_to_time( $end );
				if ( $override_end_time <= $override_start_time ) {
					$override_end_time = $override_end_time + ( 24 * 60 * 60 );
				}

				// --- maybe filter out past scheduled dates ---
				if ( $show_past_dates || ( $override_end_time > $now ) ) {

					$start_display = radio_station_get_time( $start_data_format, $override_start_time );
					$end_display = radio_station_get_time( $end_data_format, $override_end_time );
					$start_display = radio_station_translate_time( $start_display );
					$end_display = radio_station_translate_time( $end_display );
					$date_time = radio_station_to_time( $override['date'] . ' 00:00' );
					$date = radio_station_get_time( $date_format, $date_time );

					// 2.4.0.6: use filtered shift separator
					$separator = ' - ';
					$separator = apply_filters( 'radio_station_show_times_separator', $separator, 'override-content' );

					$scheduled .= '<div class="override-time">' . "\n";
						$scheduled .= '<span class="rs-date rs-start-date" data-format="' . esc_attr( $date_format ) . '" data="' . esc_attr( $date_time ) . '">' . esc_html( $date ) . '</span>' . "\n";
						$scheduled .= '<span class="rs-time rs-start-time" data-format="' . esc_attr( $start_data_format ) . '" data="' . esc_attr( $override_start_time ) . '">' . esc_html( $start_display ) . '</span>' . "\n";
						$scheduled .= '<span class="rs-sep">' . esc_html( $separator ) . '</span>' . "\n";
						$scheduled .= '<span class="rs-time rs-end-time" data-format="' . esc_attr( $end_data_format ) . '" data="' . esc_attr( $override_end_time ) . '">' . esc_html( $end_display ) . '</span>' . "\n";
					$scheduled .= '</div>' . "\n";

					$scheduled .= '<div class="show-user-time">' . "\n";
						$scheduled .= '[<span class="rs-date rs-start-date"></span>' . "\n";
						$scheduled .= '<span class="rs-time rs-start-time"></span>' . "\n";
						$scheduled .= '<span class="rs-sep">' . esc_html( $separator ) . '</span>' . "\n";
						$scheduled .= '<span class="rs-time rs-end-time"></span>]' . "\n";
					$scheduled .= '</div>' . "\n";
				}
			}
		}
	}
}
if ( '' != $scheduled ) {
	$blocks['show_times'] .= '<h5>' . esc_html( __( 'Scheduled Dates', 'radio-station' ) ) . '</h5>' . "\n";
	$blocks['show_times'] .= $scheduled . '<br>' . "\n";
}

// --- maybe add link to full schedule page ---
// 2.3.3.4: added filters for schedule link title and anchor
$schedule_page = radio_station_get_setting( 'schedule_page' );
if ( $schedule_page && !empty( $schedule_page ) ) {
	$schedule_link = get_permalink( $schedule_page );
	$blocks['show_times'] .= '<div class="show-schedule-link">' . "\n";
	$title = __( 'Go to Full Station Schedule Page', 'radio-station' ) . "\n";
	$title = apply_filters( 'radio_station_show_schedule_link_title', $title, $post_id );
	$blocks['show_times'] .= '<a href="' . esc_url( $schedule_link ) . '" title="' . esc_attr( $title ) . '">' . "\n";
	$label = __( 'Full Station Schedule', 'radio-station' );
	$label = apply_filters( 'radio_station_show_schedule_link_anchor', $label, $post_id );
	// 2.5.10: set arrow according to block position
	if ( 'left' == $block_position ) {
		$blocks['show_times'] .= '&larr; ';
	}
	$blocks['show_times'] .= esc_html( $label );
	if ( 'right' == $block_position ) {
		$blocks['show_times'] .= '&rarr; ';
	}
	$blocks['show_times'] .= '</a>' . "\n";
	$blocks['show_times'] .= '</div>' . "\n";
}

// --- filter all show info blocks ---
$blocks = apply_filters( 'radio_station_show_page_blocks', $blocks, $post_id );


// -------------------------
// === Set Show Sections ===
// -------------------------
// 2.3.0: add show information sections

// --------------------
// Set Show Description
// --------------------
$show_description = false;
if ( strlen( trim( $content ) ) > 0 ) {
	$show_description = '<div class="show-desc-content">' . $content . '</div>' . "\n";
	$show_description .= '<div id="show-more-overlay"></div>' . "\n";
	$show_desc_buttons = '<div id="show-desc-buttons">' . "\n";
	$label = __( 'Show More', 'radio-station' );
	$label = apply_filters( 'radio_station_show_more_label', $label, $post_id );
	$show_desc_buttons .= '	<input type="button" id="show-desc-more" onclick="radio_show_desc(\'more\');" value="' . esc_html( $label ) . '">' . "\n";
	$label = __( 'Show Less', 'radio-station' );
	$label = apply_filters( 'radio_station_show_less_label', $label, $post_id );
	$show_desc_buttons .= '	<input type="button" id="show-desc-less" onclick="radio_show_desc(\'less\');" value="' . esc_html( $label ) . '">' . "\n";
	$show_desc_buttons .= '	<input type="hidden" id="show-desc-state" value="">' . "\n";
	$show_desc_buttons .= '</div>' . "\n";
}

// -------------
// Show Sections
// -------------
$sections = array();

// 2.3.3.9: get label from post type object
$show_post_type = get_post_type_object( RADIO_STATION_SHOW_SLUG );
$show_label = $show_post_type->labels->singular_name;

// --- About Show Tab (Post Content) ---
// 2.3.3.4: added filter for show description label and anchor
if ( $show_description ) {

	$sections['about']['heading'] = '<a name="show-description"></a>' . "\n";
	$label = __( 'About the %s', 'radio-station' );
	$label = sprintf( $label, $show_label );
	$label = apply_filters( 'radio_station_show_description_label', $label, $post_id );
	$sections['about']['heading'] .= '<h3 id="show-section-about">' . esc_html( $label ) . '</h3>' . "\n";
	$anchor = __( 'About', 'radio-station' );
	$anchor = apply_filters( 'radio_station_show_description_anchor', $anchor, $post_id );
	$sections['about']['anchor'] = $anchor;

	$sections['about']['content'] = '<div id="show-about" class="show-tab tab-active"><br>' . "\n";
	$sections['about']['content'] .= '<div id="show-description" class="show-description">' . "\n";
	$sections['about']['content'] .= $show_description;
	$sections['about']['content'] .= '</div>' . "\n";
	$sections['about']['content'] .= $show_desc_buttons;
	$sections['about']['content'] .= '</div>' . "\n";
}

// --- Show Episodes Tab ---
// 2.3.3.4: added filter for show episodes label and anchor
// 2.3.3.9: show episodes tab added via sections filter

// --- Show Blog Posts Tab ---
// 2.3.3.4: added filter for show posts label and anchor
if ( $show_posts ) {

	// 2.3.3.9: get label from post type object
	$posts_type = get_post_type_object( 'post' );
	$posts_label = $posts_type->labels->name;
	$sections['posts']['heading'] = '<a name="show-posts"></a>' . "\n";
	$label = $show_label . ' ' . $posts_label;
	$label = apply_filters( 'radio_station_show_posts_label', $label, $post_id );
	$sections['posts']['heading'] .= '<h3 id="show-section-posts">' . esc_html( $label ) . '</h3>' . "\n";
	$anchor = apply_filters( 'radio_station_show_posts_anchor', $posts_label, $post_id );
	$sections['posts']['anchor'] = $anchor;

	$radio_station_data['show-posts'] = $show_posts;
	$sections['posts']['content'] = '<div id="show-posts" class="show-section-content"><br>' . "\n";
	$shortcode = '[show-posts-archive per_page="' . $posts_per_page . '" show="' . $post_id . '"]';
	$shortcode = apply_filters( 'radio_station_show_page_posts_shortcode', $shortcode, $post_id );
	$sections['posts']['content'] .= do_shortcode( $shortcode );
	$sections['posts']['content'] .= '</div>' . "\n";
}

// --- Show Playlists Tab ---
// 2.3.3.4: added filter for show playlists label and anchor
if ( $show_playlists ) {

	// 2.3.3.9: get label from post type object
	$playlists_type = get_post_type_object( RADIO_STATION_PLAYLIST_SLUG );
	$playlist_label = $playlists_type->labels->name;
	$sections['playlists']['heading'] = '<a name="show-playlists">';
	$label = $show_label . ' ' . $playlist_label;
	// 2.3.3.9: fix to filter name (replace dash with underscore)
	$label = apply_filters( 'radio_station_show_playlists_label', $label, $post_id );
	$sections['playlists']['heading'] .= '<h3 id="show-section-playlists">' . esc_html( $label ) . '</h3>' . "\n";
	$anchor = apply_filters( 'radio_station_show_playlists_anchor', $playlist_label, $post_id );
	$sections['playlists']['anchor'] = $anchor;

	$radio_station_data['show-playlists'] = $show_playlists;
	$sections['playlists']['content'] = '<div id="show-playlists" class="show-section-content"><br>' . "\n";
	$shortcode = '[show-playlists-archive per_page="' . $playlists_per_page . '" show="' . $post_id . '"]';
	$shortcode = apply_filters( 'radio_station_show_page_playlists_shortcode', $shortcode, $post_id );
	$sections['playlists']['content'] .= do_shortcode( $shortcode );
	$sections['playlists']['content'] .= '</div>' . "\n";
}

// 2.3.3.8: remove duplicate post_id filter argument
$sections = apply_filters( 'radio_station_show_page_sections', $sections, $post_id );


// -----------------------
// === Template Output ===
// -----------------------

// --- set content class ---
// 2.3.3.9: simplify content class code
$class = 'left-blocks';
if ( in_array( $block_position, array( 'left', 'right', 'top' ) ) ) {
	$class = $block_position . '-blocks';
}

// echo '<!-- #show-content -->' . "\n";
echo '<div id="show-content" class="' . esc_attr( $class ) . '">' . "\n";
echo '<input type="hidden" id="radio-page-type" value="show">' . "\n";

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
		// 2.5.6: wrap output in wp_kses_post
		echo wp_kses_post( $header_image );
	}

	// --- Show Info Blocks ---
	// 2.3.3.4: add show-info element ID to div tag
	echo '<div id="show-info" class="show-info">' . "\n";

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
				echo '<div class="' . esc_attr( $class ) . '">' . "\n";
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				// TODO: test wrapping in wp_kses ?
				echo $blocks[$block] . "\n";
				echo '</div>' . "\n";

				$first = '';
			}
		}
	}

	echo '</div>' . "\n";



	// --- show sections ---
	echo '<div class="show-sections">' . "\n";

	// --- Display Latest Show Posts ---
	if ( $show_latest ) {
		$label = __( 'Latest Show Posts', 'radio-station' );
		$label = apply_filters( 'radio_station_show_latest_posts_label', $label, $post_id );

		echo '<div id="show-latest">' . "\n";
			echo '<div class="show-latest-title">' . "\n";
			echo '<span class="show-latest-label show-label">' . esc_html( $label ) . '</span>' . "\n";
			echo '</div>' . "\n";

			$radio_station_data['show-latests'] = $show_latest;
			$shortcode = '[show-latest-archive show="' . $post_id . '" thumbnails="0" pagination="0" content="none"]';
			$shortcode = apply_filters( 'radio_station_show_page_latest_shortcode', $shortcode, $post_id );
			// phpcs:ignore WordPress.Security.OutputNotEscaped
			echo do_shortcode( $shortcode );
		echo '</div>' . "\n";

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
			// TODO: test wrapping output in wp_kses
			if ( isset( $sections[$section_order[0]] ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $sections[$section_order[0]]['heading'];
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $sections[$section_order[0]]['content'];
			}
			unset( $section_order[0] );

			echo '<div class="show-tabs">' . "\n";

			$i = 0;
			$found_section = false;
			foreach ( $section_order as $section ) {
				if ( isset( $sections[$section] ) ) {
					$found_section = true;
					$class = ( 0 == $i ) ? 'tab-active' : 'tab-inactive';
					echo '<div id="show-' . esc_attr( $section ) . '-tab" class="show-tab ' . esc_attr( $class ) . '" onclick="radio_show_tab(\'show\',\'' . esc_attr( $section ) . '\');">' . "\n";
					echo esc_html( $sections[$section]['anchor'] ) . "\n";
					echo '</div>' . "\n";
					if ( ( $i + 1 ) < count( $sections ) ) {
						echo '<div class="show-tab-spacer">&nbsp;</div>' . "\n";
					}
					$i++;
				}
			}
			// 2.3.3.9: add end tab right spacer
			if ( $found_section ) {
				// 2.4.0.6: add class to last spacer
				echo '<div class="show-tab-spacer last">&nbsp;</div>' . "\n";
			}
			echo '</div>' . "\n";
		}

		echo '<div class="show-section">' . "\n";
		$i = 0;
		foreach ( $section_order as $section ) {
			if ( isset( $sections[$section] ) ) {

				if ( 'tabbed' != $section_layout ) {

					// --- section heading ---
					// phpcs:ignore WordPress.Security.OutputNotEscaped
					echo $sections[$section]['heading'];

					// --- section jump links ---
					if ( 'yes' == $jump_links ) {
						// 2.5.6: make sure we have at least one jump link
						$jump_links = false;
						foreach ( $section_order as $link ) {
							if ( isset( $sections[$link] ) && ( $link != $section ) ) {
								$jump_links = true;
							}
						}
						if ( $jump_links ) {
							echo '<div class="show-jump-links">' . "\n";
							echo '<b>' . esc_html( __( 'Jump to', 'radio-station' ) ) . '</b>: ' . "\n";
							$found_link = false;
							foreach ( $section_order as $link ) {
								if ( isset( $sections[$link] ) && ( $link != $section ) ) {
									if ( $found_link ) {
										echo ' | ';
									}
									echo '<a href="javascript:void(0);" onclick="radio_scroll_link(\'' . esc_attr( $link ) . '\');">';
										echo esc_html( $sections[$link]['anchor'] );
									echo '</a>' . "\n";
									$found_link = true;
								}
							}
							echo '</div>' . "\n";
						}
					}

				} else {

					// --- add tab classes to section ---
					$classes = array( 'show-tab' );
					$tab_class = ( 0 == $i ) ? 'tab-active' : 'tab-inactive';
					$classes[] = $tab_class;
					$class = implode( ' ', $classes );
					$sections[$section]['content'] = str_replace( 'class="show-section-content"', 'class="' . esc_attr( $class ) . '"', $sections[$section]['content'] );

				}

				// --- section content ---
				// TODO: test wrapping output in wp_kses
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $sections[$section]['content'] . "\n";
				$i++;
			}
		}
		echo '</div>' . "\n";

	}
	echo '</div>' . "\n";
echo '</div>' . "\n";
// echo '<!-- /#show-content -->' . "\n";

// --- enqueue show page script ---
// 2.3.0: enqueue script instead of echoing
radio_station_enqueue_script( 'radio-station-page', array( 'radio-station' ), true );

// --- maybe detect and switch to # tab ---
// 2.3.3.9: fix to match variable not string
if ( 'tabbed' == $section_layout ) {
	$js = "setTimeout(function() {";
	$js .= " if (window.location.hash) {";
	$js .= "  hash = window.location.hash.substring(1);";
	$js .= "  if (hash.indexOf('show-') > -1) {";
	$js .= "   tab = hash.replace('show-', '');";
	$js .= "   radio_show_tab('show',tab);";
	$js .= "  }";
	$js .= " }";
	$js .= "}, 500);";
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'radio-station-page', $js );
}

// 2.3.3.9: turn off doing template flag
$radio_station_data['doing-template'] = false;
