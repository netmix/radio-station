<?php

/*
 * Admin Post Types Metaboxes and Post Lists
 * Author: Nikki Blight
 * Since: 2.2.7
 */

if ( !defined( 'ABSPATH' ) ) exit;

// === Metabox Positions ===
// - Metaboxes Above Content Area
// - Modify Taxonomy Metabox Positions
// === Language Selection ===
// - Add Language Metabox
// - Language Selection Metabox
// - Update Language Term on Save
// === Shows ===
// - Add Show Info Metabox
// - Show Info Metabox
// - Add Assign Hosts to Show Metabox
// - Assign Hosts to Show Metabox
// - Add Assign Producers to Show Metabox
// - Assign Producers to Show Metabox
// - Add Show Shifts Metabox
// - Show Shifts Metabox
// - Shifts List Styles
// - Shift Edit Script
// - Shifts Oonflict Message
// - Show Shift Table
// - Add Show Description Helper Metabox
// - Show Description Helper Metabox
// - Rename Show Featured Image Metabox
// - Add Show Images Metabox
// - Show Images Metabox
// - Update Show Metadata
// - Add Show List Columns
// - Show List Column Data
// - Show List Column Styles
// === Schedule Overrides ===
// - Add Override Show Data Metabox
// - Override Show Data Metabox
// - Override Show Data Script
// - Add Schedule Override Metabox
// - Schedule Override Metabox
// - Overrides List Styles
// - Overrides Table List
// - Override List Edit Script
// - Update Schedule Override
// - Add Schedule Override List Columns
// - Schedule Override Column Data
// - Schedule Override Column Styles
// - Sortable Override Date Column
// - Add Schedule Override Month Filter
// - Add Schedule Past Future Filter
// === Playlists ===
// - Add Playlist Data Metabox
// - Playlist Data Metabox
// - Add Assign Playlist to Show Metabox
// - Assign Playlist to Show Metabox
// - Update Playlist Data
// - Add Playlist List Columns
// - Playlist List Column Data
// - Playlist List Column Styles
// - Playlist List Column Scripts
// - Playlist Quick Edit Fields
// - Playlist Quick Edit Script
// === Posts ===
// - Add Related Shows Metabox
// - Related Shows Metabox
// - Update Relateds Show
// - Related Shows Quick Edit Select Input
// - Add Related Shows Post List Column
// - Related Shows Post List Column Data
// - Related Shows Quick Edit Script
// - Add Bulk Edit Posts Action
// - Bulk Edit Posts Script
// - Bulk Edit Posts Handler
// - Bulk Edit Posts Notice
// - Related Show Post List Styles
// === Extra Functions ===
// - Post Type List Query Filter
// - Relogin AJAX Message


// -------------------------
// === Metabox Positions ===
// -------------------------

// ----------------------------
// Metaboxes Above Content Area
// ----------------------------
// (shows metaboxes above Editor area for Radio Station CPTs)
add_action( 'edit_form_after_title', 'radio_station_top_meta_boxes' );
function radio_station_top_meta_boxes() {

	global $post, $wp_meta_boxes;
	$current_screen = get_current_screen();

	if ( RADIO_STATION_DEBUG ) {
		echo '<!-- DOING TOP METABOXES -->' . "\n";
		echo '<!-- TOP METABOXES: ' . esc_html( print_r( $wp_meta_boxes[$current_screen->post_type]['rstop'], true ) ) . ' -->' . "\n";
		echo '<!-- Current Screen: ' . esc_html( print_r( $current_screen, true ) ) . ' -->' . "\n";
		$metabox_order = get_user_option( 'meta-box-order_' . $current_screen->id );
		$hidden_metaboxes =  get_user_option( 'metaboxhidden_' . $current_screen->id );
		$screen_layout = get_user_option( 'screen_layout_' . $current_screen->id );
		echo '<!-- Metabox Order: ' . esc_html( print_r( $metabox_order, true ) ) . ' -->' . "\n";
		echo '<!-- Hidden Metaboxes: ' .  esc_html( print_r( $hidden_metaboxes, true ) ) . ' -->' . "\n";
		echo '<!-- Screen Layout: ' . esc_html (print_r( $screen_layout, true ) ) . ' -->' . "\n";
	}

	// --- top metabox output ---
	// 2.3.2: change metabox ID from rs-top
	// "-" is not supported in metabox ID for sort order saving!
	// causing bug where sorted metaboxes disappear completely.
	do_meta_boxes( $current_screen, 'rstop', $post );

	if ( RADIO_STATION_DEBUG ) {
		echo '<!-- DONE TOP METABOXES -->' . "\n";
	}
}

// ---------------------------------
// Modify Taxonomy Metabox Positions
// ---------------------------------
// 2.3.0: also apply to override post type
// 2.3.0: remove default languages metabox from shows
add_action( 'add_meta_boxes', 'radio_station_modify_taxonomy_metabox_positions', 11 );
function radio_station_modify_taxonomy_metabox_positions() {

	global $wp_meta_boxes;

	// --- move genre selection metabox ---
	$id = RADIO_STATION_GENRES_SLUG . 'div';
	if ( isset( $wp_meta_boxes[RADIO_STATION_SHOW_SLUG]['side']['core'][$id] ) ) {
		$genres = $wp_meta_boxes[RADIO_STATION_SHOW_SLUG]['side']['core'][$id];
		unset( $wp_meta_boxes[RADIO_STATION_SHOW_SLUG]['side']['core'][$id] );
		$wp_meta_boxes[RADIO_STATION_SHOW_SLUG]['side']['high'][$id] = $genres;
	}
	// 2.3.0: do similar for overrides post type
	if ( isset( $wp_meta_boxes[RADIO_STATION_OVERRIDE_SLUG]['side']['core'][$id] ) ) {
		$genres = $wp_meta_boxes[RADIO_STATION_OVERRIDE_SLUG]['side']['core'][$id];
		unset( $wp_meta_boxes[RADIO_STATION_OVERRIDE_SLUG]['side']['core'][$id] );
		$wp_meta_boxes[RADIO_STATION_OVERRIDE_SLUG]['side']['high'][$id] = $genres;
	}

	// --- remove default language metabox from shows ---
	if ( isset( $wp_meta_boxes[RADIO_STATION_SHOW_SLUG]['side']['core'][RADIO_STATION_LANGUAGES_SLUG . 'div'] ) ) {
		unset( $wp_meta_boxes[RADIO_STATION_SHOW_SLUG]['side']['core'][RADIO_STATION_LANGUAGES_SLUG . 'div'] );
	}
	if ( isset( $wp_meta_boxes[RADIO_STATION_SHOW_SLUG]['side']['core']['tagsdiv-' . RADIO_STATION_LANGUAGES_SLUG] ) ) {
		unset( $wp_meta_boxes[RADIO_STATION_SHOW_SLUG]['side']['core']['tagsdiv-' . RADIO_STATION_LANGUAGES_SLUG] );
	}
	if ( isset( $wp_meta_boxes[RADIO_STATION_OVERRIDE_SLUG]['side']['core']['tagsdiv-' . RADIO_STATION_LANGUAGES_SLUG] ) ) {
		unset( $wp_meta_boxes[RADIO_STATION_OVERRIDE_SLUG]['side']['core']['tagsdiv-' . RADIO_STATION_LANGUAGES_SLUG] );
	}
}


// --------------------------
// === Language Selection ===
// --------------------------

// --------------------
// Add Language Metabox
// --------------------
// 2.3.0: add language selection metabox
add_action( 'add_meta_boxes', 'radio_station_add_show_language_metabox' );
function radio_station_add_show_language_metabox() {
	// note: only added to overrides as moved into show info metabox for shows
	// 2.3.2: removed unnecessary array wrapper from post type argument
	add_meta_box(
		RADIO_STATION_LANGUAGES_SLUG . 'div',
		__( 'Show Language', 'radio-station' ),
		'radio_station_show_language_metabox',
		RADIO_STATION_OVERRIDE_SLUG,
		'side',
		'high'
	);
}

// --------------------------
// Language Selection Metabox
// --------------------------
// 2.3.0: added language selection metabox
function radio_station_show_language_metabox() {

	// --- use same noncename as default box so no save_post hook needed ---
	wp_nonce_field( 'taxonomy_' . RADIO_STATION_LANGUAGES_SLUG, 'taxonomy_noncename' );

	// --- get terms associated with this post ---
	$terms = wp_get_object_terms( get_the_ID(), RADIO_STATION_LANGUAGES_SLUG );

	// --- get all language options ---
	$languages = radio_station_get_languages();

	echo '<div style="margin-bottom: 5px;">' . PHP_EOL;

	// --- get main language ---
	$main_language = radio_station_get_language();
	foreach ( $languages as $i => $language ) {
		if ( strtolower( $main_language['slug'] ) == strtolower( $language['language'] ) ) {
			$label = $language['native_name'];
			if ( $language['native_name'] != $language['english_name'] ) {
				$label .= ' (' . $language['english_name'] . ')';
			}
		}
	}

	if ( isset( $label ) ) {
		echo '<b>' . esc_html( __( 'Main Radio Language', 'radio-station' ) ) . '</b>:<br>' . "\n";
		echo esc_html( $label ) . '<br>' . "\n";
	}

	echo '<div style="font-size:11px;">' . esc_html( __( 'Select below if Show language(s) differ.', 'radio-station' ) ) . '</div>' . "\n";

		echo '<ul id="' . esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '_taxradiolist" data-wp-lists="list:' . esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '_tax" class="categorychecklist form-no-clear">' . "\n";

			// --- loop existing terms ---
			$term_slugs = array();
			foreach ( $terms as $term ) {

				$slug = $term->slug;
				$term_slugs[] = $slug;
				$label = $term->name;
				// 2.5.0: only append description to label if different to name
				if ( !empty( $term->description ) && ( $term->description != $label ) ) {
					$label .= ' (' . $term->description . ')';
				}

				echo '<li id="' .  esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '_tax-' . esc_attr( $slug ) . '">' . "\n";

					// --- hidden input for term saving ---
					// echo '<input value="' . esc_attr( $name ) . '" type="checkbox" style="display: none;" name="tax_input[' . esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '][]" id="in-' . RADIO_STATION_LANGUAGES_SLUG . '_tax-' . esc_attr( $name ) . '" checked="checked">';
					echo '<input value="' . esc_attr( $slug ) . '" type="hidden" name="' . esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '[]" id="in-' . esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '_tax-' . esc_attr( $slug ) . '">' . "\n";

					// --- language term label ---
					echo '<label>' . esc_html( $label ) . '</label>' . "\n";

					// --- remove term button ---
					echo '<input type="button" class="button button-secondary" onclick="radio_remove_language(\'' . esc_attr( $slug ) . '\');" value="x" title="' . esc_attr( __( 'Remove Language', 'radio-station' ) ) . '">' . "\n";

				echo '</li>' . "\n";
			}

		echo '</ul>' . "\n";

		// --- new language selection list ---
		echo '<select id="rs-add-language-selection" onchange="radio_add_language();">' . "\n";
			echo '<option selected="selected">' . esc_html( __( 'Select Language', 'radio-station' ) ) . '</option>' . "\n";
			foreach ( $languages as $i => $language ) {
				$code = $language['language'];
				echo '<option value="' . esc_attr( $code ) . '"';
				if ( in_array( strtolower( $code ), $term_slugs ) ) {
					echo ' disabled="disabled"';
				}
				echo '>' . esc_html( $language['native_name'] );
				if ( $language['native_name'] != $language['english_name'] ) {
					echo ' (' . esc_html( $language['english_name'] ) . ')';
				}
				echo '</option>' . "\n";
			}
		echo '</select><br>' . "\n";

		// --- add language term button ---
		echo '<div style="font-size:11px;">' . esc_html( __( 'Click on a Language to Add it.', 'radio-station' ) ) . '</div>' . "\n";

	echo '</div>' . "\n";

	// --- language selection javascript ---
	$js = "function radio_add_language() {
		/* get and disable selected language item */
		select = document.getElementById('rs-add-language-selection');
		options = select.options;
		for (i = 0; i < options.length; i++) {
			if (options[i].selected) {
				optionvalue = options[i].value;
				optionlabel = options[i].innerHTML;
				options[i].setAttribute('disabled', 'disabled');
			}
		}
		select.selectedIndex = 0;

		/* add item to term list */
		listitem = document.createElement('li');
		listitem.setAttribute('id', '" . esc_js( RADIO_STATION_LANGUAGES_SLUG ) . "_tax-'+optionvalue);
		input = document.createElement('input');
		input.value = optionvalue;
		input.setAttribute('type', 'hidden');
		input.setAttribute('name', '" . esc_js( RADIO_STATION_LANGUAGES_SLUG ) . "[]');
		input.setAttribute('id', 'in-" . esc_js( RADIO_STATION_LANGUAGES_SLUG ) . "_tax-'+optionvalue);
		listitem.appendChild(input);
		label = document.createElement('label');
		label.innerHTML = optionlabel;
		listitem.appendChild(label);
		button = document.createElement('input');
		button.setAttribute('type', 'button');
		button.setAttribute('class', 'button button-secondary');
		button.setAttribute('onclick', 'radio_remove_language(\"'+optionvalue+'\");');
		button.setAttribute('value', 'x');
		listitem.appendChild(button);
		document.getElementById('" . esc_js( RADIO_STATION_LANGUAGES_SLUG ) . "_taxradiolist').appendChild(listitem);
	}
	function radio_remove_language(term) {
		/* remove item from term list */
		listitem = document.getElementById('" . esc_js( RADIO_STATION_LANGUAGES_SLUG ) . "_tax-'+term);
		listitem.parentNode.removeChild(listitem);

		/* re-enable language select option */
		select = document.getElementById('rs-add-language-selection');
		options = select.options;
		for (i = 0; i < options.length; i++) {
			if (options[i].value == term) {
				options[i].removeAttribute('disabled');
			}
		}
	}";

	// --- language input style fixes ---
	$css = "#". RADIO_STATION_LANGUAGES_SLUG . "_taxradiolist input.button {
		margin-left: 10px; padding: 0 7px; color: #E00; border-radius: 7px;
	}";
	// 2.3.3.9: added language edit styles filter
	// 2.5.0: use wp_kses_post on CSS output
	// 2.5.6: use radio_station_add_inline_style
	$css = apply_filters( 'radio_station_language_edit_styles', $css );
	// echo '<style>' . wp_kses_post( $css ) . '</style>';
	radio_station_add_inline_style( 'rs-admin', $css );

	// --- add script inline ---
	// 2.3.3.9: added language edit script filter
	$js = apply_filters( 'radio_station_language_edit_script', $js );
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'radio-station-admin', $js );
}

// ----------------------------
// Update Language Term on Save
// ----------------------------
// 2.3.0: added to sync language names to language term
add_action( 'save_post', 'radio_station_language_term_filter', 11 );
function radio_station_language_term_filter( $post_id ) {

	// ---- check permissions ---
	if ( !isset( $_POST[RADIO_STATION_LANGUAGES_SLUG] ) ) {
		return;
	}
	$check = wp_verify_nonce( sanitize_text_field( $_POST['taxonomy_noncename'] ), 'taxonomy_' . RADIO_STATION_LANGUAGES_SLUG );
	if ( !$check ) {
		return;
	}
	$taxonomy_obj = get_taxonomy( RADIO_STATION_LANGUAGES_SLUG );
	if ( !current_user_can( $taxonomy_obj->cap->assign_terms ) ) {
		return;
	}

	// --- loop and set posted terms ---
	// 2.5.6: use array_map with sanitize_text_field on array
	$terms = array_map( 'sanitize_text_field', $_POST[RADIO_STATION_LANGUAGES_SLUG] );

	$term_ids = array();
	if ( is_array( $terms ) && ( count( $terms ) > 0 ) ) {
		$languages = radio_station_get_languages();
		foreach ( $terms as $i => $term_slug ) {

			// 2.5.0: use sanitize_text_field on posted value
			// $term_slug = sanitize_text_field( $term_slug );

			foreach ( $languages as $j => $language ) {

				if ( strtolower( $language['language'] ) == strtolower( $term_slug ) ) {

					// --- get existing term ---
					$term = get_term_by( 'slug', $term_slug, RADIO_STATION_LANGUAGES_SLUG );

					// --- set language name and description to the term ---
					if ( $term ) {
						$args = array(
							'slug'         => $term_slug,
							'name'         => $language['native_name'],
							'description ' => $language['english_name'],
						);
						wp_update_term( $term->term_id, RADIO_STATION_LANGUAGES_SLUG, $args );
						$term_ids[] = $term->term_id;
					} else {
						$args = array(
							'slug'        => $term_slug,
							'description' => $language['english_name'],
						);
						$term = wp_insert_term( $language['native_name'], RADIO_STATION_LANGUAGES_SLUG, $args );
						if ( !is_wp_error( $term ) ) {
							$term_ids[] = $term['term_id'];
						}
					}
				}
			}
		}
	}

	// --- set the language terms ---
	wp_set_post_terms( $post_id, $term_ids, RADIO_STATION_LANGUAGES_SLUG );
}


// -------------
// === Shows ===
// -------------

// ---------------------
// Add Show Info Metabox
// ---------------------
add_action( 'add_meta_boxes', 'radio_station_add_show_info_metabox' );
function radio_station_add_show_info_metabox() {

	// 2.2.2: change context to show at top of edit screen
	// 2.3.2: filter top metabox position
	$position = apply_filters( 'radio_station_metabox_position', 'rstop', 'shows' );
	add_meta_box(
		'radio-station-show-info-metabox',
		__( 'Show Information', 'radio-station' ),
		'radio_station_show_info_metabox',
		RADIO_STATION_SHOW_SLUG,
		$position,
		'high'
	);
}

// -----------------
// Show Info Metabox
// -----------------
function radio_station_show_info_metabox() {

	global $post;
	// 2.4.0.9: added check for object for PHP8
	$post_id = ( is_object( $post ) && property_exists( $post, 'ID' ) ) ? $post->ID : 0;

	// 2.3.0: added missing nonce field
	wp_nonce_field( 'radio-station', 'show_meta_nonce' );

	// --- get show meta ---
	// 2.3.2: added show download disable switch
	$active = get_post_meta( $post_id, 'show_active', true );
	$link = get_post_meta( $post_id, 'show_link', true );
	$email = get_post_meta( $post_id, 'show_email', true );
	$phone = get_post_meta( $post_id, 'show_phone', true );
	$file = get_post_meta( $post_id, 'show_file', true );
	$download = get_post_meta( $post_id, 'show_download', true );
	$patreon_id = get_post_meta( $post_id, 'show_patreon', true );

	// added max-width to prevent metabox overflows
	// 2.3.0: removed new lines between labels and fields and changed widths
	// 2.3.2: increase label width to 120px for disable download field label
	// 2.3.3.9: move meta_inner ID to class
	// 2.3.3.9: change input paragraphs to list style
	echo '<div class="meta_inner">' . "\n";

		echo '<ul class="show-input-list">' . "\n";

			// --- show active switch ---
			echo '<li class="show-item">' . "\n";
				echo '<div class="input-label"><label>' . "\n";
					echo esc_html( __( 'Active', 'radio-station' ) ) . '?' . "\n";
				echo '</label></div>' . PHP_EOL;
				echo '<div class="input-field">' . "\n";
					echo '<input type="checkbox" name="show_active" ' . checked( $active, 'on', false ) . '>' . PHP_EOL;
				echo '</div>' . "\n";
				echo '<div class="input-helper">' . "\n";
					echo '<i>' . esc_html( __( 'Check this box if show is currently active (Show will not appear on schedule if unchecked.)', 'radio-station' ) ) . '</i>' . "\n";
				echo '</div>' . "\n";
			echo '</li>' . "\n";

			// --- show website link ---
			echo '<li class="show-item">' . "\n";
				echo '<div class="input-label"><label>' . "\n";
					echo esc_html( __( 'Website Link', 'radio-station' ) ) . ':' . "\n";
				echo '</label></div>' . "\n";
				echo '<div class="input-field">' . "\n";
					echo '<input type="text" name="show_link" size="100" style="max-width:80%;" value="' . esc_url_raw( $link ) . '">' . "\n";
				echo '</div>' . "\n";
			echo '</li>' . "\n";

			// --- show email address ---
			// 2.3.3.6: change text string from DJ / Host email (as maybe multiple hosts)
			echo '<li class="show-item">' . "\n";
				echo '<div class="input-label"><label>' . "\n";
					echo esc_html( __( 'Show Email', 'radio-station' ) ) . ':' . "\n";
				echo '</label></div>' . "\n";
				echo '<div class="input-field">' . "\n";
					echo '<input type="text" name="show_email" size="100" style="max-width:80%;" value="' . esc_attr( $email ) . '">' . "\n";
				echo '</div>' . "\n";
			echo '</li>' . "\n";

			// --- show phone number ---
			// 2.3.3.6: added Show phone number input field
			echo '<li class="show-item">' . "\n";
				echo '<div class="input-label"><label>' . "\n";
					echo esc_html( __( 'Show Phone', 'radio-station' ) ) . ':' . "\n";
				echo '</label></div>' . "\n";
				echo '<div class="input-field">' . "\n";
					echo '<input type="text" name="show_phone" size="100" style="max-width:80%;" value="' . esc_attr( $phone ) . '">' . "\n";
				echo '</div>' . "\n";
			echo '</li>' . "\n";

			// --- show latest audio ---
			echo '<li class="show-item">' . "\n";
				echo '<div class="input-label"><label>' . "\n";
					echo esc_html( __( 'Latest Audio File', 'radio-station' ) ) . ':' . "\n";
				echo '</label></div>' . "\n";
				echo '<div class="input-field">' . "\n";
					echo '<input type="text" name="show_file" size="100" style="max-width:80%;" value="' . esc_attr( $file ) . '">' . "\n";
				echo '</div>' . "\n";
			echo '</li>' . "\n";

			// --- disable download switch ---
			// 2.3.2: added show download disable field
			// 2.6.6: added missing checkbox item value
			echo '<li class="show-item">' . "\n";
				echo '<div class="input-label"><label>' . "\n";
					echo esc_html( __( 'Disable Download', 'radio-station' ) ) . '?' . "\n";
				echo '</label></div>' . "\n";
				echo '<div class="input-field">' . "\n";
					echo '<input type="checkbox" name="show_download" ' . checked( $download, 'on', false ) . ' value="on">' . "\n";
				echo '</div>';
			echo '</li>' . "\n";

			// 2.3.0: added patreon page field
			echo '<li class="show-item">' . "\n";
				echo '<div class="input-label"><label>'  . "\n";
					echo esc_html( __( 'Patreon Page ID', 'radio-station' ) ) . ':' . "\n";
				echo '</label></div>' . "\n";
				echo '<div class="input-field">' . "\n";
					echo ' https://patreon.com/<input type="text" name="show_patreon" size="80" style="max-width:50%;" value="' . esc_attr( $patreon_id ) . '">' . "\n";
				echo '</div>' . "\n";
			echo '</li>' . "\n";

			// 2.3.3.5: added action for further custom fields
			do_action( 'radio_station_show_fields', $post_id, 'show' );

		echo '</ul>' . "\n";

	// --- close meta inner box ---
	echo '</div>' . "\n";

	// --- inside show metaboxes ---
	// 2.3.0: move metaboxes together inside meta
	$inside_metaboxes = array(
		'hosts'     => array(
			'title'    => __( 'Show DJ(s) / Host(s)', 'radio-station' ),
			'callback' => 'radio_station_show_hosts_metabox',
		),
		'producers' => array(
			'title'    => __( 'Show Producer(s)', 'radio-station' ),
			'callback' => 'radio_station_show_producers_metabox',
		),
		'languages' => array(
			'title'    => __( 'Show Language(s)', 'radio-station' ),
			'callback' => 'radio_station_show_language_metabox',
		)
	);

	// --- display inside metaboxes ---
	echo '<div id="show-inside-metaboxes">' . "\n";

		$i = 1;
		foreach ( $inside_metaboxes as $key => $metabox ) {

			$classes = array( 'postbox' );
			if ( 1 == $i ) {
				$classes[] = 'first';
			} elseif ( count( $inside_metaboxes ) == $i ) {
				$classes[] = 'last';
			}
			$class = implode( ' ', $classes );

			echo '<div id="' . esc_attr( $key ) . '" class="' . esc_attr( $class ) . '">' . "\n";

				// echo '<button type="button" class="handlediv" aria-expanded="true">';
				// echo '<span class="screen-reader-text">' . esc_html( sprintf( __( 'Toggle panel: %s' ), $metabox['title'] ) ) . '</span>';
				// echo '<span class="toggle-indicator" aria-hidden="true"></span>';
				// echo '</button>';

				// 2.3.2: remove class="hndle" to prevent box sorting
				echo '<h2><span>' . esc_html( $metabox['title'] ) . '</span></h2>' . "\n";
				echo '<div class="inside">' . "\n";
					call_user_func( $metabox['callback'] );
				echo '</div>' . "\n";

			echo '</div>' . "\n";

			$i++;
		}

	echo '</div>' . "\n";

	// --- set metabox styles ---
	// 2.3.3.9: remove !important from display inline-block rule
	// 2.3.3.9: added show input list styles
	$css = ".show-input-list, .show-item {list-style: none;}
	.show-item .input-label, .show-item .input-field, .show-item .input-helper {display: inline-block;}
	.show-item .input-label {width: 120px;}
	.show-item .input-field, .show-item .input-helper {margin-left: 20px;}
	.show-item .input-field {max-width: 80%;}
	#show-inside-metaboxes .postbox {display: inline-block; min-width: 230px; max-width: 250px; vertical-align: top;}
	#show-inside-metaboxes .postbox.first {margin-right: 20px;}
	#show-inside-metaboxes .postbox.last {margin-left: 20px;}
	#show-inside-metaboxes .postbox select {max-width: 200px;}";

	// --- filter and output styles ---
	// 2.3.3.9: added edit styles filter
	// 2.5.0: added wp_kses_post to CSS output
	// 2.5.6: use radio_station_add_inline_style
	$css = apply_filters( 'radio_station_show_edit_styles', $css );
	// echo '<style>' . wp_kses_post( $css ) . '</style>';
	radio_station_add_inline_style( 'rs-admin', $css );
}

// ------------------------------
// Add Assign DJs to Show Metabox
// ------------------------------
// 2.3.0: move inside show meta selection metabox to reduce clutter
// add_action( 'add_meta_boxes', 'radio_station_add_show_hosts_metabox' );
function radio_station_add_show_hosts_metabox() {
	// 2.2.2: add high priority to show at top of edit sidebar
	// 2.3.0: change metabox title from DJs to DJs / Hosts
	add_meta_box(
		'radio-station-show-hosts-metabox',
		__( 'DJs / Hosts', 'radio-station' ),
		'radio_station_show_hosts_metabox',
		RADIO_STATION_SHOW_SLUG,
		'side',
		'high'
	);
}

// ----------------------------
// Assign Hosts to Show Metabox
// ----------------------------
function radio_station_show_hosts_metabox() {

	global $post;

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station', 'show_hosts_nonce' );

	// --- check for DJ / Host roles ---
	// 2.3.0: simplified by using role__in argument
	$args = array(
		'role__in' => array( 'dj', 'administrator' ),
		'orderby'  => 'display_name',
		'order'    => 'ASC'
	);
	$hosts = get_users( $args );

	// --- get the Hosts currently assigned to the show ---
	// 2.4.0.4: convert possible (old) non-array value
	$current = get_post_meta( $post->ID, 'show_user_list', true );
	if ( !$current ) {
		$current = array();
	} elseif ( !is_array( $current ) ) {
		$current = array( $current );
	}

	// --- move any selected Hosts to the top of the list ---
	foreach ( $hosts as $i => $host ) {
		// 2.2.8: remove strict in_array checking
		if ( in_array( $host->ID, $current ) ) {
			 // unset first, or prepending will change the index numbers and cause you to delete the wrong item
			unset( $hosts[$i] );
			  // prepend the user to the beginning of the array
			array_unshift( $hosts, $host );
		}
	}

	// --- Host Selection Input ---
	// 2.2.2: add fix to make DJ multi-select input full metabox width
	// 2.3.3.9: move meta_inner ID to class
	echo '<div class="meta_inner">' . "\n";
		echo '<select name="show_user_list[]" multiple="multiple" style="height: 120px; width: 100%;">' . "\n";
			echo '<option value=""></option>' . "\n";
			foreach ( $hosts as $host ) {
				// 2.2.2: set DJ display name maybe with username
				$display_name = $host->display_name;
				if ( $host->display_name !== $host->user_login ) {
					$display_name .= ' (' . $host->user_login . ')';
				}
				// 2.2.7: fix to remove unnecessary third argument
				// 2.2.8: removed unnecessary fix for non-strict check
				echo '<option value="' . esc_attr( $host->ID ) . '"';
				if ( in_array( $host->ID, $current ) ) {
					echo ' selected="selected"';
				}
				echo '>' . esc_html( $display_name ) . '</option>' . "\n";
			}
        echo '</select>' . "\n";

		// --- multiple selection helper text ---
		// 2.3.0: added multiple selection helper text
		echo '<div style="font-size:10px;">';
			echo esc_html( __( 'Ctrl-Click selects multiple.', 'radio-station' ) );
		echo '</div>' . "\n";

	echo '</div>' . "\n";
}

// ------------------------------------
// Add Assign Producers to Show Metabox
// ------------------------------------
// 2.3.0: move inside show meta selection metabox to reduce clutter
// add_action( 'add_meta_boxes', 'radio_station_add_show_producers_metabox' );
function radio_station_add_show_producers_metabox() {
	add_meta_box(
		'radio-station-show-producers-metabox',
		__( 'Show Producer(s)', 'radio-station' ),
		'radio_station_show_producers_metabox',
		RADIO_STATION_SHOW_SLUG,
		'side',
		'high'
	);
}

// --------------------------------
// Assign Producers to Show Metabox
// --------------------------------
function radio_station_show_producers_metabox() {

	global $post;

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station', 'show_producers_nonce' );

	// --- check for Producer roles ---
	$args = array(
		'role__in' => array( 'producer', 'administrator', 'show-editor' ),
		'orderby'  => 'display_name',
		'order'    => 'ASC'
	);
	$producers = get_users( $args );

	// --- get Producers currently assigned to the show ---
	$current = get_post_meta( $post->ID, 'show_producer_list', true );
	// 2.4.0.4: convert possible (old) non-array values
	if ( !$current ) {
		$current = array();
	} elseif ( !is_array( $current ) ) {
		$current = array( $current );
	}

	// --- move any selected DJs to the top of the list ---
	foreach ( $producers as $i => $producer ) {
		if ( in_array( $producer->ID, $current ) ) {
			// unset first, or prepending will change the index numbers and cause you to delete the wrong item
			unset( $producers[$i] );
			// prepend the user to the beginning of the array
			array_unshift( $producers, $producer );
		}
	}

	// --- Producer Selection Input ---
	// 2.3.3.9: move meta_inner ID to class
	echo '<div class="meta_inner">' . "\n";
		echo '<select name="show_producer_list[]" multiple="multiple" style="height:120px; width:100%;">' . "\n";
			echo '<option value=""></option>' . PHP_EOL;
			foreach ( $producers as $producer ) {
				$display_name = $producer->display_name;
				if ( $producer->display_name !== $producer->user_login ) {
					$display_name .= ' (' . $producer->user_login . ')';
				}
				echo '<option value="' . esc_attr( $producer->ID ) . '"';
				if ( in_array( $producer->ID, $current ) ) {
					echo ' selected="selected"';
				}
				echo '>' . esc_html( $display_name ) . '</option>' . "\n";
			}
		echo '</select>' . "\n";

		// --- multiple selection helper text ---
		echo '<div style="font-size: 10px;">';
			esc_html( __( 'Ctrl-Click selects multiple.', 'radio-station' ) );
		echo '</div>' . "\n";

	echo '</div>' . "\n";
}

// -----------------------
// Add Show Shifts Metabox
// -----------------------
// --- Adds schedule box to show edit screens ---
add_action( 'add_meta_boxes', 'radio_station_add_show_shifts_metabox' );
function radio_station_add_show_shifts_metabox() {
	// 2.2.2: change context to show at top of edit screen
	// 2.3.2: filter top metabox position
	$position = apply_filters( 'radio_station_metabox_position', 'rstop', 'shifts' );
	add_meta_box(
		'radio-station-show-shifts-metabox',
		__( 'Show Schedule', 'radio-station' ),
		'radio_station_show_shifts_metabox',
		RADIO_STATION_SHOW_SLUG,
		$position,
		'low'
	);
}

// -------------------
// Show Shifts Metabox
// -------------------
function radio_station_show_shifts_metabox() {

	global $post, $current_screen;

	// --- hidden debug fields ---
	// 2.3.2: added save debugging field
	if ( RADIO_STATION_DEBUG ) {
		echo '<input type="hidden" name="rs-debug" value="1">' . "\n";
	}
	if ( RADIO_STATION_SAVE_DEBUG ) {
		echo '<input type="hidden" name="rs-save-debug" value="1">' . "\n";
	}

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station', 'show_shifts_nonce' );

	// 2.3.3.9: move meta_inner ID to class
	echo '<div class="meta_inner">' . "\n";

		echo '<div id="shifts-list">' . "\n";

			// 2.3.2: added to bypass shift check on add (for debugging)
			if ( isset( $_REQUEST['check-bypass'] ) && ( '1' === sanitize_text_field( $_REQUEST['check-bypass'] ) ) ) {
				echo '<input type="hidden" name="check-bypass" value="1">' . "\n";
			}

			// --- output show shifts table ---
			// 2.3.2: separated table function (for AJAX saving)
			$table = radio_station_show_shifts_table( $post->ID );

			// --- show inactive message ---
			// 2.3.0: added show inactive reminder message
			if ( !$table['active'] ) {
				// 2.3.3.9: change to mismatched div class
				echo '<div class="show-inactive-message">' . "\n";
					echo '<b style="color:#EE0000;">' . esc_html( __( 'This Show is inactive!', 'radio-station' ) ) . '</b> ';
					echo esc_html( __( 'All Shifts are inactive also until Show is activated.', 'radio-station' ) );
				echo '</div>' . "\n";
			}

			// --- shift conflicts message ---
			// 2.3.0: added instructions for fixing shift conflicts
			// 2.3.3.9: check conflict count instead of boolean test
			if ( count( $table['conflicts'] ) > 0 ) {
				radio_station_shifts_conflict_message();
			}

			// --- output shift list ---
			if ( '' != $table['list'] ) {
				// 2.5.0: use wp_kses on table output
				$allowed = radio_station_allowed_html( 'content', 'settings' );
				echo wp_kses( $table['list'], $allowed );
			}

		echo '</div>' . "\n";

		// --- shift save/add buttons ---
		// 2.3.0: center add shift button
		// 2.3.2: added show shifts AJAX save button (for existing posts only)
		// 2.3.2: added show shifts clear button
		// 2.3.2: added table and shifts saved message
		// 2.3.2: fix centering by removing span wrapper
		// 2.3.2: change from button-primary to button-secondary
		// 2.3.3.9: change shift-table-buttons ID to shift-buttons class
		// echo '<a class="shift-add button-secondary" style="margin-top: 10px;">' . esc_html( __( 'Add Shift', 'radio-station' ) ) . '</a>';
		echo '<center><table class="shift-buttons" width="100%">' . "\n";
			echo '<tr>' . "\n";
				echo '<td width="33%" align="center">' . "\n";
					echo '<input type="button" class="shifts-clear button-secondary" value="' . esc_attr( __( 'Clear Shifts', 'radio-station' ) ) . '" onclick="radio_shifts_clear();">' . "\n";
				echo '</td>' . "\n";;
				echo '<td width="33%" align="center">' . "\n";
				if ( !is_object( $current_screen ) || ( 'add' != $current_screen->action ) ) {
					echo '<input type="button" class="shifts-save button-primary" value="' . esc_attr( __( 'Save Shifts', 'radio-station' ) ) . '" onclick="radio_shifts_save();">' . "\n";
				}
				echo '</td>';
				echo '<td width="33%" align="center">' . "\n";
					echo '<input type="button" class="shift-add button-secondary" value="' . esc_attr( __( 'Add Shift', 'radio-station' ) ) . '" onclick="radio_shift_new();">' . "\n";
				echo '</td>' . "\n";
			echo '</tr>' . "\n";

			// 2.3.3.9: change to single cell spanning 3 columns
			echo '<tr>' . "\n";
				echo '<td colspan="3" align="center">' . "\n";
					echo '<div id="shifts-saving-message" style="display:none;">' . esc_html( __( 'Saving Show Shifts...', 'radio-station' ) ) . '</div>' . "\n";
					echo '<div id="shifts-saved-message" style="display:none;">' . esc_html( __( 'Show Shifts Saved.', 'radio-station' ) ) . '</div>' . "\n";
					echo '<div id="shifts-error-message" style="display:none;"></div>' . "\n";
				echo '</td>' . "\n";
			echo '</tr>' . "\n";
		echo '</table></center>' . "\n";

		// --- get shift edit javascript ---
		// 2.3.3.9: moved to separate function
		$js = radio_station_shift_edit_script();

		// --- enqueue inline script ---
		// 2.3.0: enqueue instead of echoing
		// 2.5.0: use radio_station_add_inline_script
		radio_station_add_inline_script( 'radio-station-admin', $js );

		// --- shift display styles ---
		// 2.3.2: added dashed border to new shift
		// 2.3.3.9: moved styles to separate function
		// 2.5.0: use wp_kses_post on styles output
		// 2.5.6: use radio_station_add_inline_style
		$css = radio_station_shifts_list_styles();
		// echo '<style>' . wp_kses_post( $css ) . '</style>' . "\n";
		radio_station_add_inline_style( 'rs-admin', $css );

	// --- close meta inner ---
	echo '</div>' . "\n";
}

// ------------------
// Shifts List Styles
// ------------------
// 2.3.3.9: moved to separate function
function radio_station_shifts_list_styles() {

	// 2.3.3.9: change shift-table-buttons ID to shift-buttons class
	// 2.3.3.9: added maximum width for shift lists
	$css = "#shifts-list, #new-shifts {max-width: 960px;}
	#new-shifts .show-shift {border: 2px dashed green; background-color: #FFFFDD;}
	.show-shift {list-style: none; margin-bottom: 10px; border: 2px solid green; background-color: #EEFFEE;}
	.show-shift select.changed, .show-shift input[checkbox].changed {background-color: #FFFFCC;}
	.show-shift select option.original {font-weight: bold;}
	.show-shift li {display: inline-block; vertical-align: middle;
		margin-left: 20px; margin-top: 10px; margin-bottom: 10px;}
	.show-shift li.first-item {margin-left: 10px;}
	.show-shift li.last-item {margin-right: 10px;}
	.show-shift.changed, .show-shift.changed.disabled {background-color: #FFEECC;}
	.show-shift.disabled {border: 2px dashed orange; background-color: #FFDDDD;}
	.show-shift.conflicts {outline: 2px solid red;}
	.show-shift.disabled.conflicts {border: 2px dashed red;	outline: none;}
	.show-shift select.incomplete {border: 2px solid orange;}
	.shift-buttons .shifts-clear, .shift-buttons .shifts-save, .shift-buttons .shift-add {
		cursor: pointer; display:block; width: 150px; padding: 8px; text-align: center; line-height: 1em;}
	.shift-duplicate, .shift-remove {cursor: pointer;}
	#shifts-saving-message, #shifts-saved-message {
		background-color: lightYellow; border: 1px solid #E6DB55; margin-top: 10px; font-weight: bold; max-width: 300px; padding: 5px 0;}" . "\n";

	// 2.3.3.9: added shift edit styles filter
	$css = apply_filters( 'radio_station_shift_list_edit_styles', $css );
	return $css;
}

// -----------------
// Shift Edit Script
// -----------------
function radio_station_shift_edit_script() {

	// --- get meridiem translations ---
	// 2.2.7: added meridiem translations
	$am = radio_station_translate_meridiem( 'am' );
	$pm = radio_station_translate_meridiem( 'pm' );

	// --- set days, hours and minutes arrays ---
	// 2.5.0: add number_format_i18n to hours and minutes
	// 2.5.0: use get_schedule_weekdays to honour start of week value
	// $days = array( '', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
	$days = array_merge( array( '' ), radio_station_get_schedule_weekdays() );
	$hours = $mins = array();
	for ( $i = 1; $i < 13; $i++ ) {
		$hours[$i] = number_format_i18n( $i );
	}
	for ( $i = 0; $i < 60; $i++ ) {
		if ( $i < 10 ) {
			$min = number_format_i18n( 0 ) . number_format_i18n( $i );
			// 2.5.0: make sure values have leading 0
			$i = '0' . $i;
		} else {
			$min = number_format_i18n( $i );
		}
		$mins[$i] = $min;
	}

	// --- show shifts scripts ---
	// 2.3.0: added confirmation to remove shift button
	// 2.3.2: removed document ready functions wrapper
	// $c = 0;
	$confirm_remove = __( 'Are you sure you want to remove this shift?', 'radio-station' );
	$confirm_clear = __( 'Are you sure you want to clear the shift list?', 'radio-station' );
	// $js = "var count = " . esc_attr( $c ) . ";";

	// --- clear all shifts function ---
	$js = "function radio_shifts_clear() {
		if (jQuery('#shifts-list').children().length) {
			var agree = confirm('" . esc_js( $confirm_clear ) . "');
			if (!agree) {return false;}
			jQuery('#shifts-list').children().remove();
			jQuery('<div id=\"new-shifts\"></div>').appendTo('#shifts-list');
		}
	}" . "\n";

	// --- save shifts via AJAX ---
	// 2.3.2: added form input cloning to saving show shifts
	$ajaxurl = admin_url( 'admin-ajax.php' );
	$js .= "function radio_shifts_save() {
		action = 'radio_station_show_save_shifts';
		if (jQuery('#single-shift').length && jQuery('#single-shift').prop('checked')) {action = 'radio_station_show_save_shift';}
		jQuery('#shift-save-form, #shift-save-frame').remove();
		form = '<form id=\"shift-save-form\" method=\"post\" action=\"" . esc_url( $ajaxurl ) . "\" target=\"shift-save-frame\">';
		form += '<input type=\"hidden\" name=\"action\" value=\"'+action+'\"></form>';
		jQuery('#wpbody').append(form);
		if (!jQuery('#shift-save-frame').length) {
			frame = '<iframe id=\"shift-save-frame\" name=\"shift-save-frame\" src=\"javascript:void(0);\" style=\"display:none;\"></iframe>';
			jQuery('#wpbody').append(frame);
		}
		/* copy shifts input fields and nonce */
		jQuery('#shifts-list input').each(function() {jQuery(this).clone().appendTo('#shift-save-form');});
		jQuery('#shifts-list select').each(function() {
			name = jQuery(this).attr('name'); value = jQuery(this).children('option:selected').val();
			jQuery('<input type=\"hidden\" name=\"'+name+'\" value=\"'+value+'\">').appendTo('#shift-save-form');
		});
		jQuery('#show_shifts_nonce').clone().attr('id','').appendTo('#shift-save-form');
		jQuery('#post_ID').clone().attr('id','').attr('name','show_id').appendTo('#shift-save-form');
		if (jQuery('#shift_ID').length) {jQuery('#shift_ID').attr('id','').attr('name','shift_id').appendTo('#shift-save-form');}
		jQuery('#shifts-saving-message').show();
		jQuery('#shift-save-form').submit();
	}" . "\n";

	// --- check select change ---
	// 2.3.3: added select change detection
	$js .= "function radio_check_select(el) {
		val = el.options[el.selectedIndex].value;
		if (val == '') {jQuery('#'+el.id).addClass('incomplete');}
		else {jQuery('#'+el.id).removeClass('incomplete');}
		origid = el.id.replace('shift-','');
		origval = jQuery('#'+origid).val();
		if (val == origval) {jQuery('#'+el.id).removeClass('changed');}
		else {jQuery('#'+el.id).addClass('changed');}
		uid = origid.substr(0,8);
		radio_check_shift(uid);
	}" . "\n";

	// --- check checkbox change ---
	// 2.3.3: added checkbox change detection
	$js .= "function radio_check_checkbox(el) {
		val = el.checked ? 'on' : '';
		origid = el.id.replace('shift-','');
		origval = jQuery('#'+origid).val();
		if (val == origval) {jQuery('#'+el.id).removeClass('changed');}
		else {jQuery('#'+el.id).addClass('changed');}
		uid = origid.substr(0,8);
		radio_check_shift(uid);
	}" . "\n";

	// --- check shift change ---
	// 2.3.3: added shift change detection
	$js .= "function radio_check_shift(id) {
		var shiftchanged = false;
		jQuery('#shift-'+id).find('select,input').each(function() {
			if ( (jQuery(this).attr('id').indexOf('shift-') == 0) && (jQuery(this).hasClass('changed')) ) {
				shiftchanged = true;
			}
		});
		if (shiftchanged) {jQuery('#shift-'+id).addClass('changed');}
		else {jQuery('#shift-'+id).removeClass('changed');}
		radio_check_shifts();
	}" . "\n";

	// 2.3.3.6: store possible existing onbeforeunload function
	// (to help prevent conflicts with other plugins using this event)
	/* $js .= "var storedonbeforeunload = null; var onbeforeunloadset = false;" . "\n";
	$js .= "function radio_check_shifts() {
		if (jQuery('.show-shift.changed').length) {
			if (!onbeforeunloadset) {
				storedonbeforeunload = window.onbeforeunload;
				window.onbeforeunload = function() {return true;}
				onbeforeunloadset = true;
			}
		} else {
			if (onbeforeunloadset) {
				window.onbeforeunload = storedonbeforeunload;
				onbeforeunloadset = false;
			}
		}
	}" . "\n"; */

	// 2.5.0: replace window.onbeforeunload merhod with event listener
	// ref: https://stackoverflow.com/a/58841521/5240159
	$js .= "function radio_check_shifts() {
		if (jQuery('.show-shift.changed').length) {
			window.addEventListener('beforeunload', radio_onbeforeunload);
		} else {
			window.removeEventListener('beforeunload', radio_onbeforeunload);
		}
	}" . "\n";

	// --- onbeforeunload event function ---
	// (this display ~"do you want to leave this page?" dialogue)
	$js .= "function radio_onbeforeunload(e) {
		e.preventDefault();
		e.returnValue = '';
	}" . "\n";

	// --- remove event listener on publish/update ---
	// 2.5.0: added so publish/update does not trigger event
	$js .= "jQuery(document).ready(function() {
		jQuery('#publish').on('click', function() {
			window.removeEventListener('beforeunload', radio_onbeforeunload);
		});
	});" . "\n";

	// --- add new shift ---
	// 2.3.2: separate function for onclick
	// 2.5.0: shorten value object
	$js .= "function radio_shift_new() {
		values = {day:'', start_hour:'', start_min:'', start_meridian:'', end_hour:'', end_min:'', end_meridian:'', encore:'', disabled:''};
		radio_shift_add(values);
	}" . "\n";

	// --- remove shift ----
	// 2.3.2: separate function for onclick
	// 2.3.3: fix to jQuery targeting for new shifts
	$js .= "function radio_shift_remove(el) {
		agree = confirm('" . esc_js( $confirm_remove ) . "');
		if (!agree) {return false;}
		shiftid = el.id.replace('shift-','').replace('-remove','');
		jQuery('#'+el.id).closest('.shift-wrapper').remove();
	}" . "\n";

	// --- duplicate shift ---
	// 2.3.2: separate function for onclick
	$js .= "function radio_shift_duplicate(el) {
		shiftid = el.id.replace('shift-','').replace('-duplicate','');
		values = {};
		values.day = jQuery('#shift-'+shiftid+'-day').val();
		values.start_hour = jQuery('#shift-'+shiftid+'-start-hour').val();
		values.start_min = jQuery('#shift-'+shiftid+'-start-min').val();
		values.start_meridian = jQuery('#shift-'+shiftid+'-start-meridian').val();
		values.end_hour = jQuery('#shift-'+shiftid+'-end-hour').val();
		values.end_min = jQuery('#shift-'+shiftid+'-end-min').val();
		values.end_meridian = jQuery('#shift-'+shiftid+'-end-meridian').val();
		values.encore = '';
		if (jQuery('#shift-'+shiftid+'-encore').prop('checked')) {values.encore = 'on';}
		values.disabled = 'yes';
		radio_shift_add(values);
	}" . "\n";

	// --- add shift function ---
	// 2.3.2: added missing shift wrapper class
	// 2.3.2: set new count based on new shift children
	// 2.3.3: add input IDs so new shifts can be duplicated
	$js .= "function radio_shift_add(values) {" . "\n";
		$js .= "var count = jQuery('#new-shifts').children().length + 1;" . "\n";
		$js .= "output = '<div id=\"shift-wrapper-new' + count + '\" class=\"shift-wrapper\">';" . "\n";
		$js .= "output += '<ul id=\"shift-' + count + '\" class=\"show-shift new-shift\">';" . "\n";
		$js .= "output += '<li class=\"shift-item first-item\">';" . "\n";
		$js .= "output += '<label>" . esc_js( __( 'Day', 'radio-station' ) ) . "</label>: ';" . "\n";
			$js .= "output += '<select id=\"shift-new' + count + '-day\" name=\"show_sched[new-' + count + '][day]\" id=\"shift-new-' + count +'-day\">';" . "\n";
				// --- shift days ---
				// 2.3.0: simplify by looping days and add translation
				foreach ( $days as $day ) {
					$js .= "output += '<option value=\"" . esc_js( $day ) . "\"';" . "\n";
					$js .= "if (values.day == '" . esc_js( $day ) . "') {output += ' selected=\"selected\"';}" . "\n";
					$js .= "output += '>" . esc_js( radio_station_translate_weekday( $day ) ) . "</option>';" . "\n";
				}
			$js .= "output += '</select>'" . "\n";
		$js .= "output += '</li>';" . "\n";

		// --- start time ---
		$js .= "output += '<li class=\"shift-item\">';" . "\n";
			$js .= "output += '<label>" . esc_js( __( 'Start Time', 'radio-station' ) ) . "</label>: ';" . "\n";
			// --- start hour ---
			$js .= "output += '<select name=\"show_sched[new-' + count + '][start_hour]\" id=\"shift-new-' + count + '-start-hour\" style=\"min-width:35px;\">';" . "\n";
				// 2.5.0: use possible translated hour label
				foreach ( $hours as $hour => $label ) {
					$js .= "output += '<option value=\"" . esc_js( $hour ) . "\"';" . "\n";
					$js .= "if (values.start_hour == '" . esc_js( $hour ) . "') {output += ' selected=\"selected\"';}" . "\n";
					$js .= "output += '>" . esc_js( $label ) . "</option>';" . "\n";
				}
			$js .= "output += '</select> ';" . "\n";
			// --- start minutes ---
			$js .= "output += '<select name=\"show_sched[new-' + count + '][start_min]\" id=\"shift-new-' + count + '-start-min\" style=\"min-width:35px;\">';" . "\n";
				$js .= "output += '<option value=\"00\">00</option><option value=\"15\">15</option><option value=\"30\">30</option><option value=\"45\">45</option>';" . "\n";
				// 2.5.0: use possible translated minute label
				foreach ( $mins as $min => $label ) {
					$js .= "output += '<option value=\"" . esc_js( $min ) . "\"';" . "\n";
					$js .= "if (values.start_min == '" . esc_js( $min ) . "') {output += ' selected=\"selected\"';}" . "\n";
					$js .= "output += '>" . esc_js( $label ) . "</option>';" . "\n";
				}
			$js .= "output += '</select>';" . "\n";
			// --- start meridian ---
			$js .= "output += '<select name=\"show_sched[new-' + count + '][start_meridian]\" id=\"shift-new-' + count + '-start-meridian\" style=\"min-width:35px;\">';" . "\n";
				$js .= "output += '<option value=\"am\"';" . "\n";
				$js .= "if (values.start_meridian == '" . esc_js( $am ) . "') {output += ' selected=\"selected\"';}" . "\n";
				$js .= "output += '>" . esc_js( $am ) . "</option>';" . "\n";
				$js .= "output += '<option value=\"pm\"';" . "\n";
				$js .= "if (values.start_meridian == '" . esc_js( $pm ) . "') {output += ' selected=\"selected\"';}" . "\n";
				$js .= "output += '>" . esc_js( $pm ) . "</option>'" . "\n";
			$js .= "output += '</select> '" . "\n";
		$js .= "output += '</li>';" . "\n";

		// --- end time ---
		$js .= "output += '<li class=\"shift-item\">';" . "\n";
			// --- end hour ---
			$js .= "output += '<label>" . esc_js( __( 'End Time', 'radio-station' ) ) . "</label>: '" . "\n";
			$js .= "output += '<select name=\"show_sched[new-' + count + '][end_hour]\" id=\"shift-new-' + count + '-end-hour\" style=\"min-width:35px;\">';" . "\n";
				// 2.5.0: use possible translated hour label
				foreach ( $hours as $hour => $label ) {
					$js .= "output += '<option value=\"" . esc_js( $hour ) . "\"';" . "\n";
					$js .= "if (values.end_hour == '" . esc_js( $hour ) . "') {output += ' selected=\"selected\"';}" . "\n";
					$js .= "output += '>" . esc_js( $label ) . "</option>';" . "\n";
				}
			$js .= "output += '</select> ';" . "\n";
			// --- end min ---
			$js .= "output += '<select name=\"show_sched[new-' + count + '][end_min]\" id=\"shift-new-' + count + '-end-min\" style=\"min-width:35px;\">';" . "\n";
				$js .= "output += '<option value=\"00\">00</option><option value=\"15\">15</option><option value=\"30\">30</option><option value=\"45\">45</option>';" . "\n";
				// 2.5.0: use possible translated hour label
				foreach ( $mins as $min => $label ) {
					$js .= "output += '<option value=\"" . esc_js( $min ) . "\"';" . "\n";
					$js .= "if (values.end_min == '" . esc_js( $min ) . "') {output += ' selected=\"selected\"';}" . "\n";
					$js .= "output += '>" . esc_js( $label ) . "</option>';" . "\n";
				}
			$js .= "output += '</select> ';" . "\n";
			// --- end meridian ---
			$js .= "output += '<select name=\"show_sched[new-' + count + '][end_meridian]\" id=\"shift-new-' + count + '-end-meridian\" style=\"min-width:35px;\">';" . "\n";
				$js .= "output += '<option value=\"am\"';" . "\n";
				$js .= "if (values.end_meridian == '" . esc_js( $am ) . "') {output += ' selected=\"selected\"';}" . "\n";
				$js .= "output += '>" . esc_js( $am ) . "</option>';" . "\n";
				$js .= "output += '<option value=\"pm\"';" . "\n";
				$js .= "if (values.end_meridian == '" . esc_js( $pm ) . "') {output += ' selected=\"selected\"';}" . "\n";
				$js .= "output += '>" . esc_js( $pm ) . "</option>';" . "\n";
			$js .= "output += '</select> ';" . "\n";
		$js .= "output += '</li>';" . "\n";

		// --- encore ---
		$js .= "output += '<li class=\"shift-item shift-encore\">';" . "\n";
			$js .= "output += '<input type=\"checkbox\" value=\"on\" name=\"show_sched[new-' + count + '][encore]\" id=\"shift-new-' + count + '-encore\"';" . "\n";
			$js .= "if (values.encore == 'on') {output += ' checked=\"checked\"';}" . "\n";
			$js .= "output += '> <label>" . esc_js( __( 'Encore', 'radio-station' ) ) . "</label>';" . "\n";
		$js .= "output += '</li>';" . "\n";

		// --- disabled shift ---
		$js .= "output += '<li class=\"shift-item shift-disable\">';" . "\n";
			$js .= "output += '<input type=\"checkbox\" value=\"yes\" name=\"show_sched[new-' + count + '][disabled]\" id=\"shift-new-' + count + '-disabled\"';" . "\n";
			$js .= "if (values.disabled != '') {output += ' checked=\"checked\"';}" . "\n";
			$js .= "output += '> <label>" . esc_js( __( 'Disabled', 'radio-station' ) ) . "</label>';" . "\n";
		$js .= "output += '</li>';" . "\n";

		// --- duplicate shift ---
		$js .= "output += '<li class=\"shift-item\">';" . "\n";
		$js .= "output += '<span id=\"shift-new' + count + '-duplicate\" class=\"shift-duplicate dashicons dashicons-admin-page\" title=\"" . esc_js( __( 'Duplicate Shift', 'radio-station' ) ) . "\" onclick=\"radio_shift_duplicate(this);\"></span>';" . "\n";
		$js .= "output += '</li>';" . "\n";

		// --- remove shift ---
		$js .= "output += '<li class=\"shift-item last-item\">';" . "\n";
		$js .= "output += '<span id=\"shift-new' + count + '-remove\" class=\"shift-remove dashicons dashicons-no\" title=\"" . esc_js( __( 'Remove Shift', 'radio-station' ) ) . "\" onclick=\"radio_shift_remove(this);\"></span>'" . "\n";
		$js .= "output += '</li>';" . "\n";

		// --- append new shift list item ---
		$js .= "output += '</ul></div>';" . "\n";
		$js .= "jQuery('#new-shifts').append(output);" . "\n";
		$js .= "return false;" . "\n";
	$js .= "}" . "\n";

	$js = apply_filters( 'radio_station_shift_edit_script', $js );
	return $js;
}

// -----------------------
// Shifts Oonflict Message
// -----------------------
// 2.3.3.9: add hidden argument
function radio_station_shifts_conflict_message( $hidden = false ) {

	echo '<div class="shift-conflicts-message" style="display:none;">' . "\n";
		echo '<b style="color:#EE0000;">' . esc_html( __( 'Warning! Show Shift Conflicts were detected!', 'radio-station' ) ) . '</b><br>' . "\n";
		echo esc_html( __( 'Please note that Shifts with conflicts are automatically disabled upon saving.', 'radio-station' ) ) . "\n";
		echo esc_html( __( 'Fix the Shift and/or the Shift on the conflicting Show and Update them both.', 'radio-station' ) ) . "\n";
		echo esc_html( __( 'Then you can uncheck the shift Disable box and Save again to re-enable the Shift.', 'radio-station' ) ) . '<br>' . "\n";
		// TODO: add more information blog post / documentation link ?
		// echo '<a href="' . RADIO_STATION_DOC_URL . '/manage/#shift-conflict-checking" target="_blank">' . esc_html( __( 'Show Documentation', 'radio-station' ) ) . '</a>';
	echo '</div><br>';
}

// ----------------
// Show Shift Table
// ----------------
// 2.3.2: separate shift table function (for AJAX saving)
function radio_station_show_shifts_table( $post_id ) {

	// --- edit show link ---
	$edit_link = add_query_arg( 'action', 'edit', admin_url( 'post.php' ) );

	// 2.2.7: added meridiem translations
	$am = radio_station_translate_meridiem( 'am' );
	$pm = radio_station_translate_meridiem( 'pm' );

	// --- set days, hours and minutes arrays ---
	// 2.5.0: add number_format_i18n to hours and minutes
	// 2.5.0: use get_schedule_weekdays to honour start of week value
	// $days = array( '', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
	$days = array_merge( array( '' ), radio_station_get_schedule_weekdays() );
	$hours = $mins = array();
	for ( $i = 1; $i < 13; $i++ ) {
		$hours[$i] = number_format_i18n( $i );
	}
	for ( $i = 0; $i < 60; $i++ ) {
		if ( $i < 10 ) {
			$min = number_format_i18n( 0 ) . number_format_i18n( $i );
			// 2.5.0: make sure values have leading 0
			$i = '0' . $i;
		} else {
			$min = number_format_i18n( $i );
		}
		$mins[$i] = $min;
	}

	// --- get the saved meta as an array ---
	$active = get_post_meta( $post_id, 'show_active', true );
	$shifts = radio_station_get_show_schedule( $post_id );

	if ( !$shifts || !is_array( $shifts ) || ( 0 == count( $shifts ) ) ) {

		// --- set empty show shift array ---
		$times = array(
			'day'            => '',
			'start_hour'     => '',
			'start_min'      => '',
			'start_meridian' => '',
			'end_hour'       => '',
			'end_min'        => '',
			'end_meridian'   => '',
			'encore'         => '',
			'disabled'       => '',
		);

		// --- check and sanitize possibly posted times ---
		// 2.3.3.9: for adding new show shift by querystring
		if ( isset( $_REQUEST['day'] ) ) {
			// 2.5.0: added sanitize_text_field to request field
			$times['day'] = sanitize_text_field( $_REQUEST['day'] );
			if ( !in_array( $times['day'], $days ) ) {
				$times['day'] = '';
			}
		}
		if ( isset( $_REQUEST['start_hour'] ) ) {
			// 2.5.6: fix to key instead of variable
			$times['start_hour'] = absint( $_REQUEST['start_hour'] );
			if ( ( $times['start_hour'] < 1 ) || ( $times['start_hour'] > 12 ) ) {
				$times['start_hour'] = 12;
			}
		}
		if ( isset( $_REQUEST['start_min'] ) ) {
			$times['start_min'] = absint( $_REQUEST['start_min'] );
			if ( ( $times['start_min'] < 1 ) || ( $times['start_min'] > 60 ) ) {
				$times['start_min'] = 0;
			}
			if ( $times['start_min'] < 10 ) {
				$times['start_min'] = '0' . $times['start_min'];
			}
		}
		if ( isset( $_REQUEST['start_meridian'] ) ) {
			// 2.5.0: added sanitize_text_field to request field
			$times['start_meridian'] = sanitize_text_field( $_REQUEST['start_meridian'] );
			if ( !in_array( $times['start_meridian'], array( '', 'am', 'pm' ) ) ) {
				$times['start_meridian'] = '';
			}
		}
		if ( isset( $_REQUEST['end_hour'] ) ) {
			$times['end_hour'] = absint( $_REQUEST['end_hour'] );
			if ( ( $times['end_hour'] < 1 ) || ( $times['end_hour'] > 12 ) ) {
				$times['end_hour'] = 12;
			}
		}
		if ( isset( $_REQUEST['end_min'] ) ) {
			$times['end_min'] = absint( $_REQUEST['end_min'] );
			if ( ( $times['end_min'] < 1 ) || ( $times['end_min'] > 60 ) ) {
				$times['end_min'] = 0;
			}
			if ( $times['end_min'] < 10 ) {
				$times['end_min'] = '0' . $times['end_min'];
			}
		}
		if ( isset( $_REQUEST['end_meridian'] ) ) {
			// 2.5.0: added sanitize_text_field to request field
			$times['end_meridian'] = sanitize_text_field( $_REQUEST['end_meridian'] );
			if ( !in_array( $times['end_meridian'], array( '', 'am', 'pm' ) ) ) {
				$times['end_meridian'] = '';
			}
		}
		if ( isset( $_REQUEST['encore'] ) ) {
			// 2.5.0: added sanitize_text_field to request field
			$times['encore'] = sanitize_text_field( $_REQUEST['encore'] );
			if ( !in_array( $times['encore'], array( '', 'on' ) ) ) {
				$times['encore'] = '';
			}
		}
		if ( isset( $_REQUEST['disabled'] ) ) {
			$times['disabled'] = sanitize_text_field( $_REQUEST['disabled'] );
			if ( !in_array( $times['disabled'], array( '', 'yes' ) ) ) {
				$times['disabled'] = '';
			}
		}

		$unique_id = radio_station_unique_shift_id();
		$shifts = array( $unique_id => $times );
	}

	$check_conflicts = radio_station_get_setting( 'conflict_checker' );
	$conflict_list = array();
	$list = '';
	if ( isset( $shifts ) && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {

		// 2.2.7: soft shifts by start day and time for ordered display
		// 2.3.0: add shift index to prevent start time overwriting
		// 2.3.3.9: fixed by moving extra shift index outside loop
		$j = 1;
		foreach ( $shifts as $unique_id => $shift ) {
			$shift['unique_id'] = $unique_id;
			$shift_start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
			$shift_start = radio_station_convert_shift_time( $shift_start );
			if ( isset( $shift['day'] ) && ( '' != $shift['day'] ) ) {
				// --- group shifts by days of week ---
				$starttime = radio_station_to_time( 'next ' . $shift['day'] . ' ' . $shift_start );
				// 2.3.0: simplify by getting day index
				$i = array_search( $shift['day'], $days );
				$day_shifts[$i][$starttime . '.' . $j] = $shift;
			} else {
				// --- to still allow shift time sorting if day is not set ---
				$starttime = radio_station_to_time( '1981-04-28 ' . $shift_start );
				$day_shifts[7][$starttime . '.' . $j] = $shift;
			}
			$j++;
		}

		// --- sort day shifts by day and time ---
		ksort( $day_shifts );
		// 2.3.0: resort order using start of week
		$sorted_shifts = array();
		$weekdays = radio_station_get_schedule_weekdays();
		foreach ( $weekdays as $i => $weekday ) {
			if ( isset( $day_shifts[$i] ) ) {
				$sorted_shifts[$i] = $day_shifts[$i];
			}
		}
		if ( isset( $day_shifts[7] ) ) {
			$sorted_shifts[7] = $day_shifts[7];
		}

		// --- sort shifts by (unique) start time for each day ---
		$show_shifts = array();
		foreach ( $sorted_shifts as $shift_day => $day_shift ) {
			ksort( $day_shift );
			foreach ( $day_shift as $key => $shift ) {
				$unique_id = $shift['unique_id'];
				unset( $shift['unique_id'] );
				$show_shifts[$unique_id] = $shift;
			}
		}

		if ( RADIO_STATION_DEBUG ) {
			echo 'Sorted Shifts: ' . esc_html( print_r( $sorted_shifts, true ) ) . "\n";
			echo 'Resorted Shifts: ' . esc_html( print_r( $show_shifts, true ) ) . "\n";
		}

		// --- loop ordered show shifts ---
		foreach ( $show_shifts as $unique_id => $shift ) {

			$classes = array( 'show-shift' );

			// --- check conflicts with other show shifts ---
			// 2.3.0: added shift conflict checking
			// 2.5.6: check shift conflict setting
			if ( 'yes' == $check_conflicts ) {
				$conflicts = radio_station_check_shift( $post_id, $shift );
				if ( $conflicts && is_array( $conflicts ) ) {
					// 2.3.3.9: store conflicts by shift ID in array list
					$conflict_list[$unique_id] = $conflicts;
					$classes[] = 'conflicts';
				}
			}

			// --- check if shift disabled ---
			// 2.3.0: check shift disabled switch or show inactive
			if ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) {
				$classes[] = 'disabled';
			} elseif ( !$active ) {
				$classes[] = 'disabled';
			}
			$classlist = implode( " ", $classes );

			$list .= '<div id="shift-wrapper-' . esc_attr( $unique_id ) . '" class="shift-wrapper">' . "\n";

				$list .= '<ul id="shift-' . esc_attr( $unique_id ) . '" class="' . esc_attr( $classlist ) . '">' . "\n";

					// --- shift day selection ---
					$list .= '<li class="shift-item first-item">' . "\n";
						$list .= '<label>' . esc_html( __( 'Day', 'radio-station' ) ) . '</label>: ' . "\n";
						$class = ( '' == $shift['day'] ) ? 'incomplete' : '';
						$list .= '<select id="shift-' . esc_attr( $unique_id ) . '-day" class="' . esc_attr( $class ) . '" name="show_sched[' . esc_attr( $unique_id ) . '][day]" onchange="radio_check_select(this);">' . "\n";
							// 2.3.0: simplify by looping days
							foreach ( $days as $day ) {
								// 2.3.0: add weekday translation to display
								$list .= '<option value="' . esc_attr( $day ) . '" ' . selected( $day, $shift['day'], false ) . '>' . "\n";
								$list .= esc_html( radio_station_translate_weekday( $day ) ) . '</option>' . "\n";
							}
						$list .= '</select>' . "\n";
						$list .= '<input type="hidden" id="' . esc_attr( $unique_id ) . '-day" value="' . esc_attr( $shift['day'] ) . '">' . "\n";
					$list .= '</li>' . "\n";

					// --- shift start time ---
					$list .= '<li class="shift-item">' . "\n";
						$list .= '<label>' . esc_html( __( 'Start Time', 'radio-station' ) ) . '</label>: ' . "\n";

						// --- start hour selection ---
						$class = ( '' == $shift['start_hour'] ) ? 'incomplete' : '';
						$list .= '<select id="shift-' . esc_attr( $unique_id ) . '-start-hour" class="' . esc_attr( $class ) . '" name="show_sched[' . esc_attr( $unique_id ) . '][start_hour]" onchange="radio_check_select(this);" style="min-width:35px;">' . "\n";
						// 2.5.0: use (possibly translated) hours
						foreach ( $hours as $hour => $label ) {
							$class = ( $hour == $shift['start_hour'] ) ? 'original' : '';
							// 2.5.0: added number format internationalization
							$list .= '<option class="' . esc_attr( $class ) . '" value="' . esc_attr( $hour ) . '" ' . selected( $hour, $shift['start_hour'], false ) . '>' . esc_html( $label ) . '</option>' . "\n";
						}
						$list .= '</select>' . "\n";
						$list .= '<input type="hidden" id="' . esc_attr( $unique_id ) . '-start-hour" value="' . esc_attr( $shift['start_hour'] ) . '">' . "\n";

						// --- start minute selection ---
						$list .= '<select id="shift-' . esc_attr( $unique_id ) . '-start-min" name="show_sched[' . esc_attr( $unique_id ) . '][start_min]" onchange="radio_check_select(this);" style="min-width:35px;">' . "\n";
							// $list .= '<option value=""></option>' . "\n";
							$list .= '<option value="00">' . esc_html( number_format_i18n( 0 ) . number_format_i18n( 0 ) ) . '</option>' . "\n";
							$list .= '<option value="15">' . esc_html( number_format_i18n( 15 ) ) . '</option>' . "\n";
							$list .= '<option value="30">' . esc_html( number_format_i18n( 30 ) ) . '</option>' . "\n";
							$list .= '<option value="45">' . esc_html( number_format_i18n( 45 ) ) . '</option>' . "\n";
							// 2.5.0: use (possibly translated) minutes
							foreach ( $mins as $min => $label ) {
								$class = ( $min == $shift['start_min'] ) ? 'original' : '';
								// 2.5.0: added number format internationalization
								$list .= '<option class="' . esc_attr( $class ) . '" value="' . esc_attr( $min ) . '" ' . selected( $min, $shift['start_min'], false ) . '>' . esc_html( $label ) . '</option>' . "\n";
							}
						$list .= '</select>' . "\n";
						$list .= '<input type="hidden" id="' . esc_attr( $unique_id ) . '-start-min" value="' . esc_attr( $shift['start_min'] ) . '">' . "\n";

						// --- start meridiem selection ---
						$class = ( '' == $shift['start_meridian'] ) ? 'incomplete' :  '';
						$list .= '<select id="shift-' . esc_attr( $unique_id ) . '-start-meridian" class="' . esc_attr( $class ) . '" name="show_sched[' . esc_attr( $unique_id ) . '][start_meridian]" onchange="radio_check_select(this);" style="min-width:35px;">' . "\n";
							$class = ( 'am' == $shift['start_meridian'] ) ? 'original' : '';
							$list .= '<option class="' . esc_attr( $class ) . '" value="am" ' . selected( $shift['start_meridian'], 'am', false ) . '>' . esc_html( $am ) . '</option>' . "\n";
							$class = ( 'pm' == $shift['start_meridian'] ) ? 'original' : '';
							$list .= '<option class="' . esc_attr( $class ) . '" value="pm" ' . selected( $shift['start_meridian'], 'pm', false ) . '>' . esc_html( $pm ) . '</option>' . "\n";
						$list .= '</select>' . "\n";
						$list .= '<input type="hidden" id="' . esc_attr( $unique_id ) . '-start-meridian" value="' . esc_attr( $shift['start_meridian'] ) . '">' . "\n";

					$list .= '</li>' . "\n";

					// --- shift end time ---
					$list .= '<li class="shift-item">' . "\n";
						$list .= '<label>' . esc_html( __( 'End Time', 'radio-station' ) ) . '</label>: ' . "\n";

						// --- end hour selection ---
						$class = ( '' == $shift['end_hour'] ) ? 'incomplete' : '';
						$list .= '<select id="shift-' . esc_attr( $unique_id ) . '-end-hour" class="' . esc_attr( $class ) . '" name="show_sched[' . esc_attr( $unique_id ) . '][end_hour]" onchange="radio_check_select(this);" style="min-width:35px;">' . "\n";
							// 2.5.0: use (possibly translated) minutes
							foreach ( $hours as $hour => $label ) {
								$class = ( $hour == $shift['end_hour'] ) ? 'original' : '';
								$list .= '<option class="' . esc_attr( $class ) . '" value="' . esc_attr( $hour ) . '" ' . selected( $shift['end_hour'], $hour, false ) . '>' . esc_html( $label ) . '</option>' . "\n";
							}
						$list .= '</select>' . "\n";
						$list .= '<input type="hidden" id="' . esc_attr( $unique_id ) . '-end-hour" value="' . esc_attr( $shift['end_hour'] ) . '">' . "\n";

					// --- end minute selection ---
					$list .= '<select id="shift-' . esc_attr( $unique_id ) . '-end-min" name="show_sched[' . esc_attr( $unique_id ) . '][end_min]" onchange="radio_check_select(this);" style="min-width:35px;">' . "\n";
						$list .= '<option value=""></option>' . "\n";
						$list .= '<option value="00">' . esc_html( number_format_i18n( 0 ) . number_format_i18n( 0 ) ) . '</option>' . "\n";
						$list .= '<option value="15">' . esc_html( number_format_i18n( 15 ) ) . '</option>' . "\n";
						$list .= '<option value="30">' . esc_html( number_format_i18n( 30 ) ) . '</option>' . "\n";
						$list .= '<option value="45">' . esc_html( number_format_i18n( 45 ) ) . '</option>' . "\n";
						// 2.5.0: use (possibly translated) minutes
						foreach ( $mins as $min => $label ) {
							$class = ( $min == $shift['end_min'] ) ? 'original' : '';
							$list .= '<option class="' . esc_attr( $class ) . '" value="' . esc_attr( $min ) . '" ' . selected( $shift['end_min'], $min, false ) . '>' . esc_html( $label ) . '</option>' . "\n";
						}
					$list .= '</select>' . "\n";
					$list .= '<input type="hidden" id="' . esc_attr( $unique_id ) . '-end-min" value="' . esc_attr( $shift['end_min'] ) . '">' . "\n";

					// --- end meridiem selection ---
					$class = ( '' == $shift['end_meridian'] ) ? 'incomplete' : '';
						$list .= '<select id="shift-' . esc_attr( $unique_id ) . '-end-meridian" class="' . esc_attr( $class ) . '" name="show_sched[' . esc_attr( $unique_id ) . '][end_meridian]" onchange="radio_check_select(this);" style="min-width:35px;">' . "\n";
							$class = ( 'am' == $shift['end_meridian'] ) ? 'original' : '';
							$list .= '<option class="' . esc_attr( $class ) . '" value="am" ' . selected( $shift['end_meridian'], 'am', false ) . '>' . esc_html( $am ) . '</option>' . "\n";
							$class = ( 'pm' == $shift['end_meridian'] ) ? 'original' : '';
							$list .= '<option class="' . esc_attr( $class ) . '" value="pm" ' . selected( $shift['end_meridian'], 'pm', false ) . '>' . esc_html( $pm ) . '</option>' . "\n";
						$list .= '</select>' . "\n";
						$list .= '<input type="hidden" id="' . esc_attr( $unique_id ) . '-end-meridian" value="' . esc_attr( $shift['end_meridian'] ) . '">' . "\n";
					$list .= '</li>' . "\n";

					// --- encore presentation ---
					if ( !isset( $shift['encore'] ) ) {
						$shift['encore'] = '';
					}
					$list .= '<li class="shift-item shift-encore">' . "\n";
						$list .= '<input id="' . esc_attr( $unique_id ) . '-encore" type="checkbox" value="on" name="show_sched[' . esc_attr( $unique_id ) . '][encore]" id="shift-' . esc_attr( $unique_id ) . '-encore"' . checked( $shift['encore'], 'on', false ) . ' onchange="radio_check_checkbox(this);">' . "\n";
						$list .= ' <label>' . esc_html( __( 'Encore', 'radio-station' ) ) . '</label>' . "\n";
						$list .= '<input type="hidden" id="' . esc_attr( $unique_id ) . '-encore" value="' . esc_attr( $shift['encore'] ) . '">' . "\n";
					$list .= '</li>' . "\n";

					// --- shift disabled ---
					// 2.3.0: added disabled checkbox to shift row
					if ( !isset( $shift['disabled'] ) ) {
						$shift['disabled'] = '';
					}
					$list .= '<li class="shift-item shift-disable">' . "\n";
						$list .= '<input id="' . esc_attr( $unique_id ) . '-disabled" type="checkbox" value="yes" name="show_sched[' . esc_attr( $unique_id ) . '][disabled]" id="shift-' . esc_attr( $unique_id ) . '-disabled"' . checked( $shift['disabled'], 'yes', false ) . ' onchange="radio_check_checkbox(this);">' . "\n";
						$list .= ' <label>' . esc_html( __( 'Disabled', 'radio-station' ) ) . '</label>' . "\n";
						$list .= '<input type="hidden" id="' . esc_attr( $unique_id ) . '-disabled" value="' . esc_attr( $shift['disabled'] ) . '">' . "\n";
					$list .= '</li>' . "\n";

					// --- duplicate shift icon ---
					// 2.3.0: added duplicate shift icon
					$list .= '<li class="shift-item">' . "\n";
						$title = __( 'Duplicate Shift', 'radio-station' ) . "\n";
						$list .= '<span id="shift-' . esc_attr( $unique_id ) . '-duplicate" class="shift-duplicate dashicons dashicons-admin-page" title="' . esc_attr( $title ) . '" onclick="radio_shift_duplicate(this);"></span>' . "\n";
					$list .= '</li>' . "\n";

					// --- remove shift icon ---
					// 2.3.0: change remove button to icon
					$list .= '<li class="shift-item last-item">' . "\n";
						$title = __( 'Remove Shift', 'radio-station' ) . "\n";
						$list .= '<span id="shift-' . esc_attr( $unique_id ) . '-remove" class="shift-remove dashicons dashicons-no" title="' . esc_attr( $title ) . '" onclick="radio_shift_remove(this);"></span>' . "\n";
					$list .= '</li>' . "\n";

				$list .= '</ul>' . "\n";

				// --- output any shift conflicts found ---
				// 2.5.6: check shift conflict setting
				if ( 'yes' == $check_conflicts ) {
					if ( $conflicts && is_array( $conflicts ) && ( count( $conflicts ) > 0 ) ) {

						$list .= '<div class="shift-conflicts">' . "\n";
						$list .= '<b>' . esc_html( __( 'Shift Conflicts', 'radio-station' ) ) . '</b>: ' . "\n";
						foreach ( $conflicts as $j => $conflict ) {
							if ( $j > 0 ) {
								$list .= ', ';
							}
							if ( $conflict['show'] == $post_id ) {
								$list .= '<i>' . esc_html( __('This Show', 'radio-station' ) ) . '</i>' . "\n";
							} else {
								// 2.5.6: fix to show conflict ID key
								// $show_edit_link = add_query_arg( 'post', $conflict['show'], $edit_link );
								// $show_title = get_the_title( $conflict['show'] );
								$show_edit_link = add_query_arg( 'post', $conflict['ID'], $edit_link );
								$show_title = get_the_title( $conflict['ID'] );
								$list .= '<a href="' . esc_url( $show_edit_link ) . '">' . esc_html( $show_title ) . '</a>' . "\n";
							}
							$conflict_start = esc_html( $conflict['shift']['start_hour'] ) . ':' . esc_html( $conflict['shift']['start_min'] ) . ' ' . esc_html( $conflict['shift']['start_meridian'] );
							$conflict_end = esc_html( $conflict['shift']['end_hour'] ) . ':' . esc_html( $conflict['shift']['end_min'] ). ' ' . esc_html( $conflict['shift']['end_meridian'] );
							$list .= ' - ' . esc_html( $conflict['shift']['day'] ) . ' ' . $conflict_start . ' - ' . $conflict_end;
						}
						$list .= '</div><br>' . "\n";
					}
				}

			// --- close shift wrapper ---
			$list .= '</div>' . "\n";

		}
	}

	// 2.3.2: moved into function and changed ID
	// 2.3.3.9: change element from span to div
	$list .= '<div id="new-shifts"></div>' . "\n";

	// 2.5.6: added conflict checker is disabled reminder message
	if ( 'yes' != $check_conflicts ) {
		$list .= __( 'Note: Show shift conflict checker is currently disabled in your plugin settings.', 'radio-station' ) . '<br>' . "\n";
	} elseif ( RADIO_STATION_DEBUG ) {
		$list .= 'Conflict List: ' . esc_html( print_r( $conflict_list, true ) ) . '<br>' . "\n";
	}

	// --- set return data ---
	// 2.3.2: added for separated function
	$table = array(
		'list'      => $list,
		'active'    => $active,
		'conflicts' => $conflict_list,
	);

	return $table;
}

// -----------------------------------
// Add Show Description Helper Metabox
// -----------------------------------
// 2.3.0: added metabox for show description helper text
add_action( 'add_meta_boxes', 'radio_station_add_show_helper_box' );
function radio_station_add_show_helper_box() {
	// 2.3.2: filter top metabox position
	$position = apply_filters( 'radio_station_metabox_position', 'rstop', 'helper' );
	add_meta_box(
		'radio-station-show-helper-box',
		__( 'Show Description', 'radio-station' ),
		'radio_station_show_helper_box',
		RADIO_STATION_SHOW_SLUG,
		$position,
		'low'
	);
}

// -------------------------------
// Show Description Helper Metabox
// -------------------------------
// 2.3.0: added metabox for show description helper text
function radio_station_show_helper_box() {

	// --- show description helper text ---
	echo '<p>' . "\n";
		echo esc_html( __( 'The text field below is for your Show Description. It will display in the About section of your Show page.', 'radio-station' ) ) . "\n";
		echo ' ' . esc_html( __( 'It is not recommended to include your past show content or archives in this area, as it will affect the Show page layout your visitors see.', 'radio-station' ) ) . "\n";
		echo esc_html( __( "It may also impact SEO, as archived content won't have their own pages and thus their own SEO and Social meta rules.", 'radio-station' ) ) . "\n";
	echo '</p>' . "\n";
	echo '<p>' . "\n";
		echo esc_html( __( 'We recommend using WordPress Posts to add new posts and assign them to your Show(s) using the Related Show metabox on the Post Edit screen so they display on the Show page.', 'radio-station' ) ) . "\n";
		echo ' ' . esc_html( __( 'This way you can then assign them to a relevant Post Category for display on your site also.', 'radio-station' ) ) . "\n";
	echo '</p>' . "\n";

	// 2.5.0: activate upgrade to Pro blurb
	$upgrade_url = radio_station_get_upgrade_url();
	$pricing_url = radio_station_get_pricing_url();
	$message = '<p>' . esc_html( __( 'Radio Station Pro includes an Episodes post type for adding past episodes.', 'radio-station' ) ) . "\n";
	$message .= esc_html( __( 'These are then automatically listed on your Show page in an Episodes tab.', 'radio-station' ) ) . "\n";
	$message .= ' <a href="' . esc_url( $upgrade_url ) . '">' . esc_html( __( 'Go Pro!', 'radio-station' ) ) . '</a>';
	$message .= ' | <a href="' . esc_url( $pricing_url ) . '" target="_blank">' . esc_html( __( 'Find out more...', 'radio-station' ) ) . '</a>';
	$message .= '</p>';
	$message = apply_filters( 'radio_station_show_helper_episode_message', $message );
	$allowed_html = radio_station_allowed_html( 'content', 'settings' );
	echo wp_kses( $message, $allowed_html );

}

// ----------------------------------
// Rename Show Featured Image Metabox
// ----------------------------------
// 2.3.0: renamed from "Feature Image" to be clearer
// 2.3.0: removed this as now implementing show images separately
// (note this is the Show Logo for backwards compatibility reasons)
// add_action( 'do_meta_boxes', 'radio_station_rename_featured_image_metabox' );
function radio_station_rename_featured_image_metabox() {
	remove_meta_box( 'postimagediv', RADIO_STATION_SHOW_SLUG, 'side' );
	add_meta_box(
		'postimagediv',
		__( 'Show Logo', 'radio-station' ),
		'post_thumbnail_meta_box',
		RADIO_STATION_SHOW_SLUG,
		'side',
		'low'
	);
}

// -----------------------
// Add Show Images Metabox
// -----------------------
// 2.3.0: added show images metabox
add_action( 'add_meta_boxes', 'radio_station_add_show_images_metabox' );
function radio_station_add_show_images_metabox() {
	add_meta_box(
		'radio-station-show-images-metabox',
		__( 'Show Images', 'radio-station' ),
		'radio_station_show_images_metabox',
		array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG ),
		'side',
		'low'
	);
}

// -------------------
// Show Images Metabox
// -------------------
// 2.3.0: added show header and avatar image metabox
// ref: https://codex.wordpress.org/Javascript_Reference/wp.media
function radio_station_show_images_metabox() {

	global $post;

	if ( isset( $_GET['avatar_refix'] ) && ( 'yes' == sanitize_text_field( $_GET['avatar_refix'] ) ) ) {
		delete_post_meta( $post->ID, '_rs_image_updated', true );
		$show_avatar = radio_station_get_show_avatar_id( $post->ID );
		echo "Transferred ID: " . $show_avatar;
	}

	wp_nonce_field( 'radio-station', 'show_images_nonce' );
	$upload_link = get_upload_iframe_src( 'image', $post->ID );

	// --- get show avatar image info ---
	$show_avatar = get_post_meta( $post->ID, 'show_avatar', true );
	$show_avatar_src = wp_get_attachment_image_src( $show_avatar, 'full' );
	$has_show_avatar = is_array( $show_avatar_src );

	// --- show avatar image ---
	echo '<div id="show-avatar-image">' . "\n";

		// --- image container ---
		echo '<div class="custom-image-container">' . "\n";
		if ( $has_show_avatar ) {
			echo '<img src="' . esc_url( $show_avatar_src[0] ) . '" alt="" style="max-width:100%;">' . "\n";
		}
		echo '</div>' . "\n";

		// --- add and remove links ---
		echo '<p class="hide-if-no-js">' . "\n";
			$hidden = '';
			if ( $has_show_avatar ) {
				$hidden = ' hidden';
			}
			echo '<a class="upload-custom-image' . esc_attr( $hidden ) . '" href="' . esc_url( $upload_link ) . '">';
				echo esc_html( __( 'Set Show Avatar Image', 'radio-station' ) );
			echo '</a>' . "\n";
			$hidden = '';
			if ( !$has_show_avatar ) {
				$hidden = ' hidden';
			}
			echo '<a class="delete-custom-image' . esc_attr( $hidden ) . '" href="#">';
				echo esc_html( __( 'Remove Show Avatar Image', 'radio-station' ) );
			echo '</a>' . "\n";
		echo '</p>' . "\n";

		// --- hidden input for image ID ---
		echo '<input class="custom-image-id" name="show_avatar" type="hidden" value="' . esc_attr( $show_avatar ) . '">' . "\n";

	echo '</div>' . "\n";

	// --- check if show content header image is enabled ---
	// 2.3.3.9: fix to match yes string value
	$header_image = radio_station_get_setting( 'show_header_image' );
	if ( 'yes' == $header_image ) {

		// --- get show header image info
		$show_header = get_post_meta( $post->ID, 'show_header', true );
		$show_header_src = wp_get_attachment_image_src( $show_header, 'full' );
		$has_show_header = is_array( $show_header_src );

		// --- show header image ---
		echo '<div id="show-header-image">' . "\n";

		// --- image container ---
		echo '<div class="custom-image-container">' . "\n";
		if ( $has_show_header ) {
			echo '<img src="' . esc_url( $show_header_src[0] ) . '" alt="" style="max-width:100%;">' . "\n";
		}
		echo '</div>' . "\n";

		// --- add and remove links ---
		echo '<p class="hide-if-no-js">' . "\n";
			$hidden = '';
			if ( $has_show_header ) {
				$hidden = ' hidden';
			}
			echo '<a class="upload-custom-image' . esc_attr( $hidden ) . '" href="' . esc_url( $upload_link ) . '">';
				echo esc_html( __( 'Set Show Header Image', 'radio-station' ) );
			echo '</a>' . "\n";
			$hidden = '';
			if ( !$has_show_header ) {
				$hidden = ' hidden';
			}
			echo '<a class="delete-custom-image' . esc_attr( $hidden ) . '" href="#">';
				echo esc_html( __( 'Remove Show Header Image', 'radio-station' ) );
			echo '</a>' . "\n";
		echo '</p>' . "\n";

		// --- hidden input for image ID ---
		echo '<input class="custom-image-id" name="show_header" type="hidden" value="' . esc_attr( $show_header ) . '">' . "\n";

		echo '</div>' . "\n";

	}

	// --- set images autosave nonce and iframe ---
	$images_autosave_nonce = wp_create_nonce( 'show-images-autosave' );
	echo '<input type="hidden" id="show-images-save-nonce" value="' . esc_attr( $images_autosave_nonce ) . '">' . "\n";
	echo '<iframe src="javascript:void(0);" name="show-images-save-frame" id="show-images-save-frame" style="display:none;"></iframe>' . "\n";

	// --- script text strings ---
	$image_confirm_remove = __( 'Are you sure you want to remove this image?', 'radio-station' );
	$media_title_text = __( 'Select or Upload Image' ,'radio-station' );
	$media_button_text = __( 'Use this Image', 'radio-station' );

	// --- image selection script ---
	// 2.3.3.9: add library argument to wp.media load
	$js = "	var mediaframe, parentdiv,
	imagesmetabox = jQuery('#radio-station-show-images-metabox'),
	addimagelink = imagesmetabox.find('.upload-custom-image'),
	deleteimagelink = imagesmetabox.find('.delete-custom-image'),
	imageconfirmremove = '" . esc_js( $image_confirm_remove ) . "',
	wpmediatitletext = '" . esc_js( $media_title_text ). "',
	wpmediabuttontext = '" . esc_js( $media_button_text ) . "';

	/* Add Image on Click */
	addimagelink.on( 'click', function( event ) {

		event.preventDefault();
		parentdiv = jQuery(this).parent().parent();

		if (mediaframe) {mediaframe.open(); return;}
		mediaframe = wp.media({
			title: wpmediatitletext,
			button: {text: wpmediabuttontext},
			library: {order: 'DESC', orderby: 'date', type: 'image', search: null, uploadedTo: null},
			multiple: false
		});

		mediaframe.on( 'select', function() {
			var attachment = mediaframe.state().get('selection').first().toJSON();
			image = '<img src=\"'+attachment.url+'\" alt=\"\" style=\"max-width:100%;\"/>';
			parentdiv.find('.custom-image-container').append(image);
			parentdiv.find('.custom-image-id').val(attachment.id);
			parentdiv.find('.upload-custom-image').addClass('hidden');
			parentdiv.find('.delete-custom-image').removeClass('hidden');

			/* auto-save image via AJAX */
			postid = jQuery('#post_ID').val(); imgid = attachment.id;
			if (parentdiv.attr('id') == 'show-avatar-image') {imagetype = 'avatar';}
			if (parentdiv.attr('id') == 'show-header-image') {imagetype = 'header';}
			imagessavenonce = jQuery('#show-images-save-nonce').val();
			framesrc = ajaxurl+'?action=radio_station_show_images_save';
			framesrc += '&post_id='+postid+'&image_type='+imagetype;
			framesrc += '&image_id='+imgid+'&nonce='+imagessavenonce;
			jQuery('#show-images-save-frame').attr('src', framesrc);
		});

		mediaframe.open();
	});

	/* Delete Image on Click */
	deleteimagelink.on( 'click', function( event ) {
		event.preventDefault();
		agree = confirm(imageconfirmremove);
		if (!agree) {return;}
		parentdiv = jQuery(this).parent().parent();
		parentdiv.find('.custom-image-container').html('');
		parentdiv.find('.custom-image-id').val('');
		parentdiv.find('.upload-custom-image').removeClass('hidden');
		parentdiv.find('.delete-custom-image').addClass('hidden');
	});
	" . "\n";

	// --- enqueue script inline ---
	// 2.3.0: enqueue instead of echoing
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'radio-station-admin', $js );

	// 2.3.3.9: add media modal close button style fix
	echo '<style>.media-modal-close {z-index: 1001 !important;</style>';
}

// ---------------------------------
// AJAX to AutoSave Images on Change
// ---------------------------------
add_action( 'wp_ajax_radio_station_show_images_save', 'radio_station_show_images_save' );
function radio_station_show_images_save() {

	global $post;

	// --- sanitize posted values ---
	// 2.3.3.6: get post for checking capability
	// 2.5.0: simplified sanitization and auth check logic
	if ( isset( $_GET['post_id'] ) && ( absint( $_GET['post_id'] ) > 0 ) ) {
		$post_id = absint( $_GET['post_id'] );
		$post = get_post( $post_id );
		if ( $post ) {

			// --- check edit capability ---
			if ( !current_user_can( 'edit_shows' ) ) {
				exit;
			}

			// --- verify nonce value ---
			if ( !isset( $_GET['nonce'] ) || !wp_verify_nonce( sanitize_text_field( $_GET['nonce'] ), 'show-images-autosave' ) ) {
				exit;
			}

			// --- refresh parent frame nonce ---
			$images_save_nonce = wp_create_nonce( 'show-images-autosave' );
			echo "<script>parent.document.getElementById('show-images-save-nonce').value = '" . esc_js( $images_save_nonce ) . "';</script>";

			// --- get image ID ---
			if ( isset( $_GET['image_id'] ) ) {
				$image_id = absint( $_GET['image_id'] );
				if ( $image_id < 1 ) {
					unset( $image_id );
				}
			}

			// --- get image type ---
			if ( isset( $_GET['image_type'] ) ) {
				if ( in_array( sanitize_text_field( $_GET['image_type'] ), array( 'header', 'avatar' ) ) ) {
					$image_type = sanitize_text_field( $_GET['image_type'] );
				}
			}

			// --- update show avatar image ID ---
			if ( isset( $image_id ) && isset( $image_type ) ) {
				update_post_meta( $post_id, 'show_' . $image_type, $image_id );

				// --- maybe add image updated flag ---
				// (help prevent duplication on new posts)
				$updated = get_post_meta( $post_id, '_rs_image_updated', true );
				if ( !$updated ) {
					add_post_meta( $post_id, '_rs_image_updated', true );
				}

			}
		}
	}

	exit;
}

// --------------------
// Update Show Metadata
// --------------------
// 2.3.2: added AJAX show save shifts action
// 2.3.3.9: added AJAX show save shift action
// 2.3.3.9: change save action priority (to be after taxonomy updates)
add_action( 'wp_ajax_radio_station_show_save_shift', 'radio_station_show_save_data' );
add_action( 'wp_ajax_radio_station_show_save_shifts', 'radio_station_show_save_data' );
add_action( 'save_post', 'radio_station_show_save_data', 11 );
function radio_station_show_save_data( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- make sure we have a post ID for AJAX save ---
	// 2.3.2: added AJAX shift saving checks
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		if ( !isset( $_REQUEST['action'] ) ) {
			return;
		}
		// 2.3.3: added double check for AJAX action match
		// 2.3.3.9: check for single or multiple shift save action
		if ( 'radio_station_show_save_shift' == sanitize_text_field( $_REQUEST['action'] ) ) {
			$selection = 'single';
			if ( preg_match( '/^[a-zA-Z0-9_]+$/', sanitize_text_field( $_REQUEST['shift_id'] ) ) ) {
				$shift_id = sanitize_text_field( $_REQUEST['shift_id'] );
			}
		} elseif ( 'radio_station_show_save_shifts' == sanitize_text_field( $_REQUEST['action'] ) ) {
			$selection = 'multiple';
		} elseif ( 'radio_station_add_show_shift' == sanitize_text_field( $_REQUEST['action'] ) ) {
			$selection = 'add';
		} else {
			return;
		}

		// --- check for errors ---
		$error = false;
		if ( !current_user_can( 'edit_shows' ) ) {
			$error = __( 'Failed. Publish or Update instead.', 'radio-station' );
		} elseif ( !isset( $_POST['show_shifts_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $_POST['show_shifts_nonce'] ), 'radio-station' ) ) {
			$error = __( 'Expired. Publish or Update instead.', 'radio-station' );
		} elseif ( !isset( $_POST['show_id'] ) || ( '' === sanitize_text_field( $_POST['show_id'] ) ) ) {
			$error = __( 'Error! No Show ID provided.', 'radio-station' );
		}  else {
			$post_id = absint( $_POST['show_id'] );
			$post = get_post( $post_id );
			if ( !$post ) {
				$error = __( 'Failed. Invalid Show.', 'radio-station' );
			}
		}

		// --- send error to parent frame ---
		// 2.3.3.9: fix to remove shift save form not track save form
		if ( $error ) {
			echo "<script>parent.document.getElementById('shifts-saving-message').style.display = 'none';
			parent.document.getElementById('shifts-error-message').style.display = '';
			parent.document.getElementById('shifts-error-message').innerHTML = '" . esc_js( $error ) . "';
			form = parent.document.getElementById('shift-save-form');
			if (form) {form.parentNode.removeChild(form);}
			</script>";
			exit;
		}
	}

	// --- set show meta changed flags ---
	$show_meta_changed = $show_shifts_changed = false;

	// --- get posted DJ / host list ---
	// 2.2.7: check DJ post value is set
	if ( isset( $_POST['show_hosts_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['show_hosts_nonce'] ), 'radio-station' ) ) {

		// 2.3.3.9: user check moved to input sanitization function
		$hosts = radio_station_sanitize_input( 'show', 'user_list' );

		// 2.3.3.9: fix to get previous hosts before updating
		$prev_hosts = get_post_meta( $post_id, 'show_user_list', true );
		if ( $prev_hosts != $hosts ) {
			update_post_meta( $post_id, 'show_user_list', $hosts );
			$show_meta_changed = true;
		}
	}

	// --- get posted show producers ---
	// 2.3.0: added show producer sanitization
	if ( isset( $_POST['show_producers_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['show_producers_nonce'] ), 'radio-station' ) ) {

		// 2.3.3.9: user check moved to input sanitization function
		$producers = radio_station_sanitize_input( 'show', 'producer_list' );

		// 2.3.0: added save of show producers
		// 2.3.3.9: fix to get previous producers before updating
		$prev_producers = get_post_meta( $post_id, 'show_producer_list', true );
		if ( $prev_producers != $producers ) {
			update_post_meta( $post_id, 'show_producer_list', $producers );
			$show_meta_changed = true;
		}
	}

	// --- save show meta data ---
	// 2.3.0: added separate nonce check for show meta
	if ( isset( $_POST['show_meta_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['show_meta_nonce'] ), 'radio-station' ) ) {

		// --- get the meta data to be saved ---
		// 2.3.2: added download disable switch
		// 2.2.3: added show metadata value sanitization
		// 2.3.3.9: use input sanitization function
		$file = radio_station_sanitize_input( 'show', 'file' );
		$email = radio_station_sanitize_input( 'show', 'email' );
		$link = radio_station_sanitize_input( 'show' , 'link' );
		$patreon_id = radio_station_sanitize_input( 'show', 'patreon' );
		$phone = radio_station_sanitize_input( 'show', 'phone' );
		$active = radio_station_sanitize_input( 'show', 'active' );
		$download = radio_station_sanitize_input( 'show', 'download' );

		// --- get existing values and check if changed ---
		// 2.3.0: added check against previous values
		// 2.3.2: added download disable switch
		// 2.3.3.6: added phone number field saving
		$prev_file = get_post_meta( $post_id, 'show_file', true );
		$prev_download = get_post_meta( $post_id, 'show_download', true );
		$prev_email = get_post_meta( $post_id, 'show_email', true );
		$prev_phone = get_post_meta( $post_id, 'show_phone', true );
		$prev_active = get_post_meta( $post_id, 'show_active', true );
		$prev_link = get_post_meta( $post_id, 'show_link', true );
		$prev_patreon_id = get_post_meta( $post_id, 'show_patreon', true );
		if ( ( $prev_active != $active ) || ( $prev_link != $link )
				|| ( $prev_email != $email ) || ( $prev_phone != $phone )
				|| ( $prev_file != $file ) || ( $prev_download != $download )
				|| ( $prev_patreon_id != $patreon_id ) ) {
			$show_meta_changed = true;
		}

		// --- update the show metadata ---
		// 2.3.2: added download disable switch
		update_post_meta( $post_id, 'show_file', $file );
		update_post_meta( $post_id, 'show_download', $download );
		update_post_meta( $post_id, 'show_email', $email );
		update_post_meta( $post_id, 'show_phone', $phone );
		update_post_meta( $post_id, 'show_active', $active );
		update_post_meta( $post_id, 'show_link', $link );
		update_post_meta( $post_id, 'show_patreon', $patreon_id );
	}


	// --- update the show images ---
	if ( isset( $_POST['show_images_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['show_images_nonce'] ), 'radio-station' ) ) {

		// --- show header image ---
		if ( isset( $_POST['show_header'] ) ) {
			$header = absint( $_POST['show_header'] );
			if ( $header > 0 ) {
				// $prev_header = get_post_meta( $post_id, 'show_header', true );
				// if ( $header != $prev_header ) {$show_meta_changed = true;}
				update_post_meta( $post_id, 'show_header', $header );
			}
		}

		// --- show avatar image ---
		// 2.5.0: delete post meta if removing avatar
		if ( '' == sanitize_text_field( $_POST['show_avatar'] ) ) {
			delete_post_meta( $post_id, 'show_avatar' );
		} else {
			$avatar = absint( $_POST['show_avatar'] );
			if ( $avatar > 0 ) {
				// $prev_avatar = get_post_meta( $post_id, 'show_avatar', true );
				// if ( $avatar != $prev_avatar ) {$show_meta_changed = true;}
				update_post_meta( $post_id, 'show_avatar', $avatar );
			}
		}

		// --- add image updated flag ---
		// (to prevent duplication for new posts)
		$updated = get_post_meta( $post_id, '_rs_image_updated', true );
		if ( !$updated ) {
			add_post_meta( $post_id, '_rs_image_updated', true );
		}
	}

	// --- check show shift nonce ---
	if ( isset( $_POST['show_shifts_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['show_shifts_nonce'] ), 'radio-station' ) ) {

		// --- loop posted show shift times ---
		// 2.3.1: added check if any shifts are set (fix undefined index warning)
		$prev_shifts = radio_station_get_show_schedule( $post_id );
		$shifts = $new_shifts = array();
		// 2.3.3.9: allow for posting of just new shift
		if ( isset( $_POST['new_shift'] ) ) {
			// 2.5.0: use sanitize_text_field on posted value
			$new_shift = sanitize_text_field( $_POST['new_shift'] );
			// print_r( $new_shift );
			$new_id = $shift_id = radio_station_unique_shift_id();
			$shifts = $prev_shifts;
			$shifts[$new_id] = $new_shift;
			$_POST['show_sched'] = $shifts;
		} else {
			// TODO: test arrap_map and sanitize_text_field ?
			// $shifts = array_map( 'sanitize_text_field', $_POST['show_sched );
			$shifts = $_POST['show_sched'];
		}

		$new_ids = array();
		// 2.5.0: use get_schedule_weekdays to honour start of week value
		// $days = array( '', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
		$days = array_merge( array( '' ), radio_station_get_schedule_weekdays() );
		if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
			foreach ( $shifts as $i => $shift ) {

				// --- reset shift disabled flag ---
				// 2.3.0: added shift disabling logic
				$disabled = false;

				// --- maybe generate new unique shift ID ---
				if ( 'new-' == substr( $i, 0, 4 ) ) {
					$i = radio_station_unique_shift_id();
				}
				$new_ids[] = $i;

				// --- loop shift keys ---
				foreach ( $shift as $key => $value ) {

					// --- validate according to key ---
					$isvalid = false;
					if ( 'day' === $key ) {

						// --- check shift day ---
						// 2.2.8: remove strict in_array checking
						if ( in_array( $value, $days ) ) {
							$isvalid = true;
						}
						if ( '' == $value ) {
							// 2.3.0: auto-disable if no day is set
							$disabled = true;
						}

					} elseif ( ( 'start_hour' === $key ) || ( 'end_hour' === $key ) ) {

						// --- check shift start and end hour ---
						if ( empty( $value ) ) {
							// 2.3.0: auto-disable shift if not start/end hour
							$isvalid = $disabled = true;
						} elseif ( ( absint( $value ) > 0 ) && ( absint( $value ) < 13 ) ) {
							$isvalid = true;
						}

					} elseif ( ( 'start_min' === $key ) || ( 'end_min' === $key ) ) {

						// --- check shift start and end minute ---
						if ( empty( $value ) ) {
							// 2.3.0: auto-set minute value to 00 if empty
							$isvalid = true;
							$value = '00';
						} elseif ( ( absint( $value ) > - 1 ) && ( absint( $value ) < 61 ) ) {
							$isvalid = true;
						} else {
							$disabled = true;
						}

					} elseif ( ( 'start_meridian' === $key ) || ( 'end_meridian' === $key ) ) {

						// --- check shift meridiem ---
						$valid = array( '', 'am', 'pm' );
						// 2.2.8: remove strict in_array checking
						if ( in_array( $value, $valid ) ) {
							$isvalid = true;
						}
						if ( '' == $value ) {
							$disabled = true;
						}

					} elseif ( 'encore' === $key ) {

						// --- check shift encore switch ---
						// 2.2.4: fix to missing encore sanitization saving
						$valid = array( '', 'on' );
						// 2.2.8: remove strict in_array checking
						if ( in_array( $value, $valid ) ) {
							$isvalid = true;
						}

					} elseif ( 'disabled' == $key ) {

						// --- check shift disabled switch ---
						// 2.3.0: added shift disable switch
						// note: overridden on incomplete data or shift conflict
						$valid = array( '', 'yes' );
						if ( in_array( $value, $valid ) ) {
							$isvalid = true;
						}
						if ( 'yes' == $value ) {
							$disabled = true;
						}

					}

					// --- if valid add to new schedule ---
					if ( $isvalid ) {
						$new_shifts[$i][$key] = $value;
					} else {
						$new_shifts[$i][$key] = '';
					}
				}

				// --- check for shift conflicts with other shows ---
				// 2.3.0: added show shift conflict checking
				if ( !$disabled ) {
					// 2.5.0: changed scope to other instead of shows
					$conflicts = radio_station_check_shift( $post_id, $new_shifts[$i], 'other' );
					if ( $conflicts ) {
						$disabled = true;
						if ( RADIO_STATION_DEBUG ) {
							echo "*Conflicting Shift Disabled*" . "\n";
						}
					}
				}

				// --- disable if incomplete data or shift conflicts ---
				if ( $disabled ) {
					$new_shifts[$i]['disabled'] = 'yes';
					if ( RADIO_STATION_DEBUG ) {
						echo "*Shift Disabled*" . "\n";
					}
				}

			}

			// --- recheck for conflicts with other shifts for this show ---
			// 2.3.0: added new shift conflict checking
			$new_shifts = radio_station_check_new_shifts( $new_shifts );

			// --- update the schedule meta entry ---
			// 2.3.0: check if shift times have changed before saving
			if ( $new_shifts != $prev_shifts ) {
				$show_shifts_changed = true;
				update_post_meta( $post_id, 'show_sched', $new_shifts );
			}

		} else {
			// 2.3.0: fix to clear data if all shifts removed
			delete_post_meta( $post_id, 'show_sched' );
			$show_shifts_changed = true;
		}

		// 2.3.3.9: clear out old unique shift IDs from prev_shifts
		if ( $show_shifts_changed && is_array( $prev_shifts ) && ( count( $prev_shifts ) > 0 ) ) {
			$prev_ids = array();
			foreach ( $prev_shifts as $i => $shift ) {
				if ( !in_array( $i, $new_ids ) ) {
					$prev_ids[] = $i;
				}
			}
			if ( count( $prev_ids ) > 0 ) {
				$unique_ids = get_option( 'radio_station_shifts_ids' );
				foreach ( $unique_ids as $i => $unique_id ) {
					if ( in_array( $unique_id, $prev_ids ) ) {
						unset( $unique_ids[$i] );
					}
				}
				update_option( 'radio_station_shifts_ids', $unique_ids );
			}
		}
	}

	// 2.3.3.9: maybe sync to linked override taxonomies
	$overrides = radio_station_get_linked_overrides( $post_id );
	if ( $overrides && is_array( $overrides ) && ( count( $overrides ) > 0 ) ) {

		// --- get genre and language terms ---
		// 2.5.6: set empty arry for genre and language term ids
		$genre_term_ids = $language_term_ids = array();
		$genre_terms = wp_get_object_terms( $post_id, RADIO_STATION_GENRES_SLUG );
		if ( count( $genre_terms ) > 0 ) {
			foreach ( $genre_terms as $genre_term ) {
				$genre_term_ids[] = $genre_term->term_id;
			}
		}
		$language_terms = wp_get_object_terms( $post_id, RADIO_STATION_LANGUAGES_SLUG );
		if ( count( $language_terms ) > 0 ) {
			foreach ( $language_terms as $language_term ) {
				$language_term_ids[] = $language_term->term_id;
			}
		}

		// --- maybe set these terms to linked overrides ---
		foreach ( $overrides as $override_id ) {
			$sync_genres = get_post_meta( $override_id, 'sync_genres', true );
			if ( 'yes' == $sync_genres ) {
				wp_set_post_terms( $override_id, $genre_term_ids, RADIO_STATION_GENRES_SLUG );
			}
			$sync_languages = get_post_meta( $override_id, 'sync_languages', true );
			if ( 'yes' == $sync_languages ) {
				wp_set_post_terms( $override_id, $language_term_ids, RADIO_STATION_LANGUAGES_SLUG );
			}
			if ( ( 'yes' == $sync_genres ) || ( 'yes' == $sync_languages ) ) {
				// 2.4.0.3: added second argument to cache clear
				radio_station_clear_cached_data( $override_id, RADIO_STATION_OVERRIDE_SLUG );
			}
		}
	}

	// --- maybe clear transient data ---
	// 2.3.0: added to clear transients if any meta has changed
	// 2.3.3: remove current show transient
	// 2.3.3.9: just call clear cached data function
	if ( $show_meta_changed || $show_shifts_changed ) {
		// 2.4.0.3: added second argument to cache clear
		radio_station_clear_cached_data( $post_id, RADIO_STATION_SHOW_SLUG );
	}

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		// 2.3.3.9: removed check of AJAX action as done earlier

		// --- debug information ---
		echo "Posted Shifts: " . esc_html( print_r( $shifts, true ) ) . "\n";
		echo "New Shifts: " . esc_html( print_r( $new_shifts, true ) ) . "\n";

		// --- display shifts saved message ---
		// 2.3.3.9: fade out shifts saved message
		$show_shifts_nonce = wp_create_nonce( 'radio-station' );
		echo "<script>parent.document.getElementById('shifts-saving-message').style.display = 'none';
		parent.document.getElementById('shifts-saved-message').style.display = '';
		if (typeof parent.jQuery == 'function') {parent.jQuery('#shifts-saved-message').fadeOut(3000);}
		else {setTimeout(function() {parent.document.getElementById('shifts-saved-message').style.display = 'none';}, 3000);}
		/* form = parent.document.getElementById('shift-save-form');
		if (form) {form.parentNode.removeChild(form);} */
		parent.document.getElementById('show_shifts_nonce').value = '" . esc_js( $show_shifts_nonce ) . "';
		</script>";

		// 2.3.3.9: added check if show shifts changed
		if ( $show_shifts_changed ) {

			// --- output new show shifts list ---
			echo '<div id="shifts-list">' . "\n";
			if ( isset( $_REQUEST['check-bypass'] ) && ( '1' === sanitize_text_field( $_REQUEST['check-bypass'] ) ) ) {
				echo '<input type="hidden" name="check-bypass" value="1">' . "\n";
			}
			$table = radio_station_show_shifts_table( $post_id );

			// --- check for shift conflicts ---
			$display_warning = false;
			// 2.3.3.9: check conflict count instead of boolean test
			if ( count( $table['conflicts'] ) > 0 ) {
				// 2.3.3.9: maybe skip conflict message if single shift save has no conflict
				$display_warning = true;
				if ( isset( $selection ) && ( 'multiple' != $selection ) && isset( $shift_id ) ) {
					$display_warning = $found_conflict = false;
					foreach ( $table['conflicts'] as $unique_id => $conflict ) {
						if ( $shift_id == $unique_id ) {
							$found_conflict = true;
						}
					}
					if ( $found_conflict ) {
						$display_warning = true;
					}
				}
				$hidden = !$display_warning;
				radio_station_shifts_conflict_message( $hidden );
			}

			// 2.5.0: use wp_kses on table output
			$allowed = radio_station_allowed_html( 'content', 'settings' );
			echo wp_kses( $table['list'], $allowed );

			// echo '<div id="updated-div"></div>' . "\n";
			echo '</div>' . "\n";

			// --- refresh show shifts list ---
			$js = "shiftslist = parent.document.getElementById('shifts-list');" . "\n";
			$js .= "shiftslist.innerHTML = document.getElementById('shifts-list').innerHTML;" . "\n";
			$js .= "shiftslist.style.display = '';" . "\n";

			// --- reload the current schedule view ---
			// 2.4.0.3: added missing check for window parent function
			// 2.5.0: added extra instance argument
			$js .= "if (window.parent && (typeof parent.radio_load_schedule == 'function')) {" . "\n";
			$js .= "	parent.radio_load_schedule(false,false,false,true);" . "\n";
			$js .= "}" . "\n";

			// 2.3.3.6: clear changes may not have been saved window reload message
			$js .= "if (parent.window.onbeforeunloadset) {" . "\n";
			$js .= "	parent.window.onbeforeunload = parent.storedonbeforeunload;" . "\n";
			$js .= "	parent.window.onbeforeunloadset = false;" . "\n";
			$js .= "}" . "\n";

			// --- alert on conflicts ---
			if ( $display_warning ) {
				$warning = __( 'Warning! Shift conflicts detected.', 'radio-station' );
				$js .= "alert('" . esc_js( $warning ) . "');" . "\n";
			}

			// --- output the scripts ---
			echo '<script>' . $js . '</script>' . "\n";

			// 2.3.3.9: trigger action for single or multiple shift save
			if ( isset( $selection ) ) {
				if ( 'single' == $selection ) {
					do_action( 'radio_station_show_save_shift', $shift_id );
				} else {
					do_action( 'radio_station_show_save_shifts' );
				}
			}
		}

		// --- return early when adding single shift ---
		// 2.3.3.9: added for single shift action
		if ( 'radio_station_add_show_shift' == sanitize_text_field( $_REQUEST['action'] ) ) {
			return;
		}

		exit;
	}
}

// -----------------------------------
// Clear Schedule Cache on Delete Show
// -----------------------------------
// 2.3.3.9: added to update schedule on show/override deletions
add_action( 'delete_post', 'radio_station_show_delete', 10, 2 );
function radio_station_show_delete( $post_id, $post ) {

	if ( !is_object( $post ) || !property_exists( $post, 'post_type' ) ) {
		$post = get_post( $post_id );
	}
	// 2.4.0.3: also trigger clear data for playlists
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG, RADIO_STATION_PLAYLIST_SLUG );
	if ( !in_array( $post->post_type, $post_types ) ) {
		return;
	}

	// --- clear all cached schedule data ---
	// 2.4.0.3: added second argument to cache clear
	radio_station_clear_cached_data( $post_id, $post->post_type );

	// --- clear from unique shift IDs list ---
	// 2.3.3.9: added to keep unique ID list from bloating over time
	$shift_ids = get_option( 'radio_station_shifts_ids' );
	$ids_changed = false;
	if ( $shift_ids && is_array( $shift_ids ) && ( count( $shift_ids ) > 0 ) ) {
		if ( RADIO_STATION_SHOW_SLUG == $post->post_type ) {
			$shifts = get_post_meta( $post_id, 'show_sched', true );
			if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
				foreach ( $shifts as $id => $shift ) {
					if ( 8 == strlen( $id ) ) {
						$key = array_search( $id, $shift_ids );
						unset( $shift_ids[$key] );
					}
				}
			}
		} elseif ( RADIO_STATION_OVERRIDE_SLUG == $post->post_type ) {
			$shifts = get_post_meta( $post_id, 'show_override_sched', true );
			// 2.3.3.9: maybe convert single shift to array
			if ( $shifts && is_array( $shifts ) && array_key_exists( 'date', $shifts ) ) {
				$shifts = array( $shifts );
			}
			if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
				foreach ( $shifts as $shift ) {
					if ( isset( $shift['id'] ) && ( 8 == strlen( $shift['id'] ) ) ) {
						$key = array_search( $shift['id'], $shift_ids );
						unset( $shift_ids[$key] );
					}
				}
			}
		}
	}
	if ( $ids_changed ) {
		update_option( 'radio_station_shift_ids', $shift_ids );
	}

}

// -----------------
// Save Output Debug
// -----------------
add_action( 'save_post_' . RADIO_STATION_SHOW_SLUG, 'radio_station_save_debug_start', 0 );
add_action( 'save_post_' . RADIO_STATION_OVERRIDE_SLUG, 'radio_station_save_debug_start', 0 );
add_action( 'save_post_' . RADIO_STATION_PLAYLIST_SLUG, 'radio_station_save_debug_start', 0 );
function radio_station_save_debug_start( $post_id ) {
	if ( !RADIO_STATION_SAVE_DEBUG ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	ob_start();
}
add_action( 'save_post_' . RADIO_STATION_SHOW_SLUG, 'radio_station_save_debug_start', 9999 );
add_action( 'save_post_' . RADIO_STATION_OVERRIDE_SLUG, 'radio_station_save_debug_start', 9999 );
add_action( 'save_post_' . RADIO_STATION_PLAYLIST_SLUG, 'radio_station_save_debug_start', 9999 );
function radio_station_save_debug_end( $post_id ) {
	if ( !RADIO_STATION_SAVE_DEBUG ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$contents = ob_get_contents();
	ob_end_clean();
	if ( strlen( $contents ) > 0 ) {
		// 2.5.0: added esc_textarea to content output
		echo 'Output Detected During Save (preventing redirect):<br>' . "\n";
		echo '<textarea rows="40" cols="80">' . esc_textarea( $contents ) . '</textarea>' . "\n";
		exit;
	}
}

// ---------------------
// Add Show List Columns
// ---------------------
// 2.2.7: added data columns to show list display
add_filter( 'manage_edit-' . RADIO_STATION_SHOW_SLUG . '_columns', 'radio_station_show_columns', 6 );
function radio_station_show_columns( $columns ) {

	// --- remove thumbnail columns ---
	if ( isset( $columns['thumbnail'] ) ) {
		unset( $columns['thumbnail'] );
	}
	if ( isset( $columns['post_thumb'] ) ) {
		unset( $columns['post_thumb'] );
	}

	// --- modify existing columns ---
	$date = $columns['date'];
	unset( $columns['date'] );
	$comments = $columns['comments'];
	unset( $columns['comments'] );
	$genres = $columns['taxonomy-' . RADIO_STATION_GENRES_SLUG];
	unset( $columns['taxonomy-' . RADIO_STATION_GENRES_SLUG] );
	$languages = $columns['taxonomy-' . RADIO_STATION_LANGUAGES_SLUG];
	unset( $columns['taxonomy-' . RADIO_STATION_LANGUAGES_SLUG] );

	// --- set new column order ---
	$columns['active'] = esc_attr( __( 'Active?', 'radio-station' ) );
	// 2.3.0: added show description indicator column
	$columns['description'] = esc_attr( __( 'About?', 'radio-station' ) );
	$columns['shifts'] = esc_attr( __( 'Shifts', 'radio-station' ) );
	// 2.3.0: change DJs column label to Hosts
	$columns['hosts'] = esc_attr( __( 'Hosts', 'radio-station' ) );
	$columns['taxonomy-' . RADIO_STATION_GENRES_SLUG] = $genres;
	$columns['taxonomy-' . RADIO_STATION_LANGUAGES_SLUG] = $languages;
	$columns['comments'] = $comments;
	$columns['date'] = $date;
	$columns['show_image'] = esc_attr( __( 'Show Avatar', 'radio-station' ) );

	return $columns;
}

// ---------------------
// Show List Column Data
// ---------------------
// 2.2.7: added data columns for show list display
add_action( 'manage_' . RADIO_STATION_SHOW_SLUG . '_posts_custom_column', 'radio_station_show_column_data', 5, 2 );
function radio_station_show_column_data( $column, $post_id ) {

	if ( 'active' == $column ) {

		$active = get_post_meta( $post_id, 'show_active', true );
		if ( 'on' == $active ) {
			echo esc_html( __( 'Yes', 'radio-station' ) );
		} else {
			echo esc_html( __( 'No', 'radio-station' ) );
		}

	} elseif ( 'description' == $column ) {

		// 2.3.0: added show description indicator
		global $wpdb;
		$query = "SELECT post_content FROM " . $wpdb->prefix . "posts WHERE ID = %d";
		$query = $wpdb->prepare( $query, $post_id );
		$content = $wpdb->get_var( $query );
		if ( !$content || ( '' == trim( $content ) ) ) {
			// 2.3.3.9: change bold emphasis to italics
			echo '<i>' . esc_html( __( 'No', 'radio-station' ) ) . '</i>';
		} else {
			echo esc_html( __( 'Yes', 'radio-station' ) );
		}

	} elseif ( 'shifts' == $column ) {

		$active = get_post_meta( $post_id, 'show_active', true );
		$active = ( 'on' == $active ) ? true : false;

		// 2.3.0: check using dates for reliability
		$now = radio_station_get_now();
		$weekdays = radio_station_get_schedule_weekdays();
		$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now );

		$shifts = get_post_meta( $post_id, 'show_sched', true );
		if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {

			// 2.5.0: fix variable inconsistency for sorted shifts
			$sorted_shifts = $dayless_shifts = array();
			foreach ( $shifts as $shift ) {
				// 2.3.2: added check that shift day is not empty
				if ( isset( $shift['day'] ) && ( '' != $shift['day'] ) ) {
					// 2.3.2: fix to convert shift time to 24 hour format
					$shift_time = $shift['start_hour'] . ":" . $shift['start_min'] . ' ' . $shift['start_meridian'];
					$shift_time = radio_station_convert_shift_time( $shift_time );
					$shift_time = $weekdates[$shift['day']] . $shift_time;
					$timestamp = radio_station_to_time( $shift_time );
					$sorted_shifts[$timestamp] = $shift;
				} else {
					$dayless_shifts[] = $shift;
				}
			}

			// 2.5.0: added count check for sorted_shifts
			if ( count( $sorted_shifts ) > 0 ) {
				ksort( $sorted_shifts );
				foreach ( $sorted_shifts as $shift ) {

					// 2.3.0: highlight disabled shifts
					$classes = array( 'show-shift' );
					$disabled = false;
					$title = '';
					if ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) {
						$disabled = true;
						$classes[] = 'disabled';
						$title = __( 'This Shift is Disabled.', 'radio-station' );
					}

					// --- check and highlight conflicts ---
					// 2.3.0: added shift conflict checking
					$conflicts = radio_station_check_shift( $post_id, $shift );
					if ( $conflicts ) {
						$classes[] = 'conflict';
						if ( $disabled ) {
							$title = __( 'This Shift has Schedule Conflicts and is Disabled.', 'radio-station' );
						} else {
							$title = __( 'This Shift has Schedule Conflicts.', 'radio-station' );
						}
					}

					// 2.3.0: also highlight if the show is not active
					if ( !$active ) {
						if ( !in_array( 'disabled', $classes ) ) {
							$classes[] = 'disabled';
						}
						$title = __( 'This Show is not currently active.', 'radio-station' );
					}
					$classlist = implode( ' ', $classes );

					echo '<div class="' . esc_attr( $classlist ) . '" title="' . esc_attr( $title ) . '">' . "\n";

						// --- get shift start and end times ---
						// 2.3.2: fix to convert to 24 hour time
						$start = $shift['start_hour'] . ":" . $shift['start_min'] . $shift['start_meridian'];
						$end = $shift['end_hour'] . ":" . $shift['end_min'] . $shift['end_meridian'];
						$start_time = radio_station_convert_shift_time( $start );
						$end_time =  radio_station_convert_shift_time( $end );
						$start_time = radio_station_to_time( $weekdates[$shift['day']] . ' ' . $start_time );
						$end_time = radio_station_to_time( $weekdates[$shift['day']] . ' ' . $end_time );

						// --- make weekday filter selections bold ---
						// 2.3.0: fix to bolding only if weekday isset
						$bold = false;
						if ( isset( $_GET['weekday'] ) ) {
							$weekday = trim( sanitize_text_field( $_GET['weekday'] ) );
							$nextday = radio_station_get_next_day( $weekday );
							// 2.3.0: handle shifts that go overnight for weekday filter
							if ( ( $weekday == $shift['day'] ) || ( ( $shift['day'] == $nextday ) && ( $end_time < $start_time ) ) ) {
								echo '<b>';
								$bold = true;
							}
						}

						echo esc_html( radio_station_translate_weekday( $shift['day'] ) );
						echo ' ' . esc_html( $start ) . ' - ' . esc_html( $end );
						if ( $bold ) {
							echo '</b>';
						}

					echo '</div>' . "\n";
				}
			}

			// --- dayless shifts ---
			// 2.3.2: added separate display of dayless shifts
			if ( count( $dayless_shifts ) > 0 ) {
				foreach ( $dayless_shifts as $shift ) {
					$title = __( 'This shift is disabled as no day is set.', 'radio-station' );
					echo '<div class="show-shift disabled" title="' . esc_attr( $title ) . '">' . PHP_EOL;
						$start = $shift['start_hour'] . ':' . $shift['start_min'] . $shift['start_meridian'];
						$end = $shift['end_hour'] . ':' . $shift['end_min'] . $shift['end_meridian'];
						echo esc_html( $start ) . ' - ' . esc_html( $end );
					echo '</div>' . "\n";
				}
			}
		}

	} elseif ( 'hosts' == $column ) {

		$hosts = get_post_meta( $post_id, 'show_user_list', true );
		if ( $hosts ) {
			// 2.4.0.4: convert possible (old) non-array value
			if ( !is_array( $hosts ) ) {
				$hosts = array( $hosts );
			}
			if ( is_array( $hosts ) && ( count( $hosts ) > 0 ) ) {
				foreach ( $hosts as $host ) {
					$user_info = get_userdata( $host );
					// 2.5.9: add link to host edit URL
					$host_edit_url = radio_station_get_host_edit_url( $host );
					if ( $host_edit_url ) {
						echo '<a href="' . esc_url( $host_edit_url ) . '">';
					}				
					echo esc_html( $user_info->display_name );
					if ( $host_edit_url ) {
						echo '</a>';
					}
					echo '<br>' . "\n";
				}
			}
		}

	} elseif ( 'producers' == $column ) {

		// 2.3.0: added column for Producers
		$producers = get_post_meta( $post_id, 'show_producer_list', true );
		if ( $producers ) {
			// 2.4.0.4: convert possible (old) non-array value
			if ( !is_array( $producers ) ) {
				$producers = array( $producers );
			} 
			if ( is_array( $producers ) && ( count( $producers ) > 0 ) ) {
				foreach ( $producers as $producer ) {
					$user_info = get_userdata( $producer );
					// 2.5.9: add link to host edit URL
					$producer_edit_url = radio_station_get_producer_edit_url( $producer );
					if ( $producer_edit_url ) {
						echo '<a href="' . esc_url( $producer_edit_url ) . '">';
					}	
					echo esc_html( $user_info->display_name );
					if ( $producer_edit_url ) {
						echo '<a>';
					}
					echo '<br>' . "\n";
				}
			}
		}

	} elseif ( 'show_image' == $column ) {

		// 2.3.0: get show avatar (with fallback to thumbnail)
		$image_url = radio_station_get_show_avatar_url( $post_id );
		if ( $image_url ) {
			// 2.3.3.9: fix to use esc_attr instead of esc_attr
			echo '<div class="show-image">' . "\n";
				echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( __( 'Show Avatar', 'radio-station' ) ) . '">' . "\n";
			echo '</div>' . "\n";
		}

	}
}

// -----------------------
// Show List Column Styles
// -----------------------
// 2.2.7: added show column styles
// 2.5.0: renamed from radio_station_show_column_styles for consistency
add_action( 'admin_footer', 'radio_station_show_admin_list_styles' );
function radio_station_show_admin_list_styles() {

	$current_screen = get_current_screen();
	if ( 'edit-' . RADIO_STATION_SHOW_SLUG !== $current_screen->id ) {
		return;
	}

	$css = "#shifts {width: 200px;} #active, #description, #comments {width: 50px;}
	.show-image {width: 100px;} .show-image img {width: 100%; height: auto;}
	.show-shift.disabled {border: 1px dashed orange;}
	.show-shift.conflict {border: 1px solid red;}
	.show-shift.disabled.conflict {border: 1px dashed red;}";
	
	// 2.5.0: added missing style filter
	// 2.5.6: use radio_station_add_inline_style
	$css = apply_filters( 'radio_station_show_list_styles', $css );
	// echo '<style>' . wp_kses_post( $css ) . '</style>' . "\n";
	radio_station_add_inline_style( 'rs-admin', $css );
}

// -------------------------
// Add Show Shift Day Filter
// -------------------------
// 2.2.7: added show day selection filtering
add_action( 'restrict_manage_posts', 'radio_station_show_day_filter', 10, 2 );
function radio_station_show_day_filter( $post_type, $which ) {

	if ( RADIO_STATION_SHOW_SLUG !== $post_type ) {
		return;
	}

	// -- maybe get specified day ---
	// 2.5.0: use sanitize_text_field on get request value
	$d = isset( $_GET['weekday'] ) ? sanitize_text_field( $_GET['weekday'] ) : 0;

	// --- show day selector ---
	// 2.5.0: use get_schedule_weekdays to honour start of week value
	// $days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
	$days = radio_station_get_schedule_weekdays();

	echo '<label for="filter-by-show-day" class="screen-reader-text">' . esc_html( __( 'Filter by show day', 'radio-station' ) ) . '</label>' . "\n";
	echo '<select name="weekday" id="filter-by-show-day">' . "\n";
		echo '<option value="0" ' . selected( $d, 0, false ) . '>' . esc_html( __( 'All show days', 'radio-station' ) ) . '</option>' . "\n";
		foreach ( $days as $day ) {
			echo '<option value="' . esc_attr( $day ) . '" ' . selected( $d, $day, false ) . '>';
				// 2.5.0: output translated day label directly
				echo esc_html( radio_station_translate_weekday( $day ) );
			echo '</option>' . "\n";
		}
	echo '</select>' . "\n";
}


// --------------------------
// === Schedule Overrides ===
// --------------------------

// ------------------------------
// Add Override Show Data Metabox
// ------------------------------
add_action( 'add_meta_boxes', 'radio_station_add_override_show_metabox' );
function radio_station_add_override_show_metabox() {
	$position = apply_filters( 'radio_station_metabox_position', 'rstop', 'overrides' );
	add_meta_box(
		'radio-station-override-show-metabox',
		__( 'Override Show Data', 'radio-station' ),
		'radio_station_override_show_metabox',
		RADIO_STATION_OVERRIDE_SLUG,
		$position,
		'low'
	);
}

// --------------------------
// Override Show Data Metabox
// --------------------------
function radio_station_override_show_metabox() {

	global $post, $current_screen;
	$post_id = $post->ID;

	// --- add nonce field for update verification ---
	wp_nonce_field( 'radio-station', 'show_data_nonce' );

	// --- open meta inner wrap ---
	echo '<div class="meta_inner">' . "\n";

		// --- get all shows ---
		// 2.4.0.4: fix to remove show limit in query
		// 2.4.0.4: added pending and future post statuses
		$args = array(
			'post_type'   => RADIO_STATION_SHOW_SLUG,
			'post_status' => array( 'publish', 'draft', 'pending', 'future' ),
			'orderby'     => 'modified',
			'numberposts' => -1,
		);
		$shows = get_posts( $args );

		// --- link to Show fields ---
		$linked_id = get_post_meta( $post->ID, 'linked_show_id', true );

		echo '<ul id="override-link-list">' . "\n";

			// --- link to show field ---
			echo '<li>' . "\n";
				echo '<div class="input-label"><label>' . "\n";
					echo '<b>' . esc_html( __( 'Link to Show', 'radio-station' ) ) . ': </b>' . "\n";
				echo '</label></div>' . "\n";
				echo '<div class="input-field">' . "\n";
					echo '<select id="override-link" onchange="radio_link_check();" name="linked_show_id">' . "\n";
					echo '<option value=""';
					if ( !$linked_id || ( '' == $linked_id ) ) {
						echo ' selected="selected"';
					}
					echo '>' . esc_html( __( 'No Show Link', 'radio-station' ) ) . '</option>' . "\n";
					foreach ( $shows as $i => $show ) {
						$show_id = $show->ID;
						$title = $show->post_title;
						$status = $show->post_status;
						$active = get_post_meta( $show_id, 'show_active', true );
						echo '<option value="' . esc_attr( $show_id ) . '"';
						if ( $linked_id == $show_id ) {
							echo ' selected="selected"';
						}
						echo '>' . esc_html( $show_id ) . ': ';
						if ( 'on' != $active ) {
							echo '[Inactive] ';
						}
						echo esc_html( $title );
						if ( 'draft' == $status ) {
							echo ' (Draft)';
						} elseif ( 'pending' == $status ) {
							echo ' (Pending)';
						} elseif ( 'future' == $status ) {
							echo ' (Future)';
						}
						$hosts = get_post_meta( $show_id, 'show_user_list', true );
						if ( $hosts && is_array( $hosts ) && ( count( $hosts ) > 0 ) ) {
							echo ' : ';
							$hostnames = array();
							foreach ( $hosts as $host ) {
								$user = get_user_by( 'ID', $host );
								$hostnames[] = $user->display_name;
							}
							echo implode( ', ', $hostnames );
						}
						echo '</option>' . "\n";
					}
					echo '</select>' . "\n";
				echo '</div>' . "\n";
				echo '<div class="input-helper">' . "\n";
					echo '<i>' . esc_html( __( 'If selected, Override data will be used from the Linked Show.', 'radio-station' ) ) . '</i>' . "\n";
				echo '</div>' . "\n";
			echo '</li>' . "\n";

			// --- sync genre taxonomy ---
			$sync_genres = get_post_meta( $post_id, 'sync_genres', true );
			echo '<li id="override-genres">' . "\n";
				echo '<div class="input-label">' . "\n";
					// 2.5.0: add missing translation wrapper
					echo '<label><b>' . esc_html( __( 'Sync Genres?', 'radio-station' ) ) . '</b></label>' . "\n";
				echo '</div>' . "\n";
				echo '<div class="input-field">' . "\n";
					echo '<input id="override-genres-input" type="checkbox" name="sync_genres" value="yes" onclick="radio_sync_genres();"';
					if ( 'yes' == $sync_genres ) {
						echo ' checked="checked"';
					}
					echo '>' . "\n";
				echo '</div>' . "\n";
				echo '<div class="input-helper">' . "\n";
					echo '<i>' . esc_html( __( 'If checked, assigned Genre terms are synced from the Linked Show when Updating.', 'radio-station' ) ) . '</i> ' . "\n";
				echo '</div>' . "\n";
			echo '</li>' . "\n";

			// --- sync language taxonomy ---
			// 2.5.0: fix old table cell markup to list item
			$sync_languages = get_post_meta( $post_id, 'sync_languages', true );
			echo '<li id="override-languages">' . "\n";
				echo '<div class="input-label">' . "\n";
					// 2.5.0: add missing translation wrapper
					echo '<label><b>' . esc_html( __( 'Sync Languages?', 'radio-station' ) ) . '</b></label>' . "\n";
				echo '</div>' . "\n";
				echo '<div class="input-field">' . "\n";
					echo '<input id="override-languages-input" type="checkbox" name="sync_languages" value="yes" onclick="radio_sync_languages();"';
					if ( 'yes' == $sync_languages ) {
						echo ' checked="checked"';
					}
					echo '>' . "\n";
				echo '</div>' . "\n";
				echo '<div class="input-helper">' . "\n";
					echo '<i>' . esc_html( __( 'If checked, assigned Language terms are synced from the Linked Show when Updating.', 'radio-station' ) ) . '</i> ' . "\n";
				echo '</div>' . "\n";
			echo '</li>' . "\n";

		// --- close linked show list ---
		echo '</ul><br>' . "\n";

		// --- get show meta ---
		// $active = get_post_meta( $post->ID, 'show_active', true );
		$link = get_post_meta( $post_id, 'show_link', true );
		$email = get_post_meta( $post_id, 'show_email', true );
		$phone = get_post_meta( $post_id, 'show_phone', true );
		$file = get_post_meta( $post_id, 'show_file', true );
		$download = get_post_meta( $post_id, 'show_download', true );
		$patreon_id = get_post_meta( $post_id, 'show_patreon', true );

		$linked_fields = get_post_meta( $post_id, 'linked_show_fields', true );
		echo '<div id="linked-show-fields">' . "\n";

			echo '<div id="linked-fields-message">' . "\n";
				echo '<b>' . esc_html( __( 'Usage Note', 'radio-station' ) ) . '</b>' . "\n";
				echo ': ' . esc_html( __( ' Unchecked boxes use Show data, checked boxes use Override data.', 'radio-station' ) ) . "\n";
			echo '</div><br>' . "\n";

			// --- table headings ---
			echo '<table>' . "\n";

				echo '<tr><td class="override-label">' . "\n";
					echo '<b>' . esc_html( __( 'Override?', 'radio-station' ) ) . '</b>' . "\n";
				echo '</td><td width="30" class="override-label"></td><td class="override-label">' . "\n";
					echo '<b>' . esc_html( __( 'Override Data', 'radio-station' ) ) . '</b>' . "\n";
				echo '</td></tr>' . "\n";

				// TODO: show active ?
				// echo '<p><div style="width:120px; display:inline-block;"><label>' . esc_html( __( 'Active', 'radio-station' ) ) . '?</label></div>
				// <input type="checkbox" name="show_active" ' . checked( $active, 'on', false ) . '>
				// <em>' . esc_html( __( 'Check this box if show is currently active (Show will not appear on programming schedule if unchecked.)', 'radio-station' ) ) . '</em></p>';

				// --- title ---
				echo '<tr><td class="override-label">' . "\n";
					echo '<input type="checkbox" id="override-show-title" name="show_title_link" value="yes" onclick="radio_check_linked(\'title\');"';
					if ( isset( $linked_fields['show_content'] ) && $linked_fields['show_title'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Title', 'radio-station' ) ) . '</label>' . PHP_EOL;
				echo '</td><td width="30" class="override-label"></td><td class="override-label">' . PHP_EOL;
					echo '<div id="override-input-title"';
					if ( !isset( $linked_fields['show_title'] ) || !$linked_fields['show_title'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<i>' . esc_html( __( 'Use Title Editor metabox above.', 'radio-station' ) ) . '</i>' . "\n";
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- content ---
				echo '<tr><td class="override-label">' . "\n";
					echo '<input type="checkbox" id="override-show-content" name="show_content_link" value="yes" onclick="radio_check_linked(\'content\');"';
					if ( isset( $linked_fields['show_content'] ) && $linked_fields['show_content'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Description', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30" class="override-label"></td><td class="override-label">' . "\n";
					echo '<div id="override-input-content"';
					if ( !isset( $linked_fields['show_content'] ) || !$linked_fields['show_content'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<i>' . esc_html( __( 'Use Content Editor metabox below.', 'radio-station' ) ) . '</i>' . "\n";
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- excerpt ---
				echo '<tr><td class="override-label">' . "\n";
					echo '<input type="checkbox" id="override-show-excerpt" name="show_excerpt_link" value="yes" onclick="radio_check_linked(\'excerpt\');"';
					if ( isset( $linked_fields['show_excerpt'] ) && $linked_fields['show_excerpt'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Excerpt', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30" class="override-label"></td><td class="override-label">' . "\n";
					echo '<div id="override-input-excerpt"';
					if ( !isset( $linked_fields['show_excerpt'] ) || !$linked_fields['show_excerpt'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<i>' . esc_html( __( 'Use Excerpt Editor metabox below.', 'radio-station' ) ) . '</i>' . "\n";
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- avatar ---
				echo '<tr><td class="override-label">' . "\n";
					echo '<input type="checkbox" id="override-show-avatar" name="show_avatar_link" value="yes" onclick="radio_check_linked(\'avatar\');"';
					if ( isset( $linked_fields['show_avatar'] ) && $linked_fields['show_avatar'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Show Avatar', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30" class="override-label"></td><td class="override-label">' . "\n";
					echo '<div id="override-input-avatar"';
					if ( !isset( $linked_fields['show_avatar'] ) || !$linked_fields['show_avatar'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<i>' . esc_html( __( 'Use Show Images metabox.', 'radio-station' ) ) . '</i>' . "\n";
					echo '</div>' . "\n";
					echo '<div id="override-data-avatar"';
					if ( isset( $linked_fields['show_avatar'] ) && $linked_fields['show_avatar'] ) {
						echo ' style="display:none;"';
					}
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- featured image ---
				echo '<tr><td class="override-label">' . "\n";
					echo '<input type="checkbox" id="override-show-image" name="show_image_link" value="yes" onclick="radio_check_linked(\'image\');"';
					if ( isset( $linked_fields['show_image'] ) && $linked_fields['show_image'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Featured Image', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30" class="override-label"></td><td class="override-label">' . "\n";
					echo '<div id="override-input-image"';
					if ( !isset( $linked_fields['show_image'] ) || !$linked_fields['show_image'] ) {
						echo ' style="display:none;"';
					}
					echo '>';
						echo '<i>' . esc_html( __( 'Use Feature Image metabox.', 'radio-station' ) ) . '</i>' . "\n";
					echo '</div>' . "\n";
					echo '<div id="override-data-image"';
					if ( isset( $linked_fields['show_image'] ) && $linked_fields['show_image'] ) {
						echo ' style="display:none;"';
					}
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- hosts ---
				echo '<tr><td class="override-label">' . "\n";
					echo '<input type="checkbox" id="override-show-hosts" name="show_user_list_link" value="yes" onclick="radio_check_linked(\'hosts\');"';
					if ( isset( $linked_fields['show_user_list'] ) && $linked_fields['show_user_list'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Hosts', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30" class="override-label"></td><td class="override-label">' . "\n";
					echo '<div id="override-input-hosts"';
					if ( !isset( $linked_fields['show_user_list'] ) || !$linked_fields['show_user_list'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<i>' . esc_html( __( 'Use Host assignment box below.', 'radio-station' ) ) . '</i>' . "\n";
					echo '</div>' . "\n";
					echo '<div id="override-data-hosts"';
					if ( isset( $linked_fields['show_user_list'] ) && $linked_fields['show_user_list'] ) {
						echo ' style="display:none;"';
					}
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- producers ---
				echo '<tr><td class="override-label">' . "\n";
					echo '<input type="checkbox" id="override-show-producers" name="show_producer_list_link" value="yes" onclick="radio_check_linked(\'producers\');"';
					if ( isset( $linked_fields['show_producer_list'] ) && $linked_fields['show_producer_list'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Producers', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30" class="override-label"></td><td class="override-label">' . "\n";
					echo '<div id="override-input-producers"';
					if ( !isset( $linked_fields['show_producer_list'] ) || !$linked_fields['show_producer_list'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<i>' . esc_html( __( 'Use Producer assignment box below.', 'radio-station' ) ) . '</i>' . "\n";
					echo '</div>' . "\n";
					echo '<div id="override-data-producers"';
					if ( isset( $linked_fields['show_producer_list'] ) && $linked_fields['show_producer_list'] ) {
						echo ' style="display:none;"';
					}
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- website ---
				echo '<tr><td>' . "\n";
					echo '<input type="checkbox" class="override-checkbox" id="override-show-website" name="show_link_link" value="yes" onclick="radio_check_linked(\'website\');"';
					if ( isset( $linked_fields['show_link'] ) && $linked_fields['show_link'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Website Link', 'radio-station' ) ) . '</label>';
				echo '</td><td width="30"></td><td>' . "\n";
					echo '<div id="override-input-website" class="override-input"';
					if ( !isset( $linked_fields['show_link'] ) || !$linked_fields['show_link'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<input type="text" name="show_link" size="100" value="' . esc_url( $link ) . '" style="max-width:200px;">';
					echo '</div>' . "\n";
					echo '<div id="override-data-link"' . "\n";
					if ( isset( $linked_fields['show_link'] ) && $linked_fields['show_link'] ) {
						echo ' style="display:none;"';
					}
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- email ---
				echo '<tr><td>' . "\n";
					echo '<input type="checkbox" class="override-checkbox" id="override-show-email" name="show_email_link" value="yes" onclick="radio_check_linked(\'email\');"';
					if ( isset( $linked_fields['show_email'] ) && $linked_fields['show_email'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Show Email', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30"></td><td>' . "\n";
					echo '<div id="override-input-email" class="override-input"';
					if ( !isset( $linked_fields['show_email'] ) || !$linked_fields['show_email'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<input type="text" name="show_email" size="100" value="' . esc_attr( $email ) . '" style="max-width:200px;">';
					echo '</div>' . "\n";
					echo '<div id="override-data-email"';
					if ( isset( $linked_fields['show_email'] ) && $linked_fields['show_email'] ) {
						echo ' style="display:none;"';
					}
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- phone ---
				echo '<tr><td>' . "\n";
					echo '<input type="checkbox" class="override-checkbox" id="override-show-phone" name="show_phone_link" value="yes" onclick="radio_check_linked(\'phone\');"';
					if ( isset( $linked_fields['show_phone'] ) && $linked_fields['show_phone'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Show Phone', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30"></td><td>' . "\n";
					echo '<div id="override-input-phone" class="override-input"';
					if ( !isset( $linked_fields['show_phone'] ) || !$linked_fields['show_phone'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<input type="text" name="show_phone" size="100" value="' . esc_attr( $phone ) . '" style="max-width:200px;">';
					echo '</div>' . "\n";
					echo '<div id="override-data-phone"';
					if ( isset( $linked_fields['show_phone'] ) && $linked_fields['show_phone'] ) {
						echo ' style="display:none;"';
					}
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- audio file ---
				echo '<tr><td>' . "\n";
					echo '<input type="checkbox" class="override-checkbox" id="override-show-file" name="show_file_link" value="yes" onclick="radio_check_linked(\'file\');"';
					if ( isset( $linked_fields['show_file'] ) && $linked_fields['show_file'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Latest Audio', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30"></td><td>' . "\n";
					echo '<div id="override-input-file" class="override-input"';
					if ( !isset( $linked_fields['show_file'] ) || !$linked_fields['show_file'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<input type="text" name="show_file" size="100" value="' . esc_attr( $file ) . '" style="max-width:200px;">';
					echo '</div>' . "\n";
					echo '<div id="override-data-file"';
					if ( isset( $linked_fields['show_file'] ) && $linked_fields['show_file'] ) {
						echo ' style="display:none;"';
					}
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- disable download ---
				echo '<tr><td>' . "\n";
					echo '<input type="checkbox" class="override-checkbox" id="override-show-download" name="show_download_link" value="yes" onclick="radio_check_linked(\'download\');"';
					if ( isset( $linked_fields['show_download'] ) && $linked_fields['show_download'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Disable Download', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30"></td><td>' . "\n";
					echo '<div id="override-input-download" class="override-input"';
					if ( !isset( $linked_fields['show_download'] ) || !$linked_fields['show_download'] ) {
						echo ' style="display:none;"';
					}
					echo '>' . "\n";
						echo '<input type="checkbox" name="show_download" ' . checked( $download, 'on', false ) . '>';
						echo ' <i>' . esc_html( __( 'Check to Disable', 'radio-station' ) ) . '</i>' . "\n";
					echo '</div>' . "\n";
					echo '<div id="override-data-download"';
					if ( isset( $linked_fields['show_download'] ) && $linked_fields['show_download'] ) {
						echo ' style="display:none;"';
					}
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				// --- patreon ---
				echo '<tr><td>' . "\n";
					echo '<input type="checkbox" class="override-checkbox" id="override-show-patreon" name="show_patreon_link" value="yes" onclick="radio_check_linked(\'patreon\');"';
					if ( isset( $linked_fields['show_patreon'] ) && $linked_fields['show_patreon'] ) {
						echo ' checked="checked"';
					}
					echo '> <label>' . esc_html( __( 'Patreon Page ID', 'radio-station' ) ) . '</label>' . "\n";
				echo '</td><td width="30"></td><td>' . "\n";
					echo '<div id="override-input-patreon" class="override-input"';
					if ( !isset( $linked_fields['show_patreon'] ) || !$linked_fields['show_patreon'] ) {
						echo ' style="display:none;"';
					}
					echo '> https://patreon.com/<input type="text" name="show_patreon" size="80" value="' . esc_attr( $patreon_id ) . '" style="max-width:100px;">' . PHP_EOL;
					echo '</div>' . "\n";
					echo '<div id="override-data-patreon"';
					if ( isset( $linked_fields['show_patreon'] ) && $linked_fields['show_patreon'] ) {
						echo ' style="display:none;"';
					}
					echo '</div>' . "\n";
				echo '</td></tr>' . "\n";

				do_action( 'radio_station_show_fields', $post_id, 'override' );

			// --- close field table ---
			echo '</table>' . "\n";
		echo '</div>' . "\n";

	// --- close meta inner ---
	echo '</div><br>' . "\n";

	// --- inside show metaboxes ---
	$inside_metaboxes = array(
		'hosts'     => array(
			'title'    => __( 'Override DJ(s) / Host(s)', 'radio-station' ),
			'callback' => 'radio_station_show_hosts_metabox',
		),
		'producers' => array(
			'title'    => __( 'Override Producer(s)', 'radio-station' ),
			'callback' => 'radio_station_show_producers_metabox',
		),
	);

	// --- display inside metaboxes ---
	echo '<div id="show-inside-metaboxes">';
	$i = 1;
	foreach ( $inside_metaboxes as $key => $metabox ) {

		$classes = array( 'postbox' );
		if ( 1 == $i ) {
			$classes[] = 'first';
		} elseif ( count( $inside_metaboxes ) == $i ) {
			$classes[] = 'last';
		}
		$class = implode( ' ', $classes );

		// --- open inside metabox
		echo '<div id="' . esc_attr( $key ) . '" class="' . esc_attr( $class ) . '"';
		if ( $linked_id ) {
			if ( ( 'hosts' == $key ) && ( !isset( $linked_fields['show_hosts'] ) || !$linked_fields['show_patreon'] ) ) {
				echo ' style="display:none;"';
			}
			if ( ( 'producers' == $key ) && ( !isset( $linked_fields['show_producers'] ) || !$linked_fields['show_producers'] ) ) {
				echo ' style="display:none;"';
			}
		}
		echo '>' . "\n";

			// --- inside metabox contents ---
			echo '<h2><span>' . esc_html( $metabox['title'] ) . '</span></h2>' . "\n";
			echo '<div class="inside">' . "\n";
				call_user_func( $metabox['callback'] );
			echo '</div>' . "\n";

		// --- close inside metabox ---
		echo '</div>' . "\n";
		$i++;
	}
	echo '</div>' . "\n";

	// --- input list field styles ---
	// 2.3.3.9: add styles for table to list conversion
	$css = ".input-label, .input-field, .input-helper {display: inline-block;}
	.input-field {max-width: 120px;}
	.input-field, .input-helper {margin-left: 20px;}" . "\n";

	// --- output inside metabox styles ---
	$css .= "#show-inside-metaboxes .postbox {display: inline-block; min-width: 230px; max-width: 250px; vertical-align: top;}
	#show-inside-metaboxes .postbox.first {margin-right: 20px;}
	#show-inside-metaboxes .postbox.last {margin-left: 20px;}
	#show-inside-metaboxes .postbox select {max-width: 200px;}" . "\n";

	// 2.3.3.9: filter
	// 2.5.0: use wp_kses_post on style output
	// 2.5.6: use radio_station_add_inline_style
	$css = apply_filters( 'radio_station_override_edit_styles', $css );
	// echo '<style>' . wp_kses_post( $css ) . '</style>';
	radio_station_add_inline_style( 'rs-admin', $css );

	// --- get override show script ---
	$js = radio_station_override_show_script();

	// --- enqueue inline script ---
	// 2.5.0: use radio_station_add_inline_script
	radio_station_add_inline_script( 'radio-station-admin', $js );
}

// -------------------------
// Override Show Data Script
// -------------------------
function radio_station_override_show_script() {

	$genres_div = '#' . RADIO_STATION_GENRES_SLUG . 'div';
	$languages_div = '#' . RADIO_STATION_LANGUAGES_SLUG . 'div';

	// --- check for linked show value ---
	// 2.3.3.9: check linked show value
	$js = "function radio_link_check() {
		linked_id = jQuery('#override-link').val();
		if (linked_id == '') {
			jQuery('.override-label, .override-checkbox, #override-genres, #override-languages').hide();
			jQuery('.override-input, " . esc_js( $genres_div ) . ", " . esc_js( $languages_div ) . ", #hosts, #producers').show();
			jQuery('#linked-fields-message').hide();
		} else {
			jQuery('.override-label, .override-checkbox, #override-genres, #override-languages').show();
			keys = ['website', 'email', 'phone', 'file', 'download', 'patreon', 'hosts', 'producers'];
			for (i = 0; i < keys.length; i++) {radio_check_linked(keys[i]);}
			jQuery('#linked-fields-message').show();
			radio_sync_genres(); radio_sync_languages();
		}
	}" . "\n";

	// --- show/hide linked input field ---
	$js .= "function radio_check_linked(id) {
		/* console.log(id); */
		display = jQuery('#override-show-'+id).prop('checked');
		divid = '#override-input-'+id; dataid = '#override-data-'+id;
		if (id == 'content') {divid += ', #wp-content-wrap, #post-status-info';}
		else if (id == 'excerpt') {divid += ', #postexcerpt';}
		else if (id == 'hosts') {divid += ', #hosts';}
		else if (id == 'producers') {divid += ', #producers';}
		/* console.log(divid); */
		if (display) {jQuery(dataid).hide(); jQuery(divid).show();}
		else {jQuery(divid).hide(); jQuery(dataid).show();}
	}" . "\n";

	// --- show/hide genre taxonomy metaboxs ---
	$js .= "function radio_sync_genres() {
		checked = jQuery('#override-genres-input').prop('checked');
		if (checked) {jQuery('" . esc_js( $genres_div ) . "').hide();}
		else {jQuery('" . esc_js( $genres_div ) . "').show();}
	}" . "\n";

	// --- show/hide language taxonomy metabox ---
	$js .= "function radio_sync_languages() {
		checked = jQuery('#override-languages-input').prop('checked');
		if (checked) {jQuery('" . esc_js( $languages_div ) . "').hide();}
		else {jQuery('" . esc_js( $languages_div ) . "').show();}
	}" . "\n";

	// --- check to hide linked metaboxes on pageload ---
	$js .= "jQuery(document).ready(function() {radio_link_check();});" . "\n";

	// --- filter and return ---
	$js = apply_filters( 'radio_station_override_show_script', $js );
	return $js;
}

// -----------------------------
// Add Schedule Override Metabox
// -----------------------------
// --- Add schedule override box to override edit screens ---
add_action( 'add_meta_boxes', 'radio_station_add_schedule_override_metabox' );
function radio_station_add_schedule_override_metabox() {
	// 2.2.2: add high priority to show at top of edit screen
	// 2.3.0: set position to top to be above editor box
	// 2.3.0: update meta box ID for consistency
	// 2.3.2: filter top metabox position
	// 2.3.3.9: change priority to low to be below show data box
	$position = apply_filters( 'radio_station_metabox_position', 'rstop', 'overrides' );
	add_meta_box(
		'radio-station-override-schedule-metabox',
		__( 'Override Schedule', 'radio-station' ),
		'radio_station_schedule_override_metabox',
		RADIO_STATION_OVERRIDE_SLUG,
		$position,
		'high'
	);
}

// -------------------------
// Schedule Override Metabox
// -------------------------
function radio_station_schedule_override_metabox() {

	global $post, $current_screen;

	// --- add nonce field for update verification ---
	wp_nonce_field( 'radio-station', 'show_override_nonce' );

	// 2.2.7: add explicit width to date picker field to ensure date is visible
	// 2.3.0: convert template style output to straight php output
	// 2.3.3.9: change meta_inner ID to class
	echo '<div class="meta_inner">' . "\n";

		// --- override metabox bottom action ---
		// 2.5.0: added override metabox action
		do_action( 'radio_station_override_metabox_top' );

		// --- override list table ---
		echo '<div id="overrides-list">' . "\n";
			$table = radio_station_overrides_table( $post->ID );
			if ( '' != $table['html'] ) {
				// 2.5.0: use wp_kses on table output
				$allowed = radio_station_allowed_html( 'content', 'settings' );
				// 2.5.3: fix to incorrect table output key
				echo wp_kses( $table['html'], $allowed );
			}
		echo '</div>' . "\n";

		// --- override save/add buttons ---
		// 2.3.3.9: added for AJAX save and multiple overrides
		// 2.3.3.9: change override-table-buttons to override-buttons
		echo '<center><table class="override-buttons" width="100%">' . "\n";
			echo '<tr><td width="33%" align="center">' . "\n";
				echo '<input type="button" class="overrides-clear button-secondary" value="' . esc_attr( __( 'Clear Overrides', 'radio-station' ) ) . '" onclick="radio_overrides_clear();">' . "\n";
			echo '</td><td width="33%" align="center">' . "\n";
			if ( !is_object( $current_screen ) || ( 'add' != $current_screen->action ) ) {
				echo '<input type="button" class="overrides-save button-primary" value="' . esc_attr( __( 'Save Overrides', 'radio-station' ) ) . '" onclick="radio_overrides_save();">' . "\n";
			}
			echo '</td><td width="33%" align="center">' . "\n";
				echo '<input type="button" class="override-add button-secondary" value="' . esc_attr( __( 'Add Override', 'radio-station' ) ) . '" onclick="radio_override_new();">' . "\n";
			echo '</td></tr>' . "\n";

			// 2.3.3.9: change to single cell spanning 3 columns
			echo '<tr><td colspan="3" align="center">' . "\n";
				echo '<div id="overrides-saving-message" style="display:none;">' . esc_html( __( 'Saving Overrides...', 'radio-station' ) ) . '</div>' . "\n";
				echo '<div id="overrides-saved-message" style="display:none;">' . esc_html( __( 'Overrides Saved.', 'radio-station' ) ) . '</div>' . "\n";
				echo '<div id="overrides-error-message" style="display:none;"></div>' . "\n";
			echo '</td></tr>' . "\n";
		echo '</table></center>' . "\n";

		// --- override list styles ---
		// 2.3.0: added datepicker z-index to fix conflict with editor buttons
		// 2.3.3.9: apply class styles to override list
		// 2.3.3.9: moved styles to separate function
		// 2.5.0: use wp_kses_post on style output
		// 2.5.6: use radio_station_add_inline_style
		$css = radio_station_overrides_list_styles();
		// echo '<style>' . wp_kses_post( $css ) . '</style>';
		radio_station_add_inline_style( 'rs-admin', $css );

		// --- enqueue datepicker script and styles ---
		// 2.3.0: enqueue for override post type only
		radio_station_enqueue_datepicker();

		// --- get override edit javascript ---
		$js = radio_station_override_edit_script();

		// --- initialize datepicker fields ---
		// 2.3.3.9: also initialize end date field
		$js .= "jQuery(document).ready(function() {" . "\n";
		$js .= "	jQuery('.override-date').each(function() {" . "\n";
		$js .= "		jQuery(this).datepicker({dateFormat : 'yy-mm-dd'});" . "\n";
		$js .= "	});" . "\n";
		$js .= "});" . "\n";

		// --- enqueue inline script ---
		// 2.3.0: enqeue instead of echoing
		// 2.5.0: use radio_station_add_inline_script
		radio_station_add_inline_script( 'radio-station-admin', $js );

		// --- override metabox bottom action ---
		// 2.5.0: added override metabox action
		do_action( 'radio_station_override_metabox_bottom' );

	// --- close meta inner ---
	echo '</div>' . "\n";
}

// ---------------------
// Overrides List Styles
// ---------------------
// 2.3.3.9: moved to separate function
function radio_station_overrides_list_styles() {

	// 2.3.3.9: change override-table-buttons to override-buttons
	// 2.3.3.9: added maximum width for override lists
	// 2.5.0: adding missing vertical-align on list items
	$css = "#overrides-list, #new-overrides {max-width: 960px;}
	body.post-type-override #ui-datepicker-div {z-index: 1001 !important;}
	.override-list {list-style: none;}
	.override-list .override-item {display: inline-block; margin-left: 20px; vertical-align: middle;}
	.override-list .override-item.first-item {margin-left: 10px;}
	.override-list .override-item.last-item {margin-right: 10px;}
	.override-date {width: 100px; text-align: center;}
	.override-select {min-width:35px;}
	.override-duplicate, .override-remove {cursor:pointer;}
	.override-buttons .overrides-clear, .override-buttons .overrides-save, .override-buttons .override-add {
		cursor: pointer; display:block; width: 150px; padding: 8px; text-align: center; line-height: 1em;}
	#overrides-saving-message, #overrides-saved-message {
		background-color: lightYellow; border: 1px solid #E6DB55; margin-top: 10px; font-weight: bold; max-width: 300px; padding: 5px 0;}" . PHP_EOL;

	// 2.3.3.9: added override edit styles filter
	$css = apply_filters( 'radio_station_override_list_edit_styles', $css );
	return $css;
}


// --------------------
// Overrides Table List
// --------------------
// 2.3.3.9: separated out for AJAX saving/display
function radio_station_overrides_table( $post_id ) {

	// --- get meridiem translation ---
	// 2.2.7: added meridiem translations
	$am = radio_station_translate_meridiem( 'am' );
	$pm = radio_station_translate_meridiem( 'pm' );

	// --- set hours and minutes arrays ---
	// 2.5.0: remove days as not needed here
	$hours = $mins = array();
	for ( $i = 1; $i < 13; $i++ ) {
		$hours[$i] = number_format_i18n( $i );
	}
	for ( $i = 0; $i < 60; $i++ ) {
		if ( $i < 10 ) {
			$min = number_format_i18n( 0 ) . number_format_i18n( $i );
			// 2.5.0: make sure value has leading 0
			$i = '0' . $i;
		} else {
			$min = number_format_i18n( $i );
		}
		$mins[$i] = $min;
	}

	// --- get the saved meta as an array ---
	$overrides = get_post_meta( $post_id, 'show_override_sched', true );
	// 2.3.3.9: maybe convert single override to array
	if ( $overrides && is_array( $overrides ) && array_key_exists( 'date', $overrides ) ) {
		$overrides = array( $overrides );
		update_post_meta( $post_id, 'show_override_sched', $overrides );
	}

	// 2.2.3.9: loop to add unique shift IDs and maybe resave
	if ( $overrides && is_array( $overrides ) && ( count( $overrides ) > 0 ) )  {
		$update_overrides = false;
		foreach ( $overrides as $j => $data ) {
			if ( !isset( $data['id'] ) ) {
				$data['id'] = radio_station_unique_shift_id();
				$overrides[$j] = $data;
				$update_overrides = true;
			}
		}
		if ( $update_overrides ) {
			update_post_meta( $post_id, 'show_override_sched', $overrides );
		}
	}

	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Current Overrides: ' . esc_html( print_r( $overrides, true ) ) . '</span>' . "\n";
	}

	if ( !$overrides ) {

		// --- set empty override time array ---
		$times = array(
			'date'           => '',
			'start_hour'     => '',
			'start_min'      => '',
			'start_meridian' => '',
			'end_hour'       => '',
			'end_min'        => '',
			'end_meridian'   => '',
			// 'multiday'    => '',
			// 'end_date'    => '',
			'disabled'       => '',
		);

		// --- check and sanitize possibly posted times ---
		// 2.3.3.9: for adding new override time by querystring
		if ( isset( $_REQUEST['date'] ) ) {
			$times['date'] = sanitize_text_field( $_REQUEST['date'] );
			// 2.5.6: use radio_station_get_time instead of date
			$times['date'] = radio_station_get_time( 'Y-m-d', strtotime( $times['date'] ) );
		}
		if ( isset( $_REQUEST['start_hour'] ) ) {
			// 2.5.6: fix to key as variable
			$times['start_hour'] = absint( $_REQUEST['start_hour'] );
			if ( ( $times['start_hour'] < 1 ) || ( $times['start_hour'] > 12 ) ) {
				$times['start_hour'] = 1;
			}
		}
		if ( isset( $_REQUEST['start_min'] ) ) {
			$times['start_min'] = absint( $_REQUEST['start_min'] );
			if ( ( $times['start_min'] < 1 ) || ( $times['start_min'] > 60 ) ) {
				$times['start_min'] = 0;
			}
			if ( $times['start_min'] < 10 ) {
				$times['start_min'] = '0' . $times['start_min'];
			}
		}
		if ( isset( $_REQUEST['start_meridian'] ) ) {
			$times['start_meridian'] = sanitize_text_field( $_REQUEST['start_meridian'] );
			if ( !in_array( $times['start_meridian'], array( '', 'am', 'pm' ) ) ) {
				$times['start_meridian'] = '';
			}
		}
		if ( isset( $_REQUEST['end_hour'] ) ) {
			$times['end_hour'] = absint( $_REQUEST['end_hour'] );
			if ( ( $times['end_hour'] < 1 ) || ( $times['end_hour'] > 12 ) ) {
				$times['end_hour'] = 1;
			}
		}
		if ( isset( $_REQUEST['end_min'] ) ) {
			$times['end_min'] = absint( $_REQUEST['end_min'] );
			if ( ( $times['end_min'] < 1 ) || ( $times['end_min'] > 60 ) ) {
				$times['end_min'] = 0;
			}
			if ( $times['end_min'] < 10 ) {
				$times['end_min'] = '0' . $times['end_min'];
			}
		}
		if ( isset( $_REQUEST['end_meridian'] ) ) {
			$times['end_meridian'] = sanitize_text_field( $_REQUEST['end_meridian'] );
			if ( !in_array( $times['end_meridian'], array( '', 'am', 'pm' ) ) ) {
				$times['end_meridian'] = '';
			}
		}
		if ( isset( $_REQUEST['disabled'] ) ) {
			$times['disabled'] = sanitize_text_field( $_REQUEST['disabled'] );
			if ( !in_array( $times['disabled'], array( '', 'yes' ) ) ) {
				$times['disabled'] = '';
			}
		}

		// 2.2.8: fix undefined index warnings for new schedule overrides
		$overrides = array( $times );
	}

	// 2.3.3.9: loop possible multiple overrides
	$list = '';
	foreach ( $overrides as $i => $override ) {

		// 2.3.3.9: use override shift ID for override wrapper ID
		$id = $override['id'];
		$list .= '<div id="override-wrapper-' . esc_attr( $id ) . '" class="override-wrapper">' . "\n";

			$list .= '<ul id="override-' . esc_attr( $i ) . '" class="override-list">' . "\n";

				// 2.3.3.9: add hidden input for unique override time ID
				$list .= '<input id="override-' . esc_attr( $i ) . '-id" type="hidden" name="show_sched[' . esc_attr( $i ) . '][id]" value="' . esc_attr( $id ) . '">' . "\n";

				// --- Override (Start) Date ---
				$list .= '<li class="override-item first-item">' . "\n";

					$list .= '<label>' . esc_html( __( 'Start Date', 'radio-station' ) ) . '</label>: ';
					$date = ( !empty( $override['date'] ) ) ? trim( $override['date'] ) : '';
					$list .= '<input type="text" id="override-' . esc_attr( $i ) . '-date" class="override-date" name="show_sched[' . $i . '][date]" value="' . esc_attr( $date ) . '">' . "\n";
					$list .= '<input type="hidden" id="override-date-' . esc_attr( $i ) . '" value="' . esc_attr( $date ) . '">' . "\n";

				$list .= '</li>' . "\n";

				// --- Override Start Time ---
				$list .= '<li class="override-item">' . "\n";

					// --- start time label --
					$list .= '<label>' . esc_html( __( 'Start Time', 'radio-station' ) ) . '</label>:' . "\n";

					// --- start hour ---
					$list .= '<select id="override-' . esc_attr( $i ) . '-start-hour" name="show_sched[' . esc_attr( $i ) . '][start_hour]" class="override-select">' . "\n";
						$list .= '<option value=""></option>' . "\n";
						// 2.5.0: use hour array and possibly translated label
						foreach ( $hours as $hour => $label ) {
							$list .= '<option value="' . esc_attr( $hour ) . '" ' . selected( $override['start_hour'], $hour, false ) . '>' . esc_html( $label ) . '</option>' . "\n";
						}
					$list .= '</select>' . "\n";
					$list .= '<input type="hidden" id="override-start-hour-' . esc_attr( $i ) . '" value="' . esc_attr( $override['start_hour'] ) . '">' . "\n";

					// --- start minute ---
					$list .= '<select id="override-' . esc_attr( $i ) . '-start-min" name="show_sched[' . esc_attr( $i ) . '][start_min]" class="override-select">' . "\n";
						$list .= '<option value=""></option>' . "\n";
						$list .= '<option value="00">' . number_format_i18n( 0 ) . number_format_i18n( 0 ) . '</option>' . "\n";
						$list .= '<option value="15">' . number_format_i18n( 15 ) . '</option>' . "\n";
						$list .= '<option value="30">' . number_format_i18n( 30 ) . '</option>' . "\n";
						$list .= '<option value="45">' . number_format_i18n( 45 ) . '</option>' . "\n";
						// 2.5.0: use minute array and possibly translated label
						foreach ( $mins as $min => $label ) {
							$list .= '<option value="' . esc_attr( $min ) . '" ' . selected( $override['start_min'], $min, false ) . '>' . esc_html( $label ) . '</option>' . "\n";
						}
					$list .= '</select>' . "\n";
					$list .= '<input type="hidden" id="override-start-min-' . esc_attr( $i ) . '" value="' . esc_attr( $override['start_min'] ) . '">' . "\n";

					// --- start meridian ---
					$list .= '<select id="override-' . esc_attr( $i ) . '-start-meridian" name="show_sched[' . esc_attr( $i ) . '][start_meridian]" class="override-select">' . "\n";
						$list .= '<option value=""></option>' . "\n";
						$list .= '<option value="am" ' . selected( $override['start_meridian'], 'am', false ) . '>' . esc_html( $am ) . '</option>' . "\n";
						$list .= '<option value="pm" ' . selected( $override['start_meridian'], 'pm', false ) . '>' . esc_html( $pm ) . '</option>' . "\n";
					$list .= '</select>' . "\n";
					$list .= '<input type="hidden" id="override-start-meridian-' . esc_attr( $i ) . '" value="' . esc_attr( $override['start_meridian'] ) . '">' . "\n";

				$list .= '</li>' . "\n";

				// --- Override End Time ---
				// 2.3.4: add common end minutes to top of options
				$list .= '<li class="override-item">' . "\n";

					// --- end time label ---
					$list .= '<label>' . esc_html( __( 'End Time', 'radio-station' ) ) . '</label>:' . "\n";

					// --- end hour ---
					$list .= '<select id="override-' . esc_attr( $i ) . '-end-hour" name="show_sched['. esc_attr( $i ) . '][end_hour]" class="override-select">' . "\n";
						$list .= '<option value=""></option>' . "\n";
						// 2.5.0: use hour array and possibly translated label
						foreach ( $hours as $hour => $label ) {
							$list .= '<option value="' . esc_attr( $hour ) . '" ' . selected( $override['end_hour'], $hour, false ) . '>' . esc_html( $label ) . '</option>' . "\n";
						}
					$list .= '</select>' . "\n";
					$list .= '<input type="hidden" id="override-end-hour-' . esc_attr( $i ) . '" value="' . esc_attr( $override['end_hour'] ) . '">' . "\n";

					// --- end minutes ---
					$list .= '<select id="override-' . esc_attr( $i ) . '-end-min" name="show_sched[' . esc_attr( $i ) . '][end_min]" class="override-select">' . "\n";
						$list .= '<option value=""></option>' . "\n";
						$list .= '<option value="00">' . number_format_i18n( 0 ) . number_format_i18n( 0 ) . '</option>' . "\n";
						$list .= '<option value="15">' . number_format_i18n( 15 ) . '</option>' . "\n";
						$list .= '<option value="30">' . number_format_i18n( 30 ) . '</option>' . "\n";
						$list .= '<option value="45">' . number_format_i18n( 45 ) . '</option>' . "\n";
						// 2.5.0: use hour array and possibly translated label
						foreach ( $mins as $min => $label ) {
							$list .= '<option value="' . esc_attr( $min ) . '"' . selected( $override['end_min'], $min, false ) . '>' . esc_html( $label ) . '</option>' . "\n";
						}
					$list .= '</select>' . "\n";
					$list .= '<input type="hidden" id="override-end-min-' . esc_attr( $i ) . '" value="' . esc_attr( $override['end_min'] ) . '">' . "\n";

					// --- end meridian ---
					$list .= '<select id="override-' . esc_attr( $i ) . '-end-meridian" name="show_sched[' . esc_attr( $i ) . '][end_meridian]" class="override-select">' . "\n";
						$list .= '<option value=""></option>' . "\n";
						$list .= '<option value="am" ' . selected( $override['end_meridian'], 'am', false ) . '>' . esc_html( $am ) . '</option>' . "\n";
						$list .= '<option value="pm" ' . selected( $override['end_meridian'], 'pm', false ) . '>' . esc_html( $pm ) . '</option>' . "\n";
					$list .= '</select>' . "\n";
					$list .= '<input type="hidden" id="override-end-meridian-' . esc_attr( $i ) . '" value="' . esc_attr( $override['end_meridian'] ) . '">' . "\n";

				$list .= '</li>' . "\n";

				// --- multiday switch ---
				// 2.3.3.9: added multiday checkbox prototype
				/* $list .= '<li class="override-item" style="display:none;">' . "\n";
					$list .= '<input id="override-' . esc_attr( $i ) . '-multiday" type="checkbox" value="yes" onchange="radio_check_multiday(' . esc_attr( $i ). ');" name="show_sched[' . esc_attr( $i ) . '][multiday]\"';
					if ( isset( $override['multiday'] ) && ( 'yes' == $override['multiday'] ) ) {
						$list .= ' checked="checked"';
					}
					$list .= '> <label>' . esc_html( __( 'Multiday', 'radio-station' ) ) . '</label>' . "\n";
					$list .= '<input type="hidden" id="override-multiday-' . esc_attr( $i ) . '"';
					if ( isset( $override['multiday'] ) && ( 'yes' == $override['multiday'] ) ) {
						$list .= 'value="yes"';
					} else {
						$list .= 'value=""';
					}
					$list .= '>' . "\n";
				$list .= '</li>' . "\n"; */

				// --- multiday end date ---
				// 2.3.3.9: added multiday end date prototype
				/* $list .= '<li class="override-item" id="override-' . esc_attr( $i ) . '-end-date"';
				if ( !isset( $override['multiday'] ) || ( 'yes' != $override['multiday'] ) ) {
					$list .= ' style="display:none;"';
				}
				$list .= '>' . "\n";
					$list .= '<label>' . esc_html( __( 'End Date', 'radio-station' ) ) . '</label>: ' . "\n";
					$list .= '<input type="text" id="override-' . esc_attr( $i ) . '-end-date" name="show_sched[' . esc_attr( $i ) . '][end_date]" class="override-date">' . "\n";
				$list .= '</li>' . "\n"; */

				// --- disabled ---
				// 2.3.3.9: added disabled override checkox
				$list .= '<li class="override-item override-disable">' . "\n";
					$list .= '<input id="override-' . esc_attr( $i ) . '-disabled" type="checkbox" value="yes" onchange="radio_check_disabled(' . esc_attr( $i ). ');" name="show_sched[' . esc_attr( $i ) . '][disabled]"';
					if ( isset( $override['disabled'] ) && ( 'yes' == $override['disabled'] ) ) {
						$list .= ' checked="checked"';
					}
					$list .= '> <label>' . esc_html( __( 'Disabled', 'radio-station' ) ) . '</label>' . "\n";
					$list .= '<input type="hidden" id="override-disabled-' . esc_attr( $i ) . '" value="';
					if ( isset( $override['disabled'] ) && ( 'yes' == $override['disabled'] ) ) {
						$list .= 'yes';
					}
					$list .= '">' . "\n";
				$list .= '</li>' . "\n";

				// --- duplicate shift icon ---
				$list .= '<li class="override-item">' . "\n";
					$title = __( 'Duplicate Override', 'radio-station' );
					$list .= '<span id="override-' . esc_attr( $i ) . '-duplicate" class="override-duplicate dashicons dashicons-admin-page" title="' . esc_attr( $title ) . '" onclick="radio_override_duplicate(this);"></span>' . PHP_EOL;
				$list .= '</li>' . "\n";

				// --- remove shift icon ---
				$list .= '<li class="override-item last-item">' . "\n";
					$title = __( 'Remove Override', 'radio-station' );
					$list .= '<span id="override-' . esc_attr( $i ) . '-remove" class="override-remove dashicons dashicons-no" title="' . esc_attr( $title ) . '" onclick="radio_override_remove(this);"></span>' . PHP_EOL;
				$list .= '</li>' . "\n";

			$list .= '</ul>' . "\n";
		$list .= '</div>' . "\n";

	}

	// --- new overrides div ---
	$list .= '<div id="new-overrides"></div>' . "\n";

	// --- set table output to return ---
	$table = array(
		'html' => $list,
		// 'conflicts' => $conflicts,
	);

	return $table;
}

// -------------------------
// Override List Edit Script
// -------------------------
function radio_station_override_edit_script() {

	// --- get meridiem translation ---
	$am = radio_station_translate_meridiem( 'am' );
	$pm = radio_station_translate_meridiem( 'pm' );

	// --- set hours and minutes arrays ---
	// 2.5.0: remove days as not needed here
	$hours = $mins = array();
	for ( $i = 1; $i < 13; $i++ ) {
		$hours[$i] = number_format_i18n( $i );
	}
	for ( $i = 0; $i < 60; $i++ ) {
		if ( $i < 10 ) {
			$min = number_format_i18n( 0 ) . number_format_i18n( $i );
			// 2.5.0: make sure value has leading 0
			$i = '0' . $i;
		} else {
			$min = number_format_i18n( $i );
		}
		$mins[$i] = $min;
	}

	// --- show shifts scripts ---
	// $c = 0;
	$confirm_remove = __( 'Are you sure you want to remove this Override?', 'radio-station' );
	$confirm_clear = __( 'Are you sure you want to clear all overrides?', 'radio-station' );

	// --- clear all shifts function ---
	// 2.5.0: fix to remove weird symbol from new-overrides ID
	$js = "function radio_overrides_clear() {
		if (jQuery('#overrides-list').children().length) {
			var agree = confirm('" . esc_js( $confirm_clear ) . "');
			if (!agree) {return false;}
			jQuery('#overrides-list').children().remove();
			jQuery('<div id=\"new-overrides\"></div>').appendTo('#overrides-list');
		}
	}" . "\n";

	// --- save shifts via AJAX ---
	// 2.3.2: added form input cloning to saving show shifts
	$ajaxurl = admin_url( 'admin-ajax.php' );
	$js .= "function radio_overrides_save() {
		jQuery('#override-save-form, #override-save-frame').remove();
		form = '<form id=\"override-save-form\" method=\"post\" action=\"" . esc_url( $ajaxurl ) . "\" target=\"override-save-frame\">';
		form += '<input type=\"hidden\" name=\"action\" value=\"radio_station_override_save\"></form>';
		jQuery('#wpbody').append(form);
		if (!jQuery('#override-save-frame').length) {
			frame = '<iframe id=\"override-save-frame\" name=\"override-save-frame\" src=\"javascript:void(0);\" style=\"display:none;\"></iframe>';
			jQuery('#wpbody').append(frame);
		}
		/* copy override input fields and nonce */
		jQuery('#overrides-list input').each(function() {
			inputtype = jQuery(this).attr('type');
			if ((jQuery(this).attr('name') != '') && (inputtype != 'hidden')) {
				if ((inputtype != 'checkbox') || ((inputtype == 'checkbox') && (jQuery(this).prop('checked')))) {
					name = jQuery(this).attr('name'); value = jQuery(this).val();
					jQuery('<input type=\"hidden\" name=\"'+name+'\" value=\"'+value+'\">').appendTo('#override-save-form');
				}
			}
		});
		jQuery('#overrides-list select').each(function() {
			name = jQuery(this).attr('name'); value = jQuery(this).children('option:selected').val();
			jQuery('<input type=\"hidden\" name=\"'+name+'\" value=\"'+value+'\">').appendTo('#override-save-form');
		});
		jQuery('#show_override_nonce').clone().attr('id','').appendTo('#override-save-form');
		jQuery('#post_ID').clone().attr('id','').attr('name','override_id').appendTo('#override-save-form');
		jQuery('#override-saving-message').show();
		jQuery('#override-save-form').submit();
	}" . "\n";

	// --- check multiday selection ---
	// 2.3.3.9: added to show hide end date field
	$js .= "function radio_check_multiday(id) {
		if (jQuery('#override-multiday-'+id).prop('checked')) {
			jQuery('#override-'+id+'-end-date').show();
		} else {jQuery('#override-'+id+'-end-date').hide();}
	}" . "\n";

	// TODO: input change highlighting ?
	// #####

	// --- check disabled selection ---
	// TODO: ...
	$js .= "function radio_check_disabled() {}" . "\n";

	// --- check select change ---
	/* $js .= "function radio_check_select(el) {
		val = el.options[el.selectedIndex].value;
		if (val == '') {jQuery('#'+el.id).addClass('incomplete');}
		else {jQuery('#'+el.id).removeClass('incomplete');}
		origid = el.id.replace('shift-','');
		origval = jQuery('#'+origid).val();
		if (val == origval) {jQuery('#'+el.id).removeClass('changed');}
		else {jQuery('#'+el.id).addClass('changed');}
		uid = origid.substr(0,8);
		radio_check_shift(uid);
	}" . PHP_EOL; */

	// --- check checkbox change ---
	/* $js .= "function radio_check_checkbox(el) {
		val = el.checked ? 'on' : '';
		origid = el.id.replace('shift-','');
		origval = jQuery('#'+origid).val();
		if (val == origval) {jQuery('#'+el.id).removeClass('changed');}
		else {jQuery('#'+el.id).addClass('changed');}
		uid = origid.substr(0,8);
		radio_check_shift(uid);
	}" . PHP_EOL; */

	// --- check override change ---
	/* $js .= "function radio_check_override(id) {
		var overridechanged = false;
		jQuery('#overide-'+id).find('select,input').each(function() {
			if ( (jQuery(this).attr('id').indexOf('override-') == 0) && (jQuery(this).hasClass('changed')) ) {
				overridechanged = true;
			}
		});
		if (shiftchanged) {jQuery('#override-'+id).addClass('changed');}
		else {jQuery('#override-'+id).removeClass('changed');}
		radio_check_overrides();
	}" . PHP_EOL; */

	// --- store possible existing onbeforeunload function ---
	// (to help prevent conflicts with other plugins using this event)
	/* $js .= "var storedonbeforeunload = null; var onbeforeunloadset = false;" . PHP_EOL;
	$js .= "function radio_check_overrides() {
		if (jQuery('.show-shift.changed').length) {
			if (!onbeforeunloadset) {
				storedonbeforeunload = window.onbeforeunload;
				window.onbeforeunload = function() {return true;}
				onbeforeunloadset = true;
			}
		} else {
			if (onbeforeunloadset) {
				window.onbeforeunload = storedonbeforeunload;
				onbeforeunloadset = false;
			}
		}
	}" . PHP_EOL; */

	// --- add new override ---
	// 2.5.0: shorten value object
	// 2.5.6: use radio_station_get_time instead of date
	$todate = radio_station_get_time( 'Y-m-d', time() );
	$js .= "function radio_override_new() {
		values = {date:'" . esc_js( $todate ) . "', start_hour:'', start_min:'', start_meridian:'', end_hour:'', end_min:'', end_meridian:'', multiday:'', end_date:'', disabled:''};
		radio_override_add(values);
	}" . "\n";

	// --- remove override ---
	$js .= "function radio_override_remove(el) {
		agree = confirm('" . esc_js( $confirm_remove ) . "');
		if (!agree) {return false;}
		/* overrideid = el.id.replace('override-','').replace('-remove',''); */
		jQuery('#'+el.id).closest('.override-wrapper').remove();
	}" . "\n";

	// --- duplicate shift ---
	$js .= "function radio_override_duplicate(el) {
		overrideid = el.id.replace('override-','').replace('-duplicate','');
		console.log('Override ID: '+overrideid);
		values = {};
		values.date = jQuery('#override-'+overrideid+'-date').val();
		values.start_hour = jQuery('#override-'+overrideid+'-start-hour').val();
		values.start_min = jQuery('#override-'+overrideid+'-start-min').val();
		values.start_meridian = jQuery('#override-'+overrideid+'-start-meridian').val();
		values.end_hour = jQuery('#override-'+overrideid+'-end-hour').val();
		values.end_min = jQuery('#override-'+overrideid+'-end-min').val();
		values.end_meridian = jQuery('#override-'+overrideid+'-end-meridian').val();
		values.multiday = '';
		/* if (jQuery('#override-'+overrideid+'-multiday').prop('checked')) {values.multiday = 'yes';} */
		values.end_date = '';
		/* values.end_date = jQuery('#override-'+overrideid+'-end-date'); */
		values.disabled = 'yes';
		radio_override_add(values);
	}" . "\n";

	// --- add override function ---
	$js .= "function radio_override_add(values) {" . "\n";
		$js .= "var count = jQuery('#new-overrides').children().length + 1;" . "\n";
		$js .= "output = '<div id=\"override-wrapper-new' + count + '\" class=\"override-wrapper\">';" . "\n";
			$js .= "output += '<ul id=\"override-' + count + '\" class=\"override-list new-override\">';" . "\n";

				// --- start date ---
				$js .= "output += '<li class=\"override-item first-item\">';" . "\n";
					$js .= "output += '" . esc_js( __( 'Start Date', 'radio-station' ) ) . ": ';" . "\n";
					$js .= "output += '<input type=\"text\" id=\"override-new-' + count + '-date\" name=\"show_sched[new-' + count + '][date]\" id=\"override-new-' + count +'-date\" class=\"override-date\" value=\"' + values.date + '\">';" . "\n";
				$js .= "output += '</li>';" . "\n";

				// --- start hour ---
				$js .= "output += '<li class=\"override-item\">';" . "\n";
					$js .= "output += '" . esc_js( __( 'Start Time', 'radio-station' ) ) . ": ';" . "\n";
					$js .= "output += '<select name=\"show_sched[new-' + count + '][start_hour]\" id=\"override-new-' + count + '-start-hour\" style=\"min-width:35px;\">';" . "\n";
					// 2.5.0: use possibly translated hour label
					foreach ( $hours as $hour => $label ) {
						$js .= "output += '<option value=\"" . esc_js( $hour ) . "\"';" . "\n";
						$js .= "if (values.start_hour == '" . esc_js( $hour ) . "') {output += ' selected=\"selected\"';}" . "\n";
						$js .= "output += '>" . esc_js( $label ) . "</option>';" . "\n";
					}
					$js .= "output += '</select> ';" . "\n";

					// --- start minute ---
					$js .= "output += '<select name=\"show_sched[new-' + count + '][start_min]\" id=\"override-new-' + count + '-start-min\" style=\"min-width:35px;\">';" . "\n";
					$js .= "output += '<option value=\"00\">00</option><option value=\"15\">15</option><option value=\"30\">30</option><option value=\"45\">45</option>';" . "\n";
					// 2.5.0: use possibly translated minute label
					foreach ( $mins as $min => $label ) {
						$js .= "output += '<option value=\"" . esc_js( $min ) . "\"';" . "\n";
						$js .= "if (values.start_min == '" . esc_js( $min ) . "') {output += ' selected=\"selected\"';}" . "\n";
						$js .= "output += '>" . esc_js( $label ) . "</option>';" . "\n";
					}
					$js .= "output += '</select>';" . "\n";

					// --- start meridian ---
					// 2.5.0: fix to not use translated value for checking selected
					$js .= "output += '<select name=\"show_sched[new-' + count + '][start_meridian]\" id=\"override-new-' + count + '-start-meridian\" style=\"min-width:35px;\">';" . "\n";
						$js .= "output += '<option value=\"am\"';" . "\n";
						$js .= "if (values.start_meridian == 'am') {output += ' selected=\"selected\"';}" . "\n";
						$js .= "output += '>" . esc_js( $am ) . "</option>';" . "\n";
						$js .= "output += '<option value=\"pm\"';" . "\n";
						$js .= "if (values.start_meridian == 'pm') {output += ' selected=\"selected\"';}" . "\n";
						$js .= "output += '>" . esc_js( $pm ) . "</option>';" . "\n";
					$js .= "output += '</select> ';" . "\n";
				$js .= "output += '</li>';" . "\n";

				// - end time -
				$js .= "output += '<li class=\"override-item\">';" . "\n";
					$js .= "output += '" . esc_js( __( 'End Time', 'radio-station' ) ) . ": ';" . "\n";
					// --- end hour ---
					$js .= "output += '<select name=\"show_sched[new-' + count + '][end_hour]\" id=\"override-new-' + count + '-end-hour\" style=\"min-width:35px;\">';" . "\n";
					// 2.5.0: use possibly translated hour label
					foreach ( $hours as $hour => $label ) {
						$js .= "output += '<option value=\"" . esc_js( $hour ) . "\"';" . "\n";
						$js .= "if (values.end_hour == '" . esc_js( $hour ) . "') {output += ' selected=\"selected\"';}" . "\n";
						$js .= "output += '>" . esc_js( $label ) . "</option>';" . "\n";
					}
					$js .= "output += '</select> ';" . "\n";

					// --- end min ---
					$js .= "output += '<select name=\"show_sched[new-' + count + '][end_min]\" id=\"override-new-' + count + '-end-min\" style=\"min-width:35px;\">';" . "\n";
					$js .= "output += '<option value=\"00\">00</option><option value=\"15\">15</option><option value=\"30\">30</option><option value=\"45\">45</option>';" . "\n";
					// 2.5.0: use possibly translated minute label
					foreach ( $mins as $min => $label ) {
						$js .= "output += '<option value=\"" . esc_js( $min ) . "\"';" . "\n";
						$js .= "if (values.end_min == '" . esc_js( $min ) . "') {output += ' selected=\"selected\"';}" . "\n";
						$js .= "output += '>" . esc_js( $label ) . "</option>';" . "\n";
					}
					$js .= "output += '</select> ';" . "\n";

					// --- end meridian ---
					// 2.5.0: fix to not use translated value for checking selected
					$js .= "output += '<select name=\"show_sched[new-' + count + '][end_meridian]\" id=\"override-new-' + count + '-end-meridian\" style=\"min-width:35px;\">';" . "\n";
						$js .= "output += '<option value=\"am\"';" . "\n";
						$js .= "if (values.end_meridian == 'am') {output += ' selected=\"selected\"';}" . "\n";
						$js .= "output += '>" . esc_js( $am ) . "</option>';" . "\n";
						$js .= "output += '<option value=\"pm\"';" . "\n";
						$js .= "if (values.end_meridian == 'pm') {output += ' selected=\"selected\"';}" . "\n";
						$js .= "output += '>" . esc_js( $pm ) . "</option>';" . "\n";
					$js .= "output += '</select> ';" . "\n";
				$js .= "output += '</li>';" . "\n";

				// --- multiday switch ---
				/* $js .= "output += '<li class=\"override-item\" style=\"display:none;\">';" . "\n";
					$js .= "output += '<input type=\"checkbox\" value=\"yes\" name=\"show_sched[new-' + count + '][multiday]\" id=\"override-new-' + count + '-multiday\" onchange=\"radio_check_multiday('+count+');\"';" . "\n";
					$js .= "if (values.multiday == 'yes') {output += ' checked=\"checked\"';}" . "\n";
					$js .= "output += '> " . esc_js( __( 'Multiday', 'radio-station' ) ) . "';" . "\n";
				$js .= "output += '</li>';" . "\n"; */

				// --- multiday end date ---
				/* $js .= "output += '<li class=\"override-item\" style=\"display:none;\">';" . "\n";
					$js .= "output += '" . esc_js( __( 'End Date', 'radio-station' ) ) . ": ';" . "\n";
					$js .= "output += '<input type=\"text\" id=\"override-new-' + count + '-end-date\" name=\"show_sched[new-' + count + '][end_date]\">';" . "\n";
					$js .= "if (values.end_date != '') {output += ' value=\"' + values.end_date+ '\"';}" . "\n";
					$js .= "output += '>';" . "\n";
				$js .= "output += '</li>';" . "\n"; */

				// --- disable override ---
				$js .= "output += '<li class=\"override-item\">';" . "\n";
					$js .= "output += '<input type=\"checkbox\" id=\"override-new-' + count + '-disabled\" name=\"show_sched[new-' + count + '][disabled]\" value=\"';" . "\n";
					$js .= "if (values.disabled != '') {output += values.disabled;}" . "\n";
					$js .= "output += '\"> " . esc_js( __( 'Disabled', 'radio-station' ) ) . "';" . "\n";
				$js .= "output += '</li>';" . "\n";

				// --- duplicate override time ---
				$js .= "output += '<li class=\"override-item\">';" . "\n";
					$js .= "output += '<span id=\"override-new-' + count + '-duplicate\" class=\"override-duplicate dashicons dashicons-admin-page\" title=\"" . esc_js( __( 'Duplicate Override', 'radio-station' ) ) . "\" onclick=\"radio_override_duplicate(this);\"></span>';" . "\n";
				$js .= "output += '</li>';" . "\n";

				// --- remove override time ---
				$js .= "output += '<li class=\"override-item last-item\">';" . "\n";
					$js .= "output += '<span id=\"override-new-' + count + '-remove\" class=\"override-remove dashicons dashicons-no\" title=\"" . esc_js( __( 'Remove Override', 'radio-station' ) ) . "\" onclick=\"radio_override_remove(this);\"></span>';" . "\n";
				$js .= "output += '</li>';" . "\n";

			$js .= "output += '</ul>';" . "\n";
		$js .= "output += '</div>';" . "\n";

		// --- append new override list item ---
		$js .= "jQuery('#new-overrides').append(output);" . "\n";
		$js .= "jQuery('#override-new-' + count + '-date').datepicker({dateFormat : 'yy-mm-dd'});" . "\n";
		// $js .= "jQuery('#override-new-' + count + '-end-date').datepicker({dateFormat : 'yy-mm-dd'});" . "\n";
		$js .= "return false;" . "\n";

	$js .= "}" . "\n";

	$js = apply_filters( 'radio_station_override_edit_script', $js );
	return $js;
}

// ------------------------
// Update Schedule Override
// ------------------------
add_action( 'wp_ajax_radio_station_override_save', 'radio_station_override_save_data' );
add_action( 'save_post', 'radio_station_override_save_data', 11 );
function radio_station_override_save_data( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- check for AJAX override save ---
	// 2.3.2: added AJAX shift saving checks
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		// 2.3.3: added double check for AJAX action match
		// 2.3.3.9: match to whitelisted actions
		// 2.5.6: combine test conditions
		$actions = array( 'radio_station_override_save', 'radio_station_add_override_time' );
		if ( !isset( $_REQUEST['action'] ) || !in_array( sanitize_text_field( $_REQUEST['action'] ), $actions ) ) {
			return;
		}

		// --- make sure we have a post ID for AJAX save ---
		if ( !isset( $_POST['override_id'] ) || ( '' === sanitize_text_field( $_POST['override_id'] ) ) ) {
			return;
		}
		$post_id = absint( $_POST['override_id'] );
		$post = get_post( $post_id );

		// --- check for errors ---
		$error = false;
		if ( !isset( $_POST['show_override_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $_POST['show_override_nonce'] ), 'radio-station' ) ) {
			$error = __( 'Expired. Publish or Update instead.', 'radio-station' );
		} elseif ( !$post ) {
			$error = __( 'Failed. Invalid Override.', 'radio-station' );
		} elseif ( !current_user_can( 'edit_shows' ) ) {
			$error = __( 'Failed. Publish or Update instead.', 'radio-station' );
		}

		// --- send error to parent frame ---
		if ( $error ) {
			echo "<script>parent.document.getElementById('override-saving-message').style.display = 'none';" . "\n";
			echo "parent.document.getElementById('override-error-message').style.display = '';" . "\n";
			echo "parent.document.getElementById('override-error-message').innerHTML = '" . esc_js( $error ) . "';" . "\n";
			echo "form = parent.document.getElementById('override-save-form');" . "\n";
			echo "if (form) {form.parentNode.removeChild(form);}" . "\n";
			echo "</script>";
			exit;
		}
	}

	$meta_changed = $sched_changed = false;

	// --- verify nonce for show data ---
	// 2.3.3.9: added to save show override metabox data
	if ( isset( $_POST['show_data_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['show_data_nonce'] ), 'radio-station' ) ) {

		// --- save linked show ID ---
		// 2.3.3.9: save linked show ID
		// $linked_show_id = get_post_meta( $post_id, 'linked_show_id', true );
		$prev_linked = get_post_meta( $post_id, 'linked_show_id', true );
		$linked_id = absint( $_POST['linked_show_id'] );
		$linked_show = get_post( $linked_id );
		if ( !$linked_show ) {
			delete_post_meta( $post_id, 'linked_show_id' );
		} else {
			update_post_meta( $post_id, 'linked_show_id', $linked_id );
		}
		if ( $linked_id != $prev_linked ) {
			$meta_changed = true;
		}

		// --- sync genres switch ---
		if ( isset( $_POST['sync_genres'] ) && ( 'yes' == sanitize_text_field( $_POST['sync_genres'] ) ) ) {

			update_post_meta( $post_id, 'sync_genres', 'yes' );

			if ( $linked_show ) {

				// --- sync genre terms ---
				$prev_term_ids = $term_ids = array();
				$prev_terms = wp_get_object_terms( $post_id, RADIO_STATION_GENRES_SLUG );
				if ( count( $prev_terms ) > 0 ) {
					foreach( $prev_terms as $prev_term ) {
						$prev_term_ids[] = $prev_term->term_id;
					}
				}
				$genre_terms = wp_get_object_terms( $linked_id, RADIO_STATION_GENRES_SLUG );
				if ( count( $genre_terms ) > 0 ) {
					foreach ( $genre_terms as $genre_term ) {
						$term_ids[] = $genre_term->term_id;
					}
				}
				wp_set_post_terms( $post_id, $term_ids, RADIO_STATION_GENRES_SLUG );
				if ( array_diff( $prev_term_ids, $term_ids ) != array_diff( $term_ids, $prev_term_ids ) ) {
					$meta_changed = true;
				}
			}
		} else {
			delete_post_meta( $post_id, 'sync_genres' );
		}

		// --- sync languages switch ---
		if ( isset( $_POST['sync_languages'] ) && ( 'yes' == sanitize_text_field( $_POST['sync_languages'] ) ) ) {

			if ( $linked_show ) {

				update_post_meta( $post_id, 'sync_languages', 'yes' );

				// --- sync language terms ---
				$prev_term_ids = $term_ids = array();
				$prev_terms = wp_get_object_terms( $post_id, RADIO_STATION_LANGUAGES_SLUG );
				if ( count( $prev_terms ) > 0 ) {
					foreach( $prev_terms as $prev_term ) {
						$prev_term_ids[] = $prev_term->term_id;
					}
				}
				$language_terms = wp_get_object_terms( $linked_id, RADIO_STATION_LANGUAGES_SLUG );
				if ( count( $language_terms ) > 0 ) {
					foreach ( $language_terms as $language_term ) {
						$term_ids[] = $language_term->term_id;
					}
				}
				wp_set_post_terms( $post_id, $term_ids, RADIO_STATION_LANGUAGES_SLUG );
				if ( array_diff( $prev_term_ids, $term_ids ) != array_diff( $term_ids, $prev_term_ids ) ) {
					$meta_changed = true;
				}
			}
		} else {
			delete_post_meta( $post_id, 'sync_languages' );
		}

		// --- update linked show fields ---
		$linked_show_fields = array();
		$show_fields = array( 'title', 'content', 'excerpt', 'avatar', 'image', 'user_list', 'producer_list', 'link', 'email', 'phone', 'file', 'download', 'patreon' );
		$non_input_fields = array( 'show_title', 'show_content', 'show_excerpt' );
		foreach ( $show_fields as $field ) {
			$show_field = 'show_' . $field;
			$linked = false;
			if ( isset( $_POST[$show_field . '_link'] ) ) {
				// 2.5.0: use sanitize_text_field on posted value
				$linked = sanitize_text_field( $_POST[$show_field . '_link'] );
			}
			$linked_show_fields[$show_field] = ( 'yes' == $linked ) ? true : false;
			if ( !in_array( $show_field, $non_input_fields ) ) {
				if ( isset( $_POST[$show_field] ) ) {
					$value = radio_station_sanitize_input( 'show', $field );
					update_post_meta( $post_id, $show_field, $value );
				} else {
					delete_post_meta( $post_id, $show_field );
				}
			}
		}
		update_post_meta( $post_id, 'linked_show_fields', $linked_show_fields );

	}

	// --- verify this came from the our screen and with proper authorization ---
	// 2.3.3.9: reverse condition to allow for combined data/schedule processing
	if ( isset( $_POST['show_override_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['show_override_nonce'] ), 'radio-station' ) ) {

		// --- get the show override data ---
		$current_scheds = get_post_meta( $post_id, 'show_override_sched', true );
		if ( isset( $_POST['new_override'] ) ) {
			// 2.5.0: use sanitize_text_field on posted value
			$new_shift = sanitize_text_field( $_POST['new_override'] );
			$new_shift['id'] = radio_station_unique_shift_id();
			$show_sched = $current_scheds;
			$show_sched[] = $new_shift;
			// TODo: test array_map with sanitize_text_field here ?
			$_POST['show_sched'] = $show_sched;
		} else {
			// TODo: test array_map with sanitize_text_field here ?
			$show_sched = $_POST['show_sched'];
		}
		$new_scheds = array();
		if ( is_array( $show_sched ) ) {

			// 2.3.3.9: loop to save possible multiple override dates/times ---
			// print_r( $show_sched );
			foreach ( $show_sched as $sched ) {

				// --- get/set current schedule for merging ---
				// 2.2.2: added to set default keys
				// 2.3.3.9: just set new schedule array here
				$new_sched = array(
					'id'             => '',
					'date'           => '',
					'start_hour'     => '',
					'start_min'      => '',
					'start_meridian' => '',
					'end_hour'       => '',
					'end_min'        => '',
					'end_meridian'   => '',
					// 'multiday'    => '',
					// 'end_date'    => '',
					'disabled'       => '',
				);

				// --- sanitize values before saving ---
				// 2.2.2: loop and validate schedule override values
				$override_dates = array();
				foreach ( $sched as $key => $value ) {

					$isvalid = false;

					// --- validate according to key ---
					if ( ( 'date' === $key ) || ( 'end_date' == $key ) ) {
						// check posted date format (yyyy-mm-dd) with checkdate (month, date, year)
						$parts = explode( '-', $value );
						// 2.3.3.9: added extra check for date parts
						if ( 3 == count( $parts ) ) {
							if ( checkdate( (int) $parts[1], (int) $parts[2], (int) $parts[0] ) ) {
								$isvalid = true;
								// 2.2.7: sync separate meta key for override date
								// (could be used to improve column sorting efficiency)
								// 2.3.3.9: store all override dates for later
								$override_dates[] = $value;
							}
						}
					} elseif ( ( 'start_hour' === $key ) || ( 'end_hour' === $key ) ) {
						if ( empty( $value ) ) {
							$isvalid = true;
						} elseif ( ( absint( $value ) > 0 ) && ( absint( $value ) < 13 ) ) {
							$isvalid = true;
						}
					} elseif ( ( 'start_min' === $key ) || ( 'end_min' === $key ) ) {
						// 2.2.3: fix to validate 00 minute value
						// 2.5.0: refix to validate 00 minute value
						if ( empty( $value ) || ( '00' == $value ) ) {
							$isvalid = true;
						} elseif ( absint( $value ) > -1 && absint( $value ) < 61 ) {
							$isvalid = true;
						}
					} elseif ( ( 'start_meridian' === $key ) || ( 'end_meridian' === $key ) ) {
						$valid = array( '', 'am', 'pm' );
						// 2.2.8: remove strict in_array checking
						if ( in_array( $value, $valid ) ) {
							$isvalid = true;
						}
					} elseif ( ( 'disabled' == $key ) || ( 'multiday' == $key ) ) {
						// note: not set again if already empty
						if ( 'yes' == $value ) {
							$isvalid = true;
						}
					} elseif ( 'id' == $key ) {
						// 2.3.3.9: validate unique shift ID
						if ( preg_match( '/^[a-zA-Z0-9_]+$/', $value ) ) {
							$isvalid = true;
						}
					}

					// --- if valid add to new schedule setting ---
					if ( $isvalid ) {
						$new_sched[$key] = $value;
					}
				}

				// 2.3.3.9: add unique override shift ID
				// 2.5.0: fix to check if empty instead of isset
				if ( '' == $new_sched['id'] ) {
					$new_sched['id'] = radio_station_unique_shift_id();
				}

				// --- add to new schedule array ---
				// 2.3.3.9: to allow for multiple overrides
				$new_scheds[] = $new_sched;
			}

			// --- clear and resave override dates ---
			// 2.3.3.9: add individual records for each date
			if ( count( $override_dates ) > 0 ) {
				delete_post_meta( $post_id, 'show_override_date' );
				foreach ( $override_dates as $override_date ) {
					add_post_meta( $post_id, 'show_override_date', $override_date, false );
				}
			}

			// --- check if override schedule has changed ---
			$sched_changed = true;
			// 2.3.3.9: added check that current schedule is set
			if ( $current_scheds && is_array( $current_scheds ) ) {
				if ( array_key_exists( 'date', $current_scheds ) ) {
					$current_scheds['id'] = radio_station_unique_shift_id();
					$current_scheds = array( $current_scheds );
				}
				$prev_scheds = $current_scheds;
				if ( count( $current_scheds ) == count( $new_scheds ) ) {
					foreach ( $current_scheds as $i => $current_sched ) {
						foreach ( $new_scheds as $j => $new_sched ) {
							if ( $new_sched == $current_sched ) {
								unset( $current_scheds[$i] );
							}
						}
					}
				}
				if ( 0 === count( $current_scheds ) ) {
					$sched_changed = false;
				}
			}

			// --- sort schedule overrides by date/time ---
			if ( count( $new_scheds ) > 0 ) {
				$date_scheds = $sorted_scheds = array();
				foreach ( $new_scheds as $new_sched ) {
					$date_scheds[$new_sched['date']][] = $new_sched;
				}
				// --- sort date_scheds by date keys ---
				ksort( $date_scheds );
				foreach ( $date_scheds as $date => $scheds ) {
					$sorted_scheds = array();
					foreach( $scheds as $i => $sched ) {
						// --- sort by start time ---
						$start = $sched['start_hour'] . ':' . $sched['start_min'] . $sched['start_meridian'];
						$time = radio_station_convert_hour( $start, 24 );
						$timestamp = radio_station_to_time( $date . ' ' . $time );

						// --- deduplicate based on timestamp ---
						if ( isset( $sorted_sheds[$timestamp] ) ) {
							while( isset( $sorted_scheds[$timestamp] ) ) {
								$timestamp++;
							}
							$sched['disabled'] = 'yes';
						}
						$sorted_scheds[$timestamp] = $sched;
					}
					ksort( $sorted_scheds );
					$date_scheds[$date] = $sorted_scheds;
				}
				$new_scheds = array();
				foreach ( $date_scheds as $date => $scheds ) {
					foreach ( $scheds as $sched ) {
						$new_scheds[] = $sched;
					}
				}
			}

			// --- save schedule setting if changed ---
			// 2.3.0: check if changed before saving
			if ( $sched_changed ) {
				update_post_meta( $post_id, 'show_override_sched', $new_scheds );

				// 2.3.3.9: clear out old unique shift IDs from prev_scheds
				// 2.5.6: added isset check for prev_scheds
				$new_ids = array();
				if ( isset( $prev_scheds ) && is_array( $prev_scheds ) && ( count( $prev_scheds ) > 0 ) ) {
					if ( is_array( $new_scheds ) && ( count( $new_scheds ) > 0 ) ) {
						foreach ( $new_scheds as $i => $new_sched ) {
							$new_ids[] = $new_sched['id'];
						}
					}
					$prev_ids = array();
					foreach ( $prev_scheds as $i => $shift ) {
						if ( isset( $shift['id'] ) && !in_array( $shift['id'], $new_ids ) ) {
							$prev_ids[] = $shift['id'];
						}
					}
					if ( count( $prev_ids ) > 0 ) {
						$unique_ids = get_option( 'radio_station_shifts_ids' );
						foreach ( $unique_ids as $i => $unique_id ) {
							if ( in_array( $unique_id, $prev_ids ) ) {
								unset( $unique_ids[$i] );
							}
						}
						update_option( 'radio_station_shifts_ids', $unique_ids );
					}
				}
			}

		}
	}

	// --- clear cached schedule if changed ---
	// 2.3.3.9: clear cache on data/schedule change
	if ( $meta_changed || $sched_changed ) {
		// 2.4.0.3: added second argument to cache clear
		radio_station_clear_cached_data( $post_id, RADIO_STATION_OVERRIDE_SLUG );
	}

	// --- update overrides table when AJAX saving ---
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		// 2.3.3.9: remove duplicate action check
		$action = sanitize_text_field( $_REQUEST['action'] );

		// --- (hidden) debug information ---
		echo "Previous Overrides: " . esc_html( print_r( $current_scheds, true ) ) . "\n";
		echo "New Overrides: " . esc_html( print_r( $new_scheds, true ) ) . "\n";

		// --- display shifts saved message ---
		// 2.3.3.9: fade out overrides saved message
		$show_override_nonce = wp_create_nonce( 'radio-station' );
		echo "<script>";
			echo "parent.document.getElementById('overrides-saving-message').style.display = 'none';" . "\n";
			echo "parent.document.getElementById('overrides-saved-message').style.display = '';" . "\n";
			echo "if (typeof parent.jQuery == 'function') {parent.jQuery('#overrides-saved-message').fadeOut(3000);}" . "\n";
			echo "else {setTimeout(function() {parent.document.getElementById('overrides-saved-message').style.display = 'none';}, 3000);}" . "\n";
			echo "form = parent.document.getElementById('override-save-form');" . "\n";
			echo "if (form) {form.parentNode.removeChild(form);}" . "\n";
			echo "parent.document.getElementById('show_override_nonce').value = '" . esc_js( $show_override_nonce ) . "';" . "\n";
		echo "</script>" . "\n";

		// 2.3.3.9: added check if override schedule changed
		if ( $sched_changed ) {

			// --- output new show shifts list ---
			echo '<div id="overrides-list">' . "\n";

				$table = radio_station_overrides_table( $post_id );
				// if ( $table['conflicts'] ) {
				// 	radio_station_overrides_conflict_message();
				// }
				// 2.5.0: use wp_kses on table output
				$allowed = radio_station_allowed_html( 'content', 'settings' );
				echo wp_kses( $table['html'], $allowed );
				// echo '<div id="updated-div"></div>';

			echo '</div>' . "\n";

			// --- refresh show shifts list ---
			$js = "if (!parent.document.getElementById('overrides-list')) {console.log('Error. Could not find override list!');}" . "\n";
			$js .= "overridelist = parent.document.getElementById('overrides-list');" . "\n";
			$js .= "overridelist.innerHTML = document.getElementById('overrides-list').innerHTML;" . "\n";
			$js .= "overridelist.style.display = '';" . "\n";

			// --- reload the current schedule view ---
			// 2.4.0.3: added missing check for window parent function
			// 2.5.0: added extra instance argument
			$js .= "if (window.parent && (typeof parent.radio_load_schedule == 'function')) {" . "\n";
				$js .= "parent.radio_load_schedule(false,false,false,true);" . "\n";
			$js .= "}" . PHP_EOL;

			// $js .= "if (parent.window.onbeforeunloadset) {
			//	parent.window.onbeforeunload = parent.storedonbeforeunload;
			//	parent.window.onbeforeunloadset = false;
			// }";

			// --- alert on conflicts ---
			// if ( $table['conflicts'] ) {
			// 	$warning = __( 'Warning! Override conflicts detected.', 'radio-station' );
			//	$js .= "alert('" . esc_js( $warning ) . "');";
			// }

			// --- re-initialize datepicker fields in parent window ---
			$js .= "parent.jQuery('.override-date').each(function() {" . "\n";
				$js .= "parent.jQuery(this).datepicker({dateFormat : 'yy-mm-dd'});" . "\n";
			$js .= "});" . "\n";

			// --- output the scripts ---
			// 2.5.0: use radio_station_add_inline_script
			// echo '<script>' . $js . '</script>';		
			radio_station_add_inline_script( 'radio-station-admin', $js );

			// 2.3.3.9: trigger action for save or add override time
			if ( 'radio_station_override_save' == $action ) {
				// 2.5.6: fix second argument to post_id not shift_id
				do_action( 'radio_station_override_save_time', $post_id );
			} elseif ( 'radio_station_add_override_time' == $action ) {
				do_action( 'radio_station_override_add_time' );
			}
		}

		// --- return early when adding single override ---
		// 2.3.3.9: added for single override action
		if ( 'radio_station_add_override_time' == $action ) {
			return;
		}

		exit;
	}
}

// ----------------------------------
// Add Schedule Override List Columns
// ----------------------------------
// 2.2.7: added data columns to override list display
add_filter( 'manage_edit-' . RADIO_STATION_OVERRIDE_SLUG . '_columns', 'radio_station_override_columns', 6 );
function radio_station_override_columns( $columns ) {

	// --- unset images columns ---
	if ( isset( $columns['thumbnail'] ) ) {
		unset( $columns['thumbnail'] );
	}
	if ( isset( $columns['post_thumb'] ) ) {
		unset( $columns['post_thumb'] );
	}

	// --- modify existing columns ---
	$date = $columns['date'];
	unset( $columns['date'] );
	$genres = $columns['taxonomy-' . RADIO_STATION_GENRES_SLUG];
	unset( $columns['taxonomy-' . RADIO_STATION_GENRES_SLUG] );
	$languages = $columns['taxonomy-' . RADIO_STATION_LANGUAGES_SLUG];
	unset( $columns['taxonomy-' . RADIO_STATION_LANGUAGES_SLUG] );

	// --- add override columns ---
	// 2.3.3.9: list date and times in single column
	$columns['override_times'] = esc_attr( __( 'Override Time(s)', 'radio-station' ) );
	$columns['shows_affected'] = esc_attr( __( 'Affected Show(s)', 'radio-station' ) );
	// 2.3.0: added description indicator column
	// 2.3.3.9: removed description indicator column
	// $columns['description'] = esc_attr( __( 'About?', 'radio-station' ) );
	// 2.3.3.9: add linked show name column
	$columns['linked_show'] = esc_attr( __( 'Linked Show', 'radio-station' ) );
	// 2.3.3.9: move genres and languages columns
	$columns['taxonomy-' . RADIO_STATION_GENRES_SLUG] = $genres;
	$columns['taxonomy-' . RADIO_STATION_LANGUAGES_SLUG] = $languages;
	// 2.3.2: added missing translation text domain
	$columns['override_image'] = esc_attr( __( 'Image', 'radio-station' ) );
	// 2.3.3.9: do not re-add the published date column (to reduce confusion)
	// $columns['date'] = $date;

	return $columns;
}

// -----------------------------
// Schedule Override Column Data
// -----------------------------
// 2.2.7: added data columns for override list display
add_action( 'manage_' . RADIO_STATION_OVERRIDE_SLUG . '_posts_custom_column', 'radio_station_override_column_data', 5, 2 );
function radio_station_override_column_data( $column, $post_id ) {

	global $radio_station_show_shifts;

	// 2.3.3.9: list override date and times in single column
	$overrides = get_post_meta( $post_id, 'show_override_sched', true );
	if ( $overrides && is_array( $overrides ) && array_key_exists( 'date', $overrides ) ) {
		$overrides = array( $overrides );
	}

	if ( 'override_times' == $column ) {

		// 2.3.3.9: loop possible multiple overrides
		foreach ( $overrides as $i => $override ) {

			if ( count( $overrides ) > 1 ) {
				echo '<b>' . ( $i + 1 ) . '</b>: ' . "\n";
			}

			// 2.3.3.9: maybe display disabled for this override time
			if ( isset( $override['disabled'] ) && ( 'yes' == $override['disabled'] ) ) {
				echo '<i>[Disabled]</i><br>' . "\n";
			}

			// 2.3.2: no need to apply timezone conversions here
			// 2.5.6: use radio_station_get_time instead of date
			// 2.5.14: fix to convert full override date and time not just date
			$start = $override['start_hour'] . ':' . $override['start_min'] . ' ' . $override['start_meridian'];
			$start = radio_station_convert_shift_time( $start );
			$timestamp = radio_station_to_time( $override['date'] . ' ' . $start );
			$date = radio_station_get_time( 'j', $timestamp );
			$weekday = radio_station_get_time( 'l', $timestamp );
			$weekday = radio_station_translate_weekday( $weekday );
			$month = radio_station_get_time( 'F', $timestamp );
			$month = radio_station_translate_month( $month, true );
			$year = radio_station_get_time( 'Y', $timestamp );
			echo esc_html( $weekday ) . ' ' . esc_html( $date ) . ' ' . esc_html( $month ) . ' ' . esc_html( $year ) . "\n";
			echo '<br>' . "\n";

			// 2.3.3.9: merge override times into this columns
			// 2.3.3.9: display according to selected time format
			$time_format = radio_station_get_setting( 'clock_time_format' );
			if ( 12 == $time_format ) {
				echo esc_html( $override['start_hour'] ) . ':' . esc_html( $override['start_min'] ) . esc_html( $override['start_meridian'] );
				echo ' - ' . esc_html( $override['end_hour'] ) . ':' . esc_html( $override['end_min'] ) . esc_html( $override['end_meridian'] ) . "\n";
			} elseif ( 24 == $time_format ) {
				// $start_hour = radio_station_convert_hour( $override['start_hour'] . ' ' . $override['start_meridian'] );
				// $end_hour = radio_station_convert_hour( $override['end_hour'] . ' ' . $override['end_meridian'] );
				echo esc_html( $override['start_hour'] ) . ':' . esc_html( $override['start_min'] );
				echo ' - ' . esc_html( $override['end_hour'] ) . ':' . esc_html( $override['end_min'] ) . "\n";
			}
			echo '<br>';
		}

	} elseif ( 'shows_affected' == $column ) {

		// --- maybe get all show shifts ---
		if ( isset( $radio_station_show_shifts ) ) {
			$show_shifts = $radio_station_show_shifts;
		} else {
			global $wpdb;
			$query = "SELECT posts.post_title, meta.post_id, meta.meta_value FROM " . $wpdb->prefix . "postmeta AS meta
				JOIN " . $wpdb->prefix . "posts as posts ON posts.ID = meta.post_id
				WHERE meta.meta_key = 'show_sched' AND posts.post_status = 'publish'";
			// 2.3.0: get results as an array
			$show_shifts = $wpdb->get_results( $query, ARRAY_A );
			$radio_station_show_shifts = $show_shifts;
		}
		if ( !$show_shifts || ( count( $show_shifts ) == 0 ) ) {
			return;
		}

		// 2.3.3.9: loop possible multiple overrides
		$affected_shows = array();
		foreach ( $overrides as $i => $override ) {

			// --- get the override weekday ---
			// 2.3.2: remove date time and get day from date directly
			// 2.5.6: use radio_station-get_time instead of date
			$weekday = radio_station_get_time( 'l', strtotime( $override['date'] ) );

			// --- get start and end override times ---
			// 2.3.2: fix to convert to 24 hour format first
			$start = $override['start_hour'] . ':' . $override['start_min'] . ' ' . $override['start_meridian'];
			$end = $override['end_hour'] . ':' . $override['end_min'] . ' ' . $override['end_meridian'];
			$start = radio_station_convert_shift_time( $start );
			$end = radio_station_convert_shift_time( $end );
			$override_start = radio_station_to_time( $override['date'] . ' ' . $start );
			$override_end = radio_station_to_time( $override['date'] . ' ' . $end );
			// (if the end time is less than start time, adjust end to next day)
			if ( $override_end <= $override_start ) {
				$override_end = $override_end + ( 24 * 60 * 60 );
			}
			// 2.3.3.9: clear last shift for looping
			if ( isset( $last_shift ) ) {
				unset( $last_shift );
			}

			// --- loop show shifts ---
			foreach ( $show_shifts as $show_shift ) {
				$shifts = maybe_unserialize( $show_shift['meta_value'] );
				if ( !is_array( $shifts ) ) {
					$shifts = array();
				}

				foreach ( $shifts as $shift ) {
					if ( isset( $shift['day'] ) && ( $shift['day'] == $weekday ) ) {

						// --- get start and end shift times ---
						// 2.3.0: validate shift time to check if complete
						// 2.3.2: replace strtotime with to_time for timezones
						// 2.3.2: fix to convert to 24 hour format first
						// 2.5.6: fix to return variable for validate shift
						$shift = radio_station_validate_shift( $shift );
						$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
						$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
						$start = radio_station_convert_shift_time( $start );
						$end = radio_station_convert_shift_time( $end );
						$shift_start = radio_station_to_time( $override['date'] . ' ' . $start );
						$shift_end = radio_station_to_time( $override['date'] . ' ' . $end );
						if ( ( $shift_start == $shift_end ) || ( $shift_start > $shift_end ) ) {
							$shift_end = $shift_end + ( 24 * 60 * 60 );
						}

						if ( RADIO_STATION_DEBUG ) {
							echo esc_html( $weekday ) . ': ' . esc_html( $start ) . ' to ' . esc_html( $end ) . '<br> ' . "\n";
							echo esc_html( $override['date'] ) . ': ' . esc_html( $shift_start ) . ' to ' . esc_html( $shift_end ) . '<br>' . "\n";
							echo esc_html( $override['date'] ) . ': ' . esc_html( $override_start ) . ' to ' . esc_html( $override_end ) . '<br>' . "\n";
							echo '<br>' . PHP_EOL;
						}

						// --- compare override time overlaps to get affected shows ---
						// 2.3.2: fix to override overlap checking logic
						if ( ( ( $override_start < $shift_start ) && ( $override_end > $shift_start ) )
								|| ( ( $override_start < $shift_start ) && ( $override_end > $shift_end ) )
								|| ( $override_start == $shift_start )
								|| ( ( $override_start > $shift_start ) && ( $override_end < $shift_end ) )
								|| ( ( $override_start > $shift_start ) && ( $override_start < $shift_end ) ) ) {

							// 2.3.0: adjust cell display to two line (to allow for long show titles)
							// 2.3.2: deduplicate show check (if same show as last show displayed)
							// 2.3.3.9: buffer affected show shift output
							$affected = '';
							if ( !isset( $last_show ) || ( $last_show != $show_shift['post_id'] ) ) {
								$active = get_post_meta( $show_shift['post_id'], 'show_active', true );
								if ( 'on' != $active ) {
									$affected .= '[<i>' . esc_html( __( 'Inactive', 'radio-station' ) ) . '</i>] ';
								}
								$affected .= '<b>' . $show_shift['post_title'] . '</b><br>' . "\n";
							}

							if ( isset( $shift['disabled'] ) && $shift['disabled'] ) {
								$affected .= '[<i>' . esc_html( __( 'Disabled', 'radio-station' ) ) . '</i>] ';
							}
							$affected .= radio_station_translate_weekday( $shift['day'] ) . ' ';
							// 2.3.3.9: display according to time format setting
							$time_format = radio_station_get_setting( 'clock_time_format' );
							if ( 12 == $time_format ) {
								$affected .= esc_html( $shift['start_hour'] ) . ':' . esc_html( $shift['start_min'] ) . esc_html( $shift['start_meridian'] );
								$affected .= ' - ' . esc_html( $shift['end_hour'] ) . ':' . esc_html( $shift['end_min'] ) . esc_html( $shift['end_meridian'] );
							} elseif ( 24 == $time_format ) {
								$start_hour = radio_station_convert_hour( $shift['start_hour'] );
								$end_hour = radio_station_convert_hour( $shift['end_hour'] );
								$affected .= esc_html( $start_hour ) . ':' . esc_html( $shift['start_min'] );
								$affected .= ' - ' . esc_html( $end_hour ) . ':' . esc_html( $shift['end_min'] );
							}
							$affected .= '<br>';

							// 2.3.3.9: store affected shows by override index
							$affected_shows[$i] = $affected;

							// 2.3.2: store last show displayed
							$last_show = $show_shift['post_id'];
						}
					}
				}
			}
		}
		// 2.3.3.9: output affected shows with numbered correlations
		foreach ( $overrides as $i => $override ) {
			if ( isset( $affected_shows[$i] ) ) {
				if ( $i == 0 ) {
					echo '<div>' . "\n";
				} else {
					echo '<div style="margin-top:7px;">' . "\n";
				}
				if ( count( $overrides ) > 1 ) {
					echo '<b>' . ( $i + 1 ). '</b>: ' . "\n";
				}
				echo $affected_shows[$i];
				echo '</div>' . "\n";
			}
		}

	} elseif ( 'description' == $column ) {

		// 2.3.0: added override description indicator
		global $wpdb;
		$query = "SELECT post_content FROM " . $wpdb->prefix . "posts WHERE ID = %d";
		$query = $wpdb->prepare( $query, $post_id );
		$content = $wpdb->get_var( $query );
		if ( !$content || ( trim( $content ) == '' ) ) {
			echo '<b>' . esc_html( __( 'No', 'radio-station' ) ) . '</b>';
		} else {
			echo esc_html( __( 'Yes', 'radio-station' ) );
		}

	} elseif ( 'linked_show' == $column ) {

		// --- get linked Show ---
		$linked_id = get_post_meta( $post_id, 'linked_show_id', true );
		if ( !$linked_id || ( '' == $linked_id ) ) {
			echo '<i>' . esc_html( __( 'None', 'radio-station' ) ) . '</i>';
		} else {
			// --- get linked show title ---
			// TODO: maybe add edit Show link ?
			global $wpdb;
			$query = "SELECT post_title FROM " . $wpdb->prefix . "posts WHERE ID = %d";
			$query = $wpdb->prepare( $query, $linked_id );
			$post_title = $wpdb->get_var( $query );
			echo esc_html( $post_title );
		}

	} elseif ( 'override_image' == $column ) {

		// --- override/show avatar ---
		// 2.3.3.9: apply filters to check for linked show override
		$thumbnail_url = radio_station_get_show_avatar_url( $post_id );
		$thumbnail_url = apply_filters( 'radio_station_show_avatar', $thumbnail_url, $post_id );
		if ( $thumbnail_url ) {
			echo '<div class="override_image">';
				echo '<img src="' . esc_url( $thumbnail_url ) . '" alt="' . esc_attr( __( 'Override Logo', 'radio-station' ) ) . '">' . "\n";
			echo '</div>' . "\n";
		}
	}
}

// -----------------------------
// Sortable Override Date Column
// -----------------------------
// 2.2.7: added to allow override date column sorting
add_filter( 'manage_edit-override_sortable_columns', 'radio_station_override_sortable_columns' );
function radio_station_override_sortable_columns( $columns ) {
	$columns['override_date'] = 'show_override_date';
	return $columns;
}

// -------------------------------
// Schedule Override Column Styles
// -------------------------------
// 2.5.0: renamed from radio_station_override_column_styles for consistency
add_action( 'admin_footer', 'radio_station_override_admin_list_styles' );
function radio_station_override_admin_list_styles() {
	$currentscreen = get_current_screen();
	if ( 'edit-' . RADIO_STATION_OVERRIDE_SLUG !== $currentscreen->id ) {
		return;
	}
	
	$css = "#title {min-width: 100px;}
	#override_times {width: 150px;}
	#shows_affected {width: 250px;}
	#description {width: 40px; font-size: 12px;}
	#taxonomy-" . RADIO_STATION_GENRES_SLUG . ", #taxonomy-" . RADIO_STATION_LANGUAGES_SLUG . " {width: 75px;}
	#override_image, .override_image {width: 75px;}
	.override_image img {width: 100%; height: auto;}";

	// 2.3.2: set override image column width to override image width
	// 2.3.3.9: set override times column width
	// 2.5.0: use wp_kses_post on style output
	// 2.5.6: use radio_station_add_inline_style
	$css = apply_filters( 'radio_station_override_list_styles', $css );
	// echo '<style>' . wp_kses_post( $css ) . '</style>';
	radio_station_add_inline_style( 'rs-admin', $css );
}

// ----------------------------------
// Add Schedule Override Month Filter
// ----------------------------------
// 2.2.7: added month selection filtering
// add_action( 'restrict_manage_posts', 'radio_station_override_date_filter', 10, 2 );
function radio_station_override_date_filter( $post_type, $which ) {

	global $wp_locale;
	if ( RADIO_STATION_OVERRIDE_SLUG !== $post_type ) {
		return;
	}

	// --- get all show override months / years ---
	global $wpdb;
	// 2.5.9: use wpdb prepare on query
	$overridequery = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_type = %s";
	$results = $wpdb->get_results( $wpdb->prepare( $overridequery, RADIO_STATION_OVERRIDE_SLUG ), ARRAY_A );
	$months = array();
	if ( $results && is_array( $results ) && ( count( $results ) > 0 ) ) {
		foreach ( $results as $result ) {
			$post_id = $result['ID'];
			// 2.3.3.9: allow for multiple override dates
			$overrides = get_post_meta( $post_id, 'show_override_date', false );
			foreach ( $overrides as $i => $override ) {
				$datetime = radio_station_to_time( $override );
				$month = radio_station_get_time( 'm', $datetime );
				$year = radio_station_get_time( 'Y', $datetime );
				$months[$year . '-' . $month] = array( 'year' => $year, 'month' => $month );
			}
		}
	} else {
		return;
	}

	// --- maybe get specified month ---
	$m = isset( $_GET['month'] ) ? absint( $_GET['month'] ) : 0;
	$m = ( ( $m < 1 ) || ( $m > 12 ) ) ? 0 : $m;

	// --- month override selector ---
	echo '<label for="filter-by-override-date" class="screen-reader-text">' . esc_html( __( 'Filter by override date', 'radio-station' ) ) . '</label>' . "\n";
	echo '<select name="month" id="filter-by-override-date">' . "\n";
		echo '<option value="0" ' . selected( $m, 0, false ) . '>' . esc_html( __( 'All Override Months', 'radio-station' ) ) . '</option>' . "\n";
		if ( count( $months ) > 0 ) {
			foreach ( $months as $key => $data ) {
				$label = $wp_locale->get_month( $data['month'] ) . ' ' . $data['year'];
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $m, $key, false ) . '>' . esc_html( $label ) . '</option>' . "\n";
			}
		}
	echo '</select>' . "\n";

}

// -------------------------------
// Add Schedule Past Future Filter
// -------------------------------
// 2.3.3: added past future filter prototype code
add_action( 'restrict_manage_posts', 'radio_station_override_past_future_filter', 10, 2 );
function radio_station_override_past_future_filter( $post_type, $which ) {

	if ( RADIO_STATION_OVERRIDE_SLUG !== $post_type ) {
		return;
	}

	// --- set past future selection / default ---
	$pastfuture = isset( $_GET['pastfuture'] ) ? sanitize_text_field( $_GET['pastfuture'] ) : '';
	$pastfuture = apply_filters( 'radio_station_overrides_past_future_default', $pastfuture );

	// --- past / future override selector ---
	// 2.3.3.5: added option for today filtering
	echo '<label for="filter-by-past-future" class="screen-reader-text">' . esc_html( __( 'Past and Future', 'radio-station' ) ) . '</label>' . "\n";
	echo '<select name="pastfuture" id="filter-by-past-future">' . "\n";
		echo '<option value="" ' . selected( $pastfuture, 0, false ) . '>' . esc_html( __( 'All Overrides', 'radio-station' ) ) . '</option>' . "\n";
		echo '<option value="past"' . selected( $pastfuture, 'past', false ) . '>' . esc_html( __( 'Past Overrides', 'radio-station' ) ) . '</option>' . "\n";
		echo '<option value="today"' . selected( $pastfuture, 'today', false ) . '>' . esc_html( __( 'Overrides Today', 'radio-station' ) ) . '</option>' . "\n";
		echo '<option value="future"' . selected( $pastfuture, 'future', false ) . '>' . esc_html( __( 'Future Overrides', 'radio-station' ) ) . '</option>' . "\n";
 	echo '</select>' . "\n";
}


// -----------------
// === Playlists ===
// -----------------

// -------------------------
// Add Playlist Data Metabox
// -------------------------
// --- Add custom repeating meta field for the playlist edit form ---
// (Stores multiple associated values as a serialized string)
// Borrowed and adapted from http://wordpress.stackexchange.com/questions/19838/create-more-meta-boxes-as-needed/19852#19852
add_action( 'add_meta_boxes', 'radio_station_add_playlist_metabox' );
function radio_station_add_playlist_metabox() {

	// 2.2.2: change context to show at top of edit screen
	// 2.3.2: filter top metabox position
	$position = apply_filters( 'radio_station_metabox_position', 'rstop', 'playlist' );
	add_meta_box(
		'radio-station-playlist-metabox',
		__( 'Playlist Entries', 'radio-station' ),
		'radio_station_playlist_metabox',
		RADIO_STATION_PLAYLIST_SLUG,
		$position,
		'high'
	);
}

// ---------------------
// Playlist Data Metabox
// ---------------------
function radio_station_playlist_metabox() {

	global $post, $current_screen;

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station', 'playlist_tracks_nonce' );

	// --- set button titles ---
	// 2.3.2: added titles for button icons
	$move_up_title = __( 'Move Track Up', 'radio-station' );
	$move_down_title = __( 'Move Track Down', 'radio-station' );
	$duplicate_title = __( 'Duplicate Track', 'radio-station' );
	$remove_title = __( 'Remove Track', 'radio-station' );

	// 2.3.3.9: move meta_inner ID to class
	echo '<div class="meta_inner">' . "\n";

		// 2.3.2: separate track list table
		// 2.5.6: remove unnecessary echo statement
		radio_station_playlist_track_table( $post->ID );

		// --- track save/add buttons ---
		// 2.3.2: change track save from button-primary to button-secondary
		// 2.3.2: added playlist AJAX save button (for existing posts only)
		// 2.3.2: added playlist tracks clear button
		// 2.3.2: added table and track saved message
		echo '<table id="track-table-buttons" width="100%">' . "\n";
			echo '<tr><td width="33%" align="center">' . "\n";
				echo '<input type="button" class="clear-tracks button-secondary" value="' . esc_attr( __( 'Clear Tracks', 'radio-station' ) ) . '" onclick="radio_tracks_clear();">' . "\n";
			echo '</td><td width="33%" align="center">' . "\n";
			if ( 'add' != $current_screen->action ) {
				echo '<input type="button" class="save-tracks button-primary" value="' . esc_attr( __( 'Save Tracks', 'radio-station' ) ) . '" onclick="radio_tracks_save();">' . "\n";
			}
			echo '</td><td width="33%" align="center">' . "\n";
				echo '<input type="button" class="add-track button-secondary" value="' . esc_attr( __( 'Add Track', 'radio-station' ) ) . '" onclick="radio_track_add();">' . "\n";
			echo '</td></tr>' . "\n";

			// 2.3.3.9: change to single cell spanning 3 columns
			echo '<tr><td colspan="3" align="center">' . "\n";
				echo '<div id="tracks-saving-message" style="display:none;">' . esc_html( __( 'Saving Playlist Tracks...', 'radio-station' ) ) . '</div>' . "\n";
				echo '<div id="tracks-saved-message" style="display:none;">' . esc_html( __( 'Playlist Tracks Saved.', 'radio-station' ) ) . '</div>' . "\n";
				echo '<div id="tracks-error-message" style="display:none;"></div>' . "\n";
			echo '</td></tr>' . "\n";
		echo '</table>' . "\n";

		echo '<div style="clear: both;"></div>' . "\n";

		// --- move new tracks message ---
		// 2.3.2: added new track move message
		echo '<center>' . __( 'Tracks marked New are moved to the end of Playlist on update.', 'radio-station' ) . '</center>' . "\n";

		// --- clear all tracks function ---
		$confirm_clear = __( 'Are you sure you want to clear the track list?', 'radio-station' );
		$js = "function radio_tracks_clear() {
			if (jQuery('#track-table tr').length) {
				var agree = confirm('" . esc_js( $confirm_clear ) . "');
				if (!agree) {return false;}
				jQuery('#track-table tr').remove();
				trackcount = 1;
			}
		}" . "\n";

		// --- save tracks via AJAX ---
		// 2.3.2: added form input cloning to save playlist tracks
		$ajaxurl = admin_url( 'admin-ajax.php' );
		$js .= "function radio_tracks_save() {
			jQuery('#track-save-form, #track-save-frame').remove();
			form = '<form id=\"track-save-form\" method=\"post\" action=\"" . esc_url( $ajaxurl ) . "\" target=\"track-save-frame\">';
			form += '<input type=\"hidden\" name=\"action\" value=\"radio_station_playlist_save_tracks\"></form>';
			jQuery('#wpbody').append(form);
			if (!jQuery('#track-save-frame').length) {
				frame = '<iframe id=\"track-save-frame\" name=\"track-save-frame\" src=\"javascript:void(0);\" style=\"display:none;\"></iframe>';
				jQuery('#wpbody').append(frame);
			}
			/* copy tracklist input fields and nonce */
			jQuery('#track-table input').each(function() {jQuery(this).clone().appendTo('#track-save-form');});
			jQuery('#track-table select').each(function() {
				name = jQuery(this).attr('name'); value = jQuery(this).children('option:selected').val();
				jQuery('<input type=\"hidden\" name=\"'+name+'\" value=\"'+value+'\">').appendTo('#track-save-form');
			});
			jQuery('#playlist_tracks_nonce').clone().attr('id','').appendTo('#track-save-form');
			jQuery('#post_ID').clone().attr('id','').attr('name','playlist_id').appendTo('#track-save-form');
			jQuery('#tracks-saving-message').show();
			jQuery('#track-save-form').submit();
		}" . "\n";

		// --- move track up or down ---
		// 2.3.2: added move track function
		$js .= "function radio_track_move(updown, n) {
			/* swap track rows */
			if (updown == 'up') {
				m = n - 1;
				jQuery('#track-'+n+'-rowa').insertBefore('#track-'+m+'-rowa');
				jQuery('#track-'+n+'-rowb').insertAfter('#track-'+n+'-rowa');
			} else if (updown == 'down') {
				m = n + 1;
				jQuery('#track-'+n+'-rowa').insertAfter('#track-'+m+'-rowb');
				jQuery('#track-'+n+'-rowb').insertAfter('#track-'+n+'-rowa');
			}
			/* reset track classes */
			radio_track_classes();

			/* swap track count */
			jQuery('#track-'+n+'-rowa .track-count').html(m);
			jQuery('#track-'+m+'-rowa .track-count').html(n);

			/* swap input name keys */
			jQuery('#track-'+n+'-rowa input, #track-'+n+'-rowb input, #track-'+n+'-rowb select').each(function() {
				jQuery(this).attr('name', jQuery(this).attr('name').replace('['+n+']', '['+m+']'));
			});
			jQuery('#track-'+m+'-rowa input, #track-'+m+'-rowb input, #track-'+m+'-rowb select').each(function() {
				jQuery(this).attr('name', jQuery(this).attr('name').replace('['+m+']', '['+n+']'));
			});

			/* swap button actions */
			jQuery('#track-'+n+'-rowb .track-arrow-up').attr('onclick', 'radio_track_move(\"up\", '+m+');');
			jQuery('#track-'+n+'-rowb .track-arrow-down').attr('onclick', 'radio_track_move(\"down\", '+m+');');
			jQuery('#track-'+m+'-rowb .track-arrow-up').attr('onclick', 'radio_track_move(\"up\", '+n+');');
			jQuery('#track-'+m+'-rowb .track-arrow-down').attr('onclick', 'radio_track_move(\"down\", '+n+');');
			jQuery('#track-'+n+'-rowb .track-duplicate').attr('onclick','radio_track_duplicate('+m+');');
			jQuery('#track-'+n+'-rowb .track-remove').attr('onclick','radio_track_remove('+m+');');
			jQuery('#track-'+m+'-rowb .track-duplicate').attr('onclick','radio_track_duplicate('+n+');');
			jQuery('#track-'+m+'-rowb .track-remove').attr('onclick','radio_track_remove('+n+');');

			/* swap row IDs */
			jQuery('#track-'+m+'-rowa').attr('id', 'track-0-rowa');
			jQuery('#track-'+m+'-rowb').attr('id', 'track-0-rowb');
			jQuery('#track-'+n+'-rowa').attr('id', 'track-'+m+'-rowa');
			jQuery('#track-'+n+'-rowb').attr('id', 'track-'+m+'-rowb');
			jQuery('#track-0-rowa').attr('id', 'track-'+n+'-rowa');
			jQuery('#track-0-rowb').attr('id', 'track-'+n+'-rowb');
		}" . "\n";

		// --- reset first and last track classes ---
		$js .= "function radio_track_classes() {
			jQuery('.track-rowa, .track-rowb').removeClass('first-track').removeClass('last-track');
			jQuery('.track-rowa').first().addClass('first-track'); jQuery('.track-rowa').last().addClass('last-track');
			jQuery('.track-rowb').first().addClass('first-track'); jQuery('.track-rowb').last().addClass('last-track');
		}" . "\n";

		// --- add track function ---
		// 2.3.0: set javascript as string to enqueue
		// 2.3.2: added missing track-meta cell class
		// 2.3.2: added track move arrows
		// 2.3.2: added first and last row classes
		// 2.3.2: set to standalone onclick function
		// 2.3.3.9: added track length select inputs
		$js .= "function radio_track_add() {
			if (trackcount == 1) {classes = 'first-track last-track';} else {classes = 'last-track';}
			output = '<tr id=\"track-'+trackcount+'-rowa\" class=\"track-rowa '+classes+'\">';
				output += '<td><span class=\"track-count\">'+trackcount+'</span></td>';
				output += '<td><input type=\"text\" name=\"playlist['+trackcount+'][playlist_entry_artist]\" value=\"\" style=\"width:150px;\"></td>';
				output += '<td><input type=\"text\" name=\"playlist['+trackcount+'][playlist_entry_song]\" value=\"\" style=\"width:150px;\"></td>';
				output += '<td><input type=\"text\" name=\"playlist['+trackcount+'][playlist_entry_album]\" value=\"\" style=\"width:150px;\"></td>';
				output += '<td><input type=\"text\" name=\"playlist['+trackcount+'][playlist_entry_label]\" value=\"\" style=\"width:150px;\"></td>';
			output += '</tr>';
			output += '<tr id=\"track-'+trackcount+'-rowb\" class=\"track-rowb '+classes+'\">';
				output += '<td></td><td colspan=\"2\"><div class=\"track-time\">" . esc_js( __( 'Length', 'radio-station' ) ) . ": ';
				output += '<input type=\"text\" class=\"track-minutes\" name=\"playlist['+trackcount+'][playlist_entry_minutes]\" value=\"\">m : ';
				output += '<select class=\"track-seconds\" name=\"playlist['+trackcount+'][playlist_entry_seconds]\" value=\"\">';
				for ( i = 0; i < 60; i++ ) {
					if (i < 10) {i = '0'+i;}
					output += '<option value=\"'+i+'\">'+i+'</option>';
				}
				output += '</select>s</div> ';
				output += '<div class=\"track-comments\">" . esc_js( __( 'Comments', 'radio-station' ) ) . ": <input type=\"text\" name=\"playlist['+trackcount+'][playlist_entry_comments]\" value=\"\"></div></td>';
				output += '<td class=\"track-meta\"><div>" . esc_js( __( 'New', 'radio-station' ) ) . ":</div>';
				output += '<div><input type=\"checkbox\" name=\"playlist['+trackcount+'][playlist_entry_new]\"></div>';
				output += '<div style=\"margin-left:5px;\">" . esc_js( __( 'Status', 'radio-station' ) ) . ":</div>';
				output += '<div><select name=\"playlist['+trackcount+'][playlist_entry_status]\">';
					output += '<option value=\"queued\">" . esc_js( __( 'Queued', 'radio-station' ) ) . "</option>';
					output += '<option value=\"played\">" . esc_js( __( 'Played', 'radio-station' ) ) . "</option>';
				output += '</select></div></td>';
				output += '<td class=\"track-controls\">';
					output += '<div class=\"track-move\">" . esc_js( __( 'Move', 'radio-station') ) . "</div>: ';
					output += '<div class=\"track-arrow-up\" onclick=\"radio_track_move(\'up\', '+trackcount+');\" title=\"" . esc_js( $move_up_title ) . "\">&#9652</div>';
					output += '<div class=\"track-arrow-down\" onclick=\"radio_track_move(\'down\', '+trackcount+');\" title=\"" . esc_js( $move_down_title ) . "\">&#9662</div>';
					output += '<div class=\"track-remove dashicons dashicons-no\" title=\"" . esc_js( $remove_title ) . "\" onclick=\"radio_track_remove('+trackcount+');\"></div>';
					output += '<div class=\"track-duplicate dashicons dashicons-admin-page\" title=\"" . esc_js( $duplicate_title ) . "\" onclick=\"radio_track_duplicate('+trackcount+')\"></div>';
				output += '</td>';
			output += '</tr>';

			jQuery('#track-table').append(output);
			trackcount++;
			radio_track_classes();
			return false;
		}" . "\n";

		// --- duplicate track function ---
		$js .= "function radio_track_duplicate(id) {
			var i; var nextid = id + 1;
			/* shift rows down */
			for (i = trackcount; i > id; i--) {
				jQuery('#track-'+i+'-rowa, #track-'+i+'-rowb').each(function() {
					jQuery(this).attr('id', jQuery(this).attr('id').replace(i, (i+1)));
					jQuery(this).find('.track-count').html(i+1);
					jQuery(this).find('input, select').each(function() {
						jQuery(this).attr('name', jQuery(this).attr('name').replace('['+i+']', '['+(i+1)+']'));
					});
					jQuery(this).find('.track-arrow-up').attr('onclick','radio_track_move(\"up\",'+(i+1)+');');
					jQuery(this).find('.track-arrow-down').attr('onclick','radio_track_move(\"down\",'+(i+1)+');');
					jQuery(this).find('.track-duplicate').attr('onclick','radio_track_duplicate('+(i+1)+');');
					jQuery(this).find('.track-remove').attr('onclick','radio_track_remove('+(i+1)+');');
				});
			}
			/* add duplicate row */
			jQuery('#track-'+id+'-rowa').clone().attr('id','track-'+nextid+'-rowa').insertAfter('#track-'+id+'-rowb');
			jQuery('#track-'+id+'-rowb').clone().attr('id','track-'+nextid+'-rowb').insertAfter('#track-'+nextid+'-rowa');
			jQuery('#track-'+nextid+'-rowa .track-count').html(nextid);
			jQuery('#track-'+nextid+'-rowa, #track-'+nextid+'-rowb').each(function() {
				jQuery(this).find('input, select').each(function() {
					jQuery(this).attr('name', jQuery(this).attr('name').replace('['+id+']', '['+nextid+']'));
				});
				jQuery(this).find('.track-arrow-up').attr('onclick','radio_track_move(\"up\", '+nextid+');');
				jQuery(this).find('.track-arrow-down').attr('onclick','radio_track_move(\"down\", '+nextid+');');
				jQuery(this).find('.track-duplicate').attr('onclick','radio_track_duplicate('+nextid+');');
				jQuery(this).find('.track-remove').attr('onclick','radio_track_remove('+nextid+');');
			});
			radio_track_classes();
			trackcount++;
		}" . "\n";

		// --- remove track function ---
		// 2.3.2: reset first and last classes on remove
		// 2.3.2: set to standalone onclick function
		$js .= "function radio_track_remove(id) {
			jQuery('#track-'+id+'-rowa, #track-'+id+'-rowb').remove();
			radio_track_classes();
			trackcount--;
			/* renumber track count */
			var tcount = 1;
			jQuery('.track-rowa').each(function() {
				jQuery(this).find('.track-count').html(tcount); tcount++;
			});
		}" . "\n";

		// --- set track count ---
		// 2.3.2: set count from row count length
		// 2.3.2: removed document ready wrapper
		$js .= "var trackcount = jQuery('.track-rowa').length + 1;";

		// --- enqueue inline script ---
		// 2.3.0: enqueue instead of echoing
		// 2.5.0: use radio_station_add_inline_script
		radio_station_add_inline_script( 'radio-station-admin', $js );

		// --- track list styles ---
		// 2.3.0: added track meta style fix
		// 2.3.2: added track meta select font size fix
		// 2.3.2: added track move arrow styles
		// 2.3.2: added table buttons styling
		// 2.3.2: added track save message styling
		// 2.3.3.9: margin top fixes for move arrows
		// 2.3.3.9: added track time styling
		// 2.5.0: increase width for track seconds
		$css = '.track-meta div {display: inline-block; margin-right: 3px;}
		.track-time, .track-comments {display: inline-block;}
		.track-comments {margin-left: 30px;}
		.track-minutes {width: 30px;}
		.track-seconds {width: 50px;}
		.track-meta select, .track-time select, .track-time input {font-size: 12px;}
		.track-move {margin-top: -5px;}
		.track-arrow-up, .track-arrow-down {font-size: 36px; line-height: 24px; cursor: pointer; margin-top: -10px;}
		tr.first-track .track-arrow-up, tr.last-track .track-arrow-down {display: none;}
		tr.first-track .track-arrow-down {margin-left: 20px;}
		.track-controls .track-arrow-up, .track-controls .track-arrow-down,
		.track-controls .track-move, .track-controls .remove-track, .track-controls .duplicate-track {display: inline-block; vertical-align: middle;}
		.track-controls .track-duplicate, .track-controls .track-remove {float: right; margin-right: 15px; cursor: pointer;}
		#track-table-buttons {margin-top: 20px;}
		#track-table-buttons .clear-tracks, #track-table-buttons .save-tracks, #track-table-buttons .add-track {
			cursor: pointer; display: block; width: 120px; padding: 8px; text-align: center; line-height: 1em;}
		#tracks-saving-message, #tracks-saved-message {
			background-color: lightYellow; border: 1px solid #E6DB55; margin-top: 10px; font-weight: bold; max-width: 300px; padding: 5px 0;}' . PHP_EOL;

		// --- filter and output ---
		// 2.3.3.9: added track list style filter
		// 2.5.0: use wp_kses_post on style output
		// 2.5.6: use radio_station_add_inline_style
		$css = apply_filters( 'radio_station_tracks_list_styles', $css );
		// echo '<style>' . wp_kses_post( $css ) . '</style>';
		radio_station_add_inline_style( 'rs-admin', $css );

	// --- close meta inner ---
	echo '</div>' . "\n";

	// 2.3.2: removed publish button duplication
	// (replaced with track save AJAX button)
}

// ----------------
// Track List Table
// ----------------
// 2.3.2: separated tracklist table (for AJAX)
function radio_station_playlist_track_table( $playlist_id ) {

	// --- get the saved meta as an array ---
	// 2.3.2: set single argument to true
	$entries = get_post_meta( $playlist_id, 'playlist', true );

	// --- open track table ---
	// TODO: convert track table to list ?
	echo '<table id="track-table" class="widefat">' . "\n";
		echo '<tr>' . "\n";
			echo '<th></th><th><b>' . esc_html( __( 'Artist', 'radio-station' ) ) . '</b></th>' . "\n";
			echo '<th><b>' . esc_html( __( 'Song', 'radio-station' ) ) . '</b></th>' . "\n";
			echo '<th><b>' . esc_html( __( 'Album', 'radio-station' ) ) . '</b></th>' . "\n";
			echo '<th><b>' . esc_html( __( 'Record Label', 'radio-station' ) ) . '</th>' . "\n";
		echo '</tr>' . "\n";

		// --- set button titles ---
		// 2.3.2: added titles for icon buttons
		$move_up_title = __( 'Move Track Up', 'radio-station' );
		$move_down_title = __( 'Move Track Down', 'radio-station' );
		$duplicate_title = __( 'Duplicate Track', 'radio-station' );
		$remove_title = __( 'Remove Track', 'radio-station' );

		// 2.3.2: removed [0] array key
		$c = 1;
		if ( isset( $entries ) && !empty( $entries ) ) {

			foreach ( $entries as $track ) {
				if ( isset( $track['playlist_entry_artist'] ) || isset( $track['playlist_entry_song'] )
						|| isset( $track['playlist_entry_album'] ) || isset( $track['playlist_entry_label'] )
						|| isset( $track['playlist_entry_minutes'] ) || isset( $track['playlist_entry_seconds'] )
						|| isset( $track['playlist_entry_comments'] ) || isset( $track['playlist_entry_new'] )
						|| isset( $track['playlist_entry_status'] ) ) {

					// 2.3.3.9: set any unset keys to empty
					$entry_keys = array(
						'playlist_entry_artist',
						'playlist_entry_song',
						'playlist_entry_album',
						'playlist_entry_label',
						'playlist_entry_minutes',
						'playlist_entry_seconds',
						'playlist_entry_comments',
						'playlist_entry_new',
						'playlist_entry_status',
					);
					foreach ( $entry_keys as $entry_key ) {
						if ( !isset( $track[$entry_key] ) ) {
							$track[$entry_key] = '';
						}
					}

					// --- track row A ---
					$class = '';
					if ( 1 == $c ) {
						$class = 'first-track';
					} elseif ( $c == count( $entries ) ) {
						$class = 'last-track';
					}
					echo '<tr id="track-' . esc_attr( $c ) . '-rowa" class="track-rowa ' . esc_attr( $class ) . '">' . "\n";

						// --- track count ---
						echo '<td><span class="track-count">' . esc_html( $c ) . '</span></td>' . "\n";

						// --- track entry inputs ---
						echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_artist]" value="' . esc_attr( $track['playlist_entry_artist'] ) . '" style="width:150px;"></td>' . "\n";
						echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_song]" value="' . esc_attr( $track['playlist_entry_song'] ) . '" style="width:150px;"></td>' . "\n";
						echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_album]" value="' . esc_attr( $track['playlist_entry_album'] ) . '" style="width:150px;"></td>' . "\n";
						echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_label]" value="' . esc_attr( $track['playlist_entry_label'] ) . '" style="width:150px;"></td>' . "\n";

					echo '</tr>' . "\n";

					// --- track row B ---
					echo '<tr id="track-' . esc_attr( $c ) . '-rowb" class="track-rowb ' . esc_attr( $class ) . '">' . "\n";

					// --- track length ---
					// 2.3.3.9: added track length inputs
					// 2.3.3.9: add unit translation wrappers with notes
					echo '<td></td><td colspan="2">' . "\n";
						echo '<div class="track-time">' . esc_html( __( 'Length', 'radio-station' ) ) . ': ' . "\n";
							echo '<input type="text" class="track-minutes" name="playlist[' . esc_attr( $c ) . '][playlist_entry_minutes]" value="' . esc_attr( $track['playlist_entry_minutes'] ) . '">';
							echo _x( 'm', 'minutes unit', 'radio-station' ) . ' : ' . "\n";
							echo '<select class="track-seconds" name="playlist[' . esc_attr( $c ) . '][playlist_entry_seconds]" value="' . esc_attr( $track['playlist_entry_seconds'] ) . '">' . "\n";
							for ( $i = 0; $i < 60; $i++ ) {
								if ( $i < 10 ) {
									$i = '0' . $i;
								}
								echo '<option value="' . esc_attr( $i ) . '"';
								if ( $i == $track['playlist_entry_seconds'] ) {
									echo ' selected="selected"';
								}
								echo '>' . esc_html( $i ) . '</option>' . "\n";
							}
							echo '</select>' . _x( 's', 'seconds unit', 'radio-station' ) . '</div>' . "\n";

							// --- track comments ---
							echo '<div class="track-comments">' . esc_html( __( 'Comments', 'radio-station' ) ) . ': ' . "\n";
							echo '<input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_comments]" value="' . esc_attr( $track['playlist_entry_comments'] ) . '">' . "\n";
						echo '</div>' . "\n";
					echo '</td>' . "\n";

					// --- track meta ---
					echo '<td class="track-meta">' . "\n";
						echo '<div>' . esc_html( __( 'New', 'radio-station' ) ) . ':</div>' . "\n";
						// 2.3.2: remove new value checking as now used and cleared on save
						// $track['playlist_entry_new'] = isset( $track['playlist_entry_new'] ) ? $track['playlist_entry_new'] : false;
						// ' . checked( $track['playlist_entry_new'] ) . '
						echo '<div><input type="checkbox" style="display:inline-block;" name="playlist[' . esc_attr( $c ) . '][playlist_entry_new]"></div>' . "\n";
						echo '<div style="margin-left:5px;">' . esc_html( __( 'Status', 'radio-station' ) ) . ':</div>' . "\n";
						echo '<div>' . "\n";
							echo '<select name="playlist[' . esc_attr( $c ) . '][playlist_entry_status]">' . "\n";
								echo '<option value="queued" ' . selected( $track['playlist_entry_status'], 'queued', false ) . '>' . esc_html__( 'Queued', 'radio-station' ) . '</option>' . "\n";
								echo '<option value="played" ' . selected( $track['playlist_entry_status'], 'played', false ) . '>' . esc_html__( 'Played', 'radio-station' ) . '</option>' . "\n";
							echo '</select>' . "\n";
						echo '</div></td>' . "\n";

					// 2.3.2: added move track arrows
					echo '<td class="track-controls">' . "\n";
						echo '<div class="track-move">' . esc_html( __( 'Move', 'radio-station') ) . ': </div>' . "\n";
						echo '<div class="track-arrow-up" onclick="radio_track_move(\'up\', ' . esc_attr( $c ) . ');" title="' . esc_attr( $move_up_title ) . '">&#9652</div>' . "\n";
						echo '<div class="track-arrow-down" onclick="radio_track_move(\'down\', ' . esc_attr( $c ) . ');" title="' . esc_attr( $move_down_title ) . '">&#9662</div>' . "\n";

						// --- remove track button ---
						echo '<div class="track-remove dashicons dashicons-no" title="' . esc_attr( $remove_title ) . '" onclick="radio_track_remove(' . esc_attr( $c ) . ');"></div>' . "\n";
						echo '<div class="track-duplicate dashicons dashicons-admin-page" title="' . esc_attr( $duplicate_title ) . '" onclick="radio_track_duplicate(' . esc_attr( $c ) . ');"></div>' . "\n";
					echo '</td></tr>' . "\n";

					// --- increment track count ---
					$c++;
				}
			}
		}

	echo '</table>' . "\n";
}

// -----------------------------------
// Add Assign Playlist to Show Metabox
// -----------------------------------
// (add metabox for assigning playlist to show)
add_action( 'add_meta_boxes', 'radio_station_add_playlist_show_metabox' );
function radio_station_add_playlist_show_metabox() {
	// 2.2.2: add high priority to shift above publish box
	add_meta_box(
		'radio-station-playlist-show-metabox',
		__( 'Linked Show', 'radio-station' ),
		'radio_station_playlist_show_metabox',
		RADIO_STATION_PLAYLIST_SLUG,
		'side',
		'high'
	);
}

// -------------------------------
// Assign Playlist to Show Metabox
// -------------------------------
function radio_station_playlist_show_metabox() {

	global $post, $wpdb;

	$user = wp_get_current_user();

	// --- check that we have at least one show ---
	// 2.3.0: moved up to check for any shows
	// 2.3.3.9: allow assignment to draft ahows
	$args = array(
		'numberposts' => -1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => array( 'publish', 'draft' )
	);
	$shows = get_posts( $args );
	$have_shows = ( count( $shows ) > 0 ) ? true : false;

	// --- maybe restrict show selection to user-assigned shows ---
	// 2.2.8: remove strict argument from in_array checking
	// 2.3.0: added check for new Show Editor role
	// 2.3.0: added check for edit_others_shows capability
	if ( !in_array( 'administrator', $user->roles )	&& !in_array( 'show-editor', $user->roles )	&& !current_user_can( 'edit_others_shows' ) ) {

		// --- get the user lists for all shows ---
		// 2.5.0: shorten query to one line
		$allowed_shows = array();
		$query = "SELECT meta_value, post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'show_user_list'";
		$show_user_lists = $wpdb->get_results( $query );

		// ---- check each list for the current user ---
		foreach ( $show_user_lists as $user_list ) {

			$user_list->meta_value = maybe_unserialize( $user_list->meta_value );

			// --- if a list has no users, unserialize() will return false instead of an empty array ---
			// (fix that to prevent errors in the foreach loop)
			if ( !is_array( $user_list->meta_value ) ) {
				$user_list->meta_value = array();
			}

			// --- only include shows the user is assigned to ---
			foreach ( $user_list->meta_value as $user_id ) {
				if ( $user->ID === $user_id ) {
					$allowed_shows[] = $user_list->post_id;
				}
			}
		}

		// 2.3.3.9: allow assignment to draft shows
		$args = array(
			'numberposts' => -1,
			'offset'      => 0,
			'orderby'     => 'post_title',
			'order'       => 'ASC',
			'post_type'   => RADIO_STATION_SHOW_SLUG,
			'post_status' => array( 'publish', 'draft' ),
			'include'     => implode( ',', $allowed_shows ),
		);

		$shows = get_posts( $args );
	}

	// 2.3.3.9: move meta_inner ID to class
	echo '<div class="meta_inner">' . "\n";
	
	$metabox = '';
	if ( !$have_shows ) {

		$metabox .= esc_html( __( 'No Shows were found.', 'radio-station' ) ) . "\n";

	} else {

		if ( count( $shows ) < 1 ) {

			$metabox .= esc_html( __( 'You are not assigned to any Shows.', 'radio-station' ) ) . "\n";

		} else {
			// --- add nonce field for verification ---
			wp_nonce_field( 'radio-station', 'playlist_show_nonce' );

			// --- select show to assign playlist to ---
			$current_show = get_post_meta( $post->ID, 'playlist_show_id', true );
			$metabox .= '<div>' . "\n";
				$metabox .= '<b>' . esc_html( __( 'Assign to Show', 'radio-station' ) ) . '</b><br>' . "\n";
				$metabox .= '<select id="playlist-show-select" name="playlist_show_id" onchange="radio_playlist_show_shifts();">' . "\n";
					$metabox .= '<option value="" ' . selected( $current_show, false, false ) . '>' . esc_html__( 'Unassigned', 'radio-station' ) . '</option>' . "\n";
					foreach ( $shows as $show ) {
						$metabox .= '<option value="' . esc_attr( $show->ID ) . '" ' . selected( $show->ID, $current_show, false ) . '>' . esc_html( $show->post_title ) . '</option>' . "\n";
					}
				$metabox .= '</select>' . "\n";
			$metabox .= '</div><br>' . "\n";

			// 2.5.0: added playlist show shift selector
			$current_shift = get_post_meta( $post->ID, 'playlist_shift_id', true );
			$select = radio_station_playlist_show_shift_select( $current_show, $current_shift );
			$metabox .= $select;

			// 2.5.0: filter playlist metabox output
			$metabox = apply_filters( 'radio_station_playlist_show_metabox', $metabox, $post );
			$allowed_html = radio_station_allowed_html( 'content', 'settings' );
			echo wp_kses( $metabox, $allowed_html );

			// --- selection AJAX loader iframe ----
			echo '<iframe id="playlist-shift-selection-frame" src="javascript:void(0);" style="display:none;"></iframe>' . "\n";

			// 2.5.0: playlist selection javascript
			add_action( 'admin_footer', 'radio_station_playlist_selection_script' );

		}
	}

	// --- close metabox_inner ---
	echo '</div>' . "\n";

}

// -----------------------------------
// Playlist Episode Assignment Message
// -----------------------------------
add_filter( 'radio_station_playlist_show_metabox', 'radio_station_playlist_episode_message', 10, 2 );
function radio_station_playlist_episode_message( $metabox, $post ) {

	// --- assign to Episodes upgrade to Pro message ---
	$upgrade_url = radio_station_get_upgrade_url();
	$pricing_url = radio_station_get_pricing_url();
	$message = '<div style="width:200px;">' . esc_html( __( 'You can assign Playlists to Show Episodes in Radio Station Pro.', 'radio-station' ) ) . '</div>' . "\n";
	$message .= '<a href="' . esc_url( $upgrade_url ) . '">' . esc_html( __( 'Go PRO', 'radio-station' ) ) . '</a> | ' . "\n";
	$message .= '<a href="' . esc_url( $pricing_url ) . '" target="_blank">' . esc_html( __( 'Find Out More...', 'radio-station' ) ) . '</a><br>' . "\n";

	// --- append message and return ---
	$metabox .= $message;
	return $metabox;
}

// ----------------------------------
// Playlist Show Selection Javascript
// ----------------------------------
function radio_station_playlist_selection_script() {

	$ajax_url = add_query_arg( 'action', 'radio_station_select_show_shifts', admin_url( 'admin-ajax.php' ) );
	$js = "var playlist_ajax_url = '" . esc_url( $ajax_url ) . "';
	function radio_playlist_show_shifts() {
		select = document.getElementById('playlist-show-select');
		show_id = select.options[select.selectedIndex].value;
		playlist_id = document.getElementById('post_ID').value;
		url = playlist_ajax_url+'&playlist_id='+playlist_id+'&show_id='+show_id;
		frame = document.getElementById('playlist-shift-selection-frame');
		if (frame.src != url) {
			select = document.getElementById('playlist-show-shift-select');
			if (select) {setAttribute('disabled','disabled');}
			frame.src = url;
		}
	}" . "\n";
	$js = apply_filters( 'radio_station_playlist_selection_script', $js );
	
	echo '<script>' . $js . '</script>' . "\n";

}

// ------------------------
// AJAX Get Shift Selection
// ------------------------
// 2.5.0: added AJAX action for show shift selection loading
add_action( 'wp_ajax_radio_station_select_show_shifts', 'radio_station_select_show_shifts' );
function radio_station_select_show_shifts() {
	
	// --- sanitize posted values ---
	$playlist_id = absint( $_GET['playlist_id'] );
	$show_id = absint( $_GET['show_id'] );
	if ( $show_id > 0 ) {
		$show = get_post( $show_id );
	} else {
		$show_id = false;
	}

	// --- output show shift selector ---
	$current_shift = $playlist_id ? get_post_meta( $playlist_id, 'playlist_shift_id', true ) : false;
	$select = radio_station_playlist_show_shift_select( $show_id, $current_shift );
	$allowed_html = radio_station_allowed_html( 'content', 'settings' );
	echo wp_kses( $select, $allowed_html );

	// --- send shift select output to parent frame ---
	echo "<script>selector = parent.document.getElementById('playlist-shift-select');" . "\n";
	echo "select = parent.document.getElementById('playlist-show-shift-select');" . "\n";
	echo "if (select) {select.removeAttribute('disabled');}" . "\n";
	if ( isset( $show ) && $show ) {
		echo "newselector = document.getElementById('playlist-shift-select');" . "\n";
		echo "selector.style.display = '';" . "\n";
		echo "selector.innerHTML = newselector.innerHTML;" . "\n";
	} else {
		echo "selector.style.display = 'none';" . "\n";
	}
	echo "</script>" . "\n";

	exit;
}

// ----------------------------
// Playlist Show Shift Selector
// ----------------------------
function radio_station_playlist_show_shift_select( $show_id, $current_shift ) {

	$select = '<div id="playlist-shift-select">';
	if ( $show_id ) {
		$shifts = get_post_meta( $show_id, 'show_sched', true );
		$select .= '<b>' . esc_html( __( 'Assign to Show Shift', 'radio-station' ) ) . '</b><br>' . "\n";
		$select .= '<select id="playlist-show-shift-select" name="playlist_shift_id">' . "\n";
			$select .= '<option value="" ' . selected( $current_shift, false, false ) . '>' . esc_html__( 'Latest Show', 'radio-station' ) . '</option>' . "\n";
			$select .= '<option value="" ' . selected( $current_shift, 'unassigned', false ) . '>' . esc_html__( 'Unassigned', 'radio-station' ) . '</option>' . "\n";
			if ( is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
				foreach ( $shifts as $shift_id => $shift ) {
					$shift_time = radio_station_translate_weekday( $shift['day'] ) . ' ' . $shift['start_hour'] . ':' . $shift['start_min'] . $shift['start_meridian'];
					$select .= '<option value="' . esc_attr( $shift_id ) . '" ' . selected( $shift_id, $current_shift, false ) . '>' . esc_html( $shift_time ) . '</option>' . "\n";
				}
			}
		$select .= '</select>' . "\n";
	} else {
		$select = esc_html( __( 'No Show selected.', 'radio-station' ) );
	}
	$select .= '</div><br>' . "\n";
	return $select;
}

// --------------------
// Update Playlist Data
// --------------------
// 2.3.2: added action for AJAX save of tracks
add_action( 'wp_ajax_radio_station_playlist_save_tracks', 'radio_station_playlist_save_data' );
add_action( 'save_post', 'radio_station_playlist_save_data' );
function radio_station_playlist_save_data( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- make sure we have a post ID for AJAX save ---
	// 2.3.2: added AJAX track saving checks
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		// 2.5.0: added check to exclude quick edit action
		if ( !isset( $_POST['playlist-quick-edit'] ) || ( '1' !== sanitize_text_field( $_POST['playlist-quick-edit'] ) ) ) {

			// 2.3.3: added double check for AJAX action match
			if ( !isset( $_REQUEST['action'] ) || ( 'radio_station_playlist_save_tracks' != sanitize_text_field( $_REQUEST['action'] ) ) ) {
				return;
			}

			$error = false;
			if ( !current_user_can( 'edit_playlists' ) ) {
				$error = __( 'Failed. Use manual Publish or Update instead.', 'radio-station' );
			} elseif ( !isset( $_POST['playlist_tracks_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $_POST['playlist_tracks_nonce'] ), 'radio-station' ) ) {
				$error = __( 'Expired. Publish or Update instead.', 'radio-station' );
			} elseif ( !isset( $_POST['playlist_id'] ) || ( '' == sanitize_text_field( $_POST['playlist_id'] ) ) ) {
				$error = __( 'Failed. No Playlist ID provided.', 'radio-station' );
			} else {
				$post_id = absint( $_POST['playlist_id'] );
				$post = get_post( $post_id );
				if ( !$post ) {
					$error = __( 'Failed. Invalid Playlist ID.', 'radio-station' );
				}
			}

			// --- send error message to parent window ---
			if ( $error ) {
				echo "<script>parent.document.getElementById('tracks-saving-message').style.display = 'none';" . "\n";
				echo "parent.document.getElementById('tracks-error-message').style.display = '';" . "\n";
				echo "parent.document.getElementById('tracks-error-message').innerHTML = '" . esc_js( $error ) . "';" . "\n";
				echo "form = parent.document.getElementById('track-save-form');" . "\n";
				echo "if (form) {form.parentNode.removeChild(form);}" . "\n";
				echo "</script>" . "\n";

				exit;
			}
		}
	}

	// --- save playlist tracks ---
	$playlist_changed = false;
	if ( isset( $_POST['playlist'] ) ) {

		// --- verify playlist nonce ---
		// 2.3.2: fix OR condition to AND condition
		if ( isset( $_POST['playlist_tracks_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['playlist_tracks_nonce'] ), 'radio-station' ) ) {

			$prev_playlist = get_post_meta( $post_id, 'playlist', true );
			$playlist = isset( $_POST['playlist'] ) ? $_POST['playlist'] : array();

			if ( count( $playlist ) > 0 ) {
				// move songs that are still queued to the end of the list so that order is maintained
				foreach ( $playlist as $i => $entry ) {

					// 2.3.3.9: sanitize entry key values
					$entry = radio_station_sanitize_playlist_entry( $entry );

					// 2.3.2: move songs marked as new to the end instead of queued
					// if ( 'queued' === $song['playlist_entry_status'] ) {
					if ( $entry['playlist_entry_new'] ) {
						// 2.3.2: unset before adding to maintain (now ordered) track count
						// 2.3.2: unset new flag from track record now it has been moved
						unset( $playlist[$i] );
						unset( $entry['playlist_entry_new'] );
						$playlist[] = $entry;
					}
				}
			}

			// --- check if playlist is changed ---
			if ( $prev_playlist != $playlist ) {
				update_post_meta( $post_id, 'playlist', $playlist );
				$playlist_changed = true;
			}
		}
	}

	// --- sanitize and save related show ID ---
	// 2.3.0: check for changes in related show ID
	if ( isset( $_POST['playlist_show_id'] ) ) {

		// --- verify playlist related to show nonce ---
		if ( isset( $_POST['playlist_show_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['playlist_show_nonce'] ), 'radio-station' ) ) {

			// 2.5.0: also get previous shift (if any)
			$show_changed = false;
			$prev_show = get_post_meta( $post_id, 'playlist_show_id', true );
			$prev_shift = get_post_meta( $post_id, 'playlist_shift_id', true );

			// 2.5.0: added sanitize_text_field to post request
			$show = sanitize_text_field( $_POST['playlist_show_id'] );
			if ( empty( $show ) ) {
				delete_post_meta( $post_id, 'playlist_show_id' );
				// 2.5.0: also delete shift association
				delete_post_meta( $post_id, 'playlist_shift_id' );
				if ( $prev_show ) {
					$show = $prev_show;
					$show_changed = true;
				}
			} else {
				$show = absint( $show );
				if ( ( $show > 0 ) && ( $show != $prev_show ) ) {
					update_post_meta( $post_id, 'playlist_show_id', $show );
					$show_changed = true;
				}
				// 2.5.0: maybe save selected shift
				if ( isset( $_POST['playlist_shift_id'] ) ) {
					$shift_id = sanitize_text_field( $_POST['playlist_shift_id'] );
					if ( '' != $shift_id ) {
						update_post_meta( $post_id, 'playlist_shift_id', $shift_id );
						if ( $shift_id != $prev_shift ) {
							$show_changed = true;
						}
					}
				}
			}

			// 2.5.0: added filter to check for other show changes
			$show_changed = apply_filters( 'radio_station_playlist_show_changed', $show_changed, $post_id );

			// 2.3.0: maybe clear cached data to be safe
			// 2.3.3: remove current show transient
			// 2.3.4: add previous show transient
			// 2.3.3.9: just call new clear cache function
			if ( $show_changed ) {
				// 2.4.0.4: fix to mismatching post type argument
				radio_station_clear_cached_data( $show, RADIO_STATION_SHOW_SLUG );
			}
		}
	}

	// --- AJAX saving ---
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		if ( isset( $_POST['action'] ) && ( 'radio_station_playlist_save_tracks' == sanitize_text_field( $_POST['action'] ) ) ) {

			// --- display tracks saved message ---
			// 2.3.3.9: fadeout tracks saved message
			$playlist_tracks_nonce = wp_create_nonce( 'radio-station' );
			echo "<script>parent.document.getElementById('tracks-saving-message').style.display = 'none';" . "\n";
			echo "parent.document.getElementById('tracks-saved-message').style.display = '';" . "\n";
			echo "if (typeof parent.jQuery == 'function') {parent.jQuery('#tracks-saved-message').fadeOut(3000);}" . "\n";
			echo "else {setTimeout(function() {parent.document.getElementById('tracks-saved-message').style.display = 'none';}, 3000);}" . "\n";
			// echo "form = parent.document.getElementById('track-save-form');" . "\n";
			// echo "if (form) {form.parentNode.removeChild(form);}" . "\n";
			echo "parent.document.getElementById('playlist_tracks_nonce').value = '" . esc_js( $playlist_tracks_nonce ) . "';" . "\n";
			echo "</script>" . "\n";

			// --- refresh track list table ---
			// 2.3.3.9: added check if playlist changed
			if ( $playlist_changed ) {
				// 2.5.6: remove unnecessary echo statement
				radio_station_playlist_track_table( $post_id );
				echo "<script>tracktable = parent.document.getElementById('track-table');" . "\n";
				echo "tracktable.innerHTML = document.getElementById('track-table').innerHTML;</script>";
				
				// echo esc_html( print_r( $playlist, true ) );
			}

			exit;
		}
	}
}

// -------------------------
// Add Playlist List Columns
// -------------------------
// 2.2.7: added data columns to playlist list display
add_filter( 'manage_edit-' . RADIO_STATION_PLAYLIST_SLUG . '_columns', 'radio_station_playlist_columns', 6 );
function radio_station_playlist_columns( $columns ) {

	// --- remove image columns ---
	if ( isset( $columns['thumbnail'] ) ) {
		unset( $columns['thumbnail'] );
	}
	if ( isset( $columns['post_thumb'] ) ) {
		unset( $columns['post_thumb'] );
	}

	// --- modify existing columns ---
	$date = $columns['date'];
	unset( $columns['date'] );
	$comments = $columns['comments'];
	unset( $columns['comments'] );
	
	// --- add playlist columns ---
	$columns['show'] = esc_attr( __( 'Show', 'radio-station' ) );
	$columns['trackcount'] = esc_attr( __( 'Tracks', 'radio-station' ) );
	$columns['tracklist'] = esc_attr( __( 'Track List', 'radio-station' ) );
	$columns['comments'] = $comments;
	$columns['date'] = $date;

	return $columns;
}

// -------------------------
// Playlist List Column Data
// -------------------------
// 2.2.7: added data columns for show list display
add_action( 'manage_' . RADIO_STATION_PLAYLIST_SLUG . '_posts_custom_column', 'radio_station_playlist_column_data', 5, 2 );
function radio_station_playlist_column_data( $column, $post_id ) {

	$tracks = get_post_meta( $post_id, 'playlist', true );

	if ( 'show' == $column ) {

		// 2.5.0: also get show data for quick edit
		global $post;
		$stored_post = $post;

		// --- get Shows linked to playlist ---
		$data = '';
		$show_ids = $disabled = array();
		$show_id = get_post_meta( $post_id, 'playlist_show_id', true );
		if ( $show_id ) {
			if ( is_array( $show_id ) ) {
				$show_ids = $show_id;
				foreach ( $show_ids as $i => $show_id ) {
					if ( 0 == $show_id ) {
						unset( $show_ids[$i] );
					}
				}
				$data = implode( ',', $show_ids );
			} elseif ( $show_id > 0 ) {
				$show_ids = array( $show_id );
				$data = $show_id;
			}
		}

		// --- display Shows linked to post ---
		if ( count( $show_ids ) > 0 ) {
			foreach ( $show_ids as $show_id ) {
				$show = get_post( trim( $show_id ) );
				if ( $show ) {
					$post = $show;
					if ( current_user_can( 'edit_shows' ) ) {
						echo '<a href="' . esc_url( get_edit_post_link( $show_id ) ) . '" title="' . esc_attr( __( 'Edit Show', 'radio-station' ) ) . ' ' . $show_id . '">' . PHP_EOL;
					} else {
						$disabled[] = $show_id;
					}
					echo esc_html( $show->post_title ) . '<br>' . "\n";
					if ( current_user_can( 'edit_shows' ) ) {
						echo '</a>' . "\n";
					}
				}
			}
		}

		// --- hidden show and disabled show IDs ---
		echo '<span class="show-ids" style="display:none;">' . esc_html( $data ) . '</span>' . "\n";
		echo '<span class="disabled-ids" style="display:none;">' . esc_html( implode( ',', $disabled ) ) . '</span>' . "\n";

		// --- restore global post object ---
		$post = $stored_post;

	} elseif ( 'trackcount' == $column ) {

		// 2.5.8: added check that tracks is an array
		if ( is_array( $tracks ) ) {
			echo count( $tracks ) . "\n";
		}

	} elseif ( 'tracklist' == $column ) {

		// 2.5.8: added check that tracks is an array
		if ( is_array( $tracks ) ) {
			echo '<a href="javascript:void(0);" onclick="showhidetracklist(\'' . esc_js( $post_id ) . '\')">';
			echo esc_html( __( 'Show/Hide Tracklist', 'radio-station' ) ) . '</a><br>' . "\n";
			echo '<div id="tracklist-' . esc_attr( $post_id ) . '" style="display:none;">' . "\n";
				echo '<table class="tracklist-table" cellpadding="0" cellspacing="0">' . "\n";
					echo '<tr><td class="tracklist-heading"><b>#</b></td>' . "\n";
					echo '<td><b>' . esc_html( __( 'Song', 'radio-station' ) ) . '</b></td>' . "\n";
					echo '<td><b>' . esc_html( __( 'Artist', 'radio-station' ) ) . '</b></td>' . "\n";
					echo '<td><b>' . esc_html( __( 'Status', 'radio-station' ) ) . '</b></td></tr>' . "\n";
					foreach ( $tracks as $i => $track ) {
						echo '<tr><td class="tracklist-count">' . esc_html( $i ) . '</td>' . "\n";
						echo '<td class="tracklist-song">' . esc_html( $track['playlist_entry_song'] ) . '</td>' . "\n";
						echo '<td class="tracklist-artist">' . esc_html( $track['playlist_entry_artist'] ) . '</td>' . "\n";
						$status = $track['playlist_entry_status'];
						$status = strtoupper( substr( $status, 0, 1 ) ) . substr( $status, 1, strlen( $status ) );
						echo '</td>' . "\n";
						echo '<td class="tracklist-status">' . esc_html( $status ) . '</td></tr>' . "\n";
					}
				echo '</table>' . "\n";
			echo '</div>' . "\n";
		}

	}
}

// ---------------------------
// Playlist List Column Styles
// ---------------------------
// 2.5.0: renamed function from radio_station_playlist_column_styles
add_action( 'admin_footer', 'radio_station_playlist_admin_list_styles' );
function radio_station_playlist_admin_list_styles() {
	$currentscreen = get_current_screen();
	if ( 'edit-' . RADIO_STATION_PLAYLIST_SLUG !== $currentscreen->id ) {
		return;
	}

	// --- playlist list styles ---
	$css = "#show {width: 100px;}
	#trackcount {width: 35px; font-size: 12px;}
	#tracklist {width: 250px;}
	.tracklist-table {width: 350px;}
	.tracklist-table td {padding: 0px 10px;}";

	// 2.3.3.9: add playlist list styles filter
	// 2.5.0: use wp_kses_post on style output
	// 2.5.6: use radio_station_add_inline_style
	$css = apply_filters( 'radio_station_playlist_list_styles', $css );
	// echo '<style>' . wp_kses_post( $css ) . '</style>';
	radio_station_add_inline_style( 'rs-admin', $css );

}

// ----------------------------
// Playlist List Column Scripts
// ----------------------------
// 2.5.0: separated function from playlist list column styles
add_action( 'admin_footer', 'radio_station_playlist_admin_list_scripts' );
function radio_station_playlist_admin_list_scripts() {

	// --- expand/collapse tracklist data ---
	$js = "function showhidetracklist(postid) {" . "\n";
	$js .= " if (document.getElementById('tracklist-'+postid).style.display == 'none') {" . "\n";
	$js .= "  document.getElementById('tracklist-'+postid).style.display = '';" . "\n";
	$js .= " } else {document.getElementById('tracklist-'+postid).style.display = 'none';}" . "\n";
	$js .= "}" . "\n";

	// --- enqueue script inline ---
	// 2.3.0: enqueue instead of echo
	// 2.3.3.9: filter playlist list script
	// 2.5.0: fix incorrect variable to filter (css)
	// 2.5.0: use radio_station_add_inline_script
	$js = apply_filters( 'radio_station_playlist_list_script', $js );
	radio_station_add_inline_script( 'radio-station-admin', $js );
}


// --------------------------------
// Playlist Quick Edit Input Fields
// --------------------------------
// 2.5.0: added for quick selection of playlist show
add_action( 'quick_edit_custom_box', 'radio_station_quick_edit_playlist', 10, 2 );
function radio_station_quick_edit_playlist( $column_name, $post_type ) {

	global $post, $radio_station_data;
	$stored_post = $post;

	if ( 'playlist' != $post_type ) {
		return;
	}

	if ( isset( $radio_station_data['playlist-quick-edit'] ) ) {
		return;
	}

	// --- get all shows ---
	$args = array(
		'numberposts' => -1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => array( 'publish', 'draft' ),
	);
	$shows = get_posts( $args );

	// --- show select input field ---
	echo '<fieldset class="inline-edit-col-right playlist-show-field">' . "\n";
		echo '<div class="inline-edit-col column-' . esc_attr( $column_name ) . '">' . "\n";
			echo '<label class="inline-edit-group">' . "\n";
				echo '<span class="title">' . esc_html( __( 'Assign to Show', 'radio-station' ) ) . '</span>' . "\n";
				if ( count( $shows ) > 0 ) {
					echo '<select name="playlist_show_id" class="select-show">' . "\n";
					echo '<option value="">' . esc_html( __( 'Unassigned' ) ) . '</option>' . "\n";
					foreach ( $shows as $show ) {
						$post = $show;
						echo '<option value="' . esc_attr( $show->ID ) . '"';
						if ( !current_user_can( 'edit_shows' ) ) {
							echo ' disabled="disabled"';
						}
						echo '>' . esc_html( $show->post_title ) . '</option>' . "\n";
					}
					echo '</select>' . "\n";
				} else {
					// --- no shows message ---
					echo esc_html( __( 'No Shows available to Select.', 'radio-station' ) ) . "\n";
				}
			echo '</label>' . "\n";
		echo '</div>' . "\n";
	echo '</fieldset>' . "\n";

	// --- hidden fields for quick edit saving ---
	wp_nonce_field( 'radio-station', 'playlist_show_nonce' );
	echo '<input type="hidden" name="playlist-quick-edit" value="1">' . "\n";

	// --- related shows post box styles ---
	$css = '.pre-selected {background-color:#BBB;}';
	$css = apply_filters( 'radio_station_quick_edit_playlist_styles', $css );
	// 2.5.6: use radio_station_add_inline_style
	// echo '<style>' . wp_kses_post( $css ) . '</style>' . "\n";
	radio_station_add_inline_style( 'rs-admin', $css );

	// 2.3.3.6: restore stored post object
	$post = $stored_post;
	
	// --- set flag to prevent duplication ---
	$radio_station_data['playlist-quick-edit'] = true;
}

// --------------------------
// Playlist Quick Edit Script
// --------------------------
// ref: https://codex.wordpress.org/Plugin_API/Action_Reference/quick_edit_custom_box
// 2.5.0: added for quick selection of playlist show
add_action( 'admin_enqueue_scripts', 'radio_station_playlists_quick_edit_script' );
function radio_station_playlists_quick_edit_script( $hook ) {

	if ( 'edit.php' != $hook ) {
		return;
	}

	if ( RADIO_STATION_PLAYLIST_SLUG == sanitize_text_field( $_GET['post_type'] ) ) {
		$js = "(function($) {
			var \$wp_inline_edit = inlineEditPost.edit;
			inlineEditPost.edit = function( id ) {
				\$wp_inline_edit.apply(this, arguments);
				var post_id = 0; var disabled_ids;
				if (typeof(id) == 'object') {post_id = parseInt(this.getId(id));}
				if (post_id > 0) {
					var show_ids = jQuery('#post-'+post_id+' .column-show .show-ids').text();
					if (show_ids != '') {
						if (show_ids.indexOf(',') > -1) {ids = show_ids.split(',');}
						else {ids = new Array(); ids[0] = show_ids;}
						for (i = 0; i < ids.length; i++) {
							var thisshowid = ids[i];
							jQuery('#edit-'+post_id+' .select-show option').each(function() {
								if (jQuery(this).val() == thisshowid) {jQuery(this).attr('selected','selected');}
							});
						}
						/* disable uneditable options */
						disabled = jQuery('#post-'+post_id+' .column-show .disabled-ids').text();
						if (disabled != '') {
							if (disabled.indexOf(',') > -1) {disabled_ids = disabled.split(',');}
							else {disabled_ids = new Array(); disabled_ids[0] = disabled;}
							jQuery('#edit-'+post_id+' .select-show option').each(function() {
								for (j = 0; j < disabled_ids.length; j++) {
									if (jQuery(this).val() == disabled_ids[j]) {
										jQuery(this).attr('disabled','disabled');
										if (jQuery(this).attr('selected') == 'selected') {jQuery(this).addClass('pre-selected');}
									}
								}
							});
						}
					}
				}
			};
		})(jQuery);";

		// 2.5.0: use radio_station_add_inline_script
		radio_station_add_inline_script( 'radio-station-admin', $js );
	}
}


// -------------
// === Posts ===
// -------------

// -------------------------
// Add Related Shows Metabox
// -------------------------
// (add metabox for show assignment on blog posts)
add_action( 'add_meta_boxes', 'radio_station_add_post_show_metabox' );
function radio_station_add_post_show_metabox() {

	// 2.3.0: moved check for shows inside metabox

	// ---- add a filter for which post types to show metabox on ---
	$post_types = array( 'post' );
	$post_types = apply_filters( 'radio_station_show_related_post_types', $post_types );

	// --- add the metabox to post types ---
	// 2.3.3.9: added high priority for better visibility
	// 2.3.3.9: change ID from radio-station-post-show-metabox
	add_meta_box(
		'radio-station-related-show-metabox',
		__( 'Related to Show', 'radio-station' ),
		'radio_station_post_show_metabox',
		$post_types,
		'side',
		'high'
	);
}

// ---------------------
// Related Shows Metabox
// ---------------------
function radio_station_post_show_metabox() {

	// 2.3.3.6: store current post global
	global $post;
	$stored_post = $post;

	// 2.3.3.9: filter meta key according to post type
	$post_type = $post->post_type;
	$metakey = apply_filters( 'radio_station_related_show_meta_key', 'post_showblog_id', $post_type );

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station', 'post_show_nonce' );

	// 2.3.3.9: allow for post assignment to draft Shows
	$args = array(
		'numberposts' => -1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => array( 'publish', 'draft' ),
	);
	$shows = get_posts( $args );

	// --- get current selection ---
	// 2.3.3.4: convert possible existing selection to array
	$selected = get_post_meta( $post->ID, $metakey, true );
	if ( !$selected ) {
		$selected = array();
	} elseif ( !is_array( $selected ) ) {
		$selected = array( $selected );
	}

	// 2.3.3.6: remove possible saved zero value
	// 2.3.3.9: fix to duplicate use of selected variable
	if ( count( $selected ) > 0 ) {
		foreach ( $selected as $i => $value ) {
			if ( 0 == $value ) {
				unset( $selected[$i] );
			}
		}
	}

	// 2.3.3.9: move meta_inner ID to class
	echo '<div class="meta_inner">' . "\n";

		if ( count( $shows ) > 0 ) {

			// --- select related show input ---
			// 2.2.3.4: allow for multiple selections
			// 2.3.3.9: use metakey for post type
			echo '<select multiple="multiple" name="' . esc_attr( $metakey ) . '[]">' . "\n";
				echo '<option value="">' . esc_html( __( 'Select Show(s)', 'radio-station') ) . '</option>' . "\n";

				// --- loop shows for selection options ---
				// 2.3.3.4: check for multiple selections
				foreach ( $shows as $show ) {

					// 2.3.3.6: check capability of user to edit each Show
					// (override global post object temporarily to do this)
					$post = $show;
					echo '<option value="' . esc_attr( $show->ID ) . '"';
					// ' ' . selected( $show->ID, $current, false );
					if ( in_array( $show->ID, $selected ) ) {
						echo ' selected="selected"';
					}
					// 2.2.3.3.6: disable existing but uneditable options
					if ( !current_user_can( 'edit_shows' ) ) {
						echo ' disabled="disabled"';
						if ( in_array( $show->ID, $selected ) ) {
							echo ' class="pre-selected"';
						}
					}
					echo '>' . esc_html( $show->post_title );
					if ( 'draft' == $show->post_status ) {
						echo ' (' . esc_html( __( 'Draft', 'radio-station' ) ) . ')';
					}
					echo '</option>' . "\n";
				}
			echo '</select>' . "\n";
		} else {
			// --- no shows message ---
			echo esc_html( __( 'No Shows to Select.', 'radio-station' ) ) . "\n";
		}

	echo '</div>' . "\n";

	// --- related shows post box styles ---
	// 2.3.3.6: add style for pre-selected option
	// 2.5.0: added missing style filter
	// 2.5.0: use wp_kses_post on style output
	// 2.5.6: use radio_station_add_inline_style
	$css = ".pre-selected {background-color:#BBB;}";
	$css = apply_filters( 'radio_station_post_show_box_styles', $css );
	// echo '<style>' . wp_kses_post( $css ) . '</style>' . "\n";
	radio_station_add_inline_style( 'rs-admin', $css );

	// 2.3.3.6: revert current post global
	$post = $stored_post;
}

// --------------------
// Update Related Shows
// --------------------
add_action( 'save_post', 'radio_station_post_save_data' );
function radio_station_post_save_data( $post_id ) {

	// --- do not save when doing autosaves ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// 2.3.3.6: store post for capability checking
	global $post;
	$post = get_post( $post_id );
	$stored_post = $post;

	// 2.3.3.9: filter meta key according to post type
	$post_type = $post->post_type;
	$metakey = apply_filters( 'radio_station_related_show_meta_key', 'post_showblog_id', $post_type );

	// --- check related show field is set ---
	// 2.3.0: added check if changed
	if ( isset( $_POST[$metakey] ) ) {

		// ---  verify field save nonce ---
		if ( !isset( $_POST['post_show_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $_POST['post_show_nonce'] ), 'radio-station' ) ) {
			return;
		}

		// --- get the related show ID ---
		$changed = false;
		$current_shows = get_post_meta( $post_id, $metakey, true );
		// 2.5.8: fix to use array_map on multiselect field
		$show_ids = array_map( 'absint', $_POST[$metakey] );

		// 2.3.3.6: maybe add existing (uneditable) Show IDs
		$new_show_ids = array();
		if ( $current_shows && is_array( $current_shows ) && ( count( $current_shows ) > 0 ) ) {
			foreach ( $current_shows as $current_show ) {
				if ( $current_show > 0 ) {
					$post = get_post( $current_show );
					if ( $post && !current_user_can( 'edit_shows' ) ) {
						$new_show_ids[] = $current_show;
					}
				}
			}
		}

		if ( !empty( $show_ids ) ) {
			// --- sanitize to numeric before updating ---
			// 2.3.3.4: maybe sanitize multiple array values
			if ( !is_array( $show_ids ) ) {
				$show_ids = array( $show_ids );
			}
			foreach ( $show_ids as $i => $show_id ) {
				$show_id = absint( trim( $show_id ) );
				// 2.3.3.6: check show ID value is above zero not -1
				if ( $show_id > 0 ) {
				// 2.3.3.6: check edit Show capability before adding
					$post = get_post( $show_id );
					if ( $post && current_user_can( 'edit_shows' ) && !in_array( $show_id, $new_show_ids ) ) {
						$new_show_ids[] = $show_id;
					}
				}
			}
		}

		// --- delete or update Show IDs for post ---
		// 2.3.3.6: check existing versus new show ID values
		if ( 0 == count( $new_show_ids ) ) {
			delete_post_meta( $post_id, $metakey );
			$changed = true;
		} elseif ( $new_show_ids != $current_shows ) {
			update_post_meta( $post_id, $metakey, $new_show_ids );
			$changed = true;
		}

		// 2.3.0: clear cached data to be safe
		// 2.3.3: remove current show transient
		// 2.3.4: add previous show transient
		// 2.3.3.9: just call clear cache data function
		if ( $changed ) {
			radio_station_clear_cached_data( $post_id, 'post' );
		}
	}

	// 2.3.3.6 restore stored post object
	$post = $stored_post;
}

// -------------------------------------
// Related Shows Quick Edit Select Input
// -------------------------------------
// 2.3.3.4: added Related Show field to Post List Quick Edit
add_action( 'quick_edit_custom_box', 'radio_station_quick_edit_post', 10, 2 );
function radio_station_quick_edit_post( $column_name, $post_type ) {

	global $post, $radio_station_data;
	$stored_post = $post;

	// 2.3.3.5: added fix for post type context
	if ( 'post' != $post_type ) {
		return;
	}

	// 2.4.0.6: add fix for duplicate related show box
	if ( isset( $radio_station_data['related-post-quick-edit'] ) ) {
		return;
	}

	// --- get all shows ---
	// 2.3.3.9: allow selection shows with draft status
	$args = array(
		'numberposts' => -1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => array( 'publish', 'draft' ),
	);
	$shows = get_posts( $args );

	echo '<fieldset class="inline-edit-col-right related-show-field">' . "\n";
		echo '<div class="inline-edit-col column-' . esc_attr( $column_name ) . '">' . "\n";
			wp_nonce_field( 'radio-station', 'post_show_nonce' );
			echo '<label class="inline-edit-group">' . "\n";
				echo '<span class="title">' . esc_html( __( 'Related Show(s)', 'radio-station' ) ) . '</span>' . "\n";
				if ( count( $shows ) > 0 ) {
					echo '<select multiple="multiple" name="post_showblog_id[]" class="select-show">' . "\n";
					foreach ( $shows as $show ) {
						$post = $show;
						echo '<option value="' . esc_attr( $show->ID ) . '"';
						// 2.3.3.6: disable uneditable show options
						if ( !current_user_can( 'edit_shows' ) ) {
							echo ' disabled="disabled"';
						}
						echo '>' . esc_html( $show->post_title ) . '</option>' . "\n";
					}
					echo '</select>' . "\n";
				} else {
					// --- no shows message ---
					echo esc_html( __( 'No Shows available to Select.', 'radio-station' ) ) . "\n";
				}
			echo '</label>' . "\n";
		echo '</div>' . "\n";
	echo '</fieldset>' . "\n";

	// --- related shows post box styles ---
	// 2.3.3.6: add style for pre-selected option
	// 2.5.6: use radio_station_add_inline_style
	$css = '.pre-selected {background-color:#BBB;}';
	$css = apply_filters( 'radio_station_quick_edit_post_styles', $css );
	// echo '<style>' . wp_kses_post( $css ) . '</style>' . "\n";
	radio_station_add_inline_style( 'rs-admin', $css );

	// 2.3.3.6: restore stored post object
	$post = $stored_post;
	
	// 2.4.0.6: add fix for duplicate related show box
	$radio_station_data['related-post-quick-edit'] = true;
}

// ----------------------------------
// Add Related Shows Post List Column
// ----------------------------------
// 2.3.3.4: added Related Show Column to Post List
add_filter( 'manage_edit-post_columns', 'radio_station_post_columns', 6 );
function radio_station_post_columns( $columns ) {
	$columns['show'] = esc_attr( __( 'Show(s)', 'radio-station' ) );
	return $columns;
}

// -----------------------------------
// Related Shows Post List Column Data
// -----------------------------------
// 2.3.3.4: added Related Show Column to Post List
add_action( 'manage_post_posts_custom_column', 'radio_station_post_column_data', 5, 2 );
function radio_station_post_column_data( $column, $post_id ) {
	if ( 'show' == $column ) {

		// 2.3.3.6: store global post object while capability checking
		global $post;
		$stored_post = $post;

		// --- get Shows linked to Post ---
		$data = '';
		$show_ids = $disabled = array();
		$show_id = get_post_meta( $post_id, 'post_showblog_id', true );
		if ( $show_id ) {
			// 2.3.3.6: add check to ignore possible saved zero value
			if ( is_array( $show_id ) ) {
				$show_ids = $show_id;
				foreach ( $show_ids as $i => $show_id ) {
					if ( 0 == $show_id ) {
						unset( $show_ids[$i] );
					}
				}
				// 2.3.3.8: fix to implode show_ids not show_id
				$data = implode( ',', $show_ids );
			} elseif ( $show_id > 0 ) {
				$show_ids = array( $show_id );
				$data = $show_id;
			}
		}

		// --- display Shows linked to post ---
		if ( count( $show_ids ) > 0 ) {
			foreach ( $show_ids as $show_id ) {
				$show = get_post( trim( $show_id ) );
				if ( $show ) {
					// 2.3.3.6: only link to Shows user can edit
					$post = $show;
					if ( current_user_can( 'edit_shows' ) ) {
						// 2.5.0: added missing esc_url wrapper
						echo '<a href="' . esc_url( get_edit_post_link( $show_id ) ) . '" title="' . esc_attr( __( 'Edit Show', 'radio-station' ) ) . ' ' . $show_id . '">' . PHP_EOL;
					} else {
						// 2.3.3.6: set disabled (uneditable) data
						$disabled[] = $show_id;
					}
					echo esc_html( $show->post_title ) . '<br>' . "\n";
					if ( current_user_can( 'edit_shows' ) ) {
						echo '</a>' . "\n";
					}
				}
			}
		}

		// --- hidden show and disabled show IDs ---
		echo '<span class="show-ids" style="display:none;">' . esc_html( $data ) . '</span>' . "\n";
		echo '<span class="disabled-ids" style="display:none;">' . esc_html( implode( ',', $disabled ) ) . '</span>' . "\n";

		// --- restore global post object ---
		$post = $stored_post;
	}
}

// -------------------------------
// Related Shows Quick Edit Script
// -------------------------------
// ref: https://codex.wordpress.org/Plugin_API/Action_Reference/quick_edit_custom_box
// 2.3.3.4: added Related Show Quick Edit value population script
// 2.3.3.6: disable uneditable Show select options
add_action( 'admin_enqueue_scripts', 'radio_station_posts_quick_edit_script' );
function radio_station_posts_quick_edit_script( $hook ) {

	if ( 'edit.php' != $hook ) {
		return;
	}

	// 2.3.3.7: use jQuery instead of \$ for better compatibility
	// 2.5.0: added sanitize_text_field to posted value
	if ( !isset( $_GET['post_type'] ) || ( 'post' == sanitize_text_field( $_GET['post_type'] ) ) ) {
		$js = "(function($) {
			var \$wp_inline_edit = inlineEditPost.edit;
			inlineEditPost.edit = function( id ) {
				\$wp_inline_edit.apply(this, arguments);
				var post_id = 0; var disabled_ids;
				if (typeof(id) == 'object') {post_id = parseInt(this.getId(id));}
				if (post_id > 0) {
					var show_ids = jQuery('#post-'+post_id+' .column-show .show-ids').text();
					if (show_ids != '') {
						if (show_ids.indexOf(',') > -1) {ids = show_ids.split(',');}
						else {ids = new Array(); ids[0] = show_ids;}
						for (i = 0; i < ids.length; i++) {
							var thisshowid = ids[i];
							jQuery('#edit-'+post_id+' .select-show option').each(function() {
								if (jQuery(this).val() == thisshowid) {jQuery(this).attr('selected','selected');}
							});
						}
						/* disable uneditable options */
						disabled = jQuery('#post-'+post_id+' .column-show .disabled-ids').text();
						if (disabled != '') {
							if (disabled.indexOf(',') > -1) {disabled_ids = disabled.split(',');}
							else {disabled_ids = new Array(); disabled_ids[0] = disabled;}
							jQuery('#edit-'+post_id+' .select-show option').each(function() {
								for (j = 0; j < disabled_ids.length; j++) {
									if (jQuery(this).val() == disabled_ids[j]) {
										jQuery(this).attr('disabled','disabled');
										if (jQuery(this).attr('selected') == 'selected') {jQuery(this).addClass('pre-selected');}
									}
								}
							});
						}
					}
				}
			};
		})(jQuery);";

		// 2.5.0: use radio_station_add_inline_script
		radio_station_add_inline_script( 'radio-station-admin', $js );
	}
}


// --------------------------
// Add Bulk Edit Posts Action
// --------------------------
// 2.3.3.4: add action to Bulk Edit list
// ref: https://dream-encode.com/wordpress-custom-bulk-actions/
add_filter( 'bulk_actions-edit-post', 'radio_station_show_posts_bulk_edit_action' );
function radio_station_show_posts_bulk_edit_action( $bulk_actions ) {
	$bulk_actions['related_show'] = __( 'Set Related Show(s)', 'radio-station' );
	return $bulk_actions;
}

// ----------------------
// Bulk Edit Posts Script
// ----------------------
// 2.3.3.4: add script for Bulk Edit action
add_action( 'admin_enqueue_scripts', 'radio_station_show_posts_bulk_edit_script' );
function radio_station_show_posts_bulk_edit_script( $hook ) {

	if ( 'edit.php' != $hook ) {
		return;
	}

	// 2.3.3.7: use jQuery instead of \$ for better compatibility
	// 2.3.3.7: do not reclone the show field if it already exists
	// 2.5.0: added sanitize_text_field to posted value
	if ( !isset( $_GET['post_type'] ) || ( 'post' == sanitize_text_field( $_GET['post_type'] ) ) ) {
		$js = "jQuery(document).ready(function() {
			jQuery('#bulk-action-selector-top, #bulk-action-selector-bottom').on('change', function(e) {
				if (jQuery(this).val() == 'related_show') {
					/* clone the Quick Edit fieldset to after bulk action selector */
					if (!jQuery(this).parent().find('.related-show-field').length) {
						jQuery('.related-show-field').first().clone().insertAfter(jQuery(this));
					}
				} else {
					jQuery(this).find('.related-show-field').remove();
				}
			});
		});";

		// 2.5.0: use radio_station_add_inline_script
		radio_station_add_inline_script( 'radio-station-admin', $js );
	}
}

// -----------------------
// Bulk Edit Posts Handler
// -----------------------
// 2.3.3.4: add handler for bulk edit action
add_filter( 'handle_bulk_actions-edit-post', 'radio_station_posts_bulk_edit_handler', 10, 3 );
function radio_station_posts_bulk_edit_handler( $redirect_to, $action, $post_ids ) {

	global $post;
	$stored_post = $post;

	if ( 'related_show' !== $action ) {
		return $redirect_to;
	} elseif ( !isset( $_REQUEST['post_showblog_id'] ) || ( '' == $_REQUEST['post_showblog_id'] ) ) {
		return $redirect_to;
	}

	// 2.5.0: added sanitize_text_field to request value
	// 2.5.8: fix to use array_map on multiselect field
	$show_ids = array_map( 'absint', $_REQUEST['post_showblog_id'] );

	// 2.3.3.6: check that user can edit specified Shows
	$posted_show_ids = array();
	if ( count( $show_ids ) > 0 ) {
		foreach ( $show_ids as $show_id ) {
			// 2.3.3.6: added check to ignore zero values
			if ( 0 != $show_id ) {
				$post = get_post( $show_id );
				if ( current_user_can( 'edit_shows' ) ) {
					$posted_show_ids[] = $show_id;
				}
			}
		}
	}

	// --- loop post IDs to update ---
	$updated_post_ids = $failed_post_ids = array();
	foreach ( $post_ids as $post_id ) {
		$post = get_post( $post_id );
		if ( $post ) {

			// 2.3.3.6: keep existing (non-editable) related Shows for post
			$existing_show_ids = array();
			$current_ids = get_post_meta( $post_id, 'post_showblog_id', true );
			if ( $current_ids && is_array( $current_ids ) && ( count( $current_ids ) > 0 ) ) {
				foreach ( $current_ids as $i => $current_id ) {
					// 2.3.3.6: added check to ignore possible zero values
					if ( 0 != $current_id ) {
						$post = get_post( $current_id );
						if ( !current_user_can( 'edit_shows' ) ) {
							$existing_show_ids[] = $current_id;
						}
					}
				}
			}
			$new_show_ids = array_merge( $posted_show_ids, $existing_show_ids );

			// --- update to new show IDs ---
			update_post_meta( $post_id, 'post_showblog_id', $new_show_ids );
			$updated_post_ids[] = $post_id;
		} else {
			$failed_post_ids[] = $post_id;
		}
  	}

	if ( count( $updated_post_ids ) > 0 ) {
		$redirect_to = add_query_arg( 'radio_station_related_show_updated', count( $updated_post_ids ), $redirect_to );
	}
	if ( count( $failed_post_ids ) > 0 ) {
		$redirect_to = add_query_arg( 'radio_station_related_show_failed', count( $failed_post_ids ), $redirect_to );
	}

	// --- restore stored post ---
	$post = $stored_post;

	return $redirect_to;
}

// ----------------------
// Bulk Edit Posts Notice
// ----------------------
// 2.3.3.4: add notice for bulk edit result
add_action( 'admin_notices', 'radio_station_posts_bulk_edit_notice' );
function radio_station_posts_bulk_edit_notice() {

	$updated = $failed = false;
	if ( isset( $_REQUEST['radio_station_related_show_updated'] ) ) {
		// 2.5.6: fix variable typo updated_
		// 2.5.6: use absint instead of intval
		$updated = absint( $_REQUEST['radio_station_related_show_updated'] );
	}
	if ( isset( $_REQUEST['radio_station_related_show_failed'] ) ) {
		$failed = absint( $_REQUEST['radio_station_related_show_failed'] );
	}
	if ( $updated || $failed ) {

		// 2.5.0: fix to mismatched variable updated_products_count
		$class = ( $updated > 0 ) ? 'updated' : 'error';
		echo '<div id="message" class="' . esc_attr( $class ) . '">' . "\n";

		if ( $updated > 0 ) {
			// --- number of posts updated message ---
			$message = __( 'Updated Related Shows for %d Posts.', 'radio_station' );
			$message = sprintf( $message, $updated );
			echo '<p>' . esc_html( $message ) . '</p>' . "\n";
		}
		if ( $failed > 0 ) {
			// --- number of posts failed messsage ---
			$message = __( 'Failed to Update Related Shows for %d Posts.', 'radio-station' );
			$message = sprintf( $message, $failed );
			echo '<p>' . esc_html( $message ) . '</p>' . "\n";
		}

		echo '</div>' . "\n";
  	}
}

// -----------------------------
// Related Show Post List Styles
// -----------------------------
// 2.3.3.4: added Related Show Post List styles
add_action( 'admin_footer', 'radio_station_posts_list_styles' );
function radio_station_posts_list_styles() {
	$currentscreen = get_current_screen();
	if ( 'edit-post' !== $currentscreen->id ) {
		return;
	}

	// --- post list styles ---
	// 2.3.3.9: fix to column-show type (oclumn-show)
	$css = ".wp-list-table .posts .column-show {max-width: 100px;}
	.inline-edit-col .select-show {min-width: 200px; min-height: 100px;}
	.bulkactions .column-show .title {display: none;}";

	// 2.3.3.9: added posts list styles filter
	// 2.5.0: added wp_kses_post to style output
	// 2.5.6: use radio_station_add_inline_style
	$css = apply_filters( 'radio_station_posts_list_styles', $css );
	// echo '<style>' . wp_kses_post( $css ) . '</style>';
	radio_station_add_inline_style( 'rs-admin', $css );
}


// -----------------------
// === Extra Functions ===
// -----------------------

// ---------------------------
// Post Type List Query Filter
// ---------------------------
// 2.2.7: added filter for custom column sorting
add_action( 'pre_get_posts', 'radio_station_columns_query_filter' );
function radio_station_columns_query_filter( $query ) {

	if ( !is_admin() || !$query->is_main_query() ) {
		return;
	}

	// --- Shows by Shift Days Filtering ---
	if ( RADIO_STATION_SHOW_SLUG === $query->get( 'post_type' ) ) {

		// --- check if day filter is set ---
		if ( isset( $_GET['weekday'] ) && ( '0' != sanitize_text_field( $_GET['weekday'] ) ) ) {

			$weekday = sanitize_text_field( $_GET['weekday'] );

			// need to loop and sync a separate meta key to enable filtering
			// (not really efficient but at least it makes it possible!)
			// ...but could be improved by checking against postmeta table
			// 2.3.0: cache all show posts query result for efficiency
			global $radio_station_data;
			if ( isset( $radio_station_data['all-shows'] ) ) {
				$results = $radio_station_data['all-shows'];
			} else {
				global $wpdb;
				$showquery = "SELECT ID FROM " . $wpdb->posts . " WHERE post_type = %s";
				$results = $wpdb->get_results( $wpdb->prepare( $showquery, RADIO_STATION_SHOW_SLUG ), ARRAY_A );
				$radio_station_data['all-shows'] = $results;
			}
			if ( $results && ( count( $results ) > 0 ) ) {
				foreach ( $results as $result ) {

					$post_id = $result['ID'];
					$shifts = radio_station_get_show_schedule( $post_id );

					if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
						// $shiftdays = array();
						$shiftstart = $prevtime = false;
						foreach ( $shifts as $shift ) {
							if ( $shift['day'] == $weekday ) {
								// 2.3.0: replace old with new 24 hour conversion
								// $shiftstart = $shifttime['start_hour'] . ':' . $shifttime['start_min'] . ":00";
								// $shiftstart = radio_station_convert_schedule_to_24hour( $shift );
								// 2.3.2: replace strtotime with to_time for timezones
								$shiftstart = $shift['start_hour'] . ':' . $shift['start_min'] . $shift['start_meridian'];
								$shifttime = radio_station_convert_shift_time( $shiftstart );
								$shifttime = radio_station_to_time( $weekday . ' ' . $shiftstart );
								// 2.3.0: check for earliest shift for that day
								if ( !$prevtime || ( $shifttime < $prevtime ) ) {
									// 2.3.2: adjust as already converted to 24 hour
									// $shiftstart = radio_station_convert_shift_time( $shiftstart, 24 ) . ':00';
									$shiftstart .= ':00';
									$prevtime = $shifttime;
								}
							}
						}
						if ( $shiftstart ) {
							update_post_meta( $post_id, 'show_shift_time', $shiftstart );
						} else {
							delete_post_meta( $post_id, 'show_shift_time' );
						}
					} else {
						delete_post_meta( $post_id, 'show_shift_time' );
					}
				}
			}

			// --- order by show time start ---
			// only need to set the orderby query and exists check is automatically done!
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'show_shift_time' );
			$query->set( 'meta_type', 'TIME' );
		}
	}

	// --- Order Show Overrides by Override Date ---
	// also making this the default sort order
	// if ( 'show_override_date' === $query->get( 'orderby' ) ) {
	if ( RADIO_STATION_OVERRIDE_SLUG === $query->get( 'post_type' ) ) {

		// unless order by published date is explicitly chosen
		if ( 'date' !== $query->get( 'orderby' ) ) {

			// need to loop and sync a separate meta key to enable orderby sorting
			// (not really efficient but at least it makes it possible!)
			// ...but could be improved by checking against postmeta table
			// 2.3.3.5: use wpdb prepare method on query
			global $wpdb;
			$overridequery = "SELECT ID FROM " . $wpdb->posts . " WHERE post_type = %s";
			$results = $wpdb->get_results( $wpdb->prepare( $overridequery, RADIO_STATION_OVERRIDE_SLUG ), ARRAY_A );
			if ( $results && ( count( $results ) > 0 ) ) {
				foreach ( $results as $result ) {
					$post_id = $result['ID'];
					$overrides = get_post_meta( $post_id, 'show_override_sched', true );

					// 2.3.3.9: refresh and loop possible multiple overrides
					delete_post_meta( $post_id, 'show_override_date' );
					if ( $overrides && is_array( $overrides ) ) {
						if ( array_key_exists( 'date', $overrides ) ) {
							$overrides = array( $overrides );
						}
						foreach ( $overrides as $i => $override ) {
							add_post_meta( $post_id, 'show_override_date', $override['date'] );
						}
					}
				}
			}

			// --- now we can set the orderby meta query to the synced key ---
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'show_override_date' );
			$query->set( 'meta_type', 'date' );

			// --- apply override year/month filtering ---
			if ( isset( $_GET['month'] ) && ( '0' != sanitize_text_field( $_GET['month'] ) ) ) {
				$yearmonth = sanitize_text_field( $_GET['month'] );
				$start_date = radio_station_get_time( $yearmonth . '-01' );
				$end_date = radio_station_get_time( $yearmonth . '-t' );
				// 2.5.0: fix to use double array for meta_query
				$meta_query = array( array(
					'key'     => 'show_override_date',
					'value'   => array( $start_date, $end_date ),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				) );
				$query->set( 'meta_query', $meta_query );
			}

			// --- meta query for past / future overrides filter ---
			// 2.3.3: added past future query prototype code
			// 2.3.3.5: added option for today selection
			$valid = array( 'past', 'today', 'future' );
			if ( isset( $_GET['pastfuture'] ) && in_array( sanitize_text_field( $_GET['pastfuture'] ), $valid ) ) {

				$now = radio_station_get_now();
				$date = radio_station_get_time( 'Y-m-d', $now );
				$yesterday = radio_station_get_time( 'Y-m-d', $now - ( 24 * 60 * 60 ) );
				$tomorrow = radio_station_get_time( 'Y-m-d', $now + ( 24 * 60 * 60 ) );
				$pastfuture = sanitize_text_field( $_GET['pastfuture'] );
				if ( 'today' == $pastfuture ) {
					$compare = 'BETWEEN';
					$value = array( $yesterday, $tomorrow );
				} elseif ( 'past' == $pastfuture ) {
					$compare = '<';
					$value = $date;
				} elseif ( 'future' == $pastfuture ) {
					$compare = '>';
					$value = $date;
				}

				$pastfuture_query = array(
					'key'     => 'show_override_date',
					'value'   => $value,
					'compare' => $compare,
					'type'    => 'DATE',
				);
				if ( isset( $meta_query ) ) {
					$combined_query = array(
						'relation'	=> 'AND',
						$meta_query,
						$pastfuture_query,
					);
					$meta_query = $combined_query;
				} else {
					// 2.5.0: fix to use double array for single meta_query
					$meta_query = array( $pastfuture_query );
				}
				$query->set( 'meta_query', $meta_query );
			}

		}
	}
}

// --------------------
// Relogin AJAX Message
// --------------------
// 2.3.2: added for show shifts and playlist tracks AJAX
// 2.3.3.9: added action for override times saving
// 2.3.3.9: added action for single shift saving
add_action( 'wp_ajax_nopriv_radio_station_show_save_shift', 'radio_station_relogin_message' );
add_action( 'wp_ajax_nopriv_radio_station_show_save_shifts', 'radio_station_relogin_message' );
add_action( 'wp_ajax_nopriv_radio_station_override_save', 'radio_station_relogin_message' );
add_action( 'wp_ajax_nopriv_radio_station_playlist_save_tracks', 'radio_station_relogin_message' );
function radio_station_relogin_message() {

	// --- interim login thickbox ---
	// 2.3.3.9: maybe close existing thickbox
	$js = "if (parent && parent.tb_remove) {try {parent.tb_remove();} catch(e) {} }" . "\n";
	// 2.3.3.9: trigger WP interim login screen thickbox
	$js .= "if (parent) {parent.jQuery(document).trigger('heartbeat-tick.wp-auth-check', [{'wp-auth-check': false}]);}" . "\n";

	// 2.3.3.9: fix to playlist action name prefix
	// 2.3.3.9: added support for override times
	// 2.3.3.9: add check for single shift action
	$action = sanitize_text_field( $_REQUEST['action'] );
	$save_shift_actions = array( 'radio_station_show_save_shift', 'radio_station_show_save_shifts' );
	if ( in_array( $action, $save_shift_actions ) ) {
		$type = 'shift';
	} elseif ( 'radio_station_override_save' == $action ) {
		$type = 'override';
	} elseif ( 'radio_station_playlist_save_tracks' == $action ) {
		$type = 'track';
	}

	// TODO: maybe cache the posted data so it can be restored ?

	// --- send relogin message ---
	if ( isset( $type ) ) {
		$error = __( 'Failed. Please relogin and try again.', 'radio-station' );
		// 2.5.0: fix to concatenation typo (.-)
		// 2.5.0: added missing esc_js on type variable
		$js .= "parent.document.getElementById('" . esc_js( $type ) . "s-saving-message').style.display = 'none';" . "\n";
		$js .= "parent.document.getElementById('" . esc_js( $type ) . "s-error-message').style.display = '';" . "\n";
		$js .= "parent.document.getElementById('" . esc_js( $type ) . "s-error-message').innerHTML = '" . esc_js( $error ) . "';" . "\n";
		$js .= "form = parent.document.getElementById('" . esc_js( $type ) . "-save-form');" . "\n";
		$js .= "if (form) {form.parentNode.removeChild(form);}" . "\n";
	}

	// --- filter and output ---
	$js = apply_filters( 'radio_station_relogin_script', $js );
	echo '<script>' . $js . '</script>';
	exit;
}

