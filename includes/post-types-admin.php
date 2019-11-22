<?php

/*
 * Admin Post Types Metaboxes and Post Lists
 * Author: Nikki Blight
 * Since: 2.2.7
 */

// === Metabox Positions ===
// - Metaboxes Above Content Area
// - Move Genre Metabox on Shows
// - Add Language Metabox
// - Language Selection Metabox
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

// ---------------------------
// Move Genre Metabox on Shows
// ---------------------------
// 2.3.0: move inside show meta selection to reduce clutter
add_action( 'add_meta_boxes_show', 'radio_station_move_genre_meta_box' );
function radio_station_move_genre_meta_box() {
	global $wp_meta_boxes;
	$genres = $wp_meta_boxes['show']['side']['core']['genresdiv'];
	unset( $wp_meta_boxes['show']['side']['core']['genresdiv'] );
	$wp_meta_boxes['show']['side']['high']['genresdiv'] = $genres;
}

// --------------------
// Add Language Metabox
// --------------------
// 2.3.0: add language selection metabox
add_action( 'add_meta_boxes', 'radio_station_add_show_language_metabox' );
function radio_station_add_show_language_metabox() {
	add_meta_box(
		'languagesdiv',
		__( 'Show Language', 'radio-station' ),
		'radio_station_show_language_metabox',
		array( 'show', 'override' ),
		'side',
		'high'
	);
}

// --------------------------
// Language Selection Metabox
// --------------------------
function radio_station_show_language_metabox() {

	// --- use same noncename as default box so no save_post hook needed ---
	wp_nonce_field( 'taxonomy_languages', 'taxonomy_noncename' );

	// --- get terms associated with this post ---
	$terms = wp_get_object_terms( get_the_ID(), 'languages' );

	// --- get all language options ---
	$languages = radio_station_get_languages();

	echo '<div style="margin-bottom: 5px;">';

		$main_language = radio_station_get_language();
		echo '<b>' . __( 'Main Radio Language', 'radio-station' ) . '</b>:<br>';
		foreach ( $languages as $i => $language ) {
			if ( $main_language == $language['language'] ) {
				$label = $language['native_name'];
				if ( $language['native_name'] != $language['english_name'] ) {
					$label .= ' (' . $language['english_name'] . ')';
				}
			}
		}
		echo $label . '<br>';
		echo '<div style="font-size:11px;">' . __( 'Select below if Show language(s) differ.', 'radio-station' ) . '</div>';

	
		echo '<ul id="languages_taxradiolist" data-wp-lists="list:languages_tax" class="categorychecklist form-no-clear">';

		// --- loop existing terms ---
		$term_names = array();
		foreach ( $terms as $term ) {

			$name = $term->name;
			$term_names[] = $name;
			foreach ( $languages as $i => $language ) {
				if ( $name == $language['language'] ) {
					$label = $language['native_name'];
					if ( $language['native_name'] != $language['english_name'] ) {
						$label .= ' (' . $language['english_name'] . ')';
					}
				}
			}
		
			echo '<li id="languages_tax-' . $name . '">';
			
				// --- hidden input for term saving ---
				echo '<input value="'. $name .'" type="hidden" name="tax_input[languages][]" id="in-languages_tax-'. $name .'">';
				
				// --- language term label ---
				echo '<label>' . $label . '</label>';

				// --- remove term button ---
				echo '<input type="button" class="button button-secondary" onclick="radio_remove_language(\'' . $name . '\');" value="x" title="' . __( 'Remove Language', 'radio-station' ) . '">';
				
			echo '</li>';
		}
		echo '</ul>';
		
		// --- new language selection list ---
		echo '<select id="rs-add-language-selection" onchange="radio_add_language();">';
		echo '<option selected="selected">' . __( 'Select Language', 'radio-station' ) . '</option>';
		foreach ( $languages as $i => $language ) {
			$code = $language['language'];
			echo '<option value="' . $code . '"';
				if ( in_array( $code, $term_names ) ) {echo ' disabled="disabled"';}
				echo '>' . $language['native_name'];
				if ( $language['native_name'] != $language['english_name'] ) {
					echo  ' (' . $language['english_name'] . ')';
				}
			echo '</option>';
		}
		echo '</select><br>';
		
		// --- add language term button ---
		echo '<div style="font-size:11px;">' . __( 'Click on a Language to Add it.', 'radio-station' ) . '</div>';
		
	echo '</div>';
	
	// --- language selection javascript ---
	echo "<script>function radio_add_language() {
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
		listitem.setAttribute('id', 'languages_tax-'+optionvalue);
		input = document.createElement('input');
		input.value = optionvalue;
		input.setAttribute('type', 'hidden');
		input.setAttribute('name', 'tax_input[languages][]');
		input.setAttribute('id', 'in-languages_tax-'+optionvalue);
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
		document.getElementById('languages_taxradiolist').appendChild(listitem);
	}
	function radio_remove_language(term) {
		/* remove item from term list */
		listitem = document.getElementById('languages_tax-'+term);
		listitem.parentNode.removeChild(listitem);

		/* re-enable language select option */
		select = document.getElementById('rs-add-language-selection');
		options = select.options; 
		for (i = 0; i < options.length; i++) {
			if (options[i].value == term) {
				options[i].removeAttribute('disabled');
			}
		}
	}</script>";
	
	// --- language input style fixes ---
	echo "<style>#languages_taxradiolist input.button {
		margin-left: 10px; padding: 0 7px; color: #E00; border-radius: 7px;
	}</style>";
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
		'playlist',
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

	// --- get the saved meta as an arry ---
	$entries = get_post_meta( $post->ID, 'playlist', false );
	$c = 1;

	echo '<div id="meta_inner">';

	echo '<table id="here" class="widefat">';
	echo '<tr>';
	echo '<th></th><th><b>' . esc_html__( 'Artist', 'radio-station' ) . '</b></th>';
	echo '<th><b>' . esc_html__( 'Song', 'radio-station' ) . '</b></th>';
	echo '<th><b>' . esc_html__( 'Album', 'radio-station' ) . '</b></th>';
	echo '<th><b>' . esc_html__( 'Record Label', 'radio-station' ) . '</th>';
	// echo "<th><b>".__('DJ Comments', 'radio-station')."</b></th>";
	// echo "<th><b>".__('New', 'radio-station')."</b></th>";
	// echo "<th><b>".__('Status', 'radio-station')."</b></th>";
	// echo "<th><b>".__('Remove', 'radio-station')."</b></th>";
	echo '</tr>';

	if ( isset( $entries[0] ) && ! empty( $entries[0] ) ) {

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

				echo '<td>' . esc_html__( 'New', 'radio-station' ) . ' ';
				$track['playlist_entry_new'] = isset( $track['playlist_entry_new'] ) ? $track['playlist_entry_new'] : false;
				echo '<input type="checkbox" name="playlist[' . esc_attr( $c ) . '][playlist_entry_new]" ' . checked( $track['playlist_entry_new'] ) . ' />';

				echo ' ' . esc_html__( 'Status', 'radio-station' ) . ' ';
				echo '<select name="playlist[' . esc_attr( $c ) . '][playlist_entry_status]">';

					echo '<option value="queued" ' . selected( $track['playlist_entry_status'], 'queued' ) . '>' . esc_html__( 'Queued', 'radio-station' ) . '</option>';

					echo '<option value="played" ' . selected( $track['playlist_entry_status'], 'played' ) . '>' . esc_html__( 'Played', 'radio-station' ) . '</option>';

				echo '</select></td>';

				echo '<td align="right"><span id="track-' . esc_attr( $c ) . '" class="remove button-secondary" style="cursor: pointer;">' . esc_html__( 'Remove', 'radio-station' ) . '</span></td>';
				echo '</tr>';
				$c++;
			}
		}
	}
	echo '</table>';

	?>
	<a class="add button-primary" style="cursor: pointer; float: right; margin-top: 5px;"><?php echo esc_html__( 'Add Entry', 'radio-station' ); ?></a>
	<div style="clear: both;"></div>
	<script>
		var shiftadda = jQuery.noConflict();
		shiftadda(document).ready(function() {
			var count = <?php echo esc_attr( $c ); ?>;
			shiftadda('.add').click(function() {

				output = '<tr id="track-'+count+'-rowa"><td>'+count+'</td>';
					output += '<td><input type="text" name="playlist['+count+'][playlist_entry_artist]" value="" /></td>';
					output += '<td><input type="text" name="playlist['+count+'][playlist_entry_song]" value="" /></td>';
					output += '<td><input type="text" name="playlist['+count+'][playlist_entry_album]" value="" /></td>';
					output += '<td><input type="text" name="playlist['+count+'][playlist_entry_label]" value="" /></td>';
				output += '</tr><tr id="track-'+count+'-rowb">';
					output += '<td colspan="3"><?php echo esc_html__( 'Comments', 'radio-station' ); ?>: <input type="text" name="playlist['+count+'][playlist_entry_comments]" value="" style="width:320px;"></td>';
					output += '<td><?php echo esc_html__( 'New', 'radio-station' ); ?>: <input type="checkbox" name="playlist['+count+'][playlist_entry_new]" />';
					output += ' <?php echo esc_html__( 'Status', 'radio-station' ); ?>: <select name="playlist['+count+'][playlist_entry_status]">';
						output += '<option value="queued"><?php esc_html_e( 'Queued', 'radio-station' ); ?></option>';
						output += '<option value="played"><?php esc_html_e( 'Played', 'radio-station' ); ?></option>';
					output += '</select></td>';
					output += '<td align="right"><span id="track-'+count+'" class="remove button-secondary" style="cursor: pointer;"><?php esc_html_e( 'Remove', 'radio-station' ); ?></span></td>';
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
		</script>
	</div>

	<div id="publishing-action-bottom">
		<br /><br />
		<?php
		$can_publish = current_user_can( 'publish_playlists' );
		// borrowed from wp-admin/includes/meta-boxes.php
		// 2.2.8: remove strict in_array checking
		if ( ! in_array( $post->post_status, array( 'publish', 'future', 'private' ) ) || 0 === $post->ID ) {
			if ( $can_publish ) :
				if ( ! empty( $post->post_date_gmt ) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) :
					?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Schedule', 'radio-station' ); ?>" />
					<?php
					submit_button(
						__( 'Schedule' ),
						'primary',
						'publish',
						false,
						array(
							'tabindex'  => '50',
							'accesskey' => 'o',
						)
					);
					?>
			<?php	else : ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'radio-station' ); ?>" />
				<?php
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
				?>
				<?php
		endif;
			else :
				?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Submit for Review', 'radio-station' ); ?>" />
				<?php
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
				?>
				<?php
			endif;
		} else {
			?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update', 'radio-station' ); ?>" />
				<input name="save" type="submit" class="button-primary" id="publish" tabindex="50" accesskey="o" value="<?php esc_attr_e( 'Update Playlist', 'radio-station' ); ?>" />
			<?php
		}
		?>
	</div>

	<?php
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
		'playlist',
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
		'numberposts' => -1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => 'show',
		'post_status' => 'publish',
	);
	$shows = get_posts( $args );
	if ( count( $shows ) > 0 ) {$have_shows = true;} else {$have_shows = false;}

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
			if ( !is_array( $user_list->meta_value ) ) {$user_list->meta_value = array();}

			// --- only include shows the user is assigned to ---
			foreach ( $user_list->meta_value as $user_id ) {
				if ( $user->ID === $user_id ) {
					$allowed_shows[] = $user_list->post_id;
				}
			}
		}

		$args = array(
			'numberposts' => -1,
			'offset'      => 0,
			'orderby'     => 'post_title',
			'order'       => 'aSC',
			'post_type'   => 'show',
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
			echo '<option value="" ' . selected( $current, false ) . '>' . esc_html__( 'Unassigned', 'radio-station' ) . '</option>';
			foreach ( $shows as $show ) {
				echo '<option value="' . esc_attr( $show->ID ) . '" ' . selected( $show->ID, $current ) . '>' . esc_html( $show->post_title ) . '</option>';
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
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {return;}

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
				if ( $prev_show ) {$show = $prev_show; $changed = true;}
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
				if ( function_exists( 'radio_station_pro_clear_data' ) ) {
					radio_station_pro_clear_data( 'show_meta', $show );					
				}
			}
		}
	}

}

