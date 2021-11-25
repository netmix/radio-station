<?php

/* Shortcode for displaying the current song
 * Since 2.0.0
 */

// note: Master Schedule Shortcode in /includes/master-schedule.php

// === Time Shortcodes ===
// - Radio Timezone Shortcode
// - Radio Clock Shortcode
// === Archive Shortcodes ===
// - Archive List Shortcode Abstract
// - Show Archive Shortcode
// - Playlist Archive Shortcode
// - Override Archive Shortcode
// - Genre Archive Shortcode
// - Language Archive Shortcode
// - Archive Pagination Javascript
// === Show Related Shortcodes ===
// - Show List Shortcode Abstract
// - Show Posts Archive Shortcode
// - Show Playlists List Shortcode
// - Show Lists Pagination Javascript
// === Widget Shortcodes ===
// - Current Show Shortcode
// - AJAX Current Show Loader
// - Upcoming Shows Shortcode
// - AJAX Upcoming Shows Shortcode
// - Current Playlist Shortcode
// =-AJAX Current Playlist Loader
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

	global $radio_station_data;

	// --- set shortcode instance ---
	if ( isset( $radio_station_data['timezone_instance'] ) ) {
		$instance = $radio_station_data['timezone_instance'];
	} else {
		$instance = $radio_station_data['timezone_instance'] = 0;
	}

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
				$utc_offset = '[' . __( 'UTC', 'radio-station' ) . '+' . $offset . ']';
			} else {
				$utc_offset = '[' . __( 'UTC', 'radio-station' ) . $offset . ']';
			}
		}
		$code = radio_station_get_timezone_code( $timezone );
		// 2.3.2: display full timezone location as well
		$location = str_replace( '/', ', ', $timezone );
		$location = str_replace( '_', ' ', $location );
		$timezone_display = $code . ' (' . $location . ') ' . $utc_offset;
	}

	// --- set shortcode output ---
	$output = '<div class="radio-timezone-wrapper">';

	// --- radio timezone ---
	$output .= '<div class="radio-timezone-title">';
	$output .= esc_html( __( 'Radio Timezone', 'radio-station' ) );
	$output .= ':</div> ';
	$output .= '<div class="radio-timezone">' . esc_html( $timezone_display ) . '</div><br>';

	// --- user timezone ---
	// 2.3.3.9: change span elements to divs
	$output .= '<div class="radio-user-timezone-title">';
	$output .= esc_html( __( 'Your Timezone', 'radio-station' ) );
	$output .= ':</div> ';
	$output .= '<div class="radio-user-timezone"></div>';

	// 2.3.2 allow for timezone selector test
	// $select = apply_filters( 'radio_station_timezone_select', '', 'radio-station-timezone-' . $instance, $atts );
	// if ( '' != $select ) {
	// 	$output .= $select;
	// }

	$output .= '</div>';

	// --- enqueue shortcode styles ---
	// 2.3.2: added for timezone shortcode styles
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return ---
	$output = apply_filters( 'radio_station_timezone_shortcode', $output, $atts );
	return $output;
}

// ---------------------
// Radio Clock Shortcode
// ---------------------
add_shortcode( 'radio-clock', 'radio_station_clock_shortcode' );
function radio_station_clock_shortcode( $atts = array() ) {

	global $radio_station_data;

	// --- set shortcode instance ---
	if ( isset( $radio_station_data['clock_instance'] ) ) {
		$instance = $radio_station_data['clock_instance'];
	} else {
		$instance = $radio_station_data['clock_instance'] = 0;
	}

	// 2.3.3: use plugin setting if time format attribute is empty
	if ( isset( $atts['time'] ) && ( '' == $atts['time'] ) ) {
		unset( $atts['time'] );
	}

	// --- merge default attributes ---
	// 2.3.3: fix to incorrect setting key (clock_format)
	$clock_format = radio_station_get_setting( 'clock_time_format' );
	$defaults = array(
		'time'    => $clock_format,
		'seconds' => 1,
		'day'     => 'full', // full / short / none
		'date'    => 1,
		'month'   => 'full', // full / short / none
		'zone'    => 1,
		'widget'  => 0,
	);
	$atts = shortcode_atts( $defaults, $atts, 'radio-clock' );

	// --- set clock display classes ---
	$classes = array( 'radio-station-clock' );
	if ( $atts['widget'] ) {
		$classes[] = 'radio-station-clock-widget';
	} else {
		$classes[] = 'radio-station-clock-shortcode';
	}
	if ( 24 == $atts['time'] ) {
		$classes[] = 'format-24';
	} else {
		$classes[] = 'format-12';
	}
	if ( $atts['seconds'] ) {
		$classes[] = 'seconds';
	}
	if ( $atts['day'] ) {
		if ( 'full' == $atts['day'] ) {
			$classes[] = 'day';
		} elseif ( 'short' == $atts['day'] ) {
			$classes[] = 'day-short';
		}
	}
	if ( $atts['date'] ) {
		$classes[] = 'date';
	}
	if ( $atts['month'] ) {
		if ( 'full' == $atts['month'] ) {
			$classes[] = 'month';
		} elseif ( 'short' == $atts['month'] ) {
			$classes[] = 'month-short';
		}
	}
	if ( $atts['zone'] ) {
		$classes[] = 'zone';
	}

	// -- open clock div ---
	$classlist = implode( ' ', $classes );
	$clock = '<div id="radio-station-clock-' . esc_attr( $instance ) . '" class="' . esc_attr( $classlist ) . '">';

	// --- server clock ---
	$clock .= '<div class="radio-station-server-clock">';
	$clock .= '<div class="radio-clock-title">';
	$clock .= esc_html( __( 'Radio Time', 'radio-station' ) );
	$clock .= ':</div>';
	$clock .= '<div class="radio-server-time" data-format="' . esc_attr( $atts['time'] ) . '"></div>';
	$clock .= '<div class="radio-server-date"></div>';
	$clock .= '<div class="radio-server-zone"></div>';
	$clock .= '</div>';

	// --- user clock ---
	$clock .= '<div class="radio-station-user-clock">';
	$clock .= '<div class="radio-clock-title">';
	$clock .= esc_html( __( 'Your Time', 'radio-station' ) );
	$clock .= ':</div>';
	$clock .= '<div class="radio-user-time" data-format="' . esc_attr( $atts['time'] ) . '"></div>';
	$clock .= '<div class="radio-user-date"></div>';
	$clock .= '<div class="radio-user-zone"></div>';
	$clock .= '</div>';

	// 2.3.2 allow for timezone selector test
	// $select = radio_station_timezone_select( 'radio-station-clock-' + $instance );
	// $select = apply_filters( 'radio_station_clock_timezone_select', '', 'radio-station-clock-' . $instance, $atts );
	// if ( '' != $select ) {
	// 	$clock .= $select;
	// }

	$clock .= '</div>';

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- enqueue clock javascript ---
	radio_station_enqueue_script( 'radio-station-clock', array(), true );

	// --- filter and return ---
	$clock = apply_filters( 'radio_station_clock', $clock, $atts );

	return $clock;
}


// --------------------------
// === Archive Shortcodes ===
// --------------------------

