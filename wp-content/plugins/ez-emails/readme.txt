=== EZ Emails ===
Contributors: luigipulcini
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VQE6XWAPU96TA
Tags: EMails,HTML Emails, Email Template, User Registration Template, Template, Signature, HTML Signature, Email Signature
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.3.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

EZ Emails (Easy Emails) allows the administrators of a Wordpress site to send emails to users or manually typed email addresses, based on HTML templates.

== Description ==

**EZ Emails** (Easy Emails) allows WordPress administrators to create HTML templates for their email communications to registered users or manually typed in email addresses.

With EZ Emails administrators can:

* create as many HTML templates they want to be used as email templates
* create as many HTML signatures they like to use (each user has their own personal list of signatures)
* edit templates and signatures in a WYSIWYG editor or just in pure HTML
* replace the default WordPress notification message when users register with one of the templates created
* force all the emails sent by Wordpress to use your EZ Emails template

== Installation ==

1. Unpack the 'ez-emails.zip' file and upload its content to the `/wp-content/plugins/` directory of your Wordpress installation
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on the 'Control Panel' link under the name of the plugin or go to the menu 'Tools -> EZ Emails'


== Frequently asked questions ==

= Can I use one of the templates I created to replace the default message Wordpress send to registered users? =

Yes, after creating your favorite template, you can go to the 'Settings' tab and select the template you have just created as a replacement for Wordpress default template.

= Is it possible to create different signatures for each user? =

Yes, EZ Emails gives the ability to create as many signatures as you want. Each user can access only their own signatures.

== Screenshots ==

1. Main section of the plugin where the administrator can compose and send emails to registered users and typed in email addresses
2. Template Tab. Here it is possible to create your favorite templates in a WYSIWYG editor with HTML editing capabilities
3. In the 'Settings' tab the administrator can set the options for the plugin

== Changelog ==

= 1.3.6 =
* Bugfix: content of new registration message shows HTML code as plain text

= 1.3.5 =
* Bugfix: lost password emails not showing the link to reset the password

= 1.3.4 =
* Bugfix: template duplication and nidification in emails sent within the 'Send email' panel when using 'Force use of template for all mails' option

= 1.3.3 =
* Bugfix: undefined variable $email_nonce on installation

= 1.3.2 =
* Corrected the current version number

= 1.3.1 =
* Bugfix: Removed a file saving feature for debugging purposes

= 1.3 =
* Added a new option: now you can force ALL the emails sent by Wordpress to use your EZ Emails template. This may solve some compatibility issues with other plugins using the Wordpress mailing system.

= 1.2 =
* Bugfix: user registration notification email not sent using template or not sent at all

= 1.1 =
* Better AJAX integration in Wordpress

= 1.0 =
* First release