// -------------------------
// Add Playlist List Columns
// -------------------------
// 2.2.7: added data columns to playlist list display
add_filter( 'manage_edit-playlist_columns', 'radio_station_playlist_columns', 6 );
function radio_station_playlist_columns( $columns ) {
	if ( isset( $columns['thumbnail'] ) ) {unset( $columns['thumbnail'] );}
	if ( isset( $columns['post_thumb'] ) ) {unset( $columns['post_thumb'] );}
	$date = $columns['date']; unset( $columns['date'] );
	$comments = $columns['comments']; unset( $columns['comments'] );
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
add_action( 'manage_playlist_posts_custom_column', 'radio_station_playlist_column_data', 5, 2 );
function radio_station_playlist_column_data( $column, $post_id ) {
	if ( $column == 'show' ) {
		$show_id = get_post_meta( $post_id, 'playlist_show_id', true );
		$post = get_post( $show_id );
		echo "<a href='" . get_edit_post_link( $post->ID ). "'>" . $post->post_title . "</a>";
	} elseif ( $column == 'trackcount' ) {
		$tracks = get_post_meta( $post_id, 'playlist', true );
		echo count( $tracks );
	} elseif ( $column == 'tracklist' ) {
		$tracks = get_post_meta( $post_id, 'playlist', true );
		$tracklist = '<a href="javascript:void(0);" onclick="showhidetracklist(\'' . $post_id . '\')">';
		$tracklist .= __( 'Show/Hide Tracklist', 'radio-station' ) . "</a><br>";
		$tracklist .= '<div id="tracklist-' . $post_id . '" style="display:none;">';
		$tracklist .= '<table class="tracklist-table" cellpadding="0" cellspacing="0">';
		$tracklist .= '<tr><td><b>#</b></td>';
		$tracklist .= '<td><b>' . __('Song', 'radio-station' ). '</b></td>';
		$tracklist .= '<td><b>' . __('Artist', 'radio-station' ) . '</b></td>';
		$tracklist .= '<td><b>' . __('Status', 'radio-station' ) . '</b></td></tr>';
		foreach ( $tracks as $i => $track ) {
			$tracklist .= '<tr><td>' . $i . '</td>';
			$tracklist .= '<td>' . $track['playlist_entry_song'] . '</td>';
			$tracklist .= '<td>' . $track['playlist_entry_artist'] . '</td>';
			$status = $track['playlist_entry_status'];
			$status = strtoupper( substr( $status, 0, 1 ) ) . substr( $status, 1, strlen( $status ) );
			$tracklist .= '</td><td>' . $status . '</td></tr>';
		}
		$tracklist .= '</table></div>';
		echo $tracklist;
	}
}

// ---------------------------
// Playlist List Column Styles
// ---------------------------
add_action( 'admin_footer', 'radio_station_playlist_column_styles' );
function radio_station_playlist_column_styles() {
	$currentscreen = get_current_screen();
	if ( $currentscreen->id !== 'edit-playlist' ) {return;}
	echo "<style>#show {width: 150px;} #trackcount {width: 50px;}
	#tracklist {width: 400px;} .tracklist-table td {padding: 0px 10px;}</style>";

	// --- expand/collapse tracklist data ---
	echo "<script>function showhidetracklist(postid) {
		if (document.getElementById('tracklist-'+postid).style.display == 'none') {
			document.getElementById('tracklist-'+postid).style.display = '';
		} else {document.getElementById('tracklist-'+postid).style.display = 'none';}
	}</script>";
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
	// TODO: add filter to plugin documentation ?
	// ... or make this a plugin admin option ?
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

	$args    = array(
		'numberposts' => -1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => 'show',
		'post_status' => 'publish',
	);
	$shows   = get_posts( $args );
	$current = get_post_meta( $post->ID, 'post_showblog_id', true );

	echo '<div id="meta_inner">';
	
		if ( count( $shows ) > 0 ) {
			// --- select related show input ---
			echo '<select name="post_showblog_id">';
				echo '<option value=""></option>';
				// --- loop shows for selection options ---
				foreach ( $shows as $show ) {
					echo '<option value="' . esc_attr( $show->ID ) . '" ' . selected( $show->ID, $current ) . '>' . esc_html( $show->post_title ) . '</option>';
				}
			echo '</select>';
		} else {
			// --- no shows message ---
			echo esc_html( __( '', 'radio-station' ) );
		}
	echo '</div>';
}

