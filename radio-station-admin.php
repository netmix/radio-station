<?php
/*
 * Radio Station Plugin Admin Functions
 * @Since: 2.2.7
 */

if ( !defined( 'ABSPATH' ) ) exit;

// === Admin Setup ===
// - Enqueue Admin Scripts
// - Admin Style Fixes
// - Filter Plugin Action Links
// === Admin Menu ===
// - Setting Page Capability Check
// - Add Admin Menu and Submenu Items
// - Fix to Expand Main Menu for Submenu Items
// - Taxonomy Submenu Item Fix
// - Fix to Redirect Plugin Settings Menu Link
// - Display Import/Export Show Page
// - Display Plugin Docs Page
// - Parse Markdown Doc File
// x Display Playlist Export Page
// === Update Notices ===
// - Get Plugin Upgrade Notice
// - Parse Plugin Update Notice
// - Plugin Page Update Message
// - Display Admin Update Notice
// - Display Plugin Notice
// - Get Plugin Notices
// - AJAX Mark Notice Read
// === Admin Notices ===
// - Plugin Settings Page Top
// - Plugin Settings Page Bottom
// - Directory Listing Offer Notice
// - Launch Offer Notice
// - Directory Listing Offer Content
// - Launch Offer Content
// - Dismiss Free Listing Offer
// - Plugin Takeover Announcement Notice
// - Plugin Takeover Announcement Content
// - Dismiss Plugin Takeover Announcement
// - Show Shift Conflict Notice
// - MailChimp Subscriber Form
// - AJAX Record Subscriber
// - AJAX Clear Notices


// -------------------
// === Admin Setup ===
// -------------------

// ---------------------
// Enqueue Admin Scripts
// ---------------------
// 2.2.7: removed from frontend as datepicker is only used on the backend
add_action( 'admin_enqueue_scripts', 'radio_station_enqueue_admin_scripts' );
function radio_station_enqueue_admin_scripts() {

	// --- enqueue admin js file ---
	$script = radio_station_get_template( 'both', 'radio-station-admin.js', 'js' );
	$version = filemtime( $script['file'] );
	$deps = array( 'jquery' );
	wp_enqueue_script( 'radio-station-admin', $script['url'], $deps, $version, true );

	if ( RADIO_STATION_DEBUG ) {
		$js = "radio_admin.debug = true;";
		// 2.5.0: use radio_station_add_inline_script
		radio_station_add_inline_script( 'radio-station-admin', $js );
	}

	// --- enqueue admin styles ---
	radio_station_enqueue_style( 'admin' );

	// 2.5.0: maybe enqueue pricing page styles
	if ( isset( $_REQUEST['page'] ) && ( 'radio-station-pricing' == sanitize_text_field( $_REQUEST['page'] ) ) ) {
		$style_url = plugins_url( 'freemius-pricing/freemius-pricing.css', RADIO_STATION_FILE );
		$style_path = RADIO_STATION_DIR . '/freemius-pricing/freemius-pricing.css';
		$version = filemtime( $style_path );
		wp_enqueue_style( 'freemius-pricing', $style_url, array(), $version, 'all' );
	}

}

// -----------------
// Admin Style Fixes
// -----------------
add_action( 'admin_print_styles', 'radio_station_admin_styles' );
function radio_station_admin_styles() {

	// --- hide first admin submenu item to prevent duplicate of main menu item ---
	$css = '#toplevel_page_radio-station .wp-first-item {display: none;}' . "\n";
	$css .= '#toplevel_page_radio-station-pro .wp-first-item {display: none;}' . "\n";

	// --- reduce the height of the playlist editor area ---
	// 2.3.0: also reduce height of override editor area
	// 2.3.0: change isset to is_object and property exists check
	global $post;
	if ( is_object( $post ) && property_exists( $post, 'post_type' ) ) {
		if ( ( RADIO_STATION_PLAYLIST_SLUG === $post->post_type )
			|| ( RADIO_STATION_OVERRIDE_SLUG === $post->post_type ) ) {
			$css .= '.wp-editor-container textarea.wp-editor-area {height: 100px;}' . "\n";
		}
	}

	// --- filter admin styles ---
	// 2.5.0: added admin style filter
	$css = apply_filters( 'radio_station_admin_styles', $css );

	// --- output admin styles ---
	// 2.5.6: use wp_kses_post instead of wp_strip_all_tags
	// 2.5.6: use radio_station_add_inline_style
	// echo '<style>' . wp_kses_post( $css ) . '</style>' . "\n";
	radio_station_add_inline_style( 'rs-admin', $css );

}

// --------------------------
// Filter Plugin Action Links
// --------------------------
// 2.4.0.3: filter license activation link for free version on plugin page
// note: this is because Pro is a separate plugin not a replacement one

// add_filter( 'plugin_action_links', 'radio_station_plugin_links', 99, 2 );
/* function radio_station_plugin_links( $links, $file ) {
	if ( RADIO_STATION_BASENAME == $file ) {
		echo '<span style="display:none;">RS Plugin Links: ' . esc_html( print_r( $links, true ) ) . '</span>';
	}
	return $links;
} */

add_filter( 'plugin_action_links_' . RADIO_STATION_BASENAME, 'radio_station_plugin_page_links', 20, 2 );
// add_filter( 'network_admin_plugin_action_links_' . RADIO_STATION_BASENAME, , 'radio_station_license_activation_link', 20, 2 );
function radio_station_plugin_page_links( $links, $file ) {

	global $radio_station_data;

	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">RS Plugin Links A: ' . esc_html( print_r( $links, true ) ) . '</span>' . "\n";
	}

	foreach ( $links as $key => $link ) {

		// if ( RADIO_STATION_DEBUG ) {
		// 	echo '<span style="display:none;">Plugin Link ' . $key . ': ' . $link . '</span>' . PHP_EOL;
		// }

		// 2.4.0.6: remove addons link from Free by default
		if ( strstr( $link, '-addons' ) ) {
			if ( !$radio_station_data['settings']['hasaddons'] ) {
				unset( $links[$key] );
			}
		}

		// --- remove activate premium license link from free version ---
		// 2.4.0.8: moved handling of this to within Pro
		// if ( 'activate-license radio-station' == $key ) {
		//	if ( !defined( 'RADIO_STATION_PRO_FILE' ) && !$radio_station_data['settings']['hasaddons'] ) {
		//		unset( $links[$key] );
		//	}
		// }

		// --- remove upgrade link if Pro is already installed ---
		if ( defined( 'RADIO_STATION_PRO_FILE' ) && strstr( $key, 'upgrade' ) ) {
			unset( $links[$key] );
		}
	}

	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">RS Plugin Links B: ' . esc_html( print_r( $links, true ) ) . '</span>' . "\n";
	}

	return $links;
}


// ------------------
// === Admin Menu ===
// ------------------

// ------------------------------
// Settings Page Capability Check
// ------------------------------
// (recheck permissions for main menu item click)
add_action( 'admin_init', 'radio_station_settings_cap_check' );
function radio_station_settings_cap_check() {
	if ( isset( $_REQUEST['page'] ) && ( RADIO_STATION_SLUG == sanitize_text_field( $_REQUEST['page'] ) ) ) {
		$settingscap = apply_filters( 'radio_station_settings_capability', 'manage_options' );
		if ( !current_user_can( $settingscap ) ) {
			wp_die( esc_html( __( 'You do not have permissions to access that page.', 'radio-station' ) ) );
		}
	}
}

// --------------------------------
// Add Admin Menu and Submenu Items
// --------------------------------
add_action( 'admin_menu', 'radio_station_add_admin_menus' );
function radio_station_add_admin_menus() {

	$icon = plugins_url( 'images/radio-station-icon.png', RADIO_STATION_FILE );
	$position = apply_filters( 'radio_station_menu_position', 5 );
	$settingscap = apply_filters( 'radio_station_manage_options_capability', 'manage_options' );
	$rs = __( 'Radio Station', 'radio-station' );

	// ---- main menu item ----
	// 2.3.0: set to new plugin admin page (via plugin loader class)
	// (added with publish_playlists capability so that other submenu items remain accessible)
	add_menu_page( $rs . ' ' . __( 'Settings', 'radio-station' ), $rs, 'publish_playlists', 'radio-station', 'radio_station_settings_page', $icon, $position );

	// --- settings submenu item ---
	// 2.3.0: added for ease of access to plugin settings
	add_options_page( $rs . ' ' . __( 'Settings', 'radio-station' ), $rs, $settingscap, 'radio-station', 'radio_station_settings_page' );

	// --- submenu items ---
	// 2.3.0: prefix plugin name on page titles (but not menu items)
	// 2.3.0: remove add playlist and add override to reduce clutter
	// 2.3.0: added actions for adding of other plugin submenu items in position
	// 2.5.0: disabled Add Show submenu item to reduce clutter
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Shows', 'radio-station' ), __( 'Shows', 'radio-station' ), 'edit_shows', 'shows' );
	// add_submenu_page( 'radio-station', $rs . ' ' . __( 'Add Show', 'radio-station' ), __( 'Add Show', 'radio-station' ), 'publish_shows', 'add-show' );
	do_action( 'radio_station_admin_submenu_top' );
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Playlists', 'radio-station' ), __( 'Playlists', 'radio-station' ), 'edit_playlists', 'playlists' );
	// add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Add Playlist', 'radio-station' ), __( 'Add Playlist', 'radio-station' ), 'publish_playlists', 'add-playlist' );
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Genres', 'radio-station' ), __( 'Genres', 'radio-station' ), 'publish_playlists', 'genres' );
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Schedule Overrides', 'radio-station' ), __( 'Schedule Overrides', 'radio-station' ), 'edit_shows', 'schedule-overrides' );
	// add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Add Override', 'radio-station' ), __( 'Add Override', 'radio-station' ), 'publish_shows', 'add-override' );
	do_action( 'radio_station_admin_submenu_middle' );
	// add_submenu_page( 'radio-station', $rs . ' ' . __( 'Hosts', 'radio-station' ), __( 'Hosts', 'radio-station' ), 'edit_hosts', 'hosts' );
	// add_submenu_page( 'radio-station', $rs . ' ' . __( 'Producers', 'radio-station' ), __( 'Producers', 'radio-station' ), 'edit_producers', 'producers' );

	// 2.3.2: as temporarily disabled, allow enabling export playlists via filter
	$export_playlists = apply_filters( 'radio_station_export_playlists', false );
	if ( $export_playlists ) {
		add_submenu_page( 'radio-station', $rs . ' ' . __( 'Export Playlists', 'radio-station' ), __( 'Export Playlists', 'radio-station' ), $settingscap, 'playlist-export', 'radio_station_playlist_export_page' );
	}

	// --- import / export shows feature ---
	// note: not yet implemented in free version
	if ( file_exists( RADIO_STATION_DIR . '/includes/import-export.php' ) ) {
		add_submenu_page( 'radio-station', $rs . ' ' . __( 'Import/Export Shows', 'radio-station' ), __( 'Import/Export', 'radio-station' ), 'manage_options', 'import-export-shows', 'radio_station_import_export_show_page' );
	}

	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Settings', 'radio-station' ), __( 'Settings', 'radio-station' ), $settingscap, 'radio-station', 'radio_station_settings_page' );
	// 2.3.0: rename Help page to Documentation
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Documentation', 'radio-station' ), __( 'Help', 'radio-station' ), 'publish_playlists', 'radio-station-docs', 'radio_station_plugin_docs_page' );

	do_action( 'radio_station_admin_submenu_bottom' );

	// --- hack the submenu global to add post type add/edit URLs ---
	global $submenu;
	foreach ( $submenu as $i => $menu ) {
		if ( 'radio-station' === $i ) {
			foreach ( $menu as $j => $item ) {
				switch ( $item[2] ) {
					case 'add-show':
						// 2.3.0: removed capability check as menu cap is already publish_shows
						$submenu[$i][$j][2] = 'post-new.php?post_type=' . RADIO_STATION_SHOW_SLUG;
						break;
					case 'shows':
						$submenu[$i][$j][2] = 'edit.php?post_type=' . RADIO_STATION_SHOW_SLUG;
						break;
					case 'episodes':
						if ( defined( 'RADIO_STATION_EPISODE_SLUG' ) ) {
							$submenu[$i][$j][2] = 'edit.php?post_type=' . RADIO_STATION_EPISODE_SLUG;
						}
						break;
					case 'playlists':
						$submenu[$i][$j][2] = 'edit.php?post_type=' . RADIO_STATION_PLAYLIST_SLUG;
						break;
					case 'add-playlist':
						$submenu[$i][$j][2] = 'post-new.php?post_type=' . RADIO_STATION_PLAYLIST_SLUG;
						break;
					case 'genres':
						$submenu[$i][$j][2] = 'edit-tags.php?taxonomy=' . RADIO_STATION_GENRES_SLUG;
						break;
					case 'schedule-overrides':
						$submenu[$i][$j][2] = 'edit.php?post_type=' . RADIO_STATION_OVERRIDE_SLUG;
						break;
					case 'add-override':
						$submenu[$i][$j][2] = 'post-new.php?post_type=' . RADIO_STATION_OVERRIDE_SLUG;
						break;
					case 'hosts':
						$submenu[$i][$j][2] = 'edit.php?post_type=' . RADIO_STATION_HOST_SLUG;
						break;
					case 'producers':
						$submenu[$i][$j][2] = 'edit.php?post_type=' . RADIO_STATION_PRODUCER_SLUG;
						break;
				}
			}
		}
	}
}

