/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($){
    gestThumb();
    gestOpt();
    gestHelper();
    gestMenuNav();
    $('#color-picker').iris({
        hide: false, 
        palettes: true
    });
    $('#color-picker-paginatore').iris({
        hide: false, 
        palettes: true
    });
    if(Zindex>0){
        $('#prev, #next').css({
            "z-index":Zindex+3
        });
    }
    $('#anteprimaFoto').cycle({
        trimeot:0,
        // pager: '#paginatore-thumb',
        prev: '#prev',
        next: '#next',
        timeout: speed,
        fx:fx
    })
    // INIZIO MODULO DRAG-DROP PER GESTIRE ORDINE DELLO SLIDER!!!

    $('#salvaOrdine').attr('disabled','disabled').addClass('YS-button-disabled');
     
    $('#paginatore-thumb').sortable({
        revert: true,
        opacity: 0.55,
        zIndex: 100,
        beforeStop: function( event, ui ) {
            $('#salvaOrdine').removeAttr('disabled').removeClass('YS-button-disabled');
        }
    });
    $('#salvaOrdine').click(function(evt){
        evt.preventDefault();
        var id= new Array();
        
        $.each($('#paginatore-thumb').find('img'),function(i, el){
            var temp;
            temp= $(this).attr('id');
            temp=temp.split('slider-foto_');
            id[i]=temp[1];
        });
        salvaOrdine(encodeURIComponent(id));
    })
    
    $('#YS-update-foto').click(function(evt){
        var altFoto = $('#AltFoto').attr('value');
        var titleFoto = $('#TitleFoto').attr('value');
        if(altFoto.length>1 && titleFoto.length>1){
            return;
        }else{
            evt.preventDefault();
            $('#risposta').html('<span class="errore-save-order">I campi Alt e Title sono obbligatori per ottimizzare il SEO, sei pregato di riempirli :)</span>')
        }
    });
    $('#YS-delete-foto').click(function(evt){
        evt.preventDefault();
        var nomeFoto = $('#NomeFoto').attr('value');
        $.ajax({ //passare il name!!!!
            url: urlo+'?action=YS-deleteUploadedFoto',
            type:'post',
            data: 'nomeImg='+nomeFoto,
            success: function(data){
                if(typeof(data)=== 'string') data=JSON.parse(data);
                if(data.OK){
                    $('#risposta').html('');
                    $('#risposta').html('<span class="save-order-ok">'+data.message+'</span>');
                    $('#srcFoto').attr('value','');
                    $('#NomeFoto').attr('value','');
                   // $('#result2').fadeOut('slow',function(){
                        $('#result2').html('');
                        $('#YS-delete-foto').fadeOut('slow');
                        $('#YS-delete-foto').attr('disabled','disabled').addClass('YS-button-disabled');
                        $('.qq-upload-button input').removeAttr('disabled','disabled');
                        $('.qq-upload-button').removeClass('qq-upload-button-disabled');
                   // });
                }else{
                    $('#risposta').html('');
                    $('#risposta').html('<span class="errore-save-order">'+data.message+'</span>');
                }
            }
           
        });
    })
    $('#YS-form-config').submit(function(evt){
        //        if(validation){
        //            evt.preventDefault();
        //            DialogHelper('Validazione configurazione non superata! controlla tutti i campi');
        ////            $.each(ids,function(i,el){
        ////                console.log(ids[i]);
        ////                $('#'+ids[i]).css({"border":"1px solid red"});
        ////            })
        ////            ids=[];
        //            validation=false;
        //        }
        })
});


