<form action="" method="POST" id="payment-form">
    <input type="hidden" name="action" value="wp_full_stripe_payment_charge"/>
    <input type="hidden" name="amount" value="<?php echo $formData->amount; ?>"/>
    <input type="hidden" name="formName" value="<?php echo $formData->name; ?>"/>
    <input type="hidden" name="isCustom" value="<?php echo $formData->customAmount; ?>"/>
    <input type="hidden" name="formDoRedirect" value="<?php echo $formData->redirectOnSuccess; ?>"/>
    <input type="hidden" name="formRedirectPostID" value="<?php echo $formData->redirectPostID; ?>"/>
    <input type="hidden" name="sendEmailReceipt" value="<?php echo $formData->sendEmailReceipt; ?>"/>

    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." id="showLoading"/>
    <p class="payment-errors"></p>

    <?php include(WP_FULL_STRIPE_DIR . '/include/personal_data_fieldset.php'); ?>

    <fieldset>
      <h3 class="legend"><?php _e("Donation", "wp-full-stripe"); ?></h3>
      <!-- Payment method -->
      <div class="control-group">
          <label class="control-label fullstripe-form-label"><?php _e("Paying method", "wp-full-stripe"); ?></label>
          <div class="controls">
            <select name="fullstripe_pay_method" id="fullstripe_pay_method">
              <option value="card"><?php _e("Credit card", "wp-full-stripe"); ?></option>
              <option value="debit"><?php _e("Direct debit payment", "wp-full-stripe"); ?></option>
            </select>
          </div>
      </div>
      <?php if ( $formData->customAmount == 1 ): ?>
      <!-- Payment amount -->
      <div class="control-group">
          <label class="control-label fullstripe-form-label"><?php _e("Payment amount", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
          <div class="controls">
              <input type="text" placeholder="10.00" style="width: 60px;" name="fullstripe_custom_amount" id="fullstripe_custom_amount" class="fullstripe-form-input"> €
          </div>
      </div>
      <?php endif; ?>

      <?php include(WP_FULL_STRIPE_DIR . '/include/card_fields.php'); ?>
    </fieldset>

    <?php include(WP_FULL_STRIPE_DIR . '/include/general_conditions.php'); ?>

    <!-- Submit -->
    <div class="control-group actions">
        <div class="controls">
          <?php if ( $formData->customAmount == 0 ): ?>
            <button type="submit"><?php _e($formData->buttonTitle, "wp-full-stripe"); ?> <?php if ($formData->showButtonAmount == 1) {echo $currencySymbol . sprintf('%0.2f', $formData->amount / 100.0);}  ?></button>
          <?php else: ?>
            <button type="submit"><?php _e($formData->buttonTitle, "wp-full-stripe"); ?></button>
          <?php endif; ?>
        </div>
    </div>
    
</form>
