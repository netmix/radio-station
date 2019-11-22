<?php

/* Shortcode for displaying the current song
 * Since 2.0.0
 */

// note: Master Schedule Shortcode in /includes/master-schedule.php

// === Clock Shortcode ===
// - Clock Display Shortcode
// === Archive Shortcodes ===
// - Archive List Shortcode Abstract
// - Show Archive Shortcode
// - Playlist Archive Shortcode
// - Override Archive Shortcode
// - Genre Archive Shortcode
// === Show Shortcodes ===
// - Show List Abstract
// - Show Posts List Shortcode
// - Show Playlists List Shortcode
// - Show Lists Pagination Javascript
// === Legacy Shortcodes ===
// - Now Playing Shortcode
// - Show Playlist Shortcode
// - Show List Shortcode
// - Current Show Shortcode
// - Coming Up Shortcode

// -----------------------
// === Clock Shortcode ===
// -----------------------

// -----------------------
// Clock Display Shortcode
// -----------------------
add_shortcode( 'radio-clock', 'radio_station_clock_display' );
function radio_station_clock_display( $atts = array() ) {

	$clock = '<div class="radio-station-clock">';
	
		// --- server clock ---
		$clock .= '<div class="radio-station-server-clock">';
			$clock .= '<span class="clock-title">';
				$clock .= esc_html( __( 'Radio Time', 'radio-station' ) );
			$clock .= '</span>: ';
			$clock .= '<span class="server-time"></span> ';
			$clock .= '<span class="server-date"></span> ';
			$clock .= '<span class="server-zone"></span> ';
		$clock .= '</div>';
		
		// --- user clock ---
		$clock .= '<div class="radio-station-user-clock">';
			$clock .= '<span class="clock-title">'; 
				$clock .= esc_html( __( 'Your Time', 'radio-station' ) );
			$clock .= '</span>: ';
			$clock .= '<span class="user-time"></span> ';
			$clock .= '<span class="user-date"></span> ';
			$clock .= '<span class="user-zone"></span> ';		
		$clock .= '</div>';
		
	$clock .= '</div>';

	// --- [Pro] user timezone selector ---
	if ( function_exists( 'radio_station_pro_timezone_selector' ) ) {
		$clock .= radio_station_pro_timezone_switcher();
	}
	
	return $clock;
}


// --------------------------
// === Archive Shortcodes ===
// --------------------------

