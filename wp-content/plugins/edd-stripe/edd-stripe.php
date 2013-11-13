<?php
/*
Plugin Name: Easy Digital Downloads - Stripe Payment Gateway
Plugin URL: http://easydigitaldownloads.com/extension/stripe
Description: Adds a payment gateway for Stripe.com
Version: 1.6.5
Author: Pippin Williamson
Author URI: http://pippinsplugins.com
Contributors: mordauk
*/

if ( !defined( 'EDDS_PLUGIN_DIR' ) ) {
	define( 'EDDS_PLUGIN_DIR', dirname( __FILE__ ) );
}

if ( !defined( 'EDDSTRIPE_PLUGIN_URL' ) ) {
	define( 'EDDSTRIPE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}


define( 'EDD_STRIPE_STORE_API_URL', 'http://easydigitaldownloads.com' );
define( 'EDD_STRIPE_PRODUCT_NAME', 'Stripe Payment Gateway' );
define( 'EDD_STRIPE_VERSION', '1.6.5' );


/**
 * Register our payment gateway
 *
 * @access      public
 * @since       1.0
 * @return      array
 */

function edds_register_gateway( $gateways ) {
	// Format: ID => Name
	$gateways['stripe'] = array( 'admin_label' => 'Stripe', 'checkout_label' => __( 'Credit Card', 'edds' ) );
	return $gateways;
}
add_filter( 'edd_payment_gateways', 'edds_register_gateway' );


/**
 * Add an errors div
 *
 * @access      public
 * @since       1.0
 * @return      void
 */

function edds_add_stripe_errors() {
	echo '<div id="edd-stripe-payment-errors"></div>';
}
add_action( 'edd_after_cc_fields', 'edds_add_stripe_errors', 999 );

/**
 * Process stripe checkout submission
 *
 * @access      public
 * @since       1.0
 * @return      void
 */

function edds_process_stripe_payment( $purchase_data ) {

	global $edd_options;

	if ( ! class_exists( 'Stripe' ) )
		require_once EDDS_PLUGIN_DIR . '/Stripe/Stripe.php';

	if ( edd_is_test_mode() ) {
		$secret_key = $edd_options['test_secret_key'];
	} else {
		$secret_key = $edd_options['live_secret_key'];
	}

	$purchase_summary = edd_get_purchase_summary( $purchase_data );

	// make sure we don't have any left over errors present
	edd_clear_errors();

	if ( ! isset( $_POST['edd_stripe_token'] ) ) {

		// check for fallback mode
		if ( isset( $edd_options['stripe_js_fallback'] ) ) {

			if ( ! isset( $_POST['card_name'] ) || strlen( trim( $_POST['card_name'] ) ) == 0 )
				edd_set_error( 'no_card_name', __( 'Please enter a name for the credit card.', 'edds' ) );

			if ( ! isset( $_POST['card_number'] ) || strlen( trim( $_POST['card_number'] ) ) == 0 )
				edd_set_error( 'no_card_number', __( 'Please enter a credit card number.', 'edds' ) );

			if ( ! isset( $_POST['card_cvc'] ) || strlen( trim( $_POST['card_cvc'] ) ) == 0 )
				edd_set_error( 'no_card_cvc', __( 'Please enter a CVC/CVV for the credit card.', 'edds' ) );

			if ( ! isset( $_POST['card_exp_month'] ) || strlen( trim( $_POST['card_exp_month'] ) ) == 0 )
				edd_set_error( 'no_card_exp_month', __( 'Please enter a expiration month.', 'edds' ) );

			if ( ! isset( $_POST['card_exp_year'] ) || strlen( trim( $_POST['card_exp_year'] ) ) == 0 )
				edd_set_error( 'no_card_exp_year', __( 'Please enter a expiration year.', 'edds' ) );

			$card_data = array(
				'number'          => $purchase_data['card_info']['card_number'],
				'name'            => $purchase_data['card_info']['card_name'],
				'exp_month'       => $purchase_data['card_info']['card_exp_month'],
				'exp_year'        => $purchase_data['card_info']['card_exp_year'],
				'cvc'             => $purchase_data['card_info']['card_cvc'],
				'address_line1'   => $purchase_data['card_info']['card_address'],
				'address_line2'   => $purchase_data['card_info']['card_address_2'],
				'address_city'    => $purchase_data['card_info']['card_city'],
				'address_zip'     => $purchase_data['card_info']['card_zip'],
				'address_state'   => $purchase_data['card_info']['card_state'],
				'address_country' => $purchase_data['card_info']['card_country']
			);

		} else {

			// no Stripe token
			edd_set_error( 'no_token', __( 'Missing Stripe token. Please contact support.', 'edds' ) );
			edd_record_gateway_error( __( 'Missing Stripe Token', 'edds' ), __( 'A Stripe token failed to be generated. Please check Stripe logs for more information', ' edds' ) );

		}

	} else {
		$card_data = $_POST['edd_stripe_token'];
	}

	$errors = edd_get_errors();

	if ( !$errors ) {

		try {

			Stripe::setApiKey( $secret_key );

			// setup the payment details
			$payment_data = array(
				'price'        => $purchase_data['price'],
				'date'         => $purchase_data['date'],
				'user_email'   => $purchase_data['user_email'],
				'purchase_key' => $purchase_data['purchase_key'],
				'currency'     => isset( $edd_options['currency'] ) ? strtolower( $edd_options['currency'] ) : 'usd',
				'downloads'    => $purchase_data['downloads'],
				'cart_details' => $purchase_data['cart_details'],
				'user_info'    => $purchase_data['user_info'],
				'status'       => 'pending'
			);

			$customer_exists = false;

			if ( is_user_logged_in() ) {
				$user = get_user_by( 'email', $purchase_data['user_email'] );
				if ( $user ) {
					$customer_id = get_user_meta( $user->ID, '_edd_stripe_customer_id', true );
					if ( $customer_id ) {
						$customer_exists = true;

						// Update the customer to ensure their card data is up to date
						$cu = Stripe_Customer::retrieve( $customer_id );
						$cu->card = $card_data;
						$cu->save();
					}
				}
			}

			if ( ! $customer_exists ) {

				// Create a customer first so we can retrieve them later for future payments
				$customer = Stripe_Customer::create( array(
						'description' => $purchase_data['user_email'],
						'email'       => $purchase_data['user_email'],
						'card'        => $card_data
					)
				);

				$customer_id = is_array( $customer ) ? $customer['id'] : $customer->id;

				if ( is_user_logged_in() )
					update_user_meta( $user->ID, '_edd_stripe_customer_id', $customer_id );
			}

			if ( edds_is_recurring_purchase( $purchase_data ) && ( ! empty( $customer ) || $customer_exists ) ) {

				// Process a recurring subscription purchase
				$cu = Stripe_Customer::retrieve( $customer_id );

				/**********************************************************
				* Taxes, fees, and discounts have to be handled differently
				* with recurring subscriptions, so each is added as an
				* invoice item and then charged as one time items
				**********************************************************/

				$needs_invoiced = false;

				if ( $purchase_data['tax'] > 0 ) {

					Stripe_InvoiceItem::create( array(
							'customer'    => $customer_id,
							'amount'      => $purchase_data['tax'] * 100,
							'currency'    => isset( $edd_options['currency'] ) ? strtolower( $edd_options['currency'] ) : 'usd',
							'description' => sprintf( __( 'Sales tax for order %s', 'edds' ), $purchase_data['purchase_key'] )
						)
					);
					$needs_invoiced = true;
				}

				if ( ! empty( $purchase_data['fees'] ) ) {

					foreach ( $purchase_data['fees'] as $fee ) {
						Stripe_InvoiceItem::create( array(
								'customer'    => $customer_id,
								'amount'      => $fee['amount'] * 100,
								'currency'    => isset( $edd_options['currency'] ) ? strtolower( $edd_options['currency'] ) : 'usd',
								'description' => $fee['label']
							)
						);
					}
					$needs_invoiced = true;
				}

				if ( $purchase_data['discount'] > 0 ) {

					Stripe_InvoiceItem::create( array(
							'customer'    => $customer_id,
							'amount'      => ( $purchase_data['discount'] * 100 ) * -1,
							'currency'    => isset( $edd_options['currency'] ) ? strtolower( $edd_options['currency'] ) : 'usd',
							'description' => $purchase_data['user_info']['discount']
						)
					);
					$needs_invoiced = true;
				}

				if ( $needs_invoiced ) {

					// Create the invoice containing taxes / discounts / fees
					$invoice = Stripe_Invoice::create( array(
							'customer' => $customer_id, // the customer to apply the fee to
						) );
					$invoice->pay();

				}

				$plan_id = edds_get_plan_id( $purchase_data );

				$cu->updateSubscription( array( 'plan' => $plan_id ) );

				// record the pending payment
				$payment = edd_insert_payment( $payment_data );

				// Set user as subscriber
				EDD_Recurring_Customer::set_as_subscriber( $user->ID );

				// store the customer recurring ID
				EDD_Recurring_Customer::set_customer_id( $user->ID, $customer_id );

				// Set the customer status
				EDD_Recurring_Customer::set_customer_status( $user->ID, 'active' );

				// Calculate the customer's new expiration date
				$new_expiration = EDD_Recurring_Customer::calc_user_expiration( $user->ID, $payment );

				// Set the customer's new expiration date
				EDD_Recurring_Customer::set_customer_expiration( $user->ID, $new_expiration );

				// Store the parent payment ID in the user meta
				EDD_Recurring_Customer::set_customer_payment_id( $user->ID, $payment );

			} elseif ( ! empty( $customer ) || $customer_exists ) {

				// Process a normal one-time charge purchase

				if( ! isset( $edd_options['stripe_preapprove_only'] ) ) {
					$charge = Stripe_Charge::create( array(
							"amount"        => $purchase_data['price'] * 100, // amount in cents
							"currency"      => isset( $edd_options['currency'] ) ? strtolower( $edd_options['currency'] ) : 'usd',
							"customer"      => $customer_id,
							"description"   => $purchase_summary
						)
					);
				}

				// record the pending payment
				$payment = edd_insert_payment( $payment_data );

			} else {

				edd_record_gateway_error( __( 'Customer Creation Failed', 'edds' ), sprintf( __( 'Customer creation failed while processing a payment. Payment Data: %s', ' edds' ), json_encode( $payment_data ) ), $payment );

			}

			if ( $payment && ( ! empty( $customer_id ) || ! empty( $charge ) ) ) {

				if ( isset( $edd_options['stripe_preapprove_only'] ) ) {
					edd_update_payment_status( $payment, 'preapproval' );
					add_post_meta( $payment, '_edds_stripe_customer_id', $customer_id );
				} else {
					edd_update_payment_status( $payment, 'publish' );
				}

				if ( ! empty( $charge ) )
					edd_insert_payment_note( $payment, 'Stripe Charge ID: ' . $charge->id );
				elseif ( ! empty( $customer_id ) )
					edd_insert_payment_note( $payment, 'Stripe Customer ID: ' . $customer_id );

				edd_empty_cart();
				edd_send_to_success_page();

			} else {

				edd_set_error( 'payment_not_recorded', __( 'Your payment could not be recorded, please contact the site administrator.', 'edds' ) );

				// if errors are present, send the user back to the purchase page so they can be corrected
				edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );

			}
		} catch ( Exception $e ) {

			//print_r($e); exit;
			edd_set_error( 'payment_error', __( 'There was an error processing your payment, please ensure you have entered your card number correctly.', 'edds' ) );

			edd_record_gateway_error( __( 'Stripe Error', 'edds' ), sprintf( __( 'There was an error while processing a Stripe payment. Payment data: %s', ' edds' ), json_encode( $e ) ), 0 );

			edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
		}
	} else {
		edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
	}
}
add_action( 'edd_gateway_stripe', 'edds_process_stripe_payment' );


/**
 * Create recurring payment plans when downloads are saved
 *
 * This is in order to support the Recurring Payments module
 *
 * @access      public
 * @since       1.5
 * @return      int
 */

function edds_create_recurring_plans( $post_id = 0 ) {
	global $edd_options, $post;

	if ( ! class_exists( 'EDD_Recurring' ) )
		return $post_id;

	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) )
		return $post_id;

	if ( isset( $post->post_type ) && $post->post_type == 'revision' )
		return $post_id;

	if ( ! isset( $post->post_type )|| $post->post_type != 'download' )
		return $post_id;

	if ( ! current_user_can( 'edit_products', $post_id ) )
		return $post_id;

	if ( ! class_exists( 'Stripe' ) )
		require_once EDDS_PLUGIN_DIR . '/Stripe/Stripe.php';

	$secret_key = edd_is_test_mode() ? trim( $edd_options['test_secret_key'] ) : trim( $edd_options['live_secret_key'] );

	$plans = array();

	try {

		Stripe::setApiKey( $secret_key );

		if ( edd_has_variable_prices( $post_id ) ) {

			$prices = edd_get_variable_prices( $post_id );
			foreach ( $prices as $price_id => $price ) {

				if ( EDD_Recurring()->is_price_recurring( $post_id, $price_id ) ) {

					$period = EDD_Recurring()->get_period( $price_id, $post_id );

					if ( $period == 'day' || $period == 'week' )
						wp_die( __( 'Stripe only permits yearly and monthly plans.', 'edds' ), __( 'Error', 'edds' ) );

					if ( EDD_Recurring()->get_times( $price_id, $post_id ) > 0 )
						wp_die( __( 'Stripe requires that the Times option be set to 0.', 'edds' ), __( 'Error', 'edds' ) );

					$plans[] = array(
						'name'   => $price['name'],
						'price'  => $price['amount'],
						'period' => $period
					);

				}
			}

		} else {

			if ( EDD_Recurring()->is_recurring( $post_id ) ) {

				$period = EDD_Recurring()->get_period_single( $post_id );

				if ( $period == 'day' || $period == 'week' )
					wp_die( __( 'Stripe only permits yearly and monthly plans.', 'edds' ), __( 'Error', 'edds' ) );

				if ( EDD_Recurring()->get_times_single( $post_id ) > 0 )
					wp_die( __( 'Stripe requires that the Times option be set to 0.', 'edds' ), __( 'Error', 'edds' ) );

				$plans[] = array(
					'name'   => get_post_field( 'post_name', $post_id ),
					'price'  => edd_get_download_price( $post_id ),
					'period' => $period
				);
			}
		}

		// Get all plans so we know which ones already exist
		$all_plans = Stripe_Plan::all();
		$all_plans = $all_plans['data'];
		$all_plans = wp_list_pluck( $all_plans, "id" );

		foreach ( $plans as $plan ) {

			// Create the plan ID
			$plan_id = $plan['name'] . '_' . $plan['price'] . '_' . $plan['period'];
			$plan_id = sanitize_key( $plan_id );
			$plan_id = apply_filters( 'edd_recurring_plan_id', $plan_id, $plan );

			if ( in_array( $plan_id, $all_plans ) )
				continue;

			$plan_args = array(
				"amount"   => $plan['price'] * 100,
				"interval" => $plan['period'],
				"name"     => $plan['name'],
				"currency" => isset( $edd_options['currency'] ) ? strtolower( $edd_options['currency'] ) : 'usd',
				"id"       => $plan_id
			);

			$plan_args = apply_filters( 'edd_recurring_plan_details', $plan_args, $plan_id );

			Stripe_Plan::create( $plan_args );
		}
	} catch( Exception $e ) {
		wp_die( __( 'There was an error creating a payment plan with Stripe.', 'edds' ), __( 'Error', 'edds' ) );
	}
}
add_action( 'save_post', 'edds_create_recurring_plans', 999 );


