# Radio Station Plugin Filters

***

## Settings Filters

To programmatically override any of the Plugin Settings available from the Settings Page, see [Options Documentation](./Options.md) 

## Data Filters

There are filters throughout the plugin that allow you to override data values and plugin output. We employ the practice of adding as many of these as possible to allow users of the plugin to customize it's behaviour without needing to modify the plugin's code - as these kind of modifications are overwritten with plugin updates.

You can add your own custom filters via a Code Snippets plugin (which has the advantage of checking syntax for you), or in your Child Theme's `functions.php`, or in any file with a PHP extension in your `/wp-content/mu-plugins/` directory. 

## Finding Filters

You can find these filters by searching any of the PHP plugin files for: `apply_filters( 'radio_`

## Filter Values and Arguments

Note the first argument passed to `apply_filters` is the name of the filter, the second argument is the value to be filtered. Additional arguments may also be provided to the filter so that you can match changes to specific contexts.

## Filter Examples

You can find many examples and tutorials of how to use WordPress filters online. Here is a generic filter example to help you get started with filters. This one will add custom HTML to the bottom of the Current Show Widget, regardless of which Show is playing:

```
add_filter( 'radio_station_current_show_custom_display', 'my_custom_function_name' );
function my_custom_function_name( $html ) {
    $html .= "<div>Now taking phone requests!</div>";
    return $html;
}
```

Note if a filter has additional arguments, and you wish to check them, you need to specify the number of arguments. To do this you must also include a filter priority. Here `10` is the (default) priority of when to run the filter and `3` is the number of arguments passed to the filter function. This example will add custom HTML to the bottom of the Current Show widget only if the Show ID is 20:

```
add_filter( 'radio_station_current_show_custom_display', 'my_custom_function_name', 10, 3 );
function my_custom_function_name( $html, $show_id, $atts ) {
    if ( 20 == $show_id ) {
        $html .= "<div>Welcoming our newest DJ!</div>";
    }
    return $html;
}
```

## Filter List

Here is a full list of available filters within the plugin, grouped by file and function for ease of reference. 

