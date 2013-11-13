<?php
/*
Plugin Name: Email Pre-approval Notification
Plugin URI: 
Description: 
Version: 
Author: 
Author URI: 
License: 
License URI: 
*/

/**
*CUSTOM EMAIL SENT WHEN A PLEDGE IS MADE
*/
function edd_email_preapproved_email( $payment_id) {
        global $edd_options;
 
        $payment_data = edd_get_payment_meta( $payment_id );
        $user_info    = maybe_unserialize( $payment_data['user_info'] );
        $email        = edd_get_payment_user_email( $payment_id );
 
        if ( isset( $user_info['id'] ) && $user_info['id'] > 0 ) {
                $user_data = get_userdata($user_info['id']);
                $name = $user_data->display_name;
        } elseif ( isset( $user_info['first_name'] ) && isset( $user_info['last_name'] ) ) {
                $name = $user_info['first_name'] . ' ' . $user_info['last_name'];
        } else {
                $name = $email;
        }
 
        $message = edd_get_email_body_header();
        $message .= 'custom message goes here';
        $message .= edd_get_email_body_footer();
 
        $from_name = isset( $edd_options['from_name'] ) ? $edd_options['from_name'] : get_bloginfo('name');
        $from_email = isset( $edd_options['from_email'] ) ? $edd_options['from_email'] : get_option('admin_email');
 
        $subject = __( 'Thanks For Your Pledge!', 'edd' );
 
        $headers = "From: " . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
        $headers .= "Reply-To: ". $from_email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        $headers = apply_filters( 'edd_receipt_headers', $headers, $payment_id, $payment_data );
        wp_mail( $email, $subject, $message, $headers, $attachments );
}
function pw_edd_preapproved_purchase( $payment_id, $new_status, $old_status ) {
if ( $old_status == 'publish' || $old_status == 'complete' )
return; // Make sure that payments are only completed once
 
// Make sure the payment completion is only processed when new status is complete
if ( $new_status != 'preapproval' )
return;
 
// Send email
edd_email_preapproved_email($payment_id);
edd_admin_email_preapproved_email($payment_id);
}
add_action( 'edd_update_payment_status', 'pw_edd_preapproved_purchase', 100, 3 );