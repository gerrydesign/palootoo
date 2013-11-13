<?php
/**
 * Display the resource links for the "Easy Digital Downloads Group".
 *
 * @package    Easy Digital Downloads Toolbar
 * @subpackage Resources
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2012-2013, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/edd-toolbar/
 * @link       http://deckerweb.de/twitter
 *
 * @since 1.0.0
 */

/**
 * Resource links collection
 *
 * @since 1.0.0
 */
/** Support menu items */
$eddgroup_menu_items['eddsupport'] = array(
	'parent' => $eddgroup,
	'title'  => __( 'Support Forum', 'edd-toolbar' ),
	'href'   => 'https://easydigitaldownloads.com/support/',
	'meta'   => array( 'title' => $eddtb_edd_name_tooltip . ' ' . __( 'Support Forum', 'edd-toolbar' ) )
);

$eddgroup_menu_items['eddsupport-sub-basicsupport'] = array(
	'parent' => $eddsupport,
	'title'  => __( 'Basic Support', 'edd-toolbar' ),
	'href'   => 'https://easydigitaldownloads.com/support/forum/questions-about-the-plugin/',
	'meta'   => array( 'title' => $eddtb_sub_forum . _x( 'Basic Support', 'Translators: For the tooltip', 'edd-toolbar' ) )
);

