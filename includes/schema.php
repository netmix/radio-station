<?php

/*
 * Radio Station Schema Markup
 * Author: Tony Zeoli
 * @Since: 2.7.3
 */

if ( !defined( 'ABSPATH' ) ) exit;

// === Schema Helpers ===
// - Check Schema Enabled
// - Get Schema Node ID
// - Get Schema Language
// - Get Schema Date
// - Clean Schema Node
// === Schema Graph ===
// - Get Station Schema
// - Get Broadcast Service Schema
// - Get Schema Graph
// - Output Schema JSON-LD


// ----------------------
// === Schema Helpers ===
// ----------------------

// --------------------
// Check Schema Enabled
// --------------------
function radio_station_schema_enabled() {
	$enabled = ( 'yes' == radio_station_get_setting( 'schema_enable' ) );
	$enabled = apply_filters( 'radio_station_schema_enabled', $enabled );
	return $enabled;
}

// ------------------
// Get Schema Node ID
// ------------------
// note: stable @id values allow nodes to reference each other
function radio_station_get_schema_id( $key, $url = false ) {
	if ( !$url ) {
		$url = home_url( '/' );
	}
	return $url . '#' . $key;
}

// -------------------
// Get Schema Language
// -------------------
// note: converts locale (eg. en_US) to BCP 47 tag (eg. en-US)
function radio_station_get_schema_language( $language = false ) {
	if ( !$language ) {
		$language = radio_station_get_language();
		$language = ( is_array( $language ) && isset( $language['slug'] ) ) ? $language['slug'] : '';
	}
	return str_replace( '_', '-', $language );
}

// ---------------
// Get Schema Date
// ---------------
// note: formats a timestamp as ISO 8601 in the station timezone
function radio_station_get_schema_date( $timestamp ) {
	$timezone = radio_station_get_timezone();
	try {
		$tz = new DateTimeZone( $timezone );
	} catch ( Exception $e ) {
		$tz = function_exists( 'wp_timezone' ) ? wp_timezone() : new DateTimeZone( 'UTC' );
	}
	$datetime = new DateTime( '@' . (int) $timestamp );
	$datetime->setTimezone( $tz );
	return $datetime->format( 'c' );
}

// -----------------
// Clean Schema Node
// -----------------
// note: recursively removes empty values so no blank properties are output
function radio_station_clean_schema_node( $node ) {
	$is_list = ( array_keys( $node ) === range( 0, count( $node ) - 1 ) );
	foreach ( $node as $key => $value ) {
		if ( is_array( $value ) ) {
			$value = radio_station_clean_schema_node( $value );
			$node[$key] = $value;
		}
		if ( ( '' === $value ) || ( null === $value ) || ( false === $value ) || ( is_array( $value ) && ( 0 == count( $value ) ) ) ) {
			unset( $node[$key] );
		}
	}
	// --- reindex lists so they encode as JSON arrays ---
	if ( $is_list ) {
		$node = array_values( $node );
	}
	return $node;
}


// --------------------
// === Schema Graph ===
// --------------------

// ------------------
// Get Station Schema
// ------------------
function radio_station_get_station_schema( $station_data = false ) {

	if ( !$station_data ) {
		$station_data = radio_station_get_station_data();
	}

	// --- station name fallback ---
	$name = $station_data['name'];
	if ( !$name || ( '' == $name ) ) {
		$name = get_bloginfo( 'name' );
	}

	// --- broadcast station or internet station ---
	$station_type = radio_station_get_setting( 'schema_station_type' );
	$broadcast = ( 'internet' != $station_type );

	$node = array(
		'@type'       => $broadcast ? 'RadioStation' : 'Organization',
		'@id'         => radio_station_get_schema_id( 'radio-station' ),
		'name'        => $name,
		'url'         => home_url( '/' ),
		'description' => $station_data['tagline'],
		'email'       => radio_station_get_setting( 'station_email' ),
		'telephone'   => radio_station_get_setting( 'station_phone' ),
	);

	// --- station logo ---
	if ( '' != $station_data['image_url'] ) {
		$node['logo'] = array(
			'@type' => 'ImageObject',
			'url'   => $station_data['image_url'],
		);
		$node['image'] = $station_data['image_url'];
	}

	// --- location (broadcast stations are local businesses) ---
	if ( $broadcast ) {
		$node['address'] = $station_data['location'];
	}

	// --- link to broadcast service ---
	$node['potentialAction'] = array();
	if ( '' != $station_data['stream_url'] ) {
		$node['potentialAction'][] = array(
			'@type'  => 'ListenAction',
			'target' => array(
				'@type'          => 'EntryPoint',
				'urlTemplate'    => $station_data['stream_url'],
				'actionPlatform' => array(
					'https://schema.org/DesktopWebPlatform',
					'https://schema.org/MobileWebPlatform',
				),
			),
		);
	}

	$node = apply_filters( 'radio_station_station_schema', $node, $station_data );
	return $node;
}

