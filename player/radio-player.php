<?php

// ====================
// === RADIO PLAYER ===
// ====================
// === by Tony Hayes ==
// ====================

if ( !defined( 'ABSPATH' ) ) exit;

// - Set Player Debug Mode
// === Radio Player ===
// - Player Output
// - Store Player Instance Args
// - Player Shortcode
// - AJAX: Player Display
// - Add Inline Styles
// - Print Footer Styles
// - Sanitize Shortcode Values
// - Sanitize Values
// - Media Elements Interface
// === Player Scripts ===
// - Enqueue Player Javasscripts
// - Enqueue Player Script
// - Lazy Load Audio Script Fallbacks
// - Enqueue Amplitude Javascript
// - Enqueue JPlayer Javascript
// - Enqueue Howler Javascript
// * Enqueue Media Element Javascript
// - Dynamic Load Script via AJAX
// - Get Player Settings
// - User State Iframe
// - AJAX: Update User State
// - Load Amplitude Function
// - Load JPlayer Function
// - Load Howler Function
// * Load Media Element Function
// - Get Default Player Script
// - Enqueue Player Styles
// - Player Control Styles
// - AJAX: Control Styles
// === Standalone Compatibility ===
// x Output Script Tag
// x Output Style Tag
// - Validate Boolean


// -------------------------
// Audio/Video Support Notes
// -------------------------
//
// Script Library Support
// ----------------------
// [Amplitude] HTML5 Support - mp3, aac ...?
// ref: https://en.wikipedia.org/wiki/HTML5_audio#Supporting_browsers
// [JPlayer] Audio: mp3, m4a - Video: m4v
// +Audio: webma, oga, wav, fla, rtmpa +Video: webmv, ogv, flv, rtmpv
// [Howler] mp3, opus, ogg, wav, aac, m4a, mp4, webm
// +mpeg, oga, caf, weba, webm, dolby, flac
// [Media Elements] Audio: mp3, wma, wav +Video: mp4, ogg, webm, wmv

// Streaming Server Support
// ------------------------
// [Icecast] Ogg (Vorbis, Theora), Opus, FLAC and WebM (VP8/VP9),
// "nonfree codecs/formats like MP4 (H. 264, MPEG4), M4A, NSV, AAC and MP3 might work,
// but we do not officially support those."
// [Shoutcast]
// ref: https://help.shoutcast.com/hc/en-us/articles/115004705393-Recommended-bitrate-and-format
// MP3 320, MP3 256, MP3 192, MP3 128, MP3 64, MP3 32
// AAC4 128, AAC4 96, AAC4 64, AAC4 32
// [Azuracast]
// OGG, MP3, AAC, OPUS .. ?
// [LibreTime] (supports Shoutcast and Icecast)

// Note: MP4 vs AAC
// ----------------
// An MPEG-4 file contains a header that includes metadata followed by "tracks" which can include
// video as well as audio data, for example, H.264 encoded Video and AAC encoded Audio.
// ADTS in contrast is a streaming format consisting of a series of frames,
// each frame having a header followed by the AAC data.


// ----------------------
// Player Constants Notes
// ----------------------
//
// --- player resource URL ---
// RADIO_PLAYER_URL - define player URL path for standalone compatible version
// (note: should have a trailing slash!) eg. to use as a WordPress mu-plugins dropin:
// define( 'RADIO_PLAYER_URL', 'https://example.com/wp-content/mu-plugins/player/');
// (then include /mu-plugins/player/radio-player.php from a file in /mu-plugins/)

// --- player script and skin ---
// RADIO_PLAYER_SCRIPT - default player script (amplitude, jplayer, howler)
// RADIO_PLAYER_FORCE_SCRIPT - force override any use of other player script
// RADIO_PLAYER_SKIN - default player skin (must match script used)
// RADIO_PLAYER_FORCE_SKIN - force override any use of other player skin

// --- player display values ---
// RADIO_PLAYER_TITLE - title of station/player
// RADIO_PLAYER_IMAGE - URL of station/player image
// RADIO_PLAYER_VOLUME - initial player volume (0 to 100)

// --- user state saving ---
// RADIO_PLAYER_AJAX_URL - destination for user saving (default: WordPress admin-ajax.php)
// RADIO_PLAYER_SAVE_INTERVAL - seconds between user state saving (default: 60)


// -----------------------
// Original JPlayer Markup
// -----------------------
/* <div id="jquery_jplayer_1" class="jp-jplayer"></div>
<div id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
  <div class="jp-type-single">
	<div class="jp-gui jp-interface">
	  <div class="jp-volume-controls">
		<button class="jp-mute" role="button" tabindex="0">mute</button>
		<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
		<div class="jp-volume-bar">
		  <div class="jp-volume-bar-value"></div>
		</div>
	  </div>
	  <div class="jp-controls-holder">
		<div class="jp-controls">
		  <button class="jp-play" role="button" tabindex="0">play</button>
		  <button class="jp-stop" role="button" tabindex="0">stop</button>
		</div>
		<div class="jp-progress">
		  <div class="jp-seek-bar">
			<div class="jp-play-bar"></div>
		  </div>
		</div>
		<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
		<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
		<div class="jp-toggles">
		  <button class="jp-repeat" role="button" tabindex="0">repeat</button>
		</div>
	  </div>
	</div>
	<div class="jp-details">
	  <div class="jp-title" aria-label="title">&nbsp;</div>
	</div>
	<div class="jp-no-solution">
	  <span>Update Required</span>
	  To play the media you will need to either update your browser to a recent version or update your <a href="https://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
	</div>
  </div>
</div> */

// -----------------------------
// Original Media Element Markup
// -----------------------------
/* <div class="mejs-container">
	<div class="mejs-inner">
		<div class="mejs-mediaelement">
			<audio class="wp-audio-shortcode">...</audio>
		</div>
		<div class="mejs-layers">
			<div class="mejs-layer">...</div>
		</div>
		<div class="mejs-controls">
			<div class="mejs-button"></div>
		</div>
		<div class="mejs-clear">...</div>
	</div>
</div> */


// ---------------------
// Set Player Debug Mode
// ---------------------
add_action( 'init', 'radio_player_set_debug_mode' );
function radio_player_set_debug_mode() {
	if ( defined( 'RADIO_PLAYER_DEBUG' ) ) {
		return;
	}
	$debug = false;
	if ( function_exists( 'radio_station_get_setting' ) ) {
		$debug = radio_station_get_setting( 'player_debug' );
	} 
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_REQUEST['player-debug'] ) && ( '1' === sanitize_text_field( wp_unslash( $_REQUEST['player-debug'] ) ) ) ) {
		$debug = true;
	}
	if ( function_exists( 'apply_filters' ) ) {
		$debug = apply_filters( 'radio_station_player_debug', $debug );
		$debug = apply_filters( 'radio_player_debug', $debug );
	}
	define( 'RADIO_PLAYER_DEBUG', $debug );
}


// --------------------
// === Radio Player ===
// --------------------

// -----------------
// Get Player Output
// -----------------
// Accepts: $args (Array)
// Array Key | Accepts
// 'title'   | [String]: Player/Station Title - 0 for none
// 'image'   | [URL]: Player/Station Image  (eg. Logo) - recommended size 256x256
// 'script'  | 'amplitude' (default), 'jplayer', 'howler', // 'mediaelements'
// 'layout'  | 'horizontal', 'vertical'
// 'theme'   | 'light', 'dark'
// 'buttons' | 'circular', 'rounded', 'square'
// 'skin'    | [not implemented] Media Elements: wordpress, minimal
// 'volume'  | [Integer: 0 to 100]: Initial Player Volume - default: 77
// 'default' | Default Player flag
// 2.5.0: added second echo argument with default to false
function radio_player_output( $args = array(), $echo = false ) {

	global $radio_player;

	// --- maybe debug output arguments ---
	if ( RADIO_PLAYER_DEBUG ) {
		echo '<span style="display:none;">Passed Radio Player Output Arguments: ';
		echo esc_html( print_r( $args, true ) ) . '</span>';
	}

	// --- settings defaults ---
	// 2.5.0: added type, metadata and channel args
	$defaults = array(
		'media'    => 'stream',
		'url'      => '',
		'format'   => '',
		'fallback' => '',
		'fformat'  => '',
		'title'    => '',
		'image'    => '',
		'script'   => 'amplitude',
		'layout'   => 'vertical',
		'theme'    => 'light',
		'buttons'  => 'rounded',
		'volume'   => 77,
		'volumes'  => array( 'slider', 'updown', 'mute', 'max' ),
		'default'  => false,
	);

	// --- ensure all arguments are set ---
	foreach ( $defaults as $key => $value ) {
		if ( !isset( $args[$key] ) ) {
			$args[$key] = $value;
		}
	}

	// --- maybe set player instance ---
	// 2.4.0.1: fix for storing multiple instance IDs
	if ( !isset( $radio_player['instances'] ) ) {
		$radio_player['instances'] = array();
	}
	$instance = 0;
	if ( isset( $args['id'] ) && ( '' != $args['id'] ) ) {
		$id = abs( intval( $args['id'] ) ) ;
		if ( $id > -1 ) {
			$instance = $id; 
		}
	}		
	if ( in_array( $instance, $radio_player['instances'] ) ) {
		while ( in_array( $instance, $radio_player['instances'] ) ) {
			$instance++;
		}
	}
	$radio_player['instances'][] = $instance;
	if ( RADIO_PLAYER_DEBUG ) {
		echo '<span style="display:none;">Player Instance: ' . esc_html( $instance ) . ' - Instances: ' . esc_html( print_r( $radio_player['instances'], true ) ) . '</span>';
	}

	// --- filter player output args ---
	// 2.4.0.3: added missing function_exists wrapper
	if ( function_exists( 'apply_filters' ) ) {
		$args = apply_filters( 'radio_station_player_output_args', $args, $instance );
		$args = apply_filters( 'radio_player_output_args', $args, $instance );
	}

	// --- maybe debug output arguments ---
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( RADIO_PLAYER_DEBUG ) {
		echo '<span style="display:none;">Parsed Radio Player Output Arguments: ' . esc_html( print_r( $args, true ) ) . '</span>';
	}

	// --- set allowed tags for KSES ---
	// 2.5.13: added for output filtering
	$allowed = function_exists( 'radio_station_allowed_html' ) ? radio_station_allowed_html( 'widget', 'player' ) : 'post';

	// --- set instanced container IDs ---
	$player_id = 'radio_player_' . $instance;
	$container_id = 'radio_container_' . $instance;

	// --- set Player div ---
	$classes = array( 'radio-player', 'rp-player', 'script-' . $args['script'] );
	if ( $args['default'] ) {
		$classes[] = 'default-player';
	}
	$class_list = implode( ' ', $classes );
	$html['player_open'] = '<div id="' . esc_attr( $player_id ) . '" class="' . esc_attr( $class_list ) . '"></div>' . "\n";

	// --- set Player container ---
	$classes = array( 'radio-container', 'rp-audio' );
	$classes[] = $args['layout'];
	$classes[] = $args['theme'];
	$classes[] = $args['buttons'];
	// 2.5.0: maybe add no volume controls class
	if ( 0 == count( $args['volumes'] ) ) {
		$classes[] = 'no-volume-controls';
	}

	// 2.5.0: added preconnect/dns-prefetch link tags for URL host
	if ( 'stream' == $args['media'] ) {
		// 2.5.10: check for existence of wp_parse_url
		if ( function_exists( 'wp_parse_url' ) ) {
			$url_host = wp_parse_url( $args['url'], PHP_URL_HOST );
		} // else {
			// $url_host = parse_url( $args['url'], PHP_URL_HOST );
		// }
		// 2.5.6: added to prevent possible deprecated warning in esc_url
		if ( isset( $url_host) && !is_null( $url_host ) && ( '' != $url_host ) ) {
			$html['player_open'] .= '<link rel="preconnect" href="' . esc_url( $url_host ) . '">' . "\n";
			$html['player_open'] .= '<link rel="dns-prefetch" href="' . esc_url( $url_host ) . '">' . "\n";
		}
		$classes[] = 'rp-audio-stream';
	} elseif ( 'file' == $args['media'] ) {
		$classes[] = 'rp-media-file';
	}

	// 2.5.0: added filter for radio container class
	if ( function_exists( 'apply_filters' ) ) {
		$classes = apply_filters( 'radio_station_player_container_classes', $classes, $args, $instance );
		$classes = apply_filters( 'radio_player_container_classes', $classes, $args, $instance );
	}
	$class_list = implode( ' ', $classes );

	$html['player_open'] .= '<div id="' . esc_attr( $container_id ) . '" class="' . esc_attr( $class_list ) . '" role="application" aria-label="media player" data-href="' . esc_url( $args['url'] ) . '" data-format="' . esc_attr( $args['format'] ) . '" data-fallback="' . esc_url( $args['fallback'] ) . '" data-fformat="' . esc_attr( $args['fformat'] ) . '"';
	// 2.5.6: added optional argument for metadata URL (or 0 to disable)
	if ( isset( $args['metadata'] ) && ( '1' != $args['metadata'] ) ) {
		// 2.5.10: use esc_url not esc_url_raw
		$html['player_open'] .= ' data-meta="' . esc_url( $args['metadata'] ) . '"';
	}
	$html['player_open'] .= '>' . "\n" . '	<div class="rp-type-single">' . "\n";
    $html['player_close'] = '</div></div>' . "\n";

	// --- set interface wrapper ---
	$html['interface_open'] = '<div class="rp-gui rp-interface">' . "\n";
	$html['interface_close'] = '</div>' . "\n";

	// --- Station Info ---
	$html['station'] = '<div class="rp-station-info">' . "\n";

		// --- station logo image ---
		$image = '';
		$classes = array( 'rp-station-image' );
		if ( ( '0' != (string)$args['image'] ) && ( 0 !== $args['image'] ) && ( '' != $args['image'] ) ) {
			// 2.5.11: added missing translation text domain
			$image = '<img id="rp-station-default-image-' . esc_attr( $instance ) . '" class="rp-station-default-image" src="' . esc_url( $args['image'] ) . '" width="100%" height="100%" border="0" aria-label="' . esc_attr( __( 'Station Logo Image', 'radio-station' ) ) . '">' . "\n";
			if ( function_exists( 'apply_filters' ) ) {
				// 2.4.0.3: fix atts to args in third filter argument
				$image = apply_filters( 'radio_station_player_station_image_tag', $image, $args['image'], $args, $instance );
				$image = apply_filters( 'radio_player_station_image_tag', $image, $args['image'], $args, $instance );
			}
			if ( !is_string( $image ) ) {
				$image = '';
				$classes[] = 'no-image';
			}
		} else {
			$classes[] = 'no-image';
		}
		$class_list = implode( ' ', $classes );
		$html['station'] .= '	<div id="rp-station-image-' . esc_attr( $instance ) . '" class="' . esc_attr( $class_list ) . '">';
		$html['station'] .= $image . '</div>' . "\n";

		// --- station text display ---
		$html['station'] .= '	<div class="rp-station-text">' . "\n";

			// --- station title ---
			$title_display = '';
			if ( ( '0' != (string)$args['title'] ) && ( 0 !== $args['title'] ) && ( '' != $args['title'] ) ) {
				$title_display .= esc_html( $args['title'] );
			}
			$title_display = apply_filters( 'radio_player_station_display', $title_display, $args, $instance );
			$station_text_html = '<div class="rp-station-title" aria-label="' . esc_attr( __( 'Station Name', 'radio-station' ) ) . '">' . wp_kses( $title_display, $allowed ) . '</div>' . "\n";

			// --- station timezone / location / frequency ---
			// 2.5.0: add filters for timezone / frequency / location display
			// TODO: add timezone / frequency / location attributes ?
			$timezone_display = isset( $args['timezone'] ) ? $args['timezone'] : '';
			$timezone_display = apply_filters( 'radio_player_timezone_display', $timezone_display, $args, $instance );
			$station_text_html .= '<div class="rp-station-timezone">' . wp_kses( $timezone_display, $allowed ) . '</div>' . "\n";

			$frequency_display = isset( $args['frequency'] ) ? $args['frequency'] : '';
			$frequency_display = apply_filters( 'radio_player_frequency_display', $frequency_display, $args, $instance );
			$station_text_html .= '<div class="rp-station-frequency">' . wp_kses( $frequency_display, $allowed ) . '</div>' . "\n";

			// 2.5.0: fix to mismatched location variable and class
			$location_display = isset( $args['location'] ) ? $args['location'] : '';
			$location_display = apply_filters( 'radio_player_location_display', $location_display, $args, $instance );
			$station_text_html .= '<div class="rp-station-location">' . wp_kses( $location_display, $allowed ) . '</div>' . "\n";
			
			$html['station'] .= $station_text_html;

		$html['station'] .= '	</div>' . "\n";

	$html['station'] .= '</div>' . "\n";

	// --- Stream Play/Pause Control ---
	$html['controls'] = '<div class="rp-controls-holder">' . "\n";
		$html['controls'] .= '<div class="rp-controls">' . "\n";
			$html['controls'] .= '<div class="rp-play-pause-button-bg">' . "\n";
				$html['controls'] .= '<div class="rp-play-pause-button" role="button" aria-label="' . esc_attr( __( 'Play Radio Stream', 'radio-station' ) ) . '"></div>' . "\n";
			$html['controls'] .= '</div>' . "\n";
			// $html['controls'] .= '		<button class="rp-stop" role="button" tabindex="0">' . esc_html( __( 'Stop', 'radio-station' ) ) . '</button>' . "\n";
		$html['controls'] .= '	</div>' . "\n";
	$html['controls'] .= '</div>' . "\n";

	// --- Volume Controls ---
	$html['volume'] = '<div class="rp-volume-controls">' . "\n";

		// --- Volume Mute ---
		$html['volume'] .= '<button class="rp-mute" role="button" tabindex="0">' . esc_html( __( 'Mute', 'radio-station' ) ) . '</button>' . "\n";

		// --- Volume Decrease ---
		// 2.5.0: fix to attribute typo area-label
		$html['volume'] .= '<button class="rp-volume-down" role="button" aria-label="' . esc_attr( __( 'Volume Down', 'radio-station' ) ) . '">-</button>' . "\n";

		// --- Custom Range volume slider ---
		$html['volume'] .= '<div class="rp-volume-slider-container">' . "\n";
			$html['volume'] .= '<div class="rp-volume-slider-bg" style="width: 0; border: none;"></div>' . "\n";
				$html['volume'] .= '<input type="range" class="rp-volume-slider" value="' . esc_attr( $args['volume'] ) . '" max="100" min="0" aria-label="' . esc_attr( __( 'Volume Slider', 'radio-station' ) ) . '">' . "\n";
			$html['volume'] .= '<div class="rp-volume-thumb"></div>' . "\n";
		$html['volume'] .= '</div>' . "\n";

		// --- Volume Increase ---
		$html['volume'] .= '<button class="rp-volume-up" role="button" aria-label="' . esc_attr( __( 'Volume Up', 'radio-station' ) ) . '">+</button>' . "\n";

		// --- Volume Max ---
		$html['volume'] .= '<button class="rp-volume-max" role="button" tabindex="0">' . esc_html( __( 'Max', 'radio-station' ) ) . '</button>' . "\n";

	$html['volume'] .= '</div>' . "\n";

	// --- dropdown script switcher for testing ---
	if ( RADIO_PLAYER_DEBUG ) {
		$html['switcher'] = '<div class="rp-script-switcher">' . "\n";
			$html['switcher'] .= '<div class="rp-show-switcher" onclick="radio_player_show_switcher(' . esc_js( $instance ) . ');">*</div>';
			$html['switcher'] .= '<select class="rp-script-select" name="rp-script-select" style="display:none;">' . "\n";
			$scripts = array( 'amplitude' => 'Amplitude', 'jplayer' => 'jPlayer', 'howler' => 'Howler' );
			foreach ( $scripts as $script => $label ) {
				$html['switcher'] .= '<option value="' . esc_attr( $script ) . '"';
				if ( $script == $args['script'] ) {
					$html['switcher'] .= ' selected="selected"';
				}
				$html['switcher'] .= '>' . esc_html( $label ) . '</option>' . "\n";
			}
			$html['switcher'] .= '</select>' . "\n";
		$html['switcher'] .= '</div>' . "\n";
	}

	// --- Current Show Texts ---
	// TODO: add other show info divs ( with expander ) ?
	$show_text_html = '<div class="rp-show-text">' . "\n";
		$show_text_html .= '<div class="rp-show-title" aria-label="' . esc_attr( __( 'Show Title', 'radio-station' ) ) . '"></div>' . "\n";
		$show_text_html .= '<div class="rp-show-hosts"></div>' . "\n";
		$show_text_html .= '<div class="rp-show-producers"></div>' . "\n";
		$show_text_html .= '<div class="rp-show-shift"></div>' . "\n";
		$show_text_html .= '<div class="rp-show-remaining"></div>' . "\n";
	$show_text_html .= '</div>' . "\n";
	$show_text_html .= '<div id="rp-show-image-' . esc_attr( $instance ) . '" class="rp-show-image no-image" aria-label="' . esc_attr( __( 'Show Logo Image', 'radio-station' ) ) . '"></div>' . "\n";

	$html['show'] = '<div class="rp-show-info">' . "\n";
		$html['show'] .= $show_text_html;
	$html['show'] .= '	</div>' . "\n";

	// --- Playback Progress Bar ---
	// (for files - not implemented yet)
	/* $html['progress'] = '<div class="rp-progress">' . "\n";
		$html['progress'] .= '<div class="rp-seek-bar">' . "\n";
			$html['progress'] .= '<div class="rp-play-bar"></div>' . "\n";
		$html['progress'] .= '</div>' . "\n";
	$html['progress'] .= '</div>' . "\n";
	$html['progress'] .= '<div class="rp-current-time" role="timer" aria-label="time">&nbsp;</div>' . "\n";
	$html['progress'] .= '<div class="rp-duration" role="timer" aria-label="duration">&nbsp;</div>' . "\n";
	$html['progress'] .= '<div class="rp-toggles">' . "\n";
	$html['progress'] .= '	<button class="rp-repeat" role="button" tabindex="0">Repeat</button>' . "\n";
	$html['progress'] .= '	<button class="rp-shuffle" role="button" tabindex="0">Shuffle</button>' . "\n";
	$html['progress'] .= '</div>' . "\n"; */

	// --- no solution section ---
	// $html['no-solution'] = '<div class="rp-no-solution">' . "\n";
	// $html['no-solution'] .= '<span>' . esc_html( __( 'Update Required', 'radio-station' ) ) . '</span>' . "\n";
	// $html['no-solution'] .= 'To play the media you will need to either update your browser to a recent version or update your <a href="https://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.' . "\n";
	// $html['no-solution'] .= '</div>' . "\n";

	// --- Current Track ---
	$html['track'] = '<div class="rp-now-playing">' . "\n";
		$html['track'] .= '<div class="rp-now-playing-item rp-now-playing-title"></div>' . "\n";
		$html['track'] .= '<div class="rp-now-playing-item rp-now-playing-artist"></div>' . "\n";
		$html['track'] .= '<div class="rp-now-playing-item rp-now-playing-album"></div>' . "\n";
	$html['track'] .= '</div>' . "\n";

	// --- set section order ---
	$section_order = array( 'station', 'interface', 'show' );
	if ( isset( $args['section_order'] ) ) {
		if ( is_array( $args['section_order'] ) ) {
			$section_order = $args['section_order'];
		} else {
			$section_order = explode( ',', $args['section_order'] );
		}
	}
	if ( function_exists( 'apply_filters' ) ) {
		$section_order = apply_filters( 'radio_station_player_section_order', $section_order, $args );
		$section_order = apply_filters( 'radio_player_section_order', $section_order, $args );
	}

	// --- set interface order ---
	// if ( 'mediaelements' == $args['script'] ) {
	//	$html['mediaelements'] = radio_player_mediaelements_interface( $args );
	//	$control_order = array( 'mediaelements' );
	// } else {
		$control_order = array( 'controls', 'volume', 'switcher', 'track' );
		if ( isset( $args['control_order'] ) ) {
			if ( is_array( $args['control_order'] ) ) {
				$control_order = $args['control_order'];
			} else {
				$control_order = explode( ',', $args['control_order'] );
			}
		}
	// }
	if ( function_exists( 'apply_filters' ) ) {
		$control_order = apply_filters( 'radio_station_player_control_order', $control_order, $args, $instance );
		$control_order = apply_filters( 'radio_player_control_order', $control_order, $args, $instance );
	}

	if ( RADIO_PLAYER_DEBUG ) {
		echo '<span style="display:none;">Section Order: ' . esc_html( print_r( $section_order, true ) ) . '</span>' ."\n";
		echo '<span style="display:none;">' . esc_html( print_r( $control_order, true ) ) . '</span>' . "\n";
	}

	// --- set alternative text sections ---
	// 2.4.0.2: added for alternative display methods
	// 2.4.0.3: added missing function_exists wrappers
	$station_text_alt = '<div class="rp-station-text-alt">' . $station_text_html . '</div>' . "\n";
	if ( function_exists( 'apply_filters' ) ) {
		$station_text_alt = apply_filters( 'radio_station_player_station_text_alt', $station_text_alt, $args, $instance );
		$station_text_alt = apply_filters( 'radio_player_station_text_alt', $station_text_alt, $args, $instance );
	}
	$show_text_alt = '<div class="rp-station-text-alt">' . $show_text_html . '</div>' . "\n";
	if ( function_exists( 'apply_filters' ) ) {
		$show_text_alt = apply_filters( 'radio_station_player_show_text_alt', $show_text_alt, $args, $instance );
		$show_text_alt = apply_filters( 'radio_player_show_text_alt', $show_text_alt, $args, $instance );
	}

	// --- create player from html sections ---
	if ( function_exists( 'apply_filters' ) ) {
		$html = apply_filters( 'radio_station_player_section_html', $html, $args, $instance );
		$html = apply_filters( 'radio_player_section_html', $html, $args, $instance );
	}
	$player = $html['player_open'];
	foreach ( $section_order as $section ) {
		if ( 'interface' == $section ) {

			// --- create control interface ---
			// 2.4.0.2: added alternative text sections
			// 2.4.0.3: fix to alternative text variables
			$player .= $html['interface_open'];
			$player .= $station_text_alt;
			foreach ( $control_order as $control ) {
				if ( isset( $html[$control] ) ) {
					$player .= $html[$control];
				}
			}
			$player .= $show_text_alt;
			$player .= $html['interface_close'];

		} elseif ( isset( $html[$section] ) ) {
			$player .= $html[$section];
		}
	}
	// if ( 'jplayer' == $args['script'] ) {
	//	$player .= $html['no-solution'];
	// }
	$player .= $html['player_close'];

	// --- filter and return ---
	// 2.4.0.3: added missing function_exists wrappers
	if ( function_exists( 'apply_filters' ) ) {
		$player = apply_filters( 'radio_station_player_html', $player, $args, $instance );
		$player = apply_filters( 'radio_player_html', $player, $args, $instance );
	}

	// 2.5.10: added direct output option
	// 2.5.13: use wp_kses not wp_kses_post
	if ( $echo ) {
		echo wp_kses( $player, $allowed );
	}
	
	return $player;
}

