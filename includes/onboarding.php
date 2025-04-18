<?php

// Onboarding
// @since 2.7.0

// - Add Dashboard Widget
// - Dashboard Widget
// - Quicklinks Panel
// - Statistics Progress Panel
// - Settings Progress Panel
// - Progress Bar
// - Enqueue Onboarding Styles
// - Onboarding Styles


// --------------------
// Add Dashboard Widget
// --------------------
add_action( 'wp_dashboard_setup', 'radio_station_add_dashboard_widget' );
function radio_station_add_dashboard_widget() {

	// --- add dashboard widget ---
	wp_add_dashboard_widget( 'radio-station-stats', __( 'Radio Station', 'radio-station' ), 'radio_station_dashboard_widget' );

}

// ----------------
// Dashboard Widget
// ----------------
function radio_station_dashboard_widget() {
	radio_station_quicklinks_panel();
	radio_station_statistics_panel();
	radio_station_settings_panel();
}

// ----------------
// Quicklinks Panel
// ----------------
function radio_station_quicklinks_panel() {
	
	// --- quicklinks heading ---
	echo '<div class="progress-heading">' . esc_html( 'Quick Links', 'radio-station' ) . '</div>' . "\n";

	// --- quicklinks menu ---
	echo '<ul class="progress-list">' . "\n";
	
		// --- settings page ---
		$settings_url = add_query_arg( 'page', 'radio-station', admin_url( 'admin.php' ) );
		echo '<li class="progress-list-item">' . "\n";
			echo '<span class="progress-icon progress-settings dashicons dashicons-admin-settings"></span>' . "\n";
			echo '<a href="' . esc_url( $settings_url ) . '">' . esc_html( __( 'Settings', 'radio-station' ) ) . '</a>' . "\n";
		echo '</li>' . "\n";
		
		// --- quickstart ---
		// TODO: quickstart link / dropdown ?
		$quickstart_url = '';
		echo '<li class="progress-list-item">';
			echo '<span class="progress-icon progress-quickstart dashicons dashicons-album"></span>' . "\n";
			echo '<a href="' . esc_url( $quickstart_url ) . '">' . esc_html( __( 'Quickstart', 'radio-station' ) ) . '</a>';
		echo '</li>' . "\n";
		
		// --- documentation ---
		$documentation_url = add_query_arg( 'page', 'radio-station-docs', admin_url( 'admin.php' ) );
		echo '<li class="progress-list-item">' . "\n";
			echo '<span class="progress-icon progress-docs dashicons dashicons-book"></span>' . "\n";
			echo '<a href="' . esc_url( $documentation_url ) . '">' . esc_html( __( 'Docs', 'radio-station' ) ) . '</a>' . "\n";
		echo '</li>' . "\n";
		
		// --- upgrade to Pro ---
		if ( !defined( 'RADIO_STATION_PRO' ) ) {
			$upgrade_url = radio_station_get_upgrade_url();
			echo '<li class="progress-list-item">' . "\n";
				echo '<span class="progress-icon progress-upgrade dashicons dashicons-unlock"></span>' . "\n";
				echo '<a class="progress-quicklink" href="' . esc_url( $upgrade_url ) . '">' . esc_html( __( 'Upgrade to Pro', 'radio-station' ) ) . '</a>' . "\n";
			echo '</li>' . "\n";
		}
	
	echo '</ul>' . "\n";

}