// ----------------------------
// Get Broadcast Service Schema
// ----------------------------
function radio_station_get_broadcast_service_schema( $station_data = false ) {

	if ( !$station_data ) {
		$station_data = radio_station_get_station_data();
	}

	$name = $station_data['name'];
	if ( !$name || ( '' == $name ) ) {
		$name = get_bloginfo( 'name' );
	}

	$node = array(
		'@type'                => 'RadioBroadcastService',
		'@id'                  => radio_station_get_schema_id( 'broadcast-service' ),
		'name'                 => $name,
		'url'                  => $station_data['station_url'],
		'broadcaster'          => array( '@id' => radio_station_get_schema_id( 'radio-station' ) ),
		'broadcastDisplayName' => $station_data['callsign'],
		'callSign'             => $station_data['callsign'],
		'genre'                => $station_data['genre'],
		'inLanguage'           => radio_station_get_schema_language( $station_data['language'] ),
	);

	// --- broadcast timezone (named timezones only) ---
	if ( false !== strpos( $station_data['timezone'], '/' ) ) {
		$node['broadcastTimezone'] = $station_data['timezone'];
	}

	// --- broadcast frequency (broadcast stations only) ---
	$station_type = radio_station_get_setting( 'schema_station_type' );
	if ( ( 'internet' != $station_type ) && ( '' != $station_data['frequency'] ) ) {
		$frequency = array(
			'@type'                   => 'BroadcastFrequencySpecification',
			'broadcastFrequencyValue' => $station_data['frequency'],
		);
		if ( '' != $station_data['band'] ) {
			$frequency['broadcastSignalModulation'] = strtoupper( $station_data['band'] );
		}
		$node['broadcastFrequency'] = $frequency;
	}

	$node = apply_filters( 'radio_station_broadcast_service_schema', $node, $station_data );
	return $node;
}

// ----------------
// Get Schema Graph
// ----------------
// note: Pro and other plugins add page specific nodes via radio_station_schema_graph filter
function radio_station_get_schema_graph() {

	global $radio_station_data;
	if ( isset( $radio_station_data['schema_graph'] ) ) {
		return $radio_station_data['schema_graph'];
	}

	$station_data = radio_station_get_station_data();
	$graph = array(
		radio_station_get_station_schema( $station_data ),
		radio_station_get_broadcast_service_schema( $station_data ),
	);

	$graph = apply_filters( 'radio_station_schema_graph', $graph, $station_data );

	// --- remove empty properties ---
	foreach ( $graph as $i => $node ) {
		$graph[$i] = radio_station_clean_schema_node( $node );
	}
	$graph = array_values( array_filter( $graph ) );

	$radio_station_data['schema_graph'] = $graph;
	return $graph;
}

// ----------------------
// Output Schema JSON-LD
// ----------------------
add_action( 'wp_head', 'radio_station_output_schema', 20 );
function radio_station_output_schema() {

	if ( is_admin() || is_feed() || !radio_station_schema_enabled() ) {
		return;
	}

	// --- allow bypass (eg. when merged into an SEO plugin graph) ---
	$output = apply_filters( 'radio_station_schema_output', true );
	if ( !$output ) {
		return;
	}

	$graph = radio_station_get_schema_graph();
	if ( 0 == count( $graph ) ) {
		return;
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	// note: JSON_HEX_TAG prevents closing script tags in data values
	$json = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG );
	if ( $json ) {
		echo '<script type="application/ld+json" class="radio-station-schema">' . $json . '</script>' . "\n";
	}
}
