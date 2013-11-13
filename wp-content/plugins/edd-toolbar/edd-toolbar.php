<?php 
/**
 * Main plugin file.
 * This plugin adds useful admin links and resources for the Easy Digital Downloads plugin to the WordPress Toolbar / Admin Bar.
 *
 * @package   Easy Digital Downloads Toolbar
 * @author    David Decker
 * @link      http://deckerweb.de/twitter
 * @copyright Copyright (c) 2012-2013, David Decker - DECKERWEB
 *
 * Plugin Name: Easy Digital Downloads Toolbar
 * Plugin URI: http://genesisthemes.de/en/wp-plugins/edd-toolbar/
 * Description: This plugin adds useful admin links and resources for the Easy Digital Downloads plugin to the WordPress Toolbar / Admin Bar.
 * Version: 1.5.1
 * Author: David Decker - DECKERWEB
 * Author URI: http://deckerweb.de/
 * License: GPL-2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: edd-toolbar
 * Domain Path: /languages/
 *
 * Copyright (c) 2012-2013 David Decker - DECKERWEB
 *
 *     This file is part of Easy Digital Downloads Toolbar,
 *     a plugin for WordPress.
 *
 *     Easy Digital Downloads Toolbar is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 2 of the License, or (at your option)
 *     any later version.
 *
 *     Easy Digital Downloads Toolbar is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Setting constants & some variables
 *
 * @since 1.0.0
 */
/** Plugin directory */
define( 'EDDTB_PLUGIN_DIR', dirname( __FILE__ ) );

/** Plugin base directory */
define( 'EDDTB_PLUGIN_BASEDIR', dirname( plugin_basename( __FILE__ ) ) );


add_action( 'init', 'ddw_eddtb_init' );
/**
 * Load the text domain for translation of the plugin.
 * Load admin helper functions - only within 'wp-admin'.
 * 
 * @since 1.0.0
 */
function ddw_eddtb_init() {

	/** Set filter for WordPress languages directory */
	$eddtb_wp_lang_dir = EDDTB_PLUGIN_BASEDIR . '/../../languages/edd-toolbar/';
	$eddtb_wp_lang_dir = apply_filters( 'eddtb_filter_wp_lang_dir', $eddtb_wp_lang_dir );

	/** Set filter for plugin's languages directory */
	$eddtb_lang_dir = EDDTB_PLUGIN_BASEDIR . '/languages/';
	$eddtb_lang_dir = apply_filters( 'eddtb_filter_lang_dir', $eddtb_lang_dir );

	/** First look in WordPress' "languages" folder = custom & update-secure! */
	load_plugin_textdomain( 'edd-toolbar', false, $eddtb_wp_lang_dir );

	/** Then look in plugin's "languages" folder = default */
	load_plugin_textdomain( 'edd-toolbar', false, $eddtb_lang_dir );

	/** Include admin helper functions */
	if ( is_admin() ) {
		require_once( EDDTB_PLUGIN_DIR . '/includes/eddtb-admin.php' );
	}

	/** Add "Misc Settings Page" link to plugin page */
	if ( is_admin() && current_user_can( 'manage_options' ) ) {
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) , 'ddw_eddtb_settings_page_link' );
	}

	/** Define constants and set defaults for removing all or certain sections */
	if ( ! defined( 'EDDTB_DISPLAY' ) ) {
		define( 'EDDTB_DISPLAY', TRUE );
	}

	if ( ! defined( 'EDDTB_ADDONS_DISPLAY' ) ) {
		define( 'EDDTB_ADDONS_DISPLAY', TRUE );
	}

	if ( ! defined( 'EDDTB_RESOURCES_DISPLAY' ) ) {
		define( 'EDDTB_RESOURCES_DISPLAY', TRUE );
	}

	if ( ! defined( 'EDDTB_DE_DISPLAY' ) ) {
		define( 'EDDTB_DE_DISPLAY', TRUE );
	}

	if ( ! defined( 'EDDTB_TRANSLATIONS_DISPLAY' ) ) {
		define( 'EDDTB_TRANSLATIONS_DISPLAY', TRUE );
	}

	if ( ! defined( 'EDDTB_FILTER_STRING' ) ) {
		define( 'EDDTB_FILTER_STRING', __( 'Filter: %s', 'edd-toolbar' ) );
	}

}  // end of function ddw_eddtb_init


