<?php

/*
 * Radio Station Data Endpoints
 * Author: Tony Hayes
 * @Since: 2.3.0
 */

// === API Discovery ===
// - Add Data API Header Link
// - Add Data API Discovery Link
// - Add Data API to RSD List
// === Data Functions ===
// - Get Broadcast Data
// - Get Shows Data
// - Get Genres Data
// === REST Routes ===
// - Register Rest Routes
// - Add Route Links to Data
// - Current Broadcast Route
// - Show Schedule Route
// - Show List Route
// - Genre List Route
// - Language List Route
// === Feeds ===
// - Add Feeds
// - Add Feed Links to Data
// - Current Broadcast Feed
// - Show Schedule Feed
// - Show List Feed
// - Genre List Feed
// - Language List Feed
// - Not Found Feed Error
// - Format Data to XML
// - Convert Array to XML


// ---------------------
// === API Discovery ===
// ---------------------

// ------------------------
// Add Data API Header Link
// ------------------------
add_action( 'template_redirect', 'radio_station_api_link_header', 11, 0 );
function radio_station_api_link_header() {
	if ( headers_sent() ) {return;}
	$api_url = radio_station_get_api_url();	
	$header = 'Link: <' . esc_url_raw( $api_url ) . '>; rel="' . RADIO_STATION_API_DOCS_URL . '"';
	$header = apply_filters( 'radio_station_api_discovery_header', $header );
	if ( $header ) {header( $header, false );}
}

// ---------------------------
// Add Data API Discovery Link
// ---------------------------
add_action( 'wp_head', 'radio_station_api_discovery_link' );
function radio_station_api_discovery_link() {
	$api_url = radio_station_get_api_url();	
	$link = "<link rel='" . RADIO_STATION_API_DOCS_URL . "' href='" . esc_url( $api_url ) . "' />";
	$link = apply_filters( 'radio_station_api_discovery_link', $link );
	if ( $link ) {echo $link;}
}

// ------------------------
// Add Data API to RSD List
// ------------------------
add_action( 'xmlrpc_rsd_apis', 'radio_station_api_discovery_rsd' );
function radio_station_api_discovery_rsd() {
	$api_url = radio_station_get_api_url();
	$link = '<api name="RadioStation" blogID="1" preferred="false" apiLink="' . esc_url( $api_url ) .'" />';
	$link = apply_filters( 'radio_station_api_discovery_rsd', $link );
	if ( $link ) {echo $link;}
}


// ----------------------
// === Data Functions ===
// ----------------------

// ----------------
// Add Station Data
// ----------------
function radio_station_add_station_data( $data ) {

	// --- get station data ---
	$stream_url = radio_station_get_stream_url();
	$station_url = radio_station_get_station_url();
	$schedule_url = radio_station_get_schedule_url();
	$language = radio_station_get_language();
	
	$now = strtotime( current_time( 'mysql' ) );
	$date_time = date( 'Y-m-d H:i:s', $now );

	// --- set station data array ---
	$station_data = array(
		'stream_url'	=> $stream_url,
		'station_url'	=> $station_url,
		'schedule_url'	=> $schedule_url,
		'language'		=> $language,
		'timestamp'		=> $now,
		'date_time'		=> $date_time,
		'success'		=> true,
	);
	$station_data = apply_filters( 'radio_station_station_data', $station_data );
	$data = array_merge( $data, $station_data );
	return $data;
}

// ------------------
// Get Broadcast Data
// ------------------
function radio_station_get_broadcast_data() {

	// --- get broadcast info ---
	$current_show = radio_station_get_current_show();
	$next_show = radio_station_get_next_show();
	
	// TODO: maybe get now playing playlist ?
	// $playlist = radio_station_current_playlist();

	// --- return current and next show info ---
	$broadcast = array(
		'current_show'	=> $current_show,
		'next_show'		=> $next_show,
	);
	$broadcast = apply_filters( 'radio_station_broadcast_data', $broadcast );
	return $broadcast;
}

