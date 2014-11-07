<?php
/*
Plugin Name: WP Full Stripe
Plugin URI: http://paymentsplugin.com
Description: Complete Stripe payments integration for Wordpress
Author: Mammothology
Version: 2.9.6
Author URI: http://mammothology.com
*/

//defines
if (!defined('WP_FULL_STRIPE_NAME'))
    define('WP_FULL_STRIPE_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('WP_FULL_STRIPE_BASENAME'))
    define('WP_FULL_STRIPE_BASENAME', plugin_basename(__FILE__));

if (!defined('WP_FULL_STRIPE_DIR'))
    define('WP_FULL_STRIPE_DIR', WP_PLUGIN_DIR . '/' . WP_FULL_STRIPE_NAME);

if (!defined('BANK_STRING_VALUE'))
    define('BANK_STRING_VALUE', 'BANK_ACCOUNT_PAYMENT');

//Stripe PHP library
if (!class_exists('Stripe'))
{
    include_once('stripe-php/lib/Stripe.php');
}

//RSA PHP library
require_once('lib/Math/BigInteger.php');
require_once('lib/Crypt/RSA.php');
//IBAN PHP library
require_once('lib/php-iban/php-iban.php');

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'wp-full-stripe-main.php';
register_activation_hook( __FILE__, array( 'MM_WPFS', 'setup_db' ) );