var validationWidth=false;
var validationHeight=false;
var validationSpeed=false;
var validationColor1=false;
var validationColor2=false;
var dentro=true
var ids= new Array();
function gestOpt(){
    $('#wrapper-color-paginatore').fadeOut('slow');
    $('#wrapper-is-responsive').fadeOut('slow');
    $('#YS-opt-'+attivoDisattivo).attr('selected', 'selected');
    $('#YS-opt-'+optSlider).attr('selected','selected');
    $('#YS-opt-p-'+optPaginatore).attr('selected','selected');
    $('#YS-opt-'+responsive).attr('selected','selected');
    if($('#YS-opt-paginatore').attr('value')=='attivo')
        $('#wrapper-color-paginatore').fadeIn('slow');
    if($('#YS-opt-responsive-select').attr('value')=='custom')
        $('#wrapper-is-responsive').fadeIn('slow');
    $('#YS-opt-paginatore').on('change',function(e){
        if($(this).attr('value')=='attivo'){
            $('#wrapper-color-paginatore').fadeIn('slow');
        }else{
            $('#wrapper-color-paginatore').fadeOut('slow');
        }
    });
    $('#YS-opt-responsive-select').on('change',function(){
        if($(this).attr('value')=='responsive'){
            $('#wrapper-is-responsive').fadeOut('slow');
        }else{
            $('#wrapper-is-responsive').fadeIn('slow');
        } 
    });
    $('#width, #height, #durata-transizione').focusin(function(){
       
        if(validationWidth){
            $('#width').css({
                "border":"1px solid red"
            });
            validationWidth=false;
        }
        if(validationHeight){
           
        }
    })
    $('#width, #height, #durata-transizione').focusout(function(e){ //prevent sul % e altri caratteri NON numerici
        var id=$(this).attr('id');
        if(isNaN($('#width').attr('value'))){
            DialogHelper('E\' possibile inserire solamente caratteri numerici nella configurazione della larghezza!');
            validationWidth= true;
            $(this).css({
                "border":"1px solid red"
            });
            
        }
        if(isNaN($('#height').attr('value'))){
            DialogHelper('E\' possibile inserire solamente caratteri numerici nella configurazione dell\'altezza!');
            validationHeight= true;
            $(this).css({
                "border":"1px solid red"
            });
        }
        if(isNaN($('#durata-transizione').attr('value'))){
            DialogHelper('E\' possibile inserire solamente caratteri numerici nella configurazione della velocità di animazione!');
            validationSpeed= true;
            $(this).css({
                "border":"1px solid red"
            });
        }
        if (e.keyCode == 53) {
            DialogHelper('Non è possibile inserire il carattere %');
        }
        
    });
    var dentro2=true;
    $('#color-picker, #color-picker-paginatore').focusout(function(e){
        var colore = $(this).attr('value');
        var id=$(this).attr('id');
        if(colore.length!= 7){
            DialogHelper('E\' permesso inserire al massimo 7 caratteri come codice colore!');
            if(id=='color-picker'){
                validationColor1= true;
            }else{
                validationColor2=true;
            }
        }
        if(colore[0]!='#'){
            DialogHelper('Attenzione al colore inserito!');
            if(id=='color-picker'){
                validationColor1= true;
            }else{
                validationColor2=true;
            }
        }
       
    });
    $(document).keydown(function(e){//prevent per il tasto return
        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });
    
}
function gestHelper(){
    $('.helper-dialog p').css({
        "display":"none"
    });
    $('.helper-dialog').append('<div class="help-button"></div>');
    $('.help-button').click(function(evt){
        evt.preventDefault();
        var mess= $(this).parent().find('p').html();
        DialogHelper(mess);
    })
}
function DialogHelper(mess){
    var str='<div class="wrapper-contenitore-info" style="position:relative!important; padding:20px; border-radius:10px!important;"><a href="#" id="chiudi-modal" style="width:40px; height:40px; display:block; position:absolute!important; right:-20px; top:-20px; background:url('+urloModal+'/images/close-modal.png) center no-repeat; text-indent:-5000px;">X</a><div class="contenuto-modal"><div style="border-radius:10px;">'+mess+'</div></div>';
    $.blockUI({
        css:{
            "border":"none",
            "cursor":"default",
            "border-radius":"10px"
        },
        message: str,
        onBlock:function(){
            $('#chiudi-modal').click(function(evt){
                evt.preventDefault();
                $.unblockUI();
            });
        }
    });
}

function gestMenuNav(){
    $('.YS-indice-config li a').click(function(evt){
        evt.preventDefault();
        var cliccato = $(this).attr('id');
        var id = cliccato.substring(4,cliccato.length);
        var offset = $('.goto-'+id).offset().top;
        $("body, html").animate({
            scrollTop : offset+380
        }, 800,function(){
            $('body, html').animate({
                scrollTop: offset-45
            },400)
        })
        
    })
}

//DEPRECATA
function gestConfiguration(){
    //  console.log('entrato'); 
    var attivo = $('#attivo').val();
    $('#attivo').click(function(){
        if(attivo=="false"){
            $('#attivo').val('true');
        }else{
            $('#attivo').val('false');
        }
    });
    
    if(attivo==false){
        $('#anteprimaFoto').cycle('destroy');
    }else{
        $('#anteprimaFoto').cycle({
            trimeot:0,
            prev: '#prev',
            next: '#next',
            pagerAnchorBuilder: function(idx, slide) { 
                return;
       
            }
        });
    }
}
//FINE DEPRECATA