// -------------------------------
// Archive List Shortcode Abstract
// -------------------------------
// (handles Shows, Overrides, Playlists etc.)
function radio_station_archive_list_shortcode( $post_type, $atts ) {

	// --- set type from post type ---
	$type = str_replace( 'rs-', '', $post_type );

	// --- get clock time format ---
	$time_format = radio_station_get_setting( 'clock_time_format' );

	// --- merge defaults with passed attributes ---
	// 2.3.3.9: add atts for specific posts
	// 2.4.0.4: added optional view attribute
	$defaults = array(
		// --- shortcode display ----
		'description'  => 'excerpt',
		'hide_empty'   => 0,
		'time'         => $time_format,
		'view'         => 'none', // changed from 0 to none because of string comparison in PHP7
		// --- taxonomy queries ---
		'genre'        => '',
		'language'     => '',
		// --- query args ---
		'orderby'      => 'title',
		'order'        => 'ASC',
		'status'       => 'publish',
		'perpage'      => - 1,
		'offset'       => 0,
		'pagination'   => 1,
		// --- shows only ---
		'with_shifts'  => 1,
		// 'show_shifts' => 0,
		// --- for overrides only ---
		'show_dates' => 1,
		// --- for shows and overrides ---
		// 'display_genres' => 0,
		// 'display_languages' => 0,
		'show_avatars' => 1,
		'thumbnails'   => 0,
		// --- for playlists ---
		// 'track_count' => 0,
		// --- specific posts ---
		'show'         => false,
		'override'     => false,
		'playlist'     => false,
	);

	// --- handle possible pagination offset ---
	if ( isset( $atts['perpage'] ) && !isset( $atts['offset'] ) && get_query_var( 'page' ) ) {
		$page = absint( get_query_var( 'page' ) );
		if ( $page > - 1 ) {
			$atts['offset'] = (int) $atts['perpage'] * $page;
		}
	}
	$atts = shortcode_atts( $defaults, $atts, $post_type . '-archive' );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Shortcode Atts: ' . print_r( $atts, true ) . '</span>';
	}

	// --- get published shows ---
	// 2.3.3.9: ignore offset and limit and reapply later
	$args = array(
		'post_type'   => $post_type,
		'post_status' => $atts['status'],
		'numberposts' => - 1,
		// 'numberposts' => $atts['perpage'],
		// 'offset'      => $atts['offset'],
		'orderby'     => $atts['orderby'],
		'order'       => $atts['order'],
	);

	// --- extra queries for shows ---
	if ( RADIO_STATION_SHOW_SLUG == $post_type ) {

		if ( $atts['with_shifts'] ) {

			// --- active shows with shifts ---
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key'		=> 'show_sched',
					'compare'	=> 'EXISTS',
				),
				array(
					'key'		=> 'show_active',
					'value'		=> 'on',
					'compare'	=> '=',
				),
			);

		} else {

			// --- just active shows ---
			$args['meta_query'] = array(
				array(
					'key'		=> 'show_active',
					'value' 	=> 'on',
					'compare'	=> '=',
				),
			);
		}
	}

	// --- specific genres taxonomy query ---
	if ( !empty( $atts['genre'] ) && in_array( $post_type, array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG ) ) ) {

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

	// --- specific languages taxonomy query ---
	// 2.3.0: added language taxonomy support
	if ( !empty( $atts['language'] ) && in_array( $post_type, array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG ) ) ) {

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

	// 2.3.3.9: allow for selective post specifications
	// 2.4.0: fix selective posts for default (false)
	if ( ( RADIO_STATION_SHOW_SLUG == $post_type ) && isset( $atts['show'] ) && $atts['show'] ) {
		$args['include'] = explode( ',', $atts['show'] );		
	} elseif ( ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) && isset( $atts['override'] ) && $atts['override'] ) {
		$args['include'] = explode( ',', $atts['override'] );
	} elseif ( ( RADIO_STATION_PLAYLIST_SLUG == $post_type ) && isset( $atts['playlist'] ) && $atts['playlist'] ) {
		$args['include'] = explode( ',', $atts['playlist'] );
	}

	// --- get posts via query ---
	$args = apply_filters( 'radio_station_' . $type . '_archive_post_args', $args );
	$archive_posts = get_posts( $args );
	$archive_posts = apply_filters( 'radio_station_' . $type . '_archive_posts', $archive_posts );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Archive Shortcode: ' . PHP_EOL;
		echo 'Args: ' . print_r( $args, true ) . PHP_EOL;
		echo 'Posts: ' . print_r( $archive_posts, true ) . '</span>';
	}
	
	// --- process playlist taxonomy query ---
	if ( RADIO_STATION_PLAYLIST_SLUG == $post_type ) {
		// 2.3.3.9: added missing check for matching archive post results	
		if ( $archive_posts && is_array( $archive_posts ) && ( count( $archive_posts ) > 0 ) ) {

			// --- check assigned show has a specified genre term ---
			if ( !empty( $atts['genre'] ) && $archive_posts && ( count( $archive_posts ) > 0 ) ) {
				if ( is_array( $atts['genre'] ) ) {$genres = $atts['genre'];}
				else {$genres = explode( ',', $atts['genre'] );}
				foreach ( $archive_posts as $i => $archive_post ) {
					$found_genre = false;
					$show_id = get_post_meta( $archive_post->ID, 'playlist_show_id', true );
					if ( $show_id ) {
						$show_genres = wp_get_post_terms( $show_id, RADIO_STATION_GENRES_SLUG );
						if ( $show_genres ) {
							foreach ( $show_genres as $show_genre ) {
								if ( in_array( $show_genre->term_id, $genres ) || in_array( $show_genre->slug, $genres) ) {
									$found_genre = true;
								}
							}
						}
					}
					if ( !$found_genre ) {
						unset( $archive_posts[$i] );
					}
				}
			}

			// --- check assigned show has a specified language term ---
			if ( !empty( $atts['language'] ) && $archive_posts && ( count( $archive_posts ) > 0 ) ) {
				if ( is_array( $atts['language'] ) )  {$languages = $atts['language'];}
				else {$languages = explode( ',', $atts['language'] );}
				foreach ( $archive_posts as $i => $archive_post ) {
					$found_language = false;
					$show_id = get_post_meta( $archive_post->ID, 'playlist_show_id', true );
					if ( $show_id ) {
						$show_languages = wp_get_post_terms( $show_id, RADIO_STATION_LANGUAGES_SLUG );
						if ( $show_languages ) {
							foreach ( $show_languages as $show_language ) {
								if ( in_array( $show_language->term_id, $languages ) || in_array( $show_language->slug, $languages ) ) {
									$found_language = true;
								}
							}
						}
					}
					if ( !$found_language ) {
						unset( $archive_posts[$i] );
					}
				}
			}

		}
	}

	// --- check override dates ---
	// (overrides without a date set will not be displayed)
	if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
		if ( $archive_posts && is_array( $archive_posts ) && ( count( $archive_posts ) > 0 ) ) {
			foreach( $archive_posts as $i => $archive_post ) {
				// 2.3.3.9: set singular to false to allow for multiple override times
				$override_times = get_post_meta( $archive_post->ID, 'show_override_sched', true );
				// 2.3.3.9: convert possible single override to array
				if ( $override_times && is_array( $override_times ) && array_key_exists( 'date', $override_times ) ) {
					$override_times = array( $override_times );
				}				
				if ( !$override_times || !is_array( $override_times ) || ( count( $override_times ) == 0 ) ) {
					unset( $archive_posts[$i] );
				} else {
					// 2.3.3.9: check if all override times are disabled
					$enabled = count( $override_times );
					foreach ( $override_times as $override_time ) {
						if ( isset( $override_time['disabled'] ) && ( 'yes' == $override_time['disabled'] ) ) {
							$enabled--;
						}					
					}
					if ( 0 == count( $enabled ) ) {
						unset( $archive_posts[$i] );
					}
				}
			}
		}
	}

	// --- set time data formats ---
	// 2.3.0: added once-off meridiem pre-conversions
	// 2.3.2: replaced meridiem conversions with data formats
	if ( 24 == (int) $atts['time'] ) {
		$start_data_format = $end_data_format = 'H:i';
	} else {
		$start_data_format = $end_data_format = 'g:i a';
	}
	$start_data_format = 'j, ' . $start_data_format;
	$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, $post_type . '-archive', $atts );
	$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, $post_type . '-archive', $atts );

	// --- check for results ---
	if ( !$archive_posts || !is_array( $archive_posts ) || ( count( $archive_posts ) == 0 ) ) {
		if ( $atts['hide_empty'] ) {
			return '';
		}
		$post_count = 0;
	} else {

		// --- count total archive posts ---
		$post_count = count( $archive_posts );
	
		// --- manually apply offset and perpage limit ---
		// 2.3.3.9: added to enable pagination count
		if ( $atts['offset'] && ( $atts['perpage'] > 0 ) ) {
			if ( $post_count > $atts['offset'] ) {
				$offset_posts = array();
				foreach ( $archive_posts as $i => $archive_post ) {
					if ( ( $i > $atts['offset'] ) && ( count( $offset_posts ) < $atts['perpage'] ) ) {
						$offset_posts[] = $archive_post;
					}
				}
				$archive_posts = $offset_posts;
				$post_count = count( $archive_posts );
			} else {
				$archive_posts = array();
				$post_count = 0;
			}
		}
	}

	// --- output list or no results message ---
	// 2.4.0.4: remove rs- prefix from element classes
	// 2.4.0.4: maybe add view class
	$classes = array( $type . '-archives' );
	if ( $atts['view'] ) {
		$classes[] = $atts['view'];
	}
	$class_list = implode( ' ', $classes );
	$list = '<div class="' . esc_attr( $class_list ) . '">';
	if ( !$archive_posts || !is_array( $archive_posts ) || ( count( $archive_posts ) == 0 ) ) {

		// --- no shows messages ----
		if ( RADIO_STATION_SHOW_SLUG == $post_type ) {
			// 2.3.3.9: improve messages if genre / language specificed
			if ( ( !empty( $atts['genre'] ) ) && ( !empty( $atts['genre'] ) ) ) {
				$message = __( 'No Shows in the requested Genre and Language were found.', 'radio-station' );
			} elseif ( !empty( $atts['genre'] ) ) {
				$message = __( 'No Shows in the requested Genre were found.', 'radio-station' );
			} elseif ( !empty( $atts['language'] ) ) {
				$message = __( 'No Shows in the requested Language were found.', 'radio-station' );
			} else {
				$message = __( 'No Shows were found to display.', 'radio-station' );
			}
		} elseif ( RADIO_STATION_PLAYLIST_SLUG == $post_type ) {
			$message = __( 'No Playlists were found to display.', 'radio-station' );
		} elseif ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
			$message = __( 'No Overrides were found to display.', 'radio-station' );
		}

		// 2.3.3.9: filter message to allow for other possible types
		$message = apply_filters( 'radio_station_archive_shortcode_no_records', $message, $post_type, $atts );
		$list .= esc_html( $message );

	} else {

		// --- filter excerpt length and more ---
		$length = apply_filters( 'radio_station_archive_' . $type. '_list_excerpt_length', false );
		$more = apply_filters( 'radio_station_archive_' . $type . '_list_excerpt_more', '[&hellip;]' );

		// --- archive list ---
		$list .= '<ul class="' . esc_attr( $type ) . '-archive-list">';

		// --- set info keys ---
		// (note: meta is dates for overrides, shifts for shows, tracks for playlists etc.)
		$infokeys = array( 'avatar', 'title', 'meta', 'genres', 'languages', 'description', 'custom' );
		$infokeys = apply_filters( 'radio_station_archive_shortcode_info_order', $infokeys, $post_type, $atts );

		// --- loop post archive ---
		foreach ( $archive_posts as $archive_post ) {

			$info = array();

			// --- map archive data to variables ---
			// 2.3.3.9: added to allow overriding by override linked show data
			$post_id = $image_post_id = $archive_post->ID;
			$title = $archive_post->post_title;
			$permalink = get_permalink( $archive_post->ID );
			$post_content = $archive_post->post_content;
			$post_excerpt = $archive_post->post_excerpt;

			// --- check linked Show for overrides ---
			if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
				$linked_show = get_post_meta( $archive_post->ID, 'linked_show_id', true );
				if ( $linked_show ) {

					// --- overridc particular fields with linked show data ---
					$show_post = get_post( $linked_show );
					$show_fields = get_post_meta( $post_id, 'linked_show_fields', true );
					if ( !isset( $show_fields['show_title'] ) || !$show_fields['show_title'] ) {
						$title = $show_post->post_title;
					}
					if ( !isset( $show_fields['show_content'] ) || !$show_fields['show_content'] ) {
						$post_content = $show_post->post_content;
						$post_excerpt = $show_post->post_excerpt;
					}
					if ( !isset( $show_fields['show_avatar'] ) || !$show_fields['show_avatar'] ) {
						$image_post_id = get_post_meta( $linked_show, 'show_avatar', true );
					}
					
				}				
			}

			$list .= '<li class="' . esc_attr( $type ) . '-archive-item">';

			// --- show avatar or thumbnail fallback ---
			$info['avatar'] = '<div class="' . esc_attr( $type ) . '-archive-item-thumbnail">';
			$show_avatar = false;
			if ( $atts['show_avatars'] && in_array( $post_type, array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG ) ) ) {
				$attr = array( 'class' => esc_attr( $type ) . '-thumbnail-image' );
				$show_avatar = radio_station_get_show_avatar( $image_post_id, 'thumbnail', $attr );
			}
			if ( $show_avatar ) {
				$info['avatar'] .= $show_avatar;
			} elseif ( $atts['thumbnails'] ) {
				if ( has_post_thumbnail( $image_post_id ) ) {
					// 2.4.0.4: use attr not atts to prevent possible shortcode variable conflict
					$attr = array( 'class' => esc_attr( $type ) . '-thumbnail-image' );
					$thumbnail = get_the_post_thumbnail( $image_post_id, 'thumbnail', $attr );
					$info['avatar'] .= $thumbnail;
				}
			}
			$info['avatar'] .= '</div>';

			// --- title ----
			$info['title'] = '<div class="' . esc_attr( $type ) . '-archive-item-title">';
			$info['title'] .= '<a href="' . esc_url( $permalink ) . '">';
			$info['title'] .= esc_html( $title ) . '</a>';
			$info['title'] .= '</div>';

			// --- display Override date(s) ---
			if ( ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) && ( $atts['show_dates'] ) ) {

				// 2.3.3.9: set third attribute to false to allow for multiple override times
				$override_times = get_post_meta( $archive_post->ID, 'show_override_sched', true );
				// 2.3.3.9: convert possible single value to array
				if ( $override_times && is_array( $override_times ) && array_key_exists( 'date', $override_times ) ) {
					$override_times = array( $override_times );
				}

				// 2.3.1: fix to append not echo override date to archive list
				$info['meta'] = '<div class="override-archive-date">';

				foreach ( $override_times as $override_time ) {

					// 2.3.3.9: added check if override time/date is disabled
					if ( !isset( $override_time['disabled'] ) || ( 'yes' != $override_time['disabled'] ) ) {

						// --- convert date info ---
						// 2.3.2: replace strtotime with to_time for timezones
						// 2.3.2: fix to convert to 24 hour format first
						// $day = radio_station_get_time( 'day', $date_time );
						// $display_day = radio_station_translate_weekday( $day );
						$date_time = radio_station_to_time( $override_time['date'] );
						$start = $override_time['start_hour'] . ':' . $override_time['start_min'] . ' ' . $override_time['start_meridian'];
						$end = $override_time['end_hour'] . ':' . $override_time['end_min'] . ' ' . $override_time['end_meridian'];
						$start_time = radio_station_convert_shift_time( $start );
						$end_time = radio_station_convert_shift_time( $end );
						$shift_start_time = radio_station_to_time( $override_time['day'] . ' ' . $start_time );
						$shift_end_time = radio_station_to_time( $override_time['day'] . ' ' . $end_time );
						// 2.3.3.9: added or equals to operator
						if ( $shift_end_time <= $shift_start_time ) {
							$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
						}

						// --- convert shift times ---
						// 2.3.2: use time formats with translations
						$start = radio_station_get_time( $start_data_format, $shift_start_time );
						$end = radio_station_get_time( $end_data_format, $shift_end_time );
						$start = radio_station_translate_time( $start );
						$end = radio_station_translate_time( $end );

						// 2.3.1: fix to append not echo override date to archive list
						$info['meta'] .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>';
						$info['meta'] .= '<span class="rs-sep"> - </span>';
						$info['meta'] .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>';
						$info['meta'] .= '<br>';

					}
				}

				$info['meta'] .= '</div>';
			}

			// TODO: display Show shifts meta 
			// if ( RADIO_STATION_SHOW_SLUG == $post_type ) {
			//	if ( $atts['show_shifts'] ) {
			//		$shifts = radio_station_get_show_schedule( $post_id );
			//	}
			//	$info['meta'] = '';
			// }

			// TODO: playlist tracks / track count meta
			// if ( RADIO_STATION_PLAYLIST_SLUG == $post_type ) {
			// 	$tracks = get_post_meta( $post_id, 'playlist', true );
			//	$track_count = count( $tracks );
			//	$info['meta'] = '';
			// }
			
			// 2.3.3.9: filter meta display for different post types
			if ( !isset( $info['meta'] ) ) {
				$info['meta'] = '';
			}
			$info['meta'] = apply_filters ( 'radio_station_archive_shortcode_meta', $info['meta'], $post_id, $post_type, $atts );

			// TODO: display genre and language terms ?
			// if ( in_array( $post_type, array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG ) ) ) {
			//	if ( $atts['show_genres'] ) {
			//		$genres = wp_get_post_terms( $post_id, RADIO_STATION_GENRES_SLUG );
			//		$info['genres'] = '';
			//	}
			//	if ( $atts['show_languages'] ) {
			//		$languages = wp_get_post_terms( $post_id, RADIO_STATION_LANGUAGES_SLUG );
			//		$info['languages'] = '';
			//	}
			// }

			// --- description ---
			// 2.4.0.4: remove description for grid view
			if ( ( 'none' == $atts['description'] ) || ( 'grid' == $atts['view'] ) ) {
				$info['description'] = '';
			} elseif ( 'full' == $atts['description'] ) {
				$info['description'] = '<div class="' . esc_attr( $type ) . '-archive-item-content">';
				$content = apply_filters( 'radio_station_' . $type . '_archive_content', $post_content, $post_id );
				$info['description'] .= $content;
				$info['description'] .= '</div>';
			} else {
				$info['description'] = '<div class="' . esc_attr( $type ) . '-archive-item-excerpt">';
				if ( !empty( $post_excerpt ) ) {
					$excerpt = $post_excerpt;
					$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
				} else {
					$excerpt = radio_station_trim_excerpt( $post_content, $length, $more, $permalink );
				}
				$excerpt = apply_filters( 'radio_station_' . $type . '_archive_excerpt', $excerpt, $post_id );
				$info['description'] .= $excerpt;
				$info['description'] .= '</div>';
			}

			// 2.3.3.9: add filter for custom HTML info			
			$info['custom'] = apply_filters( 'radio_station_archive_shortcode_info_custom', '', $post_id, $post_type, $atts );

			// 2.3.3.9: filter info and loop info keys to add to archive list
			$info = apply_filters( 'radio_station_archive_shortcode_info', $info, $post_id, $post_type, $atts );
			foreach ( $infokeys as $infokey ) {
				if ( isset( $info[$infokey] ) ) {
					$list .= $info[$infokey];
				}				
			}

			$list .= '</li>';
		}
		$list .= '</ul>';
	}
	$list .= '</div>';

 	// --- add archive_pagination ---
	if ( $atts['pagination'] && ( $atts['perpage'] > 0 ) && ( $post_count > 0 ) ) {
		if ( $post_count > $atts['perpage'] ) {
			$list .= radio_station_archive_pagination( $post_type, $atts, $post_count );
		}
	}

	// --- enqueue pagination javascript ---
	add_action( 'wp_footer', 'radio_station_archive_pagination_javascript' );

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return  ---
	// 2.4.0.4: added third argument for post type
	$list = apply_filters( 'radio_station_' . $type . '_archive_list', $list, $atts, $post_type );

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

	// 2.3.3.9: default show description display to on
	$defaults = array(
		// --- genre display options ---
		'genres'       => '',
		'link_genres'  => 1,
		'genre_desc'   => 1,
		'genre_images' => 1,
		'image_width'  => 150,
		'hide_empty'   => 1,
		'pagination'   => 1,
		// --- query args ---
		'status'       => 'publish',
		'perpage'      => - 1,
		'offset'       => 0,
		'orderby'      => 'title',
		'order'        => 'ASC',
		'with_shifts'  => 1,
		// --- show display options ---
		'show_avatars' => 1,
		'thumbnails'   => 0,
		'avatar_width' => 75,
		'show_desc'    => 1,
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
		// TODO: also display Overrides in Genre archive list ?
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
					'key'		=> 'show_sched',
					'compare'	=> 'EXISTS',
				),
				array(
					'key'		=> 'show_active',
					'value'		=> 'on',
					'compare'	=> '=',
				),
			);

		} else {

			// --- just active shows ---
			$args['meta_query'] = array(
				array(
					'key'		=> 'show_active',
					'value'		=> 'on',
					'compare'	=> '=',
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

			// --- genre image ---
			$genre_image = apply_filters( 'radio_station_genre_image', false, $genre );
			if ( $genre_image ) {
				$width_style = '';
				if ( absint( $atts['image_width'] ) > 0 ) {
					$width_style = ' style="width: ' . esc_attr( absint( $atts['image_width'] ) ) . 'px"';
				}
				$list .= '<div class="genre-image-wrapper"' . $width_style . '>';
				$list .= $genre_image;
				$list .= '</div>';
			}

			// --- genre title ---
			$list .= '<div class="genre-title"><h3 class="genre-title">';
			if ( $atts['link_genres'] ) {
				$list .= '<a href="' . esc_url( $genre['url'] ) . '">' . $genre['name'] . '</a>';
			} else {
				$list .= $genre['name'];
			}
			$list .= '</h3></div>';

			// --- genre description ---
			if ( $atts['genre_desc'] && !empty( $genre['genre_desc'] ) ) {
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

			// --- filter excerpt length and more ---
			// 2.3.3.9: added for show description excerpts
			$length = apply_filters( 'radio_station_genre_archive_excerpt_length', false );
			$more = apply_filters( 'radio_station_genre_archive_excerpt_more', '[&hellip;]' );

			// --- show archive list ---
			$list .= '<ul class="show-archive-list">';

			foreach ( $posts as $post ) {
				$list .= '<li class="show-archive-item">';

				// --- show avatar or thumbnail fallback ---
				$width_style = '';
				if ( absint( $atts['avatar_width'] ) > 0 ) {
					$width_styles = ' style="width: ' . esc_attr( absint( $atts['avatar_width'] ) ) . 'px"';
				}
				$list .= '<div class="show-archive-item-thumbnail"' . $width_style . '>';
				$show_avatar = false;
				if ( $atts['show_avatars'] ) {
					$attr = array( 'class' => 'show-thumbnail-image' );
					$show_avatar = radio_station_get_show_avatar( $post->ID, 'thumbnail', $attr );
				}
				if ( $show_avatar ) {
					$list .= $show_avatar;
				} elseif ( $atts['thumbnails'] ) {
					if ( has_post_thumbnail( $post->ID ) ) {
						$attr = array( 'class' => 'show-thumbnail-image' );
						$thumbnail = get_the_post_thumbnail( $post->ID, 'thumbnail', $attr );
						$list .= $thumbnail;
					}
				}
				$list .= '</div>';

				// --- show title ----
				$permalink = get_permalink( $post->ID );
				$list .= '<div class="show-archive-item-title">';
				$list .= '<a href="' . esc_url( $permalink ) . '">';
				$list .= esc_attr( $post->post_title ) . '</a>';
				$list .= '</div>';

				// --- show excerpt ---
				// 2.2.3.9: display show description
				if ( $atts['show_desc' ] ) {
					if ( !empty( $post->post_excerpt ) ) {
						$excerpt = $post->post_excerpt;
						$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
					} else {
						$excerpt = radio_station_trim_excerpt( $post->post_content, $length, $more, $permalink );
					}
					$excerpt = apply_filters( 'radio_station_genre_archive_excerpt', $excerpt, $post->ID );

					if ( '' != $excerpt ) {
						$list .= '<div class="show-archive-item-description">';
						$list .= $excerpt;
						$list .= '</div>';
					}
				}

				$list .= '</li>';
			}
			$list .= '</ul>';
		}

		$list .= '</div>';
	}

	$list .= '</div>';

 	// --- add archive_pagination ---
 	// TODO: add genre archive list pagination
 	// if ( $atts['pagination'] ) {
	// 	$list .= radio_station_archive_pagination( 'genre', $atts, $post_count );
	// }

	// --- enqueue pagination javascript ---
	// add_action( 'wp_footer', 'radio_station_archive_pagination_javascript' );

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return ---
	$list = apply_filters( 'radio_station_genre_archive_list', $list, $atts );

	return $list;
}

// --------------------------
// Language Archive Shortcode
// --------------------------
// 2.3.3.9: add Languages Archive Shortcode
add_shortcode( 'language-archive', 'radio_station_language_archive_list' );
add_shortcode( 'languages-archive', 'radio_station_language_archive_list' );
function radio_station_language_archive_list( $atts ) {

	$defaults = array(
		// --- language display options ---
		'languages'       => '',
		'link_languages'  => 1,
		'language_desc'   => 1,
		'hide_empty'      => 1,
		'pagination'      => 1,
		// --- query args ---
		'status'          => 'publish',
		'perpage'         => - 1,
		'offset'          => 0,
		'orderby'         => 'title',
		'order'           => 'ASC',
		'with_shifts'     => 1,
		// --- show display options ---
		'show_avatars'    => 1,
		'thumbnails'      => 0,
		'avatar_width'    => 75,
		'show_desc'       => 1,
	);

	// --- handle possible pagination offset ---
	if ( isset( $atts['perpage'] ) && !isset( $atts['offset'] ) && get_query_var( 'page' ) ) {
		$page = absint( get_query_var( 'page' ) );
		if ( $page > - 1 ) {
			$atts['offset'] = (int) $atts['perpage'] * $page;
		}
	}
	$atts = shortcode_atts( $defaults, $atts, 'language-archive' );

	// --- maybe get specified language(s) ---
	if ( !empty( $atts['languages'] ) ) {
		$languages = explode( ',', $atts['languages'] );
		foreach ( $languages as $i => $language ) {
			$language = trim( $language );
			$language = radio_station_get_language( $language );
			if ( $language ) {
				$languages[$i] = $language;
			} else {
				unset( $language[$i] );
			}
		}
	} else {
		// --- get all languages ---
		$args = array();
		if ( !$atts['hide_empty'] ) {
			$args['hide_empty'] = false;
		}
		$languages = radio_station_get_language_terms( $args );
	}

	// --- check if we have languages ---
	if ( !$languages || ( count( $languages ) == 0 ) ) {
		if ( $atts['hide_empty'] ) {
			return '';
		} else {
			$list = '<div class="languages-archive">';
			$list .= esc_html( __( 'No Languages were found to display.', 'radio-station' ) );
			$list .= '</div>';

			return $list;
		}
	}

	$list = '<div class="languages-archive">';

	// --- loop languages ---
	foreach ( $languages as $name => $language ) {

		// --- get published shows ---
		// TODO: also display Overrides in archive list ?
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
					'key'		=> 'show_sched',
					'compare'	=> 'EXISTS',
				),
				array(
					'key'		=> 'show_active',
					'value'		=> 'on',
					'compare'	=> '=',
				),
			);

		} else {

			// --- just active shows ---
			$args['meta_query'] = array(
				array(
					'key'		=> 'show_active',
					'value'		=> 'on',
					'compare'	=> '=',
				),
			);
		}

		// --- set language taxonomy query ---
		$args['tax_query'] = array(
			array(
				'taxonomy' => RADIO_STATION_LANGUAGES_SLUG,
				'field'    => 'slug',
				'terms'    => $language['slug'],
			),
		);

		// --- get shows in language ---
		$args = apply_filters( 'radio_station_language_archive_post_args', $args );
		$posts = get_posts( $args );
		$posts = apply_filters( 'radio_station_language_archive_posts', $posts );

		$list .= '<div class="language-archive">';

		if ( $posts || ( count( $posts ) > 0 ) ) {
			$has_posts = true;
		} else {
			$has_posts = false;
		}
		if ( $has_posts || ( !$has_posts && !$atts['hide_empty'] ) ) {

			// --- language title ---
			$list .= '<div class="language-title"><h3 class="language-title">';
			if ( $atts['link_languages'] ) {
				$list .= '<a href="' . esc_url( $language['url'] ) . '">' . $language['name'] . '</a>';
			} else {
				$list .= $language['name'];
			}
			$list .= '</h3></div>';

			// --- language description ---
			if ( $atts['language_desc'] && !empty( $language['language_desc'] ) ) {
				$list .= '<div class="language-description">';
				$list .= $language['description'];
				$list .= '</div>';
			}

		}

		if ( !$has_posts ) {

			// --- no shows messages ----
			if ( !$atts['hide_empty'] ) {
				$list .= esc_html( __( 'No Shows in this Language.', 'radio-station' ) );
			}

		} else {

			// --- filter excerpt length and more ---
			$length = apply_filters( 'radio_station_language_archive_excerpt_length', false );
			$more = apply_filters( 'radio_station_language_archive_excerpt_more', '[&hellip;]' );

			// --- show archive list ---
			$list .= '<ul class="show-archive-list">';

			foreach ( $posts as $post ) {
				$list .= '<li class="show-archive-item">';

				// --- show avatar or thumbnail fallback ---
				$width_style = '';
				if ( absint( $atts['avatar_width'] ) > 0 ) {
					$width_styles = ' style="width: ' . esc_attr( absint( $atts['avatar_width'] ) ) . 'px"';
				}
				$list .= '<div class="show-archive-item-thumbnail"' . $width_style . '>';
				$show_avatar = false;
				if ( $atts['show_avatars'] ) {
					$attr = array( 'class' => 'show-thumbnail-image' );
					$show_avatar = radio_station_get_show_avatar( $post->ID, 'thumbnail', $attr );
				}
				if ( $show_avatar ) {
					$list .= $show_avatar;
				} elseif ( $atts['thumbnails'] ) {
					if ( has_post_thumbnail( $post->ID ) ) {
						$attr = array( 'class' => 'show-thumbnail-image' );
						$thumbnail = get_the_post_thumbnail( $post->ID, 'thumbnail', $attr );
						$list .= $thumbnail;
					}
				}
				$list .= '</div>';

				// --- show title ----
				$permalink = get_permalink( $post->ID );
				$list .= '<div class="show-archive-item-title">';
				$list .= '<a href="' . esc_url( $permalink ) . '">';
				$list .= esc_attr( $post->post_title ) . '</a>';
				$list .= '</div>';

				// --- show excerpt ---
				if ( $atts['show_desc' ] ) {
					if ( !empty( $post->post_excerpt ) ) {
						$excerpt = $post->post_excerpt;
						$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
					} else {
						$excerpt = radio_station_trim_excerpt( $post->post_content, $length, $more, $permalink );
					}
					$excerpt = apply_filters( 'radio_station_genre_archive_excerpt', $excerpt, $post->ID );

					if ( '' != $excerpt ) {
						$list .= '<div class="show-archive-item-description">';
						$list .= $excerpt;
						$list .= '</div>';
					}
				}

				$list .= '</li>';
			}
			$list .= '</ul>';
		}

		$list .= '</div>';
	}

	$list .= '</div>';

 	// --- add archive_pagination ---
 	// TODO: add language archive list pagination
	// if ( $atts['pagination'] ) {
	// 	$list .= radio_station_archive_pagination( 'language', $atts, $post_count );
	// }

	// --- enqueue pagination javascript ---
	// add_action( 'wp_footer', 'radio_station_archive_pagination_javascript' );

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return ---
	$list = apply_filters( 'radio_station_language_archive_list', $list, $atts );

	return $list;
}

