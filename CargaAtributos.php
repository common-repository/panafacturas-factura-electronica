<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action('admin_init', 'GTIPFE_QuadLayers_add_product_attributes');

function GTIPFE_QuadLayers_add_product_attributes()
{

    $unidadesJsonFileContents = file_get_contents(__DIR__ . "/Files/Unidades.json", true);
    $unidadesList = json_decode($unidadesJsonFileContents, true);
    $atts = array(

        'Unidad Medida' => $unidadesList
    );
    foreach ($atts as $key => $values) {

        new GTIPFEadd_attribute($key, $values);
    }
}


/*
 * Register a global woocommerce product attribute Class.
 *
 * @param str   $nam | name of attribute
 * @param arr   $vals | array of variations
 * 
 */
class GTIPFEadd_attribute
{
    public function __construct($nam, $vals)
    {
        $attrs = array();
        $attributes = wc_get_attribute_taxonomies();
        foreach ($attributes as $key => $value) {
            array_push($attrs, $attributes[$key]->attribute_name);
        }
        if (!in_array($nam, $attrs)) {
            $args = array(
                'slug' => sanitize_title($nam),
                'name' => $nam,
                'orderby' => 'menu_order',
                'has_archives' => ""
            );

            wc_create_attribute($args);
        }

        $this->GTIPFEadd_var($nam, $vals);
    }

    public function GTIPFEadd_var($nam, $vals)
    {
        $taxonomy = 'pa_' . sanitize_title($nam);

        foreach ($vals as $unidad) {

            $name = $unidad['Simbolo'];
            $description = $unidad['Comentario'];
            if (!term_exists($name, $taxonomy)) {


                $add = wp_insert_term($name, $taxonomy, array('description' => $description, 'slug' => lcfirst($name)));
            } else {

            }
        }


    }
}
?>