// --------------------------
// Store Player Instance Args
// --------------------------
if ( function_exists('add_filter' ) ) {
	add_filter( 'radio_station_player_output_args', 'radio_player_instance_args', 10, 2 );
	add_filter( 'radio_player_output_args', 'radio_player_instance_args', 10, 2 );
}
function radio_player_instance_args( $args, $instance ) {
	
	global $radio_player;

	// 2.5.0: store volume control arguments
	$radio_player['instance-props'][$instance]['volume-controls'] = $args['volumes'];

	return $args;
}

// ----------------
// Player Shortcode
// ----------------
// note: this Shortcode is WordPress / Radio Station plugin usage
if ( function_exists( 'add_shortcode' ) ) {
	add_shortcode( 'radio-player', 'radio_player_shortcode_output' );
	add_shortcode( 'stream-player', 'radio_player_shortcode_output' );
}
function radio_player_shortcode_output( $atts, $content, $tag ) {
	return radio_player_shortcode( $atts );
}
// 2.5.16: add shortcode block function with wrapper
function radio_player_block_shortcode( $atts, $content ) {
	
	global $radio_player;
	
	// --- get block output with wrapper ---
	$block = '<div class="radio-player-block"';
	$colors = array( 'text', 'background', 'playing', 'buttons', 'track', 'thumb' );
	$found = false;
	foreach ( $colors as $color ) {
		if ( isset( $atts[$color . '_color'] ) && ( '' != $atts[$color . '_color'] ) ) {
			$found = true;
			$block .= ' data-' . $color . '="' . $atts[$color . '_color'] . '"';
		}
	}
	if ( $found ) {
		$block .= ' data="colors"';
	}
	$block .= '>' . "\n";
		$block .= radio_player_shortcode( $atts );
	$block .= '</div>' . "\n";

	// --- javascript to load control colors ---
	if ( $found && !isset( $radio_player['control_styles_script'] ) ) {
		$js = "document.addEventListener('DOMContentLoaded', function() {
			playerblocks = document.querySelectorAll('.radio-player-block');
			playerblocks.forEach(block => {
				if (block.getAttribute('data') == 'colors') {
					atts = {}
					atts.text = block.getAttribute('data-text');
					atts.background = block.getAttribute('data-background');
					atts.playing = block.getAttribute('data-playing');
					atts.buttons = block.getAttribute('data-buttons');
					atts.track = block.getAttribute('data-track');
					atts.thumb = block.getAttribute('data-thumb');

					container = block.querySelector('.radio-container');
					instance = container.getAttribute('id').replace('radio_container_','');
					url = radio_player.settings.ajaxurl+'?action=player_control_styles&instance='+instance+'&text='+encodeURIComponent(atts.text)+'&background='+encodeURIComponent(atts.background)+'&playing='+encodeURIComponent(atts.playing)+'&buttons='+encodeURIComponent(atts.buttons)+'&track='+encodeURIComponent(atts.track)+'&thumb='+encodeURIComponent(atts.thumb);
					jQuery.ajax({
						type: 'GET',
						url: url,
						data: {'action':'player_control_styles', 'instance':instance, 'text':atts.text, 'background':atts.background, 'playing':atts.playing, 'buttons':atts.buttons, 'track':atts.track, 'thumb':atts.thumb},
						processData: false,
						beforeSend: function(request, settings) {
							request._data = settings.data; 
						},
						success: function(data, success, request) {
							if (data.success) {
								console.log('Load Control Styles Success: '+data.message);
								if (jQuery('#radio-player-control-styles-'+data.instance).length) {jQuery('#radio-player-control-styles-'+data.instance).remove();}
								jQuery('body').append('<style id=\"radio-player-control-styles-'+data.instance+'\">'+data.css+'</style>');
							} else {console.log('Load Control Styles Failed: '+data.message); console.log(request);}
						},
						fail: function(request, textStatus, errorThrown) {
							console.log(request); console.log(textStatus); console.log(errorThrown);
						}
					}).catch(function(error) {
						console.log(error); console.log(jQuery(this));
					});
				}
			});
		});";
		radio_player_inline_script( $js );
		$radio_player['control_styles_script'] = true;
	}

	return $block;
}
// 2.5.10: added optional echo argument
function radio_player_shortcode( $atts, $echo = false ) {

	// 2.4.0.3: fix for when no attributes passed
	if ( !is_array( $atts ) ) {
		$atts = array();
	}

	// --- maybe debug shortcode attributes --
	if ( RADIO_PLAYER_DEBUG ) {
		echo '<span style="display:none;">Passed Radio Player Shortcode Attributes: ';
		echo esc_html( print_r( $atts, true ) ) . '</span>';
	}

	// --- set base defaults ---
	$title = $image = $image_url = '';
	$script = 'amplitude';
	$layout = 'horizontal';
	$theme = 'light';
	$buttons = 'rounded';
	$volume = 77;

	// --- set default player title ---
	if ( defined( 'RADIO_PLAYER_TITLE_DISPLAY' ) ) {
		$title = RADIO_PLAYER_TITLE_DISPLAY;
	} elseif ( function_exists( 'radio_station_get_setting' ) ) {
		$title = radio_station_get_setting( 'player_title' );
		$title = ( 'yes' == $title ) ? '' : 0;
	} elseif ( function_exists( 'apply_filters' ) ) {
		$title = apply_filters( 'radio_station_player_default_title_display', $title );
		$title = apply_filters( 'radio_player_default_title_display', $title );
	}

	// --- set default player image ---
	if ( defined( 'RADIO_PLAYER_IMAGE_DISPLAY' ) ) {
		$image = RADIO_PLAYER_IMAGE_DISPLAY;
	} elseif ( function_exists( 'radio_station_get_setting' ) ) {
		$image = radio_station_get_setting( 'player_image' );
		$image = ( 'yes' == $image ) ? 1 : 0;
	} elseif ( function_exists( 'apply_filters' ) ) {
		$image = apply_filters( 'radio_station_player_default_image_display', $image );
		$image = apply_filters( 'radio_player_default_image_display', $image );
	}

	// --- set default player script ---
	// 2.5.7: disable howler script
	$scripts = array( 'amplitude', 'jplayer' ); // 'howler', 'mediaelements'
	if ( defined( 'RADIO_PLAYER_SCRIPT' ) && in_array( RADIO_PLAYER_SCRIPT, $scripts ) ) {
		$script = RADIO_PLAYER_SCRIPT;
	} elseif ( function_exists( 'radio_station_get_setting' ) ) {
		$script = radio_station_get_setting( 'player_script' );
	} elseif ( function_exists( 'apply_filters' ) ) {
		$script = apply_filters( 'radio_station_player_default_script', $script );
		$script = apply_filters( 'radio_player_default_script', $script );
	}
	// 2.5.7: reset default fallback player script if unsupported
	if ( !in_array( $script, $scripts ) ) {
		$script = 'amplitude';
	}

	// --- set default player layout ---
	if ( defined( 'RADIO_PLAYER_LAYOUT' ) ) {
		$layout = RADIO_PLAYER_LAYOUT;
	} elseif ( function_exists( 'apply_filters' ) ) {
		$layout = apply_filters( 'radio_station_player_default_layout', $layout );
		$layout = apply_filters( 'radio_player_default_layout', $layout );
	}

	// --- set default player volume ---
	if ( defined( 'RADIO_PLAYER_VOLUME' ) ) {
		$volume = RADIO_PLAYER_VOLUME;
	} elseif ( function_exists( 'radio_station_get_setting' ) ) {
		$volume = radio_station_get_setting( 'player_volume' );
	} elseif ( function_exists( 'apply_filters' ) ) {
		$volume = apply_filters( 'radio_station_player_default_volume', $volume );
		$volume = apply_filters( 'radio_player_default_volume', $volume );
	}

	// --- set volume control displays ---
	// 2.5.0: moved from CSS output function
	if ( defined( 'RADIO_PLAYER_VOLUME_CONTROLS' ) ) {
		$volume_controls = RADIO_PLAYER_VOLUME_CONTROLS;
		if ( is_string( $volume_controls ) ) {
			$volume_controls = explode( ',', $volume_controls );
		}
	} elseif ( function_exists( 'radio_station_get_setting' ) ) {
		$volume_controls = radio_station_get_setting( 'player_volumes' );
		if ( !is_array( $volume_controls ) ) {
			$volume_controls = array( 'slider', 'updown', 'mute', 'max' );
		}
	}
	// 2.5.6: move isset check outside of function_exists
	if ( !isset( $volume_controls ) ) {
		$volume_controls = array( 'slider', 'updown', 'mute', 'max' );
	}
	if ( function_exists( 'apply_filters' ) ) {
		$volume_controls = apply_filters( 'radio_station_player_volumes_display', $volume_controls );
		$volume_controls = apply_filters( 'radio_player_volumes_display', $volume_controls );
	}
	
	// --- set default player theme ---
	if ( defined( 'RADIO_PLAYER_THEME' ) ) {
		$theme = RADIO_PLAYER_THEME;
	} elseif ( function_exists( 'radio_station_get_setting' ) ) {
		$theme = radio_station_get_setting( 'player_theme' );
	} elseif ( function_exists( 'apply_filters' ) ) {
		$theme = apply_filters( 'radio_station_player_default_theme', $theme );
		$theme = apply_filters( 'radio_player_default_theme', $theme );
	}

	// --- set default player button shape ---
	if ( defined( 'RADIO_PLAYER_BUTTONS' ) ) {
		$buttons = RADIO_PLAYER_BUTTONS;
	} elseif ( function_exists( 'radio_station_get_setting' ) ) {
		$buttons = radio_station_get_setting( 'player_buttons' );
	} elseif ( function_exists( 'apply_filters' ) ) {
		$buttons = apply_filters( 'radio_station_player_default_buttons', $buttons );
		$buttons = apply_filters( 'radio_player_default_buttons', $buttons );
	}

	// --- set default atts ---
	// 2.4.0.1: add player ID attribute
	// 2.5.0: added block and popup attribute
	$defaults = array(
		// --- content attributes ---
		'media'     => 'stream',
		'url'       => '',
		'format'    => '',
		'fallback'  => '',
		'fformat'   => '',
		'title'	    => $title,
		'image'	    => $image,
		// --- player options ---
		'script'    => $script,
		'layout'    => $layout,
		'theme'     => $theme,
		'buttons'   => $buttons,
		// 'skin'   => $skin,
		'volume'    => $volume,
		'volumes'   => $volume_controls,
		'default'   => false,
		// --- id attributes ---
		'widget'    => 0,
		'id'        => '',
		'block'     => 0,
		// --- extra attributes ---
		'popup'     => 0,
	);

	// --- unset any attributes set to default ---
	foreach ( $atts as $key => $value ) {
		if ( 'default' === $value ) {
			unset( $atts[$key] );
		}
	}

	// --- filter attributes ---
	// 2.4.0.1: move filter to before merging
	if ( function_exists( 'apply_filters' ) ) {
		$atts = apply_filters( 'radio_station_player_shortcode_attributes', $atts );
		$atts = apply_filters( 'radio_player_shortcode_attributes', $atts );
	}

	// --- merge attribute values ---
	if ( function_exists( 'shortcode_atts' ) ) {
		$atts = shortcode_atts( $defaults, $atts, 'radio-player' );
	}
	foreach ( $defaults as $key => $value ) {
		if ( !isset( $atts[$key] ) ) {
			$atts[$key] = $value;
		}
	}

	// --- maybe debug shortcode attributes --
	if ( RADIO_PLAYER_DEBUG ) {
		echo '<span style="display:none;">Combined Radio Player Shortcode Attributes: ' . esc_html( print_r( $atts, true ) ) . '</span>' . "\n";
	}

	// --- maybe get station title ---
	if ( '' == $atts['title'] ) {
		if ( defined( 'RADIO_PLAYER_TITLE' ) ) {
			$atts['title'] = RADIO_PLAYER_TITLE;
		} elseif ( function_exists( 'radio_station_get_setting' ) ) {
			$atts['title'] = radio_station_get_setting( 'station_title' );
		} elseif ( function_exists( 'apply_filters' ) ) {
			$atts['title'] = apply_filters( 'radio_station_player_default_title', '' );
			$atts['title'] = apply_filters( 'radio_player_default_title', $atts['title'] );
		}
	} elseif ( ( '0' == $atts['title'] ) || ( 0 === $atts['title'] ) ) {
		// --- allows disabling via 0 attribute value ---
		// 2.4.0.3: allow for string or integer value match
		$atts['title'] = '';
	}

	// --- maybe get station image ---
	if ( $atts['image'] ) {
		// note: converts attribute switch to URL
		if ( ( '1' == $atts['image'] ) || ( 1 == $atts['image'] ) ) {
			if ( defined( 'RADIO_PLAYER_IMAGE' ) ) {
				$atts['image'] = RADIO_PLAYER_IMAGE;
			} elseif ( function_exists( 'radio_station_get_setting' ) ) {
				$station_image = radio_station_get_setting( 'station_image' );
				if ( $station_image ) {
					$attachment = wp_get_attachment_image_src( $station_image, 'full' );
					if ( is_array( $attachment ) ) {
						$atts['image'] = $attachment[0];
					} else {
						$atts['image'] = 0;
					}
				} else {
					$atts['image'] = 0;
				}
			} elseif ( function_exists( 'apply_filters' ) ) {
				$atts['image'] = apply_filters( 'radio_station_player_default_image', '' );
				$atts['image'] = apply_filters( 'radio_player_default_image', $atts['image'] );
			}
		}
	}

	// DEV TEMP: allow default script override via querystring
	// if ( isset( $_REQUEST['script'] ) && in_array( sanitize_text_field( $_REQUEST['script'], $scripts ) ) ) {
	//	$atts['script'] = sanitize_text_field( $_REQUEST['script'] );
	// }

	// --- check script override constant ---
	if ( defined( 'RADIO_PLAYER_FORCE_SCRIPT' ) && in_array( RADIO_PLAYER_FORCE_SCRIPT, $scripts ) ) {
		$atts['script'] = RADIO_PLAYER_FORCE_SCRIPT;
	}

	// --- check for full player output override ---
	$player = $override = '';
	if ( function_exists( 'apply_filters' ) ) {
		$override = apply_filters( 'radio_station_player_output', $override, $atts );
		$override = apply_filters( 'radio_player_output', $override, $atts );
	}
	if ( '' != $override ) {

		// --- use full override for output ---
		if ( $echo ) {
			echo wp_kses_post( $override );
		} else {
			$player = $override;
		}

	} else {

		// --- maybe open shortcode wrapper ---
		if ( !$atts['widget'] ) {
			if ( $echo ) {
				echo '<div class="radio-player-shortcode">' . "\n";
			} else {
				$player .= '<div class="radio-player-shortcode">' . "\n";
			}
		}

		// --- maybe debug shortcode attributes ---
		if ( RADIO_PLAYER_DEBUG ) {
			echo '<span style="display:none;">Parsed Radio Player Shortcode Attributes: ' . esc_html( print_r( $atts, true ) ) . '</span>';
		}

		// --- get player HTML ---
		if ( $echo ) {
			radio_player_output( $atts, true );
		} else {
			$player .= radio_player_output( $atts );
		}

		// -- maybe close shortcode wrapper ---
		if ( !$atts['widget'] ) {
			if ( $echo ) {
				echo '</div>' . "\n";
			} else {
				$player .= '</div>' . "\n";
			}
		}
	}

	// --- enqueue player script in footer ---
	radio_player_core_scripts();
	radio_player_enqueue_script( $atts['script'] );

	// --- enqueue player styles ---
	radio_player_enqueue_styles( $atts['script'], false ); // $atts['skin']

	// --- add update iframe to footer ---
	// 2.5.8: removed iframe in favour of using jquery get
	// (for saving WordPress logged in user states)
	// if ( function_exists( 'add_action' ) ) {
	//	add_action( 'wp_footer', 'radio_player_iframe', 20 );
	// }

	return $player;
}

