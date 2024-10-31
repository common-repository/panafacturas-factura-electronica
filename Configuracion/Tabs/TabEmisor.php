<?php

namespace GTIPFE;

if (!current_user_can('manage_options'))
    wp_die('No tienes suficientes permisos para acceder a esta página.');

add_filter('admin_init', 'GTIPFE_Register_my_general_settings_fields');

$ubicacion = new \GTIPFEGetUbicacion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar el nonce
    if (!isset($_POST['nonce_guardar_ajustes_emisor']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_guardar_ajustes_emisor'])), 'guardar_ajustes_emisor_pana_facturas')) {
        // El nonce no es válido
        wp_die('Nonce no válido');
    }

    // Saneado y validación de telefonos y correos electrónicos
    $telefonos = isset($_POST['GTIPFETfnEm']) ? json_decode(wp_unslash($_POST['GTIPFETfnEm']), true) : array();
    $emails = isset($_POST['GTIPFECorElectEmi']) ? json_decode(wp_unslash($_POST['GTIPFECorElectEmi']), true) : array();

    $telList = array();
    $emailList = array();

    foreach ($telefonos as $telefono) {
        // Sanear el número de teléfono eliminando cualquier carácter que no sea un dígito
        $telClean = preg_replace('/[^0-9]/', '', $telefono['value']);
        // Añadir un guión en la posición 4 del número de teléfono
        $tel = substr_replace($telClean, "-", 4, 0);
        // Escapar y sanear los números de teléfono
        $tel = sanitize_text_field(wp_unslash($tel));
        // Validar si el teléfono es válido antes de agregarlo a la lista
        if (!empty($tel) && preg_match('/^\d{3}-\d{4}-\d{4}$/', $tel)) {
            $telList[] = $tel;
        } else {
            $message = 'Es un formato de teléfono inválido: ' . $telefono['value'];
            GTIPFE_ImprimirMensajeConfiguracion($message, true);
        }
    }

    foreach ($emails as $email) {
        // Sanear el correo electrónico
        $emailValue = sanitize_email(wp_unslash($email['value']));
        // Validar si el correo electrónico es válido antes de agregarlo a la lista
        if (!empty($emailValue) && is_email($emailValue)) {
            $emailList[] = $emailValue;
        } else {
            $message = 'Es un formato de email inválido: ' . $email['value'];
            GTIPFE_ImprimirMensajeConfiguracion($message, true);
        }
    }

    // Escapar y sanear los correos electrónicos
    $emailList = implode(',', array_map('sanitize_email', array_map('trim', $emailList)));

    // Actualizar opciones con saneado y validación
    update_option("GTIPFETipoRuc", sanitize_text_field(wp_unslash($_POST['GTIPFETipoRuc'])));
    update_option("GTIPFERuc", sanitize_text_field(wp_unslash($_POST['GTIPFERuc'])));
    update_option("GTIPFEDV", sanitize_text_field(wp_unslash($_POST['GTIPFEDV'])));
    update_option("GTIPFENombEm", sanitize_text_field(wp_unslash($_POST['GTIPFENombEm'])));
    update_option("GTIPFEGTIPFECoordEmLat", sanitize_text_field(wp_unslash($_POST['GTIPFEGTIPFECoordEmLat'])));
    update_option("GTIPFECoordEmLong", sanitize_text_field(wp_unslash($_POST['GTIPFECoordEmLong'])));
    update_option("GTIPFEDirecEm", sanitize_text_field(wp_unslash($_POST['GTIPFEDirecEm'])));
    update_option("GTIPFECorreg", sanitize_text_field(wp_unslash($_POST['GTIPFECorreg'])));
    update_option("GTIPFEDistr", sanitize_text_field(wp_unslash($_POST['GTIPFEDistr'])));
    update_option("GTIPFEProv", sanitize_text_field(wp_unslash($_POST['GTIPFEProv'])));
    update_option("GTIPFETfnEm", implode(',', array_map('trim', $telList)));
    update_option("GTIPFECorElectEmi", $emailList);

    GTIPFE_ImprimirMensajeConfiguracion("Datos actualizados correctamente");
}




function GTIPFE_Register_my_general_settings_fields()
{

    register_setting('general', 'GTIPFETipoRuc', 'sanitize_text_field');
    register_setting('general', 'GTIPFERuc', 'sanitize_text_field');
    register_setting('general', 'GTIPFEDV', 'sanitize_text_field');
    register_setting('general', 'GTIPFENombEm', 'sanitize_text_field');
    register_setting('general', 'GTIPFEGTIPFECoordEmLat', 'sanitize_text_field');
    register_setting('general', 'GTIPFECoordEmLong', 'sanitize_text_field');
    register_setting('general', 'GTIPFEDirecEm', 'sanitize_text_field');
    register_setting('general', 'GTIPFECorreg', 'sanitize_text_field');
    register_setting('general', 'GTIPFEDistr', 'sanitize_text_field');
    register_setting('general', 'GTIPFEProv', 'sanitize_text_field');
    register_setting('general', 'GTIPFETfnEm');
    register_setting('general', 'GTIPFECorElectEmi');
}