// -------------------
// Update Related Show
// -------------------
add_action( 'save_post', 'radio_station_post_save_data' );
function radio_station_post_save_data( $post_id ) {

	// --- do not save when doing autosaves ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {return;}

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
			if ( $prev_show ) {$changed = true;}
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
			if ( function_exists( 'radio_station_pro_clear_data' ) ) {
				radio_station_pro_clear_data( 'show_meta', $show );					
			}
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
		'show',
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
	$file   = get_post_meta( $post->ID, 'show_file', true );
	$email  = get_post_meta( $post->ID, 'show_email', true );
	$active = get_post_meta( $post->ID, 'show_active', true );
	$link   = get_post_meta( $post->ID, 'show_link', true );

	// added max-width to prevent metabox overflows
	// 2.3.0: removed new lines between labels and fields and changed widths
	?>
	<div id="meta_inner">

	<p><div style="width:100px; display:inline-block;"><label><?php esc_html_e( 'Active', 'radio-station' ); ?></label></div>
	<input type="checkbox" name="show_active" <?php checked( $active, 'on' ); ?> /> 
	<em><?php esc_html_e( 'Check this box if show is currently active (Show will not appear on programming schedule if unchecked.)', 'radio-station' ); ?></em></p>

	<p><div style="width:100px; display:inline-block;"><label><?php esc_html_e( 'Website Link', 'radio-station' ); ?>:</label></div> 
	<input type="text" name="show_link" size="100" style="max-width:80%;" value="<?php echo esc_url( $link ); ?>" /></p>

	<p><div style="width:100px; display:inline-block;"><label><?php esc_html_e( 'DJ / Host Email', 'radio-station' ); ?>:</label></div> 
	<input type="text" name="show_email" size="100" style="max-width:80%;" value="<?php echo esc_attr( $email ); ?>" /></p>

	<p><div style="width:100px; display:inline-block;"><label><?php esc_html_e( 'Latest Audio File', 'radio-station' ); ?>:</label></div> 
	<input type="text" name="show_file" size="100" style="max-width:80%;" value="<?php echo esc_attr( $file ); ?>" /></p>

	</div>
	<?php
	
	// --- inside show metaboxes ---	
	// 2.3.0: move metaboxes together inside meta 
	$inside_metaboxes = array(
		'hosts'		=> array(
			'title'		=> __( 'Show DJ(s) / Host(s)', 'radio-station' ),
			'callback'	=> 'radio_station_show_hosts_metabox',
		),
		'producers'	=> array(
			'title'		=> __( 'Show Producer(s)', 'radio-station' ),
			'callback'	=> 'radio_station_show_producers_metabox',
		),
		// 'language'	=> array(
		//	'title'		=> __( 'Show Language(s)', 'radio-station' ),
		//	'callback'	=> 'radio_station_show_language_metabox',
		// ),
	);
	echo '<div id="show-inside-metaboxes">';
	foreach ( $inside_metaboxes as $key => $metabox ) {
		echo '<div id="' . $key . '" class="postbox">' . "\n";
			$widget_title = $box['title'];
			echo '<button type="button" class="handlediv" aria-expanded="true">';
			echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $metabox['title'] ) . '</span>';
			echo '<span class="toggle-indicator" aria-hidden="true"></span>';
			echo '</button>';

			echo '<h2 class="hndle"><span>' . $metabox['title'] . '</span></h2>' . "\n";
			echo '<div class="inside">' . "\n";
				call_user_func( $metabox['callback'] );
			echo "</div>\n";
		echo "</div>\n";
	}
	echo '</div>';
	
	echo "<style>#show-inside-metaboxes .postbox {
		display: inline-block !important; max-width: 200px; vertical-align:top;}
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
		'show',
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
		'role__in'	=> array( 'dj', 'administrator' ),
		'orderby'	=> 'display_name',
		'order'		=> 'ASC'
	);
	$hosts = get_users( $args );

	// --- get the Hosts currently assigned to the show ---
	$current = get_post_meta( $post->ID, 'show_user_list', true );
	if ( !$current ) {$current = array();}

	// --- move any selected Hosts to the top of the list ---
	foreach ( $hosts as $i => $host ) {
		// 2.2.8: remove strict in_array checking
		if ( in_array( $host->ID, $current ) ) {
			unset( $hosts[ $i ] ); // unset first, or prepending will change the index numbers and cause you to delete the wrong item
			array_unshift( $hosts, $host );  // prepend the user to the beginning of the array
		}
	}

	// --- Host Selection Input ---
	// 2.2.2: add fix to make DJ multi-select input full metabox width
	?>
	<div id="meta_inner">

	<select name="show_user_list[]" multiple="multiple" style="height: 120px; width: 100%;">
		<option value=""></option>
	<?php
	foreach ( $hosts as $host ) {
		// 2.2.2: set DJ display name maybe with username
		$display_name = $host->display_name;
		if ( $host->display_name !== $host->user_login ) {
			$display_name .= ' (' . $host->user_login . ')';
		}
		// 2.2.7: fix to remove unnecessary third argument
		// 2.2.8: removed unnecessary fix for non-strict check
		if ( in_array( $host->ID, $current ) ) {$selected = ' selected="selected"';} else {$selected = '';}
		echo '<option value="' . esc_attr( $host->ID ) . '"' . $selected . '>' . esc_html( $display_name ) . '</option>';
	}
	?>
	</select>
	<?php // --- multiple selection helper text ---
	// 2.3.0: added multiple selection helper text
	echo '<div style="font-size: 10px;">' . __( 'Ctrl-Click selects multiple.','radio-station') . '</div>'; ?>
	</div>
	<?php
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
		'show',
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
		'role__in'	=> array( 'producer', 'administrator', 'show-editor' ),
		'orderby'	=> 'display_name',
		'order'		=> 'ASC'
	);
	$producers = get_users( $args );

	// --- get Producers currently assigned to the show ---
	$current = get_post_meta( $post->ID, 'show_producer_list', true );
	if ( !$current ) {$current = array();}

	// --- move any selected DJs to the top of the list ---
	foreach ( $producers as $i => $producer ) {
		if ( in_array( $producer->ID, $current ) ) {
			unset( $producers[ $i ] ); // unset first, or prepending will change the index numbers and cause you to delete the wrong item
			array_unshift( $producers, $producer ); // prepend the user to the beginning of the array
		}
	}

	// --- Producer Selection Input ---
	?>
	<div id="meta_inner">

	<select name="show_producer_list[]" multiple="multiple" style="height: 120px; width: 100%;">
		<option value=""></option>
	<?php
	foreach ( $producers as $producer ) {
		$display_name = $producer->display_name;
		if ( $producer->display_name !== $producer->user_login ) {
			$display_name .= ' (' . $producer->user_login . ')';
		}
		if ( in_array( $producer->ID, $current ) ) {$selected = ' selected="selected"';} else {$selected = '';}
		echo '<option value="' . esc_attr( $producer->ID ) . '"' . $selected . '>' . esc_html( $display_name ) . '</option>';
	}
	?>
	</select>
	<?php // --- multiple selection helper text ---
	echo '<div style="font-size: 10px;">' . __( 'Ctrl-Click selects multiple.','radio-station') . '</div>'; ?>
	</div>
	<?php
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
		'show',
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
		for ( $i = 1; $i <= 12; $i++ ) {$hours[$i] = $i;}
		for ( $i = 0; $i < 60; $i++ ) {
			if ( $i < 10 ) {$min = '0' . $i;} else {$min = $i;}
			$mins[$i] = $min;
		}		

		// --- get the saved meta as an array ---
		$shifts = get_post_meta( $post->ID, 'show_sched', true );
	
		$c = 0;
		if ( isset( $shifts) && is_array( $shifts ) && ( count ( $shifts ) > 0 ) ) {

			// 2.2.7: soft shifts by start day and time for ordered display
			foreach ( $shifts as $shift ) {
				// 2.3.0: add shift index to prevent start time overwriting
				$j = 1; 
				if ( isset( $shift['day'] ) && ( $shift['day'] != '' ) ) {
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
				$j++;
			}

			// --- sort day shifts and loop ---
			// TODO: sort order using start of week ?
			ksort( $day_shifts );
			$show_shifts = array();
			foreach ( $day_shifts as $i => $day_shift ) {
				// --- sort shifts by start time for each day ---
				ksort( $day_shift );
				foreach ( $day_shift as $shift ) {
					$show_shifts[] = $shift;
				}
			}

			// --- loop ordered show shifts ---
			$list = ''; $has_conflicts = false;
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
				if ( isset( $shift['disabled'] ) && ( $shift['disabled'] == 'yes' ) ) {
					$classes[] = 'disabled';
				}
				$classlist = implode( " ", $classes );

				$list .= '<ul class="' . esc_attr( $classlist ).'">';

					// --- shift day selection ---
					$list .= '<li class="first">';
						$list .= esc_html( __( 'Day', 'radio-station' ) ) . ': ';
						
						if ( $shift['day'] == '' ) {$class = 'incomplete';} else {$class = '';}
						$list .= '<select class="' . $class . '" name="show_sched[' . esc_attr( $c ) . '][day]">';
							// 2.3.0: simplify by looping days 
							foreach ( $days as $day ) {
								if ( $day == $shift['day'] ) {$selected = ' selected="selected"';} else {$selected = '';}
								// 2.3.0: add weekday translation to display
								$list .= '<option value="' . $day . '"' . $selected . '>';
								$list .= esc_html( radio_station_translate_weekday( $day ) ) . '</option>';
							}
						$list .= '</select>';
					$list .= '</li>';

					// --- shift start time ---
					$list .= '<li>';
						$list .= esc_html( __( 'Start Time', 'radio-station' ) ) . ': ';
						
						// --- start hour selection ---
						if ( $shift['start_hour'] == '' ) {$class = 'incomplete';} else {$class = '';}
						$list .= '<select class="' . $class . '" name="show_sched[' . esc_attr( $c ) . '][start_hour]" style="min-width:35px;">';
							foreach ( $hours as $hour ) {
								if ( $shift['start_hour'] == $hour ) {$selected = ' selected="selected"';} else {$selected = '';}
								$list .= '<option value="' . esc_attr( $hour ) . '"' . $selected . '>' . esc_html( $hour ) . '</option>';
							}
						$list .= '</select>';
						
						// --- start minute selection ---
						$list .= '<select name="show_sched[' . esc_attr( $c ) . '][start_min]" style="min-width:35px;">';
							$list .= '<option value=""></option>';
							foreach ( $mins as $min ) {
								if ( $shift['start_min'] == $min ) {$selected = ' selected="selected"';} else {$selected = '';}
								$list .= '<option value="' . esc_attr( $min ) . '"' . $selected . '>' . esc_html( $min ) . '</option>';
							}
						$list .= '</select>';
						
						// --- start meridiem selection ---
						if ( $shift['start_meridian'] == '' ) {$class = 'incomplete';} else {$class = '';}
						$list .= '<select class="' . $class . '" name="show_sched[' . esc_attr( $c ) . '][start_meridian]" style="min-width:35px;">';
							if ( 'am' == $shift['start_meridian'] ) {$selected = ' selected="selected"';} else {$selected = '';}
							$list .= '<option value="am"'.$selected.'>' . $am . '</option>';
							if ( 'pm' == $shift['start_meridian'] ) {$selected = ' selected="selected"';} else {$selected = '';}
							$list .= '<option value="pm"'.$selected.'>' . $pm . '</option>';
						$list .= '</select>';
					$list .= '</li>';

					// --- shift end time ---
					$list .= '<li>';
						$list .= esc_html( __( 'End Time', 'radio-station' ) ) . ': ';
						
						// --- end hour selection ---
						if ( $shift['end_hour'] == '' ) {$class = 'incomplete';} else {$class = '';}
						$list .= '<select class="' . $class . '" name="show_sched[' . esc_attr( $c ) . '][end_hour]" style="min-width:35px;">';
							foreach ( $hours as $hour ) {
								if ( $shift['end_hour'] == $hour ) {$selected = ' selected="selected"';} else {$selected = '';}
								$list .= '<option value="' . esc_attr( $hour ) . '"' . $selected . '>' . esc_html( $hour ) . '</option>';
							}
						$list .= '</select>';
						
						// --- end minute selection ---
						$list .= '<select name="show_sched[' . esc_attr( $c ) . '][end_min]" style="min-width:35px;">';
							foreach( $mins as $min ) {
								if ( $shift['end_min'] == $min ) {$selected = ' selected="selected"';} else {$selected = '';}
								$list .= '<option value="' . esc_attr( $min ) . '"' . $selected . '>' . esc_html( $min ) . '</option>';
							}
						$list .= '</select>';
						
						// --- end meridiem selection ---
						if ( $shift['end_meridian'] == '' ) {$class = 'incomplete';} else {$class = '';}
						$list .= '<select class="' . $class . '" name="show_sched[' . esc_attr( $c ) . '][end_meridian]" style="min-width:35px;">';
							if ( 'am' == $shift['end_meridian'] ) {$selected = ' selected="selected"';} else {$selected = '';}
							$list .= '<option value="am"'.$selected.'>' . $am . '</option>';
							if ( 'pm' == $shift['end_meridian'] ) {$selected = ' selected="selected"';} else {$selected = '';}
							$list .= '<option value="pm"'.$selected.'>' . $pm . '</option>';
						$list .= '</select>';
					$list .= '</li>';
					
					// --- encore presentation ---
					// 2.3.0: check encore flag value explicitly
					if ( isset( $shift['encore'] ) && ( $shift['encore'] == 'on' ) ) {
						$checked = " checked";
					} else {$checked = '';}
					$list .= '<li>';
						$list .= '<input type="checkbox" value="on" name="show_sched[' . esc_attr( $c ) . '][encore]"' . $checked . '>';
						$list .= esc_html( __( 'Encore', 'radio-station' ) );
					$list .= '</li>';

					// --- shift disabled ---
					// 2.3.0: added disabled checkbox to shift row
					if ( isset( $shift['disabled'] ) && ( $shift['disabled'] == 'yes' ) ) {
						$checked = " checked";
					} else {$checked = '';}
					$list .= '<li>';
						$list .= '<input type="checkbox" value="yes" name="show_sched[' . esc_attr( $c ) . '][disabled]"' . $checked .'>';
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
						$list .= '<b>' . esc_html( __('Shift Conflicts', 'radio-station' ) ) . '</b>: ';
						foreach ( $conflicts as $i => $conflict ) {
							if ( $i > 0 ) {$list .= ', ';}
							if ( $conflict['show'] == $post->ID ) {
								$list .= '<i>This Show</i>';
							} else {
								$show_edit_link = add_query_arg( 'post', $conflict['show'], $edit_link );
								$show_title = get_the_title( $conflict['show'] );
								$list .= '<a href="' . $show_edit_link . '">' . $show_title . '</a>';
							}
							$conflict_start = $conflict['shift']['start_hour'] . ':' . $conflict['shift']['start_min'] . ' ' . $conflict['shift']['start_meridian'];
							$conflict_end = $conflict['shift']['end_hour'] . ':' . $conflict['shift']['end_min'] . ' ' . $conflict['shift']['end_meridian'];
							$list .= ' - ' . $conflict['shift']['day'] . ' ' . $conflict_start . ' - ' . $conflict_end;
						}
					$list .= '</div><br>';
				}

				// --- increment shift counter ---
				$c++;
			}
		}

		// --- shift conflicts message ---
		// 2.3.0: added instructions for fixing shift conflicts
		if ( $has_conflicts ) {
			echo '<div class="shift-conflicts-message">';
			echo '<b style="color:#EE0000;">' . esc_html( __( 'Warning! Show Shift Conflicts were detected!', 'radio-station') ) . '</b><br>';
			echo esc_html( __( 'Please note that Shifts with conflicts are automatically disabled upon saving.', 'radio-station' ) ) . '<br>';
			echo esc_html( __( 'Fix the Shift and/or the Shift on the conflicting Show and Update them both.', 'radio-station' ) ) . '<br>';
			echo esc_html( __( 'Then you can uncheck the shift Disable box and Update to re-enable the Shift.', 'radio-station' ) ) . '<br>';
			// TODO: add more information blog post / documentation link ?
			echo '</div><br>';
		}
		
		// --- output shift list ---
		echo $list;

	?>
	<span id="here"></span>
	<span style="text-align: center;"><a class="add button-primary" style="cursor: pointer; display:block; width: 150px; padding: 8px; text-align: center; line-height: 1em;"><?php echo esc_html__( 'Add Shift', 'radio-station' ); ?></a></span>
	<script>
		var shiftaddb =jQuery.noConflict();
		shiftaddb(document).ready(function() {
			var count = <?php echo esc_attr( $c ); ?>;
			shiftaddb(".add").click(function() {
				count = count + 1;
				output = '<ul class="show-shift">';
					output += '<li class="first">';
						output += '<?php esc_html_e( 'Day', 'radio-station' ); ?>: ';
						output += '<select name="show_sched[' + count + '][day]">';
							<?php // 2.3.0: simplify by looping days and add translation
							foreach ( $days as $day ) { ?>
							output += '<option value="<?php echo $day; ?>">';
							output += '<?php echo esc_html( radio_station_translate_weekday( $day ) ); ?></option>';
							<?php } ?>
						output += '</select>';
					output += '</li>';

					output += '<li>';
						output += '<?php esc_html_e( 'Start Time', 'radio-station' ); ?>: ';

						output += '<select name="show_sched[' + count + '][start_hour]" style="min-width:35px;">';
							<?php foreach ( $hours as $hour ) { ?>
							output += '<option value="<?php echo esc_attr( $hour ); ?>"><?php echo esc_html( $hour ); ?></option>';
							<?php } ?>
						output += '</select> ';
						output += '<select name="show_sched[' + count + '][start_min]" style="min-width:35px;">';
							<?php foreach ( $mins as $min ) { ?>
							output += '<option value="<?php echo esc_attr( $min ); ?>"><?php echo esc_html( $min ); ?></option>';
							<?php } ?>
						output += '</select> ';
						output += '<select name="show_sched[' + count + '][start_meridian]" style="min-width:35px;">';
							output += '<option value="am"><?php echo $am; ?></option>';
							output += '<option value="pm"><?php echo $pm; ?></option>';
						output += '</select> ';
					output += '</li>';

					output += '<li>';
						output += '<?php esc_html_e( 'End Time', 'radio-station' ); ?>: ';
						output += '<select name="show_sched[' + count + '][end_hour]" style="min-width:35px;">';
							<?php foreach ( $hours as $hour ) { ?>
							output += '<option value="<?php echo esc_attr( $hour ); ?>"><?php echo esc_html( $hour ); ?></option>';
							<?php } ?>
						output += '</select> ';
						output += '<select name="show_sched[' + count + '][end_min]" style="min-width:35px;">';
							<?php foreach ( $mins as $min ) { ?>
							output += '<option value="<?php echo esc_attr( $min ); ?>"><?php echo esc_html( $min ); ?></option>';
							<?php } ?>
						output += '</select> ';
						output += '<select name="show_sched[' + count + '][end_meridian]" style="min-width:35px;">';
							output += '<option value="am"><?php echo $am; ?></option>';
							output += '<option value="pm"><?php echo $pm; ?></option>';
						output += '</select> ';
					output += '</li>';

					output += '<li>';
						output += '<input type="checkbox" value="on" name="show_sched[' + count + '][encore]" /> <?php esc_html_e( 'Encore', 'radio-station' ); ?>';
					output += '</li>';

					output += '<li>';
						output += '<input type="checkbox" value="yes" name="show_sched[' + count + '][disabled]" /> <?php esc_html_e( 'Disabled', 'radio-station' ); ?>';
					output += '</li>';

					output += '<li class="last">';
						output += '<span class="remove button button-secondary" style="cursor: pointer;"><?php esc_html_e( 'Remove', 'radio-station' ); ?></span>';
					output += '</li>';

				output += '</ul>';
				shiftaddb('#here').append( output );

				return false;
			});
			shiftaddb(".remove").live('click', function() {
				/* ? recheck shift count ? */
				/* ? confirmation check message ? */
				shiftaddb(this).parent().parent().remove();
			});
		});
		</script>
	
		<style>.show-shift {list-style: none; margin-bottom: 10px; border: 2px solid green;}
		.show-shift li {display: inline-block; vertical-align: middle; margin-left: 20px;
			margin-top: 10px; margin-bottom: 10px;}
		.show-shift li.first {margin-left: 10px;}
		.show-shift li.last {margin-right: 10px;}
		.show-shift.disabled {border: 2px dashed orange;}
		.show-shift.conflicts {outline: 2px solid red;}
		.show-shift.disabled.conflicts {border: 2px dashed red; outline: none;}
		.show-shift select.incomplete {border: 2px solid orange;}</style>
		
	<?php
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
		'show',
		'top',
		'low'
	);
}

