/*
 Plugin Name: WP Full Stripe
 Plugin URI: http://mammothology.com/products/view/wp-full-stripe
 Description: Complete Stripe payments integration for Wordpress
 Author: Mammothology
 Version: 1.0
 Author URI: http://mammothology.com
 */

Stripe.setPublishableKey(stripekey);

jQuery(document).ready(function ($)
{
    $("#showLoading").hide();
    $("#showLoadingC").hide();
    var $err = $(".payment-errors");

    $('#payment-form').submit(function (e)
    {
        $("#showLoading").show();

        $err.removeClass('alert alert-error');
        $err.html("");

        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        Stripe.createToken($form, stripeResponseHandler);
        return false;
    });

    var stripeResponseHandler = function (status, response)
    {
        var $form = $('#payment-form');

        if (response.error)
        {
            // Show the errors
            $err.addClass('alert alert-error');
            $err.html(response.error.message);
            $err.fadeIn(500).fadeOut(500).fadeIn(500);
            $form.find('button').prop('disabled', false);
            $("#showLoading").hide();
        }
        else
        {
            // token contains id, last4, and card type
            var token = response.id;
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

            //post payment via ajax
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $("#showLoading").hide();

                    if (data.success)
                    {
                        //clear form fields
                        $form.find('input:text, input:password').val('');
                        //inform user of success
                        $err.addClass('alert alert-success');
                        $err.html(data.msg);
                        $form.find('button').prop('disabled', false);
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
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $err.addClass('alert alert-error');
                        $err.html(data.msg);
                        $err.fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }
    };

    $('#payment-form-style').submit(function (e)
    {
        $("#showLoading").show();
        var $err = $(".payment-errors");
        $err.removeClass('alert alert-error');
        $err.html("");

        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        Stripe.createToken($form, stripeResponseHandler2);
        return false;
    });

    var stripeResponseHandler2 = function (status, response)
    {
        var $form = $('#payment-form-style');

        if (response.error)
        {
            // Show the errors
            $err.addClass('alert alert-error');
            $err.html(response.error.message);
            $err.fadeIn(500).fadeOut(500).fadeIn(500);
            $form.find('button').prop('disabled', false);
            $("#showLoading").hide();
        }
        else
        {
            // token contains id, last4, and card type
            var token = response.id;
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

            //post payment via ajax
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $("#showLoading").hide();

                    if (data.success)
                    {
                        //clear form fields
                        $form.find('input:text, input:password').val('');
                        //inform user of success
                        $err.addClass('alert alert-success');
                        $err.html(data.msg);
                        $form.find('button').prop('disabled', false);
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
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $err.addClass('alert alert-error');
                        $err.html(data.msg);
                        $err.fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }
    };

    var coupon = false;
    $('#fullstripe_plan').change(function ()
    {
        var plan = $("#fullstripe_plan").val();
        var setupFee = parseInt($("#fullstripe_setupFee").val());
        var option = $("#fullstripe_plan").find("option[value='" + plan + "']");
        var count = parseInt(option.attr("data-interval-count"));
        var amount = parseFloat(option.attr('data-amount') / 100);
        var cur = option.attr("data-currency");
        var str = "Plan is " + cur + amount + " per ";
        if (count > 1)
            str += count + " ";
        str += option.attr('data-interval');
        if (count > 1)
            str += "s";

        if (coupon != false)
        {
            str += " (";
            var total;
            if (coupon.percent_off != null)
            {
                total = amount * (1 - ( parseInt(coupon.percent_off) / 100 ));
                str += total.toFixed(2) + " with coupon)";
            }
            else
            {
                total = amount - parseFloat(coupon.amount_off) / 100;
                str += total.toFixed(2) + " with coupon)";
            }
        }

        if (setupFee > 0)
        {
            var sf = (setupFee / 100).toFixed(2);
            str += ". SETUP FEE: " + cur + sf;
        }

        $(".fullstripe_plan_details").text(str);
    }).change();

    $('#fullstripe_check_coupon_code').click(function (e)
    {
        e.preventDefault();
        var cc = $('#fullstripe_coupon_input').val();
        if (cc.length > 0)
        {
            $(this).prop('disabled', true);
            $err.removeClass('alert alert-success');
            $err.removeClass('alert alert-error');
            $err.html("");
            $("#showLoadingC").show();

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: 'wp_full_stripe_check_coupon', code: cc},
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $("#fullstripe_check_coupon_code").prop('disabled', false);
                    $("#showLoadingC").hide();

                    if (data.valid)
                    {
                        coupon = data.coupon;
                        $('#fullstripe_plan').change();
                        $err.addClass('alert alert-success');
                        $err.html(data.msg);
                        $err.fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                    else
                    {
                        $err.addClass('alert alert-error');
                        $err.html(data.msg);
                        $err.fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }
        return false;
    });

});