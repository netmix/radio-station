<?php

/*
 * Plugin Admin Functions
 * @Since: 2.2.7
 */

// === Admin Setup ===
// - Enqueue Admin Scripts
// - Admin Style Fixes
// === Admin Menu ===
// - Add Admin Menu Items
// - Fix to Expand Main Menu for Submenu Items
// - Genre Taxonomy Submenu Item Fix
// - Add New Links to Admin Bar
// - Output Help Page
// - Output Export Page
// === Admin Notice ===
// - Plugin Announcement Notice
// - Dismiss Plugin Announcement
// - Patreon Supporter Blurb
// - Patreon Supporter Button
// - MailChimp Subscriber Form


// -------------------
// === Admin Setup ===
// -------------------

// ---------------------
// Enqueue Admin Scripts
// ---------------------
function radio_station_enqueue_admin_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	// $url = plugins_url( 'css/jquery-ui.css', RADIO_STATION_DIR.'/radio-station.php' );
	// wp_enqueue_style( 'jquery-style', $url, array(), '1.8.2' );
	if ( is_ssl() ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
	$url = $protocol . '://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css';
	wp_enqueue_style( 'jquery-ui-style', $url, array(), '1.8.2' );
}
// 2.2.7: removed from frontend as datepicker is only used on the backend
add_action( 'admin_enqueue_scripts', 'radio_station_enqueue_admin_scripts' );

// -----------------
// Admin Style Fixes
// -----------------
function radio_station_admin_styles() {
	global $post;

	// --- hide first admin submenu item to prevent duplicate of main menu item ---
	$styles = ' #toplevel_page_radio-station .wp-first-item { display: none; }' . "\n";

	// --- reduce the height of the playlist editor area ---
	if ( isset( $post->post_type ) && ( 'playlist' === $post->post_type ) ) {
		$styles .= ' .wp-editor-container textarea.wp-editor-area { height: 100px; }' . "\n";
	}

	echo '<style type="text/css">' . $styles . '</style>';

}
add_action( 'admin_print_styles', 'radio_station_admin_styles' );


// ------------------
// === Admin Menu ===
// ------------------

// --------------------
// Add Admin Menu Items
// --------------------
function radio_station_add_admin_menus() {

	$icon       = plugins_url( 'images/radio-station-icon.png', __FILE__ );
	$position   = apply_filters( 'radio_station_menu_position', 5 );
	$capability = 'publish_playlists';
	add_menu_page( __( 'Radio Station', 'radio-station' ), __( 'Radio Station', 'radio-station' ), $capability, 'radio-station', 'radio_station_plugin_help', $icon, $position );

	add_submenu_page( 'radio-station', __( 'Shows', 'radio-station' ), __( 'Shows', 'radio-station' ), 'edit_shows', 'shows' );
	add_submenu_page( 'radio-station', __( 'Add Show', 'radio-station' ), __( 'Add Show', 'radio-station' ), 'publish_shows', 'add-show' );
	add_submenu_page( 'radio-station', __( 'Playlists', 'radio-station' ), __( 'Playlists', 'radio-station' ), 'edit_playlists', 'playlists' );
	add_submenu_page( 'radio-station', __( 'Add Playlist', 'radio-station' ), __( 'Add Playlist', 'radio-station' ), 'publish_playlists', 'add-playlist' );
	add_submenu_page( 'radio-station', __( 'Genres', 'radio-station' ), __( 'Genres', 'radio-station' ), 'publish_playlists', 'genres' );
	add_submenu_page( 'radio-station', __( 'Schedule Overrides', 'radio-station' ), __( 'Schedule Overrides', 'radio-station' ), 'edit_shows', 'schedule-overrides' );
	add_submenu_page( 'radio-station', __( 'Add Override', 'radio-station' ), __( 'Add Override', 'radio-station' ), 'publish_shows', 'add-override' );
	add_submenu_page( 'radio-station', __( 'Export Playlists', 'radio-station' ), __( 'Export Playlists', 'radio-station' ), 'manage_options', 'playlist-export', 'radio_station_admin_export' );
	add_submenu_page( 'radio-station', __( 'Help', 'radio-station' ), __( 'Help', 'radio-station' ), 'publish_playlists', 'radio-station-help', 'radio_station_plugin_help' );

	// --- hack the submenu global to add post type add/edit URLs ---
	global $submenu;
	foreach ( $submenu as $i => $menu ) {
		if ( 'radio-station' === $i ) {
			foreach ( $menu as $j => $item ) {
				switch ( $item[2] ) {
					case 'add-show':
						// maybe remove the Add Show link for DJs
						if ( ! current_user_can( 'publish_shows' ) ) {
							unset( $submenu[ $i ][ $j ] );
						} else {
							$submenu[ $i ][ $j ][2] = 'post-new.php?post_type=show';
						}
						break;
					case 'shows':
						$submenu[ $i ][ $j ][2] = 'edit.php?post_type=show';
						break;
					case 'playlists':
						$submenu[ $i ][ $j ][2] = 'edit.php?post_type=playlist';
						break;
					case 'add-playlist':
						$submenu[ $i ][ $j ][2] = 'post-new.php?post_type=playlist';
						break;
					case 'genres':
						$submenu[ $i ][ $j ][2] = 'edit-tags.php?taxonomy=genres';
						break;
					case 'schedule-overrides':
						$submenu[ $i ][ $j ][2] = 'edit.php?post_type=override';
						break;
					case 'add-override':
						$submenu[ $i ][ $j ][2] = 'post-new.php?post_type=override';
						break;
				}
			}
		}
	}
}
add_action( 'admin_menu', 'radio_station_add_admin_menus' );