// ------------------
// Archive Pagination
// ------------------
function radio_station_archive_pagination( $type, $atts, $post_count ) {

	global $post;
	$permalink = get_permalink( $post->ID );
	$post_pages = ceil( $post_count / $atts['perpage'] );
	if ( $atts['offset'] > 0 ) {
		$current_page = $atts['offset'] / $atts['perpage'];
	} else {
		$current_page = 0;
	}
	$prev_page = $current_page - 1;
	$next_page = $current_page + 1;

	$pagi = '<br><br>';
	$pagi .= '<div class="archive-' . esc_attr( $type ) . 's-page-buttons">';
		$url = add_query_arg( 'page', $prev_page, $permalink );
		$pagi .= '<div class="archive-pagination-button">';
		if ( $prev_page > 0 ) {
			$pagi .= '<a href="' . esc_url( $url ) . '">&larr;</a>';
		}
		$pagi .= '</div>';
		for ( $pagenum = 1; $pagenum < ( $post_pages + 1 ); $pagenum ++ ) {
			if ( $current_page == $pagenum ) {
				$active = ' active';
			} else {
				$active = '';
			}
			$pagi .= '<div class="archive-' . esc_attr( $type ) . 's-page-button archive-pagination-button' . esc_attr( $active ) . '">';
			$url = add_query_arg( 'page', $pagenum, $permalink );
			$pagi .= '<a href="' . esc_url( $url ) . '">';
			$pagi .= esc_html( $pagenum );
			$pagi .= '</a>';
			$pagi .= '</div>';
		}
		$url = add_query_arg( 'page', $next_page, $permalink );
		$pagi .= '<div class="archive-pagination-button">';
		$pagi .= '<a href="' . esc_url( $url ) . '">&rarr;</a>';
		$pagi .= '</div>';
	$pagi .= '</div>';

	return $pagi;
}

