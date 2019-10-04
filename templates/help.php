<h2>Radio Station Help/FAQs</h2>

<strong>I've scheduled all my shows, but they're not showing up on the programming grid!</strong>
<p>Did you remember to check the "Active" checkbox for each show?  If a show is not marked active, the plugin assumes that it's not currently in production and hides it on the grid.</p>

<hr />

<strong>I'm seeing 404 Not Found errors when I click on the link for a show!</strong>
<p>Try re-saving your site's permalink settings.  WordPress sometimes gets confused with a new custom post type is added.</p>

<hr />

<strong>How do I display a full schedule of my station's shows? </strong>
<p>Use the shortcode <code>[master-schedule]</code> on any page.  This will generate a full-page schedule in one of three formats.
<br /><br />
The following attributes are available for the shortcode:
	<ul style="list-style: disc inside none; text-indent: 50px;">
		<li>'list' => If set to a value of 'list', the schedule will display in list format rather than table or div format. Valid values are 'list', 'divs', 'table'.  Default value is 'table'.</li>
		<li>'time' => The time format you with to use.  Valid values are 12 and 24.  Default is 12.</li>
		<li>'show_link' => Display the title of the show as a link to its profile page.  Valid values are 0 for hide, 1 for show.  Default is 1.</li>
		<li>'display_show_time' => Display start and end times of each show after the title in the grid.  Valid values are 0 for hide, 1 for show.  Default is 1.</li>
		<li>'show_image' => If set to a value of 1, the show's avatar will be displayed.  Default value is 0.</li>
		<li>'show_djs' => If set to a value of 1, the names of the show's DJs will be displayed.  Default value is 0.</li>
		<li>'divheight' => Set the height, in pixels, of the individual divs in the 'divs' layout.  Default is 45.</li>
	</ul>
<br /><br />
For example, if you wish to display the schedule in 24-hour time format, use <code>[master-schedule time="24"]</code>.</p>

<hr />

<strong>How do I schedule a show? </strong>

<p>Simply create a new show.  You will be able to assign it to any timeslot you wish on the edit page.</p>

<hr />

<strong>What if I have a special event? </strong>

<p>If you have a one-off event that you need to show up in the On-Air or Coming Up Next widgets, you can create a Schedule Override by clicking the Schedule Override tab in the Dashboard menu.  This will allow you to set aside a block of time on a specific date, and will display the title you give it in the widgets.  Please note that this will only override the widgets and their corresponding shortcodes.  If you are using the weekly master schedule shortcode on a page, its output will not be altered.</p>

<hr />

<strong>How do I get the last song played to show up? </strong>

<p>You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode <code>[now-playing]</code> in your page/post, or use <code>do_shortcode('[now-playing]');</code> in your template files.
<br /><br />
The following attributes are available for the shortcode:
	<ul style="list-style: disc inside none; text-indent: 50px;">
		<li>'title' => The title you would like to appear over the now playing block</li>
		<li>'artist' => Display artist name.  Valid values are 0 for hide, 1 for show.  Default is 1.</li>
		<li>'song' => Display song name.  Valid values are 0 for hide, 1 for show.  Default is 1.</li>
		<li>'album' => Display album name.  Valid values are 0 for hide, 1 for show.  Default is 0.</li>
		<li>'label' => Display label name.  Valid values are 0 for hide, 1 for show.  Default is 0.</li>
		<li>'comments' => Display DJ comments.  Valid values are 0 for hide, 1 for show.  Default is 0.</li>
	</ul>
<br /><br />
Example:<br />
<code>[now-playing title="Current Song" artist="1" song="1" album="1" label="1" comments="0"]</code></p>

<hr />

<strong>What about displaying the current DJ on air? </strong>

<p>You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode <code>[dj-widget]</code> in your page/post, or you can use <code>do_shortcode('[dj-widget]');</code> in your template files.
<br /><br />
The following attributes are available for the shortcode:
	<ul style="list-style: disc inside none; text-indent: 50px;">
		<li>'title' </strong>> The title you would like to appear over the on-air block</li>
		<li>'display_djs' </strong>> Display the names of the DJs on the show.  Valid values are 0 for hide names, 1 for show names.  Default is 0.</li>
		<li>'show_avatar' </strong>> Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.</li>
		<li>'show_link' </strong>> Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.</li>
		<li>'default_name' </strong>> The text you would like to display when no show is schedule for the current time.</li>
		<li>'time' </strong>> The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.</li>
		<li>'show_sched' </strong>> Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.</li>
		<li>'show_playlist' </strong>> Display a link to the show's current playlist.  Valid values are 0 for hide link, 1 for show link.  Default is 1.</li>
		<li>'show_all_sched' </strong>> Displays all schedules for a show if it airs on multiple days.  Valid values are 0 for current schedule, 1 for all schedules.  Default is 0.</li>
		<li>'show_desc' </strong>> Displays the first 20 words of the show's description. Valid values are 0 for hide descripion, 1 for show description.  Default is 0.</li>
	</ul>
