(function () {
    const forms = document.querySelectorAll('.form-contact');

    forms.forEach(form => {
        const ajaxKey = form.querySelector('input[name="_ajax_key"]');
        const button = form.querySelector('.form-contact-button');
        const loader = form.querySelector('.form-contact-loader');
        const success = form.querySelector('.form-contact-success');
    
        let isFormSubmitted = false;
    
        form.addEventListener('submit', handleSubmit);

        function handleSubmit(e) {
            e.preventDefault();

            if (isFormSubmitted) {
                return;
            }

            isFormSubmitted = true;
            
            success.classList.remove('show');
            setButtonAndLoaderState(true);

            const formData = new FormData(form);
            formData.append('action', ajaxKey.value);

            fetch(form_contact.url, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(handleResponse)
            .catch(error => {
                console.error('An error occurred:', error);
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

            const inputErrors = form.querySelectorAll('.is-invalid');
            inputErrors.forEach(input => input.classList.remove('is-invalid'));

            if (data.errors) {
                setButtonAndLoaderState(false);

                for (const [name, error] of Object.entries(data.errors)) {
                    const input = form.querySelector('[name="' + name + '"]');
                    input.classList.add('is-invalid');
                }
            } else if (data.success) {
                success.classList.add('show');
                setButtonAndLoaderState(true);
                form.reset();
            } else {
                throw new Error('Network response was not ok');
            }
        }
    
        function setButtonAndLoaderState(disabled) {
            button.disabled = disabled;
            loader.classList.toggle('active', disabled);
        }
    });
})();