// -----------------------------
// Archive Pagination Javascript
// -----------------------------
// 2.3.3.9: renamed function to distinguish from list pagination
function radio_station_archive_pagination_javascript() {

	// TEMP
	return;

	// --- fade out current page and fade in selected page ---
	$js = "function radio_archive_page(id, types, pagenum) {
		currentpage = document.getElementById('archive-'+id+'-'+types+'-current-page').value;
		if (pagenum == 'next') {
			pagenum = parseInt(currentpage) + 1;
			pagecount = document.getElementById('archive-'+id+'-'+types+'-page-count').value;
			if (pagenum > pagecount) {return;}
		}
		if (pagenum == 'prev') {
			pagenum = parseInt(currentpage) - 1;
			if (pagenum < 1) {return;}
		}
		if (typeof jQuery == 'function') {
			jQuery('.archive-'+id+'-'+types+'-page').fadeOut(500);
			jQuery('#archive-'+id+'-'+types+'-page-'+pagenum).fadeIn(1000);
			jQuery('.archive-'+id+'-'+types+'-page-button').removeClass('active');
			jQuery('#archive-'+id+'-'+types+'-page-button-'+pagenum).addClass('active');
			jQuery('#archive-'+id+'-'+types+'-current-page').val(pagenum);
		} else {
			pages = document.getElementsByClassName('archive-'+id+'-'+types+'-page');
			for (i = 0; i < pages.length; i++) {pages[i].style.display = 'none';}
			document.getElementById('archive-'+id+'-'+types+'-page-'+pagenum).style.display = '';
			buttons = document.getElementsByClassName('archive-'+id+'-'+types+'-page-button');
			for (i = 0; i < buttons.length; i++) {buttons[i].classList.remove('active');}
			document.getElementById('archive-'+id+'-'+types+'-page-button-'+pagenum).classList.add('active');
			document.getElementById('archive-'+id+'-'+types+'-current-page').value = pagenum;
		}
	}";

	// --- enqueue script inline ---
	wp_add_inline_script( 'radio-station', $js );
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
		'show'       => false,
		'per_page'   => 15,
		'limit'      => 0,
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

		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Stored Show Posts (' . $type . '):' . print_r( $posts, true ) . '</span>';
		}

	} else {
		// --- check for show ID (required at minimum) ---
		if ( !$atts['show'] ) {
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
		// 2.3.3.9: also handle host or producer lists
		$args = array();
		if ( isset( $atts['limit'] ) ) {
			$args['limit'] = $atts['limit'];
		}
		if ( 'post' == $type ) {
			$posts = radio_station_get_show_posts( $show_id, $args );
		} elseif ( RADIO_STATION_PLAYLIST_SLUG == $type ) {
			$posts = radio_station_get_show_playlists( $show_id, $args );
			$type = 'playlist';
		} elseif ( RADIO_STATION_HOST_SLUG == $type ) {
			$posts = apply_filters( 'radio_station_get_show_hosts', false, $show_id, $args );
			$type = 'host';
		} elseif ( RADIO_STATION_PRODUCER_SLUG == $type ) {
			$posts = apply_filters( 'radio_station_get_show_producers', false, $show_id, $args );
			$type = 'producer';
		} elseif ( defined( 'RADIO_STATION_EPISODE_SLUG' ) && ( RADIO_STATION_EPISODE_SLUG == $type ) ) {
			$posts = apply_filters( 'radio_station_get_show_episodes', false, $show_id, $args );			
			$type = 'episode';
		}
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Show Posts (' . $type . '):' . print_r( $posts, true ) . '</span>';
		}
	}
	if ( !isset( $posts ) || !$posts || !is_array( $posts ) || ( count( $posts ) == 0 ) )  {
		return '';
	}

	// --- filter excerpt length and more ---
	$length = apply_filters( 'radio_station_show_' . $type . '_list_excerpt_length', false );
	$more = apply_filters( 'radio_station_show_' . $type . '_list_excerpt_more', '[&hellip;]' );

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

		// 2.3.3.9: check if this object is instance of WP_User class
		if ( is_a( $post, 'WP_User' ) ) {
		
			// --- this is a user without a profile post ---
			$user = $post;
			$user_id = $user->data->ID;

			// TODO: check for user avatar ?
		
			$list .= '<div class="show-' . esc_attr( $type ) . '-info">';
		
			// --- link to author page ---
			$list .= '<div class="show-' . esc_attr( $type ) . '-title">';
			$permalink = get_author_posts_url( $user_id );
			$title = __( 'View all posts by %s', 'radio-station' );
			$title = sprintf( $title, $user->display_name );
			$list .= '<a href="' . esc_url( $permalink ) . '" title="' . esc_attr( $title ) . '">';
			$list .= esc_attr( $user->display_name );
			$list .= '</a>';
			$list .= '</div>';

			// --- author bio/excerpt ---
			$userdata = get_user_meta( $user_id );
			$bio_content = $userdata->description[0];
			if ( 'none' == $atts['content'] ) {
				$list .= '';
			} elseif ( 'full' == $atts['content'] ) {
				$list .= '<div class="show-' . esc_attr( $type ) . '-content">';
				$content = apply_filters( 'radio_station_show_' . $type . '_content', $bio_content, $user_id );
				$list .= $bio_content;
				$list .= '</div>';
			} else {
				$list .= '<div class="show-' . esc_attr( $type ) . '-excerpt">';
				$permalink = get_author_posts_url( $user_id );
				$excerpt = radio_station_trim_excerpt( $bio_content, $length, $more, $permalink );
				$excerpt = apply_filters( 'radio_station_show_' . $type . '_excerpt', $excerpt, $user_id );
				$list .= $excerpt;
				$list .= '</div>';
			}

		
			$list .= '</div>';
		
		} else {

			// --- post thumbnail ---
			if ( $atts['thumbnails'] ) {
				$thumbnail = false;	
				$has_thumbnail = has_post_thumbnail( $post['ID'] );
				if ( $has_thumbnail ) {
					$attr = array( 'class' => 'show-' . esc_attr( $type ) . '-thumbnail-image' );
					$thumbnail = get_the_post_thumbnail( $post['ID'], 'thumbnail', $attr );
				}
				$thumbnail = apply_filters( 'radio_station_show_list_archive_avatar', $thumbnail, $post['ID'], $type );
				if ( $thumbnail ) {
					$list .= '<div class="show-' . esc_attr( $type ) . '-thumbnail">' . $thumbnail . '</div>';
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
			$post_content = $post['post_content'];
			$post_id = $post['ID'];
			if ( 'none' == $atts['content'] ) {
				$list .= '';
			} elseif ( 'full' == $atts['content'] ) {
				$list .= '<div class="show-' . esc_attr( $type ) . '-content">';
				$content = apply_filters( 'radio_station_show_' . $type . '_content', $post_content, $post_id );
				$list .= $content;
				$list .= '</div>';
			} else {
				$list .= '<div class="show-' . esc_attr( $type ) . '-excerpt">';
				$permalink = get_permalink( $post['ID'] );
				if ( !empty( $post['post_excerpt'] ) ) {
					$excerpt = $post['post_excerpt'];
					$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
				} else {
					$excerpt = radio_station_trim_excerpt( $post_content, $length, $more, $permalink );
				}
				$excerpt = apply_filters( 'radio_station_show_' . $type . '_excerpt', $excerpt, $post_id );
				$list .= $excerpt;
				$list .= '</div>';
			}

			$list .= '</div>';
		}

		// --- close item div ---
		$list .= '</div>';
		$j ++;
	}

	// --- close last page div ---
	$list .= '</div>';

	// --- list pagination ---
	// 2.3.3.9: fix to hide left arrow display on load
	if ( $atts['pagination'] && ( $post_pages > 1 ) ) {
		$list .= '<br><br>';
		$list .= '<div id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page-buttons" class="show-' . esc_attr( $type ) . 's-page-buttons">';
		$list .= '<div id="show-' . esc_attr( $type ) . 's-pagination-button-left" class="show-pagination-button arrow-button-left arrow-button" onclick="radio_list_page(\'show\', ' . esc_js( $show_id ) . ', \'' . esc_js( $type ) . 's\', \'prev\');" style="display:none;">';
		$list .= '<a href="javascript:void(0);">&larr;</a>';
		$list .= '</div>';
		for ( $pagenum = 1; $pagenum < ( $post_pages + 1 ); $pagenum ++ ) {
			if ( 1 == $pagenum ) {
				$active = ' active';
			} else {
				$active = '';
			}
			$onclick = 'radio_list_page(\'show\', ' . esc_js( $show_id ) . ', \'' . esc_js( $type ) . 's\', ' . esc_js( $pagenum ) . ');';
			$list .= '<div id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ). 's-page-button-' . esc_attr( $pagenum ) . '" class="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page-button show-pagination-button' . esc_attr( $active ) . '" onclick="' . $onclick . '">';
			$list .= '<a href="javascript:void(0);">';
			$list .= esc_html( $pagenum );
			$list .= '</a>';
			$list .= '</div>';
		}
		$list .= '<div id="show-' . esc_attr( $type ) . 's-pagination-button-right" class="show-pagination-button arrow-button" onclick="radio_list_page(\'show\', ' . esc_js( $show_id ) . ', \'' . esc_js( $type ). 's\', \'next\');">';
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
	add_action( 'wp_footer', 'radio_station_list_pagination_javascript' );

	// --- filter and return ---
	$list = apply_filters( 'radio_station_show_' . $type . '_list', $list, $atts );

	return $list;
}

// ----------------------------
// Show Posts Archive Shortcode
// ----------------------------
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
// 2.3.3.9: renamed function to distinguish from archive pagination
function radio_station_list_pagination_javascript() {

	// --- fade out current page and fade in selected page ---
	// 2.3.3.9: added selector prefix as argument
	// 2.3.3.9: fix to conditional arrow displays
	$js = "function radio_list_page(prefix, id, types, pagenum) {
		currentpage = document.getElementById(prefix+'-'+id+'-'+types+'-current-page').value;
		pagecount = document.getElementById(prefix+'-'+id+'-'+types+'-page-count').value;
		if (pagenum == 'next') {
			pagenum = parseInt(currentpage) + 1;	
			if (pagenum > pagecount) {return;}
		}
		if (pagenum == 'prev') {
			pagenum = parseInt(currentpage) - 1;
			if (pagenum < 1) {return;}
		}
		if (typeof jQuery == 'function') {
			jQuery('.'+prefix+'-'+id+'-'+types+'-page').fadeOut(500);
			jQuery('#'+prefix+'-'+id+'-'+types+'-page-'+pagenum).fadeIn(1000);
			jQuery('.'+prefix+'-'+id+'-'+types+'-page-button').removeClass('active');
			jQuery('#'+prefix+'-'+id+'-'+types+'-page-button-'+pagenum).addClass('active');
			jQuery('#'+prefix+'-'+id+'-'+types+'-current-page').val(pagenum);
		} else {
			pages = document.getElementsByClassName(prefix+'-'+id+'-'+types+'-page');
			for (i = 0; i < pages.length; i++) {pages[i].style.display = 'none';}
			document.getElementById(prefix+'-'+id+'-'+types+'-page-'+pagenum).style.display = '';
			buttons = document.getElementsByClassName(prefix+'-'+id+'-'+types+'-page-button');
			for (i = 0; i < buttons.length; i++) {buttons[i].classList.remove('active');}
			document.getElementById(prefix+'-'+id+'-'+types+'-page-button-'+pagenum).classList.add('active');
			document.getElementById(prefix+'-'+id+'-'+types+'-current-page').value = pagenum;
		}
		larrow = document.getElementById(prefix+'-'+types+'-pagination-button-left');
		rarrow = document.getElementById(prefix+'-'+types+'-pagination-button-right');
		larrow.style.display = ''; rarrow.style.display = '';
		if (pagenum == 1) {larrow.style.display = 'none';}
		else if (pagenum == pagecount) {rarrow.style.display = 'none';}
		console.log(pagenum+' - '+pagecount);
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

	// --- set widget instance ID ---
	// 2.3.2: added for AJAX loading
	if ( !isset( $radio_station_data['current_show_instance'] ) ) {
		$radio_station_data['current_show_instance'] = 0;
	}
	$radio_station_data['current_show_instance']++;

	$output = '';

	// 2.3.2: get default AJAX load settings
	$ajax = radio_station_get_setting( 'ajax_widgets', false );
	$ajax =  ( 'yes' == $ajax ) ? 'on' : 'off';

	// --- apply filters for dynamic reload value ---
	$dynamic = apply_filters( 'radio_station_current_show_dynamic', false, $atts );
	$dynamic = $dynamic ? 1 : 0;

	// 2.3.3: use plugin setting if time format attribute is empty
	if ( isset( $atts['time'] ) && ( '' == $atts['time'] ) ) {
		unset( $atts['time'] );
	}

	// --- get shortcode attributes ---
	// 2.3.0: set default default_name text
	// 2.3.0: set default time format to plugin setting
	// 2.3.2: added AJAX load attribute
	// 2.3.2: added for_time attribute
	// 2.3.3.8: added show_encore attribute (default 1)
	// 2.3.3.9: added avatar_size attribute (default thumbnail)
	// 2.3.3.9: added show_title attribute (default 1)
	$time_format = radio_station_get_setting( 'clock_time_format' );
	$defaults = array(
		// --- legacy options ---
		'title'          => '',
		'display_hosts'  => 0,
		'show_avatar'    => 1,
		'show_title'     => 1,
		'show_link'      => 1,
		'default_name'   => '',
		'time'           => $time_format,
		'show_sched'     => 1,
		'show_playlist'  => 1,
		'show_all_sched' => 0,
		'show_desc'      => 0,
		// --- new options ---
		// 'display_producers' => 0,
		'avatar_width'   => '',
		'avatar_size'    => 'thumbnail',
		'title_position' => 'right',
		'link_hosts'     => 0,
		'show_encore'    => 1,
		'countdown'      => 0,
		'ajax'           => $ajax,
		'dynamic'        => $dynamic,
		'widget'         => 0,
		'id'             => '',
		'for_time'       => 0,
	);
	// 2.3.0: convert old attributes for DJs to hosts
	if ( isset( $atts['display_djs'] ) && !isset( $atts['display_hosts'] ) ) {
		$atts['display_hosts'] = $atts['display_djs'];
		unset( $atts['display_djs'] );
	}
	if ( isset( $atts['link_djs'] ) && !isset( $atts['link_hosts'] ) ) {
		$atts['link_hosts'] = $atts['link_djs'];
		unset( $atts['link_djs'] );
	}
	// 2.3.0: renamed shortcode identifier to current-show
	$atts = shortcode_atts( $defaults, $atts, 'current-show' );

	// 2.3.2: enqueue countdown script earlier
	if ( $atts['countdown'] ) {
		do_action( 'radio_station_countdown_enqueue' );
	}

	// 2.3.3: add current time override for manual testing
	if ( isset( $_GET['date'] ) && isset( $_GET['time'] ) ) {
		$date = trim( $_GET['date'] );
		$time = trim( $_GET['time'] );
		if ( isset( $_GET['month'] ) ) {
			$month = absint( trim( $_GET['month'] ) );
		} else {
			$month = radio_station_get_time( 'm' );
		}
		if ( isset( $_GET['year'] ) ) {
			$year = absint( trim( $_GET['year'] ) );
		} else {
			$year = radio_station_get_time( 'Y' );
		}
		if ( strstr( $time, ':' ) && ( $month > 0 ) && ( $month < 13 ) ) {
			$parts = explode( ':', $time );
			$time = absint( $parts[0] ) . ':' . absint( $parts[1] );
			$for_time = radio_station_to_time( $year . '-' . $month . '-' . $date . ' ' . $time );
			$atts['for_time'] = $for_time;
			echo "<script>console.log('Override Current Time: " . $for_time . "');</script>";
		}
	}

	// --- maybe do AJAX load ---
	// 2.3.2 added widget AJAX loading
	$ajax = $atts['ajax'];
	$widget = $atts['widget'];
	$ajax = apply_filters( 'radio_station_widgets_ajax_override', $ajax, 'current-show', $widget );
	if ( 'on' == $ajax ) {
		if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) {

			// --- AJAX load via iframe ---
			$ajax_url = admin_url( 'admin-ajax.php' );
			$instance = $radio_station_data['current_show_instance'];
			$html = '<div id="rs-current-show-' . esc_attr( $instance ) . '" class="ajax-widget"></div>';
			$html .= '<iframe id="rs-current-show-' . esc_attr( $instance ) . '-loader" src="javascript:void(0);" style="display:none;"></iframe>';
			$html .= "<script>timestamp = Math.floor( (new Date()).getTime() / 1000 );
			url = '" . esc_url( $ajax_url ) . "?action=radio_station_current_show';
			url += '&instance=" . esc_attr( $instance ) . "&timestamp='+timestamp;";
			if ( RADIO_STATION_DEBUG ) {
				$html .= "url += '&rs-debug=1';";
			}
			$html .= "url += '";
			foreach ( $atts as $key => $value ) {
				$value = radio_station_encode_uri_component( $value );
				$html .= "&" . esc_js( $key ) . "=" . esc_js( $value );
			}
			$html .= "'; ";
			$html .= "document.getElementById('rs-current-show-" . esc_attr( $instance ) ."-loader').src = url;";
			$html .= "</script>";

			// --- enqueue shortcode styles ---
			radio_station_enqueue_style( 'shortcodes' );

			return $html;
		}
	}

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

	// --- maybe filter excerpt values ---
	// 2.3.0: added context specific excerpt value filtering
	if ( $atts['show_desc'] ) {
		if ( $atts['widget'] ) {
			$length = apply_filters( 'radio_station_current_show_widget_excerpt_length', false );
			$more = apply_filters( 'radio_station_current_show_widget_excerpt_more', '[&hellip;]' );
		} else {
			$length = apply_filters( 'radio_station_current_show_shortcode_excerpt_length', false );
			$more = apply_filters( 'radio_station_current_show_shortcode_excerpt_more', '[&hellip;]' );
		}
	}

	// --- get current show ---
	// note: current show is not split shift
	// 2.3.0: use new get current show function
	// 2.3.2: added attribute to pass time argument
	if ( $atts['for_time'] ) {
		$current_shift = radio_station_get_current_show( $atts['for_time'] );
		$time = radio_station_get_time( 'datetime', $atts['for_time'] );
		echo '<span style="display:none;">';
			echo 'Current Shift For Time: ' . $atts['for_time'] . ' : ' . $time . PHP_EOL;
			echo print_r( $current_shift, true ) . PHP_EOL;
		echo '</span>';
	} else {
		$current_shift = radio_station_get_current_show();
	}

	// --- open shortcode div wrapper ---
	if ( !$atts['widget'] ) {

		// 2.3.0: add unique id to widget shortcode
		// 2.3.2: add shortcode wrap class
		if ( !isset( $radio_station_data['widgets']['current-show'] ) ) {
			$radio_station_data['widgets']['current-show'] = 0;
		} else {
			$radio_station_data['widgets']['current-show']++;
		}
		$id = 'current-show-widget-' . $radio_station_data['widgets']['current-show'];
		$output .= '<div id="' . esc_attr( $id ) . '" class="current-show-wrap current-show-embedded on-air-embedded dj-on-air-embedded">';

		// --- shortcode only title ---
		if ( !empty( $atts['title'] ) ) {
			// 2.3.3.9: fix class to not conflict with actual show title
			$output .= '<h3 class="current-show-shortcode-title">';
			$output .= esc_html( $atts['title'] );
			$output .= '</h3>';
		}
	}


	// --- open current show list ---
	$output .= '<ul class="current-show-list on-air-list">';

	// --- current shift display ---
	if ( $current_shift ) {

		// --- get time formats ---
		// 2.3.2: moved out to get once
		if ( 24 == (int) $atts['time'] ) {
			$start_data_format = $end_data_format = 'H:i';
		} else {
			$start_data_format = $end_data_format = 'g:i a';
		}
		$start_data_format = 'l, ' . $start_data_format;
		$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'current-show', $atts );
		$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'current-show', $atts );

		// --- set html output ---
		// 2.3.1: store all HTML to allow section re-ordering
		$html = array( 'title' => '' );

		// --- set current show data ---
		$show = $current_shift['show'];
		$show_id = $show['id'];

		// --- get show link ---
		$show_link = false;
		if ( $atts['show_link'] ) {
			$show_link = get_permalink( $show_id );
			$show_link = apply_filters( 'radio_station_current_show_link', $show_link, $show_id, $atts );
		}

		// --- check show schedule ---
		// 2.3.1: check early for later display
		if ( $atts['show_sched'] || $atts['show_all_sched'] ) {

			$shift_display = '<div class="current-show-schedule on-air-dj-schedule">';

			// --- show times subheading ---
			// 2.3.0: added for all shift display
			if ( $atts['show_all_sched'] ) {
				$shift_display .= '<div class="current-show-schedule-title on-air-dj-schedule-title">';
				$shift_display .= esc_html( __( 'Show Times', 'radio-station' ) );
				$shift_display .= '</div>';
			}

			// --- maybe show all shifts ---
			// (only if not a schedule override)
			// 2.3.2: fix to override variable key check
			if ( !isset( $current_shift['override'] ) && $atts['show_all_sched'] ) {
				$shifts = radio_station_get_show_schedule( $show_id );
			} else {
				$shifts = array( $current_shift );
			}

			// --- get weekdates ---
			// 2.3.0: use dates for reliability
			if ( $atts['for_time'] ) {
				$now = $atts['for_time'];
			} else {
				$now = radio_station_get_now();
			}
			$today = radio_station_get_time( 'l', $now );
			$yesterday = radio_station_get_previous_day( $today );
			$weekdays = radio_station_get_schedule_weekdays( $yesterday );
			$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now );

			foreach ( $shifts as $i => $shift ) {

				// --- set shift start and end ---
				if ( isset( $shift['real_start'] ) ) {
					$start = $shift['real_start'];
				} elseif ( isset( $shift['start'] ) ) {
					$start = $shift['start'];
				} else {
					$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
				}
				if ( isset( $shift['real_end'] ) ) {
					$end = $shift['real_end'];
				} elseif ( isset( $shift['end'] ) ) {
					$end = $shift['end'];
				} else {
					$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
				}

				// --- convert shift info ---
				// 2.2.2: translate weekday for display
				// 2.3.0: use dates for reliability
				// 2.3.2: replace strtotime with to_time for timezones
				// 2.3.2: fix to conver to 24 hour format first
				$start_time = radio_station_convert_shift_time( $start );
				$end_time = radio_station_convert_shift_time( $end );
				if ( isset( $shift['real_start'] ) ) {
					$prevday = radio_station_get_previous_day( $shift['day'] );
					$shift_start_time = radio_station_to_time( $weekdates[$prevday] . ' ' . $start_time );
				} else {
					$shift_start_time = radio_station_to_time( $weekdates[$shift['day']] . ' ' . $start_time );
				}
				$shift_end_time = radio_station_to_time( $weekdates[$shift['day']] . ' ' . $end_time );
				// 2.3.3.9: added or equals to operator
				if ( $shift_end_time <= $shift_start_time ) {
					$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
				}

				// --- get shift display times ---
				// 2.3.2: use time formats with translations
				// $display_day = radio_station_translate_weekday( $shift['day'] );
				$start = radio_station_get_time( $start_data_format, $shift_start_time );
				$end = radio_station_get_time( $end_data_format, $shift_end_time );
				$start = radio_station_translate_time( $start );
				$end = radio_station_translate_time( $end );

				// --- set shift classes ---
				$classes = array( 'current-show-shifts', 'on-air-dj-sched' );
				if ( ( $now > $shift_start_time ) && ( $now < $shift_end_time ) ) {
					$current_shift_start = $shift_start_time;
					$current_shift_end = $shift_end_time;
					$classes[] = 'current-shift';
					$class = implode( ' ', $classes );

					$current_shift_display = '<div class="' . esc_attr( $class ) . '">';
					$current_shift_display .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>';
					$current_shift_display .= '<span class="rs-sep"> - </span>';
					$current_shift_display .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>';
					$current_shift_display .= '</div>';

					// 2.3.3.9: add show user time div
					$current_shift_display .= '<div class="show-user-time">';
					$current_shift_display .= '[<span class="rs-user-time rs-start-time"></span>';
					$current_shift_display .= '<span class="rs-sep"> - </span>';
					$current_shift_display .= '<span class="rs-user-time rs-end-time"></span>]';
					$current_shift_display .= '</div>';
					
				}
				$class = implode( ' ', $classes );

				// --- shift display output ---
				$shift_display .= '<div class="' . esc_attr( $class ) . '">';
				if ( in_array( 'current-shift', $classes ) ) {
					// (this highlights the current shift item in the full schedule list)
					$shift_display .= '<ul class="current-shift-list"><li class="current-shift-list-item">';
				}
				$shift_display .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>';
				$shift_display .= '<span class="rs-sep"> - </span>';
				$shift_display .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>';

				// 2.3.3.9: add show user time div
				$shift_display .= '<div class="show-user-time">';
				$shift_display .= '[<span class="rs-user-time rs-start-time"></span>';
				$shift_display .= '<span class="rs-sep"> - </span>';
				$shift_display .= '<span class="rs-user-time rs-end-time"></span>]';
				$shift_display .= '</div>';

				if ( in_array( 'current-shift', $classes ) ) {
					$shift_display .= '</li></ul>';
				}
				$shift_display .= '</div>';
			}

			$shift_display .= '</div>';
		}

		// --- set clear div ---
		$html['clear'] = '<span class="radio-clear"></span>';

		// --- set show title output ---
		// 2.3.3.9: adding show title attribute
		if ( $atts['show_title'] ) {
			$title = '<div class="current-show-title on-air-dj-title">';
			if ( $show_link ) {
				$title .= '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
			} else {
				$title .= esc_html( $show['name'] );
			}
			$title .= '</div>';
			// 2.3.3.8: added current show title filter
			$title = apply_filters( 'radio_station_current_show_title_display', $title, $show_id, $atts );
			if ( ( '' != $title ) && is_string( $title ) ) {
				$html['title'] = $title;
			}
		}

		// --- show avatar ---
		if ( $atts['show_avatar'] ) {

			// 2.3.0: get show avatar (with thumbnail fallback)
			// 2.3.0: filter show avatar via display context
			// 2.3.0: maybe add link from avatar to show
			// 2.3.3.9: allow for possible avatar size attribute/filter
			$avatar = '';
			$avatar_size = apply_filters( 'radio_station_current_show_avatar_size', $atts['avatar_size'], $show_id );
			$show_avatar = radio_station_get_show_avatar( $show_id, $avatar_size );
			$show_avatar = apply_filters( 'radio_station_current_show_avatar', $show_avatar, $show_id, $atts );
			if ( $show_avatar ) {
				$avatar = '<div class="current-show-avatar on-air-dj-avatar' . esc_attr( $floatclass ) . '" ' . $widthstyle . '>';
				if ( $show_link ) {
					$avatar .= '<a href="' . esc_url( $show_link ) . '">' . $show_avatar . '</a>';
				} else {
					$avatar .= $show_avatar;
				}
				$avatar .= '</div>';
			}
			// 2.3.3.8: added avatar display filter
			// 2.3.3.9: moved filter outside of conditional
			$avatar = apply_filters( 'radio_station_current_show_avatar_display', $avatar, $show_id, $atts );
			if ( ( '' != $avatar ) && is_string( $avatar ) ) {
				$html['avatar'] = $avatar;
			}
		}

		// --- show DJs / hosts ---
		if ( $atts['display_hosts'] ) {

			$hosts = '';
			$show_hosts = get_post_meta( $show_id, 'show_user_list', true );
			if ( $show_hosts ) {
				// 2.4.0.4: convert possible (old) non-array value
				if ( !is_array( $show_hosts ) ) {
					$show_hosts = array( $show_hosts );
				}
				if ( is_array( $show_hosts ) && ( count( $show_hosts ) > 0 ) ) {

					$hosts = '<div class="current-show-hosts on-air-dj-names">';

					$hosts .= esc_html( __( 'with', 'radio-station' ) ) . ' ';

					$count = 0;
					// 2.3.3.9: fix to host count
					$host_count = count( $show_hosts );
					foreach ( $show_hosts as $host ) {

						$count ++;

						// 2.3.0: maybe get stored user data
						// $user = get_userdata( $host );
						if ( isset( $radio_station_data['user-' . $host] ) ) {
							$user = $radio_station_data['user-' . $host];
						} else {
							$user = get_user_by( 'ID', $host );
							$radio_station_data['user-' . $host] = $user;
						}

						if ( $atts['link_hosts'] ) {
							// 2.3.0: use new get host URL function
							$host_link = radio_station_get_host_url( $host );
							$host_link = apply_filters( 'radio_station_dj_link', $host_link, $host );

							// 2.3.3.5: only wrap with tags if there is a link
							if ( $host_link ) {
								$hosts .= '<a href="' . esc_url( $host_link ) . '">';
							}
							$hosts .= esc_html( $user->display_name );
							if ( $host_link ) {
								$hosts .= '</a>';
							}
						} else {
							$hosts .= esc_html( $user->display_name );
						}

						if ( ( ( 1 == $count ) && ( 2 == $host_count ) )
							 || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) == $count ) ) ) {
							$hosts .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
						} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
							$hosts .= ', ';
						}
					}
					$hosts .= '</div>';
				}
				$hosts = apply_filters( 'radio_station_current_show_hosts_display', $hosts, $show_id, $atts );
				if ( ( '' != $hosts ) && is_string( $hosts ) ) {
					$html['hosts'] = $hosts;
				}
			}
		}

		// --- output current shift display ---
		if ( $atts['show_sched'] && isset( $current_shift_display ) ) {
			$html['shift'] = $current_shift_display;
		}

		// --- encore presentation ---
		// 2.3.0: added encore presentation display
		// 2.3.3.8: added shortcode attribute check (with default 1)
		if ( $atts['show_encore'] ) {
			$encore = '';
			if ( isset( $show['encore'] ) && ( $show['encore'] ) ) {
				$encore = '<div class="current-show-encore on-air-dj-encore">';
				$encore .= esc_html( __( 'Encore Presentation', 'radio-station' ) );
				$encore .= '</div>';
			}
			// 2.3.3.8: added encore display filter
			$encore = apply_filters( 'radio_station_current_show_encore_display', $encore, $show_id, $atts );
			if ( ( '' != $encore ) && is_string( $encore ) ) {
				$html['encore'] = $encore;
			}
		}

		// --- current show playlist ---
		// 2.3.0: convert span to div tags for consistency
		if ( $atts['show_playlist'] ) {
			// 2.3.0: use new function to get current playlist
			$current_playlist  = radio_station_get_now_playing();
			if ( RADIO_STATION_DEBUG ) {
				$output .= '<span style="display:none;">Current Playlist: ' . print_r( $current_playlist, true ) . '</span>';
			}
			if ( $current_playlist && isset( $current_playlist['playlist_url'] ) ) {
				$playlist = '<div class="current-show-playlist on-air-dj-playlist">';
				$playlist .= '<a href="' . esc_url( $current_playlist['playlist_url'] ) . '">';
				$playlist .= esc_html( __( 'View Playlist', 'radio-station' ) );
				$playlist .= '</a>';
				$playlist .= '</div>';
			}
			// 2.3.3.8: added playlist diplay filter
			$playlist = apply_filters( 'radio_station_current_show_playlist_display', $playlist, $show_id, $atts );
			if ( ( '' != $playlist ) && is_string( $playlist ) ) {
				$html['playlist'] = $playlist;
			}
		}

		// --- countdown timer display ---
		if ( isset( $current_shift_end ) && $atts['countdown'] ) {
			$html['countdown'] = '<div class="current-show-countdown rs-countdown"></div>';
		}

		// --- show description ---
		// 2.3.0: convert span to div tags for consistency
		if ( $atts['show_desc'] ) {

			// --- get show post ---
			$show_post = get_post( $show_id );
			$permalink = get_permalink( $show_id );

			// --- get show excerpt ---
			if ( !empty( $show_post->post_excerpt ) ) {
				$excerpt = "<!-- Post Excerpt -->";
				$excerpt .= $show_post->post_excerpt;
				$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
			} else {
				$excerpt = "<!-- Trimmed Excerpt -->";
				$excerpt .= "<!-- Post ID: " . $show_post->ID . " -->";
				// $excerpt .= "<!-- Post Content: " . $show_post->post_content . " -->";
				$excerpt .= radio_station_trim_excerpt( $show_post->post_content, $length, $more, $permalink );
			}

			// --- filter excerpt by context ---
			// 2.3.0: added contextual filtering
			if ( $atts['widget'] ) {
				$excerpt = apply_filters( 'radio_station_current_show_widget_excerpt', $excerpt, $show_id, $atts );
			} else {
				$excerpt = apply_filters( 'radio_station_current_show_shortcode_excerpt', $excerpt, $show_id, $atts );
			}

			// --- set description ---
			$description = '';
			if ( ( '' != $excerpt ) && is_string( $excerpt ) ) {
				$description = '<div class="current-show-desc on-air-show-desc">';
				$description .= $excerpt;
				$description .= '</div>';
			}
			$description = apply_filters( 'radio_station_current_show_description_display', $description, $show_id, $atts );
			if ( ( '' != $description ) && is_string( $description ) ) {
				$html['description'] = $description;
			}
		}

		// --- output full show schedule ---
		// 2.3.2: do not display all shifts for overrides
		if ( $atts['show_all_sched'] && !isset( $current_shift['override'] ) ) {
			$schedule = apply_filters( 'radio_station_current_show_shifts_display', $shift_display, $show_id, $atts );
			if ( ( '' != $schedule ) && is_string( $schedule ) ) {
				$html['schedule'] = $schedule;
			}
		}

		// --- custom HTML section ---
		// 2.3.3.8: added custom HTML section
		$html['custom'] = apply_filters( 'radio_station_current_show_custom_display', '', $show_id, $atts );

		// --- open current show list item ---
		$output .= '<li class="current-show on-air-dj">';

		// --- filter display section order ---
		// 2.3.1: added filter for section order display
		if ( 'above' == $atts['title_position'] ) {
			$order = array( 'title', 'avatar', 'hosts', 'shift', 'encore', 'clear', 'playlist', 'countdown', 'description', 'clear', 'schedule', 'custom' );
		} else {
			$order = array( 'avatar', 'title', 'hosts', 'shift', 'encore', 'clear', 'playlist', 'countdown', 'description', 'clear', 'schedule', 'custom' );
		}
		$order = apply_filters( 'radio_station_current_show_section_order', $order, $atts );
		foreach ( $order as $section ) {
			if ( isset( $html[$section] ) && ( '' != $html[$section] ) ) {
				$output .= $html[$section];
			}
		}

		// --- close current show list item ---
		$output .= '</li>';

	} else {

		// --- no current show shift display ---
		$output .= '<li class="current-show on-air-dj default-dj">';
		if ( !empty( $atts['default_name'] ) ) {
			$no_current_show = esc_html( $atts['default_name'] );
		} else {
			$no_current_show = esc_html( __( 'No Show currently scheduled.', 'radio-station') );
		}
		// 2.3.1: add filter for no current shows text
		$no_current_show = apply_filters( 'radio_station_no_current_show_text', $no_current_show, $atts );
		$output .= $no_current_show;
		$output .= '</li>';

		// --- countdown timer display ---
		// 2.3.3.8: add countdown timer div regardless of no current show
		// (so timer can update when a current show starts)
		if ( $atts['countdown'] ) {
		 	$output .= '<li><div class="current-show-countdown rs-countdown"></div></li>';
		}

	}

	// --- close current show list ---
	$output .= '</ul>';

	// --- countdown timers ---
	if ( isset( $current_shift_end ) && ( $atts['countdown'] || $atts['dynamic'] ) ) {

		// 2.3.3.9: output current time override
		if ( isset( $atts['for_time'] ) ) {
			$output .= '<input type="hidden" class="current-time-override" value="' . esc_attr( $atts['for_time'] ) . '">';
		}

		// --- hidden inputs for current shift time ---
		$output .= '<input type="hidden" class="current-show-end" value="' . esc_attr( $current_shift_end ) . '">';

		if ( RADIO_STATION_DEBUG ) {
			$output .= '<span style="display:none;">';
			$output .= 'Now: ' . date( 'Y-m-d H:i:s', $now ) . ' (' . esc_attr( $now ) . ')' . PHP_EOL;
			$output .= 'Shift Start Time: ' . date( 'Y-m-d H:i:s', $current_shift_start ) . ' (' . esc_attr( $current_shift_start ) . ')' . PHP_EOL;
			$output .= 'Shift End Time: ' . date( 'Y-m-d H:i:s', $current_shift_end ) . ' (' . esc_attr( $current_shift_end ) . ')' . PHP_EOL;
			$output .= 'Remaining: ' . ( $current_shift_end - $now ) . PHP_EOL;
			$output .= '</span>';
		}

		// --- for dynamic reloading ---
		if ( $atts['dynamic'] ) {
			$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'current-show', $atts, $current_shift_end );
			if ( $dynamic ) {
				$output .= $dynamic;
			}
		}
	}

	// --- close shortcode div wrapper ---
	if ( !$atts['widget'] ) {
		$output .= '</div>';
	}

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	return $output;
}

