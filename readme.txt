=== Basic User Avatars ===
Contributors: strangerstudios, jaredatch
Tags: avatar, gravatar, bbpress, profiles
Requires at least: 4.5
Tested up to: 5.5
Stable tag: trunk
 
Adds an avatar upload field to user profiles. Front-end support. bbPress support.

== Description ==

Adds an avatar upload field to user profiles inside the WordPress dashboard.

Provides a plugin for front-end avatar management for sites that what to keep users out of the dashboard. Shortcode is `[basic-user-avatars]`.

Automatically adds avatar support to bbPress (2.3+) user profiles if bbPress is activated.

**[This plugin is on GitHub!](https://github.com/strangerstudios/basic-user-avatars/)** Pull requests are welcome. If possible please report issues throug Github.

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

If you are having a problem deactivate all plugins other than this one and then switch your theme to TwentyTweleve (or similar). If things work, then the issue is with a 3rd party theme or plugin.

If things are still not functioning properly then proceed to reporting an issue. The ideal method is via [GitHub issues](https://github.com/strangerstudios/basic-user-avatars/issues/).

== Screenshots ==

1. Option on the user profile edit page. 
1. Option on the front-end via the shortcode.
1. Option on the bbPress user profile edit page. 

== Changelog ==
= 1.0.4 - 2020-01-12 =
* BUG FIX: Fixed typos on readme.txt - Thanks @fred-pedro
* ENHANCEMENT: Added translation for Spanish - Thanks @ricardpriet
* ENHANCEMENT: Added translation for Brazilian Portuguese - Thanks @allysonsouza
* ENHANCEMENT: Added translation for Catalan - Thanks @alvaromartinezmajado
* ENHANCEMENT: Added proper SSL support - Thanks @zachwills
* ENHANCEMENT: Escaped strings.

= 1.0.3 - 2015-08-05 =
* ENHANCEMENT: Added Swedish and Finnish translations - Thanks @dmaester

= 1.0.2 - 2015-04-17 =
* ENHANCEMENT: Added POT file for translation

= 1.0.1 - 2015-03-17 =
* BUG FIX: Updated textdomain for better support
* ENHANCEMENT: Added plugin to GitHub for better collaboration

= 1.0.0 =
* Initial launch, should be considered beta. Use with caution.
