<?php
$options = get_option('fullstripe_options');
$currencySymbol = '$';
if ($options['currency'] === 'eur')
{
    $currencySymbol = '€';
}
elseif ($options['currency'] === 'gbp')
{
    $currencySymbol = '£';
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'subscribers';

global $wpdb;

//Load based on what tab we have open
if ($active_tab == 'subscribers')
{
    $subscribers = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "fullstripe_subscribers LIMIT 50;");
}
else if ($active_tab == 'forms')
{
    $subscriptionForms = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "fullstripe_subscription_forms;");
}
else if ($active_tab == 'plans' || $active_tab == 'createform')
{
    $plans = MM_WPFS::getInstance()->get_plans();
}
?>
<div class="wrap">
<h2> <?php echo __('Full Stripe Subscriptions', 'wp-full-stripe'); ?> </h2>
<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
<h2 class="nav-tab-wrapper">
    <a href="?page=fullstripe-subscriptions&tab=subscribers" class="nav-tab <?php echo $active_tab == 'subscribers' ? 'nav-tab-active' : ''; ?>">Subscribers</a>
    <a href="?page=fullstripe-subscriptions&tab=forms" class="nav-tab <?php echo $active_tab == 'forms' ? 'nav-tab-active' : ''; ?>">Subscription Forms</a>
    <a href="?page=fullstripe-subscriptions&tab=plans" class="nav-tab <?php echo $active_tab == 'plans' ? 'nav-tab-active' : ''; ?>">Subscription Plans</a>
    <a href="?page=fullstripe-subscriptions&tab=createform" class="nav-tab <?php echo $active_tab == 'createform' ? 'nav-tab-active' : ''; ?>">Create New Form</a>
    <a href="?page=fullstripe-subscriptions&tab=createplan" class="nav-tab <?php echo $active_tab == 'createplan' ? 'nav-tab-active' : ''; ?>">Create New Plan</a>
</h2>
<div class="tab-content">
<?php if ($active_tab == 'subscribers'): ?>
    <div class="" id="subscribers">
        <?php $subscribersTable->display(); ?>
    </div>
<?php elseif ($active_tab == 'forms'): ?>
    <div class="" id="forms">
        <div style="min-height: 200px;">
            <h2>Your Subscription Forms
                <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
            </h2>
            <?php if (count($subscriptionForms) === 0): ?>
                <p class="alert alert-info">No subscription forms created. Use the Create New Form tab to get started</p>
            <?php else: ?>
                <table class="table table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Title</th>
                        <th>Plan IDs</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="subscriptionFormsTable">
                    <?php foreach ($subscriptionForms as $sf): ?>
                        <tr>
                            <td><?php echo $sf->name; ?></td>
                            <td><?php echo $sf->formTitle; ?></td>
                            <td><?php echo $sf->plans; ?></td>
                            <td>
                                <a class="btn btn-mini edit" href="<?php echo admin_url("admin.php?page=fullstripe-edit-form&form=$sf->subscriptionFormID&type=subscription"); ?>">Edit</a>
                            </td>
                            <td>
                                <button class="btn btn-mini delete" data-id="<?php echo $sf->subscriptionFormID; ?>" data-type="subscriptionForm">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