/**
 * Detect if the current purchase is for a recurring product
 *
 * @access      public
 * @since       1.5
 * @return      bool
 */

function edds_is_recurring_purchase( $purchase_data ) {

	if ( ! class_exists( 'EDD_Recurring' ) )
		return false;

	if ( EDD_Recurring()->is_purchase_recurring( $purchase_data ) )
		return true;

	return false;
}


/**
 * Retrieve the plan ID from the purchased items
 *
 * @access      public
 * @since       1.5
 * @return      string|bool
 */

function edds_get_plan_id( $purchase_data ) {
	foreach ( $purchase_data['downloads'] as $download ) {

		if ( edd_has_variable_prices( $download['id'] ) ) {

			$prices = edd_get_variable_prices( $download['id'] );

			$price_name   = edd_get_price_option_name( $download['id'], $download['options']['price_id'] );
			$price_amount = $prices[ $download['options']['price_id'] ]['amount'];

		} else {

			$price_name   = get_post_field( 'post_name', $download['id'] );
			$price_amount = edd_get_download_price( $download['id'] );

		}

		$period = $download['options']['recurring']['period'];

		$plan_id = $price_name . '_' . $price_amount . '_' . $period;
		return sanitize_key( $plan_id );
	}
	return false;
}


/**
 * Fiter the Recurring Payments cancellation link
 *
 * @access      public
 * @since       1.5
 * @return      string
 */