// ------------------------
// AJAX Current Show Loader
// ------------------------
// 2.3.2: added AJAX current show loader
// 2.3.3: remove current show transient
add_action( 'wp_ajax_radio_station_current_show', 'radio_station_current_show' );
add_action( 'wp_ajax_nopriv_radio_station_current_show', 'radio_station_current_show' );
function radio_station_current_show() {

	// --- sanitize shortcode attributes ---
	$atts = radio_station_sanitize_shortcode_values( 'current-show' );
	if ( RADIO_STATION_DEBUG ) {
		echo "Current Show Shortcode Attributes: " . print_r( $atts, true );
	}

	// --- output widget contents ---
	echo '<div id="widget-contents">';
	echo radio_station_current_show_shortcode( $atts );
	echo '</div>';

	$js = '';
	if ( isset( $atts['instance'] ) ) {

		// --- send to parent window ---
		$js .= "widget = document.getElementById('widget-contents').innerHTML;" . PHP_EOL;
		$js .= "parent.document.getElementById('rs-current-show-" . esc_js( $atts['instance'] ) . "').innerHTML = widget;" . PHP_EOL;

		// --- maybe restart countdowns ---
		if ( $atts['countdown'] ) {
			$js .= "setTimeout(function() {parent.radio_countdown();}, 2000);" . PHP_EOL;
		}

	}

	// --- filter load script ---
	$js = apply_filters( 'radio_station_current_show_load_script', $js, $atts );

	// --- output javascript
	if ( '' != $js ) {
		echo "<script>" . $js . "</script>";
	}

	exit;
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

	// --- set widget instance ID ---
	// 2.3.2: added for AJAX loading
	if ( !isset( $radio_station_data['upcoming_shows_instance'] ) ) {
		$radio_station_data['upcoming_shows_instance'] = 0;
	}
	$radio_station_data['upcoming_shows_instance']++;

	$output = '';

	// 2.3.2: get default AJAX load settings
	$ajax = radio_station_get_setting( 'ajax_widgets' );
	$ajax = ( 'yes' == $ajax ) ? 'on' : 'off';
	$dynamic = apply_filters( 'radio_station_upcomins_shows_dynamic', false, $atts );
	$dynamic = $dynamic ? 1 : 0;

	// 2.3.3: use plugin setting if time format attribute is empty
	if ( isset( $atts['time'] ) && ( '' == $atts['time'] ) ) {
		unset( $atts['time'] );
	}

	// 2.3.0: set default time format to plugin setting
	// 2.3.2: added AJAX load attribute
	// 2.3.2: added for_time attribute
	// 2.3.3.8: added show_encore attribute (default 1)
	// 2.3.3.9: added show_title attribute (default 1)
	$time_format = radio_station_get_setting( 'clock_time_format' );
	$defaults = array(
		// --- legacy options ---
		'title'             => '',
		'limit'             => 1,
		'show_title'        => 1,
		'show_avatar'       => 0,
		'show_link'         => 0,
		'time'              => $time_format,
		'show_sched'        => 1,
		'default_name'      => '',
		// --- new options ---
		// 'display_producers' => 0,
		// 'show_desc'         => 0,
		'display_hosts'     => 0,
		'show_encore'       => 1,
		'link_hosts'        => 0,
		'avatar_width'      => '',
		'avatar_size'       => 'thumbnail',
		'title_position'    => 'right',
		'countdown'         => 0,
		'ajax'              => $ajax,
		'dynamic'           => $dynamic,
		'widget'            => 0,
		'id'                => '',
		'for_time'          => 0,
	);
	// 2.3.0: convert old attributes for DJs to hosts
	if ( isset( $atts['display_djs'] ) && !isset( $atts['display_hosts'] ) ) {
		$atts['display_hosts'] = $atts['display_djs'];
		unset( $atts['display_djs'] );
	}
	if ( isset( $atts['link_djs'] ) && !isset( $atts['link_hosts'] ) ) {
		$atts['link_hosts'] = $atts['link_djs'];
		unset( $atts['link_djs'] );
	}
	// 2.3.0: renamed shortcode identifier to upcoming-shows
	$atts = shortcode_atts( $defaults, $atts, 'upcoming-shows' );

	// 2.3.2: enqueue countdown script earlier
	if ( $atts['countdown'] ) {
		do_action( 'radio_station_countdown_enqueue' );
	}

	// 2.3.3: added current time override for manual testing
	if ( isset( $_GET['date'] ) && isset( $_GET['time'] ) ) {
		$date = trim( $_GET['date'] );
		$time = trim( $_GET['time'] );
		if ( isset( $_GET['month'] ) ) {
			$month = absint( trim( $_GET['month'] ) );
		} else {
			$month = radio_station_get_time( 'm' );
		}
		if ( isset( $_GET['year'] ) ) {
			$year = absint( trim( $_GET['year'] ) );
		} else {
			$year = radio_station_get_time( 'Y' );
		}
		if ( strstr( $time, ':' ) && ( $month > 0 ) && ( $month < 13 ) ) {
			$parts = explode( ':', $time );
			$time = absint( $parts[0] ) . ':' . absint( $parts[1] );
			$for_time = radio_station_to_time( $year . '-' . $month . '-' . $date . ' ' . $time );
			$atts['for_time'] = $for_time;
			echo "<script>console.log('Override Current Time: " . $for_time . "');</script>";
		}
	}

	// --- maybe do AJAX load ---
	// 2.3.2 added widget AJAX loading
	$ajax = $atts['ajax'];
	$widget = $atts['widget'];
	$ajax = apply_filters( 'radio_station_widgets_ajax_override', $ajax, 'upcoming-shows', $widget );
	if ( 'on' == $ajax ) {
		if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) {

			// --- AJAX load via iframe ---
			$ajax_url = admin_url( 'admin-ajax.php' );
			$instance = $radio_station_data['upcoming_shows_instance'];
			$html = '<div id="rs-upcoming-shows-' . esc_attr( $instance ) . '" class="ajax-widget"></div>';
			$html .= '<iframe id="rs-upcoming-shows-' . esc_attr( $instance ) . '-loader" src="javascript:void(0);" style="display:none;"></iframe>';
			$html .= "<script>timestamp = Math.floor( (new Date()).getTime() / 1000 );
			url = '" . esc_url( $ajax_url ) . "?action=radio_station_upcoming_shows';
			url += '&instance=" . esc_attr( $instance ) . "&timestamp='+timestamp;";
			if ( RADIO_STATION_DEBUG ) {
				$html .= "url += '&rs-debug=1';";
			}
			$html .= "url += '";
			foreach ( $atts as $key => $value ) {
				$value = radio_station_encode_uri_component( $value );
				$html .= "&" . esc_js( $key ) . "=" . esc_js( $value );
			}
			$html .= "'; ";
			$html .= "document.getElementById('rs-upcoming-shows-" . esc_attr( $instance ) . "-loader').src = url;";
			$html .= "</script>";

			// --- enqueue shortcode styles ---
			radio_station_enqueue_style( 'shortcodes' );

			return $html;
		}
	}

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

	// --- get the upcoming shows ---
	// note: upcoming shows are not split shift
	// 2.3.0: use new get next shows function
	if ( $atts['for_time'] ) {
		$shows = radio_station_get_next_shows( $atts['limit'], false, $atts['for_time'] );
	} else {
		$shows = radio_station_get_next_shows( $atts['limit'] );
	}
	if ( RADIO_STATION_DEBUG ) {
		$output .= '<span style="display:none;">Upcoming Shows: ' . print_r( $shows, true ) . '</span>';
	}

	// --- open shortcode only wrapper ---
	if ( !$atts['widget'] ) {

		// 2.3.0: add unique id to widget shortcode
		// 2.3.2: add shortcode wrap class
		if ( !isset( $radio_station_data['widgets']['upcoming-shows'] ) ) {
			$radio_station_data['widgets']['upcoming-shows'] = 0;
		} else {
			$radio_station_data['widgets']['upcoming-shows']++;
		}
		$id = 'upcoming-shows-widget-' . $radio_station_data['widgets']['upcoming-shows'];
		$output .= '<div id="' . esc_attr( $id ) . '" class="upcoming-shows-wrap upcoming-shows-embedded on-air-embedded dj-coming-up-embedded">';

		// --- maybe output shortcode title ---
		if ( !empty( $atts['title'] ) ) {
			$output .= '<h3 class="upcoming-shows-title dj-coming-up-title">';
			$output .= esc_html( $atts['title'] );
			$output .= '</h3>';
		}
	}

	// --- open upcoming show list ---
	$output .= '<ul class="upcoming-shows-list on-air-upcoming-list">';

	// --- shows upcoming output ---
	if ( $shows ) {

		// --- filter display section order ---
		// 2.3.1: added filter for section order display
		// 2.3.3.8: moved section order filter outside of show shift loop
		if ( 'above' == $atts['title_position'] ) {
			$order = array( 'title', 'avatar', 'hosts', 'shift', 'clear', 'countdown', 'encore', 'custom' );
		} else {
			$order = array( 'avatar', 'title', 'hosts', 'shift', 'clear', 'countdown', 'encore', 'custom' );
		}
		$order = apply_filters( 'radio_station_upcoming_shows_section_order', $order, $atts );

		// --- set shift display data formats ---
		// 2.2.7: fix to convert time to integer
		// 2.3.2: moved outside shift loop
		if ( 24 == (int) $atts['time'] ) {
			$start_data_format = $end_data_format = 'H:i';
		} else {
			$start_data_format = $end_data_format = 'g:i a';
		}
		$start_data_format = 'l, ' . $start_data_format;
		$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'upcoming-shows', $atts );
		$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'upcoming-shows', $atts );

		// --- convert dates ---
		// 2.3.0: use weekdates for reliability
		if ( $atts['for_time'] ) {
			$now = $atts['for_time'];
		} else {
			$now = radio_station_get_now();
		}
		$today = radio_station_get_time( 'l', $now );
		$yesterday = radio_station_get_previous_day( $today );
		$weekdays = radio_station_get_schedule_weekdays( $yesterday );
		$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now );

		// --- loop upcoming shows ---
		foreach ( $shows as $i => $shift ) {

			// --- reset output ---
			// 2.3.1: store all HTML to allow section re-ordering
			$html = array( 'title' => '' );

			// --- get show data ---
			$show = $shift['show'];
			$show_id = $show['id'];

			// --- set show link ---
			$show_link = false;
			if ( $atts['show_link'] ) {
				$show_link = get_permalink( $show_id );
				$show_link = apply_filters( 'radio_station_upcoming_show_link', $show_link, $show_id, $atts );
			}

			// --- check show schedule ---
			// 2.3.1: check earlier for later display
			if ( $atts['show_sched'] || $atts['countdown'] || $atts['dynamic'] ) {

				// --- set shift start and end ---
				if ( isset( $shift['real_start'] ) ) {
					$start = $shift['real_start'];
				} elseif ( isset( $shift['start'] ) ) {
					$start = $shift['start'];
				} else {
					$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
				}
				if ( isset( $shift['real_end'] ) ) {
					$end = $shift['real_end'];
				} elseif ( isset( $shift['end'] ) ) {
					$end = $shift['end'];
				} else {
					$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
				}

				// --- convert shift info ---
				// 2.2.2: fix to weekday value to be translated
				// 2.3.2: replace strtotime with to_time for timezones
				// 2.3.2: use exact shift date in time calculations
				// 2.3.2: fix to convert to 24 hour format first
				// $display_day = radio_station_translate_weekday( $shift['day'] );
				$shift_start = radio_station_convert_shift_time( $start );
				$shift_end = radio_station_convert_shift_time( $end );
				if ( isset( $shift['real_start'] ) ) {
					$prevday = radio_station_get_previous_day( $shift['day'] );
					$shift_start_time = radio_station_to_time( $weekdates[$prevday] . ' ' . $shift_start );
				} else {
					$shift_start_time = radio_station_to_time( $weekdates[$shift['day']] . ' ' . $shift_start );
				}
				$shift_end_time = radio_station_to_time( $weekdates[$shift['day']] . ' ' . $shift_end );
				// 2.3.3.9: added or equals to operator
				if ( $shift_end_time <= $shift_start_time ) {
					$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
				}

				// --- maybe set next show shift times ---
				if ( !isset( $next_start_time ) ) {
					$next_start_time = $shift_start_time;
					$next_end_time = $shift_end_time;
				}

				// --- set shift classes ---
				$classes = array( 'upcoming-show-shift', 'on-air-dj-sched' );
				if ( ( $now > $shift_start_time ) && ( $now < $shift_end_time ) ) {
					$classes[] = 'current-shift';
				}
				$class = implode( ' ', $classes );

				// --- get shift display times ---
				// 2.3.2: use time formats with translations
				$start = radio_station_get_time( $start_data_format, $shift_start_time );
				$end = radio_station_get_time( $end_data_format, $shift_end_time );
				$start = radio_station_translate_time( $start );
				$end = radio_station_translate_time( $end );

				// --- set shift display output ---
				$shift_display = '<div class="upcoming-show-schedule on-air-dj-schedule">';
				$shift_display .= '<div class="' . esc_attr( $class ) . '">';
				$shift_display .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>';
				$shift_display .= '<span class="rs-sep"> - </span>';
				$shift_display .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>';
				$shift_display .= '</div>';
				// 2.3.3.9: add empty user time div
				$shift_display .= '<div class="show-user-time">';
				$shift_display .= '[<span class="rs-time rs-start-time"></span>';
				$shift_display .= '<span class="rs-sep"> - </span>';
				$shift_display .= '<span class="rs-time rs-end-time"></span>]';
				$shift_display .= '</div>';
				$shift_display .= '</div>';
				if ( RADIO_STATION_DEBUG ) {
					$shift_display .= '<span style="display:none;">Upcoming Shift: ' . print_r( $shift, true ) . '</span>';
				}
			}

			// --- set clear div ---
			$html['clear'] = '<span class="radio-clear"></span>';

			// --- set show title ---
			// 2.3.3.9: added attribute for show title display
			if ( $atts['show_title'] ) {
				$title = '<div class="upcoming-show-title on-air-dj-title">';
				if ( $show_link ) {
					$title .= '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
				} else {
					$title .= esc_html( $show['name'] );
				}
				$title .= '</div>';
				$title = apply_filters( 'radio_station_upcoming_show_title_display', $title, $show_id, $atts );
				if ( ( '' != $title ) && is_string( $title ) ) {
					$html['title'] = $title;
				}
			}

			// --- set show avatar ---
			if ( $atts['show_avatar'] ) {

				// 2.3.0: get show avatar (with thumbnail fallback)
				// 2.3.0: filter show avatar by context
				// 2.3.0: maybe link avatar to show
				// 2.3.3.9: add filter for avatar image display size
				$avatar = '';
				$avatar_size = apply_filters( 'radio_station_upcoming_show_avatar_size', $atts['avatar_size'], $show_id );
				$show_avatar = radio_station_get_show_avatar( $show_id, $avatar_size );
				$show_avatar = apply_filters( 'radio_station_upcoming_show_avatar', $show_avatar, $show_id, $atts );
				if ( $show_avatar ) {
					$avatar = '<div class="upcoming-show-avatar on-air-dj-avatar' . esc_attr( $float_class ) . '" ' . $width_style . '>';
					if ( $atts['show_link'] ) {
						$avatar .= '<a href="' . esc_url( $show_link ) . '">';
					}
					$avatar .= $show_avatar;
					if ( $atts['show_link'] ) {
						$avatar .= '</a>';
					}
					$avatar .= '</div>';
				}
				$avatar = apply_filters( 'radio_station_upcoming_show_avatar_display', $avatar, $show_id, $atts );
				if ( ( '' != $avatar ) && is_string( $avatar ) ) {
					$html['avatar'] = $avatar;
				}
			}

			// --- set DJ / Host names ---
			if ( $atts['display_hosts'] ) {

				$hosts = '';
				$show_hosts = get_post_meta( $show_id, 'show_user_list', true );
				if ( $show_hosts ) {
						// 2.4.0.4: convert possible (old) non-array value
						if ( !is_array( $show_hosts ) ) {
							$show_hosts = array( $show_hosts );
						}
						if ( is_array( $show_hosts ) && ( count( $show_hosts ) > 0 ) ) {

						$hosts = '<div class="upcoming-show-hosts on-air-dj-names">';
						$hosts .= esc_html( __( 'with', 'radio-station' ) ) . ' ';

						$count = 0;
						// 2.3.3.9: fix to host count
						$host_count = count( $show_hosts );
						foreach ( $show_hosts as $host ) {

							$count ++;

							// 2.3.0: maybe get stored user data
							// $user = get_userdata( $host );
							if ( isset( $radio_station_data['user-' . $host] ) ) {
								$user = $radio_station_data['user-' . $host];
							} else {
								$user = get_user_by( 'ID', $host );
								$radio_station_data['user-' . $host] = $user;
							}

							if ( $atts['link_hosts'] ) {
								// 2.3.0: use new get host URL function
								$host_link = radio_station_get_host_url( $host );
								$host_link = apply_filters( 'radio_station_dj_link', $host_link, $host );

								// 2.3.3.5: only wrap with tags if there is a link
								if ( $host_link ) {
									$hosts .= '<a href="' . esc_url( $host_link ) . '">';
								}
								$hosts .= esc_html( $user->display_name );
								if ( $host_link ) {
									$hosts .= '</a>';
								}
							} else {
								$hosts .= esc_html( $user->display_name );
							}

							if ( ( ( 1 == $count ) && ( 2 == $host_count ) )
								 || ( ( $host_count > 2 ) && ( $count == ( $host_count - 1 ) ) ) ) {
								$hosts .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
							} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
								$hosts .= ', ';
							}
						}
						$hosts .= '</div>';
					}
					$hosts = apply_filters( 'radio_station_upcoming_show_hosts_display', $hosts, $show_id, $atts );
					if ( ( '' != $hosts ) && is_string( $hosts ) ) {
						$html['hosts'] = $hosts;
					}
				}
			}

			// --- set encore presentation ---
			// 2.2.4: added encore presentation display
			// 2.3.3.8: added shortcode attribute for encore display (default 1)
			if ( $atts['show_encore'] ) {
				$encore = '';
				if ( isset( $show['encore'] ) && ( 'on' == $show['encore'] ) ) {
					$encore = '<div class="upcoming-show-encore on-air-dj-encore">';
					$encore .= esc_html( __( 'Encore Presentation', 'radio-station' ) );
					$encore .= '</div>';
				}
				$encore = apply_filters( 'radio_station_upcoming_show_encore_display', $encore, $show_id, $atts );
				if ( ( '' != $encore ) && is_string( $encore ) ) {
					$html['encore'] = $encore;
				}
			}

			// --- set countdown timer ---
			if ( ( 0 == $i ) && isset( $next_start_time ) && $atts['countdown'] ) {
				$html['countdown'] = '<div class="upcoming-show-countdown rs-countdown"></div>';
			}

			// --- set show schedule ---
			if ( $atts['show_sched'] ) {
				$schedule = apply_filters( 'radio_station_upcoming_show_shifts_display', $shift_display, $show_id, $atts );
				if ( ( '' != $schedule ) && is_string( $schedule ) ) {
					$html['shift'] = $schedule;
				}
			}

			// --- custom HTML section ---
			// 2.3.3.8: added custom HTML section
			$html['custom'] = apply_filters( 'radio_station_upcoming_shows_custom_display', '', $show_id, $atts );

			// --- open upcoming show list item ---
			$output .= '<li class="upcoming-show on-air-dj">';

			// --- add output according to section order ---
			// 2.3.3.8: moved section order filter out of show shift loop
			foreach ( $order as $section ) {
				if ( isset( $html[$section] ) && ( '' != $html[$section] ) ) {
					$output .= $html[$section];
				}
			}

			// --- close upcoming show list item ---
			$output .= '</li>';
		}

	} else {

		// --- no shows upcoming ---
		// note: no countdown display added as no upcoming shows found
		$output .= '<li class="upcoming-show-none on-air-dj default-dj">';
		if ( ! empty( $atts['default_name'] ) ) {
			$no_upcoming_shows = esc_html( $atts['default_name'] );
		} else {
			$no_upcoming_shows = esc_html( __( 'No Upcoming Shows Scheduled.', 'radio-station' ) );
		}
		// 2.3.1: add filter for no current shows text
		$no_upcoming_shows = apply_filters( 'radio_station_no_upcoming_shows_text', $no_upcoming_shows, $atts );
		$output .= $no_upcoming_shows;
		$output .= '</li>';

	}

	// --- close upcoming shows list ---
	$output .= '</ul>';

	// --- countdown timer inputs ---
	// 2.3.0: added for countdowns
	if ( isset( $next_start_time ) && ( $atts['countdown'] || $atts['dynamic'] ) ) {

		// 2.3.3.9: output current time override
		if ( isset( $atts['for_time'] ) ) {
			$output .= '<input type="hidden" class="current-time-override" value="' . esc_attr( $atts['for_time'] ) . '">';
		}

		// --- hidden input for next start time ---
		$output .= '<input type="hidden" class="upcoming-show-times" value="' . esc_attr( $next_start_time ) . '-' . esc_attr( $next_end_time ) . '">';
		if ( RADIO_STATION_DEBUG ) {
			$output .= '<span style="display:none;">';
			$output .= 'Now: ' . date( 'Y-m-d H:i:s', $now ) . ' (' . $now . ')' . PHP_EOL;
			$output .= 'Next Start Time: ' . date('y-m-d H:i:s', $next_start_time ) . ' (' . $next_start_time . ')' . PHP_EOL;
			$output .= 'Next End Time: ' . date( 'y-m-d H:i:s', $next_end_time ) . ' (' . $next_end_time . ')' . PHP_EOL;
			$output .= 'Starting in: ' . ( $next_start_time - $now ) . PHP_EOL;
			$output .= '</span>';
		}

		// --- for dynamic reloading ---
		if ( $atts['dynamic'] ) {
			$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'upcoming-shows', $atts, $next_start_time );
			if ( $dynamic ) {
				$output .= $dynamic;
			}
		}
	}

	// --- close shortcode only wrapper ---
	if ( !$atts['widget'] ) {
		$output .= '</div>';
	}

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	return $output;
}

