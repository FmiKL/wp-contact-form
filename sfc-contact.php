<?php

namespace Mf\SFC_Contact;

/**
 * Plugin Name: SFC Contact
 * Description: This plugin offers the possibility to create a contact form on your website with a fully customizable code structure.
 * Author: Mikael Fourré
 * Version: 1.0.0
 */

if ( ! function_exists( 'add_action' ) ) exit;

define( 'SFC_CONTACT_PATH', plugin_dir_path( __FILE__ ) );
define( 'SFC_CONTACT_URL', plugin_dir_url( __FILE__ ) );

require_once SFC_CONTACT_PATH . 'classes/class-error.php';
require_once SFC_CONTACT_PATH . 'classes/class-message.php';
require_once SFC_CONTACT_PATH . 'classes/class-option.php';
require_once SFC_CONTACT_PATH . 'classes/class-form.php';
require_once SFC_CONTACT_PATH . 'classes/class-validator.php';
require_once SFC_CONTACT_PATH . 'classes/class-sender.php';

$error_cases = Error::cases();
$message_cases = Message::cases();

$option = new Option( SFC_CONTACT_PATH, $error_cases, $message_cases );
$option->init();

register_activation_hook( __FILE__, array( $option, 'save_settings' ) );
register_deactivation_hook( __FILE__, array( $option, 'delete_settings' ) );

$form = new Form( SFC_CONTACT_PATH, SFC_CONTACT_URL );
$form->init();

if ( isset( $_POST[ Sender::NONCE_KEY ] ) ) {
    $validator = new Validator( $_POST );
    $subject = Message::Subject->value;
    $success = Message::Success->value;
    
    $sender = new Sender( $_POST, $validator, $error_cases, $subject, $success );
    $sender->init();
}