// --------------
// Get Shows Data
// --------------
function radio_station_get_shows_data( $show = false ) {

	$shows = array();
	if ( $show ) {
		if ( strstr( $show, ',' ) ) {
			$show_ids = explode( ',', $show );		
			foreach ( $show_ids as $show ) {
				$show = sanitize_title( $show );
				$show = radio_station_get_show( $show );
				$show = radio_station_get_show_data_meta( $show, true );
				$shows[] = $show;
			}
		} else {
			$show = sanitize_title( $show );
			$show = radio_station_get_show( $show );
			$show = radio_station_get_show_data_meta( $show, true );
			$shows = array( $show );
		}
	} else {
		$shows = radio_station_get_shows();
		if ( count( $shows ) > 0 ) {
			foreach ( $shows as $i => $show ) {
				$shows[$i] = radio_station_get_show_data_meta( $show );
			}
		}
	}
	$shows = apply_filters( 'radio_station_shows_data', $shows );
	return $shows;
}

// ---------------
// Get Genres Data
// ---------------
function radio_station_get_genres_data( $genre = false ) {

	// -- get genre or genres ---
	$genres = array();
	if ( $genre ) {
		if ( strstr( $genre, ',' ) ) {
			$genre_ids = explode( ',', $genre );
			foreach ( $genre_ids as $genre ) {
				$genre = sanitize_title( $genre );
				$genre = radio_station_get_genre( $genre );
				$genres[] = $genre;
			}		
		} else {
			$genre = sanitize_title( $genre );
			$genres = radio_station_get_genre( $genre );
		}
	} else {
		$genres = radio_station_get_genres();
	}

	// --- loop genres to get shows ---
	if ( count( $genres ) > 0 ) {
		foreach ( $genres as $name => $genre ) {
			$shows = radio_station_get_genre_shows( $genre['slug'] );
			$genres[$name]['shows'] = array();
			$genres[$name]['show_count'] = 0;
			if ( is_object( $shows ) && property_exists( $shows, 'posts' ) 
				&& is_array( $shows->posts ) && ( count( $shows->posts ) > 0 ) ) {
				$genres[$name]['show_count'] = count( $shows->posts );
				foreach ( $shows->posts as $show ) {
					$genres[$name]['shows'][] = radio_station_get_show_data_meta( $show );
				}
			}
		}
	}
	$genres = apply_filters( 'radio_station_genres_data', $genres );
	return $genres;
}

// ------------------
// Get Languages Data
// ------------------
function radio_station_get_languages_data( $language = false ) {

	// -- get language or languages ---
	$languages = array();
	if ( $language ) {
		if ( strstr( $language, ',' ) ) {
			$language_ids = explode( ',', $language );
			foreach ( $language_ids as $language ) {
				$language = sanitize_title( $language );
				$language = radio_station_get_language( $language );
				$languages[] = $language;
			}		
		} else {
			$language = sanitize_title( $language );
			$languages = radio_station_get_language( $language );
		}
	} 

	// --- loop genres to get shows ---
	if ( count( $languages ) > 0 ) {
		foreach ( $languages as $code => $language ) {
			$shows = radio_station_get_language_shows( $language['slug'] );
			$languages[$code]['shows'] = array();
			$languages[$code]['show_count'] = 0;
			if ( is_object( $shows ) && property_exists( $shows, 'posts' ) 
			  && is_array( $shows->posts ) && ( count( $shows->posts ) > 0 ) ) {
				$languages[$code]['show_count'] = count( $shows->posts );
				foreach ( $shows->posts as $show ) {
					$languages[$code]['shows'][] = radio_station_get_show_data_meta( $show );
				}
			}
		}
	}

	// --- maybe get main language shows ---
	if ( !$language ) {
		$code = radio_station_get_language();
		$shows = radio_station_get_language_shows();
		if ( is_object( $shows ) && property_exists( $shows, 'posts' ) 
		  && is_array( $shows->posts ) && ( count( $shows->posts ) > 0 ) ) {
			$show_count = count( $shows->posts );
			if ( isset( $languages[$code]['show_count'] ) ) {
				$languages[$code]['show_count'] = $languages[$code]['show_count'] + $show_count;
			}
			$main_shows = array();
			foreach ( $shows->posts as $show ) {
				$main_shows[] = radio_station_get_show_data_meta( $show );
			}
			$languages[$code]['shows'] = array_merge( $main_shows, $languages[$code]['shows'] );
		}
	}

	$languages = apply_filters( 'radio_station_languages_data', $languages );
	return $languages;
}


// -------------------
// === REST Routes ===
// -------------------