// --------------------------
// AJAX Upcoming Shows Loader
// --------------------------
// 2.3.2: added AJAX upcoming shows loader
add_action( 'wp_ajax_radio_station_upcoming_shows', 'radio_station_upcoming_shows' );
add_action( 'wp_ajax_nopriv_radio_station_upcoming_shows', 'radio_station_upcoming_shows' );
function radio_station_upcoming_shows() {

	// --- sanitize shortcode attributes ---
	$atts = radio_station_sanitize_shortcode_values( 'upcoming-shows' );
	if ( RADIO_STATION_DEBUG ) {
		echo "Upcoming Shows Shortcode Attributes: " . print_r( $atts, true );
	}

	// --- output widget contents ---
	echo '<div id="widget-contents">';
	echo radio_station_upcoming_shows_shortcode( $atts );
	echo '</div>';

	$js = '';
	if ( isset( $atts['instance'] ) ) {

		// --- send to parent window ---
		$js .= "widget = document.getElementById('widget-contents').innerHTML;" . PHP_EOL;
		$js .= "parent.document.getElementById('rs-upcoming-shows-" . esc_js( $atts['instance'] ) . "').innerHTML = widget;" . PHP_EOL;

		// --- restart countdowns ---
		if ( $atts['countdown'] ) {
			$js .= "setTimeout(function() {parent.radio_countdown();}, 2000);" . PHP_EOL;
		}

	}

	// --- filter load script ---
	$js = apply_filters( 'radio_station_upcoming_shows_load_script', $js, $atts );

	// --- output javascript
	if ( '' != $js ) {
		echo "<script>" . $js . "</script>";
	}

	exit;
}

