<?php
/**
 * Trait provides methods for handling the security of the contact form.
 * 
 * @package WP_Contact_Form
 * @author Mikael Fourré
 * @version 2.1.2
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

    /**
     * Adds the security fields to the form.
     * 
     * @since 1.0.0
     */
    protected function add_security_fields() {
        wp_nonce_field( $this->action_key, $this->nonce_key );
        echo '<input type="text" name="' . esc_attr( $this->honeypot_key ) . '">';
        echo '<input type="hidden" name="_ajax_key" value="' . esc_attr( $this->ajax_key ) . '">';
    }

    /**
     * Checks the security of the form submission.
     * 
     * @param array $data Form data to check.
     * @return bool Returns true if the form submission is secure, false otherwise.
     * @since 1.0.0
     */
    protected function check_security( $data ) {
        if (
            ! isset( $data[ $this->nonce_key ] ) ||
            ! wp_verify_nonce( $data[ $this->nonce_key ], $this->action_key )
        ) {
            return false;
        }

        if ( ! empty( $data[ $this->honeypot_key ] ) ) {
            return false;
        }

        return true;
    }
}