// -----------------------------------------
// Fix to Expand Main Menu for Submenu Items
// -----------------------------------------
// 2.2.2: added fix for genre taxonomy page and post type editing
// 2.2.8: remove strict in_array checking
function radio_station_fix_genre_parent( $parent_file = '' ) {
	global $pagenow, $post;
	$post_types = array( 'show', 'playlist', 'override' );
	if ( ( 'edit-tags.php' === $pagenow ) && isset( $_GET['taxonomy'] ) && 'genres' === $_GET['taxonomy'] ) {
		$parent_file = 'radio-station';
	} elseif ( 'post.php' === $pagenow && in_array( $post->post_type, $post_types ) ) {
		$parent_file = 'radio-station';
	}
	return $parent_file;
}
add_filter( 'parent_file', 'radio_station_fix_genre_parent', 11 );

// -------------------------------
// Genre Taxonomy Submenu Item Fix
// -------------------------------
// 2.2.2: so genre submenu item link is set to current (bold)
function radio_station_genre_submenu_fix() {
	global $pagenow;
	if ( 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && 'genres' === $_GET['taxonomy'] ) {
		echo "<script>
	jQuery('#toplevel_page_radio-station ul li').each(function() {
		if (jQuery(this).find('a').attr('href') == 'edit-tags.php?taxonomy=genres') {
			jQuery(this).addClass('current').find('a').addClass('current').attr('aria-current', 'page');
		}
	});</script>";
	}
}
add_action( 'admin_footer', 'radio_station_genre_submenu_fix' );

