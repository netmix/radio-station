<?php

/*
 * Admin Post Types Metaboxes and Post Lists
 * Author: Nikki Blight
 * Since: 2.2.7
 */

// === Metabox Positions ===
// - Metaboxes Above Content Area
// - Modify Taxonomy Metaxboxes
// === Language Selection ===
// - Add Language Metabox
// - Language Selection Metabox
// - Update Language Term on Save
// === Playlists ===
// - Add Playlist Data Metabox
// - Playlist Data Metabox
// - Add Assign Playlist to Show Metabox
// - Assign Playlist to Show Metabox
// - Update Playlist Data
// - Add Playlist List Columns
// - Playlist List Column Data
// - Playlist List Column Styles
// === Shows ===
// - Add Related Show Metabox
// - Related Show Metabox
// - Update Related Show
// - Add Show Info Metabox
// - Show Info Metabox
// - Add Assign Hosts to Show Metabox
// - Assign Hosts to Show Metabox
// - Add Assign Producers to Show Metabox
// - Assign Producers to Show Metabox
// - Add Show Shifts Metabox
// - Show Shifts Metabox
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
// - Add Schedule Override Metabox
// - Schedule Override Metabox
// - Update Schedule Override
// - Add Schedule Override List Columns
// - Schedule Override Column Data
// - Schedule Override Column Styles
// - Sortable Override Date Column
// - Add Schedule Override Month Filter
// === Post Type List Query Filter ===


// -------------------------
// === Metabox Positions ===
// -------------------------

// ----------------------------
// Metaboxes Above Content Area
// ----------------------------
// (shows metaboxes above Editor area for Radio Station CPTs)
add_action( 'edit_form_after_title', 'radio_station_top_meta_boxes' );
function radio_station_top_meta_boxes() {
	global $post;
	do_meta_boxes( get_current_screen(), 'top', $post );
}

// -------------------------
// Modify Taxonomy Metaboxes
// -------------------------
// 2.3.0: also apply to override post type
// 2.3.0: remove default languages metabox from shows
add_action( 'add_meta_boxes', 'radio_station_modify_taxonomy_meta_boxes' );
function radio_station_modify_taxonomy_meta_boxes() {
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

	// echo "<!-- METABOXES: " . print_r( $wp_meta_boxes, true ) . " -->";
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
	add_meta_box(
		RADIO_STATION_LANGUAGES_SLUG . 'div',
		__( 'Show Language', 'radio-station' ),
		'radio_station_show_language_metabox',
		array( RADIO_STATION_OVERRIDE_SLUG ),
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

	echo '<div style="margin-bottom: 5px;">';

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
		echo '<b>' . esc_html( __( 'Main Radio Language', 'radio-station' ) ) . '</b>:<br>';
		echo esc_html( $label ) . '<br>';
	}

	echo '<div style="font-size:11px;">' . esc_html( __( 'Select below if Show language(s) differ.', 'radio-station' ) ) . '</div>';

	echo '<ul id="' . esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '_taxradiolist" data-wp-lists="list:' . esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '_tax" class="categorychecklist form-no-clear">';

	// --- loop existing terms ---
	$term_slugs = array();
	foreach ( $terms as $term ) {

		$slug = $term->slug;
		$term_slugs[] = $slug;
		$label = $term->name;
		if ( !empty( $term->description ) ) {
			$label .= ' (' . $term->description . ')';
		}

		echo '<li id="' .  esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '_tax-' . esc_attr( $slug ) . '">';

		// --- hidden input for term saving ---
		// echo '<input value="' . esc_attr( $name ) . '" type="checkbox" style="display: none;" name="tax_input[' . esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '][]" id="in-' . RADIO_STATION_LANGUAGES_SLUG . '_tax-' . esc_attr( $name ) . '" checked="checked">';
		echo '<input value="' . esc_attr( $slug ) . '" type="hidden" name="' . esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '[]" id="in-' . esc_attr( RADIO_STATION_LANGUAGES_SLUG ) . '_tax-' . esc_attr( $slug ) . '">';

		// --- language term label ---
		echo '<label>' . esc_html( $label ) . '</label>';

		// --- remove term button ---
		echo '<input type="button" class="button button-secondary" onclick="radio_remove_language(\'' . esc_attr( $slug ) . '\');" value="x" title="' . esc_attr( __( 'Remove Language', 'radio-station' ) ) . '">';

		echo '</li>';
	}
	echo '</ul>';

	// --- new language selection list ---
	echo '<select id="rs-add-language-selection" onchange="radio_add_language();">';
	echo '<option selected="selected">' . esc_html( __( 'Select Language', 'radio-station' ) ) . '</option>';
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
		echo '</option>';
	}
	echo '</select><br>';

	// --- add language term button ---
	echo '<div style="font-size:11px;">' . esc_html( __( 'Click on a Language to Add it.', 'radio-station' ) ) . '</div>';

	echo '</div>';

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

	// --- add script inline ---
	wp_add_inline_script( 'radio-station-admin', $js );

	// --- language input style fixes ---
	echo "<style>#". RADIO_STATION_LANGUAGES_SLUG . "_taxradiolist input.button {
		margin-left: 10px; padding: 0 7px; color: #E00; border-radius: 7px;
	}</style>";
}

// ----------------------------
// Update Language Term on Save
// ----------------------------
// 2.3.0: added to sync language names to language term
add_action( 'save_post', 'radio_station_language_term_filter', 11 );
function radio_station_language_term_filter( $post_id ) {

	// ---- check permissions ---
	if ( !isset( $_POST[RADIO_STATION_LANGUAGES_SLUG] ) ) {return;}
	$check = wp_verify_nonce( $_POST['taxonomy_noncename'], 'taxonomy_' . RADIO_STATION_LANGUAGES_SLUG );
	if ( !$check ) {return;}
	$taxonomy_obj = get_taxonomy( RADIO_STATION_LANGUAGES_SLUG );
	if ( !current_user_can( $taxonomy_obj->cap->assign_terms ) ) {return;}

	// --- loop and set posted terms ---
	$terms = $_POST[RADIO_STATION_LANGUAGES_SLUG];

	$term_ids = array();
	if ( is_array( $terms ) && ( count( $terms ) > 0 ) ) {
		$languages = radio_station_get_languages();
		foreach ( $terms as $i => $term_slug ) {

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
	error_log( print_r( $term_ids, true ) , 3, WP_CONTENT_DIR.'/tax-debug.log' );
	wp_set_post_terms( $post_id, $term_ids, RADIO_STATION_LANGUAGES_SLUG );
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
	add_meta_box(
		'radio-station-playlist-metabox',
		__( 'Playlist Entries', 'radio-station' ),
		'radio_station_playlist_metabox',
		RADIO_STATION_PLAYLIST_SLUG,
		'top', // shift to top
		'high'
	);
}

// ---------------------
// Playlist Data Metabox
// ---------------------
function radio_station_playlist_metabox() {

	global $post;

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station', 'playlist_tracks_nonce' );

	// --- get the saved meta as an array ---
	$entries = get_post_meta( $post->ID, 'playlist', false );
	$c = 1;

	echo '<div id="meta_inner">';

	echo '<table id="here" class="widefat">';
	echo '<tr>';
	echo '<th></th><th><b>' . esc_html( __( 'Artist', 'radio-station' ) ) . '</b></th>';
	echo '<th><b>' . esc_html( __( 'Song', 'radio-station' ) ) . '</b></th>';
	echo '<th><b>' . esc_html( __( 'Album', 'radio-station' ) ) . '</b></th>';
	echo '<th><b>' . esc_html( __( 'Record Label', 'radio-station' ) ) . '</th>';
	// echo "<th><b>" . esc_html( __( 'DJ Comments', 'radio-station' ) ) . "</b></th>";
	// echo "<th><b>" . esc_html( __( 'New', 'radio-station' ) ) . "</b></th>";
	// echo "<th><b>" . esc_html( __( 'Status', 'radio-station') ) . "</b></th>";
	// echo "<th><b>" . esc_html( __( 'Remove', 'radio-station') ) . "</b></th>";
	echo '</tr>';

	if ( isset( $entries[0] ) && !empty( $entries[0] ) ) {

		foreach ( $entries[0] as $track ) {
			if ( isset( $track['playlist_entry_artist'] ) || isset( $track['playlist_entry_song'] )
			     || isset( $track['playlist_entry_album'] ) || isset( $track['playlist_entry_label'] )
			     || isset( $track['playlist_entry_comments'] ) || isset( $track['playlist_entry_new'] )
			     || isset( $track['playlist_entry_status'] ) ) {

				echo '<tr id="track-' . esc_attr( $c ) . '-rowa">';
				echo '<td>' . esc_html( $c ) . '</td>';
				echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_artist]" value="' . esc_attr( $track['playlist_entry_artist'] ) . '" /></td>';
				echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_song]" value="' . esc_attr( $track['playlist_entry_song'] ) . '" /></td>';
				echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_album]" value="' . esc_attr( $track['playlist_entry_album'] ) . '" /></td>';
				echo '<td><input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_label]" value="' . esc_attr( $track['playlist_entry_label'] ) . '" /></td>';

				echo '</tr><tr id="track-' . esc_attr( $c ) . '-rowb">';

				echo '<td colspan="3">' . esc_html__( 'Comments', 'radio-station' ) . ' ';
				echo '<input type="text" name="playlist[' . esc_attr( $c ) . '][playlist_entry_comments]" value="' . esc_attr( $track['playlist_entry_comments'] ) . '" style="width:320px;"></td>';

				echo '<td>' . esc_html( __( 'New', 'radio-station' ) ) . ' ';
				$track['playlist_entry_new'] = isset( $track['playlist_entry_new'] ) ? $track['playlist_entry_new'] : false;
				echo '<input type="checkbox" name="playlist[' . esc_attr( $c ) . '][playlist_entry_new]" ' . checked( $track['playlist_entry_new'] ) . ' />';

				echo ' ' . esc_html( __( 'Status', 'radio-station' ) ) . ' ';
				echo '<select name="playlist[' . esc_attr( $c ) . '][playlist_entry_status]">';

				echo '<option value="queued" ' . selected( $track['playlist_entry_status'], 'queued', false ) . '>' . esc_html__( 'Queued', 'radio-station' ) . '</option>';

				echo '<option value="played" ' . selected( $track['playlist_entry_status'], 'played', false ) . '>' . esc_html__( 'Played', 'radio-station' ) . '</option>';

				echo '</select></td>';

				echo '<td align="right"><span id="track-' . esc_attr( $c ) . '" class="remove button-secondary" style="cursor: pointer;">' . esc_html( __( 'Remove', 'radio-station' ) ) . '</span></td>';
				echo '</tr>';
				$c ++;
			}
		}
	}
	echo '</table>';

	?>

    <a class="add button-primary" style="cursor: pointer; float: right; margin-top: 5px;"><?php echo esc_html( __( 'Add Entry', 'radio-station' ) ); ?></a>
    <div style="clear: both;"></div>

	<?php
	// 2.3.0: set javsacript as string to enqueue
	$js = "var shiftadda = jQuery.noConflict();
		shiftadda(document).ready(function() {
			var count = " . esc_js( $c ) . ";
			shiftadda('.add').click(function() {

				output = '<tr id=\"track-'+count+'-rowa\"><td>'+count+'</td>';
					output += '<td><input type=\"text\" name=\"playlist['+count+'][playlist_entry_artist]\" value=\"\" /></td>';
					output += '<td><input type=\"text\" name=\"playlist['+count+'][playlist_entry_song]\" value=\"\" /></td>';
					output += '<td><input type=\"text\" name=\"playlist['+count+'][playlist_entry_album]\" value=\"\" /></td>';
					output += '<td><input type=\"text\" name=\"playlist['+count+'][playlist_entry_label]\" value=\"\" /></td>';
				output += '</tr><tr id=\"track-'+count+'-rowb\">';
					output += '<td colspan=\"3\">" . esc_js( __( 'Comments', 'radio-station' ) ) . ": <input type=\"text\" name=\"playlist['+count+'][playlist_entry_comments]\" value=\"\" style=\"width:320px;\"></td>';
					output += '<td>" . esc_js( __( 'New', 'radio-station' ) ) . ": <input type=\"checkbox\" name=\"playlist['+count+'][playlist_entry_new]\" />';
					output += ' " . esc_js( __( 'Status', 'radio-station' ) ) . ": <select name=\"playlist['+count+'][playlist_entry_status]\">';
						output += '<option value=\"queued\">" . esc_js( __( 'Queued', 'radio-station' ) ) . "</option>';
						output += '<option value=\"played\">" . esc_js( __( 'Played', 'radio-station' ) ) . "</option>';
					output += '</select></td>';
					output += '<td align=\"right\"><span id=\"track-'+count+'\" class=\"remove button-secondary\" style=\"cursor: pointer;\">" . esc_js( __( 'Remove', 'radio-station' ) ) . "</span></td>';
				output += '</tr>';

				shiftadda('#here').append(output);
				count = count + 1;
				return false;
			});
			shiftadda('.remove').live('click', function() {
				rowid = shiftadda(this).attr('id');
				shiftadda('#'+rowid+'-rowa').remove();
				shiftadda('#'+rowid+'-rowb').remove();
			});
		});
	";

	// --- enqueue inline script ---
	// 2.3.0: enqueue instead of echoing
	wp_add_inline_script( 'radio-station-admin', $js );

	echo '</div>';

    echo '<div id="publishing-action-bottom">';
        echo '<br/><br/>';

		$can_publish = current_user_can( 'publish_playlists' );
		// borrowed from wp-admin/includes/meta-boxes.php
		// 2.2.8: remove strict in_array checking
		if ( !in_array( $post->post_status, array( 'publish', 'future', 'private' ) ) || 0 === $post->ID ) {
			if ( $can_publish ) {
				if ( !empty( $post->post_date_gmt ) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) {
					echo '<input name="original_publish" type="hidden" id="original_publish" value="' . esc_attr( __( 'Schedule', 'radio-station' ) ) . '"/>';
					submit_button(
						__( 'Schedule', 'radio-station' ),
						'primary',
						'publish',
						false,
						array(
							'tabindex'  => '50',
							'accesskey' => 'o',
						)
					);
				} else {
                    echo '<input name="original_publish" type="hidden" id="original_publish" value="' . esc_attr( __( 'Publish', 'radio-station' ) ) . '"/>';
					submit_button(
						__( 'Publish' ),
						'primary',
						'publish',
						false,
						array(
							'tabindex'  => '50',
							'accesskey' => 'o',
						)
					);
				}
			} else {
                echo '<input name="original_publish" type="hidden" id="original_publish" value="' . esc_attr( __( 'Submit for Review', 'radio-station' ) ) . '"/>';
				submit_button(
					__( 'Update Playlist' ),
					'primary',
					'publish',
					false,
					array(
						'tabindex'  => '50',
						'accesskey' => 'o',
					)
				);
			}
		} else {
            echo '<input name="original_publish" type="hidden" id="original_publish" value="' . esc_attr( __( 'Update', 'radio-station' ) ) . '"/>';
            echo '<input name="save" type="submit" class="button-primary" id="publish" tabindex="50" accesskey="o" value="' . esc_attr( __( 'Update Playlist', 'radio-station' ) ) . '"/>';
		}
    echo '</div>';
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
	$args = array(
		'numberposts' => - 1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => 'publish',
	);
	$shows = get_posts( $args );
	if ( count( $shows ) > 0 ) {
		$have_shows = true;
	} else {
		$have_shows = false;
	}

	// --- maybe restrict show selection to user-assigned shows ---
	// 2.2.8: remove strict argument from in_array checking
	// 2.3.0: added check for new Show Editor role
	// 2.3.0: added check for edit_others_shows capability
	if ( !in_array( 'administrator', $user->roles )
	     && !in_array( 'show-editor', $user->roles )
	     && !current_user_can( 'edit_others_shows' ) ) {

		// --- get the user lists for all shows ---
		$allowed_shows = array();
		$query = "SELECT pm.meta_value, pm.post_id FROM " . $wpdb->prefix . "postmeta pm 
			WHERE pm.meta_key = 'show_user_list'";
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

		$args = array(
			'numberposts' => - 1,
			'offset'      => 0,
			'orderby'     => 'post_title',
			'order'       => 'aSC',
			'post_type'   => RADIO_STATION_SHOW_SLUG,
			'post_status' => 'publish',
			'include'     => implode( ',', $allowed_shows ),
		);

		$shows = get_posts( $args );
	}

	echo '<div id="meta_inner">';
	if ( !$have_shows ) {
		echo esc_html( __( 'No Shows were found.', 'radio-station' ) );
	} else {
		if ( count( $shows ) < 1 ) {
			echo esc_html( __( 'You are not assigned to any Shows.', 'radio-station' ) );
		} else {
			// --- add nonce field for verification ---
			wp_nonce_field( 'radio-station', 'playlist_show_nonce' );

			// --- select show to assign playlist to ---
			$current = get_post_meta( $post->ID, 'playlist_show_id', true );
			echo '<select name="playlist_show_id">';
			echo '<option value="" ' . selected( $current, false, false ) . '>' . esc_html__( 'Unassigned', 'radio-station' ) . '</option>';
			foreach ( $shows as $show ) {
				echo '<option value="' . esc_attr( $show->ID ) . '" ' . selected( $show->ID, $current, false ) . '>' . esc_html( $show->post_title ) . '</option>';
			}
			echo '</select>';
		}
	}
	echo '</div>';
}

