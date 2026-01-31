<?php

// ================================
// === Radio Station User Roles ===
// ================================
// 2.5.0: separated from radio-station.php

if ( !defined( 'ABSPATH' ) ) exit;

// === User Roles ===
// - Set Roles and Capabilities
// - Admin Fix for DJ / Host Role Label
// - maybe Revoke Edit Show Capability
// - Map Meta Cap Override
// - Get Show User IDs
// - Get Override User IDs

// --------------------------
// Set Roles and Capabilities
// --------------------------
// 2.5.0: run set roles frontend and backend regardless of multisite
// if ( is_multisite() ) {
	add_action( 'init', 'radio_station_set_roles', 10, 0 );
	// 2.3.1: added possible fix for roles not being set on multisite
	add_action( 'admin_init', 'radio_station_set_roles', 10, 0 );
// } else {
// 	add_action( 'admin_init', 'radio_station_set_roles', 10, 0 );
// }
function radio_station_set_roles() {

	global $radio_station_data, $wp_roles;

	// 2.5.0: added check to run once only per load
	if ( isset( $radio_station_data['roles_set'] ) ) {
		return;
	}
	$radio_station_data['roles_set'] = true;

	// --- set only necessary capabilities for DJs ---
	$caps = array(
		// --- shows ---
		'edit_shows'               => true,
		'edit_published_shows'     => true,
		'edit_others_shows'        => true,
		'read_shows'               => true,
		// --- overrides ---
		// 2.5.18: add override edit capability
		'edit_overrides'           => true,
		'edit_published_overrides' => true,
		'edit_others_overrides'    => true,
		'read_overrides'           => true,
		// --- playlists ---
		// by default DJs cannot edit others playlists
		// 'edit_others_playlists'    => false,
		'edit_playlists'           => true,
		'edit_published_playlists' => true,
		'read_playlists'           => true,
		'publish_playlists'        => true,
		// --- posts ---
		'edit_posts'               => true,
		'edit_published_posts'     => true,
		'publish_posts'            => true,
		'delete_posts'             => true,
		// --- misc ---
		'read'                     => true,
		'upload_files'             => true,
	);

	// --- add the DJ role ---
	// 2.3.0: translate DJ role name
	// 2.3.0: change label from 'DJ' to 'DJ / Host'
	// 2.3.0: check/add profile capabilities to hosts
	$wp_roles->add_role( 'dj', __( 'DJ / Host', 'radio-station' ), $caps );
	$role_caps = $wp_roles->roles['dj']['capabilities'];
	// 2.3.1.1: added check if role caps is an array
	if ( !is_array( $role_caps ) ) {
		$role_caps = array();
	}
	$host_caps = array(
		'edit_hosts',
		'edit_published_hosts',
		'delete_hosts',
		'read_hosts',
		'publish_hosts',
	);
	foreach ( $host_caps as $cap ) {
		if ( !array_key_exists( $cap, $role_caps ) || !$role_caps[$cap] ) {
			$wp_roles->add_cap( 'dj', $cap, true );
		}
	}
	// 2.3.3.9: fix for existing DJ role old name
	$wp_roles->roles['dj']['name'] = __( 'DJ / Host', 'radio_station' );
	$wp_roles->role_names['dj'] = __( 'DJ / Host', 'radio_station' );

	// --- add Show Producer role ---
	// 2.3.0: add equivalent capability role for Show Producer
	$wp_roles->add_role( 'producer', __( 'Show Producer', 'radio-station' ), $caps );
	$role_caps = $wp_roles->roles['producer']['capabilities'];
	// 2.3.1.1: added check if role caps is an array
	if ( !is_array( $role_caps ) ) {
		$role_caps = array();
	}
	$producer_caps = array(
		'edit_producers',
		'edit_published_producers',
		'delete_producers',
		'read_producers',
		'publish_producers',
	);
	foreach ( $producer_caps as $cap ) {
		if ( !array_key_exists( $cap, $role_caps ) || !$role_caps[$cap] ) {
			$wp_roles->add_cap( 'producer', $cap, true );
		}
	}

	// --- grant all capabilities to Show Editors ---
	// 2.3.0: set Show Editor role capabilities
	$caps = array(
		// --- shows ---
		'edit_shows'             => true,
		'edit_published_shows'   => true,
		'edit_others_shows'      => true,
		'edit_private_shows'     => true,
		'delete_shows'           => true,
		'delete_published_shows' => true,
		'delete_others_shows'    => true,
		'delete_private_shows'   => true,
		'read_shows'             => true,
		'publish_shows'          => true,
		// --- overrides ---
		'edit_overrides'             => true,
		'edit_overrides_playlists'   => true,
		'edit_others_overrides'      => true,
		'edit_private_overrides'     => true,
		'delete_overrides'           => true,
		'delete_published_overrides' => true,
		'delete_others_overrides'    => true,
		'delete_private_overrides'   => true,
		'read_overrides'             => true,
		'publish_overrides'          => true,
		// --- playlists ---
		'edit_playlists'             => true,
		'edit_published_playlists'   => true,
		'edit_others_playlists'      => true,
		'edit_private_playlists'     => true,
		'delete_playlists'           => true,
		'delete_published_playlists' => true,
		'delete_others_playlists'    => true,
		'delete_private_playlists'   => true,
		'read_playlists'             => true,
		'publish_playlists'          => true,
		// --- hosts ---
		'edit_hosts'           => true,
		'edit_published_hosts' => true,
		'edit_others_hosts'    => true,
		'delete_hosts'         => true,
		'read_hosts'           => true,
		'publish_hosts'        => true,
		// --- producers ---
		'edit_producers'           => true,
		'edit_published_producers' => true,
		'edit_others_producers'    => true,
		'delete_producers'         => true,
		'read_producers'           => true,
		'publish_producers'        => true,
		// --- posts ---
		'edit_posts'           => true,
		'edit_others_posts'    => true,
		'edit_published_posts' => true,
		'publish_posts'        => true,
		'delete_posts'         => true,
		// --- misc ---
		'read'                 => true,
		'upload_files'         => true,
	);

	// --- add the Show Editor role ---
	// 2.3.0: added Show Editor role
	$wp_roles->add_role( 'show-editor', __( 'Show Editor', 'radio-station' ), $caps );

	// --- check plugin setting for authors ---
	$add_author_caps = radio_station_get_setting( 'add_author_capabilities' );
	if ( 'yes' === (string) $add_author_caps ) {

		// --- grant show edit capabilities to author users ---
		$author_caps = $wp_roles->roles['author']['capabilities'];
		// 2.3.1.1: added check if role caps is an array
		if ( !is_array( $author_caps ) ) {
			$author_caps = array();
		}
		$extra_caps = array(
			// --- shows ---
			'edit_shows',
			'edit_published_shows',
			'read_shows',
			'publish_shows',
			// --- overrides ---
			'edit_overrides',
			'edit_published_overrides',
			'read_overrides',
			'publish_overrides',
			// --- playlists ---
			'edit_playlists',
			'edit_published_playlists',
			'read_playlists',
			'publish_playlists',
		);
		foreach ( $extra_caps as $cap ) {
			if ( !array_key_exists( $cap, $author_caps ) || ( !$author_caps[$cap] ) ) {
				$wp_roles->add_cap( 'author', $cap, true );
			}
		}
	}

	// --- specify edit caps (for editors and admins) ---
	// 2.3.0: added show override, host and producer capabilities
	$edit_caps = array(
		// --- shows ---
		'edit_shows',
		'edit_published_shows',
		'edit_others_shows',
		'edit_private_shows',
		'delete_shows',
		'delete_published_shows',
		'delete_others_shows',
		'delete_private_shows',
		'read_shows',
		'publish_shows',
		// --- overrides ---
		'edit_overrides',
		'edit_published_overrides',
		'edit_others_overrides',
		'edit_private_overrides',
		'delete_overrides',
		'delete_published_overrides',
		'delete_others_overrides',
		'delete_private_overrides',
		'read_overrides',
		'publish_overrides',
		// --- playlists ---
		'edit_playlists',
		'edit_published_playlists',
		'edit_others_playlists',
		'edit_private_playlists',
		'delete_playlists',
		'delete_published_playlists',
		'delete_others_playlists',
		'delete_private_playlists',
		'read_playlists',
		'publish_playlists',
		// --- hosts ---
		'edit_hosts',
		'edit_published_hosts',
		'edit_others_hosts',
		'delete_hosts',
		'delete_others_hosts',
		'read_hosts',
		'publish_hosts',
		// --- producers ---
		'edit_producers',
		'edit_published_producers',
		'edit_others_producers',
		'delete_producers',
		'delete_others_producers',
		'read_producers',
		'publish_producers',
	);

	// --- check plugin setting for editors ---
	$add_editor_caps = radio_station_get_setting( 'add_editor_capabilities' );
	if ( 'yes' === (string) $add_editor_caps ) {

		// --- grant show edit capabilities to editor users ---
		$editor_caps = $wp_roles->roles['editor']['capabilities'];
		// 2.3.1.1: added check if capabilities is an array
		if ( !is_array( $editor_caps ) ) {
			$editor_caps = array();
		}
		foreach ( $edit_caps as $cap ) {
			if ( !array_key_exists( $cap, $editor_caps ) || ( !$editor_caps[$cap] ) ) {
				$wp_roles->add_cap( 'editor', $cap, true );
			}
		}
	}

	// --- grant all plugin capabilities to admin users ---
	$admin_caps = $wp_roles->roles['administrator']['capabilities'];
	// 2.3.1.1: added check if capabilities is an array
	if ( !is_array( $admin_caps ) ) {
		$admin_caps = array();
	}
	foreach ( $edit_caps as $cap ) {
		if ( !array_key_exists( $cap, $admin_caps ) || ( !$admin_caps[$cap] ) ) {
			$wp_roles->add_cap( 'administrator', $cap, true );
		}
	}

}

