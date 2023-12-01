<?php
/**
 * Trait provides methods for handling the security of the contact form.
 * 
 * @package WP_Contact_Form
 * @author Mikael Fourré
 * @version 2.0.0
 * @see https://github.com/FmiKL/wp-contact-form
 */
trait Contact_Security {
    /**
     * Key used for the honeypot field.
     * 
     * @var string
     * @since 1.0.0
     */
    protected $honeypot_key = 'required';

    /**
     * Key used for the nonce field.
     * 
     * @var string
     * @since 1.0.0
     */
    protected $nonce_key;
    
    /**
     * Key used for the action associated with the nonce.
     * 
     * @var string
     * @since 1.0.0
     */
    protected $action_key;

    /**
     * Key used for the AJAX request.
     * 
     * @var string
     * @since 1.0.0
     */
    protected $ajax_key;

    /**
     * Sets the security keys based on the provided shortcode.
     * 
     * @param string $shortcode Shortcode used to create the form.
     * @since 1.0.0
     */
    protected function set_security_key( $shortcode ) {
        $key = str_replace( '-', '_', $shortcode );
        $this->nonce_key  = $key . '_nonce';
        $this->action_key = 'send-' . $key;
        $this->ajax_key   = $key . '_send';
    }
}
