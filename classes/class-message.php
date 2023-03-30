<?php

namespace Mf\SFC_Contact;

/**
 * Enum the different values used
 * to handle messages.
 */
enum Message : string {
    /**
     * The keys used.
     */
    case Subject = 'contact_mail_subject';
    case Success = 'contact_success_message';

    /**
     * Return the label to use.
     *
     * @return string
     */
    public function label() {
        return match ( $this ) {
            self::Subject => 'Email title',
            self::Success => 'Success message',
        };
    }

    /**
     * Return the message to use.
     *
     * @return string
     */
    public function info() {
        return match ( $this ) {
            self::Subject => 'Contact message.',
            self::Success => 'Thank you for your message!',
        };
    }
}