//Function to show notifications on screen
function GTIPFE_ImprimirMensajeConfiguracion($message, $error = false)
{
    $vlcClaseError = "success";
    $vlcBackgroundError = "#fff";
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

    if ($error) {
        $vlcClaseError = "error";
        $vlcBackgroundError = "#fff";
    }
    ?>
    <div class="notice <?php echo esc_attr($vlcClaseError) ?> my-acf-notice is-dismissible"
        Style='background:<?php echo esc_attr($vlcBackgroundError) ?>;'>
        <p>
            <?php echo esc_html("{$message}"); ?>
        </p>
    </div>
    <?php
}
add_action('admin_notices', 'GTIPFE_ImprimirMensajeConfiguracion');

?>


<div class="postarea wp-editor-expand postbox" style="margin-top:15px">
<h4 class="postbox-header" style="margin: 0;padding: 15px;" ><label> Ajustes Emisor Pana Facturas</label></h4>
    <div style="padding:15px;">
    <form method="post">
    <?php wp_nonce_field('guardar_ajustes_emisor_pana_facturas', 'nonce_guardar_ajustes_emisor'); ?>


<div style="margin-top: 20px">

    <p>En esta ventana podrá realizar la configuración del emisor.</p>

    <b>Consideraciones</b>
    <p>1 - Deberá rellenar correctamente toda la información requerida para evitar errores en el funcionamiento
        normal del plugin.</p>

    <table>
        <tbody>
            <tr>
                <td><label>Tipo de RUC</label></td>
                <td>
                    <?php
                    echo '<select required  id="GTIPFETipoRuc" name="GTIPFETipoRuc">';
                    echo '<option value="1" ' . selected(1, get_option('GTIPFETipoRuc'), false) . '>Natural</option>';
                    echo '<option value="2" ' . selected(2, get_option('GTIPFETipoRuc'), false) . '>Jurídico</option>';
                    echo '</select>';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>RUC</label></td>
                <td>
                    <?php
                    $GTIPFERuc = get_option('GTIPFERuc', '');
                    echo '<input type="text" required  id="GTIPFERuc" name="GTIPFERuc" value="' . esc_attr($GTIPFERuc) . '" />';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>Dígito verificador</label></td>
                <td>
                    <?php
                    $GTIPFEDV = get_option('GTIPFEDV', '');
                    echo '<input type="text" required  id="GTIPFEDV" name="GTIPFEDV" value="' . esc_attr($GTIPFEDV) . '" />';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>Nombre Emisor</label></td>
                <td>
                    <?php
                    $GTIPFENombEm = get_option('GTIPFENombEm', '');
                    echo '<input type="text" required  id="GTIPFENombEm" name="GTIPFENombEm" value="' . esc_attr($GTIPFENombEm) . '" />';
                    ?>
                </td>
            </tr>

            <tr>
                <td><label>Coordenadas Emisor (Latitud)</label></td>
                <td>
                    <?php
                    $GTIPFEGTIPFECoordEmLat = get_option('GTIPFEGTIPFECoordEmLat', '');
                    echo '<input type="text" required  id="GTIPFEGTIPFECoordEmLat" name="GTIPFEGTIPFECoordEmLat" value="' . esc_attr($GTIPFEGTIPFECoordEmLat) . '" />';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>Coordenadas Emisor (Longitud)</label></td>
                <td>
                    <?php
                    $GTIPFECoordEmLong = get_option('GTIPFECoordEmLong', '');
                    echo '<input type="text" required  id="GTIPFECoordEmLong" name="GTIPFECoordEmLong" value="' . esc_attr($GTIPFECoordEmLong) . '" />';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>Dirección Emisor</label></td>
                <td>
                    <?php
                    $GTIPFEDirecEm = get_option('GTIPFEDirecEm', '');
                    echo '<input type="text" required  id="GTIPFEDirecEm" name="GTIPFEDirecEm" value="' . esc_attr($GTIPFEDirecEm) . '" />';
                    ?>
                </td>
            </tr>



            <tr>
                <td><label>Provincia</label></td>
                <td>
                    <?php
                    $GTIPFEProv = get_option('GTIPFEProv', '');
                    echo '<select style="width:100%" id="GTIPFEProv" name="GTIPFEProv"></select>';


                    ?>
            </tr>
            <tr>
                <td><label>Distrito</label></td>
                <td>
                    <?php
                    $GTIPFEDistr = get_option('GTIPFEDistr', '');
                    echo '<select style="width:100%" id="GTIPFEDistr" name="GTIPFEDistr"></select>';
                    ?>
                </td>
            </tr>

            <tr>
                <td><label>Corregimiento</label></td>
                <td>
                    <?php
                    $GTIPFECorreg = get_option('GTIPFECorreg', '');
                    echo '<select style="width:100%" id="GTIPFECorreg" name="GTIPFECorreg"></select>';
                    ?>
                </td>
            </tr>

            <tr>
                <td><label>Teléfonos Emisor</label></td>
                <td>
                    <?php
                    $GTIPFETfnEm = get_option('GTIPFETfnEm', '');
                    echo '<input class="tags" type="text" required  id="GTIPFETfnEm" name="GTIPFETfnEm" value="' . esc_attr($GTIPFETfnEm) . '" />';
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>Correos Electrónicos Emisor</label></td>
                <td>
                    <?php
                    $GTIPFECorElectEmi = get_option('GTIPFECorElectEmi', '');
                    echo '<input class="tags" type="text" required  id="GTIPFECorElectEmi" name="GTIPFECorElectEmi" value="' . esc_attr($GTIPFECorElectEmi) . '" />';
                    ?>
                </td>
            </tr>
        </tbody>
    </table>




    <input style="margin-top:20px" type="submit" value="Guardar Configuración" name="submit" class="button-primary">