// -------------------------
// Statistics Progress Panel
// -------------------------
function radio_station_statistics_panel() {

	global $wpdb;

	// --- content progress ---
	$progress_items = $content_notdone = $content_progress = 0;

	// --- get show counts ---
	$show_count = $shift_count = $active_count = $publish_count = $draft_count = $active_show_count = 0;
	$shows_host_count = $genre_show_count = $language_show_count = 0;
	$query = "SELECT ID,post_status FROM " . $wpdb->prefix . "posts WHERE post_type = %s";
	$results = $wpdb->get_results( $wpdb->prepare( $query, RADIO_STATION_SHOW_SLUG ), ARRAY_A );
	if ( $results && is_array( $results ) && count( $results ) > 0 ) {
		$show_count = count( $results );
		foreach( $results as $result ) {
			
			// --- show counts ---
			$active = get_post_meta( $result['ID'], 'show_active', true );
			if ( 'on' == $active ) {
				$active_count++;
			}
			if ( 'publish' == $result['post_status'] ) {
				$publish_count++;
			}
			if ( 'draft' == $result['post_status'] ) {
				$draft_count++;
			}
			if ( ( 'on' == $active ) && ( 'publish' == $result['post_status'] ) ) {
				$active_show_count++;
			}
			
			// --- shift count ---
			$shifts = get_post_meta( $result['ID'], 'show_sched', true );
			if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
				foreach ( $shifts as $shift ) {
					if ( !isset( $shift['disabled'] ) || ( 'yes' != $shift['disabled'] ) ) {
						$shift_count++;
					}
				}
			}
			
			// --- show host count ---
			$hosts = get_post_meta( $result['ID'], 'show_user_list', true );
			if ( $hosts && is_array( $hosts ) && ( count( $hosts ) > 0 ) ) {
				$shows_host_count++;
			}
			
			// --- show genres count ---
			if ( has_term( '', RADIO_STATION_GENRES_SLUG, $result['ID'] ) ) {
				$genre_show_count++;
			}

			// --- show languages count ---
			if ( has_term( '', RADIO_STATION_LANGUAGES_SLUG, $result['ID'] ) ) {
				$language_show_count++;
			}
			
		}
	}
	
	// --- check for empty content ---
	$query = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_type = %s AND post_status = 'publish' AND post_content = ''";
	$results = $wpdb->get_results( $wpdb->prepare( $query, RADIO_STATION_SHOW_SLUG ), ARRAY_A );
	if ( $results && is_array( $results ) && count( $results ) > 0 ) {
		$show_empty_desc = count( $results );
	} else {
		$show_empty_desc = 0;
	}
	
	// --- get override count ---
	$override_count = 0;
	$query = "SELECT ID,post_status FROM " . $wpdb->prefix . "posts WHERE post_type = %s";
	$results = $wpdb->get_results( $wpdb->prepare( $query, RADIO_STATION_OVERRIDE_SLUG ), ARRAY_A );
	if ( $results && is_array( $results ) && count( $results ) > 0 ) {
		foreach ( $results as $result ) {
			if ( 'publish' == $result['post_status'] ) {
				// --- single overrides ---
				$overrides = get_post_meta( $result['ID'], 'show_override_sched', true );
				if ( $overrides && is_array( $overrides ) ) {
					foreach ( $overrides as $override ) {
						if ( !isset( $override['disabled'] ) || ( 'yes' != $override['disabled'] ) ) {
							$override_count++;
						}
					}
				}
				// --- recurring overrides ---
				$recurring_overrides = get_post_meta( $result['ID'], 'show_recurring_sched', true );
				if ( $recurring_overrides && is_array( $recurring_overrides ) ) {
					foreach ( $recurring_overrides as $override ) {
						if ( !isset( $override['disabled'] ) || ( 'yes' != $override['disabled'] ) ) {
							$override_count++;
						}
					}
				}
			}
		}
	}

	// --- get playlist count ---
	$playlist_count = $playlist_show_count = 0;
	$playlist_show_ids = array();
	$query = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_type = %s AND post_status = 'publish'";
	$results = $wpdb->get_results( $wpdb->prepare( $query, RADIO_STATION_PLAYLIST_SLUG ), ARRAY_A );
	if ( $results && is_array( $results ) && count( $results ) > 0 ) {
		foreach ( $results as $result ) {
			$playlist_show_id = get_post_meta( $result['ID'], 'playlist_show_id', true );
			if ( $playlist_show_id && !in_array( $playlist_show_id, $playlist_show_ids ) ) {
				$playlist_show_count++;
			}
		}
	}
	$playlist_show_count = count( $playlist_show_ids );

	// --- get_users with host role for host count ---
	$hosts = get_users( array( 'role__in' => array( 'dj' ) ) );
	$host_count = count( $hosts );
	$producers = get_users( array( 'role__in' => array( 'producer' ) ) );
	$producer_count = count( $producers );
	
	// --- get term counts ---
	$genre_count = wp_count_terms( array( 'taxonomy' => RADIO_STATION_GENRES_SLUG, 'hide_empty' => false ) );
	$language_count = wp_count_terms( array( 'taxonomy' => RADIO_STATION_LANGUAGES_SLUG, 'hide_empty' => false ) );

	// --- get episode counts ---
	$episode_count = $episode_show_count = $episode_playlist_count = 0;
	$episode_show_ids = $episode_playlist_ids = array();
	$query = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_type = %s";
	$results = $wpdb->get_results( $wpdb->prepare( $query, RADIO_STATION_EPISODE_SLUG ), ARRAY_A );
	if ( $results && is_array( $results ) && ( count( $results ) > 0 ) ) {
		foreach ( $results as $result ) {
			$episode_show_id = get_post_meta( $result['ID'], 'episode_show_id', true );
			if ( !in_array( $episode_show_id, $episode_show_ids ) ) {
				$episode_show_ids[] = $episode_show_id;
			}
			$episode_playlist_id = get_post_meta( $result['ID'], 'episode_playlist', true );
			if ( $episode_playlist_id && !in_array( $episode_playlist_id, $episode_playlist_ids ) ) {
				$episode_playlist_ids[] = $episode_playlist_id;
			}
		}
	}
	$episode_show_count = count( $episode_show_ids );
	$episode_playlist_count = count( $episode_playlist_ids );

	// --- Content heading ---
	echo '<div class="progress-heading">' . esc_html( 'Station Content', 'radio-station' ) . '</div>' . "\n";

	// --- Shows ---
	$progress_items++;
	$active_percent = round( ( $active_count / $show_count ), 2, PHP_ROUND_HALF_DOWN ) * 100;
	$publish_percent = round( ( $publish_count / $show_count ), 2, PHP_ROUND_HALF_DOWN ) * 100;
	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( 0 == $show_count ) {
			echo 'progress-cross dashicons-dismiss';
			$content_notdone++;
		} elseif ( 100 == $publish_percent ) {
			echo 'progress-tick dashicons-yes-alt';
			$content_progress++;
		} else {
			echo 'progress-alert dashicons-warning';
			$content_progress = $content_progress + 0.5;
		}
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
		
			// --- show count ---
			echo '<span style="progress-count">' . esc_html( $show_count ) . '</span>';
			echo ' ' . esc_html( __( 'Shows', 'radio-station' ) ) . ' ' . esc_html( __( 'with', 'radio-station' ) );

			// --- show draft count ---
			$drafts_link = add_query_arg( 'post_type', RADIO_STATION_SHOW_SLUG, admin_url( 'edit.php' ) );
			$drafts_link = add_query_arg( 'post_status', 'draft', $drafts_link );
			if ( $draft_count > 0 ) {
				echo ' <a href="' . esc_url( $drafts_link ) . '">';
				echo esc_html( $draft_count ) . esc_html( __( 'Drafts', 'radio-station' ) ) . ' ';
				echo '</a>';
				echo ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
			}
			
			// --- show active percentage ---
			// TODO: filter show post link for inactive shows
			$inactive_link = add_query_arg( 'post_type', RADIO_STATION_SHOW_SLUG, admin_url( 'edit.php' ) );
			$inactive_link = add_query_arg( 'post_status', 'inactive', $inactive_link );
			echo ' ' . esc_html( $active_percent ) . '% ';
			if ( $active_percent < 100 ) {
				echo '<a href="' . esc_url( $inactive_link ) . '">';
			}
			echo esc_html( __( 'Active', 'radio-station' ) );
			if ( $active_percent < 100 ) {
				echo '</a>';
			}
			echo '.';
			
			if ( 0 == $show_empty_desc ) {
				echo ' 100% ' . esc_html( 'Description coverage', 'radio-station' ) . '.';
			} else {
				$empty_desc_percent = round( ( $show_empty_desc / $show_count ), 2, PHP_ROUND_HALF_DOWN ) * 100;
				$desc_percent = 100 - $empty_desc_percent;
				echo ' ' . esc_html( $desc_percent ) . '% ';
				// TODO: link to Show list - filtered by lack of description
				// $show_desc_url = admin_url( '' );
				// echo '<a href="' . esc_url( $show_desc_url ) . '">';
					echo esc_html( 'Description coverage', 'radio-station' ) . '.';
				// echo '</a>';
			}

		echo '</div>' . "\n";
		
		// --- add show icon ---
		$add_show_url = add_query_arg( 'post_type', RADIO_STATION_SHOW_SLUG, admin_url( 'post-new.php' ) );
		echo '<a class="progress-icon-link" href="' . esc_url( $add_show_url ) . '">';
			echo '<span class="progress-icon progress-add dashicons dashicons-plus-alt dashicons dashicons-plus-alt"></span>';
		echo '</a>';
		echo '<a class="progress-label-link" href="' . esc_url( $add_show_url ) . '">';
			echo '<div class="progress-label" title="' . esc_attr( __( 'Add Show', 'radio-station' ) ) . '">' . esc_html( __( 'Show', 'radio-station' ) ) . '</div>';
		echo '</a>';
		
	echo '</div>';
	
	// --- Shifts ---
	$progress_items++;
	// TODO: calculate % schedule coverage!
	$schedule_percent = 100; // ???
	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( ( 0 == $shift_count ) || ( 0 == $schedule_percent ) ) {
			echo 'progress-cross dashicons-dismiss';
			$content_notdone++;
		} elseif ( 100 == $schedule_percent ) {
			echo 'progress-tick dashicons-yes-alt';
			$content_progress++;
		} else {
			echo 'progress-alert dashicons-warning';
			$content_progress = $content_progress + 0.5;
		}
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
		
			// --- show count ---
			echo '<span style="progress-count">' . esc_html( $shift_count ) . '</span>';
			echo ' ' . esc_html( __( 'Shifts', 'radio-station' ) ) . ' ' . esc_html( __( 'and', 'radio-station' ) );

			echo ' <span style="progress-count">' . esc_html( $override_count ) . '</span>';
			echo ' ' . esc_html( __( 'Overrides', 'radio-station' ) ) . ' ' . esc_html( __( 'with', 'radio-station' ) );

			// --- show active percentage ---
			echo ' ' . esc_html( $schedule_percent ) . '% ' . esc_html( __( 'Schedule coverage', 'radio-station' ) );
			echo '.' . "\n";

		echo '</div>' . "\n";

		// --- add shift icon ---
		$add_shift_url = add_query_arg( 'post_type', RADIO_STATION_SHOW_SLUG, admin_url( 'edit.php' ) );
		echo '<a class="progress-icon-link" href="' . esc_url( $add_shift_url ) . '">' . "\n";
			echo '<div class="progress-icon progress-add dashicons dashicons-plus-alt"></div>' . "\n";
		echo '</a>';
		echo '<a class="progress-label-link" href="' . esc_url( $add_shift_url ) . '">' . "\n";
			echo '<div class="progress-label" title="' . esc_attr( __( 'Add Shift', 'radio-station' ) ) . '">' . esc_html( __( 'Shift', 'radio-station' ) ) . '</div>' . "\n";
		echo '</a>' . "\n";
		
	echo '</div>' . "\n";
	
	// --- Hosts ---
	$progress_items++;
	$host_percent = round( ( $shows_host_count / $active_show_count ), 2, PHP_ROUND_HALF_DOWN ) * 100;
	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( ( 0 == $host_count ) || ( 0 == $host_percent ) ) {
			echo 'progress-cross dashicons-dismiss';
			$content_notdone++;
		} elseif ( 100 == $host_percent ) {
			echo 'progress-tick dashicons-yes-alt';
			$content_progress++;
		} else {
			echo 'progress-alert dashicons-warning';
			$content_progress = $content_progress + 0.5;
		}
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
		
			// --- show count ---
			echo '<span style="progress-count">' . esc_html( $host_count ) . '</span>';
			echo ' ' . esc_html( __( 'Hosts', 'radio-station' ) ) . ' ' . esc_html( __( 'assigned to', 'radio-station' ) );

			echo ' <span style="progress-count">' . esc_html( $active_show_count ) . '</span>';
			echo ' ' . esc_html( __( 'Shows', 'radio-station' ) ) . ' ' . esc_html( __( 'with', 'radio-station' ) );

			// --- show active percentage ---
			// TODO: link to shows list - without hosts filter
			echo ' ' . esc_html( $host_percent ) . '% ';
			if ( $host_percent < 100 ) {
				$show_host_url = add_query_arg( 'post_type', RADIO_STATION_SHOW_SLUG, admin_url( 'edit.php' ) );
				echo '<a href="' . esc_url( $show_host_url ) . '">';
			}
			echo esc_html( __( 'Host coverage', 'radio-station' ) );
			if ( $host_percent < 100 ) {
				echo '</a>';
			}
			echo '.' . "\n";

		echo '</div>' . "\n";

		// --- add host icon ---
		$add_host_url = add_query_arg( 'post_type', RADIO_STATION_SHOW_SLUG, admin_url( 'users.php' ) );
		if ( defined( 'RADIO_STATION_PRO' ) ) {
			$add_host_url = add_query_arg( 'page', 'radio-station', admin_url( 'admin.php' ) );
			$add_host_url = add_query_arg( 'tab', 'roles', $add_host_url );
		}
		echo '<a class="progress-icon-link" href="' . esc_url( $add_host_url ) . '">' . "\n";
			echo '<div class="progress-icon progress-add dashicons dashicons-plus-alt"></div>' . "\n";
		echo '</a>';
		echo '<a class="progress-label-link" href="' . esc_url( $add_host_url ) . '">' . "\n";
			echo '<div class="progress-label" title="' . esc_attr( __( 'Add Host', 'radio-station' ) ) . '">' . esc_html( __( 'Host', 'radio-station' ) ) . '</div>' . "\n";
		echo '</a>' . "\n";
		
	echo '</div>' . "\n";

	// --- Genres ---
	$progress_items++;
	$genre_percent = round( ( $genre_show_count / $active_show_count ), 2, PHP_ROUND_HALF_DOWN ) * 100;
	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( ( 0 == $genre_count ) || ( 0 == $genre_percent ) ) {
			echo 'progress-cross dashicons-dismiss';
			$content_notdone++;
		} elseif ( 100 == $genre_percent ) {
			echo 'progress-tick dashicons-yes-alt';
			$content_progress++;
		} else {
			echo 'progress-alert dashicons-warning';
			$content_progress = $content_progress + 0.5;
		}
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
		
			// --- show count ---
			echo '<span style="progress-count">' . esc_html( $genre_count ) . '</span>';
			echo ' ' . esc_html( __( 'Genres', 'radio-station' ) ) . ' ' . esc_html( __( 'assigned to', 'radio-station' ) );

			echo ' <span style="progress-count">' . esc_html( $active_show_count ) . '</span>';
			echo ' ' . esc_html( __( 'Shows', 'radio-station' ) ) . ' ' . esc_html( __( 'with', 'radio-station' ) );

			// --- show active percentage ---
			echo ' ' . esc_html( $genre_percent ) . '% ';
			if ( $genre_percent < 100 ) {
				// TODO: link to shows list - without genres filter
				$show_genres_url = add_query_arg( 'post_type', RADIO_STATION_SHOW_SLUG, admin_url( 'edit.php' ) );
				$show_genres_url = add_query_arg( 'filter', 'no-genres', $show_genres_url );
				echo '<a href="' . esc_url( $show_genres_url ) . '">';
			}
			echo esc_html( __( 'Genre coverage', 'radio-station' ) );
			if ( $genre_percent < 100 ) {
				echo '</a>';
			}
			echo '.' . "\n";

		echo '</div>' . "\n";

		// --- add genre icon ---		
		$add_genre_url = add_query_arg( 'taxonomy', RADIO_STATION_GENRES_SLUG, admin_url( 'edit-tags.php' ) );
		echo '<a class="progress-icon-link" href="' . esc_url( $add_genre_url ) . '">' . "\n";
			echo '<div class="progress-icon progress-add dashicons dashicons-plus-alt"></div>' . "\n";
		echo '</a>';
		echo '<a class="progress-label-link" href="' . esc_url( $add_genre_url ) . '">' . "\n";
			echo '<div class="progress-label" title="' . esc_attr( __( 'Add Genre', 'radio-station' ) ) . '">' . esc_html( __( 'Genre', 'radio-station' ) ) . '</div>' . "\n";
		echo '</a>' . "\n";
		
	echo '</div>' . "\n";

	// --- Playlists ---
	$progress_items++;
	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( 0 == $playlist_count ) {
			echo 'progress-alert dashicons-warning';
			$content_progress = $content_progress + 0.5;
		} else {
			echo 'progress-tick dashicons-yes-alt';
			$content_progress++;
		}
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
		
			// --- show count ---
			echo '<span style="progress-count">' . esc_html( $playlist_count ) . '</span>';
			echo ' ' . esc_html( __( 'Playlists', 'radio-station' ) ) . ' ' . esc_html( __( 'assigned to', 'radio-station' ) );

			echo ' <span style="progress-count">' . esc_html( $playlist_show_count ) . '</span>';
			echo ' ' . esc_html( __( 'Shows', 'radio-station' ) ) . ' ' . esc_html( __( 'and', 'radio-station' ) );

			echo ' <span style="progress-count">' . esc_html( $episode_playlist_count ) . '</span>';
			echo ' ' . esc_html( __( 'Episodes', 'radio-station' ) ) . '.';

		echo '</div>' . "\n";

		// --- add playlist icon ---		
		$add_playlist_url = add_query_arg( 'post_type', RADIO_STATION_PLAYLIST_SLUG, admin_url( 'post-new.php' ) );
		echo '<a class="progress-icon-link" href="' . esc_url( $add_playlist_url ) . '">' . "\n";
			echo '<div class="progress-icon progress-add dashicons dashicons-plus-alt"></div>' . "\n";
		echo '</a>';
		echo '<a class="progress-label-link" href="' . esc_url( $add_playlist_url ) . '">' . "\n";
			echo '<div class="progress-label" title="' . esc_attr( __( 'Add Playlist', 'radio-station' ) ) . '">' . esc_html( __( 'Playlist', 'radio-station' ) ) . '</div>' . "\n";
		echo '</a>' . "\n";
		
	echo '</div>' . "\n";

	// --- Episodes ---
	$progress_items++;
	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( 0 == $episode_count ) {
			echo 'progress-alert dashicons-warning';
			$content_progress = $content_progress + 0.5;
		} else {
			echo 'progress-tick dashicons-yes-alt';
			$content_progress++;
		}
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
		
			// --- episode count ---
			echo '<span style="progress-count">' . esc_html( $episode_count ) . '</span>';
			echo ' ' . esc_html( __( 'Episodes', 'radio-station' ) ) . ' ' . esc_html( __( 'published for', 'radio-station' ) );

			// --- episode show count ---
			echo ' <span style="progress-count">' . esc_html( $episode_show_count ) . '</span>';
			echo ' ' . esc_html( __( 'Shows', 'radio-station' ) ) . '.';

		echo '</div>' . "\n";

		// --- add episode icon ---
		if ( defined( 'RADIO_STATION_PRO' ) ) {

			$add_episode_url = add_query_arg( 'post_type', RADIO_STATION_EPISODE_SLUG, admin_url( 'post-new.php' ) );
			echo '<a class="progress-icon-link" href="' . esc_url( $add_episode_url ) . '">' . "\n";
				echo '<div class="progress-icon progress-add dashicons dashicons-plus-alt"></div>' . "\n";
			echo '</a>';
			echo '<a class="progress-label-link" href="' . esc_url( $add_episode_url ) . '">' . "\n";
				echo '<div class="progress-label" title="' . esc_attr( __( 'Add Episode', 'radio-station' ) ) . '">' . esc_html( __( 'Episode', 'radio-station' ) ) . '</div>' . "\n";
			echo '</a>' . "\n";
		
		} else {
			// --- upgrade to Pro / feature link ---
			$upgrade_url = radio_station_get_upgrade_url();
			echo '<a class="progress-icon-link" href="' . esc_url( $upgrade_url ) . '">' . "\n";
				echo '<div class="progress-icon progress-upgrade dashicons dashicons-unlock"></div>' . "\n";
			echo '</a>';
			echo '<a class="progress-label-link" href="' . esc_url( $upgrade_url ) . '">' . "\n";
				echo '<div class="progress-label" title="' . esc_attr( __( 'Upgrade to Radio Station PRO', 'radio-station' ) ) . '">' . esc_html( __( 'Go Pro', 'radio-station' ) ) . '</div>' . "\n";
			echo '</a>' . "\n";
		}
		
	echo '</div>' . "\n";

	// --- content progress bar ---
	radio_station_progress_bar( $content_progress, $content_notdone, $progress_items );
	
}