// -------------------------------
// Archive List Shortcode Abstract
// -------------------------------
function radio_station_archive_list_shortcode( $type, $atts ) {

	// TODO: add pagination links

	// --- merge defaults with passed attributes ---
	$defaults = array(
		'genre'			=> '',
		'thumbnails'	=> 1,
		'hide_empty'	=> 0,
		'content'		=> 'excerpt',
		'paginate'		=> 1,
		// query args
		'orderby'		=> 'title',
		'order'			=> 'ASC',
		'status'		=> 'publish',
		'perpage'		=> -1,
		'offset'		=> 0,
		// note: for shows only
		'show_avatars'	=> 1,
		'with_shifts'	=> 1,
	);

	// --- handle possible pagination offset ---
	if ( isset( $atts['perpage'] ) && !isset( $atts['offset'] ) && get_query_var( 'page' ) ) {
		$page = absint( get_query_var( 'page' ) );
		if ( $page > -1 ) {
			$atts['offset'] = (int) $atts['perpage'] * $page;
		}
	}
	$atts = shortcode_atts( $defaults, $atts, $type . '-archive' );

	// --- get published shows ---
	$args = array(
		'post_type'		=> $type,
		'numberposts'	=> $atts['perpage'],
		'offset'		=> $atts['offset'],
		'orderby'		=> $atts['orderby'],
		'order'			=> $atts['order'],
		'post_status'	=> $atts['status'],
	);

	// --- extra queries for shows ---
	if ( $type == 'show' ) {

		if ( $atts['with_shifts'] ) {
		
			// --- active shows with shifts ---
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'meta_key	'	=> 'show_sched',
					'compare'		=> 'EXISTS',
				),
				array(
					'meta_key'		=> 'show_active',
					'mata_value'	=> 'on',
					'compare'		=> '=',
				),
			);
		} else {
		
			// --- just active shows ---
			$args['meta_query'] = array(
				array(
					'meta_key'		=> 'show_active',
					'meta_value'	=> 'on',
					'compare'		=> '=',
				),
			);
		}

		// --- check for a specified show genre --- 
		if ( !empty( $atts['genre'] ) ) {
			// TODO: maybe handle numeric genre term ID ?
			$args['tax_query'] = array(
				array(
					'taxonomy'	=> 'genres',
					'field'		=> 'slug',
					'terms'		=> $atts['genre'],
				),
			);
		}
	}

	// --- get posts via query ---
	$posts = get_posts( $args );

	// --- check for results ---
	$list = '<div class="' . $type . '-archives">';
	if ( !$posts || ( count( $posts ) == 0 ) ) {
		
		if ( $atts['hide_empty'] ) {return '';}
		
		// --- no shows messages ----
		if ( $type == 'show' ) {
			if ( !empty( $atts['genre'] ) ) {
				$list .= esc_html( __( 'No Shows in this Genre were found.', 'radio-station' ) );
			} else {
				$list .= esc_html( __( 'No Shows were found to display.', 'radio-station' ) ) ;
			}
		} elseif ( $type == 'playlist' ) {
			$list .= esc_html( __( 'No Playlists were found to display.', 'radio-station' ) ) ;
		} elseif ( $type == 'override' ) {
			$list .= esc_html( __( 'No Overrides were found to display.', 'radio-station' ) ) ;
		}
		
	} else {
	
		// --- archive list ---
		$list .= '<ul class="' . $type . '-archive-list">';
		
		foreach ( $posts as $post ) {
			$list .= '<li class="' . $type . '-archive-list-item">';
				
				// --- show avatar or thumbnail ---
				$list .= '<div class="' . $type . '-archive-list-item-thumbnail">';
				if ( $atts['show_avatars'] && ( $type == 'show' ) ) {
					// --- show avatar for shows ---
					$attr = array( 'class' => 'show-thumbnail-image' );
					$show_avatar = radio_station_get_show_avatar( $post->ID, 'thumbnail', $attr );
					if ( $show_avatar ) {$list .= $show_avatar;}
				} elseif ( $atts['thumbnails'] ) {
				 	// --- post thumbnail ---
					if ( has_post_thumbnail( $post->ID ) ) {
						$atts = array( 'class' => $type . '-thumbnail-image' );
						$thumbnail = get_the_post_thumbnail( $post->ID, 'thumbnail', $atts );
						$list .= $thumbnail;
					}
				}
				$list .= '</div>';
				
				// --- title ----
				$list .= '<div class="' . $type . '-archive-list-item-title">';
					$list .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">';
					$list .= esc_attr( get_the_title( $post->ID ) ) . '</a>';
				$list .= '</div>';
				
				// --- meta ---	
				if ( $type == 'show' ) {
					// TODO: show shifts, genres
				
				}
				if ( $type == 'playlist' ) {
					// TODO: playlist tracks
				}				
				if ( $type == 'override' ) {
					// TODO: override date
				
				}
				
				// --- content ---
				if ( $atts['content'] == 'none' ) {
					$list .= '';
				} elseif ( $atts['content'] == 'full' ) {
					$list .= '<div class="' . $type . '-list-item-content">';
						$content = apply_filters( 'radio_station_' . $type . '_archive_content', $post->post_content, $post->ID );
						$list .= $content;
					$list .= '</div>';
				} else {
					$list .= '<div class="' . $type . '-list-item-excerpt">';
						if ( !empty( $post->post_excerpt ) ) {
							$excerpt = apply_filters( 'radio_station_' . $type . '_archive_excerpt', $post->post_excerpt, $post->ID );
						} else {
							$excerpt = get_the_excerpt( $post->ID );
						}
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
	$output = radio_station_archive_list_shortcode( 'show', $atts );	
	return $output;
}

// --------------------------
// Playlist Archive Shortcode
// --------------------------
add_shortcode( 'playlist-archive', 'radio_station_playlist_archive_list' );
add_shortcode( 'playlists-archive', 'radio_station_playlist_archive_list' );
function radio_station_playlist_archive_list( $atts ) {
	$output = radio_station_archive_list_shortcode( 'playlist', $atts );
	return $output;
}

// --------------------------
// Override Archive Shortcode
// --------------------------
add_shortcode( 'override-archive', 'radio_station_override_archive_list' );
add_shortcode( 'overrides-archive', 'radio_station_override_archive_list' );
function radio_station_override_archive_list( $atts ) {
	$output = radio_station_archive_list_shortcode( 'override', $atts );
	return $output;
}

// -----------------------
// Genre Archive Shortcode
// -----------------------
add_shortcode( 'genre-archive', 'radio_station_genre_archive_list' );
add_shortcode( 'genres-archive', 'radio_station_genre_archive_list' );
function radio_station_genre_archive_list( $atts ) {

	// TODO: add pagination links

	$defaults = array(
		// genre display options
		'genres'		=> '',
		'description'	=> 1,
		'genre_images'	=> 1,
		'hide_empty'	=> 1,
		'paginate'		=> 1,
		// show query args
		'perpage'		=> -1,
		'offset'		=> 0,
		'orderby'		=> 'title',
		'order'			=> 'ASC',
		'status'		=> 'publish',
		// show display options
		'with_shifts'	=> 1,
		'avatars'		=> 0,
		'thumbnails'	=> 0,
	);

	// --- handle possible pagination offset ---
	if ( isset( $atts['perpage'] ) && !isset( $atts['offset'] ) && get_query_var( 'page' ) ) {
		$page = absint( get_query_var( 'page' ) );
		if ( $page > -1 ) {
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
			if ( $genre ) {$genres[$i] = $genre;}
			else {unset($genres[$i]);}
		}
	} else { 
		// --- get all genres ---
		$args = array();
		if ( !$atts['hide_empty'] ) {$args['hide_empty'] = false;}
		$genres = radio_station_get_genres( $args );
	}

	// --- check if we have genres ---
	if ( !$genres || ( count( $genres ) == 0 ) ) {
		if ( $atts['hide_empty'] ) {return '';}
		else {
			$list = '<div class="genre-archives">';
				$list .= esc_html( __( 'No Genres were found to display.', 'radio-station' ) ) ;
			$list .= '</div>';
			return $list;
		}		
	}

	$list = '<div class="genre-archives">';
	
	// --- loop genres ---
	foreach ( $genres as $genre ) {

		// --- get published shows ---
		$args = array(
			'post_type'		=> 'show',
			'numberposts'	=> $atts['perpage'],
			'offset'		=> $atts['offset'],
			'orderby'		=> $atts['orderby'],
			'order'			=> $atts['order'],
			'post_status'	=> $atts['status'],
		);

		if ( $atts['with_shifts'] ) {
		
			// --- active shows with shifts ---
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'meta_key	'	=> 'show_sched',
					'compare'		=> 'EXISTS',
				),
				array(
					'meta_key'		=> 'show_active',
					'mata_value'	=> 'on',
					'compare'		=> '=',
				),
			);
		} else {
		
			// --- just active shows ---
			$args['meta_query'] = array(
				array(
					'meta_key'		=> 'show_active',
					'meta_value'	=> 'on',
					'compare'		=> '=',
				),
			);
		}
		
		// --- set genre taxonomy query --- 
		// TODO: maybe handle numeric genre term ID
		$args['tax_query'] = array(
			array(
				'taxonomy'	=> 'genres',
				'field'		=> 'slug',
				'terms'		=> $genre['slug'],
			),
		);	

		$posts = get_posts( $args );

		$list .= '<div class="genre-archive">';

		if ( $posts || ( count( $posts ) > 0 ) ) {$has_posts = true;} else {$has_posts = false;}
		if ( $has_posts || ( !$has_posts && !$atts['hide_empty'] ) ) {

			// --- [Pro] genre image ---		
			if ( function_exists( 'radio_station_pro_get_genre_image' ) ) {
				$list .= '<div class="genre-image-wrapper">';
					$genre_image = radio_station_pro_get_genre_image( $genre->term_id );
					if ( $genre_image) {$list .= $genre_image;}
				$list .= '</div>';
			}

			// --- genre title ---
			$list .= '<div class="genre-title">';
				$list .= '<h3><a href="' . esc_url( $genre['url'] ) . '">' . $genre['name'] . '</a></h3>';
			$list .= '</div>';
			
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
				$list .= '<li class="show-archive-list-item">';

					// --- avatar or thumbnail ---
					$list .= '<div class="show-archive-list-item-thumbnail">';
					if ( $atts['show_avatars'] ) {
						// --- get show avatar ---
						$attr = array( 'class' => 'show-thumbnail-image' );
						$show_avatar = radio_station_get_show_avatar( $post->ID, 'thumbnail', $attr );
						if ( $show_avatar ) {$list .= $show_avatar;}
					} elseif ( $atts['thumbnails'] ) {
						if ( has_post_thumbnail( $post->ID ) ) {
							$atts = array( 'class' => 'show-thumbnail-image' );
							$thumbnail = get_the_post_thumbnail( $post->ID, 'thumbnail', $atts );
							$list .= $thumbnail;
						}
					}
					$list .= '</div>';

					// --- show title ----
					$list .= '<div class="show-archive-list-item-title">';
						$list .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">';
						$list .= esc_attr( get_the_title( $post->ID ) ) . '</a>';
					$list .= '</div>';

					// --- show excerpt ---
					// n/a

				$list .= '</li>';
			}
			$list .= '</ul>';
		}
		
	}
		
	$list .= '</div>';
	
	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );
	
	// --- filter and return ---
	$list = apply_filters( 'radio_station_genre_archive_list', $list, $atts );
	return $list;
}


