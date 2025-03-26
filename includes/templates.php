<?php

// ===============================
// === Radio Station Templates ===
// ===============================
// 2.5.0: separated from radio-station.php

if ( !defined( 'ABSPATH' ) ) exit;

// === Template Filters ===
// - Doing Template Check
// - Get Template
// - Station Phone Number Filter
// - Automatic Pages Content Filter
// - Single Content Template Filter
// - Show Content Template Filter
// - Playlist Content Template Filter
// - Override Content Template Filter
// - DJ / Host / Producer Template Fix
// - Get DJ / Host Template
// - Get Producer Template
// - Single Template Hierarchy
// - Single Templates Loader
// - Archive Template Hierarchy
// x Archive Templates Loader
// - Add Links Back to Show
// - Show Posts Adjacent Links
// === Query Filters ===
// - Playlist Archive Query Filter
// - Schedule Override Filters
// === Schedule Override Filters ===
// - Add Override Template Filters
// - Override Show Title
// - Override Show Avatar
// - Override Show Avatar ID
// - Override Show Thumbnail
// - Override Show Thumbnail ID
// x Override Show Header
// - Override Show Hosts
// - Override Show Producers
// - Override Show Link
// - Override Show Email
// - Override Show Phone
// - Override Show File
// - Override Show File
// - Override Disable Download
// - Override Show Patreon
// - Override Show Shifts


// ------------------------
// === Template Filters ===
// ------------------------

// --------------------
// Doing Template Check
// --------------------
// 2.3.3.9: added to help distinguish filter contexts
function radio_station_doing_template() {
	global $radio_station_data;
	if ( isset( $radio_station_data['doing-template'] ) && $radio_station_data['doing-template'] ) {
		return true;
	}
	return false;
}

// ------------
// Get Template
// ------------
// 2.3.0: added for template file hierarchy
function radio_station_get_template( $type, $template, $paths = false ) {

	global $radio_station_data;

	// --- maybe set default paths ---
	if ( !$paths ) {
		if ( isset( $radio_station_data['template-dirs'] ) ) {
			$dirs = $radio_station_data['template-dirs'];
		}
		$paths = array( 'templates', '' );
	} elseif ( is_string( $paths ) ) {
		if ( 'css' == $paths ) {
			if ( isset( $radio_station_data['style-dirs'] ) ) {
				$dirs = $radio_station_data['style-dirs'];
			}
			$paths = array( 'css', 'styles', '' );
		} elseif ( 'js' == $paths ) {
			if ( isset( $radio_station_data['script-dirs'] ) ) {
				$dirs = $radio_station_data['script-dirs'];
			}
			$paths = array( 'js', 'scripts', '' );
		}
	}

	if ( !isset( $dirs ) ) {
		$dirs = array();
		$styledir = get_stylesheet_directory();
		$styledirurl = get_stylesheet_directory_uri();
		$templatedir = get_template_directory();
		$templatedirurl = get_template_directory_uri();

		// --- maybe generate default hierarchies ---
		foreach ( $paths as $path ) {
			$dirs[] = array(
				'path'    => $styledir . '/' . $path,
				'urlpath' => $styledirurl . '/' . $path,
			);
		}
		if ( $styledir != $templatedir ) {
			foreach ( $paths as $path ) {
				$dirs[] = array(
					'path'    => $templatedir . '/' . $path,
					'urlpath' => $templatedirurl . '/' . $path,
				);
			}
		}
		if ( defined( 'RADIO_STATION_PRO_DIR' ) ) {
			foreach ( $paths as $path ) {
				$dirs[] = array(
					'path'    => RADIO_STATION_PRO_DIR . '/' . $path,
					'urlpath' => plugins_url( $path, RADIO_STATION_PRO_FILE ),
				);
			}
		}
		foreach ( $paths as $path ) {
			$dirs[] = array(
				'path'    => RADIO_STATION_DIR . '/' . $path,
				'urlpath' => plugins_url( $path, RADIO_STATION_FILE ),
			);
		}
	}
	$dirs = apply_filters( 'radio_station_template_dir_hierarchy', $dirs, $template, $paths );

	// --- loop directory hierarchy to find first template ---
	foreach ( $dirs as $dir ) {

		// 2.3.4: use trailingslashit to account for empty paths
		$template_path = trailingslashit( $dir['path'] ) . $template;
		$template_url = trailingslashit( $dir['urlpath'] ) . $template;

		if ( file_exists( $template_path ) ) {
			if ( 'file' == (string) $type ) {
				return $template_path;
			} elseif ( 'url' === (string) $type ) {
				return $template_url;
			} else {
				return array( 'file' => $template_path, 'url' => $template_url );
			}
		}
	}

	return false;
}

// -------------------------------------
// Station Phone Number for Shows Filter
// -------------------------------------
// 2.3.3.6: added to return station phone for all Shows (if not set for Show)
add_filter( 'radio_station_show_phone', 'radio_station_phone_number', 10, 2 );
function radio_station_phone_number( $phone, $post_id ) {
	if ( $phone ) {
		return $phone;
	}
	$shows_phone = radio_station_get_setting( 'shows_phone' );
	if ( 'yes' == $shows_phone ) {
		$phone = radio_station_get_setting( 'station_phone' );
		return $phone;
	}
	return false;
}

// --------------------------------------
// Station Email Address for Shows Filter
// --------------------------------------
// 2.3.3.8: added to return station email for all Shows (if not set for Show)
add_filter( 'radio_station_show_email', 'radio_station_email_address', 10, 2 );
function radio_station_email_address( $email, $post_id ) {
	if ( $email ) {
		return $email;
	}
	$shows_email = radio_station_get_setting( 'shows_email' );
	if ( 'yes' == $shows_email ) {
		$email = radio_station_get_setting( 'station_email' );
		return $email;
	}
	return false;
}

