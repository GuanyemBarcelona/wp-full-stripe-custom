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