// --------------------
// Register Rest Routes
// --------------------
add_action( 'rest_api_init', 'radio_station_register_rest_routes' );
function radio_station_register_rest_routes() {

	// --- check rest routes are enabled ---
	$enabled = radio_station_get_setting( 'enable_data_routes' );
	if ( $enabled != 'yes' ) {return;}

	// --- filter route slugs ---
	// (can disable individual routes by returning false from filters)
	$base = apply_filters( 'radio_station_route_slug_base', 'radio' );
	$station = apply_filters( 'radio_station_route_slug_station', 'station' );
	$broadcast = apply_filters( 'radio_station_route_slug_broadcast', 'broadcast' );
	$schedule = apply_filters( 'radio_station_route_slug_schedule', 'schedule' );
	$shows = apply_filters( 'radio_station_route_slug_shows', 'shows' );
	$show = apply_filters( 'radio_station_route_slug_show', 'show' );
	$genres = apply_filters( 'radio_station_route_slug_genres', 'genres' );
	$genre = apply_filters( 'radio_station_route_slug_genre', 'genre' );
	$languages = apply_filters( 'radio_station_route_slug_languages', 'languages' );
	$language = apply_filters( 'radio_station_route_slug_language', 'language' );

	// TODO: maybe add endpoint parameters (eg. weekday)
	// ref: https://stackoverflow.com/q/53126137/5240159

	// --- Station Route ---
	// default URL: /wp-json/radio/station/
	if ( $station ) {
		register_rest_route( $base, '/' . $station . '/', array(
		 	'methods' => 'GET',
		 	'callback' => 'radio_station_route_station',
		) );
	}

	// --- Show Broadcast Route ---
	// default URL: /wp-json/radio/broadcast/
	if ( $broadcast ) {
		register_rest_route( $base, '/' . $broadcast . '/', array(
			'methods' => 'GET',
			'callback' => 'radio_station_route_broadcast',
		) );
	}

	// --- Master Schedule Route ---
	// default URL: /wp-json/radio/schedule/
	// (?P<weekday>\d+)
	if ( $schedule ) {
		register_rest_route( $base, '/' . $schedule . '/', array(
			'methods' => 'GET',
			'callback' => 'radio_station_route_schedule',
			// 'args' => array(
			//	'weekday' => array(
			//		'validate_callback' => function($param, $request, $key) {
			//			return is_numeric( $param );
			//		}
			//	),
			// ),
		) );
	}

	// --- Show Genre List Route ---
	// default URL: /wp-json/radio/genres/
	if ( $genres ) {
		register_rest_route( $base, '/' . $genres . '/', array(
			'methods' => 'GET',
			'callback' => 'radio_station_route_genres',
		) );
	}
	if ( $genre ) {
		register_rest_route( $base, '/' . $genre . '/', array(
			'methods' => 'GET',
			'callback' => 'radio_station_route_genres',
		) );
	}

	// --- Show List Route ---
	// default URL: /wp-json/radio/shows/
	if ( $shows ) {
		register_rest_route( $base, '/' . $shows .'/', array(
			'methods' => 'GET',
			'callback' => 'radio_station_route_shows',
		) );
	}
	if ( $show ) {
		register_rest_route( $base, '/' . $show .'/', array(
			'methods' => 'GET',
			'callback' => 'radio_station_route_shows',
		) );
	}

	// --- Language List Route ---
	// default URL: /wp-json/radio/languages/
	if ( $shows ) {
		register_rest_route( $base, '/' . $languages .'/', array(
			'methods' => 'GET',
			'callback' => 'radio_station_route_languages',
		) );
	}
	if ( $show ) {
		register_rest_route( $base, '/' . $language .'/', array(
			'methods' => 'GET',
			'callback' => 'radio_station_route_languages',
		) );
	}

}