// ------------------------------
// Automatic Pages Content Filter
// ------------------------------
// 2.3.0: standalone filter for automatic page content
// 2.3.1: re-add filter so the_content can be processed multiple times
// 2.3.3.6: set automatic content early and clear existing content
// 2.5.0: fix to append to shortcode attribute strings
add_filter( 'the_content', 'radio_station_automatic_pages_content_set', 1 );
function radio_station_automatic_pages_content_set( $content ) {

	global $radio_station_data;

	// if ( isset( $radio_station_data['doing_excerpt'] ) && $radio_station_data['doing_excerpt'] ) {
	//	return $content;
	// }

	// --- for automatic output on selected master schedule page ---
	$schedule_page = radio_station_get_setting( 'schedule_page' );
	if ( !is_null( $schedule_page ) && !empty( $schedule_page ) ) {
		if ( is_page( $schedule_page ) ) {
			$automatic = radio_station_get_setting( 'schedule_auto' );
			if ( 'yes' === (string) $automatic ) {
				$view = radio_station_get_setting( 'schedule_view' );
				$atts = array( 'view' => $view );
				$atts = apply_filters( 'radio_station_automatic_schedule_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string .= ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[master-schedule' . $atts_string . ']';
			}
		}
	}

	// --- show archive page ---
	// 2.3.0: added automatic display of show archive page
	$show_archive_page = radio_station_get_setting( 'show_archive_page' );
	if ( !is_null( $show_archive_page ) && !empty( $show_archive_page ) ) {
		if ( is_page( $show_archive_page ) ) {
			$automatic = radio_station_get_setting( 'show_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				// $view = radio_station_get_setting( 'show_archive_view' );
				// if ( $view ) {
				// 	$atts['view'] = $view;
				// }
				$atts = apply_filters( 'radio_station_automatic_show_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string .= ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[shows-archive' . $atts_string . ']';
			}
		}
	}

	// --- override archive page ---
	// 2.3.0: added automatic display of override archive page
	$override_archive_page = radio_station_get_setting( 'override_archive_page' );
	if ( !is_null( $override_archive_page ) && !empty( $override_archive_page ) ) {
		if ( is_page( $override_archive_page ) ) {
			$automatic = radio_station_get_setting( 'override_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				// $view = radio_station_get_setting( 'override_archive_view' );
				// if ( $view ) {
				// 	$atts['view'] = $view;
				// }
				$atts = apply_filters( 'radio_station_automatic_override_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string .= ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[overrides-archive' . $atts_string . ']';
			}
		}
	}

	// --- playlist archive page ---
	// 2.3.0: added automatic display of playlist archive page
	$playlist_archive_page = radio_station_get_setting( 'playlist_archive_page' );
	if ( !is_null( $playlist_archive_page ) && !empty( $playlist_archive_page ) ) {
		if ( is_page( $playlist_archive_page ) ) {
			$automatic = radio_station_get_setting( 'playlist_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				// $view = radio_station_get_setting( 'playlist_archive_view' );
				// if ( $view ) {
				// 	$atts['view'] = $view;
				// }
				$atts = apply_filters( 'radio_station_automatic_playlist_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string .= ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[playlists-archive' . $atts_string . ']';
			}
		}
	}

	// --- genre archive page ---
	// 2.3.0: added automatic display of genre archive page
	$genre_archive_page = radio_station_get_setting( 'genre_archive_page' );
	if ( !is_null( $genre_archive_page ) && !empty( $genre_archive_page ) ) {
		if ( is_page( $genre_archive_page ) ) {
			$automatic = radio_station_get_setting( 'genre_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				// $view = radio_station_get_setting( 'genre_archive_view' );
				// if ( $view ) {
				// 	$atts['view'] = $view;
				// }
				$atts = apply_filters( 'radio_station_automatic_genre_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string .= ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[genres-archive' . $atts_string . ']';
			}
		}
	}

	// --- languages archive page ---
	// 2.3.3.9: added automatic display of language archive page
	$language_archive_page = radio_station_get_setting( 'language_archive_page' );
	if ( !is_null( $language_archive_page ) && !empty( $language_archive_page ) ) {
		if ( is_page( $language_archive_page ) ) {
			$automatic = radio_station_get_setting( 'language_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				// $view = radio_station_get_setting( 'language_archive_view' );
				// if ( $view ) {
				// 	$atts['view'] = $view;
				// }
				$atts = apply_filters( 'radio_station_automatic_languagee_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string .= ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[languages-archive' . $atts_string . ']';
			}
		}
	}

	// 2.3.3.6: moved out to reduce repetitive code
	if ( isset( $shortcode ) ) {
		remove_filter( 'the_content', 'radio_station_automatic_pages_content_set', 1 );
		remove_filter( 'the_content', 'radio_station_automatic_pages_content_get', 11 );
		$radio_station_data['automatic_content'] = do_shortcode( $shortcode );
		// 2.3.1: re-add filter so the_content may be processed multuple times
		add_filter( 'the_content', 'radio_station_automatic_pages_content_set', 1 );
		add_filter( 'the_content', 'radio_station_automatic_pages_content_get', 11 );
		// 2.3.3.6: clear existing content to allow for interim filters
		$content = '';
	}

	return $content;
}

// ----------------------------------
// Automatic Pages Content Set Filter
// ----------------------------------
// 2.3.3.6: append existing automatic page content to allow for interim filters
add_filter( 'the_content', 'radio_station_automatic_pages_content_get', 11 );
function radio_station_automatic_pages_content_get( $content ) {
	global $radio_station_data;
	if ( isset( $radio_station_data['automatic_content'] ) ) {
		$content .= $radio_station_data['automatic_content'];
	}
	return $content;
}


// ------------------------------
// Single Content Template Filter
// ------------------------------
// 2.3.0: moved here and abstracted from templates/single-show.php
// 2.3.0: standalone filter name to allow for replacement
function radio_station_single_content_template( $content, $post_type ) {

	// --- check if single plugin post type ---
	if ( !is_singular( $post_type ) ) {
		return $content;
	}

	// --- check for user content templates ---
	// 2.3.3.9: allow for prefixed and unprefixed post types
	$theme_dir = get_stylesheet_directory();
	$templates = array();
	$templates[] = $theme_dir . '/templates/single-' . $post_type . '-content.php';
	$templates[] = $theme_dir . '/single-' . $post_type . '-content.php';
	$templates[] = RADIO_STATION_DIR . '/templates/single-' . $post_type . '-content.php';
	$unprefixed_post_type = str_replace( 'rs-', '', $post_type );
	if ( $post_type != $unprefixed_post_type ) {
		$templates[] = $theme_dir . '/templates/single-' . $unprefixed_post_type . '-content.php';
		$templates[] = $theme_dir . '/single-' . $unprefixed_post_type . '-content.php';
		$templates[] = RADIO_STATION_DIR . '/templates/single-' . $unprefixed_post_type . '-content.php';
	}

	// 2.3.0: fallback to show content template for overrides
	if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
		// $templates[] = $theme_dir . '/templates/single-rs-show-content.php';
		// $templates[] = $theme_dir . '/single-rs-show-content.php';
		// $templates[] = RADIO_STATION_DIR . '/templates/single-rs-show-content.php';
		$templates[] = $theme_dir . '/templates/single-show-content.php';
		$templates[] = $theme_dir . '/single-show-content.php';
		$templates[] = RADIO_STATION_DIR . '/templates/single-show-content.php';
	}
	$templates = apply_filters( 'radio_station_' . $post_type . '_content_templates', $templates, $post_type );
	// 2.5.6: added check that templates is still a populated array
	if ( is_array( $templates ) && ( count( $templates ) > 0 ) ) {
		foreach ( $templates as $template ) {
			if ( file_exists( $template ) ) {
				$content_template = $template;
				break;
			}
		}
	}
	if ( !isset( $content_template ) ) {
		return $content;
	}

	// --- enqueue template styles ---
	// 2.3.3.9: check post type for page template style enqueue
	$page_templates = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG, RADIO_STATION_PLAYLIST_SLUG );
	if ( in_array( $post_type, $page_templates ) ) {
		radio_station_enqueue_style( 'templates' );
	}
	// 2.3.3.9: fire action for enqueueing other template styles
	do_action( 'radio_station_enqueue_template_styles', $post_type );

	// --- enqueue dashicons for frontend ---
	wp_enqueue_style( 'dashicons' );

	// --- filter post before including template ---
	global $post;
	$original_post = $post;
	$post = apply_filters( 'radio_station_single_template_post_data', $post, $post_type );

	// --- start buffer and include content template ---
	ob_start();
	include $content_template;
	$output = ob_get_contents();
	ob_end_clean();

	// --- restore post global to be safe ---
	$post = $original_post;

	// --- filter and return buffered content ---
	$output = str_replace( '<!-- the_content -->', $content, $output );
	$post_id = get_the_ID();
	$output = apply_filters( 'radio_station_content_' . $post_type, $output, $post_id );

	return $output;
}

// ------------------------------------
// Filter for Override Show Linked Data
// ------------------------------------
add_filter( 'radio_station_single_template_post_data', 'radio_station_override_linked_show_data', 10, 2 );
function radio_station_override_linked_show_data( $post, $post_type ) {
	if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
		$linked_id = get_post_meta( $post->ID, 'linked_show_id', true );
		if ( $linked_id ) {
			$show_post = get_post( $linked_id );
			if ( $show_post ) {
				$linked_fields = get_post_meta( $post->ID, 'linked_show_fields', true );
				if ( $linked_fields ) {
					foreach ( $linked_fields as $key => $switch ) {
						if ( !$switch ) {
							if ( 'show_title' == $key ) {
								$post->post_title = $show_post->post_title;
							} elseif ( 'show_excerpt' == $key ) {
								$post->post_excerpt = $show_post->post_excerpt;
							} elseif ( 'show_content' == $key ) {
								$post->post_content = $show_post->post_content;
							}
						}
					}
				}
			}
		}
	}
	return $post;
}

// ----------------------------
// Show Content Template Filter
// ----------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_show_content_template', 11 );
function radio_station_show_content_template( $content ) {
	// 2.5.8: added early bypass for non-show pages
	if ( !is_singular( RADIO_STATION_SHOW_SLUG ) ) {
		return $content;
	}
	remove_filter( 'the_content', 'radio_station_show_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_SHOW_SLUG );
	// 2.3.1: re-add filter so the_content can be processed multuple times
	add_filter( 'the_content', 'radio_station_show_content_template', 11 );
	return $output;
}

// ----------------------------
// Show Excerpt Template Filter
// ----------------------------
// 2.5.8: added fix for themes showing only the excerpt on single show pages
add_filter( 'the_excerpt', 'radio_station_show_content_template_excerpt', 11 );
function radio_station_show_content_template_excerpt( $content ) {
	if ( !is_singular( RADIO_STATION_SHOW_SLUG ) ) {
		return $content;
	}

	// 2.5.8: pass unprocesed content as description to content template
	remove_filter( 'the_content', 'radio_station_show_content_template', 11 );
	ob_start();
	the_content();
	$content = ob_get_contents();
	ob_end_clean();
	add_filter( 'the_content', 'radio_station_show_content_template', 11 );
	
	remove_filter( 'the_excerpt', 'radio_station_show_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_SHOW_SLUG );
	add_filter( 'the_excerpt', 'radio_station_show_content_template', 11 );
	// add_filter( 'the_content', 'radio_station_deduplicate_content', 99 );

	return $output;
}

// --------------------------------
// Playlist Content Template Filter
// --------------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
function radio_station_playlist_content_template( $content ) {
	// 2.5.8: added early bypass for non-playlist pages
	if ( !is_singular( RADIO_STATION_PLAYLIST_SLUG ) ) {
		return $content;
	}
	remove_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_PLAYLIST_SLUG );
	// 2.3.1: re-add filter so the_content can be processed multuple times
	add_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
	return $output;
}

// --------------------------------
// Playlist Excerpt Template Filter
// --------------------------------
// 2.5.8: added fix for themes showing only the excerpt on single show pages
add_filter( 'the_excerpt', 'radio_station_playlist_content_template_excerpt', 11 );
function radio_station_playlist_content_template_excerpt( $content ) {
	if ( !is_singular( RADIO_STATION_PLAYLIST_SLUG ) ) {
		return $content;
	}
	remove_filter( 'the_excerpt', 'radio_station_playlist_content_template_excerpt', 11 );
	remove_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_PLAYLIST_SLUG );
	add_filter( 'the_excerpt', 'radio_station_playlist_content_template_excerpt', 11 );
	// add_filter( 'the_content', 'radio_station_deduplicate_content', 99 );
	return $output;
}

// --------------------------
// Deduplicate Content Filter
// --------------------------
function radio_station_deduplicate_content( $content ) {
	return '';
}

// --------------------------------
// Override Content Template Filter
// --------------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_override_content_template', 11 );
function radio_station_override_content_template( $content ) {
	// 2.5.8: added early bypass for non-override pages
	if ( !is_singular( RADIO_STATION_OVERRIDE_SLUG ) ) {
		return $content;
	}
	remove_filter( 'the_content', 'radio_station_override_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_OVERRIDE_SLUG );
	// 2.3.1: re-add filter so the_content can be processed multiple times
	add_filter( 'the_content', 'radio_station_override_content_template', 11 );
	return $output;
}

// --------------------------------
// Override Excerpt Template Filter
// --------------------------------
// 2.5.8: added fix for themes showing only the excerpt on single pages
add_filter( 'the_excerpt', 'radio_station_override_content_template_excerpt', 11 );
function radio_station_override_content_template_excerpt( $content ) {
	if ( !is_singular( RADIO_STATION_OVERRIDE_SLUG ) ) {
		return $content;
	}

	// 2.5.8: pass unprocesed content as description to content template
	remove_filter( 'the_content', 'radio_station_override_content_template', 11 );
	ob_start();
	the_content();
	$content = ob_get_contents();
	ob_end_clean();
	add_filter( 'the_content', 'radio_station_override_content_template', 11 );

	remove_filter( 'the_excerpt', 'radio_station_override_content_template_excerpt', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_OVERRIDE_SLUG );
	add_filter( 'the_excerpt', 'radio_station_override_content_template_excerpt', 11 );
	// add_filter( 'the_content', 'radio_station_deduplicate_content', 99 );
	return $output;
}

// ----------------------------------
// Override Content with Show Content
// ----------------------------------
// 2.3.3.9: maybe use show content for override content
add_filter( 'the_content', 'radio_station_override_content', 0 );
function radio_station_override_content( $content ) {
	if ( !is_singular( RADIO_STATION_OVERRIDE_SLUG ) ) {
		return $content;
	}
	remove_filter( 'the_content', 'radio_station_override_content', 0 );
	global $post;
	$override = radio_station_get_show_override( $post->ID, 'show_content' );
	if ( false !== $override ) {
		$override = radio_station_override_linked_show_data( $post, RADIO_STATION_OVERRIDE_SLUG );
		$content = $override->post_content;
	}
	add_filter( 'the_content', 'radio_station_override_content', 0 );
	return $content;
}

// ---------------------------------
// DJ / Host / Producer Template Fix
// ---------------------------------
// 2.2.8: temporary fix to not 404 author pages for DJs without blog posts
// Ref: https://wordpress.org/plugins/show-authors-without-posts/
add_filter( '404_template', 'radio_station_author_host_pages' );
function radio_station_author_host_pages( $template ) {

	global $wp_query;

	if ( !is_author() ) {

		if ( get_query_var( 'host' ) ) {

			// --- get user by ID or name ---
			$host = get_query_var( 'host' );
			// 2.5.10: fix to numeric check
			if ( ctype_digit( $host ) ) {
				$user = get_user_by( 'ID', $host );
			} else {
				$user = get_user_by( 'slug', $host );
			}

			// --- check if specified user has DJ/host role ---
			if ( $user && in_array( 'dj', $user->roles ) ) {
				$host_template = radio_station_get_host_template();
				if ( $host_template ) {
					$template = $host_template;
				}
			}

		} elseif ( get_query_var( 'producer' ) ) {

			// --- get user by ID or name ---
			// 2.5.10: fix to numeric check
			$producer = get_query_var( 'producer' );
			if ( ctype_digit( $producer ) ) {
				$user = get_user_by( 'ID', $producer );
			} else {
				$user = get_user_by( 'slug', $producer );
			}

			// --- check if specified user has producer role ---
			if ( $user && in_array( 'producer', $user->roles ) ) {
				$producer_template = radio_station_get_producer_template();
				if ( $producer_template ) {
					$template = $producer_template;
				}
			}

		} elseif ( get_query_var( 'author' ) && ( 0 == $wp_query->posts->post ) ) {

			// --- get the author user ---
			if ( get_query_var( 'author_name' ) ) {
				$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
			} else {
				$author = get_userdata( get_query_var( 'author' ) );
			}

			if ( $author ) {

				// --- check if author has DJ, producer or administrator role ---
				if ( in_array( 'dj', $author->roles )
					|| in_array( 'producer', $author->roles )
					|| in_array( 'administrator', $author->roles ) ) {

					// TODO: maybe check if user is assigned to any shows ?
					$template = get_author_template();
				}
			}

		}

	}

	return $template;
}

// ----------------------
// Get DJ / Host Template
// ----------------------
// 2.3.0: added get DJ template function
// (modified template hierarchy from get_page_template)
function radio_station_get_host_template() {

	$templates = array();
	$hostname = get_query_var( 'host' );
	if ( $hostname ) {
		$hostname_decoded = urldecode( $hostname );
		if ( $hostname_decoded !== $hostname ) {
			$templates[] = 'host-' . $hostname_decoded . '.php';
		}
		$templates[] = 'host-' . $hostname . '.php';
	}
	$templates[] = 'single-host.php';

	$templates = apply_filters( 'radio_station_host_templates', $templates );
	return get_query_template( RADIO_STATION_HOST_SLUG, $templates );
}

// ---------------------
// Get Producer Template
// ---------------------
// 2.3.0: added get producer template function
// (modified template hierarchy from get_page_template)
function radio_station_get_producer_template() {

	$templates = array();
	$producername = get_query_var( 'producer' );
	if ( $producername ) {
		$producername_decoded = urldecode( $producername );
		if ( $producername_decoded !== $producername ) {
			$templates[] = 'producer-' . $producername_decoded . '.php';
		}
		$templates[] = 'producer-' . $producername . '.php';
	}
	$templates[] = 'single-producer.php';

	$templates = apply_filters( 'radio_station_producer_templates', $templates );
	return get_query_template( RADIO_STATION_PRODUCER_SLUG, $templates );
}

// -------------------------
// Single Template Hierarchy
// -------------------------
function radio_station_single_template_hierarchy( $templates ) {

	global $post;

	// --- remove single.php as the show / playlist fallback ---
	// (allows for user selection of page.php or single.php later)
	if ( ( RADIO_STATION_SHOW_SLUG === (string) $post->post_type )
			|| ( RADIO_STATION_OVERRIDE_SLUG === (string) $post->post_type )
			|| ( RADIO_STATION_PLAYLIST_SLUG === (string) $post->post_type ) ) {
		$i = array_search( 'single.php', $templates );
		if ( false !== $i ) {
			unset( $templates[$i] );
		}
	}

	return $templates;
}

// -----------------------
// Single Templates Loader
// -----------------------
add_filter( 'single_template', 'radio_station_load_template', 10, 3 );
function radio_station_load_template( $single_template, $type, $templates ) {

	global $post;

	// --- handle single templates ---
	$post_type = $post->post_type;
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG, RADIO_STATION_PLAYLIST_SLUG );
	// TODO: RADIO_STATION_EPISODE_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG
	if ( in_array( $post_type, $post_types ) ) {

		// --- check for existing template override ---
		// note: single.php is removed from template hierarchy via filter
		remove_filter( 'single_template', 'radio_station_load_template' );
		add_filter( 'single_template_hierarchy', 'radio_station_single_template_hierarchy' );
		$template = get_single_template();
		remove_filter( 'single_template_hierarchy', 'radio_station_single_template_hierarchy' );

		// --- use legacy template ---
		if ( $template ) {

			// --- use the found user template ---
			$single_template = $template;

			// --- check for combined template and content filter ---
			$combined = radio_station_get_setting( $post_type . '_template_combined' );
			if ( 'yes' != $combined ) {
				remove_filter( 'the_content', 'radio_station_' . $post_type . '_content_template', 11 );
			}

		} else {

			// --- get template selection ---
			// 2.3.0: removed default usage of single show/playlist templates (not theme agnostic)
			// 2.3.0: added option for use of template hierarchy
			$show_template = radio_station_get_setting( $post_type . '_template' );

			// --- maybe use legacy template ---
			if ( 'legacy' === (string) $show_template ) {
				return RADIO_STATION_DIR . '/templates/legacy/single-' . $post_type . '.php';
			}

			// --- use post or page template ---
			// 2.3.3.8: added missing singular.php template setting
			if ( 'post' == $show_template ) {
				$templates = array( 'single.php' );
			} elseif ( 'page' == $show_template ) {
				$templates = array( 'page.php' );
			} elseif ( 'singular' == $show_template ) {
				// 2.5.6: fix singular variable to plural
				$templates = array( 'singular.php' );
			}

			// --- add standard fallbacks to index ---
			// 2.3.3.8: remove singular fallback as it is explicitly chosen
			$templates[] = 'index.php';
			$single_template = get_query_template( $post_type, $templates );
		}
	}

	return $single_template;
}

// --------------------------
// Archive Template Hierarchy
// --------------------------
add_filter( 'archive_template_hierarchy', 'radio_station_archive_template_hierarchy' );
function radio_station_archive_template_hierarchy( $templates ) {

	// --- add extra template search path of /templates/ ---
	$post_types = array_filter( (array) get_query_var( 'post_type' ) );
	if ( count( $post_types ) == 1 ) {
		$post_type = reset( $post_types );
		$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_PLAYLIST_SLUG, RADIO_STATION_OVERRIDE_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
		if ( in_array( $post_type, $post_types ) ) {
			$template = array( 'templates/archive-' . $post_type . '.php' );
			// 2.3.0: add fallback to show archive template for overrides
			if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
				$template[] = 'templates/archive-' . RADIO_STATION_SHOW_SLUG . '.php';
			}
			$templates = array_merge( $template, $templates );
		}
	}

	return $templates;
}

// ------------------------
// Archive Templates Loader
// ------------------------
// TODO: implement standard archive page overrides via plugin settings
// add_filter( 'archive_template', 'radio_station_post_type_archive_template', 10, 3 );
function radio_station_post_type_archive_template( $archive_template, $type, $templates ) {

	// --- check for archive template override ---
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_PLAYLIST_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
	foreach ( $post_types as $post_type ) {
		if ( is_post_type_archive( $post_type ) ) {
			$override = radio_station_get_setting( $post_type . '_archive_override' );
			if ( 'yes' !== (string) $override ) {
				$archive_template = get_page_template();
				add_filter( 'the_content', 'radio_station_' . $post_type . '_archive', 11 );
			}
		}
	}

	return $archive_template;
}

// -------------------------
// Add Links to Back to Show
// -------------------------
// 2.3.0: add links to show from show posts and playlists
// 2.3.3.6: allow for multiple related show post assignments
add_filter( 'the_content', 'radio_station_add_show_links', 20 );
function radio_station_add_show_links( $content ) {

	global $post;

	// note: playlists are linked via single-playlist-content.php template

	// 2.4.0.6: bug out if no post object
	if ( !is_object( $post ) ) {
		return $content;
	}

	// --- filter to allow related post types ---
	$post_type = $post->post_type;
	$post_types = array( 'post' );
	$post_types = apply_filters( 'radio_station_show_related_post_types', $post_types );

	if ( in_array( $post_type, $post_types ) ) {

		// --- link show posts ---
		$related_shows = get_post_meta( $post->ID, 'post_showblog_id', true );
		// 2.3.3.6: convert string value if not multiple
		if ( $related_shows && !is_array( $related_shows ) ) {
			$related_shows = array( $related_shows );
		}
		// 2.3.3.6: remove possible zero values
		// 2.3.3.7: added count check for before looping
		if ( $related_shows && ( count( $related_shows ) > 0 ) ) {
			foreach ( $related_shows as $i => $related_show ) {
				if ( 0 == $related_show ) {
					unset( $related_shows[$i] );
				}
			}
		}
		if ( $related_shows && is_array( $related_shows ) && ( count( $related_shows ) > 0 ) ) {

			$positions = array( 'after' );
			$positions = apply_filters( 'radio_station_link_to_show_positions', $positions, $post_type, $post );
			if ( $positions && is_array( $positions ) && ( count( $positions ) > 0 ) ) {
				if ( in_array( 'before', $positions ) || in_array( 'after', $positions ) ) {

					// --- set related shows link(s) ---
					// 2.3.3.6: get all related show links
					$show_links = '';
					$hash_ref = '#show-' . str_replace( 'rs-', '', $post_type ) . 's';
					foreach ( $related_shows as $related_show ) {
						$show = get_post( $related_show );
						$title = $show->post_title;
						$permalink = get_permalink( $show->ID ) . $hash_ref;
						if ( '' != $show_links ) {
							$show_links .= ', ';
						}
						$show_links .= '<a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a>';
					}

					// --- set post type labels ---
					$before = $after = '';
					$post_type_object = get_post_type_object( $post_type );
					$singular = $post_type_object->labels->singular_name;
					$plural = $post_type_object->labels->name;

					// --- before content links ---
					if ( in_array( 'before', $positions ) ) {
						if ( count( $related_shows ) > 1 ) {
							$label = sprintf( __( '%s for Shows', 'radio-station' ), $singular );
						} else {
							$label = sprintf( __( '%s for Show', 'radio-station' ), $singular );
						}
						$before = $label . ': ' . $show_links . '<br><br>';
						$before = apply_filters( 'radio_station_link_to_show_before', $before, $post, $related_shows );
					}

					// --- after content links ---
					if ( in_array( 'after', $positions ) ) {
						if ( count( $related_shows ) > 1 ) {
							$label = sprintf( __( 'More %s for Shows', 'radio-station' ), $plural );
						} else {
							$label = sprintf( __( 'More %s for Show', 'radio-station' ), $plural );
						}
						$after = '<br>' . $label . ': ' . $show_links;
						$after = apply_filters( 'radio_station_link_to_show_after', $after, $post, $related_shows );
					}
					$content = $before . $content . $after;
				}
			}
		}

	}

	// --- adjacent post links debug output ---
	if ( RADIO_STATION_DEBUG ) {
		$content .= '<span style="display:none;">Previous Post Link: ' . esc_html( get_previous_post_link() ) . '</span>' . PHP_EOL;
		$content .= '<span style="display:none;">Next Post Link: ' . esc_html( get_next_post_link() ) . '</span>' . PHP_EOL;
	}

	return $content;
}

// -------------------------
// Show Posts Adjacent Links
// -------------------------
// 2.3.0: added show post adjacent links filter
add_filter( 'next_post_link', 'radio_station_get_show_post_link', 11, 5 );
add_filter( 'previous_post_link', 'radio_station_get_show_post_link', 11, 5 );
function radio_station_get_show_post_link( $output, $format, $link, $adjacent_post, $adjacent ) {

	global $radio_station_data, $post;

	// --- filter next and previous Show links ---
	// 2.3.4: add filtering for adjacent show links
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG );
	if ( in_array( $post->post_type, $post_types ) ) {

		// 2.5.0: get timezone for time conversion
		$timezone = radio_station_get_timezone();

		if ( RADIO_STATION_OVERRIDE_SLUG == $post->post_type ) {
			// 2.3.3.6: get next/previous Show for override date/time
			// 2.3.3.9: modified to handle multiple override times
			// 2.3.3.9: added check that schedule key is set
			$scheds = get_post_meta( $post->ID, 'show_override_sched', true );
			if ( $scheds && is_array( $scheds ) ) {
				if ( array_key_exists( 'date', $scheds ) ) {
					// 2.5.6: fix variable singular to plural
					$scheds = array( $scheds );
				}
				$now = time();
				foreach ( $scheds as $sched ) {
					$override_start = $sched['date'] . ' ' . $sched['start_hour'] . ':' . $sched['start_min'] . ' ' . $sched['start_meridian'];
					// 2.5.0: fix to use to_time not get_time!
					$override_time = radio_station_to_time( $override_start, $timezone ) + 1;
					if ( !isset( $time ) ) {
						$time = $override_time;
					} elseif ( ( $time < $now ) && ( $override_time > $now ) ) {
						$time = $override_time;
					}
				}
				if ( 'next' == $adjacent ) {
					$show = radio_station_get_next_show( $time );
				} elseif ( 'previous' == $adjacent ) {
					$show = radio_station_get_previous_show( $time );
				}
			}
		} else {
			$shifts = get_post_meta( $post->ID, 'show_sched', true );
			if ( $shifts && is_array( $shifts ) ) {
				if ( count( $shifts ) < 1 ) {
					// 2.3.3.6: default to standard adjacent post link
					return $output;
				}
				if ( 1 == count( $shifts ) ) {
					// 2.5.0: fix to get first item (not via index key 0)
					$shift_keys = array_keys( $shifts );
					$first_key = $shift_keys[0];
					$shift = $shifts[$first_key];
					$shift_start = $shift['day'] . ' ' . $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
					// 2.3.3.9: fix to put addition outside bracket
					// 2.5.0: fix to use to_time not get_time!
					$time = radio_station_to_time( $shift_start, $timezone ) + 1;
					if ( 'next' == $adjacent ) {
						$show = radio_station_get_next_show( $time );
					} elseif ( 'previous' == $adjacent ) {
						$show = radio_station_get_previous_show( $time );
					}
				} else {
					// 2.3.3.6: added method for Show with multiple shifts
					$now = radio_station_get_now();
					$show_shifts = radio_station_get_current_schedule();
					if ( !$show_shifts ) {
						return $output;
					}

					// --- get upcoming shift for Show ---
					$next_shift = false;
					foreach ( $show_shifts as $day => $day_shifts ) {
						foreach ( $day_shifts as $day_shift ) {
							if ( !$next_shift && ( $day_shift['show']['id'] == $post->ID ) ) {
								if ( !isset( $last_shift ) ) {
									$last_shift = $day_shift;
								}
								$start = $day_shift['date'] . ' ' . $day_shift['start'];
								$start_time = radio_station_to_time( $start );
								$end = $day_shift['date'] . ' ' . $day_shift['end'];
								$end_time = radio_station_to_time( $end );
								if ( ( $start_time > $now ) || ( $now < $end_time ) ) {
									$next_shift = $day_shift;
								}
							}
						}
					}
					if ( !$next_shift ) {
						$next_shift = $last_shift;
					}
					// echo "Next Show Shift: " . print_r( $next_shift, true );

					// --- reverse order for finding previous show shift ---
					if ( 'previous' == $adjacent ) {
						foreach ( $show_shifts as $day => $day_shifts ) {
							$show_shifts[$day] = array_reverse( $day_shifts, true );
						}
						$show_shifts = array_reverse( $show_shifts, true );
					}

					// --- loop shifts to find adjacent shift's Show ---
					$found = false;
					foreach ( $show_shifts as $day => $day_shifts ) {
						foreach ( $day_shifts as $day_shift ) {
							if ( !isset( $first_shift ) && ( $day_shift['show']['id'] != $post->ID ) ) {
								$first_shift = $day_shift;
							}
							// echo "Shift: " . print_r( $day_shift, true ) . PHP_EOL;
							if ( !isset( $show ) ) {
								if ( $found && ( $day_shift['show']['id'] != $post->ID ) ) {
									$show = $day_shift['show'];
								} elseif ( !$found ) {
									if ( $next_shift == $day_shift ) {
										$found = true;
									}
								}
							}
						}
					}
					if ( !isset( $show ) && isset( $first_shift ) ) {
						$show = $first_shift['show'];
					}
				}
			}
		}

		// --- generate adjacent Show link ---
		if ( isset( $show ) ) {

			// 2.5.0: fix to maybe get show key data
			if ( isset( $show['show'] ) ) {
				$show = $show['show'];
			}

			if ( 'next' == $adjacent ) {
				$rel = 'next';
			} elseif ( 'previous' == $adjacent ) {
				$rel = 'prev';
			}
			$adjacent_post = get_post( $show['id'] );

			// 2.5.0: added check for valid post
			if ( $adjacent_post ) {
				// --- adjacent post title ---
				// 2.4.0.3: added fix for missing post title
				$post_title = $adjacent_post->post_title;
				// if ( empty( $adjacent_post->post_title ) ) {
				// 	$post_title = $title;
				// }
				$post_title = apply_filters( 'the_title', $post_title, $adjacent_post->ID );

				$date = mysql2date( get_option( 'date_format' ), $adjacent_post->post_date );
				$string = '<a href="' . esc_url( get_permalink( $adjacent_post ) ) . '" rel="' . esc_attr( $rel ) . '" title="' . esc_attr( $post_title ) . '">';
				$inlink = str_replace( '%title', $post_title, $link );
				$inlink = str_replace( '%date', $date, $inlink );
				$inlink = $string . $inlink . '</a>';
				$output = str_replace( '%link', $inlink, $format );
			}
		}

		return $output;
	}

	// --- filter to allow related post types ---
	$related_post_types = array( 'post' );
	$show_post_types = apply_filters( 'radio_station_show_related_post_types', $related_post_types );
	// 2.5.6: use filtered variable in array check
	if ( in_array( $post->post_type, $show_post_types ) ) {

		// --- filter to allow disabling ---
		$link_show_posts = apply_filters( 'radio_station_link_show_posts', true, $post );
		if ( !$link_show_posts ) {
			return $output;
		}

		// --- get related show ---
		$related_show = get_post_meta( $post->ID, 'post_showblog_id', true );
		if ( !$related_show ) {
			return $output;
		}
		if ( is_array( $related_show ) ) {
			$related_shows = $related_show;
		} else {
			$related_shows = array( $related_show );
		}
		// 2.3.3.6: remove possible saved zero value
		foreach ( $related_shows as $i => $related_show ) {
			if ( 0 == $related_show ) {
				unset( $related_shows[$i] );
			}
		}
		// 2.5.0: change to less than 1, instead of equals 0
		if ( count( $related_shows ) < 1 ) {
			return $output;
		}
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Shows A: ' . esc_html( print_r( $related_shows, true ) ) . '</span>';
		}

		// --- get more Shows related to this related Post ---
		// 2.3.3.6: allow for multiple related posts
		// 2.3.3.9: added 'i:' prefix to LIKE value matches
		// 2.5.9: use wpdb prepare with LIKE statements
		global $wpdb;
		$likes = array( 'i:' . $wpdb->esc_like(  $related_shows[0] ) );
		$query = "SELECT post_id,meta_value FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'post_showblog_id' AND meta_value LIKE '%%%s%%'";
		if ( count( $related_shows ) > 1 ) {
			// 2.5.0: fix to loop related_shows (plural)
			foreach ( $related_shows as $i => $show_id ) {
				if ( $i > 0 ) {
					$likes[] = 'i:' . $wpdb->esc_like(  $show_id );
					$query .= " OR meta_key = 'post_showblog_id' AND meta_value LIKE '%%%s%%'";
				}
			}
		}
		// 2.5.9: fix to use prepare with multiple LIKE strings
		// $results = $wpdb->get_results( $query, ARRAY_A );
		$results = $wpdb->get_results( $wpdb->prepare( $query, $likes ), ARRAY_A );
		
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Shows B: ' . esc_html( print_r( $results, true ) ) . '</span>';
		}
		if ( !$results || !is_array( $results ) || ( count( $results ) < 1 ) ) {
			return $output;
		}
		$related_posts = array();
		foreach ( $results as $result ) {
			$values = maybe_unserialize( $result['meta_value'] );
			if ( RADIO_STATION_DEBUG ) {
				echo '<span style="display:none;">Post ' . esc_html( $result['post_id'] ) . ' Related Show Values : ' . esc_html( print_r( $values, true ) ) . '</span>';
			}
			// --- double check Show ID is actually a match ---
			if ( ( $result['meta_value'] == $related_show ) || ( is_array( $values ) && array_intersect( $related_shows, $values ) ) ) {
				// --- recheck post is of the same post type ---
				$query = "SELECT post_type FROM " . $wpdb->prefix . "posts WHERE ID = %d";
				$related_post_type = $wpdb->get_var( $wpdb->prepare( $query, $result['post_id'] ) );
				if ( $related_post_type == $post->post_type ) {
					$related_posts[] = $result['post_id'];
				}
			}
		}
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Posts B: ' . esc_html( print_r( $related_posts, true ) ) . '</span>';
		}
		if ( 0 == count( $related_posts ) ) {
			return $output;
		}

		// --- get adjacent post query ---
		// 2.3.3.6: use post__in related post array instead of meta_query
		$args = array(
			'post_type'           => $post->post_type,
			'posts_per_page'      => 1,
			'orderby'             => 'post_modified',
			'post__in'            => $related_posts,
			'ignore_sticky_posts' => true,
		);

		// --- setup for previous or next post ---
		// 2.3.3.6: set date_query instead of meta_query
		$post_type_object = get_post_type_object( $post->post_type );
		if ( 'previous' == $adjacent ) {
			$args['order'] = 'DESC';
			$args['date_query'] = array( array( 'before' => $post->post_date ) );
			$rel = 'prev';
			$title = __( 'Previous Related Show', 'radio-station' ) . ' ' . $post_type_object->labels->singular_name;
		} elseif ( 'next' == $adjacent ) {
			$args['order'] = 'ASC';
			$args['date_query'] = array( array( 'after' => $post->post_date ) );
			$rel = 'next';
			$title = __( 'Next Related Show', 'radio-station' ) . ' ' . $post_type_object->labels->singular_name;
		}

		// --- get the adjacent post ---
		// 2.3.3.6: use date_query instead of looping posts
		$show_posts = get_posts( $args );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Posts Args: ' . esc_html( print_r( $args, true ) ) . '</span>';
		}
		if ( 0 == count( $show_posts ) ) {
			return $output;
		}
		$adjacent_post = $show_posts[0];
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Adjacent Post: ' . esc_html( print_r( $adjacent_post, true ) ) . '</span>';
		}

		// --- adjacent post title ---
		$post_title = $adjacent_post->post_title;
		if ( empty( $adjacent_post->post_title ) ) {
			$post_title = $title;
		}
		$post_title = apply_filters( 'the_title', $post_title, $adjacent_post->ID );

		// --- adjacent post link ---
		// (from function get_adjacent_post_link)
		$date = mysql2date( get_option( 'date_format' ), $adjacent_post->post_date );
		$string = '<a href="' . esc_url( get_permalink( $adjacent_post ) ) . '" rel="' . esc_attr( $rel ) . '" title="' . esc_attr( $title ) . '">';
		$inlink = str_replace( '%title', $post_title, $link );
		$inlink = str_replace( '%date', $date, $inlink );
		$inlink = $string . $inlink . '</a>';
		$output = str_replace( '%link', $inlink, $format );

	}

	return $output;
}


