<?php

//deals with admin back-end input i.e. create plans, transfers
class MM_WPFS_Admin
{
    private $stripe = null;
    private $db = null;

    public function __construct()
    {
        $this->stripe = new MM_WPFS_Stripe();
        $this->db = new MM_WPFS_Database();
        $this->hooks();
    }

    private function hooks()
    {
        add_action('wp_ajax_wp_full_stripe_create_plan', array($this, 'fullstripe_create_plan_post'));
        add_action('wp_ajax_wp_full_stripe_create_subscripton_form', array($this, 'fullstripe_create_subscription_form_post'));
        add_action('wp_ajax_wp_full_stripe_edit_subscription_form', array($this, 'fullstripe_edit_subscription_form_post'));
        add_action('wp_ajax_wp_full_stripe_create_payment_form', array($this, 'fullstripe_create_payment_form_post'));
        add_action('wp_ajax_wp_full_stripe_edit_payment_form', array($this, 'fullstripe_edit_payment_form_post'));
        add_action('wp_ajax_wp_full_stripe_update_settings', array($this, 'fullstripe_update_settings_post'));
        add_action('wp_ajax_wp_full_stripe_delete_payment_form', array($this, 'fullstripe_delete_payment_form'));
        add_action('wp_ajax_wp_full_stripe_delete_subscription_form', array($this, 'fullstripe_delete_subscription_form'));
        add_action('wp_ajax_wp_full_stripe_create_recipient', array($this, 'fullstripe_create_recipient'));
        add_action('wp_ajax_wp_full_stripe_create_transfer', array($this, 'fullstripe_create_transfer'));
        add_action('wp_ajax_wp_full_stripe_create_checkout_form', array($this, 'fullstripe_create_checkout_form_post'));
        add_action('wp_ajax_wp_full_stripe_edit_checkout_form', array($this, 'fullstripe_edit_checkout_form_post'));
        add_action('wp_ajax_wp_full_stripe_delete_checkout_form', array($this, 'fullstripe_delete_checkout_form'));
    }

    function fullstripe_create_plan_post()
    {
        $id = $_POST['sub_id'];
        $name = $_POST['sub_name'];
        $amount = $_POST['sub_amount'];
        $interval = $_POST['sub_interval'];
        $intervalCount = $_POST['sub_interval_count'];
        $trial = $_POST['sub_trial'];

        $return = $this->stripe->create_plan($id, $name, $amount, $interval, $trial, $intervalCount);

        do_action('fullstripe_admin_create_plan_action', $return);

        header("Content-Type: application/json");
        echo json_encode($return);
        exit;
    }

    function fullstripe_create_subscription_form_post()
    {
        $name = $_POST['form_name'];
        $title = $_POST['form_title'];
        $plans = $_POST['selected_plans'];
        $showCustomInput = $_POST['form_include_custom_input'];
        $showCouponInput = $_POST['form_include_coupon_input'];
        $showAddressInput = $_POST['form_show_address_input'];
        $customInputTitle = isset($_POST['form_custom_input_label']) ? $_POST['form_custom_input_label'] : '';
        $buttonTitle = $_POST['form_button_text'];
        $setupFee = $_POST['form_setup_fee'];
        $doRedirect = $_POST['form_do_redirect'];
        $redirectPostID = isset($_POST['form_redirect_post_id']) ? $_POST['form_redirect_post_id'] : 0;

        $this->db->insert_subscription_form(
            array('name' => $name,
                'formTitle' => $title,
                'plans' => $plans,
                'showCouponInput' => $showCouponInput,
                'showCustomInput' => $showCustomInput,
                'customInputTitle' => $customInputTitle,
                'redirectOnSuccess' => $doRedirect,
                'redirectPostID' => $redirectPostID,
                'showAddress' => $showAddressInput,
                'buttonTitle' => $buttonTitle,
                'setupFee' => $setupFee
            )
        );

        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }

    function fullstripe_edit_subscription_form_post()
    {
        $id = $_POST['formID'];
        $name = $_POST['form_name'];
        $title = $_POST['form_title'];
        $plans = $_POST['selected_plans'];
        $showCustomInput = $_POST['form_include_custom_input'];
        $showCouponInput = $_POST['form_include_coupon_input'];
        $showAddressInput = $_POST['form_show_address_input'];
        $customInputTitle = isset($_POST['form_custom_input_label']) ? $_POST['form_custom_input_label'] : '';
        $buttonTitle = $_POST['form_button_text_sub'];
        $setupFee = $_POST['form_setup_fee'];
        $doRedirect = $_POST['form_do_redirect'];
        $redirectPostID = isset($_POST['form_redirect_post_id']) ? $_POST['form_redirect_post_id'] : 0;

        $this->db->update_subscription_form($id,
            array('name' => $name,
                'formTitle' => $title,
                'plans' => $plans,
                'showCouponInput' => $showCouponInput,
                'showCustomInput' => $showCustomInput,
                'customInputTitle' => $customInputTitle,
                'redirectOnSuccess' => $doRedirect,
                'redirectPostID' => $redirectPostID,
                'showAddress' => $showAddressInput,
                'buttonTitle' => $buttonTitle,
                'setupFee' => $setupFee
            )
        );

        header("Content-Type: application/json");
        echo json_encode(array('success' => true, 'redirectURL' => admin_url('admin.php?page=fullstripe-subscriptions')));
        exit;
    }