// -----------------------
// Add Route Links to Data
// -----------------------
function radio_station_add_route_urls( $data ) {

	// --- get and add route links ---
	$station = radio_station_get_route_url( 'station' );
	if ( $station ) {$data['routes']['station'] = $station;}
	$broadcast = radio_station_get_route_url( 'broadcast' );
	if ( $broadcast ) {$data['routes']['broadcast'] = $broadcast;}
	$schedule = radio_station_get_route_url( 'schedule' );
	if ( $schedule ) {$data['routes']['schedule'] = $schedule;}
	$shows = radio_station_get_route_url( 'shows' );
	if ( $shows ) {$data['routes']['shows'] = $shows;}
	$genres = radio_station_get_route_url( 'genres' );
	if ( $genres ) {$data['routes']['genres'] = $genres;}
	$languages = radio_station_get_route_url( 'languages' );
	if ( $languages ) {$data['routes']['languages'] = $languages;}

	// --- maybe get and add pro route links ---
	if ( function_exists( 'radio_station_pro_add_route_urls' ) ) {
		$data = radio_station_pro_add_route_urls( $data );
	}
	return $data;
}

// -------------
// Station Route
// -------------
// (combined data from all routes) 
function radio_station_route_station( $request ) {

	$data = array();
	$broadcast = radio_station_get_broadcast_data();
	$data['broadcast'] = $broadcast;
	$schedule = radio_station_get_current_schedule();
	$data['schedule'] = $schedule;
	$shows_data = radio_station_get_shows_data();
	$data['shows'] = $shows_data;
	$data['show_count'] = count( $shows_data );
	$genres_data = radio_station_get_genres_data();
	$data['genres'] = $genres_data;
	$data['genre_count'] = count( $genres_data );
	$languages_data = radio_station_get_languages_data();
	$data['languages'] = $languages_data;
	$data['language_count'] = count( $languages_data );
	$data = radio_station_add_station_data( $data );
	$data = radio_station_add_route_urls( $data );
	return $data;
}

// -----------------------
// Current Broadcast Route
// -----------------------
function radio_station_route_broadcast( $request ) {
	$broadcast = radio_station_get_broadcast_data();
	$broadcast = array( 'broadcast' => $broadcast );
	$broadcast = radio_station_add_station_data( $broadcast );
	$broadcast = radio_station_add_route_urls( $broadcast );
	return $broadcast;
}

// -------------------
// Show Schedule Route
// -------------------
function radio_station_route_schedule( $request ) {

	// --- get current schedule ---
	$schedule = radio_station_get_current_schedule();

	// TODO: validate weekday
	// if ( isset($request['weekday']) ) {
	//	if ( ( $request['weekday'] < 0 ) || ( $request( $weekday ) > 6 ) ) {
	//		return new WP_Error( 'invalid_weekday', 'Invalid Weekday (valid: 0-6)', array( 'status' => 404 ) );
	//	}
	// }

	$schedule = array( 'schedule' => $schedule );
	$schedule = radio_station_add_station_data( $schedule );
	$schedule = radio_station_add_route_urls( $schedule );
	return $schedule;
}

// ---------------
// Show List Route
// ---------------
function radio_station_route_shows( $request ) {

	// TODO: validate show query parameter ?
	$singular = $multiple = false;
	if ( isset( $request['show'] ) ) {
		if ( !strstr( $show, ',' ) ) {$singular = true;} else {$multiple = true;}
		$show = $request['show'];
	} else {$show = false;}

	// --- get show list data ---
	$shows_data = radio_station_get_shows_data( $show );
	
	// --- maybe return route error ---
	if ( count( $shows_data ) === 0 ) {
		if ( $singular ) {$code = 'show_not_found'; $error = 'Requested Show was not found.';}
		elseif ( $multiple ) {$code = 'shows_not_found'; $error = 'No Requested Shows were found.';}
		else {$code = 'no_shows'; $message = 'No Shows were found.';}
		return new WP_Error( $code, $message, array( 'status' => 404 ) );
	}

	if ( $singular ) {$show_list = array( 'show' => $shows_data[0] );}
	else {$show_list = array( 'shows' => $shows_data, 'show_count' => count( $shows_data ) );}
	
	// --- return show list ---
	$show_list = radio_station_add_station_data ( $show_list );
	$show_list = radio_station_add_route_urls( $show_list );
	return $show_list;
}

