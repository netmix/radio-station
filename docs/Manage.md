Radio Station Plugin Management

***

## Admin Lists

Shows, Overrides and Playlists each have their own standard list page via the WordPress admin area, which you can find via the Radio Station admin menu. Each list has relevant columns added so you can see more information about each record at a glance without having to Edit an individual page to see them, including Show Shifts, Show Override Dates, and Playlist Tracks. Also displayed are Show Hosts, Genres and Language terms, and the Show Avatar.

## Content Overview Dashboard

The Content Overview Dashboard gives you a quick health check of your station: where to jump to in the plugin, and how complete your schedule, content and settings are. You can see it in two places:

- **Station Content -> Dashboard** in the WordPress admin menu, a full page with a card for each section. (The "Station Content" menu is shown to users who can publish Playlists.)
- The **Radio Station** box on the main **WordPress Dashboard** (*Dashboard -> Home*), with the same information in a more compact layout. The box is titled **Radio Station Pro** when the Pro version is active. Like any Dashboard box you can drag it, collapse it, or hide it via *Screen Options*. If you have rearranged your Dashboard and want the Radio Station box back in its default position, add `?reset-dashboard-widgets=1` to the Dashboard URL (note this resets the positions of all your Dashboard boxes).

The page is organised into four parts: Quick Links, then Station Schedule, Station Content and Station Settings. Each of the last three starts with a **progress bar** under its title, a one-line description of what that bar measures, and then the individual checks that make it up.

### Quick Links

Quick Links are shortcuts to the screens you will use most, in two columns.

| First column | Second column |
| --- | --- |
| Schedule Editor `[Pro]` | Quickstart (opens the Quickstart Guide in the plugin Docs) |
| Shows | Settings |
| Episodes `[Pro]` | Docs |
| Playlists | Languages |
| Hosts `[Pro]` | Account `[Pro]` (or *Free PRO Trial* on the free version) |
| Producers `[Pro]` | |
| Specials (Schedule Overrides) | |
| Genres | |

On the free version the Pro-only links are replaced by a note and a **Go PRO** link.

### Progress Bars

Each section has a progress bar. It always shows three colours, and a percentage to the right of it:

- **Solid brand red** is the share that is done.
- **Grey** is the share that is partly done. A check that needs attention but is not missing entirely (an orange warning) counts as half done, so the other half stays grey.
- **Bright red** is the share that is missing or not set (a red cross).

For example, a Settings bar that reads 50% with no grey means half your checks are complete and the other half are not set up yet. The percentage is the "done" share only.

### Status Icons

Every check has an icon at the start of its row:

| Icon | Meaning |
| --- | --- |
| Green tick | Complete |
| Orange exclamation | Partly done, or needs attention |
| Red cross | Missing or not set |

### Reading the Status Text

The words in **bold** in each line name the thing that is being measured, for example **Shows**, **Hosts**, **Stream URL** or **Master Schedule Page**. Words that are links (such as *Description coverage*) take you to the list or setting that needs work. A number such as "100% Active" is a percentage of the items it describes, as explained for each check below.

### Row Links

The link at the end of each row takes you straight to where you can act on it, so there are no icons to interpret:

- **Add** opens the screen to add a new item of the kind in that row (a Show, Special, Host, Genre or Playlist).
- **Fix** opens the relevant plugin Settings tab and section for a check that is not complete. Once the check is complete it reads **Edit** instead.
- **Edit Schedule** `[Pro]` opens the Visual Schedule Editor. On the free version this link reads **Shows** and opens your Shows list.
- **Go Pro** appears next to features that are only available in the Pro version.

### Station Schedule

The Station Schedule line reads, for example, "7 Shifts and 0 Specials with 0% Schedule coverage."

- **Shifts** is the number of Show shifts that are not disabled.
- **Specials** is the number of Schedule Override dates that are not disabled, including recurring ones.
- **Schedule coverage** (and the progress bar) is the share of the current week that has a Show scheduled. It adds up the length of every scheduled block in this week's schedule and divides by the 168 hours in a week. Overlapping blocks are each counted, so heavily overlapping shifts can push this figure past 100%.

The icon is a red cross if there are no shifts or no coverage, a green tick at 100% coverage, and an orange exclamation in between.

### Station Content

The Station Content bar is the average completion of five checks. Each check is worth up to one full point and the bar shows their total as a percentage of five:

