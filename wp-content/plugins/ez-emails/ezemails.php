<?php
/*
Plugin Name: EZ Emails
Plugin URI: http://wordpress.org/extend/plugins/ez-emails
Description: EZ Emails allows you to create email templates and send users or manually input email addresses any information based on those templates.
Author: Luigi Pulcini
Version: 1.3.6
Author URI: http://www.luigipulcini.com
*/

define('CR', "\r");
define('LF', "\n");
define('CRLF', "\r\n");
define('BR', '<br />' . LF);

define ('EZEMAILS_VERSION', '1.3.6');
$ezemails_options = get_option('ezemails_options');

function ezemails_activation() {
	global $ezemails_options;
	if (!get_option('ezemails_options')) {
		$ezemails_options = array(
						 'sender_name'			=>	get_bloginfo('name'),
						 'sender_email'			=>	get_settings('admin_email'),
						 'replace_template'		=>	0,
						 'override_all_mails'	=>	0,
						 'default_template'		=>	'Default',
						 'version'				=>	EZEMAILS_VERSION
						 );
		update_option('ezemails_options', $ezemails_options);
		$templates = array(
						   'Default'				=>	'
								<html>
									<head>
										<title></title>
									</head>
									 <body style="background:#ddd;margin:0;font-family:arial,helvetica,sans-serif;font-size:14px;">
										 <div style="width:800px;background:#fff;margin:0 auto;">
											 <div style="padding:5px;text-align:center;background:#036;color:#fff;">
												 <h3>Welcome to '.get_bloginfo('name').'</h3>
											 </div>
											 <div style="padding:20px 20px 0">
												 %content%
											 </div>
											 <div style="padding:20px">
												 %signature%
											 </div>
										 </div>
									 </body>
								 </html>
							 '
		);
		update_option('ezemails_templates', $templates);
		
		// Create a simple default signature per each user registered
		$users = get_users();
		foreach ($users as $user) {
			$signatures = array(
				'Default'	=>	'<p>'.$user->display_name.'<br/><a href="mailto:'.$user->user_email.'">'.$user->user_email.'</a></p>'
			);
			update_user_meta($user->ID, 'ezemails_signatures', $signatures);
		}
	} else {
		// 
		$ezemails_options = get_option('ezemails_options');
		
		// Checks the version already installed in the website
		switch (true) {
			case ($ezemails_options['version'] < '1.3'):
				$ezemails_options['override_all_mails'] = 0;
			default:
				$ezemails_options['version'] = EZEMAILS_VERSION;
				break;
		}
		update_option('ezemails_options', $ezemails_options);
	}
}
register_activation_hook( __FILE__, 'ezemails_activation' );

function ezemails_deactivation() {
	// Deactivation commands

}
register_deactivation_hook( __FILE__, 'ezemails_deactivation' );

function ezemails_action_links($links, $file) {
    static $this_plugin;
 
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
 
    if ($file == $this_plugin) {
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/tools.php?page=ezemails">Control Panel</a>';
        array_unshift($links, $settings_link);
    }
 
    return $links;
}
add_filter('plugin_action_links', 'ezemails_action_links', 10, 2);

$ezemails_options = get_option('ezemails_options');

function ezemails_phpmailer(PHPMailer $mail) {
	global $ezemails_options;
	$templates = get_option('ezemails_templates');
	$template = htmlspecialchars_decode(stripslashes($templates[$ezemails_options['default_template']]));
	$signature = '
		Sincerely,<br/>
		<strong>'.$ezemails_options['sender_name'].'</strong>
	';
	$template = str_replace('%signature%', $signature, $template);

	$body = $mail->Body;
	if ($mail->ContentType == 'text/plain') $body = htmlspecialchars($body);
	$body = str_replace(CRLF, BR, $body);
	
	$mail->IsHTML();
	$mail->Body = str_replace('%content%', $body, $template);
	
}
if ($ezemails_options['override_all_mails']) add_action('phpmailer_init', 'ezemails_phpmailer', 10, 1);