// --------------------
// Update Playlist Data
// --------------------
// --- When a playlist is saved, saves our custom data ---
add_action( 'save_post', 'radio_station_playlist_save_data' );
function radio_station_playlist_save_data( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- save playlist tracks ---
	if ( isset( $_POST['playlist'] ) ) {

		// --- verify playlist nonce ---
		if ( isset( $_POST['playlist_tracks_nonce'] )
		     || wp_verify_nonce( $_POST['playlist_tracks_nonce'], 'radio-station' ) ) {

			$playlist = isset( $_POST['playlist'] ) ? $_POST['playlist'] : array();

			// move songs that are still queued to the end of the list so that order is maintained
			foreach ( $playlist as $i => $song ) {
				if ( 'queued' === $song['playlist_entry_status'] ) {
					$playlist[] = $song;
					unset( $playlist[$i] );
				}
			}
			update_post_meta( $post_id, 'playlist', $playlist );
		}
	}

	// --- sanitize and save related show ID ---
	// 2.3.0: check for changes in related show ID
	if ( isset( $_POST['playlist_show_id'] ) ) {

		// --- verify playlist related to show nonce ---
		if ( isset( $_POST['playlist_show_nonce'] )
		     && wp_verify_nonce( $_POST['playlist_show_nonce'], 'radio-station' ) ) {

			$changed = false;
			$prev_show = get_post_meta( $post_id, 'playlist_show_id', true );
			$show = $_POST['playlist_show_id'];
			if ( empty( $show ) ) {
				delete_post_meta( $post_id, 'playlist_show_id' );
				if ( $prev_show ) {
					$show = $prev_show;
					$changed = true;
				}
			} else {
				$show = absint( $show );
				if ( ( $show > 0 ) && ( $show != $prev_show ) ) {
					update_post_meta( $post_id, 'playlist_show_id', $show );
					$changed = true;
				}
			}

			// 2.3.0: maybe clear cached data to be safe
			if ( $changed ) {
				delete_transient( 'radio_station_current_schedule' );
				delete_transient( 'radio_station_current_show' );
				delete_transient( 'radio_station_next_show' );
				do_action( 'radio_station_clear_data', 'show_meta', $show );
			}
		}
	}

}

// -------------------------
// Add Playlist List Columns
// -------------------------
// 2.2.7: added data columns to playlist list display
add_filter( 'manage_edit-' . RADIO_STATION_PLAYLIST_SLUG . '_columns', 'radio_station_playlist_columns', 6 );
function radio_station_playlist_columns( $columns ) {
	if ( isset( $columns['thumbnail'] ) ) {
		unset( $columns['thumbnail'] );
	}
	if ( isset( $columns['post_thumb'] ) ) {
		unset( $columns['post_thumb'] );
	}
	$date = $columns['date'];
	unset( $columns['date'] );
	$comments = $columns['comments'];
	unset( $columns['comments'] );
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
	if ( 'show' == $column ) {
		$show_id = get_post_meta( $post_id, 'playlist_show_id', true );
		$post = get_post( $show_id );
		echo "<a href='" . esc_url( get_edit_post_link( $post->ID ) ) . "'>" . esc_html( $post->post_title ) . "</a>";
	} elseif ( 'trackcount' == $column ) {
		$tracks = get_post_meta( $post_id, 'playlist', true );
		echo count( $tracks );
	} elseif ( 'tracklist' == $column ) {
		$tracks = get_post_meta( $post_id, 'playlist', true );
		echo '<a href="javascript:void(0);" onclick="showhidetracklist(\'' . esc_js( $post_id ) . '\')">';
		echo esc_html( __( 'Show/Hide Tracklist', 'radio-station' ) ) . "</a><br>";
		echo '<div id="tracklist-' . esc_attr( $post_id ) . '" style="display:none;">';
		echo '<table class="tracklist-table" cellpadding="0" cellspacing="0">';
		echo '<tr><td><b>#</b></td>';
		echo '<td><b>' . esc_html( __( 'Song', 'radio-station' ) ) . '</b></td>';
		echo '<td><b>' . esc_html( __( 'Artist', 'radio-station' ) ) . '</b></td>';
		echo '<td><b>' . esc_html( __( 'Status', 'radio-station' ) ) . '</b></td></tr>';
		foreach ( $tracks as $i => $track ) {
			echo '<tr><td>' . $i . '</td>';
			echo '<td>' . esc_html( $track['playlist_entry_song'] ) . '</td>';
			echo '<td>' . esc_html( $track['playlist_entry_artist'] ) . '</td>';
			$status = $track['playlist_entry_status'];
			$status = strtoupper( substr( $status, 0, 1 ) ) . substr( $status, 1, strlen( $status ) );
			echo '</td><td>' . esc_html( $status ) . '</td></tr>';
		}
		echo '</table></div>';
	}
}

// ---------------------------
// Playlist List Column Styles
// ---------------------------
add_action( 'admin_footer', 'radio_station_playlist_column_styles' );
function radio_station_playlist_column_styles() {
	$currentscreen = get_current_screen();
	if ( 'edit-' . RADIO_STATION_PLAYLIST_SLUG !== $currentscreen->id ) {
		return;
	}
	echo "<style>#show {width: 150px;} #trackcount {width: 50px;}
	#tracklist {width: 400px;} .tracklist-table td {padding: 0px 10px;}</style>";

	// --- expand/collapse tracklist data ---
	$js = "function showhidetracklist(postid) {
		if (document.getElementById('tracklist-'+postid).style.display == 'none') {
			document.getElementById('tracklist-'+postid).style.display = '';
		} else {document.getElementById('tracklist-'+postid).style.display = 'none';}
	}";

	// --- enqueue script inline ---
	// 2.3.0: enqueue instead of echo
	wp_add_inline_script( 'radio-station-admin', $js );
}


