<?php

/*
 * Plugin Admin Functions
 * @Since: 2.2.7
 */

// === Admin Setup ===
// - Enqueue Admin Scripts
// - Admin Style Fixes
// === Admin Menu ===
// - Setting Page Capability Check
// - Add Admin Menu and Submenu Items
// - Fix to Expand Main Menu for Submenu Items
// - Genre Taxonomy Submenu Item Fix
// - Add New Links to Admin Bar
// - Output Help Page
// - Output Export Page
// === Admin Notices ===
// - Plugin Takeover Announcement Notice
// - Plugin Takeover Announcement Content
// - Dismiss Plugin Takeover Announcement
// - Display Plugin Notice
// - Get Plugin Notices
// - AJAX Mark Notice Read
// - Patreon Supporter Button
// - MailChimp Subscriber Form


// -------------------
// === Admin Setup ===
// -------------------

// ---------------------
// Enqueue Admin Scripts
// ---------------------
// 2.2.7: removed from frontend as datepicker is only used on the backend
function radio_station_enqueue_admin_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-datepicker' );

	// TODO: include jQuery UI CSS in plugin (with related resources)
	// $url = plugins_url( 'css/jquery-ui.css', RADIO_STATION_DIR.'/radio-station.php' );
	// wp_enqueue_style( 'jquery-style', $url, array(), '1.8.2' );
	$protocol = 'http'; if ( is_ssl() ) {$protocol .= 's';}
	$url = $protocol . '://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css';
	wp_enqueue_style( 'jquery-ui-style', $url, array(), '1.8.2' );
}
add_action( 'admin_enqueue_scripts', 'radio_station_enqueue_admin_scripts' );

// -----------------
// Admin Style Fixes
// -----------------
function radio_station_admin_styles() {
	global $post;

	// --- hide first admin submenu item to prevent duplicate of main menu item ---
	$styles = ' #toplevel_page_radio-station .wp-first-item { display: none; }' . "\n";

	// --- reduce the height of the playlist editor area ---
	// 2.3.0: also reduce height of override editor area
	if ( isset( $post->post_type ) ) {
		if ( ( 'playlist' === $post->post_type ) || ( 'override' === $post->post_type ) )  {
			$styles .= ' .wp-editor-container textarea.wp-editor-area { height: 100px; }' . "\n";
		}
	}

	echo '<style type="text/css">' . $styles . '</style>';

}
add_action( 'admin_print_styles', 'radio_station_admin_styles' );


// ------------------
// === Admin Menu ===
// ------------------

// ------------------------------
// Settings Page Capability Check
// ------------------------------
// (recheck permissions for main menu item click)
add_action( 'admin_init', 'radio_station_settings_cap_check' );
function radio_station_settings_cap_check() {
	if ( isset( $_REQUEST['page'] ) && ( $_REQUEST['page'] == 'radio-station' ) ) {
		$settingscap = apply_filters( 'radio_station_settings_capability', 'manage_options' );
		if ( !current_user_can( $settingscap ) ) {
			wp_die( __( 'You do not have permissions to access that page.', 'radio-station' ) );
		}
	}
}

