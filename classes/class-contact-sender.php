<?php
/**
 * Class responsible for sending the contact form data via email.
 * 
 * @package WP_Contact_Form
 * @author Mikael Fourré
 * @version 2.3.0
 * @see https://github.com/FmiKL/wp-contact-form
 */
class Contact_Sender {
    /**
     * Key used to retrieve the email field.
     * 
     * @var string
     * @since 2.0.0
     */
    private const EMAIL_FIELD_KEY_USED = 'email';

    /**
     * Key used to retrieve the subject field.
     * 
     * @var string
     * @since 2.0.0
     */
    private const SUBJECT_FIELD_KEY_USED = 'subject';

    /**
     * Subject used by default if none is provided.
     * 
     * @var string
     * @since 2.0.0
     */
    private const SUBJECT_DEFAULT = 'Contact';

    /**
     * Fields to be included in the email.
     * 
     * @var array<array>
     * @since 2.0.0
     * @see Contact_Manager::add_field()
     */
    private $fields;
    
    /**
     * Data sent via the contact form.
     * 
     * @var array<string, string>
     * @since 1.0.0
     */
    private $data;

    /**
     * @param array $fields Fields to be included in the email.
     * @param array $data   Data sent via the contact form.
     * @since 1.0.0
     */
    public function __construct( $fields, $data ) {
        $this->fields = $fields;
        $this->data   = $data;
    }

    /**
     * Sends the email to the specified receiver.
     * 
     * @param string $sender Sender of the email.
     * @param string $receiver Email address to send the email to.
     * @since 1.0.0
     * @link https://developer.wordpress.org/reference/functions/wp_mail/
     */
    public function send_to( $sender, $receiver ) {
        wp_mail( $receiver, $this->get_subject(), $this->get_content(), $this->get_headers( $sender ) );
    }

    /**
     * Returns a test email as an array.
     * 
     * @param string $sender Sender of the email.
     * @return array Test email as an array.
     * @since 2.0.0
     */
    public function send_test( $sender ) {
        return array(
            'Subject: ' . $this->get_subject() . "\n",
            'Content: ' . $this->get_content() . "\n",
            'Headers: ' . print_r( $this->get_headers( $sender ), true ) . "\n",
        );
    }

    /**
     * Returns the headers.
     * 
     * @param string $sender Sender of the email.
     * @return array Headers to be used in the email.
     * @since 1.0.0
     */
    private function get_headers( $sender ) {
        $site_url = get_site_url();
        $domain   = parse_url( $site_url, PHP_URL_HOST );

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $domain . ' <' . $sender . '>'
        );

        if ( ! empty( $this->data[ self::EMAIL_FIELD_KEY_USED ] ) ) {
            $email = $this->sanitize_data( $this->data[ self::EMAIL_FIELD_KEY_USED ] );
            $headers[] = 'Reply-To: <' . $email . '>';
        }

        return $headers;
    }

    /**
     * Sanitize data by removing "\r" and "\n".
     * 
     * @param string  $data Data to be sanitized.
     * @return string Sanitized data.
     * @since 2.1.1
     */
    private function sanitize_data( $data ) {
        return str_replace( array( "\r", "\n" ), '', $data );
    }

    /**
     * Returns the subject.
     * 
     * @return string Subject of the email.
     * @since 1.0.0
     */
    private function get_subject() {
        return stripslashes( $this->data[ self::SUBJECT_FIELD_KEY_USED ] ?? self::SUBJECT_DEFAULT );
    }

    /**
     * Returns the content.
     * 
     * @return string Content of the email.
     * @since 1.0.0
     */
    private function get_content() {
        $message = '';
        foreach ( $this->fields as $field ) {
            if ( ! isset( $this->data[ $field['name'] ] ) && $field['type'] !== 'checkbox' ) {
                throw new Exception( $field['name'] . ' is missing!' );
            }

            $label = stripslashes( esc_html( $field['label'] ?? '' ) );

            if ( $field['type'] === 'checkbox' ) {
                $value = $this->data[ $field['name'] ] ?? '' === 'on' ? '&check;' : '&cross;';
            } else {
                $value = stripslashes( esc_html( html_entity_decode( $this->data[ $field['name'] ] ?: '_____' ) ) );
            }

            if ( $label ) {
                $message .= '<p><strong>' . $label . '</strong> ' . $value . '</p>';
            } else {
                $message .= '<p>' . $value . '</p>';
            }
        }

        return '<html><body>' . $message . '</body></html>';
    }
}
