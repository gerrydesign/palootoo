<?php
/**
 * Plugin Name: Easy Digital Downloads - WePay Gateway
 * Description: Process Easy Digital Downloads payments through WePay
 * Author: Pippin Williamson
 * Author URI: http3
 * Text Domain: edd-wepay
 * Domain Path: languages
 * Version: 1.0.5
 */


class EDD_WePay_Gateway {

	private $client_id;
	private $client_secret;
	private $access_token;
	private $account_id;

	function __construct() {

		// Filters
		add_filter( 'edd_payment_gateways', array( $this, 'register_gateway' ) );
		add_filter( 'edd_settings_gateways', array( $this, 'register_settings' ) );
		add_filter( 'edd_payments_table_column', array( $this, 'payment_column_data' ), 10, 3 );
		add_filter( 'edd_payment_statuses', array( $this, 'payment_status_labels' ) );
		add_filter( 'edd_payments_table_columns', array( $this, 'payments_column' ) );
		add_filter( 'edd_payments_table_views', array( $this, 'payment_status_filters' ) );

		// Actionss
		add_action( 'edd_gateway_wepay', array( $this, 'process_payment' ) );
		if( ! $this->onsite_payments() )
			add_action( 'edd_wepay_cc_form', '__return_false' );
		add_action( 'init', array( $this, 'confirm_payment' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'edd_after_cc_fields', array( $this, 'errors_div' ) );
		add_action( 'admin_notices', array( $this, 'admin_messages' ) );
		add_action( 'init',  array( $this, 'register_post_statuses' ), 110 );
		add_action( 'edd_charge_wepay_preapproval', array( $this, 'process_preapproved_charge' ) );
		add_action( 'edd_cancel_wepay_preapproval', array( $this, 'process_preapproved_cancel' ) );
		add_action( 'admin_init', array( $this, 'activate_license' ) );
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );
		add_action( 'admin_init', array( $this, 'updater' ) );

		define( 'EDD_WEPAY_STORE_API_URL', 'https://easydigitaldownloads.com' );
		define( 'EDD_WEPAY_PRODUCT_NAME', 'WePay Payment Gateway' );
		define( 'EDD_WEPAY_VERSION', '1.0.5' );
	}


	public function get_api_credentials( $payment_id = 0 ) {
		global $edd_options;

		$creds                  = array();
		$creds['client_id']     = trim( $edd_options['wepay_client_id']     );
		$creds['client_secret'] = trim( $edd_options['wepay_client_secret'] );
		$creds['access_token']  = trim( $edd_options['wepay_access_token']  );
		$creds['account_id']    = trim( $edd_options['wepay_account_id']    );

		return apply_filters( 'edd_wepay_get_api_creds', $creds, $payment_id );

	}

	public function register_gateway( $gateways ) {
		if( $this->onsite_payments() ) {
			$checkout_label = __( 'Credit Card', 'edd-wepay' );
		} else {
			$checkout_label = __( 'Credit Card or Bank Account', 'edd-wepay' );
		}
		$gateways['wepay'] = array( 'admin_label' => 'WePay', 'checkout_label' => $checkout_label );
		return $gateways;
	}

