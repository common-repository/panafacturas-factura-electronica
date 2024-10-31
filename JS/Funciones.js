const DocumentosAutorizados = 1;
const DocumentosDiplomaticos = 2;
const DocumentosEspeciales = 3;
const DocumentosHacienda = 4;
const DocumentosV = 5;
const DocumentosIX = 6;
const DocumentosXVII = 7;
const DocumentosOtro = 99;
const DocumentoInvalido = -1;

const DocumentoAutorizado = 'Compras autorizadas';
const DocumentoDiplomatico = 'Ventas exentas a diplomáticos';
const DocumentoEspecial = 'Autorizado por ley especial';
const DocumentoHacienda = 'Exenciones de la dirección general de Hacienda';
const DocumentoV = 'Transitorio V';
const DocumentoIX = 'Transitorio IX';
const DocumentoXVII = 'Transitorio XVII';
const DocumentoOtro = 'Otros';

jQuery(document).ready(function($) {

    const boton = document.getElementById("agregarBoton");
    boton.addEventListener("click", function(evento) {

        var Cabys = document.getElementById("Cabys").value.trim();
        var Numero = document.getElementById("numero").value.trim();
        var Institucion = document.getElementById("institucion").value.trim();
        var Fecha = document.getElementById("fecha").value.trim();
        var Porcentaje = document.getElementById("porcentaje").value.replace('%', '').trim();
        var Documento = GTIPFE_ObtenerDocumentoExonerado(document.getElementById("documento").value.trim());

        if (GTIPFE_ValidarCampos()) {
            var Exoneracion = document.getElementById("exon").value;
            var NuevaExoneracion = "Código Cabys" + ";" + Cabys + ";" + "Numero Exoneración" + ";" + Numero + ";" + "Institución" + ";" + Institucion + ";" + "Fecha Exoneración" +
                ";" + Fecha + ";" + "Porcentaje Exonerado" + ";" + Porcentaje + ";" + "Tipo Documento" + ";" + Documento + ";";

            var ExoneracionVisual = document.getElementById("exon1").value;
            var NuevaExoneracionVisual = "\n" + " Código Cabys" + ": " + Cabys + "\n" + " Numero Exoneración" + ": " + Numero + "\n" + " Institución" + ": " + Institucion + "\n" + " Fecha Exoneración" +
                ": " + Fecha + "\n" + " Porcentaje Exonerado" + ": " + Porcentaje + "\n" + " Tipo Documento" + ": " + Documento + "\n";

            ExoneracionVisual += NuevaExoneracionVisual;
            document.getElementById("exon1").value = ExoneracionVisual;

            Exoneracion += NuevaExoneracion;
            document.getElementById("exon").value = Exoneracion;

            document.getElementById("Cabys").value = "";
            document.getElementById("numero").value = "";
            document.getElementById("institucion").value = "";
            document.getElementById("fecha").value = "";

            alert("Se agregó correctamente");

        } else {

            alert("La información ingresada es incorrecta");
        }
    });

    const botonBorrar = document.getElementById("borrarBoton");
    botonBorrar.addEventListener("click", function(evento) {

        document.getElementById("exon").value = "";
        document.getElementById("exon1").value = "";
    });

    const botonIdBorrar = document.getElementById("borrarIdBoton");
    botonIdBorrar.addEventListener("click", function(evento) {

        var codigocabys = document.getElementById("cabysborrar").value;
        codigocabys = "Código Cabys;" + codigocabys;
        var datosexoneracion = document.getElementById("exon").value;
        var posicion = datosexoneracion.indexOf(codigocabys);
        var datosprevios = datosexoneracion.slice(0, posicion);
        var tamano = codigocabys.length;
        var substrin = datosexoneracion.slice(posicion + tamano + 1, datosexoneracion.length);
        var PosicionSiguienteCodigo = substrin.indexOf("Código Cabys;");
        var datosposteriores = substrin.slice(PosicionSiguienteCodigo, substrin.length);
        if (datosposteriores.trim() == ";") {
            datosposteriores = "";
        }
        document.getElementById("exon").value = datosprevios + datosposteriores;
        document.getElementById("exon1").value = GTIPFE_FormatoExoneracion(datosprevios + datosposteriores);
        alert("Producto eliminado correctamente");
    });
});

function GTIPFE_FormatoExoneracion(Exoneracion) {
    var NuevaCadena = " ";
    var Validador = false;
    var Contador = 0;
    //const  Exoneraacion = CadenaExoneraacion.split(/(?=;)/g)

    for (var i = 0; i < Exoneracion.length; i++) {

        if (Contador == 6) {
            Contador = 0;
            NuevaCadena = NuevaCadena + "\n ";
        }

        if (Exoneracion.charAt(i) == ";") {
            if (Validador) {
                NuevaCadena = NuevaCadena + "\n ";
                Validador = false;
                Contador++;
            } else {
                NuevaCadena = NuevaCadena + ": ";
                Validador = true;
            }

        } else {
            NuevaCadena = NuevaCadena + Exoneracion.charAt(i);
        }
    }

    return NuevaCadena;
}

function GTIPFE_ObtenerDocumentoExonerado(pvcDocumento) {
    var vlnDocumentoExoneracion = "";

    switch (pvcDocumento) {
        case DocumentoAutorizado:
            vlnDocumentoExoneracion = DocumentosAutorizados;
            break;
        case DocumentoDiplomatico:
            vlnDocumentoExoneracion = DocumentosDiplomaticos;
            break;
        case DocumentoEspecial:
            vlnDocumentoExoneracion = DocumentosEspeciales;
            break;
        case DocumentoHacienda:
            vlnDocumentoExoneracion = DocumentosHacienda;
            break;
        case DocumentoV:
            vlnDocumentoExoneracion = DocumentosV;
            break;
        case DocumentoIX:
            vlnDocumentoExoneracion = DocumentosIX;
            break;
        case DocumentoXVII:
            vlnDocumentoExoneracion = DocumentosXVII;
            break;
        case DocumentoOtro:
            vlnDocumentoExoneracion = DocumentosOtro;
            break;
    }

    return vlnDocumentoExoneracion;
}

function GTIPFE_ValidarCampos() {

    var Cabys = document.getElementById("Cabys").value;
    var Numero = document.getElementById("numero").value;
    var Institucion = document.getElementById("institucion").value;
    var Fecha = document.getElementById("fecha").value;
    var Porcentaje = document.getElementById("porcentaje").value.replace('%', '');
    var Documento = GTIPFE_ObtenerDocumentoExonerado(document.getElementById("documento").value);

    if (Cabys != "" && Numero != "" && Institucion != "" && Fecha != "" && Porcentaje != "" && Documento != "") {
        return true;
    } else {
        return false;
    }
}