// ----------------
// Genre List Route
// ----------------
function radio_station_route_genres( $request ) {

	// TODO: validate genre query parameter ?
	$singular = $multiple = false;
	if ( isset( $request['genre'] ) ) {
		if ( !strstr( $genre, ',' ) ) {$singular = true;} else {$multiple = true;}
		$genre = $request['genre'];
	} else {$genre = false;}

	// --- get genre list data ---
	$genres_data = radio_station_get_genres_data( $genre );

	// --- maybe return route error ---
	if ( count( $genres_data ) === 0 ) {
		if ( $singular ) {$code = 'genre_not_found'; $error = 'Requested Genre was not found.';}
		elseif ( $multiple ) {$code = 'genres_not_found'; $error = 'No Requested Genres were found.';}
		else {$code = 'no_genres'; $message = 'No Genres were found.';}
		return new WP_Error( $code, $message, array( 'status' => 404 ) );
	}

	if ( $singular ) {$genre_list = array( 'genre' => $genres_data[0] );}
	else {$genre_list = array( 'genres' => $genres_data, 'genre_count' => count( $genres_data ) );}
	
	// --- return genre list ---
	$genre_list = radio_station_add_station_data( $genre_list );
	$genre_list = radio_station_add_route_urls( $genre_list );
	return $genre_list;
}

// -------------------
// Language List Route
// -------------------
function radio_station_route_languages( $request ) {

	// TODO: validate language query parameter ?
	$singular = $multiple = false;
	if ( isset( $request['language'] ) ) {
		if ( !strstr( $language, ',' ) ) {$singular = true;} else {$multiple = true;}
		$language = $request['language'];
	} else {$language = false;}

	// --- get language list data ---
	$languages_data = radio_station_get_languages_data( $language );

	// --- maybe return route error ---
	if ( count( $languages_data ) === 0 ) {
		if ( $singular ) {$code = 'language_not_found'; $error = 'Requested Language was not found.';}
		elseif ( $multiple ) {$code = 'languages_not_found'; $error = 'No Requested Languages were found.';}
		else {$code = 'no_languages'; $message = 'No Languages were found.';}
		return new WP_Error( $code, $message, array( 'status' => 404 ) );
	}

	if ( $singular ) {$languages_list = array( 'language' => $languages_data[0] );}
	else {$languages_list = array( 'languages' => $languages_data, 'language_count' => count( $languages_data ) );}
	
	// --- return language list ---
	$languages_list = radio_station_add_station_data( $languages_list );
	$languages_list = radio_station_add_route_urls( $languages_list );
	return $languages_list;
}

// ---------------
// Check for Genre
// ---------------
// function radio_station_check_genre( $genre ) {
//	$term = get_term_by( 'slug', $genre, 'genres' );
//	if ( $term ) {return true;}
//	$term = get_term_by( 'name', $genre, 'genres' );
//	if ( $term ) {return true;}
//	return false;
// }


// =============
// --- Feeds ---
// =============

// --------
// Add Feed
// --------
function radio_station_add_feed( $feedname, $function ) {

	// note: removed as this is overwriting normal page slugs...
	// so /feed/schedule/ overwrites /schedule/ - which is no good!
    // global $wp_rewrite;
    // if ( ! in_array( $feedname, $wp_rewrite->feeds ) ) {
    //     $wp_rewrite->feeds[] = $feedname;
    // }
 
    $hook = 'do_feed_' . $feedname;
    remove_action( $hook, $hook );
    add_action( $hook, $function, 10, 2 );
    return $hook;
}