function edds_recurring_cancel_link( $link = '', $user_id = 0 ) {

	$customer_id = EDD_Recurring_Customer::get_customer_id( $user_id );

	// Only modify Stripe customer's cancellation links
	if ( strpos( $customer_id, 'cus_' ) === false )
		return $link;

	$cancel_url = wp_nonce_url( add_query_arg( array( 'edd_action' => 'cancel_recurring_stripe_customer', 'customer_id' => $customer_id, 'user_id' => $user_id ) ), 'edd_stripe_cancel' );
	$link       = '<a href="%s" class="edd-recurring-cancel" title="%s">%s</a>';
	$link       = sprintf(
		$link,
		$cancel_url,
		__( 'Cancel your subscription', 'edd-recurring' ),
		empty( $atts['text'] ) ? __( 'Cancel Subscription', 'edd-recurring' ) : esc_html( $atts['text'] )
	);

	$link .= '<script type="text/javascript">jQuery(document).ready(function($) {$(".edd-recurring-cancel").on("click", function() { if(confirm("' . __( "Do you really want to cancel your subscription? You will retain access for the length of time you have paid for.", "edds" ) . '")) {return true;}return false;});});</script>';

	return $link;

}
add_filter( 'edd_recurring_cancel_link', 'edds_recurring_cancel_link', 10, 2 );