/**
 * Returns current plugin's header data in a flexible way.
 *
 * @since  1.4.0
 *
 * @uses   get_plugins()
 *
 * @param  $eddtb_plugin_value
 * @param  $eddtb_plugin_folder
 * @param  $eddtb_plugin_file
 *
 * @return string Plugin data.
 */
function ddw_eddtb_plugin_get_data( $eddtb_plugin_value ) {

	if ( ! function_exists( 'get_plugins' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	$eddtb_plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$eddtb_plugin_file = basename( ( __FILE__ ) );

	return $eddtb_plugin_folder[ $eddtb_plugin_file ][ $eddtb_plugin_value ];

}  // end of function ddw_eddtb_plugin_get_data


add_action( 'admin_bar_menu', 'ddw_eddtb_toolbar_menu', 98 );
/**
 * Add new menu items to the WordPress Toolbar / Admin Bar.
 * 
 * @since  1.0.0
 *
 * @global mixed $wp_admin_bar, $locale, $edd_options, $eddtb_edd_name, $eddtb_edd_name_tooltip
 */
function ddw_eddtb_toolbar_menu() {

	global $wp_admin_bar, $locale, $edd_options, $eddtb_edd_name, $eddtb_edd_name_tooltip;

	/** Get the proper 'Download' post type ID/tag */
	if ( post_type_exists( 'edd_download' ) ) {
		$eddtb_download_cpt = 'edd_download';
	} elseif ( post_type_exists( 'download' ) ) {
		$eddtb_download_cpt = 'download';
	} else {
		$eddtb_download_cpt = '';
	}


	/** EDD version number for later use */
	$edd_version_number = defined( 'EDD_VERSION' ) ? 'v' . EDD_VERSION : '';


	/** Resources links EDD settings check*/
	$eddtb_resources_check = 'default';

	if ( ( ! isset( $edd_options['eddtb_remove_resources'] ) && ! isset( $edd_options['eddtb_remove_translation_resources'] ) )
		|| ( isset( $edd_options['eddtb_remove_resources'] ) && ! isset( $edd_options['eddtb_remove_translation_resources'] ) )
		|| ( ! isset( $edd_options['eddtb_remove_resources'] ) && isset( $edd_options['eddtb_remove_translation_resources'] ) )
		
	) {

		$eddtb_resources_check = 'eddtb_resources_yes';

	}  // end-if resources settings check


	/**
	 * Allows for filtering the general user role/capability to display main & sub-level items
	 *
	 * Default capability: 'edit_posts' (we need this for the "Downloads" post type set by EDD itself!)
	 *
	 * @since 1.0.0
	 */
	$eddtb_cap_default = class_exists( 'EDD_Roles' ) ? 'edit_products' : 'edit_posts';
	$eddtb_filter_capability = apply_filters( 'eddtb_filter_capability_all', $eddtb_cap_default );

	/**
	 * Required WordPress cabability to display new toolbar / admin bar entry
	 * Only showing items if toolbar / admin bar is activated and user is logged in!
	 *
	 * @since 1.0.0
	 */
	if ( ! is_user_logged_in()
		|| ! is_admin_bar_showing()
		|| ! current_user_can( esc_attr( $eddtb_filter_capability ) )	// allows for custom filtering the required role/capability
		|| ! EDDTB_DISPLAY	// allows for custom disabling
	) {
		return;
	}


	/** Set unique prefix */
	$prefix = 'ddw-edd-';
	
	/** Create parent menu item references */
	$eddbar = $prefix . 'admin-bar';				// root level
		$eddsupport = $prefix . 'eddsupport';				// sub level: edd support
			$eddsupportsections = $prefix . 'eddsupportsections';		// third level: support sections
			$eddsupportaccount = $prefix . 'eddsupportaccount';		// third level: support user account
		$edddocs = $prefix . 'edddocs';					// sub level: edd docs
			$edddocsquick = $prefix . 'edddocsquick';			// third level: docs quick links
			$edddocssections = $prefix . 'edddocssections';			// third level: docs sections
		$eddsites = $prefix . 'eddsites';				// sub level: edd sites
			$eddfeatures = $prefix . 'eddfeatures';				// third level: edd features
			$eddsitesaccount = $prefix . 'eddsitesaccount';			// third level: edd account
			$eddsitesextensions = $prefix . 'eddsitesextensions';		// third level: edd extensions
		$edddownloads = $prefix . 'edddownloads';			// sub level: edd downloads
		$eddpayments = $prefix . 'eddpayments';				// sub level: edd payments
		$edddiscounts = $prefix . 'edddiscounts';			// sub level: edd discounts
		$eddreports = $prefix . 'eddreports';				// sub level: edd reports
			$eddearnings = $prefix . 'eddearnings';				// sub level: edd earnings (group)
		$eddsettings = $prefix . 'eddsettings';				// sub level: edd settings
			$eddaddons = $prefix . 'eddaddons';				// third level: edd add-ons
				$eddaosoftwarelicenses = $prefix . 'eddaosoftwarelicenses';	// third level: edd sl add-on
				$eddaocommissions = $prefix . 'eddaocommissions';	// third level: edd commissions add-on
				$eddaddonsinfo = $prefix . 'eddaddonsinfo';		// third level: edd add-ons info
			$eddsettingsother = $prefix . 'eddsettingsother';	// third level: edd misc. settings
			$eddspecials = $prefix . 'eddspecials';				// third level: edd specials (under settings)
			$eddversioninfo = $prefix . 'eddversioninfo';		// third level: edd version info (about)
		$eddgroup = $prefix . 'eddgroup';				// sub level: edd group (resources)


	/** Make the "EDD" name filterable within menu items */
	$eddtb_edd_name = apply_filters( 'eddtb_filter_edd_name', __( 'EDD', 'edd-toolbar' ) );

	/** Make the "Easy Digital Downloads" name's tooltip filterable within menu items */
	$eddtb_edd_name_tooltip = apply_filters( 'eddtb_filter_edd_name_tooltip', _x( 'Easy Digital Downloads', 'Translators: For the tooltip', 'edd-toolbar' ) );


	/** "Sub Forum" string */
	$eddtb_sub_forum = __( 'Sub Forum', 'edd-toolbar' ) . ': ';

	/** For the Documentation search */
	$eddtb_search_docs = __( 'Search Docs', 'edd-toolbar' );
	$eddtb_go_button = '<input type="submit" value="' . __( 'GO', 'edd-toolbar' ) . '" class="eddtb-search-go"  /></form>';

	/** Display these items also when Easy Digital Downloads plugin is not installed */
	if ( EDDTB_RESOURCES_DISPLAY && ! isset( $edd_options['eddtb_remove_resources'] ) ) {

		/** Include plugin file with resources links */
		require_once( EDDTB_PLUGIN_DIR . '/includes/eddtb-resources.php' );

	}  // end-if constant check for displaying resources


	/** Display language specific links only for these locales: de_DE, de_AT, de_CH, de_LU */
	if ( EDDTB_DE_DISPLAY && ! isset( $edd_options['eddtb_remove_translation_resources'] ) && ( get_locale() == 'de_DE' || get_locale() == 'de_AT' || get_locale() == 'de_CH' || get_locale() == 'de_LU' ) ) {

		/** German EDD language packs */
		$eddgroup_menu_items['languages-de'] = array(
			'parent' => $eddgroup,
			'title'  => __( 'German language files', 'edd-toolbar' ),
			'href'   => 'http://deckerweb.de/material/sprachdateien/easy-digital-downloads-plugin/',
			'meta'   => array( 'title' => __( 'German language files for Easy Digital Downloads Plugin and more EDD extensions', 'edd-toolbar' ) )
		);

	}  // end-if German locales


	/** Translate EDD section - only display for non-English locales */
	if ( EDDTB_TRANSLATIONS_DISPLAY && ! isset( $edd_options['eddtb_remove_translation_resources'] ) && ( empty( $locale )
		|| !( get_locale() == 'en_US' || get_locale() == 'en_GB' || get_locale() == 'en_NZ' || get_locale() == 'en' ) )
	) {

		/** Translations Forum */
		$eddgroup_menu_items['translations-forum'] = array(
			'parent' => $eddgroup,
			'title'  => __( 'Translations Forum', 'edd-toolbar' ),
			'href'   => 'https://easydigitaldownloads.com/support/forum/translations/',
			'meta'   => array( 'title' => sprintf( _x( 'Translations Forum (%s Support Forum)', 'Translators: For the tooltip', 'edd-toolbar' ), $eddtb_edd_name ) )
		);

	}  // end-if translate EDD


	/** Show these items only if Easy Digital Downloads plugin is actually installed */
	if ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) || defined( 'EDD_PLUGIN_FILE' )
	) {

		/** Set EDD active variable for later user */
		$edd_active = 'edd_is_active';

		/** EDD main downloads section */
		if ( current_user_can( 'edit_posts' )
			|| current_user_can( 'edit_products' )
			|| current_user_can( 'edit_product' )
		) {

			$menu_items['edddownloads'] = array(
				'parent' => $eddbar,
				'title'  => __( 'All Downloads', 'edd-toolbar' ),
				'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '' ),
				'meta'   => array( 'target' => '', 'title' => __( 'All Downloads', 'edd-toolbar' ) )
			);

			$menu_items['edddownloads-add'] = array(
				'parent' => $edddownloads,
				'title'  => __( 'Add new Download', 'edd-toolbar' ),
				'href'   => admin_url( 'post-new.php?post_type=' . $eddtb_download_cpt . '' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Add new Download', 'edd-toolbar' ) )
			);

			/** EDD CPT Taxonomies Items */
			$eddtb_tax = get_post_type_object( 'download' );  // $eddtb_tax->cap->manage_product_terms

			if ( current_user_can( 'manage_product_terms' ) || current_user_can( 'manage_download_terms' ) ) {

				$menu_items['edddownloads-categories'] = array(
					'parent' => $edddownloads,
					'title'  => __( 'Categories', 'edd-toolbar' ),
					'href'   => admin_url( 'edit-tags.php?taxonomy=download_category&post_type=' . $eddtb_download_cpt . '' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Download Categories', 'edd-toolbar' ) )
				);

				$menu_items['edddownloads-tags'] = array(
					'parent' => $edddownloads,
					'title'  => __( 'Tags', 'edd-toolbar' ),
					'href'   => admin_url( 'edit-tags.php?taxonomy=download_tag&post_type=' . $eddtb_download_cpt . '' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Download Tags', 'edd-toolbar' ) )
				);

			}  // end-if EDD Tax cap check

//

		} // end-if EDD CPT cap checks

		/** EDD admin settings sections */
		if ( current_user_can( 'manage_options' )
			|| current_user_can( 'manage_shop_settings' )
			|| current_user_can( 'view_shop_reports' )
			|| current_user_can( 'manage_shop_discounts' )
		) {

			/** Payment History */
			if ( current_user_can( 'view_shop_reports' ) ) {

				$menu_items['eddpayments'] = array(
					'parent' => $eddbar,
					'title'  => __( 'Payment History', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-payment-history' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Payment History', 'edd-toolbar' ) )
				);

					$menu_items['eddpayments-completed'] = array(
						'parent' => $eddpayments,
						'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Completed', 'edd-toolbar' ) ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-payment-history&status=publish' ),
						'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Completed', 'edd-toolbar' ) ) )
					);

					$menu_items['eddpayments-pending'] = array(
						'parent' => $eddpayments,
						'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Pending', 'edd-toolbar' ) ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-payment-history&status=pending' ),
						'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Pending', 'edd-toolbar' ) ) )
					);

					$menu_items['eddpayments-refunded'] = array(
						'parent' => $eddpayments,
						'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Refunded', 'edd-toolbar' ) ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-payment-history&status=refunded' ),
						'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Refunded', 'edd-toolbar' ) ) )
					);

					$menu_items['eddpayments-revoked'] = array(
						'parent' => $eddpayments,
						'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Revoked', 'edd-toolbar' ) ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-payment-history&status=revoked' ),
						'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Revoked', 'edd-toolbar' ) ) )
					);

					$menu_items['eddpayments-failed'] = array(
						'parent' => $eddpayments,
						'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Failed', 'edd-toolbar' ) ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-payment-history&status=failed' ),
						'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Failed', 'edd-toolbar' ) ) )
					);

			}  // end-if ? cap check

			/** Discount Codes Items */
			if ( current_user_can( 'manage_shop_discounts' ) ) {

				$menu_items['edddiscounts'] = array(
					'parent' => $eddbar,
					'title'  => __( 'Discount Codes', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-discounts' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Discount Codes', 'edd-toolbar' ) )
				);

					$menu_items['edddiscounts-filter-active'] = array(
						'parent' => $edddiscounts,
						'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Active', 'edd-toolbar' ) ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-discounts&status=active' ),
						'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Active', 'edd-toolbar' ) ) )
					);

					$menu_items['edddiscounts-filter-inactive'] = array(
						'parent' => $edddiscounts,
						'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Inactive', 'edd-toolbar' ) ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-discounts&status=inactive' ),
						'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Inactive', 'edd-toolbar' ) ) )
					);

			}  // end-if discounts cap check

			/** Reports Items */
			if ( current_user_can( 'view_shop_reports' ) ) {

				$menu_items['eddreports'] = array(
					'parent' => $eddbar,
					'title'  => __( 'Reports', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-reports' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Reports', 'edd-toolbar' ) )
				);

					$menu_items['eddearnings-earnings'] = array(
						'parent' => $eddearnings,
						'title'  => __( 'Earnings', 'edd-toolbar' ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-reports&view=earnings' ),
						'meta'   => array( 'target' => '', 'title' => __( 'Earnings', 'edd-toolbar' ) )
					);

					$menu_items['eddearnings-downloads'] = array(
						'parent' => $eddearnings,
						'title'  => __( 'Downloads', 'edd-toolbar' ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-reports&view=downloads' ),
						'meta'   => array( 'target' => '', 'title' => __( 'Downloads', 'edd-toolbar' ) )
					);

					$menu_items['eddearnings-customers'] = array(
						'parent' => $eddearnings,
						'title'  => __( 'Customers', 'edd-toolbar' ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-reports&view=customers' ),
						'meta'   => array( 'target' => '', 'title' => __( 'Customers', 'edd-toolbar' ) )
					);

					$menu_items['eddearnings-taxes'] = array(
						'parent' => $eddearnings,
						'title'  => __( 'Taxes', 'edd-toolbar' ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-reports&view=taxes' ),
						'meta'   => array( 'target' => '', 'title' => __( 'Taxes', 'edd-toolbar' ) )
					);

					$menu_items['eddreports-export'] = array(
						'parent' => $eddreports,
						'title'  => __( 'PDF &amp; CSV Export', 'edd-toolbar' ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-reports&tab=export' ),
						'meta'   => array( 'target' => '', 'title' => __( 'PDF &amp; CSV Export', 'edd-toolbar' ) )
					);

					$menu_items['eddreports-logs'] = array(
						'parent' => $eddreports,
						'title'  => __( 'View Logs', 'edd-toolbar' ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-reports&tab=logs' ),
						'meta'   => array( 'target' => '', 'title' => __( 'View Logs', 'edd-toolbar' ) )
					);

			}  // end-if reports cap check

			/** Settings Items */
			if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_shop_settings' ) ) {

				$menu_items['eddsettings'] = array(
					'parent' => $eddbar,
					'title'  => __( 'Settings', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-settings' ),
					'meta'   => array( 'target' => '', 'title' => _x( 'General Settings', 'Translators: For the tooltip', 'edd-toolbar' ) )
				);

				$menu_items['eddsettings-general'] = array(
					'parent' => $eddsettings,
					'title'  => __( 'General', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-settings&tab=general' ),
					'meta'   => array( 'target' => '', 'title' => _x( 'General Settings', 'Translators: For the tooltip', 'edd-toolbar' ) )
				);

				$menu_items['eddsettings-paymentgateways'] = array(
					'parent' => $eddsettings,
					'title'  => __( 'Payment Gateways', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-settings&tab=gateways' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Payment Gateways', 'edd-toolbar' ) )
				);

				$menu_items['eddsettings-emails'] = array(
					'parent' => $eddsettings,
					'title'  => __( 'Emails', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-settings&tab=emails' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Emails', 'edd-toolbar' ) )
				);

				$menu_items['eddsettings-styles'] = array(
					'parent' => $eddsettings,
					'title'  => __( 'Styles', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-settings&tab=styles' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Styles', 'edd-toolbar' ) )
				);

				/** Taxes Settings, since EDD v1.3.3+ */
				if ( function_exists( 'edd_use_taxes' ) ) {
					$menu_items['eddsettings-styles'] = array(
						'parent' => $eddsettings,
						'title'  => __( 'Taxes', 'edd-toolbar' ),
						'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-settings&tab=taxes' ),
						'meta'   => array( 'target' => '', 'title' => __( 'Taxes', 'edd-toolbar' ) )
					);
				}  // end-if taxes check

				$menu_items['eddsettingsother'] = array(
					'parent' => $eddsettings,
					'title'  => __( 'Misc.', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-settings&tab=misc' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Misc.', 'edd-toolbar' ) )
				);

				$menu_items['eddsettingsother-toolbar'] = array(
					'parent' => $eddsettingsother,
					'title'  => __( 'Toolbar', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-settings&tab=misc#edd-toolbar' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Toolbar', 'edd-toolbar' ) )
				);

				$menu_items['eddversioninfo'] = array(
					'parent' => $eddspecials,
					'title'  => $edd_version_number . ' ' . __( 'Version Info (About)', 'edd-toolbar' ),
					'href'   => admin_url( 'index.php?page=edd-about' ),
					'meta'   => array( 'target' => '', 'title' => $edd_version_number . ' ' . __( 'Version Info (About)', 'edd-toolbar' ) )
				);

					$menu_items['eddversioninfo-credits'] = array(
						'parent' => $eddversioninfo,
						'title'  => __( 'Contributors', 'edd-toolbar' ),
						'href'   => admin_url( 'index.php?page=edd-credits' ),
						'meta'   => array( 'target' => '', 'title' => __( 'Contributors', 'edd-toolbar' ) )
					);

				$menu_items['edd-systeminfo'] = array(
					'parent' => $eddspecials,
					'title'  => __( 'System Info (Debug)', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-system-info' ),
					'meta'   => array( 'target' => '', 'title' => __( 'System Info (Debug)', 'edd-toolbar' ) )
				);

			}  // end-if settings cap check

			/** Add Ons/ Extenstions Items */
			if ( EDDTB_ADDONS_DISPLAY
				&& ( current_user_can( 'manage_options' ) || current_user_can( 'manage_shop_settings' ) )
			) {

				$menu_items['eddaddons'] = array(
					'parent' => $eddbar,
					'title'  => __( 'Add Ons', 'edd-toolbar' ),
					'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-addons' ),
					'meta'   => array( 'target' => '', 'title' => sprintf( _x( 'Add Ons - Extend %s', 'Translators: For the tooltip', 'edd-toolbar' ), $eddtb_edd_name_tooltip ) )
				);

				$menu_items['eddaddons-more'] = array(
					'parent' => $eddaddonsinfo,
					'title'  => __( 'Buy more Add Ons', 'edd-toolbar' ),
					'href'   => 'http://ddwb.me/6m',
					'meta'   => array( 'title' => sprintf( _x( 'Buy more Add Ons - Extend %s', 'Translators: For the tooltip', 'edd-toolbar' ), $eddtb_edd_name_tooltip ) )
				);

			}  // end if Add-Ons constant & cap checks

		} // end-if global settings/options cap checks (including backward compatibility)

	} else {

		/** If Easy Digital Downloads is not active, to avoid PHP notices */
		if ( 'eddtb_resources_yes' == $eddtb_resources_check && $eddgroup_menu_items ) {
			$menu_items = $eddgroup_menu_items;
		}

		/** If Easy Digital Downloads is not active and no icon filter is active, then display no icon */
		if ( ! has_filter( 'eddtb_filter_main_icon' ) ) {
			add_filter( 'eddtb_filter_main_item_icon_display', '__eddtb_no_icon_display' );
		}

	}  // end-if EDD conditional


	/**
	 * Display links to active EDD plugins/extensions settings' pages
	 *
	 * @since 1.4.0
	 */
		/** Include plugin file with plugin support links */
		require_once( EDDTB_PLUGIN_DIR . '/includes/eddtb-plugins.php' );


	/**
	 * Display links to frontend "Downloads Archive" page.
	 *    Hook in later, so Add-On items are displayed before... :)
	 *
	 * @since 1.0.0
	 */
	/** Get the current EDD "downloads" slug */
	$eddtb_downloads_slug = defined( 'EDD_SLUG' ) ? EDD_SLUG : 'downloads';

	$menu_items['edddownloads-dlarchives'] = array(
		'parent' => $edddownloads,
		'title'  => __( 'Visit Downloads Archives', 'edd-toolbar' ),
		'href'   => get_home_url() . '/' . $eddtb_downloads_slug . '/',
		'meta'   => array( 'target' => '', 'title' => _x( 'Visit Downloads Archives on Website Frontend', 'Translators: For the tooltip', 'edd-toolbar' ) )
	);


	/** Allow menu items to be filtered, but pass in parent menu item IDs */
	$menu_items = (array) apply_filters( 'ddw_eddtb_menu_items', $menu_items,
									( 'eddtb_resources_yes' == $eddtb_resources_check ) ? $eddgroup_menu_items : '',
									$prefix,
									$eddbar,
										$eddsupport,
										$eddsupportsections,
										$eddsupportaccount,
									$edddocs,
										$edddocsquick,
										$edddocssections,
									$eddsites,
										$eddfeatures,
										$eddsitesaccount,
										$eddsitesextensions,
									$eddpayments,
									$edddiscounts,
									$eddreports,
										$eddearnings,
									$eddsettings,
										$eddaddons,
										$eddaosoftwarelicenses,
										$eddaocommissions,
										$eddaddonsinfo,
										$eddsettingsother,
										$eddspecials,
										$eddversioninfo,
									$eddgroup
	);  // end of array


	/**
	 * Add the Easy Digital Downloads top-level menu item
	 *
	 * @since 1.0.0
	 *
	 * @param $eddtb_main_item_title
	 * @param $eddtb_main_item_title_tooltip
	 * @param $eddtb_main_item_icon_display
	 */
		/** Filter the main item name */
		$eddtb_main_item_title = apply_filters( 'eddtb_filter_main_item', __( 'Downloads', 'edd-toolbar' ) );

		/** Filter the main item name's tooltip */
		$eddtb_main_item_title_tooltip = apply_filters( 'eddtb_filter_main_item_tooltip', _x( 'Easy Digital Downloads', 'Translators: For the tooltip', 'edd-toolbar' ) );

		/** Filter the main item icon's class/display */
		$eddtb_main_item_icon_display = apply_filters( 'eddtb_filter_main_item_icon_display', 'icon-edd' );

		$wp_admin_bar->add_menu( array(
			'id'    => $eddbar,
			'title' => $eddtb_main_item_title,
			'href'  => ( defined( 'EDD_PLUGIN_FILE' ) && EDD_PLUGIN_FILE ) ? admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '' ) : false,
			'meta'  => array( 'class' => $eddtb_main_item_icon_display, 'title' => $eddtb_main_item_title_tooltip )
		) );


	/** EDD Reports - Earning etc. Items */
	$wp_admin_bar->add_group( array(
		'parent' => $eddreports,
		'id'     => $eddearnings
	) );


	/** Loop through the menu items */
	foreach ( $menu_items as $id => $menu_item ) {
		
		/** Add in the item ID */
		$menu_item['id'] = $prefix . $id;

		/** Add meta target to each item where it's not already set, so links open in new window/tab */
		if ( ! isset( $menu_item['meta']['target'] ) )		
			$menu_item['meta']['target'] = '_blank';

		/** Add class to links that open up in a new window/tab */
		if ( '_blank' === $menu_item['meta']['target'] ) {
			if ( ! isset( $menu_item['meta']['class'] ) )
				$menu_item['meta']['class'] = '';
			$menu_item['meta']['class'] .= $prefix . 'eddtb-new-tab';
		}

		/** Add menu items */
		$wp_admin_bar->add_menu( $menu_item );

	}  // end foreach menu items


	/**
	 * Action Hook 'eddtb_custom_main_items'
	 * allows for hooking other main items in
	 *
	 * @since 1.2.0
	 */
	do_action( 'eddtb_custom_main_items' );


	/** EDD Add-On Special Items Sub-Group under "Settings" */
	$wp_admin_bar->add_group( array(
		'parent' => $eddsettings,
		'id'     => $eddspecials
	) );


	/** EDD Add-On Special Group: Main Entry */
	$wp_admin_bar->add_group( array(
		'parent' => $eddaddons,
		'id'     => $eddaddonsinfo
	) );


	/** EDD Group: Main Entry */
	$wp_admin_bar->add_group( array(
		'parent' => $eddbar,
		'id'     => $eddgroup,
		'meta'   => array( 'class' => 'ab-sub-secondary' )
	) );


	/** EDD Group: Loop through the group menu items */
	if ( 'eddtb_resources_yes' == $eddtb_resources_check ) {

		foreach ( $eddgroup_menu_items as $id => $eddgroup_menu_item ) {
		
			/** EDD Group: Add in the item ID */
			$eddgroup_menu_item['id'] = $prefix . $id;

			/** EDD Group: Add meta target to each item where it's not already set, so links open in new window/tab */
			if ( ! isset( $eddgroup_menu_item['meta']['target'] ) )		
				$eddgroup_menu_item['meta']['target'] = '_blank';

			/** EDD Group: Add class to links that open up in a new window/tab */
			if ( '_blank' === $eddgroup_menu_item['meta']['target'] ) {

				if ( ! isset( $eddgroup_menu_item['meta']['class'] ) ) {
					$eddgroup_menu_item['meta']['class'] = '';
				}

				$eddgroup_menu_item['meta']['class'] .= $prefix . 'eddtb-new-tab';

			}

			/** EDD Group: Add menu items */
			$wp_admin_bar->add_menu( $eddgroup_menu_item );

		}  // end foreach EDD Group

	}  // end-if resource links check


	/**
	 * Action Hook 'eddtb_custom_group_items'
	 * allows for hooking other EDD Group items in
	 *
	 * @since 1.2.0
	 */
	do_action( 'eddtb_custom_group_items' );

	/**
	 * Add plugin "About" & "Credits" items to WordPress Logo area (as sub items).
	 *
	 * @since 1.5.0
	 */ 
	if ( defined( 'EDD_VERSION' ) && current_user_can( 'manage_options' ) ) {

		$wp_admin_bar->add_menu( array(
			'id'     => 'plugin-edd-about',
			'parent' => 'wp-logo-default',
			'title'  => __( 'Plugin: Easy Digital Downloads', 'edd-toolbar' ),
			'href'   => admin_url( 'index.php?page=edd-about' ),
			'meta'   => array( 'title' => _x( 'About Easy Digital Downloads - Version Info', 'Translators: for the tooltip', 'edd-toolbar' ) . ' ' . $edd_version_number )
		) );

		$wp_admin_bar->add_menu( array(
			'id'     => 'plugin-edd-credits',
			'parent' => 'plugin-edd-about',
			'title'  => __( 'Contributors', 'edd-toolbar' ),
			'href'   => admin_url( 'index.php?page=edd-credits' ),
			'meta'   => array( 'title' => __( 'Contributors', 'edd-toolbar' ) )
		) );

	}  // end-if EDD active check

}  // end of main function