// ----------------------------------
// Admin Fix for DJ / Host Role Label
// ----------------------------------
// 2.3.3.9: added for user edit screen crackliness
add_filter( 'editable_roles', 'radio_station_role_check_test', 9 );
function radio_station_role_check_test( $roles ) {
	if ( RADIO_STATION_DEBUG && is_admin() ) {
		echo '<span style="display:none;">DJ Role: ' . esc_html( print_r( $roles['dj'], true ) ) . '</span>';
	}
	$roles['dj']['name'] = __( 'DJ / Host', 'radio-station' );
	return $roles;
}

// ---------------------------------
// maybe Revoke Edit Show Capability
// ---------------------------------
// (revoke ability to edit show if user is not assigned to it)
add_filter( 'user_has_cap', 'radio_station_revoke_show_edit_cap', 10, 4 );
function radio_station_revoke_show_edit_cap( $allcaps, $caps, $args, $user ) {

	// 2.5.18: remove global post and use args
	global $wp_roles;

	// 2.4.0.4.1: fix for early capability check plugin conflict
	if ( !function_exists( 'radio_station_get_setting' ) ) {
		return $allcaps;
	}

	// --- check if super admin ---
	// 2.3.3.6: get user object from fourth argument instead
	// ? fix to not revoke edit caps from super admin ?
	// (not implemented, as causing a connection reset error!)
	// if ( function_exists( 'is_super_admin' ) && is_super_admin() ) {
	//	return $allcaps;
	// }

	// --- debug passed capability arguments ---
	$cap_debug = false;
	if ( isset( $_REQUEST['cap-debug'] ) && ( '1' === sanitize_text_field( $_REQUEST['cap-debug'] ) ) ) {
		echo '<input type="hidden" name="cap-debug" value="1">';
		$cap_debug = true;
		// echo '<span style="display:none;">All Caps: ' . esc_html( print_r( $allcaps, true ) ) . '</span>';
		echo '<span style="display:none;">Caps: ' . esc_html( print_r( $caps, true ) ) . '</span>';
		echo '<span style="display:none;">Cap Args: ' . esc_html( print_r( $args, true ) ) . '</span>';
	}

	// --- get capability args ---
	// 2.5.18: maybe bug out early
	$capability = $args[0];
	$capabilities = array( 'edit_post', 'edit_show', 'edit_override' );
	if ( !in_array( $capability, $capabilities ) ) {
		return $allcaps;
	}
	// 2.5.18: get object ID from args not global post
	$object_id = isset( $args[2] ) ? $args[2] : null;

	// --- check for editor role ---
	// 2.3.3.6: check editor roles first separately
	// 2.4.0.4: only add WordPress editor role if on in settings
	$editor_roles = array( 'administrator', 'show-editor' );
	$editor_role_caps = radio_station_get_setting( 'add_editor_capabilities' );
	if ( 'yes' == $editor_role_caps ) {
		$editor_roles[] = 'editor';
	}
	foreach ( $editor_roles as $role ) {
		if ( in_array( $role, $user->roles ) ) {
			return $allcaps;
		}
	}

	// --- check for author role ---
	$author_roles = array( 'dj', 'producer' );
	$author_role_caps = radio_station_get_setting( 'add_author_capabilities' );
	if ( 'yes' == $author_role_caps ) {
		$author_roles[] = 'author';
	}

	// --- set override caps ---
	$add_caps = array(
		// --- posts ---
		'edit_posts'           => true,
		'edit_others_posts'    => true,
		'edit_published_posts' => true,
		'publish_posts'        => true,
		'delete_posts'         => true,
		// --- overrides ---
		'edit_overrides',
		'edit_published_overrides',
		'edit_others_overrides',
		// 'edit_private_overrides',
		'delete_overrides',
		'delete_published_overrides',
		// 'delete_others_overrides',
		// 'delete_private_overrides',
		'read_overrides',
		'publish_overrides',
		// --- misc ---
		'read'                 => true,
		'upload_files'         => true,
	);
	
	// --- maybe add override caps ---
	foreach ( $author_roles as $role ) {
		if ( in_array( $role, $user->roles ) ) {
			foreach ( $add_caps as $add_cap ) {
				if ( !array_key_exists( $add_cap, $allcaps ) ) {
					$allcaps[$add_cap] = 1;
				}
			}
		}
	}	
		
	// 2.3.0: added object and property_exists check to be safe
	if ( !is_null( $object_id ) ) {
		$post = get_post( $object_id );
	}
	if ( isset( $post ) && is_object( $post ) && property_exists( $post, 'post_type' ) && isset( $post->post_type ) ) {

		// --- show capabilities check ---
		// 2.5.18: moved check for show slug outside
		if ( ( RADIO_STATION_SHOW_SLUG == $post->post_type ) && ( 'edit_show' == $capability ) ) {

			// --- get roles with edit shows capability ---
			/* $edit_show_roles = $edit_others_shows_roles = array();
			if ( isset( $wp_roles->roles ) && is_array( $wp_roles->roles ) ) {
				foreach ( $wp_roles->roles as $name => $role ) {
					// 2.3.0: fix to skip roles with no capabilities assigned
					if ( isset( $role['capabilities'] ) ) {
						foreach ( $role['capabilities'] as $capname => $capstatus ) {
							// 2.3.0: change publish_shows cap check to edit_shows
							if ( ( 'edit_shows' === $capname ) && (bool) $capstatus ) {
								if ( !in_array( $name, $edit_show_roles ) ) {
									$edit_show_roles[] = $name;
								}
							}
							// 2.3.3.6: add check for edit_others_shows capability
							if ( ( 'edit_others_shows' === $capname ) && (bool) $capstatus ) {
								if ( !in_array( $name, $edit_others_shows_roles ) ) {
									$edit_others_shows_roles[] = $name;
								}
							}
						}
					}
				}
			}

			// 2.5.18: fix to move false before edit others show check
			$found = false;
			// 2.3.3.6: preserve if user has edit_others_shows capability
			foreach ( $edit_others_shows_roles as $role ) {
				if ( in_array( $role, $user->roles ) ) {
					// 2.4.0.4: do not automatically assume capability match
					$found = true;
				}
			}
			if ( !$found ) {
				// 2.2.8: remove strict in_array checking
				foreach ( $edit_show_roles as $role ) {
					if ( in_array( $role, $user->roles ) ) {
						$found = true;
					}
				}
			} */

			// --- maybe revoke edit show capability for post ---
			// 2.3.3.6: fix to incorrect logic for removing edit show capability
			// if ( $found ) {

				// 2.3.0: removed is_admin check (so works with frontend edit show link)

				// --- get show hosts and producers ---
				// 2.5.18: use function to get show user IDs
				$users = radio_station_get_show_user_ids( $post->ID );
				
				// ---- revoke editing capability if not assigned to this show ---
				// 2.2.8: remove strict in_array checking
				// 2.3.0: also check new Producer role
				if ( !in_array( $user->ID, $users ) ) {

					// --- remove the edit_shows capability ---
					$allcaps['edit_shows'] = false;
					$allcaps['edit_others_shows'] = false;
					$allcaps['edit_published_shows'] = false;
					if ( $cap_debug ) {
						echo '<span style="display:none;">Removed Edit Show Caps for Post ID ' . esc_html( $post->ID ) . '</span>';
					}
				} else {
					// 2.4.0.4: add edit others shows capability
					// (fix for when not original show author)
					$allcaps['edit_shows'] = true;
					$allcaps['edit_others_shows'] = true;
					$allcaps['edit_published_shows'] = true;
					if ( $cap_debug ) {
						echo '<span style="display:none;">Added Edit Show Caps for Post ID ' . esc_html( $post->ID ) . '</span>';
					}

				}
			// }

		}

		// --- override capabilites check ---
		// 2.5.18: added override post type handling
		if ( ( RADIO_STATION_OVERRIDE_SLUG == $post->post_type ) && ( 'edit_override' == $capability ) ) {

			// --- get roles with edit overrides capability ---
			/* $edit_override_roles = $edit_others_overrides_roles = array();
			if ( isset( $wp_roles->roles ) && is_array( $wp_roles->roles ) ) {
				foreach ( $wp_roles->roles as $name => $role ) {
					if ( isset( $role['capabilities'] ) ) {
						foreach ( $role['capabilities'] as $capname => $capstatus ) {
							if ( ( 'edit_overrides' === $capname ) && (bool) $capstatus ) {
								if ( !in_array( $name, $edit_override_roles ) ) {
									$edit_override_roles[] = $name;
								}
							}
							if ( ( 'edit_others_overrides' === $capname ) && (bool) $capstatus ) {
								if ( !in_array( $name, $edit_others_overrides_roles ) ) {
									$edit_others_overrides_roles[] = $name;
								}
							}
						}
					}
				}
			}

			// --- check if capability found ---
			$found = false;
			foreach ( $edit_others_overrides_roles as $role ) {
				if ( in_array( $role, $user->roles ) ) {
					$found = true;
				}
			}
			if ( !$found ) {
				foreach ( $edit_override_roles as $role ) {
					if ( in_array( $role, $user->roles ) ) {
						$found = true;
					}
				}
			} */

			// --- maybe revoke edit override capability for post ---
			// if ( $found ) {

				$users = radio_station_get_override_user_ids( $post->ID );

				// ---- revoke editing capability if not assigned to this show ---
				if ( !in_array( $user->ID, $users ) )  {

					// --- remove the edit_overrides capability ---
					$allcaps['edit_overrides'] = false;
					$allcaps['edit_others_overrides'] = false;
					$allcaps['edit_published_overrides'] = false;
					if ( $cap_debug ) {
						echo '<span style="display:none;">Removed Edit Override Caps for Post ID ' . esc_html( $post->ID ) . '</span>';
					}

				} else {
					$allcaps['edit_overrides'] = true;
					$allcaps['edit_others_overrides'] = true;
					$allcaps['edit_published_overrides'] = true;
					if ( $cap_debug ) {
						echo '<span style="display:none;">Added Edit Override Caps for Post ID ' . esc_html( $post->ID ) . '</span>';
					}
				}

			// }

		}
	}

	return $allcaps;
}

