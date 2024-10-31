<?php
/**
 * Plugin Name:       Panafacturas Factura Electrónica
 * Requires Plugins:  woocommerce
 * Plugin URI:        https://www.panafacturas.com/
 * Description:       Plugin para emitir factura electrónica.
 * Version:           1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            GTI
 * Author URI:        https://profiles.wordpress.org/tigti/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pana-facturas
 * Domain Path:       /languages
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
include_once 'Document/Document.php';
include_once 'SendDocument.php';
include_once 'ObtenerImpuesto.php';
include_once 'ObtenerDescuento.php';
include_once 'Ubicacion/Ubicacion.php';
include_once 'Configuracion/Configuracion.php';
include_once 'Validaciones/Checkout.php';
include_once 'CargaAtributos.php';
include_once 'ObtenerMedioPago.php';
include_once 'Taxes/PanamaTaxes.php';
include_once 'CustomFields/ReceptorFields.php';

ob_start();
// Verificar que WooCommerce está activo
if (!in_array("woocommerce/woocommerce.php", apply_filters("active_plugins", get_option("active_plugins")))) {
    return;
}

add_action('template_redirect', 'GTIPFE_EmitirFacturaElectronica');
function GTIPFE_EmitirFacturaElectronica()
{
    try {
        if (!is_wc_endpoint_url("order-received")) {
            return;
        }
        global $wp;
        // Si está en inglés
        $order_id = intval(str_replace("checkout/order-received/", "", $wp->request));
        if ($order_id == 0) {
            // Si está en español
            $order_id = intval(str_replace("finalizar-compra/order-received/", "", $wp->request));
        }
        // Obtener datos de compra
        $order = wc_get_order($order_id);

        $already_processed = get_post_meta($order_id, '_send_invoice_pf', true);
        if(!$already_processed)
        {
            $send = GTIPFE_sendFactura($order);
			//var_dump($send);
            $note = "";
            if ($send) {
                if ($send['Procesamiento']['Aceptado']) {
                    $vlcMensaje = 'Su factura electrónica ha sido emitida';
                    $vlcTipoMensaje = 'success';
                    add_post_meta($order->id, '_send_invoice_pf', true);
                    GTIPFE_ImprimirMensaje($vlcMensaje, $vlcTipoMensaje);
                    $note = 'Factura Electronica enviada a Pana facturas con el número: ' . $send['Procesamiento']['NumFiscal'];
                } else {
                    $vlcMensaje = 'Ha ocurrido un error al generar su factura electrónica. Por favor, comuníquese con el administrador del sitio: ' . $send['Procesamiento']['ResProc'][0]['MsgRes'];
                    $vlcTipoMensaje = 'error';
                    add_post_meta($order->id, '_send_invoice_pf', false);
                    GTIPFE_writeLog('errorPanaFacturasSendOrder_' . $order_id, $send);
                    GTIPFE_ImprimirMensaje($vlcMensaje, $vlcTipoMensaje);
                    $note = $vlcMensaje;
                }
                $order->add_order_note($note, $is_customer_note = 0, $added_by_user = false);
            }
        }else{
            $vlcTipoMensaje = 'success';
            $vlcMensaje = 'La factura ya ha sido emitida anteriormente.';
            GTIPFE_ImprimirMensaje($vlcMensaje, $vlcTipoMensaje);
        }
    } catch (\Throwable $th) {
        add_post_meta($order->id, '_send_invoice_pf', false);
        $order->add_order_note('Error al enviar la factura electrónica a Pana facturas, intente reenviarla manualmente', $is_customer_note = 0, $added_by_user = false);
        $vlcMensaje = 'Ha ocurrido un error al generar su factura electrónica, por favor comuníquese con el administrador del sitio.';
        $vlcTipoMensaje = 'error';
        GTIPFE_ImprimirMensaje($vlcMensaje, $vlcTipoMensaje);
    }
}

function GTIPFE_sendFactura($order)
{
    $documento = new GTIPFEDocumento();
    $sendDocument = new GTIPFESendDocument();
    $ubicacion = new GTIPFEGetUbicacion();
    // Validar que el pago se realizó correctamente
    if ($order->get_status() != "Failed" && $order->get_status() != "Canceled" && $order->get_status() != "cancelled") {
        $data = $order->get_data();
        $items = $order->get_items();
        $emitir = $order->get_meta('_billing_options_emitir');
        $receptorBillingData = array(
            'billing' => $data["billing"],
            'tiporuc' => $order->get_meta('_billing_options_tiporuc'),
            'iTipoRec' => $order->get_meta('_billing_options_itiporec'),
            'ruc' => $order->get_meta('_billing_options_ruc'),
            'dv' => $order->get_meta('_billing_options_dv'),
            'corregimiento' => $order->get_meta('_billing_options_corre'),
        );
        $shipping_method = array_shift($order->get_shipping_methods());
        $shippingData = array(
            'total' => $shipping_method['total'],
            'name' => $shipping_method['name']
        );
        // Generar factura
        if ($emitir == "1") {
            $emisor = $documento->GTIPFE_ObtenerEmisor();
            $receptor = $documento->GTIPFE_ObtenerReceptor($receptorBillingData);
            $lineas = $documento->GTIPFE_GenerarLineas($items, $shippingData);
            $totales = $documento->GTIPFE_ObtenerTotales($lineas,$order);
            $docData = array(
                'gDGen' => array(
                    "iTpEmis" => "01",
                    "iDoc" => "01",
                    "dPtoFacDF" => get_option('GTIPFEPunto_Facturacion'),
                    "dFechaEm" => date('Y-m-d\TH:i:sP', current_time('timestamp')),
                    "iNatOp" => "01",
                    "iTipoOp" => 1,
                    "iDest" => 1,
                    "iFormCAFE" => 3,
                    "iEntCAFE" => 3,
                    "dEnvFE" => 1,
                    "iTipoTranVenta" => 1,
                    "iTipoSuc" => 1,
                    "dInfEmFE" => "",
                    'gEmis' => $emisor,
                    'gDatRec' => $receptor,
                ),
                'gItem' => $lineas,
                'gTot' => $totales,
            );
            $json = json_encode($docData);
            $user = get_option('GTIPFEUsuario');
            $pass = get_option('GTIPFEClave');
            $tokenData = $sendDocument->GTIPFE_ObtenerToken($user, $pass);
            $send = $sendDocument->GTIPFE_SendDocument($tokenData, $json);
            return $send;
        } else {
            return false;
        }
    }
}

// Función para formatear cadena en flotante
function GTIPFE_tofloat($num)
{
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
    if (!$sep) {
        return floatval(preg_replace("/[^0-9]/", "", $num));
    }
    return floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . "." .
        preg_replace("/[^0-9]/", "", substr($num, $sep + 1, strlen($num)))
    );
}

// Función para mostrar notificaciones en pantalla
function GTIPFE_ImprimirMensaje($pvcMensaje, $pvcTipoMensaje)
{
    wc_add_notice($pvcMensaje, $pvcTipoMensaje);
    wc_print_notices();
}



function GTIPFE_admin_enqueue_scripts_callback()
{
    wp_enqueue_style('select2-css2', '/wp-content/plugins/WooCommerce-PA/css/tagify.css', array());
    // Agregar el archivo CSS de Select2
    wp_enqueue_style('select2-css', '/wp-content/plugins/WooCommerce-PA/css/select2.min.css', array());
    // Agregar el archivo JavaScript de Select2
    wp_enqueue_script('select2-js', '/wp-content/plugins/WooCommerce-PA/JS/select2.min.js', array('jquery'));
    wp_enqueue_script('select2-js2', '/wp-content/plugins/WooCommerce-PA/JS/jQuery.tagify.min.js', array('jquery'));
    wp_enqueue_script('select2-js3', '/wp-content/plugins/WooCommerce-PA/JS/Tags.js', array('jquery'));
}
add_action('admin_enqueue_scripts', 'GTIPFE_admin_enqueue_scripts_callback');

function GTIPFE_admin_enqueue_scripts_callback2()
{
    wp_enqueue_style('select2-css', '/wp-content/plugins/WooCommerce-PA/css/tagify2.css', array());
}
add_action('admin_enqueue_scripts', 'GTIPFE_admin_enqueue_scripts_callback2');

function GTIPFE_wp_enqueue_scripts_callback()
{
    // Agregar el archivo CSS de Select2
    wp_enqueue_style('select2-css', '/wp-content/plugins/WooCommerce-PA/css/select22.min.css', array());
    wp_enqueue_script('jquery');
    // Agregar el archivo JavaScript de Select2
    wp_enqueue_script('select2-js', '/wp-content/plugins/WooCommerce-PA/JS/select22.min.js', array('jquery'));
    // Agregar un archivo JavaScript para inicializar los elementos de Select2
    wp_enqueue_script('select2-init', '/wp-content/plugins/WooCommerce-PA/JS/Select2.js', array('jquery'));
}
add_action('wp_enqueue_scripts', 'GTIPFE_wp_enqueue_scripts_callback');

add_action("wp_ajax_GTIPFE_provincias_get", "GTIPFE_provincias_get");
add_action("wp_ajax_nopriv_GTIPFE_provincias_get", "GTIPFE_provincias_get");

function GTIPFE_provincias_get()
{
    header('Content-Type: application/json; charset=utf-8');
    $ubicacion = new GTIPFEGetUbicacion();
    $provincias = $ubicacion->GTIPFE_Provincias();
	echo wp_json_encode($provincias);
    wp_die();
}

add_action("wp_ajax_GTIPFE_distritos_get", "GTIPFE_distritos_get");
add_action("wp_ajax_nopriv_GTIPFE_distritos_get", "GTIPFE_distritos_get");

function GTIPFE_distritos_get()
{
    header('Content-Type: application/json; charset=utf-8');
    $ubicacion = new GTIPFEGetUbicacion();
    $distritos = $ubicacion->GTIPFE_Distritos();
    echo wp_json_encode($distritos);
    wp_die();
}

add_action("wp_ajax_GTIPFE_corregimiento_get", "GTIPFE_corregimiento_get");
add_action("wp_ajax_nopriv_GTIPFE_corregimiento_get", "GTIPFE_corregimiento_get");

function GTIPFE_corregimiento_get()
{
    header('Content-Type: application/json; charset=utf-8');
    $ubicacion = new GTIPFEGetUbicacion();
    $distritos = $ubicacion->GTIPFE_Corregimientos();
	echo wp_json_encode($distritos);
    wp_die();
}

// Definir la función a ejecutar para los usuarios desconectados
function GTIPFE_please_login()
{
    echo "Debe iniciar sesión para realizar esta acción";
    die();
}

add_action('woocommerce_order_actions', 'GTIPFE_add_custom_action');

function GTIPFE_add_custom_action($actions)
{
    $actions['add_my_custom_action'] = 'Reenviar factura a Pana Facturas';
    return $actions;
}

add_action('woocommerce_order_action_add_my_custom_action', 'GTIPFE_add_my_custom_action_function');

function GTIPFE_add_my_custom_action_function($order)
{
    try {
        $order = wc_get_order($order->id);
        $enviado = get_post_meta($order->id, '_send_invoice_pf', true);
        if ($enviado == "") {
            add_post_meta($order->id, '_send_invoice_pf', false);
        }
        if ($enviado != 1) {
            $send = GTIPFE_sendFactura($order);
            if ($send) {
                if ($send['Procesamiento']['Aceptado']) {
                    update_post_meta($order->id, '_send_invoice_pf', true);
                    $vlcMensaje = 'Factura Electronica enviada a Pana facturas con el número: ' . $send['Procesamiento']['NumFiscal'];
                } else {
                    $vlcMensaje = 'Ha ocurrido un error al generar su factura electrónica. Por favor, comuníquese con el administrador del sitio: ' . $send['Procesamiento']['ResProc'][0]['MsgRes'];
                    update_post_meta($order->id, '_send_invoice_pf', false);
                }
            }
            $order->add_order_note($vlcMensaje, $is_customer_note = 0, $added_by_user = false);
        } else {
            $order->add_order_note("Error al enviar la factura electrónica a Pana facturas, ya la factura se ha enviado anteriormente.", $is_customer_note = 0, $added_by_user = false);
        }
    } catch (\Throwable $th) {
        update_post_meta($order->id, '_send_invoice_pf', false);
        $order->add_order_note('Error al enviar la factura electrónica a Pana facturas, intente reenviarla manualmente', true);
    }
}

function GTIPFE_writeLog($name,$data){
    // Codificar array a JSON
    $json = json_encode(array('data' => $data));
    // Escribir JSON en archivo
    file_put_contents($name.".json", $json);
}


?>