// -------------
// === Shows ===
// -------------

// ------------------------
// Add Related Show Metabox
// ------------------------
// (add metabox for show assignment on blog posts)
add_action( 'add_meta_boxes', 'radio_station_add_post_show_metabox' );
function radio_station_add_post_show_metabox() {

	// 2.3.0: moved check for shows inside metabox

	// ---- add a filter for which post types to show metabox on ---
	$post_types = apply_filters( 'radio_station_show_related_post_types', array( 'post' ) );

	// --- add the metabox to post types ---
	add_meta_box(
		'radio-station-post-show-metabox',
		__( 'Related to Show', 'radio-station' ),
		'radio_station_post_show_metabox',
		$post_types,
		'side'
	);
}

// --------------------
// Related Show Metabox
// --------------------
function radio_station_post_show_metabox() {

	global $post;

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station', 'post_show_nonce' );

	$args = array(
		'numberposts' => - 1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => 'publish',
	);
	$shows = get_posts( $args );
	$current = get_post_meta( $post->ID, 'post_showblog_id', true );

	echo '<div id="meta_inner">';

	if ( count( $shows ) > 0 ) {
		// --- select related show input ---
		echo '<select name="post_showblog_id">';
		echo '<option value=""></option>';
		// --- loop shows for selection options ---
		foreach ( $shows as $show ) {
			echo '<option value="' . esc_attr( $show->ID ) . '" ' . selected( $show->ID, $current, false ) . '>' . esc_html( $show->post_title ) . '</option>';
		}
		echo '</select>';
	} else {
		// --- no shows message ---
		echo esc_html( __( 'No Shows to Select.', 'radio-station' ) );
	}
	echo '</div>';
}

// -------------------
// Update Related Show
// -------------------
add_action( 'save_post', 'radio_station_post_save_data' );
function radio_station_post_save_data( $post_id ) {

	// --- do not save when doing autosaves ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- check related show field is set ---
	// 2.3.0: added check if changed
	if ( isset( $_POST['post_showblog_id'] ) ) {

		// ---  verify field save nonce ---
		if ( !isset( $_POST['post_show_nonce'] )
		     || !wp_verify_nonce( $_POST['post_show_nonce'], 'radio-station' ) ) {
			return;
		}

		// --- get the related show ID ---
		$changed = false;
		$prev_show = get_post_meta( $post_id, 'post_showblog_id', true );
		$show = trim( $_POST['post_showblog_id'] );

		if ( empty( $show ) ) {
			// --- remove show from post ---
			delete_post_meta( $post_id, 'post_showblog_id' );
			if ( $prev_show ) {
				$changed = true;
			}
		} else {
			// --- sanitize to numeric before updating ---
			$show = absint( $show );
			if ( ( $show > 0 ) && ( $show != $prev_show ) ) {
				update_post_meta( $post_id, 'post_showblog_id', $show );
				$changed = true;
			}
		}

		// 2.3.0: clear cached data to be safe
		if ( $changed ) {
			delete_transient( 'radio_station_current_schedule' );
			delete_transient( 'radio_station_current_show' );
			delete_transient( 'radio_station_next_show' );
			do_action( 'radio_station_clear_data', 'show_meta', $show );
		}
	}

}

// ---------------------
// Add Show Info Metabox
// ---------------------
add_action( 'add_meta_boxes', 'radio_station_add_show_info_metabox' );
function radio_station_add_show_info_metabox() {
	// 2.2.2: change context to show at top of edit screen
	add_meta_box(
		'radio-station-show-info-metabox',
		__( 'Show Information', 'radio-station' ),
		'radio_station_show_info_metabox',
		RADIO_STATION_SHOW_SLUG,
		'top', // shift to top
		'high'
	);
}

// -----------------
// Show Info Metabox
// -----------------
function radio_station_show_info_metabox() {

	global $post;

	// 2.3.0: added missing nonce field
	wp_nonce_field( 'radio-station', 'show_meta_nonce' );

	// --- get show meta ---
	$file = get_post_meta( $post->ID, 'show_file', true );
	$email = get_post_meta( $post->ID, 'show_email', true );
	$active = get_post_meta( $post->ID, 'show_active', true );
	$link = get_post_meta( $post->ID, 'show_link', true );
	$patreon_id = get_post_meta( $post->ID, 'show_patreon', true );

	// added max-width to prevent metabox overflows
	// 2.3.0: removed new lines between labels and fields and changed widths
	echo '<div id="meta_inner">';
		echo '<p><div style="width:100px; display:inline-block;"><label>' . esc_html( __( 'Active', 'radio-station' ) ) . '?</label></div> 
		<input type="checkbox" name="show_active" ' . checked( $active, 'on', false ) . '/> 
		<em>' . esc_html( __( 'Check this box if show is currently active (Show will not appear on programming schedule if unchecked.)', 'radio-station' ) ) . '</em></p>

		<p><div style="width:100px; display:inline-block;"><label>' . esc_html( __( 'Website Link', 'radio-station' ) ) . ':</label></div> 
		<input type="text" name="show_link" size="100" style="max-width:80%;" value="' . esc_url( $link ) . '" /></p>

		<p><div style="width:100px; display:inline-block;"><label>' . esc_html( __( 'DJ / Host Email', 'radio-station' ) ) . ':</label></div> 
		<input type="text" name="show_email" size="100" style="max-width:80%;" value="' . esc_attr( $email ) . '" /></p>

		<p><div style="width:100px; display:inline-block;"><label>' . esc_html( __( 'Latest Audio File', 'radio-station' ) ) . ':</label></div> 
		<input type="text" name="show_file" size="100" style="max-width:80%;" value="' . esc_attr( $file ) . '" /></p>';

	// 2.3.0: added patreon page field
	echo '<p><div style="width:100px; display:inline-block;"><label>' . esc_html( __( 'Patreon Page ID', 'radio-station' ) ) . ':</label></div> 
		https://patreon.com/<input type="text" name="show_patreon" size="80" style="max-width:50%;" value="' . esc_attr( $patreon_id ) . '" /></p>

	</div>';

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
		
		echo '<div id="' . esc_attr( $key ) . '" class="' . esc_attr( $class ) . '">' . "\n";
		$widget_title = $metabox['title'];

		// echo '<button type="button" class="handlediv" aria-expanded="true">';
		// echo '<span class="screen-reader-text">' . esc_html( sprintf( __( 'Toggle panel: %s' ), $metabox['title'] ) ) . '</span>';
		// echo '<span class="toggle-indicator" aria-hidden="true"></span>';
		// echo '</button>';

		echo '<h2 class="hndle"><span>' . esc_html( $metabox['title'] ) . '</span></h2>';
		echo '<div class="inside">';
			call_user_func( $metabox['callback'] );
		echo "</div>";
		echo "</div>";

		$i ++;
	}
	echo '</div>';

	// --- output inside metabox styles ---
	echo "<style>#show-inside-metaboxes .postbox {display: inline-block !important; min-width: 230px; max-width: 250px; vertical-align: top;}
	#show-inside-metaboxes .postbox.first {margin-right: 20px;}
	#show-inside-metaboxes .postbox.last {margin-left: 20px;}
	#show-inside-metaboxes .postbox select {max-width: 200px;}</style>";
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

	global $post, $wp_roles, $wpdb;

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
	$current = get_post_meta( $post->ID, 'show_user_list', true );
	if ( !$current ) {
		$current = array();
	}

	// --- move any selected Hosts to the top of the list ---
	foreach ( $hosts as $i => $host ) {
		// 2.2.8: remove strict in_array checking
		if ( in_array( $host->ID, $current ) ) {
			unset( $hosts[$i] ); // unset first, or prepending will change the index numbers and cause you to delete the wrong item
			array_unshift( $hosts, $host );  // prepend the user to the beginning of the array
		}
	}

	// --- Host Selection Input ---
	// 2.2.2: add fix to make DJ multi-select input full metabox width
	echo '<div id="meta_inner">';
		echo '<select name="show_user_list[]" multiple="multiple" style="height: 120px; width: 100%;">';
			echo '<option value=""></option>';
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
				echo '>' . esc_html( $display_name ) . '</option>';
			}
        echo '</select>';

	    // --- multiple selection helper text ---
		// 2.3.0: added multiple selection helper text
		echo '<div style="font-size: 10px;">' . esc_html( __( 'Ctrl-Click selects multiple.', 'radio-station' ) ) . '</div>';
	echo '</div>';
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

	global $post, $wp_roles, $wpdb;

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
	if ( !$current ) {
		$current = array();
	}

	// --- move any selected DJs to the top of the list ---
	foreach ( $producers as $i => $producer ) {
		if ( in_array( $producer->ID, $current ) ) {
			unset( $producers[$i] ); // unset first, or prepending will change the index numbers and cause you to delete the wrong item
			array_unshift( $producers, $producer ); // prepend the user to the beginning of the array
		}
	}

	// --- Producer Selection Input ---
	echo '<div id="meta_inner">';
		echo '<select name="show_producer_list[]" multiple="multiple" style="height: 120px; width: 100%;">';
			echo '<option value=""></option>';
			foreach ( $producers as $producer ) {
				$display_name = $producer->display_name;
				if ( $producer->display_name !== $producer->user_login ) {
					$display_name .= ' (' . $producer->user_login . ')';
				}
				echo '<option value="' . esc_attr( $producer->ID ) . '"';
				if ( in_array( $producer->ID, $current ) ) {
					echo ' selected="selected"';
				}
				echo '>' . esc_html( $display_name ) . '</option>';
			}
		echo '</select>';

		// --- multiple selection helper text ---
		echo '<div style="font-size: 10px;">' . esc_html( __( 'Ctrl-Click selects multiple.', 'radio-station' ) ) . '</div>';
	echo '</div>';
}

// -----------------------
// Add Show Shifts Metabox
// -----------------------
// --- Adds schedule box to show edit screens ---
add_action( 'add_meta_boxes', 'radio_station_add_show_shifts_metabox' );
function radio_station_add_show_shifts_metabox() {
	// 2.2.2: change context to show at top of edit screen
	add_meta_box(
		'radio-station-show-shifts-metabox',
		__( 'Show Schedule', 'radio-station' ),
		'radio_station_show_shifts_metabox',
		RADIO_STATION_SHOW_SLUG,
		'top', // shift to top
		'low'
	);
}

