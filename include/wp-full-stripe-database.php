<?php

class MM_WPFS_Database
{
    public static function fullstripe_setup_db()
    {
        //require for dbDelta()
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        global $wpdb;

        $table = $wpdb->prefix . 'fullstripe_payments';
        $sql = "CREATE TABLE " . $table . " (
        paymentID INT NOT NULL AUTO_INCREMENT,
        eventID VARCHAR(100) NOT NULL,
        description VARCHAR(255) NOT NULL,
        paid TINYINT(1),
        livemode TINYINT(1),
        amount INT NOT NULL,
        fee INT NOT NULL,
        firstname VARCHAR(50) NOT NULL,
        lastname VARCHAR(50) NOT NULL,
        telephone VARCHAR(20) NOT NULL,
        documentType VARCHAR(20) NOT NULL,
        documentID VARCHAR(20) NOT NULL,
        birthDate DATETIME NOT NULL,
        addressCountry VARCHAR(3) NOT NULL,
        addressLine1 VARCHAR(500) NOT NULL,
        addressCity VARCHAR(500) NOT NULL,
        addressState VARCHAR(255) NOT NULL,
        addressZip VARCHAR(100) NOT NULL,
        created DATETIME NOT NULL,
        stripeCustomerID VARCHAR(100),
        bankCCC VARCHAR(255),
        bankIBAN VARCHAR(255),
        bankBIC VARCHAR(255),
        UNIQUE KEY paymentID (paymentID)
        );";

        //database write/update
        dbDelta($sql);