// --------------------------
// Add New Links to Admin Bar
// --------------------------
// 2.2.2: re-add new post type items to admin bar
// (as no longer automatically added by register_post_type)
function station_radio_modify_admin_bar_menu( $wp_admin_bar ) {

	// --- new show ---
	if ( current_user_can( 'publish_shows' ) ) {

		$args = array(
			'id'     => 'new-show',
			'title'  => __( 'Show', 'radio-station' ),
			'parent' => 'new-content',
			'href'   => admin_url( 'post-new.php?post_type=show' ),
		);
		$wp_admin_bar->add_node( $args );
	}

	// --- new playlist ---
	if ( current_user_can( 'publish_playlists' ) ) {
		$args = array(
			'id'     => 'new-playlist',
			'title'  => __( 'Playlist', 'radio-station' ),
			'parent' => 'new-content',
			'href'   => admin_url( 'post-new.php?post_type=playlist' ),
		);
		$wp_admin_bar->add_node( $args );
	}

	// --- new schedule override ---
	if ( current_user_can( 'publish_shows' ) ) {
		$args = array(
			'id'     => 'new-override',
			'title'  => __( 'Override', 'radio-station' ),
			'parent' => 'new-content',
			'href'   => admin_url( 'post-new.php?post_type=override' ),
		);
		$wp_admin_bar->add_node( $args );
	}

}
add_action( 'admin_bar_menu', 'station_radio_modify_admin_bar_menu', 999 );

// ----------------
// Output Help Page
// ----------------
function radio_station_plugin_help() {

	// 2.2.2: include patreon button link
	echo radio_station_patreon_blurb( false );

	// --- show MailChimp signup form ---
	radio_station_mailchimp_form();

	// --- include help file template ---
	include RADIO_STATION_DIR . '/templates/help.php';
}

// ---------------------------
// Output Playlist Export Page
// ---------------------------
function radio_station_admin_export() {
	global $wpdb;

	// first, delete any old exports from the export directory
	$dir = RADIO_STATION_DIR . '/export/';
	if ( is_dir( $dir ) ) {
		$get_contents = opendir( $dir );
		while ( $file = readdir( $get_contents ) ) {
			if ( '.' !== $file && '..' !== $file ) {
				unlink( $dir . $file );
			}
		}
		closedir( $get_contents );
	}

	// --- watch for form submission ---
	if ( isset( $_POST['export_action'] ) && ( 'station_playlist_export' === $_POST['export_action'] ) ) {

		// validate referrer and nonce field
		check_admin_referer( 'station_export_valid' );

		$start  = $_POST['station_export_start_year'] . '-' . $_POST['station_export_start_month'] . '-' . $_POST['station_export_start_day'];
		$start .= ' 00:00:00';
		$end    = $_POST['station_export_end_year'] . '-' . $_POST['station_export_end_month'] . '-' . $_POST['station_export_end_day'];
		$end   .= ' 23:59:59';

		// fetch all records that were created between the start and end dates
		$sql =
		"SELECT posts.ID, posts.post_date
		FROM {$wpdb->posts} AS posts
		WHERE posts.post_type = 'playlist' AND
			posts.post_status = 'publish' AND
			TO_DAYS(posts.post_date) >= TO_DAYS(%s) AND
			TO_DAYS(posts.post_date) <= TO_DAYS(%s)
		ORDER BY posts.post_date ASC";
		// prepare query before executing
		$query     = $wpdb->prepare( $sql, array( $start, $end ) );
		$playlists = $wpdb->get_results( $query );

		if ( ! $playlists ) {
			$list = 'No playlists found for this period.';}

		// fetch the tracks for each playlist from the wp_postmeta table
		foreach ( $playlists as $i => $playlist ) {

			$songs = get_post_meta( $playlist->ID, 'playlist', true );

			// remove any entries that are marked as 'queued'
			foreach ( $songs as $j => $entry ) {
				if ( 'queued' === $entry['playlist_entry_status'] ) {
					unset( $songs[ $j ] );}
			}

			$playlists[ $i ]->songs = $songs;
		}

		$output = '';

		$date = '';
		foreach ( $playlists as $playlist ) {
			if ( ! isset( $playlist->post_date ) ) {
				continue;
			}
			$playlist_datetime  = explode( ' ', $playlist->post_date );
			$playlist_post_date = array_shift( $playlist_datetime );
			if ( empty( $date ) || $date !== $playlist_post_date ) {
				$date    = $playlist_post_date;
				$output .= $date . "\n\n";
			}

			foreach ( $playlist->songs as $song ) {
				$output .= $song['playlist_entry_artist'] . ' || ' . $song['playlist_entry_song'] . ' || ' . $song['playlist_entry_album'] . ' || ' . $song['playlist_entry_label'] . "\n";
			}
		}

		// save as file
		$dir  = RADIO_STATION_DIR . '/export/';
		$file = $date . '-export.txt';
		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );}

		$f = fopen( $dir . $file, 'w' );
		fwrite( $f, $output );
		fclose( $f );

		// display link to file
		$url = get_bloginfo( 'url' ) . '/wp-content/plugins/radio-station/tmp/' . $file;
		echo wp_kses_post( '<div id="message" class="updated"><p><strong><a href="' . $url . '">' . __( 'Right-click and download this file to save your export', 'radio-station' ) . '</a></strong></p></div>' );
	}

	// display the export page
	include RADIO_STATION_DIR . '/templates/admin-export.php';

}


