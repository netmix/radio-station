# Radio Station Documentation Index

*** 


| Topic | Description |
| --- | --- |
| [FAQ](./FAQ.md) | Frequently Asked Questions |
| [Options](./Options.md) | Plugin Option Details and Value Filters |
| [Display](./Display.md) | Automatic Pages, Templates, Images and Translations |
| [Player](./Player.md) | Radio Stream Player |
| [Manage](./Manage.md) | Admin Lists, Content Overview Dashboard, Shift Editing, Conflict Checker |
| [Roles](./Roles.md) | Plugin Roles and related Capabilities |
| [Widgets](./Widgets.md) | Current Show, Upcoming Shows, Playlist, Clock, Player |
| [Shortcodes](./Shortcodes.md) | Data List Archives and Master Schedule Views |
| [Data](./Data.md) | Post Types,  Taxonomies and Translations |
| [Filters](./Filters.md) | Custom Development Value Filters |
| [API](./API.md) | Data API via REST and Feed Endpoints
| [Roadmap](./Roadmap.md) | Feature Roadmap for Free and Pro Versions |
| [Changelog](../CHANGELOG.md) | Log of Changes for each Release |
| [Pro](./Pro.md) | Index of Pro Feature Documentation |


### Quickstart Guide

Once you have installed and activated the **Radio Station** plugin on your WordPress site, your WordPress Admin area will now have a new menu item titled Radio Station with submenu page items. Note if you have a specific question, you can check out the [Frequently Asked Questions](./FAQ.md) as you may find the answer there.

Firstly, you can visit the Plugin Settings screen to adjust the default [Plugin Options](./Options.md) to your liking. Here you can set your Radio Timezone and Language, along with your Streaming URL and Station Logo, as well as other global plugin settings. Also from this Settings page you can assign [Automatic Pages](./Display.md#automatic-pages) and Views for your Program Schedule display and for other plugin post type archive displays. But first you will want to add some Shows to display in your Schedule!

To do this, click on Shows in the admin submenu, then on "Add a New Show" at the top. Give it a Shift timeslot and a description and then click Publish. Then view the Show page by clicking the Show Permalink under the show title. (Depending on your Theme, you may wish to adjust the [Templates](./Display.md#page-templates) used.) You can also assign different [Images](./Display.md#images) to Shows. 

Next, have a look at your Program Schedule page to see the Show displayed there also. Keep adding Shows until you have your Schedule filled in! You can also add schedule Overrides for specific date and time blocks only. For ease of use they can be fully or partially linked to an existing Show. You can further [Manage](./Manage.md) your Shows and other Station data via the WordPress Admin area. The [Content Overview Dashboard](./Manage.md#content-overview-dashboard) shows at a glance how complete your Schedule, Content and Settings are, with shortcuts to fix anything that is missing.

Now you may want to give some users on your site some plugin [Roles](./Roles.md). (Note that while the default interface in WordPress allows you to assign a single role to a user, it also supports multiple roles, but you need to add a plugin to get an interface for this.) Giving a user role of Host/DJ or Producer to a user will allow them to be assigned to a Show on the Show Edit Page and allow them to edit that Show. You can also assign the Show Editor role if you have someone who needs to edit all plugin post types without being a site Administator.

There are a number of [Widgets](./Widgets.md) you can add to your site via your *Appearance -> Widgets* admin submenu. These are also available as [Shortcodes](./Shortcodes.md) and [Blocks](./Widgets.md#radio-station-blocks). There are widgets for the Current Show or Playlist, and another to display Upcoming Shows. In this way you can also add a Stream Player, Radio Clock, Schedule View or Show List anywhere you like.

Radio Station has several in-built [Data](./Data.md) types. These include [Custom Post Types](./Data.md#custom-post-types) for Shows, Schedule Overrides and Playlists. There are [Taxonomies](./Data.md#taxonomies) for Genres and Languages. You can override most data values and display output via custom [Data Filters](./Filters.md#data-filters) throughout the plugin. We have also incorporated a [Data API](./API.md) in the plugin available via REST and/or WordPress Feeds, and this data is accessible in JSON format. 

This plugin is under active development and we are continuously working to enhance the free version available on [WordPress.Org](https://wordpress.org/plugins/radio-station/), as well as creating new feature additions for [Radio Station Pro](https://radiostation.pro/). Check out the [Roadmap](./Roadmap.md) if you are interested in seeing what is coming up next!

#### [Pro] Professional Version Documentation

For ease of reference, documentation of features that are included in [Radio Station Pro](https://radiostation.pro) are included within the Free Documentation here, simply marked with `[Pro]` like the heading above. For a list of all these features linked to their revelant sections see the [Pro Feature Index](./Pro.md)

#### Plugin Support and Contributing

If you are wanting to Submit a Bug or Feature Request, you can do so via the [WordPress.Org Plugin Support Forum](https://wordpress.org/support/plugin/radio-station/), but we would prefer you submit a more detailed issue via [GitHub Issues](https://github.com/netmix/radio-station/issues) where we track and prioritize these using GitHub Projects.

Similarly, you can Contribute directly to the plugin via submitting an Issue or Pull Request on the [Github Plugin Repository](https://github.com/netmix/radio-station/). Or if you would prefer to get involved in the plugin's development even more substantially, please [Contact Us via Email](mailto:info@netmix.com) and let us know what you would like to do.