<?php elseif ($active_tab == 'plans'): ?>
    <div class="" id="plans">
        <h2>Your Subscription Plans</h2>
        <?php if (count($plans) === 0): ?>
            <p class="alert alert-info">You have no subscription plans created yet. Use the Create New Plan tab to get started</p>
        <?php else: ?>
            <table class="table table-condensed table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Display Name</th>
                    <th>Amount</th>
                    <th>Interval</th>
                    <th>Trial</th>
                </tr>
                </thead>
                <tbody id="plansTable">
                <?php foreach ($plans['data'] as $plan): ?>
                    <tr>
                        <td><?php echo $plan->id; ?></td>
                        <td><?php echo $plan->name; ?></td>
                        <td><?php echo $currencySymbol . sprintf('%0.2f', $plan->amount / 100.0); ?></td>
                        <?php if ($plan->interval_count == 1): ?>
                            <td><?php echo ucfirst($plan->interval) . 'ly'; ?></td>
                        <?php else: ?>
                            <td><?php echo $plan->interval_count . ' ' . $plan->interval . 's'; ?></td>
                        <?php endif; ?>
                        <td><?php if (isset($plan->trial_period_days)) echo $plan->trial_period_days . ' days';
                            else echo "No Trial"; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php elseif ($active_tab == 'createform'): ?>
    <div class="" id="createform">
        <?php if (count($plans) === 0): ?>
            <p class="alert alert-info">You must have at least one subscription plan created before creating a subscription form</p>
        <?php else: ?>
            <form class="form-horizontal" action="" method="POST" id="create-subscription-form">
                <p class="tips"></p>
                <input type="hidden" name="action" value="wp_full_stripe_create_subscripton_form"/>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Form Name: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_name" id="form_name">
                            <p class="description">This name will be used to identify this form in the shortcode i.e. [fullstripe_subscription form="FormName"]</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Form Title: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_title" id="form_title">
                            <p class="description">The title of the form</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Include Coupon Input Field? </label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_include_coupon_input" id="noinclude_coupon_input" value="0" checked="checked"> No
                            </label> <label class="radio inline">
                                <input type="radio" name="form_include_coupon_input" id="include_coupon_input" value="1"> Yes
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
                                <input type="radio" name="form_show_address_input" id="hide_address_input" value="0" checked="checked"> Hide
                            </label> <label class="radio inline">
                                <input type="radio" name="form_show_address_input" id="show_address_input" value="1"> Show
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
                                <input type="radio" name="form_include_custom_input" id="noinclude_custom_input" value="0" checked="checked"> No
                            </label> <label class="radio inline">
                                <input type="radio" name="form_include_custom_input" id="include_custom_input" value="1"> Yes
                            </label>
                            <p class="description">You can ask for extra information from the customer to be included in the payment details</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Custom Input Label: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_custom_input_label" id="form_custom_input_label" disabled="disabled"/>
                            <p class="description">The text for the label next to the custom input field</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Subscribe Button Text: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_button_text" id="form_button_text" value="Subscribe">
                            <p class="description">The text on the subscribe button</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Setup Fee: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_setup_fee" id="form_setup_fee" value="0">
                            <p class="description">Amount to charge the customer to setup the subscription. Entering 0 will disable. (in cents. i.e. for $10.00 enter 1000)</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Plans: </label>
                        </th>
                        <td>
                            <div class="plan_checkboxes">
                                <?php foreach ($plans['data'] as $plan): ?>
                                    <label class="checkbox inline">
                                        <p>
                                            <input type="checkbox" class="plan_checkbox" id="check_<?php echo $plan->id; ?>" value="<?php echo $plan->id; ?>">
                                        <span class="plan_checkbox_text"><?php echo $plan->name; ?> (
                                            <?php
                                            $str = $currencySymbol . sprintf('%0.2f', $plan->amount / 100.0);
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
                                <input type="radio" name="form_do_redirect" id="do_redirect_no" value="0" checked="checked"> No
                            </label> <label class="radio inline">
                                <input type="radio" name="form_do_redirect" id="do_redirect_yes" value="1"> Yes
                            </label>
                            <p class="description">When payment is successful you can choose to redirect to another page or post</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Redirect Page/Post ID: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_redirect_post_id" id="form_redirect_post_id" disabled="disabled"/>
                            <p class="description">The ID for the page/post to redirect to after payment is successful</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button class="button button-primary" type="submit">Create Form</button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
            </form>
        <?php endif; ?>
    </div>
<?php elseif ($active_tab == 'createplan'): ?>
    <div class="" id="createplan">
        <form class="form-horizontal" action="" method="POST" id="create-subscription-plan">
            <p class="tips"></p>
            <input type="hidden" name="action" value="wp_full_stripe_create_plan"/>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">ID: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="sub_id" id="sub_id">
                        <p class="description">This ID is used to identify this plan when creating a subscription form and on your Stripe dashboard</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Display Name: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="sub_name" id="sub_name">
                        <p class="description">The name you wish to be displayed to customers for this plan</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Payment Amount: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="sub_amount" id="sub_amount"/>
                        <p class="description">The amount this plan will charge your customer, in cents. i.e. for $10.00 enter 1000</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Payment Interval: </label>
                    </th>
                    <td>
                        <select id="sub_interval" name="sub_interval">
                            <option value="week">Weekly</option>
                            <option value="month">Monthly</option>
                            <option value="year">Yearly</option>
                        </select>
                        <p class="description">How often the payment amount is charged to the customer</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Payment Interval Count: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="sub_interval_count" id="sub_interval_count" value="1"/>
                        <p class="description">You could specify an interval count of 3 and an interval of 'Monthly' for quarterly billing (every 3 months). Default is 1 for Weekly/Monthly/Yearly.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label">Trial Period Days: </label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="sub_trial" id="sub_trial" value="0"/>
                        <p class="description">How many trial days the customer has before being charged. Set to 0 to disable trial period.</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button class="button button-primary" type="submit">Create Plan</button>
                <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
            </p>
        </form>
    </div>
<?php endif; ?>
</div>
</div>