// =============
// Query Filters
// =============

// -----------------------------
// Playlist Archive Query Filter
// -----------------------------
// 2.3.0: added to replace old archive template meta query
add_filter( 'pre_get_posts', 'radio_station_show_playlist_query' );
function radio_station_show_playlist_query( $query ) {

	if ( RADIO_STATION_PLAYLIST_SLUG == $query->get( 'post_type' ) ) {

		// --- not needed if using legacy template ---
		$styledir = get_stylesheet_directory();
		if ( file_exists( $styledir . '/archive-playlist.php' )
			|| file_exists( $styledir . '/templates/archive-playlist.php' ) ) {
			return;
		}
		// 2.3.0: also check in parent theme directory
		$templatedir = get_template_directory();
		if ( $templatedir != $styledir ) {
			if ( file_exists( $templatedir . '/archive-playlist.php' )
				|| file_exists( $templatedir . '/templates/archive-playlist.php' ) ) {
				return;
			}
		}

		// --- check if show ID or slug is set --
		// TODO: maybe use get_query_var here ?
		if ( isset( $_GET['show_id'] ) ) {
			$show_id = absint( $_GET['show_id'] );
			if ( $show_id < 0 ) {
				unset( $show_id );
			}
		} elseif ( isset( $_GET['show'] ) ) {
			$show = sanitize_text_field( $_GET['show'] );
			global $wpdb;
			$show_query = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_type = '" . RADIO_STATION_SHOW_SLUG . "' AND post_name = %s";
			$show_id = $wpdb->get_var( $wpdb->prepare( $show_query, $show ) );
			if ( !$show_id ) {
				unset( $show_id );
			}
		}

		// --- maybe add the playlist meta query ---
		if ( isset( $show_id ) ) {
			$meta_query = array(
				'key'   => 'playlist_show_id',
				'value' => $show_id,
			);
			$query->set( $meta_query );
		}
	}
}


