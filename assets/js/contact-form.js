(function () {
    const form = document.getElementById('contact-form');
    const submit = document.getElementById('contact-btn');
    const loader = document.getElementById('contact-loader');
    const success = document.getElementById('contact-success');
    const successMessage = document.getElementById('contact-success-message');

    let isFormSubmitted = false;

    if (form) {
        form.addEventListener('submit', handleSubmit);
    }

    function handleSubmit(e) {
        e.preventDefault();

        if (isFormSubmitted) {
            return;
        }

        isFormSubmitted = true;
        setButtonAndLoaderState(true);

        const formData = new FormData(form);
        formData.append('action', 'sfc_contact_send');

        fetch(admin.ajax, {
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
        });
    }

    function handleResponse(data) {
        setButtonAndLoaderState(false);
        clearErrorMessages();

        if (data.messages) {
            setButtonAndLoaderState(false);

            for (const [name, error] of Object.entries(data.messages)) {
                const message = document.getElementById('contact-error-' + name);
                message.innerText = error;
            }
        } else if (data.success) {
            showSuccessMessage(data.success);
            setButtonAndLoaderState(true);
            form.reset();
        }
    }

    function setButtonAndLoaderState(disabled) {
        submit.disabled = disabled;
        loader.classList.toggle('active', disabled);
    }

    function clearErrorMessages() {
        const errors = document.querySelectorAll('[id^="contact-error-"]');
        errors.forEach(error => error.textContent = '');
    }

    function showSuccessMessage(message) {
        success.classList.add('show');
        successMessage.innerText = message;
    }
})();