</form>
    </div>
</div>


<?php
$GTIPFEProv =strlen($GTIPFEProv) == 0 ? 0 : $GTIPFEProv;
$GTIPFEDistr =strlen($GTIPFEDistr) == 0 ? 0 : $GTIPFEDistr;

?>

<script>

    jQuery(document).ready(function ($) {

        jQuery('#GTIPFEDistr').select2();
        jQuery('#GTIPFECorreg').select2();
        var corregimientos;
        $.ajax(
            {
                url: '/wordpress/wp-admin/admin-ajax.php',
                dataType: 'json',
                data: {
                    'action': 'GTIPFE_provincias_get',
                },
                success: function (data) {
                    provincias = data;
                    let options = jQuery.map(provincias, function (item) {
                        return {
                            id: item.IGTIPFEProvincia,
                            text: item.Descripcion
                        };
                    })
                    $('#GTIPFEProv').select2({
                        data: options,
                    });

                  
                    // Escapar y validar valores PHP antes de insertarlos en el script
                    var GTIPFEProv = '<?php echo esc_js($GTIPFEProv); ?>';
                    var GTIPFEDistr = '<?php echo esc_js($GTIPFEDistr); ?>';

                    $('#GTIPFEProv').val(GTIPFEProv).trigger('change');
                    GTIPFE_getDistritos(GTIPFEProv);
                    GTIPFE_getCorregimientos(GTIPFEProv, GTIPFEDistr);
              
                },
                error: function (error) {
                    console.log(error)
                }
            }
        )

        $('#GTIPFEProv').on('select2:select', function (e) {
            let data = e.params.data;
            GTIPFE_getDistritos(data.id);
            let dataDis = $('#GTIPFEProv').select2('data');
            let idDis = dataDis[0].id;
            GTIPFE_getCorregimientos(data.id, idDis);
        });
        $('#GTIPFEDistr').on('select2:select', function (e) {
            let data = e.params.data;
            let dataProv = $('#GTIPFEProv').select2('data');
            let iGTIPFEProvincia = dataProv[0].id;
            GTIPFE_getCorregimientos(iGTIPFEProvincia, data.id);
        });

    });

    function GTIPFE_getDistritos(provincia) {
    jQuery.ajax(
        {
            url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
            dataType: 'json',
            data: {
                'action': 'GTIPFE_distritos_get',
                'provincia': provincia,
            },
            success: function (data) {
                distritos = data;
                let options = jQuery.map(distritos, function (item) {
                    return {
                        id: item.IGTIPFEDistrito,
                        text: item.Descripcion
                    };
                })
                jQuery('#GTIPFEDistr').html('').select2({
                    data: options
                });

                jQuery('#GTIPFEDistr').val('<?php echo esc_attr($GTIPFEDistr); ?>');
                jQuery('#GTIPFEDistr').trigger('change');
            },
            error: function (error) {
                console.log(error)
            }
        }
    )
}



    function GTIPFE_getCorregimientos(provincia, distrito) {
    jQuery.ajax({
        url: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>',
        dataType: 'json',
        data: {
            'action': 'GTIPFE_corregimiento_get',
            'provincia': provincia,
            'distrito': distrito,
        },
        success: function (data) {
            corregimientos = data;
            console.log(corregimientos);
            let options = jQuery.map(corregimientos, function (item) {
                return {
                    id: item.IGTIPFECorregimientos,
                    text: item.Descripcion
                };
            });
            jQuery('#GTIPFECorreg').html('').select2({
                data: options
            });

            // Escapar el valor de $GTIPFECorreg antes de asignarlo
            jQuery('#GTIPFECorreg').val('<?php echo esc_attr($GTIPFECorreg); ?>');
            jQuery('#GTIPFECorreg').trigger('change');
        },
        error: function (error) {
            console.log(error);
        }
    });
}

</script>