// --------------------
// === Admin Notice ===
// --------------------

// --------------------------
// Plugin Announcement Notice
// --------------------------
// 2.2.2: added plugin announcement notice
function radio_station_announcement_notice() {

	// --- bug out if already dismissed ---
	if ( get_option( 'radio_station_announcement_dismissed' ) ) {
		return;
	}

	// --- bug out on certain plugin pages ---
	$pages = array( 'radio-station', 'radio-station-help' );
	// 2.2.8: remove strict in_array checking
	if ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $pages ) ) {
		return;
	}

	// --- display plugin announcement ---
	echo '<div id="radio-station-announcement-notice" class="notice notice-info" style="position:relative;">';
		echo radio_station_patreon_blurb();
		echo '<iframe src="javascript:void(0);" name="radio-station-notice-iframe" style="display:none;"></iframe>';
	echo '</div>';
}
add_action( 'admin_notices', 'radio_station_announcement_notice' );

// ----------------------------------
// Dismiss Plugin Announcement Notice
// ----------------------------------
// 2.2.2: AJAX for announcement notice dismissal
function radio_station_announcement_dismiss() {
	if ( current_user_can( 'manage_options' ) || current_user_can( 'update_plugins' ) ) {
		update_option( 'radio_station_announcement_dismissed', true );
		echo "<script>parent.document.getElementById('radio-station-announcement-notice').style.display = 'none';</script>";
		exit;
	}
}
add_action( 'wp_ajax_radio_station_announcement_dismiss', 'radio_station_announcement_dismiss' );

