<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function GTIPFE_add_additional_rate_tax_rates()
{

    $tax_classes = WC_Tax::get_tax_classes();
    $impuestos = array(
        array('label'=>'tarifa_Exento','monto'=>0),
        array('label'=>'tarifa_7','monto'=>7),
        array('label'=>'tarifa_10','monto'=>10),
        array('label'=>'tarifa_15','monto'=>15),
    );

foreach ($impuestos as $impuesto) {

    if (!in_array($impuesto['label'], $tax_classes)){
        $tax_class = WC_Tax::create_tax_class($impuesto['label'],$impuesto['label']);
        WC_Cache_Helper::invalidate_cache_group( 'taxes' );
        WC_Cache_Helper::get_transient_version( 'shipping', true );
        //Attached the tax_rate to tax_class
        $tax_rate_data = array(
            'tax_rate_country' => '*',
            'tax_rate_state' => '*',
            'tax_rate' => $impuesto['monto'],
            'tax_rate_name' => str_replace("_"," ",$impuesto['label']).'%' ,
            'tax_rate_priority' => 1,
            'tax_rate_compound' => 0,
            'tax_rate_shipping' => 0,
            'tax_rate_order' => 0,
            'tax_rate_class' =>$impuesto['label']
        );
        
        $tax_rate_id = WC_Tax::_insert_tax_rate( $tax_rate_data );

    }
    
}

}

add_action('init', 'GTIPFE_add_additional_rate_tax_rates');
?>