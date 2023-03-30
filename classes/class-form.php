<?php

namespace Mf\SFC_Contact;

/**
 * Create a form.
 */
class Form {
    /**
     * Constructor.
     *
     * @param string $plugin_path
     * @param string $plugin_url
     */
    public function __construct(
        private $plugin_path,
        private $plugin_url,
    ){}

    /**
     * Initialize the methods.
     *
     * @return void
     */
    public function init() {
        add_action( 'init', array( $this, 'add_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'add_enqueues' ) );
    }

    /**
     * Add a page in the settings menu.
     *
     * @return void
     */
    public function add_shortcode() {
        add_shortcode( 'contact-form', array( $this, 'render_form' ) );
    }

    /**
     * Add the form styles.
     *
     * @return void
     */
    public function add_enqueues() {
        wp_enqueue_style( 'contact-form-style', $this->plugin_url . 'assets/css/contact-form.css' );
    }

    /**
     * Render the contact form.
     *
     * @return void
     */
    public function render_form() {
        ob_start();
        require_once $this->plugin_path . 'views/form.php';
        echo ob_get_clean();
    }
}
