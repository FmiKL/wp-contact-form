<?php
/**
 * Class responsible for validating the data from a contact form.
 * 
 * @package WP_Contact_Form
 * @author Mikael Fourré
 * @version 2.3.3
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
            $value    = $this->data[ $field['name'] ] ?? null;
            $required = $field['options']['required'] ?? null;

            if ( $required === true || is_string( $required ) ) {
                $required_value = is_string( $required ) ? ( $this->data[ $required ] ?? null ) : null;
                if ( ! $this->has_value( $value ) && ( $required === true || ! $this->has_value( $required_value ) ) ) {
                    $this->add_error( $field['name'], false );
                    continue;
                }
            }

            if ( $this->has_value( $value ) ) {
                switch ( $field['type'] ) {
                    case 'email':
                        $this->is_email( $field['name'] );
                        break;
                    case 'tel':
                        $this->is_phone( $field['name'] );
                        break;
                    case 'url':
                        $this->is_url( $field['name'] );
                        break;
                    case 'number':
                        $this->is_number( $field['name'] );
                        break;
                    case 'select':
                        $this->is_choice( $field );
                        break;
                    case 'checkbox':
                        $this->is_checkbox( $field['name'] );
                        break;
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
     * Checks if a value should be treated as present.
     *
     * @param mixed $value Value to check.
     * @return bool True when value is not null or empty string.
     * @since 2.3.3
     */
    private function has_value( $value ) {
        return $value !== null && $value !== '';
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
     * Checks if the value is a valid URL.
     *
     * @param string $key Key to check the associated value of.
     * @return bool Whether the value associated with the key is a valid URL.
     * @since 2.3.3
     */
    private function is_url( $key ) {
        $is_valid = filter_var( $this->data[ $key ], FILTER_VALIDATE_URL );
        $this->add_error( $key, $is_valid );

        return $is_valid;
    }

    /**
     * Checks if the value is a valid number.
     *
     * @param string $key Key to check the associated value of.
     * @return bool Whether the value associated with the key is a valid number.
     * @since 2.3.3
     */
    private function is_number( $key ) {
        $is_valid = is_numeric( $this->data[ $key ] );
        $this->add_error( $key, $is_valid );

        return $is_valid;
    }

    /**
     * Checks if the value is a valid choice.
     *
     * @param array $field Field definition.
     * @return bool Whether the value is one of the allowed choices.
     * @since 2.3.3
     */
    private function is_choice( $field ) {
        $choices = $field['options']['choices'] ?? null;
        if ( ! is_array( $choices ) ) {
            return true;
        }

        $options  = array_values( $choices );
        $value    = $this->data[ $field['name'] ] ?? '';
        $is_valid = in_array( $value, $options, true );
        $this->add_error( $field['name'], $is_valid );

        return $is_valid;
    }

    /**
     * Checks if the value is a valid checkbox value.
     *
     * @param string $key Key to check the associated value of.
     * @return bool Whether the value is a valid checkbox value.
     * @since 2.3.3
     */
    private function is_checkbox( $key ) {
        $value    = $this->data[ $key ];
        $is_valid = in_array( $value, array( '0', '1' ), true );
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
