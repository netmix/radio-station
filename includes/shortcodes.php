<?php

/* Shortcode for displaying the current song
 * Since 2.0.0
 */

// note: Master Schedule Shortcode in /includes/master-schedule.php

// === Time Shortcodes ===
// - Radio Timezone Shortcode
// === Archive Shortcodes ===
// - Archive List Shortcode Abstract
// - Show Archive Shortcode
// - Playlist Archive Shortcode
// - Override Archive Shortcode
// - Genre Archive Shortcode
// * Language Archive Shortcode
// === Show Related Shortcodes ===
// - Show List Shortcode Abstract
// - Show Posts List Shortcode
// - Show Playlists List Shortcode
// - Show Lists Pagination Javascript
// === Widget Shortcodes ===
// - Current Show Shortcode
// - Upcoming Shows Shortcode
// - Now Playing Shortcode
// - Countdown Script
// === Legacy Shortcodes ===
// - Show List Shortcode
// - Show Playlist Shortcode


// -----------------------
// === Time Shortcodes ===
// -----------------------

// ------------------------
// Radio Timezone Shortcode
// ------------------------
add_shortcode( 'radio-timezone', 'radio_station_timezone_shortcode' );
function radio_station_timezone_shortcode( $atts = array() ) {

	// --- get radio timezone values ---
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
	if ( isset( $offset ) ) {
		if ( !$offset || ( 0 == $offset ) ) {
			$offset = '';
		} elseif ( $offset > 0 ) {
			$offset = '+' . $offset;
		}
		$timezone_display = __( 'UTC', 'radio-station' ) . ' ' . $offset;
	} else {
		// --- get offset and code from timezone location ---
		$datetimezone = new DateTimeZone( $timezone );
		$offset = $datetimezone->getOffset( new DateTime() );
		if ( 0 == $offset ) {
			$utc_offset = '[' . __( 'UTC', 'radio-station' ) . ']';
		} else {
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
			if ( $offset > 0 ) {
				$utc_offset = '[' . __( 'UTC', 'radio-station' ) . ' +' . $offset . ']';
			} else {
				$utc_offset = '[' . __( 'UTC', 'radio-station' ) . ' ' . $offset . ']';
			}
		}
		$code = radio_station_get_timezone_code( $timezone );
		$timezone_display = $code . ' ' . $utc_offset;
	}

	// --- set shortcode output ---
	$output = '<div class="radio-timezone-wrapper">';
	$output .= '<span class="radio-timezone-title">';
	$output .= esc_html( __( 'Radio Timezone', 'radio-station' ) );
	$output .= '</span>: ';
	$output .= '<span class="radio-timezone">' . esc_html( $timezone_display ) . '</span>';
	$output .= '</div>';

	// --- filter and return ---
	$output = apply_filters( 'radio_station_timezone_shortcode', $output, $atts );
	return $output;
}


// --------------------------
// === Archive Shortcodes ===
// --------------------------

// -------------------------------
// Archive List Shortcode Abstract
// -------------------------------
function radio_station_archive_list_shortcode( $type, $atts ) {

	// TODO: add pagination to Archive list shortcode

	// --- merge defaults with passed attributes ---
	$defaults = array(
		'genre'        => '',
		'language'     => '',
		'thumbnails'   => 1,
		'hide_empty'   => 0,
		'content'      => 'excerpt',
		// query args
		'orderby'      => 'title',
		'order'        => 'ASC',
		'status'       => 'publish',
		'perpage'      => - 1,
		'offset'       => 0,
		// shows and overrides
		'show_avatars' => 1,
		// shows only
		'with_shifts'  => 1,
		// TODO: genre archive result pagination
		// 'pagination'	=> 1,
	);

	// --- handle possible pagination offset ---
	if ( isset( $atts['perpage'] ) && !isset( $atts['offset'] ) && get_query_var( 'page' ) ) {
		$page = absint( get_query_var( 'page' ) );
		if ( $page > - 1 ) {
			$atts['offset'] = (int) $atts['perpage'] * $page;
		}
	}
	$atts = shortcode_atts( $defaults, $atts, $type . '-archive' );

	// --- get published shows ---
	$args = array(
		'post_type'   => $type,
		'numberposts' => $atts['perpage'],
		'offset'      => $atts['offset'],
		'orderby'     => $atts['orderby'],
		'order'       => $atts['order'],
		'post_status' => $atts['status'],
	);

	// --- extra queries for shows ---
	if ( RADIO_STATION_SHOW_SLUG == $type ) {

		if ( $atts['with_shifts'] ) {

			// --- active shows with shifts ---
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'meta_key	' => 'show_sched',
					'compare'      => 'EXISTS',
				),
				array(
					'meta_key'   => 'show_active',
					'mata_value' => 'on',
					'compare'    => '=',
				),
			);
		} else {

			// --- just active shows ---
			$args['meta_query'] = array(
				array(
					'meta_key'   => 'show_active',
					'meta_value' => 'on',
					'compare'    => '=',
				),
			);
		}
	}

	// --- specific genres taxonomy query ---
	if ( !empty( $atts['genre'] ) && in_array( $type, array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG ) ) ) {

		// --- check for provided genre(s) as slug or ID ---
		if ( strstr( $atts['genre'], ',' ) ) {
			$atts['genre'] = explode( ',', $atts['genre'] );
		}
		$args['tax_query'] = array(
			'relation' => 'OR',
			array(
				'taxonomy' => RADIO_STATION_GENRES_SLUG,
				'field'    => 'slug',
				'terms'    => $atts['genre'],
			),
			array(
				'taxonomy' => RADIO_STATION_GENRES_SLUG,
				'field'    => 'ID',
				'terms'    => $atts['genre'],
			),
		);
	}

	// --- specific languagees taxonomy query ---
	// 2.3.0: added language taxonomy support
	if ( !empty( $atts['language'] ) && in_array( $type, array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG ) ) ) {

		// --- check for provided genre(s) as slug or ID ---
		if ( strstr( $atts['language'], ',' ) ) {
			$atts['language'] = explode( ',', $atts['language'] );
		}

		if ( !isset( $args['tax_query'] ) ) {
			$args['tax_query'] = array( 'relation' => 'OR' );
		}
		$args['tax_query'][] = array(
			'taxonomy' => RADIO_STATION_LANGUAGES_SLUG,
			'field'    => 'slug',
			'terms'    => $atts['language'],
		);
		$args['tax_query'][] = array(
			'taxonomy' => RADIO_STATION_LANGUAGES_SLUG,
			'field'    => 'ID',
			'terms'    => $atts['language'],
		);
	}

	// --- get posts via query ---
	$args = apply_filters( 'radio_station_' . $type . '_archive_post_args', $args );
	$archive_posts = get_posts( $args );
	$archive_posts = apply_filters( 'radio_station_' . $type . '_archive_posts', $archive_posts );

	// --- check for results ---
	$list = '<div class="' . esc_attr( $type ) . '-archives">';
	if ( !$archive_posts || ( count( $archive_posts ) == 0 ) ) {

		if ( $atts['hide_empty'] ) {
			return '';
		}

		// --- no shows messages ----
		if ( RADIO_STATION_SHOW_SLUG == $type ) {
			if ( !empty( $atts['genre'] ) ) {
				$list .= esc_html( __( 'No Shows in this Genre were found.', 'radio-station' ) );
			} else {
				$list .= esc_html( __( 'No Shows were found to display.', 'radio-station' ) );
			}
		} elseif ( RADIO_STATION_PLAYLIST_SLUG == $type ) {
			$list .= esc_html( __( 'No Playlists were found to display.', 'radio-station' ) );
		} elseif ( RADIO_STATION_OVERRIDE_SLUG == $type ) {
			$list .= esc_html( __( 'No Overrides were found to display.', 'radio-station' ) );
		}

	} else {

		// --- archive list ---
		$list .= '<ul class="' . esc_attr( $type ) . '-archive-list">';

		foreach ( $archive_posts as $archive_post ) {

			$list .= '<li class="' . esc_attr( $type ) . '-archive-item">';

			// --- show avatar or thumbnail ---
			$list .= '<div class="' . esc_attr( $type ) . '-archive-item-thumbnail">';
			if ( $atts['show_avatars'] && in_array( $type, array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG ) ) ) {

				// --- show avatar for shows ---
				$attr = array( 'class' => esc_attr( $type ) . '-thumbnail-image' );
				$show_avatar = radio_station_get_show_avatar( $archive_post->ID, 'thumbnail', $attr );
				if ( $show_avatar ) {
					$list .= $show_avatar;
				}
			} elseif ( $atts['thumbnails'] ) {
				// --- post thumbnail ---
				if ( has_post_thumbnail( $archive_post->ID ) ) {
					$atts = array( 'class' => esc_attr( $type ) . '-thumbnail-image' );
					$thumbnail = get_the_post_thumbnail( $archive_post->ID, 'thumbnail', $atts );
					$list .= $thumbnail;
				}
			}
			$list .= '</div>';

			// --- title ----
			$list .= '<div class="' . esc_attr( $type ) . '-archive-item-title">';
			$list .= '<a href="' . esc_url( get_permalink( $archive_post->ID ) ) . '">';
			$list .= esc_html( $archive_post->post_title ) . '</a>';
			$list .= '</div>';

			// --- meta data ---
			// if ( RADIO_STATION_SHOW_SLUG == $type ) {
			// TODO: show shifts, genres, languages
			// $shifts = get_post_meta( $archive_post->ID, 'show_sched', true );
			// $genres = wp_get_post_terms( $archive_post->ID, RADIO_STATION_GENRES_SLUG );
			// $languages = wp_get_post_terms( $archive_post->ID, RADIO_STATION_LANGUAGES_SLUG );
			// }
			// if ( RADIO_STATION_PLAYLIST_SLUG == $type ) {
			// TODO: playlist track / count
			// $tracks = get_post_meta( $archive_post->ID, 'playlist', true );
			// }
			// if ( RADIO_STATION_OVERRIDE_SLUG == $type ) {
			// TODO: override date
			// $date = get_post_meta( $archive_post->ID, 'show_override_sched', true );
			// }

			// --- content ---
			if ( 'none' == $atts['content'] ) {
				$list .= '';
			} elseif ( 'full' == $atts['content'] ) {
				$list .= '<div class="' . esc_attr( $type ) . '-archive-item-content">';
				$content = apply_filters( 'radio_station_' . $type . '_archive_content', $archive_post->post_content, $archive_post->ID );
				$list .= $content;
				$list .= '</div>';
			} else {
				$list .= '<div class="' . esc_attr( $type ) . '-archive-item-excerpt">';
				if ( !empty( $archive_post->post_excerpt ) ) {
					$excerpt = $archive_post->post_excerpt;
				} else {
					$excerpt = radio_station_trim_excerpt( $archive_post->post_content );
				}
				$excerpt = apply_filters( 'radio_station_' . $type . '_archive_excerpt', $excerpt, $archive_post->ID );
				$list .= $excerpt;
				$list .= '</div>';
			}

			$list .= '</li>';
		}
		$list .= '</ul>';
	}
	$list .= '</div>';

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return  ---
	$list = apply_filters( 'radio_station_' . $type . '_archive_list', $list, $atts );

	return $list;
}