// -----------------------
// === Show Shortcodes ===
// -----------------------

// ------------------
// Show List Abstract
// ------------------
function radio_station_show_list_shortcode( $type, $atts ) {

	global $radio_station_data;

	// --- get time and date formats ---
	$timeformat = get_option( 'time_format' );
	$dateformat = get_option( 'date_format' );

	// --- get shortcode attributes ---
	$defaults = array(
		'per_page'		=> 15,
		'limit'			=> -1,
		'content'		=> 'excerpt',
		'thumbnails'	=> 1,
	);
	$atts = shortcode_atts( $defaults, $atts, 'show-' . $type . '-list' );

	// --- maybe get stored post data ---
	if ( isset( $radio_station_data['show-' . $type . 's'] ) ) {
	
		// --- use data stored from template ---
		$posts = $radio_station_data['show-' . $type . 's'];	
		unset( $radio_station_data['show-' . $type . 's' ] );
		$show_id = $radio_station_data['show-id'];
		
	} else {
		// --- check for show ID (required at minimum) ---
		if ( !isset( $atts['show'] ) ) {return '';}
		$show_id = $atts['show'];

		// --- attempt to get show ID via slug ---
		if ( intval( $show_id ) != $show_id ) {
			global $wpdb;
			$query = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_name = %s";
			$query = $wpdb->prepare( $query, $show_id );
			$show_id = $wpdb->get_var( $query );
			if ( !$show_id ) {return '';}
		}

		// --- get related to show posts ---
		$args = array(); 
		if ( isset( $atts['limit'] ) ) {$args['limit'] = $atts['limit'];}
		if ( $type == 'post' ) {
			$posts = radio_station_get_show_posts( $show_id, $args );
		} elseif ( $type == 'playlist' ) {
			$posts = radio_station_get_show_playlists( $show_id, $args );
		} elseif ( ( $type == 'episode') && ( function_exists( 'radio_station_pro_get_show_episodes' ) ) ) {
			$posts = radio_station_pro_get_show_episodes( $show_id, $args );
		} else {
			return '';
		}
	}
	
	// --- show list div ---
	$list = '<div id="show-' . $show_id . '-' . $type . 's-list" class="show-' . $type . 's-list">';
	
		// --- loop show posts ---
		$post_pages = 1; $j = 0;
		foreach ( $posts as $post ) {
			$newpage = $firstpage = false;
			if ( $j == 0 ) {$newpage = $firstpage = true;}
			elseif ( $j == $atts['per_page'] ) {
				// --- close page div ---
				$list .= '</div>';
				$newpage = true; $post_pages++; $j = 0;
			}
			if ( $newpage ) {
				// --- new page div ---
				if ( !$firstpage ) {$hide = ' style="display:none;"';} else {$hide = '';}
				$list .= '<div id="show-' . $show_id . '-' . $type . 's-page-' . $post_pages . '" class="show-' . $show_id . '-' . $type . 's-page"' . $hide . '>';
			}

			// --- new item div ---
			$list .= '<div class="show-' . $type . '">';

				// --- post thumbnail ---
				if ( $atts['thumbnails'] ) {
					$has_thumbnail = has_post_thumbnail( $post['ID'] );
					if ( $has_thumbnail ) {
						$attr = array( 'class' => 'show-' . $type . '-thumbnail-image' );
						$thumbnail = get_the_post_thumbnail( $post['ID'], 'thumbnail', $attr );
						if ( $thumbnail ) {
							$list .= '<div class="show-' . $type . '-thumbnail">' . $thumbnail . '</div>';
						}
					}
				}

				$list .= '<div class="show-' . $type . '-info">';
				
				
					// --- link to post ---
					$list .= '<div class="show-' . $type . '-title">';
						$permalink = get_permalink( $post['ID'] );
						$timestamp = mysql2date( $dateformat.' '.$timeformat, $post['post_date'], false );
						$title = esc_attr( __( 'Published on ', 'radio-station' ) ) . $timestamp;
						$list .= '<a href="' . esc_url( $permalink ) . '" title="' . $title . '">';
							$list .= esc_attr( $post['post_title'] );
						$list .= '</a>';
					$list .= '</div>';

					// --- post excerpt ---
					if ( $atts['content'] == 'none' ) {
						$list .= '';
					} elseif ( $atts['content'] == 'full' ) {
						$list .= '<div class="show-' . $type . '-content">';
							$content = apply_filters( 'radio_station_show_' . $type . '_content', $post['post_content'], $post['ID'] );
							$list .= $content;
						$list .= '</div>';
					} else {
						$list .= '<div class="show-' . $type . '-excerpt">';
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
			$j++;
		}
		
		// --- close last page div ---
		$list .= '</div>'; 

		// --- list pagination ---
		if ( $post_pages > 1 ) {
			$list .= '<br><br>';
			$list .= '<div id="show-' .$show_id . '-' . $type .'s-page-buttons" class="show-' . $type . 's-page-buttons">';
				$list .= '<div class="show-pagination-button" onclick="radio_show_page(' . $show_id . ', \'' . $type . 's\', \'prev\');">';
					$list .= '<a href="javascript:void(0);">&larr;</a>';
				$list .= '</div>';
				for ( $pagenum = 1; $pagenum < ($post_pages + 1); $pagenum++ ) {
					if ( $pagenum == 1 ) {$active = ' active';} else {$active = '';}
					$onclick = 'radio_show_page(' . $show_id . ', \'' . $type . 's\', ' . $pagenum . ');';
					$list .= '<div id="show-' . $show_id . '-' . $type . 's-page-button-' . $pagenum . '" class="show-' . $show_id . '-' . $type . 's-page-button show-pagination-button' . $active . '" onclick="' . $onclick . '">';
						$list .= '<a href="javascript:void(0);">';
							$list .= $pagenum;
						$list .= '</a>';
					$list .= '</div>';
				}
				$list .= '<div class="show-pagination-button" onclick="radio_show_page(' . $show_id . ', \'' . $type . 's\', \'next\');">';
					$list .= '<a href="javascript:void(0);">&rarr;</a>';
				$list .= '</div>';
			$list .= '<input type="hidden" id="show-' . $show_id . '-' . $type . 's-current-page" value="1">';
			$list .= '<input type="hidden" id="show-' . $show_id . '-' . $type . 's-page-count" value="' . $post_pages . '">';
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
// Show Posts List Shortcode
// -------------------------
// requires: show shortcode attribute, eg. [show-posts-list show="1"]
add_shortcode( 'show-posts-archive', 'radio_station_show_posts_list' );
add_shortcode( 'show-posts-list', 'radio_station_show_posts_list' );
function radio_station_show_posts_list( $atts ) {
	$output = radio_station_show_list_shortcode( 'post', $atts );
	return $output;
}

// -----------------------------
// Show Playlists List Shortcode
// -----------------------------
// requires: show shortcode attribute, eg. [show-playlists-list show="1"]
add_shortcode( 'show-playlists-archive', 'radio_station_show_playlists_list' );
add_shortcode( 'show-playlists-list', 'radio_station_show_playlists_list' );
function radio_station_show_playlists_list( $atts ) {
	$output = radio_station_show_list_shortcode( 'playlist', $atts );
	return $output;
}

// --------------------------------
// Show Lists Pagination Javascript
// --------------------------------
function radio_station_pagination_javascript() {
	
	// --- fade out current page and fade in selected page ---
	echo "<script>
	function radio_show_page(id, types, pagenum) {
		currentpage = document.getElementById('show-'+id+'-'+types+'-current-page').value;
		console.log(currentpage);
		console.log(pagenum);
		if (pagenum == 'next') {
			pagenum = parseInt(currentpage) + 1;
			pagecount = document.getElementById('show-'+id+'-'+types+'-page-count').value;
			if (pagenum > pagecount) {return;}
		}
		if (pagenum == 'prev') {
			pagenum = parseInt(currentpage) - 1;
			if (pagenum < 1) {return;}
		}
		console.log(pagenum);
		/* if (typeof jQuery == 'function') {
			console.log('.show-'+id+'-'+types+'-page');
			jQuery('.show-'+id+'-'+types+'-page').fadeOut(500);
			jQuery('#show-'+id+'-'+types+'-page-'+pagenum).fadeIn(1000);
			jQuery('.show-'+id+'-'+types+'-page-button').removeClass('active');
			jQuery('#show-'+id+'-'+types+'-page-button-'+pagenum).addClass('active');
			jQuery('#show-'+id+'-'+types+'-current-page').val(pagenum);
		} else  { */
			pages = document.getElementsByClassName('show-'+id+'-'+types+'-page');
			for (i = 0; i < pages.length; i++) {pages[i].style.display = 'none';}
			document.getElementById('show-'+id+'-'+types+'-page-'+pagenum).style.display = '';
			buttons = document.getElementsByClassName('show-'+id+'-'+types+'-page-button');
			for (i = 0; i < buttons.length; i++) {buttons[i].classList.remove('active');}
			document.getElementById('show-'+id+'-'+types+'-page-button-'+pagenum).classList.add('active');
			document.getElementById('show-'+id+'-'+types+'-current-page').value = pagenum;
		/* } */
	}</script>";
}


// -------------------------
// === Legacy Shortcodes ===
// -------------------------

// ------------------------
// Show Playlists Shortcode
// ------------------------
// [get-playlists]
/* Shortcode to fetch all playlists for a given show id
 * Since 2.0.0
 */
// 2.3.0: added missing output sanitization
add_shortcode( 'get-playlists', 'radio_station_shortcode_get_playlists_for_show' );
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

	// 2.3.0: return empty if no posts found
	if ( $query->post_count === 0 ) {return '';}

	$output = '';

	$output .= '<div id="myplaylist-playlistlinks">';
	$output .= '<ul class="myplaylist-linklist">';
	foreach ( $playlists as $playlist ) {
		$output .= '<li class="myplaylist-linklist-item">';
			$output .= '<a href="' . esc_url( get_permalink( $playlist->ID ) ) . '">';
			$output .= esc_attr( $playlist->post_title ) . '</a>';
		$output .= '</li>';
	}
	$output .= '</ul>';

	$playlist_archive = get_post_type_archive_link( 'playlist' );
	$params           = array( 'show_id' => $atts['show'] );
	$playlist_archive = add_query_arg( $params, $playlist_archive );

	$output .= '<a href="' . esc_url( $playlist_archive ) . '">' . esc_html__( 'More Playlists', 'radio-station' ) . '</a>';

	$output .= '</div>';

	return $output;
}

// -------------------
// Show List Shortcode
// -------------------
// [list-shows]
/* Shortcode for displaying a list of all shows
 * Since 2.0.0
 */
add_shortcode( 'list-shows', 'radio_station_shortcode_list_shows' );
function radio_station_shortcode_list_shows( $atts ) {

	$defaults = array(
		'title'			=> false,
		'genre'			=> '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'list-shows' );

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
				// 2.3.0: fix key/value to meta_key/meta_value
				'meta_key'		=> 'show_active',
				'meta_value'	=> 'on',
				'compare'		=> '=',
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

	// 2.3.0: use get_posts instead of WP_Query
	$posts = get_posts( $args );

	// if there are no shows saved, return nothing
	if ( !$posts || ( count( $posts ) == 0 ) ) {return '';}

	$output = '';

	$output .= '<div id="station-show-list">';
	
		if ( $atts['title'] ) {
			$output .= '<div class="station-show-list-title">';
				$output .= '<h3>' . esc_attr( $atts['title'] ) . '</h3>';
			$output .= '</div>';
		}
	
		$output .= '<ul class="show-list">';
		
		// 2.3.0: use posts loop instead of query loop
		foreach ( $posts as $post ) {
			
			$output .= '<li class="show-list-item">';
			
				$output .= '<div class="show-list-item-title">';
					$output .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">';
					$output .= esc_attr( get_the_title( $post->ID ) ) . '</a>';
				$output .= '</div>';
				
			$output .= '</li>';
		};
		
		$output .= '</ul>';
	$output .= '</div>';

	return $output;
}

// ----------------------
// Current Show Shortcode
// ----------------------
// [dj-widget]
/* Shortcode function for current DJ on-air
 * Since 2.0.9
 */
// 2.3.0: added missing output sanitization
add_shortcode( 'dj-widget', 'radio_station_shortcode_dj_on_air' );
function radio_station_shortcode_dj_on_air( $atts ) {

	$defaults =	array(
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
	);

	$atts = shortcode_atts( $defaults, $atts, 'dj-widget' );

	// find out which DJ(s) are currently scheduled to be on-air and display them
	$djs      = radio_station_dj_get_current();
	$playlist = radio_station_myplaylist_get_now_playing();

	$dj_str = '';

	$dj_str .= '<div class="on-air-embedded dj-on-air-embedded">';
	if ( ! empty( $atts['title'] ) ) {
		$dj_str .= '<h3>' . esc_attr( $atts['title'] ) . '</h3>';
	}
	$dj_str .= '<ul class="on-air-list">';

	// echo the show/dj currently on-air
	if ( 'override' === $djs['type'] ) {

		$dj_str .= '<li class="on-air-dj">';
		if ( $atts['show_avatar'] ) {
			// 2.3.0: use new get show avatar function
			$show_avatar = radio_station_get_show_avatar( $djs['all'][0]['post_id'] );
			if ( $show_avatar ) {
				$dj_str .= '<span class="on-air-dj-avatar">';
				$dj_str .= $show_avatar;
				$dj_str	.= '</span>';
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
					// 2.3.0: use new get show avatar function
					$show_avatar = radio_station_get_show_avatar( $dj->ID );
					if ( $show_avatar ) {
						$dj_str .= '<span class="on-air-dj-avatar">';
						$dj_str .= $show_avatar;
						$dj_str .= '</span>';
					}
				}

				$dj_str .= '<span class="on-air-dj-title">';
				if ( $atts['show_link'] ) {
					$dj_str .= '<a href="' . esc_url( get_permalink( $dj->ID ) ) . '">' . esc_attr( $dj->post_title ) . '</a>';
				} else {
					$dj_str .= esc_attr( $dj->post_title );
				}
				$dj_str .= '</span>';

				if ( $atts['display_djs'] ) {

					$names = get_post_meta( $dj->ID, 'show_user_list', true );
					$count = 0;

					if ( $names ) {

						$dj_str .= '<div class="on-air-dj-names">' . esc_html__( 'With', 'radio-station' ) . ' ';
						foreach ( $names as $name ) {
							$count++;
							$user_info = get_userdata( $name );

							$dj_str .= esc_attr( $user_info->display_name );

							$count_names = count( $names );
							if ( ( 1 === $count && 2 === $count_names ) || ( $count_names > 2 && $count === $count_names - 1 ) ) {
								$dj_str .= ' ' . esc_html__( 'and', 'radio-station' ) . ' ';
							} elseif ( $count < $count_names && $count_names > 2 ) {
								$dj_str .= ', ';
							}
						}
						$dj_str .= '</div>';
					}
				}

				if ( $atts['show_desc'] ) {
					$desc_string = radio_station_shorten_string( wp_strip_all_tags( $dj->post_content ), 20 );
					$dj_str     .= '<span class="on-air-show-desc">' . esc_html__( $desc_string ) . '</span>';
				}

				if ( $atts['show_playlist'] ) {
					$dj_str .= '<span class="on-air-dj-playlist"><a href="' . esc_url( $playlist['playlist_permalink'] ) . '">' . esc_html__( 'View Playlist', 'radio-station' ) . '</a></span>';
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

// -------------------
// Coming Up Shortcode
// -------------------
// [dj-coming-up-widget]
/* Shortcode for displaying upcoming DJs/shows
 * Since 2.0.9
*/
// 2.3.0: added missing output sanitization
add_shortcode( 'dj-coming-up-widget', 'radio_station_shortcode_coming_up' );
function radio_station_shortcode_coming_up( $atts ) {

	$defaults = array(
		'title'       => '',
		'display_djs' => 0,
		'show_avatar' => 0,
		'show_link'   => 0,
		'limit'       => 1,
		'time'        => '12',
		'show_sched'  => 1,
	);
	$atts = shortcode_atts( $defaults, $atts, 'dj-coming-up-widget' );

	// find out which DJ(s) are coming up today
	$djs = radio_station_dj_get_next( $atts['limit'] );
	if ( ! isset( $djs['all'] ) || count( $djs['all'] ) <= 0 ) {
		$output = '<li class="on-air-dj default-dj">' . esc_html__( 'None Upcoming', 'radio-station' ) . '</li>';
		return $output;
	}

	ob_start();
	?>
	<div class="on-air-embedded dj-coming-up-embedded">
		<?php
		if ( ! empty( $atts['title'] ) ) {
			?>
			<h3><?php echo esc_attr( $atts['title'] ); ?></h3>
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
							// 2.3.0: use new get show avatar function
							$show_avatar = radio_station_get_show_avatar(  $dj['post_id'] );
							if ( $show_avatar ) {
								echo '<span class="on-air-dj-avatar">';
									echo $show_avatar;
								echo '</span>';
							}
						}

						echo esc_html( $dj['title'] );
						if ( $atts['show_sched'] ) {

							if ( 12 === (int) $atts['time'] ) {
								?>
								<span class="on-air-dj-sched">
									<?php echo esc_html( $dj['sched']['start_hour'] . ':' . esc_html( $dj['sched']['start_min'] ) . ' ' . $dj['sched']['start_meridian'] . '-' . esc_html( $dj['sched']['end_hour'] ) . ':' . esc_html( $dj['sched']['end_min'] ) . ' ' . esc_html( $dj['sched']['end_meridian'] ) ); ?>
								</span><br />
								<?php
							} else {
								$dj['sched'] = radio_station_convert_schedule_to_24hour( $dj['sched'] );
								?>
								<span class="on-air-dj-sched">
									<?php echo esc_html( $dj['sched']['start_hour'] ) . ':' . esc_html( $dj['sched']['start_min'] ) . ' -' . esc_html( $dj['sched']['end_hour'] ) . ':' . esc_html( $dj['sched']['end_min'] ); ?>
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
							// 2.3.0: use new get show avatar function
							$show_avatar = radio_station_get_show_avatar( $dj->ID );
							if ( $show_avatar ) {
								echo '<span class="on-air-dj-avatar">';
								echo $show_avatar;
								echo '</span>';
							}
						}
						?>
						<span class="on-air-dj-title">
							<?php
							if ( $atts['show_link'] ) {
								?>
								<a href="<?php echo esc_url( get_permalink( $dj->ID ) ); ?>">
									<?php echo esc_attr( $dj->post_title ); ?>
								</a>
								<?php
							} else {
								echo esc_attr( $dj->post_title );
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
											echo ' ' . esc_html__( 'and', 'radio-station') . ' ';
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
										<?php
											// 2.3.0: added missing weekday translation
											echo radio_station_translate_weekday( date( 'l', $show_times[0] ) );
										?>,
									</span>
									<?php echo date( 'g:i a', $show_times[0] ) . '-' . date( 'g:i a', $show_times[1] ); ?>
								</span><br />
							<?php } else { ?>
								<span class="on-air-dj-sched">
									<span class="on-air-dj-sched-day">
										<?php
											// 2.3.0: added missing weekday translation
											echo radio_station_translate_weekday( date( 'l', $show_times[0] ) );
										?>,
									</span>
									<?php echo date( 'H:i', $show_times[0] ) . '-' . date( 'H:i', $show_times[1] ); ?>
								</span><br />
							<?php }
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

// ---------------------
// Now Playing Shortcode
// ---------------------
// [now-playing]
// 2.3.0: added missing output sanitization
add_shortcode( 'now-playing', 'radio_station_shortcode_now_playing' );
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

		$output .= '<div id="myplaylist-nowplaying" class="' . esc_attr( $class ) . '">';
		if ( ! empty( $atts['title'] ) ) {
			$output .= '<h3>' . $atts['title'] . '</h3>';}

		if ( 1 === $atts['song'] ) {
			$output .= '<span class="myplaylist-song">' . esc_html( $most_recent['playlist_entry_song'] ) . '</span> ';
		}
		if ( 1 === $atts['artist'] ) {
			$output .= '<span class="myplaylist-artist">' . esc_html( $most_recent['playlist_entry_artist'] ) . '</span> ';
		}
		if ( 1 === $atts['album'] ) {
			$output .= '<span class="myplaylist-album">' . esc_html( $most_recent['playlist_entry_album'] ) . '</span> ';
		}
		if ( 1 === $atts['label'] ) {
			$output .= '<span class="myplaylist-label">' . esc_html( $most_recent['playlist_entry_label'] ) . '</span> ';
		}
		if ( 1 === $atts['comments'] ) {
			$output .= '<span class="myplaylist-comments">' . esc_html( $most_recent['playlist_entry_comments'] ) . '</span> ';
		}
		$output .= '<span class="myplaylist-link"><a href="' . $most_recent['playlist_permalink'] . '">' . esc_html( __( 'View Playlist', 'radio-station' ) ) . '</a></span> ';
		$output .= '</div>';

	} else {
		$output .= esc_html__( 'No playlists available.', 'radio-station' );
	}

	return $output;
}

