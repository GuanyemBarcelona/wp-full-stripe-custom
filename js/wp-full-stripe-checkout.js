jQuery(document).ready(function ($)
{
    $('#showLoading').hide();
    $('#fullstripe_checkout_button_text').text(checkout_form_data.openButtonTitle);

    var handler = StripeCheckout.configure({
        key: stripekey,
        token: function (token, args)
        {
            var $form = $('#fullstripe_checkout_form');
            var $err = $(".payment-errors");

            $form.append("<input type='hidden' name='stripeToken' value='" + token.id + "' />");
            $form.append("<input type='hidden' name='stripeEmail' value='" + token.email + "' />");
            $form.append("<input type='hidden' name='form' value='" + checkout_form_data.name + "' />");
            $form.append("<input type='hidden' name='doRedirect' value='" + checkout_form_data.redirectOnSuccess + "' />");
            $form.append("<input type='hidden' name='redirectId' value='" + checkout_form_data.redirectPostID + "' />");

            //if billing address
            if (checkout_form_data.showBillingAddress == 1 && args.length > 0)
            {
                $form.append("<input type='hidden' name='billing_name' value='" + args.billing_name + "' />");
                $form.append("<input type='hidden' name='billing_address_country' value='" + args.billing_address_country + "' />");
                $form.append("<input type='hidden' name='billing_address_zip' value='" + args.billing_address_zip + "' />");
                $form.append("<input type='hidden' name='billing_address_state' value='" + args.billing_address_state + "' />");
                $form.append("<input type='hidden' name='billing_address_line1' value='" + args.billing_address_line1 + "' />");
                $form.append("<input type='hidden' name='billing_address_city' value='" + args.billing_address_city + "' />");
            }

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $('#showLoading').hide();

                    if (data.success)
                    {
                        //inform user of success
                        $err.addClass('alert alert-success');
                        $err.html(data.msg);

                        //server tells us if redirect is required
                        if (data.redirect)
                        {
                            setTimeout(function ()
                            {
                                window.location = data.redirectURL;
                            }, 1500);
                        }
                    }
                    else
                    {
                        // show the errors on the form
                        $err.addClass('alert alert-error');
                        $err.html(data.msg);
                        $err.fadeIn(500).fadeOut(500).fadeIn(500);
                    }

                }

            });
        },
        closed: function ()
        {
            $('#showLoading').hide();
        }
    });

    $('#fullstripe_checkout_form').submit(function(e)
    {
        e.preventDefault();
        $('#showLoading').show();

        handler.open({
            name: checkout_form_data.companyName,
            description: checkout_form_data.productDesc,
            amount: checkout_form_data.amount,
            panelLabel: checkout_form_data.buttonTitle,
            billingAddress: (checkout_form_data.showBillingAddress == 1),
            allowRememberMe: (checkout_form_data.showRememberMe == 1),
            image: checkout_form_data.image,
            currency: checkout_form_data.currency
        });

        return false;
    });

});