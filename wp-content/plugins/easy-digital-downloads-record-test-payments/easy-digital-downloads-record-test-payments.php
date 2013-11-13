<?php
/*
 * Plugin Name: Easy Digital Downloads - Record Test Payments 
 * Plugin URI:  https://github.com/Astoundify/crowdfunding-log-test-payments
 * Description: Log test payments.
 * Author:      Astoundify
 * Author URI:  http://astoundify.com
 * Version:     0.1
 */

add_filter( 'edd_log_test_payment_stats', '__return_true' );