// -------------------------------
// Show Description Helper Metabox
// -------------------------------
// 2.3.0: added metabox for show description helper text
function radio_station_show_helper_box() {

	// --- show description helper text ---
	$helper = esc_html( __( "The text field below is for your Show Description. It will display on the About tab of your Show page.", 'radio-station' ) ) . "<br>";
	$helper .= esc_html( __( "It is not recommended to edit the page and post your past show content or archives in this area, as it will affect the Show page layout your visitors see.", 'radio-station' ) );
	$helper .= ' ' . esc_html( __( "It may also impact SEO, as archived content won't have their own pages and thus their own SEO and Social Meta rules.", 'radio-station' ) ) . "<br>";
	$helper .= esc_html( __( "We recommend using WordPress Posts to add new posts and assign them to your Show(s) using the Related Show metabox in the Post Edit screen so they display on the Show page.", 'radio-station' ) );
	$helper .= ' ' . esc_html( __( "You can then assign them to a relevent Post Category for display on your site.", 'radio-station' ) );

	// TODO: upgrade to Pro for show episodes blurb
	// $upgrade_url = radio_station_get_upgrade_url();
	// $helper .= '<a href="' . $upgrade_url . '">';
		// $helper .= esc_html( __( "Upgrade to Radio Station Pro', 'radio-station' ) );
	// $helper .= '</a>';
	
	echo "<p>".$helper."</p>";

}

