<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
include_once 'Enum/EnumMedioPago.php';

class GTIPFEObtenerMedioPago
{
  public function GTIPFE_ObtenerMedio($pvcMedio)
  {
    switch ($pvcMedio) {
      case GTIPFEEnumMedioPago::Greenpay:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::Tarjeta;
        break;
      case GTIPFEEnumMedioPago::Transferencias:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::Transferencia;
        break;
      case GTIPFEEnumMedioPago::Cheques:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::Cheque;
        break;
      case GTIPFEEnumMedioPago::ContraRembolso:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::PuntoDePago;
        break;
      case GTIEnumMedioPago::PaypalEstandar:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::Tarjeta;
        break;
      case GTIPFEEnumMedioPago::PagosPei:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::Tarjeta;
        break;
      case GTIPFEEnumMedioPago::Stripe:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::Tarjeta;
        break;
      case GTIPFEEnumMedioPago::Redsys:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::Tarjeta;
        break;
      case GTIPFEEnumMedioPago::Check:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::Tarjeta;
        break;
      case GTIPFEEnumMedioPago::Vpos:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::Tarjeta;
        break;
      default:
        $vlnCodigoTarifa = GTIPFEEnumMedioPago::Invalido;
    }
    return $vlnCodigoTarifa;
  }
}
?>