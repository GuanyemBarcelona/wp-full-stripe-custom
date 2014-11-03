<?php

//deals with customer front-end input i.e. payment forms submission
class MM_WPFS_Customer
{
    private $stripe = null;

    public function __construct()
    {
        $this->stripe = new MM_WPFS_Stripe();
        $this->db = new MM_WPFS_Database();
        $this->hooks();
    }

    private function hooks()
    {
        add_action('wp_ajax_wp_full_stripe_payment_charge', array($this, 'fullstripe_payment_charge'));
        add_action('wp_ajax_nopriv_wp_full_stripe_payment_charge', array($this, 'fullstripe_payment_charge'));
        add_action('wp_ajax_wp_full_stripe_subscription_charge', array($this, 'fullstripe_subscription_charge'));
        add_action('wp_ajax_nopriv_wp_full_stripe_subscription_charge', array($this, 'fullstripe_subscription_charge'));
        add_action('wp_ajax_wp_full_stripe_check_coupon', array($this, 'fullstripe_check_coupon'));
        add_action('wp_ajax_nopriv_wp_full_stripe_check_coupon', array($this, 'fullstripe_check_coupon'));
        add_action('wp_ajax_fullstripe_checkout_form_charge', array($this, 'fullstripe_checkout_charge'));
        add_action('wp_ajax_nopriv_fullstripe_checkout_form_charge', array($this, 'fullstripe_checkout_charge'));
    }

    function fullstripe_payment_charge()
    {
        //get POST data from form
        $valid = true;
        $card = $_POST['stripeToken'];
        $name = sanitize_text_field($_POST['fullstripe_name']);
        $amount = $_POST['amount'];
        $formName = $_POST['formName'];
        $isCustom = $_POST['isCustom'];
        $doRedirect = $_POST['formDoRedirect'];
        $redirectPostID = $_POST['formRedirectPostID'];
        $showAddress = $_POST['showAddress'];
        $sendReceipt = $_POST['sendEmailReceipt'];
        $options = get_option('fullstripe_options');

        if ($isCustom == 1)
        {
            $amount = $_POST['fullstripe_custom_amount'];
            if (!is_numeric($amount))
            {
                $valid = false;
                $return = array('success' => false, 'msg' => __('The payment amount is invalid, please only use numbers and a decimal point', 'wp-full-stripe'));
            }
            else
            {
                $amount = $amount * 100; //Stripe expects amounts in cents/pence
            }
        }

        $address1 = isset($_POST['fullstripe_address_line1']) ? sanitize_text_field($_POST['fullstripe_address_line1']) : '';
        $address2 = isset($_POST['fullstripe_address_line2']) ? sanitize_text_field($_POST['fullstripe_address_line2']) : '';
        $city = isset($_POST['fullstripe_address_city']) ? sanitize_text_field($_POST['fullstripe_address_city']) : '';
        $state = isset($_POST['fullstripe_address_state']) ? sanitize_text_field($_POST['fullstripe_address_state']) : '';
        $zip = isset($_POST['fullstripe_address_zip']) ? sanitize_text_field($_POST['fullstripe_address_zip']) : '';

        if ($showAddress == 1)
        {
            if ($address1 == '' || $city == '' || $zip == '')
            {
                $valid = false;
                $return = array('success' => false, 'msg' => __('Please enter a valid billing address', 'wp-full-stripe'));
            }
        }

        $email = 'n/a';
        if (isset($_POST['fullstripe_email']))
        {
           $email = $_POST['fullstripe_email'];
           if (!filter_var($email, FILTER_VALIDATE_EMAIL))
           {
               $valid = false;
               $return = array('success' => false, 'msg' => __('Please enter a valid email address', 'wp-full-stripe'));
           }
        }

        if ($valid)
        {
            $customInput = isset($_POST['fullstripe_custom_input']) ? $_POST['fullstripe_custom_input'] : 'n/a';
            $description = "Payment from $name on form: $formName";
            $metadata = array(
                'customer_name' => $name,
                'customer_email' => $email,
                'billing_address_line1' => $address1,
                'billing_address_line2' => $address2,
                'billing_address_city' => $city,
                'billing_address_state' => $state,
                'billing_address_zip' => $zip
            );

            try
            {
                //check email
                $sendPluginEmail = true;
                if ($options['receiptEmailType'] == 'stripe' && $sendReceipt == 1 && isset($_POST['fullstripe_email']))
                {
                    $sendPluginEmail = false;
                }

                do_action('fullstripe_before_payment_charge', $amount);
                //create a customer object
                $stripeCustomer = $this->stripe->create_customer($card, $email, $metadata);
                //try the charge
                $metadata['custom_input'] = $customInput;
                $result = $this->stripe->charge_customer($stripeCustomer->id, $amount, $description, $metadata,($sendPluginEmail==false ? $email : null));
                do_action('fullstripe_after_payment_charge', $result);

                //save the payment
                $address = array('line1' => $address1, 'line2' => $address2, 'city' => $city, 'state' => $state, 'zip' => $zip);
                $this->db->fullstripe_insert_payment($result, $address, $stripeCustomer->id);

                $return = array('success' => true, 'msg' => 'Payment Successful!');
                if ($doRedirect == 1)
                {
                    $return['redirect'] = true;
                    $return['redirectURL'] = get_page_link($redirectPostID);
                }

                //send email receipt (it is better if done in a background thread...)
                if ($sendPluginEmail && $sendReceipt == 1 && isset($_POST['fullstripe_email']))
                {
                    $this->fullstripe_send_email_receipt($email, $amount, $name, $address);
                }

            }
            catch (Exception $e)
            {
                //show notification of error
                $return = array('success' => false, 'msg' => __('There was an error processing your payment: ', 'wp-full-stripe') . $e->getMessage());
            }
        }

        //correct way to return JS results in wordpress
        header("Content-Type: application/json");
        echo json_encode(apply_filters('fullstripe_payment_charge_return_message', $return));
        exit;
    }


