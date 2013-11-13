=== Easy Digital Downloads Toolbar ===
Contributors: daveshine, deckerweb
Donate link: http://genesisthemes.de/en/donate/
Tags: toolbar, adminbar, admin bar, easy digital downloads, edd, digital downloads, downloads, e-downloads, administration, resources, links, deckerweb, ddwtoolbar
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.5.1
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php

This plugin adds useful admin links and resources for the Easy Digital Downloads plugin to the WordPress Toolbar / Admin Bar.

== Description ==

> #### Have Quicker Access to Your Digital Downloads - Time Saver!
> This **small and lightweight plugin extension** just adds a lot [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/) plugin related resources to your toolbar / admin bar. Also links to all setting/ tab pages of the plugin are added making life administrators a lot easier. So you might just switch from the fontend of your site to 'Payment History' or to the 'Add new Download' page etc.

= General Features =
* Free Extension for ["Easy Digital Downloads"](http://wordpress.org/extend/plugins/easy-digital-downloads/) - all plugin settings are hooked in!
* Access your stuff from one place in the toolbar on backend and frontend, without much scrolling... in other words: manage your digital downloads even more easily :-)
* Plugin options under "Misc" settings in "Easy Digital Downloads": you can deactivate the Resources links group. (since v1.1.0)
* Official & third-party Extensions (add-ons) are also supported if they provide extra settings pages! :-)
* Alternate main Icon with 22 additional colored/alternate icons included :) (changeable via filters)
* Plus 4 filters included to change wording/tooltip and icon of the main item - for more info [see FAQ section here](http://wordpress.org/extend/plugins/edd-toolbar/faq/)
* For custom "branding" or special needs a few sections like the "Resource links group" could be hidden from displaying via your active theme/child theme - for more info [see FAQ section here](http://wordpress.org/extend/plugins/edd-toolbar/faq/)
* Supporting all official *Easy Digital Downloads* sites, so just the whole ecosystem for this plugin :)
* Fully internationalized! Real-life tested and developed with international users in mind!
* Fully WPML compatible!
* Fully Multisite compatible, you can also network-enable it if ever needed (per site use is recommended).
* Tested with WordPress versions 3.4 branch, 3.5 branch (and before with 3.3 branch) - also in debug mode (no stuff there, ok? :)
* Link to downloadable German language packs - only displayed when German locales are active (de_DE, de_AT, de_CH, de_LU)
* *NOTE:* I would be happy to add more language/locale specific resources and more useful third-party links - just contact me!

As the name suggests this plugin is **intended towards webmasters and administrators**. The new toolbar / admin bar entries will only be displayed if the current user has the WordPress cabability of `edit_posts`. (Note: I am open for suggestions here if this should maybe changed to a more suitable capability.)

= Localization =
* English (default) - always included
* German (de_DE) - always included
* Spanish (es_ES) - user-submitted - 82% complete for v1.5.1
* .pot file (`edd-toolbar.pot`) for translators is also always included :)
* Easy plugin translation platform with GlotPress tool: [Translate "Easy Digital Downloads Toolbar"...](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/edd-toolbar)
* *Your translation? - [Just send it in](http://genesisthemes.de/en/contact/)*

[A plugin from deckerweb.de and GenesisThemes](http://genesisthemes.de/en/)

= Feedback =
* I am open for your suggestions and feedback - Thank you for using or trying out one of my plugins!
* Drop me a line [@deckerweb](http://twitter.com/deckerweb) on Twitter
* Follow me on [my Facebook page](http://www.facebook.com/deckerweb.service)
* Or follow me on [+David Decker](http://deckerweb.de/gplus) on Google Plus ;-)

= More =
* [Also see my other plugins](http://genesisthemes.de/en/wp-plugins/) or see [my WordPress.org profile page](http://profiles.wordpress.org/daveshine/)
* Tip: [*GenesisFinder* - Find then create. Your Genesis Framework Search Engine.](http://genesisfinder.com/)

== Installation ==

1. Upload the entire `edd-toolbar` folder to the `/wp-content/plugins/` directory -- or just upload the ZIP package via 'Plugins > Add New > Upload' in your WP Admin
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Look at your toolbar / admin bar and enjoy using the new links there :)
4. Go and manage your digital downloads even more easily :)

**Note:** It's not required to also have the "Easy Digital Downloads" plugin installed but without it this toolbar plugin makes absolutely no sense! -- I recommend to use my plugin with ["Easy Digital Downloads"](http://wordpress.org/extend/plugins/easy-digital-downloads/) v1.0.5 or higher.

**Own translation/wording:** For custom and update-secure language files please upload them to `/wp-content/languages/edd-toolbar/` (just create this folder) - This enables you to use fully custom translations that won't be overridden on plugin updates. Also, complete custom English wording is possible with that, just use a language file like `edd-toolbar-en_US.mo/.po` to achieve that (for creating one see the tools on "Other Notes").

== Frequently Asked Questions ==

= Does this plugin work with the latest WP version and also older versions? =
Yes, this plugin works really fine with the latest WordPress 3.3+ including latest 3.4-beta!
And sorry, it DOES NOT work with older WordPress versions so please update your install if you haven't done yet :).

= How are new resources being added to the admin bar? =
Just drop me [@deckerweb](http://twitter.com/deckerweb) or Pippin Williamson [@pippinsplugins](http://twitter.com/pippinsplugins) a note via Twitter or via our contact pages and we'll add the link if it is useful for admins/ webmasters and the Easy Digital Downloads plugin ecosystem.

= Is this plugin Multisite compatible? =
Yes, it is! :) Works really fine in Multisite invironment - here I just recommend to activate on a per site basis so to load things only where and when needed.

= In Multisite, could I "network enable" this plugin? =
Yes, you could. -- However, it doesn't make much sense. The plugin is intented for a per site use as the admin links refer to the special settings for that certain site/blog. So if you have a Multisite install with 5 sites but only 3 would run "Easy Digital Downloads" then the other 2 sites will only see support links in the Toolbar / Admin Bar... I guess, you got it? :)

Though intended for a per site use it could make some sense in such an edge case: if all of the sites in Multisite use Easy Digital Downloads. This might be the case if you use Multisite for multilingual projects, especially via that awesome plugin: http://wordpress.org/extend/plugins/multilingual-press/

= Why is there no admin settings page to this plugin? =
This plugin has NO settings page because I believe it's just not neccessarry. All customizing could be done via filters, constants and regular WordPress user roles & capabilities. As the plugin is indended for a admin/ webmaster use that's the way to go. This way we can save the overhaul of an options panel/settings page, additional database storing & requests, uninstall routines and such. End result: a lightweight system that just works and saves clicks & time :-).

= Can custom menu items be hooked in via theme/child theme or other plugins? =
Yes, this is possible since version 1.2 of the plugin! There are two action hooks available for hooking custom menu items in -- `eddtb_custom_main_items` for the main section and `eddtb_custom_group_items` for the resource group section. Here's an example code:
`
add_action( 'eddtb_custom_group_items', 'eddtb_custom_additional_group_item' );
/**
 * Easy Digital Downloads Toolbar: Custom Resource Group Items
 *
 * @global mixed $wp_admin_bar
 */
function eddtb_custom_additional_group_item() {

	global $wp_admin_bar;

	$wp_admin_bar->add_menu( array(
		'parent' => 'ddw-edd-eddbar',
		'title'  => __( 'Custom Menu Item Name', 'your-textdomain' ),
		'href'   => 'http://deckerweb.de/',
		'meta'   => array( 'title' => __( 'Custom Menu Item Name Tooltip', 'your-textdomain' ) )
	) );
}
`

= Can certain sections be removed? =
Yes, this is possible! You can remove the following links or sections: "Add-Ons" settings link, "Resources link group" at the bottom (all items), "German language stuff" (all items) and "Translations resources" (all items)

To achieve this add one, some or all of the following constants to your current theme's/child theme's `functions.php` or similar file:
`
/** Easy Digital Downloads Toolbar: Remove Add-On(s) Item(s) */
define( 'EDDTB_ADDONS_DISPLAY', FALSE );

/** Easy Digital Downloads Toolbar: Remove Resource Items */
define( 'EDDTB_RESOURCES_DISPLAY', FALSE );

/** Easy Digital Downloads Toolbar: Remove German Language Items */
define( 'EDDTB_DE_DISPLAY', FALSE );

/** Easy Digital Downloads Toolbar: Remove Translations Items */
define( 'EDDTB_TRANSLATIONS_DISPLAY', FALSE );
`

= Can the the whole toolbar entry be removed, especially for certain users? =
Yes, that's also possible! This could be useful if your site has special user roles/capabilities or other settings that are beyond the default WordPress stuff etc. For example: if you want to disable the display of any "Easy Digital Downloads Toolbar" items for all user roles of "Editor" please use this code:
`
/** Easy Digital Downloads Toolbar: Remove all items for "Editor" user role */
if ( current_user_can( 'editor' ) ) {
	define( 'EDDTB_DISPLAY', FALSE );
}
`

To hide only from the user with a user ID of "2":
`
/** Easy Digital Downloads Toolbar: Remove all items for user ID 2 */
if ( 2 == get_current_user_id() ) {
	define( 'EDDTB_DISPLAY', FALSE );
}
`

To hide items only in frontend use this code:
`
/** Easy Digital Downloads Toolbar: Remove all items from frontend */
if ( ! is_admin() ) {
	define( 'EDDTB_DISPLAY', FALSE );
}
`

In general, use this constant do hide any "Easy Digital Downloads Toolbar" items:
`
/** Easy Digital Downloads Toolbar: Remove all items */
define( 'EDDTB_DISPLAY', FALSE );
`

= Available Filters to Customize More Stuff =
All filters are listed with the filter name in bold and the below additional info, helper functions (if available) as well as usage examples.

**eddtb_filter_capability_all**

* Default value: `edit_theme_options`
* 4 Predefined helper functions:
 * `__eddtb_admin_only` -- returns `'administrator'` role -- usage:
`
add_filter( 'eddtb_filter_capability_all', '__eddtb_admin_only' );
`
 * `__eddtb_role_editor` -- returns `'editor'` role -- usage:
`
add_filter( 'eddtb_filter_capability_all', '__eddtb_role_editor' );
`
 * `__eddtb_role_shop_manager` -- returns `'shop_manager'` role -- usage:
`
add_filter( 'eddtb_filter_capability_all', '__eddtb_role_shop_manager' );
`
 * `__eddtb_role_shop_accountant` -- returns `'shop_accountant'` role -- usage:
`
add_filter( 'eddtb_filter_capability_all', '__eddtb_role_shop_accountant' );
`
 * `__eddtb_role_shop_worker` -- returns `'shop_worker'` role -- usage:
`
add_filter( 'eddtb_filter_capability_all', '__eddtb_role_shop_worker' );
`
 * `__eddtb_role_shop_vendor` -- returns `'shop_vendor'` role -- usage:
`
add_filter( 'eddtb_filter_capability_all', '__eddtb_role_shop_vendor' );
`
 * `__eddtb_cap_manage_options` -- returns `'manage_options'` capability -- usage:
`
add_filter( 'eddtb_filter_capability_all', '__eddtb_cap_manage_options' );
`
 * `__eddtb_cap_edit_theme_options` -- returns `'edit_theme_options'` capability -- usage:
`
add_filter( 'eddtb_filter_capability_all', '__eddtb_cap_edit_theme_options' );
`
 * `__eddtb_cap_install_plugins` -- returns `'install_plugins'` capability -- usage:
`
add_filter( 'eddtb_filter_capability_all', '__eddtb_cap_install_plugins' );
`
* Another example:
`
add_filter( 'eddtb_filter_capability_all', 'custom_eddtb_capability_all' );
/**
 * Easy Digital Downloads Toolbar: Change Main Capability
 */
function custom_eddtb_capability_all() {
	return 'activate_plugins';
}
`
--> Changes the capability to `activate_plugins`

**eddtb_filter_main_icon**

* Default value: Easy Digital Downloads default logo graphic like in its official website
* 11 Predefined helper functions for the 22 included colored/alternate icons, returning special colored icon values - the helper function always has this name: `__eddtb_colornamehere_icon()` this results in the following filters ready for usage:
`
add_filter( 'eddtb_filter_main_icon', '__eddtb_blue2_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_brown2_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_gray2_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_green2_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_khaki2_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_orange2_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_pink2_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_red2_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_turquoise2_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_yellow2_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_blue_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_brown_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_gray_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_green_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_khaki_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_orange_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_pink_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_red_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_turquoise_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_yellow_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_alternate_icon' );

add_filter( 'eddtb_filter_main_icon', '__eddtb_theme_images_icon' );
`
--> Where the 2nd to last "alternate" icon returns the same icon as in the left-hand EDD menu!

--> Where the last helper function returns the icon file (`icon-eddtb.png`) found in your current theme's/child theme's `images` subfolder

* Example for using with current theme/child theme:
`
add_filter( 'eddtb_filter_main_icon', 'custom_eddtb_main_icon' );
/**
 * Easy Digital Downloads Toolbar: Change Main Icon
 */
function custom_eddtb_main_icon() {
	return get_stylesheet_directory_uri() . '/images/custom-icon.png';
}
`
--> Uses a custom image from your active child theme's `/images/` folder

--> Recommended dimensions are 16px x 16px

**eddtb_filter_main_icon_display**

* Returning the CSS class for the main item icon
* Default value: `icon-edd` (class is: `.icon-edd`)
* 1 Predefined helper function:
 * `__eddtb_no_icon_display()` -- usage:
`
add_filter( 'eddtb_filter_main_icon_display', '__eddtb_no_icon_display' );
`
 * This way you can REMOVE the icon!
* Another example:
`
add_filter( 'eddtb_filter_main_icon_display', 'custom_eddtb_main_icon_display_class' );
/**
 * Easy Digital Downloads Toolbar: Change Main Icon CSS Class
 */
function custom_eddtb_main_icon_display_class() {
	return 'your-custom-icon-class';
}
`
--> You then have to define CSS rules in your `custom.css` file or your child theme/skin stylesheet for your own custom class `.your-custom-icon-class`

**eddtb_filter_main_item**

* Default value: "Downloads"
* Example code for your `functions.php` file or similar file:
`
add_filter( 'eddtb_filter_main_item', 'custom_eddtb_main_item' );
/**
 * Easy Digital Downloads Toolbar: Change Main Item Name
 */
function custom_eddtb_main_item() {
	return __( 'Your custom main item', 'your-theme-textdomain' );
}
`

**eddtb_filter_main_item_tooltip**

* Default value: "Easy Digital Downloads"
* Example code for your `functions.php` file or similar file:
`
add_filter( 'eddtb_filter_main_item_tooltip', 'custom_eddtb_main_item_tooltip' );
/**
 * Easy Digital Downloads Toolbar: Change Main Item Name's Tooltip
 */
function custom_eddtb_main_item_tooltip() {
	return __( 'Your custom main item tooltip', 'your-theme-textdomain' );
}
`

**eddtb_filter_edd_name**

* Default value: "EDD"
* Used for some items within toolbar links to enable proper branding
* Change things like in the other examples/principles shown above

**eddtb_filter_edd_name_tooltip**

* Default value: "Easy Digital Downloads"
* Used for some items within toolbar links to enable proper branding
* Change things like in the other examples/principles shown above

**Final note:** If you don't like to add your customizations to your `functions.php` file or similar file of theme/child theme/skin you can also add them to a functionality plugin or an mu-plugin. This way you can also use this better for Multisite environments. In general you are then more independent from theme/child theme changes etc.

= How can I hook in additional menu items from a plugin or theme? =
This is possible! Plugin and theme developers could use the included hooks to add stuff to the main items or resource group items section. The hooks are:

* `eddtb_custom_main_items` for the main section
* `eddtb_custom_group_items` for the resource group section
* Example code:
`
add_action( 'eddtb_custom_main_items', 'eddtb_add_custom_main_items' );
/**
 * Easy Digital Downloads Toolbar: Add Custom Main Items
 */
function eddtb_add_custom_main_items() {
	// Your custom stuff here, you might only use the WP Toolbar / Admin Bar API here!
}
`

**Final note:** I DON'T recommend to add customization code snippets to your theme's/ child theme's `functions.php` file! **Please use a functionality plugin or an MU-plugin instead!** This way you are then more independent from theme/ child theme changes etc. If you don't know how to create such a plugin yourself just use one of my recommended 'Code Snippets' plugins. Read & bookmark these Sites:

* [**"What is a functionality plugin and how to create one?"**](http://wpcandy.com/teaches/how-to-create-a-functionality-plugin) - *blog post by WPCandy*
* [**"Creating a custom functions plugin for end users"**](http://justintadlock.com/archives/2011/02/02/creating-a-custom-functions-plugin-for-end-users) - *blog post by Justin Tadlock*
* DON'T hack your `functions.php` file: [Resource One](http://thomasgriffinmedia.com/custom-snippets-plugin/) - [Resource Two](http://thomasgriffinmedia.com/blog/2012/09/calling-on-the-wordpress-community/) *(both by Thomas Griffin Media)*
* [**"Code Snippets"** plugin by Shea Bunge](http://wordpress.org/extend/plugins/code-snippets/) - also network wide!
* [**"Code With WP Code Snippets"** plugin by Thomas Griffin](https://github.com/thomasgriffin/CWWP-Custom-Snippets) - Note: Plugin currently in development at GitHub.
* [**"Toolbox Modules"** plugin by Sergej Müller](http://wordpress.org/extend/plugins/toolbox/) - see also his [plugin instructions](http://playground.ebiene.de/toolbox-wordpress-plugin/).

All the custom, branding and developer stuff code above can also be found as a Gist on GitHub: https://gist.github.com/2392882 (you can also add your questions/ feedback there :)

== Screenshots ==

1. Easy Digital Downloads Toolbar in action - primary level ([Click here for larger version of screenshot](https://www.dropbox.com/s/uxbaj6wp8zszaux/screenshot-1.png))
2. Easy Digital Downloads Toolbar in action - a second level - add new download ([Click here for larger version of screenshot](https://www.dropbox.com/s/oqcf68fnz2j1q6u/screenshot-2.png))
3. Easy Digital Downloads Toolbar in action - a second level - settings: payment gateways ([Click here for larger version of screenshot](https://www.dropbox.com/s/er1a5z5n5i2k98h/screenshot-3.png))
4. Easy Digital Downloads Toolbar in action - a third level - resources: support forums ([Click here for larger version of screenshot](https://www.dropbox.com/s/0ol73zw9udpjetm/screenshot-4.png))
5. Easy Digital Downloads Toolbar in action - a third level - resources: documentation ([Click here for larger version of screenshot](https://www.dropbox.com/s/ugshtpqy5ayt0pa/screenshot-5.png))
6. Easy Digital Downloads Toolbar in action - a secondary level - resources: plugin headquarters & stuff ([Click here for larger version of screenshot](https://www.dropbox.com/s/j5q9g6px690h8c3/screenshot-6.png))
7. Easy Digital Downloads Toolbar in action - language specific links at the bottom - for German locales ([Click here for larger version of screenshot](https://www.dropbox.com/s/dvkxaks5ac4f32q/screenshot-7.png))
8. Easy Digital Downloads Toolbar in action - additional plugin settings under "Misc" in EDD settings - new option since plugin version 1.1+ ([Click here for larger version of screenshot](https://www.dropbox.com/s/1ps3woox10g7suh/screenshot-8.png))

== Changelog ==

= 1.5.1 (2013-02-15) =
* NEW: Added full support for newly added role & capability system within EDD v1.4.4+ -- this is now fully reflected within this toolbar plugin. However, it may not be fully backwards compatible for user roles other than "administrator". Therefore, another reason to always use the *latest versions* of EDD, this toolbar plugin and of course, WordPress itself! :)
* UPDATE: Added new filter "Revoked" for "Payment History" items.
* NEW: Added support for third-party Add-On "EDD Download Info" (free, by Sami Keijonen).
* UPDATE: Updated all existing translations plus the .pot file for all translators!

= 1.5.0 (2013-02-06) =
* NEW: Improved settings links support - added "Filter" sub links for *Payments*, *Coupons*, *Reports* plus a few add-ons.
* NEW: Added link items for Version Info/ About, plus Contributors.
* NEW: Added support for official "Easy Digital Downloads CSV Import" extension (free).
* UPDATE: Extended support for official "EDD Software Licensing" extension (premium).
* UPDATE: Extended support for official "EDD Commissions" extension (premium).
* CODE: Minor code/documentation updates & improvements.
* UPDATE: Improved help tab display together with EDD.
* UPDATE: Updated all existing translations plus the .pot file for all translators!

= 1.4.4 (2012-12-10) =
* *Maintenance release*
* BUGFIX: Fixed another three PHP notices introduced with v1.4.0. - Thanks to Joost de Valk for reporting :).
* *Sorry guys for the many updates...!*

= 1.4.3 (2012-12-08) =
* *Maintenance release*
* BUGFIX: Fixed two PHP notices introduced with v1.4.0. - Thanks to Pippin Williamson for reporting :).

= 1.4.2 (2012-11-22) =
* *Maintenance release*
* UPDATE: Updated "Features" resource link.
* NEW: Added new resources links for "Affiliates", "Account" plus "Purchase History".
* UPDATE: Updated all existing translations plus the .pot file for all translators!

= 1.4.1 (2012-11-22) =
* *Maintenance release*
* BUGFIX: Fixed a syntax error. Sorry guys!

= 1.4.0 (2012-11-22) =
* UPDATE: Updated the toolbar icon to the latest Easy Digital Downloads logo. Created the same 10 more color variants from it like for the former logo (the old ones are still there and usable!).
* NEW: Added support for official "EDD Commissions" extension (premium).
* NEW: Added support for official "EDD Software Licensing" extension (premium).
* NEW: Added support for official "EDD Manual Purchases" extension (premium).
* NEW: Added support for third-party extension "EDD Slider" (premium).
* NEW: Added settings links for 'Reports > Export' as well as for new 'Taxes' module.
* UPDATE: Updated all resources links to latest state. Also added a few new resource links for Support & Documentation.
* UPDATE: Updated detection of 'Download' post type to work properly with change to prefixed ID of 'edd_download' -- by keeping full backwards compatibility for 'download'!
* UPDATE: Improved and extended help tab system.
* CODE: Minor code/documentation updates & improvements.
* NEW: Added new user-submitted Spanish translations.
* UPDATE: Updated German translations and also the .pot file for all translators.
* UPDATE: Initiated new three digits versioning, starting with this version.
* UPDATE: Moved screenshots to 'assets' folder in WP.org SVN to reduce plugin package size.

= 1.3.0 (2012-05-17) =
* NEW: Added more official resources links (support & docs).
* UPDATE: Improved behavior of constants for removing sections (or all), so that setting to "FALSE" removes stuff, and setting to "TRUE" displays stuff. (This does not affect existing behavior as explained in the FAQ but introduces ability to use the boolean "TRUE" to bring stuff back in favor of removing the code lines - great for testing purposes etc.)
* UPDATE: Updated readme.txt file info, links & documentation.
* UPDATE: Updated German translations and also the .pot file for all translators.
* NEW: Easy plugin translation platform with GlotPress tool: [Translate "Easy Digital Downloads Toolbar"...](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/edd-toolbar)

= 1.2.0 (2012-05-01) =
* NEW: Added new official "Styles" and "Add Ons" settings links, plus a new Documentation resource link.
* UPDATE: Moved all admin-only functions/code from main file to extra admin file which only loads within 'wp-admin', this way it's all  performance-improved! :)
* NEW: Added two action hooks for hooking custom menu items in -- `eddtb_custom_main_items` for the main section and `eddtb_custom_group_items` for the resource group section (See FAQ section here for more info on that).
* BUGFIX: Translation display only for non-English locales fixed.
* UPDATE: Updated and improved documentation of readme.txt file here.
* UPDATE: Updated German translations and also the .pot file for all translators.
* UPDATE: Extended GPL License info in readme.txt as well as main plugin file.

= 1.1.0 (2012-04-22) =
* NEW: Added two options to the "Easy Digital Downloads > Settings > Misc" page: to easily remove the Resources & Languages/Translations resources items from the toolbar menu per checkbox option.
* NEW: Added link to "Downloads Archives" on frontend like: `http://yoursite.com/downloads/` to get quick access to your downlods listing. Note, also supports user-defined slug via `EDD_SLUG` constant (since EDD v1.0.3)!
* NEW: Added "Translations" sub-forum link to resources.
* NEW: Added more Documentation and Developer API resource links.
* NEW: Added link to free extensions in the official plugin repository.
* UPDATE: Restructured Support & Documentation resource links a bit for easier access.
* UPDATE: Minor code & documentation tweaks and improvements.
* UPDATE: Updated two existing screenshots and added one new (plugin settings).
* UPDATE: Updated/corrected readme.txt file.
* UPDATE: Updated German translations and also the .pot file for all translators!
* NEW: Added banner image on WordPress.org for better plugin branding :)

= 1.0.0 (2012-04-15) =
* Initial release

== Upgrade Notice ==

= 1.5.1 =
Several additions & improvements: Fully reflects EDD's user roles/capabilities that came with EDD 1.4.4+. Extended Add-On support. Updated .pot file for translators plus all translations.

= 1.5.0 =
Several additions & improvements: Added Extensions support; expanded lots of settings links; some code tweaks & improvements. Updated .pot file for translators plus all translations.

= 1.4.4 =
Maintenance release: Fixed another 3 PHP notices. Updated all existing translations plus the .pot file for all translators.

= 1.4.3 =
Maintenance release: Fixed 2 PHP notices. Updated all existing translations plus the .pot file for all translators.

= 1.4.2 =
Maintenance release: Added new resources links, updated existing. Updated all existing translations plus the .pot file for all translators.

= 1.4.1 =
Several additions & improvements: Added Extensions support; updated resources links. Plus: added partial Spanish translations; some code tweaks & improvements. Updated .pot file for translators plus German translations.

= 1.4.0 =
Several additions & improvements: Added Extensions support; updated resources links. Plus: added partial Spanish translations; some code tweaks & improvements. Updated .pot file for translators plus German translations.

= 1.3.0 =
Several additions & improvements: Added new resources links. Some code tweaks & improvements. Updated .pot file for translators plus German translations.

= 1.2.0 =
Several changes & improvements: Performance/Code improvements. Added new settings links, also two new action hooks for custom items. Updated .pot file for translators plus German translations.

= 1.1.0 =
Several changes & improvements: Added plugin options to "Misc" settings in EDD. Added more resource links, and restructered some. Updated documentation, screenshots, readme as well as .pot plus German translations.

= 1.0.0 =
Just released into the wild.

== Plugin Links ==
* [Translations (GlotPress)](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/edd-toolbar)
* [User support forums](http://wordpress.org/support/plugin/edd-toolbar)
* [Code snippets archive for customizing, GitHub Gist](https://gist.github.com/2392882)

== Donate ==
Enjoy using *Easy Digital Downloads Toolbar*? Please consider [making a small donation](http://genesisthemes.de/en/donate/) to support the project's continued development.

== Translations ==

* English - default, always included
* German (de_DE): Deutsch - immer dabei! [Download auch via deckerweb.de](http://deckerweb.de/material/sprachdateien/easy-digital-downloads-plugin/#edd-toolbar)
* Spanish (es_ES): Español - user-submitted
* For custom and update-secure language files please upload them to `/wp-content/languages/edd-toolbar/` (just create this folder) - This enables you to use fully custom translations that won't be overridden on plugin updates. Also, complete custom English wording is possible with that, just use a language file like `edd-toolbar-en_US.mo/.po` to achieve that (for creating one see the following tools).

**Easy plugin translation platform with GlotPress tool:** [**Translate "Easy Digital Downloads Toolbar"...**](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/edd-toolbar)

*Note:* All my plugins are internationalized/ translateable by default. This is very important for all users worldwide. So please contribute your language to the plugin to make it even more useful. For translating I recommend the awesome ["Codestyling Localization" plugin](http://wordpress.org/extend/plugins/codestyling-localization/) and for validating the ["Poedit Editor"](http://www.poedit.net/), which works fine on Windows, Mac and Linux.

== Currently Supported Extensions (Add-Ons) ==
* Software Licenses (premium, by Pippin Williamson)
* Commissions (premium, by Pippin Williamson)
* Manual Purchases (premium, by Pippin Williamson)
* EDD Slider (premium, by Captain Theme)
* Easy Digital Downloads CSV Import (free, by Daniel Espinoza & Pippin Williamson)

== Additional Info ==
**Idea Behind / Philosophy:** Just a little leightweight plugin for all the Easy Digital Downloads plugin users out there to make their daily web admin life a bit easier. I'll try to add some plugin/extension support if it makes some sense in the future. So stay tuned :).

== Credits ==
**I owe huge thanks to Pippin Williamson from *PippinsPlugins.com* who had the original idea for this plugin.** He was absolutely helpful and supportive when creating this plugin. Just check out his awesome stuff and support his great work for WordPress!