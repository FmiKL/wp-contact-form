<?php
/**
 * Class responsible for validating the data from a contact form.
 * 
 * @package WP_Contact_Form
 * @author Mikael Fourré
 * @version 2.1.2
 * @see https://github.com/FmiKL/wp-contact-form
 */
class Contact_Validator {
    /**
     * Data to be validated.
     *
     * @var array<string, string>
     * @since 1.0.0
     */
    private $data;
    
    /**
     * An array to hold any errors found during validation.
     *
     * @var array<string, int>
     * @since 1.0.0
     * @see Contact_Validator::add_error()
     */
    private $errors = array();

    /**
     * @param array $data Data to be validated.
     * @since 1.0.0
     */
    public function __construct( $data )
    {
        $this->data = $data;
    }

    /**
     * Checks the validity of the fields.
     * 
     * @param array $fields Fields to check the validity of.
     * @since 2.0.0
     */
    public function check( $fields ) {
        foreach ( $fields as $field ) {
            if ( isset( $field['options']['required'] ) ) {
                $required_field = $field['options']['required'];
                if ( empty( $this->data[ $field['name'] ] ) && empty( $this->data[ $required_field ] ) ) {
                    $this->add_error( $field['name'], false );
                } elseif ( ! empty( $this->data[ $field['name'] ] ) ) {
                    switch ( $field['type'] ) {
                        case 'email':
                            $this->is_email( $field['name'] );
                            break;
                        case 'tel':
                            $this->is_phone( $field['name'] );
                            break;
                    }
                }
            }
        }
    }

    /**
     * Checks for recorded errors.
     * 
     * @param string|null $key Specific key to check for errors. If null, checks for any errors.
     * @return bool       Returns true if no errors found, false otherwise.
     * @since 1.0.0
     */
    public function is_valid( $key = null ) {
        if ( $key ) {
            return ! array_key_exists( $key, $this->errors );
        }
        return empty( $this->errors );
    }

    /**
     * Gets the errors.
     * 
     * @return array Errors found during validation.
     * @since 1.0.0
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Checks if the value is a valid email.
     * 
     * @param string $key Key to check the associated value of.
     * @return bool  Whether the value associated with the key is a valid email.
     * @since 1.0.0
     */
    private function is_email( $key ) {
        $is_valid = filter_var( $this->data[ $key ], FILTER_VALIDATE_EMAIL );
        $this->add_error( $key, $is_valid );

        return $is_valid;
    }

    /**
     * Checks if the value is a valid phone number.
     * 
     * @param string $key Key to check the associated value of.
     * @return bool  Whether the value associated with the key is a valid phone number.
     * @since 2.0.0
     */
    private function is_phone( $key ) {
        $is_valid = preg_match( '/^0[1-9](?:[\s]?[0-9]{2}){4}$/', $this->data[ $key ] );
        $this->add_error( $key, $is_valid );

        return $is_valid;
    }

    /**
     * Adds an error for a key if the value is not valid.
     * 
     * @param string $key      Key to add an error for.
     * @param bool   $is_valid Whether the value associated with the key is valid.
     * @since 1.0.0
     */
    private function add_error( $key, $is_valid ) {
        if ( ! $is_valid ) {
            $this->errors[ $key ] = 1;
        }
    }
}
