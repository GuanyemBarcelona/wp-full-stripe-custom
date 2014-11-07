<div class="payment-method" data-type="spanishaccount">
  <div class="row-fluid">
    <!-- Entidad -->
    <div class="control-group span1">
        <label class="control-label"><?php _e("Bank", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
        <div class="controls">
            <input type="text" maxlength="4" class="input-xlarge" name="bank_spain_bankid">
        </div>
    </div>
    <!-- Oficina -->
    <div class="control-group span1">
        <label class="control-label"><?php _e("Office", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
        <div class="controls">
            <input type="text" maxlength="4" class="input-xlarge" name="bank_spain_office">
        </div>
    </div>
    <!-- DC -->
    <div class="control-group span1">
        <label class="control-label"><?php _e("DC", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
        <div class="controls">
            <input type="text" maxlength="2" class="input-xlarge" name="bank_spain_dc">
        </div>
    </div>
    <!-- Cuenta -->
    <div class="control-group span4">
        <label class="control-label"><?php _e("Account", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
        <div class="controls">
            <input type="text" maxlength="10" class="input-xlarge" name="bank_spain_account">
        </div>
    </div>
  </div>
</div>