// -----------------------------------------
// Fix to Expand Main Menu for Submenu Items
// -----------------------------------------
// 2.2.2: added fix for genre taxonomy page and post type editing
// 2.2.8: remove strict in_array checking
add_filter( 'parent_file', 'radio_station_fix_genre_parent', 11 );
function radio_station_fix_genre_parent( $parent_file = '' ) {
	global $pagenow, $post;
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_PLAYLIST_SLUG, RADIO_STATION_OVERRIDE_SLUG );
	$taxonomies = array( RADIO_STATION_GENRES_SLUG, RADIO_STATION_LANGUAGES_SLUG );
	if ( ( 'edit-tags.php' === $pagenow ) && isset( $_GET['taxonomy'] ) && in_array( sanitize_text_field( $_GET['taxonomy'] ), $taxonomies ) ) {
		// 2.3.0: also apply to language taxonomy
		$parent_file = 'radio-station';
	} elseif ( ( 'post.php' === $pagenow ) && in_array( $post->post_type, $post_types ) ) {
		$parent_file = 'radio-station';
	}

	return $parent_file;
}

// -------------------------
// Taxonomy Submenu Item Fix
// -------------------------
// 2.2.2: so genre submenu item link is set to current (bold)
// 2.3.0: change action to admin_enqueue_scripts
add_action( 'admin_enqueue_scripts', 'radio_station_taxonomy_submenu_fix', 11 );
function radio_station_taxonomy_submenu_fix() {

	global $pagenow;
	if ( ( 'edit-tags.php' === $pagenow ) && isset( $_GET['taxonomy'] ) ) {

		$js = '';
		if ( RADIO_STATION_GENRES_SLUG == sanitize_text_field( $_GET['taxonomy'] ) ) {
			$js = "jQuery('#toplevel_page_radio-station ul li').each(function() {
	    		if (jQuery(this).find('a').attr('href') == 'edit-tags.php?taxonomy=" . esc_js( RADIO_STATION_GENRES_SLUG ) . "') {
		    		jQuery(this).addClass('current').find('a').addClass('current').attr('aria-current', 'page');
			    }
		    });";
		}

		// 2.3.0: add fix for language taxonomy also
		if ( RADIO_STATION_LANGUAGES_SLUG == sanitize_text_field( $_GET['taxonomy'] ) ) {
			$js = "jQuery('#toplevel_page_radio-station ul li').each(function() {
	    		if (jQuery(this).find('a').attr('href') == 'edit-tags.php?taxonomy=" . esc_js( RADIO_STATION_LANGUAGES_SLUG ) . "') {
		    		jQuery(this).addClass('current').find('a').addClass('current').attr('aria-current', 'page');
			    }
		    });";
		}

		// --- enqueue script inline ---
		// 2.3.0: enqueue instead of echoing
		if ( '' != $js ) {
			// 2.5.0: use radio_station_add_inline_script
			radio_station_add_inline_script( 'radio-station-admin', $js );
		}
	}
}

// -----------------------------------------
// Fix to Redirect Plugin Settings Menu Link
// -----------------------------------------
// 2.3.2: added settings submenu page redirection fix
// 2.5.0: moved to admin file and changed to admin_init hook
add_action( 'admin_init', 'radio_station_settings_page_redirect' );
function radio_station_settings_page_redirect() {

	// --- bug out if not plugin settings page ---
	if ( !isset( $_REQUEST['page'] ) || ( RADIO_STATION_SLUG != sanitize_text_field( $_REQUEST['page'] ) ) ) {
		return;
	}

	// --- check if link is for options-general.php ---
	if ( strstr( filter_var( $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL ), '/options-general.php' ) ) {

		// --- redirect to plugin settings page (admin.php) ---
		$url = add_query_arg( 'page', RADIO_STATION_SLUG, admin_url( 'admin.php' ) );
		// TODO: maybe use wp_safe_redirect here ?
		wp_redirect( $url );
		exit;
	}
}

// --------------------
// Role Assignment Info
// --------------------
// 2.3.0: added section for upcoming (Pro) role editor feature
add_action( 'radio_station_admin_page_section_permissions_bottom', 'radio_station_role_editor' );
function radio_station_role_editor() {

	// 2.3.2: add filter role editor message
	$display = apply_filters( 'radio_station_role_editor_message', true );
	if ( !$display ) {
		return;
	}

	// --- role assignment message ---
	echo '<h3>' . esc_html( __( 'Role Assignments', 'radio-station' ) ) . '</h3>';
	echo esc_html( __( 'You can assign a Radio Station role to users through the WordPress User editor.', 'radio-station' ) ) . '<br>';

	// --- info regarding Pro role assignment interface ---
	// 2.4.0.3: change text to reflect inclusion in Pro
	echo esc_html( __( 'Radio Station Pro includes a Role Assignment Interface so you can easily assign Radio Station roles to any user.', 'radio-station' ) ) . '<br>';

	// TODO: maybe add picture of role editing interface ?

	// --- Pro upgrade link ---
	// 2.5.0: add direct upgrade link
	$pricing_url = add_query_arg( 'page', 'radio-station-pricing', admin_url( 'admin.php' ) );
	echo '<a href="' . esc_url( $pricing_url ) . '" target="_blank">';
		echo esc_html( __( 'Upgrade Now', 'radio-station' ) );
	echo '</a>';
	echo '&nbsp; | &nbsp;';

	// --- Pro details/pricing link ---
	$upgrade_url = radio_station_get_upgrade_url();
	echo '<a href="' . esc_url( $upgrade_url ) . '" target="_blank">';
		// echo esc_html( __( 'Upgrade to Radio Station Pro', 'radio-station' ) );
		echo esc_html( __( 'Find out more about Radio Station Pro', 'radio-station' ) );
	echo ' &rarr;</a>.';
	echo '<br><br>';
}

// -------------------------------
// Display Import/Export Show Page
// -------------------------------
function radio_station_import_export_show_page() {

	$importexport = RADIO_STATION_DIR . '/templates/import-export-shows.php';
	if ( file_exists( $importexport ) ) {

		// --- display the import/export page ---
		include $importexport;

		// --- enqueue Semenatic UI ---
		// 2.3.2: conditional enqueue to be safe
		if ( function_exists( 'radio_station_enqueue_semantic' ) ) {
			radio_station_enqueue_semantic();
		}
	}
}