// ---------
// Add Feeds
// ---------
add_action( 'init', 'radio_station_add_feeds', 11 );
function radio_station_add_feeds() {

	// --- check feeds are enabled ---
	$enabled = radio_station_get_setting( 'enable_data_feeds' );
	if ( $enabled != 'yes' ) {return;}
	
	// --- filter feed slugs ---
	$radio = apply_filters( 'radio_station_feed_slug_base', 'radio' );
	$station = apply_filters( 'radio_station_feed_slug_station', 'station' );
	$broadcast = apply_filters( 'radio_station_feed_slug_broadcast', 'broadcast' );
	$schedule = apply_filters( 'radio_station_feed_slug_schedule', 'schedule' );
	$show = apply_filters( 'radio_station_feed_slug_show', 'show' );
	$shows = apply_filters( 'radio_station_feed_slug_shows', 'shows' );
	$genre = apply_filters( 'radio_station_feed_slug_genre', 'genre' );
	$genres = apply_filters( 'radio_station_feed_slug_genres', 'genres' );
	$language = apply_filters( 'radio_station_feed_slug_language', 'language' );
	$languages = apply_filters( 'radio_station_feed_slug_languages', 'languages' );

	// --- add feeds ---
	if ( $radio ) {radio_station_add_feed( $radio, 'radio_station_feed_radio' );}
	if ( $station ) {radio_station_add_feed( $station, 'radio_station_feed_station' );}
	if ( $broadcast ) {radio_station_add_feed( $broadcast, 'radio_station_feed_broadcast' );}
	if ( $schedule ) {radio_station_add_feed( $schedule, 'radio_station_feed_schedule' );}
	if ( $shows ) {radio_station_add_feed( $shows, 'radio_station_feed_shows' );}
	if ( $show ) {radio_station_add_feed( $show, 'radio_station_feed_shows' );}
	if ( $genres ) {radio_station_add_feed( $genres, 'radio_station_feed_genres' );}
	if ( $genre ) {radio_station_add_feed( $genre, 'radio_station_feed_genres' );}
	if ( $languages ) {radio_station_add_feed( $languages, 'radio_station_feed_languages' );}
	if ( $language ) {radio_station_add_feed( $language, 'radio_station_feed_language' );}
	
	// --- add single feed rewrite rule ---
	// (without risking overriding standard permalink slugs)
	// https://wordpress.stackexchange.com/questions/351576/add-feed-rewrite-overwriting-standard-permalinks/351603#351603
	$feeds = array( $station, $broadcast, $schedule, $shows, $show, $genres, $genre );
	if ( function_exists( 'radio_station_pro_add_feeds' ) ) {
		$profeeds = radio_station_pro_add_feeds();
		$feeds = array_merge( $feeds, $profeeds );
	}
	foreach ( $feeds as $i => $feed ) {if (!$feed) {unset( $feeds[$i] );} }
	$feedstring = implode( '|', $feeds );
	$feedrule = '^feed/('. $feedstring . ')/?$';
	add_rewrite_rule( $feedrule, 'index.php?feed=$matches[1]', 'top' );

	// --- check if feeds are registered ---
	$rewrite_rules = get_option( 'rewrite_rules' );
	if ( !array_key_exists( $feedrule, $rewrite_rules ) ) {
		flush_rewrite_rules( false );
	}
}

// ----------------------
// Add Feed Links to Data
// ----------------------
function radio_station_add_feed_urls( $data ) {

	// --- get and add feed links ---
	$station = radio_station_get_feed_url( 'station' );
	if ( $station ) {$data['feeds']['station'] = $station;}
	$broadcast = radio_station_get_feed_url( 'broadcast' );
	if ( $broadcast ) {$data['feeds']['broadcast'] = $broadcast;}
	$schedule = radio_station_get_feed_url( 'schedule' );
	if ( $schedule ) {$data['feeds']['schedule'] = $schedule;}
	$shows = radio_station_get_feed_url( 'shows' );
	if ( $shows ) {$data['feeds']['shows'] = $shows;}
	$genres = radio_station_get_feed_url( 'genres' );
	if ( $genres ) {$data['feeds']['genres'] = $genres;}
	$languages = radio_station_get_feed_url( 'languages' );
	if ( $languages ) {$data['feeds']['languages'] = $languages;}

	// --- maybe get and add pro feed links ---
	if ( function_exists( 'radio_station_pro_add_feed_urls' ) ) {
		$data = radio_station_pro_add_feed_urls( $data );
	}
	return $data;
}

// -------------------------
// Radio Data Discovery Feed
// -------------------------
function radio_station_feed_radio() {

	if ( isset( $_GET['debug'] ) && ( $_GET['debug'] == '1' ) ) {
		header( 'Content-Type: text/plain' );
	}

	$radio = array( 'success' => true );
	$radio = radio_station_add_feed_urls( $radio );

	if ( isset( $_GET['format'] ) && ( $_GET['format'] == 'json' ) ) {
		header( 'Content-Type: application/rss+xml' );
		echo radio_station_format_xml( $radio );
	} else {
		header( 'Content-Type: application/json' );
	    echo json_encode( $radio );
	}
}

