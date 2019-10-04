<?php

/*
 * Admin Post Types Metaboxes and Post Lists
 * Author: Nikki Blight
 * Since: 2.2.7
 */

// === Metabox Positions ===
// - Metaboxes Above Content Area
// - Shift Genre Metabox
// === Playlists ===
// - Add Playlist Data Metabox
// - Playlist Data Metabox
// - Update Playlist Data
// - Add Playlist List Columns
// - Playlist List Column Data
// - Playlist List Column Styles
// === Shows ===
// - Add Related Show Metabox
// - Related Shows Metabox
// - Update Related Show
// - Add Assign Playlist to Show Metabox
// - Assign Playlist to Show Metabox
// - Add Playlist Info Metabox
// - Playlist Info Metabox
// - Add Assign DJs to Show Metabox
// - Assign DJs to Show Metabox
// - Add Show Shifts Metabox
// - Show Shifts Metabox
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
// --- shows plugin metaboxes above editor box for plugin CPTs ---
add_action( 'edit_form_after_title', 'radio_station_top_meta_boxes' );
function radio_station_top_meta_boxes() {
	global $post;
	do_meta_boxes( get_current_screen(), 'top', $post );
}

// ----------------------------
// Shift Genre Metabox on Shows
// ----------------------------
// --- moves genre metabox above publish box ---
add_action( 'add_meta_boxes_show', 'radio_station_genre_meta_box_order' );
function radio_station_genre_meta_box_order() {
	global $wp_meta_boxes;
	$genres = $wp_meta_boxes['show']['side']['core']['genresdiv'];
	unset( $wp_meta_boxes['show']['side']['core']['genresdiv'] );
	$wp_meta_boxes['show']['side']['high']['genresdiv'] = $genres;
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
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_custom_box', 1 );
function radio_station_myplaylist_add_custom_box() {
	// 2.2.2: change context to show at top of edit screen
	add_meta_box(
		'dynamic_sectionid',
		__( 'Playlist Entries', 'radio-station' ),
		'radio_station_myplaylist_inner_custom_box',
		'playlist',
		'top', // shift to top
		'high'
	);
}

// ---------------------
// Playlist Data Metabox
// ---------------------
// -- prints the playlist entry box to the main column on the edit screen ---
function radio_station_myplaylist_inner_custom_box() {

	global $post;

	// --- add nonce field for verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMeta_noncename' );
	?>
	<div id="meta_inner">
	<?php

	// --- get the saved meta as an arry ---
	$entries = get_post_meta( $post->ID, 'playlist', false );
	$c       = 1;

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
		if ( ! in_array( $post->post_status, array( 'publish', 'future', 'private' ) ) || ( 0 === $post->ID ) ) {
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

// --------------------
// Update Playlist Data
// --------------------
// --- When a playlist is saved, saves our custom data ---
add_action( 'save_post', 'radio_station_myplaylist_save_postdata' );
function radio_station_myplaylist_save_postdata( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['playlist'] ) || isset( $_POST['playlist_show_id'] ) ) {

		// --- verify this came from the our screen and with proper authorization ---
		if ( ! isset( $_POST['dynamicMeta_noncename'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['dynamicMeta_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		if ( ! isset( $_POST['dynamicMetaShow_noncename'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['dynamicMetaShow_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		// OK, we are authenticated: we need to find and save the data
		$playlist = isset( $_POST['playlist'] ) ? $_POST['playlist'] : array();

		// move songs that are still queued to the end of the list so that order is maintained
		foreach ( $playlist as $i => $song ) {
			if ( 'queued' === $song['playlist_entry_status'] ) {
				$playlist[] = $song;
				unset( $playlist[ $i ] );
			}
		}
		update_post_meta( $post_id, 'playlist', $playlist );

		// sanitize and save show ID
		$show = $_POST['playlist_show_id'];
		if ( empty( $show ) ) {
			delete_post_meta( $post_id, 'playlist_show_id' );
		} else {
			$show = absint( $show );
			if ( $show > 0 ) {
				update_post_meta( $post_id, 'playlist_show_id', $show );
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
	$columns['show']       = esc_attr( __( 'Show', 'radio-station' ) );
	$columns['trackcount'] = esc_attr( __( 'Tracks', 'radio-station' ) );
	$columns['tracklist']  = esc_attr( __( 'Track List', 'radio-station' ) );
	$columns['comments']   = $comments;
	$columns['date']       = $date;
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
		$post    = get_post( $show_id );
		echo "<a href='" . get_edit_post_link( $post->ID ) . "'>" . $post->post_title . '</a>';
	} elseif ( $column == 'trackcount' ) {
		$tracks = get_post_meta( $post_id, 'playlist', true );
		echo count( $tracks );
	} elseif ( $column == 'tracklist' ) {
		$tracks     = get_post_meta( $post_id, 'playlist', true );
		$tracklist  = '<a href="javascript:void(0);" onclick="showhidetracklist(\'' . $post_id . '\')">';
		$tracklist .= __( 'Show/Hide Tracklist', 'radio-station' ) . '</a><br>';
		$tracklist .= '<div id="tracklist-' . $post_id . '" style="display:none;">';
		$tracklist .= '<table class="tracklist-table" cellpadding="0" cellspacing="0">';
		$tracklist .= '<tr><td><b>#</b></td>';
		$tracklist .= '<td><b>' . __( 'Song', 'radio-station' ) . '</b></td>';
		$tracklist .= '<td><b>' . __( 'Artist', 'radio-station' ) . '</b></td>';
		$tracklist .= '<td><b>' . __( 'Status', 'radio-station' ) . '</b></td></tr>';
		foreach ( $tracks as $i => $track ) {
			$tracklist .= '<tr><td>' . $i . '</td>';
			$tracklist .= '<td>' . $track['playlist_entry_song'] . '</td>';
			$tracklist .= '<td>' . $track['playlist_entry_artist'] . '</td>';
			$status     = $track['playlist_entry_status'];
			$status     = strtoupper( substr( $status, 0, 1 ) ) . substr( $status, 1, strlen( $status ) );
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
	if ( $currentscreen->id !== 'edit-playlist' ) {
		return;
	}
	echo '<style>#show {width: 150px;} #trackcount {width: 50px;}
	#tracklist {width: 400px;} .tracklist-table td {padding: 0px 10px;}</style>';

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
// --- Add custom meta box for show assignment on blog posts ---
add_action( 'add_meta_boxes', 'radio_station_add_showblog_box' );
function radio_station_add_showblog_box() {

	// --- make sure a show exists before adding metabox ---
	$args  = array(
		'numberposts' => -1,
		'offset'      => 0,
		'orderby'     => 'post_title',
		'order'       => 'ASC',
		'post_type'   => 'show',
		'post_status' => 'publish',
	);
	$shows = get_posts( $args );

	if ( count( $shows ) > 0 ) {

		// ---- add a filter for which post types to show metabox on ---
		// TODO: add this filter to plugin documentation
		$post_types = apply_filters( 'radio_station_show_related_post_types', array( 'post' ) );

		// --- add the metabox to post types ---
		add_meta_box(
			'dynamicShowBlog_sectionid',
			__( 'Related to Show', 'radio-station' ),
			'radio_station_inner_showblog_custom_box',
			$post_types,
			'side'
		);
	}
}

// --------------------
// Related Show Metabox
// --------------------
// --- Prints the box content for the Show field ---
function radio_station_inner_showblog_custom_box() {
	global $post;

	// --- add nonce field for verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaShowBlog_noncename' );

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

	?>
	<div id="meta_inner">

	<select name="post_showblog_id">
		<option value=""></option>
	<?php
		// -- output show selection options ---
	foreach ( $shows as $show ) {
		echo '<option value="' . esc_attr( $show->ID ) . '" ' . selected( $show->ID, $current ) . '>' . esc_html( $show->post_title ) . '</option>';
	}
	?>
	</select>
	</div>
	<?php
}

// -------------------
// Update Related Show
// -------------------
// --- When a post is saved, saves our custom data ---
add_action( 'save_post', 'radio_station_save_postdata' );
function radio_station_save_postdata( $post_id ) {

	// --- verify if this is an auto save routine ---
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['post_showblog_id'] ) ) {
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times

		if ( ! isset( $_POST['dynamicMetaShowBlog_noncename'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['dynamicMetaShowBlog_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		// OK, we are authenticated: we need to find and save the data
		$show = $_POST['post_showblog_id'];

		if ( empty( $show ) ) {
			// remove show from post
			delete_post_meta( $post_id, 'post_showblog_id' );
		} else {
			// sanitize to numeric before updating
			$show = absint( $show );
			if ( $show > 0 ) {
				update_post_meta( $post_id, 'post_showblog_id', $show );
			}
		}
	}

}


// -----------------------------------
// Add Assign Playlist to Show Metabox
// -----------------------------------
// --- Add custom meta box for show assigment ---
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_show_box' );
function radio_station_myplaylist_add_show_box() {
	// 2.2.2: add high priority to shift above publish box
	add_meta_box(
		'dynamicShow_sectionid',
		__( 'Show', 'radio-station' ),
		'radio_station_myplaylist_inner_show_custom_box',
		'playlist',
		'side',
		'high'
	);
}

// -------------------------------
// Assign Playlist to Show Metabox
// -------------------------------
// --- Prints the box content for the Show field ---
function radio_station_myplaylist_inner_show_custom_box() {

	global $post, $wpdb;

	// --- add nonce field for verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaShow_noncename' );

	$user = wp_get_current_user();

	// --- allow administrators to do whatever they want ---
	// 2.2.8: remove strict in_array checking
	if ( ! in_array( 'administrator', $user->roles ) ) {

		// --- get the user lists for all shows ---
		$allowed_shows = array();

		$show_user_lists = $wpdb->get_results( "SELECT pm.meta_value, pm.post_id FROM {$wpdb->postmeta} pm WHERE pm.meta_key = 'show_user_list'" );

		// ---- check each list for the current user ---
		foreach ( $show_user_lists as $list ) {

			$list->meta_value = maybe_unserialize( $list->meta_value );

			// --- if a list has no users, unserialize() will return false instead of an empty array ---
			// (fix that to prevent errors in the foreach loop)
			if ( ! is_array( $list->meta_value ) ) {
				$list->meta_value = array();
			}

			// --- only include shows the user is assigned to ---
			foreach ( $list->meta_value as $user_id ) {
				if ( $user->ID === $user_id ) {
					$allowed_shows[] = $list->post_id;
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

	} else {

		// --- for if you are an administrator ---
		$args = array(
			'numberposts' => -1,
			'offset'      => 0,
			'orderby'     => 'post_title',
			'order'       => 'aSC',
			'post_type'   => 'show',
			'post_status' => 'publish',
		);

		$shows = get_posts( $args );
	}

	?>
	<div id="meta_inner">

	<select name="playlist_show_id">
	<?php
		// --- loop playlist selection options ---
		$current = get_post_meta( $post->ID, 'playlist_show_id', true );
		echo '<option value="" ' . selected( $current, false ) . '>' . esc_html__( 'Unassigned', 'radio-station' ) . '</option>';
	foreach ( $shows as $show ) {
		echo '<option value="' . esc_attr( $show->ID ) . '" ' . selected( $show->ID, $current ) . '>' . esc_html( $show->post_title ) . '</option>';
	}
	?>
	</select>
	</div>
	<?php
}

// -------------------------
// Add Playlist Info Metabox
// -------------------------
// --- Adds a box to the side column of the show edit screens ---
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_metainfo_box' );
function radio_station_myplaylist_add_metainfo_box() {
	// 2.2.2: change context to show at top of edit screen
	add_meta_box(
		'dynamicShowMeta_sectionid',
		__( 'Information', 'radio-station' ),
		'radio_station_myplaylist_inner_metainfo_custom_box',
		'show',
		'top', // shift to top
		'high'
	);
}

// ---------------------
// Playlist Info Metabox
// ---------------------
// --- Prints the box for additional meta data for the Show post type ---
function radio_station_myplaylist_inner_metainfo_custom_box() {

	global $post;

	$file   = get_post_meta( $post->ID, 'show_file', true );
	$email  = get_post_meta( $post->ID, 'show_email', true );
	$active = get_post_meta( $post->ID, 'show_active', true );
	$link   = get_post_meta( $post->ID, 'show_link', true );

	// added max-width to prevent metabox overflows
	?>
	<div id="meta_inner">

	<p><label><?php esc_html_e( 'Active', 'radio-station' ); ?></label>
	<input type="checkbox" name="show_active" <?php checked( $active, 'on' ); ?> />
	<em><?php esc_html_e( 'Check this box if show is currently active (Show will not appear on programming schedule if unchecked)', 'radio-station' ); ?></em></p>

	<p><label><?php esc_html_e( 'Current Audio File', 'radio-station' ); ?>:</label><br />
	<input type="text" name="show_file" size="100" style="max-width:100%;" value="<?php echo esc_attr( $file ); ?>" /></p>

	<p><label><?php esc_html_e( 'DJ Email', 'radio-station' ); ?>:</label><br />
	<input type="text" name="show_email" size="100" style="max-width:100%;" value="<?php echo esc_attr( $email ); ?>" /></p>

	<p><label><?php esc_html_e( 'Website Link', 'radio-station' ); ?>:</label><br />
	<input type="text" name="show_link" size="100" style="max-width:100%;" value="<?php echo esc_url( $link ); ?>" /></p>

	</div>
	<?php
}

// ------------------------------
// Add Assign DJs to Show Metabox
// ------------------------------
// --- Adds a box to the side column of the show edit screens ---
add_action( 'add_meta_boxes', 'radio_station_myplaylist_add_user_box' );
function radio_station_myplaylist_add_user_box() {
	// 2.2.2: add high priority to show at top of edit sidebar
	add_meta_box(
		'dynamicUser_sectionid',
		__( 'DJs', 'radio-station' ),
		'radio_station_myplaylist_inner_user_custom_box',
		'show',
		'side',
		'high'
	);
}

// --------------------------
// Assign DJs to Show Metabox
// --------------------------
// --- Prints the box for user assignement for the Show post type ---
function radio_station_myplaylist_inner_user_custom_box() {

	global $post, $wp_roles, $wpdb;

	// --- add nonce field for verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaUser_noncename' );

	// --- check for roles that have the edit_shows capability enabled ---
	$add_roles = array( 'dj' );
	foreach ( $wp_roles->roles as $name => $role ) {
		foreach ( $role['capabilities'] as $capname => $capstatus ) {
			if ( 'edit_shows' === $capname && $capstatus ) {
				$add_roles[] = $name;
			}
		}
	}
	$add_roles = array_unique( $add_roles );

	// ---- create the meta query for get_users() ---
	$meta_query = array( 'relation' => 'OR' );
	foreach ( $add_roles as $role ) {
		$meta_query[] = array(
			'key'     => $wpdb->prefix . 'capabilities',
			'value'   => $role,
			'compare' => 'like',
		);
	}

	// --- get all eligible users ---
	$args = array(
		'meta_query' => $meta_query,
		'orderby'    => 'display_name',
		'order'      => 'ASC',
		//' fields' => array( 'ID, display_name' ),
	);
	$users = get_users( $args );

	// --- get the DJs currently assigned to the show ---
	$current = get_post_meta( $post->ID, 'show_user_list', true );
	if ( ! $current ) {
		$current = array();
	}

	// --- move any selected DJs to the top of the list ---
	foreach ( $users as $i => $dj ) {
		// 2.2.8: remove strict in_array checking
		if ( in_array( $dj->ID, $current ) ) {
			unset( $users[ $i ] ); // unset first, or prepending will change the index numbers and cause you to delete the wrong item
			array_unshift( $users, $dj );  // prepend the user to the beginning of the array
		}
	}

	// 2.2.2: add fix to make DJ multi-select input full metabox width
	?>
	<div id="meta_inner">

	<select name="show_user_list[]" multiple="multiple" style="height: 150px; width: 100%;">
		<option value=""></option>
	<?php
	foreach ( $users as $dj ) {
		// 2.2.2: set DJ display name maybe with username
		$display_name = $dj->display_name;
		if ( $dj->display_name !== $dj->user_login ) {
			$display_name .= ' (' . $dj->user_login . ')';
		}
		// 2.2.7: fix to remove unnecessary third argument
		// 2.2.8: removed unnecessary fix for non-strict check
		$checkcurrent = in_array( $dj->ID, $current );
		echo '<option value="' . esc_attr( $dj->ID ) . '" ' . selected( $checkcurrent ) . '>' . esc_html( $display_name ) . '</option>';
	}
	?>
	</select>
	</div>
	<?php
}

// -----------------------
// Add Show Shifts Metabox
// -----------------------
// --- Adds schedule box to show edit screens ---
add_action( 'add_meta_boxes', 'radio_station_add_show_shifts_box' );
function radio_station_add_show_shifts_box() {
	// 2.2.2: change context to show at top of edit screen
	add_meta_box(
		'dynamicSched_sectionid',
		__( 'Schedules', 'radio-station' ),
		'radio_station_show_shifts_box',
		'show',
		'top', // shift to top
		'low'
	);
}

// -------------------
// Show Shifts Metabox
// -------------------
function radio_station_show_shifts_box() {

	global $post;

	// 2.2.7: added meridiem translations
	$am = radio_station_translate_meridiem( 'am' );
	$pm = radio_station_translate_meridiem( 'pm' );

	// --- add nonce field for verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaSched_noncename' );
	?>
		<div id="meta_inner">
		<?php

		// --- get the saved meta as an array ---
		$shifts = get_post_meta( $post->ID, 'show_sched', false );

		$c = 0;
		if ( isset( $shifts[0] ) && is_array( $shifts[0] ) ) {

			// 2.2.7: soft shifts by start day and time for ordered display
			foreach ( $shifts[0] as $shift ) {
				if ( isset( $shift['day'] ) ) {
					// --- group shifts by days of week ---
					$starttime = strtotime( 'next ' . $shift['day'] . ' ' . $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'] );
					if ( $shift['day'] == 'Sunday' ) {
						$i = 0;
					} elseif ( $shift['day'] == 'Monday' ) {
						$i = 1;
					} elseif ( $shift['day'] == 'Tuesday' ) {
							$i = 2;
					} elseif ( $shift['day'] == 'Wednesday' ) {
						$i = 3;
					} elseif ( $shift['day'] == 'Thursday' ) {
										$i = 4;
					} elseif ( $shift['day'] == 'Friday' ) {
						$i = 5;
					} elseif ( $shift['day'] == 'Saturdday' ) {
							$i = 6;
					}
											$day_shifts[ $i ][ $starttime ] = $shift;
				} else {
					// --- preserve shift times even if day is not set ---
					$starttime                   = strtotime( '1981-04-28 ' . $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'] );
					$day_shifts[7][ $starttime ] = $shift;
				}
			}

			// --- sort day shifts and loop ---
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
			foreach ( $show_shifts as $shift ) {
				?>
				<ul style="list-style:none;">

					<li style="display:inline-block;">
						<?php esc_html_e( 'Day', 'radio-station' ); ?>:
						<select name="show_sched[<?php echo esc_attr( $c ); ?>][day]">
							<option value=""></option>
							<option value="Monday"
							<?php
							if ( 'Monday' === $shift['day'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php esc_html_e( 'Monday', 'radio-station' ); ?></option>
							<option value="Tuesday"
							<?php
							if ( 'Tuesday' === $shift['day'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php esc_html_e( 'Tuesday', 'radio-station' ); ?></option>
							<option value="Wednesday"
							<?php
							if ( 'Wednesday' === $shift['day'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php esc_html_e( 'Wednesday', 'radio-station' ); ?></option>
							<option value="Thursday"
							<?php
							if ( 'Thursday' === $shift['day'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php esc_html_e( 'Thursday', 'radio-station' ); ?></option>
							<option value="Friday"
							<?php
							if ( 'Friday' === $shift['day'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php esc_html_e( 'Friday', 'radio-station' ); ?></option>
							<option value="Saturday"
							<?php
							if ( 'Saturday' === $shift['day'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php esc_html_e( 'Saturday', 'radio-station' ); ?></option>
							<option value="Sunday"
							<?php
							if ( 'Sunday' === $shift['day'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php esc_html_e( 'Sunday', 'radio-station' ); ?></option>
						</select>
					</li>

					<li style="display:inline-block; margin-left:20px;">
						<?php esc_html_e( 'Start Time', 'radio-station' ); ?>:
						<select name="show_sched[<?php echo esc_attr( $c ); ?>][start_hour]" style="min-width:35px;">
							<option value=""></option>
						<?php
						for ( $i = 1; $i <= 12; $i++ ) {
							echo '<option value="' . esc_attr( $i ) . '" ' . selected( $shift['start_hour'], $i ) . '>' . esc_html( $i ) . '</option>';
						}
						?>
						</select>
						<select name="show_sched[<?php echo esc_attr( $c ); ?>][start_min]" style="min-width:35px;">
							<option value=""></option>
						<?php
						for ( $i = 0; $i < 60; $i++ ) {
								$min = $i;
							if ( $i < 10 ) {
								$min = '0' . $i;
							}
							echo '<option value="' . esc_attr( $min ) . '" ' . selected( $shift['start_min'], $min ) . '>' . esc_html( $min ) . '</option>';
						}
						?>
						</select>
						<select name="show_sched[<?php echo esc_attr( $c ); ?>][start_meridian]" style="min-width:35px;">
							<option value=""></option>
							<option value="am"
							<?php
							if ( 'am' === $shift['start_meridian'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php echo $am; ?></option>
							<option value="pm"
							<?php
							if ( 'pm' === $shift['start_meridian'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php echo $pm; ?></option>
						</select>
					</li>

					<li style="display:inline-block; margin-left:20px;">
						<?php esc_html_e( 'End Time', 'radio-station' ); ?>:
						<select name="show_sched[<?php echo esc_attr( $c ); ?>][end_hour]" style="min-width:35px;">
							<option value=""></option>
						<?php
						for ( $i = 1; $i <= 12; $i++ ) {
							echo '<option value="' . esc_attr( $i ) . '"' . selected( $shift['end_hour'], $i ) . '>' . esc_html( $i ) . '</option>';
						}
						?>
						</select>
						<select name="show_sched[<?php echo esc_attr( $c ); ?>][end_min]" style="min-width:35px;">
							<option value=""></option>
						<?php
						for ( $i = 0; $i < 60; $i++ ) {
							$min = $i;
							if ( $i < 10 ) {
								$min = '0' . $i;
							}
							echo '<option value="' . esc_attr( $min ) . '" ' . selected( $shift['end_min'], $min ) . '>' . esc_html( $min ) . '</option>';
						}
						?>
						</select>
						<select name="show_sched[<?php echo esc_attr( $c ); ?>][end_meridian]" style="min-width:35px;">
							<option value=""></option>
							<option value="am"
							<?php
							if ( 'am' === $shift['end_meridian'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php echo $am; ?></option>
							<option value="pm"
							<?php
							if ( 'pm' === $shift['end_meridian'] ) {
								echo ' selected="selected"';
							}
							?>
							><?php echo $pm; ?></option>
						</select>
					</li>
					<?php $shift['encore'] = isset( $shift['encore'] ) ? $shift['encore'] : false; ?>
					<li style="display:inline-block; margin-left:20px;"><input type="checkbox" name="show_sched[<?php echo esc_attr( $c ); ?>][encore]" <?php checked( $shift['encore'], 'on' ); ?> /> <?php esc_html_e( 'Encore Presentation', 'radio-station' ); ?></li>

					<li style="display:inline-block; margin-left:20px;"><span class="remove button-secondary" style="cursor: pointer;"><?php esc_html_e( 'Remove', 'radio-station' ); ?></span></li>

				</ul>
				<?php
				$c++;
			}
		}

		?>
	<span id="here"></span>
	<span style="text-align: center;"><a class="add button-primary" style="cursor: pointer; display:block; width: 150px; padding: 8px; text-align: center; line-height: 1em;"><?php echo esc_html__( 'Add Shift', 'radio-station' ); ?></a></span>
	<script>
		var shiftaddb =jQuery.noConflict();
		shiftaddb(document).ready(function() {
			var count = <?php echo esc_attr( $c ); ?>;
			shiftaddb(".add").click(function() {
				count = count + 1;
				output = '<ul style="list-style:none;">';
				output += '<li style="display:inline-block;">';
				output += '<?php esc_html_e( 'Day', 'radio-station' ); ?>: ';
				output += '<select name="show_sched[' + count + '][day]">';
				output += '<option value=""></option>';
				output += '<option value="Monday"><?php esc_html_e( 'Monday', 'radio-station' ); ?></option>';
				output += '<option value="Tuesday"><?php esc_html_e( 'Tuesday', 'radio-station' ); ?></option>';
				output += '<option value="Wednesday"><?php esc_html_e( 'Wednesday', 'radio-station' ); ?></option>';
				output += '<option value="Thursday"><?php esc_html_e( 'Thursday', 'radio-station' ); ?></option>';
				output += '<option value="Friday"><?php esc_html_e( 'Friday', 'radio-station' ); ?></option>';
				output += '<option value="Saturday"><?php esc_html_e( 'Saturday', 'radio-station' ); ?></option>';
				output += '<option value="Sunday"><?php esc_html_e( 'Sunday', 'radio-station' ); ?></option>';
				output += '</select>';
				output += '</li>';

				output += '<li style="display:inline-block; margin-left:20px;">';
				output += '<?php esc_html_e( 'Start Time', 'radio-station' ); ?>: ';

				output += '<select name="show_sched[' + count + '][start_hour]" style="min-width:35px;">';
				output += '<option value=""></option>';
				<?php for ( $i = 1; $i <= 12; $i++ ) { ?>
				output += '<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>';
				<?php } ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][start_min]" style="min-width:35px;">';
				output += '<option value=""></option>';
				<?php
				for ( $i = 0; $i < 60; $i++ ) :
					$min = $i;
					if ( $i < 10 ) {
						$min = '0' . $i;
					}
					?>
				output += '<option value="<?php echo esc_attr( $min ); ?>"><?php echo esc_html( $min ); ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][start_meridian]" style="min-width:35px;">';
				output += '<option value=""></option>';
				output += '<option value="am"><?php echo $am; ?></option>';
				output += '<option value="pm"><?php echo $pm; ?></option>';
				output += '</select> ';
				output += '</li>';

				output += '<li style="display:inline-block; margin-left:20px;">';
				output += '<?php esc_html_e( 'End Time', 'radio-station' ); ?>: ';
				output += '<select name="show_sched[' + count + '][end_hour]" style="min-width:35px;">';
				output += '<option value=""></option>';
				<?php for ( $i = 1; $i <= 12; $i++ ) : ?>
				output += '<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][end_min]" style="min-width:35px;">';
				output += '<option value=""></option>';
				<?php
				for ( $i = 0; $i < 60; $i++ ) :
					$min = $i;
					if ( $i < 10 ) {
						$min = '0' . $i;
					}
					?>
				output += '<option value="<?php echo esc_attr( $min ); ?>"><?php echo esc_html( $min ); ?></option>';
				<?php endfor; ?>
				output += '</select> ';
				output += '<select name="show_sched[' + count + '][end_meridian]" style="min-width:35px;">';
				output += '<option value=""></option>';
				output += '<option value="am"><?php echo $am; ?></option>';
				output += '<option value="pm"><?php echo $pm; ?></option>';
				output += '</select> ';
				output += '</li>';

				output += '<li style="display:inline-block; margin-left:20px;">';
				output += '<input type="checkbox" name="show_sched[' + count + '][encore]" /> <?php esc_html_e( 'Encore Presentation', 'radio-station' ); ?></li>';

				output += '<li style="display:inline-block; margin-left:20px;">';
				output += '<span class="remove button-secondary" style="cursor: pointer;"><?php esc_html_e( 'Remove', 'radio-station' ); ?></span></li>';

				output += '</ul>';
				shiftaddb('#here').append( output );

				return false;
			});
			shiftaddb(".remove").live('click', function() {
				shiftaddb(this).parent().parent().remove();
			});
		});
		</script>
	</div>
	<?php
}

// --------------------
// Update Show Metadata
// --------------------
// --- save the custom fields when a show is saved ---
add_action( 'save_post', 'radio_station_save_show_data' );
function radio_station_save_show_data( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- verify this came from the our screen and with proper authorization ---
	if ( ! isset( $_POST['dynamicMetaUser_noncename'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['dynamicMetaUser_noncename'], plugin_basename( __FILE__ ) ) ) {
		return;
	}

	if ( ! isset( $_POST['dynamicMetaSched_noncename'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['dynamicMetaSched_noncename'], plugin_basename( __FILE__ ) ) ) {
		return;
	}

	// --- get the post data to be saved ---
	// 2.2.3: added show metadata value sanitization
	// 2.2.7: check DJ post value is set
	if ( isset( $_POST['show_user_list'] ) ) {
		$djs = $_POST['show_user_list'];
	}
	if ( ! isset( $djs ) || ! is_array( $djs ) ) {
		$djs = array();
	} else {
		foreach ( $djs as $i => $dj ) {
			if ( ! empty( $dj ) ) {
				$userid = get_user_by( 'id', $dj );
				if ( ! $userid ) {
					unset( $djs[ $i ] );
				}
			}
		}
	}

	$file   = wp_strip_all_tags( trim( $_POST['show_file'] ) );
	$email  = sanitize_email( trim( $_POST['show_email'] ) );
	$active = $_POST['show_active'];
	// 2.2.8: remove strict in_array checking
	if ( ! in_array( $active, array( '', 'on' ) ) ) {
		$active = '';
	}
	$link = filter_var( trim( $_POST['show_link'] ), FILTER_SANITIZE_URL );

	// --- update the show metadata ---
	update_post_meta( $post_id, 'show_user_list', $djs );
	update_post_meta( $post_id, 'show_file', $file );
	update_post_meta( $post_id, 'show_email', $email );
	update_post_meta( $post_id, 'show_active', $active );
	update_post_meta( $post_id, 'show_link', $link );

	// --- update the show shift metadata
	$scheds = $_POST['show_sched'];

	// --- sanitize the show shift times ---
	$new_scheds = array();
	$days       = array( '', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
	foreach ( $scheds as $i => $sched ) {
		foreach ( $sched as $key => $value ) {
			// --- validate according to key ---
			$isvalid = false;
			if ( 'day' === $key ) {
				// 2.2.8: remove strict in_array checking
				if ( in_array( $value, $days ) ) {
					$isvalid = true;
				}
			} elseif ( 'start_hour' === $key || 'end_hour' === $key ) {
				if ( empty( $value ) ) {
					$isvalid = true;
				} elseif ( absint( $value ) > 0 && absint( $value ) < 13 ) {
					$isvalid = true;
				}
			} elseif ( 'start_min' === $key || 'end_min' === $key ) {
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
			} elseif ( 'encore' === $key ) {
				// 2.2.4: fix to missing encore sanitization saving
				$valid = array( '', 'on' );
				// 2.2.8: remove strict in_array checking
				if ( in_array( $value, $valid ) ) {
					$isvalid = true;
				}
			}

			// --- if valid add to new schedule ---
			if ( $isvalid ) {
				$new_scheds[$i][$key] = $value;
			} else {
				$new_scheds[$i][$key] = '';
			}
		}
	}

	update_post_meta( $post_id, 'show_sched', $new_scheds );

}

// ---------------------
// Add Show List Columns
// ---------------------
// 2.2.7: added data columns to show list display
add_filter( 'manage_edit-show_columns', 'radio_station_show_columns', 6 );
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
	$genres = $columns['taxonomy-genres'];
	unset( $columns['taxonomy-genres'] );
	$columns['active']          = esc_attr( __( 'Active?', 'radio-station' ) );
	$columns['shifts']          = esc_attr( __( 'Shifts', 'radio-station' ) );
	$columns['djs']             = esc_attr( __( 'DJs', 'radio-station' ) );
	$columns['show_image']      = esc_attr( __( 'Show Image', 'radio-station' ) );
	$columns['taxonomy-genres'] = $genres;
	$columns['comments']        = $comments;
	$columns['date']            = $date;
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
		if ( $active == 'on' ) {
			echo __( 'Yes', 'radio-station' );
		} else {
			echo __( 'No', 'radio-station' );
		}
	} elseif ( $column == 'shifts' ) {
		$shifts = get_post_meta( $post_id, 'show_sched', true );
		if ( $shifts && ( count( $shifts ) > 0 ) ) {
			foreach ( $shifts as $shift ) {
				$timestamp                  = strtotime( 'next ' . $shift['day'] . ' ' . $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'] );
				$sortedshifts[ $timestamp ] = $shift;
			}
			ksort( $sortedshifts );
			foreach ( $sortedshifts as $shift ) {
				if ( isset( $_GET['weekday'] ) && ( $_GET['weekday'] == $shift['day'] ) ) {
					echo '<b>';
				}
				echo radio_station_translate_weekday( $shift['day'] );
				echo ' ' . $shift['start_hour'] . ':' . $shift['start_min'] . $shift['start_meridian'];
				echo ' - ' . $shift['end_hour'] . ':' . $shift['end_min'] . $shift['end_meridian'] . '<br>';
				if ( isset( $_GET['weekday'] ) && ( $_GET['weekday'] == $shift['day'] ) ) {
					echo '</b>';
				}
			}
		}
	} elseif ( $column == 'djs' ) {
		$djs = get_post_meta( $post_id, 'show_user_list', true );
		if ( $djs && ( count( $djs ) > 0 ) ) {
			foreach ( $djs as $dj ) {
				$user_info = get_userdata( $dj );
				echo esc_html( $user_info->display_name ) . '<br>';
			}
		}
	} elseif ( $column == 'show_image' ) {
		$thumbnail_url = get_the_post_thumbnail_url( $post_id );
		if ( $thumbnail_url ) {
			echo "<div class='show_image'><img src='" . $thumbnail_url . "'></div>";
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
	if ( 'edit-show' !== $currentscreen->id ) {
		return;
	}
	echo '<style>#shifts {width: 200px;} #active, #comments {width: 50px;}
	.show_image {width: 100px;} .show_image img {width: 100%; height: auto;}</style>';
}

// -------------------------
// Add Show Shift Day Filter
// -------------------------
// 2.2.7: added show day selection filtering
add_action( 'restrict_manage_posts', 'radio_station_show_day_filter', 10, 2 );
function radio_station_show_day_filter( $post_type, $which ) {
	if ( 'show' !== $post_type ) {
		return;
	}

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
			if ( $d === $day ) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			$label = esc_attr( radio_station_translate_weekday( $day ) );
			echo "<option value='" . $day . "'" . $selected . '>' . $label . "</option>\n";
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
add_action( 'add_meta_boxes', 'radio_station_master_override_add_sched_box' );
function radio_station_master_override_add_sched_box() {
	// 2.2.2: add high priority to show at top of edit screen
	add_meta_box(
		'dynamicSchedOver_sectionid',
		__( 'Override Schedule', 'radio-station' ),
		'radio_station_master_override_inner_sched_custom_box',
		'override',
		'normal',
		'high'
	);
}

// -------------------------
// Schedule Override Metabox
// -------------------------
function radio_station_master_override_inner_sched_custom_box() {

	global $post;

	// 2.2.7: added meridiem translations
	$am = radio_station_translate_meridiem( 'am' );
	$pm = radio_station_translate_meridiem( 'pm' );

	// --- add nonce field for update verification ---
	wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMetaSchedOver_noncename' );

	// 2.2.7: add explicit width to date picker field to ensure date is visible
	?>
	<div id="meta_inner" class="sched-override">

		<?php
		// --- get the saved meta as an array ---
		$override = get_post_meta( $post->ID, 'show_override_sched', false );
		if ( $override ) {
			$override = $override[0];
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
				<input type="text" id="OverrideDate" style="width:200px; text-align:center;" name="show_sched[date]" value="
				<?php
				if ( isset( $override['date'] ) && ! empty( $override['date'] ) ) {
					echo esc_html( trim( $override['date'] ) );
				}
				?>
				">
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
					?>
					><?php echo $am; ?></option>
					<option value="pm"
					<?php
					if ( isset( $override['start_meridian'] ) && 'pm' === $override['start_meridian'] ) {
						echo ' selected="selected"';
					}
					?>
					><?php echo $pm; ?></option>
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
					?>
					><?php $am; ?></option>
					<option value="pm"
					<?php
					if ( isset( $override['end_meridian'] ) && ( 'pm' === $override['end_meridian'] ) ) {
						echo ' selected="selected"';
					}
					?>
					><?php echo $pm; ?></option>
				</select>
			</li>
		</ul>
	</div>
	<?php
}

// ------------------------
// Update Schedule Override
// ------------------------
// --- save the custom fields when a show override is saved ---
add_action( 'save_post', 'radio_station_master_override_save_showpostdata' );
function radio_station_master_override_save_showpostdata( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- verify this came from the our screen and with proper authorization ---
	if ( ! isset( $_POST['dynamicMetaSchedOver_noncename'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['dynamicMetaSchedOver_noncename'], plugin_basename( __FILE__ ) ) ) {
		return;
	}

	// --- get the show override data ---
	$sched = $_POST['show_sched'];
	if ( ! is_array( $sched ) ) {
		return;
	}

	// --- get/set current schedule for merging ---
	// 2.2.2: added to set default keys
	$current_sched = get_post_meta( $post_id, 'show_override_sched', true );
	if ( ! $current_sched || ! is_array( $current_sched ) ) {
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
		if ( $isvalid && ( $value !== $current_sched[ $key ] ) ) {
			$current_sched[ $key ] = $value;
			$changed               = true;

			// 2.2.7: sync separate meta key for override date
			// (could be used to improve column sorting efficiency)
			if ( $key == 'date' ) {
				update_post_meta( $post_id, 'show_override_date', $value );
			}
		}
	}

	// --- save schedule setting if changed ---
	if ( $changed ) {
		update_post_meta( $post_id, 'show_override_sched', $current_sched );
	}
}

// ----------------------------------
// Add Schedule Override List Columns
// ----------------------------------
// 2.2.7: added data columns to override list display
add_filter( 'manage_edit-override_columns', 'radio_station_override_columns', 6 );
function radio_station_override_columns( $columns ) {
	if ( isset( $columns['thumbnail'] ) ) {
		unset( $columns['thumbnail'] );
	}
	if ( isset( $columns['post_thumb'] ) ) {
		unset( $columns['post_thumb'] );
	}
	$date = $columns['date'];
	unset( $columns['date'] );
	$columns['override_date']  = esc_attr( __( 'Override Date', 'radio-station' ) );
	$columns['start_time']     = esc_attr( __( 'Start Time', 'radio-station' ) );
	$columns['end_time']       = esc_attr( __( 'End Time', 'radio-station' ) );
	$columns['shows_affected'] = esc_attr( __( 'Affected Show(s) on Date', 'radio-station' ) );
	$columns['override_image'] = esc_attr( __( 'Override Image' ) );
	$columns['date']           = $date;
	return $columns;
}

// -----------------------------
// Schedule Override Column Data
// -----------------------------
// 2.2.7: added data columns for override list display
add_action( 'manage_override_posts_custom_column', 'radio_station_override_column_data', 5, 2 );
function radio_station_override_column_data( $column, $post_id ) {

	global $show_shifts;

	$override = get_post_meta( $post_id, 'show_override_sched', true );
	if ( $column == 'override_date' ) {
		$datetime = strtotime( $override['date'] );
		$month    = date( 'F', $datetime );
		$month    = radio_station_translate_month( $month );
		$weekday  = date( 'l', $datetime );
		$weekday  = radio_station_translate_weekday( $weekday );
		echo $weekday . ' ' . date( 'j', $datetime ) . ' ' . $month . ' ' . date( 'Y', $datetime );
	} elseif ( $column == 'start_time' ) {
		echo $override['start_hour'] . ':' . $override['start_min'] . ' ' . $override['start_meridian'];
	} elseif ( $column == 'end_time' ) {
		echo $override['end_hour'] . ':' . $override['end_min'] . ' ' . $override['end_meridian'];
	} elseif ( $column == 'shows_affected' ) {

				// --- maybe get all show shifts ---
		if ( ! isset( $show_shifts ) ) {
			global $wpdb;
			$show_shifts = $wpdb->get_results(
				"SELECT posts.post_title, meta.post_id, meta.meta_value FROM {$wpdb->postmeta} AS meta
				JOIN {$wpdb->posts} as posts
					ON posts.ID = meta.post_id
				WHERE meta.meta_key = 'show_sched' AND
					posts.post_status = 'publish'"
			);
		}
		if ( ! $show_shifts || ( count( $show_shifts ) == 0 ) ) {
			return;
		}

			// --- get the override weekday and convert to 24 hour time ---
			$datetime = strtotime( $override['date'] );
			$weekday  = date( 'l', $datetime );

			// --- get start and end override times ---
			$startoverride = strtotime( $override['date'] . ' ' . $override['start_hour'] . ':' . $override['start_min'] . ' ' . $override['start_meridian'] );
			$endoverride   = strtotime( $override['date'] . ' ' . $override['end_hour'] . ':' . $override['end_min'] . ' ' . $override['end_meridian'] );
		if ( $endoverride <= $startoverride ) {
			$endoverride = $endoverride + 86400;
		}

			// --- loop show shifts ---
		foreach ( $show_shifts as $show_shift ) {
			$shift = maybe_unserialize( $show_shift->meta_value );
			if ( ! is_array( $shift ) ) {
				$shift = array();
			}

			foreach ( $shift as $time ) {
				if ( isset( $time['day'] ) && ( $time['day'] == $weekday ) ) {

					// --- get start and end shift times ---
					$startshift = strtotime( $override['date'] . ' ' . $time['start_hour'] . ':' . $time['start_min'] . ' ' . $time['start_meridian'] );
					$endshift   = strtotime( $override['date'] . ' ' . $time['end_hour'] . ':' . $time['end_min'] . ' ' . $time['end_meridian'] );
					if ( $endshift <= $startshift ) {
						$endshift = $endshift + 86400;
					}

					// --- compare override time overlaps to get affected shows ---
					if ( ( ( $startoverride < $startshift ) && ( $endoverride > $startshift ) )
					  || ( ( $startoverride >= $startshift ) && ( $startoverride < $endshift ) ) ) {
						$active = get_post_meta( $show_shift->post_id, 'show_active', true );
						if ( $active != 'on' ) {
							echo '<i>[' . __( 'Inactive', 'radio-station' ) . '] ';
						}
						echo $show_shift->post_title;
						echo ' (' . $time['start_hour'] . ':' . $time['start_min'] . $time['start_meridian'];
						echo ' - ' . $time['end_hour'] . ':' . $time['end_min'] . $time['end_meridian'] . ')';
						if ( $active != 'on' ) {
							echo '</i>';
						}
						echo '<br>';
					}
				}
			}
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
	if ( $currentscreen->id !== 'edit-override' ) {
		return;
	}
	echo '<style>#shows_affected {width: 300px;} #start_time, #end_time {width: 65px;}
	.override_image {width: 100px;} .override_image img {width: 100%; height: auto;}</style>';
}

// ----------------------------------
// Add Schedule Override Month Filter
// ----------------------------------
// 2.2.7: added month selection filtering
add_action( 'restrict_manage_posts', 'radio_station_override_date_filter', 10, 2 );
function radio_station_override_date_filter( $post_type, $which ) {
	global $wp_locale;
	if ( 'override' !== $post_type ) {
		return;
	}

	// --- get all show override months/years ---
	global $wpdb;
	$overridequery = 'SELECT ID FROM ' . $wpdb->posts . " WHERE post_type = 'override'";
	$results       = $wpdb->get_results( $overridequery, ARRAY_A );
	if ( $results && ( count( $results ) > 0 ) ) {
		foreach ( $results as $result ) {
			$post_id                           = $result['ID'];
			$override                          = get_post_meta( $post_id, 'show_override_date', true );
			$datetime                          = strtotime( $override );
			$month                             = date( 'm', $datetime );
			$year                              = date( 'Y', $datetime );
			$months[ $year . $month ]['year']  = $year;
			$months[ $year . $month ]['month'] = $month;
		}
	} else {
		return;
	}

	// --- maybe get specified month ---
	$m = isset( $_GET['month'] ) ? (int) $_GET['month'] : 0;

	// --- month override selector ---
	?>
	<label for="filter-by-override-date" class="screen-reader-text"><?php _e( 'Filter by override date' ); ?></label>
	<select name="month" id="filter-by-override-date">
		<option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All override dates' ); ?></option>
		<?php
		foreach ( $months as $key => $data ) {
			if ( $m == $key ) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			$label = esc_attr( $wp_locale->get_month( $data['month'] ) . ' ' . $data['year'] );
			echo "<option value='" . $key . "'" . $selected . '>' . $label . "</option>\n";
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
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	// --- Shows by Shift Days Filtering ---
	if ( 'show' === $query->get( 'post_type' ) ) {

		// --- check if day filter is seta ---
		if ( isset( $_GET['weekday'] ) && ( '0' != $_GET['weekday'] ) ) {

			$weekday = $_GET['weekday'];

			// need to loop and sync a separate meta key to enable filtering
			// (not really efficient but at least it makes it possible!)
			// ...but could be improved by checking against postmeta table
			global $wpdb;
			$showquery = 'SELECT ID FROM ' . $wpdb->posts . " WHERE post_type = 'show'";
			$results   = $wpdb->get_results( $showquery, ARRAY_A );
			if ( $results && ( count( $results ) > 0 ) ) {
				foreach ( $results as $result ) {
					$post_id = $result['ID'];
					$shifts  = get_post_meta( $post_id, 'show_sched', true );
					if ( $shifts && is_array( $shifts ) ) {
						$shiftdays  = array();
						$shiftstart = false;
						foreach ( $shifts as $shift ) {
							if ( $shift['day'] == $weekday ) {
								$shifttime  = radio_station_convert_schedule_to_24hour( $shift );
								$shiftstart = $shifttime['start_hour'] . ':' . $shifttime['start_min'] . ':00';
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
			$meta_query = array(
				'key'     => 'show_shift_time',
				'compare' => 'EXISTS',
			);
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
		if ( 'date' !== $query->get( 'orderby' ) ) {

			// need to loop and sync a separate meta key to enable orderby sorting
			// (not really efficient but at least it makes it possible!)
			// ...but could be improved by checking against postmeta table
			global $wpdb;
			$overridequery = 'SELECT ID FROM ' . $wpdb->posts . " WHERE post_type = 'override'";
			$results       = $wpdb->get_results( $overridequery, ARRAY_A );
			if ( $results && ( count( $results ) > 0 ) ) {
				foreach ( $results as $result ) {
					$post_id  = $result['ID'];
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
				$yearmonth  = $_GET['month'];
				$start_date = date( $yearmonth . '01' );
				$end_date   = date( $yearmonth . 't' );
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
