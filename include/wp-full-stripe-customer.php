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

    private static function is_valid_dni_nie($string) {
      if (strlen($string) != 9 ||
          preg_match('/^[XYZ]?([0-9]{7,8})([A-Z])$/i', $string, $matches) !== 1) {
          return false;
      }
      $map = 'TRWAGMYFPDXBNJZSQVHLCKE';
      list(, $number, $letter) = $matches;
      return strtoupper($letter) === $map[((int) $number) % 23];
    }

    private static function is_valid_ccc($ccc) {
      //$ccc valido seria el 2077 0338 79 3100254321
      $valido = true;

      $suma = 0;
      $suma += $ccc[0] * 4;
      $suma += $ccc[1] * 8;
      $suma += $ccc[2] * 5;
      $suma += $ccc[3] * 10;
      $suma += $ccc[4] * 9;
      $suma += $ccc[5] * 7;
      $suma += $ccc[6] * 3;
      $suma += $ccc[7] * 6;

      $division = floor($suma/11);
      $resto    = $suma - ($division  * 11);
      $primer_digito_control = 11 - $resto;
      if($primer_digito_control == 11)
          $primer_digito_control = 0;

      if($primer_digito_control == 10)
          $primer_digito_control = 1;

      if($primer_digito_control != $ccc[8])
          $valido = false;

      $suma = 0;
      $suma += $ccc[10] * 1;
      $suma += $ccc[11] * 2;
      $suma += $ccc[12] * 4;
      $suma += $ccc[13] * 8;
      $suma += $ccc[14] * 5;
      $suma += $ccc[15] * 10;
      $suma += $ccc[16] * 9;
      $suma += $ccc[17] * 7;
      $suma += $ccc[18] * 3;
      $suma += $ccc[19] * 6;

      $division = floor($suma/11);
      $resto = $suma-($division  * 11);
      $segundo_digito_control = 11- $resto;

      if($segundo_digito_control == 11)
          $segundo_digito_control = 0;
      if($segundo_digito_control == 10)
          $segundo_digito_control = 1;

      if($segundo_digito_control != $ccc[9])
          $valido = false;

      return $valido;
    }

    private static function is_valid_bic($bic){
      return (preg_match('/^[a-z]{6}[0-9a-z]{2}([0-9a-z]{3})?\z/i', $bic));
    }

    function fullstripe_payment_charge()
    {
        //get POST data from form
        $card = $_POST['stripeToken'];
        $name = sanitize_text_field($_POST['fullstripe_name']); // card holder's name
        $amount = $_POST['amount'];
        $formName = $_POST['formName'];
        $isCustom = $_POST['isCustom'];
        $doRedirect = $_POST['formDoRedirect'];
        $redirectPostID = $_POST['formRedirectPostID'];
        $sendReceipt = $_POST['sendEmailReceipt'];
        $options = get_option('fullstripe_options');

        $valid = true;
        $error_messages = [];

        /****** Bank account payments changes (No Stripe payment Override) ******/
        $is_payment_credit = false;
        $is_payment_spain_bank = false;
        $is_payment_intl_bank = false;
        $payment_method = $_POST['fullstripe_pay_method'];
        switch ($payment_method) {
          case 'credit':
            $is_payment_credit = true;
            break;
          case 'spanishaccount':
            $is_payment_spain_bank = true;
            $bank_spain_bankid = isset($_POST['bank_spain_bankid']) ? sanitize_text_field($_POST['bank_spain_bankid']) : '';
            if ($bank_spain_bankid == ''){
              $error_messages[] = array(
                'text' => __('Please enter the bank number', 'wp-full-stripe'),
                'input' => 'bank_spain_bankid',
              );
            }
            $bank_spain_office = isset($_POST['bank_spain_office']) ? sanitize_text_field($_POST['bank_spain_office']) : '';
            if ($bank_spain_office == ''){
              $error_messages[] = array(
                'text' => __('Please enter the office number', 'wp-full-stripe'),
                'input' => 'bank_spain_office',
              );
            }
            $bank_spain_dc = isset($_POST['bank_spain_dc']) ? sanitize_text_field($_POST['bank_spain_dc']) : '';
            if ($bank_spain_dc == ''){
              $error_messages[] = array(
                'text' => __('Please enter the DC', 'wp-full-stripe'),
                'input' => 'bank_spain_dc',
              );
            }
            $bank_spain_account = isset($_POST['bank_spain_account']) ? sanitize_text_field($_POST['bank_spain_account']) : '';
            if ($bank_spain_account == ''){
              $error_messages[] = array(
                'text' => __('Please enter the account number', 'wp-full-stripe'),
                'input' => 'bank_spain_account',
              );
            }
            if ($bank_spain_bankid != '' && $bank_spain_office != '' && $bank_spain_dc != '' && $bank_spain_account != ''){
              $bank_spain_ccc = $bank_spain_bankid . $bank_spain_office . $bank_spain_dc . $bank_spain_account;
              if (!MM_WPFS_Customer::is_valid_ccc($bank_spain_ccc)){
                $error_messages[] = array(
                  'text' => __('The CCC is invalid', 'wp-full-stripe'),
                  'input' => '',
                );
              }
            }
            break;
          case 'internationalaccount':
            $is_payment_intl_bank = true;
            $bank_intl_iban = isset($_POST['bank_intl_iban']) ? sanitize_text_field($_POST['bank_intl_iban']) : '';
            if ($bank_intl_iban == ''){
              $error_messages[] = array(
                'text' => __('Please enter the IBAN', 'wp-full-stripe'),
                'input' => 'bank_intl_iban',
              );
            }else{
              if(!verify_iban($bank_intl_iban)) {
                $error_messages[] = array(
                  'text' => __('The IBAN is invalid', 'wp-full-stripe'),
                  'input' => 'bank_intl_iban',
                );
              }
            }
            $bank_intl_bic = isset($_POST['bank_intl_bic']) ? sanitize_text_field($_POST['bank_intl_bic']) : '';
            if ($bank_intl_bic == ''){
              $error_messages[] = array(
                'text' => __('Please enter the BIC', 'wp-full-stripe'),
                'input' => 'bank_intl_bic',
              );
            }else{
              if(!MM_WPFS_Customer::is_valid_bic($bank_intl_bic)) {
                $error_messages[] = array(
                  'text' => __('The BIC is invalid', 'wp-full-stripe'),
                  'input' => 'bank_intl_bic',
                );
              }
            }
            break;
          default:
            $error_messages[] = array(
              'text' => __('The payment method is invalid', 'wp-full-stripe'),
              'input' => 'fullstripe_pay_method',
            );
            break;
        };
        /*****************************/

        if ($isCustom == 1)
        {
            $amount = $_POST['fullstripe_custom_amount'];
            if (!is_numeric($amount))
            {
                $error_messages[] = array(
                  'text' => __('The payment amount is invalid, please only use numbers and a decimal point', 'wp-full-stripe'),
                  'input' => 'fullstripe_custom_amount',
                );
            }
            else
            {
                $amount = $amount * 100; //Stripe expects amounts in cents/pence
            }
        }
        
        $firstname = isset($_POST['fullstripe_firstname']) ? sanitize_text_field($_POST['fullstripe_firstname']) : '';
        if ($firstname == ''){
            $error_messages[] = array(
              'text' => __('Please enter first name', 'wp-full-stripe'),
              'input' => 'fullstripe_firstname',
            );
        }
        $lastname = isset($_POST['fullstripe_lastname']) ? sanitize_text_field($_POST['fullstripe_lastname']) : '';
        if ($lastname == ''){
            $error_messages[] = array(
              'text' => __('Please enter last name', 'wp-full-stripe'),
              'input' => 'fullstripe_lastname',
            );
        }

        $telephone = isset($_POST['fullstripe_telephone']) ? sanitize_text_field($_POST['fullstripe_telephone']) : '';

        $doctype = $_POST['fullstripe_doctype'];
        if ($doctype != 'dni' && $doctype != 'passport'){
            $error_messages[] = array(
              'text' => __('Please choose a Type of document', 'wp-full-stripe'),
              'input' => 'fullstripe_doctype',
            );
        }else{
            if ($doctype == 'dni'){
              $dni = isset($_POST['fullstripe_doc_dni']) ? sanitize_text_field($_POST['fullstripe_doc_dni']) : '';
              if (!MM_WPFS_Customer::is_valid_dni_nie($dni)){
                $error_messages[] = array(
                  'text' => __('Please enter a valid document ID', 'wp-full-stripe'),
                  'input' => 'fullstripe_doc_dni',
                );
              }
            }else if ($doctype == 'passport'){
              $passport = isset($_POST['fullstripe_doc_passport']) ? sanitize_text_field($_POST['fullstripe_doc_passport']) : '';
              if ($passport == ''){
                  $error_messages[] = array(
                    'text' => __('Please enter a Passport ID', 'wp-full-stripe'),
                    'input' => 'fullstripe_doc_passport',
                  );
              }
            }
        }

        $birthdate_array = $_POST['fullstripe_birthdate'];
        $valid_date = false;
        $adult = false;
        $birthdate = null;
        if ($birthdate_array[0] != '' && $birthdate_array[1] != '' && $birthdate_array[2] != ''){
          $valid_date = checkdate($birthdate_array[1],$birthdate_array[0],$birthdate_array[2]);
          if ($valid_date){
            $birthdate = new DateTime();
            $birthdate->setDate($birthdate_array[2], $birthdate_array[1], $birthdate_array[0]);
            $today = new DateTime();
            $interval = $birthdate->diff($today);
            $adult = ($interval->y >= 18);
          }
        }
        if (!$valid_date){
          $error_messages[] = array(
            'text' => __('Please choose a valid date of birth', 'wp-full-stripe'),
            'input' => 'fullstripe_birthdate',
          );
        }else{
          if (!$adult){
            $error_messages[] = array(
              'text' => __('You must be 18 years old or more', 'wp-full-stripe'),
              'input' => 'fullstripe_birthdate',
            );
          }
        }

        $country = isset($_POST['fullstripe_address_country']) ? sanitize_text_field($_POST['fullstripe_address_country']) : '';
        $address1 = isset($_POST['fullstripe_address_line1']) ? sanitize_text_field($_POST['fullstripe_address_line1']) : '';
        $city = isset($_POST['fullstripe_address_city']) ? sanitize_text_field($_POST['fullstripe_address_city']) : '';
        $state = isset($_POST['fullstripe_address_state']) ? sanitize_text_field($_POST['fullstripe_address_state']) : '';
        $zip = isset($_POST['fullstripe_address_zip']) ? sanitize_text_field($_POST['fullstripe_address_zip']) : '';
        if ($country == ''){
          $error_messages[] = array(
            'text' => __('Please enter a Country', 'wp-full-stripe'),
            'input' => 'fullstripe_address_country',
          );
        }
        if ($address1 == ''){
          $error_messages[] = array(
            'text' => __('Please enter a Billing Address', 'wp-full-stripe'),
            'input' => 'fullstripe_address_line1',
          );
        }
        if ($city == ''){
          $error_messages[] = array(
            'text' => __('Please enter a City', 'wp-full-stripe'),
            'input' => 'fullstripe_address_city',
          );
        }
        if ($zip == ''){
          $error_messages[] = array(
            'text' => __('Please enter a Zip/Postcode', 'wp-full-stripe'),
            'input' => 'fullstripe_address_zip',
          );
        }

        $email = 'n/a';
        if (isset($_POST['fullstripe_email']))
        {
           $email = $_POST['fullstripe_email'];
           if (!filter_var($email, FILTER_VALIDATE_EMAIL))
           {
                $error_messages[] = array(
                  'text' => __('Please enter a valid email address', 'wp-full-stripe'),
                  'input' => 'fullstripe_email',
                );
           }else{
                $email2 = $_POST['fullstripe_email2'];
                if ($email != $email2){
                  $error_messages[] = array(
                    'text' => __('The email and email confirmation are not the same', 'wp-full-stripe'),
                    'input' => 'fullstripe_email2',
                  );
                }
           }
        }

        if (count($error_messages)){
          $valid = false;
          $return = array('success' => false, 'error_messages' => $error_messages);
        }

        if ($valid)
        {
            if ($is_payment_credit){
              try
              {
                  $description = "Payment from $name on form: $formName";
                  $metadata = array(
                      'customer_name' => $name,
                      'customer_email' => $email,
                      'billing_address_line1' => $address1,
                      'billing_address_city' => $city,
                      'billing_address_state' => $state,
                      'billing_address_zip' => $zip
                  );

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
                  $result = $this->stripe->charge_customer($stripeCustomer->id, $amount, $description, $metadata,($sendPluginEmail==false ? $email : null));
                  do_action('fullstripe_after_payment_charge', $result);

                  //save the payment
                  $address = array('country' => $country, 'line1' => $address1, 'city' => $city, 'state' => $state, 'zip' => $zip);
                  $otherData = array(
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'telephone' => $telephone,
                    'documentType' => $doctype,
                    'documentID' => ($doctype == 'dni')? $dni : $passport,
                    'birthDate' => $birthdate->getTimestamp(),
                  );
                  $this->db->fullstripe_insert_payment($result, $address, $stripeCustomer->id, $otherData);

                  $return = array('success' => true, 'msg' => __("Payment Successful!", "wp-full-stripe"));
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
            }else{
              /* No Stripe payment Override */
              // encrypt all the bank data
              $rsa = new Crypt_RSA();
              $public_key = file_get_contents(PUBLIC_KEY_PATH);
              if ($public_key !== false){
                $rsa->loadKey(); // public key
                $enc_bank_spain_ccc = BANK_STRING_NOT_FILLED;
                $enc_bank_intl_iban = BANK_STRING_NOT_FILLED;
                $enc_bank_intl_bic = BANK_STRING_NOT_FILLED;
                if ($is_payment_spain_bank){
                  $enc_bank_spain_ccc = $rsa->encrypt($bank_spain_ccc);
                }else if ($is_payment_intl_bank){
                  $enc_bank_intl_iban = $rsa->encrypt($bank_intl_iban);
                  $enc_bank_intl_bic = $rsa->encrypt($bank_intl_bic);
                }

                //save the payment
                $address = array('country' => $country, 'line1' => $address1, 'city' => $city, 'state' => $state, 'zip' => $zip);
                $otherData = array(
                  'firstname' => $firstname,
                  'lastname' => $lastname,
                  'telephone' => $telephone,
                  'documentType' => $doctype,
                  'documentID' => ($doctype == 'dni')? $dni : $passport,
                  'birthDate' => $birthdate->getTimestamp(),
                  'bankCCC' => $enc_bank_spain_ccc,
                  'bankIBAN' => $enc_bank_intl_iban,
                  'bankBIC' => $enc_bank_intl_bic,
                );
                $phoney_payment = new stdClass();
                $phoney_payment->id = BANK_STRING_VALUE;
                $phoney_payment->description = BANK_STRING_VALUE;
                $phoney_payment->paid = BANK_STRING_VALUE;
                $phoney_payment->livemode = BANK_STRING_VALUE;
                $phoney_payment->amount = $amount;
                $phoney_payment->fee = BANK_STRING_VALUE;
                $phoney_payment->created = mktime();
                $this->db->fullstripe_insert_payment($phoney_payment, $address, BANK_STRING_VALUE, $otherData);
                $return = array('success' => true, 'msg' => __("Payment Successful!", "wp-full-stripe"));

                //$return = array('success' => false, 'msg' => $enc_bank_intl_iban . ' || ' . $enc_bank_intl_bic); // for testing
              }else{
                $return = array('success' => false, 'msg' => __('There was an error processing your payment', 'wp-full-stripe'));
              }
              
            }
            
        }

        //correct way to return JS results in wordpress
        header("Content-Type: application/json");
        echo json_encode(apply_filters('fullstripe_payment_charge_return_message', $return));
        exit;
    }

    function fullstripe_subscription_charge()
    {
        //get POST data from form
        $card = $_POST['stripeToken'];
        $name = $_POST['fullstripe_name'];
        $plan = isset($_POST['fullstripe_plan']) ? $_POST['fullstripe_plan'] : '';
        $couponCode = isset($_POST['fullstripe_coupon_input']) ? $_POST['fullstripe_coupon_input'] : '';
        $doRedirect = $_POST['formDoRedirect'];
        $redirectPostID = $_POST['formRedirectPostID'];

        $valid = true;
        $error_messages = [];

        /****** Bank account payments changes (No Stripe payment Override) ******/
        $is_payment_credit = false;
        $is_payment_spain_bank = false;
        $is_payment_intl_bank = false;
        $payment_method = $_POST['fullstripe_pay_method'];
        switch ($payment_method) {
          case 'credit':
            $is_payment_credit = true;
            break;
          case 'spanishaccount':
            $is_payment_spain_bank = true;
            $bank_spain_bankid = isset($_POST['bank_spain_bankid']) ? sanitize_text_field($_POST['bank_spain_bankid']) : '';
            if ($bank_spain_bankid == ''){
              $error_messages[] = array(
                'text' => __('Please enter the bank number', 'wp-full-stripe'),
                'input' => 'bank_spain_bankid',
              );
            }
            $bank_spain_office = isset($_POST['bank_spain_office']) ? sanitize_text_field($_POST['bank_spain_office']) : '';
            if ($bank_spain_office == ''){
              $error_messages[] = array(
                'text' => __('Please enter the office number', 'wp-full-stripe'),
                'input' => 'bank_spain_office',
              );
            }
            $bank_spain_dc = isset($_POST['bank_spain_dc']) ? sanitize_text_field($_POST['bank_spain_dc']) : '';
            if ($bank_spain_dc == ''){
              $error_messages[] = array(
                'text' => __('Please enter the DC', 'wp-full-stripe'),
                'input' => 'bank_spain_dc',
              );
            }
            $bank_spain_account = isset($_POST['bank_spain_account']) ? sanitize_text_field($_POST['bank_spain_account']) : '';
            if ($bank_spain_account == ''){
              $error_messages[] = array(
                'text' => __('Please enter the account number', 'wp-full-stripe'),
                'input' => 'bank_spain_account',
              );
            }
            if ($bank_spain_bankid != '' && $bank_spain_office != '' && $bank_spain_dc != '' && $bank_spain_account != ''){
              $bank_spain_ccc = $bank_spain_bankid . $bank_spain_office . $bank_spain_dc . $bank_spain_account;
              if (!MM_WPFS_Customer::is_valid_ccc($bank_spain_ccc)){
                $error_messages[] = array(
                  'text' => __('The CCC is invalid', 'wp-full-stripe'),
                  'input' => '',
                );
              }
            }
            break;
          case 'internationalaccount':
            $is_payment_intl_bank = true;
            $bank_intl_iban = isset($_POST['bank_intl_iban']) ? sanitize_text_field($_POST['bank_intl_iban']) : '';
            if ($bank_intl_iban == ''){
              $error_messages[] = array(
                'text' => __('Please enter the IBAN', 'wp-full-stripe'),
                'input' => 'bank_intl_iban',
              );
            }else{
              if(!verify_iban($bank_intl_iban)) {
                $error_messages[] = array(
                  'text' => __('The IBAN is invalid', 'wp-full-stripe'),
                  'input' => 'bank_intl_iban',
                );
              }
            }
            $bank_intl_bic = isset($_POST['bank_intl_bic']) ? sanitize_text_field($_POST['bank_intl_bic']) : '';
            if ($bank_intl_bic == ''){
              $error_messages[] = array(
                'text' => __('Please enter the BIC', 'wp-full-stripe'),
                'input' => 'bank_intl_bic',
              );
            }else{
              if(!MM_WPFS_Customer::is_valid_bic($bank_intl_bic)) {
                $error_messages[] = array(
                  'text' => __('The BIC is invalid', 'wp-full-stripe'),
                  'input' => 'bank_intl_bic',
                );
              }
            }
            break;
          default:
            $error_messages[] = array(
              'text' => __('The payment method is invalid', 'wp-full-stripe'),
              'input' => 'fullstripe_pay_method',
            );
            break;
        };
        /*****************************/

        if ($plan == ''){
            $error_messages[] = array(
              'text' => __('Please choose the desired donation per month', 'wp-full-stripe'),
              'input' => 'fullstripe_plan',
            );
        }

        $firstname = isset($_POST['fullstripe_firstname']) ? sanitize_text_field($_POST['fullstripe_firstname']) : '';
        if ($firstname == ''){
            $error_messages[] = array(
              'text' => __('Please enter the first name', 'wp-full-stripe'),
              'input' => 'fullstripe_firstname',
            );
        }
        $lastname = isset($_POST['fullstripe_lastname']) ? sanitize_text_field($_POST['fullstripe_lastname']) : '';
        if ($lastname == ''){
            $error_messages[] = array(
              'text' => __('Please enter the last name', 'wp-full-stripe'),
              'input' => 'fullstripe_lastname',
            );
        }

        $telephone = isset($_POST['fullstripe_telephone']) ? sanitize_text_field($_POST['fullstripe_telephone']) : '';

        $doctype = $_POST['fullstripe_doctype'];
        if ($doctype != 'dni' && $doctype != 'passport'){
            $error_messages[] = array(
              'text' => __('Please choose a Type of document', 'wp-full-stripe'),
              'input' => 'fullstripe_doctype',
            );
        }else{
            if ($doctype == 'dni'){
              $dni = isset($_POST['fullstripe_doc_dni']) ? sanitize_text_field($_POST['fullstripe_doc_dni']) : '';
              if (!MM_WPFS_Customer::is_valid_dni_nie($dni)){
                $error_messages[] = array(
                  'text' => __('Please enter a valid document ID', 'wp-full-stripe'),
                  'input' => 'fullstripe_doc_dni',
                );
              }
            }else if ($doctype == 'passport'){
              $passport = isset($_POST['fullstripe_doc_passport']) ? sanitize_text_field($_POST['fullstripe_doc_passport']) : '';
              if ($passport == ''){
                $error_messages[] = array(
                  'text' => __('Please enter a Passport ID', 'wp-full-stripe'),
                  'input' => 'fullstripe_doc_passport',
                );
              }
            }
        }

        $birthdate_array = $_POST['fullstripe_birthdate'];
        $valid_date = false;
        $adult = false;
        $birthdate = null;
        if ($birthdate_array[0] != '' && $birthdate_array[1] != '' && $birthdate_array[2] != ''){
          $valid_date = checkdate($birthdate_array[1],$birthdate_array[0],$birthdate_array[2]);
          if ($valid_date){
            $birthdate = new DateTime();
            $birthdate->setDate($birthdate_array[2], $birthdate_array[1], $birthdate_array[0]);
            $today = new DateTime();
            $interval = $birthdate->diff($today);
            $adult = ($interval->y >= 18);
          }
        }
        if (!$valid_date){
          $error_messages[] = array(
            'text' => __('Please choose a valid date of birth', 'wp-full-stripe'),
            'input' => 'fullstripe_birthdate',
          );
        }else{
          if (!$adult){
            $error_messages[] = array(
              'text' => __('You must be 18 years old or more', 'wp-full-stripe'),
              'input' => 'fullstripe_birthdate',
            );
          }
        }

        $country = isset($_POST['fullstripe_address_country']) ? sanitize_text_field($_POST['fullstripe_address_country']) : '';
        $address1 = isset($_POST['fullstripe_address_line1']) ? sanitize_text_field($_POST['fullstripe_address_line1']) : '';
        $city = isset($_POST['fullstripe_address_city']) ? sanitize_text_field($_POST['fullstripe_address_city']) : '';
        $state = isset($_POST['fullstripe_address_state']) ? sanitize_text_field($_POST['fullstripe_address_state']) : '';
        $zip = isset($_POST['fullstripe_address_zip']) ? sanitize_text_field($_POST['fullstripe_address_zip']) : '';
        $setupFee = $_POST['fullstripe_setupFee'];
        if ($country == ''){
          $error_messages[] = array(
            'text' => __('Please enter a Country', 'wp-full-stripe'),
            'input' => 'fullstripe_address_country',
          );
        }
        if ($address1 == ''){
          $error_messages[] = array(
            'text' => __('Please enter a Billing Address', 'wp-full-stripe'),
            'input' => 'fullstripe_address_line1',
          );
        }
        if ($city == ''){
          $error_messages[] = array(
            'text' => __('Please enter a City', 'wp-full-stripe'),
            'input' => 'fullstripe_address_city',
          );
        }
        if ($zip == ''){
          $error_messages[] = array(
            'text' => __('Please enter a Zip/Postcode', 'wp-full-stripe'),
            'input' => 'fullstripe_address_zip',
          );
        }

        $email = '';
        if (isset($_POST['fullstripe_email']))
        {
            $email = $_POST['fullstripe_email'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $error_messages[] = array(
                  'text' => __('Please enter a valid email address', 'wp-full-stripe'),
                  'input' => 'fullstripe_email',
                );
            }else{
                $email2 = $_POST['fullstripe_email2'];
                if ($email != $email2){
                  $error_messages[] = array(
                    'text' => __('The email and email confirmation are not the same', 'wp-full-stripe'),
                    'input' => 'fullstripe_email2',
                  );
                }
           }
        }
        else
        {
            $error_messages[] = array(
              'text' => __('Please enter a valid email address', 'wp-full-stripe'),
              'input' => 'fullstripe_email',
            );
        }

        if (count($error_messages)){
          $valid = false;
          $return = array('success' => false, 'error_messages' => $error_messages);
        }

        if ($valid)
        {
            if ($is_payment_credit){
              try
              {
                  $description =  "Subscriber: " . $name;
                  $metadata = array(
                      'customer_name' => $name,
                      'customer_email' => $email,
                      'billing_address_line1' => $address1,
                      'billing_address_city' => $city,
                      'billing_address_state' => $state,
                      'billing_address_zip' => $zip,
                  );

                  do_action('fullstripe_before_subscription_charge', $plan);
                  $customer = $this->stripe->subscribe($plan, $card, $email, $description, $couponCode, $setupFee, $metadata);
                  do_action('fullstripe_after_subscription_charge', $customer);
                  // TODO: $customer is null!

                  // save the subscriber
                  $address = array('country' => $country, 'line1' => $address1, 'city' => $city, 'state' => $state, 'zip' => $zip);
                  $otherData = array(
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'telephone' => $telephone,
                    'documentType' => $doctype,
                    'documentID' => ($doctype == 'dni')? $dni : $passport,
                    'birthDate' => $birthdate->getTimestamp(),
                  );

                  $this->db->fullstripe_insert_subscriber($customer, $name, $address, $otherData);

                  $return = array('success' => true, 'msg' => __("Payment Successful. Thanks for subscribing!", "wp-full-stripe"));
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
            }else{
              /* No Stripe payment Override */
              // encrypt all the bank data
              $rsa = new Crypt_RSA();
              $public_key = file_get_contents(PUBLIC_KEY_PATH);
              if ($public_key !== false){
                $enc_bank_spain_ccc = BANK_STRING_NOT_FILLED;
                $enc_bank_intl_iban = BANK_STRING_NOT_FILLED;
                $enc_bank_intl_bic = BANK_STRING_NOT_FILLED;
                if ($is_payment_spain_bank){
                  $enc_bank_spain_ccc = $rsa->encrypt($bank_spain_ccc);
                }else if ($is_payment_intl_bank){
                  $enc_bank_intl_iban = $rsa->encrypt($bank_intl_iban);
                  $enc_bank_intl_bic = $rsa->encrypt($bank_intl_bic);
                }

                //save the payment
                $address = array('country' => $country, 'line1' => $address1, 'city' => $city, 'state' => $state, 'zip' => $zip);
                $otherData = array(
                  'firstname' => $firstname,
                  'lastname' => $lastname,
                  'telephone' => $telephone,
                  'documentType' => $doctype,
                  'documentID' => ($doctype == 'dni')? $dni : $passport,
                  'birthDate' => $birthdate->getTimestamp(),
                  'bankCCC' => $enc_bank_spain_ccc,
                  'bankIBAN' => $enc_bank_intl_iban,
                  'bankBIC' => $enc_bank_intl_bic,
                );
                $phoney_payment = new stdClass();
                $phoney_payment->id = BANK_STRING_VALUE;
                $phoney_payment->email = BANK_STRING_VALUE;
                $customer->subscription->plan->id = BANK_STRING_VALUE;
                $phoney_payment->created = mktime();
                $this->db->fullstripe_insert_subscriber($phoney_payment, $name, $address, $otherData);

                $return = array('success' => true, 'msg' => __("Payment Successful. Thanks for subscribing!", "wp-full-stripe"));
              }else{
                $return = array('success' => false, 'msg' => __('There was an error processing your payment', 'wp-full-stripe'));
              }
            }
        }

        //correct way to return JS results in wordpress
        header("Content-Type: application/json");
        echo json_encode(apply_filters('fullstripe_subscription_charge_return_message', $return));
        exit;
    }

    /*
    * Unchanged, not used
    */
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
            $address = array('line1' => '', 'line2' => '', 'city' => '', 'state' => '', 'zip' => '');
            $this->db->fullstripe_insert_payment($result, $address, $stripeCustomer->id);

            $return = array('success' => true, 'msg' => __("Payment Successful!", "wp-full-stripe"));
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