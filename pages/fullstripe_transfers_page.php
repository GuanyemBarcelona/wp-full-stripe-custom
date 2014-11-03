<?php
$recipients = MM_WPFS::getInstance()->get_recipients();
?>
<div class="wrap">
    <h2> <?php echo __('Full Stripe Transfers', 'wp-full-stripe'); ?> </h2>
    <div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
    <p class="alert alert-info">Please note that Bank Transfers are only supported for US based Stripe accounts. The plugin will be updated to support other countries transfers once Stripe supports them.</p>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#recipients" data-toggle="tab">Recipients</a></li>
        <li><a href="#transfers" data-toggle="tab">Transfers</a></li>
        <li><a href="#create" data-toggle="tab">Create New Recipient</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="recipients">
            <?php if (count($recipients) === 0): ?>
                <p class="alert alert-info">You have created no recipients yet. Use the form below to get started</p>
            <?php else: ?>
                <table class="table table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>Stripe ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Email</th>
                    </tr>
                    </thead>
                    <tbody id="recipientsTable">
                    <?php foreach ($recipients['data'] as $rp): ?>
                        <tr>
                            <td><?php echo $rp->id; ?></td>
                            <td><?php echo $rp->name; ?></td>
                            <td><?php echo $rp->type  ?></td>
                            <td><?php if (isset($rp->email)) echo $rp->email;
                                else echo "Not Supplied"; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="tab-pane" id="transfers">
            <p class="alert alert-info">Here you can initiate a bank transfer to any created recipients or yourself.
                <strong>You must have sufficient funds in your Stripe account</strong> otherwise the transfer will fail.
            </p>
            <form class="form-horizontal" action="" method="POST" id="create-transfer-form">
                <p class="transfer-tips"></p>
                <input type="hidden" name="action" value="wp_full_stripe_create_transfer"/>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Transfer Amount: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="transfer_amount" id="transfer_amount">
                            <p class="description">The amount to transfer, in cents. i.e. for $10.00 enter 1000</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Statement Descriptor: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="transfer_desc" id="transfer_desc">
                            <p class="description">A 15 character descriptor, it will appear on the recipients bank statement</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Transfer Recipient: </label>
                        </th>
                        <td>
                            <select id="transfer_recipient" name="transfer_recipient">
                                <option value="self">Your own bank account</option>
                                <?php if (count($recipients) != 0): ?>
                                    <?php foreach ($recipients['data'] as $rp): ?>
                                        <option value="<?php echo $rp->id; ?>"><?php echo $rp->name . ' (' . $rp->id . ')'; ?></option>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </select>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button class="button button-primary" type="submit">Initiate Transfer</button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
                <p class="description">NOTE: Transfers can take up to 5 business days. Check your Stripe account for confirmation.</p>
            </form>
        </div>
        <div class="tab-pane" id="create">
            <form class="form-horizontal" action="" method="POST" id="create-recipient-form">
                <p class="tips"></p>
                <input type="hidden" name="action" value="wp_full_stripe_create_recipient"/>
                <input type="hidden" data-stripe="country" value="US">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Full Legal Name: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="recipient_name" id="recipient_name">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Recipient Type: </label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="recipient_type" id="typeInd" value="individual" checked> Individual
                            </label> <label class="radio inline">
                                <input type="radio" name="recipient_type" id="typeCorp" value="corporation"> Corporation
                            </label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Tax ID: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="recipient_tax_id" id="recipient_tax_id">
                            <p class="description">For individual use SSN, for corporation use EIN. (optional, but recommended)</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Bank Routing Number: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" data-stripe="routingNumber">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Bank Account Number: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" data-stripe="accountNumber">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Recipient Email: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="recipient_email" id="recipient_email">
                            <p class="description">Useful for searching & viewing on your Stripe dashboard. (optional)</p>
                        </td>
                    </tr>
                </table>
                <p class="sumbit">
                    <button class="button button-primary" type="submit">Create Recipient</button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
            </form>
        </div>
    </div>
</div>