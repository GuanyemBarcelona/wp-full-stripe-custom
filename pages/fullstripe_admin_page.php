<?php
$options = get_option('fullstripe_options');
?>
<div class="wrap">
    <h2><?php echo __('Full Stripe Settings', 'wp-full-stripe'); ?></h2>
    <div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
    <p class="alert alert-info">The Stripe API keys are required for payments to work. You can find your keys on your
        <a href="https://manage.stripe.com">Stripe Dashboard</a> -> Account Settings -> API Keys tab</p>
    <form class="form-horizontal" action="" method="post" id="settings-form">
        <p class="tips"></p>
        <input type="hidden" name="action" value="wp_full_stripe_update_settings"/>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="secretKey_test"><?php _e("Stripe Test Secret Key: ", 'wp-full-stripe'); ?> </label>
                </th>
                <td>
                    <input type="text" name="secretKey_test" id="secretKey_test" value="<?php echo $options['secretKey_test']; ?>" class="regular-text code">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="publishKey_test"><?php _e("Stripe Test Publishable Key: ", 'wp-full-stripe'); ?></label>
                </th>
                <td>
                    <input type="text" id="publishKey_test" name="publishKey_test" value="<?php echo $options['publishKey_test']; ?>" class="regular-text code">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="secretKey_live"><?php _e("Stripe Live Secret Key: ", 'wp-full-stripe'); ?> </label>
                </th>
                <td>
                    <input type="text" name="secretKey_live" id="secretKey_live" value="<?php echo $options['secretKey_live']; ?>" class="regular-text code">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="publishKey_live"><?php _e("Stripe Live Publishable Key: ", 'wp-full-stripe'); ?></label>
                </th>
                <td>
                    <input type="text" id="publishKey_live" name="publishKey_live" value="<?php echo $options['publishKey_live']; ?>" class="regular-text code">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php _e("API mode: ", 'wp-full-stripe'); ?> </label>
                </th>
                <td>
                    <label class="radio">
                        <input type="radio" name="apiMode" id="modeTest" value="test" <?php echo ($options['apiMode'] == 'test') ? 'checked' : '' ?> > Test
                    </label> <label class="radio">
                        <input type="radio" name="apiMode" id="modeLive" value="live" <?php echo ($options['apiMode'] == 'live') ? 'checked' : '' ?>> Live
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="currency"><?php _e("Payment Currency: ", 'wp-full-stripe'); ?></label>
                </th>
                <td>
                    <select id="currency" name="currency">
                        <option value="usd" <?php echo ($options['currency'] == 'usd') ? 'selected="selected"' : '' ?>>United States Dollar</option>
                        <option value="cad" <?php echo ($options['currency'] == 'cad') ? 'selected="selected"' : '' ?>>Canadian Dollar</option>
                        <option value="eur" <?php echo ($options['currency'] == 'eur') ? 'selected="selected"' : '' ?>>Euro</option>
                        <option value="gbp" <?php echo ($options['currency'] == 'gbp') ? 'selected="selected"' : '' ?>>British Pound Sterling</option>
                        <option value="aud" <?php echo ($options['currency'] == 'aud') ? 'selected="selected"' : '' ?>>Australian Dollar</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="form_css"><?php _e("Custom Form CSS: ", 'wp-full-stripe'); ?> </label>
                </th>
                <td>
                    <textarea name="form_css" id="form_css" class="large-text code" rows="10" cols="50"><?php echo $options['form_css']; ?></textarea>
                    <p class="description">Add extra styling to the form. NOTE: if you don't know what this is do not change it.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php _e("Include Default Styles: ", 'wp-full-stripe'); ?> </label>
                </th>
                <td>
                    <label class="radio">
                        <input type="radio" name="includeStyles" id="includeStylesY" value="1" <?php echo ($options['includeStyles'] == '1') ? 'checked' : '' ?> > Include
                    </label> <label class="radio">
                        <input type="radio" name="includeStyles" id="includeStylesN" value="0" <?php echo ($options['includeStyles'] == '0') ? 'checked' : '' ?>> Exclude
                    </label>
                    <p class="description">Exclude styles if the payment forms do not appear properly. This can indicate a conflict with your theme.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php _e("Receipt Email Type: ", 'wp-full-stripe'); ?> </label>
                </th>
                <td>
                    <label class="radio">
                        <input type="radio" name="receiptEmailType" id="receiptEmailType1" value="plugin" <?php echo ($options['receiptEmailType'] == 'plugin') ? 'checked' : '' ?> > Plugin
                    </label> <label class="radio">
                        <input type="radio" name="receiptEmailType" id="receiptEmailType0" value="stripe" <?php echo ($options['receiptEmailType'] == 'stripe') ? 'checked' : '' ?>> Stripe
                    </label>
                    <p class="description">Choose the type of payment receipt emails. Plugin emails are defined below and Stripe emails can be setup in your Stripe Dashboard.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="email_receipt_subject"><?php _e("Email Receipt Subject: ", 'wp-full-stripe'); ?></label>
                </th>
                <td>
                    <input type="text" id="email_receipt_subject" name="email_receipt_subject" value="<?php echo $options['email_receipt_subject']; ?>" class="regular-text code">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="email_receipt_html"><?php _e("Email Receipt HTML: ", 'wp-full-stripe'); ?> </label>
                </th>
                <td>
                    <textarea name="email_receipt_html" id="email_receipt_html" class="large-text code" rows="10" cols="50"><?php echo html_entity_decode($options['email_receipt_html']); ?></textarea>
                    <p class="description">The text for plugin payment email receipts.  %AMOUNT% will be replaced with payment amount and %NAME% is replaced by website name (WordPress blog name). See help page for more options.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php _e("Send Admin Email Receipt: ", 'wp-full-stripe'); ?> </label>
                </th>
                <td>
                    <label class="radio">
                        <input type="radio" name="admin_payment_receipt" id="sendAdminPaymentReceiptY" value="1" <?php echo ($options['admin_payment_receipt'] == '1') ? 'checked' : '' ?> > Yes
                    </label> <label class="radio">
                        <input type="radio" name="admin_payment_receipt" id="sendAdminPaymentReceiptN" value="0" <?php echo ($options['admin_payment_receipt'] == '0') ? 'checked' : '' ?>> No
                    </label>
                    <p class="description">Send copies of payment receipts to the website admin as well?</p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary"><?php esc_attr_e('Save Changes') ?></button>
        </p>
    </form>
</div>