// ---------------------
// Map Meta Cap Override
// ---------------------
// https://gist.github.com/brandonjp/bf8a2ef3cab014b5ae3dba3e510bca2d
add_filter( 'map_meta_cap', 'radio_station_map_meta_cap_for_nonauthor', 10, 4 );
function radio_station_map_meta_cap_for_nonauthor( $caps, $cap, $user_id, $args ) {

	global $pagenow;
	// echo "CAPS BEFORE: "; print_r( $caps );

	$edit = array( 'edit_post', 'edit_show', 'edit_override' );
	$edit_others = array( 'edit_others_posts', 'edit_others_shows', 'edit_others_overrides' );
	
	if ( in_array( $cap, $edit ) || in_array( $cap, $edit_others ) ) {
	
		$post = false;
		if ( in_array( $cap, $edit ) ) {
			$post = get_post( (int) $args[0] );
		} else if ( in_array( $cap, $edit_others )  && ( 'post.php' === $pagenow ) && !empty( $_POST['post_ID'] ) ) {
			$post = get_post( (int) $_POST['post_ID'] );
		}

		if ( $post ) {

			$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG );
			if ( in_array( $post->post_type, $post_types ) ) {
				
				// --- check for editor role ---
				$user = wp_get_current_user();
				$editor_role_caps = radio_station_get_setting( 'add_editor_capabilities' );
				if ( ( 'yes' == $editor_role_caps ) && in_array( 'editor', $user->roles ) ) {
					$allowed = true;
				} else {
					// --- check assigned users ---
					if ( RADIO_STATION_SHOW_SLUG == $post->post_type ) {
						$users = radio_station_get_show_user_ids( $post->ID );
						$type = 'show';
					} elseif ( RADIO_STATION_OVERRIDE_SLUG == $post->post_type ) {
						$users = radio_station_get_override_user_ids( $post->ID );
						$type = 'override';
					}
					if ( in_array( $user->ID, $users ) ) {
						$allowed = true;
					}
				}

				if ( $allowed ) {

					// --- remove the need for these caps ---
					$remove_caps = array( 'edit_others_posts', 'edit_others_' . $type . 's', 'edit_published_posts', 'edit_published_' . $type . 's' );
					foreach ( $remove_caps as $remove_cap ) {
						$key = array_search( $remove_cap, $caps );
						if ( false !== $key ) {
							unset( $caps[$key] );
						}
					}

				}
			}
		}
	}

	// echo "CAPS AFTER: "; print_r( $caps );
	return $caps;
}

