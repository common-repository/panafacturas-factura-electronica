<?php
// Añadir etiquetas
add_filter('admin_init', 'GTIPFE_Register_my_general_settings_fields');

function GTIPFE_Register_my_general_settings_fields()
{
    register_setting('general', 'GTIPFETitulo_Ajustes_GTI', 'esc_attr');
    add_settings_field('GTIPFETitulo_Ajustes_GTI', '<h3> Ajustes FACTURA GTI</h3>', 'GTIPFE_CrearTituloAjustes', 'general');

    register_setting('general', 'GTIPFENumero_de_Cuenta', 'esc_attr');
    add_settings_field('GTIPFENumero_de_Cuenta', '<label for="GTIPFENumero_de_Cuenta">' . 'Numero de Cuenta' . '</label>', 'GTIPFE_CrearInputNumeroCuenta', 'general');

    register_setting('general', 'GTIPFEDecimales', 'esc_attr');
    add_settings_field('GTIPFEDecimales', '<label for="GTIPFEDecimales">' . 'Decimales' . '</label>', 'GTIPFE_CrearInputDecimales', 'general');

    register_setting('general', 'GTIPFESufijo', 'esc_attr');
    add_settings_field('GTIPFESufijo', '<label for="GTIPFESufijo">' . 'Sufijo de Exoneración' . '</label>', 'GTIPFE_CrearInputSufijo', 'general');

    register_setting('general', 'GTIPFECodigo_Actividad', 'esc_attr');
    add_settings_field('GTIPFECodigo_Actividad', '<label for="GTIPFECodigo_Actividad">' . 'Codigo de Actividad' . '</label>', 'GTIPFE_CrearInputCodigoActividad', 'general');

    register_setting('general', 'GTIPFEUsuario', 'esc_attr');
    add_settings_field('GTIPFEUsuario', '<label for="GTIPFEUsuario">' . 'Usuario' . '</label>', 'GTIPFE_CrearInputUsuario', 'general');

    register_setting('general', 'GTIPFEClave', 'esc_attr');
    add_settings_field('GTIPFEClave', '<label for="GTIPFEClave">' . 'Contraseña' . '</label>', 'GTIPFE_CrearInputClave', 'general');
}

// Añadir campos de entrada
function GTIPFE_CrearTituloAjustes()
{
    echo '<h3> Ajustes FACTURA GTI</h3>';
}

function GTIPFE_CrearInputNumeroCuenta()
{
    $Cuenta = get_option('GTIPFENumero_de_Cuenta', '');
    echo '<input type="number" id="GTIPFENumero_de_Cuenta" name="GTIPFENumero_de_Cuenta" value="' . esc_attr($Cuenta) . '" />';
}

function GTIPFE_CrearInputCodigoActividad()
{
    $Actividad = get_option('GTIPFECodigo_Actividad', '');
    echo '<input type="number" id="GTIPFECodigo_Actividad" name="GTIPFECodigo_Actividad" value="' . esc_attr($Actividad) . '" />';
}

function GTIPFE_CrearInputUsuario()
{
    $vlcUsurio = get_option('GTIPFEUsuario', '');
    echo '<input type="text" id="GTIPFEUsuario" name="GTIPFEUsuario" value="' . esc_attr($vlcUsurio) . '" />';
}

function GTIPFE_CrearInputDecimales()
{
    $vlcDecimales = get_option('GTIPFEDecimales', '');
    echo '<input type="number" id="GTIPFEDecimales" name="GTIPFEDecimales" oninput="if (this.value > 5) {
        this.value = 2; 
    }" min="1" max="5" value="' . esc_attr($vlcDecimales) . '" />';
}

function GTIPFE_CrearInputClave()
{
    $vlcContrasenia = get_option('GTIPFEClave', '');
    echo '<input type="password" id="GTIPFEClave" name="GTIPFEClave" value="' . esc_attr($vlcContrasenia) . '" />';
}

function GTIPFE_CrearInputSufijo()
{
    $vlcSufijo = get_option('GTIPFESufijo', '');
    echo '<input type="text" id="GTIPFESufijo" name="GTIPFESufijo" value="' . esc_attr($vlcSufijo) . '" />';
}
?>
