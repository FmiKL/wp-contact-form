<?php

namespace Mf\SFC_Contact;

/**
 * Form submission.
 */
class Sender {
    /**
     * The receiver key.
     */
    public const RECEIVER_KEY = 'contact_mail_receiver';

    /**
     * The nonce key.
     */
    public const NONCE_KEY = '_contact_nonce_field';

    /**
     * Constructor.
     *
     * @param array $data
     * @param Validator $validator
     * @param array $error_cases
     * @param string $subject
     * @param string $success
     */
    public function __construct(
        private $data,
        private $validator,
        private $error_cases,
        private $subject,
        private $success,
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
            $this->http_response_message( 403, array( 'error' => 'Forbidden' ) );
        }

        foreach ( $this->error_cases as $error ) {
            $function_name = $error->validator();

            if ( ! method_exists( $this->validator, $function_name ) ) {
                throw new \Exception( 'Non-existent validation function : ' . $function_name );
            }

            $this->validator->{$function_name}( $error->name(), get_option( $error->value ) );
        }

        if ( $this->validator->is_valid() ) {
            $this->send_message();
        }

        $this->http_response_message(400, [
            'error'    => 'Bad Request',
            'messages' => $this->validator->get_errors(),
        ]);
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

    /**
     * Send the message.
     * 
     * @return void
     */
    private function send_message() { 
        wp_mail(
            get_option( self::RECEIVER_KEY ),
            get_option( $this->subject ),
            $this->get_mail_content(),
            $this->get_mail_headers()
        );
        
        $success = array( 'success' => get_option( $this->success ) );
        $this->http_response_message( 200, $success );
    }

    /**
     * Get the mail headers.
     *
     * @return array
     */
    private function get_mail_headers() {
        $name = str_replace( array( "\r", "\n" ), '', $_POST['name'] );
        $email = str_replace( array( "\r", "\n" ), '', $_POST['email'] );

        return array(
            'From: ' . $name . ' <' . $email  . '>',
            'Reply-To: ' . $email ,
            'Content-Type: text/html; charset=UTF-8',
        );
    }

    /**
     * Get the content of the email.
     *
     * @return string
     */
    private function get_mail_content() {
        $message = html_entity_decode( $this->data['message'] );
        $message = nl2br( wordwrap( $message , 70, PHP_EOL ) );

        return $message;
    }

    /**
     * Display an HTTP response with JSON format.
     *
     * @param integer $code
     * @param array $response
     * @return void
     */
    private function http_response_message( $code, $response ) {
        status_header( $code );
        echo wp_json_encode( $response );
        exit;
    }
}
