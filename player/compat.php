<?php

// --------------------------------------------
// Standalone Compatibility WordPress Functions
// --------------------------------------------

// ---------
// Escape JS
// ---------
if ( !function_exists( 'esc_js' ) ) {
 function esc_js( $js ) {
	return $js;
 }
}

// -----------
// Escape HTML
// -----------
if ( !function_exists( 'esc_html' ) ) {
 function esc_html( $html ) {
	return $html;
 }
}

// ----------
// Escape URL
// ----------
if ( !function_exists( 'esc_url' ) ) {
 function esc_url( $url ) {
	return $url;
 }
}

// -------------------
// Sanitize Text Field
// -------------------
if ( !function_exists( 'sanitize_text_field' ) ) {
 function sanitize_text_field( $text ) {
	return $text;
 }
}


// ----------------------------------------
// Player Backwards Compatibility Functions
// ----------------------------------------

if ( !function_exists( 'radio_station_player_enqueue_styles' ) ) {
	function radio_station_player_enqueue_styles() {
		radio_player_enqueue_styles();
	}
}

if ( !function_exists( 'radio_station_player_output' ) ) {
	function radio_station_player_output( $args = array(), $echo = false ) {
		// 2.5.4: fix to arguments
		return radio_player_output( $args, $echo );
	}
}

if ( !function_exists( 'radio_station_player_instance_args' ) ) {
	function radio_station_player_instance_args( $args, $instance ) {
		return radio_player_instance_args( $args, $instance );
	}
}

if ( !function_exists( 'radio_station_player_shortcode' ) ) {
	// 2.5.4: fix for missing argument
	function radio_station_player_shortcode( $atts ) {
		return radio_player_shortcode( $atts );
	}
}

if ( !function_exists( 'radio_station_player_default_colors' ) ) {
	function radio_station_player_default_colors( $atts ) {
		return radio_player_default_colors( $atts );
	}
}

if ( !function_exists( 'radio_station_player_ajax' ) ) {
	function radio_station_player_ajax() {
		radio_player_ajax();
	}
}

if ( !function_exists( 'radio_station_player_sanitize_shortcode_values' ) ) {
	function radio_station_player_sanitize_shortcode_values() {
		return radio_player_sanitize_shortcode_values();
	}
}

if ( !function_exists( 'radio_station_player_core_scripts' ) ) {
	function radio_station_player_core_scripts() {
		radio_player_core_scripts();
	}
}

if ( !function_exists( 'radio_station_player_enqueue_script' ) ) {
	// 2.5.4: fix for missing argument
	function radio_station_player_enqueue_script( $script ) {
		radio_player_enqueue_script( $script );
	}
}

if ( !function_exists( 'radio_station_player_load_script_fallbacks' ) ) {
	function radio_station_player_load_script_fallbacks( $js ) {
		return radio_player_load_script_fallbacks( $js );
	}
}

if ( !function_exists( 'radio_station_player_enqueue_amplitude' ) ) {
	function radio_station_player_enqueue_amplitude( $infooter ) {
		radio_player_enqueue_amplitude( $infooter );
	}
}

if ( !function_exists( 'radio_station_player_enqueue_jplayer' ) ) {
	function radio_station_player_enqueue_jplayer( $infooter ) {
		radio_player_enqueue_jplayer( $infooter );
	}
}

if ( !function_exists( 'radio_station_player_enqueue_howler' ) ) {
	function radio_station_player_enqueue_howler( $infooter ) {
		radio_player_enqueue_howler( $infooter );
	}
}

if ( !function_exists( 'radio_player_script' ) ) {
	function radio_player_script() {
		radio_player_script();
	}
}

if ( !function_exists( 'radio_station_player_get_settings' ) ) {
	function radio_station_player_get_settings() {
		return radio_player_get_player_settings();
	}
}

if ( !function_exists( 'radio_station_player_iframe' ) ) {
	function radio_station_player_iframe() {
		radio_player_iframe();
	}
}

if ( !function_exists( 'radio_station_player_state' ) ) {
	function radio_station_player_state() {
		radio_player_state();
	}
}

if ( !function_exists( 'radio_station_player_script_amplitude' ) ) {
	function radio_station_player_script_amplitude() {
		return radio_player_script_amplitude();
	}
}

if ( !function_exists( 'radio_station_player_script_howler' ) ) {
	function radio_station_player_script_howler() {
		return radio_player_script_howler();
	}
}

if ( !function_exists( 'radio_station_player_script_jplayer' ) ) {
	function radio_station_player_script_jplayer() {
		return radio_player_script_jplayer();
	}
}

if ( !function_exists( 'radio_station_player_script_mediaelements' ) ) {
	function radio_station_player_script_mediaelements() {
		return radio_player_script_mediaelements();
	}
}

if ( !function_exists( 'radio_station_player_get_default_script' ) ) {
	function radio_station_player_get_default_script() {
		return radio_player_get_default_script();
	}
}

if ( !function_exists( 'radio_station_player_enqueue_styles' ) ) {
	function radio_station_player_enqueue_styles( $script = false, $skin = false ) {
		radio_player_enqueue_styles( $script, $skin );
	}
}

if ( !function_exists( 'radio_station_player_control_styles' ) ) {
	function radio_station_player_control_styles( $instance ) {
		return radio_player_control_styles( $instance );
	}
}

if ( !function_exists( 'radio_station_player_script_tag' ) ) {
	function radio_station_player_script_tag( $url, $version ) {
		return radio_player_script_tag( $url, $version );
	}
}

if ( !function_exists( 'radio_station_player_style_tag' ) ) {
	function radio_station_player_style_tag( $id, $url, $version ) {
		return radio_player_style_tag( $id, $url, $version );
	}
}

if ( !function_exists( 'radio_station_player_validate_boolean' ) ) {
	function radio_station_player_validate_boolean( $value ) {
		return (bool) $value;
	}
}