// -----------------------
// Settings Progress Panel
// -----------------------
function radio_station_settings_panel() {
	
	global $current_screen;
	$context = ( 'dashboard' == $current_screen->base ) ? 'dashboard' : 'settings';
	$settings = radio_station_get_settings( false );
	
	// --- settings progress ---
	$progress_items = $settings_notdone = $settings_progress = 0;
	
	// --- settings checks ---
	echo '<div class="progress-heading">' . esc_html( 'Station Settings', 'radio-station' ) . '</div>' . "\n";

	// --- Stream URLs ---
	$progress_items++;
	$settings_tab = 'general';
	$settings_section = 'broadcast';
	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( '' == $settings['streaming_url'] ) {
			echo 'progress-cross dashicons-dismiss';
			$settings_notdone++;
		} elseif ( ( '' != $settings['streaming_url'] ) && ( '' != $settings['fallback_url'] ) ) {
			echo 'progress-tick dashicons-yes-alt';
			$settings_progress++;
		} else {
			echo 'progress-alert dashicons-warning';
			$settings_progress = $settings_progress + 0.5;
		}
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
		
			// --- stream URL ---
			echo esc_html( __( 'Stream URL', 'radio-station' ) ) . ' ';
			if ( '' != $settings['streaming_url'] ) {
				echo esc_html( __( 'is set', 'radio-station' ) ) . '. ';
			} else {
				echo esc_html( __( 'not set', 'radio-station' ) ) . '. ';
			}

			// --- fallback URL ---
			echo ' ' . esc_html( __( 'Fallback stream URL', 'radio-station' ) ) . ' ';
			if ( '' != $settings['fallback_url'] ) {
				echo esc_html( __( 'is set', 'radio-station' ) ) . '. ';
			} else {
				echo esc_html( __( 'not set', 'radio-station' ) ) . '. ';
			}

		echo '</div>' . "\n";
				
		// --- fix setting icon ---
		if ( 'dashboard' == $context ) {
			$fix_setting_url = add_query_arg( 'page', 'radio-station', admin_url( 'admin.php' ) );
			$fix_setting_url = add_query_arg( 'tab', $settings_tab, $fix_setting_url );
			$fix_setting_url = add_query_arg( 'section', $settings_section, $fix_setting_url );
		} else {
			$fix_setting_url = 'javascript:void(0)';
		}
		echo '<a class="progress-icon-link" href="' . esc_url( $fix_setting_url ) . '"';
		if ( 'settings' == $context ) {
			echo ' onclick="jQuery(\'#' . esc_attr( $settings_tab ) . '-tab-button\').click();"';
		}
		echo '>';
			echo '<span class="progress-icon progress-add dashicons dashicons-controls-volumeon"></span>';
		echo '</a>' . "\n";
		echo '<a class="progress-label-link" href="' . esc_url( $fix_setting_url ) . '">';
			if ( ( '' == $settings['streaming_url'] ) || ( '' == $settings['fallback_url'] ) ) {
				echo '<div class="progress-label" title="' . esc_attr( __( 'Fix this Setting', 'radio-station' ) ) . '">' . esc_html( __( 'Fix', 'radio-station' ) ) . '</div>';
			} else {
				echo '<div class="progress-label" title="' . esc_attr( __( 'Edit this Setting', 'radio-station' ) ) . '">' . esc_html( __( 'Edit', 'radio-station' ) ) . '</div>';
			}
		echo '</a>' . "\n";
		
	echo '</div>' . "\n";

	// --- Player Bar ---
	$progress_items++;
	$settings_tab = 'player';
	$settings_section = 'bar';
	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( !defined( 'RADIO_STATION_PRO' ) ) {	
			echo 'progress-alert dashicons-warning';
			$settings_progress = $settings_progress + 0.5;
		} elseif ( 'off' == $settings['player_bar'] ) {
			echo 'progress-cross dashicons-dismiss';
			$settings_notdone++;
		} else {
			if ( 'yes' != $settings['player_bar_continuous'] ) {
				echo 'progress-alert dashicons-warning';
				$settings_progress = $settings_progress + 0.5;				
			} else {
				echo 'progress-tick dashicons-yes-alt';
				$settings_progress++;
			}
		} 
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
		
			// --- player bar ---
			if ( !defined( 'RADIO_STATION_PRO' ) ) {
				echo esc_html( __( 'Player Bar with continuous playback available in Pro version.', 'radio-station' ) );
			} else {
			
				echo esc_html( __( 'Player Bar', 'radio-station' ) ) . ' ';			
				if ( 'off' == $settings['player_bar'] ) {
					echo esc_html( __( 'not enabled', 'radio-station' ) ) . '. ';
				} elseif ( 'top' == $settings['player_bar'] ) {
					echo esc_html( __( 'enabled at page top', 'radio-station' ) ) . '. ';
				} elseif ( 'bottom' == $settings['player_bar'] ) {
					echo esc_html( __( 'enabled at page bottom', 'radio-station' ) ) . '. ';
				}

				// --- continuous playback ---
				echo esc_html( __( 'Continuous playback', 'radio-station' ) ) . ' ';
				if ( 'yes' == $settings['player_bar_continuous'] ) {
					echo esc_html( __( 'enabled', 'radio-station' ) ) . '. ';
				} else {
					echo esc_html( __( 'disabled', 'radio-station' ) ) . '. ';
				}
			}

		echo '</div>' . "\n";
				
		// --- fix setting icon ---
		if ( !defined( 'RADIO_STATION_PRO' ) ) {

			$upgrade_url = radio_station_get_upgrade_url();
			echo '<a class="progress-icon-link" href="' . esc_url( $upgrade_url ) . '">' . "\n";
				echo '<div class="progress-icon progress-upgrade dashicons dashicons-unlock"></div>' . "\n";
			echo '</a>';
			echo '<a class="progress-label-link" href="' . esc_url( $upgrade_url ) . '">' . "\n";
				echo '<div class="progress-label" title="' . esc_attr( __( 'Upgrade to Radio Station PRO', 'radio-station' ) ) . '">' . esc_html( __( 'Go Pro', 'radio-station' ) ) . '</div>' . "\n";
			echo '</a>' . "\n";

		} else {

			if ( 'dashboard' == $context ) {
				$fix_setting_url = add_query_arg( 'page', 'radio-station', admin_url( 'admin.php' ) );
				$fix_setting_url = add_query_arg( 'tab', $settings_tab, $fix_setting_url );
				$fix_setting_url = add_query_arg( 'section', $settings_section, $fix_setting_url );
			} else {
				$fix_setting_url = 'javascript:void(0)';
			}

			echo '<a class="progress-icon-link" href="' . esc_url( $fix_setting_url ) . '"';
			if ( 'settings' == $context ) {
				echo ' onclick="jQuery(\'#' . esc_attr( $settings_tab ) . '-tab-button\').click();"';
			}
			echo '>';
				echo '<span class="progress-icon progress-add dashicons dashicons-controls-volumeon"></span>';
			echo '</a>' . "\n";
			echo '<a class="progress-label-link" href="' . esc_url( $fix_setting_url ) . '">';
				if ( ( 'off' == $settings['player_bar'] ) || ( 'yes' != $settings['player_bar_continuous'] ) ) {
					echo '<div class="progress-label" title="' . esc_attr( __( 'Fix this Setting', 'radio-station' ) ) . '">' . esc_html( __( 'Fix', 'radio-station' ) ) . '</div>';
				} else {
					echo '<div class="progress-label" title="' . esc_attr( __( 'Edit this Setting', 'radio-station' ) ) . '">' . esc_html( __( 'Edit', 'radio-station' ) ) . '</div>';
				}
			echo '</a>' . "\n";
		}
		
	echo '</div>' . "\n";
	
	// --- Schedule Page ---
	$progress_items++;
	$settings_tab = 'pages';
	$settings_section = 'schedule';
	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( '' == $settings['schedule_page'] ) {
			echo 'progress-cross dashicons-dismiss';
			$settings_notdone++;
		} else {
			echo 'progress-tick dashicons-yes-alt';
			$settings_progress++;
		} // else {
		//	echo 'progress-alert dashicons-warning';
		//	$settings_progress = $settings_progress + 0.5;
		// }
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
		
			// --- stream URL ---
			echo ' ' . esc_html( __( 'Master Schedule Page', 'radio-station' ) ) . ' ';
			if ( '' != $settings['schedule_page'] ) {
				echo esc_html( __( 'is set', 'radio-station' ) ) . ' ';

				// --- automatic schedule display ---
				if ( 'yes' == $settings['schedule_auto'] ) {
					echo esc_html( __( 'to display automatically', 'radio-station' ) ) . '.';
				} else {
					// TODO: check for shortcode on schedule page ?
					echo esc_html( __( 'to manual display', 'radio-station' ) ) . '.';
				}
			} else {
				echo esc_html( __( 'not set', 'radio-station' ) ) . '.';
			}

		echo '</div>' . "\n";
				
		// --- fix setting icon ---
		if ( 'dashboard' == $context ) {
			$fix_setting_url = add_query_arg( 'page', 'radio-station', admin_url( 'admin.php' ) );
			$fix_setting_url = add_query_arg( 'tab', $settings_tab, $fix_setting_url );
			$fix_setting_url = add_query_arg( 'section', $settings_section, $fix_setting_url );
		} else {
			$fix_setting_url = 'javascript:void(0)';
		}
		echo '<a class="progress-icon-link" href="' . esc_url( $fix_setting_url ) . '"';
		if ( 'settings' == $context ) {
			echo ' onclick="jQuery(\'#' . esc_attr( $settings_tab ) . '-tab-button\').click();"';
		}
		echo '>';
			echo '<span class="progress-icon progress-add dashicons dashicons-calendar-alt"></span>';
		echo '</a>';
		echo '<a class="progress-label-link" href="' . esc_url( $fix_setting_url ) . '">';
			if ( '' == $settings['schedule_page'] ) {
				echo '<div class="progress-label" title="' . esc_attr( __( 'Fix this Setting', 'radio-station' ) ) . '">' . esc_html( __( 'Fix', 'radio-station' ) ) . '</div>';
			} else {
				echo '<div class="progress-label" title="' . esc_attr( __( 'Edit this Setting', 'radio-station' ) ) . '">' . esc_html( __( 'Edit', 'radio-station' ) ) . '</div>';
			}
		echo '</a>';
		
	echo '</div>';

	// --- Station Info ---
	$progress_items++;
	$infos = array( 'station_title', 'station_image', 'station_phone', 'station_email' );
	$infos_count = count( $infos );
	$info_count = 0;
	foreach ( $infos as $info ) {
		if ( '' != $settings[$info] ) {
			$info_count++;
		}
	}
	$info_percent = round( ( $info_count / $infos_count ), 2, PHP_ROUND_HALF_DOWN ) * 100;

	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( 100 == $info_percent ) {
			echo 'progress-tick dashicons-yes-alt';
			$settings_progress++;
		} elseif ( $info_percent > 50 ) {
			echo 'progress-alert dashicons-warning';
			$settings_progress = $settings_progress + 0.5;
		} else {
			echo 'progress-cross dashicons-dismiss';
			$settings_notdone++;
		}
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
			
			echo esc_html( $info_count ) . ' ' . esc_html( __( 'out of', 'radio-station' ) ) . ' ' . esc_html( $infos_count );
			echo ' ' . esc_html( __( 'Station information fields set', 'radio-station' ) ) . '. ';

		echo '</div>' . "\n";
				
		// --- fix setting icon ---
		if ( 'dashboard' == $context ) {
			$fix_setting_url = add_query_arg( 'page', 'radio-station', admin_url( 'admin.php' ) );
			$fix_setting_url = add_query_arg( 'tab', $settings_tab, $fix_setting_url );
			$fix_setting_url = add_query_arg( 'section', $settings_section, $fix_setting_url );
		} else {
			$fix_setting_url = 'javascript:void(0)';
		}
		echo '<a class="progress-icon-link" href="' . esc_url( $fix_setting_url ) . '"';
		if ( 'settings' == $context ) {
			echo ' onclick="jQuery(\'#' . esc_attr( $settings_tab ) . '-tab-button\').click();"';
		}
		echo '>';
			echo '<span class="progress-icon progress-add dashicons dashicons-playlist-audio"></span>';
		echo '</a>';
		echo '<a class="progress-label-link" href="' . esc_url( $fix_setting_url ) . '">';
			if ( 100 == $info_percent ) {
				echo '<div class="progress-label" title="' . esc_attr( __( 'Edit these Settings', 'radio-station' ) ) . '">' . esc_html( __( 'Edit', 'radio-station' ) ) . '</div>';
			} else {
				echo '<div class="progress-label" title="' . esc_attr( __( 'Fix this Setting', 'radio-station' ) ) . '">' . esc_html( __( 'Fix', 'radio-station' ) ) . '</div>';
			}
		echo '</a>';
		
	echo '</div>';


	// --- Archive Pages ---
	$progress_items++;
	$settings_tab = 'archives';
	$settings_section = 'post-types';
	$archives = array( 'show', 'override', 'playlist', 'genre', 'language' );
	$pro_archives = array( 'episode', 'team' );
	if ( defined( 'RADIO_STATION_PRO' ) ) {
		$archives = array_merge( $archives, $pro_archives );
	}
	$archives_count = count( $archives );
	$archive_count = 0;
	foreach ( $archives as $archive ) {
		$key = $archive . '_archive_page';
		if ( '' != $settings[$key] ) {
			$archive_count++;
		}
	}
	$archive_percent = round( ( $archive_count / $archives_count ), 2, PHP_ROUND_HALF_DOWN ) * 100;
	
	echo '<div class="progress-line">' . "\n";

		// --- progress icon ---
		echo '<div class="progress-icon dashicons ';
		if ( 100 == $archive_percent ) {
			echo 'progress-tick dashicons-yes-alt';
			$settings_progress++;
		} elseif ( $archive_percent > 50 ) {
			echo 'progress-alert dashicons-warning';
			$settings_progress = $settings_progress + 0.5;
		} else {
			echo 'progress-cross dashicons-dismiss';
			$settings_notdone++;
		}
		echo '"></div>' . "\n";

		echo '<div class="progress-text">' . "\n";
			
			echo esc_html( $archive_count ) . ' ' . esc_html( __( 'out of', 'radio-station' ) ) . ' ' . esc_html( $archives_count );
			echo ' ' . esc_html( __( 'Archive pages set', 'radio-station' ) ) . '. ';
			echo esc_html( $archive_percent ) . '% ' . esc_html( __( 'Archive coverage', 'radio-station' ) ) . '.';

		echo '</div>' . "\n";
				
		// --- fix setting icon ---
		if ( 'dashboard' == $context ) {
			$fix_setting_url = add_query_arg( 'page', 'radio-station', admin_url( 'admin.php' ) );
			$fix_setting_url = add_query_arg( 'tab', $settings_tab, $fix_setting_url );
			$fix_setting_url = add_query_arg( 'section', $settings_section, $fix_setting_url );
		} else {
			$fix_setting_url = 'javascript:void(0)';
		}
		echo '<a class="progress-icon-link" href="' . esc_url( $fix_setting_url ) . '"';
		if ( 'settings' == $context ) {
			echo ' onclick="jQuery(\'#' . esc_attr( $settings_tab ) . '-tab-button\').click();"';
		}
		echo '>';
			echo '<span class="progress-icon progress-add dashicons dashicons-playlist-audio"></span>';
		echo '</a>';
		echo '<a class="progress-label-link" href="' . esc_url( $fix_setting_url ) . '">';
			if ( 100 == $archive_percent ) {
				echo '<div class="progress-label" title="' . esc_attr( __( 'Edit these Settings', 'radio-station' ) ) . '">' . esc_html( __( 'Edit', 'radio-station' ) ) . '</div>';
			} else {
				echo '<div class="progress-label" title="' . esc_attr( __( 'Fix this Setting', 'radio-station' ) ) . '">' . esc_html( __( 'Fix', 'radio-station' ) ) . '</div>';
			}
		echo '</a>';
		
	echo '</div>';

	// --- Pro Archives Pages ---
	if ( !defined( 'RADIO_STATION_PRO' ) ) {
		
		$progress_items++;
		echo '<div class="progress-line">' . "\n";

			// --- progress icon ---
			echo '<div class="progress-icon dashicons progress-alert dashicons-warning"></div>' . "\n";

			echo '<div class="progress-text">' . "\n";
				echo esc_html( __( 'Episode and Team Archive pages available in Pro version.' ) );
			echo '</div>' . "\n";
					
			// --- upgrade to Pro / feature link ---
			$upgrade_url = radio_station_get_upgrade_url();
			echo '<a class="progress-icon-link" href="' . esc_url( $upgrade_url ) . '">' . "\n";
				echo '<div class="progress-icon progress-upgrade dashicons dashicons-unlock"></div>' . "\n";
			echo '</a>';
			echo '<a class="progress-label-link" href="' . esc_url( $upgrade_url ) . '">' . "\n";
				echo '<div class="progress-label" title="' . esc_attr( __( 'Upgrade to Radio Station PRO', 'radio-station' ) ) . '">' . esc_html( __( 'Go Pro', 'radio-station' ) ) . '</div>' . "\n";
			echo '</a>' . "\n";
			
		echo '</div>';
	}


	// ...


	// --- settings progress bar ---
	radio_station_progress_bar( $settings_progress, $settings_notdone, $progress_items );

}