// -----------------------------------
// Set Player Default Color Attributes
// -----------------------------------
add_filter( 'radio_player_shortcode_attributes', 'radio_player_default_colors' );
function radio_player_default_colors( $atts ) {

	// --- map bar color settings to shortcode attributes ---
	// 2.5.0: added for mapping colors to attributes and instance
	if ( isset( $atts['sitewide'] ) ) {
		$keys = array(
			'playing_color'    => 'player_playing_color',
			'buttons_color'    => 'player_buttons_color',
			'track_color'      => 'player_range_color',
			'thumb_color'      => 'player_thumb_color',
		);
		foreach ( $keys as $att => $key ) {
			if ( !isset( $atts[$att] ) ) {
				$atts[$att] = radio_station_get_setting( $key );
			}
		}
	}

	return $atts;
}

// --------------------
// AJAX: Player Display
// --------------------
add_action( 'wp_ajax_radio_player', 'radio_player_ajax' );
add_action( 'wp_ajax_nopriv_radio_player', 'radio_player_ajax' );
function radio_player_ajax() {

	// --- sanitize shortcode attributes ---
	$atts = radio_player_sanitize_shortcode_values();

	// 2.5.0: anti-conflict with WP theme querystring overrides
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['theme'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		unset( $_GET['theme'] );
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.NonceVerification.Missing
	if ( isset( $_POST['theme'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		unset( $_POST['theme'] );
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_REQUEST['theme'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		unset( $_REQUEST['theme'] );
	}

	// 2.5.0: check for popup attribute
	$popup = ( isset( $atts['popup'] ) && $atts['popup'] ) ? true : false;
	// 2.5.0: clear widget/block/popup attributes
	$atts['widget'] = $atts['block'] = $atts['popup'] = 0;

	// 2.5.0: strip text color attribute (applied to div container)
	$text_color = '';
	if ( isset( $atts['text_color'] ) ) {
		$text_color = $atts['text_color'];
		unset( $atts['text_color'] );
		if ( isset( $atts['text'] ) ) {
			unset( $atts['text'] );
		}
	} elseif ( isset( $atts['text'] ) ) {
		$text_color = $atts['text'];
		unset( $atts['text_color'] );
	} elseif ( function_exists( 'apply_filters' ) ) {
		// 2.5.14: add instance argument for consistency
		$text_color = apply_filters( 'radio_player_text_color', $text_color, false );
	}

	// 2.5.0: strip background color attribute (applied to window body)
	$background_color = '';
	if ( isset( $atts['background_color'] ) ) {
		$background_color = $atts['background_color'];
		unset( $atts['background_color'] );
		if ( isset( $atts['background'] ) ) {
			unset( $atts['background'] );
		}
	} elseif ( isset( $atts['background'] ) ) {
		$background_color = $atts['background'];
		unset( $atts['background'] );
	} elseif ( function_exists( 'apply_filters' ) ) {
		// 2.5.0: fallback to apply_filters
		// 2.5.14: add instance argument for consistency
		$background_color = apply_filters( 'radio_player_background_color', $background_color, false );
	}

	// --- maybe add text color ---
	// 2.5.0: added for matching with background color
	// 2.5.6: fix for undefined variable css
	// 2.5.10: moved up so that inline style can be in header
	// 2.5.14: add body prefix to selector
	$css = '';
	if ( '' != $text_color ) {
		if ( ( 'rgb' != substr( $text_color, 0, 3 ) ) && ( '#' != substr( $text_color, 0, 1 ) ) ) {
			$text_color = '#' . $text_color;
		}
		$css .= 'body #player-contents {color: ' . esc_attr( $text_color ) . ';}' . "\n";
	}

	// --- maybe add background color ---
	// 2.5.14: add body prefix to selector
	if ( '' != $background_color ) {
		if ( ( 'rgb' != substr( $background_color, 0, 3 ) ) && ( '#' != substr( $background_color, 0, 1 ) ) ) {
			$background_color = '#' . $background_color;
		}
		$css .= 'body, body #player-contents {background-color: ' . esc_attr( $background_color ) . ';}' . "\n";
	}

	// 2.5.10: set document title with javascript
	if ( isset( $atts['title'] ) && $atts['title'] && ( '' != $atts['title'] ) ) {
		$js = "document.title = '" . esc_js( $atts['title'] ) . "';" . "\n";
		// 2.5.10.1: fix to incorrect function name
		radio_player_inline_script( $js );
	}

	// --- open HTML and head ---
	// 2.5.10: set title with javascript instead to avoid output buffering entirely
	// 2.5.0: buffer head content to maybe replace window title tag
	// note: do not remove these span tags, they magically "fix" broken output buffering!?
	echo '<html><head>' . "\n";
	// echo '<span></span>';
	// ob_start();
	// echo '<span></span>';
	wp_head();
	// echo '<span></span>';
	// $head = ob_get_contents();
	// echo '<span></span>';
	// ob_end_clean();
	/* if ( isset( $atts['title'] ) && $atts['title'] && ( '' != $atts['title'] ) ) {
		if ( stristr( $head, '<title' ) ) {
			$posa = stripos( $head, '<title' );
			$chunks = str_split( $head, $posa );
			$before = $chunks[0];
			unset( $chunks[0] );
			$head = implode( '', $chunks );
			$posa = stripos( $head, '>' ) + 1;
			$chunks = str_split( $head, $posa );
			unset( $chunks[0] );
			$head = implode( '', $chunks );
			$posb = stripos( $head, '</title>' ) + strlen( '</title>' );
			$chunks = str_split( $head, $posb );
			unset( $chunks[0] );
			$after = implode( '', $chunks );
			$head = $before . "\n" . '<title>' . esc_html( $atts['title'] ) . '</title>' . "\n" . $after;
		} else {
			$head .= '<title>' . esc_html( $atts['title'] ) . '</title>' . "\n";
		}
	} */
	
	// echo $head . "\n";
	echo '</head>' . "\n";

	// --- open body ---
	echo '<body>' . "\n";
		
	// --- debug shortcode attributes ---
	if ( defined( 'RADIO_PLAYER_DEBUG' ) && RADIO_PLAYER_DEBUG ) {
		echo '<span style="display:none;">Radio Player Shortcode Attributes: ' . esc_html( print_r( $atts, true ) ) . '</span>';
	}

	// --- output widget contents ---
	// 2.5.0: maybe add popup player class
	echo '<div id="player-contents"';
	if ( $popup ) {
		echo ' class="popup"';
	}
	echo '>' . "\n";
		// 2.5.10: use echo as second argument
		radio_player_shortcode( $atts, true );
	echo '</div>' . "\n";

	// --- output (hidden) footer for scripts ---
	echo '<div style="display:none;">' . "\n";

		// --- output extra player styles ---
		// 2.5.14: move add inline style to before footer
		$css = apply_filters( 'radio_station_player_ajax_styles', $css, $atts );
		$css = apply_filters( 'radio_player_ajax_styles', $css, $atts );
		// 2.5.6: use wp_kses_post instead of wp_strip_all_tags
		// 2.5.6: use radio_player_add_inline_style (with fallback)
		radio_player_add_inline_style( $css );

		// --- call wp_footer actions ---
		wp_footer();

	// --- close footer ---
	echo '</div>' . "\n";

	// --- close body and HTML ---
	echo '</body></html>' . "\n";

	exit;
}


// -----------------
// Add Inline Styles
// -----------------
// 2.5.6: added for possible missed inline styles (via shortcodes)
function radio_player_add_inline_style( $css ) {

	// TODO: find a way to fix this? it does NOT enqueue inline
	// --- add check if style is already done ---
	if ( wp_style_is( 'radio-player', 'registered' ) && !wp_style_is( 'radio-player', 'done' ) ) {

		// --- add styles as normal ---
		wp_add_inline_style( 'radio-player', $css );
		
	} else {

		$version = function_exists( 'radio_station_plugin_version' ) ? radio_station_plugin_version() : '2.5.0';
		wp_register_style( 'radio-player-footer', null, array(), $version, 'all' );
		wp_enqueue_style( 'radio-player-footer' );
		wp_add_inline_style( 'radio-player-footer', $css );

		// --- fallback: store extra styles for later output ---
		// global $radio_player_styles;
		// add_action( 'wp_print_footer_scripts', 'radio_player_print_footer_styles', 20 );
		// if ( !isset( $radio_player_styles ) ) {
		//	$radio_player_styles = '';
		// }
		// $radio_player_styles .= $css;

	}
}

// -------------------
// Print Footer Styles
// -------------------
// 2.5.6: added for possible missed inline styles (via shortcode)
// 2.5.10: removed in favour of enqueueing inline style to dummy

// -------------------------
// Sanitize Shortcode Values
// -------------------------
function radio_player_sanitize_shortcode_values() {

	// --- current show attribute keys ---
	// 2.5.0: added alternative text attribute
	// 2.5.0: added block attribute
	$keys = array(
		'url'        => 'url',
		'title'	     => 'text',
		'image'	     => 'url',
		'script'     => 'howler/amplitude/jplayer',
		'layout'     => 'text',
		'theme'      => 'text',
		'buttons'    => 'text',
		'volume'     => 'integer',
		'default'    => 'boolean',
		'widget'     => 'boolean',
		'background' => 'text',
		'text'       => 'text',
		'block'      => 'boolean',
	);

	// 2.5.0: added filter for attribute keys
	$keys = apply_filters( 'radio_player_attribute_keys', $keys );

	// --- sanitize values by key type ---
	// 2.5.6: remove unnecessary first argument
	// $atts = radio_player_sanitize_values( $_REQUEST, $keys );
	$atts = radio_player_sanitize_values( $keys );
	return $atts;
}

// ---------------
// Sanitize Values
// ---------------
// 2.5.6: remove unnecessary first argument
// function radio_player_sanitize_values( $data, $keys ) {
function radio_player_sanitize_values( $keys ) {

	$sanitized = array();
	foreach ( $keys as $key => $type ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST[$key] ) ) {
			if ( 'boolean' == $type ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( ( '0' === sanitize_text_field( wp_unslash( $_REQUEST[$key] ) ) )
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				  || ( '1' === sanitize_text_field( wp_unslash( $_REQUEST[$key] ) ) ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$sanitized[$key] = sanitize_text_field( wp_unslash( $_REQUEST[$key] ) );
				}
			} elseif ( 'integer' == $type ) {
				// 2.5.10: fix to mismatched variable (data)
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$sanitized[$key] = absint( wp_unslash( $_REQUEST[$key] ) );
			} elseif ( 'alphanumeric' == $type ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$value = preg_match( '/^[a-zA-Z0-9_]+$/', sanitize_text_field( wp_unslash( $_REQUEST[$key] ) ) );
				if ( $value ) {
					$sanitized[$key] = $value;
				}
			} elseif ( 'text' == $type ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$sanitized[$key] = sanitize_text_field( wp_unslash( $_REQUEST[$key] ) );
			} elseif ( 'slug' == $type ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$sanitized[$key] = sanitize_title( wp_unslash( $_REQUEST[$key] ) );
			} elseif ( strstr( $type, '/' ) ) {
				$options = explode( '/', $type );
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( in_array( sanitize_text_field( wp_unslash( $_REQUEST[$key] ) ), $options ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$sanitized[$key] = sanitize_text_field( wp_unslash( $_REQUEST[$key] ) );
				}
			}
		}
	}
	return $sanitized;
}

// ------------------------
// Media Elements Interface
// ------------------------
// note: exception to the main interface used by all other scripts
/* function radio_player_mediaelements_interface( $atts ) {

	global $radio_player;

	$post_id = 0;
	if ( function_exists( 'get_post' ) && function_exists( 'get_the_ID' ) ) {
		$post_id = get_post() ? get_the_ID() : 0;
	}

	// --- set player instance ---
	$instance = 0;
	if ( isset( $radio_player['me_instance'] ) ) {
		$instance = $radio_player['me_instance'];
	}
	$instance++;

	// --- get shortcode attributes ---
	$defaults_atts = array(
		'src'      => '',
		'loop'     => '',
		'autoplay' => '',
		'preload'  => 'none',
		'class'    => 'rp-audio', // 'mejs-audio'
		'style'    => 'width: 100%;'
	);
	if ( function_exists( 'shortcode_atts' ) ) {
		$atts = shortcode_atts( $defaults_atts, $atts, 'radio-player-mediaelements' );
	} else {
		foreach ( $defaults as $key => $value ) {
			if ( !isset( $atts[$key] ) ) {
				$atts[$key] = $value;
			}
		}
	}
	if ( function_exists( 'apply_filters' ) ) {
		$atts = apply_filters( 'radio_station_player_atts', $atts );
		$atts = apply_filters( 'radio_player_atts', $atts );
	}

	// --- set HTML attributes ---
	// TODO: replace and store player ID ?
	$html_atts = array(
		'class'    => $atts['class'],
		'id'       => sprintf( 'audio-%d-%d', $post_id, $instance ),
		'loop'     => (bool) $atts['loop'],
		'autoplay' => (bool) $atts['autoplay']),
		'preload'  => $atts['preload'],
		'style'    => $atts['style'],
	);
	foreach ( array( 'loop', 'autoplay', 'preload' ) as $a ) {
		if ( empty( $html_atts[$a] ) ) {
			unset( $html_atts[$a] );
		}
	}

	// --- set audio attributes ---
	$attr_strings = array();
	foreach ( $html_atts as $k => $v ) {
		$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
	}

	// --- open audio element ---
	$html = '';
	if ( 1 === $instance ) {
		$html .= "<!--[if lt IE 9]><!--script>document.createElement('audio');</script><![endif]-->\n";
	}
	$html .= sprintf( '<audio %s controls="controls">', join( ' ', $attr_strings ) );

	// --- set audio source ---
	$source = '<source type="%s" src="%s" />';
	$html .= sprintf( $source, $stream_format, $streaming_url );
	if ( $fallback_format && $fallback_url ) {
		$html .= sprintf( $source, $fallback_format, $fallback_url );
	}

	// --- close audio element ---
	$html .= '</audio>';

	// --- filter and return ---
	if ( function_exists( 'apply_filters' ) ) {
		$html = apply_filters( 'radio_player_mediaelements_interface', $html, $atts, $post_id );
	}
	return $html;
} */


// ----------------------
// === Player Scripts ===
// ----------------------

// -------------------------
// Enqueue Player Javascript
// -------------------------
function radio_player_core_scripts() {

	global $radio_player;
	if ( !isset( $radio_player ) ) {
		$radio_player = array();
	}

	// --- enqueue sysend message script ---
	// 2.4.0.9: update sysend to version 1.11.1
	$version = '1.11.1';
	if ( function_exists( 'wp_enqueue_script' ) ) {

		// --- enqueue player script ---
		if ( defined( 'RADIO_PLAYER_URL' ) ) {
			$url = RADIO_PLAYER_URL . 'js/sysend.js';
		} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
			$url = plugins_url( 'player/js/sysend.js', RADIO_STATION_FILE );
		} else {
			$url = plugins_url( 'js/sysend.js', __FILE__ );
		}
		wp_enqueue_script( 'sysend', $url, array(), $version, true );

	} /* elseif ( !isset( $radio_player['printed_sysend'] ) ) {

		// --- output script tag directly ---
		$url = 'js/sysend.js';
		if ( defined( 'RADIO_PLAYER_URL' ) ) {
			$url = RADIO_PLAYER_URL . $url;
		}
		radio_player_script_tag( $url, $version );
		$radio_player['printed_sysend'] = true;

	} */

	// --- enqueue radio player script ---
	// TODO: add minimized version of player script ?
	// $suffix = '.min';
	// if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		$suffix = '';
	// }
	if ( defined( 'RADIO_STATION_DIR' ) ) {
		$version = filemtime( RADIO_STATION_DIR . '/player/js/radio-player' . $suffix . '.js' );
	} else {
		$version = filemtime( dirname( __FILE__ ) . '/js/radio-player' . $suffix . '.js' );
	}
	if ( function_exists( 'wp_enqueue_script' ) ) {

		// --- enqueue player script ---
		if ( defined( 'RADIO_PLAYER_URL' ) ) {
			$url = RADIO_PLAYER_URL . 'js/radio-player' . $suffix . '.js';
		} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
			$url = plugins_url( 'player/js/radio-player' . $suffix . '.js', RADIO_STATION_FILE );
		} else {
			$url = plugins_url( 'js/radio-player' . $suffix . '.js', __FILE__ );
		}
		wp_enqueue_script( 'radio-player', $url, array( 'jquery' ), $version, true );

	} /* elseif ( !isset( $radio_player['printed_player'] ) ) {

		// note: jQuery should be enqueued for standalone version

		// --- output script tag directly ---
		$url = 'js/radio-player' . $suffix . '.js';
		if ( defined( 'RADIO_PLAYER_URL' ) ) {
			$url = RADIO_PLAYER_URL . $url;
		}
		radio_player_script_tag( $url, $version );
		$radio_player['printed_player'] = true;

	} */

	// --- set minified script suffix ---
	$suffix = '.min';
	if ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'RADIO_STATION_DEBUG') && RADIO_STATION_DEBUG ) ) {
		  $suffix = '';
	}
	// $suffix = ''; // DEV TEST

	// --- set amplitude script ---
	$path = dirname( __FILE__ ) . '/js/amplitude' . $suffix . '.js';
	if ( function_exists( 'wp_enqueue_script' ) ) {
		if ( defined( 'RADIO_PLAYER_URL' ) ) {
			$url = RADIO_PLAYER_URL . 'js/amplitude' . $suffix . '.js';
		} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
			$url = plugins_url( 'player/js/amplitude' . $suffix . '.js', RADIO_STATION_FILE );
		} else {
			$url = plugins_url( 'js/amplitude' . $suffix . '.js', __FILE__ );
		}

	} /* else {
		$url = 'js/amplitude' . $suffix . '.js';
		if ( defined( 'RADIO_PLAYER_URL' ) ) {
			$url = RADIO_PLAYER_URL . $url;
		}
	} */
	$radio_player['amplitude_script'] = array( 'version' => '5.3.2', 'url' => $url, 'path' => $path );

	// --- set jplayer script ---
	$path = dirname( __FILE__ ) . '/js/jplayer' . $suffix . '.js';
	if ( function_exists( 'wp_enqueue_script' ) ) {
		if ( defined( 'RADIO_PLAYER_URL' ) ) {
			$url = RADIO_PLAYER_URL . 'js/jplayer' . $suffix . '.js';
		} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
			$url = plugins_url( 'player/js/jplayer' . $suffix . '.js', RADIO_STATION_FILE );
		} else {
			$url = plugins_url( 'js/jplayer' . $suffix . '.js', __FILE__ );
		}
	} /* else {
		radio_player_script_tag( $url, $version );
		$radio_player['printed_jplayer'] = true;
	} */
	$radio_player['jplayer_script'] = array( 'version' => '2.9.2', 'url' => $url, 'path' => $path );

	// --- set howler script ---
	$path = dirname( __FILE__ ) . '/js/howler' . $suffix . '.js';
	if ( function_exists( 'wp_enqueue_script' ) ) {
		if ( defined( 'RADIO_PLAYER_URL' ) ) {
			$url = RADIO_PLAYER_URL . 'js/howler' . $suffix . '.js';
		} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
			$url = plugins_url( 'player/js/howler' . $suffix . '.js', RADIO_STATION_FILE );
		} else {
			$url = plugins_url( 'js/howler' . $suffix . '.js', __FILE__ );
		}
	} /* else {
		$url = 'js/howler' . $suffix . '.js';
		if ( defined( 'RADIO_PLAYER_URL' ) ) {
			$url = RADIO_PLAYER_URL . $url;
		}
	} */
	$radio_player['howler_script'] = array( 'version' => '2.2.3', 'url' => $url, 'path' => $path );

	// --- set core media elements script ---
	/* $version = '4.2.6'; // as of WP 4.9
	$version = filemtime( dirname( __FILE__ ) . '/js/mediaelement-and-player' . $suffix . '.js' );
	$url = 'js/mediaelement-and-player' . $suffix . '.js';
	if ( defined( 'RADIO_PLAYER_URL' ) ) {$url = RADIO_PLAYER_URL . $url;}
	$radio_player['media_script'] = array( 'version' => $version, 'url' => $url, 'path' => $path );

	// --- set media elements player script ---
	$path = dirname( __FILE__ ) . '/js/rp-mediaelement' . $suffix . '.js';
	if ( function_exists( 'wp_enqueue_script' ) ) {
		if ( defined( 'RADIO_PLAYER_URL' ) ) {
			$url = RADIO_PLAYER_URL . 'js/rp-mediaelement.js';
			$version = filemtime( dirname( __FILE__ ) . '/js/rp-mediaelement.js' );
		} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
			$url = plugins_url( 'player/js/rp-mediaelement.js', RADIO_STATION_FILE );
			$version = filemtime( RADIO_STATION_DIR . '/player/js/rp-mediaelement.js' );
		} else {
			$url = plugins_url( 'js/rp-mediaelement.js', __FILE__ );
			$version = filemtime( dirname( __FILE__ ) . '/js/rp-mediaelement.js' );
		}
	} elseif ( !isset( $radio_player['printed_mediaelement'] ) ) {
		// note: no minified version here yet ?
		// $version = filemtime( dirname( __FILE__ ) . '/js/rp-mediaelement.js' );
		// $url = 'js/rp-mediaelement.js';
		// if ( defined( 'RADIO_PLAYER_URL' ) ) {$url = RADIO_PLAYER_URL . $url;}
	}
	$radio_player['elements_script'] = array( 'version' => $version, 'url' => $url, 'path' => $path );
	*/

	// --- add radio player settings (once only) ---
	// note: intentionally here after player scripts are set
	if ( !isset( $radio_player['enqeued_player'] ) ) {
		$js = radio_player_get_player_settings();
		if ( '' != $js ) {
			if ( function_exists( 'wp_add_inline_script' ) ) {
				// 2.5.0: added check if script already done
				if ( !wp_script_is( 'radio-player', 'done' ) ) {
					// --- add inline script ---
					wp_add_inline_script( 'radio-player', $js, 'before' );
				} else {
					// --- print settings directly ---
					// 2.5.7: use direct echo arg for player settings
					radio_player_get_player_settings( true );
				}
			} else {
				// --- print settings directly ---
				// 2.5.7: use direct echo arg for player settings
				radio_player_get_player_settings( true );
			}
		}
		$radio_player['enqueued_player'] = true;
	}

	// 2.5.0: added do action for player scripts
	do_action( 'radio_station_player_enqueued_scripts' );
	do_action( 'radio_player_enqueued_scripts' );

}