// ----------------------------------
// Rename Show Featured Image Metabox
// ----------------------------------
// 2.3.0: renamed from "Feature Image" to be clearer
// 2.3.0: removed this as now implementing show images separately
// (note this is the Show Logo for backwards compatibility reasons)
// add_action( 'do_meta_boxes', 'radio_station_rename_featured_image_metabox' );
function radio_station_rename_featured_image_metabox() {
    remove_meta_box( 'postimagediv', 'show', 'side' );
    add_meta_box('postimagediv', __('Show Logo'), 'post_thumbnail_meta_box', 'show', 'side', 'low');
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
		'show',
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

	if ( isset( $_GET['avatar_refix'] ) && ( $_GET['avatar_refix'] == 'yes' ) ) {
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
				echo '<img src="' . $show_avatar_src[0] . '" alt="" style="max-width:100%;">';
			}
		echo '</div>';

		// --- add and remove links ---
		echo '<p class="hide-if-no-js">';
			if ( $has_show_avatar  ) {$hidden = ' hidden'; } else {$hidden = '';}
			echo '<a class="upload-custom-image' . $hidden . '" href="' . esc_url( $upload_link ) . '">';
				echo esc_html( __( 'Set Show Avatar Image' ) );
			echo '</a>';
			if ( !$has_show_avatar  ) {$hidden = ' hidden';} else {$hidden = '';}
			echo '<a class="delete-custom-image' . $hidden .'" href="#">';
				echo esc_html( __( 'Remove Show Avatar Image' ) );
			echo '</a>';
		echo '</p>';

		// --- hidden input for image ID ---
		echo '<input class="custom-image-id" name="show_avatar" type="hidden" value="' . esc_attr( $show_avatar ) .'">';
		
	echo '</div>';

	// --- get show header image info
	$show_header = get_post_meta( $post->ID, 'show_header', true );
	$show_header_src = wp_get_attachment_image_src( $show_header, 'full' );
	$has_show_header = is_array( $show_header_src );

	// --- show header image ---
	echo '<div id="show-header-image">';

		// --- image container ---
		echo '<div class="custom-image-container">';
			if ( $has_show_header ) {
				echo '<img src="' . $show_header_src[0] . '" alt="" style="max-width:100%;">';
			}
		echo '</div>';

		// --- add and remove links ---
		echo '<p class="hide-if-no-js">';
			if ( $has_show_header  ) {$hidden = ' hidden';} else {$hidden = '';}
			echo '<a class="upload-custom-image' . $hidden . '" href="' . esc_url( $upload_link ) .'">';
				echo esc_html( __( 'Set Show Header Image' ) ); 
			echo '</a>';
			if ( !$has_show_header  ) {$hidden = ' hidden';} else {$hidden = '';}
			echo '<a class="delete-custom-image' . $hidden . '" href="#">';
				echo esc_html( __( 'Remove Show Header Image' ) );
			echo '</a>';
		echo '</p>';

		// --- hidden input for image ID ---
		echo '<input class="custom-image-id" name="show_header" type="hidden" value="' . esc_attr( $show_header ) . '">';
		
	echo '</div>';

	// --- set images autosave nonce and iframe ---
	$images_autosave_nonce = wp_create_nonce( 'show-images-autosave' );
	echo '<input type="hidden" id="show-images-save-nonce" value="' . $images_autosave_nonce . '">';
	echo '<iframe src="javascript:void(0);" name="show-images-save-frame" id="show-images-save-frame" style="display:none;"></iframe>';

// --- image selection script ---
?>
<script>
jQuery(function(){

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
			image = '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>';
			parentdiv.find('.custom-image-container').append(image);
			parentdiv.find('.custom-image-id').val(attachment.id);
			parentdiv.find('.upload-custom-image').addClass('hidden');
			parentdiv.find('.delete-custom-image').removeClass('hidden');

			/* auto-save image via AJAX */
			postid = '<?php echo $post->ID; ?>'; imgid = attachment.id;
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
  
});</script>
<?php

}

// ---------------------------------
// AJAX to AutoSave Images on Change
// ---------------------------------
add_action( 'wp_ajax_radio_station_show_images_save', 'radio_station_show_images_save');
function radio_station_show_images_save() {

	if ( !current_user_can( 'edit_shows' ) ) {exit;}

	// --- verify nonce value ---
	if ( !isset( $_GET['_wpnonce'] ) 
	  || !wp_verify_nonce( $_GET['_wpnonce'], 'show-images-autosave' ) ) {
	 	exit;
	}

	// --- sanitize posted values ---
	if ( isset( $_GET['post_id'] ) ) {
		$post_id = absint( $_GET['post_id'] );
		if ( $post_id < 1 ) {unset( $post_id );}
	}
	// if ( !current_user_can( 'edit_show', $post_id ) ) {return;}
	
	if ( isset( $_GET['image_id'] ) ) {
		$image_id = absint( $_GET['image_id'] );
		if ( $image_id < 1 ) {unset( $image_id );}
	}
	if ( isset( $_GET['image_type'] ) ) {
		if ( in_array( $_GET['image_type'], array( 'header', 'avatar' ) ) ) {
			$image_type = $_GET['image_type'];
		}
	}
	
	if ( isset( $post_id ) && isset( $image_id ) && isset( $image_type ) ) {
		update_post_meta( $post_id, 'show_' . $image_type, $image_id );
	} else {return;}

	// --- add image updated flag ---
	// (help prevent duplication on new posts)
	$updated = get_post_meta( $post_id, '_rs_image_updated', true );
	if ( !$updated ) {add_post_meta( $post_id, '_rs_image_updated', true );}

	// --- refresh parent frame nonce ---
	$images_save_nonce = wp_create_nonce( 'show-images-autosave' );
	echo "<script>parent.document.getElementById('show-images-save-nonce').value = '" . $images_save_nonce . "';</script>";

	exit;
}