// ----------------------
// Current Broadcast Feed
// ----------------------
function radio_station_feed_broadcast( $comment_feed, $feed_name ) {

	if ( isset( $_GET['debug'] ) && ( $_GET['debug'] == '1' ) ) {
		header( 'Content-Type: text/plain' );
	}
    
	$broadcast = radio_station_get_broadcast_data();
	$broadcast = array( 'broadcast', $broadcast );
	$broadcast = radio_station_add_station_data( $broadcast );
	$broadcast = radio_station_add_feed_urls( $broadcast );
	
	if ( isset( $_GET['format'] ) && ( $_GET['format'] == 'xml' ) ) {
		header( 'Content-Type: application/rss+xml' );
		echo radio_station_format_xml( $broadcast );
	} else {
		header( 'Content-Type: application/json' );
	    echo json_encode( $broadcast );
	}
}

// ------------------
// Show Schedule Feed
// ------------------
function radio_station_feed_schedule( $comment_feed, $feed_name ) {

	if ( isset( $_GET['debug'] ) && ( $_GET['debug'] == '1' ) ) {
		header( 'Content-Type: text/plain' );
	}

	// --- get current schedule ---
	$schedule = radio_station_get_current_schedule();
	
	// TODO: check for a specified weekday ?

	$schedule = array( 'schedule' => $schedule );
	$schedule = radio_station_add_station_data( $schedule );
	$schedule = radio_station_add_feed_urls( $schedule );
	
	if ( isset( $_GET['format'] ) && ( $_GET['format'] == 'xml' ) ) {
		header( 'Content-Type: application/rss+xml' );
		echo radio_station_format_xml( $schedule );
	} else {
		header( 'Content-Type: application/json' );
	    echo json_encode( $schedule );
	}
}

// --------------
// Show List Feed
// --------------
function radio_station_feed_shows( $comment_feed, $feed_name ) {

	if ( isset( $_GET['debug'] ) && ( $_GET['debug'] == '1' ) ) {
		header( 'Content-Type: text/plain' );
	}

	// --- check for single show query ---
	$singular = $multiple = false;
	if ( isset( $_GET['show'] ) ) {
		if ( !strstr( $_GET['show'], ',' ) ) {$singular = true;} else {$singular = false;}
		$show = $_GET['show'];
	} else {$show = false;}

	// --- get show list data ---
	$shows_data = radio_station_get_shows_data();
	
	// --- maybe output feed error message ---
	if ( count( $shows_data ) === 0 ) {
		if ( $singular ) {$details = __( 'Requested Show was not found.', 'radio-station' );}
		elseif ( $multiple ) {$details .= __( 'No Requested Genres were found.', 'radio-station' );}
		else { $details = __( 'No Shows were found.', 'radio-station' );}
		return radio_station_feed_not_found( $details );
	}

	if ( $singular ) {$show_list = array( 'show' => $shows_data[0] );}
	else {$show_list = array( 'shows' => $shows_data, 'show_count' => count( $shows_data ) );}
	if ( isset( $_GET['debug'] ) && ( $_GET['debug'] == '1' ) ) {print_r( $show_list );}

	// --- output encoded show list ---
	$show_list = radio_station_add_station_data( $show_list );
	$show_list = radio_station_add_feed_urls( $show_list );
	
	if ( isset( $_GET['format'] ) && ( $_GET['format'] == 'xml' ) ) {
		header( 'Content-Type: application/rss+xml' );
		echo radio_station_format_xml( $show_list );
	} else {
		header( 'Content-Type: application/json' );
	    echo json_encode( $show_list );
	}
}

// ---------------
// Genre List Feed
// ---------------
function radio_station_feed_genres( $comment_feed, $feed_name ) {

	if ( isset( $_GET['debug'] ) && ( $_GET['debug'] == '1' ) ) {
		header( 'Content-Type: text/plain' );
	}

	// --- check for single genre query ---
	$singular = $multiple = false;
	if ( isset( $_GET['genre'] ) ) {
		if ( !strstr( $_GET['genre'], ',' ) ) {$singular = true;} else {$multiple = true;}
		$genre = $_GET['genre'];
	} else {$genre = false;}

	// --- get genre list data ---
	$genres_data = radio_station_get_genres_data();

	// --- maybe output feed error message ---
	if ( count( $genres_data ) === 0 ) {
		if ( $singular ) {$details = __( 'Requested Genre was not found.', 'radio-station' );}
		elseif ( $multiple ) {$details = __( 'No Requested Genres were found.', 'radio-station' );}
		else {$details = __( 'No Genres were found.', 'radio-station' );}
		return radio_station_feed_not_found( $details );
	}
	
	// --- output encoded genre list ---
	if ( $singular ) {$genre_list = array( 'genre' => $genres_data[0] );}
	else {$genre_list = array( 'genres' => $genres_data, 'genre_count' => count( $genres_data ) );}

	if ( isset( $_GET['debug'] ) && ( $_GET['debug'] == '1' ) ) {print_r( $genre_list );}
	
	$genre_list = radio_station_add_station_data( $genre_list );
	$genre_list = radio_station_add_feed_urls( $genre_list );
	if ( isset( $_GET['format'] ) && ( $_GET['format'] == 'xml' ) ) {
		header( 'Content-Type: application/rss+xml' );
		echo radio_station_format_xml( $genre_list );
	} else {
		header( 'Content-Type: application/json' );
	    echo json_encode( $genre_list );
	}
}