// ---------------------
// Enqueue Player Script
// ---------------------
function radio_player_enqueue_script( $script ) {

	global $radio_player;
	if ( !isset( $radio_player ) ) {
		$radio_player = array();
	}

	if ( RADIO_PLAYER_DEBUG ) {
		echo '<span style="display:none;">Default Player Script: ' . esc_html( $script ) . '</span>';
	}

	// --- add player specific functions (once only ) ---
	if ( ( 'amplitude' == $script ) && !isset( $radio_player['enqeued_amplitude'] ) ) {

		radio_player_enqueue_amplitude( true );

	} elseif ( ( 'jplayer' == $script ) && !isset( $radio_player['enqeued_jplayer'] ) ) {

		radio_player_enqueue_jplayer( true );

	} elseif ( ( 'howler' == $script ) &&  !isset( $radio_player['enqeued_howler'] ) ) {

		radio_player_enqueue_howler( true );

	}
	// elseif ( ( 'mediaelements' == $script ) &&  !isset( $radio_player['enqeued_mediaelements'] ) ) {
	//	radio_player_enqueue_mediaelements( true );
	//	$js = radio_player_script_mediaelements();
	// }

	// --- set specific script as enqueued ---
	$radio_player['enqeued_' . $script] = true;

	if ( isset( $radio_player['enqueued_inline_scripts'] ) && $radio_player['enqueued_inline_scripts'] ) {
		return;
	}

	$js = '';
	// 2.4.0.3: load all player scripts regardless
	// 2.5.7: disable scripts here (moved into main js file)
	// $js .= radio_player_script_howler();
	// $js .= radio_player_script_jplayer();
	// $js .= radio_player_script_amplitude();

	// --- append any pageload scripts ---
	if ( function_exists( 'apply_filters') ) {
		$pageload = apply_filters( 'radio_station_player_pageload_script', '' );
		$pageload = apply_filters( 'radio_player_pageload_script', $pageload );
		if ( '' != $pageload ) {
			$js .= "\n" . "jQuery(document).ready(function() {" . "\n" . $pageload . "\n" . "});" . "\n";
		}
	}

	// --- maybe filter the full script output ---
	if ( function_exists( 'apply_filters' ) ) {
		$js = apply_filters( 'radio_station_player_scripts', $js );
		$js = apply_filters( 'radio_player_scripts', $js );
	}

	// --- output script tag ---
	if ( '' != $js ) {
		radio_player_inline_script( $js );
	}
	
	// --- set specific script as enqueued ---
	// 2.5.15: fix to mismatching enqueued flag
	$radio_player['enqueued_inline_scripts'] = true;

}

// -----------------
// Add Inline Script
// -----------------
// 2.5.10: added for fallback printing of scripts in footer
function radio_player_inline_script( $js, $position = 'after' ) {

	if ( function_exists( 'wp_add_inline_script' ) ) {
		if ( !wp_script_is( 'radio-player', 'done' ) ) {
			wp_add_inline_script( 'radio-player', $js, $position );
		} else {
			$version = function_exists( 'radio_station_plugin_version' ) ? radio_station_plugin_version() : '2.5.0';
			wp_register_script( 'radio-player-footer', null, array( 'jquery' ), $version, true );
			wp_enqueue_script( 'radio-player-footer' );
			wp_add_inline_script( 'radio-player-footer', $js, $position );
		}
	}

}


// --------------------------------
// Lazy Load Audio Script Fallbacks
// --------------------------------
// 2.4.0.3: lazy load fallback scripts on pageload to cache them
// add_filter( 'radio_station_player_pageload_script', 'radio_player_load_script_fallbacks' );
add_filter( 'radio_player_pageload_script', 'radio_player_load_script_fallbacks' );
function radio_player_load_script_fallbacks( $js ) {

	global $radio_player;

	// 2.4.0.3: check for fallback selection (default all)
	$fallbacks = array( 'jplayer', 'howler', 'amplitude' );
	if ( function_exists( 'radio_station_get_setting' ) ) {
		$fallbacks = radio_station_get_setting( 'player_fallbacks' );
	} elseif ( function_exists( 'apply_filters' ) ) {
		$fallbacks = apply_filters( 'radio_station_player_fallbacks', $fallbacks );
		$fallbacks = apply_filters( 'radio_player_fallbacks', $fallbacks );
	}
	if ( defined( 'RADIO_PLAYER_FALLBACKS' ) ) {
		$fallbacks = explode( ',', RADIO_PLAYER_FALLBACKS );
	}

	// --- load fallback audio scripts ---
	if ( count( $fallbacks ) > 0 ) {
		$js .= "head = document.getElementsByTagName('head')[0]; ";
		if ( !isset( $radio_player['enqueued_howler'] ) && in_array( 'howler', $fallbacks ) ) {
			$js .= "el = document.createElement('script'); el.src = radio_player.scripts.howler; head.appendChild(el);";
		}
		if ( !isset( $radio_player['enqueued_jplayer'] )  && in_array( 'jplayer', $fallbacks ) ) {
			$js .= "el = document.createElement('script'); el.src = radio_player.scripts.jplayer; head.appendChild(el);";
		}
		if ( !isset( $radio_player['enqueued_amplitude'] )  && in_array( 'amplitude', $fallbacks ) ) {
			$js .= "el = document.createElement('script'); el.src = radio_player.scripts.amplitude; head.appendChild(el);";
		}
		$js .= PHP_EOL;
	}
	
	return $js;
}

// ----------------------------
// Enqueue Amplitude Javascript
// ----------------------------
function radio_player_enqueue_amplitude( $infooter ) {
	global $radio_player;
	if ( function_exists( 'wp_enqueue_script' ) ) {
		// note: jquery dependency not required
		wp_enqueue_script( 'amplitude', $radio_player['amplitude_script']['url'], array(), $radio_player['amplitude_script']['version'], $infooter );
	} /* elseif ( !isset( $radio_player['printed_amplitude'] ) ) {
		radio_player_script_tag( $radio_player['amplitude_script']['url'], $radio_player['amplitude_script']['version'] );
		$radio_player['printed_amplitude'] = true;
	} */
}