	public function process_payment( $purchase_data ) {

		global $edd_options;

		require dirname( __FILE__ ) . '/vendor/wepay.php';

		$creds = $this->get_api_credentials();

		if( edd_is_test_mode() )
			Wepay::useStaging( $creds['client_id'], $creds['client_secret'] );
		else
			Wepay::useProduction( $creds['client_id'], $creds['client_secret'] );

		$wepay = new WePay( $creds['access_token'] );

		// Purchase summary
		$summary = edd_get_purchase_summary( $purchase_data, false );

		$prefill_info = new stdClass;
		$prefill_info->name  = $purchase_data['user_info']['first_name'] . ' ' . $purchase_data['user_info']['last_name'];
		$prefill_info->email = $purchase_data['user_email'];

	    // Collect payment data
	    $payment_data = array(
	        'price'         => $purchase_data['price'],
	        'date'          => $purchase_data['date'],
	        'user_email'    => $purchase_data['user_email'],
	        'purchase_key'  => $purchase_data['purchase_key'],
	        'currency'      => edd_get_currency(),
	        'downloads'     => $purchase_data['downloads'],
	        'user_info'     => $purchase_data['user_info'],
	        'cart_details'  => $purchase_data['cart_details'],
	        'status'        => 'pending'
	     );

	    // Record the pending payment
	    $payment = edd_insert_payment( $payment_data );

    	$endpoint = isset( $edd_options['wepay_preapprove_only'] ) ? 'preapproval' : 'checkout';

	    $args = array(
			'account_id'        => $creds['account_id'],
			'amount'            => $purchase_data['price'],
			'fee_payer'         => $this->fee_payer(),
			'short_description' => stripslashes_deep( html_entity_decode( wp_strip_all_tags( $summary ), ENT_COMPAT, 'UTF-8' ) ),
			'prefill_info'      => $prefill_info,
			'reference_id'      => $purchase_data['purchase_key'],
			'fallback_uri'      => edd_get_failed_transaction_uri(),
			'redirect_uri'      => add_query_arg( 'payment-confirmation', 'wepay', get_permalink( $edd_options['success_page'] ) )
		);

		if( isset( $edd_options['wepay_preapprove_only'] ) ) {
			$args['period'] = 'once';
		} else {
			$args['type']   = $this->payment_type();
		}

		if( $this->onsite_payments() && ! empty( $_POST['edd_wepay_card'] ) ) {

			// Use a tokenized card
			$args['payment_method_id']   = $_POST['edd_wepay_card'];
			$args['payment_method_type'] = 'credit_card';
	    }

	    // Let other plugins modify the data that goes to WePay (such as Crowd Funding)
	    $args = apply_filters( 'edd_wepay_checkout_args', $args );

	    //echo '<pre>'; print_r( $args ); echo '</pre>'; exit;

		// create the checkout
		$response = $wepay->request( $endpoint . '/create', $args );

		// Get rid of cart contents

		if( $this->onsite_payments() ) {

			if( ! empty( $response->error ) ) {
				// if errors are present, send the user back to the purchase page so they can be corrected
				edd_set_error( $response->error, $response->error_description . '. Error code: ' . $response->error_code );
				edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );

			}

			$query_str = '?payment-confirmation=wepay&';

			if( isset( $edd_options['wepay_preapprove_only'] ) ) {
				$query_str .= 'preapproval_id=' . $response->preapproval_id;
			} else {
				$query_str .= 'checkout_id=' . $response->checkout_id;
			}

			edd_send_to_success_page( $query_str );
			edd_empty_cart();

		} else {

			edd_empty_cart();
			// Send to WePay terminal
			if( isset( $edd_options['wepay_preapprove_only'] ) ) {
				wp_redirect( $response->preapproval_uri ); exit;
			} else {
				wp_redirect( $response->checkout_uri ); exit;
			}

		}
	}

	public function confirm_payment() {

		global $edd_options;

		if( empty( $_GET['payment-confirmation'] ) )
			return;

		if( empty( $_GET['checkout_id'] ) && empty( $_GET['preapproval_id'] ) )
			return;

		if( 'wepay' != $_GET['payment-confirmation'] )
			return;

		require dirname( __FILE__ ) . '/vendor/wepay.php';

		$creds = $this->get_api_credentials();

		if( edd_is_test_mode() )
			Wepay::useStaging( $creds['client_id'], $creds['client_secret'] );
		else
			Wepay::useProduction( $creds['client_id'], $creds['client_secret'] );

		$wepay = new WePay( $creds['access_token'] );

		try {

			if( isset( $edd_options['wepay_preapprove_only'] ) ) {

				$preapproval_id = urldecode( $_GET['preapproval_id'] );
				$response       = $wepay->request( 'preapproval', array(
					'preapproval_id' => $preapproval_id
				) );

				if( $response->account_id != $creds['account_id'] )
					wp_die( __( 'The store ID does not match those set in the site settings.', 'edd-wepay' ), __( 'Error', 'edd-wepay' ) );

				if( $response->state != 'captured' && $response->state != 'approved' )
					wp_die( __( 'Your payment is still processing. Please refresh the page to see your purchase receipt.', 'edd-wepay' ), __( 'Error', 'edd-wepay' ) );

				$payment_id = edd_get_purchase_id_by_key( $response->reference_id );

				edd_insert_payment_note( $payment_id, sprintf( __( 'WePay Preapproval ID: %s', 'edd' ) , $response->preapproval_id ) );
				edd_update_payment_status( $payment_id, 'preapproval' );

			} else {

				$checkout_id = urldecode( $_GET['checkout_id'] );
				$response    = $wepay->request( 'checkout', array(
					'checkout_id' => $checkout_id
				) );

				if( $response->account_id != $creds['account_id'] )
					wp_die( __( 'The store ID does not match those set in the site settings.', 'edd-wepay' ), __( 'Error', 'edd-wepay' ) );

				if( $response->state != 'captured' && $response->state != 'authorized' )
					wp_die( __( 'Your payment is still processing. Please refresh the page to see your purchase receipt.', 'edd-wepay' ), __( 'Error', 'edd-wepay' ) );

				$payment_id = edd_get_purchase_id_by_key( $response->reference_id );

				edd_insert_payment_note( $payment_id, sprintf( __( 'WePay Checkout ID: %s', 'edd' ) , $response->checkout_id ) );
				edd_update_payment_status( $payment_id, 'publish' );
			}

		} catch ( Exception $e ) {

			// Show a message if there was an error of some kind

		}

	}


	public function scripts() {
		if( ! $this->onsite_payments() )
			return;

		if( edd_is_test_mode() )
			$script_url = 'https://stage.wepay.com/min/js/tokenization.v2.js';
		else
			$script_url = 'https://www.wepay.com/min/js/tokenization.v2.js';

		$creds = $this->get_api_credentials();

		wp_enqueue_script( 'wepay-tokenization', $script_url );
		wp_enqueue_script( 'wepay-gateway', plugin_dir_url( __FILE__ ) . 'wepay.js', array( 'wepay-tokenization', 'jquery' ), EDD_WEPAY_VERSION );
		wp_localize_script( 'wepay-gateway', 'wepay_js', array(
			'is_test_mode' => edd_is_test_mode() ? '1' : '0',
			'client_id'    => $creds['client_id']
		) );
	}

	/**
	 * Add an errors div
	 *
	 * @access      public
	 * @since       1.0
	 * @return      void
	 */

	function errors_div() {
		echo '<div id="edd-wepay-payment-errors"></div>';
	}


	/**
	 * Show admin notices
	 *
	 * @access      public
	 * @since       1.0
	 * @return      void
	 */

	public function admin_messages() {

		if ( isset( $_GET['edd-message'] ) && 'preapproval-charged' == $_GET['edd-message'] ) {
			 add_settings_error( 'edd-wepay-notices', 'edd-wepay-preapproval-charged', __( 'The preapproved payment was successfully charged.', 'edd-wepay' ), 'updated' );
		}
		if ( isset( $_GET['edd-message'] ) && 'preapproval-failed' == $_GET['edd-message'] ) {
			 add_settings_error( 'edd-wepay-notices', 'edd-wepay-preapproval-charged', __( 'The preapproved payment failed to be charged.', 'edd-wepay' ), 'error' );
		}
		if ( isset( $_GET['edd-message'] ) && 'preapproval-cancelled' == $_GET['edd-message'] ) {
			 add_settings_error( 'edd-wepay-notices', 'edd-wepay-preapproval-cancelled', __( 'The preapproved payment was successfully cancelled.', 'edd-wepay' ), 'updated' );
		}

		settings_errors( 'edd-wepay-notices' );
	}

	/**
	 * Trigger preapproved payment charge
	 *
	 * @since 1.0
	 * @return void
	 */
	public function process_preapproved_charge() {

		if( empty( $_GET['nonce'] ) )
			return;

		if( ! wp_verify_nonce( $_GET['nonce'], 'edd-wepay-process-preapproval' ) )
			return;

		$payment_id  = absint( $_GET['payment_id'] );
		$charge      = $this->charge_preapproved( $payment_id );

		if ( $charge ) {
			wp_redirect( add_query_arg( array( 'edd-message' => 'preapproval-charged' ), admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ) ); exit;
		} else {
			wp_redirect( add_query_arg( array( 'edd-message' => 'preapproval-failed' ), admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ) ); exit;
		}

	}


	/**
	 * Cancel a preapproved payment
	 *
	 * @since 1.0
	 * @return void
	 */
	public function process_preapproved_cancel() {
		global $edd_options;

		if( empty( $_GET['nonce'] ) )
			return;

		if( ! wp_verify_nonce( $_GET['nonce'], 'edd-wepay-process-preapproval' ) )
			return;

		require dirname( __FILE__ ) . '/vendor/wepay.php';

		$payment_id  = absint( $_GET['payment_id'] );

		if( empty( $payment_id ) )
			return;

		if ( 'preapproval' !== get_post_status( $payment_id ) )
			return;

		$creds = $this->get_api_credentials();

		if( edd_is_test_mode() )
			Wepay::useStaging( $creds['client_id'], $creds['client_secret'] );
		else
			Wepay::useProduction( $creds['client_id'], $creds['client_secret'] );

		$wepay    = new WePay( $creds['access_token'] );

		$response = $wepay->request( 'preapproval/find', array(
			'reference_id' => edd_get_payment_key( $payment_id ),
			'account_id'   => $creds['account_id']
		) );

		foreach( $response as $preapproval ) {

			$cancel = $wepay->request( 'preapproval/cancel', array(
				'preapproval_id' => $preapproval->preapproval_id
			) );

			edd_insert_payment_note( $payment_id, __( 'Preapproval cancelled', 'edd-wepay' ) );
			edd_update_payment_status( $payment_id, 'cancelled' );

		}

		wp_redirect( add_query_arg( array( 'edd-message' => 'preapproval-cancelled' ), admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ) ); exit;
	}

	/**
	 * Charge a preapproved payment
	 *
	 * @since 1.0
	 * @return bool
	 */
	function charge_preapproved( $payment_id = 0 ) {

		global $edd_options;

		if( empty( $payment_id ) )
			return false;

		require_once( dirname( __FILE__ ) . '/vendor/wepay.php' );

		$creds = $this->get_api_credentials( $payment_id );

		try {
			if( edd_is_test_mode() )
				Wepay::useStaging( $creds['client_id'], $creds['client_secret'] );
			else
				Wepay::useProduction( $creds['client_id'], $creds['client_secret'] );
		} catch (RuntimeException $e) {
			// already been setup
		}

		$wepay    = new WePay( $creds['access_token'] );

		$response = $wepay->request( 'preapproval/find', array(
			'reference_id' => edd_get_payment_key( $payment_id ),
			'account_id'   => $creds['account_id']
		) );

		foreach( $response as $preapproval ) {
			try {
				$charge = $wepay->request( 'checkout/create', array(
					'account_id'        => $creds['account_id'],
					'preapproval_id'    => $preapproval->preapproval_id,
					'type'              => $this->payment_type(),
					'fee_payer'         => $this->fee_payer(),
					'amount'            => edd_get_payment_amount( $payment_id ),
					'short_description' => sprintf( __( 'Charge of preapproved payment %s', 'edd-wepay' ), edd_get_payment_key( $payment_id ) )
				) );

				edd_insert_payment_note( $payment_id, 'WePay Checkout ID: ' . $charge->checkout_id );
				edd_update_payment_status( $payment_id, 'publish' );

				return true;
			} catch (WePayException $e) {
				edd_insert_payment_note( $payment_id, 'WePay Checkout Error: ' . $e->getMessage() );

				do_action( 'edd_wepay_charge_failed', $e );

				return false;
			}
		}
	}


	/**
	 * Register payment statuses for preapproval
	 *
	 * @since 1.0
	 * @return void
	 */
	public function register_post_statuses() {
		register_post_status( 'preapproval', array(
			'label'                     => _x( 'Preapproved', 'Preapproved payment', 'edd' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'edd' )
		) );
		register_post_status( 'cancelled', array(
			'label'                     => _x( 'Cancelled', 'Cancelled payment', 'edd' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'edd' )
		) );
	}


	/**
	 * Register our new payment status labels for EDD
	 *
	 * @since 1.0
	 * @return array
	 */
	public function payment_status_labels( $statuses ) {
		$statuses['preapproval'] = __( 'Preapproved', 'edd-wepay' );
		$statuses['cancelled']   = __( 'Cancelled', 'edd-wepay' );
		return $statuses;
	}


	/**
	 * Display the Preapprove column label
	 *
	 * @since 1.0
	 * @return array
	 */
	public function payments_column( $columns ) {

		global $edd_options;

		if ( isset( $edd_options['wepay_preapprove_only'] ) ) {
			$columns['preapproval'] = __( 'Preapproval', 'edd-wepay' );
		}
		return $columns;
	}


	/**
	 * Display the payment status filters
	 *
	 * @since 1.0
	 * @return array
	 */
	public function payment_status_filters( $views ) {
		$payment_count        = wp_count_posts( 'edd_payment' );
		$preapproval_count    = '&nbsp;<span class="count">(' . $payment_count->preapproval . ')</span>';
		$cancelled_count      = '&nbsp;<span class="count">(' . $payment_count->cancelled . ')</span>';
		$current              = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$views['preapproval'] = sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'preapproval', admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ), $current === 'preapproval' ? ' class="current"' : '', __( 'Preapproval Pending', 'edd' ) . $preapproval_count );
		$views['cancelled']   = sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'cancelled', admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ), $current === 'cancelled' ? ' class="current"' : '', __( 'Cancelled', 'edd' ) . $cancelled_count );

		return $views;
	}


	/**
	 * Show the Process / Cancel buttons for preapproved payments
	 *
	 * @since 1.0
	 * @return string
	 */
	public function payment_column_data( $value, $payment_id, $column_name ) {
		if ( $column_name == 'preapproval' ) {
			$status      = get_post_status( $payment_id );

			$nonce = wp_create_nonce( 'edd-wepay-process-preapproval' );

			$preapproval_args     = array(
				'payment_id'      => $payment_id,
				'nonce'           => $nonce,
				'edd-action'      => 'charge_wepay_preapproval'
			);
			$cancel_args          = array(
				'payment_id'      => $payment_id,
				'nonce'           => $nonce,
				'edd-action'      => 'cancel_wepay_preapproval'
			);

			if ( 'preapproval' === $status ) {
				$value = '<a href="' . add_query_arg( $preapproval_args, admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ) . '" class="button-secondary button">' . __( 'Process Payment', 'edd-wepay' ) . '</a>&nbsp;';
				$value .= '<a href="' . add_query_arg( $cancel_args, admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ) . '" class="button-secondary button">' . __( 'Cancel Preapproval', 'edd-wepay' ) . '</a>';
			}
		}
		return $value;
	}


	/**
	 * Register the gateway settings
	 *
	 * @access      public
	 * @since       1.0
	 * @return      array
	 */

	public function register_settings( $settings ) {

		$wepay_settings = apply_filters( 'edd_gateway_wepay_settings', array(
			array(
				'id'   => 'wepay_settings',
				'name'  => '<strong>' . __( 'WePay Settings', 'edd-wepay' ) . '</strong>',
				'desc'  => __( 'Configure the WePay settings', 'edd-wepay' ),
				'type'  => 'header'
			),
			array(
				'id'     => 'wepay_license_key',
				'name'   => __( 'License Key', 'edd-wepay' ),
				'desc'   => __( 'Enter your license for the WePay Payment Gateway to receive automatic upgrades', 'edd-wepay' ),
				'type'   => 'license_key',
				'options'=> array( 'is_valid_license_option' => 'edd_wepay_license_active' ),
				'size'   => 'regular'
			),
			array(
				'id'   => 'wepay_client_id',
				'name'  => __( 'Client ID', 'edd-wepay' ),
				'desc'  => __( 'Enter your client ID, found in your WePay API Account Settings', 'edd-wepay' ),
				'type'  => 'text',
				'size'  => 'regular'
			),
			array(
				'id'   => 'wepay_client_secret',
				'name'  => __( 'Client Secret', 'edd-wepay' ),
				'desc'  => __( 'Enter your Client Secret, found in your WePay API Account Settings', 'edd-wepay' ),
				'type'  => 'text',
				'size'  => 'regular'
			),
			array(
				'id'   => 'wepay_access_token',
				'name'  => __( 'Access Token', 'edd-wepay' ),
				'desc'  => __( 'Enter your Access Token, found in your WePay API Account Settings', 'edd-wepay' ),
				'type'  => 'text',
				'size'  => 'regular'
			),
			array(
				'id'   => 'wepay_account_id',
				'name'  => __( 'Account ID', 'edd-wepay' ),
				'desc'  => __( 'Enter your Account ID, found in your WePay API Account Settings', 'edd-wepay' ),
				'type'  => 'text',
				'size'  => 'regular'
			),
			array(
				'id'   => 'wepay_preapprove_only',
				'name'  => __( 'Preapprove Only?', 'edd-wepay' ),
				'desc'  => __( 'Check this if you would like to preapprove payments but not charge until a later date.', 'edd-wepay' ),
				'type'  => 'checkbox'
			),
			array(
				'id'   => 'wepay_payment_type',
				'name'  => __( 'Payment Type', 'edd-wepay' ),
				'desc'  => __( 'Select the type of payment you want to process.', 'edd-wepay' ),
				'type'  => 'select',
				'options' => array(
					'GOODS'    => __( 'Goods', 'edd-wepay' ),
					'SERVICE'  => __( 'Service', 'edd-wepay' ),
					'DONATION' => __( 'Donation', 'edd-wepay' ),
					'EVENT'    => __( 'Event', 'edd-wepay' ),
					'PERSONAL' => __( 'Personal', 'edd-wepay' ),
				)
			),
			array(
				'id'   => 'wepay_fee_payer',
				'name'  => __( 'Fee Payer', 'edd-wepay' ),
				'desc'  => __( 'Who pays the WePay fee on purchases?', 'edd-wepay' ),
				'type'  => 'radio',
				'options' => array(
					'Payee'  => __( 'Store', 'edd-wepay' ),
					'Payer'  => __( 'Customer', 'edd-wepay' )
				),
				'std' => 'Payee'
			),
			array(
				'id'   => 'wepay_onsite_payments',
				'name'  => __( 'On Site Payments', 'edd-wepay' ),
				'desc'  => __( 'Process credit cards on-site or send customers to WePay\'s terminal? On-site payments require SSL.', 'edd-wepay' ),
				'type'  => 'radio',
				'options' => array(
					'onsite'  => __( 'On-Site', 'edd-wepay' ),
					'offsite'  => __( 'Off-Site', 'edd-wepay' )
				),
				'std' => 'offsite'
			)
		) );

		return array_merge( $settings, $wepay_settings );
	}


	/**
	 * Determine the type of payment we are processing
	 *
	 * @access      public
	 * @since       1.0
	 * @return      string
	 */

	private function payment_type() {
		global $edd_options;
		$type = isset( $edd_options['wepay_payment_type'] ) ? $edd_options['wepay_payment_type'] : 'GOODS';
		return $type;
	}


	/**
	 * Who pays the fee?
	 *
	 * @access      public
	 * @since       1.0
	 * @return      string
	 */

	private function fee_payer() {
		global $edd_options;
		$payer = isset( $edd_options['wepay_fee_payer'] ) ? $edd_options['wepay_fee_payer'] : 'Payee';
		return $payer;
	}


	/**
	 * Process payments onsite or off?
	 *
	 * @access      public
	 * @since       1.0
	 * @return      string
	 */

	private function onsite_payments() {
		global $edd_options;
		return isset( $edd_options['wepay_onsite_payments'] ) && $edd_options['wepay_onsite_payments'] == 'onsite';
	}


	/**
	 * Activate the license key
	 *
	 * @access      public
	 * @since       1.0
	 * @return      void
	 */

	public function activate_license() {
		global $edd_options;

		if ( ! isset( $_POST['edd_settings_gateways'] ) )
			return;
		if ( ! isset( $_POST['edd_settings_gateways']['wepay_license_key'] ) )
			return;

		if ( get_option( 'edd_wepay_license_active' ) == 'valid' )
			return;

		$license = sanitize_text_field( $_POST['edd_settings_gateways']['wepay_license_key'] );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license'   => $license,
			'item_name' => urlencode( EDD_WEPAY_PRODUCT_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_WEPAY_STORE_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'edd_wepay_license_active', $license_data->license );

	}


	/**
	 * Deactivate the license key
	 *
	 * @access      public
	 * @since       1.0
	 * @return      void
	 */

	public function deactivate_license() {
		global $edd_options;

		// listen for our activate button to be clicked
		if( isset( $_POST['wepay_license_key_deactivate'] ) ) {

		    // run a quick security check
		    if( ! check_admin_referer( 'wepay_license_key_nonce', 'wepay_license_key_nonce' ) )
		      return; // get out if we didn't click the Activate button

		    // retrieve the license from the database
		    $license = trim( $edd_options['wepay_license_key'] );

		    // data to send in our API request
		    $api_params = array(
		      'edd_action'=> 'deactivate_license',
		      'license'   => $license,
		      'item_name' => urlencode( EDD_WEPAY_PRODUCT_NAME ) // the name of our product in EDD
		    );

		    // Call the custom API.
		    $response = wp_remote_get( add_query_arg( $api_params, EDD_WEPAY_STORE_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		    // make sure the response came back okay
		    if ( is_wp_error( $response ) )
		    	return false;

		    // decode the license data
		    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

		    // $license_data->license will be either "deactivated" or "failed"
		    if( $license_data->license == 'deactivated' )
		    	delete_option( 'edd_wepay_license_active' );

		}

	}


	/**
	 * Plugin updater
	 *
	 * @access      public
	 * @since       1.0
	 * @return      void
	 */

	public function updater() {

		if ( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			// load our custom updater
			include dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php';
		}

		global $edd_options;

		// retrieve our license key from the DB
		$license_key = isset( $edd_options['wepay_license_key'] ) ? trim( $edd_options['wepay_license_key'] ) : '';

		if( empty( $license_key ) )
			return;

		// setup the updater
		$edd_wepay_updater = new EDD_SL_Plugin_Updater( EDD_WEPAY_STORE_API_URL, __FILE__, array(
				'version'   => EDD_WEPAY_VERSION,   // current version number
				'license'   => $license_key, // license key (used get_option above to retrieve from DB)
				'item_name' => EDD_WEPAY_PRODUCT_NAME, // name of this plugin
				'author'   => 'Pippin Williamson'  // author of this plugin
			)
		);
	}

}
$edd_wepay = new EDD_WePay_Gateway;


/**
 * Registers the new license field type
 *
 * @access      private
 * @since       10
 * @return      void
*/

if( ! function_exists( 'edd_license_key_callback' ) ) {
	function edd_license_key_callback( $args ) {
		global $edd_options;

		if( isset( $edd_options[ $args['id'] ] ) ) { $value = $edd_options[ $args['id'] ]; } else { $value = isset( $args['std'] ) ? $args['std'] : ''; }
		$size = isset( $args['size'] ) && !is_null($args['size']) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $args['size'] . '-text" id="edd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="edd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';

		if( 'valid' == get_option( $args['options']['is_valid_license_option'] ) ) {
			$html .= wp_nonce_field( $args['id'] . '_nonce', $args['id'] . '_nonce', false );
			$html .= '<input type="submit" class="button-secondary" name="' . $args['id'] . '_deactivate" value="' . __( 'Deactivate License',  'edd-recurring' ) . '"/>';
		}
		$html .= '<label for="edd_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}
}