1. **Shows** - how many Shows are published, and the percentage of them marked *Active* on the Show edit screen. Drafts are listed as a link so you can find them. A cross means you have no Shows yet.
2. **Description coverage** - the percentage of Shows and Specials that have a description. The link opens your Shows list so you can find those without one.
3. **Hosts** - how many Hosts you have, and the percentage of Shows that have at least one Host assigned. A cross means you have no Hosts or no Show has one.
4. **Genres** - how many Genres you have, and the percentage of Shows that have at least one Genre assigned.
5. **Playlists** - how many Playlists you have. You get full credit with at least one Playlist, and half credit (an orange exclamation) with none.

For the first four checks the credit is the percentage shown, so 50% coverage gives half a point.

The **Episodes** line shows how many Episodes are published and for how many Shows. It is for information and is not counted in the bar. `[Pro]` On the free version this line shows **Go Pro** instead of an **Add** link.

### Station Settings

The Station Settings bar measures whether the key plugin settings are in place. A complete check earns one point, a partly done check earns half a point, and a missing one earns nothing.

1. **Stream URL** and **Fallback stream URL** - complete when both are set, partly done (orange) when only the Stream URL is set, and missing (red cross) when there is no Stream URL.
2. **Player Bar** `[Pro]` - missing if the Player Bar is switched off, partly done if it is on but Continuous playback is not enabled, and complete when both are on.
3. **Master Schedule Page** - complete when a page is assigned for your schedule (the line also says whether it displays automatically or manually), otherwise missing.
4. **Station information** - counts how many of the six station fields are filled in (title, image, frequency, location, phone and email). Complete when all six are set, partly done when at least half are, otherwise missing.
5. **Archive pages** - counts how many archive pages are assigned (Shows, Schedule Overrides, Playlists, Genres and Languages, plus Episodes and Teams with Pro). Complete when all are set, partly done when at least half are, otherwise missing.

### Free and Pro Versions

The Dashboard reflects whichever version is active, and updates the next time you load the page. There is nothing to configure.

- **Free version active:** only free features are counted in the bars. Pro-only items, such as the Player Bar settings and the Episode and Team archive pages, are shown as upgrade rows with a **Go Pro** link but are **not** counted, so the free version can reach 100%. The Settings bar description lists only what is counted.
- **Pro version active:** the Pro items replace the upgrade rows, and the free and Pro items are counted together.
- **Pro deactivated:** the Dashboard goes back to the free view.

## Shift Editing

You can add Show Shifts from the Show's Edit page. You can click Add Shift as many times as you like to create new Shift entries. For a Shift to be valid it must have all time fields filled in. The Encore field (repeat airing) is optional. You can disable a Shift and it will be unused but still saved. Each Shift entry also has a Duplicate and Remove icon to copy that entry to a new Shift or remove it. Shifts are not altered until you save them via clicking Update for a Show.

### [Pro] Visual Schedule Editor

[Radio Station Pro](https://radiostation.pro) includes a Visual Schedule Editor to allow you to assign Shifts directly to the Schedule - whether on the Frontend or Backend! On the backend the Visual Schedule Editor is loaded at the top of the Shows and Overrides list pages. And on the Frontend it is loaded wherever the Schedule is displayed (via shortcode or automatic page setting.)

Show Editors and Administrators can edit existing Shows and Shifts by clicking the edit pen icon next to any Show  title on the Schedule, or on any shift time. This will popup a thickbox editing screen for that Show's shifts (or  single shift time) where the shift time(s) can be modified and updated.

Clicking the plus icon will load a popup thickbox screen allowing the adding of either a Show or an Override, with the times filled in based on which timeslot is clicked. Simply modify the time as desired and choose an existing Show or Override to add the timeslot to and Save. The new shift will be added and the window will revert to an edit screen for that Show/Override.


## Shift Conflict Checking

### Shift Save Checking

Since 2.3.0, when Shifts are saved on the Show Edit page, they are checked against all other Show's (active) Shifts - and then against other Shifts for the same show - to detect for Shift Conflicts. If a conflict is found, that Shift is disabled and the specific conflict is displayed underneath the Shift after saving. 

Once the time is adjusted (or adjusted on the conflicting Show Shift and saved) so that there is no conflict, you can then uncheck the Disabled box for that Shift. If there are no further conflicts found, the Shift will then be active.

### Existing Schedule Conflicts

Since shift save checking was introduced in 2.3.0, you might already have existing schedule conflicts without realizing it. Radio Station attempts to handle existing conflicts in three ways:

* An Admin Notice is generated displaying the Shift Conflicts, linked to their Shows.
* Show Shift Conflicts are highlighted in red in the Shift column on the Admin Shows list page.
* The Master Schedule display does it's best to check for conflicts and handle the overlaps.