// -------------------
// Languages List Feed
// -------------------
function radio_station_feed_languages( $comment_feed, $feed_name ) {

	if ( isset( $_GET['debug'] ) && ( $_GET['debug'] == '1' ) ) {
		header( 'Content-Type: text/plain' );
	}

	// --- check for single language query ---
	$singular = $multiple = false;
	if ( isset( $_GET['language'] ) ) {
		if ( !strstr( $_GET['language'], ',' ) ) {$singular = true;} else {$multiple = true;}
		$genre = $_GET['language'];
	} else {$genre = false;}

	// --- get genre list data ---
	$languages_data = radio_station_get_languages_data();

	// --- maybe output feed error message ---
	if ( count( $languages_data ) === 0 ) {
		if ( $singular ) {$details = __( 'Requested Language was not found.', 'radio-station' );}
		elseif ( $multiple ) {$details = __( 'No Requested Languages were found.', 'radio-station' );}
		else {$details = __( 'No Languages were found.', 'radio-station' );}
		return radio_station_feed_not_found( $details );
	}
	
	// --- output encoded language list ---
	if ( $singular ) {$language_list = array( 'language' => $languages_data[0] );}
	else {$language_list = array( 'languages' => $languages_data, 'language_count' => count( $languages_data ) );}

	if ( isset( $_GET['debug'] ) && ( $_GET['debug'] == '1' ) ) {print_r( $language_list );}
	
	$language_list = radio_station_add_station_data( $language_list );
	$language_list = radio_station_add_feed_urls( $language_list );
	if ( isset( $_GET['format'] ) && ( $_GET['format'] == 'xml' ) ) {
		header( 'Content-Type: application/rss+xml' );
		echo radio_station_format_xml( $language_list );
	} else {
		header( 'Content-Type: application/json' );
	    echo json_encode( $language_list );
	}
}

// --------------------
// Not Found Feed Error
// --------------------
function radio_station_feed_not_found( $details ) {

	if ( isset( $_GET['debug'] ) && ( $_GET['debug'] == '1' ) ) {
		header( 'Content-Type: text/plain' );
	}

	$error = array(
		'success' => false,
		'errors' => array(
			'status'	=> 404,
			'code'		=> 404,
			'title'		=> __( 'Error 404 Not Found', 'radio-station' ),
			'message'	=> __( 'The requested data could not be found.', 'radio-station' ),
			'detail'	=> $details,
		),
	);

	if ( isset( $_GET['format'] ) && ( $_GET['format'] == 'xml' ) ) {
		header( 'Content-Type: application/rss+xml' );
		echo radio_station_format_xml( $error );
	} else {
		header( 'Content-Type: application/json' );
	    echo json_encode( $error );
	}
}

// ------------------
// Format Date to XML
// ------------------
function radio_station_format_xml( $data ) {

	$xml = new SimpleXMLElement('<station/>');
	radio_station_array_to_xml($xml, $data);
	$export = $xml->asXML();
	$contenttype = 'text/xml';
	$dom = new DOMDocument();
	// $dom->formatOutput = true;
	$dom->loadXML( $export );
	$export = $dom->saveXML();
}

// --------------------
// Convert Array to XML
// --------------------
function radio_station_array_to_xml( SimpleXMLElement $object, array $data ) {

	foreach ($data as $key => $value) {
		if (is_array($value)) {
			$newobject = $object->addChild($key);
			radio_station_array_to_xml($newobject, $value);
		} else {
			$object->addChild($key, htmlspecialchars($value));
		}
	}
}