    function fullstripe_subscription_charge()
    {
        $card = $_POST['stripeToken'];
        $name = $_POST['fullstripe_name'];
        $plan = $_POST['fullstripe_plan'];
        $customInput = isset($_POST['fullstripe_custom_input']) ? $_POST['fullstripe_custom_input'] : 'n/a';
        $couponCode = isset($_POST['fullstripe_coupon_input']) ? $_POST['fullstripe_coupon_input'] : '';
        $doRedirect = $_POST['formDoRedirect'];
        $redirectPostID = $_POST['formRedirectPostID'];
        $address1 = isset($_POST['fullstripe_address_line1']) ? sanitize_text_field($_POST['fullstripe_address_line1']) : '';
        $address2 = isset($_POST['fullstripe_address_line2']) ? sanitize_text_field($_POST['fullstripe_address_line2']) : '';
        $city = isset($_POST['fullstripe_address_city']) ? sanitize_text_field($_POST['fullstripe_address_city']) : '';
        $state = isset($_POST['fullstripe_address_state']) ? sanitize_text_field($_POST['fullstripe_address_state']) : '';
        $zip = isset($_POST['fullstripe_address_zip']) ? sanitize_text_field($_POST['fullstripe_address_zip']) : '';
        $setupFee = $_POST['fullstripe_setupFee'];

        //validation
        $valid = true;
        $email = '';
        if (isset($_POST['fullstripe_email']))
        {
            $email = $_POST['fullstripe_email'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $valid = false;
                $return = array('success' => false, 'msg' => __('Please enter a valid email address', 'wp-full-stripe'));
            }
        }
        else
        {
            $valid = false;
            $return = array('success' => false, 'msg' => __('Please enter a valid email address', 'wp-full-stripe'));
        }

        if ($valid)
        {
            $description =  "Subscriber: " . $name;
            $metadata = array(
                'customer_name' => $name,
                'customer_email' => $email,
                'billing_address_line1' => $address1,
                'billing_address_line2' => $address2,
                'billing_address_city' => $city,
                'billing_address_state' => $state,
                'billing_address_zip' => $zip,
                'custom_input' => $customInput
            );

            try
            {
                do_action('fullstripe_before_subscription_charge', $plan);
                $customer = $this->stripe->subscribe($plan, $card, $email, $description, $couponCode, $setupFee, $metadata);
                do_action('fullstripe_after_subscription_charge', $customer);

                $this->db->fullstripe_insert_subscriber($customer, $name, array('line1' => $address1, 'line2' => $address2, 'city' => $city, 'state' => $state,  'zip' => $zip));

                $return = array('success' => true, 'msg' => 'Payment Successful. Thanks for subscribing!');
                if ($doRedirect == 1)
                {
                    $return['redirect'] = true;
                    $return['redirectURL'] = get_page_link($redirectPostID);
                }

            }
            catch (Exception $e)
            {
                //show notification of error
                $return = array('success' => false, 'msg' => __('There was an error processing your payment: ', 'wp-full-stripe') . $e->getMessage());
            }
        }

        //correct way to return JS results in wordpress
        header("Content-Type: application/json");
        echo json_encode(apply_filters('fullstripe_subscription_charge_return_message', $return));
        exit;
    }

