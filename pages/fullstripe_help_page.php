<div class="wrap">
    <h2><?php echo __('Full Stripe Help', 'wp-full-stripe'); ?></h2>
    <p>This plugin is designed to make it easy for you to accept payments and create subscriptions from your Wordpress site. Powered by Stripe, you can embed payment forms into any post or page and take payments directly from your website without making your customers leave for a 3rd party website.</p>
    <h4>Setup</h4>
    <ul>
        <li>You need a free Stripe account from <a href="https://stripe.com">Stripe.com</a></li>
        <li>Get your Stripe API keys from your
            <a href="https://manage.stripe.com">Stripe Dashboard</a> -> Account Settings -> API Keys tab
        </li>
        <li>Update the Full Stripe settings with your API keys and select the mode. (Test most is recommended initially to make sure everything is setup correctly)</li>
    </ul>
    <h4>Payments</h4>
    <p>Now that the Stripe keys are set, you can create a payment form from the Full Stripe Payments page. A payment form is setup to take a specific payment amount from your customers. Create the form by setting it's name, title and payment amount.
        You can also choose to allow your customers to enter custom amounts on the form.  This makes creating things like donation forms easier.
        The form name is used in the shortcode (see below) to display the form.</p>
    <p>To show a payment form, add the following shortcode to any post or page:
        <code>[fullstripe_payment form="formName"]</code> where "formName" equals the name you used to create the form.
    </p>
    <p>Once a payment is taken using the form, the payment information will appear on the Full Stripe Payments page as well as on your Stripe Dashboard</p>
    <h4>Checkout Payments</h4>
    <p>You can also create payment forms using the <a href="https://stripe.com/docs/checkout">Stripe Checkout</a> functionality which will give you the option to place a button on any post or page.  <br/>
    The button will trigger loading of a pre-styled form which built in validation.  The styling and functionality of the form is all controlled by Stripe and offers a fast and easy way to get started. <br/>
    Even better, the Stripe Checkout forms are responsive meaning you can have great looking payment forms on mobile devices as well.  These forms act just like regular payment forms with regards to making
    charges and storing the information in your admin Payments table and your Stripe Dashboard.</p>
    <p>To show a Stripe Checkout payment form, add the following shortcode to any post or page:
        <code>[fullstripe_checkout form="formName"]</code> where "formName" equals the name you used to create the form.
    </p>
    <h4>Subscriptions</h4>
    <p>Similar to payments, you can sign customers up for recurring subscriptions using a subscription form. Before creating subscription forms, you will need to create a subscription plan. You can do this from the Full Stripe Subscriptions page or from within your
        <a href="https://manage.stripe.com">Stripe Dashboard</a>. Both ways will create a subscription plan that will be listed on the Full Stripe Subscriptions page ready to use.
    </p>
    <p>When creating a subscription form you choose the name, title and the plans you wish to offer your customer using this form. To show a subscription form, add the following shortcode to any post or page:
        <code>[fullstripe_subscription form="formName"]</code> where "formName" equals the name you used to create the form.
    </p>
    <p>You can view your list of subscribers on the Full Stripe Subscriptions page under the Subscribers tab or directly on the
        <a href="https://manage.stripe.com">Stripe Dashboard</a></p>
    <h4>Bank Transfers</h4>
    <p>In order to use the Full Stripe Bank Transfer feature you must first enable API transfers from your
        <a href="https://manage.stripe.com">Stripe Dashboard</a>. You do this by going to Account Settings -> Transfers tab and clicking the "Switch to API Transfers" button. Please note, that doing this will DISABLE automatic payments from Stripe to your bank account meaning you must request payments MANUALLY.
    </p>
    <p>To send a transfer you must first create a recipient. Due to money laundering and criminal activity regulations
        <strong>you must verify who you are sending money to.</strong> You can do this by adding as much information about the recipient as possible. The minimum required is the recipients full legal name. Stripe may contact you directly if they need more information about a recipient which you will be able to provide through your Stripe dashboard at that time.
    </p>
    <p>Create a recipient from the Full Stripe Transfers page under the Recipients tab. This will create a recipient in your
        <a href="https://manage.stripe.com">Stripe Dashboard</a> which you will be able to send transfers to.</p>
    <p>To make a transfer, go to the Transfers tab and fill in the form for amount, statement descriptor and recipient. You can only select recipients you have previously created or yourself. If you choose "Your own bank account" the transfer will be sent to the bank account you have associated with your Stripe account.</p>
    <p>Because bank transfers are not instant you will have to check your Stripe account to view the status of the transfer.
        <a href="https://stripe.com/docs/tutorials/sending-transfers#transfer-timeline">Stripe says,</a> "Transfers will be available in the bank account the next business day if created before 21:00 UTC (2pm PST). If the transfer fails (due to a typo in the bank details, for example), it can take up to five business days for Stripe to be notified"
    </p>
    <h4>SSL</h4>
    <p>Use of SSL is
        <strong>highly recommended</strong> as this will protect your customers card details. No card details are ever stored on your server however without SSL they are still subject to certain types of hacking. SSL certificates are extremely affordable from companies like
        <a href="http://www.namecheap.com/?aff=51961">Namecheap</a> and well worth it for the security of your customers.
    </p>
    <h4>Payment Currency</h4>
    <p>Select the currency you receive payments in from the Full Stripe settings menu.  If you have a US Stripe account you can only accept payments in USD and selecting any other option will fail.  If you have a Canadian Stripe account you can select either USD or CAD.
    The UK and the Ireland beta accounts can accept Euros, GBP and USD (only with a US account).</p>
    <p>Please note that you must make sure your Stripe account is set to accept the same currency otherwise payment will fail.  This means going to your Stripe Account Settings and selecting the currency, or adding a secondary currency in the case of accounts which allow that.</p>
    <h4>Custom Fields</h4>
    <p>You can add an extra field to subscription and payment forms to include any extra data you want to request from the customer.  When creating the payment form you can choose to include this extra field and it's title which will be shown to the user on the form.
    The extra data will be appended to the payment information and viewable in your Stripe dashboard once the payment is complete.</p>
    <h4>Styling</h4>
    <p>You can customize the appearance of the payment forms using the included CSS classes. Add CSS rules for <code>fullstripe-form-title</code> to style the form titles, <code>fullstripe-form-input</code> for the form inputs and <code>fullstripe-form-label</code> for the form labels.
    You can update the CSS in the Settings menu.  There are defaults provided and it's recommended if you don't understand CSS you should leave these alone as it could break your forms.</p>
    <h4>Coupons</h4>
    <p>You can accept coupon codes with subscriptions.  First you must create the coupon in your Stripe dashboard.  When creating your subscription forms you can turn on the option to allow a coupon code input and if the customer adds the correct code this will be applied to their payment(s).</p>
    <h4>Redirects</h4>
    <p>When creating payment or subscription forms you have the option to redirect to a specific page or post after a successful payment.  To do this you must turn on redirects when creating the form and also input the post ID of the post/page you wish to redirect to.<br/>
    If you are using the default permalink structure you'll see a the post ID of each post/page in the URL bar when viewing that post/page.  If you are using "pretty" permalinks you can also find the post/page ID by viewing all posts/pages in the Wordpress dashboard menu.
    Simply hover your mouse over the post/page title and you'll see the post ID in the browser status bar.  Finally, you can also see the post ID by browsing your database table 'wp_posts' for your Wordpress website.</p>
    <h4>Email Receipts</h4>
    <p>Standard payment forms have the option to send customized email receipts (Subscription and Checkout form emails can be enabled in your Stripe dashboard).  You have a few tokens that can be placed in the email HTML
    and WP Full Stripe will replace them with the relevant values at the point of successful payment.  The tokens you can use are: <br/>
    <ul>
        <li><strong>%AMOUNT%</strong> - The payment amount</li>
        <li><strong>%NAME%</strong> - The name of your WordPress blog</li>
        <li><strong>%CUSTOMERNAME%</strong> - The customers cardholder name</li>
        <li><strong>%ADDRESS1%</strong> - The customers billing address line 1 (street)</li>
        <li><strong>%ADDRESS2%</strong> - The customers billing address line 2</li>
        <li><strong>%CITY%</strong> - The customers billing address city</li>
        <li><strong>%STATE%</strong> - The customers billing address state (or region/county)</li>
        <li><strong>%ZIP%</strong> - The customers billing address zip (or postal) code</li>
    </ul>
    </p>
    <h4>Plugin Updates</h4>
    <p>If you are having any issues when updating the plugin to the latest code, please try re-installing the plugin first and then de-activate, activate again.  This forces the database to update any changes.  Don't worry, none of your data will be lost if you do this!</p>
    <h4>More Help</h4>
    <p>If you require any more help with this plugin, you can always go to
        <a href="http://mammothology.com/forums">the Mammothology Support Forums</a> to ask your question, or email us directly at
        <a href="mailto:support@mammothology.com">support@mammothology.com</a></p>
    <div style="padding-top: 50px;">
        <h4>Notices</h4>
        <p>Please note that while every care has been taken to write secure and working code, Mammothology and Jamie Osborne take no responsibility for any errors, faults or other problems arising from using this payments plugin. Use at your own risk. Mammothology cannot foresee every possible usage and user error and does not condone the use of this plugin for any illegal means. Mammothology has no affiliation with
            <a href="https://stripe.com">Stripe</a> and any issues with payments should be directed to
            <a href="https://stripe.com">Stripe.com</a>.</p>
    </div>
</div>