// ----------------------
// Show Archive Shortcode
// ----------------------
add_shortcode( 'show-archive', 'radio_station_show_archive_list' );
add_shortcode( 'shows-archive', 'radio_station_show_archive_list' );
function radio_station_show_archive_list( $atts ) {
	return radio_station_archive_list_shortcode( RADIO_STATION_SHOW_SLUG, $atts );
}

// --------------------------
// Playlist Archive Shortcode
// --------------------------
add_shortcode( 'playlist-archive', 'radio_station_playlist_archive_list' );
add_shortcode( 'playlists-archive', 'radio_station_playlist_archive_list' );
function radio_station_playlist_archive_list( $atts ) {
	return radio_station_archive_list_shortcode( RADIO_STATION_PLAYLIST_SLUG, $atts );
}

// --------------------------
// Override Archive Shortcode
// --------------------------
add_shortcode( 'override-archive', 'radio_station_override_archive_list' );
add_shortcode( 'overrides-archive', 'radio_station_override_archive_list' );
function radio_station_override_archive_list( $atts ) {
	return radio_station_archive_list_shortcode( RADIO_STATION_OVERRIDE_SLUG, $atts );
}

// -----------------------
// Genre Archive Shortcode
// -----------------------
add_shortcode( 'genre-archive', 'radio_station_genre_archive_list' );
add_shortcode( 'genres-archive', 'radio_station_genre_archive_list' );
function radio_station_genre_archive_list( $atts ) {

	$defaults = array(
		// genre display options
		'genres'       => '',
		'link_genres'  => 1,
		'description'  => 1,
		'genre_images' => 1,
		'hide_empty'   => 1,
		// show query args
		'perpage'      => - 1,
		'offset'       => 0,
		'orderby'      => 'title',
		'order'        => 'ASC',
		'status'       => 'publish',
		// show display options
		'with_shifts'  => 1,
		'show_avatars' => 0,
		'show_desc'    => 0,
		'thumbnails'   => 0,
		// TODO: genre archive result pagination
		// 'pagination'	=> 1,
	);

	// --- handle possible pagination offset ---
	if ( isset( $atts['perpage'] ) && !isset( $atts['offset'] ) && get_query_var( 'page' ) ) {
		$page = absint( get_query_var( 'page' ) );
		if ( $page > - 1 ) {
			$atts['offset'] = (int) $atts['perpage'] * $page;
		}
	}
	$atts = shortcode_atts( $defaults, $atts, 'genre-archive' );

	// --- maybe get specified genre(s) ---
	if ( !empty( $atts['genres'] ) ) {
		$genres = explode( ',', $atts['genres'] );
		foreach ( $genres as $i => $genre ) {
			$genre = trim( $genre );
			$genre = radio_station_get_genre( $genre );
			if ( $genre ) {
				$genres[$i] = $genre;
			} else {
				unset( $genres[$i] );
			}
		}
	} else {
		// --- get all genres ---
		$args = array();
		if ( !$atts['hide_empty'] ) {
			$args['hide_empty'] = false;
		}
		$genres = radio_station_get_genres( $args );
	}

	// --- check if we have genres ---
	if ( !$genres || ( count( $genres ) == 0 ) ) {
		if ( $atts['hide_empty'] ) {
			return '';
		} else {
			$list = '<div class="genres-archive">';
			$list .= esc_html( __( 'No Genres were found to display.', 'radio-station' ) );
			$list .= '</div>';

			return $list;
		}
	}

	$list = '<div class="genres-archive">';

	// --- loop genres ---
	foreach ( $genres as $name => $genre ) {

		// --- get published shows ---
		$args = array(
			'post_type'   => RADIO_STATION_SHOW_SLUG,
			'numberposts' => $atts['perpage'],
			'offset'      => $atts['offset'],
			'orderby'     => $atts['orderby'],
			'order'       => $atts['order'],
			'post_status' => $atts['status'],
		);

		if ( $atts['with_shifts'] ) {

			// --- active shows with shifts ---
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'meta_key	' => 'show_sched',
					'compare'      => 'EXISTS',
				),
				array(
					'meta_key'   => 'show_active',
					'mata_value' => 'on',
					'compare'    => '=',
				),
			);

		} else {

			// --- just active shows ---
			$args['meta_query'] = array(
				array(
					'meta_key'   => 'show_active',
					'meta_value' => 'on',
					'compare'    => '=',
				),
			);
		}

		// --- set genre taxonomy query ---
		$args['tax_query'] = array(
			array(
				'taxonomy' => RADIO_STATION_GENRES_SLUG,
				'field'    => 'slug',
				'terms'    => $genre['slug'],
			),
		);

		// --- get shows in genre ---
		$args = apply_filters( 'radio_station_genre_archive_post_args', $args );
		$posts = get_posts( $args );
		$posts = apply_filters( 'radio_station_genre_archive_posts', $posts );

		$list .= '<div class="genre-archive">';

		if ( $posts || ( count( $posts ) > 0 ) ) {
			$has_posts = true;
		} else {
			$has_posts = false;
		}
		if ( $has_posts || ( !$has_posts && !$atts['hide_empty'] ) ) {

			// --- Genre image ---
			$list .= '<div class="genre-image-wrapper">';
			$genre_image = apply_filters( 'radio_station_genre_image', false, $genre );
			if ( $genre_image ) {
				$list .= $genre_image;
			}
			$list .= '</div>';

			// --- genre title ---
			$list .= '<div class="genre-title"><h3 class="genre-title">';
			if ( $atts['link_genres'] ) {
				$list .= '<a href="' . esc_url( $genre['url'] ) . '">' . $genre['name'] . '</a>';
			} else {
				$list .= $genre['name'];
			}
			$list .= '</h3></div>';

			// --- genre description ---
			if ( $atts['description'] && !empty( $genre['description'] ) ) {
				$list .= '<div class="genre-description">';
				$list .= $genre['description'];
				$list .= '</div>';
			}

		}

		if ( !$has_posts ) {

			// --- no shows messages ----
			if ( !$atts['hide_empty'] ) {
				$list .= esc_html( __( 'No Shows in this Genre.', 'radio-station' ) );
			}

		} else {

			// --- show archive list ---
			$list .= '<ul class="show-archive-list">';

			foreach ( $posts as $post ) {
				$list .= '<li class="show-archive-item">';

				// --- avatar or thumbnail ---
				$list .= '<div class="show-archive-item-thumbnail">';
				if ( $atts['show_avatars'] ) {
					// --- get show avatar ---
					$attr = array( 'class' => 'show-thumbnail-image' );
					$show_avatar = radio_station_get_show_avatar( $post->ID, 'thumbnail', $attr );
					if ( $show_avatar ) {
						$list .= $show_avatar;
					}
				} elseif ( $atts['thumbnails'] ) {
					if ( has_post_thumbnail( $post->ID ) ) {
						$attr = array( 'class' => 'show-thumbnail-image' );
						$thumbnail = get_the_post_thumbnail( $post->ID, 'thumbnail', $attr );
						$list .= $thumbnail;
					}
				}
				$list .= '</div>';

				// --- show title ----
				$list .= '<div class="show-archive-item-title">';
				$list .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">';
				$list .= esc_attr( get_the_title( $post->ID ) ) . '</a>';
				$list .= '</div>';

				// --- show excerpt ---
				// n/a

				$list .= '</li>';
			}
			$list .= '</ul>';
		}

		$list .= '</div>';
	}

	$list .= '</div>';

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return ---
	$list = apply_filters( 'radio_station_genre_archive_list', $list, $atts );

	return $list;
}

