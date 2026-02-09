<?php
/**
 * Class used to manage the construction of the contact form
 * and the associated request.
 * 
 * @package WP_Contact_Form
 * @author Mikael Fourré
 * @version 2.3.3
 * @see https://github.com/FmiKL/wp-contact-form
 */
class Contact_Manager {
    use Contact_Security;

    /**
     * Hosts that are considered as test mode.
     * 
     * @var array
     * @since 2.0.0
     */
    private const TEST_MODE = array( 'localhost', '127.0.0.1' );

    /**
     * Shortcode for the contact form.
     *
     * @var string
     * @since 1.0.0
     */
    private $shortcode;

    /**
     * Data handled by the contact form.
     *
     * @var array<string, string>
     * @since 1.0.0
     * @see Contact_Manager::set_options()
     */
    private $data;

    /**
     * Sender of the contact form.
     *
     * @var string
     * @since 2.2.0
     */
    private $sender;

    /**
     * Receiver of the contact form.
     *
     * @var string
     * @since 1.0.0
     */
    private $receiver;

    /**
     * Options of the contact form.
     *
     * @var array<string, string>
     * @since 2.1.0
     */
    private $options;

    /**
     * Fields to add to the contact form.
     *
     * @var array<array>
     * @since 2.0.0
     * @see Contact_Manager::add_field()
     */
    private $fields = array();

    /**
     * Grouped fields wrapped in a template.
     *
     * @var array<string, array>
     * @since 2.0.0
     * @see Contact_Manager::group_fields()
     */
    private $groups = array();

    /**
     * @param string $shortcode Shortcode for the contact form.
     * @param mixed  $emails    Sender and receiver of the contact form. Can be a string (same sender and receiver) or an array with 'sender' and 'receiver' keys.
     * @param array  $options   Options for the form, which can include:
     *                          - post: If is used, data will be set accordingly. Otherwise, $_POST will be used.
     *                          - class: CSS class for the form.
     * @since 1.0.0
     */
    public function __construct( $shortcode, $emails, $options = array() ) {
        $this->set_security_key( $shortcode );
        $this->set_options( $options );

        $this->shortcode = $shortcode;

        if ( is_array( $emails ) ) {
            $this->sender   = $emails['sender'] ?? '';
            $this->receiver = $emails['receiver'] ?? '';
        } else {
            $this->sender   = $emails;
            $this->receiver = $emails;
        }
    }

    /**
     * Sets the form options.
     *
     * @param array $options Options for the form.
     * @since 2.1.0
     */
    private function set_options( $options ) {
        $this->options = $options;

        if ( array_key_exists( 'post', $options ) ) {
            $this->data = $options['post'];
        } else {
            $this->data = $_POST;
        }
    }

    /**
     * Handles the request.
     * 
     * @since 1.0.0
     */
    public function handle_request() {
        add_action( 'wp_ajax_' . $this->ajax_key, array( $this, 'handle' ) );
        add_action( 'wp_ajax_nopriv_' . $this->ajax_key, array( $this, 'handle' ) );
    }

    /**
     * Creates the contact form.
     * 
     * @since 1.0.0
     */
    public function create_form() {
        $form = new Contact_Form( $this->shortcode, $this->options, $this->fields, $this->groups );
        $form->setup();
    }

    /**
     * Handles the form submission.
     * 
     * @since 1.0.0
     */
    public function handle() {
        if ( ! $this->check_security( $this->data ) ) {
            $this->http_response_message( 403, array( 'message' => 'Forbidden' ) );
        }

        $data_fields = $this->get_data_fields();
        $this->sanitize_data_fields( $data_fields );

        $validator = new Contact_Validator( $this->data );
        $validator->check( $data_fields );

        if ( $validator->is_valid() ) {
            $sender = new Contact_Sender( $data_fields, $this->data );

            $response = array(
                'message' => 'Success',
                'success' => 1,
            );

            if ( $this->is_test_mode() ) {
                $response['data'] = $sender->send_test( $this->sender );
            } else {
                $sender->send_to( $this->sender, $this->receiver );
            }

            $this->http_response_message( 200, $response );
        }

        $this->http_response_message( 400, array(
            'message' => 'Bad Request',
            'errors'  => $validator->get_errors(),
        ) );
    }

