<?php
interface MM_WPFS_Payment_API
{
    function charge($amount, $card, $description, $metadata = null, $stripeEmail = null);

    function subscribe($plan, $token, $email, $description, $couponCode, $setupFee, $metadata = null);

    function create_plan($id, $name, $amount, $interval, $trialDays, $intervalCount);

    function get_plans();

    function get_recipients();

    function create_recipient($recipient);

    function create_transfer($transfer);

    function get_coupon($code);

    function create_customer($card, $email, $metadata);

    function charge_customer($customerId, $amount, $description, $metadata = null, $stripeEmail = null);
}

//deals with calls to Stripe API
class MM_WPFS_Stripe implements MM_WPFS_Payment_API
{
    function charge($amount, $card, $description, $metadata = null, $stripeEmail = null)
    {
        $options = get_option('fullstripe_options');

        $charge = array(
            'card' => $card,
            'amount' => $amount,
            'currency' => $options['currency'],
            'description' => $description,
            'receipt_email' => $stripeEmail
        );

        if ($metadata)
            $charge['metadata'] = $metadata;

        $result = Stripe_Charge::create($charge);

        return $result;
    }

    function subscribe($plan, $token, $email, $description, $couponCode, $setupFee, $metadata = null)
    {
        $data = array(
            "card" => $token,
            "plan" => $plan,
            "email" => $email,
            "description" => $description
        );

        if ($couponCode != '')
            $data["coupon"] = $couponCode;

        if ($metadata)
            $data['metadata'] = $metadata;

        if ($setupFee != 0)
            $data['account_balance'] = $setupFee;

        $customer = Stripe_Customer::create($data);

        return $customer;
    }


    function create_plan($id, $name, $amount, $interval, $trialDays, $intervalCount)
    {
        $options = get_option('fullstripe_options');

        try
        {
            $planData = array(
                "amount" => $amount,
                "interval" => $interval,
                "name" => $name,
                "currency" => $options['currency'],
                "interval_count" => $intervalCount,
                "id" => $id);

            if ($trialDays != 0)
            {
                $planData['trial_period_days'] = $trialDays;
            }

            do_action('fullstripe_before_create_plan', $planData);
            Stripe_Plan::create($planData);
            do_action('fullstripe_after_create_plan');

            $return = array('success' => true, 'msg' => __('Subscription plan created ', 'wp-full-stripe'));
        }
        catch (Exception $e)
        {
            //show notification of error
            $return = array('success' => false, 'msg' => __('There was an error creating the plan: ', 'wp-full-stripe') . $e->getMessage());
        }

        return $return;
    }

    function get_plans()
    {
        try
        {
            $plans = Stripe_Plan::all(array('count' => 100));
        }
        catch (Exception $e)
        {
            $plans = array();
        }
        return $plans;
    }

    function get_recipients()
    {
        try
        {
            $recipients = Stripe_Recipient::all();
        }
        catch (Exception $e)
        {
            $recipients = array();
        }

        return $recipients;
    }

    function create_recipient($recipient)
    {
        return Stripe_Recipient::create($recipient);
    }

    function create_transfer($transfer)
    {
        return Stripe_Transfer::create($transfer);
    }

    function get_coupon($code)
    {
        return Stripe_Coupon::retrieve($code);
    }

    function create_customer($card, $email, $metadata)
    {
        $customer = array(
            "card" => $card,
            "email" => $email,
            "metadata" => $metadata
        );

        return Stripe_Customer::create($customer);
    }

    function charge_customer($customerId, $amount, $description, $metadata = null, $stripeEmail = null)
    {
        $options = get_option('fullstripe_options');

        $charge = array(
            'customer' => $customerId,
            'amount' => $amount,
            'currency' => $options['currency'],
            'description' => $description,
            'receipt_email' => $stripeEmail
        );

        if ($metadata)
            $charge['metadata'] = $metadata;

        $result = Stripe_Charge::create($charge);

        return $result;
    }
}