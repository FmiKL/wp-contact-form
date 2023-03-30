<?php

namespace Mf\SFC_Contact;

/**
 * Form submission.
 */
class Sender {
    /**
     * The nonce key.
     */
    public const NONCE_KEY = '_contact_nonce_field';

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(
        private $data,
    ){}

    /**
     * Initialize the methods.
     *
     * @return void
     */
    public function init() {
        add_action( 'wp_ajax_sfc_contact_send', array( $this, 'send_handle' ) );
        add_action( 'wp_ajax_nopriv_sfc_contact_send', array( $this, 'send_handle' ) );
    }

    /**
     * Handle form submission.
     *
     * @return void
     */
    public function send_handle() {
        if ( $this->is_invalid_nonce( 'contact', self::NONCE_KEY ) ) {
            echo wp_json_encode( array( 'error' => 'Forbidden' ) );
            exit;
        }

        // TODO: Replace with logic to submit the form
        echo wp_json_encode( array( 'data' => $this->data ) );
        exit;
    }

    /**
     * Return "true" if the form has been
     * sent from the site.
     *
     * @param string $key
     * @param string $nonce
     * @return boolean
     */
    private function is_invalid_nonce( $key, $nonce ) {
        return ! isset( $this->data[ $nonce ] ) || ! wp_verify_nonce( $this->data[ $nonce ], $key );
    }
}