// -------------------
// Show Shifts Metabox
// -------------------
function radio_station_show_shifts_metabox() {

	global $post;

	// --- edit show link ---
	$edit_link = add_query_arg( 'action', 'edit', admin_url( 'post.php' ) );

	// 2.2.7: added meridiem translations
	$am = radio_station_translate_meridiem( 'am' );
	$pm = radio_station_translate_meridiem( 'pm' );

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station', 'show_shifts_nonce' );

	echo '<div id="meta_inner">';

	// --- set days, hours and minutes arrays ---
	$days = array( '', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
	$hours = $mins = array();
	for ( $i = 1; $i <= 12; $i ++ ) {
		$hours[$i] = $i;
	}
	for ( $i = 0; $i < 60; $i ++ ) {
		if ( $i < 10 ) {
			$min = '0' . $i;
		} else {
			$min = $i;
		}
		$mins[$i] = $min;
	}

	// --- get the saved meta as an array ---
	$shifts = get_post_meta( $post->ID, 'show_sched', true );

	$c = 0;
	$has_conflicts = false;
	$list = '';
	if ( isset( $shifts ) && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {

		// 2.2.7: soft shifts by start day and time for ordered display
		foreach ( $shifts as $shift ) {
			// 2.3.0: add shift index to prevent start time overwriting
			$j = 1;
			if ( isset( $shift['day'] ) && ( '' != $shift['day'] ) ) {
				// --- group shifts by days of week ---
				$starttime = strtotime( 'next ' . $shift['day'] . ' ' . $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'] );
				// 2.3.0: simplify by getting day index
				$i = array_search( $shift['day'], $days );
				$day_shifts[$i][$starttime . '.' . $j] = $shift;
			} else {
				// --- to still allow shift time sorting if day is not set ---
				$starttime = strtotime( '1981-04-28 ' . $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'] );
				$day_shifts[7][$starttime . '.' . $j] = $shift;
			}
			$j ++;
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
		$show_shifts = array();
		foreach ( $sorted_shifts as $shift_day => $day_shift ) {
			// --- sort shifts by (unique) start time for each day ---
			ksort( $day_shift );
			foreach ( $day_shift as $shift ) {
				$show_shifts[] = $shift;
			}
		}

		// --- loop ordered show shifts ---
		foreach ( $show_shifts as $i => $shift ) {

			$classes = array( 'show-shift' );

			// --- check conflicts with other show shifts ---
			// 2.3.0: added shift conflict checking
			$conflicts = radio_station_check_shift( $post->ID, $shift );
			if ( $conflicts && is_array( $conflicts ) ) {
				$has_conflicts = true;
				$classes[] = 'conflicts';
			}

			// --- check if shift disabled ---
			// 2.3.0: added shift disabled switch
			if ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) {
				$classes[] = 'disabled';
			}
			$classlist = implode( " ", $classes );

			$list .= '<ul class="' . esc_attr( $classlist ) . '">';

			// --- shift day selection ---
			$list .= '<li class="first">';
			$list .= esc_html( __( 'Day', 'radio-station' ) ) . ': ';

			$class = '';
			if ( '' == $shift['day'] ) {
				$class = 'incomplete';
			}
			$list .= '<select class="' . esc_attr( $class ) . '" name="show_sched[' . esc_attr( $c ) . '][day]">';
			// 2.3.0: simplify by looping days
			foreach ( $days as $day ) {
				// 2.3.0: add weekday translation to display
				$list .= '<option value="' . esc_attr( $day ) . '" ' . selected( $day, $shift['day'], false ) . '>';
				$list .= esc_html( radio_station_translate_weekday( $day ) ) . '</option>';
			}
			$list .= '</select>';
			$list .= '</li>';

			// --- shift start time ---
			$list .= '<li>';
			$list .= esc_html( __( 'Start Time', 'radio-station' ) ) . ': ';

			// --- start hour selection ---
            $class = '';
			if ( '' == $shift['start_hour'] ) {
				$class = 'incomplete';
			}
			$list .= '<select class="' . esc_attr( $class ) . '" name="show_sched[' . esc_attr( $c ) . '][start_hour]" style="min-width:35px;">';
			foreach ( $hours as $hour ) {
				$list .= '<option value="' . esc_attr( $hour ) . '" ' . selected( $hour, $shift['start_hour'], false ) . '>' . esc_html( $hour ) . '</option>';
			}
			$list .= '</select>';

			// --- start minute selection ---
			$list .= '<select name="show_sched[' . esc_attr( $c ) . '][start_min]" style="min-width:35px;">';
			$list .= '<option value=""></option>';
			foreach ( $mins as $min ) {
				$list .= '<option value="' . esc_attr( $min ) . '" ' . selected( $min, $shift['start_min'], false ) . '>' . esc_html( $min ) . '</option>';
			}
			$list .= '</select>';

			// --- start meridiem selection ---
			$class = '';
			if ( '' == $shift['start_meridian'] ) {
				$class = 'incomplete';
			}
			$list .= '<select class="' . esc_attr( $class ) . '" name="show_sched[' . esc_attr( $c ) . '][start_meridian]" style="min-width:35px;">';
			$list .= '<option value="am" ' . selected( $shift['start_meridian'], 'am', false ) . '>' . esc_html( $am ) . '</option>';
			$list .= '<option value="pm" ' . selected( $shift['start_meridian'], 'pm', false ) . '>' . esc_html( $pm ) . '</option>';
			$list .= '</select>';
			$list .= '</li>';

			// --- shift end time ---
			$list .= '<li>';
			$list .= esc_html( __( 'End Time', 'radio-station' ) ) . ': ';

			// --- end hour selection ---
			$class = '';
			if ( '' == $shift['end_hour'] ) {
				$class = 'incomplete';
			}
			$list .= '<select class="' . esc_attr( $class ) . '" name="show_sched[' . esc_attr( $c ) . '][end_hour]" style="min-width:35px;">';
			foreach ( $hours as $hour ) {
				$list .= '<option value="' . esc_attr( $hour ) . '" ' . selected( $shift['end_hour'], $hour, false ) . '>' . esc_html( $hour ) . '</option>';
			}
			$list .= '</select>';

			// --- end minute selection ---
			$list .= '<select name="show_sched[' . esc_attr( $c ) . '][end_min]" style="min-width:35px;">';
			foreach ( $mins as $min ) {
				$list .= '<option value="' . esc_attr( $min ) . '" ' . selected( $shift['end_min'], $min, false ) . '>' . esc_html( $min ) . '</option>';
			}
			$list .= '</select>';

			// --- end meridiem selection ---
			$class = '';
			if ( '' == $shift['end_meridian'] ) {
				$class = 'incomplete';
			}
			$list .= '<select class="' . esc_attr( $class ) . '" name="show_sched[' . esc_attr( $c ) . '][end_meridian]" style="min-width:35px;">';
			$list .= '<option value="am" ' . selected( $shift['end_meridian'], 'am', false ) . '>' . esc_html( $am ) . '</option>';
			$list .= '<option value="pm" ' . selected( $shift['end_meridian'], 'pm', false ) . '>' . esc_html( $pm ) . '</option>';
			$list .= '</select>';
			$list .= '</li>';

			// --- encore presentation ---
			if ( !isset( $shift['encore'] ) ) {$shift['encore'] = '';}
			$list .= '<li>';
			$list .= '<input type="checkbox" value="on" name="show_sched[' . esc_attr( $c ) . '][encore]"' . checked( $shift['encore'], 'on', false ) . '>';
			$list .= esc_html( __( 'Encore', 'radio-station' ) );
			$list .= '</li>';

			// --- shift disabled ---
			// 2.3.0: added disabled checkbox to shift row
			if ( !isset( $shift['disabled'] ) ) {$shift['disabled'] = '';}
			$list .= '<li>';
			$list .= '<input type="checkbox" value="yes" name="show_sched[' . esc_attr( $c ) . '][disabled]"' . checked( $shift['disabled'], 'yes', false ) . '>';
			$list .= esc_html( __( 'Disabled', 'radio-station' ) );
			$list .= '</li>';

			// --- remove shift button ---
			$list .= '<li class="last">';
			$list .= '<span class="remove button button-secondary" style="cursor: pointer;">';
			$list .= esc_html( __( 'Remove', 'radio-station' ) );
			$list .= '</span>';
			$list .= '</li>';

			$list .= '</ul>';

			// --- output any shift conflicts found ---
			if ( $conflicts && is_array( $conflicts ) && ( count( $conflicts ) > 0 ) ) {
				$list .= '<div class="shift-conflicts">';
				$list .= '<b>' . esc_html( __( 'Shift Conflicts', 'radio-station' ) ) . '</b>: ';
				foreach ( $conflicts as $j => $conflict ) {
					if ( $j > 0 ) {
						$list .= ', ';
					}
					if ( $conflict['show'] == $post->ID ) {
						$list .= '<i>' . esc_html( __('This Show', 'radio-station' ) ) . '</i>';
					} else {
						$show_edit_link = add_query_arg( 'post', $conflict['show'], $edit_link );
						$show_title = get_the_title( $conflict['show'] );
						$list .= '<a href="' . esc_url( $show_edit_link ) . '">' . esc_html( $show_title ) . '</a>';
					}
					$conflict_start = esc_html( $conflict['shift']['start_hour'] ) . ':' . esc_html( $conflict['shift']['start_min'] ) . ' ' . esc_html( $conflict['shift']['start_meridian'] );
					$conflict_end = esc_html( $conflict['shift']['end_hour'] ) . ':' . esc_html( $conflict['shift']['end_min'] ). ' ' . esc_html( $conflict['shift']['end_meridian'] );
					$list .= ' - ' . esc_html( $conflict['shift']['day'] ) . ' ' . $conflict_start . ' - ' . $conflict_end;
				}
				$list .= '</div><br>';
			}

			// --- increment shift counter ---
			$c ++;
		}
	}

	// --- shift conflicts message ---
	// 2.3.0: added instructions for fixing shift conflicts
	if ( $has_conflicts ) {
		echo '<div class="shift-conflicts-message">';
		echo '<b style="color:#EE0000;">' . esc_html( __( 'Warning! Show Shift Conflicts were detected!', 'radio-station' ) ) . '</b><br>';
		echo esc_html( __( 'Please note that Shifts with conflicts are automatically disabled upon saving.', 'radio-station' ) ) . '<br>';
		echo esc_html( __( 'Fix the Shift and/or the Shift on the conflicting Show and Update them both.', 'radio-station' ) ) . '<br>';
		echo esc_html( __( 'Then you can uncheck the shift Disable box and Update to re-enable the Shift.', 'radio-station' ) ) . '<br>';
		// TODO: add more information blog post / documentation link ?
		// echo '<a href="' . RADIO_STATION_DOC_URL . '/shows/" target="_blank">' . esc_html( __( 'Show Documentation', 'radio-station' ) ) . '</a>';
		echo '</div><br>';
	}

	// --- output shift list ---
	if ( '' != $list ) {
		echo $list; // phpcs:ignore WordPress.Security.OutputNotEscaped
	}

	?>

    <span id="here"></span>
    <span style="text-align: center;"><a class="add button-primary" style="cursor: pointer; display:block; width: 150px; padding: 8px; text-align: center; line-height: 1em;"><?php echo esc_html( __( 'Add Shift', 'radio-station' ) ); ?></a></span>

	<?php
	// 2.3.0: added confirmation to remove shift button
	$confirm_remove = __( 'Are you sure you want to remove this shift?', 'radio-station' );
	$js = "var shiftaddb =jQuery.noConflict();
		shiftaddb(document).ready(function() {
			var count = " . esc_attr( $c ) . ";
			shiftaddb('.add').click(function() {
				count = count + 1;
				output = '<ul class=\"show-shift\">';
					output += '<li class=\"first\">';
						output += '" . esc_js( __( 'Day', 'radio-station' ) ) . "';
						output += '<select name=\"show_sched[' + count + '][day]\">';";

	// 2.3.0: simplify by looping days and add translation
	foreach ( $days as $day ) {
		$js .= "output += '<option value=\"" . esc_js( $day ) . "\">';";
		$js .= "output += '" . esc_js( radio_station_translate_weekday( $day ) ) . "</option>';";
	}

	$js .= "output += '</select>';
					output += '</li>';

					output += '<li>';
						output += '" . esc_js( __( 'Start Time', 'radio-station' ) ) . ": ';
						output += '<select name=\"show_sched[' + count + '][start_hour]\" style=\"min-width:35px;\">';";

	foreach ( $hours as $hour ) {
		$js .= "output += '<option value=\"" . esc_js( $hour ) . "\">" . esc_js( $hour ) . "</option>';";
	}

	$js .= "output += '</select> ';
						output += '<select name=\"show_sched[' + count + '][start_min]\" style=\"min-width:35px;\">';";

	foreach ( $mins as $min ) {
		$js .= "output += '<option value=\"" . esc_js( $min ) . "\">" . esc_js( $min ) . "</option>';";
	}

	$js .= "output += '</select> ';
						output += '<select name=\"show_sched[' + count + '][start_meridian]\" style=\"min-width:35px;\">';
							output += '<option value=\"am\">" . esc_js( $am ) . "</option>';
							output += '<option value=\"pm\">" . esc_js( $pm ) . "</option>';
						output += '</select> ';
					output += '</li>';

					output += '<li>';
						output += '" . esc_js( __( 'End Time', 'radio-station' ) ) . ": ';
						output += '<select name=\"show_sched[' + count + '][end_hour]\" style=\"min-width:35px;\">';";

	foreach ( $hours as $hour ) {
		$js .= "output += '<option value=\"" . esc_js( $hour ) . "\">" . esc_js( $hour ) . "</option>';";
	}

	$js .= "output += '</select> ';
						output += '<select name=\"show_sched[' + count + '][end_min]\" style=\"min-width:35px;\">';";

	foreach ( $mins as $min ) {
		$js .= "output += '<option value=\"" . esc_js( $min ) . "\">" . esc_js( $min ) . "</option>';";
	}

	$js .= "output += '</select> ';
						output += '<select name=\"show_sched[' + count + '][end_meridian]\" style=\"min-width:35px;\">';
							output += '<option value=\"am\">" . esc_js( $am ) . "</option>';
							output += '<option value=\"pm\">" . esc_js( $pm ) . "</option>';
						output += '</select> ';
					output += '</li>';

					output += '<li>';
						output += '<input type=\"checkbox\" value=\"on\" name=\"show_sched['+count+'][encore]\" /> " . esc_js( __( 'Encore', 'radio-station' ) ) . "';
					output += '</li>';

					output += '<li>';
						output += '<input type=\"checkbox\" value=\"yes\" name=\"show_sched['+count+'][disabled]\" /> " . esc_js( __( 'Disabled', 'radio-station' ) ) . "';
					output += '</li>';

					output += '<li class=\"last\">';
						output += '<span class=\"remove button button-secondary\" style=\"cursor: pointer;\">" . esc_js( __( 'Remove', 'radio-station' ) ) . "</span>';
					output += '</li>';

				output += '</ul>';
				shiftaddb('#here').append( output );

				return false;
			});
			shiftaddb('.remove').live('click', function() {
				/* ? maybe recheck shift count ? */
				agree = confirm('" . esc_js( $confirm_remove ) . "');
				if (!agree) {return;}
				shiftaddb(this).parent().parent().remove();
			});
		});";

	// --- enqueue inline script ---
	// 2.3.0: enqueue instead of echoing
	wp_add_inline_script( 'radio-station-admin', $js );

	echo '<style>
        .show-shift {
            list-style: none;
            margin-bottom: 10px;
            border: 2px solid green;
        }
        
        .show-shift li {
            display: inline-block;
            vertical-align: middle;
            margin-left: 20px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        
        .show-shift li.first {
            margin-left: 10px;
        }
        
        .show-shift li.last {
            margin-right: 10px;
        }
        
        .show-shift.disabled {
            border: 2px dashed orange;
        }
        
        .show-shift.conflicts {
            outline: 2px solid red;
        }
        
        .show-shift.disabled.conflicts {
            border: 2px dashed red;
            outline: none;
        }
        
        .show-shift select.incomplete {
            border: 2px solid orange;
        }</style>';

	echo '</div>';
}

// -----------------------------------
// Add Show Description Helper Metabox
// -----------------------------------
// 2.3.0: added metabox for show description helper text
add_action( 'add_meta_boxes', 'radio_station_add_show_helper_box' );
function radio_station_add_show_helper_box() {
	add_meta_box(
		'radio-station-show-helper-box',
		__( 'Show Description', 'radio-station' ),
		'radio_station_show_helper_box',
		RADIO_STATION_SHOW_SLUG,
		'top',
		'low'
	);
}

// -------------------------------
// Show Description Helper Metabox
// -------------------------------
// 2.3.0: added metabox for show description helper text
function radio_station_show_helper_box() {

	echo "<p>";

	// --- show description helper text ---
	echo esc_html( __( "The text field below is for your Show Description. It will display in the About section of your Show page.", 'radio-station' ) );
	echo ' ' . esc_html( __( "It is not recommended to include your past show content or archives in this area, as it will affect the Show page layout your visitors see.", 'radio-station' ) );
	echo esc_html( __( "It may also impact SEO, as archived content won't have their own pages and thus their own SEO and Social meta rules.", 'radio-station' ) ) . "<br>";
	echo esc_html( __( "We recommend using WordPress Posts to add new posts and assign them to your Show(s) using the Related Show metabox on the Post Edit screen so they display on the Show page.", 'radio-station' ) );
	echo ' ' . esc_html( __( "You can then assign them to a relevent Post Category for display on your site also.", 'radio-station' ) );

	// TODO: upgrade to Pro for upcoming Show Episodes blurb
	// $upgrade_url = radio_station_get_upgrade_url();
	// echo '<a href="' . $upgrade_url . '">';
	// echo esc_html( __( "Upgrade to Radio Station Pro', 'radio-station' ) );
	// echo '</a>';

	echo "</p>";

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
		__( 'Show Logo' ),
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

	if ( isset( $_GET['avatar_refix'] ) && ( 'yes' == $_GET['avatar_refix'] ) ) {
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
	echo '<div id="show-avatar-image">';

	// --- image container ---
	echo '<div class="custom-image-container">';
	if ( $has_show_avatar ) {
		echo '<img src="' . esc_url( $show_avatar_src[0] ) . '" alt="" style="max-width:100%;">';
	}
	echo '</div>';

	// --- add and remove links ---
	echo '<p class="hide-if-no-js">';
	$hidden = '';
	if ( $has_show_avatar ) {
		$hidden = ' hidden';
	}
	echo '<a class="upload-custom-image' . esc_attr( $hidden ) . '" href="' . esc_url( $upload_link ) . '">';
	echo esc_html( __( 'Set Show Avatar Image' ) );
	echo '</a>';
	$hidden = '';
	if ( !$has_show_avatar ) {
		$hidden = ' hidden';
	}
	echo '<a class="delete-custom-image' . esc_attr( $hidden ) . '" href="#">';
	echo esc_html( __( 'Remove Show Avatar Image' ) );
	echo '</a>';
	echo '</p>';

	// --- hidden input for image ID ---
	echo '<input class="custom-image-id" name="show_avatar" type="hidden" value="' . esc_attr( $show_avatar ) . '">';

	echo '</div>';

	// --- check if show content header image is enabled ---
	$header_image = radio_station_get_setting( 'show_header_image' );
	if ( $header_image ) {

		// --- get show header image info
		$show_header = get_post_meta( $post->ID, 'show_header', true );
		$show_header_src = wp_get_attachment_image_src( $show_header, 'full' );
		$has_show_header = is_array( $show_header_src );

		// --- show header image ---
		echo '<div id="show-header-image">';

		// --- image container ---
		echo '<div class="custom-image-container">';
		if ( $has_show_header ) {
			echo '<img src="' . esc_url( $show_header_src[0] ) . '" alt="" style="max-width:100%;">';
		}
		echo '</div>';

		// --- add and remove links ---
		echo '<p class="hide-if-no-js">';
		$hidden = '';
		if ( $has_show_header ) {
			$hidden = ' hidden';
		}
		echo '<a class="upload-custom-image' . esc_attr( $hidden ) . '" href="' . esc_url( $upload_link ) . '">';
		echo esc_html( __( 'Set Show Header Image' ) );
		echo '</a>';
		$hidden = '';
		if ( !$has_show_header ) {
			$hidden = ' hidden';
		}
		echo '<a class="delete-custom-image' . esc_attr( $hidden ) . '" href="#">';
		echo esc_html( __( 'Remove Show Header Image' ) );
		echo '</a>';
		echo '</p>';

		// --- hidden input for image ID ---
		echo '<input class="custom-image-id" name="show_header" type="hidden" value="' . esc_attr( $show_header ) . '">';

		echo '</div>';

	}

	// --- set images autosave nonce and iframe ---
	$images_autosave_nonce = wp_create_nonce( 'show-images-autosave' );
	echo '<input type="hidden" id="show-images-save-nonce" value="' . esc_attr( $images_autosave_nonce ) . '">';
	echo '<iframe src="javascript:void(0);" name="show-images-save-frame" id="show-images-save-frame" style="display:none;"></iframe>';

	// --- image selection script ---
	$confirm_remove = __( 'Are you sure you want to remove this image?', 'radio-station' );
	$js = "jQuery(function(){

		var mediaframe, parentdiv,
			imagesmetabox = jQuery('#radio-station-show-images-metabox'),
			addimagelink = imagesmetabox.find('.upload-custom-image'),
			deleteimagelink = imagesmetabox.find('.delete-custom-image');

		/* Add Image on Click */
		addimagelink.on( 'click', function( event ) {

			event.preventDefault();
			parentdiv = jQuery(this).parent().parent();

			if (mediaframe) {mediaframe.open(); return;}
			mediaframe = wp.media({
				title: 'Select or Upload Image',
				button: {text: 'Use this Image'},
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
				postid = '" . $post->ID . "'; imgid = attachment.id;
				if (parentdiv.attr('id') == 'show-avatar-image') {imagetype = 'avatar';}
				if (parentdiv.attr('id') == 'show-header-image') {imagetype = 'header';}
				imagessavenonce = jQuery('#show-images-save-nonce').val();
				framesrc = ajaxurl+'?action=radio_station_show_images_save';
				framesrc += '&post_id='+postid+'&image_type='+imagetype;
				framesrc += '&image_id='+imgid+'&_wpnonce='+imagessavenonce;
				jQuery('#show-images-save-frame').attr('src', framesrc);
			});

			mediaframe.open();
		});

		/* Delete Image on Click */
		deleteimagelink.on( 'click', function( event ) {
			event.preventDefault();
			agree = confirm('Are you sure?');
			if (!agree) {return;}
			parentdiv = jQuery(this).parent().parent();
			parentdiv.find('.custom-image-container').html('');
			parentdiv.find('.custom-image-id').val('');
			parentdiv.find('.upload-custom-image').removeClass('hidden');
			parentdiv.find('.delete-custom-image').addClass('hidden');
		});

	});";

	// --- enqueue script inline ---
	// 2.3.0: enqueue instead of echoing
	wp_add_inline_script( 'radio-station-admin', $js );

}

// ---------------------------------
// AJAX to AutoSave Images on Change
// ---------------------------------
add_action( 'wp_ajax_radio_station_show_images_save', 'radio_station_show_images_save' );
function radio_station_show_images_save() {

	if ( !current_user_can( 'edit_shows' ) ) {
		exit;
	}

	// --- verify nonce value ---
	if ( !isset( $_GET['_wpnonce'] ) || !wp_verify_nonce( $_GET['_wpnonce'], 'show-images-autosave' ) ) {
		exit;
	}

	// --- sanitize posted values ---
	if ( isset( $_GET['post_id'] ) ) {
		$post_id = absint( $_GET['post_id'] );
		if ( $post_id < 1 ) {
			unset( $post_id );
		}
	}
	// if ( !current_user_can( 'edit_show', $post_id ) ) {return;}

	if ( isset( $_GET['image_id'] ) ) {
		$image_id = absint( $_GET['image_id'] );
		if ( $image_id < 1 ) {
			unset( $image_id );
		}
	}
	if ( isset( $_GET['image_type'] ) ) {
		if ( in_array( $_GET['image_type'], array( 'header', 'avatar' ) ) ) {
			$image_type = $_GET['image_type'];
		}
	}

	if ( isset( $post_id ) && isset( $image_id ) && isset( $image_type ) ) {
		update_post_meta( $post_id, 'show_' . $image_type, $image_id );
	} else {
		exit;
	}

	// --- add image updated flag ---
	// (help prevent duplication on new posts)
	$updated = get_post_meta( $post_id, '_rs_image_updated', true );
	if ( !$updated ) {
		add_post_meta( $post_id, '_rs_image_updated', true );
	}

	// --- refresh parent frame nonce ---
	$images_save_nonce = wp_create_nonce( 'show-images-autosave' );
	echo "<script>parent.document.getElementById('show-images-save-nonce').value = '" . esc_js( $images_save_nonce ) . "';</script>";

	exit;
}

// --------------------
// Update Show Metadata
// --------------------
add_action( 'save_post', 'radio_station_show_save_data' );
function radio_station_show_save_data( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- set show meta changed flags ---
	$show_meta_changed = $show_shifts_changed = false;

	// --- get posted DJ / host list ---
	// 2.2.7: check DJ post value is set
	if ( isset( $_POST['show_hosts_nonce'] ) && wp_verify_nonce( $_POST['show_hosts_nonce'], 'radio-station' ) ) {

		if ( isset( $_POST['show_user_list'] ) ) {
			$hosts = $_POST['show_user_list'];
		}
		if ( !isset( $hosts ) || !is_array( $hosts ) ) {
			$hosts = array();
		} else {
			foreach ( $hosts as $i => $host ) {
				if ( !empty( $host ) ) {
					$userid = get_user_by( 'ID', $host );
					if ( !$userid ) {
						unset( $hosts[$i] );
					}
				}
			}
		}
		update_post_meta( $post_id, 'show_user_list', $hosts );
		$prev_hosts = get_post_meta( $post_id, 'show_user_list', true );
		if ( $prev_hosts != $hosts ) {
			$show_meta_changed = true;
		}
	}

	// --- get posted show producers ---
	// 2.3.0: added show producer sanitization
	if ( isset( $_POST['show_producers_nonce'] ) && wp_verify_nonce( $_POST['show_producers_nonce'], 'radio-station' ) ) {

		if ( isset( $_POST['show_producer_list'] ) ) {
			$producers = $_POST['show_producer_list'];
		}
		if ( !isset( $producers ) || !is_array( $producers ) ) {
			$producers = array();
		} else {
			foreach ( $producers as $i => $producer ) {
				if ( !empty( $producer ) ) {
					$userid = get_user_by( 'ID', $producer );
					if ( !$userid ) {
						unset( $producers[$i] );
					}
				}
			}
		}
		// 2.3.0: added save of show producers
		update_post_meta( $post_id, 'show_producer_list', $producers );
		$prev_producers = get_post_meta( $post_id, 'show_producer_list', true );
		if ( $prev_producers != $producers ) {
			$show_meta_changed = true;
		}
	}

	// --- save show meta data ---
	// 2.3.0: added separate nonce check for show meta
	if ( isset( $_POST['show_meta_nonce'] ) && wp_verify_nonce( $_POST['show_meta_nonce'], 'radio-station' ) ) {

		// --- get the meta data to be saved ---
		// 2.2.3: added show metadata value sanitization
		$file = wp_strip_all_tags( trim( $_POST['show_file'] ) );
		$email = sanitize_email( trim( $_POST['show_email'] ) );
		$active = $_POST['show_active'];
		// 2.2.8: removed strict in_array checking
		if ( !in_array( $active, array( '', 'on' ) ) ) {
			$active = '';
		}
		$link = filter_var( trim( $_POST['show_link'] ), FILTER_SANITIZE_URL );
		$patreon_id = sanitize_title( $_POST['show_patreon'] );

		// --- get existing values and check if changed ---
		// 2.3.0: added check against previous values
		$prev_file = get_post_meta( $post_id, 'show_file', true );
		$prev_email = get_post_meta( $post_id, 'show_email', true );
		$prev_active = get_post_meta( $post_id, 'show_active', true );
		$prev_link = get_post_meta( $post_id, 'show_link', true );
		$prev_patreon_id = get_post_meta( $post_id, 'show_patreont', true );
		if ( ( $prev_file != $file ) || ( $prev_email != $email )
		     || ( $prev_active != $active ) || ( $prev_link != $link )
		     || ( $prev_patreon_id != $patreon_id ) ) {
			$show_meta_changed = true;
		}

		// --- update the show metadata ---
		update_post_meta( $post_id, 'show_file', $file );
		update_post_meta( $post_id, 'show_email', $email );
		update_post_meta( $post_id, 'show_active', $active );
		update_post_meta( $post_id, 'show_link', $link );
		update_post_meta( $post_id, 'show_patreon', $patreon_id );
	}


	// --- update the show images ---
	if ( isset( $_POST['show_images_nonce'] ) && wp_verify_nonce( $_POST['show_images_nonce'], 'radio-station' ) ) {

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
		$avatar = absint( $_POST['show_avatar'] );
		if ( $avatar > 0 ) {
			// $prev_avatar = get_post_meta( $post_id, 'show_avatar', true );
			// if ( $avatar != $prev_avatar ) {$show_meta_changed = true;}
			update_post_meta( $post_id, 'show_avatar', $avatar );
		}

		// --- add image updated flag ---
		// (to prevent duplication for new posts)
		$updated = get_post_meta( $post_id, '_rs_image_updated', true );
		if ( !$updated ) {
			add_post_meta( $post_id, '_rs_image_updated', true );
		}
	}

	// --- check show shift nonce ---
	if ( isset( $_POST['show_shifts_nonce'] ) && wp_verify_nonce( $_POST['show_shifts_nonce'], 'radio-station' ) ) {

		// --- loop posted show shift times ---
		$new_shifts = array();
		$shifts = $_POST['show_sched'];
		$prev_shifts = get_post_meta( $post_id, 'show_sched', true );
		$days = array( '', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
		if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
			foreach ( $shifts as $i => $shift ) {

				// --- reset shift disabled flag ---
				// 2.3.0: added shift disabling logic
				$disabled = false;

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
					$conflicts = radio_station_check_shift( $post_id, $new_shifts[$i], 'shows' );
					if ( $conflicts ) {
						$disabled = true;
					}
				}

				// --- disable if incomplete data or shift conflicts ---
				if ( $disabled ) {
					$new_shifts[$i]['disabled'] = 'yes';
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
	}

	// --- maybe clear transient data ---
	// 2.3.0: added to clear transients if any meta has changed
	if ( $show_meta_changed || $show_shifts_changed ) {
		delete_transient( 'radio_station_current_schedule' );
		delete_transient( 'radio_station_current_show' );
		delete_transient( 'radio_station_next_show' );
		do_action( 'radio_station_clear_data', 'show', $post_id );
		do_action( 'radio_station_clear_data', 'show_meta', $post_id );
	}

}

// ---------------------
// Add Show List Columns
// ---------------------
// 2.2.7: added data columns to show list display
add_filter( 'manage_edit-' . RADIO_STATION_SHOW_SLUG . '_columns', 'radio_station_show_columns', 6 );
function radio_station_show_columns( $columns ) {

	if ( isset( $columns['thumbnail'] ) ) {
		unset( $columns['thumbnail'] );
	}
	if ( isset( $columns['post_thumb'] ) ) {
		unset( $columns['post_thumb'] );
	}

	$date = $columns['date'];
	unset( $columns['date'] );
	$comments = $columns['comments'];
	unset( $columns['comments'] );
	$genres = $columns['taxonomy-' . RADIO_STATION_GENRES_SLUG];
	unset( $columns['taxonomy-' . RADIO_STATION_GENRES_SLUG] );
	$languages = $columns['taxonomy-' . RADIO_STATION_LANGUAGES_SLUG];
	unset( $columns['taxonomy-' . RADIO_STATION_LANGUAGES_SLUG] );

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
		if ( !$content || ( trim( $content ) == '' ) ) {
			echo '<b>' . esc_html( __( 'No', 'radio-station' ) ) . '</b>';
		} else {
			echo esc_html( __( 'Yes', 'radio-station' ) );
		}
	} elseif ( 'shifts' == $column ) {
		$active = get_post_meta( $post_id, 'show_active', true );
		if ( 'on' == $active ) {
			$active = true;
		}
		$shifts = get_post_meta( $post_id, 'show_sched', true );
		if ( $shifts && ( count( $shifts ) > 0 ) ) {
			foreach ( $shifts as $shift ) {
				$timestamp = strtotime( 'next ' . $shift['day'] . ' ' . $shift['start_hour'] . ":" . $shift['start_min'] . " " . $shift['start_meridian'] );
				$sortedshifts[$timestamp] = $shift;
			}
			ksort( $sortedshifts );

			foreach ( $sortedshifts as $shift ) {

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

				echo "<div class='" . esc_attr( $classlist ) . "' title='" . esc_attr( $title ) . "'>";

				// --- get shift start and end times ---
				$start = $shift['start_hour'] . ":" . $shift['start_min'] . $shift['start_meridian'];
				$end = $shift['end_hour'] . ":" . $shift['end_min'] . $shift['end_meridian'];
				$start_time = strtotime( 'next ' . $shift['day'] . ' ' . $start );
				$end_time = strtotime( 'next' . $shift['day'] . ' ' . $end );

				// --- make weekday filter selections bold ---
				if ( isset( $_GET['weekday'] ) ) {
					$weekday = trim( $_GET['weekday'] );
				}
				$nextday = radio_station_get_next_day( $weekday );
				// 2.3.0: handle shifts that go overnight for weekday filter
				if ( ( $weekday == $shift['day'] ) || ( ( $shift['day'] == $nextday ) && ( $end_time < $start_time ) ) ) {
					echo "<b>";
					$bold = true;
				} else {
					$bold = false;
				}

				echo esc_html( radio_station_translate_weekday( $shift['day'] ) );
				echo " " . esc_html( $start ) . " - " . esc_html( $end );
				if ( $bold ) {
					echo "</b>";
				}
				echo "</div>";
			}
		}
	} elseif ( 'hosts' == $column ) {
		$hosts = get_post_meta( $post_id, 'show_user_list', true );
		if ( $hosts && ( count( $hosts ) > 0 ) ) {
			foreach ( $hosts as $host ) {
				$user_info = get_userdata( $host );
				echo esc_html( $user_info->display_name ) . "<br>";
			}
		}
	} elseif ( 'producers' == $column ) {
		// 2.3.0: added column for Producers
		$producers = get_post_meta( $post_id, 'show_producer_list', true );
		if ( $producers && ( count( $producers ) > 0 ) ) {
			foreach ( $producers as $producer ) {
				$user_info = get_userdata( $producer );
				echo esc_html( $user_info->display_name ) . "<br>";
			}
		}
	} elseif ( 'show_image' == $column ) {
		// 2.3.0: get show avatar (with fallback to thumbnail)
		$image_url = radio_station_get_show_avatar_url( $post_id );
		if ( $image_url ) {
			echo "<div class='show-image'><img src='" . esc_url( $image_url ) . "' alt='" . esc_html( __( 'Show Avatar', 'radio-station' ) ) . "'></div>";
		}
	}
}

// -----------------------
// Show List Column Styles
// -----------------------
// 2.2.7: added show column styles
add_action( 'admin_footer', 'radio_station_show_column_styles' );
function radio_station_show_column_styles() {
	$currentscreen = get_current_screen();
	if ( 'edit-' . RADIO_STATION_SHOW_SLUG !== $currentscreen->id ) {
		return;
	}

	echo "<style>#shifts {width: 200px;} #active, #description, #comments {width: 50px;}
	.show-image {width: 100px;} .show-image img {width: 100%; height: auto;}
	.show-shift.disabled {border: 1px dashed orange;} 
	.show-shift.conflict {border: 1px solid red;}
	.show-shift.disabled.conflict {border: 1px dashed red;}</style>";
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
	$d = isset( $_GET['weekday'] ) ? $_GET['weekday'] : 0;

	// --- show day selector ---
	$days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );

	echo '<label for="filter-by-show-day" class="screen-reader-text">' . esc_html( __( 'Filter by show day', 'radio-station' ) ) . '</label>';
	echo '<select name="weekday" id="filter-by-show-day">';
	echo '<option value="0" ' . selected( $d, 0, false ) . '>' . esc_html( __( 'All show days', 'radio-station' ) ) . '</option>';

	foreach ( $days as $day ) {
		$label = esc_attr( radio_station_translate_weekday( $day ) );
		echo '<option value="' . esc_attr( $day ) . '" ' . selected( $d, $day, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select>';
}


// --------------------------
// === Schedule Overrides ===
// --------------------------

// -----------------------------
// Add Schedule Override Metabox
// -----------------------------
// --- Add schedule override box to override edit screens ---
add_action( 'add_meta_boxes', 'radio_station_add_override_schedule_box' );
function radio_station_add_override_schedule_box() {
	// 2.2.2: add high priority to show at top of edit screen
	// 2.3.0: set position to top to be above editor box
	add_meta_box(
		'dynamicSchedOver_sectionid',
		__( 'Override Schedule', 'radio-station' ),
		'radio_station_master_override_schedule_metabox',
		RADIO_STATION_OVERRIDE_SLUG,
		'top', // shift to top
		'high'
	);
}

// -------------------------
// Schedule Override Metabox
// -------------------------
function radio_station_master_override_schedule_metabox() {

	global $post;

	// 2.2.7: added meridiem translations
	$am = radio_station_translate_meridiem( 'am' );
	$pm = radio_station_translate_meridiem( 'pm' );

	// --- add nonce field for update verification ---
	wp_nonce_field( 'radio-station', 'show_override_nonce' );

	// 2.2.7: add explicit width to date picker field to ensure date is visible
	// 2.3.0: convert template style output to straight php output
	echo '<div id="meta_inner" class="sched-override">';

	// --- get the saved meta as an array ---
	$override = get_post_meta( $post->ID, 'show_override_sched', false );
	if ( $override ) {
		$override = $override[0];
	} else {
		// 2.2.8: fix undefined index warnings for new schedule overrides
		$override = array(
			'date'           => '',
			'start_hour'     => '',
			'start_min'      => '',
			'start_meridian' => '',
			'end_hour'       => '',
			'end_min'        => '',
			'end_meridian'   => ''
		);
	}

	echo '<ul style="list-style:none;">';

	echo '<li style="display:inline-block;">';
	echo esc_html( __( 'Date', 'radio-station' ) ) . ':';
	if ( !empty( $override['date'] ) ) {
		$date = trim( $override['date'] );
	} else {
		$date = '';
	}
	echo '<input type="text" id="OverrideDate" style="width:200px; text-align:center;" name="show_sched[date]" value="' . esc_attr( $date ) . '">';
	echo '</li>';

	echo '<li style="display:inline-block; margin-left:20px;">';
	echo esc_html( __( 'Start Time', 'radio-station' ) ) . ':';
	echo '<select name="show_sched[start_hour]" style="min-width:35px;">';
	echo '<option value=""></option>';
	for ( $i = 1; $i <= 12; $i ++ ) {
		echo '<option value="' . esc_attr( $i ) . '" ' . selected( $override['start_hour'], $i, false ) . '>' . esc_html( $i ) . '</option>';
	}
	echo '</select>';
	echo '<select name="show_sched[start_min]" style="min-width:35px;">';
	echo '<option value=""></option>';
	for ( $i = 0; $i < 60; $i ++ ) {
		$min = $i;
		if ( $i < 10 ) {
			$min = '0' . $i;
		}
		echo '<option value="' . esc_attr( $min ) . '" ' . selected( $override['start_min'], $min, false ) . '>' . esc_html( $min ) . '</option>';
	}
	echo '</select>';
	echo '<select name="show_sched[start_meridian]" style="min-width:35px;">';
	echo '<option value=""></option>';
	echo '<option value="am" ' . selected( $override['start_meridian'], 'am', false ) . '>' . esc_html( $am ) . '</option>';
	echo '<option value="pm" ' . selected( $override['start_meridian'], 'pm', false ) . '>' . esc_html( $pm ) . '</option>';
	echo '</select>';
	echo '</li>';

	echo '<li style="display:inline-block; margin-left:20px;">';
	echo esc_html( __( 'End Time', 'radio-station' ) ) . ':';
	echo '<select name="show_sched[end_hour]" style="min-width:35px;">';
	echo '<option value=""></option>';
	for ( $i = 1; $i <= 12; $i ++ ) {
		echo '<option value="' . esc_attr( $i ) . '" ' . selected( $override['end_hour'], $i, false ) . '>' . esc_html( $i ) . '</option>';
	}
	echo '</select>';
	echo '<select name="show_sched[end_min]" style="min-width:35px;">';
	echo '<option value=""></option>';
	for ( $i = 0; $i < 60; $i ++ ) {
		$min = $i;
		if ( $i < 10 ) {
			$min = '0' . $i;
		}
		echo '<option value="' . esc_attr( $min ) . '"' . selected( $override['end_min'], $min, false ) . '>' . esc_html( $min ) . '</option>';
	}
	echo '</select>';
	echo '<select name="show_sched[end_meridian]" style="min-width:35px;">';
	echo '<option value=""></option>';
	echo '<option value="am" ' . selected( $override['end_meridian'], 'am', false ) . '>' . esc_html( $am ) . '</option>';
	echo '<option value="pm" ' . selected( $override['end_meridian'], 'pm', false ) . '>' . esc_html( $pm ) . '</option>';

	echo '</select>';
	echo '</li>';
	echo '</ul>';
	echo '</div>';

	// --- datepicker z-index style fix ---
	// 2.3.0: added for display conflict with editor buttons
	echo "<style>body.post-type-override #ui-datepicker-div {z-index: 1001 !important;}</style>";

	// --- enqueue inline script ---
	// 2.3.0: enqeue instead of echoing
	$js = "jQuery(document).ready(function() {
		jQuery('#OverrideDate').datepicker({dateFormat : 'yy-mm-dd'});
	});";
	wp_add_inline_script( 'radio-station-admin', $js );

}

// ------------------------
// Update Schedule Override
// ------------------------
add_action( 'save_post', 'radio_station_master_override_save_showpostdata' );
function radio_station_master_override_save_showpostdata( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- verify this came from the our screen and with proper authorization ---
	if ( !isset( $_POST['show_override_nonce'] ) || !wp_verify_nonce( $_POST['show_override_nonce'], 'radio-station' ) ) {
		return;
	}

	// --- get the show override data ---
	$sched = $_POST['show_sched'];
	if ( !is_array( $sched ) ) {
		return;
	}

	// --- get/set current schedule for merging ---
	// 2.2.2: added to set default keys
	$current_sched = get_post_meta( $post_id, 'show_override_sched', true );
	if ( !$current_sched || !is_array( $current_sched ) ) {
		$current_sched = array(
			'date'           => '',
			'start_hour'     => '',
			'start_min'      => '',
			'start_meridian' => '',
			'end_hour'       => '',
			'end_min'        => '',
			'end_meridian'   => '',
		);
	}

	// --- sanitize values before saving ---
	// 2.2.2: loop and validate schedule override values
	$changed = false;
	foreach ( $sched as $key => $value ) {
		$isvalid = false;

		// --- validate according to key ---
		if ( 'date' === $key ) {
			// check posted date format (yyyy-mm-dd) with checkdate (month, date, year)
			$parts = explode( '-', $value );
			if ( checkdate( $parts[1], $parts[2], $parts[0] ) ) {
				$isvalid = true;
			}
		} elseif ( ( 'start_hour' === $key ) || ( 'end_hour' === $key ) ) {
			if ( empty( $value ) ) {
				$isvalid = true;
			} elseif ( ( absint( $value ) > 0 ) && ( absint( $value ) < 13 ) ) {
				$isvalid = true;
			}
		} elseif ( ( 'start_min' === $key ) || ( 'end_min' === $key ) ) {
			// 2.2.3: fix to validate 00 minute value
			if ( empty( $value ) ) {
				$isvalid = true;
			} elseif ( absint( $value ) > - 1 && absint( $value ) < 61 ) {
				$isvalid = true;
			}
		} elseif ( ( 'start_meridian' === $key ) || ( 'end_meridian' === $key ) ) {
			$valid = array( '', 'am', 'pm' );
			// 2.2.8: remove strict in_array checking
			if ( in_array( $value, $valid ) ) {
				$isvalid = true;
			}
		}

		// --- if valid add to current schedule setting ---
		if ( $isvalid && ( $value !== $current_sched[$key] ) ) {
			$current_sched[$key] = $value;
			$changed = true;

			// 2.2.7: sync separate meta key for override date
			// (could be used to improve column sorting efficiency)
			if ( 'date' == $key ) {
				update_post_meta( $post_id, 'show_override_date', $value );
			}
		}
	}

	// --- save schedule setting if changed ---
	// 2.3.0: check if changed before saving
	if ( $changed ) {
		update_post_meta( $post_id, 'show_override_sched', $current_sched );

		// --- clear cached schedule data if changed ---
		delete_transient( 'radio_station_current_schedule' );
		delete_transient( 'radio_station_current_show' );
		delete_transient( 'radio_station_next_show' );
	}
}

// ----------------------------------
// Add Schedule Override List Columns
// ----------------------------------
// 2.2.7: added data columns to override list display
add_filter( 'manage_edit-' . RADIO_STATION_OVERRIDE_SLUG . '_columns', 'radio_station_override_columns', 6 );
function radio_station_override_columns( $columns ) {

	if ( isset( $columns['thumbnail'] ) ) {
		unset( $columns['thumbnail'] );
	}
	if ( isset( $columns['post_thumb'] ) ) {
		unset( $columns['post_thumb'] );
	}
	$date = $columns['date'];
	unset( $columns['date'] );

	$columns['override_date'] = esc_attr( __( 'Override Date', 'radio-station' ) );
	$columns['start_time'] = esc_attr( __( 'Start Time', 'radio-station' ) );
	$columns['end_time'] = esc_attr( __( 'End Time', 'radio-station' ) );
	$columns['shows_affected'] = esc_attr( __( 'Affected Show(s) on Date', 'radio-station' ) );
	// 2.3.0: added description indicator column
	$columns['description'] = esc_attr( __( 'Description', 'radio-station' ) );
	$columns['override_image'] = esc_attr( __( 'Override Image' ) );
	$columns['date'] = $date;

	return $columns;
}

// -----------------------------
// Schedule Override Column Data
// -----------------------------
// 2.2.7: added data columns for override list display
add_action( 'manage_' . RADIO_STATION_OVERRIDE_SLUG . '_posts_custom_column', 'radio_station_override_column_data', 5, 2 );
function radio_station_override_column_data( $column, $post_id ) {

	global $radio_station_show_shifts;

	$override = get_post_meta( $post_id, 'show_override_sched', true );
	if ( 'override_date' == $column ) {
		$datetime = strtotime( $override['date'] );
		$month = date( 'F', $datetime );
		$month = radio_station_translate_month( $month );
		$weekday = date( 'l', $datetime );
		$weekday = radio_station_translate_weekday( $weekday );
		echo esc_html( $weekday ) . ' ' . esc_html( date( 'j', $datetime ) ) . ' ' . esc_html( $month ) . ' ' . esc_html( date( 'Y', $datetime ) );
	} elseif ( 'start_time' == $column ) {
		echo esc_html( $override['start_hour'] ) . ':' . esc_html( $override['start_min'] ) . ' ' . esc_html( $override['start_meridian'] );
	} elseif ( 'end_time' == $column ) {
		echo esc_html( $override['end_hour'] ) . ':' . esc_html( $override['end_min'] ) . ' ' . esc_html( $override['end_meridian'] );
	} elseif ( 'shows_affected' == $column ) {

		// --- maybe get all show shifts ---
		if ( isset( $radio_station_show_shifts ) ) {
			$show_shifts = $radio_station_show_shifts;
		} else {
			global $wpdb;
			$query = "SELECT posts.post_title, meta.post_id, meta.meta_value FROM " . $wpdb->prefix . "postmeta} AS meta
				JOIN " . $wpdb->prefix . "posts as posts ON posts.ID = meta.post_id
				WHERE meta.meta_key = 'show_sched' AND posts.post_status = 'publish'";
			// 2.3.0: get results as an array
			$show_shifts = $wpdb->get_results( $query, ARRAY_A );
			$radio_station_show_shifts = $show_shifts;
		}
		if ( !$show_shifts || ( count( $show_shifts ) == 0 ) ) {
			return;
		}

		// --- get the override weekday and convert to 24 hour time ---
		$datetime = strtotime( $override['date'] );
		$weekday = date( 'l', $datetime );

		// --- get start and end override times ---
		$override_start = strtotime( $override['date'] . ' ' . $override['start_hour'] . ':' . $override['start_min'] . ' ' . $override['start_meridian'] );
		$override_end = strtotime( $override['date'] . ' ' . $override['end_hour'] . ':' . $override['end_min'] . ' ' . $override['end_meridian'] );
		// (if the end time is less than start time, adjust end to next day)
		if ( $override_end <= $override_start ) {
			$override_end = $override_end + 86400;
		}

		// --- loop show shifts ---
		foreach ( $show_shifts as $show_shift ) {
			$shift = maybe_unserialize( $show_shift['meta_value'] );
			if ( !is_array( $shift ) ) {
				$shift = array();
			}

			foreach ( $shift as $time ) {
				if ( isset( $time['day'] ) && ( $time['day'] == $weekday ) ) {

					// --- get start and end shift times ---
					// 2.3.0: validate shift time to check if complete
					$time = radio_station_validate_shift( $time );
					$shift_start = strtotime( $override['date'] . ' ' . $time['start_hour'] . ':' . $time['start_min'] . ' ' . $time['start_meridian'] );
					$shift_end = strtotime( $override['date'] . ' ' . $time['end_hour'] . ':' . $time['end_min'] . ' ' . $time['end_meridian'] );
					if ( ( $shift_start == $shift_end ) || ( $shift_start > $shift_end ) ) {
						$shift_end = $shift_end + 86400;
					}

					// --- compare override time overlaps to get affected shows ---
					if ( ( ( $override_start < $shift_start ) && ( $override_end > $shift_end ) )
					     || ( ( $override_start >= $shift_start ) && ( $override_end < $shift_end ) ) ) {
						// 2.3.0: adjust cell display to two line (to allow for long show titles)
						$active = get_post_meta( $show_shift['post_id'], 'show_active', true );
						if ( 'on' != $active ) {
							echo "[<i>" . esc_html( __( 'Inactive Show', 'radio-station' ) ) . "</i>] ";
						}
						echo $show_shift['post_title'] . "<br>";
						if ( $time['disabled'] ) {
							echo "[<i>" . esc_html( __( 'Disabled Shift', 'radio-station' ) ) . "</i>] ";
						}
						echo radio_station_translate_weekday( $time['day'] );
						echo " " . esc_html( $time['start_hour'] ) . ":" . esc_html( $time['start_min'] ) . esc_html( $time['start_meridian'] );
						echo " - " . esc_html( $time['end_hour'] ) . ":" . esc_html( $time['end_min'] ) . esc_html( $time['end_meridian'] );
						echo "<br>";
					}
				}
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
	} elseif ( 'override_image' == $column ) {
		$thumbnail_url = radio_station_get_show_avatar_url( $post_id );
		if ( $thumbnail_url ) {
			echo "<div class='override_image'><img src='" . esc_url( $thumbnail_url ) . "' alt='" . esc_attr( __( 'Override Logo', 'radio-station' ) ) . "'></div>";
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
add_action( 'admin_footer', 'radio_station_override_column_styles' );
function radio_station_override_column_styles() {
	$currentscreen = get_current_screen();
	if ( 'edit-' . RADIO_STATION_OVERRIDE_SLUG !== $currentscreen->id ) {
		return;
	}
	echo "<style>#shows_affected {width: 300px;} #start_time, #end_time {width: 65px;}
	.override_image {width: 100px;} .override_image img {width: 100%; height: auto;}</style>";
}

// ----------------------------------
// Add Schedule Override Month Filter
// ----------------------------------
// 2.2.7: added month selection filtering
add_action( 'restrict_manage_posts', 'radio_station_override_date_filter', 10, 2 );
function radio_station_override_date_filter( $post_type, $which ) {

	global $wp_locale;
	if ( RADIO_STATION_OVERRIDE_SLUG !== $post_type ) {
		return;
	}

	// --- get all show override months / years ---
	global $wpdb;
	$overridequery = "SELECT ID FROM " . $wpdb->posts . " WHERE post_type = '" . RADIO_STATION_OVERRIDE_SLUG . "'";
	$results = $wpdb->get_results( $overridequery, ARRAY_A );
	$months = array();
	if ( $results && ( count( $results ) > 0 ) ) {
		foreach ( $results as $result ) {
			$post_id = $result['ID'];
			$override = get_post_meta( $post_id, 'show_override_date', true );
			$datetime = strtotime( $override );
			$month = date( 'm', $datetime );
			$year = date( 'Y', $datetime );
			$months[$year . $month]['year'] = $year;
			$months[$year . $month]['month'] = $month;
		}
	} else {
		return;
	}

	// --- maybe get specified month ---
	// TODO: maybe use get_query_var for month ?
	$m = isset( $_GET['month'] ) ? (int) $_GET['month'] : 0;

	// --- month override selector ---
	echo '<label for="filter-by-override-date" class="screen-reader-text">' . esc_html( __( 'Filter by override date', 'radio-station' ) ) . '</label>';
	echo '<select name="month" id="filter-by-override-date">';
	echo '<option value="0" ' . selected( $m, 0, false ) . '>' . esc_html( __( 'All override dates', 'radio-station' ) ) . '</option>';
	if ( count( $months ) > 0 ) {
		foreach ( $months as $key => $data ) {
			$label = $wp_locale->get_month( $data['month'] ) . ' ' . $data['year'];
			echo "<option value='" . esc_attr( $key ) . "' " . selected( $m, $key, false ) . ">" . esc_html( $label ) . "</option>\n";
		}
	}
	echo '</select>';

}


// -----------------------------------
// === Post Type List Query Filter ===
// -----------------------------------
// 2.2.7: added filter for custom column sorting
add_action( 'pre_get_posts', 'radio_station_columns_query_filter' );
function radio_station_columns_query_filter( $query ) {
	if ( !is_admin() || !$query->is_main_query() ) {
		return;
	}

	// --- Shows by Shift Days Filtering ---
	if ( RADIO_STATION_SHOW_SLUG === $query->get( 'post_type' ) ) {

		// --- check if day filter is seta ---
		// TODO: maybe use get_query_var for weekday ?
		if ( isset( $_GET['weekday'] ) && ( '0' != $_GET['weekday'] ) ) {

			$weekday = $_GET['weekday'];

			// need to loop and sync a separate meta key to enable filtering
			// (not really efficient but at least it makes it possible!)
			// ...but could be improved by checking against postmeta table
			// 2.3.0: cache all show posts query result for efficiency
			global $radio_station_data;
			if ( isset( $radio_station_data['all-shows'] ) ) {
				$results = $radio_station_data['all-shows'];
			} else {
				global $wpdb;
				$showquery = "SELECT ID FROM " . $wpdb->posts . " WHERE post_type = '" . RADIO_STATION_SHOW_SLUG . "'";
				$results = $wpdb->get_results( $showquery, ARRAY_A );
				$radio_station_data['all-shows'] = $results;
			}
			if ( $results && ( count( $results ) > 0 ) ) {
				foreach ( $results as $result ) {
					$post_id = $result['ID'];
					$shifts = get_post_meta( $post_id, 'show_sched', true );

					if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
						$shiftdays = array();
						$shiftstart = $prevtime = false;
						foreach ( $shifts as $shift ) {
							if ( $shift['day'] == $weekday ) {
								// 2.3.0: replace old with new 24 hour conversion
								// $shiftstart = $shifttime['start_hour'] . ':' . $shifttime['start_min'] . ":00";
								// $shiftstart = radio_station_convert_schedule_to_24hour( $shift );
								$shiftstart = $shift['start_hour'] . ':' . $shift['start_min'] . $shift['start_meridian'];
								$shifttime = strtotime( $weekday . ' ' . $shiftstart );
								// 2.3.0: check for earliest shift for that day
								if ( !$prevtime || ( $shifttime < $prevtime ) ) {
									$shiftstart = radio_station_convert_shift_time( $shiftstart, 24 ) . ':00';
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

			// --- set the meta query for filtering ---
			// this is not working?! but does not need to as using orderby fixes it
			// $meta_query = array(
			//	'key'       => 'show_shift_time',
			//	'compare'   => 'EXISTS',
			// );
			// $query->set( 'meta_query', $meta_query );

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
			global $wpdb;
			$overridequery = "SELECT ID FROM " . $wpdb->posts . " WHERE post_type = '" . RADIO_STATION_OVERRIDE_SLUG . "'";
			$results = $wpdb->get_results( $overridequery, ARRAY_A );
			if ( $results && ( count( $results ) > 0 ) ) {
				foreach ( $results as $result ) {
					$post_id = $result['ID'];
					$override = get_post_meta( $post_id, 'show_override_sched', true );
					if ( $override ) {
						update_post_meta( $post_id, 'show_override_date', $override['date'] );
					} else {
						delete_post_meta( $post_id, 'show_override_data' );
					}
				}
			}

			// --- now we can set the orderby meta query to the synced key ---
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'show_override_date' );
			$query->set( 'meta_type', 'date' );

			// --- apply override year/month filtering ---
			if ( isset( $_GET['month'] ) && ( '0' != $_GET['month'] ) ) {
				$yearmonth = $_GET['month'];
				$start_date = date( $yearmonth . '01' );
				$end_date = date( $yearmonth . 't' );
				$meta_query = array(
					'key'     => 'show_override_date',
					'value'   => array( $start_date, $end_date ),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				);
				$query->set( 'meta_query', $meta_query );
			}

		}
	}
}