// ------------------------
// Display Plugin Docs Page
// ------------------------
function radio_station_plugin_docs_page() {

	// --- show MailChimp signup form ---
	echo "<p>&nbsp;</p>";
	radio_station_mailchimp_form();

	// --- include help file template ---
	// include RADIO_STATION_DIR . '/templates/help.php';

	// --- include markdown reader ---
	include_once RADIO_STATION_DIR . '/reader.php';

	$docs = scandir( RADIO_STATION_DIR . '/docs/' );
	$docs[] = 'CHANGELOG.md';
	foreach ( $docs as $doc ) {
		if ( !in_array( $doc, array( '.', '..' ) ) ) {
			$id = str_replace( '.md', '', $doc );
			echo '<div id="doc-page-' . esc_attr( strtolower( $id ) ) . '" class="doc-page"';
			if ( 'index' != $id ) {
				echo ' style="display:none;"';
			}
			echo '>';
				// 2.5.6: use wp_kses with allowed HTML
				$allowed = radio_station_allowed_html( 'content', 'docs' );
				echo wp_kses( radio_station_parse_doc( $id ), $allowed );
			echo '</div>' . "\n";
		}
	}

	// 2.5.6: added jquery onclick function to replace onclick attributes
	echo "<script>jQuery('.doc-link').on('click',function(){ref = jQuery(this).attr('id').replace('-doc-link',''); radio_load_doc(ref);});
	function radio_load_doc(id) {
		pages = document.getElementsByClassName('doc-page');
		for (i = 0; i < pages.length; i++) {pages[i].style.display = 'none';}
		hash = '';
		if (id.indexOf('#') > -1) {
			parts = id.split('#');
			id = parts[0]; hash = parts[1];
		} else if (id == 'index') {hash = 'index-top';}
		document.getElementById('doc-page-'+id).style.display = 'block';
		if (hash != '') {
			anchor = document.getElementById(hash);
			atop = anchor.offsetTop; /* do not use 'top'! */
			window.scrollTo(0, (atop-20));
		}
	}</script>" . "\n";

	echo '<style>.doc-page {padding: 20px 40px 20px 10px;}
	.doc-page, .doc-page p {font-size: 14px;}
	.doc-page table {padding: 10px; background-color: #F9F9F9; border: 1px solid #CCC; border-radius: 10px;}
	.doc-page th {text-align: left; padding: 7px 14px;}
	.doc-page td {font-size:16px; padding: 7px 14px;}
	.doc-page td a {text-decoration: none; font-weight: bold;}
	.doc-page td a:hover {text-decoration: underline;}
	h1.docs-heading {font-size: 1.65em; margin-bottom: 1.65em;}
	h2.docs-heading {font-size: 1.5em; margin-top: 1.5em;}
	h3.docs-heading {font-size: 1.3em;}
	h4.docs-heading {font-size: 1.1em;}
	</style>' . "\n";

	// --- output announcement content ---
	radio_station_announcement_content( false );

}

// -----------------------
// Parse Markdown Doc File
// -----------------------
function radio_station_parse_doc( $id ) {

	// --- get docs page contents ---
	if ( 'CHANGELOG' == $id ) {
		$path = RADIO_STATION_DIR . '/CHANGELOG.md';
	} else {
		$path = RADIO_STATION_DIR . '/docs/' . $id . '.md';
	}
	$contents = file_get_contents( $path );

	// --- strip top level heading to prevent duplicate title ---
	$sep = '***';
	$backlink = '';
	if ( 'index' != $id ) {
		// 2.5.6: replace onlick attribute with class
		// $backlink = '<alink href="javascript:void(0);" onclick="radio_load_doc(\'index\');">&larr; ';
		$backlink = '<alink class="doc-link" id="index-doc-link">&larr; ';
		$backlink .= esc_html( __( 'Back to Documentation Index', 'radio-station' ) );
		$backlink .= '</a><br>';
	}
	$contents = str_replace( $sep, $backlink, $contents );

	// --- replace relative links ---
	$contents = str_replace( '(#', '(./' . $id . '#', $contents );
	$contents = str_replace( '.md)', ')', $contents );
	$contents = str_replace( '.md#', '#', $contents );

	// --- process markdown formatting ---
	// 2.5. : use prefixed markdown function
	$formatted = radio_station_markdown( $contents );

	// --- a # name links to headings ---
	for ( $i = 1; $i < 7; $i++ ) {
		$tag_start = '<h' . $i . '>';
		$tag_end = '</h' . $i . '>';
		if ( stristr( $formatted, $tag_start ) ) {
			while ( stristr( $formatted, $tag_start ) ) {
				$pos = stripos( $formatted, $tag_start );
				$pos2 = $pos + strlen( $tag_start );
				$before = substr( $formatted, 0, $pos );
				$after = substr( $formatted, $pos2, strlen( $formatted ) );
				$pos3 = stripos( $after, $tag_end );
				$anchor = sanitize_title( substr( $after, 0, $pos3 ) );
				$alink = '<a id="' . esc_attr( $anchor ) . '" name="' . esc_attr( $anchor ) . '"></a>';
				$newheading = $alink . '<h' . $i . ' class="docs-heading">';
				$formatted = $before . $newheading . $after;
			}
		}
	}

	// --- replace links with javascript ---
	$tag_start = '<a href="./';
	$tag_end = '"';
	$placeholder = '<alink ';
	if ( stristr( $formatted, $tag_start ) ) {
		while ( stristr( $formatted, $tag_start ) ) {
			$pos = strpos( $formatted, $tag_start );
			$before = substr( $formatted, 0, $pos );
			$pos = strpos( $formatted, $tag_start ) + strlen( $tag_start );
			$after = substr( $formatted, $pos, strlen( $formatted ) );
			$pos2 = strpos( $after, $tag_end );
			$url = substr( $after, 0, $pos2 );
			$url = strtolower( $url );
			// 2.5.6: replace onclick with class and id
			// $onclick = ' onclick="radio_load_doc(\'' . esc_js( $url ) . '\');';
			$class = ' class="doc-link" id="' . esc_attr( $url ) . '-doc-link"';
			$after = substr( $after, ( $pos2 + 1 ), strlen( $after ) );
			$formatted = $before . $placeholder . $class . $after;
		}
	}
	$formatted = str_replace( '<a href="', '<a target="_blank" href="', $formatted );
	if ( 'index' != $id ) {
		$formatted .= $backlink . '<br>';
	} else {
		$formatted = '<a id="index-top" name="index-top"></a>' . $formatted;
	}
	$formatted = str_replace( '<alink ', '<a ', $formatted );

	return $formatted;
}

// ----------------------------
// Display Playlist Export Page
// ----------------------------
// note: this is a legacy export playlist function
// TODO: rewrite playlist export function ?
function radio_station_playlist_export_page() {

	global $wpdb;

	// first, delete any old exports from the export directory
	$dir = RADIO_STATION_DIR . '/export/';
	if ( is_dir( $dir ) ) {
		$get_contents = opendir( $dir );
		while ( $file = readdir( $get_contents ) ) {
			if ( '.' !== $file && '..' !== $file ) {
				// 2.5.6: use wp_delete_file
				// unlink( $dir . $file );
				wp_delete_file( $dir . $file );
			}
		}
		closedir( $get_contents );
	}

	// --- watch for form submission ---
	if ( isset( $_POST['export_action'] ) && ( 'station_playlist_export' === sanitize_text_field( $_POST['export_action'] ) ) ) {

		// --- validate referrer and nonce field ---
		check_admin_referer( 'station_export_valid' );

		$start = sanitize_text_field( $_POST['station_export_start_year'] ) . '-';
		$start .= sanitize_text_field( $_POST['station_export_start_month'] ) . '-';
		$start .= sanitize_text_field(  $_POST['station_export_start_day'] );
		$start .= ' 00:00:00';
		$end = sanitize_text_field( $_POST['station_export_end_year'] ) . '-';
		$end .= sanitize_text_field( $_POST['station_export_end_month'] ) . '-';
		$end .= sanitize_text_field( $_POST['station_export_end_day'] );
		$end .= ' 23:59:59';

		// fetch all records that were created between the start and end dates
		$sql = "SELECT ID,post_date FROM " . $wpdb->posts . " WHERE post_type = %s AND post_status = 'publish' AND TO_DAYS(post_date) >= TO_DAYS(%s) AND TO_DAYS(post_date) <= TO_DAYS(%s) ORDER BY post_date ASC";

		// prepare query before executing
		$playlists = $wpdb->get_results( $wpdb->prepare( $sql, array( RADIO_STATION_PLAYLIST_SLUG, $start, $end ) ) );

		if ( !$playlists ) {
			// 2.5.6: output translated and escaped message
			echo esc_html( __( 'No playlists found for this period.', 'radio-station' ) );
		}

		// fetch the tracks for each playlist from the wp_postmeta table
		foreach ( $playlists as $i => $playlist ) {

			$songs = get_post_meta( $playlist->ID, 'playlist', true );

			// remove any entries that are marked as 'queued'
			foreach ( $songs as $j => $entry ) {
				if ( 'queued' === $entry['playlist_entry_status'] ) {
					unset( $songs[$j] );
				}
			}

			$playlists[$i]->songs = $songs;
		}

		$output = '';

		$date = '';
		foreach ( $playlists as $playlist ) {
			if ( ! isset( $playlist->post_date ) ) {
				continue;
			}
			$playlist_datetime = explode( ' ', $playlist->post_date );
			$playlist_post_date = array_shift( $playlist_datetime );
			if ( empty( $date ) || $date !== $playlist_post_date ) {
				$date = $playlist_post_date;
				$output .= $date . "\n\n";
			}

			foreach ( $playlist->songs as $song ) {
				$output .= $song['playlist_entry_artist'] . ' || ' . $song['playlist_entry_song'] . ' || ' . $song['playlist_entry_album'] . ' || ' . $song['playlist_entry_label'] . "\n";
			}
		}

		// save as file
		// TODO: use WP Filesystem for writing
		$dir = RADIO_STATION_DIR . '/export/';
		$file = $date . '-export.txt';
		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		// TODO: use WP Filesystem for writing
		$f = fopen( $dir . $file, 'w' );
		fwrite( $f, $output );
		fclose( $f );

		// display link to file
		$url = get_bloginfo( 'url' ) . '/wp-content/plugins/radio-station/tmp/' . $file;
		echo '<div id="message" class="updated"><p><strong>';
		echo '<a href="' . esc_url( $url ) . '">';
		echo esc_html( __( 'Right-click and download this file to save your export', 'radio-station' ) );
		echo '</a>';
		echo '</strong></p></div>';
	}

	// display the export page
	include RADIO_STATION_DIR . '/templates/playlist-export.php';

}


// ----------------------
// === Update Notices ===
// ----------------------

// -------------------------
// Get Plugin Upgrade Notice
// -------------------------
// 2.3.0: added to get plugin upgrade notice
function radio_station_get_upgrade_notice() {

	// --- check updates transient for upgrade notices ---
	$notice = false;
	$pluginslug = RADIO_STATION_SLUG;
	$pluginupdates = get_site_transient( 'update_plugins' );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Update Transient: ' . esc_html( print_r( $pluginupdates, true ) ) . '</span>';
	}

	// 2.4.0.9: check for object for PHP8
	if ( $pluginupdates && is_object( $pluginupdates ) && property_exists( $pluginupdates, 'response' ) ) {
		foreach ( $pluginupdates->response as $file => $update ) {
			if ( is_object( $update ) && property_exists( $update, 'slug' ) ) {
				if ( $update->slug == $pluginslug ) {
					if ( property_exists( $update, 'upgrade_notice' ) ) {

						// 2.3.3.9: compare new version with installed version
						$new_version = $update->new_version;
						$version = radio_station_plugin_version();
						if ( version_compare( $version, $new_version, '<' ) ) {

							// --- parse upgrade notice ---
							$notice = $update->upgrade_notice;
							$notice = radio_station_parse_upgrade_notice( $notice );
							$notice['update_id'] = str_replace( '.', '', $new_version );
							if ( property_exists( $update, 'icons' ) && isset( $update->icons['1x'] ) ) {
								$notice['icon_url'] = $update->icons['1x'];
							}
							$notice['plugin_file'] = $file;
							break;
						}
					}
				}
			}
		}
	}
	if ( RADIO_STATION_DEBUG && $notice ) {
		echo '<span style="display:none;">Update Notice: ' . esc_html( print_r( $notice, true ) ) . '</span>';
	}
	return $notice;
}

