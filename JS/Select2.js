
jQuery(document).ready(function($) {
    $('#GTIPFECorreg2').select2();
    var tipoRuc =    $('#billing_options_tiporuc').val();     
        
    SetLabel(tipoRuc);

    $('#billing_options_tiporuc').change(function(){
        var tipo =   $(this).find("option:selected").attr('value');     

        if(tipo ==1){
            jQuery('#billing_options_iTipoRec').val("02");
        }
        else if (tipo == 2){
            jQuery('#billing_options_iTipoRec').val("01");
        }
        
        SetLabel(tipo);
    });
    $('#billing_options_itiporec').change(function(){
        var tipo =   $(this).find("option:selected").attr('value');     
        
        if(tipo =="01" || tipo == "03"){
            $('#billing_options_tiporuc').val(2);
        }
        else if (tipo == "02" || tipo == "04"){
            $('#billing_options_tiporuc').val(1);
        }

        var tipoRuc =    $('#billing_options_tiporuc').val();     
        
        SetLabel(tipoRuc);
    });
    
});

function SetLabel(tipo){
    if(tipo ==1){
        jQuery('#rucLabel').html('Número de cédula o pasaporte');
    }
    else if (tipo == 2){
        jQuery('#rucLabel').html('Número de RUC');
    }
}
