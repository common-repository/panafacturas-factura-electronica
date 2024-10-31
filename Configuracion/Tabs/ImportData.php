<?php
namespace GTIPFE;

if (!current_user_can('manage_options')) {
    wp_die('No tienes suficientes permisos para acceder a esta página.');
}

add_filter('admin_init', 'GTIPFE_Register_my_general_settings_fields');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar el nonce
    if (!isset($_POST['nonce_importar_ajustes']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_importar_ajustes'])), 'importar_ajustes_pana_facturas')) {
        // El nonce no es válido
        wp_die('Nonce no válido');
    }

    // Verificar si se ha enviado un archivo y si se ha subido correctamente
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        // Obtener el contenido del archivo JSON y sanitizarlo
        $jsonString = file_get_contents($_FILES['archivo']['tmp_name']);
        $jsonString = wp_kses_post($jsonString);

        // Decodificar el JSON en un array asociativo
        $configData = json_decode($jsonString, true);

        // Validar que $configData sea un array
        if (is_array($configData) && isset($configData['GTIPFEPunto_Facturacion'])) {
            // Saneado y actualización de opciones
            // Sanitizar y escapar los datos antes de actualizar las opciones uno por uno
            update_option("GTIPFEPunto_Facturacion", esc_html(sanitize_text_field($configData['GTIPFEPunto_Facturacion'])));
            update_option("GTIPFEDecimales", esc_html(sanitize_text_field($configData['GTIPFEDecimales'])));
            update_option("GTIPFEUsuario", esc_html(sanitize_text_field($configData['GTIPFEUsuario'])));
            update_option("GTIPFEClave", esc_html(sanitize_text_field($configData['GTIPFEClave'])));
            update_option("GTIPFETipoRuc", esc_html(sanitize_text_field($configData['GTIPFETipoRuc'])));
            update_option("GTIPFERuc", esc_html(sanitize_text_field($configData['GTIPFERuc'])));
            update_option("GTIPFEDV", esc_html(sanitize_text_field($configData['GTIPFEDV'])));
            update_option("GTIPFENombEm", esc_html(sanitize_text_field($configData['GTIPFENombEm'])));
            update_option("GTIPFEGTIPFECoordEmLat", esc_html(sanitize_text_field($configData['GTIPFEGTIPFECoordEmLat'])));
            update_option("GTIPFECoordEmLong", esc_html(sanitize_text_field($configData['GTIPFECoordEmLong'])));
            update_option("GTIPFEDirecEm", esc_html(sanitize_text_field($configData['GTIPFEDirecEm'])));
            update_option("GTIPFECorreg", esc_html(sanitize_text_field($configData['GTIPFECorreg'])));
            update_option("GTIPFEDistr", esc_html(sanitize_text_field($configData['GTIPFEDistr'])));
            update_option("GTIPFEProv", esc_html(sanitize_text_field($configData['GTIPFEProv'])));
            update_option("GTIPFETfnEm", esc_html(sanitize_text_field($configData['GTIPFETfnEm'])));
            update_option("GTIPFECorElectEmi", esc_html(sanitize_text_field($configData['GTIPFECorElectEmi'])));

            GTIPFE_ImprimirMensajeConfiguracion("Datos importados correctamente");
        } else {
            GTIPFE_ImprimirMensajeConfiguracion("El archivo proporcionado no contiene la estructura esperada.", true);
        }
    }
}


function GTIPFE_Register_my_general_settings_fields()
{
    // Puedes registrar tus campos aquí si lo necesitas
}

function GTIPFE_ImprimirMensajeConfiguracion($message, $error = false)
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

    if ($error) {
        $vlcClaseError = "error"; // Si se especifica un error, cambia la clase a error
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
    <h4 class="postbox-header" style="margin: 0;padding: 15px;"><label>Importar Ajustes de Pana Facturas</label></h4>
    <div style="padding:15px;">
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('importar_ajustes_pana_facturas', 'nonce_importar_ajustes'); ?>
            <table>
                <tbody>
                    <tr>
                        <td><label>Archivo a importar</label></td>
                        <td>
                            <?php
                            echo '<input type="file" required id="archivo" name="archivo" accept="application/JSON"/>';
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input style="margin-top:20px" type="submit" value="Importar Configuración" name="submit" class="button-primary">
        </form>
    </div>
</div>
