<?php
/**
 * Class responsible for creating contact forms.
 * 
 * @package WP_Contact_Form
 * @author Mikael Fourré
 * @version 2.1.2
 * @see https://github.com/FmiKL/wp-contact-form
 */
class Contact_Form {
    use Contact_Security;
    
    /**
     * Path to the assets folder.
     * 
     * @var string
     * @since 2.0.0
     * @see Option_Page::enqueues_assets()
     */
    private const ASSETS_PATH = '/wp-contact-form/assets';

    /**
     * Shortcode for the contact form.
     * 
     * @var string
     * @since 1.0.0
     */
    private $shortcode;

    /**
     * Options of the contact form.
     *
     * @var array<string, string>
     * @since 2.1.0
     */
    private $options;

    /**
     * Fields for add to the contact form.
     * 
     * @var array<string, array>
     * @since 2.0.0
     * @see Contact_Manager::add_field()
     */
    private $fields;
    
    /**
     * Grouped fields wrapped in a template.
     * 
     * @var array<array>
     * @since 2.0.0
     * @see Contact_Manager::group_fields()
     */
    private $groups;

    /**
     * @param string $shortcode Shortcode for the contact form.
     * @param array  $options   Options for the contact form.
     * @param array  $fields    Fields for add to the contact form.
     * @param array  $groups    Grouped fields wrapped in a template.
     * @since 2.0.0
     */
    public function __construct( $shortcode, $options, $fields, $groups ) {
        $this->set_security_key( $shortcode );
        $this->shortcode = $shortcode;
        $this->options   = $options;
        $this->fields    = $fields;
        $this->groups    = $groups;
    }