// ---------------------------------
// === Schedule Override Filters ===
// ---------------------------------
// 2.5.0: moved here from includes/post-types.php
// (to apply linked show overrides in template single-show-content.php)
// note: post object overrides (content, excerpt) are in radio_station_override_linked_show_data

// --------------------------------------
// Add Schedule Override Template Filters
// --------------------------------------
add_action( 'wp', 'radio_station_override_filters' );
function radio_station_override_filters() {

	if ( is_admin() || !is_singular() ) {
		return;
	}

	global $post;
	if ( is_object( $post ) && ( RADIO_STATION_OVERRIDE_SLUG == $post->post_type ) ) {
		add_filter( 'the_title', 'radio_station_override_show_title', 10, 2 );
		add_filter( 'radio_station_show_title', 'radio_station_override_show_title', 10, 2 );
		add_filter( 'radio_station_show_avatar', 'radio_station_override_show_avatar', 10, 2 );
		add_filter( 'radio_station_show_avatar_id', 'radio_station_override_show_avatar_id', 10, 2 );
		add_filter( 'radio_station_show_thumbnail', 'radio_station_override_show_thumbnail', 10, 2 );
		add_filter( 'get_post_metadata', 'radio_station_override_thumbnail_id', 11, 4 );
		// add_filter( 'radio_station_show_header', 'radio_station_override_show_header', 10, 2 );
		add_filter( 'radio_station_show_hosts', 'radio_station_override_show_hosts', 10, 2 );
		add_filter( 'radio_station_show_producers', 'radio_station_override_show_producers', 10, 2 );
		add_filter( 'radio_station_show_link', 'radio_station_override_show_link', 10, 2 );
		add_filter( 'radio_station_show_email', 'radio_station_override_show_email', 10, 2 );
		add_filter( 'radio_station_show_phone', 'radio_station_override_show_phone', 10, 2 );
		add_filter( 'radio_station_show_download', 'radio_station_override_show_download', 10, 2 );
		add_filter( 'radio_station_show_file', 'radio_station_override_show_file', 10, 2 );
		add_filter( 'radio_station_show_patreon', 'radio_station_override_show_patreon', 10, 2 );
		add_filter( 'radio_station_show_shifts', 'radio_station_override_show_shifts', 10, 2 );
		// add_filter( 'radio_station_show_rss', 'radio_station_override_show_rss', 10, 2 );
		// add_filter( 'radio_station_show_social_icons', 'radio_station_override_show_social_icons', 10, 2 );
	}
}