// --------------------
// Parse Upgrade Notice
// --------------------
function radio_station_parse_upgrade_notice( $notice ) {

	$lines = $content_lines = array();
	$notice_url = '';
	if ( strstr( $notice, "\n" ) ) {
		$contents = explode( "\n", $notice );
	} else {
		$contents = array( $notice );
	}

	foreach ( $contents as $content ) {
		if ( '' != trim( $content ) ) {
			// --- extract link from line ---
			if ( strstr( $content, 'http' ) ) {
				$pos = strpos( $content, 'http' );
				$chunks = str_split( $content, $pos );
				unset( $chunks[0] );
				$remainder = implode( '', $chunks );
				$breaks = array( ' ', '<', "\n", "\r" );
				$pos = array();
				foreach ( $breaks as $i => $urlbreak ) {
					if ( strstr( $remainder, $urlbreak ) ) {
						$pos[$i] = strpos( $remainder, $urlbreak );
					}
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
		if ( '' != trim( $content ) ) {
			$content_lines[] = $content;
			if ( !in_array( $content, array( '<ul>', '</ul>' ) ) ) {
				$line = str_replace( array( '<li>', '</li>' ), array( '', '' ), $content );
				$lines[] = $line;
			}
		}
	}

	// --- recombine lines and return ---
	$content = implode( "\n", $content_lines );
	$notice = array(
		'content' => $content,
		'lines'   => $lines,
		'url'     => $notice_url,
	);

	return $notice;
}

// --------------------------
// Plugin Page Update Message
// --------------------------
add_action( 'in_plugin_update_message-' . RADIO_STATION_BASENAME, 'radio_station_plugin_update_message', 10, 2 );
function radio_station_plugin_update_message( $plugin_data, $response ) {

	// --- bug out if no update plugins capability ---
	if ( !current_user_can( 'update_plugins' ) ) {
		return;
	}

	// --- get upgrade notice ---
	$notice = radio_station_get_upgrade_notice();

	// --- bug out if no upgrade notice ---
	if ( !$notice ) {
		return;
	}

	// --- output update available message ---
	echo '<br><b>' . esc_html( __( 'Take a moment to Update for a better experience. In this update', 'radio-station' ) ) . ":</b><br>";
	// 2.5.6: added missing index variable $i to foreach
	foreach ( $notice['lines'] as $i => $line ) {
		// 2.5.0: maybe output link to notice URL
		if ( ( '' != $notice['url'] ) && ( 0 == $i ) ) {
			// 2.5.6: fix variable notice_url to notice['url']
			echo '&bull; <a href="' . esc_url( $notice['url'] ) . '" target="_blank" title="' . esc_attr( __( 'Read full update details.', 'radio-station' ) ) . '">' . esc_html( $line ) . '</a><br>';
		} else {
			echo '&bull; ' . esc_html( $line ) . '<br>';
		}
	}

}

// ---------------------------
// Display Admin Update Notice
// ---------------------------
add_action( 'admin_notices', 'radio_station_admin_update_notice' );
function radio_station_admin_update_notice() {
	// --- do not display on settings page ---
	if ( isset( $_GET['page'] ) && ( 'radio-station' === sanitize_text_field( $_GET['page'] ) ) ) {
		return;
	}
	radio_station_update_notice();
}

// ---------------------------
// Display Admin Update Notice
// ---------------------------
function radio_station_update_notice() {

	// --- bug out if no update plugins capability ---
	if ( !current_user_can( 'update_plugins' ) ) {
		return;
	}

	// --- get upgrade notice ---
	$notice = radio_station_get_upgrade_notice();

	// --- bug out if no upgrade notice ---
	if ( !$notice ) {
		return;
	}

	// --- ignore if updating now ---
	if ( isset( $_GET['action'] ) && ( 'upgrade-plugin' === sanitize_text_field( $_GET['action'] ) )
		&& isset( $_GET['plugin'] ) && ( $notice['plugin_file'] === sanitize_text_field( $_GET['plugin'] ) ) ) {
		return;
	}

	// --- bug out if already read ---
	$read = get_option( 'radio_station_read_upgrades' );
	if ( $read && is_array( $read ) && isset( $read[$notice['update_id']] ) && ( '1' == $read[$notice['update_id']] ) ) {
		return;
	}

	// --- set plugin update URL ---
	$update_url = admin_url( 'update.php' ) . '?action=upgrade-plugin&plugin=' . $notice['plugin_file'];
	$update_url = wp_nonce_url( $update_url, 'upgrade-plugin_' . $notice['plugin_file'] );

	// --- output update available notice ---
	echo '<div id="radio-station-update-' . esc_attr( $notice['update_id'] ) . '" class="notice update-nag" style="position:relative;">' . "\n";

		echo '<ul style="list-style:none;">' . "\n";

			if ( isset( $notice['icon_url'] ) ) {
				echo '<li style="display:inline-block; vertical-align:top; margin-right:40px;">' . "\n";
					echo '<img src="' . esc_url( $notice['icon_url'] ) . '" style="width:75px; height: 75px;">' . "\n";
				echo '</li>' . "\n";
			}

			echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-right:40px; line-height:1.8em;">' . "\n";
				echo esc_html( __( 'A new version of', 'radio-station' ) ) . '<br>' . "\n";
				echo '<b><span style="font-size:1.2em;">' . esc_html( __( 'Radio Station', 'radio-station' ) ) . '</span></b><br>' . "\n";
				echo esc_html( __( 'is available.', 'radio-station' ) ) . "\n";
			echo '</li>' . "\n";

			echo '<li style="display:inline-block; vertical-align:top; margin-right:40px; max-width:600px;">' . "\n";
			echo '<b>' . esc_html( __( 'Take a moment to Update for a better experience. In this update', 'radio-station' ) ) . ":</b><br>" . "\n";
				echo '<ul style="padding:0; list-style:disc;">' . "\n";
					foreach ( $notice['lines'] as $i => $line ) {
						if ( ( '' != $notice['url'] ) && ( 0 == $i ) ) {
							echo '<li style="text-indent:20px;"><a href="' . esc_url( $notice['url'] ) . '" target="_blank" title="' . esc_attr( __( 'Read full update details.', 'stream-player' ) ) . '">' . esc_html( $line ) . '</li>' . "\n";
						} else {
							echo '<li style="text-indent:20px;">' . esc_html( $line ) . '</li>' . "\n";
						}
					}
				echo '</ul>' . "\n";
			echo '</li>' . "\n";

			echo '<li style="display:inline-block; text-align:center; vertical-align:top;">' . "\n";
				echo '<a class="button button-primary" href="' . esc_url( $update_url ) . '">' . esc_html( __( 'Update Now', 'radio-station' ) ) . '</a>' . "\n";
				if ( '' != $notice['url'] ) {
					echo '<br><br>' . "\n";
					echo '<a class="button" href="' . esc_url( $notice['url'] ) . '" target="_blank">' . esc_html( __( 'Full Update Details', 'radio-station' ) ) . ' &rarr;</a>' . "\n";
				}
			echo '</li>' . "\n";

		echo '</ul>' . "\n";

		// --- dismiss notice link ---
		// 2.5.8: add nonce to dismissal link
		$dismiss_url = add_query_arg( 'action', 'radio_station_notice_dismiss', admin_url( 'admin-ajax.php' ) );
		$dismiss_url = add_query_arg( 'upgrade', $notice['update_id'], $dismiss_url );
		$dismiss_url = add_query_arg( 'nonce', wp_create_nonce( 'radio_station_notice' ), $dismiss_url );
		echo '<div style="position:absolute; top:20px; right: 20px;">' . "\n";
			echo '<a href="' . esc_url( $dismiss_url ) . '" target="radio-station-notice-iframe" style="text-decoration:none;">' . "\n";
			echo '<span class="dashicons dashicons-dismiss" title="' . esc_attr( __( 'Dismiss this Notice', 'radio-station' ) ) . '"></span></a>' . "\n";
		echo '</div>' . "\n";

	echo '</div>' . "\n";

	// --- notice dismissal iframe (once only) ---
	radio_station_admin_notice_iframe();
}

// ---------------------
// Display Plugin Notice
// ---------------------
// 2.3.0: added 2.3.0 template update announcement notice
add_action( 'admin_notices', 'radio_station_notice' );
function radio_station_notice() {

	// 2.5.0: check for user capability
	if ( !current_user_can( 'update_plugins' ) ) {
		return;
	}

	// --- get latest notice ---
	$notices = radio_station_get_notices();
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Notices: ' . esc_html( print_r( $notices, true ) ) . '</span>' . PHP_EOL;
	}
	if ( count( $notices ) < 1 ) {
		return;
	}
	$notice_ids = array_keys( $notices );
	$notice_id = max( $notice_ids );
	$notice = $notices[$notice_id];

	// --- bug out if already read ---
	$read = get_option( 'radio_station_read_notices' );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Read Notices: ' . esc_html( print_r( $read, true ) ) . '</span>' . "\n";
		echo '<span style="display:none;">Latest Notices: ' . esc_html( print_r( $notice, true ) ) . '</span>' . "\n";
	}
	if ( $read && isset( $read[$notice_id] ) && ( '1' == $read[$notice_id] ) ) {
		return;
	}

	// --- display plugin notice ---
	echo '<div id="radio-station-notice-' . esc_attr( $notice['id'] ) . '" class="notice notice-info" style="position:relative;">' . "\n";

		// --- output plugin notice text ---
		echo '<ul style="list-style:none;">' . "\n";

			// --- plugin icon ---
			$icon_url = plugins_url( 'images/radio-station.png', RADIO_STATION_FILE );
			echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-right:40px; line-height:1.8em;">' . "\n";
				echo '<img src="' . esc_url( $icon_url ) . '" style="width:75px; height:75px;">' . "\n";
			echo '</li>' . "\n";

			// --- notice title ---
			echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-right:40px; line-height:1.8em;">' . "\n";
				echo '<b><span style="font-size:1.2em;">' . esc_html( __( 'Radio Station', 'radio-station' ) ) . '</span></b><br>' . "\n";
				echo '<b>' . esc_html( __( 'Update Notice', 'radio-station' ) ) . '</b>' . "\n";
			echo '</li>' . "\n";

			// --- notice details ---
			echo '<li style="display:inline-block; vertical-align:top; margin-right:40px; font-size:16px; line-height:22px; max-width:600px;">' . "\n";
				echo '<div style="margin-bottom:10px;">' . "\n";
					echo '<b>' . esc_html( __( 'Thanks for Updating! You can enjoy these improvements now', 'radio-station' ) ) . '</b>:' . "\n";
				echo '</div>' . "\n";
				echo '<ul style="padding:0; list-style:disc;">' . "\n";
					foreach ( $notice['lines'] as $line ) {
						// 2.5.0: added wp_kses to line output
						// #
						$allowed = radio_station_allowed_html( 'content', 'notice' );
						echo '<li style="text-indent:20px;">' . wp_kses( $line, $allowed ) . '</li>' . "\n";
					}
				echo '</ul>' . "\n";
			echo '</li>' . "\n";

			echo '<li style="display:inline-block; text-align:center; vertical-align:top;">' . "\n";

				// --- link to update blog post ---
				if ( isset( $notice['url'] ) && ( '' != $notice['url'] ) ) {
					echo '<a class="button" href="' . esc_url( $notice['url'] ) . '">' . esc_html( __( 'Full Update Details', 'radio-station' ) ) . ' &rarr;</a>' . "\n";
					echo '<br><br>' . "\n";
				}

				// --- link to settings page ---
				if ( !isset( $_REQUEST['page'] ) || ( 'radio-station' !== sanitize_text_field( $_REQUEST['page'] ) ) ) {
					$settings_url = add_query_arg( 'page', 'radio-station', admin_url( 'admin.php' ) );
					echo '<a class="button button-primary" href="' . esc_url( $settings_url ) . '">' . esc_html( __( 'Plugin Settings', 'radio-station' ) ) . '</a>' . "\n";
				}

			echo '</li>' . "\n";

		echo '</ul>' . "\n";

		// --- notice dismissal button ---
		// 2.5.8: add nonce to dismissal link
		$dismiss_url = add_query_arg( 'action', 'radio_station_notice_dismiss', admin_url( 'admin-ajax.php' ) );
		$dismiss_url = add_query_arg( 'notice', $notice['id'], $dismiss_url );
		$dismiss_url = add_query_arg( 'nonce', wp_create_nonce( 'radio_station_notice' ), $dismiss_url );
		echo '<div style="position:absolute; top:20px; right: 20px;">' . "\n";
			echo '<a href="' . esc_url( $dismiss_url ) . '" target="radio-station-notice-iframe" id+style="text-decoration:none;">' . "\n";
			echo '<span class="dashicons dashicons-dismiss" title="' . esc_attr( __( 'Dismiss this Notice', 'radio-station' ) ) . '"></span></a>' . "\n";
		echo '</div>' . "\n";

	// --- close notice wrap ---
	echo '</div>' . "\n";

	// --- notice dismissal iframe (once only) ---
	radio_station_admin_notice_iframe();

}

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
		// 2.5.10: remove license lines regardless of contents
		// $contents = str_replace( 'License: GPLv2 or later', '', $contents );
		// $contents = str_replace( 'License URI: http://www.gnu.org/licenses/gpl-2.0.html', '', $contents );
		$strip_lines = array( 'License', 'License URI' );
		foreach( $strip_lines as $strip_line ) {
			if ( strstr( $contents, $strip_line ) ) {
				$pos = strpos( $contents, $strip_line . ':' );
				$chunks = str_split( $contents, $pos );
				$before = $chunks[0];
				unset( $chunks[0] );
				$remainder = implode( '', $chunks );
				$posb = strpos( $remainder, "\n" );
				$chunks = str_split( $remainder, $posb );
				unset( $chunks[0] );
				$remainder = implode( '', $chunks );
				$contents = $before . $remainder;
			}
		}

		// --- include Markdown Readme Parser ---
		include $parser;
		$readme = new radio_station_readme_parser();
		$parsed = $readme->parse_readme_contents( $contents );

		// --- parse all the notices to get notice info ---
		if ( isset( $parsed['upgrade_notice'] ) ) {
			$notices = array();
			foreach ( $parsed['upgrade_notice'] as $version => $notice ) {
				if ( trim( $notice ) != '' ) {
					$id = str_replace( '.', '', $version );
					$notice = radio_station_parse_upgrade_notice( $notice );
					$notices[$id] = array(
						'id'      => $id,
						'version' => $version,
						'url'     => $notice['url'],
						'content' => $notice['content'],
						'lines'   => $notice['lines'],
					);
				}
			}
		}
	}

	return $notices;
}