    /**
     * Sets up the contact form by registering the
     * shortcode and enqueuing assets.
     * 
     * @since 1.0.0
     * @link https://developer.wordpress.org/reference/functions/add_shortcode/
     */
    public function setup() {
        add_shortcode( $this->shortcode, array( $this, 'render' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueues_assets' ) );
    }

    /**
     * Enqueues the necessary scripts and styles.
     * 
     * @since 1.0.0
     */
    public function enqueues_assets() {
        $assets_path_directory_uri = get_template_directory_uri() . self::ASSETS_PATH;

        if ( ! wp_style_is( 'form-contact', 'registered' ) ) {
            wp_enqueue_style( 'form-contact', $assets_path_directory_uri . '/css/form-contact.css' );
        }

        if ( ! wp_script_is( 'form-contact', 'registered' ) ) {
            wp_enqueue_script( 'form-contact', $assets_path_directory_uri . '/js/form-contact.js', array(), false, true );
            wp_localize_script( 'form-contact', 'form_contact', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
        }
    }

    /**
     * Renders the contact form.
     * 
     * @return string HTML for the contact form.
     * @since 1.0.0
     */
    public function render() {
        ob_start();
        ?>
        <form id="<?php echo esc_attr( $this->shortcode ); ?>" class="form-contact <?php echo esc_attr( $this->options['class'] ?? '' ); ?>">
            <?php
            $this->add_security_fields();

            foreach ( $this->fields as $field ) {
                if ( $field['type'] === 'button' ) {
                    echo $this->render_button( $field );
                    continue;
                }
        
                if ( $field['type'] === 'group' ) {
                    echo $this->render_group( $field );
                } elseif ( ! isset( $this->groups[ $field['name'] ] ) ) {
                    echo $this->render_field( $field );
                }
            }
            ?>
        </form>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a field for the contact form.
     * 
     * @param array   $field Field to render.
     * @return string HTML for the field.
     * @since 2.0.0
     */
    private function render_field( $field ) {
        $options = $field['options'] ?? array();
        
        $field_html = '';
    
        switch ( $field['type'] ) {
            case 'textarea':
                $field_html = $this->render_textarea_field( $field, $options );
                break;
            default:
                $field_html = $this->render_input_field( $field, $options );
                break;
        }
    
        if ( $field['label'] ) {
            $field_html = $this->render_label( $field, $options ) . $field_html;
        }
    
        return isset( $options['wrapper'] ) ? str_replace( '%field', $field_html, $options['wrapper'] ) : $field_html;
    }
    
    /**
     * Renders a label for a field.
     * 
     * @param array   $field   Field to render a label for.
     * @param array   $options Options for the label.
     * @return string HTML for the label.
     * @since 2.0.0
     */
    private function render_label( $field, $options ) {
        $class = $this->get_class_attribute( $options, 'label_class' );
        ob_start();
        ?>
        <label <?php echo $class; ?> for="<?php echo esc_attr( $this->shortcode . '-' . $field['name'] ); ?>">
            <?php echo esc_html( $field['label'] ); ?>
        </label>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Renders an input field.
     * 
     * @param array   $field   Field to render an input for.
     * @param array   $options Options for the input.
     * @return string HTML for the input.
     * @since 2.0.0
     */
    private function render_input_field( $field, $options ) {
        $class       = $this->get_class_attribute( $options, 'input_class' );
        $pattern     = $this->get_pattern_attribute( $options );
        $required    = $this->get_required_attribute( $options );
        $placeholder = $this->get_placeholder_attribute( $options );
        $value       = $this->get_value_attribute( $options );
    
        ob_start();
        ?>
        <input <?php echo $class; ?> type="<?php echo esc_attr( $field['type'] ); ?>"
               name="<?php echo esc_attr( $field['name'] ); ?>"
               id="<?php echo esc_attr( $this->shortcode . '-' . $field['name'] ); ?>"
               <?php echo $pattern; ?> <?php echo $placeholder; ?> <?php echo $value; ?> <?php echo $required; ?>>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Renders a textarea field.
     * 
     * @param array   $field   Field to render a textarea for.
     * @param array   $options Options for the textarea.
     * @return string HTML for the textarea.
     * @since 2.0.0
     */
    private function render_textarea_field( $field, $options ) {
        $class       = $this->get_class_attribute( $options, 'input_class' );
        $required    = $this->get_required_attribute( $options );
        $rows        = $this->get_rows_attribute( $options );
        $placeholder = $this->get_placeholder_attribute( $options );
        $value       = $this->get_value_attribute( $options, 'textarea' );
    
        ob_start();
        ?>
        <textarea <?php echo $class; ?> name="<?php echo esc_attr( $field['name'] ); ?>"
                  id="<?php echo esc_attr( $this->shortcode . '-' . $field['name'] ); ?>"
                  <?php echo $placeholder; ?> <?php echo $rows; ?> <?php echo $required; ?>><?php echo $value; ?></textarea>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a group of fields.
     * 
     * @param array   $group Group of fields to render.
     * @return string HTML for the group of fields.
     * @since 2.0.0
     */
    private function render_group( $group ) {
        if ( ! is_array( $group['fields'] ) ) {
            return '';
        }
    
        $group_html = '';
        foreach ( $group['fields'] as $field ) {
            $group_html .= $this->render_field( $field );
        }
    
        return str_replace( '%fields', $group_html, $group['wrapper'] );
    }

    /**
     * Renders a button.
     * 
     * @param array   $button Button to render.
     * @return string HTML for the button.
     * @since 2.0.0
     */
    private function render_button( $button ) {
        ob_start();
        $class = $this->get_class_attribute( $button['options'], 'class', 'form-contact-button' );
        $key   = esc_attr( $this->shortcode );
        ?>
        <button id="<?php echo $key . '-button'; ?>" <?php echo $class; ?> type="submit">
            <?php echo esc_html( $button['title'] ?: '&rarr;' ); ?>
            <div id="<?php echo $key . '-loader'; ?>" class="form-contact-loader">
                <span class="form-contact-spinner"></span>
            </div>
            <div id="<?php echo $key . '-success'; ?>" class="form-contact-success">&check;</div>
        </button>
        <?php
        $button_html = ob_get_clean();

        if ( isset( $button['options']['wrapper'] ) ) {
            $button_html = str_replace( '%button', $button_html, $button['options']['wrapper'] );
        }

        return $button_html;
    }

    /**
     * Get the class attribute for an HTML element.
     *
     * @param array   $options Options array to look for the key.
     * @param string  $key     Key to look for in the options array.
     * @param string  $default Default value if the key is not found.
     * @return string Class attribute or default value.
     * @since 2.0.0
     */
    private function get_class_attribute( $options, $key, $default = '' ) {
        if ( isset( $options[ $key ] ) ) {
            $classes = array( $options[ $key ], $default );
            return 'class="' . esc_attr( trim( implode( ' ', $classes ) ) ) . '"';
        }
        return $default;
    }

    /**
     * Get the required attribute for an HTML element.
     *
     * @param array   $options Options array to look for the key.
     * @return string Required attribute or an empty string.
     * @since 2.0.0
     */
    private function get_required_attribute( $options ) {
        return isset( $options['required'] ) && $options['required'] === true ? 'required' : '';
    }
    
    /**
     * Get the pattern attribute for an HTML element.
     *
     * @param array   $options Options array to look for the key.
     * @return string Pattern attribute or an empty string.
     * @since 2.0.0
     */
    private function get_pattern_attribute( $options ) {
        return isset( $options['pattern'] ) ? 'pattern="' . esc_attr( $options['pattern'] ) . '"' : '';
    }

    /**
     * Get the rows attribute for an HTML element.
     *
     * @param array   $options Options array to look for the key.
     * @return string Rows attribute or an empty string.
     * @since 2.0.0
     */
    private function get_rows_attribute( $options ) {
        return isset( $options['rows'] ) ? 'rows="' . esc_attr( $options['rows'] ) . '"' : '';
    }
    
    /**
     * Get the placeholder attribute for an HTML element.
     *
     * @param array   $options Options array to look for the key.
     * @return string Placeholder attribute or an empty string.
     * @since 2.0.0
     */
    private function get_placeholder_attribute( $options ) {
        return isset( $options['placeholder'] ) ? 'placeholder="' . esc_attr( $options['placeholder'] ) . '"' : '';
    }

    /**
     * Get the value attribute for an HTML element.
     *
     * @param array   $options Options array to look for the key.
     * @param string  $type    Type of the HTML element. It can be "input" or "textarea". 
     * @return string Value attribute or an empty string.
     * @since 2.0.0
     */
    private function get_value_attribute( $options, $type = 'input' ) {
        if ( $type === 'textarea' ) {
            return isset( $options['default'] ) ? esc_textarea( $options['default'] ) : '';
        }

        return isset( $options['default'] ) ? 'value="' . esc_attr( $options['default'] ) . '"' : '';
    }
}