// -------------------
// Override Show Title
// -------------------
function radio_station_override_show_title( $show_title, $post_id ) {
	global $post;
	if ( !is_object( $post ) || ( $post->ID != $post_id ) ) {
		return $show_title;
	}
	$override = radio_station_get_show_override( $post_id, 'show_title' );
	if ( false !== $override ) {
		return $override;
	}
	return $show_title;
}

// --------------------
// Override Show Avatar
// --------------------
function radio_station_override_show_avatar( $show_avatar, $post_id ) {
	if ( radio_station_doing_template() ) {
		$override = radio_station_get_show_override( $post_id, 'show_avatar' );
		if ( false !== $override ) {
			return $override;
		}
	}
	return $show_avatar;
}

// -----------------------
// Override Show Avatar ID
// -----------------------
function radio_station_override_show_avatar_id( $avatar_id, $post_id ) {
	if ( radio_station_doing_template() ) {
		$override = radio_station_get_show_override( $post_id, 'show_avatar' );
		if ( false !== $override ) {
			return $override;
		}
	}
	return $avatar_id;
}

// -----------------------
// Override Show Thumbnail
// -----------------------
function radio_station_override_show_thumbnail( $show_thumbnail, $post_id ) {
	$override = radio_station_get_show_override( $post_id, 'show_image' );
	if ( false !== $override ) {
		return $override;
	}
	return $show_thumbnail;
}

