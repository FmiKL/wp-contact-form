<?php
/**
 * Registers an autoloader for contact form classes and traits.
 *
 * @since 2.4.0
 */
spl_autoload_register( function ( $class ) {
    if ( strpos( $class, 'Contact_' ) !== 0 ) {
        return;
    }

    $file = __DIR__ . '/classes/class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';

    if ( is_readable( $file ) ) {
        require_once $file;
    }
} );