// Overrides the wp_new_user_notification function using one of the templates designed by user
if ( !function_exists('wp_new_user_notification') && $ezemails_options['replace_template']) {
	function wp_new_user_notification($user_id, $plaintext_pass = '') {
		global $ezemails_options;
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$user = get_userdata( $user_id );
		$user_email = stripslashes($user->user_email);
	
		$headers = array();
		$headers[] = 'From: '.$ezemails_options['sender_name'].' <'.$ezemails_options['sender_email'].'>';
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=utf-8';

		if (!$ezemails_options['override_all_mails']) add_action('phpmailer_init', 'ezemails_phpmailer', 10, 1);

		$message = ezemails_register_admin_message($user_id);
		@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message, $headers);

		if ( empty($plaintext_pass) )
			return;

		$message = ezemails_register_user_message($user_id, $plaintext_pass);
		$result = wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message, $headers);
		
		if (!$ezemails_options['override_all_mails']) remove_action('phpmailer_init', 'ezemails_phpmailer', 10, 1);
	}
	add_filter ("wpmu_welcome_user_notification", "wp_new_user_notification", 10, 2);
}

function ezemails_register_admin_message($user_id) {
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$user = get_userdata( $user_id );
	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);
	$user_display_name = stripslashes($user->display_name);
	
	$message  = '<p>'.sprintf(__('A new user has just registered to %s:','ezemails'), $blogname) . "</p>";
	$message .= '<p>'.sprintf(__('name: %s'), '<strong>'.$user_display_name.'</strong>') . "<br/>";
	$message .= sprintf(__('username: %s'), '<strong>'.$user_login.'</strong>') . "<br/>";
	$message .= sprintf(__('email: %s'), '<strong>'.$user_email.'</strong>').'</p>';
	
	return $message;
}

function ezemails_register_user_message($user_id, $plaintext_pass) {
	global $ezemails_options;
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$user = get_userdata( $user_id );
	$user_login = stripslashes($user->user_login);
	
	$message  = '<p>'.sprintf(__('Thank you for registering to %s.','ezemails'), $blogname) . "</p>";
	$message .= '<p>'.__('You can login using the following credentials', 'ezemails') . "<br/>";
	$message .= sprintf(__('username: %s'), '<strong>'.$user_login.'</strong>') . "<br/>";
	$message .= sprintf(__('password: %s'), '<strong>'.$plaintext_pass.'</strong>') . "</p>";
	$message .= "<p>" . wp_login_url() . "</p>";

	return $message;
}

/* 
Loads the resources for the languages
*/
function load_languages() {
	load_plugin_textdomain('ezemails', false, dirname( plugin_basename( __FILE__ ) )  . '/lang/' );
}
add_action('init', 'load_languages');