// --------------------------
// Enqueue JPlayer Javascript
// --------------------------
function radio_player_enqueue_jplayer( $infooter ) {
	global $radio_player;
	if ( function_exists( 'wp_enqueue_script' ) ) {
		wp_enqueue_script( 'jplayer', $radio_player['jplayer_script']['url'], array( 'jquery' ), $radio_player['jplayer_script']['version'], $infooter );
	} /* elseif ( !isset( $radio_player['printed_jplayer'] ) ) {
		radio_player_script_tag( $radio_player['jplayer_script']['url'], $radio_player['jplayer_script']['version'] );
		$radio_player['printed_jplayer'] = true;
	} */
}

// -------------------------
// Enqueue Howler Javascript
// -------------------------
// TODO: maybe test use of howler.core.min.js instead ?
function radio_player_enqueue_howler( $infooter ) {
	global $radio_player;
	if ( function_exists( 'wp_enqueue_script' ) ) {
		wp_enqueue_script( 'howler', $radio_player['howler_script']['url'], array( 'jquery' ), $radio_player['howler_script']['version'], $infooter );
	} /* elseif ( !isset( $radio_player['printed_howler'] ) ) {
		radio_player_script_tag( $radio_player['howler_script']['url'], $radio_player['howler_script']['version'] );
		$radio_player['printed_howler'] = true;
	} */
}

// --------------------------------
// Enqueue Media Element Javascript
// --------------------------------
/* function radio_player_enqueue_mediaelements( $infooter ) {
	global $radio_player;

	// --- enqueue media element javascript ---
	if ( function_exists( 'wp_enqueue_script' ) ) {
		// note: media player script enqueued via dependency
		wp_enqueue_script( 'rp-mediaelement', $radio_player['elements_script']['url'], array( 'mediaelement' ), $radio_player['elements_script']['version'], $infooter );
	} elseif ( !isset( $radio_player['printed_mediaelement'] ) ) {
		// --- output core media element script ---
		// radio_player_script_tag( $radio_player['media_script']['url'], $radio_player['media_script']['version'] );

		// --- output media element player script ---
		// radio_player_script_tag( $radio_player['elements_script']['url'], $radio_player['elements_script']['version'] );
		// $radio_player['printed_mediaelement'] = true;
	}


	// --- localize settings ---
	// TODO: move this code block
	if ( function_exists( 'plugins_url' ) ) {
		$url = plugins_url( 'js/', __FILE__ );
	} else {
		$url = 'js/';
	}
	$player_settings = array(
		'pluginPath'    => $url,
		'classPrefix'   => 'rp-', // 'mejs-'
		'stretching'    => 'responsive',
		'forceLive'		=> true,
	);
	if ( function_exists( 'apply_filters' ) ) {
		$player_settings = apply_filters( 'radio_player_mediaelement_settings', $player_settings );
	}
	if ( function_exists( 'wp_localize_script') ) {
		// --- localize script output ---
		wp_localize_script( 'rp-mediaelement', 'rpSettings', $player_settings );
	} else {
		// --- output script settings variable directly ---
		echo "var rpSettings; ";
		foreach ( $player_settings as $key => $value ) {
			if ( is_string( $value ) ) {
				echo "rpSettings[" . esc_js( $key ) . "] = '" . esc_js( $value ) . "'; ";
			} else {
				echo "rpSettings[" . esc_js( $key ) . "] = " . esc_js( $value ) . "; ";
			}
		}
	}
} */

// ----------------------------
// Dynamic Load Script via AJAX
// ----------------------------
/* 2.5.7: deprecate unused dynamic script load function
if ( function_exists( 'add_action' ) ) {
	add_action( 'wp_ajax_radio_player_script', 'radio_player_script' );
	add_action( 'wp_ajax_nopriv_radio_player_script', 'radio_player_script' );
} elseif ( isset( $_REQUEST['action'] ) && ( 'radio_player_script' == sanitize_text_field( $_REQUEST['action'] ) ) ) {
	radio_player_script();
}
function radio_player_script() {
	$script = sanitize_text_field( $_REQUEST['script'] );
	$js = '';
	if ( 'amplitude' == $script ) {
		$js .= radio_player_script_amplitude();
	} elseif ( 'jplayer' == $script ) {
		$js .= radio_player_script_jplayer();
	} elseif ( 'howler' == $script ) {
		$js .= radio_player_script_howler();
	} // elseif ( 'elements' == $script ) {
		// TODO: combine both media elements scripts
		// $js .= radio_player_script_mediaelements();
		// TODO: localize script settings ?
	// }
	else {
		exit;
	}

	if ( isset( $js ) ) {
		header( 'Content-type: application/javascript' );
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $js;
	}
	exit;
} */

// -------------------
// Get Player Settings
// -------------------
// 2.5.7: added echo argument for direct output
function radio_player_get_player_settings( $echo = false ) {

	global $radio_player;
	
	/* if ( isset( $radio_player['localized-script'] ) ) {
		return '';
	}
	$radio_player['localized-script'] = true; */
	
	$js = '';

	// ---- set AJAX URL ---
	$admin_ajax = '';
	if ( defined( 'RADIO_PLAYER_AJAX_URL' ) ) {
		$admin_ajax = RADIO_PLAYER_AJAX_URL;
	} elseif ( function_exists( 'admin_url' ) ) {
		$admin_ajax = admin_url( 'admin-ajax.php' );
	}

	// --- set save interval ---
	$save_interval = 60;
	if ( defined( 'RADIO_PLAYER_SAVE_INTERVAL' ) ) {
		$save_interval = RADIO_PLAYER_SAVE_INTERVAL;
	} elseif ( function_exists( 'apply_filters' ) ) {
		apply_filters( 'radio_station_player_save_interval', $save_interval );
		apply_filters( 'radio_player_save_interval', $save_interval );
	}
	$save_interval = abs( intval( $save_interval ) );
	if ( $save_interval < 1 ) {
		$save_interval = 60;
	}

	// --- set jPlayer Flash path ---
	// 2.5.7: disable swf fallback support
	/* if ( defined( 'RADIO_PLAYER_URL' ) ) {
		$swf_path = RADIO_PLAYER_URL . 'js';
	} elseif ( function_exists( 'plugins_url' ) ) {
		if ( defined( 'RADIO_STATION_FILE' ) ) {
			$swf_path = plugins_url( 'player/js', RADIO_STATION_FILE );
		} else {
			$swf_path = plugins_url( 'js', __FILE__ );
		}
	} elseif ( function_exists( 'apply_filters' ) ) {
		$swf_path = apply_filters( 'radio_station_player_jplayer_swf_path', '' );
		$swf_path = apply_filters( 'radio_player_jplayer_swf_path', $swf_path );
	} else {
		// TODO: check fallback to SWF (URL) relative path js/ ?
		$swf_path = '';
	} */

	// --- set default stream settings ---
	$player_script = radio_player_get_default_script();
	if ( function_exists( 'radio_station_get_setting' ) ) {

		// --- get player settings ---
		$player_script = radio_station_get_setting( 'player_script' );
		$player_title = radio_station_get_setting( 'player_title' );
		$player_image = radio_station_get_setting( 'player_image' );
		$player_volume = radio_station_get_setting( 'player_volume' );
		$player_single = radio_station_get_setting( 'player_single' );

	} else {

		// --- get player settings ---
		$player_title = '';
		$player_image = '';
		$player_volume = 77;
		$player_single = true;

		if ( function_exists( 'apply_filters' ) ) {
			// 2.5.6: fix to function typo apply_fitlers!
			$player_script = apply_filters( 'radio_station_player_script', $player_script );
			$player_script = apply_filters( 'radio_player_script', $player_script );
			$player_title = apply_filters( 'radio_station_player_title', $player_title );
			$player_title = apply_filters( 'radio_player_title', $player_title );
			$player_image = apply_filters( 'radio_station_player_image', $player_image );
			$player_image = apply_filters( 'radio_player_image', $player_image );
			$player_volume = abs( intval( apply_filters( 'radio_station_player_volume', $player_volume ) ) );
			$player_volume = abs( intval( apply_filters( 'radio_player_volume', $player_volume ) ) );
			$player_single = apply_filters( 'radio_station_player_single', $player_single );
			$player_single = apply_filters( 'radio_player_single', $player_single );
		}
	}
	
	// 2.4.0.3: move constant checks out
	if ( defined( 'RADIO_PLAYER_SCRIPT' ) ) {
		$player_script = RADIO_PLAYER_SCRIPT;
	}
	if ( defined( 'RADIO_PLAYER_TITLE' ) ) {
		$player_title = RADIO_PLAYER_TITLE;
	}
	if ( defined( 'RADIO_PLAYER_IMAGE' ) ) {
		$player_image = RADIO_PLAYER_IMAGE;
	}
	if ( defined( 'RADIO_PLAYER_VOLUME' ) ) {
		$player_volume = abs( intval( RADIO_PLAYER_VOLUME ) );
	}
	if ( defined( 'RADIO_PLAYER_SINGLE' ) ) {
		$player_single = RADIO_PLAYER_SINGLE;
	}

	// --- set script suffix ---
	// $suffix = '.min';
	// if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
	// 	$suffix = '';
	// }
	
	// 2.5.7: automatically disable Howler script (browser incompatibilities)
	$player_script = ( 'howler' == $player_script ) ? 'amplitude' : $player_script;

	// --- convert player behaviour settings to boolean string ---
	$player_single = $player_single ? 'true' : 'false';

	// 2.5.7: maybe buffer settings output
	if ( !$echo ) {
		ob_start();
	}

	// --- set radio player settings ---
	// 2.5.7: disable swf fallback support
	// 2.5.13: set swf_path to empty string to prevent jplayer error
	echo "player_settings = {";
		echo "'ajaxurl': '" . esc_url( $admin_ajax ) . "', ";
		echo "'saveinterval':" . esc_js( $save_interval ) . ", ";
		// echo "'swf_path': '" . esc_url( $swf_path ) . "', ";
		echo "'swf_path': '', ";
		echo "'script': '" . esc_js( $player_script ). "', ";
		echo "'title': '" . esc_js( $player_title ) . "', ";
		echo "'image': '" . esc_url( $player_image ) . "', ";
		echo "'singular': " . esc_js( $player_single ) . ", ";
		// echo "'suffix': '" . esc_js( $suffix ) . "', ";
	echo "}" . "\n";

	// --- maybe limit available scripts for testing purposes ---
	$valid_scripts = array( 'amplitude', 'howler', 'jplayer' );
	// 2.4.0.3: set single script override only
	// 2.5.0: fix to typo in $_REQUST['player-script']
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_REQUEST['player-script'] ) && in_array( sanitize_text_field( $_REQUEST['player-script'] ), $valid_scripts ) ) {
		// 2.4.0.3: only allow admin to override script for testing purposes
		if ( function_exists( 'current_user_can' ) && current_user_can( 'manage_options' ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$player_script = sanitize_text_field( wp_unslash( $_REQUEST['player-script'] ) );
		}
	}
	$scripts = array( $player_script );

	// --- set script URL ouput ---
	// 2.4.0.3: check for fallback script settings
	// 2.5.7: remove howler from script fallback list
	$fallbacks = array( 'jplayer', 'amplitude' ); // 'howler'
	if ( function_exists( 'radio_station_get_setting' ) ) {
		$fallbacks = radio_station_get_setting( 'player_fallbacks' );
	} elseif ( function_exists( 'apply_filters' ) ) {
		$fallbacks = apply_filters( 'radio_station_player_fallbacks', $fallbacks );
		$fallbacks = apply_filters( 'radio_player_fallbacks', $fallbacks );
	}
	if ( defined( 'RADIO_PLAYER_FALLBACKS' ) ) {
		$fallbacks = explode( ',', RADIO_PLAYER_FALLBACKS );
	}
	// 2.4.0.3: allow for admin-only fallback script override
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_REQUEST['fallback-scripts'] ) ) {
		if ( function_exists( 'current_user_can' ) && current_user_can( 'manage_options' ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$fallback_scripts = explode( ',', sanitize_text_field( wp_unslash( $_REQUEST['fallback-scripts'] ) ) );
			if ( count( $fallback_scripts ) > 0 ) {
				foreach ( $fallback_scripts as $i => $fallback_script ) {
					if ( !in_array( $fallback_script, $valid_scripts ) ) {
						unset( $fallback_scripts[$i] );
					}
				}
			}
			if ( count( $fallback_scripts ) > 0 ) {
				$fallbacks = $fallback_scripts;
			}
		}
	}

	// 2.4.0.3: merge fallbacks with current script
	if ( is_array( $fallbacks ) && ( count( $fallbacks ) > 0 ) ) {
		$scripts = array_merge( $scripts, $fallbacks );
	}
	echo "scripts = {";
		if ( in_array( 'amplitude', $scripts ) ) {
			echo "'amplitude': '" . esc_url( $radio_player['amplitude_script']['url'] ) . '?version=' . esc_js( $radio_player['amplitude_script']['version'] ) . "', ";
		}
		if ( in_array( 'howler', $scripts ) ) {
			echo "'howler': '" . esc_url( $radio_player['howler_script']['url'] ) . '?version=' . esc_js( $radio_player['howler_script']['version'] ) . "', ";
		}
		if ( in_array( 'jplayer', $scripts ) ) {
			echo "'jplayer': '" . esc_url( $radio_player['jplayer_script']['url'] ) . '?version=' . esc_js( $radio_player['jplayer_script']['version'] ) . "', ";
		}
		// echo "'media': '" . $radio_player['media_script']['url'] . '?version=' . $radio_player['media_script']['version'] . "', "
		// echo "'elements': '" . $radio_player['elements_script']['url'] . '?version=' . $radio_player['elements_script']['version'] . "', ";
	echo "}" . "\n";

	// --- set player script supported formats ---
	// TODO: recheck supported amplitude formats ?
	// [JPlayer] Audio: mp3, m4a - Video: m4v
	// +Audio: webma, oga, wav, fla, rtmpa +Video: webmv, ogv, flv, rtmpv
	// [Howler] mp3, opus, ogg, wav, aac, m4a, mp4, webm
	// +mpeg, oga, caf, weba, webm, dolby, flac
	// [Amplitude] HTML5 Support - mp3, aac ...?
	// ref: https://en.wikipedia.org/wiki/HTML5_audio#Supporting_browsers
	// [Media Elements] Audio: mp3, wma, wav +Video: mp4, ogg, webm, wmv
	// 2.5.7: disable Howler format list
	echo "formats = {";
		echo "'amplitude': ['mp3','aac'], ";
		echo "'jplayer': ['mp3','m4a','webm','oga','rtmpa','wav','flac'], ";
		echo "'howler': ['mp3','opus','ogg','oga','wav','aac','m4a','mp4','webm','weba','flac'], ";
		// $js .= "'mediaelements': ['mp3','wma','wav'], ";
	echo "}" . "\n";

	// --- set debug mode ---
	// 2.5.10: moved conditions to radio_player_set_debug_mode
	$debug = RADIO_PLAYER_DEBUG ? 'true' : 'false';

	// 2.5.6: added explicit touchscreen detection setting
	echo "matchmedia = window.matchMedia || window.msMatchMedia;" . "\n";
	echo "touchscreen = !matchmedia('(any-pointer: fine)').matches;" . "\n";

	// --- set radio player settings and radio data objects ---
	// (with empty arrays for instances, script types, failbacks, audio targets and stream data)
	echo "var radio_player = {settings:player_settings, scripts:scripts, formats:formats, loading:false, touchscreen:touchscreen, debug:" . esc_js( $debug ) . "}" . "\n";
	echo "var radio_player_data = {state:{}, players:[], scripts:[], failed:[], data:[], types:[], channels:[], faders:[]}" . "\n";

	// --- logged in / user state settings ---
	$loggedin = 'false';
	if ( function_exists( 'is_user_logged_in' ) && is_user_logged_in() ) {
		$loggedin = 'true';
		$user_id = get_current_user_id();
		$state = get_user_meta( $user_id, 'radio_player_state', true );
	}
	echo "radio_player_data.state.loggedin = " . esc_js( $loggedin ) . ";" . "\n";

	// ---- maybe set play state ---
	// 2.5.0: set playing variable in single line
	$playing = ( isset( $state ) && isset( $state['playing'] ) && $state['playing'] ) ? 'true' : 'false';
	echo "radio_player_data.state.playing = " . esc_js( $playing ) . "; " . "\n";

	// --- maybe set station ID ---
	if ( isset( $state ) && isset( $state['station'] ) ) {
		$station = abs( intval( $state['station'] ) );
	}
	if ( isset( $station ) && $station && ( $station > 0 ) ) {
		echo "radio_player_data.state.station = " . esc_js( $station ) . ";" . "\n";
	} else {
		echo "radio_player_data.state.station = 0;" . "\n";
	}

	// --- maybe set user volume ---
	// note: default already set above
	if ( isset( $state ) && isset( $state['volume'] ) ) {
		$player_volume = abs( intval( $state['volume'] ) );
	}
	echo "radio_player_data.state.volume = " . esc_js( $player_volume ) . "; " . "\n";

	// --- maybe set user mute ---
	$player_mute = 'false';
	if ( isset( $state ) && isset( $state['mute'] ) && ( $state['mute'] ) ) {
		$player_mute = 'true';
	}
	echo "radio_player_data.state.mute = " . esc_js( $player_mute ) . "; " . "\n";

	// --- set main radio stream data ---
	echo "radio_player_data.state.data = {};" . "\n";
	if ( function_exists( 'apply_filters' ) ) {
		$station = ( isset( $state['station'] ) ) ? $state['station'] : 0;
		// note: this is the main stream data filter hooked into by Radio Station plugin
		// 2.4.0.3: fix for uninitialized string offset
		$data = apply_filters( 'radio_station_player_data', false, $station );
		$data = apply_filters( 'radio_player_data', false, $station );
	}
	// 2.5.6: add isset check for data variable
	if ( isset( $data ) && $data && is_array( $data ) ) {
		
		// 2.5.7: set currently playing data only if script supported
		$set_data = false;
		foreach ( $data as $key => $value ) {
			if ( ( 'script' == $key ) && in_array( $value, array( 'jplayer', 'amplitude', 'howler' ) ) ) {
				$set_data = true;
			}
		}
		if ( $set_data ) {
			foreach ( $data as $key => $value ) {
				// TODO: attempt auto-fix for secure https stream ?
				/* if ( ( 'url' == $key ) || ( 'fallback' == $key ) ) {
					if ( is_ssl() && strstr( $value, 'http://' ) ) {
						$value = str_replace( 'http://', 'https://', $value );
					}
				} */
				echo "radio_player_data.state.data['" . esc_js( $key ) . "'] = '" . esc_js( $value ) . "';" . "\n";
			}
		}
		echo "radio_player.stream_data = radio_player_data.state.data;" . "\n";
	}

	// --- attempt to set player state from cookies ---
	echo "var radio_player_state_loaded = false;
	var radio_player_state_loader = setInterval(function() {
		if (!radio_player_state_loaded && (typeof radio_player_load_state != 'undefined')) {
			radio_player_load_state(); radio_player_state_loaded = true;
			radio_player_custom_event('rp-state-loaded', false);			
			clearInterval(radio_player_state_loader);
		}
	}, 1000);" . "\n";

	// --- periodic save to user meta ---
	echo "radio_player_save_interval = radio_player.settings.saveinterval * 1000;
	var radio_player_state_saver = setInterval(function() {
		if (typeof radio_player_data.state != 'undefined') {
			if (!radio_player_data.state.loggedin) {clearInterval(radio_player_state_saver); return;}
			radio_player_save_user_state();
		}
	}, radio_player_save_interval);" . "\n";

	// 2.5.7: maybe return output buffer if not echoing
	if ( !$echo ) {
		$js = ob_get_contents();
		ob_get_clean();
		return $js;
	}
}

