<?php

namespace Mf\SFC_Contact;

/**
 * Create an option page for configuration.
 */
class Option {
    /**
     * The plugin title.
     */
    public const PLUGIN_TITLE = 'Contact';

    /**
     * The options key.
     */
    public const OPTIONS_KEY = 'form_contact_settings';

    /**
     * Constructor.
     *
     * @param string $plugin_path
     * @param array $error_cases
     * @param array $message_cases
     */
    public function __construct(
        private $plugin_path,
        private $error_cases,
        private $message_cases,
    ){}

    /**
     * Initialize the methods.
     *
     * @return void
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Add a page in the settings menu.
     *
     * @return void
     */
    public function add_page() {
        add_options_page( self::PLUGIN_TITLE, self::PLUGIN_TITLE, 'manage_options', self::OPTIONS_KEY, array( $this, 'render_page_content' ) );
    }

    /**
     * Return the settings modification page.
     *
     * @return void
     */
    public function render_page_content() {
        ob_start();

        $receiver_key = array( 'receiver_key' => Sender::RECEIVER_KEY );
        extract( $receiver_key );

        require_once $this->plugin_path . 'views/config.php';
        echo ob_get_clean();
    }

    /**
     * Register the configuration settings
     * that will be used.
     *
     * @return void
     */
    public function register_settings() {
        foreach ( $this->get_setting_keys() as $key ) {
            register_setting( self::OPTIONS_KEY, $key );
        }
    }

    /**
     * Get an array containing all
     * configuration keys.
     *
     * @return array
     */
    private function get_setting_keys() {
        $setting_keys = array(
            Sender::RECEIVER_KEY,
        );

        foreach ( $this->message_cases as $message ) {
            $setting_keys[] = $message->value;
        }

        foreach ( $this->error_cases as $error ) {
            $setting_keys[] = $error->value;
        }

        return $setting_keys;
    }
}