/**
 * Process a recurring payments cancellation
 *
 * @access      public
 * @since       1.5
 * @return      void
 */

function edds_cancel_subscription( $data ) {
	if ( wp_verify_nonce( $data['_wpnonce'], 'edd_stripe_cancel' ) ) {

		global $edd_options;

		$secret_key = edd_is_test_mode() ? trim( $edd_options['test_secret_key'] ) : trim( $edd_options['live_secret_key'] );

		if ( ! class_exists( 'Stripe' ) )
			require_once EDDS_PLUGIN_DIR . '/Stripe/Stripe.php';

		Stripe::setApiKey( $secret_key );

		try {

			$cu = Stripe_Customer::retrieve( urldecode( $data['customer_id'] ) );
			$cu->cancelSubscription( array( 'at_period_end' => true ) );

			EDD_Recurring_Customer::set_customer_status( $data['user_id'], 'cancelled' );

			wp_redirect(
				add_query_arg(
					'subscription',
					'cancelled',
					remove_query_arg( array( 'edd_action', 'customer_id', 'user_id', '_wpnonce' ) )
				)
			);
			exit;

		} catch( Exception $e ) {
			wp_die( '<pre>' . $e . '</pre>', __( 'Error', 'edds' ) );
		}

	}
}
add_action( 'edd_cancel_recurring_stripe_customer', 'edds_cancel_subscription' );