    function fullstripe_create_payment_form_post()
    {
        $name = $_POST['form_name'];
        $title = $_POST['form_title'];
        $amount = isset($_POST['form_amount']) ? $_POST['form_amount'] : '0';
        $custom = $_POST['form_custom'];
        $buttonTitle = $_POST['form_button_text'];
        $showButtonAmount = $_POST['form_button_amount'];
        $showCustomInput = $_POST['form_include_custom_input'];
        $customInputTitle = isset($_POST['form_custom_input_label']) ? $_POST['form_custom_input_label'] : '';
        $doRedirect = $_POST['form_do_redirect'];
        $redirectPostID = isset($_POST['form_redirect_post_id']) ? $_POST['form_redirect_post_id'] : 0;
        $showAddressInput = $_POST['form_show_address_input'];
        $sendEmailReceipt = isset($_POST['form_send_email_receipt']) ? $_POST['form_send_email_receipt'] : 0;
        $formStyle = $_POST['form_style'];

        $data = array(
            'name' => $name,
            'formTitle' => $title,
            'amount' => $amount,
            'customAmount' => $custom,
            'buttonTitle' => $buttonTitle,
            'showButtonAmount' => $showButtonAmount,
            'showCustomInput' => $showCustomInput,
            'customInputTitle' => $customInputTitle,
            'redirectOnSuccess' => $doRedirect,
            'redirectPostID' => $redirectPostID,
            'showAddress' => $showAddressInput,
            'sendEmailReceipt' => $sendEmailReceipt,
            'formStyle' => $formStyle
        );

        $this->db->insert_payment_form($data);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }

