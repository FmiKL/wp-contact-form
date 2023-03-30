<!-- Contact -->
<form id="contact-form">
    <!-- Fields -->
    <div>
        <label for="input-name">Name</label>
        <input type="text" name="name" id="input-name">
    </div>
    <div>
        <label for="input-email">Email</label>
        <input type="email" name="email" id="input-email">
    </div>
    <div>
        <label for="input-message">Message</label>
        <textarea name="message" id="input-message" cols="30" rows="10"></textarea>
    </div>

    <!-- Action -->
    <div>
        <button id="contact-btn" type="submit">Send</button>
        <div id="contact-loader">Sending in progress...</div>
    </div>
</form>
