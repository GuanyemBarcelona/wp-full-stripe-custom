/*
 Plugin Name: WP Full Stripe
 Plugin URI: http://mammothology.com/products/view/wp-full-stripe
 Description: Complete Stripe payments integration for Wordpress
 Author: Mammothology
 Version: 1.0
 Author URI: http://mammothology.com
 */

Stripe.setPublishableKey(stripekey);

var locale = {
  ERROR_TERMS: {
    ca: "Has d'acceptar les condicions generals per continuar.",
    es: "Tienes que aceptar las condiciones generales para continuar."
  },
  ERROR_ADULT: {
    ca: "Has de ser adult per continuar.",
    es: "Tienes que ser adulto para continuar."
  },
  PER: {
    ca: "/",
    es: "/"
  },
  MONTH: {
    ca: "mes",
    es: "mes"
  },
};
var config = {
  LANGUAGE: 'ca'
};

jQuery(document).ready(function ($)
{
    config.LANGUAGE = $('html').attr('lang');

    $("#showLoading").hide();
    $("#showLoadingC").hide();
    var $err = $(".payment-errors");

    $('#payment-form').submit(function (e)
    {   
        e.preventDefault();

        // acceptances
        var error_msg = '';
        var terms_check = $('#fullstripe_accept_terms');
        var adult_check = $('#fullstripe_adult');
        if (terms_check.is(':checked')){
          if (adult_check.is(':checked') || adult_check.length == 0){
            $("#showLoading").show();

            var $form = $(this);

            // Disable the submit button
            $form.find('button').prop('disabled', true);

            Stripe.createToken($form, stripeResponseHandler);
          }else{
            error_msg = locale.ERROR_ADULT[config.LANGUAGE];
          }
        }else{
          error_msg = locale.ERROR_TERMS[config.LANGUAGE];
        }
        if (error_msg == ''){
          $err.removeClass('alert alert-error');
        }else{
          $err.addClass('alert alert-error');
        }
        $err.html(error_msg);

        //location.hash = '#content';
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
                        // remove errors
                        $('.control-group').removeClass('error');
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $err.addClass('alert alert-error');
                        var messages = "";
                        for (var i in data.error_messages){
                          var obj = data.error_messages[i];
                          messages += obj.text + '<br>';
                          $('[name^="' + obj.input + '"]').closest('.control-group').addClass('error');
                        }
                        $err.html(messages);
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
    $('[name="fullstripe_plan"]').change(function ()
    {
        var plan = $(this).val();
        var setupFee = parseInt($("#fullstripe_setupFee").val());
        var count = parseInt($(this).attr("data-interval-count"));
        var amount = parseFloat($(this).attr('data-amount') / 100);
        var cur = $(this).attr("data-currency");
        var interval = $(this).attr('data-interval');
        var str = amount + cur + " " + locale.PER[config.LANGUAGE] + " ";
        if (count > 1) str += count + " ";
        str += locale[interval.toUpperCase()][config.LANGUAGE];
        //if (count > 1) str += "s";

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
    });

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

    var _getSpanishProvinces = function (){
      return [
              "Álava/Araba",
              "Albacete",
              "Alicante",
              "Almería",
              "Asturias/Asturies",
              "Ávila",
              "Badajoz",
              "Barcelona",
              "Burgos",
              "Cáceres",
              "Cádiz",
              "Cantabria",
              "Castellón/Castelló",
              "Ceuta",
              "Ciudad Real",
              "Córdoba",
              "Cuenca",
              "Gerona/Girona",
              "Granada",
              "Guadalajara",
              "Guipúzcoa/Gipuzkoa",
              "Huelva",
              "Huesca",
              "Islas Baleares/Illes Balears",
              "Jaén",
              "La Coruña/A Coruña",
              "La Rioja",
              "Las Palmas",
              "León",
              "Lérida/Lleida",
              "Lugo",
              "Madrid",
              "Málaga",
              "Melilla",
              "Murcia",
              "Navarra/Nafarroa",
              "Orense/Ourense",
              "Palencia",
              "Pontevedra",
              "Salamanca",
              "Santa Cruz de Tenerife",
              "Segovia",
              "Sevilla",
              "Soria",
              "Tarragona",
              "Teruel",
              "Toledo",
              "Valencia/València",
              "Valladolid",
              "Vizcaya/Bizkaia",
              "Zamora",
              "Zaragoza"
          ];
    };
    // Province select list: This is only valid for Spain
    var province_input = $('#fullstripe_address_state');
    if (province_input.length){
      var provinces = _getSpanishProvinces();
      for (var i in provinces){
        var province_name = provinces[i];
        var selected = '';
        if (province_name == 'Barcelona') selected = ' selected="selected"';
        province_input.append('<option value="'+province_name+'"'+selected+'>'+province_name+'</option>');
      }
    }

    // document type selector
    var doctype_selector = $('#doctype-selector');
    if (doctype_selector.length){
      var doctype_values = $('#doctype-values');
      doctype_values.find('> div').hide();
      doctype_selector.find('input[name="fullstripe_doctype"]').change(function(e){
        var value = $(this).val();
        doctype_values.find('> div').hide();
        doctype_values.find('[data-type="'+value+'"]').fadeIn(300);
      });
    }
    
});