// -----------------
// User State Iframe
// -----------------
// 2.5.8: removed iframe in favour of using jquery get
// note: only triggered for WordPress logged in users
// function radio_player_iframe() {
	// echo '<span style="display:none;">FRAME TEST</span>';
	// if ( function_exists( 'is_user_logged_in') && is_user_logged_in() ) {
		// echo "<iframe src='about:blank' id='radio-player-state-iframe' name='radio-player-state-iframe' style='display:none;'></iframe>" . "\n";
	// }
// }

// -----------------------
// AJAX: Update User State
// -----------------------
// note: only triggered for WordPress logged in users
if ( function_exists( 'add_action' ) ) {
	add_action( 'wp_ajax_radio_player_state', 'radio_player_state' );
	// note: non-logged in user action still added to prevent 400 bad request
	add_action( 'wp_ajax_nopriv_radio_player_state', 'radio_player_state' );
}
function radio_player_state() {

	// --- reset saving state in parent frame ---
	// 2.5.8: not necessary to do this here
	// echo "parent.radio_player_data.state.saving = false;" . "\n";

	if ( !function_exists( 'get_current_user_id' ) || !function_exists( 'update_user_meta' ) ) {
		exit;
	}

	// --- get current user ID ---
	$user_id = get_current_user_id();
	if ( 0 == $user_id ) {
		exit;
	}

	// --- get user state values ---
	// 2.5.9.4: combined with value sanitizing
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$playing = isset( $_REQUEST['playing'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['playing'] ) ) : false;
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$volume = isset( $_REQUEST['volume'] ) ? absint( wp_unslash( $_REQUEST['volume'] ) ) : 77;
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$station = isset( $_REQUEST['station'] ) ? absint( wp_unslash( $_REQUEST['station'] ) ) : false;
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$mute = isset( $_REQUEST['mute'] ) ? (bool) sanitize_text_field( wp_unslash( $_REQUEST['mute'] ) ) : false;

	// --- check user state values ---
	if ( $volume < 1 ) {
		$volume = false;
	} elseif ( $volume > 100 ) {
		$volume = 100;
	}
	if ( $station < 1 ) {
		$station = false;
	}

	// --- save player state to user meta ---
	$state = array(
		'playing'	=> $playing,
		'volume'	=> $volume,
		'station'	=> $station,
		'mute'		=> $mute,
	);
	update_user_meta( $user_id, 'radio_player_state', $state );

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended	
	// if ( isset( $_REQUEST['debug'] ) && ( '1' == sanitize_text_field( wp_unslash( $_REQUEST['debug'] ) ) ) ) {
	//	echo 'User state saved.';
	// }

	exit;
}

// -----------------------
// Load Amplitude Function
// -----------------------
// "mp3" "aac" ... (+HTML5 Browser Supported Sources)
function radio_player_script_amplitude() {

	// $method = 'callbacks';
	$method = 'listeners';

	// --- load amplitude player ---
	$js = "function radio_player_amplitude(instance, url, format, fallback, fformat) {

		player_id = 'radio_player_'+instance;
		container_id = 'radio_container_'+instance;
		if (url == '') {url = radio_player.settings.url;}
		if (url == '') {return;}
		if (!format || (format == '')) {format = 'aac';}
		if (fallback == '') {fallback = radio_player.settings.fallback;}
		if (!fallback || !fformat || (fformat == '')) {fallback = ''; fformat = '';}

		/* check if already loaded with same source 
		if ( (typeof radio_player_data.scripts[instance] != 'undefined') && (radio_player_data.scripts[instance] == 'amplitude')
		  && (typeof radio_player.previous_data != 'undefined') ) {
			pdata = radio_player.previous_data;
			if ( (pdata.url == url) && (pdata.format == format) && (pdata.fallback == fallback) && (pdata.fformat == fformat) ) {
				if (radio_player.debug) {console.log('Player already loaded with this stream data.');}
				return radio_player_data.players[instance];
			}
		} */

		/* set song streams */
		songs = new Array();
		songs[0] = {'name': '',	'artist': '', 'album': '', 'url': url, 'cover_art_url': '',	'live': true};
		/* if ('' != fallback) {songs[1] = {'name': '', 'artist': '', 'album': '', 'url': fallback, 'cover_art_url': '', 'live': true};} */

		/* set volume */
		if (jQuery('#'+container_id+' .rp-volume-slider').hasClass('changed')) {
			volume = jQuery('#'+container_id+' .rp-volume-slider').val();
		} else if (typeof radio_player_data.state.volume != 'undefined') {volume = radio_player_data.state.volume;}
		else {volume = radio_player.settings.volume;}
		radio_player_volume_slider(instance, volume);
		if (radio_player.debug) {console.log('Amplitude init Volume: '+volume);}

		/* initialize player */
		if (radio_player.debug) {console.log('Init Amplitude: '+instance+' : '+url+' : '+format+' : '+fallback+' : '+fformat);}
		radio_player_instance = Amplitude;
		radio_player_instance.init({
			'debug': radio_player.debug,
			'songs': songs,
			'volume': volume,
			'volume_increment': 5,
			'volume_decrement': 5,
			'continue_next': false,
			'preload': 'none'," . "\n";

		// 2.5.0: callbacks disabled as no event is being passed to get player instance
		if ( 'callbacks' == $method ) {
			
			$js .= "'callbacks': {
				
				/* amp 5.0.3 bug: initialized callback not firing! */
				/* amp 5.3.2 initialized callback is now firing */
				/* 'initialized': function(e) {
					radio_player.loading = false;
					instance = radio_player_event_instance(e, 'Loaded', 'amplitude');
					radio_player_event_handler('loaded', {instance:instance, script:'amplitude'});

					channel = radio_player_data.channels[instance];
					if (channel) {radio_player_set_state('channel', channel);}
					station = jQuery('#radio_player_'+instance).attr('station-id');
					if (station) {radio_player_set_state('station', station);}
				}, */
				/* amp 5.0.3 bug: play callback event is not being triggered */
				/* amp 5.3.2: play callback is now being triggered */
				'play': function(e) {
					radio_player.loading = false;
					instance = radio_player_event_instance(e, 'Playing', 'amplitude');
					radio_player_event_handler('playing', {instance:instance, script:'amplitude'});
					radio_player_pause_others(instance);
				},
				/* bug: pause callback is still not firing */
				'pause': function(e) {
					instance = radio_player_event_instance(e, 'Paused', 'amplitude');
					radio_player_event_handler('paused', {instance:instance, script:'amplitude'});
				},
				'stop': function(e) {
					instance = radio_player_event_instance(e, 'Stopped', 'amplitude');
					radio_player_event_handler('stopped', {instance:instance, script:'amplitude'});
				},
				'volumechange': function(e) {
					instance = radio_player_event_instance(e, 'Volume', 'amplitude');
					if (instance && (radio_player_data.scripts[instance] == 'amplitude')) {
						volume = radio_player_data.players[instance].getConfig().volume;
						radio_player_player_volume(instance, 'amplitude', volume);
					}
				},
				/* 'error': function(e) {
					console.log('Amplitude Error'); console.log(e);
					instance = radio_player_event_instance(e, 'Error', 'amplitude');
					if (radio_player.debug) {console.log(e);}
					radio_player_event_handler('error', {instance:instance, script:'amplitude'});
					radio_player_player_fallback(instance, 'amplitude', 'Amplitude Error');
				}, */
			}";
		}

	$js .= "});
		radio_player_data.players[instance] = radio_player_instance;
		radio_player_data.scripts[instance] = 'amplitude';

		/* set instance on audio source */
		audio = radio_player_instance.getAudio();
		if (radio_player.debug) {console.log('Amplitude Audio Element:'); console.log(audio);}
		audio.setAttribute('instance-id', instance);

		/* amp 5.0.3 bind loaded to canplay event (as initialized callback not firing!) */
		/* amp 5.3.2 initialized callback is now firing */
		audio.addEventListener('canplay', function(e) {
			radio_player.loading = false;
			instance = radio_player_event_instance(e, 'Loaded', 'amplitude');
			radio_player_event_handler('loaded', {instance:instance, script:'amplitude'});

			channel = radio_player_data.channels[instance];
			if (channel) {radio_player_set_state('channel', channel);}
			station = jQuery('#radio_player_'+instance).attr('station-id');
			if (station) {radio_player_set_state('station', station);}
		}, false);" . "\n";

	if ( 'listeners' == $method ) {
		$js .= "/* amp 5.0.3: bind play(ing) event (as play callback not firing!) */
		/* amp 5.3.2: play callback is now firing */
		audio.addEventListener('playing', function(e) {
			radio_player.loading = false;
			instance = radio_player_event_instance(e, 'Playing', 'amplitude');
			radio_player_event_handler('playing', {instance:instance, script:'amplitude'});
			radio_player_pause_others(instance);
		}, false);

		/* bind volume change event */
		audio.addEventListener('volumechange', function(e) {
			instance = radio_player_event_instance(e, 'Volume', 'amplitude');
			if (instance && (radio_player_data.scripts[instance] == 'amplitude')) {
				volume = radio_player_data.players[instance].getConfig().volume;
				radio_player_player_volume(instance, 'amplitude', volume);
			}
		}, false);
		
		/* bind error event (as event not being passed in callback) */
		audio.addEventListener('error', function(e) {
			instance = radio_player_event_instance(e, 'Error', 'amplitude');
			if (radio_player.debug) {console.log(e);}
			radio_player_event_handler('error', {instance:instance, script:'amplitude'});
			radio_player_player_fallback(instance, 'amplitude', 'Amplitude Error');
		}, false);
		
		/* listen for pause event */
		document.addEventListener('rp-pause', function(e) {
			instance = e.detail.instance;
			if (radio_player_data.scripts[instance] == 'amplitude') {
				radio_player_event_handler('paused', {instance:instance, script:'amplitude'});
			}
		}, false);
		
		/* listen for stop event */
		document.addEventListener('rp-stop', function(e) {
			instance = e.detail.instance;
			if (radio_player_data.scripts[instance] == 'amplitude') {
				radio_player_event_handler('stopped', {instance:instance, script:'amplitude'});
			}
		}, false);" . "\n";
	}

	$js .= "/* match script select dropdown value */
		if (jQuery('#'+container_id+' .rp-script-select').length) {
			jQuery('#'+container_id+' .rp-script-select').val('amplitude');
		}

		return radio_player_instance;
	}";

	// TODO: maybe set continue_next to true to use for fallback URL ?
	// TODO: recheck repeat off setting: 'repeat': false, ?

	/* ref: https://521dimensions.com/open-source/amplitudejs/docs/configuration/
	'station_art_url': stationartwork,
	'default_album_art': defaultartwork, 
	'soundcloud_client': soundcloudkey,
	'debug': debug,
	'start_song': currentindex,
	'dynamic_mode': dynamic,
	'use_visualizations': visualizations,
	'visualization_backup': 'nothing',
	*/

	// --- filter and return ---
	if ( function_exists( 'apply_filters' ) ) {
		$js = apply_filters( 'radio_station_player_script_amplitude', $js );
		$js = apply_filters( 'radio_player_script_amplitude', $js );
	}
	return $js;
}

// --------------------
// Load Howler Function
// --------------------
// Howler Note: "A live stream can only be played through HTML5 Audio."
// "mp3", "opus", "ogg", "wav", "aac", "m4a", "mp4", "webm"
function radio_player_script_howler() {

	// --- load howler player ---
	$js = "function radio_player_howler(instance, url, format, fallback, fformat) {

		player_id = 'radio_player_'+instance;
		container_id = 'radio_container_'+instance;
		if (url == '') {url = radio_player.settings.url;}
		if (url == '') {return;}
		if (!format || (format == '')) {format = 'aac';}
		if (fallback == '') {fallback = radio_player.settings.fallback;}
		if (!fallback || !fformat || (fformat == '')) {fallback = ''; fformat = '';}

		/* check if already loaded with same source
		if ( (typeof radio_player_data.scripts[instance] != 'undefined') && (radio_player_data.scripts[instance] == 'howler')
		  && (typeof radio_player.previous_data != 'undefined') ) {
			pdata = radio_player.previous_data;
			if ( (pdata.url == url) && (pdata.format == format) && (pdata.fallback == fallback) && (pdata.fformat == fformat) ) {
				if (radio_player.debug) {console.log('Player already loaded with this stream data.');}
				return radio_player_data.players[instance];
			}
		} */

		/* set sources */
		sources = new Array(); formats = new Array();
		sources[0] = url; /* if (fallback != '') {sources[1] = fallback;} */
		formats[0] = format; /* if ((fallback != '') && (fformat != '')) {formats[1] = fformat;} */

		/* set volume */
		if (jQuery('#'+container_id+' .rp-volume-slider').hasClass('changed')) {
			volume = jQuery('#'+container_id+' .rp-volume-slider').val();
		} else if (typeof radio_player_data.state.volume != 'undefined') {volume = radio_player_data.state.volume;}
		else {volume = radio_player.settings.volume;}
		radio_player_volume_slider(instance, volume);
		volume = parseFloat(volume / 100);
		if (radio_player.debug) {console.log('Howler init Volume: '+volume);}

		/* intialize player */
		if (radio_player.debug) {console.log('Init Howler: '+instance+' : '+url+' : '+format+' : '+fallback+' : '+fformat);}
		radio_player_instance = new Howl({
			src: sources,
			format: formats,
			html5: false,
			autoplay: false,
			preload: false,
			volume: volume,
			onload: function(e) {
				/* possible bug: maybe not always being triggered ? */
				radio_player.loading = false;
				instance = radio_player_match_instance(this, e, 'howler');
				radio_player_event_handler('loaded', {instance:instance, script:'howler'});

				channel = radio_player_data.channels[instance];
				if (channel) {radio_player_set_state('channel', channel);}
				station = jQuery('#radio_player_'+instance).attr('station-id');
				if (station) {radio_player_set_state('station', station);}
			},
			onplay: function(e) {
				radio_player.loading = false;
				instance = radio_player_match_instance(this, e, 'howler');
				radio_player_event_handler('playing', {instance:instance, script:'howler'});
				radio_player_pause_others(instance);
			},
			onpause: function(e) {
				instance = radio_player_match_instance(this, e, 'howler');
				radio_player_event_handler('paused', {instance:instance, script:'howler'});
			},
			onstop: function(e) {
				instance = radio_player_match_instance(this, e, 'howler');
				radio_player_event_handler('stopped', {instance:instance, script:'howler'});
			},
			onvolume: function(e) {
				instance = radio_player_match_instance(this, e, 'howler');
				if (instance && (radio_player_data.scripts[instance] == 'howler')) {
					volume = this.volume() * 100;
					if (volume > 100) {volume = 100;}
					radio_player_player_volume(instance, 'howler', volume);
				}
			},
			onloaderror: function(id,e) {
				instance = radio_player_match_instance(this, e, 'howler');
				radio_player_event_handler('error', {instance:instance, script:'howler'});
				if (radio_player.debug) {console.log('Load Error, Howler Instance: '+instance+', Sound ID: '+id);}
				radio_player_player_fallback(instance, 'howler', 'Howler Load Error');
			},
			onplayerror: function(id,e) {
				instance = radio_player_match_instance(this, e, 'howler');
				radio_player_event_handler('error', {instance:instance, script:'howler'});
				if (radio_player.debug) {console.log('Play Error, Howler Instance: '+instance+', Sound ID: '+id);}
				radio_player_player_fallback(instance, 'howler', 'Howler Play Error');
			},
		});
		radio_player_data.players[instance] = radio_player_instance;
		radio_player_data.scripts[instance] = 'howler';

		/* match script select dropdown value */
		if (jQuery('#'+container_id+' .rp-script-select').length) {
			jQuery('#'+container_id+' .rp-script-select').val('howler');
		}

		return radio_player_instance;
	}";

	// --- filter and return ---
	if ( function_exists( 'apply_filters' ) ) {
		$js = apply_filters( 'radio_station_player_script_howler', $js );
		$js = apply_filters( 'radio_player_script_howler', $js );
	}
	return $js;
}