/* 
Enqueues the necessary scripts and styles in the <head> section
*/
function ezemails_enqueue_scripts() {

	// Adds the required dependency scripts
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-autocomplete');
	
	// Adds the required custom scripts to the header of the Admin pages
	wp_register_script('ezemails_ckeditor', plugins_url( '/js/ckeditor/ckeditor.js' , __FILE__ ), array('jquery'), null, false);
	wp_enqueue_script('ezemails_ckeditor');
	wp_register_script('ezemails_scripts', plugins_url( '/js/scripts.js' , __FILE__ ), array('jquery'), null, false);
	wp_enqueue_script('ezemails_scripts');

	// Adds the required styles to the header of the Admin pages
	wp_register_style('ezemails_style', plugins_url( '/css/styles.css' , __FILE__ ));
	wp_enqueue_style('ezemails_style');
	
	$ezemails_nonce = wp_create_nonce('ezemails_nonce');
	wp_localize_script( 'ezemails_scripts', 'ezemails_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => $ezemails_nonce ) );

}
add_action('admin_enqueue_scripts', 'ezemails_enqueue_scripts');

/* 
Adds the plugin in the Admin Menu
*/
function ezemails_add_menu() {
    add_submenu_page( 'tools.php', 'EZ Emails', 'EZ Emails', 'activate_plugins', 'ezemails', 'ezemails_admin_page' );
}
add_action('admin_menu','ezemails_add_menu');

function ezemmails_add_default_signature($user_id) {

	$signatures = array(
		'Default'	=>	'<p>'.$user->display_name.'<br/><a href="mailto:'.$user->user_email.'">'.$user->user_email.'</a></p>'
	);
	update_user_meta($user_id, 'ezemails_signatures', '<p></p>');

}
add_action('user_register', 'ezemmails_add_default_signature');

/*
Outputs the HTML code to create the admin page
*/
function ezemails_admin_page() {
	
	global $current_user;
	global $ezemails_options;
	
	if(isset($_POST['submit'])) {
		//Form data sent
		
		$ezemails_options['sender_name'] = $_POST['ezemails_sender_name'];
		$ezemails_options['sender_email'] = $_POST['ezemails_sender_email'];
		$ezemails_options['replace_template'] = $_POST['ezemails_replace_template'];
		$ezemails_options['override_all_mails'] = $_POST['ezemails_override_all_mails'];
		$ezemails_options['default_template'] = $_POST['ezemails_default_template'];
		update_option('ezemails_options', $ezemails_options);
?>
<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
<?php
	}  

	$ezemails_sender_name = $ezemails_options['sender_name'];
	$ezemails_sender_email = $ezemails_options['sender_email'];  
	$ezemails_replace_template = $ezemails_options['replace_template'];
	$ezemails_override_all_mails = $ezemails_options['override_all_mails'];
	$ezemails_default_template = $ezemails_options['default_template'];
	$templates = get_option('ezemails_templates');
	$signatures = get_user_meta($current_user->ID, 'ezemails_signatures', true);

	if (isset($_POST['ezemails_current_tab'])) {
		$current_tab = $_POST['ezemails_current_tab'];
	} else {
		$current_tab = 'ezemails-sendemail';
	}
	$tabs = array(
		'sendemail'		=>	__('Send email','ezemails'),
		'templates'		=>	__('Templates','ezemails'),
		'signatures'	=>	__('Signatures','ezemails'),
		'settings'		=>	__('Settings','ezemails'),
		'about'		=>	__('About','ezemails')
	);

?>

<div id="ezemails_page_overlay">
		<div id="ezemails_user_list">
			<h3>Add recipients</h3>
			<ul><?php
			$users = get_users('orderby=display_name');
			$ezemails_user_list = array();
			foreach ($users as $user) {
				$display_name = $user->display_name;
				$full_email = htmlspecialchars($user->display_name . ' <' . $user->user_email . '>');
				$ezemails_user_list[] = $user->display_name . ' <' . $user->user_email . '>';
?>
				<li><input type="checkbox" name="ezemails_users" value="<?php echo $full_email; ?>"> <?php echo $display_name; ?></li>
<?php
			}
?>
			</ul>
			<script type="text/javascript">
				var ezemailsUserList = <?php echo json_encode($ezemails_user_list); ?>;
			</script>
		<div id="ezemails_add_selected_users" class="button button-primary"><?php _e('Add'); ?></div>
		<div id="ezemails_cancel_selected_users" class="button button-secondary"><?php _e('Cancel'); ?></div>
		</div>
	</div>
	<div class="wrap">
		<div id="icon-ezemails" class="icon32"></div>
			<h2 class="nav-tab-wrapper">
		<?php _e( 'EZ Emails', 'ezemails' ); ?>&nbsp;
<?php
			foreach ($tabs as $tab => $title) {
?>
				<span id="ezemails-tab-<?php echo $tab; ?>" class="ezemails_tab nav-tab <?php if ($current_tab == 'ezemails-'.$tab) echo 'nav-tab-active';  ?>" style="cursor:pointer;">
					<?php echo $title; ?>
				</span>
<?php
			}
?>
			</h2>
		<form id="ezemails_form" name="ezemails_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<input type="hidden" name="ezemails_current_tab" id="ezemails_current_tab" value="ezemails-sendemail">

			<!-- SEND EMAIL TAB -->
			<div id="ezemails-sendemail" class="ezemails-option-page" style="<?php if ($current_tab != 'ezemails-sendemail') echo 'display:none';  ?>">
				<div class="ezemails_left">
					<table class="form-table ezemails">
						<tbody>
							<tr>
								<th scope="row">
									
								</th>
								<td>
									<div id="ezemails_send_now" class="button button-primary" style="width:100%;text-align:center;"><?php _e('Send email now','ezemails'); ?></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="ezemails_select_template"><?php _e('Template: ', 'ezemails' ); ?></label>
								</th>
								<td>
									<select id="ezemails_select_template" class="ezemails" name="ezemails_select_template">
<?php
									foreach ($templates as $name => $body) {
										$name = stripslashes($name);
?>
										<option value="<?php echo ezemails__validate_id($name); ?>"><?php echo $name; ?></option>
<?php
									}
?>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="ezemails_select_signature"><?php _e('Signature: ', 'ezemails' ); ?></label>
								</th>
								<td>
									<select id="ezemails_select_signature" class="ezemails" name="ezemails_select_signature">
<?php
									foreach ($signatures as $name => $body) {
										$name = stripslashes($name);
?>
										<option value="<?php echo ezemails__validate_id($name); ?>"><?php echo $name; ?></option>
<?php
									}
?>
									</select>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="ezemails_from"><?php _e('From: ', 'ezemails' ); ?></label>
								</th>
								<td>
									<select id="ezemails_from" class="ezemails" name="ezemails_from">
										<option id="ezemails_from_webmaster" value="webmaster"><?php echo '"'.$ezemails_sender_name.'" &lt;'.$ezemails_sender_email.'&gt;'; ?></option>
										<option id="ezemails_from_user" value="user"><?php echo '"'.$current_user->display_name.'" &lt;'.$current_user->user_email.'&gt;'; ?></option>
									</select>
									<input type="hidden" id="ezemails_from_value" name="ezemails_from_value"/>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="ezemails_to_addresses"><?php _e('To: ', 'ezemails' ); ?></label>
								</th>
								<td>
									<textarea id="ezemails_to_addresses" name="ezemails_to_addresses" class="no-border" rows="1"></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="ezemails_cc_addresses"><?php _e('Cc: ', 'ezemails' ); ?></label>
								</th>
								<td>
									<textarea id="ezemails_cc_addresses" name="ezemails_cc_addresses" class="no-border" rows="1"></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label for="ezemails_bcc_addresses"><?php _e('Bcc: ', 'ezemails' ); ?></label>
								</th>
								<td>
									<textarea id="ezemails_bcc_addresses" name="ezemails_bcc_addresses" class="no-border" rows="1"></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="ezemail_send_result" class="tooltip"></div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="ezemails_right">
					<table class="form-table ezemails">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="ezemails_send_subject"><?php _e('Subject: ', 'ezemails' ); ?></label>
								</th>
								<td>
									<input id="ezemails_send_subject" type="text" name="ezemails_send_subject" value="" style="width:100%">
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="ezemails_email_body"><?php _e('Message: ', 'ezemails' ); ?></label>
								</th>
								<td>
									<div id="ezemails_email_editor">
										<textarea id="ezemails_email_body" name="ezemails_email_body" cols="80" rows="10"></textarea>
										<script>
											CKEDITOR.replace('ezemails_email_body', {toolbar:'Standard'});
										</script>
										<textarea id="ezemails_final_email" name="ezemails_final_email" cols="80" rows="10" style="display:none;"></textarea>
									</div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="ezemails_email_preview"><?php _e('Preview: ', 'ezemails' ); ?></label>
								</th>
								<td>
									<div id="ezemails_email_preview">
										<div id="iframeBlocker"></div>
										<iframe id="ezemails_email_preview_frame" scrolling="no" frameborder="0"></iframe>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- TEMPLATES TAB -->
			<div id="ezemails-templates" class="ezemails-option-page" style="<?php if ($current_tab != 'ezemails-templates') echo 'display:none';  ?>">
				<div class="ezemails_left">
					<div id="ezemails_template_list">
						<!-- The table of template will be created here by Javascript using AJAX  -->
					</div>
				</div>
				<div class="ezemails_right">
					<label for="ezemails_template_name">Template name:</label><input type="text" id="ezemails_template_name" name="ezemails_template_name">
					<input type="hidden" id="ezemails_template_current" name="ezemails_template_current">
					<div id="ezemails_save_template" class="button button-primary" style="display:none;">Save</div>
					<div id="ezemails_cancel_template" class="button button-secondary" style="display:none;">Cancel</div>
<?php
						foreach ($templates as $name => $body) {
							$name = stripslashes($name);
							$body = stripslashes($body);
?>
							<input id="ezemails-template-name-<?php echo ezemails__validate_id($name); ?>" type="hidden" name="template_names[]" value="<?php echo $name; ?>">
							<textarea id="ezemails-template-body-<?php echo ezemails__validate_id($name); ?>" name="template_bodies[]" style="display:none;"><?php echo $body; ?></textarea>
<?php
						}
?>
					<div id="ezemails_template_editor">
						<textarea id="ezemails_template_body" id="ezemails_template_body"></textarea>
						<script>
							CKEDITOR.replace('ezemails_template_body', {fullPage: true});
						</script>
					</div>
					<p class="description">You <strong>MUST</strong> insert the variable <strong>%content%</strong> in any place of the template.<br/>You can also use the variable <strong>%signature%</strong> to be replaced by a signature of your choice.</p>
				</div>
			</div>
			
			<!-- SIGNATURES TAB -->
			<div id="ezemails-signatures" class="ezemails-option-page" style="<?php if ($current_tab != 'ezemails-signatures') echo 'display:none';  ?>">
				<div class="ezemails_left">
					<div id="ezemails_signature_list">
						<!-- The table of signature will be created here by Javascript using AJAX  -->
					</div>
				</div>
				<div class="ezemails_right">
					<label for="ezemails_signature_name">Signature name:</label><input type="text" id="ezemails_signature_name" name="ezemails_signature_name">
					<input type="hidden" id="ezemails_signature_current" name="ezemails_signature_current">
					<div id="ezemails_save_signature" class="button button-primary" style="display:none;">Save</div>
					<div id="ezemails_cancel_signature" class="button button-secondary" style="display:none;">Cancel</div>
<?php
						foreach ($signatures as $name => $body) {
							$name = stripslashes($name);
							$body = stripslashes($body);
							//$body = str_replace(
							//	array('%display_name%', '%user_email%'),
							//	array($current_user->display_name, $current_user->user_email),
							//	$body
							//);
?>
							<input id="ezemails-signature-name-<?php echo ezemails__validate_id($name); ?>" type="hidden" name="signature_names[]" value="<?php echo $name; ?>">
							<textarea id="ezemails-signature-body-<?php echo ezemails__validate_id($name); ?>" name="signature_bodies[]" style="display:none;"><?php echo $body; ?></textarea>
<?php
						}
?>
					<div id="ezemails_signature_editor">
						<textarea id="ezemails_signature_body" id="ezemails_signature_body"></textarea>
						<script>
							CKEDITOR.replace('ezemails_signature_body');
						</script>
					</div>
				</div>
			</div>
			
			<!-- SEND SETTINGS TAB -->
			<div id="ezemails-settings" class="ezemails-option-page" style="<?php if ($current_tab != 'ezemails-settings') echo 'display:none';  ?>">
				<?php echo "<h2>" . __( 'Sender information', 'ezemails' ) . "</h2>"; ?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="ezemails_sender_name"><?php _e('Sender name: ', 'ezemails' ); ?></label>
							</th>
							<td>
								<input type="text" name="ezemails_sender_name" value="<?php echo $ezemails_sender_name; ?>" size="40">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="ezemails_sender_email"><?php _e('Sender email: ', 'ezemails' ); ?></label>
							</th>
							<td>
								<input type="text" name="ezemails_sender_email" value="<?php echo $ezemails_sender_email; ?>" size="40">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="ezemails_replace_template"><?php _e('Replace WP notification emails: ', 'ezemails' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="ezemails_replace_template" <?php echo ($ezemails_replace_template) ? ' checked' : '' ; ?> />
								<span class="description">Check this option if you want to replace the template used by Wordpress for the notification emails when a user registers.</span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="ezemails_default_template"><?php _e('Template for notification emails: ', 'ezemails' ); ?></label>
							</th>
							<td>
								<select id="ezemails_default_template" class="ezemails" name="ezemails_default_template">
<?php
								foreach ($templates as $name => $body) {
									$name = stripslashes($name);
?>
									<option value="<?php echo $name; ?>"<?php echo ($ezemails_default_template == $name) ? ' selected' : '' ; ?>><?php echo $name; ?></option>
<?php
								}
?>
								</select>
								<p class="description">Select the template that replaces WP template.<br/>
									Please note that:<br/>
									- you have to check the option 'Use template for notification emails' in order to use this template<br/>
									- your template <strong>MUST</strong> include the token <strong>%content%</strong>, that will be replace with the original message
								</p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="ezemails_replace_template"><?php _e('Force use of template for all mails: ', 'ezemails' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="ezemails_override_all_mails" value="1"<?php echo ($ezemails_override_all_mails) ? ' checked' : '' ; ?> />
								<span class="description">Check this option if you want to force the use of your template for any email sent by Wordpress.</span>
								<p class="description"><strong>WARNING!</strong> This feature is still experimental and may interfere with plugins that use the WP mailing system.<br/>
									(e.g. any other customized template from other plugins will be incorporated into your EZ Emails template)<br/>
									A good practice to make EZ Emails live together with such other plugins is to let them manage just the <strong>content</strong> of the messages.
								</p>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input class="button button-primary" type="submit" name="submit" value="<?php _e('Save settings', 'ezemails' ) ?>" />
				</p>
			</div>
		</form>
		<div id="ezemails-about" class="ezemails-option-page" style="<?php if ($current_tab != 'ezemails-about') echo 'display:none';  ?>">
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="postbox-container-2" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable"><div id="ezemails_about-plugin-purpose" class="postbox ">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle"><span>Plugin Purpose</span></h3>
							<div class="inside">
								<p>
									<strong>EZ Emails v<?php echo $ezemails_options['version'];  ?></strong> (Easy Emails) allows WordPress administrators to create HTML templates for their email communications to registered users or manually typed in email addresses.
									With EZ Emails administrators can:<br/>
									- create as many HTML templates they want to be used as email templates<br/>
									- create as many HTML signatures they like to use (each user has their own personal list of signatures)<br/>
									- edit templates and signatures in a WYSIWYG editor or just in pure HTML<br/>
									- replace the default WordPress notification messages when users register with one of the templates created
									- force the whole WP mailing system to use EZ Emails templates
								</p>
							</div>
						</div>
						<div id="ezemails_about-more-information" class="postbox ">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle"><span>Support</span></h3>
							<div class="inside">
								<p>
									More information about EZ Emails can be found on its page in the <a href="http://wordpress.org/extend/plugins/ez-emails/">WordPress Plugin Directory</a>.
								</p>
								<p>
									Support is provided through the <a href="http://wordpress.org/support/plugin/ez-emails">WordPress Support Forums</a>.<br/>
									Before asking for support, please carefully read the <a href="http://wordpress.org/extend/plugins/ez-emails/faq/">Frequently Asked Questions</a>, where you will find answers to the most common questions, and search through the forums.
								</p>
								<p>
									If you do not find an answer there, please <a href="http://wordpress.org/support/plugin/ez-emails">open a new thread</a> in the WordPress Support Forums. 
								</p>
							</div>
						</div>
						<div id="ezemails_about-donations" class="postbox ">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle"><span>Donations</span></h3>
							<div class="inside">
								<p>
									Donations and good ratings encourage me to further develop the plugin and to provide countless hours of support. Any amount is appreciated! Thanks!
								</p>
								<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
									<input type="hidden" name="cmd" value="_s-xclick">
									<input type="hidden" name="hosted_button_id" value="VQE6XWAPU96TA">
									<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
									<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
								</form>
							</div>
						</div>
					</div>
					<div id="additional-sortables" class="meta-box-sortables ui-sortable"></div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<div id="ezemails_about-author-license" class="postbox ">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle"><span>Author and License</span></h3>
							<div class="inside">
								<p>
									This plugin was written and developed by <a href="http://www.luigipulcini.com/dev/">Luigi Pulcini</a>. 
									It is licensed as Free Software under GNU General Public License 2 (GPL 2).<br/>
									Please rate and review the plugin in the <a href="http://wordpress.org/support/view/plugin-reviews/ez-emails">WordPress Plugin Directory</a>.
								</p>
							</div>
						</div>
						<div id="ezemails_about-credits-thanks" class="postbox ">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle"><span>Credits and Thanks</span></h3>
							<div class="inside">
								<p>
									Credits:<br/>
									– <a href="http://ckeditor.com/" target="_blank">CKEditor</a> for the WYSIWYG editor<br/>
									– <a href="http://stackoverflow.com/">StackOverflow</a> for the limitless source of ideas, suggestions, advices and solutions.<br/>
									– <a href="http://profiles.wordpress.org/TobiasBg/" target="_blank">Tobias Bäthge</a> for inspiring the 'About' page<br/>
								</p>
								<p>
									Thank you to all donors, contributors, supporters, reviewers and users of the plugin!
								</p>
							</div>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	</div>
<?php
}

function ezemails__validate_id($value) {

	$value = strtolower($value);

	$patterns 		= array('/^[0-9]/',	'/[^0-9a-z-_]/'	);
	$replacements	= array('x',		'_'					);

	$result = preg_replace($patterns, $replacements, $value);
	return $result;
}

function ezemails_classes() {
	// Includes the required files to use the WP_List_Table class
	if(!class_exists('WP_List_Table')){
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	
	// Extends WP_List_Table class
	class EZEmails_Signature_Table extends WP_List_Table {
	
		var $signatures = array();
		
		function __construct(){
		global $status, $page;
	
			parent::__construct( array(
				'singular'  => __( 'signature', 'ezemails' ),     //singular name of the listed records
				'plural'    => __( 'signatures', 'ezemails' ),   //plural name of the listed records
				'ajax'      => true        //does this table support ajax?
		) );
		}
	
		function column_default( $item, $column_name ) {
			switch( $column_name ) { 
				case 'signature':
					return $item[ $column_name ];
				default:
					return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
			}
		}
	
		function get_columns(){
			$columns = array(
				'signature' => __( 'Signature', 'ezemails' )
			);
			return $columns;
		}
	
		function prepare_items() {
			$columns  = $this->get_columns();
			$hidden   = array();
			$sortable = array();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items = $this->signatures;
		}
		
		function column_signature($item) {
			$actions = array(
				'edit'      => '<span id="ezemails-signature-edit-'.$item['id'].'" class="ezemails-signature-edit cliccable"><a>'.__( 'Edit' ).'</a></span>',
				'trash'    => '<span id="ezemails-signature-delete-'.$item['id'].'" class="ezemails-signature-delete cliccable"><a>'.__( 'Delete' ).'</a></span>'
			);
			return sprintf('%1$s %2$s', $item['signature'], $this->row_actions($actions) );
		}
	
	}
	
	// Extends WP_List_Table class
	class EZEmails_Template_Table extends WP_List_Table {
	
		var $templates = array();
		
		function __construct(){
		global $status, $page;
	
			parent::__construct( array(
				'singular'  => __( 'template', 'ezemails' ),     //singular name of the listed records
				'plural'    => __( 'templates', 'ezemails' ),   //plural name of the listed records
				'ajax'      => true        //does this table support ajax?
		) );
		}
	
		function column_default( $item, $column_name ) {
			switch( $column_name ) { 
				case 'template':
					return $item[ $column_name ];
				default:
					return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
			}
		}
	
		function get_columns(){
			$columns = array(
				'template' => __( 'Template', 'ezemails' )
			);
			return $columns;
		}
	
		function prepare_items() {
			$columns  = $this->get_columns();
			$hidden   = array();
			$sortable = array();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items = $this->templates;
		}
		
		function column_template($item) {
			$actions = array(
				'edit'      => '<span id="ezemails-template-edit-'.$item['id'].'" class="ezemails-template-edit cliccable"><a>'.__( 'Edit' ).'</a></span>',
				'trash'    => '<span id="ezemails-template-delete-'.$item['id'].'" class="ezemails-template-delete cliccable"><a>'.__( 'Delete' ).'</a></span>'
			);
			return sprintf('%1$s %2$s', $item['template'], $this->row_actions($actions) );
		}
	}
}
add_action('plugins_loaded','ezemails_classes');
	
function ezemails_templates_callback() {
	$names = $_POST['template_names'];
	$bodies = $_POST['template_bodies'];
	$templates = array();
	
	if (count($names)) {
		$names = array_map('htmlspecialchars',$names);
		$bodies = array_map('htmlspecialchars',$bodies);
		$template_list = array_combine($names, $bodies);
		ksort($template_list);
		// delete_option('ezemails_templates');
		update_option('ezemails_templates',$template_list);
		$template_list = get_option('ezemails_templates');
		$names = array_keys($template_list);
		$bodies = array_values($template_list);
		foreach ($names as $name) {
			$templates[] = array('template' => $name, 'id' => ezemails__validate_id($name));
		}
	} else {
		// delete_option('ezemails_templates');
	}
	
	echo '<h2>'.__( 'Templates', 'ezemails' ).'<a id="ezemails_new_template" href="javascript:void(0);" class="add-new-h2">Add New</a></h2>';
	
	echo '<div id="ezemails_template_table_wrap">';
		echo '<div id="ezemails_template_table_overlay"></div>';
	
		$ezTemplateTable = new EZEmails_Template_Table();
		$ezTemplateTable->templates = $templates;
		$ezTemplateTable->prepare_items();
		$ezTemplateTable->display();
	echo '</div>';
	die();
}
add_action('wp_ajax_ezemails_template_list','ezemails_templates_callback');

function ezemails_signatures_callback() {
	global $current_user;
	
	$names = $_POST['signature_names'];
	$bodies = $_POST['signature_bodies'];
	$signatures = array();
	
	if (count($names)) {
		$names = array_map('htmlspecialchars',$names);
		$bodies = array_map('htmlspecialchars',$bodies);
		$signature_list = array_combine($names, $bodies);
		ksort($signature_list);
		// delete_user_meta($current_user->ID, 'ezemails_signatures');
		update_user_meta($current_user->ID, 'ezemails_signatures', $signature_list);
		$signature_list = get_user_meta($current_user->ID, 'ezemails_signatures', true);
		$names = array_keys($signature_list);
		$bodies = array_values($signature_list);
		foreach ($names as $name) {
			$signatures[] = array('signature' => $name, 'id' => ezemails__validate_id($name));
		}
	} else {
		// delete_user_meta($current_user->ID, 'ezemails_signatures');
	}
	
	echo '<h2>'.__( 'Signatures', 'ezemails' ).'<a id="ezemails_new_signature" href="javascript:void(0);" class="add-new-h2">Add New</a></h2>';
	
	echo '<div id="ezemails_signature_table_wrap">';
		echo '<div id="ezemails_signature_table_overlay"></div>';
	
		$ezSignatureTable = new EZEmails_Signature_Table();
		$ezSignatureTable->signatures = $signatures;
		$ezSignatureTable->prepare_items();
		$ezSignatureTable->display();
	echo '</div>';
	die();
}
add_action('wp_ajax_ezemails_signature_list','ezemails_signatures_callback');

function ezemails_send_callback() {
	global $ezemails_options;

	$message = '';
	$addresses = $_POST['addressList'];
	if (sizeof($addresses) == 0) {
		ezemails__message(__('You have to select at least one recipient', 'ezemails'));
		die();
	}
	$from = htmlspecialchars_decode(stripslashes($_POST['from']));
	if ($from == '') {
		ezemails__message(__('You have not selected a valid sender address', 'ezemails'));
		die();
	}
	$subject = htmlspecialchars_decode(stripslashes($_POST['subject']));
	if ($subject == '') {
		ezemails__message(__('You cannot send an email without a subject', 'ezemails'));
		die();
	}
	$body = htmlspecialchars_decode(stripslashes($_POST['body']));
	$to_list  = array();
	$ccbcc_list  = array();
	foreach ($addresses as $address) {
		if ($address['type'] == 'to') {
			$to_list[] = htmlspecialchars_decode(stripslashes(($address['name'] != '') ? $address['name'] . ' <' . $address['email'] .'>' : $address['email']));
		} else {
			$ccbcc_list[] = $address['type'] . ': ' . htmlspecialchars_decode(stripslashes(($address['name'] != '') ? $address['name'] . ' <' . $address['email'] .'>' : $address['email']));
		}
	}
	
	$headers = array();
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=utf-8';
	$headers[] = 'From: '.$from;
	foreach ($ccbcc_list as $item) {
		$headers[] = $item;
	}
	
	$result = array();
	$format = '
		<div class="%2$s ezemails">
			%1$s
		</div>
	';
	
	if ($ezemails_options['override_all_mails']) remove_action('phpmailer_init', 'ezemails_phpmailer', 10, 1);
	
	if (wp_mail($to_list, $subject, $body, $headers)) {
		$result['err'] = 0;
		$result['html'] = sprintf($format, __('The email was successfully sent to all the recipients','ezemails'), 'updated');
	} else {
		$result['err'] = 1;
		$result['html'] = sprintf($format, __('An error occurred while sending this message','ezemails'), 'error');
	}
	echo json_encode($result);
	if ($ezemails_options['override_all_mails']) add_action('phpmailer_init', 'ezemails_phpmailer', 10, 1);
	die();
}
add_action('wp_ajax_ezemails_sendemail','ezemails_send_callback');

?>