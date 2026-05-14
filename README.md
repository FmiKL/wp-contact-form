# WordPress Contact Form

An OOP contact form builder for WordPress with Ajax handling.

## Features

- Text, email, tel, url, number, color, checkbox, textarea, select fields
- Grouped fields with custom HTML wrappers
- Automatic mail headers from `email`, `name` and `subject` fields
- Ajax submission with validation and error display
- Nonce + honeypot spam protection
- Test mode on localhost

## Setup

```php
require_once 'path/to/wp-contact-form/autoload.php';
```

## Usage

```php
function setup_form_contact() {
    $form = new Contact_Manager( 'form-contact', get_option( 'admin_email' ) );

    $form->group_fields(
        '<div class="form-row">%fields</div>',
        $form->add_field( 'text', 'name', 'Name', array(
            'wrapper'     => '<div class="form-group">%field</div>',
            'input_class' => 'form-control',
            'required'    => true,
        ) ),
        $form->add_field( 'email', 'email', 'Email', array(
            'wrapper'     => '<div class="form-group">%field</div>',
            'input_class' => 'form-control',
            'required'    => true,
        ) ),
    );

    $form->add_field( 'textarea', 'message', 'Message', array(
        'wrapper'     => '<div class="form-group">%field</div>',
        'input_class' => 'form-control',
    ) );

    $form->add_button( 'Send', array( 'class' => 'btn btn-primary' ) );

    $form->create_form();
    $form->handle_request();
}
add_action( 'init', 'setup_form_contact' );
```

## License

[GPL v2](LICENSE)
