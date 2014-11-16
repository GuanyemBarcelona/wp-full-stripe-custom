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
  PER: {
    ca: "/",
    es: "/"
  },
  MONTH: {
    ca: "mes",
    es: "mes"
  },
  PAYMENT_METHOD_CREDIT_CARD: {
    ca: "Targeta de crèdit",
    es: "Tarjeta de crédito"
  },
  PAYMENT_METHOD_SPANISH_BANK_ACCOUNT: {
    ca: "Domiciliació compte bancari (CCC)",
    es: "Domiciliación cuenta bancaria (CCC)"
  },
  PAYMENT_METHOD_INTERNATIONAL_BANK_ACCOUNT: {
    ca: "Domiciliació compte bancari (IBAN)",
    es: "Domiciliación cuenta bancaria (IBAN)"
  }
};
var stripe_locale = {
  "This card number looks invalid": {
    ca: "Aquest número de targeta sembla erroni",
    es: "Este número de tarjeta parece erróneo"
  },
  "Your card number is incorrect.": {
    ca: "El número de la targeta és incorrecte",
    es: "El número de la tarjeta es incorrecto"
  },
  "Your card's expiration year is invalid.": {
    ca: "L'any de caducitat de la targeta és incorrecte",
    es: "El año de caducidad de la tarjeta es incorrecto"
  },
  "Your card's expiration month is invalid.": {
    ca: "El mes de caducitat de la targeta és incorrecte",
    es: "El mes de caducidad de la tarjeta es incorrecto"
  },
  "Your card's security code is invalid.": {
    ca: "El codi de seguretat de la targeta és incorrecte",
    es: "El código de seguridad de la tarjeta es incorrecto"
  },
};
var config = {
  LANGUAGE: 'ca'
};

