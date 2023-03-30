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

require_once SFC_CONTACT_PATH . 'classes/class-form.php';

$form = new Form( SFC_CONTACT_PATH, SFC_CONTACT_URL );
$form->init();
