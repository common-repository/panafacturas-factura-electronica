<?php
class GTIPFEObtenerDescuento
{
    public function GTIPFE_ObtenerDescuentos($pvoProducto, $cantidad, $CantDecimales)
    {
        $vlnPrecioRegular = (float) $pvoProducto->get_regular_price(); // Regular price
        $vlnPrecioVenta = (float) $pvoProducto->get_sale_price(); // Sale price
        $admin = is_admin();
        $variable = $pvoProducto->is_type('variable');
        //Only for discounted products
        if (($vlnPrecioRegular > $vlnPrecioVenta) && $vlnPrecioVenta > 0 && !$variable) {

            //Get the prices

            $vlnPrecioConDescuento = (float) $pvoProducto->get_price(); // rice with discount
            // discount amount

            $descuento = number_format((float) ($vlnPrecioRegular - $vlnPrecioConDescuento), $CantDecimales);

            return array(
                'precioSinDescuento' => $vlnPrecioRegular,
                'precioConDescuento' => $vlnPrecioConDescuento,
                'descuento' => $descuento,
            );
        }
        return array(
            'precioSinDescuento' => $vlnPrecioRegular,
            'precioConDescuento' => $vlnPrecioRegular,
            'descuento' => 0,
        );
    }
}
?>