/**
 * Register payment statuses for preapproval
 *
 * @since 1.6
 * @return void
 */
function edds_register_post_statuses() {
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
add_action( 'init',  'edds_register_post_statuses', 110 );


/**
 * Register our new payment status labels for EDD
 *
 * @since 1.6
 * @return array
 */
function edds_payment_status_labels( $statuses ) {
	$statuses['preapproval'] = __( 'Preapproved', 'edds' );
	$statuses['cancelled']   = __( 'Cancelled', 'edds' );
	return $statuses;
}
add_filter( 'edd_payment_statuses', 'edds_payment_status_labels' );


/**
 * Display the Preapprove column label
 *
 * @since 1.6
 * @return array
 */
function edds_payments_column( $columns ) {

	global $edd_options;

	if ( isset( $edd_options['stripe_preapprove_only'] ) ) {
		$columns['preapproval'] = __( 'Preapproval', 'edds' );
	}
	return $columns;
}
add_filter( 'edd_payments_table_columns', 'edds_payments_column' );


/**
 * Display the payment status filters
 *
 * @since 1.6
 * @return array
 */
function edds_payment_status_filters( $views ) {
	$payment_count        = wp_count_posts( 'edd_payment' );
	$preapproval_count    = '&nbsp;<span class="count">(' . $payment_count->preapproval . ')</span>';
	$cancelled_count      = '&nbsp;<span class="count">(' . $payment_count->cancelled . ')</span>';
	$current              = isset( $_GET['status'] ) ? $_GET['status'] : '';
	$views['preapproval'] = sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'preapproval', admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ), $current === 'preapproval' ? ' class="current"' : '', __( 'Preapproval Pending', 'edd' ) . $preapproval_count );
	$views['cancelled']   = sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'cancelled', admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ), $current === 'cancelled' ? ' class="current"' : '', __( 'Cancelled', 'edd' ) . $cancelled_count );

	return $views;
}
add_filter( 'edd_payments_table_views', 'edds_payment_status_filters' );

/**
 * Show the Process / Cancel buttons for preapproved payments
 *
 * @since 1.6
 * @return string
 */
function edds_payments_column_data( $value, $payment_id, $column_name ) {
	if ( $column_name == 'preapproval' ) {
		$status      = get_post_status( $payment_id );
		$customer_id = get_post_meta( $payment_id, '_edds_stripe_customer_id', true );

		if( ! $customer_id )
			return $value;

		$nonce = wp_create_nonce( 'edds-process-preapproval' );

		$preapproval_args     = array(
			'payment_id'      => $payment_id,
			'nonce'           => $nonce,
			'edd-action'      => 'charge_stripe_preapproval'
		);
		$cancel_args          = array(
			'preapproval_key' => $customer_id,
			'payment_id'      => $payment_id,
			'nonce'           => $nonce,
			'edd-action'      => 'cancel_stripe_preapproval'
		);

		if ( 'preapproval' === $status ) {
			$value = '<a href="' . add_query_arg( $preapproval_args, admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ) . '" class="button-secondary button">' . __( 'Process Payment', 'edds' ) . '</a>&nbsp;';
			$value .= '<a href="' . add_query_arg( $cancel_args, admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ) . '" class="button-secondary button">' . __( 'Cancel Preapproval', 'edds' ) . '</a>';
		}
	}
	return $value;
}
add_filter( 'edd_payments_table_column', 'edds_payments_column_data', 10, 3 );


