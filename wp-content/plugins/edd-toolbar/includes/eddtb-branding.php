<?php
/**
 * Helper functions for custom branding & capabilities
 *
 * @package    Easy Digital Downloads Toolbar
 * @subpackage Branding
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2012-2013, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/edd-toolbar/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.0.0
 */

/**
 * Helper functions for returning a few popular roles/capabilities.
 *
 * @since  1.0.0
 *
 * @return role/capability
 */
	/**
	 * Helper function for returning 'administrator' role/capability.
	 *
	 * @since  1.0.0
	 *
	 * @return 'administrator' role
	 */
	function __eddtb_admin_only() {

		return 'administrator';
	}

	/**
	 * Helper function for returning 'editor' role/capability.
	 *
	 * @since  1.0.0
	 *
	 * @return 'editor' role
	 */
	function __eddtb_role_editor() {

		return 'editor';
	}

	/**
	 * Helper function for returning 'shop_manager' role/capability.
	 *
	 * @since  1.5.1
	 *
	 * @return 'shop_manager' role
	 */
	function __eddtb_role_shop_manager() {

		return 'shop_manager';
	}

	/**
	 * Helper function for returning 'shop_accountant' role/capability.
	 *
	 * @since  1.5.1
	 *
	 * @return 'shop_accountant' role
	 */
	function __eddtb_role_shop_accountant() {

		return 'shop_accountant';
	}

	/**
	 * Helper function for returning 'shop_worker' role/capability.
	 *
	 * @since  1.5.1
	 *
	 * @return 'shop_worker' role
	 */
	function __eddtb_role_shop_worker() {

		return 'shop_worker';
	}

	/**
	 * Helper function for returning 'shop_vendor' role/capability.
	 *
	 * @since  1.5.1
	 *
	 * @return 'shop_vendor' role
	 */
	function __eddtb_role_shop_vendor() {

		return 'shop_vendor';
	}
	
	/**
	 * Helper function for returning 'manage_options' capability.
	 *
	 * @since  1.0.0
	 *
	 * @return 'manage_options' capability
	 */
	function __eddtb_cap_manage_options() {

		return 'manage_options';
	}

	/**
	 * Helper function for returning 'edit_theme_options' capability.
	 *
	 * @since  1.0.0
	 *
	 * @return 'edit_theme_options' capability
	 */
	function __eddtb_cap_edit_theme_options() {

		return 'edit_theme_options';
	}

	/**
	 * Helper function for returning 'install_plugins' capability.
	 *
	 * @since  1.0.0
	 *
	 * @return 'install_plugins' capability
	 */
	function __eddtb_cap_install_plugins() {

		return 'install_plugins';
	}

/** End of role/capability helper functions */