// --------------------------
// Current Playlist Shortcode
// --------------------------
// [current-playlist] / [now-playing]
// 2.3.0: added missing output sanitization
add_shortcode( 'current-playlist', 'radio_station_current_playlist_shortcode' );
add_shortcode( 'now-playing', 'radio_station_current_playlist_shortcode' );
function radio_station_current_playlist_shortcode( $atts ) {

	global $radio_station_data;

	// --- set widget instance ID ---
	// 2.3.2: added for AJAX loading
	if ( !isset( $radio_station_data['current_playlist_instance'] ) ) {
		$radio_station_data['current_playlist_instance'] = 0;
	}
	$radio_station_data['current_playlist_instance']++;

	$output = '';

	// 2.3.2: get default AJAX load settings
	$ajax = radio_station_get_setting( 'ajax_widgets' );
	$ajax = ( 'yes' == $ajax ) ? 'on' : 'off';
	$dynamic = apply_filters( 'radio_station_current_playlist_dynamic', false, $atts );
	$dynamic = $dynamic ? 1 : 0;

	// --- get shortcode attributes ---
	// 2.3.2: added AJAX load attribute
	// 2.3.2: added for_time attribute
	$defaults = array(
		// --- legacy options ---
		'title'     => '',
		'artist'    => 1,
		'song'      => 1,
		'album'     => 0,
		'label'     => 0,
		'comments'  => 0,
		// --- new options ---
		'link'      => 1,
		'countdown' => 0,
		'ajax'      => $ajax,
		'dynamic'   => $dynamic,
		'widget'    => 0,
		'id'        => '',
		'for_time'  => 0,
	);

	// 2.3.0: renamed shortcode identifier to current-playlist
	$atts = shortcode_atts( $defaults, $atts, 'current-playlist' );

	// 2.3.2: enqueue countdown script earlier
	if ( $atts['countdown'] ) {
		do_action( 'radio_station_countdown_enqueue' );
	}

	// 2.3.3: added current time override for manual testing
	if ( isset( $_GET['date'] ) && isset( $_GET['time'] ) ) {
		$date = trim( $_GET['date'] );
		$time = trim( $_GET['time'] );
		if ( isset( $_GET['month'] ) ) {
			$month = absint( trim( $_GET['month'] ) );
		} else {
			$month = radio_station_get_time( 'm' );
		}
		if ( isset( $_GET['year'] ) ) {
			$year = absint( trim( $_GET['year'] ) );
		} else {
			$year = radio_station_get_time( 'Y' );
		}
		if ( strstr( $time, ':' ) && ( $month > 0 ) && ( $month < 13 ) ) {
			$parts = explode( ':', $time );
			$time = absint( $parts[0] ) . ':' . absint( $parts[1] );
			$for_time = radio_station_to_time( $year . '-' . $month . '-' . $date . ' ' . $time );
			$atts['for_time'] = $for_time;
			echo "<script>console.log('Override Current Time: " . $for_time . "');</script>";
		}
	}

	// --- maybe do AJAX load ---
	// 2.3.2 added widget AJAX loading
	$ajax = $atts['ajax'];
	$widget = $atts['widget'];
	$ajax = apply_filters( 'radio_station_widgets_ajax_override', $ajax, 'current-playlist', $widget );
	if ( 'on' == $ajax ) {
		if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) {

			// --- AJAX load via iframe ---
			$ajax_url = admin_url( 'admin-ajax.php' );
			$instance = $radio_station_data['current_playlist_instance'];
			$html = '<div id="rs-current-playlist-' . esc_attr( $instance ) . '" class="ajax-widget"></div>';
			$html .= '<iframe id="rs-current-playlist-' . esc_attr( $instance ) . '-loader" src="javascript:void(0);" style="display:none;"></iframe>';
			$html .= "<script>timestamp = Math.floor( (new Date()).getTime() / 1000 );
			url = '" . esc_url( $ajax_url ) . "?action=radio_station_current_playlist';
			url += '&instance=" . esc_attr( $instance ) . "&timestamp='+timestamp;";
			$html .= "url += '";
			foreach ( $atts as $key => $value ) {
				$value = radio_station_encode_uri_component( $value );
				$html .= "&" . esc_js( $key ) . "=" . esc_js( $value );
			}
			$html .= "'; ";
			$html .= "document.getElementById('rs-current-playlist-" . esc_attr( $instance ) ."-loader').src = url;";
			$html .= "</script>";

			// --- enqueue shortcode styles ---
			radio_station_enqueue_style( 'shortcodes' );

			return $html;
		}
	}

	// --- fetch the current playlist ---
	if ( $atts['for_time'] ) {
		$playlist = radio_station_get_now_playing( $atts['for_time'] );
		$time = radio_station_get_time( 'datetime', $atts['for_time'] );
		echo '<span style="display:none;">';
			echo 'Current Playlist For Time: ' . $atts['for_time'] . ' : ' . $time . PHP_EOL;
			echo print_r( $playlist, true );
		echo '</span>';
	} else {
		$playlist = radio_station_get_now_playing();
	}

	// --- shortcode only wrapper ---
	if ( !$atts['widget'] ) {

		// 2.3.0: add unique id to widget shortcode
		// 2.3.2: fix to shortcode classes
		// 2.3.2: add shortcode wrap class
		if ( !isset( $radio_station_data['widgets']['current-playlist'] ) ) {
			$radio_station_data['widgets']['current-playlist'] = 0;
		} else {
			$radio_station_data['widgets']['current-playlist']++;
		}
		$id = 'show-playlist-widget-' . $radio_station_data['widgets']['current-playlist'];
		$output .= '<div id="' . esc_attr( $id ) . '" class="current-playlist-wrap current-playlist-embedded now-playing-embedded">';

		// --- shortcode title ---
		if ( !empty( $atts['title'] ) ) {
			// 2.3.0: added title class for shortcode
			$output .= '<h3 class="show-playlist-title myplaylist-title">' . esc_attr( $atts['title'] ) . '</h3>';
		}
	}

	// --- set empty HTML array ---
	$html = array();

	// --- countdown timer ---
	// 2.3.0: added for countdown changeovers
	// 2.3.3.8: moved outside of current playlist check
	if ( $atts['countdown'] || $atts['dynamic'] ) {

		$html['countdown'] = '';
		// if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Playlist: ' . print_r( $playlist, true ) . '</span>';
		// }

		// 2.3.1: added check for playlist shifts value
		if ( isset( $playlist['shifts'] ) && is_array( $playlist['shifts'] ) && ( count( $playlist['shifts'] ) > 0 ) ) {

			// --- convert dates ---
			// 2.3.0: use weekdates for reliability
			if ( $atts['for_time'] ) {
				$now = $atts['for_time'];
			} else {
				$now = radio_station_get_now();
			}
			$today = radio_station_get_time( 'l', $now );
			$yesterday = radio_station_get_previous_day( $today );
			$weekdays = radio_station_get_schedule_weekdays( $yesterday );
			$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now );
			// echo 'Now: ' . $now . ' : ' . radio_station_get_time( 'datetime', $now ) . '<br>';

			// --- loop shifts ---
			foreach ( $playlist['shifts'] as $shift_id => $shift ) {

				// 2.3.3.9: added check that shift day is set
				if ( isset( $shift['day'] ) && ( '' != $shift['day'] ) ) {

					// --- set shift start and end ---
					if ( isset( $shift['real_start'] ) ) {
						$start = $shift['real_start'];
					} elseif ( isset( $shift['start'] ) ) {
						$start = $shift['start'];
					} else {
						$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
					}
					if ( isset( $shift['real_end'] ) ) {
						$end = $shift['real_end'];
					} elseif ( isset( $shift['end'] ) ) {
						$end = $shift['end'];
					} else {
						$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
					}

					// --- convert shift info ---
					// 2.3.2: replace strtotime with to_time for timezones
					// 2.3.2: fix to convert to 24 hour format first
					// TODO: check/test possible undefined index for $shift['day'] ?
					$start_time = radio_station_convert_shift_time( $start );
					$end_time = radio_station_convert_shift_time( $end );
					if ( isset( $shift['real_start'] ) ) {
						$prevday = radio_station_get_previous_day( $shift['day'] );
						$shift_start_time = radio_station_to_time( $weekdates[$prevday] . ' ' . $start_time );
					} else {
						$shift_start_time = radio_station_to_time( $weekdates[$shift['day']] . ' ' . $start_time );
					}
					$shift_end_time = radio_station_to_time( $weekdates[$shift['day']] . ' ' . $end_time );
					// 2.3.3.9: fix to overnight check variables
					// 2.3.3.9: added or equals to operator
					if ( $shift_end_time <= $shift_start_time ) {
						$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
					}

					// --- check currently playing show time ---
					if ( $atts['for_time'] ) {
						$now = $atts['for_time'];
						$html['countdown'] .= '<input type="hidden" class="current-time-override" value="' . esc_attr( $atts['for_time'] ) . '">';
					} else {
						$now = radio_station_get_now();
					}
					// echo 'Shift Start: ' . $shift_start_time . '(' . radio_station_get_time( 'datetime', $shift_start_time ) . ')<br>';
					// echo 'Shift End: ' . $shift_end_time . '(' . radio_station_get_time( 'datetime', $shift_end_time ) . ')<br>';

					if ( ( ( $now > $shift_start_time ) || ( $now == $shift_start_time ) ) && ( $now < $shift_end_time ) ) {

						// print_r( $shift );
						// echo "^^^ NOW PLAYING ^^^";

						// --- hidden input for playlist end time ---
						$html['countdown'] .= '<input type="hidden" class="current-playlist-end" value="' . esc_attr( $shift_end_time ) . '">';

						// --- for countdown timer display ---
						if ( $atts['countdown'] ) {
							$html['countdown'] .= '<div class="show-playlist-countdown rs-countdown"></div>';
						}

						// --- for dynamic reloading ---
						if ( $atts['dynamic'] ) {
							$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'current-playlist', $atts, $shift_end_time );
							if ( $dynamic ) {
								$html['countdown'] .= $dynamic;
							}
						}
					}
				}
			}
		}
	}

	// 2.3.0: use updated code from now playing widget
	// 2.3.3.9: move check for playlist tracks here
	$tracks = '';
	if ( $playlist && isset( $playlist['tracks'] ) && is_array( $playlist['tracks'] ) && ( count( $playlist['tracks'] ) > 0 ) ) {

		// 2.3.0: split div wrapper from track wrapper
		$tracks .= '<div class="show-playlist-tracks myplaylist-nowplaying">';

		// --- loop playlist tracks ---
		// 2.3.0: loop all instead of just latest
		// 2.3.1: added check for playlist tracks
		// 2.3.3.9: moved up check for playlist tracks
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

			$tracks .= '<div class="show-playlist-track myplaylist-track' . esc_attr( $class ) . '">';

			// 2.2.3: convert span tags to div tags
			// 2.2.4: check value keys are set before outputting
			if ( $atts['song'] && isset( $track['playlist_entry_song'] ) ) {
				$tracks .= '<div class="show-playlist-song myplaylist-song">';
				$tracks .= esc_html( __( 'Song', 'radio-station' ) );
				$tracks .= ': ' . esc_html( $track['playlist_entry_song'] );
				$tracks .= '</div>';
			}

			// 2.2.7: add label prefixes to now playing data
			if ( $atts['artist'] && isset( $track['playlist_entry_artist'] ) ) {
				$tracks .= '<div class="show-playlist-artist myplaylist-artist">';
				$tracks .= esc_html( __( 'Artist', 'radio-station' ) );
				$tracks .= ': ' . esc_html( $track['playlist_entry_artist'] );
				$tracks .= '</div>';
			}

			if ( $atts['album'] && !empty( $track['playlist_entry_album'] ) ) {
				$tracks .= '<div class="show-playlist-album myplaylist-album">';
				$tracks .= esc_html( __( 'Album', 'radio-station' ) );
				$tracks .= ': ' . esc_html( $track['playlist_entry_album'] );
				$tracks .= '</div>';
			}

			if ( $atts['label'] && !empty( $track['playlist_entry_label'] ) ) {
				$tracks .= '<div class="show-playlist-label myplaylist-label">';
				$tracks .= esc_html( __( 'Label', 'radio-station' ) );
				$tracks .= ': ' . esc_html( $track['playlist_entry_label'] );
				$tracks .= '</div>';
			}

			if ( $atts['comments'] && !empty( $track['playlist_entry_comments'] ) ) {
				$tracks .= '<div class="show-playlist-comments myplaylist-comments">';
				$tracks .= esc_html( __( 'Comments', 'radio-station' ) );
				$tracks .= ': ' . esc_html( $track['playlist_entry_comments'] );
				$tracks .= '</div>';
			}

			$tracks .= '</div>';
		}
		$tracks .= '</div>';

		// --- playlist permalink ---
		// 2.3.3.8 added playlist_link shortcode attribute (default 1)
		if ( $atts['link'] ) {
			$link = '';
			if ( isset( $playlist['playlist_permalink'] ) ) {
				$link = '<div class="show-playlist-link myplaylist-link">';
				$link .= '<a href="' . esc_url( $playlist['playlist_permalink'] ) . '">';
				$link .= esc_html( __( 'View Playlist', 'radio-station' ) );
				$link .= '</a>';
				$link .= '</div>';
			}
			// 2.3.3.8: added playlist link display filter
			$link = apply_filters( 'radio_station_current_playlist_link_display', $link, $playlist, $atts );
			if ( ( '' != $link ) && is_string( $link ) ) {
				$html['link'] = $link;
			}
		}

	} else {

		// 2.2.3: added missing translation wrapper
		// 2.3.0: added no playlist class
		// 2.3.1: add filter for no playlist text
		// 2.3.3.8: fix to unclosed double quote on class attribute
		$no_playlist = '<div class="show-playlist-noplaylist myplaylist-noplaylist">';
		$no_current_playlist = esc_html( __( 'No Current Playlist available.', 'radio-station' ) );
		$no_current_playlist = apply_filters( 'radio_station_no_current_playlist_text', $no_current_playlist, $atts );
		$no_playlist .= $no_current_playlist;
		$no_playlist .= '</div>';

		// 2.3.3.8: added no playlist display filter
		// 2.3.3.9: assign to tracks key for possible output re-ordering
		$no_playlist = apply_filters( 'radio_station_current_playlist_no_playlist_display', $no_playlist, $atts );
		if ( ( '' != $no_playlist ) && is_string( $no_playlist ) ) {
			$html['tracks'] = $no_playlist;
		}
	}

	// 2.3.3.8: added track display filter
	// 2.3.3.9: moved outside of playlist check
	$tracks = apply_filters( 'radio_station_current_playlist_tracks_display', $tracks, $playlist, $atts );
	if ( ( '' != $tracks ) && is_string( $tracks ) ) {
		$html['tracks'] = $tracks;
	}

	// --- custom HTML section ---
	// 2.3.3.8: added custom HTML section
	// 2.3.3.9: move outside of playlist check
	$html['custom'] = apply_filters( 'radio_station_current_playlist_custom_display', '', $playlist, $atts );

	// --- filter display section order ---
	// 2.3.1: added filter for section order display
	// 2.3.3.9: moved outside of check for current playlist
	$order = array( 'tracks', 'link', 'countdown', 'custom' );
	$order = apply_filters( 'radio_station_current_playlist_section_order', $order, $atts );
	// $output .= print_r( array_keys( $html ), true );
	foreach ( $order as $section ) {
		if ( isset( $html[$section] ) && ( '' != $html[$section] ) ) {
			$output .= $html[$section];
		}
	}
		
	// --- close shortcode only wrapper ---
	if ( !$atts['widget'] ) {
		$output .= '</div>';
	}

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	return $output;
}

