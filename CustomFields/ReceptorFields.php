<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_filter('woocommerce_billing_fields', 'GTIPFE_custom_woocommerce_billing_fields');

function GTIPFE_custom_woocommerce_billing_fields($fields)
{


    $fields['billing_options_emitir'] = array(
        'label' => 'Emitir documento', 'panafacturas-factura-electronica',
        // Add custom field label
        'placeholder' => _x('Emitir documento', 'placeholder', 'panafacturas-factura-electronica'),
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'select',
        'priority' => 80,
        // add field type
        'options' => array(
            '' => 'Por favor elija una opción',
            '1' => 'Si',
            '2' => 'No'
        )
    );
    $fields['billing_options_tiporuc'] = array(
        'label' => 'Tipo de RUC', 'panafacturas-factura-electronica',
        'priority' => 90,
        // Add custom field label
        'placeholder' => _x('Your RUC here....', 'placeholder', 'panafacturas-factura-electronica'),
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'select',
        // add field type
        'options' => array(
            '' => 'Por favor elija una opción',
            '1' => 'Natural',
            '2' => 'Jurídico'
        )
    );
    $fields['billing_options_itiporec'] = array(
        'label' => 'Tipo de receptor', 'panafacturas-factura-electronica',
        // Add custom field label
        'placeholder' => _x('Por favor elija una opción', 'placeholder', 'panafacturas-factura-electronica'),
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'select',
        'priority' => 100,
        // add field type
        'options' => array(
            '' => 'Por favor elija una opción',
            '01' => 'Contribuyente',
            '02' => 'Consumidor final',
            '03' => 'Gobierno',
            '04' => 'Extranjero',
        )
    );
    $fields['billing_options_ruc'] = array(
        'label' => '<span id="rucLabel">Número de RUC</span>', 'panafacturas-factura-electronica',
        // Add custom field label
        'placeholder' => _x('Your RUC here....', 'placeholder', 'panafacturas-factura-electronica'),
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'text',
        // add field type
        'priority' => 110,
    );

    $fields['billing_options_dv'] = array(
        'label' => 'Dígito Verificador', 'panafacturas-factura-electronica',
        // Add custom field label
        'placeholder' => _x('Dígito Verificador....', 'placeholder', 'panafacturas-factura-electronica'),
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'text',
        // add field type
        'priority' => 120,
    );

    $ubicacion = new GTIPFEGetUbicacion();
    $corregimientos = $ubicacion->GTIPFE_AllCorregimientos();
    $correList = array();
    $correList[" "] = 'Elija un valor';
    foreach ($corregimientos as $corregimiento) {
        $key = $corregimiento['IGTIPFEProvincia'] . '-' . $corregimiento['IGTIPFEDistrito'] . '-' . $corregimiento['IGTIPFECorregimientos'];
        $correList[$key] = $corregimiento['Descripcion'];
    }

    $fields['billing_options_corre'] = array(
        'label' => 'Corregimiento', 'panafacturas-factura-electronica',
        // Add custom field label
        'placeholder' => _x('Corregimiento', 'placeholder', 'panafacturas-factura-electronica'),
        // Add custom field placeholder
        'required' => false,
        // if field is required or not
        'clear' => false,
        // add clear or not
        'type' => 'select',
        'priority' => 130,
        'id' => 'GTIPFECorreg2',
        // add field type
        'options' => $correList
    );

    return $fields;
}


?>