// --------------------------
// Override Show Thumbnail ID
// --------------------------
function radio_station_override_thumbnail_id( $id, $object_id, $meta_key, $single ) {
	global $post;
	if ( ( '_thumbnail_id' != $meta_key ) || !is_object( $post ) || ( $post->ID != $object_id ) ) {
		return $id;
	}
	if ( RADIO_STATION_OVERRIDE_SLUG == $post->post_type ) {
		$override = radio_station_get_show_override( $object_id, 'show_image' );
		if ( false !== $override ) {
			return $override;
		}
	}
	return $id;
}

// --------------------
// Override Show Header
// --------------------
/* function radio_station_override_show_header( $header_id, $post_id ) {
	return $header_id;
} */

// -------------------
// Override Show Hosts
// -------------------
function radio_station_override_show_hosts( $hosts, $post_id ) {
	$override = radio_station_get_show_override( $post_id, 'show_user_list' );
	if ( false !== $override ) {
		return $override;
	}
	return $hosts;
}

// -----------------------
// Override Show Producers
// -----------------------
function radio_station_override_show_producers( $producers, $post_id ) {
	$override = radio_station_get_show_override( $post_id, 'show_producer_list' );
	if ( false !== $override ) {
		return $override;
	}
	return $producers;
}

// ------------------
// Override Show Link
// ------------------
function radio_station_override_show_link( $show_link, $post_id ) {
	$override = radio_station_get_show_override( $post_id, 'show_link' );
	if ( false !== $override ) {
		return $override;
	}
	return $show_link;
}

