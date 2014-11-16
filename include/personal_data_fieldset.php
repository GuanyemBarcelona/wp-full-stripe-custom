    <fieldset>
      <h3 class="legend"><?php _e("Personal data", "wp-full-stripe"); ?></h3>
      <div class="row-fluid">
        <!-- First name -->
        <div class="control-group span6">
            <label class="control-label fullstripe-form-label"><?php _e("First name", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text" class="fullstripe-form-input" name="fullstripe_firstname" id="fullstripe_firstname">
            </div>
        </div>
        <!-- Last name -->
        <div class="control-group span6">
            <label class="control-label fullstripe-form-label"><?php _e("Last name", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text" class="fullstripe-form-input" name="fullstripe_lastname" id="fullstripe_lastname">
            </div>
        </div>
      </div>

      <div class="row-fluid">
        <!-- Type of document -->
        <div id="doctype-selector" class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php _e("Type of document", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <label class="inline">
              <input type="radio" name="fullstripe_doctype" id="fullstripe_doctype_1" value="dni"> DNI/NIE
            </label>
            <label class="inline">
              <input type="radio" name="fullstripe_doctype" id="fullstripe_doctype_2" value="passport"> <?php _e("Passport", "wp-full-stripe"); ?>
            </label>
        </div>
        <!-- Document ID -->
        <div id="doctype-values" class="control-group span3">
            <div data-type="dni">
              <label class="control-label fullstripe-form-label">DNI/NIE <span class="required-field">*</span></label>
              <div class="controls">
                <input type="text" class="fullstripe-form-input" name="fullstripe_doc_dni" id="fullstripe_doc_dni">
              </div>
            </div>
            <div data-type="passport">
              <label class="control-label fullstripe-form-label"><?php _e("Passport", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
              <div class="controls">
                <input type="text" class="fullstripe-form-input" name="fullstripe_doc_passport" id="fullstripe_doc_passport">
              </div>
            </div>
        </div>
        <!-- Date of birth -->
        <div class="control-group span4">
            <label class="control-label fullstripe-form-label"><?php _e("Date of birth", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <select name="fullstripe_birthdate[]" class="inline">
                  <option value=""><?php _e("Day", "wp-full-stripe"); ?></option>
                  <?php for ($i = 1; $i <= 31; $i++){ ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                  <?php } ?>
                </select>
                <select name="fullstripe_birthdate[]" class="inline">
                  <option value=""><?php _e("Month", "wp-full-stripe"); ?></option>
                  <?php for ($i = 1; $i <= 12; $i++){ ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                  <?php } ?>
                </select>
                <select name="fullstripe_birthdate[]" class="inline">
                  <option value=""><?php _e("Year", "wp-full-stripe"); ?></option>
                  <?php for ($i = date('Y'); $i >= 1910; $i--){ ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                  <?php } ?>
                </select>
            </div>
        </div>
        <!-- Telephone -->
        <div class="control-group span2">
            <label class="control-label fullstripe-form-label"><?php _e("Telephone", "wp-full-stripe"); ?></label>
            <div class="controls">
                <input type="text" class="fullstripe-form-input" name="fullstripe_telephone" id="fullstripe_telephone">
            </div>
        </div>
      </div>

      <div class="row-fluid">
          <!-- Email -->
          <div class="control-group span6">
              <label class="control-label fullstripe-form-label"><?php _e("Email", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
              <div class="controls">
                  <input type="text" class="fullstripe-form-input" name="fullstripe_email" id="fullstripe_email">
              </div>
          </div>
          <!-- Email confirmation -->
          <div class="control-group span6">
              <label class="control-label fullstripe-form-label"><?php _e("Confirm email", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
              <div class="controls">
                  <input type="text" class="fullstripe-form-input" name="fullstripe_email2" id="fullstripe_email2">
              </div>
          </div>
      </div>

      <!-- Address -->
      <div class="control-group">
          <label class="control-label fullstripe-form-label"><?php _e("Billing Address Street", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
          <div class="controls">
              <input type="text"  name="fullstripe_address_line1" id="fullstripe_address_line1" class="fullstripe-form-input"><br/>
          </div>
      </div>
      <div class="row-fluid">
        <div class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php _e("Country", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
              <select name="fullstripe_address_country" id="fullstripe_address_country">
                
              </select>
            </div>
        </div>
        <div class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php _e("Region", "wp-full-stripe"); ?></label>
            <div class="controls">
              <select name="fullstripe_address_state" id="fullstripe_address_state">
                
              </select>
            </div>
        </div>
        <div class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php _e("City", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text"  name="fullstripe_address_city" id="fullstripe_address_city" class="fullstripe-form-input"><br/>
            </div>
        </div>
        
        <div class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php _e("Zip / Postcode", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text" style="width: 60px;"  name="fullstripe_address_zip" id="fullstripe_address_zip" class="fullstripe-form-input"><br/>
            </div>
        </div>
      </div>
    </fieldset>