// ---------------------
// Load JPlayer Function
// ---------------------
// ref: http://www.jplayer.org/latest/developer-guide/
// Audio: mp3 / m4a, Video: m4v
// Extra formats:
// audio: webma, oga, wav, fla, rtmpa
// video: webmv, ogv, flv, rtmpv
function radio_player_script_jplayer() {

	// --- load jplayer ---
	$js = "function radio_player_jplayer(instance, url, format, fallback, fformat) {

		player_id = 'radio_player_'+instance;
		container_id = 'radio_container_'+instance;
		if (url == '') {url = radio_player.settings.url;}
		if (url == '') {return;}
		if (!format || (format == '') || (format == 'aac')) {format = 'm4a';}
		if (fallback == '') {fallback = radio_player.settings.fallback;}
		if (!fallback || !fformat || (fformat == '')) {fallback = ''; fformat = '';}
		if (fformat == 'aac') {fformat = 'm4a';}

		/* check if already loaded with same source
		if ( (typeof radio_player_data.scripts[instance] != 'undefined') && (radio_player_data.scripts[instance] == 'jplayer')
		  && (typeof radio_player.previous_data != 'undefined') ) {
			pdata = radio_player.previous_data;
			if ( (pdata.url == url) && (pdata.format == format) && (pdata.fallback == fallback) && (pdata.fformat == fformat) ) {
				if (radio_player.debug) {console.log('Player already loaded with this stream data.');}
				return radio_player_data.players[instance];
			}
		} */

		/* set volume */
		if (jQuery('#'+container_id+' .rp-volume-slider').hasClass('changed')) {
			volume = jQuery('#'+container_id+' .rp-volume-slider').val();
		} else if (typeof radio_player_data.state.volume != 'undefined') {volume = radio_player_data.state.volume;}
		else {volume = radio_player.settings.volume;}
		radio_player_volume_slider(instance, volume);
		volume = parseFloat(volume / 100);
		if (radio_player.debug) {console.log('jPlayer init Volume: '+volume);}

		media = {}; /* media.title = ''; */ media[format] = url; supplied = format;
		/* if (fallback && fformat) {media[fformat] = fallback; supplied += ', '+fformat;} */
		radio_player.jplayer_media = media;
		console.log(radio_player.jplayer_media);
		radio_player.jplayer_ready = false;

		/* load jplayer */
		if (radio_player.debug) {console.log('Init jPlayer: '+instance+' : '+url+' : '+format+' : '+fallback+' : '+fformat);}
		radio_player_instance = jQuery('#'+player_id).jPlayer({
			ready: function () {
				console.log('jPlayer Ready.');
				console.log(radio_player.jplayer_media);
				jQuery(this).jPlayer('setMedia', radio_player.jplayer_media);
				radio_player.jplayer_ready = true;
			},
			supplied: supplied,
			cssSelectorAncestor: '#'+container_id,
			swfPath: radio_player.settings.swf_path,
			idPrefix: 'rp',
			preload: 'none',
			volume: volume,
			globalVolume: true,
			useStateClassSkin: true,
			autoBlur: false,
			smoothPlayBar: true,
			keyEnabled: true,
			remainingDuration: false,
			toggleDuration: false,
			backgroundColor: 'transparent',
			/* cssSelector: {
				videoPlay: '.rp-video-play',
				play: '.rp-play',
				pause: '.rp-pause',
				stop: '.rp-stop',
				seekBar: '.rp-seek-bar',
				playBar: '.rp-play-bar',
				mute: '.rp-mute',
				unmute: '.rp-unmute',
				volumeBar: '.rp-volume-bar',
				volumeBarValue: '.rp-volume-bar-value',
				volumeMax: '.rp-volume-max',
				playbackRateBar: '.rp-playback-rate-bar',
				playbackRateBarValue: '.rp-playback-rate-bar-value',
				currentTime: '.rp-current-time',
				duration: '.rp-duration',
				title: '.rp-title',
				fullScreen: '.rp-full-screen',
				restoreScreen: '.rp-restore-screen',
				repeat: '.rp-repeat',
				repeatOff: '.rp-repeat-off',
				gui: '.rp-gui',
				noSolution: '.rp-no-solution'
			},
			stateClass: {
			  playing: 'rp-state-playing',
			  seeking: 'rp-state-seeking',
			  muted: 'rp-state-muted',
			  looped: 'rp-state-looped',
			  fullScreen: 'rp-state-full-screen',
			  noVolume: 'rp-state-no-volume'
			}, */
		});
		radio_player_data.players[instance] = radio_player_instance;
		radio_player_data.scripts[instance] = 'jplayer';

		/* bind load event */
		jQuery('#'+player_id).bind(jQuery.jPlayer.event.load, function(e) {
			radio_player.loading = false;
			instance = radio_player_event_instance(e, 'Loaded', 'jplayer');
			radio_player_event_handler('loaded', {instance:instance, script:'jplayer'});

			channel = radio_player_data.channels[instance];
			if (channel) {radio_player_set_state('channel', channel);}
			/* station = jQuery('#radio_player_'+instance).attr('station-id');
			if (station) {radio_player_set_state('station', station);} */
		});

		/* bind play event */
		jQuery('#'+player_id).bind(jQuery.jPlayer.event.play, function(e) {
			radio_player.loading = false;
			instance = radio_player_event_instance(e, 'Playing', 'jplayer');
			radio_player_event_handler('playing', {instance:instance, script:'jplayer'});
			radio_player_pause_others(instance);
		});

		/* bind pause and stop events */
		jQuery('#'+player_id).bind(jQuery.jPlayer.event.pause, function(e) {
			instance = radio_player_event_instance(e, 'Paused', 'jplayer');
			radio_player_event_handler('paused', {instance:instance, script:'jplayer'});
		});
		jQuery('#'+player_id).bind(jQuery.jPlayer.event.stop, function(e) {
			instance = radio_player_event_instance(e, 'Stopped', 'jplayer');
			radio_player_event_handler('stopped', {instance:instance, script:'jplayer'});
		});

		/* bind volume change event */
		jQuery('#'+player_id).bind(jQuery.jPlayer.event.volumechange, function(e) {
			instance = radio_player_event_instance(e, 'Volume', 'jplayer');
			if (instance && (radio_player_data.scripts[instance] == 'jplayer')) {
				radio_player_player_volume(instance, 'jplayer', volume);
			}
		});

		/* bind can play debug message */
		jQuery('#'+player_id).bind(jQuery.jPlayer.event.canplay, function(e) {
			instance = radio_player_event_instance(e, 'CanPlay', 'jplayer');
			console.log('jPlayer Instance '+instance+' Can Play');
		});

		/* bind player error event to fallback scripts */
		jQuery('#'+player_id).bind(jQuery.jPlayer.event.error, function(e) {
			radio_player.jplayer_ready = false;
			instance = radio_player_event_instance(e, 'Error', 'jplayer');
			radio_player_event_handler('error', {instance:instance, script:'jplayer'});
			radio_player_player_fallback(instance, 'jplayer', 'jPlayer Error');
		});

		/* match script select dropdown value */
		if (jQuery('#'+container_id+' .rp-script-select').length) {
			jQuery('#'+container_id+' .rp-script-select').val('jplayer');
		}

		return radio_player_instance;
    }";

	// --- filter and return ---
	if ( function_exists( 'apply_filters' ) ) {
		$js = apply_filters( 'radio_station_player_script_jplayer', $js );
		$js = apply_filters( 'radio_player_script_jplayer', $js );
	}
	return $js;
}

// ---------------------------
// Load Media Element Function
// ---------------------------
// Usage ref: https://github.com/mediaelement/mediaelement/blob/master/docs/usage.md
// API ref: https://github.com/mediaelement/mediaelement/blob/master/docs/api.md
// Audio support: MP3, WMA, WAV
// Video support: MP4, Ogg, WebM, WMV
function radio_player_script_mediaelements() {

	// --- load media elements ---
	$js = "function radio_player_mediaelements(instance, url, format, fallback, fformat) {

		if (url == '') {url = radio_player.settings.url;}
		if (!format || (format == '')) {format = 'mp3';}
		if (fallback == '') {fallback = radio_player.settings.fallback;}
		if (!fallback || !fformat || (fformat == '')) {fallback = ''; fformat = '';}

		radio_player_data.scripts[instance] = 'mediaelements';
		player_id = 'radio_player_'+instance;
		container_id = 'radio_container_'+instance;

		/* load media element player */
		/* TODO: only initialize on new media elements? */
		radio_player_instance = jQuery('#'+player_id).mediaelementplayer(rpSettings);
		radio_player_data.players[instance] = radio_player_instance;

		// radio_player.instance = jQuery('.rp-audio').not('.rp-container').filter(function () {
		// 	return !jQuery(this).parent().hasClass('rp-mediaelement');	})
		// .mediaelementplayer(rpSettings);

		/* TODO: bind to play, stop/pause and volumechange events */

		/* bind to play event */
		/* 'play' */
		/* TODO: get this player instance ID
		/* TODO: maybe pause existing player */
		/* if (typeof window.top.current_radio == 'object') {
			radio_player_pause_current();
		} */
		/* TODO: send pause message to other windows */
		/* radio_player_pause_others(instance); */

		/* bind to pause event */
		/* 'pause' */

		/* bind to stop event */
		/* 'ended' */

		/* bind to volume change event */
		/* 'volumechange'
		/* volume = radio_player.instance.getVolume(); */

		/* TODO: bind cannot play event to fallback format? */
	}";

	// ? add media element events ?
	// ref: https://stackoverflow.com/questions/23114963/mediaelement-js-trigger-event-if-some-specific-audio-file-has-ended

    /* maybe add container class ? */
    /* ref: https://www.cedaro.com/blog/customizing-mediaelement-wordpress/
	/* (function() {
		var settings = window._wpmejsSettings || {};
		settings.features = settings.features || mejs.MepDefaults.features;
		settings.features.push( 'exampleclass' );

		MediaElementPlayer.prototype.buildexampleclass = function( player ) {
			player.container.addClass( 'example-mejs-container' );
		};
	})(); */

	// --- filter and return ---
	if ( function_exists( 'apply_filters' ) ) {
		$js = apply_filters( 'radio_station_player_script_mediaelements', $js );
		$js = apply_filters( 'radio_player_script_mediaelements', $js );
	}
	return $js;
}

// -------------------------
// Get Default Player Script
// -------------------------
function radio_player_get_default_script() {

	$script = 'amplitude'; // default
	if ( defined( 'RADIO_PLAYER_SCRIPT' ) ) {
		$script = RADIO_PLAYER_SCRIPT;
	} elseif ( function_exists( 'radio_station_get_setting' ) ) {
		$script = radio_station_get_setting( 'player_script' );
	}
	if ( function_exists( 'apply_filters' ) ) {
		$script = apply_filters( 'radio_station_player_script', $script );
		$script = apply_filters( 'radio_player_script', $script );
	}
	if ( defined( 'RADIO_PLAYER_FORCE_SCRIPT' ) ) {
		$script = RADIO_PLAYER_FORCE_SCRIPT;
	}
	return $script;
}

// ---------------------
// Enqueue Player Styles
// ---------------------
function radio_player_enqueue_styles( $script = false, $skin = false ) {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$suffix = ''; // DEV TEMP

	// --- get default if not passed by shortcode attribute ---
	if ( !$script ) {
		radio_player_get_default_script();
	}

	// --- get default if not passed by shortcode attribute ---
	/* if ( !$skin ) {

		// --- get skin settings ---
		$skin = 'blue-monday'; // default
		if ( defined( 'RADIO_PLAYER_SKIN' ) ) {
			$skin = RADIO_PLAYER_SKIN;
		}
		if ( function_exists( 'radio_station_get_setting' ) ) {
			$skin = radio_station_get_setting( 'player_skin' );
		} elseif ( function_exists( 'apply_filters' ) ) {
			$skin = apply_filters( 'radio_player_skin', $skin );
		}
		if ( defined( 'RADIO_PLAYER_FORCE_SKIN' ) ) {
			$skin = RADIO_PLAYER_FORCE_SKIN;
		}
	} */

	// --- debug script / skin used ---
	if ( RADIO_PLAYER_DEBUG ) {
		echo '<span style="display:none;">Script: ' . esc_html( $script ) . ' - Skin: ' . esc_html( $skin ) . '</span>' . "\n";
	}

	// --- enqueue base player styles ---
	$suffix = ''; // DEV TEMP

	if ( defined( 'RADIO_STATION_DIR' ) ) {
		$path = RADIO_STATION_DIR . '/player/css/radio-player' . $suffix . '.css';
	} elseif ( defined( 'RADIO_PLAYER_DIR' ) ) {
		$path = RADIO_PLAYER_DIR . '/css/radio-player' . $suffix . '.css';
	} else {
		$path = dirname( __FILE__ ) . '/css/radio-player' . $suffix . '.css';
	}
	if ( defined( 'RADIO_PLAYER_DEBUG' ) && RADIO_PLAYER_DEBUG ) {
		echo '<span style="display:none;">Style Path: ' . esc_html( $path ) . '</span>';
	}
	if ( file_exists( $path ) ) {
		$version = filemtime( $path );
		if ( function_exists( 'wp_enqueue_style' ) ) {
			if ( defined( 'RADIO_PLAYER_URL' ) ) {
				$url = RADIO_PLAYER_URL . 'css/radio-player' . $suffix. '.css';
			} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
				$url = plugins_url( 'player/css/radio-player' . $suffix . '.css', RADIO_STATION_FILE );
			} else {
				$url = plugins_url( 'css/radio-player.css', __FILE__ );
			}
			wp_enqueue_style( 'radio-player', $url, array(), $version, 'all' );

			// --- enqueue player control styles inline ---
			$control_styles = radio_player_control_styles( false );
			if ( '' != $control_styles ) {
				// 2.5.0: use radio_player_add_inline_style (with fallback)
				radio_player_add_inline_style( $control_styles );
			}

		} /* else {
			// --- output style tag directly ---
			$url = 'css/radio-player' . $suffix . '.css';
			if ( defined( 'RADIO_PLAYER_URL' ) ) {
				$url = RADIO_PLAYER_URL . $url;
			}
			radio_player_style_tag( 'radio-player', $url, $version );
			
			// 2.5.0: added missing non-WP control style output
			$control_styles = radio_player_control_styles( false );
			if ( '' != $control_styles ) {
				// 2.5.6: use wp_kses_post on control styles
				echo wp_kses_post( $control_styles );
			}
			
		} */

		// --- debug skin path / URL ---
		if ( RADIO_PLAYER_DEBUG ) {
			echo '<span style="display:none;">Skin Path: ' . esc_html( $path ) . '</span>' . "\n";
			echo '<span style="display:none;">Skin URL: ' . esc_html( $url ) . '</span>' . "\n";
		}
		return;
	}

	// --- enqueue base jplayer styles ---
	/*
	$suffix = ''; // DEV TEMP

	if ( defined( 'RADIO_STATION_DIR' ) ) {
		$path = RADIO_STATION_DIR . '/player/css/jplayer' . $suffix . '.css';
	} else {
		$path = dirname( __FILE__ ) . '/css/jplayer' . $suffix . '.css';
	}
	if ( file_exists( $path ) ) {
		$version = filemtime( $path );
		if ( function_exists( 'wp_enqueue_style' ) ) {
			if ( defined( 'RADIO_PLAYER_URL' ) ) {
				$url = RADIO_PLAYER_URL . 'css/jplayer' . $suffix. '.css';
			} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
				$url = plugins_url( 'player/css/jplayer' . $suffix . '.css', RADIO_STATION_FILE );
			} else {
				$url = plugins_url( 'css/jplayer.css', __FILE__ );
			}
			wp_enqueue_style( 'rp-jplayer', $url, array(), $version, 'all' );
		} else {
			// --- output style tag directly ---
			// $url = 'css/jplayer' . $suffix . '.css';
			// if ( defined( 'RADIO_PLAYER_URL' ) ) {$url = RADIO_PLAYER_URL . $url;}
			// radio_player_style_tag( 'rp-jplayer', $url, $version );
		}

		// --- debug skin path / URL ---
		if ( RADIO_PLAYER_DEBUG ) {
			echo '<span style="display:none;">Skin Path: ' . esc_html( $path ) . '</span>' . "\n";
			echo '<span style="display:none;">Skin URL: ' . esc_html( $url ) . '</span>' . "\n";
		}
	} */

	// --- JPlayer Skins ---
	// $skins = array( 'pink-flag', 'blue-monday' );
	// if ( in_array( $skin, $skins ) ) {

		// --- enqeueue player skin ---
		/* $skin_ref = '.' . str_replace( '-', '.', $skin );
		if ( defined( 'RADIO_STATION_DIR' ) ) {
			$path = RADIO_STATION_DIR . '/player/css/jplayer' . $skin_ref . $suffix . '.css';
		} else {
			$path = dirname( __FILE__ ) . '/css/jplayer' . $skin_ref . $suffix . '.css';
		}
		if ( file_exists( $path ) ) {
			$version = filemtime( $path );
			if ( function_exists( 'wp_enqueue_style' ) ) {
				if ( defined( 'RADIO_PLAYER_URL' ) ) {
					$url = RADIO_PLAYER_URL . 'css/jplayer' . $skin_ref . $suffix . '.css';
				} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
					$url = plugins_url( 'player/css/jplayer' . $skin_ref . $suffix . '.css', RADIO_STATION_FILE );
				} else {
					$url = plugins_url( 'css/jplayer' . $skin_ref . $suffix . '.css', __FILE__ );
				}
				// $deps = array();
				// if ( '' == $suffix ) {
					$deps = array( 'rp-jplayer' );
				// }
				wp_enqueue_style( 'rp-jplayer-' . $skin, $url, $deps, $version, 'all' );
			} else {
				// --- output style tag directly ---
				// $url = 'css/jplayer' . $skin_ref . $suffix . '.css';
				// if ( defined( 'RADIO_PLAYER_URL' ) ) {$url = RADIO_PLAYER_URL . $url;}
				// radio_player_style_tag( 'rp-jplayer-skin', $url, $version );
			}

			// --- debug skin path / URL ---
			if ( RADIO_PLAYER_DEBUG ) {
				echo '<span style="display:none;">Skin Path: ' . $path . '</span>';
				echo '<span style="display:none;">Skin URL: ' . $url . '</span>';
			}
		} */

	// }

	// --- Media Element Skins ---
	// (note: classes reprefixed to rp-)
	/* if ( 'mediaelements' == $script ) {

		$skins = array( 'wordpress', 'minimal' );
		if ( !in_array( $skin, $skins ) ) {
			$skin = 'wordpress';
		}
		if ( 'wordpress' == $skin ) {

			// --- WordPress Default ---
			if ( defined( 'RADIO_STATION_DIR' ) ) {
				$path = RADIO_STATION_DIR . '/player/css/rp-mediaelement.css';
			} else {
				$path = dirname( __FILE__ ) . '/player/css/rp-mediaelement.css';
			}
			$version = filemtime( $path );
			if ( function_exists( 'wp_enqueue_style' ) ) {
				if ( defined( 'RADIO_PLAYER_URL' ) ){
					$url = RADIO_PLAYER_URL . 'css/rp-mediaelement.css';
				} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
					$url = plugins_url( 'player/css/rp-mediaelement.css', RADIO_STATION_FILE );
				} else {
					$url = plugins_url( 'css/rp-mediaelement.css', __FILE__ );
				}
				wp_enqueue_style( 'rp-mediaelement', $url, array(), $version, 'all' );
			} else {
				// --- output style tag directly ---
				// $url = 'css/rp-mediaelement.css';
				// if ( defined( 'RADIO_PLAYER_URL' ) ) {$url = RADIO_PLAYER_URL . $url;}
				// radio_player_style_tag( 'rp-mediaelement', $url, $version );
			}

		} elseif ( 'minimal' == $skin ) {

			// --- Minimal Style ---
			// ref: https://github.com/justintadlock/theme-mediaelement
			if ( defined( 'RADIO_STATION_DIR' ) ) {
				$path = RADIO_STATION_DIR . '/player/css/mediaelement.css';
			} else {
				$path = dirname( __FILE__ ) . '/css/mediaelement.css';
			}
			$version = filemtime( $path );
			if ( function_exists( 'wp_enqueue_style' ) ) {
				if ( defined( 'RADIO_PLAYER_URL' ) ) {
					$url = RADIO_PLAYER_URL . 'css/mediaelement.css';
				} elseif ( defined( 'RADIO_STATION_FILE' ) ) {
					$url = plugins_url( 'player/css/mediaelement.css', RADIO_STATION_FILE );
				} else {
					$url = plugins_url( 'css/mediaelement.css', __FILE__ );
				}
				wp_enqueue_style( 'rp-mediaelement', $url, array(), $version, 'all' );
			} else {
				// --- output style tag directly ---
				// $url = 'css/mediaelement.css';
				// if ( defined( 'RADIO_PLAYER_URL' ) ) {$url = RADIO_PLAYER_URL . $url;}
				// radio_player_style_tag( 'rp-mediaelement', $url, $version );
			}
		}
		return;
	} */
}