// --------------------------
// Language Archive Shortcode
// --------------------------
// TODO: Languages Archive Shortcode
// add_shortcode( 'language-archive', 'radio_station_language_archive_list' );
// add_shortcode( 'languages-archive', 'radio_station_language_archive_list' );
function radio_station_language_archive_list( $atts ) {

	$list = '';

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return ---
	$list = apply_filters( 'radio_station_language_archive_list', $list, $atts );

	return $list;
}

// -------------------------------
// === Show Related Shortcodes ===
// -------------------------------

// ----------------------------
// Show List Shortcode Abstract
// ----------------------------
function radio_station_show_list_shortcode( $type, $atts ) {

	global $radio_station_data;

	// --- get time and date formats ---
	$timeformat = get_option( 'time_format' );
	$dateformat = get_option( 'date_format' );

	// --- get shortcode attributes ---
	$defaults = array(
		'per_page'   => 15,
		'limit'      => - 1,
		'content'    => 'excerpt',
		'thumbnails' => 1,
		'pagination' => 1,
	);
	$atts = shortcode_atts( $defaults, $atts, 'show-' . $type . '-list' );

	// --- maybe get stored post data ---
	if ( isset( $radio_station_data['show-' . $type . 's'] ) ) {

		// --- use data stored from template ---
		$posts = $radio_station_data['show-' . $type . 's'];
		unset( $radio_station_data['show-' . $type . 's'] );
		$show_id = $radio_station_data['show-id'];

	} else {
		// --- check for show ID (required at minimum) ---
		if ( !isset( $atts['show'] ) ) {
			return '';
		}
		$show_id = $atts['show'];

		// --- attempt to get show ID via slug ---
		if ( intval( $show_id ) != $show_id ) {
			global $wpdb;
			$query = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_name = %s";
			$query = $wpdb->prepare( $query, $show_id );
			$show_id = $wpdb->get_var( $query );
			if ( !$show_id ) {
				return '';
			}
		}

		// --- get related to show posts ---
		$args = array();
		if ( isset( $atts['limit'] ) ) {
			$args['limit'] = $atts['limit'];
		}
		if ( 'post' == $type ) {
			$posts = radio_station_get_show_posts( $show_id, $args );
		} elseif ( RADIO_STATION_PLAYLIST_SLUG == $type ) {
			$posts = radio_station_get_show_playlists( $show_id, $args );
		} elseif ( defined( 'RADIO_STATION_EPISODE_SLUG' ) && ( RADIO_STATION_EPISODE_SLUG == $type ) ) {
			$posts = apply_filters( 'radio_station_get_show_episodes', false, $show_id, $args );
		}
	}
	if ( !isset( $posts ) || !$posts || !is_array( $posts ) || ( count( $posts ) == 0 ) )  {return '';}

	// --- show list div ---
	$list = '<div id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-list" class="show-' . esc_attr( $type ) . 's-list">';

	// --- loop show posts ---
	$post_pages = 1;
	$j = 0;
	foreach ( $posts as $post ) {
		$newpage = $firstpage = false;
		if ( 0 == $j ) {
			$newpage = $firstpage = true;
		} elseif ( $j == $atts['per_page'] ) {
			// --- close page div ---
			$list .= '</div>';
			$newpage = true;
			$post_pages ++;
			$j = 0;
		}
		if ( $newpage ) {
			// --- new page div ---
			if ( !$firstpage ) {
				$hide = ' style="display:none;"';
			} else {
				$hide = '';
			}
			$list .= '<div id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page-' . esc_attr( $post_pages ) . '" class="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page"' . $hide . '>';
		}

		// --- new item div ---
		$classes = array( 'show-' . $type );
		if ( $newpage ) {$classes[] = 'first-item';}
		$class = implode( ' ', $classes );
		$list .= '<div class="' . esc_attr( $class ) . '">';

		// --- post thumbnail ---
		if ( $atts['thumbnails'] ) {
			$has_thumbnail = has_post_thumbnail( $post['ID'] );
			if ( $has_thumbnail ) {
				$attr = array( 'class' => 'show-' . esc_attr( $type ) . '-thumbnail-image' );
				$thumbnail = get_the_post_thumbnail( $post['ID'], 'thumbnail', $attr );
				if ( $thumbnail ) {
					$list .= '<div class="show-' . esc_attr( $type ) . '-thumbnail">' . $thumbnail . '</div>';
				}
			}
		}

		$list .= '<div class="show-' . esc_attr( $type ) . '-info">';

		// --- link to post ---
		$list .= '<div class="show-' . esc_attr( $type ) . '-title">';
		$permalink = get_permalink( $post['ID'] );
		$timestamp = mysql2date( $dateformat . ' ' . $timeformat, $post['post_date'], false );
		$title = __( 'Published on ', 'radio-station' ) . $timestamp;
		$list .= '<a href="' . esc_url( $permalink ) . '" title="' . esc_attr( $title ) . '">';
		$list .= esc_attr( $post['post_title'] );
		$list .= '</a>';
		$list .= '</div>';

		// --- post excerpt ---
		if ( 'none' == $atts['content'] ) {
			$list .= '';
		} elseif ( 'full' == $atts['content'] ) {
			$list .= '<div class="show-' . esc_attr( $type ) . '-content">';
			$content = apply_filters( 'radio_station_show_' . $type . '_content', $post['post_content'], $post['ID'] );
			// $list .= $content;
			$list .= '</div>';
		} else {
			$list .= '<div class="show-' . esc_attr( $type ) . '-excerpt">';
			if ( !empty( $post['post_excerpt'] ) ) {
				$excerpt = $post['post_excerpt'];
			} else {
				$excerpt = radio_station_trim_excerpt( $post['post_content'] );
			}
			$excerpt = apply_filters( 'radio_station_show_' . $type . '_excerpt', $excerpt, $post['ID'] );
			$list .= $excerpt;
			$list .= '</div>';
		}

		$list .= '</div>';

		// --- close item div ---
		$list .= '</div>';
		$j ++;
	}

	// --- close last page div ---
	$list .= '</div>';

	// --- list pagination ---
	if ( $atts['pagination'] && ( $post_pages > 1 ) ) {
		$list .= '<br><br>';
		$list .= '<div id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page-buttons" class="show-' . esc_attr( $type ) . 's-page-buttons">';
		$list .= '<div class="show-pagination-button" onclick="radio_show_page(' . esc_js( $show_id ) . ', \'' . esc_js( $type ) . 's\', \'prev\');">';
		$list .= '<a href="javascript:void(0);">&larr;</a>';
		$list .= '</div>';
		for ( $pagenum = 1; $pagenum < ( $post_pages + 1 ); $pagenum ++ ) {
			if ( 1 == $pagenum ) {
				$active = ' active';
			} else {
				$active = '';
			}
			$onclick = 'radio_show_page(' . esc_js( $show_id ) . ', \'' . esc_js( $type ) . 's\', ' . esc_js( $pagenum ) . ');';
			$list .= '<div id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ). 's-page-button-' . esc_attr( $pagenum ) . '" class="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page-button show-pagination-button' . esc_attr( $active ) . '" onclick="' . $onclick . '">';
			$list .= '<a href="javascript:void(0);">';
			$list .= esc_html( $pagenum );
			$list .= '</a>';
			$list .= '</div>';
		}
		$list .= '<div class="show-pagination-button" onclick="radio_show_page(' . esc_js( $show_id ) . ', \'' . esc_js( $type ). 's\', \'next\');">';
		$list .= '<a href="javascript:void(0);">&rarr;</a>';
		$list .= '</div>';
		$list .= '<input type="hidden" id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-current-page" value="1">';
		$list .= '<input type="hidden" id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page-count" value="' . esc_attr( $post_pages ) . '">';
		$list .= '</div>';
	}

	// --- close list div ---
	$list .= '</div>';

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- enqueue pagination javascript ---
	add_action( 'wp_footer', 'radio_station_pagination_javascript' );

	// --- filter and return ---
	$list = apply_filters( 'radio_station_show_' . $type . '_list', $list, $atts );

	return $list;
}

