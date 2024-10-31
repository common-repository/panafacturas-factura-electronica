<?php
class GTIPFEDocumento
{

    public function GTIPFE_ObtenerEmisor()
    {
        $ubicacion = new GTIPFEGetUbicacion();
        $ubicacionData = $ubicacion->GTIPFE_GenerarUbicacion(get_option('GTIPFECorreg'),get_option('GTIPFEProv'));
        $GTIPFETfnEm = explode(',', get_option('GTIPFETfnEm'));
        $GTIPFECorElectEmi = explode(',', get_option('GTIPFECorElectEmi'));

        $valordDV = get_option('GTIPFEDV');
        if (strlen($valordDV) === 3 && $valordDV[0] === '0') {
            $valordDV = substr($valordDV, 1);
        }

        $emisor = array(
            "gRucEmi" => array(
                "dTipoRuc" => get_option('GTIPFETipoRuc'),
                "dRuc" => get_option('GTIPFERuc'),
                "dDV" => $valordDV
            ),
            "dNombEm" => get_option('GTIPFENombEm'),
            "dSucEm" => "0001", //get_option('dSucEm'),
            "dCoordEm" => get_option('GTIPFEGTIPFECoordEmLat') . ',' . get_option('GTIPFECoordEmLong'),
            "dDirecEm" => get_option('GTIPFEDirecEm'),
            "gUbiEm" => array(
                "dCodUbi" => $ubicacionData['CodigoUbicacion'],
                "dCorreg" => $ubicacionData['Corregimiento'],
                "dDistr" => $ubicacionData['Distrito'],
                "dProv" => $ubicacionData['Provincia']
            ),


            "dTfnEm" => $GTIPFETfnEm,
            "dCorElectEmi" => $GTIPFECorElectEmi
        );
        return $emisor;
    }
    public function GTIPFE_ObtenerReceptor($receptorData)
    {
        $corregimiento = explode("-", $receptorData['corregimiento']);

        $ubicacion = new GTIPFEGetUbicacion();
        $ubicacionData = $ubicacion->GTIPFE_GenerarUbicacion($corregimiento[2],$corregimiento[0]);

        $receptor = array(
            "iTipoRec" => $receptorData['iTipoRec'],
            "gRucRec" => array(
                "dTipoRuc" => $receptorData['tiporuc'],
                "dRuc" => $receptorData['ruc']
            )
        );
        
        if($receptorData['dv'] != ""){
            $receptor["gRucRec"]["dDV"] = $receptorData['dv'];
        }
        
        $receptor["dNombRec"] = "{$receptorData['billing']['first_name']} {$receptorData['billing']['last_name']}";
        $receptor["dDirecRec"] = $receptorData['billing']['address_1'];
        
        $receptor["gUbiRec"] = array(
            "dCodUbi" => $ubicacionData['CodigoUbicacion'],
            "dCorreg" => $ubicacionData['Corregimiento'],
            "dDistr" => $ubicacionData['Distrito'],
            "dProv" => $ubicacionData['Provincia']
        );
        
        $receptor["dTfnRec"] = $receptorData['billing']['phone'];
        $receptor["dCorElectRec"] = $receptorData['billing']['email'];
        $receptor["cPaisRec"] = "PA";        

        return $receptor;

    }

    public function GTIPFE_ObtenerTotales($items, $order)
{

    $vloObtenerMedioPago = new GTIPFEObtenerMedioPago();
    $medioPago = $vloObtenerMedioPago->GTIPFE_ObtenerMedio($order->get_payment_method());

    $dTotNeto = 0;
    $dTotITBMS = 0;
    $dTotGravado = 0;
    $dVTot = 0;
    $dTotRec = 0;
    $dVuelto = 0;
    $dVlrCuota = 0;
    $dVTotItems = 0;

    foreach ($items as $item) {
        // Eliminar separadores de miles antes de convertir a número
        $dTotNeto += (float) str_replace(',', '', $item['gPrecios']['dPrItem']);
        $dTotITBMS += (float) str_replace(',', '', $item['gITBMSItem']['dValITBMS']);
        $dTotGravado = (float) $dTotITBMS;
        $dVTot += (float) str_replace(',', '', $item['gPrecios']['dValTotItem']);
        $dTotRec = (float) $dVTot;
        $dVlrCuota = (float) $dVTot;
        $dVTotItems = (float) $dVTot;
    }

    $totales = array(
        "dTotNeto" => (number_format((float) $dTotNeto, 2)),
        "dTotITBMS" => (number_format((float) $dTotITBMS, 2)),
        "dTotGravado" => (number_format((float) $dTotGravado, 2)),
        "dVTot" => (number_format((float) $dVTot, 2)),
        "dTotRec" => (number_format((float) $dTotRec, 2)),
        "dVuelto" => (number_format((float) $dVuelto, 2)),
        "iPzPag" => 1,
        "dNroItems" => count($items),
        "dVTotItems" => (number_format((float) $dVTotItems, 2)),
        "gFormaPago" => array(
            array(
                "iFormaPago" => $medioPago,
                "dVlrCuota" => (number_format((float) $dVlrCuota, 2)),
            )
        ),
    );

    return $totales;
}
	
    public function GTIPFE_GenerarLineas($items,$shipping)
    {
        $vloObtenerImpuesto = new GTIPFEObtenerImpuesto();
        $vloObtenerDescuento = new GTIPFEObtenerDescuento();
      
        $lineas = array();
        $lineCount = 1;
        foreach ($items as $item) {
            $product = wc_get_product($item['product_id']);
            $descuentoData = $vloObtenerDescuento->GTIPFE_ObtenerDescuentos($product, $item['quantity'], 2);
            $taxes = $item->get_taxes();
            $taxClass = $product->get_tax_class();
            $rates = array_shift($taxes);
            $rates = array_filter( $rates);
            $item_rate = array_shift($rates);
            
            $linea = array(
                "dSecItem" => $lineCount,
                "dDescProd" => $item['name'],
                "dCodProd" => $item['product_id'],
                "cUnidad" => $product->get_attribute('unidad-medida'),
                "dCantCodInt" => $item['quantity'],
                "gPrecios" => array(
                    "dPrUnit" => (number_format((float) $descuentoData['precioSinDescuento'], 2)),
                    "dPrUnitDesc" => (number_format((float) $descuentoData['descuento'], 2)),
                    "dPrItem" => (number_format((float) $item['total'], 2)),
                    "dValTotItem" => (number_format((float) $item['total'] + $item['total_tax'], 2))
                ),
                "gITBMSItem" => array(
                    "dTasaITBMS" => $vloObtenerImpuesto->GTIPFE_ObtenerCodigoTarifa($taxClass),
                    "dValITBMS" => (number_format((float) $item_rate, 2))
                ),
            );
            array_push($lineas, $linea);
            $lineCount++;
        }

        if($shipping['total']>0){

            $linea = array(
                "dSecItem" => $lineCount,
                "dDescProd" => $shipping['name'],
                "dCodProd" => rand(0, 50),
                "cUnidad" => 'und',
                "dCantCodInt" => 1,
                "gPrecios" => array(
                    "dPrUnit" => (number_format((float) $shipping['total'], 2)),
                    "dPrUnitDesc" => (number_format((float) 0, 2)),
                    "dPrItem" => (number_format((float) $shipping['total'], 2)),
                    "dValTotItem" => (number_format((float) $shipping['total'], 2))
                ),
                "gITBMSItem" => array(
                    "dTasaITBMS" => '00',
                    "dValITBMS" => (number_format((float) 0, 2))
                ),
            );
            array_push($lineas, $linea);

        }

        return $lineas;
    }

}

?>