// -----------------
// Get Show User IDs
// -----------------
// 2.5.18: moved out from user_has_cap filter
function radio_station_get_show_user_ids( $post_id ) {

	// --- get show hosts and producers ---
	$hosts = get_post_meta( $post_id, 'show_user_list', true );
	$producers = get_post_meta( $post_id, 'show_producer_list', true );

	// 2.3.0.4: convert possible (old) non-array values
	if ( !$hosts || empty( $hosts ) ) {
		$hosts = array();
	} elseif ( !is_array( $hosts ) ) {
		$hosts = array( $hosts );
	}
	if ( !$producers || empty( $producers ) ) {
		$producers = array();
	} elseif ( !is_array( $producers ) ) {
		$producers = array( $producers );
	}

	$users = array_merge( $hosts, $producers );
	return $users;
}

// ---------------------
// Get Override User IDs
// ---------------------
// 2.5.18: moved out from user_has_cap filter
function radio_station_get_override_user_ids( $post_id ) {

	// 2.5.18: merge linked show AND override users
	// (so hosts/producers of linked show always can edit)
	$show_id = get_post_meta( $post_id, 'linked_show_id', true );

	// --- get show hosts and override hosts ---
	$hosts = array();
	if ( $show_id ) {
		$hosts_a = get_post_meta( $show_id, 'show_user_list', true );
		if ( $hosts_a ) {
			$hosts_a = is_array( $hosts_a ) ? $hosts_a : array( $hosts_a );
			$hosts = array_merge( $hosts, $hosts_a );
		}
	}
	$hosts_b = get_post_meta( $post_id, 'show_user_list', true );
	if ( $hosts_b ) {
		$hosts_b = is_array( $hosts_b ) ? $hosts_b : array( $hosts_b );
		$hosts = array_merge( $hosts, $hosts_b );
		$hosts = array_unique( $hosts );
	}

	// --- get show producers and override producers ---
	$producers = array();
	if ( $show_id ) {
		$producers_a = get_post_meta( $show_id, 'show_producer_list', true );
		if ( $producers_a ) {
			$producers_a = is_array( $producers_a ) ? $producers_a : array( $producers_a );
			$producers = array_merge( $producers, $producers_a );
		}
	}
	$producers_b = get_post_meta( $post_id, 'show_producer_list', true );
	if ( $producers_b ) {
		$producers_b = is_array( $producers_b ) ? $producers_b : array( $producers_b );
		$producers = array_merge( $producers, $producers_b );
		$producers = array_unique( $producers );
	}

	// echo 'Hosts: ' . print_r( $hosts, true );
	// echo 'Producers: ' . print_r( $producers, true );
	$users = array_merge( $hosts, $producers );
	return $users;
}