// -------------------------
// Show Posts Archive Shortcode
// -------------------------
// requires: show shortcode attribute, eg. [show-posts-list show="1"]
add_shortcode( 'show-posts-archive', 'radio_station_show_posts_archive' );
add_shortcode( 'show-post-archive', 'radio_station_show_posts_archive' );
function radio_station_show_posts_archive( $atts ) {
	$output = radio_station_show_list_shortcode( 'post', $atts );
	return $output;
}

// -----------------------------------
// Show Latest Posts Archive Shortcode
// -----------------------------------
add_shortcode( 'show-latests-archive', 'radio_station_show_latest_archive' );
add_shortcode( 'show-latest-archive', 'radio_station_show_latest_archive' );
function radio_station_show_latest_archive( $atts ) {
	$output = radio_station_show_list_shortcode( 'latest', $atts );
	return $output;
}

// --------------------------------
// Show Playlists Archive Shortcode
// --------------------------------
// requires: show shortcode attribute, eg. [show-playlists-list show="1"]
add_shortcode( 'show-playlists-archive', 'radio_station_show_playlists_archive' );
add_shortcode( 'show-playlist-archive', 'radio_station_show_playlists_archive' );
function radio_station_show_playlists_archive( $atts ) {
	$output = radio_station_show_list_shortcode( RADIO_STATION_PLAYLIST_SLUG, $atts );
	return $output;
}

// --------------------------------
// Show Lists Pagination Javascript
// --------------------------------
function radio_station_pagination_javascript() {

	// --- fade out current page and fade in selected page ---
	$js = "function radio_show_page(id, types, pagenum) {
		currentpage = document.getElementById('show-'+id+'-'+types+'-current-page').value;
		if (pagenum == 'next') {
			pagenum = parseInt(currentpage) + 1;
			pagecount = document.getElementById('show-'+id+'-'+types+'-page-count').value;
			if (pagenum > pagecount) {return;}
		}
		if (pagenum == 'prev') {
			pagenum = parseInt(currentpage) - 1;
			if (pagenum < 1) {return;}
		}
		if (typeof jQuery == 'function') {
			jQuery('.show-'+id+'-'+types+'-page').fadeOut(500);
			jQuery('#show-'+id+'-'+types+'-page-'+pagenum).fadeIn(1000);
			jQuery('.show-'+id+'-'+types+'-page-button').removeClass('active');
			jQuery('#show-'+id+'-'+types+'-page-button-'+pagenum).addClass('active');
			jQuery('#show-'+id+'-'+types+'-current-page').val(pagenum);
		} else {
			pages = document.getElementsByClassName('show-'+id+'-'+types+'-page');
			for (i = 0; i < pages.length; i++) {pages[i].style.display = 'none';}
			document.getElementById('show-'+id+'-'+types+'-page-'+pagenum).style.display = '';
			buttons = document.getElementsByClassName('show-'+id+'-'+types+'-page-button');
			for (i = 0; i < buttons.length; i++) {buttons[i].classList.remove('active');}
			document.getElementById('show-'+id+'-'+types+'-page-button-'+pagenum).classList.add('active');
			document.getElementById('show-'+id+'-'+types+'-current-page').value = pagenum;
		}
	}";

	// --- enqueue script inline ---
	// 2.3.0: enqueue instead of echoing
	wp_add_inline_script( 'radio-station', $js );
}


// -------------------------
// === Widget Shortcodes ===
// -------------------------

