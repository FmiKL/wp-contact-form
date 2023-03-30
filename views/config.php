<div class="wrap">
    <h1><?php echo self::PLUGIN_TITLE; ?></h1>
    <form method="post" action="options.php">
        <?php settings_fields( self::OPTIONS_KEY ); ?>
        <p>Shortcode : <strong>[<?= Mf\SFC_Contact\Form::SHORT_NAME ?>]</strong></p>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="<?php echo $receiver_key; ?>">Receiving address</label>
                </th>
                <td>
                    <input name="<?php echo $receiver_key; ?>" type="email" id="<?php echo $receiver_key; ?>" value="<?php echo get_option( $receiver_key ); ?>" class="regular-text">
                </td>
            </tr>
            <?php foreach ( $this->message_cases as $message ) : ?>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $message->value; ?>"><?php echo $message->label(); ?></label>
                    </th>
                    <td>
                        <input name="<?php echo $message->value; ?>" type="text" id="<?php echo $message->value; ?>" value="<?php echo get_option( $message->value ); ?>" class="regular-text">
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php foreach ( $this->error_cases as $error ) : ?>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $error->value; ?>"><?php echo $error->title(); ?></label>
                    </th>
                    <td>
                        <input name="<?php echo $error->value; ?>" type="text" id="<?php echo $error->value; ?>" value="<?php echo get_option( $error->value ); ?>" class="regular-text">
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
