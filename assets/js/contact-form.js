(function () {
    const form = document.getElementById('contact-form');

    if (form) {
        form.addEventListener('submit', handleSubmit);
    }

    function handleSubmit(e) {
        e.preventDefault();

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
        })
    }

    function handleResponse(data) {
        console.log(data);
    }
})();
