<?php
/*
Plugin Name: Purchase checker
Plugin URI: 
Description: 
Version: 
Author: 
Author URI: 
License: 
License URI: 
*/



// output our custom field HTML
function pippin_edd_custom_checkout_fields() {
	?>
	<p><label class="edd-label" for="edd-phone"><?php _e('Phone Number', 'edd'); ?></label>
		<input class="edd-input required" type="text" name="edd_phone" id="edd-phone" placeholder="<?php _e('Phone Number', 'edd'); ?>" value=""/>
	
	</p>
	<p><label class="edd-label" for="edd-company"><?php _e('Company Name', 'edd'); ?></label>
		<input class="edd-input" type="text" name="edd_company" id="edd-company" placeholder="<?php _e('Company name', 'edd'); ?>" value=""/>
	
	</p>	
	<input type="hidden" name="edd_pal_status" value="Pending">
	<?php
}
add_action('edd_purchase_form_user_info', 'pippin_edd_custom_checkout_fields');

// check for errors with out custom fields
function pippin_edd_validate_custom_fields($data) {
	if(!isset($data['edd_phone']) || $data['edd_phone'] == '') {
		// check for a phone number
		//edd_set_error( 'invalid_phone', __('You must provide your phone number.', 'pippin_edd') );
	}
}
add_action('edd_checkout_error_checks', 'pippin_edd_validate_custom_fields');


// store the custom field data in the payment meta
function pippin_edd_store_custom_fields($payment_meta) {
	$payment_meta['phone'] = isset($_POST['edd_phone']) ? $_POST['edd_phone'] : '';
	$payment_meta['company'] = isset($_POST['edd_company']) ? $_POST['edd_company'] : '';
	$payment_meta['edd_pal_status'] = isset($_POST['edd_pal_status']) ? $_POST['edd_pal_status'] : '';
	return $payment_meta;
}
add_filter('edd_payment_meta', 'pippin_edd_store_custom_fields');


// show the custom fields in the "View Order Details" popup
function pippin_edd_purchase_details($payment_meta, $user_info) {
	$phone = isset($payment_meta['phone']) ? $payment_meta['phone'] : 'none';
	$company = isset($payment_meta['company']) ? $payment_meta['company'] : 'none';	
	$status= isset($payment_meta['edd_pal_status']) ? $payment_meta['edd_pal_status'] : 'none';	
	?>
	<li><?php echo __('Phone:', 'pippin') . ' ' . $phone; ?></li>
	<li><?php echo __('Company:', 'pippin') . ' ' . $company; ?></li>
	<li><?php echo __('Delivery:', 'pippin') . ' ' . $status; ?></li>
	<?php
}
add_action('edd_payment_personal_details_list', 'pippin_edd_purchase_details', 1, 2);








function iamashortcode() {
	
	echo 'I am a short-code Ding dong the wicked witch is here!!!!!!!!!';
}
add_shortcode( 'short_code_o', 'iamashortcode' );