// ------------
// Progress Bar
// ------------
function radio_station_progress_bar( $done, $undone, $items ) {
	$bar_width = 395;
	$progress_percent = round( ( $done / $items ), 2, PHP_ROUND_HALF_DOWN ) * 100;
	$progress_px = round( ( $progress_percent / 100 * $bar_width ), 0, PHP_ROUND_HALF_DOWN );
	$undone_percent = round( ( $undone / $items ), 2, PHP_ROUND_HALF_DOWN ) * 100;
	$undone_px = round( ( $undone_percent / 100 * $bar_width ), 0, PHP_ROUND_HALF_DOWN );
	$remainder_percent = 100 - $progress_percent - $undone_percent;
	$remainder_px = round( ( $remainder_percent / 100 * $bar_width ), 0, PHP_ROUND_HALF_DOWN );	
	echo '<table class="progress-bar" cellpadding="0" cellspacing="0"><tr>' . "\n";
		echo '<td class="progress-bar-done" style="width:' . esc_attr( $progress_px ) . 'px;"></div>' . "\n";
		echo '<td class="progress-bar-remainder" style="width:' . esc_attr( $remainder_px ) . 'px;"></div>' . "\n";
		if ( $undone_percent > 0 ) {
			echo '<td class="progress-bar-undone" style="width:' . esc_attr( $undone_px ) . 'px;"></div>' . "\n";
		}
		echo '<td class="progress-bar-label">' . esc_html( $progress_percent ) . '%</td>' . "\n";
	echo '</tr></table>' . "\n";
}

