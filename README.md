# WordPress Contact Form

This project enables the rapid and easy creation of contact forms for WordPress using an object-oriented approach.

## Features

- **Customizable Fields**: You can add any type of field to the form.
- **Automated Mail Headers**: Using specific fields like `email` and `name` automatically populates the mail headers. Similarly, a `subject` field sets the mail subject automatically.
- **Ajax Management**: The form is fully managed with Ajax, ensuring a responsive and smooth user experience.
- **Flexible Structure and Clean HTML**: The form can have any structure, and the generated HTML is clean, exactly matching what is expected, without any superfluous tags.

## Installation and Setup

1. Clone or download this repository into your WordPress environment.
2. Import the classes into your `functions.php` file or your plugin.

```php
require_once 'path/to/wp-contact-form/class-contact-security.php';
require_once 'path/to/wp-contact-form/class-contact-validator.php';
require_once 'path/to/wp-contact-form/class-contact-sender.php';
require_once 'path/to/wp-contact-form/class-contact-form.php';
require_once 'path/to/wp-contact-form/class-contact-manager.php';
```

## Usage Example

```php
function setup_form_contact() {
    $form = new Contact_Manager( 'form-contact', get_option( 'admin_email' ) );

    $form->add_field(
        'text',
        'name',
        'Name',
        array(
            'wrapper'     => '<div class="form-group">%field</div>',
            'label_class' => 'form-label',
            'input_class' => 'form-control',
        )
    );

    $form->group_fields(
        '<div class="form-row">%fields</div>',
        $form->add_field(
            'email',
            'email',
            'Email',
            array(
                'wrapper'     => '<div class="form-group">%field</div>',
                'label_class' => 'form-label',
                'input_class' => 'form-control',
            )
        ),

        $form->add_field(
            'tel',
            'phone',
            'Phone',
            array(
                'required'    => false,
                'pattern'     => '^0[1-9](?:[\s]?[0-9]{2}){4}$',
                'wrapper'     => '<div class="form-group">%field</div>',
                'label_class' => 'form-label',
                'input_class' => 'form-control',
            )
        ),
    );

    $form->add_field(
        'textarea',
        'message',
        'Message',
        array(
            'wrapper'     => '<div class="form-group">%field</div>',
            'label_class' => 'form-label',
            'input_class' => 'form-control',
        )
    );

    $form->add_button(
        'Send',
        array(
            'wrapper' => '<div class="form-group">%button</div>',
            'class'   => 'btn btn-primary',
        )
    );

    $form->create_form();
    $form->handle_request();
}
add_action( 'init', 'setup_form_contact' );
```

## Integrating the Form

After setting up, integrate the form into your WordPress site by using the shortcode specific to your configuration. In our usage example, the key we've used is form-contact. Add this shortcode `[form-contact]` to your WordPress pages or posts where you want the form to appear.

## Contributing

Contributions to improve this project are welcome, whether they be bug fixes, documentation improvements, or new feature suggestions.

## License

This project is distributed under the [GNU General Public License version 2 (GPL v2)](LICENSE).
