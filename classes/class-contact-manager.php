<?php
/**
 * Class used to managing the construction of the contact form
 * and the associated request.
 * 
 * @package WP_Contact_Form
 * @author Mikael Fourré
 * @version 2.0.0
 * @see https://github.com/FmiKL/wp-contact-form
 */
class Contact_Manager {
    use Contact_Security;
    
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
     */
    private $data;

    /**
     * Receiver of the contact form.
     *
     * @var string
     * @since 1.0.0
     */
    private $receiver;

    /**
     * Fields for add to the contact form.
     *
     * @var array<array>
     * @since 2.0.0
     */
    private $fields = array();

    /**
     * Grouped fields wrapped in a template.
     *
     * @var array<string, array>
     * @since 2.0.0
     */
    private $groups = array();

    /**
     * @param string $shortcode Shortcode for the contact form.
     * @param string $receiver  Receiver of the contact form.
     * @param mixed  $data      Data handled by the contact form.
     * @since 1.0.0
     */
    public function __construct( $shortcode, $receiver, $data = 'post' ) {
        $this->set_security_key( $shortcode );
        $this->shortcode = $shortcode;
        $this->receiver  = $receiver;

        if ( $data === 'post' ) {
            $this->data = $_POST;
        } else {
            $this->data = $data;
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
     * @param string $type    Field type (e.g., "text", "email", "tel", "textarea").
     * @param string $name    Field name. If the key is "name" or "email", appropriate headers will be set. If the "subject" key is found, it will be used as the email subject.
     * @param string $label   Field label. No label elements will be present in the HTML if not provided.
     * @param array  $options Field configuration:
     *                        - required: Is the field required? Defaults to false.
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