/**
 * Helper functions for returning colored icons.
 *
 * @since  1.0.0
 *
 * @return colored icon image
 */
	/**
	 * Helper function for returning the blue icon.
	 *
	 * @since  1.0.0
	 *
	 * @return blue icon
	 */
	function __eddtb_blue_icon() {

		return plugins_url( 'images/icon-eddtb-blue.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the brown icon.
	 *
	 * @since  1.0.0
	 *
	 * @return brown icon
	 */
	function __eddtb_brown_icon() {

		return plugins_url( 'images/icon-eddtb-brown.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the gray icon.
	 *
	 * @since  1.0.0
	 *
	 * @return gray icon
	 */
	function __eddtb_gray_icon() {

		return plugins_url( 'images/icon-eddtb-gray.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the green icon.
	 *
	 * @since  1.0.0
	 *
	 * @return green icon
	 */
	function __eddtb_green_icon() {

		return plugins_url( 'images/icon-eddtb-green.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the khaki icon.
	 *
	 * @since  1.0.0
	 *
	 * @return khaki icon
	 */
	function __eddtb_khaki_icon() {

		return plugins_url( 'images/icon-eddtb-khaki.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the orange icon.
	 *
	 * @since  1.0.0
	 *
	 * @return orange icon
	 */
	function __eddtb_orange_icon() {

		return plugins_url( 'images/icon-eddtb-orange.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the pink icon.
	 *
	 * @since  1.0.0
	 *
	 * @return pink icon
	 */
	function __eddtb_pink_icon() {

		return plugins_url( 'images/icon-eddtb-pink.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the red icon.
	 *
	 * @since  1.0.0
	 *
	 * @return red icon
	 */
	function __eddtb_red_icon() {

		return plugins_url( 'images/icon-eddtb-red.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the turquoise icon.
	 *
	 * @since  1.0.0
	 *
	 * @return turquoise icon
	 */
	function __eddtb_turquoise_icon() {

		return plugins_url( 'images/icon-eddtb-turquoise.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the yellow icon.
	 *
	 * @since  1.0.0
	 *
	 * @return yellow icon
	 */
	function __eddtb_yellow_icon() {

		return plugins_url( 'images/icon-eddtb-yellow.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the blue2 icon.
	 *
	 * @since  1.4.0
	 *
	 * @return blue2 icon
	 */
	function __eddtb_blue2_icon() {

		return plugins_url( 'images/icon-eddtb2-blue.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the brown2 icon.
	 *
	 * @since  1.4.0
	 *
	 * @return brown2 icon
	 */
	function __eddtb_brown2_icon() {

		return plugins_url( 'images/icon-eddtb2-brown.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the gray2 icon.
	 *
	 * @since  1.4.0
	 *
	 * @return gray2 icon
	 */
	function __eddtb_gray2_icon() {

		return plugins_url( 'images/icon-eddtb2-gray.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the green2 icon.
	 *
	 * @since  1.4.0
	 *
	 * @return green2 icon
	 */
	function __eddtb_green2_icon() {

		return plugins_url( 'images/icon-eddtb2-green.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the khaki2 icon.
	 *
	 * @since  1.4.0
	 *
	 * @return khaki2 icon
	 */
	function __eddtb_khaki2_icon() {

		return plugins_url( 'images/icon-eddtb2-khaki.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the orange2 icon.
	 *
	 * @since  1.4.0
	 *
	 * @return orange2 icon
	 */
	function __eddtb_orange2_icon() {

		return plugins_url( 'images/icon-eddtb2-orange.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the pink2 icon.
	 *
	 * @since  1.4.0
	 *
	 * @return pink2 icon
	 */
	function __eddtb_pink2_icon() {

		return plugins_url( 'images/icon-eddtb2-pink.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the red2 icon.
	 *
	 * @since  1.4.0
	 *
	 * @return red2 icon
	 */
	function __eddtb_red2_icon() {

		return plugins_url( 'images/icon-eddtb2-red.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the turquoise2 icon.
	 *
	 * @since  1.4.0
	 *
	 * @return turquoise2 icon
	 */
	function __eddtb_turquoise2_icon() {

		return plugins_url( 'images/icon-eddtb2-turquoise.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the yellow2 icon.
	 *
	 * @since  1.4.0
	 *
	 * @return yellow2 icon
	 */
	function __eddtb_yellow2_icon() {

		return plugins_url( 'images/icon-eddtb2-yellow.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the alternate (left menu) icon.
	 *
	 * @since  1.0.0
	 *
	 * @return alternate icon
	 */
	function __eddtb_alternate_icon() {

		return plugins_url( 'images/icon-eddtb-alternate.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning a custom icon (icon-eddtb.png) from stylesheet/theme "images" folder.
	 *
	 * @since  1.0.0
	 *
	 * @return eddtb custom icon
	 */
	function __eddtb_theme_images_icon() {

		return get_stylesheet_directory_uri() . '/images/icon-eddtb.png';
	}

/** End of icon helper functions */


/**
 * Helper functions for returning icon class.
 *
 * @since  1.0.0
 *
 * @return icon class
 */
	/**
	 * Helper function for returning no icon class.
	 *
	 * @since  1.0.0
	 *
	 * @return int 0
	 */
	function __eddtb_no_icon_display() {

		return NULL;
	}

/** End of icon class helper functions */