// ---------------------
// AJAX Mark Notice Read
// ---------------------
// 2.3.0: added for plugin notice dismissal
add_action( 'wp_ajax_radio_station_notice_dismiss', 'radio_station_notice_dismiss' );
function radio_station_notice_dismiss() {
	if ( current_user_can( 'manage_options' ) || current_user_can( 'update_plugins' ) ) {

		// 2.5.8: added extra nonce check
		if ( !wp_verify_nonce( sanitize_text_field( $_REQUEST['nonce'] ), 'radio_station_notice' ) ) {
			exit;
		}
		
		if ( isset( $_GET['notice'] ) ) {

			$notice = absint( $_GET['notice'] );
			if ( $notice < 0 ) {
				return;
			}
			$notices = get_option( 'radio_station_read_notices' );
			if ( ! $notices ) {
				$notices = array();
			}
			$notices[$notice] = '1';
			update_option( 'radio_station_read_notices', $notices );
			echo "<script>parent.document.getElementById('radio-station-notice-" . esc_js( $notice ) . "').style.display = 'none';</script>" . "\n";

		} elseif ( isset( $_GET['upgrade'] ) ) {

			$upgrade = absint( $_GET['upgrade'] );
			if ( $upgrade < 0 ) {
				return;
			}
			$upgrades = get_option( 'radio_station_read_upgrades' );
			if ( ! $upgrades ) {
				$upgrades = array();
			}
			$upgrades[$upgrade] = '1';
			update_option( 'radio_station_read_upgrades', $upgrades );
			echo "<script>parent.document.getElementById('radio-station-update-" . esc_js( $upgrade ) . "').style.display = 'none';</script>" . "\n";

		}
	}
	exit;
}


// ---------------------
// === Admin Notices ===
// ---------------------

// --------------------------
// Admin Notice Action Iframe
// --------------------------
// 2.3.3.9: separated out to individual function
function radio_station_admin_notice_iframe() {
	global $radio_station_notice_iframe;
	if ( !isset( $radio_station_notice_iframe ) || !$radio_station_notice_iframe ) {
		echo '<iframe src="javascript:void(0);" name="radio-station-notice-iframe" id="radio-station-notice-iframe" style="display:none;"></iframe>' . "\n";
		$radio_station_notice_iframe = true;
	}
}

// ------------------------
// Plugin Settings Page Top
// ------------------------
add_action( 'radio_station_admin_page_top', 'radio_station_settings_page_top' );
function radio_station_settings_page_top() {

	$now = time();

	// --- free directory listing offer ---
	// 2.3.1: added offer to top of admin settings
	// 2.3.3.9: remove listing offer notice
	/*
	$offer_end = strtotime( '2020-08-01 00:01' );
	if ( $now < $offer_end ) {
		if ( !get_option( 'radio_station_listing_offer_accepted' ) ) {
			radio_station_listing_offer_content( false );
		}
	} */

	// --- pro launch discount notice ---
	// 2.3.3.9: add timed launch offer signup notice
	$offer_start = strtotime( '2021-07-20 00:01' );
	$offer_end = strtotime( '2021-07-26 00:01' );
	if ( $now < $offer_end ) {
		$user_id = get_current_user_id();
		$user_ids = get_option( 'radio_station_launch_offer_accepted' );
		if ( !$user_ids || !is_array( $user_ids ) || !in_array( $user_id, $user_ids ) ) {
			$prelaunch = ( $now < $offer_start ) ? true : false;
			if ( isset( $_GET['offertest'] ) ) {
				$offertest = sanitize_text_field( $_GET['offertest'] );
				if ( '1' == $offertest ) {
					$prelaunch = false;
				} elseif ( '2' == $offertest ) {
					$prelaunch = true;
				}
			}
			radio_station_launch_offer_content( false, $prelaunch );
		}
	}

	// --- plugin update notice ---
	radio_station_update_notice();
	echo '<br>' . "\n";
}

// ---------------------------
// Plugin Settings Page Bottom
// ---------------------------
add_action( 'radio_station_admin_page_bottom', 'radio_station_settings_page_bottom' );
function radio_station_settings_page_bottom() {
	// 2.3.1: move mailchimp form for listing offer
	radio_station_mailchimp_form();
	radio_station_announcement_content( false );
}

// ------------------------------
// Directory Listing Offer Notice
// ------------------------------
// 2.3.1: added free directory listing offer notice
// 2.3.3.9: disable listing offer notice
// add_action( 'admin_notices', 'radio_station_listing_offer_notice' );
function radio_station_listing_offer_notice() {

	// --- bug out on certain plugin pages ---
	$pages = array( 'radio-station', 'radio-station-docs' );
	if ( isset( $_REQUEST['page'] ) && in_array( sanitize_text_field( $_REQUEST['page'] ), $pages ) ) {
		return;
	}

	// --- check offer time window ---
	$now = time();
	$offer_end = strtotime( '2020-08-01 00:01' );
	if ( $now > $offer_end ) {
		return;
	}

	// --- bug out if already dismissed ---
	if ( get_option( 'radio_station_listing_offer_dismissed' ) ) {
		return;
	}

	// --- display plugin announcement ---
	echo '<div id="radio-station-listing-offer-notice" class="notice notice-success" style="position:relative;">' . "\n";
		radio_station_listing_offer_content();
	echo '</div>' . "\n";

	// --- notice dismissal frame (once) ---
	radio_station_admin_notice_iframe();
}

