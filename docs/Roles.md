# Radio Station Plugin Roles

***

Note that while the default interface in WordPress allows you to assign a single role to a user, it also supports multiple roles, but you need to add a plugin to get an interface for this. The [Multiple Roles Plugin](https://wordpress.org/plugins/multiple-roles/) is one such plugin. Radio Station Pro includes a Role Editing Interface for assigning plugin roles to users directly, and this is available on the Plugin Settings page. If you want more fine-grained control over specific capabilities and roles, then you can use the [User Role Editor Plugin](https://wordpress.org/plugins/). 


### Plugin Roles

#### Show Host Role
Previously labelled DJ role, users with this Role can be assigned to a Show and are displayed as Show Hosts on the Show page, as well as in other widgets and shortcodes if the option to display them is set. Hosts assigned to a Show are then able to Edit that Show as well.

#### Show Producer Role
Similar to the Show Host role, users with this Role can be assigned to a Show and are displayed on the Show page, as well as in other widgets and shortcodes if the option to display them is set. Producers assigned to a Show are then able to Edit that Show as well.

#### Show Editor Role
Since 2.3.0, a new Show Editor role has been added with Publish and Edit capabilities for all Radio Station Post Types. You can assign this Role to any user to give them full Station Schedule updating permissions, without them needing to be a site Administrator.


### WordPress Role Capabilities

#### Author Role Capabilities
There is a Plugin Setting which if enabled, allows users with the in-built WordPress Author role to publish and edit their own Shows and Playlists.

#### Editor Role Capabilities
There is a Plugin Setting which if enabled, allows users with the in-built WordPress Editor role to edit all Radio Station post types.

#### Administrator Role Capabilities
Users with Administrator role are automatically given all permissions to edit all Radio Station post types.


### Role Editing

#### [Pro] Role Editor Interface
Accessible from the Plugin Settings page under the Roles tab.  
Allows you to assign any of the Radio Station plugin Roles directly to any user.