// --------------------------------
// Add Admin Menu and Submenu Items
// --------------------------------
function radio_station_add_admin_menus() {

	$icon       = plugins_url( 'images/radio-station-icon.png', __FILE__ );
	$position   = apply_filters( 'radio_station_menu_position', 5 );
	$settingscap = apply_filters( 'radio_station_manage_options_capability', 'manage_options' );
	$rs = __( 'Radio Station', 'radio-station' );

	// ---- main menu item ----
	// 2.3.0: set to new plugin admin page (via plugin loader class)
	// (added with publish_playlists capability so that other submenu items remain accessible)
	add_menu_page( $rs . ' ' .__( 'Settings', 'radio-station' ), __( 'Radio Station', 'radio-station' ), 'publish_playlists', 'radio-station', 'radio_station_settings_page', $icon, $position );

	// --- settings submenu item ---
	// 2.3.0: added for ease of access to plugin settings
	add_options_page( $rs . ' ' . __( 'Settings', 'radio-station' ), __( 'Settings', 'radio-station' ), $settingscap, 'radio-station-settings', 'radio_station_settings_page' );

	// --- submenu items ---
	// 2.3.0: prefix plugin name on page titles (but not menu items)
	// 2.3.0: remove add playlist and add override to reduce clutter
	add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Shows', 'radio-station' ), __( 'Shows', 'radio-station' ), 'edit_shows', 'shows' );
	add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Add Show', 'radio-station' ), __( 'Add Show', 'radio-station' ), 'publish_shows', 'add-show' );
	// if ( function_exists( 'radio_station_pro_get_episodes' ) ) {
		add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Episodes', 'radio-station' ), __( 'Episodes', 'radio-station' ), 'publish_episodes', 'episodes' );
	// }	
	add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Playlists', 'radio-station' ), __( 'Playlists', 'radio-station' ), 'edit_playlists', 'playlists' );
	// add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Add Playlist', 'radio-station' ), __( 'Add Playlist', 'radio-station' ), 'publish_playlists', 'add-playlist' );
	add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Genres', 'radio-station' ), __( 'Genres', 'radio-station' ), 'publish_playlists', 'genres' );
	add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Schedule Overrides', 'radio-station' ), __( 'Schedule Overrides', 'radio-station' ), 'edit_shows', 'schedule-overrides' );
	// add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Add Override', 'radio-station' ), __( 'Add Override', 'radio-station' ), 'publish_shows', 'add-override' );
	// add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Hosts', 'radio-station' ), __( 'Hosts', 'radio-station' ), 'edit_hosts', 'hosts' );
	// add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Producers', 'radio-station' ), __( 'Producers', 'radio-station' ), 'edit_producers', 'producers' );
	add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Export Playlists', 'radio-station' ), __( 'Export Playlists', 'radio-station' ), $settingscap, 'playlist-export', 'radio_station_admin_export' );
	add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Settings', 'radio-station' ), __( 'Settings', 'radio-station' ), $settingscap, 'radio-station-settings', 'radio_station_settings_page' );
	add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Help', 'radio-station' ), __( 'Help', 'radio-station' ), 'publish_playlists', 'radio-station-help', 'radio_station_plugin_help' );

	// --- hack the submenu global to add post type add/edit URLs ---
	global $submenu;
	foreach ( $submenu as $i => $menu ) {
		if ( 'radio-station' === $i ) {
			foreach ( $menu as $j => $item ) {
				switch ( $item[2] ) {
					case 'add-show':
						// 2.3.0: removed capability check as menu cap is already publish_shows
						$submenu[ $i ][ $j ][2] = 'post-new.php?post_type=show';
						break;
					case 'shows':
						$submenu[ $i ][ $j ][2] = 'edit.php?post_type=show';
						break;
					case 'episodes':
						$submenu[ $i ][ $j ][2] = 'edit.php?post_type=episode';
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
					case 'hosts':
						$submenu[ $i ][ $j ][2] = 'post-new.php?post_type=hosts';
						break;
					case 'producers':
						$submenu[ $i ][ $j ][2] = 'post-new.php?post_type=producers';
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

// -----------------------
// Role Editor Information
// -----------------------
// 2.3.0: added for upcoming (Pro) role editor feature
// add_action( 'radio_station_admin_page_tab_roles_bottom', 'radio_station_role_editor' );
function radio_station_role_editor() {
	echo "<h3>" . __( 'Role Editor', 'radio-station' ) . "</h3>";
	echo __( 'You can assign a Radio Station role to users through the WordPress user editor.', 'radio-station' );

	// TODO: info regarding role editor feature
	echo __( '', 'radio-station' );
	$upgrade_url = radio_station_get_upgrade_url();
	echo "<a href='" . $upgrade_url . "'>";
	 	echo __( 'Upgrade to Radio Station Pro', 'radio-station' );
	echo "</a>";
}

// ----------------
// Output Help Page
// ----------------
function radio_station_plugin_help() {

	// --- output announcement content ---
	// 2.2.2: include patreon button link
	echo radio_station_announcement_content( false );

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


// ---------------------
// === Admin Notices ===
// ---------------------

// -----------------------------------
// Plugin Takeover Announcement Notice
// -----------------------------------
// 2.2.2: added plugin announcement notice
function radio_station_announcement_notice() {

	// --- bug out if already dismissed ---
	if ( get_option( 'radio_station_announcement_dismissed' ) ) {return;}

	// --- bug out on certain plugin pages ---
	$pages = array( 'radio-station', 'radio-station-help' );
	// 2.2.8: remove strict in_array checking
	if ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $pages ) ) {return;}

	// --- display plugin announcement ---
	echo '<div id="radio-station-announcement-notice" class="notice notice-info" style="position:relative;">';
		echo radio_station_announcement_content();
	echo '</div>';

	// --- notice dismissal frame (once) ---
	global $radio_station_notice_frame;
	if ( !isset( $radio_station_notice_iframe ) ) {	
		echo '<iframe src="javascript:void(0);" name="radio-station-notice-iframe" style="display:none;"></iframe>';
		$radio_station_notice_iframe = true;
	}

}
add_action( 'admin_notices', 'radio_station_announcement_notice' );

// ------------------------------------
// Plugin Takeover Announcement Content
// ------------------------------------
// 2.2.2: added simple patreon supporter blurb
function radio_station_announcement_content( $dismissable = true ) {

	// --- takeover announcement ---
	$plugin_image = plugins_url( 'images/radio-station.png', __FILE__ );
	$blurb = '<ul style="list-style:none;">';
		$blurb .= '<li style="display:inline-block; vertical-align:middle;">';
			$blurb .= '<img src="' . $plugin_image . '">';
		$blurb .= '</li>';
		
		$blurb .= '<li style="display:inline-block; vertical-align:middle; margin-left:40px; font-size:16px; line-height:24px;">';
			$blurb .= '<b style="font-size:17px;">' . __( 'Help support us to make improvements, modifications and introduce new features!', 'radio-station' ) . '</b><br>';
			$blurb .= __( 'With over a thousand radio station users thanks to the original plugin author Nikki Blight', 'radio-station' ) . ', <br>';
			$blurb .= __( 'since June 2019', 'radio-station' ) . ', ';
			$blurb .= '<b>' . __( 'Radio Station', 'radio-station' ) . '</b> ';
			$blurb .= __( ' plugin development has been actively taken over by', 'radio-station' );
			$blurb .= ' <a href="http://netmix.com" target="_blank">Netmix</a>.<br>';
			$blurb .= __( 'We invite you to', 'radio-station' );
			$blurb .= ' <a href="https://patreon.com/radiostation" target="_blank">';
				$blurb .= __( 'Become a Radio Station Patreon Supporter', 'radio-station' );
			$blurb .= '</a> ' . __( 'to make it better for everyone', 'radio-station' ) . '!';
		$blurb .= '</li>';
		$blurb .= '<li style="display:inline-block; text-align:center; vertical-align:middle; margin-left:40px;">';
			$blurb .= radio_station_patreon_button();
			// 2.2.7: added WordPress.Org star rating link
			$blurb .= '<br><br><span style="color:#FC5;" class="dashicons dashicons-star-filled"></span> ';
			$blurb .= '<a class="notice-link" href="https://wordpress.org/support/plugin/radio-station/reviews/#new-post" target=_blank>' . __( 'Rate on WordPress.Org', 'radio-station' ) . '</a>';
		$blurb .= '</li>';
	$blurb  .= '</ul>';

	if ( $dismissable ) {
		$blurb .= '<div style="position:absolute; top:20px; right: 20px;">';
			$dismiss_url = admin_url( 'admin-ajax.php?action=radio_station_announcement_dismiss' );
			$blurb .= '<a href="' . $dismiss_url . '" target="radio-station-notice-iframe" style="text-decoration:none;">';
			$blurb .= '<span class="dashicons dashicons-dismiss" title="' . __( 'Dismiss this Notice', 'radio-station' ) . '"></span></a>';
		$blurb .= '</div>';
	}
	return $blurb;
}

// ------------------------------------
// Dismiss Plugin Takeover Announcement
// ------------------------------------
// 2.2.2: AJAX for takeover announcement notice dismissal
function radio_station_announcement_dismiss() {
	if ( current_user_can( 'manage_options' ) || current_user_can( 'update_plugins' ) ) {
		update_option( 'radio_station_announcement_dismissed', true );
		echo "<script>parent.document.getElementById('radio-station-announcement-notice').style.display = 'none';</script>";
		exit;
	}
}
add_action( 'wp_ajax_radio_station_announcement_dismiss', 'radio_station_announcement_dismiss' );

// --------------------------
// Show Shift Conflict Notice
// --------------------------
function radio_station_shift_conflict_notice() {
	
	if ( !current_user_can( 'edit_shows' ) ) {return;}
	
	// note: calling this will run the shift conflict checker
	$show_shifts = radio_station_get_show_shifts();
	
	// --- display any shift conflicts found ---
	$conflicts = get_option( 'radio_station_schedule_conflicts' );
	if ( $conflicts && is_array( $conflicts ) && ( count( $conflicts ) > 0 ) ) {
	
		echo '<div id="radio-station-conflicts" class="notice notice-error" style="position:relative;">';

			echo '<ul style="list-style:none;">';

				echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-left:40px;">';
					echo '<b>' . __( 'Radio Station', 'radio-station' ) . '</b><br>';
					echo __( 'has detected', 'radio-station' ) . '<br>';
					echo __( 'Schedule conflicts!', 'radio-station' );
				echo '</li>';

				echo '<li style="display:inline-block; vertical-align:top; margin-left:40px;">';
					echo '<b>' . __( 'The following Shows have conflicting Shift times', 'radio-station' ) . ":</b><br>";
					
					echo '<ul style="list-style:none; margin-top:5px;">';
					$edit_link = add_query_arg( 'action', 'edit', admin_url( 'post.php' ) );
					foreach ( $conflicts as $show => $show_conflicts ) {
						foreach ( $show_conflicts as $conflict ) {
							if ( !$conflict['duplicate'] ) {
								echo '<li>';
									$show_edit_link = add_query_arg( 'post', $conflict['show'], $edit_link );
									$show_title = get_the_title( $conflict['show'] );							
									echo '<a href="' . $show_edit_link . '">' . $show_title . '</a>';
									if ( $conflict['disabled'] ) {$disabled = ' [disabled] ';} else {$disabled = '';}
									echo ' (' . $disabled . $conflict['day'] . ' ' . $conflict['start'] . ' - ' . $conflict['end'] . ')';
									echo ' ' . __( 'with', 'radio-station' ) . ' ';
									$show_edit_link = add_query_arg( 'post', $conflict['with_show'], $edit_link );
									$show_title = get_the_title( $conflict['with_show'] );
									echo '<a href="' . $show_edit_link . '">' . $show_title . '</a>';
									if ( $conflict['with_disabled'] ) {$disabled = ' [disabled] ';} else {$disabled = '';}
									echo ' (' . $disabled . $conflict['with_day'] . ' ' . $conflict['with_start'] . ' - ' . $conflict['with_end'] . ')';
								echo '</li>';
							}
						}
					}	
					echo '</ul>';
				echo '</li>';

				// --- show list link ---
				// TODO: say here that conflicts are highlighted in show list
				echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-left:40px;">';
					$show_list_url = add_query_arg( 'post_type', 'show', admin_url( 'edit.php' ) );
					echo '<a class="button" style="margin-bottom:5px;" href="' . $show_list_url . '">' . esc_html( __( 'Go to Show List', 'radio-station' ) ) . ' &rarr;</a><br>';
					echo __( 'Conflicts are highlighted', 'radio-station' ) . '<br>';
					echo __( 'in Show Shift column.', 'radio-station');
				echo '</li>';

				// --- undismissable error notice ---
				echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-left:40px;">';
					echo __( 'This notice will persist', 'radio-station' ) . '<br>';
					echo __( 'until conflicts are resolved.', 'radio-station' );
				echo '</li>';

			echo '</ul>';

		echo '</div>';
		
	}
}
add_action( 'admin_notices', 'radio_station_shift_conflict_notice' );

// ----------------------
// Display Upgrade Notice
// ----------------------
function radio_station_upgrade_notice() {

	// --- bug out if not update plugins capability ---
	if ( !current_user_can( 'update_plugins' ) ) {return;}

	// --- check updates transient for upgrade notices ---
	$pluginslug = 'radio-station';
	$pluginupdates = get_site_transient('update_plugins');
	if ( property_exists( $pluginupdates, 'response' ) ) {
		foreach ( $pluginupdates->response as $file => $update ) {
			if ( $update->slug == $pluginslug ) {
				if ( property_exists( $update, 'upgrade_notice' ) ) {
					$pluginfile = $file;
					$notice = $update->upgrade_notice;
					$new_version = $update->new_version;
					$update_id = str_replace( '.', '', $new_version );
					if ( property_exists( $update, 'icons') && isset( $update->icons['1x'] ) ) {
						$icon_url = $update->icons['1x'];
					}
					break;
				}
			}
		}
	}
	if ( !isset( $notice ) ) {return;}

	// --- ignore if updating now ---
	if ( isset( $_GET['action'] ) && ( $_GET['action'] == 'upgrade-plugin' ) 
	  && isset( $_GET['plugin'] ) && ( $_GET['plugin'] == $pluginfile ) ) {return;}

	// --- bug out if already read ---
	$read = get_option( 'radio_station_read_upgrades' );
	if ( $read && isset( $read[$update_id] ) && ( $read[$update_id] == '1' ) ) {return;}

	// --- extract notice URL from upgrade notice ---
	$notice = radio_station_parse_upgrade_notice( $notice );

	// --- set plugin update URL ---	
	$update_url = admin_url( 'update.php' ).'?action=upgrade-plugin&plugin='.$pluginfile;
	$update_url = wp_nonce_url( $update_url, 'upgrade-plugin_'.$pluginfile );

	// --- output update available notice ---
	echo '<div id="radio-station-upgrade-' . $update_id . '" class="notice notice-warning" style="position:relative;">';
	
		echo '<ul style="list-style:none;">';
		
			if ( isset( $icon_url ) ) {
				echo '<li style="display:inline-block; vertical-align:top;">';
					echo '<img src="' . $icon_url . '" style="width:50px; height: 50px;">';
				echo '</li>';
			}
	
			echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-left:40px;">';
				echo __( 'A new version of','radio-station' ) . '<br>';
				echo '<b>' . __( 'Radio Station', 'radio-station' ) . '</b> ';
				echo __( 'is available.', 'radio-station' );
			echo '</li>';

			echo '<li style="display:inline-block; vertical-align:top; margin-left:40px;">';
				echo __( 'Take a moment to Upgrade for a better experience. In this update...', 'radio-station' ) . "<br>";
				echo $notice['content'];
			echo '</li>';

			if ( $notice['url'] != '' ) {
				echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-left:40px;">';
					echo '<a class="button" href="' . $notice['url'] . '" target="_blank">' . esc_html( __( 'Update Details', 'radio-station' ) ) . ' &rarr;</a>';
				echo '</li>';
			}
			
			echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-left:40px;">';
				echo '<a class="button button-primary" href="' . $update_url . '">' . esc_html( __( 'Update Now', 'radio-station' ) ) . '</a>';
			echo '</li>';
			
		echo '</ul>';
	
		echo '<div style="position:absolute; top:20px; right: 20px;">';
			$dismiss_url = add_query_arg( 'action', 'radio_station_notice_dismiss', admin_url( 'admin-ajax.php' ) );
			$dismiss_url = add_query_arg( 'upgrade', $update_id, $dismiss_url );
			echo '<a href="' . $dismiss_url . '" target="radio-station-notice-iframe" style="text-decoration:none;">';
			echo '<span class="dashicons dashicons-dismiss" title="' . __( 'Dismiss this Notice', 'radio-station' ) . '"></span></a>';
		echo '</div>';

	echo '</div>';

	// --- notice dismissal iframe (once only) ---
	global $radio_station_notice_iframe;
	if ( !isset( $radio_station_notice_iframe ) ) {
		echo '<iframe src="javascript:void(0);" name="radio-station-notice-iframe" style="display:none;"></iframe>';
		$radio_station_notice_iframe = true;
	}
}
add_action( 'admin_notices', 'radio_station_upgrade_notice' );

// ---------------------
// Display Plugin Notice 
// ---------------------
// 2.3.0: added 2.3.0 template update announcement notice
function radio_station_notice() {

	// --- bug out on plugin admin pages ---
	$pages = array( 'radio-station', 'radio-station-help' );
	if ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $pages ) ) {return;}

	// --- get latest notice ---
	$notices = radio_station_get_notices();
	if ( count( $notices ) < 1 ) {return;}
	$notice_ids = array_keys( $notices );
	$notice_id = max( $notice_ids );
	$notice = $notices[$notice_id];

	// --- bug out if already read ---
	$read = get_option( 'radio_station_read_notices' );
	if ( $read && isset( $read[$notice_id] ) && ( $read[$notice_id] == '1' ) ) {return;}

	// --- display plugin notice ---
	echo '<div id="radio-station-notice-' . $notice['id'] . '" class="notice notice-info" style="position:relative;">';
	
		// --- output plugin notice text ---
		echo '<ul style="list-style:none;">';
		
			echo '<li style="display:inline-block; text-align:center; vertical-align:top;">';
				echo '<b>' . esc_html( __( 'Radio Station', 'radio-station') ) . '</b><br>';
				echo '<b>' . esc_html( __( 'Update Notice', 'radio-station') ) . '</b>';
			echo '</li>';

			echo '<li style="display:inline-block; vertical-align:top; margin-left:40px; font-size:16px; line-height:24px;">';
				echo '<div style="margin-bottom:10px;">';
					echo '<b>' . __( 'Thanks for Upgrading! You can enjoy these improvements now', 'radio-station' ) . '</b>:';
				echo '</div>';
				echo $notice['content'];	
			echo '</li>';

			if ( isset( $notice['url'] ) && ( $notice['url'] != '' ) ) {
				echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-left:40px;">';
					echo '<a class="button" href="' . $notice['url'] . '">' . esc_html( __( 'Update Details', 'radio-station' ) ) . ' &rarr;</a>';
				echo '</li>';
			}

			$settings_url = add_query_arg( 'page', 'radio-station', admin_url('admin.php') );
			echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-left:40px;">';
				echo '<a class="button button-primary" href="' . $settings_url . '">' . esc_html( __( 'Plugin Settings', 'radio-station' ) ) . '</a>';
			echo '</li>';
			
		echo '</ul>';

		// --- notice dismissal button ---
		echo '<div style="position:absolute; top:20px; right: 20px;">';
			$dismiss_url = add_query_arg( 'action', 'radio_station_notice_dismiss', admin_url( 'admin-ajax.php ') );
			$dismiss_url = add_query_arg( 'notice', $notice['id'], $dismiss_url );
			echo '<a href="' . $dismiss_url . '" target="radio-station-notice-iframe" style="text-decoration:none;">';
			echo '<span class="dashicons dashicons-dismiss" title="' . __( 'Dismiss this Notice', 'radio-station' ) . '"></span></a>';
		echo '</div>';
	echo '</div>';

	// --- notice dismissal iframe (once only) ---
	global $radio_station_notice_iframe;
	if ( !isset( $radio_station_notice_iframe ) ) {
		echo '<iframe src="javascript:void(0);" name="radio-station-notice-iframe" style="display:none;"></iframe>';
		$radio_station_notice_iframe = true;
	}
}
add_action( 'admin_notices', 'radio_station_notice' );

// ------------------
// Get Plugin Notices
// ------------------
// 2.3.0: added get notices helper
function radio_station_get_notices() {

	// --- check for needed files ---
	$readme = RADIO_STATION_DIR . '/readme.txt';
	$parser = RADIO_STATION_DIR . '/reader.php';

	// --- get upgrade notices ---
	$notices = array();
	if ( file_exists( $parser ) && file_exists( $readme ) ) {

		// --- get readme contents ---
		$contents = file_get_contents( $readme );
		
		// --- fix to parser failing on license lines ---
		$contents = str_replace('License: GPLv2 or later', '', $contents);
		$contents = str_replace('License URI: http://www.gnu.org/licenses/gpl-2.0.html', '', $contents);
		
		// --- include Markdown Readme Parser ---
		include( $parser );
		$readme = new WordPress_Readme_Parser;
		$parsed = $readme->parse_readme_contents( $contents );
		
		// --- parse all the notices to get notice info ---
		if ( isset( $parsed['upgrade_notice'] ) ) {
			$notices = array();	
			foreach ( $parsed['upgrade_notice'] as $version => $notice ) {		
				if ( trim( $notice ) != '' ) {
					$id = str_replace( '.', '', $version );
					$notice = radio_station_parse_upgrade_notice( $notice );
					$notices[$id] = array( 
						'id'		=> $id,
						'version'	=> $version,
						'url'		=> $notice['url'],
						'content'	=> $notice['content'],
					);
				}
			}
		}
	}

	return $notices;
}

// --------------------
// Parse Upgrade Notice
// --------------------
function radio_station_parse_upgrade_notice( $notice ) {
	
	if ( strstr( $notice, "\n" ) ) {
		$contents = explode( "\n", $notice );
		$lines = array(); $notice_url = '';
		foreach ( $contents as $content ) {
			if ( trim( $content ) != '' ) {
				// --- extract link from line ---
				if ( strstr( $content, 'http' ) ) {
					$pos = strpos( $content, 'http' ); 
					$chunks = str_split( $content, $pos );
					unset( $chunks[0] ); 
					$remainder = implode( '', $chunks );
					$breaks = array( ' ', '<', "\n", "\r" ); 
					$pos = array();
					foreach ( $breaks as $i => $urlbreak ) {
						if (strstr( $remainder, $urlbreak ) ) {$pos[$i] = strpos( $remainder, $urlbreak );}
					}
					if ( count( $pos ) > 0 ) {
						$pos = min( $pos );
						$chunks = str_split( $remainder, $pos );
						$notice_url = $chunks[0];
					} else {
						$notice_url = $remainder;
					}
					$content = str_replace( $notice_url, '', $content );
					$content = str_replace( '<li>', '', $content );
					$content = str_replace( '</li>', '', $content );
				}
			}
			if ( trim( $content ) != '' ) {$lines[] = $content;}
		}
	}
	
	// --- recombine lines and return ---
	$content = implode( "\n", $lines );	
	$notice = array( 'content' => $content, 'url' => $notice_url );
	return $notice;	
}

// ---------------------
// AJAX Mark Notice Read
// ---------------------
// 2.3.0: added for plugin notice dismissal
function radio_station_notice_dismiss() {
	if ( current_user_can( 'manage_options' ) || current_user_can( 'update_plugins' ) ) {
		if ( isset( $_GET['notice'] ) ) {
			$notice = absint( $_GET['notice'] );
			if ( $notice < 0 ) {return;}
			$notices = get_option( 'radio_station_read_notices' );
			if ( !$notices ) {$notices = array();}
			$notices[$notice] = '1';
			update_option( 'radio_station_read_notices', $notices );
			echo "<script>parent.document.getElementById('radio-station-notice-" . esc_js( $notice ) . "').style.display = 'none';</script>";
		} elseif ( isset( $_GET['upgrade'] ) ) {
			$upgrade = absint( $_GET['upgrade'] );
			if ( $upgrade < 0 ) {return;}
			$upgrades = get_option( 'radio_station_read_upgrades' );
			if ( !$upgrades ) {$upgrades = array();}
			$upgrades[$upgrade] = '1';
			update_option( 'radio_station_read_upgrades', $versions );
			echo "<script>parent.document.getElementById('radio-station-upgrade-" . esc_js( $upgrade ) . "').style.display = 'none';</script>";
		}
	}
	exit;
}
add_action( 'wp_ajax_radio_station_notice_dismiss', 'radio_station_notice_dismiss' );

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
	// 2.3.0: prefix mailchimp CSS file
	$version = filemtime( RADIO_STATION_DIR . '/css/rs-mailchimp.css' );
	$url     = plugins_url( 'css/rs-mailchimp.css', __FILE__ );
	wp_enqueue_style( 'program-schedule', $url, array(), $version, 'all' );

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
