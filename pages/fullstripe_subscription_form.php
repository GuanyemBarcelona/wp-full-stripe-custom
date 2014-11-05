<form action="" method="POST" id="payment-form">
    <input type="hidden" name="action" value="wp_full_stripe_subscription_charge"/>
    <input type="hidden" name="formName" value="<?php echo $formData->name; ?>"/>
    <input type="hidden" name="formDoRedirect" value="<?php echo $formData->redirectOnSuccess; ?>"/>
    <input type="hidden" name="formRedirectPostID" value="<?php echo $formData->redirectPostID; ?>"/>
    <input type="hidden" name="showAddress" value="<?php echo $formData->showAddress; ?>"/>
    <input type="hidden" name="fullstripe_setupFee" id="fullstripe_setupFee" value="<?php echo $formData->setupFee; ?>"/>
    
    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." id="showLoading"/>
    <p class="payment-errors"></p>
    
    <fieldset>
      <h3 class="legend"><?php _e("Personal data", "wp-full-stripe"); ?></h3>
      <div class="row-fluid">
        <!-- First name -->
        <div class="control-group span6">
            <label class="control-label fullstripe-form-label"><?php _e("First name", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text" class="fullstripe-form-input" name="fullstripe_firstname" id="fullstripe_firstname">
            </div>
        </div>
        <!-- Last name -->
        <div class="control-group span6">
            <label class="control-label fullstripe-form-label"><?php _e("Last name", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text" class="fullstripe-form-input" name="fullstripe_lastname" id="fullstripe_lastname">
            </div>
        </div>
      </div>

      <div class="row-fluid">
        <!-- Type of document -->
        <div id="doctype-selector" class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php _e("Type of document", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <label class="inline">
              <input type="radio" name="fullstripe_doctype" id="fullstripe_doctype_1" value="dni"> DNI/NIE
            </label>
            <label class="inline">
              <input type="radio" name="fullstripe_doctype" id="fullstripe_doctype_2" value="passport"> <?php _e("Passport", "wp-full-stripe"); ?>
            </label>
        </div>
        <!-- Document ID -->
        <div id="doctype-values" class="control-group span3">
            <div data-type="dni">
              <label class="control-label fullstripe-form-label">DNI/NIE <span class="required-field">*</span></label>
              <div class="controls">
                <input type="text" class="fullstripe-form-input" name="fullstripe_doc_dni" id="fullstripe_doc_dni">
              </div>
            </div>
            <div data-type="passport">
              <label class="control-label fullstripe-form-label"><?php _e("Passport", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
              <div class="controls">
                <input type="text" class="fullstripe-form-input" name="fullstripe_doc_passport" id="fullstripe_doc_passport">
              </div>
            </div>
        </div>
        <!-- Date of birth -->
        <div class="control-group span4">
            <label class="control-label fullstripe-form-label"><?php _e("Date of birth", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <select name="fullstripe_birthdate[]" class="inline">
                  <option value=""><?php _e("Day", "wp-full-stripe"); ?></option>
                  <?php for ($i = 1; $i <= 31; $i++){ ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                  <?php } ?>
                </select>
                <select name="fullstripe_birthdate[]" class="inline">
                  <option value=""><?php _e("Month", "wp-full-stripe"); ?></option>
                  <?php for ($i = 1; $i <= 12; $i++){ ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                  <?php } ?>
                </select>
                <select name="fullstripe_birthdate[]" class="inline">
                  <option value=""><?php _e("Year", "wp-full-stripe"); ?></option>
                  <?php for ($i = 1910; $i <= date('Y'); $i++){ ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                  <?php } ?>
                </select>
            </div>
        </div>
        <!-- Telephone -->
        <div class="control-group span2">
            <label class="control-label fullstripe-form-label"><?php _e("Telephone", "wp-full-stripe"); ?></label>
            <div class="controls">
                <input type="text" class="fullstripe-form-input" name="fullstripe_telephone" id="fullstripe_telephone">
            </div>
        </div>
      </div>

      <div class="row-fluid">
          <!-- Email -->
          <div class="control-group span6">
              <label class="control-label fullstripe-form-label"><?php _e("Email", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
              <div class="controls">
                  <input type="text" class="fullstripe-form-input" name="fullstripe_email" id="fullstripe_email">
              </div>
          </div>
          <!-- Email confirmation -->
          <div class="control-group span6">
              <label class="control-label fullstripe-form-label"><?php _e("Confirm email", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
              <div class="controls">
                  <input type="text" class="fullstripe-form-input" name="fullstripe_email2" id="fullstripe_email2">
              </div>
          </div>
      </div>

      <?php if ( $formData->showAddress == 1 ): ?>
      <!-- Address -->
      <div class="control-group">
          <label class="control-label fullstripe-form-label"><?php _e("Billing Address Street", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
          <div class="controls">
              <input type="text"  name="fullstripe_address_line1" id="fullstripe_address_line1" class="fullstripe-form-input"><br/>
          </div>
      </div>
      <div class="row-fluid">
        <div class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php _e("Country", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
              <select name="fullstripe_address_country" id="fullstripe_address_country">
                
              </select>
            </div>
        </div>
        <div class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php echo $localeState; ?></label>
            <div class="controls">
              <select name="fullstripe_address_state" id="fullstripe_address_state">
                
              </select>
            </div>
        </div>
        <div class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php _e("City", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text"  name="fullstripe_address_city" id="fullstripe_address_city" class="fullstripe-form-input"><br/>
            </div>
        </div>
        <div class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php echo $localeZip; ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text" style="width: 60px;"  name="fullstripe_address_zip" id="fullstripe_address_zip" class="fullstripe-form-input"><br/>
            </div>
        </div>
      </div> 
      <?php endif; ?>
    </fieldset>

    <fieldset>
      <h3 class="legend"><?php _e("Donation", "wp-full-stripe"); ?></h3>
      <!-- Payment method -->
      <div class="control-group">
          <label class="control-label fullstripe-form-label"><?php _e("Paying method", "wp-full-stripe"); ?></label>
          <div class="controls">
            <select name="fullstripe_pay_method" id="fullstripe_pay_method" readonly>
              <option value="card"><?php _e("Credit card", "wp-full-stripe"); ?></option>
            </select>
            <span class="help-block"><?php _e("For now, you may only pay through our web by Credit card. If you'd like to pay through bank transfer, you can do so manually through the link on the top. Sorry for the inconvenience.", "wp-full-stripe"); ?></span>
          </div>
      </div>
      <!-- Subscription Plan -->
      <div class="control-group">
          <label class="control-label fullstripe-form-label"><?php _e("Donation per month", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
          <div class="controls">
              <?php foreach ($plans as $plan): ?>
              <div class="radio">
                <label>
                  <input type="radio" name="fullstripe_plan" id="fullstripe_plan_<?php echo $plan->id; ?>" value="<?php echo $plan->id; ?>"  
                                data-amount="<?php echo $plan->amount;?>"
                                data-interval="<?php echo $plan->interval;?>"
                                data-interval-count="<?php echo $plan->interval_count;?>"
                                data-currency="<?php echo $currencySymbol; ?>">
                  <?php echo $plan->name; ?>
                </label>
              </div>
              <?php endforeach; ?>
          </div>
      </div>
      <!-- Card Name -->
      <div class="control-group">
          <label class="control-label fullstripe-form-label"><?php _e("Card Holder's Name", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
          <div class="controls">
              <input type="text" autocomplete="off" placeholder="Full name" class="input-xlarge fullstripe-form-input" name="fullstripe_name" id="fullstripe_name">
          </div>
      </div>
      <div class="row-fluid">
        <!-- Card Number -->
        <div class="control-group span4">
            <label class="control-label fullstripe-form-label"><?php _e("Card Number", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text" autocomplete="off" placeholder="4242424242424242" class="input-xlarge fullstripe-form-input" size="20" data-stripe="number">
            </div>
        </div>
        <!-- Expiry-->
        <div class="control-group span3">
            <label class="control-label fullstripe-form-label"><?php _e("Card Expiry Date", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="text" style="width: 60px;" size="2" placeholder="10" data-stripe="exp-month" class="fullstripe-form-input"/>
                <span> / </span>
                <input type="text" style="width: 60px;" size="4" placeholder="2016" data-stripe="exp-year" class="fullstripe-form-input"/>
            </div>
        </div>
        <!-- CVV -->
        <div class="control-group span5">
            <label class="control-label fullstripe-form-label"><?php _e("Card CVV", "wp-full-stripe"); ?> <span class="required-field">*</span></label>
            <div class="controls">
                <input type="password" autocomplete="off" placeholder="123" class="input-mini fullstripe-form-input" size="4" data-stripe="cvc"/>
                <span class="help-block"><?php _e("The CVV is a verification number that is generally 3 digit long and is printed on the back of your card.", "wp-full-stripe"); ?></span>
            </div>
        </div>
        <?php if ( $formData->showCouponInput == 1 ): ?>
        <div class="control-group">
            <label class="control-label fullstripe-form-label"><?php _e("Coupon Code", "wp-full-stripe"); ?></label>
            <div class="controls">
                <input type="text" class="input-mini fullstripe-form-input" name="fullstripe_coupon_input" id="fullstripe_coupon_input">
                <button id="fullstripe_check_coupon_code">Apply</button>
                <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." id="showLoadingC"/>
            </div>
        </div>
        <?php endif; ?>
      </div>
    </fieldset>

    <h3 class="total"><?php _e("Total", "wp-full-stripe"); ?>: <span class="value fullstripe_plan_details"></span></h3>

    <!-- General Conditions -->
    <div class="control-group">
      <div class="general-conditions-wrapper">
        <?php if (ICL_LANGUAGE_CODE=='ca'){ ?>
        <h3>CONDICIONS GENERALS</h3>
        <h4>RELACIÓ ENTRE GUANYEM BARCELONA I EL COL·LABORADOR</h4>

        <p>LA REALITZACIÓ D’APORTACIONS ECONÒMIQUES NO IMPLICA LA VINCULACIÓ DEL COL·LABORADOR AMB GUANYEM BARCELONA NI LI CONFEREIX LA CONDICIÓ LEGAL DE SOCI, AFILIAT O SIMPATITZANT, NI LI ATORGA DRETS DAVANT L'ASSOCIACIÓ NI EL PARTIT, LA INSCRIPCIÓ DEL QUAL ESTÀ PENDENT D'APROVACIÓ, NI QUEDA OBLIGAT PELS SEUS ESTATUTS I RESTA DE NORMES INTERNES. D’ACORD AMB EL QUE S'ESTABLEIX LA LLEI 4/2008, DEL 24 D'ABRIL, DEL LLIBRE TERCER DEL CODI CIVIL DE CATALUNYA, RELATIU A LES PERSONES JURÍDIQUES; LA LLEI ORGÀNICA 1/2002, DEL 22 DE MARÇ, REGULADORA DEL DRET D'ASSOCIACIÓ; LA LLEI ORGÀNICA 8/2007, DE 4 DE JULIOL, DE FINANCIACIÓ DE PARTITS POLÍTICS, EL VOSTRE DONATIU TINDRÀ LA CONSIDERACIÓ D’“APORTACIÓ PRIVADA”, DIFERENT DE LES QUOTES DE SOCIS, AFILIATS O SIMPATITZANTS.</p>

        <h4>PROTECCIÓ DE DADES DE CARÀCTER PERSONAL</h4>

        <p>GUANYEM BARCELONA , D’ACORD AMB LA LLEI ORGÀNICA 15/1999, DE PROTECCIÓ DE DADES DE CARÀCTER PERSONAL, US INFORMA QUE LES VOSTRES DADES SERAN INCORPORADES AL FITXER “APORTACIONS”, DEL QUAL ÉS RESPONSABLE GUANYEM BARCELONA, QUE LES EMPRARÀ PER TRAMITAR LA VOSTRA COL·LABORACIÓ, GESTIONAR ELS CÀRRECS BANCARIS I COMPLIR AMB LA NORMATIVA DE FINANÇAMENT D'ASSOCIACIONS I DE PARTITS POLÍTICS, AIXÍ COM AMB FINALITATS ESTADÍSTIQUES I FER-VOS ARRIBAR PERIÒDICAMENT INFORMACIÓ DE LES SEVES ACTIVITATS. VOS CONCEDIU EL CONSENTIMENT EXPRÉS PERQUÈ AQUESTES DADES PASSIN A FORMAR PART DE GUANYEM BARCELONA, TANT DE L'ASSOCIACIÓ COM DEL PARTIT POLÍTIC, LA INSCRIPCIÓ DEL QUAL ESTÀ PENDENT D'APROVACIÓ. GUANYEM BARCELONA LI INFORMA QUE LES SEVES DADES TAMBÉ SERAN TRANSFERIDES I EMMAGATZEMADES PER STRIPE, Inc. NOMÉS AMB LA FINALITAT DE PROCEDIR A L'ACTUAL TRANSACCIÓ DINERÀRIA. SE LI INFORMA QUE STRIPE, Inc. POTSER EMMAGATZEMI I PROCESSI LES SEVES DADES FORA DEL TERRITORI DE LA UE, SEMPRE D'ACORD AMB LA SEVA CERTIFICACIÓ SAFE HARBOR/PORT SEGUR ACREDITADA PEL <a href="http://export.gov/safeharbor/" rel="external">U.S. DEPARTMENT OF COMMERCE</a>. PER EXERCIR ELS VOSTRES DRETS D’ACCÉS, RECTIFICACIÓ, CANCEL·LACIÓ O OPOSICIÓ, COM ARA CANCEL·LAR LA SUBSCRIPCIÓ, MODIFICAR LES DADES BANCÀRIES O L’IMPORT I FREQÜÈNCIA DE LA VOSTRA APORTACIÓ, PODEU ENVIAR UN CORREU ELECTRÒNIC A TRESORERIA@GUANYEMBARCELONA.CAT, INDICANT LA REFERÈNCIA “MODIFICACIÓ DE L’APORTACIÓ” O, PER CORREU POSTAL A LA SEU SOCIAL SÍTIA AL CARRER CASTILLEJOS, 233, BAIXOS, DE BARCELONA, ADJUNTANT CÒPIA DEL VOSTRE DOCUMENT NACIONAL D’IDENTITAT.</p>

        <h4>CONSENTIMENT DE L'ORDRE DE DOMICILIACIÓ I CÀRRECS EN TARGETA DE CRÈDIT O DÈBIT</h4>

        <p>EN PRÉMER EL BOTÓ “ENVIA” VOS AUTORITZEU A GUANYEM BARCELONA I A LA VOSTRA ENTITAT BANCÀRIA A EFECTUAR LES OPERACIONS DE COBRAMENT MITJANÇANT CÀRRECS AL COMPTE BANCARI O A LA TARGETA FINANCERA QUE ENS HEU FACILITAT, PER L’IMPORT I AMB LA FREQÜÈNCIA QUE ENS HEU INDICAT AL FORMULARI. ELS CÀRRECS DOMICILIATS I ELS CÀRRECS A LA TARGETA FINANCERA ES REALITZARAN ENTRE ELS DIES 1 I 15 DE CADA MES, TRIMESTRE O ANY EN FUNCIÓ DE LA FREQÜÈNCIA DE PAGAMENT QUE HEU SELECCIONAT.</p>
        <?php }elseif(ICL_LANGUAGE_CODE=='es'){ ?>
        <h3>CONDICIONES GENERALES</h3>
        <h4>RELACIÓN ENTRE GUANYEM BARCELONA Y EL COLABORADOR.</h4>

        <p>LA REALIZACIÓN DE APORTACIONES ECONÓMICAS NO IMPLICA LA VINCULACIÓN DEL COLABORADOR CON GUANYEM BARCELONA NI LE CONFIERE LA CONDICIÓN LEGAL DE SOCIO, AFILIADO O SIMPATIZANTE, NI DE DERECHOS FRENTE A LA ASOCIACIÓN NI AL PARTIDO CUYA INSCRIPCIÓN SE ENCUENTRA PENDIENTE DE APROBACIÓN, NI QUEDA OBLIGADO POR SUS ESTATUTOS Y DEMÁS NORMAS INTERNAS. CONFORME A LO DISPUESTO EN LA LEY 4/2008, DE 24 DE ABRIL, DEL LIBRO TERCERO DEL CÓDIGO CIVIL DE CATALUÑA, RELATIVO A LAS PERSONAS JURÍDICAS; LA LEY ORGÁNICA 1/2002, DE 22 DE MARZO, REGULADORA DEL DERECHO DE  ASOCIACIÓN,  LA LEY ORGÁNICA 8/2007, DE 4 DE JULIO, DE FINANCIACIÓN DE PARTIDOS POLÍTICOS SU DONATIVO TENDRÁ LA CONSIDERACIÓN DE “APORTACIÓN PRIVADA”, DISTINTA DE LAS CUOTAS DE SOCIOS, AFILIADOS O SIMPATIZANTES.</p>

        <h4>PROTECCIÓN DE DATOS DE CARÁCTER PERSONAL.</h4>

        <p>GUANYEM BARCELONA, DE ACUERDO CON LA LEY ORGÁNICA 15/1999 DE PROTECCIÓN DE DATOS DE CARÁCTER PERSONAL, LE INFORMA DE QUE SUS DATOS SERÁN INCORPORADOS AL FICHERO “APORTACIONES”, DEL QUE ES RESPONSABLE GUANYEM BARCELONA, QUE LOS UTILIZARÁ PARA TRAMITAR SU COLABORACIÓN, GESTIONAR LOS ADEUDOS BANCARIOS, CUMPLIR CON LA NORMATIVA DE FINANCIACIÓN DE ASOCIACIONES Y PARTIDOS POLÍTICOS, PARA FINES ESTADÍSTICOS Y HACERLE LLEGAR PERIÓDICAMENTE INFORMACIÓN SOBRE SUS ACTIVIDADES. GUANYEM BARCELONA LE INFORMA QUE SUS DATOS TAMBIÉN SERÁN TRANSFERIDOS Y ALMACENADOS POR STRIPE, Inc. CON EL SÓLO FIN DE PROCEDER A ESTA TRANSACCIÓN DINERARIA. SE LE INFORMA QUE STRIPE, Inc. PUEDE QUE ALMACENE Y PROCESE SUS DATOS FUERA DEL TERRITORIO UE, SIEMPRE DE ACUERDO A SU CERTIFICADO SAFE HARBOR/PUERTO SEGURO EMITIDO POR EL <a href="http://export.gov/safeharbor/" rel="external">U.S. DEPARTMENT OF COMMERCE</a>. USTED CONCEDE SU CONSENTIMIENTO EXPRESO PARA QUE DICHOS DATOS PASEN A FORMAR PARTE DE GUANYEM BARCELONA, TANTO EN LA ASOCIACIÓN COMO EN EL PARTIDO POLÍTICO  CUYA INSCRIPCIÓN SE ENCUENTRA PENDIENTE DE APROBACIÓN. SUS DPARA EJERCER SUS DERECHOS DE ACCESO, RECTIFICACIÓN, CANCELACIÓN U OPOSICIÓN, COMO CANCELAR SU SUSCRIPCIÓN, MODIFICAR LOS DATOS BANCARIOS O EL IMPORTE Y FRECUENCIA DE SU APORTACIÓN, PUEDE MANDAR UN CORREO ELECTRÓNICO A TRESORERIA@GUANYEMBARCELONA.CAT INDICANDO LA REFERENCIA “MODIFICACIÓN DE LA APORTACIÓN” O, POR CORREO POSTAL EN LA SEDE SOCIAL SITA EN LA CALLE CASTILLEJOS, 233, BAJOS, DE BARCELONA, ADJUNTANDO COPIA DE SU DOCUMENTO NACIONAL DE IDENTIDAD.</p>

        <h4>CONSENTIMIENTO ORDEN DE DOMICILIACIÓN  Y CARGOS EN TARJETA DE CRÉDITO O DÉBITO.</h4>

        <p>AL PULSAR EL BOTÓN “ENVÍA” USTED AUTORIZA A GUANYEM BARCELONA Y A SU ENTIDAD BANCARIA A EFECTUAR LAS OPERACIONES DE COBRO MEDIANTE ADEUDOS Y CARGOS EN LA CUENTA BANCARIA O EN LA TARJETA FINANCIERA QUE USTED NOS HA FACILITADO, POR EL IMPORTE Y CON LA FRECUENCIA QUE NOS HA INDICADO EN EL FORMULARIO. LOS ADEUDOS DOMICILIADOS Y LOS CARGOS EN TARJETA SE REALIZARÁN ENTRE EL DÍA 1 Y 15 DE CADA MES, TRIMESTRE O AÑO DEPENDIENDO DE LA FRECUENCIA DE PAGO QUE HA ELEGIDO.</p>
        <?php } ?>
      </div>
    </div>
    <!-- Accept conditions -->
    <div class="control-group">
      <label class="checkbox">
        <input type="checkbox" name="fullstripe_accept_terms" id="fullstripe_accept_terms" value="1">
        <?php _e("Agree to General Terms", "wp-full-stripe"); ?>
      </label>
    </div>
    
    <!-- Submit -->
    <div class="control-group actions">
        <div class="controls">
            <button type="submit"><?php _e($formData->buttonTitle, "wp-full-stripe"); ?></button>
        </div>
    </div>
</form>