| File / *Function* | Filter | Value | Extra Args |
| - | - |
|**radio-station.php**||||
|*radio_station_localize_script*|`radio_station_time_separator` | ` ':'` | `'javascript'`|
|*radio_station_streaming_data*|`radio_station_localization_script` | ` $js` | |
| |`radio_station_streaming_data` | ` $data` | `$station`|
|*radio_station_doing_template*|`radio_station_player_allowed_origins` | ` $allowed` | |
|*radio_station_phone_number*|`radio_station_template_dir_hierarchy` | ` $dirs` | `$template`, `$paths`|
|*radio_station_automatic_pages_content_get*|`radio_station_automatic_schedule_atts` | ` $atts` | |
| |`radio_station_automatic_show_archive_atts` | ` $atts` | |
| |`radio_station_automatic_override_archive_atts` | ` $atts` | |
| |`radio_station_automatic_playlist_archive_atts` | ` $atts` | |
| |`radio_station_automatic_genre_archive_atts` | ` $atts` | |
| |`radio_station_automatic_languagee_archive_atts` | ` $atts` | |
| |`radio_station_'.$post_type.'_content_templates` | ` $templates` | `$post_type`|
| |`radio_station_single_template_post_data` | ` $post` | `$post_type`|
| |`radio_station_content_'.$post_type` | ` $output` | `$post_id`|
|*radio_station_override_content_template*|`radio_station_host_templates` | ` $templates` | |
| |`radio_station_producer_templates` | ` $templates` | |
|*radio_station_archive_template_hierarchy*|`radio_station_show_related_post_types` | ` $post_types` | |
| |`radio_station_link_to_show_positions` | ` $positions` | `$post_type`, `$post`|
| |`radio_station_link_to_show_before` | ` $before` | `$post`, `$related_shows`|
| |`radio_station_link_to_show_after` | ` $after` | `$post`, `$related_shows`|
|**radio-station-admin.php**||||
|*radio_station_license_activation_link*|`radio_station_settings_capability` | ` 'manage_options'` | |
| |`radio_station_menu_position` | ` 5` | |
| |`radio_station_manage_options_capability` | ` 'manage_options'` | |
| |`radio_station_export_playlists` | ` false` | |
|*radio_station_role_editor*|`radio_station_role_editor_message` | ` true` | |
|**includes/data-feeds.php**||||
|*radio_station_api_discovery_link*|`radio_station_api_discovery_header` | ` $header` | |
| |`radio_station_api_discovery_link` | ` $link` | |
| |`radio_station_api_discovery_rsd` | ` $link` | |
|*radio_station_add_station_data*|`radio_station_station_data` | ` $station_data` | |
|*radio_station_get_broadcast_data*|`radio_station_broadcast_data` | ` $broadcast` | |
|*radio_station_get_shows_data*|`radio_station_shows_data` | ` $shows` | `$show`|
|*radio_station_get_languages_data*|`radio_station_genres_data` | ` $genres` | `$genre`|
|*radio_station_station_endpoint*|`radio_station_languages_data` | ` $languages_data` | `$language`|
|*radio_station_register_rest_routes*|`radio_station_route_slug_base` | ` 'radio'` | |
| |`radio_station_route_slug_station` | ` 'station'` | |
| |`radio_station_route_slug_broadcast` | ` 'broadcast'` | |
| |`radio_station_route_slug_schedule` | ` 'schedule'` | |
| |`radio_station_route_slug_shows` | ` 'shows'` | |
| |`radio_station_route_slug_genres` | ` 'genres'` | |
| |`radio_station_route_slug_languages` | ` 'languages'` | |
|*radio_station_route_radio*|`radio_station_route_urls` | ` $routes` | |
| |`radio_station_route_slug_base` | ` 'radio'` | |
|*radio_station_route_station*|`radio_station_route_station` | ` $station` | `$request`|
| |`radio_station_route_broadcast` | ` $broadcast` | `$request`|
|*radio_station_route_schedule*|`radio_station_route_schedule` | ` $schedule` | `$request`|
|*radio_station_route_genres*|`radio_station_route_shows` | ` $show_list` | `$request`|
|*radio_station_route_languages*|`radio_station_route_genres` | ` $genre_list` | `$request`|
| |`radio_station_route_languages` | ` $language_list` | `$request`|
|**includes/master-schedule.php**||||
|*radio_station_master_schedule*|`radio_station_master_schedule_default_atts` | ` $defaults` | `$view`, `$views`|
| |`radio_station_schedule_clock_atts` | ` array()` | `$atts`|
| |`radio_station_schedule_timezone_atts` | ` array()` | `$atts`|
| |`radio_station_schedule_control_order` | ` $control_order` | `$atts`|
| |`radio_station_schedule_controls` | ` $controls` | `$atts`|
| |`radio_station_schedule_controls_output` | ` $output` | `$atts`|
| |`radio_station_schedule_override` | ` $output` | `$atts`|
| |`master_schedule_table_view` | ` $html` | `$atts`|
| |`master_schedule_tabs_view` | ` $html` | `$atts`|
| |`master_schedule_list_view` | ` $html` | `$atts`|
|*radio_station_ajax_schedule_loader*|`radio_station_master_schedule_loader_js` | ` $js` | |
|*radio_station_master_schedule_genre_selector*|`radio_station_master_schedule_load_script` | ` $js` | `$atts`|
| |`radio_station_master_schedule_table_js` | ` $js` | |
|**includes/post-types.php**||||
|*radio_station_create_post_types*|`radio_station_post_type_show` | ` $post_type` | |
| |`radio_station_post_type_playlist` | ` $post_type` | |
| |`radio_station_post_type_override` | ` $post_type` | |
| |`radio_station_host_interface` | ` false` | |
| |`radio_station_post_type_host` | ` $post_type` | |
| |`radio_station_producer_interface` | ` false` | |
|*radio_station_post_type_editor*|`radio_station_post_type_producer` | ` $post_type` | |
|*radio_station_add_featured_image_support*|`radio_station_admin_bar_post_types` | ` $post_types` | `'new'`|
| |`radio_station_admin_bar_post_types` | ` $post_types` | `'edit'`|
| |`radio_station_admin_bar_post_types` | ` $post_types` | `'view'`|
| |`radio_station_genre_taxonomy_args` | ` $args` | |
| |`radio_station_language_taxonomy_args` | ` $args` | |
|**includes/post-types-admin.php**||||
|*radio_remove_language*|`radio_station_language_edit_styles` | ` $css` | |
| |`radio_station_language_edit_script` | ` $js` | |
| |`radio_station_metabox_position` | ` 'rstop'` | `'shows'`|
|*radio_station_add_show_hosts_metabox*|`radio_station_show_edit_styles` | ` $css` | |
|*radio_station_add_show_producers_metabox*|`radio_station_metabox_position` | ` 'rstop'` | `'shifts'`|
|*radio_station_shift_edit_script*|`radio_station_shift_list_edit_styles` | ` $css` | |
| |`radio_station_shift_edit_script` | ` $js` | |
|*radio_station_add_show_helper_box*|`radio_station_metabox_position` | ` 'rstop'` | `'helper'`|
|*radio_station_add_override_show_metabox*|`radio_station_metabox_position` | ` 'rstop'` | `'overrides'`|
| |`radio_station_override_edit_styles` | ` $css` | |
|*radio_station_override_show_script*|`radio_station_override_show_script` | ` $js` | |
| |`radio_station_metabox_position` | ` 'rstop'` | `'overrides'`|
| |`radio_station_override_list_edit_styles` | ` $css` | |
|*radio_station_override_save_data*|`radio_station_override_edit_script` | ` $js` | |
|*radio_station_override_sortable_columns*|`radio_station_show_avatar` | ` $thumbnail_url` | `$post_id`|
|*radio_station_override_past_future_filter*|`radio_station_overrides_past_future_default` | ` $pastfuture` | |
| |`radio_station_metabox_position` | ` 'rstop'` | `'playlist'`|
|*radio_track_add*|`radio_station_tracks_list_styles` | ` $css` | |
|**includes/shortcodes.php**||||
|*radio_station_clock_shortcode*|`radio_station_timezone_shortcode` | ` $output` | `$atts`|
|*radio_station_archive_list_shortcode*|`radio_station_clock` | ` $clock` | `$atts`|
| |`radio_station_'.$type.'_archive_post_args` | ` $args` | |
| |`radio_station_'.$type.'_archive_posts` | ` $archive_posts` | |
| |`radio_station_time_separator` | ` $time_separator` | `$post_type.'-archive'`|
| |`radio_station_time_format_start` | ` $start_data_format` | `$post_type.'-archive'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `$post_type.'-archive'`, `$atts`|
| |`radio_station_archive_shortcode_no_records` | ` $message` | `$post_type`, `$atts`|
| |`radio_station_archive_'.$type.'_list_excerpt_length` | ` false` | |
| |`radio_station_archive_'.$type.'_list_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_archive_shortcode_info_order` | ` $infokeys` | `$post_type`, `$atts`|
|*radio_station_show_archive_list*|`radio_station_show_times_separator` | ` $separator` | `'override'`|
| |`radio_station_'.$type.'_archive_content` | ` $post_content` | `$post_id`|
| |`radio_station_'.$type.'_archive_excerpt` | ` $excerpt` | `$post_id`|
| |`radio_station_archive_shortcode_info_custom` | ` ''` | `$post_id`, `$post_type`, `$atts`|
| |`radio_station_archive_shortcode_info` | ` $info` | `$post_id`, `$post_type`, `$atts`|
| |`radio_station_'.$type.'_archive_list` | ` $list` | `$atts`, `$post_type`|
| |`radio_station_genre_archive_post_args` | ` $args` | |
| |`radio_station_genre_archive_posts` | ` $posts` | |
| |`radio_station_genre_image` | ` false` | `$genre`|
| |`radio_station_genre_archive_excerpt_length` | ` false` | |
| |`radio_station_genre_archive_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_genre_archive_excerpt` | ` $excerpt` | `$post->ID`|
|*radio_station_language_archive_list*|`radio_station_genre_archive_list` | ` $list` | `$atts`|
|*radio_station_archive_pagination*|`radio_station_language_archive_post_args` | ` $args` | |
| |`radio_station_language_archive_posts` | ` $posts` | |
| |`radio_station_language_archive_excerpt_length` | ` false` | |
| |`radio_station_language_archive_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_genre_archive_excerpt` | ` $excerpt` | `$post->ID`|
| |`radio_station_language_archive_list` | ` $list` | `$atts`|
|*radio_station_show_posts_archive*|`radio_station_get_show_hosts` | ` false` | `$show_id`, `$args`|
| |`radio_station_get_show_producers` | ` false` | `$show_id`, `$args`|
| |`radio_station_get_show_episodes` | ` false` | `$show_id`, `$args`|
| |`radio_station_show_'.$type.'_list_excerpt_length` | ` false` | |
| |`radio_station_show_'.$type.'_list_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_show_'.$type.'_content` | ` $bio_content` | `$user_id`|
| |`radio_station_show_'.$type.'_excerpt` | ` $excerpt` | `$user_id`|
| |`radio_station_show_list_archive_avatar` | ` $thumbnail` | `$post['ID']`, `$type`|
| |`radio_station_show_'.$type.'_content` | ` $post_content` | `$post_id`|
| |`radio_station_show_'.$type.'_excerpt` | ` $excerpt` | `$post_id`|
| |`radio_station_show_'.$type.'_list` | ` $list` | `$atts`|
| |`radio_station_current_show_dynamic` | ` false` | `$atts`|
| |`radio_station_widgets_ajax_override` | ` $ajax` | `'current-show'`, `$widget`|
| |`radio_station_current_show_widget_excerpt_length` | ` false` | |
| |`radio_station_current_show_widget_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_current_show_shortcode_excerpt_length` | ` false` | |
| |`radio_station_current_show_shortcode_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_time_separator` | ` $time_separator` | `'current-show'`|
| |`radio_station_time_format_start` | ` $start_data_format` | `'current-show'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'current-show'`, `$atts`|
| |`radio_station_current_show_link` | ` $show_link` | `$show_id`, `$atts`|
| |`radio_station_show_times_separator` | ` $separator` | `'current-show'`|
| |`radio_station_current_show_title_display` | ` $title` | `$show_id`, `$atts`|
| |`radio_station_current_show_avatar_size` | ` $atts['avatar_size']` | `$show_id`|
| |`radio_station_current_show_avatar` | ` $show_avatar` | `$show_id`, `$atts`|
| |`radio_station_current_show_avatar_display` | ` $avatar` | `$show_id`, `$atts`|
| |`radio_station_dj_link` | ` $host_link` | `$host`|
| |`radio_station_current_show_hosts_display` | ` $hosts` | `$show_id`, `$atts`|
| |`radio_station_current_show_encore_display` | ` $encore` | `$show_id`, `$atts`|
| |`radio_station_current_show_playlist_display` | ` $playlist` | `$show_id`, `$atts`|
| |`radio_station_current_show_widget_excerpt` | ` $excerpt` | `$show_id`, `$atts`|
| |`radio_station_current_show_shortcode_excerpt` | ` $excerpt` | `$show_id`, `$atts`|
| |`radio_station_current_show_description_display` | ` $description` | `$show_id`, `$atts`|
| |`radio_station_current_show_shifts_display` | ` $shift_display` | `$show_id`, `$atts`|
| |`radio_station_current_show_custom_display` | ` ''` | `$show_id`, `$atts`|
| |`radio_station_current_show_section_order` | ` $order` | `$atts`|
| |`radio_station_no_current_show_text` | ` $no_current_show` | `$atts`|
| |`radio_station_countdown_dynamic` | ` false` | `'current-show'`, `$atts`, `$current_shift_end`|
|*radio_station_upcoming_shows_shortcode*|`radio_station_current_show_load_script` | ` $js` | `$atts`|
| |`radio_station_upcomins_shows_dynamic` | ` false` | `$atts`|
| |`radio_station_widgets_ajax_override` | ` $ajax` | `'upcoming-shows'`, `$widget`|
| |`radio_station_upcoming_shows_section_order` | ` $order` | `$atts`|
| |`radio_station_time_separator` | ` $time_separator` | `'upcoming-shows'`|
| |`radio_station_time_format_start` | ` $start_data_format` | `'upcoming-shows'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'upcoming-shows'`, `$atts`|
| |`radio_station_upcoming_show_link` | ` $show_link` | `$show_id`, `$atts`|
| |`radio_station_show_times_separator` | ` $separator` | `'upcoming-shows'`|
| |`radio_station_upcoming_show_title_display` | ` $title` | `$show_id`, `$atts`|
| |`radio_station_upcoming_show_avatar_size` | ` $atts['avatar_size']` | `$show_id`|
| |`radio_station_upcoming_show_avatar` | ` $show_avatar` | `$show_id`, `$atts`|
| |`radio_station_upcoming_show_avatar_display` | ` $avatar` | `$show_id`, `$atts`|
| |`radio_station_dj_link` | ` $host_link` | `$host`|
| |`radio_station_upcoming_show_hosts_display` | ` $hosts` | `$show_id`, `$atts`|
| |`radio_station_upcoming_show_encore_display` | ` $encore` | `$show_id`, `$atts`|
| |`radio_station_upcoming_show_shifts_display` | ` $shift_display` | `$show_id`, `$atts`|
| |`radio_station_upcoming_shows_custom_display` | ` ''` | `$show_id`, `$atts`|
|**includes/support-functions.php**||||
|*radio_station_get_shows*|`radio_station_get_shows` | ` $shows` | `$defaults`|
|*radio_station_get_overrides*|`radio_station_show_day_shifts` | ` $day_shifts` | |
|*radio_station_get_show_data*|`radio_station_get_overrides` | ` $override_list` | `$start_date`, `$end_date`|
| |`radio_station_cached_data` | ` false` | `$datatype`, `$show_id`|
|*radio_station_get_show_data_meta*|`radio_station_show_data_excerpt_length` | ` 55` | |
| |`radio_station_show_data_excerpt_more` | ` ''` | |
| |`radio_station_show_'.$datatype` | ` $results` | `$show_id`, `$args`|
|*radio_station_get_show_description*|`radio_station_show_data_meta` | ` $show_data` | `$show_id`|
| |`radio_station_show_data_excerpt_length` | ` 55` | |
| |`radio_station_show_data_excerpt_more` | ` ''` | |
| |`radio_station_show_data_description` | ` $description` | `$show_id`|
| |`radio_station_show_data_excerpt` | ` $excerpt` | `$show_id`|
| |`radio_station_override_data` | ` $override_data` | `$override_id`|
| |`radio_station_linked_overrides` | ` $override_ids` | `$post_id`|
| |`radio_station_linked_override_times` | ` $overrides` | `$post_id`|
| |`radio_station_previous_show` | ` $prev_shift` | `$time`|
| |`radio_station_previous_show` | ` $prev_shift` | `$time`|
|*radio_station_get_current_show*|`radio_station_current_schedule` | ` $show_shifts` | `$time`|
|*radio_station_get_previous_show*|`radio_station_previous_show` | ` $prev_shift` | `$time`|
| |`radio_station_current_show` | ` $current_show` | `$time`|
|*radio_station_get_current_playlist*|`radio_station_next_show` | ` $next_show` | `$time`|
| |`radio_station_next_shows` | ` $next_shows` | `$limit`, `$show_shifts`|
| |`radio_station_next_shows` | ` $next_shows` | `$limit`, `$show_shifts`|
| |`radio_station_get_genres` | ` $genres` | `$args`|
|*radio_station_get_language_shows*|`radio_station_show_genres_query_args` | ` $args` | `$genre`|
| |`radio_station_show_languages_query_args` | ` $args` | `$language`|
|*radio_station_update_show_avatar*|`radio_station_show_avatar_post_types` | ` $post_types` | |
|*radio_station_get_show_avatar_url*|`radio_station_show_avatar_id` | ` $avatar_id` | `$show_id`|
| |`radio_station_show_avatar_size` | ` $size` | |
| |`radio_station_show_avatar_url` | ` $avatar_url` | `$show_id`, `$size`|
| |`radio_station_show_avatar_size` | ` $size` | |
| |`radio_station_show_avatar_output` | ` $avatar` | `$show_id`, `$size`|
|*radio_station_get_stream_url*|`radio_station_stream_url` | ` $streaming_url` | |
|*radio_station_get_stream_formats*|`radio_station_fallback_url` | ` $fallback_url` | |
| |`radio_station_stream_formats` | ` $formats` | |
|*radio_station_get_station_url*|`radio_station_station_url` | ` $station_url` | |
|*radio_station_get_schedule_url*|`radio_station_station_image_url` | ` $station_image` | |
| |`radio_station_schedule_url` | ` $schedule_url` | |
| |`radio_station_api_url` | ` $api_url` | |
|*radio_station_get_route_url*|`radio_station_route_slug_base` | ` 'radio'` | |
| |`radio_station_route_slug_'.$route` | ` $route` | |
|*radio_station_get_feed_url*|`radio_station_feed_slug_'.$feedname` | ` $feedname` | |
| |`radio_station_host_url` | ` $host_url` | `$host_id`|
|*radio_station_get_upgrade_url*|`radio_station_producer_url` | ` $producer_url` | `$producer_id`|
|*radio_station_patreon_button_styles*|`radio_station_patreon_button` | ` $button` | `$page`|
|*radio_station_get_weekday*|`radio_station_get_timezone_options` | ` $options` | `$include_wp_timezone`|
|*radio_station_get_schedule_weekdays*|`radio_station_schedule_weekday_start` | ` $weekstart` | |
|**includes/class-current-show-widget.php**||||
|*update*|`radio_station_current_show_widget_fields` | ` $fields` | `$this`, `$instance`|
|*widget*|`radio_station_current_show_widget_update` | ` $instance` | `$new_instance`, `$old_instance`|
| |`radio_station_current_show_widget_atts` | ` $atts` | `$instance`|
|**includes/class-upcoming-shows-widget.php**||||
|*update*|`radio_station_upcoming_shows_widget_fields` | ` $fields` | `$this`, `$instance`|
|*widget*|`radio_station_upcoming_shows_widget_update` | ` $instance` | `$new_instance`, `$old_instance`|
| |`radio_station_upcoming_shows_widget_atts` | ` $atts` | `$instance`|
|**includes/class-current-playlist-widget.php**||||
|*update*|`radio_station_playlist_widget_fields` | ` $fields` | `$this`, `$instance`|
|*widget*|`radio_station_playlist_widget_update` | ` $instance` | `$new_instance`, `$old_instance`|
| |`radio_station_current_playlist_widget_atts` | ` $atts` | `$instance`|
| |`radio_station_current_playlist_widget_override` | ` $output` | `$args`, `$atts`|
|**includes/class-radio-clock-widget.php**||||
| |`radio_station_clock_widget_atts` | ` $atts` | `$instance`|
|**includes/class-radio-player-widget.php**||||
|*form*|`radio_station_player_theme_options` | ` $options` | |
| |`radio_station_player_button_options` | ` $options` | |
|*update*|`radio_station_player_widget_fields` | ` $fields` | `$this`, `$instance`|
|*widget*|`radio_station_player_widget_update` | ` $instance` | `$new_instance`, `$old_instance`|
|**templates/master-schedule-table.php**||||
| |`radio_station_schedule_start_time` | ` $start_time` | `'table'`, `$atts`|
| |`radio_station_show_time_separator` | ` $shifts_separator` | `'schedule-table'`|
| |`radio_station_time_separator` | ` $time_separator` | `'schedule-table'`|
| |`radio_station_time_format_start` | ` $start_data_format` | `'schedule-table'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'schedule-table'`, `$atts`|
| |`radio_station_schedule_start_day` | ` false` | `'table'`|
| |`radio_station_schedule_show_avatar_size` | ` 'thumbnail'` | `'table'`|
| |`radio_station_schedule_table_excerpt_length` | ` false` | |
| |`radio_station_schedule_table_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_schedule_arrows` | ` $arrows` | `'table'`|
| |`radio_station_schedule_table_info_order` | ` $infokeys` | |
| |`radio_station_schedule_loader_control` | ` ''` | `'table'`, `'left'`|
| |`radio_station_schedule_loader_control` | ` ''` | `'table'`, `'right'`|
| |`radio_station_schedule_show_link` | ` $show_link` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_avatar` | ` $show_avatar` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_avatar_display` | ` $avatar` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_title_display` | ` $title` | `$show_id`, `'table'`|
| |`radio_station_show_edit_link` | ` ''` | `$show_id`, `$shift['id']`, `'table'`|
| |`radio_station_schedule_show_hosts` | ` $show_hosts` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_hosts_display` | ` $hosts` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_time` | ` $show_time` | `$show_id`, `'table'`, `$shift`, `$tcount`|
| |`radio_station_schedule_show_time_display` | ` true` | `$show_id`, `'table'`, `$shift`|
| |`radio_station_schedule_show_encore` | ` $show_encore` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_encore_display` | ` $encore` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_file` | ` $show_file` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_file_anchor` | ` $anchor` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_file_display` | ` $file` | `$show_file`, `$show_id`, `'table'`|
| |`radio_station_schedule_show_excerpt` | ` $show_excerpt` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_excerpt_display` | ` $excerpy` | `$show_id`, `'table'`|
| |`radio_station_schedule_show_custom_display` | ` ''` | `$show_id`, `'table'`|
| |`radio_station_schedule_add_link` | ` ''` | `$times`, `'table'`|
|**templates/master-schedule-tabs.php**||||
| |`radio_station_schedule_start_time` | ` $start_time` | `'tabs'`|
| |`radio_station_show_times_separator` | ` $shifts_separator` | `'schedule-tabs'`|
| |`radio_station_time_separator` | ` $time_separator` | `'schedule-tabs'`|
| |`radio_station_time_format_start` | ` $start_data_format` | `'schedule-tabs'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'schedule-tabs'`, `$atts`|
| |`radio_station_schedule_start_day` | ` false` | `'tabs'`|
| |`radio_station_schedule_show_avatar_size` | ` 'thumbnail'` | `'tabs'`|
| |`radio_station_schedule_tabs_excerpt_length` | ` false` | |
| |`radio_station_schedule_tabs_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_schedule_arrows` | ` $arrows` | `'tabs'`|
| |`radio_station_schedule_tabs_info_order` | ` $infokeys` | |
| |`radio_station_schedule_loader_control` | ` ''` | `'tabs'`, `'left'`|
| |`radio_station_schedule_tabs_avatar_position_start` | ` $avatar_position` | |
| |`radio_station_schedule_show_link` | ` $show_link` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_avatar` | ` $show_avatar` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_avatar_display` | ` $avatar` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_title_display` | ` $title` | `$show_id`, `'tabs'`|
| |`radio_station_show_edit_link` | ` ''` | `$show_id`, `$shift['id']`, `'tabs'`|
| |`radio_station_schedule_show_hosts` | ` $show_hosts` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_hosts_display` | ` $hosts` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_time` | ` $show_time` | `$show_id`, `'tabs'`, `$shift`, `$tcount`|
| |`radio_station_schedule_show_times_display` | ` true` | `$show_id`, `'tabs'`, `$shift`|
| |`radio_station_schedule_show_encore` | ` $show_encore` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_encore_display` | ` $encore` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_file` | ` $show_file` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_file_anchor` | ` $anchor` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_file_display` | ` $file` | `$show_file`, `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_genres` | ` $genres` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_custom_display` | ` ''` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_excerpt` | ` $show_excerpt` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_show_excerpt_display` | ` $excerpt` | `$show_id`, `'tabs'`|
| |`radio_station_schedule_loader_control` | ` ''` | `'tabs'`, `'right'`|
|**templates/master-schedule-legacy.php**||||
| |`radio_station_schedule_show_avatar_size` | ` 'thumbnail'` | `'legacy'`|
| |`radio_station_schedule_show_avatar` | ` $show_avatar` | `$show['id']`, `'legacy'`|
| |`radio_station_schedule_show_link` | ` $show_link` | `$show['id']`, `'legacy'`|
| |`radio_station_schedule_show_time` | ` $times` | `$show['id']`, `'legacy'`, `false`, `false`|
| |`radio_station_schedule_show_encore` | ` $encore` | `$show['id']`, `'legacy'`|
| |`radio_station_schedule_show_file` | ` $show_file` | `$show['id']`, `'legacy'`|
|**templates/master-schedule-list.php**||||
| |`radio_station_show_times_separator` | ` $shifts_separator` | `'schedule-list'`|
| |`radio_station_time_separator` | ` $time_separator` | `'schedule-list'`|
| |`radio_station_time_format_start` | ` $start_data_format` | `'schedule-list'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'schedule-list'`, `$atts`|
| |`radio_station_schedule_start_day` | ` false` | `'list'`|
| |`radio_station_schedule_show_avatar_size` | ` 'thumbnail'` | `'list'`|
| |`radio_station_schedule_list_excerpt_length` | ` false` | |
| |`radio_station_schedule_list_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_schedule_list_info_order` | ` $infokeys` | |
| |`radio_station_schedule_show_link` | ` $show_link` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_avatar` | ` $show_avatar` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_avatar_display` | ` $avatar` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_title` | ` $title` | `$show_id`, `'list'`|
| |`radio_station_show_edit_link` | ` ''` | `$show_id`, `$shift['id']`, `'list'`|
| |`radio_station_schedule_show_hosts` | ` $show_hosts` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_hosts_display` | ` $hosts` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_time` | ` $show_time` | `$show_id`, `'list'`, `$shift`, `$tcount`|
| |`radio_station_schedule_show_time_display` | ` true` | `$show_id`, `'list'`, `$shift`|
| |`radio_station_schedule_show_encore` | ` $show_encore` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_encore_display` | ` $encore` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_file` | ` $show_file` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_file_anchor` | ` $anchor` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_file_display` | ` $file` | `$show_file`, `$show_id`, `'list'`|
| |`radio_station_schedule_show_genres_display` | ` $genres` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_excerpt` | ` $show_excerpt` | `$show_id`, `'list'`|
| |`radio_station_schedule_show_custom_display` | ` ''` | `$show_id`, `'list'`|
|**templates/single-playlist-content.php**||||
| |`radio_station_link_playlist_to_show_before` | ` $before` | `$post`, `$show`|
| |`radio_station_link_playlist_to_show_after` | ` $after` | `$post`, `$show`|
|**templates/single-show-content.php**||||
| |`radio_station_show_title` | ` $show_title` | `$post_id`|
| |`radio_station_show_header` | ` $header_id` | `$post_id`|
| |`radio_station_show_avatar` | ` $avatar_id` | `$post_id`|
| |`radio_station_show_thumbnail` | ` $thumbnail_id` | `$post_id`|
| |`radio_station_show_genres` | ` $genres` | `$post_id`|
| |`radio_station_show_languages` | ` $languages` | `$post_id`|
| |`radio_station_show_hosts` | ` $hosts` | `$post_id`|
| |`radio_station_show_producers` | ` $producers` | `$post_id`|
| |`radio_station_show_active` | ` $active` | `$post_id`|
| |`radio_station_show_shifts` | ` $shifts` | `$post_id`|
| |`radio_station_show_file` | ` $show_file` | `$post_id`|
| |`radio_station_show_download` | ` $show_download` | `$post_id`|
| |`radio_station_show_link` | ` $show_link` | `$post_id`|
| |`radio_station_show_email` | ` $show_email` | `$post_id`|
| |`radio_station_show_phone` | ` $show_phone` | `$post_id`|
| |`radio_station_show_patreon` | ` $show_patreon` | `$post_id`|
| |`radio_station_show_rss` | ` $show_rss` | `$post_id`|
| |`radio_station_show_social_icons` | ` false` | `$post_id`|
| |`radio_station_time_format_start` | ` $start_data_format` | `'show-template'`, `$post_id`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'show-template'`, `$post_id`|
| |`radio_station_show_website_title` | ` $title` | `$post_id`|
| |`radio_station_show_home_icon` | ` $icon` | `$post_id`|
| |`radio_station_show_phone_title` | ` $title` | `$post_id`|
| |`radio_station_show_phone_icon` | ` $icon` | `$post_id`|
| |`radio_station_show_email_title` | ` $title` | `$post_id`|
| |`radio_station_show_email_icon` | ` $icon` | `$post_id`|
| |`radio_station_show_rss_title` | ` $title` | `$post_id`|
| |`radio_station_show_rss_icon` | ` $icon` | `$post_id`|
| |`radio_station_show_page_icons` | ` $show_icons` | `$post_id`|
| |`radio_station_show_page_posts_limit` | ` false` | `$post_id`|
| |`radio_station_show_page_playlist_limit` | ` false` | `$post_id`|
| |`radio_station_show_jump_links` | ` 'yes'` | `$post_id`|
| |`radio_station_show_avatar_size` | ` 'medium'` | `$post_id`, `'show-page'`|
| |`radio_station_show_social_icons_display` | ` ''` | |
| |`radio_station_show_patreon_title` | ` $title` | `$post_id`|
| |`radio_station_show_patreon_button` | ` $patreon_button` | `$post_id`|
| |`radio_station_show_player_label` | ` ''` | `$post_id`|
| |`radio_station_show_download_title` | ` $title` | `$post_id`|
| |`radio_station_show_images_blocks` | ` $image_blocks` | `$post_id`|
| |`radio_station_show_image_block_order` | ` $image_block_order` | `$post_id`|
| |`radio_station_show_info_label` | ` $label` | `$post_id`|
| |`radio_station_show_hosts_label` | ` $label` | `$post_id`|
| |`radio_station_show_producers_label` | ` $label` | `$post_id`|
| |`radio_station_show_genres_label` | ` $label` | `$post_id`|
| |`radio_station_show_languages_label` | ` $label` | `$post_id`|
| |`radio_station_show_phone_label` | ` $label` | `$post_id`|
| |`radio_station_show_meta_blocks` | ` $meta_blocks` | `$post_id`|
| |`radio_station_show_meta_block_order` | ` $meta_block_order` | `$post_id`|
| |`radio_station_show_times_label` | ` $label` | `$post_id`|
| |`radio_station_show_no_shifts_label` | ` $label` | `$post_id`|
| |`radio_station_show_timezone_label` | ` $label` | `$post_id`|
| |`radio_station_show_times_separator` | ` $separator` | `'show-content'`|
| |`radio_station_show_encore_label` | ` $label` | `$post_id`|
| |`radio_station_override_date_format` | ` 'j F'` | |
| |`radio_station_override_show_past_dates` | ` false` | |
| |`radio_station_show_times_separator` | ` $separator` | `'override-content'`|
| |`radio_station_show_schedule_link_title` | ` $title` | `$post_id`|
| |`radio_station_show_schedule_link_anchor` | ` $label` | `$post_id`|
| |`radio_station_show_page_blocks` | ` $blocks` | `$post_id`|
| |`radio_station_show_more_label` | ` $label` | `$post_id`|
| |`radio_station_show_less_label` | ` $label` | `$post_id`|
| |`radio_station_show_description_label` | ` $label` | `$post_id`|
| |`radio_station_show_description_anchor` | ` $anchor` | `$post_id`|
| |`radio_station_show_posts_label` | ` $label` | `$post_id`|
| |`radio_station_show_posts_anchor` | ` $posts_label` | `$post_id`|
| |`radio_station_show_page_posts_shortcode` | ` $shortcode` | `$post_id`|
| |`radio_station_show_playlists_label` | ` $label` | `$post_id`|
| |`radio_station_show_playlists_anchor` | ` $playlist_label` | `$post_id`|
| |`radio_station_show_page_playlists_shortcode` | ` $shortcode` | `$post_id`|
| |`radio_station_show_page_sections` | ` $sections` | `$post_id`|
| |`radio_station_show_header_size` | ` 'full'` | `$post_id`|
| |`radio_station_show_page_header_image` | ` $header_image` | `$post_id`|
| |`radio_station_show_page_block_order` | ` $block_order` | `$post_id`|
| |`radio_station_show_latest_posts_label` | ` $label` | `$post_id`|
| |`radio_station_show_page_latest_shortcode` | ` $shortcode` | `$post_id`|
| |`radio_station_show_page_section_order` | ` $section_order` | `$post_id`|
|**player/radio-player.php**||||
|*radio_station_player_output*|`radio_station_player_output_args` | ` $args` | `$instance`|
| |`radio_station_player_station_image_tag` | ` $image` | `$args['image']`, `$args`, `$instance`|
|*radio_station_player_shortcode*|`radio_station_player_section_order` | ` $section_order` | `$args`|
| |`radio_station_player_control_order` | ` $control_order` | `$args`, `$instance`|
| |`radio_station_player_station_text_alt` | ` $station_text_alt` | `$args`, `$instance`|
| |`radio_station_player_show_text_alt` | ` $show_text_alt` | `$args`, `$instance`|
| |`radio_station_player_html` | ` $player` | `$args`, `$instance`|
| |`radio_station_player_default_title_display` | ` $title` | |
| |`radio_station_player_default_image_display` | ` $image` | |
| |`radio_station_player_default_script` | ` $script` | |
| |`radio_station_player_default_layout` | ` $layout` | |
| |`radio_station_player_default_volume` | ` $volume` | |
| |`radio_station_player_default_theme` | ` $theme` | |
| |`radio_station_player_default_buttons` | ` $buttons` | |
| |`radio_station_player_shortcode_attributes` | ` $atts` | |
| |`radio_station_player_default_title` | ` ''` | |
| |`radio_station_player_default_image` | ` ''` | |
|*radio_station_player_ajax*|`radio_station_player_output` | ` $override` | `$atts`|
| |`radio_station_player_atts` | ` $atts` | |
| |`radio_station_player_mediaelements_interface` | ` $html` | `$atts`, `$post_id`|
|*radio_station_player_enqueue_script*|`radio_station_player_pageload_script` | ` ''` | |
| |`radio_station_player_scripts` | ` $js` | |
| |`radio_station_player_fallbacks` | ` $fallbacks` | |
|*radio_station_player_enqueue_mediaelements*|`radio_station_player_mediaelement_settings` | ` $player_settings` | |
|*radio_station_player_script*|`radio_station_player_save_interval` | ` $save_interval` | |
| |`radio_station_player_jplayer_swf_path` | ` ''` | |
| |`radio_station_player_title` | ` $player_title` | |
| |`radio_station_player_image` | ` $player_image` | |
| |`radio_station_player_volume` | ` $player_volume ) )` | |
| |`radio_station_player_single` | ` $player_single` | |
| |`radio_station_player_fallbacks` | ` $fallbacks` | |
| |`radio_station_player_debug` | ` $debug` | |
|*radio_station_player_iframe*|`radio_station_player_data` | ` false` | `$station`|
|*radio_station_player_script_howler*|`radio_station_player_script_amplitude` | ` $js` | |
| |`radio_station_player_script_howler` | ` $js` | |
| |`radio_station_player_script_jplayer` | ` $js` | |


## [Pro] Pro Filter List

Below is a list of filters that are available within [Radio Station Pro](https://radiostation.pro).

| File / *Function* | Filter | Value | Extra Args |
| - | - |
|**radio-station-pro.php**||||
| |`radio_station_editor_relogin_script` | ` $js` | `$type`|
|*radio_station_pro_thickbox_loading_image*|`radio_station_thickbox_loading_icon_url` | ` $thickbox_loading_url` | |
| |`radio_station_thickbox_styles` | ` $css` | |
|*radio_station_pro_set_roles*|`radio_station_user_shows` | ` $shows` | `$type`, `$user_id`|
|**includes/rsp-data-feeds.php**||||
|*radio_station_pro_register_rest_routes*|`radio_station_route_slug_base` | ` 'radio'` | |
| |`radio_station_route_slug_episodes` | ` false` | |
| |`radio_station_route_slug_hosts` | ` 'hosts'` | |
| |`radio_station_route_slug_producers` | ` 'producers'` | |
|*radio_station_pro_route_episodes*|`radio_station_route_episodes` | ` $episode_list` | |
| |`radio_station_feed_hosts` | ` $host_list` | |
|*radio_station_pro_route_producers*|`radio_station_route_producers` | ` $producer_list` | |
|**includes/rsp-episodes.php**||||
|*radio_station_pro_register_taxonomies*|`radio_station_topic_taxonomy_args` | ` $args` | |
|*radio_station_pro_set_data_slug*|`radio_station_guest_taxonomy_args` | ` $args` | |
| |`radio_station_episode_url` | ` $episode_url` | `$episode_id`|
|*radio_station_pro_get_show_episodes*|`radio_station_episode_avatar_output` | ` $avatar` | `$episode_id`|
| |`radio_station_episode_avatar_id` | ` $avatar_id` | `$episode_id`|
|*radio_station_pro_get_show_page_episodes*|`radio_station_show_page_episodes_limit` | ` false` | `$post_id`|
|**includes/rsp-episodes-admin.php**||||
|*radio_station_pro_add_episodes_submenu*|`radio_station_metabox_position` | ` 'rstop'` | `'profiles'`|
|*radio_episode_type*|`radio_station_episode_edit_styles` | ` $css` | |
| |`radio_station_update_segments` | ` false` | `$post_id`|
|**includes/rsp-import-export.php**||||
|*radio_station_create_show_image_archive*|`radio_station_valid_with_paragraph_tags` | ` true` | |
|**includes/rsp-metadata.php**||||
|*radio_station_pro_get_stream_metadata*|`radio_station_stream_metadata_types` | ` $data_types` | |
|*radio_station_pro_broadcast_data*|`radio_station_stream_metadata` | ` $np` | `$stream`|
| |`radio_station_stream_metadata_url` | ` $metadata` | `$broadcast`|
| |`radio_station_metadata_cache_interval` | ` 5` | |
| |`radio_station_current_song` | ` $currentsong` | |
|*radio_station_pro_icy_stream_title*|`radio_station_icy_metadata_method` | ` $method` | |
|*radio_station_pro_icy_song_info*|`radio_station_pro_metadata` | ` $metadata` | `$stream`, `'shoutcast1'`|
|*radio_station_pro_shoutcast2_current_song*|`radio_station_pro_metadata` | ` $metadata` | `$stream`, `'shoutcast2'`|
| |`radio_station_stream_mount_index` | ` $mount` | `$stream`|
|**includes/rsp-player.php**||||
|*radio_station_pro_player_scripts*|`radio_station_player_bar_metadata_cycle` | ` $metadata_cycle` | |
| |`radio_station_pro_scripts` | ` $js` | |
| |`radio_station_player_bar_atts` | ` $atts` | |
|**includes/rsp-post-types.php**||||
| |`radio_station_pro_post_type_episodes` | ` $episodes` | |
|**includes/rsp-profiles.php**||||
|*radio_station_pro_get_profile_posts*|`radio_station_'.$profile_type.'_'.$data_type` | ` $results` | `$author_id`, `$args`|
| |`radio_station_show_avatar_output` | ` $avatar` | `$profile_id`, `$type`|
| |`radio_station_profile_avatar_id` | ` $avatar_id` | `$profile_id`, `$type`|
| |`radio_station_'.$type.'_hosts_label` | ` $label` | `$post_id`|
| |`radio_station_'.$type.'_hosts_anchor` | ` $anchor` | `$post_id`|
| |`radio_station_'.$type.'_page_hosts_shortcode` | ` $shortcode` | `$post_id`|
| |`radio_station_'.$type.'_producers_label` | ` $label` | `$post_id`|
| |`radio_station_'.$type.'_producers_anchor` | ` $anchor` | `$post_id`|
| |`radio_station_'.$type.'_page_producers_shortcode` | ` $shortcode` | `$post_id`|
| |`radio_station_'.$type.'_team_label` | ` $label` | `$post_id`|
| |`radio_station_'.$type.'_team_anchor` | ` $anchor` | `$post_id`|
|**includes/rsp-profiles-admin.php**||||
|*radio_station_pro_add_profile_metabox*|`radio_station_metabox_position` | ` 'rstop'` | `'profiles'`|
|*radio_station_pro_add_image_metaboxes*|`radio_station_profile_edit_styles` | ` $css` | |
|**includes/rsp-schedule-editor.php**||||
|*radio_station_pro_schedule_editor_menu*|`radio_station_pro_view_images` | ` false` | |
| |`radio_station_pro_schedule_editor_atts` | ` $atts` | |
|**includes/rsp-schedule-views.php**||||
|*radio_station_pro_schedule_loader_control*|`radio_station_schedule_arrows` | ` $arrows` | `$view`|
| |`master_schedule_grid_view` | ` $html` | `$atts`|
| |`master_schedule_calendar_view` | ` $html` | `$atts`|
|*radio_shift_grid*|`radio_station_pro_master_schedule_grid_js` | ` $js` | |
|*radio_calendar_show_highlight*|`radio_station_pro_master_schedule_calendar_js` | ` $js` | |
|*radio_slide_check*|`radio_station_show_slider_script` | ` $js` | |
| |`radio_station_pro_view_order` | ` $view_order` | `$atts`|
|*radio_switch_view*|`radio_station_pro_view_images` | ` false` | `$atts`|
|**includes/rsp-shortcodes.php**||||
|*radio_station_pro_archive_list_shortcode*|`radio_station_'.$type.'_archive_post_args` | ` $args` | |
| |`radio_station_'.$type.'_archive_posts` | ` $archive_posts` | |
| |`radio_station_time_separator` | ` $time_separator` | `$post_type.'-archive'`|
| |`radio_station_time_format_start` | ` $start_data_format` | `$post_type.'-archive'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `$post_type.'-archive'`, `$atts`|
| |`radio_station_archive_shortcode_no_records` | ` $message` | `$post_type`, `$atts`|
| |`radio_station_archive_'.$type.'_list_excerpt_length` | ` false` | |
| |`radio_station_archive_'.$type.'_list_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_archive_shortcode_info_order` | ` $infokeys` | `$post_type`, `$atts`|
| |`radio_station_'.$type.'_archive_avatar_size` | ` 'thumbnail'` | `$post_id`, `$type.'-archive'`|
|*radio_station_pro_profile_list_shortcode*|`radio_station_archive_shortcode_meta` | ` ''` | `$post_id`, `$post_type`, `$atts`|
| |`radio_station_'.$type.'_archive_content` | ` $post_content` | `$post_id`|
| |`radio_station_'.$type.'_archive_excerpt` | ` $excerpt` | `$post_id`|
| |`radio_station_archive_shortcode_info_custom` | ` ''` | `$post_id`, `$post_type`, `$atts`|
| |`radio_station_archive_shortcode_info` | ` $info` | `$post_id`, `$post_type`, `$atts`|
| |`radio_station_'.$type.'_archive_list` | ` $list` | `$atts`|
| |`radio_station_'.$profile_type.'_'.$type.'_list_excerpt_length` | ` false` | |
| |`radio_station_'.$profile_type.'_'.$type.'_list_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_'.$profile_type.'_'.$type.'_content` | ` $post_content` | `$post_id`|
| |`radio_station_'.$profile_type.'_'.$type.'_excerpt` | ` $excerpt` | `$post_id`|
|**includes/rsp-social.php**||||
|*radio_station_pro_get_social_icon*|`radio_station_social_icons_services` | ` $services` | |
| |`radio_station_social_icon_url` | ` $icon_url` | `$service`|
|*radio_station_pro_social_icons_inputs*|`radio_station_social_icon_dir` | ` get_stylesheet_directory() . '/images/'` | |
| |`radio_station_social_icon_path` | ` get_stylesheet_directory_uri() . '/images/'` | |
| |`radio_station_social_icon_output` | ` $html` | `$service`|
|*radio_social_first_last*|`radio_station_pro_social_icon_script` | ` $js` | |
|*radio_station_pro_social_icons_save*|`radio_station_social_icon_edit_styles` | ` $css` | |
|**includes/rsp-timezones.php**||||
|*radio_station_pro_timezone_resources*|`radio_station_timezone_switcher_styles` | ` $css` | |
|**templates/master-schedule-grid.php**||||
| |`radio_station_schedule_start_time` | ` $start_time` | `'grid'`|
| |`radio_station_schedule_show_time_separator` | ` $shifts_separator` | `'schedule-grid'`|
| |`radio_station_time_separator` | ` $time_separator` | `'schedule-grid'`|
| |`radio_station_time_format_start` | ` $start_data_format` | `'schedule-grid'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'schedule-grid'`, `$atts`|
| |`radio_station_schedule_start_day` | ` false` | `'grid'`|
| |`radio_station_schedule_show_avatar_size` | ` $avatar_size` | `'grid'`|
| |`radio_station_schedule_arrows` | ` $arrows` | `'grid'`|
| |`radio_station_schedule_grid_info_order` | ` $infokeys` | |
| |`radio_station_schedule_show_link` | ` $show_link` | `$show_id`, `'grid'`|
| |`radio_station_schedule_show_avatar` | ` $show_avatar` | `$show_id`, `'grid'`|
| |`radio_station_schedule_show_avatar_display` | ` $avatar` | `$show_id`, `'grid'`|
| |`radio_station_schedule_show_title_display` | ` $title` | `$show_id`, `'grid'`|
| |`radio_station_show_edit_link` | ` ''` | `$show_id`, `$shift['id']`, `'grid'`|
| |`radio_station_schedule_show_hosts` | ` $show_hosts` | `$show_id`, `'grid'`|
| |`radio_station_schedule_show_hosts_display` | ` $hosts` | `$show_id`, `'grid'`|
| |`radio_station_schedule_show_time` | ` $show_time` | `$show_id`, `'grid'`, `$shift`, `$tcount`|
| |`radio_station_schedule_show_times_display` | ` true` | `$show_id`, `'grid'`, `$shift`|
| |`radio_station_schedule_show_encore` | ` $show_encore` | `$show_id`, `'grid'`|
| |`radio_station_schedule_show_encore_display` | ` $encore` | `$show_id`, `'grid'`|
| |`radio_station_schedule_show_file` | ` $show_file` | `$show_id`, `'grid'`|
| |`radio_station_schedule_show_file_anchor` | ` $anchor` | `$show_id`, `'grid'`|
| |`radio_station_schedule_show_file_display` | ` $file` | `$show_file`, `$show_id`, `'grid'`|
| |`radio_station_schedule_show_genres` | ` $genres` | `$show_id`, `'grid'`|
| |`radio_station_schedule_show_custom_display` | ` ''` | `$show_id`, `'grid'`|
| |`radio_station_schedule_loader_control` | ` ''` | `'grid'`, `'left'`|
| |`radio_station_schedule_loader_control` | ` ''` | `'grid'`, `'right'`|
| |`radio_station_master_schedule_styles_grid` | ` $css` | |
|**templates/master-schedule-calendar.php**||||
| |`radio_station_schedule_start_time` | ` $start_time` | `'calendar'`|
| |`radio_station_schedule_show_time_separator` | ` $shifts_separator` | `'schedule-calendar'`|
| |`radio_station_time_separator` | ` $time_separator` | `'schedule-calendar'`|
| |`radio_station_time_format_start` | ` $start_data_format` | `'schedule-calendar'`, `$atts`|
| |`radio_station_time_format_end` | ` $end_data_format` | `'schedule-calendar'`, `$atts`|
| |`radio_station_schedule_start_day` | ` false` | `'calendar'`|
| |`radio_station_schedule_show_avatar_size` | ` 'thumbnail'` | `'calendar'`|
| |`radio_station_schedule_tabs_excerpt_length` | ` false` | |
| |`radio_station_schedule_tabs_excerpt_more` | ` '[&hellip;]'` | |
| |`radio_station_schedule_arrows` | ` $arrows` | `'calendar'`|
| |`radio_station_schedule_calendar_info_order` | ` $infokeys` | |
| |`radio_station_schedule_add_link` | ` ''` | `$times`, `'calendar'`|
| |`radio_station_schedule_show_link` | ` $show_link` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_avatar` | ` $show_avatar` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_avatar_display` | ` $avatar` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_title_display` | ` $title` | `$show_id`, `'calendar'`|
| |`radio_station_show_edit_link` | ` ''` | `$show_id`, `$shift['id']`, `'calendar'`|
| |`radio_station_schedule_show_hosts` | ` $show_hosts` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_hosts_display` | ` $hosts` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_time` | ` $show_time` | `$show_id`, `'calendar'`, `$shift`, `$tcount`|
| |`radio_station_schedule_show_times_display` | ` true` | `$show_id`, `'calendar'`, `$shift`|
| |`radio_station_schedule_show_encore` | ` $show_encore` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_encore_display` | ` $encore` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_file` | ` $show_file` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_file_anchor` | ` $anchor` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_file_display` | ` $file` | `$show_file`, `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_genres` | ` $genres` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_custom_display` | ` ''` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_excerpt` | ` $show_excerpt` | `$show_id`, `'calendar'`|
| |`radio_station_schedule_show_excerpt_display` | ` $excerpt` | `$show_id`, `'calendar'`|




