<h4><span class="fullstripe-form-title"><?php echo $formData->formTitle; ?></span></h4>
<form action="" method="POST" id="payment-form-style">
    <input type="hidden" name="action" value="wp_full_stripe_payment_charge"/>
    <input type="hidden" name="amount" value="<?php echo $formData->amount; ?>"/>
    <input type="hidden" name="formName" value="<?php echo $formData->name; ?>"/>
    <input type="hidden" name="isCustom" value="<?php echo $formData->customAmount; ?>"/>
    <input type="hidden" name="formDoRedirect" value="<?php echo $formData->redirectOnSuccess; ?>"/>
    <input type="hidden" name="formRedirectPostID" value="<?php echo $formData->redirectPostID; ?>"/>
    <input type="hidden" name="showAddress" value="<?php echo $formData->showAddress; ?>"/>
    <input type="hidden" name="sendEmailReceipt" value="<?php echo $formData->sendEmailReceipt; ?>"/>
    <p class="payment-errors"></p>
    <?php if ($formData->showEmailInput == 1): ?>
        <div class="_100">
            <label class="control-label fullstripe-form-label">Email Address</label>
            <input type="text" name="fullstripe_email" id="fullstripe_email">
        </div>
    <?php endif; ?>
    <?php if ($formData->showCustomInput == 1): ?>
        <div class="_100">
            <label class="control-label fullstripe-form-label"><?php echo $formData->customInputTitle; ?></label>
            <input type="text" name="fullstripe_custom_input" id="fullstripe_custom_input">
        </div>
    <?php endif; ?>
    <?php if ($formData->customAmount == 1): ?>
        <div class="_100">
            <label class="control-label fullstripe-form-label">Payment Amount</label><br/>
            <input type="text" style="width: 100px;" name="fullstripe_custom_amount" id="fullstripe_custom_amount">
        </div>
    <?php endif; ?>

    <?php if ($formData->showAddress == 1): ?>
        <div class="_100">
            <label class="control-label fullstripe-form-label">Billing Address Street</label>
            <input type="text" name="fullstripe_address_line1" id="fullstripe_address_line1"><br/>
        </div>
        <div class="_100">
            <label class="control-label fullstripe-form-label">Billing Address Line 2</label>
            <input type="text" name="fullstripe_address_line2" id="fullstripe_address_line2"><br/>
        </div>
        <div class="_100">
            <label class="control-label fullstripe-form-label">City</label>
            <input type="text" name="fullstripe_address_city" id="fullstripe_address_city"><br/>
        </div>
        <div class="_50">
            <label class="control-label fullstripe-form-label"><?php echo $localeState; ?></label><br/>
            <input type="text" name="fullstripe_address_state" id="fullstripe_address_state">
        </div>
        <div class="_50">
            <label class="control-label fullstripe-form-label"><?php echo $localeZip; ?></label><br/>
            <input type="text" name="fullstripe_address_zip" id="fullstripe_address_zip">
        </div>
        <div class="_100">
            <hr/>
        </div>
    <?php endif; ?>
    <div class="_100" style="padding-bottom: 5px;">
        <img src="<?php echo plugins_url('../img/' . $creditCardImage, dirname(__FILE__)); ?>" alt="Credit Cards"/>
    </div>
    <div class="_50">
        <label class="control-label fullstripe-form-label">Card Holder's Name</label>
        <input type="text" name="fullstripe_name" id="fullstripe_name">
    </div>
    <div class="_50">
        <label class="control-label fullstripe-form-label">Card Number</label>
        <input type="text" autocomplete="off" size="20" data-stripe="number">
    </div>
    <div class="_50">
        <label class="control-label fullstripe-form-label">Card CVV</label><br/>
        <input type="password" autocomplete="off" size="4" data-stripe="cvc" style="width: 80px;"/>
    </div>
    <div class="_25">
        <label class="control-label fullstripe-form-label">Month</label>
        <select data-stripe="exp-month">
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
    </div>
    <div class="_25">
        <label class="control-label fullstripe-form-label">Year</label>
        <select data-stripe="exp-year">
            <?php
            $startYear = date('Y');
            $numYears = 20;
            for ($i = 0; $i < $numYears; $i++)
            {
                $yr = $startYear + $i;
                echo "<option value='" . $yr . "'>" . $yr . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="_100">
        <br/>
    </div>
    <div class="_100">
        <?php if ($formData->customAmount == 0): ?>
            <button type="submit"><?php echo $formData->buttonTitle; ?> <?php if ($formData->showButtonAmount == 1)
                {
                    echo $currencySymbol . sprintf('%0.2f', $formData->amount / 100.0);
                }  ?></button>
        <?php else: ?>
            <button type="submit"><?php echo $formData->buttonTitle; ?></button>
        <?php endif; ?>
        <img src="<?php echo plugins_url('../img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." id="showLoading"/>
    </div>
</form>