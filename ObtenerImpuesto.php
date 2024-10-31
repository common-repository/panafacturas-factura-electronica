<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
include_once "Enum/EnumCodigoTarifa.php";
include_once "Enum/EnumTipoMoneda.php";
include_once "Enum/EnumCodigoImpuesto.php";
 
class GTIPFEObtenerImpuesto
{
  public function GTIPFE_ObtenerCodigoTarifa($pvnCodigoTarifa)
  {
      switch($pvnCodigoTarifa) 
      {
          case "tarifa_Exento" :
              $vlnCodigoTarifa=GTIPFEEnumCodigoTarifa::tarifa_Exento;
            break;
          case "tarifa_7" :
              $vlnCodigoTarifa=GTIPFEEnumCodigoTarifa::tarifa_7;
            break;
          case "tarifa_10" :
              $vlnCodigoTarifa=GTIPFEEnumCodigoTarifa::tarifa_10;
            break;
          case "tarifa_15" :
              $vlnCodigoTarifa=GTIPFEEnumCodigoTarifa::tarifa_15;
            break;
          default:
            $vlnCodigoTarifa=GTIPFEEnumCodigoTarifa::tarifa_Exento;
      }                
      return $vlnCodigoTarifa;               
  }
        
    public function GTIPFE_ObtenerMoneda($pvnMoneda)
    {
        switch($pvnMoneda) 
        {
            case GTIPFEEnumTipoMoneda::MonedaUSD:
                $vlnMoneda=GTIPFEEnumTipoMoneda::USD;
            break;
            default:
                $vlnMoneda=GTIPFEEnumTipoMoneda::MonedaInvalida;
        }
        return $vlnMoneda;
    }
}
?>