<br /><br />
Example:<br />
<code>[dj-widget title</strong>"Now On-Air" display_djs</strong>"1" show_avatar</strong>"1" show_link</strong>"1" default_name</strong>"RadioBot" time</strong>"12" show_sched</strong>"1" show_playlist</strong>"1"]</code></p>

<hr />

<strong>Can I display upcoming shows, too? </strong>

<p>You'll find a widget for just that purpose under the Widgets tab.  You can also use the shortcode <code>[dj-coming-up-widget]</code> in your page/post, or you can use <code>do_shortcode('[dj-coming-up-widget]');</code> in your template files.
<br /><br />
The following attributes are available for the shortcode:
	<ul style="list-style: disc inside none; text-indent: 50px;">
		<li>'title' => The title you would like to appear over the on-air block</li>
		<li>'display_djs' => Display the names of the DJs on the show.  Valid values are 0 for hide names, 1 for show names.  Default is 0.</li>
		<li>'show_avatar' => Display a show's thumbnail.  Valid values are 0 for hide avatar, 1 for show avatar.  Default is 0.</li>
		<li>'show_link' => Display a link to a show's page.  Valid values are 0 for hide link, 1 for show link.  Default is 0.</li>
		<li>'limit' => The number of upcoming shows to display.  Default is 1.</li>
		<li>'time' => The time format used for displaying schedules.  Valid values are 12 and 24.  Default is 12.</li>
		<li>'show_sched' => Display the show's schedules.  Valid values are 0 for hide schedule, 1 for show schedule.  Default is 1.</li>
	</ul>
	<br /><br />
	Example:<br />
	<code>[dj-coming-up-widget title</strong>"Coming Up On-Air" display_djs</strong>"1" show_avatar</strong>"1" show_link</strong>"1" limit</strong>"3" time</strong>"12" schow_sched</strong>"1"]`</code></p>

<hr />

<strong>Can I change how show pages are laid out/displayed? </strong>

<p>Yes.  Copy the radio-station/templates/single-show.php file into your theme directory, and alter as you wish.  This template, and all of the other templates in this plugin, are based on the TwentyEleven theme.  If you're using a different theme, you may have to rework them to reflect your theme's layout.</p>

<hr />

<strong>What about playlist pages? </strong>

<p>Same deal.  Grab the radio-station/templates/single-playlist.php file, copy it to your theme directory, and go to town.</p>

<hr />

<strong>And playlist archive pages?  </strong>

<p>Same deal.  Grab the radio-station/templates/archive-playlist.php file, copy it to your theme directory, and go to town.</p>

<hr />

<strong>And the program schedule, too? 	</strong>

<p>Because of the complexity of outputting the data, you can't directly alter the template, but you can copy the radio-station/css/program-schedule.css file into your theme directory and change the CSS rules for the page.</p>

<hr />

<strong>What if I want to style the DJ on air sidebar widget? </strong>

<p>Copy the radio-station/css/djonair.css file to your theme directory.</p>

<hr />

<strong>How do I get an archive page that lists ALL of the playlists instead of just the archives of individual shows? </strong>

<p>First, grab the radio-station/templates/playlist-archive-template.php file, and copy it to your active theme directory.
<br /><br />
Then, create a Page in WordPress to hold the playlist archive.
<br /><br />
Under Page Attributes, set the template to Playlist Archive.  Please note: If you don't copy the template file to your theme first, the option to select it will not appear.</p>

<hr />

<strong>Can show pages link to an archive of related blog posts? </strong>

<p>Yes, in much the same way as the full playlist archive described above. First, grab the radio-station/templates/show-blog-archive-template.php file, and copy it to your active theme directory.
<br /><br />
Then, create a Page in WordPress to hold the blog archive.
<br /><br />
Under Page Attributes, set the template to Show Blog Archive.</p>

<hr />

<strong>How can I list all of my shows? </strong>

<p>Use the shortcode <code>[list-shows]</code> in your page/posts or use <code>do_shortcode(['list-shows']);</code> in your template files.  This will output an unordered list element containing the titles of and links to all shows marked as "Active".
<br /><br />
The following attributes are available for the shortcode:
	<ul style="list-style: disc inside none; text-indent: 50px;">
		<li>'genre' => Displays shows only from the specified genre(s).  Separate multiple genres with a comma, e.g. genre="pop,rock".</li>
	</ul>
<br /><br />
Example:
<code>`[list-shows genre="pop"]`</code>
<code>[list-shows genre="pop,rock,metal"]</code></p>

<hr />

<strong>I need users other than just the Administrator and DJ roles to have access to the Shows and Playlists post types.  How do I do that? </strong>

<p>Since I'm stongly opposed to reinventing the wheel, I recommend Justin Tadlock's excellent "Members" plugin for that purpose.  You can find it on Wordpress.org, here: <a href="http://wordpress.org/extend/plugins/members/" target="_blank">http://wordpress.org/extend/plugins/members/</a>
<br /><br />
Add the following capabilities to any role you want to give access to Shows and Playlist:
	<ul style="list-style: disc inside none; text-indent: 50px;">
		<li>edit_shows</li>
		<li>edit_published_shows</li>
		<li>edit_others_shows</li>
		<li>read_shows</li>
		<li>edit_playlists</li>
		<li>edit_published_playlists</li>
		<li>read_playlists</li>
		<li>publish_playlists</li>
		<li>read</li>
		<li>upload_files</li>
		<li>edit_posts</li>
		<li>edit_published_posts</li>
		<li>publish_posts</li>
	</ul>
<br /><br />
If you want the new role to be able to create or approve new shows, you should also give them the following capabilities:
	<ul style="list-style: disc inside none; text-indent: 50px;">
		<li>publish_shows</li>
		<li>edit_others_shows</li>
	</ul>
</p>

<hr />

<strong>How do I change the DJ's avatar in the sidebar widget? </strong>

<p>The avatar is whatever image is assigned as the DJ/Show's featured image.  All you have to do is set a new featured image.</p>

<hr />

<strong>Why don't any users show up in the DJs list on the Show edit page? </strong>

<p>You did remember to assign the DJ role to the users you want to be DJs, right?</p>

<hr />

<strong>My DJs can't edit a show page.  What do I do? </strong>

<p>The only DJs that can edit a show are the ones listed as being ON that show in the DJs select menu.  This is to prevent DJs from editing other DJs shows without permission.</p>

<hr />

<strong>How can I export a list of songs played on a given date? </strong>

<p>Under the Playlists menu in the dashboard is an Export link.  Simply specify the a date range, and a text file will be generated for you.</p>

<hr />

<strong>Can my DJ's have customized user pages in addition to Show pages? </strong>

<p>Yes.  These pages are the same as any other author page (edit or create the author.php template file in your theme directory).  A sample can be found in the radio-station/templates/author.php file (please note that this file doesn't actually do anything unless you copy it over to your theme's directory).  Like the other theme templates included with this plugin, this file is based on the TwentyEleven theme and may need to be modified in order to work with your theme.</p>

<hr />

<strong>I don't want to use Gravatar for my DJ's image on their profile page. </strong>

<p>Then you'll need to install a plugin that lets you add a different image to your DJ's user account and edit your author.php theme file accordingly.  That's a little out of the scope of this plugin.  I recommend Cimy User Extra Fields:  <a href="http://wordpress.org/extend/plugins/cimy-user-extra-fields/" target="_blank">http://wordpress.org/extend/plugins/cimy-user-extra-fields/</a></p>

<hr />

<strong>What languages other than English is the plugin available in, and can you translate the plugin into my language? </strong>

<p>Right now:
	<ul style="list-style: disc inside none; text-indent: 50px;">
		<li>Albanian (sq_AL)</li>
		<li>French (fr_FR)</li>
		<li>German (de_DE)</li>
		<li>Italian (it_IT)</li>
		<li>Russion (ru_RU)</li>
		<li>Serbian (sr_RS)</li>
		<li>Spanish (es_ES)</li>
	</ul>

My foreign language skills are rather lacking.  I managed a Spanish translation, sheerly due to the fact that I still remember at least some of what I learned in high school Spanish class.  But I've included the .pot file in the /languages directory.  If you want to give it a shot, be my guest.  If you <a href="mailto:nblight@nlb-creations.com">send me</a> your finished translation, I'd love to include it.</p>