// --------------------
// Update Show Metadata
// --------------------
add_action( 'save_post', 'radio_station_show_save_data' );
function radio_station_show_save_data( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {return;}

	// --- set show meta changed flags ---
	$show_meta_changed = $show_shifts_changed = false;

	// --- get posted DJ / host list ---
	// 2.2.7: check DJ post value is set
	if ( isset( $_POST['show_hosts_nonce'] ) 
	  && wp_verify_nonce( $_POST['show_hosts_nonce'], 'radio-station' ) ) {

		if ( isset( $_POST['show_user_list'] ) ) {$hosts = $_POST['show_user_list'];}
		if ( !isset( $hosts ) || !is_array( $hosts ) ) {
			$hosts = array();
		} else {
			foreach ( $hosts as $i => $host ) {
				if ( !empty( $host ) ) {
					$userid = get_user_by( 'ID', $host );
					if ( !$userid ) {unset( $hosts[$i] );}
				}
			}
		}
		update_post_meta( $post_id, 'show_user_list', $hosts );
		$prev_hosts = get_post_meta( $post_id, 'show_user_list', true );
		if ( $prev_hosts != $hosts ) {$show_meta_changed = true;}
	}

	// --- get posted show producers ---
	// 2.3.0: added show producer sanitization
	if ( isset( $_POST['show_producers_nonce'] ) 
	  && wp_verify_nonce( $_POST['show_producers_nonce'], 'radio-station' ) ) {
	
		if ( isset( $_POST['show_producer_list'] ) ) {$producers = $_POST['show_producer_list'];}
		if ( !isset( $producers ) || !is_array( $producers ) ) {
			$producers = array();
		} else {
			foreach ( $producers as $i => $producer ) {
				if ( !empty( $producer ) ) {
					$userid = get_user_by( 'ID', $producer );
					if ( !$userid ) {unset( $producers[$i] );}
				}
			}
		}
		// 2.3.0: added save of show producers
		update_post_meta( $post_id, 'show_producer_list', $producers );
		$prev_producers = get_post_meta( $post_id, 'show_producer_list', true );
		if ( $prev_producers != $producers ) {$show_meta_changed = true;}
	}


	// --- save show meta data ---
	// 2.3.0: added separate nonce check for show meta
	if ( isset( $_POST['show_meta_nonce'] )
	  && wp_verify_nonce( $_POST['show_meta_nonce'], 'radio-station' ) ) {

		// --- get the meta data to be saved ---
		// 2.2.3: added show metadata value sanitization
		$file   = wp_strip_all_tags( trim( $_POST['show_file'] ) );
		$email  = sanitize_email( trim( $_POST['show_email'] ) );
		$active = $_POST['show_active'];
		// 2.2.8: removed strict in_array checking
		if ( !in_array( $active, array( '', 'on' ) ) ) {$active = '';}
		$link = filter_var( trim( $_POST['show_link'] ), FILTER_SANITIZE_URL );

		// --- update the show metadata ---
		update_post_meta( $post_id, 'show_file', $file );
		update_post_meta( $post_id, 'show_email', $email );
		update_post_meta( $post_id, 'show_active', $active );
		update_post_meta( $post_id, 'show_link', $link );

		// --- get existing values and check if changed ---
		// 2.3.0: added check against previous values
		$prev_file = get_post_meta( $post_id, 'show_file', true );
		$prev_email = get_post_meta( $post_id, 'show_email', true );
		$prev_active = get_post_meta( $post_id, 'show_active', true );
		$prev_link = get_post_meta( $post_id, 'show_link', true );
		if ( ( $prev_file != $file ) || ( $prev_email != $email )
		  || ( $prev_active != $active ) || ( $prev_link != $link ) ) {$show_meta_changed = true;}
	
	}


	// --- update the show images ---
	if ( isset( $_POST['show_images_nonce'] ) 
	  && wp_verify_nonce( $_POST['show_images_nonce'], 'radio-station' ) ) {

		// --- show header image ---
		$header = absint( $_POST['show_header'] );
		if ( $header > 0 ) {
			// $prev_header = get_post_meta( $post_id, 'show_header', true );
			// if ( $header != $prev_header ) {$show_meta_changed = true;}
			update_post_meta( $post_id, 'show_header', $header );
		}
		
		// --- show avatar image ---
		$avatar = absint( $_POST['show_avatar'] );
		if ( $avatar > 0 ) {
			// $prev_avatar = get_post_meta( $post_id, 'show_avatar', true );
			// if ( $avatar != $prev_avatar ) {$show_meta_changed = true;}
			update_post_meta( $post_id, 'show_avatar', $avatar );
		}

		// --- add image updated flag ---
		// (help prevent duplication for new posts)
		$updated = get_post_meta( $post_id, '_rs_image_updated', true );
		if ( !$updated ) {add_post_meta( $post_id, '_rs_image_updated', true );}
	}

	// --- check show shift nonce ---
	if ( isset( $_POST['show_shifts_nonce'] )
	  && wp_verify_nonce( $_POST['show_shifts_nonce'], 'radio-station' ) ) {

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
						if ( in_array( $value, $days ) ) {$isvalid = true;}
						if ( $value == '' ) {
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
							$isvalid = true; $value = '00';
						} elseif ( ( absint( $value ) > -1 ) && ( absint( $value ) < 61 ) ) {
							$isvalid = true;
						} else {$disabled = true;}

					} elseif ( ( 'start_meridian' === $key ) || ( 'end_meridian' === $key ) ) {

						// --- check shift meridiem ---
						$valid = array( '', 'am', 'pm' );
						// 2.2.8: remove strict in_array checking
						if ( in_array( $value, $valid ) ) {$isvalid = true;}
						if ( $value == '' ) {$disabled = true;}

					} elseif ( 'encore' === $key ) {

						// --- check shift encore switch ---
						// 2.2.4: fix to missing encore sanitization saving
						$valid = array( '', 'on' );
						// 2.2.8: remove strict in_array checking
						if ( in_array( $value, $valid ) ) {$isvalid = true;}

					} elseif ( 'disabled' == $key ) {

						// --- check shift disabled switch ---
						// 2.3.0: added shift disable switch
						// note: overridden on incomplete data or shift conflict
						$valid = array( '', 'yes' );
						if ( in_array( $value, $valid ) ) {$isvalid = true;}
						if ( $value == 'yes' ) {$disabled = true;}

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
					if ( $conflicts ) {$disabled = true;}
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
		}
	}

	// --- maybe clear transient data ---
	// 2.3.0: added to clear transients if any meta has changed
	if ( $show_meta_changed || $show_shifts_changed ) {
		delete_transient( 'radio_station_current_schedule' );
		delete_transient( 'radio_station_current_show' );
		delete_transient( 'radio_station_next_show' );
		if ( function_exists( 'radio_station_pro_clear_data' ) ) {
			radio_station_pro_clear_data( 'show', $post_id );
			radio_station_pro_clear_data( 'show_meta', $post_id );	
		}
	}

}

// ---------------------
// Add Show List Columns
// ---------------------
// 2.2.7: added data columns to show list display
add_filter( 'manage_edit-show_columns', 'radio_station_show_columns', 6 );
function radio_station_show_columns( $columns ) {
	if ( isset( $columns['thumbnail'] ) ) {unset( $columns['thumbnail'] );}
	if ( isset( $columns['post_thumb']) ) {unset( $columns['post_thumb'] );}
	$date = $columns['date']; unset( $columns['date'] );
	$comments = $columns['comments']; unset( $columns['comments'] );
	$genres = $columns['taxonomy-genres']; unset( $columns['taxonomy-genres'] );
	$columns['active'] = esc_attr( __( 'Active?', 'radio-station' ) );
	// 2.3.0: added show description indicator column
	$columns['description'] = esc_attr( __( 'About?', 'radio-station' ) );
	$columns['shifts'] = esc_attr( __( 'Shifts', 'radio-station' ) );
	// 2.3.0: change DJs column label to Hosts
	$columns['hosts'] = esc_attr( __( 'Hosts', 'radio-station' ) );
	$columns['show_image'] = esc_attr( __( 'Show Avatar', 'radio-station' ) );
	$columns['taxonomy-genres'] = $genres;
	$columns['comments'] = $comments;
	$columns['date'] = $date;
	return $columns;
}

