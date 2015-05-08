=== Basic User Avatars ===
Contributors: jaredatch
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AD8KTWTTDX9JL
Tags: avatar, gravatar, bbpress, profiles
Requires at least: 4.0
Tested up to: 4.2
Stable tag: trunk
 
Adds an avatar upload field to user profiles. Front-end support. bbPress support.

== Description ==

Adds an avatar upload field to user profiles inside the WordPress dashboard.

Provides a plugin for front-end avatar management for sites that what to keep users out of the dashboard. Shortcode is `[basic-user-avatars]`.

Automatically adds avatar support to bbPress (2.3+) user profiles if bbPress is activated.

**[This plugin is on GitHub!](https://github.com/jaredatch/basic-user-avatars/)** Pull requests are welcome. If possible please report issues throug Github.

Note: This plugin is a fork of Simple Local Avatars v1.3.1 by Jake Goldman (10up). If you want snazzy ajax and some other nifty features, check out [Simple Local Avatars 2.x](http://wordpress.org/plugins/simple-local-avatars).

== Installation ==

1. Upload `basic-user-avatars` to your `/wp-content/plugins/` directory or download through the Plugins page
1. Activate the plugin through the 'Plugins' menu in WordPress
1. If you only want users with file upload capabilities to upload avatars, check the applicable option under Settings > Discussion
1. Start uploading avatars by editing user profiles!

bbPress support is added automatically if bbPress is activated.

Shortcode for front-end support is `[basic-user-avatars]`.

== Frequently Asked Questions ==

= I'm still having problems =

While this plugin has been tested with common server setups, some enviroments could cause an issue.

If you are having a problem deactivate all plugins other than this one and then switch you theme to TwentyTweleve (or similar). If things then work, then the issue is with a 3rd party theme or plugin.

If things are still not functioning incorrectly then proceed to reporting an issue. The ideal method is via [GitHub issues](https://github.com/jaredatch/basic-user-avatars/issues/).

== Screenshots ==

1. Option on the user profile edit page. 
1. Option on the front-end via the shortcode.
1. Option on the bbPress user profile edit page. 

== Changelog ==

= 1.0.3 (5/8/2015)
* Added Swedish and Finnish translations - props dmaester

= 1.0.2 (4/17/2015)
* Added POT file for translation

= 1.0.1 (3/17/2015)
* Updated textdomain for better support
* Added plugin to GitHub for better collaboration

= 1.0.0 =
* Initial launch, should be considered beta. Use with caution.