    /**
     * Checks if the application is in test mode.
     * 
     * @return bool Returns true if the application is in test mode, false otherwise.
     * @since 2.0.0
     */
    private function is_test_mode() {
        return in_array( $_SERVER['SERVER_NAME'], self::TEST_MODE );
    }

    /**
     * Sends an HTTP response with a given status code and response body.
     * 
     * @param int   $code     HTTP status code to send.
     * @param array $response Response body to send.
     * @since 1.0.0
     */
    private function http_response_message( $code, $response ) {
        status_header( $code );
        echo wp_json_encode( $response );
        exit;
    }

    /**
     * Filters the form fields and returns only the data fields.
     * 
     * @return array Data fields to be sent.
     * @since 2.0.0
     */
    private function get_data_fields() {
        $filtered_fields = array_filter( $this->fields, function ( $field ) {
            return $field['type'] !== 'group' && $field['type'] !== 'button';
        } );

        return $filtered_fields;
    }

    /**
     * Sanitizes data fields based on their type.
     *
     * @param array $fields Fields to sanitize.
     * @since 2.3.3
     */
    private function sanitize_data_fields( $fields ) {
        foreach ( $fields as $field ) {
            $input = $this->data[ $field['name'] ] ?? null;
            $this->data[ $field['name'] ] = $this->sanitize_field_value( $input, $field );
        }
    }

    /**
     * Sanitizes a field value based on the field type.
     *
     * @param mixed $input Input value.
     * @param array $field Field definition.
     * @return string Sanitized value.
     * @since 2.3.3
     */
    private function sanitize_field_value( $input, $field ) {
        if ( is_array( $input ) ) {
            return '';
        }

        $type  = $field['type'] ?? 'text';
        $value = trim( (string) $input );

        switch ( $type ) {
            case 'checkbox':
                return ( '1' === $value ) ? '1' : '0';
            case 'email':
                return sanitize_email( $value );
            case 'url':
                return esc_url_raw( $value );
            case 'number':
                return is_numeric( $value ) ? $value : '';
            case 'color':
                $color = sanitize_hex_color( $value );
                return $color ? $color : '';
            case 'textarea':
                return sanitize_textarea_field( $value );
            case 'select':
                $choices = $field['options']['choices'] ?? null;
                $options = is_array( $choices ) ? array_values( $choices ) : array();
                return in_array( $value, $options, true ) ? $value : '';
            default:
                return sanitize_text_field( $value );
        }
    }

    /**
     * Groups the given fields under a wrapper.
     * 
     * @param string $wrapper   Wrapper for the fields. Use %fields for fields insertion.
     * @param array  ...$fields Fields to be grouped.
     * @since 2.0.0
     */
    public function group_fields( $wrapper, ...$fields ) {
        foreach ( $fields as $field ) {
            $this->groups[ $field['name'] ] = $field;
        }

        $this->fields[] = array(
            'type'    => 'group',
            'wrapper' => $wrapper,
            'fields'  => $fields,
        );
    }

    /**
     * Adds a new field to the form.
     * 
     * @param string $type    Field type (e.g., "text", "email", "tel", "textarea", "select", "checkbox").
     * @param string $name    Field name. If the key is "name" or "email", appropriate headers will be set. If the "subject" key is found, it will be used as the email subject.
     * @param string $label   Field label. No label elements will be present in the HTML if not provided.
     * @param array  $options Field configuration:
     *                        - required: Is the field required? Defaults to false.
     *                        - default: Default value for the field. If not provided, the field will be empty.
     *                        - pattern: Regular expression for value checking.
     *                        - wrapper: Custom HTML wrapper. Use %field for field insertion.
     *                        - label_class: CSS class for the label.
     *                        - input_class: CSS class for the input.
     * @return array Created field with its options.
     * @since 2.0.0
     */
    public function add_field( $type, $name, $label, $options = array() ) {
        $field = array(
            'type'    => $type,
            'name'    => $name,
            'label'   => $label,
            'options' => $options,
        );

        $this->fields[] = $field;

        return $field;
    }

    /**
     * Adds a new button to the form.
     * 
     * @param string $title   Title of the button. If not provided, a default icon will be used instead.
     * @param array  $options Button configuration:
     *                        - class: CSS class for the button.
     * @since 2.0.0
     */
    public function add_button( $title, $options = array() ) {
        $this->fields[] = array(
            'type'    => 'button',
            'title'   => $title,
            'options' => $options,
        );
    }
}