// -------------------
// Launch Offer Notice
// -------------------
add_action( 'admin_notices', 'radio_station_launch_offer_notice' );
function radio_station_launch_offer_notice( $rspage = false ) {

	// --- bug out on certain plugin pages ---
	$pages = array( 'radio-station', 'radio-station-docs' );
	if ( isset( $_REQUEST['page'] ) && in_array( sanitize_text_field( $_REQUEST['page'] ), $pages ) ) {
		return;
	}

	// --- bug out if not admin ---
	if ( !current_user_can( 'manage_options' ) && !current_user_can( 'update_plugins' ) ) {
		return;
	}

	// --- check offer time window ---
	$now = time();
	$offer_start = strtotime( '2021-07-20 00:01' );
	$offer_end = strtotime( '2021-07-26 00:01' );
	if ( $now > $offer_end ) {
		return;
	}

	// --- bug out if already dismissed (by user) ---
	$user_id = get_current_user_id();
	$user_ids = get_option( 'radio_station_launch_offer_dismissed' );
	if ( $user_ids && is_array( $user_ids ) && in_array( $user_id, $user_ids ) ) {
		return;
	}

	// --- display plugin announcement ---
	echo '<div id="radio-station-launch-offer-notice" class="notice notice-success" style="position:relative;">' . "\n";
		$prelaunch = ( $now < $offer_start ) ? true : false;
		radio_station_launch_offer_content( true, $prelaunch );
	echo '</div>' . "\n";

	// --- notice dismissal frame (once) ---
	radio_station_admin_notice_iframe();
}

// -------------------------------
// Directory Listing Offer Content
// -------------------------------
// 2.3.1: added free directory listing offer content
function radio_station_listing_offer_content( $dismissable = true ) {

	// 2.5.13: add nonce to dismiss action
	$nonce = wp_create_nonce( 'offer_dismiss' );
	$dismiss_url = add_query_arg( 'action', 'radio_station_listing_offer_dismiss', admin_url( 'admin-ajax.php' ) );
	$dismiss_url = add_query_arg( '_wpnonce', $nonce, $dismiss_url );
	$accept_dismiss_url = add_query_arg( 'accepted', '1', $dismiss_url );
	
	echo '<ul style="list-style:none;">' . "\n";

		// --- directory logo image ---
		$logo_image = plugins_url( 'images/netmix-logo.png', RADIO_STATION_FILE );
		echo '<li style="display:inline-block; vertical-align:middle;">' . "\n";
			echo '<img src="' . esc_url( $logo_image ) . '">' . "\n";
		echo '</li>' . "\n";

		// --- free listing offer text ---
		echo '<li style="display:inline-block; vertical-align:middle; margin-left:40px; font-size:16px; line-height:24px;">' . "\n";
			echo '<center><b style="font-size:17px;">' . esc_html( __( 'Time Sensitive Free Offer', 'radio-station' ) ) . '</b></center>' . "\n";

			echo '<p style="font-size: 14px; line-height: 21px; margin-top: 0;">' . "\n";
				echo esc_html( __( 'We are excited to announce the opening of the new', 'radio-station' ) ) . "\n";
				echo ' <a href="' . esc_url( RADIO_STATION_NETMIX_DIR ) . '" target="_blank">Netmix Station Directory</a>!!!<br>' . "\n";
				echo esc_html( __( 'Allowing listeners to newly discover Stations and Shows - which can include yours...', 'radio-station' ) ) . '<br>' . "\n";

				echo esc_html( __( 'Because while launching,' ) ) . ' <b>' . esc_html( __( 'we are offering 30 days free listing to all Radio Station users!', 'radio-station' ) ) . '</b><br>' . "\n";
				echo esc_html( __( 'Interested in more exposure and listeners for your Radio Station, for free?', 'radio-station' ) ) . "\n";
			echo '</p>' . "\n";
		echo '</li>' . "\n";

		// --- accept / decline offer button links ---
		echo '<li style="display:inline-block; vertical-align:middle; margin-left:40px; font-size:16px; line-height:24px;">' . "\n";
			echo '<center>' . "\n";
			echo '<div id="directory-offer-accept-button" style="display:inline-block; margin-right:10px;">' . "\n";
				echo '<a href="' . esc_url( RADIO_STATION_NETMIX_DIR . 'station-listing/' ) . '" style="font-size: 11px;" target="_blank" class="button-primary"';
				if ( $dismissable ) {
					echo ' onclick="radio_display_dismiss_link();"';
				}
				echo '>' . esc_html( __( 'Yes please!', 'radio-station' ) ) . '</a>' . "\n";
			echo '</div>' . "\n";
			echo '<div id="directory-offer-blog-button" style="display:inline-block;">' . "\n";
				echo '<a href="' . esc_url( RADIO_STATION_NETMIX_DIR . 'announcing-new-netmix-directory/' ) . '" style="font-size: 11px;" target="_blank" class="button-secondary">' . esc_html( __( 'More Details', 'radio-station' ) ) . '</a>' . "\n";
			echo '</div><br>' . "\n";

			echo '<div id="directory-offer-dismiss-link" style="display:none;">' . "\n";
				echo '<a href="' . esc_url( $accept_dismiss_url ) . '" style="font-size: 12px;" target="radio-station-notice-iframe">' . esc_html( __( "Great! I'm listed, dismiss this notice.", 'radio-station' ) ) . '</a>' . "\n";
			echo '</div>' . "\n";
			echo '</center><br>' . "\n";

			echo '<div style="font-size: 11px; line-height: 18px;">' . "\n";
				echo esc_html( __( 'Offer valid until end of July 2020.', 'radio-station' ) ) . '<br>' . "\n";
				echo esc_html( __( 'Activate your 30 days before it ends!', 'radio-station' ) ) . "\n";
			echo '</div>' . "\n";

		echo '</li>' . "\n";

	echo '</ul>' . "\n";

	// --- dismiss notice icon ---
	if ( $dismissable ) {
		echo '<div style="position:absolute; top:20px; right: 20px;">' . "\n";
			echo '<a href="' . esc_url( $dismiss_url ) . '" target="radio-station-notice-iframe" style="text-decoration:none;">' . "\n";
				echo '<span class="dashicons dashicons-dismiss" title="' . esc_html( __( 'Dismiss this Notice', 'radio-station' ) ) . '"></span>' . "\n";
			echo '</a>' . "\n";
		echo '</div>' . "\n";
	}

	// --- display dismiss link script ---
	echo "<script>function radio_display_dismiss_link() {
		document.getElementById('directory-offer-accept-button').style.display = 'none';
		document.getElementById('directory-offer-dismiss-link').style.display = '';
	}</script>" . "\n";

}

// --------------------
// Launch Offer Content
// --------------------
// 2.3.3.9: added for Pro launch discount
function radio_station_launch_offer_content( $dismissable = true, $prelaunch = false ) {

	// 2.5.13: added offer dismiss nonce
	$nonce = wp_create_nonce( 'offer_dismiss' );
	$dismiss_url = add_query_arg( 'action', 'radio_station_launch_offer_dismiss', admin_url( 'admin-ajax.php' ) );
	$dismiss_url = add_query_arg( '_wpnonce', $nonce, $dismiss_url );
	$accept_dismiss_url = add_query_arg( 'accepted', '1', $dismiss_url );
	echo '<ul style="list-style:none;">' . PHP_EOL;

		// --- directory logo image ---
		// $launch_image = plugins_url( 'images/netmix-logo.png', RADIO_STATION_FILE );
		$launch_image = plugins_url( 'images/pro-launch.gif', RADIO_STATION_FILE );
		echo '<li style="display:inline-block; vertical-align:middle;">' . "\n";
			echo '<img src="' . esc_url( $launch_image ) . '" style="width:128px; height:128px">' . "\n";
		echo '</li>' . "\n";

		// --- free listing offer text ---
		echo '<li style="display:inline-block; vertical-align:middle; margin-left:40px; font-size:16px; line-height:24px;">' . "\n";

			if ( $prelaunch ) {
				echo '<center><b style="font-size:18px;">' . esc_html( __( 'Radio Station Pro Launch Discount!', 'radio-station' ) ) . '</b></center>' . "\n";
				echo '<p style="font-size: 16px; line-height: 24px; margin-top: 0;">' . "\n";
					echo esc_html( __( 'We are thrilled to announce the upcoming launch of Radio Station PRO', 'radio-station' ) ) . ' !!!<br>' . "\n";
					echo esc_html( __( 'Jam-packed with new features to "level up" your Station\'s online presence.', 'radio-station' ) ) . '<br>' . "\n";
					echo esc_html( __( 'During the launch,' ) ) . ' <b>' . esc_html( __( 'we are offering 30% discount to existing Radio Station users!', 'radio-station' ) ) . '</b><br>' . "\n";
					echo esc_html( __( 'Sign up to the exclusive launch list to receive your discount code when we go LIVE.', 'radio-station' ) ) . "\n";
				echo '</p>' . "\n";
			} else {
				echo '<center><b style="font-size:18px;">' . esc_html( __( 'Radio Station PRO Launch is LIVE!', 'radio-station' ) ) . '</b></center>' . "\n";
				echo '<p style="font-size: 16px; line-height: 24px; margin-top: 0;">' . "\n";
					echo esc_html( __( 'The long anticipated moment has arrived. The doors are open to get PRO', 'radio-station' ) ) . ' !!!<br>' . "\n";
					echo esc_html( __( 'Jam-packed with new features to "level up" your Station\'s online presence.', 'radio-station' ) ) . '<br>' . "\n";
					echo esc_html( __( 'Remember,' ) ) . ' <b>' . esc_html( __( 'we are offering 30% discount to existing Radio Station users!', 'radio-station' ) ) . '</b><br>' . "\n";
					echo '<a href="' . esc_url( RADIO_STATION_PRO_URL ) . 'plugin-launch-discount/" target="_blank">' . "\n";
					echo esc_html( __( 'Sign up here to receive your exclusive launch discount code.', 'radio-station' ) ) . PHP_EOL;
				echo '</a></p>' . "\n";
			}

		echo '</li>' . "\n";

		// --- accept / decline offer button links ---
		echo '<li style="display:inline-block; vertical-align:middle; margin-left:40px; font-size:16px; line-height:24px;">' . "\n";
			echo '<center>' . "\n";
			echo '<div id="launch-offer-accept-button" style="display:inline-block; margin-right:10px;">' . "\n";
			if ( $prelaunch ) {
				echo '<a href="' . RADIO_STATION_PRO_URL . 'plugin-launch-discount/" style="font-size: 16px;" target="_blank" class="button-primary"';
				if ( $dismissable ) {
					echo ' onclick="radio_display_dismiss_link();"';
				}
				echo '>' . esc_html( __( "Yes, I'm in!", 'radio-station' ) ) . '</a>' . "\n";
			} else {
				// 2.5.6: added missing escape wrapper for URL
				echo '<a href="' . esc_url_raw( RADIO_STATION_PRO_URL . 'pricing/' )  . '" style="font-size: 16px;" target="_blank" class="button-primary"';
				if ( $dismissable ) {
					echo ' onclick="radio_display_dismiss_link();"';
				}
				echo '>' . esc_html( __( 'Go PRO', 'radio-station' ) ) . '</a>' . "\n";
			}
			echo '</div>' . "\n";

			echo '<div id="launch-offer-dismiss-link" style="display:none;">' . "\n";
				echo '<a href="' . esc_url( $accept_dismiss_url ) . '" style="font-size: 12px;" target="radio-station-notice-iframe">' . esc_html( __( 'Thanks, already done.', 'radio-station' ) ) . '</a>' . "\n";
			echo '</div>' . "\n";
			echo '</center><br>' . "\n";

		echo '</li>' . "\n";

	echo '</ul>' . "\n";

	// --- dismiss notice icon ---
	if ( $dismissable ) {
		echo '<div style="position:absolute; top:20px; right: 20px;">' . "\n";
			echo '<a href="' . esc_url( $dismiss_url ) . '" target="radio-station-notice-iframe" style="text-decoration:none;">' . "\n";
				echo '<span class="dashicons dashicons-dismiss" title="' . esc_html( __( 'Dismiss this Notice', 'radio-station' ) ) . '"></span>' . "\n";
			echo '</a>' . "\n";
		echo '</div>' . "\n";
	}

	// --- display dismiss link script ---
	echo "<script>function radio_display_dismiss_link() {
		document.getElementById('launch-offer-accept-button').style.display = 'none';
		document.getElementById('launch-offer-dismiss-link').style.display = '';
		document.getElementById('radio-station-notice-iframe').src = '" . esc_url( $accept_dismiss_url ) . "';
	}</script>" . "\n";

}