// ---------------------
// Show List Column Data
// ---------------------
// 2.2.7: added data columns for show list display
add_action( 'manage_show_posts_custom_column', 'radio_station_show_column_data', 5, 2 );
function radio_station_show_column_data( $column, $post_id ) {
	if ( $column == 'active' ) {
		$active = get_post_meta( $post_id, 'show_active', true );
		if ( $active == 'on') {echo __( 'Yes', 'radio-station' );}
		else {echo __( 'No', 'radio-station' );}
	} elseif ( $column == 'description' ) {
		// 2.3.0: added show description indicator
		global $wpdb;
		$query = "SELECT post_content FROM ".$wpdb->prefix."posts WHERE ID = %d";
		$query = $wpdb->prepare( $query, $post_id );
		$content = $wpdb->get_var( $query );
		if ( !$content || ( trim( $content ) == '' ) ) {
			echo '<b>' . __( 'No', 'radio-station' ) . '</b>';
		} else {
			echo __( 'Yes', 'radio-station' );
		}
	} elseif ( $column == 'shifts' ) {
		$shifts = get_post_meta( $post_id, 'show_sched', true );
		if ( $shifts && ( count( $shifts ) > 0 ) ) {
			foreach ( $shifts as $shift ) {
				$timestamp = strtotime( 'next ' . $shift['day'] . ' ' . $shift['start_hour'] . ":" . $shift['start_min'] . " " . $shift['start_meridian'] );
				$sortedshifts[$timestamp] = $shift;
			}
			ksort( $sortedshifts );
			foreach ( $sortedshifts as $shift ) {
				
				// 2.3.0: highlight disabled and conflicting shifts
				$classes = array( 'show-shift' );
				$disabled = false; $title = '';
				if ( isset( $shift['disabled'] ) && ( $shift['disabled'] == 'yes' ) ) {
					$disabled = true; 
					$classes[] = 'disabled';
					$title = __( 'This Shift is Disabled.', 'radio-station' );
				}
				$conflicts = radio_station_check_shift( $post_id, $shift );
				if ( $conflicts ) {
					$classes[] = 'conflict';
					if ( $disabled ) {$title = __( 'This Shift has Schedule Conflicts and is Disabled.', 'radio-station' );}
					else {$title = __( 'This Shift has Schedule Conflicts.', 'radio-station' );}
				}
				$classlist = implode( ' ', $classes );
				
				echo "<div class='" . $classlist . "' title='" . $title . "'>";
				
					// --- get shift start and end times ---
					$start = $shift['start_hour'] . ":" . $shift['start_min'] . $shift['start_meridian'];
					$end = $shift['end_hour'] . ":" . $shift['end_min'] . $shift['end_meridian'];
					$start_time = strtotime( 'next ' . $day . ' ' . $start );
					$end_time = strtotime( 'next' . $day . ' ' . $end );

					// --- make weekday filter selections bold ---
					if ( isset( $_GET['weekday'] ) ) 
						$weekday = trim( $_GET['weekday'] );
						$nextday = radio_station_get_next_day( $weekday );
						// 2.3.0: handle shifts that go overnight for weekday filter
						if ( ( $weekday == $shift['day'] ) 
						  || ( ( $shift['day'] == $nextday ) && ( $end_time < $start_time ) ) ) {
						echo "<b>"; $bold = true;
					} else {$bold = false;}
					
					echo radio_station_translate_weekday( $shift['day'] );
					echo " " . $start . " - " . $end;
					if ( $bold ) {echo "</b>";}
				echo "</div>";
			}
		}
	} elseif ( $column == 'hosts') {
		$hosts = get_post_meta( $post_id, 'show_user_list', true );
		if ( $hosts && ( count( $hosts ) > 0 ) ) {
			foreach ( $hosts as $host ) {
				$user_info = get_userdata( $host );
				echo esc_html( $user_info->display_name )."<br>";
			}
		}
	} elseif ( $column == 'producers' ) {
		// 2.3.0: added column for Producers
		$producers = get_post_meta( $post_id, 'show_producer_list', true );
		if ( $producers && ( count( $producers ) > 0 ) ) {
			foreach ( $producers as $producer ) {
				$user_info = get_userdata( $producer );
				echo esc_html( $user_info->display_name )."<br>";
			}
		}	
	} elseif ( $column == 'show_image' ) {
		// 2.3.0: get show avatar (with fallback to thumbnail)
		$image_url = radio_station_get_show_avatar_url( $post_id );
		if ( $thumbnail_url ) {
			echo "<div class='show-image'><img src='" . $image_url . "'></div>";
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
	if ( $currentscreen->id !== 'edit-show' ) {return;}
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
	if ( 'show' !== $post_type ) {return;}

	// -- maybe get specified day ---
	$d = isset( $_GET['weekday'] ) ? $_GET['weekday'] : 0;

	// --- show day selector ---
	$days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
	?>
	<label for="filter-by-show-day" class="screen-reader-text"><?php _e( 'Filter by show day' ); ?></label>
	<select name="weekday" id="filter-by-show-day">
		<option<?php selected( $d, 0 ); ?> value="0"><?php _e( 'All show days' ); ?></option>
		<?php
		foreach ( $days as $day ) {
			if ( $d === $day ) {$selected = ' selected="selected"';} else {$selected = '';}
			$label = esc_attr( radio_station_translate_weekday( $day ) );
			echo "<option value='" . $day . "'" . $selected . ">" . $label . "</option>\n";
		}
		?>
	</select>
	<?php
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
		'override',
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
	?>
	<div id="meta_inner" class="sched-override">

		<?php
		// --- get the saved meta as an array ---
		$override = get_post_meta( $post->ID, 'show_override_sched', false );
		if ( $override ) {$override = $override[0];}
		else {
			// 2.2.8: fix undefined index warnings for new schedule overrides
			$override = array(
				'date'				=> '',
				'start_hour'		=> '',
				'start_min'			=> '',
				'start_meridian'	=> '',
				'end_hour'			=> '',
				'end_min'			=> '',
				'end_meridian'		=> ''
			);
		}
		?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#OverrideDate').datepicker({dateFormat : 'yy-mm-dd'});
		});
		</script>

		<ul style="list-style:none;">
			<li style="display:inline-block;">
				<?php esc_html_e( 'Date', 'radio-station' ); ?>:
				<input type="text" id="OverrideDate" style="width:200px; text-align:center;" name="show_sched[date]" value="<?php
				if ( ! empty( $override['date'] ) ) {
					echo esc_html( trim( $override['date'] ) );
				}
				?>">
			</li>

			<li style="display:inline-block; margin-left:20px;">
				<?php esc_html_e( 'Start Time', 'radio-station' ); ?>:
				<select name="show_sched[start_hour]" style="min-width:35px;">
					<option value=""></option>
				<?php
				for ( $i = 1; $i <= 12; $i++ ) {
					echo '<option value="' . esc_attr( $i ) . '" ' . selected( $override['start_hour'], $i ) . '>' . esc_html( $i ) . '</option>';
				}
				?>
				</select>
				<select name="show_sched[start_min]" style="min-width:35px;">
					<option value=""></option>
				<?php
				for ( $i = 0; $i < 60; $i++ ) {
					$min = $i;
					if ( $i < 10 ) {
						$min = '0' . $i;
					}
					echo '<option value="' . esc_attr( $min ) . '"' . selected( $override['start_min'], $min ) . '>' . esc_html( $min ) . '</option>';
				}
				?>
				</select>
				<select name="show_sched[start_meridian]" style="min-width:35px;">
					<option value=""></option>
					<option value="am"
					<?php
					if ( isset( $override['start_meridian'] ) && 'am' === $override['start_meridian'] ) {
						echo ' selected="selected"';
					}
					?>><?php echo $am; ?></option>
					<option value="pm"
					<?php
					if ( isset( $override['start_meridian'] ) && 'pm' === $override['start_meridian'] ) {
						echo ' selected="selected"';
					}
					?>><?php echo $pm; ?></option>
				</select>
			</li>

			<li style="display:inline-block; margin-left:20px;">
				<?php esc_html_e( 'End Time', 'radio-station' ); ?>:
				<select name="show_sched[end_hour]" style="min-width:35px;">
					<option value=""></option>
				<?php
				for ( $i = 1; $i <= 12; $i++ ) {
					echo '<option value="' . esc_attr( $i ) . '" ' . selected( $override['end_hour'], $i ) . '>' . esc_html( $i ) . '</option>';
				}
				?>
				</select>
				<select name="show_sched[end_min]" style="min-width:35px;">
					<option value=""></option>
				<?php
				for ( $i = 0; $i < 60; $i++ ) {
					$min = $i;
					if ( $i < 10 ) {
						$min = '0' . $i;
					}
					echo '<option value="' . esc_attr( $min ) . '"' . selected( $override['end_min'], $min ) . '>' . esc_html( $min ) . '</option>';
				}
				?>
				</select>
				<select name="show_sched[end_meridian]" style="min-width:35px;">
					<option value=""></option>
					<option value="am"
					<?php
					if ( isset( $override['end_meridian'] ) && ( 'am' === $override['end_meridian'] ) ) {
						echo ' selected="selected"';
					}
					?>><?php echo $am; ?></option>
					<option value="pm"
					<?php
					if ( isset( $override['end_meridian'] ) && ( 'pm' === $override['end_meridian'] ) ) {
						echo ' selected="selected"';
					}
					?>><?php echo $pm; ?></option>
				</select>
			</li>
		</ul>
	</div>
	<?php
}

