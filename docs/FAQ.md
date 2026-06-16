# Radio Station Plugin FAQ

***
### Getting Started

#### How do I get started with Radio Station (Free or PRO)?

Read the [Quickstart Guide](./index.md#quickstart-guide) for an introduction to the plugin, what features are available and how to set them up.

#### Where can I find the full Radio Station documentation (Free or PRO)?

The latest documentation [can be found online here](https://radiostation.pro/docs/). Documentation is also included with the currently installed version via the Radio Station Help menu item located under the Radio Station admin menu. You can find the Markdown-formatted files in the `/docs` folder of the [GitHub Repository](https://github.com/netmix/radio-station/docs/) and in the `/docs` folder of the plugin directory. 

#### How do I get support for Radio Station (Free or PRO)?

For Radio Station customers using the free, open-source version of our plugin, you can contact us via [our support channel in the WordPress support forums here](https://wordpress.org/plugins/support/radio-station). If you have any bug reports or feature suggestions please [open an issue on our Github repository](https://github.com/netmix/radio-station/) For Radio Station PRO subscribers, you can email us at support@radiostation.pro and someone will respond to your inquiry within 12 to 124 hours. All support inquiries will be handled in the order they are received. Before contacting support or opening an issue, make sure you check for conflicts by disabling all of your plugins and re-enabling them one at a time to ascertain which plugin is conflicting with Radio Station. Note that Radio Station PRO works as an addon to Radio Station, so deactivating it will disable the PRO features until you reactivate it.

#### Can I try Radio Station PRO before I purchase the plugin?

Yes, you can trial Radio Station PRO for up to 14 days. You are required to set a credit or debit card when you sign up for the free trial and can cancel any time before the trial ends. The credit or debit card on file will be charged automatically once the trial expires 14 days from the date your free trial began. [Click here to start your trial.](https://radiostation.pro/pricing)


### Account and Billing

#### How do I access my Radio Station PRO account?

We have partnered with Freemius who provide an integrated subscription and upgrade system for WordPress plugin developers. When you purchase Radio Station PRO, you will receive email instructions to create your account, which contain a link to the [Freemius User Dashboard here](https://users.freemius.com/login). While you can see your account details by navigating to WordPress Dashboard > Radio Station > Account, logging into the Freemius Dashboard will give you full control over your account.

#### Can I request a refund for Radio Station PRO?

We offer a 30 day, moneyback guarantee. If you are not satisfied with Radio Station PRO within the 30 day period, we will issue a full refund, no questions asked. Once the 30 day period is exhausted refunds are not available.

#### How do I cancel my Radio Station PRO subscription?

Login to your [Freemius User Dashboard](https://users.freemius.com/login) and navigate to Renewals & Billing to cancel your Radio Station PRO subscription. When you cancel your subscription, your account will stay active for the remainder of the billing period. Once your subscription is cancelled, you will lose access to PRO customer support and any future upgrades, bug fixes and feature additions. Radio Station (free) will continue to operate normally.


### Plugin Usage

#### How do I schedule a Show? 

Simply create a new show via Add Show in the Radio Station plugin menu in the Admin area. You will be able to assign Shift timeslots to it on the Show edit page, as well as add the Show description and other meta fields, including Show images.
In order to schedule a Show, a Show must be added and available to accept schedule entries. If this is your first time using Radio Station, create a new show via the Add Show item in the Radio Station menu in your WordPress Admin screen. You can assign Shift timeslots to your new Show or pre-existing Show on the Show edit page, as well as add a Show description and other meta fields, including Show images.

#### How do I display a full schedule of my Station's shows? 

Navigate to the plugin Settings page via the Radio Station menu in your WordPress Admin screen and then click the Pages tab. There you can select the Page on which to automatically display the schedule, as well as which View to display (a Table grid by default.) Alternatively, you can use the shortcode `[master-schedule]` on any page (or post.) This option allows you to use additional shortcode attributes to control what is displayed in your Schedule (see [Master Schedule Shortcode Docs](./Shortcodes.md#master-schedule-shortcode) )

#### What if I want to schedule a special event or one-off schedule change?

If you have a one-off event that you need to show up in the Schedule and Widgets, you can create a Schedule Override by navigating to WordPress Dashboard > Radio Station > Schedule Overrides > Add New. This will allow you to set aside a block of time on a specific date, and when the Schedule or Widget is displaying that date, the override will be used instead of the normally scheduled Show. You can also link an Override to an existing Show, and partially update any Show information to be overridden. (Note that Schedule Overrides will not display in the old Legacy Table/Div Views of the Master Schedule.) In the Free version, if an Override needs to apply to multiple dates, you must schedule each time slot individually. In the PRO version, you can repeat the Override via day periods or monthly recurrences. To enable recurring overrides, [Upgrade to Pro here](https://radiostation.pro/pricing/)

#### How do I change the Show Image displayed in the widgets and schedule? 

The schedule, widgets and show page will display whichever avatar is assigned to the show on the Show Edit screen. Navigate in the WordPress Dashboard to Radio Station -> Shows and locate the Show you want to add/edit the Avatar for in the Shows list. Edit the Show then simply set a new image for the Show.

#### How do I style how the plugin displays content on the front end of my site?

The default styles for Radio Station have intentionally kept fairly minimal so as to be compatible with most themes, so you may wish to add your own CSS styles to suit your site's look and feel. You can add these styles via Theme Customizer's Additional CSS setting. You can also add your own `rs-custom.css` file to your Child Theme's directory, and Radio Station will automatically detect the presence of this file and enqueue it. Either way you can add more specific selectors with rules that modify or override the existing styles. You can find the base styles in the `/css/` directory of the plugin. 


### Widgets, Blocks and Shortcodes

#### What Widgets/Blocks are available with this plugin?

The following Widgets are available to add via the WordPress Appearance -> Widgets page:

- Streaming Player
- Current Show
- Upcoming Shows
- Current Playlist
- Radio Clock
 
Since 2.5.0, these widgets are now also available as Gutenberg Blocks. See the [Widget Documentation](./Widgets.md) for more details on these Widgets.

#### Do the Widgets reload automatically?

In the free version of Radio Station, the Current Show, Upcoming Shows, and Current Playlist widgets can display a countdown, but do not refresh automatically. To enable auto-reloading of these widgets, so that they refresh exactly at a Show’s changeover time, [upgrade to Radio Station PRO](https://radiostation.pro/pricing/).

Current Show, Upcoming Shows and Current Playlist widgets do not refresh automatically in the Free version of Radio Station. This functionality is only available in our Pro version so widgets refresh exactly at a Show's changeover time. To enable s refresh exactly at Show changeover times. To enable auto-refresh widgets [upgrade to Radio Station PRO](https://radiostation.pro)

#### What Shortcodes are available in Radio Station?

See the [Shortcode Documentation](./Shortcodes.md) for more details and a full list of possible Attributes for these Shortcodes:

* `[master-schedule]` - Master Program Schedule Display
* `[current-show]` - Current Show Widget
* `[upcoming-shows]` - Upcoming Shows Widget
* `[current-playlist]` - Current Playlist Widget
* `[shows-archive]` - Archive List of Shows
* `[genres-archive]` - Archive List of Shows sorted by Genre
* `[languages-archive]` - Archive List of Shows sorted by Language
* `[overrides-archive]` - Archive List of Schedule overrides
* `[playlists-archive]` - Archive List of Show Playlists

(Note old shortcode aliases will still work in current and future versions to prevent breakage.)

#### Do you include Page Builder support?

The free version of Radio Station includes support for classic Widgets and Gutenberg Blocks. The same widgets are available as modules for Elementor and Beaver Builder in the Pro version, along with additional styling options for each module. To enable these page builder modules, [upgrade to PRO here](https://radiostation.pro/pricing/).


### User Plugin Roles

#### How do I assign a User as the Host or Producer to one or more Shows? 

You can to assign the Host or Producer roles to any WordPress User by accessing the User editor located under WordPress Dashboard -> Users. Search for or navigate to the User you want to assign as a Host or Producer, then click Edit to open the Edit screen for that User. Find the Roles dropdown menu and select from the Role options provided. Choosing either Host or Producer grants the User that role. You can then assign that User to single or multiple Shows via the Show Edit page. A Host or Producer only has permissions to Edit the Show(s) they are assigned to. The Pro version includes an additional Role Editor interface where you can assign the plugin Roles to any number of users at once. To enable the Role Editor interface, [upgrade to PRO here](https://radiostation.pro/pricing/).

#### How do I grant users other than Administrator and DJ roles permission to edit Shows and Playlists?

There are a number of different options depending on what your goals are. Assigning a user as a Host or Producer to a Show will allow that User to edit it (and it's Playlists.) You could also assign a single User as the Author of a Show/Playlist. If you’d like to give a user that isn't a site Administrator permissions for all Radio Station records, you can assign them the Show Editor role that was created for this purpose. This may help keep clear lines of separation for editorial responsibility over your content. You can find more information on roles in the [Roles Documentation](https://radiostation.pro/docs/Roles/).

#### How do I use a different image from the Gravatar for a Host/Producer?

If you prefer not to use WordPress-owned, Gravatar.com for your User profile images,  you'll need to install a plugin that allows you to add a different image to your Host/Producer's user account. You can search for a free plugin in the WordPress plugin repository at WordPress.org. As there are a number of plugins that do this already, it's mostly out of the scope of this plugin. However, in our Pro version, you can create separate Profile pages to showcase each of your Hosts and Producers, to which you can assign profile images that appear on those. To enable Profile pages, [upgrade to PRO here](https://radiostation.pro/pricing/).


### Languages and Translations

#### What languages other than English is the plugin available in? 

As of April 1st, 2023, known languages include the following: 

* Albanian (sq_AL)
* Dutch (nl_NL)
* French (fr_FR)
* German (de_DE)
* Italian (it_IT)
* Russian (ru_RU)
* Serbian (sr_RS)
* Spanish (es_ES)
* Catalan (ca)

Note that for ease of translation the Free version contains any extra text strings from the PRO version. The auto-generated `radio-station.pot` file is located in the `/languages` directory of the plugin. If you do add a translation for your preferred language, please send or notify us of the completed translation to `info@netmix.com`. We'd love to include it.

#### Can Radio Station be translated into my language?

You may translate the plugin into any other language supported by the WordPress translation engine (GlotPress.) Here are some instructions on how to do that:

- Go to https://translate.wordpress.org/ and sign in with a WordPress.org account (or create one).
- In the search box, enter "Radio Station" plugin name or `radio-station` text domain, and click the project result to open its translation page. Here's the link: [Radio Station Translate Project Page](https://translate.wordpress.org/projects/wp-plugins/radio-station/)
- On the project page, choose the language from the language list.
- Choose the latest stable branch version number (note this may change as the plugin is updated so always select the latest version as older translations will automatically be transferred to it.)
- Use the filter/search to find a string.
- Click a string to open the editor.
- In the "Translation" box enter the German text (keep placeholders like %s, %d, intact).
- If unsure, add a comment in the discussion box for other translators/reviewers.
- For contributors without commit rights: click "Suggest new translation".
- If a translation is uncertain, mark it as "Needs work" or leave a note; do not publish incomplete translations.
- Check the right‑hand panel for translation suggestions and previous translations to stay consistent.
- Reviews are handled by language moderators. If the customer has reviewer role, they can approve suggestions from others.


### Troubleshooting

#### Why aren't all my Shows displaying in the Schedule?

Did you remember to check the "Active" checkbox for each Show? If a Show is not marked active, the plugin assumes that it's not currently in production and it is not shown on the Schedule. A Show will also not be shown if it has a Draft status or has no active Shifts assigned to it.

#### I'm seeing a 404 Not Found error when I click on the link for a Show! 

Try re-saving your site's permalink settings via Settings > Permalinks. WordPress sometimes gets confused with a new custom post type is added. Permalink rewrites are automatically flushed on plugin activation, so you can also just deactivate and reactivate the plugin to regenerate your site's permalinks.

#### Where is my data stored? Can I export my data?

Radio Station is stores your site's settings and all post type data in your WordPress MySQL database on your webhost. You can export your data using WordPress Dashboard -> Tools -> Export feature, or use Radio Station PRO’s Export feature located at WordPress Dashboard -> Import/Export. Our import/export feature works with YML and not XML, which is the standard WordPress format.

#### Why can't Show Hosts or Producers can't edit a Show page?

The only Hosts and Producers that may Edit a Show are the ones assigned as Host(s) or Producer(s) to that specific Show in the respective user selection menus. This is to prevent Hosts/Producers from editing other Shows managed by different Hosts/Producers without permission. If you need a user other than Administrator to be able to edit all Shows you can assign them a Show Editor role.


### Integrations

#### Can I use this plugin for Podcasts?

While the plugin is not specifically geared toward Podcasting, which is not live programming, some podcaster's have used Radio Station to let their subscribers know when they publish new shows.

#### Can I use this plugin for TwitchTV, Facebook Live, YouTube or Clubhouse shows?

Sure, there's no reason why you couldn't use the plugin to display a show schedule on a WordPress site for those services. Unfortunately, we are not currently syncing events from these platforms, but may do so in the future. While there may be APIs available from the larger services, Clubhouse does not yet have a public API, so scheduled rooms can't be automated to the Radio Station show scheduling system.

#### I use Google Calendar to print a show schedule online. Can I import/sync my Google Calendar with Radio Station?

We haven't built an interface between Google Calendar and Radio Station just yet, but it's on our radar to do so in the foreseeable future.

#### Can I import Show data from Pro.Radio or the JOAN (Jock on Air Now) plugin?

We do not have a method of importing data directly from JOAN or Pro.Radio


### Development Versions

#### How do I install the latest Development version for testing?

If you are having issues with the plugin, we may recommend you install the development version for further bugfix testing, as it may contain fixes that are not yet released into the next stable WordPress version. It is recommended you do this on a staging site. Instructions:

1. Visit the `develop` branch of the Radio Station Github repository at:
`https://github.com/netmix/radio-station/tree/develop/`
2. Click on the green "Code" button and select Download a ZIP.
3. Unzip the downloaded file on your computer and upload it via FTP to the subdirectory of your WordPress install on your web server: `/wp-content/plugins/radio-station-develop/`
4. Rename the subdirectory `/wp-content/plugins/radio-station/` to `/wp-content/plugins/radio-station-old/`
5. Rename the subdirectory `/wp-content/plugins/radio-station-develop/` to `/wp-content/plugins/radio-station/`

Then upload to WordPress via the plugin installer as normal.
Note that it will install to /wp-content/plugins/radio-station-develop/, and because of this won't overwrite your existing installation, so you'll need to deactivate that before activating the development version.

You can now visit your site to make sure nothing is broken. If you experience issues you can reverse the folder renaming process to activate the old copy of the plugin. If the new development version works fine, at your convenience you can delete the `/wp-content/plugins/radio-station-old/` directory.

Alternatively, if you want to do this from your WordPress Plugin area, you can upload the development Zip file from your Plugins -> Upload page. This will install it to `/wp-content/plugins/radio-station-develop/`. You can then deactivate the existing Radio Station plugin from you Plugins page and then activate the development version. (You can tell them apart on the plugins page via their version numbers. Official releases are 2.x.x, only development releases have the extra digit 2.x.x.x) Again, if you experience issues, you can deactivate the development version and reactivate the old version.

#### What about Pro Beta Version Testing?

We are constantly improving and adding new features to [Radio Station Pro](https://radiostation.pro/pricing/). Periodically we will release a Beta version to test out a new feature (or fix) out before it is officially released. If you have a Pro license, you can access these cutting edge Pro Beta version releases in two ways:

1. Download the Beta versions by logging in to your [Freemius User Dashboard](https://users.freemius.com/login). and navigating to the "Downloads" section. You will see a dropdown list of all the Radio Station Pro releases, including beta ones.
2. Enable the Beta program option from your Radio Station Account page in your WordPress site's Admin area, and the latest Beta version will then be available as an update.

**Important Note**: As we are developing the Free and Pro versions in tandem, the latest Pro Beta may require you to install a development version of the Free plugin for it to work. Please see the previous section for how you can install the development version from Github (if the required version is not yet available via the WordPress repository.)

We recommend you test these on a Staging site (or a development copy of your live site.) This way you can make sure there are no significant bugs before using it on a production site. Of course, please be willing to [report any bugs](https://github.com/netmix/radio-station/issues) that you do find so we can ensure they are not present in the next official release.