add_action( 'wp_head', 'ddw_eddtb_admin_style' );
add_action( 'admin_head', 'ddw_eddtb_admin_style' );
/**
 * Add the styles for new WordPress Toolbar / Admin Bar entry
 * 
 * @since 1.0.0
 *
 * @param $eddtb_main_icon
 */
function ddw_eddtb_admin_style() {

	/** No styles if admin bar is disabled or user is not logged in or items are disabled via constant */
	if ( ! is_admin_bar_showing()
		|| ! is_user_logged_in()
		|| ! EDDTB_DISPLAY
	) {
		return;
	}

	/** Add CSS styles to wp_head/admin_head */
	$eddtb_main_icon = apply_filters( 'eddtb_filter_main_icon', plugins_url( 'edd-toolbar/images/icon-eddtb2.png',
dirname( __FILE__ ) ) );

	?>
	<style type="text/css">
		#wpadminbar.nojs .ab-top-menu > li.menupop.icon-edd:hover > .ab-item,
		#wpadminbar .ab-top-menu > li.menupop.icon-edd.hover > .ab-item,
		#wpadminbar.nojs .ab-top-menu > li.menupop.icon-edd > .ab-item,
		#wpadminbar .ab-top-menu > li.menupop.icon-edd > .ab-item {
      			background-image: url(<?php echo esc_url_raw( $eddtb_main_icon ); ?>);
			background-repeat: no-repeat;
			background-position: 0.85em 50%;
			padding-left: 30px;
		}
		#wp-admin-bar-ddw-edd-languages-de > .ab-item:before,
		#wp-admin-bar-ddw-edd-translations-forum > .ab-item:before {
			color: #ff9900;
			content: '• ';
		}
		#wpadminbar .eddtb-search-input {
			width: 140px;
		}
		#wp-admin-bar-ddw-edd-eddsupportsections .ab-item,
		#wp-admin-bar-ddw-edd-edddocsquick .ab-item,
		#wp-admin-bar-ddw-edd-edddocssections .ab-item,
		#wpadminbar .eddtb-search-input,
		#wpadminbar .eddtb-search-go {
			color: #21759b !important;
			text-shadow: none;
		}
		#wpadminbar .eddtb-search-input,
		#wpadminbar .eddtb-search-go {
			background-color: #fff;
			height: 18px;
			line-height: 18px;
			padding: 1px 4px;
		}
		#wpadminbar .eddtb-search-go {
			-webkit-border-radius: 11px;
			   -moz-border-radius: 11px;
			        border-radius: 11px;
			font-size: 0.67em;
			margin: 0 0 0 2px;
		}
	</style>
	<?php

}  // end of function ddw_eddtb_admin_style


/**
 * Helper functions for custom branding of the plugin
 *
 * @since 1.0.0
 */
	/** Include plugin file with special custom stuff */
	require_once( EDDTB_PLUGIN_DIR . '/includes/eddtb-branding.php' );