// ----------------------
// Current Show Shortcode
// ----------------------
// [current-show] / [dj-widget]
// 2.0.9: shortcode function for current DJ on-air
// 2.3.0: added missing output sanitization
// 2.3.0: added current-show shortcode alias
add_shortcode( 'dj-widget', 'radio_station_current_show_shortcode' );
add_shortcode( 'current-show', 'radio_station_current_show_shortcode' );
function radio_station_current_show_shortcode( $atts ) {

	global $radio_station_data;

	$output = '';

	// --- get shortcode attributes ---
	// 2.3.0: set default default_name text
	// 2.3.0: set default time format to plugin setting
	$time_format = radio_station_get_setting( 'clock_time_format' );
	$defaults = array(
		// --- legacy options ---
		'title'          => '',
		'display_hosts'  => 0,
		'show_avatar'    => 0,
		'show_link'      => 0,
		'default_name'   => '',
		'time'           => $time_format,
		'show_sched'     => 1,
		'show_playlist'  => 1,
		'show_all_sched' => 0,
		'show_desc'      => 0,
		// --- new options ---
		// 'display_producers' => 0,
		'avatar_width'   => '',
		'title_position' => 'right',
		'link_djs'       => 0,
		'countdown'      => 0,
		'dynamic'        => 0,
		'widget'         => 0,
		'id'             => '',
	);
	// 2.3.0: convert old attribute display_djs to display_hosts
	if ( isset( $atts['display_djs'] ) && !isset( $atts['display_hosts'] ) ) {
		$atts['display_hosts'] = $atts['display_djs'];
		unset( $atts['display_djs'] );
	}
	// 2.3.0: renamed shortcode identifier to current-show
	$atts = shortcode_atts( $defaults, $atts, 'current-show' );

	// 2.3.0: maybe set float class and avatar width style
	$widthstyle = $floatclass = '';
	if ( !empty( $atts['avatar_width'] ) ) {
		$widthstyle = 'style="width:' . esc_attr( $atts['avatar_width'] ) . 'px;"';
	}
	if ( 'right' == $atts['title_position'] ) {
		$floatclass = ' float-left';
	} elseif ( 'left' == $atts['title_position'] ) {
		$floatclass = ' float-right';
	}

	// --- get meridiem conversions ---
	// 2.3.0: added once-off pre-conversions
	if ( 12 == (int) $atts['time'] ) {
		$am = radio_station_translate_meridiem( 'am' );
		$pm = radio_station_translate_meridiem( 'pm' );
	}

	// --- maybe filter excerpt values ---
	// 2.3.0: added context specific excerpt value filtering
	if ( $atts['show_desc'] ) {
		if ( $atts['widget'] ) {
			$length = apply_filters( 'radio_station_current_show_widget_excerpt_length', false );
			$more = apply_filters( 'radio_station_current_show_widget_excerpt_more', false );
		} else {
			$length = apply_filters( 'radio_station_current_show_shortcode_excerpt_length', false );
			$more = apply_filters( 'radio_station_current_show_shortcode_excerpt_more', false );
		}
	}

	// --- get current show ---
	// 2.3.0: use new get current show function
	$shift = radio_station_get_current_show();

	// --- open shortcode div wrapper ---
	if ( !$atts['widget'] ) {

		// 2.3.0: add unique id to widget shortcode
		if ( !isset( $radio_station_data['widgets']['current-show'] ) ) {
			$radio_station_data['widgets']['current-show'] = 0;
		} else {
			$radio_station_data['widgets']['current-show']++;
		}
		$id = 'current-show-widget-' . $radio_station_data['widgets']['current-show'];
		$output .= '<div id="' . esc_attr( $id ) . '" class="current-show-embedded on-air-embedded dj-on-air-embedded">';

		// --- shortcode only title ---
		if ( !empty( $atts['title'] ) ) {
			$output .= '<h3 class="current-show-title dj-on-air-title">';
			$output .= esc_html( $atts['title'] );
			$output .= '</h3>';
		}
	}


	// --- open current show list ---
	$output .= '<ul class="current-show-list on-air-list">';

	if ( !$shift ) {

		// --- default output if no current shift ---
		$output .= '<li class="current-show on-air-dj default-dj">';
		if ( !empty( $atts['default_name'] ) ) {
			$output .= esc_html( $atts['default_name'] );
		} else {
			$output .= esc_html( __( 'No Show currently scheduled.', 'radio-station') );
		}
		$output .= '</li>';

	} else {

		// --- set current show data ---
		$show = $shift['show'];

		$output .= '<li class="current-show on-air-dj">';

		$show_link = false;
		if ( $atts['show_link'] ) {
			$show_link = get_permalink( $show['id'] );
			$show_link = apply_filters( 'radio_station_current_show_link', $show_link, $show['id'], $atts );
		}

		// --- set show title output ---
		$title = '<div class="current-show-title on-air-dj-title">';
		if ( $show_link ) {
			$title .= '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
		} else {
			$title .= esc_html( $show['name'] );
		}
		$title .= '</div>';

		// --- show title (above only) ---
		if ( 'above' == $atts['title_position'] ) {
			$output .= $title; // already escaped
		}

		// --- show avatar ---
		if ( $atts['show_avatar'] ) {

			// 2.3.0: get show avatar (with thumbnail fallback)
			// 2.3.0: filter show avatar via display context
			// 2.3.0: maybe add link from avatar to show
			$show_avatar = radio_station_get_show_avatar( $show['id'] );
			$show_avatar = apply_filters( 'radio_station_current_show_avatar', $show_avatar, $show['id'], $atts );
			if ( $show_avatar ) {
				$output .= '<div class="current-show-avatar on-air-dj-avatar' . esc_attr( $floatclass ) . '" ' . $widthstyle . '>';
				if ( $show_link ) {
					$output .= '<a href="' . esc_url( $show_link ) . '">' . $show_avatar . '</a>';
				} else {
					$output .= $show_avatar;
				}
				$output .= '</div>';
			}
		}

		// --- show title (all other positions) ---
		if ( 'above' != $atts['title_position'] ) {
			$output .= $title; // already escaped
		}

		// --- encore presentation ---
		// 2.3.0: added encore presentation display
		if ( isset( $show['encore'] ) && ( $show['encore'] ) ) {
			$output .= '<div class="current-show-encore on-air-dj-encore">';
			$output .= esc_html( __( 'Encore Presentation', 'radio-station' ) );
			$output .= '</div>';
		}

		// --- show DJs / hosts ---
		if ( $atts['display_hosts'] ) {

			$hosts = get_post_meta( $show['id'], 'show_user_list', true );

			if ( $hosts && is_array( $hosts ) && ( count( $hosts ) > 0 ) ) {

				$output .= '<div class="current-show-hosts on-air-dj-names">';

				$output .= esc_html( __( 'with', 'radio-station' ) ) . ' ';

				$count = 0;
				$host_count = count( $hosts );
				foreach ( $hosts as $host ) {

					$count ++;

					// 2.3.0: maybe get stored user data
					// $user = get_userdata( $host );
					if ( isset( $radio_station_data['user-' . $host] ) ) {
						$user = $radio_station_data['user-' . $host];
					} else {
						$user = get_user_by( 'ID', $host );
						$radio_station_data['user-' . $host] = $user;
					}

					if ( $atts['link_djs'] ) {
						// 2.3.0: use new get host URL function
						$host_link = radio_station_get_host_url( $host );
						$host_link = apply_filters( 'radio_station_dj_link', $host_link, $host );

						$output .= '<a href="' . esc_url( $host_link ) . '">';
						$output .= esc_html( $user->display_name );
						$output .= '</a>';
					} else {
						$output .= esc_html( $user->display_name );
					}

					if ( ( ( 1 == $count ) && ( 2 == $host_count ) )
					     || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) == $count ) ) ) {
						$output .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
					} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
						$output .= ', ';
					}
				}
				$output .= '</div>';
			}
		}

		$output .= '<span class="radio-clear"></span>';

		// --- show description ---
		// 2.3.0: convert span to div tags for consistency
		if ( $atts['show_desc'] ) {

			// --- get show post ---
			$show_post = get_post( $show['id'] );

			// --- get show excerpt ---
			if ( !empty( $show_post->post_excerpt ) ) {
				$excerpt = $show_post->post_excerpt;
			} else {
				$excerpt = radio_station_trim_excerpt( $show_post->post_content, $length, $more );
			}

			// --- filter excerpt by context ---
			// 2.3.0: added contextual filtering
			if ( $atts['widget'] ) {
				$excerpt = apply_filters( 'radio_station_current_show_widget_excerpt', $excerpt, $show['id'] );
			} else {
				$excerpt = apply_filters( 'radio_station_current_show_shortcode_excerpt', $excerpt, $show['id'] );
			}

			// --- output excerpt ---
			$output .= '<div class="current-show-desc on-air-show-desc">';
			$output .= $excerpt;
			$output .= '</div>';
		}

		// --- current show playlist ---
		// 2.3.0: convert span to div tags for consistency
		if ( $atts['show_playlist'] ) {
			// 2.3.0: use new function to get current playlist
			$playlist  = radio_station_get_now_playing();
			$output .= '<div class="current-show-playlist on-air-dj-playlist">';
			$output .= '<a href="' . esc_url( $playlist['playlist_url'] ) . '">';
			$output .= esc_html( __( 'View Playlist', 'radio-station' ) );
			$output .= '</a>';
			$output .= '</div>';
		}

		$output .= '<span class="radio-clear"></span>';

		// --- show schedule ---
		if ( $atts['show_sched'] ) {

			$output .= '<br><div class="current-show-schedule on-air-dj-schedule">';

			// --- maybe show all shifts ---
			// (only if not a schedule override)
			if ( !isset( $show['override'] ) && $atts['show_all_sched'] ) {
				$shifts = get_post_meta( $show['id'], 'show_sched', true );
			} else {
				$shifts = array( $show['shifts'] );
			}

			foreach ( $shifts as $shift ) {

				// --- convert shift info ---
				// 2.2.2: translate weekday for display
				$display_day = radio_station_translate_weekday( $shift['day'] );
				$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
				$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
				$shift_start_time = strtotime( $start );
				$shift_end_time = strtotime( $end );
				if ( $start > $end ) {
					$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
				}

				// --- add class to highlight now playing shift ---
				$classes = array( 'current-show-shifts', 'on-air-dj-sched' );
				$now = strtotime( current_time( 'mysql' ) );
				if ( ( $now > $shift_start_time ) && ( $now < $shift_end_time ) ) {
					$current_shift = $shift;
					$current_shift_start = $shift_start_time;
					$current_shift_end = $shift_end_time;
					$classes[] = 'current-shift';
				}
				$class = implode( ' ', $classes );

				// --- convert shift times ---
				if ( 24 == (int) $atts['time'] ) {
					$start = radio_station_convert_time( $start, 24 );
					$end = radio_station_convert_time( $end, 24 );
					$data_format =  'j, g:i';
					$data_format2 = 'g:i';
				} else {
					$start = str_replace( array( 'am', 'pm' ), array( $am, $pm ), $start );
					$end = str_replace( array( 'am', 'pm' ), array( $am, $pm ), $end );
					$data_format = 'j, H:i a';
					$data_format2 = 'H:i a';
				}

				// --- shift display output ---
				$output .= '<div class="' . esc_attr( $class ) . '">';
				$output .= '<span class="rs-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $data_format ) . '">';
				$output .= esc_html( $display_day ) . ', ' . $start . '</span> - ';
				$output .= '<span class="rs-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $data_format2 ) . '">' . $end . '</span>';
				$output .= '</div>';
			}

			$output .= '</div>';
		}

		// --- countdown timer display ---
		if ( isset( $current_shift_end ) && $atts['countdown'] ) {
			$output .= '<div class="current-show-countdown rs-countdown"></div>';
			do_action( 'radio_station_countdown_enqueue' );
		}

		$output .= '</li>';

	}

	// --- close show list ---
	$output .= '</ul>';

	// --- countdown timers ---
	if ( isset( $current_shift_end ) && ( $atts['countdown'] || $atts['dynamic'] ) ) {

		// --- hidden inputs for current shift time ---
		$output .= '<input type="hidden" class="current-show-end" value="' . esc_attr( $current_shift_end ) . '">';

		// --- for dynamic reloading ---
		if ( $atts['dynamic'] ) {
			$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'current_show', $atts, $current_shift_end );
			if ( $dynamic ) {
				$output .= $dynamic;
			}
		}
	}

	// --- close shortcode div wrapper ---
	if ( !$atts['widget'] ) {
		$output .= '</div>';
	}

	return $output;
}

