<?php
/**
 * Display links to active EDD plugins/extensions settings' pages
 *
 * @package    Easy Digital Downloads Toolbar
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2012-2013, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/edd-toolbar/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.4.0
 */

/**
 * Add-On: Software Licenses (premium, by Pippin Williamson)
 *
 * @since 1.4.0
 */
if ( defined( 'EDD_SL_PLUGIN_URL' ) && current_user_can( 'manage_options' ) ) {

	/** Entries at "Add-Ons" section */
	$menu_items['eddaosoftwarelicenses'] = array(
		'parent' => $eddaddons,
		'title'  => __( 'Software Licenses', 'edd-toolbar' ),
		'href'   => ( defined( 'EDD_PLUGIN_FILE' ) && EDD_PLUGIN_FILE ) ? admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-licenses' ) : false,
		'meta'   => array( 'target' => '', 'title' => __( 'Software Licenses', 'edd-toolbar' ) )
	);

		$menu_items['eddaosoftwarelicenses-active'] = array(
			'parent' => $eddaosoftwarelicenses,
			'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Active', 'edd-toolbar' ) ),
			'href'   => ( defined( 'EDD_PLUGIN_FILE' ) && EDD_PLUGIN_FILE ) ? admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-licenses&view=active' ) : false,
			'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Active', 'edd-toolbar' ) ) )
		);

		$menu_items['eddaosoftwarelicenses-inactive'] = array(
			'parent' => $eddaosoftwarelicenses,
			'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Inactive', 'edd-toolbar' ) ),
			'href'   => ( defined( 'EDD_PLUGIN_FILE' ) && EDD_PLUGIN_FILE ) ? admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-licenses&view=inactive' ) : false,
			'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Inactive', 'edd-toolbar' ) ) )
		);

		$menu_items['eddaosoftwarelicenses-expired'] = array(
			'parent' => $eddaosoftwarelicenses,
			'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Expired', 'edd-toolbar' ) ),
			'href'   => ( defined( 'EDD_PLUGIN_FILE' ) && EDD_PLUGIN_FILE ) ? admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-licenses&view=expired' ) : false,
			'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Expired', 'edd-toolbar' ) ) )
		);

}  // end-if Software Licenses


/**
 * Add-On: Commissions (premium, by Pippin Williamson)
 *
 * @since 1.4.0
 */
if ( defined( 'EDDC_PLUGIN_URL' ) && current_user_can( 'manage_options' ) ) {

	/** Entries at "Add-Ons" section */
	$menu_items['eddaocommissions'] = array(
		'parent' => $eddaddons,
		'title'  => __( 'Commissions', 'edd-toolbar' ),
		'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-commissions' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Commissions', 'edd-toolbar' ) )
	);

		$menu_items['eddaocommissions-unpaid'] = array(
			'parent' => $eddaocommissions,
			'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Unpaid', 'edd-toolbar' ) ),
			'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-commissions&view=unpaid' ),
			'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Unpaid', 'edd-toolbar' ) ) )
		);

		$menu_items['eddaocommissions-paid'] = array(
			'parent' => $eddaocommissions,
			'title'  => sprintf( EDDTB_FILTER_STRING, __( 'Paid', 'edd-toolbar' ) ),
			'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-commissions&view=paid' ),
			'meta'   => array( 'target' => '', 'title' => sprintf( EDDTB_FILTER_STRING, __( 'Paid', 'edd-toolbar' ) ) )
		);

	/** Entry at "Reports" section */
	$menu_items['eddearnings-commissions'] = array(
		'parent' => $eddearnings,
		'title'  => __( 'Commissions', 'edd-toolbar' ),
		'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-reports&view=commissions' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Commissions', 'edd-toolbar' ) )
	);

}  // end-if Commissions


/**
 * Add-On: Manual Purchases (premium, by Pippin Williamson)
 *
 * @since 1.4.0
 */
if ( class_exists( 'EDD_Manual_Purchases' ) && current_user_can( 'manage_options' ) ) {

	/** Entry at "Add-Ons" section */
	$menu_items['eddao-manualpayment'] = array(
		'parent' => $eddpayments,
		'title'  => __( 'Create Payment', 'edd-toolbar' ),
		'href'   => admin_url( 'options.php?page=edd-manual-purchase' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Create Payment - via Manual Purchases Add-On', 'edd-toolbar' ) )
	);

}  // end-if Manual Purchases


/**
 * Add-On: EDD Slider (premium, by Captain Theme)
 *
 * @since 1.4.0
 */
if ( defined( 'EDDSLIDER_PLUGIN_DIR' ) && ( current_user_can( 'manage_options' ) || current_user_can( 'administrator' ) ) ) {

	/** Entry at "Add-Ons" section */
	$menu_items['eddao-eddslider'] = array(
		'parent' => $eddaddons,
		'title'  => __( 'EDD Slider', 'edd-toolbar' ),
		'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=eddslider_options' ),
		'meta'   => array( 'target' => '', 'title' => __( 'EDD Slider', 'edd-toolbar' ) )
	);

}  // end-if EDD Slider


/**
 * Add-On: Easy Digital Downloads CSV Import (free, by Daniel Espinoza & Pippin Williamson)
 *
 * @since 1.5.0
 */
if ( defined( 'EDD_CSVIMPORT_FILE' ) && current_user_can( 'manage_options' ) ) {

	/** Entry at "Add-Ons" section */
	$menu_items['eddao-csvimport'] = array(
		'parent' => $eddaddons,
		'title'  => __( 'CSV Product Import', 'edd-toolbar' ),
		'href'   => admin_url( 'edit.php?post_type=' . $eddtb_download_cpt . '&page=edd-csv-import' ),
		'meta'   => array( 'target' => '', 'title' => __( 'CSV Product Import', 'edd-toolbar' ) )
	);

}  // end-if Easy Digital Downloads CSV Import


/**
 * Add-On: EDD Download Info (free, by Sami Keijonen)
 *
 * @since 1.5.1
 */
if ( class_exists( 'EDD_Download_Info' )
	&& ( current_user_can( 'manage_product_terms' ) || current_user_can( 'manage_download_terms' ) )
) {

	/** Entry at "Taxonomies" section */
	$menu_items['edddownloads-features-ao'] = array(
		'parent' => $edddownloads,
		'title'  => _x( 'Features', 'Translators: Taxonomy name', 'edd-toolbar' ),
		'href'   => admin_url( 'edit-tags.php?taxonomy=edd_download_info_feature&post_type=' . $eddtb_download_cpt . '' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'Download Features', 'Translators: Taxonomy name tooltip', 'edd-toolbar' ) )
	);

}  // end-if EDD Download Info