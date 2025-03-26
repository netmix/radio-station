<?php

/* Shortcode for displaying the current song
 * Since 2.0.0
 */

// note: Master Schedule Shortcode in /includes/master-schedule.php

// === Time Shortcodes ===
// - Radio Timezone Shortcode
// - Radio Clock Shortcode
// === Archive Shortcodes ===
// - Archive List Shortcode Router
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
	
	// 2.5.0: added shortcode_atts call for filtering
	$defaults = array();
	$atts = shortcode_atts( $defaults, $atts, 'radio-timezone' );

	// --- set shortcode instance ---
	// 2.5.0: simplified instance data
	if ( !isset( $radio_station_data['instances']['timezone_shortcode'] ) ) {
		$radio_station_data['instances']['timezone_shortcode'] = 0;
	}
	$radio_station_data['instances']['timezone_shortcode']++;
	$instance = $radio_station_data['instances']['timezone_shortcode'];

	// --- get radio timezone values ---
	// $timezone = radio_station_get_setting( 'timezone_location' );
	$timezone = radio_station_get_timezone();
	if ( !$timezone || ( '' == $timezone ) ) {
		// --- fallback to WordPress timezone ---
		$timezone = get_option( 'timezone_string' );
		if ( false !== strpos( $timezone, 'Etc/GMT' ) ) {
			$timezone = '';
		}
		if ( '' == $timezone ) {
			$offset = get_option( 'gmt_offset' );
			if ( !$offset || ( 0 == $offset ) ) {
				$offset = '';
			} elseif ( $offset > 0 ) {
				$offset = '+' . $offset;
			}
			// 2.5.0: added square brackets around UTC timezone display
			$timezone_display = '[' . __( 'UTC', 'radio-station' ) . ' ' . $offset . ']';
		}
	} elseif ( strstr( $timezone, 'UTC' ) ) {
		// 2.5.0: added fallback for returned UTC timezone string
		$utc = __( 'UTC', 'radio-station' );
		if ( strstr( $timezone, 'UTC-') ) {
			$timezone_display = '[' . str_replace( 'UTC-', $utc . ' -', $timezone ) . ']';
		} else {
			$timezone_display = '[' . str_replace( 'UTC', $utc . ' +', $timezone ) . ']';
		}
	}

	if ( !isset( $timezone_display ) ) {

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
	// 2.5.0: added instance ID to timezone div wrapper 
	$output = '<div id="radio-timezone-' . esc_attr( $instance ) . '" class="radio-timezone-wrapper">' . "\n";

	// --- radio timezone ---
	$output .= '<div class="radio-timezone-title">' . "\n";
		$output .= esc_html( __( 'Radio Timezone', 'radio-station' ) ) . "\n";
	$output .= ':</div> ' . "\n";
	$output .= '<div class="radio-timezone">' . "\n";
		$output .= esc_html( $timezone_display ) . "\n";
	$output .= '</div><br>' . "\n";

	// --- user timezone ---
	// 2.3.3.9: change span elements to divs
	$output .= '<div class="radio-user-timezone-title">' . "\n";
		$output .= esc_html( __( 'Your Timezone', 'radio-station' ) ) . "\n";
	$output .= ':</div> ' . "\n";
	$output .= '<div class="radio-user-timezone"></div>' . "\n";

	// 2.3.2 allow for timezone selector test
	// $select = apply_filters( 'radio_station_timezone_select', '', 'radio-station-timezone-' . $instance, $atts );
	// if ( '' != $select ) {
	// 	$output .= $select;
	// }

	$output .= '</div>' . "\n";

	// --- enqueue shortcode styles ---
	// 2.3.2: added for timezone shortcode styles
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return ---
	$output = apply_filters( 'radio_station_timezone_shortcode', $output, $atts, $instance );
	return $output;
}

