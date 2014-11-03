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
    $(".showLoading").hide();
    $("#updateDiv").hide();
    $("#createCheckoutFormSection").hide();

    function resetForm($form)
    {
        $form.find('input:text, input:password, input:file, select, textarea').val('');
        $form.find('input:radio, input:checkbox')
            .removeAttr('checked').removeAttr('selected');
    }

    function validField(field, fieldName, errorField)
    {
        var valid = true;
        if (field.val() === "")
        {
            errorField.addClass('alert alert-error');
            errorField.html("<p>" + fieldName + " must contain a value</p>");
            valid = false;
        }
        return valid;
    }

    //for uploading images using WordPress media library
    var custom_uploader;
    function uploadImage(inputID)
    {
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader)
        {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title:'Choose Image',
            button:{
                text:'Choose Image'
            },
            multiple:false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function ()
        {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $(inputID).val(attachment.url);
        });

        //Open the uploader dialog
        custom_uploader.open();
    }

    //upload checkout form images
    $('#upload_image_button').click(function (e)
    {
        e.preventDefault();
        uploadImage('#form_checkout_image');
    });


    $('#create-subscription-plan').submit(function (e)
    {
        $(".tips").removeClass('alert alert-error');
        $(".tips").html("");

        var valid = validField($('#sub_id'), 'ID', $('.tips'));
        valid = valid && validField($('#sub_name'), 'Name', $('.tips'));
        valid = valid && validField($('#sub_amount'), 'Amount', $('.tips'));
        valid = valid && validField($('#sub_trial'), 'Trial Days', $('.tips'));

        if (valid)
        {
            $(".showLoading").show();
            var $form = $(this);

            // Disable the submit button
            $form.find('button').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        var row = '<tr>';
                        row += '<td>' + $('#sub_id').val() + '</td>';
                        row += '<td>' + $('#sub_name').val() + '</td>';
                        row += '<td>' + $('#sub_amount').val() + '</td>';
                        row += '<td>' + $('#sub_interval').val() + '</td>';
                        row += '<td>' + $('#sub_trial').val() + '</td>';
                        row += '</tr>';
                        $('#plansTable').append(row);
                        $("#updateMessage").text("Plan created.");
                        $("#updateDiv").addClass('updated').show();
                        $form.find('button').prop('disabled', false);
                        resetForm($form);
                    }
                    else
                    {
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $(".tips").addClass('alert alert-error');
                        $(".tips").html(data.msg);
                        $(".tips").fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }

        return false;

    });

    $('#create-subscription-form').submit(function (e)
    {
        $(".tips").removeClass('alert alert-error');
        $(".tips").html("");

        //get the checked plans
        var checkedVals = $('.plan_checkbox:checkbox:checked').map(function ()
        {
            return this.value;
        }).get();
        var plans = checkedVals.join(",");

        var includeCustom = $('input[name=form_include_custom_input]:checked', '#create-subscription-form').val();

        var valid = validField($('#form_name'), 'Name', $('.tips'));
        valid = valid && validField($('#form_title'), 'Form Title', $('.tips'));
        if (includeCustom == 1)
            valid = valid && validField($('#form_custom_input_label'), 'Custom Input Label', $('.tips'));

        if (valid && checkedVals.length === 0)
        {
            $(".tips").addClass('alert alert-error');
            $(".tips").html("<p>You must check at least one subscription plan</p>");
            valid = false;
        }

        if (valid)
        {
            $(".showLoading").show();
            var $form = $(this);
            // Disable the submit button
            $form.find('button').prop('disabled', true);

            //create a plans field for all the checked plans
            $form.append("<input type='hidden' name='selected_plans' value='" + plans + "' />");

            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        var row = '<tr>';
                        row += '<td>' + $('#form_name').val() + '</td>';
                        row += '<td>' + $('#form_title').val() + '</td>';
                        row += '<td>' + plans + '</td>';
                        row += '<td><button class="btn btn-mini edit" disabled="disabled">Edit</button></td>';
                        row += '<td><button class="btn btn-mini" disabled="disabled">Delete</button></td>';
                        row += '</tr>';
                        $('#subscriptionFormsTable').append(row);

                        $("#updateMessage").text("Subscription form created");
                        $("#updateDiv").addClass('updated').show();
                        $form.find('button').prop('disabled', false);
                        resetForm($form);
                    }
                    else
                    {
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $(".tips").addClass('alert alert-error');
                        $(".tips").html(data.msg);
                        $(".tips").fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }

        return false;
    });

    $('#edit-subscription-form').submit(function (e)
    {
        var $err = $(".tips");
        $err.removeClass('alert alert-error');
        $err.html("");

        //get the checked plans
        var checkedVals = $('.plan_checkbox:checkbox:checked').map(function ()
        {
            return this.value;
        }).get();
        var plans = checkedVals.join(",");

        var includeCustom = $('input[name=form_include_custom_input]:checked', '#edit-subscription-form').val();

        var valid = validField($('#form_name'), 'Name', $err);
        valid = valid && validField($('#form_title'), 'Form Title', $err);
        if (includeCustom == 1)
            valid = valid && validField($('#form_custom_input_label'), 'Custom Input Label', $err);

        if (valid && checkedVals.length === 0)
        {
            $err.addClass('alert alert-error');
            $err.html("<p>You must check at least one subscription plan</p>");
            valid = false;
        }

        if (valid)
        {
            $(".showLoading").show();
            var $form = $(this);
            // Disable the submit button
            $form.find('button').prop('disabled', true);

            //create a plans field for all the checked plans
            $form.append("<input type='hidden' name='selected_plans' value='" + plans + "' />");

            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        $("#updateMessage").text("Subscription form updated");
                        $("#updateDiv").addClass('updated').show();
                        resetForm($form);
                        setTimeout(function ()
                        {
                            window.location = data.redirectURL;
                        }, 1500);
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

        return false;
    });

    //payment type toggle
    $('#set_custom_amount').click(function ()
    {
        $('#form_amount').prop('disabled', true);
    });
    $('#set_specific_amount').click(function ()
    {
        $('#form_amount').prop('disabled', false);
    });
    $('#noinclude_custom_input').click(function ()
    {
        $('#form_custom_input_label').prop('disabled', true);
    });
    $('#include_custom_input').click(function ()
    {
        $('#form_custom_input_label').prop('disabled', false);
    });
    $('#do_redirect_no').click(function ()
    {
        $('#form_redirect_post_id').prop('disabled', true);
    });
    $('#do_redirect_yes').click(function ()
    {
        $('#form_redirect_post_id').prop('disabled', false);
    });
    $('#do_redirect_no_ck').click(function ()
    {
        $('#form_redirect_post_id_ck').prop('disabled', true);
    });
    $('#do_redirect_yes_ck').click(function ()
    {
        $('#form_redirect_post_id_ck').prop('disabled', false);
    });

    //form type toggle
    $('#set_payment_form_type_payment').click(function ()
    {
        $("#createCheckoutFormSection").hide();
        $("#createPaymentFormSection").show();
    });
    $('#set_payment_form_type_checkout').click(function ()
    {
        $("#createCheckoutFormSection").show();
        $("#createPaymentFormSection").hide();
    });


    $('#create-payment-form').submit(function (e)
    {
        $(".tips").removeClass('alert alert-error');
        $(".tips").html("");

        var customAmount = $('input[name=form_custom]:checked', '#create-payment-form').val();
        var includeCustom = $('input[name=form_include_custom_input]:checked', '#create-payment-form').val();

        var valid = validField($('#form_name'), 'Name', $('.tips'));
        valid = valid && validField($('#form_title'), 'Form Title', $('.tips'));
        if (customAmount == 0)
            valid = valid && validField($('#form_amount'), 'Amount', $('.tips'));
        if (includeCustom == 1)
            valid = valid && validField($('#form_custom_input_label'), 'Custom Input Label', $('.tips'));

        if (valid)
        {
            $(".showLoading").show();
            var $form = $(this);
            // Disable the submit button
            $form.find('button').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        var row = '<tr>';
                        row += '<td>' + $('#form_name').val() + '</td>';
                        row += '<td>' + $('#form_title').val() + '</td>';
                        row += '<td>' + $('#form_amount').val() + '</td>';
                        row += '<td><button class="btn btn-mini edit" disabled="disabled">Edit</button></td>';
                        row += '<td><button class="btn btn-mini" disabled="disabled">Delete</button></td>';
                        row += '</tr>';
                        $('#paymentFormsTable').append(row);

                        $("#updateMessage").text("Payment form created");
                        $("#updateDiv").addClass('updated').show();
                        $form.find('button').prop('disabled', false);
                        resetForm($form);
                    }
                    else
                    {
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $(".tips").addClass('alert alert-error');
                        $(".tips").html(data.msg);
                        $(".tips").fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }

        return false;
    });

    $('#edit-payment-form').submit(function (e)
    {
        var $err = $(".tips");
        $err.removeClass('alert alert-error');
        $err.html("");

        var customAmount = $('input[name=form_custom]:checked', '#edit-payment-form').val();
        var includeCustom = $('input[name=form_include_custom_input]:checked', '#edit-payment-form').val();

        var valid = validField($('#form_name'), 'Name', $err);
        valid = valid && validField($('#form_title'), 'Form Title', $err);
        if (customAmount == 0)
            valid = valid && validField($('#form_amount'), 'Amount', $err);
        if (includeCustom == 1)
            valid = valid && validField($('#form_custom_input_label'), 'Custom Input Label', $err);

        if (valid)
        {
            $(".showLoading").show();
            var $form = $(this);
            // Disable the submit button
            $form.find('button').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        $("#updateMessage").text("Payment form updated");
                        $("#updateDiv").addClass('updated').show();
                        resetForm($form);
                        setTimeout(function ()
                        {
                            window.location = data.redirectURL;
                        }, 1500);
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

        return false;
    });


    $('#create-checkout-form').submit(function (e)
    {
        $(".tips").removeClass('alert alert-error');
        $(".tips").html("");

        var valid = validField($('#form_name_ck'), 'Name', $('.tips'));
        valid = valid && validField($('#company_name_ck'), 'Company Name', $('.tips'));
        valid = valid && validField($('#form_amount_ck'), 'Amount', $('.tips'));

        if (valid)
        {
            $(".showLoading").show();
            var $form = $(this);
            // Disable the submit button
            $form.find('button').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        var row = '<tr>';
                        row += '<td>' + $('#form_name_ck').val() + '</td>';
                        row += '<td>' + $('#prod_desc_ck').val() + '</td>';
                        row += '<td>' + $('#form_amount_ck').val() + '</td>';
                        row += '<td><button class="btn btn-mini edit" disabled="disabled">Edit</button></td>';
                        row += '<td><button class="btn btn-mini" disabled="disabled">Delete</button></td>';
                        row += '</tr>';
                        $('#checkoutFormsTable').append(row);

                        $("#updateMessage").text("Checkout form created");
                        $("#updateDiv").addClass('updated').show();
                        $form.find('button').prop('disabled', false);
                        resetForm($form);
                    }
                    else
                    {
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $(".tips").addClass('alert alert-error');
                        $(".tips").html(data.msg);
                        $(".tips").fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }

        return false;
    });

    $('#edit-checkout-form').submit(function (e)
    {
        var $err = $(".tips");
        $err.removeClass('alert alert-error');
        $err.html("");

        var valid = validField($('#form_name_ck'), 'Name', $('.tips'));
        valid = valid && validField($('#company_name_ck'), 'Company Name', $('.tips'));
        valid = valid && validField($('#form_amount_ck'), 'Amount', $('.tips'));

        if (valid)
        {
            $(".showLoading").show();
            var $form = $(this);
            // Disable the submit button
            $form.find('button').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        $("#updateMessage").text("Checkout form updated");
                        $("#updateDiv").addClass('updated').show();
                        resetForm($form);
                        setTimeout(function ()
                        {
                            window.location = data.redirectURL;
                        }, 1500);
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

        return false;
    });

    $('#settings-form').submit(function (e)
    {
        $(".showLoading").show();
        $(".tips").removeClass('alert alert-error');
        $(".tips").html("");

        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        var valid = true;

        if (valid)
        {
            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        $("#updateMessage").text("Settings updated");
                        $("#updateDiv").addClass('updated').show();
                        $form.find('button').prop('disabled', false);
                    }
                    else
                    {
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $(".tips").addClass('alert alert-error');
                        $(".tips").html(data.msg);
                        $(".tips").fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });

            return false;
        }

    });

    //The forms delete button
    $('button.delete').click(function ()
    {
        var id = $(this).attr('data-id');
        var type = $(this).attr('data-type');
        var action = '';
        if (type === 'paymentForm')
            action = 'wp_full_stripe_delete_payment_form';
        else if (type === 'subscriptionForm')
            action = 'wp_full_stripe_delete_subscription_form';
        else if (type === 'checkoutForm')
            action = 'wp_full_stripe_delete_checkout_form';

        var row = $(this).parents('tr:first');

        $(".showLoading").show();

        $.ajax({
            type: "POST",
            url: admin_ajaxurl,
            data: {id: id, action: action},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $(".showLoading").hide();

                if (data.success)
                {
                    $(row).remove();
                    $("#updateMessage").text("Form deleted");
                    $("#updateDiv").addClass('updated').show();
                }
            }
        });

        return false;

    });

    /////////////////////////

    var stripeResponseHandler = function (status, response)
    {
        var $form = $('#create-recipient-form');

        if (response.error)
        {
            // Show the errors
            $(".tips").addClass('alert alert-error');
            $(".tips").html(response.error.message);
            $(".tips").fadeIn(500).fadeOut(500).fadeIn(500);
            $form.find('button').prop('disabled', false);
            $(".showLoading").hide();
        }
        else
        {
            // token contains bank account
            var token = response.id;
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

            //post payment via ajax
            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();

                    if (data.success)
                    {
                        //clear form fields
                        $form.find('input:text, input:password').val('');
                        //inform user of success
                        $(".tips").addClass('alert alert-success');
                        $(".tips").html(data.msg);
                        $form.find('button').prop('disabled', false);
                        //add to table
                        var row = "<tr>";
                        row += "<td>" + data.recipient.id + "</td>";
                        row += "<td>" + data.recipient.name + "</td>";
                        row += "<td>" + data.recipient.type + "</td>";
                        row += "<td>" + data.recipient.email + "</td>";
                        row += "</tr>";
                        $('#recipientsTable').append(row);
                    }
                    else
                    {
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $(".tips").addClass('alert alert-error');
                        $(".tips").html(data.msg);
                        $(".tips").fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }
    };

    $('#create-recipient-form').submit(function (e)
    {
        e.preventDefault();
        $(".tips").removeClass('alert alert-error');
        $(".tips").html("");

        var $form = $(this);

        var valid = validField($('#recipient_name'), 'Recipient Name', $('.tips'));

        if (valid)
        {
            $(".showLoading").show();
            // Disable the submit button
            $form.find('button').prop('disabled', true);
            Stripe.bankAccount.createToken($form, stripeResponseHandler);
        }
        return false;
    });

    $('#create-transfer-form').submit(function (e)
    {
        $(".transfer-tips").removeClass('alert alert-error');
        $(".transfer-tips").html("");

        var $form = $(this);

        var valid = validField($('#transfer_amount'), 'Transfer Amount', $('.transfer-tips'));

        if (valid)
        {
            $(".showLoading").show();
            // Disable the submit button
            $form.find('button').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();

                    if (data.success)
                    {
                        $("#updateMessage").text("Transfer initiated");
                        $("#updateDiv").addClass('updated').show();
                        $form.find('button').prop('disabled', false);
                        //clear form fields
                        $form.find('input:text, input:password').val('');
                    }
                    else
                    {
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $(".transfer-tips").addClass('alert alert-error');
                        $(".transfer-tips").html(data.msg);
                        $(".transfer-tips").fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }
        return false;

    });

});