function salvaOrdine(data){
    //console.log(data)
    $.ajax({
        url: urlo+'?action=YS-saveOrder',
        type:'post',
        data: 'newOrder='+data,
        success: function(data){
            if(typeof(data)=== 'string') data=JSON.parse(data);
            $('#rispostaOrdine').html(data.message);
            if(data.OK){
                $('#anteprimaFoto').cycle('destroy');
                $('#anteprimaFoto').html('LOADER!!!!');
                $.ajax({
                    url: urlo+'?action=getNewSliderOrder',
                    type:'post',
                    success: function(data){
                        if(typeof(data)=== 'string') data=JSON.parse(data);
                    
                        $('#anteprimaFoto').html(''); //cancello il loader
                        $('#anteprimaFoto').html(data.message); //riunserisco le foto
                        $('#rispostaOrdine').html('');
                        if(data.OK){
                            $('#anteprimaFoto').cycle({
                                trimeot:0,
                                prev: '#prev',
                                next: '#next',
                                timeout: speed,
                                fx:fx
                            });
                        }
                    }
                });
            }
        }
    })
}
/**
 * FINE MODULO RIPOSIZIONAMENTO
 */

/**
 * Modulo PopUp per gestione informazioni
 */
function gestThumb(){
    
    $('.wrapper-thumb').find('div').stop().fadeOut('slow');

    $('.wrapper-thumb').hover(
        function(){
            $(this).find('div').stop().fadeIn('slow');
        },
        function(){
            $(this).find('div').stop().fadeOut('slow');
        });
    $('.wrapper-thumb div').hover(
        function(){
            var div = $(this).attr('class');
            var messaggio='';
            var classe='tooltip-up';
            switch (div){
                case 'area-modifica':
                    messaggio="Clicca per modificare Alt e title dell'immagine";
                    break;
                case 'area-cancellami':
                    messaggio ="Clicca per eliminare l'immagine";
                    break;
                case 'area-wrap':
                    messaggio="Clicca e trascina per cambiare l'ordine delle foto; ricordati di premere sul tasto salva ordine";
                    classe='tooltip-down';
                    break;
            }
            $(this).parent().append('<div class="tooltip '+classe+'" style="width:280px; color:#FFF; z-index:5; padding:10px; background:url('+urloModal+'/images/bg_tooltip.png); position:relative; border-radius:10px;">'+messaggio+'<div style="width:15px; height:15px; background:url('+urloModal+'/images/arrow_tooltip.png); top:-15px; left:22px; position:absolute; display:block;"></div></div>');
        },function(){
            $(this).parent().find('.tooltip').remove();
        }
        );
    $('.area-modifica').click(function(evt){
        evt.preventDefault();
        var id_foto=$(this).parent().find('img').attr('id');
        id_foto= id_foto.split('_');
        var info_txt= $('#info-foto_'+id_foto[1]).html();
        var mess= '<div class="wrapper-contenitore-info" style="position:relative!important; padding:20px; border-radius:10px!important;"><a href="#" id="chiudi-modal" style="width:40px; height:40px; display:block; position:absolute!important; right:-20px; top:-20px; background:url('+urloModal+'/images/close-modal.png) center no-repeat; text-indent:-5000px;">X</a><div class="contenuto-modal"><div style="border-radius:10px;">'+info_txt+'<div><div class="blocco-modal-2"><input type="hidden" value="'+id_foto[1]+'" name="idFoto" id="idFoto"/><br /><input type="button" name="" value="Aggiorna dati" id="AggiornaModifiche" style="border-radius:5px!important;" /><div id="validateUpdate"></div></div></div></div>';
        Dialog(mess,'aggiorna');
        info_txt='';
    })
    $('.area-cancellami').click(function(evt){
        evt.preventDefault();
        var id_foto=$(this).parent().find('img').attr('id');
        var src_foto= $(this).parent().find('img').attr('src');
        id_foto= id_foto.split('_');
        var mess='<div class="wrapper-contenitore-info" style="position:relative!important; padding:20px; border-radius:10px!important;"><a href="#" style="width:40px; height:40px; display:block; position:absolute!important; right:-20px; top:-20px; background:url('+urloModal+'/images/close-modal.png) center no-repeat; text-indent:-5000px;" id="chiudi-modal">X</a><div class="contenuto-modal"  style="border-radius:10px;"><input type="hidden" value="'+id_foto[1]+'" name="idFoto" id="idFoto"/> Sicuri di voler eliminare questa foto? <div style="clear:both"></div> <img style="margin-top: 20px;margin-bottom:20px;" src="'+src_foto+'" width="100" height="100" /> <div style="clear:both"></div> <input type="button" class="YS-button" name="" value="Elimina" id="eliminaFoto" /> <div style="clear:both"></div><input type="button" class="YS-button" name="" value="Annulla" id="annullaElimina" /><div id="rispostaElimina"></div></div></div>';
        Dialog(mess,'cancella');
        src_foto='';
    })
}
//controllare all'aggiornamento galleria
function Dialog(str, funzione){
    var mess=str
    $.blockUI({
        css:{
            "border":"none",
            "cursor":"default",
            "border-radius":"10px"
        },
        message: mess,
        onBlock:function(){
            if(funzione=='aggiorna'){ //parte di aggiornamento
                $('#AggiornaModifiche').click(function(evt){
                    evt.preventDefault();
                    // #validateUpdate
                    var id= $('#idFoto').val();
                    var title= $('.contenuto-modal #modal-title-foto').val();
                    var alt= $('.contenuto-modal #modal-alt-foto').val();
                    var ok=true;
                    if(title.length<5){
                        $('#validateUpdate').html('Controllare la lunghezza del titolo');
                        ok=false
                    }
                    if(alt.length<5){
                        $('#validateUpdate').html("Controllare la lunghezza dell'alt");
                        ok=false;
                    }
                    if(ok){//chiamata per aggiornare
                        $.ajax({
                            url: urlo+'?action=updateAttributes',
                            type:'post',
                            data: 'id='+id+'&title='+title+'&alt='+alt,
                            success: function(data){
                                $('#validateUpdate').html('');
                                if(typeof(data)=== 'string') data=JSON.parse(data);
                                if(data.OK){
                                    $('#informazioniFoto').html('');
                                    $('#informazioniFoto').html(data.data);
                                }
                                $('#validateUpdate').html(data.message);
                            }
                        });
                    }
                });
            }
            else
            if(funzione=='cancella'){ //cancellazione foto
                $('#annullaElimina').click(function(){
                    $.unblockUI();
                });
                $('#eliminaFoto').click(function(evt){
                    evt.preventDefault();
                    var id= $('#idFoto').val();
                    $.ajax({
                        url: urlo+'?action=deleteFoto',
                        type:'post',
                        data: 'idFoto='+id,
                        success: function(data){
                            if(typeof(data)=== 'string') data=JSON.parse(data);
                            $('#rispostaElimina').html(data.message);
                            if(data.OK){
                                $('#anteprimaFoto').html(''); 
                                $('#anteprimaFoto').cycle('destroy');
                                $('#anteprimaFoto').html('LOADER!!!!');
                                //   console.log("QUI");
                                $.ajax({
                                    url: urlo+'?action=getNewSliderConfiguration',
                                    type:'post',
                                    success: function(data){
                                        if(typeof(data)=== 'string') data=JSON.parse(data);
                                        //  console.log("INTERNO!!");
                                        $('#anteprimaFoto').html(''); //cancello il loader
                                        $('#anteprimaFoto').html(data.message); //riunserisco le foto
                                        $('#rispostaOrdine').html('');
                                        $('#paginatore-thumb').html('');
                                        $('#paginatore-thumb').html(data.thumb);
                                        $('#informazioniFoto').html('');
                                        $('#informazioniFoto').html(data.info);
                                        gestThumb();
                                        if(data.OK){
                                            $('#anteprimaFoto').cycle({
                                                trimeot:0,
                                                //pager: '#paginatore-thumb',
                                                prev: '#prev',
                                                next: '#next',
                                                timeout: speed,
                                                fx:fx
                                            });
                                            $.unblockUI();
                                        }
                                        if(data.empty){//forzo a ricaricare
                                            //console.log('qua')
                                            window.location=window.location.href;
                                        }
                                    }
                                });
                            }
                        }
                    });
                })
            }
            $('#chiudi-modal').click(function(evt){
                evt.preventDefault();
                $.unblockUI();
            });
        }
    })
}

/**
 * Fine modulo gestione informazioni
 */