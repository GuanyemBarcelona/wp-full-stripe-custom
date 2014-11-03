<form class="fullstripe_checkout_form" action="" method="POST" id="fullstripe_checkout_form">
    <input type="hidden" name="action" value="fullstripe_checkout_form_charge"/>
    <p class="payment-errors"></p>
    <button id="fullstripe_checkout_button" class="stripe-button-el" type="submit">
        <span id="fullstripe_checkout_button_text" style="display: block; min-height: 30px;">Pay With Card</span>
    </button>
    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." id="showLoading"/>
</form>