// ----------------------------
// AJAX Current Playlist Loader
// ----------------------------
// 2.3.2: added AJAX current playlist loader
add_action( 'wp_ajax_radio_station_current_playlist', 'radio_station_current_playlist' );
add_action( 'wp_ajax_nopriv_radio_station_current_playlist', 'radio_station_current_playlist' );
function radio_station_current_playlist() {

	// --- sanitize shortcode attributes ---
	$atts = radio_station_sanitize_shortcode_values( 'current-playlist' );
	if ( RADIO_STATION_DEBUG ) {
		echo "Current Playlist Shortcode Attributes: " . print_r( $atts, true );
	}

	// --- output widget contents ---
	echo '<div id="widget-contents">';
	echo radio_station_current_playlist_shortcode( $atts );
	echo '</div>';

	$js = '';
	if ( isset( $atts['instance'] ) ) {

		// --- send to parent window ---
		$js .= "widget = document.getElementById('widget-contents').innerHTML;" . PHP_EOL;
		$js .= "parent.document.getElementById('rs-current-playlist-" . esc_js( $atts['instance'] ) . "').innerHTML = widget;" . PHP_EOL;

		// --- restart countdowns ---
		if ( $atts['countdown'] ) {
			$js .= "setTimeout(function() {parent.radio_countdown();}, 2000);" . PHP_EOL;
		}

	}

	// --- filter load script ---
	$js = apply_filters( 'radio_station_current_playlist_load_script', $js, $atts );

	// --- output javascript
	if ( '' != $js ) {
		echo "<script>" . $js . "</script>";
	}

	exit;
}


// ----------------
// Countdown Script
// ----------------
// 2.3.0: added shortcode/widget countdown script
add_action( 'radio_station_countdown_enqueue', 'radio_station_countdown_enqueue' );
function radio_station_countdown_enqueue() {
	
	// 2.3.3.9: check if script is enqueued
	global $radio_station_data;
	if ( isset( $radio_station_data['countdown-script'] ) ) {
		return;
	}
	
	// --- enqueue countdown script ---
	radio_station_enqueue_script( 'radio-station-countdown', array( 'radio-station' ), true );

	// --- set countdown labels ---
	$js = "radio.labels.showstarted = '" . esc_js( __( 'This Show has started.', 'radio-station' ) ) . "';
	radio.labels.showended = '" . esc_js( __( 'This Show has ended.', 'radio-station' ) ) . "';
	radio.labels.playlistended = '" . esc_js( __( 'This Playlist has ended.', 'radio-station') ) . "';
	radio.labels.timecommencing = '" . esc_js( __( 'Commencing in', 'radio-station' ) ) . "';
	radio.labels.timeremaining = '" . esc_js( __( 'Remaining Time', 'radio-station' ) ) . "';
	";

	// --- add script inline ---
	wp_add_inline_script( 'radio-station-countdown', $js );

	// 2.3.3.9: flag script as enqueued
	$radio_station_data['countdown-script'] = true;

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
				'key'		=> 'show_active',
				'value'		=> 'on',
				'compare'	=> '=',
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