jQuery(document).ready(function ($)
{
    config.LANGUAGE = $('html').attr('lang');
    if (config.LANGUAGE == 'es-ES') config.LANGUAGE = 'es';

    // Stripe strings localization
    var _get_localized_stripe_string = function(str){
      if (typeof stripe_locale[str] !== 'undefined'){
        return stripe_locale[str][config.LANGUAGE];
      }
      return str;
    };

    $("#showLoading").hide();
    $("#showLoadingC").hide();
    var $err = $(".payment-errors");

    $('#payment-form').submit(function (e)
    {   
      e.preventDefault();

      // acceptances
      var error_msg = '';
      var terms_check = $('#fullstripe_accept_terms');
      if (terms_check.is(':checked')){
        $("#showLoading").show();

        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        // payment method
        $pay_method = $form.find('[name="fullstripe_pay_method"]');
        if ($pay_method.val() == 'credit'){
          Stripe.createToken($form, stripeResponseHandler);
        }else{
          // No Stripe payment Override
          bankStartTransfer();
        }
      }else{
        error_msg = locale.ERROR_TERMS[config.LANGUAGE];
      }
      if (error_msg == ''){
        $err.removeClass('alert alert-error');
      }else{
        $.scrollTo('#content', 800);
        $err.addClass('alert alert-error');
      }
      $err.html(error_msg);
    });

    var bankStartTransfer = function(){
      ajaxPayment();
    };

    var stripeResponseHandler = function (status, response)
    {
        var $form = $('#payment-form');

        $.scrollTo('#content', 800);

        if (response.error)
        {
            // Show the errors
            $err.addClass('alert alert-error');
            $err.html(_get_localized_stripe_string(response.error.message));
            $err.fadeIn(500).fadeOut(500).fadeIn(500);
            $form.find('button').prop('disabled', false);
            $("#showLoading").hide();
        }
        else
        {
            // token contains id, last4, and card type
            var token = response.id;
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

            ajaxPayment();
        }
    };

    //post payment via ajax
    var ajaxPayment = function(){
      var $form = $('#payment-form');

      $.ajax({
          type: "POST",
          url: ajaxurl,
          data: $form.serialize(),
          cache: false,
          dataType: "json",
          success: function (data)
          {   
              $.scrollTo('#content', 800);

              $("#showLoading").hide();
              // remove fields errors
              $('.control-group').removeClass('error');
              // re-enable the submit button
              $form.find('button').prop('disabled', false);

              if (data.success)
              {
                  //clear form fields
                  $form.find('input:text, input:password').val('');
                  //inform user of success
                  $err.addClass('alert alert-success');
                  $err.html(data.msg);
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
                  var messages = "";
                  if (data.msg) messages = data.msg + '<br>';
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

        $.scrollTo('#content', 800);

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
                    $.scrollTo('#content', 800);

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
                    $.scrollTo('#content', 800);

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

    var _getPaymentMethods = function(){
      return {
        'credit': locale.PAYMENT_METHOD_CREDIT_CARD[config.LANGUAGE],
        /*'spanishaccount': locale.PAYMENT_METHOD_SPANISH_BANK_ACCOUNT[config.LANGUAGE],*/
        'internationalaccount': locale.PAYMENT_METHOD_INTERNATIONAL_BANK_ACCOUNT[config.LANGUAGE]
      };
    };

    // Payments method select
    var fillPaymentsMehodSelect = function(){
      var select = $('[name="fullstripe_pay_method"]');
      if (select.length){
        var methods = _getPaymentMethods();
        var general_conditions = $('.general-conditions-wrapper .stripe-legal');
        for (var i in methods){
          var method_name = methods[i];
          var selected = '';
          if (i == 'credit'){
            selected = ' selected="selected"';
          }else{
            $('.payment-method[data-type="'+i+'"]').hide();
          }
          select.append('<option value="'+i+'"'+selected+'>'+method_name+'</option>');
        }
        select.change(function (){
          var method_value = $(this).val();
          if (method_value == 'credit'){
            general_conditions.show();
          }else{
            general_conditions.hide();
          }
          $('.payment-method').each(function(i){
            var type = $(this).attr('data-type');
            if (type == method_value){
              $(this).fadeIn(300);
            }else{
              $(this).hide();
            }
          });
        });
      }
    };
    fillPaymentsMehodSelect();

    var _getSpanishProvinces = function(){
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

    // Spanish Provinces select list
    var fillRegionSelectWithSpanishProvinces = function(){
      var input = $('[name="fullstripe_address_state"]');
      if (input.length){
        if (input.is('input')) input.replaceWith('<select name="fullstripe_address_state" id="fullstripe_address_state"></select>');
        var provinces = _getSpanishProvinces();
        input = $('[name="fullstripe_address_state"]'); // redeclaring after the replace
        for (var i in provinces){
          var province_name = provinces[i];
          var selected = '';
          if (province_name == 'Barcelona') selected = ' selected="selected"';
          input.append('<option value="'+province_name+'"'+selected+'>'+province_name+'</option>');
        }
      }
    };

    var changeRegionSelectToInput = function(){
      var input = $('[name="fullstripe_address_state"]');
      if (input.length){
        if (input.is('select')) input.replaceWith('<input type="text" name="fullstripe_address_state" id="fullstripe_address_state">');
      }
    };

    var _getWorldCountries = function(){
      return {"AF":"Afghanistan","AL":"Albania","DZ":"Algeria","AS":"American Samoa","AD":"Andorra","AO":"Angola","AI":"Anguilla","AQ":"Antarctica","AG":"Antigua and Barbuda","AR":"Argentina","AM":"Armenia","AW":"Aruba","AU":"Australia","AT":"Austria","AZ":"Azerbaijan","BS":"Bahamas","BH":"Bahrain","BD":"Bangladesh","BB":"Barbados","BY":"Belarus","BE":"Belgium","BZ":"Belize","BJ":"Benin","BM":"Bermuda","BT":"Bhutan","BO":"Bolivia","BA":"Bosnia and Herzegovina","BW":"Botswana","BV":"Bouvet Island","BR":"Brazil","BQ":"British Antarctic Territory","IO":"British Indian Ocean Territory","VG":"British Virgin Islands","BN":"Brunei","BG":"Bulgaria","BF":"Burkina Faso","BI":"Burundi","KH":"Cambodia","CM":"Cameroon","CA":"Canada","CT":"Canton and Enderbury Islands","CV":"Cape Verde","KY":"Cayman Islands","CF":"Central African Republic","TD":"Chad","CL":"Chile","CN":"China","CX":"Christmas Island","CC":"Cocos [Keeling] Islands","CO":"Colombia","KM":"Comoros","CG":"Congo - Brazzaville","CD":"Congo - Kinshasa","CK":"Cook Islands","CR":"Costa Rica","HR":"Croatia","CU":"Cuba","CY":"Cyprus","CZ":"Czech Republic","CI":"Côte d’Ivoire","DK":"Denmark","DJ":"Djibouti","DM":"Dominica","DO":"Dominican Republic","NQ":"Dronning Maud Land","DD":"East Germany","EC":"Ecuador","EG":"Egypt","SV":"El Salvador","GQ":"Equatorial Guinea","ER":"Eritrea","EE":"Estonia","ET":"Ethiopia","FK":"Falkland Islands","FO":"Faroe Islands","FJ":"Fiji","FI":"Finland","FR":"France","GF":"French Guiana","PF":"French Polynesia","TF":"French Southern Territories","FQ":"French Southern and Antarctic Territories","GA":"Gabon","GM":"Gambia","GE":"Georgia","DE":"Germany","GH":"Ghana","GI":"Gibraltar","GR":"Greece","GL":"Greenland","GD":"Grenada","GP":"Guadeloupe","GU":"Guam","GT":"Guatemala","GG":"Guernsey","GN":"Guinea","GW":"Guinea-Bissau","GY":"Guyana","HT":"Haiti","HM":"Heard Island and McDonald Islands","HN":"Honduras","HK":"Hong Kong SAR China","HU":"Hungary","IS":"Iceland","IN":"India","ID":"Indonesia","IR":"Iran","IQ":"Iraq","IE":"Ireland","IM":"Isle of Man","IL":"Israel","IT":"Italy","JM":"Jamaica","JP":"Japan","JE":"Jersey","JT":"Johnston Island","JO":"Jordan","KZ":"Kazakhstan","KE":"Kenya","KI":"Kiribati","KW":"Kuwait","KG":"Kyrgyzstan","LA":"Laos","LV":"Latvia","LB":"Lebanon","LS":"Lesotho","LR":"Liberia","LY":"Libya","LI":"Liechtenstein","LT":"Lithuania","LU":"Luxembourg","MO":"Macau SAR China","MK":"Macedonia","MG":"Madagascar","MW":"Malawi","MY":"Malaysia","MV":"Maldives","ML":"Mali","MT":"Malta","MH":"Marshall Islands","MQ":"Martinique","MR":"Mauritania","MU":"Mauritius","YT":"Mayotte","FX":"Metropolitan France","MX":"Mexico","FM":"Micronesia","MI":"Midway Islands","MD":"Moldova","MC":"Monaco","MN":"Mongolia","ME":"Montenegro","MS":"Montserrat","MA":"Morocco","MZ":"Mozambique","MM":"Myanmar [Burma]","NA":"Namibia","NR":"Nauru","NP":"Nepal","NL":"Netherlands","AN":"Netherlands Antilles","NT":"Neutral Zone","NC":"New Caledonia","NZ":"New Zealand","NI":"Nicaragua","NE":"Niger","NG":"Nigeria","NU":"Niue","NF":"Norfolk Island","KP":"North Korea","VD":"North Vietnam","MP":"Northern Mariana Islands","NO":"Norway","OM":"Oman","PC":"Pacific Islands Trust Territory","PK":"Pakistan","PW":"Palau","PS":"Palestinian Territories","PA":"Panama","PZ":"Panama Canal Zone","PG":"Papua New Guinea","PY":"Paraguay","YD":"People's Democratic Republic of Yemen","PE":"Peru","PH":"Philippines","PN":"Pitcairn Islands","PL":"Poland","PT":"Portugal","PR":"Puerto Rico","QA":"Qatar","RO":"Romania","RU":"Russia","RW":"Rwanda","RE":"Réunion","BL":"Saint Barthélemy","SH":"Saint Helena","KN":"Saint Kitts and Nevis","LC":"Saint Lucia","MF":"Saint Martin","PM":"Saint Pierre and Miquelon","VC":"Saint Vincent and the Grenadines","WS":"Samoa","SM":"San Marino","SA":"Saudi Arabia","SN":"Senegal","RS":"Serbia","CS":"Serbia and Montenegro","SC":"Seychelles","SL":"Sierra Leone","SG":"Singapore","SK":"Slovakia","SI":"Slovenia","SB":"Solomon Islands","SO":"Somalia","ZA":"South Africa","GS":"South Georgia and the South Sandwich Islands","KR":"South Korea","ES":"Spain (España)","LK":"Sri Lanka","SD":"Sudan","SR":"Suriname","SJ":"Svalbard and Jan Mayen","SZ":"Swaziland","SE":"Sweden","CH":"Switzerland","SY":"Syria","ST":"São Tomé and Príncipe","TW":"Taiwan","TJ":"Tajikistan","TZ":"Tanzania","TH":"Thailand","TL":"Timor-Leste","TG":"Togo","TK":"Tokelau","TO":"Tonga","TT":"Trinidad and Tobago","TN":"Tunisia","TR":"Turkey","TM":"Turkmenistan","TC":"Turks and Caicos Islands","TV":"Tuvalu","UM":"U.S. Minor Outlying Islands","PU":"U.S. Miscellaneous Pacific Islands","VI":"U.S. Virgin Islands","UG":"Uganda","UA":"Ukraine","SU":"Union of Soviet Socialist Republics","AE":"United Arab Emirates","GB":"United Kingdom","US":"United States","ZZ":"Unknown or Invalid Region","UY":"Uruguay","UZ":"Uzbekistan","VU":"Vanuatu","VA":"Vatican City","VE":"Venezuela","VN":"Vietnam","WK":"Wake Island","WF":"Wallis and Futuna","EH":"Western Sahara","YE":"Yemen","ZM":"Zambia","ZW":"Zimbabwe"};
    };

    // World Countries select list
    var fillWorldCountriesSelect = function(){
      var spain_code = 'ES';
      var input = $('[name="fullstripe_address_country"]');
      if (input.length){
        var countries = _getWorldCountries();
        for (var i in countries){
          var country_name = countries[i];
          var selected = '';
          if (i == spain_code) selected = ' selected="selected"';
          input.append('<option value="'+i+'"'+selected+'>'+country_name+'</option>');
        }

        input.change(function (){
          var country_code = $(this).val();
          if (country_code == spain_code){
            fillRegionSelectWithSpanishProvinces();
          }else{
            changeRegionSelectToInput();
          }
        });

        fillRegionSelectWithSpanishProvinces();
      }
    };
    fillWorldCountriesSelect();

    // document type selector
    var doctype_selector = $('#doctype-selector');
    if (doctype_selector.length){
      var doctype_values = $('#doctype-values');
      doctype_values.find('> div').hide();
      doctype_selector.find('[name="fullstripe_doctype"]').change(function(e){
        var value = $(this).val();
        doctype_values.find('> div').hide();
        doctype_values.find('[data-type="'+value+'"]').fadeIn(300);
      });
    }
});