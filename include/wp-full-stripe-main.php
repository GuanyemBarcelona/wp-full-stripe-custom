<?php
/*
WP Full Stripe
http://paymentsplugin.com
Complete Stripe payments integration for Wordpress
Mammothology
 2.9.6
http://mammothology.com
*/

class MM_WPFS
{
    public static $instance;
    private $customer = null;
    private $admin = null;
    private $database = null;
    private $stripe = null;

    const VERSION = '2.9.6.4';

    public static function getInstance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new MM_WPFS();
        }
        return self::$instance;
    }

    public static function setup_db()
    {
        MM_WPFS_Database::fullstripe_setup_db();
    }

    public function __construct()
    {
        $this->includes();
        $this->setup();
        $this->hooks();
    }

    function includes()
    {
        include 'wp-full-stripe-database.php';
        include 'wp-full-stripe-customer.php';
        include 'wp-full-stripe-payments.php';
        include 'wp-full-stripe-admin.php';
        include 'wp-full-stripe-admin-menu.php';

        do_action('fullstripe_includes_action');
    }

    function hooks()
    {
        //TODO: find out why enabling this filter breaks wp_list_table on the plugins page!!
        //add_filter('plugin_action_links', array($this, 'fullstripe_plugin_action_links', 10, 2));
        add_shortcode('fullstripe_payment', array($this, 'fullstripe_payment_form'));
        add_shortcode('fullstripe_subscription', array($this, 'fullstripe_subscription_form'));
        add_shortcode('fullstripe_checkout', array($this, 'fullstripe_checkout_form'));
        add_action('wp_head', array($this, 'fullstripe_wp_head'));

        do_action('fullstripe_main_hooks_action');
    }

    function setup()
    {
        //set option defaults
        $options = get_option('fullstripe_options');
        if ($options == '' || $options['fullstripe_version'] != self::VERSION)
        {
            $this->set_option_defaults($options);
        }

        //set API key
        if ($options['apiMode'] === 'test')
        {
            $this->fullstripe_set_api_key($options['secretKey_test']);
        }
        else
        {
            $this->fullstripe_set_api_key($options['secretKey_live']);
        }

        //setup subclasses to handle everything
        $this->database = new MM_WPFS_Database();
        $this->customer = new MM_WPFS_Customer();
        $this->admin = new MM_WPFS_Admin();
        $this->stripe = new MM_WPFS_Stripe();

        do_action('fullstripe_setup_action');

    }

    function fullstripe_plugin_action_links($links, $file)
    {
        if ($file == WP_FULL_STRIPE_BASENAME)
        {
            $settings_link = '<a href="' . menu_page_url('fullstripe-settings', false) . '">' . esc_html(__('Settings', 'fullstripe-settings')) . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;

    }

    function set_option_defaults($options)
    {
        if ($options == '')
        {
            $arr = array(
                'secretKey_test' => 'YOUR_TEST_SECRET_KEY',
                'publishKey_test' => 'YOUR_TEST_PUBLISHABLE_KEY',
                'secretKey_live' => 'YOUR_LIVE_SECRET_KEY',
                'publishKey_live' => 'YOUR_LIVE_PUBLISHABLE_KEY',
                'apiMode' => 'test',
                'currency' => 'usd',
                'form_css' => ".fullstripe-form-title{ font-size: 120%;  color: #363636; font-weight: bold;}\n.fullstripe-form-input{}\n.fullstripe-form-label{font-weight: bold;}",
                'includeStyles' => '1',
                'receiptEmailType' => 'plugin',
                'email_receipt_subject' => 'Payment Receipt',
                'email_receipt_html' => "<html><body><p>Hi,</p><p>Here's your receipt for your payment of %AMOUNT%</p><p>Thanks</p><br/>%NAME%</body></html>",
                'admin_payment_receipt' => '0',
                'fullstripe_version' => self::VERSION
            );

            update_option('fullstripe_options', $arr);
        }
        else //different version
        {
            $options['fullstripe_version'] = self::VERSION;
            if (!array_key_exists('secretKey_test', $options)) $options['secretKey_test'] = 'YOUR_TEST_SECRET_KEY';
            if (!array_key_exists('publishKey_test', $options)) $options['publishKey_test'] = 'YOUR_TEST_PUBLISHABLE_KEY';
            if (!array_key_exists('secretKey_live', $options)) $options['secretKey_live'] = 'YOUR_LIVE_SECRET_KEY';
            if (!array_key_exists('publishKey_live', $options)) $options['publishKey_live'] = 'YOUR_LIVE_PUBLISHABLE_KEY';
            if (!array_key_exists('apiMode', $options)) $options['apiMode'] = 'test';
            if (!array_key_exists('currency', $options)) $options['currency'] = 'usd';
            if (!array_key_exists('form_css', $options)) $options['form_css'] = ".fullstripe-form-title{ font-size: 120%;  color: #363636; font-weight: bold;}\n.fullstripe-form-input{}\n.fullstripe-form-label{font-weight: bold;}";
            if (!array_key_exists('includeStyles', $options)) $options['includeStyles'] = '1';
            if (!array_key_exists('receiptEmailType', $options)) $options['receiptEmailType'] = 'plugin';
            if (!array_key_exists('email_receipt_subject', $options)) $options['email_receipt_subject'] = 'Payment Receipt';
            if (!array_key_exists('email_receipt_html', $options)) $options['email_receipt_html'] = "<html><body><p>Hi,</p><p>Here's your receipt for your payment of %AMOUNT%</p><p>Thanks</p><br/>%NAME%</body></html>";
            if (!array_key_exists('admin_payment_receipt', $options)) $options['admin_payment_receipt'] = '0';

            update_option('fullstripe_options', $options);

            //also, if version changed then the DB might be out of date
            MM_WPFS_Database::fullstripe_setup_db();
        }

    }

    function fullstripe_set_api_key($key)
    {
        if ($key != '' && $key != 'YOUR_TEST_SECRET_KEY' && $key != 'YOUR_LIVE_SECRET_KEY')
        {
            try
            {
                Stripe::setApiKey($key);
            }
            catch (Exception $e)
            {
                //invalid key was set, ignore it
            }
        }
    }

    function fullstripe_payment_form($atts)
    {
        extract(shortcode_atts(array(
            'form' => 'default',
        ), $atts));

        //load scripts and styles
        $this->fullstripe_load_css();
        $this->fullstripe_load_js();
        //load form data into scope
        list($formData, $currencySymbol, $localeState, $localeZip, $creditCardImage) = $this->load_payment_form_data($form);

        //get the form style
        $style = 0;
        if (!$formData) $style = -1;
        else $style = $formData->formStyle;

        ob_start();
        include $this->get_payment_form_by_style($style);
        $content = ob_get_clean();
        return apply_filters('fullstripe_payment_form_output', $content);
    }

    function load_payment_form_data($form)
    {
        list ($currencySymbol,  $localeState,  $localeZip, $creditCardImage) = $this->get_locale_strings();

        $formData = array(
            $this->database->get_payment_form_by_name($form),
            $currencySymbol,
            $localeState,
            $localeZip,
            $creditCardImage
        );

        return $formData;
    }

    function get_payment_form_by_style($styleID)
    {
        switch ($styleID)
        {
            case -1:
                return WP_FULL_STRIPE_DIR . '/pages/forms/invalid_shortcode.php';

            case 0:
                return WP_FULL_STRIPE_DIR . '/pages/fullstripe_payment_form.php';

            case 1:
                return WP_FULL_STRIPE_DIR . '/pages/forms/payment_form_compact.php';

            default:
                return WP_FULL_STRIPE_DIR . '/pages/fullstripe_payment_form.php';
        }
    }

    function fullstripe_subscription_form($atts)
    {
        extract(shortcode_atts(array(
            'form' => 'default',
        ), $atts));

        $this->fullstripe_load_css();
        $this->fullstripe_load_js();

        //load form data into scope
        list($formData, $currencySymbol, $localeState, $localeZip, $creditCardImage) = $this->load_subscription_form_data($form);
        //get the form style & plans
        $style = 0;
        $plans = array();
        if (!$formData)
        {
            $style = -1;
        }
        else
        {
            $style = $formData->formStyle;
            $allPlans = $this->get_plans();
            if (count($allPlans) === 0)
            {
                $style = -2;
            }
            else
            {
                $planIDs = explode(',', $formData->plans);
                foreach ($allPlans["data"] as $ap)
                {
                    if (in_array($ap->id, $planIDs))
                    {
                        $plans[] = $ap;
                    }
                }
            }
        }

        ob_start();
        include $this->get_subscription_form_by_style($style);
        $content = ob_get_clean();
        return apply_filters('fullstripe_subscription_form_output', $content);
    }

    function load_subscription_form_data($form)
    {
        list ($currencySymbol,  $localeState,  $localeZip, $creditCardImage) = $this->get_locale_strings();

        $formData = array(
            $this->database->get_subscription_form_by_name($form),
            $currencySymbol,
            $localeState,
            $localeZip,
            $creditCardImage
        );

        return $formData;
    }

    function get_subscription_form_by_style($styleID)
    {
        switch ($styleID)
        {
            case -2:
                return WP_FULL_STRIPE_DIR . '/pages/forms/invalid_plans.php';

            case -1:
                return WP_FULL_STRIPE_DIR . '/pages/forms/invalid_shortcode.php';

            case 0:
                return WP_FULL_STRIPE_DIR . '/pages/fullstripe_subscription_form.php';

            default:
                return WP_FULL_STRIPE_DIR . '/pages/fullstripe_subscription_form.php';
        }
    }

    function fullstripe_checkout_form($atts)
    {
        extract(shortcode_atts(array(
            'form' => 'default',
        ), $atts));

        $this->fullstripe_load_css();
        $this->fullstripe_load_checkout_js($form);

        ob_start();
        include WP_FULL_STRIPE_DIR . '/pages/fullstripe_checkout_form.php';
        $content = ob_get_clean();
        return apply_filters('fullstripe_checkout_form_output', $content);
    }

    function fullstripe_load_js()
    {
        $options = get_option('fullstripe_options');
        wp_enqueue_script('stripe-js', 'https://js.stripe.com/v2/', array('jquery'));
        wp_enqueue_script('wp-full-stripe-js', plugins_url('js/wp-full-stripe.js', dirname(__FILE__)), array('stripe-js'));
        if ($options['apiMode'] === 'test')
        {
            wp_localize_script('wp-full-stripe-js', 'stripekey', $options['publishKey_test']);
        }
        else
        {
            wp_localize_script('wp-full-stripe-js', 'stripekey', $options['publishKey_live']);
        }

        wp_localize_script('wp-full-stripe-js', 'ajaxurl', admin_url('admin-ajax.php'));

        do_action('fullstripe_load_js_action');
    }

    function fullstripe_load_checkout_js($form)
    {
        $options = get_option('fullstripe_options');
        wp_enqueue_script('checkout-js', 'https://checkout.stripe.com/checkout.js', array('jquery'));
        wp_enqueue_script('stripe-checkout-js', plugins_url('js/wp-full-stripe-checkout.js', dirname(__FILE__)), array('checkout-js'));
        if ($options['apiMode'] === 'test')
        {
            wp_localize_script('stripe-checkout-js', 'stripekey', $options['publishKey_test']);
        }
        else
        {
            wp_localize_script('stripe-checkout-js', 'stripekey', $options['publishKey_live']);
        }

        //load form specific options
        $formData = $this->database->get_checkout_form_by_name($form);
        $formData['currency'] = $options['currency'];
        wp_localize_script('stripe-checkout-js', 'checkout_form_data', $formData);
        wp_localize_script('stripe-checkout-js', 'ajaxurl', admin_url('admin-ajax.php'));

        do_action('fullstripe_load_checkout_js_action');
    }

    function fullstripe_load_css()
    {
        $options = get_option('fullstripe_options');
        if ($options['includeStyles'] === '1')
        {
            wp_enqueue_style('fullstripe-bootstrap-css', plugins_url('/css/newstyle.css', dirname(__FILE__)));
        }

        do_action('fullstripe_load_css_action');
    }

    function fullstripe_wp_head()
    {
        //output the custom css
        $options = get_option('fullstripe_options');
        echo '<style type="text/css" media="screen">' . $options['form_css'] . '</style>';
    }

    function get_locale_strings()
    {
        $options = get_option('fullstripe_options');
        $currencySymbol = '$';
        $localeState = 'State';
        $localeZip = 'Zip';
        $creditCardImage = 'creditcards-us.png';

        if ( $options['currency'] === 'eur' )
        {
            $currencySymbol = '€';
            $localeState = 'Region';
            $localeZip = 'Zip / Postcode';
            $creditCardImage = 'creditcards.png';
        }
        elseif ( $options['currency'] === 'gbp' )
        {
            $currencySymbol = '£';
            $localeState = 'County';
            $localeZip = 'Postcode';
            $creditCardImage = 'creditcards.png';
        }
        elseif ( $options['currency'] === 'cad' )
        {
            $currencySymbol = '$';
            $localeState = 'Province';
            $localeZip = 'Postal Code';
            $creditCardImage = 'creditcards.png';
        }

        return array(
            $currencySymbol,
            $localeState,
            $localeZip,
            $creditCardImage
        );
    }

    public function get_plans()
    {
        return $this->stripe != null ? apply_filters('fullstripe_subscription_plans_filter', $this->stripe->get_plans()) : array();
    }

    public function get_recipients()
    {
        return $this->stripe != null ? apply_filters('fullstripe_transfer_receipients_filter', $this->stripe->get_recipients()) : array();
    }
}

MM_WPFS::getInstance();

?>