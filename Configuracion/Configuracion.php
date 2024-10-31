<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Activar la página de Pana Facturas
function GTIPFE_activate_page()
{
}

// Agregar elemento al menú
add_action('admin_menu', 'GTIPFE_AddMenuAdministrator');
function GTIPFE_AddMenuAdministrator()
{
    add_menu_page('Pana Facturas', 'Pana Facturas', 'manage_options', 'GTIPFE_Factura', 'GTIPFE_admin_page_html', 'dashicons-analytics', 5);
}

function GTIPFE_admin_page_html()
{
    // Verificar capacidades del usuario
    if (!current_user_can('manage_options')) {
        return;
    }

    // Obtener la pestaña activa del parámetro $_GET
    $default_tab = null;
    $tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : $default_tab;

    // Agregar nonce al formulario de la página de administración
    function GTIPFE_add_nonce_to_admin_page()
    {
        ?>
        <input type="hidden" name="gtipfe_admin_nonce" value="<?php echo esc_attr(wp_create_nonce('gtipfe_admin_nonce')); ?>" />
        <?php
    }
    add_action('admin_init', 'GTIPFE_add_nonce_to_admin_page');

    // Verificar nonce antes de procesar los datos del formulario
    function GTIPFE_process_admin_form() {
        // Verificar si se ha enviado el nonce en el formulario
        if (isset($_POST['gtipfe_admin_nonce'])) {
            // Sanear y desinfectar el valor del nonce
            $nonce = sanitize_text_field(wp_unslash($_POST['gtipfe_admin_nonce']));

            // Verificar la validez del nonce
            if (wp_verify_nonce($nonce, 'gtipfe_admin_nonce')) {
                // Procesar los datos del formulario aquí
            } else {
                // El nonce no es válido, mostrar un mensaje de error o tomar alguna acción
                // Por ejemplo:
                wp_die('Nonce no válido');
            }
        } else {
            // El nonce no se ha enviado, mostrar un mensaje de error o tomar alguna acción
            // Por ejemplo:
            wp_die('Nonce no encontrado en la solicitud');
        }
    }

    add_action('admin_post', 'GTIPFE_process_admin_form');
    ?>
    <!-- Nuestro contenido de página de administración debe estar dentro de .wrap -->
    <div class="wrap">
        <nav class="nav-tab-wrapper GTIPFE_Factura_tabs">
            <a href="?page=<?php echo esc_attr('GTIPFE_Factura'); ?>"
                class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>">Configuración
                General</a>
            <a href="?page=<?php echo esc_attr('GTIPFE_Factura'); ?>&tab=<?php echo esc_attr('Emisor'); ?>"
                class="nav-tab <?php if ($tab === 'Emisor') : ?>nav-tab-active<?php endif; ?>">Configuración
                Emisor</a>
            <a href="?page=<?php echo esc_attr('GTIPFE_Factura'); ?>&tab=<?php echo esc_attr('Exportar'); ?>"
                class="nav-tab <?php if ($tab === 'Exportar') : ?>nav-tab-active<?php endif; ?>">Exportar
                Configuración</a>
            <a href="?page=<?php echo esc_attr('GTIPFE_Factura'); ?>&tab=<?php echo esc_attr('Importar'); ?>"
                class="nav-tab <?php if ($tab === 'Importar') : ?>nav-tab-active<?php endif; ?>">Importar
                Configuración</a>
        </nav>

        <div class="tab-content">
            <?php
            switch ($tab) :
                case 'Emisor':
                    include(plugin_dir_path(__FILE__) . '/Tabs/TabEmisor.php');
                    break;
                case 'Importar':
                    include(plugin_dir_path(__FILE__) . '/Tabs/ImportData.php');
                    break;
                case 'Exportar':
                    include(plugin_dir_path(__FILE__) . '/Tabs/ExportData.php');
                    break;
                default:
                    include(plugin_dir_path(__FILE__) . '/Tabs/TabConfiguracion.php');
                    break;
            endswitch;
            ?>
        </div>
    </div>
<?php
}

