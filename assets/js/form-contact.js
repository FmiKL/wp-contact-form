(function () {
    const forms = document.querySelectorAll('.form-contact');

    forms.forEach(form => {
        const ajaxKey = form.querySelector('input[name="_ajax_key"]');
        const button = form.querySelector('.form-contact-button');
        const loader = form.querySelector('.form-contact-loader');
        const success = form.querySelector('.form-contact-success');

        if (!ajaxKey) {
            return;
        }
    
        let isFormSubmitted = false;
    
        form.addEventListener('submit', handleSubmit);

        function handleSubmit(e) {
            e.preventDefault();

            // Prevent multiple submissions while a request is in flight.
            if (isFormSubmitted) {
                return;
            }

            isFormSubmitted = true;
            
            if (success) {
                success.classList.remove('show');
            }
            setButtonAndLoaderState(true);

            const formData = new FormData(form);
            formData.append('action', ajaxKey.value);

            // Submit through admin-ajax.php and let PHP return JSON errors/success.
            fetch(form_contact.url, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(handleResponse)
            .catch(error => {
                alert('An error occurred!');
                setButtonAndLoaderState(false);
            })
            .finally(() => {
                isFormSubmitted = false;
                setButtonAndLoaderState(false);
            });
        }

        function handleResponse(data) {
            setButtonAndLoaderState(false);

            // Reset previous validation state before applying new errors.
            const inputErrors = form.querySelectorAll('.is-invalid');
            inputErrors.forEach(input => input.classList.remove('is-invalid'));

            if (data.errors) {
                setButtonAndLoaderState(false);

                Object.keys(data.errors).forEach(name => {
                    const input = getFormControl(name);

                    if (input) {
                        input.classList.add('is-invalid');
                    }
                });
            } else if (data.success) {
                if (success) {
                    success.classList.add('show');
                }
                setButtonAndLoaderState(true);
                form.reset();
            } else {
                throw new Error('Network response was not ok');
            }
        }

        function getFormControl(name) {
            const control = form.elements.namedItem(name);

            if (!control) {
                return null;
            }

            if (control instanceof Element) {
                return control;
            }

            return control[0] || null;
        }
    
        function setButtonAndLoaderState(disabled) {
            if (button) {
                button.disabled = disabled;
            }

            if (loader) {
                loader.classList.toggle('active', disabled);
            }
        }
    });
})();