// ------------------------
// Update Schedule Override
// ------------------------
add_action( 'save_post', 'radio_station_master_override_save_showpostdata' );
function radio_station_master_override_save_showpostdata( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {return;}

	// --- verify this came from the our screen and with proper authorization ---
	if ( !isset( $_POST['show_override_nonce'] ) 
	  || !wp_verify_nonce( $_POST['show_override_nonce'], 'radio-station' ) ) {
		return;
	}

	// --- get the show override data ---
	$sched = $_POST['show_sched'];
	if ( !is_array( $sched ) ) {return;}

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
		} elseif ( 'start_hour' === $key || 'end_hour' === $key ) {
			if ( empty( $value ) ) {
				$isvalid = true;
			} elseif ( ( absint( $value ) > 0 ) && ( absint( $value ) < 13 ) ) {
				$isvalid = true;
			}
		} elseif ( 'start_min' === $key || 'end_min' === $key ) {
			// 2.2.3: fix to validate 00 minute value
			if ( empty( $value ) ) {
				$isvalid = true;
			} elseif ( absint( $value ) > -1 && absint( $value ) < 61 ) {
				$isvalid = true;
			}
		} elseif ( 'start_meridian' === $key || 'end_meridian' === $key ) {
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
			if ( $key == 'date' ) {
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
add_filter( 'manage_edit-override_columns', 'radio_station_override_columns', 6 );
function radio_station_override_columns( $columns ) {
	if ( isset( $columns['thumbnail'] ) ) {unset( $columns['thumbnail'] );}
	if ( isset( $columns['post_thumb'] ) ) {unset( $columns['post_thumb'] );}
	$date = $columns['date']; unset($columns['date']);
	$columns['override_date'] = esc_attr( __( 'Override Date', 'radio-station' ) );
	$columns['start_time'] = esc_attr( __( 'Start Time', 'radio-station' ) );
	$columns['end_time'] = esc_attr( __( 'End Time', 'radio-station' ) );
	$columns['shows_affected'] = esc_attr( __( 'Affected Show(s) on Date', 'radio-station') );
	// 2.3.0: added description indicator column
	$columns['description'] = esc_attr( __('Description', 'radio-station' ) );
	$columns['override_image'] = esc_attr( __( 'Override Image' ) );
	$columns['date'] = $date;
	return $columns;
}

// -----------------------------
// Schedule Override Column Data
// -----------------------------
// 2.2.7: added data columns for override list display
add_action( 'manage_override_posts_custom_column', 'radio_station_override_column_data', 5, 2 );
function radio_station_override_column_data( $column, $post_id ) {

	global $radio_station_show_shifts;

	$override = get_post_meta( $post_id, 'show_override_sched', true );
	if ( $column == 'override_date' ) {
		$datetime = strtotime( $override['date'] );
		$month = date( 'F', $datetime );
		$month = radio_station_translate_month( $month );
		$weekday = date ( 'l', $datetime );
		$weekday = radio_station_translate_weekday( $weekday );
		echo $weekday . ' ' . date( 'j', $datetime ) . ' '. $month . ' ' . date( 'Y', $datetime );
	}
	elseif ( $column == 'start_time' ) {echo $override['start_hour'] . ':' . $override['start_min'] . ' ' . $override['start_meridian'];}
	elseif ( $column == 'end_time' ) {echo $override['end_hour'] . ':' . $override['end_min'] . ' ' . $override['end_meridian'];}
	elseif ( $column == 'shows_affected' ) {

		// --- maybe get all show shifts ---
		if ( isset( $radio_station_show_shifts ) ) {
			$show_shifts = $radio_station_show_shifts;
		} else {
			global $wpdb;
			$query = "SELECT posts.post_title, meta.post_id, meta.meta_value FROM " . $wpdb->prefix . "postmeta} AS meta
				JOIN " . $wpdb->prefix . "posts as posts
					ON posts.ID = meta.post_id
				WHERE meta.meta_key = 'show_sched' AND
					posts.post_status = 'publish'";
			// 2.3.0: get results as an array
			$show_shifts = $wpdb->get_results( $query, ARRAY_A );
			$radio_station_show_shifts = $show_shifts;
		}
		if ( !$show_shifts || ( count( $show_shifts ) == 0 ) ) {return;}

		// --- get the override weekday and convert to 24 hour time ---
		$datetime = strtotime( $override['date'] );
		$weekday = date( 'l', $datetime );

		// --- get start and end override times ---
		$override_start = strtotime( $override['date']. ' ' . $override['start_hour'] . ':' . $override['start_min'] . ' ' . $override['start_meridian'] );
		$override_end = strtotime( $override['date']. ' ' . $override['end_hour'] . ':' . $override['end_min'] . ' ' . $override['end_meridian'] );
		// (if the end time is less than start time, adjust end to next day)
		if ( $override_end <= $override_start ) {$override_end = $override_end + 86400;}

		// --- loop show shifts ---
		foreach ( $show_shifts as $show_shift ) {
			$shift = maybe_unserialize( $show_shift['meta_value'] );
			if ( !is_array( $shift ) ) {$shift = array();}

			foreach ( $shift as $time ) {
				if ( isset( $time['day'] ) && ( $time['day'] == $weekday ) ) {
					
					// --- get start and end shift times ---
					// 2.3.0: validate shift time to check if complete
					$time = radio_station_validate_shift( $time );
					$shift_start = strtotime( $override['date']. ' ' . $time['start_hour'] . ':' . $time['start_min'] . ' ' .$time['start_meridian'] );
					$shift_end = strtotime( $override['date']. ' ' . $time['end_hour'] . ':' . $time['end_min'] . ' ' . $time['end_meridian'] );
					if ( $shift_end <= $shift_end ) {$shift_end = $shift_end + 86400;}

					// --- compare override time overlaps to get affected shows ---
					if ( ( ( $override_start < $shift_start ) && ( $override_end > $shift_end ) )
					  || ( ( $override_start >= $shift_start ) && ( $override_end < $shift_end ) ) ) {
						// 2.3.0: adjust cell display to two line (to allow for long show titles)
						$active = get_post_meta( $show_shift['post_id'], 'show_active', true );
						if ( $active != 'on' ) {echo "[<i>" . __( 'Inactive Show', 'radio-station' ) . "</i>] ";}
						echo $show_shift['post_title'] . "<br>";
						if ( $time['disabled'] ) {echo "[<i>" . __( 'Disabled Shift', 'radio-station' ) . "</i>] ";}
						echo radio_station_translate_weekday( $time['day'] );
						echo " " . $time['start_hour'] . ":" . $time['start_min'] . $time['start_meridian'];
						echo " - " . $time['end_hour'] . ":" . $time['end_min'] . $time['end_meridian'];
						echo "<br>";
					}
				}
			}
		}
	} elseif ( $column == 'description' ) {
		// 2.3.0: added override description indicator
		global $wpdb;
		$query = "SELECT post_content FROM ".$wpdb->prefix."posts WHERE ID = %d";
		$query = $wpdb->prepare( $query, $post_id );
		$content = $wpdb->get_var( $query );
		if ( !$content || ( trim( $content ) == '' ) ) {
			echo '<b>' . __( 'No', 'radio-station' ) . '</b>';
		} else {
			echo __( 'Yes', 'radio-station' );
		}
	} elseif ( $column == 'override_image' ) {
		$thumbnail_url = get_the_post_thumbnail_url( $post_id );
		if ( $thumbnail_url ) {
			echo "<div class='override_image'><img src='" . $thumbnail_url . "'></div>";
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
	if ( $currentscreen->id !== 'edit-override' ) {return;}
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
	if ( 'override' !== $post_type ) {return;}

	// --- get all show override months / years ---
	global $wpdb;
	$overridequery = "SELECT ID FROM ".$wpdb->posts." WHERE post_type = 'override'";
	$results = $wpdb->get_results( $overridequery, ARRAY_A );
	if ( $results && ( count($results) > 0 ) ) {
		foreach ( $results as $result ) {
			$post_id = $result['ID'];
			$override = get_post_meta( $post_id, 'show_override_date', true );
			$datetime = strtotime( $override );
			$month = date( 'm', $datetime );
			$year = date( 'Y', $datetime );
			$months[$year.$month]['year'] = $year;
			$months[$year.$month]['month'] = $month;
		}
	} else {return;}

	// --- maybe get specified month ---
	// TODO: use get_query_var for month ?
	$m = isset( $_GET['month'] ) ? (int) $_GET['month'] : 0;

	// --- month override selector ---
	?>
	<label for="filter-by-override-date" class="screen-reader-text"><?php _e( 'Filter by override date' ); ?></label>
	<select name="month" id="filter-by-override-date">
		<option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All override dates' ); ?></option>
		<?php
		foreach ( $months as $key => $data ) {
			if ( $m == $key ) {$selected = ' selected="selected"';} else {$selected = '';}
			$label  = esc_attr( $wp_locale->get_month( $data['month'] ) . ' ' . $data['year'] );
			echo "<option value='" . $key . "'" . $selected . ">" . $label . "</option>\n";
		}
		?>
	</select>
	<?php
}


// -----------------------------------
// === Post Type List Query Filter ===
// -----------------------------------
// 2.2.7: added filter for custom column sorting
add_action( 'pre_get_posts', 'radio_station_columns_query_filter' );
function radio_station_columns_query_filter( $query ) {
	if( ! is_admin() || ! $query->is_main_query() ) {return;}

	// --- Shows by Shift Days Filtering ---
	if ( 'show' === $query->get( 'post_type' ) ) {

		// --- check if day filter is seta ---
		// TODO: use get_query_var for weekday ? 
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
				$showquery = "SELECT ID FROM ".$wpdb->posts." WHERE post_type = 'show'";
				$results = $wpdb->get_results( $showquery, ARRAY_A );
				$radio_station_data['all-shows'] = $results;
			}
			if ( $results && ( count( $results ) > 0 ) ) {
				foreach ( $results as $result ) {
					$post_id = $result['ID'];
					$shifts = get_post_meta( $post_id, 'show_sched', true );
					// TODO: check if need to get array index 0 ?
					if ( $shifts && is_array( $shifts ) ) {
						$shiftdays = array(); $shiftstart = false;
						foreach ( $shifts as $shift ) {
							if ( $shift['day'] == $weekday ) {
								$shifttime = radio_station_convert_schedule_to_24hour( $shift );
								$shiftstart = $shifttime['start_hour'] . ':' . $shifttime['start_min'] . ":00";
							}
						}
						// TODO: get earliest shift for that day
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
	if ( 'override' === $query->get( 'post_type' ) ) {

		// unless order by published date is explicitly chosen
		if ( 'date' !== $query->get( 'orderby') ) {

			// need to loop and sync a separate meta key to enable orderby sorting
			// (not really efficient but at least it makes it possible!)
			// ...but could be improved by checking against postmeta table
			global $wpdb;
			$overridequery = "SELECT ID FROM ".$wpdb->posts." WHERE post_type = 'override'";
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
					'key'       => 'show_override_date',
					'value'     => array( $start_date, $end_date ),
					'compare'   => 'BETWEEN',
					'type'      => 'DATE'
				);
				$query->set( 'meta_query', $meta_query );
			}

		}
	}
}

