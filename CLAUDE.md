# Radio Station Plugin — Claude Development Guide

## Overview

**Plugin Name:** Radio Station by netmix®  
**Version:** 2.7.1  
**Authors:** Tony Zeoli, Tony Hayes (majick)  
**License:** GPLv2  
**WordPress.org:** https://wordpress.org/plugins/radio-station/  
**Docs:** https://radiostation.pro/docs/  
**GitHub Repo:** https://github.com/netmix/radio-station  

Radio Station is a free, open-source WordPress plugin that lets broadcasters manage show schedules, stream audio, and display programming on their WordPress website. It has a PRO version (Radio Station PRO) that adds advanced features.

---

## Repository Structure

```
radio-station/
├── radio-station.php          # Main plugin file — constants, includes, core hooks
├── radio-station-admin.php    # Admin-only functions, menus, notices
├── options.php                # Plugin options definitions and defaults
├── loader.php                 # Plugin loader / Freemius integration
├── reader.php                 # Data reader utilities
├── includes/
│   ├── support-functions.php  # Core utility/helper functions
│   ├── post-types.php         # Custom post types: Show, Playlist, Override
│   ├── templates.php          # Template loading and overrides
│   ├── user-roles.php         # Host and Producer user roles
│   ├── data-feeds.php         # REST API and data feeds
│   ├── schedules.php          # Schedule data and logic
│   ├── times.php              # Time/timezone handling
│   ├── master-schedule.php    # Master schedule display shortcode
│   ├── shortcodes.php         # All shortcode definitions
│   ├── blocks.php             # Gutenberg block registration
│   ├── legacy.php             # Backwards compatibility functions
│   └── import-export.php      # Import/export feature (if present)
├── player/
│   ├── radio-player.php       # Stream player core
│   └── compat.php             # Player backwards compatibility
├── scheduler/
│   └── schedule-engine.php    # Schedule conflict detection engine
├── widgets/
│   ├── class-current-show-widget.php
│   ├── class-upcoming-shows-widget.php
│   ├── class-current-playlist-widget.php
│   ├── class-radio-clock-widget.php
│   └── class-radio-player-widget.php
├── blocks/                    # Gutenberg block assets
├── css/                       # Frontend and admin stylesheets
├── js/                        # Frontend and admin JavaScript
├── images/                    # Plugin images
├── templates/                 # Frontend template files
├── assets/                    # WordPress.org SVN assets (banners, icons)
├── languages/                 # Translation .pot/.po/.mo files
├── freemius/                  # Freemius SDK (do not modify)
├── freemius-pricing/          # Freemius pricing page assets
├── vendor/                    # Composer dependencies (do not modify)
├── docs/                      # Documentation files
└── help/                      # Help tab content
```

---

## Key Constants

```php
RADIO_STATION_SLUG           // 'radio-station'
RADIO_STATION_FILE           // Absolute path to main plugin file
RADIO_STATION_DIR            // Absolute path to plugin directory
RADIO_STATION_BASENAME       // Plugin basename for WP
RADIO_STATION_HOME_URL       // 'https://radiostation.pro/radio-station/'
RADIO_STATION_DOCS_URL       // 'https://radiostation.pro/docs/'
RADIO_STATION_PRO_URL        // 'https://radiostation.pro/'
RADIO_STATION_DEBUG          // Boolean, triggered by ?rs-debug=1
RADIO_STATION_SAVE_DEBUG     // Boolean, triggered by ?rs-save-debug=1

// CPT Slugs (prefixed or legacy depending on option)
RADIO_STATION_SHOW_SLUG      // 'rs-show' or 'show'
RADIO_STATION_PLAYLIST_SLUG  // 'rs-playlist' or 'playlist'
RADIO_STATION_OVERRIDE_SLUG  // 'rs-override' or 'override'
RADIO_STATION_GENRES_SLUG    // 'rs-genres' or 'genres'
RADIO_STATION_LANGUAGES_SLUG // 'rs-languages'
RADIO_STATION_HOST_SLUG      // 'rs-host'
RADIO_STATION_PRODUCER_SLUG  // 'rs-producer'
```

---

## Custom Post Types

- **Show** (`show` or `rs-show`) — The core content type. Has weekly shift scheduling, images, hosts, producers, genres, languages, playlists, and related posts.
- **Playlist** (`playlist` or `rs-playlist`) — Track listings linked to Shows.
- **Override** (`override` or `rs-override`) — One-off schedule exceptions, optionally linked to a Show.

---

## Custom Taxonomies

- **Genres** (`genres` or `rs-genres`)
- **Languages** (`rs-languages`)

---

## User Roles

- **Host** (`rs-host`) — Can edit assigned Shows
- **Producer** (`rs-producer`) — Can edit assigned Shows

