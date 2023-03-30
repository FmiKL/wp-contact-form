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
     */
    public function __construct(
        private $plugin_path,
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
        register_setting( self::OPTIONS_KEY, Sender::RECEIVER_KEY );
    }
}
