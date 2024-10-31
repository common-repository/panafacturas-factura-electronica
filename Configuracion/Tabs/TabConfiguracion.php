<?php
namespace GTIPFE;

if (!current_user_can('manage_options')) {
    wp_die('No tienes suficientes permisos para acceder a esta página.');
}

add_filter('admin_init', 'GTIPFE_Register_my_general_settings_fields');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar el nonce
    if (!isset($_POST['nonce_guardar_ajustes']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_guardar_ajustes'])), 'guardar_ajustes_pana_facturas')) {
        // El nonce no es válido
        wp_die('Nonce no válido');
    }

    // Saneado y validación
    $GTIPFEambiente = isset($_POST['GTIPFEambiente']) ? sanitize_text_field(wp_unslash($_POST['GTIPFEambiente'])) : ''; // Sanitizamos el campo "GTIPFEambiente"
    $puntoFacturacion = isset($_POST['GTIPFEPunto_Facturacion']) ? sanitize_text_field(str_pad(wp_unslash($_POST['GTIPFEPunto_Facturacion']), 3, "0", STR_PAD_LEFT)) : ''; // Sanitizamos el número de punto de facturación
    $GTIPFEUsuario = isset($_POST['GTIPFEUsuario']) ? sanitize_text_field(wp_unslash($_POST['GTIPFEUsuario'])) : ''; // Sanitizamos el nombre de usuario
    $GTIPFEClave = isset($_POST['GTIPFEClave']) ? sanitize_text_field(wp_unslash($_POST['GTIPFEClave'])) : ''; // Sanitizamos la clave de usuario

    // Validar ambiente
    $GTIPFEambiente_valido = ($GTIPFEambiente === 'on' || $GTIPFEambiente === 'off');

    // Validar punto de facturación como un número de tres dígitos
    $puntoFacturacion_valido = preg_match('/^\d{3}$/', $puntoFacturacion);

    // Validar nombre de usuario
    $GTIPFEUsuario_valido = !empty($GTIPFEUsuario);

    // Validar clave de usuario
    $GTIPFEClave_valido = !empty($GTIPFEClave);

    // Si todos los datos son válidos, actualizamos las opciones
    if ($GTIPFEambiente_valido && $puntoFacturacion_valido && $GTIPFEUsuario_valido && $GTIPFEClave_valido) {
        // Actualizamos las opciones
        update_option("GTIPFEambiente", $GTIPFEambiente); // No es necesario validar ya que solo puede ser 'on' o 'off'
        update_option("GTIPFEPunto_Facturacion", $puntoFacturacion);
        update_option("GTIPFEUsuario", $GTIPFEUsuario);
        update_option("GTIPFEClave", $GTIPFEClave);

        GTIPFE_ImprimirMensajeConfiguracion("Datos actualizados correctamente");
    } else {
        // Si algún dato no es válido, mostramos un mensaje de error
        GTIPFE_ImprimirMensajeConfiguracion("Los datos ingresados no son válidos");
    }
}

function GTIPFE_Register_my_general_settings_fields()
{
    register_setting('general', 'GTIPFETitulo_Ajustes_GTI', 'esc_attr');
    register_setting('general', 'GTIPFEPunto_Facturacion', 'esc_attr');
    //register_setting('general', 'GTIPFEDecimales', 'esc_attr');
    register_setting('general', 'GTIPFEUsuario', 'esc_attr');
    register_setting('general', 'GTIPFEClave', 'esc_attr');
    register_setting('general', 'GTIPFEambiente', 'esc_attr');
}

// Function to show notifications on screen
function GTIPFE_ImprimirMensajeConfiguracion($message)
{
    $vlcClaseError = "success";
    $vlcBackgroundError = "#fff";

    // Escapar el mensaje
    $escaped_message = esc_html($message);

    if (is_wp_error($message)) {
        if ($message->get_error_data() && is_string($message->get_error_data())) {
            $message = $message->get_error_message() . ': ' . $message->get_error_data();
            $vlcClaseError = "error";
            $vlcBackgroundError = "#fff";
        } else {
            $message = $message->get_error_message();
            $vlcClaseError = "error";
            $vlcBackgroundError = "#fff";
        }
    }
    ?>
    <div class="notice <?php echo esc_attr($vlcClaseError); ?> my-acf-notice is-dismissible" style="background:<?php echo esc_attr($vlcBackgroundError); ?>;">
        <p><?php echo esc_html($escaped_message); ?></p>
    </div>
    <?php
}


add_action('admin_notices', 'GTIPFE_ImprimirMensajeConfiguracion');
?>

<div class="postarea wp-editor-expand postbox" style="margin-top:15px">
    <h4 class="postbox-header" style="margin: 0;padding: 15px;"><label>Ajustes Pana Facturas</label></h4>
    <div style="padding:15px;">
        <form method="post">
            <?php wp_nonce_field('guardar_ajustes_pana_facturas', 'nonce_guardar_ajustes'); ?>
            <div style="margin-top: 20px">
                <p>En esta ventana podrás realizar la configuración general del plugin.</p>
                <b>Consideraciones</b>
                <p>1 - Deberás rellenar correctamente toda la información requerida para evitar errores en el funcionamiento normal del plugin.</p>
                <table>
                    <tbody>
                        <tr>
                            <td><label>Punto de facturación</label></td>
                            <td>
                                <?php
                                $Punto = get_option('GTIPFEPunto_Facturacion', '');
                                echo '<input type="number" required id="GTIPFEPunto_Facturacion" name="GTIPFEPunto_Facturacion" value="' . esc_attr($Punto) . '" />';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Usuario</label></td>
                            <td>
                                <?php
                                $vlcUsurio = get_option('GTIPFEUsuario', '');
                                echo '<input type="text" required id="GTIPFEUsuario" name="GTIPFEUsuario" value="' . esc_attr($vlcUsurio) . '" />';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Contraseña</label></td>
                            <td>
                                <?php
                                $vlcContrasenia = get_option('GTIPFEClave', '');
                                echo '<input type="password" required id="GTIPFEClave" name="GTIPFEClave" value="' . esc_attr($vlcContrasenia) . '" />';
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top:20px;">
                    <b>Ambiente de pruebas</b>
                    <p>1 - <b>Esta opción solo deberá estar activa cuando se realicen pruebas del plugin</b>, ya que todas las facturas serán emitidas a un ambiente de pruebas.</p>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td><label>Ambiente de pruebas&nbsp;&nbsp;&nbsp;</label></td>
                            <td>
                                <?php
                                $vlcAmbiente = get_option('GTIPFEambiente', '');

                                if ($vlcAmbiente == "on") {
                                    echo '<input type="checkbox" id="GTIPFEambiente" checked name="GTIPFEambiente"/>';
                                } else {
                                    echo '<input type="checkbox" id="GTIPFEambiente" name="GTIPFEambiente"/>';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input style="margin-top:20px" type="submit" value="Guardar Configuración" name="submit" class="button-primary">
            </div>
        </form>
    </div>
</div>
