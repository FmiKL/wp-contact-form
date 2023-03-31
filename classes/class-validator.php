<?php

namespace Mf\SFC_Contact;

/**
 * Form data validator.
 */
class Validator {
    /**
     * The error table.
     *
     * @var array
     */
    private array $errors = [];

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(
        private $data,
    ){}

    /**
     * Return "true" if the form is valid.
     *
     * @return boolean
     */
    public function is_valid() {
        return empty( $this->errors );
    }

    /**
     * Get error messages.
     *
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Add a message in case of error.
     *
     * @param string $key
     * @param boolean $isValid
     * @param string $message
     * @return void
     */
    private function add_message_error( $key, $is_valid, $message = 'Invalid field!' ) {
        if ( ! $is_valid ) {
            $this->errors[ $key ] = $message;
        }
    }

    /**
     * Return "true" if the field has been filled.
     *
     * @param string $key
     * @param string $message
     * @return boolean
     */
    public function is_not_empty( $key, $message ) {
        $is_valid = ! empty( $this->data[ $key ] );
        $this->add_message_error( $key, $is_valid, $message );

        return $is_valid;
    }

    /**
     * Return "true" if the value has a
     * valid mail format.
     *
     * @param string $key
     * @param string $message
     * @return boolean
     */
    public function is_email( $key, $message ) {
        $is_valid = isset( $this->data[ $key ] ) && filter_var( $this->data[ $key ], FILTER_VALIDATE_EMAIL );
        $this->add_message_error($key, $is_valid, $message);

        return $is_valid;
    }
}