// --------------------------
// Dismiss Free Listing Offer
// --------------------------
// 2.3.1: AJAX for free listing notice dismissal
add_action( 'wp_ajax_radio_station_listing_offer_dismiss', 'radio_station_listing_offer_dismiss' );
function radio_station_listing_offer_dismiss() {

	// --- bug out if no permissions ---
	if ( !current_user_can( 'manage_options' ) && !current_user_can( 'update_plugins' ) ) {
		exit;
	}

	// 2.5.13: added nonce check
	$nonce = sanitize_text_field( $_REQUEST['_wpnonce'] );
	$checknonce = wp_verify_nonce( $nonce, 'offer_dismiss' );
	if ( $checknonce ) {

		// --- set option to dismissed ---
		update_option( 'radio_station_listing_offer_dismissed', true );
		if ( isset( $_REQUEST['accept'] ) && ( '1' === sanitize_text_field( $_REQUEST['accept'] ) ) ) {
			update_option( 'radio_station_listing_offer_accepted', true );
		}

		// --- hide the announcement in parent frame ---
		echo "<script>parent.document.getElementById('radio-station-listing-offer-notice').style.display = 'none';</script>" . "\n";
		
	}
	exit;
}

// --------------------
// Dismiss Launch Offer
// --------------------
// 2.3.3.9: AJAX for free listing notice dismissal
add_action( 'wp_ajax_radio_station_launch_offer_dismiss', 'radio_station_launch_offer_dismiss' );
function radio_station_launch_offer_dismiss() {

	// --- bug out if no permissions ---
	if ( !current_user_can( 'manage_options' ) && !current_user_can( 'update_plugins' ) ) {
		exit;
	}

	// 2.5.13: added nonce check
	$nonce = sanitize_text_field( $_REQUEST['_wpnonce'] );
	$checknonce = wp_verify_nonce( $nonce, 'offer_dismiss' );
	if ( $checknonce ) {

		// --- get current user ID ---
		$user_id = get_current_user_id();

		// --- set option to dismissed ---
		$user_ids = get_option( 'radio_station_launch_offer_dismissed' );
		if ( !$user_ids || !is_array( $user_ids ) ) {
			$user_ids = array( $user_id );
		} elseif ( !in_array( $user_id, $user_ids ) ) {
			$user_ids[] = $user_id;
		}
		update_option( 'radio_station_launch_offer_dismissed', $user_ids );

		// --- maybe set option for accepted ---
		if ( isset( $_REQUEST['accept'] ) && ( '1' === sanitize_text_field( $_REQUEST['accepted'] ) ) ) {
			$user_ids = get_option( 'radio_station_launch_offer_accepted' );
			if ( !$user_ids || !is_array( $user_ids ) ) {
				$user_ids = array( $user_id );
			} elseif ( !in_array( $user_id, $user_ids ) ) {
				$user_ids[] = $user_id;
			}
			update_option( 'radio_station_launch_offer_accepted', $user_ids );
		}

		// --- hide the announcement in parent frame ---
		echo "<script>parent.document.getElementById('radio-station-launch-offer-notice').style.display = 'none';</script>" . "\n";
	}

	exit;
}

// -----------------------------------
// Plugin Takeover Announcement Notice
// -----------------------------------
// 2.2.2: added plugin announcement notice
add_action( 'admin_notices', 'radio_station_announcement_notice' );
function radio_station_announcement_notice() {

	if ( !current_user_can( 'manage_options' ) && !current_user_can( 'update_plugins' ) ) {
		return;
	}

	// --- bug out if already dismissed ---
	if ( get_option( 'radio_station_announcement_dismissed' ) ) {
		return;
	}

	// --- bug out on certain plugin pages ---
	// 2.2.8: remove strict in_array checking
	$pages = array( 'radio-station', 'radio-station-docs' );
	if ( isset( $_REQUEST['page'] ) && in_array( sanitize_text_field( $_REQUEST['page'] ), $pages ) ) {
		return;
	}

	// --- display plugin announcement ---
	echo '<div id="radio-station-announcement-notice" class="notice notice-info" style="position:relative;">';
		radio_station_announcement_content();
	echo '</div>';

	// --- notice dismissal frame (once) ---
	radio_station_admin_notice_iframe();

}

// ------------------------------------
// Plugin Takeover Announcement Content
// ------------------------------------
// 2.2.2: added simple patreon supporter blurb
function radio_station_announcement_content( $dismissable = true ) {

	echo '<ul style="list-style:none;">' . "\n";

		// --- plugin image ---
		$plugin_image = plugins_url( 'images/radio-station.png', RADIO_STATION_FILE );
		echo '<li style="display:inline-block; vertical-align:middle;">' . "\n";
			echo '<img src="' . esc_url( $plugin_image ) . '">' . "\n";
		echo '</li>' . "\n";

		// --- takeover announcement ---
		echo '<li style="display:inline-block; vertical-align:middle; margin-left:40px; font-size:16px; line-height:24px;">' . "\n";
			echo '<b style="font-size:17px;">' . esc_html( __( 'Help support us to make improvements, modifications and introduce new features!', 'radio-station' ) ) . '</b><br>' . "\n";
			echo esc_html( __( 'With over a thousand radio station users thanks to the original plugin author Nikki Blight', 'radio-station' ) ) . ', <br>' . "\n";
			echo esc_html( __( 'since June 2019', 'radio-station' ) ) . ', ' . "\n";
			echo '<b>' . esc_html( __( 'Radio Station', 'radio-station' ) ) . '</b> ' . "\n";
			echo esc_html( __( ' plugin development has been actively taken over by', 'radio-station' ) ) . "\n";
			echo ' <a href="' . esc_url( RADIO_STATION_NETMIX_DIR ) . '" target="_blank">Netmix</a>.<br>' . "\n";
			// 2.3.3.9: add updated text after 2000 user mileston
			echo esc_html( __( 'And due to our continued efforts we now have a community of over two thousand active stations!', 'radio-station' ) ) . '<br>' . "\n";
			echo esc_html( __( 'We invite you to', 'radio-station' ) ) . "\n";
			echo ' <a href="' . esc_url( RADIO_STATION_PATREON ) . '" target="_blank">' . "\n";
				echo esc_html( __( 'Become a Radio Station Patreon Supporter', 'radio-station' ) ) . "\n";
			echo '</a> ' . esc_html( __( 'to make it better for everyone', 'radio-station' ) ) . '!' . "\n";
		echo '</li>' . "\n";
		echo '<li style="display:inline-block; text-align:center; vertical-align:middle; margin-left:40px;">' . "\n";

			$button = radio_station_patreon_button( 'radiostation' );
			// 2.5.0: added wp_kses to button output
			$allowed = radio_station_allowed_html( 'button', 'patreon' );
			echo wp_kses( $button, $allowed );

			// 2.2.7: added WordPress.Org star rating link
			// 2.3.0: only show for dismissable notice
			if ( $dismissable ) {
				echo '<br><br><span style="color:#FC5;" class="dashicons dashicons-star-filled"></span> ' . "\n";
				echo '<a class="notice-link" href="https://wordpress.org/support/plugin/radio-station/reviews/#new-post" target="_blank">' . "\n";
					echo esc_html( __( 'Rate on WordPress.Org', 'radio-station' ) ) . "\n";
				echo '</a>' . "\n";
			}
		echo '</li>' . "\n";
	echo '</ul>' . "\n";

	// --- dismiss notice icon ---
	if ( $dismissable ) {
		// 2.5.8: added nonce to dismissal URL
		$dismiss_url = admin_url( 'admin-ajax.php?action=radio_station_announcement_dismiss' );
		$dismiss_url = add_query_arg( 'nonce',  wp_create_nonce( 'radio_station_notice' ), $dismiss_url );
		echo '<div style="position:absolute; top:20px; right: 20px;">' . "\n";
			echo '<a href="' . esc_url( $dismiss_url ) . '" target="radio-station-notice-iframe" style="text-decoration:none;">' . "\n";
				echo '<span class="dashicons dashicons-dismiss" title="' . esc_html( __( 'Dismiss this Notice', 'radio-station' ) ) . '"></span>' . "\n";
			echo '</a>' . "\n";
		echo '</div>' . "\n";
	}

}

