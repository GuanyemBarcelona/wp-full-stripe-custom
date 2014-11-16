    <!-- General Conditions -->
    <div class="control-group">
      <div class="general-conditions-wrapper">
        <?php if (ICL_LANGUAGE_CODE=='ca'){ ?>
        <h3>Condicions generals</h3>
        <h4>Relació entre Guanyem Barcelona i el col·laborador</h4>

        <p>La realització de donacions econòmiques no implica la vinculació del col·laborador amb Guanyem Barcelona ni li confereix la condició legal de soci, afiliat o simpatitzant, ni li atorga drets davant l'associació ni el partit, la inscripció del qual està pendent d'aprovació, ni queda obligat pels seus estatuts i resta de normes internes. D’acord amb el que s'estableix la llei 4/2008, del 24 d'abril, del llibre tercer del codi civil de Catalunya, relatiu a les persones jurídiques; la llei orgànica 1/2002, del 22 de març, reguladora del dret d'associació; la llei orgànica 8/2007, de 4 de juliol, de finançament de partits polítics, el vostre donatiu tindrà la consideració de "donació privada”, diferent de les quotes de socis, afiliats o simpatitzants.</p>

        <h4>Protecció de dades de caràcter personal</h4>

        <p>Guanyem Barcelona , d’acord amb la llei orgànica 15/1999, de protecció de dades de caràcter personal, us informa que les vostres dades seran incorporades al fitxer “donacions”, del qual és responsable Guanyem Barcelona, que les emprarà per tramitar la vostra col·laboració, gestionar els càrrecs bancaris i complir amb la normativa de finançament d'associacions i de partits polítics, així com amb finalitats estadístiques i fer-vos arribar periòdicament informació de les seves activitats. Vós concediu el consentiment exprés perquè aquestes dades passin a formar part de Guanyem Barcelona, tant de l'associació com del partit polític, la inscripció del qual està pendent d'aprovació. <span class="stripe-legal">Guanyem Barcelona li informa que les seves dades també seran transferides i emmagatzemades per Stripe, Inc. només amb la finalitat de procedir a l'actual transacció dinerària. Se li informa que Stripe, inc. potser emmagatzemi i processi les seves dades fora del territori de la UE, sempre d'acord amb la seva certificació Safe Harbor/Port Segur acreditada pel <a href="http://export.gov/safeharbor/" rel="external">U.S. Department of Commerce</a></span>. Per exercir els vostres drets d’accés, rectificació, cancel·lació o oposició, com ara cancel·lar la subscripció, modificar les dades bancàries o l’import i freqüència de la vostra donació, podeu enviar un correu electrònic a <strong>tresoreria@guanyembarcelona.cat</strong>, indicant la referència “modificació de la donació” o, per correu postal a la seu social sítia al carrer Castillejos, 233, baixos, de Barcelona, adjuntant còpia del vostre document nacional d’identitat.</p>

        <h4>Consentiment de l'ordre de domiciliació i càrrecs en targeta de crèdit o dèbit</h4>

        <p>En prémer el botó “Fes donació” vós autoritzeu a Guanyem Barcelona i a la vostra entitat bancària a efectuar les operacions de cobrament mitjançant càrrecs al compte bancari o a la targeta financera que ens heu facilitat, per l’import i amb la freqüència que ens heu indicat al formulari. Els càrrecs domiciliats i els càrrecs a la targeta financera es realitzaran entre els dies 1 i 15 de cada mes.</p>
        <?php }elseif(ICL_LANGUAGE_CODE=='es'){ ?>
        <h3>Condiciones generales</h3>
        <h4>Relación entre Guanyem Barcelona y el colaborador</h4>

        <p>La realización de donaciones económicas no implica la vinculación del colaborador con Guanyem Barcelona ni le confiere la condición legal de socio, afiliado o simpatizante, ni de derechos frente a la asociación ni al partido cuya inscripción se encuentra pendiente de aprobación, ni queda obligado por sus estatutos y demás normas internas. Conforme a lo dispuesto en la ley 4/2008, de 24 de abril, del libro tercero del código civil de Cataluña, relativo a las personas jurídicas; la ley orgánica 1/2002, de 22 de marzo, reguladora del derecho de  asociación,  la ley orgánica 8/2007, de 4 de julio, de financiación de partidos políticos su donativo tendrá la consideración de “donación privada”, distinta de las cuotas de socios, afiliados o simpatizantes.</p>

        <h4>Protección de datos de carácter personal</h4>

        <p>Guanyem Barcelona, de acuerdo con la ley orgánica 15/1999 de protección de datos de carácter personal, le informa de que sus datos serán incorporados al fichero “donaciones”, del que es responsable Guanyem Barcelona, que los utilizará para tramitar su colaboración, gestionar los adeudos bancarios, cumplir con la normativa de financiación de asociaciones y partidos políticos, para fines estadísticos y hacerle llegar periódicamente información sobre sus actividades. Usted concede su consentimiento expreso para que dichos datos pasen a formar parte de Guanyem Barcelona, tanto en la asociación como en el partido político  cuya inscripción se encuentra pendiente de aprobación. <span class="stripe-legal">Guanyem Barcelona le informa que sus datos también serán transferidos y almacenados por Stripe, inc. con el sólo fin de proceder a esta transacción dineraria. Se le informa que Stripe, inc. puede que almacene y procese sus datos fuera del territorio UE, siempre de acuerdo a su certificado Safe Harbor/Puerto Seguro emitido por el <a href="http://export.gov/safeharbor/" rel="external">U.S. Department of Commerce</a></span>. Para ejercer sus derechos de acceso, rectificación, cancelación u oposición, como cancelar su suscripción, modificar los datos bancarios o el importe y frecuencia de su donación, puede mandar un correo electrónico a <strong>tresoreria@guanyembarcelona.cat</strong> indicando la referencia “modificación de la donación” o, por correo postal en la sede social sita en la calle Castillejos, 233, bajos, de Barcelona, adjuntando copia de su documento nacional de identidad.</p>

        <h4>Consentimiento orden de domiciliación  y cargos en tarjeta de crédito o débito</h4>

        <p>Al pulsar el botón “Hacer donación” usted autoriza a Guanyem Barcelona y a su entidad bancaria a efectuar las operaciones de cobro mediante adeudos y cargos en la cuenta bancaria o en la tarjeta financiera que usted nos ha facilitado, por el importe y con la frecuencia que nos ha indicado en el formulario. Los adeudos domiciliados y los cargos en tarjeta se realizarán entre el día 1 y 15 de cada mes.</p>
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