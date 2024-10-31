<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action('woocommerce_checkout_process', 'GTIPFE_wh_phoneValidateCheckoutFields');

function GTIPFE_wh_phoneValidateCheckoutFields() {
    $billing_options_emitir = filter_input(INPUT_POST, 'billing_options_emitir', FILTER_SANITIZE_STRING);
    $billing_options_tiporuc = filter_input(INPUT_POST, 'billing_options_tiporuc', FILTER_SANITIZE_STRING);
    $billing_options_iTipoRec = filter_input(INPUT_POST, 'billing_options_itiporec', FILTER_SANITIZE_STRING);
    $billing_options_ruc = filter_input(INPUT_POST, 'billing_options_ruc', FILTER_SANITIZE_STRING);
    $billing_options_dv = filter_input(INPUT_POST, 'billing_options_dv', FILTER_SANITIZE_STRING);
    $billing_options_corre = filter_input(INPUT_POST, 'billing_options_corre', FILTER_SANITIZE_STRING);

    // Validar si va a emitir
    if($billing_options_emitir == "1")
    {
        if (empty($billing_options_tiporuc)) {
            wc_add_notice('El tipo de <strong>RUC</strong> es requerido.', 'error');
        }
        if (empty($billing_options_iTipoRec)) {
            wc_add_notice('El tipo de <strong>receptor </strong> es requerido.', 'error');
        }
        if (empty($billing_options_ruc)) {
            wc_add_notice('El número de <strong>RUC</strong> es requerido.', 'error');
        }
        if($billing_options_iTipoRec == "01" || $billing_options_iTipoRec == "03"){
            if (empty($billing_options_dv)) {
                wc_add_notice('El <strong>dígito verificador</strong> es requerido.', 'error');
            }
        }
        if($billing_options_iTipoRec == "01" || $billing_options_iTipoRec == "03"){
            if (empty($billing_options_corre)) {
                wc_add_notice('El <strong>corregimiento</strong> es requerido.', 'error');
            }
        }
    }
}

?>