// -----------------------
// Patreon Supporter Blurb
// -----------------------
// 2.2.2: added simple patreon supporter blurb
function radio_station_patreon_blurb( $dismissable = true ) {

	$blurb                = '<ul style="list-style:none;">';
		$blurb           .= '<li style="display:inline-block; vertical-align:middle;">';
			$plugin_image = plugins_url( 'images/radio-station.png', __FILE__ );
			$blurb       .= '<img src="' . $plugin_image . '">';
		$blurb           .= '</li>';
		$blurb           .= '<li style="display:inline-block; vertical-align:middle; margin-left:40px; font-size:16px; line-height:24px;">';
			$blurb       .= '<b style="font-size:17px;">' . __( 'Help support us to make improvements, modifications and introduce new features!', 'radio-station' ) . '</b><br>';
			$blurb       .= __( 'With over a thousand radio station users thanks to the original plugin author Nikki Blight', 'radio-station' ) . ', <br>';
			$blurb       .= __( 'since June 2019', 'radio-station' ) . ', ';
			$blurb       .= '<b>' . __( 'Radio Station', 'radio-station' ) . '</b> ';
			$blurb       .= __( ' plugin development has been actively taken over by', 'radio-station' );
			$blurb       .= ' <a href="http://netmix.com" target="_blank">Netmix</a>.<br>';
			$blurb       .= __( 'We invite you to', 'radio-station' );
			$blurb       .= ' <a href="https://patreon.com/radiostation" target="_blank">';
				$blurb   .= __( 'Become a Radio Station Patreon Supporter', 'radio-station' );
			$blurb       .= '</a> ' . __( 'to make it better for everyone', 'radio-station' ) . '!';
		$blurb           .= '</li>';
		$blurb           .= '<li style="display:inline-block; text-align:center; vertical-align:middle; margin-left:40px;">';
			$blurb       .= radio_station_patreon_button();
			// 2.2.7: added WordPress.Org star rating link
			$blurb		 .= '<br><br><span style="color:#FC5;" class="dashicons dashicons-star-filled"></span> ';
			$blurb		 .= '<a class="notice-link" href="https://wordpress.org/support/plugin/radio-station/reviews/#new-post" target=_blank>' . __( 'Rate on WordPress.Org', 'radio-station' ) . '</a>';
		$blurb           .= '</li>';
	$blurb               .= '</ul>';
	if ( $dismissable ) {
		$blurb          .= '<div style="position:absolute; top:20px; right: 20px;">';
			$dismiss_url = admin_url( 'admin-ajax.php?action=radio_station_announcement_dismiss' );
			$blurb      .= '<a href="' . $dismiss_url . '" target="radio-station-notice-iframe" style="text-decoration:none;">';
			$blurb      .= '<span class="dashicons dashicons-dismiss" title="' . __( 'Dismiss this Notice', 'radio-station' ) . '"></span></a>';
		$blurb          .= '</div>';
	}
	return $blurb;
}

// ------------------------
// Patreon Supporter Button
// ------------------------
// 2.2.2: added simple patreon supporter image button
function radio_station_patreon_button() {
	// 2.2.7: added button hover opacity
	$image_url = plugins_url( 'images/patreon-button.jpg', __FILE__ );
	$button    = '<a href="https://patreon.com/radiostation" target="_blank">';
	$button   .= '<img id="radio-station-patreon-button" style="opacity:0.9;" src="' . $image_url . '" border="0">';
	$button   .= '</a>';
	$button	  .= '<style>#radio-station-patreon-button:hover {opacity:1 !important;}</style>';
	return $button;
}

// -------------------------
// MailChimp Subscriber Form
// -------------------------
function radio_station_mailchimp_form() {

	// --- enqueue MailChimp form styles ---
	$version = filemtime( RADIO_STATION_DIR . '/css/mailchimp.css' );
	$url     = plugins_url( 'css/mailchimp.css', __FILE__ );
	wp_enqueue_style( 'program-schedule', $url, array(), $version );

	// --- get current user email ---
	$current_user = wp_get_current_user();
	$user_email = $current_user->user_email;

	// --- set radio station plugin icon URL ---
	$icon = plugins_url( 'images/radio-station-icon.png', __FILE__ );

	// --- output MailChimp signup form ---
?>
<div id="mc_embed_signup">
<form action="https://netmix.us8.list-manage.com/subscribe/post?u=c53a6feec82d81974edd00a95&amp;id=7130454f20" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
	<div style="position: absolute; left: -5000px;" aria-hidden="true">
		<input type="text" name="b_c53a6feec82d81974edd00a95_7130454f20" tabindex="-1" value="">
	</div>
    <div id="mc_embed_signup_scroll">
		<div id="plugin-icon">
			<img src="<?php echo $icon; ?>">
		</div>
		<label id="signup-label" for="mce-EMAIL"><?php _e( "Stay tuned! Subscribe to Radio Station's", 'radio-station'); ?><br>
		<?php _e( 'Plugin Updates and Announcements List', 'radio-station' ); ?></label>
		<input type="email" name="EMAIL" class="email" id="mce-EMAIL" value="<?php echo $user_email; ?>" placeholder="Your email address" required>
	    <div class="subscribe">
	    	<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button">
	    </div>
    </div>
</form>
</div>

<?php
}