/**
 * Trigger preapproved payment charge
 *
 * @since 1.6
 * @return void
 */
function edds_process_preapproved_charge() {

	if( empty( $_GET['nonce'] ) )
		return;

	if( ! wp_verify_nonce( $_GET['nonce'], 'edds-process-preapproval' ) )
		return;

	$payment_id  = absint( $_GET['payment_id'] );
	$charge      = edds_charge_preapproved( $payment_id );

	if ( $charge ) {
		wp_redirect( add_query_arg( array( 'edd-message' => 'preapproval-charged' ), admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ) ); exit;
	} else {
		wp_redirect( add_query_arg( array( 'edd-message' => 'preapproval-failed' ), admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ) ); exit;
	}

}
add_action( 'edd_charge_stripe_preapproval', 'edds_process_preapproved_charge' );


/**
 * Cancel a preapproved payment
 *
 * @since 1.6
 * @return void
 */
function edds_process_preapproved_cancel() {
	global $edd_options;

	if( empty( $_GET['nonce'] ) )
		return;

	if( ! wp_verify_nonce( $_GET['nonce'], 'edds-process-preapproval' ) )
		return;

	$payment_id  = absint( $_GET['payment_id'] );
	$customer_id = get_post_meta( $payment_id, '_edds_stripe_customer_id', true );

	if( empty( $customer_id ) || empty( $payment_id ) )
		return;

	if ( 'preapproval' !== get_post_status( $payment_id ) )
		return;

	if ( ! class_exists( 'Stripe' ) )
		require_once EDDS_PLUGIN_DIR . '/Stripe/Stripe.php';

	edd_insert_payment_note( $payment_id, __( 'Preapproval cancelled', 'edds' ) );
	edd_update_payment_status( $payment_id, 'cancelled' );
	delete_post_meta( $payment_id, '_edds_stripe_customer_id' );

	wp_redirect( add_query_arg( array( 'edd-message' => 'preapproval-cancelled' ), admin_url( 'edit.php?post_type=download&page=edd-payment-history' ) ) ); exit;
}
add_action( 'edd_cancel_stripe_preapproval', 'edds_process_preapproved_cancel' );


/**
 * Charge a preapproved payment
 *
 * @since 1.6
 * @return bool
 */
function edds_charge_preapproved( $payment_id = 0 ) {

	global $edd_options;

	if( empty( $payment_id ) )
		return false;

	$customer_id = get_post_meta( $payment_id, '_edds_stripe_customer_id', true );

	if( empty( $customer_id ) || empty( $payment_id ) )
		return;

	if ( 'preapproval' !== get_post_status( $payment_id ) )
		return;

	if ( ! class_exists( 'Stripe' ) )
		require_once EDDS_PLUGIN_DIR . '/Stripe/Stripe.php';

	$secret_key = edd_is_test_mode() ? trim( $edd_options['test_secret_key'] ) : trim( $edd_options['live_secret_key'] );

	Stripe::setApiKey( $secret_key );

	$charge = Stripe_Charge::create( array(
			"amount"        => edd_get_payment_amount( $payment_id ) * 100, // amount in cents
			"currency"      => isset( $edd_options['currency'] ) ? strtolower( $edd_options['currency'] ) : 'usd',
			"customer"      => $customer_id,
			"description"   => sprintf( __( 'Preappoved charge for purchase %s from %s', 'edds' ), edd_get_payment_key( $payment_id ), home_url() )
		)
	);

	if ( ! empty( $charge ) ) {
		edd_insert_payment_note( $payment_id, 'Stripe Charge ID: ' . $charge->id );
		edd_update_payment_status( $payment_id, 'publish' );
		delete_post_meta( $payment_id, '_edds_stripe_customer_id' );
		return true;
	} else {
		return false;
	}
}


/**
 * Admin Messages
 *
 * @since 1.6
 * @return void
 */
function edds_admin_messages() {

	if ( isset( $_GET['edd-message'] ) && 'preapproval-charged' == $_GET['edd-message'] ) {
		 add_settings_error( 'edds-notices', 'edds-preapproval-charged', __( 'The preapproved payment was successfully charged.', 'edds' ), 'updated' );
	}
	if ( isset( $_GET['edd-message'] ) && 'preapproval-failed' == $_GET['edd-message'] ) {
		 add_settings_error( 'edds-notices', 'edds-preapproval-charged', __( 'The preapproved payment failed to be charged.', 'edds' ), 'error' );
	}
	if ( isset( $_GET['edd-message'] ) && 'preapproval-cancelled' == $_GET['edd-message'] ) {
		 add_settings_error( 'edds-notices', 'edds-preapproval-cancelled', __( 'The preapproved payment was successfully cancelled.', 'edds' ), 'updated' );
	}

	settings_errors( 'edds-notices' );
}
add_action( 'admin_notices', 'edds_admin_messages' );