// ------------------------
// Upcoming Shows Shortcode
// ------------------------
// [upcoming-shows] / [dj-coming-up-widget]
// 2.0.9: shortcode for displaying upcoming DJs/shows
// 2.3.0: added missing output sanitization
// 2.3.0: added new upcoming-shows shortcode alias
add_shortcode( 'dj-coming-up-widget', 'radio_station_upcoming_shows_shortcode' );
add_shortcode( 'upcoming-shows', 'radio_station_upcoming_shows_shortcode' );
function radio_station_upcoming_shows_shortcode( $atts ) {

	global $radio_station_data;

	$output = '';

	// 2.3.0: set default time format to plugin setting
	$time_format = radio_station_get_setting( 'clock_time_format' );
	$defaults = array(
		// --- legacy options ---
		'title'             => '',
		'show_avatar'       => 0,
		'show_link'         => 0,
		'limit'             => 1,
		'time'              => $time_format,
		'show_sched'        => 1,
		'default_name'      => '',
		// --- new options ---
		// 'display_producers' => 0,
		// 'show_desc'         => 0,
		'display_hosts'     => 0,
		'avatar_width'      => '',
		'title_position'    => 'right',
		'link_djs'          => 0,
		'countdown'         => 0,
		'dynamic'           => 0,
		'widget'            => 0,
		'id'                => '',
	);
	// 2.3.0: convert old attribute display_djs to display_hosts
	if ( isset( $atts['display_djs'] ) && !isset( $atts['display_hosts'] ) ) {
		$atts['display_hosts'] = $atts['display_djs'];
		unset( $atts['display_djs'] );
	}
	// 2.3.0: renamed shortcode identifier to upcoming-shows
	$atts = shortcode_atts( $defaults, $atts, 'upcoming-shows' );

	// 2.2.4: maybe set float class and avatar width style
	// 2.3.0: moved here from upcoming widget class
	$width_style = $float_class = '';
	if ( !empty( $atts['avatar_width'] ) ) {
		$width_style = 'style="width:' . esc_attr( $atts['avatar_width'] ) . 'px;"';
	}
	if ( 'right' == $atts['title_position'] ) {
		$float_class = ' float-left';
	} elseif ( 'left' == $atts['title_position'] ) {
		$float_class = ' float-right';
	}

	// --- get meridiem conversions ---
	// 2.3.0: added once-off pre-conversions
	if ( 12 == (int) $atts['time'] ) {
		$am = radio_station_translate_meridiem( 'am' );
		$pm = radio_station_translate_meridiem( 'pm' );
	}

	// --- get the upcoming shows ---
	// 2.3.0: use new get next shows function
	$shows = radio_station_get_next_shows( $atts['limit'] );

	// --- open shortcode only wrapper ---
	if ( !$atts['widget'] ) {

		// 2.3.0: add unique id to widget shortcode
		if ( !isset( $radio_station_data['widgets']['upcoming-shows'] ) ) {
			$radio_station_data['widgets']['upcoming-shows'] = 0;
		} else {
			$radio_station_data['widgets']['upcoming-shows']++;
		}
		$id = 'upcoming-shows-widget-' . $radio_station_data['widgets']['upcoming-shows'];
		$output .= '<div id="' . esc_attr( $id ) . '" class="upcoming-shows-embedded on-air-embedded dj-coming-up-embedded">';

		// --- maybe output shortcode title ---
		if ( !empty( $atts['title'] ) ) {
			$output .= '<h3 class="upcoming-shows-title dj-coming-up-title">';
			$output .= esc_html( $atts['title'] );
			$output .= '</h3>';
		}
	}

	// --- open upcoming show list ---
	$output .= '<ul class="upcoming-shows-list on-air-upcoming-list">';

	// --- no shows upcoming output ---
	if ( !$shows ) {

		$output .= '<li class="upcoming-show-none on-air-dj default-dj">';
		if ( ! empty( $atts['default_name'] ) ) {
			$output .= esc_html( $atts['default_name'] );
		} else {
			$output .= esc_html( __( 'No Upcoming Shows Scheduled.', 'radio-station' ) );
		}
		$output .= '</li>';

	} else {

		// --- loop upcoming shows ---
		foreach ( $shows as $i => $shift ) {

			$show = $shift['show'];

			$show_link = false;
			if ( $atts['show_link'] ) {
				$show_link = get_permalink( $show['id'] );
				$show_link = apply_filters( 'radio_station_upcoming_show_link', $show_link, $show['id'], $atts );
			}

			$output .= '<li class="upcoming-show on-air-dj">';

			// --- set show title output ---
			$title = '<div class="upcoming-show-title on-air-dj-title">';
			if ( $show_link ) {
				$title .= '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
			} else {
				$title .= esc_html( $show['name'] );
			}
			$title .= '</div>';

			// --- show title (above position only) ---
			// (for above position only)
			if ( 'above' == $atts['title_position'] ) {
				$output .= $title; // already escaped
			}

			// --- show avatar ---
			if ( $atts['show_avatar'] ) {

				// 2.3.0: get show avatar (with thumbnail fallback)
				// 2.3.0: filter show avatar by context
				// 2.3.0: maybe link avatar to show
				$show_avatar = radio_station_get_show_avatar( $show['id'] );
				$show_avatar = apply_filters( 'radio_station_upcoming_show_avatar', $show_avatar, $show['id'], $atts );
				if ( $show_avatar ) {
					$output .= '<div class="upcoming-show-avatar on-air-dj-avatar' . esc_attr( $float_class ) . '" ' . $width_style . '>';
					if ( $atts['show_link'] ) {
						$output .= '<a href="' . esc_url( $show_link ) . '">';
					}
					$output .= $show_avatar;
					if ( $atts['show_link'] ) {
						$output .= '</a>';
					}
					$output .= '</div>';
				}
			}

			// --- show title (all other positions) ---
			// (for all positions except above)
			if ( 'above' != $atts['title_position'] ) {
				$output .= $title; // already escaped
			}

			$output .= '<span class="radio-clear"></span>';

			// --- encore presentation ---
			// 2.2.4: added encore presentation display
			// if ( array_key_exists( $showtime, $djs['encore'] ) ) {
			if ( isset( $show['encore'] ) && ( $show['encore'] ) ) {
				$output .= '<div class="upcoming-show-encore on-air-dj-encore">';
				$output .= esc_html( __( 'Encore Presentation', 'radio-station' ) );
				$output .= '</div>';
			}

			// --- DJ / Host names ---
			if ( $atts['display_hosts'] ) {

				$hosts = get_post_meta( $show['id'], 'show_user_list', true );
				if ( isset( $hosts ) && is_array( $hosts ) && ( count( $hosts ) > 0 ) ) {

					$output .= '<div class="upcoming-show-hosts on-air-dj-names">';

					$output .= esc_html( __( 'with', 'radio-station' ) ) . ' ';

					$count = 0;
					$host_count = count( $hosts );
					foreach ( $hosts as $host ) {

						$count ++;

						// 2.3.0: maybe get stored user data
						// $user = get_userdata( $host );
						if ( isset( $radio_station_data['user-' . $host] ) ) {
							$user = $radio_station_data['user-' . $host];
						} else {
							$user = get_user_by( 'ID', $host );
							$radio_station_data['user-' . $host] = $user;
						}

						if ( $atts['link_djs'] ) {
							// 2.3.0: use new get host URL function
							$host_link = radio_station_get_host_url( $host );
							$host_link = apply_filters( 'radio_station_dj_link', $host_link, $host );

							$output .= '<a href="' . esc_url( $host_link ) . '">';
							$output .= esc_html( $user->display_name );
							$output .= '</a>';
						} else {
							$output .= esc_html( $user->display_name );
						}

						if ( ( ( 1 == $count ) && ( 2 == $host_count ) )
						     || ( ( $host_count > 2 ) && ( $count == ( $host_count - 1 ) ) ) ) {
							$output .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
						} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
							$output .= ', ';
						}
					}
					$output .= '</div>';
				}
			}

			$output .= '<span class="radio-clear"></span>';

			// --- show schedule ---
			if ( $atts['show_sched'] ) {

				$output .= '<div class="upcoming-show-schedule on-air-dj-schedule">';

				// --- convert shift info ---
				// 2.2.2: fix to weekday value to be translated
				$display_day = radio_station_translate_weekday( $shift['day'] );
				$shift_start_time = strtotime ( $shift['day'] . ' ' . $shift['start'] );
				$shift_end_time = strtotime( $shift['day'] . ' ' . $shift['end'] );
				if ( $shift_end_time < $shift_start_time ) {
					$shift_end_time = $shift_end_time + ( 7 * 60 * 60 );
				}

				// --- maybe set next show shift times ---
				if ( !isset( $next_start_time ) ) {
					$next_start_time = $shift_start_time;
					$next_end_time = $shift_end_time;
				}

				$classes = array( 'upcoming-show-shift', 'on-air-dj-sched' );
				$now = strtotime( current_time( 'mysql' ) );
				if ( ( $now > $shift_start_time ) && ( $now < $shift_end_time ) ) {
					$classes[] = 'current-shift';
				}
				$class = implode( ' ', $classes );

				// 2.2.7: fix to convert time to integer
				if ( 24 == (int) $atts['time'] ) {

					// --- convert start/end time to 24 hours ---
					$start = radio_station_convert_time( $shift['start'], 24 );
					$end = radio_station_convert_time( $shift['end'], 24 );
					$data_format = 'j, G:i';
					$data_format2 = 'G:i';

				} else {

					$start = str_replace( array( 'am', 'pm' ), array( $am, $pm ), $shift['start'] );
					$end = str_replace( array( 'am', 'pm' ), array( $am, $pm ), $shift['end'] );
					$data_format = 'j, H:i a';
					$data_format2 = 'H:i a';

				}

				$output .= '<div class="' . esc_attr( $class ) . '">';
				$output .= '<span class="rs-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $data_format ) . '">';
				$output .= esc_html( $display_day ) . ', ' . $start . '</span> - ';
				$output .= '<span class="rs-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $data_format2 ) . '">' . $end . '</span>';
				$output .= '</div>';

				$output .= '</div>';
			}

			// --- countdown timer display ---
			if ( ( 0 == $i ) && isset( $next_start_time ) && $atts['countdown'] ) {
				$output .= '<div class="upcoming-show-countdown rs-countdown"></div>';
				do_action( 'radio_station_countdown_enqueue' );
			}

			$output .= '</li>';
		}

	}

	// --- close upcoming shows list ---
	$output .= '</ul>';

	// --- countdown timer inputs ---
	// 2.3.0: added for countdowns
	if ( isset( $next_start_time ) && ( $atts['countdown'] || $atts['dynamic'] ) ) {

		// --- hidden input for next start time ---
		$output .= '<input type="hidden" class="upcoming-show-times" value="' . esc_attr( $next_start_time ) . '-' . esc_attr( $next_end_time ) . '">';
		$output .= '<!-- ' . date('y-m-d H:i:s', $next_start_time ) . ' --- ' . date( 'y-m-d H:i:s', $next_end_time ) . ' -->';

		// --- for dynamic reloading ---
		if ( $atts['dynamic'] ) {
			$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'upcoming_shows', $atts, $next_start_time );
			if ( $dynamic ) {
				$output .= $dynamic;
			}
		}
	}

	// --- close shortcode only wrapper ---
	if ( !$atts['widget'] ) {
		$output .= '</div>';
	}

	return $output;
}

