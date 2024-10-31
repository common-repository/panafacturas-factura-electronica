<?php
    abstract class GTIPFEEnumCodigoImpuesto
    {
        const ImpuestoValorAgregado=1;
        const ImpuestoSelectivoConsumo=2;
        const ImpuestoUnicoCombustibles=3;
        const ImpuestoBebidasAlcoholicas=4;
        const ImpuestoBebidasEnvasadasYJabonesTocador=5;
        const ImpuestoAlTabaco=6;
        const ImpuestoIvaEspecial=7;
        const ImpuestoBienesUsados=8;
        const ImpuestoAlCemento=12;
        const ImpuestosOtros=99;
        const ImpuestoInvalido=-1;

        const ImpuestoIVA="IVA";
        const ImpuestoConsumo="Selectivo Consumo";
        const ImpuestoCombustibles="Combustibles";
        const ImpuestoBebidaAlcholica="Bebidas Alcoholicas";
        const ImpuestoBebidaEnvasadaJabonesTocador="Bebidas Envasadas";
        const ImpuestoTabaco="Tabaco";
        const ImpuestoIvaCalcEspecial="Iva Especial";
        const ImpuestoBienUsado="Bienes Usados";
        const ImpuestoCemento="Cemento";
        const ImpuestoOtros="Otros";
        
    }
?>