// ---------------------
// Radio Clock Shortcode
// ---------------------
add_shortcode( 'radio-clock', 'radio_station_clock_shortcode' );
function radio_station_clock_shortcode( $atts = array() ) {

	global $radio_station_data;

	// --- set shortcode instance ---
	// 2.5.0: simplified instance data
	if ( !isset( $radio_station_data['instances']['clock_shortcode'] ) ) {
		$radio_station_data['instances']['clock_shortcode'] = 0;
	}
	$radio_station_data['instances']['clock_shortcode']++;
	$instance = $radio_station_data['instances']['clock_shortcode'];

	// 2.3.3: use plugin setting if time format attribute is empty
	// 2.5.0: fix to update time attribute to time_format
	if ( isset( $atts['time'] ) ) {
		if ( '' != trim( $atts['time'] ) ) {
			$atts['time_format'] = $atts['time'];
		}
		unset( $atts['time'] );
	}

	// --- merge default attributes ---
	// 2.3.3: fix to incorrect setting key (clock_format)
	$time_format = radio_station_get_setting( 'clock_time_format' );
	$defaults = array(
		'time_format' => $time_format,
		'day'         => 'full', // full / short / none
		'date'        => 1,
		'month'       => 'full', // full / short / none
		'zone'        => 1,
		'seconds'     => 1,
		'widget'      => 0,
	);
	$atts = shortcode_atts( $defaults, $atts, 'radio-clock' );

	// --- set clock display classes ---
	$classes = array( 'radio-station-clock' );
	if ( $atts['widget'] ) {
		$classes[] = 'radio-station-clock-widget';
	} else {
		$classes[] = 'radio-station-clock-shortcode';
	}
	if ( 24 == (int) $atts['time_format'] ) {
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
	$clock = '<div id="radio-station-clock-' . esc_attr( $instance ) . '" class="' . esc_attr( $classlist ) . '">' . "\n";

		// --- server clock ---
		$clock .= '<div class="radio-station-server-clock">' . "\n";
			$clock .= '<div class="radio-clock-title">' . "\n";
				$clock .= esc_html( __( 'Radio Time', 'radio-station' ) );
			$clock .= ':</div>' . "\n";
			$clock .= '<div class="radio-server-time" data-format="' . esc_attr( $atts['time_format'] ) . '"></div>' . "\n";
			$clock .= '<div class="radio-server-date"></div>' . "\n";
			$clock .= '<div class="radio-server-zone"></div>' . "\n";
		$clock .= '</div>' . "\n";

		// --- user clock ---
		$clock .= '<div class="radio-station-user-clock">' . "\n";
			$clock .= '<div class="radio-clock-title">' . "\n";
				$clock .= esc_html( __( 'Your Time', 'radio-station' ) );
			$clock .= ':</div>' . "\n";
			$clock .= '<div class="radio-user-time" data-format="' . esc_attr( $atts['time_format'] ) . '"></div>' . "\n";
			$clock .= '<div class="radio-user-date"></div>' . "\n";
			$clock .= '<div class="radio-user-zone"></div>' . "\n";
		$clock .= '</div>' . "\n";

	$clock .= '</div>' . "\n";

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- enqueue clock javascript ---
	radio_station_enqueue_script( 'radio-station-clock', array(), true );

	// --- filter and return ---
	$clock = apply_filters( 'radio_station_clock', $clock, $atts, $instance );
	return $clock;
}


// --------------------------
// === Archive Shortcodes ===
// --------------------------

// -----------------------------
// Archive List Shortcode Router
// -----------------------------
function radio_station_archive_list( $atts ) {
	
	// echo '<span style="display:none;">' . print_r( $atts, true ) . '</span>';
	
	if ( 'shows' == $atts['archive_type'] ) {
		return radio_station_show_archive_list( $atts );
	} elseif ( 'overrides' == $atts['archive_type'] ) {
		return radio_station_override_archive_list( $atts );
	} elseif ( 'playlists' == $atts['archive_type'] ) {
        return radio_station_playlist_archive_list( $atts );
	} elseif ( 'genres' == $atts['archive_type'] ) {
		return radio_station_genre_archive_list( $atts );
	} elseif ( 'languages' == $atts['archive_type'] ) {
        return radio_station_language_archive_list( $atts );
	}

	// --- filter and return ---
	$output = apply_filters( 'radio_station_archive_list', '', $atts );
	return $output;
}

// -------------------------------
// Archive List Shortcode Abstract
// -------------------------------
// (handles Shows, Overrides, Playlists etc.)
function radio_station_archive_list_shortcode( $post_type, $atts ) {

	global $radio_station_data;

	// --- set type from post type ---
	$type = str_replace( 'rs-', '', $post_type );

	// --- set shortcode instance ---
	if ( !isset( $radio_station_data['instances'][$type . '-archive-list'] ) ) {
		$radio_station_data['instances'][$type . '-archive-list'] = 0;
	}
	$radio_station_data['instances'][$type . '-archive-list']++;
	$instance = $radio_station_data['instances'][$type . '-archive-list'];

	// --- get clock time format ---
	$time_format = radio_station_get_setting( 'clock_time_format' );

	// 2.5.0: fix for old settings names
	if ( isset( $atts['time'] ) ) {
		if ( '' != trim( $atts['time'] ) ) {
			$atts['time_format'] = $atts['time'];
		}
		unset( $atts['time'] );
	}

	// --- merge defaults with passed attributes ---
	// 2.3.3.9: add atts for specific posts
	// 2.4.0.4: added optional view attribute
	// 2.4.1.8: change default view value to list
	$defaults = array(
		// --- shortcode display ----
		'description'  => 'excerpt',
		'hide_empty'   => 0,
		'time_format'  => $time_format,
		'view'         => 'list',
		// --- taxonomy queries ---
		'genre'        => '',
		'language'     => '',
		// --- query args ---
		'orderby'      => 'title',
		'order'        => 'ASC',
		'status'       => 'publish',
		'perpage'      => -1,
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
		// 'display_tracks' => 0,
		// --- specific posts ---
		'show'         => false,
		'override'     => false,
		'playlist'     => false,
	);

	// 2.4.1.8: change default description value for grid view
	if ( isset( $atts['view'] ) && ( 'grid' == $atts['view'] ) ) {
		$defaults['description'] = 'none';
	}

	// --- handle possible pagination offset ---
	// 2.5.0: fix to work by offset
	if ( isset( $atts['perpage'] ) && ( $atts['perpage'] > 0 ) ) {
		if ( !isset( $atts['offset'] ) ) {
			if ( isset( $_REQUEST['offset'] ) ) {
				$atts['offset'] = absint( $_REQUEST['offset'] );
			} elseif  ( get_query_var( 'page' ) ) {
				$page = absint( get_query_var( 'page' ) );
				if ( $page > -1 ) {
					$atts['offset'] = (int) $atts['perpage'] * $page;
				}
			}
		}
	}

	// --- process shortcode attributes ---
	$atts = shortcode_atts( $defaults, $atts, $post_type . '-archive' );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">' . esc_html( $type ) . ' Archive Shortcode Atts: ' . esc_html( print_r( $atts, true ) ) . '</span>' . "\n";
	}

	// --- get published shows ---
	// 2.3.3.9: ignore offset and limit and reapply later
	$args = array(
		'post_type'   => $post_type,
		'post_status' => $atts['status'],
		'numberposts' => -1,
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

	// 2.5.0: added missing override meta query check
	if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
		if ( $atts['with_shifts'] ) {
			$args['meta_query'] = array(
				array(
					'key'		=> 'show_override_sched',
					'compare'	=> 'EXISTS',
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
	// 2.5.0: added atts as second argument to filters
	$args = apply_filters( 'radio_station_' . $type . '_archive_post_args', $args, $atts );
	$archive_posts = get_posts( $args );
	
	// --- process playlist taxonomy query ---
	if ( RADIO_STATION_PLAYLIST_SLUG == $post_type ) {
		// 2.3.3.9: added missing check for matching archive post results	
		if ( $archive_posts && is_array( $archive_posts ) && ( count( $archive_posts ) > 0 ) ) {

			// --- check assigned show has a specified genre term ---
			if ( !empty( $atts['genre'] ) && $archive_posts && ( count( $archive_posts ) > 0 ) ) {
				if ( is_array( $atts['genre'] ) ) {
					$genres = $atts['genre'];
				} else {
					$genres = explode( ',', $atts['genre'] );
				}
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
					// 2.5.8 : fix to double count bug
					if ( 0 == $enabled ) {
						unset( $archive_posts[$i] );
					}
				}
			}
		}
	}

	// 2.5.0: move archive posts filter to after show taxonomy/override date checks
	$archive_posts = apply_filters( 'radio_station_' . $type . '_archive_posts', $archive_posts, $atts );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Archive Shortcode: ' . "\b";
			echo 'Args: ' . esc_html( print_r( $args, true ) ) . "\n";
			echo 'Posts: ' . esc_html( print_r( $archive_posts, true ) ) . "\n";
		echo '</span>' . "\n";
	}

	// --- set time data formats ---
	// 2.3.0: added once-off meridiem pre-conversions
	// 2.3.2: replaced meridiem conversions with data formats
	// 2.4.0.6: added filter for default time format separator
	$time_separator = ':';
	$time_separator = apply_filters( 'radio_station_time_separator', $time_separator, $post_type . '-archive', $atts );
	if ( 24 == (int) $atts['time_format'] ) {
		$start_data_format = $end_data_format = 'H' . $time_separator . 'i';
	} else {
		$start_data_format = $end_data_format = 'g' . $time_separator . 'i a';
	}
	$start_data_format = 'j F, ' . $start_data_format;
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
		// 2.5.0: fix by removing post count resets
		if ( ( $atts['offset'] > 0 ) && ( $atts['perpage'] > 0 ) ) {
			if ( $post_count > $atts['offset'] ) {
				$offset_posts = array();
				foreach ( $archive_posts as $i => $archive_post ) {
					if ( count( $offset_posts ) < $atts['perpage'] ) {
						if ( ( $i + 1 ) > $atts['offset'] ) {
							$offset_posts[] = $archive_post;
						}
					}
				}
				$archive_posts = $offset_posts;
				if ( RADIO_STATION_DEBUG ) {
					echo '<span style="display:none;">Offset Archive Posts: ' . esc_html( print_r( $archive_posts ) ) . '</span>' . "\n";
				}
			} else {
				$archive_posts = array();
			}
		} elseif ( ( $atts['perpage'] > 0 ) && ( $post_count > $atts['perpage'] ) ) {
			// 2.5.0: fix to per page pagination
			foreach ( $archive_posts as $i => $archive_post ) {
				if ( ( $i + 1 ) > $atts['perpage'] ) {
					unset( $archive_posts[$i] );
				}
			}
		}
	}

	// --- output list or no results message ---
	// 2.4.0.4: remove rs- prefix from element classes
	// 2.4.0.4: maybe add view class
	// 2.5.0: add generic radio-archives class
	// 2.5.0: always add view class
	$classes = array( 'radio-archive', $type . '-archives' );
	$classes[] = $atts['view'];
	$class_list = implode( ' ', $classes );
	// 2.5.0: add element ID for archive shortcode instance
	$list = '<div id="' . esc_attr( $type ) . '-archives-' . esc_attr( $instance ) . '" class="' . esc_attr( $class_list ) . '">';
	if ( !$archive_posts || !is_array( $archive_posts ) || ( count( $archive_posts ) == 0 ) ) {

		// --- no shows messages ----
		$message = '';
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
		// 2.5.0: added generic radio-archive-list class
		$list .= '<ul class="radio-archive-list ' . esc_attr( $type ) . '-archive-list">' . "\n";

		// --- set info keys ---
		// (note: meta is dates for overrides, shifts for shows, tracks for playlists etc.)
		$infokeys = array( 'avatar', 'title', 'meta', 'genres', 'languages', 'description', 'custom' );
		$infokeys = apply_filters( 'radio_station_archive_shortcode_info_order', $infokeys, $post_type, $atts );

		// --- loop post archive ---
		foreach ( $archive_posts as $i => $archive_post ) {

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

			$list .= '<li class="' . esc_attr( $type ) . '-archive-item">' . "\n";

			// --- show avatar or thumbnail fallback ---
			$info['avatar'] = '<div class="' . esc_attr( $type ) . '-archive-item-thumbnail">' . "\n";
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
			$info['avatar'] .=  "\n" . '</div>' . "\n";

			// --- title ----
			$info['title'] = '<div class="' . esc_attr( $type ) . '-archive-item-title">' . "\n";
				$info['title'] .= '<a href="' . esc_url( $permalink ) . '">' . "\n";
					$info['title'] .= esc_html( $title ) . "\n";
				$info['title'] .= '</a>' . "\n";
			$info['title'] .= '</div>' . "\n";

			// 2.5.0: added meta div open wrapper
			$info['meta'] = '<div class="' . esc_attr( $type ) . '-archive-item-meta">' . "\n";

			// --- display Override date(s) ---
			if ( ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) && ( $atts['show_dates'] ) ) {

				// 2.3.3.9: set third attribute to false to allow for multiple override times
				$override_times = get_post_meta( $archive_post->ID, 'show_override_sched', true );
				// 2.3.3.9: convert possible single value to array
				if ( $override_times && is_array( $override_times ) && array_key_exists( 'date', $override_times ) ) {
					$override_times = array( $override_times );
				}

				// 2.3.1: fix to append not echo override date to archive list
				$info['meta'] .= '<div class="override-archive-date">' . "\n";

				foreach ( $override_times as $override_time ) {

					// 2.3.3.9: added check if override time/date is disabled
					if ( !isset( $override_time['disabled'] ) || ( 'yes' != $override_time['disabled'] ) ) {

						// --- convert date info ---
						// 2.3.2: replace strtotime with to_time for timezones
						// 2.3.2: fix to convert to 24 hour format first
						$date_time = radio_station_to_time( $override_time['date'] );
						// $day = radio_station_get_time( 'day', $date_time );
						// $display_day = radio_station_translate_weekday( $day );
						$start = $override_time['start_hour'] . ':' . $override_time['start_min'] . ' ' . $override_time['start_meridian'];
						$end = $override_time['end_hour'] . ':' . $override_time['end_min'] . ' ' . $override_time['end_meridian'];
						$start_time = radio_station_convert_shift_time( $start );
						$end_time = radio_station_convert_shift_time( $end );
						// 2.5.8: fix to use date instead of day key
						$shift_start_time = radio_station_to_time( $override_time['date'] . ' ' . $start_time );
						$shift_end_time = radio_station_to_time( $override_time['date'] . ' ' . $end_time );
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

						// 2.4.0.6: use filtered shift separator
						$separator =  ' - ';
						$separator = apply_filters( 'radio_station_show_times_separator', $separator, 'override' );

						// 2.3.1: fix to append not echo override date to archive list
						$info['meta'] .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
						$info['meta'] .= '<span class="rs-sep rs-shift-sep">' . esc_html( $separator ) . '</span>' . "\n";
						$info['meta'] .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";
						$info['meta'] .= '<br>' . "\n";

					}
				}

				$info['meta'] .= '</div>' . "\n";
			}

			// TODO: display Show shifts meta 
			/* if ( ( RADIO_STATION_SHOW_SLUG == $post_type ) && ( $atts['show_shifts'] ) ) {
				$shifts = radio_station_get_show_schedule( $post_id );
				if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
					foreach ( $shifts as $i => $shift ) {
						$info['meta'] .= '';
					}
				}
			} */

			// TODO: playlist tracks / track count meta
			/* if ( ( RADIO_STATION_PLAYLIST_SLUG == $post_type ) && $atts['display_tracks'] ) {
			 	$tracks = get_post_meta( $post_id, 'playlist', true );
				if ( $tracks && is_array( $tracks ) && ( count( $tracks ) > 0 ) ) {
					$track_count = count( $tracks );
					foreach ( $tracks as $i => $track ) {
						$info['meta'] = '';
					}
				}
			} */
			
			// 2.3.3.9: filter meta display for different post types
			// 2.5.0: added meta div close wrapper
			$info['meta'] .= '</div>';
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
			// 2.4.1.8: set different grid default earlier instead
			if ( 'none' == $atts['description'] ) {
				$info['description'] = '';
			} elseif ( 'full' == $atts['description'] ) {
				$content = apply_filters( 'radio_station_' . $type . '_archive_content', $post_content, $post_id );
				$info['description'] = '<div class="' . esc_attr( $type ) . '-archive-item-content">' . "\n";
					$info['description'] .= $content . "\n";
				$info['description'] .= '</div>' . "\n";
			} else {
				if ( !empty( $post_excerpt ) ) {
					$excerpt = $post_excerpt;
					$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
				} else {
					$excerpt = radio_station_trim_excerpt( $post_content, $length, $more, $permalink );
				}
				$excerpt = apply_filters( 'radio_station_' . $type . '_archive_excerpt', $excerpt, $post_id );
				$info['description'] = '<div class="' . esc_attr( $type ) . '-archive-item-excerpt">' . "\n";
					$info['description'] .= $excerpt . "\n";
				$info['description'] .= '</div>' . "\n";
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

			$list .= '</li>' . "\n";
		}
		$list .= '</ul>' . "\n";
	}
	$list .= '</div>' . "\n";

 	// --- add archive_pagination ---
	if ( $atts['pagination'] && ( $atts['perpage'] > 0 ) && ( $post_count > 0 ) ) {
		if ( $post_count > $atts['perpage'] ) {
			$list .= radio_station_archive_pagination( $instance, $post_type, $atts, $post_count );
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
// Override Archive Shortcode
// --------------------------
add_shortcode( 'override-archive', 'radio_station_override_archive_list' );
add_shortcode( 'overrides-archive', 'radio_station_override_archive_list' );
function radio_station_override_archive_list( $atts ) {
	return radio_station_archive_list_shortcode( RADIO_STATION_OVERRIDE_SLUG, $atts );
}

// --------------------------
// Playlist Archive Shortcode
// --------------------------
add_shortcode( 'playlist-archive', 'radio_station_playlist_archive_list' );
add_shortcode( 'playlists-archive', 'radio_station_playlist_archive_list' );
function radio_station_playlist_archive_list( $atts ) {
	return radio_station_archive_list_shortcode( RADIO_STATION_PLAYLIST_SLUG, $atts );
}

// -----------------------
// Genre Archive Shortcode
// -----------------------
add_shortcode( 'genre-archive', 'radio_station_genre_archive_list' );
add_shortcode( 'genres-archive', 'radio_station_genre_archive_list' );
function radio_station_genre_archive_list( $atts ) {

	global $radio_station_data;

	// --- set shortcode instance ---
	// 2.5.0: added for consistency
	if ( !isset( $radio_station_data['instances']['genre-archive-list'] ) ) {
		$radio_station_data['instances']['genre-archive-list'] = 0;
	}
	$radio_station_data['instances']['genre-archive-list']++;
	$instance = $radio_station_data['instances']['genre-archive-list'];

	// 2.3.3.9: default show description display to on
	// 2.5.0: added view attribute for grid view
	$defaults = array(
		// --- genre display options ---
		'genres'       => '',
		'link_genres'  => 1,
		'genre_desc'   => 1,
		'genre_images' => 1,
		'view'         => 'list',
		'image_width'  => 150,
		'hide_empty'   => 1,
		'pagination'   => 1,
		// --- query args ---
		'status'       => 'publish',
		'perpage'      => -1,
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

	// 2.5.0: change show description default for grid view
	if ( isset( $atts['view'] ) && ( 'grid' == $atts['view'] ) ) {
		$defaults['show_desc'] = 0;
	}

	// --- handle possible pagination offset ---
	// 2.5.0: fix to work by offset
	if ( isset( $atts['perpage'] ) && ( $atts['perpage'] > 0 ) ) {
		if ( !isset( $atts['offset'] ) ) {
			if ( isset( $_REQUEST['offset'] ) ) {
				$atts['offset'] = absint( $_REQUEST['offset'] );
			} elseif  ( get_query_var( 'page' ) ) {
				$page = absint( get_query_var( 'page' ) );
				if ( $page > -1 ) {
					$atts['offset'] = (int) $atts['perpage'] * $page;
				}
			}
		}
	}

	// --- process shortcode attributes ---
	$atts = shortcode_atts( $defaults, $atts, 'genre-archive' );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Genre Archive Shortcode Atts: ' . esc_html( print_r( $atts, true ) ) . '</span>' . "\n";
	}

	// --- maybe get specified genre(s) ---
	// 2.5.0: also gather genres ID array
	$genres = $genre_ids = array();
	if ( '' != trim( $atts['genres'] ) ) {
		$genre_slugs = explode( ',', $atts['genres'] );
		foreach ( $genre_slugs as $genre_slug ) {
			$genre_slug = trim( $genre_slug );
			// 2.5.6: fix to get genre by genre_slug
			$genre = radio_station_get_genre( $genre_slug );
			if ( $genre ) {
				// 2.5.10: fix to get first array key
				$name = array_key_first( $genre );
				$genres[$name] = $genre[$name];
				$genre_ids[] = $genre[$name]['id'];
			}
		}
	} else {
		// --- get all genres ---
		$args = array();
		if ( !$atts['hide_empty'] ) {
			$args['hide_empty'] = false;
		}
		$genres = radio_station_get_genres( $args );
		foreach ( $genres as $genre ) {
			$genre_ids[] = $genre['id'];
		}
	}

	// --- check if we have genres ---
	// 2.5.0: improve logic to allow filtering
	if ( 0 === count( $genres ) ) {

		if ( $atts['hide_empty'] ) {
			$list = '';
		} else {
			$list = '<div id="genres-archive-' . esc_attr( $instance ) . '" class="genres-archive">' . "\n";
				$list .= esc_html( __( 'No Genres were found to display.', 'radio-station' ) ) . "\n";
			$list .= '</div>' . "\n";
		}
		$list = apply_filters( 'radio_station_genre_archive_list', $list, $atts, $instance );
		return $list;

	}

	// --- get published shows ---
	// TODO: also display Overrides in Genre archive list ?
	$args = array(
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		// 'numberposts' => $atts['perpage'],
		// 'offset'      => $atts['offset'],
		'numberposts' => -1,
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
	// 2.5.0: get all shows with a genre term assigned
	$args['tax_query'] = array(
		array(
			'taxonomy' => RADIO_STATION_GENRES_SLUG,
			'field'    => 'term_id',
			'terms'    => $genre_ids,
			// 'field'    => 'slug',
			// 'terms'    => $genre['slug'],
		),
	);

	// --- get shows in genre ---
	// 2.5.0: added shortcode atts as second filter argument
	$args = apply_filters( 'radio_station_genre_archive_post_args', $args, $atts );
	$posts = get_posts( $args );
	$posts = apply_filters( 'radio_station_genre_archive_posts', $posts, $atts );
	$post_count = count( $posts );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Genre Archive Args: ' . esc_html( print_r( $args, true ) ) . "\n";
		echo 'Genre Archive Shows: ' . esc_html( print_r( $posts, true ) ) . '</span>' . "\n";
	}

	// --- get genre terms for each post ---
	$genre_posts = array();
	if ( $posts && is_array( $posts ) && ( $post_count > 0 ) ) {
		foreach ( $posts as $i => $post ) {
			$post_terms[$i] = wp_get_post_terms( $post->ID, RADIO_STATION_GENRES_SLUG );
			if ( $post_terms[$i] ) {
				foreach ( $genres as $genre ) {
					foreach ( $post_terms[$i] as $term ) {
						if ( $genre['id'] == $term->term_id ) {
							if ( isset( $genre_posts[$genre['id']] ) ) {
								if ( !in_array( $post->ID, $genre_posts[$genre['id']] ) ) {
									$genre_posts[$genre['id']][] = $post->ID;
								}
							} else {
								$genre_posts[$genre['id']] = array( $post->ID );
							}
						}
					}
				}
			}
		}
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Genre Posts: ' . esc_html( print_r( $genre_posts, true ) ) . '</span>' . "\n";
		}
	} else {

		// 2.5.0: added no shows with genres message
		$list = '<div id="genres-archive-' . esc_attr( $instance ) . '" class="genres-archive">' . "\n";
			$list .= esc_html( __( 'No Shows were found with Genres assigned.', 'radio-station' ) ) . "\n";
		$list .= '</div>' . "\n";
		$list = apply_filters( 'radio_station_genre_archive_list', $list, $atts, $instance );
		return $list;
			
	}

	// 2.5.0: added id attribute with instance
	// 2.5.0: add view attribute to class list
	$list = '<div id="genres-archive-' . esc_attr( $instance ) . '" class="genres-archive ' . esc_attr( $atts['view'] ) . '">' . "\n";

	// --- loop genres ---
	// 2.5.0: track post display count
	$display_count = 1;
	$start = ( isset( $atts['offset'] ) && ( $atts['offset'] > 0 ) ) ? ( $atts['offset'] ) : 0;
	$end = ( $atts['perpage'] > -1 ) ? ( $start + $atts['perpage'] + 1 ) : 999999;	
	foreach ( $genres as $name => $genre ) {

		$list .= '<div class="genre-archive">' . "\n";

		// 2.5.0: modified to check for shows in this genre
		$heading = '';
		if ( !isset( $genre_posts[$genre['id']] ) ) {

			if ( !$atts['hide_empty'] ) {

				// --- genre image ---
				$genre_image = apply_filters( 'radio_station_genre_image', false, $genre );
				if ( $genre_image ) {
					$list .= '<div class="genre-image-wrapper"';
					if ( absint( $atts['image_width'] ) > 0 ) {
						$list .= ' style="width: ' . esc_attr( absint( $atts['image_width'] ) ) . 'px"';
					}
					$list .= '>' . "\n";
						// 2.5.0: added wp_kses on image output
						// 2.5.6: fix to add missing allowed variable
						$allowed = radio_station_allowed_html( 'media', 'image' );
						$list .= wp_kses( $genre_image, $allowed );
					$list .= '</div>' . "\n";
				}

				// --- genre title ---
				// 2.5.10: change div class to genre-title-wrapper
				$list .= '<div class="genre-title-wrapper"><h3 class="genre-title">' . "\n";
				if ( $atts['link_genres'] ) {
					// 2.5.10: added genre-link class
					$list .= '<a class="genre-link" href="' . esc_url( $genre['url'] ) . '">';
						$list .= esc_html( $genre['name'] );
					$list .= '</a>' . "\n";
				} else {
					$list .= esc_html( $genre['name'] ) . "\n";
				}
				$list .= '</h3></div>' . "\n";

				// --- no shows messages ----
				$list .= esc_html( __( 'No Shows in this Genre.', 'radio-station' ) ) . "\n";

			}
			
		} else { 

			// --- genre image ---
			$genre_image = apply_filters( 'radio_station_genre_image', false, $genre );
			if ( $genre_image ) {
				$heading = '<div class="genre-image-wrapper"';
				if ( absint( $atts['image_width'] ) > 0 ) {
					$heading .= ' style="width: ' . esc_attr( absint( $atts['image_width'] ) ) . 'px"';
				}
				$heading .= '>' . "\n";
					// 2.5.0: added wp_kses on image output
					// 2.5.6: fix to add missing allowed variable
					$allowed = radio_station_allowed_html( 'media', 'image' );
					$heading .= wp_kses( $genre_image, $allowed );
				$heading .= '</div>' . "\n";
			}

			// --- genre title ---
			// 2.5.10: change div class to genre-title-wrapper
			$heading .= '<div class="genre-title-wrapper"><h3 class="genre-title">' . "\n";
			if ( $atts['link_genres'] ) {
				// 2.5.10: added genre-link class
				$heading .= '<a class="genre-link" href="' . esc_url( $genre['url'] ) . '">';
					$heading .= esc_html( $genre['name'] );
				$heading .= '</a>' . "\n";
			} else {
				$heading .= esc_html( $genre['name'] ) . "\n";
			}
			$heading .= '</h3></div>' . "\n";
				
			// --- genre description ---
			if ( $atts['genre_desc'] && !empty( $genre['genre_desc'] ) ) {
				$heading .= '<div class="genre-description">' . "\n";
					// 2.5.0: added wp_kses on description output
					$allowed = radio_station_allowed_html( 'content', 'description' );
					$heading .= wp_kses( $genre['description'], $allowed ) . "\n";
				$heading .= '</div>' . "\n";
			}

			// --- filter excerpt length and more ---
			// 2.3.3.9: added for show description excerpts
			$length = apply_filters( 'radio_station_genre_archive_excerpt_length', false );
			$more = apply_filters( 'radio_station_genre_archive_excerpt_more', '[&hellip;]' );

			$items = array();
			foreach ( $posts as $post ) {

				if ( in_array( $post->ID, $genre_posts[$genre['id']] ) ) {

					// echo $name . ' >>' . $start . ' - ' . $display_count . ' - ' . $end . '<< <br>' . "\n";
					if ( ( $display_count > $start ) && ( $display_count < $end ) ) {				

						$item = '<li class="show-archive-item">' . "\n";

						// --- show avatar or thumbnail fallback ---
						$item .= '<div class="show-archive-item-thumbnail"';
						if ( absint( $atts['avatar_width'] ) > 0 ) {
							$item .= ' style="width: ' . esc_attr( absint( $atts['avatar_width'] ) ) . 'px"';
						}
						$item .= '>' . "\n";
						$show_avatar = false;
						if ( $atts['show_avatars'] ) {
							$attr = array( 'class' => 'show-thumbnail-image' );
							$show_avatar = radio_station_get_show_avatar( $post->ID, 'thumbnail', $attr );
						}
						if ( $show_avatar ) {
							// 2.5.0: added wp_kses on show avatar output
							$allowed = radio_station_allowed_html( 'media', 'image' );
							$item .= wp_kses( $show_avatar, $allowed ). "\n";
						} elseif ( $atts['thumbnails'] ) {
							if ( has_post_thumbnail( $post->ID ) ) {
								$attr = array( 'class' => 'show-thumbnail-image' );
								$thumbnail = get_the_post_thumbnail( $post->ID, 'thumbnail', $attr );
								// 2.5.0: added wp_kses on thumbnail outputting
								$allowed = radio_station_allowed_html( 'media', 'image' );
								$item .= wp_kses( $thumbnail, $allowed ) . "\n";
							}
						}
						$item .= '</div>' . "\n";

						// --- show title ----
						$permalink = get_permalink( $post->ID );
						$item .= '<div class="show-archive-item-title">' . "\n";
							$item .= '<a href="' . esc_url( $permalink ) . '">' . "\n";
								// 2.5.0: replaced esc_attr with esc_html
								$item .= esc_html( $post->post_title ) . "\n";
							$item .= '</a>' . "\n";
						$item .= '</div>' . "\n";

						// --- show excerpt ---
						// 2.2.3.9: display show description
						if ( $atts['show_desc' ] ) {
							if ( !empty( $post->post_excerpt ) ) {
								$excerpt = $post->post_excerpt;
								// 2.5.0: added missing esc_html on more anchor
								$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . esc_html( $more ) . '</a>';
							} else {
								$excerpt = radio_station_trim_excerpt( $post->post_content, $length, $more, $permalink );
							}
							$excerpt = apply_filters( 'radio_station_genre_archive_excerpt', $excerpt, $post->ID );

							if ( '' != $excerpt ) {
								$item .= '<div class="show-archive-item-description">' . "\n";
									// 2.5.0: use wp_kses on excerpt output
									$allowed = radio_station_allowed_html( 'content', 'excerpt' );
									$item .= wp_kses( $excerpt, $allowed ) . "\n";
								$item .= '</div>' . "\n";
							}
						}

						$item .= '</li>' . "\n";
						$items[] = $item;
						
					}
					
					$display_count++;

				}
			}
			
			// --- show archive list ---
			if ( count( $items ) > 0 ) {
				$list .= $heading;
				$list .= '<ul class="show-archive-list">' . "\n";
					$list .= implode( "\n", $items );
				$list .= '</ul>' . "\n";
			}
		}
		$list .= '</div>' . "\n";
	}

	$list .= '</div>' . "\n";

 	// --- add archive_pagination ---
 	// 2.5.0: enable genre archive list pagination
	if ( $atts['pagination'] && ( $atts['perpage'] > 0 ) && ( $post_count > 0 ) ) {
		if ( $post_count > $atts['perpage'] ) {
			$list .= radio_station_archive_pagination( $instance, 'genre', $atts, $post_count );
		}
	}

	// --- enqueue pagination javascript ---
	add_action( 'wp_footer', 'radio_station_archive_pagination_javascript' );

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return ---
	$list = apply_filters( 'radio_station_genre_archive_list', $list, $atts, $instance );
	return $list;
}

// --------------------------
// Language Archive Shortcode
// --------------------------
// 2.3.3.9: add Languages Archive Shortcode
add_shortcode( 'language-archive', 'radio_station_language_archive_list' );
add_shortcode( 'languages-archive', 'radio_station_language_archive_list' );
function radio_station_language_archive_list( $atts ) {

	global $radio_station_data;

	// --- set shortcode instance ---
	// 2.5.0: set shortcode instance for consistency
	if ( !isset( $radio_station_data['instances']['language-archive-list'] ) ) {
		$radio_station_data['instances']['language-archive-list'] = 0;
	}
	$radio_station_data['instances']['language-archive-list']++;
	$instance = $radio_station_data['instances']['language-archive-list'];

	// TODO: attribute in include/exclude main language term ?
	$defaults = array(
		// --- language display options ---
		'languages'       => '',
		'link_languages'  => 1,
		'language_desc'   => 1,
		'view'            => 'list',
		'hide_empty'      => 1,
		'pagination'      => 1,
		// --- query args ---
		'status'          => 'publish',
		'perpage'         => -1,
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

	// 2.5.0: change show description default for grid view
	if ( isset( $atts['view'] ) && ( 'grid' == $atts['view'] ) ) {
		$defaults['show_desc'] = 0;
	}

	// --- handle possible pagination offset ---
	// 2.5.0: fix to work by offset
	if ( isset( $atts['perpage'] ) && ( $atts['perpage'] > 0 ) ) {
		if ( !isset( $atts['offset'] ) ) {
			if ( isset( $_REQUEST['offset'] ) ) {
				$atts['offset'] = absint( $_REQUEST['offset'] );
			} elseif  ( get_query_var( 'page' ) ) {
				$page = absint( get_query_var( 'page' ) );
				if ( $page > -1 ) {
					$atts['offset'] = (int) $atts['perpage'] * $page;
				}
			}
		}
	}

	// --- process shortcode attributes ---
	$atts = shortcode_atts( $defaults, $atts, 'language-archive' );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Language Archive Shortcode Atts: ' . esc_html( print_r( $atts, true ) ) . '</span>' . "\n";
	}

	// --- maybe get specified language(s) ---
	// 2.5.0: also gather language term IDs
	$languages = $language_ids = array();
	if ( '' != trim( $atts['languages'] ) ) {
		$language_slugs = explode( ',', $atts['languages'] );
		foreach ( $language_slugs as $language_slug ) {
			$language_slug = trim( $language_slug );
			// 2.5.0: convert main language slug to get default
			if ( 'main' == $language_slug ) {
				$language_slug = false;
			}
			$language = radio_station_get_language( $language_slug );
			if ( $language ) {
				// 2.5.10: fix to get first array key
				$name = array_key_first( $language );
				$languages[$name] = $language[$name];
				$language_ids[] = $language[$name]['id'];
			}
		}
	} else {
		// --- get all languages ---
		$args = array();
		if ( !$atts['hide_empty'] ) {
			$args['hide_empty'] = false;
		}
		$languages = radio_station_get_language_terms( $args );
		// 2.5.0: merge in main language
		$main_language = radio_station_get_language();
		if ( !isset( $languages[$main_language['name']] ) ) {
			$languages[$main_language['name']] = $main_language;
		}
		foreach ( $languages as $language ) {
			$language_ids[] = $language['id'];
		}
		// echo 'Languages IDs: '; print_r( $language_ids, true );
	}

	// --- check if we have languages ---
	// 2.5.0: modified to allow filtering
	if ( 0 == count( $languages ) ) {
		if ( $atts['hide_empty'] ) {
			$list = '';
		} else {
			$list = '<div id="languages-archive-' . esc_attr( $instance ) . '" class="languages-archive">' . "\n";
				$list .= esc_html( __( 'No Languages were found to display.', 'radio-station' ) ) . "\n";
			$list .= '</div>' . "\n";
		}
		$list = apply_filters( 'radio_station_language_archive_list', $list, $atts, $instance );
		return $list;
	}

	// --- get published shows ---
	// TODO: also display Overrides in archive list ?
	$args = array(
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		// 'numberposts' => $atts['perpage'],
		// 'offset'      => $atts['offset'],
		'numberposts' => -1,
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
	// 2.5.0: get all shows with a language term assigned
	$args['tax_query'] = array(
		array(
			'taxonomy' => RADIO_STATION_LANGUAGES_SLUG,
			'field'    => 'term_id',
			'terms'    => $language_ids,
			// 'field'    => 'slug',
			// 'terms'    => $language['slug'],
		),
	);

	// --- get shows in language ---
	$args = apply_filters( 'radio_station_language_archive_post_args', $args, $atts );
	$posts = get_posts( $args );

	$posts = apply_filters( 'radio_station_language_archive_posts', $posts, $atts );
	$post_count = count( $posts );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Language Archive Args: ' . esc_html( print_r( $args, true ) ) . "\n";
		echo 'Language Archive Shows: ' . esc_html( print_r( $posts, true ) ) . '</span>' . "\n";
	}

	// --- get language terms for each post ---
	$language_posts = array();
	if ( $posts && is_array( $posts ) && ( $post_count > 0 ) ) {
		foreach ( $posts as $i => $post ) {
			$post_terms[$i] = wp_get_post_terms( $post->ID, RADIO_STATION_LANGUAGES_SLUG );
			if ( $post_terms[$i] ) {
				foreach ( $languages as $language ) {
					foreach ( $post_terms[$i] as $term ) {
						if ( $language['id'] == $term->term_id ) {
							if ( isset( $language_posts[$language['id']] ) ) {
								if ( !in_array( $post->ID, $language_posts[$language['id']] ) ) {
									$language_posts[$language['id']][] = $post->ID;
								}
							} else {
								$language_posts[$language['id']] = array( $post->ID );
							}
						}
					}
				}
			}
		}
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Language Posts: ' . esc_html( print_r( $language_posts, true ) ) . '</span>' . "\n";
		}
	} 

	// --- maybe merge in main language posts ---
	// 2.5.0: added for when no specific language is specified
	if ( '' == trim ( $atts['languages'] ) ) {
		$main_language = radio_station_get_language();
		
		$args['tax_query'][0]['operator'] = 'NOT IN';
		$main_posts = get_posts( $args );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Main Language: ' . esc_html( print_r( $main_language, true ) ) . '</span>' . "\n";
			echo '<span style="display:none;">Shows with No Language Terms: ' . esc_html( print_r( $main_posts, true ) ) . '</span>' . "\n";
		}
		
		if ( count( $main_posts ) > 0 ) {

			// --- loop to set language posts array ---
			foreach ( $main_posts as $main_post ) {
				if ( isset( $language_posts[$main_language['id']] ) ) {
					if ( !in_array( $main_post->ID, $language_posts[$main_language['id']] ) ) {
						$language_posts[$main_language['id']][] = $main_post->ID;
					}
				} else {
					$language_posts[$main_language['id']] = array( $main_post->ID );
				}
			}
			if ( RADIO_STATION_DEBUG ) {
				echo '<span style="display:none;">Combined Language Posts: ' . esc_html( print_r( $language_posts, true ) ) . '</span>' . "\n";
			}

			// --- merge and refetch (to reorder) ---
			$posts = array_merge( $posts, $main_posts );
			$post_ids = array();
			foreach ( $posts as $post ) {
				$post_ids[] = $post->ID;
			}
			if ( RADIO_STATION_DEBUG ) {
				echo '<span style="display:none;">Combined Shows: ' . esc_html( print_r( $posts, true ) ) . '</span>' . "\n";
				echo '<span style="display:none;">Combined Show IDs: ' . esc_html( print_r( $post_ids, true ) ) . '</span>' . "\n";
			}

			$args = array(
				'numberposts'    => -1,
				'post_type'      => RADIO_STATION_SHOW_SLUG,
				'orderby'        => $atts['orderby'],
				'order'          => $atts['order'],
				'include'        => $post_ids,
			);
			$posts = get_posts( $args );
			if ( RADIO_STATION_DEBUG ) {
				echo '<span style="display:none;">Sorted Shows: ' . esc_html( print_r( $posts, true ) ) . '</span>' . "\n";
			}
		}
	}
	
	// 2.5.0: added no shows with languages message
	$post_count = count( $posts );
	if ( !$posts || !is_array( $posts ) || ( 0 == $post_count ) ) {

		$list = '<div id="languages-archive-' . esc_attr( $instance ) . '" class="languages-archive">' . "\n";
			$list .= esc_html( __( 'No Shows were found with Languages assigned.', 'radio-station' ) ) . "\n";
		$list .= '</div>' . "\n";
		$list = apply_filters( 'radio_station_language_archive_list', $list, $atts, $instance );
		return $list;
			
	}

	// 2.5.0: added id attribute with instance
	// 2.5.0: add view attribute to class list
	$list = '<div id="languages-archive-' . esc_attr( $instance ) . '" class="languages-archive ' . esc_attr( $atts['view'] ) . '">' . "\n";

	// --- loop languages ---
	// 2.5.0: track post display count
	$display_count = 1;
	$start = ( isset( $atts['offset'] ) && ( $atts['offset'] > 0 ) ) ? ( $atts['offset'] ) : 0;
	$end = ( $atts['perpage'] > -1 ) ? ( $start + $atts['perpage'] + 1 ) : 999999;
	foreach ( $languages as $name => $language ) {
		
		$list .= '<div class="language-archive">' . "\n";

		// $heading = '';
		if ( !isset( $language_posts[$language['id']] ) ) {

			if ( !$atts['hide_empty'] ) {

				// --- language title ---
				$list .= '<div class="language-title"><h3 class="language-title">' . "\n";
				if ( $atts['link_languages'] ) {
					$list .= '<a href="' . esc_url( $language['url'] ) . '">' . "\n";
						$list .= esc_html( $language['name'] ) . "\n";
					$list .= '</a>' . "\n";
				} else {
					$list .= esc_html( $language['name'] )  . "\n";
				}
				$list .= '</h3></div>' . "\n";

				$list .= esc_html( __( 'No Shows in this Language.', 'radio-station' ) );

			}
		
		} else {
			
			// --- language title ---
			$heading = '<div class="language-title"><h3 class="language-title">' . "\n";
			if ( $atts['link_languages'] ) {
				$heading .= '<a href="' . esc_url( $language['url'] ) . '">' . "\n";
					$heading .= esc_html( $language['name'] ) . "\n";
				$heading .= '</a>' . "\n";
			} else {
				$heading .= esc_html( $language['name'] )  . "\n";
			}
			$heading .= '</h3></div>' . "\n";

			// --- language description ---
			if ( $atts['language_desc'] && !empty( $language['language_desc'] ) ) {
				$heading .= '<div class="language-description">' . "\n";
					// 2.5.0: use wp_kses on description output
					$allowed = radio_station_allowed_html( 'content', 'description' );
					$heading .= wp_kses( $language['description'], $allowed ) . "\n";
				$heading .= '</div>' . "\n";
			}

			// --- filter excerpt length and more ---
			$length = apply_filters( 'radio_station_language_archive_excerpt_length', false );
			$more = apply_filters( 'radio_station_language_archive_excerpt_more', '[&hellip;]' );

			$items = array();
			foreach ( $posts as $post ) {

				if ( in_array( $post->ID, $language_posts[$language['id']] ) ) {

					// echo $name . ' >>' . $start . ' - ' . $display_count . ' - ' . $end . '<< <br>' . "\n";
					if ( ( $display_count > $start ) && ( $display_count < $end ) ) {

						$item = '<li class="show-archive-item">' . "\n";

						// --- show avatar or thumbnail fallback ---
						$item .= '<div class="show-archive-item-thumbnail"';
						if ( absint( $atts['avatar_width'] ) > 0 ) {
							$item .= ' style="width: ' . esc_attr( absint( $atts['avatar_width'] ) ) . 'px"';
						}
						$item .= '>' . "\n";
						$show_avatar = false;
						if ( $atts['show_avatars'] ) {
							$attr = array( 'class' => 'show-thumbnail-image' );
							$show_avatar = radio_station_get_show_avatar( $post->ID, 'thumbnail', $attr );
						}
						if ( $show_avatar ) {
							// 2.5.0: use wp_kses on show avatar output
							$allowed = radio_station_allowed_html( 'media', 'image' );
							$item .= wp_kses( $show_avatar, $allowed ) . "\n";
						} elseif ( $atts['thumbnails'] ) {
							if ( has_post_thumbnail( $post->ID ) ) {
								$attr = array( 'class' => 'show-thumbnail-image' );
								$thumbnail = get_the_post_thumbnail( $post->ID, 'thumbnail', $attr );
								// 2.5.0: use wp_kses on thumbnail output
								$allowed = radio_station_allowed_html( 'media', 'image' );
								$item .= wp_kses( $thumbnail, $allowed ) . "\n";
							}
						}
						$item .= '</div>';

						// --- show title ----
						$permalink = get_permalink( $post->ID );
						$item .= '<div class="show-archive-item-title">' . "\n";
							$item .= '<a href="' . esc_url( $permalink ) . '">' . "\n";
								// 2.5.0: change esc_attr to esc_html
								$item .= esc_html( $post->post_title );
							$item .= '</a>' . "\n";
						$item .= '</div>' . "\n";

						// --- show excerpt ---
						if ( $atts['show_desc' ] ) {
							if ( !empty( $post->post_excerpt ) ) {
								$excerpt = $post->post_excerpt;
								// 2.5.0: use esc_html on more anchor text
								$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . esc_html( $more ) . '</a>' . "\n";
							} else {
								$excerpt = radio_station_trim_excerpt( $post->post_content, $length, $more, $permalink );
							}
							// 2.5.0: fix to incorrect filter name radio_station_genre_archive_excerpt
							$excerpt = apply_filters( 'radio_station_language_archive_excerpt', $excerpt, $post->ID );

							if ( '' != $excerpt ) {
								$item .= '<div class="show-archive-item-description">';
									// 2.5.0: use wp_kses on excerpt output
									$allowed = radio_station_allowed_html( 'content', 'excerpt' );
									$item .= wp_kses( $excerpt, $allowed );
								$item .= '</div>';
							}
						}

						$item .= '</li>' . "\n";

						$items[] = $item;
					}

					$display_count++;

				}
			}

			if ( count( $items ) > 0 ) {
				// --- show archive list ---
				$list .= $heading;
				$list .= '<ul class="show-archive-list">' . "\n";
					$list .= implode( "\n", $items );
				$list .= '</ul>' . "\n";
				
			}
		}
		$list .= '</div>' . "\n";
	}
	$list .= '</div>' . "\n";

 	// --- add archive_pagination ---
 	// 2.5.0: enable language archive list pagination
	if ( $atts['pagination'] && ( $atts['perpage'] > 0 ) && ( $post_count > 0 ) ) {
		if ( $post_count > $atts['perpage'] ) {
			$list .= radio_station_archive_pagination( $instance, 'language', $atts, $post_count );
		}
	}

	// --- enqueue pagination javascript ---
	add_action( 'wp_footer', 'radio_station_archive_pagination_javascript' );

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return ---
	$list = apply_filters( 'radio_station_language_archive_list', $list, $atts, $instance );
	return $list;
}

// ------------------
// Archive Pagination
// ------------------
function radio_station_archive_pagination( $instance, $type, $atts, $post_count ) {

	// 2.5.0: fix to current page, default to 1
	// 2.5.0: use offset value instead of page
	global $post;
	$permalink = get_permalink( $post->ID );
	$post_pages = ceil( $post_count / $atts['perpage'] );
	$current_page = ( isset( $atts['offset'] ) && ( $atts['offset'] > 0 ) ) ? (int) ( $atts['offset'] / $atts['perpage'] ) + 1 : 1;
	$prev_page = $current_page - 1;
	$next_page = $current_page + 1;

	$pagi = '<br>' . "\n";
	$pagi .= '<div class="archive-pagination-buttons archive-' . esc_attr( $type ) . 's-page-buttons">' . "\n";
		if ( $prev_page > 0 ) {
			$pagi .= '<div class="archive-pagination-button">' . "\n";
				// 2.5.6: fix to set permalink as URL for first page
				$url = $permalink;
				if ( $prev_page > 1 ) {
					$url = add_query_arg( 'offset', ( $prev_page * $atts['perpage'] ), $permalink );
				}
				$onclick = "return radio_archive_page(" . esc_attr( $instance ) . ",'" . esc_attr( $type ) . "'," . esc_attr( $prev_page ) . "');";
				$pagi .= '<a href="' . esc_url( $url ) . '" onclick="' . $onclick . '">&larr;</a>' . "\n";
			$pagi .= '</div>' . "\n";
		}
		for ( $page_num = 1; $page_num < ( $post_pages + 1 ); $page_num++ ) {
			$active = ( $current_page == $page_num ) ? ' active' : '';
			$pagi .= '<div class="archive-' . esc_attr( $type ) . 's-page-button archive-pagination-button' . esc_attr( $active ) . '">' . "\n";
				$url = $permalink;
				if ( $page_num > 1 ) {
					$offset = ( ( $page_num - 1 ) * $atts['perpage'] );
					$url = add_query_arg( 'offset', $offset, $permalink );
				}
				$onclick = "return radio_archive_page(" . esc_attr( $instance ) . ",'" . esc_attr( $type ) . "'," . esc_attr( $page_num ) . "');";
				$pagi .= '<a href="' . esc_url( $url ) . '" onclick="' . $onclick .'">' . "\n";
					$pagi .= esc_html( $page_num ) . "\n";
				$pagi .= '</a>' . "\n";
			$pagi .= '</div>' . "\n";
		}		
		if ( $next_page < ( $post_pages + 1 ) ) {
			$pagi .= '<div class="archive-pagination-button">' . "\n";
				$offset = ( ( $next_page - 1 ) * $atts['perpage'] );
				$url = add_query_arg( 'offset', $offset, $permalink );
				$onclick = "return radio_archive_page(" . esc_attr( $instance ) . ",'" . esc_attr( $type ) . "'," . esc_attr( $next_page ) . "');";
				$pagi .= '<a href="' . esc_url( $url ) . '" onclick="' . $onclick . '">&rarr;</a>' . "\n";
			$pagi .= '</div>' . "\n";
		}
	
	$pagi .= '</div>' . "\n";
	$pagi .= '<br>' . "\n";

	return $pagi;
}

// -----------------------------
// Archive Pagination Javascript
// -----------------------------
// 2.3.3.9: renamed function to distinguish from list pagination
function radio_station_archive_pagination_javascript() {

	// --- fade out current page and fade in selected page ---
	$js = "function radio_archive_page(id, types, pagenum) {
		return; /* TEMP */
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
		
		
		return false;
	}";

	// --- enqueue script inline ---
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'radio-station', $js );
}


