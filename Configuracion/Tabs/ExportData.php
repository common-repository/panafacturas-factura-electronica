<?php
namespace GTIPFE;

if (!current_user_can('manage_options')) {
    wp_die('No tienes suficientes permisos para acceder a esta página.');
}

add_filter('admin_init', 'GTIPFE_Register_my_general_settings_fields');

$download = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar el nonce
    if (!isset($_POST['nonce_exportar_ajustes']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_exportar_ajustes'])), 'exportar_ajustes_pana_facturas')) {
        // El nonce no es válido
        wp_die('Nonce no válido');
    }

    $base = WP_PLUGIN_DIR;
    $data = array(
        'GTIPFEPunto_Facturacion' => sanitize_text_field(get_option('GTIPFEPunto_Facturacion')),
        'GTIPFEDecimales' => sanitize_text_field(get_option('GTIPFEDecimales')),
        'GTIPFEUsuario' => sanitize_text_field(get_option('GTIPFEUsuario')),
        'GTIPFEClave' => sanitize_text_field(get_option('GTIPFEClave')),
        'GTIPFEambiente' => sanitize_text_field(get_option('GTIPFEambiente')),
        'GTIPFETipoRuc' => sanitize_text_field(get_option('GTIPFETipoRuc')),
        'GTIPFERuc' => sanitize_text_field(get_option('GTIPFERuc')),
        'GTIPFEDV' => sanitize_text_field(get_option('GTIPFEDV')),
        'GTIPFENombEm' => sanitize_text_field(get_option('GTIPFENombEm')),
        'GTIPFEGTIPFECoordEmLat' => sanitize_text_field(get_option('GTIPFEGTIPFECoordEmLat')),
        'GTIPFECoordEmLong' => sanitize_text_field(get_option('GTIPFECoordEmLong')),
        'GTIPFEDirecEm' => sanitize_text_field(get_option('GTIPFEDirecEm')),
        'GTIPFECorreg' => sanitize_text_field(get_option('GTIPFECorreg')),
        'GTIPFEDistr' => sanitize_text_field(get_option('GTIPFEDistr')),
        'GTIPFEProv' => sanitize_text_field(get_option('GTIPFEProv')),
        'GTIPFETfnEm' => sanitize_text_field(get_option('GTIPFETfnEm')),
        'GTIPFECorElectEmi' => sanitize_text_field(get_option('GTIPFECorElectEmi'))
    );
    $jsonString  = json_encode($data);
    $rutaArchivo = $base . '/WooCommerce-PA/respaldoPanaFacturas.json';
    $nombre = 'respaldoPanaFacturas.json';
    file_put_contents($rutaArchivo, $jsonString);

    $download = true;
}

function GTIPFE_Register_my_general_settings_fields()
{
    register_setting('general', 'GTIPFETitulo_Ajustes_GTI', 'esc_attr');

    register_setting('general', 'GTIPFEPunto_Facturacion', 'esc_attr');
    register_setting('general', 'GTIPFEDecimales', 'esc_attr');

    register_setting('general', 'GTIPFEUsuario', 'esc_attr');

    register_setting('general', 'GTIPFEClave', 'esc_attr');

    register_setting('general', 'GTIPFEambiente', 'esc_attr');
}

//Function to show notifications on screen
function GTIPFE_ImprimirMensajeConfiguracion($message)
{
    $vlcClaseError = "success"; // Clase por defecto para éxito
    $vlcBackgroundError = "#fff"; // Color de fondo por defecto

    if (is_wp_error($message)) {
        if ($message->get_error_data() && is_string($message->get_error_data())) {
            $message = $message->get_error_message() . ': ' . $message->get_error_data();
            $vlcClaseError = "error"; // Si hay un error y datos, cambia la clase a error
        } else {
            $message = $message->get_error_message();
            $vlcClaseError = "error"; // Si hay un error sin datos, cambia la clase a error
        }
    }
    ?>
    <div class="notice <?php echo esc_attr($vlcClaseError); ?> my-acf-notice is-dismissible" style="background:<?php echo esc_attr($vlcBackgroundError); ?>;">
        <p><?php echo esc_html($message); ?></p>
    </div>
<?php
}

add_action('admin_notices', 'GTIPFE_ImprimirMensajeConfiguracion');

?>
<div class="postarea wp-editor-expand postbox" style="margin-top:15px">
    <h4 class="postbox-header" style="margin: 0;padding: 15px;"><label><?php esc_html_e('Exportar Ajustes de Pana Facturas', 'panafacturas-factura-electronica'); ?></label></h4>
    <div style="padding:15px;">
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('exportar_ajustes_pana_facturas', 'nonce_exportar_ajustes'); ?>
            <p><?php esc_html_e('La Configuración se exportará en formato json.', 'panafacturas-factura-electronica'); ?></p>
            <?php if (!$download) { ?>
                <input style="margin-top:20px" type="submit" value="<?php esc_attr_e('Generar archivo de Configuración', 'panafacturas-factura-electronica'); ?>" name="submit" class="button-secondary">
            <?php } ?>
        </form>
        <?php if ($download) { ?>
            <a href="<?php echo esc_url(get_site_url() . '/wp-content/plugins/WooCommerce-PA/respaldoPanaFacturas.json'); ?>" download="respaldoPanaFacturas.json" style="margin-top:20px" class="button-primary"><?php esc_html_e('Descargar Archivo', 'panafacturas-factura-electronica'); ?></a>
        <?php } ?>
    </div>
</div>