// ---------------------
// Player Control Styles
// ---------------------
// 2.5.16: add optional attribute overrides argument
function radio_player_control_styles( $instance, $atts = false ) {

	global $radio_player;

	// --- set default control colors ---
	// 2.5.0: added empty text and background color styles
	$colors = array(
		'text'		 => '',
		'background' => '',
		'playing'	 => '#70E070',
		'buttons'	 => '#00A0E0',
		'track'		 => '#80C080',
		'thumb'		 => '#80C080',
	);

	// --- get color settings ---
	if ( function_exists( 'radio_station_get_setting' ) ) {
		$colors['playing'] = radio_station_get_setting( 'player_playing_color' );
		$colors['buttons'] = radio_station_get_setting( 'player_buttons_color' );
		$colors['thumb'] = radio_station_get_setting( 'player_thumb_color' );
		$colors['track'] = radio_station_get_setting( 'player_range_color' );
	}

	// 2.5.16: maybe apply override attributes
	if ( $atts ) {
		foreach ( $atts as $key => $value ) {
			$colors[$key] = $value;
		}
	}

	if ( function_exists( 'apply_filters' ) ) {
		$colors['text'] = apply_filters( 'radio_station_player_text_color', $colors['text'], $instance );
		$colors['text'] = apply_filters( 'radio_player_text_color', $colors['text'], $instance );
		$colors['background'] = apply_filters( 'radio_station_player_background_color', $colors['background'], $instance );
		$colors['background'] = apply_filters( 'radio_player_background_color', $colors['background'], $instance );
		$colors['playing'] = apply_filters( 'radio_station_player_playing_color', $colors['playing'], $instance );
		$colors['playing'] = apply_filters( 'radio_player_playing_color', $colors['playing'], $instance );
		$colors['buttons'] = apply_filters( 'radio_station_player_buttons_color', $colors['buttons'], $instance );
		$colors['buttons'] = apply_filters( 'radio_player_buttons_color', $colors['buttons'], $instance );
		$colors['thumb'] = apply_filters( 'radio_station_player_thumb_color', $colors['thumb'], $instance );
		$colors['thumb'] = apply_filters( 'radio_player_thumb_color', $colors['thumb'], $instance );
		$colors['track'] = apply_filters( 'radio_station_player_range_color', $colors['track'], $instance );
		$colors['track'] = apply_filters( 'radio_player_range_color', $colors['track'], $instance );
	}

	// --- check for player instance ---
	// 2.5.0: improved instance and ID matching
	if ( false !== $instance ) {
		
		// -- set instance container selector ---
		$container = '#radio_container_' . $instance;

		// --- get colors for container instance ---
		if ( isset( $radio_player['instance-props'][$instance] ) ) {
			$instance_props = $radio_player['instance-props'][$instance];
			foreach ( $instance_props as $key => $value ) {
				if ( substr( $key, -6, 6 ) == '_color' ) {

					// 2.5.0: ignore text and background for bar instance
					if ( isset( $radio_player['bar-instance'] ) && ( $instance == $radio_player['bar-instance'] ) ) {
						if ( in_array( $key, array( 'text_color', 'background_color' ) ) ) {
							$value = '';
						}
					}
					
					if ( $value && ( '' != $value ) ) {
						if ( 'rgb' != ( substr( $value, 0, 3 ) ) && ( '#' != substr( $value, 0, 1 ) ) ) {
							$value = '#' . $value;
						}
						$key = str_replace( '_color', '', $key );
						$colors[$key] = $value;
					}
				}
			}
		}

	} else {
		// 2.5.0: added check to do once only
		if ( isset( $radio_player['control-styles'] ) ) {
			return '';
		}
		$radio_player['control-styles'] = true;
		
		// --- set generic container selector ---
		$container = '.radio-container';
	}

	// 2.4.0.3: added missing function_exists wrapper
	if ( function_exists( 'apply_filters' ) ) {
		$colors = apply_filters( 'radio_station_player_control_colors', $colors, $instance );
		$colors = apply_filters( 'radio_player_control_colors', $colors, $instance );
	}

	$css = '';

	// --- Player Colors ---
	// 2.5.0: added for main color styling override
	if ( isset( $colors['text'] ) && ( '' != $colors['text'] ) ) {
		$css .= esc_attr( $container ) . " {color: " . esc_attr( $colors['text'] ) . ";}" . "\n";
	}
	if ( isset( $colors['background'] ) && ( '' != $colors['background'] ) ) {
		$css .= esc_attr( $container ) . " {background-color: " . esc_attr( $colors['background'] ) . ";}" . "\n";
	}

	// --- Play Button ---
	// 2.4.0.2: fix to glowingloading animation reference
	$css .= "/* Playing Button */
" . esc_attr( $container ) . ".loaded .rp-play-pause-button-bg {background-color: " . esc_attr( $colors['buttons'] ) . ";}
" . esc_attr( $container ) . ".playing .rp-play-pause-button-bg {background-color: " . esc_attr( $colors['playing'] ) . ";}
" . esc_attr( $container ) . ".error .rp-play-pause-button-bg {background-color: #CC0000;}
" . esc_attr( $container ) . ".loading .rp-play-pause-button-bg {animation: glowingloading 1s infinite alternate;}
" . esc_attr( $container ) . ".playing .rp-play-pause-button-bg, 
" . esc_attr( $container ) . ".playing.loaded .rp-play-pause-button-bg {animation: glowingplaying 1s infinite alternate;}
@keyframes glowingloading {
	from {background-color: " . esc_attr( $colors['buttons'] ) . ";} to {background-color: " . esc_attr( $colors['buttons'] ) . "80;}
}
@keyframes glowingplaying {
	from {background-color: " . esc_attr( $colors['playing'] ) . ";} to {background-color: " . esc_attr( $colors['playing'] ) . "C0;}
}" . "\n";

	// --- Active Volume Buttons Color ---
	// 2.5.0: added popup player button selector
	$css .= "/* Volume Buttons */
" . esc_attr( $container ) . " .rp-mute:hover, " . esc_attr( $container ) . ".muted .rp-mute, " . esc_attr( $container ) . ".muted .rp-mute:hover,
" . esc_attr( $container ) . " .rp-volume-max:focus, " . esc_attr( $container ) . " .rp-volume-max:hover, " . esc_attr( $container ) . ".maxed .rp-volume-max,
" . esc_attr( $container ) . " .rp-volume-up:focus, " . esc_attr( $container ) . " .rp-volume-up:hover,
" . esc_attr( $container ) . " .rp-volume-down:focus, " . esc_attr( $container ) . " .rp-volume-down:hover,
" . esc_attr( $container ) . " .rp-popup-button:focus, " . esc_attr( $container ) . " .rp-popup-button:hover {
	background-color: " . esc_attr( $colors['buttons'] ) . ";
}" . "\n";

	// --- Volume Range Input and Container ---
	// ref: http://danielstern.ca/range.css/#/
	// ref: https://css-tricks.fcom/sliding-nightmare-understanding-range-input/
	// 2.4.0.4: added no border style to range input (border added on some themes)
	// 2.5.0: added input height 100% to fix vertical slider alignment
	$css .= "/* Range Input */
" . esc_attr( $container ) . " .rp-volume-controls input[type=range] {";
	$css .= "height: 100%; margin: 0; background-color: transparent; vertical-align: middle; -webkit-appearance: none; border: none;}
" . esc_attr( $container ) . " .rp-volume-controls input[type=range]:focus {outline: none; box-shadow: none;}
" . esc_attr( $container ) . " .rp-volume-controls input[type=range]::-moz-focus-inner,
" . esc_attr( $container ) . " .rp-volume-controls input[type=range]::-moz-focus-outer {outline: none; box-shadow: none;}" . "\n";

	// --- Range Track (synced Background Div) ---
	// 2.4.0.3: add position absolute/top on slider background (cross-browser display fix)
	// 2.5.10: set top bottom and height to 10px for consistent display
	// 2.5.10: set z-index to 1 to use with improved javascript calculations
	$css .= "/* Range Track */
" . esc_attr( $container ) . " .rp-volume-controls .rp-volume-slider-bg {
	position: absolute; top: 10px; bottom: 10px; overflow: hidden; height: 10px; margin-left: 9px; z-index: 1; border: 1px solid rgba(128, 128, 128, 0.5); border-radius: 3px; background: rgba(128, 128, 128, 0.5);
}
" . esc_attr( $container ) . ".playing .rp-volume-controls .rp-volume-slider-bg {background: " . esc_attr( $colors['track'] ) . ";}
" . esc_attr( $container ) . ".playing.muted .rp-volume-controls .rp-volume-slider-bg {background: rgba(128, 128, 128, 0.5);}" . "\n";

	// --- Slider Range Track (Clickable Transparent) ---
	$css .= "/* Range Track */
" . esc_attr( $container ) . " .rp-volume-controls input[type=range]::-webkit-slider-runnable-track {height: 9px; background: transparent; -webkit-appearance: none; color: transparent}
" . esc_attr( $container ) . " .rp-volume-controls input[type=range]::-moz-range-track {height: 9px; background: transparent; color: transparent;}
" . esc_attr( $container ) . " .rp-volume-controls input[type=range]::-ms-track {height: 9px; color: transparent; background: transparent; border-color: transparent;}" . "\n";
// 2.4.0.3: remove float on range input (cross-browser display fix)
// " . $container . " .rp-volume-controls input[type=range] {float: left; margin-top: -9px;}

	// --- Slider Range Thumb ---
	// 2.5.10: fix to slider range thumb border radius (for default circle buttons)
	$css .= "/* Range Thumb */
" . esc_attr( $container ) . " .rp-volume-controls input[type=range]::-webkit-slider-thumb {
	width: 18px; height: 18px; cursor: pointer; background: rgba(128, 128, 128, 1);
	border: 1px solid rgba(128, 128, 128, 0.5); border-radius: 9px; z-index: 2;
	margin-top: -4.5px; -webkit-appearance: none;
}
" . esc_attr( $container ) . " .rp-volume-controls input[type=range]::-moz-range-thumb {
	width: 18px; height: 18px; cursor: pointer; background: rgba(128, 128, 128, 1);
	border: 1px solid rgba(128, 128, 128, 0.5); border-radius: 9px; z-index: 2;
}
" . esc_attr( $container ) . " .rp-volume-controls input[type=range]::-ms-thumb {
	width: 18px; height: 18px; cursor: pointer; background: rgba(128, 128, 128, 1);
	border: 1px solid rgba(128, 128, 128, 0.5); border-radius: 9px; z-index: 2; margin-top: 0px; 
}
" . esc_attr( $container ) . ".rounded .rp-volume-controls input[type=range]::-webkit-slider-thumb {border-radius: 5px !important;}
" . esc_attr( $container ) . ".square .rp-volume-controls input[type=range]::-webkit-slider-thumb {border-radius: 0px !important;}
" . esc_attr( $container ) . ".playing .rp-volume-controls input[type=range]::-webkit-slider-thumb {background: " . esc_attr( $colors['thumb'] ) . "};
" . esc_attr( $container ) . ".playing .rp-volume-controls input[type=range]::-moz-range-thumb {background: " . esc_attr( $colors['thumb'] ) . "};
" . esc_attr( $container ) . ".playing .rp-volume-controls input[type=range]::-ms-thumb {background: " . esc_attr( $colors['thumb'] ) . "};
" . esc_attr( $container ) . " input[type=range]::-ms-tooltip {display: none;}
@supports (-ms-ime-align:auto) {
  " . esc_attr( $container ) . " .rp-volume-controls input[type=range] {margin: 0;}
}" . "\n";

	// --- dummy element style for thumb width ---
	// note: since *actual* range input thumb width is hard/impossible to get with jQuery,
	// if changing the thumb width style, override this style also for volume background to match!
	$css .= esc_attr( $container ) . " .rp-volume-thumb {display: none; width: 18px;}" . "\n";

	// --- get volume control display settings ---
	// 2.4.1.4: added volume control visibility options filter
	// 2.5.0: added check for instance property for volume controls
	$volume_controls = array( 'slider', 'updown', 'mute', 'max' );
	if ( isset( $radio_player['instance-props'][$instance]['volume-controls'] ) ) {
		$volume_controls = $radio_player['instance-props'][$instance]['volume-controls'];
	}

	// --- volume display styles ---
	if ( !in_array( 'slider', $volume_controls ) ) {
		$css .= "\n" . esc_attr(  $container ) . " .rp-volume-slider-container {display: none;}" . "\n";
	}
	if ( !in_array( 'updown', $volume_controls ) ) {
		$css .= "\n" . esc_attr( $container ) . " .rp-volume-up, " . esc_attr( $container ) . " .rp-volume-down {display: none;}" . "\n";
	}
	if ( !in_array( 'mute', $volume_controls ) ) {
		$css .= "\n" . esc_attr( $container ) . " .rp-mute {display: none;}" . "\n";
	}
	if ( !in_array( 'max', $volume_controls ) ) {
		$css .= "\n" . esc_attr( $container ) . " .rp-volume-max {display: none;}" . "\n";
	}

	// --- filter and return ---
	// 2.4.0.3: added missing function_exists wrapper
	if ( function_exists( 'apply_filters' ) ) {
		$css = apply_filters( 'radio_station_player_control_styles', $css, $instance );
		$css = apply_filters( 'radio_player_control_styles', $css, $instance );
	}

	return $css;
}

// --------------------
// AJAX: Control Styles
// --------------------
// 2.5.16: added separate control styles load for block editor
add_action( 'wp_ajax_player_control_styles', 'radio_player_control_styles_ajax' );
add_action( 'wp_ajax_nopriv_player_control_styles', 'radio_player_control_styles_ajax' );
function radio_player_control_styles_ajax() {

	// --- get provided parameters ---
	$instance = absint( $_REQUEST['instance'] );
	$atts['text'] = sanitize_text_field( wp_unslash( $_REQUEST['text'] ) );
	$atts['background'] = sanitize_text_field( wp_unslash( $_REQUEST['background'] ) );
	$atts['playing'] = sanitize_text_field( wp_unslash( $_REQUEST['playing'] ) );
	$atts['buttons'] = sanitize_text_field( wp_unslash( $_REQUEST['buttons'] ) );
	$atts['thumb'] = sanitize_text_field( wp_unslash( $_REQUEST['thumb'] ) );
	$atts['track'] = sanitize_text_field( wp_unslash( $_REQUEST['track'] ) );
	
	// --- generate control styles ---
	$css = radio_player_control_styles( $instance, $atts );
	
	// --- return result ---
	if ( '' != $css ) {
		$data = array(
			'success'	=> 1,
			'message'	=> 'Generated Control Colors for Player Instance ' . $instance,
			'instance'	=> $instance,
			'css'		=> $css
		);
	} else {
		$data = array(
			'success'	=> 0,
			'message'	=> 'Empty Styles for Player Instance ' . $instance,
			'instance'	=> $instance,
		);
	}
	wp_send_json( $data );
}

// ------------------
// Debug Skin Loading
// ------------------
// add_filter( 'style_loader_tag', 'radio_player_debug_skin',10, 2 );
// function radio_player_debug_skin( $tag, $handle ) {
// 	if ( RADIO_PLAYER_DEBUG ) {
//		if ( 'rp-jplayer' == $handle ) {
//			echo "[!Radio Player JPlayer Handle Found!]";
//		}
//	}
//	return $tag;
// }