/** Support: Sections */
$eddgroup_menu_items['eddsupportsections'] = array(
	'parent' => $eddsupport,
	'title'  => __( 'Sections', 'edd-toolbar' ),
	'href'   => false,
	'meta'   => array( 'title' => __( 'Sections', 'edd-toolbar' ) . ' &raquo;' )
);

	$eddgroup_menu_items['eddsupportsections-sub-prioritysupport'] = array(
		'parent' => $eddsupportsections,
		'title'  => __( 'Priority Support', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/support/forum/general-support/',
		'meta'   => array( 'title' => $eddtb_sub_forum . _x( 'Priority Support', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['eddsupportsections-sub-devapi'] = array(
		'parent' => $eddsupportsections,
		'title'  => __( 'Developer\'s API', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/support/forum/developers-api/',
		'meta'   => array( 'title' => $eddtb_sub_forum . _x( 'Developer\'s API', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['eddsupportsections-sub-addons'] = array(
		'parent' => $eddsupportsections,
		'title'  => __( 'Add-On Plugins', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/support/forum/add-on-plugins/',
		'meta'   => array( 'title' => $eddtb_sub_forum . _x( 'Add-On Plugins', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['eddsupportsections-sub-requests'] = array(
		'parent' => $eddsupportsections,
		'title'  => __( 'Feature Requests', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/support/forum/feature-requests/',
		'meta'   => array( 'title' => $eddtb_sub_forum . _x( 'Feature Requests', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['eddsupportsections-sub-themes'] = array(
		'parent' => $eddsupportsections,
		'title'  => __( 'Themes', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/support/forum/official-themes/',
		'meta'   => array( 'title' => $eddtb_sub_forum . _x( 'Themes', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['eddsupportsections-sub-tconflicts'] = array(
		'parent' => $eddsupportsections,
		'title'  => __( 'Theme Conflicts', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/support/forum/theme-conflicts/',
		'meta'   => array( 'title' => $eddtb_sub_forum . _x( 'Theme Conflicts', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['eddsupportsections-sub-pconflicts'] = array(
		'parent' => $eddsupportsections,
		'title'  => __( 'Plugin Conflicts', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/support/forum/plugin-conflicts/',
		'meta'   => array( 'title' => $eddtb_sub_forum . _x( 'Plugin Conflicts', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['eddsupportsections-sub-translations'] = array(
		'parent' => $eddsupportsections,
		'title'  => __( 'Translations', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/support/forum/translations/',
		'meta'   => array( 'title' => $eddtb_sub_forum . _x( 'Translations', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['eddsupportsections-sub-betatesters'] = array(
		'parent' => $eddsupportsections,
		'title'  => __( 'Beta Testers', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/support/forum/beta-testers/',
		'meta'   => array( 'title' => $eddtb_sub_forum . _x( 'Beta Testers', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

/** Support: User account */
$eddgroup_menu_items['eddsupportaccount'] = array(
	'parent' => $eddsupport,
	'title'  => __( 'User Account at Support Site', 'edd-toolbar' ),
	'href'   => 'https://easydigitaldownloads.com/your-account/',
	'meta'   => array( 'title' => _x( 'User Account at Support Site', 'Translators: For the tooltip', 'edd-toolbar' ) )
);

	$eddgroup_menu_items['eddsupportaccount-login'] = array(
		'parent' => $eddsupportaccount,
		'title'  => __( 'Login', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/your-account/login/',
		'meta'   => array( 'title' => _x( 'Login', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

$eddgroup_menu_items['eddsupport-premium'] = array(
	'parent' => $eddsupport,
	'title'  => __( 'Premium Support', 'edd-toolbar' ),
	'href'   => 'https://easydigitaldownloads.com/support/pricing/',
	'meta'   => array( 'title' => _x( 'Register for Premium Support Account', 'Translators: For the tooltip', 'edd-toolbar' ) )
);

$eddgroup_menu_items['eddsupport-bugsgithub'] = array(
	'parent' => $eddsupport,
	'title'  => __( 'Bug Tracker at GitHub', 'edd-toolbar' ),
	'href'   => 'https://github.com/pippinsplugins/Easy-Digital-Downloads/issues',
	'meta'   => array( 'title' => __( 'Bug Tracker at GitHub', 'edd-toolbar' ) )
);

/** Documentation menu items */
$eddgroup_menu_items['edddocs'] = array(
	'parent' => $eddgroup,
	'title'  => __( 'Documentation', 'edd-toolbar' ),
	'href'   => 'http://easydigitaldownloads.com/documentation/',
	'meta'   => array( 'title' => __( 'Documentation', 'edd-toolbar' ) )
);

	$eddgroup_menu_items['edddocs-faq'] = array(
		'parent' => $edddocs,
		'title'  => __( 'Frequently Asked Questions', 'edd-toolbar' ),
		'href'   => 'http://easydigitaldownloads.com/documentation/faqs/',
		'meta'   => array( 'title' => __( 'Frequently Asked Questions', 'edd-toolbar' ) )
	);

/** Documentation: Quick Links */
$eddgroup_menu_items['edddocsquick'] = array(
	'parent' => $edddocs,
	'title'  => __( 'Quick Links', 'edd-toolbar' ),
	'href'   => false,
	'meta'   => array( 'title' => __( 'Quick Links', 'edd-toolbar' ) . ' &raquo;' )
);

	$eddgroup_menu_items['edddocsquick-install'] = array(
		'parent' => $edddocsquick,
		'title'  => __( 'Installation', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/installation/',
		'meta'   => array( 'title' => __( 'Installation', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocsquick-basicconfig'] = array(
		'parent' => $edddocsquick,
		'title'  => __( 'Basic Config', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/basic-config/',
		'meta'   => array( 'title' => __( 'Basic Config', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocsquick-createdownloads'] = array(
		'parent' => $edddocsquick,
		'title'  => __( 'Create Downloads', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/create-your-first-download/',
		'meta'   => array( 'title' => __( 'Create Downloads', 'edd-toolbar' ) )
	);
	$eddgroup_menu_items['edddocsquick-cartsetup'] = array(
		'parent' => $edddocsquick,
		'title'  => __( 'Shopping Cart Setup', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/setting-up-the-shopping-cart/',
		'meta'   => array( 'title' => __( 'Shopping Cart Setup', 'edd-toolbar' ) )
	);

/** Documentation: Sections */
$eddgroup_menu_items['edddocssections'] = array(
	'parent' => $edddocs,
	'title'  => __( 'Sections', 'edd-toolbar' ),
	'href'   => false,
	'meta'   => array( 'title' => __( 'Sections', 'edd-toolbar' ) . ' &raquo;' )
);

	$eddgroup_menu_items['edddocssections-install'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'Install &amp; Upgrades', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/install-upgrade/',
		'meta'   => array( 'title' => __( 'Install &amp Upgrades', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocssections-usage'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'Usage', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/usage/',
		'meta'   => array( 'title' => __( 'Usage', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocssections-shortcodes'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'Shortcodes', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/short-codes/',
		'meta'   => array( 'title' => __( 'Shortcodes', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocssections-extensions'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'Extensions', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/extensions/',
		'meta'   => array( 'title' => __( 'Usage', 'edd-toolbar' ) )
	);
	$eddgroup_menu_items['edddocssections-themes'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'Themes', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/themes/',
		'meta'   => array( 'title' => _x( 'Themes', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocssections-theming'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'Theming', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/theming/',
		'meta'   => array( 'title' => _x( 'Theming', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocssections-customization'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'Customization', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/customization/',
		'meta'   => array( 'title' => _x( 'Customization', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocssections-plugincompat'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'Plugin Compatibility', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/plugin-compatibility/',
		'meta'   => array( 'title' => _x( 'Plugin Compatibility', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocssections-devapi'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'Developer API', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/developer-api/',
		'meta'   => array( 'title' => __( 'Developer API', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocssections-hooks'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'API: Hook Reference', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/actions/',
		'meta'   => array( 'title' => __( 'API: Hook Reference', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['edddocssections-filters'] = array(
		'parent' => $edddocssections,
		'title'  => __( 'API: Filter Reference', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/docs/section/filters/',
		'meta'   => array( 'title' => __( 'API: Filter Reference', 'edd-toolbar' ) )
	);

/** Docs search form */
$eddgroup_menu_items['edddocs-searchform'] = array(
	'parent' => $eddgroup,
	'title' => '<form method="get" action="https://easydigitaldownloads.com/" class=" " target="_blank">
	<input type="text" placeholder="' . $eddtb_search_docs . '" onblur="this.value=(this.value==\'\') ? \'' . $eddtb_search_docs . '\' : this.value;" onfocus="this.value=(this.value==\'' . $eddtb_search_docs . '\') ? \'\' : this.value;" value="' . $eddtb_search_docs . '" name="s" value="' . esc_attr( 'Search Docs', 'edd-toolbar' ) . '" class="text eddtb-search-input" />
	<input type="hidden" name="post_type[]" value="docs" />
	<input type="hidden" name="post_type[]" value="page" />' . $eddtb_go_button,
	'href'   => false,
	'meta'   => array( 'target' => '', 'title' => _x( 'Search Docs', 'Translators: For the tooltip', 'edd-toolbar' ) )
);

/** Easy Digital Downloads HQ menu items */
$eddgroup_menu_items['eddsites'] = array(
	'parent' => $eddgroup,
	'title'  => $eddtb_edd_name . ' ' . __( 'Plugin HQ', 'edd-toolbar' ),
	'href'   => 'https://easydigitaldownloads.com/',
	'meta'   => array( 'title' => $eddtb_edd_name_tooltip . ' ' . __( 'Plugin HQ', 'edd-toolbar' ) )
);

/** HQ: GitHub */
$eddgroup_menu_items['eddsites-dev'] = array(
	'parent' => $eddsites,
	'title'  => __( 'GitHub Repository Developer Center', 'edd-toolbar' ),
	'href'   => 'https://github.com/pippinsplugins/Easy-Digital-Downloads',
	'meta'   => array( 'title' => __( 'GitHub Repository Developer Center', 'edd-toolbar' ) )
);

/** HQ: Blog */
$eddgroup_menu_items['eddsites-blog'] = array(
	'parent' => $eddsites,
	'title'  => __( 'Official Blog', 'edd-toolbar' ),
	'href'   => 'https://easydigitaldownloads.com/blog/',
	'meta'   => array( 'title' => __( 'Official Blog', 'edd-toolbar' ) )
);

/** HQ: Features */
$eddgroup_menu_items['eddfeatures'] = array(
	'parent' => $eddsites,
	'title'  => __( 'Features', 'edd-toolbar' ),
	'href'   => 'https://easydigitaldownloads.com/edd-features/',
	'meta'   => array( 'title' => __( 'Features', 'edd-toolbar' ) )
);

	$eddgroup_menu_items['eddfeatures-screens'] = array(
		'parent' => $eddfeatures,
		'title'  => __( 'Screenshots', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/screenshots/',
		'meta'   => array( 'title' => __( 'Screenshots', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['eddfeatures-showcase'] = array(
		'parent' => $eddfeatures,
		'title'  => __( 'Showcase', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/easy-digital-downloads-showcase/',
		'meta'   => array( 'title' => __( 'Showcase', 'edd-toolbar' ) )
	);

/** HQ: Site Account */
$eddgroup_menu_items['eddsitesaccount'] = array(
	'parent' => $eddsites,
	'title'  => __( 'My Account', 'edd-toolbar' ),
	'href'   => 'https://easydigitaldownloads.com/your-account/',
	'meta'   => array( 'title' => __( 'My Account', 'edd-toolbar' ) )
);

	$eddgroup_menu_items['eddsitesaccount-history'] = array(
		'parent' => $eddsitesaccount,
		'title'  => __( 'Purchase History', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/your-account/purchase-history/',
		'meta'   => array( 'title' => __( 'Purchase History', 'edd-toolbar' ) )
	);

/** HQ: Affiliates */
$eddgroup_menu_items['eddsitesaffiliates'] = array(
	'parent' => $eddsites,
	'title'  => __( 'Affiliates', 'edd-toolbar' ),
	'href'   => 'https://easydigitaldownloads.com/affiliates/',
	'meta'   => array( 'title' => __( 'Affiliates', 'edd-toolbar' ) )
);

/** HQ: Extensions */
$eddgroup_menu_items['eddsitesextensions'] = array(
	'parent' => $eddsites,
	'title'  => __( 'Extensions', 'edd-toolbar' ),
	'href'   => 'https://easydigitaldownloads.com/extensions/',
	'meta'   => array( 'title' => __( 'Extensions', 'edd-toolbar' ) )
);

	$eddgroup_menu_items['eddsitesextensions-newest'] = array(
		'parent' => $eddsitesextensions,
		'title'  => __( 'Newest Extensions', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/extensions/?display=newest',
		'meta'   => array( 'title' => __( 'Newest Extensions', 'edd-toolbar' ) )
	);

	$eddgroup_menu_items['eddsites-themes'] = array(
		'parent' => $eddsitesextensions,
		'title'  => __( 'Themes', 'edd-toolbar' ),
		'href'   => 'https://easydigitaldownloads.com/themes/',
		'meta'   => array( 'title' => sprintf( _x( 'Special %s Themes', 'Translators: For the tooltip', 'edd-toolbar' ), $eddtb_edd_name_tooltip ) )
	);

/** HQ: WP.org */
$eddgroup_menu_items['eddsites-wporg'] = array(
	'parent' => $eddsites,
	'title'  => __( 'Free plugins/extensions at WP.org', 'edd-toolbar' ),
	'href'   => 'http://wordpress.org/extend/plugins/search.php?q=easy+digital+downloads',
	'meta'   => array( 'title' => __( 'Free plugins/extensions at WP.org', 'edd-toolbar' ) )
);

/** HQ: Behind */
$eddgroup_menu_items['eddsites-pplugins'] = array(
	'parent' => $eddsites,
	'title'  => __( 'Pippins Plugins', 'edd-toolbar' ),
	'href'   => 'http://pippinsplugins.com/',
	'meta'   => array( 'title' => __( 'Pippins Plugins', 'edd-toolbar' ) )
);