<?php
abstract class GTIPFEEnumMedioPago
{
    const Efectivo='02';
    
    const TarjetaCredito='03';
    const Tarjeta='04';
    const TarjetaFidelizacion='05';
    const Vale='06';
    const TarjetaRegalo='07';
    
    const Transferencia='08';
    const Cheque='09';
    const PuntoDePago='10';
    const Otro=99;
    const Invalido=-1;

    const Greenpay="greenpay";
    const Transferencias="bacs";
    const Cheques="cheque";
    const ContraRembolso="cod";
    const PaypalEstandar="paypal";
    const PagosPei="pei_greepay";
    const Stripe="stripe";
    const Redsys="redsys";
    const Check="nmwoo_2co";
    const Vpos="misha";
}
?>