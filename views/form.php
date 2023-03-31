<!-- Contact -->
<form id="contact-form">
    <!-- Nonce -->
    <?php wp_nonce_field( 'contact', Mf\SFC_Contact\Sender::NONCE_KEY ); ?>

    <!-- Boot -->
    <input type="text" name="required">

    <!-- Fields -->
    <div>
        <label for="input-name">Name</label>
        <input type="text" name="name" id="input-name">
        <span id="contact-error-name"></span>
    </div>
    <div>
        <label for="input-email">Email</label>
        <input type="email" name="email" id="input-email">
        <span id="contact-error-email"></span>
    </div>
    <div>
        <label for="input-message">Message</label>
        <textarea name="message" id="input-message" cols="30" rows="10"></textarea>
        <span id="contact-error-message"></span>
    </div>

    <!-- Action -->
    <div>
        <button id="contact-btn" type="submit">Send</button>
        <div id="contact-loader">Sending in progress...</div>
    </div>

    <!-- Success -->
    <div id="contact-success">
        <span id="contact-success-message"></span>
    </div>
</form>