/**
 * Listen for Stripe events, primarily recurring payments
 *
 * @access      public
 * @since       1.5
 * @return      void
 */

function edds_stripe_event_listener() {

	if ( ! class_exists( 'EDD_Recurring' ) )
		return;

	if ( isset( $_GET['edd-listener'] ) && $_GET['edd-listener'] == 'stripe' ) {

		global $edd_options;

		if ( ! class_exists( 'Stripe' ) )
			require_once EDDS_PLUGIN_DIR . '/Stripe/Stripe.php';

		$secret_key = edd_is_test_mode() ? trim( $edd_options['test_secret_key'] ) : trim( $edd_options['live_secret_key'] );

		Stripe::setApiKey( $secret_key );

		// retrieve the request's body and parse it as JSON
		$body = @file_get_contents( 'php://input' );
		$event_json = json_decode( $body );

		// for extra security, retrieve from the Stripe API
		$event_id = $event_json->id;
		if ( isset( $event_json->id ) ) {

			//print_r( $event_json ); exit;

			$invoice = $event_json->data->object;
			switch ( $event_json->type ) :

				case 'invoice.payment_succeeded' :

					// Process a subscription payment

					// retrieve the customer who made this payment (only for subscriptions)
					$user_id = EDD_Recurring_Customer::get_user_id_by_customer_id( $invoice->customer );

					// retrieve the customer ID from WP database
					$customer_id = EDD_Recurring_Customer::get_customer_id( $user_id );

					// check to confirm this is a stripe subscriber
					if ( $user_id && $customer_id ) {

						$cu = Stripe_Customer::retrieve( $customer_id );
						$plan_id = $cu->subscription->plan->id;

						// Make sure this charge is for the user's subscription
						if ( $plan_id != $invoice->lines->data[0]->plan->id )
							return;

						// Retrieve the original payment details
						$parent_payment_id = EDD_Recurring_Customer::get_customer_payment_id( $user_id );
						$customer_email    = edd_get_payment_user_email( $parent_payment_id );

						// Store the payment
						EDD_Recurring()->record_subscription_payment( $parent_payment_id, $invoice->total / 100, $invoice->charge );

						// Set the customer's status to active
						EDD_Recurring_Customer::set_customer_status( $user_id, 'active' );

						// Calculate the customer's new expiration date
						$new_expiration = EDD_Recurring_Customer::calc_user_expiration( $user_id, $parent_payment_id );

						// Set the customer's new expiration date
						EDD_Recurring_Customer::set_customer_expiration( $user_id, $new_expiration );

						exit;

					}

				break;

				case 'customer.subscription.deleted' :

					// Process a cancellation

					// retrieve the customer who made this payment (only for subscriptions)
					$user_id = EDD_Recurring_Customer::get_user_id_by_customer_id( $invoice->customer );

					$parent_payment_id = EDD_Recurring_Customer::get_customer_payment_id( $user_id );

					// Set the customer's status to active
					EDD_Recurring_Customer::set_customer_status( $user_id, 'cancelled' );

					edd_update_payment_status( $parent_payment_id, 'cancelled' );

					exit;

					break;

			endswitch;
			//edd_record_gateway_error( __( 'Stripe Webhook Error', 'edds' ), sprintf( __( 'There was an error parsing a Stripe webhook: ', 'edds' ), json_encode( $e ) ) );

		}
	}
}
add_action( 'init', 'edds_stripe_event_listener' );


/**
 * Register the gateway settings
 *
 * @access      public
 * @since       1.0
 * @return      array
 */