// ---------------------
// Now Playing Shortcode
// ---------------------
// [current-playlist] / [now-playing]
// 2.3.0: added missing output sanitization
add_shortcode( 'current-playlist', 'radio_station_current_playlist_shortcode' );
add_shortcode( 'now-playing', 'radio_station_current_playlist_shortcode' );
function radio_station_current_playlist_shortcode( $atts ) {

	global $radio_station_data;

	$output = '';

	// --- get shortcode attributes ---
	$defaults = array(
		// --- legacy options ---
		'title'     => '',
		'artist'    => 1,
		'song'      => 1,
		'album'     => 0,
		'label'     => 0,
		'comments'  => 0,
		// --- new options ---
		'countdown' => 0,
		'dynamic'   => 0,
		'widget'    => 0,
		'id'        => '',
	);
	// 2.3.0: renamed shortcode identifier to current-playlist
	$atts = shortcode_atts( $defaults, $atts, 'current-playlist' );

	// --- fetch the current playlist ---
	$playlist = radio_station_get_now_playing();

	// --- shortcode only wrapper ---
	if ( !$atts['widget'] ) {

		// 2.3.0: add unique id to widget shortcode
		if ( !isset( $radio_station_data['widgets']['current-playlist'] ) ) {
			$radio_station_data['widgets']['current-playlist'] = 0;
		} else {
			$radio_station_data['widgets']['current-playlist']++;
		}
		$id = 'show-playlist-widget-' . $radio_station_data['widgets']['current-playlist'];
		$output .= '<div id="' . esc_attr( $id ) . '" class="upcoming-shows-embedded on-air-embedded dj-coming-up-embedded">';

		// --- shortcode title ---
		if ( !empty( $atts['title'] ) ) {
			// 2.3.0: added title class for shortcode
			$output .= '<h3 class="show-playlist-title myplaylist-title">' . esc_attr( $atts['title'] ) . '</h3>';
		}
	}

	// 2.3.0: use updated code from now playing widget
	if ( $playlist ) {

		// 2.3.0: split div wrapper from track wrapper
		$output .= '<div class="show-playlist-tracks myplaylist-nowplaying">';

		// --- loop playlist tracks ---
		// 2.3.0: loop all instead of just latest
		foreach ( $playlist['tracks'] as $track ) {

			$class = '';
			if ( isset( $track['playlist_entry_new'] ) && ( 'on' === $track['playlist_entry_new'] ) ) {
				$class .= ' new';
			}
			// 2.3.0: added check for latest track since looping
			if ( $track == $playlist['latest'] ) {
				$class .= ' latest';
			} else {
				$class .= ' played';
			}

			$output .= '<div class="show-playlist-track myplaylist-track' . esc_attr( $class ) . '">';

			// 2.2.3: convert span tags to div tags
			// 2.2.4: check value keys are set before outputting
			if ( $atts['song'] && isset( $track['playlist_entry_song'] ) ) {
				$output .= '<div class="show-playlist-song myplaylist-song">';
				$output .= esc_html( __( 'Song', 'radio-station' ) );
				$output .= ': ' . esc_html( $track['playlist_entry_song'] );
				$output .= '</div>';
			}

			// 2.2.7: add label prefixes to now playing data
			if ( $atts['artist'] && isset( $track['playlist_entry_artist'] ) ) {
				$output .= '<div class="show-playlist-artist myplaylist-artist">';
				$output .= esc_html( __( 'Artist', 'radio-station' ) );
				$output .= ': ' . esc_html( $track['playlist_entry_artist'] );
				$output .= '</div>';
			}

			if ( $atts['album'] && isset( $track['playlist_entry_album'] ) ) {
				$output .= '<div class="show-playlist-album myplaylist-album">';
				$output .= esc_html( __( 'Album', 'radio-station' ) );
				$output .= ': ' . esc_html( $track['playlist_entry_album'] );
				$output .= '</div>';
			}

			if ( $atts['label'] && isset( $track['playlist_entry_label'] ) ) {
				$output .= '<div class="show-playlist-label myplaylist-label">';
				$output .= esc_html( __( 'Label', 'radio-station' ) );
				$output .= ': ' . esc_html( $track['playlist_entry_label'] );
				$output .= '</div>';
			}

			if ( $atts['comments'] && isset( $track['playlist_entry_comments'] ) ) {
				$output .= '<div class="show-playlist-comments myplaylist-comments">';
				$output .= esc_html( __( 'Comments', 'radio-station' ) );
				$output .= ': ' . esc_html( $track['playlist_entry_comments'] );
				$output .= '</div>';
			}

			$output .= '</div>';
		}

		// --- playlist permalink ---
		if ( isset( $playlist['playlist_permalink'] ) ) {
			$output .= '<div class="show-playlist-link myplaylist-link">';
			$output .= '<a href="' . esc_url( $playlist['playlist_permalink'] ) . '">';
			$output .= esc_html( __( 'View Playlist', 'radio-station' ) );
			$output .= '</a>';
			$output .= '</div>';
		}

		// --- countdown timer ---
		// 2.3.0: added for countdown changeovers
		if ( $atts['countdown'] || $atts['dynamic'] ) {

			foreach ( $playlist['shifts'] as $shift ) {

				// --- convert shift info ---
				$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
				$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
				$shift_start_time = strtotime( $start );
				$shift_end_time = strtotime( $end );
				if ( $start > $end ) {
					$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
				}

				// --- check currently playing show time ---
				$now = strtotime( current_time( 'mysql' ) );
				if ( ( $now > $shift_start_time ) && ( $now < $shift_end_time ) ) {

					// --- hidden input for playlist end time ---
					$output .= '<input type="hidden" class="current-playlist-end" value="' . esc_attr( $shift_end_time ) . '">';

					// --- for countdown timer display ---
					if ( $atts['countdown'] ) {
						$output .= '<div class="show-playlist-countdown rs-countdown"></div>';
						do_action( 'radio_station_countdown_enqueue' );
					}

					// --- for dynamic reloading ---
					if ( $atts['dynamic'] ) {
						$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'current_playlist', $atts, $shift_end_time );
						if ( $dynamic ) {
							$output .= $dynamic;
						}
					}

				}
			}
		}

	} else {
		// 2.2.3: added missing translation wrapper
		// 2.3.0: added no playlist class
		$output .= '<div class="show-playlist-noplaylist myplaylist-noplaylist>';
		$output .= esc_html( __( 'No Playlist available.', 'radio-station' ) );
		$output .= '</div>';
	}

	// --- close shortcode only wrapper ---
	if ( !$atts['widget'] ) {
		$output .= '</div>';
	}
	
	return $output;
}