        $table = $wpdb->prefix . 'fullstripe_subscribers';
        $sql = "CREATE TABLE " . $table . " (
        subscriberID INT NOT NULL AUTO_INCREMENT,
        stripeCustomerID VARCHAR(100) NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        planID VARCHAR(100) NOT NULL,
        firstname VARCHAR(50) NOT NULL,
        lastname VARCHAR(50) NOT NULL,
        telephone VARCHAR(20) NOT NULL,
        documentType VARCHAR(20) NOT NULL,
        documentID VARCHAR(20) NOT NULL,
        birthDate DATETIME NOT NULL,
        addressCountry VARCHAR(3) NOT NULL,
        addressLine1 VARCHAR(500) NOT NULL,
        addressLine2 VARCHAR(500) NOT NULL,
        addressCity VARCHAR(500) NOT NULL,
        addressState VARCHAR(255) NOT NULL,
        addressZip VARCHAR(100) NOT NULL,
        created DATETIME NOT NULL,
        UNIQUE KEY subscriberID (subscriberID)
        );";

        //database write/update
        dbDelta($sql);

        $table = $wpdb->prefix . 'fullstripe_payment_forms';
        $sql = "CREATE TABLE " . $table . " (
        paymentFormID INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        formTitle VARCHAR(100) NOT NULL,
        amount INT NOT NULL,
        customAmount TINYINT(1) DEFAULT '0',
        buttonTitle VARCHAR(100) NOT NULL DEFAULT 'Make Payment',
        showButtonAmount TINYINT(1) DEFAULT '1',
        showCustomInput TINYINT(1) DEFAULT '0',
        customInputTitle VARCHAR(100) NOT NULL DEFAULT 'Extra Information',
        redirectOnSuccess TINYINT(1) DEFAULT '0',
        redirectPostID INT(5) DEFAULT 0,
        sendEmailReceipt TINYINT(1) DEFAULT '0',
        formStyle INT(5) DEFAULT 0,
        UNIQUE KEY paymentFormID (paymentFormID)
        );";

        //database write/update
        dbDelta($sql);

        $table = $wpdb->prefix . 'fullstripe_subscription_forms';
        $sql = "CREATE TABLE " . $table . " (
        subscriptionFormID INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        formTitle VARCHAR(100) NOT NULL,
        plans VARCHAR(255) NOT NULL,
        showCustomInput TINYINT(1) DEFAULT '0',
        customInputTitle VARCHAR(100) NOT NULL DEFAULT 'Extra Information',
        redirectOnSuccess TINYINT(1) DEFAULT '0',
        redirectPostID INT(5) DEFAULT 0,
        formStyle INT(5) DEFAULT 0,
        buttonTitle VARCHAR(100) NOT NULL DEFAULT 'Subscribe',
        setupFee INT NOT NULL DEFAULT '0',
        UNIQUE KEY subscriptionFormID (subscriptionFormID)
        );";

        //database write/update
        dbDelta($sql);

        $table = $wpdb->prefix . 'fullstripe_checkout_forms';
        $sql = "CREATE TABLE " . $table . " (
        checkoutFormID INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        companyName VARCHAR(100) NOT NULL,
        productDesc VARCHAR(100) NOT NULL,
        amount INT NOT NULL,
        openButtonTitle VARCHAR(100) NOT NULL DEFAULT 'Pay With Card',
        buttonTitle VARCHAR(100) NOT NULL DEFAULT 'Pay {{amount}}',
        showBillingAddress TINYINT(1) DEFAULT '0',
        showShippingAddress TINYINT(1) DEFAULT '0',
        showRememberMe TINYINT(1) DEFAULT '0',
        image VARCHAR(500) NOT NULL DEFAULT '/img/checkout.png',
        redirectOnSuccess TINYINT(1) DEFAULT '0',
        redirectPostID INT(5) DEFAULT 0,
        UNIQUE KEY checkoutFormID (checkoutFormID)
        );";

        //database write/update
        dbDelta($sql);

        //default form
        $defaultPaymentForm = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "fullstripe_payment_forms" . " WHERE name='default';");
        if ($defaultPaymentForm === null)
        {
            $data = array(
                'name' => 'default',
                'formTitle' => 'Payment',
                'amount' => 1000 //$10.00
            );
            $formats = array('%s', '%s', '%d');
            $wpdb->insert($wpdb->prefix . 'fullstripe_payment_forms', $data, $formats);
        }

        do_action('fullstripe_setup_db');
    }

    function fullstripe_insert_payment($payment, $address, $customerID = null, $otherData = array())
    {
        global $wpdb;

        $data = array(
            'eventID' => $payment->id,
            'description' => $payment->description,
            'paid' => $payment->paid,
            'livemode' => $payment->livemode,
            'amount' => $payment->amount,
            'fee' => $payment->fee,
            'firstname' => $otherData['firstname'],
            'lastname' => $otherData['lastname'],
            'telephone' => $otherData['telephone'],
            'documentType' => $otherData['documentType'],
            'documentID' => $otherData['documentID'],
            'birthDate' => date('Y-m-d H:i:s', $otherData['birthDate']),
            'addressCountry' => $address['country'],
            'addressLine1' => $address['line1'],
            'addressCity' => $address['city'],
            'addressState' => $address['state'],
            'addressZip' => $address['zip'],
            'created' => date('Y-m-d H:i:s', $payment->created),
            'stripeCustomerID' => $customerID,
            'bankCCC' => $otherData['bankCCC'],
            'bankIBAN' => $otherData['bankIBAN'],
            'bankBIC' => $otherData['bankBIC'],
        );

        $wpdb->insert($wpdb->prefix . 'fullstripe_payments', apply_filters('fullstripe_insert_payment_data', $data));
    }

    function fullstripe_insert_subscriber($customer, $name, $address, $otherData = array())
    {
        $data = array(
            'stripeCustomerID' => $customer->id,
            'name' => $name,
            'email' => $customer->email,
            'planID' => $customer->subscription->plan->id,
            'firstname' => $otherData['firstname'],
            'lastname' => $otherData['lastname'],
            'telephone' => $otherData['telephone'],
            'documentType' => $otherData['documentType'],
            'documentID' => $otherData['documentID'],
            'birthDate' => date('Y-m-d H:i:s', $otherData['birthDate']),
            'addressCountry' => $address['country'],
            'addressLine1' => $address['line1'],
            'addressCity' => $address['city'],
            'addressState' => $address['state'],
            'addressZip' => $address['zip'],
            'created' => date('Y-m-d H:i:s', $customer->created)
        );

        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'fullstripe_subscribers', apply_filters('fullstripe_insert_subscriber_data', $data));
    }

    function insert_subscription_form($form)
    {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'fullstripe_subscription_forms', $form);
    }

    function update_subscription_form($id, $form)
    {
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'fullstripe_subscription_forms', $form, array('subscriptionFormID' => $id));
    }

    function insert_payment_form($form)
    {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'fullstripe_payment_forms', $form);
    }

    function update_payment_form($id, $form)
    {
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'fullstripe_payment_forms', $form, array('paymentFormID' => $id));
    }

    function insert_checkout_form($form)
    {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'fullstripe_checkout_forms', $form);
    }

    function update_checkout_form($id, $form)
    {
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'fullstripe_checkout_forms', $form, array('checkoutFormID' => $id));
    }

    function delete_payment_form($id)
    {
        global $wpdb;
        $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'fullstripe_payment_forms' . " WHERE paymentFormID='" . $id . "';");
    }

    function delete_subscription_form($id)
    {
        global $wpdb;
        $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'fullstripe_subscription_forms' . " WHERE subscriptionFormID='" . $id . "';");
    }

    function delete_checkout_form($id)
    {
        global $wpdb;
        $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'fullstripe_checkout_forms' . " WHERE checkoutFormID='" . $id . "';");
    }

    ////////////////////////////////////////////////////////////////////////////////////////////
    function get_payment_form_by_name($name)
    {
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "fullstripe_payment_forms" . " WHERE name='" . $name . "';");
    }

    function get_subscription_form_by_name($name)
    {
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "fullstripe_subscription_forms" . " WHERE name='" . $name . "';");
    }

    function get_checkout_form_by_name($name)
    {
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "fullstripe_checkout_forms" . " WHERE name='" . $name . "';", ARRAY_A);
    }
}


?>