// -------------------------
// Enqueue Onboarding Styles
// -------------------------
add_action( 'admin_init', 'radio_station_enqueue_onboarding_styles' );
function radio_station_enqueue_onboarding_styles() {
	
	global $current_screen;
	echo '<span style="display:none;">Current Screen' . print_r( $current_screen, true ) . '</span>';
	
	// --- add onboarding styles ---
	if ( ( 'dashboard' == $current_screen->base )
	  || ( isset( $_REQUEST['page'] ) && ( 'radio-station' == $_REQUEST['page'] ) ) ) {
		radio_station_enqueue_style( 'admin' );
		$css = radio_station_onboarding_styles();
		radio_station_add_inline_style( 'rs-admin', $css );
	}
}

// -----------------
// Onboarding Styles
// -----------------
function radio_station_onboarding_styles() {
	$css = ".progress-heading {text-align:center; font-size:15px; font-variant:small-caps; letter-spacing:5px;  margin-bottom:5px;}
	.progress-list {margin-bottom:7px;}
	.progress-list-item {display:inline-block; margin-right:25px;}
	.progress-list-item:last-child {margin-right:0;}
	.progress-list-item a {text-decoration:none;}
	.progress-list-item a:hover {text-decoration:underline; font-weight:bold;}
	.progress-line {font-size:13px;}
	.progress-bar {width:96%; height:7px; margin-top:7px; margin-bottom:15px; padding:0;}
	.progress-bar-label {font-size:13px; font-weight:bold; margin-top:7px; text-indent:15px; line-height:7px;}
	/* .progress-bar, .progress-bar-done, .progress-bar-remainder, .progress-bar-label {display:inline-block; vertical-align:middle;} */
	.progress-bar-done {background-color:#00AA77; height:7px; border:1px solid #555; border-right:0;}
	.progress-bar-remainder {background-color:#FF9900; height:7px; border:1px solid #555;}
	.progress-bar-undone {background-color:#EE0000; height:7px; border:1px solid #555; border-left:0; }
	.progress-count {font-weight:bold; font-size:14px; background-color:#CCC; border-radius:10px;}
	.progress-icon {width:25px; height:25px;}
	.progress-icon.progress-tick, .progress-icon.progress-quickstart {color:#00AA00;}
	.progress-icon.progress-cross {color:#EE0000;}
	.progress-icon.progress-alert {color:#FF9900;}
	.progress-icon.progress-add, .progress-icon.progress-settings {color:#0077CC;}
	.progress-icon.progress-upgrade {color:#AA00AA; font-size:22px;}
	.progress-icon.progress-docs {color:#CC7700;}
	.progress-text {width:350px;}
	.progress-icon, .progress-text, .progress-icon-link, .progress-label-link {display:inline-block; vertical-align:middle;}
	.progress-text a, .progress-icon-link, .progress-label-link {text-decoration:none;}
	.progress-text a:hover, .progress-label-link:hover {text-decoration:underline;}
	.progress-label {font-size:11px; margin-top:-5px;}
	";

	return $css;
}