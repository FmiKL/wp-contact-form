<?php

namespace Mf\SFC_Contact;

/**
 * Enum the different values used
 * to handle errors.
 */
enum Error : string {
    /**
     * The keys used.
     */
    case Name    = 'contact_name_error';
    case Email   = 'contact_email_error';
    case Message = 'contact_message_error';

    /**
     * Return the title of the error.
     *
     * @return string
     */
    public function title() {
        return match ( $this ) {
            self::Name    => 'Name error',
            self::Email   => 'Email error',
            self::Message => 'Message Error',
        };
    }

    /**
     * Return the message to display
     * in case of error.
     *
     * @return string
     */
    public function message() {
        return match ( $this ) {
            self::Name    => 'Enter your name!',
            self::Email   => 'Your email is invalid!',
            self::Message => 'Enter your message!',
        };
    }

    /**
     * Return the name used for the input.
     *
     * @return string
     */
    public function name() {
        return match ( $this ) {
            self::Name    => 'name',
            self::Email   => 'email',
            self::Message => 'message',
        };
    }
}
