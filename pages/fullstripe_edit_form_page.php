<?php
global $wpdb;
//get the data we need
$formID = -1;
$formType = "";
if (isset($_GET['form']))
    $formID = $_GET['form'];
if (isset($_GET['type']))
    $formType = $_GET['type'];

$valid = true;
if ($formID == -1 || $formType == "")
    $valid = false;

$editForm = null;
$plans = array();

if ($valid)
{

    if ($formType == "payment")
    {
        $editForm = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "fullstripe_payment_forms WHERE paymentFormID=%d", $formID));
    }
    else if ($formType == "subscription")
    {
        $editForm = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "fullstripe_subscription_forms WHERE subscriptionFormID=%d", $formID));
        $plans = MM_WPFS::getInstance()->get_plans();
    }
    else if ($formType == "checkout")
    {
        $editForm = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "fullstripe_checkout_forms WHERE checkoutFormID=%d", $formID));
    }
    else
        $valid = false;

    if ($editForm == null) $valid = false;
}
?>
<div class="wrap">
<h2> <?php echo __('Full Stripe Edit Form', 'wp-full-stripe'); ?> </h2>
<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
<?php if (!$valid): ?>
    <p>Form not found!</p>
<?php else: ?>
    <?php if ($formType == "payment"): ?>
        <form class="form-horizontal" action="" method="POST" id="edit-payment-form">
            <p class="tips"></p>
            <input type="hidden" name="action" value="wp_full_stripe_edit_payment_form">
            <input type="hidden" name="formID" value="<?php echo $editForm->paymentFormID; ?>">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Form Name: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_name" id="form_name" value="<?php echo $editForm->name; ?>">
                        <p class="description">This name will be used to identify this form in the shortcode i.e. [fullstripe_payment form="FormName"]</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Form Title: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_title" id="form_title" value="<?php echo $editForm->formTitle; ?>">
                        <p class="description">The title of the form</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Payment Type: </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_custom" id="set_specific_amount" value="0" <?php echo ($editForm->customAmount == '0') ? 'checked' : '' ?> > Set Amount
                        </label> <label class="radio inline">
                            <input type="radio" name="form_custom" id="set_custom_amount" value="1" <?php echo ($editForm->customAmount == '1') ? 'checked' : '' ?> > Custom Amount
                        </label>
                        <p class="description">Choose to set a specific amount for this form, or allow customers to set custom amounts</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Payment Amount: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_amount" id="form_amount" value="<?php echo $editForm->amount; ?>"/>
                        <p class="description">The amount this form will charge your customer, in cents. i.e. for $10.00 enter 1000</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Payment Button Text: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_button_text" id="form_button_text" value="<?php echo $editForm->buttonTitle; ?>">
                        <p class="description">The text on the payment button</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Include Amount on Button? </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_button_amount" id="hide_button_amount" value="0" <?php echo ($editForm->showButtonAmount == '0') ? 'checked' : '' ?> > Hide
                        </label> <label class="radio inline">
                            <input type="radio" name="form_button_amount" id="show_button_amount" value="1" <?php echo ($editForm->showButtonAmount == '1') ? 'checked' : '' ?> > Show
                        </label>
                        <p class="description">For set amount forms, choose to show/hide the amount on the payment button</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Include Email Address Field? </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_show_email_input" id="hide_email_input" value="0" <?php echo ($editForm->showEmailInput == '0') ? 'checked' : '' ?> > Hide
                        </label> <label class="radio inline">
                            <input type="radio" name="form_show_email_input" id="show_email_input" value="1" <?php echo ($editForm->showEmailInput == '1') ? 'checked' : '' ?> > Show
                        </label>
                        <p class="description">Should this payment form also ask for the customers email address?</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Send Email Receipt? </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_send_email_receipt" value="0" <?php echo ($editForm->sendEmailReceipt == '0') ? 'checked' : '' ?>> No
                        </label> <label class="radio inline">
                            <input type="radio" name="form_send_email_receipt" value="1" <?php echo ($editForm->sendEmailReceipt == '1') ? 'checked' : '' ?>> Yes
                        </label>
                        <p class="description">Send an email receipt on successful payment? Must include email address field on this form.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Include Billing Address Field? </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_show_address_input" id="hide_address_input" value="0" <?php echo ($editForm->showAddress == '0') ? 'checked' : '' ?> > Hide
                        </label> <label class="radio inline">
                            <input type="radio" name="form_show_address_input" id="show_address_input" value="1" <?php echo ($editForm->showAddress == '1') ? 'checked' : '' ?> > Show
                        </label>
                        <p class="description">Should this payment form also ask for the customers billing address?</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Include Custom Input Field? </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_include_custom_input" id="noinclude_custom_input" value="0" <?php echo ($editForm->showCustomInput == '0') ? 'checked' : '' ?> > No
                        </label> <label class="radio inline">
                            <input type="radio" name="form_include_custom_input" id="include_custom_input" value="1" <?php echo ($editForm->showCustomInput == '1') ? 'checked' : '' ?> > Yes
                        </label>
                        <p class="description">You can ask for extra information from the customer to be included in the payment details</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Custom Input Label: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_custom_input_label" id="form_custom_input_label" <?php echo ($editForm->showCustomInput == '0') ? 'disabled="disabled"' : '' ?> value="<?php echo $editForm->customInputTitle; ?>"/>
                        <p class="description">The text for the label next to the custom input field</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Redirect On Success?</label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_do_redirect" id="do_redirect_no" value="0" <?php echo ($editForm->redirectOnSuccess == '0') ? 'checked' : '' ?> > No
                        </label> <label class="radio inline">
                            <input type="radio" name="form_do_redirect" id="do_redirect_yes" value="1" <?php echo ($editForm->redirectOnSuccess == '1') ? 'checked' : '' ?> > Yes
                        </label>
                        <p class="description">When payment is successful you can choose to redirect to another page or post</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Redirect Page/Post ID: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_redirect_post_id" id="form_redirect_post_id" <?php echo ($editForm->redirectOnSuccess == '0') ? 'disabled="disabled"' : '' ?> value="<?php echo $editForm->redirectPostID; ?>"/>
                        <p class="description">The ID for the page/post to redirect to after payment is successful</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Form Style: </label>
                    </th>
                    <td>
                        <select class="regular-text" name="form_style" id="form_style">
                            <option value="0" <?php if ($editForm->formStyle == 0) echo 'selected="selected"'; ?> >Default</option>
                            <option value="1" <?php if ($editForm->formStyle == 1) echo 'selected="selected"'; ?> >Compact</option>
                        </select>
                        <p class="description">Choose how you'd like the form to look. (More coming soon!)</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button class="button button-primary" type="submit">Save Changes</button>
                <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
            </p>
        </form>
    <?php elseif ($formType == "subscription"): ?>
        <form class="form-horizontal" action="" method="POST" id="edit-subscription-form">
            <p class="tips"></p>
            <input type="hidden" name="action" value="wp_full_stripe_edit_subscription_form"/>
            <input type="hidden" name="formID" value="<?php echo $editForm->subscriptionFormID; ?>">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Form Name: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_name" id="form_name" value="<?php echo $editForm->name; ?>">
                        <p class="description">This name will be used to identify this form in the shortcode i.e. [fullstripe_subscription form="FormName"]</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Form Title: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_title" id="form_title" value="<?php echo $editForm->formTitle; ?>">
                        <p class="description">The title of the form</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Include Coupon Input Field? </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_include_coupon_input" id="noinclude_coupon_input" value="0" <?php echo ($editForm->showCouponInput == '0') ? 'checked' : '' ?> > No
                        </label> <label class="radio inline">
                            <input type="radio" name="form_include_coupon_input" id="include_coupon_input" value="1" <?php echo ($editForm->showCouponInput == '1') ? 'checked' : '' ?> > Yes
                        </label>
                        <p class="description">You can allow customers to input coupon codes for discounts. Must create the coupon in your Stripe account dashboard.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Include Billing Address Field? </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_show_address_input" id="hide_address_input" value="0" <?php echo ($editForm->showAddress == '0') ? 'checked' : '' ?> > Hide
                        </label> <label class="radio inline">
                            <input type="radio" name="form_show_address_input" id="show_address_input" value="1" <?php echo ($editForm->showAddress == '1') ? 'checked' : '' ?> > Show
                        </label>
                        <p class="description">Should this form also ask for the customers billing address?</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Include Custom Input Field? </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_include_custom_input" id="noinclude_custom_input" value="0" <?php echo ($editForm->showCustomInput == '0') ? 'checked' : '' ?> > No
                        </label> <label class="radio inline">
                            <input type="radio" name="form_include_custom_input" id="include_custom_input" value="1" <?php echo ($editForm->showCustomInput == '1') ? 'checked' : '' ?> > Yes
                        </label>
                        <p class="description">You can ask for extra information from the customer to be included in the payment details</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Custom Input Label: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_custom_input_label" id="form_custom_input_label"  <?php echo ($editForm->showCustomInput == '0') ? 'disabled="disabled"' : '' ?> value="<?php echo $editForm->customInputTitle; ?>"/>
                        <p class="description">The text for the label next to the custom input field</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Subscribe Button Text: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_button_text_sub" id="form_button_text_sub" value="<?php echo $editForm->buttonTitle; ?>">
                        <p class="description">The text on the subscribe button</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Setup Fee: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_setup_fee" id="form_setup_fee" value="<?php echo $editForm->setupFee; ?>">
                        <p class="description">Amount to charge the customer to setup the subscription. Entering 0 will disable. (in cents. i.e. for $10.00 enter 1000)</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Plans: </label>
                    </th>
                    <td>
                        <div class="plan_checkboxes">
                            <?php $formPlans = explode(",", $editForm->plans); ?>
                            <?php foreach ($plans['data'] as $plan): ?>
                                <label class="checkbox inline">
                                    <p>
                                        <input type="checkbox" class="plan_checkbox" id="check_<?php echo $plan->id; ?>" value="<?php echo $plan->id; ?>" <?php echo (in_array($plan->id, $formPlans)) ? 'checked' : '' ?> >
                                        <span class="plan_checkbox_text"><?php echo $plan->name; ?> (
                                            <?php
                                            $str = sprintf('$%0.2f', $plan->amount / 100.0);
                                            if ($plan->interval_count == 1)
                                            {
                                                $str .= ' ' . ucfirst($plan->interval) . 'ly';
                                            }
                                            else
                                            {
                                                $str .= ' every ' . $plan->interval_count . ' ' . $plan->interval . 's';
                                            }
                                            echo $str;
                                            ?>
                                            )</span>
                                    </p>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="description">Which subscription plans can be chosen on this form</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Redirect On Success?</label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_do_redirect" id="do_redirect_no" value="0" <?php echo ($editForm->redirectOnSuccess == '0') ? 'checked' : '' ?> > No
                        </label> <label class="radio inline">
                            <input type="radio" name="form_do_redirect" id="do_redirect_yes" value="1" <?php echo ($editForm->redirectOnSuccess == '1') ? 'checked' : '' ?> > Yes
                        </label>
                        <p class="description">When payment is successful you can choose to redirect to another page or post</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Redirect Page/Post ID: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_redirect_post_id" id="form_redirect_post_id"  <?php echo ($editForm->redirectOnSuccess == '0') ? 'disabled="disabled"' : '' ?> value="<?php echo $editForm->redirectPostID; ?>"/>
                        <p class="description">The ID for the page/post to redirect to after payment is successful</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button class="button button-primary" type="submit">Save Changes</button>
                <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
            </p>
        </form>
    <?php elseif ($formType == "checkout"): ?>
        <form class="form-horizontal" action="" method="POST" id="edit-checkout-form">
            <p class="tips"></p>
            <input type="hidden" name="action" value="wp_full_stripe_edit_checkout_form">
            <input type="hidden" name="formID" value="<?php echo $editForm->checkoutFormID; ?>">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Form Name: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_name_ck" id="form_name_ck" value="<?php echo $editForm->name; ?>">
                        <p class="description">This name will be used to identify this form in the shortcode i.e. [fullstripe_checkout form="FormName"]</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Company Name: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="company_name_ck" id="company_name_ck" value="<?php echo $editForm->companyName; ?>">
                        <p class="description">Used as the title of the checkout form</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Product Description: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="prod_desc_ck" id="prod_desc_ck" value="<?php echo $editForm->productDesc; ?>">
                        <p class="description">A short description (one line) about the product sold using this form</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Payment Amount: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_amount_ck" id="form_amount_ck" value="<?php echo $editForm->amount; ?>"/>
                        <p class="description">The amount this form will charge your customer, in cents. i.e. for $10.00 enter 1000</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Open Form Button Text: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="open_form_button_text_ck" id="open_form_button_text_ck" value="<?php echo $editForm->openButtonTitle; ?>">
                        <p class="description">The text on the button used to pop open this form</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Payment Button Text: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_button_text_ck" id="form_button_text_ck" value="<?php echo $editForm->buttonTitle; ?>" >
                        <p class="description">The text on the payment button. Use {{amount}} to show the payment amount on this button.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Include Billing Address Field? </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_show_address_input_ck" id="hide_address_input_ck" value="0" <?php echo ($editForm->showBillingAddress == '0') ? 'checked' : '' ?> > Hide
                        </label> <label class="radio inline">
                            <input type="radio" name="form_show_address_input_ck" id="show_address_input_ck" value="1" <?php echo ($editForm->showBillingAddress == '1') ? 'checked' : '' ?> > Show
                        </label>
                        <p class="description">Should this payment form also ask for the customers billing address?</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Include Remember Me Field? </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_show_remember_me_ck" id="hide_remember_me_ck" value="0" <?php echo ($editForm->showRememberMe == '0') ? 'checked' : '' ?>> Hide
                        </label> <label class="radio inline">
                            <input type="radio" name="form_show_remember_me_ck" id="show_remember_me_ck" value="1" <?php echo ($editForm->showRememberMe == '1') ? 'checked' : '' ?> > Show
                        </label>
                        <p class="description">Show the Stripe Remember Me checkbox, allowing users to save their information with Stripe for later use.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Image</label>
                    </th>
                    <td>
                        <input id="form_checkout_image" type="text" name="form_checkout_image" value="<?php echo $editForm->image; ?>"/>
                        <button id="upload_image_button" class="button" type="button" value="Upload Image">Upload Image</button>
                        <p class="description">A square image of your brand or product which is shown on the form.  Min size 128px x 128px.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Redirect On Success?</label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_do_redirect_ck" id="do_redirect_no_ck" value="0" <?php echo ($editForm->redirectOnSuccess == '0') ? 'checked' : '' ?> > No
                        </label> <label class="radio inline">
                            <input type="radio" name="form_do_redirect_ck" id="do_redirect_yes_ck" value="1" <?php echo ($editForm->redirectOnSuccess == '1') ? 'checked' : '' ?> > Yes
                        </label>
                        <p class="description">When payment is successful you can choose to redirect to another page or post</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Redirect Page/Post ID: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="form_redirect_post_id_ck" id="form_redirect_post_id_ck"  <?php echo ($editForm->redirectOnSuccess == '0') ? 'disabled="disabled"' : '' ?> value="<?php echo $editForm->redirectPostID; ?>"/>
                        <p class="description">The ID for the page/post to redirect to after payment is successful</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button class="button button-primary" type="submit">Save Changes</button>
                <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
            </p>
        </form>
    <?php endif; ?>
<?php endif; ?>
</div>