// -------------------
// Override Show Email
// -------------------
function radio_station_override_show_email( $show_email, $post_id ) {
	$override = radio_station_get_show_override( $post_id, 'show_email' );
	if ( false !== $override ) {
		return $override;
	}
	return $show_email;
}

// -------------------
// Override Show Phone
// -------------------
function radio_station_override_show_phone( $show_phone, $post_id ) {
	$override = radio_station_get_show_override( $post_id, 'show_phone' );
	if ( false !== $override ) {
		return $override;
	}
	return $show_phone;
}

// ------------------
// Override Show File
// ------------------
function radio_station_override_show_file( $show_file, $post_id ) {
	$override = radio_station_get_show_override( $post_id, 'show_file' );
	if ( false !== $override ) {
		return $override;
	}
	return $show_file;
}

// -------------------------
// Override Disable Download
// -------------------------
function radio_station_override_show_download( $show_download, $post_id ) {
	$override = radio_station_get_show_override( $post_id, 'show_download' );
	if ( false !== $override ) {
		return $override;
	}
	return $show_download;
}

// ---------------------
// Override Show Patreon
// ---------------------
function radio_station_override_show_patreon( $show_patreon, $post_id ) {
	$override = radio_station_get_show_override( $post_id, 'show_patreon' );
	if ( false !== $override ) {
		return $override;
	}
	return $show_patreon;
}

// --------------------
// Override Show Shifts
// --------------------
function radio_station_override_show_shifts( $show_shifts, $post_id ) {
	$linked_id = get_post_meta( $post_id, 'linked_show_id', true );
	if ( $linked_id ) {
		$show_shifts = radio_station_get_show_schedule( $linked_id );
	}
	return $show_shifts;
}