    function fullstripe_checkout_charge()
    {
        //get POST data from form
        $token = $_POST['stripeToken'];
        $email = $_POST['stripeEmail'];
        $form = $_POST['form'];
        $doRedirect = $_POST['doRedirect'];
        $redirectPostID = $_POST['redirectId'];
        //TODO: get billing address and save in DB with payment. Look into pros/cons.
        //...
        //get form
        $formData = $this->db->get_checkout_form_by_name($form);
        $amount = $formData["amount"];
        $description = "Payment for " . $formData["productDesc"];

        try
        {
            do_action('fullstripe_before_checkout_payment_charge', $amount);
            //create a customer object
            $stripeCustomer = $this->stripe->create_customer($token, $email, null);
            //try the charge
            $result = $this->stripe->charge_customer($stripeCustomer->id, $amount, $description);
            do_action('fullstripe_after_checkout_payment_charge', $result);

            //save the payment
            $this->db->fullstripe_insert_payment($result, array('line1' => '', 'line2' => '', 'city' => '', 'state' => '', 'zip' => ''), $stripeCustomer->id);

            $return = array('success' => true, 'msg' => 'Payment Successful!');
            if ($doRedirect == 1)
            {
                $return['redirect'] = true;
                $return['redirectURL'] = get_page_link($redirectPostID);
            }
        }
        catch (Exception $e)
        {
            //show notification of error
            $return = array('success' => false, 'msg' => __('There was an error processing your payment: ', 'wp-full-stripe') . $e->getMessage());
        }

        header("Content-Type: application/json");
        echo json_encode(apply_filters('fullstripe_checkout_charge_return_message', $return));
        exit;
    }


    function fullstripe_check_coupon()
    {
        $code = $_POST['code'];

        try
        {
            $coupon = $this->stripe->get_coupon($code);

            if ($coupon->valid == false)
            {
                $return = array('msg' => "This coupon has expired", 'valid' => false);
            }
            else
            {
                $return = array('msg' => "The coupon has been applied successfully",
                    'coupon' => array('percent_off' => $coupon->percent_off, 'amount_off' => $coupon->amount_off),
                    'valid' => true);
            }
        }
        catch (Exception $e)
        {
            $return = array('msg' => "You have entered an invalid coupon code", 'valid' => false);
        }

        header("Content-Type: application/json");
        echo json_encode($return);
        exit;
    }

    function fullstripe_send_email_receipt($email, $amount, $cardholderName, $billingAddress)
    {
        $name = get_bloginfo('name');
        $admin_email = get_bloginfo('admin_email');
        $headers[] = "From: $name <$admin_email>";
        $headers[] = "Content-type: text/html";

        $options = get_option('fullstripe_options');
        //saved in db using htmlentities()
        $msg = html_entity_decode($options['email_receipt_html']);
        $cur = $options['currency'];
        $symbol = '$';
        if ($cur === 'eur') $symbol = '€';
        else if ($cur === 'gbp') $symbol = '£';

        $msg = str_replace(
            array(
                "%AMOUNT%",
                "%NAME%",
                "%CUSTOMERNAME%",
                "%ADDRESS1%",
                "%ADDRESS2%",
                "%CITY%",
                "%STATE%",
                "%ZIP%"),
            array(
                $symbol . sprintf('%0.2f', $amount / 100),
                $name,
                $cardholderName,
                $billingAddress['line1'],
                $billingAddress['line2'],
                $billingAddress['city'],
                $billingAddress['state'],
                $billingAddress['zip']),
            $msg);

        wp_mail($email,
            apply_filters('fullstripe_email_subject_filter', $options['email_receipt_subject']),
            apply_filters('fullstripe_email_message_filter', $msg),
            apply_filters('fullstripe_email_headers_filter', $headers));

        if ($options['admin_payment_receipt'] == 1)
        {
            wp_mail($admin_email,
                "COPY: " . apply_filters('fullstripe_email_subject_filter', $options['email_receipt_subject']),
                apply_filters('fullstripe_email_message_filter', $msg),
                apply_filters('fullstripe_email_headers_filter', $headers));
        }
    }
}