// -------------------------------
// === Show Related Shortcodes ===
// -------------------------------

// ----------------------------
// Show List Shortcode Abstract
// ----------------------------
function radio_station_show_list_shortcode( $type, $atts ) {

	global $radio_station_data;

	// --- set shortcode instance ---
	// 2.5.0: added for consistency
	if ( !isset( $radio_station_data['instances']['show-' . $type . '-list'] ) ) {
		$radio_station_data['instances']['show-' . $type . '-list'] = 0;
	}
	$radio_station_data['instances']['show-' . $type . '-list']++;
	$instance = $radio_station_data['instances']['show-' . $type . '-list'];

	// --- get time and date formats ---
	$timeformat = get_option( 'time_format' );
	$dateformat = get_option( 'date_format' );

	// 2.5.0: fix for latest type argument
	if ( 'latest' == $type ) {
		$type = 'post';
		if ( !isset( $atts['limit'] ) ) {
			$atts['limit'] = 3;
		}
	}

	// --- get shortcode attributes ---
	$defaults = array(
		'show'       => false,
		'per_page'   => 15,
		'limit'      => 0,
		'content'    => 'excerpt',
		'thumbnails' => 1,
		'pagination' => 1,
		// 2.5.6: add ordering defaults
		'orderby'    => 'date',
		'order'      => 'DESC',
	);
	// 2.5.6: add filter for default attributes
	$defaults = apply_filters( 'radio_station_show_list_defaults_atts', $defaults, $type );
	$atts = shortcode_atts( $defaults, $atts, 'show-' . $type . '-list' );

	// --- maybe get stored post data ---
	if ( isset( $radio_station_data['show-' . $type . 's'] ) ) {

		// --- use data stored from template ---
		$posts = $radio_station_data['show-' . $type . 's'];
		unset( $radio_station_data['show-' . $type . 's'] );
		$show_id = $radio_station_data['show-id'];

		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Stored Show Posts (' . esc_html( $type ) . '):' . esc_html( print_r( $posts, true ) ) . '</span>';
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
			// 2.5.5: added filter for show posts
			$posts = radio_station_get_show_posts( $show_id, $args );
			$posts = apply_filters( 'radio_station_get_show_posts', $posts, $show_id, $args, $atts );
		} elseif ( RADIO_STATION_PLAYLIST_SLUG == $type ) {
			// 2.5.5: added filter for show playlists
			$posts = radio_station_get_show_playlists( $show_id, $args );
			$posts = apply_filters( 'radio_station_get_show_playlists', $posts, $show_id, $args, $atts );
			$type = 'playlist';
		} elseif ( RADIO_STATION_HOST_SLUG == $type ) {
			// 2.5.0: added shortcode atts as fourth argument
			$posts = apply_filters( 'radio_station_get_show_hosts', false, $show_id, $args, $atts );
			$type = 'host';
		} elseif ( RADIO_STATION_PRODUCER_SLUG == $type ) {
			// 2.5.0: added shortcode atts as fourth argument
			$posts = apply_filters( 'radio_station_get_show_producers', false, $show_id, $args, $atts );
			$type = 'producer';
		} elseif ( defined( 'RADIO_STATION_EPISODE_SLUG' ) && ( RADIO_STATION_EPISODE_SLUG == $type ) ) {
			// 2.5.0: added shortcode atts as fourth argument
			$posts = apply_filters( 'radio_station_get_show_episodes', false, $show_id, $args, $atts );
			$type = 'episode';
		}
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Show Posts (' . esc_html( $type ) . '):' . esc_html( print_r( $posts, true ) ) . '</span>';
		}
	}
	if ( !isset( $posts ) || !$posts || !is_array( $posts ) || ( count( $posts ) == 0 ) )  {
		return '';
	}

	// --- filter excerpt length and more ---
	$length = apply_filters( 'radio_station_show_' . $type . '_list_excerpt_length', false );
	$more = apply_filters( 'radio_station_show_' . $type . '_list_excerpt_more', '[&hellip;]' );

	// --- show list div ---
	$list = '<div id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-list" class="show-' . esc_attr( $type ) . 's-list">' . "\n";

	// --- loop show posts ---
	$post_pages = 1;
	$j = 0;
	foreach ( $posts as $post ) {
		$newpage = $firstpage = false;
		if ( 0 == $j ) {
			$newpage = $firstpage = true;
		} elseif ( $j == $atts['per_page'] ) {
			// --- close page div ---
			$list .= '</div>' . "\n";
			$newpage = true;
			$post_pages++;
			$j = 0;
		}
		if ( $newpage ) {
			// --- new page div ---
			$hide = !$firstpage ? ' style="display:none;"' : '';
			$list .= '<div id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page-' . esc_attr( $post_pages ) . '" class="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page"' . $hide . '>' . "\n";
		}

		// --- new item div ---
		$classes = array( 'show-' . $type );
		if ( $newpage ) {
			$classes[] = 'first-item';
		}
		$class = implode( ' ', $classes );
		$list .= '<div class="' . esc_attr( $class ) . '">' . "\n";

		// 2.3.3.9: check if this object is instance of WP_User class
		if ( is_a( $post, 'WP_User' ) ) {
		
			// --- this is a user without a profile post ---
			$user = $post;
			$user_id = $user->data->ID;

			// TODO: add check for user avatar ?
		
			$list .= '<div class="show-' . esc_attr( $type ) . '-info">' . "\n";
		
			// --- link to author page ---
			$permalink = get_author_posts_url( $user_id );
			$title = __( 'View all posts by %s', 'radio-station' );
			$title = sprintf( $title, $user->display_name ) . "\n";
			$list .= '<div class="show-' . esc_attr( $type ) . '-title">' . "\n";
				$list .= '<a href="' . esc_url( $permalink ) . '" title="' . esc_attr( $title ) . '">' . "\n";
					$list .= esc_attr( $user->display_name ) . "\n";
				$list .= '</a>' . "\n";
			$list .= '</div>' . "\n";

			// --- author bio/excerpt ---
			$userdata = get_user_meta( $user_id );
			$bio_content = $userdata->description[0];
			if ( 'none' == $atts['content'] ) {
				$list .= '';
			} elseif ( 'full' == $atts['content'] ) {
				// 2.5.6: fix to filter variable assignment content
				$bio_content = apply_filters( 'radio_station_show_' . $type . '_content', $bio_content, $user_id );
				$list .= '<div class="show-' . esc_attr( $type ) . '-content">' . "\n";
					// 2.5.0: use wp_kses on bio content output
					$allowed = radio_station_allowed_html( 'content', 'bio' );
					$list .= wp_kses( $bio_content, $allowed ) . "\n";
				$list .= '</div>' . "\n";
			} else {
				$permalink = get_author_posts_url( $user_id );
				$excerpt = radio_station_trim_excerpt( $bio_content, $length, $more, $permalink );
				$excerpt = apply_filters( 'radio_station_show_' . $type . '_excerpt', $excerpt, $user_id );
				$list .= '<div class="show-' . esc_attr( $type ) . '-excerpt">' . "\n";
					// 2.5.0: use wp_kses on excerpt output
					$allowed = radio_station_allowed_html( 'content', 'excerpt' );
					$list .= wp_kses( $excerpt, $allowed ) . "\n";
				$list .= '</div>' . "\n";
			}

			$list .= '</div>' . "\n";
		
		} else {

			// 2.5.0: fix to maybe get post object
			if ( is_object( $post ) && !is_a( $post, 'WP_Post' ) ) {
				$post = get_post( $post, ARRAY_A );
			}

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
					$list .= '<div class="show-' . esc_attr( $type ) . '-thumbnail">' . "\n";
						// 2.5.0: use wp_kses on thumbnail output
						$allowed = radio_station_allowed_html( 'media', 'image' );
						$list .= wp_kses( $thumbnail, $allowed ) . "\n";
					$list .= '</div>' . "\n";
				}
			}

			$list .= '<div class="show-' . esc_attr( $type ) . '-info">' . "\n";

			// --- link to post ---
			$permalink = get_permalink( $post['ID'] );
			$publish_time = mysql2date( $dateformat . ' ' . $timeformat, $post['post_date'], false );
			$title = __( 'Published on ', 'radio-station' ) . $publish_time;
			$list .= '<div class="show-' . esc_attr( $type ) . '-title">' . "\n";
				$list .= '<a href="' . esc_url( $permalink ) . '" title="' . esc_attr( $title ) . '">' . "\n";
					// 2.5.0: use esc_html instead of esc_attr
					$list .= esc_html( $post['post_title'] ) . "\n";
				$list .= '</a>' . "\n";
			$list .= '</div>' . "\n";

			// --- post meta ---
			// 2.5.6: add filtered post meta
			$meta = apply_filters( 'radio_station_show_' . $type . '_shortcode_meta', '', $post, $type, $atts );
			if ( '' != $meta ) {
				$allowed = radio_station_allowed_html( 'content', 'meta' );
				$list .= wp_kses( $meta, $allowed ) . "\n";
			}

			// --- post excerpt ---
			$post_content = $post['post_content'];
			$post_id = $post['ID'];
			if ( 'none' == $atts['content'] ) {
				$list .= '';
			} elseif ( 'full' == $atts['content'] ) {
				$content = apply_filters( 'radio_station_show_' . $type . '_content', $post_content, $post_id );
				$list .= '<div class="show-' . esc_attr( $type ) . '-content">' . "\n";
					// 2.5.0: use wp_kses on content output
					$allowed = radio_station_allowed_html( 'content', 'full' );
					$list .= wp_kses( $content, $allowed ) . "\n";
				$list .= '</div>' . "\n";
			} else {
				$permalink = get_permalink( $post['ID'] );
				if ( !empty( $post['post_excerpt'] ) ) {
					$excerpt = $post['post_excerpt'];
					// 2.5.0: use esc_html on more anchor text
					$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . esc_html( $more ) . '</a>' . "\n";
				} else {
					$excerpt = radio_station_trim_excerpt( $post_content, $length, $more, $permalink );
				}
				$excerpt = apply_filters( 'radio_station_show_' . $type . '_excerpt', $excerpt, $post_id );

				// 2.5.0: added check excerpt is not empty
				if ( '' != $excerpt ) {
					$list .= '<div class="show-' . esc_attr( $type ) . '-excerpt">' . "\n";
						// 2.5.0: use wp_kses on excerpt output
						$allowed = radio_station_allowed_html( 'content', 'excerpt' );
						$list .= wp_kses( $excerpt, $allowed ) . "\n";
					$list .= '</div>' . "\n";
				}
			}
			$list .= '</div>' . "\n";
		}

		// --- close item div ---
		$list .= '</div>' . "\n";
		$j ++;
	}

	// --- close last page div ---
	$list .= '</div>' . "\n";

	// --- list pagination ---
	// 2.3.3.9: fix to hide left arrow display on load
	if ( $atts['pagination'] && ( $post_pages > 1 ) ) {
		$list .= '<br>' . "\n";
		$list .= '<div id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page-buttons" class="show-pagination-buttons show-' . esc_attr( $type ) . 's-page-buttons">' . "\n";
			$list .= '<div id="show-' . esc_attr( $type ) . 's-pagination-button-left" class="show-pagination-button arrow-button-left arrow-button" onclick="radio_list_page(\'show\', ' . esc_js( $show_id ) . ', \'' . esc_js( $type ) . 's\', \'prev\');" style="display:none;">' . "\n";
				$list .= '<a href="javascript:void(0);">&larr;</a>' . "\n";
			$list .= '</div>' . "\n";
			for ( $pagenum = 1; $pagenum < ( $post_pages + 1 ); $pagenum++ ) {
				if ( 1 == $pagenum ) {
					$active = ' active';
				} else {
					$active = '';
				}
				$onclick = 'radio_list_page(\'show\', ' . esc_js( $show_id ) . ', \'' . esc_js( $type ) . 's\', ' . esc_js( $pagenum ) . ');';
				$list .= '<div id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ). 's-page-button-' . esc_attr( $pagenum ) . '" class="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page-button show-pagination-button' . esc_attr( $active ) . '" onclick="' . $onclick . '">' . "\n";
					$list .= '<a href="javascript:void(0);">' . "\n";
						$list .= esc_html( $pagenum ) . "\n";
					$list .= '</a>' . "\n";
				$list .= '</div>' . "\n";
			}
			$list .= '<div id="show-' . esc_attr( $type ) . 's-pagination-button-right" class="show-pagination-button arrow-button" onclick="radio_list_page(\'show\', ' . esc_js( $show_id ) . ', \'' . esc_js( $type ). 's\', \'next\');">' . "\n";
			$list .= '<a href="javascript:void(0);">&rarr;</a>' . "\n";
			$list .= '</div>' . "\n";
			$list .= '<input type="hidden" id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-current-page" value="1">' . "\n";
			$list .= '<input type="hidden" id="show-' . esc_attr( $show_id ) . '-' . esc_attr( $type ) . 's-page-count" value="' . esc_attr( $post_pages ) . '">' . "\n";
		$list .= '</div>' . "\n";
	}

	// --- close list div ---
	$list .= '</div>' . "\n";

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- enqueue pagination javascript ---
	add_action( 'wp_footer', 'radio_station_list_pagination_javascript' );

	// --- filter and return ---
	$list = apply_filters( 'radio_station_show_' . $type . '_list', $list, $atts, $instance );
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
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'radio-station', $js );
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
	if ( !isset( $radio_station_data['instances']['current_show'] ) ) {
		$radio_station_data['instances']['current_show'] = 0;
	}
	$radio_station_data['instances']['current_show']++;
	$instance = $radio_station_data['instances']['current_show'];

	$output = '';

	// --- set default time format ---
	$time_format = radio_station_get_setting( 'clock_time_format' );

	// 2.3.2: get default AJAX load settings
	// 2.5.0: unset empty AJAX attribute to use default
	$ajax = radio_station_get_setting( 'ajax_widgets', false );
	$ajax = ( 'yes' == $ajax ) ? 'on' : 'off';
	if ( isset( $atts['ajax'] ) && ( '' == $atts['ajax'] ) ) {
		unset( $atts['ajax'] );
	}

	// --- apply filters for dynamic reload value ---
	// 2.5.0: added instance as third filter argument
	$dynamic = apply_filters( 'radio_station_current_show_dynamic', false, $atts, $instance );
	$dynamic = $dynamic ? 1 : 0;

	// 2.3.3: use plugin setting if time format attribute is empty
	// 2.5.0: fix to update time attribute to time_format
	if ( isset( $atts['time'] ) ) {
		if ( '' != trim( $atts['time'] ) ) {
			$atts['time_format'] = $atts['time'];
		}
		unset( $atts['time'] );
	}
	
	// 2.5.0: change to default_name attribute key
	if ( isset( $atts['default_name'] ) ) {
		$atts['no_shows'] = $atts['default_name'];
		unset( $atts['default_name'] );
	}

	// --- get shortcode attributes ---
	// 2.3.0: set default default_name text
	// 2.3.0: set default time format to plugin setting
	// 2.3.2: added AJAX load attribute
	// 2.3.2: added for_time attribute
	// 2.3.3.8: added show_encore attribute (default 1)
	// 2.3.3.9: added avatar_size attribute (default thumbnail)
	// 2.3.3.9: added show_title attribute (default 1)
	// 2.5.0: removed show_title attribute (as always needed)
	// 2.5.0: change default_name key to no_shows text
	$defaults = array(
		// --- legacy options ---
		'title'          => '',
		'ajax'           => $ajax,
		'dynamic'        => $dynamic,
		'no_shows'       => '',
		'hide_empty'     => 0,
		// --- show display options ---
		'show_link'      => 1,
		'title_position' => 'right',
		'show_avatar'    => 1,
		'avatar_width'   => '',
		'avatar_size'    => 'thumbnail',
		// --- show time display options ---
		'show_sched'     => 1,
		'show_all_sched' => 0,
		'countdown'      => 0,
		'time_format'    => $time_format,
		// --- extra display options ---
		'display_hosts'  => 0,
		'link_hosts'     => 0,
		// 'display_producers' => 0,
		// 'link_producers' => 0,
		'show_desc'      => 0,
		'show_playlist'  => 1,
		'show_encore'    => 1,
		// --- widget data ---
		'widget'         => 0,
		'block'          => 0,
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
	// 2.5.10: enqueue countdown for dynamic reloading
	if ( $atts['countdown'] || $atts['dynamic'] ) {
		do_action( 'radio_station_countdown_enqueue' );
	}

	// 2.3.3: add current time override for manual testing
	if ( isset( $_GET['date'] ) && isset( $_GET['time'] ) ) {
		$date = trim( sanitize_text_field( $_GET['date'] ) );
		$time = trim( sanitize_text_field( $_GET['time'] ) );
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
			echo "<script>console.log('Override Current Time: " . esc_js( $for_time ) . "');</script>";
		}
	}

	// --- maybe do AJAX load ---
	// 2.3.2 added widget AJAX loading
	$ajax = $atts['ajax'];
	$widget = $atts['widget'];
	$ajax = apply_filters( 'radio_station_widgets_ajax_override', $ajax, 'current-show', $widget );
	if ( 'on' === (string) $ajax ) {
		if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) {

			// --- AJAX load via iframe ---
			$ajax_url = admin_url( 'admin-ajax.php' );
			$html = '<div id="rs-current-show-' . esc_attr( $instance ) . '" class="ajax-widget"></div>' . "\n";
			$html .= '<iframe id="rs-current-show-' . esc_attr( $instance ) . '-loader" src="javascript:void(0);" style="display:none;"></iframe>' . "\n";

			// --- shortcode loader script ---
			$html .= "<script>" . "\n";
				$html .= "timestamp = Math.floor( (new Date()).getTime() / 1000 );" . "\n";
				$html .= "url = '" . esc_url( $ajax_url ) . "?action=radio_station_current_show';" . "\n";
				$html .= "url += '&instance=" . esc_js( $instance ) . "';" . "\n";
				if ( RADIO_STATION_DEBUG ) {
					$html .= "url += '&rs-debug=1';";
				}
				$html .= "url += '";
				foreach ( $atts as $key => $value ) {
					// 2.5.7: fix to ensure for_time is used for timestamp
					if ( ( 'for_time' == $key ) && ( 0 == $value ) ) {
						$html .= "&for_time='+timestamp+'";
					} else {
						$value = radio_station_encode_uri_component( $value );
						$html .= "&" . esc_js( $key ) . "=" . esc_js( $value );
					}
				}
				$html .= "';" . "\n";
				$html .= "document.getElementById('rs-current-show-" . esc_js( $instance ) ."-loader').src = url;" . "\n";
			$html .= "</script>" . "\n";

			// --- enqueue shortcode styles ---
			radio_station_enqueue_style( 'shortcodes' );
			
			// 2.6.7: filter to enqueue= reloader script in current window
			if ( $atts['dynamic'] ) {
				$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'current-show', $atts, false );
			}

			return $html;
		}
	}

	// 2.3.0: maybe set float class and avatar width style
	// 2.5.0: use absint and above zero check on avatart_width
	$widthstyle = $floatclass = '';
	if ( absint( $atts['avatar_width'] ) > 0 ) {
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
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">' . "\n";
				echo 'Current Shift For Time: ' . esc_html( $atts['for_time'] ) . ' : ' . esc_html( $time ) . "\n";
				echo esc_html( print_r( $current_shift, true ) ) . "\n";
			echo '</span>' . "\n";
		}
	} else {
		$current_shift = radio_station_get_current_show();
	}

	// --- open shortcode div wrapper ---
	if ( !$atts['widget'] ) {

		// 2.3.0: add unique id to widget shortcode
		// 2.3.2: add shortcode wrap class
		// 2.5.0: change id key to shortcode NOT widget
		// 2.5.0: simplified to use existing instance count
		$id = 'current-show-shortcode-' . $instance;
		$output .= '<div id="' . esc_attr( $id ) . '" class="current-show-wrap current-show-embedded on-air-embedded dj-on-air-embedded">' . "\n";

	}

	// --- shortcode title ---
	// 2.5.0: also display title for non-shortcodes if set
	// 2.5.7: but do not display for widgets (duplication)
	if ( ( '' != $atts['title'] ) && ( 0 != $atts['title'] ) && !$atts['widget'] ) {
		// 2.3.3.9: fix class to not conflict with actual show title
		$output .= '<h3 class="current-show-shortcode-title">' . "\n";
			$output .= esc_html( $atts['title'] ) . "\n";
		$output .= '</h3>' . "\n";
	}

	// --- open current show list ---
	// 2.5.0: add countdown class for countdown script targeting
	// 2.5.10: add countdown class for dynamic reloading
	$classes = array( 'current-show-list', 'on-air-list' );
	if ( $atts['countdown'] || $atts['dynamic'] ) {
		$classes[] = 'countdown';
	}
	$class_list = implode( ' ', $classes );
	$output .= '<ul class="' . esc_attr( $class_list ) . '">' . "\n";

	// --- current shift display ---
	if ( $current_shift ) {

		// --- get time formats ---
		// 2.3.2: moved out to get once
		// 2.4.0.6: added filter for default time format separator
		$time_separator = ':';
		$time_separator = apply_filters( 'radio_station_time_separator', $time_separator, 'current-show' );
		if ( 24 == (int) $atts['time_format'] ) {
			$start_data_format = $end_data_format = 'H' . $time_separator . 'i';
		} else {
			$start_data_format = $end_data_format = 'g' . $time_separator . 'i a';
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

			$shift_display = '<div class="current-show-schedule on-air-dj-schedule">' . "\n";

			// --- show times subheading ---
			// 2.3.0: added for all shift display
			if ( $atts['show_all_sched'] ) {
				$shift_display .= '<div class="current-show-schedule-title on-air-dj-schedule-title">' . "\n";
					$shift_display .= esc_html( __( 'Show Times', 'radio-station' ) ) . "\n";
				$shift_display .= '</div>' . "\n";
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

				// 2.4.0.6: use filtered shift separator
				$separator =  ' - ';
				$separator = apply_filters( 'radio_station_show_times_separator', $separator, 'current-show' );

				// --- set shift classes ---
				// 2.4.0.6: fix for exact current time as start time
				$classes = array( 'current-show-shifts', 'on-air-dj-sched' );
				if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {
					$current_shift_start = $shift_start_time;
					$current_shift_end = $shift_end_time;
					$classes[] = 'current-shift';
					$classlist = implode( ' ', $classes );

					$current_shift_display = '<div class="' . esc_attr( $classlist ) . '">' . "\n";
						$current_shift_display .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
						$current_shift_display .= '<span class="rs-sep rs-shift-sep">' . esc_html( $separator ) . '</span>' . "\n";
						$current_shift_display .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";
					$current_shift_display .= '</div>' . "\n";

					// 2.3.3.9: add show user time div
					$current_shift_display .= '<div class="show-user-time">' . "\n";
						$current_shift_display .= '[<span class="rs-user-time rs-start-time"></span>' . "\n";
						$current_shift_display .= '<span class="rs-sep rs-shift-sep">' . esc_html( $separator ) . '</span>' . "\n";
						$current_shift_display .= '<span class="rs-user-time rs-end-time"></span>]' . "\n";
					$current_shift_display .= '</div>' . "\n";
					
				}
				$classlist = implode( ' ', $classes );

				// --- shift display output ---
				$shift_display .= '<div class="' . esc_attr( $classlist ) . '">' . "\n";
					if ( in_array( 'current-shift', $classes ) ) {
						// (this highlights the current shift item in the full schedule list)
						$shift_display .= '<ul class="current-shift-list"><li class="current-shift-list-item">' . "\n";
					}
					$shift_display .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
					$shift_display .= '<span class="rs-sep rs-shift-sep">' . esc_html( $separator ) . '</span>' . "\n";
					$shift_display .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";

					// 2.3.3.9: add show user time div
					$shift_display .= '<div class="show-user-time">' . "\n";
					$shift_display .= '[<span class="rs-user-time rs-start-time"></span>' . "\n";
					$shift_display .= '<span class="rs-sep rs-shift-sep">' . esc_html( $separator ) . '</span>' . "\n";
					$shift_display .= '<span class="rs-user-time rs-end-time"></span>]' . "\n";
					$shift_display .= '</div>';

					if ( in_array( 'current-shift', $classes ) ) {
						$shift_display .= '</li></ul>' . "\n";
					}
				$shift_display .= '</div>' . "\n";
			}

			$shift_display .= '</div>' . "\n";
		}

		// --- set clear div ---
		$html['clear'] = '<span class="radio-clear"></span>' . "\n";

		// --- set show title output ---
		// 2.3.3.9: adding show title attribute
		// 2.5.0: remove show_title attribute (as always needed)
		$title = '<div class="current-show-title on-air-dj-title">' . "\n";
		if ( $show_link ) {
			$title .= '<a href="' . esc_url( $show_link ) . '">' . "\n";
		}
		$title .= esc_html( $show['name'] ) . "\n";
		if ( $show_link ) {
			$title .= '</a>' . "\n";
		}
		$title .= '</div>' . "\n";
		// 2.3.3.8: added current show title filter
		$title = apply_filters( 'radio_station_current_show_title_display', $title, $show_id, $atts );
		if ( ( '' != $title ) && is_string( $title ) ) {
			$html['title'] = $title;
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
				$avatar = '<div class="current-show-avatar on-air-dj-avatar' . esc_attr( $floatclass ) . '" ' . $widthstyle . '>' . "\n";
				if ( $show_link ) {
					$avatar .= '<a href="' . esc_url( $show_link ) . '">' . "\n";
				}
				// 2.5.0: use wp_kses on show avatar output
				$allowed = radio_station_allowed_html( 'media', 'image' );
				$avatar .= wp_kses( $show_avatar, $allowed )  . "\n";
				if ( $show_link ) {
					$avatar .= '</a>' . "\n";
				}
				$avatar .= '</div>' . "\n";
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

					$hosts = '<div class="current-show-hosts on-air-dj-names">' . "\n";
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
			// note: this is set via the current show shift
			if ( isset( $show['encore'] ) && ( $show['encore'] ) ) {
				$encore = '<div class="current-show-encore on-air-dj-encore">' . "\n";
					$encore .= esc_html( __( 'Encore Presentation', 'radio-station' ) ) . "\n";
				$encore .= '</div>' . "\n";
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
				$output .= '<span style="display:none;">Current Playlist: ' . esc_html( print_r( $current_playlist, true ) ) . '</span>';
			}
			// 2.5.0: set empty playlist for undefined variable warning
			$playlist = '';
			if ( $current_playlist && isset( $current_playlist['playlist_url'] ) ) {
				$playlist = '<div class="current-show-playlist on-air-dj-playlist">' . "\n";
					$playlist .= '<a href="' . esc_url( $current_playlist['playlist_url'] ) . '">' . "\n";
						$playlist .= esc_html( __( 'View Playlist', 'radio-station' ) ) . "\n";
					$playlist .= '</a>' . "\n";
				$playlist .= '</div>' . "\n";
			}
			// 2.3.3.8: added playlist diplay filter
			$playlist = apply_filters( 'radio_station_current_show_playlist_display', $playlist, $show_id, $atts );
			if ( ( '' != $playlist ) && is_string( $playlist ) ) {
				$html['playlist'] = $playlist;
			}
		}

		// --- countdown timer display ---
		// 2.5.10: add countdown for dynamic reloading
		if ( isset( $current_shift_end ) && ( $atts['countdown'] || $atts['dynamic'] ) ) {
			$html['countdown'] = '<div class="current-show-countdown rs-countdown"';
			// 2.5.10: hide countdown display if disabled but dynamic enabled
			if ( $atts['dynamic'] && !$atts['countdown'] ) {
				$html['countdown'] .= ' style="display:none;"';
			}
			$html['countdown'] .= '></div>' . "\n";
		}

		// --- show description ---
		// 2.3.0: convert span to div tags for consistency
		if ( $atts['show_desc'] ) {

			// --- get show post ---
			$show_post = get_post( $show_id );
			$permalink = get_permalink( $show_id );

			// --- get show excerpt ---
			if ( !empty( $show_post->post_excerpt ) ) {
				$excerpt = $show_post->post_excerpt;
				// 2.5.0: added esc_html to more anchor text
				$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . esc_html( $more ) . '</a>';
				// $excerpt .= "<!-- Post Excerpt -->";
			} else {
				$excerpt = radio_station_trim_excerpt( $show_post->post_content, $length, $more, $permalink );
				// $excerpt . = "<!-- Trimmed Excerpt -->";
				// $excerpt .= "<!-- Post ID: " . $show_post->ID . " -->";
				// $excerpt .= "<!-- Post Content: " . $show_post->post_content . " -->";
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
				$description = '<div class="current-show-desc on-air-show-desc">' . "\n";
					// 2.5.0: use wp_kses on excerpt output
					$allowed = radio_station_allowed_html( 'content', 'excerpt' );
					$description .= wp_kses( $excerpt, $allowed ) . "\n";
				$description .= '</div>' . "\n";
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
		$output .= '<li class="current-show on-air-dj">' . "\n";

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
		$output .= '</li>' . "\n";

	} else {

		// 2.5.0: allow for hiding when empty (with filter override)
		if ( $atts['hide_empty'] ) {
			$output = apply_filters( 'radio_station_current_show_shortcode', '', $atts, $instance );
			return $output;
		}

		// --- no current show shift display ---
		// 2.5.0: change attribute key from default_name
		// 2.5.0: remove unneeded esc_html from no shows text
		// 2.5.0: allow for zero value for no text
		if ( '0' != $atts['no_shows'] ) {
			if ( '' != $atts['no_shows'] ) {
				$no_current_show = $atts['no_shows'];
			} else {
				$no_current_show = __( 'No Show currently scheduled.', 'radio-station' );
			}
			// 2.3.1: add filter for no current shows text
			$no_current_show = apply_filters( 'radio_station_no_current_show_text', $no_current_show, $atts );

			$output .= '<li class="current-show on-air-dj default-dj">' . "\n";
				// 2.5.0: use wp_kses on message output
				$allowed = radio_station_allowed_html( 'message', 'no-shows' );
				$output .= wp_kses( $no_current_show, $allowed ) . "\n";
			$output .= '</li>' . "\n";
		}

		// --- countdown timer display ---
		// 2.3.3.8: add countdown timer div regardless of no current show
		// (so timer can update when a current show starts)
		// 2.5.10: add countdown for dynamic reloading
		if ( $atts['countdown'] || $atts['dynamic'] ) {
		 	$output .= '<li><div class="current-show-countdown rs-countdown"';
			// 2.5.10: hide countdown if disabled but dynamic enabled
			if ( $atts['dynamic'] && !$atts['countdown'] ) {
				$output .= ' style="display:none;"';
			}
			$output .= '></div></li>' . "\n";
		}

	}

	// --- countdown timers ---
	if ( isset( $current_shift_end ) && ( $atts['countdown'] || $atts['dynamic'] ) ) {

		// 2.5.0: wrap countdown/dynamic data in list item
		$output .= '<li style="display:none;">' . "\n";

			// 2.3.3.9: output current time override
			if ( isset( $atts['for_time'] ) ) {
				$output .= '<input type="hidden" class="current-time-override" value="' . esc_attr( $atts['for_time'] ) . '">' . "\n";
			}

			// --- hidden inputs for current shift time ---
			$output .= '<input type="hidden" class="current-show-end" value="' . esc_attr( $current_shift_end ) . '">' . "\n";

			if ( RADIO_STATION_DEBUG ) {
				$output .= '<span style="display:none;">';
					$output .= 'Now: ' . radio_station_get_time( 'Y-m-d H:i:s', $now ) . ' (' . esc_attr( $now ) . ')' . "\n";
					$output .= 'Shift Start Time: ' . radio_station_get_time( 'Y-m-d H:i:s', $current_shift_start ) . ' (' . esc_attr( $current_shift_start ) . ')' . "\n";
					$output .= 'Shift End Time: ' . radio_station_get_time( 'Y-m-d H:i:s', $current_shift_end ) . ' (' . esc_attr( $current_shift_end ) . ')' . "\n";
					$output .= 'Remaining: ' . ( $current_shift_end - $now ) . "\n";
				$output .= '</span>';
			}

			// --- for dynamic reloading ---
			if ( $atts['dynamic'] ) {
				$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'current-show', $atts, $current_shift_end );
				if ( $dynamic ) {
					$output .= $dynamic;
				}
			}
		
		$output .= '</li>' . "\n";
	}

	// --- close current show list ---
	$output .= '</ul>' . "\n";

	// --- close shortcode div wrapper ---
	if ( !$atts['widget'] ) {
		$output .= '</div>' . "\n";
	}

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return ---
	// 2.5.0: added missing shortcode output filter
	$output = apply_filters( 'radio_station_current_show_shortcode', $output, $atts, $instance );
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
		echo '<span style="display:none;">Current Show Shortcode Attributes: ' . esc_html( print_r( $atts, true ) ) . '</span>' . "\n";
	}

	// --- output widget contents ---
	$output = radio_station_current_show_shortcode( $atts );
	echo '<div id="widget-contents">' . "\n";
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $output;
	echo '</div>' . "\n";

	$js = '';
	if ( isset( $atts['instance'] ) ) {

		// 2.5.6: maybe hide entire parent widget if empty
		if ( $atts['hide_empty'] && ( '' == trim( $output ) ) ) {

			// 2.5.7: added check that widget element exists
			$js .= "instance = parent.document.getElementById('current-show-widget-" . esc_js( $atts['instance'] ) . "');" . "\n";
			$js .= "if (instance) {instance.style.display = 'none';}" . "\n";

		} else {

			// --- send to parent window ---
			// 2.5.6: ensure parent widget is displayed
			// 2.5.7: added check that widget element exists
			$js .= "instance = parent.document.getElementById('current-show-widget-" . esc_js( $atts['instance'] ) . "');" . "\n";
			$js .= "if (instance) {instance.style.display = '';}" . "\n";
			$js .= "widget = document.getElementById('widget-contents').innerHTML;" . "\n";
			$js .= "parent.document.getElementById('rs-current-show-" . esc_js( $atts['instance'] ) . "').innerHTML = widget;" . PHP_EOL;

			// --- maybe restart countdowns ---
			// 2.5.10: also restart when dynamic enabled
			if ( $atts['countdown'] || $atts['dynamic'] ) {
				// 2.5.0: replace timeout with interval and function check
				// $js .= "setTimeout(function() {parent.radio_countdown();}, 2000);" . "\n";
				$js .= "countdown = setInterval(function() {" . "\n";
					$js .= "if (typeof parent.radio_countdown == 'function') {" . "\n";
						$js .= "parent.radio_countdown();" . "\n";
						$js .= "clearInterval(countdown);" . "\n";
					$js .= "}" . "\n";
				$js .= "}, 1000);" . "\n";
			}
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
	if ( !isset( $radio_station_data['instances']['upcoming_shows'] ) ) {
		$radio_station_data['instances']['upcoming_shows'] = 0;
	}
	$radio_station_data['instances']['upcoming_shows']++;
	$instance = $radio_station_data['instances']['upcoming_shows'];

	$output = '';

	// --- set default time format ---
	$time_format = radio_station_get_setting( 'clock_time_format' );

	// 2.3.2: get default AJAX load settings
	// 2.5.0: unset empty AJAX attribute to use default
	$ajax = radio_station_get_setting( 'ajax_widgets', false );
	$ajax =  ( 'yes' == $ajax ) ? 'on' : 'off';
	if ( isset( $atts['ajax'] ) && ( '' == $atts['ajax'] ) ) {
		unset( $atts['ajax'] );
	}

	// --- check for dynamic setting ---
	// 2.5.0: fix typo in filter name (radio_station_upcomins_shows_dynamic)
	// 2.5.0: added instance as third filter argument
	$dynamic = apply_filters( 'radio_station_upcoming_shows_dynamic', false, $atts, $instance );
	$dynamic = $dynamic ? 1 : 0;

	// 2.3.3: use plugin setting if time format attribute is empty
	// 2.5.0: fix to update time attribute to time_format
	if ( isset( $atts['time'] ) ) {
		if ( '' != trim( $atts['time'] ) ) {
			$atts['time_format'] = $atts['time'];
		}
		unset( $atts['time'] );
	}

	// 2.3.0: convert old attributes for DJs to hosts
	if ( isset( $atts['display_djs'] ) && !isset( $atts['display_hosts'] ) ) {
		$atts['display_hosts'] = $atts['display_djs'];
		unset( $atts['display_djs'] );
	}
	if ( isset( $atts['link_djs'] ) && !isset( $atts['link_hosts'] ) ) {
		$atts['link_hosts'] = $atts['link_djs'];
		unset( $atts['link_djs'] );
	}

	// 2.5.0: change to default_name attribute key
	if ( isset( $atts['default_name'] ) ) {
		$atts['no_shows'] = $atts['default_name'];
		unset( $atts['default_name'] );
	}

	// 2.3.0: set default time format to plugin setting
	// 2.3.2: added AJAX load attribute
	// 2.3.2: added for_time attribute
	// 2.3.3.8: added show_encore attribute (default 1)
	// 2.3.3.9: added show_title attribute (default 1)
	// 2.5.0: change default_name key to no_shows
	$defaults = array(
		// --- widget display options ---
		'title'             => '',
		'limit'             => 1,
		'ajax'              => $ajax,
		'dynamic'           => $dynamic,
		'no_shows'          => '',
		'hide_empty'        => 0,
		// --- show display options ---
		'show_link'         => 0,
		'title_position'    => 'right',
		'show_avatar'       => 0,
		'avatar_size'       => 'thumbnail',
		'avatar_width'      => '',
		// --- show time display ---
		'show_sched'        => 1,
		'countdown'         => 0,
		'time_format'       => $time_format,
		// --- extra display options ---
		'display_hosts'     => 0,
		'link_hosts'        => 0,
		// 'display_producers' => 0,
		// 'list_producers' => 0,
		'show_encore'       => 1,
		// --- instance data ---
		'widget'            => 0,
		'block'             => 0,
		'id'                => '',
		'for_time'          => 0,
	);
	// 2.3.0: renamed shortcode identifier to upcoming-shows
	$atts = shortcode_atts( $defaults, $atts, 'upcoming-shows' );

	// 2.3.2: enqueue countdown script earlier
	// 2.5.10: enqueue when dynamic is enabled
	if ( $atts['countdown'] || $atts['dynamic'] ) {
		do_action( 'radio_station_countdown_enqueue' );
	}

	// 2.3.3: added current time override for manual testing
	if ( isset( $_GET['date'] ) && isset( $_GET['time'] ) ) {
		$date = sanitize_text_field( trim( $_GET['date'] ) );
		$time = sanitize_text_field( trim( $_GET['time'] ) );
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
			echo "<script>console.log('Override Current Time: " . esc_js( $for_time ) . "');</script>";
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
			$html = '<div id="rs-upcoming-shows-' . esc_attr( $instance ) . '" class="ajax-widget"></div>' . "\n";
			$html .= '<iframe id="rs-upcoming-shows-' . esc_attr( $instance ) . '-loader" src="javascript:void(0);" style="display:none;"></iframe>' . "\n";

			// --- shortcode loader script ---
			$html .= "<script>" . "\n";
				$html .= "timestamp = Math.floor( (new Date()).getTime() / 1000 );" . "\n";
				$html .= "url = '" . esc_url( $ajax_url ) . "?action=radio_station_upcoming_shows';" . "\n";
				$html .= "url += '&instance=" . esc_js( $instance ) . "';" . "\n";
				if ( RADIO_STATION_DEBUG ) {
					$html .= "url += '&rs-debug=1';" . "\n";
				}
				$html .= "url += '";
				foreach ( $atts as $key => $value ) {
					// 2.5.7: fix to ensure for_time is used for timestamp
					if ( ( 'for_time' == $key ) && ( 0 == $value ) ) {
						$html .= "&for_time='+timestamp+'";
					} else {
						$value = radio_station_encode_uri_component( $value );
						$html .= "&" . esc_js( $key ) . "=" . esc_js( $value );
					}
				}
				$html .= "';" . "\n";
				$html .= "document.getElementById('rs-upcoming-shows-" . esc_js( $instance ) . "-loader').src = url;" . "\n";
			$html .= "</script>" . "\n";

			// --- enqueue shortcode styles ---
			radio_station_enqueue_style( 'shortcodes' );

			// 2.6.7: filter to enqueue reloader script in current window
			if ( $atts['dynamic'] ) {
				$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'upcoming-shows', $atts, false );
			}

			// --- filter and return ---
			$html = apply_filters( 'radio_station_upcoming_shows_shortcode_ajax', $html, $atts, $instance );
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
		$output .= '<span style="display:none;">Upcoming Shows: ' . esc_html( print_r( $shows, true ) ) . '</span>';
	}

	// --- open shortcode only wrapper ---
	if ( !$atts['widget'] ) {

		// 2.3.0: add unique id to widget shortcode
		// 2.3.2: add shortcode wrap class
		// 2.5.0: change id key to shortcodes NOT widget
		// 2.5.0: simplified to use existing instance count
		$id = 'upcoming-shows-shortcode-' . $instance;
		$output .= '<div id="' . esc_attr( $id ) . '" class="upcoming-shows-wrap upcoming-shows-embedded on-air-embedded dj-coming-up-embedded">' . "\n";

	}

	// --- shortcode title ---
	// 2.5.0: also maybe output for non-shortcodes
	// 2.5.7: but do not display for widgets (duplication)
	if ( ( '' != $atts['title'] ) && ( 0 != $atts['title'] ) && !$atts['widget'] ) {
		$output .= '<h3 class="upcoming-shows-title dj-coming-up-title">' . "\n";
			$output .= esc_html( $atts['title'] ) . "\n";
		$output .= '</h3>' . "\n";
	}

	// --- open upcoming show list ---
	// 2.5.0: added countdown class for countdown script targeting
	$classes = array( 'upcoming-shows-list', 'on-air-upcoming-list' );
	// 2.5.10: also add class when dynamic is enabled
	if ( $atts['countdown'] || $atts['dynamic'] ) {
		$classes[] = 'countdown';
	}
	$class_list = implode( ' ', $classes );
	$output .= '<ul class="' . esc_attr( $class_list ) . '">' . "\n";

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
		// 2.4.0.6: added filter for default time format separator
		$time_separator = ':';
		$time_separator = apply_filters( 'radio_station_time_separator', $time_separator, 'upcoming-shows' );
		if ( 24 == (int) $atts['time_format'] ) {
			$start_data_format = $end_data_format = 'H' . $time_separator . 'i';
		} else {
			$start_data_format = $end_data_format = 'g' . $time_separator . 'i a';
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

				// 2.4.0.6: use filtered shift separator
				$separator = ' - ';
				$separator = apply_filters( 'radio_station_show_times_separator', $separator, 'upcoming-shows' );

				// --- set shift display output ---
				$shift_display = '<div class="upcoming-show-schedule on-air-dj-schedule">';
					$shift_display .= '<div class="' . esc_attr( $class ) . '">' . "\n";
					$shift_display .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
					$shift_display .= '<span class="rs-sep rs-shift-sep">' . esc_html( $separator ) . '</span>' . "\n";
					$shift_display .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";
				$shift_display .= '</div>';
				// 2.3.3.9: add empty user time div
				$shift_display .= '<div class="show-user-time">' . "\n";
					$shift_display .= '[<span class="rs-time rs-start-time"></span>' . "\n";
					$shift_display .= '<span class="rs-sep rs-shift-sep">' . esc_html( $separator ) . '</span>' . "\n";
					$shift_display .= '<span class="rs-time rs-end-time"></span>]' . "\n";
					$shift_display .= '</div>' . "\n";
				$shift_display .= '</div>' . "\n";
				if ( RADIO_STATION_DEBUG ) {
					$shift_display .= '<span style="display:none;">Upcoming Shift: ' . esc_html( print_r( $shift, true ) ) . '</span>';
				}
			}

			// --- set clear div ---
			$html['clear'] = '<span class="radio-clear"></span>' . "\n";

			// --- set show title ---
			// 2.3.3.9: added attribute for show title display
			// 2.5.0: removed show_title attribute as always needed
			$title = '<div class="upcoming-show-title on-air-dj-title">' . "\n";
			if ( $show_link ) {
				$title .= '<a href="' . esc_url( $show_link ) . '">' . "\n";
			}
			$title .= esc_html( $show['name'] ) . "\n";
			if ( $show_link ) {
				$title .= '</a>' . "\n";
			}
			$title .= '</div>' . "\n";
			$title = apply_filters( 'radio_station_upcoming_show_title_display', $title, $show_id, $atts );
			if ( ( '' != $title ) && is_string( $title ) ) {
				$html['title'] = $title;
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
					$avatar = '<div class="upcoming-show-avatar on-air-dj-avatar' . esc_attr( $float_class ) . '" ' . $width_style . '>' . "\n";
					if ( $atts['show_link'] ) {
						$avatar .= '<a href="' . esc_url( $show_link ) . '">' . "\n";
					}
					// 2.5.0: added wp_kses to avatar output
					$allowed = radio_station_allowed_html( 'media', 'image' );
					$avatar .= wp_kses( $show_avatar, $allowed ) . "\n";
					if ( $atts['show_link'] ) {
						$avatar .= '</a>' . "\n";
					}
					$avatar .= '</div>' . "\n";
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

						$hosts = '<div class="upcoming-show-hosts on-air-dj-names">' . "\n";
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
						$hosts .= '</div>' . "\n";
					}
					$hosts = apply_filters( 'radio_station_upcoming_show_hosts_display', $hosts, $show_id, $atts );
					if ( ( '' != $hosts ) && is_string( $hosts ) ) {
						// 2.5.0: added wp_kses to hosts display output
						$allowed = radio_station_allowed_html( 'content', 'hosts' );
						$html['hosts'] = wp_kses( $hosts, $allowed );
					}
				}
			}

			// --- set encore presentation ---
			// 2.2.4: added encore presentation display
			// 2.3.3.8: added shortcode attribute for encore display (default 1)
			if ( $atts['show_encore'] ) {
				$encore = '';
				if ( isset( $show['encore'] ) && ( 'on' == $show['encore'] ) ) {
					$encore = '<div class="upcoming-show-encore on-air-dj-encore">' . "\n";
						$encore .= esc_html( __( 'Encore Presentation', 'radio-station' ) ) . "\n";
					$encore .= '</div>' . "\n";
				}
				$encore = apply_filters( 'radio_station_upcoming_show_encore_display', $encore, $show_id, $atts );
				if ( ( '' != $encore ) && is_string( $encore ) ) {
					// 2.5.0: added wp_kses to encore display output
					$allowed = radio_station_allowed_html( 'content', 'encore' );
					$html['encore'] = wp_kses( $encore, $allowed );
				}
			}

			// --- set countdown timer ---
			// 2.5.10: add counter when dynamic is enabled
			if ( ( 0 == $i ) && isset( $next_start_time ) && ( $atts['countdown'] || $atts['dynamic'] ) ) {
				$html['countdown'] = '<div class="upcoming-show-countdown rs-countdown"';
				// 2.5.10: hide countdown if disabled but dynamic enabled
				if ( $atts['dynamic'] && !$atts['countdown'] ) {
					$html['countdown'] .= ' style="display:none;"';
				}
				$html['countdown'] .= '></div>' . "\n";
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
			$output .= '<li class="upcoming-show on-air-dj">' . "\n";

			// --- add output according to section order ---
			// 2.3.3.8: moved section order filter out of show shift loop
			foreach ( $order as $section ) {
				if ( isset( $html[$section] ) && ( '' != $html[$section] ) ) {
					$output .= $html[$section];
				}
			}

			// --- close upcoming show list item ---
			$output .= '</li>' . "\n";
		}

	} else {

		// 2.5.0: allow for hiding when empty (with filter override)
		if ( $atts['hide_empty'] ) {
			$output = apply_filters( 'radio_station_upcoming_shows_shortcode', '', $atts, $instance );
			return $output;
		}

		// --- no shows upcoming ---
		// note: no countdown display added as no upcoming shows found
		// 2.5.0: change default_name key to no_shows_text
		// 2.5.0: move esc_html to output line
		// 2.5.0: allow for zero value for no text
		if ( '0' != $atts['no_shows'] ) {
			if ( '' != $atts['no_shows'] ) {
				$no_upcoming_shows = $atts['no_shows'];
			} else {
				$no_upcoming_shows = __( 'No Upcoming Shows Scheduled.', 'radio-station' );
			}
			// 2.3.1: add filter for no current shows text
			$no_upcoming_shows = apply_filters( 'radio_station_no_upcoming_shows_text', $no_upcoming_shows, $atts );

			$output .= '<li class="upcoming-show-none on-air-dj default-dj">' . "\n";
				// 2.5.0: use wp_kses on message output
				$allowed = radio_station_allowed_html( 'message', 'no-shows' );
				$output .= wp_kses( $no_upcoming_shows, $allowed );
			$output .= '</li>' . "\n";
		}
	}

	// --- countdown timer inputs ---
	// 2.3.0: added for countdowns
	if ( isset( $next_start_time ) && ( $atts['countdown'] || $atts['dynamic'] ) ) {

		// 2.5.0: wrap data in hidden list item for script
		$output .= '<li style="display:none;">' . "\n";

			// 2.3.3.9: output current time override
			if ( isset( $atts['for_time'] ) ) {
				$output .= '<input type="hidden" class="current-time-override" value="' . esc_attr( $atts['for_time'] ) . '">';
			}

			// --- hidden input for next start time ---
			$output .= '<input type="hidden" class="upcoming-show-times" value="' . esc_attr( $next_start_time ) . '-' . esc_attr( $next_end_time ) . '">' . "\n";
			if ( RADIO_STATION_DEBUG ) {
				$output .= '<span style="display:none;">';
					$output .= 'Now: ' . esc_html( radio_station_get_time( 'Y-m-d H:i:s', $now ) ) . ' (' . esc_html( $now ) . ')' . "\n";
					$output .= 'Next Start Time: ' . esc_html( radio_station_get_time('y-m-d H:i:s', $next_start_time ) ) . ' (' . esc_html( $next_start_time ) . ')' . "\n";
					$output .= 'Next End Time: ' . esc_html( radio_station_get_time( 'y-m-d H:i:s', $next_end_time  ) ) . ' (' . esc_html( $next_end_time ) . ')' . "\n";
					$output .= 'Starting in: ' . esc_html( $next_start_time - $now ) . "\n";
				$output .= '</span>';
			}

			// --- for dynamic reloading ---
			if ( $atts['dynamic'] ) {
				$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'upcoming-shows', $atts, $next_start_time );
				if ( $dynamic ) {
					$output .= $dynamic;
				}
			}
			
		$output .= '</li>' . "\n";
	}

	// --- close upcoming shows list ---
	$output .= '</ul>' . "\n";

	// --- close shortcode only wrapper ---
	if ( !$atts['widget'] ) {
		$output .= '</div>' . "\n";
	}

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// 2.5.0: added missing output filter
	$output = apply_filters( 'radio_station_upcoming_shows_shortcode', $output, $atts, $instance );
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
		echo '<span style="display:none">Upcoming Shows Shortcode Attributes: ' . esc_html( print_r( $atts, true ) ) . '</span>' . "\n";
	}

	// --- output widget contents ---
	$output = radio_station_upcoming_shows_shortcode( $atts );
	echo '<div id="widget-contents">' . "\n";
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $output;
	echo '</div>' . "\n";

	$js = '';
	if ( isset( $atts['instance'] ) ) {

		// 2.6.5: maybe hide entire parent widget area if empty
		if ( $atts['hide_empty'] && ( '' == trim( $output ) ) ) {

			$js .= "instance = parent.document.getElementById('upcoming-shows-widget-" . esc_js( $atts['instance'] ) . "');" . "\n";
			$js .= "instance.style.display = 'none';" . "\n";
			
		} else {

			// --- send to parent window ---
			// 2.5.6: ensure parent widget is displayed
			// 2.5.7: added check that widget element exists
			$js .= "instance = parent.document.getElementById('upcoming-shows-widget-" . esc_js( $atts['instance'] ) . "');" . "\n";
			$js .= "if (instance) {instance.style.display = '';}" . "\n";
			$js .= "widget = document.getElementById('widget-contents').innerHTML;" . "\n";
			$js .= "parent.document.getElementById('rs-upcoming-shows-" . esc_js( $atts['instance'] ) . "').innerHTML = widget;" . "\n";

			// --- restart countdowns ---
			// 2.5.10: restart countdown if dynamic is enabled
			if ( $atts['countdown'] || $atts['dynamic'] ) {
				// 2.5.0: replace timeout with interval and function check
				// $js .= "setTimeout(function() {parent.radio_countdown();}, 2000);" . "\n";
				$js .= "countdown = setInterval(function() {" . "\n";
					$js .= "if (typeof parent.radio_countdown == 'function') {" . "\n";
						$js .= "parent.radio_countdown();" . "\n";
						$js .= "clearInterval(countdown);" . "\n";
					$js .= "}" . "\n";
				$js .= "}, 1000);" . "\n";
			}
		}
	}

	// --- filter load script ---
	$js = apply_filters( 'radio_station_upcoming_shows_load_script', $js, $atts );

	// --- output javascript
	if ( '' != $js ) {
		echo "<script>" . $js . "</script>" . "\n";
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
	if ( !isset( $radio_station_data['instances']['current_playlist'] ) ) {
		$radio_station_data['instances']['current_playlist'] = 0;
	}
	$radio_station_data['instances']['current_playlist']++;
	$instance = $radio_station_data['instances']['current_playlist'];

	$output = '';

	// 2.3.2: get default AJAX load settings
	// 2.5.0: unset empty AJAX attribute to use default
	$ajax = radio_station_get_setting( 'ajax_widgets', false );
	$ajax =  ( 'yes' == $ajax ) ? 'on' : 'off';
	if ( isset( $atts['ajax'] ) && ( '' == $atts['ajax'] ) ) {
		unset( $atts['ajax'] );
	}

	// --- check for dynamic setting ---
	// 2.5.0: added instance as third filter argument
	$dynamic = apply_filters( 'radio_station_current_playlist_dynamic', false, $atts, $instance );
	$dynamic = $dynamic ? 1 : 0;

	// --- get shortcode attributes ---
	// 2.3.2: added AJAX load attribute
	// 2.3.2: added for_time attribute
	// 2.5.0: added no_playlist text attribute
	// 2.5.0: added playlist_title switch attribute
	$defaults = array(
		// --- widget display options ---
		'title'          => '',
		'ajax'           => $ajax,
		'dynamic'        => $dynamic,
		'hide_empty'     => 0,
		// --- playlist display options ---
		'playlist_title' => 0,
		'link'           => 1,
		'no_playlist'    => '',
		'countdown'      => 0,
		// --- track display options ---
		'song'           => 1,
		'artist'         => 1,
		'album'          => 0,
		'label'          => 0,
		'comments'       => 0,
		// --- widget data ---
		'widget'         => 0,
		'block'          => 0,
		'id'             => '',
		'for_time'       => 0,
	);

	// 2.3.0: renamed shortcode identifier to current-playlist
	$atts = shortcode_atts( $defaults, $atts, 'current-playlist' );

	// 2.3.2: enqueue countdown script earlier
	// 2.5.10: enqueue countdown if dynamic is enabled
	if ( $atts['countdown'] || $atts['dynamic'] ) {
		do_action( 'radio_station_countdown_enqueue' );
	}

	// 2.3.3: added current time override for manual testing
	if ( isset( $_GET['date'] ) && isset( $_GET['time'] ) ) {
		// 2.5.0: added sanitize_text_field to date/time
		$date = trim( sanitize_text_field( $_GET['date'] ) );
		$time = trim( sanitize_text_field( $_GET['time'] ) );
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
			echo "<script>console.log('Override Current Time: " . esc_js( $for_time ) . "');</script>";
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
			$html = '<div id="rs-current-playlist-' . esc_attr( $instance ) . '" class="ajax-widget"></div>' . "\n";
			$html .= '<iframe id="rs-current-playlist-' . esc_attr( $instance ) . '-loader" src="javascript:void(0);" style="display:none;"></iframe>' . "\n";

			// --- shortcode script loader ---
			$html .= "<script>timestamp = Math.floor( (new Date()).getTime() / 1000 );" . "\n";
				$html .= "url = '" . esc_url( $ajax_url ) . "?action=radio_station_current_playlist';" . "\n";
				$html .= "url += '&instance=" . esc_attr( $instance ) . "';" . "\n";
				if ( RADIO_STATION_DEBUG ) {
					$html .= "url += '&rs-debug=1';" . "\n";
				}
				$html .= "url += '";
				foreach ( $atts as $key => $value ) {
					// 2.5.7: fix to ensure for_time is used for timestamp
					if ( ( 'for_time' == $key ) && ( 0 == $value ) ) {
						$html .= "&for_time='+timestamp+'";
					} else {
						$value = radio_station_encode_uri_component( $value );
						$html .= "&" . esc_js( $key ) . "=" . esc_js( $value );
					}
				}
				$html .= "';" . "\n";
				$html .= "document.getElementById('rs-current-playlist-" . esc_attr( $instance ) ."-loader').src = url;" . "\n";
			$html .= "</script>" . "\n";

			// --- enqueue shortcode styles ---
			radio_station_enqueue_style( 'shortcodes' );

			// 2.6.7: filter to enqueue= reloader script in current window
			if ( $atts['dynamic'] ) {
				$dynamic = apply_filters( 'radio_station_countdown_dynamic', false, 'current-playlist', $atts, false );
			}

			// 2.5.0: added filter for shortcode output
			$html = apply_filters( 'radio_station_current_playlist_shortcode_ajax', $html, $atts, $instance );
			return $html;
		}
	}

	// --- fetch the current playlist ---
	if ( $atts['for_time'] ) {
		$playlist = radio_station_get_now_playing( $atts['for_time'] );
		$time = radio_station_get_time( 'datetime', $atts['for_time'] );
		echo '<span style="display:none;">';
			echo 'Current Playlist For Time: ' . esc_html( $atts['for_time'] ) . ' : ' . esc_html( $time ) . "\n";;
			echo esc_html( print_r( $playlist, true ) );
		echo '</span>';
	} else {
		$playlist = radio_station_get_now_playing();
	}

	// --- shortcode only wrapper ---
	if ( !$atts['widget'] ) {

		// 2.3.0: add unique id to widget shortcode
		// 2.3.2: fix to shortcode classes
		// 2.3.2: add shortcode wrap class
		// 2.5.0: change data key to shortcodes NOT widget
		// 2.5.0: simplify instances to start at 1
		if ( !isset( $radio_station_data['shortcodes']['current-playlist'] ) ) {
			$radio_station_data['shortcodes']['current-playlist'] = 0;
		}
		$radio_station_data['shortcodes']['current-playlist']++;
		$id = 'show-playlist-shortcode-' . $radio_station_data['widgets']['current-playlist'];
		$output .= '<div id="' . esc_attr( $id ) . '" class="current-playlist-wrap current-playlist-embedded now-playing-embedded">' . "\n";

	}

	// --- shortcode title ---
	// 2.5.0: also maybe display for non-shortcodes
	// 2.5.7: but do not display for widgets (duplication)
	if ( ( '' ==  $atts['title'] ) && ( 0 != $atts['title'] ) && !$atts['widget'] ) {
		// 2.3.0: added title class for shortcode
		$output .= '<h3 class="show-playlist-title myplaylist-title">' . "\n";
			// 2.5.0: fixed to use esc_html instead of esc_attr
			$output .= esc_html( $atts['title'] ) . "\n";
		$output .= '</h3>' . "\n";
	}

	// --- set empty HTML array ---
	$html = array();

	// --- countdown timer ---
	// 2.3.0: added for countdown changeovers
	// 2.3.3.8: moved outside of current playlist check
	if ( $atts['countdown'] || $atts['dynamic'] ) {

		$html['countdown'] = '';
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Playlist: ' . esc_html( print_r( $playlist, true ) ) . '</span>' . "\n";
		}

		// 2.3.1: added check for playlist shifts value
		if ( isset( $playlist['shifts'] ) && is_array( $playlist['shifts'] ) && ( count( $playlist['shifts'] ) > 0 ) ) {

			// --- convert dates ---
			// 2.3.0: use weekdates for reliability
			$now = $atts['for_time'] ? $atts['for_time'] : radio_station_get_now();
			$today = radio_station_get_time( 'l', $now );
			$yesterday = radio_station_get_previous_day( $today );
			$weekdays = radio_station_get_schedule_weekdays( $yesterday );
			$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now );

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
					// 2.5.0: removed duplicate now declarations
					if ( $atts['for_time'] ) {
						// $now = $atts['for_time'];
						$html['countdown'] .= '<input type="hidden" class="current-time-override" value="' . esc_attr( $atts['for_time'] ) . '">' . "\n";
					}
					// echo 'Shift Start: ' . $shift_start_time . '(' . radio_station_get_time( 'datetime', $shift_start_time ) . ')<br>';
					// echo 'Shift End: ' . $shift_end_time . '(' . radio_station_get_time( 'datetime', $shift_end_time ) . ')<br>';

					if ( ( ( $now > $shift_start_time ) || ( $now == $shift_start_time ) ) && ( $now < $shift_end_time ) ) {

						// print_r( $shift );
						// echo "^^^ NOW PLAYING ^^^";

						// --- hidden input for playlist end time ---
						$html['countdown'] .= '<input type="hidden" class="current-playlist-end" value="' . esc_attr( $shift_end_time ) . '">' . "\n";

						// --- for countdown timer display ---
						// 2.5.10: also output if dynamic enabled
						if ( $atts['countdown'] || $atts['dynamic'] ) {
							$html['countdown'] .= '<div class="show-playlist-countdown rs-countdown"';
							// 2.5.10: hide if countdown disabled but dynamic enabled
							if ( $atts['dynamic'] && !$atts['countdown'] ) {
								$html['countdown'] .= ' style="display:none;"';
							}
							$html['countdown'] .= '></div>' . "\n";
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
		$tracks .= '<div class="show-playlist-tracks myplaylist-nowplaying">' . "\n";

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

			$tracks .= '<div class="show-playlist-track myplaylist-track' . esc_attr( $class ) . '">' . "\n";

			// 2.2.3: convert span tags to div tags
			// 2.2.4: check value keys are set before outputting
			if ( $atts['song'] && isset( $track['playlist_entry_song'] ) ) {
				$tracks .= '<div class="show-playlist-song myplaylist-song">' . "\n";
					$tracks .= '<span class="playlist-label">' . esc_html( __( 'Song', 'radio-station' ) ) . '</span>';
					$tracks .= ': <span class="playlist-info">' . esc_html( $track['playlist_entry_song'] ) . '</span>' . "\n";
				$tracks .= '</div>' . "\n";
			}

			// 2.2.7: add label prefixes to now playing data
			if ( $atts['artist'] && isset( $track['playlist_entry_artist'] ) ) {
				$tracks .= '<div class="show-playlist-artist myplaylist-artist">' . "\n";
					$tracks .= '<span class="playlist-label">' . esc_html( __( 'Artist', 'radio-station' ) ) . '</span>';
					$tracks .= ': <span class="playlist-info">' . esc_html( $track['playlist_entry_artist'] ) . '</span>' . "\n";
				$tracks .= '</div>' . "\n";
			}

			if ( $atts['album'] && !empty( $track['playlist_entry_album'] ) ) {
				$tracks .= '<div class="show-playlist-album myplaylist-album">' . "\n";
					$tracks .= '<span class="playlist-label">' . esc_html( __( 'Album', 'radio-station' ) ) . '</span>';
					$tracks .= ': <span class="playlist-info">' . esc_html( $track['playlist_entry_album'] ) . '</span>' . "\n";
				$tracks .= '</div>' . "\n";
			}

			if ( $atts['label'] && !empty( $track['playlist_entry_label'] ) ) {
				$tracks .= '<div class="show-playlist-label myplaylist-label">' . "\n";
					$tracks .= '<span class="playlist-label">' . esc_html( __( 'Label', 'radio-station' ) ) . '</span>';
					$tracks .= ': <span class="playlist-info">' . esc_html( $track['playlist_entry_label'] ) . '</span>' . "\n";
				$tracks .= '</div>' . "\n";
			}

			if ( $atts['comments'] && !empty( $track['playlist_entry_comments'] ) ) {
				$tracks .= '<div class="show-playlist-comments myplaylist-comments">' . "\n";
					$tracks .= '<span class="playlist-label">' . esc_html( __( 'Comments', 'radio-station' ) ) . '</span>';
					$tracks .= ': <span class="playlist-info">' . esc_html( $track['playlist_entry_comments'] ) . '</span>' . "\n";
				$tracks .= '</div>' . "\n";
			}

			$tracks .= '</div>' . "\n";
		}
		$tracks .= '</div>' . "\n";

		// --- playlist permalink ---
		// 2.3.3.8 added playlist_link shortcode attribute (default 1)
		if ( $atts['link'] ) {
			$link = '';
			// 2.5.0: fix to playlist_permalink key
			if ( isset( $playlist['playlist_url'] ) ) {
				$link = '<div class="show-playlist-link myplaylist-link">' . "\n";
					$link .= '<a href="' . esc_url( $playlist['playlist_url'] ) . '">';
						// 2.5.0: maybe show playlist title
						if ( $atts['playlist_title'] ) {
							$link .= esc_html( $playlist['title'] );
						} else {
							$link .= esc_html( __( 'View Playlist', 'radio-station' ) );
						}
					$link .= '</a>' . "\n";
				$link .= '</div>' . "\n";
			}
			// 2.3.3.8: added playlist link display filter
			$link = apply_filters( 'radio_station_current_playlist_link_display', $link, $playlist, $atts );
			if ( ( '' != $link ) && is_string( $link ) ) {
				$html['link'] = $link;
			}
		} elseif ( $atts['playlist_title'] ) {
			// 2.5.0: maybe show playlist title
			$html['link'] = esc_html( $playlist['title'] );
		}

	} else {

		// 2.5.0: allow for hiding when empty (with filter override)
		if ( $atts['hide_empty'] ) {
			$output = apply_filters( 'radio_station_current_playlist_shortcode', '', $atts, $instance );
			return $output;
		}

		// 2.2.3: added missing translation wrapper
		// 2.3.0: added no playlist class
		// 2.3.1: add filter for no playlist text
		// 2.3.3.8: fix to unclosed double quote on class attribute
		// 2.5.0: add shortcode attribute for no playlist text
		// 2.5.0: allow for zero value for no text
		if ( '0' != $atts['no_playlist'] ) {
			if ( '' != $atts['no_playlist'] ) {
				$no_current_playlist = $atts['no_playlist'];
			} else {
				$no_current_playlist = __( 'No Current Playlist available.', 'radio-station' );
			}
			$no_current_playlist = apply_filters( 'radio_station_no_current_playlist_text', $no_current_playlist, $atts );

			$no_playlist = '<div class="show-playlist-noplaylist myplaylist-noplaylist">' . "\n";
				// 2.5.0: use wp_kses on message output
				$allowed = radio_station_allowed_html( 'message', 'no-playlist' );
				$no_playlist .= wp_kses( $no_current_playlist, $allowed ) . "\n";
			$no_playlist .= '</div>' . "\n";

			// 2.3.3.8: added no playlist display filter
			// 2.3.3.9: assign to tracks key for possible output re-ordering
			$no_playlist = apply_filters( 'radio_station_current_playlist_no_playlist_display', $no_playlist, $atts );
			if ( ( '' != $no_playlist ) && is_string( $no_playlist ) ) {
				$html['tracks'] = $no_playlist;
			}
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

	// 2.5.0: added wrapper and countdown class for countdown script targeting
	$classes = array( 'current-playlist show-playlist' );
	// 2.5.10: also add class when dynamic is enabled
	if ( $atts['countdown'] || $atts['dynamic'] ) {
		$classes[] = 'countdown';
	}
	$class_list = implode( ' ', $classes );
	$output .= '<div class="' . esc_attr( $class_list ) . '">' . "\n";

		// --- loop sections to add to output ---
		foreach ( $order as $section ) {
			if ( isset( $html[$section] ) && ( '' != $html[$section] ) ) {
				$output .= $html[$section];
			}
		}

	$output .= '</div>' . "\n";
		
	// --- close shortcode only wrapper ---
	if ( !$atts['widget'] ) {
		$output .= '</div>' . "\n";
	}

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return ---
	// 2.5.0: added missing output override filter
	$output = apply_filters( 'radio_station_current_playlist_shortcode', $output, $atts, $instance );
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
		echo '<span style="display:none;">Current Playlist Shortcode Attributes: ' . esc_html( print_r( $atts, true ) ) . '</span>' . "\n";
	}

	// --- output widget contents ---
	$output = radio_station_current_playlist_shortcode( $atts );
	echo '<div id="widget-contents">' . "\n";
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $output;
	echo '</div>' . "\n";

	$js = '';
	if ( isset( $atts['instance'] ) ) {

		// 2.6.5: maybe hide entire parent widget area if empty
		if ( $atts['hide_empty'] && ( '' == trim( $output ) ) ) {

			// 2.5.7: added check that widget element exists
			$js .= "instance = parent.document.getElementById('current-playlist-widget-" . esc_js( $atts['instance'] ) . "');" . "\n";
			$js .= "if (instance) {instance.style.display = 'none';}" . "\n";

		} else {

			// --- send to parent window ---
			// 2.5.6: ensure parent widget is displayed
			// 2.5.7: added check that widget element exists
			$js .= "instance = parent.document.getElementById('current-playlist-widget-" . esc_js( $atts['instance'] ) . "');" . "\n";
			$js .= "if (instance) {instance.style.display = '';}" . "\n";
			$js .= "widget = document.getElementById('widget-contents').innerHTML;" . "\n";
			$js .= "parent.document.getElementById('rs-current-playlist-" . esc_js( $atts['instance'] ) . "').innerHTML = widget;" . "\n";

			// --- restart countdowns ---
			// 2,5,10: also restart if dynamic is enabled
			if ( $atts['countdown'] || $atts['dynamic'] ) {
				// 2.5.0: replace timeout with interval and function check
				// $js .= "setTimeout(function() {parent.radio_countdown();}, 2000);" . "\n";
				$js .= "countdown = setInterval(function() {" . "\n";
					$js .= "if (typeof parent.radio_countdown == 'function') {" . "\n";
						$js .= "parent.radio_countdown();" . "\n";
						$js .= "clearInterval(countdown);" . "\n";
					$js .= "}" . "\n";
				$js .= "}, 1000);" . "\n";
			}
		}

	}

	// --- filter load script ---
	$js = apply_filters( 'radio_station_current_playlist_load_script', $js, $atts );

	// --- output javascript
	if ( '' != $js ) {
		echo "<script>" . $js . "</script>" . "\n";
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
	if ( isset( $radio_station_data['countdown-script'] ) && $radio_station_data['countdown-script'] ) {
		return;
	}
	
	// --- enqueue countdown script ---
	radio_station_enqueue_script( 'radio-station-countdown', array( 'radio-station' ), true );

	// --- add script inline ---
	// 2.5.0: moved countdown labels to main script localization
	// wp_add_inline_script( 'radio-station-countdown', $js );

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

	global $radio_station_data;

	// --- set widget instance ID ---
	// 2.3.2: added for AJAX loading
	if ( !isset( $radio_station_data['instances']['list_shows'] ) ) {
		$radio_station_data['instances']['list_shows'] = 0;
	}
	$radio_station_data['instances']['list_shows']++;
	$instance = $radio_station_data['instances']['list_shows'];

	// --- combine shortcode atts with defaults ---
	$defaults = array(
		'title' => false,
		'genre' => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'list-shows' );

	// --- grab the published shows ---
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

	$output .= '<div id="station-show-list">' . "\n";

		if ( $atts['title'] ) {
			$output .= '<div class="station-show-list-title">' . "\n";
				$output .= '<h3>' . esc_html( $atts['title'] ) . '</h3>' . "\n";
			$output .= '</div>' . "\n";
		}

		$output .= '<ul class="show-list">' . "\n";

		// 2.3.0: use posts loop instead of query loop
		foreach ( $posts as $post ) {
			$output .= '<li class="show-list-item">' . "\n";
				$output .= '<div class="show-list-item-title">' . "\n";
					$output .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">';
						$output .= esc_html( get_the_title( $post->ID ) );
					$output .= '</a>' . "\n";
				$output .= '</div>' . "\n";
			$output .= '</li>' . "\n";
		}
		
		$output .= '</ul>' . "\n";

	$output .= '</div>' . "\n";

	// --- filter and return ---
	// 2.5.0: added shortcode filter for consistency
	$output = apply_filters( 'radio_station_list_shows_shortcode', $output, $atts, $instance );
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

	global $radio_station_data;

	// --- set widget instance ID ---
	// 2.3.2: added for AJAX loading
	if ( !isset( $radio_station_data['instances']['get_playlists'] ) ) {
		$radio_station_data['instances']['get_playlists'] = 0;
	}
	$radio_station_data['instances']['get_playlists']++;
	$instance = $radio_station_data['instances']['get_playlists'];

	// --- combine shortcode atts with defaults ---
	$defaults = array(
		'show'  => '',
		'limit' => -1,
	);
	$atts = shortcode_atts( $defaults, $atts, 'get-playlists' );

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

	$output .= '<div id="myplaylist-playlistlinks">' . "\n";

		$output .= '<ul class="myplaylist-linklist">' . "\n";
		foreach ( $playlists as $playlist ) {
			$output .= '<li class="myplaylist-linklist-item">' . "\n";
				$output .= '<a href="' . esc_url( get_permalink( $playlist->ID ) ) . '">';
					$output .= esc_html( $playlist->post_title );
				$output .= '</a>' . "\n";
			$output .= '</li>' . "\n";
		}
		$output .= '</ul>' . "\n";

		$playlist_archive = get_post_type_archive_link( RADIO_STATION_PLAYLIST_SLUG );
		$params = array( 'show_id' => $atts['show'] );
		$playlist_archive = add_query_arg( $params, $playlist_archive );

		$output .= '<a href="' . esc_url( $playlist_archive ) . '">';
			$output .= esc_html( __( 'More Playlists', 'radio-station' ) );
		$output .= '</a>' . "\n";

	$output .= '</div>' . "\n";

	// --- filter and return ---
	// 2.5.0: added shortcode filter for consistency
	$output = apply_filters( 'radio_station_get_playlists_shortcode', $output, $atts, $instance );
	return $output;
}

