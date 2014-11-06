<form action="" method="POST" id="payment-form">
    <input type="hidden" name="action" value="wp_full_stripe_subscription_charge"/>
    <input type="hidden" name="formName" value="<?php echo $formData->name; ?>"/>
    <input type="hidden" name="formDoRedirect" value="<?php echo $formData->redirectOnSuccess; ?>"/>
    <input type="hidden" name="formRedirectPostID" value="<?php echo $formData->redirectPostID; ?>"/>
    <input type="hidden" name="fullstripe_setupFee" id="fullstripe_setupFee" value="<?php echo $formData->setupFee; ?>"/>
    
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
      <!-- Subscription Plan -->
      <div class="control-group">
          <label class="control-label fullstripe-form-label"><?php _e("Donation per month", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
          <div class="controls">
              <?php foreach ($plans as $plan): ?>
              <div class="radio">
                <label>
                  <input type="radio" name="fullstripe_plan" id="fullstripe_plan_<?php echo $plan->id; ?>" value="<?php echo $plan->id; ?>"  
                                data-amount="<?php echo $plan->amount;?>"
                                data-interval="<?php echo $plan->interval;?>"
                                data-interval-count="<?php echo $plan->interval_count;?>"
                                data-currency="<?php echo $currencySymbol; ?>">
                  <?php echo $plan->name; ?>
                </label>
              </div>
              <?php endforeach; ?>
          </div>
      </div>
      
      <?php include(WP_FULL_STRIPE_DIR . '/include/card_fields.php'); ?>
    </fieldset>

    <h3 class="total"><?php _e("Total", "wp-full-stripe"); ?>: <span class="value fullstripe_plan_details"></span></h3>

    <?php include(WP_FULL_STRIPE_DIR . '/include/general_conditions.php'); ?>
    
    <!-- Submit -->
    <div class="control-group actions">
        <div class="controls">
            <button type="submit"><?php _e($formData->buttonTitle, "wp-full-stripe"); ?></button>
        </div>
    </div>
</form>