    function fullstripe_edit_payment_form_post()
    {
        $id = $_POST['formID'];
        $name = $_POST['form_name'];
        $title = $_POST['form_title'];
        $amount = isset($_POST['form_amount']) ? $_POST['form_amount'] : '0';
        $custom = $_POST['form_custom'];
        $buttonTitle = $_POST['form_button_text'];
        $showButtonAmount = $_POST['form_button_amount'];
        $showCustomInput = $_POST['form_include_custom_input'];
        $customInputTitle = isset($_POST['form_custom_input_label']) ? $_POST['form_custom_input_label'] : '';
        $doRedirect = $_POST['form_do_redirect'];
        $redirectPostID = isset($_POST['form_redirect_post_id']) ? $_POST['form_redirect_post_id'] : 0;
        $showAddressInput = $_POST['form_show_address_input'];
        $sendEmailReceipt = isset($_POST['form_send_email_receipt']) ? $_POST['form_send_email_receipt'] : 0;
        $formStyle = $_POST['form_style'];

        $data = array(
            'name' => $name,
            'formTitle' => $title,
            'amount' => $amount,
            'customAmount' => $custom,
            'buttonTitle' => $buttonTitle,
            'showButtonAmount' => $showButtonAmount,
            'showCustomInput' => $showCustomInput,
            'customInputTitle' => $customInputTitle,
            'redirectOnSuccess' => $doRedirect,
            'redirectPostID' => $redirectPostID,
            'showAddress' => $showAddressInput,
            'sendEmailReceipt' => $sendEmailReceipt,
            'formStyle' => $formStyle
        );

        $this->db->update_payment_form($id, $data);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true, 'redirectURL' => admin_url('admin.php?page=fullstripe-payments')));
        exit;
    }

    function fullstripe_create_checkout_form_post()
    {
        $name = $_POST['form_name_ck'];
        $companyName = $_POST['company_name_ck'];
        $amount = $_POST['form_amount_ck'];
        $prodDesc = $_POST['prod_desc_ck'];
        $openButtonText = $_POST['open_form_button_text_ck'];
        $buttonText = $_POST['form_button_text_ck'];
        $showBillingAddress = $_POST['form_show_address_input_ck'];
        $showRememberMe = $_POST['form_show_remember_me_ck'];
        $doRedirect = $_POST['form_do_redirect_ck'];
        $redirectPostID = isset($_POST['form_redirect_post_id_ck']) ? $_POST['form_redirect_post_id_ck'] : 0;
        $image = $_POST['form_checkout_image'];

        $data = array(
            'name' => $name,
            'companyName' => $companyName,
            'amount' => $amount,
            'productDesc' => $prodDesc,
            'openButtonTitle' => $openButtonText,
            'buttonTitle' => $buttonText,
            'showBillingAddress' => $showBillingAddress,
            'showRememberMe'  => $showRememberMe,
            'redirectOnSuccess' => $doRedirect,
            'redirectPostID' => $redirectPostID,
            'image' => $image
        );

        $this->db->insert_checkout_form($data);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }

    function fullstripe_edit_checkout_form_post()
    {
        $id = $_POST['formID'];
        $name = $_POST['form_name_ck'];
        $companyName = $_POST['company_name_ck'];
        $amount = $_POST['form_amount_ck'];
        $prodDesc = $_POST['prod_desc_ck'];
        $openButtonText = $_POST['open_form_button_text_ck'];
        $buttonText = $_POST['form_button_text_ck'];
        $showBillingAddress = $_POST['form_show_address_input_ck'];
        $showRememberMe = $_POST['form_show_remember_me_ck'];
        $doRedirect = $_POST['form_do_redirect_ck'];
        $redirectPostID = isset($_POST['form_redirect_post_id_ck']) ? $_POST['form_redirect_post_id_ck'] : 0;
        $image = $_POST['form_checkout_image'];

        $data = array(
            'name' => $name,
            'companyName' => $companyName,
            'amount' => $amount,
            'productDesc' => $prodDesc,
            'openButtonTitle' => $openButtonText,
            'buttonTitle' => $buttonText,
            'showBillingAddress' => $showBillingAddress,
            'showRememberMe'  => $showRememberMe,
            'redirectOnSuccess' => $doRedirect,
            'redirectPostID' => $redirectPostID,
            'image' => $image

        );

        $this->db->update_checkout_form($id, $data);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true, 'redirectURL' => admin_url('admin.php?page=fullstripe-payments')));
        exit;
    }

    function fullstripe_update_settings_post()
    {
        // Save the posted value in the database
        $options = get_option('fullstripe_options');
        $options['publishKey_test'] = trim($_POST['publishKey_test']);
        $options['secretKey_test'] = trim($_POST['secretKey_test']);
        $options['publishKey_live'] = trim($_POST['publishKey_live']);
        $options['secretKey_live'] = trim($_POST['secretKey_live']);
        $options['apiMode'] = $_POST['apiMode'];
        $options['currency'] = $_POST['currency'];
        $options['form_css'] = stripslashes($_POST['form_css']);
        $options['includeStyles'] = $_POST['includeStyles'];
        $options['receiptEmailType'] = $_POST['receiptEmailType'];
        $options['email_receipt_subject'] = $_POST['email_receipt_subject'];
        $options['email_receipt_html'] = htmlentities(stripslashes($_POST['email_receipt_html']));
        $options['admin_payment_receipt'] = $_POST['admin_payment_receipt'];
        update_option('fullstripe_options', $options);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }

    function fullstripe_delete_payment_form()
    {
        $id = $_POST['id'];
        do_action('fullstripe_admin_delete_payment_form_action', $id);

        $this->db->delete_payment_form($id);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }

    function fullstripe_delete_subscription_form()
    {
        $id = $_POST['id'];
        do_action('fullstripe_admin_delete_subscription_form_action', $id);

        $this->db->delete_subscription_form($id);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }

    function fullstripe_delete_checkout_form()
    {
        $id = $_POST['id'];
        do_action('fullstripe_admin_delete_checkout_form_action', $id);

        $this->db->delete_checkout_form($id);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }

    function fullstripe_create_recipient()
    {
        $bank = $_POST['stripeToken'];
        $name = $_POST['recipient_name'];
        $type = $_POST['recipient_type'];
        $taxID = $_POST['recipient_tax_id'];
        $email = $_POST['recipient_email'];

        try
        {
            $data = array(
                'name' => $name,
                'type' => $type,
                'bank_account' => $bank,
                'tax_id' => $taxID,
                'email' => $email
            );

            $recipient = $this->stripe->create_recipient($data);

            do_action('fullstripe_admin_create_recipient_action', $recipient);

            $return = array('success' => true, 'msg' => 'Recipient created',
                'recipient' => array('name' => $recipient->name, 'id' => $recipient->id, 'type' => $recipient->type, 'email' => $recipient->email));

        }
        catch (Exception $e)
        {
            //show notification of error
            $return = array('success' => false, 'msg' => __('There was an error creating the recipient: ', 'wp-full-stripe') . $e->getMessage());
        }

        //correct way to return JS results in wordpress
        header("Content-Type: application/json");
        echo json_encode($return);
        exit;
    }

    function fullstripe_create_transfer()
    {
        $amount = $_POST['transfer_amount'];
        $desc = $_POST['transfer_desc'];
        $recipient = $_POST['transfer_recipient'];

        try
        {
            $transfer = $this->stripe->create_transfer(array(
                "amount" => $amount,
                "currency" => "usd",
                "recipient" => $recipient,
                "statement_description" => $desc));

            do_action('fullstripe_admin_create_transfer_action', $transfer);

            $return = array('success' => true);
        }
        catch (Exception $e)
        {
            //show notification of error
            $return = array('success' => false, 'msg' => __('There was an error creating the transfer: ', 'wp-full-stripe') . $e->getMessage());
        }

        header("Content-Type: application/json");
        echo json_encode($return);
        exit;
    }

}


?>