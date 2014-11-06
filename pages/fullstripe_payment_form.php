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
      <!-- Card Name -->
      <div class="control-group">
          <label class="control-label fullstripe-form-label"><?php _e("Card Holder's Name", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
          <div class="controls">
              <input type="text" placeholder="Full name" class="input-xlarge fullstripe-form-input" name="fullstripe_name" id="fullstripe_name">
          </div>
      </div>
      <div class="row-fluid">
        <!-- Card Number -->
        <div class="control-group span4">
            <label class="control-label fullstripe-form-label"><?php _e("Card Number", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text" autocomplete="off" placeholder="4242424242424242" class="input-xlarge fullstripe-form-input" size="20" data-stripe="number">
            </div>
        </div>
        <!-- Expiry-->
        <div class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php _e("Card Expiry Date", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text" style="width: 60px;" size="2" placeholder="10" data-stripe="exp-month" class="fullstripe-form-input"/>
                <span> / </span>
                <input type="text" style="width: 60px;" size="4" placeholder="2016" data-stripe="exp-year" class="fullstripe-form-input"/>
            </div>
        </div>
        <!-- CVV -->
        <div class="control-group span5">
            <label class="control-label fullstripe-form-label"><?php _e("Card CVV", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="password" autocomplete="off" placeholder="123" class="input-mini fullstripe-form-input" size="4" data-stripe="cvc"/>
                <span class="help-block"><?php _e("The CVV is a verification number that is generally 3 digit long and is printed on the back of your card.", "wp-full-stripe"); ?></span>
            </div>
        </div>
      </div>
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