---

## Shortcodes

| Shortcode | Description |
|---|---|
| `[radio-player]` | Stream audio player |
| `[master-schedule]` | Full show schedule (Table, Tabs, or List view) |
| `[show-list]` | List of all shows |
| `[current-show]` | Currently airing show |
| `[upcoming-shows]` | Next upcoming shows |
| `[current-playlist]` | Currently playing playlist |
| `[radio-clock]` | Station time clock |

All shortcodes are also available as Widgets and Gutenberg Blocks.

---

## Audio Player

- Supports: MP3, AAC/M4A, OGG, OGA, WebM, RTMPA, OPUS
- Compatible with: Shoutcast, Icecast, Live365, Radio.co
- Audio scripts: Amplitude (default), jPlayer (Howler disabled due to browser incompatibilities)
- Light and Dark themes
- 3 button styles

---

## Coding Standards

- **Language:** PHP 7.0+ compatible
- **WordPress Coding Standards:** Follow WordPress PHP coding standards
- **Prefix:** All functions, classes, hooks, and options use `radio_station_` prefix
- **Escaping:** Always use WordPress escaping functions (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`)
- **Sanitization:** Always sanitize input with `sanitize_text_field`, `absint`, etc.
- **Nonces:** Use WordPress nonces for all form submissions and AJAX calls
- **Hooks:** Use WordPress actions and filters; avoid direct function calls where hooks exist
- **Inline comments:** Use version-tagged inline comments (e.g. `// 2.7.1: description of change`)
- **Debug output:** Wrap all debug output in `if ( RADIO_STATION_DEBUG )` checks
- **No closing PHP tag** at end of files

---

## Admin Structure

Admin menus and pages are registered in `radio-station-admin.php`. The plugin adds:
- A top-level **Radio Station** menu in wp-admin
- Submenus for: Settings, Schedule, Shows, Playlists, Overrides, Genres, Languages, Docs, Pricing

---

## Freemius Integration

- Freemius SDK is in `/freemius/` — **do not modify this folder**
- The Freemius instance is initialized in `loader.php`
- PRO features are gated via plan checks using `radio_station_check_plan_options()`
- Free vs PRO is determined by Freemius license

---

## Template System

- Templates are loaded via `radio_station_get_template()` in `includes/templates.php`
- Templates can be overridden by placing files in the active theme's `/radio-station/` folder
- Both plugin and theme template paths are checked

---

## Transient Caching

- Schedule data is cached in WordPress transients
- Clear cache with `radio_station_clear_cached_data()`
- Transients are auto-cleared on Show/Override post status changes
- Debug mode or `clear_transients` setting will clear on every page load

---

## Branch Workflow

- **`master`** — Stable, production-ready code
- **`develop`** — Active development branch; all Claude changes go here
- Tony Hayes (majick) reviews and merges develop → master
- WordPress.org SVN deployment is handled separately from the Git repo

---

## Development Workflow with Claude

1. Always work on the `develop` branch in GitHub Desktop
2. Claude edits files in `/Users/tonyzeoli/Documents/GitHub/radio-station/`
3. Run `syncrs` in Terminal to push changes to LocalWP for testing
4. Test changes at your LocalWP site in the browser
5. Commit via GitHub Desktop with a clear commit message
6. Push to `develop` on GitHub for Tony Hayes to review

---

## What Claude Should NOT Modify

- `/freemius/` — Freemius SDK, managed externally
- `/vendor/` — Composer dependencies, managed via composer
- `/assets/` — WordPress.org SVN assets (banners/icons), not plugin code
- `/languages/` — Translation files, generated separately
- Version numbers in `radio-station.php` and `readme.txt` — only update on release

---

## UI/UX Principles

- Keep all existing functionality intact — no feature removal
- Maintain backwards compatibility with existing shortcode attributes and widget settings
- Admin UI should follow WordPress admin design conventions
- Frontend output should be theme-agnostic and not impose opinionated styles
- CSS classes use `radio-station-` prefix throughout
- All UI changes should be mobile-responsive
- Accessibility: maintain proper ARIA labels, semantic HTML, keyboard navigation

---

## Key Helper Functions

```php
radio_station_get_setting( $key )         // Get a plugin option value
radio_station_get_template( $context, $file, $type )  // Load a template file
radio_station_clear_cached_data()         // Clear all schedule transients
radio_station_debug( $data, $echo, $file ) // Debug output/logging
radio_station_check_plan_options()        // Check free vs PRO feature gates
```

---

## Contact

- **Tony Zeoli** — Product owner, deployment, AI-assisted development
- **Tony Hayes (majick)** — Lead developer, GitHub merges, core architecture