function edds_add_settings( $settings ) {

	$stripe_settings = array(
		array(
			'id'   => 'stripe_settings',
			'name'  => '<strong>' . __( 'Stripe Settings', 'edds' ) . '</strong>',
			'desc'  => __( 'Configure the Stripe settings', 'edds' ),
			'type'  => 'header'
		),
		array(
			'id'   => 'stripe_license_key',
			'name'  => __( 'License Key', 'edds' ),
			'desc'  => __( 'Enter your license for the Stripe Payment Gateway to receive automatic upgrades', 'edds' ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'   => 'live_secret_key',
			'name'  => __( 'Live Secret Key', 'edds' ),
			'desc'  => __( 'Enter your live secret key, found in your Stripe Account Settings', 'edds' ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'   => 'live_publishable_key',
			'name'  => __( 'Live Publishable Key', 'edds' ),
			'desc'  => __( 'Enter your live publishable key, found in your Stripe Account Settings', 'edds' ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'   => 'test_secret_key',
			'name'  => __( 'Test Secret Key', 'edds' ),
			'desc'  => __( 'Enter your test secret key, found in your Stripe Account Settings', 'edds' ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'   => 'test_publishable_key',
			'name'  => __( 'Test Publishable Key', 'edds' ),
			'desc'  => __( 'Enter your test publishable key, found in your Stripe Account Settings', 'edds' ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'   => 'stripe_js_fallback',
			'name'  => __( 'Stripe JS Fallback Support', 'edds' ),
			'desc'  => __( 'Check this if your site has problems with processing cards using Stripe JS. This option makes card processing slightly less seccure.', 'edds' ),
			'type'  => 'checkbox'
		),
		array(
			'id'   => 'stripe_preapprove_only',
			'name'  => __( 'Preapprove Only?', 'edds' ),
			'desc'  => __( 'Check this if you would like to preapprove payments but not charge until a later date.', 'edds' ),
			'type'  => 'checkbox'
		)
	);

	return array_merge( $settings, $stripe_settings );
}
add_filter( 'edd_settings_gateways', 'edds_add_settings' );


/**
 * Activate a license key
 *
 * @access      public
 * @since       1.0
 * @return      void
 */

function edd_stripe_activate_license() {
	global $edd_options;

	if ( ! isset( $_POST['edd_settings_gateways'] ) )
		return;
	if ( ! isset( $_POST['edd_settings_gateways']['stripe_license_key'] ) )
		return;

	if ( get_option( 'edd_stripe_license_key_active' ) == 'valid' )
		return;

	$license = sanitize_text_field( $_POST['edd_settings_gateways']['stripe_license_key'] );

	// data to send in our API request
	$api_params = array(
		'edd_action'=> 'activate_license',
		'license'   => $license,
		'item_name' => urlencode( EDD_STRIPE_PRODUCT_NAME ) // the name of our product in EDD
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, EDD_STRIPE_STORE_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

	// make sure the response came back okay
	if ( is_wp_error( $response ) )
		return false;

	// decode the license data
	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	update_option( 'edd_stripe_license_key_active', $license_data->license );

}
add_action( 'admin_init', 'edd_stripe_activate_license' );


/**
 * Load our javascript
 *
 * @access      public
 * @since       1.0
 * @return      void
 */

function edd_stripe_js() {
	if ( function_exists( 'edd_is_checkout' ) ) {
		global $edd_options;

		if ( isset( $edd_options['stripe_js_fallback'] ) )
			return; // in fallback mode

		$publishable_key = NULL;

		if ( edd_is_test_mode() ) {
			$publishable_key = trim( $edd_options['test_publishable_key'] );
		} else {
			$publishable_key = trim( $edd_options['live_publishable_key'] );
		}
		if ( edd_is_checkout() && edd_is_gateway_active( 'stripe' ) ) {

			wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/v1/', array( 'jquery' ) );
			wp_enqueue_script( 'edd-stripe-js', EDDSTRIPE_PLUGIN_URL . 'edd-stripe.js', array( 'jquery', 'stripe-js' ), EDD_STRIPE_VERSION );

			$stripe_vars = array(
				'publishable_key' => $publishable_key,
				'is_ajaxed'   => edd_is_ajax_enabled() ? 'true' : 'false'
			);

			wp_localize_script( 'edd-stripe-js', 'edd_stripe_vars', $stripe_vars );

		}
	}
}
add_action( 'wp_enqueue_scripts', 'edd_stripe_js', 100 );


/**
 * Plugin updater
 *
 * @access      public
 * @since       1.0
 * @return      void
 */

function edd_stripe_updater() {

	if ( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		// load our custom updater
		include dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php';
	}


	global $edd_options;

	// retrieve our license key from the DB
	$edd_stripe_license_key = isset( $edd_options['stripe_license_key'] ) ? trim( $edd_options['stripe_license_key'] ) : '';

	// setup the updater
	$edd_stripe_updater = new EDD_SL_Plugin_Updater( EDD_STRIPE_STORE_API_URL, __FILE__, array(
			'version'   => EDD_STRIPE_VERSION,   // current version number
			'license'   => $edd_stripe_license_key, // license key (used get_option above to retrieve from DB)
			'item_name' => EDD_STRIPE_PRODUCT_NAME, // name of this plugin
			'author'   => 'Pippin Williamson'  // author of this plugin
		)
	);
}
add_action( 'admin_init', 'edd_stripe_updater' );