// ----------------
// Countdown Script
// ----------------
// 2.3.0: added shortcode/widget countdown script
add_action( 'radio_station_countdown_enqueue', 'radio_station_countdown_enqueue' );
function radio_station_countdown_enqueue() {

	// --- enqueue countdown script ---
	radio_station_enqueue_script( 'radio-station-countdown', array( 'radio-station' ), true );

	// --- set countdown labels ---
	$js = "radio.label_showstarted = '" . esc_js( __( 'This Show has started.', 'radio-station' ) ) . "';
	radio.label_showended = '" . esc_js( __( 'This Show has ended.', 'radio-station' ) ) . "';
	radio.label_playlistended = '" . esc_js( __( 'This Playlist has ended.', 'radio-station') ) . "';
	radio.label_timecommencing = '" . esc_js( __( 'Commencing in', 'radio-station' ) ) . "';
	radio.label_timeremaining = '" . esc_js( __( 'Remaining', 'radio-station' ) ) . "'; 
	";

	// --- add script inline ---
	wp_add_inline_script( 'radio-station-countdown', $js );

}


// -------------------------
// === Legacy Shortcodes ===
// -------------------------

// -------------------
// Show List Shortcode
// -------------------
// 2.0.0: shortcode for displaying a list of all shows
// [list-shows]
add_shortcode( 'list-shows', 'radio_station_shortcode_list_shows' );
function radio_station_shortcode_list_shows( $atts ) {

	$defaults = array(
		'title' => false,
		'genre' => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'list-shows' );

	// grab the published shows
	$args = array(
		'posts_per_page' => 1000,
		'offset'         => 0,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'post_type'      => RADIO_STATION_SHOW_SLUG,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				// 2.3.0: fix key/value to meta_key/meta_value
				'meta_key'   => 'show_active',
				'meta_value' => 'on',
				'compare'    => '=',
			),
		),
	);
	if ( !empty( $atts['genre'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => RADIO_STATION_GENRES_SLUG,
				'field'    => 'slug',
				'terms'    => $atts['genre'],
			),
		);
	}

	// 2.3.0: use get_posts instead of WP_Query
	$posts = get_posts( $args );

	// if there are no shows saved, return nothing
	if ( !$posts || ( count( $posts ) == 0 ) ) {
		return '';
	}

	$output = '';

	$output .= '<div id="station-show-list">';

	if ( $atts['title'] ) {
		$output .= '<div class="station-show-list-title">';
		$output .= '<h3>' . esc_html( $atts['title'] ) . '</h3>';
		$output .= '</div>';
	}

	$output .= '<ul class="show-list">';

	// 2.3.0: use posts loop instead of query loop
	foreach ( $posts as $post ) {

		$output .= '<li class="show-list-item">';

		$output .= '<div class="show-list-item-title">';
		$output .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">';
		$output .= esc_html( get_the_title( $post->ID ) ) . '</a>';
		$output .= '</div>';

		$output .= '</li>';
	}

	$output .= '</ul>';
	$output .= '</div>';

	return $output;
}

// ------------------------
// Show Playlists Shortcode
// ------------------------
// 2.0.0: shortcode to fetch all playlists for a given show id
// 2.3.0: added missing output sanitization
// [get-playlists] / [show-playlists]
add_shortcode( 'show-playlists', 'radio_station_shortcode_get_playlists_for_show' );
add_shortcode( 'get-playlists', 'radio_station_shortcode_get_playlists_for_show' );
function radio_station_shortcode_get_playlists_for_show( $atts ) {

	$atts = shortcode_atts(
		array(
			'show'  => '',
			'limit' => - 1,
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
		'post_type'      => RADIO_STATION_PLAYLIST_SLUG,
		'post_status'    => 'publish',
		'meta_key'       => 'playlist_show_id',
		'meta_value'     => $atts['show'],
	);

	$query = new WP_Query( $args );
	$playlists = $query->posts;

	// 2.3.0: return empty if no posts found
	if ( 0 == $query->post_count ) {
		return '';
	}

	$output = '';

	$output .= '<div id="myplaylist-playlistlinks">';
	$output .= '<ul class="myplaylist-linklist">';
	foreach ( $playlists as $playlist ) {
		$output .= '<li class="myplaylist-linklist-item">';
		$output .= '<a href="' . esc_url( get_permalink( $playlist->ID ) ) . '">';
		$output .= esc_html( $playlist->post_title ) . '</a>';
		$output .= '</li>';
	}
	$output .= '</ul>';

	$playlist_archive = get_post_type_archive_link( RADIO_STATION_PLAYLIST_SLUG );
	$params = array( 'show_id' => $atts['show'] );
	$playlist_archive = add_query_arg( $params, $playlist_archive );

	$output .= '<a href="' . esc_url( $playlist_archive ) . '">' . esc_html( __( 'More Playlists', 'radio-station' ) ) . '</a>';

	$output .= '</div>';

	return $output;
}