// ------------------------------------
// Dismiss Plugin Takeover Announcement
// ------------------------------------
// 2.2.2: AJAX for takeover announcement notice dismissal
add_action( 'wp_ajax_radio_station_announcement_dismiss', 'radio_station_announcement_dismiss' );
function radio_station_announcement_dismiss() {
	if ( current_user_can( 'manage_options' ) || current_user_can( 'update_plugins' ) ) {
		
		// 2.5.8: added extra nonce check
		if ( !wp_verify_nonce( sanitize_text_field( $_REQUEST['nonce'] ), 'radio_station_notice' ) ) {
			exit;
		}

		// --- set option to dismissed ---
		update_option( 'radio_station_announcement_dismissed', true );

		// --- hide the announcement in parent frame ---
		echo "<script>parent.document.getElementById('radio-station-announcement-notice').style.display = 'none';</script>" . "\n";
	}
	exit;
}

// --------------------------
// Show Shift Conflict Notice
// --------------------------
add_action( 'admin_notices', 'radio_station_shift_conflict_notice' );
function radio_station_shift_conflict_notice() {

	if ( !current_user_can( 'edit_shows' ) ) {
		return;
	}

	// --- get show shifts ---
	// note: calling this will run the shift conflict checker
	$show_shifts = radio_station_get_show_shifts();

	// --- display any shift conflicts found ---
	$conflicts = get_option( 'radio_station_schedule_conflicts' );
	if ( $conflicts && is_array( $conflicts ) && ( count( $conflicts ) > 0 ) ) {

		echo '<div id="radio-station-conflicts" class="notice notice-error" style="position:relative;">' . "\n";

			echo '<ul style="list-style:none;">' . "\n";

				// 2.3.3.9: remove unnecessary left margin on first list item
				echo '<li style="display:inline-block; text-align:center; vertical-align:top;">' . "\n";
					echo '<b>' . esc_html( __( 'Radio Station', 'radio-station' ) ) . '</b><br>' . "\n";
					echo esc_html( __( 'has detected', 'radio-station' ) ) . '<br>' . "\n";
					echo esc_html( __( 'Schedule conflicts!', 'radio-station' ) ) . "\n";
				echo '</li>' . "\n";

				echo '<li style="display:inline-block; vertical-align:top; margin-left:40px;">' . "\n";
				echo '<b>' . esc_html( __( 'The following Shows have conflicting Shift times', 'radio-station' ) ) . ":</b><br>" . "\n";

					echo '<ul style="list-style:none; margin-top:5px;">' . "\n";
					$edit_link = add_query_arg( 'action', 'edit', admin_url( 'post.php' ) );
					foreach ( $conflicts as $show => $show_conflicts ) {
						foreach ( $show_conflicts as $conflict ) {
							if ( !$conflict['duplicate'] ) {
								echo '<li>';
								$show_edit_link = add_query_arg( 'post', $conflict['show'], $edit_link );
								$show_title = get_the_title( $conflict['show'] );
								echo '<a href="' . esc_url( $show_edit_link ) . '">' . esc_attr( $show_title ) . '</a>';
								if ( $conflict['disabled'] ) {
									$disabled = ' [disabled] ';
								} else {
									$disabled = '';
								}
								echo ' (' . esc_html( $disabled ) . esc_html( $conflict['day'] ) . ' ';
								echo esc_html( $conflict['start'] ) . ' - ' . esc_html( $conflict['end'] ) . ')';
								echo ' ' . esc_html( __( 'with', 'radio-station' ) ) . ' ';
								$show_edit_link = add_query_arg( 'post', $conflict['with_show'], $edit_link );
								$show_title = get_the_title( $conflict['with_show'] );
								echo '<a href="' . esc_url( $show_edit_link ) . '">' . esc_attr( $show_title ) . '</a>';
								if ( $conflict['with_disabled'] ) {
									$disabled = ' [disabled] ';
								} else {
									$disabled = '';
								}
								echo ' (' . esc_html( $disabled ) . esc_html( $conflict['with_day'] ) . ' ';
								echo esc_html( $conflict['with_start'] ) . ' - ' . esc_html( $conflict['with_end'] ) . ')';
								echo '</li>';
							}
						}
					}
					echo '</ul>';
				echo '</li>';

				// --- show list link ---
				$show_list_url = add_query_arg( 'post_type', RADIO_STATION_SHOW_SLUG, admin_url( 'edit.php' ) );
				echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-left:40px;">' . "\n";
					echo '<a class="button" style="margin-bottom:5px;" href="' . esc_url( $show_list_url ) . '">' . esc_html( __( 'Go to Show List', 'radio-station' ) ) . ' &rarr;</a><br>' . "\n";
					echo esc_html( __( 'Conflicts are highlighted', 'radio-station' ) ) . '<br>' . "\n";
					echo esc_html( __( 'in Show Shift column.', 'radio-station' ) ) . "\n";
				echo '</li>' . "\n";

				// --- undismissable error notice ---
				echo '<li style="display:inline-block; text-align:center; vertical-align:top; margin-left:40px;">' . "\n";
					echo esc_html( __( 'This notice will persist', 'radio-station' ) ) . '<br>' . "\n";
					echo esc_html( __( 'until conflicts are resolved.', 'radio-station' ) ) . "\n";
				echo '</li>' . "\n";

			echo '</ul>' . "\n";

		echo '</div>' . "\n";

	}
}

// -------------------------
// MailChimp Subscriber Form
// -------------------------
function radio_station_mailchimp_form() {

	// --- get current user email ---
	$current_user = wp_get_current_user();
	$user_email = $current_user->user_email;

	// --- bug out if already subscribed ---
	// 2.3.0: added to hide for existing subscribers
	// note: there is a typo in this option not worth fixing
	$subscribed = get_option( 'radio_station_subcribed' );
	if ( $subscribed && is_array( $subscribed ) && in_array( $user_email, $subscribed ) ) {
		return;
	}

	// --- enqueue MailChimp form styles ---
	// 2.3.0: prefix mailchimp CSS file
	// 2.3.0: fix to incorrect style handle
	$version = filemtime( RADIO_STATION_DIR . '/css/rs-mailchimp.css' );
	$url = plugins_url( 'css/rs-mailchimp.css', RADIO_STATION_FILE );
	wp_enqueue_style( 'rs-mailchimp', $url, array(), $version, 'all' );

	// --- set radio station plugin icon URL ---
	$icon = plugins_url( 'images/radio-station-icon.png', RADIO_STATION_FILE );

	// --- output MailChimp signup form ---
	?>

	<div id="mc_embed_signup">
		<form action="https://netmix.us8.list-manage.com/subscribe/post?u=c53a6feec82d81974edd00a95&amp;id=7130454f20" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
			<div style="position: absolute; left: -5000px;" aria-hidden="true">
				<input type="text" name="b_c53a6feec82d81974edd00a95_7130454f20" tabindex="-1" value="">
			</div>
			<div id="mc_embed_signup_scroll">
				<div id="plugin-icon">
					<img src="<?php echo esc_url( $icon ); ?>" alt='<?php echo esc_html( __( 'Radio Station', 'radio-station' ) ); ?>'>
				</div>
				<label id="signup-label" for="mce-EMAIL"><?php echo esc_html( __( "Stay tuned! Subscribe to Radio Station's", 'radio-station' ) ); ?>
					<br>
					<?php echo esc_html( __( 'Plugin Updates and Announcements List', 'radio-station' ) ); ?></label>
				<input type="email" name="EMAIL" class="email" id="mce-EMAIL" value="<?php echo esc_html( $user_email ); ?>" placeholder="Your email address" required>
				<div class="subscribe">
					<input type="button" value="Subscribe" name="subscribe" id="mc-embedded-button" class="button">
					<!-- input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button" -->
				</div>
			</div>
		</form>
	</div>

	<?php

	echo '<iframe id="mc-subscribe-record" src="javascript:void(0);" style="display:none;"></iframe>' . "\n";

	// --- AJAX subscription call ---
	// 2.3.0: added to record subscribers
	$recordurl = add_query_arg( 'action', 'radio_station_record_subscribe', admin_url( 'admin-ajax.php' ) );
	echo "<script>
	jQuery(document).ready(function() {
		jQuery('#mc-embedded-button').on('click', function(e) {
			email = document.getElementById('mce-EMAIL').value;
			url = '" . esc_url_raw( $recordurl ) . "&email='+encodeURIComponent(email);
			document.getElementById('mc-subscribe-record').src = url;
		});
	});</script>" . "\n";

}

// ---------------------
// AJAX Record Subcriber
// ---------------------
// 2.3.0: added to record subscribers
add_action( 'wp_ajax_radio_station_record_subscribe', 'radio_station_record_subscribe' );
function radio_station_record_subscribe() {

	// note: there is a typo in this option not worth fixing
	$email = sanitize_email( $_GET['email'] );
	$subscribed = get_option( 'radio_station_subcribed' );
	if ( !$subscribed || !is_array( $subscribed ) ) {
		add_option( 'radio_station_subcribed', array( $email ) );
	} else {
		$subscribed[] = $email;
		update_option( 'radio_station_subcribed', $subscribed );
	}

	// --- submit form in parent window ---
	echo "<script>console.log('Subscription Recorded');";
	echo "parent.jQuery('#mc-embedded-subscribe-form').submit();</script>" . "\n";

	exit;
}

// ------------------
// AJAX Clear Notices
// ------------------
// (for manual use in development testing)
// 2.3.3: fix to function prefix
add_action( 'wp_ajax_radio_station_clear_option', 'radio_station_clear_plugin_options' );
function radio_station_clear_plugin_options() {

	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['option'] ) ) {
		// 2.5.0: condensed logic and added sanitize_text_field
		$option = sanitize_text_field( $_GET['option'] );
		if ( 'subscribed' == $option ) {
			// note: there is a typo in this option not worth fixing
			delete_option( 'radio_station_subcribed' );
		} elseif ( 'notices' == $option ) {
			delete_option( 'radio_station_read_notices' );
		} elseif ( 'upgrades' == $option ) {
			delete_option( 'radio_station_read_upgrades' );
		} elseif ( 'announcement' == $option ) {
			delete_option( 'radio_station_announcement_dismissed' );
		} elseif ( 'listingoffer' == $option ) {
			// 2.3.1: added clearing of listing offer options
			delete_option( 'radio_station_listing_offer_dismissed' );
		} elseif ( 'offeraccepted' == $option ) {
			delete_option( 'radio_station_listing_offer_accepted' );
		}
	}

	exit;
}

